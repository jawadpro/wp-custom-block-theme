<?php
/**
 * Work items. track: horizontal pinned row (home). grid: filterable grid (work archive).
 * Filters link to real taxonomy archives so they work without JavaScript and are crawlable.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

$jd_layout = ( $attributes['layout'] ?? 'track' ) === 'grid' ? 'grid' : 'track';
$jd_args   = array(
	'post_type'      => 'jd_project',
	'posts_per_page' => (int) ( $attributes['count'] ?? 6 ),
	'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
	'no_found_rows'  => true,
);
if ( ! empty( $attributes['featuredOnly'] ) ) {
	$jd_args['meta_query'] = array( array( 'key' => '_jd_featured', 'value' => '1' ) ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
}
$jd_current_term = is_tax( 'jd_project_type' ) ? get_queried_object() : null;
if ( $jd_current_term ) {
	$jd_args['tax_query'] = array( array( 'taxonomy' => 'jd_project_type', 'terms' => $jd_current_term->term_id ) ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
}
$jd_projects = get_posts( $jd_args );

if ( ! $jd_projects ) {
	if ( current_user_can( 'edit_posts' ) ) {
		printf( '<p %s>%s</p>', get_block_wrapper_attributes( array( 'class' => 'jd-empty' ) ), esc_html__( 'Add projects under Work in the dashboard and they will appear here.', 'jawad' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	return;
}

$jd_cards = '';
foreach ( $jd_projects as $jd_p ) {
	$jd_cards .= jd_project_card( $jd_p, 'grid' === $jd_layout ? 'jd-pcard jd-gcard' : 'jd-pcard' );
}

if ( 'track' === $jd_layout ) {
	printf(
		'<div %1$s><div class="jd-track">%2$s</div></div>',
		get_block_wrapper_attributes( array( 'class' => 'jd-track-wrap' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$jd_cards // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	);
	return;
}

$jd_filters = '';
if ( ! empty( $attributes['filters'] ) ) {
	$jd_terms = get_terms( array( 'taxonomy' => 'jd_project_type', 'hide_empty' => true ) );
	if ( $jd_terms && ! is_wp_error( $jd_terms ) ) {
		$jd_archive  = get_post_type_archive_link( 'jd_project' );
		$jd_filters .= sprintf(
			'<a class="jd-chip%1$s" href="%2$s" data-filter="*"%3$s>%4$s</a>',
			$jd_current_term ? '' : ' is-on',
			esc_url( $jd_archive ),
			$jd_current_term ? '' : ' aria-current="page"',
			esc_html__( 'All', 'jawad' )
		);
		foreach ( $jd_terms as $jd_t ) {
			$jd_on       = $jd_current_term && $jd_current_term->term_id === $jd_t->term_id;
			$jd_filters .= sprintf(
				'<a class="jd-chip%1$s" href="%2$s" data-filter="%3$s"%4$s>%5$s</a>',
				$jd_on ? ' is-on' : '',
				esc_url( get_term_link( $jd_t ) ),
				esc_attr( $jd_t->slug ),
				$jd_on ? ' aria-current="page"' : '',
				esc_html( $jd_t->name )
			);
		}
		$jd_filters = '<nav class="jd-chips jd-filters" aria-label="' . esc_attr__( 'Filter projects', 'jawad' ) . '">' . $jd_filters . '</nav>';
	}
}

printf(
	'<div %1$s>%2$s<div class="jd-grid" aria-live="polite">%3$s</div></div>',
	get_block_wrapper_attributes( array( 'class' => 'jd-work' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$jd_filters, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$jd_cards // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
