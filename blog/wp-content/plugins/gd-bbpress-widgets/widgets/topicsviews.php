<?php

if (!defined('ABSPATH')) exit;

class d4pbbpWidget_topicsviews extends gdr2_Widget {
    var $folder_name = 'd4p-bbpress-topicsviews';
    var $widget_base = 'd4p_bbw_topicsviews';
    var $widget_domain = 'd4pbbw_widgets';
    var $cache_prefix = 'd4pbbw';

    var $defaults = array(
        'title' => 'Topics Views List',
        '_display' => 'all',
        '_cached' => 0,
        'views' => array()
    );

    function __construct($id_base = false, $name = '', $widget_options = array(), $control_options = array()) {
        $this->widget_description = __("List of the selected topic views.", "gd-bbpress-widgets");
        $this->widget_name = 'bbPress: '.__("Topics Views", "gd-bbpress-widgets");
        parent::__construct($this->widget_base, $this->widget_name, array(), array('width' => 250));
    }

    function form($instance) {
        $instance = wp_parse_args((array)$instance, $this->defaults);

        echo '<div class="d4pbbp-widget">';
        include(GDBBPRESSWIDGETS_PATH."forms/widgets/title.php");
        include(GDBBPRESSWIDGETS_PATH."forms/widgets/topicsviews.php");
        echo '</div>';
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['title'] = strip_tags(stripslashes($new_instance['title']));
        $instance['_display'] = strip_tags(stripslashes($new_instance['_display']));
        $instance['_cached'] = intval(strip_tags(stripslashes($new_instance['_cached'])));
        $instance['views'] = (array)$new_instance['views'];

        return $instance;
    }

    function render($results, $instance) {
        echo '<div class="d4p-bbw-widget bbf-topicsviews">'.GDR2_EOL;
        echo '<ul>'.GDR2_EOL;

        $current_view = bbp_is_single_view() ? get_query_var('bbp_view') : '';

        foreach ($instance['views'] as $view) {
            $class = 'bbp-view-'.$view;
            if ($view == $current_view) {
                $class.= ' current';
            }

            echo '<li class="'.$class.'">'.GDR2_EOL.GDR2_TAB;
            echo '<a title="'.sprintf(__("Topic View: %s", "gd-bbpress-widgets"), bbp_get_view_title($view)).'" href="'.bbp_get_view_url($view).'">'.bbp_get_view_title($view).'</a>';
            echo GDR2_EOL."</li>".GDR2_EOL;
        }
        echo '</ul>'.GDR2_EOL;
        echo '</div>'.GDR2_EOL;
    }
}

?>