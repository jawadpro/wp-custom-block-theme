<?php
/**
 * Case study body: problem, solution, results, quote. Sections without content are skipped.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

$jd_id = (int) ( $block->context['postId'] ?? get_the_ID() );
if ( ! $jd_id ) {
	return;
}

$jd_problem  = jd_meta( $jd_id, '_jd_problem' );
$jd_solution = jd_meta( $jd_id, '_jd_solution' );
$jd_results  = '';
foreach ( array( 1, 2 ) as $jd_n ) {
	$jd_v = jd_meta( $jd_id, '_jd_result_' . $jd_n );
	if ( $jd_v ) {
		$jd_results .= sprintf( '<div><span class="jd-ba__v is-good">%1$s</span><span class="jd-muted">%2$s</span></div>', esc_html( $jd_v ), esc_html( jd_meta( $jd_id, '_jd_result_' . $jd_n . '_lbl' ) ) );
	}
}

$jd_cols = '';
if ( $jd_problem ) {
	$jd_cols .= '<div><h2 class="jd-case__h">' . esc_html__( 'The problem', 'jawad' ) . '</h2>' . wpautop( esc_html( $jd_problem ) ) . '</div>';
}
if ( $jd_solution ) {
	$jd_cols .= '<div><h2 class="jd-case__h">' . esc_html__( 'What I did', 'jawad' ) . '</h2>' . wpautop( esc_html( $jd_solution ) ) . '</div>';
}
if ( $jd_results ) {
	$jd_cols .= '<div><h2 class="jd-case__h">' . esc_html__( 'The result', 'jawad' ) . '</h2><div class="jd-results">' . $jd_results . '</div></div>';
}

$jd_quote = jd_meta( $jd_id, '_jd_quote' );
$jd_q     = '';
if ( $jd_quote ) {
	$jd_q = sprintf(
		'<figure class="jd-case__quote"><blockquote><p>%1$s</p></blockquote><figcaption>%2$s</figcaption></figure>',
		esc_html( $jd_quote ),
		esc_html( trim( jd_meta( $jd_id, '_jd_quote_name' ) . ( jd_meta( $jd_id, '_jd_quote_role' ) ? ', ' . jd_meta( $jd_id, '_jd_quote_role' ) : '' ) ) )
	);
}

if ( ! $jd_cols && ! $jd_q ) {
	return;
}
printf( '<div %1$s><div class="jd-case__cols">%2$s</div>%3$s</div>', get_block_wrapper_attributes( array( 'class' => 'jd-case' ) ), $jd_cols, $jd_q ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
