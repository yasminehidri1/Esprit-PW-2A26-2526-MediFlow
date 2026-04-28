<?php
require_once __DIR__ . '/config.php';
$pdo = config::getConnexion();

try {
    // 1. Create Doctor
    $dr_email = 'dr.smith@mediflow.com';
    $dr_pass  = 'Smith123!';
    $dr_hash  = password_hash($dr_pass, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, mail, motdp, id_role, matricule) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute(['Smith', 'Adam', $dr_email, $dr_hash, 2, 'MD200']);
    $dr_id = $pdo->lastInsertId();
    echo "Doctor created: $dr_email (Pass: $dr_pass, ID: $dr_id)\n";

    // 2. Create Patient
    $pt_email = 'john.doe@gmail.com';
    $pt_pass  = 'JohnDoe123!';
    $pt_hash  = password_hash($pt_pass, PASSWORD_DEFAULT);
    $pt_cin   = '12345678';
    
    // Note: 'cin' might be missing in the INSERT list above if it's a separate column or handled differently.
    // Let's check if 'cin' is in utilisateurs. Based on my previous view, it was NOT there? 
    // Wait, let me check the column list again.
    
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, mail, motdp, id_role, matricule, tel) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute(['Doe', 'John', $pt_email, $pt_hash, 9, 'PT200', '55667788']);
    $pt_id = $pdo->lastInsertId();
    echo "Patient created: $pt_email (Pass: $pt_pass, ID: $pt_id, CIN: $pt_cin)\n";

    // 3. Insert Planning for Doctor
    $stmt_plan = $pdo->prepare("INSERT INTO planning (medecin_id, titre, date_debut, date_fin, type, note) VALUES (?, ?, ?, ?, ?, ?)");
    $today = date('Y-m-d');
    $stmt_plan->execute([$dr_id, 'Réunion Staff', "$today 09:00:00", "$today 10:30:00", 'reunion', 'Important']);
    $stmt_plan->execute([$dr_id, 'Chirurgie', date('Y-m-d', strtotime('+1 day'))." 14:00:00", date('Y-m-d', strtotime('+1 day'))." 17:00:00", 'chirurgie', 'Bloc B']);
    echo "Planning inserted for Doctor.\n";

    // 4. Insert Rendez-vous for Patient
    $stmt_rdv = $pdo->prepare("INSERT INTO rendez_vous (medecin_id, patient_nom, patient_prenom, cin, genre, date_rdv, heure_rdv, statut) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt_rdv->execute([$dr_id, 'Doe', 'John', $pt_cin, 'homme', date('Y-m-d', strtotime('+2 days')), '10:00:00', 'confirme']);
    $stmt_rdv->execute([$dr_id, 'Doe', 'John', $pt_cin, 'homme', date('Y-m-d', strtotime('+3 days')), '11:30:00', 'en_attente']);
    echo "Rendez-vous inserted for Patient.\n";

    echo "\nSEEDING COMPLETE.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
