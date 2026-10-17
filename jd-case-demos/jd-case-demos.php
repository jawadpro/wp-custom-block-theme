<?php
/**
 * Plugin Name: JD Case Demos
 * Description: Interactive, responsive demos of your case studies at /demos/. Preview each live project in desktop, tablet and phone frames, or as scrolling screenshots. Separate from the theme; deactivate to remove it completely.
 * Version: 1.0.0
 * Author: Jawad Ilyas
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * License: GPL-2.0-or-later
 * Text Domain: jd-case-demos
 *
 * @package JD_Case_Demos
 */

defined( 'ABSPATH' ) || exit;

define( 'JDCD_VERSION', '1.0.0' );
define( 'JDCD_URL', plugin_dir_url( __FILE__ ) );
define( 'JDCD_BASE', 'demos' );

/**
 * /demos/ and /demos/{project-slug}/ route to the demo viewer.
 */
add_action( 'init', function () {
	add_rewrite_rule( '^' . JDCD_BASE . '/?$', 'index.php?jdcd=1', 'top' );
	add_rewrite_rule( '^' . JDCD_BASE . '/([^/]+)/?$', 'index.php?jdcd=1&jdcd_slug=$matches[1]', 'top' );
} );

add_filter( 'query_vars', function ( $vars ) {
	$vars[] = 'jdcd';
	$vars[] = 'jdcd_slug';
	return $vars;
} );

register_activation_hook( __FILE__, function () {
	add_rewrite_rule( '^' . JDCD_BASE . '/?$', 'index.php?jdcd=1', 'top' );
	add_rewrite_rule( '^' . JDCD_BASE . '/([^/]+)/?$', 'index.php?jdcd=1&jdcd_slug=$matches[1]', 'top' );
	flush_rewrite_rules();
} );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

/**
 * Projects that can be demoed: the theme's jd_project posts, newest order first.
 * Filter with add_filter( 'jdcd_projects', ... ) to add or remove items.
 *
 * @return array[]
 */
function jdcd_projects() {
	$out = array();
	if ( post_type_exists( 'jd_project' ) ) {
		$posts = get_posts(
			array(
				'post_type'      => 'jd_project',
				'posts_per_page' => 30,
				'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
				'no_found_rows'  => true,
			)
		);
		foreach ( $posts as $p ) {
			$shots = array();
			$thumb = get_post_thumbnail_id( $p );
			if ( $thumb ) {
				$shots[] = (int) $thumb;
			}
			// Images in the case study content become extra screenshots.
			foreach ( get_attached_media( 'image', $p->ID ) as $img ) {
				if ( (int) $img->ID !== (int) $thumb ) {
					$shots[] = (int) $img->ID;
				}
			}
			if ( preg_match_all( '/wp-image-(\d+)/', $p->post_content, $m ) ) {
				foreach ( $m[1] as $id ) {
					$shots[] = (int) $id;
				}
			}
			$shots = array_values( array_unique( $shots ) );
			$url   = (string) get_post_meta( $p->ID, '_jd_live_url', true );
			if ( ! $url && ! $shots ) {
				continue;
			}
			$results = array();
			foreach ( array( 1, 2 ) as $n ) {
				$v = (string) get_post_meta( $p->ID, '_jd_result_' . $n, true );
				$l = (string) get_post_meta( $p->ID, '_jd_result_' . $n . '_lbl', true );
				if ( '' !== $v ) {
					$results[] = array( $v, $l );
				}
			}
			$out[] = array(
				'slug'    => $p->post_name,
				'title'   => get_the_title( $p ),
				'client'  => (string) get_post_meta( $p->ID, '_jd_client', true ),
				'scope'   => (string) get_post_meta( $p->ID, '_jd_scope', true ),
				'summary' => wp_strip_all_tags( get_the_excerpt( $p ) ),
				'problem' => (string) get_post_meta( $p->ID, '_jd_problem', true ),
				'did'     => (string) get_post_meta( $p->ID, '_jd_solution', true ),
				'results' => $results,
				'url'     => esc_url_raw( $url ),
				'case'    => get_permalink( $p ),
				'shots'   => array_values(
					array_filter(
						array_map(
							function ( $id ) {
								$src = wp_get_attachment_image_url( $id, 'full' );
								return $src ? array( 'src' => $src, 'alt' => (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) ) : null;
							},
							$shots
						)
					)
				),
			);
		}
	}
	return apply_filters( 'jdcd_projects', $out );
}

/**
 * Render the standalone viewer. It does not load the theme, so it stays fast and can't affect it.
 */
add_action( 'template_redirect', function () {
	if ( ! get_query_var( 'jdcd' ) ) {
		return;
	}
	$projects = jdcd_projects();
	$slug     = sanitize_title( (string) get_query_var( 'jdcd_slug' ) );
	$current  = 0;
	if ( $slug ) {
		$found = false;
		foreach ( $projects as $i => $p ) {
			if ( $p['slug'] === $slug ) {
				$current = $i;
				$found   = true;
			}
		}
		if ( ! $found ) {
			wp_safe_redirect( home_url( '/' . JDCD_BASE . '/' ), 302 );
			exit;
		}
	}
	status_header( 200 );
	nocache_headers();
	header( 'Content-Type: text/html; charset=utf-8' );
	$home = home_url( '/' );
	$site = get_bloginfo( 'name' );
	include __DIR__ . '/viewer.php';
	exit;
} );

/**
 * Keep WordPress from treating /demos/ as a 404 before template_redirect runs.
 */
add_filter( 'pre_handle_404', function ( $pre ) {
	return get_query_var( 'jdcd' ) ? true : $pre;
} );

/**
 * Shortcode [jd_demo_link slug="wendy-paquette" label="Try the live demo"] for theme pages and case studies.
 */
add_shortcode( 'jd_demo_link', function ( $atts ) {
	$a   = shortcode_atts( array( 'slug' => '', 'label' => __( 'Try the interactive demo', 'jd-case-demos' ) ), $atts );
	$url = home_url( '/' . JDCD_BASE . '/' . ( $a['slug'] ? sanitize_title( $a['slug'] ) . '/' : '' ) );
	return sprintf( '<a class="jdcd-link wp-element-button" href="%1$s">%2$s</a>', esc_url( $url ), esc_html( $a['label'] ) );
} );
