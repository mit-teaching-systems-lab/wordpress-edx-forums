<?php

if (!defined('ABSPATH')) exit;

class d4pbbpWidget_searchtopics extends gdr2_Widget {
    var $folder_name = 'd4p-bbpress-searchtopics';
    var $widget_base = 'd4p_bbw_searchtopics';
    var $widget_domain = 'd4pbbw_widgets';
    var $cache_prefix = 'd4pbbw';

    var $defaults = array(
        'title' => 'Search Topics',
        '_display' => 'all',
        '_cached' => 0
    );

    function __construct($id_base = false, $name = "", $widget_options = array(), $control_options = array()) {
        $this->widget_description = __("Search form for topic search.", "gd-bbpress-widgets");
        $this->widget_name = "bbPress: ".__("Search Topics", "gd-bbpress-widgets");
        parent::__construct($this->widget_base, $this->widget_name, array(), array("width" => 250));
    }

    function form($instance) {
        $instance = wp_parse_args((array)$instance, $this->defaults);

        echo '<div class="d4pbbp-widget">';
        include(GDBBPRESSWIDGETS_PATH."forms/widgets/title.php");
        if (!$this->tools_on()) {
            include(GDBBPRESSWIDGETS_PATH."forms/widgets/searchtopics.php");
        }
        echo '</div>';
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['title'] = strip_tags(stripslashes($new_instance['title']));
        $instance['_display'] = strip_tags(stripslashes($new_instance['_display']));
        $instance['_cached'] = intval(strip_tags(stripslashes($new_instance['_cached'])));

        return $instance;
    }

    function tools_on() {
        if (defined('GDBBPRESSTOOLS_INSTALLED')) {
            return d4p_bbt_o('view_searchresults_active') == 1;
        } else {
            return false;
        }
    }

    function render($results, $instance) {
        if (!$this->tools_on()) return;

        $form_file = apply_filters('d4p_bbpresswidgets_searchtopics_form', GDBBPRESSWIDGETS_PATH."forms/widgets/searchform.php");
        echo '<div class="d4p-bbw-widget bbf-searchtopics">'.GDR2_EOL;
        include($form_file);
        echo '</div>'.GDR2_EOL;
    }
}

?>