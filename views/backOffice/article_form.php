<?php
/**
 * Back Office — Article Create/Edit Form
 */
$isEdit = !empty($post);
$pageTitle = $isEdit ? 'Edit Article' : 'Create New Article';
?>

<div class="max-w-4xl">
    <div class="flex items-center gap-4 mb-8">
        <a href="backOffice.php?action=articles" class="p-2 hover:bg-surface-container-high rounded-lg transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h2 class="text-2xl font-bold"><?= $pageTitle ?></h2>
            <p class="text-sm text-on-surface-variant"><?= $isEdit ? 'Editing: ' . htmlspecialchars($post['titre']) : 'Fill in the details for your new article' ?></p>
        </div>
    </div>

    <form method="POST" action="backOffice.php?action=save" class="space-y-6" id="articleForm">
        <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= $post['id'] ?>"/>
        <?php endif; ?>
        <input type="hidden" name="auteur_id" value="<?= $isEdit ? $post['auteur_id'] : 1 ?>"/>

        <!-- Title -->
        <div class="bg-surface-container-lowest rounded-xl p-6 shadow-sm space-y-4">
            <label class="block">
                <span class="text-sm font-bold text-on-surface flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-sm text-primary">title</span>
                    Article Title <span class="text-error">*</span>
                </span>
                <input type="text" name="titre" required
                       value="<?= htmlspecialchars($post['titre'] ?? '') ?>"
                       placeholder="Enter a compelling title..."
                       class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-lg text-on-surface focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all text-lg font-semibold"/>
            </label>

            <!-- Category & Status Row -->
            <div class="grid grid-cols-2 gap-4">
                <label class="block">
                    <span class="text-sm font-bold text-on-surface flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-sm text-tertiary">category</span>
                        Category
                    </span>
                    <select name="categorie" class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-lg text-on-surface focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                        <?php 
                        $defaultCategories = ['General Health', 'Mental Wellness', 'Diet & Nutrition', 'Active Living', 'Research', 'Journals'];
                        foreach ($defaultCategories as $cat): 
                        ?>
                        <option value="<?= $cat ?>" <?= ($post['categorie'] ?? 'General Health') === $cat ? 'selected' : '' ?>>
                            <?= $cat ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="block">
                    <span class="text-sm font-bold text-on-surface flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-sm text-secondary">toggle_on</span>
                        Status
                    </span>
                    <select name="statut" class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-lg text-on-surface focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                        <option value="brouillon" <?= ($post['statut'] ?? 'brouillon') === 'brouillon' ? 'selected' : '' ?>>Draft</option>
                        <option value="publie" <?= ($post['statut'] ?? '') === 'publie' ? 'selected' : '' ?>>Published</option>
                        <option value="archive" <?= ($post['statut'] ?? '') === 'archive' ? 'selected' : '' ?>>Archived</option>
                    </select>
                </label>
            </div>

            <!-- Image URL -->
            <label class="block">
                <span class="text-sm font-bold text-on-surface flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-sm text-secondary">image</span>
                    Cover Image URL <span class="text-on-surface-variant font-normal">(optional)</span>
                </span>
                <input type="url" name="image_url"
                       value="<?= htmlspecialchars($post['image_url'] ?? '') ?>"
                       placeholder="https://example.com/image.jpg"
                       class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-lg text-on-surface focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
            </label>
        </div>

        <!-- Content -->
        <div class="bg-surface-container-lowest rounded-xl p-6 shadow-sm">
            <label class="block">
                <span class="text-sm font-bold text-on-surface flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-sm text-primary">edit_note</span>
                    Article Content <span class="text-error">*</span>
                </span>
                <textarea name="contenu" required rows="16"
                          placeholder="Write your article content here..."
                          class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-lg text-on-surface focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all leading-relaxed resize-y"><?= htmlspecialchars($post['contenu'] ?? '') ?></textarea>
                <div class="flex items-center justify-between mt-2">
                    <span class="text-xs text-on-surface-variant" id="charCount">0 characters</span>
                    <span class="text-xs text-on-surface-variant" id="readTime">~0 min read</span>
                </div>
            </label>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between">
            <a href="backOffice.php?action=articles" class="px-6 py-3 text-sm font-semibold text-on-surface-variant bg-surface-container rounded-lg hover:bg-surface-container-high transition-colors">
                Cancel
            </a>
            <div class="flex gap-3">
                <button type="submit" name="statut" value="brouillon" class="px-6 py-3 text-sm font-semibold text-on-surface border border-outline-variant/30 rounded-lg hover:bg-surface-container transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">save</span>
                    Save as Draft
                </button>
                <button type="submit" class="px-6 py-3 text-sm font-semibold text-on-primary bg-gradient-to-r from-primary to-primary-container rounded-lg hover:opacity-90 transition-opacity shadow-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm"><?= $isEdit ? 'update' : 'publish' ?></span>
                    <?= $isEdit ? 'Update Article' : 'Create Article' ?>
                </button>
            </div>
        </div>
    </form>
</div>
