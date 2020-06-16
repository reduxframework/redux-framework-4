<?php
/**
 * Redux Framework background config.
 * For full documentation, please visit: http://docs.redux.io/
 *
 * @package Redux Framework
 */

defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title'      => esc_html__( 'Background', 'your-textdomain-here' ),
		'id'         => 'design-background',
		'desc'       => esc_html__( 'For full documentation on this field, visit: ', 'your-textdomain-here' ) . '<a href="//docs.redux.io/core/fields/background/" target="_blank">docs.redux.io/core/fields/background/</a>',
		'subsection' => true,
		'fields'     => array(
			array(
				'id'       => 'opt-background',
				'type'     => 'background',
				'output'   => array( 'body' ),
				'title'    => __( 'Body Background', 'your-textdomain-here' ),
				'subtitle' => __( 'Body background with image, color, etc.', 'your-textdomain-here' ),
			),
		),
	)
);
