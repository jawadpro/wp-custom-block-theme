# JD Case Demos

A standalone WordPress plugin for jawadjd.dev that adds interactive case study demos at `/demos/`, kept completely separate from the Jawad theme.

- `/demos/` opens the first project; `/demos/{project-slug}/` opens a specific one.
- Each project can be previewed as the **live site** in desktop, tablet and phone frames (rendered at the real device width and scaled to fit), or as **screenshots** that scroll through slowly.
- Data comes from the theme's Projects (`jd_project`): title, client, scope, excerpt, problem, "what I did", results, live URL, featured image and images in the case study content. Projects without a live URL or screenshots are skipped.
- The viewer is its own lightweight page (no theme CSS or JS), marked `noindex`.
- Shortcode for linking from anywhere: `[jd_demo_link slug="wendy-paquette" label="Try the interactive demo"]`.
- Deactivate the plugin to remove everything; nothing is stored in the database.

Some sites block being shown inside another page (X-Frame-Options / CSP frame-ancestors). For those, use the Screenshots view.

Install: zip the `jd-case-demos` folder, upload it under Plugins › Add New › Upload, activate, then visit Settings › Permalinks once (or the plugin flushes rules on activation).
