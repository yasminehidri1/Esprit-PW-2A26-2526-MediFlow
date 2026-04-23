<section class="register-container">
	<!-- Decorative pattern overlay -->
	<div class="register-bg-pattern"></div>
	
	<div class="register-content">
		<!-- Logo & Title -->
		<div class="register-header" style="animation: fadeInDown 0.6s ease-out;">
			<div class="register-logo-wrapper">
				<a href="/Mediflow/" class="register-logo-link">
					<img src="assets/images/logo.png" alt="MediFlow" class="register-logo-image" onerror="this.style.display='none'" />
					<span class="register-logo-text">Medi<span class="register-logo-accent">Flow</span></span>
				</a>
			</div>
			<h1 class="register-main-title">Créer un compte</h1>
			<p class="register-main-subtitle">Rejoignez MediFlow et accédez à nos services</p>
		</div>

		<!-- Registration Card -->
		<div class="register-card" style="animation: slideUp 0.8s ease-out 0.1s both;">
			<!-- Registration Form (Patients Only) -->
			<div id="step2" class="register-step active" style="display: block; opacity: 1;">
				<h2 class="step-title">Créer votre compte Patient</h2>
				<p class="step-subtitle">Rejoignez MediFlow en tant que patient</p>

				<!-- Server-side Error Alerts -->
				<?php if (!empty($errors)): ?>
					<div class="register-alert-error" style="display: block; margin-bottom: 20px;">
						<span class="alert-icon">⚠️</span>
						<div class="alert-content">
							<p><?php echo implode('<br>', array_map('htmlspecialchars', $errors)); ?></p>
						</div>
					</div>
				<?php endif; ?>

				<!-- Client-side Error Alerts -->
				<div id="registerErrors" class="register-alert-error" style="display: none;"></div>

				<!-- Registration Form -->
				<form id="registerForm" class="register-form" method="POST" novalidate>
					<!-- First Name -->
					<div class="form-group">
						<label for="firstName" class="form-label">Prénom</label>
						<div class="form-input-group">
							<span class="form-input-icon">👤</span>
							<input 
								id="firstName" 
								name="firstName" 
								type="text" 
								class="form-input" 
								placeholder="Jean"
								required
							/>
						</div>
						<span class="form-error" id="errorFirstName"></span>
					</div>

					<!-- Last Name -->
					<div class="form-group">
						<label for="lastName" class="form-label">Nom</label>
						<div class="form-input-group">
							<span class="form-input-icon">👤</span>
							<input 
								id="lastName" 
								name="lastName" 
								type="text" 
								class="form-input" 
								placeholder="Dupont"
								required
							/>
						</div>
						<span class="form-error" id="errorLastName"></span>
					</div>

					<!-- Email -->
					<div class="form-group">
						<label for="registerEmail" class="form-label">Email</label>
						<div class="form-input-group">
							<span class="form-input-icon">✉️</span>
							<input 
								id="registerEmail" 
								name="email" 
								type="email" 
								class="form-input" 
								placeholder="vous@exemple.com"
								required
							/>
						</div>
						<span class="form-error" id="errorEmail"></span>
					</div>

					<!-- Phone (optional for patient, required for personnel) -->
					<div class="form-group">
						<label for="phone" class="form-label">Téléphone <span id="phoneRequired"></span></label>
						<div class="form-input-group">
							<span class="form-input-icon">📱</span>
							<input 
								id="phone" 
								name="phone" 
								type="tel" 
								class="form-input" 
								placeholder="+33 6 XX XX XX XX"
							/>
						</div>
						<span class="form-error" id="errorPhone"></span>
					</div>

					<!-- Password -->
					<div class="form-group">
						<label for="registerPassword" class="form-label">Mot de passe</label>
						<div class="form-input-group">
							<span class="form-input-icon">🔐</span>
							<input 
								id="registerPassword" 
								name="password" 
								type="password" 
								class="form-input" 
								placeholder="••••••••"
								required
							/>
						</div>
						<span class="form-error" id="errorPassword"></span>
						<small class="form-hint">Minimum 8 caractères avec majuscules et chiffres</small>
					</div>

					<!-- Confirm Password -->
					<div class="form-group">
						<label for="confirmPassword" class="form-label">Confirmer le mot de passe</label>
						<div class="form-input-group">
							<span class="form-input-icon">🔐</span>
							<input 
								id="confirmPassword" 
								name="confirmPassword" 
								type="password" 
								class="form-input" 
								placeholder="••••••••"
								required
							/>
						</div>
						<span class="form-error" id="errorConfirmPassword"></span>
					</div>

					<!-- Terms Agreement -->
					<div class="form-group">
						<label class="form-checkbox">
							<input type="checkbox" name="terms" id="terms" required />
							J'accepte les <a href="/Mediflow/terms" target="_blank" class="terms-link" style="color: #004d99; font-weight: 600; text-decoration: underline; cursor: pointer;">Conditions d'Utilisation</a> et la politique de confidentialité
						</label>
						<span class="form-error" id="errorTerms"></span>
					</div>

				<!-- reCAPTCHA v2 -->
			<?php 
				if (!class_exists('config')) {
					require_once __DIR__ . '/../../config.php';
				}
				$siteKey = \config::getRecaptchaSiteKey();
			?>
			<div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($siteKey); ?>" style="margin: 20px 0;"></div>

				<!-- Action Buttons -->
				<div class="register-form-actions">
					<button type="submit" class="register-btn-submit" style="width: 100%;">
						S'inscrire en tant que Patient
						<span class="btn-arrow">→</span>
					</button>
				</div>
			</form>
			</div>
		</div>

		<!-- Bottom Links -->
		<div class="register-footer-links" style="animation: fadeIn 0.8s ease-out 0.3s both;">
			<p class="register-signin-link">
				Vous avez déjà un compte? <a href="/Mediflow/login" class="link-primary">Se connecter</a>
			</p>
			<a href="/Mediflow/" class="footer-link">← Retour à l'accueil</a>
		</div>
	</div>
</section>

<!-- Google reCAPTCHA v2 Script -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script src="assets/js/register.js"></script>

<script>
	/**
	 * Handle registration form submission
	 */
	document.addEventListener('DOMContentLoaded', function() {
		const registerForm = document.getElementById('registerForm');
		
		if (registerForm) {
			registerForm.addEventListener('submit', function(e) {
				// For reCAPTCHA v2, the API automatically handles the token
				// No need to manually check anything
			});
		}
	});
</script>
