(function ($) {

    /**
     * Initiating Select2 input fields.
     * 
     * For more information see available options of Select2 input
     * https://select2.org/configuration/options-api
     * 
     * @since      1.0.0
     * @returns {undefined}
     */
    function initiate_select2() {
        $('select.favourite-posts[multiple="multiple"]').select2({
            ajax: {
                url: ajaxurl,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        action: 'wetory_search_posts_for_options' // Callback function from includes/wetory-support-functions-ajax.php
                    };
                },
                processResults: function (data) {
                    var options = [];
                    if (data) {
                        $.each(data, function (index, text) {
                            options.push({id: text[0], text: text[1]});
                        });
                    }
                    return {
                        results: options
                    };
                },
                cache: true
            },
            minimumInputLength: 1 // the minimum of symbols to input before perform a search
        });
    }

    $(window).load(function () {
        initiate_select2();
    });
    
    /**
     * This need to be done so as to avoid loosing select2 after updating widget
     */
    $(document).ajaxSuccess(function (e, xhr, settings) {
        var widget_id_base = 'wetory_favourite_posts';
        if (settings.data.search('action=save-widget') !== -1 && settings.data.search('id_base=' + widget_id_base) !== -1) {
            initiate_select2();
        }
    });
})(jQuery);


