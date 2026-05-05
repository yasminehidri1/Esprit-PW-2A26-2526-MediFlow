<?php // Views/Back/dossier_medical/admin_doctors_edit.php ?>

<div class="max-w-3xl mx-auto">
  <!-- Header -->
  <div class="mb-8">
    <div class="flex items-center gap-3 mb-4">
      <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center shadow-[0_4px_20px_rgba(0,77,153,0.1)]">
        <span class="material-symbols-outlined text-blue-700 text-2xl">edit</span>
      </div>
      <div>
        <h1 class="text-2xl lg:text-4xl font-extrabold text-blue-900 font-headline tracking-tight">Éditer Médecin</h1>
        <p class="text-sm lg:text-base text-slate-600 font-medium">Modifiez les informations du professionnel de santé</p>
      </div>
    </div>
  </div>

  <!-- Form Card -->
  <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgba(0,77,153,0.05)] border border-slate-100 overflow-hidden">
    <?php if (!empty($validation_errors)): ?>
    <div class="mx-6 mt-6 p-4 rounded-xl bg-red-50 border border-red-200 shadow-sm">
      <p class="font-bold text-red-700 mb-2 flex items-center gap-2">
        <span class="material-symbols-outlined text-lg">error</span>Erreurs de saisie
      </p>
      <ul class="text-sm text-red-600 list-disc list-inside space-y-1 font-medium">
        <?php foreach ($validation_errors as $msg): ?>
        <li><?php echo htmlspecialchars($msg); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>
    <form method="POST" class="p-6 lg:p-8 space-y-8" id="editDoctorForm" novalidate>
      <input type="hidden" name="id" value="<?php echo $doctor['id_PK']; ?>"/>

      <!-- Form Sections -->
      <div class="space-y-8">
        <!-- Personal Information Section -->
        <div class="border-b border-slate-100 pb-8">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center border border-blue-100 shadow-sm">
              <span class="material-symbols-outlined text-blue-700 text-lg">person</span>
            </div>
            <h2 class="text-lg font-bold text-slate-800 font-headline">Informations Personnelles</h2>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Nom -->
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">Nom *</label>
              <input type="text" name="nom" id="nom" value="<?php echo htmlspecialchars($doctor['nom'] ?? ''); ?>" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all shadow-sm" placeholder="Ex: Dupont"/>
              <p class="text-xs text-red-500 mt-1.5 hidden font-medium" id="nomError"></p>
            </div>

            <!-- Prénom -->
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">Prénom *</label>
              <input type="text" name="prenom" id="prenom" value="<?php echo htmlspecialchars($doctor['prenom'] ?? ''); ?>" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all shadow-sm" placeholder="Ex: Jean"/>
              <p class="text-xs text-red-500 mt-1.5 hidden font-medium" id="prenomError"></p>
            </div>
          </div>
        </div>

        <!-- Contact Information Section -->
        <div class="border-b border-slate-100 pb-8">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center border border-emerald-100 shadow-sm">
              <span class="material-symbols-outlined text-emerald-600 text-lg">mail</span>
            </div>
            <h2 class="text-lg font-bold text-slate-800 font-headline">Informations de Contact</h2>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Email -->
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">Email *</label>
              <input type="text" name="mail" id="mail" value="<?php echo htmlspecialchars($doctor['mail'] ?? ''); ?>" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all shadow-sm" placeholder="Ex: jean@mediflow.com"/>
              <p class="text-xs text-red-500 mt-1.5 hidden font-medium" id="mailError"></p>
            </div>

            <!-- Téléphone -->
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">Téléphone</label>
              <input type="text" name="tel" id="tel" value="<?php echo htmlspecialchars($doctor['tel'] ?? ''); ?>" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all shadow-sm" placeholder="Ex: 06 12 34 56 78"/>
              <p class="text-xs text-red-500 mt-1.5 hidden font-medium" id="telError"></p>
            </div>
          </div>
        </div>

        <!-- Address Section -->
        <div>
          <div class="flex items-center gap-3 mb-6">
            <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center border border-purple-100 shadow-sm">
              <span class="material-symbols-outlined text-purple-600 text-lg">location_on</span>
            </div>
            <h2 class="text-lg font-bold text-slate-800 font-headline">Adresse</h2>
          </div>

          <label class="block text-sm font-bold text-slate-700 mb-2">Adresse Complète</label>
          <textarea name="adresse" id="adresse" rows="3" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent transition-all resize-none shadow-sm" placeholder="Ex: 123 Rue de la Paix, 75000 Paris"><?php echo htmlspecialchars($doctor['adresse'] ?? ''); ?></textarea>
          <p class="text-xs text-red-500 mt-1.5 hidden font-medium" id="adresseError"></p>
        </div>
      </div>

      <!-- Actions -->
      <div class="pt-8 flex flex-col sm:flex-row gap-3 flex-wrap bg-slate-50 -mx-6 lg:-mx-8 -mb-6 lg:-mb-8 p-6 lg:p-8 border-t border-slate-100">
        <button type="submit" class="flex items-center justify-center gap-2 px-8 py-3 bg-blue-700 hover:bg-blue-800 text-white rounded-xl font-bold transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5">
          <span class="material-symbols-outlined text-[20px]">save</span>
          <span>Enregistrer</span>
        </button>

        <a href="/integration/dossier/admin/doctors" class="flex items-center justify-center gap-2 px-8 py-3 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 rounded-xl font-bold transition-all shadow-sm hover:shadow">
          <span class="material-symbols-outlined text-[20px]">close</span>
          <span>Annuler</span>
        </a>

        <!-- Delete Button -->
        <div class="ml-auto w-full sm:w-auto m-0">
          <form method="POST" action="/integration/dossier/admin/doctors/delete" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce médecin ? Cette action est irréversible.');">
            <input type="hidden" name="id" value="<?php echo $doctor['id_PK']; ?>"/>
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-8 py-3 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white rounded-xl font-bold transition-all border border-red-100 hover:border-red-600 shadow-sm">
              <span class="material-symbols-outlined text-[20px]">delete</span>
              <span>Supprimer</span>
            </button>
          </form>
        </div>
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
            if (!/^[a-zA-ZÀ-ÿ\s'-]{2,50}$/.test(value)) return 'Le prénom doit contenir au moins 2 caractères (lettres uniquement)';
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
          field.addEventListener('input', () => { validateField(fieldName, field, errorElement); });
          field.addEventListener('blur', () => { validateField(fieldName, field, errorElement); });
        }
      });

      function validateField(fieldName, field, errorElement) {
        const error = validators[fieldName].validate(field.value);
        if (error) {
          field.classList.add('border-red-500', 'focus:ring-red-500', 'bg-red-50');
          field.classList.remove('border-slate-200', 'focus:ring-blue-600', 'bg-slate-50', 'bg-white');
          errorElement.innerHTML = '<span class="material-symbols-outlined text-[14px] align-middle mr-1">warning</span>' + error;
          errorElement.classList.remove('hidden');
          errorElement.classList.add('block');
          return false;
        } else {
          field.classList.remove('border-red-500', 'focus:ring-red-500', 'bg-red-50');
          field.classList.add('border-slate-200', 'focus:ring-blue-600', 'bg-slate-50');
          errorElement.classList.add('hidden');
          errorElement.classList.remove('block');
          return true;
        }
      }

      // Form submission validation
      form.addEventListener('submit', (e) => {
        let isValid = true;

        Object.keys(validators).forEach(fieldName => {
          const field = document.getElementById(fieldName);
          const errorElement = document.getElementById(fieldName + 'Error');
          if (field && !validateField(fieldName, field, errorElement)) {
            isValid = false;
          }
        });

        if (!isValid) {
          e.preventDefault();
          window.scrollTo({ top: 0, behavior: 'smooth' });
        }
      });
    </script>
  </div>
</div>
