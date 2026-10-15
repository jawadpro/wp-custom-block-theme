<?php
/**
 * Burger toggle and full-screen overlay menu.
 * Links come from the newest published Navigation menu (edited in Site Editor > Navigation),
 * so the desktop Navigation block and this overlay always match.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

$jd_links = array();
$jd_nav   = get_posts(
	array(
		'post_type'      => 'wp_navigation',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'no_found_rows'  => true,
	)
);
if ( $jd_nav ) {
	$jd_walk = function ( $blocks ) use ( &$jd_walk, &$jd_links ) {
		foreach ( $blocks as $b ) {
			if ( in_array( $b['blockName'], array( 'core/navigation-link', 'core/navigation-submenu' ), true ) && ! empty( $b['attrs']['label'] ) ) {
				$jd_links[] = array(
					'label' => wp_strip_all_tags( $b['attrs']['label'] ),
					'url'   => $b['attrs']['url'] ?? ( ! empty( $b['attrs']['id'] ) ? get_permalink( (int) $b['attrs']['id'] ) : '#' ),
				);
			}
			if ( ! empty( $b['innerBlocks'] ) && 'core/navigation-submenu' !== $b['blockName'] ) {
				$jd_walk( $b['innerBlocks'] );
			}
		}
	};
	$jd_walk( parse_blocks( $jd_nav[0]->post_content ) );
}
if ( ! $jd_links ) {
	$jd_links[] = array( 'label' => __( 'Home', 'jawad' ), 'url' => home_url( '/' ) );
	foreach ( get_pages( array( 'sort_column' => 'menu_order', 'number' => 6, 'parent' => 0 ) ) as $jd_page ) {
		$jd_links[] = array( 'label' => $jd_page->post_title, 'url' => get_permalink( $jd_page ) );
	}
}

$jd_current = untrailingslashit( (string) strtok( isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/', '?' ) );
$jd_email   = (string) jd_opt( 'email' );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'jd-menu' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<button class="jd-burger jd-mag" type="button" aria-expanded="false" aria-controls="jd-overlay" aria-label="<?php esc_attr_e( 'Open menu', 'jawad' ); ?>"><span></span><span></span></button>
	<div class="jd-overlay" id="jd-overlay" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Site menu', 'jawad' ); ?>" hidden>
		<div class="jd-overlay__inner">
			<nav class="jd-overlay__nav" aria-label="<?php esc_attr_e( 'Menu', 'jawad' ); ?>">
				<?php foreach ( $jd_links as $jd_i => $jd_link ) : ?>
					<?php $jd_is = untrailingslashit( (string) wp_parse_url( $jd_link['url'], PHP_URL_PATH ) ) === $jd_current; ?>
					<a href="<?php echo esc_url( $jd_link['url'] ); ?>"<?php echo $jd_is ? ' aria-current="page"' : ''; ?>><span class="jd-overlay__k"><?php echo esc_html( sprintf( '%02d', $jd_i + 1 ) ); ?></span><span class="jd-overlay__t"><?php echo esc_html( $jd_link['label'] ); ?></span></a>
				<?php endforeach; ?>
			</nav>
			<aside class="jd-overlay__aside">
				<?php if ( jd_opt( 'audit_on' ) ) : ?>
					<div class="jd-promo">
						<p class="jd-label"><?php echo esc_html( jd_opt( 'audit_label' ) ); ?></p>
						<p class="jd-promo__title"><?php echo esc_html( jd_opt( 'audit_title' ) ); ?></p>
						<p><?php echo esc_html( jd_opt( 'audit_text' ) ); ?></p>
						<a class="jd-btn jd-btn--light" href="<?php echo esc_url( jd_opt( 'audit_url' ) ); ?>"><?php echo esc_html( jd_opt( 'audit_cta' ) ); ?></a>
					</div>
				<?php endif; ?>
				<?php if ( jd_opt( 'availability_on' ) && jd_opt( 'availability_text' ) ) : ?>
					<p class="jd-badge"><span class="jd-dot"></span><?php echo esc_html( jd_opt( 'availability_text' ) ); ?></p>
				<?php endif; ?>
				<p class="jd-muted jd-overlay__meta">
					<?php if ( $jd_email ) : ?>
						<span><?php echo esc_html( $jd_email ); ?></span><br>
					<?php endif; ?>
					<?php if ( jd_opt( 'hours_text' ) ) : ?><span><?php echo esc_html( jd_opt( 'hours_text' ) ); ?></span><?php endif; ?>
				</p>
				<p class="jd-overlay__social">
					<?php
					foreach ( array( 'linkedin_url' => 'LinkedIn', 'github_url' => 'GitHub', 'upwork_url' => 'Upwork', 'x_url' => 'X' ) as $jd_key => $jd_label ) {
						if ( jd_opt( $jd_key ) ) {
							printf( '<a href="%1$s" rel="me noopener" target="_blank">%2$s</a>', esc_url( jd_opt( $jd_key ) ), esc_html( $jd_label ) );
						}
					}
					?>
				</p>
			</aside>
		</div>
	</div>
</div>
