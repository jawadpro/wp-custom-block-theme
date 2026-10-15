<?php
/**
 * Theme options: one option array, a tabbed settings page under Appearance.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

const JD_OPTION = 'jd_options';

/**
 * Field definitions, grouped by tab. Each field: label, type, default, help.
 */
function jd_option_fields() {
	return array(
		'general' => array(
			'title'  => __( 'Brand & hero', 'jawad' ),
			'fields' => array(
				'brand_name'        => array( __( 'Brand name', 'jawad' ), 'text', 'Jawad Ilyas', __( 'Shown in the header logo, footer wordmark and structured data.', 'jawad' ) ),
				'brand_mark'        => array( __( 'Logo mark letter', 'jawad' ), 'text', 'J', __( 'One or two characters inside the cobalt logo tile. A Site Logo set in the Site Editor replaces the tile.', 'jawad' ) ),
				'job_title'         => array( __( 'Role', 'jawad' ), 'text', 'WordPress Developer', '' ),
				'availability_on'   => array( __( 'Show availability badge', 'jawad' ), 'checkbox', 1, '' ),
				'availability_text' => array( __( 'Availability text', 'jawad' ), 'text', 'Taking 2 new projects this month', __( 'Keep it true and update it monthly.', 'jawad' ) ),
				'badge_2'           => array( __( 'Hero badge 2', 'jawad' ), 'text', 'Top Rated on Upwork', __( 'Leave empty to hide.', 'jawad' ) ),
				'badge_3'           => array( __( 'Hero badge 3', 'jawad' ), 'text', 'Fixed-price quotes', __( 'Leave empty to hide.', 'jawad' ) ),
				'footer_blurb'      => array( __( 'Footer description', 'jawad' ), 'textarea', 'WordPress developer for startups, growing businesses and established brands. Fast sites, clear offers, and replies inside your working day.', '' ),
			),
		),
		'contact' => array(
			'title'  => __( 'Contact & social', 'jawad' ),
			'fields' => array(
				'email'           => array( __( 'Public email', 'jawad' ), 'email', 'hello@jawadjd.dev', '' ),
				'notify_email'    => array( __( 'Send form leads to', 'jawad' ), 'email', '', __( 'Defaults to the site admin email.', 'jawad' ) ),
				'phone'           => array( __( 'Phone or WhatsApp', 'jawad' ), 'text', '', __( 'Optional.', 'jawad' ) ),
				'hours_text'      => array( __( 'Working hours line', 'jawad' ), 'text', 'Live 9am to 2pm Eastern, Monday to Friday', '' ),
				'response_text'   => array( __( 'Response promise', 'jawad' ), 'text', 'Replies within 24 hours', '' ),
				'calendly_url'    => array( __( 'Call booking link', 'jawad' ), 'url', '', __( 'Your Cal.com, Calendly or Google Calendar appointment schedule link. When set, a "Book a call" button appears in the header and menu, and the Book page embeds the calendar. Leave empty to hide all of it.', 'jawad' ) ),
				'book_label'      => array( __( 'Book button label', 'jawad' ), 'text', 'Book a 20-min call', '' ),
				'book_page'       => array( __( 'Book page link', 'jawad' ), 'url', '/book/', __( 'Where the Book button goes. The page should contain the "Call booking calendar" block.', 'jawad' ) ),
				'upwork_url'      => array( __( 'Upwork profile', 'jawad' ), 'url', '', '' ),
				'linkedin_url'    => array( __( 'LinkedIn', 'jawad' ), 'url', 'https://www.linkedin.com/in/jawadjddev/', '' ),
				'github_url'      => array( __( 'GitHub', 'jawad' ), 'url', 'https://github.com/jawadpro', '' ),
				'x_url'           => array( __( 'X / Twitter', 'jawad' ), 'url', '', '' ),
				'gf_form_id'      => array( __( 'Send form entries to Gravity Forms form ID', 'jawad' ), 'number', '', __( 'Optional. When set and Gravity Forms is active, the theme form submits into that form (fields matched by label: Name, Email, Website, Service, Budget, Timeline, Details), so its notifications, entries and SMTP apply.', 'jawad' ) ),
				'form_success'    => array( __( 'Form success message', 'jawad' ), 'textarea', "Thanks. I'll reply within 24 hours with next steps and a time for a quick call.", '' ),
				'live_tz'         => array( __( 'Client time zone for live status', 'jawad' ), 'text', 'America/New_York', __( 'Any PHP time zone name.', 'jawad' ) ),
				'live_start'      => array( __( 'Live hours start (0-23, client time)', 'jawad' ), 'number', 9, '' ),
				'live_end'        => array( __( 'Live hours end (0-23, client time)', 'jawad' ), 'number', 14, '' ),
			),
		),
		'cta'     => array(
			'title'  => __( 'Offer card', 'jawad' ),
			'fields' => array(
				'audit_on'    => array( __( 'Show offer card in menu', 'jawad' ), 'checkbox', 1, '' ),
				'audit_badge' => array( __( 'Badge on hero card', 'jawad' ), 'text', '24h', __( 'One short word or number, e.g. Free or 48h.', 'jawad' ) ),
				'audit_label' => array( __( 'Label', 'jawad' ), 'text', 'Free project review', '' ),
				'audit_title' => array( __( 'Title', 'jawad' ), 'text', 'Get a fixed quote, not a guess', '' ),
				'audit_text'  => array( __( 'Text', 'jawad' ), 'textarea', 'Tell me what you need. You get a written price and timeline within 24 hours.', '' ),
				'audit_url'   => array( __( 'Button link', 'jawad' ), 'url', '/contact/', '' ),
				'audit_cta'   => array( __( 'Button label', 'jawad' ), 'text', 'Start my quote', '' ),
			),
		),
		'motion'  => array(
			'title'  => __( 'Motion & speed', 'jawad' ),
			'fields' => array(
				'motion_on'        => array( __( 'Scroll animations', 'jawad' ), 'checkbox', 1, __( 'Turning this off removes the animation scripts entirely. Visitors with reduced motion always get a static page.', 'jawad' ) ),
				'smooth_scroll'    => array( __( 'Smooth scrolling', 'jawad' ), 'checkbox', 1, '' ),
				'custom_cursor'    => array( __( 'Custom cursor and magnetic buttons', 'jawad' ), 'checkbox', 1, __( 'Desktop mouse only.', 'jawad' ) ),
				'page_transitions' => array( __( 'Page transitions', 'jawad' ), 'checkbox', 1, __( 'Uses the browser View Transitions API. No JavaScript.', 'jawad' ) ),
				'hero_field'       => array( __( 'Animated dot field in hero', 'jawad' ), 'checkbox', 1, '' ),
				'webp_uploads'     => array( __( 'Create WebP image sizes', 'jawad' ), 'checkbox', 1, __( 'New PNG and JPEG uploads get WebP versions for every size. The original file is kept.', 'jawad' ) ),
				'delay_chat'       => array( __( 'Load live chat after first interaction', 'jawad' ), 'checkbox', 1, __( 'Delays Tawk.to, Crisp, Tidio, LiveChat or Intercom until the visitor scrolls, taps or types, or after 15 seconds. Faster first paint.', 'jawad' ) ),
			),
		),
		'seo'     => array(
			'title'  => __( 'SEO', 'jawad' ),
			'fields' => array(
				'seo_on'          => array( __( 'Built-in SEO tags', 'jawad' ), 'checkbox', 1, __( 'Meta descriptions, Open Graph, Twitter cards and JSON-LD. Switches itself off when Yoast, Rank Math, SEOPress or AIOSEO is active.', 'jawad' ) ),
				'title_sep'       => array( __( 'Title separator', 'jawad' ), 'text', '|', '' ),
				'home_title'      => array( __( 'Home page title', 'jawad' ), 'text', 'Jawad Ilyas | WordPress Developer for Fast, High-Converting Websites', '' ),
				'home_desc'       => array( __( 'Home page meta description', 'jawad' ), 'textarea', 'WordPress developer who builds fast, conversion-focused WordPress and WooCommerce websites for businesses of every size. Fixed quotes, clear process, replies within 24 hours.', __( 'Aim for 140 to 160 characters.', 'jawad' ) ),
				'default_desc'    => array( __( 'Fallback meta description', 'jawad' ), 'textarea', 'Fast, SEO-friendly WordPress websites, WooCommerce stores, speed fixes and care plans by Jawad Ilyas.', '' ),
				'work_desc'       => array( __( 'Work archive description', 'jawad' ), 'textarea', 'Selected WordPress websites, landing pages and WooCommerce stores, with the results they produced.', '' ),
				'services_desc'   => array( __( 'Services archive description', 'jawad' ), 'textarea', 'WordPress website design, WooCommerce stores, speed optimization and monthly care plans with fixed, upfront pricing.', '' ),
				'og_image'        => array( __( 'Default social share image', 'jawad' ), 'image', '', __( '1200 × 630 recommended.', 'jawad' ) ),
				'x_handle'        => array( __( 'X / Twitter handle', 'jawad' ), 'text', '', __( 'Without the @.', 'jawad' ) ),
				'schema_type'     => array( __( 'Schema entity', 'jawad' ), 'select', 'ProfessionalService', '', array( 'ProfessionalService' => 'Professional service (business)', 'Person' => 'Person' ) ),
				'area_served'     => array( __( 'Area served', 'jawad' ), 'text', 'Worldwide', '' ),
				'price_range'     => array( __( 'Price range', 'jawad' ), 'text', '$$', '' ),
				'gsc_verify'      => array( __( 'Google Search Console verification code', 'jawad' ), 'text', '', __( 'Only the content value of the meta tag.', 'jawad' ) ),
				'ga4_id'          => array( __( 'Google Analytics 4 ID', 'jawad' ), 'text', '', __( 'Optional, e.g. G-XXXXXXX. Loads after the page is interactive.', 'jawad' ) ),
			),
		),
	);
}

/**
 * Flat defaults array.
 */
function jd_option_defaults() {
	static $defaults = null;
	if ( null === $defaults ) {
		$defaults = array();
		foreach ( jd_option_fields() as $tab ) {
			foreach ( $tab['fields'] as $key => $field ) {
				$defaults[ $key ] = $field[2];
			}
		}
	}
	return $defaults;
}

/**
 * Read one theme option.
 */
function jd_opt( $key, $fallback = null ) {
	static $cache = null;
	if ( null === $cache ) {
		$cache = wp_parse_args( (array) get_option( JD_OPTION, array() ), jd_option_defaults() );
	}
	if ( array_key_exists( $key, $cache ) && null !== $cache[ $key ] ) {
		return $cache[ $key ];
	}
	return null === $fallback ? '' : $fallback;
}

/**
 * Bust the static cache after save (same request).
 */
add_action( 'update_option_' . JD_OPTION, function () {
	wp_cache_delete( JD_OPTION, 'options' );
} );

add_action( 'admin_menu', function () {
	add_theme_page( __( 'Theme Options', 'jawad' ), __( 'Theme Options', 'jawad' ), 'edit_theme_options', 'jd-options', 'jd_render_options_page' );
} );

add_action( 'admin_init', function () {
	register_setting(
		'jd_options_group',
		JD_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'jd_sanitize_options',
			'default'           => array(),
		)
	);
} );

/**
 * Sanitize by field type. Unchecked checkboxes are stored as 0.
 */
function jd_sanitize_options( $input ) {
	$input = is_array( $input ) ? $input : array();
	$clean = (array) get_option( JD_OPTION, array() );
	$tab   = isset( $input['_tab'] ) ? sanitize_key( $input['_tab'] ) : '';
	$all   = jd_option_fields();
	$tabs  = $tab && isset( $all[ $tab ] ) ? array( $tab => $all[ $tab ] ) : $all;

	foreach ( $tabs as $group ) {
		foreach ( $group['fields'] as $key => $field ) {
			$type = $field[1];
			$raw  = $input[ $key ] ?? '';
			switch ( $type ) {
				case 'checkbox':
					$clean[ $key ] = empty( $raw ) ? 0 : 1;
					break;
				case 'email':
					$clean[ $key ] = sanitize_email( $raw );
					break;
				case 'url':
					$clean[ $key ] = esc_url_raw( trim( $raw ) );
					break;
				case 'number':
					$clean[ $key ] = '' === $raw ? '' : (int) $raw;
					break;
				case 'image':
					$clean[ $key ] = absint( $raw );
					break;
				case 'textarea':
					$clean[ $key ] = sanitize_textarea_field( $raw );
					break;
				case 'select':
					$choices       = $field[4] ?? array();
					$clean[ $key ] = array_key_exists( $raw, $choices ) ? $raw : $field[2];
					break;
				default:
					$clean[ $key ] = sanitize_text_field( $raw );
			}
		}
	}
	if ( isset( $clean['gsc_verify'] ) ) {
		$clean['gsc_verify'] = preg_replace( '/[^A-Za-z0-9_\-]/', '', $clean['gsc_verify'] );
	}
	if ( isset( $clean['ga4_id'] ) ) {
		$clean['ga4_id'] = preg_replace( '/[^A-Za-z0-9\-]/', '', strtoupper( $clean['ga4_id'] ) );
	}
	return $clean;
}

add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( 'appearance_page_jd-options' !== $hook ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script( 'jd-options', JD_URI . '/assets/js/admin-options.js', array(), JD_VERSION, true );
	wp_add_inline_style( 'wp-admin', '.jd-opt .form-table th{width:260px}.jd-opt .description{max-width:60ch}.jd-img-prev{display:block;max-width:240px;height:auto;margin:8px 0;border-radius:8px}.jd-opt .jd-tools{margin-top:32px;padding:16px 20px;background:#fff;border:1px solid #dcdcde;border-radius:8px;max-width:720px}' );
} );

/**
 * Render the settings page.
 */
function jd_render_options_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$all     = jd_option_fields();
	$current = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'general'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! isset( $all[ $current ] ) ) {
		$current = 'general';
	}
	$values = wp_parse_args( (array) get_option( JD_OPTION, array() ), jd_option_defaults() );
	?>
	<div class="wrap jd-opt">
		<h1><?php esc_html_e( 'Theme Options', 'jawad' ); ?></h1>
		<?php if ( jd_seo_plugin_active() && 'seo' === $current ) : ?>
			<div class="notice notice-info"><p><?php esc_html_e( 'An SEO plugin is active, so the theme SEO tags are switched off automatically. The verification and analytics fields still work.', 'jawad' ); ?></p></div>
		<?php endif; ?>
		<nav class="nav-tab-wrapper" aria-label="<?php esc_attr_e( 'Option sections', 'jawad' ); ?>">
			<?php foreach ( $all as $slug => $tab ) : ?>
				<a class="nav-tab <?php echo $slug === $current ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( array( 'page' => 'jd-options', 'tab' => $slug ), admin_url( 'themes.php' ) ) ); ?>"><?php echo esc_html( $tab['title'] ); ?></a>
			<?php endforeach; ?>
		</nav>
		<form method="post" action="options.php">
			<?php settings_fields( 'jd_options_group' ); ?>
			<input type="hidden" name="<?php echo esc_attr( JD_OPTION ); ?>[_tab]" value="<?php echo esc_attr( $current ); ?>">
			<table class="form-table" role="presentation">
				<?php
				foreach ( $all[ $current ]['fields'] as $key => $field ) :
					list( $label, $type ) = $field;
					$help  = $field[3] ?? '';
					$id    = 'jd_' . $key;
					$name  = JD_OPTION . '[' . $key . ']';
					$value = $values[ $key ] ?? '';
					?>
					<tr>
						<th scope="row"><label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label></th>
						<td>
							<?php
							switch ( $type ) {
								case 'checkbox':
									printf( '<input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s>', esc_attr( $id ), esc_attr( $name ), checked( (int) $value, 1, false ) );
									break;
								case 'textarea':
									printf( '<textarea id="%1$s" name="%2$s" rows="3" class="large-text">%3$s</textarea>', esc_attr( $id ), esc_attr( $name ), esc_textarea( $value ) );
									break;
								case 'select':
									printf( '<select id="%1$s" name="%2$s">', esc_attr( $id ), esc_attr( $name ) );
									foreach ( ( $field[4] ?? array() ) as $opt_value => $opt_label ) {
										printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $opt_value ), selected( $value, $opt_value, false ), esc_html( $opt_label ) );
									}
									echo '</select>';
									break;
								case 'image':
									$src = $value ? wp_get_attachment_image_url( (int) $value, 'medium' ) : '';
									printf( '<input type="hidden" id="%1$s" name="%2$s" value="%3$s">', esc_attr( $id ), esc_attr( $name ), esc_attr( $value ) );
									printf( '<img class="jd-img-prev" id="%1$s_prev" src="%2$s" alt="" %3$s>', esc_attr( $id ), esc_url( (string) $src ), $src ? '' : 'hidden' );
									printf( '<button type="button" class="button jd-media" data-target="%1$s">%2$s</button> ', esc_attr( $id ), esc_html__( 'Choose image', 'jawad' ) );
									printf( '<button type="button" class="button-link jd-media-clear" data-target="%1$s">%2$s</button>', esc_attr( $id ), esc_html__( 'Remove', 'jawad' ) );
									break;
								default:
									$input_type = in_array( $type, array( 'email', 'url', 'number' ), true ) ? $type : 'text';
									printf( '<input type="%1$s" id="%2$s" name="%3$s" value="%4$s" class="regular-text">', esc_attr( $input_type ), esc_attr( $id ), esc_attr( $name ), esc_attr( $value ) );
							}
							if ( $help ) {
								printf( '<p class="description">%s</p>', esc_html( $help ) );
							}
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<?php submit_button(); ?>
		</form>

		<div class="jd-tools">
			<h2><?php esc_html_e( 'Starter content', 'jawad' ); ?></h2>
			<p><?php esc_html_e( 'Creates the Home, About, Contact and Journal pages, the main menu, and example services, pricing plans, projects, testimonials and FAQs. Existing items with the same title are skipped, so it is safe to run again.', 'jawad' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="jd_import_starter">
				<?php wp_nonce_field( 'jd_import_starter' ); ?>
				<?php submit_button( __( 'Import starter content', 'jawad' ), 'secondary', 'submit', false ); ?>
			</form>
		</div>
	</div>
	<?php
}
