<?php

class DOC_Admin_Admin_doc {

	private static $instance;

	static function getInstance() {
		if ( !self::$instance) {
			self::$instance = new DOC_Admin_Admin_doc();
		}

		return self::$instance;
	}

	function __construct() {
		add_action( 'add_meta_boxes_doc_documenter', array( $this, 'add_meta_box_document_post' ) );
		add_filter( 'enter_title_here', array( $this, 'change_default_title' ) );
		add_filter('manage_doc_documenter_posts_columns', array( $this, 'documenter_columns_head') );

        add_action('manage_doc_documenter_posts_custom_column', array( $this, 'documenter_columns_content' ),10, 2 );

	}

	function documenter_columns_content( $head, $post_id ) {
		if ( $head == 'shortcode' ) {
			echo '[doc_documenter doc_id="'.$post_id.'"]';
		}

	}

	function documenter_columns_head( $title ) {
		unset($title['date']);
		$title['shortcode'] = __( 'Shortcode', 'doc' );
		$title['date'] = __( 'Date', 'doc' );
		return $title;
	}

	function change_default_title($title) {
		$screen = get_current_screen();

        if ( 'doc_documenter' == $screen->post_type ) {
            $title = __( 'Documentation Title', 'doc' );
        }

        return $title;
	}

	function add_meta_box_document_post() {
		add_meta_box( 'doc-metabox-documentation', __( 'Documentation', 'doc' ), array( $this, 'admin_doc' ), 'doc_documenter', 'side', 'high' );
		add_meta_box( 'doc-metabox-section', __( 'Section', 'doc' ), array( $this, 'doc_section' ), 'doc_documenter', 'normal', 'high' );
		add_meta_box( 'doc-metabox-section-menu', __( 'Section Menu', 'doc' ), array( $this, 'doc_section_menu' ), 'doc_documenter', 'side', 'low' );
	}

	function admin_doc() {
		?>
		<a href="#" class="doc-add-new-section button button-primary"><?php _e( 'Add new section', 'doc' ); ?></a>
		<?php

	}

	function doc_section() {
		?>
		<div class="doc-section-wrap">
			<input type="hidden" name="section_ID" value="">
			<input type="text"  value="" placeholder="<?php _e( 'Section Title', 'doc' ); ?>" size="30" name="section_title">

			<?php wp_editor( __( 'Section Description', 'doc' ), 'doc-section-editor', array( 'textarea_name' => 'section_desc' ) ); ?>
			<a href="#" class="button button-primary doc-section-submit"><?php _e( 'Add Section', 'doc' ); ?></a>
		</div>
		<?php
	}

	function doc_section_menu() {
		?>
		<div class="doc-section-menu-wrap">


		</div>
		<?php
	}
}