<?php
/**
 * Redux Framework textarea config.
 * For full documentation, please visit: http://docs.redux.io/
 *
 * @package Redux Framework
 */

defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title'      => esc_html__( 'Textarea', 'your-domain-here' ),
		'id'         => 'basic-textarea',
		'desc'       => esc_html__( 'For full documentation on this field, visit: ', 'your-domain-here' ) . '<a href="//docs.redux.io/core/fields/textarea/" target="_blank">docs.redux.io/core/fields/textarea/</a>',
		'subsection' => true,
		'fields'     => array(
			array(
				'id'       => 'opt-textarea',
				'type'     => 'textarea',
				'title'    => esc_html__( 'Textarea Option - HTML Validated Custom', 'your-domain-here' ),
				'subtitle' => esc_html__( 'Subtitle', 'your-domain-here' ),
				'desc'     => esc_html__( 'This is the description field, again good for additional info.', 'your-domain-here' ),
				'default'  => 'Default Text',
			),
		),
	)
);
