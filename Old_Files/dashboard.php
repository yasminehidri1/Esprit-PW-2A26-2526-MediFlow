<?php
// ============================================================
//  dashboard.php — MediFlow Pro (VERSION AMÉLIORÉE)
// ============================================================
require_once __DIR__ . '/../../../controller/RendezVousController.php';

$controller = new RendezVousController();
$medecin_id = 1;

if (isset($_GET['supprimer'])) {
    $controller->supprimerRdv(intval($_GET['supprimer']), $medecin_id);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'modifier') {
    $controller->modifierRdv($medecin_id);
}

$filtre      = isset($_GET['statut']) && in_array($_GET['statut'],['en_attente','confirme','annule']) ? $_GET['statut'] : '';
$data        = $controller->getDashboardData($medecin_id, $filtre);
$rendez_vous = $data['rendez_vous'];
$stats       = $data['stats'];

// Tri : du plus récent au plus ancien
usort($rendez_vous, fn($a,$b) => strcmp($b['date_rdv'].' '.$b['heure_rdv'], $a['date_rdv'].' '.$a['heure_rdv']));

// Pagination
$per_page    = 8;
$total_rdv   = count($rendez_vous);
$total_pages = max(1, (int)ceil($total_rdv/$per_page));
$page        = max(1, min($total_pages, intval($_GET['page'] ?? 1)));
$offset      = ($page-1)*$per_page;
$rdv_page    = array_slice($rendez_vous, $offset, $per_page);

// Prochain RDV du jour
$today    = date('Y-m-d');
$now_time = date('H:i:s');
$prochain = null;
foreach (array_filter($rendez_vous, fn($r)=>$r['date_rdv']===$today) as $r) {
    if ($r['heure_rdv'] >= $now_time && $r['statut'] !== 'annule') {
        if (!$prochain || $r['heure_rdv'] < $prochain['heure_rdv']) $prochain = $r;
    }
}

// Taux de confirmation
$taux_conf = $stats['total'] > 0 ? round(($stats['total']-$stats['nb_attente'])/$stats['total']*100) : 0;

// Calcul RDV par jour de la semaine (pour mini graphique activité)
$rdv_par_jour = array_fill(0,7,0);
$debut_sem = new DateTime(); $debut_sem->setISODate((int)date('Y'),(int)date('W')); $debut_sem->setTime(0,0,0);
foreach ($rendez_vous as $r) {
    $d = new DateTime($r['date_rdv']);
    $diff = (int)$debut_sem->diff($d)->days;
    if ($diff >= 0 && $diff < 7) $rdv_par_jour[$diff]++;
}
$max_rdv_jour = max(1, max($rdv_par_jour));

// Messages
$msg_succes = '';
$msg_erreur = '';
if (isset($_GET['succes'])) $msg_succes = $_GET['succes'] === 'modifie' ? 'Rendez-vous modifié avec succès.' : 'Rendez-vous supprimé avec succès.';
if (isset($_GET['succes']) && strpos($_GET['succes'],'succès')!==false) $msg_succes = urldecode($_GET['succes']);
if (isset($_GET['erreur'])) $msg_erreur = 'Une erreur est survenue.';

function pageUrl(int $p, string $filtre): string {
    $params = ['page' => $p];
    if ($filtre) $params['statut'] = $filtre;
    return 'dashboard.php?' . http_build_query($params);
}
$noms_j = ['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Dashboard — MediFlow Pro</title>
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|inter:400,500,600,700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --pr:#004d99;--prd:#1565c0;--prl:#d6e3ff;
  --te:#005851;--tel:#84f5e8;--teb:rgba(0,88,81,.10);
  --bg:#f0f4f8;--sf:#fff;--sfl:#f5f7fa;
  --bd:#e2e8f0;--tx:#0f172a;--tm:#64748b;--er:#ba1a1a;--erb:#ffdad6;
  --sh:0 2px 16px rgba(0,77,153,.08);--shh:0 8px 32px rgba(0,77,153,.15);
  --sw:220px;--rm:12px;--rl:16px;--rx:20px;--rf:9999px
}
body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--tx);min-height:100vh;display:flex}

/* ── SIDEBAR ── */
.sidebar{width:var(--sw);min-height:100vh;position:fixed;top:0;left:0;background:var(--sf);border-right:1px solid var(--bd);display:flex;flex-direction:column;padding:20px 12px;z-index:100}
.sb-brand{display:flex;align-items:center;gap:10px;padding:6px 8px 16px;border-bottom:1px solid var(--bd);margin-bottom:12px}
.brand-logo{width:38px;height:38px;background:linear-gradient(135deg,var(--pr),var(--prd));border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.brand-logo svg{width:20px;height:20px;fill:white}
.bt-name{font-family:'Manrope',sans-serif;font-weight:800;font-size:15px;color:#1e3a6e;display:block;line-height:1.1}
.bt-sub{font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:.12em;color:var(--tm);display:block}
.sb-profile{display:flex;align-items:center;gap:10px;padding:10px;background:var(--sfl);border-radius:var(--rm);margin-bottom:10px}
.pf-av{width:40px;height:40px;border-radius:var(--rf);background:var(--prl);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.pf-av svg{width:22px;height:22px;fill:var(--pr)}
.pf-n{font-family:'Manrope',sans-serif;font-weight:700;font-size:13px;display:block}
.pf-s{font-size:11px;color:var(--tm);display:block}
.sb-nav{display:flex;flex-direction:column;gap:2px;flex:1}
.nav-item{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:var(--rm);color:var(--tm);font-size:14px;font-weight:500;text-decoration:none;transition:all .15s;border-left:3px solid transparent}
.nav-item svg{width:18px;height:18px;flex-shrink:0}
.nav-item:hover{background:rgba(0,77,153,.05);color:var(--pr)}
.nav-item.active{background:var(--sf);color:var(--pr);font-weight:700;border-left-color:var(--te);box-shadow:var(--sh)}
.nav-item.logout{color:var(--er)}
.nav-item.logout:hover{background:rgba(186,26,26,.05)}
.sb-footer{padding-top:12px;border-top:1px solid var(--bd);display:flex;flex-direction:column;gap:2px}

/* ── MAIN ── */
.main{margin-left:var(--sw);flex:1;display:flex;flex-direction:column;min-height:100vh}
.topbar{height:64px;background:rgba(255,255,255,.9);backdrop-filter:blur(12px);border-bottom:1px solid var(--bd);display:flex;align-items:center;justify-content:space-between;padding:0 28px;position:sticky;top:0;z-index:50}
.topbar-left{display:flex;flex-direction:column}
.topbar-title{font-family:'Manrope',sans-serif;font-weight:800;font-size:18px;background:linear-gradient(135deg,#1e3a6e,var(--pr));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;line-height:1.2}
.topbar-date{font-size:11px;color:var(--tm);font-weight:500;margin-top:1px}
.topbar-right{display:flex;align-items:center;gap:8px}
.search-bar{display:flex;align-items:center;background:var(--sfl);border-radius:var(--rf);padding:7px 14px;gap:7px;width:210px;border:1px solid var(--bd)}
.search-bar svg{width:15px;height:15px;color:var(--tm);flex-shrink:0}
.search-bar input{border:none;background:transparent;outline:none;font-size:13px;color:var(--tx);width:100%;font-family:'Inter',sans-serif}
.search-bar input::placeholder{color:#94a3b8}
.notif-btn{width:36px;height:36px;border:none;background:var(--sfl);border-radius:var(--rm);display:flex;align-items:center;justify-content:center;color:var(--tm);cursor:pointer;border:1px solid var(--bd);position:relative}
.notif-btn svg{width:18px;height:18px}
.notif-dot{width:8px;height:8px;background:var(--er);border-radius:50%;position:absolute;top:6px;right:6px;border:2px solid white}

.page-content{padding:22px 28px;flex:1}

/* ── ALERTES ── */
.alert{display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:var(--rm);font-size:14px;font-weight:500;margin-bottom:18px}
.alert-ok{background:#dcfce7;color:#15803d;border:1px solid #bbf7d0}
.alert-er{background:#fee2e2;color:var(--er);border:1px solid #fecaca}

/* ── GRILLE STATS ── */
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:18px}
.stat-card{background:var(--sf);border-radius:var(--rl);padding:16px 18px;box-shadow:var(--sh);display:flex;align-items:center;gap:12px;transition:all .2s;cursor:default;border:1px solid transparent}
.stat-card:hover{box-shadow:var(--shh);transform:translateY(-2px);border-color:rgba(0,77,153,.08)}
.stat-icon{width:42px;height:42px;border-radius:var(--rm);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.stat-icon svg{width:20px;height:20px}
.si-blue  {background:var(--prl);color:var(--pr)}
.si-teal  {background:rgba(132,245,232,.25);color:var(--te)}
.si-orange{background:#fff7ed;color:#c2410c}
.si-red   {background:#fee2e2;color:var(--er)}
.si-green {background:#dcfce7;color:#15803d}
.stat-info{}
.stat-value{font-family:'Manrope',sans-serif;font-size:26px;font-weight:800;line-height:1;color:var(--tx)}
.stat-label{font-size:11px;color:var(--tm);margin-top:3px;font-weight:500}
.stat-trend{font-size:10px;font-weight:700;margin-top:4px;display:flex;align-items:center;gap:3px}
.trend-up  {color:#15803d}.trend-dw{color:var(--er)}.trend-ne{color:var(--tm)}

/* ── ROW : Prochain RDV + Activité ── */
.dash-row{display:grid;grid-template-columns:1fr 320px;gap:14px;margin-bottom:18px}

/* Bannière prochain RDV */
.next-banner{background:linear-gradient(135deg,#004d99,#1565c0);border-radius:var(--rx);padding:20px 24px;color:white;box-shadow:0 4px 20px rgba(0,77,153,.28);display:flex;align-items:center;justify-content:space-between;position:relative;overflow:hidden}
.next-banner::before{content:'';position:absolute;top:-30px;right:-30px;width:120px;height:120px;background:rgba(255,255,255,.06);border-radius:50%}
.next-banner::after{content:'';position:absolute;bottom:-40px;right:60px;width:80px;height:80px;background:rgba(255,255,255,.04);border-radius:50%}
.next-left{display:flex;align-items:center;gap:14px;z-index:1}
.next-ico{width:46px;height:46px;background:rgba(255,255,255,.18);border-radius:var(--rm);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.next-ico svg{width:22px;height:22px}
.next-lbl{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.12em;opacity:.75}
.next-name{font-family:'Manrope',sans-serif;font-weight:800;font-size:18px;margin-top:2px}
.next-meta{font-size:12px;opacity:.8;margin-top:3px;display:flex;align-items:center;gap:8px}
.next-meta span{display:flex;align-items:center;gap:4px}
.next-right{z-index:1;text-align:right}
.next-time-badge{background:rgba(255,255,255,.2);border-radius:var(--rf);padding:8px 18px;font-family:'Manrope',sans-serif;font-weight:800;font-size:18px;margin-bottom:6px;display:inline-block}
.next-countdown{font-size:11px;opacity:.7;font-weight:600}
.no-next{display:flex;align-items:center;gap:14px}
.no-next-ico{width:46px;height:46px;background:rgba(255,255,255,.12);border-radius:var(--rm);display:flex;align-items:center;justify-content:center}
.no-next-txt{font-size:14px;opacity:.85;font-weight:600}
.no-next-sub{font-size:11px;opacity:.6;margin-top:3px}

/* Graphique activité semaine */
.activity-card{background:var(--sf);border-radius:var(--rx);padding:18px;box-shadow:var(--sh)}
.ac-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px}
.ac-title{font-family:'Manrope',sans-serif;font-size:13px;font-weight:700;color:var(--tx)}
.ac-total{font-size:12px;font-weight:600;color:var(--tm)}
.ac-bars{display:flex;align-items:flex-end;gap:6px;height:72px}
.ac-bar-wrap{display:flex;flex-direction:column;align-items:center;gap:4px;flex:1}
.ac-bar-bg{width:100%;background:var(--sfl);border-radius:4px;height:60px;display:flex;align-items:flex-end;overflow:hidden}
.ac-bar-fill{width:100%;border-radius:4px;transition:height .5s ease;min-height:4px}
.ac-bar-lbl{font-size:9px;font-weight:700;color:var(--tm);text-align:center}
.ac-bar-val{font-size:10px;font-weight:800;color:var(--pr);margin-top:1px}
.ac-bar-wrap.today-bar .ac-bar-fill{background:linear-gradient(180deg,var(--pr),var(--prd))}
.ac-bar-wrap:not(.today-bar) .ac-bar-fill{background:var(--prl)}

/* ── BANDEAU PLANNING ── */
.plan-banner{display:flex;justify-content:space-between;align-items:center;background:var(--sf);border-radius:var(--rx);padding:16px 22px;box-shadow:var(--sh);border:1px solid rgba(194,198,212,.2);margin-bottom:18px}
.pb-left{display:flex;align-items:center;gap:12px}
.pb-ico{width:42px;height:42px;background:var(--prl);border-radius:var(--rm);display:flex;align-items:center;justify-content:center}
.pb-ico svg{width:20px;height:20px;color:var(--pr)}
.pb-title{font-family:'Manrope',sans-serif;font-size:14px;font-weight:700}
.pb-sub{font-size:12px;color:var(--tm);margin-top:2px}
.pb-right{display:flex;align-items:center;gap:8px}
.btn-plan{display:flex;align-items:center;gap:7px;padding:9px 16px;background:linear-gradient(135deg,var(--pr),var(--prd));color:white;border-radius:var(--rm);font-family:'Manrope',sans-serif;font-weight:700;font-size:13px;text-decoration:none;box-shadow:0 2px 8px rgba(0,77,153,.25);transition:all .15s}
.btn-plan:hover{box-shadow:0 4px 16px rgba(0,77,153,.35);transform:translateY(-1px)}
.btn-plan svg{width:15px;height:15px}
.btn-plan-ghost{display:flex;align-items:center;gap:6px;padding:8px 14px;background:transparent;color:var(--pr);border-radius:var(--rm);font-family:'Manrope',sans-serif;font-weight:600;font-size:13px;text-decoration:none;border:1.5px solid var(--bd);transition:all .15s}
.btn-plan-ghost:hover{border-color:var(--pr);background:var(--prl)}

/* ── SECTION CONTRÔLES ── */
.section-controls{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;flex-wrap:wrap;gap:10px}
.section-title{font-family:'Manrope',sans-serif;font-weight:800;font-size:17px}
.section-title small{font-size:13px;color:var(--tm);font-weight:500;margin-left:6px}
.filter-tabs{display:flex;gap:6px;flex-wrap:wrap}
.ftab{padding:6px 13px;border-radius:var(--rf);border:1.5px solid var(--bd);background:var(--sf);font-size:12px;font-weight:600;color:var(--tm);text-decoration:none;transition:all .15s}
.ftab:hover{border-color:var(--pr);color:var(--pr)}
.ftab.active{background:var(--pr);border-color:var(--pr);color:white}
.ftab-at{border-color:rgba(0,77,153,.3);color:var(--pr)}
.ftab-at.active{background:var(--prl);color:var(--pr);border-color:var(--pr)}
.ftab-co.active{background:#dcfce7;color:#15803d;border-color:#15803d}
.ftab-an.active{background:#fee2e2;color:var(--er);border-color:var(--er)}
.inline-search{display:flex;align-items:center;background:var(--sfl);border-radius:var(--rf);padding:6px 12px;gap:7px;border:1px solid var(--bd)}
.inline-search svg{width:14px;height:14px;color:var(--tm);flex-shrink:0}
.inline-search input{border:none;background:transparent;outline:none;font-size:13px;color:var(--tx);width:150px;font-family:'Inter',sans-serif}
.inline-search input::placeholder{color:#94a3b8}

/* ── LISTE RDV ── */
.rdv-list{display:flex;flex-direction:column;gap:7px}
.rdv-row{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;background:var(--sf);border-radius:var(--rl);border:1px solid transparent;box-shadow:var(--sh);transition:all .2s;position:relative;overflow:hidden}
.rdv-row:hover{border-color:rgba(194,198,212,.5);box-shadow:var(--shh);transform:translateY(-1px)}
.rdv-row.is-today{border-left:3px solid var(--te)}
.rdv-row.is-past{opacity:.6}
.rdv-row.is-today::before{content:'';position:absolute;top:0;left:0;bottom:0;width:3px;background:linear-gradient(180deg,var(--te),#00897b)}

.rdv-left{display:flex;align-items:center;gap:13px;flex:1;min-width:0}
.pt-av{position:relative;flex-shrink:0}
.pt-av-circle{width:42px;height:42px;border-radius:var(--rm);display:flex;align-items:center;justify-content:center;font-family:'Manrope',sans-serif;font-weight:800;font-size:14px}
.status-dot{position:absolute;bottom:-3px;right:-3px;width:11px;height:11px;border-radius:50%;border:2px solid white}
.dot-co{background:#00897b}.dot-at{background:var(--pr)}.dot-an{background:var(--er)}.dot-gr{background:#94a3b8}

.rdv-info{display:grid;grid-template-columns:170px 150px 110px auto;gap:8px;align-items:center;flex:1;min-width:0}
.pt-name{font-weight:700;font-size:14px;color:var(--tx);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.pt-cin{font-size:11px;color:#94a3b8;margin-top:1px}
.meta-date{display:flex;align-items:center;gap:5px;font-size:13px;font-weight:600;color:var(--tm)}
.meta-date svg{width:14px;height:14px;color:var(--pr);flex-shrink:0}
.meta-time{display:flex;align-items:center;gap:5px;font-size:13px;font-weight:600;color:var(--tm)}
.meta-time svg{width:14px;height:14px;color:var(--te);flex-shrink:0}
.badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:var(--rf);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em}
.b-co{background:var(--teb);color:var(--te)}
.b-at{background:rgba(0,77,153,.10);color:var(--pr)}
.b-an{background:var(--erb);color:var(--er)}
.b-today{background:#f0fdf4;color:#15803d;font-size:10px;padding:2px 8px;border-radius:var(--rf);border:1px solid #bbf7d0}
.b-past{background:var(--sfl);color:#94a3b8;font-size:10px;padding:2px 8px;border-radius:var(--rf);border:1px solid var(--bd)}

.rdv-actions{display:flex;align-items:center;gap:5px;padding-left:14px;border-left:1px solid var(--bd);flex-shrink:0}
.act-btn{width:32px;height:32px;border:none;border-radius:var(--rm);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .15s;text-decoration:none}
.act-btn svg{width:15px;height:15px}
.act-edit{background:rgba(0,77,153,.07);color:var(--pr)}
.act-edit:hover{background:var(--pr);color:white}
.act-del{background:rgba(186,26,26,.07);color:var(--er)}
.act-del:hover{background:var(--er);color:white}
.act-view{background:rgba(0,88,81,.07);color:var(--te)}
.act-view:hover{background:var(--te);color:white}

/* Empty state */
.empty-state{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:52px 24px;background:var(--sf);border-radius:var(--rx);box-shadow:var(--sh)}
.empty-state svg{width:48px;height:48px;color:#cbd5e1;margin-bottom:16px}
.empty-state .es-title{font-family:'Manrope',sans-serif;font-weight:700;font-size:16px;color:var(--tx);margin-bottom:6px}
.empty-state .es-sub{font-size:13px;color:var(--tm);text-align:center}

/* ── PAGINATION ── */
.pag-wrap{display:flex;align-items:center;justify-content:space-between;margin-top:18px;padding:0 2px}
.pag-info{font-size:13px;color:var(--tm)}
.pag-info strong{color:var(--tx);font-weight:700}
.pag-nav{display:flex;align-items:center;gap:4px}
.pg-btn{width:32px;height:32px;border:1.5px solid var(--bd);background:var(--sf);border-radius:var(--rm);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;color:var(--tm);text-decoration:none;transition:all .15s}
.pg-btn:hover{border-color:var(--pr);color:var(--pr)}
.pg-btn.active{background:var(--pr);border-color:var(--pr);color:white}
.pg-btn.disabled{opacity:.35;pointer-events:none}
.pg-btn svg{width:14px;height:14px}

/* ── MODALE ── */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:200;align-items:center;justify-content:center}
.modal-overlay.open{display:flex}
.modal{background:var(--sf);border-radius:var(--rx);padding:28px;width:460px;max-width:95vw;box-shadow:0 20px 60px rgba(0,0,0,.2)}
.modal-title{font-family:'Manrope',sans-serif;font-size:17px;font-weight:800;margin-bottom:5px}
.modal-sub{font-size:13px;color:var(--tm);margin-bottom:20px}
.modal-patient-badge{display:flex;align-items:center;gap:10px;background:var(--prl);border-radius:var(--rm);padding:12px 16px;margin-bottom:20px}
.mpi{width:36px;height:36px;border-radius:50%;background:var(--pr);color:white;font-family:'Manrope',sans-serif;font-weight:800;font-size:13px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.mpn{font-weight:700;font-size:14px;color:var(--tx)}
.modal-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.modal-group{display:flex;flex-direction:column;gap:5px;margin-bottom:14px}
.modal-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--tm)}
.modal-input{width:100%;background:var(--sfl);border:2px solid transparent;border-radius:var(--rm);padding:10px 14px;font-size:14px;font-family:'Inter',sans-serif;color:var(--tx);outline:none;transition:all .18s}
.modal-input:focus{border-color:var(--te);background:white}
.modal-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:6px}
.btn-mc{padding:10px 18px;background:transparent;border:1.5px solid var(--bd);color:var(--tm);font-family:'Manrope',sans-serif;font-weight:600;font-size:13px;border-radius:var(--rm);cursor:pointer}
.btn-ms{display:flex;align-items:center;gap:7px;padding:10px 18px;background:linear-gradient(135deg,var(--pr),var(--prd));color:white;border:none;border-radius:var(--rm);font-family:'Manrope',sans-serif;font-weight:700;font-size:13px;cursor:pointer;box-shadow:0 2px 8px rgba(0,77,153,.25)}
.btn-ms svg{width:14px;height:14px}

/* Toast */
.toast{position:fixed;bottom:22px;right:22px;background:#0f172a;color:white;padding:12px 18px;border-radius:var(--rm);font-size:13px;font-weight:600;z-index:999;transform:translateY(70px);opacity:0;transition:all .3s;display:flex;align-items:center;gap:9px;pointer-events:none}
.toast.show{transform:translateY(0);opacity:1}
</style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="sb-brand">
    <div class="brand-logo"><svg viewBox="0 0 24 24"><path d="M19 8h-3V5a1 1 0 0 0-1-1h-6a1 1 0 0 0-1 1v3H5a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h3v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3h3a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1zm-1 6h-3a1 1 0 0 0-1 1v3h-4v-3a1 1 0 0 0-1-1H6v-4h3a1 1 0 0 0 1-1V6h4v3a1 1 0 0 0 1 1h3v4z"/></svg></div>
    <div><span class="bt-name">MediFlow Pro</span><span class="bt-sub">Practitioner Portal</span></div>
  </div>
  <div class="sb-profile">
    <div class="pf-av"><svg viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg></div>
    <div><span class="pf-n">Dr. Marc Laurent</span><span class="pf-s">Cardiologue</span></div>
  </div>
  <nav class="sb-nav">
    <a href="dashboard.php" class="nav-item active"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>Dashboard</a>
    <a href="planning.php" class="nav-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Planning</a>
    <a href="patients.php" class="nav-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>Patients</a>
    <a href="statistiques.php" class="nav-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>Statistiques</a>
    <a href="settings.php" class="nav-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>Paramètres</a>
  </nav>
  <div class="sb-footer">
    <a href="logout.php" class="nav-item logout"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>Déconnexion</a>
  </div>
</aside>

<!-- MAIN -->
<div class="main">
  <header class="topbar">
    <div class="topbar-left">
      <div class="topbar-title">Tableau de Bord</div>
      <div class="topbar-date">
        <?php
          $jours_fr = ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'];
          $mois_fr  = ['','Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
          echo $jours_fr[date('w')] . ' ' . date('j') . ' ' . $mois_fr[(int)date('n')] . ' ' . date('Y');
        ?>
      </div>
    </div>
    <div class="topbar-right">
      <div class="search-bar">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Rechercher un patient…" id="searchRdv" oninput="filterRdv()">
      </div>
      <button class="notif-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        <?php if ($stats['nb_attente'] > 0): ?><span class="notif-dot"></span><?php endif; ?>
      </button>
    </div>
  </header>

  <div class="page-content">

    <!-- Alertes -->
    <?php if ($msg_succes): ?><div class="alert alert-ok">✓ <?= htmlspecialchars($msg_succes) ?></div><?php endif; ?>
    <?php if ($msg_erreur): ?><div class="alert alert-er">✗ <?= htmlspecialchars($msg_erreur) ?></div><?php endif; ?>

    <!-- ── ROW : Prochain RDV + Activité ── -->
    <div class="dash-row">
      <!-- Prochain RDV -->
      <div class="next-banner">
        <?php if ($prochain): ?>
        <?php
          $p_ini = strtoupper(substr($prochain['patient_prenom'],0,1).substr($prochain['patient_nom'],0,1));
          $p_hr  = date('H:i', strtotime($prochain['heure_rdv']));
          $p_date = date('d M', strtotime($prochain['date_rdv']));
          // Calcul countdown
          $rdv_dt = new DateTime($prochain['date_rdv'].' '.$prochain['heure_rdv']);
          $now_dt = new DateTime();
          $diff_min = (int)round(($rdv_dt->getTimestamp()-$now_dt->getTimestamp())/60);
          $countdown = $diff_min < 60 ? "Dans {$diff_min} min" : "Dans ".floor($diff_min/60)."h".($diff_min%60>0?sprintf('%02d',($diff_min%60)).'min':'');
        ?>
        <div class="next-left">
          <div class="next-ico" style="background:rgba(255,255,255,.2);font-family:'Manrope';font-weight:800;font-size:15px;color:white"><?= $p_ini ?></div>
          <div>
            <div class="next-lbl">Prochain patient</div>
            <div class="next-name"><?= htmlspecialchars($prochain['patient_prenom'].' '.$prochain['patient_nom']) ?></div>
            <div class="next-meta">
              <span><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg><?= $p_date ?></span>
              <span>CIN: <?= htmlspecialchars($prochain['cin'] ?? '—') ?></span>
              <span>· <?= ucfirst($prochain['genre'] ?? '') ?></span>
            </div>
          </div>
        </div>
        <div class="next-right">
          <div class="next-time-badge">⏰ <?= $p_hr ?></div>
          <div class="next-countdown"><?= $countdown ?></div>
        </div>
        <?php else: ?>
        <div class="no-next">
          <div class="no-next-ico">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </div>
          <div>
            <div class="no-next-txt">Aucun patient à venir aujourd'hui</div>
            <div class="no-next-sub">Votre journée est libre ou tous les RDV sont passés.</div>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <!-- Activité semaine -->
      <div class="activity-card">
        <div class="ac-header">
          <span class="ac-title">📊 Activité semaine</span>
          <span class="ac-total"><?= array_sum($rdv_par_jour) ?> RDV</span>
        </div>
        <div class="ac-bars">
          <?php foreach ($rdv_par_jour as $i => $nb):
            $is_today_bar = ($i === intval(date('N'))-1);
            $pct = round($nb/$max_rdv_jour*100);
          ?>
          <div class="ac-bar-wrap <?= $is_today_bar?'today-bar':'' ?>">
            <div class="ac-bar-val"><?= $nb ?: '' ?></div>
            <div class="ac-bar-bg">
              <div class="ac-bar-fill" style="height:<?= $pct ?>%"></div>
            </div>
            <div class="ac-bar-lbl"><?= $noms_j[$i] ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- ── BANDEAU PLANNING ── -->
    <div class="plan-banner">
      <div class="pb-left">
        <div class="pb-ico">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div>
          <div class="pb-title">Planning de la journée</div>
          <div class="pb-sub"><?= $stats['nb_aujourdhui'] ?> consultation(s) prévue(s) · Semaine <?= date('W') ?></div>
        </div>
      </div>
      <div class="pb-right">
        <a href="planning.php?vue=jour&date=<?= $today ?>" class="btn-plan-ghost">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          Vue jour
        </a>
        <a href="planning.php" class="btn-plan">
          Planning complet
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
      </div>
    </div>

    <!-- ── LISTE RDV ── -->
    <div class="section-controls">
      <h2 class="section-title">
        Rendez-vous
        <small>(<?= $total_rdv ?> au total)</small>
      </h2>
      <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
        <div class="filter-tabs">
          <a href="dashboard.php"                   class="ftab <?= !$filtre           ?'active':'' ?>">Tous</a>
          <a href="dashboard.php?statut=en_attente" class="ftab ftab-at <?= $filtre==='en_attente'?'active':'' ?>">⏳ En attente</a>
          <a href="dashboard.php?statut=confirme"   class="ftab ftab-co <?= $filtre==='confirme'  ?'active':'' ?>">✓ Confirmés</a>
          <a href="dashboard.php?statut=annule"     class="ftab ftab-an <?= $filtre==='annule'    ?'active':'' ?>">✗ Annulés</a>
        </div>
        <div class="inline-search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input type="text" placeholder="Nom du patient…" id="searchRdv2" oninput="filterRdv2()">
        </div>
      </div>
    </div>

    <div class="rdv-list" id="rdvList">
      <?php if (empty($rdv_page)): ?>
      <div class="empty-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <div class="es-title">Aucun rendez-vous trouvé</div>
        <div class="es-sub">Les RDV pris par les patients apparaîtront ici.</div>
      </div>
      <?php else: ?>
        <?php foreach ($rdv_page as $rdv):
          $ini       = strtoupper(substr($rdv['patient_prenom'],0,1).substr($rdv['patient_nom'],0,1));
          $date_fr   = date('d M Y', strtotime($rdv['date_rdv']));
          $heure_fr  = date('H:i',   strtotime($rdv['heure_rdv']));
          $is_today  = ($rdv['date_rdv'] === $today);
          $is_past   = ($rdv['date_rdv'] < $today);

          $dot_cls   = ['confirme'=>'dot-co','en_attente'=>'dot-at','annule'=>'dot-an'][$rdv['statut']] ?? 'dot-gr';
          $bdg_cls   = ['confirme'=>'b-co','en_attente'=>'b-at','annule'=>'b-an'][$rdv['statut']] ?? '';
          $bdg_lbl   = ['confirme'=>'✓ Confirmé','en_attente'=>'⏳ En attente','annule'=>'✗ Annulé'][$rdv['statut']] ?? $rdv['statut'];
          $row_cls   = 'rdv-row'.($is_today?' is-today':'').($is_past?' is-past':'');

          // Couleur avatar
          $colors    = ['#d6e3ff','#dcfce7','#fff7ed','#fce7f3','#ede9fe','#e0f2fe'];
          $tcolors   = ['#004d99','#15803d','#c2410c','#be185d','#6d28d9','#0369a1'];
          $ci        = ord($rdv['patient_prenom'][0] ?? 'A') % 6;

          $djson = htmlspecialchars(json_encode([
            'id'=>$rdv['id'],'prenom'=>$rdv['patient_prenom'],'nom'=>$rdv['patient_nom'],
            'date'=>$rdv['date_rdv'],'heure'=>substr($rdv['heure_rdv'],0,5),'statut'=>$rdv['statut'],
          ]));
        ?>
        <div class="<?= $row_cls ?>" data-patient="<?= htmlspecialchars($rdv['patient_prenom'].' '.$rdv['patient_nom']) ?>">
          <div class="rdv-left">
            <!-- Avatar -->
            <div class="pt-av">
              <div class="pt-av-circle" style="background:<?= $colors[$ci] ?>;color:<?= $tcolors[$ci] ?>">
                <?= $ini ?>
              </div>
              <div class="status-dot <?= $dot_cls ?>"></div>
            </div>
            <!-- Infos -->
            <div class="rdv-info">
              <div>
                <div class="pt-name"><?= htmlspecialchars($rdv['patient_prenom'].' '.$rdv['patient_nom']) ?></div>
                <div class="pt-cin">CIN : <?= htmlspecialchars($rdv['cin']) ?> · <?= ucfirst($rdv['genre']) ?></div>
              </div>
              <div class="meta-date">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <?php if ($is_today): ?><span class="b-today">Aujourd'hui</span>
                <?php elseif ($is_past): ?><span class="b-past"><?= $date_fr ?></span>
                <?php else: ?><?= $date_fr ?><?php endif; ?>
              </div>
              <div class="meta-time">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <?= $heure_fr ?>
              </div>
              <span class="badge <?= $bdg_cls ?>"><?= $bdg_lbl ?></span>
            </div>
          </div>
          <!-- Actions -->
          <div class="rdv-actions">
            <button class="act-btn act-edit" title="Modifier" onclick="openModal(<?= $djson ?>)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </button>
            <a href="modifier-rdv.php?id=<?= $rdv['id'] ?>" class="act-btn act-view" title="Voir détail">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </a>
            <a href="dashboard.php?supprimer=<?= $rdv['id'] ?>&page=<?= $page ?><?= $filtre?'&statut='.$filtre:'' ?>"
               class="act-btn act-del" title="Supprimer"
               onclick="return confirm('Supprimer le RDV de <?= htmlspecialchars($rdv['patient_prenom'].' '.$rdv['patient_nom']) ?> ?')">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
            </a>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="pag-wrap">
      <div class="pag-info">Affichage <strong><?= $offset+1 ?>–<?= min($offset+$per_page,$total_rdv) ?></strong> sur <strong><?= $total_rdv ?></strong> rendez-vous</div>
      <nav class="pag-nav">
        <a href="<?= pageUrl($page-1,$filtre) ?>" class="pg-btn <?= $page<=1?'disabled':'' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        </a>
        <?php
          $s = max(1,$page-2); $e = min($total_pages,$page+2);
          if ($s>1){ echo '<a href="'.pageUrl(1,$filtre).'" class="pg-btn">1</a>'; if ($s>2) echo '<span class="pg-btn" style="cursor:default;border:none">…</span>'; }
          for ($p=$s;$p<=$e;$p++) echo '<a href="'.pageUrl($p,$filtre).'" class="pg-btn '.($p===$page?'active':'').'">'.$p.'</a>';
          if ($e<$total_pages){ if ($e<$total_pages-1) echo '<span class="pg-btn" style="cursor:default;border:none">…</span>'; echo '<a href="'.pageUrl($total_pages,$filtre).'" class="pg-btn">'.$total_pages.'</a>'; }
        ?>
        <a href="<?= pageUrl($page+1,$filtre) ?>" class="pg-btn <?= $page>=$total_pages?'disabled':'' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
      </nav>
    </div>
    <?php endif; ?>

  </div><!-- /page-content -->
</div><!-- /main -->

<!-- Toast -->
<div class="toast" id="toast" style="pointer-events:none"></div>

<!-- ── MODALE MODIFIER ── -->
<div class="modal-overlay" id="modalOverlay">
  <div class="modal">
    <h3 class="modal-title">Modifier le Rendez-vous</h3>
    <p class="modal-sub">Modifiez la date, l'heure ou le statut.</p>
    <div class="modal-patient-badge">
      <div class="mpi" id="mIni"></div>
      <div class="mpn" id="mNom"></div>
    </div>
    <form method="POST" action="dashboard.php">
      <input type="hidden" name="action" value="modifier">
      <input type="hidden" name="rdv_id" id="mRdvId">
      <div class="modal-row">
        <div class="modal-group">
          <label class="modal-label">Date</label>
          <input class="modal-input" type="date" name="date_rdv" id="mDate" min="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="modal-group">
          <label class="modal-label">Heure</label>
          <input class="modal-input" type="time" name="heure_rdv" id="mHeure" required>
        </div>
      </div>
      <div class="modal-group">
        <label class="modal-label">Statut</label>
        <select class="modal-input" name="statut" id="mStatut">
          <option value="en_attente">⏳ En attente</option>
          <option value="confirme">✓ Confirmé</option>
          <option value="annule">✗ Annulé</option>
        </select>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn-mc" onclick="closeModal()">Annuler</button>
        <button type="submit" class="btn-ms">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          Enregistrer
        </button>
      </div>
    </form>
  </div>
</div>

<script>
// ── Filtrage live ─────────────────────────────────────────────
function filterRdv() {
  const q = document.getElementById('searchRdv').value.toLowerCase();
  filterRows(q);
}
function filterRdv2() {
  const q = document.getElementById('searchRdv2').value.toLowerCase();
  filterRows(q);
}
function filterRows(q){
  document.querySelectorAll('.rdv-row').forEach(r => {
    r.style.display = r.dataset.patient.toLowerCase().includes(q) ? '' : 'none';
  });
}

// ── Modale ───────────────────────────────────────────────────
function openModal(rdv) {
  document.getElementById('mRdvId').value  = rdv.id;
  document.getElementById('mDate').value   = rdv.date;
  document.getElementById('mHeure').value  = rdv.heure;
  document.getElementById('mStatut').value = rdv.statut;
  document.getElementById('mIni').textContent = ((rdv.prenom[0]||'')+(rdv.nom[0]||'')).toUpperCase();
  document.getElementById('mNom').textContent  = rdv.prenom + ' ' + rdv.nom;
  document.getElementById('modalOverlay').classList.add('open');
}
function closeModal() { document.getElementById('modalOverlay').classList.remove('open'); }
document.getElementById('modalOverlay').addEventListener('click', e => { if(e.target===document.getElementById('modalOverlay'))closeModal(); });

// ── Compte à rebours prochain patient ────────────────────────
<?php if ($prochain): ?>
function updateCountdown(){
  const rdvTs = <?= strtotime($prochain['date_rdv'].' '.$prochain['heure_rdv']) ?> * 1000;
  const diff  = Math.floor((rdvTs - Date.now()) / 60000);
  const el    = document.querySelector('.next-countdown');
  if (!el) return;
  if (diff <= 0) { el.textContent = '▶ En cours maintenant'; return; }
  el.textContent = diff < 60 ? `Dans ${diff} min` : `Dans ${Math.floor(diff/60)}h${diff%60>0?(diff%60+'min'):''}`;
}
updateCountdown(); setInterval(updateCountdown, 30000);
<?php endif; ?>

// ── Toast ────────────────────────────────────────────────────
<?php if ($msg_succes): ?>
window.addEventListener('DOMContentLoaded', () => {
  const t = document.getElementById('toast');
  t.innerHTML = '<span style="color:#16a34a;font-size:15px">✓</span><?= addslashes($msg_succes) ?>';
  t.style.cssText = 'transform:translateY(0);opacity:1;background:#0f172a;color:white;padding:12px 18px;border-radius:12px;font-size:13px;font-weight:600;z-index:999;position:fixed;bottom:22px;right:22px;display:flex;align-items:center;gap:9px';
  setTimeout(()=>t.style.cssText+='transition:all .3s;transform:translateY(70px);opacity:0', 3200);
});
<?php endif; ?>
</script>
</body>
</html>