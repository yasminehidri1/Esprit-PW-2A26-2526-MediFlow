<?php // Views/Back/dossier_medical/demandes_liste.php ?>

<div class="max-w-5xl mx-auto space-y-8">
    <!-- Flash -->
    <?php if (!empty($flash)): ?>
    <div id="flash-msg" class="mb-6 flex items-center gap-3 p-4 rounded-xl fade-in
        <?= $flash['type'] === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' ?>">
        <span class="material-symbols-outlined text-[20px]"><?= $flash['type'] === 'success' ? 'check_circle' : 'info' ?></span>
        <span class="font-medium text-sm"><?= htmlspecialchars($flash['msg']) ?></span>
        <button onclick="this.parentElement.remove()" class="ml-auto opacity-60 hover:opacity-100 transition-opacity">
            <span class="material-symbols-outlined text-sm">close</span>
        </button>
    </div>
    <?php endif ?>

    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between md:items-end mb-10 gap-4 fade-in">
        <div>
            <h1 class="text-4xl font-extrabold text-blue-900 tracking-tighter mb-2 font-headline">
                Demandes d'Ordonnance
            </h1>
            <p class="text-slate-500 font-medium">
                <?php
                $total          = count($demandes);
                $enAttente      = count(array_filter($demandes, fn($d) => $d['statut'] === 'en_attente'));
                $critiques      = count(array_filter($demandes, fn($d) => ($d['ai_urgence'] ?? '') === 'tres_urgent' && $d['statut'] === 'en_attente'));
                ?>
                <span class="text-blue-700 font-bold"><?= $total ?> demande<?= $total > 1 ? 's' : '' ?></span> reçue<?= $total > 1 ? 's' : '' ?>
                <?php if ($enAttente > 0): ?>
                — <span class="text-amber-600 font-bold"><?= $enAttente ?> en attente</span>
                <?php endif ?>
                <?php if ($critiques > 0): ?>
                — <span class="text-red-600 font-bold flex-inline items-center gap-1">
                    <span class="material-symbols-outlined text-sm align-middle">emergency</span>
                    <?= $critiques ?> critique<?= $critiques > 1 ? 's' : '' ?>
                  </span>
                <?php endif ?>
            </p>
        </div>
        <!-- Légende IA -->
        <div class="flex items-center gap-2 text-xs text-slate-400">
            <span class="material-symbols-outlined text-base text-violet-400">smart_toy</span>
            Priorisé par IA
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgba(0,77,153,0.05)] border border-slate-100 overflow-hidden fade-in">

        <?php if (empty($demandes)): ?>
        <div class="px-8 py-20 text-center text-slate-400">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-5 border border-slate-100">
                <span class="material-symbols-outlined text-4xl text-slate-300">inbox</span>
            </div>
            <p class="font-bold text-lg text-slate-700 mb-1 font-headline">Aucune demande reçue</p>
            <p class="text-sm font-medium">Les demandes envoyées par vos patients apparaîtront ici.</p>
        </div>

        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50/60 border-b border-slate-100">
                        <th class="px-8 py-5 text-[11px] uppercase tracking-widest font-bold text-slate-400">Patient</th>
                        <th class="px-6 py-5 text-[11px] uppercase tracking-widest font-bold text-slate-400">Description</th>
                        <th class="px-6 py-5 text-[11px] uppercase tracking-widest font-bold text-slate-400">
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm text-violet-400">smart_toy</span>
                                Urgence IA
                            </span>
                        </th>
                        <th class="px-6 py-5 text-[11px] uppercase tracking-widest font-bold text-slate-400">Date</th>
                        <th class="px-6 py-5 text-[11px] uppercase tracking-widest font-bold text-slate-400">Statut</th>
                        <th class="px-6 py-5 text-[11px] uppercase tracking-widest font-bold text-slate-400 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                <?php foreach ($demandes as $idx => $d):
                    $initials = strtoupper(substr($d['patient_prenom'], 0, 1) . substr($d['patient_nom'], 0, 1));
                    $fullName = htmlspecialchars($d['patient_prenom'] . ' ' . $d['patient_nom']);
                    $dateStr  = date('d/m/Y à H:i', strtotime($d['created_at']));

                    $statutCfg = match($d['statut']) {
                        'en_attente' => ['bg-amber-50 text-amber-700 border-amber-200',       'schedule',      'En attente'],
                        'traitee'    => ['bg-emerald-50 text-emerald-700 border-emerald-200',  'check_circle',  'Traitée'],
                        'refusee'    => ['bg-red-50 text-red-600 border-red-200',              'cancel',        'Refusée'],
                        default      => ['bg-slate-100 text-slate-500 border-slate-200',       'help',          $d['statut']],
                    };
                    [$statutClass, $statutIcon, $statutLabel] = $statutCfg;

                    $avatarColors = ['bg-blue-100 text-blue-700 border-blue-200', 'bg-violet-100 text-violet-700 border-violet-200',
                                     'bg-teal-100 text-teal-700 border-teal-200', 'bg-rose-100 text-rose-700 border-rose-200'];
                    $avatarClass  = $avatarColors[($d['id_demande'] ?? $idx) % count($avatarColors)];

                    // Badge urgence IA
                    $aiUrgence = $d['ai_urgence'] ?? null;
                    $aiJust    = htmlspecialchars($d['ai_justification'] ?? '');
                    $aiBadge   = match($aiUrgence) {
                        'tres_urgent' => ['bg-red-100 text-red-700 border-red-300 ring-1 ring-red-300',       'emergency', 'Très urgent'],
                        'urgent'      => ['bg-amber-100 text-amber-700 border-amber-300 ring-1 ring-amber-200','schedule',  'Urgent'],
                        'normale'     => ['bg-emerald-50 text-emerald-700 border-emerald-200',                 'check_circle','Normale'],
                        default       => ['bg-slate-100 text-slate-400 border-slate-200',                      'hourglass_empty','En analyse…'],
                    };
                    [$aiBadgeClass, $aiBadgeIcon, $aiBadgeLabel] = $aiBadge;

                    // Surlignage ligne si critique en attente
                    $rowHighlight = ($aiUrgence === 'tres_urgent' && $d['statut'] === 'en_attente')
                                    ? 'bg-red-50/40 hover:bg-red-50/60'
                                    : 'hover:bg-slate-50/80';
                ?>
                <tr class="group <?= $rowHighlight ?> transition-colors">

                    <!-- Patient -->
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-4">
                            <?php if ($aiUrgence === 'tres_urgent' && $d['statut'] === 'en_attente'): ?>
                            <div class="relative">
                                <div class="w-11 h-11 rounded-full <?= $avatarClass ?> flex items-center justify-center font-bold text-sm border shadow-sm shrink-0">
                                    <?= $initials ?>
                                </div>
                                <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full flex items-center justify-center">
                                    <span class="material-symbols-outlined text-white text-[10px]">priority_high</span>
                                </span>
                            </div>
                            <?php else: ?>
                            <div class="w-11 h-11 rounded-full <?= $avatarClass ?> flex items-center justify-center font-bold text-sm border shadow-sm shrink-0">
                                <?= $initials ?>
                            </div>
                            <?php endif ?>
                            <div>
                                <p class="font-bold text-slate-800 text-sm font-headline"><?= $fullName ?></p>
                                <p class="text-xs text-slate-500 font-medium"><?= htmlspecialchars($d['patient_mail']) ?></p>
                            </div>
                        </div>
                    </td>

                    <!-- Description -->
                    <td class="px-6 py-5">
                        <div class="max-w-xs whitespace-normal">
                            <p class="text-[13px] font-medium text-slate-600 line-clamp-2" title="<?= htmlspecialchars($d['description']) ?>"><?= htmlspecialchars($d['description']) ?></p>
                        </div>
                    </td>

                    <!-- Urgence IA -->
                    <td class="px-6 py-5">
                        <div class="group/ai relative inline-block">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider border <?= $aiBadgeClass ?>">
                                <span class="material-symbols-outlined text-[13px]"><?= $aiBadgeIcon ?></span>
                                <?= $aiBadgeLabel ?>
                            </span>
                            <?php if (!empty($aiJust)): ?>
                            <div class="absolute left-0 top-full mt-1 z-20 hidden group-hover/ai:block w-56 bg-slate-800 text-white text-xs rounded-xl px-3 py-2 shadow-xl leading-relaxed">
                                <span class="material-symbols-outlined text-violet-300 text-sm align-middle mr-1">smart_toy</span>
                                <?= $aiJust ?>
                            </div>
                            <?php endif ?>
                        </div>
                    </td>

                    <!-- Date -->
                    <td class="px-6 py-5">
                        <span class="text-sm text-slate-700 font-semibold"><?= $dateStr ?></span>
                    </td>

                    <!-- Statut -->
                    <td class="px-6 py-5">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider border <?= $statutClass ?>">
                            <span class="material-symbols-outlined text-[14px]"><?= $statutIcon ?></span>
                            <?= $statutLabel ?>
                        </span>
                    </td>

                    <!-- Actions -->
                    <td class="px-6 py-5 text-right">
                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <?php if ($d['statut'] === 'en_attente'): ?>
                            <form method="POST" action="/integration/dossier/demandes/statut" class="m-0 inline">
                                <input type="hidden" name="id_demande" value="<?= (int)$d['id_demande'] ?>"/>
                                <input type="hidden" name="statut" value="traitee"/>
                                <button type="submit" title="Marquer comme traitée"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center bg-emerald-50 text-emerald-600 hover:bg-emerald-100 hover:text-emerald-700 transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">check</span>
                                </button>
                            </form>
                            <!-- Bouton refus → ouvre le modal IA -->
                            <button type="button"
                                    onclick="openRefusModal(<?= (int)$d['id_demande'] ?>, <?= htmlspecialchars(json_encode($d['description']), ENT_QUOTES) ?>)"
                                    title="Refuser avec message IA"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 transition-colors">
                                <span class="material-symbols-outlined text-[18px]">close</span>
                            </button>
                            <a href="/integration/dossier/nouvelle-consultation?patient_id=<?= $d['id_patient'] ?>" title="Créer une ordonnance/consultation"
                               class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg transition-colors ml-2">
                                <span class="material-symbols-outlined text-[16px]">history_edu</span>
                                Créer Ordo.
                            </a>
                            <?php else: ?>
                            <form method="POST" action="/integration/dossier/demandes/statut" class="m-0 inline">
                                <input type="hidden" name="id_demande" value="<?= (int)$d['id_demande'] ?>"/>
                                <input type="hidden" name="statut" value="en_attente"/>
                                <button type="submit"
                                        class="px-4 py-1.5 text-xs font-bold text-slate-500 bg-slate-50 hover:bg-slate-100 hover:text-slate-700 rounded-lg transition-colors border border-slate-200">
                                    Remettre en attente
                                </button>
                            </form>
                            <?php endif ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
        <?php endif ?>
    </div>

</div>

<!-- ══════════════════════════════════════════════
     MODAL : Refus avec message IA
══════════════════════════════════════════════ -->
<div id="modalRefusIA" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">

    <!-- Header -->
    <div class="bg-gradient-to-r from-red-500 to-rose-600 px-7 py-5 flex items-center justify-between text-white">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
          <span class="material-symbols-outlined">smart_toy</span>
        </div>
        <div>
          <h3 class="text-lg font-bold">Refus assisté par IA</h3>
          <p class="text-rose-100 text-xs">L'IA rédige un message professionnel pour le patient</p>
        </div>
      </div>
      <button onclick="closeRefusModal()" class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center">
        <span class="material-symbols-outlined text-base">close</span>
      </button>
    </div>

    <div class="px-7 py-6 space-y-5">

      <!-- Motif de refus -->
      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-3">Motif du refus <span class="text-red-500">*</span></label>
        <div class="grid grid-cols-2 gap-2" id="refus_reason_chips">
          <?php
          $reasons = [
            ['id' => 'info',        'label' => 'Informations insuffisantes',    'icon' => 'info'],
            ['id' => 'consultation','label' => 'Consultation préalable requise', 'icon' => 'stethoscope'],
            ['id' => 'inapproprie', 'label' => 'Médicament non approprié',       'icon' => 'medication_liquid'],
            ['id' => 'perimetre',   'label' => 'Hors périmètre du médecin',      'icon' => 'domain_disabled'],
          ];
          foreach ($reasons as $r):
          ?>
          <button type="button" data-reason="<?= $r['id'] ?>" data-label="<?= htmlspecialchars($r['label']) ?>"
                  class="refus-chip flex items-center gap-2 px-3 py-2.5 rounded-xl border-2 border-slate-200 text-xs font-semibold text-slate-600 bg-white hover:border-red-300 hover:text-red-600 hover:bg-red-50 transition-all text-left">
            <span class="material-symbols-outlined text-sm shrink-0"><?= $r['icon'] ?></span>
            <span><?= htmlspecialchars($r['label']) ?></span>
          </button>
          <?php endforeach ?>
        </div>
      </div>

      <!-- Bouton générer -->
      <button id="btn_generate_refus" disabled onclick="generateRefusMessage()"
              class="w-full py-2.5 flex items-center justify-center gap-2 bg-violet-600 text-white rounded-xl text-sm font-bold opacity-40 cursor-not-allowed transition-all">
        <span class="material-symbols-outlined text-sm" id="gen_icon">auto_awesome</span>
        <span id="gen_label">Sélectionnez un motif pour générer</span>
      </button>

      <!-- Message généré -->
      <div id="refus_message_block" class="hidden space-y-2">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <label class="text-sm font-semibold text-slate-700">Message généré</label>
            <span id="variation_badge" class="hidden text-[10px] font-bold px-2 py-0.5 rounded-full bg-violet-100 text-violet-600">v1</span>
            <span class="text-xs font-normal text-slate-400">— modifiable</span>
          </div>
          <button onclick="copyRefusMessage()" id="btn_copy"
                  class="text-xs text-slate-500 hover:text-slate-700 font-semibold flex items-center gap-1 px-2 py-1 rounded-lg hover:bg-slate-100 transition-colors">
            <span class="material-symbols-outlined text-sm" id="copy_icon">content_copy</span>
            <span id="copy_label">Copier</span>
          </button>
        </div>
        <textarea id="refus_message_text" rows="5"
                  class="w-full px-4 py-3 border-2 border-violet-200 rounded-xl text-sm text-slate-700 outline-none focus:border-violet-400 transition-all resize-none bg-violet-50/30"></textarea>
        <p class="text-[10px] text-slate-400 text-right" id="refus_char_count">0 caractères</p>
      </div>

      <!-- Erreur -->
      <div id="refus_error" class="hidden bg-red-50 border border-red-200 rounded-xl px-4 py-3 flex items-center gap-2 text-sm text-red-700">
        <span class="material-symbols-outlined text-base">error</span>
        <span id="refus_error_text">Erreur lors de la génération.</span>
      </div>

      <!-- Actions -->
      <div class="flex gap-3 pt-1">
        <button type="button" onclick="closeRefusModal()"
                class="flex-1 py-2.5 border-2 border-slate-200 text-slate-600 rounded-xl text-sm font-semibold hover:bg-slate-50 transition-colors">
          Annuler
        </button>
        <form method="POST" action="/integration/dossier/demandes/statut" id="form_refus_confirm" class="flex-1">
          <input type="hidden" name="id_demande" id="refus_id_demande" value=""/>
          <input type="hidden" name="statut" value="refusee"/>
          <input type="hidden" name="ai_refus_message" id="refus_hidden_message" value=""/>
          <button type="submit" id="btn_confirm_refus" disabled
                  class="w-full py-2.5 bg-red-500 text-white rounded-xl text-sm font-bold opacity-40 cursor-not-allowed transition-all flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-sm">cancel</span>
            Confirmer le refus
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
@keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }
.fade-in { animation: fadeIn .35s ease forwards; }
.refus-chip.selected { border-color: #f87171; color: #dc2626; background-color: #fef2f2; }
</style>

<script>
// ── Flash auto-dismiss ─────────────────────────────────────────────
setTimeout(() => { document.getElementById('flash-msg')?.remove(); }, 4000);

// ── État modal refus ───────────────────────────────────────────────
let _refusDemandId   = null;
let _refusDesc       = '';
let _selectedReason  = '';
let _selectedLabel   = '';
let _generateCount   = 0;

function openRefusModal(id, description) {
    _refusDemandId  = id;
    _refusDesc      = description;
    _selectedReason = '';
    _selectedLabel  = '';
    _generateCount  = 0;

    // Reset UI
    document.querySelectorAll('.refus-chip').forEach(b => b.classList.remove('selected'));
    document.getElementById('btn_generate_refus').disabled = true;
    document.getElementById('btn_generate_refus').classList.add('opacity-40','cursor-not-allowed');
    document.getElementById('gen_icon').textContent  = 'auto_awesome';
    document.getElementById('gen_label').textContent = 'Sélectionnez un motif pour générer';
    document.getElementById('variation_badge')?.classList.add('hidden');
    document.getElementById('refus_message_block').classList.add('hidden');
    document.getElementById('refus_error').classList.add('hidden');
    document.getElementById('refus_id_demande').value = id;
    document.getElementById('refus_hidden_message').value = '';
    document.getElementById('btn_confirm_refus').disabled = true;
    document.getElementById('btn_confirm_refus').classList.add('opacity-40','cursor-not-allowed');

    document.getElementById('modalRefusIA').classList.remove('hidden');
}

function closeRefusModal() {
    document.getElementById('modalRefusIA').classList.add('hidden');
}

// ── Sélection du motif ─────────────────────────────────────────────
document.querySelectorAll('.refus-chip').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.refus-chip').forEach(b => b.classList.remove('selected'));
        btn.classList.add('selected');
        _selectedReason = btn.dataset.reason;
        _selectedLabel  = btn.dataset.label;

        const genBtn = document.getElementById('btn_generate_refus');
        genBtn.disabled = false;
        genBtn.classList.remove('opacity-40','cursor-not-allowed');
        document.getElementById('gen_label').textContent = 'Générer le message IA';
        document.getElementById('refus_error').classList.add('hidden');
    });
});

// ── Génération / Régénération IA ───────────────────────────────────
async function generateRefusMessage() {
    if (!_selectedReason) return;

    _generateCount++;

    const genBtn = document.getElementById('btn_generate_refus');
    const icon   = document.getElementById('gen_icon');
    const label  = document.getElementById('gen_label');
    const errBox = document.getElementById('refus_error');

    const isRegen = _generateCount > 1;

    // État chargement
    genBtn.disabled = true;
    genBtn.classList.add('opacity-70');
    icon.textContent  = 'hourglass_empty';
    label.textContent = isRegen ? 'Régénération en cours…' : 'Génération en cours…';
    errBox.classList.add('hidden');

    try {
        const res  = await fetch('/integration/dossier/demandes/ai-refus', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ raison: _selectedLabel, description: _refusDesc, variation: _generateCount }),
        });
        const data = await res.json();

        if (res.ok && data.success) {
            const textarea = document.getElementById('refus_message_text');

            // Animation sortie/entrée si c'est une régénération
            if (isRegen) {
                textarea.style.transition = 'opacity 0.2s';
                textarea.style.opacity = '0';
                await new Promise(r => setTimeout(r, 200));
                textarea.value = data.message;
                textarea.style.opacity = '1';
            } else {
                textarea.value = data.message;
            }

            document.getElementById('refus_hidden_message').value = data.message;
            document.getElementById('refus_message_block').classList.remove('hidden');
            updateCharCount(data.message);

            // Badge variation v2, v3…
            const badge = document.getElementById('variation_badge');
            if (badge) {
                badge.textContent = 'v' + _generateCount;
                badge.classList.remove('hidden');
            }

            // Activer confirmer
            const confirmBtn = document.getElementById('btn_confirm_refus');
            confirmBtn.disabled = false;
            confirmBtn.classList.remove('opacity-40','cursor-not-allowed');

        } else {
            document.getElementById('refus_error_text').textContent = data.error || 'Erreur lors de la génération.';
            errBox.classList.remove('hidden');
            _generateCount--;
        }
    } catch {
        document.getElementById('refus_error_text').textContent = 'Problème de connexion. Vérifiez votre réseau.';
        errBox.classList.remove('hidden');
        _generateCount--;
    } finally {
        genBtn.disabled = false;
        genBtn.classList.remove('opacity-70');
        // Après 1ère génération, le bouton devient "Régénérer"
        if (_generateCount >= 1) {
            icon.textContent  = 'refresh';
            label.textContent = 'Régénérer un autre message';
        } else {
            icon.textContent  = 'auto_awesome';
            label.textContent = 'Générer le message IA';
        }
    }
}

// ── Copier le message ──────────────────────────────────────────────
async function copyRefusMessage() {
    const text = document.getElementById('refus_message_text').value.trim();
    if (!text) return;
    try {
        await navigator.clipboard.writeText(text);
        const icon  = document.getElementById('copy_icon');
        const lbl   = document.getElementById('copy_label');
        icon.textContent = 'check';
        lbl.textContent  = 'Copié !';
        setTimeout(() => { icon.textContent = 'content_copy'; lbl.textContent = 'Copier'; }, 2000);
    } catch {}
}

// ── Compteur caractères ────────────────────────────────────────────
function updateCharCount(text) {
    const el = document.getElementById('refus_char_count');
    if (el) el.textContent = text.length + ' caractères';
}

// ── Sync textarea → hidden input avant submit ──────────────────────
document.getElementById('refus_message_text')?.addEventListener('input', function () {
    document.getElementById('refus_hidden_message').value = this.value;
    updateCharCount(this.value);
    const hasText = this.value.trim().length > 0;
    const confirmBtn = document.getElementById('btn_confirm_refus');
    confirmBtn.disabled = !hasText;
    confirmBtn.classList.toggle('opacity-40', !hasText);
    confirmBtn.classList.toggle('cursor-not-allowed', !hasText);
});
</script>
