<?php
/**
 * Redux Pro Widget Areas Sample config.
 *
 * For full documentation, please visit: http://docs.reduxframework.com/
 *
 * @package Redux Pro
 */

defined( 'ABSPATH' ) || exit;

// --> Below this line not needed. This is just for demonstration purposes.
Redux::set_section(
	$opt_name,
	array(
		'title'  => esc_html__( 'Widget Areas', 'your-domain-here' ),
		'desc'   => esc_html__( 'For full documentation on this field, visit: ', 'your-domain-here' ) . '<a href="//docs.reduxframework.com/extensions/widget-areas" target="_blank">docs.reduxframework.com/extensions/widget-areas</a>',
		'subsection' => true,
		'fields' => array(
			array(
				'id'       => 'widget_areas',
				'type'     => 'info',
				'style'    => 'info',
				'notice'   => true,
				'title'    => esc_html__( 'Widget Areas is Already Running!', 'your-domain-here' ),

				// translators: %1$s: Widget Admin URL.
				'subtitle' => sprintf( esc_html__( 'To see it in action, head over to your %1$s', 'your-domain-here' ), '<a href="' . admin_url( 'widgets.php' ) . '">' . esc_html__( 'Widgets page', 'your-domain-here' ) . '</a>.' ),
			),
		),
	)
);
