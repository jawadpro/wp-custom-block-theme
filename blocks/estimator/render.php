<?php
/**
 * Project cost estimator. Visitors pick a project type, size, design, extras and timing and
 * see a price range update live. The button carries the estimate into the quote form.
 * Without JavaScript the options still show, with starting prices and a link to the form.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

$jd_cfg = jd_estimator_config();
$jd_uid = wp_unique_id( 'jde' );
$jd_cur = $attributes['currency'] ? $attributes['currency'] : '$';

$jd_contact = get_page_by_path( 'contact' );
$jd_quote   = $jd_contact ? get_permalink( $jd_contact ) : home_url( '/contact/' );

$jd_money = function ( $n ) use ( $jd_cur ) {
	return $jd_cur . number_format_i18n( (float) $n );
};

$jd_choices = function ( $name, $items, $type, $checked, $uid, $note = null ) {
	$out = '';
	foreach ( $items as $key => $item ) {
		$id    = $uid . '-' . $name . '-' . $key;
		$extra = $note ? $note( $item ) : '';
		$out  .= sprintf(
			'<label class="jd-opt" for="%1$s"><input type="%2$s" id="%1$s" name="%3$s" value="%4$s"%5$s><span>%6$s%7$s</span></label>',
			esc_attr( $id ),
			esc_attr( $type ),
			esc_attr( $name ),
			esc_attr( $key ),
			$key === $checked ? ' checked' : '',
			esc_html( $item['label'] ),
			$extra ? '<small>' . esc_html( $extra ) . '</small>' : ''
		);
	}
	return $out;
};
$jd_from = function ( $item ) use ( $jd_money ) {
	return sprintf( /* translators: %s: price */ __( 'from %s', 'jawad' ), $jd_money( $item['min'] ) );
};
$jd_plus = function ( $item ) use ( $jd_money ) {
	return '+' . $jd_money( $item['min'] ) . '–' . number_format_i18n( (float) $item['max'] );
};
$jd_month = function ( $item ) use ( $jd_money ) {
	return $item['monthly'] ? $jd_money( $item['monthly'] ) . __( '/mo', 'jawad' ) : '';
};
$jd_first = array_key_first( $jd_cfg['types'] );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'jd-est' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-config="<?php echo esc_attr( wp_json_encode( $jd_cfg ) ); ?>" data-currency="<?php echo esc_attr( $jd_cur ); ?>" data-quote="<?php echo esc_url( $jd_quote ); ?>">
	<form class="jd-est__form" action="<?php echo esc_url( $jd_quote ); ?>" method="get">
		<fieldset class="jd-est__q">
			<legend class="jd-form__title"><span>01</span><?php esc_html_e( 'What are we building?', 'jawad' ); ?></legend>
			<div class="jd-opts"><?php echo $jd_choices( 'type', $jd_cfg['types'], 'radio', $jd_first, $jd_uid, $jd_from ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		</fieldset>
		<fieldset class="jd-est__q" data-when="sized">
			<legend class="jd-form__title"><span>02</span><?php esc_html_e( 'How big is it?', 'jawad' ); ?></legend>
			<div class="jd-opts"><?php echo $jd_choices( 'size', $jd_cfg['sizes'], 'radio', 'small', $jd_uid ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		</fieldset>
		<fieldset class="jd-est__q" data-when="designed">
			<legend class="jd-form__title"><span>03</span><?php esc_html_e( 'Design', 'jawad' ); ?></legend>
			<div class="jd-opts"><?php echo $jd_choices( 'design', $jd_cfg['designs'], 'radio', 'figma', $jd_uid ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		</fieldset>
		<fieldset class="jd-est__q">
			<legend class="jd-form__title"><span>04</span><?php esc_html_e( 'Anything extra?', 'jawad' ); ?></legend>
			<div class="jd-opts"><?php echo $jd_choices( 'extras[]', $jd_cfg['extras'], 'checkbox', '', $jd_uid, $jd_plus ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		</fieldset>
		<fieldset class="jd-est__q">
			<legend class="jd-form__title"><span>05</span><?php esc_html_e( 'When do you need it?', 'jawad' ); ?></legend>
			<div class="jd-opts"><?php echo $jd_choices( 'pace', $jd_cfg['paces'], 'radio', 'standard', $jd_uid ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		</fieldset>
		<fieldset class="jd-est__q">
			<legend class="jd-form__title"><span>06</span><?php esc_html_e( 'Looking after it afterwards', 'jawad' ); ?></legend>
			<div class="jd-opts"><?php echo $jd_choices( 'care', $jd_cfg['care'], 'radio', 'none', $jd_uid, $jd_month ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		</fieldset>
	</form>
	<aside class="jd-est__result" aria-live="polite">
		<p class="jd-label"><?php esc_html_e( 'Your estimate', 'jawad' ); ?></p>
		<p class="jd-est__price"><?php echo esc_html( $jd_from( $jd_cfg['types'][ $jd_first ] ) ); ?></p>
		<p class="jd-est__time jd-muted"></p>
		<p class="jd-est__care jd-muted" hidden></p>
		<p class="jd-est__promo" hidden></p>
		<ul class="jd-est__list jd-ticks"></ul>
		<a class="jd-btn jd-btn--primary jd-mag jd-est__cta" href="<?php echo esc_url( $jd_quote ); ?>"><?php esc_html_e( 'Turn this into a fixed quote', 'jawad' ); ?></a>
		<p class="jd-est__note jd-muted"><?php esc_html_e( 'A guide, not a bill. You get a written fixed price within 24 hours, and it never goes up once agreed.', 'jawad' ); ?></p>
	</aside>
</div>
