<?php
/**
 * ReduxTemplates - Full Width / Contained
 *
 * @since   4.0.0
 * @package redux-framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo '<style type="text/css" id="redux-template-overrides">' . ReduxTemplates\Template_Overrides::get_overrides() . '</style>';
while ( have_posts() ) :
	the_post();
	the_content();
endwhile; // End of the loop.

get_footer();
