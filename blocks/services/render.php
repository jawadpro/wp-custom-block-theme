<?php
/**
 * Services from the jd_service post type.
 * stack: sticky stacked cards (home). rows: detailed rows with anchors (services archive).
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

$jd_layout   = in_array( $attributes['layout'] ?? 'stack', array( 'stack', 'rows' ), true ) ? $attributes['layout'] : 'stack';
$jd_services = get_posts(
	array(
		'post_type'      => 'jd_service',
		'posts_per_page' => (int) ( $attributes['count'] ?? 6 ),
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
		'no_found_rows'  => true,
	)
);

if ( ! $jd_services ) {
	if ( current_user_can( 'edit_posts' ) ) {
		printf( '<p %s>%s</p>', get_block_wrapper_attributes( array( 'class' => 'jd-empty' ) ), esc_html__( 'Add services under Services in the dashboard and they will appear here.', 'jawad' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	return;
}

$jd_out = '';
foreach ( $jd_services as $jd_i => $jd_s ) {
	$jd_id       = $jd_s->ID;
	$jd_features = jd_lines( get_post_meta( $jd_id, '_jd_features', true ) );
	$jd_list     = '';
	foreach ( 'stack' === $jd_layout ? array_slice( $jd_features, 0, 4 ) : $jd_features as $jd_f ) {
		$jd_list .= '<li>' . esc_html( $jd_f ) . '</li>';
	}
	$jd_price    = jd_meta( $jd_id, '_jd_price' );
	$jd_timeline = jd_meta( $jd_id, '_jd_timeline' );
	$jd_summary  = has_excerpt( $jd_id ) ? get_the_excerpt( $jd_id ) : '';
	$jd_num      = sprintf( '%02d', $jd_i + 1 );

	if ( 'stack' === $jd_layout ) {
		$jd_out .= sprintf(
			'<article class="jd-scard"><div><p class="jd-scard__idx">%1$s / %2$s</p><h3 class="jd-scard__title">%3$s</h3></div><div><p class="jd-muted jd-scard__sum">%4$s</p><ul class="jd-ticks">%5$s</ul><div class="jd-scard__meta">%6$s%7$s<a class="jd-scard__link" href="%8$s">%9$s %10$s</a></div></div></article>',
			esc_html( $jd_num ),
			esc_html( get_the_title( $jd_id ) ),
			esc_html( jd_meta( $jd_id, '_jd_card_title', get_the_title( $jd_id ) ) ),
			esc_html( $jd_summary ),
			$jd_list,
			$jd_price ? '<span class="jd-price">' . esc_html( $jd_price ) . '</span>' : '',
			$jd_timeline ? '<span class="jd-muted">' . esc_html( $jd_timeline ) . '</span>' : '',
			esc_url( get_permalink( $jd_id ) ),
			esc_html__( 'Details', 'jawad' ),
			jd_icon( 'arrow-up-right' )
		);
	} else {
		$jd_before = jd_meta( $jd_id, '_jd_before' );
		$jd_after  = jd_meta( $jd_id, '_jd_after' );
		$jd_ba     = '';
		if ( $jd_before && $jd_after ) {
			$jd_ba = sprintf(
				'<div class="jd-ba"><div><span class="jd-label">%1$s</span><span class="jd-ba__v is-bad">%2$s</span></div><div><span class="jd-label">%3$s</span><span class="jd-ba__v is-good">%4$s</span></div></div>',
				esc_html__( 'Before', 'jawad' ),
				esc_html( $jd_before ),
				esc_html__( 'After', 'jawad' ),
				esc_html( $jd_after )
			);
		}
		$jd_facts = '';
		if ( $jd_price ) {
			$jd_facts .= '<span><span class="jd-label">' . esc_html__( 'Price', 'jawad' ) . '</span><b>' . esc_html( $jd_price ) . '</b></span>';
		}
		if ( $jd_timeline ) {
			$jd_facts .= '<span><span class="jd-label">' . esc_html__( 'Timeline', 'jawad' ) . '</span><b>' . esc_html( $jd_timeline ) . '</b></span>';
		}
		$jd_out .= sprintf(
			'<article class="jd-svc jd-reveal" id="%1$s"><div><p class="jd-label">%2$s · %3$s</p><h2 class="jd-svc__title"><a href="%4$s">%5$s</a></h2><p class="jd-muted">%6$s</p><div class="jd-facts">%7$s</div>%8$s</div><ul class="jd-arrows">%9$s</ul></article>',
			esc_attr( $jd_s->post_name ),
			esc_html( $jd_num ),
			esc_html( get_the_title( $jd_id ) ),
			esc_url( get_permalink( $jd_id ) ),
			esc_html( jd_meta( $jd_id, '_jd_card_title', get_the_title( $jd_id ) ) ),
			esc_html( $jd_summary ),
			$jd_facts,
			$jd_ba,
			$jd_list
		);
	}
}

if ( 'rows' === $jd_layout ) {
	$jd_chips = '';
	foreach ( $jd_services as $jd_s ) {
		$jd_chips .= sprintf( '<a class="jd-chip" href="#%1$s">%2$s</a>', esc_attr( $jd_s->post_name ), esc_html( get_the_title( $jd_s ) ) );
	}
	$jd_out = '<nav class="jd-chips" aria-label="' . esc_attr__( 'Jump to a service', 'jawad' ) . '">' . $jd_chips . '</nav>' . $jd_out;
}

printf(
	'<div %1$s>%2$s</div>',
	get_block_wrapper_attributes( array( 'class' => 'stack' === $jd_layout ? 'jd-stack' : 'jd-svcs' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$jd_out // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
