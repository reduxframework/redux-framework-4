<?php
/**
 * ReduxTemplates - Full Width / Stretched
 *
 * @since v.4.0.0
 * @package redux-framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();
echo '</div></div>';
while ( have_posts() ) :
	the_post();

	// Future - Break out of any div.
	the_content();
endwhile; // End of the loop.
echo '<div><div>';
get_footer();
