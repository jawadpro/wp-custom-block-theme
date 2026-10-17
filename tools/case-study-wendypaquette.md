# Case study: wendypaquette.com (custom WordPress block theme)

Goal: present this as the strongest project on jawadjd.dev, with real screenshots and a live link.
Use only facts you can see or measure on the live site. Never invent client results or quotes.

## 0. Back up
Export all `jd_project` posts with meta, plus the Home page, to wp-content/jd-theme-backups/<timestamp>-pre-wendypaquette/.

## 1. Screenshots (from https://wendypaquette.com/ in the browser)
- Desktop 1440px: the hero (above the fold), plus 2 or 3 of the most distinctive sections further down.
- Mobile 390px: the hero and one inner section.
- One full-page desktop capture for the gallery.
- Save as WebP (quality ~82, max 2000px wide), named wendypaquette-*.webp, with alt text that describes each view.
- Upload them to the Media Library.

## 2. Project post (`jd_project`, published)
- Title: "Wendy Paquette". Slug: wendy-paquette. Project type term: Website.
- Featured image: the desktop hero screenshot.
- Meta:
  - _jd_client: Wendy Paquette
  - _jd_scope: Custom block theme · WordPress
  - _jd_tag: Client · Live
  - _jd_live_url: https://wendypaquette.com/
  - _jd_featured: 1
  - _jd_problem / _jd_solution: 2–3 plain sentences each, written from what the site actually is and does (the audience, the pages, the editing experience), in the same voice as the rest of the site. Mention that it's a custom block theme the client can edit in the block editor without breaking the design.
  - _jd_result_1 / _jd_result_2: measured facts only, e.g. the page weight in KB, the number of requests, or a measured mobile load time. Leave them empty if you can't measure them reliably.
  - _jd_timeline, _jd_quote: leave empty unless Jawad provides them.
  - Card colours: pick _jd_card_bg / _jd_card_accent from the site's own palette.
- Content: a short intro paragraph, then the screenshots as image blocks (desktop views full width, the two mobile views side by side in columns), each with a one-line caption saying what the section does, then a "Visit the live site" button linking to the URL (new tab).
- Order: put it first in the project list (lowest menu_order), so it leads the homepage work track and /work/.

## 3. Check (desktop 1440 and phone 390)
- The homepage work track shows it first with the screenshot.
- /work/ lists it.
- The single project page shows the problem/solution cards, results (if any), gallery and live link, with no layout breaks and no JS errors.
- Purge the LiteSpeed cache afterwards.
