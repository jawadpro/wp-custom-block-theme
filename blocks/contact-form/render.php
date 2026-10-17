<?php
/**
 * Three-step quote form. Without JavaScript every step shows at once and the form posts normally.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$jd_state = isset( $_GET['jd_sent'] ) ? sanitize_key( wp_unslash( $_GET['jd_sent'] ) ) : '';
$jd_uid   = wp_unique_id( 'jdf' );

$jd_types   = array( 'New website', 'Online store', 'Web app', 'AI or automation', 'Landing page', 'Speed or fix', 'Care plan', 'Something else' );
$jd_budgets = array( 'Under $1,500', '$1,500–3,500', '$3,500–6,000', '$6,000–12,000', '$12,000+' );
$jd_whens   = array( 'ASAP', 'This month', 'Flexible' );

$jd_radio = function ( $name, $choices, $checked, $uid ) {
	$out = '';
	foreach ( $choices as $i => $choice ) {
		$id   = $uid . '-' . $name . '-' . $i;
		$out .= sprintf(
			'<label class="jd-opt" for="%1$s"><input type="radio" id="%1$s" name="%2$s" value="%3$s"%4$s><span>%5$s</span></label>',
			esc_attr( $id ),
			esc_attr( $name ),
			esc_attr( $choice ),
			$i === $checked ? ' checked' : '',
			esc_html( $choice )
		);
	}
	return $out;
};
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'jd-form-wrap' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( 'ok' === $jd_state ) : ?>
		<div class="jd-form jd-form--done" role="status">
			<p class="jd-form__title"><?php esc_html_e( 'Request received', 'jawad' ); ?></p>
			<p class="jd-muted"><?php echo esc_html( jd_opt( 'form_success' ) ); ?></p>
		</div>
	<?php else : ?>
		<form class="jd-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
			<input type="hidden" name="action" value="jd_contact">
			<input type="hidden" name="jd_ts" value="<?php echo esc_attr( jd_form_stamp() ); ?>">
			<input type="hidden" name="jd_js" value="">
			<input type="hidden" name="jd_ref" value="<?php echo esc_attr( get_permalink() ? get_permalink() : home_url( '/' ) ); ?>">
			<?php wp_nonce_field( 'jd_contact', 'jd_nonce' ); ?>
			<div class="jd-hp" aria-hidden="true"><label for="<?php echo esc_attr( $jd_uid ); ?>-hp">Website</label><input type="text" id="<?php echo esc_attr( $jd_uid ); ?>-hp" name="jd_hp" tabindex="-1" autocomplete="off"><label for="<?php echo esc_attr( $jd_uid ); ?>-hp2">Company email</label><input type="email" id="<?php echo esc_attr( $jd_uid ); ?>-hp2" name="jd_hp2" tabindex="-1" autocomplete="off"></div>

			<div class="jd-prog" aria-hidden="true"><i class="is-on"></i><i></i><i></i></div>

			<fieldset class="jd-step is-on" data-step="1">
				<legend class="jd-form__title"><?php esc_html_e( 'What do you need?', 'jawad' ); ?></legend>
				<div class="jd-opts"><?php echo $jd_radio( 'type', $jd_types, 0, $jd_uid ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			</fieldset>

			<fieldset class="jd-step" data-step="2">
				<legend class="jd-form__title"><?php esc_html_e( 'Budget and timing', 'jawad' ); ?></legend>
				<p class="jd-label"><?php esc_html_e( 'Budget', 'jawad' ); ?></p>
				<div class="jd-opts"><?php echo $jd_radio( 'budget', $jd_budgets, 1, $jd_uid ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<p class="jd-label"><?php esc_html_e( 'When', 'jawad' ); ?></p>
				<div class="jd-opts"><?php echo $jd_radio( 'when', $jd_whens, 1, $jd_uid ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			</fieldset>

			<fieldset class="jd-step" data-step="3">
				<legend class="jd-form__title"><?php esc_html_e( 'Where can I reach you?', 'jawad' ); ?></legend>
				<p class="jd-field"><label for="<?php echo esc_attr( $jd_uid ); ?>-name"><?php esc_html_e( 'Your name', 'jawad' ); ?></label><input id="<?php echo esc_attr( $jd_uid ); ?>-name" name="name" autocomplete="name" required maxlength="120"></p>
				<p class="jd-field"><label for="<?php echo esc_attr( $jd_uid ); ?>-email"><?php esc_html_e( 'Email', 'jawad' ); ?></label><input id="<?php echo esc_attr( $jd_uid ); ?>-email" name="email" type="email" autocomplete="email" required maxlength="190"></p>
				<p class="jd-field"><label for="<?php echo esc_attr( $jd_uid ); ?>-url"><?php esc_html_e( 'Current website (optional)', 'jawad' ); ?></label><input id="<?php echo esc_attr( $jd_uid ); ?>-url" name="website" type="url" inputmode="url" placeholder="https://" maxlength="300"></p>
				<p class="jd-field"><label for="<?php echo esc_attr( $jd_uid ); ?>-details"><?php esc_html_e( 'What should I know?', 'jawad' ); ?></label><textarea id="<?php echo esc_attr( $jd_uid ); ?>-details" name="details" rows="4" maxlength="5000"></textarea></p>
			</fieldset>

			<p class="jd-err" role="alert"><?php echo 'err' === $jd_state ? esc_html__( 'Please add your name and a valid email, then send again.', 'jawad' ) : ''; ?></p>
			<div class="jd-form__nav">
				<button type="button" class="jd-btn jd-btn--line jd-form__back" hidden><?php esc_html_e( 'Back', 'jawad' ); ?></button>
				<?php if ( jd_opt( 'turnstile_site' ) && jd_opt( 'turnstile_secret' ) ) : ?>
					<?php wp_enqueue_script( 'cf-turnstile', 'https://challenges.cloudflare.com/turnstile/v0/api.js', array(), null, array( 'in_footer' => true, 'strategy' => 'defer' ) ); ?>
					<div class="cf-turnstile jd-captcha" data-sitekey="<?php echo esc_attr( jd_opt( 'turnstile_site' ) ); ?>" data-theme="dark" data-size="flexible"></div>
				<?php endif; ?>
				<button type="submit" class="jd-btn jd-btn--primary jd-form__next"><?php esc_html_e( 'Send request', 'jawad' ); ?></button>
			</div>
		</form>
	<?php endif; ?>
</div>
