;(function($) {
    var Documentation = {
        init: function() {
            this.sectionMenu();
            this.buttonControl();doc-section-edit
            $('.doc-section-wrap').on( 'click', '.doc-section-submit', this.newSectionForm );
            $('.doc-section-wrap').on( 'click', '.doc-udate-section-submit', this.newSectionForm );
            $('#doc-metabox-section-menu').on( 'click', '.doc-section-delete', this.SectionDelete );
            $('#doc-metabox-section-menu').on( 'click', '.doc-section-edit', this.SectionEdit );
            $('.doc-section-wrap').on( 'click', '.doc-cancel-section-submit', this.updateSectionCancel );
        },

        buttonControl: function() {
            var section_id = $('input[name="section_ID"]').val();
            if ( typeof section_id !== "undefined" && section_id !== '' ) {
                $('.doc-section-wrap .doc-section-submit').hide();
                $('.doc-section-wrap .doc-udate-section-submit').show();
                $('.doc-section-wrap .doc-cancel-section-submit').show();
            }
        },


        
        customtinyMCE: function(id) {

            tinyMCE.init({
                skin : "wp_theme",
                mode : "exact",
                elements : id,
                theme: "modern",
                menubar: false,
                toolbar1: 'bold,italic,underline,blockquote,strikethrough,bullist,numlist,alignleft,aligncenter,alignright,undo,redo,link,unlink,spellchecker,wp_fullscreen',
                plugins: "wpfullscreen,charmap,colorpicker,hr,lists,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpautoresize,wpeditimage,wpgallery,wplink,wpdialogs,wpview"
            });
        },

        SectionEdit: function() {
            var self = $(this),
                data = {
                    action : 'section_edit',
                    section_id:self.closest('li').data('id'),
                    _wpnonce: doc._wpnonce
                };

            $.post(doc.ajax_url, data, function(res) {
                if ( res.success ) {

                    $('input[name="section_ID"]').val(res.data.post.ID);
                    $('input[name="section_title"]').val(res.data.post.post_title);
                    $('#doc-section-editor').html(res.data.post.post_content);
                    tinymce.execCommand( 'mceRemoveEditor', true, 'doc-section-editor' );
                    tinymce.execCommand( 'mceAddEditor', true, 'doc-section-editor' );
                    tinyMCE.get('doc-section-editor').setContent(res.data.post.post_content);

                    $('.doc-section-wrap .doc-section-submit').hide();
                    $('.doc-section-wrap .doc-udate-section-submit').show();
                    $('.doc-section-wrap .doc-cancel-section-submit').show();

                    var content_wrap = $('.doc-section-menu-wrap').offset().top+180;
                    Documentation.scroll(content_wrap);                    
                }

            });
        },

        SectionDelete: function() {
            if ( !confirm('Are you sure!') ) {
                return;
            }
            var self = $(this),
                data = {
                    action: 'section_delete',
                    section_id:self.closest('li').data('id'),
                    doc_id: $('input[name="post_ID"]').val(),
                    _wpnonce: doc._wpnonce
                };
            $.post(doc.ajax_url, data, function(res) {
                Documentation.menuDeligation(res);

                if ( $('input[name="section_ID"]').val() == res.data.section_id ) {

                    Documentation.updateSectionCancel();
                }
            });
        },

        makeSortableSection: function() {
            $('.doc-dd').on('change', function() {
                var self = $(this),
                    li = self.children('ol').children('li');
                Documentation.nestedOl(li);
            });
        },

        nestedOl: function(li) {
            var liparent = [];
            $.each( li, function( index, value ) {

                var ol_depath = $(value).find('ol').length,
                    all_ol = $(value).find('ol'),
                    list_id = $(value).data('id'),
                    list_obj = {
                        li_id : list_id,
                        parent_id : 0
                    };
                liparent.push(list_obj);

                for ( var i = 0; i < ol_depath; i++ ) {
                    var olinsideli= $(all_ol[i]).children('li');
                    var paent_id = $(all_ol[i]).closest('li').data('id');

                    $.each( olinsideli, function( key, livalue ) {
                        var child_li_id = $(livalue).data('id'),
                            pushObjecct = {
                                li_id : child_li_id,
                                parent_id : paent_id
                            };

                        liparent.push(pushObjecct);

                    });
                }
            });

            var data = {
                action: 'menu_rearrange',
                documenter_id: $('input[name="post_ID"]').val(),
                menu: liparent,
                _wpnonce: doc._wpnonce
            }

            $.post(doc.ajax_url, data);
        },

        saveOrder: function(order) {
            var data = {
                items: order,
                action: 'doc_section_order',
                _wpnonce: doc._wpnonce
            };

            $.post(doc.ajax_url, data);
        },

        sectionMenu: function() {
            var post_type = $('input[name="post_type"]').val();
            if ( post_type != 'doc_documenter' ) {
                return;
            }
            var data = {
                action: 'section_menu',
                post_id: $('input[name="post_ID"]').val(),
                post_type: post_type,
                _wpnonce: doc._wpnonce
            }

            $.post( doc.ajax_url, data, function( res ) {
                if ( res.success ) {
                    Documentation.menuDeligation(res);
                }
            });
        },

        menuDeligation: function(res) {
            $('.doc-section-menu-wrap').html( res.data.menu );
            $('#doc-nestable').nestable();
            Documentation.makeSortableSection();

        },

        updateSectionCancel: function(e) {
            if (typeof e != 'undefined') {
                e.preventDefault();
            }

            var title_field = $('input[name="section_title"]'),
                content_field = $('textarea[name="section_desc"]');
            $('input[name="section_ID"]').val('');
            title_field.val('');
            tinyMCE.get('doc-section-editor').setContent('');
            title_field.attr( 'placeholder','Section Title' );
            $('.doc-section-wrap .doc-section-submit').show();
            $('.doc-section-wrap .doc-udate-section-submit').hide();
            $('.doc-section-wrap .doc-cancel-section-submit').hide();
        },

        newSectionForm: function(e) {
            e.preventDefault();
            tinyMCE.triggerSave();
            var title_field = $('input[name="section_title"]'),
                content_field = $('textarea[name="section_desc"]'),
                title = title_field.val();

            if ( title == '' || title === null) {
                alert( 'Please Insert Section Title' );
                return;
            }

            var data = {
                action: 'new_documentation',
                section_title: title,
                section_desc: tinyMCE.get('doc-section-editor').getContent(), //content_field.val(),
                post_id: $('input[name="post_ID"]').val(),
                section_id: $('input[name="section_ID"]').val(),
                _wpnonce: doc._wpnonce
            }

            $('.doc-spinner-section').addClass('doc-spinner');

            $.post( doc.ajax_url, data, function( res ) {
                $('.doc-spinner-section').removeClass('doc-spinner');
                if( res.success ) {
                    $('input[name="section_ID"]').val('');
                    title_field.val('');
                    tinyMCE.get('doc-section-editor').setContent('');

                    title_field.attr( 'placeholder','Section Title' );

                    Documentation.menuDeligation(res);

                    $('.doc-section-wrap .doc-section-submit').show();
                    $('.doc-section-wrap .doc-udate-section-submit').hide();
                    $('.doc-section-wrap .doc-cancel-section-submit').hide();

                    var section_menu_top = $('#doc-metabox-section-menu').offset().top-100,
                        section_update_wrap = $('.doc-success-section');

                    section_update_wrap.addClass('doc-section-update');
                    section_update_wrap.html(res.data.msg).show();

                    Documentation.scroll(section_menu_top);

                    setTimeout(function() {
                        section_update_wrap.fadeOut('500', function() {
                            section_update_wrap.removeClass('doc-section-update');
                        }).html('');

                    }, 3000);
                }
            });
        },

        scroll: function($scrolltop) {
            $('body,html').animate({
                scrollTop: $scrolltop
            }, 800);
        }
    }

    Documentation.init();

})(jQuery);