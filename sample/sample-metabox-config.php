<?php
/**
 * Redux Framework Sample Metabox Config File
 * For full documentation, please visit: http://docs.reduxframework.com/
 *
 * Metabox Lite support the following fields only:  checkbox, radio, text, textarea, media, & color
 * Post Format and Post Template options are not avaialble in Metabox Lite.
 *
 * These advanced options are available in Redux Pro.
 *
 * @package Redux Framework
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redux_Metaboxes' ) ) {
	return;
}

Redux_Metaboxes::set_box(
	$opt_name,
	array(
		'id'         => 'opt-metaboxes',
		'title'      => esc_html__( 'Metabox Options', 'your-domain-here' ),
		'post_types' => array( 'page', 'post' ),
		'position'   => 'normal', // normal, advanced, side.
		'priority'   => 'high', // high, core, default, low.
		'sections'   => array(
			array(
				'title'  => esc_html__( 'Basic Fields', 'your-domain-here' ),
				'id'     => 'opt-basic-fields',
				'desc'   => esc_html__( 'Redux Framework was created with the developer in mind. It allows for any theme developer to have an advanced theme panel with most of the features a developer would need. For more information check out the Github repo at:', 'your-domain-here' ) . '  <a href="https://github.com/ReduxFramework/Redux-Framework">https://github.com/ReduxFramework/Redux-Framework</a>',
				'icon'   => 'el-icon-cogs',
				'fields' => array(
					array(
						'id'       => 'opt-checkbox',
						'type'     => 'checkbox',
						'title'    => esc_html__( 'Checkbox', 'your-domain-here' ),
						'subtitle' => esc_html__( 'Basic Checkbox field.', 'your-domain-here' ),
						'default'  => true,
					),
					array(
						'id'       => 'opt-radio',
						'type'     => 'radio',
						'title'    => esc_html__( 'Radio Button', 'your-domain-here' ),
						'subtitle' => esc_html__( 'Basic Radio Button field.', 'your-domain-here' ),
						'options'  => array(
							'1' => esc_html__( 'Option 1', 'your-domain-here' ),
							'2' => esc_html__( 'Option 2', 'your-domain-here' ),
							'3' => esc_html__( 'Option 3', 'your-domain-here' ),
						),
						'default'  => '2',
					),
					array(
						'id'       => 'opt-media',
						'type'     => 'media',
						'url'      => true,
						'title'    => esc_html__( 'Media w/ URL', 'your-domain-here' ),
						'compiler' => 'true',
						'desc'     => esc_html__( 'Basic media uploader with disabled URL input field.', 'your-domain-here' ),
						'subtitle' => esc_html__( 'Upload any media using the WordPress native uploader', 'your-domain-here' ),
						'default'  => array( 'url' => 'http://s.wordpress.org/style/images/codeispoetry.png' ),
					),
				),
			),

			array(
				'title'      => esc_html__( 'Text Fields', 'your-domain-here' ),
				'desc'       => esc_html__( 'Redux Framework was created with the developer in mind. It allows for any theme developer to have an advanced theme panel with most of the features a developer would need. For more information check out the Github repo at:', 'your-domain-here' ) . '  <a href="https://github.com/ReduxFramework/Redux-Framework">https://github.com/ReduxFramework/Redux-Framework</a>',
				'icon'       => 'el-icon-cog',
				'id'         => 'opt-text-fields',
				'subsection' => true,
				'fields'     => array(
					array(
						'title' => esc_html__( 'Text Field', 'your-domain-here' ),
						'id'    => 'opt-text',
						'type'  => 'text',
					),
					array(
						'title' => esc_html__( 'Textarea Field', 'your-domain-here' ),
						'id'    => 'opt-textarea',
						'type'  => 'textarea',
					),
				),
			),

			array(
				'title'  => esc_html__( 'Color Field', 'your-domain-here' ),
				'desc'   => esc_html__( 'Redux Framework was created with the developer in mind. It allows for any theme developer to have an advanced theme panel with most of the features a developer would need. For more information check out the Github repo at:', 'your-domain-here' ) . '  <a href="https://github.com/ReduxFramework/Redux-Framework">https://github.com/ReduxFramework/Redux-Framework</a>',
				'icon'   => 'el-icon-pencil',
				'id'     => 'home-layout',
				'fields' => array(
					array(
						'id'       => 'opt-color',
						'type'     => 'color',
						'title'    => __( 'Color Field', 'your-domain-here' ),
						'default'  => '#333333',
						'required' => array( 'opt-layout', '=', 'on' ),
					),
				),
			),
		),
	)
);

Redux_Metaboxes::set_box(
	$opt_name,
	array(
		'id'         => 'opt-metaboxes-2',
		'post_types' => array( 'page', 'post' ),
		'position'   => 'side', // normal, advanced, side.
		'priority'   => 'high', // high, core, default, low.
		'sections'   => array(
			array(
				'icon_class' => 'icon-large',
				'icon'       => 'el-icon-home',
				'fields'     => array(
					array(
						'title'   => esc_html__( 'Cross Box Required', 'your-domain-here' ),
						'desc'    => esc_html__( 'Required arguments work across metaboxes! Click on Color Field under Metabox Options then adjust this field to see the fields within show or hide.', 'your-domain-here' ),
						'id'      => 'opt-layout',
						'type'    => 'radio',
						'options' => array(
							'on'  => esc_html__( 'On', 'your-domain-here' ),
							'off' => esc_html__( 'Off', 'your-domain-here' ),
						),
						'default' => 'on',
					),
				),
			),
		),
	)
);
