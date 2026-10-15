<?php
/**
 * Migration from the previous theme: legacy "project" posts move to Work (jd_project),
 * and every old /projects/ URL permanently redirects to its /work/ equivalent.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

/**
 * 301 old portfolio URLs: /projects/ and /projects/<slug>/ → /work/...
 * Runs only when the request would otherwise 404, so it never shadows real content.
 */
add_action( 'template_redirect', function () {
	if ( ! is_404() ) {
		return;
	}
	$path = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_parse_url( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : '';
	$home = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	$rel  = '/' . ltrim( substr( $path, strlen( rtrim( $home, '/' ) ) ), '/' );

	if ( ! preg_match( '#^/projects(?:/([^/]+))?/?$#', $rel, $m ) ) {
		return;
	}
	$target = get_post_type_archive_link( 'jd_project' );
	if ( ! empty( $m[1] ) ) {
		$post = get_page_by_path( sanitize_title( $m[1] ), OBJECT, 'jd_project' );
		if ( $post ) {
			$target = get_permalink( $post );
		}
	}
	if ( $target ) {
		wp_safe_redirect( $target, 301, 'Jawad theme' );
		exit;
	}
}, 1 );

/**
 * Convert legacy project posts to jd_project, keeping IDs, content, images and SEO meta.
 * Returns the number of converted posts. Safe to run more than once.
 */
function jd_migrate_legacy_projects( $legacy_type = 'project' ) {
	global $wpdb;
	$ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s", $legacy_type ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	if ( ! $ids ) {
		return 0;
	}
	$types = array();
	foreach ( array( 'Website' => 'website', 'Landing page' => 'landing-page', 'Store' => 'store', 'Speed and fixes' => 'speed-and-fixes' ) as $name => $slug ) {
		$term = term_exists( $slug, 'jd_project_type' );
		if ( ! $term ) {
			$term = wp_insert_term( $name, 'jd_project_type', array( 'slug' => $slug ) );
		}
		if ( ! is_wp_error( $term ) ) {
			$types[ $slug ] = (int) ( is_array( $term ) ? $term['term_id'] : $term );
		}
	}
	$palette = array( '#3D5AFE', '#0F3B44', '#1A2547', '#C9D4FF', '#D7E8C6', '#13263F', '#FFB4A2', '#E8E2D4' );
	$order   = 0;
	foreach ( $ids as $i => $id ) {
		$wpdb->update( $wpdb->posts, array( 'post_type' => 'jd_project' ), array( 'ID' => $id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		clean_post_cache( $id );
		$title = get_the_title( $id );
		$slug  = false !== stripos( $title, 'landing' ) ? 'landing-page' : ( false !== stripos( $title, 'store' ) || false !== stripos( $title, 'woocommerce' ) ? 'store' : 'website' );
		if ( isset( $types[ $slug ] ) && ! has_term( '', 'jd_project_type', $id ) ) {
			wp_set_object_terms( $id, array( $types[ $slug ] ), 'jd_project_type' );
		}
		if ( ! get_post_meta( $id, '_jd_card_bg', true ) ) {
			update_post_meta( $id, '_jd_card_bg', $palette[ $i % count( $palette ) ] );
		}
		if ( ! get_post_meta( $id, '_jd_scope', true ) ) {
			update_post_meta( $id, '_jd_scope', 'landing-page' === $slug ? 'Landing page' : 'Full website' );
		}
		if ( 'publish' === get_post_status( $id ) && $order < 6 ) {
			update_post_meta( $id, '_jd_featured', true );
			$order++;
		}
	}
	update_option( 'jd_legacy_projects_migrated', array( 'from' => $legacy_type, 'ids' => array_map( 'intval', $ids ), 'at' => time() ) );
	flush_rewrite_rules( false );
	return count( $ids );
}

/**
 * Undo: turn migrated posts back into the legacy type (for switching back to the old theme).
 */
function jd_unmigrate_legacy_projects() {
	global $wpdb;
	$log = get_option( 'jd_legacy_projects_migrated' );
	if ( empty( $log['ids'] ) ) {
		return 0;
	}
	foreach ( $log['ids'] as $id ) {
		$wpdb->update( $wpdb->posts, array( 'post_type' => $log['from'] ), array( 'ID' => (int) $id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		clean_post_cache( (int) $id );
	}
	delete_option( 'jd_legacy_projects_migrated' );
	return count( $log['ids'] );
}
