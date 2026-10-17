# Live update to v1.6.1: full stack repositioning + smooth wave hero

Source of truth: branch `playful-scroll`, commit in the go message (latest on the branch). New copy is in `inc/starter-content.php`,
`inc/options.php` and `tools/build-patterns.py`; copy it from there exactly.

## 0. Back up first
- Live theme files listed in step 1, page content of Home, About, Cost estimator, Contact,
  all `jd_service`, `jd_plan`, `jd_faq` posts with meta, and the theme options row.
  Save to `wp-content/jd-theme-backups/<timestamp>-pre-1.6.1/`.

## 1. Theme files (write from 48ec823, check md5 after)
assets/js/main.js, blocks/contact-form/render.php, blocks/marquee/block.json, inc/blocks.php,
inc/options.php, inc/seo.php, inc/starter-content.php, patterns/page-about.php, patterns/pricing.php,
patterns/proof.php, patterns/services-stack.php, templates/home.html, tools/build-patterns.py,
style.css, functions.php

## 2. Theme options (saved values override the new defaults)
Set to the new defaults in inc/options.php: job_title, footer_blurb, home_title, home_desc,
default_desc, work_desc, services_desc. Change the hero badge that reads "12+ years on WordPress"
(wherever it is stored) to "12+ years full stack".
Also: if the site tagline mentions WordPress, set it to "Full stack web developer".
If a template override of templates/home.html exists in the DB (wp_template), update its lead too.

## 3. Services (`jd_service`) — match the `$services` array in starter-content.php
- "Business websites" -> title "Websites", slug "websites", new excerpt, features, content.
- "WooCommerce stores" -> title "Online stores", slug "online-stores", new excerpt, features, price "$3,000–6,000", from 3000, content.
- New "Web apps and AI" (slug web-apps-and-ai) as menu_order 2 with all meta; shift Speed and fixes to 3, Care plans to 4.
- Care plans: new content paragraph. Use wp_update_post so old slugs redirect.

## 4. Pricing plans (`jd_plan`) — match the `$plans` array
- Update Landing page, Business website; rename "WooCommerce store" -> "Online store" with new price/desc/features.
- Add Webflow site, AI and automation, Custom web app (group project, CTA url /contact/), menu_order 3–5; care plans 6–8.
- Care "Store": desc "For Shopify and WooCommerce stores."

## 5. FAQs (`jd_faq`)
- Update the Figma answer.
- Add "Which platform should I build on?" to the general group AND the home group.
- Read every live FAQ (home group too) and replace any WordPress-only wording with platform-neutral
  wording in the same voice. Report each change.

## 6. Pages (DB keeps its own copy of the sections)
- Home: proof label "years building on WordPress" -> "years building for the web";
  "sites built, fixed or sped up" -> "sites, stores and apps shipped";
  services heading "Four jobs.<br>Done properly." -> "One developer.<br>Every layer.";
  pricing lead -> the new text in patterns/pricing.php; if the jd/marquee block has an items attribute,
  set it to the new default in blocks/marquee/block.json. Leave the hero as it is.
- About: replace the intro paragraph with the one in patterns/page-about.php; update _jd_seo_desc.
- Cost estimator and Contact: update _jd_seo_desc to the new values.
- Search Home/About/Contact/Estimate content for any other "WordPress developer" phrasing and report it.

## 7. Purge LiteSpeed and verify at 1440px and 390px
- Hero shows the flowing wave lines (no code rain), and they bulge smoothly around the cursor with no kinks, easing in and out. No JS errors.
- Pricing shows 6 project cards; each "estimate" link opens the popup with the right type preselected.
- Estimator lists Shopify or WooCommerce store, Webflow website, Custom web app, AI feature or automation.
- Services, FAQ, About and page <title>/meta description read full stack, no freelancing platform names.
