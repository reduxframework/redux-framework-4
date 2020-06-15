<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Templates overrides for pages.
 *
 * @since 4.0.0
 * @package Redux Framework
 */

namespace ReduxTemplates;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Redux Templates Templates Class
 *
 * @since 4.0.0
 */
class Templates {

	/**
	 * ReduxTemplates Template.
	 *
	 * @since 4.0.0
	 */
	public function __construct() {
		// Include ReduxTemplates default template without wrapper.
		add_filter( 'template_include', array( $this, 'template_include' ) );

		// Add ReduxTemplates supported Post type in page template.
		$post_types = get_post_types();
		if ( ! empty( $post_types ) ) {
			foreach ( $post_types as $post_type ) {
				add_filter( "theme_{$post_type}_templates", array( $this, 'add_templates' ) );
			}
		}
	}

	/**
	 * Include the template
	 *
	 * @param string $template Template type.
	 *
	 * @return string
	 * @since 4.0.0
	 */
	public function template_include( $template ) {
		if ( is_singular() ) {
			$page_template = get_post_meta( get_the_ID(), '_wp_page_template', true );
			if ( 'redux-templates_full_width' === $page_template ) {
				$template = REDUXTEMPLATES_DIR_PATH . 'core/templates/template-full-width.php';
			} elseif ( 'redux-templates_contained' === $page_template ) {
				$template = REDUXTEMPLATES_DIR_PATH . 'core/templates/template-contained.php';
			} elseif ( 'redux-templates_canvas' === $page_template ) {
				$template = REDUXTEMPLATES_DIR_PATH . 'core/templates/template-canvas.php';
			}
		}

		return $template;
	}

	/**
	 * Hook to add the templates to the dropdown
	 *
	 * @param array $post_templates Default post templates array.
	 *
	 * @return array
	 * @since 4.0.0
	 */
	public function add_templates( $post_templates ) {
		$post_templates['redux-templates_contained']  = __( 'Redux Contained', 'redux-templates' );
		$post_templates['redux-templates_full_width'] = __( 'Redux Full Width', 'redux-templates' );
		$post_templates['redux-templates_canvas']     = __( 'Redux Canvas', 'redux-templates' );

		return $post_templates;
	}
}
