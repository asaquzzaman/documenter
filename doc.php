<?php
/**
 * Plugin Name: WP Documenter
 * Plugin URI:
 * Description: Documentation is now easy way
 * Author: asaquzzaman
 * Version: 0.1
 * Author URI: http://mishubd.com
 * License: GPL2
 * TextDomain: doc
 */

/**
 * Copyright (c) 2013 asaquzzaman (email: joy.mishu@gmail.com). All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 * **********************************************************************
 */

/**
 * Autoload class files on demand
 *
 * @param string $class requested class name
 */
function doc_autoload( $class ) {

    if ( stripos( $class, 'DOC_' ) !== false ) {

        $admin = ( stripos( $class, '_Admin_' ) !== false ) ? true : false;

        if ( $admin ) {
            $class_name = str_replace( array('DOC_Admin_', '_'), array('', '-'), $class );
            $filename = dirname( __FILE__ ) . '/admin/' . strtolower( $class_name ) . '.php';
        } else {
            $class_name = str_replace( array('DOC_', '_'), array('', '-'), $class );
            $filename = dirname( __FILE__ ) . '/class/' . strtolower( $class_name ) . '.php';
        }
        if ( file_exists( $filename ) ) {
            require_once $filename;
        }
    }
}
spl_autoload_register( 'doc_autoload' );

require_once dirname( __FILE__ ) . '/include/' . 'function.php';

class Wp_doc {
	function __construct() {
        $this->instantiate();
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'rad_scripts' ) );
	}

    function rad_scripts() {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_style( 'doc-read', plugins_url( 'assets/css/read.css', __FILE__ ), false, false, 'all' );
        wp_enqueue_script( 'lockfixed-sticky-scroll', plugins_url( 'assets/js/stickyMojo.js', __FILE__ ), array( 'jquery' ), false, true );
        wp_enqueue_script( 'doc-scri', plugins_url( 'assets/js/jquery.jpanelmenu.js', __FILE__ ), array( 'jquery' ), false, true );
        wp_enqueue_script( 'doc-read', plugins_url( 'assets/js/read.js', __FILE__ ), array( 'jquery' ), false, true );
    }

	function admin_menu() {
		$capability = 'read';
		$menu = add_menu_page( __( 'Documenter', 'wpuf' ), __( 'Documenter', 'wpuf' ), $capability, 'doc-documenter', array($this, 'admin_page_handler'), 'dashicons-exerpt-view' );
	}

    function scripts() {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_script( 'doc-menu-nested', plugins_url( 'assets/js/jquery.nestable.js', __FILE__ ), array( 'jquery' ), false, true );
        wp_enqueue_script( 'doc-scripts', plugins_url( 'assets/js/doc.js', __FILE__ ), array( 'jquery' ), false, true );
        wp_localize_script( 'doc-scripts', 'doc', array(
            'ajax_url'    => admin_url( 'admin-ajax.php' ),
            '_wpnonce'    => wp_create_nonce( 'doc_nonce' ),
            'is_admin'    => is_admin() ? true : false,
        ));

        wp_enqueue_style( 'nestable', plugins_url( 'assets/css/nestable.css', __FILE__ ), false, false, 'all' );
        wp_enqueue_style( 'font-awesome', plugins_url( 'assets/css/font-awesome.css', __FILE__ ), false, false, 'all' );
        wp_enqueue_style( 'doc-style', plugins_url( 'assets/css/doc.css', __FILE__ ), false, false, 'all' );
    }

    function instantiate() {
        DOC_Admin_Admin_doc::getInstance();
        DOC_Ajax::getInstance();
        DOC_Read::getInstance();
    }

	function register_post_type() {
		register_post_type( 'doc_documenter', array(
            'label' => __( 'documenter', 'doc' ),
            'description' => __( 'documenter', 'doc' ),
            'public' => false,
            'show_in_admin_bar' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'show_in_admin_bar' => false,
            'show_ui' => true,
            'show_in_menu' => 'doc-documenter', //false,
            'capability_type' => 'post',
            'hierarchical' => false,
            'rewrite' => array('slug' => 'doc_documenter'),
            'query_var' => true,
            'supports' => array('title', 'editor'),
            'labels' => array(
                'name' => __( 'documenter', 'doc' ),
                'singular_name' => __( 'documenter', 'doc' ),
                'menu_name' => __( 'documenter', 'doc' ),
                'add_new' => __( 'Add documenter', 'doc' ),
                'add_new_item' => __( 'Add New documenter', 'doc' ),
                'edit' => __( 'Edit', 'doc' ),
                'edit_item' => __( 'Edit documenter', 'doc' ),
                'new_item' => __( 'New documenter', 'doc' ),
                'view' => __( 'View documenter', 'doc' ),
                'view_item' => __( 'View documenter', 'doc' ),
                'search_items' => __( 'Search documenter', 'doc' ),
                'not_found' => __( 'No documenter Found', 'doc' ),
                'not_found_in_trash' => __( 'No documenter Found in Trash', 'doc' ),
                'parent' => __( 'Parent documenter', 'doc' ),
            ),
        ));

        register_post_type( 'doc_section', array(
            'label' => __( 'section', 'doc' ),
            'description' => __( 'section', 'doc' ),
            'public' => false,
            'show_in_admin_bar' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'show_in_admin_bar' => false,
            'show_ui' => true,
            'show_in_menu' => 'doc-section', //false,
            'capability_type' => 'post',
            'hierarchical' => false,
            'rewrite' => array('slug' => 'doc_section'),
            'query_var' => true,
            'supports' => array('title', 'editor'),
            'labels' => array(
                'name' => __( 'section', 'doc' ),
                'singular_name' => __( 'section', 'doc' ),
                'menu_name' => __( 'section', 'doc' ),
                'add_new' => __( 'Add section', 'doc' ),
                'add_new_item' => __( 'Add New section', 'doc' ),
                'edit' => __( 'Edit', 'doc' ),
                'edit_item' => __( 'Edit section', 'doc' ),
                'new_item' => __( 'New section', 'doc' ),
                'view' => __( 'View section', 'doc' ),
                'view_item' => __( 'View section', 'doc' ),
                'search_items' => __( 'Search section', 'doc' ),
                'not_found' => __( 'No section Found', 'doc' ),
                'not_found_in_trash' => __( 'No section Found in Trash', 'doc' ),
                'parent' => __( 'Parent section', 'doc' ),
            ),
        ));
	}
}

new Wp_doc();