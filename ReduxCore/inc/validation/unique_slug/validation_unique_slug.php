<?php

if ( !defined ( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Redux_Validation_unique_slug' ) ) {
    
    class Redux_Validation_unique_slug extends Redux_Validate {

        function validate() {
            global $wpdb, $wp_rewrite;

            $this->field['msg']              = ( isset( $this->field['msg'] ) ) ? $this->field['msg'] : esc_html__( 'That URL slug is in use, please choose another.', 'redux-framework' );
            $this->field['flush_permalinks'] = ( isset( $this->field['flush_permalinks'] ) ) ? $this->field['flush_permalinks'] : false;

            $slug = $this->value;

            $feeds = $wp_rewrite->feeds;
            if ( ! is_array( $feeds ) ) {
                $feeds = array();
            }

            // Post slugs must be unique across all posts.
            $check_sql       = "SELECT post_name FROM $wpdb->posts WHERE post_name = %s LIMIT 1";
            $post_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $slug ) );

            /**
             * Filter whether the post slug would be bad as a flat slug.
             *
             * @since 3.1.0
             *
             * @param bool   $bad_slug  Whether the post slug would be bad as a flat slug.
             * @param string $slug      The post slug.
             * @param string $post_type Post type.
             */
            if ( $post_name_check || in_array( $slug, $feeds ) || apply_filters( 'wp_unique_post_slug_is_bad_attachment_slug', false, $slug ) ) {
                $suffix = 2;
                
                do {
                    $alt_post_name   = _truncate_post_slug( $slug, 200 - ( strlen( $suffix ) + 1 ) ) . "-$suffix";
                    $post_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $alt_post_name ) );
                    
                    $suffix ++;
                } while ( $post_name_check );
                
                $slug                   = $alt_post_name;
                $this->value            = ( isset( $this->current ) ) ? $this->current : '';
                $this->field['msg']     = sprintf( $this->field['msg'], $slug );
                $this->field['current'] = $this->value;
                $this->error            = $this->field;
            } else if ( isset( $this->field['flush_permalinks'] ) && $this->field['flush_permalinks'] == true ) {
                add_action( 'init', array( $this, 'flush_permalinks' ), 99 );
            }
        }

        function flush_permalinks() {
            flush_rewrite_rules();
        }
    }
}
