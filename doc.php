<?php
/**
 * Plugin Name: Wp Documenter
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
        wp_enqueue_script( 'doc-scripts', plugins_url( 'assets/js/doc.js', __FILE__ ), array( 'jquery' ), false, true );

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
        ) );
	}
}

new Wp_doc();