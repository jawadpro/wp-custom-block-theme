# Wendy Paquette: one feature image, like the other projects

Jawad wants this project to look like every other project on /work/: one feature image, no screenshot gallery. Visitors who want to see more can use the live site link.

## 1. Back up first
Export the `wendy-paquette` jd_project post (content, meta, featured image ID, attached media IDs) to `wp-content/jd-theme-backups/<timestamp>-pre-wendy-one-image/`.

## 2. Match the other projects' feature images
- Look at the featured images of the other 8 projects (Resumedone, Devnetix, Botlya, cardLt, Health Hub, Ugaie, Uxtorch, Revenue Accelerator): pixel size, aspect ratio, crop (top of page or full page), any frame, background or padding, and file format.
- Make ONE image of wendypaquette.com in exactly that style, size and format (for example the desktop hero cropped the same way). Give it descriptive alt text.
- Set it as the project's featured image.

## 3. Remove the screenshots
- Remove the screenshot gallery, the phone shots, the full-page scroll frame and their captions from the post content.
- Keep the intro text, the problem and what-I-did fields, the facts and the live site button.
- Detach the old wendypaquette-*.webp images from the post so the demos page shows only the new one. Leave them in the Media Library and don't delete anything.

## 4. Check (desktop 1440 and phone 390), then purge the LiteSpeed cache
- /work/ and the homepage work track: the Wendy card looks like the other cards.
- /work/wendy-paquette/: no gallery, no layout gaps and no JS errors.
- /demos/wendy-paquette/: Screenshots mode shows the single image.
