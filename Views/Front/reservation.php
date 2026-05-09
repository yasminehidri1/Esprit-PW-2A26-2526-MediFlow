<?php
$eq     = $data['eq']     ?? null;
$erreur = $data['erreur'] ?? null;
$user   = $data['currentUser'] ?? ($_SESSION['user'] ?? []);
$prixDT = $eq ? (float)$eq['prix_jour'] : 0;
$prixDTFmt = number_format($prixDT, 3, ',', '.');

function getEqImageUrl($eq): string {
    if (!$eq) return '';
    $bases = [
        __DIR__ . '/../../assets/images/equipements/',
        __DIR__ . '/../../Assets/images/equipements/',
    ];
    $exts = ['jpg','jpeg','png','webp'];
    foreach ($bases as $base) {
        foreach ($exts as $ext) {
            if (file_exists($base . $eq['reference'] . '.' . $ext)) {
                return '/integration/assets/images/equipements/' . $eq['reference'] . '.' . $ext;
            }
        }
    }
    if (!empty($eq['image'])) {
        return '/integration/assets/images/equipements/' . htmlspecialchars($eq['image']);
    }
    return '';
}
$imgUrl = $eq ? getEqImageUrl($eq) : '';


?>
<html lang="fr" class="light">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Réservation — MediFlow</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="/integration/assets/css/style.css"/>
  <script src="https://js.stripe.com/v3/"></script>
  <!-- Leaflet Maps -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script>
    tailwind.config={darkMode:"class",theme:{extend:{colors:{"primary":"#004d99","primary-fixed":"#d6e3ff","primary-container":"#1565c0","surface":"#f7f9fb","surface-container-low":"#f2f4f6","surface-dim":"#d8dadc","outline":"#727783","on-surface":"#191c1e","on-surface-variant":"#424752"},borderRadius:{DEFAULT:"0.25rem",lg:"0.5rem",xl:"0.75rem",full:"9999px"},fontFamily:{headline:["Manrope"],body:["Inter"]}}}}
  </script>
  <style>
    .form-card{background:#fff;border-radius:14px;border:1px solid #e8eaf0;padding:28px;margin-bottom:20px;}
    .section-title{display:flex;align-items:center;gap:10px;margin-bottom:18px;padding-bottom:12px;border-bottom:1px solid #f3f4f6;}
    .section-title h3{font-family:'Manrope',sans-serif;font-size:15px;font-weight:800;color:#111827;}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
    .form-group{display:flex;flex-direction:column;gap:5px;}
    .form-group label{font-size:12px;font-weight:600;color:#6b7280;}
    .form-input{width:100%;padding:10px 13px;background:#f5f7fa;border:1px solid #e5e7eb;border-radius:8px;font-size:13.5px;font-family:'Inter',sans-serif;color:#111827;outline:none;transition:border-color .18s,box-shadow .18s;}
    .form-input:focus{border-color:#004d99;box-shadow:0 0 0 3px rgba(0,77,153,.10);}
    .delivery-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
    .delivery-opt{display:flex;align-items:center;gap:10px;padding:12px 14px;border-radius:10px;cursor:pointer;border:2px solid transparent;transition:all .18s;}
    .delivery-opt.selected{background:#eff6ff;border-color:#004d99;}
    .delivery-opt.unselected{background:#f9fafb;border-color:#e5e7eb;}
    .delivery-opt input[type="radio"]{display:none;}
    .opt-title{font-size:13px;font-weight:700;display:block;}
    .opt-title.blue{color:#004d99;}
    .opt-title.gray{color:#374151;}
    .opt-sub{font-size:11px;color:#9ca3af;}
    .opt-icon{font-size:20px;}
    .opt-icon.filled{color:#004d99;}
    .opt-icon.unfilled{color:#d1d5db;}
    .summary-card{background:#fff;border-radius:14px;border:1px solid #e8eaf0;padding:24px;position:sticky;top:90px;}
    .summary-card h3{font-family:'Manrope',sans-serif;font-size:16px;font-weight:800;color:#111827;margin-bottom:18px;}
    .summary-row{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #f3f4f6;font-size:13.5px;}
    .summary-row .lbl{color:#6b7280;}
    .summary-row .val{font-weight:700;color:#111827;}
    .summary-row .val.free{color:#16a34a;}
    .summary-divider{border:none;border-top:2px solid #e8eaf0;margin:12px 0;}
    .total-label{font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;}
    .total-amount{font-family:'Manrope',sans-serif;font-size:26px;font-weight:900;color:#004d99;margin-bottom:18px;}
    .btn-confirm{width:100%;padding:14px;border-radius:10px;background:#004d99;color:#fff;border:none;font-size:15px;font-weight:700;font-family:'Inter',sans-serif;cursor:pointer;transition:background .18s;}
    .btn-confirm:hover:not(:disabled){background:#00357a;}
    .btn-confirm:disabled{opacity:.6;cursor:not-allowed;}
    .confirm-legal{font-size:11px;color:#9ca3af;text-align:center;margin-top:12px;}
    .tip-card{display:flex;gap:10px;background:#eff6ff;border-radius:10px;padding:12px;margin-top:14px;}
    .tip-card .material-symbols-outlined{font-size:18px;color:#004d99;flex-shrink:0;}
    .tip-title{font-size:12px;font-weight:700;color:#004d99;display:block;}
    .tip-body{font-size:11px;color:#6b7280;}
    .equip-card{display:flex;gap:16px;align-items:center;background:#fff;border-radius:14px;border:1px solid #e8eaf0;padding:20px;margin-bottom:20px;}
    .equip-info .ref{font-size:11px;font-weight:700;color:#0ea5e9;text-transform:uppercase;letter-spacing:.06em;display:block;}
    .equip-info h2{font-family:'Manrope',sans-serif;font-size:18px;font-weight:900;color:#111827;}
    .equip-info .desc{font-size:13px;color:#9ca3af;}
    .equip-info .price{font-size:16px;font-weight:700;color:#004d99;margin-top:4px;}
    .stepper{display:flex;align-items:center;gap:0;margin-bottom:24px;}
    .step{display:flex;align-items:center;gap:8px;}
    .step-circle{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;}
    .step-circle.active{background:#004d99;color:#fff;}
    .step-circle.inactive{background:#e5e7eb;color:#9ca3af;}
    .step-label{font-size:12.5px;font-weight:600;}
    .step-label.active{color:#004d99;}
    .step-label.inactive{color:#9ca3af;}
    .step-line{flex:1;height:2px;background:#e5e7eb;margin:0 8px;}
    .content-grid{display:grid;grid-template-columns:1fr 340px;gap:24px;align-items:start;}
    .toast-container{position:fixed;bottom:24px;right:24px;display:flex;flex-direction:column;gap:10px;z-index:9999;}
    .toast{display:flex;align-items:center;gap:10px;padding:12px 18px;border-radius:10px;background:#fff;box-shadow:0 4px 20px rgba(0,0,0,.12);font-size:13.5px;font-weight:600;font-family:'Inter',sans-serif;animation:toastIn .3s ease;}
    .toast.success{border-left:4px solid #16a34a;color:#15803d;}
    .toast.error{border-left:4px solid #dc2626;color:#dc2626;}
    .toast.info{border-left:4px solid #004d99;color:#004d99;}
    @keyframes toastIn{from{opacity:0;transform:translateX(20px)}to{opacity:1;transform:translateX(0)}}
    .dispo-msg{display:none;margin-top:14px;padding:14px 16px;border-radius:10px;font-size:13px;line-height:1.6;animation:fadeIn .3s ease;}
    @keyframes fadeIn{from{opacity:0;transform:translateY(-5px)}to{opacity:1;transform:translateY(0)}}
    .dispo-msg.checking{background:#f0f6ff;border:1px solid #bfdbfe;color:#1d4ed8;}
    .dispo-msg.disponible{background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;}
    .dispo-msg.indispo{background:#fff7ed;border:1px solid #fed7aa;color:#c2410c;}
    .dispo-msg-header{display:flex;align-items:center;gap:8px;font-weight:700;font-size:13.5px;margin-bottom:5px;}
    .dispo-msg-header .material-symbols-outlined{font-size:18px;flex-shrink:0;}
    .dispo-msg-detail{font-size:12px;font-weight:500;opacity:.9;}
    .dispo-spinner{width:14px;height:14px;border:2px solid #bfdbfe;border-top-color:#1d4ed8;border-radius:50%;animation:spin .7s linear infinite;flex-shrink:0;}
    @keyframes spin{to{transform:rotate(360deg)}}

    /* 📍 Mini carte Leaflet */
    #map-container {
      display: none;
      margin-top: 10px;
      border-radius: 10px;
      overflow: hidden;
      border: 1.5px solid #bfdbfe;
      animation: fadeIn .3s ease;
    }
    #map-container.show { display: block; }
    #leaflet-map { height: 220px; width: 100%; }
    .map-loading {
      height: 220px; display: flex; align-items: center; justify-content: center;
      background: #f0f6ff; gap: 10px; font-size: 13px; color: #1d4ed8; font-weight: 600;
    }
    .map-error {
      height: 60px; display: flex; align-items: center; justify-content: center;
      background: #fff7ed; gap: 8px; font-size: 12px; color: #c2410c; font-weight: 600;
      border-radius: 0 0 10px 10px;
    }
    .map-address-bar {
      background: #004d99; color: #fff; padding: 8px 14px;
      font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 6px;
    }
    .map-address-bar .material-symbols-outlined { font-size: 15px; }
    .btn-map-icon {
      position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
      width: 32px; height: 32px; border-radius: 7px;
      background: #eff6ff; border: 1px solid #bfdbfe;
      color: #004d99; cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      transition: background .18s, transform .15s;
      flex-shrink: 0;
    }
    .btn-map-icon:hover { background: #dbeafe; transform: translateY(-50%) scale(1.1); }
    .btn-map-icon .material-symbols-outlined { font-size: 17px; }

    /* 🚫 Note retrait — liste noire */
    .nb-retrait {
      display: none;
      gap: 12px;
      align-items: flex-start;
      background: #fff5f5;
      border: 1.5px solid #fca5a5;
      border-radius: 12px;
      padding: 14px 16px;
      margin-bottom: 20px;
      animation: fadeIn .3s ease;
    }
    .nb-retrait.show { display: flex; }
    .nb-retrait .nb-icon-red { font-size: 22px; color: #dc2626; flex-shrink: 0; margin-top: 1px; }
    .nb-retrait .nb-title-red { font-size: 11px; font-weight: 800; color: #991b1b; text-transform: uppercase; letter-spacing: .07em; display: block; margin-bottom: 5px; }
    .nb-retrait .nb-text-red { font-size: 12.5px; color: #7f1d1d; line-height: 1.65; }

    /* 💳 Section paiement */
    .payment-section { margin-top: 16px; }
    .payment-opts { display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; margin-bottom:0; }
    .payment-opt { display:flex; align-items:center; gap:10px; padding:12px 14px; border-radius:10px; cursor:pointer; border:2px solid transparent; transition:all .18s; }
    .payment-opt.selected { background:#f0fdf4; border-color:#16a34a; }
    .payment-opt.unselected { background:#f9fafb; border-color:#e5e7eb; }
    .payment-opt input[type="radio"] { display:none; }
    .payment-opt .opt-icon.green { color:#16a34a; }
    .payment-opt .opt-title.green { color:#16a34a; }

    /* 💳 Card Form — Redesigned */
    .card-form {
      display: none;
      margin-top: 16px;
      border-radius: 18px;
      overflow: hidden;
      animation: fadeIn .35s ease;
      box-shadow: 0 24px 64px rgba(0,0,0,.28), 0 0 0 1px rgba(0,0,0,.1);
    }
    .card-form.show { display: block; }

    /* ── Credit card visual ── */
    .card-visual {
      position: relative;
      height: 210px;
      background: linear-gradient(135deg, #1a1a2e 0%, #16213e 45%, #0f3460 100%);
      padding: 22px 24px 20px;
      overflow: hidden;
      transition: background .55s cubic-bezier(.4,0,.2,1);
    }
    .card-visual.brand-visa       { background: linear-gradient(135deg, #0d1b4b 0%, #1565c0 65%, #0d47a1 100%); }
    .card-visual.brand-mastercard { background: linear-gradient(135deg, #1a0800 0%, #b71c1c 55%, #880e4f 100%); }
    .card-visual.brand-amex       { background: linear-gradient(135deg, #003d38 0%, #00695c 60%, #004d40 100%); }
    .card-visual.brand-discover   { background: linear-gradient(135deg, #4a1a00 0%, #e65100 60%, #bf360c 100%); }

    .card-shine {
      position: absolute; top: -90px; right: -90px;
      width: 280px; height: 280px;
      background: radial-gradient(circle, rgba(255,255,255,.09) 0%, transparent 65%);
      border-radius: 50%; pointer-events: none;
    }
    .card-circle-1 {
      position: absolute; width: 210px; height: 210px; border-radius: 50%;
      bottom: -75px; right: -55px; pointer-events: none;
      background: rgba(255,255,255,.025); border: 1px solid rgba(255,255,255,.06);
    }
    .card-circle-2 {
      position: absolute; width: 140px; height: 140px; border-radius: 50%;
      bottom: -22px; right: 28px; pointer-events: none;
      background: rgba(255,255,255,.018); border: 1px solid rgba(255,255,255,.04);
    }
    .card-top-row {
      display: flex; justify-content: space-between; align-items: flex-start;
      margin-bottom: 22px;
    }
    .card-chip-el {
      width: 40px; height: 30px;
      background: linear-gradient(135deg, #e8c84a 0%, #f5d97a 50%, #c8a830 100%);
      border-radius: 5px; position: relative;
      box-shadow: 0 3px 10px rgba(0,0,0,.35);
    }
    .card-chip-el::before {
      content: ''; position: absolute; top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      width: 22px; height: 15px;
      border: 1.5px solid rgba(0,0,0,.2); border-radius: 3px;
    }
    .card-chip-el::after {
      content: ''; position: absolute; top: 50%; left: 0; right: 0;
      height: 1.5px; background: rgba(0,0,0,.15); transform: translateY(-50%);
    }
    .card-brand-area {
      display: flex; align-items: center; gap: 7px;
    }
    .card-brand-label {
      font-size: 13px; font-weight: 900;
      color: rgba(255,255,255,.75);
      letter-spacing: .1em; text-transform: uppercase;
      transition: all .35s ease;
    }
    .card-num-row {
      font-family: 'Courier New', monospace;
      font-size: 17px; letter-spacing: .18em;
      color: rgba(255,255,255,.82);
      margin-bottom: 20px;
      display: flex; gap: 18px;
      text-shadow: 0 1px 5px rgba(0,0,0,.45);
    }
    .card-bottom-row {
      display: flex; justify-content: space-between; align-items: flex-end;
    }
    .cv-lbl {
      font-size: 9px; font-weight: 700;
      color: rgba(255,255,255,.38);
      text-transform: uppercase; letter-spacing: .1em; margin-bottom: 3px;
    }
    .cv-val {
      font-size: 13px; font-weight: 700;
      color: rgba(255,255,255,.88);
      letter-spacing: .06em; text-transform: uppercase;
      transition: all .22s ease;
    }

    /* ── Stripe input section ── */
    .card-inputs-section {
      background: linear-gradient(180deg, #111827 0%, #0d1421 100%);
      padding: 20px 20px 16px;
    }
    .stripe-input-label {
      font-size: 9px; font-weight: 700;
      color: rgba(255,255,255,.32);
      text-transform: uppercase; letter-spacing: .12em;
      display: flex; align-items: center; gap: 5px;
      margin-bottom: 9px;
    }
    .stripe-input-label .material-symbols-outlined { font-size: 12px; color: #60a5fa; }

    #stripe-card-element {
      padding: 13px 14px;
      background: rgba(255,255,255,.055);
      border: 1.5px solid rgba(255,255,255,.09);
      border-radius: 10px;
      transition: border-color .2s, background .2s, box-shadow .2s;
      margin-bottom: 0;
    }
    #stripe-card-element.StripeElement--focus {
      border-color: #60a5fa;
      background: rgba(96,165,250,.07);
      box-shadow: 0 0 0 3px rgba(96,165,250,.12);
    }
    #stripe-card-element.StripeElement--invalid {
      border-color: #f87171;
      background: rgba(248,113,113,.06);
      box-shadow: 0 0 0 3px rgba(248,113,113,.09);
    }
    #stripe-card-errors {
      font-size: 11px; font-weight: 600; color: #f87171;
      min-height: 18px; margin-top: 6px;
      display: flex; align-items: center; gap: 4px;
    }

    /* ── Name inputs ── */
    .card-name-row {
      display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
      margin-top: 12px;
    }
    .card-name-group { display: flex; flex-direction: column; gap: 5px; }
    .card-name-lbl {
      font-size: 9px; font-weight: 700;
      color: rgba(255,255,255,.35);
      text-transform: uppercase; letter-spacing: .1em;
    }
    .card-name-inp {
      width: 100%; padding: 10px 13px;
      background: rgba(255,255,255,.055);
      border: 1.5px solid rgba(255,255,255,.09);
      border-radius: 10px;
      font-size: 13px; font-family: 'Inter', sans-serif; color: #fff;
      outline: none;
      transition: border-color .2s, background .2s, box-shadow .2s;
    }
    .card-name-inp::placeholder { color: rgba(255,255,255,.22); }
    .card-name-inp:focus {
      border-color: #60a5fa;
      background: rgba(96,165,250,.07);
      box-shadow: 0 0 0 3px rgba(96,165,250,.12);
    }
    .card-name-inp.valid   { border-color: #4ade80 !important; background: rgba(74,222,128,.07) !important; }
    .card-name-inp.invalid { border-color: #f87171 !important; background: rgba(248,113,113,.06) !important; }

    /* ── Footer bar ── */
    .card-footer-bar {
      background: rgba(0,0,0,.35);
      padding: 10px 20px;
      display: flex; align-items: center; gap: 7px;
      border-top: 1px solid rgba(255,255,255,.05);
    }
    .cbadge {
      padding: 2px 9px; border-radius: 4px;
      font-size: 10px; font-weight: 800; letter-spacing: .03em;
    }
    .cbadge-visa { background: #1a1f71; color: #fff; border: 1px solid rgba(255,255,255,.15); }
    .cbadge-mc   { background: #eb001b; color: #fff; }
    .cbadge-amex { background: #007bc1; color: #fff; }
    .card-ssl {
      margin-left: auto;
      display: flex; align-items: center; gap: 4px;
      font-size: 10px; color: rgba(255,255,255,.32); font-weight: 600;
    }
    .card-ssl .material-symbols-outlined { font-size: 12px; color: #4ade80; }

    /* ⚠️ Note annulation */
    .nb-annulation {
      display: flex;
      gap: 12px;
      align-items: flex-start;
      background: #fffbeb;
      border: 1.5px solid #f59e0b;
      border-radius: 12px;
      padding: 14px 16px;
      margin-bottom: 20px;
    }
    .nb-annulation .nb-icon {
      font-size: 22px;
      color: #d97706;
      flex-shrink: 0;
      margin-top: 1px;
    }
    .nb-annulation .nb-title {
      font-size: 11px;
      font-weight: 800;
      color: #92400e;
      text-transform: uppercase;
      letter-spacing: .07em;
      display: block;
      margin-bottom: 5px;
    }
    .nb-annulation .nb-text {
      font-size: 12.5px;
      color: #78350f;
      line-height: 1.65;
    }
  </style>
</head>

<div class="pt-24 pb-12 px-10">
  <?php if ($erreur): ?>
    <div class="max-w-lg mx-auto text-center py-20">
      <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <span class="material-symbols-outlined text-red-500 text-3xl">error_outline</span>
      </div>
      <h2 class="text-xl font-bold mb-2">Équipement introuvable</h2>
      <p class="text-slate-500 mb-6"><?= htmlspecialchars($erreur) ?></p>
      <a href="/integration/catalogue" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl font-bold">
        <span class="material-symbols-outlined text-base">arrow_back</span> Retour au catalogue
      </a>
    </div>
  <?php else: ?>

    <h2 class="text-3xl font-extrabold bg-gradient-to-r from-primary via-primary-container to-primary bg-clip-text text-transparent mb-2">Réservation d'équipement</h2>
    <p class="text-on-surface-variant mb-6 font-medium">Configurez votre location en quelques étapes simples.</p>

    <div class="stepper">
      <div class="step"><div class="step-circle active">1</div><span class="step-label active">Configuration</span></div>
      <div class="step-line"></div>
      <div class="step"><div class="step-circle inactive">2</div><span class="step-label inactive">Livraison</span></div>
      <div class="step-line"></div>
      <div class="step"><div class="step-circle inactive">3</div><span class="step-label inactive">Validation</span></div>
    </div>

    <div class="content-grid">
      <div>

        <!-- Carte équipement -->
        <div class="equip-card">
          <?php if ($imgUrl): ?>
            <img src="<?= $imgUrl ?>"
                 alt="<?= htmlspecialchars($eq['nom']) ?>"
                 style="width:90px;height:90px;object-fit:contain;border-radius:10px;background:#f3f4f6;padding:8px;flex-shrink:0;"
                 loading="lazy"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"/>
            <div style="width:90px;height:90px;background:#f3f4f6;border-radius:10px;display:none;align-items:center;justify-content:center;flex-shrink:0;">
              <span class="material-symbols-outlined" style="font-size:40px;color:#d1d5db;">medical_services</span>
            </div>
          <?php else: ?>
            <div style="width:90px;height:90px;background:#f3f4f6;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <span class="material-symbols-outlined" style="font-size:40px;color:#d1d5db;">medical_services</span>
            </div>
          <?php endif; ?>
          <div class="equip-info">
            <span class="ref">Réf: <?= htmlspecialchars($eq['reference']) ?></span>
            <h2><?= htmlspecialchars($eq['nom']) ?></h2>
            <p class="desc"><?= htmlspecialchars($eq['categorie']) ?></p>
            <div class="price" id="daily-rate" data-rate="<?= $prixDT ?>"><?= $prixDTFmt ?> DT / jour</div>
          </div>
        </div>

        <!-- ⚠️ NB Politique d'annulation — paiement en ligne -->
        <div class="nb-annulation" id="nb-annulation" style="display:none;">
          <span class="material-symbols-outlined nb-icon">warning_amber</span>
          <div>
            <span class="nb-title">⚠ NB — Politique d'annulation (paiement en ligne)</span>
            <span class="nb-text">
              En cas d'annulation après un paiement en ligne, <strong>50 % du montant total réglé seront retenus</strong> à titre de frais d'annulation. Seule la moitié restante vous sera remboursée dans un délai de 5 à 7 jours ouvrables.
            </span>
          </div>
        </div>

        <!-- 🚫 NB Retrait en clinique — liste noire -->
        <div class="nb-retrait" id="nb-retrait">
          <span class="material-symbols-outlined nb-icon-red">gpp_bad</span>
          <div>
            <span class="nb-title-red">⚠ NB — Retrait en clinique : obligation de présence</span>
            <span class="nb-text-red">
              Si vous ne vous présentez pas à la clinique pour récupérer l'équipement réservé,
              votre compte sera <strong>automatiquement placé sur liste noire</strong>.
              Toute nouvelle réservation sera bloquée jusqu'au <strong>règlement complet du montant
              de l'équipement non retiré</strong>.
            </span>
          </div>
        </div>

        <!-- Dates + disponibilité -->
        <div class="form-card">
          <div class="section-title">
            <span class="material-symbols-outlined text-primary">calendar_today</span>
            <h3>Période de location</h3>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="date-start">Date de début <span style="color:#dc2626;">*</span></label>
              <input class="form-input" id="date-start" type="date"/>
            </div>
            <div class="form-group">
              <label for="date-end">Date de fin <span style="color:#dc2626;">*</span></label>
              <input class="form-input" id="date-end" type="date"/>
            </div>
          </div>
          <div id="dispo-msg" class="dispo-msg">
            <div class="dispo-msg-header" id="dispo-header"></div>
            <div class="dispo-msg-detail" id="dispo-detail"></div>
          </div>
        </div>

        <!-- Livraison -->
        <div class="form-card">
          <div class="section-title">
            <span class="material-symbols-outlined text-primary">local_shipping</span>
            <h3>Options de livraison</h3>
          </div>
          <div class="delivery-grid">
            <label class="delivery-opt selected" id="opt-livraison">
              <input type="radio" name="delivery" value="livraison" checked/>
              <div class="opt-text">
                <span class="opt-title blue">Livraison &amp; Installation</span>
                <span class="opt-sub">À domicile (Inclus)</span>
              </div>
              <span class="material-symbols-outlined opt-icon filled">check_circle</span>
            </label>
            <label class="delivery-opt unselected" id="opt-retrait">
              <input type="radio" name="delivery" value="retrait"/>
              <div class="opt-text">
                <span class="opt-title gray">Retrait en clinique</span>
                <span class="opt-sub">Sous 24h (Gratuit)</span>
              </div>
              <span class="material-symbols-outlined opt-icon unfilled">radio_button_unchecked</span>
            </label>
          </div>
          <div id="adresse-section" style="margin-top:16px;">
            <div class="form-group">
              <label for="adresse-livraison" style="display:flex;align-items:center;justify-content:space-between;">
                <span>Adresse de livraison <span style="color:#dc2626;">*</span></span>
                <span style="font-size:10px;color:#9ca3af;font-weight:500;">Cliquez 📍 pour voir sur la carte</span>
              </label>
              <div style="position:relative;">
                <input class="form-input" id="adresse-livraison" type="text"
                       placeholder="Ex: 12 Rue de la République, Tunis"
                       style="padding-right:46px;"
                       oninput="onAdresseInput()"/>
                <button type="button" class="btn-map-icon" id="btn-show-map"
                        onclick="afficherCarte()" title="Voir sur la carte" style="display:none;">
                  <span class="material-symbols-outlined">location_on</span>
                </button>
              </div>
            </div>
            <!-- Mini carte -->
            <div id="map-container">
              <div class="map-address-bar">
                <span class="material-symbols-outlined">navigation</span>
                <span id="map-address-label">—</span>
              </div>
              <div id="leaflet-map"></div>
              <div id="map-error" class="map-error" style="display:none;">
                <span class="material-symbols-outlined">location_off</span>
                Adresse introuvable — vérifiez la saisie
              </div>
            </div>
          </div>

          <!-- 💳 Options de paiement -->
          <div class="payment-section" style="margin-top:20px;">
            <div class="section-title" style="margin-bottom:12px;padding-bottom:10px;">
              <span class="material-symbols-outlined text-primary">payments</span>
              <h3>Mode de paiement</h3>
            </div>
            <div class="payment-opts">
              <label class="payment-opt selected" id="opt-espece">
                <input type="radio" name="payment" value="espece" checked/>
                <div class="opt-text">
                  <span class="opt-title blue">Paiement à la livraison</span>
                  <span class="opt-sub">Espèces à la réception</span>
                </div>
                <span class="material-symbols-outlined opt-icon filled" id="icon-espece">check_circle</span>
              </label>
              <label class="payment-opt unselected" id="opt-ligne">
                <input type="radio" name="payment" value="enligne"/>
                <div class="opt-text">
                  <span class="opt-title gray" id="title-ligne">Paiement en ligne</span>
                  <span class="opt-sub">Carte bancaire sécurisé</span>
                </div>
                <span class="material-symbols-outlined opt-icon unfilled" id="icon-ligne">radio_button_unchecked</span>
              </label>
              <label class="payment-opt unselected" id="opt-clinique">
                <input type="radio" name="payment" value="clinique"/>
                <div class="opt-text">
                  <span class="opt-title gray" id="title-clinique">Paiement en clinique</span>
                  <span class="opt-sub">Sur place à l'accueil</span>
                </div>
                <span class="material-symbols-outlined opt-icon unfilled" id="icon-clinique">radio_button_unchecked</span>
              </label>
            </div>

            <!-- 💳 Card Form — Redesigned -->
            <div class="card-form" id="card-form">

              <!-- Credit card visual preview -->
              <div class="card-visual" id="cv-card">
                <div class="card-shine"></div>
                <div class="card-circle-1"></div>
                <div class="card-circle-2"></div>

                <div class="card-top-row">
                  <div class="card-chip-el"></div>
                  <div class="card-brand-area">
                    <!-- NFC rings -->
                    <svg viewBox="0 0 22 22" fill="none" width="18" style="opacity:.45;">
                      <circle cx="11" cy="11" r="9.5" stroke="white" stroke-width="1.4"/>
                      <circle cx="11" cy="11" r="6"   stroke="white" stroke-width="1.4"/>
                      <circle cx="11" cy="11" r="2.5" fill="white"/>
                    </svg>
                    <span class="card-brand-label" id="cv-brand-label"></span>
                  </div>
                </div>

                <div class="card-num-row">
                  <span>••••</span><span>••••</span><span>••••</span><span>••••</span>
                </div>

                <div class="card-bottom-row">
                  <div>
                    <div class="cv-lbl">Titulaire</div>
                    <div class="cv-val" id="cv-holder">PRÉNOM NOM</div>
                  </div>
                  <div style="text-align:right;">
                    <div class="cv-lbl">Expire</div>
                    <div class="cv-val">•• / ••</div>
                  </div>
                </div>
              </div>

              <!-- Stripe input section -->
              <div class="card-inputs-section">
                <div class="stripe-input-label">
                  <span class="material-symbols-outlined">lock</span>
                  Coordonnées bancaires — Connexion Stripe sécurisée
                </div>
                <div id="stripe-card-element"></div>
                <div id="stripe-card-errors"></div>

                <div class="card-name-row">
                  <div class="card-name-group">
                    <label class="card-name-lbl">Prénom du titulaire</label>
                    <input class="card-name-inp" id="card-prenom" type="text" placeholder="Mohamed"
                           value="<?= htmlspecialchars($user['prenom'] ?? '') ?>" autocomplete="cc-given-name"/>
                  </div>
                  <div class="card-name-group">
                    <label class="card-name-lbl">Nom du titulaire</label>
                    <input class="card-name-inp" id="card-nom" type="text" placeholder="Ben Ali"
                           value="<?= htmlspecialchars($user['nom'] ?? '') ?>" autocomplete="cc-family-name"/>
                  </div>
                </div>
              </div>

              <!-- Footer -->
              <div class="card-footer-bar">
                <!-- Visa -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 54 34" height="26" style="border-radius:5px;flex-shrink:0;">
                  <rect width="54" height="34" fill="white" rx="4"/>
                  <text x="27" y="23" text-anchor="middle" font-family="Arial Black,Arial,sans-serif" font-size="15" font-weight="900" font-style="italic" fill="#1a1f71">VISA</text>
                </svg>
                <!-- Mastercard -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 54 34" height="26" style="border-radius:5px;flex-shrink:0;">
                  <rect width="54" height="34" fill="#252525" rx="4"/>
                  <circle cx="21" cy="17" r="11" fill="#EB001B"/>
                  <circle cx="33" cy="17" r="11" fill="#F79E1B"/>
                  <path d="M27,7.78 A11,11 0 0,1 27,26.22 A11,11 0 0,0 27,7.78Z" fill="#FF5F00"/>
                </svg>
                <!-- Amex -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 54 34" height="26" style="border-radius:5px;flex-shrink:0;">
                  <rect width="54" height="34" fill="#007bc1" rx="4"/>
                  <text x="27" y="23" text-anchor="middle" font-family="Arial Black,Arial,sans-serif" font-size="13" font-weight="900" fill="white" letter-spacing="1">AMEX</text>
                </svg>
                <div class="card-ssl">
                  <span class="material-symbols-outlined">lock</span>
                  SSL 256-bit
                </div>
                <div style="display:flex;align-items:center;gap:4px;font-size:9px;color:rgba(255,255,255,.22);font-weight:700;letter-spacing:.05em;text-transform:uppercase;margin-left:8px;">
                  via&nbsp;
                  <svg viewBox="0 0 60 25" fill="white" xmlns="http://www.w3.org/2000/svg" style="width:30px;opacity:.3;">
                    <path d="M6.2 10.3c0-.7.6-1 1.5-1 1.4 0 3 .4 4.4 1.2V7c-1.5-.6-3-.9-4.4-.9C4.4 6.1 2.5 7.7 2.5 10.5c0 4.3 5.9 3.6 5.9 5.5 0 .8-.7 1.1-1.7 1.1-1.5 0-3.4-.6-4.9-1.5v3.6c1.7.7 3.3 1 4.9 1 3 0 5-1.5 5-4.3-.1-4.7-5.9-3.8-5.9-5.6zM20.5 4.4l-3.8.8v2.4h-2v3.2h2v6.1c0 2.5 1.7 3.5 4.2 3.5.9 0 1.9-.1 2.6-.4v-3.1c-.5.2-1.1.3-1.6.3-.9 0-1.4-.3-1.4-1.3v-5.1h3V7.6h-3V4.4zM29.6 8.8v-1.2h-3.5v12.8h3.6v-7.3c.9-1.1 2.3-1 2.8-.8V9c-.6-.2-2.4-.4-2.9.8v-.2zM34.3 7.6h3.6v12.8h-3.6zM36 2.5c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zM47.4 6.9c-1.1 0-2.1.5-2.7 1.2V7.6h-3.5v17.3h3.6v-5.6c.6.6 1.5 1 2.6 1 3.1 0 5.3-2.5 5.3-6.8s-2.2-6.6-5.3-6.6zm-.9 10.3c-1.1 0-1.8-.6-2.2-1.3v-5.3c.4-.8 1.1-1.4 2.2-1.4 1.6 0 2.7 1.5 2.7 4s-1.1 4-2.7 4z"/>
                  </svg>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Contact -->
        <div class="form-card">
          <div class="section-title">
            <span class="material-symbols-outlined text-primary">person</span>
            <h3>Informations de contact</h3>
          </div>
          <div class="form-row" style="margin-bottom:14px;">
            <div class="form-group">
              <label for="firstname">Prénom <span style="color:#dc2626;">*</span></label>
              <input class="form-input" id="firstname" type="text" placeholder="Mohamed" value="<?= htmlspecialchars($user['prenom']??'') ?>"/>
            </div>
            <div class="form-group">
              <label for="lastname">Nom <span style="color:#dc2626;">*</span></label>
              <input class="form-input" id="lastname" type="text" placeholder="Ben Ali" value="<?= htmlspecialchars($user['nom']??'') ?>"/>
            </div>
          </div>
          <div class="form-group">
            <label for="phone">Téléphone <span style="color:#9ca3af;font-size:11px;">(optionnel)</span></label>
            <input class="form-input" id="phone" type="text" placeholder="20 123 456" value="<?= htmlspecialchars($user['tel']??'') ?>"/>
          </div>
        </div>

      </div>

      <aside class="summary-card">
        <h3>Récapitulatif</h3>
        <input type="hidden" id="equipement_id" value="<?= (int)$eq['id'] ?>"/>
        <div class="summary-row" id="duration-row">
          <span class="lbl">Location</span><span class="val">—</span>
        </div>
        <div class="summary-row">
          <span class="lbl">Frais de livraison</span><span class="val free">OFFERT</span>
        </div>
        <hr class="summary-divider"/>
        <div class="total-label">TOTAL TTC</div>
        <div class="total-amount" id="total-amount">—</div>
        <button class="btn-confirm" id="btn-confirm" type="button" disabled>
          Confirmer la réservation
        </button>
        <p class="confirm-legal">En confirmant, vous acceptez nos conditions générales de location médicale.</p>
        <div class="tip-card">
          <span class="material-symbols-outlined">info</span>
          <div>
            <span class="tip-title">Prise en charge CNAM</span>
            <span class="tip-body">Ce matériel est éligible au remboursement CNAM sous réserve de prescription médicale valide.</span>
          </div>
        </div>
      </aside>
    </div>

  <?php endif; ?>
</div>

<div class="toast-container"></div>
<script>
const API_RES   = '/integration/equipment/api/reservations';
const API_DISPO = '/integration/equipment/api/disponibilite';
const prixDT    = <?= $prixDT ?>;
const equipId      = <?= (int)($eq['id'] ?? 0) ?>;
const patientNom   = <?= json_encode(trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? ''))) ?>;
// ✅ Dates déjà réservées par CE patient — injectées proprement depuis PHP
const mesDatesPHPreservation = null;

function nbJours(d1,d2){if(!d1||!d2)return 0;return Math.ceil((new Date(d2)-new Date(d1))/86400000);}
function formatDT(v){return v.toLocaleString('fr-TN',{minimumFractionDigits:3,maximumFractionDigits:3})+' DT';}
function fmtDate(d){if(!d)return '—';const[y,m,j]=d.split('-');return`${j}/${m}/${y}`;}

function updateTotal(){
  const d1=document.getElementById('date-start').value,d2=document.getElementById('date-end').value,j=nbJours(d1,d2);
  const tot=document.getElementById('total-amount'),dr=document.getElementById('duration-row');
  if(!d1||!d2||j<=0){tot.textContent='—';dr.querySelector('.lbl').textContent='Location';dr.querySelector('.val').textContent='—';return;}
  const t=j*prixDT;tot.textContent=formatDT(t);
  dr.querySelector('.lbl').textContent=`Location (${j} jour${j>1?'s':''})`;
  dr.querySelector('.val').textContent=formatDT(t);
}

let dispoOk=false;
let timerDispo=null;

// ✅ Mémoriser les dates de la dernière demande du patient
const STORAGE_KEY_DATES = 'mediflow_res_' + equipId;
function sauvegarderDerniereDemande(debut, fin) {
  try { localStorage.setItem(STORAGE_KEY_DATES, JSON.stringify({debut, fin})); } catch(e) {}
}
function getDerniereDemande() {
  try { const r = localStorage.getItem(STORAGE_KEY_DATES); return r ? JSON.parse(r) : null; } catch(e) { return null; }
}

function verifierDisponibilite(){
  const debut=document.getElementById('date-start').value;
  const fin=document.getElementById('date-end').value;
  const msg=document.getElementById('dispo-msg');
  const header=document.getElementById('dispo-header');
  const detail=document.getElementById('dispo-detail');
  const btn=document.getElementById('btn-confirm');

  if(!debut||!fin||fin<=debut){msg.style.display='none';dispoOk=false;btn.disabled=true;return;}

  // ✅ CAS 1 — Même patient, mêmes dates
  // Vérifier depuis PHP (réservation existante en BDD) OU depuis localStorage
  const derniere = getDerniereDemande();
  const dejaEnBDD = mesDatesPHPreservation &&
                    mesDatesPHPreservation.debut === debut &&
                    mesDatesPHPreservation.fin   === fin;
  const dejaEnCache = derniere && derniere.debut === debut && derniere.fin === fin;

  if (dejaEnBDD || dejaEnCache) {
    msg.className='dispo-msg indispo'; msg.style.display='block';
    header.innerHTML='<span class="material-symbols-outlined">history</span> Avez-vous oublié votre réservation en cours ?';
    detail.textContent = 'Vous avez déjà une réservation active pour ces mêmes dates (du ' + fmtDate(debut) + ' au ' + fmtDate(fin) + "). Consultez vos réservations en cours avant d'en soumettre une nouvelle.";
    btn.disabled=true; dispoOk=false; return;
  }

  msg.className='dispo-msg checking'; msg.style.display='block';
  header.innerHTML='<div class="dispo-spinner"></div> Vérification de la disponibilité en cours...';
  detail.textContent=''; btn.disabled=true; dispoOk=false;
  clearTimeout(timerDispo);
  timerDispo=setTimeout(async()=>{
    try{
      const res=await fetch(`${API_DISPO}?equipement_id=${equipId}&date_debut=${debut}&date_fin=${fin}`);
      const json=await res.json();
      if(json.disponible){
        const j=nbJours(debut,fin);
        msg.className='dispo-msg disponible';
        header.innerHTML='<span class="material-symbols-outlined">check_circle</span> Équipement disponible sur cette période';
        detail.textContent=`L'équipement est libre du ${fmtDate(debut)} au ${fmtDate(fin)} (${j} jour${j>1?'s':''}). Vous pouvez confirmer votre réservation.`;
        // ✅ Mémoriser immédiatement pour détecter les doublons
        sauvegarderDerniereDemande(debut, fin);
        btn.disabled=false; dispoOk=true;
      }else{
        // ✅ CAS 2 — Dates occupées
        msg.className='dispo-msg indispo';
        const estEnAttente = json.statut === 'en_cours' || json.statut === 'pending';
        if(estEnAttente){
          // Réservation non encore confirmée — expliquer pourquoi
          header.innerHTML='<span class="material-symbols-outlined">hourglass_top</span> Une demande est en attente sur ces dates';
          let txt = 'Une réservation est actuellement en attente de validation par notre équipe pour cette période. ';
          if(json.date_debut_conflit&&json.date_fin_conflit){ txt += `Elle couvre du ${fmtDate(json.date_debut_conflit)} au ${fmtDate(json.date_fin_conflit)}. `; }
          txt += 'Votre demande ne peut pas être traitée tant que cette période est bloquée. Choisissez d\'autres dates ou réessayez ultérieurement.';
          detail.textContent=txt;
        } else {
          // Réservation confirmée
          header.innerHTML='<span class="material-symbols-outlined">event_busy</span> Période indisponible — déjà réservée';
          let txt='Cet équipement est déjà réservé sur les dates choisies.';
          if(json.date_debut_conflit&&json.date_fin_conflit){ txt=`Une réservation confirmée occupe cette période du ${fmtDate(json.date_debut_conflit)} au ${fmtDate(json.date_fin_conflit)}. Veuillez sélectionner d'autres dates.`; }
          else if(json.message){ txt=json.message; }
          detail.textContent=txt;
        }
        btn.disabled=true; dispoOk=false;
      }
    }catch(e){msg.style.display='none';btn.disabled=false;dispoOk=true;}
  },600);
}

document.getElementById('date-start')?.addEventListener('change',()=>{updateTotal();verifierDisponibilite();});
document.getElementById('date-end')?.addEventListener('change',()=>{updateTotal();verifierDisponibilite();});

// ✅ Au chargement : chercher si ce patient a déjà réservé cet équipement
(async function() {
  try {
    const res  = await fetch(API_RES);
    const json = await res.json();
    if (!Array.isArray(json)) return;
    json.forEach(function(r) {
      // Comparer par equipement_id ET nom du patient connecté
      const memeEq      = parseInt(r.equipement_id) === equipId || String(r.equipement_id) === String(equipId);
      const memePatient = patientNom && r.locataire_nom &&
                          r.locataire_nom.toLowerCase().trim() === patientNom.toLowerCase().trim();
      if (memeEq && memePatient && r.date_debut && r.date_fin) {
        sauvegarderDerniereDemande(r.date_debut, r.date_fin);
      }
    });
  } catch(e) { /* silencieux */ }
})();

const optLiv=document.getElementById('opt-livraison'),optRet=document.getElementById('opt-retrait');
const adrsec=document.getElementById('adresse-section'),adrinp=document.getElementById('adresse-livraison');
function setDelivery(isLiv){
  optLiv.className='delivery-opt '+(isLiv?'selected':'unselected');
  optRet.className='delivery-opt '+(isLiv?'unselected':'selected');
  optLiv.querySelector('.opt-title').className='opt-title '+(isLiv?'blue':'gray');
  optRet.querySelector('.opt-title').className='opt-title '+(isLiv?'gray':'blue');
  optLiv.querySelector('.opt-icon').textContent=isLiv?'check_circle':'radio_button_unchecked';
  optRet.querySelector('.opt-icon').textContent=isLiv?'radio_button_unchecked':'check_circle';
  optLiv.querySelector('.opt-icon').className='material-symbols-outlined opt-icon '+(isLiv?'filled':'unfilled');
  optRet.querySelector('.opt-icon').className='material-symbols-outlined opt-icon '+(isLiv?'unfilled':'filled');
  adrsec.style.display=isLiv?'block':'none';
  if(!isLiv)adrinp.value='';
  // Afficher la note liste noire seulement si retrait en clinique
  var nbRetrait = document.getElementById('nb-retrait');
  if(nbRetrait) nbRetrait.classList.toggle('show', !isLiv);
}
optLiv?.addEventListener('click',()=>{document.querySelector('input[value="livraison"]').checked=true;setDelivery(true);});
optRet?.addEventListener('click',()=>{document.querySelector('input[value="retrait"]').checked=true;setDelivery(false);});
setDelivery(true);

/* ── Paiement ── */
const optEspece = document.getElementById('opt-espece');
const optLigne  = document.getElementById('opt-ligne');
const cardForm  = document.getElementById('card-form');

const optClinique = document.getElementById('opt-clinique');

function setPayment(mode) {
  // mode = 'espece' | 'enligne' | 'clinique'
  var opts = {
    'espece':   { el: optEspece,   icon: 'icon-espece',   title: null,           color: 'blue'  },
    'enligne':  { el: optLigne,    icon: 'icon-ligne',    title: 'title-ligne',  color: 'green' },
    'clinique': { el: optClinique, icon: 'icon-clinique', title: 'title-clinique', color: 'blue' }
  };
  ['espece','enligne','clinique'].forEach(function(m) {
    var o = opts[m];
    var isSelected = (m === mode);
    o.el.className = 'payment-opt ' + (isSelected ? 'selected' : 'unselected');
    var iconEl = document.getElementById(o.icon);
    if(iconEl) {
      iconEl.textContent = isSelected ? 'check_circle' : 'radio_button_unchecked';
      iconEl.className   = 'material-symbols-outlined opt-icon ' + (isSelected ? (o.color==='green'?'green':'filled') : 'unfilled');
    }
    if(o.title) {
      var titleEl = document.getElementById(o.title);
      if(titleEl) titleEl.className = 'opt-title ' + (isSelected ? o.color : 'gray');
    }
  });
  // Afficher le formulaire carte seulement si paiement en ligne
  if(mode === 'enligne') {
    cardForm.classList.add('show');
    updateCardHolder();
    // ✅ Valider automatiquement le nom/prenom pré-remplis
    setTimeout(function(){
      document.getElementById('card-prenom')?.dispatchEvent(new Event('input'));
      document.getElementById('card-nom')?.dispatchEvent(new Event('input'));
    }, 100);
    var nbAnn = document.getElementById('nb-annulation');
    if(nbAnn) nbAnn.style.display = 'flex';
  } else {
    cardForm.classList.remove('show');
    var nbAnn = document.getElementById('nb-annulation');
    if(nbAnn) nbAnn.style.display = 'none';
  }
}
optEspece?.addEventListener('click',   () => { document.querySelector('input[value="espece"]').checked=true;   setPayment('espece'); });
optLigne?.addEventListener('click',    () => { document.querySelector('input[value="enligne"]').checked=true;  setPayment('enligne'); });
optClinique?.addEventListener('click', () => { document.querySelector('input[value="clinique"]').checked=true; setPayment('clinique'); });
setPayment('espece');

/* ══════════════════════════════════════════
   💳 VALIDATION CARTE — TEMPS RÉEL GUIDÉE
══════════════════════════════════════════ */

// ── Luhn algorithm ──
function luhn(num) {
  var arr = num.split('').reverse().map(Number);
  var sum = arr.reduce(function(acc,d,i){
    if(i%2!==0){d*=2;if(d>9)d-=9;}return acc+d;
  },0);
  return sum%10===0;
}

// ── Détection type de carte ──
function detectCard(num) {
  var n = num.replace(/\s/g,'');
  if(/^4/.test(n))                          return {type:'visa', label:'VISA',  color:'#1a1f71', len:16, cvc:3};
  if(/^5[1-5]/.test(n)||/^2[2-7]/.test(n)) return {type:'mc',   label:'MC',    color:'#eb001b', len:16, cvc:3};
  if(/^3[47]/.test(n))                      return {type:'amex', label:'AMEX',  color:'#007bc1', len:15, cvc:4};
  return null;
}

// ── setCardFieldState ──
function setCardField(inputId, iconId, errId, isValid, errMsg) {
  var inp  = document.getElementById(inputId);
  var icon = document.getElementById(iconId);
  var err  = document.getElementById(errId);
  if(!inp) return;
  inp.classList.remove('valid','invalid');
  if(inp.value.trim() === '') { if(icon) icon.style.display='none'; if(err) err.textContent=''; return; }
  if(isValid) {
    inp.classList.add('valid');
    if(icon){ icon.textContent='check_circle'; icon.style.display='block'; icon.style.color='#4ade80'; }
    if(err) err.textContent='';
  } else {
    inp.classList.add('invalid');
    if(icon){ icon.textContent='cancel'; icon.style.display='block'; icon.style.color='#f87171'; }
    if(err) err.textContent = errMsg || '';
  }
}

// ── Mise à jour barre de progression ──
function updateProgress() {
  var fields = ['card-number','card-expiry','card-cvc','card-prenom','card-nom'];
  var valid  = fields.filter(function(id){ return document.getElementById(id)?.classList.contains('valid'); }).length;
  var pct    = Math.round((valid/fields.length)*100);
  document.getElementById('card-progress-bar').style.width = pct+'%';
  document.getElementById('card-progress-pct').textContent = pct+'%';
}

// ── Numéro de carte ──
document.getElementById('card-number')?.addEventListener('input', function(){
  var digits = this.value.replace(/\D/g,'');
  var card   = detectCard(digits);
  var maxLen = card ? card.len : 16;
  digits = digits.substring(0, maxLen);
  // Mettre à jour maxlength dynamiquement + formatage
  if(card && card.type==='amex') {
    var p = [digits.slice(0,4), digits.slice(4,10), digits.slice(10,15)].filter(Boolean);
    this.value = p.join('  ');
    this.maxLength = 19; // 4 + 2espaces + 6 + 2espaces + 5
  } else {
    this.value = digits.replace(/(\d{4})(?=\d)/g,'$1  ');
    this.maxLength = 23; // 16 chiffres + 6 doubles espaces = 22
  }

  // Badges type carte
  ['visa','mc','amex'].forEach(function(t){
    var b = document.getElementById('badge-'+t);
    if(b) b.style.opacity = card && card.type===t ? '1' : '.4';
  });
  var badge = document.getElementById('card-type-badge');
  if(badge) {
    if(card){ badge.textContent=card.label; badge.style.background=card.color; badge.style.color='#fff'; badge.style.display='inline-block'; }
    else     { badge.style.display='none'; }
  }
  // Hint CVC
  if(card && card.type==='amex') document.getElementById('hint-cvc').textContent='4 chiffres au recto';
  else                            document.getElementById('hint-cvc').textContent='3 chiffres au dos';

  // Validation
  var clean = this.value.replace(/\s/g,'');
  var complete = clean.length === maxLen;
  var valid    = complete; // ✅ Accepter tout numéro avec le bon nombre de chiffres
  var errMsg   = '';
  if(!complete)    errMsg = 'Numéro incomplet (' + clean.length + '/' + maxLen + ' chiffres)';
  setCardField('card-number','icon-number','err-number', valid, errMsg);
  document.getElementById('hint-number').textContent = clean.length + '/' + maxLen;
  updateProgress();
});

// ── Expiry ──
document.getElementById('card-expiry')?.addEventListener('input', function(){
  var v = this.value.replace(/\D/g,'').substring(0,4);
  if(v.length>=2) v = v.substring(0,2)+'/'+v.substring(2);
  this.value = v;
  var parts = this.value.split('/');
  var mm = parseInt(parts[0]||0,10);
  var yy = parseInt(parts[1]||0,10);
  var now = new Date();
  var curY = now.getFullYear()%100, curM = now.getMonth()+1;
  var complete = parts[0]?.length===2 && parts[1]?.length===2;
  var valid=false; var errMsg='';
  if(!complete){ errMsg='Format MM/AA attendu'; }
  else if(mm<1||mm>12){ errMsg='Mois invalide (01–12)'; }
  else if(yy < curY || (yy===curY && mm<curM)){ errMsg='Carte expirée'; }
  else { valid=true; }
  setCardField('card-expiry','icon-expiry','err-expiry', valid, errMsg);
  if(valid) document.getElementById('hint-expiry').textContent='✓ Non expirée';
  else      document.getElementById('hint-expiry').textContent='';
  updateProgress();
});

// ── CVC ──
document.getElementById('card-cvc')?.addEventListener('input', function(){
  this.value = this.value.replace(/\D/g,'');
  var num = this.value.replace(/\D/g,'');
  var cn  = document.getElementById('card-number')?.value.replace(/\s/g,'')||'';
  var card= detectCard(cn);
  var req = card && card.type==='amex' ? 4 : 3;
  this.maxLength = req;
  var valid = num.length===req;
  setCardField('card-cvc','icon-cvc','err-cvc', valid, valid?'':'CVC incomplet ('+num.length+'/'+req+')');
  updateProgress();
});

// ── Live card holder preview ──
function updateCardHolder() {
  var p = (document.getElementById('card-prenom')?.value || '').trim();
  var n = (document.getElementById('card-nom')?.value    || '').trim();
  var el = document.getElementById('cv-holder');
  if (el) el.textContent = (p || n) ? (p + ' ' + n).trim().toUpperCase() : 'PRÉNOM NOM';
}

// ── Prénom ──
document.getElementById('card-prenom')?.addEventListener('input', function(){
  var v = this.value.trim();
  var valid = v.length>=2 && /^[a-zA-ZÀ-ÿ\s'\-]+$/.test(v);
  setCardField('card-prenom','icon-prenom','err-prenom', valid, valid?'':'Prénom invalide (min 2 lettres)');
  updateProgress();
  updateCardHolder();
});

// ── Nom ──
document.getElementById('card-nom')?.addEventListener('input', function(){
  var v = this.value.trim();
  var valid = v.length>=2 && /^[a-zA-ZÀ-ÿ\s'\-]+$/.test(v);
  setCardField('card-nom','icon-cnom','err-cnom', valid, valid?'':'Nom invalide (min 2 lettres)');
  updateProgress();
  updateCardHolder();
});

function showToast(msg,type='info'){
  const c=document.querySelector('.toast-container');
  const t=document.createElement('div');t.className='toast '+type;
  const icons={success:'check_circle',error:'error',info:'info'};
  t.innerHTML=`<span class="material-symbols-outlined">${icons[type]||'info'}</span><span>${msg}</span>`;
  c.appendChild(t);setTimeout(()=>{t.style.opacity='0';t.style.transition='opacity .3s';setTimeout(()=>t.remove(),300);},3500);
}

function afficherErr(id,msg){const el=document.getElementById(id);if(!el)return;el.style.borderColor='#dc2626';el.style.boxShadow='0 0 0 3px rgba(220,38,38,.10)';el.parentElement.querySelector('.msg-erreur')?.remove();const s=document.createElement('small');s.className='msg-erreur';s.textContent='⚠ '+msg;s.style.cssText='color:#dc2626;font-size:11px;font-weight:600;display:block;margin-top:4px;';el.insertAdjacentElement('afterend',s);}
function effacerErr(id){const el=document.getElementById(id);if(!el)return;el.style.borderColor='';el.style.boxShadow='';el.parentElement?.querySelector('.msg-erreur')?.remove();}

function valider(){
  let ok=true;
  document.querySelectorAll('.msg-erreur').forEach(e=>e.remove());
  document.querySelectorAll('.form-input').forEach(i=>{i.style.borderColor='';i.style.boxShadow='';});
  const today=new Date().toISOString().split('T')[0];
  const prenom=document.getElementById('firstname')?.value.trim();
  const nom=document.getElementById('lastname')?.value.trim();
  const debut=document.getElementById('date-start')?.value;
  const fin=document.getElementById('date-end')?.value;
  const isLiv=document.querySelector('input[name="delivery"]:checked')?.value==='livraison';
  const adresse=adrinp?.value.trim();
  if(!prenom||!/^[a-zA-ZÀ-ÿ\s'\-]{2,50}$/.test(prenom)){afficherErr('firstname','Prénom invalide.');ok=false;}
  if(!nom||!/^[a-zA-ZÀ-ÿ\s'\-]{2,50}$/.test(nom)){afficherErr('lastname','Nom invalide.');ok=false;}
  if(!debut){afficherErr('date-start','Date de début obligatoire.');ok=false;}
  else if(debut<today){afficherErr('date-start','La date ne peut pas être dans le passé.');ok=false;}
  if(!fin){afficherErr('date-end','Date de fin obligatoire.');ok=false;}
  else if(debut&&fin<=debut){afficherErr('date-end','La date de fin doit être après le début.');ok=false;}
  if(isLiv&&(!adresse||adresse.length<5)){afficherErr('adresse-livraison','Adresse obligatoire (min 5 caractères).');ok=false;}
  if(!dispoOk){showToast('Veuillez sélectionner des dates disponibles.','error');ok=false;}
  // Card name validation only (Stripe validates the card number/expiry/cvc itself)
  const isLigne = document.querySelector('input[name="payment"]:checked')?.value === 'enligne';
  if(isLigne){
    const cp = document.getElementById('card-prenom')?.value.trim();
    const cn2= document.getElementById('card-nom')?.value.trim();
    if(!cp){showToast('Prénom du titulaire requis.','error');ok=false;}
    if(!cn2){showToast('Nom du titulaire requis.','error');ok=false;}
  }
  if(!ok)showToast('Veuillez corriger les erreurs.','error');
  return ok;
}

/* ══════════════════════════════════════════
   💳 STRIPE — Initialisation & Paiement
══════════════════════════════════════════ */
const STRIPE_PK   = '<?= STRIPE_PUBLIC_KEY ?>';
const API_INTENT  = '/integration/equipment/api/payment-intent';
const PRIX_JOUR   = <?= (float)$prixDT ?>;

const stripe   = Stripe(STRIPE_PK);
const elements = stripe.elements();
const cardEl   = elements.create('card', {
  hidePostalCode: true,
  style: {
    base: {
      iconColor: '#60a5fa',
      color: '#ffffff',
      fontFamily: "'Inter', sans-serif",
      fontSize: '13.5px',
      fontSmoothing: 'antialiased',
      letterSpacing: '0.04em',
      '::placeholder': { color: 'rgba(255,255,255,0.3)' },
    },
    invalid: { color: '#f87171', iconColor: '#f87171' },
  },
});
cardEl.mount('#stripe-card-element');
cardEl.on('change', e => {
  document.getElementById('stripe-card-errors').textContent = e.error ? e.error.message : '';
  // Update card visual brand
  var cardVis  = document.getElementById('cv-card');
  var brandLbl = document.getElementById('cv-brand-label');
  if (cardVis) {
    var brand = e.brand || 'unknown';
    var labels = { visa:'VISA', mastercard:'MASTERCARD', amex:'AMEX', discover:'DISCOVER' };
    cardVis.className = 'card-visual' + (brand !== 'unknown' ? ' brand-' + brand : '');
    if (brandLbl) brandLbl.textContent = labels[brand] || '';
  }
});

let enCours = false;
document.getElementById('btn-confirm')?.addEventListener('click', async function() {
  if (enCours) return;
  if (!valider()) return;

  enCours = true;
  const btn = this;
  btn.disabled = true;
  btn.textContent = 'Traitement en cours...';

  const isLiv    = document.querySelector('input[name="delivery"]:checked')?.value === 'livraison';
  const isLigne  = document.querySelector('input[name="payment"]:checked')?.value === 'enligne';
  const methode  = document.querySelector('input[name="payment"]:checked')?.value || 'espece';
  const dateD    = document.getElementById('date-start')?.value;
  const dateF    = document.getElementById('date-end')?.value;

  // Calculate number of days for amount
  const msPerDay = 86400000;
  const jours    = dateD && dateF ? Math.max(1, Math.round((new Date(dateF) - new Date(dateD)) / msPerDay)) : 1;
  const totalDT  = Math.round(PRIX_JOUR * jours);

  const basePayload = {
    equipement_id:  document.getElementById('equipement_id')?.value,
    locataire_nom:  (document.getElementById('firstname')?.value.trim() + ' ' + document.getElementById('lastname')?.value.trim()),
    locataire_ville: isLiv ? (adrinp?.value.trim() || '') : '',
    date_debut:     dateD,
    date_fin:       dateF,
    telephone:      document.getElementById('phone')?.value.trim(),
    statut:         'en_cours',
    payment_method: methode,
  };

  const reset = () => { enCours = false; btn.disabled = false; btn.textContent = 'Confirmer la réservation'; };

  try {
    if (isLigne) {
      // 1 — Create PaymentIntent on server
      btn.textContent = 'Connexion Stripe...';
      const piRes  = await fetch(API_INTENT, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ amount_cents: totalDT * 100 }),
      });
      const piData = await piRes.json();
      if (!piRes.ok || !piData.clientSecret) {
        showToast('Erreur Stripe : ' + (piData.error || 'Inconnue'), 'error');
        return reset();
      }

      // 2 — Confirm card payment in browser
      btn.textContent = 'Vérification de la carte...';
      const holderName = (document.getElementById('card-prenom')?.value.trim() + ' ' + document.getElementById('card-nom')?.value.trim()).trim();
      const { paymentIntent, error } = await stripe.confirmCardPayment(piData.clientSecret, {
        payment_method: { card: cardEl, billing_details: { name: holderName } },
      });

      if (error) {
        document.getElementById('stripe-card-errors').textContent = error.message;
        showToast(error.message, 'error');
        return reset();
      }

      // 3 — Payment succeeded — save reservation
      btn.textContent = 'Enregistrement...';
      const payload = { ...basePayload, payment_status: 'paid', stripe_payment_id: paymentIntent.id };
      const res  = await fetch(API_RES, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
      const json = await res.json();
      if (json.success) {
        sauvegarderDerniereDemande(payload.date_debut, payload.date_fin);
        showToast('Paiement accepté ! Réservation confirmée.', 'success');
        setTimeout(() => window.location.href = '/integration/mes-reservations', 1800);
      } else {
        showToast('Paiement OK mais erreur d\'enregistrement : ' + (json.message || ''), 'error');
        reset();
      }

    } else {
      // Cash or clinic — no Stripe
      const payload = { ...basePayload, payment_status: 'pending' };
      const res  = await fetch(API_RES, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
      const json = await res.json();
      if (json.success) {
        sauvegarderDerniereDemande(payload.date_debut, payload.date_fin);
        showToast('Réservation confirmée avec succès !', 'success');
        setTimeout(() => window.location.href = '/integration/mes-reservations', 1800);
      } else {
        showToast('Erreur : ' + (json.message || 'Inconnue'), 'error');
        reset();
      }
    }
  } catch(e) {
    showToast('Erreur réseau.', 'error');
    reset();
  }
});
</script>
<script>
/* ══════════════════════════════════════════
   📍 MAPS — OpenStreetMap + Leaflet + Nominatim
══════════════════════════════════════════ */
var leafletMap    = null;
var leafletMarker = null;
var mapTimer      = null;

function onAdresseInput() {
  var val = document.getElementById('adresse-livraison').value.trim();
  var btn = document.getElementById('btn-show-map');
  if (btn) btn.style.display = val.length >= 5 ? 'flex' : 'none';
  if (val.length < 5) fermerCarte();
}

function afficherCarte() {
  var adresse = document.getElementById('adresse-livraison').value.trim();
  if (!adresse || adresse.length < 5) return;

  var container = document.getElementById('map-container');
  var mapDiv    = document.getElementById('leaflet-map');
  var errDiv    = document.getElementById('map-error');
  var labelDiv  = document.getElementById('map-address-label');

  // Toggle : déjà ouverte → fermer
  if (container.classList.contains('show')) { fermerCarte(); return; }

  container.classList.add('show');
  errDiv.style.display = 'none';
  labelDiv.textContent = adresse;
  mapDiv.innerHTML = '<div class="map-loading"><span class="material-symbols-outlined" style="font-size:20px;">refresh</span> Localisation en cours...</div>';

  clearTimeout(mapTimer);
  mapTimer = setTimeout(async function() {
    try {
      // ✅ Tentatives progressives : adresse exacte → + ville → + pays seul
      var queries = [
        adresse + ', Tunis, Tunisie',
        adresse + ', Tunisie',
        adresse,
      ];
      var data = [];
      for (var qi = 0; qi < queries.length; qi++) {
        var url = 'https://nominatim.openstreetmap.org/search?format=json&limit=3&addressdetails=1&q='
                  + encodeURIComponent(queries[qi]);
        var res  = await fetch(url, { headers: { 'Accept-Language': 'fr', 'User-Agent': 'MediFlow/1.0' } });
        data = await res.json();
        if (data && data.length > 0) break;
        // Petite pause entre tentatives
        await new Promise(function(r){ setTimeout(r, 200); });
      }

      if (!data || data.length === 0) {
        mapDiv.innerHTML = '';
        errDiv.style.display = 'flex';
        return;
      }

      var lat  = parseFloat(data[0].lat);
      var lon  = parseFloat(data[0].lon);
      var name = data[0].display_name;

      mapDiv.innerHTML = '';

      if (leafletMap) { leafletMap.remove(); leafletMap = null; }

      leafletMap = L.map('leaflet-map', { zoomControl: true, scrollWheelZoom: false }).setView([lat, lon], 16);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
      }).addTo(leafletMap);

      var blueIcon = L.divIcon({
        className: '',
        html: '<div style="width:28px;height:28px;background:#004d99;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #fff;box-shadow:0 2px 10px rgba(0,77,153,.5);"></div>',
        iconSize: [28, 28],
        iconAnchor: [14, 28],
        popupAnchor: [0, -30]
      });

      leafletMarker = L.marker([lat, lon], { icon: blueIcon }).addTo(leafletMap);
      leafletMarker.bindPopup(
        '<div style="font-family:Inter,sans-serif;font-size:12px;font-weight:600;color:#004d99;max-width:200px;">' +
        '📍 ' + adresse + '</div>'
      ).openPopup();

      labelDiv.textContent = name.length > 65 ? name.substring(0, 65) + '...' : name;

    } catch(err) {
      mapDiv.innerHTML = '';
      errDiv.style.display = 'flex';
    }
  }, 500);
}

function fermerCarte() {
  var container = document.getElementById('map-container');
  if (container) container.classList.remove('show');
  if (leafletMap) { leafletMap.remove(); leafletMap = null; }
}
</script>