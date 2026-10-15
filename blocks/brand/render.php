<?php
/**
 * Brand logo: Site Logo if set, otherwise a cobalt tile with the mark letter.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

$jd_name    = (string) jd_opt( 'brand_name' );
$jd_name    = $jd_name ? $jd_name : get_bloginfo( 'name' );
$jd_logo_id = (int) get_theme_mod( 'custom_logo' );
$jd_parts   = preg_split( '/\s+/', trim( $jd_name ), 2 );

if ( $jd_logo_id ) {
	$jd_mark = wp_get_attachment_image( $jd_logo_id, 'thumbnail', false, array( 'class' => 'jd-brand__img', 'alt' => '', 'loading' => 'eager', 'decoding' => 'async' ) );
} else {
	$jd_mark = '<span class="jd-brand__mark" aria-hidden="true">' . esc_html( jd_opt( 'brand_mark' ) ) . '</span>';
}

$jd_word = esc_html( mb_strtolower( $jd_parts[0] ) );
if ( isset( $jd_parts[1] ) ) {
	$jd_word .= '<em>.</em>' . esc_html( mb_strtolower( $jd_parts[1] ) );
}

printf(
	'<a %1$s href="%2$s" rel="home" aria-label="%3$s">%4$s<span class="jd-brand__word">%5$s</span></a>',
	get_block_wrapper_attributes( array( 'class' => 'jd-brand' ) ),
	esc_url( home_url( '/' ) ),
	esc_attr( sprintf( /* translators: %s brand name */ __( '%s, home', 'jawad' ), $jd_name ) ),
	$jd_mark, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$jd_word // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
