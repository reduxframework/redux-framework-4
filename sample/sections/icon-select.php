<?php
/**
 * Redux Pro Icon Select Sample config.
 *
 * For full documentation, please visit: http://docs.reduxframework.com/
 *
 * @package Redux Pro
 */

defined( 'ABSPATH' ) || exit;

Redux::setSection(
	$opt_name,
	array(
		'title'  => esc_html__( 'Icon Select', 'your-domain-here' ),
		'desc'   => esc_html__( 'For full documentation on this field, visit: ', 'your-domain-here' ) . '<a href="//docs.reduxframework.com/extensions/icon-select/" target="_blank">docs.reduxframework.com/extensions/icon-select/</a>',
		'subsection' => true,
		'fields' => array(
			array(
				'id'               => 'icon_select_field',
				'type'             => 'icon_select',
				'title'            => esc_html__( 'Icon Select', 'your-domain-here' ),
				'subtitle'         => esc_html__( 'Select an icon.', 'your-domain-here' ),
				'default'          => '',
				//'options'          => redux_icon_select_fa_5_free(),

				// Disable auto-enqueue of stylesheet if present in the panel.
				'enqueue'          => true,

				// Disable auto-enqueue of stylesheet on the front-end.
				'enqueue_frontend' => false,
				'stylesheet'       => 'https://use.fontawesome.com/releases/v5.6.3/css/all.css',

				'prefix'           => 'fa',
				// If needed to initialize the icon.
				'selector'         => 'fa-',
				// How each icons begins for this given font.
				'height'           => 300,
				// Change the height of the container. defaults to 300px; .
			),
		),
	)
);
