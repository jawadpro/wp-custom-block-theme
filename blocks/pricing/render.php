<?php
/**
 * Pricing plans from the jd_plan post type.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

$jd_group = ( $attributes['group'] ?? 'project' ) === 'care' ? 'care' : 'project';
$jd_plans = get_posts(
	array(
		'post_type'      => 'jd_plan',
		'posts_per_page' => 6,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
		'meta_query'     => array( array( 'key' => '_jd_group', 'value' => $jd_group ) ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		'no_found_rows'  => true,
	)
);
if ( ! $jd_plans ) {
	if ( current_user_can( 'edit_posts' ) ) {
		printf( '<p %s>%s</p>', get_block_wrapper_attributes( array( 'class' => 'jd-empty' ) ), esc_html__( 'Add plans under Pricing in the dashboard and they will appear here.', 'jawad' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	return;
}

$jd_contact = get_page_by_path( 'contact' );
$jd_default = $jd_contact ? get_permalink( $jd_contact ) : home_url( '/contact/' );
$jd_out     = '';
foreach ( $jd_plans as $jd_p ) {
	$jd_id    = $jd_p->ID;
	$jd_hot   = (bool) get_post_meta( $jd_id, '_jd_featured', true );
	$jd_badge = jd_meta( $jd_id, '_jd_badge' );
	$jd_feats = '';
	foreach ( jd_lines( get_post_meta( $jd_id, '_jd_features', true ) ) as $jd_f ) {
		$jd_feats .= '<li>' . esc_html( $jd_f ) . '</li>';
	}
	$jd_cta = jd_meta( $jd_id, '_jd_cta_label' );
	$jd_out .= sprintf(
		'<div class="jd-plan%1$s"><p class="jd-label jd-plan__name"><span>%2$s</span>%3$s</p><p class="jd-plan__amt">%4$s<small>%5$s</small></p>%6$s<ul class="jd-plan__list">%7$s</ul>%8$s</div>',
		$jd_hot ? ' is-hot' : '',
		esc_html( get_the_title( $jd_id ) ),
		$jd_badge ? '<span>' . esc_html( $jd_badge ) . '</span>' : '',
		esc_html( jd_meta( $jd_id, '_jd_price_main' ) ),
		esc_html( jd_meta( $jd_id, '_jd_price_sub' ) ),
		jd_meta( $jd_id, '_jd_desc' ) ? '<p class="jd-muted jd-plan__desc">' . esc_html( jd_meta( $jd_id, '_jd_desc' ) ) . '</p>' : '',
		$jd_feats,
		$jd_cta ? sprintf( '<a class="jd-btn %1$s" href="%2$s">%3$s</a>', $jd_hot ? 'jd-btn--dark' : 'jd-btn--line', esc_url( jd_meta( $jd_id, '_jd_cta_url', $jd_default ) ), esc_html( $jd_cta ) ) : ''
	);
}

printf(
	'<div %1$s>%2$s</div>',
	get_block_wrapper_attributes( array( 'class' => 'jd-plans jd-plans--' . $jd_group ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$jd_out // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
