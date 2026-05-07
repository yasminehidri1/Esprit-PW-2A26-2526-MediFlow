<?php
// Views/Back/dossier_medical/patient_dossier.php
// Patient's own medical record — embedded in Back/layout.php shell
$pInitials = strtoupper(substr($patient['prenom'],0,1).substr($patient['nom'],0,1));
$pName     = htmlspecialchars($patient['prenom'].' '.$patient['nom']);
$activeMeds = [];
if (!empty($prescriptions)) {
    foreach ($prescriptions as $presc) {
        if ($presc['statut']==='active') {
            $m = json_decode($presc['medicaments']??'[]',true) ?: [];
            foreach ($m as $med) $activeMeds[] = $med;
        }
    }
}
?>
<div class="space-y-6">

  <!-- Hero card -->
  <div class="bg-white rounded-2xl p-7 shadow-[0_4px_20px_rgba(0,77,153,0.05)] border-l-4 border-primary relative overflow-hidden">
    <div class="absolute -top-10 -right-10 w-48 h-48 bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="flex flex-wrap items-start justify-between gap-4 relative z-10">
      <div class="flex items-center gap-5">
        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-primary to-primary-container text-white text-3xl font-bold flex items-center justify-center shadow-md"><?= $pInitials ?></div>
        <div>
          <h1 class="font-headline text-2xl font-extrabold text-blue-900"><?= $pName ?></h1>
          <p class="text-sm text-on-surface-variant">#MF-<?= str_pad($patient['id_PK'],5,'0',STR_PAD_LEFT) ?> · <?= htmlspecialchars($patient['mail']) ?></p>
          <span class="mt-2 inline-block px-3 py-0.5 bg-tertiary-fixed/40 text-tertiary text-xs font-bold rounded-full">Patient Actif</span>
        </div>
      </div>
      <div class="flex gap-2">
        <button id="btnModifierProfil" class="flex items-center gap-2 px-4 py-2.5 bg-surface-container hover:bg-surface-container-high rounded-xl text-sm font-semibold text-on-surface transition-colors">
          <span class="material-symbols-outlined text-lg">edit</span>Modifier Profil
        </button>
        <button id="btndemandeOrdonnance" class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-xl text-sm font-bold shadow-md hover:shadow-lg transition-all">
          <span class="material-symbols-outlined text-lg">history_edu</span>Demande Ordonnance
        </button>
      </div>
    </div>

    <!-- Vitals row -->
    <?php if ($vitals): ?>
    <div class="grid grid-cols-4 gap-4 mt-6 pt-6 border-t border-outline-variant/20">
      <?php foreach([['Tension','tension_arterielle','mmHg'],['Rythme Cardiaque','rythme_cardiaque','BPM'],['Poids','poids','kg'],['Saturation O²','saturation_o2','%']] as [$l,$k,$u]): ?>
      <div class="bg-surface-container-low rounded-xl p-4">
        <p class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1"><?= $l ?></p>
        <p class="text-2xl font-extrabold text-on-surface"><?= htmlspecialchars($vitals[$k]??'—') ?> <span class="text-xs font-normal text-on-surface-variant"><?= $u ?></span></p>
      </div>
      <?php endforeach ?>
    </div>
    <?php endif ?>
  </div>

  <div class="grid grid-cols-12 gap-6">

    <!-- Consultation timeline (col 8) -->
    <div class="col-span-8">
      <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
        <div class="px-6 py-4 border-b border-outline-variant/20">
          <h3 class="font-headline font-bold text-blue-900 flex items-center gap-2"><span class="material-symbols-outlined text-primary">history</span>Historique Clinique</h3>
        </div>
        <?php if (empty($consultations)): ?>
        <div class="py-12 text-center text-on-surface-variant"><span class="material-symbols-outlined text-4xl block mb-2 opacity-30">event_note</span><p class="text-sm">Aucune consultation enregistrée.</p></div>
        <?php else: ?>
        <div class="p-6 space-y-0">
          <?php foreach ($consultations as $i => $c): ?>
          <div class="relative pl-8 pb-8 group <?= $i===count($consultations)-1?'pb-2':'' ?>">
            <div class="absolute left-0 top-1 w-0.5 h-full bg-outline-variant/20 group-last:hidden"></div>
            <div class="absolute left-[-5px] top-1 w-3 h-3 rounded-full <?= $i===0?'bg-primary':'bg-tertiary' ?> border-2 border-white shadow-sm"></div>
            <div class="bg-surface-container-low rounded-xl p-4 hover:bg-white hover:shadow-md transition-all">
              <div class="flex justify-between items-start mb-1">
                <span class="text-[10px] font-black uppercase tracking-widest <?= $i===0?'text-primary':'text-tertiary' ?>"><?= date('d M Y',strtotime($c['date_consultation'])) ?> · <?= htmlspecialchars($c['type_consultation']??'Consultation') ?></span>
                <span class="px-2 py-0.5 bg-white text-[10px] font-bold rounded border border-outline-variant/20">Visite</span>
              </div>
              <?php if (!empty($c['diagnostic'])): ?><p class="font-semibold text-on-surface text-sm"><?= htmlspecialchars($c['diagnostic']) ?></p><?php endif ?>
              <?php if (!empty($c['compte_rendu'])): ?><p class="text-xs text-on-surface-variant mt-1 line-clamp-2"><?= htmlspecialchars(substr($c['compte_rendu'],0,100)) ?></p><?php endif ?>
              <div class="flex items-center gap-1.5 text-xs text-on-surface-variant mt-3">
                <span class="material-symbols-outlined text-sm">person</span>
                Dr. <?= htmlspecialchars($c['medecin_prenom'].' '.$c['medecin_nom']) ?>
              </div>
            </div>
          </div>
          <?php endforeach ?>
        </div>
        <?php endif ?>
      </div>
    </div>

    <!-- Right col (4) -->
    <div class="col-span-4 space-y-5">

      <!-- Active treatment -->
      <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgba(0,77,153,0.05)] p-5">
        <h3 class="font-headline font-bold text-blue-900 mb-4 flex items-center gap-2 text-sm"><span class="material-symbols-outlined text-tertiary">pill</span>Traitement Actuel</h3>
        <?php if (empty($activeMeds)): ?>
        <p class="text-sm text-on-surface-variant italic">Aucun traitement actif.</p>
        <?php else: ?>
        <div class="space-y-3">
          <?php foreach (array_slice($activeMeds,0,3) as $m): ?>
          <div class="bg-tertiary/5 border-l-4 border-tertiary rounded-xl p-3">
            <p class="font-bold text-on-surface text-sm"><?= htmlspecialchars($m['nom']??'') ?> <span class="font-normal text-xs text-on-surface-variant"><?= htmlspecialchars($m['dosage']??'') ?></span></p>
            <p class="text-xs text-on-surface-variant mt-0.5"><?= htmlspecialchars($m['categorie']??'') ?> · <?= htmlspecialchars($m['frequence']??'') ?></p>
          </div>
          <?php endforeach ?>
        </div>
        <?php endif ?>
        <button id="btndemandeOrdonnance2" class="mt-4 w-full border border-tertiary/20 text-tertiary text-sm font-bold rounded-xl py-2.5 hover:bg-tertiary/5 transition-colors flex items-center justify-center gap-2">
          <span class="material-symbols-outlined text-base">history_edu</span>Demande Ordonnance
        </button>
      </div>

      <!-- Care team -->
      <div class="bg-gradient-to-br from-primary to-primary-container rounded-2xl p-5 text-white shadow-xl shadow-primary/20">
        <h3 class="font-headline font-bold mb-4 text-sm">Mon Équipe Médicale</h3>
        <?php if (!empty($doctors)): ?>
        <div class="space-y-3">
          <?php foreach (array_slice($doctors,0,3) as $d): ?>
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center text-xs font-bold"><?= strtoupper(substr($d['prenom'],0,1).substr($d['nom'],0,1)) ?></div>
            <div><p class="text-sm font-bold"><?= htmlspecialchars($d['prenom'].' '.$d['nom']) ?></p><p class="text-[10px] opacity-70">Médecin</p></div>
          </div>
          <?php endforeach ?>
        </div>
        <?php else: ?><p class="text-sm opacity-70">Aucun médecin assigné.</p><?php endif ?>
      </div>

      <!-- Allergies alert -->
      <?php
      $allergies = [];
      if (!empty($consultations[0])) $allergies = json_decode($consultations[0]['allergies']??'[]',true) ?: [];
      ?>
      <?php if (!empty($allergies)): ?>
      <div class="bg-error-container/20 border border-error/10 rounded-2xl p-4">
        <div class="flex items-center gap-2 text-error mb-2"><span class="material-symbols-outlined">warning</span><span class="text-sm font-bold">Allergies</span></div>
        <ul class="space-y-1">
          <?php foreach (array_slice($allergies,0,3) as $a): ?>
          <li class="flex items-start gap-2 text-xs text-on-surface-variant">
            <span class="w-1.5 h-1.5 rounded-full bg-error mt-1 shrink-0"></span>
            <?= htmlspecialchars($a['nom']??'') ?> (<?= htmlspecialchars($a['niveau']??'') ?>)
          </li>
          <?php endforeach ?>
        </ul>
      </div>
      <?php endif ?>
    </div>
  </div>
</div>

<!-- ══ Chatbot flottant ══════════════════════════════════════════════ -->

<!-- Bouton flottant -->
<button id="chatbotToggle"
        onclick="toggleChatbot()"
        class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-gradient-to-br from-primary to-primary-container text-white rounded-2xl shadow-xl shadow-primary/30 flex items-center justify-center hover:scale-105 active:scale-95 transition-all group"
        title="Assistant médical">
  <span class="material-symbols-outlined text-2xl" id="chatbotToggleIcon" style="font-variation-settings:'FILL' 1">health_and_safety</span>
  <span id="chatbotBadge" class="hidden absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 text-white text-[10px] font-black rounded-full flex items-center justify-center border-2 border-white animate-pulse">!</span>
</button>

<!-- Panel chatbot -->
<div id="chatbotPanel"
     class="fixed bottom-24 right-6 z-50 w-96 max-w-[calc(100vw-24px)] bg-white rounded-2xl shadow-2xl shadow-primary/15 border border-slate-100 flex flex-col overflow-hidden transition-all duration-300 origin-bottom-right scale-95 opacity-0 pointer-events-none"
     style="height: 560px;">

  <!-- Header -->
  <div class="bg-gradient-to-r from-primary to-primary-container px-5 py-4 flex items-center justify-between text-white flex-shrink-0">
    <div class="flex items-center gap-3">
      <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center">
        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">health_and_safety</span>
      </div>
      <div>
        <p class="font-bold text-sm leading-none">Assistant Médical</p>
        <p class="text-blue-100 text-[10px] mt-0.5">Triage symptomatique · IA</p>
      </div>
    </div>
    <button onclick="toggleChatbot()" class="w-7 h-7 rounded-lg bg-white/20 hover:bg-white/30 flex items-center justify-center transition-colors">
      <span class="material-symbols-outlined text-sm">close</span>
    </button>
  </div>

  <!-- Disclaimer -->
  <div class="bg-amber-50 border-b border-amber-100 px-4 py-2.5 flex items-start gap-2 flex-shrink-0">
    <span class="material-symbols-outlined text-amber-500 text-sm mt-0.5 shrink-0">info</span>
    <p class="text-[10px] text-amber-700 font-medium leading-relaxed">Cet outil ne remplace pas un médecin. En cas d'urgence vitale, appelez le <strong>15</strong> immédiatement.</p>
  </div>

  <!-- Messages -->
  <div id="chatbotMessages" class="flex-1 overflow-y-auto p-4 space-y-4">

    <!-- Message d'accueil -->
    <div class="flex gap-3">
      <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
        <span class="material-symbols-outlined text-primary text-sm" style="font-variation-settings:'FILL' 1">health_and_safety</span>
      </div>
      <div class="bg-surface-container-low rounded-2xl rounded-tl-sm px-4 py-3 max-w-[85%]">
        <p class="text-sm text-on-surface font-medium">Bonjour ! Je suis votre assistant médical.</p>
        <p class="text-xs text-on-surface-variant mt-1 leading-relaxed">Décrivez vos symptômes et je vous conseille sur la démarche à suivre (urgence, RDV, etc.).</p>
      </div>
    </div>

  </div>

  <!-- Zone de saisie -->
  <div class="border-t border-slate-100 p-3 flex-shrink-0 bg-white">
    <div class="flex gap-2 items-end">
      <textarea id="chatbotInput"
                rows="2"
                placeholder="Ex: J'ai mal à la tête depuis 2 jours avec de la fièvre..."
                class="flex-1 bg-surface-container-low border border-slate-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40 transition-all resize-none placeholder:text-slate-400"
                onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendChatMessage();}"></textarea>
      <button id="chatbotSendBtn"
              onclick="sendChatMessage()"
              class="w-10 h-10 bg-primary text-white rounded-xl flex items-center justify-center hover:opacity-90 active:scale-95 transition-all shadow-md shadow-primary/20 shrink-0 mb-0.5 disabled:opacity-40 disabled:cursor-not-allowed">
        <span class="material-symbols-outlined text-lg" id="chatbotSendIcon">send</span>
      </button>
    </div>
    <p class="text-[10px] text-slate-400 mt-1.5 text-center">Entrée pour envoyer · Maj+Entrée pour nouvelle ligne</p>
  </div>
</div>

<!-- Modal: Modifier Profil -->
<div id="modalModifierProfil" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">

    <!-- Header -->
    <div class="bg-gradient-to-r from-primary to-primary-container px-7 py-5 flex items-center justify-between text-white">
      <div class="flex items-center gap-4">
        <div id="modal_avatar" class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center text-lg font-extrabold">
          <?= strtoupper(substr($patient['prenom']??'P',0,1).substr($patient['nom']??'X',0,1)) ?>
        </div>
        <div>
          <h3 class="text-lg font-bold">Modifier le profil</h3>
          <p class="text-blue-100 text-xs" id="modal_subtitle"><?= htmlspecialchars($patient['prenom'].' '.$patient['nom']) ?></p>
        </div>
      </div>
      <button onclick="closeModifierProfil()" class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center transition-colors">
        <span class="material-symbols-outlined text-base">close</span>
      </button>
    </div>

    <div class="px-7 py-6">

      <!-- État succès -->
      <div id="profil_success" class="hidden flex-col items-center text-center py-6 gap-4">
        <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mx-auto">
          <span class="material-symbols-outlined text-4xl text-emerald-600" style="font-variation-settings:'FILL' 1">check_circle</span>
        </div>
        <div>
          <p class="text-lg font-bold text-slate-800">Profil mis à jour !</p>
          <p class="text-sm text-slate-500 mt-1">Vos informations ont été enregistrées avec succès.</p>
        </div>
        <button onclick="closeModifierProfil()" class="px-8 py-2.5 bg-primary text-white rounded-xl font-semibold text-sm hover:opacity-90 transition-opacity">
          Fermer
        </button>
      </div>

      <!-- Formulaire -->
      <form id="formModifierProfil" class="space-y-5">

        <!-- Erreur globale -->
        <div id="profil_error" class="hidden bg-red-50 border border-red-200 rounded-xl px-4 py-3 flex items-center gap-2 text-sm text-red-700">
          <span class="material-symbols-outlined text-base shrink-0">error</span>
          <span id="profil_error_text">Une erreur est survenue.</span>
        </div>

        <!-- Section identité -->
        <div>
          <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-sm text-primary">person</span>Identité
          </p>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-slate-600 mb-1.5">Prénom <span class="text-red-500">*</span></label>
              <input type="text" id="modif_prenom" value="<?= htmlspecialchars($patient['prenom']??'') ?>"
                     class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40 transition-all"/>
              <p id="err_prenom" class="hidden text-xs text-red-500 mt-1"></p>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-600 mb-1.5">Nom <span class="text-red-500">*</span></label>
              <input type="text" id="modif_nom" value="<?= htmlspecialchars($patient['nom']??'') ?>"
                     class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40 transition-all"/>
              <p id="err_nom" class="hidden text-xs text-red-500 mt-1"></p>
            </div>
          </div>
        </div>

        <!-- Section contact -->
        <div>
          <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-sm text-primary">contact_page</span>Contact
          </p>
          <div class="space-y-3">
            <div class="relative">
              <label class="block text-xs font-bold text-slate-600 mb-1.5">Email <span class="text-red-500">*</span></label>
              <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">mail</span>
                <input type="email" id="modif_mail" value="<?= htmlspecialchars($patient['mail']??'') ?>"
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40 transition-all"/>
              </div>
              <p id="err_mail" class="hidden text-xs text-red-500 mt-1"></p>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-600 mb-1.5">Téléphone</label>
              <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">call</span>
                <input type="text" id="modif_tel" value="<?= htmlspecialchars($patient['tel']??'') ?>"
                       placeholder="+216 XX XXX XXX"
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40 transition-all"/>
              </div>
              <p id="err_tel" class="hidden text-xs text-red-500 mt-1"></p>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-600 mb-1.5">Adresse</label>
              <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-3 text-slate-400 text-[18px]">location_on</span>
                <textarea id="modif_adresse" rows="2" placeholder="Rue, ville, code postal..."
                          class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40 transition-all resize-none"><?= htmlspecialchars($patient['adresse']??'') ?></textarea>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-3 pt-1 border-t border-slate-100">
          <button type="button" onclick="closeModifierProfil()"
                  class="flex-1 px-4 py-2.5 border-2 border-slate-200 text-slate-600 rounded-xl text-sm font-semibold hover:bg-slate-50 transition-colors">
            Annuler
          </button>
          <button type="submit" id="profil_submit_btn"
                  class="flex-1 px-4 py-2.5 bg-primary text-white rounded-xl text-sm font-bold hover:opacity-90 active:scale-95 transition-all flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-sm" id="profil_submit_icon">save</span>
            <span id="profil_submit_label">Enregistrer</span>
          </button>
        </div>
      </form>

    </div>
  </div>
</div>

<!-- Modal: Demande Ordonnance -->
<div id="modaldemandeOrdonnance" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">

    <!-- Header -->
    <div class="bg-gradient-to-r from-tertiary to-teal-500 px-7 py-5 flex items-center justify-between text-white">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
          <span class="material-symbols-outlined">history_edu</span>
        </div>
        <div>
          <h3 class="text-lg font-bold">Demande d'Ordonnance</h3>
          <p class="text-teal-100 text-xs">Envoyée directement à votre médecin</p>
        </div>
      </div>
      <button onclick="document.getElementById('modaldemandeOrdonnance').classList.add('hidden')"
              class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center">
        <span class="material-symbols-outlined text-base">close</span>
      </button>
    </div>

    <div class="px-7 py-6 space-y-4">

      <!-- État succès -->
      <div id="ord_success_state" class="hidden flex-col items-center text-center py-6 gap-4">
        <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center">
          <span class="material-symbols-outlined text-4xl text-emerald-600" style="font-variation-settings:'FILL' 1">check_circle</span>
        </div>
        <div>
          <p class="text-lg font-bold text-on-surface">Demande envoyée !</p>
          <p class="text-sm text-on-surface-variant mt-1">Votre médecin recevra votre demande et vous répondra prochainement.</p>
        </div>
        <button onclick="document.getElementById('modaldemandeOrdonnance').classList.add('hidden')"
                class="px-8 py-2.5 bg-tertiary text-white rounded-xl font-semibold text-sm hover:opacity-90 transition-opacity">
          Fermer
        </button>
      </div>

      <!-- Formulaire -->
      <form id="formdemandeOrdonnance" class="space-y-5">

        <!-- Erreur globale -->
        <div id="ord_global_error" class="hidden bg-red-50 border border-red-200 rounded-xl px-4 py-3 flex items-center gap-2 text-sm text-red-700">
          <span class="material-symbols-outlined text-base">error</span>
          <span id="ord_global_error_text">Une erreur est survenue. Réessayez.</span>
        </div>

        <!-- Médecin -->
        <div>
          <label class="block text-sm font-semibold text-on-surface mb-2">
            Médecin <span class="text-error">*</span>
          </label>
          <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">stethoscope</span>
            <select id="ord_medecin" required
                    class="w-full pl-10 pr-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm outline-none focus:border-tertiary transition-all appearance-none bg-white">
              <option value="">— Sélectionner un médecin —</option>
              <?php $liste = !empty($allDoctors) ? $allDoctors : ($doctors??[]); ?>
              <?php foreach ($liste as $d): ?>
              <option value="<?= (int)$d['id_PK'] ?>">Dr. <?= htmlspecialchars($d['prenom'].' '.$d['nom']) ?></option>
              <?php endforeach ?>
            </select>
          </div>
          <p id="err_ord_medecin" class="hidden text-xs text-red-500 mt-1 flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">error</span>Veuillez sélectionner un médecin.
          </p>
        </div>

        <!-- Niveau d'urgence -->
        <div>
          <label class="block text-sm font-semibold text-on-surface mb-2">Niveau d'urgence</label>
          <div class="flex gap-2" id="ord_urgence_chips">
            <button type="button" data-value="normale"
                    class="ord-chip flex-1 py-2 px-3 rounded-xl border-2 text-xs font-bold transition-all border-emerald-300 text-emerald-700 bg-emerald-50 ring-2 ring-emerald-300">
              <span class="material-symbols-outlined text-sm block mx-auto mb-0.5">check_circle</span>Normale
            </button>
            <button type="button" data-value="urgent"
                    class="ord-chip flex-1 py-2 px-3 rounded-xl border-2 text-xs font-bold transition-all border-slate-200 text-slate-500 bg-white hover:border-amber-300 hover:text-amber-600 hover:bg-amber-50">
              <span class="material-symbols-outlined text-sm block mx-auto mb-0.5">schedule</span>Urgent
            </button>
            <button type="button" data-value="tres_urgent"
                    class="ord-chip flex-1 py-2 px-3 rounded-xl border-2 text-xs font-bold transition-all border-slate-200 text-slate-500 bg-white hover:border-red-300 hover:text-red-600 hover:bg-red-50">
              <span class="material-symbols-outlined text-sm block mx-auto mb-0.5">emergency</span>Très urgent
            </button>
          </div>
          <input type="hidden" id="ord_urgence" value="normale">
        </div>

        <!-- Description -->
        <div>
          <label class="block text-sm font-semibold text-on-surface mb-2">
            Description <span class="text-error">*</span>
            <span class="text-xs font-normal text-on-surface-variant">(10-500 car.)</span>
          </label>
          <textarea id="ord_description" rows="4"
                    placeholder="Ex: Renouvellement de mon traitement Amlodipine 5mg, ordonnance expirée..."
                    class="w-full bg-surface-container-low border-2 border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-tertiary transition-all resize-none"></textarea>
          <div class="mt-2 space-y-1">
            <div class="flex justify-between items-center">
              <p id="err_ord_description" class="hidden text-xs text-red-500 flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">error</span>
                <span id="err_ord_description_text"></span>
              </p>
              <p id="ok_ord_description" class="hidden text-xs text-emerald-600 flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">check_circle</span>Description valide
              </p>
              <span class="text-xs font-semibold ml-auto">
                <span id="ord_char_count">0</span><span class="text-slate-400">/500</span>
              </span>
            </div>
            <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
              <div id="ord_desc_bar" class="h-full rounded-full transition-all duration-200 bg-slate-300" style="width:0%"></div>
            </div>
            <p class="text-[10px] text-slate-400">Minimum requis : <span id="ord_min_indicator" class="font-bold text-slate-500">0/10 caractères</span></p>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-3 pt-1">
          <button type="button" onclick="document.getElementById('modaldemandeOrdonnance').classList.add('hidden')"
                  class="flex-1 py-2.5 border-2 border-slate-200 text-slate-600 rounded-xl text-sm font-semibold hover:bg-slate-50 transition-colors">
            Annuler
          </button>
          <button type="submit" id="ord_submit_btn" disabled
                  class="flex-1 py-2.5 bg-tertiary text-white rounded-xl text-sm font-bold opacity-40 cursor-not-allowed flex items-center justify-center gap-2 transition-all">
            <span class="material-symbols-outlined text-sm" id="ord_submit_icon">send</span>
            <span id="ord_submit_label">Envoyer</span>
          </button>
        </div>
      </form>

    </div>
  </div>
</div>

<script>
// ── Ouverture modals ───────────────────────────────────────────────
['btnModifierProfil','btndemandeOrdonnance','btndemandeOrdonnance2'].forEach(id => {
  document.getElementById(id)?.addEventListener('click', () => {
    const target = id.includes('Modifier') ? 'modalModifierProfil' : 'modaldemandeOrdonnance';
    if (id.includes('Modifier')) {
      // Reset état succès si on rouvre le modal
      const form = document.getElementById('formModifierProfil');
      const succ = document.getElementById('profil_success');
      if (form) form.style.display = '';
      if (succ) { succ.classList.add('hidden'); succ.style.display = ''; }
      document.getElementById('profil_error')?.classList.add('hidden');
    }
    document.getElementById(target).classList.remove('hidden');
  });
});

// ── Chips urgence ──────────────────────────────────────────────────
const chipBase    = 'ord-chip flex-1 py-2 px-3 rounded-xl border-2 text-xs font-bold transition-all text-center';
const chipVariant = {
  normale:     { active: 'border-emerald-300 text-emerald-700 bg-emerald-50 ring-2 ring-emerald-300', inactive: 'border-slate-200 text-slate-500 bg-white' },
  urgent:      { active: 'border-amber-300 text-amber-700 bg-amber-50 ring-2 ring-amber-300',         inactive: 'border-slate-200 text-slate-500 bg-white' },
  tres_urgent: { active: 'border-red-300 text-red-700 bg-red-50 ring-2 ring-red-300',                 inactive: 'border-slate-200 text-slate-500 bg-white' },
};
document.querySelectorAll('.ord-chip').forEach(btn => {
  btn.addEventListener('click', () => {
    const val = btn.dataset.value;
    document.getElementById('ord_urgence').value = val;
    document.querySelectorAll('.ord-chip').forEach(b => {
      const v = chipVariant[b.dataset.value];
      b.className = chipBase + ' ' + (b.dataset.value === val ? v.active : v.inactive);
    });
  });
});

// ── Validation description ─────────────────────────────────────────
function ordValidate() {
  const desc = document.getElementById('ord_description').value;
  const len  = desc.length;
  const medecinOk = !!document.getElementById('ord_medecin').value;
  const descOk    = len >= 10 && len <= 500;

  // Compteur + barre
  document.getElementById('ord_char_count').textContent = len;
  document.getElementById('ord_min_indicator').textContent = Math.min(len, 10) + '/10 caractères';
  const pct = Math.min((len / 500) * 100, 100);
  const bar = document.getElementById('ord_desc_bar');
  bar.style.width = pct + '%';
  bar.className = 'h-full rounded-full transition-all duration-200 ' + (len < 10 ? 'bg-slate-300' : len <= 500 ? 'bg-emerald-400' : 'bg-red-400');

  // Messages description
  const errDesc = document.getElementById('err_ord_description');
  const okDesc  = document.getElementById('ok_ord_description');
  if (len > 0 && len < 10) {
    document.getElementById('err_ord_description_text').textContent = 'Description trop courte (min. 10 caractères).';
    errDesc.classList.remove('hidden'); okDesc.classList.add('hidden');
  } else if (len > 500) {
    document.getElementById('err_ord_description_text').textContent = 'Description trop longue (max. 500 caractères).';
    errDesc.classList.remove('hidden'); okDesc.classList.add('hidden');
  } else if (len >= 10) {
    errDesc.classList.add('hidden'); okDesc.classList.remove('hidden');
  } else {
    errDesc.classList.add('hidden'); okDesc.classList.add('hidden');
  }

  // Bouton submit
  const btn = document.getElementById('ord_submit_btn');
  const ok  = descOk && medecinOk;
  btn.disabled = !ok;
  btn.classList.toggle('opacity-40', !ok);
  btn.classList.toggle('cursor-not-allowed', !ok);
}
document.getElementById('ord_description')?.addEventListener('input', ordValidate);
document.getElementById('ord_medecin')?.addEventListener('change', () => {
  document.getElementById('err_ord_medecin')?.classList.add('hidden');
  ordValidate();
});

// ── Soumission formulaire ──────────────────────────────────────────
document.getElementById('formdemandeOrdonnance')?.addEventListener('submit', async function(e) {
  e.preventDefault();
  const btn   = document.getElementById('ord_submit_btn');
  const icon  = document.getElementById('ord_submit_icon');
  const label = document.getElementById('ord_submit_label');
  const errGlobal = document.getElementById('ord_global_error');

  // État chargement
  btn.disabled = true; btn.classList.add('opacity-70');
  icon.textContent = 'hourglass_empty'; icon.classList.add('animate-spin');
  label.textContent = 'Envoi en cours…';
  errGlobal.classList.add('hidden');

  try {
    const res = await fetch('/integration/dossier/patient/request-prescription', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        medecin_id:  document.getElementById('ord_medecin').value,
        description: document.getElementById('ord_description').value,
        urgence:     document.getElementById('ord_urgence').value,
      })
    });

    if (res.ok) {
      this.style.display = 'none';
      const s = document.getElementById('ord_success_state');
      s.classList.remove('hidden'); s.style.display = 'flex';
    } else {
      const data = await res.json().catch(() => ({}));
      document.getElementById('ord_global_error_text').textContent = data?.error || 'Erreur lors de l\'envoi. Réessayez.';
      errGlobal.classList.remove('hidden');
      btn.disabled = false; btn.classList.remove('opacity-40','opacity-70','cursor-not-allowed');
      icon.textContent = 'send'; icon.classList.remove('animate-spin');
      label.textContent = 'Envoyer';
      ordValidate();
    }
  } catch {
    document.getElementById('ord_global_error_text').textContent = 'Problème de connexion. Vérifiez votre réseau.';
    errGlobal.classList.remove('hidden');
    btn.disabled = false; btn.classList.remove('opacity-70');
    icon.textContent = 'send'; icon.classList.remove('animate-spin');
    label.textContent = 'Envoyer';
    ordValidate();
  }
});

// ── Chatbot médical ────────────────────────────────────────────────
let _chatbotOpen = false;

function toggleChatbot() {
  _chatbotOpen = !_chatbotOpen;
  const panel = document.getElementById('chatbotPanel');
  const icon  = document.getElementById('chatbotToggleIcon');
  const badge = document.getElementById('chatbotBadge');
  if (_chatbotOpen) {
    panel.classList.remove('scale-95','opacity-0','pointer-events-none');
    panel.classList.add('scale-100','opacity-100');
    icon.textContent = 'close';
    badge.classList.add('hidden');
    document.getElementById('chatbotInput').focus();
  } else {
    panel.classList.add('scale-95','opacity-0','pointer-events-none');
    panel.classList.remove('scale-100','opacity-100');
    icon.textContent = 'health_and_safety';
  }
}

async function sendChatMessage() {
  const input   = document.getElementById('chatbotInput');
  const sendBtn = document.getElementById('chatbotSendBtn');
  const sendIcon = document.getElementById('chatbotSendIcon');
  const messages = document.getElementById('chatbotMessages');
  const text = input.value.trim();
  if (!text || sendBtn.disabled) return;

  // Afficher message utilisateur
  messages.insertAdjacentHTML('beforeend', `
    <div class="flex gap-3 justify-end">
      <div class="bg-primary text-white rounded-2xl rounded-tr-sm px-4 py-3 max-w-[85%]">
        <p class="text-sm leading-relaxed">${text.replace(/</g,'&lt;').replace(/>/g,'&gt;')}</p>
      </div>
    </div>`);
  input.value = '';
  messages.scrollTop = messages.scrollHeight;

  // État chargement
  sendBtn.disabled = true;
  sendIcon.textContent = 'hourglass_empty';
  sendIcon.classList.add('animate-spin');

  // Indicateur "en train d'écrire"
  const typingId = 'typing_' + Date.now();
  messages.insertAdjacentHTML('beforeend', `
    <div id="${typingId}" class="flex gap-3">
      <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
        <span class="material-symbols-outlined text-primary text-sm" style="font-variation-settings:'FILL' 1">health_and_safety</span>
      </div>
      <div class="bg-surface-container-low rounded-2xl rounded-tl-sm px-4 py-3">
        <div class="flex gap-1 items-center h-4">
          <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-bounce" style="animation-delay:0ms"></span>
          <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-bounce" style="animation-delay:150ms"></span>
          <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-bounce" style="animation-delay:300ms"></span>
        </div>
      </div>
    </div>`);
  messages.scrollTop = messages.scrollHeight;

  try {
    const res  = await fetch('/integration/dossier/patient/chatbot', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ symptoms: text })
    });
    const data = await res.json();

    document.getElementById(typingId)?.remove();

    if (!res.ok || !data.success) {
      _chatAppendError((data.error || 'Erreur lors de l\'analyse.') + (data._debug ? '\n[Debug] ' + data._debug : ''), messages);
    } else {
      if (data._debug) console.warn('[ChatbotIA fallback]', data._debug);
      if (data.urgence === 'none') {
        // Message de courtoisie sans badge médical
        messages.insertAdjacentHTML('beforeend', `
          <div class="flex gap-3">
            <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
              <span class="material-symbols-outlined text-primary text-sm" style="font-variation-settings:'FILL' 1">health_and_safety</span>
            </div>
            <div class="bg-surface-container-low rounded-2xl rounded-tl-sm px-4 py-3 max-w-[85%]">
              <p class="text-sm text-on-surface leading-relaxed">${data.message}</p>
            </div>
          </div>`);
      } else {
        _chatAppendResponse(data, messages);
      }
    }
  } catch {
    document.getElementById(typingId)?.remove();
    _chatAppendError('Problème de connexion. Vérifiez votre réseau.', messages);
  } finally {
    sendBtn.disabled = false;
    sendIcon.textContent = 'send';
    sendIcon.classList.remove('animate-spin');
    messages.scrollTop = messages.scrollHeight;
    input.focus();
  }
}

function _chatAppendResponse(data, messages) {
  const urgenceConfig = {
    non_urgent:      { label: 'Non urgent',     icon: 'check_circle',   bg: 'bg-emerald-50', border: 'border-emerald-200', text: 'text-emerald-700', dot: 'bg-emerald-500' },
    semi_urgent:     { label: 'Semi-urgent',    icon: 'schedule',       bg: 'bg-amber-50',   border: 'border-amber-200',   text: 'text-amber-700',   dot: 'bg-amber-500'   },
    urgent:          { label: 'Urgent',         icon: 'priority_high',  bg: 'bg-orange-50',  border: 'border-orange-200',  text: 'text-orange-700',  dot: 'bg-orange-500'  },
    urgence_vitale:  { label: 'URGENCE VITALE', icon: 'emergency',      bg: 'bg-red-50',     border: 'border-red-300',     text: 'text-red-700',     dot: 'bg-red-500 animate-pulse' },
  };
  const cfg = urgenceConfig[data.urgence] || urgenceConfig.semi_urgent;

  const signesHtml = data.signes_alerte && data.signes_alerte.length
    ? `<div class="mt-2.5 pt-2 border-t border-slate-100">
         <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Signes d'alerte à surveiller</p>
         <ul class="space-y-1">
           ${data.signes_alerte.map(s => `<li class="flex items-start gap-1.5 text-xs text-slate-600"><span class="w-1.5 h-1.5 rounded-full bg-amber-400 mt-1.5 shrink-0"></span>${s.replace(/</g,'&lt;')}</li>`).join('')}
         </ul>
       </div>`
    : '';

  messages.insertAdjacentHTML('beforeend', `
    <div class="flex gap-3">
      <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
        <span class="material-symbols-outlined text-primary text-sm" style="font-variation-settings:'FILL' 1">health_and_safety</span>
      </div>
      <div class="flex-1 max-w-[90%] space-y-2">
        <!-- Badge urgence -->
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border ${cfg.bg} ${cfg.border} ${cfg.text}">
          <span class="w-2 h-2 rounded-full ${cfg.dot} shrink-0"></span>
          <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1">${cfg.icon}</span>
          <span class="text-xs font-extrabold uppercase tracking-wider">${cfg.label}</span>
        </div>
        <!-- Conseil -->
        <div class="${cfg.bg} border ${cfg.border} rounded-xl px-3 py-2">
          <p class="text-xs font-bold ${cfg.text} flex items-center gap-1.5">
            <span class="material-symbols-outlined text-sm">${cfg.icon}</span>
            ${data.conseil.replace(/</g,'&lt;')}
          </p>
        </div>
        <!-- Message -->
        <div class="bg-surface-container-low rounded-2xl rounded-tl-sm px-4 py-3">
          <p class="text-sm text-on-surface leading-relaxed">${data.message.replace(/</g,'&lt;')}</p>
          ${signesHtml}
        </div>
      </div>
    </div>`);
}

function _chatAppendError(msg, messages) {
  messages.insertAdjacentHTML('beforeend', `
    <div class="flex gap-3">
      <div class="w-7 h-7 rounded-full bg-red-50 flex items-center justify-center shrink-0 mt-0.5">
        <span class="material-symbols-outlined text-red-400 text-sm">error</span>
      </div>
      <div class="bg-red-50 border border-red-100 rounded-2xl rounded-tl-sm px-4 py-3 max-w-[85%]">
        <p class="text-xs text-red-600 font-medium">${msg.replace(/</g,'&lt;')}</p>
      </div>
    </div>`);
}

// ── Modifier profil ────────────────────────────────────────────────
function closeModifierProfil() {
  document.getElementById('modalModifierProfil').classList.add('hidden');
}

document.getElementById('formModifierProfil')?.addEventListener('submit', async function(e) {
  e.preventDefault();

  // Reset erreurs
  ['prenom','nom','mail','tel'].forEach(f => {
    const el = document.getElementById('err_'+f);
    if (el) { el.textContent = ''; el.classList.add('hidden'); }
    const inp = document.getElementById('modif_'+f);
    if (inp) inp.classList.remove('border-red-400');
  });
  document.getElementById('profil_error').classList.add('hidden');

  // Chargement
  const btn   = document.getElementById('profil_submit_btn');
  const icon  = document.getElementById('profil_submit_icon');
  const label = document.getElementById('profil_submit_label');
  btn.disabled = true;
  icon.textContent  = 'hourglass_empty';
  label.textContent = 'Enregistrement…';

  try {
    const res  = await fetch('/integration/dossier/patient/update-profile', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        prenom:  document.getElementById('modif_prenom').value.trim(),
        nom:     document.getElementById('modif_nom').value.trim(),
        email:   document.getElementById('modif_mail').value.trim(),
        tel:     document.getElementById('modif_tel').value.trim(),
        adresse: document.getElementById('modif_adresse').value.trim(),
      })
    });
    const data = await res.json();

    if (res.ok && data.success) {
      // Afficher l'état succès
      document.getElementById('formModifierProfil').style.display = 'none';
      const s = document.getElementById('profil_success');
      s.classList.remove('hidden'); s.style.display = 'flex';

      // Mettre à jour le nom affiché dans la page sans reload
      const newName = data.prenom + ' ' + data.nom;
      document.getElementById('modal_subtitle').textContent = newName;
      document.getElementById('modal_avatar').textContent   = (data.prenom[0]+data.nom[0]).toUpperCase();
    } else if (data.errors) {
      Object.entries(data.errors).forEach(([field, msg]) => {
        const key = field === 'email' ? 'mail' : field;
        const el  = document.getElementById('err_'+key);
        const inp = document.getElementById('modif_'+key);
        if (el)  { el.textContent = msg; el.classList.remove('hidden'); }
        if (inp) inp.classList.add('border-red-400');
      });
    } else {
      document.getElementById('profil_error_text').textContent = data.error || 'Erreur lors de l\'enregistrement.';
      document.getElementById('profil_error').classList.remove('hidden');
    }
  } catch {
    document.getElementById('profil_error_text').textContent = 'Problème de connexion. Réessayez.';
    document.getElementById('profil_error').classList.remove('hidden');
  } finally {
    btn.disabled = false;
    icon.textContent  = 'save';
    label.textContent = 'Enregistrer';
  }
});
</script>
