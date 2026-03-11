# NovaKur Base — Full UI Component Architecture

**Platform:** OpenCart 4.x | **Implementation:** Twig + SCSS + JavaScript Modules
**Design Token Base:** Inter font, `#2563EB` primary, `#1E3A8A` deep, `#38BDF8` accent, `#0F172A` text, `#FFFFFF` background

---

## STEP 1 — Layout Components

### 1.1 `layout/AnnouncementBar`

| Field | Value |
|---|---|
| **Purpose** | Full-width promotional strip above the header for offers, shipping notices, or promo codes |
| **Appears on** | All pages — site-wide |
| **Reusability** | Global / configurable per variant |

**Design details extracted:**
- Primary-colored (`bg-primary`) background, white text, `text-xs` font
- Supports two messages separated by a divider (desktop only)
- Configurable: dismissible toggle, message count, background color override

---

### 1.2 `layout/HeaderDesktop`

| Field | Value |
|---|---|
| **Purpose** | Global desktop header — logo, predictive search, user action icons, cart |
| **Appears on** | All pages — sticky, `z-50` |
| **Reusability** | Global / 3 confirmed visual variants |

**Sub-regions:**
- **Logo block** — icon + wordmark, replaceable SVG
- **Predictive Search** — category dropdown prefix + text input + search button (variation: rounded pill vs. flat bar)
- **User Action Bar** — Account icon, Wishlist icon with dot indicator, Cart button with badge count + total
- **Nav Row** — Secondary horizontal nav strip below the main row, scrollable on overflow

**Variants identified:**
- `header-v1`: Compact single-row, white background, search pill
- `header-v2`: Two-row — top bar + category mega-menu strip
- `header-v3`: Two-row, glassmorphism `backdrop-blur-md`

---

### 1.3 `layout/MegaMenu`

| Field | Value |
|---|---|
| **Purpose** | Full-width dropdown panel for deep category navigation |
| **Appears on** | Desktop header, triggered on hover or click |
| **Reusability** | High — configurable column count (2–4 columns) |

**Design details:**
- `800px` wide panel, `grid-cols-4` layout
- Each column: bold heading + 4 sub-links
- Optional 4th column: featured product image card with badge overlay
- Hover-triggered with `opacity-0 → opacity-100` transition + `invisible → visible`
- Dark mode compatible

---

### 1.4 `layout/MobileNavDrawer`

| Field | Value |
|---|---|
| **Purpose** | Off-canvas slide-in navigation for mobile and tablet |
| **Appears on** | All pages — mobile breakpoint |
| **Reusability** | Global |

**Design details:**
- `w-10/12 max-w-sm` slide-in from left
- Blurred backdrop overlay (`backdrop-blur-sm`)
- Drawer header: user avatar + greeting + close button
- Section groups: Categories (with chevron-right arrows) and Account Settings
- Drawer footer: CTA button (Sign Out / Login)
- Mobile top bar: hamburger + logo + search icon + cart icon with badge

---

### 1.5 `layout/Footer`

| Field | Value |
|---|---|
| **Purpose** | Global site footer with brand info, link columns, newsletter, and legal |
| **Appears on** | All pages |
| **Reusability** | Global / 3 confirmed visual variants |

**Variants identified:**
- `footer-modular-card`: White card panel, newsletter card inset, 5 link columns, payment icons row
- `footer-minimalist`: Clean minimal, 4 columns, single-line bottom bar
- `footer-deep-contrast`: Dark background, high contrast text

**Sub-regions:**
- Brand block (logo + tagline + social links)
- Link column group (Shop, Support, Company, Contact)
- Newsletter form (email input + subscribe button)
- Bottom bar (copyright + payment icons + legal links)

---

### 1.6 `layout/Breadcrumb`

| Field | Value |
|---|---|
| **Purpose** | Hierarchical page path for SEO and navigation context |
| **Appears on** | Product detail, category listing, cart, account, information pages |
| **Reusability** | Global |

**Design details:**
- `text-sm text-slate-500` with `chevron_right` icon separators
- Last segment: `text-slate-900 font-medium` (non-linked)
- Overflow horizontal scroll on mobile (`overflow-x-auto whitespace-nowrap`)

---

### 1.7 `layout/GlobalContainer`

| Field | Value |
|---|---|
| **Purpose** | Consistent max-width content wrapper |
| **Appears on** | All pages |
| **Reusability** | Universal |

**Design details:**
- `max-w-7xl mx-auto px-4 lg:px-8`
- Responsive padding: `px-4 sm:px-6 lg:px-8`
- Background surface: `bg-background-light` (`#f6f6f8`) on page, `bg-white` on cards

---

## STEP 2 — UI Components

### 2.1 `ui/Button`

| Variant | Description | Where Used |
|---|---|---|
| **Primary** | `bg-primary text-white rounded-xl`, hover darkens | Hero CTA, Add to Cart, Subscribe |
| **Secondary** | `bg-primary/10 text-primary rounded-xl` | Secondary CTAs, filter pills |
| **Outline** | `border-2 border-slate-200 text-slate-600 rounded-xl` | Ghost actions, cancel buttons |
| **Dark** | `bg-slate-900 text-white rounded-xl` | Cart button in header, compact actions |
| **Icon-only** | `p-2.5 rounded-xl` or `p-2.5 rounded-full` | Wishlist, account, nav arrows |
| **Pill** | `rounded-full px-8 py-4` | Hero banners, mobile CTAs |

**Reusability:** Universal — highest priority component

---

### 2.2 `ui/Badge`

| Variant | Style | Where Used |
|---|---|---|
| **Sale** | `bg-red-500 text-white rounded-full text-[10px]` | Product card overlay, listing |
| **New Arrival** | `bg-primary text-white rounded` | Product card overlay |
| **Hot / Featured** | `bg-indigo-600 text-white rounded` | Product card overlay |
| **In Stock** | `bg-green-100 text-green-600 rounded-full` | Product detail, listing |
| **Low Stock** | `bg-amber-100 text-amber-600 rounded-full` | Product detail |
| **Out of Stock** | `bg-slate-200 text-slate-500 rounded-full` | Product cards |
| **Counter** | `bg-primary text-white rounded-full h-4 w-4` | Cart icon, wishlist icon |
| **Label** | `bg-primary/10 text-primary rounded-full px-3 py-1` | Category tags, section labels |

**Reusability:** Universal

---

### 2.3 `ui/RatingStars`

- 5 `material-symbols-outlined:star` icons in amber-400
- States: `filled` (full star), `star_half` (half), `star_outline` (empty)
- Compact version: single star + numeric score `4.9` + review count `(128 reviews)`
- Full version: 5-star display + score breakdown bars (product detail)
- **Used in:** Product cards, product detail, listing sidebar filter, review section

---

### 2.4 `ui/SearchInput`

| Variant | Description |
|---|---|
| **Standard** | Pill-shaped `bg-slate-100 rounded-2xl`, icon prefix, no border |
| **Advanced** | Category dropdown prefix + text field + Search CTA button |
| **Mobile Overlay** | Full-width below header bar, `rounded-xl focus:ring-primary/50` |

- Focus state: `ring-2 ring-primary/20` or `ring-primary`
- **Used in:** Header desktop (all variants), mobile header, design system docs sidebar

---

### 2.5 `ui/Dropdown` / `ui/Select`

- `bg-slate-100 border-none rounded-lg focus:ring-primary/20`
- Used for Sort By (category listing), Category prefix (search), Options (product variants)
- **Reusability:** Forms, listing controls

---

### 2.6 `ui/TabBar`

- Pill-style tab group: `bg-slate-100 p-1 rounded-xl`
- Active tab: `bg-white shadow-sm rounded-lg font-bold`
- Inactive tab: `text-slate-500 rounded-lg`
- **Used in:** Featured products section (New Arrivals / Best Sellers / On Sale), Product detail (Description / Specifications / Reviews)

---

### 2.7 `ui/Pagination`

- Button size: `w-10 h-10 rounded-xl`
- Active page: `bg-primary text-white font-bold shadow-lg shadow-primary/20`
- Inactive page: `bg-white border border-slate-200 text-slate-600`
- Prev/Next: icon buttons with `chevron_left` / `chevron_right`
- Ellipsis separator: `px-2 text-slate-400`
- **Used in:** Category listing page

---

### 2.8 `ui/PriceDisplay`

| State | Markup |
|---|---|
| **Regular** | `text-xl font-bold text-primary $899.00` |
| **Sale (with original)** | Primary price + `line-through text-slate-400` below or inline |
| **Large (product detail)** | `text-4xl font-black text-primary` |

- **Used in:** Every product card, product detail, cart rows

---

### 2.9 `ui/QuantitySelector`

- Layout: `border border-slate-200 rounded-lg` container with `-` / input / `+` buttons
- Button: `px-4 py-3 hover:bg-slate-100`
- Input: `w-12 text-center border-none focus:ring-0 bg-transparent font-bold`
- **Used in:** Product detail, cart table rows, design system forms demo

---

### 2.10 `ui/FormElements`

| Element | Style |
|---|---|
| Text Input | `rounded-xl bg-slate-50 border-slate-200 focus:ring-primary px-4 py-2.5` |
| Select | Same as input, with dropdown arrow |
| Checkbox | `w-5 h-5 rounded border-slate-300 text-primary focus:ring-primary` |
| Radio | `text-primary focus:ring-primary/20` |
| Toggle Switch | `h-6 w-11 rounded-full bg-primary`, inner dot translates on state |
| Range Slider | `h-1.5 bg-slate-200 rounded-full` with primary-colored fill range + handles |

- **Used in:** Filter sidebar, checkout, account forms, newsletter

---

### 2.11 `ui/Accordion`

- Header: icon + label + `expand_more` chevron (rotates on open)
- Body: revealed on expand, collapsible filter groups
- **Used in:** Filter sidebar (Price Range, Brands, Ratings, Availability)

---

### 2.12 `ui/Icons`

- **Library:** Google Material Symbols Outlined (variable font, FILL 0–1, wght 100–700)
- **Filled state:** `font-variation-settings: 'FILL' 1` class `.filled` for active stars
- **Primary icons used:** `search`, `shopping_bag`, `shopping_cart`, `favorite`, `person`, `menu`, `layers`, `local_shipping`, `verified_user`, `cached`, `support_agent`, `star`, `chevron_right`, `arrow_forward`, `add_shopping_cart`, `add`, `remove`, `delete`, `logout`

---

### 2.13 `ui/ToastNotification` *(Implied)*

- Not explicitly in designs but architecturally required for: cart add, wishlist add, form submit feedback
- Structure: icon + message + dismiss, position: bottom-right, `rounded-xl shadow-xl`

---

### 2.14 `ui/ModalOverlay` *(Implied — Quick View)*

- Backdrop: `bg-slate-900/40 backdrop-blur-sm`
- Panel: `bg-white dark:bg-slate-900 rounded-2xl shadow-2xl`
- Used in: Related products "Quick View" button (product detail)

---

## STEP 3 — Product-Related Components

### 3.1 `product/ProductCard`

**Core structure (all variants share this DNA):**

```
[Card Container — white bg, border, rounded-xl, hover:shadow-2xl]
  ├── [Image Container — aspect-square, rounded-lg, overflow-hidden]
  │     ├── [Badge Overlay — top-left: Sale/New/Hot/In Stock]
  │     ├── [Wishlist Button — top-right: white/80 rounded-full, opacity-0 → hover:opacity-100]
  │     ├── [Product Image — object-cover, hover:scale-110]
  │     └── [Quick-Add CTA — bottom overlay, slides up on hover] (variant-dependent)
  └── [Content Block]
        ├── [Category Label — text-[11px] uppercase tracking-widest text-slate-400]
        ├── [Product Name — font-bold line-clamp-1]
        ├── [Rating Row — star icon + score + review count]
        └── [Price + ATC Row — price left, add-to-cart icon button right]
```

**Confirmed variants:**

| Variant | Aspect Ratio | ATC Style | Notes |
|---|---|---|---|
| `card-standard` | `aspect-square` | Icon button (right of price) | Default grid card |
| `card-featured` | `aspect-[4/5]` | Full-width slide-up button on hover | Used in featured sections |
| `card-compact` | `aspect-square` | Icon only, no text | High-density grid |
| `card-list` | Horizontal layout | Inline ATC | Search results / list view |

---

### 3.2 `product/ProductImageGallery`

**Two confirmed layout variants:**

| Variant | Layout | Thumbnail Position |
|---|---|---|
| `gallery-vertical-thumb` | Main image left (col-7) + vertical thumb strip | Left sidebar, scrollable |
| `gallery-horizontal-thumb` | Main image top (4:3 ratio) + horizontal thumb row | Bottom, scrollable |

- Main image: `rounded-xl border border-slate-200 overflow-hidden`
- Active thumbnail: `border-2 border-primary`
- Inactive thumbnail: `border-2 border-transparent hover:border-primary/50`
- **Used in:** Product Detail Page (V1, V2, V3)

---

### 3.3 `product/PriceBlock`

Standalone component used inside product detail (not just cards):

```
[Price Block]
  ├── [Sale Price — text-4xl font-black text-primary]
  ├── [Original Price — text-xl text-slate-400 line-through]
  └── [Discount Badge — bg-red-500 text-white rounded-full] (optional)
```

---

### 3.4 `product/QuantitySelector`

Shared with `ui/QuantitySelector` — used both in product detail and cart.

---

### 3.5 `product/AddToCartButton`

- Full-width version: `bg-primary text-white font-bold py-3 px-8 rounded-lg flex items-center gap-2`
- Icon version: `p-2 bg-slate-900 rounded-lg hover:bg-primary` (used in cards)
- With icon: `shopping_cart` or `add_shopping_cart`
- Active state: `active:scale-[0.98]`

---

### 3.6 `product/WishlistButton`

- Card overlay: `w-8 h-8 bg-white/80 backdrop-blur rounded-full text-slate-400 hover:text-rose-500`
- Product detail standalone: `p-3 border border-slate-200 rounded-lg hover:text-red-500 hover:border-red-200`
- Header wishlist: icon with dot indicator

---

### 3.7 `product/StockBadge`

Shares `ui/Badge` — `In Stock` (green), `Low Stock` (amber), `Out of Stock` (gray).
Also displayed inline as text: `text-sm text-green-600 font-semibold uppercase` in product detail header.

---

### 3.8 `product/DiscountBadge`

Shares `ui/Badge` — overlaid on top-left of product image.
Variants: `-20% SALE` (red), `NEW ARRIVAL` (primary), `HOT` (indigo), `LIMITED` (custom).

---

### 3.9 `product/ProductTabs`

Tabbed content panel on product detail page:
- Tabs: Description, Specifications, Reviews
- Tab bar style: `border-b border-slate-200`, active tab `border-b-2 border-primary`
- Content panels: Description (rich text + checklist), Specs (key-value table), Reviews (rating breakdown + comments)

---

### 3.10 `product/RelatedProductsCarousel`

- Section heading + prev/next icon buttons
- Carousel of Product Cards (4 visible desktop, scrollable mobile)
- Hover overlay: "Quick View" button slides up from bottom
- **Used in:** Product detail page bottom section

---

### 3.11 `product/RecentlyViewed`

- Horizontal scroll strip of grayscale thumbnails (`grayscale hover:grayscale-0`)
- `min-w-[120px] aspect-square rounded-lg`
- **Used in:** Product detail page

---

## STEP 4 — Section Modules

### 4.1 `sections/HeroBanner`

| Property | Value |
|---|---|
| **Purpose** | Primary above-fold promotional section |
| **Components used** | Button (Primary + Secondary), Badge (label), heading, paragraph |
| **Reusability** | High — configurable layout |

**Two layout variants:**
- `hero-split`: `grid-cols-12` — large image left (col-8) + two mini-promo cards right (col-4, grid-rows-2)
- `hero-fullwidth`: Full-width dark image with text overlay left, gradient mask, slider dots

**Shared design tokens:** `rounded-2xl`, `bg-gradient-to-r from-slate-900/80`, text white, CTA buttons inline

---

### 4.2 `sections/CategoryGrid`

| Property | Value |
|---|---|
| **Purpose** | Visual category navigation grid |
| **Components used** | `ui/Badge` (optional), category card |
| **Reusability** | High — column count configurable (2, 4, 6) |

**Category Card structure:**
- Square image container: `aspect-square rounded-2xl overflow-hidden border hover:border-primary/20`
- Image: `hover:scale-110 transition-transform duration-500`
- Label: centered bold text below
- Section header: title + subtitle + "View All" link (`arrow_forward` icon)

---

### 4.3 `sections/FeaturedProducts`

| Property | Value |
|---|---|
| **Purpose** | Tabbed product showcase (New Arrivals / Best Sellers / On Sale) |
| **Components used** | `ui/TabBar`, `product/ProductCard`, prev/next nav buttons |
| **Reusability** | High — tab labels and product source are configurable |

- Tab bar + carousel navigation row at top
- `grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8` product grid
- Both tab-switching and carousel navigation controls present

---

### 4.4 `sections/PromoBanners`

| Property | Value |
|---|---|
| **Purpose** | Side-by-side or stacked promotional image banners |
| **Components used** | `ui/Button`, image overlay, text block |
| **Reusability** | High — 1, 2, or 3 column configurations |

**Design:** `grid-cols-1 md:grid-cols-2 gap-8`, each banner:
- `h-80 rounded-2xl overflow-hidden`
- Absolute-positioned text + CTA button over image
- Hover: `group-hover:scale-110 transition-transform duration-1000`

---

### 4.5 `sections/BenefitsBar`

| Property | Value |
|---|---|
| **Purpose** | 4-item trust signal strip |
| **Components used** | Material icon, heading, subtext |
| **Reusability** | High — icon + text pairs configurable |

**Design:**
- `grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 py-10 border-y`
- Each item: `w-12 h-12 rounded-2xl bg-primary/10 text-primary` icon + text block
- Hover: icon container `group-hover:scale-110`
- Default items: Free Shipping, Secure Payment, Easy Returns, 24/7 Support

---

### 4.6 `sections/NewsletterSignup`

| Property | Value |
|---|---|
| **Purpose** | Email capture module |
| **Components used** | `ui/FormElements` (email input), `ui/Button` (Primary), heading, subtext |
| **Reusability** | High — 2 confirmed layout variants |

**Variants:**
- `newsletter-inline`: `flex-row` layout, text left, form right, `bg-primary/5 rounded-3xl p-16`
- `newsletter-card`: Card style with blurred circles decorative background, centered on footer top

---

### 4.7 `sections/BestSellersCarousel`

- Horizontal scrolling product carousel with prev/next buttons
- Lazy-loaded grid with tab filter (reuses `sections/FeaturedProducts` pattern)
- Renderable as a standalone section module

---

### 4.8 `sections/BlogPreview`

- `aspect-video` image top, `p-5` content block below
- Category label (`text-[10px] uppercase tracking-widest text-primary`)
- Title `text-lg font-bold hover:text-primary`
- Excerpt `text-sm text-slate-500 line-clamp-2`
- Blog Card is a variant inside `ui/Card` variants in the design system

---

### 4.9 `sections/BrandLogos` *(Architectural)*

- Horizontal scroll or grid of brand logo images, grayscale by default
- Hover: `grayscale-0 opacity-100` (same pattern as recently viewed thumbnails)

---

### 4.10 `sections/UpsellGrid` / `sections/FrequentlyBoughtTogether`

- `grid-cols-2 sm:grid-cols-4 gap-4` mini product grid
- Minimal card: image + name + price only (no ATC button visible)
- **Used in:** Cart page bottom section

---

## STEP 5 — Page Templates

### 5.1 `templates/HomePage`

**Structure:**
```
AnnouncementBar
HeaderDesktop (with Mega Menu + Nav Row)
  └── Main [max-w-7xl container, py-8, space-y-16]
        ├── HeroBanner (split or fullwidth variant)
        ├── CategoryGrid (6-col)
        ├── FeaturedProducts (tabbed, 4-col)
        ├── PromoBanners (2-col)
        ├── BenefitsBar (4-col)
        └── NewsletterSignup
Footer (modular card variant)
```

**Key components:** AnnouncementBar, HeroBanner, CategoryGrid, FeaturedProducts, PromoBanners, BenefitsBar, NewsletterSignup

---

### 5.2 `templates/CategoryListingPage`

**Structure:**
```
HeaderDesktop
  └── Main [max-w-7xl container]
        ├── Breadcrumb
        ├── Page Title (H1)
        └── [grid: sidebar + product grid]
              ├── Sidebar [w-64 shrink-0]
              │     ├── FilterAccordion: Price Range Slider
              │     ├── FilterAccordion: Brand Checkboxes
              │     ├── FilterAccordion: Rating Filter
              │     └── FilterAccordion: Availability Toggle
              └── Product Grid Area [flex-1]
                    ├── GridControls (results count + sort select)
                    ├── ProductCard Grid [grid-cols 1/2/3]
                    └── Pagination
Footer (minimalist or standard)
```

**Key components:** Breadcrumb, FilterSidebar, GridControls, ProductCard, Pagination

---

### 5.3 `templates/ProductDetailPage`

**Structure:**
```
HeaderDesktop
  └── Main [max-w-7xl container, py-8]
        ├── Breadcrumb
        ├── [Product Hero — grid-cols-12]
        │     ├── ImageGallery [col-span-7]
        │     │     └── DiscountBadge (on main image)
        │     └── ProductInfo [col-span-5]
        │           ├── Product Name (H1)
        │           ├── RatingStars + StockBadge
        │           ├── PriceBlock (sale + original)
        │           ├── Product Description
        │           ├── [QuantitySelector + AddToCartButton + WishlistButton]
        │           └── ShippingInfoBlock (2-col: Fast Delivery, Easy Returns)
        ├── ProductTabs (Description / Specs / Reviews)
        ├── RelatedProductsCarousel
        └── RecentlyViewed
Footer
```

**Key components:** Breadcrumb, ImageGallery, PriceBlock, QuantitySelector, AddToCartButton, WishlistButton, ProductTabs, RelatedProductsCarousel, RecentlyViewed

---

### 5.4 `templates/CartPage`

**Structure:**
```
HeaderDesktop
  └── Main [max-w-7xl, py-8]
        ├── Breadcrumb
        ├── Page Title + subtitle
        └── [grid-cols-12]
              ├── CartItemsTable [col-span-8]
              │     ├── Table Header (Product / Price / Qty / Total)
              │     ├── CartItemRow x N (image + name + variant + price + QuantitySelector + total + delete)
              │     └── Continue Shopping link
              └── OrderSummary [col-span-4, sticky top-24]
                    ├── Subtotal / Shipping / Tax rows
                    ├── Order Total
                    ├── ProceedToCheckout Button
                    └── TrustSignals (SSL + Returns icons)
        └── UpsellGrid (Frequently Bought Together)
Footer (minimal)
```

**Key components:** CartItemRow, QuantitySelector, PriceDisplay, OrderSummary, TrustSignals, UpsellGrid

---

### 5.5 `templates/AccountPages`

**Inferred from mobile drawer design:**

```
Sub-pages:
  ├── account/Login
  ├── account/Register
  ├── account/Dashboard (My Orders, Wishlist, Settings)
  ├── account/OrderDetail
  └── account/AddressBook
```

**Key components:** FormElements, Button, Breadcrumb, OrderRow, WishlistGrid

---

### 5.6 `templates/InformationPage`

Static content pages (About, Privacy, Terms, Shipping Policy):

```
HeaderDesktop
  └── Main [max-w-7xl, py-8]
        ├── Breadcrumb
        ├── Page Title (H1)
        └── Rich Text Content Block (prose layout, sidebar optional)
Footer
```

---

### 5.7 `templates/CheckoutPage` *(Architectural — implied)*

```
Simplified Header (logo only, no nav)
  └── Multi-step form:
        ├── Step 1: Shipping Information (FormElements)
        ├── Step 2: Shipping Method (radio selection)
        ├── Step 3: Payment (FormElements)
        └── Order Summary sidebar (sticky)
Minimal Footer
```

---

## STEP 6 — Final Component Map

```
NovaKur Base Theme Framework
│
├── FOUNDATION TOKENS
│     ├── Colors: primary #2563EB, deep #1E3A8A, accent #38BDF8, neutral #0F172A
│     ├── Typography: Inter, scale H1 → H4 + Body + Label
│     ├── Spacing: 4/8/12/16/20/24px grid
│     ├── Radius: sm 4px, lg 8px, xl 12px, 2xl 16px, full
│     └── Shadows: slate-200, primary/20 glow, 2xl depth
│
├── LAYOUT COMPONENTS [layout/]
│     ├── AnnouncementBar          — configurable, dismissible
│     ├── HeaderDesktop            — 3 variants (v1, v2, v3-glass)
│     ├── MegaMenu                 — 2–4 col dropdown, featured image slot
│     ├── MobileNavDrawer          — off-canvas, grouped navigation
│     ├── Footer                   — 3 variants (card, minimalist, dark)
│     ├── Breadcrumb               — auto-generated from OC route
│     └── GlobalContainer          — max-w-7xl responsive wrapper
│
├── UI COMPONENTS [ui/]
│     ├── Button                   — Primary, Secondary, Outline, Dark, Icon, Pill
│     ├── Badge                    — Sale, New, Hot, Stock, Counter, Label
│     ├── RatingStars              — filled/half/empty, compact + full breakdown
│     ├── SearchInput              — Standard, Advanced (with category), Mobile
│     ├── Dropdown/Select          — Sort, Category, Options
│     ├── TabBar                   — Pill-container tabs + underline tabs
│     ├── Pagination               — numbered + prev/next + ellipsis
│     ├── PriceDisplay             — Regular, Sale, Large
│     ├── QuantitySelector         — border-container +/- input
│     ├── FormElements             — Input, Select, Checkbox, Radio, Toggle, Slider
│     ├── Accordion                — Collapsible filter groups
│     ├── Icons                    — Material Symbols Outlined (variable)
│     ├── ToastNotification        — cart/wishlist/form feedback
│     └── ModalOverlay             — Quick View, lightbox
│
├── COMMERCE COMPONENTS [product/]
│     ├── ProductCard              — 4 variants: standard, featured, compact, list
│     ├── ProductImageGallery      — vertical-thumb + horizontal-thumb variants
│     ├── PriceBlock               — sale + original + discount badge
│     ├── QuantitySelector         — shared with ui/
│     ├── AddToCartButton          — full-width + icon variants
│     ├── WishlistButton           — overlay + standalone variants
│     ├── StockBadge               — In/Low/Out of stock
│     ├── DiscountBadge            — Sale/New/Hot/Limited overlays
│     ├── ProductTabs              — Description, Specs, Reviews
│     ├── RelatedProductsCarousel  — 4-up grid with Quick View
│     └── RecentlyViewed           — grayscale horizontal scroll strip
│
├── SECTION MODULES [sections/]
│     ├── HeroBanner               — split (12-col) + fullwidth variants
│     ├── CategoryGrid             — 2/4/6 col, image card + label
│     ├── FeaturedProducts         — tabbed + carousel nav + 4-col grid
│     ├── PromoBanners             — 1/2-col image overlay banners
│     ├── BenefitsBar              — 4-col icon + text trust signals
│     ├── NewsletterSignup         — inline + card variants
│     ├── BestSellersCarousel      — horizontal product scroll
│     ├── BlogPreview              — aspect-video card grid
│     ├── BrandLogos               — grayscale logo grid/scroll
│     └── UpsellGrid               — mini product grid (cart upsell)
│
└── PAGE TEMPLATES [templates/]
      ├── HomePage                 — AnnouncementBar + full section stack
      ├── CategoryListingPage      — Sidebar filters + product grid + pagination
      ├── ProductDetailPage        — Gallery + info panel + tabs + related
      ├── CartPage                 — Items table + order summary + upsell
      ├── CheckoutPage             — Multi-step form + summary sidebar
      ├── AccountPages             — Login, Register, Dashboard, Orders
      └── InformationPage          — Rich text content, breadcrumb
```

---

## Cross-Variant Design System Constraints

These rules must be enforced across all NovaKur child themes (Electronics, Fashion, Living):

| Rule | Value |
|---|---|
| **Primary color token** | CSS custom property `--color-primary` (overridable per variant) |
| **Font family token** | `--font-display` (Inter as base, swappable) |
| **Border radius scale** | Fixed 4px / 8px / 12px / 16px / 9999px — do not break |
| **Dark mode** | All components implement `dark:` class variants |
| **Grid system** | All pages use `max-w-7xl` container |
| **Hover patterns** | `scale-105/110` on images, `shadow-2xl` on cards, `text-primary` on links |
| **Motion** | `transition-all duration-300/500` — no abrupt jumps |
| **Icon system** | Material Symbols Outlined only — no SVG icon sprawl |
| **Badge color logic** | Sale = red-500, New = primary, Hot = indigo-600, Stock = green/amber/slate |

---

## Implementation File Structure

```
catalog/view/theme/novakur_base/
├── template/
│   ├── layout/
│   │   ├── announcement_bar.twig
│   │   ├── header_desktop.twig
│   │   ├── mega_menu.twig
│   │   ├── mobile_nav_drawer.twig
│   │   ├── footer.twig
│   │   └── breadcrumb.twig
│   ├── ui/
│   │   ├── button.twig
│   │   ├── badge.twig
│   │   ├── rating_stars.twig
│   │   ├── pagination.twig
│   │   └── quantity_selector.twig
│   ├── product/
│   │   ├── card/
│   │   │   ├── standard.twig
│   │   │   ├── featured.twig
│   │   │   └── compact.twig
│   │   ├── image_gallery.twig
│   │   ├── price_block.twig
│   │   └── product_tabs.twig
│   ├── sections/
│   │   ├── hero_banner.twig
│   │   ├── category_grid.twig
│   │   ├── featured_products.twig
│   │   ├── promo_banners.twig
│   │   ├── benefits_bar.twig
│   │   └── newsletter_signup.twig
│   └── pages/
│       ├── home.twig
│       ├── category.twig
│       ├── product.twig
│       ├── cart.twig
│       └── information.twig
├── stylesheet/
│   ├── tokens/
│   │   ├── _colors.scss
│   │   ├── _typography.scss
│   │   └── _spacing.scss
│   ├── components/
│   │   ├── _buttons.scss
│   │   ├── _badges.scss
│   │   ├── _cards.scss
│   │   ├── _header.scss
│   │   ├── _footer.scss
│   │   └── _product.scss
│   └── novakur.scss
└── javascript/
    ├── modules/
    │   ├── mega-menu.js
    │   ├── mobile-drawer.js
    │   ├── product-gallery.js
    │   ├── tab-switcher.js
    │   ├── quantity-selector.js
    │   └── cart-actions.js
    └── novakur.js
```
