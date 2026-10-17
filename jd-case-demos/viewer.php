<?php
/**
 * Standalone demo viewer markup. Variables: $projects, $current, $home, $site.
 *
 * @package JD_Case_Demos
 */

defined( 'ABSPATH' ) || exit;

$jdcd_title = $projects ? $projects[ $current ]['title'] . ' · ' . __( 'Live demo', 'jd-case-demos' ) : __( 'Case study demos', 'jd-case-demos' );
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, follow">
<title><?php echo esc_html( $jdcd_title . ' | ' . $site ); ?></title>
<link rel="stylesheet" href="<?php echo esc_url( JDCD_URL . 'assets/demo.css?v=' . JDCD_VERSION ); ?>">
</head>
<body class="jdcd">
<header class="jdcd-top">
	<a class="jdcd-back" href="<?php echo esc_url( $home ); ?>"><span aria-hidden="true">&larr;</span> <?php echo esc_html( $site ); ?></a>
	<p class="jdcd-kicker"><?php esc_html_e( 'Case study demos', 'jd-case-demos' ); ?></p>
	<a class="jdcd-cta" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Get a fixed quote', 'jd-case-demos' ); ?></a>
</header>

<?php if ( ! $projects ) : ?>
	<main class="jdcd-empty"><p><?php esc_html_e( 'No projects with a live link or screenshots yet. Add them under Projects and they appear here.', 'jd-case-demos' ); ?></p></main>
<?php else : ?>
<nav class="jdcd-pills" aria-label="<?php esc_attr_e( 'Projects', 'jd-case-demos' ); ?>">
	<?php foreach ( $projects as $i => $p ) : ?>
		<a href="<?php echo esc_url( home_url( '/' . JDCD_BASE . '/' . $p['slug'] . '/' ) ); ?>" data-i="<?php echo (int) $i; ?>" <?php echo $i === $current ? 'aria-current="page"' : ''; ?>><?php echo esc_html( $p['title'] ); ?></a>
	<?php endforeach; ?>
</nav>

<main class="jdcd-main">
	<section class="jdcd-stage" aria-label="<?php esc_attr_e( 'Preview', 'jd-case-demos' ); ?>">
		<div class="jdcd-tools">
			<div class="jdcd-seg" role="group" aria-label="<?php esc_attr_e( 'Device', 'jd-case-demos' ); ?>">
				<button type="button" data-device="desktop" aria-pressed="true"><?php esc_html_e( 'Desktop', 'jd-case-demos' ); ?></button>
				<button type="button" data-device="tablet" aria-pressed="false"><?php esc_html_e( 'Tablet', 'jd-case-demos' ); ?></button>
				<button type="button" data-device="phone" aria-pressed="false"><?php esc_html_e( 'Phone', 'jd-case-demos' ); ?></button>
			</div>
			<div class="jdcd-seg" role="group" aria-label="<?php esc_attr_e( 'View', 'jd-case-demos' ); ?>">
				<button type="button" data-mode="live" aria-pressed="true"><?php esc_html_e( 'Live site', 'jd-case-demos' ); ?></button>
				<button type="button" data-mode="shots" aria-pressed="false"><?php esc_html_e( 'Screenshots', 'jd-case-demos' ); ?></button>
			</div>
		</div>
		<div class="jdcd-viewport">
			<div class="jdcd-device" data-device="desktop">
				<div class="jdcd-chrome"><i></i><i></i><i></i><span class="jdcd-addr"></span></div>
				<div class="jdcd-screen">
					<iframe class="jdcd-frame" title="<?php esc_attr_e( 'Live site preview', 'jd-case-demos' ); ?>" loading="lazy" referrerpolicy="no-referrer" sandbox="allow-scripts allow-same-origin allow-forms allow-popups"></iframe>
					<div class="jdcd-shots" hidden><img alt=""></div>
					<div class="jdcd-loading"><span></span><?php esc_html_e( 'Loading the live site…', 'jd-case-demos' ); ?></div>
				</div>
			</div>
		</div>
		<p class="jdcd-hint"><?php esc_html_e( 'Scroll and click inside the preview. Not loading?', 'jd-case-demos' ); ?> <button type="button" class="jdcd-to-shots"><?php esc_html_e( 'View screenshots', 'jd-case-demos' ); ?></button></p>
		<div class="jdcd-thumbs" hidden></div>
	</section>

	<aside class="jdcd-info" aria-live="polite">
		<p class="jdcd-scope"></p>
		<h1 class="jdcd-title"></h1>
		<p class="jdcd-client"></p>
		<p class="jdcd-summary"></p>
		<ul class="jdcd-results"></ul>
		<div class="jdcd-block" data-k="problem"><h2><?php esc_html_e( 'The problem', 'jd-case-demos' ); ?></h2><p></p></div>
		<div class="jdcd-block" data-k="did"><h2><?php esc_html_e( 'What I did', 'jd-case-demos' ); ?></h2><p></p></div>
		<div class="jdcd-actions">
			<a class="jdcd-btn jdcd-btn--main jdcd-live" target="_blank" rel="noopener"><?php esc_html_e( 'Open the live site', 'jd-case-demos' ); ?> <span aria-hidden="true">&nearr;</span></a>
			<a class="jdcd-btn jdcd-case"><?php esc_html_e( 'Read the case study', 'jd-case-demos' ); ?></a>
		</div>
		<div class="jdcd-nav">
			<button type="button" class="jdcd-prev" aria-label="<?php esc_attr_e( 'Previous project', 'jd-case-demos' ); ?>">&larr;</button>
			<span class="jdcd-count"></span>
			<button type="button" class="jdcd-next" aria-label="<?php esc_attr_e( 'Next project', 'jd-case-demos' ); ?>">&rarr;</button>
		</div>
	</aside>
</main>
<script id="jdcd-data" type="application/json"><?php echo wp_json_encode( array( 'projects' => $projects, 'current' => $current, 'base' => home_url( '/' . JDCD_BASE . '/' ) ) ); ?></script>
<script src="<?php echo esc_url( JDCD_URL . 'assets/demo.js?v=' . JDCD_VERSION ); ?>" defer></script>
<?php endif; ?>
</body>
</html>
