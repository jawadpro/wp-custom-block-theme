<?php
/**
 * Dynamic theme blocks. Each lives in /blocks/<name>/ with block.json and render.php.
 * One small editor script registers them with a server-side preview, so no build step is needed.
 *
 * @package Jawad
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'block_categories_all', function ( $categories ) {
	array_unshift( $categories, array( 'slug' => 'jawad', 'title' => __( 'Jawad', 'jawad' ), 'icon' => null ) );
	return $categories;
} );

add_action( 'init', function () {
	wp_register_script(
		'jd-blocks-editor',
		JD_URI . '/assets/js/editor-blocks.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render', 'wp-i18n' ),
		JD_VERSION,
		true
	);
	$names = array();
	foreach ( glob( JD_DIR . '/blocks/*/block.json' ) as $file ) {
		$type = register_block_type( dirname( $file ) );
		if ( $type ) {
			$names[] = $type->name;
		}
	}
	wp_add_inline_script( 'jd-blocks-editor', 'window.jdBlockNames=' . wp_json_encode( $names ) . ';', 'before' );
} );

/**
 * Collected FAQ entries for one FAQPage JSON-LD in the footer.
 */
function jd_faq_schema_collect( $items = null ) {
	static $all = array();
	if ( null !== $items ) {
		$all = array_merge( $all, $items );
	}
	return $all;
}

add_action( 'wp_footer', function () {
	if ( ! jd_opt( 'seo_on' ) || ! apply_filters( 'jd_output_faq_schema', true ) ) {
		return;
	}
	$items = jd_faq_schema_collect();
	if ( ! $items ) {
		return;
	}
	$entities = array();
	foreach ( $items as $item ) {
		$entities[] = array(
			'@type'          => 'Question',
			'name'           => $item['q'],
			'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $item['a'] ),
		);
	}
	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode( array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $entities ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG )
	);
}, 20 );

/**
 * CSS-drawn browser mock used when a project has no featured image.
 */
function jd_project_mock( $post_id ) {
	$bg      = jd_meta( $post_id, '_jd_card_bg', '#3D5AFE' );
	$ink     = jd_meta( $post_id, '_jd_card_ink', '#1A1D2B' );
	$accent  = jd_meta( $post_id, '_jd_card_accent', '#3D5AFE' );
	$dark    = (bool) get_post_meta( $post_id, '_jd_card_dark', true );
	$variant = (int) jd_meta( $post_id, '_jd_card_variant', 0 );
	$frame   = $dark ? '#0B1220' : '#FFFFFF';
	$soft    = $dark ? 'rgba(255,255,255,.08)' : 'rgba(0,0,0,.06)';
	$style   = sprintf( '--mk-bg:%s;--mk-ink:%s;--mk-acc:%s;--mk-frame:%s;--mk-soft:%s', $bg, $ink, $accent, $frame, $soft );
	$blocks  = array(
		0 => '<i class="b w28 acc"></i><i class="b w82 h22 ink"></i><i class="b w58 h22 ink"></i><span class="row"><i class="b pill w34 acc"></i><i class="b pill w24 line"></i></span><span class="g3"><i class="b soft"></i><i class="b soft"></i><i class="b soft"></i></span>',
		1 => '<span class="split"><span class="col"><i class="b w50 acc"></i><i class="b h20 ink"></i><i class="b h20 w70 ink"></i><i class="b w60 h26 acc"></i></span><i class="b tall soft"></i></span><span class="g4"><i class="b soft"></i><i class="b soft"></i><i class="b soft"></i><i class="b soft"></i></span>',
		2 => '<i class="b w46 h20 ink"></i><span class="g4 big"><i class="b soft"></i><i class="b soft"></i><i class="b soft"></i><i class="b soft"></i></span><span class="g4 thin"><i class="b ink o"></i><i class="b ink o"></i><i class="b ink o"></i><i class="b ink o"></i></span><i class="b h30 acc"></i>',
		3 => '<span class="row between"><i class="b w24 ink"></i><i class="b pill w22 acc"></i></span><i class="b hero acc"></i><span class="g3"><i class="b soft"></i><i class="b soft"></i><i class="b soft"></i></span>',
	);
	return sprintf(
		'<span class="jd-shot jd-mock" style="%1$s" aria-hidden="true"><span class="jd-frame"><span class="jd-frame__bar"><i></i><i></i><i></i></span><span class="jd-frame__bd">%2$s</span></span></span>',
		esc_attr( $style ),
		$blocks[ $variant ] ?? $blocks[0]
	);
}

/**
 * Project card markup shared by the Work track and grid.
 */
function jd_project_card( $post, $class = 'jd-pcard' ) {
	$id    = $post->ID;
	$terms = get_the_terms( $id, 'jd_project_type' );
	$slugs = $terms && ! is_wp_error( $terms ) ? wp_list_pluck( $terms, 'slug' ) : array();
	$tag   = jd_meta( $id, '_jd_tag', $terms && ! is_wp_error( $terms ) ? $terms[0]->name : '' );
	if ( has_post_thumbnail( $id ) ) {
		$media = sprintf(
			'<span class="jd-shot jd-shot--img" style="--mk-bg:%1$s">%2$s</span>',
			esc_attr( jd_meta( $id, '_jd_card_bg', '#151D36' ) ),
			get_the_post_thumbnail( $id, 'jd-card', array( 'class' => 'jd-shot__img', 'alt' => '', 'sizes' => '(max-width: 820px) 82vw, 560px' ) )
		);
	} else {
		$media = jd_project_mock( $id );
	}
	$excerpt = has_excerpt( $id ) ? get_the_excerpt( $id ) : wp_trim_words( wp_strip_all_tags( $post->post_content ), 12 );
	return sprintf(
		'<a href="%1$s" class="%2$s" data-cats="%3$s" data-cursor="%4$s">%5$s<span class="jd-pmeta"><span class="jd-pmeta__text"><span class="jd-pmeta__title">%6$s</span><span class="jd-pmeta__desc">%7$s</span></span>%8$s</span></a>',
		esc_url( get_permalink( $id ) ),
		esc_attr( $class ),
		esc_attr( implode( ' ', $slugs ) ),
		esc_attr__( 'View', 'jawad' ),
		$media,
		esc_html( get_the_title( $id ) ),
		esc_html( $excerpt ),
		$tag ? '<span class="jd-tag">' . esc_html( $tag ) . '</span>' : ''
	);
}
