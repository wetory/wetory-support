<?php
/**
 * Maintenance page content from Wetory Support plugin
 *
 * This file is shown instead of website when maintenance is running.
 *
 * @link       https://www.wetory.eu/
 * @since      1.0.0
 *
 * @package    wetory_support
 * @subpackage wetory_support/public/partials
 */
?>

<!DOCTYPE html>
<html>
    <head>        
        <title><?php _e('Maintenance Mode', 'wetory-support'); ?></title>
        <!-- Meta information -->
        <meta charset="utf-8">
        <meta name="description" content="<?php _e('Maintenance ongoing on this website.', 'wetory-support'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name='robots' content='noindex,nofollow' />
        <!-- Favicon -->
        <link href="https://src.x-wetory.eu/img/icon.png" rel="shortcut icon" type="image/x-icon" />
        <!-- Styles -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://src.x-wetory.eu/css/wetory-support-maintenance.css">
    </head>
    <body>
        <div class="container maintenance-page">                                 
            <img class="maintenance-logo" src="https://src.x-wetory.eu/img/workers.png">
            <h1 class="maintenance-title">
                <?php _e('We are working to give you a better website right now.', 'wetory-support'); ?>
            </h1>  
            <p class="maintenance-info">
                <?php _e('If you are facing problems for long time, let me know via <a href="mailto:admin@wetory.eu">admin@wetory.eu</a>', 'wetory-support'); ?>
            </p>
            <img class="maintenance-img" src="https://src.x-wetory.eu/img/chart.png"/>   
            <p class="maintenance-counter">
                <?php _e('Page will refresh in', 'wetory-support') ?> <span id="autorefresh-counter"></span> <?php _e('seconds', 'wetory-support'); ?>
            </p>
            <p><?php wetory_copyright_info(); ?> | <?php wetory_created_by_link(); ?></p>
        </div>
    </body>
    <!-- JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
    <script type="text/javascript">
        var countDown = 30;

        // Lets run page refresh interval countdown
        function countdown() {
            setInterval(function () {
                if (countDown === 0) {
                    return;
                }
                countDown--;
                document.getElementById('autorefresh-counter').innerHTML = countDown;
                return countDown;
            }, 1000);
        }
        countdown();
    </script>
</html>
