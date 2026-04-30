<?php
/**
 * Front Order Controller
 * Gère l'affichage des commandes dans le front office (LECTURE SEULE)
 * 
 * @package MediFlow\Controllers
 * @version 1.0.0
 */

class FrontOrderController {
    private $orderModel;
    private $errors = [];
    
    /**
     * Constructeur
     */
    public function __construct() {
        $this->orderModel = new Order();
    }
    
    /**
     * Affiche la liste des commandes (Front Office)
     */
    public function list() {
        try {
            $commandes = $this->orderModel->getAllOrders();
            
            include 'views/Front/orders_list.php';
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            include 'views/Back/error.php';
        }
    }
    
    /**
     * Affiche le détail d'une commande (Front Office - LECTURE SEULE)
     */
    public function view() {
        try {
            // Validation de l'ID
            if (!isset($_GET['id'])) {
                throw new Exception("ID de la commande manquant");
            }
            
            $id = $_GET['id'];
            
            // Validation serveur : ID doit être un nombre positif
            if (!is_numeric($id) || intval($id) <= 0) {
                throw new Exception("ID invalide");
            }
            
            $id = intval($id);
            
            // Récupérer la commande via le modèle
            $commande = $this->orderModel->getOrderWithLines($id);
            
            if (!$commande) {
                throw new Exception("Commande non trouvée");
            }
            
            include 'views/Front/order_detail.php';
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            include 'views/Back/error.php';
        }
    }
}
?>
