<?php
/**
 * View: planning.php — Design MediFlow Pro
 * Pure view file — toutes les variables sont injectées par le Controller.
 * Ne pas ajouter de require_once ni d'instanciation ici.
 *
 * Variables attendues depuis le Controller :
 *   $vue_active, $date_debut, $date_fin, $debut_str, $fin_str
 *   $par_jour, $stats, $jours, $noms_jours, $semaine_param
 *   $url_prec, $url_suiv
 *   $mini_cal_date, $mini_cal_mois_prec, $mini_cal_mois_suiv
 *   $mini_cal_first_dow, $mini_cal_days_in_month, $mini_cal_mois_param
 *   $msg_succes, $msg_erreur
 */

// ── Helpers locaux (positionnement événements) ────────────────
if (!function_exists('calculTop')) {
    function calculTop(string $heure): int {
        list($h, $m) = explode(':', $heure);
        return (int)round(($h + $m/60 - 8) * 76);
    }
}
if (!function_exists('calculHauteur')) {
    function calculHauteur(string $debut, string $fin): int {
        list($hd, $md) = explode(':', $debut);
        list($hf, $mf) = explode(':', $fin);
        return max(38, (int)round((($hf + $mf/60) - ($hd + $md/60)) * 76));
    }
}

// ── Valeurs de sécurité (si le controller ne les passe pas encore) ──
$noms_jours       = $noms_jours ?? ['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'];
$base_url         = '/integration/rdv/planning';
$mini_mois_labels = ['','Janvier','Février','Mars','Avril','Mai','Juin',
                     'Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
$mini_sem_param   = $semaine_param ?? date('Y-\WW');
$msg_succes       = $msg_succes ?? (isset($_GET['succes']) ? 'Opération effectuée avec succès.' : '');
$msg_erreur       = $msg_erreur ?? (isset($_GET['erreur']) ? 'Une erreur est survenue.' : '');

// ── Prochain patient du jour ──────────────────────────────────
$prochain_patient = null;
$today_str        = date('Y-m-d');
$heure_now        = date('H:i');
if (isset($par_jour[$today_str])) {
    foreach ($par_jour[$today_str] as $ev) {
        if ($ev['source'] === 'rdv' && $ev['debut'] >= $heure_now) {
            $prochain_patient = $ev; break;
        }
    }
}

// ── Efficacité semaine ────────────────────────────────────────
$total_ev   = max(1, $stats['total'] ?? 1);
$confirmes  = max(0, $total_ev - ($stats['nb_attente'] ?? 0));
$efficacite = round($confirmes / $total_ev * 100);
?>
<style>
/* ════════════════════════════════════════════════════════════
   MediFlow Pro — planning view (intégration)
════════════════════════════════════════════════════════════ */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --pr:      #004d99;
  --prd:     #1565c0;
  --prl:     #d6e3ff;
  --teal:    #005851;
  --bg:      #f0f4f8;
  --sf:      #ffffff;
  --sfl:     #f5f7fa;
  --sfh:     #e6e8ea;
  --bd:      #e2e8f0;
  --tx:      #0f172a;
  --tm:      #64748b;
  --er:      #ba1a1a;
  --ok:      #15803d;
  --shadow:  0 2px 16px rgba(0,77,153,0.08);
  --r-sm:8px; --r-md:12px; --r-lg:16px; --r-xl:20px; --r-full:9999px;
}

/* ── Layout ── */
.planning-wrap {
  display: grid;
  grid-template-columns: 260px 1fr;
  gap: 20px;
  padding: 24px 28px;
  background: var(--bg);
  font-family: 'Inter', sans-serif;
  color: var(--tx);
}

/* ── En-tête page ── */
.page-header {
  grid-column: 1 / -1;
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 4px;
}
.page-title       { font-family:'Manrope',sans-serif; font-size:26px; font-weight:800; color:var(--tx); }
.page-subtitle    { font-size:13px; color:var(--tm); margin-top:2px; }
.header-controls  { display:flex; align-items:center; gap:10px; }

/* ── Boutons génériques ── */
.icon-btn {
  width:36px; height:36px; border:1px solid var(--bd);
  background:var(--sf); border-radius:var(--r-md);
  display:flex; align-items:center; justify-content:center;
  color:var(--tm); cursor:pointer; transition:all 0.15s; text-decoration:none;
}
.icon-btn:hover { background:var(--sfl); color:var(--pr); border-color:var(--pr); }
.icon-btn svg   { width:18px; height:18px; }

.view-toggle { display:flex; background:var(--sfh); border-radius:var(--r-md); padding:4px; gap:2px; }
.view-btn {
  padding:6px 16px; border:none; border-radius:8px;
  font-family:'Manrope',sans-serif; font-weight:600; font-size:13px;
  cursor:pointer; color:var(--tm); background:transparent;
  transition:all 0.15s; text-decoration:none; display:inline-flex; align-items:center;
}
.view-btn.active { background:white; color:var(--pr); box-shadow:0 2px 8px rgba(0,77,153,0.10); }
.view-btn:hover:not(.active) { color:var(--pr); }

.btn-add-event {
  display:inline-flex; align-items:center; gap:7px; padding:8px 16px;
  background:linear-gradient(135deg,var(--pr),var(--prd));
  color:white; border:none; border-radius:var(--r-md);
  font-family:'Manrope',sans-serif; font-weight:700; font-size:13px;
  cursor:pointer; box-shadow:0 2px 8px rgba(0,77,153,0.25); transition:all 0.15s;
}
.btn-add-event:hover { box-shadow:0 4px 16px rgba(0,77,153,0.35); transform:translateY(-1px); }
.btn-add-event svg   { width:16px; height:16px; }

/* ── Messages ── */
.msg { grid-column:1/-1; padding:12px 18px; border-radius:var(--r-md); font-size:14px; font-weight:600; margin-bottom:4px; }
.msg-ok  { background:#dcfce7; color:var(--ok); border:1px solid #bbf7d0; }
.msg-err { background:#fee2e2; color:var(--er); border:1px solid #fecaca; }

/* ── Barre de recherche ── */
.search-bar {
  display:flex; align-items:center; gap:7px;
  background:var(--sf); border-radius:var(--r-full);
  padding:7px 14px; width:200px; border:1px solid var(--bd);
}
.search-bar svg   { width:15px; height:15px; color:var(--tm); flex-shrink:0; }
.search-bar input { border:none; background:transparent; outline:none; font-size:13px; color:var(--tx); width:100%; font-family:'Inter',sans-serif; }
.search-bar input::placeholder { color:#94a3b8; }

/* ════════════════════════════════════════════════════════════
   PANNEAU GAUCHE
════════════════════════════════════════════════════════════ */
.side-panels { display:flex; flex-direction:column; gap:14px; position:sticky; top:24px; align-self:start; }

.side-card {
  background:var(--sf); border-radius:var(--r-xl);
  padding:20px; box-shadow:var(--shadow); border:1px solid var(--bd);
}
.side-card-title {
  font-family:'Manrope',sans-serif; font-size:13px; font-weight:700;
  color:var(--tx); margin-bottom:14px;
  display:flex; align-items:center; justify-content:space-between;
}

/* Stats */
.day-stats  { display:flex; flex-direction:column; gap:10px; }
.stat-row   { display:flex; justify-content:space-between; align-items:center; font-size:13px; }
.stat-label { color:var(--tm); }
.stat-val   { font-weight:700; font-size:15px; }
.v-pr  { color:var(--pr); }
.v-ok  { color:var(--ok); }
.v-er  { color:var(--er); }
.v-tel { color:var(--teal); }

/* Prochain patient */
.next-patient {
  display:flex; align-items:center; gap:10px; padding:12px;
  background:linear-gradient(135deg,var(--prl),#e0eaff);
  border-radius:var(--r-md); margin-top:14px; border:1px solid rgba(0,77,153,0.12);
}
.next-avatar {
  width:36px; height:36px; border-radius:var(--r-full); background:var(--pr);
  display:flex; align-items:center; justify-content:center; flex-shrink:0;
  font-family:'Manrope',sans-serif; font-weight:800; font-size:13px; color:white;
}
.next-name  { font-weight:700; font-size:13px; color:var(--tx); }
.next-time  { font-size:11px; font-weight:700; color:var(--pr); margin-top:1px; }
.next-none  { font-size:13px; color:var(--tm); text-align:center; padding:14px 0; font-style:italic; }

/* Mini calendrier */
.mini-cal-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
.mini-cal-title  { font-size:13px; font-weight:700; color:var(--tx); }
.mini-cal-nav    { display:flex; gap:2px; }
.mini-cal-nav a  {
  width:24px; height:24px; background:transparent; cursor:pointer;
  color:#94a3b8; border-radius:4px; display:flex; align-items:center;
  justify-content:center; transition:background 0.12s; text-decoration:none;
}
.mini-cal-nav a:hover { background:var(--sfl); color:var(--pr); }
.mini-cal-nav a svg   { width:14px; height:14px; }

.mini-cal-grid { display:grid; grid-template-columns:repeat(7,1fr); gap:2px; text-align:center; }
.mini-day-name { font-size:9px; font-weight:700; color:#94a3b8; padding:3px 0; }
.mini-day {
  font-size:11px; font-weight:600; padding:5px 2px; border-radius:5px;
  cursor:pointer; transition:background 0.12s; color:var(--tx);
  text-decoration:none; display:block;
}
.mini-day:hover      { background:var(--sfl); }
.mini-day.other      { color:#cbd5e1; pointer-events:none; }
.mini-day.today-mark { background:var(--prl); color:var(--pr); font-weight:800; }
.mini-day.active     { background:var(--pr); color:white; font-weight:800; box-shadow:0 2px 8px rgba(0,77,153,0.25); }

/* Actions rapides */
.quick-actions { display:flex; flex-direction:column; gap:8px; }
.quick-btn {
  display:flex; align-items:center; gap:10px; padding:10px 12px;
  background:var(--sfl); border-radius:var(--r-md); border:1px solid var(--bd);
  cursor:pointer; font-size:13px; font-weight:600; color:var(--tx);
  text-decoration:none; transition:all 0.15s; font-family:'Inter',sans-serif;
  width:100%;
}
.quick-btn:hover { border-color:var(--pr); color:var(--pr); background:var(--prl); }
.quick-btn-icon { width:28px; height:28px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.quick-btn-icon svg { width:14px; height:14px; }
.qb-blue   { background:var(--prl); color:var(--pr); }
.qb-teal   { background:#ccfaf5; color:var(--teal); }
.qb-orange { background:#ffedd5; color:#ea580c; }
.qb-green  { background:#dcfce7; color:var(--ok); }

/* Efficacité */
.efficiency-card {
  background:linear-gradient(135deg,#1e293b,#0f172a);
  border-radius:var(--r-xl); padding:20px; color:white;
  position:relative; overflow:hidden;
}
.eff-label    { font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:0.15em; color:#2dd4bf; margin-bottom:5px; }
.eff-value    { font-family:'Manrope',sans-serif; font-size:34px; font-weight:800; margin-bottom:10px; }
.eff-bar-bg   { height:5px; background:rgba(255,255,255,0.10); border-radius:3px; overflow:hidden; }
.eff-bar-fill { height:100%; background:#2dd4bf; border-radius:3px; }
.eff-note     { font-size:10px; opacity:0.5; margin-top:8px; }
.eff-bg-icon  { position:absolute; bottom:-10px; right:-10px; opacity:0.06; }
.eff-bg-icon svg { width:80px; height:80px; fill:white; transform:rotate(12deg); }

/* ════════════════════════════════════════════════════════════
   CALENDRIER PRINCIPAL
════════════════════════════════════════════════════════════ */
.calendar-card {
  background:var(--sf); border-radius:var(--r-xl);
  box-shadow:var(--shadow); overflow:hidden; border:1px solid var(--bd);
}

/* En-têtes jours */
.cal-days-header {
  display:grid;
  background:var(--sfl);
  border-bottom:1px solid rgba(194,198,212,0.3);
}
.cal-day-hdr-spacer { padding:12px 8px; }
.cal-day-col        { padding:12px 8px; text-align:center; border-left:1px solid rgba(194,198,212,0.2); }
.cal-day-name       { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:#94a3b8; }
.cal-day-num        { font-family:'Manrope',sans-serif; font-size:18px; font-weight:800; color:var(--tx); margin-top:2px; }
.cal-day-col.today  { background:rgba(0,77,153,0.04); }
.cal-day-col.today .cal-day-name { color:var(--pr); }
.cal-day-col.today .cal-day-num  { color:var(--pr); }

/* Corps scrollable */
.calendar-scroll { height:580px; overflow-y:auto; }
.calendar-grid   { display:grid; }

/* Colonne heures */
.time-col  { display:flex; flex-direction:column; }
.time-slot {
  height:76px; border-bottom:1px solid rgba(194,198,212,0.12);
  font-size:10px; font-weight:700; color:#94a3b8;
  padding:5px 8px 0 0; text-align:right; flex-shrink:0;
}

/* Colonne jour */
.day-col        { border-left:1px solid rgba(194,198,212,0.12); position:relative; }
.day-col.today  { background:rgba(0,77,153,0.02); }
.day-hour-lines { position:absolute; inset:0; display:flex; flex-direction:column; pointer-events:none; }
.day-hour-line  { height:76px; border-bottom:1px solid rgba(194,198,212,0.10); flex-shrink:0; }

/* Ligne maintenant */
.now-line { position:absolute; left:0; right:0; height:2px; background:var(--er); z-index:10; }
.now-line::before { content:''; width:8px; height:8px; background:var(--er); border-radius:var(--r-full); position:absolute; left:-4px; top:-3px; }

/* Événements */
.cal-event {
  position:absolute; left:4px; right:4px; border-radius:8px;
  padding:7px 9px; cursor:pointer; transition:all 0.15s; overflow:hidden;
}
.cal-event:hover { filter:brightness(0.95); box-shadow:0 4px 12px rgba(0,0,0,0.12); }
.cal-event-time { font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; opacity:0.75; }
.cal-event-name { font-size:12px; font-weight:700; margin-top:2px; }
.cal-event-note { font-size:10px; opacity:0.7; font-style:italic; margin-top:2px; }

.ev-blue   { background:#eff4ff; border-left:4px solid var(--pr);  color:var(--pr); }
.ev-teal   { background:#f0fdfb; border-left:4px solid var(--teal); color:var(--teal); }
.ev-solid  { background:var(--pr); color:white; box-shadow:0 4px 16px rgba(0,77,153,0.30); }
.ev-grey   { background:var(--sfl); border-left:4px solid #cbd5e1; color:#64748b; }
.ev-urgent { background:#fff5f5; border-left:4px solid var(--er);  color:var(--er); }
.ev-purple { background:#f5f3ff; border-left:4px solid #7c3aed;   color:#7c3aed; }
.ev-orange { background:#fff7ed; border-left:4px solid #ea580c;   color:#ea580c; }
.ev-green  { background:#f0fdf4; border-left:4px solid #16a34a;   color:#16a34a; }
.ev-pink   { background:#fdf2f8; border-left:4px solid #db2777;   color:#db2777; }

.badge-urgent { display:inline-block; margin-top:3px; padding:1px 6px; background:rgba(186,26,26,0.12); color:var(--er); border-radius:4px; font-size:8px; font-weight:800; text-transform:uppercase; }

.event-actions { display:flex; gap:4px; margin-top:5px; }
.ev-btn {
  display:inline-flex; align-items:center; gap:3px; padding:2px 7px;
  border:none; border-radius:4px; font-size:9px; font-weight:700;
  cursor:pointer; text-transform:uppercase; transition:opacity 0.15s; text-decoration:none;
}
.ev-btn svg       { width:10px; height:10px; }
.ev-btn-edit      { background:rgba(0,77,153,0.12); color:var(--pr); }
.ev-btn-del       { background:rgba(186,26,26,0.12); color:var(--er); }
.ev-btn:hover     { opacity:0.8; }

/* ── VUE MOIS ── */
.month-grid-header { display:grid; grid-template-columns:repeat(7,1fr); border-bottom:1px solid var(--bd); }
.month-col-name    { padding:10px 6px; text-align:center; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--tm); }
.month-grid        { display:grid; grid-template-columns:repeat(7,1fr); }
.month-cell        { min-height:100px; border-right:1px solid var(--bd); border-bottom:1px solid var(--bd); padding:6px; }
.month-cell.other  { background:var(--sfl); }
.month-cell.today  { background:#eff6ff; }
.month-cell-num    { font-size:12px; font-weight:700; color:var(--tm); margin-bottom:4px; width:22px; height:22px; display:flex; align-items:center; justify-content:center; border-radius:50%; }
.month-cell-num.today-num { background:var(--pr); color:white; }
.month-event { font-size:10px; font-weight:600; padding:2px 5px; border-radius:4px; margin-bottom:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.dot-blue    { background:var(--prl); color:var(--pr); }
.dot-grey    { background:#f1f5f9; color:#64748b; }
.dot-event   { background:rgba(0,88,81,0.12); color:var(--teal); }
.month-more  { font-size:10px; color:var(--tm); font-weight:600; margin-top:2px; }

/* ── MODALES ── */
.modal-overlay      { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:1000; align-items:center; justify-content:center; }
.modal-overlay.open { display:flex; }
.modal {
  background:var(--sf); border-radius:var(--r-xl); padding:28px;
  width:520px; max-width:96vw; box-shadow:0 20px 60px rgba(0,0,0,0.2);
  max-height:90vh; overflow-y:auto;
}
.modal-title { font-family:'Manrope',sans-serif; font-size:17px; font-weight:800; margin-bottom:6px; color:var(--tx); }
.modal-sub   { font-size:13px; color:var(--tm); margin-bottom:22px; }
.modal-group { display:flex; flex-direction:column; gap:5px; margin-bottom:14px; }
.modal-row   { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.modal-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--tm); display:flex; justify-content:space-between; }
.char-count  { font-weight:500; text-transform:none; letter-spacing:0; color:#94a3b8; }
.modal-input {
  width:100%; background:var(--sfl); border:2px solid transparent;
  border-radius:var(--r-md); padding:10px 14px;
  font-size:14px; font-family:'Inter',sans-serif; color:var(--tx);
  outline:none; transition:all 0.18s;
}
.modal-input:focus        { border-color:var(--teal); background:white; }
.modal-input.input-error  { border-color:var(--er) !important; background:#fff5f5; }
.modal-input.input-ok     { border-color:#16a34a; }
.field-error              { font-size:11px; color:var(--er); font-weight:600; margin-top:4px; display:none; }
.field-error.show         { display:block; }
.modal-actions            { display:flex; justify-content:flex-end; gap:10px; margin-top:20px; }
.btn-modal-cancel {
  padding:10px 20px; background:transparent; border:1.5px solid var(--bd);
  color:var(--tm); font-family:'Manrope',sans-serif; font-weight:600; font-size:13px;
  border-radius:var(--r-md); cursor:pointer;
}
.btn-modal-save {
  display:flex; align-items:center; gap:7px; padding:10px 20px;
  background:linear-gradient(135deg,var(--pr),var(--prd));
  color:white; border:none; border-radius:var(--r-md);
  font-family:'Manrope',sans-serif; font-weight:700; font-size:13px; cursor:pointer;
}
.btn-modal-save svg { width:14px; height:14px; }

/* Pills type */
.type-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:6px; margin-top:4px; }
.type-pill {
  padding:8px 4px; border:2px solid var(--bd); border-radius:var(--r-md);
  text-align:center; cursor:pointer; font-size:10px; font-weight:700;
  transition:all 0.15s; user-select:none;
}
.type-pill:hover    { border-color:var(--pr); }
.type-pill.selected { border-color:var(--pr); background:var(--prl); color:var(--pr); }
.type-pill .tp-icon { font-size:16px; display:block; margin-bottom:3px; }

/* Toast */
.toast {
  position:fixed; bottom:24px; right:24px; background:#0f172a; color:white;
  padding:14px 20px; border-radius:var(--r-md); font-size:13px; font-weight:600;
  z-index:9999; transform:translateY(80px); opacity:0; transition:all 0.3s;
  display:flex; align-items:center; gap:10px;
}
.toast.show      { transform:translateY(0); opacity:1; }
.toast-icon      { width:20px; height:20px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.toast-icon.ok   { background:#16a34a; }
.toast-icon.err  { background:var(--er); }
</style>

<div class="planning-wrap">

  <?php if ($msg_succes): ?>
  <div class="msg msg-ok">✓ <?= htmlspecialchars($msg_succes) ?></div>
  <?php endif; ?>
  <?php if ($msg_erreur): ?>
  <div class="msg msg-err">✗ <?= htmlspecialchars($msg_erreur) ?></div>
  <?php endif; ?>

  <!-- ── En-tête ── -->
  <div class="page-header">
    <div>
      <?php
        if ($vue_active === 'jour') {
          $nl = ['','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche'];
          echo '<div class="page-title">'.$nl[(int)$date_debut->format('N')].' '.$date_debut->format('j').' '.$mini_mois_labels[(int)$date_debut->format('n')].' '.$date_debut->format('Y').'</div>';
          echo '<div class="page-subtitle">Vue journalière</div>';
        } elseif ($vue_active === 'mois') {
          echo '<div class="page-title">'.$mini_mois_labels[(int)$date_debut->format('n')].' '.$date_debut->format('Y').'</div>';
          echo '<div class="page-subtitle">Vue mensuelle</div>';
        } else {
          echo '<div class="page-title">Semaine '.$date_debut->format('W').'</div>';
          echo '<div class="page-subtitle">'.$date_debut->format('d').' '.$mini_mois_labels[(int)$date_debut->format('n')].' — '.$date_fin->format('d').' '.$mini_mois_labels[(int)$date_fin->format('n')].' '.$date_fin->format('Y').'</div>';
        }
      ?>
    </div>
    <div class="header-controls">
      <div class="search-bar">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Rechercher..." oninput="rechercherEvenement(this.value)">
      </div>
      <div style="display:flex;gap:4px;">
        <a href="<?= $url_prec ?>" class="icon-btn" title="Précédent">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        </a>
        <a href="<?= $url_suiv ?>" class="icon-btn" title="Suivant">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
      </div>
      <div class="view-toggle">
        <a href="<?= $base_url ?>?vue=jour&date=<?= date('Y-m-d') ?>"                          class="view-btn <?= $vue_active==='jour'    ?'active':'' ?>">Jour</a>
        <a href="<?= $base_url ?>?vue=semaine&semaine=<?= $mini_sem_param ?>"                  class="view-btn <?= $vue_active==='semaine' ?'active':'' ?>">Semaine</a>
        <a href="<?= $base_url ?>?vue=mois&date=<?= $date_debut->format('Y-m-01') ?>"         class="view-btn <?= $vue_active==='mois'    ?'active':'' ?>">Mois</a>
      </div>
      <button class="btn-add-event" onclick="ouvrirModalAjout()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Ajouter
      </button>
    </div>
  </div>

  <!-- ════ PANNEAU GAUCHE ════ -->
  <div class="side-panels">

    <!-- Aperçu journée -->
    <div class="side-card">
      <div class="side-card-title">
        <span>📊 Aperçu Journée</span>
        <span style="font-size:11px;font-weight:500;color:var(--tm);"><?= date('d/m/Y') ?></span>
      </div>
      <div class="day-stats">
        <div class="stat-row"><span class="stat-label">Total RDV semaine</span><span class="stat-val v-pr"><?= $stats['total'] ?></span></div>
        <div class="stat-row"><span class="stat-label">Aujourd'hui</span>     <span class="stat-val v-tel"><?= $stats['nb_aujourdhui'] ?></span></div>
        <div class="stat-row"><span class="stat-label">En attente</span>      <span class="stat-val v-er"><?= $stats['nb_attente'] ?></span></div>
        <div class="stat-row"><span class="stat-label">Confirmés</span>       <span class="stat-val v-ok"><?= ($stats['total'] - $stats['nb_attente']) ?></span></div>
      </div>
      <div style="margin-top:14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:var(--tm);margin-bottom:6px;">Prochain patient</div>
      <?php if ($prochain_patient):
        $words = explode(' ', $prochain_patient['titre']);
        $init  = strtoupper(substr($words[0],0,1).(isset($words[1])?substr($words[1],0,1):''));
      ?>
      <div class="next-patient">
        <div class="next-avatar"><?= $init ?></div>
        <div>
          <div class="next-name"><?= htmlspecialchars($prochain_patient['titre']) ?></div>
          <div class="next-time">🕐 <?= substr($prochain_patient['debut'],0,5) ?></div>
        </div>
      </div>
      <?php else: ?>
      <div class="next-none">Aucun patient à venir aujourd'hui</div>
      <?php endif; ?>
    </div>

    <!-- Mini calendrier -->
    <div class="side-card">
      <div class="mini-cal-header">
        <span class="mini-cal-title"><?= $mini_mois_labels[(int)$mini_cal_date->format('n')] ?> <?= $mini_cal_date->format('Y') ?></span>
        <div class="mini-cal-nav">
          <a href="<?= $base_url ?>?semaine=<?= $mini_sem_param ?>&mini_mois=<?= $mini_cal_mois_prec->format('Y-m') ?>" title="Mois précédent">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
          </a>
          <a href="<?= $base_url ?>?semaine=<?= $mini_sem_param ?>&mini_mois=<?= $mini_cal_mois_suiv->format('Y-m') ?>" title="Mois suivant">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
          </a>
        </div>
      </div>
      <div class="mini-cal-grid">
        <?php foreach(['L','M','M','J','V','S','D'] as $dn): ?>
          <div class="mini-day-name"><?= $dn ?></div>
        <?php endforeach; ?>
        <?php
          for ($b = 1; $b < $mini_cal_first_dow; $b++) echo '<div class="mini-day other">·</div>';
          for ($d = 1; $d <= $mini_cal_days_in_month; $d++) {
            $dc   = $mini_cal_date->format('Y-m-').sprintf('%02d',$d);
            $cls  = 'mini-day';
            if ($dc === date('Y-m-d'))              $cls .= ' today-mark';
            if ($dc >= $debut_str && $dc <= $fin_str) $cls .= ' active';
            $wl   = (new DateTime($dc))->format('Y-\WW');
            echo "<a href='$base_url?vue=semaine&semaine={$wl}&mini_mois={$mini_cal_mois_param}' class='{$cls}'>{$d}</a>";
          }
          $dl = (int)(new DateTime($mini_cal_date->format('Y-m-').$mini_cal_days_in_month))->format('N');
          for ($b = $dl+1; $b <= 7; $b++) echo '<div class="mini-day other">·</div>';
        ?>
      </div>
    </div>

    <!-- Actions rapides -->
    <div class="side-card">
      <div class="side-card-title">⚡ Actions Rapides</div>
      <div class="quick-actions">
        <button class="quick-btn" onclick="ouvrirModalAjout()">
          <span class="quick-btn-icon qb-blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span>
          Nouvel événement
        </button>
        <a href="/integration/rdv/dashboard" class="quick-btn">
          <span class="quick-btn-icon qb-teal"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></span>
          Voir les RDV
        </a>
        <a href="<?= $base_url ?>?vue=semaine&semaine=<?= date('Y-\WW') ?>" class="quick-btn">
          <span class="quick-btn-icon qb-orange"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
          Semaine actuelle
        </a>
        <a href="/integration/rdv/statistiques" class="quick-btn">
          <span class="quick-btn-icon qb-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span>
          Statistiques
        </a>
      </div>
    </div>

    <!-- Efficacité -->
    <div class="efficiency-card">
      <div class="eff-label">Efficacité Semaine</div>
      <div class="eff-value"><?= $efficacite ?>%</div>
      <div class="eff-bar-bg"><div class="eff-bar-fill" style="width:<?= $efficacite ?>%;"></div></div>
      <div class="eff-note">Taux de confirmation des RDV cette semaine.</div>
      <div class="eff-bg-icon"><svg viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/></svg></div>
    </div>

  </div><!-- /side-panels -->

  <!-- ════ CALENDRIER ════ -->
  <div class="calendar-card">

    <?php if ($vue_active === 'mois'): ?>
    <!-- ═══ VUE MOIS ═══ -->
    <?php
      $mois_fdow     = (int)(new DateTime($date_debut->format('Y-m-01')))->format('N');
      $nb_jours_mois = (int)$date_fin->format('d');
    ?>
    <div class="month-grid-header">
      <?php foreach(['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'] as $n): ?>
      <div class="month-col-name"><?= $n ?></div>
      <?php endforeach; ?>
    </div>
    <div class="month-grid">
      <?php
        for ($b = 1; $b < $mois_fdow; $b++) echo '<div class="month-cell other"></div>';
        for ($d = 1; $d <= $nb_jours_mois; $d++):
          $cd  = $date_debut->format('Y-m-').sprintf('%02d',$d);
          $ist = ($cd === date('Y-m-d'));
          $evs = $par_jour[$cd] ?? [];
      ?>
      <div class="month-cell <?= $ist?'today':'' ?>">
        <div class="month-cell-num <?= $ist?'today-num':'' ?>"><?= $d ?></div>
        <?php foreach(array_slice($evs,0,3) as $ev):
          $dc = ($ev['source']==='rdv') ? ($ev['type']==='annule'?'dot-grey':'dot-blue') : 'dot-event';
        ?>
        <div class="month-event <?= $dc ?> cal-ev-item" data-titre="<?= htmlspecialchars($ev['titre']) ?>">
          <?= substr($ev['debut'],0,5) ?> <?= htmlspecialchars(mb_strimwidth($ev['titre'],0,12,'…')) ?>
        </div>
        <?php endforeach; ?>
        <?php if(count($evs)>3): ?><div class="month-more">+<?= count($evs)-3 ?> autres</div><?php endif; ?>
      </div>
      <?php endfor; ?>
      <?php
        $dl = (int)(new DateTime($date_debut->format('Y-m-').$nb_jours_mois))->format('N');
        for ($b=$dl+1;$b<=7;$b++) echo '<div class="month-cell other"></div>';
      ?>
    </div>

    <?php else: ?>
    <!-- ═══ VUE JOUR / SEMAINE ═══ -->
    <?php
      $nb_cols = count($jours);
      $grid_cols = '56px ' . implode(' ', array_fill(0, $nb_cols, '1fr'));
    ?>
    <div class="cal-days-header" style="grid-template-columns:<?= $grid_cols ?>;">
      <div class="cal-day-hdr-spacer"></div>
      <?php foreach($jours as $jour):
        $ea  = $jour->format('Y-m-d') === date('Y-m-d');
        $di  = (int)$jour->format('N')-1;
      ?>
      <div class="cal-day-col <?= $ea?'today':'' ?>">
        <div class="cal-day-name"><?= $noms_jours[$di] ?></div>
        <div class="cal-day-num"><?= $jour->format('j') ?></div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="calendar-scroll">
      <div class="calendar-grid" style="grid-template-columns:<?= $grid_cols ?>;">
        <!-- Heures -->
        <div class="time-col">
          <?php for($h=8;$h<=18;$h++): ?>
          <div class="time-slot"><?= sprintf('%02d:00',$h) ?></div>
          <?php endfor; ?>
        </div>

        <!-- Colonnes jours -->
        <?php foreach($jours as $jour):
          $js  = $jour->format('Y-m-d');
          $ea  = ($js === date('Y-m-d'));
          $evj = $par_jour[$js] ?? [];
        ?>
        <div class="day-col <?= $ea?'today':'' ?>">
          <?php if($ea): ?><div class="now-line" id="nowLine"></div><?php endif; ?>
          <div class="day-hour-lines">
            <?php for($h=0;$h<11;$h++): ?><div class="day-hour-line"></div><?php endfor; ?>
          </div>

          <?php
          $icons = ['rdv'=>'👤','chirurgie'=>'🔪','reunion'=>'🤝','pause'=>'☕','urgence'=>'🚨',
                    'formation'=>'📚','garde'=>'🌙','consultation'=>'💊','visite'=>'🏥',
                    'telemedicine'=>'💻','administratif'=>'📋','autre'=>'📌'];
          foreach($evj as $ev):
            $top  = calculTop($ev['debut']);
            $haut = $ev['fin'] ? calculHauteur($ev['debut'],$ev['fin']) : 60;
            $fin_fmt = $ev['fin'] ? substr($ev['fin'],0,5) : null;
            $icon    = $icons[$ev['type']] ?? '📌';
            if($ev['source']==='rdv'){
              $css = $ev['type']==='annule'?'ev-grey':'ev-blue';
            } else {
              $css = match($ev['type']){
                'chirurgie'    =>'ev-solid',
                'reunion'      =>'ev-teal',
                'pause'        =>'ev-grey',
                'urgence'      =>'ev-urgent',
                'formation'    =>'ev-purple',
                'garde'        =>'ev-orange',
                'consultation' =>'ev-green',
                'visite'       =>'ev-pink',
                'telemedicine' =>'ev-purple',
                'administratif'=>'ev-grey',
                default        =>'ev-blue',
              };
            }
            $dj = htmlspecialchars(json_encode($ev));
          ?>
          <div class="cal-event <?= $css ?> cal-ev-item"
               style="top:<?= $top ?>px;height:<?= $haut ?>px;"
               data-titre="<?= htmlspecialchars($ev['titre']) ?>">
            <div class="cal-event-time"><?= $icon ?> <?= substr($ev['debut'],0,5) ?><?= $fin_fmt?' – '.$fin_fmt:'' ?></div>
            <div class="cal-event-name"><?= htmlspecialchars($ev['titre']) ?></div>
            <?php if($ev['note']): ?><div class="cal-event-note"><?= htmlspecialchars($ev['note']) ?></div><?php endif; ?>
            <?php if($ev['type']==='urgence'): ?><span class="badge-urgent">Urgent</span><?php endif; ?>
            <div class="event-actions">
              <?php if($ev['source']==='planning'): ?>
              <button class="ev-btn ev-btn-edit" onclick="ouvrirModalModif(<?= $dj ?>)">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>Modifier
              </button>
              <a href="<?= $base_url ?>?supprimer_event=<?= $ev['id'] ?>&vue=<?= $vue_active ?>" class="ev-btn ev-btn-del" onclick="return confirm('Supprimer cet événement ?')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg>Supprimer
              </a>
              <?php else: ?>
              <a href="/integration/rdv/modifier?id=<?= $ev['id'] ?>" class="ev-btn ev-btn-edit">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>Voir RDV
              </a>
              <?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

  </div><!-- /calendar-card -->

</div><!-- /planning-wrap -->

<!-- Toast -->
<div class="toast" id="toast">
  <div class="toast-icon" id="toastIcon"></div>
  <span id="toastMsg"></span>
</div>

<!-- ═══════════════ MODALE AJOUTER ═══════════════ -->
<div class="modal-overlay" id="modalAjout">
  <div class="modal">
    <h3 class="modal-title">➕ Ajouter un événement</h3>
    <p class="modal-sub">Planifiez un événement : réunion, chirurgie, garde, formation...</p>
    <form method="POST" action="<?= $base_url ?>" id="formAjout" novalidate>
      <input type="hidden" name="action" value="ajouter_evenement">

      <div class="modal-group">
        <label class="modal-label">Titre <span class="char-count" id="cntATitre">0/10</span></label>
        <input class="modal-input" type="text" name="titre" id="aTitre" placeholder="Ex : Réunion, Pause..." maxlength="10" required>
        <span class="field-error" id="errATitre"></span>
      </div>

      <div class="modal-group">
        <label class="modal-label">Type d'événement</label>
        <input type="hidden" name="type" id="aTypeVal" value="reunion">
        <div class="type-grid">
          <div class="type-pill selected" data-type="reunion"       onclick="selectType(this,'aTypeVal')"><span class="tp-icon">🤝</span>Réunion</div>
          <div class="type-pill"          data-type="chirurgie"     onclick="selectType(this,'aTypeVal')"><span class="tp-icon">🔪</span>Chirurgie</div>
          <div class="type-pill"          data-type="pause"         onclick="selectType(this,'aTypeVal')"><span class="tp-icon">☕</span>Pause</div>
          <div class="type-pill"          data-type="formation"     onclick="selectType(this,'aTypeVal')"><span class="tp-icon">📚</span>Formation</div>
          <div class="type-pill"          data-type="urgence"       onclick="selectType(this,'aTypeVal')"><span class="tp-icon">🚨</span>Urgence</div>
          <div class="type-pill"          data-type="garde"         onclick="selectType(this,'aTypeVal')"><span class="tp-icon">🌙</span>Garde</div>
          <div class="type-pill"          data-type="consultation"  onclick="selectType(this,'aTypeVal')"><span class="tp-icon">💊</span>Consultation</div>
          <div class="type-pill"          data-type="visite"        onclick="selectType(this,'aTypeVal')"><span class="tp-icon">🏥</span>Visite</div>
          <div class="type-pill"          data-type="telemedicine"  onclick="selectType(this,'aTypeVal')"><span class="tp-icon">💻</span>Télémédecine</div>
          <div class="type-pill"          data-type="administratif" onclick="selectType(this,'aTypeVal')"><span class="tp-icon">📋</span>Administratif</div>
          <div class="type-pill"          data-type="autre"         onclick="selectType(this,'aTypeVal')"><span class="tp-icon">📌</span>Autre</div>
        </div>
      </div>

      <div class="modal-row">
        <div class="modal-group">
          <label class="modal-label">Date et heure de début</label>
          <input class="modal-input" type="datetime-local" name="date_debut" id="aDebut" required>
          <span class="field-error" id="errADebut"></span>
        </div>
        <div class="modal-group">
          <label class="modal-label">Date et heure de fin</label>
          <input class="modal-input" type="datetime-local" name="date_fin" id="aFin" required>
          <span class="field-error" id="errAFin"></span>
        </div>
      </div>

      <div class="modal-group">
        <label class="modal-label">Remarque (optionnel) <span class="char-count" id="cntANote">0/20</span></label>
        <input class="modal-input" type="text" name="note" id="aNote" placeholder="Courte remarque..." maxlength="20">
        <span class="field-error" id="errANote"></span>
      </div>

      <div class="modal-actions">
        <button type="button" class="btn-modal-cancel" onclick="fermerModals()">Annuler</button>
        <button type="submit" class="btn-modal-save">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          Ajouter
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ═══════════════ MODALE MODIFIER ═══════════════ -->
<div class="modal-overlay" id="modalModif">
  <div class="modal">
    <h3 class="modal-title">✏️ Modifier l'événement</h3>
    <p class="modal-sub">Modifiez les informations de cet événement.</p>
    <form method="POST" action="<?= $base_url ?>" id="formModif" novalidate>
      <input type="hidden" name="action" value="modifier_evenement">
      <input type="hidden" name="event_id" id="mId">

      <div class="modal-group">
        <label class="modal-label">Titre <span class="char-count" id="cntMTitre">0/10</span></label>
        <input class="modal-input" type="text" name="titre" id="mTitre" maxlength="10" required>
        <span class="field-error" id="errMTitre"></span>
      </div>

      <div class="modal-group">
        <label class="modal-label">Type d'événement</label>
        <input type="hidden" name="type" id="mTypeVal" value="reunion">
        <div class="type-grid" id="mTypeGrid">
          <div class="type-pill" data-type="reunion"       onclick="selectType(this,'mTypeVal')"><span class="tp-icon">🤝</span>Réunion</div>
          <div class="type-pill" data-type="chirurgie"     onclick="selectType(this,'mTypeVal')"><span class="tp-icon">🔪</span>Chirurgie</div>
          <div class="type-pill" data-type="pause"         onclick="selectType(this,'mTypeVal')"><span class="tp-icon">☕</span>Pause</div>
          <div class="type-pill" data-type="formation"     onclick="selectType(this,'mTypeVal')"><span class="tp-icon">📚</span>Formation</div>
          <div class="type-pill" data-type="urgence"       onclick="selectType(this,'mTypeVal')"><span class="tp-icon">🚨</span>Urgence</div>
          <div class="type-pill" data-type="garde"         onclick="selectType(this,'mTypeVal')"><span class="tp-icon">🌙</span>Garde</div>
          <div class="type-pill" data-type="consultation"  onclick="selectType(this,'mTypeVal')"><span class="tp-icon">💊</span>Consultation</div>
          <div class="type-pill" data-type="visite"        onclick="selectType(this,'mTypeVal')"><span class="tp-icon">🏥</span>Visite</div>
          <div class="type-pill" data-type="telemedicine"  onclick="selectType(this,'mTypeVal')"><span class="tp-icon">💻</span>Télémédecine</div>
          <div class="type-pill" data-type="administratif" onclick="selectType(this,'mTypeVal')"><span class="tp-icon">📋</span>Administratif</div>
          <div class="type-pill" data-type="autre"         onclick="selectType(this,'mTypeVal')"><span class="tp-icon">📌</span>Autre</div>
        </div>
      </div>

      <div class="modal-row">
        <div class="modal-group">
          <label class="modal-label">Début</label>
          <input class="modal-input" type="datetime-local" name="date_debut" id="mDebut" required>
          <span class="field-error" id="errMDebut"></span>
        </div>
        <div class="modal-group">
          <label class="modal-label">Fin</label>
          <input class="modal-input" type="datetime-local" name="date_fin" id="mFin" required>
          <span class="field-error" id="errMFin"></span>
        </div>
      </div>

      <div class="modal-group">
        <label class="modal-label">Remarque (optionnel) <span class="char-count" id="cntMNote">0/20</span></label>
        <input class="modal-input" type="text" name="note" id="mNote" maxlength="20">
        <span class="field-error" id="errMNote"></span>
      </div>

      <div class="modal-actions">
        <button type="button" class="btn-modal-cancel" onclick="fermerModals()">Annuler</button>
        <button type="submit" class="btn-modal-save">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          Enregistrer
        </button>
      </div>
    </form>
  </div>
</div>

<script>
/* ══════════════════════════════════════════════
   VALIDATION
══════════════════════════════════════════════ */
const TITRE_MAX   = 10, NOTE_MAX = 20;
const TITRE_REGEX = /^[a-zA-ZÀ-ÿ0-9\s\-']+$/;

const $  = id => document.getElementById(id);
const se = (id,msg) => { const e=$(id); if(e){ e.textContent=msg; e.classList.add('show'); } };
const ce = id       => { const e=$(id); if(e){ e.textContent='';  e.classList.remove('show'); } };
const si = (el,ok)  => { el.classList.remove('input-error','input-ok'); if(ok===true) el.classList.add('input-ok'); if(ok===false) el.classList.add('input-error'); };

function bindCounter(inpId, cntId, max){
  const inp=$(inpId), cnt=$(cntId); if(!inp||!cnt) return;
  inp.addEventListener('input',()=>{ cnt.textContent=inp.value.length+'/'+max; cnt.style.color=inp.value.length>max?'var(--er)':'#94a3b8'; });
}
bindCounter('aTitre','cntATitre',TITRE_MAX);
bindCounter('aNote', 'cntANote', NOTE_MAX);
bindCounter('mTitre','cntMTitre',TITRE_MAX);
bindCounter('mNote', 'cntMNote', NOTE_MAX);

function vTitre(val,errId,el){
  ce(errId);
  if(!val.trim()){ se(errId,'⚠ Le titre est obligatoire.'); si(el,false); return false; }
  if(val.length>TITRE_MAX){ se(errId,'⚠ Maximum '+TITRE_MAX+' caractères.'); si(el,false); return false; }
  if(!TITRE_REGEX.test(val)){ se(errId,'⚠ Pas de caractères spéciaux.'); si(el,false); return false; }
  si(el,true); return true;
}
function vDate(val,errId,el,lbl){
  ce(errId);
  if(!val){ se(errId,'⚠ La '+lbl+' est obligatoire.'); si(el,false); return false; }
  const d=new Date(val),now=new Date(); now.setSeconds(now.getSeconds()-120);
  if(d<now){ se(errId,'⚠ Impossible de choisir une date passée.'); si(el,false); return false; }
  si(el,true); return true;
}
function vDateFin(deb,fin,errId,el){
  if(!deb||!fin) return true;
  if(new Date(fin)<=new Date(deb)){ se(errId,'⚠ La fin doit être après le début.'); si(el,false); return false; }
  si(el,true); return true;
}
function vNote(val,errId,el){
  ce(errId);
  if(val.length>NOTE_MAX){ se(errId,'⚠ Maximum '+NOTE_MAX+' caractères.'); si(el,false); return false; }
  si(el,val.length>0?true:null); return true;
}
function setMin(id){ const el=$(id); if(!el) return; const n=new Date(); n.setSeconds(0,0); el.min=n.toISOString().slice(0,16); }

/* ══ Sélection type ══ */
function selectType(pill,hiddenId){
  pill.closest('.type-grid').querySelectorAll('.type-pill').forEach(p=>p.classList.remove('selected'));
  pill.classList.add('selected');
  $(hiddenId).value=pill.dataset.type;
}

/* ══ Modales ══ */
function ouvrirModalAjout(){ setMin('aDebut'); setMin('aFin'); $('modalAjout').classList.add('open'); }

function ouvrirModalModif(ev){
  $('mId').value    = ev.id;
  $('mTitre').value = ev.titre;
  $('mNote').value  = ev.note||'';
  $('mTypeVal').value = ev.type||'autre';
  $('mDebut').value = ev.debut_dt||'';
  $('mFin').value   = ev.fin_dt||'';
  $('cntMTitre').textContent = $('mTitre').value.length+'/10';
  $('cntMNote').textContent  = $('mNote').value.length+'/20';
  $('mTypeGrid').querySelectorAll('.type-pill').forEach(p=>p.classList.toggle('selected',p.dataset.type===ev.type));
  setMin('mDebut'); setMin('mFin');
  $('modalModif').classList.add('open');
}

function fermerModals(){
  ['modalAjout','modalModif'].forEach(id=>$(id).classList.remove('open'));
  ['errATitre','errADebut','errAFin','errANote','errMTitre','errMDebut','errMFin','errMNote'].forEach(ce);
  ['aTitre','aDebut','aFin','aNote','mTitre','mDebut','mFin','mNote'].forEach(id=>{ const el=$(id); if(el) el.classList.remove('input-error','input-ok'); });
}

document.querySelectorAll('.modal-overlay').forEach(o=>o.addEventListener('click',function(e){ if(e.target===this) fermerModals(); }));

/* ══ Validation soumission ══ */
$('formAjout').addEventListener('submit',function(e){
  let ok=true;
  if(!vTitre($('aTitre').value,'errATitre',$('aTitre'))) ok=false;
  if(!vDate($('aDebut').value,'errADebut',$('aDebut'),'date de début')) ok=false;
  if(!vDate($('aFin').value,'errAFin',$('aFin'),'date de fin')) ok=false;
  if(ok && !vDateFin($('aDebut').value,$('aFin').value,'errAFin',$('aFin'))) ok=false;
  if(!vNote($('aNote').value,'errANote',$('aNote'))) ok=false;
  if(!ok){ e.preventDefault(); showToast('Veuillez corriger les erreurs.',false); }
});

$('formModif').addEventListener('submit',function(e){
  let ok=true;
  if(!vTitre($('mTitre').value,'errMTitre',$('mTitre'))) ok=false;
  if(!vDate($('mDebut').value,'errMDebut',$('mDebut'),'date de début')) ok=false;
  if(!vDate($('mFin').value,'errMFin',$('mFin'),'date de fin')) ok=false;
  if(ok && !vDateFin($('mDebut').value,$('mFin').value,'errMFin',$('mFin'))) ok=false;
  if(!vNote($('mNote').value,'errMNote',$('mNote'))) ok=false;
  if(!ok){ e.preventDefault(); showToast('Veuillez corriger les erreurs.',false); }
});

/* Validation temps réel */
$('aTitre').addEventListener('input',function(){ vTitre(this.value,'errATitre',this); });
$('mTitre').addEventListener('input',function(){ vTitre(this.value,'errMTitre',this); });
$('aNote').addEventListener('input', function(){ vNote(this.value,'errANote',this); });
$('mNote').addEventListener('input', function(){ vNote(this.value,'errMNote',this); });
$('aDebut').addEventListener('change',function(){ vDate(this.value,'errADebut',this,'date de début'); });
$('aFin').addEventListener('change',function(){ vDate(this.value,'errAFin',this,'date de fin'); vDateFin($('aDebut').value,this.value,'errAFin',this); });
$('mDebut').addEventListener('change',function(){ vDate(this.value,'errMDebut',this,'date de début'); });
$('mFin').addEventListener('change',function(){ vDate(this.value,'errMFin',this,'date de fin'); vDateFin($('mDebut').value,this.value,'errMFin',this); });

/* ══ Recherche ══ */
function rechercherEvenement(q){
  q=q.toLowerCase().trim();
  document.querySelectorAll('.cal-ev-item').forEach(el=>{
    const t=(el.dataset.titre||'').toLowerCase();
    const show=!q||t.includes(q);
    el.style.opacity=show?'1':'0.15';
    el.style.pointerEvents=show?'':'none';
  });
}

/* ══ Ligne du temps ══ */
function updateNowLine(){
  const line=$('nowLine'); if(!line) return;
  const now=new Date();
  line.style.top=Math.max(0,((now.getHours()+now.getMinutes()/60)-8)*76)+'px';
}
updateNowLine(); setInterval(updateNowLine,60000);

/* ══ Scroll auto ══ */
window.addEventListener('DOMContentLoaded',()=>{
  const s=document.querySelector('.calendar-scroll'); if(!s) return;
  const now=new Date();
  s.scrollTop=Math.max(0,((now.getHours()+now.getMinutes()/60)-8)*76-120);
});

/* ══ Toast ══ */
function showToast(msg,success=true){
  const t=$('toast'),i=$('toastIcon'),x=$('toastMsg');
  i.className='toast-icon '+(success?'ok':'err'); i.textContent=success?'✓':'✗'; x.textContent=msg;
  t.classList.add('show'); setTimeout(()=>t.classList.remove('show'),3500);
}
<?php if($msg_succes): ?>
window.addEventListener('DOMContentLoaded',()=>showToast('<?= addslashes($msg_succes) ?>',true));
<?php endif; ?>
</script>