<?php
/**
 * Back Office — Article Create/Edit Form
 */
$isEdit = !empty($post);
$pageTitle = $isEdit ? 'Edit Article' : 'Create New Article';
?>

<div class="max-w-4xl">
    <div class="flex items-center gap-4 mb-8">
        <a href="/integration/magazine/admin/articles" class="p-2 hover:bg-surface-container-high rounded-lg transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h2 class="text-2xl font-bold"><?= $pageTitle ?></h2>
            <p class="text-sm text-on-surface-variant"><?= $isEdit ? 'Editing: ' . htmlspecialchars($post['titre']) : 'Fill in the details for your new article' ?></p>
        </div>
    </div>

    <form method="POST" action="/integration/magazine/admin/save" class="space-y-6" id="articleForm" enctype="multipart/form-data">
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
                <input type="hidden" id="originalImageData" name="originalImageData" value=""/>
                
                <!-- Display current/preview image if exists -->
                <?php if (!empty($post['image_url']) && !isset($_FILES['image_file'])): ?>
                <div id="imagePreviewContainer" class="mb-4">
                    <img id="imagePreview" src="<?= htmlspecialchars($post['image_url']) ?>" alt="Current image" class="max-h-48 mx-auto rounded-lg mb-3"/>
                    <div class="flex gap-2 justify-center">
                        <button type="button" onclick="openImageEditor()" class="text-xs font-bold text-primary hover:text-primary/80 transition-colors flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">crop</span> Edit Image
                        </button>
                        <button type="button" onclick="removeImage()" class="text-xs font-bold text-error hover:text-error/80 transition-colors">Remove Image</button>
                    </div>
                </div>
                <?php else: ?>
                <div id="imagePreviewContainer" class="hidden mb-4">
                    <img id="imagePreview" alt="Image preview" class="max-h-48 mx-auto rounded-lg mb-3"/>
                    <div class="flex gap-2 justify-center">
                        <button type="button" onclick="openImageEditor()" class="text-xs font-bold text-primary hover:text-primary/80 transition-colors flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">crop</span> Edit Image
                        </button>
                        <button type="button" onclick="removeImage()" class="text-xs font-bold text-error hover:text-error/80 transition-colors">Remove Image</button>
                    </div>
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
                           value="<?= (isset($post['image_url']) && strpos($post['image_url'], 'http') === 0) ? htmlspecialchars($post['image_url']) : '' ?>"
                           placeholder="https://example.com/image.jpg"
                           class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-lg text-on-surface focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all text-sm"/>
                </label>
            </div>

            <script>
            (function() {
                const dropZone   = document.getElementById('imageDropZone');
                const imageInput = document.getElementById('imageInput');
                const imagePreview = document.getElementById('imagePreview');
                const imagePreviewContainer = document.getElementById('imagePreviewContainer');
                const uploadPrompt = document.getElementById('uploadPrompt');
                const imageUrlField = document.getElementById('imageUrlField');

                // Drag and drop — prevent browser default open
                ['dragenter','dragover','dragleave','drop'].forEach(ev =>
                    dropZone.addEventListener(ev, e => { e.preventDefault(); e.stopPropagation(); })
                );

                dropZone.addEventListener('dragenter', () => dropZone.classList.add('border-primary','bg-surface-container'));
                dropZone.addEventListener('dragleave', e => {
                    if (!dropZone.contains(e.relatedTarget)) dropZone.classList.remove('border-primary','bg-surface-container');
                });
                dropZone.addEventListener('drop', e => {
                    dropZone.classList.remove('border-primary','bg-surface-container');
                    if (e.dataTransfer.files.length > 0) processFile(e.dataTransfer.files[0]);
                });

                // Clicking the zone opens the file picker (but not if Browse button is inside)
                dropZone.addEventListener('click', function(e) {
                    if (e.target.closest('button')) return; // Don't double-trigger
                    imageInput.click();
                });

                imageInput.addEventListener('change', function() {
                    if (this.files.length > 0) processFile(this.files[0]);
                });

                function processFile(file) {
                    const validTypes = ['image/jpeg','image/png','image/webp'];
                    if (!validTypes.includes(file.type)) {
                        alert('Please select a valid image file (JPG, PNG, or WebP)');
                        return;
                    }
                    if (file.size > 5 * 1024 * 1024) {
                        alert('Image size must be less than 5MB');
                        return;
                    }

                    // Show preview
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        imagePreview.src = ev.target.result;
                        imagePreviewContainer.style.display = 'block';
                        uploadPrompt.style.display = 'none';
                        imageUrlField.value = ''; // clear URL field
                        // DO NOT store data URL for regular file uploads - let form handle the file normally
                    };
                    reader.readAsDataURL(file);
                }

                window.removeImage = function() {
                    imageInput.value = '';
                    imagePreviewContainer.style.display = 'none';
                    uploadPrompt.style.display = 'block';
                    imagePreview.src = '';
                    imageUrlField.value = '';
                    document.getElementById('originalImageData').value = '';
                };
            })();
            </script>
        </div>

        <!-- Content -->
        <div class="bg-surface-container-lowest rounded-xl p-6 shadow-sm">
            <label class="block mb-4">
                <span class="text-sm font-bold text-on-surface flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-sm text-primary">edit_note</span>
                    Article Content <span class="text-error">*</span>
                </span>
                <textarea id="articleContent" name="contenu" required rows="16"
                          placeholder="Write your article content here..."
                          class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-lg text-on-surface focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all leading-relaxed resize-y"><?= htmlspecialchars($post['contenu'] ?? '') ?></textarea>
                <div class="flex items-center justify-between mt-2">
                    <span class="text-xs text-on-surface-variant" id="charCount">0 characters</span>
                    <span class="text-xs text-on-surface-variant" id="readTime">~0 min read</span>
                </div>
            </label>

            <!-- AI Rephrase Section -->
            <div class="mt-4 p-4 bg-gradient-to-r from-indigo-50/50 to-blue-50/50 rounded-lg border border-primary/20">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">sparkles</span>
                        <span class="text-sm font-semibold text-on-surface">AI Text Improvement</span>
                    </div>
                    <button type="button" onclick="openRephrasingModal()" class="px-4 py-2 text-xs font-bold bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-lg hover:opacity-90 transition-opacity flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">auto_awesome</span>
                        Rephrase Content
                    </button>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between">
            <a href="/integration/magazine/admin/articles" class="px-6 py-3 text-sm font-semibold text-on-surface-variant bg-surface-container rounded-lg hover:bg-surface-container-high transition-colors">
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

<!-- ====== AI REPHRASING MODAL ====== -->
<div id="rephraseModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-surface-container-lowest rounded-2xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="sticky top-0 flex items-center justify-between px-8 py-6 border-b border-outline-variant/20 bg-surface-container-lowest">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-600 to-blue-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-white text-lg">auto_awesome</span>
                </div>
                <h3 class="text-lg font-bold text-on-surface">Rephrase Content</h3>
            </div>
            <button onclick="closeRephrasingModal()" class="p-2 hover:bg-surface-container rounded-lg transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Content -->
        <div class="px-8 py-6 space-y-4">
            <!-- Original -->
            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-2">ORIGINAL TEXT</label>
                <div id="originalTextDisplay" class="w-full px-4 py-3 bg-surface-container-low rounded-lg text-on-surface text-sm leading-relaxed max-h-[150px] overflow-y-auto border border-outline-variant/20"></div>
            </div>

            <!-- Rephrased -->
            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-2">REPHRASED TEXT</label>
                <div id="rephraseOutput" class="w-full px-4 py-3 bg-surface-container-low rounded-lg text-on-surface text-sm leading-relaxed min-h-[150px] max-h-[150px] overflow-y-auto border border-outline-variant/20">
                    <div class="flex items-center justify-center h-full">
                        <p class="text-on-surface-variant text-xs">Click "Generate" to see the rephrased text</p>
                    </div>
                </div>
            </div>

            <!-- Tone Selector -->
            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-2">WRITING TONE</label>
                <select id="toneSelector" class="w-full px-4 py-2 bg-surface-container-low border border-outline-variant/20 rounded-lg text-on-surface text-sm focus:ring-2 focus:ring-primary/30">
                    <option value="professional">Professional & Formal</option>
                    <option value="friendly">Friendly & Conversational</option>
                    <option value="academic">Academic & Detailed</option>
                    <option value="simple">Simple & Clear</option>
                </select>
            </div>
        </div>

        <!-- Footer -->
        <div class="sticky bottom-0 flex items-center justify-end gap-3 px-8 py-4 border-t border-outline-variant/20 bg-surface-container-lowest">
            <button onclick="closeRephrasingModal()" class="px-5 py-2 text-sm font-semibold text-on-surface-variant bg-surface-container rounded-lg hover:bg-surface-container-high transition-colors">
                Cancel
            </button>
            <button onclick="generateRephrase()" id="generateRephraiseBtn" class="px-5 py-2 text-sm font-semibold text-on-primary bg-gradient-to-r from-indigo-600 to-blue-600 rounded-lg hover:opacity-90 transition-opacity flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">auto_awesome</span>
                Generate
            </button>
            <button onclick="applyRephrase()" id="applyRephraseBtn" class="hidden px-5 py-2 text-sm font-semibold text-on-primary bg-gradient-to-r from-green-600 to-emerald-600 rounded-lg hover:opacity-90 transition-opacity flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">check_circle</span>
                Apply Changes
            </button>
        </div>
    </div>
</div>

<!-- ====== IMAGE EDITOR MODAL ====== -->
<div id="imageEditorModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-surface-container-lowest rounded-2xl shadow-2xl max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="sticky top-0 flex items-center justify-between px-8 py-6 border-b border-outline-variant/20 bg-surface-container-lowest">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-green-600 to-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-white text-lg">crop</span>
                </div>
                <h3 class="text-lg font-bold text-on-surface">Edit Image</h3>
            </div>
            <button onclick="closeImageEditor()" class="p-2 hover:bg-surface-container rounded-lg transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Content -->
        <div class="px-8 py-6 space-y-4">
            <!-- Canvas for cropping -->
            <div class="bg-surface-container rounded-lg overflow-hidden border border-outline-variant/20">
                <canvas id="cropCanvas" class="w-full max-h-[400px]" style="display: block;"></canvas>
            </div>

            <!-- Controls -->
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant mb-1">Rotation</label>
                    <input type="range" id="rotateSlider" min="0" max="360" value="0" class="w-full">
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant mb-1">Brightness</label>
                    <input type="range" id="brightnessSlider" min="50" max="150" value="100" class="w-full">
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant mb-1">Contrast</label>
                    <input type="range" id="contrastSlider" min="50" max="150" value="100" class="w-full">
                </div>
            </div>

            <!-- Preset Ratios -->
            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-2">Aspect Ratio</label>
                <div class="flex flex-wrap gap-2">
                    <button onclick="setCropRatio(16/9)" class="px-3 py-1 text-xs font-semibold bg-surface-container border border-outline-variant/20 rounded-lg hover:bg-surface-container-high transition-colors">16:9</button>
                    <button onclick="setCropRatio(4/3)" class="px-3 py-1 text-xs font-semibold bg-surface-container border border-outline-variant/20 rounded-lg hover:bg-surface-container-high transition-colors">4:3</button>
                    <button onclick="setCropRatio(1/1)" class="px-3 py-1 text-xs font-semibold bg-surface-container border border-outline-variant/20 rounded-lg hover:bg-surface-container-high transition-colors">1:1</button>
                    <button onclick="setCropRatio(3/2)" class="px-3 py-1 text-xs font-semibold bg-surface-container border border-outline-variant/20 rounded-lg hover:bg-surface-container-high transition-colors">3:2</button>
                    <button onclick="setCropRatio(null)" class="px-3 py-1 text-xs font-semibold bg-surface-container border border-outline-variant/20 rounded-lg hover:bg-surface-container-high transition-colors">Free</button>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="sticky bottom-0 flex items-center justify-end gap-3 px-8 py-4 border-t border-outline-variant/20 bg-surface-container-lowest">
            <button onclick="closeImageEditor()" class="px-5 py-2 text-sm font-semibold text-on-surface-variant bg-surface-container rounded-lg hover:bg-surface-container-high transition-colors">
                Cancel
            </button>
            <button onclick="resetImageEditor()" class="px-5 py-2 text-sm font-semibold text-on-surface-variant bg-surface-container rounded-lg hover:bg-surface-container-high transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">undo</span>
                Reset to Last Save
            </button>
            <button onclick="resetToOriginal()" class="px-5 py-2 text-sm font-semibold text-on-surface-variant bg-surface-container rounded-lg hover:bg-surface-container-high transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">history</span>
                Reset to Original
            </button>
            <button onclick="saveEditedImage()" class="px-5 py-2 text-sm font-semibold text-on-primary bg-gradient-to-r from-green-600 to-emerald-600 rounded-lg hover:opacity-90 transition-opacity flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">check_circle</span>
                Save Changes
            </button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    // ====== AI REPHRASING ======
    function openRephrasingModal() {
        const content = document.getElementById('articleContent').value;
        if (!content.trim()) {
            alert('Please write some content first');
            return;
        }
        document.getElementById('originalTextDisplay').textContent = content;
        document.getElementById('rephraseModal').classList.remove('hidden');
    }

    function closeRephrasingModal() {
        document.getElementById('rephraseModal').classList.add('hidden');
    }

    async function generateRephrase() {
        const btn = document.getElementById('generateRephraiseBtn');
        const originalText = document.getElementById('articleContent').value;
        const tone = document.getElementById('toneSelector').value;
        const output = document.getElementById('rephraseOutput');

        if (!originalText.trim()) {
            alert('No content to rephrase');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin">sync</span> Processing...';
        output.innerHTML = '<div class="flex items-center justify-center h-full"><span class="material-symbols-outlined animate-spin text-primary">sync</span></div>';

        try {
            const res = await fetch('/integration/magazine/admin/rephrase-content', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ content: originalText, tone: tone })
            });
            
            // Get the raw text first to debug
            const text = await res.text();
            console.log('Raw Response:', text.substring(0, 500)); // Log first 500 chars
            
            // Try to parse as JSON
            let data;
            try {
                data = JSON.parse(text);
            } catch (parseErr) {
                output.innerHTML = `<p class="text-error text-xs">Invalid JSON Response from server:<br>${text.substring(0, 200)}</p>`;
                return;
            }

            if (data.success) {
                output.textContent = data.rephrased;
                document.getElementById('generateRephraiseBtn').classList.add('hidden');
                document.getElementById('applyRephraseBtn').classList.remove('hidden');
                output.style.whiteSpace = 'pre-wrap';
            } else {
                output.innerHTML = `<p class="text-error text-xs">${data.error}<br><small>${data.details || ''}</small></p>`;
            }
        } catch (err) {
            output.innerHTML = `<p class="text-error text-xs">Fetch Error: ${err.message}</p>`;
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<span class="material-symbols-outlined text-sm">auto_awesome</span> Generate';
        }
    }

    function applyRephrase() {
        const rephrased = document.getElementById('rephraseOutput').textContent;
        document.getElementById('articleContent').value = rephrased;
        closeRephrasingModal();
    }

    // ====== IMAGE EDITOR ======
    let cropCanvas = null;
    let trueOriginalCanvas = null;  // Hidden canvas: COMPLETELY original, never changes
    let lastSavedCanvas = null;     // Hidden canvas: last saved version
    let originalImageObj = null;
    let cropRatio = null;
    let currentCropBox = { x: 0, y: 0, width: 0, height: 0 };

    function openImageEditor() {
        const preview = document.getElementById('imagePreview');
        if (!preview.src) {
            alert('Please select an image first');
            return;
        }

        document.getElementById('imageEditorModal').classList.remove('hidden');
        cropCanvas = document.getElementById('cropCanvas');
        const ctx = cropCanvas.getContext('2d');

        const img = new Image();
        img.crossOrigin = "anonymous";
        img.onload = function() {
            originalImageObj = img;
            cropCanvas.width = Math.min(img.width, 800);
            cropCanvas.height = Math.min(img.height, 600);
            
            // Scale image to fit canvas while maintaining aspect ratio
            const scale = Math.min(cropCanvas.width / img.width, cropCanvas.height / img.height);
            const scaledWidth = img.width * scale;
            const scaledHeight = img.height * scale;
            const x = (cropCanvas.width - scaledWidth) / 2;
            const y = (cropCanvas.height - scaledHeight) / 2;
            
            ctx.drawImage(img, x, y, scaledWidth, scaledHeight);
            
            // Initialize crop box to full image
            currentCropBox = { x: x, y: y, width: scaledWidth, height: scaledHeight };
            
            // Create hidden canvases to store the completely original image
            if (!trueOriginalCanvas) {
                trueOriginalCanvas = document.createElement('canvas');
            }
            trueOriginalCanvas.width = cropCanvas.width;
            trueOriginalCanvas.height = cropCanvas.height;
            const origCtx = trueOriginalCanvas.getContext('2d');
            origCtx.drawImage(cropCanvas, 0, 0);
            
            // Create hidden canvas for last saved version (initially same as original)
            if (!lastSavedCanvas) {
                lastSavedCanvas = document.createElement('canvas');
            }
            lastSavedCanvas.width = cropCanvas.width;
            lastSavedCanvas.height = cropCanvas.height;
            const savedCtx = lastSavedCanvas.getContext('2d');
            savedCtx.drawImage(cropCanvas, 0, 0);
            
            // Reset sliders and ratio
            document.getElementById('rotateSlider').value = 0;
            document.getElementById('brightnessSlider').value = 100;
            document.getElementById('contrastSlider').value = 100;
            cropRatio = null;
        };
        img.src = preview.src;
    }

    function closeImageEditor() {
        document.getElementById('imageEditorModal').classList.add('hidden');
    }

    function resetImageEditor() {
        if (!cropCanvas || !lastSavedCanvas) return;

        const ctx = cropCanvas.getContext('2d');
        
        // Restore from the last saved version
        ctx.clearRect(0, 0, cropCanvas.width, cropCanvas.height);
        ctx.drawImage(lastSavedCanvas, 0, 0);
        
        // Reset all sliders
        document.getElementById('rotateSlider').value = 0;
        document.getElementById('brightnessSlider').value = 100;
        document.getElementById('contrastSlider').value = 100;
        cropRatio = null;
        
        // Reset crop box
        const img = originalImageObj;
        if (img) {
            const scale = Math.min(cropCanvas.width / img.width, cropCanvas.height / img.height);
            const scaledWidth = img.width * scale;
            const scaledHeight = img.height * scale;
            const x = (cropCanvas.width - scaledWidth) / 2;
            const y = (cropCanvas.height - scaledHeight) / 2;
            currentCropBox = { x: x, y: y, width: scaledWidth, height: scaledHeight };
        }
        
        // Redraw canvas with the reset values
        redrawCanvas();
    }

    function resetToOriginal() {
        if (!cropCanvas || !trueOriginalCanvas || !originalImageObj) return;

        const ctx = cropCanvas.getContext('2d');
        
        // Draw the COMPLETELY original image (never modified)
        ctx.clearRect(0, 0, cropCanvas.width, cropCanvas.height);
        ctx.drawImage(trueOriginalCanvas, 0, 0);
        
        // Reset all sliders to default
        document.getElementById('rotateSlider').value = 0;
        document.getElementById('brightnessSlider').value = 100;
        document.getElementById('contrastSlider').value = 100;
        cropRatio = null;
        
        // Reset crop box to full image
        const scale = Math.min(cropCanvas.width / originalImageObj.width, cropCanvas.height / originalImageObj.height);
        const scaledWidth = originalImageObj.width * scale;
        const scaledHeight = originalImageObj.height * scale;
        const x = (cropCanvas.width - scaledWidth) / 2;
        const y = (cropCanvas.height - scaledHeight) / 2;
        currentCropBox = { x: x, y: y, width: scaledWidth, height: scaledHeight };
        
        // Redraw canvas with the reset values
        redrawCanvas();
    }

    function setCropRatio(ratio) {
        cropRatio = ratio;
        if (ratio && originalImageObj) {
            // Calculate new crop box with the specified ratio
            const maxWidth = currentCropBox.width;
            const maxHeight = currentCropBox.height;
            
            let newWidth, newHeight;
            if (ratio === null) {
                // Free ratio - use full crop box
                newWidth = maxWidth;
                newHeight = maxHeight;
            } else {
                // Calculate dimensions maintaining aspect ratio
                const currentRatio = maxWidth / maxHeight;
                if (currentRatio > ratio) {
                    newHeight = maxHeight;
                    newWidth = maxHeight * ratio;
                } else {
                    newWidth = maxWidth;
                    newHeight = maxWidth / ratio;
                }
            }
            
            // Center the crop box
            currentCropBox.width = newWidth;
            currentCropBox.height = newHeight;
            currentCropBox.x = (cropCanvas.width - newWidth) / 2;
            currentCropBox.y = (cropCanvas.height - newHeight) / 2;
            
            redrawCanvas();
        }
    }

    function redrawCanvas() {
        if (!cropCanvas || !originalImageObj) return;

        const ctx = cropCanvas.getContext('2d');
        const rotation = parseInt(document.getElementById('rotateSlider').value);
        const brightness = parseInt(document.getElementById('brightnessSlider').value) / 100;
        const contrast = parseInt(document.getElementById('contrastSlider').value) / 100;

        // Clear canvas
        ctx.fillStyle = '#f0f0f0';
        ctx.fillRect(0, 0, cropCanvas.width, cropCanvas.height);

        // Apply transformations
        ctx.save();
        ctx.translate(cropCanvas.width / 2, cropCanvas.height / 2);
        ctx.rotate(rotation * Math.PI / 180);
        ctx.filter = `brightness(${brightness}) contrast(${contrast})`;

        // Scale image to fit
        const scale = Math.min(cropCanvas.width / originalImageObj.width, cropCanvas.height / originalImageObj.height);
        const scaledWidth = originalImageObj.width * scale;
        const scaledHeight = originalImageObj.height * scale;
        
        ctx.drawImage(originalImageObj, -scaledWidth / 2, -scaledHeight / 2, scaledWidth, scaledHeight);
        ctx.restore();

        // Draw crop guide
        ctx.strokeStyle = '#004d99';
        ctx.lineWidth = 2;
        ctx.setLineDash([5, 5]);
        ctx.strokeRect(currentCropBox.x, currentCropBox.y, currentCropBox.width, currentCropBox.height);
        ctx.setLineDash([]);
    }

    ['rotateSlider', 'brightnessSlider', 'contrastSlider'].forEach(id => {
        document.getElementById(id).addEventListener('input', redrawCanvas);
    });

    function saveEditedImage() {
        if (!cropCanvas || !originalImageObj) return;

        // Create a new canvas for the cropped/edited image
        const newCanvas = document.createElement('canvas');
        newCanvas.width = currentCropBox.width;
        newCanvas.height = currentCropBox.height;
        const newCtx = newCanvas.getContext('2d');

        // Apply all transformations to the new canvas
        const rotation = parseInt(document.getElementById('rotateSlider').value);
        const brightness = parseInt(document.getElementById('brightnessSlider').value) / 100;
        const contrast = parseInt(document.getElementById('contrastSlider').value) / 100;

        newCtx.save();
        newCtx.translate(newCanvas.width / 2, newCanvas.height / 2);
        newCtx.rotate(rotation * Math.PI / 180);
        newCtx.filter = `brightness(${brightness}) contrast(${contrast})`;

        const scale = Math.min(cropCanvas.width / originalImageObj.width, cropCanvas.height / originalImageObj.height);
        const scaledWidth = originalImageObj.width * scale;
        const scaledHeight = originalImageObj.height * scale;

        newCtx.drawImage(originalImageObj, -scaledWidth / 2, -scaledHeight / 2, scaledWidth, scaledHeight);
        newCtx.restore();

        // Update the preview with edited image
        const editedImageData = newCanvas.toDataURL('image/jpeg', 0.95);
        document.getElementById('imagePreview').src = editedImageData;
        
        // Store edited image data in hidden field for form submission
        document.getElementById('originalImageData').value = editedImageData;
        
        // Clear the file input so form won't try to upload the old file
        const imageInput = document.getElementById('imageInput');
        imageInput.value = '';
        
        // Update the last saved canvas with this new edited state
        // (so next time "Reset to Last Save" is clicked, it will restore to this version)
        const savedCtx = lastSavedCanvas.getContext('2d');
        savedCtx.clearRect(0, 0, lastSavedCanvas.width, lastSavedCanvas.height);
        savedCtx.drawImage(cropCanvas, 0, 0);
        
        closeImageEditor();
    }
</script>
