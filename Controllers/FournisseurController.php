<?php
/**
 * FournisseurController
 *
 * Workflow :
 *   - CRUD complet sur les produits
 *   - Confirmation / livraison des commandes
 *   - Admin → rendu dans layout.php (sidebar reste visible)
 *   - Fournisseur → rendu standalone (pages propres)
 */

namespace Controllers;

use Core\SessionHelper;

class FournisseurController
{
    use SessionHelper;

    private const ALLOWED_ROLES = ['Admin', 'Fournisseur'];

    public function __construct()
    {
        $this->ensureSession();
        $this->requireRole();
        require_once __DIR__ . '/../Models/Product.php';
        require_once __DIR__ . '/../Models/Order.php';
        require_once __DIR__ . '/../config.php';
    }

    // ─── Auth ─────────────────────────────────────────────────────────────────

    private function requireRole(): void
    {
        $role = $_SESSION['user']['role'] ?? '';
        if (!in_array($role, self::ALLOWED_ROLES)) {
            http_response_code(403);
            die('Accès refusé. Module réservé aux Fournisseurs.');
        }
    }

    /**
     * Inclut une vue en dual-mode :
     * - Admin → via layout.php ($embeddedInLayout = true → sidebar/topbar du back-office restent)
     * - Fournisseur → page standalone
     */
    private function render(string $viewFile, array $vars = []): void
    {
        extract($vars);
        
        // Toujours utiliser le layout principal du back-office pour garder la sidebar et la topbar
        $embeddedInLayout = true;
        $stockViewPath    = __DIR__ . '/../Views/Back/' . basename($viewFile);
        include __DIR__ . '/../Views/Back/layout.php';
    }

    // ─── PRODUITS — CRUD complet ──────────────────────────────────────────────

    public function productList(): void
    {
        try {
            $productModel    = new \Product();
            $produits        = $productModel->getAll();
            $stocksCritiques = $productModel->getLowStock();
            $totalProduits   = $productModel->count();
            $this->render('products_list.php', compact('produits', 'stocksCritiques', 'totalProduits'));
        } catch (\Exception $e) {
            $this->flashError($e->getMessage());
            $this->redirect('/integration/fournisseur/products');
        }
    }

    public function productCreate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->productStore();
            return;
        }
        $categories = ['comprimés', 'sirops', 'injectables'];
        $formAction = '/integration/fournisseur/products/create';
        $this->render('product_form.php', compact('categories', 'formAction'));
    }

    private function productStore(): void
    {
        try {
            $errors = $this->validateProductData();
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $this->redirect('/integration/fournisseur/products/create');
                return;
            }
            $imagePath = $this->handleImageUpload($_FILES['image'] ?? null);
            $produit = new \Product();
            $produit->setNom($_POST['nom'])
                    ->setImage($imagePath)
                    ->setCategorie($_POST['categorie'])
                    ->setQuantiteDisponible((int)$_POST['quantite_disponible'])
                    ->setPrixUnitaire((float)$_POST['prix_unitaire'])
                    ->setSeuilAlerte((int)$_POST['seuil_alerte'])
                    ->setPrixAchat((float)$_POST['prix_achat'])
                    ->setFournisseurMatricule($_SESSION['user']['matricule'] ?? null);
            $produit->create();
            $this->flashSuccess('Produit créé avec succès !');
            $this->redirect('/integration/fournisseur/products');
        } catch (\Exception $e) {
            $_SESSION['errors'] = [$e->getMessage()];
            $this->redirect('/integration/fournisseur/products/create');
        }
    }

    public function productEdit(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->productUpdate();
            return;
        }
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->flashError('ID produit invalide.');
            $this->redirect('/integration/fournisseur/products');
            return;
        }
        $productModel = new \Product();
        $produit      = $productModel->getById($id);
        if (!$produit) {
            $this->flashError('Produit introuvable.');
            $this->redirect('/integration/fournisseur/products');
            return;
        }
        $categories = ['comprimés', 'sirops', 'injectables'];
        $formAction = '/integration/fournisseur/products/edit?id=' . $id;
        $this->render('product_form.php', compact('produit', 'categories', 'formAction'));
    }

    private function productUpdate(): void
    {
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->flashError('ID produit invalide.');
            $this->redirect('/integration/fournisseur/products');
            return;
        }
        try {
            $errors = $this->validateProductData();
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $this->redirect('/integration/fournisseur/products/edit?id=' . $id);
                return;
            }
            $imagePath = $_POST['image_existing'] ?? '';
            if (!empty($_FILES['image']['name'])) {
                $imagePath = $this->handleImageUpload($_FILES['image']);
            }
            $produit = new \Product();
            $produit->setId($id)
                    ->setNom($_POST['nom'])
                    ->setImage($imagePath)
                    ->setCategorie($_POST['categorie'])
                    ->setQuantiteDisponible((int)$_POST['quantite_disponible'])
                    ->setPrixUnitaire((float)$_POST['prix_unitaire'])
                    ->setSeuilAlerte((int)$_POST['seuil_alerte'])
                    ->setPrixAchat((float)$_POST['prix_achat'])
                    ->setFournisseurMatricule($_SESSION['user']['matricule'] ?? null);
            $produit->update();
            $this->flashSuccess('Produit modifié avec succès !');
            $this->redirect('/integration/fournisseur/products');
        } catch (\Exception $e) {
            $_SESSION['errors'] = [$e->getMessage()];
            $this->redirect('/integration/fournisseur/products/edit?id=' . $id);
        }
    }

    public function productDelete(): void
    {
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->flashError('ID produit invalide.');
            $this->redirect('/integration/fournisseur/products');
            return;
        }
        try {
            (new \Product())->delete($id);
            $this->flashSuccess('Produit supprimé.');
        } catch (\Exception $e) {
            $this->flashError($e->getMessage());
        }
        $this->redirect('/integration/fournisseur/products');
    }

    public function productSearch(): void
    {
        try {
            $keyword = trim($_GET['q'] ?? '');
            if (strlen($keyword) < 2) {
                $this->redirect('/integration/fournisseur/products');
                return;
            }
            $productModel    = new \Product();
            $produits        = $productModel->search($keyword);
            $stocksCritiques = $productModel->getLowStock();
            $totalProduits   = $productModel->count();
            $searchQuery     = $keyword;
            $this->render('products_list.php', compact('produits', 'stocksCritiques', 'totalProduits', 'searchQuery'));
        } catch (\Exception $e) {
            $this->flashError($e->getMessage());
            $this->redirect('/integration/fournisseur/products');
        }
    }

    public function productFilter(): void
    {
        try {
            $categorie = trim($_GET['category'] ?? '');
            if (!in_array($categorie, ['comprimés', 'sirops', 'injectables'])) {
                $this->redirect('/integration/fournisseur/products');
                return;
            }
            $productModel    = new \Product();
            $produits        = $productModel->getByCategorie($categorie);
            $stocksCritiques = $productModel->getLowStock();
            $totalProduits   = $productModel->count();
            $filterCategory  = $categorie;
            $this->render('products_list.php', compact('produits', 'stocksCritiques', 'totalProduits', 'filterCategory'));
        } catch (\Exception $e) {
            $this->flashError($e->getMessage());
            $this->redirect('/integration/fournisseur/products');
        }
    }

    // ─── COMMANDES — confirmation ─────────────────────────────────────────────

    public function orderList(): void
    {
        try {
            $orderModel   = new \Order();
            $commandes    = $orderModel->getAllOrders();
            $statsStatuts = $orderModel->countByStatut();
            $this->render('orders_list.php', compact('commandes', 'statsStatuts'));
        } catch (\Exception $e) {
            $this->flashError($e->getMessage());
            $this->redirect('/integration/fournisseur/products');
        }
    }

    public function orderView(): void
    {
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->flashError('ID commande invalide.');
            $this->redirect('/integration/fournisseur/orders');
            return;
        }
        try {
            $orderModel = new \Order();
            $commande   = $orderModel->getOrderWithLines($id);
            if (!$commande) {
                $this->flashError('Commande introuvable.');
                $this->redirect('/integration/fournisseur/orders');
                return;
            }
            $transitionsAutorisees = $this->getTransitionsAutorisees($commande['statut']);
            $this->render('order_view.php', compact('commande', 'transitionsAutorisees'));
        } catch (\Exception $e) {
            $this->flashError($e->getMessage());
            $this->redirect('/integration/fournisseur/orders');
        }
    }

    public function orderChangeStatus(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/integration/fournisseur/orders');
            return;
        }
        $orderId   = intval($_POST['order_id'] ?? 0);
        $newStatut = $_POST['new_statut'] ?? '';
        $statutsAutorises = [\Order::STATUT_VALIDEE, \Order::STATUT_LIVREE, \Order::STATUT_ANNULEE];
        if ($orderId <= 0 || !in_array($newStatut, $statutsAutorises, true)) {
            $this->flashError('Action invalide.');
            $this->redirect('/integration/fournisseur/orders');
            return;
        }
        try {
            $orderModel = new \Order();
            $commande   = $orderModel->getOrderWithLines($orderId);
            if (!$commande) throw new \Exception('Commande introuvable.');
            $transitions = $this->getTransitionsAutorisees($commande['statut']);
            if (!array_key_exists($newStatut, $transitions)) {
                throw new \Exception("Transition non autorisée depuis « {$commande['statut']} ».");
            }
            if (in_array($newStatut, [\Order::STATUT_ANNULEE, \Order::STATUT_RETOURNEE])) {
                $orderModel->restoreStock($orderId);
            }
            $orderModel->updateOrderStatus($orderId, $newStatut);
            $labels = [
                \Order::STATUT_VALIDEE    => 'Validée',
                \Order::STATUT_LIVREE     => 'Livrée',
                \Order::STATUT_ANNULEE    => 'Annulée',
            ];
            $this->flashSuccess("Commande #{$orderId} — " . ($labels[$newStatut] ?? $newStatut));
            $this->redirect('/integration/fournisseur/orders/view?id=' . $orderId);
        } catch (\Exception $e) {
            $this->flashError($e->getMessage());
            $this->redirect('/integration/fournisseur/orders');
        }
    }

    private function getTransitionsAutorisees(string $statut): array
    {
        return match ($statut) {
            \Order::STATUT_EN_ATTENTE => [
                \Order::STATUT_VALIDEE => 'Confirmer la commande',
                \Order::STATUT_ANNULEE => 'Refuser / Annuler',
            ],
            \Order::STATUT_VALIDEE => [
                \Order::STATUT_LIVREE  => 'Marquer comme livrée',
                \Order::STATUT_ANNULEE => 'Annuler',
            ],
            default => [],
        };
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function validateProductData(): array
    {
        $errors = [];
        if (empty($_POST['nom']) || strlen($_POST['nom']) < 3 || strlen($_POST['nom']) > 100) {
            $errors[] = 'Le nom doit contenir entre 3 et 100 caractères.';
        }
        if (!in_array($_POST['categorie'] ?? '', ['comprimés', 'sirops', 'injectables'])) {
            $errors[] = 'Catégorie invalide.';
        }
        if (!isset($_POST['quantite_disponible']) || !is_numeric($_POST['quantite_disponible']) || $_POST['quantite_disponible'] < 0) {
            $errors[] = 'Quantité disponible invalide.';
        }
        if (!isset($_POST['prix_unitaire']) || !is_numeric($_POST['prix_unitaire']) || $_POST['prix_unitaire'] <= 0) {
            $errors[] = 'Prix unitaire invalide.';
        }
        if (!isset($_POST['seuil_alerte']) || !is_numeric($_POST['seuil_alerte']) || $_POST['seuil_alerte'] < 0) {
            $errors[] = "Seuil d'alerte invalide.";
        }
        if (!isset($_POST['prix_achat']) || !is_numeric($_POST['prix_achat']) || $_POST['prix_achat'] <= 0) {
            $errors[] = "Prix d'achat invalide.";
        }
        return $errors;
    }

    private function handleImageUpload(?array $file): string
    {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE || empty($file['name'])) {
            return '';
        }
        if ($file['error'] !== UPLOAD_ERR_OK) throw new \Exception("Erreur upload : " . $file['error']);
        if ($file['size'] > 5 * 1024 * 1024) throw new \Exception("Image trop volumineuse (max 5 Mo).");
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo   = finfo_open(FILEINFO_MIME_TYPE);
        $mime    = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, $allowed)) throw new \Exception("Format d'image non autorisé.");
        $uploadDir = __DIR__ . '/../assets/images/produit/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'produit_' . time() . '_' . uniqid() . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) throw new \Exception("Échec upload.");
        return 'assets/images/produit/' . $filename;
    }

    private function flashSuccess(string $msg): void { $_SESSION['flash_success'] = $msg; }
    private function flashError(string $msg): void   { $_SESSION['flash_error']   = $msg; }
    private function redirect(string $url): void     { header('Location: ' . $url); exit; }
}
?>
