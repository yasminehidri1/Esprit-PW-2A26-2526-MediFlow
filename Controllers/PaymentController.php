<?php

namespace Controllers;

use Core\SessionHelper;

class PaymentController
{
    use SessionHelper;

    private const ALLOWED_ROLES = ['Admin', 'pharmacien'];

    public function __construct()
    {
        $this->ensureSession();
        $this->requireRole();
        require_once __DIR__ . '/../config.php';
        require_once __DIR__ . '/../config_stripe.php';
        require_once __DIR__ . '/../Models/Order.php';
    }

    private function requireRole(): void
    {
        $role = $_SESSION['user']['role'] ?? '';
        if (!in_array($role, self::ALLOWED_ROLES)) {
            http_response_code(403);
            die('Accès refusé.');
        }
    }

    // ─── Paiement confirmé par Stripe ─────────────────────────────────────────

    public function success(): void
    {
        $sessionId = trim($_GET['session_id'] ?? '');
        $orderId   = intval($_GET['order_id'] ?? 0);

        if (empty($sessionId) || $orderId <= 0) {
            $this->flashError('Paramètres de retour Stripe invalides.');
            $this->redirect('/integration/stock/orders');
            return;
        }

        try {
            \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            if ($session->payment_status === 'paid') {
                $orderModel = new \Order();
                $orderModel->markAsPaid($orderId, $sessionId);
                $this->flashSuccess("Paiement confirmé ! Commande #{$orderId} réglée avec succès.");
            } else {
                $this->flashError("Paiement non finalisé (statut : {$session->payment_status}). Veuillez réessayer.");
            }
        } catch (\Throwable $e) {
            error_log('[PaymentController::success] ' . $e->getMessage());
            $this->flashError('Erreur lors de la vérification du paiement.');
        }

        $this->redirect('/integration/stock/orders/view?id=' . $orderId);
    }

    // ─── Paiement annulé par l'utilisateur ────────────────────────────────────

    public function cancel(): void
    {
        $orderId = intval($_GET['order_id'] ?? 0);

        if ($orderId > 0) {
            try {
                $orderModel = new \Order();
                $orderModel->restoreStock($orderId);
                $orderModel->updateOrderStatus($orderId, \Order::STATUT_ANNULEE);
                $this->flashError("Paiement annulé — commande #{$orderId} annulée et stock restauré.");
            } catch (\Throwable $e) {
                error_log('[PaymentController::cancel] ' . $e->getMessage());
            }
        }

        $this->redirect('/integration/stock/cart');
    }

    private function flashSuccess(string $msg): void { $_SESSION['flash_success'] = $msg; }
    private function flashError(string $msg): void   { $_SESSION['flash_error']   = $msg; }
    private function redirect(string $url): void     { header('Location: ' . $url); exit; }
}
