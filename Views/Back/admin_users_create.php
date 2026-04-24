<?php
/**
 * Admin - Create User View
 */
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Ajouter Utilisateur - MediFlow Admin</title>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="/integration/assets/css/style.css">
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
<aside class="h-screen w-64 fixed left-0 top-0 bg-slate-50 dark:bg-slate-900 flex flex-col py-8 space-y-6 z-50 border-r border-outline">
    <div class="px-8">
        <h1 class="text-2xl font-black tracking-tight text-blue-900 dark:text-blue-100">MediFlow</h1>
        <p class="text-xs font-medium text-slate-500 uppercase tracking-widest mt-1">Admin Panel</p>
    </div>
    
    <nav class="flex-1 flex flex-col space-y-2 px-4">
        <a href="/integration/admin/users" class="flex items-center space-x-3 text-slate-500 dark:text-slate-400 hover:text-primary pl-4 py-3 group transition">
            <span class="material-symbols-outlined">people</span>
            <span class="font-medium">Utilisateurs</span>
        </a>
        <a href="/integration/dashboard" class="flex items-center space-x-3 text-slate-500 dark:text-slate-400 hover:text-primary pl-4 py-3 group transition">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="font-medium">Dashboard</span>
        </a>
    </nav>

    <div class="px-8 border-t border-outline pt-4">
        <a href="/integration/logout" class="flex items-center space-x-3 text-slate-500 dark:text-slate-400 hover:text-error">
            <span class="material-symbols-outlined">logout</span>
            <span class="font-medium text-sm">Déconnexion</span>
        </a>
    </div>
</aside>

<!-- Main Content -->
<div class="ml-64 h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-gradient-to-r from-white to-surface-container-low dark:from-slate-800 dark:to-slate-700 border-b border-outline shadow-md">
        <div class="flex items-center justify-between px-8 py-6 animate-fade-in">
            <div class="flex items-center gap-4">
                <a href="/integration/admin" class="text-primary hover:text-primary-container transition-all duration-300 hover:scale-110 transform p-2">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <div>
                    <h2 class="text-3xl font-bold bg-gradient-to-r from-primary to-primary-container bg-clip-text text-transparent">Ajouter Utilisateur</h2>
                    <p class="text-on-surface-variant text-sm mt-1">Créez un nouveau compte utilisateur pour le système</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Content -->
    <main class="flex-1 overflow-auto">
        <div class="p-8">
            <!-- Error Messages -->
            <?php if (!empty($error)): ?>
                <div class="bg-gradient-to-r from-red-50 to-rose-50 border border-red-300 text-red-800 px-6 py-4 rounded-xl mb-6 flex items-center gap-3 shadow-md animate-fade-in">
                    <span class="material-symbols-outlined text-red-600 text-2xl">error</span>
                    <div>
                        <p class="font-semibold">Erreur</p>
                        <p class="text-sm text-red-700"><?php echo $error; ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <div class="max-w-3xl mx-auto bg-white dark:bg-slate-800 rounded-2xl shadow-2xl p-10 border border-outline/20 animate-scale-in">
                <form method="POST" action="/integration/admin?action=create" class="space-y-6">
                    <!-- Nom & Prénom Row -->
                    <div class="grid grid-cols-2 gap-6">
                        <div class="form-group">
                            <label for="nom" class="block text-sm font-semibold text-on-surface mb-2">Nom *</label>
                            <input type="text" id="nom" name="nom"
                                   class="w-full px-4 py-3 border border-outline/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-surface-container-lowest dark:bg-slate-700 transition-all duration-300 hover:border-outline/50"
                                   placeholder="Dupont">
                            <div class="h-1 mt-2 bg-gradient-to-r from-primary to-primary-container rounded-full scale-x-0 group-focus-within:scale-x-100 transition-transform"></div>
                        </div>

                        <div class="form-group">
                            <label for="prenom" class="block text-sm font-semibold text-on-surface mb-2">Prénom *</label>
                            <input type="text" id="prenom" name="prenom"
                                   class="w-full px-4 py-3 border border-outline/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-surface-container-lowest dark:bg-slate-700 transition-all duration-300 hover:border-outline/50"
                                   placeholder="Jean">
                            <div class="h-1 mt-2 bg-gradient-to-r from-primary to-primary-container rounded-full scale-x-0 focus-within:scale-x-100 transition-transform"></div>
                        </div>
                    </div>

                    <!-- Email Row -->
                    <div class="form-group">
                        <label for="mail" class="block text-sm font-semibold text-on-surface mb-2">Email *</label>
                        <input type="email" id="mail" name="mail"
                               class="w-full px-4 py-3 border border-outline/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-surface-container-lowest dark:bg-slate-700 transition-all duration-300 hover:border-outline/50"
                               placeholder="jean.dupont@mediflow.com">
                        <div class="h-1 mt-2 bg-gradient-to-r from-primary to-primary-container rounded-full scale-x-0 focus-within:scale-x-100 transition-transform"></div>
                    </div>

                    <!-- Téléphone & Rôle Row -->
                    <div class="grid grid-cols-2 gap-6">
                        <div class="form-group">
                            <label for="tel" class="block text-sm font-semibold text-on-surface mb-2">Téléphone</label>
                            <input type="tel" id="tel" name="tel"
                                   class="w-full px-4 py-3 border border-outline/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-surface-container-lowest dark:bg-slate-700 transition-all duration-300 hover:border-outline/50"
                                   placeholder="+216 XX XXX XXX">
                            <div class="h-1 mt-2 bg-gradient-to-r from-primary to-primary-container rounded-full scale-x-0 focus-within:scale-x-100 transition-transform"></div>
                        </div>

                        <div class="form-group">
                            <label for="id_role" class="block text-sm font-semibold text-on-surface mb-2">Rôle *</label>
                            <select id="id_role" name="id_role"
                                    class="admin-select-dropdown w-full px-4 py-3 border border-outline/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-surface-container-lowest dark:bg-slate-700 transition-all duration-300 hover:border-outline/50 appearance-none cursor-pointer">
                                <option value="">Sélectionner un rôle</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?php echo $role['id_role']; ?>">
                                        <?php echo htmlspecialchars($role['libelle']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="h-1 mt-2 bg-gradient-to-r from-primary to-primary-container rounded-full scale-x-0 focus-within:scale-x-100 transition-transform"></div>
                        </div>
                    </div>

                    <!-- Adresse Row -->
                    <div class="form-group">
                        <label for="adresse" class="block text-sm font-semibold text-on-surface mb-2">Adresse</label>
                        <textarea id="adresse" name="adresse" rows="3" 
                                  class="w-full px-4 py-3 border border-outline/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-surface-container-lowest dark:bg-slate-700 transition-all duration-300 resize-none hover:border-outline/50"
                                  placeholder="Rue, Ville, Code Postal"></textarea>
                        <div class="h-1 mt-2 bg-gradient-to-r from-primary to-primary-container rounded-full scale-x-0 focus-within:scale-x-100 transition-transform"></div>
                    </div>

                    <!-- Password Row -->
                    <div class="grid grid-cols-2 gap-6">
                        <div class="form-group">
                            <label for="password" class="block text-sm font-semibold text-on-surface mb-2">Mot de passe *</label>
                            <input type="password" id="password" name="password"
                                   class="w-full px-4 py-3 border border-outline/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-surface-container-lowest dark:bg-slate-700 transition-all duration-300 hover:border-outline/50"
                                   placeholder="Minimum 6 caractères">
                            <div class="h-1 mt-2 bg-gradient-to-r from-primary to-primary-container rounded-full scale-x-0 focus-within:scale-x-100 transition-transform"></div>
                        </div>

                        <div class="form-group">
                            <label for="password_confirm" class="block text-sm font-semibold text-on-surface mb-2">Confirmer *</label>
                            <input type="password" id="password_confirm" name="password_confirm"
                                   class="w-full px-4 py-3 border border-outline/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-surface-container-lowest dark:bg-slate-700 transition-all duration-300 hover:border-outline/50"
                                   placeholder="Répéter le mot de passe">
                            <div class="h-1 mt-2 bg-gradient-to-r from-primary to-primary-container rounded-full scale-x-0 focus-within:scale-x-100 transition-transform"></div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-4 pt-8">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-primary to-primary-container text-on-primary px-6 py-4 rounded-xl hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02] active:scale-95 font-semibold flex items-center justify-center gap-2 btn-submit shadow-lg hover:shadow-2xl">
                            <span class="material-symbols-outlined">check_circle</span> Créer l'Utilisateur
                        </button>
                        <a href="/integration/admin" class="flex-1 bg-surface-container hover:bg-surface-container-high text-on-surface px-6 py-4 rounded-xl transition-all duration-300 font-semibold text-center flex items-center justify-center gap-2 btn-cancel border border-outline/20 hover:border-outline/40">
                            <span class="material-symbols-outlined">close</span> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script src="/integration/assets/js/form-validation.js"></script>
</body>
</html>
