<?php
/**
 * Redux Framework field sanitizing config.
 * For full documentation, please visit: http://docs.redux.io/
 *
 * @package Redux Framework
 */

defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title'      => esc_html__( 'Field Sanitizing', 'your-domain-here' ),
		'id'         => 'sanitizing',
		'desc'       => esc_html__( 'For full documentation on sanitizing, visit: ', 'your-domain-here' ) . '<a href="//docs.redux.io/core/the-basics/sanitizing/" target="_blank">docs.redux.io/core/the-basics/sanitizing/</a>',
		'subsection' => true,
		'fields'     => array(
			array(
				'id'       => 'opt-text-uppercase',
				'type'     => 'text',
				'title'    => esc_html__( 'Text Option - Force Uppercase', 'your-domain-here' ),
				'subtitle' => esc_html__( 'Uses the strtoupper function to force all uppercase characters.', 'your-domain-here' ),
				'desc'     => esc_html__( 'This is the description field, again good for additional info.', 'your-domain-here' ),
				'sanitize' => array( 'strtoupper' ),
				'default'  => 'Force Uppercase',
			),
			array(
				'id'       => 'opt-text-sanitize-title',
				'type'     => 'text',
				'title'    => esc_html__( 'Text Option - Sanitize Title', 'your-domain-here' ),
				'subtitle' => esc_html__( 'Uses the WordPress sanitize_title function to format text.', 'your-domain-here' ),
				'desc'     => esc_html__( 'This is the description field, again good for additional info.', 'your-domain-here' ),
				'sanitize' => array( 'sanitize_title' ),
				'default'  => 'Sanitize This Title',
			),
			array(
				'id'       => 'opt-text-custom-sanitize',
				'type'     => 'text',
				'title'    => esc_html__( 'Text Option - Custom Sanitize', 'your-domain-here' ),
				'subtitle' => esc_html__( 'Uses the custom function redux_custom_santize to capitalize every other letter.', 'your-domain-here' ),
				'desc'     => esc_html__( 'This is the description field, again good for additional info.', 'your-domain-here' ),
				'sanitize' => array( 'redux_custom_sanitize' ),
				'default'  => 'Sanitize This Text',
			),
		),
	)
);
