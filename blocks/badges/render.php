<?php
/**
 * Hero badges from Theme Options.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

$jd_items = array();
if ( jd_opt( 'availability_on' ) && jd_opt( 'availability_text' ) ) {
	$jd_items[] = '<span class="jd-badge"><span class="jd-dot"></span>' . esc_html( jd_opt( 'availability_text' ) ) . '</span>';
}
foreach ( array( 'badge_2', 'badge_3' ) as $jd_key ) {
	if ( jd_opt( $jd_key ) ) {
		$jd_items[] = '<span class="jd-badge">' . esc_html( jd_opt( $jd_key ) ) . '</span>';
	}
}
if ( ! $jd_items ) {
	return;
}
printf( '<div %1$s>%2$s</div>', get_block_wrapper_attributes( array( 'class' => 'jd-badges' ) ), implode( '', $jd_items ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
