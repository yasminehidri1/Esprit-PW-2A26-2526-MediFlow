<?php
/**
 * Admin Dashboard View
 * 
 * Displays the admin dashboard with KPI cards, charts, and user management data
 * 
 * @package MediFlow\Views\Back
 * @version 1.0.0
 */
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>MediFlow Admin Dashboard</title>
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
</head>
<body class="bg-surface text-on-surface overflow-hidden">
<!-- SideNavBar -->
<aside class="h-screen w-64 fixed left-0 top-0 bg-gradient-to-b from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800 flex flex-col py-8 space-y-6 z-50 border-r border-outline shadow-xl">
    <div class="px-8">
        <h1 class="text-2xl font-black tracking-tight bg-gradient-to-r from-primary to-primary-container bg-clip-text text-transparent">MediFlow</h1>
        <p class="text-xs font-medium text-slate-500 uppercase tracking-widest mt-1">Clinical Sanctuary</p>
    </div>
    <nav class="flex-1 flex flex-col space-y-2 px-4">
        <a class="flex items-center space-x-3 text-slate-700 dark:text-slate-300 hover:text-primary bg-gradient-to-r from-primary-fixed to-primary-fixed/50 bg-opacity-40 pl-4 py-3 rounded-xl transition-all duration-300 transform hover:translate-x-1 shadow-sm hover:shadow-md" href="/Mediflow/dashboard">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="font-medium">Dashboard</span>
        </a>
        
        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role']): ?>
            <?php $role = $_SESSION['user']['role']; ?>
            
            <!-- Role-Specific Module Links -->
            <?php if ($role === 'Admin'): ?>
                <a class="flex items-center space-x-3 text-slate-500 dark:text-slate-400 hover:text-primary pl-4 py-3 group transition-all duration-300 transform hover:translate-x-1 rounded-xl" href="/Mediflow/admin">
                    <span class="material-symbols-outlined">people</span>
                    <span class="font-medium">User Management</span>
                </a>
            <?php elseif ($role === 'Medecin'): ?>
                <a class="flex items-center space-x-3 text-slate-500 dark:text-slate-400 hover:text-primary pl-4 py-3 group transition-all duration-300 transform hover:translate-x-1 rounded-xl" href="/Mediflow/medical">
                    <span class="material-symbols-outlined">description</span>
                    <span class="font-medium">Dossier Medical</span>
                </a>
            <?php elseif ($role === 'Rendez-vous'): ?>
                <a class="flex items-center space-x-3 text-slate-500 dark:text-slate-400 hover:text-primary pl-4 py-3 group transition-all duration-300 transform hover:translate-x-1 rounded-xl" href="/Mediflow/appointments">
                    <span class="material-symbols-outlined">calendar_today</span>
                    <span class="font-medium">Rendez-vous</span>
                </a>
            <?php elseif ($role === 'Stock medicament'): ?>
                <a class="flex items-center space-x-3 text-slate-500 dark:text-slate-400 hover:text-primary pl-4 py-3 group transition-all duration-300 transform hover:translate-x-1 rounded-xl" href="/Mediflow/stock">
                    <span class="material-symbols-outlined">inventory_2</span>
                    <span class="font-medium">Stock Medicament</span>
                </a>
            <?php elseif ($role === 'Equipment'): ?>
                <a class="flex items-center space-x-3 text-slate-500 dark:text-slate-400 hover:text-primary pl-4 py-3 group transition-all duration-300 transform hover:translate-x-1 rounded-xl" href="/Mediflow/equipment">
                    <span class="material-symbols-outlined">hardware</span>
                    <span class="font-medium">Equipment Management</span>
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </nav>
    <div class="px-4 border-t border-outline pt-6 flex flex-col space-y-3">
        <a href="/Mediflow/logout" class="logout-btn">
            <span class="material-symbols-outlined logout-icon">logout</span>
            <span>Logout</span>
        </a>
    </div>
</aside>

<!-- Main Wrapper -->
<main class="ml-64 min-h-screen bg-gradient-to-br from-surface via-surface-container-low to-surface-dim">
    <!-- TopNavBar -->
    <header class="fixed top-0 right-0 w-[calc(100%-16rem)] h-16 bg-gradient-to-r from-white/80 to-primary-fixed/10 backdrop-blur-xl flex items-center justify-between px-8 z-40 shadow-xl border-b border-outline/20">
        <div class="flex items-center space-x-8">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input class="pl-10 pr-4 py-2 bg-surface-container-low border border-outline/20 rounded-full text-sm w-64 focus:ring-2 focus:ring-primary/30 outline-none transition-all duration-300" placeholder="Search users..." type="text"/>
            </div>
            <nav class="hidden md:flex space-x-6">
                <a class="text-primary font-bold border-b-2 border-primary pb-1 transition-all duration-300" href="/Mediflow/dashboard">Dashboard</a>
                <a class="text-slate-500 hover:text-primary transition-colors duration-300 font-medium" href="/Mediflow/dashboard?tab=users">Users</a>
                <a class="text-slate-500 hover:text-primary transition-colors duration-300 font-medium" href="/Mediflow/dashboard?tab=reports">Reports</a>
                <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'Admin'): ?>
                    <a class="text-slate-500 hover:text-error transition-colors duration-300 font-bold" href="/Mediflow/admin">User Management</a>
                <?php endif; ?>
            </nav>
        </div>
        <div class="flex items-center space-x-6">
            <div class="flex items-center space-x-3">
                <button class="text-slate-500 hover:text-primary relative transition-colors duration-300 hover:scale-110">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-error rounded-full animate-pulse"></span>
                </button>
                <button class="text-slate-500 hover:text-primary transition-colors duration-300 hover:scale-110">
                    <span class="material-symbols-outlined">help_outline</span>
                </button>
            </div>
            <div class="flex items-center space-x-3 pl-6 border-l border-outline/20">
                <div class="text-right">
                    <p class="text-sm font-bold text-on-surface">
                        <?php echo isset($data['currentUser']) ? htmlspecialchars($data['currentUser']['prenom'] . ' ' . $data['currentUser']['nom']) : 'Admin'; ?>
                    </p>
                    <p class="text-xs text-slate-500">
                        <?php echo isset($data['currentUser']) ? htmlspecialchars($data['currentUser']['role_name']) : 'Administrator'; ?>
                    </p>
                </div>
                <img alt="Admin Avatar" class="w-10 h-10 rounded-full border-2 border-primary-fixed hover:border-primary transition-all duration-300 cursor-pointer hover:scale-110" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBYPJ5lof7TyPYcDQMTIqlqy9_EnbCRQNlituOGkD8EagHkVvQd1KZpwyL1vjOT1yTSO3w3sP8VpxAMSa34d9WtQjlvKYF6XEyyWxWXitkQtvDO2cCIWJTNxIeGgRopkbP9SslNkybURTmSZhpYO9QGJKH7o4JQUM4-k_SdF_j17mD59kBsVc8Ep7h55Ju4wJOjsgODP4eGcB2hSgEuiVkPd7l2tTeKD1USMvHj9u_MgR-w_ty6lekC3w1h1LEvgZzPY-hs6uxE440"/>
            </div>
        </div>
    </header>

    <!-- Content Area -->
    <div class="pt-24 pb-12 px-10 space-y-10">
        <!-- Greeting -->
        <section>
            <h2 class="text-4xl font-extrabold bg-gradient-to-r from-primary via-primary-container to-primary bg-clip-text text-transparent tracking-tight">Welcome back, <?php echo isset($data['currentUser']) ? htmlspecialchars($data['currentUser']['prenom']) : 'Admin'; ?></h2>
            <p class="text-on-surface-variant mt-2 font-medium">Votre résumé du système pour aujourd'hui, <?php echo date('d F Y'); ?>.</p>
        </section>

        <!-- KPI Cards Grid -->
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- KPI 1: Total Users -->
            <div class="hover-lift bg-gradient-to-br from-surface-container-lowest to-surface-container-low p-6 rounded-xl shadow-sm border-t-2 border-primary border-r border-b border-outline/10">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-on-surface-variant">Total Users</p>
                        <h3 class="text-3xl font-black mt-2 text-primary"><?php echo isset($data['stats']['totalUsers']) ? $data['stats']['totalUsers'] : 0; ?></h3>
                    </div>
                    <span class="bg-gradient-to-r from-primary-fixed to-primary-fixed/50 text-primary px-3 py-1 rounded-full text-xs font-bold">+12%</span>
                </div>
                <div class="mt-4 flex items-center text-primary">
                    <span class="material-symbols-outlined text-lg mr-2">person_add</span>
                    <span class="text-xs font-semibold">Active in system</span>
                </div>
            </div>

            <!-- KPI 2: Users by Admin Role -->
            <div class="hover-lift bg-gradient-to-br from-surface-container-lowest to-surface-container-low p-6 rounded-xl shadow-sm border-t-2 border-tertiary border-r border-b border-outline/10">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-on-surface-variant">Admins</p>
                        <h3 class="text-3xl font-black mt-2 text-tertiary">
                            <?php 
                                $adminCount = 0;
                                if (isset($data['stats']['usersByRole'])) {
                                    foreach ($data['stats']['usersByRole'] as $role) {
                                        if (strtolower($role['role_name'] ?? '') === 'admin') {
                                            $adminCount = $role['user_count'] ?? 0;
                                        }
                                    }
                                }
                                echo $adminCount;
                            ?>
                        </h3>
                    </div>
                    <span class="material-symbols-outlined text-tertiary text-2xl">admin_panel_settings</span>
                </div>
                <div class="mt-4 flex items-center text-tertiary">
                    <span class="material-symbols-outlined text-lg mr-2">security</span>
                    <span class="text-xs font-semibold">System managers</span>
                </div>
            </div>

            <!-- KPI 3: Medical Staff -->
            <div class="hover-lift bg-gradient-to-br from-surface-container-lowest to-surface-container-low p-6 rounded-xl shadow-sm border-t-2 border-secondary border-r border-b border-outline/10">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-on-surface-variant">Medical Staff</p>
                        <h3 class="text-3xl font-black mt-2 text-secondary">
                            <?php 
                                $medicalCount = 0;
                                if (isset($data['stats']['usersByRole'])) {
                                    foreach ($data['stats']['usersByRole'] as $role) {
                                        if (in_array(strtolower($role['role_name'] ?? ''), ['medecin', 'doctor'])) {
                                            $medicalCount += $role['user_count'] ?? 0;
                                        }
                                    }
                                }
                                echo $medicalCount;
                            ?>
                        </h3>
                    </div>
                    <span class="material-symbols-outlined text-secondary text-2xl">medical_services</span>
                </div>
                <div class="mt-4 flex items-center text-secondary">
                    <span class="material-symbols-outlined text-lg mr-2">trending_up</span>
                    <span class="text-xs font-semibold">Active doctors</span>
                </div>
            </div>

            <!-- KPI 4: Support Staff -->
            <div class="hover-lift bg-gradient-to-br from-surface-container-lowest to-surface-container-low p-6 rounded-xl shadow-sm border-t-2 border-error border-r border-b border-outline/10">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-on-surface-variant">Support Staff</p>
                        <h3 class="text-3xl font-black mt-2 text-error">
                            <?php 
                                $supportCount = 0;
                                if (isset($data['stats']['usersByRole'])) {
                                    foreach ($data['stats']['usersByRole'] as $role) {
                                        if (in_array(strtolower($role['role_name'] ?? ''), ['receptioniste', 'pharmacien', 'receptionist', 'pharmacist'])) {
                                            $supportCount += $role['user_count'] ?? 0;
                                        }
                                    }
                                }
                                echo $supportCount;
                            ?>
                        </h3>
                    </div>
                    <span class="material-symbols-outlined text-error text-2xl">group</span>
                </div>
                <div class="mt-4 flex items-center text-error">
                    <span class="material-symbols-outlined text-lg mr-2">people</span>
                    <span class="text-xs font-semibold">Reception & pharmacy</span>
                </div>
            </div>

            <!-- KPI 5: Patients (New) -->
            <div class="hover-lift bg-gradient-to-br from-surface-container-lowest to-surface-container-low p-6 rounded-xl shadow-sm border-t-2 border-green-500 border-r border-b border-outline/10">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-on-surface-variant">Patients</p>
                        <h3 class="text-3xl font-black mt-2 text-green-600">
                            <?php echo isset($data['stats']['totalPatients']) ? $data['stats']['totalPatients'] : 0; ?>
                        </h3>
                    </div>
                    <span class="material-symbols-outlined text-green-500 text-2xl">badge</span>
                </div>
                <div class="mt-4 flex items-center text-green-600">
                    <span class="material-symbols-outlined text-lg mr-2">person_check</span>
                    <span class="text-xs font-semibold">Registered patients</span>
                </div>
            </div>
        </section>

        <!-- Users Distribution Chart Section -->
        <section class="hover-lift bg-gradient-to-br from-surface-container-lowest to-surface-container-low p-8 rounded-xl shadow-sm border border-outline/10">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h3 class="text-2xl font-bold text-on-surface">Users by Role Distribution</h3>
                    <p class="text-sm text-on-surface-variant font-medium">System personnel breakdown</p>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <?php if (isset($data['stats']['usersByRole']) && is_array($data['stats']['usersByRole'])): ?>
                    <?php foreach ($data['stats']['usersByRole'] as $role): ?>
                        <div class="hover-lift p-5 bg-gradient-to-br from-surface-container-low via-surface-container-low to-surface-container-low rounded-xl flex flex-col items-start border border-outline/5 cursor-pointer">
                            <p class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">
                                <?php echo htmlspecialchars($role['role_name'] ?? 'Unknown'); ?>
                            </p>
                            <p class="text-3xl font-black text-primary mt-3">
                                <?php echo $role['user_count'] ?? 0; ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- Bottom Sections -->
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Activity -->
            <div class="hover-lift bg-gradient-to-br from-surface-container-lowest to-surface-container-low p-8 rounded-xl shadow-sm border border-outline/10">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-on-surface">Recent Activity</h3>
                    <a href="#activity" class="text-sm font-bold text-primary hover:underline transition-colors duration-300">View All</a>
                </div>
                <div class="space-y-6">
                    <?php if (isset($data['recentActivity']) && is_array($data['recentActivity'])): ?>
                        <?php foreach (array_slice($data['recentActivity'], 0, 5) as $activity): ?>
                            <div class="flex items-center space-x-4 p-3 rounded-lg hover:bg-surface-container/50 transition-colors duration-300">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-fixed to-primary-fixed/50 flex items-center justify-center text-primary flex-shrink-0">
                                    <span class="material-symbols-outlined text-lg">person_add</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-on-surface">
                                        <?php echo htmlspecialchars($activity['prenom'] . ' ' . $activity['nom']); ?> registered
                                    </p>
                                    <p class="text-xs text-on-surface-variant font-medium">
                                        <?php echo htmlspecialchars($activity['role_name'] ?? 'No role'); ?> - <?php echo htmlspecialchars($activity['mail']); ?>
                                    </p>
                                </div>
                                <span class="text-xs font-medium text-slate-400">Just now</span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-on-surface-variant text-center py-6">No recent activity</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- User Statistics Summary -->
            <div class="hover-lift bg-gradient-to-br from-surface-container-lowest to-surface-container-low p-8 rounded-xl shadow-sm border border-outline/10">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-on-surface">User Statistics</h3>
                    <div class="bg-gradient-to-r from-primary-fixed to-primary-fixed/50 px-3 py-1 rounded-full text-xs font-bold text-primary">
                        <?php echo isset($data['stats']['totalUsers']) ? $data['stats']['totalUsers'] : 0; ?> Total
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-surface-container-low to-surface-container-low/50 rounded-xl border border-outline/5 hover:border-primary/30 transition-all duration-300">
                        <span class="text-sm font-medium text-on-surface">Total Registered</span>
                        <span class="text-lg font-black text-primary"><?php echo isset($data['stats']['totalUsers']) ? $data['stats']['totalUsers'] : 0; ?></span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-surface-container-low to-surface-container-low/50 rounded-xl border border-outline/5 hover:border-tertiary/30 transition-all duration-300">
                        <span class="text-sm font-medium text-on-surface">Total Roles</span>
                        <span class="text-lg font-black text-tertiary"><?php echo isset($data['stats']['usersByRole']) ? count($data['stats']['usersByRole']) : 0; ?></span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-surface-container-low to-surface-container-low/50 rounded-xl border border-outline/5 hover:border-green-500/30 transition-all duration-300">
                        <span class="text-sm font-medium text-on-surface">System Status</span>
                        <span class="text-sm font-black text-green-600 flex items-center gap-2"><span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>Operational</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Patients Section (New) -->
        <section class="hover-lift bg-gradient-to-br from-surface-container-lowest to-surface-container-low p-8 rounded-xl shadow-sm border border-outline/10">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h3 class="text-2xl font-bold text-on-surface">Registered Patients</h3>
                    <p class="text-sm text-on-surface-variant font-medium">All patient accounts in the system</p>
                </div>
                <div class="bg-gradient-to-r from-green-500/10 to-green-500/5 px-4 py-2 rounded-full text-sm font-bold text-green-600">
                    Total: <?php echo isset($data['stats']['totalPatients']) ? $data['stats']['totalPatients'] : 0; ?>
                </div>
            </div>

            <!-- Patients Table -->
            <div style="background: white; border: 1px solid rgba(0, 77, 153, 0.08); border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr style="background: linear-gradient(90deg, rgba(34, 197, 94, 0.12) 0%, rgba(22, 163, 74, 0.08) 50%, rgba(16, 185, 129, 0.06) 100%); border-bottom: 2px solid rgba(34, 197, 94, 0.12);">
                        <th style="padding: 18px 24px; text-align: left; font-size: 12px; font-weight: 800; color: #191c1e; text-transform: uppercase; letter-spacing: 0.6px;">Matricule</th>
                        <th style="padding: 18px 24px; text-align: left; font-size: 12px; font-weight: 800; color: #191c1e; text-transform: uppercase; letter-spacing: 0.6px;">Nom</th>
                        <th style="padding: 18px 24px; text-align: left; font-size: 12px; font-weight: 800; color: #191c1e; text-transform: uppercase; letter-spacing: 0.6px;">Email</th>
                        <th style="padding: 18px 24px; text-align: left; font-size: 12px; font-weight: 800; color: #191c1e; text-transform: uppercase; letter-spacing: 0.6px;">Téléphone</th>
                        <th style="padding: 18px 24px; text-align: center; font-size: 12px; font-weight: 800; color: #191c1e; text-transform: uppercase; letter-spacing: 0.6px;">Status</th>
                    </tr>
                    <?php if (!empty($data['patients'])): ?>
                        <?php foreach ($data['patients'] as $patient): ?>
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 16px 24px; font-size: 14px; color: #22c55e; font-weight: 800;"><?php echo htmlspecialchars($patient['matricule'] ?? 'N/A'); ?></td>
                                <td style="padding: 16px 24px; font-size: 14px; color: #191c1e; font-weight: 600;"><?php echo htmlspecialchars($patient['prenom'] . ' ' . $patient['nom']); ?></td>
                                <td style="padding: 16px 24px; font-size: 14px; color: #424752;"><?php echo htmlspecialchars($patient['mail']); ?></td>
                                <td style="padding: 16px 24px; font-size: 14px; color: #191c1e;"><?php echo htmlspecialchars($patient['tel'] ?? '-'); ?></td>
                                <td style="padding: 16px 24px; text-align: center;"><span style="background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); color: #16a34a; font-weight: 800; padding: 8px 14px; border-radius: 20px; font-size: 12px; display: inline-block; border: 1px solid rgba(34, 197, 94, 0.2); box-shadow: 0 2px 8px rgba(34, 197, 94, 0.1); text-transform: uppercase; letter-spacing: 0.3px;">Active</span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="padding: 60px 40px; text-align: center;">
                                <span class="material-symbols-outlined" style="display: block; font-size: 56px; margin-bottom: 16px; opacity: 0.3; color: #22c55e;">person_add</span>
                                <p style="font-weight: 700; font-size: 18px; color: #191c1e; margin-bottom: 8px;">Aucun patient enregistré</p>
                                <p style="font-size: 14px; color: #999;">Les patients apparaîtront ici après leur inscription</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </section>
    </div>

    <!-- Include Users Section Component (Admin Only) -->
    <?php include __DIR__ . '/users_section.php'; ?>
</main>
</body>
</html>
