<?php
/**
 * Admin - Edit User View
 */
?>
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
            <div class="max-w-3xl mx-auto bg-white dark:bg-slate-800 rounded-2xl shadow-xl p-8 border border-outline/30 animate-scale-in">
                <form method="POST" action="/integration/admin?action=update" class="space-y-6">
                    <input type="hidden" name="id" value="<?php echo $user['id_PK']; ?>">

                    <!-- Nom & Prénom Row -->
                    <div class="grid grid-cols-2 gap-6">
                        <div class="form-group">
                            <label for="nom" class="block text-sm font-semibold text-on-surface mb-2">Nom *</label>
                            <input type="text" id="nom" name="nom"
                                   value="<?php echo htmlspecialchars($user['nom']); ?>"
                                   class="w-full px-4 py-3 border border-outline/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-surface-container-lowest dark:bg-slate-700 transition-all duration-300 hover:border-outline/50"
                                   placeholder="Dupont">
                            <div class="h-1 mt-2 bg-gradient-to-r from-primary to-primary-container rounded-full scale-x-0 focus-within:scale-x-100 transition-transform"></div>
                        </div>

                        <div class="form-group">
                            <label for="prenom" class="block text-sm font-semibold text-on-surface mb-2">Prénom *</label>
                            <input type="text" id="prenom" name="prenom"
                                   value="<?php echo htmlspecialchars($user['prenom']); ?>"
                                   class="w-full px-4 py-3 border border-outline/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-surface-container-lowest dark:bg-slate-700 transition-all duration-300 hover:border-outline/50"
                                   placeholder="Jean">
                            <div class="h-1 mt-2 bg-gradient-to-r from-primary to-primary-container rounded-full scale-x-0 focus-within:scale-x-100 transition-transform"></div>
                        </div>
                    </div>

                    <!-- Email Row -->
                    <div class="form-group">
                        <label for="mail" class="block text-sm font-semibold text-on-surface mb-2">Email *</label>
                        <input type="email" id="mail" name="mail"
                               value="<?php echo htmlspecialchars($user['mail']); ?>"
                               class="w-full px-4 py-3 border border-outline/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-surface-container-lowest dark:bg-slate-700 transition-all duration-300 hover:border-outline/50"
                               placeholder="jean.dupont@mediflow.com">
                        <div class="h-1 mt-2 bg-gradient-to-r from-primary to-primary-container rounded-full scale-x-0 focus-within:scale-x-100 transition-transform"></div>
                    </div>

                    <!-- Téléphone & Rôle Row -->
                    <div class="grid grid-cols-2 gap-6">
                        <div class="form-group">
                            <label for="tel" class="block text-sm font-semibold text-on-surface mb-2">Téléphone</label>
                            <input type="tel" id="tel" name="tel"
                                   value="<?php echo htmlspecialchars($user['tel'] ?? ''); ?>"
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
                                    <option value="<?php echo $role['id_role']; ?>"
                                            <?php echo ($user['id_role'] == $role['id_role']) ? 'selected' : ''; ?>>
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
                                  placeholder="Rue, Ville, Code Postal"><?php echo htmlspecialchars($user['adresse'] ?? ''); ?></textarea>
                        <div class="h-1 mt-2 bg-gradient-to-r from-primary to-primary-container rounded-full scale-x-0 focus-within:scale-x-100 transition-transform"></div>
                    </div>

                    <!-- Password Section -->
                    <div class="border-t border-outline/20 pt-6 mt-8">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="material-symbols-outlined text-primary text-5 opacity-70">lock</span>
                            <div>
                                <p class="text-sm font-semibold text-on-surface">Modifier le mot de passe</p>
                                <p class="text-xs text-on-surface-variant">Laisser vide pour garder le mot de passe actuel</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-6">
                            <div class="form-group">
                                <label for="password" class="block text-sm font-semibold text-on-surface mb-2">Nouveau mot de passe</label>
                                <input type="password" id="password" name="password" 
                                       class="w-full px-4 py-3 border border-outline/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-surface-container-lowest dark:bg-slate-700 transition-all duration-300 hover:border-outline/50"
                                       placeholder="Laisser vide pour ne pas changer">
                                <div class="h-1 mt-2 bg-gradient-to-r from-primary to-primary-container rounded-full scale-x-0 focus-within:scale-x-100 transition-transform"></div>
                            </div>

                            <div class="form-group">
                                <label for="password_confirm" class="block text-sm font-semibold text-on-surface mb-2">Confirmer</label>
                                <input type="password" id="password_confirm" name="password_confirm" 
                                       class="w-full px-4 py-3 border border-outline/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-surface-container-lowest dark:bg-slate-700 transition-all duration-300 hover:border-outline/50"
                                       placeholder="Répéter le mot de passe">
                                <div class="h-1 mt-2 bg-gradient-to-r from-primary to-primary-container rounded-full scale-x-0 focus-within:scale-x-100 transition-transform"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-4 pt-8">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-primary to-primary-container text-on-primary px-6 py-4 rounded-xl hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02] active:scale-95 font-semibold flex items-center justify-center gap-2 btn-submit shadow-lg hover:shadow-2xl">
                            <span class="material-symbols-outlined">check_circle</span> Mettre à jour
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
