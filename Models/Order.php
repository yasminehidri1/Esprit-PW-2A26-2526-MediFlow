<?php
/**
 * Order Model — adapté aux vraies tables mediflow
 *
 * Tables : commandes, lignescommandes, produits (toutes minuscules)
 * Statuts : 'en attente', 'validée', 'livrée', 'annulée', 'retournée'
 */

class Order {
    private $pdo;

    // Statuts valides (valeurs exactes de l'ENUM DB)
    public const STATUT_EN_ATTENTE = 'en attente';
    public const STATUT_VALIDEE    = 'validée';
    public const STATUT_LIVREE     = 'livrée';
    public const STATUT_ANNULEE    = 'annulée';
    public const STATUT_RETOURNEE  = 'retournée';

    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    // ─── Création d'une commande (par Pharmacien) ────────────────────────────

    /**
     * Crée une commande avec ses lignes
     * @param array $items Articles du panier
     * @param string|null $matricule Matricule du pharmacien (optionnel)
     * @return int ID de la commande créée
     */
    public function createOrder(array $items, ?string $matricule = null): int
    {
        try {
            $this->pdo->beginTransaction();

            // 1. Créer la commande
            $stmt = $this->pdo->prepare("
                INSERT INTO commandes (date_commandes, statut, pharmacien_matricule)
                VALUES (NOW(), :statut, :matricule)
            ");
            $stmt->execute([
                'statut' => self::STATUT_EN_ATTENTE,
                'matricule' => $matricule
            ]);
            $commandeId = $this->pdo->lastInsertId();

            // 2. Ajouter les lignes de commande
            foreach ($items as $item) {
                // Vérifier que le produit existe et que le stock est suffisant
                $productStmt = $this->pdo->prepare("
                    SELECT id, nom, prix_unitaire, quantite_disponible
                    FROM produits
                    WHERE id = ?
                ");
                $productStmt->execute([$item['id']]);
                $product = $productStmt->fetch(\PDO::FETCH_ASSOC);

                if (!$product) {
                    throw new \Exception("Produit ID {$item['id']} introuvable");
                }

                $qty = intval($item['quantite'] ?? 1);

                if ($product['quantite_disponible'] < $qty) {
                    throw new \Exception(
                        "Stock insuffisant pour {$product['nom']}: disponible {$product['quantite_disponible']}, demandé {$qty}"
                    );
                }

                // Ligne de commande
                $ligneStmt = $this->pdo->prepare("
                    INSERT INTO lignescommandes (commande_id, produit_id, quantite_demande, prix)
                    VALUES (?, ?, ?, ?)
                ");
                $ligneStmt->execute([
                    $commandeId,
                    $item['id'],
                    $qty,
                    $product['prix_unitaire']
                ]);

                // Réduire le stock
                $updateStmt = $this->pdo->prepare("
                    UPDATE produits
                    SET quantite_disponible = quantite_disponible - ?
                    WHERE id = ?
                ");
                $updateStmt->execute([$qty, $item['id']]);
            }

            $this->pdo->commit();
            return (int)$commandeId;

        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    // ─── Lecture ─────────────────────────────────────────────────────────────

    /**
     * Toutes les commandes avec stats
     */
    public function getAllOrders(): array
    {
        $stmt = $this->pdo->query("
            SELECT c.*,
                   COUNT(l.id)                     AS nombre_articles,
                   SUM(l.quantite_demande * l.prix) AS total
            FROM commandes c
            LEFT JOIN lignescommandes l ON c.id = l.commande_id
            GROUP BY c.id
            ORDER BY c.id DESC
        ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Commandes filtrées par statut
     */
    public function getOrdersByStatut(string $statut): array
    {
        $stmt = $this->pdo->prepare("
            SELECT c.*,
                   COUNT(l.id)                     AS nombre_articles,
                   SUM(l.quantite_demande * l.prix) AS total
            FROM commandes c
            LEFT JOIN lignescommandes l ON c.id = l.commande_id
            WHERE c.statut = ?
            GROUP BY c.id
            ORDER BY c.id DESC
        ");
        $stmt->execute([$statut]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Une commande avec ses lignes de détail
     */
    public function getOrderWithLines(int $commandeId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT c.*,
                   COUNT(l.id)                     AS nombre_articles,
                   SUM(l.quantite_demande * l.prix) AS total
            FROM commandes c
            LEFT JOIN lignescommandes l ON c.id = l.commande_id
            WHERE c.id = ?
            GROUP BY c.id
        ");
        $stmt->execute([$commandeId]);
        $commande = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$commande) {
            return null;
        }

        // Lignes détaillées
        $lignesStmt = $this->pdo->prepare("
            SELECT l.*, p.nom, p.categorie, p.image
            FROM lignescommandes l
            JOIN produits p ON l.produit_id = p.id
            WHERE l.commande_id = ?
        ");
        $lignesStmt->execute([$commandeId]);
        $commande['lignes'] = $lignesStmt->fetchAll(\PDO::FETCH_ASSOC);

        return $commande;
    }

    // ─── Mise à jour du statut ────────────────────────────────────────────────

    /**
     * Met à jour le statut d'une commande
     * @param int    $commandeId
     * @param string $statut  Valeur exacte de l'ENUM DB
     * @return bool
     */
    public function updateOrderStatus(int $commandeId, string $statut): bool
    {
        $allowed = [
            self::STATUT_EN_ATTENTE,
            self::STATUT_VALIDEE,
            self::STATUT_LIVREE,
            self::STATUT_ANNULEE,
            self::STATUT_RETOURNEE,
        ];

        if (!in_array($statut, $allowed, true)) {
            throw new \InvalidArgumentException("Statut invalide : {$statut}");
        }

        $stmt = $this->pdo->prepare("
            UPDATE commandes SET statut = ? WHERE id = ?
        ");
        return $stmt->execute([$statut, $commandeId]);
    }

    /**
     * Restitue le stock d'une commande (quand annulée ou retournée)
     * @param int $commandeId
     */
    public function restoreStock(int $commandeId): void
    {
        $this->pdo->beginTransaction();
        try {
            $lignes = $this->pdo->prepare("
                SELECT produit_id, quantite_demande FROM lignescommandes WHERE commande_id = ?
            ");
            $lignes->execute([$commandeId]);

            foreach ($lignes->fetchAll(\PDO::FETCH_ASSOC) as $l) {
                $this->pdo->prepare("
                    UPDATE produits
                    SET quantite_disponible = quantite_disponible + ?
                    WHERE id = ?
                ")->execute([$l['quantite_demande'], $l['produit_id']]);
            }
            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Compte les commandes par statut
     */
    public function countByStatut(): array
    {
        $stmt = $this->pdo->query("
            SELECT statut, COUNT(*) AS total
            FROM commandes
            GROUP BY statut
        ");
        $result = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $result[$row['statut']] = (int)$row['total'];
        }
        return $result;
    }
}
?>
