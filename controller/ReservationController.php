<?php
/**
 * ReservationController.php
 * API JSON — CRUD pour la table `reservation`
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/Reservation.php';
require_once __DIR__ . '/../model/Equipement.php';

try {
    $model  = new Reservation();
    $method = $_SERVER['REQUEST_METHOD'];
    $id     = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;

    switch ($method) {

        case 'GET':
            echo json_encode($id ? ($model->getById($id) ?: []) : $model->getAll());
            break;

        case 'POST':
            $raw  = file_get_contents('php://input');
            $data = json_decode($raw, true);

            if (!$data) {
                echo json_encode(['success'=>false,'message'=>'Données JSON invalides.','raw'=>$raw]);
                break;
            }

            // Vérifier champs obligatoires
            if (empty($data['equipement_id']) || empty($data['locataire_nom']) || empty($data['date_debut'])) {
                echo json_encode(['success'=>false,'message'=>'Champs obligatoires manquants (equipement_id, locataire_nom, date_debut).']);
                break;
            }

            // Vérifier que l'équipement existe
            $eq = (new Equipement())->getById((int)$data['equipement_id']);
            if (!$eq) {
                echo json_encode(['success'=>false,'message'=>'Équipement ID='.(int)$data['equipement_id'].' introuvable.']);
                break;
            }

            $ok = $model->create([
                'equipement_id'   => (int)$data['equipement_id'],
                'locataire_nom'   => htmlspecialchars(trim($data['locataire_nom'])),
                'locataire_ville' => htmlspecialchars(trim($data['locataire_ville'] ?? '')),
                'date_debut'      => $data['date_debut'],
                'date_fin'        => !empty($data['date_fin']) ? $data['date_fin'] : null,
                'statut'          => in_array($data['statut']??'',['en_cours','termine','en_retard']) ? $data['statut'] : 'en_cours',
                'telephone'       => htmlspecialchars(trim($data['telephone'] ?? '')),
            ]);

            echo json_encode(['success'=>$ok,'message'=>$ok?'Réservation créée.':'Échec insertion BDD.']);
            break;

        case 'PUT':
            if (!$id) { echo json_encode(['success'=>false,'message'=>'ID manquant.']); break; }
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) { echo json_encode(['success'=>false,'message'=>'JSON invalide.']); break; }

            $ok = $model->update($id, [
                'equipement_id'   => (int)($data['equipement_id'] ?? 0),
                'locataire_nom'   => htmlspecialchars(trim($data['locataire_nom']   ?? '')),
                'locataire_ville' => htmlspecialchars(trim($data['locataire_ville'] ?? '')),
                'date_debut'      => $data['date_debut'] ?? '',
                'date_fin'        => !empty($data['date_fin']) ? $data['date_fin'] : null,
                'statut'          => in_array($data['statut']??'',['en_cours','termine','en_retard']) ? $data['statut'] : 'en_cours',
                'telephone'       => htmlspecialchars(trim($data['telephone'] ?? '')),
            ]);

            echo json_encode(['success'=>$ok,'message'=>$ok?'Réservation modifiée.':'Échec modification.']);
            break;

        case 'DELETE':
            if (!$id) { echo json_encode(['success'=>false,'message'=>'ID manquant.']); break; }
            $ok = $model->delete($id);
            echo json_encode(['success'=>$ok,'message'=>$ok?'Réservation supprimée.':'Échec suppression.']);
            break;

        default:
            http_response_code(405);
            echo json_encode(['success'=>false,'message'=>'Méthode non autorisée : '.$method]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>'Erreur BDD : '.$e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>'Erreur : '.$e->getMessage()]);
}