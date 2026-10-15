<?php
/**
 * Hero offer card: a free audit (or any offer) visitors can act on immediately.
 * Defaults come from Theme Options › Free audit offer.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

$jd_label = $attributes['label'] ? $attributes['label'] : (string) jd_opt( 'audit_label' );
$jd_title = $attributes['title'] ? $attributes['title'] : (string) jd_opt( 'audit_title' );
$jd_text  = $attributes['text'] ? $attributes['text'] : (string) jd_opt( 'audit_text' );
$jd_url   = $attributes['url'] ? $attributes['url'] : jd_opt( 'audit_url' );
if ( ! $jd_url ) {
	$jd_contact = get_page_by_path( 'contact' );
	$jd_url     = $jd_contact ? get_permalink( $jd_contact ) : home_url( '/contact/' );
}

printf(
	'<a %1$s href="%2$s"><span class="jd-offer__icon" aria-hidden="true"><b>%3$s</b></span><span class="jd-offer__text"><span class="jd-label">%4$s</span><span class="jd-offer__title">%5$s</span><span class="jd-muted">%6$s</span></span><span class="jd-offer__arrow" aria-hidden="true">%7$s</span></a>',
	get_block_wrapper_attributes( array( 'class' => 'jd-offer jd-mag' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_url( $jd_url ),
	esc_html( $attributes['badge'] ? $attributes['badge'] : (string) jd_opt( 'audit_badge' ) ),
	esc_html( $jd_label ),
	esc_html( $jd_title ),
	esc_html( $jd_text ),
	jd_icon( 'arrow-up-right' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
