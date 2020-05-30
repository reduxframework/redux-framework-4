<?php
/**
 * Redux Pro Search Sample config.
 *
 * For full documentation, please visit: http://docs.reduxframework.com/
 *
 * @package Redux Pro
 */

defined( 'ABSPATH' ) || exit;

// --> Below this line not needed. This is just for demonstration purposes.
Redux::set_section(
	$opt_name,
	array(
		'title'      => esc_html__( 'Live Search', 'your-domain-here' ),
		'desc'       => esc_html__( 'For full documentation on this field, visit: ', 'your-domain-here' ) . '<a href="//docs.reduxframework.com/extensions/live-search" target="_blank">docs.reduxframework.com/extensions/live-search</a>',
		'heading'    => esc_html__( 'This extension is a drop-in utility. Try the search box to the top right of every panel or metabox section. It will dynamically filter out the visible fields to match your search.', 'your-domain-here' ),
		'subsection' => true,
		'customizer' => false,
	)
);
