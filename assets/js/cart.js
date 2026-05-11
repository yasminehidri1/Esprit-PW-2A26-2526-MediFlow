const CART_KEY = 'mediflow_cart';

const cart = {
    getCart: function() {
        return JSON.parse(localStorage.getItem(CART_KEY)) || [];
    },
    setCart: function(items) {
        localStorage.setItem(CART_KEY, JSON.stringify(items));
        this.updateBadge();
    },
    addToCart: function(item) {
        let items = this.getCart();
        const existing = items.find(i => parseInt(i.id) === parseInt(item.id));
        if (existing) {
            existing.quantite += 1;
        } else {
            item.quantite = 1;
            items.push(item);
        }
        this.setCart(items);
        this.showToast('Produit ajouté au panier : ' + item.nom, 'success');
    },
    removeFromCart: function(id) {
        let items = this.getCart();
        items = items.filter(i => parseInt(i.id) !== parseInt(id));
        this.setCart(items);
    },
    clearCart: function() {
        localStorage.removeItem(CART_KEY);
        this.updateBadge();
    },
    updateQuantity: function(id, qty) {
        if (qty < 1) return;
        let items = this.getCart();
        const existing = items.find(i => parseInt(i.id) === parseInt(id));
        if (existing) {
            existing.quantite = parseInt(qty);
            this.setCart(items);
        }
    },
    getTotalPrice: function() {
        return this.getCart().reduce((sum, item) => sum + (parseFloat(item.prix_unitaire) * parseInt(item.quantite)), 0);
    },
    updateBadge: function() {
        const count = this.getCart().reduce((sum, item) => sum + parseInt(item.quantite), 0);
        const badge = document.getElementById('cartCount');
        if (badge) badge.innerText = count;
    },
    showToast: function(msg, type) {
        alert(msg);
    }
};

function addProductToCart(id, nom, prix_unitaire, prix_achat, categorie, image) {
    cart.addToCart({
        id: id,
        nom: nom,
        prix_unitaire: parseFloat(prix_unitaire),
        prix_achat: parseFloat(prix_achat),
        categorie: categorie,
        image: image
    });
}

function checkout() {
    const items = cart.getCart();
    if (items.length === 0) {
        alert('Votre panier est vide');
        return;
    }

    const payload = { cart: items };
    if (window.editOrderId) {
        payload.edit_order_id = window.editOrderId;
    }

    // Le bouton affiche un chargement
    const btn = event.currentTarget;
    const oldHtml = btn.innerHTML;
    btn.innerHTML = 'Traitement...';
    btn.disabled = true;

    fetch('/integration/stock/orders/create', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.checkout_url) {
            cart.clearCart();
            window.location.href = data.checkout_url;
        } else if (data.success) {
            cart.clearCart();
            window.location.href = data.redirect || '/integration/stock/orders';
        } else {
            alert('Erreur: ' + data.message);
            btn.innerHTML = oldHtml;
            btn.disabled = false;
        }
    })
    .catch(err => {
        console.error(err);
        alert('Erreur de connexion. Veuillez réessayer.');
        btn.innerHTML = oldHtml;
        btn.disabled = false;
    });
}

// Initialisation du badge au chargement
document.addEventListener('DOMContentLoaded', () => {
    cart.updateBadge();
});
