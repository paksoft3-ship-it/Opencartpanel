/*!
 * NovaKur Minimal Theme JS v1.0.0
 * Vanilla JS — no jQuery dependency
 */
(function () {
  'use strict';

  /* ── Helpers ─────────────────────────────────────────────── */
  const $ = (sel, ctx) => (ctx || document).querySelector(sel);
  const $$ = (sel, ctx) => [...(ctx || document).querySelectorAll(sel)];

  /* ── Mobile Nav ──────────────────────────────────────────── */
  function initMobileNav() {
    const hamburger = $('.nm-hamburger');
    const mobileNav = $('.nm-mobile-nav');
    const overlay   = $('.nm-mobile-overlay');
    const closeBtn  = $('.nm-mobile-close');

    if (!hamburger || !mobileNav) return;

    const open  = () => { mobileNav.classList.add('is-open'); document.body.style.overflow = 'hidden'; };
    const close = () => { mobileNav.classList.remove('is-open'); document.body.style.overflow = ''; };

    hamburger.addEventListener('click', open);
    overlay && overlay.addEventListener('click', close);
    closeBtn && closeBtn.addEventListener('click', close);

    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
  }

  /* ── Product Image Gallery ───────────────────────────────── */
  function initGallery() {
    const main = $('.nm-gallery-main img');
    if (!main) return;

    $$('.nm-gallery-thumb').forEach(thumb => {
      thumb.addEventListener('click', () => {
        main.src = thumb.dataset.full || thumb.querySelector('img')?.src || '';
        $$('.nm-gallery-thumb').forEach(t => t.classList.remove('active'));
        thumb.classList.add('active');
      });
    });
  }

  /* ── Quantity Selector ───────────────────────────────────── */
  function initQtySelectors() {
    $$('.nm-qty').forEach(widget => {
      const input = widget.querySelector('.nm-qty-input');
      const minus = widget.querySelector('[data-action="minus"]');
      const plus  = widget.querySelector('[data-action="plus"]');
      if (!input) return;

      minus && minus.addEventListener('click', () => {
        const v = parseInt(input.value) || 1;
        if (v > 1) input.value = v - 1;
      });
      plus && plus.addEventListener('click', () => {
        input.value = (parseInt(input.value) || 1) + 1;
      });

      input.addEventListener('change', () => {
        const v = parseInt(input.value) || 1;
        input.value = Math.max(1, v);
      });
    });
  }

  /* ── AJAX Add to Cart ────────────────────────────────────── */
  function initAddToCart() {
    $$('[data-nm-add-to-cart]').forEach(btn => {
      btn.addEventListener('click', function (e) {
        e.preventDefault();

        const productId = this.dataset.productId;
        const qtyInput  = document.getElementById('nm-qty-' + productId) || document.getElementById('nm-qty');
        const qty       = qtyInput ? parseInt(qtyInput.value) || 1 : 1;

        const options = {};
        $$('[name^="option["]').forEach(el => {
          const match = el.name.match(/option\[(\d+)\]/);
          if (match && el.value) options[match[1]] = el.value;
        });

        const params = new URLSearchParams({
          product_id: productId,
          quantity: qty,
          ...Object.fromEntries(Object.entries(options).map(([k, v]) => [`option[${k}]`, v]))
        });

        const lang = document.documentElement.lang || 'en-gb';

        this.disabled = true;
        this.textContent = '...';

        fetch(`index.php?route=checkout/cart.add&language=${lang}`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: params.toString()
        })
        .then(r => r.json())
        .then(json => {
          if (json.success) {
            updateCartCount(json.total || null);
            showToast(json.success, 'success');
          } else if (json.error && json.error.option) {
            showToast(Object.values(json.error).join(' '), 'error');
          }
        })
        .catch(() => showToast('Something went wrong. Please try again.', 'error'))
        .finally(() => {
          this.disabled = false;
          this.textContent = 'Add to Cart';
        });
      });
    });
  }

  /* ── Wishlist ─────────────────────────────────────────────── */
  function initWishlist() {
    $$('[data-nm-wishlist]').forEach(btn => {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        const productId = this.dataset.productId;
        const lang = document.documentElement.lang || 'en-gb';

        fetch(`index.php?route=account/wishlist.add&language=${lang}`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({ product_id: productId }).toString()
        })
        .then(r => r.json())
        .then(json => {
          showToast(json.success || json.error?.warning || 'Added to wishlist', json.success ? 'success' : 'info');
        });
      });
    });
  }

  /* ── Cart Count ──────────────────────────────────────────── */
  function updateCartCount(count) {
    const badge = $('.nm-cart-badge');
    if (!badge) return;
    if (count !== null) {
      badge.textContent = count;
      badge.style.display = count > 0 ? '' : 'none';
    }
  }

  /* ── Toast Notifications ─────────────────────────────────── */
  let toastEl = null;
  let toastTimer = null;

  function showToast(msg, type = 'info') {
    if (!toastEl) {
      toastEl = document.createElement('div');
      toastEl.style.cssText = [
        'position:fixed', 'bottom:24px', 'right:24px', 'z-index:9999',
        'padding:12px 20px', 'font-family:inherit', 'font-size:14px',
        'max-width:320px', 'border:1px solid', 'opacity:0',
        'transition:opacity 200ms ease', 'pointer-events:none'
      ].join(';');
      document.body.appendChild(toastEl);
    }

    const styles = {
      success: { background: '#f0fdf4', border: '#16a34a', color: '#15803d' },
      error:   { background: '#fef2f2', border: '#dc2626', color: '#b91c1c' },
      info:    { background: '#f8f8f8', border: '#e5e5e5', color: '#111' }
    };
    const s = styles[type] || styles.info;

    toastEl.style.background = s.background;
    toastEl.style.borderColor = s.border;
    toastEl.style.color = s.color;
    toastEl.textContent = msg;
    toastEl.style.opacity = '1';
    toastEl.style.pointerEvents = 'auto';

    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => {
      toastEl.style.opacity = '0';
      toastEl.style.pointerEvents = 'none';
    }, 3000);
  }

  /* ── Smooth Scroll Anchors ───────────────────────────────── */
  function initSmoothScroll() {
    $$('a[href^="#"]').forEach(a => {
      a.addEventListener('click', (e) => {
        const target = document.getElementById(a.hash.slice(1));
        if (target) {
          e.preventDefault();
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
    });
  }

  /* ── Newsletter Form ─────────────────────────────────────── */
  function initNewsletter() {
    const form = $('.nm-newsletter-form');
    if (!form) return;

    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const input = form.querySelector('input[type="email"]');
      if (!input || !input.value) return;

      showToast('Thank you for subscribing!', 'success');
      input.value = '';
    });
  }

  /* ── Cart Remove ─────────────────────────────────────────── */
  function initCartRemove() {
    $$('[data-nm-remove]').forEach(btn => {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        const href = this.dataset.href || this.href;
        if (!href) return;

        fetch(href, { method: 'POST' })
          .then(() => location.reload())
          .catch(() => location.reload());
      });
    });
  }

  /* ── Tab Bar (Product page tabs) ─────────────────────────── */
  function initTabs() {
    $$('.nm-tabs').forEach(tabGroup => {
      const buttons = $$('[data-tab]', tabGroup);
      const panels  = $$('[data-panel]', tabGroup.closest('.nm-product-tabs') || document);

      buttons.forEach(btn => {
        btn.addEventListener('click', () => {
          buttons.forEach(b => b.classList.remove('active'));
          panels.forEach(p => p.classList.add('nm-hidden'));

          btn.classList.add('active');
          const panel = document.getElementById(btn.dataset.tab);
          if (panel) panel.classList.remove('nm-hidden');
        });
      });
    });
  }

  /* ── Color Swatch Selection ──────────────────────────────── */
  function initSwatches() {
    $$('.nm-swatch-group').forEach(group => {
      $$('.nm-swatch', group).forEach(swatch => {
        swatch.addEventListener('click', () => {
          $$('.nm-swatch', group).forEach(s => s.classList.remove('active'));
          swatch.classList.add('active');

          const select = document.getElementById(group.dataset.target);
          if (select) select.value = swatch.dataset.value;
        });
      });
    });
  }

  /* ── Lazy Image Loading ───────────────────────────────────── */
  function initLazyImages() {
    if (!('IntersectionObserver' in window)) return;

    const io = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const img = entry.target;
          if (img.dataset.src) {
            img.src = img.dataset.src;
            img.removeAttribute('data-src');
          }
          io.unobserve(img);
        }
      });
    }, { rootMargin: '200px' });

    $$('img[data-src]').forEach(img => io.observe(img));
  }

  /* ── Init ─────────────────────────────────────────────────── */
  function init() {
    initMobileNav();
    initGallery();
    initQtySelectors();
    initAddToCart();
    initWishlist();
    initSmoothScroll();
    initNewsletter();
    initCartRemove();
    initTabs();
    initSwatches();
    initLazyImages();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
