<?php
/**
 * Redux Framework color gradient config.
 * For full documentation, please visit: http://docs.redux.io/
 *
 * @package Redux Framework
 */

defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title'      => esc_html__( 'Color Gradient', 'your-textdomain-here' ),
		'desc'       => esc_html__( 'For full documentation on this field, visit: ', 'your-textdomain-here' ) . '<a href="//docs.redux.io/core/fields/color-gradient/" target="_blank">docs.redux.io/core/fields/color-gradient/</a>',
		'id'         => 'color-gradient',
		'subsection' => true,
		'fields'     => array(
			array(
				'id'       => 'opt-color-header',
				'type'     => 'color_gradient',
				'title'    => esc_html__( 'Header Gradient Color Option', 'your-textdomain-here' ),
				'subtitle' => esc_html__( 'Only color validation can be done on this field type', 'your-textdomain-here' ),
				'desc'     => esc_html__( 'This is the description field, again good for additional info.', 'your-textdomain-here' ),
				'output'   => '.site-header',
				'preview'  => true,
				'default'  => array(
					'from' => '#1e73be',
					'to'   => '#00897e',
				),
			),
		),
	)
);
