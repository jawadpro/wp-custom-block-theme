<?php
/**
 * Custom post types, taxonomies and registered meta.
 *
 * Work (jd_project)      public, archive at /work/, singles are case studies.
 * Services (jd_service)  public, archive at /services/, singles are landing pages per service.
 * Pricing (jd_plan)      admin-only, shown by the Pricing block.
 * Testimonials           admin-only, shown by the Testimonials block.
 * FAQs (jd_faq)          admin-only, shown by the FAQ block with FAQPage schema.
 * Leads (jd_lead)        private, created by the contact form.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

/**
 * Meta field definitions: key => [ label, type, help, choices ].
 * Types: text, textarea, url, number, checkbox, color, select.
 */
function jd_meta_config() {
	return array(
		'jd_project'     => array(
			'title'  => __( 'Project details', 'jawad' ),
			'fields' => array(
				'_jd_client'        => array( __( 'Client', 'jawad' ), 'text', __( 'Business name, and city if you like.', 'jawad' ) ),
				'_jd_scope'         => array( __( 'Scope', 'jawad' ), 'text', __( 'e.g. WooCommerce · Speed', 'jawad' ) ),
				'_jd_timeline'      => array( __( 'Timeline', 'jawad' ), 'text', __( 'e.g. 3 weeks', 'jawad' ) ),
				'_jd_live_url'      => array( __( 'Live site URL', 'jawad' ), 'url', '' ),
				'_jd_tag'           => array( __( 'Card label', 'jawad' ), 'text', __( 'Short label on the card, e.g. Client · Live or Concept.', 'jawad' ) ),
				'_jd_featured'      => array( __( 'Show on home page', 'jawad' ), 'checkbox', '' ),
				'_jd_problem'       => array( __( 'The problem', 'jawad' ), 'textarea', '' ),
				'_jd_solution'      => array( __( 'What I did', 'jawad' ), 'textarea', '' ),
				'_jd_result_1'      => array( __( 'Result 1 value', 'jawad' ), 'text', __( 'e.g. 96', 'jawad' ) ),
				'_jd_result_1_lbl'  => array( __( 'Result 1 label', 'jawad' ), 'text', __( 'e.g. mobile PageSpeed', 'jawad' ) ),
				'_jd_result_2'      => array( __( 'Result 2 value', 'jawad' ), 'text', '' ),
				'_jd_result_2_lbl'  => array( __( 'Result 2 label', 'jawad' ), 'text', '' ),
				'_jd_quote'         => array( __( 'Client quote', 'jawad' ), 'textarea', '' ),
				'_jd_quote_name'    => array( __( 'Quote author', 'jawad' ), 'text', '' ),
				'_jd_quote_role'    => array( __( 'Quote author role', 'jawad' ), 'text', '' ),
				'_jd_card_bg'       => array( __( 'Card background color', 'jawad' ), 'color', __( 'Used behind the screenshot, or for the mock frame when no featured image is set.', 'jawad' ) ),
				'_jd_card_ink'      => array( __( 'Mock frame ink color', 'jawad' ), 'color', '' ),
				'_jd_card_accent'   => array( __( 'Mock frame accent color', 'jawad' ), 'color', '' ),
				'_jd_card_dark'     => array( __( 'Dark mock frame', 'jawad' ), 'checkbox', '' ),
				'_jd_card_variant'  => array( __( 'Mock layout', 'jawad' ), 'select', '', array( '0' => 'Landing', '1' => 'Split', '2' => 'Shop grid', '3' => 'Service site' ) ),
			),
		),
		'jd_service'     => array(
			'title'  => __( 'Service details', 'jawad' ),
			'fields' => array(
				'_jd_card_title' => array( __( 'Card headline', 'jawad' ), 'text', __( 'Big headline in the service stack, e.g. A checkout people finish.', 'jawad' ) ),
				'_jd_price'      => array( __( 'Price', 'jawad' ), 'text', __( 'e.g. $1,500–3,500 or from $150', 'jawad' ) ),
				'_jd_price_from' => array( __( 'Lowest price (number, for schema)', 'jawad' ), 'number', '' ),
				'_jd_timeline'   => array( __( 'Timeline', 'jawad' ), 'text', '' ),
				'_jd_features'   => array( __( 'What is included (one per line)', 'jawad' ), 'textarea', '' ),
				'_jd_before'     => array( __( 'Before score (optional)', 'jawad' ), 'text', '' ),
				'_jd_after'      => array( __( 'After score (optional)', 'jawad' ), 'text', '' ),
				'_jd_cta_label'  => array( __( 'Button label', 'jawad' ), 'text', '' ),
			),
		),
		'jd_plan'        => array(
			'title'  => __( 'Plan details', 'jawad' ),
			'fields' => array(
				'_jd_group'      => array( __( 'Plan group', 'jawad' ), 'select', '', array( 'project' => 'Project packages', 'care' => 'Care plans' ) ),
				'_jd_price_main' => array( __( 'Price', 'jawad' ), 'text', __( 'e.g. $1.5k or $149', 'jawad' ) ),
				'_jd_price_sub'  => array( __( 'Price suffix', 'jawad' ), 'text', __( 'e.g. –3.5k or /mo', 'jawad' ) ),
				'_jd_desc'       => array( __( 'Short description', 'jawad' ), 'textarea', '' ),
				'_jd_features'   => array( __( 'Features (one per line)', 'jawad' ), 'textarea', '' ),
				'_jd_featured'   => array( __( 'Highlight this plan', 'jawad' ), 'checkbox', '' ),
				'_jd_badge'      => array( __( 'Highlight badge', 'jawad' ), 'text', __( 'e.g. Most booked', 'jawad' ) ),
				'_jd_cta_label'  => array( __( 'Button label', 'jawad' ), 'text', '' ),
				'_jd_cta_url'    => array( __( 'Button link', 'jawad' ), 'url', '' ),
			),
		),
		'jd_testimonial' => array(
			'title'  => __( 'Testimonial details', 'jawad' ),
			'fields' => array(
				'_jd_quote_short' => array( __( 'Short pull quote', 'jawad' ), 'textarea', __( 'One punchy sentence shown large on the home page. The full review goes in the main editor.', 'jawad' ) ),
				'_jd_role'        => array( __( 'Role and business', 'jawad' ), 'text', '' ),
				'_jd_rating'      => array( __( 'Rating (1 to 5)', 'jawad' ), 'number', '' ),
				'_jd_source_url'  => array( __( 'Link to original review', 'jawad' ), 'url', __( 'Upwork, Google, Clutch. Verifiable reviews build the most trust.', 'jawad' ) ),
			),
		),
		'jd_lead'        => array(
			'title'  => __( 'Lead details', 'jawad' ),
			'fields' => array(
				'_jd_email'   => array( __( 'Email', 'jawad' ), 'text', '' ),
				'_jd_type'    => array( __( 'Project type', 'jawad' ), 'text', '' ),
				'_jd_budget'  => array( __( 'Budget', 'jawad' ), 'text', '' ),
				'_jd_when'    => array( __( 'Timing', 'jawad' ), 'text', '' ),
				'_jd_website' => array( __( 'Current website', 'jawad' ), 'text', '' ),
			),
		),
	);
}

add_action( 'init', function () {
	$common = array(
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-portfolio',
	);

	// Taxonomy first so /work/type/<term>/ rules win over project single-page rules.
	register_taxonomy( 'jd_project_type', 'jd_project', array(
		'labels'            => array(
			'name'          => __( 'Project types', 'jawad' ),
			'singular_name' => __( 'Project type', 'jawad' ),
		),
		'public'            => true,
		'hierarchical'      => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'rewrite'           => array( 'slug' => 'work/type', 'with_front' => false ),
	) );

	register_post_type( 'jd_project', array_merge( $common, array(
		'labels'        => array(
			'name'          => __( 'Work', 'jawad' ),
			'singular_name' => __( 'Project', 'jawad' ),
			'add_new_item'  => __( 'Add project', 'jawad' ),
			'edit_item'     => __( 'Edit project', 'jawad' ),
			'all_items'     => __( 'All projects', 'jawad' ),
			'archives'      => __( 'Work', 'jawad' ),
		),
		'public'        => true,
		'has_archive'   => 'work',
		'rewrite'       => array( 'slug' => 'work', 'with_front' => false ),
		'menu_position' => 20,
		'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'page-attributes', 'revisions' ),
	) ) );

	register_post_type( 'jd_service', array_merge( $common, array(
		'labels'        => array(
			'name'          => __( 'Services', 'jawad' ),
			'singular_name' => __( 'Service', 'jawad' ),
			'add_new_item'  => __( 'Add service', 'jawad' ),
			'edit_item'     => __( 'Edit service', 'jawad' ),
			'archives'      => __( 'Services', 'jawad' ),
		),
		'public'        => true,
		'has_archive'   => 'services',
		'rewrite'       => array( 'slug' => 'services', 'with_front' => false ),
		'menu_icon'     => 'dashicons-hammer',
		'menu_position' => 21,
		'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'page-attributes', 'revisions' ),
	) ) );

	$private = array(
		'public'              => false,
		'show_ui'             => true,
		'show_in_rest'        => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'has_archive'         => false,
		'rewrite'             => false,
	);

	register_post_type( 'jd_plan', array_merge( $private, array(
		'labels'        => array(
			'name'          => __( 'Pricing', 'jawad' ),
			'singular_name' => __( 'Plan', 'jawad' ),
			'add_new_item'  => __( 'Add plan', 'jawad' ),
		),
		'menu_icon'     => 'dashicons-tag',
		'menu_position' => 22,
		'supports'      => array( 'title', 'custom-fields', 'page-attributes' ),
	) ) );

	register_post_type( 'jd_testimonial', array_merge( $private, array(
		'labels'        => array(
			'name'          => __( 'Testimonials', 'jawad' ),
			'singular_name' => __( 'Testimonial', 'jawad' ),
			'add_new_item'  => __( 'Add testimonial', 'jawad' ),
			'enter_title_here' => __( 'Client full name', 'jawad' ),
		),
		'menu_icon'     => 'dashicons-format-quote',
		'menu_position' => 23,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'page-attributes' ),
	) ) );

	register_post_type( 'jd_faq', array_merge( $private, array(
		'labels'        => array(
			'name'          => __( 'FAQs', 'jawad' ),
			'singular_name' => __( 'FAQ', 'jawad' ),
			'add_new_item'  => __( 'Add question', 'jawad' ),
			'enter_title_here' => __( 'Question', 'jawad' ),
		),
		'menu_icon'     => 'dashicons-editor-help',
		'menu_position' => 24,
		'supports'      => array( 'title', 'editor', 'page-attributes' ),
	) ) );

	register_taxonomy( 'jd_faq_group', 'jd_faq', array(
		'labels'            => array(
			'name'          => __( 'FAQ groups', 'jawad' ),
			'singular_name' => __( 'FAQ group', 'jawad' ),
		),
		'public'            => false,
		'show_ui'           => true,
		'show_in_rest'      => true,
		'hierarchical'      => true,
		'show_admin_column' => true,
		'rewrite'           => false,
	) );

	register_post_type( 'jd_lead', array(
		'labels'              => array(
			'name'          => __( 'Leads', 'jawad' ),
			'singular_name' => __( 'Lead', 'jawad' ),
		),
		'public'              => false,
		'show_ui'             => true,
		'show_in_rest'        => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'menu_icon'           => 'dashicons-email-alt',
		'menu_position'       => 25,
		'supports'            => array( 'title', 'editor' ),
		'capability_type'     => 'post',
		'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
		'map_meta_cap'        => true,
	) );

	// Register meta so it is available in REST and the block editor.
	foreach ( jd_meta_config() as $post_type => $group ) {
		foreach ( $group['fields'] as $key => $field ) {
			$type = $field[1];
			register_post_meta( $post_type, $key, array(
				'type'              => in_array( $type, array( 'checkbox', 'number' ), true ) ? ( 'checkbox' === $type ? 'boolean' : 'number' ) : 'string',
				'single'            => true,
				'show_in_rest'      => 'jd_lead' !== $post_type,
				'sanitize_callback' => function ( $value ) use ( $type ) {
					return jd_sanitize_meta_value( $value, $type );
				},
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
			) );
		}
	}

	// SEO meta on every public, editable type.
	foreach ( array( 'post', 'page', 'jd_project', 'jd_service' ) as $post_type ) {
		foreach ( array( '_jd_seo_title' => 'string', '_jd_seo_desc' => 'string', '_jd_noindex' => 'boolean' ) as $key => $type ) {
			register_post_meta( $post_type, $key, array(
				'type'          => $type,
				'single'        => true,
				'show_in_rest'  => true,
				'auth_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			) );
		}
	}
} );

/**
 * Sanitize a single meta value by field type.
 */
function jd_sanitize_meta_value( $value, $type ) {
	switch ( $type ) {
		case 'checkbox':
			return (bool) $value;
		case 'number':
			return '' === $value || null === $value ? '' : (float) $value;
		case 'url':
			return esc_url_raw( (string) $value );
		case 'color':
			return sanitize_hex_color( (string) $value ) ?: '';
		case 'textarea':
			return sanitize_textarea_field( (string) $value );
		default:
			return sanitize_text_field( (string) $value );
	}
}

/**
 * Flush rewrites once after the theme is switched so /work/ and /services/ resolve.
 */
add_action( 'after_switch_theme', function () {
	update_option( 'jd_flush_rewrites', 1 );
} );
add_action( 'init', function () {
	if ( get_option( 'jd_flush_rewrites' ) ) {
		flush_rewrite_rules( false );
		delete_option( 'jd_flush_rewrites' );
	}
}, 99 );

/**
 * Order archives by the manual order field, then date.
 */
add_action( 'pre_get_posts', function ( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_post_type_archive( array( 'jd_project', 'jd_service' ) ) || $query->is_tax( 'jd_project_type' ) ) {
		$query->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
		$query->set( 'posts_per_page', 24 );
	}
} );

/**
 * Admin list columns for leads.
 */
add_filter( 'manage_jd_lead_posts_columns', function ( $cols ) {
	return array(
		'cb'        => $cols['cb'],
		'title'     => __( 'Lead', 'jawad' ),
		'jd_email'  => __( 'Email', 'jawad' ),
		'jd_type'   => __( 'Project', 'jawad' ),
		'jd_budget' => __( 'Budget', 'jawad' ),
		'date'      => $cols['date'],
	);
} );
add_action( 'manage_jd_lead_posts_custom_column', function ( $col, $post_id ) {
	$map = array( 'jd_email' => '_jd_email', 'jd_type' => '_jd_type', 'jd_budget' => '_jd_budget' );
	if ( isset( $map[ $col ] ) ) {
		$value = (string) get_post_meta( $post_id, $map[ $col ], true );
		if ( 'jd_email' === $col && is_email( $value ) ) {
			printf( '<a href="mailto:%1$s">%1$s</a>', esc_html( $value ) );
		} else {
			echo esc_html( $value );
		}
	}
}, 10, 2 );
