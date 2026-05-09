<?php
/**
 * PatientEquipmentController.php
 *
 * Handles the patient-facing equipment rental module.
 * Routes: /catalogue | /reservation?id=X | /mes-reservations
 * API:    /equipment/api/equipements | /equipment/api/reservations | /equipment/api/disponibilite
 *
 * @package MediFlow\Controllers
 * @version 1.0.0
 */

namespace Controllers;

use Core\SessionHelper;

class PatientEquipmentController
{
    use SessionHelper;

    /* ────────────────────────────────────────────
       VIEWS
    ──────────────────────────────────────────── */

    /** Equipment catalogue (browse all) */
    public function catalogue(): void
    {
        $this->ensureSession();
        $this->requireAuth();

        require_once __DIR__ . '/../Models/Equipement.php';
        $model       = new \Equipement();
        $equipements = $model->getAll();

        $data = [
            'equipements' => $equipements,
            'currentUser' => $_SESSION['user'] ?? [],
            'pageTitle'   => 'Equipment Catalogue',
        ];

        $currentView = '../Front/catalogue';
        include __DIR__ . '/../Views/Back/layout.php';
    }

    /** Single-equipment reservation form */
    public function reservation(): void
    {
        $this->ensureSession();
        $this->requireAuth();

        $id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;

        require_once __DIR__ . '/../Models/Equipement.php';
        $eq     = $id > 0 ? (new \Equipement())->getById($id) : null;
        $erreur = !$eq ? ($id <= 0 ? "Aucun identifiant fourni." : "Équipement introuvable.") : null;

        $data = [
            'eq'          => $eq,
            'erreur'      => $erreur,
            'currentUser' => $_SESSION['user'] ?? [],
            'pageTitle'   => $eq ? 'Réserver — ' . htmlspecialchars($eq['nom']) : 'Réservation',
        ];

        $currentView = '../Front/reservation';
        include __DIR__ . '/../Views/Back/layout.php';
    }

    /** Patient's own reservation history */
    public function mesReservations(): void
    {
        $this->ensureSession();
        $this->requireAuth();

        require_once __DIR__ . '/../Models/Reservation.php';
        require_once __DIR__ . '/../Models/Equipement.php';

        $reservationModel = new \Reservation();

        // ✅ Filtrer par matricule ou par nom selon ce qui est disponible
        $matricule = $_SESSION['user']['matricule'] ?? null;
        $prenom    = $_SESSION['user']['prenom']    ?? '';
        $nom       = $_SESSION['user']['nom']       ?? '';
        $nomComplet = trim($prenom . ' ' . $nom);

        if ($matricule) {
            $reservations = $reservationModel->getByMatricule($matricule);
        } elseif (strlen($nomComplet) > 2) {
            $reservations = $reservationModel->getByNom($nomComplet);
        } else {
            $reservations = [];
        }

        $data = [
            'reservations' => $reservations,
            'currentUser'  => $_SESSION['user'] ?? [],
            'pageTitle'    => 'Mes Réservations',
        ];

        $currentView = '../Front/mes-reservations';
        include __DIR__ . '/../Views/Back/layout.php';
    }

    /** Equipment manager — backoffice CRUD view */
    public function gestionEquipements(): void
    {
        $this->ensureSession();
        $this->requireAuth();

        require_once __DIR__ . '/../Models/Equipement.php';
        $equipementModel = new \Equipement();
        $equipements     = $equipementModel->getAll();

        $data = [
            'equipements' => $equipements,
            'currentUser' => $_SESSION['user'] ?? [],
            'pageTitle'   => 'Gestion des Équipements',
        ];

        $currentView = 'equipements';
        include __DIR__ . '/../Views/Back/layout.php';
    }

    /** Equipment manager — rental history view */
    public function historiqueLocation(): void
    {
        $this->ensureSession();
        $this->requireAuth();

        require_once __DIR__ . '/../Models/Reservation.php';
        require_once __DIR__ . '/../Models/Equipement.php';

        $reservationModel = new \Reservation();
        $equipementModel  = new \Equipement();
        $reservations     = $reservationModel->getAll();
        $equipements      = $equipementModel->getAll();

        $data = [
            'reservations' => $reservations,
            'equipements'  => $equipements,
            'currentUser'  => $_SESSION['user'] ?? [],
            'pageTitle'    => 'Historique de Location',
        ];

        $currentView = 'historique-location';
        include __DIR__ . '/../Views/Back/layout.php';
    }

    /* ────────────────────────────────────────────
       JSON APIs
    ──────────────────────────────────────────── */

    /** Local AI proxy — forwards image to Ollama/Moondream and returns parsed JSON */
    public function analyzeImage(): void
    {
        $this->ensureSession();
        $this->requireAuth();

        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $body   = json_decode(file_get_contents('php://input'), true);
        $base64 = $body['image'] ?? '';

        if (empty($base64)) {
            http_response_code(400);
            echo json_encode(['error' => 'No image provided']);
            return;
        }

        // Single query: ask Moondream to freely describe what it sees.
        // Small models describe far more accurately than they classify.
        $description = $this->moondreamQuery($base64,
            'Describe what you see in this image in detail. ' .
            'Mention the shape, color, any wheels, tubes, screens, buttons, or text you can see.'
        );

        if ($description === null) {
            http_response_code(502);
            echo json_encode(['error' => 'Ollama did not respond — is it running?']);
            return;
        }

        $desc = mb_strtolower(trim((string)$description));

        // Keyword → French name + category + price, most specific first
        $rules = [
            // term to look for => [French name, category, price]
            'electric wheelchair'    => ['Fauteuil roulant électrique',    'Mobilité',      450],
            'power wheelchair'       => ['Fauteuil roulant électrique',    'Mobilité',      450],
            'wheelchair'             => ['Fauteuil roulant',               'Mobilité',      350],
            'rollator'               => ['Déambulateur à roulettes',       'Mobilité',      320],
            'walker'                 => ['Déambulateur',                   'Mobilité',      310],
            'crutch'                 => ['Béquilles médicales',            'Mobilité',      300],
            'cane'                   => ['Canne médicale',                 'Mobilité',      300],
            'stair lift'             => ['Monte-escalier médical',         'Mobilité',      800],
            'oxygen concentrator'    => ['Concentrateur d\'oxygène',       'Respiratoire',  700],
            'oxygen tank'            => ['Bouteille d\'oxygène',           'Respiratoire',  500],
            'oxygen cylinder'        => ['Bouteille d\'oxygène',           'Respiratoire',  500],
            'oxygen'                 => ['Concentrateur d\'oxygène',       'Respiratoire',  650],
            'ventilator'             => ['Ventilateur médical',            'Respiratoire',  900],
            'cpap'                   => ['Appareil CPAP',                  'Respiratoire',  600],
            'nebulizer'              => ['Nébuliseur médical',             'Respiratoire',  400],
            'nebuliser'              => ['Nébuliseur médical',             'Respiratoire',  400],
            'suction'                => ['Aspirateur chirurgical',         'Respiratoire',  500],
            'ecg'                    => ['Électrocardiographe',            'Cardiologie',   800],
            'ekg'                    => ['Électrocardiographe',            'Cardiologie',   800],
            'defibrillator'          => ['Défibrillateur',                 'Cardiologie',   950],
            'heart monitor'          => ['Moniteur cardiaque',             'Cardiologie',   750],
            'cardiac monitor'        => ['Moniteur cardiaque',             'Cardiologie',   750],
            'blood pressure'         => ['Tensiomètre médical',            'Cardiologie',   350],
            'pulse oximeter'         => ['Oxymètre de pouls',              'Cardiologie',   320],
            'stethoscope'            => ['Stéthoscope médical',            'Cardiologie',   300],
            'infusion pump'          => ['Pompe à perfusion',              'Réanimation',   850],
            'iv pump'                => ['Pompe à perfusion',              'Réanimation',   850],
            'syringe pump'           => ['Pousse-seringue médical',        'Réanimation',   800],
            'icu'                    => ['Moniteur de réanimation',        'Réanimation',   950],
            'feeding tube'           => ['Pompe entérale',                 'Réanimation',   700],
            'hospital bed'           => ['Lit médicalisé électrique',      'Gériatrie',     600],
            'medical bed'            => ['Lit médicalisé électrique',      'Gériatrie',     600],
            'adjustable bed'         => ['Lit médicalisé électrique',      'Gériatrie',     600],
            'patient lift'           => ['Lève-personne médical',          'Gériatrie',     750],
            'hoist'                  => ['Lève-personne médical',          'Gériatrie',     750],
            'shower chair'           => ['Chaise de douche médicale',      'Gériatrie',     330],
            'commode'                => ['Chaise garde-robe médicale',     'Gériatrie',     320],
            'anti-bedsore'           => ['Matelas anti-escarre',           'Gériatrie',     450],
            'mattress'               => ['Matelas anti-escarre',           'Gériatrie',     450],
            'ultrasound'             => ['Échographe médical',             'Radiologie',    950],
            'x-ray'                  => ['Appareil de radiographie',       'Radiologie',    900],
            'xray'                   => ['Appareil de radiographie',       'Radiologie',    900],
            'scanner'                => ['Scanner médical',                'Radiologie',    950],
            'mri'                    => ['IRM médical',                    'Radiologie',    1000],
            'cylinder'               => ['Bouteille d\'oxygène',           'Respiratoire',  500],
            'tank'                   => ['Bouteille d\'oxygène',           'Respiratoire',  500],
            'tube'                   => ['Appareil médical à tubes',       'Réanimation',   600],
            'wheel'                  => ['Fauteuil roulant',               'Mobilité',      350],
            'screen'                 => ['Moniteur médical',               'Cardiologie',   700],
            'monitor'                => ['Moniteur médical',               'Cardiologie',   700],
            'mask'                   => ['Masque respiratoire',            'Respiratoire',  350],
        ];

        $nomFr       = null;
        $catResolved = null;
        $prixInt     = null;

        foreach ($rules as $keyword => [$fr, $cat, $price]) {
            if (str_contains($desc, $keyword)) {
                $nomFr       = $fr;
                $catResolved = $cat;
                $prixInt     = $price;
                break;
            }
        }

        // Fallback if nothing matched
        if ($nomFr === null) {
            $nomFr       = ucwords(trim((string)$description, " \t\n\r.\"'"));
            $catResolved = 'Mobilité';
            $prixInt     = 400;
        }

        echo json_encode([
            'nom'        => $nomFr,
            'categorie'  => $catResolved,
            'prix'       => $prixInt,
            '_debug_desc' => $description,  // raw Moondream description — visible in browser console
        ]);
    }

    /** Keyword lookup from English equipment name → French medical name */
    private function englishToFrenchMedical(string $en): string
    {
        $en = mb_strtolower(trim($en));
        $map = [
            'wheelchair'            => 'Fauteuil roulant',
            'electric wheelchair'   => 'Fauteuil roulant électrique',
            'rollator'              => 'Déambulateur à roulettes',
            'walker'                => 'Déambulateur',
            'crutches'              => 'Béquilles médicales',
            'crutch'                => 'Béquille médicale',
            'cane'                  => 'Canne médicale',
            'oxygen concentrator'   => 'Concentrateur d\'oxygène',
            'oxygen'                => 'Concentrateur d\'oxygène',
            'ventilator'            => 'Ventilateur médical',
            'cpap'                  => 'Appareil CPAP',
            'nebulizer'             => 'Nébuliseur médical',
            'nebuliser'             => 'Nébuliseur médical',
            'suction machine'       => 'Aspirateur chirurgical',
            'suction'               => 'Aspirateur chirurgical',
            'ecg'                   => 'Électrocardiographe',
            'ekg'                   => 'Électrocardiographe',
            'heart monitor'         => 'Moniteur cardiaque',
            'cardiac monitor'       => 'Moniteur cardiaque',
            'defibrillator'         => 'Défibrillateur',
            'blood pressure'        => 'Tensiomètre',
            'pulse oximeter'        => 'Oxymètre de pouls',
            'infusion pump'         => 'Pompe à perfusion',
            'iv pump'               => 'Pompe à perfusion',
            'hospital bed'          => 'Lit médicalisé',
            'medical bed'           => 'Lit médicalisé',
            'patient lift'          => 'Lève-personne',
            'hoist'                 => 'Lève-personne',
            'shower chair'          => 'Chaise de douche médicale',
            'commode'               => 'Chaise garde-robe',
            'mattress'              => 'Matelas anti-escarre',
            'ultrasound'            => 'Échographe',
            'x-ray'                 => 'Appareil de radiographie',
            'xray'                  => 'Appareil de radiographie',
            'scanner'               => 'Scanner médical',
            'mri'                   => 'IRM',
            'stretcher'             => 'Brancard médical',
            'gurney'                => 'Brancard médical',
            'wheelchair ramp'       => 'Rampe d\'accès fauteuil roulant',
            'stair lift'            => 'Monte-escalier',
            'feeding tube'          => 'Sonde d\'alimentation',
            'syringe pump'          => 'Pousse-seringue',
        ];

        foreach ($map as $en_key => $fr) {
            if (str_contains($en, $en_key)) return $fr;
        }

        // Fallback: capitalise the English name as-is
        return ucwords($en);
    }

    /** Send one simple question about an image to Moondream via Ollama. Returns the response text or null on failure. */
    private function moondreamQuery(string $base64, string $question): ?string
    {
        $payload = json_encode([
            'model'  => 'moondream:latest',
            'prompt' => $question,
            'images' => [$base64],
            'stream' => false,
        ]);

        if ($payload === false) return null;

        $ch = curl_init('http://localhost:11434/api/generate');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 120,
        ]);

        $raw      = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($raw === false || $httpCode !== 200) return null;

        $resp = json_decode($raw, true);
        return isset($resp['response']) ? trim($resp['response']) : null;
    }

    /** Equipement CRUD API — GET / POST (FormData+image) / PUT (JSON) / DELETE */
    public function equipementApi(): void
    {
        $this->ensureSession();
        $this->requireAuth();

        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

        require_once __DIR__ . '/../Models/Equipement.php';
        require_once __DIR__ . '/../config.php';

        $model  = new \Equipement();
        $pdo    = \config::getConnexion();
        $method = $_SERVER['REQUEST_METHOD'];
        $id     = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;

        try {
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

                    if (empty($nom))       { echo json_encode(['success'=>false,'message'=>'Le nom est obligatoire.']);       exit; }
                    if (empty($reference)) { echo json_encode(['success'=>false,'message'=>'La référence est obligatoire.']); exit; }
                    if ($prix_jour <= 0)   { echo json_encode(['success'=>false,'message'=>'Le prix doit être supérieur à 0.']); exit; }
                    if (empty($categorie)) { echo json_encode(['success'=>false,'message'=>'La catégorie est obligatoire.']); exit; }

                    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM equipement WHERE reference = ?");
                    $stmtCheck->execute([$reference]);
                    if ((int)$stmtCheck->fetchColumn() > 0) {
                        echo json_encode(['success'=>false,'message'=>'La référence "'.htmlspecialchars($reference).'" est déjà utilisée.']);
                        exit;
                    }

                    $imageName = null;
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                        $allowed = ['jpg','jpeg','png','webp'];
                        if (!in_array($ext, $allowed)) { echo json_encode(['success'=>false,'message'=>'Format image non autorisé.']); exit; }
                        if ($_FILES['image']['size'] > 5*1024*1024) { echo json_encode(['success'=>false,'message'=>'Image trop grande (max 5 Mo).']); exit; }

                        $cleanRef  = preg_replace('/[^a-zA-Z0-9\-]/', '', $reference);
                        $imageName = $cleanRef . '.' . $ext;
                        $uploadDir = __DIR__ . '/../assets/images/equipements/';
                        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName)) {
                            echo json_encode(['success'=>false,'message'=>"Erreur lors de la sauvegarde de l'image."]); exit;
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
                        'message' => $ok
                            ? 'Équipement "'.htmlspecialchars($nom).'" ajouté avec succès !'
                            : "Erreur lors de l'insertion en base de données.",
                    ]);
                    break;

                case 'PUT':
                    if (!$id) { echo json_encode(['success'=>false,'message'=>'ID manquant.']); exit; }
                    $data = json_decode(file_get_contents('php://input'), true);
                    if (!$data) { echo json_encode(['success'=>false,'message'=>'Données JSON invalides.']); exit; }

                    $newRef = trim($data['reference'] ?? '');
                    if ($newRef) {
                        $stmtMod = $pdo->prepare("SELECT COUNT(*) FROM equipement WHERE reference = ? AND id != ?");
                        $stmtMod->execute([$newRef, $id]);
                        if ((int)$stmtMod->fetchColumn() > 0) {
                            echo json_encode(['success'=>false,'message'=>'La référence "'.htmlspecialchars($newRef).'" est déjà utilisée.']);
                            exit;
                        }
                    }

                    $ok = $model->update($id, [
                        'reference' => htmlspecialchars($newRef),
                        'nom'       => htmlspecialchars(trim($data['nom']       ?? '')),
                        'categorie' => htmlspecialchars(trim($data['categorie'] ?? '')),
                        'prix_jour' => (float)($data['prix_jour'] ?? 0),
                        'statut'    => in_array($data['statut'] ?? '', ['disponible','loue','maintenance'])
                                        ? $data['statut'] : 'disponible',
                        'image'     => $data['image'] ?? null,
                    ]);
                    echo json_encode([
                        'success' => $ok,
                        'message' => $ok ? 'Équipement modifié avec succès.' : 'Erreur lors de la modification.',
                    ]);
                    break;

                case 'DELETE':
                    if (!$id) { echo json_encode(['success'=>false,'message'=>'ID manquant.']); exit; }
                    $ok = $model->delete($id);
                    echo json_encode([
                        'success' => $ok,
                        'message' => $ok ? 'Équipement supprimé.' : 'Erreur lors de la suppression.',
                    ]);
                    break;

                default:
                    http_response_code(405);
                    echo json_encode(['success'=>false,'message'=>'Méthode non autorisée.']);
            }

        } catch (\PDOException $e) {
            http_response_code(500);
            if ($e->getCode() == 23000) {
                echo json_encode(['success'=>false,'message'=>'La référence est déjà utilisée. Elle doit être unique.']);
            } else {
                echo json_encode(['success'=>false,'message'=>'Erreur base de données : '.$e->getMessage()]);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success'=>false,'message'=>'Erreur : '.$e->getMessage()]);
        }
        exit;
    }

    /** Create a Stripe PaymentIntent and return the clientSecret */
    public function createPaymentIntent(): void
    {
        $this->ensureSession();
        $this->requireAuth();

        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); echo json_encode(['error' => 'Method not allowed']); return;
        }

        $body        = json_decode(file_get_contents('php://input'), true);
        $amountCents = (int)($body['amount_cents'] ?? 0);

        if ($amountCents < 50) {
            http_response_code(400); echo json_encode(['error' => 'Montant invalide']); return;
        }

        $secretKey = STRIPE_SECRET_KEY;

        $ch = curl_init('https://api.stripe.com/v1/payment_intents');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'amount'                          => $amountCents,
                'currency'                        => 'eur',
                'automatic_payment_methods[enabled]' => 'true',
            ]),
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $secretKey,
                'Content-Type: application/x-www-form-urlencoded',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
        ]);

        $raw      = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            $err = json_decode($raw, true);
            http_response_code(502);
            echo json_encode(['error' => $err['error']['message'] ?? 'Erreur Stripe']);
            return;
        }

        $intent = json_decode($raw, true);
        echo json_encode(['clientSecret' => $intent['client_secret']]);
    }

    /** Proxy for Reservation CRUD — GET / POST / PUT / DELETE */
    public function reservationApi(): void
    {
        $this->ensureSession();
        $this->requireAuth();

        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

        require_once __DIR__ . '/../Models/Reservation.php';
        require_once __DIR__ . '/../Models/Equipement.php';

        $model  = new \Reservation();
        $method = $_SERVER['REQUEST_METHOD'];
        $id     = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;

        try {
            switch ($method) {
                case 'GET':
                    echo json_encode($id ? ($model->getById($id) ?: []) : $model->getAll());
                    break;

                case 'POST':
                    $data = json_decode(file_get_contents('php://input'), true);
                    if (!$data) { echo json_encode(['success' => false, 'message' => 'JSON invalide.']); exit; }
                    if (empty($data['equipement_id']) || empty($data['locataire_nom']) || empty($data['date_debut'])) {
                        echo json_encode(['success' => false, 'message' => 'Champs obligatoires manquants.']); exit;
                    }
                    $eq = (new \Equipement())->getById((int)$data['equipement_id']);
                    if (!$eq) { echo json_encode(['success' => false, 'message' => 'Equipement introuvable.']); exit; }

                    $matricule = $_SESSION['user']['matricule'] ?? null;

                    $allowedMethods = ['espece', 'enligne', 'clinique'];
                    $allowedPStatus = ['pending', 'paid', 'failed'];

                    $ok = $model->create([
                        'equipement_id'     => (int)$data['equipement_id'],
                        'locataire_nom'     => htmlspecialchars(trim($data['locataire_nom'])),
                        'matricule'         => $matricule,
                        'locataire_ville'   => htmlspecialchars(trim($data['locataire_ville'] ?? '')),
                        'date_debut'        => $data['date_debut'],
                        'date_fin'          => !empty($data['date_fin']) ? $data['date_fin'] : null,
                        'statut'            => in_array($data['statut'] ?? '', ['en_cours','termine','en_retard']) ? $data['statut'] : 'en_cours',
                        'telephone'         => htmlspecialchars(trim($data['telephone'] ?? '')),
                        'payment_method'    => in_array($data['payment_method'] ?? '', $allowedMethods) ? $data['payment_method'] : 'espece',
                        'payment_status'    => in_array($data['payment_status'] ?? '', $allowedPStatus) ? $data['payment_status'] : 'pending',
                        'stripe_payment_id' => preg_match('/^pi_[a-zA-Z0-9_]+$/', $data['stripe_payment_id'] ?? '') ? $data['stripe_payment_id'] : null,
                    ]);
                    echo json_encode(['success' => $ok, 'message' => $ok ? 'Réservation créée.' : 'Échec insertion.']);
                    break;

                case 'PUT':
                    if (!$id) { echo json_encode(['success' => false, 'message' => 'ID missing.']); exit; }
                    $data = json_decode(file_get_contents('php://input'), true);
                    if (!$data) { echo json_encode(['success' => false, 'message' => 'Invalid JSON.']); exit; }

                    $ok = $model->update($id, [
                        'equipement_id'   => (int)($data['equipement_id'] ?? 0),
                        'locataire_nom'   => htmlspecialchars(trim($data['locataire_nom'] ?? '')),
                        'locataire_ville' => htmlspecialchars(trim($data['locataire_ville'] ?? '')),
                        'date_debut'      => $data['date_debut'] ?? '',
                        'date_fin'        => !empty($data['date_fin']) ? $data['date_fin'] : null,
                        'statut'          => in_array($data['statut'] ?? '', ['en_cours','termine','en_retard']) ? $data['statut'] : 'en_cours',
                        'telephone'       => htmlspecialchars(trim($data['telephone'] ?? '')),
                    ]);
                    echo json_encode(['success' => $ok, 'message' => $ok ? 'Réservation modifiée.' : 'Échec modification.']);
                    break;

                case 'DELETE':
                    if (!$id) { echo json_encode(['success' => false, 'message' => 'ID missing.']); exit; }
                    $ok = $model->delete($id);
                    echo json_encode(['success' => $ok, 'message' => $ok ? 'Réservation supprimée.' : 'Échec suppression.']);
                    break;

                default:
                    http_response_code(405);
                    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /* ────────────────────────────────────────────
       ✅ FONCTIONNALITÉ MÉTIER — VÉRIFICATION DISPONIBILITÉ
       API : GET /equipment/api/disponibilite
             ?equipement_id=X&date_debut=YYYY-MM-DD&date_fin=YYYY-MM-DD
    ──────────────────────────────────────────── */

    /**
     * Vérifie en temps réel si un équipement est disponible
     * sur une période donnée — appelé par reservation.php via fetch()
     * dès que le patient choisit ses dates (avant de confirmer).
     *
     * Retourne :
     *   { "disponible": true }
     *   { "disponible": false, "date_debut_conflit": "...", "date_fin_conflit": "...", "message": "..." }
     */
    public function checkDisponibilite(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, OPTIONS');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        // ── Récupérer et valider les paramètres ──
        $equipementId = isset($_GET['equipement_id']) && is_numeric($_GET['equipement_id'])
            ? (int)$_GET['equipement_id']
            : null;
        $dateDebut = trim($_GET['date_debut'] ?? '');
        $dateFin   = trim($_GET['date_fin']   ?? '');

        if (!$equipementId || empty($dateDebut) || empty($dateFin)) {
            echo json_encode(['disponible' => false, 'message' => 'Paramètres manquants.']);
            return;
        }

        if ($dateFin <= $dateDebut) {
            echo json_encode(['disponible' => false, 'message' => 'La date de fin doit être après la date de début.']);
            return;
        }

        try {
            require_once __DIR__ . '/../config.php';
            $pdo = \config::getConnexion();

            // ── Vérifier chevauchement ──
            // Deux périodes se chevauchent si :
            //   debut_A <= fin_B  ET  fin_A >= debut_B
            $stmt = $pdo->prepare("
                SELECT id, date_debut, date_fin
                FROM reservation
                WHERE equipement_id = :equip_id
                  AND statut NOT IN ('termine')
                  AND date_debut <= :date_fin
                  AND (date_fin IS NULL OR date_fin >= :date_debut)
                LIMIT 1
            ");

            $stmt->execute([
                ':equip_id'   => $equipementId,
                ':date_debut' => $dateDebut,
                ':date_fin'   => $dateFin,
            ]);

            $conflit = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($conflit) {
                // ❌ Période occupée — retourner les dates du conflit
                echo json_encode([
                    'disponible'         => false,
                    'date_debut_conflit' => $conflit['date_debut'],
                    'date_fin_conflit'   => $conflit['date_fin'],
                    'message'            => sprintf(
                        'Une réservation est déjà confirmée du %s au %s. Veuillez choisir d\'autres dates.',
                        (new \DateTime($conflit['date_debut']))->format('d/m/Y'),
                        $conflit['date_fin']
                            ? (new \DateTime($conflit['date_fin']))->format('d/m/Y')
                            : '?'
                    )
                ]);
            } else {
                // ✅ Disponible
                echo json_encode(['disponible' => true]);
            }

        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['disponible' => false, 'message' => 'Erreur base de données.']);
        }
    }
}