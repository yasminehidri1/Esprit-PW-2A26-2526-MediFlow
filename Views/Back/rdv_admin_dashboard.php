<?php
/**
 * View: Back/rdv_admin_dashboard.php
 * Admin overview of all appointments across the clinic.
 */
?>
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-on-surface font-headline tracking-tight">Clinic Appointments</h1>
            <p class="text-on-surface-variant text-sm mt-1">Global management and monitoring of all clinic consultations.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="flex items-center gap-2 px-4 py-2 bg-primary-fixed text-on-primary-fixed rounded-xl text-sm font-bold">
                <span class="material-symbols-outlined text-lg">medical_services</span>
                <?= $stats['nb_medecins'] ?> Doctors Active
            </span>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-outline-variant/30 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 bg-primary/10 text-primary rounded-2xl flex items-center justify-center">
                <span class="material-symbols-outlined text-3xl">calendar_today</span>
            </div>
            <div>
                <p class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Today</p>
                <p class="text-2xl font-black text-on-surface"><?= $stats['nb_aujourdhui'] ?></p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-outline-variant/30 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 bg-tertiary/10 text-tertiary rounded-2xl flex items-center justify-center">
                <span class="material-symbols-outlined text-3xl">check_circle</span>
            </div>
            <div>
                <p class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Confirmed</p>
                <p class="text-2xl font-black text-on-surface"><?= $stats['nb_confirmes'] ?></p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-outline-variant/30 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 bg-secondary/10 text-secondary rounded-2xl flex items-center justify-center">
                <span class="material-symbols-outlined text-3xl">pending_actions</span>
            </div>
            <div>
                <p class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Pending</p>
                <p class="text-2xl font-black text-on-surface"><?= $stats['nb_attente'] ?></p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-outline-variant/30 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 bg-error/10 text-error rounded-2xl flex items-center justify-center">
                <span class="material-symbols-outlined text-3xl">cancel</span>
            </div>
            <div>
                <p class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Cancelled</p>
                <p class="text-2xl font-black text-on-surface"><?= $stats['nb_annules'] ?></p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="/integration/rdv/admin" class="bg-white p-6 rounded-3xl border border-outline-variant/30 shadow-sm flex flex-wrap items-center gap-6">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-[10px] font-black text-on-surface-variant uppercase mb-1.5 ml-1">Search Patient</label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-on-surface-variant">search</span>
                <input type="text" name="search" value="<?= htmlspecialchars($recherche) ?>" placeholder="Name or CIN..." class="w-full pl-11 pr-4 py-2.5 bg-surface-container-lowest border border-outline-variant/50 rounded-2xl text-sm focus:ring-2 focus:ring-primary/20 outline-none">
            </div>
        </div>
        <div class="w-48">
            <label class="block text-[10px] font-black text-on-surface-variant uppercase mb-1.5 ml-1">Doctor</label>
            <select name="medecin" class="w-full px-4 py-2.5 bg-surface-container-lowest border border-outline-variant/50 rounded-2xl text-sm focus:ring-2 focus:ring-primary/20 outline-none">
                <option value="0">All Doctors</option>
                <?php foreach($liste_medecins as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= $filtre_medecin == $m['id'] ? 'selected' : '' ?>><?= htmlspecialchars($m['nom'] . ' ' . $m['prenom']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="w-40">
            <label class="block text-[10px] font-black text-on-surface-variant uppercase mb-1.5 ml-1">Status</label>
            <select name="statut" class="w-full px-4 py-2.5 bg-surface-container-lowest border border-outline-variant/50 rounded-2xl text-sm focus:ring-2 focus:ring-primary/20 outline-none">
                <option value="">All Status</option>
                <option value="en_attente" <?= $filtre_statut === 'en_attente' ? 'selected' : '' ?>>Pending</option>
                <option value="confirme" <?= $filtre_statut === 'confirme' ? 'selected' : '' ?>>Confirmed</option>
                <option value="annule" <?= $filtre_statut === 'annule' ? 'selected' : '' ?>>Cancelled</option>
            </select>
        </div>
        <div class="pt-5">
            <button type="submit" class="px-6 py-2.5 bg-primary text-on-primary rounded-2xl text-sm font-bold shadow-md hover:opacity-90 transition-opacity">Apply</button>
        </div>
    </form>

    <!-- Results -->
    <div class="space-y-6">
        <?php if (empty($medecins_rdv)): ?>
            <div class="bg-white p-12 rounded-3xl border border-dashed border-outline-variant/50 text-center">
                <span class="material-symbols-outlined text-6xl text-outline-variant/40 mb-4">event_busy</span>
                <h3 class="text-lg font-bold text-on-surface">No appointments found</h3>
                <p class="text-on-surface-variant text-sm mt-1">Try adjusting your filters or search terms.</p>
            </div>
        <?php else: ?>
            <?php foreach($medecins_rdv as $mid => $data): ?>
                <div class="bg-white rounded-3xl border border-outline-variant/30 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-surface-container-low border-b border-outline-variant/30 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary text-on-primary rounded-full flex items-center justify-center font-bold">
                                <?= strtoupper(substr($data['info']['nom'], 0, 1)) ?>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-on-surface"><?= htmlspecialchars($data['info']['nom'] . ' ' . $data['info']['prenom']) ?></h3>
                                <p class="text-[10px] text-on-surface-variant uppercase font-bold tracking-widest">Medical Practitioner</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-white text-primary text-[10px] font-black rounded-full border border-primary/20">
                            <?= count($data['rdvs']) ?> APPOINTMENTS
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-surface-container-lowest">
                                    <th class="px-6 py-3 text-[10px] font-black text-on-surface-variant uppercase tracking-widest">Patient</th>
                                    <th class="px-6 py-3 text-[10px] font-black text-on-surface-variant uppercase tracking-widest">Date & Time</th>
                                    <th class="px-6 py-3 text-[10px] font-black text-on-surface-variant uppercase tracking-widest">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant/10">
                                <?php foreach($data['rdvs'] as $rdv): ?>
                                    <tr class="hover:bg-surface-container-lowest transition-colors">
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-bold text-on-surface"><?= htmlspecialchars($rdv['patient_prenom'] . ' ' . $rdv['patient_nom']) ?></p>
                                            <p class="text-[10px] text-on-surface-variant font-medium">CIN: <?= htmlspecialchars($rdv['cin']) ?></p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-medium text-on-surface"><?= date('D, d M Y', strtotime($rdv['date_rdv'])) ?></p>
                                            <p class="text-xs text-on-surface-variant"><?= date('H:i', strtotime($rdv['heure_rdv'])) ?></p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php
                                            $badgeCls = match($rdv['statut']) {
                                                'confirme'   => 'bg-tertiary-fixed text-on-tertiary-fixed',
                                                'en_attente' => 'bg-primary-fixed text-on-primary-fixed',
                                                'annule'     => 'bg-error-container text-on-error-container',
                                                default      => 'bg-surface-container text-on-surface-variant'
                                            };
                                            $lbl = match($rdv['statut']) {
                                                'confirme'   => 'Confirmed',
                                                'en_attente' => 'Pending',
                                                'annule'     => 'Cancelled',
                                                default      => ucfirst($rdv['statut'])
                                            };
                                            ?>
                                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider <?= $badgeCls ?>">
                                                <?= $lbl ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
