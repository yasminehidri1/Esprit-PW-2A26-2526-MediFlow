<?php
/**
 * View: Back/modifier-rdv.php
 * Integrated from Old_Files/modifier-rdv.php
 */
?>
<style>
:root {
  --primary: #004d99; --primary-dark: #1565c0; --primary-light: #d6e3ff;
  --teal: #005851; --teal-light: #84f5e8;
  --bg: #f0f4f8; --surface: #ffffff; --surface-low: #f5f7fa;
  --border: #e2e8f0; --text: #0f172a; --text-muted: #64748b;
  --error: #ba1a1a;
  --r-md: 12px; --r-lg: 16px; --r-xl: 20px;
}

.page { max-width: 640px; margin: 40px auto; padding: 0 24px; }

.breadcrumb {
  display: flex; align-items: center; gap: 8px;
  font-size: 13px; color: var(--text-muted); margin-bottom: 24px;
}
.breadcrumb a { color: var(--primary); text-decoration: none; font-weight: 600; }
.breadcrumb a:hover { text-decoration: underline; }
.breadcrumb svg { width: 14px; height: 14px; }

.page-title { font-family: 'Manrope', sans-serif; font-size: 26px; font-weight: 800; margin-bottom: 6px; }
.page-sub   { font-size: 14px; color: var(--text-muted); margin-bottom: 28px; }

/* Carte info patient (lecture seule) */
.patient-info-card {
  background: linear-gradient(135deg, var(--primary), var(--primary-dark));
  border-radius: var(--r-xl); padding: 20px 24px; color: white;
  margin-bottom: 24px; display: flex; align-items: center; gap: 16px;
}
.patient-initials {
  width: 52px; height: 52px; border-radius: 50%;
  background: rgba(255,255,255,0.2);
  display: flex; align-items: center; justify-content: center;
  font-family: 'Manrope', sans-serif; font-weight: 800; font-size: 18px;
  flex-shrink: 0;
}
.patient-details .name { font-family: 'Manrope', sans-serif; font-weight: 800; font-size: 17px; }
.patient-details .meta { font-size: 12px; opacity: 0.75; margin-top: 4px; }

/* Formulaire */
.form-card {
  background: var(--surface); border-radius: var(--r-xl);
  padding: 32px; box-shadow: 0 2px 16px rgba(0,77,153,0.08);
}
.form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 20px; }
.form-label {
  font-size: 11px; font-weight: 700; text-transform: uppercase;
  letter-spacing: 0.08em; color: var(--text-muted);
}
.form-input {
  width: 100%; background: var(--surface-low);
  border: 2px solid transparent; border-radius: var(--r-md);
  padding: 12px 16px; font-size: 14px; font-family: 'Inter', sans-serif;
  color: var(--text); outline: none; transition: all 0.18s;
}
.form-input:focus { border-color: var(--teal); background: white; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

.form-actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; }
.btn-cancel {
  padding: 12px 24px; background: transparent;
  border: 1.5px solid var(--border); color: var(--text-muted);
  font-family: 'Manrope', sans-serif; font-weight: 600; font-size: 14px;
  border-radius: var(--r-md); cursor: pointer; text-decoration: none;
  display: inline-flex; align-items: center; transition: all 0.15s;
}
.btn-cancel:hover { border-color: var(--text-muted); color: var(--text); }
.btn-save {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 12px 28px;
  background: linear-gradient(135deg, var(--primary), var(--primary-dark));
  color: white; border: none; border-radius: var(--r-md);
  font-family: 'Manrope', sans-serif; font-weight: 700; font-size: 14px;
  cursor: pointer; box-shadow: 0 3px 12px rgba(0,77,153,0.25);
  transition: all 0.18s;
}
.btn-save:hover { box-shadow: 0 5px 20px rgba(0,77,153,0.35); transform: translateY(-1px); }
.btn-save svg { width: 16px; height: 16px; }
</style>

<div class="page">

  <div class="breadcrumb">
    <a href="/integration/rdv/dashboard">Dashboard</a>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <span>Modifier le RDV</span>
  </div>

  <h1 class="page-title">Modifier le Rendez-vous</h1>
  <p class="page-sub">Modifiez la date, l'heure ou le statut du rendez-vous.</p>

  <!-- Info patient (lecture seule) -->
  <?php
    $initiales = strtoupper(substr($rdv['patient_prenom'], 0, 1) . substr($rdv['patient_nom'], 0, 1));
  ?>
  <div class="patient-info-card">
    <div class="patient-initials"><?= $initiales ?></div>
    <div class="patient-details">
      <div class="name"><?= htmlspecialchars($rdv['patient_prenom'] . ' ' . $rdv['patient_nom']) ?></div>
      <div class="meta">CIN : <?= htmlspecialchars($rdv['cin']) ?> &bull; <?= ucfirst($rdv['genre']) ?></div>
    </div>
  </div>

  <!-- Formulaire modification -->
  <div class="form-card">
    <form method="POST" action="/integration/rdv/modifier">
      <input type="hidden" name="rdv_id" value="<?= $rdv['id'] ?>">

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Date du rendez-vous</label>
          <input class="form-input" type="date" name="date_rdv"
                 value="<?= htmlspecialchars($rdv['date_rdv']) ?>"
                 min="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Heure</label>
          <input class="form-input" type="time" name="heure_rdv"
                 value="<?= htmlspecialchars(substr($rdv['heure_rdv'], 0, 5)) ?>" required>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Statut</label>
        <select class="form-input" name="statut">
          <option value="en_attente" <?= $rdv['statut'] === 'en_attente' ? 'selected' : '' ?>>En attente</option>
          <option value="confirme"   <?= $rdv['statut'] === 'confirme'   ? 'selected' : '' ?>>Confirmé</option>
          <option value="annule"     <?= $rdv['statut'] === 'annule'     ? 'selected' : '' ?>>Annulé</option>
        </select>
      </div>

      <div class="form-actions">
        <a href="/integration/rdv/dashboard" class="btn-cancel">Annuler</a>
        <button type="submit" class="btn-save">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          Enregistrer
        </button>
      </div>
    </form>
  </div>
</div>