<?php
/**
 * Product Controller
 * Gère la logique métier pour les produits
 * 
 * @package MediFlow\Controllers
 * @version 1.0.0
 */

class ProductController {
    private $productModel;
    private $errors = [];
    private $success = [];
    
    /**
     * Constructeur
     */
    public function __construct() {
        $this->productModel = new Product();
    }
    
    /**
     * Affiche la liste des produits (Backoffice)
     */
    public function list() {
        try {
            $produits = $this->productModel->getAll();
            $stocksCritiques = $this->productModel->getLowStock();
            $totalProduits = $this->productModel->count();
            
            include 'views/Back/products_list.php';
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            include 'views/Back/error.php';
        }
    }
    
    /**
     * Recherche les produits par mot-clé
     */
    public function search() {
        try {
            $keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
            
            if (empty($keyword)) {
                header('Location: ?action=products&method=list');
                exit;
            }
            
            $produits = $this->productModel->search($keyword);
            $searchQuery = $keyword;
            
            include 'views/Back/products_list.php';
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            include 'views/Back/error.php';
        }
    }
    
    /**
     * Filtre les produits par catégorie
     */
    public function filter() {
        try {
            $categorie = isset($_GET['category']) ? trim($_GET['category']) : '';
            
            if (empty($categorie)) {
                header('Location: ?action=products&method=list');
                exit;
            }
            
            // Validation ENUM
            $categoriesValides = ['comprimés', 'sirops', 'injectables'];
            if (!in_array($categorie, $categoriesValides)) {
                throw new Exception("Catégorie invalide");
            }
            
            $produits = $this->productModel->getByCategorie($categorie);
            $filterCategory = $categorie;
            
            include 'views/Back/products_list.php';
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            include 'views/Back/error.php';
        }
    }
    
    /**
     * Affiche le formulaire d'ajout de produit
     */
    public function create() {
        try {
            $categories = ['comprimés', 'sirops', 'injectables'];
            include 'views/Back/product_form.php';
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            include 'views/Back/error.php';
        }
    }
    
    /**
     * Sauvegarde un nouveau produit
     */
    public function store() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Méthode non autorisée");
            }
            
            // Validation côté serveur (PAS HTML5)
            $this->validateProductData();
            
            // Gestion upload image
            $imagePath = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $imagePath = $this->handleImageUpload($_FILES['image']);
            }
            
            if (!empty($this->errors)) {
                throw new Exception(implode(', ', $this->errors));
            }
            
            // Création du produit
            $produit = new Product();
            $produit->setNom($_POST['nom'])
                   ->setImage($imagePath)
                   ->setCategorie($_POST['categorie'])
                   ->setQuantiteDisponible($_POST['quantite_disponible'])
                   ->setPrixUnitaire($_POST['prix_unitaire'])
                   ->setSeuilAlerte($_POST['seuil_alerte'])
                   ->setPrixAchat($_POST['prix_achat']);
            
            $produit->create();
            
            $this->success[] = "Produit créé avec succès!";
            $_SESSION['success'] = $this->success;
            
            header('Location: ?action=products&method=list');
            exit;
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            $_SESSION['errors'] = $this->errors;
            header('Location: ?action=products&method=create');
            exit;
        }
    }
    
    /**
     * Affiche le formulaire d'édition d'un produit
     */
    public function edit() {
        try {
            $id = isset($_GET['id']) ? intval($_GET['id']) : null;
            
            if (!$id || $id <= 0) {
                throw new Exception("ID du produit invalide");
            }
            
            $produit = $this->productModel->getById($id);
            
            if (!$produit) {
                throw new Exception("Produit non trouvé");
            }
            
            $categories = ['comprimés', 'sirops', 'injectables'];
            include 'views/Back/product_form.php';
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            include 'views/Back/error.php';
        }
    }
    
    /**
     * Met à jour un produit existant
     */
    public function update() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Méthode non autorisée");
            }
            
            $id = isset($_POST['id']) ? intval($_POST['id']) : null;
            
            if (!$id || $id <= 0) {
                throw new Exception("ID du produit invalide");
            }
            
            // Validation côté serveur
            $this->validateProductData();
            
            // Gestion upload image
            $imagePath = isset($_POST['image_existing']) ? $_POST['image_existing'] : '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $imagePath = $this->handleImageUpload($_FILES['image']);
            }
            
            if (!empty($this->errors)) {
                throw new Exception(implode(', ', $this->errors));
            }
            
            // Mise à jour du produit
            $produit = new Product();
            $produit->setId($id)
                   ->setNom($_POST['nom'])
                   ->setImage($imagePath)
                   ->setCategorie($_POST['categorie'])
                   ->setQuantiteDisponible($_POST['quantite_disponible'])
                   ->setPrixUnitaire($_POST['prix_unitaire'])
                   ->setSeuilAlerte($_POST['seuil_alerte'])
                   ->setPrixAchat($_POST['prix_achat']);
            
            $produit->update();
            
            $this->success[] = "Produit modifié avec succès!";
            $_SESSION['success'] = $this->success;
            
            header('Location: ?action=products&method=list');
            exit;
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            $_SESSION['errors'] = $this->errors;
            header('Location: ?action=products&method=edit&id=' . $id);
            exit;
        }
    }
    
    /**
     * Supprime un produit
     */
    public function delete() {
        try {
            $id = isset($_GET['id']) ? intval($_GET['id']) : null;
            
            if (!$id || $id <= 0) {
                throw new Exception("ID du produit invalide");
            }
            
            $this->productModel->delete($id);
            
            $this->success[] = "Produit supprimé avec succès!";
            $_SESSION['success'] = $this->success;
            
            header('Location: ?action=products&method=list');
            exit;
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            $_SESSION['errors'] = $this->errors;
            header('Location: ?action=products&method=list');
            exit;
        }
    }
    
    /**
     * Met à jour le stock d'un produit
     */
    public function updateStock() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Méthode non autorisée");
            }
            
            $id = isset($_POST['id']) ? intval($_POST['id']) : null;
            $quantite = isset($_POST['quantite']) ? intval($_POST['quantite']) : null;
            
            if (!$id || $id <= 0) {
                throw new Exception("ID du produit invalide");
            }
            
            if ($quantite === null || $quantite < 0) {
                throw new Exception("Quantité invalide");
            }
            
            $this->productModel->updateStock($id, $quantite);
            
            $this->success[] = "Stock mis à jour avec succès!";
            $_SESSION['success'] = $this->success;
            
            header('Location: ?action=products&method=list');
            exit;
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            $_SESSION['errors'] = $this->errors;
            header('Location: ?action=products&method=list');
            exit;
        }
    }
    
    /**
     * Valide les données du formulaire produit
     * ⚠️ VALIDATION CÔTÉ SERVEUR - PAS HTML5
     */
    private function validateProductData() {
        // Nom
        if (empty($_POST['nom'])) {
            $this->errors[] = "Le nom du produit est requis";
        } elseif (strlen($_POST['nom']) < 3 || strlen($_POST['nom']) > 100) {
            $this->errors[] = "Le nom doit contenir entre 3 et 100 caractères";
        }
        
        // Catégorie
        $categoriesValides = ['comprimés', 'sirops', 'injectables'];
        if (empty($_POST['categorie']) || !in_array($_POST['categorie'], $categoriesValides)) {
            $this->errors[] = "Catégorie invalide. Doit être: " . implode(', ', $categoriesValides);
        }
        
        // Quantité disponible
        if (!isset($_POST['quantite_disponible']) || !is_numeric($_POST['quantite_disponible']) || $_POST['quantite_disponible'] < 0 || $_POST['quantite_disponible'] > 999999) {
            $this->errors[] = "Quantité disponible invalide (doit être un nombre positif)";
        }
        
        // Prix unitaire
        if (!isset($_POST['prix_unitaire']) || !is_numeric($_POST['prix_unitaire']) || $_POST['prix_unitaire'] <= 0 || $_POST['prix_unitaire'] > 99999.99) {
            $this->errors[] = "Prix unitaire invalide (doit être supérieur à 0)";
        }
        
        // Seuil d'alerte
        if (!isset($_POST['seuil_alerte']) || !is_numeric($_POST['seuil_alerte']) || $_POST['seuil_alerte'] < 0 || $_POST['seuil_alerte'] > 999999) {
            $this->errors[] = "Seuil d'alerte invalide (doit être un nombre positif)";
        }
        
        // Prix d'achat
        if (!isset($_POST['prix_achat']) || !is_numeric($_POST['prix_achat']) || $_POST['prix_achat'] <= 0 || $_POST['prix_achat'] > 99999.99) {
            $this->errors[] = "Prix d'achat invalide (doit être supérieur à 0)";
        }
        
        // Image (optionnel mais validé si présent)
        if (!empty($_POST['image']) && strlen($_POST['image']) > 255) {
            $this->errors[] = "Chemin image trop long (max 255 caractères)";
        }
    }
    
    /**
     * Récupère les erreurs
     */
    public function getErrors() {
        return $this->errors;
    }
    
    
    /**
     * Gère l'upload d'image
     * @param array $file
     * @return string
     */
    private function handleImageUpload($file) {
        try {
            // Valider le fichier
            if ($file['error'] === UPLOAD_ERR_NO_FILE) {
                return '';
            }
            
            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Erreur lors de l'upload: " . $file['error']);
            }
            
            // Valider la taille (max 5MB)
            $maxSize = 5 * 1024 * 1024; // 5MB
            if ($file['size'] > $maxSize) {
                throw new Exception("L'image est trop volumineuse (max 5MB)");
            }
            
            // Valider le type MIME
            $mimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mimeType, $mimeTypes)) {
                throw new Exception("Format d'image non autorisé. Formats acceptés: JPG, PNG, GIF, WebP");
            }
            
            // Créer le dossier s'il n'existe pas
            $uploadDir = 'assets/images/produit/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Générer un nom unique
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'produit_' . time() . '_' . uniqid() . '.' . $ext;
            $filepath = $uploadDir . $filename;
            
            // Déplacer le fichier
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                throw new Exception("Erreur lors du déplacement du fichier");
            }
            
            return $filepath;
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            return '';
        }
    }
    /**
     * Récupère les messages de succès
     */
    public function getSuccess() {
        return $this->success;
    }
}
?>
