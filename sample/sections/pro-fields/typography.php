<?php
/**
 * Redux Pro Typography Sample config.
 *
 * For full documentation, please visit: http://docs.reduxframework.com/
 *
 * @package Redux Pro
 */

defined( 'ABSPATH' ) || exit;

Redux::setSection(
	$opt_name,
	array(
		'title'      => esc_html__( 'Typography', 'your-textdomain-here' ),
		'id'         => 'pro-typography',
		'desc'       => esc_html__( 'For full documentation on this field, visit: ', 'your-textdomain-here' ) . '<a href="//docs.reduxframework.com/core/fields/typography/" target="_blank">docs.reduxframework.com/core/fields/typography/</a>',
		'subsection' => true,
		'fields'     => array(
			array(
				'id'                => 'opt-pro-typography-body',
				'type'              => 'typography',
				'title'             => esc_html__( 'Body Font', 'your-textdomain-here' ),
				'subtitle'          => esc_html__( 'Specify the body font properties.', 'your-textdomain-here' ),
				'google'            => true,
				'font_family_clear' => false,
				'text-shadow'       => true,
				'color_alpha'       => true,
				'margin-top'        => true,
				'margin-bottom'     => true,
				'default'           => array(
					'color'         => '#dd9933',
					'font-size'     => '30px',
					'font-family'   => 'Arial, Helvetica, sans-serif',
					'font-weight'   => 'Normal',
					'margin-top'    => '2px',
					'margin-bottom' => '2px',
				),
				'output'            => array( '.site-description, .entry-title' ),
			),
		),
	)
);
