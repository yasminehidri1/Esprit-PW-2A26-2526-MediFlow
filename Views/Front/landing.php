<!-- Inline Custom Styles for Animations & Effects -->
<style>
    /* Custom Gradients & Shadows */
    .bg-mesh {
        background-color: #f7f9fb;
        background-image: 
            radial-gradient(at 0% 0%, hsla(213, 100%, 93%, 1) 0, transparent 50%), 
            radial-gradient(at 50% 0%, hsla(225, 100%, 96%, 1) 0, transparent 50%), 
            radial-gradient(at 100% 0%, hsla(213, 100%, 93%, 1) 0, transparent 50%);
    }
    
    .glass-panel {
        background: rgba(255, 255, 255, 0.65);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        box-shadow: 0 8px 32px 0 rgba(0, 77, 153, 0.05);
    }

    .glass-dark {
        background: rgba(15, 23, 42, 0.75);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 16px 40px -8px rgba(0,0,0,0.5);
    }

    /* Gradient Text */
    .text-gradient {
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-image: linear-gradient(135deg, #004d99 0%, #3b82f6 100%);
    }
    .text-gradient-light {
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-image: linear-gradient(135deg, #60a5fa 0%, #e0e7ff 100%);
    }

    /* Animations */
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
    }
    .animate-float { animation: float 6s ease-in-out infinite; }
    
    @keyframes pulse-glow {
        0%, 100% { opacity: 0.5; transform: scale(1); }
        50% { opacity: 0.8; transform: scale(1.05); }
    }
    .animate-pulse-glow { animation: pulse-glow 4s ease-in-out infinite; }

    @keyframes slideUpFade {
        0% { opacity: 0; transform: translateY(30px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animate-entry { animation: slideUpFade 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
    .delay-400 { animation-delay: 0.4s; }

    /* Bento Grid Hover Effects */
    .bento-card {
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .bento-card:hover {
        transform: translateY(-5px) scale(1.01);
        box-shadow: 0 20px 40px -10px rgba(0, 77, 153, 0.12);
        z-index: 10;
    }
    
    /* Glowing orb behind AI section */
    .orb-bg {
        position: absolute;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(59,130,246,0.15) 0%, rgba(0,0,0,0) 70%);
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        pointer-events: none;
        z-index: 0;
    }
</style>

<!-- ===== HERO SECTION ===== -->
<section class="relative min-h-[90vh] flex items-center justify-center bg-mesh overflow-hidden pb-20 pt-10">
    <!-- Decorative floating elements -->
    <div class="absolute top-20 left-10 w-64 h-64 bg-blue-400/20 rounded-full blur-[80px] animate-pulse-glow"></div>
    <div class="absolute bottom-20 right-10 w-72 h-72 bg-indigo-400/20 rounded-full blur-[80px] animate-pulse-glow" style="animation-delay: 2s;"></div>

    <div class="max-w-7xl mx-auto px-6 w-full relative z-10">
        <div class="flex flex-col lg:flex-row items-center gap-16">
            
            <!-- Hero Content -->
            <div class="flex-1 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-100/50 border border-blue-200/50 text-blue-700 font-bold text-xs uppercase tracking-widest mb-6 animate-entry">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-600"></span>
                    </span>
                    Santé Connectée 2.0
                </div>
                
                <h1 class="text-5xl md:text-6xl lg:text-7xl font-black font-headline tracking-tight text-slate-900 leading-[1.1] mb-6 animate-entry delay-100">
                    Votre hôpital, <br />
                    <span class="text-gradient">réinventé.</span>
                </h1>
                
                <p class="text-lg md:text-xl text-slate-600 mb-10 max-w-2xl mx-auto lg:mx-0 leading-relaxed font-medium animate-entry delay-200">
                    La plateforme complète pour moderniser la gestion médicale. Dossiers patients, rendez-vous, analyses et intelligence artificielle réunis dans une interface intuitive.
                </p>
                
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 animate-entry delay-300">
                    <a href="/integration/login" class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-primary to-blue-600 text-white font-bold rounded-2xl shadow-[0_8px_20px_rgba(0,77,153,0.3)] hover:shadow-[0_12px_25px_rgba(0,77,153,0.4)] hover:-translate-y-1 transition-all flex items-center justify-center gap-2">
                        Démarrer l'expérience <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                    <a href="#modules" class="w-full sm:w-auto px-8 py-4 bg-white text-slate-700 font-bold rounded-2xl shadow-sm border border-slate-200 hover:bg-slate-50 hover:-translate-y-1 transition-all flex items-center justify-center gap-2">
                        Découvrir les modules
                    </a>
                </div>
                
                <div class="mt-12 flex items-center justify-center lg:justify-start gap-6 text-slate-500 text-sm font-semibold animate-entry delay-400">
                    <div class="flex items-center gap-2"><span class="material-symbols-outlined text-emerald-500">check_circle</span> 100% Sécurisé</div>
                    <div class="flex items-center gap-2"><span class="material-symbols-outlined text-emerald-500">check_circle</span> Conformité RGPD</div>
                    <div class="flex items-center gap-2"><span class="material-symbols-outlined text-emerald-500">check_circle</span> Support 24/7</div>
                </div>
            </div>

            <!-- Hero Visual -->
            <div class="flex-1 relative w-full max-w-lg lg:max-w-none animate-entry delay-200">
                <div class="relative w-full aspect-square md:aspect-video lg:aspect-square">
                    <!-- Main Dashboard Mockup (Glassmorphism) -->
                    <div class="absolute inset-0 glass-panel rounded-3xl p-6 flex flex-col z-20 animate-float border border-white/60">
                        <!-- Mockup Header -->
                        <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-200/50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">DR</div>
                                <div>
                                    <div class="h-3 w-24 bg-slate-200 rounded-full mb-2"></div>
                                    <div class="h-2 w-16 bg-slate-100 rounded-full"></div>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <div class="w-8 h-8 rounded-full bg-slate-100"></div>
                                <div class="w-8 h-8 rounded-full bg-slate-100"></div>
                            </div>
                        </div>
                        
                        <!-- Mockup Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-white/50 p-4 rounded-2xl border border-white/50">
                                <div class="h-2 w-16 bg-blue-200 rounded-full mb-3"></div>
                                <div class="h-6 w-20 bg-slate-800 rounded-full mb-1"></div>
                            </div>
                            <div class="bg-white/50 p-4 rounded-2xl border border-white/50">
                                <div class="h-2 w-16 bg-emerald-200 rounded-full mb-3"></div>
                                <div class="h-6 w-20 bg-slate-800 rounded-full mb-1"></div>
                            </div>
                        </div>

                        <!-- Mockup List -->
                        <div class="flex-1 flex flex-col gap-3">
                            <div class="h-12 w-full bg-white/60 rounded-xl border border-white/40 flex items-center px-4 gap-3">
                                <div class="w-6 h-6 rounded-full bg-rose-100"></div>
                                <div class="h-2 w-1/3 bg-slate-200 rounded-full"></div>
                                <div class="h-2 w-1/4 bg-slate-100 rounded-full ml-auto"></div>
                            </div>
                            <div class="h-12 w-full bg-white/60 rounded-xl border border-white/40 flex items-center px-4 gap-3">
                                <div class="w-6 h-6 rounded-full bg-indigo-100"></div>
                                <div class="h-2 w-1/2 bg-slate-200 rounded-full"></div>
                                <div class="h-2 w-1/5 bg-slate-100 rounded-full ml-auto"></div>
                            </div>
                            <div class="h-12 w-full bg-white/60 rounded-xl border border-white/40 flex items-center px-4 gap-3">
                                <div class="w-6 h-6 rounded-full bg-amber-100"></div>
                                <div class="h-2 w-1/4 bg-slate-200 rounded-full"></div>
                                <div class="h-2 w-1/3 bg-slate-100 rounded-full ml-auto"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Floating Accent Card 1 -->
                    <div class="absolute -left-8 top-1/4 glass-panel p-4 rounded-2xl z-30 shadow-xl animate-float" style="animation-delay: -2s;">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                                <span class="material-symbols-outlined text-emerald-600 text-lg">medical_services</span>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-800">Équipement</p>
                                <p class="text-[10px] text-slate-500">Location validée</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Floating Accent Card 2 -->
                    <div class="absolute -right-6 bottom-1/4 glass-panel p-4 rounded-2xl z-30 shadow-xl animate-float" style="animation-delay: -4s;">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <span class="material-symbols-outlined text-blue-600 text-lg">event_available</span>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-800">Rendez-vous</p>
                                <p class="text-[10px] text-slate-500">Confirmé: 14h30</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ===== STATS BANNER ===== -->
<section class="py-10 border-y border-slate-200 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 divide-x divide-slate-100 text-center">
            <div>
                <p class="text-3xl font-black text-slate-900 mb-1">200<span class="text-primary">+</span></p>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Hôpitaux</p>
            </div>
            <div>
                <p class="text-3xl font-black text-slate-900 mb-1">1.5<span class="text-primary">M</span></p>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Patients Gérés</p>
            </div>
            <div>
                <p class="text-3xl font-black text-slate-900 mb-1">40<span class="text-primary">%</span></p>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Gain de Temps</p>
            </div>
            <div>
                <p class="text-3xl font-black text-slate-900 mb-1">24<span class="text-primary">/7</span></p>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Disponibilité</p>
            </div>
        </div>
    </div>
</section>

<!-- ===== BENTO GRID (MODULES) ===== -->
<section id="modules" class="py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <h2 class="text-sm font-bold text-primary tracking-widest uppercase mb-3">Écosystème Intégré</h2>
            <h3 class="text-3xl md:text-4xl font-black text-slate-900 font-headline mb-4">6 modules pour tout gérer</h3>
            <p class="text-slate-500 text-lg">Plus besoin d'utiliser plusieurs logiciels. MediFlow centralise l'ensemble de votre workflow médical et administratif.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 auto-rows-[250px]">
            <!-- Module 1: Dossier Médical (Large) -->
            <div class="bento-card md:col-span-2 bg-white rounded-3xl p-8 border border-slate-200 overflow-hidden relative group">
                <div class="absolute right-0 top-0 w-64 h-64 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-bl-full -z-10 group-hover:scale-110 transition-transform duration-500"></div>
                <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 mb-6">
                    <span class="material-symbols-outlined text-2xl">folder_shared</span>
                </div>
                <h4 class="text-2xl font-black text-slate-900 mb-2">Dossier Médical Électronique</h4>
                <p class="text-slate-500 max-w-md">Historique des consultations, ordonnances, antécédents et allergies accessibles en un clic par les professionnels autorisés.</p>
                <span class="absolute bottom-8 right-8 text-slate-200 material-symbols-outlined text-8xl opacity-30 transform group-hover:-rotate-12 transition-transform duration-500">medical_information</span>
            </div>

            <!-- Module 2: Rendez-vous -->
            <div class="bento-card bg-white rounded-3xl p-8 border border-slate-200 overflow-hidden relative group">
                <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600 mb-6">
                    <span class="material-symbols-outlined text-2xl">calendar_month</span>
                </div>
                <h4 class="text-xl font-black text-slate-900 mb-2">Rendez-vous</h4>
                <p class="text-slate-500 text-sm">Prise de RDV en ligne, rappels automatiques et gestion optimisée des files d'attente.</p>
            </div>

            <!-- Module 3: Magazine Médical -->
            <div class="bento-card bg-slate-900 text-white rounded-3xl p-8 border border-slate-800 overflow-hidden relative group">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/20 to-purple-500/20 z-0"></div>
                <div class="relative z-10">
                    <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-white mb-6 backdrop-blur-sm border border-white/10">
                        <span class="material-symbols-outlined text-2xl">newspaper</span>
                    </div>
                    <h4 class="text-xl font-black mb-2">Magazine Médical</h4>
                    <p class="text-slate-300 text-sm">Portail d'information santé avec articles validés par des professionnels, commentaires et gestion éditoriale complète.</p>
                </div>
            </div>

            <!-- Module 4: Utilisateurs & Audit -->
            <div class="bento-card bg-white rounded-3xl p-8 border border-slate-200 overflow-hidden relative group">
                <div class="w-12 h-12 bg-purple-100 rounded-2xl flex items-center justify-center text-purple-600 mb-6">
                    <span class="material-symbols-outlined text-2xl">admin_panel_settings</span>
                </div>
                <h4 class="text-xl font-black text-slate-900 mb-2">Audit & Accès</h4>
                <p class="text-slate-500 text-sm">Traçabilité complète des actions, gestion des rôles (Admin, Médecin, Patient) et sécurité maximale.</p>
            </div>

            <!-- Module 5: Stock & Fournisseurs -->
            <div class="bento-card bg-white rounded-3xl p-8 border border-slate-200 overflow-hidden relative group">
                <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center text-amber-600 mb-6">
                    <span class="material-symbols-outlined text-2xl">local_shipping</span>
                </div>
                <h4 class="text-xl font-black text-slate-900 mb-2">Stock & Fournisseurs</h4>
                <p class="text-slate-500 text-sm">Suivi des commandes médicaments, gestion du catalogue fournisseurs et alertes de stock en temps réel.</p>
            </div>
            
            <!-- Module 6: Location d'Équipements -->
            <div class="bento-card bg-white rounded-3xl p-8 border border-slate-200 overflow-hidden relative group md:col-span-3 lg:col-span-1">
                <div class="w-12 h-12 bg-rose-100 rounded-2xl flex items-center justify-center text-rose-600 mb-6">
                    <span class="material-symbols-outlined text-2xl">medical_services</span>
                </div>
                <h4 class="text-xl font-black text-slate-900 mb-2">Location d'Équipement</h4>
                <p class="text-slate-500 text-sm">Catalogue interactif de matériel médical pour les patients, avec suivi de l'historique de location et gestion des retours.</p>
            </div>
        </div>
    </div>
</section>

<!-- ===== AI SECTION (DARK PREMIUM) ===== -->
<section class="relative py-32 bg-slate-900 overflow-hidden text-white">
    <div class="orb-bg"></div>
    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0IiBoZWlnaHQ9IjQiPjxyZWN0IHdpZHRoPSI0IiBoZWlnaHQ9IjQiIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSIvPjwvc3ZnPg==')] opacity-30"></div>
    
    <div class="max-w-7xl mx-auto px-6 relative z-10 flex flex-col md:flex-row items-center gap-16">
        <div class="flex-1">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 font-bold text-xs uppercase tracking-widest mb-6">
                <span class="material-symbols-outlined text-sm">auto_awesome</span> Intelligence Artificielle
            </div>
            <h2 class="text-4xl md:text-5xl font-black font-headline mb-6">
                L'assistant médical de demain, <span class="text-gradient-light">aujourd'hui.</span>
            </h2>
            <p class="text-slate-400 text-lg mb-8 leading-relaxed">
                Notre intégration poussée de l'API Gemini analyse instantanément vos données textuelles, met en évidence les points clés et assiste les praticiens en générant des synthèses pertinentes et rapides pour le dossier patient et le magazine médical.
            </p>
            
            <ul class="space-y-4 mb-10">
                <li class="flex items-center gap-3 text-slate-300">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center border border-white/10 text-emerald-400"><span class="material-symbols-outlined text-sm">bolt</span></div>
                    <span>Synthèse intelligente des dossiers</span>
                </li>
                <li class="flex items-center gap-3 text-slate-300">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center border border-white/10 text-emerald-400"><span class="material-symbols-outlined text-sm">edit_note</span></div>
                    <span>Aide à la rédaction d'articles médicaux</span>
                </li>
                <li class="flex items-center gap-3 text-slate-300">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center border border-white/10 text-emerald-400"><span class="material-symbols-outlined text-sm">summarize</span></div>
                    <span>Génération automatique de rapports vulgarisés</span>
                </li>
            </ul>
        </div>
        
        <div class="flex-1 w-full max-w-md mx-auto">
            <div class="glass-dark p-6 rounded-3xl border border-slate-700">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-700">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-400">psychiatry</span>
                        <span class="font-bold text-sm text-slate-200">Synthèse Patient</span>
                    </div>
                    <span class="px-2 py-1 bg-emerald-500/20 text-emerald-400 text-[10px] font-bold rounded">Terminé</span>
                </div>
                
                <div class="space-y-4">
                    <div class="p-3 bg-slate-800/50 rounded-xl border border-slate-700/50 flex justify-between items-center">
                        <div>
                            <p class="text-xs text-slate-400 mb-1">Dernière consultation</p>
                            <p class="font-bold text-sm">Céphalées chroniques et fatigue</p>
                        </div>
                    </div>
                    
                    <div class="mt-6 p-4 bg-blue-500/10 border border-blue-500/20 rounded-xl relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-blue-400 mt-0.5">smart_toy</span>
                            <div>
                                <p class="text-xs font-bold text-blue-300 mb-1">Analyse IA (Gemini)</p>
                                <p class="text-sm text-slate-300 leading-relaxed">Les antécédents montrent des prescriptions fréquentes d'antalgiques. Il est recommandé d'explorer des pistes de prévention (hydratation, suivi du sommeil) avant un nouveau traitement lourd.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== CALL TO ACTION (BOTTOM) ===== -->
<section class="py-24 bg-white relative overflow-hidden">
    <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>
    <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
        <h2 class="text-4xl md:text-5xl font-black font-headline text-slate-900 mb-6">
            Prêt à transformer votre établissement de santé ?
        </h2>
        <p class="text-xl text-slate-500 mb-10 max-w-2xl mx-auto">
            Rejoignez l'écosystème MediFlow et offrez à vos patients et à votre personnel une expérience fluide, sécurisée et intelligente.
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="/integration/register" class="px-8 py-4 bg-slate-900 text-white font-bold rounded-2xl hover:bg-primary transition-colors shadow-xl hover:shadow-2xl hover:-translate-y-1">
                Créer mon compte hôpital
            </a>
            <a href="/integration/login" class="px-8 py-4 bg-blue-50 text-blue-700 font-bold rounded-2xl border border-blue-100 hover:bg-blue-100 transition-colors">
                Se connecter
            </a>
        </div>
    </div>
</section>
