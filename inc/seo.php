<?php
/**
 * Built-in SEO: titles, meta descriptions, robots, Open Graph, Twitter cards, JSON-LD and sitemap tuning.
 * Steps aside automatically when a dedicated SEO plugin is active.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the theme should print SEO tags.
 */
function jd_seo_enabled() {
	return jd_opt( 'seo_on' ) && ! jd_seo_plugin_active();
}

/**
 * Meta description for the current request.
 */
function jd_seo_description() {
	$desc = '';
	if ( is_front_page() ) {
		$desc = (string) jd_opt( 'home_desc' );
		$id   = (int) get_option( 'page_on_front' );
		if ( $id && get_post_meta( $id, '_jd_seo_desc', true ) ) {
			$desc = (string) get_post_meta( $id, '_jd_seo_desc', true );
		}
	} elseif ( is_singular() ) {
		$id   = get_queried_object_id();
		$desc = (string) get_post_meta( $id, '_jd_seo_desc', true );
		if ( ! $desc && has_excerpt( $id ) ) {
			$desc = get_the_excerpt( $id );
		}
		if ( ! $desc ) {
			$desc = wp_trim_words( wp_strip_all_tags( strip_shortcodes( (string) get_post_field( 'post_content', $id ) ) ), 28, '…' );
		}
	} elseif ( is_post_type_archive( 'jd_project' ) ) {
		$desc = (string) jd_opt( 'work_desc' );
	} elseif ( is_post_type_archive( 'jd_service' ) ) {
		$desc = (string) jd_opt( 'services_desc' );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$desc = wp_strip_all_tags( term_description() );
	} elseif ( is_home() ) {
		$id   = (int) get_option( 'page_for_posts' );
		$desc = $id ? (string) get_post_meta( $id, '_jd_seo_desc', true ) : '';
	}
	if ( ! $desc ) {
		$desc = (string) jd_opt( 'default_desc' );
	}
	$desc = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $desc ) ) );
	return mb_strlen( $desc ) > 160 ? rtrim( mb_substr( $desc, 0, 157 ), " ,.;:" ) . '…' : $desc;
}

/**
 * Social share image URL for the current request.
 */
function jd_seo_image() {
	if ( is_singular() && has_post_thumbnail( get_queried_object_id() ) ) {
		$src = wp_get_attachment_image_src( get_post_thumbnail_id( get_queried_object_id() ), 'jd-wide' );
		if ( $src ) {
			return array( $src[0], $src[1], $src[2] );
		}
	}
	$og = (int) jd_opt( 'og_image' );
	if ( $og ) {
		$src = wp_get_attachment_image_src( $og, 'full' );
		if ( $src ) {
			return array( $src[0], $src[1], $src[2] );
		}
	}
	return null;
}

add_filter( 'document_title_separator', function ( $sep ) {
	return jd_opt( 'title_sep' ) ? jd_opt( 'title_sep' ) : $sep;
} );

add_filter( 'pre_get_document_title', function ( $title ) {
	if ( ! jd_seo_enabled() ) {
		return $title;
	}
	if ( is_front_page() && jd_opt( 'home_title' ) ) {
		$id = (int) get_option( 'page_on_front' );
		$custom = $id ? (string) get_post_meta( $id, '_jd_seo_title', true ) : '';
		return $custom ? $custom : (string) jd_opt( 'home_title' );
	}
	if ( is_singular() ) {
		$custom = (string) get_post_meta( get_queried_object_id(), '_jd_seo_title', true );
		if ( $custom ) {
			return $custom;
		}
	}
	return $title;
} );

add_filter( 'document_title_parts', function ( $parts ) {
	if ( jd_seo_enabled() && is_post_type_archive( array( 'jd_project', 'jd_service' ) ) ) {
		$parts['title'] = is_post_type_archive( 'jd_project' ) ? __( 'Work and case studies', 'jawad' ) : __( 'Web development services and pricing', 'jawad' );
	}
	return $parts;
} );

add_filter( 'wp_robots', function ( $robots ) {
	if ( ! jd_seo_enabled() ) {
		return $robots;
	}
	if ( ( is_singular() && get_post_meta( get_queried_object_id(), '_jd_noindex', true ) ) || is_404() || is_search() ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}
	if ( empty( $robots['noindex'] ) ) {
		$robots['max-image-preview'] = 'large';
		$robots['max-snippet']       = '-1';
	}
	return $robots;
} );

add_action( 'wp_head', function () {
	if ( jd_opt( 'gsc_verify' ) ) {
		printf( '<meta name="google-site-verification" content="%s">' . "\n", esc_attr( jd_opt( 'gsc_verify' ) ) );
	}
	if ( ! jd_seo_enabled() ) {
		return;
	}

	$desc  = jd_seo_description();
	$title = wp_get_document_title();
	$url   = jd_current_url();
	$image = jd_seo_image();
	$type  = is_singular( 'post' ) ? 'article' : 'website';
	$site  = jd_opt( 'brand_name' ) ? jd_opt( 'brand_name' ) : get_bloginfo( 'name' );

	if ( $desc ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	}
	if ( ! is_singular() && ( is_home() || is_archive() ) ) {
		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );
	}

	$tags = array(
		'og:locale'      => get_locale(),
		'og:type'        => $type,
		'og:site_name'   => $site,
		'og:title'       => $title,
		'og:description' => $desc,
		'og:url'         => $url,
	);
	if ( $image ) {
		$tags['og:image']        = $image[0];
		$tags['og:image:width']  = $image[1];
		$tags['og:image:height'] = $image[2];
	}
	if ( 'article' === $type ) {
		$tags['article:published_time'] = get_the_date( 'c' );
		$tags['article:modified_time']  = get_the_modified_date( 'c' );
	}
	foreach ( $tags as $prop => $content ) {
		if ( '' !== (string) $content ) {
			printf( '<meta property="%1$s" content="%2$s">' . "\n", esc_attr( $prop ), esc_attr( $content ) );
		}
	}
	printf( '<meta name="twitter:card" content="%s">' . "\n", $image ? 'summary_large_image' : 'summary' );
	if ( jd_opt( 'x_handle' ) ) {
		printf( '<meta name="twitter:site" content="@%s">' . "\n", esc_attr( ltrim( jd_opt( 'x_handle' ), '@' ) ) );
	}

	printf( '<script type="application/ld+json">%s</script>' . "\n", wp_json_encode( jd_schema_graph( $desc, $url, $image ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG ) );
}, 2 );

/**
 * Canonical-style URL for the current request (no query string, keeps pagination).
 */
function jd_current_url() {
	if ( is_singular() ) {
		return (string) wp_get_canonical_url( get_queried_object_id() );
	}
	if ( is_front_page() ) {
		return home_url( '/' );
	}
	$paged = max( 1, (int) get_query_var( 'paged' ) );
	if ( is_home() ) {
		$url = get_permalink( (int) get_option( 'page_for_posts' ) );
	} elseif ( is_post_type_archive() ) {
		$url = get_post_type_archive_link( get_query_var( 'post_type' ) ? ( is_array( get_query_var( 'post_type' ) ) ? get_query_var( 'post_type' )[0] : get_query_var( 'post_type' ) ) : 'post' );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$url = get_term_link( get_queried_object() );
	} elseif ( is_author() ) {
		$url = get_author_posts_url( get_queried_object_id() );
	} else {
		$url = home_url( '/' );
	}
	$url = is_wp_error( $url ) || ! $url ? home_url( '/' ) : $url;
	return $paged > 1 ? trailingslashit( $url ) . 'page/' . $paged . '/' : $url;
}

/**
 * JSON-LD graph: WebSite, the business or person, WebPage, breadcrumbs and the main entity.
 */
function jd_schema_graph( $desc, $url, $image ) {
	$home   = home_url( '/' );
	$name   = jd_opt( 'brand_name' ) ? jd_opt( 'brand_name' ) : get_bloginfo( 'name' );
	$entity = 'Person' === jd_opt( 'schema_type' ) ? 'Person' : 'ProfessionalService';
	$same   = array_values( array_filter( array( jd_opt( 'linkedin_url' ), jd_opt( 'github_url' ), jd_opt( 'upwork_url' ), jd_opt( 'x_url' ) ) ) );
	$logo   = (int) get_theme_mod( 'custom_logo' );

	$org = array(
		'@type' => $entity,
		'@id'   => $home . '#owner',
		'name'  => $name,
		'url'   => $home,
	);
	if ( $same ) {
		$org['sameAs'] = $same;
	}
	if ( jd_opt( 'email' ) ) {
		$org['email'] = jd_opt( 'email' );
	}
	if ( $logo ) {
		$org['image'] = wp_get_attachment_image_url( $logo, 'full' );
	}
	if ( 'Person' === $entity ) {
		$org['jobTitle'] = jd_opt( 'job_title' );
	} else {
		$org['description'] = jd_opt( 'home_desc' );
		$org['areaServed']  = jd_opt( 'area_served' );
		$org['priceRange']  = jd_opt( 'price_range' );
		$org['founder']     = array( '@type' => 'Person', 'name' => $name, 'jobTitle' => jd_opt( 'job_title' ) );
	}

	$graph = array(
		array(
			'@type'     => 'WebSite',
			'@id'       => $home . '#website',
			'url'       => $home,
			'name'      => $name,
			'publisher' => array( '@id' => $home . '#owner' ),
			'potentialAction' => array(
				'@type'       => 'SearchAction',
				'target'      => home_url( '/?s={search_term_string}' ),
				'query-input' => 'required name=search_term_string',
			),
		),
		$org,
	);

	$page = array(
		'@type'      => is_singular() ? 'WebPage' : ( is_search() ? 'SearchResultsPage' : 'CollectionPage' ),
		'@id'        => $url . '#webpage',
		'url'        => $url,
		'name'       => wp_get_document_title(),
		'isPartOf'   => array( '@id' => $home . '#website' ),
		'inLanguage' => get_bloginfo( 'language' ),
	);
	if ( $desc ) {
		$page['description'] = $desc;
	}
	if ( $image ) {
		$page['primaryImageOfPage'] = array( '@type' => 'ImageObject', 'url' => $image[0] );
	}
	if ( is_front_page() ) {
		$page['about'] = array( '@id' => $home . '#owner' );
	}

	// Breadcrumbs for everything but the front page.
	if ( ! is_front_page() ) {
		$crumbs = array( array( __( 'Home', 'jawad' ), $home ) );
		if ( is_singular( array( 'jd_project', 'jd_service' ) ) ) {
			$pt       = get_post_type();
			$crumbs[] = array( 'jd_project' === $pt ? __( 'Work', 'jawad' ) : __( 'Services', 'jawad' ), get_post_type_archive_link( $pt ) );
		} elseif ( is_singular( 'post' ) && get_option( 'page_for_posts' ) ) {
			$crumbs[] = array( get_the_title( (int) get_option( 'page_for_posts' ) ), get_permalink( (int) get_option( 'page_for_posts' ) ) );
		}
		$crumbs[] = array( wp_strip_all_tags( is_singular() ? get_the_title() : wp_get_document_title() ), $url );
		$items    = array();
		foreach ( $crumbs as $i => $crumb ) {
			$items[] = array( '@type' => 'ListItem', 'position' => $i + 1, 'name' => $crumb[0], 'item' => $crumb[1] );
		}
		$graph[]            = array( '@type' => 'BreadcrumbList', '@id' => $url . '#breadcrumb', 'itemListElement' => $items );
		$page['breadcrumb'] = array( '@id' => $url . '#breadcrumb' );
	}
	$graph[] = $page;

	if ( is_singular( 'post' ) ) {
		$post    = get_queried_object();
		$graph[] = array(
			'@type'            => 'BlogPosting',
			'@id'              => $url . '#article',
			'headline'         => wp_strip_all_tags( get_the_title( $post ) ),
			'description'      => $desc,
			'datePublished'    => get_the_date( 'c', $post ),
			'dateModified'     => get_the_modified_date( 'c', $post ),
			'author'           => array( '@id' => $home . '#owner' ),
			'publisher'        => array( '@id' => $home . '#owner' ),
			'mainEntityOfPage' => array( '@id' => $url . '#webpage' ),
			'image'            => $image ? $image[0] : null,
		);
	} elseif ( is_singular( 'jd_service' ) ) {
		$id      = get_queried_object_id();
		$service = array(
			'@type'       => 'Service',
			'@id'         => $url . '#service',
			'name'        => wp_strip_all_tags( get_the_title( $id ) ),
			'description' => $desc,
			'provider'    => array( '@id' => $home . '#owner' ),
			'areaServed'  => jd_opt( 'area_served' ),
			'url'         => $url,
		);
		$from = get_post_meta( $id, '_jd_price_from', true );
		if ( '' !== $from && null !== $from ) {
			$service['offers'] = array( '@type' => 'Offer', 'price' => (string) $from, 'priceCurrency' => 'USD' );
		}
		$graph[] = $service;
	} elseif ( is_singular( 'jd_project' ) ) {
		$id      = get_queried_object_id();
		$graph[] = array_filter( array(
			'@type'       => 'CreativeWork',
			'@id'         => $url . '#work',
			'name'        => wp_strip_all_tags( get_the_title( $id ) ),
			'description' => $desc,
			'creator'     => array( '@id' => $home . '#owner' ),
			'url'         => get_post_meta( $id, '_jd_live_url', true ) ? get_post_meta( $id, '_jd_live_url', true ) : $url,
			'image'       => $image ? $image[0] : null,
		) );
	}

	return array( '@context' => 'https://schema.org', '@graph' => array_values( array_filter( $graph ) ) );
}

/**
 * Sitemap: drop the thin users sitemap and anything marked noindex.
 */
add_filter( 'wp_sitemaps_add_provider', function ( $provider, $name ) {
	return 'users' === $name ? false : $provider;
}, 10, 2 );

add_filter( 'wp_sitemaps_posts_query_args', function ( $args ) {
	$args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		'relation' => 'OR',
		array( 'key' => '_jd_noindex', 'compare' => 'NOT EXISTS' ),
		array( 'key' => '_jd_noindex', 'value' => '1', 'compare' => '!=' ),
	);
	return $args;
} );

add_filter( 'wp_sitemaps_taxonomies', function ( $taxonomies ) {
	unset( $taxonomies['post_format'] );
	return $taxonomies;
} );

/**
 * Google Analytics 4, loaded after the page is interactive so it never blocks rendering.
 */
add_action( 'wp_footer', function () {
	$id = (string) jd_opt( 'ga4_id' );
	if ( ! $id || is_user_logged_in() ) {
		return;
	}
	?>
	<script>
	window.addEventListener('load',function(){setTimeout(function(){var s=document.createElement('script');s.async=true;s.src='https://www.googletagmanager.com/gtag/js?id=<?php echo esc_js( $id ); ?>';document.head.appendChild(s);window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}window.gtag=gtag;gtag('js',new Date());gtag('config','<?php echo esc_js( $id ); ?>');},1500);});
	</script>
	<?php
}, 99 );

/**
 * Yoast sometimes prints og:image without its dimensions (for example when the image was set by URL).
 * Facebook and LinkedIn skip images on first share without them, so add width, height, type and alt.
 */
add_action( 'wp_head', function () {
	if ( ! function_exists( 'YoastSEO' ) || is_admin() ) {
		return;
	}
	try {
		$images = YoastSEO()->meta->for_current_page()->open_graph_images;
	} catch ( Throwable $e ) {
		return;
	}
	if ( ! $images || ! is_array( $images ) ) {
		return;
	}
	$first = reset( $images );
	if ( ! empty( $first['width'] ) || empty( $first['url'] ) ) {
		return;
	}
	$id = attachment_url_to_postid( $first['url'] );
	if ( ! $id ) {
		return;
	}
	$src = wp_get_attachment_image_src( $id, 'full' );
	if ( ! $src ) {
		return;
	}
	printf( '<meta property="og:image:width" content="%d" />' . "\n", (int) $src[1] );
	printf( '<meta property="og:image:height" content="%d" />' . "\n", (int) $src[2] );
	$type = wp_check_filetype( (string) wp_parse_url( $first['url'], PHP_URL_PATH ) );
	printf( '<meta property="og:image:type" content="%s" />' . "\n", esc_attr( $type['type'] ? $type['type'] : get_post_mime_type( $id ) ) );
	$alt = (string) get_post_meta( $id, '_wp_attachment_image_alt', true );
	if ( '' !== $alt ) {
		printf( '<meta property="og:image:alt" content="%s" />' . "\n", esc_attr( $alt ) );
	}
}, 99 );
