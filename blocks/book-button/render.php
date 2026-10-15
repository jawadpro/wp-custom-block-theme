<?php
/**
 * "Book a 20-min call" button. Renders nothing until a booking link is configured.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

if ( '' === jd_booking_url() ) {
	return;
}
$jd_label = $attributes['label'] ? $attributes['label'] : (string) jd_opt( 'book_label' );
$jd_style = in_array( $attributes['variant'] ?? '', array( 'line', 'primary', 'light' ), true ) ? $attributes['variant'] : 'line';

printf(
	'<a %1$s href="%2$s"><span class="jd-book-btn__dot" aria-hidden="true"></span>%3$s</a>',
	get_block_wrapper_attributes( array( 'class' => 'jd-btn jd-btn--' . $jd_style . ' jd-book-btn jd-mag' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_url( jd_book_page_url() ),
	esc_html( $jd_label )
);
