<?php
/**
 * _notifications.php
 * À inclure dans Views/Back/layout.php à la place de la cloche existante
 * Placer ce fichier dans : Views/Back/partials/_notifications.php
 *
 * Dans layout.php, remplace la cloche actuelle par :
 * <?php include __DIR__ . '/partials/_notifications.php'; ?>
 */

require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../Services/NotificationService.php';

$notif_user_id = $_SESSION['user']['id'] ?? 0;
$notifService  = new \Services\NotificationService(\config::getConnexion());
$notifs        = $notifService->getTout($notif_user_id, 15);
$nb_non_lues   = $notifService->compterNonLues($notif_user_id);
$base_notif    = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
?>

<!-- ── Cloche notifications ─────────────────────────────── -->
<div style="position:relative;display:inline-block;" id="notifWrapper">

    <!-- Bouton cloche -->
    <button id="notifBtn"
            onclick="toggleNotifs()"
            style="position:relative;width:38px;height:38px;border-radius:50%;border:none;background:rgba(0,77,153,0.07);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .15s;"
            onmouseenter="this.style.background='rgba(0,77,153,0.15)'"
            onmouseleave="this.style.background='rgba(0,77,153,0.07)'"
            title="Notifications">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#004d99" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
        </svg>
        <?php if ($nb_non_lues > 0): ?>
        <span id="notifBadge"
              style="position:absolute;top:4px;right:4px;width:16px;height:16px;background:#ba1a1a;border-radius:50%;font-size:9px;font-weight:800;color:white;display:flex;align-items:center;justify-content:center;border:2px solid white;">
            <?= min($nb_non_lues, 9) ?><?= $nb_non_lues > 9 ? '+' : '' ?>
        </span>
        <?php else: ?>
        <span id="notifBadge" style="display:none;position:absolute;top:4px;right:4px;width:16px;height:16px;background:#ba1a1a;border-radius:50%;font-size:9px;font-weight:800;color:white;display:none;align-items:center;justify-content:center;border:2px solid white;"></span>
        <?php endif; ?>
    </button>

    <!-- Dropdown notifications -->
    <div id="notifDropdown"
         style="display:none;position:absolute;right:0;top:48px;width:360px;background:white;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,.15);border:1px solid #e5e7eb;z-index:999;overflow:hidden;">

        <!-- Header dropdown -->
        <div style="padding:14px 18px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between;">
            <div>
                <p style="margin:0;font-family:'Manrope',sans-serif;font-size:14px;font-weight:800;color:#111827;">Notifications</p>
                <p style="margin:0;font-size:11px;color:#9ca3af;" id="notifCount">
                    <?= $nb_non_lues ?> non lue<?= $nb_non_lues > 1 ? 's' : '' ?>
                </p>
            </div>
            <?php if ($nb_non_lues > 0): ?>
            <button onclick="marquerToutesLues()"
                    style="font-size:11px;color:#004d99;font-weight:600;border:none;background:none;cursor:pointer;padding:4px 8px;border-radius:6px;"
                    onmouseenter="this.style.background='#eff6ff'"
                    onmouseleave="this.style.background='none'">
                Tout marquer lu
            </button>
            <?php endif; ?>
        </div>

        <!-- Liste des notifications -->
        <div id="notifListe" style="max-height:320px;overflow-y:auto;">
            <?php if (empty($notifs)): ?>
            <div style="padding:32px;text-align:center;">
                <p style="font-size:32px;margin:0 0 8px;">🔔</p>
                <p style="font-size:13px;color:#9ca3af;margin:0;">Aucune notification pour l'instant.</p>
            </div>
            <?php else: ?>
                <?php foreach ($notifs as $n): ?>
                <?php
                    $icone = match($n['type']) {
                        'nouveau_rdv' => '📅',
                        'confirme'    => '✅',
                        'annule'      => '❌',
                        'modifie'     => '📝',
                        default       => '🔔',
                    };
                    $bg  = $n['is_read'] ? 'white' : '#eff6ff';
                    $fw  = $n['is_read'] ? '500' : '700';
                    $dt  = date('d/m · H:i', strtotime($n['created_at']));
                ?>
                <div id="notif-<?= $n['id'] ?>"
                     style="padding:12px 18px;border-bottom:1px solid #f9fafb;background:<?= $bg ?>;display:flex;align-items:flex-start;gap:10px;cursor:pointer;transition:background .1s;"
                     onmouseenter="this.style.background='#f0f9ff'"
                     onmouseleave="this.style.background='<?= $bg ?>'"
                     onclick="marquerLue(<?= $n['id'] ?>, this)">
                    <div style="font-size:20px;flex-shrink:0;margin-top:2px;"><?= $icone ?></div>
                    <div style="flex:1;min-width:0;">
                        <p style="margin:0 0 3px;font-size:13px;font-weight:<?= $fw ?>;color:#111827;line-height:1.4;">
                            <?= htmlspecialchars($n['message']) ?>
                        </p>
                        <p style="margin:0;font-size:10px;color:#9ca3af;"><?= $dt ?></p>
                    </div>
                    <?php if (!$n['is_read']): ?>
                    <div style="width:8px;height:8px;background:#004d99;border-radius:50%;flex-shrink:0;margin-top:6px;" id="dot-<?= $n['id'] ?>"></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
// ── Toggle dropdown ────────────────────────────────────────
function toggleNotifs() {
    const d = document.getElementById('notifDropdown');
    d.style.display = d.style.display === 'none' ? 'block' : 'none';
}

// Fermer en cliquant dehors
document.addEventListener('click', function(e) {
    const w = document.getElementById('notifWrapper');
    if (w && !w.contains(e.target)) {
        document.getElementById('notifDropdown').style.display = 'none';
    }
});

// ── Marquer une notif comme lue (AJAX) ────────────────────
function marquerLue(id, el) {
    fetch('<?= $base_notif ?>/rdv/notification-lue', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'notif_id=' + id
    }).then(() => {
        el.style.background = 'white';
        const dot = document.getElementById('dot-' + id);
        if (dot) dot.remove();
        mettreAJourCompteur(-1);
    });
}

// ── Marquer toutes lues (AJAX) ────────────────────────────
function marquerToutesLues() {
    fetch('<?= $base_notif ?>/rdv/notifications-toutes-lues', {
        method: 'POST'
    }).then(() => {
        document.querySelectorAll('[id^="dot-"]').forEach(d => d.remove());
        document.querySelectorAll('#notifListe > div').forEach(el => {
            el.style.background = 'white';
            const p = el.querySelector('p');
            if (p) p.style.fontWeight = '500';
        });
        mettreAJourCompteur(0, true);
    });
}

// ── Mettre à jour le badge ────────────────────────────────
let _nbNonLues = <?= $nb_non_lues ?>;
function mettreAJourCompteur(delta, reset = false) {
    if (reset) _nbNonLues = 0;
    else _nbNonLues = Math.max(0, _nbNonLues + delta);

    const badge = document.getElementById('notifBadge');
    const count = document.getElementById('notifCount');

    if (_nbNonLues > 0) {
        badge.style.display = 'flex';
        badge.textContent = _nbNonLues > 9 ? '9+' : _nbNonLues;
    } else {
        badge.style.display = 'none';
    }
    if (count) {
        count.textContent = _nbNonLues + ' non lue' + (_nbNonLues > 1 ? 's' : '');
    }
}

// ── Polling toutes les 30s pour nouvelles notifs ──────────
setInterval(() => {
    fetch('<?= $base_notif ?>/rdv/notifications-count')
        .then(r => r.json())
        .then(data => {
            if (data.count !== _nbNonLues) {
                // Recharger la page si nouvelle notif
                if (data.count > _nbNonLues) location.reload();
                else mettreAJourCompteur(0, true);
            }
        }).catch(() => {});
}, 30000);
</script>