<?php
/**
 * Redux Framework password config.
 * For full documentation, please visit: http://docs.redux.io/
 *
 * @package Redux Framework
 */

defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title'      => esc_html__( 'Password', 'your-textdomain-here' ),
		'id'         => 'basic-password',
		'desc'       => esc_html__( 'For full documentation on this field, visit: ', 'your-textdomain-here' ) . '<a href="//docs.redux.io/core/fields/password/" target="_blank">docs.redux.io/core/fields/password/</a>',
		'subsection' => true,
		'fields'     => array(
			array(
				'id'       => 'password',
				'type'     => 'password',
				'username' => true,
				'title'    => 'Password Field',
			),
		),
	)
);
