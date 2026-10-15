<?php
/**
 * Project facts row for single project templates.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

$jd_id = (int) ( $block->context['postId'] ?? get_the_ID() );
if ( ! $jd_id ) {
	return;
}

$jd_terms = get_the_terms( $jd_id, 'jd_project_type' );
$jd_facts = array(
	__( 'Client', 'jawad' )   => esc_html( jd_meta( $jd_id, '_jd_client' ) ),
	__( 'Scope', 'jawad' )    => esc_html( jd_meta( $jd_id, '_jd_scope', $jd_terms && ! is_wp_error( $jd_terms ) ? $jd_terms[0]->name : '' ) ),
	__( 'Timeline', 'jawad' ) => esc_html( jd_meta( $jd_id, '_jd_timeline' ) ),
);
$jd_live = jd_meta( $jd_id, '_jd_live_url' );
if ( $jd_live ) {
	$jd_facts[ __( 'Live site', 'jawad' ) ] = sprintf( '<a href="%1$s" target="_blank" rel="noopener">%2$s %3$s</a>', esc_url( $jd_live ), esc_html( preg_replace( '#^https?://(www\.)?#', '', untrailingslashit( $jd_live ) ) ), jd_icon( 'arrow-up-right' ) );
}
$jd_cells = '';
foreach ( array_filter( $jd_facts ) as $jd_label => $jd_value ) {
	$jd_cells .= sprintf( '<div><dt class="jd-label">%1$s</dt><dd>%2$s</dd></div>', esc_html( $jd_label ), $jd_value );
}
if ( ! $jd_cells ) {
	return;
}
printf( '<dl %1$s>%2$s</dl>', get_block_wrapper_attributes( array( 'class' => 'jd-facts-row' ) ), $jd_cells ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
