<?php
/**
 * Redux Framework select image config.
 * For full documentation, please visit: http://docs.redux.io/
 *
 * @package Redux Framework
 */

defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title'      => esc_html__( 'Select Image', 'your-domain-here' ),
		'id'         => 'select-select_image',
		'desc'       => esc_html__( 'For full documentation on this field, visit: ', 'your-domain-here' ) . '<a href="//docs.redux.io/core/fields/select-image/" target="_blank">docs.redux.io/core/fields/select-image/</a>',
		'subsection' => true,
		'fields'     => array(
			array(
				'id'      => 'opt-select_image',
				'type'    => 'select_image',
				'presets' => true,
				'title'   => esc_html__( 'Select Image', 'your-domain-here' ),
				'options' => array(
					array(
						'alt' => 'Preset 1',
						'img' => Redux_Core::$url . '../sample/presets/preset1.png',
					),
					array(
						'alt' => 'Preset 2',
						'img' => Redux_Core::$url . '../sample/presets/preset2.png',
					),
				),
				'default' => Redux_Core::$url . '../sample/presets/preset2.png',
			),
			array(
				'id'       => 'opt-select-image',
				'type'     => 'select_image',
				'title'    => esc_html__( 'Select Image', 'your-domain-here' ),
				'subtitle' => esc_html__( 'A preview of the selected image will appear underneath the select box.', 'your-domain-here' ),
				'options'  => $sample_patterns,
				'default'  => Redux_Core::$url . '../sample/patterns/triangular.png',
			),
		),
	)
);
