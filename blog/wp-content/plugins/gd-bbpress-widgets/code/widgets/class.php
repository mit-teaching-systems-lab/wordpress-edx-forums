<?php

if (!defined('ABSPATH')) exit;

class gdbbPressWidgets {
    private $wp_version;
    private $plugin_path;
    private $plugin_url;

    public $l;
    public $o;

    public $widgets = array('searchtopics', 'topicsviews');

    function __construct() {
        $this->_init();
    }

    private function _upgrade($old, $new) {
        foreach ($new as $key => $value) {
            if (!isset($old[$key])) $old[$key] = $value;
        }

        $unset = array();
        foreach ($old as $key => $value) {
            if (!isset($new[$key])) $unset[] = $key;
        }

        foreach ($unset as $key) {
            unset($old[$key]);
        }

        return $old;
    }

    private function _init() {
        global $wp_version;
        $this->wp_version = substr(str_replace('.', '', $wp_version), 0, 2);
        define('GDBBPRESSWIDGETS_WPV', intval($this->wp_version));

        $gdd = new gdbbPressWidgets_Defaults();

        $this->o = get_option('gd-bbpress-widgets');
        if (!is_array($this->o)) {
            $this->o = $gdd->default_options;
            update_option('gd-bbpress-widgets', $this->o);
        }

        if ($this->o['build'] != $gdd->default_options['build']) {
            $this->o = $this->_upgrade($this->o, $gdd->default_options);

            $this->o['version'] = $gdd->default_options['version'];
            $this->o['date'] = $gdd->default_options['date'];
            $this->o['status'] = $gdd->default_options['status'];
            $this->o['build'] = $gdd->default_options['build'];
            $this->o['revision'] = $gdd->default_options['revision'];
            $this->o['edition'] = $gdd->default_options['edition'];

            update_option('gd-bbpress-widgets', $this->o);
        }

        define('GDBBPRESSWIDGETS_INSTALLED', $gdd->default_options['version'].' Free');
        define('GDBBPRESSWIDGETS_VERSION', $gdd->default_options['version'].'_b'.($gdd->default_options['build'].'_free'));

        $this->plugin_path = dirname(dirname(dirname(__FILE__))).'/';
        $this->plugin_url = plugins_url('/gd-bbpress-widgets/');

        define('GDBBPRESSWIDGETS_URL', $this->plugin_url);
        define('GDBBPRESSWIDGETS_PATH', $this->plugin_path);

        add_action('setup_theme', array($this, 'load'));
        add_action('setup_theme', array(&$this, 'widgets_disable'));
        add_action('widgets_init', array(&$this, 'widgets_init'));
    }

    public function load() {
        add_action('init', array(&$this, 'load_translation'));

        if (is_admin()) {
            require_once(GDBBPRESSWIDGETS_PATH.'code/admin.php');
        } else {
            // require_once(GDBBPRESSWIDGETS_PATH.'code/widgets/front.php');
        }
    }

    public function widgets_init() {
        foreach ($this->widgets as $widget) {
            require_once(GDBBPRESSWIDGETS_PATH.'widgets/'.$widget.'.php');

            register_widget('d4pbbpWidget_'.$widget);
        }
    }

    public function widgets_disable() {
        if ($this->o['default_disable_topicviewslist'] == 1) {
            remove_action('bbp_widgets_init', array('BBP_Views_Widget',  'register_widget'), 10);
        }
    }

    public function load_translation() {
        $this->l = get_locale();

        if(!empty($this->l)) {
            load_plugin_textdomain('gd-bbpress-widgets', false, 'gd-bbpress-widgets/languages');
        }
    }
}

global $gdbbpress_widgets;
$gdbbpress_widgets = new gdbbPressWidgets();

?>