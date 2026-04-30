<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$embeddedInLayout = $embeddedInLayout ?? false;

// Base URLs selon le rôle
$stockRole      = $_SESSION['user']['role'] ?? '';
$baseProductUrl = '/integration/stock/products';
$baseOrderUrl   = '/integration/stock/orders';
$baseCartUrl    = '/integration/stock/cart';
$orderCreateUrl = '/integration/stock/orders/create';
$orderUpdateUrl = '/integration/stock/orders/create';

if (in_array($stockRole, ['Admin', 'Fournisseur'])) {
    $baseProductUrl = '/integration/fournisseur/products';
    $baseOrderUrl   = '/integration/fournisseur/orders';
    $baseCartUrl    = '/integration/stock/cart'; // panier reste stock
    $orderCreateUrl = '/integration/stock/orders/create';
}

// Commande en cours de modification
$editOrderId   = isset($_GET['edit_order']) ? intval($_GET['edit_order']) : null;
$editOrderData = null;
if ($editOrderId) {
    try {
        require_once __DIR__ . '/../../Models/Order.php';
        require_once __DIR__ . '/../../config.php';
        $orderModel    = new Order();
        $editOrderData = $orderModel->getOrderWithLines($editOrderId);
    } catch (Exception $e) {
        error_log('Erreur chargement commande: ' . $e->getMessage());
    }
}
?>
<?php if (!$embeddedInLayout): ?>
<!DOCTYPE html>
<html class="light" lang="fr">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Panier - Mediflow | Stock Management</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script>tailwind.config={darkMode:"class",theme:{extend:{colors:{"primary":"#004d99","on-primary":"#ffffff","surface":"#f7f9fb","surface-container-low":"#f2f4f6","surface-container":"#eceef0","surface-container-high":"#e6e8ea","surface-container-highest":"#e0e3e5","on-surface":"#191c1e","secondary":"#5c5f61","outline":"#727783","outline-variant":"#c2c6d4","error":"#ba1a1a","error-container":"#ffdad6","primary-fixed":"#d6e3ff"}}}}</script>
    <style>body{font-family:'Inter',sans-serif;background-color:#f7f9fb}.material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24}</style>
</head>
<body class="bg-surface text-on-surface">
<!-- SideNavBar -->
<aside class="h-screen w-64 fixed left-0 top-0 bg-slate-50 flex flex-col py-6 z-50">
    <div class="px-6 mb-10">
        <h1 class="text-xl font-bold text-blue-800 font-['Manrope']">MediFlow</h1>
        <p class="text-xs text-slate-500 font-medium tracking-wider uppercase mt-1">Stock Management</p>
    </div>
    <nav class="flex-1 space-y-1">
        <a class="flex items-center gap-3 px-4 py-3 text-slate-500 font-['Manrope'] font-bold text-sm hover:bg-slate-100 transition-colors" href="<?= $baseProductUrl ?>">
            <span class="material-symbols-outlined">inventory_2</span><span>Produits</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 bg-white text-blue-700 border-l-4 border-teal-600 font-['Manrope'] font-bold text-sm" href="<?= $baseCartUrl ?>">
            <span class="material-symbols-outlined">shopping_cart</span><span>Panier</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 text-slate-500 font-['Manrope'] font-bold text-sm hover:bg-slate-100 transition-colors" href="<?= $baseOrderUrl ?>">
            <span class="material-symbols-outlined">receipt</span><span>Commandes</span>
        </a>
    </nav>
</aside>
<!-- TopNavBar standalone -->
<header class="fixed top-0 right-0 left-64 z-40 flex justify-between items-center px-8 py-3 rounded-2xl mt-4 mx-4 bg-white/80 backdrop-blur-md shadow-[0_20px_50px_rgba(0,77,153,0.05)] font-['Manrope'] font-semibold">
    <div>
        <h2 class="text-2xl font-extrabold text-on-surface font-headline tracking-tight mb-1">Panier</h2>
        <p class="text-secondary font-body text-sm">Préparez votre commande de médicaments</p>
    </div>
</header>
<?php else: ?>
<!-- Breadcrumb embarqué -->
<div class="flex items-center gap-3 mb-6">
    <a href="<?= $baseProductUrl ?>" class="flex items-center gap-2 text-secondary hover:text-primary transition-colors text-sm font-medium">
        <span class="material-symbols-outlined text-base">arrow_back</span>
        Retour aux produits
    </a>
    <span class="text-outline/40">/</span>
    <span class="text-on-surface font-bold text-sm">Panier</span>
</div>
<?php endif; ?>

<!-- Main Content -->
<main class="pl-64 pt-28 min-h-screen bg-surface">
    <div class="max-w-7xl mx-auto px-8 pb-12">
        <?php if ($editOrderId && $editOrderData): ?>
        <!-- Bande info modification -->
        <div class="mb-6 p-4 bg-blue-50 border-l-4 border-primary rounded-xl flex items-center gap-3">
            <span class="material-symbols-outlined text-primary text-2xl">edit_note</span>
            <div>
                <p class="font-bold text-on-surface">Modification de la commande #<?php echo $editOrderId; ?></p>
                <p class="text-sm text-secondary">Modifiez les articles et enregistrez vos changements</p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Panier vide -->
        <div id="empty-cart" class="text-center py-24">
            <div class="text-6xl mb-4">📦</div>
            <h2 class="text-3xl font-extrabold text-on-surface font-headline tracking-tight mb-2">Votre panier est vide</h2>
            <p class="text-secondary font-body mb-6 max-w-md mx-auto">Commencez par ajouter des produits à votre panier</p>
            <a href="<?= $baseProductUrl ?>" class="inline-block px-6 py-3 bg-primary text-white rounded-xl hover:bg-primary/90 font-bold transition-colors">
                🛍️ Ajouter des produits
            </a>
        </div>

        <!-- Panier avec produits -->
        <div id="cart-content" class="hidden">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Colonne gauche : Articles du Panier -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <h2 class="text-xl font-bold text-on-surface mb-6 flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">shopping_cart</span>
                            Articles du Panier
                        </h2>
                        
                        <div id="cart-items" class="space-y-3">
                            <!-- Générés par JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- Colonne droite : Résumé -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-sm p-6 sticky top-32">
                        <h2 class="text-xl font-bold text-on-surface mb-6">Résumé de la Commande</h2>
                        
                        <div class="space-y-4 mb-6 pb-6 border-b border-surface-container">
                            <div class="flex justify-between">
                                <span class="text-body text-secondary">Nombre d'articles</span>
                                <span class="font-bold text-on-surface" id="summary-items">0</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-body text-secondary">Quantité totale</span>
                                <span class="font-bold text-on-surface" id="summary-qty">0</span>
                            </div>
                        </div>

                        <div class="mb-6 pb-6 border-b border-surface-container">
                            <div class="flex justify-between items-end">
                                <div class="text-body text-secondary">Total</div>
                                <div>
                                    <span class="text-3xl font-bold text-primary" id="summary-total">0.00</span>
                                    <span class="text-on-surface ml-1">DT</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <button onclick="checkout()" class="w-full px-4 py-3 bg-primary text-on-primary font-bold rounded-lg hover:bg-primary/90 flex items-center justify-center gap-2 transition-colors">
                                <span class="material-symbols-outlined">check_circle</span>
                                Valider la Commande
                            </button>
                            <button onclick="clearCartConfirm()" class="w-full px-4 py-3 bg-surface-container text-on-surface font-bold rounded-lg hover:bg-surface-container-high flex items-center justify-center gap-2 transition-colors">
                                <span class="material-symbols-outlined">delete</span>
                                Vider le Panier
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                </div>
            </div>
        </div>
    </main>

    <script src="/integration/assets/js/cart.js"></script>
    <script>
        /**
         * Affiche le panier
         */
        function displayCart() {
            const cartData = cart.getCart();
            const emptyCart = document.getElementById('empty-cart');
            const cartContent = document.getElementById('cart-content');
            const cartItems = document.getElementById('cart-items');

            if (cartData.length === 0) {
                emptyCart.classList.remove('hidden');
                cartContent.classList.add('hidden');
                return;
            }

            emptyCart.classList.add('hidden');
            cartContent.classList.remove('hidden');

            cartItems.innerHTML = '';

            cartData.forEach(item => {
                const itemElement = document.createElement('div');
                itemElement.className = 'flex items-center justify-between p-4 bg-surface-container-low rounded-xl border border-surface-container hover:border-primary/30 transition';
                itemElement.innerHTML = `
                    <div class="flex-1">
                        <h3 class="font-bold text-on-surface">${htmlEscape(item.nom)}</h3>
                        <p class="text-xs text-secondary mb-3">
                            <span class="px-2.5 py-0.5 bg-primary-fixed text-primary rounded-full">${htmlEscape(item.categorie)}</span>
                        </p>
                        <div class="flex items-center gap-2">
                            <button onclick="updateItemQuantity(${item.id}, ${item.quantite - 1})" class="px-2 py-1 bg-surface-container text-on-surface hover:bg-surface-container-high rounded font-bold">−</button>
                            <input type="number" value="${item.quantite}" onchange="updateItemQuantity(${item.id}, this.value)" class="w-12 px-2 py-1 border border-surface-container rounded text-center font-bold" min="1">
                            <button onclick="updateItemQuantity(${item.id}, ${item.quantite + 1})" class="px-2 py-1 bg-surface-container text-on-surface hover:bg-surface-container-high rounded font-bold">+</button>
                        </div>
                    </div>
                    <div class="text-right pl-4">
                        <div class="text-lg font-bold text-primary mb-2">${(item.prix_unitaire * item.quantite).toFixed(2)}</div>
                        <p class="text-xs text-secondary mb-3">DT ${item.prix_unitaire.toFixed(2)}/u</p>
                        <button onclick="removeItem(${item.id})" class="px-3 py-1 bg-error-container hover:bg-error/20 text-error rounded font-bold text-xs flex items-center gap-1 justify-center w-full">
                            <span class="material-symbols-outlined text-sm">delete</span>
                            Supprimer
                        </button>
                    </div>
                `;
                cartItems.appendChild(itemElement);
            });

            updateSummary();
        }

        /**
         * Met à jour le résumé
         */
        function updateSummary() {
            const cartData = cart.getCart();
            const totalItems = cartData.length;
            const totalQty = cartData.reduce((sum, item) => sum + item.quantite, 0);
            const totalPrice = cart.getTotalPrice();

            document.getElementById('summary-items').textContent = totalItems;
            document.getElementById('summary-qty').textContent = totalQty;
            document.getElementById('summary-total').textContent = totalPrice.toFixed(2);
            document.getElementById('cart-total-items').textContent = totalQty;
            
            // Mettre à jour la barre de progression (max 50 articles)
            const progress = Math.min((totalQty / 50) * 100, 100);
            document.getElementById('cart-progress').style.width = progress + '%';
        }

        /**
         * Supprime un article
         */
        function removeItem(productId) {
            if (confirm('Supprimer cet article?')) {
                cart.removeFromCart(productId);
                displayCart();
            }
        }

        /**
         * Met à jour la quantité
         */
        function updateItemQuantity(productId, quantity) {
            quantity = parseInt(quantity);
            if (quantity < 1) {
                removeItem(productId);
            } else {
                cart.updateQuantity(productId, quantity);
                displayCart();
            }
        }

        /**
         * Valide la commande et envoie au serveur
         */
        function checkout() {
            const cartData = cart.getCart();
            if (cartData.length === 0) {
                cart.showToast('❌ Le panier est vide', 'error');
                return;
            }

            // Désactiver le bouton
            const btn = event.target.closest('button');
            btn.disabled = true;
            btn.innerHTML = '<span class="material-symbols-outlined animate-spin">loading</span> Validation...';

            // Déterminer si c'est une création ou une modification
            const isEdit = typeof window.editOrderId !== 'undefined' && window.editOrderId;
            const action = isEdit ? 'update' : 'create';
            const payload = {
                cart: cartData
            };
            
            if (isEdit) {
                payload.order_id = window.editOrderId;
            }

            // Envoyer au serveur
            const endpoint = isEdit ? '/integration/stock/orders/create' : '/integration/stock/orders/create';
            fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const message = isEdit ? '✅ Commande modifiée avec succès!' : '✅ Commande créée avec succès!';
                    cart.showToast(message, 'success');
                    cart.clearCart();
                    setTimeout(() => {
                        window.location.href = data.redirect || '?action=orders&method=list';
                    }, 2000);
                } else {
                    cart.showToast('❌ ' + (data.message || 'Erreur lors de la validation'), 'error');
                    btn.disabled = false;
                    btn.innerHTML = '<span class="material-symbols-outlined">check_circle</span> Valider la Commande';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                cart.showToast('❌ Erreur serveur', 'error');
                btn.disabled = false;
                btn.innerHTML = '<span class="material-symbols-outlined">check_circle</span> Valider la Commande';
            });
        }

        /**
         * Vide le panier
         */
        function clearCartConfirm() {
            if (confirm('Êtes-vous sûr de vouloir vider le panier?')) {
                cart.clearCart();
                displayCart();
            }
        }

        /**
         * Échappe les caractères HTML
         */
        function htmlEscape(text) {
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        // Charger la commande en modification si nécessaire
        <?php if ($editOrderData): ?>
        const editOrderData = <?php echo json_encode($editOrderData); ?>;
        
        // Convertir les articles de commande en format compatible avec le panier
        const cartItems = editOrderData.lignes.map(ligne => ({
            id: ligne.produit_id,
            nom: ligne.nom,
            categorie: ligne.categorie,
            quantite: parseInt(ligne.quantite_demande),
            prix_unitaire: parseFloat(ligne.prix),
            prix_achat: 0
        }));

        // Injecter les articles dans le panier
        cart.setCart(cartItems);
        
        // Afficher une notification
        cart.showToast('✏️ Commande #' + editOrderData.id + ' chargée pour modification', 'success');
        
        // Garder l'ID de commande pour la mise à jour
        window.editOrderId = editOrderData.id;
        <?php endif; ?>

        // Afficher le panier au chargement
        window.addEventListener('load', displayCart);
    </script>
    </script>
<?php if (!$embeddedInLayout): ?>
</body></html>
<?php endif; ?>
