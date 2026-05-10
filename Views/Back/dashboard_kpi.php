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

<!-- Restart Tour Button (if onboarding completed) -->
<?php if (!($data['show_tour'] ?? false)): ?>
<div class="mb-6 flex justify-end">
    <button class="restart-tour-btn" onclick="mediflowTour.restart()">
        <span class="material-symbols-outlined">tour</span>
        <span>Relancer la visite guidée</span>
    </button>
</div>
<?php endif; ?>

<section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 dashboard-stats">
  <?php
  $pCards = [
    ['label'=>'Total réservations','val'=>$data['nbTotal']??0,'icon'=>'receipt_long','color'=>'#004d99','bg'=>'#eff6ff'],
    ['label'=>'En cours','val'=>$data['nbEnCours']??0,'icon'=>'hourglass_top','color'=>'#d97706','bg'=>'#fffbeb'],
    ['label'=>'Terminées','val'=>$data['nbTermines']??0,'icon'=>'check_circle','color'=>'#16a34a','bg'=>'#f0fdf4'],
    ['label'=>'En retard','val'=>$data['nbEnRetard']??0,'icon'=>'warning','color'=>'#dc2626','bg'=>'#fff5f5'],
  ];
  foreach ($pCards as $c): ?>
  <div class="hover-lift p-6 rounded-2xl shadow-sm border border-outline/10" style="background:<?= $c['bg'] ?>">
    <div class="flex justify-between items-start">
      <div>
        <p class="text-sm font-semibold" style="color:<?= $c['color'] ?>;opacity:.7"><?= $c['label'] ?></p>
        <h3 class="text-4xl font-black mt-2" style="color:<?= $c['color'] ?>"><?= $c['val'] ?></h3>
      </div>
      <span class="material-symbols-outlined text-3xl" style="color:<?= $c['color'] ?>;opacity:.5"><?= $c['icon'] ?></span>
    </div>
  </div>
  <?php endforeach; ?>
</section>

<!-- Dernières réservations -->
<section class="hover-lift bg-gradient-to-br from-surface-container-lowest to-surface-container-low p-8 rounded-xl shadow-sm border border-outline/10 reservations-table">
  <div class="flex justify-between items-center mb-6">
    <h3 class="text-xl font-bold">Mes dernières réservations</h3>
    <a href="/integration/mes-reservations" class="text-sm font-bold text-primary hover:underline">Voir tout →</a>
  </div>
  <?php if (empty($data['latestRes'])): ?>
    <div class="text-center py-14">
      <span class="material-symbols-outlined text-5xl text-slate-300 block mb-3">shopping_cart</span>
      <p class="font-semibold text-on-surface-variant">Aucune réservation pour l'instant.</p>
      <a href="/integration/catalogue" class="mt-4 inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl font-bold text-sm">
        <span class="material-symbols-outlined text-base">medical_services</span> Parcourir le catalogue
      </a>
    </div>
  <?php else: ?>
  <div class="space-y-4">
    <?php foreach ($data['latestRes'] as $r):
      $sColor = ['en_cours'=>'#d97706','termine'=>'#16a34a','en_retard'=>'#dc2626'][$r['statut']??''] ?? '#6b7280';
      $sLabel = ['en_cours'=>'En cours','termine'=>'Terminée','en_retard'=>'En retard'][$r['statut']??''] ?? $r['statut'];
    ?>
    <div class="flex items-center justify-between p-5 bg-surface-container-low rounded-xl border border-outline/5 hover:border-primary/20 transition-all">
      <div class="flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-primary-fixed flex items-center justify-center">
          <span class="material-symbols-outlined text-primary">medical_services</span>
        </div>
        <div>
          <p class="font-bold text-on-surface text-sm"><?= htmlspecialchars($r['equipement_nom']??($r['equipement_id']??'Équipement')) ?></p>
          <p class="text-xs text-on-surface-variant mt-0.5"><?= htmlspecialchars($r['date_debut']??'') ?> → <?= htmlspecialchars($r['date_fin']??'') ?></p>
        </div>
      </div>
      <span class="px-3 py-1 rounded-full text-xs font-bold" style="background:<?= $sColor ?>20;color:<?= $sColor ?>"><?= $sLabel ?></span>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</section>

<!-- Quick actions -->
<section class="grid grid-cols-1 md:grid-cols-2 gap-6 quick-actions">
  <a href="/integration/catalogue" class="hover-lift flex items-center gap-5 p-6 bg-gradient-to-r from-primary to-primary-container text-white rounded-2xl shadow-lg transition-all">
    <span class="material-symbols-outlined text-4xl opacity-80">medical_services</span>
    <div>
      <p class="font-black text-lg">Catalogue d'équipements</p>
      <p class="text-sm opacity-80">Réserver du matériel médical</p>
    </div>
  </a>
  <a href="/integration/mes-reservations" class="hover-lift flex items-center gap-5 p-6 bg-gradient-to-r from-slate-700 to-slate-800 text-white rounded-2xl shadow-lg transition-all">
    <span class="material-symbols-outlined text-4xl opacity-80">receipt_long</span>
    <div>
      <p class="font-black text-lg">Mes réservations</p>
      <p class="text-sm opacity-80">Suivre toutes mes locations</p>
    </div>
  </a>
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

<?php else: ?>
<!-- ═══════════════ AUTRE RÔLE — placeholder ═══════════════ -->
<section class="hover-lift bg-gradient-to-br from-surface-container-lowest to-surface-container-low p-16 rounded-2xl shadow-sm border border-outline/10 text-center">
  <span class="material-symbols-outlined text-6xl text-primary opacity-30 block mb-4">construction</span>
  <h3 class="text-2xl font-bold text-on-surface">Module en cours de développement</h3>
  <p class="text-on-surface-variant mt-2">Votre tableau de bord personnalisé sera disponible prochainement.</p>
</section>
<?php endif; ?>
