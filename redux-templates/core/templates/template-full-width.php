<?php
/**
 * ReduxTemplates - Full Width / Stretched
 *
 * @since 4.0.0
 * @package redux-framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
get_header();

echo '</div></div>';
echo '<style type="text/css">#wrapper {min-height: auto;}</style>';
while ( have_posts() ) :
	the_post();

	the_content();
endwhile; // End of the loop.
echo '<div><div>';
get_footer();
