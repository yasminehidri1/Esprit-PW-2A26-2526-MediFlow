<?php
// Charger les clés API depuis le fichier de config sécurisé
if (file_exists(__DIR__ . '/../../config_keys.php')) require_once __DIR__ . '/../../config_keys.php';
if (!defined('OPENROUTER_API_KEY')) define('OPENROUTER_API_KEY', '');

// $data injected by PatientEquipmentController::gestionEquipements()
$equipements     = $data['equipements'] ?? [];
$user            = $data['currentUser']  ?? ($_SESSION['user'] ?? []);

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

function getImgUrl($eq) {
    $exts = ['jpg','jpeg','png','webp'];
    foreach ($exts as $ext) {
        foreach ([__DIR__.'/../../assets/images/equipements/', __DIR__.'/../../Assets/images/equipements/'] as $base) {
            if (file_exists($base . $eq['reference'] . '.' . $ext))
                return '/integration/assets/images/equipements/' . $eq['reference'] . '.' . $ext;
        }
    }
    if (!empty($eq['image']))
        return '/integration/assets/images/equipements/' . htmlspecialchars($eq['image']);
    return '';
}
?>
<style>
/* ── Stats row ── */
.stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;}
@media(max-width:900px){.stats-row{grid-template-columns:repeat(2,1fr);}}
.stat-mini{background:#fff;border:1px solid #e8eaf0;border-radius:12px;padding:18px 20px;display:flex;align-items:center;gap:14px;}
.stat-mini-icon{width:44px;height:44px;border-radius:11px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.stat-mini-icon .material-symbols-outlined{font-size:22px;}
.stat-mini-icon.blue{background:#dbeafe;color:#1d4ed8;}
.stat-mini-icon.green{background:#dcfce7;color:#16a34a;}
.stat-mini-icon.red{background:#fee2e2;color:#dc2626;}
.stat-mini-icon.amber{background:#fef9c3;color:#d97706;}
.stat-mini-num{font-family:'Manrope',sans-serif;font-size:26px;font-weight:900;color:#111827;line-height:1;}
.stat-mini-lbl{font-size:12px;color:#6b7280;margin-top:2px;}

.filter-bar{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:22px;}
.filter-bar button{padding:7px 18px;border-radius:20px;border:1.5px solid #e5e7eb;background:#fff;font-size:13px;font-weight:600;color:#374151;cursor:pointer;transition:all .15s;}
.filter-bar button.active,.filter-bar button:hover{background:#004d99;border-color:#004d99;color:#fff;}

.eq-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:32px;}
@media(max-width:1100px){.eq-grid{grid-template-columns:repeat(2,1fr);}}
@media(max-width:700px){.eq-grid{grid-template-columns:1fr;}}

.eq-card{background:#fff;border:1px solid #e8eaf0;border-radius:14px;overflow:hidden;display:flex;flex-direction:column;transition:box-shadow .22s,transform .22s;}
.eq-card:hover{box-shadow:0 8px 30px rgba(26,54,110,.10);transform:translateY(-2px);}
.eq-card-img{height:180px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;overflow:hidden;position:relative;}
.eq-card-img img{width:100%;height:100%;object-fit:contain;padding:12px;transition:transform .4s;}
.eq-card:hover .eq-card-img img{transform:scale(1.05);}
.eq-card-img .no-img{display:flex;flex-direction:column;align-items:center;justify-content:center;color:#d1d5db;gap:8px;}
.eq-card-img .no-img .material-symbols-outlined{font-size:48px;}
.eq-badge{position:absolute;top:10px;left:10px;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;}
.eq-badge.disponible{background:#dcfce7;color:#15803d;}
.eq-badge.loue{background:#fee2e2;color:#dc2626;}
.eq-badge.maintenance{background:#fef9c3;color:#92400e;}
.eq-card-body{padding:16px;flex:1;display:flex;flex-direction:column;gap:6px;}
.eq-card-ref{font-size:11px;font-weight:700;color:#0ea5e9;text-transform:uppercase;letter-spacing:.06em;}
.eq-card-nom{font-family:'Manrope',sans-serif;font-size:15px;font-weight:800;color:#111827;line-height:1.2;}
.eq-card-cat{font-size:12px;color:#9ca3af;display:flex;align-items:center;gap:5px;}
.eq-card-cat .material-symbols-outlined{font-size:14px;}
.eq-card-prix{font-size:15px;font-weight:700;color:#1a56db;margin-top:4px;}
.eq-card-actions{display:flex;gap:8px;padding:12px 16px;border-top:1px solid #f3f4f6;}
.btn-eq-edit{width:38px;height:38px;display:flex;align-items:center;justify-content:center;padding:0;border-radius:8px;background:#eff6ff;border:1px solid #bfdbfe;color:#1a56db;cursor:pointer;transition:background .18s;flex-shrink:0;}
.btn-eq-edit:hover{background:#dbeafe;}
.btn-eq-edit .material-symbols-outlined{font-size:16px;}
.btn-eq-del{width:38px;height:38px;border-radius:8px;background:#fff5f5;border:1px solid #fecaca;color:#dc2626;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .18s;}
.btn-eq-del:hover{background:#fee2e2;}
.btn-eq-del .material-symbols-outlined{font-size:17px;}

.empty-eq{grid-column:1/-1;text-align:center;padding:60px 20px;color:#9ca3af;}
.empty-eq .material-symbols-outlined{font-size:56px;display:block;margin-bottom:12px;color:#d1d5db;}
.empty-eq h3{font-family:'Manrope',sans-serif;font-size:18px;color:#374151;margin-bottom:8px;}

.upload-zone{border:2px dashed #c7d2fe;border-radius:10px;padding:20px;text-align:center;cursor:pointer;transition:border-color .15s,background .15s;margin-bottom:4px;}
.upload-zone:hover,.upload-zone.has-file{border-color:#004d99;background:#f0f5ff;}
.upload-zone input[type="file"]{display:none;}
.upload-icon-big{font-size:32px;color:#1a56db;display:block;margin-bottom:6px;}
.upload-preview-img{width:70px;height:70px;object-fit:cover;border-radius:8px;margin:0 auto 6px;display:block;border:2px solid #16a34a;}

/* ✅ Zone analyse IA */
.ai-analyzing {
  display:none;
  margin-top:12px;
  padding:12px 16px;
  background:#f0f6ff;
  border:1px solid #bfdbfe;
  border-radius:10px;
  align-items:center;
  gap:10px;
  animation: fadeIn .3s ease;
}
@keyframes fadeIn{from{opacity:0;transform:translateY(-5px)}to{opacity:1;transform:translateY(0)}}
.ai-analyzing.show { display:flex; }
.ai-spinner{width:18px;height:18px;border:2.5px solid #bfdbfe;border-top-color:#1d4ed8;border-radius:50%;animation:spin .7s linear infinite;flex-shrink:0;}
@keyframes spin{to{transform:rotate(360deg)}}
.ai-success {
  display:none;
  margin-top:10px;
  padding:10px 14px;
  background:#f0fdf4;
  border:1px solid #bbf7d0;
  border-radius:10px;
  align-items:center;
  gap:8px;
  font-size:13px;
  color:#15803d;
  font-weight:600;
}
.ai-success.show { display:flex; }
.ai-badge {
  display:inline-flex;align-items:center;gap:5px;
  padding:3px 10px;border-radius:20px;
  background:linear-gradient(135deg,#004d99,#0ea5e9);
  color:#fff;font-size:11px;font-weight:700;
  margin-bottom:8px;
}
.ai-badge .material-symbols-outlined{font-size:13px;}

.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(4px);z-index:1000;align-items:center;justify-content:center;}
.modal-overlay.open{display:flex;}
.modal-box{background:#fff;border-radius:16px;padding:28px 32px 24px;width:540px;max-width:96vw;max-height:90vh;overflow-y:auto;box-shadow:0 24px 64px rgba(0,0,0,.18);animation:slideUp .22s ease;}
@keyframes slideUp{from{transform:translateY(24px);opacity:0;}to{transform:translateY(0);opacity:1;}}
.modal-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;}
.modal-header h2{font-family:'Manrope',sans-serif;font-size:17px;font-weight:800;color:#111827;}
.modal-close{background:none;border:none;cursor:pointer;color:#6b7280;padding:4px;border-radius:6px;display:flex;align-items:center;}
.modal-close:hover{background:#f3f4f6;color:#374151;}
.modal-field{display:flex;flex-direction:column;margin-bottom:14px;}
.modal-field label{font-size:12px;font-weight:600;color:#6b7280;margin-bottom:5px;}
.modal-input{padding:9px 13px;background:#f9fafb;border:1.5px solid #e5e7eb;border-radius:8px;font-size:13.5px;font-family:'Inter',sans-serif;color:#111827;outline:none;transition:border-color .15s,box-shadow .15s,background .2s;width:100%;}
.modal-input:focus{border-color:#004d99;box-shadow:0 0 0 3px rgba(0,77,153,.10);}
/* ✅ Champ rempli par l'IA */
.modal-input.ai-filled {
  border-color:#16a34a;
  background:#f0fdf4;
  box-shadow:0 0 0 3px rgba(22,163,74,.10);
}
.modal-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:0;}
.modal-footer{display:flex;justify-content:flex-end;gap:10px;margin-top:20px;padding-top:16px;border-top:1px solid #f3f4f6;}
.btn-save{display:flex;align-items:center;gap:6px;padding:10px 22px;background:#004d99;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;transition:background .15s;}
.btn-save:hover{background:#00357a;}
.btn-save:disabled{opacity:.6;cursor:not-allowed;}
.btn-cancel{padding:10px 18px;background:#f3f4f6;color:#374151;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:background .15s;}
.btn-cancel:hover{background:#e5e7eb;}
.modal-divider{border:none;border-top:1px solid #f3f4f6;margin:14px 0;}
.modal-section-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9ca3af;margin-bottom:10px;display:block;}
</style>

    <!-- Page Header -->
    <section class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-extrabold text-on-surface font-headline tracking-tight mb-2">Équipements</h2>
            <p class="text-secondary font-body max-w-md">Gérez l'inventaire des équipements médicaux et leur disponibilité.</p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <button onclick="ouvrirModaleAjout()" class="flex items-center gap-2 bg-primary px-4 py-2 rounded-xl text-sm font-bold text-white hover:bg-primary/90 transition-colors shadow-sm hover-lift">
                <span class="material-symbols-outlined text-lg">add</span>
                <span>Nouvel Équipement</span>
            </button>
        </div>
    </section>

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
            <div class="eq-card-img">
              <?php if ($imgUrl): ?>
                <img src="<?= $imgUrl ?>" alt="<?= htmlspecialchars($eq['nom']) ?>" loading="lazy"
                     onerror="this.parentElement.innerHTML='<div class=\'no-img\'><span class=\'material-symbols-outlined\'>hide_image</span><span>Pas d\'image</span></div>'"/>
              <?php else: ?>
                <div class="no-img">
                  <span class="material-symbols-outlined">add_photo_alternate</span>
                  <span>Pas d'image</span>
                </div>
              <?php endif; ?>
              <span class="eq-badge <?= $eq['statut'] ?>"><?= $statLbl ?></span>
            </div>
            <div class="eq-card-body">
              <div class="eq-card-ref"><?= htmlspecialchars($eq['reference']) ?></div>
              <div class="eq-card-nom"><?= htmlspecialchars($eq['nom']) ?></div>
              <div class="eq-card-cat">
                <span class="material-symbols-outlined"><?= $catIco ?></span>
                <?= htmlspecialchars($eq['categorie']) ?>
              </div>
              <div class="eq-card-prix"><?= $prixDT ?> DT / jour</div>
            </div>
            <div class="eq-card-actions">
              <button class="btn-eq-edit" type="button" onclick='ouvrirModaleModifier(<?= $eqJson ?>)' title="Modifier">
                <span class="material-symbols-outlined">edit</span>
              </button>
              <button class="btn-eq-del" type="button"
                      onclick="supprimerEquipement(<?= (int)$eq['id'] ?>, '<?= htmlspecialchars($eq['nom'], ENT_QUOTES) ?>')">
                <span class="material-symbols-outlined">delete</span>
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

    </div>

  </main>
</div>

<!-- ══ MODALE AJOUTER ══ -->
<div id="modal-ajout" class="modal-overlay">
  <div class="modal-box">
    <div class="modal-header">
      <h2>➕ Nouvel Équipement</h2>
      <button class="modal-close" onclick="fermerModaleAjout()" type="button">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>

    <!-- ✅ Section upload image EN PREMIER avec analyse IA -->
    <span class="modal-section-label">
      <span class="ai-badge">
        <span class="material-symbols-outlined">auto_awesome</span>
        Analyse IA
      </span>
      Photo de l'équipement — L'IA remplit les champs automatiquement
    </span>

    <div class="upload-zone" id="upload-zone-ajout"
         onclick="document.getElementById('aj-image').click()">
      <input type="file" id="aj-image" accept=".jpg,.jpeg,.png,.webp"
             onchange="previewEtAnalyser(this)"/>
      <div id="upload-placeholder-ajout">
        <span class="material-symbols-outlined upload-icon-big">add_photo_alternate</span>
        <div style="font-size:13px;font-weight:600;color:#374151;">Uploadez une photo</div>
        <div style="font-size:11px;color:#9ca3af;margin-top:4px;">L'IA identifie l'équipement et remplit les champs ✨</div>
      </div>
      <div id="upload-preview-ajout" style="display:none;">
        <img id="upload-img-ajout" class="upload-preview-img" src="" alt="Aperçu"/>
        <div id="upload-name-ajout" style="font-size:12px;color:#16a34a;font-weight:600;"></div>
        <div style="font-size:11px;color:#9ca3af;margin-top:2px;">Cliquez pour changer</div>
      </div>
    </div>

    <!-- Spinner analyse IA -->
    <div class="ai-analyzing" id="ai-analyzing">
      <div class="ai-spinner"></div>
      <div>
        <div style="font-size:13px;font-weight:700;color:#1d4ed8;" id="ai-step">Analyse de l'image en cours...</div>
        <div style="font-size:11px;color:#6b7280;margin-top:2px;">L'IA identifie l'équipement et suggère les informations</div>
      </div>
    </div>

    <!-- Message succès IA -->
    <div class="ai-success" id="ai-success">
      <span class="material-symbols-outlined" style="font-size:18px;">check_circle</span>
      <span>Champs remplis automatiquement par l'IA — Vérifiez et corrigez si nécessaire</span>
    </div>

    <hr class="modal-divider"/>
    <span class="modal-section-label">Informations</span>

    <div class="modal-field">
      <label for="aj-nom">Nom de l'équipement <span style="color:#dc2626;">*</span></label>
      <input id="aj-nom" class="modal-input" type="text" placeholder="L'IA remplit ce champ automatiquement..."/>
    </div>

    <div class="modal-row">
      <div class="modal-field">
        <label for="aj-reference">Référence <span style="color:#dc2626;">*</span></label>
        <input id="aj-reference" class="modal-input" type="text" placeholder="Ex: EQ-5001"/>
      </div>
      <div class="modal-field">
        <label for="aj-prix">Prix / jour (DT) <span style="color:#dc2626;">*</span></label>
        <input id="aj-prix" class="modal-input" type="text" placeholder="L'IA suggère un prix..."/>
      </div>
    </div>

    <div class="modal-field">
      <label for="aj-categorie">Catégorie <span style="color:#dc2626;">*</span></label>
      <select id="aj-categorie" class="modal-input">
        <option value="">-- L'IA sélectionne automatiquement --</option>
        <option value="Mobilité">Mobilité</option>
        <option value="Respiratoire">Respiratoire</option>
        <option value="Cardiologie">Cardiologie</option>
        <option value="Réanimation">Réanimation</option>
        <option value="Gériatrie">Gériatrie</option>
        <option value="Radiologie">Radiologie</option>
      </select>
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

<!-- ══ MODALE MODIFIER ══ -->
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
          <option value="disponible">Disponible</option>
          <option value="loue">🔴 Loué</option>
          <option value="maintenance">🔧 Maintenance</option>
        </select>
      </div>
    </div>
    <hr class="modal-divider"/>
    <span class="modal-section-label">Changer la photo</span>
    <div class="upload-zone" id="upload-zone-mod" onclick="document.getElementById('mod-image').click()">
      <input type="file" id="mod-image" accept=".jpg,.jpeg,.png,.webp" onchange="previewImage(this, 'modifier')"/>
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

<div class="toast-container" style="position:fixed;bottom:24px;right:24px;display:flex;flex-direction:column;gap:10px;z-index:9999;"></div>

<script>
  const API_EQ = '/integration/equipment/api/equipements';
  const TODAY  = '<?= $today ?>';

  /* ════════════════════════════════════════════════════
     ✅ FONCTIONNALITÉ MÉTIER — ANALYSE IA DE L'IMAGE
     Quand l'admin uploade une photo :
     1. Prévisualisation immédiate
     2. Envoi à OpenRouter (openrouter/free — sélection auto du meilleur modèle vision gratuit)
     3. Réponse JSON : nom, référence, catégorie, prix
     4. Remplissage automatique des champs avec animation
  ════════════════════════════════════════════════════ */
  let imageBase64 = null;
  let imageMime   = null;

  const OPENROUTER_KEY = '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'; // MediFlow

  const AI_STEPS = [
    "Identification de l'équipement médical...",
    'Détermination de la catégorie...',
    'Estimation du prix de location en DT...',
    'Génération de la référence...',
    'Finalisation...'
  ];

  async function previewEtAnalyser(input) {
    const file = input.files[0];
    if (!file) return;

    imageMime = file.type || 'image/jpeg';

    const reader = new FileReader();
    reader.onload = async function(e) {
      const dataUrl = e.target.result;
      imageBase64   = dataUrl.split(',')[1];

      // Afficher l'aperçu
      document.getElementById('upload-img-ajout').src           = dataUrl;
      document.getElementById('upload-name-ajout').textContent  = file.name;

      // ✅ Référence extraite du nom du fichier (ex: EQ-1108.jpg → EQ-1108)
      const fileRef = file.name.replace(/\.[^/.]+$/, '').toUpperCase();
      remplirChamp('aj-reference', fileRef);
      document.getElementById('upload-placeholder-ajout').style.display = 'none';
      document.getElementById('upload-preview-ajout').style.display     = 'block';
      document.getElementById('upload-zone-ajout').classList.add('has-file');

      // Cacher ancien succès/erreur
      document.getElementById('ai-success').classList.remove('show');

      // Lancer l'analyse IA
      await analyserAvecIA();
    };
    reader.readAsDataURL(file);
  }

  async function analyserAvecIA() {
    const analyzing = document.getElementById('ai-analyzing');
    const stepEl    = document.getElementById('ai-step');
    const success   = document.getElementById('ai-success');

    analyzing.classList.add('show');
    success.classList.remove('show');

    // Animer les étapes
    let stepIdx = 0;
    const interval = setInterval(() => {
      stepEl.textContent = AI_STEPS[stepIdx % AI_STEPS.length];
      stepIdx++;
    }, 900);

    try {
      // API OpenRouter - essaie plusieurs modeles vision gratuits
      var VISION_MODELS = [
        'google/gemma-4-31b-it:free',
        'google/gemma-4-26b-a4b-it:free',
        'qwen/qwen2.5-vl-32b-instruct:free',
        'qwen/qwen2.5-vl-72b-instruct:free',
        'google/gemma-3-27b-it:free'
      ];

      var promptText = 'Tu es un expert en equipements medicaux tunisiens. ' +
        'Analyse cette image et retourne UNIQUEMENT un objet JSON valide, sans texte avant ou apres, sans backticks. ' +
        'Format exact : {"nom":"nom en francais","categorie":"Mobilite","prix":50} ' +
        'Regles : nom = nom precis en francais, ' +
        'categorie = EXACTEMENT parmi : Mobilite, Respiratoire, Cardiologie, Reanimation, Geriatrie, Radiologie, ' +
        'prix = entier entre 5 et 500 en DT/jour. ' +
        'Si pas un equipement medical : {"erreur":"pas un equipement medical"}';

      var imageDataUrl = 'data:' + imageMime + ';base64,' + imageBase64;

      var msgBody = {
        max_tokens: 300,
        temperature: 0.1,
        messages: [{
          role: 'user',
          content: [
            { type: 'image_url', image_url: { url: imageDataUrl } },
            { type: 'text', text: promptText }
          ]
        }]
      };

      var rawText = '';
      var lastErr = '';
      for (var mi = 0; mi < VISION_MODELS.length; mi++) {
        var currentModel = VISION_MODELS[mi];
        try {
          var reqBody = Object.assign({ model: currentModel }, msgBody);
          var resp = await fetch('https://openrouter.ai/api/v1/chat/completions', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Authorization': 'Bearer ' + OPENROUTER_KEY,
              'HTTP-Referer': window.location.origin,
              'X-Title': 'MediFlow Equipment Analyzer'
            },
            body: JSON.stringify(reqBody)
          });
          if (!resp.ok) {
            lastErr = 'HTTP ' + resp.status + ' (' + currentModel + ')';
            // Attendre 500ms avant de réessayer si rate limit
            if (resp.status === 429) await new Promise(r => setTimeout(r, 800));
            continue;
          }
          var d = await resp.json();
          var txt = '';
          if (d && d.choices && d.choices[0] && d.choices[0].message) {
            txt = d.choices[0].message.content || '';
          }
          if (txt && txt.trim().length > 5) { rawText = txt; break; }
          lastErr = 'Reponse vide (' + currentModel + ')';
        } catch(fetchErr) { lastErr = fetchErr.message; }
      }

      clearInterval(interval);

      if (!rawText) throw new Error('Tous les modeles ont echoue : ' + lastErr);

      // ✅ Nettoyage robuste
      let cleanText = rawText
        .replace(/```json/gi, '')
        .replace(/```/g, '')
        .trim();

      // Extraire le JSON
      const jsonMatch = cleanText.match(/\{[\s\S]*\}/);
      if (!jsonMatch) {
        console.error('Réponse brute :', rawText);
        throw new Error('Aucun JSON trouvé dans la réponse');
      }

      let result;
      try {
        result = JSON.parse(jsonMatch[0]);
      } catch(parseErr) {
        console.error('JSON invalide :', jsonMatch[0]);
        throw new Error('JSON malformé reçu');
      }

      analyzing.classList.remove('show');

      if (result.erreur) {
        showToast('⚠️ ' + result.erreur, 'error');
        return;
      }

      // Valider que les champs essentiels existent
      if (!result.nom) throw new Error('Champ "nom" manquant dans la réponse');

      // ✅ Remplir les champs avec animation verte
      remplirChamp('aj-nom',       String(result.nom       || ''));
      // Référence déjà remplie depuis le nom du fichier
      remplirChamp('aj-prix',      String(result.prix      || ''));

      // Sélectionner la catégorie — correspondance avec ou sans accents
      const select = document.getElementById('aj-categorie');
      const catMap = {
        'mobilite':'Mobilité','mobilité':'Mobilité','mobility':'Mobilité','mobile':'Mobilité',
        'respiratoire':'Respiratoire','respiratory':'Respiratoire','respiration':'Respiratoire','pulmonaire':'Respiratoire',
        'cardiologie':'Cardiologie','cardiology':'Cardiologie','cardiaque':'Cardiologie','cardiac':'Cardiologie',
        'reanimation':'Réanimation','réanimation':'Réanimation','icu':'Réanimation','soins intensifs':'Réanimation',
        'geriatrie':'Gériatrie','gériatrie':'Gériatrie','geriatrics':'Gériatrie','geriatrique':'Gériatrie',
        'radiologie':'Radiologie','radiology':'Radiologie','radio':'Radiologie','imagerie':'Radiologie'
      };
      const catRaw = (result.categorie || '').toLowerCase().trim()
        .replace(/[àáâã]/g,'a').replace(/[éèêë]/g,'e').replace(/[îï]/g,'i')
        .replace(/[ôõ]/g,'o').replace(/[ùûü]/g,'u');
      let catFound = null;
      for (const key in catMap) {
        const keyN = key.replace(/[àáâã]/g,'a').replace(/[éèêë]/g,'e').replace(/[îï]/g,'i').replace(/[ôõ]/g,'o').replace(/[ùûü]/g,'u');
        if (catRaw === keyN || catRaw.includes(keyN) || keyN.includes(catRaw)) {
          catFound = catMap[key]; break;
        }
      }
      if (catFound) {
        select.value = catFound;
        select.classList.add('ai-filled');
        setTimeout(() => select.classList.remove('ai-filled'), 3000);
      }

      success.classList.add('show');
      showToast('✅ Champs remplis automatiquement par l\'IA !', 'success');

    } catch(err) {
      clearInterval(interval);
      analyzing.classList.remove('show');
      console.error('Erreur IA complète :', err);
      showToast('Erreur analyse IA : ' + err.message, 'error');
    }
  }

  // Remplir un champ avec animation verte
  function remplirChamp(id, valeur) {
    const input = document.getElementById(id);
    if (!input) return;
    input.value = valeur;
    input.classList.add('ai-filled');
    setTimeout(() => input.classList.remove('ai-filled'), 3000);
  }

  /* ── Preview image (modifier) ── */
  function previewImage(input, mode) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('upload-img-mod').src          = e.target.result;
      document.getElementById('upload-name-mod').textContent = file.name;
      document.getElementById('upload-placeholder-mod').style.display = 'none';
      document.getElementById('upload-preview-mod').style.display     = 'block';
      document.getElementById('upload-zone-mod').classList.add('has-file');
    };
    reader.readAsDataURL(file);
  }

  /* ── Erreurs ── */
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

  /* ── Validation ── */
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
    if (!nom || nom.length < 2)                                     { afficherErr(prefix+'-nom',      'Nom obligatoire.'); ok = false; }
    if (!ref || ref.length < 2)                                     { afficherErr(prefix+'-reference', 'Référence obligatoire.'); ok = false; }
    if (!prix || isNaN(parseFloat(prix)) || parseFloat(prix) <= 0) { afficherErr(prefix+'-prix',      'Prix doit être positif.'); ok = false; }
    if (!cat)                                                        { afficherErr(prefix+'-categorie', 'Catégorie obligatoire.'); ok = false; }
    if (!ok) showToast('Veuillez corriger les erreurs.', 'error');
    return ok;
  }

  /* ── Modale ajout ── */
  function ouvrirModaleAjout() {
    ['aj-nom','aj-reference','aj-prix'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('aj-categorie').value = '';
    document.getElementById('aj-image').value     = '';
    document.getElementById('upload-placeholder-ajout').style.display = 'block';
    document.getElementById('upload-preview-ajout').style.display     = 'none';
    document.getElementById('upload-zone-ajout').classList.remove('has-file');
    document.getElementById('ai-analyzing').classList.remove('show');
    document.getElementById('ai-success').classList.remove('show');
    document.querySelectorAll('#modal-ajout .msg-erreur').forEach(e => e.remove());
    imageBase64 = null; imageMime = null;
    document.getElementById('modal-ajout').classList.add('open');
  }
  function fermerModaleAjout() {
    document.getElementById('modal-ajout').classList.remove('open');
  }

  document.getElementById('btn-save-ajout').addEventListener('click', async () => {
    if (!validerChamps('aj')) return;
    const btn = document.getElementById('btn-save-ajout');
    btn.disabled = true; btn.textContent = 'Enregistrement...';
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
      try { json = JSON.parse(text); } catch(e) { showToast('Erreur PHP : ' + text.substring(0, 120), 'error'); btn.disabled=false; btn.innerHTML='<span class="material-symbols-outlined" style="font-size:16px;vertical-align:middle;">save</span> Enregistrer'; return; }
      if (json.success) {
        showToast('Équipement ajouté !', 'success');
        fermerModaleAjout();
        setTimeout(() => location.reload(), 1400);
      } else {
        showToast('Erreur : ' + (json.message || ''), 'error');
        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:16px;vertical-align:middle;">save</span> Enregistrer';
      }
    } catch(e) {
      showToast('Erreur réseau.', 'error');
      btn.disabled = false;
      btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:16px;vertical-align:middle;">save</span> Enregistrer';
    }
  });

  ['aj-nom','aj-reference','aj-prix'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', () => effacerErr(id));
  });

  /* ── Modale modifier ── */
  function ouvrirModaleModifier(eq) {
    document.getElementById('mod-id').value        = eq.id;
    document.getElementById('mod-nom').value       = eq.nom       || '';
    document.getElementById('mod-reference').value = eq.reference || '';
    document.getElementById('mod-prix').value      = eq.prix_jour || '';
    document.getElementById('mod-categorie').value = eq.categorie || '';
    document.getElementById('mod-statut').value    = eq.statut    || 'disponible';
    document.getElementById('mod-image').value     = '';
    document.getElementById('upload-placeholder-mod').style.display = 'block';
    document.getElementById('upload-preview-mod').style.display     = 'none';
    document.getElementById('upload-zone-mod').classList.remove('has-file');
    document.querySelectorAll('#modal-modifier .msg-erreur').forEach(e => e.remove());
    document.getElementById('modal-modifier').classList.add('open');
  }
  function fermerModaleModifier() {
    document.getElementById('modal-modifier').classList.remove('open');
  }

  document.getElementById('btn-save-modifier').addEventListener('click', async () => {
    if (!validerChamps('mod')) return;
    const id  = document.getElementById('mod-id').value;
    const btn = document.getElementById('btn-save-modifier');
    const imgFile = document.getElementById('mod-image').files[0];
    if (imgFile) {
      btn.disabled = true; btn.textContent = 'Enregistrement...';
      const formData = new FormData();
      formData.append('nom',       document.getElementById('mod-nom').value.trim());
      formData.append('reference', document.getElementById('mod-reference').value.trim());
      formData.append('prix_jour', document.getElementById('mod-prix').value.trim());
      formData.append('categorie', document.getElementById('mod-categorie').value);
      formData.append('statut',    document.getElementById('mod-statut').value);
      formData.append('image',     imgFile);
      await fetch(`${API_EQ}?id=${id}`, { method: 'DELETE' });
      const res  = await fetch(API_EQ, { method: 'POST', body: formData });
      const text = await res.text();
      try {
        const json = JSON.parse(text);
        if (json.success) { showToast('Équipement modifié !', 'success'); fermerModaleModifier(); setTimeout(() => location.reload(), 1400); }
        else showToast('Erreur : ' + (json.message || ''), 'error');
      } catch(e) { showToast('Erreur serveur.', 'error'); }
      btn.disabled = false;
      btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:16px;vertical-align:middle;">save</span> Enregistrer';
    } else {
      const data = {
        reference: document.getElementById('mod-reference').value.trim(),
        nom:       document.getElementById('mod-nom').value.trim(),
        categorie: document.getElementById('mod-categorie').value,
        prix_jour: parseFloat(document.getElementById('mod-prix').value),
        statut:    document.getElementById('mod-statut').value,
        image:     null,
      };
      try {
        const res  = await fetch(`${API_EQ}?id=${id}`, { method:'PUT', headers:{'Content-Type':'application/json'}, body:JSON.stringify(data) });
        const json = await res.json();
        if (json.success) { showToast('Équipement modifié !', 'success'); fermerModaleModifier(); setTimeout(() => location.reload(), 1400); }
        else showToast('Erreur : ' + (json.message || ''), 'error');
      } catch(e) { showToast('Erreur réseau.', 'error'); }
    }
  });

  ['mod-nom','mod-reference','mod-prix'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', () => effacerErr(id));
  });

  /* ── Supprimer ── */
  async function supprimerEquipement(id, nom) {
    if (!confirm(`⚠️ Supprimer "${nom}" ? Action irréversible.`)) return;
    try {
      const res  = await fetch(`${API_EQ}?id=${id}`, { method: 'DELETE' });
      const json = await res.json();
      if (json.success) {
        showToast('Équipement supprimé.', 'success');
        const card = document.getElementById('eq-' + id);
        if (card) { card.style.transition='opacity .3s,transform .3s'; card.style.opacity='0'; card.style.transform='scale(.95)'; setTimeout(()=>card.remove(),300); }
      } else showToast('Erreur : ' + (json.message || ''), 'error');
    } catch(e) { showToast('Erreur réseau.', 'error'); }
  }

  /* ── Filtres ── */
  document.querySelectorAll('[data-filter-eq]').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('[data-filter-eq]').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const filter = btn.dataset.filterEq;
      document.querySelectorAll('.eq-card').forEach(card => {
        card.style.display = (filter === 'all' || card.dataset.statut === filter) ? '' : 'none';
      });
    });
  });

  /* ── Recherche ── */
  document.getElementById('search-input')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.eq-card').forEach(card => {
      card.style.display = card.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  });

  /* ── Fermer modales ── */
  ['modal-ajout','modal-modifier'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
      if (e.target === this) this.classList.remove('open');
    });
  });
</script>
<script>
function showToast(msg,type='info'){
  const c=document.querySelector('.toast-container');
  const t=document.createElement('div');
  const icons={success:'check_circle',error:'error',info:'info'};
  t.innerHTML=`<span class="material-symbols-outlined" style="font-size:18px;">${icons[type]||'info'}</span><span>${msg}</span>`;
  const colors={success:'#16a34a',error:'#dc2626',info:'#004d99'};
  const textColors={success:'#15803d',error:'#dc2626',info:'#004d99'};
  t.style.cssText=`display:flex;align-items:center;gap:10px;padding:12px 18px;border-radius:10px;background:#fff;box-shadow:0 4px 20px rgba(0,0,0,.12);font-size:13.5px;font-weight:600;font-family:'Inter',sans-serif;border-left:4px solid ${colors[type]||colors.info};color:${textColors[type]||textColors.info};`;
  c.appendChild(t);
  setTimeout(()=>{t.style.opacity='0';t.style.transition='opacity .3s';setTimeout(()=>t.remove(),300);},3500);
}
</script>