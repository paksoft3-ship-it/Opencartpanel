# NovaKur Handoff Pack

## Project Identity Snapshot

**Project Name:** NovaKur  
**Base Platform:** OpenCart 4.x  
**Type:** Modular storefront framework / advanced theme architecture  
**Owner:** Hilal Ahmad

### Short definition
NovaKur is a **component-driven e-commerce storefront framework built on OpenCart 4**.

It is not intended to be just a simple theme skin. It is intended to become a **reusable storefront architecture** that can power multiple stores and future variants.

---

## What NovaKur Is Trying to Become

NovaKur is meant to evolve through these stages:

### Stage 1
A stable custom OpenCart 4 storefront theme.

### Stage 2
A reusable component-based storefront architecture.

### Stage 3
A section-driven configurable storefront system.

### Stage 4
A variant-based commerce framework that can power multiple store types.

### Stage 5
A broader commerce platform layer on top of OpenCart.

---

## Core Architecture

NovaKur uses a component system inside OpenCart.

### Main frontend structure

```text
catalog/view/template/
  common/
  product/
  checkout/
  account/
  information/
  component/
    layout/
    ui/
    commerce/
    sections/
```

### Assets structure

```text
catalog/view/assets/
  scss/
  js/
  dist/
    css/
    js/
```

### Admin theme structure

```text
admin/controller/extension/theme/novakur_base.php
admin/language/en-gb/extension/theme/novakur_base.php
admin/view/template/extension/theme/novakur_base.twig
```

### Important custom frontend controller

```text
catalog/controller/common/header.php
```

This file is important because it injects theme settings and CSS-variable related data.

---

## Component Categories

### Layout components
Used for structural UI:

```text
header_desktop.twig
footer.twig
breadcrumb.twig
mega_menu.twig
mobile_nav_drawer.twig
announcement_bar.twig
global_container.twig
```

### UI components
Reusable primitives:

```text
button.twig
badge.twig
accordion.twig
dropdown_select.twig
pagination.twig
price_display.twig
form_elements.twig
quantity_selector.twig
search_input.twig
rating_stars.twig
tab_bar.twig
```

### Commerce components
Used for product and transactional UI:

```text
product_card_standard.twig
product_card_featured.twig
product_image_gallery.twig
price_block.twig
add_to_cart_button.twig
wishlist_button.twig
stock_badge.twig
discount_badge.twig
product_tabs.twig
related_products_carousel.twig
```

### Section components
Used to build pages, especially homepage:

```text
hero_banner.twig
category_grid.twig
featured_products.twig
benefits_bar.twig
promo_banners.twig
newsletter_signup.twig
blog_preview.twig
brand_logos.twig
upsell_grid.twig
```

---

## Page Files Already In Project

These custom templates exist in the project:

```text
common/header.twig
common/footer.twig
common/home.twig

product/category.twig
product/product.twig

checkout/cart.twig
checkout/checkout.twig

account/account.twig
account/login.twig
account/register.twig
account/order_list.twig
account/address.twig
account/wishlist.twig

information/information.twig
```

---

## Theme Settings Already Planned / Built

NovaKur uses theme settings like:

- primary color
- secondary color
- base font
- heading font
- container width
- header layout
- footer layout
- enable hero banner
- enable category grid
- enable featured products
- enable benefits bar

These are meant to flow from admin settings into Twig and CSS variables.

---

## Important Development Principles

Any future AI or developer must follow these rules.

### Rule 1
Do not break OpenCart core unnecessarily.

### Rule 2
Preserve modularity. Do not collapse components into giant templates.

### Rule 3
Prefer Twig includes over duplicated markup.

### Rule 4
Keep visual system token-based where possible.

### Rule 5
Treat NovaKur as a framework, not a one-off theme.

### Rule 6
Always maintain OC4 compatibility.

---

## Major Problems Already Encountered

These are important because future AI should not repeat the same mistakes.

### Problem A — OC3 vs OC4 structure confusion
The project was first built with an OC3-style assumption:

```text
catalog/view/theme/novakur_base/
```

Then migrated to OC4-style:

```text
catalog/view/template/
catalog/view/assets/
```

This caused path confusion and broken includes.

### Problem B — Overwriting core template folder
At one point, replacing the whole `catalog/view/template` folder broke OpenCart because core files like:

```text
common/column_left.twig
product/thumb.twig
```

were missing.

**Lesson:** Never replace the full stock template tree blindly.

### Problem C — Twig include path issues
Relative Twig includes like:

```twig
{% include 'component/layout/header_desktop.twig' %}
```

caused loader failures in OC4.

These were refactored to explicit paths like:

```twig
{% include 'catalog/view/template/component/layout/header_desktop.twig' %}
```

### Problem D — Theme registration issues
The theme has rendered on the storefront, but OpenCart 4 admin has not cleanly recognized it as a native selectable theme in the theme list.

This means NovaKur is currently partly functioning as a **manual storefront override**, while the **true OC4 theme-extension registration** remains incomplete.

### Problem E — Admin blank page
Admin broke at one stage due to copied custom files / admin extension conflicts, and was restored by re-copying original OpenCart admin/system core files.

**Lesson:** Be careful when touching admin core.

---

## Current Reality of the Project

### What is working
- OpenCart 4 is installed locally in XAMPP
- storefront works
- custom NovaKur storefront has rendered
- custom components exist
- CSS pipeline works
- admin works again
- installer zip was created and uploads

### What is not fully solved
- NovaKur does not yet cleanly appear as a normal native theme in OC4 admin theme lists
- package/registration structure likely still needs refinement
- current storefront rendering is functional but visually still MVP/basic in some areas
- real data integration and premium polish need improvement

---

## Local Environment Snapshot

### Local OpenCart path
```text
/Applications/XAMPP/xamppfiles/htdocs/opencart
```

### Project root
```text
/Users/hilalahamd/MyRestProjects/OpenCartProject/OpenCartThemes
```

### Stock OpenCart source
```text
/Users/hilalahamd/MyRestProjects/OpenCartProject/opencart-4.1.0.3
```

### Current generated package path
Example recent package:

```text
/Users/hilalahamd/MyRestProjects/OpenCartProject/OpenCartThemes/build/novakur_base_oc4_theme.ocmod.zip
```

---

## Most Important Pending Goal

The next major technical goal is:

### Make NovaKur a true native OpenCart 4 theme extension
So that it:

- appears in `Extensions → Extensions → Themes`
- can be installed normally
- can be selected in store settings
- preserves its current storefront architecture

This is the biggest unresolved platform issue.

---

## Secondary Goal After Registration

Once native registration is fixed, the next priority is:

### Upgrade the storefront from MVP rendering to premium final design

That means:

- stronger hero section
- better homepage spacing
- real category data
- real featured products
- improved product cards
- better visual hierarchy
- real-world store polish

---

## Immediate Priority List

Any future AI should prioritize in this order:

### Priority 1
Fix native OC4 theme registration/package structure.

### Priority 2
Verify admin theme settings work end-to-end.

### Priority 3
Bind real OpenCart data to homepage/category/product blocks.

### Priority 4
Polish visual design to match intended premium NovaKur look.

### Priority 5
Prepare section registry / orchestrator for v2.

---

## Safe Prompt to Start a New AI Session

Paste this into a new AI session:

```text
You are continuing development of NovaKur, a modular component-driven storefront framework built on OpenCart 4.

This is not a normal theme. It is intended to become a reusable commerce frontend architecture.

Important context:
- Project root: OpenCartThemes
- Core frontend structure:
  - catalog/view/template/common
  - catalog/view/template/product
  - catalog/view/template/checkout
  - catalog/view/template/account
  - catalog/view/template/information
  - catalog/view/template/component/{layout,ui,commerce,sections}
- Assets are in:
  - catalog/view/assets/{scss,js,dist}
- Admin theme files are in:
  - admin/controller/extension/theme/novakur_base.php
  - admin/language/en-gb/extension/theme/novakur_base.php
  - admin/view/template/extension/theme/novakur_base.twig
- A custom catalog/controller/common/header.php exists and is important.

Known history:
- The project was originally scaffolded in an OC3-like way and later migrated to OC4.
- Relative Twig component include paths caused issues and were refactored to explicit catalog/view/template/component/... paths.
- The storefront can render custom NovaKur markup, but the theme is not yet cleanly registered as a native OC4 theme.
- Installer package uploads, but NovaKur still does not appear properly as a theme in admin theme lists.
- Do not break OpenCart core files.
- Do not replace the entire stock template tree blindly.

Your first task:
Audit the current OC4 theme registration/discovery structure and make NovaKur a true native OC4 theme extension while preserving the current storefront component architecture.
```

---

## Safe Prompt for Package/Registration Repair

```text
Audit and repair NovaKur as a native OpenCart 4 theme extension.

Goal:
Make NovaKur appear properly in:
- Extensions → Extensions → Themes
- Store theme selection

Constraints:
- Preserve current storefront templates/components/assets
- Preserve current admin theme settings logic
- Do not redesign the storefront
- Do not break OpenCart core

Investigate:
- correct native OC4 theme structure
- how the built-in basic theme is discovered
- required file locations
- required install metadata
- required admin/controller/language/template shape
- whether manual oc_extension insertion is insufficient because structure is still not native

Deliver:
1. what is structurally wrong now
2. exact corrected file/package tree
3. all files changed
4. rebuilt installer package path
5. exact install steps
6. any old files to remove from local XAMPP before reinstall
```

---

## Safe Prompt for Storefront Polish

```text
Now that NovaKur storefront renders, perform a premium visual polish + real data integration pass.

Goals:
- improve hero section
- improve category grid
- improve featured products block
- improve product card styling
- use real OpenCart data where available
- keep fallbacks if data is missing
- preserve OC4 compatibility
- preserve current component architecture

Do not redesign admin or break registration work.

Deliver:
1. files changed
2. visual improvements made
3. real data bindings added
4. placeholders still remaining
```

---

## Strategic Definition

The single most important definition of NovaKur is:

> NovaKur is a component-driven storefront framework for OpenCart that is evolving from a custom theme into a reusable commerce platform layer.

Any future work should reinforce that idea.

---

## Final Guidance for Future AI

If you are a future AI continuing this project:

- respect the architecture
- do not flatten the component system
- solve registration cleanly
- stabilize before adding major features
- use real OpenCart data first
- treat NovaKur as a platform, not just a theme

