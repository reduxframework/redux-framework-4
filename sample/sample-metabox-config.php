<?php
/**
 * Redux Framework Sample Metabox Config File
 * For full documentation, please visit: http://docs.reduxframework.com/
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
		'id'         => 'demo-layout',
		'title'      => __( 'Cool Options', 'your-domain-here' ),
		'post_types' => array( 'page', 'post', 'acme_product' ),
		'position'   => 'normal', // normal, advanced, side.
		'priority'   => 'high', // high, core, default, low.
		'sections'   => array(
			array(
				'title'  => __( 'Home Settings', 'your-domain-here' ),
				'id'     => 'home-settings',
				'desc'   => esc_html__( 'Redux Framework was created with the developer in mind. It allows for any theme developer to have an advanced theme panel with most of the features a developer would need. For more information check out the Github repo at: <a href="https://github.com/ReduxFramework/Redux-Framework">https://github.com/ReduxFramework/Redux-Framework</a>', 'your-domain-here' ),
				'icon'   => 'el-icon-home',
				'fields' => array(
					array(
						'id'       => 'webFonts',
						'type'     => 'media',
						'title'    => __( 'Web Fonts', 'your-domain-here' ),
						'compiler' => 'true',
						'mode'     => false,
						// Can be set to false to allow any media type, or can also be set to any mime type.
						'desc'     => __( 'Basic media uploader with disabled URL input field.', 'your-domain-here' ),
						'subtitle' => __( 'Upload any media using the WordPress native uploader', 'your-domain-here' ),
					),
					array(
						'id'       => 'section-media-start',
						'type'     => 'section',
						'title'    => __( 'Media Options', 'your-domain-here' ),
						'subtitle' => __( 'With the "section" field you can create indent option sections.' ),
						'indent'   => true, // Indent all options below until the next 'section' option is set.
					),
					array(
						'id'       => 'media',
						'type'     => 'media',
						'url'      => true,
						'title'    => __( 'Media w/ URL', 'your-domain-here' ),
						'compiler' => 'true',
						'desc'     => __( 'Basic media uploader with disabled URL input field.', 'your-domain-here' ),
						'subtitle' => __( 'Upload any media using the WordPress native uploader', 'your-domain-here' ),
						'default'  => array( 'url' => 'http://s.wordpress.org/style/images/codeispoetry.png' ),
					),
					array(
						'id'     => 'section-media-end',
						'type'   => 'section',
						'indent' => false, // Indent all options below until the next 'section' option is set.
					),
					array(
						'id'       => 'media-nourl',
						'type'     => 'media',
						'title'    => __( 'Media w/o URL', 'your-domain-here' ),
						'desc'     => __( 'This represents the minimalistic view. It does not have the preview box or the display URL in an input box. ', 'your-domain-here' ),
						'subtitle' => __( 'Upload any media using the WordPress native uploader', 'your-domain-here' ),
					),
					array(
						'id'       => 'media-nopreview',
						'type'     => 'media',
						'preview'  => false,
						'title'    => __( 'Media No Preview', 'your-domain-here' ),
						'desc'     => __( 'This represents the minimalistic view. It does not have the preview box or the display URL in an input box. ', 'your-domain-here' ),
						'subtitle' => __( 'Upload any media using the WordPress native uploader', 'your-domain-here' ),
					),
					array(
						'id'       => 'gallery',
						'type'     => 'gallery',
						'title'    => __( 'Add/Edit Gallery', 'so-panels' ),
						'subtitle' => __( 'Create a new Gallery by selecting existing or uploading new images using the WordPress native uploader', 'so-panels' ),
						'desc'     => __( 'This is the description field, again good for additional info.', 'your-domain-here' ),
					),
					array(
						'id'      => 'slider1bDOVY23',
						'type'    => 'slider',
						'title'   => __( 'JQuery UI Slider Example 1', 'your-domain-here' ),
						'desc'    => __( 'JQuery UI slider description. Min: 1, max: 500, step: 3, default value: 45', 'your-domain-here' ),
						'default' => '45',
						'min'     => '1',
						'step'    => '3',
						'max'     => '500',
					),
					array(
						'id'      => 'slider2bc',
						'type'    => 'slider',
						'title'   => __( 'JQuery UI Slider Example 2 w/ Steps (5)', 'your-domain-here' ),
						'desc'    => __( 'JQuery UI slider description. Min: 0, max: 300, step: 5, default value: 75', 'your-domain-here' ),
						'default' => '0',
						'min'     => '0',
						'step'    => '5',
						'max'     => '300',
					),
					array(
						'id'      => 'spinner1bcd',
						'type'    => 'spinner',
						'title'   => __( 'JQuery UI Spinner Example 1', 'your-domain-here' ),
						'desc'    => __( 'JQuery UI spinner description. Min:20, max: 100, step:20, default value: 40', 'your-domain-here' ),
						'default' => '40',
						'min'     => '20',
						'step'    => '20',
						'max'     => '100',
					),
					array(
						'id'       => 'switch-on',
						'type'     => 'switch',
						'title'    => __( 'Switch On', 'your-domain-here' ),
						'subtitle' => __( 'Look, it\'s on!', 'your-domain-here' ),
						'default'  => 1,
					),
					array(
						'id'       => 'switch-off',
						'type'     => 'switch',
						'title'    => __( 'Switch Off', 'your-domain-here' ),
						'subtitle' => __( 'Look, it\'s on!', 'your-domain-here' ),
						'default'  => 0,
					),
				),
			),

			array(
				'title'      => __( 'Home Layout Sub', 'your-domain-here' ),
				'desc'       => __( 'Redux Framework was created with the developer in mind. It allows for any theme developer to have an advanced theme panel with most of the features a developer would need. For more information check out the Github repo at: <a href="https://github.com/ReduxFramework/Redux-Framework">https://github.com/ReduxFramework/Redux-Framework</a>', 'your-domain-here' ),
				'icon'       => 'el-icon-home',
				'id'         => 'home-sub',
				'subsection' => true,
				'fields'     => array(
					array(
						'id'    => 'some-text',
						'type'  => 'text',
						'title' => 'whatever',
					),
				),
			),

			array(
				'title'  => __( 'Home Layout', 'your-domain-here' ),
				'desc'   => __( 'Redux Framework was created with the developer in mind. It allows for any theme developer to have an advanced theme panel with most of the features a developer would need. For more information check out the Github repo at: <a href="https://github.com/ReduxFramework/Redux-Framework">https://github.com/ReduxFramework/Redux-Framework</a>', 'your-domain-here' ),
				'icon'   => 'el-icon-home',
				'id'     => 'home-layout',
				'fields' => array(
					array(
						'id'       => 'homepage_blocks',
						'type'     => 'sorter',
						'title'    => 'Homepage Layout Manager',
						'desc'     => 'Organize how you want the layout to appear on the homepage',
						'compiler' => 'true',
						'required' => array( 'layout', '=', '1' ),
						'options'  => array(
							'enabled'  => array(
								'highlights' => 'Highlights',
								'slider'     => 'Slider',
								'staticpage' => 'Static Page',
								'services'   => 'Services',
							),
							'disabled' => array(),
						),
					),

					array(
						'id'       => 'slides',
						'type'     => 'slides',
						'title'    => __( 'Slides Options', 'your-domain-here' ),
						'subtitle' => __( 'Unlimited slides with drag and drop sortings.', 'your-domain-here' ),
						'desc'     => __( 'This field will store all slides values into a multidimensional array to use into a foreach loop.', 'your-domain-here' ),
					),
					array(
						'id'       => 'presets',
						'type'     => 'image_select',
						'presets'  => true,
						'title'    => __( 'Preset', 'your-domain-here' ),
						'subtitle' => __( 'This allows you to set a json string or array to override multiple preferences in your theme.', 'your-domain-here' ),
						'default'  => 0,
						'desc'     => __( 'This allows you to set a json string or array to override multiple preferences in your theme.', 'your-domain-here' ),
						'options'  => array(
							'1' => array(
								'alt'     => 'Preset 1',
								'img'     => ReduxFramework::$_url . '../sample/presets/preset1.png',
								'presets' => array(
									'switch-on'     => 1,
									'switch-off'    => 1,
									'switch-custom' => 1,
								),
							),
							'2' => array(
								'alt'     => 'Preset 2',
								'img'     => ReduxFramework::$_url . '../sample/presets/preset2.png',
								'presets' => "{'slider1':'1', 'slider2':'0', 'switch-on':'0'}",
							),
						),
					),
				),
			),
		),
	)
);

Redux_Metaboxes::set_box(
	$opt_name,
	array(
		'id'         => 'demo-layout2',
		'post_types' => array( 'page', 'post', 'acme_product' ),
		'position'   => 'side', // normal, advanced, side.
		'priority'   => 'high', // high, core, default, low.
		'sections'   => array(
			array(
				'icon_class' => 'icon-large',
				'icon'       => 'el-icon-home',
				'fields'     => array(
					array(
						'title'      => __( 'Cross Box Required', 'your-domain-here' ),
						'desc'       => __( 'Required arguments work across metaboxes! Click on Home Layout under the Cool Options metabox, then adjust this field to see the fields within Home Layout visibility being modified.', 'your-domain-here' ),
						'id'         => 'layout',
						'default'    => '1',
						'type'       => 'image_select',
						'customizer' => array(),
						'options'    => array(
							'0' => ReduxFramework::$_url . 'assets/img/1c.png',
							'1' => ReduxFramework::$_url . 'assets/img/2cr.png',
						),
					),
				),
			),
		),
	)
);

Redux_Metaboxes::set_box(
	$opt_name,
	array(
		'id'         => 'page-options',
		'title'      => __( 'Page Options', 'fusion-framework' ),
		'post_types' => array( 'page', 'post', 'demo_metaboxes' ),
		'position'   => 'side', // normal, advanced, side.
		'priority'   => 'normal', // high, core, default, low.
		'sidebar'    => false, // enable/disable the sidebar in the normal/advanced positions.
		'sections'   => array(
			array(
				'icon_class' => 'icon-large',
				'icon'       => 'el-icon-home',
				'fields'     => array(
					array(
						'id'      => 'sidebar',
						'title'   => __( 'Sidebar', 'fusion-framework' ),
						'desc'    => 'This metabox is bound to the Post Format. It will only appear when the post format is set to standard.',
						'type'    => 'select',
						'data'    => 'sidebars',
						'default' => 'None',
					),
				),
			),
		),
	)
);
