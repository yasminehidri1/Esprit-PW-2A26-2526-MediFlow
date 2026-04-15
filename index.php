<?php
/**
 * MediFlow — Front Controller & Router
 * Module: Gestion du Dossier Médical
 *
 * URL pattern:  index.php?page=X&action=Y&...
 *
 * All requests pass through this file.
 * It boots the app, starts the session, and dispatches to the right controller.
 */

declare(strict_types=1);

// ── Autoload & Session helper ─────────────────────────────────
require_once __DIR__ . '/config/database.php';

/**
 * Start session only if not already started (avoids header-already-sent errors).
 */
function session_start_if_not_started(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

session_start_if_not_started();

// ── Error display (development only — disable in production) ──
ini_set('display_errors', '1');
error_reporting(E_ALL);

// ── Routing ───────────────────────────────────────────────────
$page   = $_GET['page']   ?? 'patients';
$action = $_GET['action'] ?? 'view';

match (true) {

    // ── Admin Dashboard ───────────────────────────────────────
    $page === 'admin' && ($action === 'dashboard' || $action === '') => (function () {
        require_once __DIR__ . '/controllers/AdminController.php';
        (new AdminController())->dashboard();
    })(),

    $page === 'admin' && $action === 'doctors' => (function () {
        require_once __DIR__ . '/controllers/AdminController.php';
        (new AdminController())->doctorsList();
    })(),

    $page === 'admin' && $action === 'edit_doctor' => (function () {
        require_once __DIR__ . '/controllers/AdminController.php';
        (new AdminController())->editDoctor();
    })(),

    $page === 'admin' && $action === 'delete_doctor' => (function () {
        require_once __DIR__ . '/controllers/AdminController.php';
        (new AdminController())->deleteDoctor();
    })(),

    $page === 'admin' && $action === 'consultations' => (function () {
        require_once __DIR__ . '/controllers/AdminController.php';
        (new AdminController())->consultationsList();
    })(),

    $page === 'admin' && $action === 'view_consultation' => (function () {
        require_once __DIR__ . '/controllers/AdminController.php';
        (new AdminController())->viewConsultation();
    })(),

    $page === 'admin' && $action === 'delete_consultation' => (function () {
        require_once __DIR__ . '/controllers/AdminController.php';
        (new AdminController())->deleteConsultation();
    })(),

    $page === 'admin' && $action === 'prescriptions' => (function () {
        require_once __DIR__ . '/controllers/AdminController.php';
        (new AdminController())->prescriptionsList();
    })(),

    $page === 'admin' && $action === 'view_prescription' => (function () {
        require_once __DIR__ . '/controllers/AdminController.php';
        (new AdminController())->viewPrescription();
    })(),

    $page === 'admin' && $action === 'update_prescription_status' => (function () {
        require_once __DIR__ . '/controllers/AdminController.php';
        (new AdminController())->updatePrescriptionStatus();
    })(),

    $page === 'admin' && $action === 'delete_prescription' => (function () {
        require_once __DIR__ . '/controllers/AdminController.php';
        (new AdminController())->deletePrescription();
    })(),

    // ── Patient list ──────────────────────────────────────────
    $page === 'patients' => (function () {
        require_once __DIR__ . '/controllers/DossierController.php';
        (new DossierController())->listPatients();
    })(),

    // ── Dossier médical ───────────────────────────────────────
    $page === 'dossier' && ($action === 'view' || $action === '') => (function () {
        require_once __DIR__ . '/controllers/DossierController.php';
        (new DossierController())->viewDossier();
    })(),

    $page === 'dossier' && $action === 'add' => (function () {
        require_once __DIR__ . '/controllers/DossierController.php';
        (new DossierController())->addConsultation();
    })(),

    $page === 'dossier' && $action === 'edit' => (function () {
        require_once __DIR__ . '/controllers/DossierController.php';
        (new DossierController())->editConsultation();
    })(),

    $page === 'dossier' && $action === 'delete' => (function () {
        require_once __DIR__ . '/controllers/DossierController.php';
        (new DossierController())->deleteConsultation();
    })(),

    // ── Ordonnance ────────────────────────────────────────────
    $page === 'ordonnance' && ($action === 'view' || $action === '') => (function () {
        require_once __DIR__ . '/controllers/OrdonnanceController.php';
        (new OrdonnanceController())->view();
    })(),

    $page === 'ordonnance' && $action === 'add' => (function () {
        require_once __DIR__ . '/controllers/OrdonnanceController.php';
        (new OrdonnanceController())->add();
    })(),

    $page === 'ordonnance' && $action === 'edit' => (function () {
        require_once __DIR__ . '/controllers/OrdonnanceController.php';
        (new OrdonnanceController())->edit();
    })(),

    $page === 'ordonnance' && $action === 'delete' => (function () {
        require_once __DIR__ . '/controllers/OrdonnanceController.php';
        (new OrdonnanceController())->delete();
    })(),

    // ── Ordonnances list (all, grouped by patient) ────────────
    $page === 'ordonnances_list' => (function () {
        require_once __DIR__ . '/controllers/OrdonnanceController.php';
        (new OrdonnanceController())->listAll();
    })(),

    // ── Nouvelle consultation (patient selector + form) ───────
    $page === 'nouvelle_consultation' => (function () {
        require_once __DIR__ . '/controllers/DossierController.php';
        (new DossierController())->nouvelleConsultation();
    })(),

    // ── Patient Portal ───────────────────────────────────────
    $page === 'patient' && ($action === '' || $action === 'dashboard' || $action === 'view') => (function () {
        require_once __DIR__ . '/controllers/PatientController.php';
        (new PatientController())->dashboard();
    })(),

    $page === 'patient' && $action === 'update-profile' => (function () {
        require_once __DIR__ . '/controllers/PatientController.php';
        (new PatientController())->updateProfile();
    })(),

    $page === 'patient' && $action === 'request-prescription' => (function () {
        require_once __DIR__ . '/controllers/PatientController.php';
        (new PatientController())->requestPrescription();
    })(),

    $page === 'patient' && $action === 'contact-team' => (function () {
        require_once __DIR__ . '/controllers/PatientController.php';
        (new PatientController())->contactTeam();
    })(),

    $page === 'patient' && $action === 'export-pdf' => (function () {
        require_once __DIR__ . '/controllers/PatientController.php';
        (new PatientController())->exportPDF();
    })(),

    // ── Logout ────────────────────────────────────────────────
    $page === 'logout' => (function () {
        session_start_if_not_started();
        session_destroy();
        header('Location: index.php?page=patients');
        exit;
    })(),

    // ── Default / 404 ─────────────────────────────────────────
    default => (function () {
        http_response_code(404);
        echo '<!DOCTYPE html><html lang="fr"><head>
            <meta charset="utf-8"/>
            <title>404 — Page introuvable</title>
            <script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body class="min-h-screen bg-slate-50 flex items-center justify-center flex-col gap-4">
            <span style="font-size:64px">🔍</span>
            <h1 class="text-2xl font-bold text-slate-800">Page introuvable</h1>
            <p class="text-slate-500">La page demandée n\'existe pas dans ce module.</p>
            <a href="index.php?page=patients"
               class="mt-4 px-6 py-2.5 bg-blue-700 text-white rounded-xl font-semibold hover:bg-blue-800 transition-colors">
               Retour à la liste des patients
            </a>
        </body></html>';
    })(),

};
