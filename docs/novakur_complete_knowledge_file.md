# NovaKur Complete Project Knowledge File

## Master Context for AI Developers and Engineers

**Project Name:** NovaKur  
**Version:** v1 → v2 transition architecture  
**Base Platform:** OpenCart 4.x  
**Author:** Hilal Ahmad

---

## 1. Project Definition

NovaKur is a modular e-commerce storefront architecture built on OpenCart 4.

The system replaces the traditional OpenCart theme structure with a component-driven UI framework.

NovaKur is not intended to be a simple theme. It is designed to become a scalable storefront architecture and future commerce platform layer.

The project introduces:

- reusable components
- section-based pages
- design tokens
- layout modularity
- scalable storefront architecture

---

## 2. Core Philosophy

NovaKur follows modern frontend architecture principles while remaining compatible with OpenCart.

### Component-first design
Every UI element must be reusable.

Bad pattern:

Large monolithic templates.

Correct pattern:

Small components combined into sections and pages.

### Separation of concerns
The platform separates four UI responsibilities:

- UI components
- layout components
- commerce components
- page sections

### Twig composition
Pages are built through Twig includes, not duplication.

Example:

```twig
home.twig
 include hero_banner.twig
 include category_grid.twig
 include featured_products.twig
```

### Commerce abstraction
Commerce UI should be isolated.

Example components:

```text
product_card_standard
price_block
add_to_cart_button
stock_badge
```

These components receive data from controllers and render product information.

---

## 3. Platform Stack

### Backend
OpenCart 4.x

### Languages
PHP  
Twig  
SCSS  
JavaScript

### Database
MySQL (OpenCart default schema)

---

## 4. System Layers

### Layer 1 — OpenCart Core
Provides:

- products
- orders
- customers
- checkout
- payments
- shipping
- admin interface

NovaKur must not break or override core logic unnecessarily.

NovaKur only modifies presentation and frontend behavior.

### Layer 2 — NovaKur Storefront Engine
This layer controls:

- page layout
- component orchestration
- design tokens
- theme settings

Location example:

```text
catalog/view/template/
```

### Layer 3 — Component System
NovaKur organizes UI into four component groups.

#### UI Components
Location:

```text
component/ui/
```

Examples:

- button.twig
- dropdown_select.twig
- rating_stars.twig
- accordion.twig
- badge.twig
- pagination.twig
- search_input.twig
- quantity_selector.twig

Purpose: reusable interface primitives.

#### Layout Components
Location:

```text
component/layout/
```

Examples:

- header_desktop.twig
- footer.twig
- breadcrumb.twig
- announcement_bar.twig
- mobile_nav_drawer.twig
- mega_menu.twig

Purpose: global page structure.

#### Commerce Components
Location:

```text
component/commerce/
```

Examples:

- product_card_standard.twig
- product_card_featured.twig
- add_to_cart_button.twig
- price_block.twig
- wishlist_button.twig
- discount_badge.twig
- stock_badge.twig
- product_image_gallery.twig
- product_tabs.twig

Purpose: render product commerce information.

#### Section Components
Location:

```text
component/sections/
```

Examples:

- hero_banner.twig
- category_grid.twig
- featured_products.twig
- promo_banners.twig
- newsletter_signup.twig
- benefits_bar.twig
- blog_preview.twig
- brand_logos.twig
- upsell_grid.twig

Purpose: page blocks used to build layouts.

---

## 5. Asset Pipeline

Assets are stored separately.

Directory:

```text
catalog/view/assets/
```

Structure:

```text
assets
 ├── scss
 ├── js
 └── dist
     ├── css
     └── js
```

Main stylesheet:

```text
dist/css/main.css
```

SCSS compiles into CSS.

---

## 6. Page Templates

NovaKur overrides several OpenCart templates.

### Homepage
```text
common/home.twig
```

Built using sections.

Example layout:

- hero_banner
- category_grid
- featured_products
- promo_banners
- newsletter_signup

### Product page
```text
product/product.twig
```

Uses commerce components.

### Category page
```text
product/category.twig
```

Displays product cards.

### Cart page
```text
checkout/cart.twig
```

### Checkout page
```text
checkout/checkout.twig
```

### Account pages
```text
account/login.twig
account/register.twig
account/account.twig
account/wishlist.twig
account/order_list.twig
```

---

## 7. Data Flow

Product creation occurs in admin.

Example:

```text
Admin → Catalog → Products
```

OpenCart stores product data.

Controllers retrieve product information.

Controllers pass data to Twig templates.

Twig renders UI components.

Example chain:

```text
product.twig
 → product_card_standard.twig
 → price_block.twig
 → add_to_cart_button.twig
```

---

## 8. Theme Settings System

NovaKur includes theme settings to control visual appearance.

Examples:

- Logo
- Color palette
- Container width
- Header layout
- Footer layout
- Homepage sections
- Hero banner settings

These settings influence:

- CSS variables
- component styles
- section behavior

---

## 9. Design Token System

NovaKur uses CSS variables to control design.

Example tokens:

```css
--nk-primary
--nk-secondary
--nk-radius
--nk-spacing
--nk-container-width
--nk-font-base
--nk-font-heading
```

All UI components should reference tokens instead of hardcoded values.

---

## 10. Development Rules

Developers must follow these rules.

Never modify OpenCart core files.

Allowed modification areas:

```text
catalog/view/
catalog/controller/
admin/controller/extension/theme/
```

Always maintain component modularity.

Avoid duplicating templates.

Use Twig includes.

Maintain compatibility with OpenCart updates.

---

## 11. Current Project Status

NovaKur currently includes:

- component library
- homepage structure
- product page layout
- category page layout
- cart and checkout UI
- account pages
- asset pipeline

However some parts may still require:

- theme registration stabilization
- admin configuration improvements
- real data verification
- fallback behavior improvements

---

## 12. Future Architecture (v2)

NovaKur v2 should introduce:

- section registry
- homepage section orchestrator
- theme variants
- expanded design tokens
- configurable section settings

This moves NovaKur from a theme to a storefront architecture.

---

## 13. Theme Variant System

NovaKur should support industry variants.

Examples:

- NovaKur Electronics
- NovaKur Fashion
- NovaKur Furniture
- NovaKur Cosmetics

Variants modify:

- hero layout
- product card style
- section order
- visual tone

---

## 14. Section System

Pages should eventually be assembled dynamically.

Example concept:

```text
homepage_sections = [
 hero_banner,
 category_grid,
 featured_products,
 benefits_bar
]
```

Twig loops through sections and renders them.

This enables future visual builders.

---

## 15. Performance Principles

NovaKur should optimize for:

- clean Twig templates
- minimal CSS duplication
- reusable components
- fast rendering

Avoid heavy JavaScript unless necessary.

---

## 16. Business Potential

NovaKur can support multiple business models.

- premium theme sales
- custom client stores
- variant-based themes
- module ecosystem
- agency storefront framework

---

## 17. AI Development Expectations

AI developers working on NovaKur should:

- analyze architecture before changes
- maintain modular structure
- avoid breaking OpenCart core
- document decisions clearly
- prioritize scalability

---

## 18. Long-Term Vision

NovaKur should evolve into a commerce frontend framework for OpenCart.

Future potential includes:

- visual page builders
- multi-store architectures
- template marketplaces
- store generators
- agency deployment frameworks

---

## Final Statement

NovaKur is not simply a theme project.

It is a modular storefront framework designed to extend OpenCart into a modern component-driven commerce platform.

All development should focus on:

- modularity
- scalability
- maintainability
- platform potential

