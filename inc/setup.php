<?php
/**
 * Theme supports, assets and editor integration.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', function () {
	load_theme_textdomain( 'jawad', JD_DIR . '/languages' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_editor_style( array( 'assets/css/main.css', 'assets/css/editor.css' ) );
	add_image_size( 'jd-card', 1200, 675, true );
	add_image_size( 'jd-wide', 1800, 1012, true );
} );

/**
 * Whether the animation layer should load for this request.
 */
function jd_motion_enabled() {
	return (bool) jd_opt( 'motion_on' ) && ! is_admin();
}

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'jd-main', JD_URI . '/assets/css/main.css', array(), JD_VERSION );

	if ( jd_opt( 'page_transitions' ) ) {
		wp_add_inline_style( 'jd-main', '@view-transition{navigation:auto}' );
	}

	$deps = array();
	if ( jd_motion_enabled() ) {
		wp_register_script( 'jd-gsap', JD_URI . '/assets/vendor/gsap.min.js', array(), '3.12.5', array( 'in_footer' => true, 'strategy' => 'defer' ) );
		wp_register_script( 'jd-scrolltrigger', JD_URI . '/assets/vendor/ScrollTrigger.min.js', array( 'jd-gsap' ), '3.12.5', array( 'in_footer' => true, 'strategy' => 'defer' ) );
		$deps = array( 'jd-gsap', 'jd-scrolltrigger' );
		if ( jd_opt( 'smooth_scroll' ) ) {
			wp_register_script( 'jd-lenis', JD_URI . '/assets/vendor/lenis.min.js', array(), '1.1.13', array( 'in_footer' => true, 'strategy' => 'defer' ) );
			$deps[] = 'jd-lenis';
		}
	}

	wp_enqueue_script( 'jd-main', JD_URI . '/assets/js/main.js', $deps, JD_VERSION, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_localize_script(
		'jd-main',
		'JD',
		array(
			'motion'   => jd_motion_enabled(),
			'smooth'   => (bool) jd_opt( 'smooth_scroll' ),
			'cursor'   => (bool) jd_opt( 'custom_cursor' ),
			'field'    => (bool) jd_opt( 'hero_field' ),
			'ajax'     => admin_url( 'admin-post.php' ),
			'i18n'     => array(
				'send'     => __( 'Send request', 'jawad' ),
				'next'     => __( 'Continue', 'jawad' ),
				'sending'  => __( 'Sending…', 'jawad' ),
				'copied'   => __( 'Copied', 'jawad' ),
				'selected' => __( 'Selected', 'jawad' ),
				'copy'     => __( 'Copy', 'jawad' ),
				'online'   => __( 'Online now', 'jawad' ),
				'offline'  => __( 'Offline, replies next working day', 'jawad' ),
				'error'    => __( 'Something went wrong. Please email me directly instead.', 'jawad' ),
			),
			'liveTz'    => (string) jd_opt( 'live_tz' ),
			'liveStart' => (int) jd_opt( 'live_start' ),
			'liveEnd'   => (int) jd_opt( 'live_end' ),
		)
	);
} );

/**
 * Preload the two text faces that render above the fold.
 */
add_action( 'wp_head', function () {
	foreach ( array( 'archivo-wdth.woff2', 'instrument-sans-wdth.woff2' ) as $font ) {
		printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( JD_URI . '/assets/fonts/' . $font ) );
	}
}, 1 );

/**
 * Mark the document as JS-capable before paint, so reveal states never hide content without JS.
 */
add_action( 'wp_head', function () {
	echo "<script>document.documentElement.classList.add('jd-js');</script>\n";
}, 0 );

add_filter( 'body_class', function ( $classes ) {
	if ( jd_motion_enabled() ) {
		$classes[] = 'jd-motion';
	}
	return $classes;
} );

/**
 * Pattern categories.
 */
add_action( 'init', function () {
	register_block_pattern_category( 'jawad-sections', array( 'label' => __( 'Jawad: sections', 'jawad' ) ) );
	register_block_pattern_category( 'jawad-pages', array( 'label' => __( 'Jawad: full pages', 'jawad' ) ) );

	register_block_style( 'core/button', array( 'name' => 'ghost', 'label' => __( 'Ghost', 'jawad' ) ) );
	register_block_style( 'core/group', array( 'name' => 'panel', 'label' => __( 'Panel', 'jawad' ) ) );
	register_block_style( 'core/group', array( 'name' => 'cobalt-panel', 'label' => __( 'Cobalt panel', 'jawad' ) ) );
	register_block_style( 'core/paragraph', array( 'name' => 'label', 'label' => __( 'Mono label', 'jawad' ) ) );
	register_block_style( 'core/heading', array( 'name' => 'condensed', 'label' => __( 'Condensed display', 'jawad' ) ) );
} );

/**
 * Remove core's remote pattern directory: faster editor, no third-party requests.
 */
add_filter( 'should_load_remote_block_patterns', '__return_false' );
