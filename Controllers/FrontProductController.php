<?php
/**
 * Front Product Controller
 * Gère l'affichage des produits dans le front office (LECTURE SEULE)
 * 
 * @package MediFlow\Controllers
 * @version 1.0.0
 */

class FrontProductController {
    private $productModel;
    private $errors = [];
    
    /**
     * Constructeur
     */
    public function __construct() {
        $this->productModel = new Product();
    }
    
    /**
     * Affiche la liste des produits (Front Office)
     */
    public function list() {
        try {
            $produits = $this->productModel->getAll();
            
            include 'views/Front/products_list.php';
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            include 'views/Back/error.php';
        }
    }
    
    /**
     * Affiche le détail d'un produit (Front Office - LECTURE SEULE)
     */
    public function view() {
        try {
            // Validation de l'ID
            if (!isset($_GET['id'])) {
                throw new Exception("ID du produit manquant");
            }
            
            $id = $_GET['id'];
            
            // Validation serveur : ID doit être un nombre positif
            if (!is_numeric($id) || intval($id) <= 0) {
                throw new Exception("ID invalide");
            }
            
            $id = intval($id);
            
            // Récupérer le produit via le modèle
            $produit = $this->productModel->getById($id);
            
            if (!$produit) {
                throw new Exception("Produit non trouvé");
            }
            
            include 'views/Front/product_detail.php';
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            include 'views/Back/error.php';
        }
    }
}
?>
