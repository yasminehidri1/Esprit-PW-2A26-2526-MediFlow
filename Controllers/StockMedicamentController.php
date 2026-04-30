<?php
/**
 * StockMedicamentController (Pharmacien)
 *
 * Workflow Pharmacien :
 *   - Voir le catalogue des produits (lecture seule)
 *   - Ajouter des produits au panier
 *   - Créer des commandes (statut initial = 'en attente')
 *   - Voir ses commandes + annuler si encore 'en attente'
 *
 * @package MediFlow\Controllers
 */

namespace Controllers;

use Core\SessionHelper;

class StockMedicamentController
{
    use SessionHelper;

    private const ALLOWED_ROLES = ['Admin', 'pharmacien'];

    public function __construct()
    {
        $this->ensureSession();
        $this->requireRole();
        require_once __DIR__ . '/../Models/Product.php';
        require_once __DIR__ . '/../Models/Order.php';
        require_once __DIR__ . '/../config.php';
    }

    private function requireRole(): void
    {
        $role = $_SESSION['user']['role'] ?? '';
        if (!in_array($role, self::ALLOWED_ROLES)) {
            http_response_code(403);
            die('Accès refusé. Module réservé au rôle Stock Médicament.');
        }
    }

    /**
     * Toujours utiliser le layout principal du back-office pour garder la sidebar et la topbar.
     */
    private function render(string $viewFile, array $vars = []): void
    {
        extract($vars);
        $embeddedInLayout = true;
        $stockViewPath    = __DIR__ . '/../Views/Back/' . basename($viewFile);
        include __DIR__ . '/../Views/Back/layout.php';
    }

    // ─── PRODUITS — lecture seule + panier ────────────────────────────────────

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
            $this->redirect('/integration/stock/products');
        }
    }

    public function productSearch(): void
    {
        try {
            $keyword = trim($_GET['q'] ?? '');
            if (strlen($keyword) < 2) {
                $this->redirect('/integration/stock/products');
                return;
            }
            $productModel    = new \Product();
            $produits        = $productModel->search($keyword);
            $stocksCritiques = $productModel->getLowStock();
            $totalProduits   = $productModel->count();
            $searchQuery     = $keyword;
            $this->render('products_list.php', compact('produits', 'stocksCritiques', 'totalProduits'));
        } catch (\Exception $e) {
            $this->flashError($e->getMessage());
            $this->redirect('/integration/stock/products');
        }
    }

    public function productFilter(): void
    {
        try {
            $categorie = trim($_GET['category'] ?? '');
            if (!in_array($categorie, ['comprimés', 'sirops', 'injectables'])) {
                $this->redirect('/integration/stock/products');
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
            $this->redirect('/integration/stock/products');
        }
    }

    // ─── PANIER ───────────────────────────────────────────────────────────────

    public function cart(): void
    {
        $this->render('cart.php');
    }

    // ─── COMMANDES — création + suivi ─────────────────────────────────────────

    /**
     * Liste de toutes les commandes (le pharmacien voit ses commandes)
     */
    public function orderList(): void
    {
        try {
            $orderModel   = new \Order();
            $commandes    = $orderModel->getAllOrders();
            $statsStatuts = $orderModel->countByStatut();
            $this->render('orders_list.php', compact('commandes', 'statsStatuts'));
        } catch (\Exception $e) {
            $this->flashError($e->getMessage());
            $this->redirect('/integration/stock/orders');
        }
    }

    /**
     * Détail d'une commande
     */
    public function orderView(): void
    {
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->flashError('ID commande invalide.');
            $this->redirect('/integration/stock/orders');
            return;
        }

        try {
            $orderModel = new \Order();
            $commande   = $orderModel->getOrderWithLines($id);

            if (!$commande) {
                $this->flashError('Commande introuvable.');
                $this->redirect('/integration/stock/orders');
                return;
            }

            // Pharmacien peut uniquement annuler si 'en attente'
            $transitionsAutorisees = [];
            if ($commande['statut'] === \Order::STATUT_EN_ATTENTE) {
                $transitionsAutorisees = [
                    \Order::STATUT_ANNULEE => 'Annuler la commande',
                ];
            }
            $this->render('order_view.php', compact('commande', 'transitionsAutorisees'));
        } catch (\Exception $e) {
            $this->flashError($e->getMessage());
            $this->redirect('/integration/stock/orders');
        }
    }

    /**
     * Crée une commande depuis le panier (JSON POST depuis cart.js)
     */
    public function orderCreate(): void
    {
        header('Content-Type: application/json');

        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['cart'])) {
                throw new \Exception('Panier vide.');
            }

            $cart = is_array($input['cart']) ? $input['cart'] : json_decode($input['cart'], true);

            if (!is_array($cart) || empty($cart)) {
                throw new \Exception('Format de panier invalide.');
            }

            $orderModel = new \Order();
            $matricule  = $_SESSION['user']['matricule'] ?? null;
            $commandeId = $orderModel->createOrder($cart, $matricule);

            echo json_encode([
                'success'     => true,
                'message'     => 'Commande créée avec succès.',
                'commande_id' => $commandeId,
                'redirect'    => '/integration/stock/orders/view?id=' . $commandeId,
            ]);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Pharmacien annule sa commande si encore 'en attente'
     */
    public function orderCancel(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/integration/stock/orders');
            return;
        }

        $orderId = intval($_POST['order_id'] ?? 0);
        if ($orderId <= 0) {
            $this->flashError('ID commande invalide.');
            $this->redirect('/integration/stock/orders');
            return;
        }

        try {
            $orderModel = new \Order();
            $commande   = $orderModel->getOrderWithLines($orderId);

            if (!$commande) {
                throw new \Exception('Commande introuvable.');
            }
            if ($commande['statut'] !== \Order::STATUT_EN_ATTENTE) {
                throw new \Exception('Seules les commandes en attente peuvent être annulées par le pharmacien.');
            }

            $orderModel->restoreStock($orderId);
            $orderModel->updateOrderStatus($orderId, \Order::STATUT_ANNULEE);

            $this->flashSuccess("Commande #{$orderId} annulée et stock restauré.");
            $this->redirect('/integration/stock/orders');
        } catch (\Exception $e) {
            $this->flashError($e->getMessage());
            $this->redirect('/integration/stock/orders/view?id=' . $orderId);
        }
    }

    private function flashSuccess(string $msg): void { $_SESSION['flash_success'] = $msg; }
    private function flashError(string $msg): void   { $_SESSION['flash_error']   = $msg; }
    private function redirect(string $url): void     { header('Location: ' . $url); exit; }
}
?>
