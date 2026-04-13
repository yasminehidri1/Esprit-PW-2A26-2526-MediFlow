<section class="login-container">
	<!-- Decorative pattern overlay -->
	<div class="login-bg-pattern"></div>
	
	<div class="login-content">
		<!-- Logo & Title -->
		<div class="login-header" style="animation: fadeInDown 0.6s ease-out;">
			<div class="login-logo-wrapper">
				<a href="/Mediflow/" class="login-logo-link">
					<img src="assets/images/logo.png" alt="MediFlow" class="login-logo-image" onerror="this.style.display='none'" />
					<span class="login-logo-text">Medi<span class="login-logo-accent">Flow</span></span>
				</a>
			</div>
			<h1 class="login-main-title">Connexion</h1>
			<p class="login-main-subtitle">Accédez à votre compte MediFlow</p>
		</div>

		<!-- Main Login Card -->
		<div class="login-card" style="animation: slideUp 0.8s ease-out 0.1s both;">
			<!-- Error Alerts -->
			<?php if (!empty($errors)): ?>
				<div class="login-alert-error" role="alert" style="animation: slideDown 0.3s ease">
					<span class="alert-icon">⚠️</span>
					<div class="alert-content">
						<?php foreach ($errors as $error): ?>
							<p><?php echo htmlspecialchars($error); ?></p>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<!-- Login Form -->
			<form method="post" action="" class="login-form" novalidate>
				<!-- Email Field -->
				<div class="form-group">
					<label for="email" class="form-label">Email</label>
					<div class="form-input-group">
						<span class="form-input-icon">✉️</span>
						<input 
							id="email" 
							name="username" 
							type="email" 
							class="form-input" 
							placeholder="vous@exemple.com"
							autocomplete="email"
							required
						/>
					</div>
				</div>

				<!-- Password Field -->
				<div class="form-group">
					<div class="form-label-row">
						<label for="password" class="form-label">Mot de passe</label>
						<a href="#" class="form-forgot-link">Oublié?</a>
					</div>
					<div class="form-input-group">
						<span class="form-input-icon">🔐</span>
						<input 
							id="password" 
							name="password" 
							type="password" 
							class="form-input" 
							placeholder="••••••••"
							autocomplete="current-password"
							required
						/>
					</div>
				</div>

				<!-- Remember Me -->
				<div class="form-group">
					<label class="form-checkbox">
						<input type="checkbox" name="remember" />
						Se souvenir de moi
					</label>
				</div>

				<!-- Submit Button -->
				<button class="login-btn-submit" type="submit">
					<span>Se Connecter</span>
					<span class="btn-arrow">→</span>
				</button>
			</form>

			<!-- Sign Up Section -->
			<div class="login-divider"></div>
			<p class="login-signup-prompt">
				Pas encore de compte? <a href="/Mediflow/register" class="login-signup-link">S'inscrire</a>
			</p>

			<!-- Footer Info -->
			<p class="login-footer-text">
				✓ Sécurisé et RGPD compliant
			</p>
		</div>

		<!-- Back to Home -->
		<div class="login-footer-links" style="animation: fadeIn 0.8s ease-out 0.3s both;">
			<a href="/Mediflow/" class="footer-link">← Retour à l'accueil</a>
		</div>
	</div>
</section>

<script>
	/**
	 * Handle login form submission
	 */
	document.addEventListener('DOMContentLoaded', function() {
		const loginForm = document.querySelector('.login-form');
		if (loginForm) {
			loginForm.addEventListener('submit', function(e) {
				// Form submission handled by PHP backend
				console.log('Login form submitted');
			});
		}
	});
</script>
