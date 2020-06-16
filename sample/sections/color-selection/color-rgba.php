<?php
/**
 * Redux Framework color RGBA config.
 * For full documentation, please visit: http://docs.redux.io/
 *
 * @package Redux Framework
 */

defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title'      => esc_html__( 'Color RGBA', 'your-textdomain-here' ),
		'desc'       => esc_html__( 'For full documentation on this field, visit: ', 'your-textdomain-here' ) . '<a href="//docs.redux.io/core/fields/color-rgba/" target="_blank">docs.redux.io/core/fields/color-rgba/</a>',
		'id'         => 'color-rgba',
		'subsection' => true,
		'fields'     => array(
			array(
				'id'       => 'opt-color-rgba',
				'type'     => 'color_rgba',
				'title'    => esc_html__( 'Color RGBA', 'your-textdomain-here' ),
				'subtitle' => esc_html__( 'Gives you the RGBA color.', 'your-textdomain-here' ),
				'default'  => array(
					'color' => '#7e33dd',
					'alpha' => '.8',
				),
				'mode'     => 'color',
				'validate' => 'colorrgba',
			),
		),
	)
);
