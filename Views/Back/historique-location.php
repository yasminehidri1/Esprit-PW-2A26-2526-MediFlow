<?php
// $data injected by PatientEquipmentController::historiqueLocation()
$reservations = $data['reservations'] ?? [];
$equipements  = $data['equipements']  ?? [];
$user         = $data['currentUser']   ?? ($_SESSION['user'] ?? []);

// GROUPER les réservations par locataire_nom
$groupes = [];
foreach ($reservations as $r) {
    $nom = trim($r['locataire_nom'] ?? 'Inconnu');
    $groupes[$nom][] = $r;
}
// Trier les groupes par nom alphabétique
ksort($groupes);

// Stats globales
$totalRes = count($reservations);
$enCours  = count(array_filter($reservations, fn($r) => $r['statut'] === 'en_cours'));
$termines = count(array_filter($reservations, fn($r) => $r['statut'] === 'termine'));
$enRetard = count(array_filter($reservations, fn($r) => $r['statut'] === 'en_retard'));

function getBadgeClass($s) { return ['termine'=>'termine','en_cours'=>'encours','en_retard'=>'retard'][$s]??'encours'; }
function getBadgeLabel($s) { return ['termine'=>'Terminé','en_cours'=>'En cours','en_retard'=>'En retard'][$s]??'—'; }
function getCatClass($c)   { return ['Cardiologie'=>'cardio','Réanimation'=>'reani','Gériatrie'=>'geriat','Radiologie'=>'radio','Mobilité'=>'mobi','Respiratoire'=>'resp'][$c]??'cardio'; }
function getCatIcon($c)    { return ['Cardiologie'=>'monitor_heart','Réanimation'=>'air','Gériatrie'=>'chair','Radiologie'=>'radiology','Mobilité'=>'accessibility_new','Respiratoire'=>'air'][$c]??'medical_services'; }
function fmtDate($d)       { if(!$d)return 'En cours'; return (new DateTime($d))->format('d M Y'); }

// Détecter si c'est un Cabinet/Hôpital/Clinique
function getTypeIcon($nom) {
    $low = strtolower($nom);
    if (str_contains($low,'cabinet'))  return 'business';
    if (str_contains($low,'hôpital') || str_contains($low,'hopital')) return 'local_hospital';
    if (str_contains($low,'clinique')) return 'medical_services';
    if (str_contains($low,'ehpad'))    return 'home_health';
    return 'person';
}

$today = date('Y-m-d');
?>
<style>
/* ── Quick stats ── */
.quick-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px;}
@media(max-width:900px){.quick-stats{grid-template-columns:repeat(2,1fr);}}
.qs-card{background:#fff;border:1px solid #e8eaf0;border-radius:12px;padding:16px 18px;display:flex;align-items:center;gap:12px;}
.qs-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.qs-icon .material-symbols-outlined{font-size:20px;}
.qs-icon.blue{background:#dbeafe;color:#1d4ed8;}
.qs-icon.green{background:#dcfce7;color:#16a34a;}
.qs-icon.amber{background:#fef9c3;color:#d97706;}
.qs-icon.red{background:#fee2e2;color:#dc2626;}
.qs-num{font-family:'Manrope',sans-serif;font-size:24px;font-weight:900;color:#111827;line-height:1;}
.qs-lbl{font-size:11px;color:#6b7280;margin-top:2px;}

/* ── Page title ── */
.page-title-row{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;}
.page-title-row h1{font-family:'Manrope',sans-serif;font-size:22px;font-weight:800;color:#111827;}
.page-title-row p{font-size:13px;color:#6b7280;margin-top:2px;}

/* ── Search bar ── */
.search-bar{display:flex;align-items:center;gap:10px;background:#fff;border:1.5px solid #e5e7eb;border-radius:10px;padding:9px 14px;margin-bottom:20px;width:100%;max-width:520px;}
.search-bar .material-symbols-outlined{font-size:18px;color:#9ca3af;}
.search-bar input{border:none;outline:none;font-size:13px;font-family:'Inter',sans-serif;color:#111827;flex:1;background:transparent;}

/* ── Patient bloc ── */
.patient-bloc{background:#fff;border:1px solid #e8eaf0;border-radius:14px;overflow:hidden;margin-bottom:14px;}
.patient-header{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;cursor:pointer;user-select:none;transition:background .15s;}
.patient-header:hover{background:#f9fafb;}
.patient-header-left{display:flex;align-items:center;gap:12px;}
.patient-avatar{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.patient-avatar .material-symbols-outlined{font-size:20px;}
.patient-avatar.person{background:#dbeafe;color:#1d4ed8;}
.patient-avatar.business{background:#e0e7ff;color:#6366f1;}
.patient-avatar.hospital{background:#dcfce7;color:#16a34a;}
.patient-nom{font-family:'Manrope',sans-serif;font-size:14px;font-weight:800;color:#111827;}
.patient-meta{display:flex;align-items:center;gap:6px;font-size:12px;color:#6b7280;margin-top:2px;}
.patient-meta-dot{width:3px;height:3px;border-radius:50%;background:#d1d5db;}
.patient-header-right{display:flex;align-items:center;gap:8px;}
.patient-count{font-family:'Manrope',sans-serif;font-size:12px;font-weight:700;background:#f3f4f6;color:#374151;padding:2px 8px;border-radius:20px;}
.toggle-icon{font-size:20px;color:#9ca3af;transition:transform .2s;}
.toggle-icon.collapsed{transform:rotate(-90deg);}

/* ── Patient table ── */
.patient-table-wrap{overflow-x:auto;}
.patient-table-wrap.collapsed{display:none;}
.patient-table{width:100%;border-collapse:collapse;font-size:13px;}
.patient-table thead tr{background:#f9fafb;border-top:1px solid #f3f4f6;}
.patient-table th{padding:10px 14px;text-align:left;font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;}
.patient-table td{padding:12px 14px;border-top:1px solid #f3f4f6;vertical-align:middle;}
.patient-table tbody tr:hover{background:#f9fafb;}

/* ── Table cell helpers ── */
.eq-ref{font-size:11px;font-weight:700;color:#0ea5e9;text-transform:uppercase;letter-spacing:.06em;}
.eq-cell{display:flex;align-items:center;gap:8px;}
.eq-icon{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.eq-icon .material-symbols-outlined{font-size:16px;}
.eq-icon.cardio{background:#fee2e2;color:#dc2626;}
.eq-icon.reani{background:#dbeafe;color:#1d4ed8;}
.eq-icon.geriat{background:#f3e8ff;color:#9333ea;}
.eq-icon.radio{background:#fef9c3;color:#d97706;}
.eq-icon.mobi{background:#dcfce7;color:#16a34a;}
.eq-icon.resp{background:#e0f2fe;color:#0284c7;}
.eq-nom{font-weight:700;color:#111827;font-size:13px;}
.eq-cat{font-size:11px;color:#9ca3af;margin-top:2px;}
.adresse-cell{display:flex;align-items:center;gap:5px;font-size:12px;}
.adresse-cell .material-symbols-outlined{font-size:14px;}
.adresse-cell.livraison{color:#1d4ed8;}
.adresse-cell.retrait{color:#6b7280;}
.date-debut{font-size:12px;font-weight:600;color:#111827;}
.date-arrow{font-size:11px;color:#d1d5db;margin:1px 0;}
.date-fin{font-size:12px;color:#6b7280;}
.actions-cell{display:flex;gap:6px;}
.action-btn{width:30px;height:30px;border-radius:7px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .15s;}
.action-btn .material-symbols-outlined{font-size:15px;}
.action-btn.edit{background:#eff6ff;color:#1d4ed8;}
.action-btn.edit:hover{background:#dbeafe;}
.action-btn.del{background:#fff5f5;color:#dc2626;}
.action-btn.del:hover{background:#fee2e2;}

/* ── Status badge ── */
.badge{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;}
.badge-dot{width:6px;height:6px;border-radius:50%;background:currentColor;opacity:.7;}
.badge.encours{background:#dbeafe;color:#1d4ed8;}
.badge.termine{background:#dcfce7;color:#15803d;}
.badge.retard{background:#fee2e2;color:#dc2626;}

/* ── Export button ── */
.btn-export{display:flex;align-items:center;gap:6px;padding:8px 16px;background:#004d99;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;transition:background .15s;}
.btn-export:hover{background:#00357a;}

/* ── Empty ── */
.empty-global{text-align:center;padding:60px 20px;color:#9ca3af;}
.empty-global .material-symbols-outlined{font-size:56px;display:block;margin-bottom:12px;color:#d1d5db;}

/* ── Modal ── */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(4px);z-index:1000;align-items:center;justify-content:center;}
.modal-overlay.open{display:flex;}
.modal-box{background:#fff;border-radius:16px;padding:28px 32px 24px;width:540px;max-width:96vw;max-height:90vh;overflow-y:auto;box-shadow:0 24px 64px rgba(0,0,0,.18);}
.modal-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;}
.modal-header h2{font-family:'Manrope',sans-serif;font-size:17px;font-weight:800;color:#111827;}
.modal-close{background:none;border:none;cursor:pointer;color:#6b7280;padding:4px;border-radius:6px;display:flex;align-items:center;}
.modal-close:hover{background:#f3f4f6;color:#374151;}
.modal-field{display:flex;flex-direction:column;margin-bottom:14px;}
.modal-field label{font-size:12px;font-weight:600;color:#6b7280;margin-bottom:5px;}
.modal-input{padding:9px 13px;background:#f9fafb;border:1.5px solid #e5e7eb;border-radius:8px;font-size:13.5px;font-family:'Inter',sans-serif;color:#111827;outline:none;transition:border-color .15s;width:100%;}
.modal-input:focus{border-color:#004d99;box-shadow:0 0 0 3px rgba(0,77,153,.10);}
.modal-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.modal-footer{display:flex;justify-content:flex-end;gap:10px;margin-top:20px;padding-top:16px;border-top:1px solid #f3f4f6;}
.btn-save-m{display:flex;align-items:center;gap:6px;padding:10px 22px;background:#004d99;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;}
.btn-save-m:hover{background:#00357a;}
.btn-cancel-m{padding:10px 18px;background:#f3f4f6;color:#374151;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;}
.btn-cancel-m:hover{background:#e5e7eb;}
</style>

    <!-- Titre -->
    <div class="page-title-row" style="margin-bottom:20px;">
      <div>
        <h1>Historique par Patient</h1>
        <p>Chaque patient, clinique ou cabinet a son propre historique de location.</p>
      </div>
    </div>

    <!-- Stats rapides -->
    <div class="quick-stats">
      <div class="qs-card">
        <div class="qs-icon blue"><span class="material-symbols-outlined">groups</span></div>
        <div><div class="qs-num"><?= count($groupes) ?></div><div class="qs-lbl">Patients / Cliniques</div></div>
      </div>
      <div class="qs-card">
        <div class="qs-icon green"><span class="material-symbols-outlined">receipt_long</span></div>
        <div><div class="qs-num"><?= $totalRes ?></div><div class="qs-lbl">Total réservations</div></div>
      </div>
      <div class="qs-card">
        <div class="qs-icon amber"><span class="material-symbols-outlined">pending</span></div>
        <div><div class="qs-num"><?= $enCours ?></div><div class="qs-lbl">En cours</div></div>
      </div>
      <div class="qs-card">
        <div class="qs-icon red"><span class="material-symbols-outlined">warning</span></div>
        <div><div class="qs-num"><?= $enRetard ?></div><div class="qs-lbl">En retard</div></div>
      </div>
    </div>

    <!-- Recherche globale -->
    <div class="search-bar">
      <span class="material-symbols-outlined">search</span>
      <input type="text" id="search-global" placeholder="Rechercher un patient, équipement, adresse..."/>
      <span style="font-size:12px;color:#9ca3af;"><?= count($groupes) ?> patient(s)</span>
    </div>

    <!-- ══════════════════════════════════════
         BLOCS PAR PATIENT
    ══════════════════════════════════════ -->
    <?php if (empty($groupes)): ?>
      <div class="empty-global">
        <span class="material-symbols-outlined">inbox</span>
        <p>Aucune réservation enregistrée.</p>
      </div>

    <?php else: ?>
      <?php foreach ($groupes as $nomPatient => $resasPatient): ?>
        <?php
          $typeIcon   = getTypeIcon($nomPatient);
          $avatarClass= ($typeIcon === 'person') ? 'person' : (str_contains(strtolower($nomPatient),'hôpital') || str_contains(strtolower($nomPatient),'hopital') ? 'hospital' : 'business');
          $nbEnCours  = count(array_filter($resasPatient, fn($r) => $r['statut'] === 'en_cours'));
          $nbTermines = count(array_filter($resasPatient, fn($r) => $r['statut'] === 'termine'));
          $nbRetard   = count(array_filter($resasPatient, fn($r) => $r['statut'] === 'en_retard'));
          $slugId     = 'bloc-' . preg_replace('/[^a-z0-9]/', '-', strtolower($nomPatient));
        ?>

        <div class="patient-bloc" data-patient="<?= htmlspecialchars(strtolower($nomPatient)) ?>">

          <!-- En-tête du patient — clic pour replier/déplier -->
          <div class="patient-header" onclick="toggleBloc('<?= $slugId ?>')">
            <div class="patient-header-left">
              <!-- Avatar -->
              <div class="patient-avatar <?= $avatarClass ?>">
                <span class="material-symbols-outlined"><?= $typeIcon ?></span>
              </div>
              <div>
                <div class="patient-nom"><?= htmlspecialchars($nomPatient) ?></div>
                <div class="patient-meta">
                  <span><?= count($resasPatient) ?> réservation(s)</span>
                  <?php if ($nbEnCours > 0): ?>
                    <span class="patient-meta-dot"></span>
                    <span style="color:#1d4ed8;font-weight:600;"><?= $nbEnCours ?> en cours</span>
                  <?php endif; ?>
                  <?php if ($nbRetard > 0): ?>
                    <span class="patient-meta-dot"></span>
                    <span style="color:#dc2626;font-weight:600;"><?= $nbRetard ?> en retard ⚠️</span>
                  <?php endif; ?>
                  <?php if ($nbTermines > 0): ?>
                    <span class="patient-meta-dot"></span>
                    <span style="color:#16a34a;"><?= $nbTermines ?> terminé(s)</span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <div class="patient-header-right">
              <span class="patient-count"><?= count($resasPatient) ?></span>
              <span class="material-symbols-outlined toggle-icon" id="icon-<?= $slugId ?>">
                expand_more
              </span>
            </div>
          </div>

          <!-- Tableau des réservations de ce patient -->
          <div class="patient-table-wrap" id="<?= $slugId ?>">
            <table class="patient-table">
              <thead>
                <tr>
                  <th>Réf.</th>
                  <th>Équipement</th>
                  <th>Adresse / Livraison</th>
                  <th>Téléphone</th>
                  <th>Dates</th>
                  <th>Statut</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($resasPatient as $r): ?>
                  <?php
                    $rJson    = htmlspecialchars(json_encode($r), ENT_QUOTES);
                    $badgeCls = getBadgeClass($r['statut']);
                    $badgeLbl = getBadgeLabel($r['statut']);
                    $catCls   = getCatClass($r['categorie'] ?? '');
                    $catIco   = getCatIcon($r['categorie'] ?? '');
                  ?>
                  <tr>
                    <td><span class="eq-ref"><?= htmlspecialchars($r['reference'] ?? '—') ?></span></td>
                    <td>
                      <div class="eq-cell">
                        <div class="eq-icon <?= $catCls ?>">
                          <span class="material-symbols-outlined"><?= $catIco ?></span>
                        </div>
                        <div>
                          <div class="eq-nom"><?= htmlspecialchars($r['equipement_nom'] ?? '—') ?></div>
                          <div class="eq-cat"><?= htmlspecialchars($r['categorie'] ?? '—') ?></div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <?php if (!empty($r['locataire_ville'])): ?>
                        <div class="adresse-cell livraison">
                          <span class="material-symbols-outlined">location_on</span>
                          <?= htmlspecialchars($r['locataire_ville']) ?>
                        </div>
                      <?php else: ?>
                        <div class="adresse-cell retrait">
                          <span class="material-symbols-outlined">store</span>
                          Retrait clinique
                        </div>
                      <?php endif; ?>
                    </td>
                    <td style="font-size:12px;color:#374151;">
                      <?= htmlspecialchars($r['telephone'] ?? '—') ?>
                    </td>
                    <td>
                      <div class="date-debut"><?= fmtDate($r['date_debut']) ?></div>
                      <div class="date-arrow">↓</div>
                      <div class="date-fin <?= $r['statut']==='en_retard'?'style="color:#dc2626;font-weight:700;"':'' ?>">
                        <?= $r['date_fin'] ? fmtDate($r['date_fin']) : 'En cours' ?>
                      </div>
                    </td>
                    <td>
                      <span class="badge <?= $badgeCls ?>">
                        <span class="badge-dot"></span><?= $badgeLbl ?>
                      </span>
                    </td>
                    <td>
                      <div class="actions-cell">
                        <button class="action-btn edit" title="Modifier"
                                onclick='openEditModal(<?= $rJson ?>)'>
                          <span class="material-symbols-outlined">edit</span>
                        </button>
                        <button class="action-btn del" title="Supprimer"
                                onclick="deleteReservation(<?= (int)$r['id'] ?>)">
                          <span class="material-symbols-outlined">delete</span>
                        </button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

        </div><!-- /patient-bloc -->
      <?php endforeach; ?>
    <?php endif; ?>

<!-- ══ MODALE MODIFIER ══ -->
<div id="modal-overlay" class="modal-overlay">
  <div class="modal-box">
    <div class="modal-header">
      <h2>✏️ Modifier la Réservation</h2>
      <button class="modal-close" onclick="closeModal()" type="button">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>
    <input type="hidden" id="edit-id"/>
    <div class="modal-field">
      <label>Équipement</label>
      <select id="edit-equipement" class="modal-input">
        <?php foreach ($equipements as $eq): ?>
          <option value="<?= (int)$eq['id'] ?>"><?= htmlspecialchars($eq['reference']) ?> — <?= htmlspecialchars($eq['nom']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="modal-row">
      <div class="modal-field">
        <label for="edit-nom">Nom locataire <span style="color:#dc2626;">*</span></label>
        <input id="edit-nom" class="modal-input" type="text"/>
      </div>
      <div class="modal-field">
        <label for="edit-ville">Adresse livraison</label>
        <input id="edit-ville" class="modal-input" type="text" placeholder="Adresse ou ville"/>
      </div>
    </div>
    <div class="modal-field">
      <label for="edit-tel">Téléphone</label>
      <input id="edit-tel" class="modal-input" type="text" placeholder="20 123 456"/>
    </div>
    <div class="modal-row">
      <div class="modal-field">
        <label for="edit-debut">Date début <span style="color:#dc2626;">*</span></label>
        <input id="edit-debut" class="modal-input" type="date"/>
      </div>
      <div class="modal-field">
        <label for="edit-fin">Date fin</label>
        <input id="edit-fin" class="modal-input" type="date"/>
      </div>
    </div>
    <div class="modal-field">
      <label for="edit-statut">Statut</label>
      <select id="edit-statut" class="modal-input">
        <option value="en_cours">🔵 En cours</option>
        <option value="termine">🟢 Terminé</option>
        <option value="en_retard">🔴 En retard</option>
      </select>
    </div>
    <div class="modal-footer">
      <button class="btn-cancel-m" onclick="closeModal()" type="button">Annuler</button>
      <button class="btn-save-m" id="btn-save" type="button">
        <span class="material-symbols-outlined" style="font-size:15px;vertical-align:middle;">save</span>
        Enregistrer
      </button>
    </div>
  </div>
</div>

<div class="toast-container" style="position:fixed;bottom:24px;right:24px;display:flex;flex-direction:column;gap:10px;z-index:9999;"></div>
<script>
  const API_RES = '/integration/equipment/api/reservations';

  /* ── Replier / Déplier un bloc patient ── */
  function toggleBloc(id) {
    const wrap = document.getElementById(id);
    const icon = document.getElementById('icon-' + id);
    if (!wrap) return;
    const isOpen = !wrap.classList.contains('collapsed');
    wrap.classList.toggle('collapsed', isOpen);
    icon.classList.toggle('collapsed', isOpen);
  }

  /* ── Recherche globale ── */
  document.getElementById('search-global').addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    document.querySelectorAll('.patient-bloc').forEach(bloc => {
      const txt = bloc.textContent.toLowerCase();
      bloc.style.display = (!q || txt.includes(q)) ? '' : 'none';
      // Si recherche active → déplier les blocs correspondants
      if (q) {
        const id   = bloc.querySelector('.patient-table-wrap')?.id;
        const icon = bloc.querySelector('.toggle-icon');
        if (id && txt.includes(q)) {
          document.getElementById(id)?.classList.remove('collapsed');
          icon?.classList.remove('collapsed');
        }
      }
    });
  });

  /* ── Modale modifier ── */
  function openEditModal(r) {
    document.getElementById('edit-id').value          = r.id;
    document.getElementById('edit-equipement').value  = r.equipement_id;
    document.getElementById('edit-nom').value         = r.locataire_nom   || '';
    document.getElementById('edit-ville').value       = r.locataire_ville || '';
    document.getElementById('edit-tel').value         = r.telephone       || '';
    document.getElementById('edit-debut').value       = r.date_debut      || '';
    document.getElementById('edit-fin').value         = r.date_fin        || '';
    document.getElementById('edit-statut').value      = r.statut          || 'en_cours';
    document.getElementById('modal-overlay').classList.add('open');
  }

  function closeModal() { document.getElementById('modal-overlay').classList.remove('open'); }
  document.getElementById('modal-overlay').addEventListener('click', e => {
    if (e.target === document.getElementById('modal-overlay')) closeModal();
  });

  document.getElementById('btn-save').addEventListener('click', async () => {
    const id    = document.getElementById('edit-id').value;
    const nom   = document.getElementById('edit-nom').value.trim();
    const debut = document.getElementById('edit-debut').value;
    if (!nom)   { showToast('Nom du locataire requis.', 'error'); return; }
    if (!debut) { showToast('Date de début requise.', 'error');   return; }
    try {
      const res  = await fetch(`${API_RES}?id=${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          equipement_id:   document.getElementById('edit-equipement').value,
          locataire_nom:   nom,
          locataire_ville: document.getElementById('edit-ville').value.trim(),
          telephone:       document.getElementById('edit-tel').value.trim(),
          date_debut:      debut,
          date_fin:        document.getElementById('edit-fin').value || null,
          statut:          document.getElementById('edit-statut').value,
        }),
      });
      const json = await res.json();
      if (json.success) {
        showToast('Réservation modifiée !', 'success');
        closeModal();
        setTimeout(() => location.reload(), 1200);
      } else { showToast('Erreur : ' + (json.message || ''), 'error'); }
    } catch(e) { showToast('Erreur réseau.', 'error'); }
  });

  /* ── Supprimer ── */
  async function deleteReservation(id) {
    if (!confirm('⚠️ Supprimer cette réservation ? Action irréversible.')) return;
    try {
      const res  = await fetch(`${API_RES}?id=${id}`, { method: 'DELETE' });
      const json = await res.json();
      if (json.success) {
        showToast('Réservation supprimée.', 'success');
        setTimeout(() => location.reload(), 1000);
      } else { showToast('Erreur.', 'error'); }
    } catch(e) { showToast('Erreur réseau.', 'error'); }
  }

  /* ── Export CSV ── */
  document.getElementById('btn-export').addEventListener('click', () => {
    const header = 'Patient;Référence;Équipement;Catégorie;Adresse;Téléphone;Début;Fin;Statut\n';
    const rows   = [];
    document.querySelectorAll('.patient-bloc').forEach(bloc => {
      const patient = bloc.querySelector('.patient-nom')?.textContent.trim() || '';
      bloc.querySelectorAll('.patient-table tbody tr').forEach(tr => {
        const td = [...tr.querySelectorAll('td')];
        rows.push([
          patient,
          td[0]?.textContent.trim(),
          td[1]?.querySelector('.eq-nom')?.textContent.trim(),
          td[1]?.querySelector('.eq-cat')?.textContent.trim(),
          td[2]?.textContent.trim(),
          td[3]?.textContent.trim(),
          td[4]?.querySelector('.date-debut')?.textContent.trim(),
          td[4]?.querySelector('.date-fin')?.textContent.trim(),
          td[5]?.textContent.trim(),
        ].join(';'));
      });
    });
    const csv  = header + rows.join('\n');
    const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
    const a    = Object.assign(document.createElement('a'), {
      href: URL.createObjectURL(blob),
      download: `mediflow_historique_${new Date().toISOString().slice(0,10)}.csv`
    });
    a.click(); URL.revokeObjectURL(a.href);
    showToast('CSV exporté avec succès !', 'success');
  });
</script>
<script>
