/**
 * Patient Registration JavaScript
 * Simple single-step registration form
 */

/**
 * Validate registration form (client-side)
 */
function validateRegistrationForm() {
	// Clear all errors first
	document.querySelectorAll('.form-error').forEach(el => el.textContent = '');
	const errorsContainer = document.getElementById('registerErrors');
	errorsContainer.style.display = 'none';
	errorsContainer.innerHTML = '';

	let errors = [];
	let isValid = true;

	// First Name validation
	const firstName = document.getElementById('firstName').value.trim();
	if (!firstName) {
		errors.push('Le prénom est requis');
		document.getElementById('errorFirstName').textContent = 'Requis';
		isValid = false;
	} else if (firstName.length < 2) {
		errors.push('Le prénom doit avoir au moins 2 caractères');
		document.getElementById('errorFirstName').textContent = 'Minimum 2 caractères';
		isValid = false;
	}

	// Last Name validation
	const lastName = document.getElementById('lastName').value.trim();
	if (!lastName) {
		errors.push('Le nom est requis');
		document.getElementById('errorLastName').textContent = 'Requis';
		isValid = false;
	} else if (lastName.length < 2) {
		errors.push('Le nom doit avoir au moins 2 caractères');
		document.getElementById('errorLastName').textContent = 'Minimum 2 caractères';
		isValid = false;
	}

	// Email validation
	const email = document.getElementById('registerEmail').value.trim();
	const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
	if (!email) {
		errors.push('L\'email est requis');
		document.getElementById('errorEmail').textContent = 'Requis';
		isValid = false;
	} else if (!emailRegex.test(email)) {
		errors.push('Format d\'email invalide');
		document.getElementById('errorEmail').textContent = 'Format invalide';
		isValid = false;
	}

	// Phone validation (optional)
	const phone = document.getElementById('phone').value.trim();
	if (phone && !/^[\d\s\+\-\(\)]{10,}$/.test(phone)) {
		errors.push('Numéro de téléphone invalide');
		document.getElementById('errorPhone').textContent = 'Format invalide';
		isValid = false;
	}

	// Password validation
	const password = document.getElementById('registerPassword').value;
	if (!password) {
		errors.push('Le mot de passe est requis');
		document.getElementById('errorPassword').textContent = 'Requis';
		isValid = false;
	} else if (password.length < 8) {
		errors.push('Le mot de passe doit avoir au moins 8 caractères');
		document.getElementById('errorPassword').textContent = 'Minimum 8 caractères';
		isValid = false;
	}

	// Confirm Password validation
	const confirmPassword = document.getElementById('confirmPassword').value;
	if (!confirmPassword) {
		errors.push('Veuillez confirmer votre mot de passe');
		document.getElementById('errorConfirmPassword').textContent = 'Requis';
		isValid = false;
	} else if (password !== confirmPassword) {
		errors.push('Les mots de passe ne correspondent pas');
		document.getElementById('errorConfirmPassword').textContent = 'Ne correspond pas';
		isValid = false;
	}

	// Terms validation
	if (!document.getElementById('terms').checked) {
		errors.push('Vous devez accepter les conditions d\'utilisation');
		document.getElementById('errorTerms').textContent = 'Requis';
		isValid = false;
	}

	// Show errors if any
	if (!isValid) {
		errorsContainer.style.display = 'block';
		errorsContainer.innerHTML = '<span class="alert-icon">⚠️</span><div class="alert-content"><p>' + errors.join('<br>') + '</p></div>';
	}

	return isValid;
}

/**
 * Initialize on page load
 */
document.addEventListener('DOMContentLoaded', function() {
	console.log('Page d\'inscription patient chargée');

	/**
	 * Handle form submission
	 */
	const registerForm = document.getElementById('registerForm');
	if (registerForm) {
		registerForm.addEventListener('submit', function(e) {
			e.preventDefault();
			
			if (validateRegistrationForm()) {
				console.log('Formulaire valide, soumission...');
				this.submit(); // Submit form to server
			} else {
				console.log('Validation du formulaire échouée');
			}
		});
	}
});
