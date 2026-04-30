<?php
$embeddedInLayout = $embeddedInLayout ?? false;
$_pf_role = $_SESSION['user']['role'] ?? '';
$_pf_back = (isset($formAction) && strpos($formAction, 'fournisseur') !== false)
    ? '/integration/fournisseur/products'
    : '/integration/stock/products';
if ($_pf_role === 'Admin') {
    $_pf_back = '/integration/fournisseur/products';
}
?>
<?php if (!$embeddedInLayout): ?>
<!DOCTYPE html>
<html class="light" lang="fr">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>MediFlow | <?php echo isset($produit) ? 'Modifier' : 'Créer'; ?> Produit</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&amp;family=Inter:wght@400;500;600&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f7f9fb; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .section-header { display: flex; align-items: center; gap: 12px; font-weight: bold; color: #191c1e; }
        .section-number { width: 30px; height: 30px; background: #004d99; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        .emoji-input { font-size: 20px; margin-right: 8px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        .auto-calc { background: #f0f4ff; border: 1px solid #dae5ff; padding: 16px; border-radius: 8px; }

        /* ========== STYLES VALIDATION ========== */
        
        /* Champ valide (bordure verte) */
        input.field-valid,
        select.field-valid {
            border-color: #10b981 !important;
            background-color: #f0fdf4;
        }

        input.field-valid:focus,
        select.field-valid:focus {
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        /* Champ avec erreur (bordure rouge) */
        input.field-error,
        select.field-error {
            border-color: #ef4444 !important;
            background-color: #fef2f2;
        }

        input.field-error:focus,
        select.field-error:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        /* Bouton submit - désactivé */
        button[type="submit"]:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

    </style>
</head>
<body class="bg-surface text-on-surface">
<!-- SideNavBar -->
<aside class="h-screen w-64 fixed left-0 top-0 bg-slate-50 dark:bg-slate-900 flex flex-col py-6 z-50">
    <div class="px-6 mb-10">
        <h1 class="text-xl font-bold text-blue-800 dark:text-blue-300 font-['Manrope']">MediFlow</h1>
        <p class="text-xs text-slate-500 font-medium tracking-wider uppercase mt-1">Stock Management</p>
    </div>
    <nav class="flex-1 space-y-1">
        <a class="flex items-center gap-3 px-4 py-3 bg-white text-blue-700 border-l-4 border-teal-600 font-['Manrope'] font-bold text-sm" href="<?= $_pf_back ?>">
            <span class="material-symbols-outlined">inventory_2</span><span>Produits</span>
        </a>
    </nav>
</aside>
<!-- Header standalone -->
<header class="fixed top-0 right-0 left-64 z-40 flex justify-between items-center px-8 py-3 rounded-2xl mt-4 mx-4 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md shadow-[0_20px_50px_rgba(0,77,153,0.05)]">
    <div class="flex items-center gap-4 flex-1">
        <h3 class="text-lg font-bold"><?php echo isset($produit) ? '✏️ Modifier le produit' : '➕ Créer un produit'; ?></h3>
    </div>
</header>
<main class="pl-64 pt-28 min-h-screen bg-surface">
<div class="max-w-4xl mx-auto px-8 pb-12">
<?php else: ?>
<div class="max-w-4xl mx-auto">
<!-- Breadcrumb embarqué -->
<div class="flex items-center gap-3 mb-6">
    <a href="<?= $_pf_back ?>" class="flex items-center gap-2 text-secondary hover:text-primary transition-colors text-sm font-medium">
        <span class="material-symbols-outlined text-base">arrow_back</span>
        Retour aux produits
    </a>
    <span class="text-outline/40">/</span>
    <span class="text-on-surface font-bold text-sm"><?= isset($produit) ? 'Modifier le produit' : 'Créer un produit' ?></span>
</div>
<?php endif; ?>
        
        <!-- Form Card -->
        <div class="bg-white rounded-2xl p-8 shadow-lg">
            
            <div class="mb-6">
                <p class="text-sm text-primary font-bold">Remplissez tous les champs marqués d'un astérisque (*)</p>
            </div>
            
            <!-- Messages d'erreurs -->
            <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    <?php foreach ($_SESSION['errors'] as $err): ?>
                        <p class="mb-2">❌ <?php echo htmlspecialchars($err); ?></p>
                    <?php endforeach; ?>
                    <?php unset($_SESSION['errors']); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" action="<?php echo $formAction ?? '/integration/fournisseur/products/create'; ?>" class="space-y-8">
                
                <?php if (isset($produit)): ?>
                    <input type="hidden" name="id" value="<?php echo $produit['id']; ?>">
                    <input type="hidden" name="image_existing" value="<?php echo htmlspecialchars($produit['image']); ?>">
                <?php endif; ?>
                
                <!-- SECTION 1: INFORMATIONS GÉNÉRALES -->
                <div>
                    <div class="section-header mb-6">
                        <div class="section-number">1</div>
                        <div>Informations Générales</div>
                    </div>
                    
                    <div class="grid-2">
                        <!-- Nom du Produit -->
                        <div>
                            <label class="block text-sm font-bold text-on-surface mb-2">
                                <span class="emoji-input">💊</span>Nom du Produit *
                            </label>
                            <input 
                                type="text" 
                                name="nom" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition"
                                value="<?php echo isset($produit) ? htmlspecialchars($produit['nom']) : (isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ''); ?>"
                                placeholder="Ex: Amoxicilline 500mg">
                        </div>
                        
                        <!-- Catégorie -->
                        <div>
                            <label class="block text-sm font-bold text-on-surface mb-2">
                                <span class="emoji-input">📂</span>Catégorie *
                            </label>
                            <select name="categorie" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition">
                                <option value="">-- Sélectionner --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat; ?>" <?php echo (isset($produit) && $produit['categorie'] === $cat) || (isset($_POST['categorie']) && $_POST['categorie'] === $cat) ? 'selected' : ''; ?>>
                                        <?php if ($cat === 'comprimés') echo '💊'; elseif ($cat === 'sirops') echo '🧪'; else echo '💉'; ?> <?php echo ucfirst($cat); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Image du produit -->
                    <div class="mt-6">
                        <label class="block text-sm font-bold text-on-surface mb-2">
                            <span class="emoji-input">🖼️</span>Image du produit
                        </label>
                        
                        <!-- Zone de drop compacte -->
                        <div id="dropZone" class="cursor-pointer hover:border-primary transition border-2 border-dashed rounded-lg h-32 flex items-center justify-center bg-gray-50 hover:bg-blue-50" ondrop="handleDrop(event)" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)">
                            <?php if (isset($produit) && !empty($produit['image'])): ?>
                                <?php $imgSrc = (strpos($produit['image'], 'http') === 0 || strpos($produit['image'], '/') === 0) ? $produit['image'] : '/integration/' . $produit['image']; ?>
                                <img id="imagePreview" src="<?php echo htmlspecialchars($imgSrc); ?>" alt="Preview" class="max-h-full max-w-full object-contain">
                            <?php else: ?>
                                <div class="text-center">
                                    <p class="text-2xl mb-1">📸</p>
                                    <p class="text-xs text-secondary">Glissez-déposez ou cliquez</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Input fichier caché -->
                        <input type="file" id="imageInput" name="image" accept="image/jpeg,image/png,image/gif,image/webp" style="display:none;" onchange="handleFileSelect(event)">
                        
                        <p class="text-xs text-secondary mt-2">📁 Formats: JPG, PNG, GIF, WebP (Max 5MB)</p>
                        <?php if (isset($produit) && !empty($produit['image'])): ?>
                            <button type="button" class="mt-2 text-sm text-error hover:underline" onclick="clearImage()">Supprimer l'image</button>
                        <?php endif; ?>
                    </div>
                </div>
                
                <hr class="my-6">
                
                <!-- SECTION 2: GESTION DU STOCK -->
                <div>
                    <div class="section-header mb-6">
                        <div class="section-number">2</div>
                        <div>Gestion du Stock</div>
                    </div>
                    
                    <div class="grid-2">
                        <!-- Quantité Disponible -->
                        <div>
                            <label class="block text-sm font-bold text-on-surface mb-2">
                                <span class="emoji-input">📦</span>Quantité disponible *
                            </label>
                            <input 
                                type="text" 
                                id="quantite" 
                                name="quantite_disponible" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition"
                                value="<?php echo isset($produit) ? htmlspecialchars($produit['quantite_disponible']) : (isset($_POST['quantite_disponible']) ? htmlspecialchars($_POST['quantite_disponible']) : 0); ?>"
                                placeholder="40"
                                onchange="calculateMetrics()">
                        </div>
                        
                        <!-- Seuil d'Alerte -->
                        <div>
                            <label class="block text-sm font-bold text-on-surface mb-2">
                                <span class="emoji-input">⚠️</span>Seuil d'alerte *
                            </label>
                            <input 
                                type="text" 
                                name="seuil_alerte" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition"
                                value="<?php echo isset($produit) ? htmlspecialchars($produit['seuil_alerte']) : (isset($_POST['seuil_alerte']) ? htmlspecialchars($_POST['seuil_alerte']) : 5); ?>"
                                placeholder="5">
                            <p class="text-xs text-secondary mt-1">Alerte si stock < ce seuil</p>
                        </div>
                    </div>
                </div>
                
                <hr class="my-6">
                
                <!-- SECTION 3: TARIFICATION -->
                <div>
                    <div class="section-header mb-6">
                        <div class="section-number">3</div>
                        <div>Tarification</div>
                    </div>
                    
                    <div class="grid-2">
                        <!-- Prix de vente -->
                        <div>
                            <label class="block text-sm font-bold text-on-surface mb-2">
                                <span class="emoji-input">💰</span>Prix de vente (DT) *
                            </label>
                            <input 
                                type="text" 
                                id="prixVente" 
                                name="prix_unitaire" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition"
                                value="<?php echo isset($produit) ? htmlspecialchars($produit['prix_unitaire']) : (isset($_POST['prix_unitaire']) ? htmlspecialchars($_POST['prix_unitaire']) : ''); ?>"
                                placeholder="10.00"
                                onchange="calculateMetrics()">
                        </div>
                        
                        <!-- Prix d'achat -->
                        <div>
                            <label class="block text-sm font-bold text-on-surface mb-2">
                                <span class="emoji-input">🛒</span>Prix d'achat (DT) *
                            </label>
                            <input 
                                type="text" 
                                id="prixAchat" 
                                name="prix_achat" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition"
                                value="<?php echo isset($produit) ? htmlspecialchars($produit['prix_achat']) : (isset($_POST['prix_achat']) ? htmlspecialchars($_POST['prix_achat']) : ''); ?>"
                                placeholder="5.00"
                                onchange="calculateMetrics()">
                        </div>
                    </div>
                    
                    <!-- Calculs Automatiques -->
                    <div class="auto-calc mt-6">
                        <p class="text-sm font-bold text-primary mb-4">📊 CALCULS AUTOMATIQUES</p>
                        
                        <div class="grid-2">
                            <div>
                                <p class="text-xs text-secondary mb-2">Marge</p>
                                <p class="text-xl font-bold text-orange-500">
                                    <span id="margeValue">0.00</span> <span class="text-sm">DT</span>
                                </p>
                            </div>
                            
                            <div>
                                <p class="text-xs text-secondary mb-2">% Bénéfice</p>
                                <p class="text-xl font-bold text-green-500">
                                    <span id="beneficeValue">0.00</span><span class="text-sm">%</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr class="my-6">
                
                <!-- Boutons d'action -->
                <div class="flex gap-4 pt-4">
                    <button 
                        type="submit" 
                        class="flex-1 bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2">
                        <span>✅</span>
                        <?php echo isset($produit) ? 'Mettre à jour' : 'Créer'; ?>
                    </button>
                    <a 
                        href="<?php
                            // Retour vers la liste produits selon le contexte
                            if (isset($formAction) && strpos($formAction, 'fournisseur') !== false) {
                                echo '/integration/fournisseur/products';
                            } else {
                                echo '/integration/stock/products';
                            }
                        ?>" 
                        class="flex-1 bg-gray-200 text-gray-700 font-bold py-3 rounded-lg hover:bg-gray-300 transition flex items-center justify-center gap-2">
                        <span>❌</span>
                        Annuler
                    </a>
                </div>
                
            </form>
        </div>
        
    </div>
</main>

<script>
// ========================================
// OBJET VALIDATEUR - Règles identiques au serveur PHP
// ========================================
const ProductValidator = {
    // Définition des règles
    rules: {
        nom: {
            required: true,
            minLength: 3,
            maxLength: 100,
            pattern: /^[\w\s\-(),.éèêëàâäùûüôöœïî]+$/i
        },
        categorie: {
            required: true,
            enum: ['comprimés', 'sirops', 'injectables']
        },
        quantite_disponible: {
            required: true,
            type: 'number',
            min: 0,
            max: 999999,
            integer: true
        },
        prix_unitaire: {
            required: true,
            type: 'number',
            min: 0.01,
            max: 99999.99
        },
        prix_achat: {
            required: true,
            type: 'number',
            min: 0.01,
            max: 99999.99
        },
        seuil_alerte: {
            required: true,
            type: 'number',
            min: 0,
            max: 999999,
            integer: true
        },
        image: {
            required: false,
            maxSize: 5 * 1024 * 1024, // 5MB
            mimeTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp']
        }
    },

    // Messages d'erreur personnalisés
    messages: {
        nom: {
            required: 'Le nom du produit est requis',
            minLength: 'Le nom doit contenir au moins 3 caractères',
            maxLength: 'Le nom ne doit pas dépasser 100 caractères',
            pattern: 'Le nom contient des caractères non autorisés'
        },
        categorie: {
            required: 'Sélectionnez une catégorie',
            enum: 'Catégorie invalide'
        },
        quantite_disponible: {
            required: 'La quantité est requise',
            type: 'La quantité doit être un nombre',
            min: 'La quantité doit être supérieure ou égale à 0',
            max: 'La quantité ne doit pas dépasser 999999',
            integer: 'La quantité doit être un nombre entier'
        },
        prix_unitaire: {
            required: 'Le prix de vente est requis',
            type: 'Le prix doit être un nombre',
            min: 'Le prix doit être supérieur à 0',
            max: 'Le prix ne doit pas dépasser 99999.99'
        },
        prix_achat: {
            required: 'Le prix d\'achat est requis',
            type: 'Le prix doit être un nombre',
            min: 'Le prix doit être supérieur à 0',
            max: 'Le prix ne doit pas dépasser 99999.99'
        },
        seuil_alerte: {
            required: 'Le seuil d\'alerte est requis',
            type: 'Le seuil doit être un nombre',
            min: 'Le seuil doit être supérieur ou égal à 0',
            max: 'Le seuil ne doit pas dépasser 999999',
            integer: 'Le seuil doit être un nombre entier'
        },
        image: {
            maxSize: 'L\'image est trop volumineuse (max 5MB)',
            mimeTypes: 'Format d\'image non autorisé (JPG, PNG, GIF, WebP)'
        }
    },

    // Validateurs individuels par type
    validators: {
        // Valide un nombre entier
        integer: (value) => {
            return Number.isInteger(Number(value));
        },
        // Valide un nombre décimal
        decimal: (value) => {
            const num = parseFloat(value);
            return !isNaN(num);
        },
        // Valide la plage de valeur
        range: (value, min, max) => {
            const num = parseFloat(value);
            return num >= min && num <= max;
        },
        // Valide enum
        enum: (value, allowedValues) => {
            return allowedValues.includes(value);
        },
        // Valide la taille du fichier
        fileSize: (file, maxSize) => {
            return file.size <= maxSize;
        },
        // Valide le type MIME
        mimeType: (file, allowedTypes) => {
            return allowedTypes.includes(file.type);
        }
    },

    // Fonction principale de validation d'un champ
    validateField: function(fieldName, value) {
        const rules = this.rules[fieldName];
        const messages = this.messages[fieldName];
        
        if (!rules) return null; // Champ non défini

        // Vérification si requis
        if (rules.required && (value === '' || value === null)) {
            return messages.required;
        }

        // Si vide et non requis, valide
        if (!rules.required && (value === '' || value === null)) {
            return null;
        }

        // Validation spécifique par champ
        switch(fieldName) {
            case 'nom':
                if (value.length < rules.minLength) return messages.minLength;
                if (value.length > rules.maxLength) return messages.maxLength;
                if (!rules.pattern.test(value)) return messages.pattern;
                break;

            case 'categorie':
                if (!this.validators.enum(value, rules.enum)) return messages.enum;
                break;

            case 'quantite_disponible':
            case 'seuil_alerte':
                if (!this.validators.decimal(value)) return messages.type;
                if (!this.validators.integer(value)) return messages.integer;
                if (!this.validators.range(value, rules.min, rules.max)) {
                    if (parseFloat(value) < rules.min) return messages.min;
                    if (parseFloat(value) > rules.max) return messages.max;
                }
                break;

            case 'prix_unitaire':
            case 'prix_achat':
                if (!this.validators.decimal(value)) return messages.type;
                if (!this.validators.range(value, rules.min, rules.max)) {
                    if (parseFloat(value) <= 0) return messages.min;
                    if (parseFloat(value) > rules.max) return messages.max;
                }
                break;

            case 'image':
                // Validation image si fichier sélectionné
                const fileInput = document.getElementById('imageInput');
                if (fileInput && fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    if (!this.validators.fileSize(file, rules.maxSize)) return messages.maxSize;
                    if (!this.validators.mimeType(file, rules.mimeTypes)) return messages.mimeTypes;
                }
                break;
        }

        return null; // Valide
    }
};

function handleDragOver(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('dropZone').style.borderColor = '#004d99';
    document.getElementById('dropZone').style.backgroundColor = '#dae5ff';
}

function handleDragLeave(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('dropZone').style.borderColor = '#c2c6d4';
    document.getElementById('dropZone').style.backgroundColor = '#f2f4f6';
}

function handleDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('dropZone').style.borderColor = '#c2c6d4';
    document.getElementById('dropZone').style.backgroundColor = '#f2f4f6';
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        document.getElementById('imageInput').files = files;
        handleFileSelect({ target: { files: files } });
    }
}

function handleFileSelect(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            const dropZone = document.getElementById('dropZone');
            dropZone.innerHTML = '<img id="imagePreview" src="' + event.target.result + '" alt="Preview" class="max-h-full max-w-full object-contain">';
        };
        reader.readAsDataURL(file);
    }
}

function clearImage() {
    document.getElementById('imageInput').value = '';
    const dropZone = document.getElementById('dropZone');
    dropZone.innerHTML = `
        <div class="text-center">
            <p class="text-2xl mb-1">📸</p>
            <p class="text-xs text-secondary">Glissez-déposez ou cliquez</p>
        </div>
    `;
    dropZone.style.backgroundColor = '#f2f4f6';
}

document.getElementById('dropZone').addEventListener('click', function() {
    document.getElementById('imageInput').click();
});

function calculateMetrics() {
    const prixVente = parseFloat(document.getElementById('prixVente').value) || 0;
    const prixAchat = parseFloat(document.getElementById('prixAchat').value) || 0;
    
    const marge = prixVente - prixAchat;
    const benefice = prixAchat > 0 ? ((marge / prixAchat) * 100).toFixed(2) : 0;
    
    document.getElementById('margeValue').textContent = marge.toFixed(2);
    document.getElementById('beneficeValue').textContent = benefice;
}

// Calculer au chargement
window.addEventListener('load', function() {
    calculateMetrics();
    setupValidationListeners();
});

// ========================================
// VALIDATION COMPLÈTE DU FORMULAIRE À LA SOUMISSION
// ========================================
function validateCompleteForm(event) {
    const form = document.querySelector('form');
    const fieldsToValidate = ['nom', 'categorie', 'quantite_disponible', 'prix_unitaire', 'prix_achat', 'seuil_alerte'];
    
    let formIsValid = true;
    let firstInvalidField = null;

    // Valider tous les champs
    fieldsToValidate.forEach(fieldName => {
        const field = form.querySelector(`input[name="${fieldName}"], select[name="${fieldName}"]`);
        if (field) {
            const error = ProductValidator.validateField(fieldName, field.value);
            
            if (error) {
                formIsValid = false;
                validateAndShowError(fieldName, field.value);
                
                // Se souvenir du premier champ invalide
                if (!firstInvalidField) {
                    firstInvalidField = field;
                }
            } else {
                validateAndShowError(fieldName, field.value);
            }
        }
    });

    // Si formulaire invalide, empêcher la soumission
    if (!formIsValid) {
        event.preventDefault();
        
        // Scroll vers le premier champ invalide
        if (firstInvalidField) {
            firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstInvalidField.focus();
        }

        // Afficher un message d'alerte
        alert('⚠️ Veuillez corriger les erreurs du formulaire');
        return false;
    }

    // Formulaire valide, permettre la soumission
    return true;
}

// Attacher la validation au formulaire
window.addEventListener('load', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', validateCompleteForm);
    }
});

function setupValidationListeners() {
    // Champs texte et nombre
    const textFields = ['nom', 'quantite_disponible', 'prix_unitaire', 'prix_achat', 'seuil_alerte'];
    
    textFields.forEach(fieldName => {
        const field = document.querySelector(`input[name="${fieldName}"], select[name="${fieldName}"]`);
        if (field) {
            // Validation au blur (quand on quitte le champ)
            field.addEventListener('blur', function() {
                validateAndShowError(fieldName, this.value);
            });

            // Validation en temps réel (au fur et à mesure de la saisie)
            field.addEventListener('input', function() {
                if (this.classList.contains('field-error')) {
                    validateAndShowError(fieldName, this.value);
                }
            });
        }
    });

    // Catégorie (select)
    const categorie = document.querySelector('select[name="categorie"]');
    if (categorie) {
        categorie.addEventListener('change', function() {
            validateAndShowError('categorie', this.value);
        });
    }

    // Image (file input)
    const imageInput = document.getElementById('imageInput');
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            validateAndShowError('image', this.value);
        });
    }
}

// ========================================
// AFFICHAGE ERREURS ET VALIDATION TEMPS RÉEL
// ========================================
function validateAndShowError(fieldName, value) {
    const field = document.querySelector(`input[name="${fieldName}"], select[name="${fieldName}"]`);
    if (!field) return;

    const error = ProductValidator.validateField(fieldName, value);

    if (error) {
        // Afficher erreur
        field.classList.remove('field-valid');
        field.classList.add('field-error');
        field.title = error; // Affiche l'erreur en tooltip
    } else {
        // Pas d'erreur = valide
        field.classList.remove('field-error');
        field.classList.add('field-valid');
        field.title = ''; // Enlève le tooltip
    }

    // Mettre à jour l'état du bouton submit
    updateSubmitButtonState();
}

// ========================================
// GESTION ÉTAT BOUTON SUBMIT
// ========================================
function updateSubmitButtonState() {
    const form = document.querySelector('form');
    const submitBtn = form.querySelector('button[type="submit"]');
    const fieldsToValidate = ['nom', 'categorie', 'quantite_disponible', 'prix_unitaire', 'prix_achat', 'seuil_alerte'];
    
    let isFormValid = true;

    fieldsToValidate.forEach(fieldName => {
        const field = form.querySelector(`input[name="${fieldName}"], select[name="${fieldName}"]`);
        if (field && !field.classList.contains('field-valid')) {
            // Si le champ n'a pas été validé encore, vérifier s'il a une valeur
            const error = ProductValidator.validateField(fieldName, field.value);
            if (error && field.value !== '') {
                isFormValid = false;
            }
        }
    });

    submitBtn.disabled = !isFormValid;
}

</script>
<?php if (!$embeddedInLayout): ?>
</body></html>
<?php endif; ?>
