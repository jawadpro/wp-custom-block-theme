<?php
/**
 * Contact form handler: validates, rate-limits, saves a private Lead and emails it.
 * Works as a normal POST (redirect) and as fetch() from main.js (JSON).
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_post_nopriv_jd_contact', 'jd_handle_contact' );
add_action( 'admin_post_jd_contact', 'jd_handle_contact' );
add_action( 'admin_post_nopriv_jd_nonce', 'jd_fresh_form_nonce' );
add_action( 'admin_post_jd_nonce', 'jd_fresh_form_nonce' );

/**
 * Signed render time for the form, so bots can't fake or drop the timing check.
 */
function jd_form_stamp() {
	$t = (string) time();
	return $t . '.' . substr( wp_hash( 'jd_form|' . $t ), 0, 12 );
}

/**
 * Does this submission look automated? Checked before anything is saved or sent.
 *
 * @param string $details Message text.
 * @param string $name    Sender name.
 */
function jd_looks_like_bot( $details, $name ) {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- nonce is verified by the caller.
	$hp    = isset( $_POST['jd_hp'] ) ? (string) wp_unslash( $_POST['jd_hp'] ) : '';
	$hp2   = isset( $_POST['jd_hp2'] ) ? (string) wp_unslash( $_POST['jd_hp2'] ) : '';
	$stamp = isset( $_POST['jd_ts'] ) ? sanitize_text_field( wp_unslash( $_POST['jd_ts'] ) ) : '';
	$js    = isset( $_POST['jd_js'] ) ? sanitize_text_field( wp_unslash( $_POST['jd_js'] ) ) : '';
	// phpcs:enable
	if ( '' !== $hp || '' !== $hp2 ) {
		return true;
	}
	// The timestamp must be ours and at least 4 seconds old: people need time to fill three steps.
	$parts = explode( '.', $stamp );
	if ( 2 !== count( $parts ) || ! hash_equals( substr( wp_hash( 'jd_form|' . $parts[0] ), 0, 12 ), $parts[1] ) || time() - (int) $parts[0] < 4 ) {
		return true;
	}
	// main.js fills this after a real keypress, tap or click inside the form.
	if ( strrev( $stamp ) !== $js ) {
		return true;
	}
	// Link spam: real briefs rarely carry more than a couple of links, and names never do.
	if ( preg_match_all( '#https?://|www\.#i', $details ) > 3 || preg_match( '#https?://|www\.|<a\s#i', $name ) ) {
		return true;
	}
	return false;
}

/**
 * Verify a Cloudflare Turnstile token when both keys are set. Returns true when the check passes or is off.
 */
function jd_turnstile_ok() {
	$secret = (string) jd_opt( 'turnstile_secret' );
	if ( '' === $secret || '' === (string) jd_opt( 'turnstile_site' ) ) {
		return true;
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce is verified by the caller.
	$token = isset( $_POST['cf-turnstile-response'] ) ? sanitize_text_field( wp_unslash( $_POST['cf-turnstile-response'] ) ) : '';
	if ( '' === $token ) {
		return false;
	}
	$res = wp_remote_post(
		'https://challenges.cloudflare.com/turnstile/v0/siteverify',
		array(
			'timeout' => 8,
			'body'    => array(
				'secret'   => $secret,
				'response' => $token,
				'remoteip' => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '',
			),
		)
	);
	if ( is_wp_error( $res ) ) {
		return true; // Don't lose a real lead because Cloudflare was unreachable.
	}
	$body = json_decode( wp_remote_retrieve_body( $res ), true );
	return ! empty( $body['success'] );
}

/**
 * Return a fresh form nonce, so pages served from the page cache can still submit.
 */
function jd_fresh_form_nonce() {
	nocache_headers();
	wp_send_json_success( array( 'nonce' => wp_create_nonce( 'jd_contact' ) ) );
}

/**
 * Handle a contact submission.
 */
function jd_handle_contact() {
	$is_ajax = isset( $_SERVER['HTTP_ACCEPT'] ) && false !== strpos( sanitize_text_field( wp_unslash( $_SERVER['HTTP_ACCEPT'] ) ), 'application/json' );
	$ref     = isset( $_POST['jd_ref'] ) ? esc_url_raw( wp_unslash( $_POST['jd_ref'] ) ) : home_url( '/' );
	$ref     = wp_validate_redirect( $ref, home_url( '/' ) );

	$fail = function ( $message, $code = 400 ) use ( $is_ajax, $ref ) {
		if ( $is_ajax ) {
			wp_send_json_error( array( 'message' => $message ), $code );
		}
		wp_safe_redirect( add_query_arg( 'jd_sent', 'err', $ref ) . '#contact' );
		exit;
	};

	if ( ! isset( $_POST['jd_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jd_nonce'] ) ), 'jd_contact' ) ) {
		$fail( __( 'This form expired. Reload the page and try again.', 'jawad' ), 403 );
	}


	// Simple rate limit: 5 submissions per hour per IP.
	$ip_raw = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$ip_key = 'jd_rl_' . substr( wp_hash( $ip_raw ), 0, 20 );
	$hits   = (int) get_transient( $ip_key );
	if ( $hits >= 5 ) {
		$fail( __( 'Too many requests. Please email me directly instead.', 'jawad' ), 429 );
	}

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$website = isset( $_POST['website'] ) ? esc_url_raw( wp_unslash( $_POST['website'] ) ) : '';
	$details = isset( $_POST['details'] ) ? sanitize_textarea_field( wp_unslash( $_POST['details'] ) ) : '';
	$type    = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
	$budget  = isset( $_POST['budget'] ) ? sanitize_text_field( wp_unslash( $_POST['budget'] ) ) : '';
	$when    = isset( $_POST['when'] ) ? sanitize_text_field( wp_unslash( $_POST['when'] ) ) : '';

	if ( '' === $name || ! is_email( $email ) ) {
		$fail( __( 'Please add your name and a valid email.', 'jawad' ), 422 );
	}

	// Spam traps. Bots get a normal-looking success so they learn nothing, and nothing is saved or sent.
	if ( jd_looks_like_bot( $details, $name ) ) {
		if ( $is_ajax ) {
			wp_send_json_success( array( 'message' => jd_opt( 'form_success' ) ) );
		}
		wp_safe_redirect( add_query_arg( 'jd_sent', 'ok', $ref ) . '#contact' );
		exit;
	}
	if ( ! jd_turnstile_ok() ) {
		$fail( __( 'Please complete the human check and send again.', 'jawad' ), 403 );
	}

	set_transient( $ip_key, $hits + 1, HOUR_IN_SECONDS );

	// Hand the entry to Gravity Forms when configured, so its entries, notifications and SMTP apply.
	$gf_id = (int) jd_opt( 'gf_form_id' );
	if ( $gf_id && class_exists( 'GFAPI' ) ) {
		$sent = jd_submit_to_gravity_forms(
			$gf_id,
			array(
				'name'     => $name,
				'email'    => $email,
				'website'  => $website,
				'service'  => $type,
				'budget'   => $budget,
				'timeline' => $when,
				'details'  => $details,
			)
		);
		if ( true === $sent ) {
			if ( $is_ajax ) {
				wp_send_json_success( array( 'message' => jd_opt( 'form_success' ) ) );
			}
			wp_safe_redirect( add_query_arg( 'jd_sent', 'ok', $ref ) . '#contact' );
			exit;
		}
		// Fall through to the built-in handler if Gravity Forms rejected the entry, so no lead is lost.
	}

	$lead_id = wp_insert_post(
		array(
			'post_type'    => 'jd_lead',
			'post_status'  => 'private',
			'post_title'   => sprintf( '%s · %s', $name, $type ? $type : __( 'Enquiry', 'jawad' ) ),
			'post_content' => $details,
		),
		true
	);
	if ( ! is_wp_error( $lead_id ) ) {
		update_post_meta( $lead_id, '_jd_email', $email );
		update_post_meta( $lead_id, '_jd_type', $type );
		update_post_meta( $lead_id, '_jd_budget', $budget );
		update_post_meta( $lead_id, '_jd_when', $when );
		update_post_meta( $lead_id, '_jd_website', $website );
	}

	$to      = jd_opt( 'notify_email' ) ? jd_opt( 'notify_email' ) : get_option( 'admin_email' );
	$subject = sprintf( /* translators: 1: name 2: project type */ __( 'New enquiry: %1$s (%2$s)', 'jawad' ), $name, $type );
	$body    = implode(
		"\n",
		array(
			__( 'Name', 'jawad' ) . ': ' . $name,
			__( 'Email', 'jawad' ) . ': ' . $email,
			__( 'Project', 'jawad' ) . ': ' . $type,
			__( 'Budget', 'jawad' ) . ': ' . $budget,
			__( 'When', 'jawad' ) . ': ' . $when,
			__( 'Website', 'jawad' ) . ': ' . $website,
			'',
			$details,
			'',
			is_wp_error( $lead_id ) ? '' : admin_url( 'post.php?post=' . $lead_id . '&action=edit' ),
		)
	);
	wp_mail( $to, $subject, $body, array( 'Reply-To: ' . $name . ' <' . $email . '>' ) );

	if ( $is_ajax ) {
		wp_send_json_success( array( 'message' => jd_opt( 'form_success' ) ) );
	}
	wp_safe_redirect( add_query_arg( 'jd_sent', 'ok', $ref ) . '#contact' );
	exit;
}

/**
 * Submit values into a Gravity Forms form, matching fields by label or parameter name.
 * Returns true on success, or a WP_Error describing why it failed.
 */
function jd_submit_to_gravity_forms( $form_id, $values ) {
	$form = GFAPI::get_form( $form_id );
	if ( ! $form || empty( $form['fields'] ) ) {
		return new WP_Error( 'jd_gf_missing', 'Form not found' );
	}
	$keys = array(
		'name'     => array( 'name', 'your name', 'full name' ),
		'email'    => array( 'email', 'email address', 'your email' ),
		'website'  => array( 'website', 'current website', 'url', 'site' ),
		'service'  => array( 'service', 'project type', 'project', 'type' ),
		'budget'   => array( 'budget', 'budget range' ),
		'timeline' => array( 'timeline', 'when', 'timing' ),
		'details'  => array( 'details', 'project details', 'message', 'what should i know?' ),
	);
	$input = array();
	foreach ( $form['fields'] as $field ) {
		$label = strtolower( trim( (string) $field->label ) );
		$param = strtolower( trim( (string) $field->inputName ) );
		foreach ( $keys as $key => $aliases ) {
			if ( in_array( $label, $aliases, true ) || in_array( $param, $aliases, true ) ) {
				if ( 'name' === $field->type && is_array( $field->inputs ) ) {
					$parts = preg_split( '/\s+/', $values['name'], 2 );
					foreach ( $field->inputs as $sub ) {
						$suffix = substr( (string) $sub['id'], strpos( (string) $sub['id'], '.' ) + 1 );
						if ( '3' === $suffix ) {
							$input[ 'input_' . str_replace( '.', '_', $sub['id'] ) ] = $parts[0];
						} elseif ( '6' === $suffix ) {
							$input[ 'input_' . str_replace( '.', '_', $sub['id'] ) ] = $parts[1] ?? '';
						}
					}
				} else {
					$input[ 'input_' . $field->id ] = $values[ $key ];
				}
				break;
			}
		}
	}
	$result = GFAPI::submit_form( $form_id, $input );
	if ( is_wp_error( $result ) ) {
		return $result;
	}
	return ! empty( $result['is_valid'] ) ? true : new WP_Error( 'jd_gf_invalid', 'Gravity Forms validation failed', $result['validation_messages'] ?? array() );
}
