<?php
/**
 * Back Office â€” Article Create/Edit Form
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

            <!-- Edit Image button (shows after an image is loaded) -->
            <div id="editImageBtnWrap" class="mt-3 <?= empty($post['image_url']) ? 'hidden' : '' ?>">
                <button type="button" id="openImageEditorBtn"
                        class="flex items-center gap-2 px-4 py-2 rounded-xl border border-blue-200 bg-blue-50 text-blue-700 text-xs font-bold hover:bg-blue-100 hover:border-blue-300 transition-all shadow-sm shadow-blue-100">
                    <span class="material-symbols-outlined text-sm">tune</span>
                    Edit Image
                </button>
            </div>
            <!-- Hidden input to store edited image data -->
            <input type="hidden" name="edited_image_data" id="editedImageData"/>

            <!-- Fallback URL field for external images -->
            <div class="mt-4">
                <label class="block">
                    <span class="text-xs font-bold text-on-surface-variant flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-xs">link</span>
                        Or use external image URL
                    </span>
                    <input type="text" name="image_url" id="imageUrlField"
                           value="<?= htmlspecialchars($post['image_url'] ?? '') ?>"
                           placeholder="https://example.com/image.jpg"
                           class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-lg text-on-surface focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all text-sm"/>
                </label>
            </div>

            <script>
            function showUploadToast(msg, type) {
                const isError = type === 'error';
                const t = document.createElement('div');
                t.style.cssText = 'position:fixed;top:24px;right:24px;z-index:99999;display:flex;align-items:center;gap:12px;padding:14px 18px;background:#fff;border-radius:16px;box-shadow:0 8px 32px rgba(0,77,153,.14);border:1px solid ' + (isError ? '#fecaca' : '#bfdbfe') + ';font-size:13px;font-weight:500;color:#1e293b;max-width:340px;animation:slideDownIn .3s ease-out';
                t.innerHTML = '<div style="width:34px;height:34px;border-radius:10px;background:' + (isError ? '#fef2f2' : '#eff6ff') + ';display:flex;align-items:center;justify-content:center;flex-shrink:0"><span class="material-symbols-outlined" style="font-size:18px;color:' + (isError ? '#ef4444' : '#004d99') + '">' + (isError ? 'error' : 'info') + '</span></div><span style="flex:1">' + msg + '</span><button onclick="this.parentElement.remove()" style="padding:4px;border-radius:8px;background:none;border:none;cursor:pointer;color:#94a3b8;line-height:0"><span class="material-symbols-outlined" style="font-size:16px">close</span></button>';
                document.body.appendChild(t);
                setTimeout(() => { t.style.transition = 'opacity .3s'; t.style.opacity = '0'; setTimeout(() => t.remove(), 320); }, 4100);
            }
            (function() {
                const dropZone   = document.getElementById('imageDropZone');
                const imageInput = document.getElementById('imageInput');
                const imagePreview = document.getElementById('imagePreview');
                const imagePreviewContainer = document.getElementById('imagePreviewContainer');
                const uploadPrompt = document.getElementById('uploadPrompt');
                const imageUrlField = document.getElementById('imageUrlField');

                // Drag and drop â€” prevent browser default open
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
                        showUploadToast('Please select a valid image file (JPG, PNG, or WebP).', 'error');
                        return;
                    }
                    if (file.size > 5 * 1024 * 1024) {
                        showUploadToast('Image size must be less than 5MB.', 'error');
                        return;
                    }

                    // Show preview
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        imagePreview.src = ev.target.result;
                        imagePreviewContainer.style.display = 'block';
                        uploadPrompt.style.display = 'none';
                        imageUrlField.value = ''; // clear URL field
                        document.getElementById('editImageBtnWrap')?.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                }

                window.removeImage = function() {
                    imageInput.value = '';
                    imagePreviewContainer.style.display = 'none';
                    uploadPrompt.style.display = 'block';
                    imagePreview.src = '';
                    imageUrlField.value = '';
                    document.getElementById('editedImageData').value = '';
                    document.getElementById('editImageBtnWrap')?.classList.add('hidden');
                };
            })();
            </script>
        </div>

        <!-- Content -->
        <div class="bg-surface-container-lowest rounded-xl p-6 shadow-sm">
            <label class="block">
                <span class="text-sm font-bold text-on-surface flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-sm text-primary">edit_note</span>
                    Article Content <span class="text-error">*</span>
                </span>
                <textarea id="contentTextarea" name="contenu" required rows="16"
                          placeholder="Write your article content here..."
                          class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-lg text-on-surface focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all leading-relaxed resize-y"><?= htmlspecialchars($post['contenu'] ?? '') ?></textarea>
                <div class="flex items-center justify-between mt-2">
                    <span class="text-xs text-on-surface-variant" id="charCount">0 characters</span>
                    <span class="text-xs text-on-surface-variant" id="readTime">~0 min read</span>
                </div>
            </label>

            <!-- AI Writing Assistant -->
            <div class="mt-5 rounded-xl overflow-hidden border border-blue-200/70 shadow-sm">
                <!-- Header -->
                <div class="flex items-center justify-between px-5 py-3 bg-gradient-to-r from-blue-50 to-sky-50 border-b border-blue-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-blue-600 to-sky-500 flex items-center justify-center shadow-sm shadow-blue-200">
                            <span class="material-symbols-outlined text-white text-[15px]">auto_fix_high</span>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-blue-800 leading-none">AI Writing Assistant</p>
                            <p class="text-[10px] text-blue-400 mt-0.5">Select text in the editor, then click Rephrase</p>
                        </div>
                    </div>
                    <button id="rephraseBtn" type="button" disabled
                            class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold
                                   bg-blue-100 text-blue-300 cursor-not-allowed
                                   transition-all duration-200">
                        <span class="material-symbols-outlined text-[16px]">auto_awesome</span>
                        <span id="rephraseBtnLabel">Rephrase Selected</span>
                    </button>
                </div>

                <!-- Result panel (hidden until used) -->
                <div id="rephrasePanel" class="hidden bg-white">

                    <!-- Loading -->
                    <div id="rephraseLoading" class="flex items-center gap-3 px-5 py-5">
                        <div class="flex gap-1.5">
                            <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#004d99;animation:aiDotBO 1.2s ease-in-out infinite;animation-delay:0ms"></span>
                            <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#0284c7;animation:aiDotBO 1.2s ease-in-out infinite;animation-delay:180ms"></span>
                            <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#38bdf8;animation:aiDotBO 1.2s ease-in-out infinite;animation-delay:360ms"></span>
                        </div>
                        <span class="text-sm text-slate-500 font-medium">Rephrasing with AIâ€¦</span>
                    </div>

                    <!-- Result content -->
                    <div id="rephraseResult" class="hidden px-5 py-5 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Original -->
                            <div>
                                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2">Original</p>
                                <div class="bg-slate-50 border border-slate-100 rounded-lg px-3 py-3 max-h-32 overflow-y-auto">
                                    <p id="rephraseOriginal" class="text-xs text-slate-400 italic leading-relaxed"></p>
                                </div>
                            </div>
                            <!-- Rephrased -->
                            <div>
                                <p class="text-[10px] font-extrabold text-blue-600 uppercase tracking-widest mb-2">Rephrased</p>
                                <div class="bg-blue-50 border border-blue-200 rounded-lg px-3 py-3 max-h-32 overflow-y-auto">
                                    <p id="rephraseOutput" class="text-xs text-slate-800 leading-relaxed"></p>
                                </div>
                            </div>
                        </div>
                        <!-- Action buttons -->
                        <div class="flex items-center gap-2 pt-1">
                            <button id="rephraseApply" type="button"
                                    class="flex items-center gap-1.5 px-4 py-2 bg-primary text-white text-xs font-bold rounded-lg hover:bg-blue-700 transition-colors shadow-sm shadow-blue-200">
                                <span class="material-symbols-outlined text-[15px]">check_circle</span> Apply
                            </button>
                            <button id="rephraseRetry" type="button"
                                    class="flex items-center gap-1.5 px-4 py-2 text-blue-600 text-xs font-bold rounded-lg border border-blue-200 hover:bg-blue-50 transition-colors">
                                <span class="material-symbols-outlined text-[15px]">refresh</span> Try Again
                            </button>
                            <button id="rephraseDismiss" type="button"
                                    class="ml-auto px-3 py-2 text-slate-400 text-xs font-medium rounded-lg hover:bg-slate-50 transition-colors">
                                Dismiss
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
        @keyframes aiDotBO { 0%,80%,100%{transform:translateY(0);opacity:.4} 40%{transform:translateY(-5px);opacity:1} }
        @keyframes boCursor { 0%,100%{opacity:1} 50%{opacity:0} }
        .bo-cursor { display:inline-block;width:2px;height:.85em;background:#004d99;margin-left:2px;vertical-align:text-bottom;animation:boCursor .7s step-end infinite; }
        /* Image editor modal */
        #imageEditorModal { backdrop-filter:blur(6px); }
        .editor-tool-btn { display:flex;flex-direction:column;align-items:center;gap:4px;padding:8px 12px;border-radius:10px;font-size:11px;font-weight:700;color:#475569;background:transparent;border:1px solid transparent;cursor:pointer;transition:all .15s; }
        .editor-tool-btn:hover { background:#eff6ff;border-color:#bfdbfe;color:#1e40af; }
        .editor-tool-btn.active { background:#dbeafe;border-color:#93c5fd;color:#1d4ed8; }
        .filter-thumb { width:60px;height:44px;border-radius:8px;object-fit:cover;cursor:pointer;border:2px solid transparent;transition:all .2s; }
        .filter-thumb.active { border-color:#0284c7;box-shadow:0 0 0 3px #bae6fd; }
        #cropperContainer { max-height:420px;display:flex;align-items:center;justify-content:center;background:#0f172a; }
        #cropperContainer img { max-height:420px;display:block; }
        </style>

        <script>
        (function() {
            const textarea     = document.getElementById('contentTextarea');
            const rephraseBtn  = document.getElementById('rephraseBtn');
            const panel        = document.getElementById('rephrasePanel');
            const loadingEl    = document.getElementById('rephraseLoading');
            const resultEl     = document.getElementById('rephraseResult');
            const originalEl   = document.getElementById('rephraseOriginal');
            const outputEl     = document.getElementById('rephraseOutput');
            const applyBtn     = document.getElementById('rephraseApply');
            const retryBtn     = document.getElementById('rephraseRetry');
            const dismissBtn   = document.getElementById('rephraseDismiss');

            let selStart = 0, selEnd = 0, selectedText = '', lastRephrased = '';

            // Enable button when text is selected in the textarea
            function checkSelection() {
                const s = textarea.selectionStart;
                const e = textarea.selectionEnd;
                const hasSelection = s !== e && e - s > 5;
                rephraseBtn.disabled = !hasSelection;
                rephraseBtn.className = rephraseBtn.className
                    .replace(/cursor-not-allowed|cursor-pointer/g, '')
                    .trim();
                if (hasSelection) {
                    rephraseBtn.classList.add('cursor-pointer');
                    rephraseBtn.className = rephraseBtn.className
                        .replace('bg-blue-100 text-blue-300', '')
                        .trim();
                    rephraseBtn.classList.add('bg-primary', 'text-white', 'hover:bg-blue-700', 'shadow-sm');
                } else {
                    rephraseBtn.classList.remove('bg-primary', 'text-white', 'hover:bg-blue-700', 'shadow-sm');
                    rephraseBtn.classList.add('bg-blue-100', 'text-blue-300', 'cursor-not-allowed');
                }
                if (hasSelection) { selStart = s; selEnd = e; selectedText = textarea.value.slice(s, e); }
            }

            textarea.addEventListener('mouseup', checkSelection);
            textarea.addEventListener('keyup', checkSelection);
            textarea.addEventListener('select', checkSelection);

            rephraseBtn.addEventListener('click', function() {
                if (this.disabled) return;
                panel.classList.remove('hidden');
                fetchRephrase(selectedText);
            });

            retryBtn.addEventListener('click', () => fetchRephrase(selectedText));

            dismissBtn.addEventListener('click', () => {
                panel.classList.add('hidden');
                resultEl.classList.add('hidden');
                outputEl.innerHTML = '';
            });

            applyBtn.addEventListener('click', () => {
                if (!lastRephrased) return;
                const val = textarea.value;
                textarea.value = val.slice(0, selStart) + lastRephrased + val.slice(selEnd);
                selEnd = selStart + lastRephrased.length;
                panel.classList.add('hidden');
                resultEl.classList.add('hidden');
            });

            async function fetchRephrase(text) {
                loadingEl.classList.remove('hidden');
                resultEl.classList.add('hidden');
                outputEl.innerHTML = '';

                try {
                    const res  = await fetch('/integration/magazine/admin/rephrase', {
                        method:  'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body:    JSON.stringify({ text })
                    });
                    const data = await res.json();

                    loadingEl.classList.add('hidden');
                    resultEl.classList.remove('hidden');
                    originalEl.textContent = text.length > 300 ? text.slice(0, 300) + 'â€¦' : text;

                    if (!data.success) {
                        outputEl.textContent = data.error || 'Error occurred.';
                        lastRephrased = '';
                        return;
                    }

                    lastRephrased = data.rephrased;
                    typewriteBO(outputEl, data.rephrased, 14);

                } catch(e) {
                    loadingEl.classList.add('hidden');
                    resultEl.classList.remove('hidden');
                    originalEl.textContent = text.slice(0, 200);
                    outputEl.textContent = 'AI service unavailable.';
                }
            }

            function typewriteBO(element, text, speed) {
                let i = 0;
                const textNode = document.createTextNode('');
                const cursor   = document.createElement('span');
                cursor.className = 'bo-cursor';
                element.innerHTML = '';
                element.appendChild(textNode);
                element.appendChild(cursor);
                const iv = setInterval(() => {
                    textNode.textContent += text[i++];
                    if (i >= text.length) { clearInterval(iv); cursor.remove(); }
                }, speed);
            }
        })();
        </script>

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

<!-- ============================================================ -->
<!-- Image Editor Modal                                             -->
<!-- ============================================================ -->
<div id="imageEditorModal"
     class="fixed inset-0 z-[9999] bg-black/60 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl flex flex-col overflow-hidden max-h-[95vh]">

    <!-- Modal header -->
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 flex-shrink-0">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-600 to-sky-500 flex items-center justify-center shadow-md shadow-blue-200">
          <span class="material-symbols-outlined text-white text-[18px]">tune</span>
        </div>
        <div>
          <p class="font-bold text-slate-800 text-sm leading-none">Image Editor</p>
          <p class="text-[11px] text-slate-400 mt-0.5">Crop, adjust, and apply filters</p>
        </div>
      </div>
      <button id="closeImageEditor" type="button"
              class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-all">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>

    <!-- Cropper canvas -->
    <div id="cropperContainer" class="flex-shrink-0">
      <img id="editorImage" src="" alt="edit"/>
    </div>

    <!-- Toolbar -->
    <div class="flex-shrink-0 border-t border-slate-100 bg-slate-50 px-4 py-3 space-y-3">

      <!-- Transform tools -->
      <div class="flex items-center gap-1 flex-wrap">
        <button class="editor-tool-btn" type="button" id="btnCropMode">
          <span class="material-symbols-outlined text-lg">crop</span>Crop
        </button>
        <button class="editor-tool-btn" type="button" onclick="editorCropper?.rotate(-90)">
          <span class="material-symbols-outlined text-lg">rotate_left</span>Rotate L
        </button>
        <button class="editor-tool-btn" type="button" onclick="editorCropper?.rotate(90)">
          <span class="material-symbols-outlined text-lg">rotate_right</span>Rotate R
        </button>
        <button class="editor-tool-btn" type="button" onclick="editorCropper?.scaleX(-editorCropper.getData().scaleX||1)">
          <span class="material-symbols-outlined text-lg">flip</span>Flip H
        </button>
        <button class="editor-tool-btn" type="button" onclick="editorCropper?.scaleY(-editorCropper.getData().scaleY||1)">
          <span class="material-symbols-outlined text-lg" style="transform:rotate(90deg)">flip</span>Flip V
        </button>
        <button class="editor-tool-btn" type="button" onclick="editorCropper?.reset()">
          <span class="material-symbols-outlined text-lg">restart_alt</span>Reset
        </button>

        <!-- Brightness & Contrast sliders -->
        <div class="ml-auto flex items-center gap-4">
          <label class="flex items-center gap-1.5 text-xs font-bold text-slate-500">
            <span class="material-symbols-outlined text-sm">light_mode</span>
            <input id="brightnessSlider" type="range" min="-100" max="100" value="0"
                   class="w-20 accent-blue-600" title="Brightness"/>
          </label>
          <label class="flex items-center gap-1.5 text-xs font-bold text-slate-500">
            <span class="material-symbols-outlined text-sm">contrast</span>
            <input id="contrastSlider" type="range" min="-100" max="100" value="0"
                   class="w-20 accent-sky-500" title="Contrast"/>
          </label>
        </div>
      </div>

      <!-- CSS Filter presets -->
      <div class="flex items-center gap-2 overflow-x-auto pb-1">
        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest flex-shrink-0">Filters:</span>
        <button class="editor-tool-btn active flex-shrink-0 !flex-row !p-1.5 !gap-2 text-[11px]"
                type="button" data-filter="" id="filterNone">None</button>
        <button class="editor-tool-btn flex-shrink-0 !flex-row !p-1.5 !gap-2 text-[11px]"
                type="button" data-filter="grayscale(100%)">B&W</button>
        <button class="editor-tool-btn flex-shrink-0 !flex-row !p-1.5 !gap-2 text-[11px]"
                type="button" data-filter="sepia(80%)">Sepia</button>
        <button class="editor-tool-btn flex-shrink-0 !flex-row !p-1.5 !gap-2 text-[11px]"
                type="button" data-filter="saturate(180%) contrast(110%)">Vivid</button>
        <button class="editor-tool-btn flex-shrink-0 !flex-row !p-1.5 !gap-2 text-[11px]"
                type="button" data-filter="contrast(120%) brightness(110%)">Crisp</button>
        <button class="editor-tool-btn flex-shrink-0 !flex-row !p-1.5 !gap-2 text-[11px]"
                type="button" data-filter="hue-rotate(30deg) saturate(140%)">Cool</button>
        <button class="editor-tool-btn flex-shrink-0 !flex-row !p-1.5 !gap-2 text-[11px]"
                type="button" data-filter="sepia(40%) saturate(160%) hue-rotate(-20deg)">Warm</button>
        <button class="editor-tool-btn flex-shrink-0 !flex-row !p-1.5 !gap-2 text-[11px]"
                type="button" data-filter="saturate(0%) brightness(110%) contrast(90%)">Matte</button>
      </div>
    </div>

    <!-- Modal footer -->
    <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 flex-shrink-0 bg-white">
      <button type="button" id="cancelImageEditor"
              class="px-5 py-2.5 text-sm font-semibold text-slate-500 bg-slate-100 rounded-xl hover:bg-slate-200 transition-colors">
        Cancel
      </button>
      <button type="button" id="applyImageEdit"
              class="flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-blue-600 to-sky-500 text-white text-sm font-bold rounded-xl hover:opacity-90 transition-opacity shadow-md shadow-blue-200">
        <span class="material-symbols-outlined text-sm">check_circle</span>
        Apply & Use This Image
      </button>
    </div>
  </div>
</div>

<!-- Cropper.js CDN (loaded only on this page) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>

<script>
(function() {
    let editorCropper = null;
    window.editorCropper = null;

    const modal          = document.getElementById('imageEditorModal');
    const editorImg      = document.getElementById('editorImage');
    const openBtn        = document.getElementById('openImageEditorBtn');
    const closeBtn       = document.getElementById('closeImageEditor');
    const cancelBtn      = document.getElementById('cancelImageEditor');
    const applyBtn       = document.getElementById('applyImageEdit');
    const brightSlider   = document.getElementById('brightnessSlider');
    const contrastSlider = document.getElementById('contrastSlider');

    let cssFilter = '';
    let brightnessVal = 0, contrastVal = 0;

    // Modern toast instead of browser alert()
    function showEditorToast(msg, type) {
        const isError = type === 'error';
        const t = document.createElement('div');
        t.style.cssText = 'position:fixed;top:24px;right:24px;z-index:99999;display:flex;align-items:center;gap:12px;padding:14px 18px;background:#fff;border-radius:16px;box-shadow:0 8px 32px rgba(0,77,153,.14);border:1px solid ' + (isError ? '#fecaca' : '#bfdbfe') + ';font-size:13px;font-weight:500;color:#1e293b;max-width:340px;animation:slideDownIn .3s ease-out';
        t.innerHTML = `
            <div style="width:34px;height:34px;border-radius:10px;background:${isError ? '#fef2f2' : '#eff6ff'};display:flex;align-items:center;justify-content:center;flex-shrink:0">
              <span class="material-symbols-outlined" style="font-size:18px;color:${isError ? '#ef4444' : '#004d99'}">${isError ? 'error' : 'info'}</span>
            </div>
            <span style="flex:1">${msg}</span>
            <button onclick="this.parentElement.remove()" style="padding:4px;border-radius:8px;background:none;border:none;cursor:pointer;color:#94a3b8;line-height:0">
              <span class="material-symbols-outlined" style="font-size:16px">close</span>
            </button>`;
        document.body.appendChild(t);
        setTimeout(() => t.style.transition = 'opacity .3s', 3800);
        setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 320); }, 4100);
    }

    // Apply filter to both the img element AND the .cropper-canvas wrapper for reliable live preview
    function buildFilter() {
        const parts = [];
        if (brightnessVal !== 0) parts.push(`brightness(${1 + brightnessVal / 100})`);
        if (contrastVal   !== 0) parts.push(`contrast(${1 + contrastVal / 100})`);
        if (cssFilter) parts.push(cssFilter);
        const filterStr = parts.join(' ') || '';
        editorImg.style.filter = filterStr;
        // Also target the cropper canvas wrapper so the preview always reflects changes
        const cropCanvas = document.querySelector('#cropperContainer .cropper-canvas');
        if (cropCanvas) cropCanvas.style.filter = filterStr;
    }

    brightSlider.addEventListener('input', () => { brightnessVal = +brightSlider.value; buildFilter(); });
    contrastSlider.addEventListener('input', () => { contrastVal  = +contrastSlider.value; buildFilter(); });

    // Filter presets
    document.querySelectorAll('[data-filter]').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('[data-filter]').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            cssFilter = this.dataset.filter;
            buildFilter();
        });
    });

    // Crop mode toggle
    document.getElementById('btnCropMode')?.addEventListener('click', function() {
        if (!editorCropper) return;
        this.classList.toggle('active');
        if (this.classList.contains('active')) editorCropper.enable();
        else editorCropper.disable();
    });

    function openEditor() {
        // Determine source image: edited data, file preview, or URL field
        const editedData = document.getElementById('editedImageData').value;
        const previewSrc = document.getElementById('imagePreview').src;
        const urlSrc     = document.getElementById('imageUrlField').value;

        const src = (editedData && editedData.startsWith('data:')) ? editedData
                  : (previewSrc && !previewSrc.includes('undefined')) ? previewSrc
                  : urlSrc;

        if (!src) { showEditorToast('Please add an image first.', 'error'); return; }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';

        editorImg.src = src;
        editorImg.style.filter = '';

        // Reset controls
        brightSlider.value = 0; brightnessVal = 0;
        contrastSlider.value = 0; contrastVal = 0;
        document.querySelectorAll('[data-filter]').forEach(b => b.classList.remove('active'));
        document.getElementById('filterNone')?.classList.add('active');
        cssFilter = '';

        editorImg.onload = () => {
            if (editorCropper) { editorCropper.destroy(); editorCropper = null; }
            editorCropper = new Cropper(editorImg, {
                viewMode:      2,
                autoCrop:      false,
                movable:       true,
                zoomable:      true,
                rotatable:     true,
                scalable:      true,
                responsive:    true,
                checkCrossOrigin: false,
            });
            window.editorCropper = editorCropper;
        };
        // If already loaded (same src)
        if (editorImg.complete && editorImg.naturalWidth) editorImg.onload();
    }

    function closeEditor() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
        if (editorCropper) { editorCropper.destroy(); editorCropper = null; window.editorCropper = null; }
    }

    openBtn?.addEventListener('click', openEditor);
    closeBtn?.addEventListener('click', closeEditor);
    cancelBtn?.addEventListener('click', closeEditor);
    modal?.addEventListener('click', e => { if (e.target === modal) closeEditor(); });

    applyBtn?.addEventListener('click', () => {
        if (!editorCropper) return;

        const canvas = editorCropper.getCroppedCanvas({ maxWidth: 1920, maxHeight: 1080, fillColor: '#fff' });
        if (!canvas) { closeEditor(); return; }

        // Apply CSS filters by drawing through an off-screen canvas
        const finalCanvas = document.createElement('canvas');
        finalCanvas.width  = canvas.width;
        finalCanvas.height = canvas.height;
        const ctx = finalCanvas.getContext('2d');

        const filterParts = [];
        if (brightnessVal !== 0) filterParts.push(`brightness(${1 + brightnessVal / 100})`);
        if (contrastVal   !== 0) filterParts.push(`contrast(${1 + contrastVal / 100})`);
        if (cssFilter) filterParts.push(cssFilter);
        if (filterParts.length) ctx.filter = filterParts.join(' ');

        ctx.drawImage(canvas, 0, 0);

        const dataUrl = finalCanvas.toDataURL('image/jpeg', 0.9);

        // Store in hidden input and update preview
        document.getElementById('editedImageData').value = dataUrl;
        const preview = document.getElementById('imagePreview');
        preview.src = dataUrl;
        document.getElementById('imagePreviewContainer').style.display = 'block';
        document.getElementById('uploadPrompt').style.display = 'none';
        document.getElementById('imageInput').value = '';

        closeEditor();
    });
})();
</script>

<!-- ============================================================ -->
<!-- Unsaved Changes Modal                                          -->
<!-- ============================================================ -->
<div id="unsavedModal"
     class="fixed inset-0 z-[10000] bg-black/50 backdrop-blur-sm hidden items-center justify-center p-4">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden" style="animation:slideDownIn .25s ease-out">
    <div class="px-6 pt-6 pb-4">
      <div class="flex items-start gap-4">
        <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center flex-shrink-0 shadow-sm">
          <span class="material-symbols-outlined text-amber-500 text-2xl"
                style="font-variation-settings:'FILL' 1">warning</span>
        </div>
        <div class="pt-0.5">
          <p class="font-bold text-slate-800 text-base leading-snug">Unsaved Changes</p>
          <p class="text-sm text-slate-500 mt-1 leading-relaxed">
            You have unsaved changes that will be lost if you leave this page.
          </p>
        </div>
      </div>
    </div>
    <div class="flex gap-3 px-6 pb-6">
      <button id="unsavedStay" type="button"
              class="flex-1 py-2.5 text-sm font-bold text-blue-700 bg-blue-50 border border-blue-200 rounded-xl hover:bg-blue-100 transition-colors">
        Continue Editing
      </button>
      <button id="unsavedLeave" type="button"
              class="flex-1 py-2.5 text-sm font-bold text-white bg-slate-700 rounded-xl hover:bg-slate-800 transition-colors">
        Discard &amp; Leave
      </button>
    </div>
  </div>
</div>

<script>
// ============================================================
// Real-time field validation
// ============================================================
(function () {
    const titleInput  = document.querySelector('[name="titre"]');
    const contentArea = document.getElementById('contentTextarea');
    if (!titleInput || !contentArea) return;

    function setFieldState(input, state, message) {
        const wrap = input.closest('label') || input.parentNode;
        wrap.querySelectorAll('.field-hint').forEach(n => n.remove());

        input.classList.remove(
            'border-red-300','focus:ring-red-100','focus:border-red-300',
            'border-green-300','focus:ring-green-100','focus:border-green-400'
        );

        if (state === 'error') {
            input.classList.add('border-red-300','focus:ring-red-100','focus:border-red-300');
            const hint = document.createElement('p');
            hint.className = 'field-hint flex items-center gap-1 text-xs text-red-500 mt-1.5 font-medium';
            hint.innerHTML = `<span class="material-symbols-outlined" style="font-size:13px">error</span>${message}`;
            input.after(hint);
        } else if (state === 'ok') {
            input.classList.add('border-green-300','focus:ring-green-100','focus:border-green-400');
        }
    }

    function validateTitle() {
        const v = titleInput.value.trim();
        if (!v)           { setFieldState(titleInput, 'error', 'Title is required.'); return false; }
        if (v.length < 5) { setFieldState(titleInput, 'error', 'Title must be at least 5 characters.'); return false; }
        setFieldState(titleInput, 'ok'); return true;
    }

    function validateContent() {
        const v = contentArea.value.trim();
        if (!v)            { setFieldState(contentArea, 'error', 'Article content is required.'); return false; }
        if (v.length < 20) { setFieldState(contentArea, 'error', 'Content is too short (minimum 20 characters).'); return false; }
        setFieldState(contentArea, 'ok'); return true;
    }

    // Validate on blur
    titleInput.addEventListener('blur',  validateTitle);
    contentArea.addEventListener('blur', validateContent);

    // Live feedback while typing — debounced
    let tTimer, cTimer;
    titleInput.addEventListener('input', () => {
        clearTimeout(tTimer);
        tTimer = setTimeout(() => {
            if (titleInput.value.trim().length >= 5) setFieldState(titleInput, 'ok');
            else if (titleInput.closest('label')?.querySelector('.field-hint')) validateTitle();
        }, 350);
    });
    contentArea.addEventListener('input', () => {
        clearTimeout(cTimer);
        cTimer = setTimeout(() => {
            if (contentArea.value.trim().length >= 20) setFieldState(contentArea, 'ok');
            else if (contentArea.parentNode.querySelector('.field-hint')) validateContent();
        }, 350);
    });

    // Block submit if invalid
    document.getElementById('articleForm').addEventListener('submit', function (e) {
        const t = validateTitle();
        const c = validateContent();
        if (!t || !c) {
            e.preventDefault();
            const first = document.querySelector('.field-hint');
            if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }, true); // capture phase so it runs before other handlers
})();

// ============================================================
// Unsaved-changes guard
// ============================================================
(function () {
    let isDirty    = false;
    let pendingNav = null;

    const modal    = document.getElementById('unsavedModal');
    const stayBtn  = document.getElementById('unsavedStay');
    const leaveBtn = document.getElementById('unsavedLeave');

    function showModal(href) { pendingNav = href; modal.classList.remove('hidden'); modal.classList.add('flex'); }
    function hideModal()      { modal.classList.add('hidden'); modal.classList.remove('flex'); pendingNav = null; }

    stayBtn.addEventListener('click',  hideModal);
    modal.addEventListener('click',    e => { if (e.target === modal) hideModal(); });
    leaveBtn.addEventListener('click', () => {
        isDirty = false;
        hideModal();
        if (pendingNav) window.location.href = pendingNav;
    });

    // Mark dirty on any form field change
    document.getElementById('articleForm')
        .querySelectorAll('input,textarea,select')
        .forEach(el => {
            el.addEventListener('input',  () => { isDirty = true; });
            el.addEventListener('change', () => { isDirty = true; });
        });

    // Also dirty when image editor applies or file is picked
    document.getElementById('applyImageEdit')?.addEventListener('click', () => { isDirty = true; });
    document.getElementById('imageInput')?.addEventListener('change',    () => { isDirty = true; });

    // Clean on successful submit (allow save to proceed)
    document.getElementById('articleForm').addEventListener('submit', () => { isDirty = false; });

    // Intercept all in-page navigation links (Back arrow + Cancel)
    document.querySelectorAll('a[href]').forEach(link => {
        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript')) return;
        link.addEventListener('click', function (e) {
            if (!isDirty) return;
            e.preventDefault();
            showModal(href);
        });
    });

    // Browser close / refresh (native dialog — browser security forces this)
    window.addEventListener('beforeunload', e => {
        if (!isDirty) return;
        e.preventDefault();
        e.returnValue = '';
    });
})();
</script>
