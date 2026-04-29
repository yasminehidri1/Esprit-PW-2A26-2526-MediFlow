<?php
/**
 * View: Back/statistiques.php
 * Integrated from Old_Files/statistiques.php
 */
?>
<style>
:root {
  --pr: #004d99; --prd: #1565c0; --prl: #d6e3ff;
  --bg: #f0f4f8; --sf: #ffffff; --sfl: #f5f7fa;
  --bd: #e2e8f0; --tx: #0f172a; --tm: #64748b;
  --er: #ba1a1a; --ok: #15803d;
}

.stats-container { padding: 24px 32px; max-width: 1400px; margin: 0 auto; }

/* KPI Cards */
.kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 32px; }
.kpi-card { 
    background: var(--sf); border-radius: 20px; padding: 24px; 
    box-shadow: 0 4px 20px rgba(0,77,153,0.06); border: 1px solid var(--bd);
    display: flex; flex-direction: column; gap: 8px;
}
.kpi-label { font-size: 13px; color: var(--tm); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
.kpi-value { font-family: 'Manrope', sans-serif; font-size: 32px; font-weight: 800; color: var(--tx); }
.kpi-trend { font-size: 12px; font-weight: 700; display: flex; align-items: center; gap: 4px; }
.trend-up { color: var(--ok); }
.trend-down { color: var(--er); }

/* Charts Layout */
.charts-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 24px; }
.chart-card { 
    background: var(--sf); border-radius: 24px; padding: 28px;
    box-shadow: 0 4px 20px rgba(0,77,153,0.06); border: 1px solid var(--bd);
}
.chart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.chart-title { font-family: 'Manrope', sans-serif; font-size: 18px; font-weight: 800; color: var(--tx); }

.gender-stats { display: flex; flex-direction: column; gap: 20px; }
.gender-row { display: flex; align-items: center; gap: 16px; }
.gender-icon { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
.gi-male { background: #e0f2fe; color: #0369a1; }
.gi-female { background: #fce7f3; color: #be185d; }
.gender-info { flex: 1; }
.gender-top { display: flex; justify-content: space-between; margin-bottom: 6px; }
.gender-name { font-weight: 700; font-size: 14px; }
.gender-pct { font-weight: 800; color: var(--pr); }
.progress-bg { height: 8px; background: var(--sfl); border-radius: 4px; overflow: hidden; }
.progress-fill { height: 100%; border-radius: 4px; transition: width 1s ease; }

/* Summary List */
.summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
</style>

<div class="stats-container">
    <div style="margin-bottom: 32px;">
        <h1 style="font-family:'Manrope'; font-size: 28px; font-weight: 800; color: var(--tx);">Statistiques de Performance</h1>
        <p style="color: var(--tm); font-size: 15px;">Analyse détaillée de votre activité et de votre patientèle.</p>
    </div>

    <!-- KPIs -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <span class="kpi-label">RDV ce mois</span>
            <span class="kpi-value"><?= $rdv_ce_mois ?></span>
            <span class="kpi-trend <?= $evol_mois >= 0 ? 'trend-up' : 'trend-down' ?>">
                <?= $evol_mois >= 0 ? '↑' : '↓' ?> <?= abs($evol_mois) ?>% vs mois dernier
            </span>
        </div>
        <div class="kpi-card">
            <span class="kpi-label">Taux de confirmation</span>
            <span class="kpi-value"><?= $taux_confirmation ?>%</span>
            <div class="progress-bg" style="margin-top: 8px;"><div class="progress-fill" style="width: <?= $taux_confirmation ?>%; background: var(--ok);"></div></div>
        </div>
        <div class="kpi-card">
            <span class="kpi-label">Total Année <?= date('Y') ?></span>
            <span class="kpi-value"><?= $rdv_cette_annee ?></span>
            <span class="kpi-trend" style="color: var(--tm)">Cumul annuel</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-label">Moyenne mensuelle</span>
            <span class="kpi-value"><?= $moy_mois ?></span>
            <span class="kpi-trend" style="color: var(--tm)">Patients / mois</span>
        </div>
    </div>

    <!-- Graphiques Principaux -->
    <div class="charts-grid">
        <!-- Évolution mensuelle -->
        <div class="chart-card">
            <div class="chart-header">
                <h2 class="chart-title">Évolution des Rendez-vous</h2>
                <div style="font-size: 12px; color: var(--tm); font-weight: 600;">DERNIERS 12 MOIS</div>
            </div>
            <div style="height: 320px;">
                <canvas id="mainChart"></canvas>
            </div>
        </div>

        <!-- Répartition par genre -->
        <div class="chart-card">
            <div class="chart-header">
                <h2 class="chart-title">Patientèle</h2>
            </div>
            <div style="height: 200px; margin-bottom: 30px;">
                <canvas id="genderChart"></canvas>
            </div>
            <div class="gender-stats">
                <div class="gender-row">
                    <div class="gender-icon gi-male">♂</div>
                    <div class="gender-info">
                        <div class="gender-top"><span class="gender-name">Hommes</span><span class="gender-pct"><?= $pct_homme ?>%</span></div>
                        <div class="progress-bg"><div class="progress-fill" style="width: <?= $pct_homme ?>%; background: #0ea5e9;"></div></div>
                    </div>
                </div>
                <div class="gender-row">
                    <div class="gender-icon gi-female">♀</div>
                    <div class="gender-info">
                        <div class="gender-top"><span class="gender-name">Femmes</span><span class="gender-pct"><?= $pct_femme ?>%</span></div>
                        <div class="progress-bg"><div class="progress-fill" style="width: <?= $pct_femme ?>%; background: #ec4899;"></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section secondaire -->
    <div class="summary-grid">
        <!-- Statuts -->
        <div class="chart-card">
            <h2 class="chart-title" style="margin-bottom: 20px;">Répartition des Statuts</h2>
            <div style="height: 220px;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
        <!-- Évolution annuelle -->
        <div class="chart-card">
            <h2 class="chart-title" style="margin-bottom: 20px;">Historique Annuel</h2>
            <div style="height: 220px;">
                <canvas id="yearChart"></canvas>
            </div>
        </div>
        <!-- Motifs de consultation -->
        <div class="chart-card">
            <h2 class="chart-title" style="margin-bottom: 20px;">Motifs de consultation</h2>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <?php if (empty($motifs_dist)): ?>
                    <p style="color: var(--tm); font-size: 13px;">Aucune donnée disponible.</p>
                <?php else: ?>
                    <?php foreach($motifs_dist as $m): ?>
                        <div style="display: flex; justify-content: space-between; font-size: 14px;">
                            <span><?= htmlspecialchars($m['motif']) ?></span>
                            <span style="font-weight: 700;"><?= $m['pct'] ?>%</span>
                        </div>
                        <div class="progress-bg"><div class="progress-fill" style="width: <?= $m['pct'] ?>%; background: var(--pr);"></div></div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = '#64748b';

// Main Chart
new Chart(document.getElementById('mainChart'), {
    type: 'line',
    data: {
        labels: <?= $json_mois_labels ?>,
        datasets: [{
            label: 'Rendez-vous',
            data: <?= $json_mois_data ?>,
            borderColor: '#004d99',
            backgroundColor: 'rgba(0, 77, 153, 0.05)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointBackgroundColor: '#fff',
            pointBorderColor: '#004d99',
            pointBorderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
            x: { grid: { display: false } }
        }
    }
});

// Gender Chart
new Chart(document.getElementById('genderChart'), {
    type: 'doughnut',
    data: {
        labels: ['Hommes', 'Femmes'],
        datasets: [{
            data: [<?= $nb_homme ?>, <?= $nb_femme ?>],
            backgroundColor: ['#0ea5e9', '#ec4899'],
            borderWidth: 0,
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } } },
        cutout: '75%'
    }
});

// Status Chart
new Chart(document.getElementById('statusChart'), {
    type: 'pie',
    data: {
        labels: ['Confirmés', 'En attente', 'Annulés'],
        datasets: [{
            data: [<?= $nb_confirme ?>, <?= $nb_attente ?>, <?= $nb_annule ?>],
            backgroundColor: ['#15803d', '#004d99', '#ba1a1a'],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'right' } }
    }
});

// Year Chart
new Chart(document.getElementById('yearChart'), {
    type: 'bar',
    data: {
        labels: <?= $json_annee_labels ?>,
        datasets: [{
            label: 'Total annuel',
            data: <?= $json_annee_data ?>,
            backgroundColor: '#d6e3ff',
            borderRadius: 6,
            hoverBackgroundColor: '#004d99'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true },
            x: { grid: { display: false } }
        }
    }
});
</script>