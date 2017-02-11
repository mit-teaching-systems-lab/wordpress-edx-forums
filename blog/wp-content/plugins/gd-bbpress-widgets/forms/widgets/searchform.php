<form role="search" method="get" id="d4p-bbp-searchtopics-form" action="<?php bbp_view_url('search'); ?>" >
    <div>
        <label class="screen-reader-text" for="s"><?php _e("Search for:", "gd-bbpress-widgets"); ?></label>
        <input type="text" value="<?php echo get_search_query(); ?>" name="s" />
        <input type="submit" value="<?php _e("Search", "gd-bbpress-widgets"); ?>" />
    </div>
</form>
