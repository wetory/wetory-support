(function ($) {

    // Initialize admin datepickers
    if (0 < $('.wetory-support-datepicker').length) {
        $('.wetory-support-datepicker').datepicker({
            dateFormat: "dd.mm.yy"
        });
    }

    // AJAX Validation
    jQuery(document).ready(function () {

        jQuery('#publish').click(function () {
            if (jQuery(this).data("valid")) {
                return true;
            }

            //hide loading icon, return Publish button to normal
            jQuery('#publishing-action .spinner').addClass('is-active');
            jQuery('#publish').addClass('button-primary-disabled');
            jQuery('#save-post').addClass('button-disabled');

//            var data = {
//                action: 'ep_pre_product_submit',
//                security: '<?php echo wp_create_nonce( "pre_publish_validation" ); ?>',
//                'product_number': jQuery('#acf-field-product_number').val()
//            };
//            jQuery.post(ajaxurl, data, function (response) {
//
//                jQuery('#publishing-action .spinner').removeClass('is-active');
//                if (response.success) {
//                    jQuery("#post").data("valid", true).submit();
//                } else {
//                    alert("Error: " + response.data.message);
//                    jQuery("#post").data("valid", false);
//
//                }
//                //hide loading icon, return Publish button to normal
//                jQuery('#publish').removeClass('button-primary-disabled');
//                jQuery('#save-post').removeClass('button-disabled');
//            });
//            return false;
        });
    });

})(jQuery);