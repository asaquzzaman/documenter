;(function($) {
    var Documentation = {
        init: function() {
            this.sectionMenu();
            $('.doc-section-wrap').on( 'click', '.doc-section-submit', this.newSectionForm );
            $('.doc-section-wrap').on( 'click', '.doc-udate-section-submit', this.newSectionForm );
            $('#doc-metabox-section-menu').on( 'click', '.doc-section-delete', this.SectionDelete );
            $('#doc-metabox-section-menu').on( 'click', '.doc-section-edit', this.SectionEdit );
            $('.doc-section-wrap').on( 'click', '.doc-cancel-section-submit', this.updateSectionCancel );
            this.docScroll();

        },

        docScroll: function() {

            if ( !$('.doc-read-menu').length ) {
                return;
            }
            var menu_lis = $('.doc-read-menu').find('li'),
                li_id = [];
            $.each( menu_lis, function( index, value ) {
                li_id.push( $(value).data('id') );
            });



            $(window).scroll( function() {
                var self = $(this),
                    window_top = self.scrollTop(),
                    menu_content_wrap = $('.doc-read-menu'),
                    menu_content = menu_content_wrap.offset().top,
                    menu_content_height = menu_content_wrap.outerHeight(),
                    read_content_wrap = $('.doc-read-content'),
                    read_content = read_content_wrap.offset().top,
                    offset_bottom = $('.doc-offset-bottom').offset().top - menu_content_height;


                if ( window_top >= read_content && window_top <= offset_bottom ) {
                    menu_content_wrap.css({
                        position : 'fixed',
                        top : 0,
                    });
                    read_content_wrap.addClass('doc-read-content-scroll');
                } else if (window_top > offset_bottom ) {
                    menu_content_wrap.css({
                        position : 'fixed',
                        top : offset_bottom - window_top,
                    });
                }
                else {

                    menu_content_wrap.removeAttr('style');
                    read_content_wrap.removeClass('doc-read-content-scroll');
                }

                $.each( li_id, function( index, menu ) {

                    var item_number = $('.doc-read-menu').find('.doc-item-'+menu ),
                        item_number_height = item_number.outerHeight(),
                        content_top = $('.doc-read-content').find( '.doc-menu-id-'+menu ).offset().top-5;

                    if ( window_top > content_top ) {

                        menu_lis.not(item_number).find('.doc-menu-text-wrap').removeAttr('style');
                        menu_lis.not(item_number).find('.doc-menu-text-wrap').find('a').removeAttr('style');

                        item_number.find('.doc-menu-text-wrap').first().css({
                            'background': '#BCBCBC',
                            'border-left' : '2px solid #000',
                        });
                        item_number.find('.doc-menu-text-wrap').first().find('a').css({
                            'color' : '#fff'
                        });

                    }
                })
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

                $('input[name="section_ID"]').val(res.data.post.ID);
                $('input[name="section_title"]').val(res.data.post.post_title);
                $('.doc-section-wrap').find('iframe').contents().find('body').html(res.data.post.post_content);
                $('.doc-section-wrap .doc-section-submit').hide();
                $('.doc-section-wrap .doc-udate-section-submit').show();
                $('.doc-section-wrap .doc-cancel-section-submit').show();
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
            $('input[name="section_ID"]').val(''),
            title_field.val('');
            $('.doc-section-wrap').find('iframe').contents().find('body').html('Section Description...');
            content_field.html('');
            title_field.attr( 'placeholder','Section Title' );
            $('.doc-section-wrap .doc-section-submit').show();
            $('.doc-section-wrap .doc-udate-section-submit').hide();
            $('.doc-section-wrap .doc-cancel-section-submit').hide();
        },

        newSectionForm: function(e) {
            e.preventDefault();
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
                section_desc: $('.doc-section-wrap iframe').contents().find('body').html(),//content_field.val(),
                post_id: $('input[name="post_ID"]').val(),
                section_id: $('input[name="section_ID"]').val(),
                _wpnonce: doc._wpnonce
            }

            $.post( doc.ajax_url, data, function( res ) {
                if( res.success ) {
                    title_field.val('');
                    $('.doc-section-wrap').find('iframe').contents().find('body').html('Section Description...');
                    content_field.html('');
                    title_field.attr( 'placeholder','Section Title' );

                    Documentation.menuDeligation(res);

                    $('.doc-section-wrap .doc-section-submit').show();
                    $('.doc-section-wrap .doc-udate-section-submit').hide();
                    $('.doc-section-wrap .doc-cancel-section-submit').hide();

                }
            });
        }
    }

    Documentation.init();

})(jQuery);