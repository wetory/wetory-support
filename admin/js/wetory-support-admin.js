(function ($) {


    /**
     * All of the code for your admin-facing JavaScript source
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
     * 
     */
    $(function () {

        /**
         * Hide or show widget link options conditionaly on value of widget link style
         */
        $('select.widget-link-style').change(function () {
            if ($(this).val() === 'none') {
                $(this).parent().next('div.widget-link-options').css("display", "none");
            } else {
                $(this).parent().next('div.widget-link-options').css("display", "block");
            }
        });

        $('input.mp-operation').click(function () {
            button = $(this);
            original_value = button.val();
            $.ajax({
                url: ajaxurl,
                type: 'post',
                data: 'action=wetory_' + button.attr('name') + '_maintenance_page',
                beforeSend: function () {
                    button.val(button.attr('data-working-text'));
                },
                success: function (response) {
                    $(".mp-operation-outcome").html(response);
                },
                error: function () {
                    // TODO
                },
                complete: function () {
                    button.val(original_value);
                },
            });
        });

        // Navigation tabs in settings page
        var ws_nav_tab = $('.wetory-support-nav-tab-wrapper .nav-tab');
        if (ws_nav_tab.length > 0) {
            ws_nav_tab.click(
                    function () {
                        var href_hash = $(this).attr('href');
                        ws_nav_tab.removeClass('nav-tab-active');
                        $(this).addClass('nav-tab-active');
                        href_hash = href_hash.charAt(0) === '#' ? href_hash.substring(1) : href_hash;
                        var ws_tab_elm = $('div[data-id="' + href_hash + '"]');
                        $('.wetory-support-tab-content').hide();
                        if (ws_tab_elm.length > 0) {
                            ws_tab_elm.fadeIn();
                        }
                    }
            );
            var location_hash = window.location.hash;
            if (location_hash !== "") {
                var ws_tab_hash = location_hash.charAt(0) === '#' ? location_hash.substring(1) : location_hash;
                if (ws_tab_hash !== "") {
                    $('div[data-id="' + ws_tab_hash + '"]').show();
                    $('a[href="#' + ws_tab_hash + '"]').addClass('nav-tab-active');
                }
            } else {
                ws_nav_tab.eq(0).click();
            }
        }
        // Navigation sub-tabs under top tabs in settings page
        $('.wetory-support-sub-tab li').click(
                function () {
                    var trgt = $(this).attr('data-target');
                    var prnt = $(this).parent('.wetory-support-sub-tab');
                    var ctnr = prnt.siblings('.wetory-support-sub-tab-container');
                    prnt.find('li a').css({'color': '#0073aa', 'cursor': 'pointer'});
                    $(this).find('a').css({'color': '#000', 'cursor': 'default', 'font-weight': '600'});
                    ctnr.find('.wetory-support-sub-tab-content').hide();
                    ctnr.find('.wetory-support-sub-tab-content[data-id="' + trgt + '"]').fadeIn();
                }
        );
        $('.wetory-support-sub-tab').each(
                function () {
                    var elm = $(this).children('li').eq(0);
                    elm.click();
                }
        );

    });

})(jQuery);

function openSettingsTab(evt, tabName) {
    // Declare all variables
    var i, tabcontent, tablinks;

    // Get all elements with class="tabcontent" and hide them
    tabcontent = document.getElementsByClassName("wetory-tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName("wetory-tab-link");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" nav-tab-active", "");
    }

    // Show the current tab, and add an "active" class to the button that opened the tab
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " nav-tab-active";
}