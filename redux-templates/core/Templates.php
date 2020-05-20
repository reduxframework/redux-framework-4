<?php

namespace StarterBlocks;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Templates {

    /**
     * StarterBlocks Template.
     *
     * @since 1.0.0
     */
    public function __construct() {
        //Include StarterBlocks default template without wrapper
        add_filter( 'template_include', array( $this, 'template_include' ) );

        //Add StarterBlocks supported Post type in page template
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
            if ( $page_template === 'starterblocks_full_width' ) {
                $template = STARTERBLOCKS_DIR_PATH . 'core/templates/template-full-width.php';
            }
            if ( $page_template === 'starterblocks_contained' ) {
                $template = STARTERBLOCKS_DIR_PATH . 'core/templates/template-contained.php';
            }
            if ( $page_template === 'starterblocks_canvas' ) {
                $template = STARTERBLOCKS_DIR_PATH . 'core/templates/template-canvas.php';
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
        $post_templates['starterblocks_contained']  = __( 'StarterBlocks Contained', 'starterblocks' );
        $post_templates['starterblocks_full_width'] = __( 'StarterBlocks Full Width', 'starterblocks' );
        $post_templates['starterblocks_canvas']     = __( 'StarterBlocks Canvas', 'starterblocks' );

        return $post_templates;
    }
}
