<?php
/**
 * Giant footer wordmark. Letters are split server-side so the animation needs no DOM work.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

$jd_text = trim( (string) ( $attributes['text'] ?? '' ) );
if ( '' === $jd_text ) {
	$jd_text = (string) jd_opt( 'brand_name' );
}
if ( '' === $jd_text ) {
	$jd_text = get_bloginfo( 'name' );
}
$jd_letters = '';
foreach ( mb_str_split( $jd_text ) as $jd_ch ) {
	$jd_letters .= ' ' === $jd_ch ? '<span>&nbsp;</span>' : '<span>' . esc_html( $jd_ch ) . '</span>';
}
$jd_legal = array();
if ( get_privacy_policy_url() ) {
	$jd_legal[] = sprintf( '<a href="%1$s">%2$s</a>', esc_url( get_privacy_policy_url() ), esc_html__( 'Privacy', 'jawad' ) );
}
$jd_terms = get_page_by_path( 'terms' );
if ( $jd_terms && 'publish' === $jd_terms->post_status ) {
	$jd_legal[] = sprintf( '<a href="%1$s">%2$s</a>', esc_url( get_permalink( $jd_terms ) ), esc_html__( 'Terms', 'jawad' ) );
}
$jd_brand = (string) jd_opt( 'brand_name' );

printf(
	'<div %1$s><p class="jd-wordmark" aria-hidden="true">%2$s</p><div class="jd-fbot"><span>&copy; %3$s %4$s</span><span>%5$s</span></div></div>',
	get_block_wrapper_attributes( array( 'class' => 'jd-footer-end' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$jd_letters, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_html( wp_date( 'Y' ) ),
	esc_html( $jd_brand ? $jd_brand : get_bloginfo( 'name' ) ),
	implode( ' · ', $jd_legal ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
