<?php
/**
 * Redux Framework WPML integration config.
 * For full documentation, please visit: http://docs.redux.io/
 *
 * @package Redux Framework
 */

defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title'      => esc_html__( 'WPML Integration', 'your-textdomain-here' ),
		'desc'       => esc_html__( 'These fields can be fully translated by WPML (WordPress Multi-Language). This serves as an example for you to implement. For extra details look at our <a href="//docs.redux.io/core/advanced/wpml-integration/" target="_blank">WPML Implementation</a> documentation.', 'your-textdomain-here' ),
		'subsection' => true,
		'fields'     => array(
			array(
				'id'    => 'wpml-text',
				'type'  => 'textarea',
				'title' => esc_html__( 'WPML Text', 'your-textdomain-here' ),
				'desc'  => esc_html__( 'This string can be translated via WPML.', 'your-textdomain-here' ),
			),
			array(
				'id'      => 'wpml-multicheck',
				'type'    => 'checkbox',
				'title'   => esc_html__( 'WPML Multi Checkbox', 'your-textdomain-here' ),
				'desc'    => esc_html__( 'You can literally translate the values via key.', 'your-textdomain-here' ),
				'options' => array(
					'1' => esc_html__( 'Option 1', 'your-textdomain-here' ),
					'2' => esc_html__( 'Option 2', 'your-textdomain-here' ),
					'3' => esc_html__( 'Option 3', 'your-textdomain-here' ),
				),
			),
		),
	)
);
