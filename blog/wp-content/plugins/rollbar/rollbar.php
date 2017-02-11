<?php
/**
 * Plugin Name:     Rollbar
 * Plugin URI:      https://wordpress.org/plugins/rollbar/
 * Description:     Rollbar full-stack error tracking for WordPress
 * Version:         1.0.3
 * Author:          flowdee
 * Author URI:      http://flowdee.de
 * Text Domain:     rollbar
 *
 * @package         RollbarWP
 * @author          flowdee
 * @copyright       Copyright (c) flowdee
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'Rollbar_WP' ) ) {

    /**
     * Main Rollbar_WP class
     *
     * @since       1.0.0
     */
    class Rollbar_WP {

        /**
         * @var         Rollbar_WP $instance The one true Rollbar_WP
         * @since       1.0.0
         */
        private static $instance;

        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      object self::$instance The one true Rollbar_WP
         */
        public static function instance() {
            if( !self::$instance ) {
                self::$instance = new Rollbar_WP();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_textdomain();
                self::$instance->hooks();
            }

            return self::$instance;
        }


        /**
         * Setup plugin constants
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function setup_constants() {
            // Plugin version
            define( 'ROLLBAR_WP_VER', '1.0.3' );

            // Plugin path
            define( 'ROLLBAR_WP_DIR', plugin_dir_path( __FILE__ ) );

            // Plugin URL
            define( 'ROLLBAR_WP_URL', plugin_dir_url( __FILE__ ) );
        }


        /**
         * Include necessary files
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function includes() {
            // Include scripts
            require_once ROLLBAR_WP_DIR . 'includes/class.settings.php';
            require_once ROLLBAR_WP_DIR . 'includes/functions.php';
        }


        /**
         * Run action and filter hooks
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function hooks() {
            add_action('init', 'rollbar_wp_initialize_php_logging');
            add_action('wp_head', 'rollbar_wp_initialize_js_logging');
        }

        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function load_textdomain() {
            load_plugin_textdomain( 'rollbar', false, dirname( plugin_basename( __FILE__  ) ) . '/languages/' );
        }
    }
} // End if class_exists check


/**
 * The main function responsible for returning the one true Rollbar_WP
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \Rollbar_WP The one true Rollbar_WP
 */
function Rollbar_WP_load() {
    return Rollbar_WP::instance();
}
add_action( 'plugins_loaded', 'Rollbar_WP_load' );
