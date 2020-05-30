<?php
/**
 * Redux Framework dimensions config.
 * For full documentation, please visit: http://docs.redux.io/
 *
 * @package Redux Framework
 */

defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title'      => esc_html__( 'Dimensions', 'your-domain-here' ),
		'id'         => 'design-dimensions',
		'desc'       => esc_html__( 'For full documentation on this field, visit: ', 'your-domain-here' ) . '<a href="//docs.redux.io/core/fields/dimensions/" target="_blank">docs.redux.io/core/fields/dimensions/</a>',
		'subsection' => true,
		'fields'     => array(
			array(
				'id'             => 'opt-dimensions',
				'type'           => 'dimensions',
				'units'          => array( 'em', 'px', '%' ), // You can specify a unit value. Possible: px, em, %.
				'units_extended' => 'true', // Allow users to select any type of unit.
				'title'          => esc_html__( 'Dimensions (Width/Height) Option', 'your-domain-here' ),
				'subtitle'       => esc_html__( 'Allow your users to choose width, height, and/or unit.', 'your-domain-here' ),
				'desc'           => esc_html__( 'You can enable or disable any piece of this field. Width, Height, or Units.', 'your-domain-here' ),
				'default'        => array(
					'width'  => 200,
					'height' => 100,
				),
			),
			array(
				'id'             => 'opt-dimensions-width',
				'type'           => 'dimensions',
				'units'          => array( 'em', 'px', '%' ), // You can specify a unit value. Possible: px, em, %.
				'units_extended' => 'true', // Allow users to select any type of unit.
				'title'          => esc_html__( 'Dimensions (Width) Option', 'your-domain-here' ),
				'subtitle'       => esc_html__( 'Allow your users to choose width, height, and/or unit.', 'your-domain-here' ),
				'desc'           => esc_html__( 'You can enable or disable any piece of this field. Width, Height, or Units.', 'your-domain-here' ),
				'height'         => false,
				'default'        => array(
					'width'  => 200,
					'height' => 100,
				),
			),
		),
	)
);
