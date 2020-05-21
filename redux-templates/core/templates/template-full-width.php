<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * ReduxTemplates - Full Width / Stretched
 *
 * @since v.1.0.0
 * @package redux-templates
 */
get_header();
echo '</div></div>';
while ( have_posts() ) : the_post();

// TODO - Break out of any div
	the_content();
endwhile; // End of the loop.
echo "<div><div>";
get_footer();
