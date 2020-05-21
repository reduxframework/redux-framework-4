<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * ReduxTemplates - Full Width / Contained
 *
 * @since v.1.0.0
 * @package starter-blocks
 */
get_header();
while ( have_posts() ) : the_post();
	the_content();
endwhile; // End of the loop.

get_footer();
