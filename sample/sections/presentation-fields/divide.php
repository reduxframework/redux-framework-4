<?php
/**
 * Redux Framework divide config.
 * For full documentation, please visit: http://docs.redux.io/
 *
 * @package Redux Framework
 */

defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title'      => esc_html__( 'Divide', 'your-textdomain-here' ),
		'id'         => 'presentation-divide',
		'desc'       => esc_html__( 'The spacer to the section menu as seen to the left (after this section block) is the divide "section". Also the divider below is the divide "field".', 'your-textdomain-here' ) . '<br />' . __( 'For full documentation on this field, visit: ', 'your-textdomain-here' ) . '<a href="//docs.redux.io/core/fields/divide/" target="_blank">docs.redux.io/core/fields/divide/</a>',
		'subsection' => true,
		'fields'     => array(
			array(
				'id'   => 'opt-divide',
				'type' => 'divide',
			),
		),
	)
);
