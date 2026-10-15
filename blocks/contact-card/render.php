<?php
/**
 * Contact side panel: email with copy, booking link, next steps.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

$jd_email    = (string) jd_opt( 'email' );
$jd_calendly = (string) jd_opt( 'calendly_url' );
$jd_phone    = (string) jd_opt( 'phone' );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'jd-side' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( $jd_email ) : ?>
		<div class="jd-box">
			<p class="jd-label"><?php esc_html_e( 'Email', 'jawad' ); ?></p>
			<p class="jd-copy"><a class="jd-copy__text" href="mailto:<?php echo esc_attr( $jd_email ); ?>"><?php echo esc_html( $jd_email ); ?></a><button type="button" class="jd-btn jd-btn--line jd-btn--sm" data-copy="<?php echo esc_attr( $jd_email ); ?>"><?php esc_html_e( 'Copy', 'jawad' ); ?></button></p>
			<?php if ( $jd_phone ) : ?>
				<p class="jd-muted"><?php echo esc_html( $jd_phone ); ?></p>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<?php if ( $jd_calendly ) : ?>
		<div class="jd-box">
			<p class="jd-label"><?php esc_html_e( 'Prefer to talk?', 'jawad' ); ?></p>
			<p><?php esc_html_e( 'Pick a 20-minute slot that suits you. No slideshow, just your project.', 'jawad' ); ?></p>
			<a class="jd-btn jd-btn--primary" href="<?php echo esc_url( $jd_calendly ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Book a call', 'jawad' ); ?> <?php echo jd_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
		</div>
	<?php endif; ?>
	<div class="jd-box">
		<p class="jd-label"><?php esc_html_e( 'What happens next', 'jawad' ); ?></p>
		<ol class="jd-next">
			<li><?php echo esc_html( jd_opt( 'response_text' ) ); ?></li>
			<li><?php esc_html_e( 'A quick call, about 20 minutes', 'jawad' ); ?></li>
			<li><?php esc_html_e( 'A written fixed quote', 'jawad' ); ?></li>
		</ol>
	</div>
</div>
