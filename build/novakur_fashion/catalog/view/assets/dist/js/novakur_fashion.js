/*!
 * NovaKur Fashion Theme JS v1.0.0
 */
(function () {
  'use strict';

  const $ = (sel, ctx) => (ctx || document).querySelector(sel);
  const $$ = (sel, ctx) => [...(ctx || document).querySelectorAll(sel)];

  function initMobileNav() {
    const hamburger = $('.nf-hamburger');
    const mobileNav = $('.nf-mobile-nav');
    const overlay   = $('.nf-mobile-overlay');
    const closeBtn  = $('.nf-mobile-close');
    if (!hamburger || !mobileNav) return;
    const open  = () => { mobileNav.classList.add('is-open'); document.body.style.overflow = 'hidden'; };
    const close = () => { mobileNav.classList.remove('is-open'); document.body.style.overflow = ''; };
    hamburger.addEventListener('click', open);
    overlay && overlay.addEventListener('click', close);
    closeBtn && closeBtn.addEventListener('click', close);
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
  }

  function initGallery() {
    const thumbs = $$('.nf-gallery-thumb');
    const main   = $('.nf-gallery-main img');
    if (!main || !thumbs.length) return;
    thumbs.forEach(thumb => {
      thumb.addEventListener('click', () => {
        main.src = thumb.dataset.full || thumb.querySelector('img')?.src || '';
        thumbs.forEach(t => t.classList.remove('active'));
        thumb.classList.add('active');
      });
    });
  }

  function initQtySelectors() {
    $$('.nf-qty').forEach(widget => {
      const input = widget.querySelector('.nf-qty-input');
      const minus = widget.querySelector('[data-action="minus"]');
      const plus  = widget.querySelector('[data-action="plus"]');
      if (!input) return;
      minus && minus.addEventListener('click', () => { const v = parseInt(input.value) || 1; if (v > 1) input.value = v - 1; });
      plus  && plus.addEventListener('click',  () => { input.value = (parseInt(input.value) || 1) + 1; });
      input.addEventListener('change', () => { input.value = Math.max(1, parseInt(input.value) || 1); });
    });
  }

  function initAddToCart() {
    $$('[data-nf-add-to-cart]').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        const productId = this.dataset.productId;
        const qtyInput  = document.getElementById('nf-qty-' + productId) || document.getElementById('nf-qty');
        const qty       = qtyInput ? parseInt(qtyInput.value) || 1 : 1;
        const options   = {};
        $$('[name^="option["]').forEach(el => {
          const match = el.name.match(/option\[(\d+)\]/);
          if (match && el.value) options[match[1]] = el.value;
        });
        const params = new URLSearchParams({ product_id: productId, quantity: qty, ...Object.fromEntries(Object.entries(options).map(([k,v]) => [`option[${k}]`, v])) });
        const lang = document.documentElement.lang || 'en-gb';
        const originalText = this.textContent;
        this.disabled = true;
        this.textContent = '...';
        fetch(`index.php?route=checkout/cart.add&language=${lang}`, { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: params.toString() })
          .then(r => r.json())
          .then(json => {
            if (json.success) { updateCartCount(json.total || null); showToast(json.success, 'success'); }
            else if (json.error) { showToast(Object.values(json.error).join(' '), 'error'); }
          })
          .catch(() => showToast('Something went wrong. Please try again.', 'error'))
          .finally(() => { this.disabled = false; this.textContent = originalText; });
      });
    });
  }

  function initWishlist() {
    $$('[data-nf-wishlist]').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        const lang = document.documentElement.lang || 'en-gb';
        fetch(`index.php?route=account/wishlist.add&language=${lang}`, { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: new URLSearchParams({ product_id: this.dataset.productId }).toString() })
          .then(r => r.json())
          .then(json => { showToast(json.success || json.error?.warning || 'Added to wishlist', json.success ? 'success' : 'info'); });
      });
    });
  }

  function updateCartCount(count) {
    const badge = $('.nf-cart-badge');
    if (!badge || count === null) return;
    badge.textContent = count;
    badge.style.display = count > 0 ? '' : 'none';
  }

  let toastEl = null, toastTimer = null;
  function showToast(msg, type = 'info') {
    if (!toastEl) {
      toastEl = document.createElement('div');
      toastEl.style.cssText = 'position:fixed;bottom:32px;right:32px;z-index:9999;padding:14px 22px;font-family:inherit;font-size:13px;letter-spacing:0.04em;max-width:320px;border:1px solid;opacity:0;transition:opacity 200ms ease;pointer-events:none;';
      document.body.appendChild(toastEl);
    }
    const styles = {
      success: { background: '#f0fdf4', border: '#16a34a', color: '#15803d' },
      error:   { background: '#fef2f2', border: '#dc2626', color: '#b91c1c' },
      info:    { background: '#fafaf8', border: '#e0d8cc', color: '#1a1a1a' }
    };
    const s = styles[type] || styles.info;
    Object.assign(toastEl.style, { background: s.background, borderColor: s.border, color: s.color, opacity: '1', pointerEvents: 'auto' });
    toastEl.textContent = msg;
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => { toastEl.style.opacity = '0'; toastEl.style.pointerEvents = 'none'; }, 3500);
  }

  function initCartRemove() {
    $$('[data-nf-remove]').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        const href = this.dataset.href || this.href;
        if (!href) return;
        fetch(href, { method: 'POST' }).finally(() => location.reload());
      });
    });
  }

  function initTabs() {
    $$('.nf-tabs').forEach(tabGroup => {
      const buttons = $$('[data-tab]', tabGroup);
      buttons.forEach(btn => {
        btn.addEventListener('click', () => {
          buttons.forEach(b => b.classList.remove('active'));
          $$('[data-panel]', tabGroup.closest('.nf-product-tabs') || document).forEach(p => p.classList.add('nf-hidden'));
          btn.classList.add('active');
          const panel = document.getElementById(btn.dataset.tab);
          if (panel) panel.classList.remove('nf-hidden');
        });
      });
    });
  }

  function initSizeButtons() {
    $$('.nf-size-group').forEach(group => {
      $$('.nf-size-btn', group).forEach(btn => {
        btn.addEventListener('click', () => {
          if (btn.disabled) return;
          $$('.nf-size-btn', group).forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
          const select = document.getElementById(group.dataset.target);
          if (select) select.value = btn.dataset.value;
        });
      });
    });
  }

  function initNewsletter() {
    const form = $('.nf-newsletter-form');
    if (!form) return;
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const input = form.querySelector('input[type="email"]');
      if (!input || !input.value) return;
      showToast('Thank you for subscribing!', 'success');
      input.value = '';
    });
  }

  function initLazyImages() {
    if (!('IntersectionObserver' in window)) return;
    const io = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const img = entry.target;
          if (img.dataset.src) { img.src = img.dataset.src; img.removeAttribute('data-src'); }
          io.unobserve(img);
        }
      });
    }, { rootMargin: '200px' });
    $$('img[data-src]').forEach(img => io.observe(img));
  }

  function init() {
    initMobileNav();
    initGallery();
    initQtySelectors();
    initAddToCart();
    initWishlist();
    initCartRemove();
    initTabs();
    initSizeButtons();
    initNewsletter();
    initLazyImages();
  }

  if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', init); }
  else { init(); }
})();
