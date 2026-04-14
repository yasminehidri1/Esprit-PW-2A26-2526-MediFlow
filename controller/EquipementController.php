<?php
/**
 * EquipementController.php
 * API JSON — CRUD pour la table `equipement`
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/Equipement.php';

try {
    $model  = new Equipement();
    $method = $_SERVER['REQUEST_METHOD'];
    $id     = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;

    switch ($method) {

        case 'GET':
            echo json_encode($id ? ($model->getById($id) ?: []) : $model->getAll());
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) { echo json_encode(['success'=>false,'message'=>'JSON invalide']); break; }
            $ok = $model->create([
                'reference' => htmlspecialchars(trim($data['reference'] ?? '')),
                'nom'       => htmlspecialchars(trim($data['nom']       ?? '')),
                'categorie' => htmlspecialchars(trim($data['categorie'] ?? '')),
                'prix_jour' => (float)($data['prix_jour'] ?? 0),
                'statut'    => in_array($data['statut']??'', ['disponible','loue','maintenance']) ? $data['statut'] : 'disponible',
                'image'     => $data['image'] ?? null,
            ]);
            echo json_encode(['success'=>$ok, 'message'=>$ok?'Équipement créé.':'Erreur insertion.']);
            break;

        case 'PUT':
            if (!$id) { echo json_encode(['success'=>false,'message'=>'ID manquant.']); break; }
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) { echo json_encode(['success'=>false,'message'=>'JSON invalide']); break; }
            $ok = $model->update($id, [
                'reference' => htmlspecialchars(trim($data['reference'] ?? '')),
                'nom'       => htmlspecialchars(trim($data['nom']       ?? '')),
                'categorie' => htmlspecialchars(trim($data['categorie'] ?? '')),
                'prix_jour' => (float)($data['prix_jour'] ?? 0),
                'statut'    => in_array($data['statut']??'', ['disponible','loue','maintenance']) ? $data['statut'] : 'disponible',
                'image'     => $data['image'] ?? null,
            ]);
            echo json_encode(['success'=>$ok, 'message'=>$ok?'Équipement modifié.':'Erreur modification.']);
            break;

        case 'DELETE':
            if (!$id) { echo json_encode(['success'=>false,'message'=>'ID manquant.']); break; }
            $ok = $model->delete($id);
            echo json_encode(['success'=>$ok, 'message'=>$ok?'Équipement supprimé.':'Erreur suppression.']);
            break;

        default:
            http_response_code(405);
            echo json_encode(['success'=>false,'message'=>'Méthode non autorisée.']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>'Erreur BDD : '.$e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>'Erreur : '.$e->getMessage()]);
}