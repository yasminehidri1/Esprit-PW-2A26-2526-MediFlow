<?php
/**
 * Equipement.php — Model
 * Toutes les opérations BDD sur la table `equipement`
 */

require_once __DIR__ . '/../config.php';

class Equipement {

    private $pdo;

    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    /* ════════════════════════════
       READ ALL
    ════════════════════════════ */
    public function getAll() {
        return $this->pdo
            ->query("SELECT * FROM equipement ORDER BY id DESC")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ════════════════════════════
       READ ONE — par ID
    ════════════════════════════ */
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM equipement WHERE id = ?");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* ════════════════════════════
       READ — Disponibles seulement
    ════════════════════════════ */
    public function getDisponibles() {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM equipement WHERE statut = 'disponible' ORDER BY id DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ════════════════════════════
       CREATE
    ════════════════════════════ */
    public function create($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO equipement (reference, nom, categorie, prix_jour, statut, image)
            VALUES (:reference, :nom, :categorie, :prix_jour, :statut, :image)
        ");
        return $stmt->execute([
            ':reference' => $data['reference'],
            ':nom'       => $data['nom'],
            ':categorie' => $data['categorie'],
            ':prix_jour' => $data['prix_jour'],
            ':statut'    => $data['statut'] ?? 'disponible',
            ':image'     => $data['image']  ?? null,
        ]);
    }

    /* ════════════════════════════
       UPDATE
    ════════════════════════════ */
    public function update($id, $data) {
        $stmt = $this->pdo->prepare("
            UPDATE equipement
            SET reference  = :reference,
                nom        = :nom,
                categorie  = :categorie,
                prix_jour  = :prix_jour,
                statut     = :statut,
                image      = :image
            WHERE id = :id
        ");
        return $stmt->execute([
            ':id'        => (int)$id,
            ':reference' => $data['reference'],
            ':nom'       => $data['nom'],
            ':categorie' => $data['categorie'],
            ':prix_jour' => $data['prix_jour'],
            ':statut'    => $data['statut'],
            ':image'     => $data['image'] ?? null,
        ]);
    }

    /* ════════════════════════════
       DELETE
    ════════════════════════════ */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM equipement WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }
}