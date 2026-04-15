<?php
/**
 * Admin Edit Doctor View
 */
$activePage = 'admin';
?>
<!DOCTYPE html>
<html class="light" lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>Éditer Médecin — MediFlow Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          "colors": {
            "primary": "#004d99",
            "tertiary": "#005851",
            "secondary": "#4a5f83",
            "error": "#ba1a1a",
            "surface": "#f7f9fb",
            "surface-container-lowest": "#ffffff",
            "on-surface": "#191c1e"
          }
        }
      }
    }
  </script>
  <style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    body { font-family: 'Inter', sans-serif; }
    h1, h2, h3 { font-family: 'Manrope', sans-serif; }
  </style>
</head>
<body class="bg-surface text-on-surface min-h-screen">

<?php require __DIR__ . '/../layout/sidebar.php'; ?>
<?php require __DIR__ . '/../layout/topbar.php'; ?>

<!-- Main Content -->
<main class="lg:ml-64 pt-24 pb-12 px-4 lg:px-8 min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
  <div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center gap-3 mb-4">
        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
          <span class="material-symbols-outlined text-blue-600 text-2xl">edit</span>
        </div>
        <div>
          <h1 class="text-2xl lg:text-4xl font-bold text-blue-900">Éditer Médecin</h1>
          <p class="text-sm lg:text-base text-slate-600">Modifiez les informations du professionnel</p>
        </div>
      </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
      <form method="POST" class="p-6 lg:p-8 space-y-6" id="editDoctorForm" novalidate>
        <input type="hidden" name="id" value="<?php echo $doctor['id_PK']; ?>"/>

        <!-- Form Sections -->
        <div class="space-y-6">
          <!-- Personal Information Section -->
          <div class="border-b border-slate-200 pb-6">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-blue-600 text-lg">person</span>
              </div>
              <h2 class="text-lg font-bold text-slate-800">Informations Personnelles</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
              <!-- Nom -->
              <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Nom *</label>
                <input type="text" name="nom" id="nom" value="<?php echo htmlspecialchars($doctor['nom'] ?? ''); ?>" required class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" placeholder="Ex: Dupont"/>
                <p class="text-xs text-red-500 mt-1 hidden" id="nomError"></p>
              </div>

              <!-- Prénom -->
              <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Prénom *</label>
                <input type="text" name="prenom" id="prenom" value="<?php echo htmlspecialchars($doctor['prenom'] ?? ''); ?>" required class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" placeholder="Ex: Jean"/>
                <p class="text-xs text-red-500 mt-1 hidden" id="prenomError"></p>
              </div>
            </div>
          </div>

          <!-- Contact Information Section -->
          <div class="border-b border-slate-200 pb-6">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-green-600 text-lg">mail</span>
              </div>
              <h2 class="text-lg font-bold text-slate-800">Informations de Contact</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
              <!-- Email -->
              <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Email *</label>
                <input type="email" name="mail" id="mail" value="<?php echo htmlspecialchars($doctor['mail'] ?? ''); ?>" required class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" placeholder="Ex: jean@mediflow.com"/>
                <p class="text-xs text-red-500 mt-1 hidden" id="mailError"></p>
              </div>

              <!-- Téléphone -->
              <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Téléphone</label>
                <input type="tel" name="tel" id="tel" value="<?php echo htmlspecialchars($doctor['tel'] ?? ''); ?>" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" placeholder="Ex: +33 1 23 45 67 89"/>
                <p class="text-xs text-red-500 mt-1 hidden" id="telError"></p>
              </div>
            </div>
          </div>

          <!-- Address Section -->
          <div>
            <div class="flex items-center gap-3 mb-6">
              <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-purple-600 text-lg">location_on</span>
              </div>
              <h2 class="text-lg font-bold text-slate-800">Adresse</h2>
            </div>

            <label class="block text-sm font-bold text-slate-700 mb-2">Adresse Complète</label>
            <textarea name="adresse" id="adresse" rows="3" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition resize-none" placeholder="Ex: 123 Rue de la Paix, 75000 Paris"><?php echo htmlspecialchars($doctor['adresse'] ?? ''); ?></textarea>
            <p class="text-xs text-red-500 mt-1 hidden" id="adresseError"></p>
          </div>
        </div>

        <!-- Actions -->
        <div class="pt-8 border-t border-slate-200 flex flex-col sm:flex-row gap-3 flex-wrap">
          <button type="submit" class="flex items-center justify-center gap-2 px-8 py-3 bg-primary hover:bg-blue-700 text-white rounded-lg font-bold transition-all transform hover:scale-105">
            <span class="material-symbols-outlined">save</span>
            <span>Enregistrer</span>
          </button>

          <a href="?page=admin&action=doctors" class="flex items-center justify-center gap-2 px-8 py-3 bg-slate-300 hover:bg-slate-400 text-slate-800 rounded-lg font-bold transition-all transform hover:scale-105">
            <span class="material-symbols-outlined">close</span>
            <span>Annuler</span>
          </a>

          <!-- Delete Button -->
          <form method="POST" action="?page=admin&action=delete_doctor" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce médecin? Cette action est irréversible.');" class="ml-auto w-full sm:w-auto">
            <input type="hidden" name="id" value="<?php echo $doctor['id_PK']; ?>"/>
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-8 py-3 bg-error hover:bg-red-700 text-white rounded-lg font-bold transition-all transform hover:scale-105">
              <span class="material-symbols-outlined">delete</span>
              <span>Supprimer</span>
            </button>
          </form>
        </div>
      </form>

      <script>
        const form = document.getElementById('editDoctorForm');

        // Validation rules
        const validators = {
          nom: {
            validate: (value) => {
              if (!value.trim()) return 'Le nom est requis';
              if (!/^[a-zA-ZÀ-ÿ\s'-]{2,50}$/.test(value)) return 'Le nom doit contenir au moins 2 caractères (lettres uniquement)';
              return null;
            }
          },
          prenom: {
            validate: (value) => {
              if (!value.trim()) return 'Le prénom est requis';
              if (!/^[a-zA-ZÀ-ÿ\s'-]{2,50}$/.test(value)) return 'Le prénom doit contenir au least 2 caractères (lettres uniquement)';
              return null;
            }
          },
          mail: {
            validate: (value) => {
              if (!value.trim()) return 'L\'email est requis';
              const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
              if (!emailRegex.test(value)) return 'Veuillez entrer une adresse email valide';
              return null;
            }
          },
          tel: {
            validate: (value) => {
              if (value.trim() === '') return null; // Optionnel
              const telRegex = /^[\d\s\-+()\.]{10,20}$/;
              if (!telRegex.test(value)) return 'Veuillez entrer un numéro de téléphone valide (10-20 caractères)';
              return null;
            }
          },
          adresse: {
            validate: (value) => {
              if (value.trim() === '') return null; // Optionnel
              if (value.length < 5) return 'L\'adresse doit contenir au moins 5 caractères';
              if (value.length > 200) return 'L\'adresse ne doit pas dépasser 200 caractères';
              return null;
            }
          }
        };

        // Real-time validation on input
        Object.keys(validators).forEach(fieldName => {
          const field = document.getElementById(fieldName);
          const errorElement = document.getElementById(fieldName + 'Error');

          if (field) {
            // Validate on every keystroke
            field.addEventListener('input', () => {
              validateField(fieldName, field, errorElement);
            });

            // Also validate on blur
            field.addEventListener('blur', () => {
              validateField(fieldName, field, errorElement);
            });
          }
        });

        function validateField(fieldName, field, errorElement) {
          const error = validators[fieldName].validate(field.value);
          if (error) {
            field.classList.add('border-red-500', 'focus:ring-red-500', 'bg-red-50');
            field.classList.remove('border-slate-300', 'focus:ring-primary', 'bg-white');
            errorElement.textContent = '⚠️ ' + error;
            errorElement.classList.remove('hidden');
            errorElement.classList.add('block');
            return false;
          } else {
            field.classList.remove('border-red-500', 'focus:ring-red-500', 'bg-red-50');
            field.classList.add('border-slate-300', 'focus:ring-primary', 'bg-white');
            errorElement.classList.add('hidden');
            errorElement.classList.remove('block');
            return true;
          }
        }

        // Form submission validation
        form.addEventListener('submit', (e) => {
          e.preventDefault();
          let isValid = true;

          Object.keys(validators).forEach(fieldName => {
            const field = document.getElementById(fieldName);
            const errorElement = document.getElementById(fieldName + 'Error');
            if (field && !validateField(fieldName, field, errorElement)) {
              isValid = false;
            }
          });

          if (isValid) {
            form.submit();
          } else {
            alert('❌ Veuillez corriger les erreurs dans le formulaire');
            window.scrollTo({ top: 0, behavior: 'smooth' });
          }
        });
      </script>
    </div>
  </div>
</main>

</body>
</html>
