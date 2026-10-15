<?php
/**
 * Live clocks. Server renders the current times so the block is correct without JS; JS keeps them ticking.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

$jd_zones = array();
foreach ( explode( ',', (string) ( $attributes['zones'] ?? '' ) ) as $jd_pair ) {
	$jd_bits = array_map( 'trim', explode( '|', $jd_pair ) );
	if ( empty( $jd_bits[0] ) ) {
		continue;
	}
	try {
		$jd_tz = new DateTimeZone( $jd_bits[0] );
	} catch ( Exception $e ) {
		continue;
	}
	$jd_zones[] = array( $jd_bits[0], $jd_bits[1] ?? $jd_bits[0], $jd_tz );
}
if ( ! $jd_zones ) {
	return;
}

$jd_now   = new DateTimeImmutable( 'now' );
$jd_cells = '';
foreach ( $jd_zones as $jd_z ) {
	$jd_cells .= sprintf(
		'<div><p class="jd-label">%1$s</p><p class="jd-clocks__t" data-tz="%2$s">%3$s</p></div>',
		esc_html( $jd_z[1] ),
		esc_attr( $jd_z[0] ),
		esc_html( $jd_now->setTimezone( $jd_z[2] )->format( 'g:i A' ) )
	);
}

try {
	$jd_client = $jd_now->setTimezone( new DateTimeZone( (string) jd_opt( 'live_tz' ) ) );
} catch ( Exception $e ) {
	$jd_client = $jd_now;
}
$jd_hour   = (int) $jd_client->format( 'G' );
$jd_day    = (int) $jd_client->format( 'N' );
$jd_online = $jd_day < 6 && $jd_hour >= (int) jd_opt( 'live_start' ) && $jd_hour < (int) jd_opt( 'live_end' );

printf(
	'<div %1$s>%2$s<div class="jd-clocks__status"><p class="jd-badge jd-live%3$s"><span class="jd-dot"></span><span class="jd-live__text">%4$s</span></p><p class="jd-muted">%5$s</p></div></div>',
	get_block_wrapper_attributes( array( 'class' => 'jd-clocks' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$jd_cells, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$jd_online ? ' is-online' : '',
	esc_html( $jd_online ? __( 'Online now', 'jawad' ) : __( 'Offline, replies next working day', 'jawad' ) ),
	esc_html( jd_opt( 'hours_text' ) )
);
