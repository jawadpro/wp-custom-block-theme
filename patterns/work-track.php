<?php
/**
 * Title: Work track
 * Slug: jawad/work-track
 * Categories: jawad-sections
 * Description: Heading with a horizontal pinned row of projects.
 * Inserter: yes
 *
 * @package Jawad
 */

?>
<!-- wp:group {"tagName":"section","className":"jd-section jd-hwork"} -->
<section class="wp-block-group jd-section jd-hwork"><!-- wp:group {"className":"jd-wrap jd-section__head"} -->
<div class="wp-block-group jd-wrap jd-section__head"><!-- wp:heading {"className":"jd-section__title"} -->
<h2 class="wp-block-heading jd-section__title">Recent<br>builds</h2>
<!-- /wp:heading -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-ghost jd-mag"} -->
<div class="wp-block-button is-style-ghost jd-mag"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/work/' ) ); ?>">All projects</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->

<!-- wp:jd/projects /--></section>
<!-- /wp:group -->
