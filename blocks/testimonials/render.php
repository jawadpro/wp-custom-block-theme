<?php
/**
 * Testimonials slider. All quotes are in the HTML (crawlable, no-JS readable); JS shows one at a time.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

$jd_items = get_posts(
	array(
		'post_type'      => 'jd_testimonial',
		'posts_per_page' => (int) ( $attributes['count'] ?? 5 ),
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		'no_found_rows'  => true,
	)
);
if ( ! $jd_items ) {
	if ( current_user_can( 'edit_posts' ) ) {
		printf( '<p %s>%s</p>', get_block_wrapper_attributes( array( 'class' => 'jd-empty' ) ), esc_html__( 'Add reviews under Testimonials in the dashboard and they will appear here.', 'jawad' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	return;
}

$jd_total   = count( $jd_items );
$jd_slides  = '';
$jd_ratings = array();
foreach ( $jd_items as $jd_i => $jd_t ) {
	$jd_id     = $jd_t->ID;
	$jd_name   = get_the_title( $jd_id );
	$jd_short  = jd_meta( $jd_id, '_jd_quote_short', wp_trim_words( wp_strip_all_tags( $jd_t->post_content ), 18 ) );
	$jd_role   = jd_meta( $jd_id, '_jd_role' );
	$jd_source = jd_meta( $jd_id, '_jd_source_url' );
	$jd_rating = (float) jd_meta( $jd_id, '_jd_rating', 0 );
	if ( $jd_rating ) {
		$jd_ratings[] = $jd_rating;
	}
	$jd_avatar = has_post_thumbnail( $jd_id )
		? get_the_post_thumbnail( $jd_id, 'thumbnail', array( 'class' => 'jd-av jd-av--img', 'alt' => '' ) )
		: '<span class="jd-av" aria-hidden="true">' . esc_html( jd_initials( $jd_name ) ) . '</span>';

	$jd_slides .= sprintf(
		'<figure class="jd-review%1$s" data-i="%2$d"%3$s><blockquote class="jd-review__q"><p>%4$s</p></blockquote><figcaption class="jd-who">%5$s<span><span class="jd-who__name">%6$s</span><span class="jd-muted jd-who__role">%7$s%8$s</span></span></figcaption></figure>',
		0 === $jd_i ? ' is-on' : '',
		(int) $jd_i,
		0 === $jd_i ? '' : ' aria-hidden="true"',
		esc_html( $jd_short ),
		$jd_avatar,
		esc_html( $jd_name ),
		esc_html( $jd_role ),
		$jd_source ? ' · <a href="' . esc_url( $jd_source ) . '" rel="noopener" target="_blank">' . esc_html__( 'View review', 'jawad' ) . '</a>' : ''
	);
}

$jd_controls = '';
if ( $jd_total > 1 ) {
	$jd_controls = sprintf(
		'<div class="jd-reviews__ctrl"><span class="jd-label jd-reviews__count">01 / %1$s</span><button type="button" class="jd-rbtn jd-mag" data-dir="-1" aria-label="%2$s">%3$s</button><button type="button" class="jd-rbtn jd-mag" data-dir="1" aria-label="%4$s">%5$s</button></div>',
		esc_html( sprintf( '%02d', $jd_total ) ),
		esc_attr__( 'Previous review', 'jawad' ),
		jd_icon( 'arrow-left' ),
		esc_attr__( 'Next review', 'jawad' ),
		jd_icon( 'arrow-right' )
	);
}

$jd_rate = '';
if ( $jd_ratings ) {
	$jd_avg  = round( array_sum( $jd_ratings ) / count( $jd_ratings ), 1 );
	$jd_rate = sprintf(
		'<div class="jd-rate"><span class="jd-stars" aria-hidden="true">★★★★★</span><span><b>%1$s</b> <span class="jd-muted">%2$s</span></span></div>',
		esc_html( number_format_i18n( $jd_avg, 1 ) ),
		esc_html( sprintf( /* translators: %d number of reviews */ _n( 'average from %d review', 'average from %d reviews', count( $jd_ratings ), 'jawad' ), count( $jd_ratings ) ) )
	);
}

printf(
	'<div %1$s><div class="jd-reviews__panel" aria-roledescription="carousel"><div class="jd-reviews__stage">%2$s</div>%3$s</div>%4$s</div>',
	get_block_wrapper_attributes( array( 'class' => 'jd-reviews' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$jd_slides, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$jd_controls, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$jd_rate // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
