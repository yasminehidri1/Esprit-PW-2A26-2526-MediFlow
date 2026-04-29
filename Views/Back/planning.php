<?php
/**
 * View: Back/planning.php
 * Integrated from Old_Files/planning.php
 */
?>
<style>
:root {
  --pr: #004d99; --prd: #1565c0; --prl: #d6e3ff;
  --bg: #f0f4f8; --sf: #ffffff; --sfl: #f5f7fa;
  --bd: #e2e8f0; --tx: #0f172a; --tm: #64748b;
  --er: #ba1a1a; --ok: #15803d;
}

.planning-container { display: grid; grid-template-columns: 280px 1fr; gap: 24px; padding: 24px 32px; }

/* Mini Calendar */
.sidebar-cal { background: var(--sf); border-radius: 20px; padding: 20px; border: 1px solid var(--bd); position: sticky; top: 24px; }
.minical-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.minical-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; text-align: center; }
.minical-day-name { font-size: 10px; font-weight: 700; color: var(--tm); padding-bottom: 8px; }
.minical-day { font-size: 12px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px; cursor: pointer; transition: 0.15s; color: var(--tx); }
.minical-day:hover { background: var(--sfl); color: var(--pr); }
.minical-day.active { background: var(--pr); color: white; font-weight: 700; }
.minical-day.other { opacity: 0.3; }

/* Main Calendar */
.calendar-main { background: var(--sf); border-radius: 24px; border: 1px solid var(--bd); overflow: hidden; display: flex; flex-direction: column; }
.calendar-toolbar { padding: 20px 24px; border-bottom: 1px solid var(--bd); display: flex; justify-content: space-between; align-items: center; }
.view-switch { display: flex; background: var(--sfl); border-radius: 12px; padding: 4px; }
.view-btn { padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; transition: 0.15s; background: transparent; color: var(--tm); }
.view-btn.active { background: var(--sf); color: var(--pr); box-shadow: 0 2px 8px rgba(0,0,0,0.05); }

/* Grid */
.calendar-grid { flex: 1; display: grid; grid-template-columns: 60px repeat(5, 1fr); background: var(--bd); gap: 1px; }
.time-col { background: var(--sfl); }
.day-col { background: var(--sf); min-height: 800px; position: relative; }
.hour-cell { height: 60px; border-bottom: 1px solid var(--sfl); }
.time-label { height: 60px; font-size: 11px; color: var(--tm); display: flex; align-items: center; justify-content: center; }

/* Event */
.event { 
    position: absolute; left: 4px; right: 4px; border-radius: 8px; padding: 8px; font-size: 11px; 
    border-left: 4px solid transparent; overflow: hidden; cursor: pointer; transition: 0.2s; z-index: 10;
}
.event:hover { transform: scale(1.02); z-index: 20; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.event-rdv { background: #e0f2fe; border-color: var(--pr); color: #0369a1; }
.event-planning { background: #fef9c3; border-color: #ca8a04; color: #854d0e; }
.event-type-urgence { background: #fee2e2; border-color: var(--er); color: #991b1b; }
.event-type-pause { background: #f1f5f9; border-color: #64748b; color: #334155; }

/* Modal */
.modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 1000; align-items: center; justify-content: center; }
.modal { background: white; border-radius: 20px; padding: 32px; width: 480px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); }
.form-group { margin-bottom: 20px; display: flex; flex-direction: column; gap: 8px; }
.form-label { font-size: 12px; font-weight: 700; color: var(--tm); text-transform: uppercase; }
.form-input { padding: 12px 16px; border-radius: 12px; border: 2px solid var(--sfl); font-size: 14px; outline: none; }
.form-input:focus { border-color: var(--pr); }
</style>

<div class="planning-container">
    <!-- Sidebar -->
    <div class="sidebar-left">
        <div class="sidebar-cal">
            <div class="minical-header">
                <span style="font-weight: 800; font-size: 14px;"><?= $mini_cal_date->format('F Y') ?></span>
                <div style="display: flex; gap: 8px;">
                    <a href="?mini_mois=<?= $mini_cal_mois_prec->format('Y-m') ?>" style="text-decoration: none; color: var(--tm);">‹</a>
                    <a href="?mini_mois=<?= $mini_cal_mois_suiv->format('Y-m') ?>" style="text-decoration: none; color: var(--tm);">›</a>
                </div>
            </div>
            <div class="minical-grid">
                <?php foreach(['L','M','M','J','V','S','D'] as $d): ?>
                    <div class="minical-day-name"><?= $d ?></div>
                <?php endforeach; ?>
                <?php 
                    for($i=1; $i < $mini_cal_first_dow; $i++) echo '<div class="minical-day other"></div>';
                    for($d=1; $d <= $mini_cal_days_in_month; $d++) {
                        $cdate = $mini_cal_date->format('Y-m-') . sprintf('%02d', $d);
                        $active = ($cdate === date('Y-m-d')) ? 'active' : '';
                        echo "<a href='?vue=jour&date=$cdate' class='minical-day $active'>$d</a>";
                    }
                ?>
            </div>
        </div>

        <button onclick="openModal()" style="margin-top: 24px; width: 100%; padding: 16px; background: var(--pr); color: white; border: none; border-radius: 16px; font-weight: 800; cursor: pointer; box-shadow: 0 8px 16px rgba(0,77,153,0.2);">
            + Ajouter un événement
        </button>
    </div>

    <!-- Main Calendar -->
    <div class="calendar-main">
        <div class="calendar-toolbar">
            <div style="display: flex; align-items: center; gap: 16px;">
                <h1 style="font-family: 'Manrope'; font-size: 20px; font-weight: 800;"><?= $vue_active === 'semaine' ? 'Semaine ' . $date_debut->format('W') : $date_debut->format('d F Y') ?></h1>
                <div style="display: flex; gap: 4px;">
                    <a href="<?= $url_prec ?>" class="view-btn" style="background: var(--sfl);">‹</a>
                    <a href="<?= $url_suiv ?>" class="view-btn" style="background: var(--sfl);">›</a>
                </div>
            </div>
            <div class="view-switch">
                <a href="?vue=jour" class="view-btn <?= $vue_active==='jour'?'active':'' ?>">Jour</a>
                <a href="?vue=semaine" class="view-btn <?= $vue_active==='semaine'?'active':'' ?>">Semaine</a>
                <a href="?vue=mois" class="view-btn <?= $vue_active==='mois'?'active':'' ?>">Mois</a>
            </div>
        </div>

        <div class="calendar-grid">
            <div class="time-col">
                <?php for($h=8; $h<=19; $h++): ?>
                    <div class="time-label"><?= sprintf('%02dh', $h) ?></div>
                <?php endfor; ?>
            </div>
            
            <?php foreach($jours as $jour_obj): 
                $jstr = $jour_obj->format('Y-m-d');
                $is_today = ($jstr === date('Y-m-d'));
            ?>
                <div class="day-col" style="<?= $is_today ? 'background: #fdfaff;' : '' ?>">
                    <div style="padding: 12px; text-align: center; border-bottom: 2px solid <?= $is_today ? 'var(--pr)' : 'var(--sfl)' ?>;">
                        <div style="font-size: 11px; font-weight: 700; color: var(--tm); text-transform: uppercase;"><?= $jour_obj->format('D') ?></div>
                        <div style="font-size: 18px; font-weight: 800; color: <?= $is_today ? 'var(--pr)' : 'var(--tx)' ?>;"><?= $jour_obj->format('d') ?></div>
                    </div>
                    
                    <?php for($h=8; $h<=19; $h++): ?>
                        <div class="hour-cell"></div>
                    <?php endfor; ?>

                    <?php 
                    if(isset($par_jour[$jstr])) {
                        foreach($par_jour[$jstr] as $ev) {
                            $h_start = (int)substr($ev['debut'], 0, 2);
                            $m_start = (int)substr($ev['debut'], 3, 2);
                            $top = (($h_start - 8) * 60) + $m_start + 45; // 45px for header
                            
                            $dur = 45; // default 45min
                            if($ev['fin']) {
                                $h_end = (int)substr($ev['fin'], 0, 2);
                                $m_end = (int)substr($ev['fin'], 3, 2);
                                $dur = (($h_end - $h_start) * 60) + ($m_end - $m_start);
                            }
                            
                            $cls = ($ev['source'] === 'rdv') ? 'event-rdv' : 'event-planning';
                            if($ev['type'] === 'urgence') $cls = 'event-type-urgence';
                            if($ev['type'] === 'pause') $cls = 'event-type-pause';

                            $djson = htmlspecialchars(json_encode($ev));
                            echo "<div class='event $cls' style='top: {$top}px; height: {$dur}px;' onclick='editEvent($djson)'>";
                            echo "<div style='font-weight: 800;'>{$ev['debut']} - ".($ev['fin'] ?? '')."</div>";
                            echo "<div style='font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;'>{$ev['titre']}</div>";
                            echo "</div>";
                        }
                    }
                    ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modal Add/Edit -->
<div class="modal-overlay" id="eventModal">
    <div class="modal">
        <h2 id="modalTitle" style="margin-bottom: 24px; font-family: 'Manrope';">Ajouter un événement</h2>
        <form method="POST" action="/integration/rdv/planning">
            <input type="hidden" name="action" id="formAction" value="ajouter_evenement">
            <input type="hidden" name="event_id" id="eventId">
            
            <div class="form-group">
                <label class="form-label">Titre</label>
                <input type="text" name="titre" id="fTitre" class="form-input" required placeholder="Ex: Réunion staff">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Début</label>
                    <input type="datetime-local" name="date_debut" id="fDebut" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Fin</label>
                    <input type="datetime-local" name="date_fin" id="fFin" class="form-input" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Type</label>
                <select name="type" id="fType" class="form-input">
                    <option value="autre">Standard</option>
                    <option value="urgence">Urgence</option>
                    <option value="pause">Pause</option>
                    <option value="reunion">Réunion</option>
                    <option value="formation">Formation</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Note</label>
                <textarea name="note" id="fNote" class="form-input" rows="3"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px;">
                <button type="button" onclick="closeModal()" class="view-btn" style="background: var(--sfl);">Annuler</button>
                <button type="submit" class="view-btn" style="background: var(--pr); color: white;">Enregistrer</button>
            </div>

            <div id="deleteBtnContainer" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--bd); display: none;">
                <a id="deleteLink" href="#" style="color: var(--er); font-size: 13px; font-weight: 700; text-decoration: none;" onclick="return confirm('Supprimer cet événement ?')">🗑 Supprimer l'événement</a>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('modalTitle').textContent = "Ajouter un événement";
    document.getElementById('formAction').value = "ajouter_evenement";
    document.getElementById('eventId').value = "";
    document.getElementById('fTitre').value = "";
    document.getElementById('fNote').value = "";
    document.getElementById('deleteBtnContainer').style.display = "none";
    document.getElementById('eventModal').style.display = "flex";
}

function editEvent(ev) {
    if(ev.source === 'rdv') {
        window.location.href = "/integration/rdv/modifier?id=" + ev.id;
        return;
    }
    document.getElementById('modalTitle').textContent = "Modifier l'événement";
    document.getElementById('formAction').value = "modifier_evenement";
    document.getElementById('eventId').value = ev.id;
    document.getElementById('fTitre').value = ev.titre;
    document.getElementById('fDebut').value = ev.debut_dt;
    document.getElementById('fFin').value = ev.fin_dt;
    document.getElementById('fType').value = ev.type;
    document.getElementById('fNote').value = ev.note;
    document.getElementById('deleteLink').href = "?supprimer_event=" + ev.id;
    document.getElementById('deleteBtnContainer').style.display = "block";
    document.getElementById('eventModal').style.display = "flex";
}

function closeModal() {
    document.getElementById('eventModal').style.display = "none";
}
</script>