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
$jd_est_pg  = get_page_by_path( 'estimate' );
$jd_est_url = $jd_est_pg ? get_permalink( $jd_est_pg ) : home_url( '/estimate/' );
$jd_out     = '';
$jd_pct     = (int) jd_opt( 'promo_pct' );
$jd_promo   = 'project' === $jd_group && jd_opt( 'promo_on' ) && $jd_pct > 0;
foreach ( $jd_plans as $jd_p ) {
	$jd_id    = $jd_p->ID;
	$jd_hot   = (bool) get_post_meta( $jd_id, '_jd_featured', true );
	$jd_badge = jd_meta( $jd_id, '_jd_badge' );
	$jd_feats = '';
	foreach ( jd_lines( get_post_meta( $jd_id, '_jd_features', true ) ) as $jd_f ) {
		$jd_feats .= '<li>' . esc_html( $jd_f ) . '</li>';
	}
	$jd_cta = jd_meta( $jd_id, '_jd_cta_label' );
	$jd_est = '';
	if ( 'project' === $jd_group ) {
		$jd_type = jd_estimator_type_for( get_the_title( $jd_id ) );
		$jd_est  = sprintf(
			'<a class="jd-plan__est" href="%1$s" data-est="%2$s">%3$s <span aria-hidden="true">&rarr;</span></a>',
			esc_url( add_query_arg( 'type', $jd_type, $jd_est_url ) ),
			esc_attr( $jd_type ),
			esc_html__( 'Estimate my exact cost', 'jawad' )
		);
	}
	$jd_stick = '';
	if ( $jd_hot && $jd_promo ) {
		$jd_ring  = str_repeat( sprintf( '%1$s%% OFF · FIRST PROJECT · ', $jd_pct ), 2 );
		$jd_stick = sprintf(
			'<span class="jd-sticker" aria-hidden="true"><svg viewBox="0 0 100 100"><defs><path id="%1$s" d="M50,50 m-38,0 a38,38 0 1,1 76,0 a38,38 0 1,1 -76,0"/></defs><text><textPath href="#%1$s">%2$s</textPath></text></svg><b>%3$s%%</b></span>',
			esc_attr( 'jd-ring-' . $jd_id ),
			esc_html( $jd_ring ),
			esc_html( $jd_pct )
		);
	}
	$jd_out .= sprintf(
		'<div class="jd-plan%1$s">%10$s<p class="jd-label jd-plan__name"><span>%2$s</span>%3$s</p><p class="jd-plan__amt">%4$s<small>%5$s</small></p>%6$s<ul class="jd-plan__list">%7$s</ul>%8$s%9$s</div>',
		$jd_hot ? ' is-hot' : '',
		esc_html( get_the_title( $jd_id ) ),
		$jd_badge ? '<span>' . esc_html( $jd_badge ) . '</span>' : '',
		esc_html( jd_meta( $jd_id, '_jd_price_main' ) ),
		esc_html( jd_meta( $jd_id, '_jd_price_sub' ) ),
		jd_meta( $jd_id, '_jd_desc' ) ? '<p class="jd-muted jd-plan__desc">' . esc_html( jd_meta( $jd_id, '_jd_desc' ) ) . '</p>' : '',
		$jd_feats,
		$jd_cta ? sprintf( '<a class="jd-btn %1$s" href="%2$s">%3$s</a>', $jd_hot ? 'jd-btn--dark' : 'jd-btn--line', esc_url( jd_meta( $jd_id, '_jd_cta_url', $jd_default ) ), esc_html( $jd_cta ) ) : '',
		$jd_est,
		$jd_stick
	);
}

$jd_deal = '';
if ( $jd_promo ) {
	$jd_code = (string) jd_opt( 'promo_code' );
	$jd_deal = sprintf(
		'<div class="jd-deal"><div class="jd-deal__pct"><b>%1$s</b><span>%%<br>%2$s</span></div><div class="jd-deal__body"><p class="jd-deal__k">%3$s</p><p class="jd-deal__t">%4$s</p><p class="jd-deal__s">%5$s</p></div>%6$s</div>',
		esc_html( $jd_pct ),
		esc_html__( 'off', 'jawad' ),
		esc_html__( 'New client offer', 'jawad' ),
		/* translators: %s: discount percent. */
		esc_html( sprintf( __( '%s%% off your first project', 'jawad' ), $jd_pct ) ),
		esc_html( jd_opt( 'promo_text' ) ),
		$jd_code ? sprintf( '<button type="button" class="jd-deal__code" data-copy="%1$s" data-done="%3$s"><span>%1$s</span><small>%2$s</small></button>', esc_attr( $jd_code ), esc_html__( 'Tap to copy', 'jawad' ), esc_attr__( 'Copied', 'jawad' ) ) : ''
	);
}

echo $jd_deal; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
printf(
	'<div %1$s>%2$s</div>',
	get_block_wrapper_attributes( array( 'class' => 'jd-plans jd-plans--' . $jd_group ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$jd_out // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);

