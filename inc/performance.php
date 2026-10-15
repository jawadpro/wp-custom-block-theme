<?php
/**
 * Front-end weight reduction. Everything here is safe to remove individually.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', function () {
	// Emoji detection script and styles.
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'emoji_svg_url', '__return_false' );

	// Head noise that does nothing for visitors or search engines.
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
} );

/**
 * Only load the CSS of blocks that are actually on the page (default for block themes, made explicit).
 */
add_filter( 'should_load_separate_core_block_assets', '__return_true' );

/**
 * Skip the classic-theme compatibility stylesheet.
 */
add_action( 'wp_enqueue_scripts', function () {
	wp_dequeue_style( 'classic-theme-styles' );
}, 20 );

/**
 * Remove the DNS prefetch to s.w.org that the emoji script used.
 */
add_filter( 'wp_resource_hints', function ( $urls, $relation ) {
	if ( 'dns-prefetch' === $relation ) {
		$urls = array_filter( $urls, function ( $url ) {
			$href = is_array( $url ) ? ( $url['href'] ?? '' ) : $url;
			return false === strpos( (string) $href, 's.w.org' );
		} );
	}
	return $urls;
}, 10, 2 );

/**
 * Give the first project or featured image on a page high fetch priority (LCP).
 */
add_filter( 'wp_get_attachment_image_attributes', function ( $attr, $attachment, $size ) {
	if ( 'jd-wide' === $size && ! is_admin() ) {
		$attr['fetchpriority'] = 'high';
		unset( $attr['loading'] );
	}
	return $attr;
}, 10, 3 );

/**
 * Delay live-chat widgets (Tawk.to, Crisp, Tidio, LiveChat, Intercom) until the visitor
 * first scrolls, taps, moves the mouse or presses a key. Chat still appears, but it no longer
 * competes with the page's first paint. Toggle in Theme Options › Motion & speed.
 */
add_action( 'wp_footer', function () {
	if ( is_admin() || ! jd_opt( 'delay_chat' ) ) {
		return;
	}
	global $wp_filter;
	if ( empty( $wp_filter['wp_footer'] ) ) {
		return;
	}
	$needles   = apply_filters( 'jd_delayed_chat_callbacks', array( 'tawk', 'crisp', 'tidio', 'livechat', 'intercom' ) );
	$callbacks = array();
	foreach ( $wp_filter['wp_footer']->callbacks as $priority => $items ) {
		foreach ( $items as $id => $item ) {
			$fn   = $item['function'];
			$name = is_array( $fn ) ? ( is_object( $fn[0] ) ? get_class( $fn[0] ) : (string) $fn[0] ) . '::' . $fn[1] : ( is_string( $fn ) ? $fn : '' );
			foreach ( $needles as $needle ) {
				if ( '' !== $name && false !== stripos( $name, $needle ) ) {
					$callbacks[] = array( $priority, $fn, $item['accepted_args'] );
					remove_action( 'wp_footer', $fn, $priority );
					break;
				}
			}
		}
	}
	if ( ! $callbacks ) {
		return;
	}
	ob_start();
	foreach ( $callbacks as $cb ) {
		call_user_func( $cb[1] );
	}
	$html = (string) ob_get_clean();
	// Neutralise the scripts; the loader below turns them back on after the first interaction.
	$html = preg_replace( '#<script(?![^>]*type=["\']application/ld\+json)([^>]*)>#i', '<script type="text/plain" data-jd-delay$1>', $html );
	$html = preg_replace( '#(<script type="text/plain" data-jd-delay[^>]*?)\stype=["\'][^"\']*["\']#i', '$1', $html );
	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output of the plugin's own embed callback.
	?>
	<script>
	(function(){var done=false,ev=['scroll','pointerdown','pointermove','keydown','touchstart'];function run(){if(done)return;done=true;ev.forEach(function(e){removeEventListener(e,run,{passive:true});});document.querySelectorAll('script[data-jd-delay]').forEach(function(old){var s=document.createElement('script');for(var i=0;i<old.attributes.length;i++){var a=old.attributes[i];if(a.name!=='type'&&a.name!=='data-jd-delay')s.setAttribute(a.name,a.value);}if(!old.src)s.text=old.text;old.parentNode.replaceChild(s,old);});}ev.forEach(function(e){addEventListener(e,run,{passive:true,once:true});});setTimeout(run,15000);})();
	</script>
	<?php
}, 9 );

/**
 * Generate WebP versions of uploaded PNG and JPEG sizes (WordPress keeps the original file).
 * Typically 60-90% smaller. Toggle in Theme Options › Motion & speed.
 */
add_filter( 'image_editor_output_format', function ( $formats ) {
	if ( jd_opt( 'webp_uploads' ) ) {
		$formats['image/png']  = 'image/webp';
		$formats['image/jpeg'] = 'image/webp';
	}
	return $formats;
} );
