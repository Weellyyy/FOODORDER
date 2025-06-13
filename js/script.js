document.addEventListener('DOMContentLoaded', function() {
    document.body.addEventListener('click', function(event) {
        if (event.target.classList.contains('order-btn')) {
            event.target.disabled = true;
            event.target.textContent = 'Adding...';
            const foodId = event.target.dataset.id;
            addToCart(foodId, event.target);
        }
    });
});

async function addToCart(foodId, buttonElement) {
    const formData = new FormData();
    formData.append('food_id', foodId);

    try {
        const response = await fetch('handle_cart.php', {
            method: 'POST',
            body: formData
        });
        if (!response.ok) throw new Error('Network response was not ok.');

        const data = await response.json();
        showNotification(`${data.cart[foodId].name} ditambahkan ke keranjang!`);

        // ✅ Alihkan ke payment_details.php
        setTimeout(() => {
            window.location.href = 'payment_details.php';
        }, 1000);
    } catch (error) {
        console.error('Fetch error:', error);
        showNotification('Gagal menambahkan item.', 'error');
    } finally {
        buttonElement.disabled = false;
        buttonElement.textContent = 'Order Now';
    }
}


function updateInvoiceUI(cart, totals) {
    const invoiceItemsContainer = document.getElementById('invoice-items');
    const subTotalEl = document.getElementById('sub-total');
    const taxEl = document.getElementById('tax');
    const totalPaymentEl = document.getElementById('total-payment');
    const placeOrderBtn = document.querySelector('.place-order-btn');

    invoiceItemsContainer.innerHTML = '';
    if (Object.keys(cart).length === 0) {
        invoiceItemsContainer.innerHTML = '<p class="empty-cart-message">Keranjang Anda masih kosong.</p>';
        placeOrderBtn.disabled = true;
    } else {
        for (const id in cart) {
            const item = cart[id];
            const itemElement = document.createElement('div');
            itemElement.classList.add('invoice-item');
            itemElement.innerHTML = `
                <img src="${item.image_url}" alt="${item.name}">
                <div class="item-details">
                    <p class="item-name">${item.name}</p>
                    <p class="item-quantity">x ${item.quantity}</p>
                </div>
                <strong class="item-price">$${(item.price * item.quantity).toFixed(2)}</strong>
            `;
            invoiceItemsContainer.appendChild(itemElement);
        }
        placeOrderBtn.disabled = false;
    }
    subTotalEl.textContent = `$${totals.sub_total.toFixed(2)}`;
    taxEl.textContent = `$${totals.tax.toFixed(2)}`;
    totalPaymentEl.textContent = `$${totals.total.toFixed(2)}`;
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `toast-notification ${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => notification.classList.add('show'), 10);
    setTimeout(() => {
        notification.classList.remove('show');
        notification.addEventListener('transitionend', () => notification.remove());
    }, 3000);
}