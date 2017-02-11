<?php if (isset($_GET["settings-updated"]) && $_GET["settings-updated"] == "true") { ?>
<div class="updated settings-error" id="setting-error-settings_updated"> 
    <p><strong><?php _e("Settings saved.", "gd-bbpress-widgets"); ?></strong></p>
</div>
<?php } ?>

<form action="" method="post">
    <?php wp_nonce_field("gd-bbpress-tools"); ?>
    <div class="d4p-settings">
        <h3><?php _e("JavaScript and CSS Settings", "gd-bbpress-widgets"); ?></h3>
        <p><?php _e("You can disable including styles and JavaScript by the plugin, if you want to do it some other way.", "gd-bbpress-widgets"); ?></p>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row"><label for="include_js"><?php _e("Include JavaScript", "gd-bbpress-widgets"); ?></label></th>
                    <td>
                        <input type="checkbox" <?php if ($options["include_js"] == 1) echo " checked"; ?> name="include_js" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="include_css"><?php _e("Include CSS", "gd-bbpress-widgets"); ?></label></th>
                    <td>
                        <input type="checkbox" <?php if ($options["include_css"] == 1) echo " checked"; ?> name="include_css" />
                    </td>
                </tr>
            </tbody>
        </table>
        <p><?php _e("If you use shortcodes to embed forums, and you rely on plugin to add JS and CSS, you also need to enable this option to skip checking for bbPress specific pages.", "gd-bbpress-widgets"); ?></p>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row"><label for="include_always"><?php _e("Always Include", "gd-bbpress-widgets"); ?></label></th>
                    <td>
                        <input type="checkbox" <?php if ($options["include_always"] == 1) echo " checked"; ?> name="include_always" />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="d4p-settings-second">
        <h3><?php _e("Disable Default bbPress Widgets", "gd-bbpress-widgets"); ?></h3>
        <p><?php _e("You can disable default widgets that can be replaced with widgets in this plugin.", "gd-bbpress-widgets"); ?></p>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row"><label for="default_disable_topicviewslist"><?php _e("Topic Views List", "gd-bbpress-widgets"); ?></label></th>
                    <td>
                        <input type="checkbox" <?php if ($options["default_disable_topicviewslist"] == 1) echo " checked"; ?> name="default_disable_topicviewslist" />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="d4p-clear"></div>
    <p class="submit"><input type="submit" value="<?php _e("Save Changes", "gd-bbpress-widgets"); ?>" class="button-primary" id="gdbb-tools-submit" name="gdbb-widgets-submit" /></p>
</form>
