<?php
/**
 * Big scrolling words. Duplicated once for a seamless loop; the copy is hidden from assistive tech.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

$jd_words = array_filter( array_map( 'trim', explode( ',', (string) ( $attributes['items'] ?? '' ) ) ) );
if ( ! $jd_words ) {
	return;
}
$jd_run = '';
foreach ( array_values( $jd_words ) as $jd_i => $jd_word ) {
	$jd_run .= sprintf( '<span class="%1$s">%2$s</span><i class="jd-marquee__dot"></i>', $jd_i % 2 ? 'is-outline' : '', esc_html( $jd_word ) );
}
printf(
	'<div %1$s><p class="screen-reader-text">%2$s</p><div class="jd-marquee__track" aria-hidden="true">%3$s%3$s</div></div>',
	get_block_wrapper_attributes( array( 'class' => 'jd-marquee' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_html( implode( ', ', $jd_words ) ),
	$jd_run // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
