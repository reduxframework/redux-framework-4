<?php
/**
 * Redux Framework disable section config.
 * For full documentation, please visit: http://docs.redux.io/
 *
 * @package Redux Framework
 */

defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title'            => esc_html__( 'Disable Section', 'your-domain-here' ),
		'id'               => 'basic-checkbox-section-disable',
		'subsection'       => true,
		'customizer_width' => '450px',
		'disabled'         => true,
		'desc'             => esc_html__( 'For full documentation on this field, visit: ', 'your-domain-here' ) . '<a href="//docs.redux.io/core/fields/checkbox/" target="_blank">docs.redux.io/core/fields/checkbox/</a>',
		'fields'           => array(
			array(
				'id'       => 'opt-checkbox-section-disable',
				'type'     => 'checkbox',
				'title'    => esc_html__( 'Checkbox Option', 'your-domain-here' ),
				'subtitle' => esc_html__( 'No validation can be done on this field type', 'your-domain-here' ),
				'desc'     => esc_html__( 'This is the description field, again good for additional info.', 'your-domain-here' ),
				'default'  => '1', // 1 = on | 0 = off.
			),
		),
	)
);
