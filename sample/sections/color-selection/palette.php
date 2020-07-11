<?php
/**
 * Redux Framework palette config.
 * For full documentation, please visit: http://docs.redux.io/
 *
 * @package Redux Framework
 */

defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title'      => esc_html__( 'Palette', 'your-textdomain-here' ),
		'desc'       => esc_html__( 'For full documentation on this field, visit: ', 'your-textdomain-here' ) . '<a href="//docs.redux.io/core/fields/palette-color/" target="_blank">docs.redux.io/core/fields/palette-color/</a>',
		'id'         => 'palette',
		'subsection' => true,
		'fields'     => array(
			array(
				'id'       => 'opt-palette-color',
				'type'     => 'palette',
				'title'    => esc_html__( 'Palette Color Option', 'your-textdomain-here' ),
				'subtitle' => esc_html__( 'Only color validation can be done on this field type', 'your-textdomain-here' ),
				'desc'     => esc_html__( 'This is the description field, again good for additional info.', 'your-textdomain-here' ),
				'default'  => 'red',
				'palettes' => array(
					'red'  => array(
						'#ef9a9a',
						'#f44336',
						'#ff1744',
					),
					'pink' => array(
						'#fce4ec',
						'#f06292',
						'#e91e63',
						'#ad1457',
						'#f50057',
					),
					'cyan' => array(
						'#e0f7fa',
						'#80deea',
						'#26c6da',
						'#0097a7',
						'#00e5ff',
					),
				),
			),
		),
	)
);
