<?php

//  traitement-rdv.php — View (patient)
//  Ce fichier ne fait PLUS de SQL directement
//  Il appelle le Controller qui appelle le Model


require_once __DIR__ . '/../../../controller/RendezVousController.php';

// On crée le controller et on appelle la bonne action
$controller = new RendezVousController();
$controller->enregistrerRdv();
// La méthode gère validation + INSERT + redirection toute seule
?>