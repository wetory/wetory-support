(function ($) {
    'use strict';

    $.fn.serializeAssoc = function () {
        var data = {};
        $.each(this.serializeArray(), function (key, obj) {
            var a = obj.name.match(/(.*?)\[(.*?)\]/);
            if (a !== null)
            {
                var subName = a[1];
                var subKey = a[2];

                if (!data[subName]) {
                    data[subName] = [];
                }

                if (!subKey.length) {
                    subKey = data[subName].length;
                }

                if (data[subName][subKey]) {
                    if ($.isArray(data[subName][subKey])) {
                        data[subName][subKey].push(obj.value);
                    } else {
                        data[subName][subKey] = [];
                        data[subName][subKey].push(obj.value);
                    }
                } else {
                    data[subName][subKey] = obj.value;
                }
            } else {
                if (data[obj.name]) {
                    if ($.isArray(data[obj.name])) {
                        data[obj.name].push(obj.value);
                    } else {
                        data[obj.name] = [];
                        data[obj.name].push(obj.value);
                    }
                } else {
                    data[obj.name] = obj.value;
                }
            }
        });
        return data;
    };

    /**
     * All of the code for your public-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */

    /**
     * Automatically submit filter forms on load if they have class autoload
     */
    $(window).load(function () {
        // If filtering in place it need to be sumbmitted on first load
        var ajax_filter = $("form.wetory-ajax-filter");
        if (ajax_filter.length !== 0 && ajax_filter.hasClass('autoload')) {
             ajax_filter.submit();
        }
    });

    $(function () {
        // 
    });

    function update_posts_sumaries() {

        var displayed_posts_count = $('span.displayed-posts-count');
        var total_posts_count = $('span.total-posts-count');

        if (displayed_posts_count) {
            var posts_count = wp_query.posts_per_page * wp_query.current_page;
            posts_count = (posts_count > wp_query.found_posts) ? wp_query.found_posts : posts_count;
            displayed_posts_count.text(posts_count);
        }
        if (total_posts_count) {
            total_posts_count.text(wp_query.found_posts);
        }
    }

    /**
     * Handle click for filter form submit button
     */
    $('form.wetory-ajax-filter button[type="submit"]').click(function (e) {
        e.preventDefault();
        $('form.wetory-ajax-filter').submit();
        return false;
    });
    
    /**
     * Handle click for filter form reset button
     */
    $('form.wetory-ajax-filter button[type="reset"]').click(function (e) {
        this.form.reset();
        $('form.wetory-ajax-filter').submit();
        return false;
    });

    /**
     * Handle posts filter form submit
     */
    $('form.wetory-ajax-filter').submit(function () {
        var filter = $(this);
        var filter_status = filter.find('.filter-status');
        var post_list = $('.wetory-ajax-post-list');
        var template = post_list.attr('data-loadmore-template');
        var loadmore = $('.wetory-ajax-loadmore');

        var data = {
            'action': 'wetory_ajax_filter',
            'query': wp_query.query,
            'form': filter.serializeAssoc(),
            'template': template
        };

        $.ajax({
            url: parameters.ajaxurl,
            data: data,
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {
                filter.find('button[type="submit"]').addClass('loading');
            },
            success: function (data) {
                // Set content
                if (post_list.is('table')) {
                    post_list.find('tbody').html(data.html);
                } else {
                    post_list.html(data.html);
                }
                // Set variables
                wp_query.query = data.query;
                wp_query.posts_per_page = data.posts_per_page;
                wp_query.current_page = 1;
                wp_query.found_posts = data.found_posts;
                wp_query.max_num_pages = data.max_num_pages;

                // Update summary info
                update_posts_sumaries();
                
                // Show filter status
                filter_status.show();

                // Check if loadmore button is needed
                if (wp_query.current_page >= wp_query.max_num_pages) {
                    loadmore.hide();
                } else {
                    loadmore.show();
                }
            },
            complete: function (xhr) {
                filter.find('button[type="submit"]').removeClass('loading');
            }
        });

        return false;
    });

    /**
     * Handle load more button click
     */
    $('.wetory-ajax-loadmore').click(function () {
        var loadmore = $(this);
        var post_list = $('.wetory-ajax-post-list');

        var template = post_list.attr('data-loadmore-template');

        $.ajax({
            url: parameters.ajaxurl,
            data: {
                'action': 'wetory_ajax_loadmore',
                'query': wp_query.query,
                'page': wp_query.current_page,
                'template': template
            },
            type: 'POST',
            beforeSend: function (xhr) {
                loadmore.addClass('loading');
            },
            success: function (posts) {
                if (posts) {
                    // Insert data to page
                    post_list.append(posts);
                    wp_query.current_page++;

                    // Update summary info
                    update_posts_sumaries();

                    // Hide button if no more pages available
                    if (wp_query.current_page >= wp_query.max_num_pages) {
                        loadmore.hide();
                    }

                } else {
                    loadmore.hide();
                }
            },
            complete: function (xhr) {
                loadmore.removeClass('loading');
            }
        });
        return false;
    });

})(jQuery);
