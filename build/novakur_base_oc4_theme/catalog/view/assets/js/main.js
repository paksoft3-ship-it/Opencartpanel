// NovaKur Base main JavaScript entry
import { initMegaMenu } from './layout/mega_menu.js';
import { initMobileNavDrawer } from './layout/mobile_nav_drawer.js';
import { initQuantitySelector } from './ui/quantity_selector.js';
import { initAccordion } from './ui/accordion.js';
import { initProductImageGallery } from './commerce/product_image_gallery.js';
import { initProductTabs } from './commerce/product_tabs.js';
import { initAddToCartButton } from './commerce/add_to_cart_button.js';
import { initWishlistButton } from './commerce/wishlist_button.js';
import { initFeaturedProducts } from './sections/featured_products.js';

document.addEventListener('DOMContentLoaded', () => {
  initMegaMenu();
  initMobileNavDrawer();
  initQuantitySelector();
  initAccordion();
  initProductImageGallery();
  initProductTabs();
  initAddToCartButton();
  initWishlistButton();
  initFeaturedProducts();
});
