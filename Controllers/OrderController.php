<?php
/**
 * Controller pour les Commandes
 */

class OrderController {
    private $orderModel;

    public function __construct() {
        $this->orderModel = new Order();
    }

    /**
     * Crée une commande depuis le panier (localStorage)
     * Accepte JSON POST
     */
    public function create() {
        header('Content-Type: application/json');

        try {
            // Récupérer les données JSON du panier
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['cart']) || empty($input['cart'])) {
                throw new Exception('Panier vide');
            }

            // Valider les articles
            $cart = $input['cart'];
            if (!is_array($cart)) {
                $cart = json_decode($input['cart'], true);
            }

            if (!is_array($cart) || empty($cart)) {
                throw new Exception('Format de panier invalide');
            }

            // Créer la commande
            $commandeId = $this->orderModel->createOrder($cart);

            // Retourner la réponse
            echo json_encode([
                'success' => true,
                'message' => 'Commande créée avec succès',
                'commande_id' => $commandeId,
                'redirect' => '?action=orders&method=view&id=' . $commandeId
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Met à jour une commande existante
     * Accepte JSON POST
     */
    public function update() {
        header('Content-Type: application/json');

        try {
            // Récupérer les données JSON
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['order_id'])) {
                throw new Exception('ID de commande manquant');
            }

            if (!isset($input['cart']) || empty($input['cart'])) {
                throw new Exception('Panier vide');
            }

            $orderId = intval($input['order_id']);
            $cart = $input['cart'];

            if (!is_array($cart)) {
                $cart = json_decode($input['cart'], true);
            }

            if (!is_array($cart) || empty($cart)) {
                throw new Exception('Format de panier invalide');
            }

            // Vérifier que la commande existe
            $existingOrder = $this->orderModel->getOrderWithLines($orderId);
            if (!$existingOrder) {
                throw new Exception('Commande non trouvée');
            }

            // Mettre à jour la commande
            $this->orderModel->updateOrder($orderId, $cart);

            // Retourner la réponse
            echo json_encode([
                'success' => true,
                'message' => 'Commande modifiée avec succès',
                'commande_id' => $orderId,
                'redirect' => '?action=orders&method=view&id=' . $orderId
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Affiche la liste des commandes
     */
    public function list() {
        $commandes = $this->orderModel->getAllOrders();
        require_once 'views/Back/orders_list.php';
    }

    /**
     * Affiche une commande avec ses articles
     */
    public function view() {
        $commandeId = isset($_GET['id']) ? intval($_GET['id']) : null;
        
        if (!$commandeId) {
            $_SESSION['error'] = 'Commande non spécifiée';
            header('Location: ?action=orders&method=list');
            exit;
        }

        $commande = $this->orderModel->getOrderWithLines($commandeId);
        
        if (!$commande) {
            $_SESSION['error'] = 'Commande introuvable';
            header('Location: ?action=orders&method=list');
            exit;
        }

        require_once 'views/Back/order_view.php';
    }

    /**
     * Valide ou annule une commande
     * Accepte POST
     */
    public function validate() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Méthode non autorisée");
            }

            $orderId = isset($_POST['order_id']) ? intval($_POST['order_id']) : null;
            $action = isset($_POST['action']) ? $_POST['action'] : null;

            if (!$orderId || !$action) {
                throw new Exception("Données manquantes");
            }

            // Valider l'action
            if (!in_array($action, ['valider', 'annuler'])) {
                throw new Exception("Action invalide");
            }

            // Déterminer le nouveau statut
            $newStatus = ($action === 'valider') ? 'validée' : 'annulée';

            // Vérifier que la commande existe et est en attente
            $commande = $this->orderModel->getOrderWithLines($orderId);
            if (!$commande) {
                throw new Exception("Commande non trouvée");
            }

            if ($commande['statut'] !== 'en_attente') {
                throw new Exception("Seules les commandes en attente peuvent être modifiées");
            }

            // Mettre à jour le statut
            $this->orderModel->updateOrderStatus($orderId, $newStatus);

            $_SESSION['success'] = [$action === 'valider' ? 'Commande validée avec succès' : 'Commande annulée'];
            
            header('Location: ?action=orders&method=view&id=' . $orderId);
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ?action=orders&method=list');
            exit;
        }
    }
}
?>
