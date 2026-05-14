<?php
$role = $data['role'] ?? ($_SESSION['user']['role'] ?? '');
$cu   = $data['currentUser'] ?? [];
$prenom = htmlspecialchars($cu['prenom'] ?? 'Utilisateur');
?>
<!-- Greeting -->
<section>
  <h2 class="text-4xl font-extrabold bg-gradient-to-r from-primary via-primary-container to-primary bg-clip-text text-transparent tracking-tight">
    Bienvenue, <?= $prenom ?> 👋
  </h2>
  <p class="text-on-surface-variant mt-2 font-medium">Votre tableau de bord — <?= date('d F Y') ?>.</p>
</section>

<?php if ($role === 'Admin'): ?>
<!-- ═══════════════ ADMIN ═══════════════ -->

<!-- Inline styles for admin dashboard -->
<style>
@keyframes countUp { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
@keyframes barGrow { from { width:0; } to { width:var(--bar-w); } }
@keyframes pulseRing { 0%,100%{transform:scale(1);opacity:.6} 50%{transform:scale(1.4);opacity:0} }
.kpi-card { transition: transform .25s cubic-bezier(.34,1.56,.64,1), box-shadow .25s ease; }
.kpi-card:hover { transform: translateY(-5px); box-shadow: 0 20px 48px rgba(0,77,153,.18); }
.kpi-num { animation: countUp .5s ease both; }
.role-bar { animation: barGrow .8s cubic-bezier(.25,.46,.45,.94) both; }
.activity-row { transition: background .2s ease; }
.activity-row:hover { background: rgba(0,77,153,.04); }
.status-ring { animation: pulseRing 2s ease-in-out infinite; }
.admin-table tr { transition: background .15s ease; }
.admin-table tbody tr:hover { background: #f0f5ff; }
</style>

<!-- KPI Cards -->
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
<?php
$totalUsers    = $data['stats']['totalUsers']    ?? 0;
$totalPatients = $data['stats']['totalPatients'] ?? 0;
$totalRoles    = isset($data['stats']['usersByRole']) ? count($data['stats']['usersByRole']) : 0;

$kpis = [
  ['label'=>'Utilisateurs totaux', 'val'=>$totalUsers,    'icon'=>'group',         'from'=>'#004d99','to'=>'#1565c0', 'sub'=>'Tous rôles confondus'],
  ['label'=>'Patients actifs',     'val'=>$totalPatients, 'icon'=>'personal_injury','from'=>'#005851','to'=>'#00736a', 'sub'=>'Patients enregistrés'],
  ['label'=>'Rôles système',       'val'=>$totalRoles,    'icon'=>'shield_person',  'from'=>'#4a5f83','to'=>'#6b7db3', 'sub'=>'Types d\'accès définis'],
  ['label'=>'Statut système',      'val'=>'En ligne',     'icon'=>'verified',       'from'=>'#1b5e20','to'=>'#2e7d32', 'sub'=>'Tous systèmes opérationnels','online'=>true],
];
foreach ($kpis as $k): ?>
<div class="kpi-card rounded-2xl p-6 text-white relative overflow-hidden cursor-default"
     style="background:linear-gradient(135deg,<?= $k['from'] ?>,<?= $k['to'] ?>);box-shadow:0 8px 24px <?= $k['from'] ?>44;">
  <!-- bg glow blob -->
  <div style="position:absolute;top:-20px;right:-20px;width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,.08);pointer-events:none;"></div>
  <div class="flex items-start justify-between mb-4">
    <div style="width:44px;height:44px;border-radius:12px;background:rgba(255,255,255,.18);display:flex;align-items:center;justify-content:center;">
      <span class="material-symbols-outlined text-white" style="font-size:22px;font-variation-settings:'FILL' 1"><?= $k['icon'] ?></span>
    </div>
    <?php if (!empty($k['online'])): ?>
    <div style="position:relative;width:10px;height:10px;margin-top:4px;">
      <div class="status-ring" style="position:absolute;inset:0;border-radius:50%;background:#4ade80;"></div>
      <div style="position:absolute;inset:0;border-radius:50%;background:#4ade80;"></div>
    </div>
    <?php endif; ?>
  </div>
  <p class="kpi-num" style="font-size:2.4rem;font-weight:900;line-height:1;letter-spacing:-1px;"><?= $k['val'] ?></p>
  <p style="font-size:13px;font-weight:700;margin-top:6px;opacity:.95;"><?= $k['label'] ?></p>
  <p style="font-size:11px;opacity:.65;margin-top:3px;"><?= $k['sub'] ?></p>
</div>
<?php endforeach; ?>
</section>

<!-- Role Distribution + System Health -->
<section class="grid grid-cols-1 lg:grid-cols-3 gap-5">

  <!-- Role distribution bars -->
  <div class="lg:col-span-2 bg-white rounded-2xl p-7 border border-slate-100 shadow-sm">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h3 style="font-size:16px;font-weight:800;color:#191c1e;">Répartition des rôles</h3>
        <p style="font-size:12px;color:#6b7280;margin-top:2px;">Distribution des utilisateurs par type d'accès</p>
      </div>
      <span style="background:#eff6ff;color:#004d99;font-size:11px;font-weight:700;padding:4px 12px;border-radius:20px;"><?= $totalUsers ?> au total</span>
    </div>
    <?php
    $roleColors = ['Admin'=>'#004d99','Patient'=>'#005851','Medecin'=>'#4a5f83','Technicien'=>'#7c3aed','redacteur'=>'#d97706','pharmacien'=>'#0891b2','Fournisseur'=>'#dc2626'];
    $roles = $data['stats']['usersByRole'] ?? [];
    $maxCount = max(1, max(array_column($roles, 'user_count') ?: [1]));
    foreach ($roles as $r):
      $cnt   = (int)($r['user_count'] ?? 0);
      $name  = $r['role_name'] ?? '—';
      $pct   = round($cnt / $maxCount * 100);
      $color = $roleColors[$name] ?? '#6b7280';
    ?>
    <div style="margin-bottom:18px;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
        <div style="display:flex;align-items:center;gap:8px;">
          <div style="width:8px;height:8px;border-radius:50%;background:<?= $color ?>;"></div>
          <span style="font-size:13px;font-weight:600;color:#374151;"><?= htmlspecialchars($name) ?></span>
        </div>
        <span style="font-size:13px;font-weight:800;color:<?= $color ?>;"><?= $cnt ?></span>
      </div>
      <div style="height:8px;background:#f1f5f9;border-radius:99px;overflow:hidden;">
        <div class="role-bar" style="height:100%;border-radius:99px;background:linear-gradient(90deg,<?= $color ?>,<?= $color ?>aa);--bar-w:<?= $pct ?>%;width:<?= $pct ?>%;"></div>
      </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($roles)): ?>
      <p style="color:#9ca3af;text-align:center;padding:24px 0;">Aucun rôle défini</p>
    <?php endif; ?>
  </div>

  <!-- System health panel -->
  <div class="bg-white rounded-2xl p-7 border border-slate-100 shadow-sm flex flex-col gap-4">
    <div>
      <h3 style="font-size:16px;font-weight:800;color:#191c1e;">Santé du système</h3>
      <p style="font-size:12px;color:#6b7280;margin-top:2px;">Vue d'ensemble en temps réel</p>
    </div>
    <?php
    $health = [
      ['label'=>'Base de données',  'status'=>true,  'icon'=>'database'],
      ['label'=>'Authentification', 'status'=>true,  'icon'=>'lock'],
      ['label'=>'Serveur web',      'status'=>true,  'icon'=>'dns'],
      ['label'=>'Stockage fichiers','status'=>true,  'icon'=>'folder'],
    ];
    foreach ($health as $h): ?>
    <div style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:12px;background:#f8fafc;">
      <div style="width:36px;height:36px;border-radius:10px;background:<?= $h['status'] ? '#f0fdf4' : '#fff5f5' ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <span class="material-symbols-outlined" style="font-size:18px;color:<?= $h['status'] ? '#16a34a' : '#dc2626' ?>;font-variation-settings:'FILL' 1;"><?= $h['icon'] ?></span>
      </div>
      <span style="flex:1;font-size:13px;font-weight:600;color:#374151;"><?= $h['label'] ?></span>
      <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:<?= $h['status'] ? '#dcfce7' : '#fee2e2' ?>;color:<?= $h['status'] ? '#15803d' : '#dc2626' ?>;"><?= $h['status'] ? 'OK' : 'Erreur' ?></span>
    </div>
    <?php endforeach; ?>
    <!-- Uptime -->
    <div style="margin-top:auto;padding:14px;border-radius:12px;background:linear-gradient(135deg,#004d99,#1565c0);color:white;text-align:center;">
      <p style="font-size:11px;opacity:.7;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Disponibilité</p>
      <p style="font-size:28px;font-weight:900;letter-spacing:-1px;">99.9<span style="font-size:16px;">%</span></p>
      <p style="font-size:11px;opacity:.6;">30 derniers jours</p>
    </div>
  </div>
</section>

<!-- Recent Activity + User Stats -->
<section class="grid grid-cols-1 lg:grid-cols-2 gap-5">

  <!-- Activity feed -->
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div style="padding:24px 28px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
      <div>
        <h3 style="font-size:16px;font-weight:800;color:#191c1e;">Activité récente</h3>
        <p style="font-size:12px;color:#6b7280;margin-top:2px;">Dernières inscriptions sur la plateforme</p>
      </div>
      <div style="width:36px;height:36px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;">
        <span class="material-symbols-outlined" style="color:#004d99;font-size:18px;">timeline</span>
      </div>
    </div>
    <div style="padding:8px 0;">
    <?php
    $acts = array_slice($data['recentActivity'] ?? [], 0, 6);
    $avatarColors = ['#004d99','#005851','#4a5f83','#7c3aed','#d97706','#0891b2'];
    foreach ($acts as $i => $act):
      $initials = strtoupper(substr($act['prenom']??'?',0,1).substr($act['nom']??'',0,1));
      $bg = $avatarColors[$i % count($avatarColors)];
      $rColor = $roleColors[$act['role_name']??''] ?? '#6b7280';
    ?>
    <div class="activity-row" style="display:flex;align-items:center;gap:14px;padding:12px 28px;">
      <div style="width:40px;height:40px;border-radius:12px;background:<?= $bg ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <span style="color:white;font-size:13px;font-weight:800;"><?= $initials ?></span>
      </div>
      <div style="flex:1;min-width:0;">
        <p style="font-size:13px;font-weight:700;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars(($act['prenom']??'').' '.($act['nom']??'')) ?></p>
        <p style="font-size:11px;color:#9ca3af;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($act['mail']??'') ?></p>
      </div>
      <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:<?= $rColor ?>18;color:<?= $rColor ?>;flex-shrink:0;"><?= htmlspecialchars($act['role_name']??'—') ?></span>
    </div>
    <?php endforeach; ?>
    <?php if (empty($acts)): ?>
      <div style="padding:40px;text-align:center;color:#9ca3af;">
        <span class="material-symbols-outlined" style="font-size:40px;display:block;margin-bottom:8px;">inbox</span>
        Aucune activité récente
      </div>
    <?php endif; ?>
    </div>
  </div>

  <!-- User stats breakdown -->
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div style="padding:24px 28px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
      <div>
        <h3 style="font-size:16px;font-weight:800;color:#191c1e;">Statistiques utilisateurs</h3>
        <p style="font-size:12px;color:#6b7280;margin-top:2px;">Métriques clés de la plateforme</p>
      </div>
      <div style="width:36px;height:36px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;">
        <span class="material-symbols-outlined" style="color:#004d99;font-size:18px;font-variation-settings:'FILL' 1;">bar_chart</span>
      </div>
    </div>
    <div style="padding:20px 28px;display:flex;flex-direction:column;gap:14px;">
    <?php
    $statRows = [
      ['label'=>'Total inscrits',      'val'=>$totalUsers,    'icon'=>'group',          'color'=>'#004d99','bg'=>'#eff6ff'],
      ['label'=>'Patients',            'val'=>$totalPatients, 'icon'=>'personal_injury', 'color'=>'#005851','bg'=>'#f0fdf4'],
      ['label'=>'Rôles définis',       'val'=>$totalRoles,    'icon'=>'shield_person',   'color'=>'#4a5f83','bg'=>'#f0f4ff'],
      ['label'=>'Admins actifs',       'val'=>count(array_filter($data['stats']['usersByRole']??[],fn($r)=>($r['role_name']??'')==='Admin')), 'icon'=>'manage_accounts','color'=>'#7c3aed','bg'=>'#f5f3ff'],
    ];
    foreach ($statRows as $s): ?>
    <div style="display:flex;align-items:center;gap:14px;padding:14px 16px;border-radius:14px;background:<?= $s['bg'] ?>;border:1px solid <?= $s['color'] ?>18;">
      <div style="width:38px;height:38px;border-radius:10px;background:<?= $s['color'] ?>22;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <span class="material-symbols-outlined" style="color:<?= $s['color'] ?>;font-size:18px;font-variation-settings:'FILL' 1;"><?= $s['icon'] ?></span>
      </div>
      <span style="flex:1;font-size:13px;font-weight:600;color:#374151;"><?= $s['label'] ?></span>
      <span style="font-size:22px;font-weight:900;color:<?= $s['color'] ?>;"><?= $s['val'] ?></span>
    </div>
    <?php endforeach; ?>
    <!-- System status -->
    <div style="display:flex;align-items:center;gap:10px;padding:14px 16px;border-radius:14px;background:linear-gradient(135deg,#004d9908,#1565c008);border:1px solid #004d9920;margin-top:4px;">
      <div style="position:relative;width:10px;height:10px;flex-shrink:0;">
        <div style="position:absolute;inset:0;border-radius:50%;background:#22c55e;animation:pulseRing 2s ease-in-out infinite;"></div>
        <div style="position:absolute;inset:0;border-radius:50%;background:#22c55e;"></div>
      </div>
      <span style="flex:1;font-size:13px;font-weight:600;color:#374151;">Statut système</span>
      <span style="font-size:13px;font-weight:800;color:#16a34a;">Opérationnel</span>
    </div>
    </div>
  </div>
</section>

<!-- Patients Table -->
<section class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
  <div style="padding:24px 28px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
    <div>
      <h3 style="font-size:16px;font-weight:800;color:#191c1e;">Patients enregistrés</h3>
      <p style="font-size:12px;color:#6b7280;margin-top:2px;">Liste complète des patients de la plateforme</p>
    </div>
    <span style="background:#f0fdf4;color:#15803d;font-size:11px;font-weight:700;padding:4px 14px;border-radius:20px;border:1px solid #bbf7d0;"><?= $totalPatients ?> patients</span>
  </div>
  <div style="overflow-x:auto;">
    <table class="admin-table" style="width:100%;border-collapse:collapse;">
      <thead>
        <tr style="background:#f8fafc;">
          <th style="padding:13px 24px;text-align:left;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#6b7280;border-bottom:1px solid #f1f5f9;">Matricule</th>
          <th style="padding:13px 24px;text-align:left;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#6b7280;border-bottom:1px solid #f1f5f9;">Patient</th>
          <th style="padding:13px 24px;text-align:left;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#6b7280;border-bottom:1px solid #f1f5f9;">Email</th>
          <th style="padding:13px 24px;text-align:left;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#6b7280;border-bottom:1px solid #f1f5f9;">Téléphone</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach (($data['patients']??[]) as $i => $p):
        $avatarBg = $avatarColors[$i % count($avatarColors)];
        $initials2 = strtoupper(substr($p['prenom']??'?',0,1).substr($p['nom']??'',0,1));
      ?>
        <tr style="border-bottom:1px solid #f8fafc;">
          <td style="padding:14px 24px;">
            <span style="font-size:12px;font-weight:700;color:#15803d;background:#f0fdf4;padding:3px 10px;border-radius:20px;border:1px solid #bbf7d0;"><?= htmlspecialchars($p['matricule']??'N/A') ?></span>
          </td>
          <td style="padding:14px 24px;">
            <div style="display:flex;align-items:center;gap:10px;">
              <div style="width:34px;height:34px;border-radius:10px;background:<?= $avatarBg ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <span style="color:white;font-size:12px;font-weight:800;"><?= $initials2 ?></span>
              </div>
              <span style="font-size:13px;font-weight:700;color:#111827;"><?= htmlspecialchars(($p['prenom']??'').' '.($p['nom']??'')) ?></span>
            </div>
          </td>
          <td style="padding:14px 24px;font-size:13px;color:#6b7280;"><?= htmlspecialchars($p['mail']??'') ?></td>
          <td style="padding:14px 24px;font-size:13px;font-weight:600;color:#374151;"><?= htmlspecialchars($p['tel']??'—') ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($data['patients'])): ?>
        <tr><td colspan="4" style="padding:48px;text-align:center;color:#9ca3af;">
          <span class="material-symbols-outlined" style="font-size:40px;display:block;margin-bottom:8px;">person_off</span>
          Aucun patient enregistré
        </td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</section>


<?php elseif ($role === 'Patient'): ?>
<!-- ═══════════════ PATIENT ═══════════════ -->
<style>
@keyframes ptFadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:none}}
.pt-card{animation:ptFadeUp .42s ease both;transition:transform .22s cubic-bezier(.34,1.56,.64,1),box-shadow .22s}
.pt-card:hover{transform:translateY(-4px);box-shadow:0 18px 40px rgba(0,77,153,.13)}
.pt-res-row{transition:background .18s,border-color .18s,transform .18s}
.pt-res-row:hover{background:#f0f6ff;border-color:#a9c7ff;transform:translateX(3px)}
</style>

<!-- Hero welcome banner -->
<section class="relative overflow-hidden rounded-2xl p-7 text-white shadow-xl pt-card" style="background:linear-gradient(135deg,#004d99,#1565c0);animation-delay:.04s">
  <div style="position:absolute;top:-40px;right:-40px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.06)"></div>
  <div style="position:absolute;bottom:-50px;left:35%;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,.04)"></div>
  <div class="flex flex-wrap items-center gap-6">
    <div style="width:60px;height:60px;border-radius:18px;background:rgba(255,255,255,.18);display:flex;align-items:center;justify-content:center;flex-shrink:0">
      <span class="material-symbols-outlined text-white" style="font-size:30px;font-variation-settings:'FILL' 1">personal_injury</span>
    </div>
    <div class="flex-1 min-w-0">
      <p style="font-size:11px;font-weight:700;opacity:.7;text-transform:uppercase;letter-spacing:.08em">Espace patient</p>
      <p style="font-size:24px;font-weight:900;margin-top:3px;letter-spacing:-.5px">Bonjour, <?= $prenom ?> 👋</p>
      <?php $hasActive = ($data['nbEnCours']??0) > 0; ?>
      <p style="font-size:13px;opacity:.8;margin-top:5px">
        <?php if($hasActive): ?>
          🔵 Vous avez <strong><?= $data['nbEnCours'] ?> location(s) en cours</strong> — suivez leur avancement ici.
        <?php else: ?>
          Bienvenue sur votre tableau de bord MediFlow. Réservez du matériel médical en quelques clics.
        <?php endif; ?>
      </p>
    </div>
    <div class="flex gap-3 flex-shrink-0">
      <a href="/integration/catalogue" style="background:rgba(255,255,255,.2);padding:11px 20px;border-radius:13px;font-size:13px;font-weight:700;color:white;text-decoration:none;transition:background .2s" onmouseover="this.style.background='rgba(255,255,255,.3)'" onmouseout="this.style.background='rgba(255,255,255,.2)'">
        <span style="vertical-align:middle;font-size:15px">🏥</span> Catalogue
      </a>
      <?php if (!($data['show_tour'] ?? false)): ?>
      <button onclick="mediflowTour.restart()" style="background:rgba(255,255,255,.1);padding:11px 20px;border-radius:13px;font-size:13px;font-weight:700;color:white;border:none;cursor:pointer;transition:background .2s" onmouseover="this.style.background='rgba(255,255,255,.2)'" onmouseout="this.style.background='rgba(255,255,255,.1)'">
        <span class="material-symbols-outlined" style="font-size:15px;vertical-align:middle">tour</span> Visite guidée
      </button>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- KPI Cards -->
<section class="grid grid-cols-2 lg:grid-cols-4 gap-5 dashboard-stats">
<?php
$pKpis = [
  ['label'=>'Total réservations','val'=>$data['nbTotal']??0,    'icon'=>'receipt_long', 'from'=>'#004d99','to'=>'#1565c0','sub'=>'Depuis votre inscription'],
  ['label'=>'En cours',          'val'=>$data['nbEnCours']??0,  'icon'=>'hourglass_top','from'=>'#b45309','to'=>'#d97706','sub'=>'Locations actives'],
  ['label'=>'Terminées',         'val'=>$data['nbTermines']??0, 'icon'=>'check_circle', 'from'=>'#15803d','to'=>'#16a34a','sub'=>'Équipements rendus'],
  ['label'=>'En retard',         'val'=>$data['nbEnRetard']??0, 'icon'=>'warning',      'from'=>'#b91c1c','to'=>'#dc2626','sub'=>'Retours en attente'],
];
foreach ($pKpis as $i => $k): ?>
<div class="pt-card rounded-2xl p-6 text-white relative overflow-hidden cursor-default" style="background:linear-gradient(135deg,<?=$k['from']?>,<?=$k['to']?>);box-shadow:0 8px 22px <?=$k['from']?>44;animation-delay:<?=$i*.07?>s">
  <div style="position:absolute;top:-16px;right:-16px;width:86px;height:86px;border-radius:50%;background:rgba(255,255,255,.09)"></div>
  <div style="width:42px;height:42px;border-radius:12px;background:rgba(255,255,255,.18);display:flex;align-items:center;justify-content:center;margin-bottom:14px">
    <span class="material-symbols-outlined text-white" style="font-size:20px;font-variation-settings:'FILL' 1"><?=$k['icon']?></span>
  </div>
  <p style="font-size:2.2rem;font-weight:900;line-height:1;letter-spacing:-1px"><?=$k['val']?></p>
  <p style="font-size:13px;font-weight:700;margin-top:6px;opacity:.95"><?=$k['label']?></p>
  <p style="font-size:11px;opacity:.6;margin-top:3px"><?=$k['sub']?></p>
</div>
<?php endforeach; ?>
</section>

<!-- Reservations + Sidebar -->
<section class="grid grid-cols-1 lg:grid-cols-3 gap-5 reservations-table">

  <!-- Reservations list -->
  <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden pt-card" style="animation-delay:.18s">
    <div style="padding:22px 26px 14px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
      <div>
        <h3 style="font-size:15px;font-weight:800;color:#191c1e">Mes dernières réservations</h3>
        <p style="font-size:12px;color:#6b7280;margin-top:2px">Suivi de vos locations d'équipements médicaux</p>
      </div>
      <a href="/integration/mes-reservations" style="font-size:12px;font-weight:700;color:#004d99;text-decoration:none;background:#eff6ff;padding:5px 13px;border-radius:20px">Voir tout →</a>
    </div>

    <?php if (empty($data['latestRes'])): ?>
    <div style="padding:48px;text-align:center;color:#9ca3af">
      <span class="material-symbols-outlined" style="font-size:44px;display:block;margin-bottom:12px;opacity:.35">medical_services</span>
      <p style="font-size:15px;font-weight:700;color:#374151">Aucune réservation pour l'instant</p>
      <p style="font-size:13px;margin-top:6px">Explorez notre catalogue et réservez votre premier équipement.</p>
      <a href="/integration/catalogue" style="display:inline-flex;align-items:center;gap:8px;margin-top:18px;padding:10px 22px;background:#004d99;color:white;border-radius:12px;font-size:13px;font-weight:700;text-decoration:none">
        <span class="material-symbols-outlined" style="font-size:16px">medical_services</span> Parcourir le catalogue
      </a>
    </div>
    <?php else: ?>
    <div style="padding:8px 16px">
    <?php
    $sMap = ['en_cours'=>['#d97706','#fffbeb','⏳ En cours'],'termine'=>['#16a34a','#f0fdf4','✅ Terminée'],'en_retard'=>['#dc2626','#fff5f5','⚠️ En retard']];
    foreach ($data['latestRes'] as $r):
      $sc = $sMap[$r['statut']??''] ?? ['#6b7280','#f9fafb', $r['statut']??'—'];
      $eqName = htmlspecialchars($r['equipement_nom']??($r['equipement_id']??'Équipement'));
      $debut  = $r['date_debut'] ? date('d M Y', strtotime($r['date_debut'])) : '—';
      $fin    = $r['date_fin']   ? date('d M Y', strtotime($r['date_fin']))   : 'En cours';
    ?>
    <div class="pt-res-row" style="display:flex;align-items:center;gap:14px;padding:14px 12px;border:1px solid transparent;border-radius:14px;margin-bottom:6px">
      <!-- Icon -->
      <div style="width:44px;height:44px;border-radius:13px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <span class="material-symbols-outlined" style="color:#004d99;font-size:20px;font-variation-settings:'FILL' 1">medical_services</span>
      </div>
      <!-- Info -->
      <div style="flex:1;min-width:0">
        <p style="font-size:13px;font-weight:800;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?=$eqName?></p>
        <p style="font-size:11px;color:#9ca3af;margin-top:3px">
          📅 <?=$debut?> → <?=$fin?>
        </p>
      </div>
      <!-- Badge -->
      <span style="font-size:11px;font-weight:700;padding:4px 11px;border-radius:20px;background:<?=$sc[1]?>;color:<?=$sc[0]?>;flex-shrink:0;white-space:nowrap"><?=$sc[2]?></span>
    </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <!-- Right sidebar: status breakdown + quick actions -->
  <div class="flex flex-col gap-4">

    <!-- Status breakdown -->
    <?php if (($data['nbTotal']??0) > 0): ?>
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 pt-card" style="animation-delay:.22s">
      <h3 style="font-size:14px;font-weight:800;color:#191c1e;margin-bottom:14px">Mes locations</h3>
      <?php
      $breakdown = [
        ['label'=>'En cours',  'val'=>$data['nbEnCours']??0,  'color'=>'#d97706','bg'=>'#fffbeb'],
        ['label'=>'Terminées', 'val'=>$data['nbTermines']??0, 'color'=>'#16a34a','bg'=>'#f0fdf4'],
        ['label'=>'En retard', 'val'=>$data['nbEnRetard']??0, 'color'=>'#dc2626','bg'=>'#fff5f5'],
      ];
      $total = max(1, $data['nbTotal']??1);
      foreach ($breakdown as $b):
        $pct = round($b['val']/$total*100);
      ?>
      <div style="margin-bottom:13px">
        <div style="display:flex;justify-content:space-between;margin-bottom:5px">
          <span style="font-size:12px;font-weight:600;color:#374151"><?=$b['label']?></span>
          <span style="font-size:12px;font-weight:800;color:<?=$b['color']?>"><?=$b['val']?></span>
        </div>
        <div style="height:7px;background:#f1f5f9;border-radius:99px;overflow:hidden">
          <div style="height:100%;width:<?=$pct?>%;border-radius:99px;background:linear-gradient(90deg,<?=$b['color']?>,<?=$b['color']?>aa);transition:width .8s ease"></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Quick actions -->
    <div class="flex flex-col gap-3 pt-card quick-actions" style="animation-delay:.27s">
      <a href="/integration/catalogue" style="display:flex;align-items:center;gap:12px;padding:14px 18px;background:linear-gradient(135deg,#004d99,#1565c0);color:white;border-radius:16px;text-decoration:none;font-weight:700;font-size:13px;box-shadow:0 5px 18px #004d9940;transition:opacity .2s" onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
        <span class="material-symbols-outlined" style="font-size:20px;font-variation-settings:'FILL' 1">medical_services</span>
        Catalogue d'équipements
      </a>
      <a href="/integration/mes-reservations" style="display:flex;align-items:center;gap:12px;padding:14px 18px;background:linear-gradient(135deg,#005851,#00736a);color:white;border-radius:16px;text-decoration:none;font-weight:700;font-size:13px;box-shadow:0 5px 18px #00585140;transition:opacity .2s" onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
        <span class="material-symbols-outlined" style="font-size:20px;font-variation-settings:'FILL' 1">receipt_long</span>
        Mes réservations
      </a>
      <a href="/integration/rdv/planning-patient" style="display:flex;align-items:center;gap:12px;padding:14px 18px;background:linear-gradient(135deg,#4a5f83,#6b7db3);color:white;border-radius:16px;text-decoration:none;font-weight:700;font-size:13px;box-shadow:0 5px 18px #4a5f8340;transition:opacity .2s" onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
        <span class="material-symbols-outlined" style="font-size:20px;font-variation-settings:'FILL' 1">calendar_month</span>
        Prendre un RDV
      </a>
    </div>
  </div>
</section>


<?php elseif ($role === 'Technicien'): ?>
<!-- ═══════════════ EQUIPMENT MANAGER ═══════════════ -->
<section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
  <?php
  $eCards = [
    ['label'=>'Total équipements','val'=>$data['totalEq']??0,'icon'=>'medical_services','color'=>'#004d99','bg'=>'#eff6ff'],
    ['label'=>'Disponibles','val'=>$data['disponibles']??0,'icon'=>'check_circle','color'=>'#16a34a','bg'=>'#f0fdf4'],
    ['label'=>'Loués','val'=>$data['loues']??0,'icon'=>'shopping_cart','color'=>'#d97706','bg'=>'#fffbeb'],
    ['label'=>'En maintenance','val'=>$data['maintenance']??0,'icon'=>'build','color'=>'#dc2626','bg'=>'#fff5f5'],
    ['label'=>'Total locations','val'=>$data['totalRes']??0,'icon'=>'receipt_long','color'=>'#7c3aed','bg'=>'#f5f3ff'],
    ['label'=>'Locations en cours','val'=>$data['resEnCours']??0,'icon'=>'hourglass_top','color'=>'#0891b2','bg'=>'#ecfeff'],
  ];
  foreach ($eCards as $c): ?>
  <div class="hover-lift p-6 rounded-2xl shadow-sm border border-outline/10" style="background:<?= $c['bg'] ?>">
    <div class="flex justify-between items-start">
      <div>
        <p class="text-sm font-semibold" style="color:<?= $c['color'] ?>;opacity:.75"><?= $c['label'] ?></p>
        <h3 class="text-4xl font-black mt-2" style="color:<?= $c['color'] ?>"><?= $c['val'] ?></h3>
      </div>
      <span class="material-symbols-outlined text-3xl" style="color:<?= $c['color'] ?>;opacity:.4"><?= $c['icon'] ?></span>
    </div>
  </div>
  <?php endforeach; ?>
</section>

<!-- Quick actions -->
<section class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <a href="/integration/equipements" class="hover-lift flex items-center gap-5 p-6 bg-gradient-to-r from-primary to-primary-container text-white rounded-2xl shadow-lg">
    <span class="material-symbols-outlined text-4xl opacity-80">medical_services</span>
    <div>
      <p class="font-black text-lg">Gérer les équipements</p>
      <p class="text-sm opacity-80">Ajouter, modifier, supprimer</p>
    </div>
  </a>
  <a href="/integration/historique-location" class="hover-lift flex items-center gap-5 p-6 bg-gradient-to-r from-slate-700 to-slate-800 text-white rounded-2xl shadow-lg">
    <span class="material-symbols-outlined text-4xl opacity-80">history</span>
    <div>
      <p class="font-black text-lg">Historique des locations</p>
      <p class="text-sm opacity-80">Toutes les réservations</p>
    </div>
  </a>
</section>

<!-- Derniers équipements -->
<section class="hover-lift bg-gradient-to-br from-surface-container-lowest to-surface-container-low p-8 rounded-xl shadow-sm border border-outline/10">
  <div class="flex justify-between items-center mb-6">
    <h3 class="text-xl font-bold">Équipements récents</h3>
    <a href="/integration/equipements" class="text-sm font-bold text-primary hover:underline">Voir tout →</a>
  </div>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <?php foreach (($data['latestEq']??[]) as $eq):
      $sc = ['disponible'=>['#16a34a','#f0fdf4','Disponible'],'loue'=>['#d97706','#fffbeb','Loué'],'maintenance'=>['#dc2626','#fff5f5','Maintenance']][$eq['statut']??''] ?? ['#6b7280','#f9fafb','—'];
    ?>
    <div class="flex items-center gap-4 p-4 bg-surface-container-low rounded-xl border border-outline/5 hover:border-primary/20 transition-all">
      <div class="w-12 h-12 rounded-xl bg-primary-fixed flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-primary">medical_services</span>
      </div>
      <div class="flex-1 min-w-0">
        <p class="font-bold text-sm truncate"><?= htmlspecialchars($eq['nom']??'') ?></p>
        <p class="text-xs text-on-surface-variant"><?= htmlspecialchars($eq['categorie']??'') ?> · <?= number_format($eq['prix_jour']??0,2) ?> DT/j</p>
      </div>
      <span class="px-3 py-1 rounded-full text-xs font-bold flex-shrink-0" style="background:<?= $sc[1] ?>;color:<?= $sc[0] ?>"><?= $sc[2] ?></span>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<?php elseif ($role === 'redacteur'): ?>
<!-- ═══════════════ MAGAZINE EDITOR ═══════════════ -->
<section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
  <?php
  $ps = $data['postStats'] ?? [];
  $cs = $data['commentStats'] ?? [];
  $mCards = [
    ['label'=>'Articles totaux',   'val'=>$ps['total_articles']??0, 'icon'=>'article',      'color'=>'#004d99', 'bg'=>'#eff6ff'],
    ['label'=>'Publiés',           'val'=>$ps['published']??0,      'icon'=>'check_circle',  'color'=>'#16a34a', 'bg'=>'#f0fdf4'],
    ['label'=>'Brouillons',        'val'=>$ps['drafts']??0,         'icon'=>'edit_note',     'color'=>'#d97706', 'bg'=>'#fffbeb'],
    ['label'=>'Vues totales',      'val'=>$ps['total_views']??0,    'icon'=>'visibility',    'color'=>'#004d99', 'bg'=>'#eff6ff'],
    ['label'=>'Likes totaux',      'val'=>$ps['total_likes']??0,    'icon'=>'favorite',      'color'=>'#dc2626', 'bg'=>'#fff5f5'],
    ['label'=>'Commentaires en attente','val'=>$cs['pending']??0,   'icon'=>'pending',       'color'=>'#7c3aed', 'bg'=>'#f5f3ff'],
  ];
  foreach ($mCards as $c): ?>
  <div class="hover-lift p-6 rounded-2xl shadow-sm border border-outline/10" style="background:<?= $c['bg'] ?>">
    <div class="flex justify-between items-start">
      <div>
        <p class="text-sm font-semibold" style="color:<?= $c['color'] ?>;opacity:.75"><?= $c['label'] ?></p>
        <h3 class="text-4xl font-black mt-2" style="color:<?= $c['color'] ?>"><?= $c['val'] ?></h3>
      </div>
      <span class="material-symbols-outlined text-3xl" style="color:<?= $c['color'] ?>;opacity:.4"><?= $c['icon'] ?></span>
    </div>
  </div>
  <?php endforeach; ?>
</section>

<!-- Quick actions -->
<section class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <a href="/integration/magazine/admin" class="hover-lift flex items-center gap-5 p-6 bg-gradient-to-r from-primary to-primary-container text-white rounded-2xl shadow-lg">
    <span class="material-symbols-outlined text-4xl opacity-80">newspaper</span>
    <div>
      <p class="font-black text-lg">Tableau de bord Magazine</p>
      <p class="text-sm opacity-80">Gérer les articles et commentaires</p>
    </div>
  </a>
  <a href="/integration/magazine/admin/article-form" class="hover-lift flex items-center gap-5 p-6 bg-gradient-to-r from-slate-700 to-slate-800 text-white rounded-2xl shadow-lg">
    <span class="material-symbols-outlined text-4xl opacity-80">edit_note</span>
    <div>
      <p class="font-black text-lg">Nouvel article</p>
      <p class="text-sm opacity-80">Rédiger et publier</p>
    </div>
  </a>
</section>

<!-- Recent articles -->
<section class="hover-lift bg-gradient-to-br from-surface-container-lowest to-surface-container-low p-8 rounded-xl shadow-sm border border-outline/10">
  <div class="flex justify-between items-center mb-6">
    <h3 class="text-xl font-bold">Articles récents</h3>
    <a href="/integration/magazine/admin/articles" class="text-sm font-bold text-primary hover:underline">Voir tout →</a>
  </div>
  <?php if (empty($data['recentPosts'])): ?>
    <div class="text-center py-14">
      <span class="material-symbols-outlined text-5xl text-slate-300 block mb-3">article</span>
      <p class="font-semibold text-on-surface-variant">Aucun article publié pour l'instant.</p>
      <a href="/integration/magazine/admin/article-form" class="mt-4 inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl font-bold text-sm">
        <span class="material-symbols-outlined text-base">add</span> Créer un article
      </a>
    </div>
  <?php else: ?>
  <div class="space-y-4">
    <?php foreach (($data['recentPosts']??[]) as $post): ?>
    <div class="flex items-center justify-between p-4 bg-surface-container-low rounded-xl border border-outline/5 hover:border-primary/20 transition-all">
      <div class="flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-primary-fixed flex items-center justify-center flex-shrink-0">
          <span class="material-symbols-outlined text-primary">article</span>
        </div>
        <div>
          <p class="font-bold text-sm"><?= htmlspecialchars($post['titre']??'') ?></p>
          <p class="text-xs text-on-surface-variant"><?= htmlspecialchars($post['categorie']??'') ?> · <?= $post['views_count']??0 ?> vues</p>
        </div>
      </div>
      <a href="/integration/magazine/admin/article-form?id=<?= $post['id'] ?>" class="text-primary hover:underline text-xs font-bold">Modifier</a>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</section>

<?php elseif ($role === 'Medecin'): ?>
<!-- ═══════════════ MÉDECIN ═══════════════ -->
<style>
@keyframes mdFadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:none} }
@keyframes mdPulse  { 0%,100%{transform:scale(1);opacity:.7} 50%{transform:scale(1.5);opacity:0} }
.md-card { animation: mdFadeUp .45s ease both; transition: transform .22s cubic-bezier(.34,1.56,.64,1),box-shadow .22s ease; }
.md-card:hover { transform:translateY(-4px); box-shadow:0 18px 40px rgba(0,77,153,.15); }
.md-pulse { animation: mdPulse 2s ease-in-out infinite; }
.md-rdv-row { transition: background .18s,border-color .18s; }
.md-rdv-row:hover { background:#f0f6ff; border-color:#a9c7ff; }
</style>

<!-- Next appointment hero banner -->
<?php if ($data['nextRdv']): $nr = $data['nextRdv'];
  $isToday = ($nr['date_rdv'] ?? '') === date('Y-m-d');
  $banner = $isToday ? ['Aujourd\'hui à','from-primary to-primary-container','#d6e3ff'] : ['Prochain RDV le','from-slate-700 to-slate-800','#c0d5ff'];
?>
<section class="relative overflow-hidden rounded-2xl p-7 text-white bg-gradient-to-r <?= $banner[0]==='Aujourd\'hui à'?'from-primary to-primary-container':'from-slate-700 to-slate-800' ?> shadow-xl md-card" style="animation-delay:.05s">
  <div style="position:absolute;top:-30px;right:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.07);pointer-events:none"></div>
  <div style="position:absolute;bottom:-40px;left:40%;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.04);pointer-events:none"></div>
  <div class="flex flex-wrap items-center gap-6">
    <div style="width:56px;height:56px;border-radius:16px;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0">
      <span class="material-symbols-outlined text-white" style="font-size:28px;font-variation-settings:'FILL' 1">event_available</span>
    </div>
    <div class="flex-1 min-w-0">
      <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;opacity:.7"><?= $banner[0] ?></p>
      <p style="font-size:22px;font-weight:900;letter-spacing:-.5px;margin-top:2px"><?= htmlspecialchars($nr['patient_prenom'].' '.$nr['patient_nom']) ?></p>
      <p style="font-size:13px;opacity:.75;margin-top:4px">
        <?= $isToday ? '🕐 '.htmlspecialchars($nr['heure_rdv']??'') : '📅 '.date('d M Y',strtotime($nr['date_rdv'])).' · '.htmlspecialchars($nr['heure_rdv']??'') ?>
        &nbsp;·&nbsp; CIN&nbsp;<strong><?= htmlspecialchars($nr['cin']??'—') ?></strong>
      </p>
    </div>
    <div class="flex gap-3 flex-shrink-0">
      <a href="/integration/rdv/dashboard" style="background:rgba(255,255,255,.18);padding:10px 20px;border-radius:12px;font-size:13px;font-weight:700;color:white;text-decoration:none;transition:background .2s" onmouseover="this.style.background='rgba(255,255,255,.28)'" onmouseout="this.style.background='rgba(255,255,255,.18)'">
        Voir mes RDV →
      </a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- KPI Cards -->
<section class="grid grid-cols-2 lg:grid-cols-4 gap-5">
<?php
$mdKpis = [
  ['label'=>'Total RDV',        'val'=>$data['totalRdv']??0,        'icon'=>'calendar_month',  'from'=>'#004d99','to'=>'#1565c0', 'sub'=>'Depuis votre inscription'],
  ['label'=>'Aujourd\'hui',     'val'=>$data['rdvAujourdhui']??0,   'icon'=>'today',            'from'=>'#005851','to'=>'#00736a', 'sub'=>'Rendez-vous du jour'],
  ['label'=>'En attente',       'val'=>$data['rdvEnAttente']??0,    'icon'=>'hourglass_top',    'from'=>'#b45309','to'=>'#d97706', 'sub'=>'Confirmation requise'],
  ['label'=>'Patients suivis',  'val'=>$data['patientsUniques']??0, 'icon'=>'group',            'from'=>'#4a5f83','to'=>'#6b7db3', 'sub'=>'Patients distincts (CIN)'],
];
foreach ($mdKpis as $i=>$k): ?>
<div class="md-card rounded-2xl p-6 text-white relative overflow-hidden cursor-default" style="background:linear-gradient(135deg,<?=$k['from']?>,<?=$k['to']?>);box-shadow:0 8px 24px <?=$k['from']?>44;animation-delay:<?=$i*.07?>s">
  <div style="position:absolute;top:-18px;right:-18px;width:90px;height:90px;border-radius:50%;background:rgba(255,255,255,.09)"></div>
  <div style="width:42px;height:42px;border-radius:12px;background:rgba(255,255,255,.18);display:flex;align-items:center;justify-content:center;margin-bottom:16px">
    <span class="material-symbols-outlined text-white" style="font-size:20px;font-variation-settings:'FILL' 1"><?=$k['icon']?></span>
  </div>
  <p style="font-size:2.2rem;font-weight:900;line-height:1;letter-spacing:-1px"><?=$k['val']?></p>
  <p style="font-size:13px;font-weight:700;margin-top:6px;opacity:.95"><?=$k['label']?></p>
  <p style="font-size:11px;opacity:.6;margin-top:3px"><?=$k['sub']?></p>
</div>
<?php endforeach; ?>
</section>

<!-- Upcoming this week + Quick stats -->
<section class="grid grid-cols-1 lg:grid-cols-3 gap-5">

  <!-- Upcoming RDVs -->
  <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden md-card" style="animation-delay:.18s">
    <div style="padding:22px 28px 14px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
      <div>
        <h3 style="font-size:15px;font-weight:800;color:#191c1e">Cette semaine</h3>
        <p style="font-size:12px;color:#6b7280;margin-top:2px">Rendez-vous des 7 prochains jours</p>
      </div>
      <a href="/integration/rdv/planning" style="font-size:12px;font-weight:700;color:#004d99;text-decoration:none;background:#eff6ff;padding:6px 14px;border-radius:20px">Planning →</a>
    </div>
    <div style="padding:8px 0">
    <?php
    $upcoming = $data['rdvUpcoming'] ?? [];
    usort($upcoming, fn($a,$b)=>strcmp($a['date_rdv'].$a['heure_rdv'], $b['date_rdv'].$b['heure_rdv']));
    $statusMap = ['en_attente'=>['#d97706','#fffbeb','En attente'],'confirme'=>['#16a34a','#f0fdf4','Confirmé'],'annule'=>['#dc2626','#fff5f5','Annulé']];
    if (empty($upcoming)): ?>
      <div style="padding:40px;text-align:center;color:#9ca3af">
        <span class="material-symbols-outlined" style="font-size:38px;display:block;margin-bottom:8px;opacity:.4">event_busy</span>
        <p style="font-size:14px;font-weight:600">Aucun RDV cette semaine</p>
        <a href="/integration/rdv/dashboard" style="display:inline-flex;align-items:center;gap:6px;margin-top:14px;padding:8px 18px;background:#004d99;color:white;border-radius:10px;font-size:13px;font-weight:700;text-decoration:none">Voir tous mes RDV</a>
      </div>
    <?php else: foreach (array_slice($upcoming,0,6) as $rdv):
      $sc = $statusMap[$rdv['statut']??''] ?? ['#6b7280','#f9fafb','—'];
      $isToday2 = ($rdv['date_rdv']??'') === date('Y-m-d');
    ?>
      <div class="md-rdv-row" style="display:flex;align-items:center;gap:14px;padding:13px 28px;border:1px solid transparent;border-radius:0;margin:0 8px;margin-bottom:4px;border-radius:12px">
        <!-- Date badge -->
        <div style="width:48px;flex-shrink:0;text-align:center;background:<?= $isToday2?'#eff6ff':'#f8fafc' ?>;border-radius:12px;padding:8px 4px;border:1px solid <?= $isToday2?'#a9c7ff':'#e5e7eb' ?>">
          <p style="font-size:16px;font-weight:900;color:<?= $isToday2?'#004d99':'#374151' ?>;line-height:1"><?= date('d',strtotime($rdv['date_rdv'])) ?></p>
          <p style="font-size:9px;font-weight:700;color:<?= $isToday2?'#004d99':'#6b7280' ?>;text-transform:uppercase"><?= date('M',strtotime($rdv['date_rdv'])) ?></p>
        </div>
        <!-- Time -->
        <div style="flex-shrink:0;text-align:center;min-width:44px">
          <p style="font-size:13px;font-weight:800;color:#374151"><?= htmlspecialchars($rdv['heure_rdv']??'—') ?></p>
        </div>
        <!-- Patient info -->
        <div style="flex:1;min-width:0">
          <p style="font-size:13px;font-weight:700;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
            <?= htmlspecialchars(($rdv['patient_prenom']??'').' '.($rdv['patient_nom']??'')) ?>
          </p>
          <p style="font-size:11px;color:#9ca3af;margin-top:2px">CIN: <?= htmlspecialchars($rdv['cin']??'—') ?> · <?= htmlspecialchars($rdv['genre']??'') ?></p>
        </div>
        <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:<?=$sc[1]?>;color:<?=$sc[0]?>;flex-shrink:0"><?=$sc[2]?></span>
      </div>
    <?php endforeach; endif; ?>
    </div>
  </div>

  <!-- Right panel: stats + quick actions -->
  <div class="flex flex-col gap-4">

    <!-- Status breakdown -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 md-card" style="animation-delay:.22s">
      <h3 style="font-size:14px;font-weight:800;color:#191c1e;margin-bottom:14px">Statut des RDV</h3>
      <?php
      $breakdown = [
        ['label'=>'Confirmés',  'val'=>$data['rdvConfirmes']??0, 'color'=>'#16a34a','bg'=>'#f0fdf4'],
        ['label'=>'En attente', 'val'=>$data['rdvEnAttente']??0, 'color'=>'#d97706','bg'=>'#fffbeb'],
        ['label'=>'Annulés',    'val'=>$data['rdvAnnules']??0,   'color'=>'#dc2626','bg'=>'#fff5f5'],
      ];
      $total = max(1, ($data['totalRdv']??1));
      foreach ($breakdown as $b):
        $pct = round($b['val']/$total*100);
      ?>
      <div style="margin-bottom:12px">
        <div style="display:flex;justify-content:space-between;margin-bottom:5px">
          <span style="font-size:12px;font-weight:600;color:#374151"><?=$b['label']?></span>
          <span style="font-size:12px;font-weight:800;color:<?=$b['color']?>"><?=$b['val']?></span>
        </div>
        <div style="height:7px;background:#f1f5f9;border-radius:99px;overflow:hidden">
          <div style="height:100%;width:<?=$pct?>%;border-radius:99px;background:linear-gradient(90deg,<?=$b['color']?>,<?=$b['color']?>aa);transition:width .8s ease"></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Quick actions -->
    <div class="flex flex-col gap-3 md-card" style="animation-delay:.28s">
      <a href="/integration/rdv/dashboard" style="display:flex;align-items:center;gap:12px;padding:14px 18px;background:linear-gradient(135deg,#004d99,#1565c0);color:white;border-radius:16px;text-decoration:none;font-weight:700;font-size:13px;transition:opacity .2s;box-shadow:0 6px 20px #004d9940" onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
        <span class="material-symbols-outlined" style="font-size:20px;font-variation-settings:'FILL' 1">calendar_month</span>
        Mes Rendez-vous
      </a>
      <a href="/integration/rdv/planning" style="display:flex;align-items:center;gap:12px;padding:14px 18px;background:linear-gradient(135deg,#005851,#00736a);color:white;border-radius:16px;text-decoration:none;font-weight:700;font-size:13px;transition:opacity .2s;box-shadow:0 6px 20px #00585140" onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
        <span class="material-symbols-outlined" style="font-size:20px;font-variation-settings:'FILL' 1">event_note</span>
        Mon Planning
      </a>
      <a href="/integration/dossier/patients" style="display:flex;align-items:center;gap:12px;padding:14px 18px;background:linear-gradient(135deg,#4a5f83,#6b7db3);color:white;border-radius:16px;text-decoration:none;font-weight:700;font-size:13px;transition:opacity .2s;box-shadow:0 6px 20px #4a5f8340" onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
        <span class="material-symbols-outlined" style="font-size:20px;font-variation-settings:'FILL' 1">folder_shared</span>
        Dossiers médicaux
      </a>
    </div>
  </div>
</section>

<!-- Recent past appointments -->
<?php if (!empty($data['recentRdvs'])): ?>
<section class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden md-card" style="animation-delay:.32s">
  <div style="padding:20px 28px 14px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
    <div>
      <h3 style="font-size:15px;font-weight:800;color:#191c1e">Consultations récentes</h3>
      <p style="font-size:12px;color:#6b7280;margin-top:2px">Derniers patients consultés</p>
    </div>
    <a href="/integration/rdv/dashboard" style="font-size:12px;font-weight:700;color:#004d99;text-decoration:none">Voir tout →</a>
  </div>
  <div style="overflow-x:auto">
    <table style="width:100%;border-collapse:collapse">
      <thead>
        <tr style="background:#f8fafc">
          <th style="padding:11px 24px;text-align:left;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#6b7280;border-bottom:1px solid #f1f5f9">Patient</th>
          <th style="padding:11px 24px;text-align:left;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#6b7280;border-bottom:1px solid #f1f5f9">CIN</th>
          <th style="padding:11px 24px;text-align:left;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#6b7280;border-bottom:1px solid #f1f5f9">Date</th>
          <th style="padding:11px 24px;text-align:left;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#6b7280;border-bottom:1px solid #f1f5f9">Heure</th>
          <th style="padding:11px 24px;text-align:left;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#6b7280;border-bottom:1px solid #f1f5f9">Statut</th>
        </tr>
      </thead>
      <tbody>
      <?php $avatarColors2=['#004d99','#005851','#4a5f83','#7c3aed','#d97706'];
      foreach ($data['recentRdvs'] as $i=>$rdv):
        $sc2 = $statusMap[$rdv['statut']??''] ?? ['#6b7280','#f9fafb','—'];
        $initials3 = strtoupper(substr($rdv['patient_prenom']??'?',0,1).substr($rdv['patient_nom']??'',0,1));
      ?>
        <tr style="border-bottom:1px solid #f8fafc;transition:background .15s" onmouseover="this.style.background='#f0f5ff'" onmouseout="this.style.background=''">
          <td style="padding:13px 24px">
            <div style="display:flex;align-items:center;gap:10px">
              <div style="width:34px;height:34px;border-radius:10px;background:<?=$avatarColors2[$i%5]?>;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <span style="color:white;font-size:12px;font-weight:800"><?=$initials3?></span>
              </div>
              <span style="font-size:13px;font-weight:700;color:#111827"><?= htmlspecialchars(($rdv['patient_prenom']??'').' '.($rdv['patient_nom']??'')) ?></span>
            </div>
          </td>
          <td style="padding:13px 24px;font-size:12px;font-weight:700;color:#6b7280"><?= htmlspecialchars($rdv['cin']??'—') ?></td>
          <td style="padding:13px 24px;font-size:13px;color:#374151;font-weight:600"><?= date('d M Y',strtotime($rdv['date_rdv'])) ?></td>
          <td style="padding:13px 24px;font-size:13px;color:#374151;font-weight:600"><?= htmlspecialchars($rdv['heure_rdv']??'—') ?></td>
          <td style="padding:13px 24px"><span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:<?=$sc2[1]?>;color:<?=$sc2[0]?>"><?=$sc2[2]?></span></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
<?php endif; ?>

<?php elseif ($role === 'Fournisseur'): ?>
<!-- ═══════ FOURNISSEUR ═══════ -->
<style>
@keyframes frFadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
.fr-card{animation:frFadeUp .4s ease both;transition:transform .2s cubic-bezier(.34,1.56,.64,1),box-shadow .2s}
.fr-card:hover{transform:translateY(-4px);box-shadow:0 16px 36px rgba(220,38,38,.12)}
.fr-row:hover{background:#fff5f5}
</style>

<!-- Hero banner -->
<section class="relative overflow-hidden rounded-2xl p-7 text-white shadow-xl fr-card" style="background:linear-gradient(135deg,#dc2626,#b91c1c);animation-delay:.04s">
  <div style="position:absolute;top:-30px;right:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.07)"></div>
  <div class="flex flex-wrap items-center gap-6">
    <div style="width:56px;height:56px;border-radius:16px;background:rgba(255,255,255,.18);display:flex;align-items:center;justify-content:center;flex-shrink:0">
      <span class="material-symbols-outlined text-white" style="font-size:28px;font-variation-settings:'FILL' 1">local_shipping</span>
    </div>
    <div class="flex-1">
      <p style="font-size:11px;font-weight:700;opacity:.7;text-transform:uppercase;letter-spacing:.08em">Tableau de bord Fournisseur</p>
      <p style="font-size:22px;font-weight:900;margin-top:4px">Bienvenue, <?= $prenom ?> 👋</p>
      <p style="font-size:13px;opacity:.75;margin-top:3px">Gérez votre catalogue et suivez vos commandes en temps réel.</p>
    </div>
    <div class="flex gap-3 flex-shrink-0">
      <a href="/integration/fournisseur/products/create" style="background:rgba(255,255,255,.18);padding:10px 18px;border-radius:12px;font-size:13px;font-weight:700;color:white;text-decoration:none">+ Nouveau produit</a>
      <a href="/integration/fournisseur/orders" style="background:rgba(255,255,255,.1);padding:10px 18px;border-radius:12px;font-size:13px;font-weight:700;color:white;text-decoration:none">Commandes →</a>
    </div>
  </div>
</section>

<!-- KPI Cards -->
<section class="grid grid-cols-2 lg:grid-cols-4 gap-5">
<?php $frKpis=[
  ['label'=>'Mes produits',       'val'=>$data['totalProduits']??0,      'icon'=>'inventory_2',    'from'=>'#dc2626','to'=>'#b91c1c','sub'=>'Articles dans le catalogue'],
  ['label'=>'Commandes reçues',   'val'=>$data['totalCommandes']??0,     'icon'=>'receipt_long',   'from'=>'#7c3aed','to'=>'#6d28d9','sub'=>'Total commandes'],
  ['label'=>'En attente',         'val'=>$data['commandesEnAttente']??0, 'icon'=>'hourglass_top',  'from'=>'#d97706','to'=>'#b45309','sub'=>'À confirmer'],
  ['label'=>'Stocks critiques',   'val'=>$data['stocksCritiques']??0,    'icon'=>'warning',        'from'=>'#0891b2','to'=>'#0e7490','sub'=>'Produits en rupture'],
];
foreach($frKpis as $i=>$k): ?>
<div class="fr-card rounded-2xl p-6 text-white relative overflow-hidden" style="background:linear-gradient(135deg,<?=$k['from']?>,<?=$k['to']?>);box-shadow:0 8px 20px <?=$k['from']?>44;animation-delay:<?=$i*.07?>s">
  <div style="position:absolute;top:-16px;right:-16px;width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,.09)"></div>
  <div style="width:40px;height:40px;border-radius:11px;background:rgba(255,255,255,.18);display:flex;align-items:center;justify-content:center;margin-bottom:14px">
    <span class="material-symbols-outlined text-white" style="font-size:19px;font-variation-settings:'FILL' 1"><?=$k['icon']?></span>
  </div>
  <p style="font-size:2.1rem;font-weight:900;line-height:1;letter-spacing:-1px"><?=$k['val']?></p>
  <p style="font-size:13px;font-weight:700;margin-top:5px;opacity:.95"><?=$k['label']?></p>
  <p style="font-size:11px;opacity:.6;margin-top:2px"><?=$k['sub']?></p>
</div>
<?php endforeach; ?>
</section>

<!-- Recent Products + Quick Actions -->
<section class="grid grid-cols-1 lg:grid-cols-3 gap-5">
  <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden fr-card" style="animation-delay:.2s">
    <div style="padding:20px 26px 13px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
      <div><h3 style="font-size:15px;font-weight:800;color:#191c1e">Mes produits récents</h3>
      <p style="font-size:12px;color:#6b7280;margin-top:2px">Derniers articles du catalogue</p></div>
      <a href="/integration/fournisseur/products" style="font-size:12px;font-weight:700;color:#dc2626;text-decoration:none;background:#fff5f5;padding:5px 13px;border-radius:20px">Voir tout →</a>
    </div>
    <div style="overflow-x:auto"><table style="width:100%;border-collapse:collapse">
      <thead><tr style="background:#f8fafc">
        <?php foreach(['Référence','Produit','Catégorie','Prix','Stock'] as $h): ?>
        <th style="padding:10px 20px;text-align:left;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#6b7280;border-bottom:1px solid #f1f5f9"><?=$h?></th>
        <?php endforeach; ?>
      </tr></thead>
      <tbody>
      <?php foreach(array_slice($data['recentProducts']??[],0,6) as $i=>$p):
        $lowS = ($p['quantite_stock']??$p['stock']??999) < 10;
        $avatarC=['#dc2626','#7c3aed','#d97706','#0891b2','#16a34a'];
        $init = strtoupper(substr($p['nom']??'?',0,2));
      ?>
      <tr class="fr-row" style="border-bottom:1px solid #f8fafc;transition:background .15s">
        <td style="padding:12px 20px"><span style="font-size:11px;font-weight:700;color:#dc2626;background:#fff5f5;padding:3px 9px;border-radius:20px"><?=htmlspecialchars($p['reference']??$p['ref']??'—')?></span></td>
        <td style="padding:12px 20px"><div style="display:flex;align-items:center;gap:9px">
          <div style="width:32px;height:32px;border-radius:9px;background:<?=$avatarC[$i%5]?>;display:flex;align-items:center;justify-content:center;flex-shrink:0"><span style="color:white;font-size:11px;font-weight:800"><?=$init?></span></div>
          <span style="font-size:13px;font-weight:700;color:#111827"><?=htmlspecialchars($p['nom']??'—')?></span>
        </div></td>
        <td style="padding:12px 20px;font-size:12px;color:#6b7280"><?=htmlspecialchars($p['categorie']??'—')?></td>
        <td style="padding:12px 20px;font-size:13px;font-weight:700;color:#374151"><?=number_format($p['prix']??$p['prix_unitaire']??0,2)?> DT</td>
        <td style="padding:12px 20px"><span style="font-size:11px;font-weight:700;padding:3px 9px;border-radius:20px;background:<?=$lowS?'#fff5f5':'#f0fdf4'?>;color:<?=$lowS?'#dc2626':'#16a34a'?>"><?=$p['quantite_stock']??$p['stock']??'—'?></span></td>
      </tr>
      <?php endforeach; ?>
      <?php if(empty($data['recentProducts'])): ?>
      <tr><td colspan="5" style="padding:36px;text-align:center;color:#9ca3af">
        <span class="material-symbols-outlined" style="font-size:36px;display:block;opacity:.4;margin-bottom:8px">inventory_2</span>
        Aucun produit — <a href="/integration/fournisseur/products/create" style="color:#dc2626;font-weight:700">Ajouter un produit</a>
      </td></tr>
      <?php endif; ?>
      </tbody>
    </table></div>
  </div>

  <!-- Quick actions + order info -->
  <div class="flex flex-col gap-4">
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 fr-card" style="animation-delay:.24s">
      <h3 style="font-size:14px;font-weight:800;color:#191c1e;margin-bottom:14px">Commandes</h3>
      <?php $frOrders=[
        ['label'=>'En attente','val'=>$data['commandesEnAttente']??0,'c'=>'#d97706','bg'=>'#fffbeb'],
        ['label'=>'Total','val'=>$data['totalCommandes']??0,'c'=>'#dc2626','bg'=>'#fff5f5'],
      ];
      foreach($frOrders as $o): ?>
      <div style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:12px;background:<?=$o['bg']?>;margin-bottom:10px">
        <div style="flex:1;font-size:13px;font-weight:600;color:#374151"><?=$o['label']?></div>
        <span style="font-size:22px;font-weight:900;color:<?=$o['c']?>"><?=$o['val']?></span>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="flex flex-col gap-3 fr-card" style="animation-delay:.29s">
      <a href="/integration/fournisseur/products/create" style="display:flex;align-items:center;gap:11px;padding:13px 16px;background:linear-gradient(135deg,#dc2626,#b91c1c);color:white;border-radius:14px;text-decoration:none;font-weight:700;font-size:13px;box-shadow:0 5px 18px #dc262640">
        <span class="material-symbols-outlined" style="font-size:19px;font-variation-settings:'FILL' 1">add_box</span>Ajouter un produit
      </a>
      <a href="/integration/fournisseur/products" style="display:flex;align-items:center;gap:11px;padding:13px 16px;background:linear-gradient(135deg,#7c3aed,#6d28d9);color:white;border-radius:14px;text-decoration:none;font-weight:700;font-size:13px;box-shadow:0 5px 18px #7c3aed40">
        <span class="material-symbols-outlined" style="font-size:19px;font-variation-settings:'FILL' 1">inventory_2</span>Mon catalogue
      </a>
      <a href="/integration/fournisseur/orders" style="display:flex;align-items:center;gap:11px;padding:13px 16px;background:linear-gradient(135deg,#d97706,#b45309);color:white;border-radius:14px;text-decoration:none;font-weight:700;font-size:13px;box-shadow:0 5px 18px #d9770640">
        <span class="material-symbols-outlined" style="font-size:19px;font-variation-settings:'FILL' 1">receipt_long</span>Commandes reçues
      </a>
    </div>
  </div>
</section>

<?php elseif ($role === 'pharmacien'): ?>
<!-- ═══════ PHARMACIEN ═══════ -->
<style>
@keyframes phFadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
.ph-card{animation:phFadeUp .4s ease both;transition:transform .2s cubic-bezier(.34,1.56,.64,1),box-shadow .2s}
.ph-card:hover{transform:translateY(-4px);box-shadow:0 16px 36px rgba(8,145,178,.14)}
.ph-row:hover{background:#ecfeff}
</style>

<!-- Hero banner -->
<section class="relative overflow-hidden rounded-2xl p-7 text-white shadow-xl ph-card" style="background:linear-gradient(135deg,#0891b2,#0e7490);animation-delay:.04s">
  <div style="position:absolute;top:-30px;right:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.07)"></div>
  <div class="flex flex-wrap items-center gap-6">
    <div style="width:56px;height:56px;border-radius:16px;background:rgba(255,255,255,.18);display:flex;align-items:center;justify-content:center;flex-shrink:0">
      <span class="material-symbols-outlined text-white" style="font-size:28px;font-variation-settings:'FILL' 1">medication</span>
    </div>
    <div class="flex-1">
      <p style="font-size:11px;font-weight:700;opacity:.7;text-transform:uppercase;letter-spacing:.08em">Tableau de bord Pharmacien</p>
      <p style="font-size:22px;font-weight:900;margin-top:4px">Bienvenue, <?= $prenom ?> 👋</p>
      <?php if(($data['stocksCritiques']??0)>0): ?>
      <p style="font-size:13px;margin-top:3px;background:rgba(255,255,255,.15);display:inline-block;padding:4px 12px;border-radius:20px;font-weight:700">
        ⚠️ <?=$data['stocksCritiques']?> produit(s) en rupture de stock
      </p>
      <?php else: ?>
      <p style="font-size:13px;opacity:.75;margin-top:3px">Tous les stocks sont en bon état. ✅</p>
      <?php endif; ?>
    </div>
    <div class="flex gap-3 flex-shrink-0">
      <a href="/integration/stock/products" style="background:rgba(255,255,255,.18);padding:10px 18px;border-radius:12px;font-size:13px;font-weight:700;color:white;text-decoration:none">Gérer stocks</a>
      <a href="/integration/stock/orders" style="background:rgba(255,255,255,.1);padding:10px 18px;border-radius:12px;font-size:13px;font-weight:700;color:white;text-decoration:none">Commandes →</a>
    </div>
  </div>
</section>

<!-- KPI Cards -->
<section class="grid grid-cols-2 lg:grid-cols-4 gap-5">
<?php $phKpis=[
  ['label'=>'Produits en stock', 'val'=>$data['totalProduits']??0,      'icon'=>'medication',    'from'=>'#0891b2','to'=>'#0e7490','sub'=>'Références disponibles'],
  ['label'=>'Commandes',         'val'=>$data['totalCommandes']??0,     'icon'=>'receipt_long',  'from'=>'#7c3aed','to'=>'#6d28d9','sub'=>'Total commandes passées'],
  ['label'=>'En attente',        'val'=>$data['commandesEnAttente']??0, 'icon'=>'pending',       'from'=>'#d97706','to'=>'#b45309','sub'=>'À valider'],
  ['label'=>'Stocks critiques',  'val'=>$data['stocksCritiques']??0,    'icon'=>'warning',       'from'=>($data['stocksCritiques']??0)>0?'#dc2626':'#16a34a','to'=>($data['stocksCritiques']??0)>0?'#b91c1c':'#15803d','sub'=>'Ruptures détectées'],
];
foreach($phKpis as $i=>$k): ?>
<div class="ph-card rounded-2xl p-6 text-white relative overflow-hidden" style="background:linear-gradient(135deg,<?=$k['from']?>,<?=$k['to']?>);box-shadow:0 8px 20px <?=$k['from']?>44;animation-delay:<?=$i*.07?>s">
  <div style="position:absolute;top:-16px;right:-16px;width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,.09)"></div>
  <div style="width:40px;height:40px;border-radius:11px;background:rgba(255,255,255,.18);display:flex;align-items:center;justify-content:center;margin-bottom:14px">
    <span class="material-symbols-outlined text-white" style="font-size:19px;font-variation-settings:'FILL' 1"><?=$k['icon']?></span>
  </div>
  <p style="font-size:2.1rem;font-weight:900;line-height:1;letter-spacing:-1px"><?=$k['val']?></p>
  <p style="font-size:13px;font-weight:700;margin-top:5px;opacity:.95"><?=$k['label']?></p>
  <p style="font-size:11px;opacity:.6;margin-top:2px"><?=$k['sub']?></p>
</div>
<?php endforeach; ?>
</section>

<!-- Low Stock Alerts + Orders Summary + Quick Actions -->
<section class="grid grid-cols-1 lg:grid-cols-3 gap-5">

  <!-- Low stock table -->
  <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden ph-card" style="animation-delay:.18s">
    <div style="padding:20px 26px 13px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
      <div><h3 style="font-size:15px;font-weight:800;color:#191c1e">Alertes stock bas</h3>
      <p style="font-size:12px;color:#6b7280;margin-top:2px">Produits nécessitant un réapprovisionnement</p></div>
      <a href="/integration/stock/products" style="font-size:12px;font-weight:700;color:#0891b2;text-decoration:none;background:#ecfeff;padding:5px 13px;border-radius:20px">Tous les produits →</a>
    </div>
    <?php $lowItems = $data['lowStockItems']??[]; ?>
    <?php if(empty($lowItems)): ?>
    <div style="padding:40px;text-align:center;color:#9ca3af">
      <span class="material-symbols-outlined" style="font-size:40px;display:block;margin-bottom:8px;color:#16a34a;opacity:.6">check_circle</span>
      <p style="font-size:14px;font-weight:600;color:#16a34a">Tous les stocks sont suffisants !</p>
    </div>
    <?php else: ?>
    <div style="overflow-x:auto"><table style="width:100%;border-collapse:collapse">
      <thead><tr style="background:#f8fafc">
        <?php foreach(['Produit','Catégorie','Stock actuel','Seuil','Statut'] as $h): ?>
        <th style="padding:10px 20px;text-align:left;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#6b7280;border-bottom:1px solid #f1f5f9"><?=$h?></th>
        <?php endforeach; ?>
      </tr></thead>
      <tbody>
      <?php foreach($lowItems as $i=>$p):
        $stock = (int)($p['quantite_stock']??$p['stock']??0);
        $seuil = (int)($p['seuil_alerte']??$p['seuil']??10);
        $pct   = $seuil > 0 ? min(100, round($stock/$seuil*100)) : 0;
        $color = $pct < 30 ? '#dc2626' : ($pct < 70 ? '#d97706' : '#16a34a');
      ?>
      <tr class="ph-row" style="border-bottom:1px solid #f8fafc;transition:background .15s">
        <td style="padding:12px 20px;font-size:13px;font-weight:700;color:#111827"><?=htmlspecialchars($p['nom']??'—')?></td>
        <td style="padding:12px 20px;font-size:12px;color:#6b7280"><?=htmlspecialchars($p['categorie']??'—')?></td>
        <td style="padding:12px 20px">
          <span style="font-size:14px;font-weight:900;color:<?=$color?>"><?=$stock?></span>
          <div style="height:4px;background:#f1f5f9;border-radius:99px;margin-top:5px;width:70px;overflow:hidden">
            <div style="height:100%;width:<?=$pct?>%;background:<?=$color?>;border-radius:99px"></div>
          </div>
        </td>
        <td style="padding:12px 20px;font-size:13px;font-weight:600;color:#6b7280"><?=$seuil?></td>
        <td style="padding:12px 20px"><span style="font-size:11px;font-weight:700;padding:3px 9px;border-radius:20px;background:<?=$pct<30?'#fff5f5':'#fffbeb'?>;color:<?=$color?>"><?=$pct<30?'Rupture':'Stock bas'?></span></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
    <?php endif; ?>
  </div>

  <!-- Right: order breakdown + quick actions -->
  <div class="flex flex-col gap-4">
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 ph-card" style="animation-delay:.22s">
      <h3 style="font-size:14px;font-weight:800;color:#191c1e;margin-bottom:14px">Résumé commandes</h3>
      <?php $phOrders=[
        ['label'=>'En attente',  'val'=>$data['commandesEnAttente']??0, 'c'=>'#d97706','bg'=>'#fffbeb'],
        ['label'=>'Validées',    'val'=>$data['commandesValidees']??0,  'c'=>'#16a34a','bg'=>'#f0fdf4'],
        ['label'=>'Total',       'val'=>$data['totalCommandes']??0,     'c'=>'#0891b2','bg'=>'#ecfeff'],
      ];
      $totCmd = max(1,$data['totalCommandes']??1);
      foreach($phOrders as $o):
        $pctO = round($o['val']/$totCmd*100);
      ?>
      <div style="margin-bottom:13px">
        <div style="display:flex;justify-content:space-between;margin-bottom:4px">
          <span style="font-size:12px;font-weight:600;color:#374151"><?=$o['label']?></span>
          <span style="font-size:12px;font-weight:800;color:<?=$o['c']?>"><?=$o['val']?></span>
        </div>
        <div style="height:6px;background:#f1f5f9;border-radius:99px;overflow:hidden">
          <div style="height:100%;width:<?=$pctO?>%;background:<?=$o['c']?>;border-radius:99px;transition:width .8s ease"></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="flex flex-col gap-3 ph-card" style="animation-delay:.27s">
      <a href="/integration/stock/products" style="display:flex;align-items:center;gap:11px;padding:13px 16px;background:linear-gradient(135deg,#0891b2,#0e7490);color:white;border-radius:14px;text-decoration:none;font-weight:700;font-size:13px;box-shadow:0 5px 18px #0891b240">
        <span class="material-symbols-outlined" style="font-size:19px;font-variation-settings:'FILL' 1">medication</span>Gérer les produits
      </a>
      <a href="/integration/stock/orders" style="display:flex;align-items:center;gap:11px;padding:13px 16px;background:linear-gradient(135deg,#7c3aed,#6d28d9);color:white;border-radius:14px;text-decoration:none;font-weight:700;font-size:13px;box-shadow:0 5px 18px #7c3aed40">
        <span class="material-symbols-outlined" style="font-size:19px;font-variation-settings:'FILL' 1">receipt_long</span>Mes commandes
      </a>
      <a href="/integration/stock/cart" style="display:flex;align-items:center;gap:11px;padding:13px 16px;background:linear-gradient(135deg,#d97706,#b45309);color:white;border-radius:14px;text-decoration:none;font-weight:700;font-size:13px;box-shadow:0 5px 18px #d9770640">
        <span class="material-symbols-outlined" style="font-size:19px;font-variation-settings:'FILL' 1">shopping_cart</span>Panier d'achat
      </a>
    </div>
  </div>
</section>

<?php else: ?>
<!-- ═══════════════ AUTRE RÔLE — placeholder ═══════════════ -->
<section class="hover-lift bg-gradient-to-br from-surface-container-lowest to-surface-container-low p-16 rounded-2xl shadow-sm border border-outline/10 text-center">
  <span class="material-symbols-outlined text-6xl text-primary opacity-30 block mb-4">construction</span>
  <h3 class="text-2xl font-bold text-on-surface">Module en cours de développement</h3>
  <p class="text-on-surface-variant mt-2">Votre tableau de bord personnalisé sera disponible prochainement.</p>
</section>
<?php endif; ?>


