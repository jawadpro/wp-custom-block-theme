<?php
/**
 * Small shared helpers.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

/**
 * Read a single post meta value with a fallback.
 */
function jd_meta( $post_id, $key, $default = '' ) {
	$value = get_post_meta( $post_id, $key, true );
	return ( '' === $value || null === $value ) ? $default : $value;
}

/**
 * Split a textarea value into trimmed, non-empty lines.
 */
function jd_lines( $text ) {
	$lines = preg_split( '/\r\n|\r|\n/', (string) $text );
	return array_values( array_filter( array_map( 'trim', $lines ), 'strlen' ) );
}

/**
 * Inline SVG icons used by the theme. All decorative, hidden from assistive tech.
 */
function jd_icon( $name ) {
	$icons = array(
		'arrow-up-right' => '<svg width="13" height="13" viewBox="0 0 14 14" fill="none" aria-hidden="true" focusable="false"><path d="M3 11L11 3M11 3H5M11 3V9" stroke="currentColor" stroke-width="2"/></svg>',
		'arrow-left'     => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true" focusable="false"><path d="M13 8H3M3 8l4-4M3 8l4 4" stroke="currentColor" stroke-width="1.8"/></svg>',
		'arrow-right'    => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true" focusable="false"><path d="M3 8h10M13 8L9 4M13 8l-4 4" stroke="currentColor" stroke-width="1.8"/></svg>',
	);
	return $icons[ $name ] ?? '';
}

/**
 * Two-letter initials from a name.
 */
function jd_initials( $name ) {
	$parts = preg_split( '/\s+/', trim( (string) $name ) );
	$out   = '';
	foreach ( array_slice( $parts, 0, 2 ) as $part ) {
		$out .= mb_strtoupper( mb_substr( $part, 0, 1 ) );
	}
	return $out;
}

/**
 * Whether a third-party SEO plugin is active, so the theme stays out of the way.
 */
function jd_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' )
		|| defined( 'RANK_MATH_VERSION' )
		|| defined( 'SEOPRESS_VERSION' )
		|| defined( 'AIOSEO_VERSION' )
		|| defined( 'THE_SEO_FRAMEWORK_VERSION' )
		|| class_exists( 'SQ_Classes_ObjController' );
}
