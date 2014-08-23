<?php
function doc_excerpt( $text, $length, $append = '...' ) {
    $text = wp_strip_all_tags( $text, true );
    $count = mb_strlen( $text );
    $text = mb_substr( $text, 0, $length );

    if( $count > $length ) {
        $text = $text . $append;
    }

    return $text;
}

function doc_get_section( $doc_id ) {
    $args = array(
        'numberposts'      => -1,
        'orderby'          => 'menu_order',
        'order'            => 'ASC',
        'post_type'        => 'doc_section',
        'post_parent'      => $doc_id,
        'post_status'      => 'publish',
    );

    $posts = get_posts( $args );
    $section = array();
    foreach ( $posts as $key => $post ) {
        $section[$post->ID] = $post;
    }
    return $section;
}

function doc_get_menu_meta($doc_id) {
    return get_post_meta( $doc_id, '_documenter_menu', true );
}

function doc_get_section_menu( $doc_id ) {
    $menu = doc_get_menu_meta($doc_id);
    $section = doc_get_section( $doc_id );

    $menu['parent'] = isset( $menu['parent'] ) ? $menu['parent'] : array();
    ?>
    <div class="doc-dd" id="doc-nestable">
        <ol class="doc-dd-list">
            <?php
            foreach ( $menu['parent'] as $post_id ) {
                if ( isset($section[$post_id]) ) {
                ?>
                <li class="doc-dd-item doc-item-<?php echo $post_id; ?>" data-id="<?php echo $post_id; ?>">
                    <div class="doc-menu-text-wrap">
                        <div class="doc-delete"><i class="fa fa-times doc-section-delete"></i></div>
                        <span class="doc-section-edit">
                            <a href="<?php echo '#doc-cotent-id-' . $post_id; ?>">
                                <?php echo doc_excerpt( $section[$post_id]->post_title, 20 ); ?>
                            </a>

                        </span>

                        <div class="doc-menu-action-wrap">
                            <i class="doc-dd-handle   fa fa-arrows-alt"></i>

                        </div>
                        <div class="doc-clear"></div>
                    </div>
                    <?php
                    if ( array_key_exists( $post_id, $menu['child'] ) ) {
                        doc_show_child_menu( $post_id, $section, $menu['child'] );
                    }
                    ?>
                </li>
                <?php
                }
            }
            ?>
        </ol>
    </div>
    <?php
}

function doc_show_child_menu( $post_id, $section, $menu_child ) {

    ?>
    <ol class="doc-dd-list">
        <?php
        foreach ( $menu_child[$post_id] as $key => $id ) {
            if ( isset( $section[$id] ) ) {
            ?>
            <li class="doc-dd-item doc-item-<?php echo $section[$id]->ID; ?>" data-id="<?php echo $section[$id]->ID; ?>">
                <div class="doc-menu-text-wrap">
                    <div class="doc-delete"><i class="fa fa-times doc-section-delete"></i></div>
                    <span class="doc-section-edit">
                        <a href="<?php echo '#doc-cotent-id-' . $id; ?>">
                        <?php echo doc_excerpt( $section[$id]->post_title, 15 ); ?>
                        </a>

                    </span>

                    <div class="doc-menu-action-wrap">
                        <i class="doc-dd-handle fa fa-arrows-alt"></i>

                    </div>
                    <div class="doc-clear"></div>
                </div>
                <?php
                if ( array_key_exists( $id, $menu_child ) ) {
                    doc_show_child_menu( $id, $section, $menu_child );
                }
                ?>
            </li>
            <?php
            }
        }
        ?>
    </ol>
    <?php
}

?>