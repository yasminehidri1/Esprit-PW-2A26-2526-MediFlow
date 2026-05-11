<?php

namespace Controllers;

use Core\SessionHelper;
use Dompdf\Dompdf;
use Dompdf\Options;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class InvoiceController
{
    use SessionHelper;

    private const ALLOWED_ROLES = ['Admin', 'pharmacien', 'Fournisseur'];

    public function __construct()
    {
        $this->ensureSession();
        $this->requireRole();
        require_once __DIR__ . '/../config.php';
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

    public function download(): void
    {
        $orderId = intval($_GET['id'] ?? 0);
        if ($orderId <= 0) {
            $this->redirect('/integration/stock/orders');
            return;
        }

        $orderModel = new \Order();
        $commande   = $orderModel->getOrderWithLines($orderId);

        if (!$commande) {
            $this->redirect('/integration/stock/orders');
            return;
        }

        // QR code SVG pour chaque produit (BaconQrCode)
        $qrCodes  = [];
        $renderer = new ImageRenderer(new RendererStyle(90), new SvgImageBackEnd());
        $writer   = new Writer($renderer);

        foreach ($commande['lignes'] as $ligne) {
            $content = 'MEDIFLOW | Produit: ' . $ligne['nom']
                     . ' | Ref: #' . $ligne['produit_id']
                     . ' | Cat: ' . $ligne['categorie']
                     . ' | Prix: ' . number_format($ligne['prix'], 2) . ' DT'
                     . ' | Qte: ' . $ligne['quantite_demande'];
            try {
                $svg = $writer->writeString($content);
                $qrCodes[$ligne['produit_id']] = 'data:image/svg+xml;base64,' . base64_encode($svg);
            } catch (\Throwable $e) {
                $qrCodes[$ligne['produit_id']] = '';
            }
        }

        // Générer HTML
        ob_start();
        include __DIR__ . '/../Views/Back/invoice_pdf.php';
        $html = ob_get_clean();

        // Générer PDF
        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('facture_' . $orderId . '.pdf', ['Attachment' => true]);
        exit;
    }

    private function redirect(string $url): void { header('Location: ' . $url); exit; }
}
