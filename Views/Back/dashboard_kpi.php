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
<section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
  <?php
  $kpis = [
    ['label'=>'Utilisateurs totaux','val'=>$data['stats']['totalUsers']??0,'icon'=>'people','color'=>'primary'],
    ['label'=>'Patients','val'=>$data['stats']['totalPatients']??0,'icon'=>'badge','color'=>'tertiary'],
    ['label'=>'Rôles','val'=>isset($data['stats']['usersByRole'])?count($data['stats']['usersByRole']):0,'icon'=>'shield_person','color'=>'secondary'],
    ['label'=>'Statut système','val'=>'✔ En ligne','icon'=>'check_circle','color'=>'primary','text'=>true],
  ];
  foreach ($kpis as $k): ?>
  <div class="hover-lift bg-gradient-to-br from-surface-container-lowest to-surface-container-low p-6 rounded-xl shadow-sm border-t-2 border-<?= $k['color'] ?> border-r border-b border-outline/10">
    <div class="flex justify-between items-start">
      <div>
        <p class="text-sm font-medium text-on-surface-variant"><?= $k['label'] ?></p>
        <h3 class="text-3xl font-black mt-2 text-<?= $k['color'] ?>"><?= $k['val'] ?></h3>
      </div>
      <span class="material-symbols-outlined text-<?= $k['color'] ?> text-2xl"><?= $k['icon'] ?></span>
    </div>
  </div>
  <?php endforeach; ?>
</section>

<!-- Répartition par rôle -->
<section class="hover-lift bg-gradient-to-br from-surface-container-lowest to-surface-container-low p-8 rounded-xl shadow-sm border border-outline/10">
  <h3 class="text-xl font-bold text-on-surface mb-6">Répartition par rôle</h3>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <?php foreach (($data['stats']['usersByRole'] ?? []) as $r): ?>
    <div class="p-5 bg-surface-container-low rounded-xl border border-outline/5 hover:border-primary/20 transition-all">
      <p class="text-xs font-bold text-on-surface-variant uppercase tracking-wider"><?= htmlspecialchars($r['role_name']??'—') ?></p>
      <p class="text-3xl font-black text-primary mt-3"><?= $r['user_count']??0 ?></p>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Activité récente + Stats -->
<section class="grid grid-cols-1 lg:grid-cols-2 gap-8">
  <div class="hover-lift bg-gradient-to-br from-surface-container-lowest to-surface-container-low p-8 rounded-xl shadow-sm border border-outline/10">
    <h3 class="text-xl font-bold text-on-surface mb-6">Activité récente</h3>
    <div class="space-y-4">
      <?php foreach (array_slice($data['recentActivity']??[], 0, 5) as $act): ?>
      <div class="flex items-center space-x-4 p-3 rounded-lg hover:bg-surface-container/50 transition-colors">
        <div class="w-10 h-10 rounded-full bg-primary-fixed flex items-center justify-center text-primary font-bold">
          <?= strtoupper(substr($act['prenom']??'?',0,1)) ?>
        </div>
        <div class="flex-1">
          <p class="text-sm font-bold"><?= htmlspecialchars($act['prenom'].' '.$act['nom']) ?> — inscrit</p>
          <p class="text-xs text-on-surface-variant"><?= htmlspecialchars($act['role_name']??'') ?> · <?= htmlspecialchars($act['mail']) ?></p>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if (empty($data['recentActivity'])): ?>
        <p class="text-on-surface-variant text-center py-6">Aucune activité récente</p>
      <?php endif; ?>
    </div>
  </div>
  <div class="hover-lift bg-gradient-to-br from-surface-container-lowest to-surface-container-low p-8 rounded-xl shadow-sm border border-outline/10">
    <h3 class="text-xl font-bold mb-6">Statistiques utilisateurs</h3>
    <div class="space-y-4">
      <div class="flex items-center justify-between p-4 bg-surface-container-low rounded-xl border border-outline/5">
        <span class="text-sm font-medium">Total inscrits</span>
        <span class="text-lg font-black text-primary"><?= $data['stats']['totalUsers']??0 ?></span>
      </div>
      <div class="flex items-center justify-between p-4 bg-surface-container-low rounded-xl border border-outline/5">
        <span class="text-sm font-medium">Patients</span>
        <span class="text-lg font-black text-tertiary"><?= $data['stats']['totalPatients']??0 ?></span>
      </div>
      <div class="flex items-center justify-between p-4 bg-surface-container-low rounded-xl border border-outline/5">
        <span class="text-sm font-medium">Statut système</span>
        <span class="text-sm font-black text-green-600 flex items-center gap-2"><span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>Opérationnel</span>
      </div>
    </div>
  </div>
</section>

<!-- Patients list -->
<section class="hover-lift bg-gradient-to-br from-surface-container-lowest to-surface-container-low p-8 rounded-xl shadow-sm border border-outline/10">
  <div class="flex justify-between items-center mb-6">
    <h3 class="text-xl font-bold">Patients enregistrés</h3>
    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">Total : <?= $data['stats']['totalPatients']??0 ?></span>
  </div>
  <div style="border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;">
    <table style="width:100%;border-collapse:collapse;">
      <tr style="background:#f0fdf4;border-bottom:2px solid #bbf7d0;">
        <th style="padding:14px 20px;text-align:left;font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;">Matricule</th>
        <th style="padding:14px 20px;text-align:left;font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;">Nom</th>
        <th style="padding:14px 20px;text-align:left;font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;">Email</th>
        <th style="padding:14px 20px;text-align:left;font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;">Tél</th>
      </tr>
      <?php foreach (($data['patients']??[]) as $p): ?>
      <tr style="border-bottom:1px solid #f3f4f6;">
        <td style="padding:13px 20px;font-size:13px;color:#16a34a;font-weight:700;"><?= htmlspecialchars($p['matricule']??'N/A') ?></td>
        <td style="padding:13px 20px;font-size:13px;font-weight:600;"><?= htmlspecialchars($p['prenom'].' '.$p['nom']) ?></td>
        <td style="padding:13px 20px;font-size:13px;color:#6b7280;"><?= htmlspecialchars($p['mail']) ?></td>
        <td style="padding:13px 20px;font-size:13px;"><?= htmlspecialchars($p['tel']??'—') ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($data['patients'])): ?>
      <tr><td colspan="4" style="padding:40px;text-align:center;color:#9ca3af;">Aucun patient</td></tr>
      <?php endif; ?>
    </table>
  </div>
</section>

<?php elseif ($role === 'Patient'): ?>
<!-- ═══════════════ PATIENT ═══════════════ -->
<section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
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
<section class="hover-lift bg-gradient-to-br from-surface-container-lowest to-surface-container-low p-8 rounded-xl shadow-sm border border-outline/10">
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
<section class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
