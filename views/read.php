<div class="doc-read-wrap">
    <div class="doc-read-menu">
        <?php doc_get_section_menu( $doc_id ); ?>
    </div>

    <div class="doc-read-content">
        <?php $document = get_post($doc_id); ?>
        <div class="doc-content-wrap">
            <h1 class="doc-title"><?php echo $document->post_title; ?></h1>
            <div class="doc-content"><?php echo do_shortcode( $document->post_content );?></div>
        </div>
        <?php
            foreach ( $menu['parent'] as $post_id ) {
                if ( isset($section[$post_id]) ) {
                    ?>
                    <div class="doc-menu-id-<?php echo $post_id; ?>" id="doc-cotent-id-<?php echo $post_id; ?>">
                        <h1 class="doc-section-title"><?php echo $section[$post_id]->post_title; ?></h1>
                        <div><?php echo do_shortcode( $section[$post_id]->post_content ); ?></div>
                    </div>
                    <?php
                    if ( array_key_exists( $post_id, $menu['child'] ) ) {
                        $this->doc_show_child_content( $post_id, $section, $menu['child'] );
                    }
                }
            }
        ?>

        <div class="doc-offset-bottom"></div>
    </div>
    <div class="doc-clear"></div>
</div>