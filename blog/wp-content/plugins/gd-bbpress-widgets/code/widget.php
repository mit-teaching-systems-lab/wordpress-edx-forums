<?php

/*
Name:    gdr2_Widget
Version: 2.7.3
Author:  Milan Petrovic
Email:   milan@gdragon.info
Website: http://www.dev4press.com/libs/gdr2/
Info:    Expanded base widget class

== Copyright ==
Copyright 2008 - 2012 Milan Petrovic (email: milan@gdragon.info)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!defined("GDR2_EOL")) { define("GDR2_EOL", "\r\n"); }
if (!defined("GDR2_TAB")) { define("GDR2_TAB", "\t"); }

if (!function_exists("gdr2_html_id_from_name")) {
    /**
     * Get valid ID for HTML control from name.
     *
     * @param string $name name for the control
     * @param string $id id for the control
     * @return string valid id
     */
    function gdr2_html_id_from_name($name, $id = "") {
        if ($id == "") {
            $id = str_replace("]", "", $name);
            $id = str_replace("[", "_", $id);
        } else if ($id == "_") {
            $id = "";
        }

        return $id;
    }
}

if (!class_exists("gdr2_Widget")) {
    /**
     * Base widget class expanding default WordPress class.
     */
    class gdr2_Widget extends WP_Widget {
        var $widget_visibility_display = "_display";
        var $widget_visibility_active = true;
        var $widget_cached_results = "_cached";
        var $widget_cached_active = true;
        var $widget_cached_exclude = array("title", "_display", "_cached");

        var $widget_name = "gdr2: Base Widget Class";
        var $widget_description = "Information about the widget";
        var $widget_base = "gdr2_widget";
        var $widget_domain = "gdr2_widgets";
        var $widget_id;

        var $folder_name = 'gdr2-widget';

        var $cache_key = "";
        var $cache_prefix = "gdrw";
        var $cache_active = false;
        var $cache_time = 0;

        var $defaults = array(
            "title" => "Base Widget Class",
            "_display" => "all",
            "_cached" => 6
        );

        /**
         * Expanded constructor from the WordPress base widget class.
         */
        function __construct($id_base = false, $name = "", $widget_options = array(), $control_options = array()) {
            $this->_actions();

            $widget_options = empty($widget_options) ? array("classname" => "cls_".$this->widget_base, "description" => $this->widget_description) : $widget_options;
            $control_options = empty($control_options) ? array("width" => 400) : $control_options;
            parent::__construct($this->widget_base, $this->widget_name, $widget_options, $control_options);

            if (!defined("GDR2_CACHE_ACTIVE") || !GDR2_CACHE_ACTIVE) {
                $this->widget_cached_active = false;
            }
        }

        /**
         * Create unique name that can be used for filter purposes.
         *
         * @param string $name name to use to create filter
         * @return string formated name
         */
        private function _filter($name = "results") {
            $base = substr($this->folder_name, 5);
            return $this->widget_base."_".$name."_".str_replace("-", "", $base);
        }

        /**
         * Check if the widget should be visible.
         *
         * @param array $instance list of options for the widget
         * @return bool true if the widget should be visible, false if not
         */
        private function _visible($instance) {
            if (!$this->widget_visibility_active) {
                return true;
            }

            $visible = $this->is_visible($instance);
            $_display = $this->widget_visibility_display;

            if ($visible && isset($instance[$_display])) {
                $logged = is_user_logged_in();

                if ($instance[$_display] == "all" || ($instance[$_display] == "user" && $logged) || ($instance[$_display] == "visitor" && !$logged)) {
                    $visible = true;
                } else {
                    $visible = false;
                }
            }

            $visible = apply_filters($this->widget_domain."_visibility", $visible, $this, $instance);
            return apply_filters($this->_filter("visibility"), $visible, $this, $instance);
        }

        /**
         * Create unique id that should be used for JavaScript or other 
         * purposes.
         *
         * @param string $args basic widget arguments used to base the id on
         * @return string formated id
         */
        private function _widget_id($args) {
            $this->widget_id = str_replace(array("-", "_"), array("", ""), $args["widget_id"]);
        }

        /**
         * Create string to be used for cache.
         *
         * @param type $instance list of options for the widget
         */
        private function _cache_key($instance) {
            $this->cache_active = $this->_cache_active($instance);
            if ($this->cache_active) {
                $copy = array();
                foreach ($instance as $key => $value) {
                    if (!in_array($key, $this->widget_cached_exclude)) $copy[$key] = $value;
                }
                $this->cache_key = $this->cache_prefix."_".md5($this->widget_base."_".serialize($copy));
            }
        }

        /**
         * Check if the widget should be cached
         *
         * @param array $instance list of options for the widget
         * @return bool true if the widget should be cached, false if not
         */
        private function _cache_active($instance) {
            $cached = isset($instance[$this->widget_cached_results]) ? intval($instance[$this->widget_cached_results]) : 0;
            $cached = apply_filters($this->widget_domain."_cached", $cached, $this);
            $this->cache_time = apply_filters($this->_filter("cached"), $cached, $this);

            return $this->widget_cached_active && $this->cache_time > 0;
        }

        /**
         * Attempt to get cached widget rendering, or render from scratch.
         *
         * @param type $instance list of options for the widget
         * @return string rendered widget output
         */
        private function _cached($instance) {
            if ($this->cache_active && $this->cache_key !== "") {
                $results = gdr2c_get($this->cache_key);

                if ($results === false) {
                    $results = $this->results($instance);
                    gdr2c_set($this->cache_key, $results, $this->cache_time * 3600);
                }

                return $results;
            } else {
                return $this->results($instance);
            }
        }

        /**
         * Actions or filters to set up when the widget instance is constructed.
         */
        public function _actions() { }

        /**
         * Render the widget, based on the base WordPress widget class.
         *
         * @param mixed $args settings for the widget
         * @param mixed $instance widget instance settings
         */
        public function widget($args, $instance) {
            $args = apply_filters($this->_filter("arguments"), $args, $this);
            extract($args, EXTR_SKIP);

            $this->_widget_id($args);
            $this->_cache_key($instance);

            if ($this->_visible($instance)) {
                $results = $this->_cached($instance);
                echo $before_widget;
                if (isset($instance["title"]) && $instance["title"] != '') {
                    echo $before_title;
                    echo $this->title($instance);
                    echo $after_title;
                }
                echo $this->render($results, $instance);
                echo $after_widget;
            }
        }

        /**
         * Prepare widget title for display.
         *
         * @param type $instance widget instance settings
         * @return string prepared title
         */
        public function title($instance) {
            return $instance["title"];
        }

        /**
         * Public check for the widget visibility, each widget can override. If
         * this return false, widget visibility settings is not used.
         *
         * @param type $instance  widget instance settings
         * @return boolean true or false if the widget should be visible
         */
        public function is_visible($instance) {
            return true;
        }

        /**
         * Echo the settings update form.
	 *
	 * @param array $instance widget instance settings
	 */
        public function form($instance) {
            $instance = wp_parse_args((array)$instance, $this->defaults);
        }

        /**
         * Render the select HTML control.
         *
         * @param array $values associated array with values
         * @param array|string $selected selected value
         * @param string $name name for the control
         * @param string $id id for the control
         * @param string $class classes to add to control
         * @param string $style styles to add to control
         * @param bool $multi add multiple argument to select box
         * @param bool $echo display or return rendered control
         * @return string rendered control
         */
        public function display_select_options($values, $selected = "", $name = "", $id = "", $class = "", $style = "", $multi = false, $echo = true) {
            $render = "";
            $selected = (array)$selected;
            $id = gdr2_html_id_from_name($name, $id);

            if ($class != "") $class = ' class="'.$class.'"';
            if ($style != "") $style = ' style="'.$style.'"';

            $multi = $multi ? " multiple" : "";
            $name = $multi ? $name."[]" : $name;

            $render.= '<select name="'.$name.'" id="'.$id.'"'.$class.$style.$multi.'>';
            foreach ($values as $value => $display) {
                $sel = in_array($value, $selected) ? ' selected="selected"' : '';
                $render.= '<option value="'.$value.'"'.$sel.'>'.$display.'</option>';
            }
            $render.= '</select>';

            if ($echo) {
                echo $render;
            } else {
                return $render;
            }
        }

        /**
         * Get the excerpt for the provided content.
         *
         * @param string $content start content to get excerpt from
         * @param int $words_count number of words for the excerpt
         * @param int $id post or comment id, or other id
         * @return string resulting excerpt.
         */
        public function excerpt($content, $words_count = 16, $id = 0) {
            $text = apply_filters("gdr2_widget_excerpt", "", $content, $words_count, $id, $this->widget_base);

            if ($text == "") {
                $text = trim($content);

                $text = str_replace(']]>', ']]&gt;', $text);
                $text = strip_shortcodes($text);
                $text = strip_tags($text);
                $text = str_replace('"', '\'', $text);

                if ($words_count > 0) {
                    $words = explode(" ", $text, $words_count + 1);
                    if (count($words) > $words_count) {
                        $words = array_slice($words, 0, $words_count);
                        $text = implode(" ", $words)."...";
                    }
                }
            }

            return $text;
        }

        /**
         * Prepare SQL from the query parts.
         *
         * @param array $instance widget instance settings
         * @param string $select for SELECT part
         * @param string $from for FROM part
         * @param string $where for WHERE part
         * @param string $group for GROUP part
         * @param string $order for ORDER part
         * @param string $limit for LIMIT part
         * @return string resulting SQL query 
         */
        public function prepare_sql($instance, $select, $from, $where, $group, $order, $limit) {
            $select = apply_filters($this->_filter("sql_select"), $select, $instance);
            $from = apply_filters($this->_filter("sql_from"), $from, $instance);
            $where = apply_filters($this->_filter("sql_where"), $where, $instance);
            $group = apply_filters($this->_filter("sql_group"), $group, $instance);
            $order = apply_filters($this->_filter("sql_order"), $order, $instance);
            $limit = apply_filters($this->_filter("sql_limit"), $limit, $instance);

            $sql = "SELECT ".$select." FROM ".$from;
            if ($where != "") $sql.= " WHERE ".$where;
            if ($group != "") $sql.= " GROUP BY ".$group;
            if ($order != "") $sql.= " ORDER BY ".$order;
            if ($limit != "") $sql.= " LIMIT ".$limit;

            return $sql;
        }

        /**
         * Update widget instance settings from supplied data.
         *
         * @param array $new_instance new instance settings
         * @param array $old_instance old instance settings
         * @return array updated instance settings
         */
        public function update($new_instance, $old_instance) {
            $instance = $old_instance;

            $instance["title"] = strip_tags(stripslashes($new_instance["title"]));

            return $instance;
        }

        /**
         * Simplifed rendering to be used for shortcodes.
         *
         * @param array $instance widget instance settings
         * @return string rendered content
         */
        public function simple_render($instance = array()) {
            $instance = shortcode_atts($this->defaults, $instance);
            $results = $this->results($instance);
            return $this->render($results, $instance);
        }

        /**
         * Prepare widget results for rendering.
         *
         * @param array $instance widget instance settings
         * @param mixed $results raw results
         * @return mixed modified results
         */
        public function prepare($instance, $results) { return $results; }

        /**
         * Get widget results based on the instance settings.
         *
         * @param type $instance widget instance settings
         * @return mixed raw results 
         */
        public function results($instance) { return null; }

        /**
         * Render widget contents based on the instance settings and results.
         *
         * @param mixed $results widget results
         * @param type $instance widget instance settings
         * @return string rendered content
         */
        public function render($results, $instance) { return $results; }
    }
}

?>