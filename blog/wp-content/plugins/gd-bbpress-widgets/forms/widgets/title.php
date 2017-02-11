<label for="<?php echo $this->get_field_id("title"); ?>"><?php _e("Title", "gd-bbpress-widgets"); ?>:</label>
<input class="widefat xswdg-input-title" id="<?php echo $this->get_field_id("title"); ?>" name="<?php echo $this->get_field_name("title"); ?>" type="text" value="<?php echo $instance["title"]; ?>" />
<br/>
<label for="<?php echo $this->get_field_id("_display"); ?>"><?php _e("Display to", "gd-bbpress-widgets"); ?>:</label>
<?php $this->display_select_options(array("all" => __("Everyone", "gd-bbpress-widgets"), "user" => __("Only Users", "gd-bbpress-widgets"), "visitor" => __("Only Visitors", "gd-bbpress-widgets")),
        $instance["_display"], $this->get_field_name("_display"), $this->get_field_id("_display"), "widefat xswdg-select-type"); ?>
