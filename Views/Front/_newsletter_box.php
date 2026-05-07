<?php
$newsletterRedirect = $_SERVER['REQUEST_URI'] ?? '/integration/magazine';
?>

<div class="bg-primary rounded-xl p-8 text-white relative overflow-hidden" id="newsletter-box">
  <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
  <div class="absolute -left-6 -bottom-8 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
  <h4 class="font-headline text-xl font-bold mb-3 relative z-10">📰 Medical Excellence in your inbox.</h4>
  <p class="text-on-primary-container text-sm mb-6 relative z-10 opacity-90">
    Get notified by email when we publish a new article.
  </p>

  <form id="newsletter-form" class="space-y-3 relative z-10">
    <input type="email"
           id="newsletter-email"
           required
           class="w-full bg-white/10 border-none rounded-lg py-3 px-4 text-sm placeholder:text-white/60 focus:ring-2 focus:ring-tertiary-fixed/30 text-white"
           placeholder="Your email address"
           autocomplete="email"/>
    <button type="submit"
            id="newsletter-btn"
            class="w-full bg-tertiary-fixed text-on-tertiary-fixed font-headline font-bold py-3 rounded-lg hover:bg-tertiary-fixed-dim transition-all active:scale-[0.98]">
      Subscribe
    </button>
  </form>

  <p class="text-[10px] mt-4 text-center text-white/50">You can unsubscribe anytime.</p>
</div>

<script>
document.getElementById('newsletter-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const email = document.getElementById('newsletter-email').value;
  const btn = document.getElementById('newsletter-btn');
  const form = document.getElementById('newsletter-form');
  
  if (!email) {
    showToast('Please enter your email', 'error');
    return;
  }
  
  try {
    btn.disabled = true;
    btn.innerHTML = '<span style="display:inline-block;width:14px;height:14px;border:2px solid #fff;border-top-color:transparent;border-radius:50%;animation:spin .6s linear infinite;vertical-align:middle;margin-right:6px;"></span>Subscribing...';
    
    const response = await fetch('/integration/subscription/subscribe', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ email: email })
    });
    
    const data = await response.json();
    
    if (data.success) {
      showToast(data.message, 'success');
      form.reset();
      setTimeout(() => {
        btn.innerHTML = 'Subscribe';
        btn.disabled = false;
      }, 2000);
    } else {
      showToast(data.message, 'error');
      btn.innerHTML = 'Subscribe';
      btn.disabled = false;
    }
  } catch (err) {
    console.error('Subscription error:', err);
    showToast('Could not subscribe. Please try again.', 'error');
    btn.innerHTML = 'Subscribe';
    btn.disabled = false;
  }
});
</script>
