<?php

class DOC_Read {
    private static $instance;

    static function getInstance() {
        if ( !self::$instance) {
            self::$instance = new DOC_Read();
        }

        return self::$instance;
    }
    function __construct() {
        add_shortcode( 'doc_documenter', array( $this, 'read_document' ) );
    }

    function read_document( $atts ) {
        extract( shortcode_atts( array('doc_id' => 0 ), $atts ) );
        $section = doc_get_section( $doc_id );
        $menu = doc_get_menu_meta($doc_id);
        ob_start();
            require_once dirname (__FILE__) . '/../views/read.php';
        return ob_get_clean();
    }

    function doc_show_child_content( $post_id, $section, $menu_child ) {
        foreach ( $menu_child[$post_id] as $key => $id ) {
            if ( isset($section[$id]) ) {
                ?>
                <div class="doc-menu-id-<?php echo $id; ?>" id="doc-cotent-id-<?php echo $id; ?>">
                    <h1 class="doc-section-title"><?php echo $section[$id]->post_title; ?></h1>
                    <div><?php echo $section[$id]->post_content; ?></div>
                </div>
                <?php
                if ( array_key_exists( $id, $menu_child ) ) {
                    $this->doc_show_child_content( $id, $section, $menu_child );
                }
            }
        }
    }
}