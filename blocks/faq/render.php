<?php
/**
 * FAQ accordion from the jd_faq post type. Native <details>, works without JS,
 * and feeds one FAQPage JSON-LD block per page.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

$jd_args = array(
	'post_type'      => 'jd_faq',
	'posts_per_page' => (int) ( $attributes['count'] ?? 12 ),
	'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
	'no_found_rows'  => true,
);
if ( ! empty( $attributes['group'] ) ) {
	$jd_args['tax_query'] = array( array( 'taxonomy' => 'jd_faq_group', 'field' => 'slug', 'terms' => sanitize_title( $attributes['group'] ) ) ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
}
$jd_faqs = get_posts( $jd_args );
if ( ! $jd_faqs ) {
	if ( current_user_can( 'edit_posts' ) ) {
		printf( '<p %s>%s</p>', get_block_wrapper_attributes( array( 'class' => 'jd-empty' ) ), esc_html__( 'Add questions under FAQs in the dashboard and they will appear here.', 'jawad' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	return;
}

$jd_out    = '';
$jd_schema = array();
foreach ( $jd_faqs as $jd_f ) {
	$jd_answer   = apply_filters( 'the_content', $jd_f->post_content ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
	$jd_out     .= sprintf( '<details class="jd-faq__item"><summary>%1$s</summary><div class="jd-faq__a">%2$s</div></details>', esc_html( get_the_title( $jd_f ) ), wp_kses_post( $jd_answer ) );
	$jd_schema[] = array( 'q' => wp_strip_all_tags( get_the_title( $jd_f ) ), 'a' => wp_strip_all_tags( $jd_answer ) );
}
if ( ! is_admin() && ! ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
	jd_faq_schema_collect( $jd_schema );
}

printf( '<div %1$s>%2$s</div>', get_block_wrapper_attributes( array( 'class' => 'jd-faq' ) ), $jd_out ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
