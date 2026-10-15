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

/**
 * The external booking link (Cal.com, Calendly, Google appointment schedule), or ''.
 */
function jd_booking_url() {
	return (string) jd_opt( 'calendly_url' );
}

/**
 * Where on-site "Book a call" buttons point: the Book page, or the booking link itself if no page is set.
 */
function jd_book_page_url() {
	$page = trim( (string) jd_opt( 'book_page' ) );
	if ( '' === $page ) {
		return jd_booking_url();
	}
	return 0 === strpos( $page, '/' ) ? home_url( $page ) : $page;
}

/**
 * Which booking service a link belongs to: cal, calendly, google or other.
 */
function jd_booking_provider( $url ) {
	$host = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );
	if ( preg_match( '/(^|\.)cal\.(com|eu)$/', $host ) ) {
		return 'cal';
	}
	if ( false !== strpos( $host, 'calendly.com' ) ) {
		return 'calendly';
	}
	if ( 'calendar.google.com' === $host || 'calendar.app.google' === $host ) {
		return 'google';
	}
	return 'other';
}

/**
 * Embed URL for the booking link, themed to match the site where the service allows it.
 */
function jd_booking_embed_src( $url ) {
	switch ( jd_booking_provider( $url ) ) {
		case 'cal':
			return add_query_arg( array( 'embed' => 'true', 'theme' => 'dark', 'layout' => 'month_view' ), $url );
		case 'calendly':
			return add_query_arg(
				array(
					'embed_domain'     => (string) wp_parse_url( home_url(), PHP_URL_HOST ),
					'embed_type'       => 'Inline',
					'hide_gdpr_banner' => '1',
					'background_color' => '0f1528',
					'text_color'       => 'f4f6fb',
					'primary_color'    => '3d5afe',
				),
				$url
			);
		case 'google':
			return add_query_arg( 'gv', 'true', $url );
	}
	return $url;
}
