<?php
/**
 * Service facts and inclusions for single service templates.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

$jd_id = (int) ( $block->context['postId'] ?? get_the_ID() );
if ( ! $jd_id ) {
	return;
}

$jd_items = '';
foreach ( jd_lines( get_post_meta( $jd_id, '_jd_features', true ) ) as $jd_f ) {
	$jd_items .= '<li>' . esc_html( $jd_f ) . '</li>';
}
$jd_facts = '';
foreach ( array( '_jd_price' => __( 'Price', 'jawad' ), '_jd_timeline' => __( 'Timeline', 'jawad' ) ) as $jd_key => $jd_label ) {
	if ( jd_meta( $jd_id, $jd_key ) ) {
		$jd_facts .= '<span><span class="jd-label">' . esc_html( $jd_label ) . '</span><b>' . esc_html( jd_meta( $jd_id, $jd_key ) ) . '</b></span>';
	}
}
$jd_contact = get_page_by_path( 'contact' );
$jd_url     = $jd_contact ? get_permalink( $jd_contact ) : home_url( '/contact/' );
$jd_cta     = jd_meta( $jd_id, '_jd_cta_label', __( 'Get a fixed quote', 'jawad' ) );

printf(
	'<div %1$s><div class="jd-svc-detail__facts"><div class="jd-facts">%2$s</div><a class="jd-btn jd-btn--primary jd-mag" href="%3$s">%4$s %5$s</a></div>%6$s</div>',
	get_block_wrapper_attributes( array( 'class' => 'jd-svc-detail' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$jd_facts, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_url( $jd_url ),
	esc_html( $jd_cta ),
	jd_icon( 'arrow-up-right' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$jd_items ? '<div><h2 class="jd-case__h">' . esc_html__( 'What is included', 'jawad' ) . '</h2><ul class="jd-arrows">' . $jd_items . '</ul></div>' : '' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
