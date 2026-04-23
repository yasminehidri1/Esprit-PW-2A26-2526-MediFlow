<?php
/**
 * Admin - Users List View
 */
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Gestion des Utilisateurs - MediFlow Admin</title>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="/Mediflow/assets/css/style.css">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "background": "#f7f9fb",
                        "surface-container-high": "#e6e8ea",
                        "on-error": "#ffffff",
                        "surface-dim": "#d8dadc",
                        "surface-container-lowest": "#ffffff",
                        "error": "#ba1a1a",
                        "on-tertiary": "#ffffff",
                        "primary-fixed": "#d6e3ff",
                        "on-surface-variant": "#424752",
                        "inverse-surface": "#2d3133",
                        "surface-container-highest": "#e0e3e5",
                        "on-primary-fixed-variant": "#00468c",
                        "on-error-container": "#93000a",
                        "on-primary-container": "#dae5ff",
                        "on-secondary-container": "#475c80",
                        "outline": "#727783",
                        "primary": "#004d99",
                        "surface-bright": "#f7f9fb",
                        "on-secondary-fixed": "#021b3c",
                        "surface-variant": "#e0e3e5",
                        "primary-container": "#1565c0",
                        "on-primary-fixed": "#001b3d",
                        "secondary": "#4a5f83",
                        "on-tertiary-fixed-variant": "#005049",
                        "error-container": "#ffdad6",
                        "surface-tint": "#005db7",
                        "surface-container": "#eceef0",
                        "tertiary-container": "#00736a",
                        "primary-fixed-dim": "#a9c7ff",
                        "outline-variant": "#c2c6d4",
                        "inverse-on-surface": "#eff1f3",
                        "on-tertiary-container": "#87f8ea",
                        "surface": "#f7f9fb",
                        "on-primary": "#ffffff",
                        "on-surface": "#191c1e",
                        "tertiary": "#005851",
                        "inverse-primary": "#a9c7ff",
                        "on-secondary": "#ffffff",
                        "tertiary-fixed": "#84f5e8",
                        "tertiary-fixed-dim": "#66d9cc",
                        "secondary-container": "#c0d5ff",
                        "on-background": "#191c1e",
                        "secondary-fixed": "#d6e3ff",
                        "on-secondary-fixed-variant": "#32476a",
                        "surface-container-low": "#f2f4f6",
                        "on-tertiary-fixed": "#00201d",
                        "secondary-fixed-dim": "#b2c7f1"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "fontFamily": {
                        "headline": ["Manrope"],
                        "body": ["Inter"],
                        "label": ["Inter"]
                    }
                },
            },
        }
    </script>
    <style>
        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        /* Header animation */
        header {
            animation: fadeInDown 0.6s ease-out;
        }

        /* Content animation */
        main {
            animation: fadeInUp 0.6s ease-out 0.2s both;
        }

        /* Message boxes animation */
        [style*="background: #ecfdf5"],
        [style*="background: #fef2f2"] {
            animation: slideInLeft 0.5s ease-out;
        }

        /* Filter form animation */
        div[style*="background: white; border: 1px solid rgba(0, 77, 153, 0.1)"] {
            animation: fadeInUp 0.6s ease-out 0.3s both;
        }

        /* Table animation */
        table {
            animation: fadeInUp 0.7s ease-out 0.4s both;
        }

        /* Table row hover effect */
        tbody tr {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        tbody tr:hover {
            background-color: #f0f7ff !important;
            transform: scale(1.01);
            box-shadow: 0 4px 12px rgba(0, 77, 153, 0.1);
        }

        tbody tr::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, #004d99, #1565c0);
            transform: scaleY(0);
            transform-origin: center;
            transition: transform 0.3s ease;
        }

        tbody tr:hover::before {
            transform: scaleY(1);
        }

        /* Action buttons animation */
        a[style*="color: #004d99"],
        a[style*="color: #ba1a1a"] {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        a[style*="color: #004d99"]:hover {
            transform: scale(1.15) rotate(-5deg);
            filter: drop-shadow(0 4px 12px rgba(0, 77, 153, 0.3));
        }

        a[style*="color: #ba1a1a"]:hover {
            transform: scale(1.15) rotate(5deg);
            filter: drop-shadow(0 4px 12px rgba(186, 26, 26, 0.3));
        }

        /* Input focus effect */
        input[type="text"],
        select {
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        select:focus {
            box-shadow: 0 0 0 3px rgba(0, 77, 153, 0.15) !important;
            transform: translateY(-2px);
        }

        /* Button hover effect */
        button[type="submit"],
        a[href*="action=create"] {
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            overflow: hidden;
        }

        button[type="submit"]:hover,
        a[href*="action=create"]:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(0, 77, 153, 0.2);
            filter: brightness(1.1);
        }

        button[type="submit"]:active,
        a[href*="action=create"]:active {
            transform: translateY(-1px);
        }

        /* Reset button hover */
        a[href="/Mediflow/admin"]:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Role badge animation */
        span[style*="background: linear-gradient(135deg, #d6e3ff"] {
            transition: all 0.3s ease;
        }

        tbody tr:hover span[style*="background: linear-gradient(135deg, #d6e3ff"] {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 77, 153, 0.3);
        }

        /* Table row stagger animation */
        tbody tr {
            animation: fadeInUp 0.5s ease-out backwards;
        }

        tbody tr:nth-child(1) { animation-delay: 0.45s; }
        tbody tr:nth-child(2) { animation-delay: 0.50s; }
        tbody tr:nth-child(3) { animation-delay: 0.55s; }
        tbody tr:nth-child(4) { animation-delay: 0.60s; }
        tbody tr:nth-child(5) { animation-delay: 0.65s; }
        tbody tr:nth-child(n+6) { animation-delay: 0.70s; }

        /* Smooth page load */
        body {
            animation: fadeInUp 0.4s ease-out;
        }
    </style>
<body class="bg-surface text-on-surface overflow-hidden">

<!-- SideNavBar -->
<aside class="h-screen w-64 fixed left-0 top-0 bg-slate-50 dark:bg-slate-900 flex flex-col py-8 space-y-6 z-50 border-r border-outline">
    <div class="px-8">
        <h1 class="text-2xl font-black tracking-tight text-blue-900 dark:text-blue-100">MediFlow</h1>
        <p class="text-xs font-medium text-slate-500 uppercase tracking-widest mt-1">Admin Panel</p>
    </div>
    
    <nav class="flex-1 flex flex-col space-y-2 px-4">
        <a href="/Mediflow/admin/users" class="flex items-center space-x-3 text-slate-500 dark:text-slate-400 hover:text-primary pl-4 py-3 group transition">
            <span class="material-symbols-outlined">people</span>
            <span class="font-medium">Utilisateurs</span>
        </a>
        <a href="/Mediflow/dashboard" class="flex items-center space-x-3 text-slate-500 dark:text-slate-400 hover:text-primary pl-4 py-3 group transition">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="font-medium">Dashboard</span>
        </a>
    </nav>

    <div class="px-8 border-t border-outline pt-4">
        <a href="/Mediflow/logout" class="flex items-center space-x-3 text-slate-500 dark:text-slate-400 hover:text-error">
            <span class="material-symbols-outlined">logout</span>
            <span class="font-medium text-sm">Déconnexion</span>
        </a>
    </div>
</aside>

<!-- Main Content -->
<div class="ml-64 h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-white dark:bg-slate-800 border-b border-outline shadow-sm">
        <div class="flex items-center justify-between px-8 py-6">
            <div>
                <h2 class="text-4xl font-black bg-gradient-to-r from-primary via-primary-container to-primary bg-clip-text text-transparent">Gestion des Utilisateurs</h2>
                <p class="text-on-surface-variant text-sm mt-2">Administrez tous les utilisateurs du système</p>
            </div>
            <a href="/Mediflow/admin?action=create" style="display: flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #004d99 0%, #1565c0 100%); color: white; padding: 12px 24px; border-radius: 12px; font-weight: 700; text-decoration: none; box-shadow: 0 4px 15px rgba(0, 77, 153, 0.3); border: none; cursor: pointer;">
                <span class="material-symbols-outlined">add_circle</span>
                <span>Ajouter Utilisateur</span>
            </a>
        </div>
    </header>

    <!-- Content -->
    <main class="flex-1 overflow-auto">
        <div class="p-8">
            <!-- Messages -->
            <?php if (!empty($data['message'])): ?>
                <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 16px; border-radius: 12px; margin-bottom: 20px; display: flex; gap: 12px;">
                    <span class="material-symbols-outlined" style="flex-shrink: 0;">check_circle</span>
                    <p><?php echo $data['message']; ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($data['error'])): ?>
                <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 16px; border-radius: 12px; margin-bottom: 20px; display: flex; gap: 12px;">
                    <span class="material-symbols-outlined" style="flex-shrink: 0;">error</span>
                    <p><?php echo $data['error']; ?></p>
                </div>
            <?php endif; ?>

            <!-- Search & Filter Form -->
            <div style="background: linear-gradient(135deg, #ffffff 0%, #f8fafb 100%); border: 1px solid rgba(0, 77, 153, 0.08); border-radius: 16px; padding: 24px; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                <form method="GET" action="/Mediflow/admin" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
                    <!-- Search -->
                    <div style="flex: 1; min-width: 220px;">
                        <label style="display: block; font-size: 12px; font-weight: 700; color: #191c1e; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Rechercher</label>
                        <input type="text" name="search" placeholder="Nom, email, téléphone..." value="<?php echo htmlspecialchars($data['search'] ?? ''); ?>" 
                               style="width: 100%; padding: 12px 16px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 14px; background: white; box-sizing: border-box; transition: all 0.3s ease;">
                    </div>
                    
                    <!-- Role Filter -->
                    <div style="min-width: 160px;">
                        <label style="display: block; font-size: 12px; font-weight: 700; color: #191c1e; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Filtrer par rôle</label>
                        <select name="role" style="width: 100%; padding: 12px 16px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 14px; background: white; box-sizing: border-box; cursor: pointer; transition: all 0.3s ease;">
                            <option value="">-- Tous les rôles --</option>
                            <?php foreach ($data['roles'] as $role): ?>
                                <option value="<?php echo $role['id_role']; ?>" <?php echo $data['roleFilter'] == $role['id_role'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($role['libelle']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Buttons -->
                    <button type="submit" style="padding: 12px 24px; background: linear-gradient(135deg, #004d99 0%, #1565c0 100%); color: white; border: none; border-radius: 10px; cursor: pointer; font-weight: 700; font-size: 14px; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0, 77, 153, 0.2);">Chercher</button>
                    <a href="/Mediflow/admin" style="padding: 12px 24px; background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; border-radius: 10px; cursor: pointer; font-weight: 700; font-size: 14px; text-decoration: none; display: inline-block; transition: all 0.3s ease;">Réinitialiser</a>
                </form>
            </div>

            <!-- Users Table -->
            <div style="background: white; border: 1px solid rgba(0, 77, 153, 0.08); border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr style="background: linear-gradient(90deg, rgba(0, 77, 153, 0.12) 0%, rgba(21, 101, 192, 0.08) 50%, rgba(0, 118, 81, 0.06) 100%); border-bottom: 2px solid rgba(0, 77, 153, 0.12);">
                        <th style="padding: 18px 24px; text-align: left; font-size: 12px; font-weight: 800; color: #191c1e; text-transform: uppercase; letter-spacing: 0.6px;">Matricule</th>
                        <th style="padding: 18px 24px; text-align: left; font-size: 12px; font-weight: 800; color: #191c1e; text-transform: uppercase; letter-spacing: 0.6px;">Nom</th>
                        <th style="padding: 18px 24px; text-align: left; font-size: 12px; font-weight: 800; color: #191c1e; text-transform: uppercase; letter-spacing: 0.6px;">Email</th>
                        <th style="padding: 18px 24px; text-align: left; font-size: 12px; font-weight: 800; color: #191c1e; text-transform: uppercase; letter-spacing: 0.6px;">Téléphone</th>
                        <th style="padding: 18px 24px; text-align: left; font-size: 12px; font-weight: 800; color: #191c1e; text-transform: uppercase; letter-spacing: 0.6px;">Rôle</th>
                        <th style="padding: 18px 24px; text-align: center; font-size: 12px; font-weight: 800; color: #191c1e; text-transform: uppercase; letter-spacing: 0.6px;">Actions</th>
                    </tr>
                    <?php if (!empty($data['users'])): ?>
                        <?php foreach ($data['users'] as $user): ?>
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 16px 24px; font-size: 14px; color: #004d99; font-weight: 800;"><?php echo htmlspecialchars($user['matricule'] ?? 'N/A'); ?></td>
                                <td style="padding: 16px 24px; font-size: 14px; color: #191c1e; font-weight: 600;"><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></td>
                                <td style="padding: 16px 24px; font-size: 14px; color: #424752;"><?php echo htmlspecialchars($user['mail']); ?></td>
                                <td style="padding: 16px 24px; font-size: 14px; color: #191c1e;"><?php echo htmlspecialchars($user['tel'] ?? '-'); ?></td>
                                <td style="padding: 16px 24px; font-size: 14px;"><span style="background: linear-gradient(135deg, #d6e3ff 0%, #c0d5ff 100%); color: #004d99; font-weight: 800; padding: 8px 14px; border-radius: 20px; font-size: 12px; display: inline-block; border: 1px solid rgba(0, 77, 153, 0.2); box-shadow: 0 2px 8px rgba(0, 77, 153, 0.1); text-transform: uppercase; letter-spacing: 0.3px;"><?php echo htmlspecialchars($user['role_name'] ?? 'N/A'); ?></span></td>
                                <td style="padding: 16px 24px; text-align: center;">
                                    <a href="/Mediflow/admin?action=edit&id=<?php echo $user['id_PK']; ?>" title="Modifier" style="color: #004d99; text-decoration: none; margin-right: 12px; display: inline-block; padding: 8px; border-radius: 8px; transition: all 0.3s ease; background: rgba(0, 77, 153, 0.05);">
                                        <span class="material-symbols-outlined" style="font-size: 20px;">edit</span>
                                    </a>
                                    <a href="/Mediflow/admin?action=delete&id=<?php echo $user['id_PK']; ?>" title="Supprimer" style="color: #ba1a1a; text-decoration: none; display: inline-block; padding: 8px; border-radius: 8px; transition: all 0.3s ease; background: rgba(186, 26, 26, 0.05);" onclick="return confirm('Êtes-vous sûr?');">
                                        <span class="material-symbols-outlined" style="font-size: 20px;">delete</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="padding: 60px 40px; text-align: center;">
                                <span class="material-symbols-outlined" style="display: block; font-size: 56px; margin-bottom: 16px; opacity: 0.3; color: #004d99;">group_off</span>
                                <p style="font-weight: 700; font-size: 18px; color: #191c1e; margin-bottom: 8px;">Aucun utilisateur trouvé</p>
                                <p style="font-size: 14px; color: #999;">Cliquez sur "Ajouter Utilisateur" pour en créer un nouveau</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </main>
</div>

</body>
</html>
<?php
/**
 * Admin Users Management - Full Dashboard View
 */
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Gestion des Utilisateurs - MediFlow Admin</title>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="/Mediflow/assets/css/style.css">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "background": "#f7f9fb",
                        "surface-container-high": "#e6e8ea",
                        "on-error": "#ffffff",
                        "surface-dim": "#d8dadc",
                        "surface-container-lowest": "#ffffff",
                        "error": "#ba1a1a",
                        "on-tertiary": "#ffffff",
                        "primary-fixed": "#d6e3ff",
                        "on-surface-variant": "#424752",
                        "inverse-surface": "#2d3133",
                        "surface-container-highest": "#e0e3e5",
                        "on-primary-fixed-variant": "#00468c",
                        "on-error-container": "#93000a",
                        "on-primary-container": "#dae5ff",
                        "on-secondary-container": "#475c80",
                        "outline": "#727783",
                        "primary": "#004d99",
                        "surface-bright": "#f7f9fb",
                        "on-secondary-fixed": "#021b3c",
                        "surface-variant": "#e0e3e5",
                        "primary-container": "#1565c0",
                        "on-primary-fixed": "#001b3d",
                        "secondary": "#4a5f83",
                        "on-tertiary-fixed-variant": "#005049",
                        "error-container": "#ffdad6",
                        "surface-tint": "#005db7",
                        "surface-container": "#eceef0",
                        "tertiary-container": "#00736a",
                        "primary-fixed-dim": "#a9c7ff",
                        "outline-variant": "#c2c6d4",
                        "inverse-on-surface": "#eff1f3",
                        "on-tertiary-container": "#87f8ea",
                        "surface": "#f7f9fb",
                        "on-primary": "#ffffff",
                        "on-surface": "#191c1e",
                        "tertiary": "#005851",
                        "inverse-primary": "#a9c7ff",
                        "on-secondary": "#ffffff",
                        "tertiary-fixed": "#84f5e8",
                        "tertiary-fixed-dim": "#66d9cc",
                        "secondary-container": "#c0d5ff",
                        "on-background": "#191c1e",
                        "secondary-fixed": "#d6e3ff",
                        "on-secondary-fixed-variant": "#32476a",
                        "surface-container-low": "#f2f4f6",
                        "on-tertiary-fixed": "#00201d",
                        "secondary-fixed-dim": "#b2c7f1"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "fontFamily": {
                        "headline": ["Manrope"],
                        "body": ["Inter"],
                        "label": ["Inter"]
                    }
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3 { font-family: 'Manrope', sans-serif; }
        
        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
        
        @keyframes shimmer {
            0% {
                background-position: -1000px 0;
            }
            100% {
                background-position: 1000px 0;
            }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        .animate-slide-in-left {
            animation: slideInLeft 0.5s ease-out;
        }
        
        .animate-slide-in-right {
            animation: slideInRight 0.5s ease-out;
        }
        
        .animate-scale-in {
            animation: scaleIn 0.3s ease-out;
        }
        
        .animate-pulse-slow {
            animation: pulse 3s ease-in-out infinite;
        }
        
        /* Smooth transitions */
        .transition-all {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Button hover effects */
        .btn-icon-hover {
            transition: all 0.2s ease;
        }
        
        .btn-icon-hover:hover {
            transform: scale(1.15);
        }
        
        /* Table row animations */
        tbody tr {
            animation: fadeIn 0.4s ease-out backwards;
        }
        
        tbody tr:nth-child(1) { animation-delay: 0.05s; }
        tbody tr:nth-child(2) { animation-delay: 0.1s; }
        tbody tr:nth-child(3) { animation-delay: 0.15s; }
        tbody tr:nth-child(4) { animation-delay: 0.2s; }
        tbody tr:nth-child(5) { animation-delay: 0.25s; }
        tbody tr:nth-child(n+6) { animation-delay: 0.3s; }
        
        /* Smooth row hover effect */
        tbody tr {
            position: relative;
            overflow: hidden;
        }
        
        tbody tr::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.5s;
        }
        
        tbody tr:hover::before {
            left: 100%;
        }
        
        /* Icon animations */
        .icon-spin {
            transition: transform 0.3s ease;
        }
        
        .icon-spin:hover {
            transform: rotate(180deg) scale(1.2);
        }
        
        /* Action buttons with glow effect */
        .action-btn {
            position: relative;
            overflow: hidden;
        }
        
        .action-btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255,255,255,0.5);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .action-btn:hover::after {
            width: 300px;
            height: 300px;
        }
        
        /* Logout Button Styles */
        .logout-btn {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #ba1a1a 0%, #a41817 100%);
            padding: 12px 20px;
            border-radius: 12px;
            border: none;
            color: white;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            justify-content: flex-start;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 4px 15px rgba(186, 26, 26, 0.2);
        }
        
        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(186, 26, 26, 0.35);
            background: linear-gradient(135deg, #d32f2f 0%, #ba1a1a 100%);
        }
        
        .logout-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 10px rgba(186, 26, 26, 0.2);
        }
        
        .logout-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.3) 1px, transparent 1px);
            background-size: 20px 20px;
            animation: shimmer-logout 0.6s ease-in-out;
        }
        
        .logout-btn:hover::before {
            animation: shimmer-logout 0.6s ease-in-out;
        }
        
        @keyframes shimmer-logout {
            0% {
                left: -100%;
            }
            100% {
                left: 100%;
            }
        }
        
        .logout-icon {
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            font-size: 20px;
        }
        
        .logout-btn:hover .logout-icon {
            transform: scale(1.2) rotate(-15deg);
        }
        
        /* Sidebar animations */
        aside {
            animation: slideInLeft 0.6s ease-out;
        }
        
        /* Header enhancement animations */
        header {
            animation: slideInDown 0.6s ease-out;
        }
        
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Table container animations */
        .table-container {
            animation: fadeIn 0.8s ease-out 0.2s backwards;
        }
        
        /* Hover effects for nav items */
        nav a {
            position: relative;
            overflow: hidden;
        }
        
        nav a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: -100%;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #004d99, #1565c0);
            transition: left 0.3s ease;
        }
        
        nav a:hover::after {
            left: 0;
        }
        
        /* Message animations */
        .message-box {
            animation: slideInRight 0.5s ease-out;
        }
        
        /* Button ripple effect improvements */
        .ripple-btn {
            position: relative;
            overflow: hidden;
        }
        
        .ripple-btn span {
            position: relative;
            z-index: 2;
        }
    </style>
</head>
<body class="bg-surface text-on-surface overflow-hidden">

<!-- SideNavBar -->
<aside class="h-screen w-64 fixed left-0 top-0 bg-gradient-to-b from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800 flex flex-col py-8 space-y-6 z-50 border-r border-outline shadow-xl">
    <div class="px-8">
        <h1 class="text-2xl font-black tracking-tight bg-gradient-to-r from-primary to-primary-container bg-clip-text text-transparent">MediFlow</h1>
        <p class="text-xs font-medium text-slate-500 uppercase tracking-widest mt-1">Admin Panel</p>
    </div>
    
    <nav class="flex-1 flex flex-col space-y-2 px-4">
        <a href="/Mediflow/admin" class="flex items-center space-x-3 text-slate-700 dark:text-slate-300 hover:text-primary bg-gradient-to-r from-primary-fixed to-primary-fixed/50 bg-opacity-40 pl-4 py-3 rounded-xl transition-all duration-300 transform hover:translate-x-1 shadow-sm hover:shadow-md">
            <span class="material-symbols-outlined">people</span>
            <span class="font-medium">Utilisateurs</span>
        </a>
        <a href="/Mediflow/dashboard" class="flex items-center space-x-3 text-slate-500 dark:text-slate-400 hover:text-primary pl-4 py-3 group transition-all duration-300 transform hover:translate-x-1 rounded-xl">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="font-medium">Dashboard</span>
        </a>
    </nav>

    <div class="px-4 border-t border-outline pt-6">
        <a href="/Mediflow/logout" class="logout-btn">
            <span class="material-symbols-outlined logout-icon">logout</span>
            <span>Déconnexion</span>
        </a>
    </div>
</aside>

<!-- Main Content -->
<div class="ml-64 h-screen flex flex-col bg-gradient-to-br from-surface via-surface-container-low to-surface-dim">
    <!-- Header -->
    <header class="bg-gradient-to-r from-primary/8 via-white to-primary-fixed/10 dark:from-primary/15 dark:via-slate-800 dark:to-slate-700 border-b border-outline/30 shadow-lg backdrop-blur-sm">
        <div class="flex items-center justify-between px-8 py-8 animate-fade-in">
            <div>
                <h2 class="text-4xl font-black bg-gradient-to-r from-primary via-primary-container to-primary-container bg-clip-text text-transparent mb-2">Gestion des Utilisateurs</h2>
                <p class="text-on-surface-variant text-sm font-medium">Administrez tous les utilisateurs du système • Total: <span class="font-bold text-primary"><?php echo count($data['users'] ?? []); ?></span> utilisateurs</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="/Mediflow/admin?action=create" class="bg-gradient-to-r from-primary to-primary-container text-on-primary px-8 py-3 rounded-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02] active:scale-95 font-semibold text-sm flex items-center gap-2 action-btn shadow-lg hover:from-primary-container hover:to-primary">
                    <span class="material-symbols-outlined text-lg">add_circle</span> Ajouter Utilisateur
                </a>
            </div>
        </div>
    </header>

    <!-- Content -->
    <main class="flex-1 overflow-auto">
        <div class="p-8">
            <!-- Messages -->
            <?php if (!empty($data['message'])): ?>
                <div class="message-box bg-gradient-to-r from-emerald-50 via-green-50 to-emerald-50 border-l-4 border-emerald-500 border-r border-t border-b border-emerald-200 text-emerald-800 px-6 py-4 rounded-xl mb-6 flex items-center gap-4 shadow-lg backdrop-blur-sm">
                    <span class="material-symbols-outlined text-emerald-600 text-3xl flex-shrink-0">task_alt</span>
                    <div class="flex-1">
                        <p class="font-bold text-lg">Opération réussie</p>
                        <p class="text-sm text-emerald-700 font-medium"><?php echo htmlspecialchars($data['message']); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($data['error'])): ?>
                <div class="message-box bg-gradient-to-r from-red-50 via-rose-50 to-red-50 border-l-4 border-red-500 border-r border-t border-b border-red-200 text-red-800 px-6 py-4 rounded-xl mb-6 flex items-center gap-4 shadow-lg backdrop-blur-sm">
                    <span class="material-symbols-outlined text-red-600 text-3xl flex-shrink-0">error_outline</span>
                    <div class="flex-1">
                        <p class="font-bold text-lg">Erreur</p>
                        <p class="text-sm text-red-700 font-medium"><?php echo htmlspecialchars($data['error']); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Search and Filter Controls -->
            <div class="mb-6 bg-white rounded-xl shadow-lg p-6 border border-outline/30" style="background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(242,244,246,0.95) 100%);">
                <form method="GET" action="/Mediflow/admin" class="flex flex-wrap gap-4 items-end">
                    <!-- Search Input -->
                    <div class="flex-1 min-w-[250px]">
                        <label class="block text-sm font-semibold text-on-surface mb-2 flex items-center gap-2">
                            <span class="material-symbols-outlined text-base text-primary">search</span>
                            Rechercher
                        </label>
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Nom, email, téléphone..." 
                            value="<?php echo htmlspecialchars($data['search'] ?? ''); ?>"
                            class="w-full px-4 py-3 border-2 border-outline rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-300 text-sm font-medium"
                            style="background: white; box-shadow: 0 2px 8px rgba(0, 77, 153, 0.05);"
                        />
                    </div>

                    <!-- Role Filter Dropdown -->
                    <div class="min-w-[200px]">
                        <label class="block text-sm font-semibold text-on-surface mb-2 flex items-center gap-2">
                            <span class="material-symbols-outlined text-base text-primary">filter_alt</span>
                            Filtrer par rôle
                        </label>
                        <select 
                            name="role" 
                            class="w-full px-4 py-3 border-2 border-outline rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-300 text-sm font-medium appearance-none cursor-pointer"
                            style="background: white url('data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 20 20%22><path fill=%22%23004d99%22 d=%22M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z%22/></svg>') no-repeat right 12px center; background-size: 20px; padding-right: 40px; box-shadow: 0 2px 8px rgba(0, 77, 153, 0.05);"
                        >
                            <option value="">-- Tous les rôles --</option>
                            <?php foreach ($data['roles'] as $role): ?>
                                <option value="<?php echo $role['id_role']; ?>" <?php echo isset($data['roleFilter']) && $data['roleFilter'] == $role['id_role'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($role['libelle']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3">
                        <button 
                            type="submit"
                            class="bg-gradient-to-r from-primary to-primary-container text-on-primary px-6 py-3 rounded-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02] active:scale-95 font-semibold text-sm flex items-center gap-2 action-btn shadow-lg"
                            style="border: none; cursor: pointer;"
                        >
                            <span class="material-symbols-outlined text-base">search</span>
                            Chercher
                        </button>
                        <?php if (!empty($data['search']) || !empty($data['roleFilter'])): ?>
                            <a 
                                href="/Mediflow/admin"
                                class="bg-gradient-to-r from-surface-container to-surface-container-high text-on-surface px-6 py-3 rounded-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02] active:scale-95 font-semibold text-sm flex items-center gap-2 action-btn shadow-lg"
                                style="border: 1px solid var(--outline); text-decoration: none; display: inline-flex;"
                            >
                                <span class="material-symbols-outlined text-base">clear</span>
                                Réinitialiser
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Search Results Info -->
            <?php if (!empty($data['search']) || !empty($data['roleFilter'])): ?>
                <div class="mb-4 px-4 py-3 bg-primary/10 border-l-4 border-primary rounded-lg flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary text-lg">info</span>
                    <p class="text-sm text-on-surface font-medium">
                        Résultats trouvés: <strong><?php echo count($data['users']); ?></strong>
                        <?php if (!empty($data['search'])): ?>
                            pour "<strong><?php echo htmlspecialchars($data['search']); ?></strong>"
                        <?php endif; ?>
                        <?php if (!empty($data['roleFilter'])): ?>
                            dans le rôle "<strong><?php 
                                $selectedRole = array_values(array_filter($data['roles'], fn($r) => $r['id_role'] == $data['roleFilter']));
                                echo $selectedRole[0]['libelle'] ?? 'Inconnu';
                            ?></strong>"
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>
            <div class="table-container" style="background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(242,244,246,0.95) 100%); border-radius: 18px; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08); overflow: hidden; border: 1px solid rgba(0, 77, 153, 0.08); backdrop-filter: blur(10px);">
                <table style="width: 100%;">
                    <tr style="background: linear-gradient(90deg, rgba(0, 77, 153, 0.12), rgba(21, 101, 192, 0.08), rgba(0, 118, 81, 0.06)); border-bottom: 2px solid rgba(0, 77, 153, 0.15);">
                        <th style="padding: 20px 24px; text-align: left; font-size: 14px; font-weight: 700; color: #191c1e; text-transform: uppercase; letter-spacing: 0.5px;">Nom Complet</th>
                        <th style="padding: 20px 24px; text-align: left; font-size: 14px; font-weight: 700; color: #191c1e; text-transform: uppercase; letter-spacing: 0.5px;">Email</th>
                        <th style="padding: 20px 24px; text-align: left; font-size: 14px; font-weight: 700; color: #191c1e; text-transform: uppercase; letter-spacing: 0.5px;">Téléphone</th>
                        <th style="padding: 20px 24px; text-align: left; font-size: 14px; font-weight: 700; color: #191c1e; text-transform: uppercase; letter-spacing: 0.5px;">Rôle</th>
                        <th style="padding: 20px 24px; text-align: center; font-size: 14px; font-weight: 700; color: #191c1e; text-transform: uppercase; letter-spacing: 0.5px;">Actions</th>
                    </tr>
                    <?php if (!empty($data['users'])): ?>
                        <?php foreach ($data['users'] as $user): ?>
                            <tr style="border-bottom: 1px solid rgba(0, 77, 153, 0.06); transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='rgba(0, 77, 153, 0.06)'; this.style.transform='scale(1.001)'; this.style.boxShadow='inset 0 2px 8px rgba(0, 77, 153, 0.05)'" onmouseout="this.style.backgroundColor='transparent'; this.style.transform='scale(1)'; this.style.boxShadow='none'">
                                <td style="padding: 18px 24px; font-size: 14px; color: #191c1e; font-weight: 700; vertical-align: middle;"><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></td>
                                <td style="padding: 18px 24px; font-size: 14px; color: #424752; vertical-align: middle; font-weight: 500;"><?php echo htmlspecialchars($user['mail']); ?></td>
                                <td style="padding: 18px 24px; font-size: 14px; color: #191c1e; vertical-align: middle; font-weight: 500;"><?php echo htmlspecialchars($user['tel'] ?? '-'); ?></td>
                                <td style="padding: 18px 24px; font-size: 14px; vertical-align: middle;"><span style="background: linear-gradient(135deg, #d6e3ff 0%, #c0d5ff 100%); color: #004d99; font-weight: 800; padding: 10px 16px; border-radius: 20px; font-size: 12px; display: inline-block; white-space: nowrap; border: 1.5px solid rgba(0, 77, 153, 0.25); box-shadow: 0 4px 12px rgba(0, 77, 153, 0.12); text-transform: uppercase; letter-spacing: 0.4px;"><?php echo htmlspecialchars($user['role_name'] ?? 'N/A'); ?></span></td>
                                <td style="padding: 18px 24px; font-size: 14px; vertical-align: middle; text-align: center;"><a href="/Mediflow/admin?action=edit&id=<?php echo $user['id_PK']; ?>" style="color: #004d99; background: linear-gradient(135deg, rgba(0, 77, 153, 0.08), rgba(21, 101, 192, 0.06)); width: 44px; height: 44px; display: inline-flex; align-items: center; justify-content: center; border-radius: 12px; text-decoration: none; transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); cursor: pointer; border: 1px solid rgba(0, 77, 153, 0.1); font-size: 20px; margin-right: 10px; box-shadow: 0 2px 8px rgba(0, 77, 153, 0.06);" title="Modifier" onmouseover="this.style.backgroundColor='rgba(0, 77, 153, 0.15)'; this.style.transform='scale(1.15) rotate(-5deg)'; this.style.boxShadow='0 8px 20px rgba(0, 77, 153, 0.2)'" onmouseout="this.style.backgroundColor='rgba(0, 77, 153, 0.08)'; this.style.transform='scale(1)'; this.style.boxShadow='0 2px 8px rgba(0, 77, 153, 0.06)'"><span class="material-symbols-outlined">edit</span></a><a href="/Mediflow/admin?action=delete&id=<?php echo $user['id_PK']; ?>" style="color: #ba1a1a; background: linear-gradient(135deg, rgba(186, 26, 26, 0.08), rgba(212, 47, 47, 0.06)); width: 44px; height: 44px; display: inline-flex; align-items: center; justify-content: center; border-radius: 12px; text-decoration: none; transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); cursor: pointer; border: 1px solid rgba(186, 26, 26, 0.1); font-size: 20px; box-shadow: 0 2px 8px rgba(186, 26, 26, 0.06);" title="Supprimer" onmouseover="this.style.backgroundColor='rgba(186, 26, 26, 0.15)'; this.style.transform='scale(1.15) rotate(5deg)'; this.style.boxShadow='0 8px 20px rgba(186, 26, 26, 0.2)'" onmouseout="this.style.backgroundColor='rgba(186, 26, 26, 0.08)'; this.style.transform='scale(1)'; this.style.boxShadow='0 2px 8px rgba(186, 26, 26, 0.06)'" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur?');"><span class="material-symbols-outlined">delete</span></a></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="padding: 60px 20px; text-align: center; color: #424752;"><span class="material-symbols-outlined" style="display: block; font-size: 56px; margin-bottom: 16px; opacity: 0.3; color: #004d99;">group_off</span><p style="font-weight: 700; font-size: 20px; margin-top: 8px; color: #191c1e;">Aucun utilisateur trouvé</p><p style="font-size: 14px; margin-top: 12px; color: #424752; font-weight: 500;">Cliquez sur <strong>"Ajouter Utilisateur"</strong> pour en créer un nouveau</p></td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </main>
</div>

</body>
</html>
