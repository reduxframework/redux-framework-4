<?php
/**
 * Redux Framework raw config.
 * For full documentation, please visit: http://docs.redux.io/
 *
 * @package Redux Framework
 */

defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title'      => esc_html__( 'Raw', 'your-textdomain-here' ),
		'id'         => 'additional-raw',
		'desc'       => esc_html__( 'For full documentation on this field, visit: ', 'your-textdomain-here' ) . '<a href="//docs.redux.io/core/fields/raw/" target="_blank">docs.redux.io/core/fields/raw/</a>',
		'subsection' => true,
		'fields'     => array(
			array(
				'id'       => 'opt-raw_info_4',
				'type'     => 'raw',
				'title'    => esc_html__( 'Standard Raw Field', 'your-textdomain-here' ),
				'subtitle' => esc_html__( 'Subtitle', 'your-textdomain-here' ),
				'desc'     => esc_html__( 'Description', 'your-textdomain-here' ),
				'content'  => $sample_html,
			),
			array(
				'id'         => 'opt-raw_info_5',
				'type'       => 'raw',
				'full_width' => false,
				'title'      => esc_html__( 'Raw Field <code>full_width</code> False', 'your-textdomain-here' ),
				'subtitle'   => esc_html__( 'Subtitle', 'your-textdomain-here' ),
				'desc'       => esc_html__( 'Description', 'your-textdomain-here' ),
				'content'    => $sample_html,
			),
		),
	)
);
