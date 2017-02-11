<?php
/**
 * Settings
 *
 * @package     RollbarWP\Settings
 * @since       1.0.0
 */


// Exit if accessed directly
if (!defined('ABSPATH')) exit;


if (!class_exists('ROLLBAR_WP_SETTINGS')) {

    class ROLLBAR_WP_SETTINGS
    {
        public $options;

        public function __construct()
        {
            // Variables
            $this->options = get_option('rollbar_wp');

            // Initialize
            add_action('admin_menu', array(&$this, 'add_admin_menu'));
            add_action('admin_init', array(&$this, 'init_settings'));
        }

        function add_admin_menu()
        {

            add_submenu_page(
                'tools.php',
                'Rollbar',
                'Rollbar',
                'manage_options',
                'rollbar_wp',
                array(&$this, 'options_page')
            );
        }

        function init_settings()
        {
            register_setting('rollbar_wp', 'rollbar_wp');

            // SECTION: General
            add_settings_section(
                'rollbar_wp_general',
                false,
                false,
                'rollbar_wp'
            );

            // On/off
            add_settings_field(
                'rollbar_wp_status',
                __('Status', 'rollbar'),
                array(&$this, 'status_render'),
                'rollbar_wp',
                'rollbar_wp_general'
            );

            // Token
            add_settings_field(
                'rollbar_wp_access_token',
                __('Access Token', 'rollbar'),
                array(&$this, 'access_token_render'),
                'rollbar_wp',
                'rollbar_wp_general'
            );

            // Config
            add_settings_field(
                'rollbar_wp_environment',
                __('Environment', 'rollbar'),
                array(&$this, 'environment_render'),
                'rollbar_wp',
                'rollbar_wp_general',
                array( 'label_for' => 'rollbar_wp_environment' )
            );

            add_settings_field(
                'rollbar_wp_logging_level',
                __('Logging level', 'rollbar'),
                array(&$this, 'logging_level_render'),
                'rollbar_wp',
                'rollbar_wp_general',
                array( 'label_for' => 'rollbar_wp_logging_level' )
            );

            /* TODO
            // SECTION: Filter
            add_settings_section(
                'rollbar_wp_filter',
                __('Filter', 'rollbar'),
                array(&$this, 'filter_callback'),
                'rollbar_wp'
            );

            add_settings_field(
                'rollbar_wp_filter_enabled',
                __('Status', 'rollbar'),
                array(&$this, 'filter_enabled_render'),
                'rollbar_wp',
                'rollbar_wp_filter'
            );

            add_settings_field(
                'rollbar_wp_filter_plugins',
                __('Plugins', 'rollbar'),
                array(&$this, 'rollbar_wp_filter_plugins_render'),
                'rollbar_wp',
                'rollbar_wp_filter'
            );
            */
        }

        function status_render()
        {
            $php_logging_enabled = (!empty($this->options['php_logging_enabled'])) ? 1 : 0;
            $js_logging_enabled = (!empty($this->options['js_logging_enabled'])) ? 1 : 0;

            ?>

            <input type='checkbox' name='rollbar_wp[php_logging_enabled]'
                   id="rollbar_wp_php_logging_enabled" <?php checked($php_logging_enabled, 1); ?> value='1'/>
            <label for="rollbar_wp_php_logging_enabled"><?php _e('PHP error logging', 'rollbar-wp'); ?></label>
            &nbsp;
            <input type='checkbox' name='rollbar_wp[js_logging_enabled]'
                   id="rollbar_wp_js_logging_enabled" <?php checked($js_logging_enabled, 1); ?> value='1'/>
            <label for="rollbar_wp_js_logging_enabled"><?php _e('Javascript error logging', 'rollbar-wp'); ?></label>
            <?php
        }

        function access_token_render()
        {
            $client_side_access_token = (!empty($this->options['client_side_access_token'])) ? esc_attr(trim($this->options['client_side_access_token'])) : null;
            $server_side_access_token = (!empty($this->options['server_side_access_token'])) ? esc_attr(trim($this->options['server_side_access_token'])) : null;

            ?>
            <h4 style="margin: 5px 0;"><?php _e('Client Side Access Token', 'rollbar-wp'); ?> <small>(post_client_item)</small></h4>
            <input type='text' name='rollbar_wp[client_side_access_token]' id="rollbar_wp_client_side_access_token"
               value='<?php echo esc_attr(trim($client_side_access_token)); ?>' style="width: 300px;">

            <h4 style="margin: 15px 0 5px 0;"><?php _e('Server Side Access Token', 'rollbar-wp'); ?> <small>(post_server_item)</small></h4>
            <input type='text' name='rollbar_wp[server_side_access_token]' id="rollbar_wp_server_side_access_token"
                   value='<?php echo esc_attr(trim($server_side_access_token)); ?>' style="width: 300px;">
            <p>
                <small><?php _e('You can find your access tokens under your project settings: <strong>Project Access Tokens</strong>.', 'rollbar-wp'); ?></small>
            </p>
            <?php
        }

        function environment_render()
        {
            $environment = (!empty($this->options['environment'])) ? esc_attr(trim($this->options['environment'])) : '';

            ?>
            <input type='text' name='rollbar_wp[environment]' id="rollbar_wp_environment"
                   value='<?php echo esc_attr(trim($environment)); ?>'>
            <p>
                <small><?php _e('Define the current environment: e.g. "production" or "development".', 'rollbar-wp'); ?></small>
            </p>
            <?php
        }

        function logging_level_render()
        {
            $logging_level = (!empty($this->options['logging_level'])) ? esc_attr(trim($this->options['logging_level'])) : 1024;

            ?>

            <select name="rollbar_wp[logging_level]" id="rollbar_wp_logging_level">
                <option
                    value="1" <?php selected($logging_level, 1); ?>><?php _e('Fatal run-time errors (E_ERROR) only', 'rollbar-wp'); ?></option>
                <option
                    value="2" <?php selected($logging_level, 2); ?>><?php _e('Run-time warnings (E_WARNING) and above', 'rollbar-wp'); ?></option>
                <option
                    value="4" <?php selected($logging_level, 4); ?>><?php _e('Compile-time parse errors (E_PARSE) and above', 'rollbar-wp'); ?></option>
                <option
                    value="8" <?php selected($logging_level, 8); ?>><?php _e('Run-time notices (E_NOTICE) and above', 'rollbar-wp'); ?></option>
                <option
                    value="256" <?php selected($logging_level, 256); ?>><?php _e('User-generated error messages (E_USER_ERROR) and above', 'rollbar-wp'); ?></option>
                <option
                    value="512" <?php selected($logging_level, 512); ?>><?php _e('User-generated warning messages (E_USER_WARNING) and above', 'rollbar-wp'); ?></option>
                <option
                    value="1024" <?php selected($logging_level, 1024); ?>><?php _e('User-generated notice messages (E_USER_NOTICE) and above', 'rollbar-wp'); ?></option>
                <option
                    value="2048" <?php selected($logging_level, 2028); ?>><?php _e('Suggest code changes to ensure forward compatibility (E_STRICT) and above', 'rollbar-wp'); ?></option>
                <option
                    value="8192" <?php selected($logging_level, 8192); ?>><?php _e('Warnings about code that will not work in future versions (E_DEPRECATED) and above', 'rollbar-wp'); ?></option>
                <option
                    value="32767" <?php selected($logging_level, 32767); ?>><?php _e('Absolutely everything (E_ALL)', 'rollbar-wp'); ?></option>
            </select>

            <?php
        }

        function filter_enabled_render()
        {
            $filter_enabled = (!empty($this->options['filter_enabled'])) ? 1 : 0;

            ?>
            <input type='checkbox' name='rollbar_wp[filter_enabled]'
                   id="rollbar_wp_filter_enabled" <?php checked($filter_enabled, 1); ?> value='1'>
            <label for="rollbar_wp_filter_enabled"><?php _e('Enabled', 'rollbar-wp'); ?></label>
            <?php

        }

        function rollbar_wp_filter_plugins_render()
        {
            $filter_plugins = (!empty($this->options['filter_plugins'])) ? $this->options['filter_plugins'] : array();
            $plugins = get_option('active_plugins');

            if (count($plugins) != 0) {

                echo '<select name="rollbar_wp[filter_plugins][]" multiple="multiple">';

                foreach ($plugins as $plugin) {

                    $array = explode("/", $plugin);

                    if (!empty($array[0])) {

                        $selected = false;

                        if (sizeof($filter_plugins) != 0) {

                            foreach ($filter_plugins as $selected_plugin) {
                                if ($array[0] == $selected_plugin) {
                                    $selected = true;
                                }
                            }
                        }
                        ?>
                        <option
                            value="<?php echo $array[0]; ?>"<?php if ($selected) echo ' selected="selected"'; ?>><?php echo $array[0]; ?></option>
                        <?php
                    }
                }

                echo '</select>';
            } else {
                echo '<p>' . __('No plugins installed.', 'rollbar-wp') . '</p>';
            }
        }

        function filter_callback()
        {

            echo __('Coming soon!', 'rollbar');
        }


        function options_page()
        {

            ?>
            <form action='options.php' method='post'>

                <h2>Rollbar for WordPress</h2>

                <?php
                settings_fields('rollbar_wp');
                do_settings_sections('rollbar_wp');
                submit_button();
                ?>

            </form>
            <?php
        }
    }
}

new ROLLBAR_WP_SETTINGS();

?>