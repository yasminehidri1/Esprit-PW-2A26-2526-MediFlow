<?php
require_once __DIR__ . '/../../model/Reservation.php';
require_once __DIR__ . '/../../model/Equipement.php';

$reservationModel = new Reservation();
$equipementModel  = new Equipement();

$reservations = $reservationModel->getAll();
$equipements  = $equipementModel->getAll();
$totalMois    = $reservationModel->countThisMonth();

function getBadgeClass($s){ return ['termine'=>'termine','en_cours'=>'encours','en_retard'=>'retard'][$s]??'encours'; }
function getBadgeLabel($s){ return ['termine'=>'Terminé','en_cours'=>'En cours','en_retard'=>'En retard'][$s]??'—'; }
function getCatClass($c){ return ['Cardiologie'=>'cardio','Réanimation'=>'reani','Gériatrie'=>'geriat','Radiologie'=>'radio','Mobilité'=>'mobi','Respiratoire'=>'resp'][$c]??'cardio'; }
function getCatIcon($c){  return ['Cardiologie'=>'monitor_heart','Réanimation'=>'air','Gériatrie'=>'chair','Radiologie'=>'radiology','Mobilité'=>'accessibility_new','Respiratoire'=>'air'][$c]??'medical_services'; }
function fmtDate($d){ if(!$d)return 'En cours'; return (new DateTime($d))->format('d M Y'); }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>MediFlow Admin - Historique Location</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@700;800;900&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="/projet web/Assets/materiel.css"/>
</head>
<body class="admin-body">

<!-- ══ SIDEBAR ADMIN ══ -->
<aside class="sidebar-admin">
  <div class="sb-admin-brand">
    <div class="name">MediFlow Admin</div>
    <div class="sub">Équipement Médical</div>
  </div>
  <nav class="sb-admin-nav">
    <a href="#"><span class="material-symbols-outlined">inventory_2</span>Stock</a>
    <a href="#"><span class="material-symbols-outlined">shopping_cart</span>Commandes</a>
    <a href="/projet web/view/Backoffice/historique-location.php" class="active">
      <span class="material-symbols-outlined">history</span>Historique Location
    </a>
    <a href="#"><span class="material-symbols-outlined">build</span>Maintenance</a>
  </nav>
  <div class="sb-admin-bottom">
    <!-- Bouton Nouvelle Commande → ouvre la modale CREATE -->
    <button class="btn-new-order" type="button" id="btn-new-entry">
      <span class="material-symbols-outlined">add</span>Nouvelle Commande
    </button>
    <a href="#" class="sb-util"><span class="material-symbols-outlined">help_outline</span>Aide</a>
    <a href="#" class="sb-util"><span class="material-symbols-outlined">logout</span>Déconnexion</a>
  </div>
</aside>

<!-- ══ MAIN ADMIN ══ -->
<div class="main-admin">

  <!-- ── Topbar ── -->
  <header class="topbar-admin">
    <div class="topbar-admin-title">Gestion de<br>Location</div>
    <div class="topnav-search" style="flex:1;max-width:280px;">
      <span class="material-symbols-outlined">search</span>
      <input type="text" id="search-input" placeholder="Rechercher un équipement..."/>
    </div>
    <div class="topbar-admin-links">
      <a href="#">Dashboard</a>
      <a href="#">Support</a>
    </div>
    <div style="display:flex;align-items:center;gap:10px;">
      <button class="btn-export" id="btn-export" type="button">
        <span class="material-symbols-outlined" style="font-size:16px">file_download</span>
        Exporter Rapport
      </button>
      <button class="icon-btn"><span class="material-symbols-outlined">notifications</span></button>
      <div class="nav-avatar" style="width:34px;height:34px;border-radius:50%;background:#dbeafe;display:flex;align-items:center;justify-content:center;">
        <span class="material-symbols-outlined" style="font-size:20px;color:#1a56db;">person</span>
      </div>
    </div>
  </header>

  <!-- ── Contenu principal ── -->
  <main class="content-admin">

    <!-- Titre + bouton Nouvelle Entrée -->
    <div class="page-title-row">
      <div>
        <h1>Historique de Location</h1>
        <p>Visualisez et gérez le cycle de vie complet de vos équipements médicaux en location.</p>
      </div>
      <!-- Ce bouton et celui de la sidebar pointent tous les deux vers btn-new-entry -->
      <button class="btn-new-entry" id="btn-new-entry-top" type="button">
        <span class="material-symbols-outlined">add_circle</span>Nouvelle Entrée
      </button>
    </div>

    <!-- Filtres + Stat -->
    <div class="filter-stat-row">
      <div class="filter-card">
        <div class="filter-field">
          <label>Période</label>
          <div class="filter-dates">
            <input type="date" id="filter-date-from" class="filter-input"/>
            <input type="date" id="filter-date-to"   class="filter-input"/>
          </div>
        </div>
        <div class="filter-field">
          <label>Catégorie</label>
          <select id="filter-cat" class="filter-input">
            <option value="">Toutes les catégories</option>
            <option>Cardiologie</option>
            <option>Réanimation</option>
            <option>Gériatrie</option>
            <option>Radiologie</option>
            <option>Mobilité</option>
            <option>Respiratoire</option>
          </select>
        </div>
        <button class="btn-filter" id="btn-filter" type="button">Filtrer</button>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <span class="material-symbols-outlined">trending_up</span>
        </div>
        <div>
          <div class="stat-num" id="stat-month"><?= $totalMois ?></div>
          <div class="stat-lbl">Locations ce mois-ci</div>
        </div>
      </div>
    </div>

    <!-- Tableau des réservations -->
    <div class="table-card">
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>ID Équipement</th>
              <th>Nom Matériel</th>
              <th>Locataire</th>
              <th>Dates de Location</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="reservations-tbody">

            <?php if (empty($reservations)): ?>
              <tr>
                <td colspan="6" style="text-align:center;padding:40px;color:#9ca3af;">
                  Aucune réservation trouvée. Cliquez sur "Nouvelle Entrée" pour en créer une.
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($reservations as $r): ?>
                <?php
                  $rJson    = htmlspecialchars(json_encode($r), ENT_QUOTES);
                  $badgeCls = getBadgeClass($r['statut']);
                  $badgeLbl = getBadgeLabel($r['statut']);
                  $catCls   = getCatClass($r['categorie'] ?? '');
                  $catIco   = getCatIcon($r['categorie'] ?? '');
                  $startCls = ($r['statut'] === 'en_cours') ? 'blue' : '';
                  $endCls   = ($r['statut'] === 'en_retard') ? 'late' : (!$r['date_fin'] ? 'ongoing' : 'normal');
                ?>
                <tr data-cat="<?= htmlspecialchars($r['categorie'] ?? '') ?>">
                  <td><span class="eq-id"><?= htmlspecialchars($r['reference'] ?? 'EQ-???') ?></span></td>
                  <td>
                    <div class="eq-cell">
                      <div class="eq-icon <?= $catCls ?>">
                        <span class="material-symbols-outlined"><?= $catIco ?></span>
                      </div>
                      <div>
                        <div class="eq-name"><?= htmlspecialchars($r['equipement_nom'] ?? '—') ?></div>
                        <div class="eq-cat"><?= htmlspecialchars($r['categorie'] ?? '—') ?></div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="locataire-name"><?= htmlspecialchars($r['locataire_nom']) ?></div>
                    <div class="locataire-loc"><?= htmlspecialchars($r['locataire_ville'] ?? '') ?></div>
                  </td>
                  <td>
                    <div class="date-start <?= $startCls ?>"><?= fmtDate($r['date_debut']) ?></div>
                    <div class="date-arrow">→
                      <span class="date-end <?= $endCls ?>">
                        <?= $r['date_fin'] ? fmtDate($r['date_fin']) : 'En cours' ?>
                      </span>
                    </div>
                  </td>
                  <td>
                    <span class="badge <?= $badgeCls ?>">
                      <span class="badge-dot"></span><?= $badgeLbl ?>
                    </span>
                  </td>
                  <td>
                    <div class="actions-cell">
                      <!-- Bouton EDIT → appelle openEditModal() avec les données de la ligne -->
                      <button class="action-btn edit" title="Modifier"
                              onclick='openEditModal(<?= $rJson ?>)'>
                        <span class="material-symbols-outlined">edit</span>
                      </button>
                      <!-- Bouton DELETE → appelle deleteReservation() avec l'id -->
                      <button class="action-btn del" title="Supprimer"
                              onclick="deleteReservation(<?= (int)$r['id'] ?>)">
                        <span class="material-symbols-outlined">delete</span>
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>

          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="pagination-row">
        <span class="pagination-info">
          Affichage de <strong><?= count($reservations) ?></strong> entrées
        </span>
        <div class="pagination-btns">
          <button class="page-btn text" disabled>Précédent</button>
          <button class="page-btn active">1</button>
          <button class="page-btn text" disabled>Suivant</button>
        </div>
      </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="bottom-cards">
      <div class="bottom-card">
        <div class="bc-top">
          <div class="bc-icon teal"><span class="material-symbols-outlined">info</span></div>
          <span class="bc-tag">Statut Critique</span>
        </div>
        <div class="bc-title">Rappels en retard</div>
        <div class="bc-desc">
          <?= count(array_filter($reservations, fn($r) => $r['statut'] === 'en_retard')) ?>
          équipement(s) nécessitent une action immédiate.
        </div>
        <a href="#" class="bc-link">Gérer les retards →</a>
      </div>

      <div class="bottom-card">
        <div class="bc-top">
          <div class="bc-icon blue"><span class="material-symbols-outlined">bar_chart</span></div>
          <span class="bc-tag">Performances</span>
        </div>
        <div class="bc-title">Taux d'occupation</div>
        <?php
          $totalEq = count($equipements);
          $loues   = count(array_filter($equipements, fn($e) => $e['statut'] === 'loue'));
          $taux    = $totalEq > 0 ? round($loues / $totalEq * 100) : 0;
        ?>
        <div class="bc-desc">Actuellement à <?= $taux ?>% de la flotte louée.</div>
        <div class="bc-progress"><div class="bc-progress-bar" style="width:<?= $taux ?>%"></div></div>
      </div>

      <div class="bottom-card">
        <div class="bc-top">
          <div class="bc-icon gray"><span class="material-symbols-outlined">receipt_long</span></div>
          <span class="bc-tag">Revenus</span>
        </div>
        <div class="bc-title">Chiffre d'Affaires</div>
        <div class="bc-desc">Projection mensuelle pour la location d'équipements.</div>
        <a href="#" class="bc-link">Voir les factures →</a>
      </div>
    </div>

  </main>
</div>

<!-- ══════════════════════════════════════════════════
     MODALE CRUD — CREATE / EDIT
     IDs importants utilisés par materiel.js :
     - modal-overlay  → container de la modale
     - modal-title    → titre dynamique
     - edit-id        → vide = CREATE | rempli = UPDATE
     - edit-equipement, edit-nom, edit-ville,
       edit-tel, edit-debut, edit-fin, edit-statut
     - btn-save       → soumet (appelle saveReservation())
     - btn-cancel     → ferme la modale
     - btn-cancel-footer → idem
════════════════════════════════════════════════════ -->
<div id="modal-overlay" class="modal-overlay">
  <div class="modal-box">

    <div class="modal-header">
      <h2 id="modal-title">Nouvelle Réservation</h2>
      <button class="modal-close" id="btn-cancel" type="button">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>

    <!-- ID caché : vide = création, rempli = modification -->
    <input type="hidden" id="edit-id"/>

    <!-- Sélection équipement -->
    <div class="modal-field">
      <label for="edit-equipement">
        Équipement <span style="color:#dc2626;">*</span>
      </label>
      <select id="edit-equipement" class="modal-input">
        <?php foreach ($equipements as $eq): ?>
          <option value="<?= (int)$eq['id'] ?>">
            <?= htmlspecialchars($eq['reference']) ?> — <?= htmlspecialchars($eq['nom']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Nom + Ville -->
    <div class="modal-row">
      <div class="modal-field">
        <label for="edit-nom">
          Nom du locataire <span style="color:#dc2626;">*</span>
        </label>
        <input id="edit-nom" class="modal-input" type="text"
               placeholder="Clinique / Hôpital / Patient"/>
      </div>
      <div class="modal-field">
        <label for="edit-ville">Ville</label>
        <input id="edit-ville" class="modal-input" type="text" placeholder="Tunis"/>
      </div>
    </div>

    <!-- Téléphone -->
    <div class="modal-field">
      <label for="edit-tel">Téléphone <small style="color:#9ca3af;">(optionnel)</small></label>
      <input id="edit-tel" class="modal-input" type="tel" placeholder="20 123 456"/>
    </div>

    <!-- Dates -->
    <div class="modal-row">
      <div class="modal-field">
        <label for="edit-debut">Date de début <span style="color:#dc2626;">*</span></label>
        <input id="edit-debut" class="modal-input" type="date"/>
      </div>
      <div class="modal-field">
        <label for="edit-fin">Date de fin <small style="color:#9ca3af;">(optionnelle)</small></label>
        <input id="edit-fin" class="modal-input" type="date"/>
      </div>
    </div>

    <!-- Statut -->
    <div class="modal-field">
      <label for="edit-statut">Statut</label>
      <select id="edit-statut" class="modal-input">
        <option value="en_cours">🔵 En cours</option>
        <option value="termine">🟢 Terminé</option>
        <option value="en_retard">🔴 En retard</option>
      </select>
    </div>

    <!-- Boutons footer -->
    <div class="modal-footer">
      <button class="btn-cancel" id="btn-cancel-footer" type="button">Annuler</button>
      <button class="btn-save"   id="btn-save"          type="button">
        <span class="material-symbols-outlined" style="font-size:16px;vertical-align:middle;">save</span>
        Enregistrer
      </button>
    </div>

  </div>
</div>

<div class="toast-container"></div>
<script src="/projet web/Assets/materiel.js"></script>
<script>
  /**
   * Initialisation spécifique au backoffice
   * Les deux boutons "Nouvelle Entrée" (sidebar + page) ouvrent la même modale
   */
  document.getElementById('btn-new-entry-top')?.addEventListener('click', openCreateModal);
</script>
</body>
</html>