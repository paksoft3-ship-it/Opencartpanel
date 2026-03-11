function nkOpenDrawer() {
  var drawer = document.getElementById('nk-mobile-drawer');
  var backdrop = document.getElementById('nk-drawer-backdrop');
  if (drawer) drawer.classList.remove('-translate-x-full');
  if (backdrop) backdrop.classList.remove('hidden');
  document.body.style.overflow = 'hidden';
}

function nkCloseDrawer() {
  var drawer = document.getElementById('nk-mobile-drawer');
  var backdrop = document.getElementById('nk-drawer-backdrop');
  if (drawer) drawer.classList.add('-translate-x-full');
  if (backdrop) backdrop.classList.add('hidden');
  document.body.style.overflow = '';
}

function updateQty(cartId, delta) {
  var input = document.getElementById('qty-' + cartId);
  if (!input) return;
  var current = parseInt(input.value, 10);
  var val = (isNaN(current) ? 1 : current) + delta;
  if (val < 1) val = 1;
  input.value = val;
}

window.nkOpenDrawer = nkOpenDrawer;
window.nkCloseDrawer = nkCloseDrawer;
window.updateQty = updateQty;

// Strip HTML tags for clean toast messages
function nkStripHtml(html) {
  var tmp = document.createElement('div');
  tmp.innerHTML = html;
  return tmp.textContent || tmp.innerText || html;
}

// Add to cart via AJAX (OC4 endpoint: checkout/cart.add)
window.nkCartAdd = function(formId, lang) {
  var form = document.getElementById(formId);
  if (!form) return;
  var btn = document.getElementById('button-cart');
  if (btn) { btn.disabled = true; }

  var data = new FormData(form);
  var params = new URLSearchParams();
  for (var pair of data.entries()) { params.append(pair[0], pair[1]); }

  fetch('index.php?route=checkout/cart.add&language=' + (lang || 'en-gb'), {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: params.toString()
  })
  .then(function(r) { return r.json(); })
  .then(function(d) {
    if (btn) { btn.disabled = false; }
    if (d.success) {
      window.nkToast(nkStripHtml(d.success), 'success');
      var badge = document.getElementById('nk-cart-count');
      if (badge && d.total) { badge.textContent = d.total; badge.classList.remove('hidden'); }
    } else if (d.error) {
      var msg = typeof d.error === 'object' ? Object.values(d.error).join(' ') : d.error;
      window.nkToast(nkStripHtml(msg), 'error');
    }
  })
  .catch(function() { if (btn) { btn.disabled = false; } });
};

// Quick add to cart from product cards
window.nkQuickAdd = function(productId, lang) {
  fetch('index.php?route=checkout/cart.add&language=' + (lang || 'en-gb'), {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'product_id=' + productId + '&quantity=1'
  })
  .then(function(r) { return r.json(); })
  .then(function(d) {
    if (d.success) {
      window.nkToast(nkStripHtml(d.success), 'success');
      var badge = document.getElementById('nk-cart-count');
      if (badge && d.total) { badge.textContent = d.total; badge.classList.remove('hidden'); }
    } else if (d.error) {
      var msg = typeof d.error === 'object' ? Object.values(d.error).join(' ') : d.error;
      window.nkToast(nkStripHtml(msg), 'error');
    }
  });
};

// Remove from cart — stays on cart page, removes row, reloads totals
window.nkCartRemove = function(key, lang) {
  fetch('index.php?route=checkout/cart.remove&language=' + (lang || 'en-gb') + '&key=' + key)
  .then(function(r) { return r.json(); })
  .then(function(d) {
    if (d.success) {
      window.nkToast(nkStripHtml(d.success), 'success');
      var row = document.getElementById('cart-row-' + key);
      if (row) row.remove();
      setTimeout(function() { window.location.reload(); }, 600);
    } else if (d.redirect) {
      window.location = d.redirect;
    }
  });
};

// Add to wishlist
window.nkWishlistAdd = function(productId, lang) {
  fetch('index.php?route=account/wishlist.add&language=' + (lang || 'en-gb'), {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'product_id=' + productId
  })
  .then(function(r) { return r.json(); })
  .then(function(d) {
    if (d.success) {
      window.nkToast(nkStripHtml(d.success), 'success');
    } else if (d.error) {
      var msg = typeof d.error === 'object' ? Object.values(d.error).join(' ') : d.error;
      window.nkToast(nkStripHtml(msg), 'info');
    }
  });
};

// Remove from wishlist (OC4 endpoint returns redirect, so use optimistic UI)
window.nkWishlistRemove = function(productId, lang, customerToken) {
  var url = 'index.php?route=account/wishlist.remove&language=' + (lang || 'en-gb') + '&product_id=' + productId;
  if (customerToken) url += '&customer_token=' + customerToken;
  var card = document.getElementById('wishlist-item-' + productId);
  if (card) card.remove();
  window.nkToast('Removed from wishlist', 'success');
  fetch(url);
};

window.nkToast = function(message, type) {
  var colors = { success: 'bg-green-500', error: 'bg-red-500', info: 'bg-blue-500' };
  var toast = document.createElement('div');
  toast.className = 'fixed bottom-6 right-6 z-[999] px-5 py-3 rounded-xl text-white text-sm font-semibold shadow-lg max-w-sm ' + (colors[type] || colors.info) + ' transition-all';
  toast.textContent = message;
  document.body.appendChild(toast);
  setTimeout(function() {
    toast.style.opacity = '0';
    setTimeout(function() { toast.remove(); }, 300);
  }, 3500);
};

document.addEventListener('DOMContentLoaded', function() {
  var backdrop = document.getElementById('nk-drawer-backdrop');
  if (backdrop) {
    backdrop.addEventListener('click', nkCloseDrawer);
  }

  // Newsletter form — prevent navigation if no real action endpoint is set
  var nlForm = document.querySelector('form[data-nk-newsletter]');
  if (nlForm) {
    nlForm.addEventListener('submit', function(e) {
      e.preventDefault();
      var emailInput = nlForm.querySelector('input[type="email"]');
      if (emailInput && emailInput.value) {
        window.nkToast('Thank you for subscribing!', 'success');
        emailInput.value = '';
      }
    });
  }

  document.querySelectorAll('[data-action="increase"], [data-action="decrease"]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var targetId = btn.getAttribute('data-qty-target');
      var input = document.getElementById(targetId);
      if (!input) return;
      var val = parseInt(input.value, 10);
      if (isNaN(val)) val = 1;
      var min = parseInt(input.min, 10);
      if (isNaN(min)) min = 1;
      var max = parseInt(input.max, 10);
      if (isNaN(max)) max = 9999;
      if (btn.getAttribute('data-action') === 'increase') {
        val = Math.min(val + 1, max);
      } else {
        val = Math.max(val - 1, min);
      }
      input.value = val;
      input.dispatchEvent(new Event('change'));
    });
  });

  var header = document.querySelector('header[class*="sticky"]');
  if (header) {
    window.addEventListener('scroll', function() {
      if (window.scrollY > 10) header.classList.add('shadow-md');
      else header.classList.remove('shadow-md');
    }, { passive: true });
  }
});
