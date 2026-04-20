<?php
/**
 * EquipementController.php
 * API JSON — CRUD pour la table `equipement`
 */

ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$configPath = __DIR__ . '/../config.php';
$modelPath  = __DIR__ . '/../model/Equipement.php';

if (!file_exists($configPath)) { echo json_encode(['success'=>false,'message'=>'config.php introuvable.']); exit; }
if (!file_exists($modelPath))  { echo json_encode(['success'=>false,'message'=>'model/Equipement.php introuvable.']); exit; }

require_once $configPath;
require_once $modelPath;

try {
    $model  = new Equipement();
    $pdo    = config::getConnexion();
    $method = $_SERVER['REQUEST_METHOD'];
    $id     = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;

    switch ($method) {

        case 'GET':
            echo json_encode($id ? ($model->getById($id) ?: []) : $model->getAll());
            break;

        case 'POST':
            $nom       = trim($_POST['nom']       ?? '');
            $categorie = trim($_POST['categorie'] ?? '');
            $reference = trim($_POST['reference'] ?? '');
            $prix_jour = (float)($_POST['prix_jour'] ?? 0);
            $statut    = 'disponible';

            if (empty($nom))       { echo json_encode(['success'=>false,'message'=>'Le nom est obligatoire.']); exit; }
            if (empty($reference)) { echo json_encode(['success'=>false,'message'=>'La référence est obligatoire.']); exit; }
            if ($prix_jour <= 0)   { echo json_encode(['success'=>false,'message'=>'Le prix doit être supérieur à 0.']); exit; }
            if (empty($categorie)) { echo json_encode(['success'=>false,'message'=>'La catégorie est obligatoire.']); exit; }

            //  Vérifier doublon référence AVANT insertion
            $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM equipement WHERE reference = ?");
            $stmtCheck->execute([$reference]);
            if ((int)$stmtCheck->fetchColumn() > 0) {
                echo json_encode([
                    'success' => false,
                    //  Message clair sans emoji
                    'message' => 'La référence "' . htmlspecialchars($reference) . '" est déjà utilisée. La référence doit être unique.'
                ]);
                exit;
            }

            // Upload image
            $imageName = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','webp'];
                if (!in_array($ext, $allowed)) { echo json_encode(['success'=>false,'message'=>'Format image non autorisé.']); exit; }
                if ($_FILES['image']['size'] > 5*1024*1024) { echo json_encode(['success'=>false,'message'=>'Image trop grande (max 5 Mo).']); exit; }
                $cleanRef  = preg_replace('/[^a-zA-Z0-9\-]/', '', $reference);
                $imageName = $cleanRef . '.' . $ext;
                $uploadDir = __DIR__ . '/../Assets/images/equipements/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName)) {
                    echo json_encode(['success'=>false,'message'=>'Erreur lors de la sauvegarde de l\'image.']); exit;
                }
            }

            $ok = $model->create([
                'reference' => htmlspecialchars($reference),
                'nom'       => htmlspecialchars($nom),
                'categorie' => htmlspecialchars($categorie),
                'prix_jour' => $prix_jour,
                'statut'    => $statut,
                'image'     => $imageName,
            ]);

            echo json_encode([
                'success' => $ok,
                //  Message succès sans emoji tick vert
                'message' => $ok
                    ? 'Équipement "' . htmlspecialchars($nom) . '" ajouté avec succès !'
                    : 'Erreur lors de l\'insertion en base de données.'
            ]);
            break;

        case 'PUT':
            if (!$id) { echo json_encode(['success'=>false,'message'=>'ID manquant.']); break; }
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) { echo json_encode(['success'=>false,'message'=>'Données JSON invalides.']); break; }

            //  Vérifier doublon référence sur modification (ignorer l'équipement courant)
            $newRef = trim($data['reference'] ?? '');
            if ($newRef) {
                $stmtCheckMod = $pdo->prepare("SELECT COUNT(*) FROM equipement WHERE reference = ? AND id != ?");
                $stmtCheckMod->execute([$newRef, $id]);
                if ((int)$stmtCheckMod->fetchColumn() > 0) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'La référence "' . htmlspecialchars($newRef) . '" est déjà utilisée. La référence doit être unique.'
                    ]);
                    break;
                }
            }

            $ok = $model->update($id, [
                'reference' => htmlspecialchars($newRef),
                'nom'       => htmlspecialchars(trim($data['nom']       ?? '')),
                'categorie' => htmlspecialchars(trim($data['categorie'] ?? '')),
                'prix_jour' => (float)($data['prix_jour'] ?? 0),
                'statut'    => in_array($data['statut']??'', ['disponible','loue','maintenance']) ? $data['statut'] : 'disponible',
                'image'     => $data['image'] ?? null,
            ]);
            echo json_encode(['success'=>$ok,'message'=>$ok?'Équipement modifié avec succès.':'Erreur lors de la modification.']);
            break;

        case 'DELETE':
            if (!$id) { echo json_encode(['success'=>false,'message'=>'ID manquant.']); break; }
            $ok = $model->delete($id);
            echo json_encode(['success'=>$ok,'message'=>$ok?'Équipement supprimé.':'Erreur lors de la suppression.']);
            break;

        default:
            http_response_code(405);
            echo json_encode(['success'=>false,'message'=>'Méthode non autorisée.']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    //  Intercepter erreur MySQL 1062 (doublon) avec message clair
    if ($e->getCode() == 23000) {
        echo json_encode([
            'success' => false,
            'message' => 'La référence est déjà utilisée. La référence doit être unique.'
        ]);
    } else {
        echo json_encode(['success'=>false,'message'=>'Erreur base de données : '.$e->getMessage()]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>'Erreur : '.$e->getMessage()]);
}