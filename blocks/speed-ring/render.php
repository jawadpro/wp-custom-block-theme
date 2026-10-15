<?php
/**
 * Score ring with caption.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

$jd_value = max( 0, min( 100, (int) ( $attributes['value'] ?? 98 ) ) );
printf(
	'<div %1$s><div class="jd-ring" style="--v:%2$d" data-value="%2$d" role="img" aria-label="%3$s"><b>%2$d</b></div><div class="jd-speed__text"><span class="jd-label">%4$s</span><span>%5$s</span></div></div>',
	get_block_wrapper_attributes( array( 'class' => 'jd-speed' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	(int) $jd_value,
	esc_attr( sprintf( '%s: %d', $attributes['label'] ?? '', $jd_value ) ),
	esc_html( $attributes['label'] ?? '' ),
	esc_html( $attributes['text'] ?? '' )
);
