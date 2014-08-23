;(function($) {
	var DocRead = {
		init: function() {
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
	}

	DocRead.init();
})(jQuery);