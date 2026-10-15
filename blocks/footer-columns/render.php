<?php
/**
 * Footer brand blurb plus link columns from the navigation menu and Theme Options.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

$jd_pages = '';
$jd_nav   = get_posts( array( 'post_type' => 'wp_navigation', 'post_status' => 'publish', 'posts_per_page' => 1, 'no_found_rows' => true ) );
if ( $jd_nav ) {
	foreach ( parse_blocks( $jd_nav[0]->post_content ) as $jd_b ) {
		if ( 'core/navigation-link' === $jd_b['blockName'] && ! empty( $jd_b['attrs']['label'] ) ) {
			$jd_pages .= sprintf( '<a href="%1$s">%2$s</a>', esc_url( $jd_b['attrs']['url'] ?? '#' ), esc_html( wp_strip_all_tags( $jd_b['attrs']['label'] ) ) );
		}
	}
}

$jd_social = '';
foreach ( array( 'linkedin_url' => 'LinkedIn', 'github_url' => 'GitHub', 'upwork_url' => 'Upwork', 'x_url' => 'X' ) as $jd_key => $jd_label ) {
	if ( jd_opt( $jd_key ) ) {
		$jd_social .= sprintf( '<a href="%1$s" rel="me noopener" target="_blank">%2$s</a>', esc_url( jd_opt( $jd_key ) ), esc_html( $jd_label ) );
	}
}
$jd_blog = (int) get_option( 'page_for_posts' );
if ( $jd_blog ) {
	$jd_social .= sprintf( '<a href="%1$s">%2$s</a>', esc_url( get_permalink( $jd_blog ) ), esc_html( get_the_title( $jd_blog ) ) );
}

$jd_contact = '';
if ( jd_opt( 'email' ) ) {
	$jd_contact .= sprintf( '<a href="mailto:%1$s">%2$s</a>', esc_attr( jd_opt( 'email' ) ), esc_html( jd_opt( 'email' ) ) );
}
if ( jd_opt( 'phone' ) ) {
	$jd_contact .= '<span>' . esc_html( jd_opt( 'phone' ) ) . '</span>';
}
$jd_contact .= '<span class="jd-muted">' . esc_html( jd_opt( 'response_text' ) ) . '</span>';

$jd_brand = render_block( array( 'blockName' => 'jd/brand', 'attrs' => array(), 'innerBlocks' => array(), 'innerHTML' => '', 'innerContent' => array() ) );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'jd-fgrid' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="jd-fgrid__brand">
		<?php echo $jd_brand; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<p class="jd-muted"><?php echo esc_html( jd_opt( 'footer_blurb' ) ); ?></p>
	</div>
	<div class="jd-fcols">
		<?php if ( $jd_pages ) : ?>
			<nav aria-label="<?php esc_attr_e( 'Footer', 'jawad' ); ?>"><p class="jd-label"><?php esc_html_e( 'Pages', 'jawad' ); ?></p><?php echo $jd_pages; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></nav>
		<?php endif; ?>
		<?php if ( $jd_social ) : ?>
			<div><p class="jd-label"><?php esc_html_e( 'Elsewhere', 'jawad' ); ?></p><?php echo $jd_social; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		<?php endif; ?>
		<div><p class="jd-label"><?php esc_html_e( 'Contact', 'jawad' ); ?></p><?php echo $jd_contact; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
	</div>
</div>
