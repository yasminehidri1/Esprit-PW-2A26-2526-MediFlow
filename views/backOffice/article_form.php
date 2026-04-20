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

    <form method="POST" action="backOffice.php?action=save" class="space-y-6" id="articleForm" enctype="multipart/form-data">
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

            <!-- Image Upload with Drag & Drop -->
            <label class="block">
                <span class="text-sm font-bold text-on-surface flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-sm text-secondary">image</span>
                    Cover Image <span class="text-on-surface-variant font-normal">(optional)</span>
                </span>
            </label>

            <!-- Image Upload Area with Drag & Drop -->
            <div id="imageDropZone" class="relative border-2 border-dashed border-outline-variant/50 rounded-lg p-8 text-center transition-all bg-surface-container-low/50 hover:bg-surface-container-low hover:border-primary cursor-pointer group">
                <input type="file" id="imageInput" name="image_file" accept="image/*" class="hidden"/>
                
                <!-- Display current/preview image if exists -->
                <?php if (!empty($post['image_url']) && !isset($_FILES['image_file'])): ?>
                <div id="imagePreviewContainer" class="mb-4">
                    <img id="imagePreview" src="<?= htmlspecialchars($post['image_url']) ?>" alt="Current image" class="max-h-48 mx-auto rounded-lg mb-3"/>
                    <button type="button" onclick="removeImage()" class="text-xs font-bold text-error hover:text-error/80 transition-colors">Remove Image</button>
                </div>
                <?php else: ?>
                <div id="imagePreviewContainer" class="hidden mb-4">
                    <img id="imagePreview" alt="Image preview" class="max-h-48 mx-auto rounded-lg mb-3"/>
                </div>
                <?php endif; ?>

                <!-- Upload prompt -->
                <div id="uploadPrompt" <?= !empty($post['image_url']) && !isset($_FILES['image_file']) ? 'style="display:none;"' : '' ?>>
                    <span class="material-symbols-outlined text-4xl text-on-surface-variant mb-2 inline-block group-hover:text-primary transition-colors">cloud_upload</span>
                    <p class="text-sm font-bold text-on-surface mb-1">Drag and drop your image here</p>
                    <p class="text-xs text-on-surface-variant mb-3">or</p>
                    <button type="button" onclick="document.getElementById('imageInput').click()" class="px-4 py-2 text-xs font-bold rounded-lg bg-primary text-on-primary hover:opacity-90 transition-opacity">
                        Browse Files
                    </button>
                    <p class="text-xs text-on-surface-variant mt-3">Supported formats: JPG, PNG, WebP (Max 5MB)</p>
                </div>
            </div>

            <!-- Fallback URL field for external images -->
            <div class="mt-4">
                <label class="block">
                    <span class="text-xs font-bold text-on-surface-variant flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-xs">link</span>
                        Or use external image URL
                    </span>
                    <input type="url" name="image_url" id="imageUrlField"
                           value="<?= htmlspecialchars($post['image_url'] ?? '') ?>"
                           placeholder="https://example.com/image.jpg"
                           class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-lg text-on-surface focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all text-sm"/>
                </label>
            </div>

            <script>
            let selectedFile = null;
            const dropZone = document.getElementById('imageDropZone');
            const imageInput = document.getElementById('imageInput');
            const imagePreview = document.getElementById('imagePreview');
            const imagePreviewContainer = document.getElementById('imagePreviewContainer');
            const uploadPrompt = document.getElementById('uploadPrompt');
            const imageUrlField = document.getElementById('imageUrlField');

            // Drag and drop events
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            dropZone.addEventListener('dragenter', () => dropZone.classList.add('border-primary', 'bg-surface-container'));
            dropZone.addEventListener('dragleave', () => dropZone.classList.remove('border-primary', 'bg-surface-container'));
            dropZone.addEventListener('drop', handleDrop, false);
            dropZone.addEventListener('click', () => imageInput.click());

            function handleDrop(e) {
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    handleFile(files[0]);
                }
                dropZone.classList.remove('border-primary', 'bg-surface-container');
            }

            imageInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    handleFile(e.target.files[0]);
                }
            });

            function handleFile(file) {
                const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
                const maxSize = 5 * 1024 * 1024;

                if (!validTypes.includes(file.type)) {
                    alert('Please select a valid image file (JPG, PNG, or WebP)');
                    return;
                }
                if (file.size > maxSize) {
                    alert('Image size must be less than 5MB');
                    return;
                }

                selectedFile = file;
                imageInput.files = new DataTransfer().items.add(file).files;

                const reader = new FileReader();
                reader.onload = (e) => {
                    imagePreview.src = e.target.result;
                    imagePreviewContainer.style.display = 'block';
                    uploadPrompt.style.display = 'none';
                    imageUrlField.value = '';
                };
                reader.readAsDataURL(file);
            }

            function removeImage() {
                selectedFile = null;
                imageInput.value = '';
                imagePreviewContainer.style.display = 'none';
                uploadPrompt.style.display = 'block';
                imageUrlField.value = '';
            }
            </script>
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
