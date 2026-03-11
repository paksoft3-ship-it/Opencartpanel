# Prompt: NovaKur Fashion Theme (novakur-fashion)

## Context — Read First

You are building **NovaKur Fashion** (`novakur_fashion`) — an OpenCart 4 theme specifically designed for fashion, clothing, and lifestyle stores.

Bold editorial layout. Full-bleed imagery. Magazine-style grid. Instagram-inspired look.
Think ZARA, H&M, Net-a-Porter.

## Platform
- OpenCart 4.0 / 4.1, PHP 8.1+, Twig 3, SCSS, Vanilla JS
- OCMOD package

## Architecture
Same as NovaKur base:
```
extension/novakur_fashion/
  admin/controller/theme/novakur_fashion.php
  admin/language/en-gb/theme/novakur_fashion.php
  admin/view/template/theme/novakur_fashion.twig
  catalog/controller/event/novakur_fashion.php
  catalog/view/template/
  catalog/view/assets/scss/
  catalog/view/assets/js/
  catalog/view/assets/dist/css/main.css
  install.json
```

## Design System (Fashion)

### Colors
- Background: `#FAFAFA`
- Text: `#0A0A0A`
- Accent: `#C9A96E` (warm gold)
- Secondary: `#8B6A4A` (warm brown)
- Light surface: `#F5F0EB`
- Border: `#E8DDD4`
- Sale: `#C41E3A` (deep red)
- Button: `#0A0A0A` (black)

### Typography
- Display/Hero: `Playfair Display` or `Cormorant Garamond` (serif, elegant)
- Body: `Montserrat` or `Lato` (clean sans-serif)
- Heading sizes: editorial scale — 64/48/32/24px
- Letter spacing on uppercase: 0.15em

### Spacing
- Generous: 80px between sections minimum
- Product grid: masonry-inspired irregular heights

### Unique Components

**Fashion-specific sections:**
- `sections/lookbook_grid.twig` — full-bleed image grid (2-col asymmetric)
- `sections/style_editorial.twig` — 50/50 text+image editorial block
- `sections/instagram_grid.twig` — 6-photo Instagram-style grid
- `sections/size_chart_teaser.twig` — "Find Your Size" CTA block
- `sections/collection_banner.twig` — full-screen collection launch banner

**Fashion product card:**
- Hover: second product image swaps in (front/back photo)
- "Hızlı Ekle" overlay with size selector
- Color swatches below card
- "Favori" heart icon always visible

**Product page enhancements:**
- Image gallery: fullscreen zoom on click
- Size selector with availability dots
- "Beden Rehberi" link (integrates with nk-size-guide module if installed)
- "Komple Bak" section (styled with the product)
- Sticky Add to Cart bar on mobile

## Pages to Build

1. `common/home.twig` — editorial homepage with lookbook
2. `common/header.twig` — centered logo, full-width nav bar, search overlay
3. `common/footer.twig` — dark footer with newsletter, social links
4. `product/product.twig` — fashion product detail with large gallery
5. `product/category.twig` — masonry grid, color filter, size filter
6. `checkout/cart.twig` — clean checkout cart
7. `checkout/checkout.twig` — multi-step checkout

## Admin Settings

- Primary color (accent gold)
- Hero image upload
- Collection banner text and link
- Show/hide Instagram grid
- Lookbook images (up to 6 upload slots)
- Homepage layout (editorial / standard)

## OCMOD Package

install.json:
```json
{
  "name": "NovaKur Fashion Theme",
  "description": "Editorial fashion-forward OpenCart 4 theme for clothing and lifestyle stores.",
  "version": "1.0.0",
  "author": "NovaKur",
  "type": "theme",
  "code": "novakur_fashion"
}
```

## Deliverable
All files with complete working code.
Every component fully implemented — no stub templates.
Output each file with its full path and complete code.
