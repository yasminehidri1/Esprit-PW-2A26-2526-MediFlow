<?php
/**
 * rappel_rdv.php
 * Script de rappel automatique — à exécuter via CRON chaque jour à 08h00
 *
 * XAMPP (Windows) — Task Scheduler :
 *   Exécutable : C:\xampp\php\php.exe
 *   Arguments  : C:\xampp\htdocs\integration\rappel_rdv.php
 *   Déclencheur: Tous les jours à 08:00
 *
 * Linux/Mac — crontab -e :
 *   0 8 * * * php /var/www/html/integration/rappel_rdv.php >> /var/log/mediflow_rappels.log 2>&1
 *
 * À placer dans : /integration/rappel_rdv.php  (racine du projet)
 */

// ── Bootstrap ────────────────────────────────────────────────────────────────
define('MEDIFLOW_ROOT', __DIR__);

require_once MEDIFLOW_ROOT . '/config.php';
require_once MEDIFLOW_ROOT . '/Services/MailService.php';

use Services\MailService;

// ── Connexion BDD ────────────────────────────────────────────────────────────
$pdo = \config::getConnexion();

// ── Date cible : DEMAIN ──────────────────────────────────────────────────────
$demain = date('Y-m-d', strtotime('+1 day'));

echo "[" . date('Y-m-d H:i:s') . "] MediFlow — Rappels RDV du {$demain}\n";

// ── Vérifier si la colonne rappel_envoye existe (sécurité si migration oubliée) ──
$colonnes = $pdo->query("SHOW COLUMNS FROM rendez_vous LIKE 'rappel_envoye'")->fetchAll();
if (empty($colonnes)) {
    echo "[" . date('H:i:s') . "] ⚠️  Colonne rappel_envoye manquante — création en cours...\n";
    $pdo->exec("ALTER TABLE rendez_vous ADD COLUMN rappel_envoye TINYINT(1) NOT NULL DEFAULT 0");
    echo "[" . date('H:i:s') . "] ✅ Colonne rappel_envoye ajoutée.\n";
}

// ── Requête : RDV confirmés demain, rappel pas encore envoyé ─────────────────
// On filtre statut = 'confirme' pour éviter de rappeler des RDV annulés
// La colonne rappel_envoye évite les doublons si le script tourne deux fois
$sql = "
    SELECT
        r.*,
        u.nom        AS medecin_nom,
        u.prenom     AS medecin_prenom,
        u.specialite AS medecin_specialite
    FROM rendez_vous r
    LEFT JOIN utilisateurs u ON u.id_PK = r.medecin_id
    WHERE r.date_rdv = :demain
      AND r.statut   = 'confirme'
      AND (r.rappel_envoye IS NULL OR r.rappel_envoye = 0)
      AND r.patient_email IS NOT NULL
      AND r.patient_email != ''
";

$stmt = $pdo->prepare($sql);
$stmt->execute([':demain' => $demain]);
$rdvs = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "[" . date('H:i:s') . "] " . count($rdvs) . " RDV à rappeler trouvés.\n";

// ── Envoi des rappels ────────────────────────────────────────────────────────
$envoyes  = 0;
$echoues  = 0;

foreach ($rdvs as $rdv) {
    $medecin = [
        'nom'        => $rdv['medecin_nom']    ?? 'Médecin',
        'prenom'     => $rdv['medecin_prenom'] ?? '',
        'specialite' => $rdv['medecin_specialite'] ?? '',
    ];

    $ok = MailService::rdvRappel($rdv, $medecin);

    if ($ok) {
        // Marquer le rappel comme envoyé pour ne pas re-envoyer
        $upd = $pdo->prepare("UPDATE rendez_vous SET rappel_envoye = 1 WHERE id = :id");
        $upd->execute([':id' => $rdv['id']]);
        $envoyes++;
        echo "[" . date('H:i:s') . "] ✅ Rappel envoyé → {$rdv['patient_email']} (RDV #{$rdv['id']})\n";
    } else {
        $echoues++;
        echo "[" . date('H:i:s') . "] ❌ Échec envoi  → {$rdv['patient_email']} (RDV #{$rdv['id']})\n";
    }
}

echo "[" . date('H:i:s') . "] Terminé — {$envoyes} envoyés, {$echoues} échoués.\n";