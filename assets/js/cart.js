/**
 * Shopping Cart Management
 * Utilise localStorage pour persister les données du panier
 */

class ShoppingCart {
    constructor() {
        this.cartKey = 'mediflow_cart';
        this.cart = this.loadCart();
    }

    /**
     * Charge le panier depuis localStorage
     */
    loadCart() {
        const cart = localStorage.getItem(this.cartKey);
        return cart ? JSON.parse(cart) : [];
    }

    /**
     * Sauvegarde le panier dans localStorage
     */
    saveCart() {
        localStorage.setItem(this.cartKey, JSON.stringify(this.cart));
        this.updateCartCount();
    }

    /**
     * Ajoute un produit au panier (ou augmente la quantité)
     */
    addToCart(product) {
        // Vérifier si le produit existe déjà
        const existingProduct = this.cart.find(item => item.id === product.id);

        if (existingProduct) {
            // Augmenter la quantité si le produit existe
            existingProduct.quantite += 1;
            this.showToast(`${product.nom} - Quantité mise à jour (${existingProduct.quantite})`, 'info');
        } else {
            // Ajouter le nouveau produit
            this.cart.push({
                id: product.id,
                nom: product.nom,
                prix_unitaire: product.prix_unitaire,
                prix_achat: product.prix_achat,
                categorie: product.categorie,
                quantite: 1,
                image: product.image
            });
            this.showToast(`✅ ${product.nom} ajoutée au panier!`, 'success');
        }

        this.saveCart();
    }

    /**
     * Affiche le nombre d'articles dans le panier
     */
    updateCartCount() {
        const totalItems = this.cart.reduce((sum, item) => sum + item.quantite, 0);
        
        // Mettre à jour le badge du panier dans le header si présent
        const cartBadge = document.querySelector('.cart-count');
        if (cartBadge) {
            cartBadge.textContent = totalItems;
            cartBadge.style.display = totalItems > 0 ? 'flex' : 'none';
        }
    }

    /**
     * Récupère le nombre total d'articles
     */
    getTotalItems() {
        return this.cart.reduce((sum, item) => sum + item.quantite, 0);
    }

    /**
     * Récupère le montant total du panier
     */
    getTotalPrice() {
        return this.cart.reduce((sum, item) => sum + (item.prix_unitaire * item.quantite), 0);
    }

    /**
     * Récupère le panier complet
     */
    getCart() {
        return this.cart;
    }

    /**
     * Supprime un produit du panier
     */
    removeFromCart(productId) {
        this.cart = this.cart.filter(item => item.id !== productId);
        this.saveCart();
        this.showToast('✅ Produit supprimé du panier', 'info');
    }

    /**
     * Met à jour la quantité d'un produit
     */
    updateQuantity(productId, quantity) {
        const product = this.cart.find(item => item.id === productId);
        if (product) {
            if (quantity <= 0) {
                this.removeFromCart(productId);
            } else {
                product.quantite = quantity;
                this.saveCart();
            }
        }
    }

    /**
     * Vide le panier complètement
     */
    clearCart() {
        this.cart = [];
        this.saveCart();
        this.showToast('✅ Panier vidé', 'info');
    }

    /**
     * Remplace le contenu du panier
     */
    setCart(items) {
        this.cart = items;
        this.saveCart();
    }

    /**
     * Affiche une notification toast
     */
    showToast(message, type = 'success') {
        // Créer le conteneur toast s'il n'existe pas
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.style.cssText = `
                position: fixed;
                top: 80px;
                right: 20px;
                z-index: 9999;
                display: flex;
                flex-direction: column;
                gap: 10px;
            `;
            document.body.appendChild(toastContainer);
        }

        // Créer le toast
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6';
        const textColor = '#ffffff';

        toast.style.cssText = `
            background-color: ${bgColor};
            color: ${textColor};
            padding: 16px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            font-weight: 600;
            animation: slideIn 0.3s ease-out;
            min-width: 300px;
            word-wrap: break-word;
        `;
        toast.textContent = message;

        toastContainer.appendChild(toast);

        // Ajouter l'animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(400px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            @keyframes slideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(400px);
                    opacity: 0;
                }
            }
        `;
        if (!document.querySelector('style[data-toast-animation]')) {
            style.setAttribute('data-toast-animation', 'true');
            document.head.appendChild(style);
        }

        // Supprimer le toast après 3 secondes
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}

// Initialiser le panier au chargement
let cart = new ShoppingCart();

// Fonction pour ajouter un produit au panier
function addProductToCart(productId, productName, productPrice, productAchat, productCategorie, productImage = '') {
    cart.addToCart({
        id: productId,
        nom: productName,
        prix_unitaire: productPrice,
        prix_achat: productAchat,
        categorie: productCategorie,
        image: productImage
    });
}

// Initialiser le compte du panier au chargement de la page
window.addEventListener('load', function() {
    cart.updateCartCount();
});
