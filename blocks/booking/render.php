<?php
/**
 * Booking calendar embed. The iframe is the only third-party request and only loads on the page that holds it.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

$jd_url = ! empty( $attributes['url'] ) ? (string) $attributes['url'] : jd_booking_url();

if ( '' === $jd_url ) {
	$jd_contact = get_page_by_path( 'contact' );
	printf(
		'<div %1$s><p>%2$s</p><a class="jd-btn jd-btn--primary" href="%3$s">%4$s</a></div>',
		get_block_wrapper_attributes( array( 'class' => 'jd-booking jd-booking--empty' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		esc_html__( 'Online booking is being set up. Send a short message and I will reply with a few times that suit you.', 'jawad' ),
		esc_url( $jd_contact ? get_permalink( $jd_contact ) : home_url( '/contact/' ) ),
		esc_html__( 'Send a message', 'jawad' )
	);
	return;
}

$jd_provider = jd_booking_provider( $jd_url );
$jd_height   = max( 480, min( 1400, (int) ( $attributes['height'] ?? 760 ) ) );

printf(
	'<div %1$s style="--jd-booking-h:%2$dpx"><iframe src="%3$s" title="%4$s" loading="lazy" allow="payment" referrerpolicy="strict-origin-when-cross-origin"></iframe><p class="jd-booking__alt jd-muted">%5$s <a href="%6$s" target="_blank" rel="noopener">%7$s</a></p></div>',
	get_block_wrapper_attributes( array( 'class' => 'jd-booking jd-booking--' . $jd_provider ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	(int) $jd_height,
	esc_url( jd_booking_embed_src( $jd_url ) ),
	esc_attr__( 'Book a call', 'jawad' ),
	esc_html__( 'Calendar not loading?', 'jawad' ),
	esc_url( $jd_url ),
	esc_html__( 'Open it in a new tab', 'jawad' )
);
