<?php
/**
 * Redux Pro Custom Fonts Sample config.
 *
 * For full documentation, please visit: http://docs.reduxframework.com/
 *
 * @package Redux Pro
 */

defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title'      => esc_html__( 'Custom Fonts', 'your-textdomain-here' ),
		'desc'       => esc_html__( 'For full documentation on this field, visit: ', 'your-textdomain-here' ) . '<a href="//docs.reduxframework.com/extensions/custom-fonts" target="_blank">docs.reduxframework.com/extensions/custom-fonts</a>',
		'subsection' => true,
		'fields'     => array(
			array(
				'id'   => 'custom_fonts',
				'type' => 'custom_fonts',
			),
			array(
				'id'          => 'custom_fonts_typography',
				'type'        => 'typography',
				'title'       => esc_html__( 'Custom Fonts Typography', 'your-textdomain-here' ),
				'subtitle'    => 'This will modify the font family of the .entry-title classes.',
				'output'      => '.site-title, .widget-title, .entry-title',
				'font-size'   => false,
				'line-height' => false,
				'text-align'  => false,
			),
		),
	)
);
