<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Annuaire Médecins — MediFlow</title>
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|inter:400,500,600,700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --primary: #2563eb;
      --primary-dark: #1565c0;
      --primary-light: #dbeafe;
      --primary-glow: rgba(37,99,235,0.12);
      --teal: #0d9488;
      --teal-light: #ccfbf1;
      --teal-dark: #0f766e;
      --bg: #f8fafc;
      --surface: #ffffff;
      --surface-alt: #f1f5f9;
      --border: #e2e8f0;
      --border-light: #f1f5f9;
      --text: #0f172a;
      --text-secondary: #475569;
      --text-muted: #94a3b8;
      --shadow-sm: 0 1px 3px rgba(0,0,0,0.06);
      --shadow-md: 0 4px 20px rgba(0,0,0,0.06);
      --shadow-lg: 0 10px 40px rgba(0,0,0,0.08);
      --shadow-glow: 0 4px 20px rgba(37,99,235,0.15);
      --r: 12px;
      --r-lg: 16px;
      --r-xl: 20px;
      --r-full: 9999px;
      --transition: 0.2s cubic-bezier(0.4,0,0.2,1);
    }

    body {
      font-family: 'Inter', -apple-system, sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      -webkit-font-smoothing: antialiased;
    }

    /* ===== NAVBAR ===== */
    .navbar {
      background: rgba(255,255,255,0.85);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-bottom: 1px solid var(--border);
      padding: 0 40px;
      height: 64px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .navbar-left { display: flex; align-items: center; gap: 40px; }

    .navbar-brand {
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
    }

    .brand-logo {
      width: 36px; height: 36px;
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      box-shadow: 0 2px 8px rgba(37,99,235,0.3);
    }

    .brand-logo svg { width: 20px; height: 20px; fill: white; }

    .brand-name {
      font-family: 'Manrope', sans-serif;
      font-weight: 800;
      font-size: 18px;
      color: var(--text);
    }

    .navbar-links { display: flex; gap: 2px; list-style: none; }

    .navbar-links a {
      display: flex; align-items: center; gap: 7px;
      padding: 8px 14px;
      border-radius: 8px;
      color: var(--text-secondary);
      font-size: 13.5px; font-weight: 500;
      text-decoration: none;
      transition: all var(--transition);
    }

    .navbar-links a svg { width: 16px; height: 16px; }

    .navbar-links a:hover { background: var(--surface-alt); color: var(--text); }

    .navbar-links a.active {
      background: var(--primary-light);
      color: var(--primary);
      font-weight: 600;
    }

    .navbar-right { display: flex; align-items: center; gap: 6px; }

    .icon-btn {
      width: 36px; height: 36px;
      border: none; background: transparent;
      border-radius: 8px;
      display: flex; align-items: center; justify-content: center;
      color: var(--text-muted); cursor: pointer;
      transition: all var(--transition);
    }

    .icon-btn:hover { background: var(--surface-alt); color: var(--text-secondary); }
    .icon-btn svg { width: 18px; height: 18px; }

    .avatar-btn {
      width: 34px; height: 34px;
      border-radius: var(--r-full);
      background: linear-gradient(135deg, var(--primary), #7c3aed);
      border: none; cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      transition: transform var(--transition);
    }

    .avatar-btn:hover { transform: scale(1.08); }
    .avatar-btn svg { width: 16px; height: 16px; fill: white; }

    /* ===== HERO ===== */
    .hero {
      background: linear-gradient(135deg, #1e3a5f 0%, var(--primary) 50%, #7c3aed 100%);
      padding: 56px 40px 80px;
      position: relative;
      overflow: hidden;
    }

    .hero::before {
      content: '';
      position: absolute;
      top: -50%; right: -20%;
      width: 600px; height: 600px;
      background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 60%);
      border-radius: 50%;
    }

    .hero::after {
      content: '';
      position: absolute;
      bottom: -40%; left: -10%;
      width: 400px; height: 400px;
      background: radial-gradient(circle, rgba(124,58,237,0.2) 0%, transparent 60%);
      border-radius: 50%;
    }

    .hero-content {
      max-width: 700px;
      margin: 0 auto;
      text-align: center;
      position: relative;
      z-index: 1;
    }

    .hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 5px 14px;
      background: rgba(255,255,255,0.15);
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: var(--r-full);
      color: white;
      font-size: 12px;
      font-weight: 600;
      margin-bottom: 16px;
      backdrop-filter: blur(10px);
    }

    .hero-badge svg { width: 14px; height: 14px; }

    .hero h1 {
      font-family: 'Manrope', sans-serif;
      font-size: 36px;
      font-weight: 800;
      color: white;
      margin-bottom: 10px;
      letter-spacing: -0.02em;
    }

    .hero p {
      font-size: 15px;
      color: rgba(255,255,255,0.75);
      line-height: 1.6;
    }

    /* ===== SEARCH BAR ===== */
    .search-section {
      max-width: 900px;
      margin: -36px auto 0;
      padding: 0 24px;
      position: relative;
      z-index: 10;
    }

    .search-card {
      background: var(--surface);
      border-radius: var(--r-xl);
      box-shadow: var(--shadow-lg);
      padding: 8px;
      display: flex;
      gap: 6px;
    }

    .search-input-wrap {
      display: flex;
      align-items: center;
      background: var(--surface-alt);
      border-radius: var(--r);
      padding: 0 16px;
      gap: 10px;
      flex: 1;
      border: 2px solid transparent;
      transition: all var(--transition);
    }

    .search-input-wrap:focus-within {
      border-color: var(--primary);
      background: white;
      box-shadow: 0 0 0 4px var(--primary-glow);
    }

    .search-input-wrap svg { width: 18px; height: 18px; color: var(--text-muted); flex-shrink: 0; }

    .search-input-wrap input {
      border: none; outline: none;
      font-size: 14px; font-family: inherit;
      color: var(--text);
      width: 100%; padding: 14px 0;
      background: transparent;
    }

    .search-input-wrap input::placeholder { color: var(--text-muted); }

    .filter-select {
      padding: 0 16px;
      background: var(--surface-alt);
      border: 2px solid transparent;
      border-radius: var(--r);
      color: var(--text);
      font-family: inherit;
      font-size: 14px;
      cursor: pointer; outline: none;
      min-width: 170px;
      transition: all var(--transition);
      -webkit-appearance: none;
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2394a3b8' viewBox='0 0 16 16'%3E%3Cpath d='M4.5 6l3.5 4 3.5-4H4.5z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 12px center;
      padding-right: 36px;
    }

    .filter-select:focus {
      border-color: var(--primary);
      background-color: white;
      box-shadow: 0 0 0 4px var(--primary-glow);
    }

    .search-btn {
      padding: 0 24px;
      background: var(--primary);
      color: white;
      border: none;
      border-radius: var(--r);
      font-family: 'Manrope', sans-serif;
      font-weight: 700;
      font-size: 14px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: all var(--transition);
      white-space: nowrap;
    }

    .search-btn:hover { background: var(--primary-dark); box-shadow: var(--shadow-glow); }
    .search-btn svg { width: 16px; height: 16px; }

    /* ===== STATS BAR ===== */
    .stats-bar {
      max-width: 900px;
      margin: 20px auto 0;
      padding: 0 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .results-info {
      font-size: 13px;
      color: var(--text-muted);
    }

    .results-info strong { color: var(--text); font-weight: 600; }

    .view-toggle {
      display: flex;
      gap: 4px;
      background: var(--surface);
      padding: 3px;
      border-radius: 8px;
      border: 1px solid var(--border);
    }

    .view-toggle button {
      width: 32px; height: 32px;
      border: none;
      background: transparent;
      border-radius: 6px;
      display: flex; align-items: center; justify-content: center;
      color: var(--text-muted);
      cursor: pointer;
      transition: all var(--transition);
    }

    .view-toggle button.active { background: var(--primary); color: white; }
    .view-toggle button svg { width: 16px; height: 16px; }

    /* ===== GRID ===== */
    .page-content {
      max-width: 1100px;
      margin: 0 auto;
      padding: 28px 24px 60px;
    }

    .doctors-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 20px;
    }

    /* ===== DOCTOR CARD ===== */
    .doctor-card {
      background: var(--surface);
      border-radius: var(--r-lg);
      border: 1px solid var(--border);
      overflow: hidden;
      transition: all var(--transition);
      position: relative;
    }

    .doctor-card:hover {
      border-color: var(--primary-light);
      box-shadow: var(--shadow-md);
      transform: translateY(-3px);
    }

    .card-top-accent {
      height: 3px;
      background: linear-gradient(90deg, var(--primary), #7c3aed);
      opacity: 0;
      transition: opacity var(--transition);
    }

    .doctor-card:hover .card-top-accent { opacity: 1; }

    .card-body { padding: 22px 24px; }

    .card-header {
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 16px;
    }

    .doctor-avatar-placeholder {
      width: 56px; height: 56px;
      border-radius: 14px;
      background: linear-gradient(135deg, var(--primary-light), #ede9fe);
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }

    .doctor-avatar-placeholder svg { width: 24px; height: 24px; fill: var(--primary); opacity: 0.7; }

    .card-meta { flex: 1; min-width: 0; }

    .card-meta h3 {
      font-family: 'Manrope', sans-serif;
      font-size: 15px;
      font-weight: 700;
      color: var(--text);
      margin-bottom: 6px;
    }

    .specialty-badge {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 3px 10px;
      background: var(--teal-light);
      color: var(--teal-dark);
      border-radius: var(--r-full);
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 0.02em;
    }

    .card-rating {
      display: flex;
      align-items: center;
      gap: 2px;
      margin-left: auto;
      flex-shrink: 0;
    }

    .card-rating svg { width: 14px; height: 14px; fill: #facc15; }

    .card-rating span {
      font-size: 12px;
      font-weight: 600;
      color: var(--text-secondary);
      margin-left: 4px;
    }

    .card-details {
      display: flex;
      flex-direction: column;
      gap: 8px;
      margin-bottom: 18px;
    }

    .detail-row {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 13px;
      color: var(--text-secondary);
    }

    .detail-row svg { width: 15px; height: 15px; flex-shrink: 0; color: var(--text-muted); }

    .detail-row .highlight { color: var(--teal-dark); font-weight: 600; }

    .card-divider {
      height: 1px;
      background: var(--border-light);
      margin-bottom: 16px;
    }

    .card-actions {
      display: flex;
      gap: 8px;
    }

    .btn-rdv {
      flex: 1;
      padding: 11px 16px;
      background: var(--primary);
      color: white;
      border: none;
      border-radius: 10px;
      font-family: 'Manrope', sans-serif;
      font-weight: 700;
      font-size: 13px;
      cursor: pointer;
      transition: all var(--transition);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
    }

    .btn-rdv:hover { background: var(--primary-dark); box-shadow: var(--shadow-glow); }
    .btn-rdv svg { width: 15px; height: 15px; }

    .btn-outline {
      padding: 11px 14px;
      background: transparent;
      color: var(--text-secondary);
      border: 1.5px solid var(--border);
      border-radius: 10px;
      font-family: 'Manrope', sans-serif;
      font-weight: 600;
      font-size: 13px;
      cursor: pointer;
      transition: all var(--transition);
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .btn-outline:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-glow); }
    .btn-outline svg { width: 15px; height: 15px; }

    /* ===== EMPTY STATE ===== */
    .empty-state {
      text-align: center;
      padding: 60px 20px;
      display: none;
    }

    .empty-state svg { width: 48px; height: 48px; color: var(--text-muted); margin-bottom: 16px; }
    .empty-state h3 { font-family: 'Manrope', sans-serif; font-size: 18px; font-weight: 700; margin-bottom: 6px; }
    .empty-state p { font-size: 14px; color: var(--text-muted); }

    /* ===== FOOTER ===== */
    .footer {
      background: var(--surface);
      border-top: 1px solid var(--border);
      padding: 24px 40px;
      text-align: center;
      font-size: 13px;
      color: var(--text-muted);
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
      .navbar { padding: 0 16px; }
      .navbar-links { display: none; }
      .hero { padding: 40px 20px 70px; }
      .hero h1 { font-size: 26px; }
      .search-section, .stats-bar { padding: 0 16px; }
      .search-card { flex-direction: column; padding: 12px; }
      .filter-select { min-width: 100%; padding: 14px 16px; }
      .search-btn { justify-content: center; padding: 14px; }
      .page-content { padding: 24px 16px 40px; }
      .doctors-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<!-- ===== NAVBAR ===== -->
<nav class="navbar">
  <div class="navbar-left">
    <a href="index.php" class="navbar-brand">
      <div class="brand-logo">
        <svg viewBox="0 0 24 24"><path d="M19 8h-3V5a1 1 0 0 0-1-1h-6a1 1 0 0 0-1 1v3H5a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h3v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3h3a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1zm-1 6h-3a1 1 0 0 0-1 1v3h-4v-3a1 1 0 0 0-1-1H6v-4h3a1 1 0 0 0 1-1V6h4v3a1 1 0 0 0 1 1h3v4z"/></svg>
      </div>
      <span class="brand-name">MediFlow</span>
    </a>
    <div class="navbar-links">
      <a href="profil.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
        Mon Profil
      </a>
      <a href="mes-rdv.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        Mes RDV
      </a>
      <a href="annuaire.php" class="active">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        Annuaire
      </a>
    </div>
  </div>
  <div class="navbar-right">
    <button class="icon-btn" title="Notifications">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
    </button>
    <button class="avatar-btn" title="Profil">
      <svg viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
    </button>
  </div>
</nav>

<!-- ===== HERO ===== -->
<section class="hero">
  <div class="hero-content">
    <div class="hero-badge">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      Annuaire des praticiens
    </div>
    <h1>Trouvez votre spécialiste</h1>
    <p>Explorez notre réseau de professionnels de santé qualifiés et prenez rendez-vous en ligne en quelques clics.</p>
  </div>
</section>

<!-- ===== SEARCH ===== -->
<div class="search-section">
  <div class="search-card">
    <div class="search-input-wrap">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="searchInput" placeholder="Rechercher un médecin par nom..." oninput="filterDoctors()">
    </div>
    <select class="filter-select" id="filterSpecialty" onchange="filterDoctors()">
      <option value="">Toutes les spécialités</option>
      <option value="Cardiologue">Cardiologue</option>
      <option value="Pédiatre">Pédiatre</option>
      <option value="Neurologue">Neurologue</option>
      <option value="Chirurgien">Chirurgien</option>
      <option value="Ophtalmologue">Ophtalmologue</option>
      <option value="Dermatologue">Dermatologue</option>
    </select>
    <button class="search-btn" onclick="filterDoctors()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      Rechercher
    </button>
  </div>
</div>

<!-- ===== STATS ===== -->
<div class="stats-bar">
  <div class="results-info">
    <strong id="resultsCount">6</strong> médecins disponibles
  </div>
  <div class="view-toggle">
    <button class="active" title="Grille">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
    </button>
    <button title="Liste">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
  </div>
</div>

<!-- ===== CONTENU ===== -->
<div class="page-content">
  <!--
    En PHP MVC :
    <?php foreach ($medecins as $med): ?>
      <div class="doctor-card" data-nom="<?= htmlspecialchars($med['nom']) ?>" data-specialite="<?= htmlspecialchars($med['specialite']) ?>">
        ...
      </div>
    <?php endforeach; ?>
  -->
  <div class="doctors-grid" id="doctorsGrid">

    <!-- Carte 1 -->
    <div class="doctor-card" data-nom="Sarah Valory" data-specialite="Cardiologue">
      <div class="card-top-accent"></div>
      <div class="card-body">
        <div class="card-header">
          <div class="doctor-avatar-placeholder">
            <svg viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
          </div>
          <div class="card-meta">
            <h3>Dr. Sarah Valory</h3>
            <span class="specialty-badge">Cardiologue</span>
          </div>
          <div class="card-rating">
            <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            <span>4.9</span>
          </div>
        </div>
        <div class="card-details">
          <div class="detail-row">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span>Expert en Rythmologie</span>
          </div>
          <div class="detail-row">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span class="highlight">Disponible demain, 09:00</span>
          </div>
        </div>
        <div class="card-divider"></div>
        <div class="card-actions">
          <button class="btn-rdv" onclick="window.location.href='rdv.php?medecin_id=1&nom=Sarah Valory&specialite=Cardiologue'">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Prendre RDV
          </button>
          <button class="btn-outline" onclick="window.location.href='planning.php?id=1'">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Planning
          </button>
        </div>
      </div>
    </div>

    <!-- Carte 2 -->
    <div class="doctor-card" data-nom="Thomas Morin" data-specialite="Pédiatre">
      <div class="card-top-accent"></div>
      <div class="card-body">
        <div class="card-header">
          <div class="doctor-avatar-placeholder">
            <svg viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
          </div>
          <div class="card-meta">
            <h3>Dr. Thomas Morin</h3>
            <span class="specialty-badge">Pédiatre</span>
          </div>
          <div class="card-rating">
            <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            <span>4.7</span>
          </div>
        </div>
        <div class="card-details">
          <div class="detail-row">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span>Spécialiste Petite Enfance</span>
          </div>
          <div class="detail-row">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span class="highlight">Disponible le 22 Mai</span>
          </div>
        </div>
        <div class="card-divider"></div>
        <div class="card-actions">
          <button class="btn-rdv" onclick="window.location.href='rdv.php?medecin_id=2&nom=Thomas Morin&specialite=Pédiatre'">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Prendre RDV
          </button>
          <button class="btn-outline" onclick="window.location.href='planning.php?id=2'">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Planning
          </button>
        </div>
      </div>
    </div>

    <!-- Carte 3 -->
    <div class="doctor-card" data-nom="Lena Dupont" data-specialite="Neurologue">
      <div class="card-top-accent"></div>
      <div class="card-body">
        <div class="card-header">
          <div class="doctor-avatar-placeholder">
            <svg viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
          </div>
          <div class="card-meta">
            <h3>Dr. Lena Dupont</h3>
            <span class="specialty-badge">Neurologue</span>
          </div>
          <div class="card-rating">
            <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            <span>4.8</span>
          </div>
        </div>
        <div class="card-details">
          <div class="detail-row">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span>Expert Troubles du Sommeil</span>
          </div>
          <div class="detail-row">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span class="highlight">Disponible ce soir</span>
          </div>
        </div>
        <div class="card-divider"></div>
        <div class="card-actions">
          <button class="btn-rdv" onclick="window.location.href='rdv.php?medecin_id=3&nom=Lena Dupont&specialite=Neurologue'">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Prendre RDV
          </button>
          <button class="btn-outline" onclick="window.location.href='planning.php?id=3'">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Planning
          </button>
        </div>
      </div>
    </div>

    <!-- Carte 4 -->
    <div class="doctor-card" data-nom="Marc Jobert" data-specialite="Chirurgien">
      <div class="card-top-accent"></div>
      <div class="card-body">
        <div class="card-header">
          <div class="doctor-avatar-placeholder">
            <svg viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
          </div>
          <div class="card-meta">
            <h3>Dr. Marc Jobert</h3>
            <span class="specialty-badge">Chirurgien</span>
          </div>
          <div class="card-rating">
            <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            <span>4.6</span>
          </div>
        </div>
        <div class="card-details">
          <div class="detail-row">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span>Chirurgie Viscérale</span>
          </div>
          <div class="detail-row">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span class="highlight">Disponible le 24 Mai</span>
          </div>
        </div>
        <div class="card-divider"></div>
        <div class="card-actions">
          <button class="btn-rdv" onclick="window.location.href='rdv.php?medecin_id=4&nom=Marc Jobert&specialite=Chirurgien'">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Prendre RDV
          </button>
          <button class="btn-outline" onclick="window.location.href='planning.php?id=4'">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Planning
          </button>
        </div>
      </div>
    </div>

    <!-- Carte 5 -->
    <div class="doctor-card" data-nom="Amine Kader" data-specialite="Ophtalmologue">
      <div class="card-top-accent"></div>
      <div class="card-body">
        <div class="card-header">
          <div class="doctor-avatar-placeholder">
            <svg viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
          </div>
          <div class="card-meta">
            <h3>Dr. Amine Kader</h3>
            <span class="specialty-badge">Ophtalmologue</span>
          </div>
          <div class="card-rating">
            <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            <span>4.5</span>
          </div>
        </div>
        <div class="card-details">
          <div class="detail-row">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span>Chirurgie Réfractive</span>
          </div>
          <div class="detail-row">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span class="highlight">Disponible en Juin</span>
          </div>
        </div>
        <div class="card-divider"></div>
        <div class="card-actions">
          <button class="btn-rdv" onclick="window.location.href='rdv.php?medecin_id=5&nom=Amine Kader&specialite=Ophtalmologue'">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Prendre RDV
          </button>
          <button class="btn-outline" onclick="window.location.href='planning.php?id=5'">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Planning
          </button>
        </div>
      </div>
    </div>

    <!-- Carte 6 -->
    <div class="doctor-card" data-nom="Sophie Morel" data-specialite="Dermatologue">
      <div class="card-top-accent"></div>
      <div class="card-body">
        <div class="card-header">
          <div class="doctor-avatar-placeholder">
            <svg viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
          </div>
          <div class="card-meta">
            <h3>Dr. Sophie Morel</h3>
            <span class="specialty-badge">Dermatologue</span>
          </div>
          <div class="card-rating">
            <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            <span>4.8</span>
          </div>
        </div>
        <div class="card-details">
          <div class="detail-row">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span>Dermatologie Esthétique</span>
          </div>
          <div class="detail-row">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span class="highlight">Disponible demain</span>
          </div>
        </div>
        <div class="card-divider"></div>
        <div class="card-actions">
          <button class="btn-rdv" onclick="window.location.href='rdv.php?medecin_id=6&nom=Sophie Morel&specialite=Dermatologue'">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Prendre RDV
          </button>
          <button class="btn-outline" onclick="window.location.href='planning.php?id=6'">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Planning
          </button>
        </div>
      </div>
    </div>

  </div>

  <!-- Empty state -->
  <div class="empty-state" id="emptyState">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <h3>Aucun médecin trouvé</h3>
    <p>Essayez de modifier vos critères de recherche.</p>
  </div>
</div>

<!-- ===== FOOTER ===== -->
<footer class="footer">
  © 2025 MediFlow — Plateforme de gestion médicale
</footer>

<script>
  function filterDoctors() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    const spe = document.getElementById('filterSpecialty').value;
    let count = 0;
    document.querySelectorAll('.doctor-card').forEach(card => {
      const nom = card.dataset.nom.toLowerCase();
      const specialite = card.dataset.specialite;
      const matchText = nom.includes(q) || specialite.toLowerCase().includes(q);
      const matchSpe = !spe || specialite === spe;
      const show = matchText && matchSpe;
      card.style.display = show ? '' : 'none';
      if (show) count++;
    });
    document.getElementById('resultsCount').textContent = count;
    document.getElementById('emptyState').style.display = count === 0 ? 'block' : 'none';
  }
</script>
</body>
</html>