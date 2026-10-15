<?php
/**
 * Starter content importer. Creates pages, the main menu and example entries for every post type.
 * Idempotent: anything with the same title already present is skipped.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_post_jd_import_starter', function () {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'jawad' ) );
	}
	check_admin_referer( 'jd_import_starter' );
	$log = jd_import_starter();
	set_transient( 'jd_import_log', $log, 60 );
	wp_safe_redirect( admin_url( 'themes.php?page=jd-options&imported=1' ) );
	exit;
} );

add_action( 'admin_notices', function () {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$log = get_transient( 'jd_import_log' );
	if ( $log ) {
		delete_transient( 'jd_import_log' );
		printf( '<div class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html( sprintf( /* translators: %d items */ __( 'Starter content imported: %d items created.', 'jawad' ), count( $log ) ) ) );
		return;
	}
	if ( ! get_option( 'jd_starter_done' ) && 'appearance_page_jd-options' !== get_current_screen()->id ) {
		printf(
			'<div class="notice notice-info"><p>%1$s <a href="%2$s">%3$s</a></p></div>',
			esc_html__( 'The Jawad theme is active. Import the starter pages, menu and example content to see the full design.', 'jawad' ),
			esc_url( admin_url( 'themes.php?page=jd-options' ) ),
			esc_html__( 'Go to Theme Options', 'jawad' )
		);
	}
} );

/**
 * Expand a registered pattern into plain block markup, following nested pattern references.
 */
function jd_pattern_markup( $slug, $depth = 0 ) {
	$registry = WP_Block_Patterns_Registry::get_instance();
	$pattern  = $registry->get_registered( $slug );
	if ( ! $pattern || $depth > 3 ) {
		return '';
	}
	$content = $pattern['content'];
	return preg_replace_callback(
		'/<!-- wp:pattern \{"slug":"([^"]+)"\} \/-->/',
		function ( $m ) use ( $depth ) {
			return jd_pattern_markup( $m[1], $depth + 1 );
		},
		$content
	);
}

/**
 * Find a post of a type by exact title.
 */
function jd_find_by_title( $title, $type ) {
	$found = get_posts(
		array(
			'post_type'      => $type,
			'title'          => $title,
			'post_status'    => array( 'publish', 'draft', 'private', 'pending', 'future' ),
			'posts_per_page' => 1,
			'no_found_rows'  => true,
		)
	);
	return $found ? $found[0] : null;
}

/**
 * Insert a post unless one with the same title exists. Returns the ID either way.
 */
function jd_seed_post( $args, $meta = array(), &$log = array() ) {
	$existing = jd_find_by_title( $args['post_title'], $args['post_type'] );
	if ( $existing ) {
		return $existing->ID;
	}
	$id = wp_insert_post( wp_slash( array_merge( array( 'post_status' => 'publish' ), $args ) ), true );
	if ( is_wp_error( $id ) ) {
		return 0;
	}
	foreach ( $meta as $key => $value ) {
		update_post_meta( $id, $key, $value );
	}
	$log[] = $args['post_type'] . ': ' . $args['post_title'];
	return $id;
}

/**
 * Run the import.
 */
function jd_import_starter() {
	$log = array();

	/* Pages ------------------------------------------------------------- */
	$home    = jd_seed_post( array( 'post_type' => 'page', 'post_title' => 'Home', 'post_name' => 'home', 'post_content' => jd_pattern_markup( 'jawad/page-home' ), 'menu_order' => 0 ), array(), $log );
	$about   = jd_seed_post( array( 'post_type' => 'page', 'post_title' => 'About', 'post_name' => 'about', 'post_content' => jd_pattern_markup( 'jawad/page-about' ), 'page_template' => 'page-blank', 'menu_order' => 3 ), array( '_jd_seo_desc' => 'Jawad Ilyas is a WordPress developer with more than ten years of experience building fast, conversion-focused sites for small businesses.' ), $log );
	$contact = jd_seed_post( array( 'post_type' => 'page', 'post_title' => 'Contact', 'post_name' => 'contact', 'post_content' => jd_pattern_markup( 'jawad/page-contact' ), 'page_template' => 'page-blank', 'menu_order' => 5 ), array( '_jd_seo_desc' => 'Tell me about your WordPress project. You get a reply within 24 hours and a written fixed quote after a short call.' ), $log );
	$journal = jd_seed_post( array( 'post_type' => 'page', 'post_title' => 'Journal', 'post_name' => 'journal', 'post_content' => '', 'menu_order' => 4 ), array(), $log );

	if ( $home ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home );
	}
	if ( $journal ) {
		update_option( 'page_for_posts', $journal );
	}

	/* Navigation -------------------------------------------------------- */
	if ( ! jd_find_by_title( 'Main menu', 'wp_navigation' ) ) {
		$links = array(
			array( 'Work', home_url( '/work/' ) ),
			array( 'Services', home_url( '/services/' ) ),
			array( 'About', $about ? get_permalink( $about ) : home_url( '/about/' ) ),
			array( 'Journal', $journal ? get_permalink( $journal ) : home_url( '/journal/' ) ),
			array( 'Contact', $contact ? get_permalink( $contact ) : home_url( '/contact/' ) ),
		);
		$markup = '';
		foreach ( $links as $link ) {
			$markup .= '<!-- wp:navigation-link ' . wp_json_encode( array( 'label' => $link[0], 'url' => $link[1], 'kind' => 'custom', 'isTopLevelLink' => true ), JSON_UNESCAPED_SLASHES ) . ' /-->' . "\n";
		}
		jd_seed_post( array( 'post_type' => 'wp_navigation', 'post_title' => 'Main menu', 'post_content' => $markup ), array(), $log );
	}

	/* Services ------------------------------------------------------------ */
	$services = array(
		array(
			'Business websites', 'business-websites', 'A site that sells while you sleep',
			'For consultants, clinics, trades and agencies. Designed around one goal: getting the right people to call or book.',
			"Custom design, or built from your Figma file\nElementor or block editor, so your team can edit it\nContact and booking forms wired to email or your CRM\nOn-page SEO, Google Analytics and Search Console\n90+ mobile PageSpeed target, tested before launch\nRecorded walkthrough so you can update it yourself",
			'$1,500–3,500', 1500, '2–4 weeks',
			"<!-- wp:paragraph -->\n<p>A business website has one job: turn the people who find you into enquiries. I design and build WordPress sites that load fast, explain your offer in seconds, and make it obvious what to do next.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>You approve the homepage design before any code is written, then watch the site come together on a private staging link. At launch you get a short training video so you can change text, images and pages yourself.</p>\n<!-- /wp:paragraph -->",
		),
		array(
			'WooCommerce stores', 'woocommerce-stores', 'A checkout people finish',
			'New stores, or rescuing one that leaks sales. Clean product pages and a fast, trustworthy mobile checkout.',
			"Product, category, cart and checkout templates\nStripe, PayPal, Apple Pay and Google Pay\nUS sales tax and Canadian GST/HST setup\nShipping zones, rates and label plugins\nOrder emails, abandoned-cart and review flows\nPlugin conflicts and checkout bugs traced to the cause",
			'$3,500–6,000', 3500, '3–6 weeks',
			"<!-- wp:paragraph -->\n<p>Most WooCommerce stores lose sales in the last two steps. I build and repair stores so product pages answer the buyer's questions and checkout is quick on a phone.</p>\n<!-- /wp:paragraph -->",
		),
		array(
			'Speed and fixes', 'speed-and-fixes', 'Fixed at the root, not patched',
			'White screens, broken layouts, hacked sites, failing Core Web Vitals. You get a before and after report.',
			"Bug fixes from \$150, speed rebuilds from \$450\nImage compression, WebP and lazy loading\nCaching, CDN and database cleanup\nUnused plugin and script removal\nMalware cleanup and security hardening\nBefore and after report you can share",
			'from $150', 150, '2–7 days',
			"<!-- wp:paragraph -->\n<p>Slow pages cost rankings and enquiries. I find what is actually slowing your site down, fix it properly, and show you the before and after scores.</p>\n<!-- /wp:paragraph -->",
		),
		array(
			'Care plans', 'care-plans', 'After launch, I stay on call',
			'Monthly updates, backups and small edits, so your site never goes stale or breaks quietly.',
			"Weekly updates and offsite backups\nUptime and security monitoring\nEdit hours every month\nMonthly health report in plain English",
			'from $79/mo', 79, 'Monthly',
			"<!-- wp:paragraph -->\n<p>A website is never finished. Care plans keep WordPress, plugins and backups up to date, watch for downtime and attacks, and give you hours each month for small changes.</p>\n<!-- /wp:paragraph -->",
		),
	);
	foreach ( $services as $i => $s ) {
		jd_seed_post(
			array( 'post_type' => 'jd_service', 'post_title' => $s[0], 'post_name' => $s[1], 'post_excerpt' => $s[3], 'post_content' => $s[8], 'menu_order' => $i ),
			array( '_jd_card_title' => $s[2], '_jd_features' => $s[4], '_jd_price' => $s[5], '_jd_price_from' => $s[6], '_jd_timeline' => $s[7], '_jd_cta_label' => 'Get a fixed quote' ),
			$log
		);
	}

	/* Pricing plans ------------------------------------------------------- */
	$contact_url = $contact ? get_permalink( $contact ) : home_url( '/contact/' );
	$plans       = array(
		array( 'Landing page', 'project', '$500', '–1.5k', 'One focused page for an offer, ad campaign or launch.', "Custom layout\nForm or booking setup\n5 to 10 days", 0, '', 'Start a landing page' ),
		array( 'Business website', 'project', '$1.5k', '–3.5k', 'Homepage plus inner pages, forms, SEO basics and launch support.', "5 to 10 pages\nEditable by your team\n2 to 4 weeks", 1, 'Most booked', 'Start my website' ),
		array( 'WooCommerce store', 'project', '$3.5k', '–6k', 'Products, checkout, payments and shipping, fast on mobile.', "Payments and tax\nSpeed tuned\n3 to 6 weeks", 0, '', 'Start my store' ),
		array( 'Keep', 'care', '$79', '/mo', 'Updates, backups and monitoring.', "Weekly updates and backups\nUptime and security monitoring\nMonthly report", 0, '', '' ),
		array( 'Grow', 'care', '$149', '/mo', 'For sites that change often.', "Everything in Keep\n2 edit hours a month\nSpeed checks and fixes", 1, 'Most chosen', '' ),
		array( 'Store', 'care', '$249', '/mo', 'For WooCommerce stores.', "Everything in Grow\nCheckout and payment checks\n4 edit hours a month", 0, '', '' ),
	);
	foreach ( $plans as $i => $p ) {
		$meta = array( '_jd_group' => $p[1], '_jd_price_main' => $p[2], '_jd_price_sub' => $p[3], '_jd_desc' => $p[4], '_jd_features' => $p[5] );
		if ( $p[6] ) {
			$meta['_jd_featured'] = true;
		}
		if ( $p[7] ) {
			$meta['_jd_badge'] = $p[7];
		}
		if ( $p[8] ) {
			$meta['_jd_cta_label'] = $p[8];
			$meta['_jd_cta_url']   = $contact_url;
		}
		jd_seed_post( array( 'post_type' => 'jd_plan', 'post_title' => $p[0], 'menu_order' => $i ), $meta, $log );
	}

	/* Testimonials (from the current jawadjd.dev site) -------------------- */
	$reviews = array(
		array( 'Amina Yusuf', 'Ongoing WordPress support', 'He finds the source of the problem and fixes it properly.', 'Jawad has helped us with complex WordPress fixes, custom functionality, responsive issues, and ongoing website improvements. He does more than apply temporary fixes. He finds the source of the problem and provides a reliable solution. He is responsive, technically skilled, and dependable.' ),
		array( 'Sarah Mitchell', 'Business website', 'Fast, responsive, and easy for our team to manage.', 'Jawad built us a modern, professional WordPress website that is fast, responsive, and easy for our team to manage. He handled everything from the page structure and Elementor development to mobile optimization and launch. Communication was clear throughout, and the finished website represents our business perfectly.' ),
		array( 'David Kramer', 'WooCommerce store', 'Checkout finally works smoothly on every device.', 'Our WooCommerce website had performance problems, plugin conflicts, and checkout issues that were affecting the customer experience. Jawad carefully diagnosed each problem, improved the website speed, and made the shopping and checkout process much smoother across desktop and mobile.' ),
	);
	foreach ( $reviews as $i => $r ) {
		jd_seed_post(
			array( 'post_type' => 'jd_testimonial', 'post_title' => $r[0], 'post_content' => "<!-- wp:paragraph -->\n<p>" . $r[3] . "</p>\n<!-- /wp:paragraph -->", 'menu_order' => $i ),
			array( '_jd_role' => $r[1], '_jd_quote_short' => $r[2] ),
			$log
		);
	}

	/* FAQs -------------------------------------------------------------- */
	$group = term_exists( 'general', 'jd_faq_group' );
	if ( ! $group ) {
		$group = wp_insert_term( 'General', 'jd_faq_group', array( 'slug' => 'general' ) );
	}
	$faqs = array(
		array( 'How do time zones work if you are in Pakistan?', 'I work 7pm to midnight my time, which is 9am to 2pm Eastern and 6 to 11am Pacific. You get replies during your working day and progress overnight.' ),
		array( 'Will I be able to edit the site myself?', 'Yes. Every site ships with a short recorded walkthrough of how to change text, images and pages without breaking the layout.' ),
		array( 'Who owns the website?', 'You do. Hosting, domain, logins and files are in your name from day one.' ),
		array( 'What if something breaks after launch?', 'Anything I built that breaks in the first 30 days is fixed free. After that, a care plan or a small fixed-price fix.' ),
		array( "Can you work from my designer's Figma file?", 'Yes. I build Figma designs accurately in Elementor or as a lightweight custom theme, whichever suits how your team will edit it.' ),
	);
	foreach ( $faqs as $i => $f ) {
		$id = jd_seed_post( array( 'post_type' => 'jd_faq', 'post_title' => $f[0], 'post_content' => "<!-- wp:paragraph -->\n<p>" . $f[1] . "</p>\n<!-- /wp:paragraph -->", 'menu_order' => $i ), array(), $log );
		if ( $id && ! is_wp_error( $group ) ) {
			wp_set_object_terms( $id, (int) ( is_array( $group ) ? $group['term_id'] : $group ), 'jd_faq_group' );
		}
	}

	/* Projects (from the current jawadjd.dev portfolio) ------------------- */
	$types = array();
	foreach ( array( 'Website' => 'website', 'Landing page' => 'landing-page', 'Store' => 'store', 'Speed and fixes' => 'speed-and-fixes' ) as $name => $slug ) {
		$term = term_exists( $slug, 'jd_project_type' );
		if ( ! $term ) {
			$term = wp_insert_term( $name, 'jd_project_type', array( 'slug' => $slug ) );
		}
		if ( ! is_wp_error( $term ) ) {
			$types[ $slug ] = (int) ( is_array( $term ) ? $term['term_id'] : $term );
		}
	}
	$projects = array(
		array( 'Resumedone', 'landing-page', 'AI resume builder landing page built around reviews and outcome stats.', '#3D5AFE', '#1A1D2B', '#3D5AFE', 0, 0,
			'Resume builders compete on identical promises. The page had to lead with measurable outcomes and a large body of reviews, while keeping one unmistakable path into the builder.',
			"An outcome-led hero with the success-rate stat and social proof up front.\nA template gallery as proof, so visitors start from evidence rather than a blank page.\nA curated review wall with names, dates and ratings.\nEvery section resolves to the same builder entry point." ),
		array( 'Devnetix', 'website', 'Results-driven software agency website covering UI/UX, Next.js, AI and WordPress.', '#0F3B44', '#E6F2F0', '#3FD0B6', 1, 1, '', '' ),
		array( 'Revenue Accelerator', 'landing-page', 'Conversion-focused landing page for a done-for-you sales system agency.', '#E8E2D4', '#1C1B18', '#5B4BFF', 0, 0, '', '' ),
		array( 'Uxtorch', 'website', 'Editorial, portfolio-forward website for a UX agency serving early-stage startups.', '#FFB4A2', '#2A1410', '#E0482C', 0, 2, '', '' ),
		array( 'Ugaie', 'landing-page', 'Data-rich SaaS landing page for an integrated risk management platform.', '#13263F', '#CFE6FF', '#5AB4FF', 1, 1, '', '' ),
		array( 'cardLt', 'landing-page', 'Fintech landing page for a virtual card app, built around bold stats and a frictionless sign-up story.', '#C9D4FF', '#1B2A6B', '#3D5AFE', 0, 2, '', '' ),
		array( 'Health Hub', 'landing-page', 'Medical app landing page showing appointments, doctor chat and health tracking.', '#D7E8C6', '#1C2E14', '#3E8E2F', 0, 3, '', '' ),
		array( 'Botlya', 'landing-page', 'Futuristic landing page for an AI chat app with immersive phone mockups.', '#1A2547', '#F2F4FA', '#5CF2C8', 1, 3, '', '' ),
	);
	foreach ( $projects as $i => $p ) {
		$meta = array(
			'_jd_card_bg'      => $p[3],
			'_jd_card_ink'     => $p[4],
			'_jd_card_accent'  => $p[5],
			'_jd_card_variant' => (string) $p[7],
			'_jd_scope'        => 'website' === $p[1] ? 'Full website' : 'Landing page',
		);
		if ( $p[6] ) {
			$meta['_jd_card_dark'] = true;
		}
		if ( $i < 6 ) {
			$meta['_jd_featured'] = true;
		}
		if ( $p[8] ) {
			$meta['_jd_problem'] = $p[8];
		}
		if ( $p[9] ) {
			$meta['_jd_solution'] = $p[9];
		}
		$id = jd_seed_post(
			array( 'post_type' => 'jd_project', 'post_title' => $p[0], 'post_excerpt' => $p[2], 'post_content' => '', 'menu_order' => $i ),
			$meta,
			$log
		);
		if ( $id && isset( $types[ $p[1] ] ) ) {
			wp_set_object_terms( $id, array( $types[ $p[1] ] ), 'jd_project_type' );
		}
	}

	update_option( 'jd_starter_done', 1 );
	flush_rewrite_rules( false );
	return $log;
}
