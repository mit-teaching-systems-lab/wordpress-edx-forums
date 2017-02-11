<?php

if (!defined('ABSPATH')) exit;

require_once(GDBBPRESSWIDGETS_PATH.'code/widgets/admin.php');

class gdbbW_Admin {
    private $page_ids = array();
    private $admin_plugin = false;

    function __construct() {
        add_action('after_setup_theme', array($this, 'load'));
    }

    public function load() {
        add_action('admin_init', array(&$this, 'admin_init'));
        add_action('admin_menu', array(&$this, 'admin_menu'));

        add_filter('plugin_action_links', array(&$this, 'plugin_actions'), 10, 2);
        add_filter('plugin_row_meta', array(&$this, 'plugin_links'), 10, 2);
    }

    function upgrade_notice() {
        global $gdbbpress_widgets;

        if ($gdbbpress_widgets->o['upgrade_to_pro_102'] == 1) {
            $no_thanks = add_query_arg('proupgradebbw', 'hide');

            echo '<div class="updated d4p-updated">';
                echo __("Thank you for using this plugin. Please, take a few minutes and check out the GD bbPress Toolbox Pro plugin with many new and improved features.", "gd-bbpress-widgets");
                echo '<br/>'.__("Buy GD bbPress Toolbox Pro version or Dev4Press Plugins Pack and get 15% discount using this coupon", "gd-bbpress-widgets");
                echo ': <strong style="color: #c00;">GDBBPTOPRO</strong><br/>';
                echo '<strong><a href="http://www.gdbbpbox.com/" target="_blank">'.__("Official Website", "gd-bbpress-widgets")."</a></strong> | ";
                echo '<strong><a href="http://d4p.me/247" target="_blank">'.__("Dev4Press Plugins Pack", "gd-bbpress-widgets")."</a></strong> | ";
                echo '<a href="'.$no_thanks.'">'.__("Don't display this message anymore", "gd-bbpress-widgets")."</a>.";
            echo '</div>';
        }
    }

    public function admin_init() {
        if (isset($_GET['page'])) {
            $this->admin_plugin = $_GET['page'] == 'gdbbpress_widgets';
        }

        wp_enqueue_style('gd-bbpress-widgets-admin', GDBBPRESSWIDGETS_URL."css/gd-bbpress-widgets_widgets.css", array(), GDBBPRESSWIDGETS_VERSION);
        if ($this->admin_plugin) {
            wp_enqueue_style('gd-bbpress-widgets', GDBBPRESSWIDGETS_URL."css/gd-bbpress-widgets_admin.css", array(), GDBBPRESSWIDGETS_VERSION);
        }

        if (isset($_GET['proupgradebbw']) && $_GET['proupgradebbw'] == 'hide') {
            global $gdbbpress_widgets;

            $gdbbpress_widgets->o['upgrade_to_pro_102'] = 0;

            update_option('gd-bbpress-widgets', $gdbbpress_widgets->o);

            wp_redirect(remove_query_arg('proupgradebbw'));
            exit;
        }
    }

    public function admin_menu() {
        $this->page_ids[] = add_submenu_page('edit.php?post_type=forum', 'GD bbPress Widgets', __("Widgets", "gd-bbpress-widgets"), GDBBPRESSWIDGETS_CAP, 'gdbbpress_widgets', array(&$this, 'menu_tools'));

        $this->admin_load_hooks();
    }

    public function admin_load_hooks() {
        if (GDBBPRESSWIDGETS_WPV < 33) return;

        foreach ($this->page_ids as $id) {
            add_action('load-'.$id, array(&$this, 'load_admin_page'));
        }
    }

    public function plugin_actions($links, $file) {
        if ($file == 'gd-bbpress-widgets/gd-bbpress-widgets.php'){
            $settings_link = '<a href="edit.php?post_type=forum&page=gdbbpress_widgets">'.__("Settings", "gd-bbpress-widgets").'</a>';
            array_unshift($links, $settings_link);
        }

        return $links;
    }

    function plugin_links($links, $file) {
        if ($file == 'gd-bbpress-widgets/gd-bbpress-widgets.php'){
            $links[] = '<a href="edit.php?post_type=forum&page=gdbbpress_tools&tab=faq">'.__("FAQ", "gd-bbpress-widgets").'</a>';
            $links[] = '<a target="_blank" style="color: #cc0000; font-weight: bold;" href="http://www.gdbbpbox.com/">'.__("Upgrade to GD bbPress Toolbox Pro", "gd-bbpress-widgets").'</a>';
        }

        return $links;
    }

    public function load_admin_page() {
        $screen = get_current_screen();

        $screen->set_help_sidebar('
            <p><strong>Dev4Press:</strong></p>
            <p><a target="_blank" href="http://www.dev4press.com/">'.__("Website", "gd-bbpress-widgets").'</a></p>
            <p><a target="_blank" href="http://twitter.com/milangd">'.__("On Twitter", "gd-bbpress-widgets").'</a></p>
            <p><a target="_blank" href="http://facebook.com/dev4press">'.__("On Facebook", "gd-bbpress-widgets").'</a></p>');

        $screen->add_help_tab(array(
            "id" => "gdpt-screenhelp-help",
            "title" => __("Get Help", "gd-bbpress-widgets"),
            "content" => '<h5>'.__("General Plugin Information", "gd-bbpress-widgets").'</h5>
                <p><a href="http://www.dev4press.com/plugins/gd-bbpress-widgets/" target="_blank">'.__("Home Page on Dev4Press.com", "gd-bbpress-widgets").'</a> | 
                <a href="http://wordpress.org/extend/plugins/gd-bbpress-widgets/" target="_blank">'.__("Home Page on WordPress.org", "gd-bbpress-widgets").'</a></p> 
                <h5>'.__("Getting Plugin Support", "gd-bbpress-widgets").'</h5>
                <p><a href="http://www.dev4press.com/forums/forum/free-plugins/gd-bbpress-widgets/" target="_blank">'.__("Support Forum on Dev4Press.com", "gd-bbpress-widgets").'</a> | 
                <a href="http://wordpress.org/tags/gd-bbpress-attachments?forum_id=10" target="_blank">'.__("Support Forum on WordPress.org", "gd-bbpress-widgets").'</a> | 
                <a href="http://www.dev4press.com/plugins/gd-bbpress-widgets/support/" target="_blank">'.__("Plugin Support Sources", "gd-bbpress-widgets").'</a></p>'));

        $screen->add_help_tab(array(
            "id" => "gdpt-screenhelp-website",
            "title" => "Dev4Press", "sfc",
            "content" => '<p>'.__("On Dev4Press website you can find many useful plugins, themes and tutorials, all for WordPress. Please, take a few minutes to browse some of these resources, you might find some of them very useful.", "gd-bbpress-widgets").'</p>
                <p><a href="http://www.dev4press.com/plugins/" target="_blank"><strong>'.__("Plugins", "gd-bbpress-widgets").'</strong></a> - '.__("We have more than 10 plugins available, some of them are commercial and some are available for free.", "gd-bbpress-widgets").'</p>
                <p><a href="http://www.dev4press.com/themes/" target="_blank"><strong>'.__("Themes", "gd-bbpress-widgets").'</strong></a> - '.__("All our themes are based on our own xScape Theme Framework, and only available as premium.", "gd-bbpress-widgets").'</p>
                <p><a href="http://www.dev4press.com/category/tutorials/" target="_blank"><strong>'.__("Tutorials", "gd-bbpress-widgets").'</strong></a> - '.__("Premium and free tutorials for our plugins themes, and many general and practical WordPress tutorials.", "gd-bbpress-widgets").'</p>
                <p><a href="http://www.dev4press.com/documentation/" target="_blank"><strong>'.__("Central Documentation", "gd-bbpress-widgets").'</strong></a> - '.__("Growing collection of functions, classes, hooks, constants with examples for our plugins and themes.", "gd-bbpress-widgets").'</p>
                <p><a href="http://www.dev4press.com/forums/" target="_blank"><strong>'.__("Support Forums", "gd-bbpress-widgets").'</strong></a> - '.__("Premium support forum for all with valid licenses to get help. Also, report bugs and leave suggestions.", "gd-bbpress-widgets").'</p>'));
    }

    public function menu_tools() {
        global $gdbbpress_widgets;
        $options = $gdbbpress_widgets->o;

        include(GDBBPRESSWIDGETS_PATH.'forms/panels.php');
    }
}

global $gdbbpress_w_admin;
$gdbbpress_w_admin = new gdbbW_Admin();

?>