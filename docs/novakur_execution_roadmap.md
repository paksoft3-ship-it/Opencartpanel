# NovaKur Execution Roadmap

## Practical Development Plan

**Project:** NovaKur Commerce Framework  
**Base Platform:** OpenCart 4.x  
**Current State:** v1 theme + modular components

Goal: evolve NovaKur into a reusable storefront architecture and future commerce platform layer.

---

## Phase 1 — Stabilize NovaKur v1 (Critical Foundation)

This phase is about making the existing architecture stable and production-ready.

Do not add complex new features yet.

Focus on correctness, reliability, and compatibility with OpenCart 4.

### 1.1 Fix Theme Registration (Highest Priority)
The theme must behave as a proper OpenCart 4 extension.

Tasks:

- correct extension structure
- installer package reliability
- ensure theme appears in `Admin → Extensions → Extensions → Themes`
- ensure it can be selected in `System → Settings → Store → Theme`

Expected result:

NovaKur installs like a normal OpenCart theme.

### 1.2 Verify Page Rendering
Check all storefront pages.

Required pages:

- homepage
- product page
- category page
- cart page
- checkout page
- account pages

Tasks:

- verify Twig templates load correctly
- remove missing includes
- ensure header/footer always render
- confirm CSS loads everywhere

### 1.3 Real Data Integration
Ensure every page uses actual OpenCart data.

Example improvements:

- category grid → load real categories
- featured products → use OpenCart featured module
- product card → display real product data
- product page → correct gallery and stock status

Fallback behavior:

```text
if no data exists → show empty state
```

Avoid placeholder/demo content.

### 1.4 CSS System Stabilization
Improve the SCSS pipeline.

Tasks:

- remove duplicate styles
- move hardcoded values to variables
- ensure `main.css` loads globally

Example token structure:

```css
--nk-primary
--nk-secondary
--nk-radius
--nk-spacing
--nk-container-width
```

Goal:

Make the design system predictable and themeable.

### 1.5 Mobile UX Verification
Audit responsive design.

Check:

- header navigation
- product grid
- product page gallery
- cart page layout
- checkout usability

Goal:

NovaKur should feel mobile-first, not desktop-adapted.

---

## Phase 2 — Expand the Design System

Now NovaKur moves from a stable theme to a true design system.

### 2.1 Design Tokens
Move all visual constants into tokens.

Examples:

- colors
- spacing
- border radius
- font scale
- shadow system

Example:

```css
--nk-color-primary
--nk-color-accent
--nk-spacing-xs
--nk-spacing-lg
```

Benefit:

Store branding becomes easier.

### 2.2 Component Standardization
Ensure every component follows the same structure.

Each component should define:

- required inputs
- optional inputs
- fallback behavior

Example contract:

Product card expects:

```text
name
href
thumb
price
special
stock
rating
badge
```

This allows easier reuse.

### 2.3 Component Variants
Allow components to support variants.

Example:

```text
product_card_standard
product_card_compact
product_card_featured
```

This enables layout flexibility.

### 2.4 Layout Variants
Allow multiple header/footer styles.

Example:

Header variants:

```text
header_standard
header_centered
header_mega_menu
```

Footer variants:

```text
footer_minimal
footer_extended
footer_columns
```

These should be controlled through theme settings.

---

## Phase 3 — Section System (Key Platform Feature)

This phase transforms NovaKur into a block-based storefront architecture.

### 3.1 Section Registry
Create a structured list of available sections.

Example:

```text
hero_banner
category_grid
featured_products
benefits_bar
promo_banners
newsletter_signup
blog_preview
brand_logos
```

Each section should define:

- template
- settings
- required data
- optional data

### 3.2 Homepage Section Orchestrator
Instead of hardcoding sections:

```twig
hero_banner
category_grid
featured_products
```

Use a loop:

```text
homepage_sections = [
 hero_banner,
 category_grid,
 featured_products
]
```

Twig renders them dynamically.

### 3.3 Section Settings
Each section should have configurable settings.

Example hero banner settings:

- title
- subtitle
- CTA button
- background image
- layout style

These settings will be stored in theme configuration.

### 3.4 Enable / Disable Sections
Store owners should be able to toggle sections.

Example:

Enable:

```text
featured_products
newsletter_signup
```

Disable:

```text
blog_preview
```

This allows store customization without code.

---

## Phase 4 — Theme Variant System

Now NovaKur becomes adaptable to multiple industries.

### 4.1 Variant Profiles
Define theme variants.

Examples:

```text
electronics
fashion
furniture
cosmetics
grocery
```

Each variant modifies:

- hero style
- product card style
- section defaults
- spacing tone

### 4.2 Variant Configuration Layer
Variants should define:

```text
default homepage sections
default card layout
default color palette
default hero style
```

This allows launching new store themes quickly.

### 4.3 Industry Templates
From the same architecture you can create:

- NovaKur Electronics
- NovaKur Fashion
- NovaKur Furniture
- NovaKur Cosmetics

All sharing the same core system.

---

## Phase 5 — Commerce UX Enhancements

Improve the shopping experience.

### 5.1 Product Page UX
Enhancements:

- sticky add-to-cart
- improved image gallery
- variant selection UI
- stock messaging

### 5.2 Category Page UX
Improve browsing.

Examples:

- quick add to cart
- hover product preview
- better filters
- improved pagination

### 5.3 Cart UX
Improve conversion.

Examples:

- mini cart drawer
- better quantity editing
- cart summary clarity

### 5.4 Checkout UX
Simplify checkout flow.

Goals:

- fewer steps
- clearer layout
- mobile usability

---

## Phase 6 — Platform Preparation

This prepares NovaKur for long-term expansion.

### 6.1 Page Builder Foundations
Prepare data structure for visual builders.

Example:

Each section defined as JSON configuration.

### 6.2 Store Presets
Allow generating stores quickly.

Example:

```text
create store → choose template → electronics
```

NovaKur configures:

- sections
- colors
- layout
- hero

### 6.3 Module Ecosystem
Potential future modules:

- advanced mega menu
- advanced filters
- marketing banners
- conversion widgets
- product comparison

---

## Phase 7 — Business Layer

Once NovaKur is stable, you can monetize through:

- premium themes
- custom client stores
- theme variants
- module packs
- agency frameworks

---

## Recommended Development Order

Most important sequence:

1. stabilize NovaKur v1
2. improve design token system
3. implement section registry
4. create homepage section orchestrator
5. introduce theme variants
6. enhance commerce UX
7. prepare builder architecture

---

## Final Strategy

NovaKur should grow in this order:

### Stage 1
Stable OpenCart theme.

### Stage 2
Reusable component architecture.

### Stage 3
Section-based storefront system.

### Stage 4
Variant-driven theme platform.

### Stage 5
Commerce framework ecosystem.

---

## Final Insight

Do not treat NovaKur as a one-off theme.

Treat it like a commerce UI framework for OpenCart.

That mindset ensures:

- clean architecture
- scalability
- future monetization
- platform potential

