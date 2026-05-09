<?php
/**
 * Shared Magazine Newsletter Subscription Widget
 * Include in any front-office view that needs the newsletter box.
 * No variables required — fully self-contained.
 */
?>
<div class="relative overflow-hidden rounded-2xl" id="nlWidget">

  <!-- Background gradient -->
  <div class="absolute inset-0 bg-gradient-to-br from-primary via-blue-700 to-sky-600 z-0"></div>
  <!-- Decorative blobs -->
  <div class="absolute -right-8 -top-8 w-44 h-44 bg-white/10 rounded-full blur-3xl z-0"></div>
  <div class="absolute -left-6 -bottom-10 w-36 h-36 bg-teal-400/20 rounded-full blur-2xl z-0"></div>

  <div class="relative z-10 p-7">

    <!-- Icon + heading -->
    <div class="flex items-center gap-3 mb-4">
      <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-white text-xl" style="font-variation-settings:'FILL' 1">mark_email_read</span>
      </div>
      <h4 class="font-headline text-lg font-extrabold text-white leading-tight">
        Stay in the loop
      </h4>
    </div>

    <p class="text-sm text-white/75 leading-relaxed mb-5">
      Get notified by email the moment a new health article is published on MediFlow Magazine. No spam — just great content.
    </p>

    <!-- Benefits pills -->
    <div class="flex flex-wrap gap-2 mb-5">
      <span class="flex items-center gap-1 px-2.5 py-1 bg-white/10 rounded-full text-[11px] text-white/80 font-medium">
        <span class="material-symbols-outlined text-[13px]">check_circle</span> Instant alerts
      </span>
      <span class="flex items-center gap-1 px-2.5 py-1 bg-white/10 rounded-full text-[11px] text-white/80 font-medium">
        <span class="material-symbols-outlined text-[13px]">check_circle</span> Free
      </span>
      <span class="flex items-center gap-1 px-2.5 py-1 bg-white/10 rounded-full text-[11px] text-white/80 font-medium">
        <span class="material-symbols-outlined text-[13px]">check_circle</span> Unsubscribe anytime
      </span>
    </div>

    <!-- Form -->
    <form id="nlForm" novalidate>
      <div class="space-y-2.5">
        <div class="relative">
          <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-white/40 text-[18px]">mail</span>
          <input id="nlEmail" type="email" autocomplete="email" required
                 placeholder="your@email.com"
                 class="w-full bg-white/10 border border-white/20 rounded-xl py-3 pl-10 pr-4
                        text-sm text-white placeholder:text-white/40
                        focus:outline-none focus:ring-2 focus:ring-white/30 focus:border-white/40
                        transition-all"/>
        </div>
        <button id="nlBtn" type="submit"
                class="w-full flex items-center justify-center gap-2
                       bg-white text-primary font-headline font-bold
                       py-3 rounded-xl text-sm
                       hover:bg-white/90 active:scale-[0.98]
                       transition-all duration-200 shadow-lg shadow-black/10">
          <span class="material-symbols-outlined text-[17px]" id="nlBtnIcon">send</span>
          <span id="nlBtnText">Subscribe Now</span>
        </button>
      </div>
    </form>

    <!-- Success state (hidden initially) -->
    <div id="nlSuccess" class="hidden text-center py-2">
      <div class="inline-flex items-center gap-2.5 px-5 py-3 bg-white/15 rounded-xl">
        <span class="material-symbols-outlined text-tertiary-fixed text-xl" style="font-variation-settings:'FILL' 1">check_circle</span>
        <div class="text-left">
          <p class="text-sm font-bold text-white leading-none mb-0.5" id="nlSuccessTitle">You're subscribed!</p>
          <p class="text-[11px] text-white/65" id="nlSuccessMsg">Check your inbox for a welcome email.</p>
        </div>
      </div>
    </div>

    <!-- Error state (hidden initially) -->
    <div id="nlError" class="hidden mt-2 px-4 py-2.5 bg-red-900/30 border border-red-400/30 rounded-xl">
      <p class="text-[12px] text-red-200" id="nlErrorMsg">Something went wrong. Please try again.</p>
    </div>

    <p class="text-[10px] text-white/35 text-center mt-4">
      By subscribing you agree to receive email notifications about new MediFlow Magazine articles.
    </p>
  </div>
</div>

<script>
(function () {
    const form        = document.getElementById('nlForm');
    const emailInput  = document.getElementById('nlEmail');
    const btn         = document.getElementById('nlBtn');
    const btnIcon     = document.getElementById('nlBtnIcon');
    const btnText     = document.getElementById('nlBtnText');
    const successBox  = document.getElementById('nlSuccess');
    const successTitle = document.getElementById('nlSuccessTitle');
    const successMsg  = document.getElementById('nlSuccessMsg');
    const errorBox    = document.getElementById('nlErrorMsg');
    const errorWrap   = document.getElementById('nlError');

    if (!form) return;

    function setLoading(on) {
        btn.disabled     = on;
        btn.style.opacity = on ? '0.7' : '1';
        btnIcon.textContent = on ? 'hourglass_top' : 'send';
        btnText.textContent = on ? 'Subscribing…' : 'Subscribe Now';
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        errorWrap.classList.add('hidden');

        const email = emailInput.value.trim();
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            emailInput.focus();
            emailInput.style.borderColor = 'rgba(248,113,113,0.8)';
            setTimeout(() => { emailInput.style.borderColor = ''; }, 2000);
            return;
        }

        setLoading(true);
        try {
            const fd = new FormData();
            fd.append('email', email);
            const res  = await fetch('/integration/magazine/newsletter/subscribe', { method: 'POST', body: fd });
            const data = await res.json();

            if (data.success) {
                form.classList.add('hidden');
                successBox.classList.remove('hidden');

                if (data.status === 'already_subscribed') {
                    successTitle.textContent = 'Already subscribed';
                    successMsg.textContent   = 'This email is already on our list.';
                } else {
                    successTitle.textContent = "You're subscribed!";
                    successMsg.textContent   = 'Check your inbox for a welcome email.';
                }
            } else {
                errorBox.textContent = data.message || 'Something went wrong. Please try again.';
                errorWrap.classList.remove('hidden');
                setLoading(false);
            }
        } catch (err) {
            errorBox.textContent = 'Network error. Please try again.';
            errorWrap.classList.remove('hidden');
            setLoading(false);
        }
    });
})();
</script>
