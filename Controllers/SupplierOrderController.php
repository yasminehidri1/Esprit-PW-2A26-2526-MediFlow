<?php
/**
 * Supplier Order Controller
 * Gère l'affichage des commandes - BackOffice Fournisseur (Lecture seule)
 * 
 * @package MediFlow\Controllers
 * @version 1.0.0
 */

class SupplierOrderController {
    private $orderModel;

    public function __construct() {
        $this->orderModel = new Order();
    }

    /**
     * Affiche la liste des commandes (Lecture seule)
     */
    public function list() {
        try {
            $commandes = $this->orderModel->getAllOrders();
            require_once 'views/Back-Supplier/orders_list.php';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            require_once 'views/Back-Supplier/error.php';
        }
    }

    /**
     * Affiche une commande avec ses articles (Lecture seule)
     */
    public function view() {
        try {
            $commandeId = isset($_GET['id']) ? intval($_GET['id']) : null;
            
            if (!$commandeId) {
                throw new Exception('Commande non spécifiée');
            }

            $commande = $this->orderModel->getOrderWithLines($commandeId);
            
            if (!$commande) {
                throw new Exception('Commande introuvable');
            }

            require_once 'views/Back-Supplier/order_view.php';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ?action=supplier&controller=orders&method=list');
            exit;
        }
    }
}
?>
