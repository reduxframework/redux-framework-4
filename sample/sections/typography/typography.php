<?php
/**
 * Redux Framework typography config.
 * For full documentation, please visit: http://docs.redux.io/
 *
 * @package Redux Framework
 */

defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title'  => esc_html__( 'Typography', 'your-textdomain-here' ),
		'id'     => 'typography',
		'desc'   => esc_html__( 'For full documentation on this field, visit: ', 'your-textdomain-here' ) . '<a href="//docs.redux.io/core/fields/typography/" target="_blank">docs.redux.io/core/fields/typography/</a>',
		'icon'   => 'el el-font',
		'fields' => array(
			array(
				'id'                => 'opt-typography-body',
				'type'              => 'typography',
				'title'             => esc_html__( 'Body Font', 'your-textdomain-here' ),
				'subtitle'          => esc_html__( 'Specify the body font properties.', 'your-textdomain-here' ),
				'google'            => true,
				'font_family_clear' => false,
				'default'           => array(
					'color'       => '#dd9933',
					'font-size'   => '30px',
					'font-family' => 'Arial, Helvetica, sans-serif',
					'font-weight' => 'Normal',
				),
				'output'            => array( '.site-description, .entry-title' ),
			),
			array(
				'id'          => 'opt-typography',
				'type'        => 'typography',
				'title'       => esc_html__( 'Typography h2.site-description', 'your-textdomain-here' ),

				// Use if you want to hook in your own CSS compiler.
				'compiler'    => true,

				// Select a backup non-google font in addition to a google font.
				'font-backup' => true,

				// Enable all Google Font style/weight variations to be added to the page.
				'all-styles'  => true,
				'all-subsets' => true,
				'units'       => 'px',
				'subtitle'    => esc_html__( 'Typography option with each property can be called individually.', 'your-textdomain-here' ),
				'default'     => array(
					'color'       => '#333',
					'font-style'  => '700',
					'font-family' => 'Abel',
					'google'      => true,
					'font-size'   => '33px',
					'line-height' => '40px',
				),
				// Disable google fonts.
				// 'google'      => false,
				// Includes font-style and weight. Can use font-style or font-weight to declare.
				// 'font-style'    => false,
				// Only appears if google is true and subsets not set to false.
				// 'subsets'       => false,
				// Hide or show the font size input.
				// 'font-size'     => false,
				// Hide or show the line height input.
				// 'line-height'   => false,
				// Hide or show the word spacing input. Defaults to false.
				// 'word-spacing'  => true,
				// Hide or show the word spacing input. Defaults to false.
				// 'letter-spacing'=> true,
				// Hide or show the font color picker.
				// 'color'         => false,
				// Disable the font previewer
				// 'preview'       => false,
				// An array of CSS selectors to apply this font style to dynamically
				// 'output'      => array( 'h2.site-description, .entry-title' ),
				// An array of CSS selectors to apply this font style to dynamically
				// 'compiler'    => array( 'h2.site-description-compiler' ),
				// .
			),
		),
	)
);
