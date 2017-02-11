<hr/>
<label><?php _e("Select Views to Show", "gd-bbpress-widgets"); ?>:</label>

<?php

foreach (bbp_get_views() as $view => $args) {
    $on = empty($instance['views']);

    if (!$on) {
        $on = in_array($view, $instance['views']);
    }

    echo sprintf('<input type="checkbox" name="%s[]" value="%s"%s /><label class="list">%s</label><br/>',
            $this->get_field_name('views'), $view, $on ? 'checked="checked"' : '', bbp_get_view_title($view));
}

?>