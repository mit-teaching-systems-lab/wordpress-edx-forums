<?php

if (!defined('ABSPATH')) exit;

class gdbbWdg_Admin {
    public $admin_plugin = false;

    function __construct() {
        add_action('after_setup_theme', array($this, 'load'));
    }

    public function load() {
        add_action('admin_init', array(&$this, 'admin_init'));
    }

    public function admin_init() {
        if (isset($_POST['gdbb-widgets-submit'])) {
            global $gdbbpress_widgets;
            check_admin_referer('gd-bbpress-tools');

            $gdbbpress_widgets->o['include_always'] = isset($_POST['include_always']) ? 1 : 0;
            $gdbbpress_widgets->o['include_js'] = isset($_POST['include_js']) ? 1 : 0;
            $gdbbpress_widgets->o['include_css'] = isset($_POST['include_css']) ? 1 : 0;
            $gdbbpress_widgets->o['default_disable_topicviewslist'] = isset($_POST['default_disable_topicviewslist']) ? 1 : 0;

            update_option('gd-bbpress-widgets', $gdbbpress_widgets->o);
            wp_redirect(add_query_arg('settings-updated', 'true'));
            exit();
        }
    }
}

global $gdbbpress_widgets_admin;
$gdbbpress_widgets_admin = new gdbbWdg_Admin();

?>