<?php

namespace ReduxTemplates;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Templates {

    /**
     * ReduxTemplates Template.
     *
     * @since 1.0.0
     */
    public function __construct() {
        //Include ReduxTemplates default template without wrapper
        add_filter( 'template_include', array( $this, 'template_include' ) );

        //Add ReduxTemplates supported Post type in page template
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
     * @since 1.0.0
     *
     * @param $template
     *
     * @return string
     */
    public function template_include( $template ) {
        if ( is_singular() ) {
            $page_template = get_post_meta( get_the_ID(), '_wp_page_template', true );
            if ( $page_template === 'redux-templates_full_width' ) {
                $template = REDUXTEMPLATES_DIR_PATH . 'core/templates/template-full-width.php';
            }
            if ( $page_template === 'redux-templates_contained' ) {
                $template = REDUXTEMPLATES_DIR_PATH . 'core/templates/template-contained.php';
            }
            if ( $page_template === 'redux-templates_canvas' ) {
                $template = REDUXTEMPLATES_DIR_PATH . 'core/templates/template-canvas.php';
            }

        }

        return $template;
    }

    /**
     * Hook to add the templates to the dropdown
     *
     * @since 1.0.0
     *
     * @param $post_templates
     *
     * @return array
     */
    public function add_templates( $post_templates ) {
        $post_templates['redux-templates_contained']  = __( 'ReduxTemplates Contained', 'redux-templates' );
        $post_templates['redux-templates_full_width'] = __( 'ReduxTemplates Full Width', 'redux-templates' );
        $post_templates['redux-templates_canvas']     = __( 'ReduxTemplates Canvas', 'redux-templates' );

        return $post_templates;
    }
}
