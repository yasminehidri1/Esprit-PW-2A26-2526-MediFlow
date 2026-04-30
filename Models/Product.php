<?php
/**
 * Product Model
 * Gère toutes les opérations sur les produits via PDO
 * 
 * @package MediFlow\Models
 * @version 1.0.0
 */

class Product {
    private $pdo;
    private $table = 'produits'; // table réelle en minuscules
    
    // Propriétés du produit
    private $id;
    private $nom;
    private $image;
    private $categorie;
    private $quantite_disponible;
    private $prix_unitaire;
    private $seuil_alerte;
    private $prix_achat;
    private $fournisseur_matricule;
    
    /**
     * Constructeur
     */
    public function __construct() {
        $this->pdo = config::getConnexion();
    }
    
    // ===== GETTERS =====
    public function getId() {
        return $this->id;
    }
    
    public function getNom() {
        return $this->nom;
    }
    
    public function getImage() {
        return $this->image;
    }
    
    public function getCategorie() {
        return $this->categorie;
    }
    
    public function getQuantiteDisponible() {
        return $this->quantite_disponible;
    }
    
    public function getPrixUnitaire() {
        return $this->prix_unitaire;
    }
    
    public function getSeuilAlerte() {
        return $this->seuil_alerte;
    }
    
    public function getPrixAchat() {
        return $this->prix_achat;
    }
    
    public function getFournisseurMatricule() {
        return $this->fournisseur_matricule;
    }
    
    // ===== SETTERS =====
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function setNom($nom) {
        $this->nom = $nom;
        return $this;
    }
    
    public function setImage($image) {
        $this->image = $image;
        return $this;
    }
    
    public function setCategorie($categorie) {
        $this->categorie = $categorie;
        return $this;
    }
    
    public function setQuantiteDisponible($quantite) {
        $this->quantite_disponible = $quantite;
        return $this;
    }
    
    public function setPrixUnitaire($prix) {
        $this->prix_unitaire = $prix;
        return $this;
    }
    
    public function setSeuilAlerte($seuil) {
        $this->seuil_alerte = $seuil;
        return $this;
    }
    
    public function setPrixAchat($prix) {
        $this->prix_achat = $prix;
        return $this;
    }

    public function setFournisseurMatricule($matricule) {
        $this->fournisseur_matricule = $matricule;
        return $this;
    }
    
    // ===== CRUD OPERATIONS =====
    
    /**
     * Récupère tous les produits
     * @return array
     */
    public function getAll() {
        try {
            $sql = "SELECT * FROM {$this->table} ORDER BY nom ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la récupération des produits: " . $e->getMessage());
        }
    }
    
    /**
     * Récupère un produit par ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la récupération du produit: " . $e->getMessage());
        }
    }
    
    /**
     * Recherche des produits par mot-clé
     * @param string $keyword
     * @return array
     */
    public function search($keyword) {
        try {
            // Validation: enlever les caractères spéciaux
            $keyword = htmlspecialchars(strip_tags($keyword));
            
            if (strlen($keyword) < 2) {
                throw new Exception("Au moins 2 caractères requis");
            }
            
            $sql = "SELECT * FROM {$this->table} WHERE nom LIKE ? ORDER BY nom ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['%' . $keyword . '%']);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la recherche: " . $e->getMessage());
        }
    }
    
    /**
     * Filtre les produits par catégorie
     * @param string $categorie
     * @return array
     */
    public function getByCategorie($categorie) {
        try {
            // Validation ENUM
            $categoriesValides = ['comprimés', 'sirops', 'injectables'];
            if (!in_array($categorie, $categoriesValides)) {
                throw new Exception("Catégorie invalide");
            }
            
            $sql = "SELECT * FROM {$this->table} WHERE categorie = ? ORDER BY nom ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$categorie]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Erreur lors du filtrage: " . $e->getMessage());
        }
    }
    
    /**
     * Récupère les produits en stock critique (< seuil d'alerte)
     * @return array
     */
    public function getLowStock() {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE quantite_disponible < seuil_alerte ORDER BY quantite_disponible ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la récupération des stocks critiques: " . $e->getMessage());
        }
    }
    
    /**
     * Ajoute un nouveau produit
     * @return bool
     */
    public function create() {
        try {
            // Validation des champs
            $this->validate();
            
            $sql = "INSERT INTO {$this->table} 
                    (nom, image, categorie, quantite_disponible, prix_unitaire, seuil_alerte, prix_achat, fournisseur_matricule) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $this->nom,
                $this->image,
                $this->categorie,
                $this->quantite_disponible,
                $this->prix_unitaire,
                $this->seuil_alerte,
                $this->prix_achat,
                $this->fournisseur_matricule
            ]);
            
            $this->id = $this->pdo->lastInsertId();
            return true;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la création du produit: " . $e->getMessage());
        }
    }
    
    /**
     * Modifie un produit existant
     * @return bool
     */
    public function update() {
        try {
            if (!$this->id) {
                throw new Exception("ID du produit requis");
            }
            
            $this->validate();
            
            $sql = "UPDATE {$this->table} 
                    SET nom = ?, image = ?, categorie = ?, quantite_disponible = ?, 
                        prix_unitaire = ?, seuil_alerte = ?, prix_achat = ?, fournisseur_matricule = ? 
                    WHERE id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $this->nom,
                $this->image,
                $this->categorie,
                $this->quantite_disponible,
                $this->prix_unitaire,
                $this->seuil_alerte,
                $this->prix_achat,
                $this->fournisseur_matricule,
                $this->id
            ]);
            
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la modification du produit: " . $e->getMessage());
        }
    }
    
    /**
     * Supprime un produit
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        try {
            if (!is_numeric($id) || $id <= 0) {
                throw new Exception("ID invalide");
            }
            
            $sql = "DELETE FROM {$this->table} WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la suppression du produit: " . $e->getMessage());
        }
    }
    
    /**
     * Met à jour le stock d'un produit
     * @param int $id
     * @param int $quantite
     * @return bool
     */
    public function updateStock($id, $quantite) {
        try {
            if (!is_numeric($id) || $id <= 0) {
                throw new Exception("ID invalide");
            }
            
            if (!is_numeric($quantite) || $quantite < 0) {
                throw new Exception("Quantité invalide");
            }
            
            $sql = "UPDATE {$this->table} SET quantite_disponible = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$quantite, $id]);
            
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la mise à jour du stock: " . $e->getMessage());
        }
    }
    
    // ===== VALIDATION =====
    
    /**
     * Valide les données du produit côté serveur (PAS HTML5)
     * @throws Exception
     */
    private function validate() {
        // Nom requis et longueur
        if (empty($this->nom) || strlen($this->nom) < 3 || strlen($this->nom) > 100) {
            throw new Exception("Le nom doit contenir entre 3 et 100 caractères");
        }
        
        // Catégorie valide (ENUM)
        $categoriesValides = ['comprimés', 'sirops', 'injectables'];
        if (!in_array($this->categorie, $categoriesValides)) {
            throw new Exception("Catégorie invalide. Doit être: " . implode(', ', $categoriesValides));
        }
        
        // Quantité disponible
        if (!is_numeric($this->quantite_disponible) || $this->quantite_disponible < 0 || $this->quantite_disponible > 999999) {
            throw new Exception("Quantité disponible invalide");
        }
        
        // Prix unitaire
        if (!is_numeric($this->prix_unitaire) || $this->prix_unitaire <= 0 || $this->prix_unitaire > 99999.99) {
            throw new Exception("Prix unitaire invalide");
        }
        
        // Seuil d'alerte
        if (!is_numeric($this->seuil_alerte) || $this->seuil_alerte < 0 || $this->seuil_alerte > 999999) {
            throw new Exception("Seuil d'alerte invalide");
        }
        
        // Prix d'achat
        if (!is_numeric($this->prix_achat) || $this->prix_achat <= 0 || $this->prix_achat > 99999.99) {
            throw new Exception("Prix d'achat invalide");
        }
        
        // Image (optionnel mais validé si présent)
        if (!empty($this->image) && strlen($this->image) > 255) {
            throw new Exception("Chemin image trop long (max 255 caractères)");
        }
    }
    
    /**
     * Compte le nombre total de produits
     * @return int
     */
    public function count() {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table}";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (Exception $e) {
            throw new Exception("Erreur lors du comptage: " . $e->getMessage());
        }
    }
}
?>
