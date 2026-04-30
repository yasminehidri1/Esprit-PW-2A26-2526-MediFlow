<?php
/**
 * Avatar Helper — Génère des avatars colorés avec initiales
 */

function generateAvatarColor($id) {
    $colors = [
        '#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8',
        '#F7DC6F', '#BB8FCE', '#85C1E2', '#F8B88B', '#85C1E2',
        '#52B788', '#D62828', '#F77F00', '#06A77D', '#E63946'
    ];
    return $colors[$id % count($colors)];
}

function generateInitials($prenom, $nom) {
    $initials = strtoupper(substr($prenom, 0, 1) . substr($nom, 0, 1));
    return $initials;
}

function getAvatarHtml($id, $prenom, $nom, $size = '40px') {
    $color = generateAvatarColor($id);
    $initials = generateInitials($prenom, $nom);

    return <<<HTML
    <div style="
        width: $size;
        height: $size;
        background-color: $color;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: calc($size * 0.4);
        flex-shrink: 0;
    ">$initials</div>
    HTML;
}
