<?php

if (!defined('ABSPATH')) exit;

class gdbbWdg_Front {
    function __construct() {
        add_action('after_setup_theme', array($this, 'load'));
    }

    public function load() {
        add_action('bbp_head', array(&$this, 'bbp_head'));
    }

    public function bbp_head() { 
        if (d4p_bbt_o('include_always') == 1 || d4p_is_bbpress()) {
            if (d4p_bbt_o('include_css') == 1) { ?>
                <style type="text/css">
                    /*<![CDATA[*/
                    
                    /*]]>*/
                </style>
            <?php } ?>
        <?php }
    }
}

global $gdbbpress_widgets_front;
$gdbbpress_widgets_front = new gdbbWdg_Front();

?>