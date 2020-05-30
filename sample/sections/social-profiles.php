<?php
/**
 * Redux Pro Social Profiles Sample config.
 * For full documentation, please visit: http://docs.reduxframework.com/
 *
 * @package Redux Pro
 */

defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title'    => esc_html__( 'Social Profiles', 'your-domain-here' ),
		'desc'     => esc_html__( 'For full documentation on this field, visit: ', 'your-domain-here' ) . '<a href="//docs.reduxframework.com/extensions/social-profiles" target="_blank">docs.reduxframework.com/extensions/social-profiles</a>',
		'subtitle' => esc_html( 'Click an icon to activate it, drag and drop to change the icon order.', 'your-domain-here' ),
		'subsection' => true,
		'fields'   => array(
			array(
				'id'              => 'opt-social-profiles',
				'type'            => 'social_profiles',
				'title'           => esc_html__( 'Social Profiles', 'your-domain-here' ),
				'subtitle'        => esc_html__( 'Click an icon to activate it, drag and drop to change the icon order.', 'your-domain-here' ),
				'hide_widget_msg' => true,
			),
		),
	)
);
