<?php
/**
 * Classic meta boxes for CPT details and per-page SEO. They work inside the block editor
 * without a build step and keep the theme dependency-free.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

add_action( 'add_meta_boxes', function () {
	foreach ( jd_meta_config() as $post_type => $group ) {
		add_meta_box( 'jd_details', $group['title'], 'jd_render_meta_box', $post_type, 'normal', 'high', array( 'fields' => $group['fields'] ) );
	}
	if ( ! jd_seo_plugin_active() ) {
		foreach ( array( 'post', 'page', 'jd_project', 'jd_service' ) as $post_type ) {
			add_meta_box( 'jd_seo', __( 'SEO', 'jawad' ), 'jd_render_seo_box', $post_type, 'normal', 'low' );
		}
	}
} );

/**
 * Render the details meta box from the field config.
 */
function jd_render_meta_box( $post, $box ) {
	wp_nonce_field( 'jd_meta_save', 'jd_meta_nonce' );
	echo '<div class="jd-meta" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px 24px">';
	foreach ( $box['args']['fields'] as $key => $field ) {
		jd_render_meta_field( $post->ID, $key, $field );
	}
	echo '</div>';
}

/**
 * Render one meta field.
 */
function jd_render_meta_field( $post_id, $key, $field ) {
	list( $label, $type ) = $field;
	$help    = $field[2] ?? '';
	$choices = $field[3] ?? array();
	$value   = get_post_meta( $post_id, $key, true );
	$id      = 'jdm' . $key;
	$wide    = 'textarea' === $type ? 'grid-column:1/-1;' : '';

	printf( '<p style="margin:0;%s">', esc_attr( $wide ) );
	if ( 'checkbox' === $type ) {
		printf( '<label><input type="checkbox" name="jd_meta[%1$s]" value="1" %2$s> %3$s</label>', esc_attr( $key ), checked( (bool) $value, true, false ), esc_html( $label ) );
	} else {
		printf( '<label for="%1$s" style="display:block;font-weight:600;margin-bottom:4px">%2$s</label>', esc_attr( $id ), esc_html( $label ) );
		switch ( $type ) {
			case 'textarea':
				printf( '<textarea id="%1$s" name="jd_meta[%2$s]" rows="3" style="width:100%%">%3$s</textarea>', esc_attr( $id ), esc_attr( $key ), esc_textarea( (string) $value ) );
				break;
			case 'select':
				printf( '<select id="%1$s" name="jd_meta[%2$s]" style="width:100%%">', esc_attr( $id ), esc_attr( $key ) );
				foreach ( $choices as $opt => $opt_label ) {
					printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $opt ), selected( (string) $value, (string) $opt, false ), esc_html( $opt_label ) );
				}
				echo '</select>';
				break;
			case 'color':
				printf( '<input type="color" id="%1$s" name="jd_meta[%2$s]" value="%3$s"> <code>%3$s</code>', esc_attr( $id ), esc_attr( $key ), esc_attr( $value ? $value : '#3D5AFE' ) );
				break;
			default:
				$input = in_array( $type, array( 'url', 'number' ), true ) ? $type : 'text';
				printf( '<input type="%1$s" id="%2$s" name="jd_meta[%3$s]" value="%4$s" style="width:100%%" %5$s>', esc_attr( $input ), esc_attr( $id ), esc_attr( $key ), esc_attr( (string) $value ), 'number' === $type ? 'step="any"' : '' );
		}
	}
	if ( $help ) {
		printf( '<span class="description" style="display:block;margin-top:4px">%s</span>', esc_html( $help ) );
	}
	echo '</p>';
}

/**
 * SEO box: title, description with live length, noindex.
 */
function jd_render_seo_box( $post ) {
	wp_nonce_field( 'jd_meta_save', 'jd_meta_nonce' );
	$title   = (string) get_post_meta( $post->ID, '_jd_seo_title', true );
	$desc    = (string) get_post_meta( $post->ID, '_jd_seo_desc', true );
	$noindex = (bool) get_post_meta( $post->ID, '_jd_noindex', true );
	?>
	<p><label for="jd_seo_title" style="display:block;font-weight:600"><?php esc_html_e( 'SEO title', 'jawad' ); ?></label>
	<input type="text" id="jd_seo_title" name="jd_seo[_jd_seo_title]" value="<?php echo esc_attr( $title ); ?>" style="width:100%" placeholder="<?php echo esc_attr( get_the_title( $post ) ); ?>">
	<span class="description"><?php esc_html_e( 'Leave empty to use the post title. Aim for 50 to 60 characters.', 'jawad' ); ?></span></p>
	<p><label for="jd_seo_desc" style="display:block;font-weight:600"><?php esc_html_e( 'Meta description', 'jawad' ); ?></label>
	<textarea id="jd_seo_desc" name="jd_seo[_jd_seo_desc]" rows="3" style="width:100%" oninput="this.nextElementSibling.textContent=this.value.length+' / 160'"><?php echo esc_textarea( $desc ); ?></textarea>
	<span class="description"><?php echo esc_html( strlen( $desc ) . ' / 160' ); ?></span></p>
	<p><label><input type="checkbox" name="jd_seo[_jd_noindex]" value="1" <?php checked( $noindex ); ?>> <?php esc_html_e( 'Hide from search engines (noindex) and the sitemap', 'jawad' ); ?></label></p>
	<?php
}

add_action( 'save_post', function ( $post_id, $post ) {
	if ( ! isset( $_POST['jd_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jd_meta_nonce'] ) ), 'jd_meta_save' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$config = jd_meta_config();
	if ( isset( $config[ $post->post_type ] ) ) {
		$input = isset( $_POST['jd_meta'] ) ? (array) wp_unslash( $_POST['jd_meta'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		foreach ( $config[ $post->post_type ]['fields'] as $key => $field ) {
			$raw = $input[ $key ] ?? ( 'checkbox' === $field[1] ? '' : null );
			if ( null === $raw ) {
				continue;
			}
			$value = jd_sanitize_meta_value( $raw, $field[1] );
			if ( '' === $value || false === $value ) {
				delete_post_meta( $post_id, $key );
			} else {
				update_post_meta( $post_id, $key, $value );
			}
		}
	}

	if ( isset( $_POST['jd_seo'] ) || in_array( $post->post_type, array( 'post', 'page', 'jd_project', 'jd_service' ), true ) ) {
		$seo = isset( $_POST['jd_seo'] ) ? (array) wp_unslash( $_POST['jd_seo'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( ! jd_seo_plugin_active() ) {
			$title = sanitize_text_field( $seo['_jd_seo_title'] ?? '' );
			$desc  = sanitize_textarea_field( $seo['_jd_seo_desc'] ?? '' );
			$title ? update_post_meta( $post_id, '_jd_seo_title', $title ) : delete_post_meta( $post_id, '_jd_seo_title' );
			$desc ? update_post_meta( $post_id, '_jd_seo_desc', $desc ) : delete_post_meta( $post_id, '_jd_seo_desc' );
			empty( $seo['_jd_noindex'] ) ? delete_post_meta( $post_id, '_jd_noindex' ) : update_post_meta( $post_id, '_jd_noindex', true );
		}
	}
}, 10, 2 );
