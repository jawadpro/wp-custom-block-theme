<?php
/**
 * Front-end weight reduction. Everything here is safe to remove individually.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', function () {
	// Emoji detection script and styles.
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'emoji_svg_url', '__return_false' );

	// Head noise that does nothing for visitors or search engines.
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
} );

/**
 * Only load the CSS of blocks that are actually on the page (default for block themes, made explicit).
 */
add_filter( 'should_load_separate_core_block_assets', '__return_true' );

/**
 * Skip the classic-theme compatibility stylesheet.
 */
add_action( 'wp_enqueue_scripts', function () {
	wp_dequeue_style( 'classic-theme-styles' );
}, 20 );

/**
 * Remove the DNS prefetch to s.w.org that the emoji script used.
 */
add_filter( 'wp_resource_hints', function ( $urls, $relation ) {
	if ( 'dns-prefetch' === $relation ) {
		$urls = array_filter( $urls, function ( $url ) {
			$href = is_array( $url ) ? ( $url['href'] ?? '' ) : $url;
			return false === strpos( (string) $href, 's.w.org' );
		} );
	}
	return $urls;
}, 10, 2 );

/**
 * Give the first project or featured image on a page high fetch priority (LCP).
 */
add_filter( 'wp_get_attachment_image_attributes', function ( $attr, $attachment, $size ) {
	if ( 'jd-wide' === $size && ! is_admin() ) {
		$attr['fetchpriority'] = 'high';
		unset( $attr['loading'] );
	}
	return $attr;
}, 10, 3 );
