<?php
require_once __DIR__ . '/../../model/Equipement.php';

$equipementModel = new Equipement();
$equipements     = $equipementModel->getAll();

$today = date('Y-m-d');

// Stats
$totalEq      = count($equipements);
$disponibles  = count(array_filter($equipements, fn($e) => $e['statut'] === 'disponible'));
$loues        = count(array_filter($equipements, fn($e) => $e['statut'] === 'loue'));
$maintenance  = count(array_filter($equipements, fn($e) => $e['statut'] === 'maintenance'));

function getStatutClass($s) {
    return ['disponible' => 'termine', 'loue' => 'encours', 'maintenance' => 'retard'][$s] ?? 'encours';
}
function getStatutLabel($s) {
    return ['disponible' => 'Disponible', 'loue' => 'Loué', 'maintenance' => 'Maintenance'][$s] ?? '—';
}
function getCatClass($c) {
    return ['Cardiologie'=>'cardio','Réanimation'=>'reani','Gériatrie'=>'geriat','Radiologie'=>'radio','Mobilité'=>'mobi','Respiratoire'=>'resp'][$c] ?? 'cardio';
}
function getCatIcon($c) {
    return ['Cardiologie'=>'monitor_heart','Réanimation'=>'air','Gériatrie'=>'chair','Radiologie'=>'radiology','Mobilité'=>'accessibility_new','Respiratoire'=>'air'][$c] ?? 'medical_services';
}

// Résolution image locale
function getImgUrl($eq) {
    $exts = ['jpg','jpeg','png','webp'];
    foreach ($exts as $ext) {
        $path = __DIR__ . '/../../Assets/images/equipements/' . $eq['reference'] . '.' . $ext;
        if (file_exists($path))
            return '/projet%20web/Assets/images/equipements/' . $eq['reference'] . '.' . $ext;
    }
    if (!empty($eq['image']))
        return '/projet%20web/Assets/images/equipements/' . htmlspecialchars($eq['image']);
    return '';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>MediFlow Admin - Équipements</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@700;800;900&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="/projet%20web/Assets/materiel.css"/>
  <style>
    /* ── Grille équipements ── */
    .eq-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      margin-bottom: 32px;
    }
    @media (max-width: 1100px) { .eq-grid { grid-template-columns: repeat(2,1fr); } }
    @media (max-width: 700px)  { .eq-grid { grid-template-columns: 1fr; } }

    /* ── Carte équipement ── */
    .eq-card {
      background: #fff;
      border: 1px solid #e8eaf0;
      border-radius: 14px;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      transition: box-shadow .22s, transform .22s;
    }
    .eq-card:hover {
      box-shadow: 0 8px 30px rgba(26,54,110,.10);
      transform: translateY(-2px);
    }

    /* Image */
    .eq-card-img {
      height: 180px;
      background: #f3f4f6;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      position: relative;
    }
    .eq-card-img img {
      width: 100%; height: 100%;
      object-fit: contain; padding: 12px;
      transition: transform .4s;
    }
    .eq-card:hover .eq-card-img img { transform: scale(1.05); }
    .eq-card-img .no-img {
      display: flex; flex-direction: column;
      align-items: center; justify-content: center;
      color: #d1d5db; gap: 8px;
    }
    .eq-card-img .no-img .material-symbols-outlined { font-size: 48px; }
    .eq-card-img .no-img span { font-size: 12px; }

    /* Badge statut sur l'image */
    .eq-badge {
      position: absolute; top: 10px; left: 10px;
      padding: 3px 10px; border-radius: 20px;
      font-size: 10px; font-weight: 800;
      letter-spacing: .06em; text-transform: uppercase;
    }
    .eq-badge.disponible { background: #dcfce7; color: #15803d; }
    .eq-badge.loue       { background: #fee2e2; color: #dc2626; }
    .eq-badge.maintenance{ background: #fef9c3; color: #92400e; }

    /* Corps carte */
    .eq-card-body { padding: 16px; flex: 1; display: flex; flex-direction: column; gap: 6px; }
    .eq-card-ref  { font-size: 11px; font-weight: 700; color: #0ea5e9; text-transform: uppercase; letter-spacing:.06em; }
    .eq-card-nom  { font-family:'Manrope',sans-serif; font-size: 15px; font-weight: 800; color: #111827; line-height:1.2; }
    .eq-card-cat  { font-size: 12px; color: #9ca3af; display:flex; align-items:center; gap:5px; }
    .eq-card-cat .material-symbols-outlined { font-size:14px; }
    .eq-card-prix { font-size: 15px; font-weight: 700; color: #1a56db; margin-top: 4px; }

    /* Actions carte */
    .eq-card-actions {
      display: flex; gap: 8px;
      padding: 12px 16px;
      border-top: 1px solid #f3f4f6;
    }
    .btn-eq-edit {
      flex: 1; display: flex; align-items: center; justify-content: center; gap: 5px;
      padding: 8px; border-radius: 8px;
      background: #eff6ff; border: 1px solid #bfdbfe;
      color: #1a56db; font-size: 13px; font-weight: 700;
      cursor: pointer; font-family: 'Inter', sans-serif;
      transition: background .18s;
    }
    .btn-eq-edit:hover { background: #dbeafe; }
    .btn-eq-edit .material-symbols-outlined { font-size: 16px; }

    .btn-eq-del {
      width: 38px; height: 38px; border-radius: 8px;
      background: #fff5f5; border: 1px solid #fecaca;
      color: #dc2626; cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      transition: background .18s;
    }
    .btn-eq-del:hover { background: #fee2e2; }
    .btn-eq-del .material-symbols-outlined { font-size: 17px; }

    /* ── Stats cards ── */
    .stats-row {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 16px;
      margin-bottom: 28px;
    }
    @media (max-width: 900px) { .stats-row { grid-template-columns: repeat(2,1fr); } }

    .stat-mini {
      background: #fff; border: 1px solid #e8eaf0; border-radius: 12px;
      padding: 18px 20px; display: flex; align-items: center; gap: 14px;
    }
    .stat-mini-icon {
      width: 44px; height: 44px; border-radius: 11px;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
    .stat-mini-icon .material-symbols-outlined { font-size: 22px; }
    .stat-mini-icon.blue  { background: #dbeafe; color: #1d4ed8; }
    .stat-mini-icon.green { background: #dcfce7; color: #16a34a; }
    .stat-mini-icon.red   { background: #fee2e2; color: #dc2626; }
    .stat-mini-icon.amber { background: #fef9c3; color: #d97706; }
    .stat-mini-num  { font-family:'Manrope',sans-serif; font-size: 26px; font-weight: 900; color: #111827; line-height:1; }
    .stat-mini-lbl  { font-size: 12px; color: #6b7280; margin-top: 2px; }

    /* ── État vide ── */
    .empty-eq {
      grid-column: 1 / -1; text-align: center;
      padding: 60px 20px; color: #9ca3af;
    }
    .empty-eq .material-symbols-outlined { font-size: 56px; display: block; margin-bottom: 12px; color: #d1d5db; }
    .empty-eq h3 { font-family:'Manrope',sans-serif; font-size: 18px; color: #374151; margin-bottom: 8px; }

    /* ── Upload zone ── */
    .upload-zone {
      border: 2px dashed #bfdbfe; border-radius: 10px; padding: 20px;
      text-align: center; cursor: pointer; background: #f0f7ff;
      transition: border-color .2s, background .2s;
    }
    .upload-zone:hover { border-color: #1a56db; background: #dbeafe; }
    .upload-zone.has-file { border-color: #16a34a; background: #f0fdf4; }
    .upload-zone input[type="file"] { display: none; }
    .upload-icon-big { font-size: 32px; color: #1a56db; display: block; margin-bottom: 6px; }
    .upload-preview-img { width:70px; height:70px; object-fit:cover; border-radius:8px; margin:0 auto 6px; display:block; border:2px solid #16a34a; }

    /* ── Modal divider ── */
    .modal-divider { border:none; border-top:1px solid #f3f4f6; margin:14px 0; }
    .modal-section-label { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#9ca3af; margin-bottom:10px; display:block; }
  </style>
</head>
<body class="admin-body">

<!-- ══ SIDEBAR ══ -->
<aside class="sidebar-admin">
  <div class="sb-admin-brand">
    <div class="name">MediFlow Admin</div>
    <div class="sub">Équipement Médical</div>
  </div>
  <nav class="sb-admin-nav">
    <a href="#"><span class="material-symbols-outlined">inventory_2</span>Stock</a>
    <a href="#"><span class="material-symbols-outlined">shopping_cart</span>Commandes</a>
    <a href="/projet%20web/view/Backoffice/historique-location.php">
      <span class="material-symbols-outlined">history</span>Historique Location
    </a>
    <!--  Lien actif sur Équipements -->
    <a href="/projet%20web/view/Backoffice/equipements.php" class="active">
      <span class="material-symbols-outlined">medical_services</span>Équipements
    </a>
    <a href="#"><span class="material-symbols-outlined">build</span>Maintenance</a>
  </nav>
  <div class="sb-admin-bottom">
    <button class="btn-new-order" type="button" onclick="ouvrirModaleAjout()">
      <span class="material-symbols-outlined">add</span>Nouvel Équipement
    </button>
    <a href="#" class="sb-util"><span class="material-symbols-outlined">help_outline</span>Aide</a>
    <a href="#" class="sb-util"><span class="material-symbols-outlined">logout</span>Déconnexion</a>
  </div>
</aside>

<!-- ══ MAIN ══ -->
<div class="main-admin">

  <!-- Topbar -->
  <header class="topbar-admin">
    <div class="topbar-admin-title">Gestion des<br>Équipements</div>
    <div class="topnav-search" style="flex:1;max-width:280px;">
      <span class="material-symbols-outlined">search</span>
      <input type="text" id="search-input" placeholder="Rechercher un équipement..."/>
    </div>
    <div class="topbar-admin-links">
      <a href="#">Dashboard</a>
      <a href="#">Support</a>
    </div>
    <div style="display:flex;align-items:center;gap:10px;">
      <button class="icon-btn"><span class="material-symbols-outlined">notifications</span></button>
      <div class="nav-avatar" style="width:34px;height:34px;border-radius:50%;background:#dbeafe;display:flex;align-items:center;justify-content:center;">
        <span class="material-symbols-outlined" style="font-size:20px;color:#1a56db;">person</span>
      </div>
    </div>
  </header>

  <!-- ══ CONTENU ══ -->
  <main class="content-admin">

    <!-- Titre + bouton -->
    <div class="page-title-row">
      <div>
        <h1>Liste des Équipements</h1>
        <p>Gérez tous les équipements médicaux disponibles à la location.</p>
      </div>
      <button class="btn-new-entry" type="button" onclick="ouvrirModaleAjout()">
        <span class="material-symbols-outlined">add_circle</span>Nouvel Équipement
      </button>
    </div>

    <!-- Stats -->
    <div class="stats-row">
      <div class="stat-mini">
        <div class="stat-mini-icon blue"><span class="material-symbols-outlined">medical_services</span></div>
        <div><div class="stat-mini-num"><?= $totalEq ?></div><div class="stat-mini-lbl">Total équipements</div></div>
      </div>
      <div class="stat-mini">
        <div class="stat-mini-icon green"><span class="material-symbols-outlined">check_circle</span></div>
        <div><div class="stat-mini-num"><?= $disponibles ?></div><div class="stat-mini-lbl">Disponibles</div></div>
      </div>
      <div class="stat-mini">
        <div class="stat-mini-icon red"><span class="material-symbols-outlined">shopping_cart</span></div>
        <div><div class="stat-mini-num"><?= $loues ?></div><div class="stat-mini-lbl">Loués</div></div>
      </div>
      <div class="stat-mini">
        <div class="stat-mini-icon amber"><span class="material-symbols-outlined">build</span></div>
        <div><div class="stat-mini-num"><?= $maintenance ?></div><div class="stat-mini-lbl">En maintenance</div></div>
      </div>
    </div>

    <!-- Filtres -->
    <div style="display:flex;gap:10px;margin-bottom:22px;flex-wrap:wrap;">
      <div class="filter-bar">
        <button class="active" data-filter-eq="all">Tous</button>
        <button data-filter-eq="disponible">Disponibles</button>
        <button data-filter-eq="loue">Loués</button>
        <button data-filter-eq="maintenance">Maintenance</button>
      </div>
    </div>

    <!-- Grille équipements -->
    <div class="eq-grid" id="eq-grid">

      <?php if (empty($equipements)): ?>
        <div class="empty-eq">
          <span class="material-symbols-outlined">medical_services</span>
          <h3>Aucun équipement</h3>
          <p>Cliquez sur "Nouvel Équipement" pour en ajouter un.</p>
        </div>

      <?php else: ?>
        <?php foreach ($equipements as $eq): ?>
          <?php
            $imgUrl   = getImgUrl($eq);
            $statCls  = getStatutClass($eq['statut']);
            $statLbl  = getStatutLabel($eq['statut']);
            $catCls   = getCatClass($eq['categorie']);
            $catIco   = getCatIcon($eq['categorie']);
            $prixDT   = number_format((float)$eq['prix_jour'], 3, ',', '.');
            $eqJson   = htmlspecialchars(json_encode($eq), ENT_QUOTES);
          ?>
          <div class="eq-card" data-statut="<?= $eq['statut'] ?>" id="eq-<?= $eq['id'] ?>">

            <!-- Image -->
            <div class="eq-card-img">
              <?php if ($imgUrl): ?>
                <img src="<?= $imgUrl ?>"
                     alt="<?= htmlspecialchars($eq['nom']) ?>"
                     loading="lazy"
                     onerror="this.parentElement.innerHTML='<div class=\'no-img\'><span class=\'material-symbols-outlined\'>hide_image</span><span>Pas d\'image</span></div>'"/>
              <?php else: ?>
                <div class="no-img">
                  <span class="material-symbols-outlined">add_photo_alternate</span>
                  <span>Pas d'image</span>
                </div>
              <?php endif; ?>
              <span class="eq-badge <?= $eq['statut'] ?>"><?= $statLbl ?></span>
            </div>

            <!-- Corps -->
            <div class="eq-card-body">
              <div class="eq-card-ref"><?= htmlspecialchars($eq['reference']) ?></div>
              <div class="eq-card-nom"><?= htmlspecialchars($eq['nom']) ?></div>
              <div class="eq-card-cat">
                <span class="material-symbols-outlined"><?= $catIco ?></span>
                <?= htmlspecialchars($eq['categorie']) ?>
              </div>
              <div class="eq-card-prix">
                <?= $prixDT ?> DT / jour
              </div>
            </div>

            <!-- Actions -->
            <div class="eq-card-actions">
              <!-- ✏️ Modifier -->
              <button class="btn-eq-edit" type="button"
                      title="Modifier cet équipement"
                      onclick='ouvrirModaleModifier(<?= $eqJson ?>)'>
                <span class="material-symbols-outlined">edit</span>
                Modifier
              </button>
              <!-- 🗑️ Supprimer -->
              <button class="btn-eq-del" type="button"
                      title="Supprimer cet équipement"
                      onclick="supprimerEquipement(<?= (int)$eq['id'] ?>, '<?= htmlspecialchars($eq['nom'], ENT_QUOTES) ?>')">
                <span class="material-symbols-outlined">delete</span>
              </button>
            </div>

          </div>
        <?php endforeach; ?>
      <?php endif; ?>

    </div><!-- /eq-grid -->

  </main>
</div>

<!-- ══════════════════════════════════════════
     MODALE — AJOUTER UN ÉQUIPEMENT
════════════════════════════════════════════ -->
<div id="modal-ajout" class="modal-overlay">
  <div class="modal-box">
    <div class="modal-header">
      <h2>➕ Nouvel Équipement</h2>
      <button class="modal-close" onclick="fermerModaleAjout()" type="button">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>

    <span class="modal-section-label">Informations</span>

    <div class="modal-field">
      <label for="aj-nom">Nom de l'équipement <span style="color:#dc2626;">*</span></label>
      <input id="aj-nom" class="modal-input" type="text" placeholder="Ex: Fauteuil roulant électrique"/>
    </div>

    <div class="modal-row">
      <div class="modal-field">
        <label for="aj-reference">Référence <span style="color:#dc2626;">*</span></label>
        <input id="aj-reference" class="modal-input" type="text" placeholder="Ex: EQ-5001"/>
      </div>
      <div class="modal-field">
        <label for="aj-prix">Prix / jour (DT) <span style="color:#dc2626;">*</span></label>
        <input id="aj-prix" class="modal-input" type="text" placeholder="Ex: 15"/>
      </div>
    </div>

    <div class="modal-field">
      <label for="aj-categorie">Catégorie <span style="color:#dc2626;">*</span></label>
      <select id="aj-categorie" class="modal-input">
        <option value="">-- Choisir --</option>
        <option value="Mobilité">Mobilité</option>
        <option value="Respiratoire">Respiratoire</option>
        <option value="Cardiologie">Cardiologie</option>
        <option value="Réanimation">Réanimation</option>
        <option value="Gériatrie">Gériatrie</option>
        <option value="Radiologie">Radiologie</option>
      </select>
    </div>

    <hr class="modal-divider"/>
    <span class="modal-section-label">Photo de l'équipement</span>

    <div class="upload-zone" id="upload-zone-ajout"
         onclick="document.getElementById('aj-image').click()">
      <input type="file" id="aj-image" accept=".jpg,.jpeg,.png,.webp"
             onchange="previewImage(this, 'ajout')"/>
      <div id="upload-placeholder-ajout">
        <span class="material-symbols-outlined upload-icon-big">add_photo_alternate</span>
        <div style="font-size:13px;font-weight:600;color:#374151;">Cliquez pour télécharger une photo</div>
        <div style="font-size:11px;color:#9ca3af;margin-top:4px;">JPG, JPEG, PNG, WEBP — max 5 Mo</div>
      </div>
      <div id="upload-preview-ajout" style="display:none;">
        <img id="upload-img-ajout" class="upload-preview-img" src="" alt="Aperçu"/>
        <div id="upload-name-ajout" style="font-size:12px;color:#16a34a;font-weight:600;"></div>
        <div style="font-size:11px;color:#16a34a;margin-top:2px;">✅ Cliquez pour changer</div>
      </div>
    </div>

    <div class="modal-footer">
      <button class="btn-cancel" onclick="fermerModaleAjout()" type="button">Annuler</button>
      <button class="btn-save" id="btn-save-ajout" type="button">
        <span class="material-symbols-outlined" style="font-size:16px;vertical-align:middle;">save</span>
        Enregistrer
      </button>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════
     MODALE — MODIFIER UN ÉQUIPEMENT
════════════════════════════════════════════ -->
<div id="modal-modifier" class="modal-overlay">
  <div class="modal-box">
    <div class="modal-header">
      <h2>✏️ Modifier l'Équipement</h2>
      <button class="modal-close" onclick="fermerModaleModifier()" type="button">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>

    <input type="hidden" id="mod-id"/>

    <span class="modal-section-label">Informations</span>

    <div class="modal-field">
      <label for="mod-nom">Nom <span style="color:#dc2626;">*</span></label>
      <input id="mod-nom" class="modal-input" type="text"/>
    </div>

    <div class="modal-row">
      <div class="modal-field">
        <label for="mod-reference">Référence <span style="color:#dc2626;">*</span></label>
        <input id="mod-reference" class="modal-input" type="text"/>
      </div>
      <div class="modal-field">
        <label for="mod-prix">Prix / jour (DT) <span style="color:#dc2626;">*</span></label>
        <input id="mod-prix" class="modal-input" type="text"/>
      </div>
    </div>

    <div class="modal-row">
      <div class="modal-field">
        <label for="mod-categorie">Catégorie <span style="color:#dc2626;">*</span></label>
        <select id="mod-categorie" class="modal-input">
          <option value="Mobilité">Mobilité</option>
          <option value="Respiratoire">Respiratoire</option>
          <option value="Cardiologie">Cardiologie</option>
          <option value="Réanimation">Réanimation</option>
          <option value="Gériatrie">Gériatrie</option>
          <option value="Radiologie">Radiologie</option>
        </select>
      </div>
      <div class="modal-field">
        <label for="mod-statut">Statut</label>
        <select id="mod-statut" class="modal-input">
          <option value="disponible"> Disponible</option>
          <option value="loue">🔴 Loué</option>
          <option value="maintenance">🔧 Maintenance</option>
        </select>
      </div>
    </div>

    <hr class="modal-divider"/>
    <span class="modal-section-label">Changer la photo</span>

    <div class="upload-zone" id="upload-zone-mod"
         onclick="document.getElementById('mod-image').click()">
      <input type="file" id="mod-image" accept=".jpg,.jpeg,.png,.webp"
             onchange="previewImage(this, 'modifier')"/>
      <div id="upload-placeholder-mod">
        <span class="material-symbols-outlined upload-icon-big">add_photo_alternate</span>
        <div style="font-size:12px;color:#6b7280;">Cliquez pour changer la photo (optionnel)</div>
      </div>
      <div id="upload-preview-mod" style="display:none;">
        <img id="upload-img-mod" class="upload-preview-img" src="" alt="Aperçu"/>
        <div id="upload-name-mod" style="font-size:12px;color:#16a34a;font-weight:600;"></div>
      </div>
    </div>

    <div class="modal-footer">
      <button class="btn-cancel" onclick="fermerModaleModifier()" type="button">Annuler</button>
      <button class="btn-save" id="btn-save-modifier" type="button">
        <span class="material-symbols-outlined" style="font-size:16px;vertical-align:middle;">save</span>
        Enregistrer
      </button>
    </div>
  </div>
</div>

<div class="toast-container"></div>
<script src="/projet%20web/Assets/materiel.js"></script>
<script>
  const API_EQ = '/projet%20web/controller/EquipementController.php';
  const TODAY  = '<?= $today ?>';

  /* ════════════════════════════
     PRÉVISUALISATION IMAGE
  ════════════════════════════ */
  function previewImage(input, mode) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
      const suffix = mode === 'ajout' ? 'ajout' : 'mod';
      document.getElementById('upload-img-'   + suffix).src         = e.target.result;
      document.getElementById('upload-name-'  + suffix).textContent  = file.name;
      document.getElementById('upload-placeholder-' + suffix).style.display = 'none';
      document.getElementById('upload-preview-'     + suffix).style.display = 'block';
      document.getElementById('upload-zone-'  + suffix).classList.add('has-file');
    };
    reader.readAsDataURL(file);
  }

  /* ════════════════════════════
     AFFICHER / EFFACER ERREUR
  ════════════════════════════ */
  function afficherErr(id, msg) {
    const input = document.getElementById(id);
    if (!input) return;
    input.style.borderColor = '#dc2626';
    input.style.boxShadow   = '0 0 0 3px rgba(220,38,38,.10)';
    input.parentElement.querySelector('.msg-erreur')?.remove();
    const span = document.createElement('small');
    span.className   = 'msg-erreur';
    span.textContent = '⚠ ' + msg;
    span.style.cssText = 'color:#dc2626;font-size:11px;font-weight:600;display:block;margin-top:4px;';
    input.insertAdjacentElement('afterend', span);
  }

  function effacerErr(id) {
    const input = document.getElementById(id);
    if (!input) return;
    input.style.borderColor = '';
    input.style.boxShadow   = '';
    input.parentElement.querySelector('.msg-erreur')?.remove();
  }

  /* ════════════════════════════
     VALIDATION COMMUNE
  ════════════════════════════ */
  function validerChamps(prefix) {
    let ok = true;
    document.querySelectorAll(`#modal-${prefix === 'aj' ? 'ajout' : 'modifier'} .msg-erreur`).forEach(e => e.remove());
    document.querySelectorAll(`#modal-${prefix === 'aj' ? 'ajout' : 'modifier'} .modal-input`).forEach(i => {
      i.style.borderColor = ''; i.style.boxShadow = '';
    });

    const nom  = document.getElementById(prefix + '-nom').value.trim();
    const ref  = document.getElementById(prefix + '-reference').value.trim();
    const prix = document.getElementById(prefix + '-prix').value.trim();
    const cat  = document.getElementById(prefix + '-categorie').value;

    if (!nom || nom.length < 2)                                       { afficherErr(prefix+'-nom',       'Nom obligatoire (min 2 caractères).'); ok = false; }
    if (!ref || ref.length < 2)                                       { afficherErr(prefix+'-reference',  'Référence obligatoire.'); ok = false; }
    if (!prix || isNaN(parseFloat(prix)) || parseFloat(prix) <= 0)   { afficherErr(prefix+'-prix',       'Prix doit être un nombre positif.'); ok = false; }
    if (!cat)                                                          { afficherErr(prefix+'-categorie',  'Veuillez sélectionner une catégorie.'); ok = false; }

    if (!ok) showToast('Veuillez corriger les erreurs.', 'error');
    return ok;
  }

  /* ════════════════════════════
     MODALE AJOUT
  ════════════════════════════ */
  function ouvrirModaleAjout() {
    ['aj-nom','aj-reference','aj-prix'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('aj-categorie').value = '';
    document.getElementById('aj-image').value     = '';
    document.getElementById('upload-placeholder-ajout').style.display = 'block';
    document.getElementById('upload-preview-ajout').style.display     = 'none';
    document.getElementById('upload-zone-ajout').classList.remove('has-file');
    document.querySelectorAll('#modal-ajout .msg-erreur').forEach(e => e.remove());
    document.querySelectorAll('#modal-ajout .modal-input').forEach(i => { i.style.borderColor=''; i.style.boxShadow=''; });
    document.getElementById('modal-ajout').classList.add('open');
  }

  function fermerModaleAjout() {
    document.getElementById('modal-ajout').classList.remove('open');
  }

  /* Enregistrer ajout */
  document.getElementById('btn-save-ajout').addEventListener('click', async () => {
    if (!validerChamps('aj')) return;

    const btn = document.getElementById('btn-save-ajout');
    btn.disabled    = true;
    btn.textContent = 'Enregistrement...';

    const formData = new FormData();
    formData.append('nom',       document.getElementById('aj-nom').value.trim());
    formData.append('reference', document.getElementById('aj-reference').value.trim());
    formData.append('prix_jour', document.getElementById('aj-prix').value.trim());
    formData.append('categorie', document.getElementById('aj-categorie').value);
    formData.append('statut',    'disponible');

    const imgFile = document.getElementById('aj-image').files[0];
    if (imgFile) formData.append('image', imgFile);

    try {
      const res  = await fetch(API_EQ, { method: 'POST', body: formData });
      const text = await res.text();
      let json;
      try { json = JSON.parse(text); }
      catch(e) { showToast('Erreur PHP : ' + text.substring(0, 120), 'error'); btn.disabled=false; btn.innerHTML='<span class="material-symbols-outlined" style="font-size:16px;vertical-align:middle;">save</span> Enregistrer'; return; }

      if (json.success) {
        showToast(' Équipement ajouté avec succès !', 'success');
        fermerModaleAjout();
        setTimeout(() => location.reload(), 1400);
      } else {
        showToast('Erreur : ' + (json.message || 'Inconnue'), 'error');
        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:16px;vertical-align:middle;">save</span> Enregistrer';
      }
    } catch(e) {
      showToast('Erreur réseau : ' + e.message, 'error');
      btn.disabled = false;
      btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:16px;vertical-align:middle;">save</span> Enregistrer';
    }
  });

  /* Effacer erreurs temps réel */
  ['aj-nom','aj-reference','aj-prix'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', () => effacerErr(id));
  });

  /* ════════════════════════════
     MODALE MODIFIER
  ════════════════════════════ */
  function ouvrirModaleModifier(eq) {
    document.getElementById('mod-id').value         = eq.id;
    document.getElementById('mod-nom').value        = eq.nom        || '';
    document.getElementById('mod-reference').value  = eq.reference  || '';
    document.getElementById('mod-prix').value       = eq.prix_jour  || '';
    document.getElementById('mod-categorie').value  = eq.categorie  || '';
    document.getElementById('mod-statut').value     = eq.statut     || 'disponible';
    document.getElementById('mod-image').value      = '';
    document.getElementById('upload-placeholder-mod').style.display = 'block';
    document.getElementById('upload-preview-mod').style.display     = 'none';
    document.getElementById('upload-zone-mod').classList.remove('has-file');
    document.querySelectorAll('#modal-modifier .msg-erreur').forEach(e => e.remove());
    document.querySelectorAll('#modal-modifier .modal-input').forEach(i => { i.style.borderColor=''; i.style.boxShadow=''; });
    document.getElementById('modal-modifier').classList.add('open');
  }

  function fermerModaleModifier() {
    document.getElementById('modal-modifier').classList.remove('open');
  }

  /* Enregistrer modification */
  document.getElementById('btn-save-modifier').addEventListener('click', async () => {
    if (!validerChamps('mod')) return;

    const id  = document.getElementById('mod-id').value;
    const btn = document.getElementById('btn-save-modifier');

    // Si une nouvelle image est sélectionnée → FormData (POST pour image)
    const imgFile = document.getElementById('mod-image').files[0];

    if (imgFile) {
      // Upload via FormData
      btn.disabled    = true;
      btn.textContent = 'Enregistrement...';

      const formData = new FormData();
      formData.append('nom',       document.getElementById('mod-nom').value.trim());
      formData.append('reference', document.getElementById('mod-reference').value.trim());
      formData.append('prix_jour', document.getElementById('mod-prix').value.trim());
      formData.append('categorie', document.getElementById('mod-categorie').value);
      formData.append('statut',    document.getElementById('mod-statut').value);
      formData.append('_method',   'PUT'); // indicateur pour le controller
      formData.append('image',     imgFile);

      // Supprimer l'ancien et recréer
      await fetch(`${API_EQ}?id=${id}`, { method: 'DELETE' });
      const res  = await fetch(API_EQ, { method: 'POST', body: formData });
      const text = await res.text();
      try {
        const json = JSON.parse(text);
        if (json.success) {
          showToast(' Équipement modifié !', 'success');
          fermerModaleModifier();
          setTimeout(() => location.reload(), 1400);
        } else {
          showToast('Erreur : ' + (json.message || ''), 'error');
        }
      } catch(e) { showToast('Erreur serveur.', 'error'); }
      btn.disabled = false;
      btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:16px;vertical-align:middle;">save</span> Enregistrer';

    } else {
      // Modification sans nouvelle image → PUT JSON
      const data = {
        reference: document.getElementById('mod-reference').value.trim(),
        nom:       document.getElementById('mod-nom').value.trim(),
        categorie: document.getElementById('mod-categorie').value,
        prix_jour: parseFloat(document.getElementById('mod-prix').value),
        statut:    document.getElementById('mod-statut').value,
        image:     null,
      };
      try {
        const res  = await fetch(`${API_EQ}?id=${id}`, {
          method: 'PUT', headers:{'Content-Type':'application/json'}, body: JSON.stringify(data)
        });
        const json = await res.json();
        if (json.success) {
          showToast(' Équipement modifié !', 'success');
          fermerModaleModifier();
          setTimeout(() => location.reload(), 1400);
        } else {
          showToast('Erreur : ' + (json.message || ''), 'error');
        }
      } catch(e) { showToast('Erreur réseau.', 'error'); }
    }
  });

  /* Effacer erreurs temps réel */
  ['mod-nom','mod-reference','mod-prix'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', () => effacerErr(id));
  });

  /* ════════════════════════════
     SUPPRIMER UN ÉQUIPEMENT
  ════════════════════════════ */
  async function supprimerEquipement(id, nom) {
    if (!confirm(`⚠️ Supprimer l'équipement "${nom}" ?\nCette action est irréversible.`)) return;
    try {
      const res  = await fetch(`${API_EQ}?id=${id}`, { method: 'DELETE' });
      const json = await res.json();
      if (json.success) {
        showToast('Équipement supprimé.', 'success');
        const card = document.getElementById('eq-' + id);
        if (card) {
          card.style.transition = 'opacity .3s, transform .3s';
          card.style.opacity    = '0';
          card.style.transform  = 'scale(.95)';
          setTimeout(() => { card.remove(); }, 300);
        }
      } else {
        showToast('Erreur : ' + (json.message || ''), 'error');
      }
    } catch(e) { showToast('Erreur réseau.', 'error'); }
  }

  /* ════════════════════════════
     FILTRES PAR STATUT
  ════════════════════════════ */
  document.querySelectorAll('[data-filter-eq]').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('[data-filter-eq]').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const filter = btn.dataset.filterEq;
      document.querySelectorAll('.eq-card').forEach(card => {
        const statut = card.dataset.statut;
        card.style.display = (filter === 'all' || statut === filter) ? '' : 'none';
      });
    });
  });

  /* ════════════════════════════
     RECHERCHE EN TEMPS RÉEL
  ════════════════════════════ */
  document.getElementById('search-input').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.eq-card').forEach(card => {
      card.style.display = card.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  });

  /* Fermer modales en cliquant dehors */
  ['modal-ajout','modal-modifier'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
      if (e.target === this) this.classList.remove('open');
    });
  });
</script>
</body>
</html>