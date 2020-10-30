<?php

/**
 * Name: Notice Board
 * Description: Custom post type notice board is used for official records which is mandatory for city/village websites.
 *
 * Link: https://www.wetory.eu/ideas/
 * 
 * @package    wetory_support
 * @subpackage wetory_support/includes/cpt
 * @author     Tomas Rybnicky <tomas.rybnicky@wetory.eu>
 */
class Cpt_Wetory_Support_Notice_Board extends Wetory_Support_Cpt {

    /**
     * Create new instance with static properties
     * 
     * @since    1.1.0
     */
    public function __construct() {
        $id = 'wcpt-notice-board'; // post type key
        parent::__construct($id);
    }

    /**
     * Specify arguments for registering this post type. See $args section in WP documentation
     * https://developer.wordpress.org/reference/functions/register_post_type/
     * 
     * @since    1.1.0
     * 
     * @return array Arguments for registering post type
     */
    protected function get_arguments() {
        $args = array();
        
        return $args;
    }

}
