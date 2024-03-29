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
        
        // Initialize admin datepickers
        if (0 < $('.wetory-datepicker').length) {
            $('.wetory-datepicker').datepicker({
                dateFormat: "dd.mm.yy"
            });
        }

        // Hide or show widget link options conditionaly on value of widget link style
        $('select.widget-link-style').change(function () {
            if ($(this).val() === 'none') {
                $(this).parent().next('div.widget-link-options').css("display", "none");
            } else {
                $(this).parent().next('div.widget-link-options').css("display", "block");
            }
        });

        // Maintenance page operations handler
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