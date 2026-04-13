/**
 * Form Validation Script
 * Handles validation for Create and Edit user forms
 * 
 * @package MediFlow
 * @version 1.0.0
 */

// Validation Rules
const validationRules = {
    nom: {
        required: true,
        minLength: 2,
        maxLength: 50,
        pattern: /^[a-zA-ZÀ-ÿ\s'-]+$/,
        messages: {
            required: 'Le nom est requis',
            minLength: 'Le nom doit contenir au moins 2 caractères',
            maxLength: 'Le nom ne doit pas dépasser 50 caractères',
            pattern: 'Le nom ne peut contenir que des lettres, espaces, traits et apostrophes'
        }
    },
    prenom: {
        required: true,
        minLength: 2,
        maxLength: 50,
        pattern: /^[a-zA-ZÀ-ÿ\s'-]+$/,
        messages: {
            required: 'Le prénom est requis',
            minLength: 'Le prénom doit contenir au moins 2 caractères',
            maxLength: 'Le prénom ne doit pas dépasser 50 caractères',
            pattern: 'Le prénom ne peut contenir que des lettres, espaces, traits et apostrophes'
        }
    },
    mail: {
        required: true,
        pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
        messages: {
            required: 'L\'email est requis',
            pattern: 'L\'email n\'est pas valide'
        }
    },
    tel: {
        pattern: /^[\d\s+()-]*$/,
        messages: {
            pattern: 'Le téléphone n\'est pas valide'
        }
    },
    id_role: {
        required: true,
        messages: {
            required: 'Veuillez sélectionner un rôle'
        }
    },
    password: {
        minLength: 6,
        messages: {
            minLength: 'Le mot de passe doit contenir au moins 6 caractères'
        }
    },
    password_confirm: {
        messages: {
            mismatch: 'Les mots de passe ne correspondent pas'
        }
    }
};

/**
 * Validate a single field
 * @param {string} fieldName - Field name/id
 * @param {string} value - Field value
 * @param {boolean} isEdit - Is this an edit form?
 * @returns {string|null} Error message or null if valid
 */
function validateField(fieldName, value, isEdit = false) {
    const rules = validationRules[fieldName];
    if (!rules) return null;

    // For edit form: password fields are optional
    if (isEdit && (fieldName === 'password' || fieldName === 'password_confirm')) {
        // Only validate if filled
        if (!value || value.trim() === '') return null;
    }

    // For create form: password is required
    if (!isEdit && fieldName === 'password' && !value) {
        return 'Le mot de passe est requis';
    }

    // Check required
    if (rules.required && (!value || value.trim() === '')) {
        return rules.messages.required;
    }

    // Check minLength
    if (rules.minLength && value.length < rules.minLength) {
        return rules.messages.minLength;
    }

    // Check maxLength
    if (rules.maxLength && value.length > rules.maxLength) {
        return rules.messages.maxLength;
    }

    // Check pattern
    if (rules.pattern && value && !rules.pattern.test(value)) {
        return rules.messages.pattern;
    }

    return null;
}

/**
 * Show error message for a field
 * @param {string} fieldName - Field name/id
 * @param {string} message - Error message
 */
function showFieldError(fieldName, message) {
    const field = document.getElementById(fieldName);
    if (!field) return;

    const formGroup = field.closest('.form-group');
    if (!formGroup) return;

    // Remove existing error
    const existingError = formGroup.querySelector('.error-message');
    if (existingError) existingError.remove();

    // Add error styles
    field.style.borderColor = '#ba1a1a';
    field.style.backgroundColor = 'rgba(186, 26, 26, 0.03)';

    // Add error message
    if (message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.color = '#ba1a1a';
        errorDiv.style.fontSize = '13px';
        errorDiv.style.marginTop = '6px';
        errorDiv.style.display = 'flex';
        errorDiv.style.alignItems = 'center';
        errorDiv.style.gap = '6px';
        errorDiv.innerHTML = `<span class="material-symbols-outlined" style="font-size: 16px;">error</span><span>${message}</span>`;
        formGroup.appendChild(errorDiv);
    }
}

/**
 * Clear error message for a field
 * @param {string} fieldName - Field name/id
 */
function clearFieldError(fieldName) {
    const field = document.getElementById(fieldName);
    if (!field) return;

    const formGroup = field.closest('.form-group');
    if (!formGroup) return;

    field.style.borderColor = '';
    field.style.backgroundColor = '';

    const errorDiv = formGroup.querySelector('.error-message');
    if (errorDiv) errorDiv.remove();
}

/**
 * Initialize form validation
 * @param {boolean} isEdit - Is this an edit form?
 */
function initializeFormValidation(isEdit = false) {
    const form = document.querySelector('form');
    if (!form) return;

    // Form Submit
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        let isValid = true;
        const errors = {};

        // Validate all required fields
        for (const fieldName in validationRules) {
            const field = document.getElementById(fieldName);
            if (!field) continue;

            const value = field.value;
            const error = validateField(fieldName, value, isEdit);

            if (error) {
                errors[fieldName] = error;
                showFieldError(fieldName, error);
                isValid = false;
            } else {
                clearFieldError(fieldName);
            }
        }

        // Check password match (if password fields exist and are filled)
        const passwordField = document.getElementById('password');
        const passwordConfirmField = document.getElementById('password_confirm');

        if (passwordField && passwordConfirmField) {
            const password = passwordField.value;
            const passwordConfirm = passwordConfirmField.value;

            if ((password || passwordConfirm) && password !== passwordConfirm) {
                errors['password_confirm'] = validationRules.password_confirm.messages.mismatch;
                showFieldError('password_confirm', errors['password_confirm']);
                isValid = false;
            } else if (!password && !passwordConfirm) {
                clearFieldError('password_confirm');
            }
        }

        // If valid, submit
        if (isValid) {
            this.submit();
        }
    });

    // Real-time Validation
    document.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('blur', function() {
            // Skip password fields on blur for edit forms (only validate on submit)
            if (isEdit && (this.id === 'password' || this.id === 'password_confirm')) {
                return;
            }

            const error = validateField(this.id, this.value, isEdit);
            if (error) {
                showFieldError(this.id, error);
            } else {
                clearFieldError(this.id);
            }
        });

        field.addEventListener('input', function() {
            const error = validateField(this.id, this.value, isEdit);
            if (!error) {
                clearFieldError(this.id);
            }
        });
    });
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Check if this is an edit form by looking for the URL
    const isEdit = window.location.href.includes('action=edit');
    initializeFormValidation(isEdit);
});
