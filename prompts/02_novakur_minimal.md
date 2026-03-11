# Prompt: NovaKur Minimal Theme (novakur-minimal)

## Context — Read First

You are building a new OpenCart 4 theme called **NovaKur Minimal** (`novakur_minimal`).

It is a variant of the NovaKur theme family but with a completely different aesthetic: ultra-clean, whitespace-heavy, typographic, monochromatic. Think Muji, Apple, Arc'teryx.

## Platform
- OpenCart 4.0 / 4.1
- PHP 8.1+, Twig 3, SCSS, Vanilla JS
- OCMOD installer package

## Architecture (same as NovaKur base — see novakur_superprompt.md)

```
extension/novakur_minimal/
  admin/controller/theme/novakur_minimal.php
  admin/language/en-gb/theme/novakur_minimal.php
  admin/view/template/theme/novakur_minimal.twig
  catalog/controller/event/novakur_minimal.php
  catalog/view/template/         ← all Twig templates
  catalog/view/assets/scss/      ← all SCSS files
  catalog/view/assets/js/        ← all JS files
  catalog/view/assets/dist/css/main.css  ← compiled
  install.json
```

## Design System (Minimal)

### Colors
- Background: `#FFFFFF`
- Surface: `#FAFAFA`
- Border: `#E5E5E5`
- Text primary: `#111111`
- Text secondary: `#666666`
- Text muted: `#999999`
- Accent: `#111111` (monochromatic — no blue)
- Hover: `#000000`
- Sale/badge: `#E00000`

### Typography
- Font: `Inter` or `system-ui` (no decorative fonts)
- Heading sizes: 32/24/20/16px
- Body: 14px line-height 1.6
- Uppercase tracking for labels: `letter-spacing: 0.1em`
- Weight: 400 body / 500 medium / 700 bold — no 900/extrabold

### Spacing
- Base unit: 8px
- Generous whitespace — minimum 48px between sections
- Max container width: 1280px

### Components
- Buttons: rectangular (border-radius: 2px), outline style as default
- Cards: no shadow, only border `1px solid #E5E5E5`
- Images: grayscale filter on hover
- Icons: minimal line icons (Lucide or similar), no filled icons
- Animations: subtle only — 200ms ease, no bounce/spring

## Pages to Build

### All 7 page templates (same pages as NovaKur base):
1. `common/home.twig` — clean grid, no hero full-bleed, editorial layout
2. `common/header.twig` — minimal header: logo left, nav center, icons right
3. `common/footer.twig` — 4-column footer with minimal links
4. `product/product.twig` — product detail with large typography, specs table
5. `product/category.twig` — list/grid toggle, sidebar filters minimal
6. `checkout/cart.twig` — clean table layout
7. `checkout/checkout.twig` — single-page checkout, step indicators

### Homepage Sections
- Simple announcement strip (text only, no background color)
- Typographic hero (large text, no image)
- 3-column product grid (no card shadows)
- Featured product (full-width, image left + text right)
- Brand logos strip
- Newsletter signup (email input only, clean)

## Admin Settings

Same settings as NovaKur base but with Minimal defaults:
- Primary color: `#111111`
- Font: `Inter`
- Container width: `1280px`
- Border radius: `2px`
- Enable/disable homepage sections

## OCMOD Package

```
novakur_minimal.ocmod.zip
  extension/novakur_minimal/...
  install.json
```

install.json:
```json
{
  "name": "NovaKur Minimal Theme",
  "description": "Ultra-clean, whitespace-driven OpenCart 4 theme for premium brands.",
  "version": "1.0.0",
  "author": "NovaKur",
  "type": "theme",
  "code": "novakur_minimal"
}
```

## Deliverable

All files with complete working Twig, SCSS, and PHP code.
Every component must be fully implemented — no stub templates.
Output each file with its full path.
