<?php
/*

 * *************************************************************************

  Plugin Name:  bbPress WP Tweaks
  Plugin URI:   http://www.veppa.com/blog/bbpress-wp-tweaks/
  Description:  Wordpress theme integration for bbpress 2.0 plugin and above.
  Version:      1.3.1
  Author:       veppa
  Author URI:   http://www.veppa.com/

 * *************************************************************************

  Copyright (C) 2012 veppa

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

 * ************************************************************************ */

// Seems some themes bundle this plugin and then users install it,
// resulting in double output. This should be avoided, so use a global.
if(!isset($bbpress_wp_tweaks))
	$bbpress_wp_tweaks = false;

if(!class_exists('BbpressWpTweaks')) :

	class BbpressWpTweaks
	{
		# Configuration has been moved to the new options page at Settings -> Clean Archives.
		# You can also set the configuration via the shortcode tag.

		var $version = '1.3.1';
		var $is_bbp = false;
		var $sidebar_id = 'sidebar-bbpress';
		var $loading_bbp_sidebar = false;
		var $is_main_sidebar = false;
		var $bbp_templates = array('index.php', 'page.php', 'single.php');
		var $skip_widgets = array();

		// Class initialization
		function BbpressWpTweaks()
		{
			if(!function_exists('add_action'))
				return;

			// Load up the localization file if we're using WordPress in a different language
			// Place it in this plugin's "languages" folder and name it "car-[value in wp-config].mo"
			load_plugin_textdomain('bbpress-wp-tweaks', false, '/bbpress-wp-tweaks/languages');

			// Make sure all the plugin options have defaults set
			if(FALSE === get_option('default_wrapper_file'))
				add_option('default_wrapper_file', 'bbpress.php');

			// add widget to theme widgets
			add_action('admin_menu', array(&$this, 'AddAdminMenu'));
			add_filter('plugin_action_links', array(&$this, 'filter_plugin_actions'), 10, 2);


			// check if bbpress enabled.
			//if (!function_exists('add_action'))
			//return;

			add_action('widgets_init', array(&$this, 'vp_register_sidebar'));
			add_action('get_sidebar', array(&$this, 'vp_get_sidebar'));
			add_filter('sidebars_widgets', array(&$this, 'vp_sidebars_widgets'));
			if(function_exists('bbp_get_query_template'))
			{
				// this is fix to work in bbpress vrestion >= 2.1 
				add_filter('bbp_get_bbpress_template', array(&$this, 'vp_bbp_get_theme_compat_templates'));
			}
			else
			{
				// this will work in bbpress version 2
				add_filter('bbp_get_theme_compat_templates', array(&$this, 'vp_bbp_get_theme_compat_templates'));
			}



			// add widgets
			add_action('widgets_init', array('BbpressWpTweaks_Login_Links_Widget', 'register_widget'));
		}

		/**
		 * Register sidebar for forum
		 * 
		 * @uses register_sidebar()
		 */
		function vp_register_sidebar()
		{
			register_sidebar(array(
				'name' => __('bbPress sidebar', 'twentyeleven'),
				'id' => $this->sidebar_id,
				'description' => __('The sidebar for bbPress forum', 'twentyeleven'),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget' => "</aside>",
				'before_title' => '<h3 class="widget-title">',
				'after_title' => '</h3>',
			));
		}

		/**
		 * mark the next sidebar request as main sidebar request
		 *
		 */
		function vp_get_sidebar($data)
		{
			if(is_null($data))
			{
				// unnamed sidebar is main sidebar
				$this->is_main_sidebar = true;
			}
			else
			{
				// added to version 1.3 in order to make compatible with wp 3.5
				$this->is_main_sidebar = false;
			}
		}

		function vp_sidebars_widgets($data)
		{
			// if bbpress enambled and main sidebar requested
			if($this->is_bbp && $this->is_main_sidebar)
			{
				// switch sidebar if bbrepss sidebar has some widgets in it
				if(!empty($data[$this->sidebar_id]))
				{
					if(isset($data['sidebar-1']))
					{
						// uses sidebar-1 as main sidebar in most themes
						// set forum sidebar as main sidebar
						$main_sidebar_key = 'sidebar-1';
					}
					else
					{
						// get first sidebar which should be main in most themes
						foreach($data as $k => $v)
						{
							if(strpos($k, 'inactive') === false)
							{
								$main_sidebar_key = $k;
								break;
							}
						}
					}

					// replace main sidebar with bbpress sidebar
					if(isset($main_sidebar_key))
					{
						$data[$main_sidebar_key] = $data[$this->sidebar_id];
					}
				}

				// removed from version 1.3 in order to make compatible with wp 3.5
				// reset main sidebar request
				// $this->is_main_sidebar = false;
			}

			// return modified widgets array
			return $data;
		}

		/**
		 * reorder priority of template files to use as forum wrapper
		 *
		 */
		function vp_bbp_get_theme_compat_templates($templates)
		{
			// put index.php theme before any other theme from blog
			/*
			  $templates = array(
			  'bbpress.php',
			  'forum.php',
			  'page.php',
			  'single.php',
			  'index.php'
			  );
			 */

			// searhced for bbpress compatible theme files . then bbpress page is requested
			$this->is_bbp = true;
			$this->bbp_templates = $templates;

			//return $templates;

			$default_wrapper_file = get_option('default_wrapper_file');
			if(strlen($default_wrapper_file))
			{
				$return = array($default_wrapper_file);
			}
			else
			{
				$return = array();
			}


			foreach($templates as $t)
			{
				if(!in_array($t, $return))
				{
					$return[] = $t;
				}
			}
			// echo '[vp_bbp_get_theme_compat_templates]';
			//print_r($return);
			return $return;
		}

		/**
		 *  add settings link to plugin listing
		 */
		function filter_plugin_actions($links, $file)
		{
			//Static so we don't call plugin_basename on every plugin row.
			static $this_plugin;
			if(!$this_plugin)
				$this_plugin = plugin_basename(__FILE__);

			if($file == $this_plugin)
			{
				$settings_link = '<a href="options-general.php?page=bbpress-wp-tweaks">' . __('Settings') . '</a>';
				array_unshift($links, $settings_link); // before other links
			}
			return $links;
		}

		/**
		 *  Register the admin menu
		 */
		function AddAdminMenu()
		{
			add_options_page(__('bbPress WP Tweaks', 'bbpress-wp-tweaks'), __('bbPress WP Tweaks', 'bbpress-wp-tweaks'), 'manage_options', 'bbpress-wp-tweaks', array(&$this, 'OptionsPage'));
		}

		/**
		 * The options page for this plugin
		 */
		function OptionsPage()
		{
			?>
			<div class="wrap">
				<h2><?php _e('bbPress WP Tweaks', 'bbpress-wp-tweaks'); ?></h2>
			<?php
			if(!function_exists('bbp_logout_link') || !function_exists('bbp_get_theme_compat_templates'))
			{
				// no bbpress detected. 
				echo '<div class="updated error-message">' . __('bbPress installation is not detected. This plugin works with bbPress. Install bbPress first.', 'bbpress-wp-tweaks') . '</div>';
				echo '</div>';
				return;
			}

			$default_wrapper_file = get_option('default_wrapper_file');

			// get settings from bbpress plugin
			$_default_wrapper_file = bbp_get_theme_compat_templates();
			
			
			if(!$this->is_bbp)
			{
				// no bbpress detected. 
				echo '<div class="updated error-message">' . __('bbPress installation detected but bbPress wp tweaks plugin is not initialized. Please send this bug to <a href="http://www.veppa.com/index/support/">bbPress wp tweaks developer</a>.', 'bbpress-wp-tweaks') . '</div>';
				echo '</div>';
				return;
			}
			
			
			?>
				<form method="post" action="options.php">
				<?php wp_nonce_field('update-options') ?>
					<table class="form-table">					

						<tr valign="top">
							<th scope="row"><?php _e('Default forum wrapper', 'bbpress-wp-tweaks'); ?></th>
							<td>
								<p><?php _e('Select template file that you prefer bbPress rendered in. Make sure template file is present in your theme directory. If sidebar is not displaying make sure you put some wodgets to "bbPress sidebar" in <a href="widgets.php">widgets</a> page then try different forum wrapper from this list.', 'bbpress-wp-tweaks'); ?></p>
								<p>
									<?php foreach($this->bbp_templates as $t): ?>								
										<label style="font-weight: <?php echo ( locate_template( $t, false, false )?'bold':'normal');?>;"><input name="default_wrapper_file" type="radio" value="<?php _e($t); ?>" <?php checked($t, $default_wrapper_file); ?> /> <?php _e($t); ?></label><br />
									<?php endforeach; ?>
								</p>
								<p><?php _e('Files with <b>Bold text</b> exist.', 'bbpress-wp-tweaks'); ?></p>
							</td>
						</tr>

					</table>

					<p class="submit">
						<input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
						<input type="hidden" name="action" value="update" />
						<input type="hidden" name="page_options" value="default_wrapper_file" />
					</p>

				</form>
			</div>

			<?php
		}

	}

	/**
	 * BbpressWpTweaks Login Links Widget
	 *
	 * Adds a widget which displays the login, register, logout links
	 *
	 * @uses WP_Widget
	 */
	class BbpressWpTweaks_Login_Links_Widget extends WP_Widget
	{

		/**
		 * Register the widget
		 *
		 * @uses register_widget()
		 */
		public static function register_widget()
		{
			register_widget('BbpressWpTweaks_Login_Links_Widget');
		}

		/**
		 * BbpressWpTweaks Login Links Widget
		 *
		 * Registers the login widget
		 *
		 * @uses apply_filters() Calls 'bbpresswptweaks_login_links_widget_options' with the
		 *                        widget options
		 */
		function BbpressWpTweaks_Login_Links_Widget()
		{
			$widget_ops = apply_filters('bbpresswptweaks_login_links_widget_options', array(
				'classname' => 'bbpresswptweaks_login_links_widget',
				'description' => __('The login links widget. Displays login, register, logout links.', 'bbpress-wp-tweaks')
					));

			parent::WP_Widget(false, __('bbPress Login Links Widget', 'bbpress-wp-tweaks'), $widget_ops);
		}

		/**
		 * Displays the output, the login form
		 *
		 * @param mixed $args Arguments
		 * @param array $instance Instance
		 * @uses function_exists() to check if bbpress installed
		 * @uses apply_filters() Calls 'bbp_login_widget_title' with the title
		 * @uses get_template_part() To get the login/logged in form
		 */
		function widget($args, $instance)
		{
			if(!function_exists('bbp_logout_link'))
			{
				// no bbress detected then forum login widget is not required
				return false;
			}

			extract($args);

			$title = $instance['title'];
			$css_class_logout = $instance['css_class_logout'];
			$css_class_login = $this->get_css_class_login($instance);

			echo $before_widget;

			if(!empty($title))
				echo $before_title . $title . $after_title;

			// get curretn url to redirect back 
			$redirect_to = esc_url($_SERVER["REQUEST_URI"]);

			if(!is_user_logged_in())
			{
				// get login url
				ob_start();
				bbp_wp_login_action(array('context' => 'login_post'));
				$login_url = ob_get_clean();
				echo '<div class="' . $css_class_login . '">
					<a href="' . $login_url . '?redirect_to=' . $redirect_to . '" rel="nofollow">' . __('Log in', 'bbpress') . '</a>
					- or - 
					<a href="' . $login_url . '?action=register" rel="nofollow">' . __('Register', 'bbpress') . '</a>
				</div>';
			}
			else
			{
				?>
				<div class="bbp-logged-in<?php echo ' ' . $css_class_logout ?>">
					<a href="<?php bbp_user_profile_url(bbp_get_current_user_id()); ?>" class="submit user-submit"><?php echo get_avatar(bbp_get_current_user_id(), '40'); ?></a>
					<h4><?php bbp_user_profile_link(bbp_get_current_user_id()); ?></h4>
				<?php bbp_logout_link($redirect_to); ?>
				</div>
					<?php
				}

				echo $after_widget;
			}

			/**
			 * Update the login widget options
			 *
			 * @param array $new_instance The new instance options
			 * @param array $old_instance The old instance options
			 */
			function update($new_instance, $old_instance)
			{
				$instance = $old_instance;
				$instance['title'] = strip_tags($new_instance['title']);
				$instance['css_class_login'] = strip_tags($new_instance['css_class_login']);
				$instance['css_class_logout'] = strip_tags($new_instance['css_class_logout']);

				return $instance;
			}

			/**
			 * Output the login links widget options form
			 *
			 * @param $instance Instance
			 * @uses WP_Widget::get_field_id() To output the field id
			 * @uses WP_Widget::get_field_name() To output the field name
			 */
			function form($instance)
			{
				// Form values
				if(!isset($instance['title']))
				{
					$title = __('Forum Login', 'bbpress-wp-tweaks');
				}
				else
				{
					$title = !empty($instance['title']) ? esc_attr($instance['title']) : '';
				}
				$css_class_login = $this->get_css_class_login($instance);
				$css_class_logout = !empty($instance['css_class_logout']) ? esc_attr($instance['css_class_logout']) : '';
				?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'bbpress-wp-tweaks'); ?>
					<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('css_class_login'); ?>"><?php _e('CSS class login:', 'bbpress-wp-tweaks'); ?>
					<input class="widefat" id="<?php echo $this->get_field_id('css_class_login'); ?>" name="<?php echo $this->get_field_name('css_class_login'); ?>" type="text" value="<?php echo $css_class_login; ?>" /></label>
			<?php echo __('default:', 'bbpress-wp-tweaks') . $this->get_css_class_login(); ?>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('css_class_logout'); ?>"><?php _e('CSS class logout:', 'bbpress-wp-tweaks'); ?>
					<input class="widefat" id="<?php echo $this->get_field_id('css_class_logout'); ?>" name="<?php echo $this->get_field_name('css_class_logout'); ?>" type="text" value="<?php echo $css_class_logout; ?>" /></label>
			</p>
			<?php
		}

		/**
		 * get default login CSS class. login links should stand out from rest of page
		 *
		 * @param $instance Instance
		 */
		function get_css_class_login($instance = null)
		{

			$default_css_class = 'bbp-template-notice';

			if(is_null($instance))
			{
				// no instance then return default CSS class
				return $default_css_class;
			}
			else
			{
				$css_class = (isset($instance['css_class']) ? (!empty($instance['css_class']) ? esc_attr($instance['css_class']) : '') : $default_css_class);
				return $css_class;
			}
		}

	}

// Start this plugin once all other plugins are fully loaded
	add_action('after_setup_theme', create_function('', 'global $BbpressWpTweaks; $BbpressWpTweaks = new BbpressWpTweaks();'));
  


endif; // class_exists