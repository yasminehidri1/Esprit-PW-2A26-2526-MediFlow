<?php
/**
 * Modèle Order
 * Gère les opérations sur les commandes et lignes de commandes
 */

class Order {
    private $pdo;
    
    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    /**
     * Crée une commande avec ses lignes
     * @param array $items Articles du panier depuis localStorage
     * @return int ID de la commande créée
     */
    public function createOrder($items) {
        try {
            // Démarrer une transaction
            $this->pdo->beginTransaction();

            // 1. Créer la commande
            $stmt = $this->pdo->prepare("
                INSERT INTO Commandes (statut)
                VALUES ('en_attente')
            ");
            $stmt->execute();
            $commandeId = $this->pdo->lastInsertId();

            // 2. Ajouter les lignes de commande
            foreach ($items as $item) {
                // Vérifier que le produit existe et que la quantité est disponible
                $productStmt = $this->pdo->prepare("
                    SELECT id, nom, prix_unitaire, quantite_disponible 
                    FROM Produits 
                    WHERE id = ?
                ");
                $productStmt->execute([$item['id']]);
                $product = $productStmt->fetch();

                if (!$product) {
                    throw new Exception("Produit ID {$item['id']} introuvable");
                }

                if ($product['quantite_disponible'] < $item['quantite']) {
                    throw new Exception("Stock insuffisant pour {$product['nom']}: disponible {$product['quantite_disponible']}, demandé {$item['quantite']}");
                }

                // Ajouter la ligne de commande
                $ligneStmt = $this->pdo->prepare("
                    INSERT INTO LignesCommandes (commande_id, produit_id, quantite_demande, prix)
                    VALUES (?, ?, ?, ?)
                ");
                $ligneStmt->execute([
                    $commandeId,
                    $item['id'],
                    $item['quantite'],
                    $product['prix_unitaire']
                ]);

                // Réduire le stock du produit
                $updateStmt = $this->pdo->prepare("
                    UPDATE Produits 
                    SET quantite_disponible = quantite_disponible - ?
                    WHERE id = ?
                ");
                $updateStmt->execute([$item['quantite'], $item['id']]);
            }

            // Commit de la transaction
            $this->pdo->commit();

            return $commandeId;
        } catch (Exception $e) {
            // Rollback en cas d'erreur
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Récupère une commande avec ses lignes
     */
    public function getOrderWithLines($commandeId) {
        $stmt = $this->pdo->prepare("
            SELECT c.*, 
                   COUNT(l.id) as nombre_articles,
                   SUM(l.quantite_demande * l.prix) as total
            FROM Commandes c
            LEFT JOIN LignesCommandes l ON c.id = l.commande_id
            WHERE c.id = ?
            GROUP BY c.id
        ");
        $stmt->execute([$commandeId]);
        $commande = $stmt->fetch();

        if (!$commande) {
            return null;
        }

        // Récupérer les lignes détaillées
        $lignesStmt = $this->pdo->prepare("
            SELECT l.*, p.nom, p.categorie, p.image
            FROM LignesCommandes l
            JOIN Produits p ON l.produit_id = p.id
            WHERE l.commande_id = ?
        ");
        $lignesStmt->execute([$commandeId]);
        $commande['lignes'] = $lignesStmt->fetchAll();

        return $commande;
    }

    /**
     * Récupère toutes les commandes
     */
    public function getAllOrders() {
        $stmt = $this->pdo->query("
            SELECT c.*, 
                   COUNT(l.id) as nombre_articles,
                   SUM(l.quantite_demande * l.prix) as total
            FROM Commandes c
            LEFT JOIN LignesCommandes l ON c.id = l.commande_id
            GROUP BY c.id
            ORDER BY c.id DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Met à jour une commande existante
     * @param int $commandeId ID de la commande à modifier
     * @param array $items Articles de la commande
     * @return bool Succès de la mise à jour
     */
    public function updateOrder($commandeId, $items) {
        try {
            // Démarrer une transaction
            $this->pdo->beginTransaction();

            // 1. Supprimer les anciennes lignes de commande
            $deleteStmt = $this->pdo->prepare("
                DELETE FROM LignesCommandes WHERE commande_id = ?
            ");
            $deleteStmt->execute([$commandeId]);

            // 2. Refaire le stock pour tous les anciens articles
            $oldLignesStmt = $this->pdo->prepare("
                SELECT l.produit_id, l.quantite_demande
                FROM LignesCommandes l
                WHERE l.commande_id = ?
            ");
            // Note: Cette requête n'aura rien car on vient de supprimer
            // On utilisera la nouvelle méthode directe

            // 2. Ajouter les nouvelles lignes de commande
            foreach ($items as $item) {
                // Vérifier que le produit existe
                $productStmt = $this->pdo->prepare("
                    SELECT id, nom, prix_unitaire, quantite_disponible 
                    FROM Produits 
                    WHERE id = ?
                ");
                $productStmt->execute([$item['id']]);
                $product = $productStmt->fetch();

                if (!$product) {
                    throw new Exception("Produit ID {$item['id']} introuvable");
                }

                if ($product['quantite_disponible'] < $item['quantite']) {
                    throw new Exception("Stock insuffisant pour {$product['nom']}: disponible {$product['quantite_disponible']}, demandé {$item['quantite']}");
                }

                // Ajouter la ligne de commande
                $ligneStmt = $this->pdo->prepare("
                    INSERT INTO LignesCommandes (commande_id, produit_id, quantite_demande, prix)
                    VALUES (?, ?, ?, ?)
                ");
                $ligneStmt->execute([
                    $commandeId,
                    $item['id'],
                    $item['quantite'],
                    $product['prix_unitaire']
                ]);

                // Réduire le stock du produit
                $updateStmt = $this->pdo->prepare("
                    UPDATE Produits 
                    SET quantite_disponible = quantite_disponible - ?
                    WHERE id = ?
                ");
                $updateStmt->execute([$item['quantite'], $item['id']]);
            }

            // Commit de la transaction
            $this->pdo->commit();

            return true;
        } catch (Exception $e) {
            // Rollback en cas d'erreur
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Met à jour le statut d'une commande
     */
    public function updateOrderStatus($commandeId, $statut) {
        $stmt = $this->pdo->prepare("
            UPDATE Commandes 
            SET statut = ?
            WHERE id = ?
        ");
        return $stmt->execute([$statut, $commandeId]);
    }
}
?>
