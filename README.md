# Jawad: WordPress block theme

A fast, SEO-ready full-site-editing theme for a WordPress developer studio: midnight navy, cobalt and mint, condensed display type, scroll motion, and content managed through custom post types.

- No page builder and no build step. Install the folder and activate.
- No third-party requests. Fonts and scripts are self-hosted.
- Readable without JavaScript. Motion is an enhancement and switches off for visitors who prefer reduced motion.

## Install

1. Copy this folder to `wp-content/themes/jawad` (or upload it as a zip under Appearance › Themes › Add New).
2. Activate **Jawad**.
3. Go to **Appearance › Theme Options** and click **Import starter content**. This creates Home, About, Contact and Journal pages, the main menu, and example services, pricing plans, projects, testimonials and FAQs. It skips anything that already exists, so running it twice is safe.
4. Visit **Settings › Permalinks** once if `/work/` or `/services/` return a 404.

## Where things are edited

| What | Where |
| --- | --- |
| Home, About, Contact copy | Pages (block editor). Sections are normal blocks and theme patterns. |
| Header, footer | Appearance › Editor › Patterns › Template parts |
| Menu (desktop + overlay) | Appearance › Editor › Navigation |
| Work / case studies | **Work** in the dashboard. Fields: client, scope, timeline, live URL, problem, solution, two results, quote, card colors |
| Services and their pages | **Services**. Fields: card headline, price, timeline, inclusions, before/after scores |
| Pricing plans | **Pricing**. Group: project packages or care plans |
| Reviews | **Testimonials**. Title = client name, short pull quote, role, rating, link to the original review |
| FAQs | **FAQs**, with optional FAQ groups |
| Form submissions | **Leads** (also emailed to you) |
| Brand, badges, contact details, social links, audit offer, motion switches, SEO defaults, Search Console, GA4 | **Appearance › Theme Options** |
| Per-page SEO title, description, noindex | "SEO" box under the editor |

## Theme blocks (category "Jawad")

Brand logo, Menu toggle, Hero badges, Speed ring, Marquee, Services (stack or rows), Projects (pinned track or filterable grid), Testimonials, Pricing plans, FAQ, Contact form, Contact details, Time zone clocks, Project facts, Case study body, Service details, Footer columns, Footer wordmark.

All are server-rendered (`blocks/<name>/render.php`), so content stays in the HTML for search engines.

## Performance

- Self-hosted variable fonts (Archivo, Instrument Sans, JetBrains Mono), two of them preloaded.
- Scripts load with `defer` in the footer. GSAP, ScrollTrigger and Lenis load only when animations are on (Theme Options › Motion & speed).
- Emoji scripts, generator and RSD/WLW tags, classic-theme CSS and the remote pattern directory are removed.
- Only the CSS of blocks on the page is loaded.
- Page transitions use the browser's View Transitions API (CSS only).

## SEO

When no SEO plugin is active the theme outputs titles, meta descriptions, canonical URLs for archives, Open Graph and Twitter tags, and one JSON-LD graph (WebSite, ProfessionalService or Person, WebPage, BreadcrumbList, plus BlogPosting, Service or CreativeWork on singles and FAQPage wherever the FAQ block is used). Search and 404 pages are `noindex`. The users sitemap is removed and noindexed posts are left out of the sitemap. If Yoast, Rank Math, SEOPress, AIOSEO or The SEO Framework is active, the theme's tags switch off automatically.

## Development

- Patterns are generated from `tools/build-patterns.py` so block markup stays valid and consistent: `python3 tools/build-patterns.py`.
- Block markup in templates, parts and patterns was validated against `@wordpress/blocks` 16.

## Licenses

- Theme code: GPLv2 or later.
- Archivo, Instrument Sans, JetBrains Mono: SIL Open Font License 1.1.
- Lenis: MIT.
- GSAP 3.12.5: GreenSock Standard "No Charge" license, free to use on this site. It is not GPL, so swap it out before submitting the theme to WordPress.org.
