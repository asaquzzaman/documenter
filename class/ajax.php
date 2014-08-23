<?php
class DOC_Ajax {
    private static $instance;

    public static function getInstance() {
        if( ! self::$instance ) {
            self::$instance = new DOC_Ajax();
        }

        return self::$instance;
    }

    function __construct() {
    	add_action( 'wp_ajax_new_documentation', array( $this, 'new_documentation' ) );
        add_action( 'wp_ajax_section_menu', array( $this, 'section_menu' ) );
        add_action( 'wp_ajax_doc_section_order', array( $this, 'doc_section_order' ) );
        add_action( 'wp_ajax_menu_rearrange', array( $this, 'menu_rearrange' ) );
        add_action( 'wp_ajax_section_delete', array( $this, 'section_delete' ) );
        add_action( 'wp_ajax_section_edit', array( $this, 'section_edit' ) );

    }

    function section_edit() {
        check_ajax_referer( 'doc_nonce' );
        $post = get_post( $_POST['section_id'] );

        wp_send_json_success( array( 'post' => $post ) );
    }

    function section_delete() {
        check_ajax_referer( 'doc_nonce' );
        wp_delete_post( $_POST['section_id'], true );
        ob_start();
            doc_get_section_menu( $_POST['doc_id'] );
        wp_send_json_success( array('menu' => ob_get_clean(), 'section_id' => $_POST['section_id'] ) );
    }

    function menu_rearrange() {
        check_ajax_referer( 'doc_nonce' );
        $menu = array(
            'child' => array(),
            'parent' => array()
        );
        foreach ($_POST['menu'] as $key => $menu_id ) {

            if ( $menu_id['parent_id'] != 0 ) {

                if ( array_key_exists($menu_id['parent_id'], $menu['child']) ) {
                    array_push( $menu['child'][$menu_id['parent_id']], $menu_id['li_id'] );

                } else {
                    $menu['child'][$menu_id['parent_id']] = array( $menu_id['li_id'] );
                }
            }

            if ( $menu_id['parent_id'] == 0 ) {
                $menu['parent'][$menu_id['li_id']] = $menu_id['li_id'];
            }
        }

        update_post_meta( $_POST['documenter_id'], '_documenter_menu', $menu );
    }

    function doc_section_order() {

        check_ajax_referer( 'doc_nonce' );

        if ( $_POST['items'] ) {
            foreach ($_POST['items'] as $index => $section_id) {
                wp_update_post( array('ID' => $section_id, 'menu_order' => $index) );
            }
        }

        exit;
    }

    function section_menu() {
        check_ajax_referer( 'doc_nonce' );
        if ( !isset( $_POST['post_type'] ) || $_POST['post_type'] != 'doc_documenter'  ) {
            wp_send_json_success();
        }
        $doc_id = $_POST['post_id'];
        ob_start();
        doc_get_section_menu( $doc_id );
        wp_send_json_success( array( 'menu' => ob_get_clean() ) );
    }



    function new_documentation() {
    	check_ajax_referer( 'doc_nonce' );
        $is_update = empty( $_POST['section_id'] ) ? false : true;

        $section_title = $_POST['section_title'];
        $section_content = $_POST['section_desc'];
        $doc_id = $_POST['post_id'];

        $args = array(
            'post_title' => $section_title,
            'post_content' => $section_content,
            'post_parent' =>  $doc_id,
            'post_status' => 'publish',
            'post_type' => 'doc_section'
        );

        if ( $is_update ) {
            $args['ID'] = $_POST['section_id'];
            $post_id = wp_update_post( $args );

        } else {
            $post_id = wp_insert_post( $args );
            $menu = get_post_meta( $doc_id, '_documenter_menu', true );
            if ( empty($menu) ) {
                $menu = array(
                    'child' => array(),
                    'parent' => array( $post_id => $post_id )
                );
            } else {
                array_push($menu['parent'], $post_id );
            }

            update_post_meta( $doc_id, '_documenter_menu', $menu );
        }

        ob_start();
            doc_get_section_menu( $doc_id );
        wp_send_json_success( array('msg' => __('Section Update Successfully'), 'menu' => ob_get_clean() ) );

    }

}

