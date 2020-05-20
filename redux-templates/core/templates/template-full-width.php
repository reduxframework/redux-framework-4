<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * StarterBlocks - Full Width / Stretched
 *
 * @since v.1.0.0
 * @package starter-blocks
 */
get_header();
echo '</div></div>';
while ( have_posts() ) : the_post();

// TODO - Break out of any div
	the_content();
endwhile; // End of the loop.
echo "<div><div>";
get_footer();
