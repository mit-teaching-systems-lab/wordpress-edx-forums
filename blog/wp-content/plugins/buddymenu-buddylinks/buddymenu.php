<?php
/*
Plugin Name: BuddyMenu
Plugin URI: http://journalxtra.com/websiteadvice/wordpress/use-buddypress-dynamic-links-in-your-network-with-buddymenu-buddylinks-5316/
Description: BuddyPress widget menu. Does three things awesomely well: BuddyPress menu widget, BuddyPress menu shortcode and BuddyPress dynamic link shortcode. Put a BuddyPress menu or dynamic link in your sidebar, post, page, widget, footer or anywhere else. Needs BuddyPress to work. There is no need to network activate in WP Multisite. Displays when a visitor is logged in.
Version: 2.2.0
Author: Lee Hodson
Author URI: http://vizred.com/

---------------------------------------------------------------------------

Copyright 2013  Lee Hodson  (email : leehodson@vizred.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

---------------------------------------------------------------------------

*/
?>
<?php
function buddymenu_style_fn()  
{  
    wp_register_style( 'buddymenu-style', plugins_url( '/buddymenu-style.css', __FILE__ ), array(), '20130328', 'all' );  
    wp_enqueue_style( 'buddymenu-style' );
}

add_action( 'wp_enqueue_scripts', 'buddymenu_style_fn' );

class buddymenu_widget_class extends WP_Widget {
	function buddymenu_widget_class() {
	 //Load Language
	 load_plugin_textdomain( 'buddymenu-plugin-handle', false, dirname(plugin_basename(__FILE__)) .  '/lang' );
	 $widget_ops = array('classname' => 'widget_text', 'description' => __('Shows BuddyPress Menus.', 'buddymenu-plugin-handle') );
	 $control_ops = array('width' => 400, 'height' => 350);
	 //Create widget
	 $this->WP_Widget('buddymenu', __('BuddyMenu', 'buddymenu-plugin-handle'), $widget_ops, $control_ops);
	}


// Widget output

  function widget($args, $instance) {

	extract($args, EXTR_SKIP);

	$title = empty($instance['title']) ? __('BuddyMenu', 'buddymenu-plugin-handle') : apply_filters('widget_title', $instance['title']);
	$parameters = array(

		'title' => esc_attr($instance['title']), // Text
		'showtitle' => (bool) $instance['showtitle'], // Boolean - show widget title
		'showtitle' => esc_attr($instance['showtitle']), // Show title to logged out users
		'showwidget' => esc_attr($instance['showwidget']), // Show widget to logged out users
		'showavatar' => (bool) $instance['showavatar'], // Boolean - show user avatar
		'welcome' => esc_attr($instance['welcome']), // Show message before avatar image

		'menu_one' => esc_attr($instance['menu_one']), // Text - menu item selection
		'menu_one_custom_slug' => esc_attr($instance['menu_one_custom_slug']), // Text - menu item selection
		'menu_one_custom_url' => esc_attr($instance['menu_one_custom_url']), // Text - menu item selection
		'label_one' => esc_attr($instance['label_one']), // Text - custom label for menu item
		'menu_icon_one' => esc_attr($instance['menu_icon_one']), // Text - menu item icon selection
		'custom_icon_one' => esc_url($instance['custom_icon_one']), // URL - custom icon URL
		'menu_one_lo' => esc_attr($instance['menu_one_lo']), // Show menu item to logged out visitors

		'menu_two' => esc_attr($instance['menu_two']),
		'menu_two_custom_slug' => esc_attr($instance['menu_two_custom_slug']),
		'menu_two_custom_url' => esc_attr($instance['menu_two_custom_url']),
		'label_two' => esc_attr($instance['label_two']),
		'menu_icon_two' => esc_attr($instance['menu_icon_two']),
		'custom_icon_two' => esc_url($instance['custom_icon_two']),
		'menu_two_lo' => esc_attr($instance['menu_two_lo']),

		'menu_three' => esc_attr($instance['menu_three']),
		'menu_three_custom_slug' => esc_attr($instance['menu_three_custom_slug']),
		'menu_three_custom_url' => esc_attr($instance['menu_three_custom_url']),
		'label_three' => esc_attr($instance['label_three']),
		'menu_icon_three' => esc_attr($instance['menu_icon_three']),
		'custom_icon_three' => esc_url($instance['custom_icon_three']),
		'menu_three_lo' => esc_attr($instance['menu_three_lo']),

		'menu_four' => esc_attr($instance['menu_four']), // Text - menu item selection
		'menu_four_custom_slug' => esc_attr($instance['menu_four_custom_slug']), // Text - menu item selection
		'menu_four_custom_url' => esc_attr($instance['menu_four_custom_url']), // Text - menu item selection
		'label_four' => esc_attr($instance['label_four']), // Text - custom label for menu item
		'menu_icon_four' => esc_attr($instance['menu_icon_four']), // Text - menu item icon selection
		'custom_icon_four' => esc_url($instance['custom_icon_four']), // URL - custom icon URL
		'menu_four_lo' => esc_attr($instance['menu_four_lo']),

		'menu_five' => esc_attr($instance['menu_five']), // Text - menu item selection
		'menu_five_custom_slug' => esc_attr($instance['menu_five_custom_slug']), // Text - menu item selection
		'menu_five_custom_url' => esc_attr($instance['menu_five_custom_url']), // Text - menu item selection
		'label_five' => esc_attr($instance['label_five']), // Text - custom label for menu item
		'menu_icon_five' => esc_attr($instance['menu_icon_five']), // Text - menu item icon selection
		'custom_icon_five' => esc_url($instance['custom_icon_five']), // URL - custom icon URL
		'menu_five_lo' => esc_attr($instance['menu_five_lo']),

		'menu_six' => esc_attr($instance['menu_six']), // Text - menu item selection
		'menu_six_custom_slug' => esc_attr($instance['menu_six_custom_slug']), // Text - menu item selection
		'menu_six_custom_url' => esc_attr($instance['menu_six_custom_url']), // Text - menu item selection
		'label_six' => esc_attr($instance['label_six']), // Text - custom label for menu item
		'menu_icon_six' => esc_attr($instance['menu_icon_six']), // Text - menu item icon selection
		'custom_icon_six' => esc_url($instance['custom_icon_six']), // URL - custom icon URL
		'menu_six_lo' => esc_attr($instance['menu_six_lo']),

		'menu_seven' => esc_attr($instance['menu_seven']), // Text - menu item selection
		'menu_seven_custom_slug' => esc_attr($instance['menu_seven_custom_slug']), // Text - menu item selection
		'menu_seven_custom_url' => esc_attr($instance['menu_seven_custom_url']), // Text - menu item selection
		'label_seven' => esc_attr($instance['label_seven']), // Text - custom label for menu item
		'menu_icon_seven' => esc_attr($instance['menu_icon_seven']), // Text - menu item icon selection
		'custom_icon_seven' => esc_url($instance['custom_icon_seven']), // URL - custom icon URL
		'menu_seven_lo' => esc_attr($instance['menu_seven_lo']),

		'menu_eight' => esc_attr($instance['menu_eight']), // Text - menu item selection
		'menu_eight_custom_slug' => esc_attr($instance['menu_eight_custom_slug']), // Text - menu item selection
		'menu_eight_custom_url' => esc_attr($instance['menu_eight_custom_url']), // Text - menu item selection
		'label_eight' => esc_attr($instance['label_eight']), // Text - custom label for menu item
		'menu_icon_eight' => esc_attr($instance['menu_icon_eight']), // Text - menu item icon selection
		'custom_icon_eight' => esc_url($instance['custom_icon_eight']), // URL - custom icon URL
		'menu_eight_lo' => esc_attr($instance['menu_eight_lo']),

		'menu_nine' => esc_attr($instance['menu_nine']), // Text - menu item selection
		'menu_nine_custom_slug' => esc_attr($instance['menu_nine_custom_slug']), // Text - menu item selection
		'menu_nine_custom_url' => esc_attr($instance['menu_nine_custom_url']), // Text - menu item selection
		'label_nine' => esc_attr($instance['label_nine']), // Text - custom label for menu item
		'menu_icon_nine' => esc_attr($instance['menu_icon_nine']), // Text - menu item icon selection
		'custom_icon_nine' => esc_url($instance['custom_icon_nine']), // URL - custom icon URL
		'menu_nine_lo' => esc_attr($instance['menu_nine_lo']),

		'menu_ten' => esc_attr($instance['menu_ten']), // Text - menu item selection
		'menu_ten_custom_slug' => esc_attr($instance['menu_ten_custom_slug']), // Text - menu item selection
		'menu_ten_custom_url' => esc_attr($instance['menu_ten_custom_url']), // Text - menu item selection
		'label_ten' => esc_attr($instance['label_ten']), // Text - custom label for menu item
		'menu_icon_ten' => esc_attr($instance['menu_icon_ten']), // Text - menu item icon selection
		'custom_icon_ten' => esc_url($instance['custom_icon_ten']), // URL - custom icon URL
		'menu_ten_lo' => esc_attr($instance['menu_ten_lo']),

		'menu_eleven' => esc_attr($instance['menu_eleven']), // Text - menu item selection
		'menu_eleven_custom_slug' => esc_attr($instance['menu_eleven_custom_slug']), // Text - menu item selection
		'menu_eleven_custom_url' => esc_attr($instance['menu_eleven_custom_url']), // Text - menu item selection
		'label_eleven' => esc_attr($instance['label_eleven']), // Text - custom label for menu item
		'menu_icon_eleven' => esc_attr($instance['menu_icon_eleven']), // Text - menu item icon selection
		'custom_icon_eleven' => esc_url($instance['custom_icon_eleven']), // URL - custom icon URL
		'menu_eleven_lo' => esc_attr($instance['menu_eleven_lo']),

		'menu_twelve' => esc_attr($instance['menu_twelve']), // Text - menu item selection
		'menu_twelve_custom_slug' => esc_attr($instance['menu_twelve_custom_slug']), // Text - menu item selection
		'menu_twelve_custom_url' => esc_attr($instance['menu_twelve_custom_url']), // Text - menu item selection
		'label_twelve' => esc_attr($instance['label_twelve']), // Text - custom label for menu item
		'menu_icon_twelve' => esc_attr($instance['menu_icon_twelve']), // Text - menu item icon selection
		'custom_icon_twelve' => esc_url($instance['custom_icon_twelve']), // URL - custom icon URL
		'menu_twelve_lo' => esc_attr($instance['menu_twelve_lo']),

		'layout' => esc_attr($instance['layout']), // Test Selector
	);

	// Check visitor is logged in

	$showtitle = $instance['showtitle'];
	$showtitle = $instance['showtitle'];
	$showwidget = $instance['showwidget'];

	if ($showwidget!='no-one') {

	    echo $before_widget;
	
	      if ( !is_user_logged_in() ) {
		  if ($showtitle!='no-one') {

		    if ( $showtitle=='logged-out' || $showtitle=='logged-in-out' ) {

		      if ( !empty( $title ) ) {
		      echo $before_title . $title . $after_title;
		      }

		    }
		  }

		  if ( $showwidget=='logged-out' || $showwidget=='logged-in-out' ) {

		      // Call function that does the work
			  buddymenu($parameters);
		      // End Work

		  }



	      }

	      if ( is_user_logged_in() ) {
		  if ($showtitle!='no-one') {

		      if ( !empty( $title ) ) {
		      echo $before_title . $title . $after_title;
		      }

		  }

		  if ( $showwidget=='logged-in' || $showwidget=='logged-in-out' ) {

		      // Call function that does the work
			  buddymenu($parameters);
		      // End Work

		  }

	      }

	    echo $after_widget;

	}

  }
// End of widget output

	
//Update widget options
  function update($new_instance, $old_instance) {


		$instance = $old_instance;
		//get old variables
		$instance['title'] = esc_attr($new_instance['title']);
		$instance['showtitle'] = esc_attr($new_instance['showtitle']);
		$instance['showwidget'] = esc_attr($new_instance['showwidget']);
		$instance['showavatar'] = $new_instance['showavatar'] ? 1 : 0;
		$instance['welcome'] = esc_attr($new_instance['welcome']);

		$instance['menu_one'] = esc_attr($new_instance['menu_one']);
		$instance['menu_one_custom_slug'] = esc_attr($new_instance['menu_one_custom_slug']);
		$instance['menu_one_custom_url'] = esc_attr($new_instance['menu_one_custom_url']);
		$instance['label_one'] = esc_attr($new_instance['label_one']);
		$instance['menu_icon_one'] = esc_attr($new_instance['menu_icon_one']);
		$instance['custom_icon_one'] = esc_url($new_instance['custom_icon_one']);
		$instance['menu_one_lo'] = esc_attr($new_instance['menu_one_lo']);

		$instance['menu_two'] = esc_attr($new_instance['menu_two']);
		$instance['menu_two_custom_slug'] = esc_attr($new_instance['menu_two_custom_slug']);
		$instance['menu_two_custom_url'] = esc_attr($new_instance['menu_two_custom_url']);
		$instance['label_two'] = esc_attr($new_instance['label_two']);
		$instance['menu_icon_two'] = esc_attr($new_instance['menu_icon_two']);
		$instance['custom_icon_two'] = esc_url($new_instance['custom_icon_two']);
		$instance['menu_two_lo'] = esc_attr($new_instance['menu_two_lo']);

		$instance['menu_three'] = esc_attr($new_instance['menu_three']);
		$instance['menu_three_custom_slug'] = esc_attr($new_instance['menu_three_custom_slug']);
		$instance['menu_three_custom_url'] = esc_attr($new_instance['menu_three_custom_url']);
		$instance['label_three'] = esc_attr($new_instance['label_three']);
		$instance['menu_icon_three'] = esc_attr($new_instance['menu_icon_three']);
		$instance['custom_icon_three'] = esc_url($new_instance['custom_icon_three']);
		$instance['menu_three_lo'] = esc_attr($new_instance['menu_three_lo']);

		$instance['menu_four'] = esc_attr($new_instance['menu_four']);
		$instance['menu_four_custom_slug'] = esc_attr($new_instance['menu_four_custom_slug']);
		$instance['menu_four_custom_url'] = esc_attr($new_instance['menu_four_custom_url']);
		$instance['label_four'] = esc_attr($new_instance['label_four']);
		$instance['menu_icon_four'] = esc_attr($new_instance['menu_icon_four']);
		$instance['custom_icon_four'] = esc_url($new_instance['custom_icon_four']);
		$instance['menu_four_lo'] = esc_attr($new_instance['menu_four_lo']);

		$instance['menu_five'] = esc_attr($new_instance['menu_five']);
		$instance['menu_five_custom_slug'] = esc_attr($new_instance['menu_five_custom_slug']);
		$instance['menu_five_custom_url'] = esc_attr($new_instance['menu_five_custom_url']);
		$instance['label_five'] = esc_attr($new_instance['label_five']);
		$instance['menu_icon_five'] = esc_attr($new_instance['menu_icon_five']);
		$instance['custom_icon_five'] = esc_url($new_instance['custom_icon_five']);
		$instance['menu_five_lo'] = esc_attr($new_instance['menu_five_lo']);

		$instance['menu_six'] = esc_attr($new_instance['menu_six']);
		$instance['menu_six_custom_slug'] = esc_attr($new_instance['menu_six_custom_slug']);
		$instance['menu_six_custom_url'] = esc_attr($new_instance['menu_six_custom_url']);
		$instance['label_six'] = esc_attr($new_instance['label_six']);
		$instance['menu_icon_six'] = esc_attr($new_instance['menu_icon_six']);
		$instance['custom_icon_six'] = esc_url($new_instance['custom_icon_six']);
		$instance['menu_six_lo'] = esc_attr($new_instance['menu_six_lo']);

		$instance['menu_seven'] = esc_attr($new_instance['menu_seven']);
		$instance['menu_seven_custom_slug'] = esc_attr($new_instance['menu_seven_custom_slug']);
		$instance['menu_seven_custom_url'] = esc_attr($new_instance['menu_seven_custom_url']);
		$instance['label_seven'] = esc_attr($new_instance['label_seven']);
		$instance['menu_icon_seven'] = esc_attr($new_instance['menu_icon_seven']);
		$instance['custom_icon_seven'] = esc_url($new_instance['custom_icon_seven']);
		$instance['menu_seven_lo'] = esc_attr($new_instance['menu_seven_lo']);

		$instance['menu_eight'] = esc_attr($new_instance['menu_eight']);
		$instance['menu_eight_custom_slug'] = esc_attr($new_instance['menu_eight_custom_slug']);
		$instance['menu_eight_custom_url'] = esc_attr($new_instance['menu_eight_custom_url']);
		$instance['label_eight'] = esc_attr($new_instance['label_eight']);
		$instance['menu_icon_eight'] = esc_attr($new_instance['menu_icon_eight']);
		$instance['custom_icon_eight'] = esc_url($new_instance['custom_icon_eight']);
		$instance['menu_eight_lo'] = esc_attr($new_instance['menu_eight_lo']);

		$instance['menu_nine'] = esc_attr($new_instance['menu_nine']);
		$instance['menu_nine_custom_slug'] = esc_attr($new_instance['menu_nine_custom_slug']);
		$instance['menu_nine_custom_url'] = esc_attr($new_instance['menu_nine_custom_url']);
		$instance['label_nine'] = esc_attr($new_instance['label_nine']);
		$instance['menu_icon_nine'] = esc_attr($new_instance['menu_icon_nine']);
		$instance['custom_icon_nine'] = esc_url($new_instance['custom_icon_nine']);
		$instance['menu_nine_lo'] = esc_attr($new_instance['menu_nine_lo']);

		$instance['menu_ten'] = esc_attr($new_instance['menu_ten']);
		$instance['menu_ten_custom_slug'] = esc_attr($new_instance['menu_ten_custom_slug']);
		$instance['menu_ten_custom_url'] = esc_attr($new_instance['menu_ten_custom_url']);
		$instance['label_ten'] = esc_attr($new_instance['label_ten']);
		$instance['menu_icon_ten'] = esc_attr($new_instance['menu_icon_ten']);
		$instance['custom_icon_ten'] = esc_url($new_instance['custom_icon_ten']);
		$instance['menu_ten_lo'] = esc_attr($new_instance['menu_ten_lo']);

		$instance['menu_eleven'] = esc_attr($new_instance['menu_eleven']);
		$instance['menu_eleven_custom_slug'] = esc_attr($new_instance['menu_eleven_custom_slug']);
		$instance['menu_eleven_custom_url'] = esc_attr($new_instance['menu_eleven_custom_url']);
		$instance['label_eleven'] = esc_attr($new_instance['label_eleven']);
		$instance['menu_icon_eleven'] = esc_attr($new_instance['menu_icon_eleven']);
		$instance['custom_icon_eleven'] = esc_url($new_instance['custom_icon_eleven']);
		$instance['menu_eleven_lo'] = esc_attr($new_instance['menu_eleven_lo']);

		$instance['menu_twelve'] = esc_attr($new_instance['menu_twelve']);
		$instance['menu_twelve_custom_slug'] = esc_attr($new_instance['menu_twelve_custom_slug']);
		$instance['menu_twelve_custom_url'] = esc_attr($new_instance['menu_twelve_custom_url']);
		$instance['label_twelve'] = esc_attr($new_instance['label_twelve']);
		$instance['menu_icon_twelve'] = esc_attr($new_instance['menu_icon_twelve']);
		$instance['custom_icon_twelve'] = esc_url($new_instance['custom_icon_twelve']);
		$instance['menu_twelve_lo'] = esc_attr($new_instance['menu_twelve_lo']);

		$instance['layout'] = esc_attr($new_instance['layout']);

		return $instance;
  } //end of update
	
//Widget options form
  function form($instance) {
		$instance = wp_parse_args( (array) $instance, array(
		    'title' => __('BuddyMenu','buddymenu-plugin-handle'),
		    'title' => 'BuddyMenu',
		    'showtitle'=>'logged-in',
		    'showwidget'=>'logged-in',
		    'showavatar'=>'0',
		    'welcome'=>'',

		    'menu_one' => 'media',
		    'menu_one_custom_slug' => '',
		    'menu_one_custom_url' => '',
		    'label_one' => 'Media',
		    'menu_icon_one' => 'media',
		    'custom_icon_one' => '',
		    'menu_one_lo'=>'logged-in',

		    'menu_two' => 'activity',
		    'menu_two_custom_slug' => '',
		    'menu_two_custom_url' => '',
		    'label_two' => 'Activity',
		    'menu_icon_two' => 'activity',
		    'custom_icon_two' => '',
		    'menu_two_lo'=>'logged-in',

		    'menu_three' => 'forums',
		    'menu_three_custom_slug' => '',
		    'menu_three_custom_url' => '',
		    'label_three' => 'Forums',
		    'menu_icon_three' => 'forums',
		    'custom_icon_three' => '',
		    'menu_three_lo'=>'logged-in',

		    'menu_four' => 'friends',
		    'menu_four_custom_slug' => '',
		    'menu_four_custom_url' => '',
		    'label_four' => 'Friends',
		    'menu_icon_four' => 'friends',
		    'custom_icon_four' => '',
		    'menu_four_lo'=>'logged-in',

		    'menu_five' => 'messages',
		    'menu_five_custom_slug' => '',
		    'menu_five_custom_url' => '',
		    'label_five' => 'Messages',
		    'menu_icon_five' => 'messages',
		    'custom_icon_five' => '',
		    'menu_five_lo'=>'logged-in',

		    'menu_six' => 'profile',
		    'menu_six_custom_slug' => '',
		    'menu_six_custom_url' => '',
		    'label_six' => 'Profile',
		    'menu_icon_six' => 'profile',
		    'custom_icon_six' => '',
		    'menu_six_lo'=>'logged-in',

		    'menu_seven' => 'profile/edit',
		    'menu_seven_custom_slug' => '',
		    'menu_seven_custom_url' => '',
		    'label_seven' => 'Edit Profile',
		    'menu_icon_seven' => 'edit-profile',
		    'custom_icon_seven' => '',
		    'menu_seven_lo'=>'logged-in',

		    'menu_eight' => 'avatar',
		    'menu_eight_custom_slug' => '',
		    'menu_eight_custom_url' => '',
		    'label_eight' => 'Change Avatar',
		    'menu_icon_eight' => 'avatar',
		    'custom_icon_eight' => '',
		    'menu_eight_lo'=>'logged-in',

		    'menu_nine' => 'settings',
		    'menu_nine_custom_slug' => '',
		    'menu_nine_custom_url' => '',
		    'label_nine' => 'Settings',
		    'menu_icon_nine' => 'settings',
		    'custom_icon_nine' => '',
		    'menu_nine_lo'=>'logged-in',

		    'menu_ten' => 'void',
		    'menu_ten_custom_slug' => '',
		    'menu_ten_custom_url' => '',
		    'label_ten' => 'Custom Slug',
		    'menu_icon_ten' => 'void',
		    'custom_icon_ten' => '',
		    'menu_ten_lo'=>'logged-in',

		    'menu_eleven' => 'void',
		    'menu_eleven_custom_slug' => '',
		    'menu_eleven_custom_url' => '',
		    'label_eleven' => 'Custom Slug',
		    'menu_icon_eleven' => 'void',
		    'custom_icon_eleven' => '',
		    'menu_eleven_lo'=>'logged-in',

		    'menu_twelve' => 'void',
		    'menu_twelve_custom_slug' => '',
		    'menu_twelve_custom_url' => '',
		    'label_twelve' => 'Custom Slug',
		    'menu_icon_twelve' => 'void',
		    'custom_icon_twelve' => '',
		    'menu_twelve_lo'=>'logged-in',

		    'layout'=>'Vertical'
		    ) );


		$title = esc_attr($instance['title']);
		$showtitle = esc_attr($instance['showtitle']);
		$showwidget = esc_attr($instance['showwidget']);
		$showavatar = (bool) $instance['showavatar'];
		$welcome = esc_attr($instance['welcome']);

		$menu_one = esc_attr($instance['menu_one']);
		$menu_one_custom_slug = esc_attr($instance['menu_one_custom_slug']);
		$menu_one_custom_url = esc_attr($instance['menu_one_custom_url']);
		$label_one = esc_attr($instance['label_one']);
		$menu_icon_one = esc_attr($instance['menu_icon_one']);
		$custom_icon_one = esc_url($instance['custom_icon_one']);
		$menu_one_lo = esc_attr($instance['menu_one_lo']);

		$menu_two = esc_attr($instance['menu_two']);
		$menu_two_custom_slug = esc_attr($instance['menu_two_custom_slug']);
		$menu_two_custom_url = esc_attr($instance['menu_two_custom_url']);
		$label_two = esc_attr($instance['label_two']);
		$menu_icon_two = esc_attr($instance['menu_icon_two']);
		$custom_icon_two = esc_url($instance['custom_icon_two']);
		$menu_two_lo = esc_attr($instance['menu_two_lo']);

		$menu_three = esc_attr($instance['menu_three']);
		$menu_three_custom_slug = esc_attr($instance['menu_three_custom_slug']);
		$menu_three_custom_url = esc_attr($instance['menu_three_custom_url']);
		$label_three = esc_attr($instance['label_three']);
		$menu_icon_three = esc_attr($instance['menu_icon_three']);
		$custom_icon_three = esc_url($instance['custom_icon_three']);
		$menu_three_lo = esc_attr($instance['menu_three_lo']);

		$menu_four = esc_attr($instance['menu_four']);
		$menu_four_custom_slug = esc_attr($instance['menu_four_custom_slug']);
		$menu_four_custom_url = esc_attr($instance['menu_four_custom_url']);
		$label_four = esc_attr($instance['label_four']);
		$menu_icon_four = esc_attr($instance['menu_icon_four']);
		$custom_icon_four = esc_url($instance['custom_icon_four']);
		$menu_four_lo = esc_attr($instance['menu_four_lo']);

		$menu_five = esc_attr($instance['menu_five']);
		$menu_five_custom_slug = esc_attr($instance['menu_five_custom_slug']);
		$menu_five_custom_url = esc_attr($instance['menu_five_custom_url']);
		$label_five = esc_attr($instance['label_five']);
		$menu_icon_five = esc_attr($instance['menu_icon_five']);
		$custom_icon_five = esc_url($instance['custom_icon_five']);
		$menu_five_lo = esc_attr($instance['menu_five_lo']);

		$menu_six = esc_attr($instance['menu_six']);
		$menu_six_custom_slug = esc_attr($instance['menu_six_custom_slug']);
		$menu_six_custom_url = esc_attr($instance['menu_six_custom_url']);
		$label_six = esc_attr($instance['label_six']);
		$menu_icon_six = esc_attr($instance['menu_icon_six']);
		$custom_icon_six = esc_url($instance['custom_icon_six']);
		$menu_six_lo = esc_attr($instance['menu_six_lo']);

		$menu_seven = esc_attr($instance['menu_seven']);
		$menu_seven_custom_slug = esc_attr($instance['menu_seven_custom_slug']);
		$menu_seven_custom_url = esc_attr($instance['menu_seven_custom_url']);
		$label_seven = esc_attr($instance['label_seven']);
		$menu_icon_seven = esc_attr($instance['menu_icon_seven']);
		$custom_icon_seven = esc_url($instance['custom_icon_seven']);
		$menu_seven_lo = esc_attr($instance['menu_seven_lo']);

		$menu_eight = esc_attr($instance['menu_eight']);
		$menu_eight_custom_slug = esc_attr($instance['menu_eight_custom_slug']);
		$menu_eight_custom_url = esc_attr($instance['menu_eight_custom_url']);
		$label_eight = esc_attr($instance['label_eight']);
		$menu_icon_eight = esc_attr($instance['menu_icon_eight']);
		$custom_icon_eight = esc_url($instance['custom_icon_eight']);
		$menu_eight_lo = esc_attr($instance['menu_eight_lo']);

		$menu_nine = esc_attr($instance['menu_nine']);
		$menu_nine_custom_slug = esc_attr($instance['menu_nine_custom_slug']);
		$menu_nine_custom_url = esc_attr($instance['menu_nine_custom_url']);
		$label_nine = esc_attr($instance['label_nine']);
		$menu_icon_nine = esc_attr($instance['menu_icon_nine']);
		$custom_icon_nine = esc_url($instance['custom_icon_nine']);
		$menu_nine_lo = esc_attr($instance['menu_nine_lo']);

		$menu_ten = esc_attr($instance['menu_ten']);
		$menu_ten_custom_slug = esc_attr($instance['menu_ten_custom_slug']);
		$menu_ten_custom_url = esc_attr($instance['menu_ten_custom_url']);
		$label_ten = esc_attr($instance['label_ten']);
		$menu_icon_ten = esc_attr($instance['menu_icon_ten']);
		$custom_icon_ten = esc_url($instance['custom_icon_ten']);
		$menu_ten_lo = esc_attr($instance['menu_ten_lo']);

		$menu_eleven = esc_attr($instance['menu_eleven']);
		$menu_eleven_custom_slug = esc_attr($instance['menu_eleven_custom_slug']);
		$menu_eleven_custom_url = esc_attr($instance['menu_eleven_custom_url']);
		$label_eleven = esc_attr($instance['label_eleven']);
		$menu_icon_eleven = esc_attr($instance['menu_icon_eleven']);
		$custom_icon_eleven = esc_url($instance['custom_icon_eleven']);
		$menu_eleven_lo = esc_attr($instance['menu_eleven_lo']);

		$menu_twelve = esc_attr($instance['menu_twelve']);
		$menu_twelve_custom_slug = esc_attr($instance['menu_twelve_custom_slug']);
		$menu_twelve_custom_url = esc_attr($instance['menu_twelve_custom_url']);
		$label_twelve = esc_attr($instance['label_twelve']);
		$menu_icon_twelve = esc_attr($instance['menu_icon_twelve']);
		$custom_icon_twelve = esc_url($instance['custom_icon_twelve']);
		$menu_twelve_lo = esc_attr($instance['menu_twelve_lo']);

		$layout = esc_attr($instance['layout']);

		?>
		<?php /* START: Selectable Item Options - Menu */ ?>
		<?php

		$menu_options='
			<option value="activity">Activity</option>
			<option value="avatar">Change Avatar</option>
			<option value="custom_slug">Custom Slug (Dynamic)</option>
			<option value="custom_url">Custom URL</option>
			<option value="profile/edit">Edit Profile</option>
			<option value="forums">Forums</option>
			<option value="friends">Friends</option>
			<option value="groups">Groups</option>
			<option value="home">Home</option>
			<option value="login">Login</option>
			<option value="logout">Logout</option>
			<option value="media">Media</option>
			<option value="members">Members</option>
			<option value="messages">Messages</option>
			<option value="profile">Profile</option>
			<option value="register">Register</option>
			<option value="settings">Settings</option>
			<option disabled role=separator>----EXTRAS----</option>
			<option value="custom_slug">Custom Slug (Dynamic)</option>
			<option value="custom_url">Custom URL</option>
			<option value="void">Do not show</option>
		    '
		?>

		<?php /* START: Selectable Item Options - Icons */ ?>
		<?php

		$icon_options='
			<option value="activity">Activity</option>
			<option value="avatar">Avatar</option>
			<option value="bp-avatar">BuddyPress Avatar</option>
			<option value="compose">Compose</option>
			<option value="custom">Custom Icon</option>
			<option value="edit-profile">Edit Profile</option>
			<option value="forums">Forums</option>
			<option value="friends">Friends</option>
			<option value="groups">Groups</option>
			<option value="home">Home</option>
			<option value="login">Login</option>
			<option value="logout">Logout</option>
			<option value="media">Media</option>
			<option value="members">Members</option>
			<option value="messages">Messages</option>
			<option value="profile">Profile</option>
			<option value="register">Register</option>
			<option value="settings">Settings</option>
			<option disabled role=separator>----EXTRAS----</option>
			<option value="custom">Custom Icon</option>
			<option value="void">Do not show</option>
		    '
		?>

		<?php /* START: Selectable User Role Options */ ?>
		<?php

		$role_options='
			<option value="logged-in">Logged In</option>
			<option value="logged-out">Logged Out</option>
			<option value="logged-in-out">Logged In/Out</option>
			<option value="no-one">No One</option>
		    '
		?>

		<?php /* Helpful URLs */

		$helpurl = esc_url('http://journalxtra.com/websiteadvice/wordpress/use-buddypress-dynamic-links-in-your-network-with-buddymenu-buddylinks-5316/');
		$rateurl = esc_url('http://wordpress.org/extend/plugins/buddymenu-buddylinks/');
		$donateurl = esc_url('https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=paypal@journalxtra.com&currency_code=USD&amount=&item_name=Donation%20to%20JournalXtra&return=http://journalxtra.com/thank-you/&notify_url=&cbt=Thank%20you%20for%20your%20donation,%20it%20is%20greatly%20appreciated&page_style=');

		?>

		<p>
			<a href="<?php echo $helpurl; ?>" target="_blank">Help</a> | <a href="<?php echo $rateurl; ?>" target="_blank">Rate</a> | <a href="<?php echo $donateurl; ?>" target="_blank">Donate</a>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('<strong>Widget Title:</strong>');?></label><br />
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('showtitle'); ?>"><?php _e('Show title to visitors who are: ', 'buddymenu-plugin-handle');?></label>
			<select class="select" id="<?php echo $this->get_field_id('showtitle'); ?>" name="<?php echo $this->get_field_name('showtitle'); ?>" selected="<?php echo $showtitle; ?>">
			  <option value="<?php echo $showtitle ?>" selected="<?php echo $showtitle; ?>"><?php echo $showtitle; ?></option>
			  <?php echo $role_options; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('showwidget'); ?>"><?php _e('Show widget to visitors who are: ', 'buddymenu-plugin-handle');?></label>
			<select class="select" id="<?php echo $this->get_field_id('showwidget'); ?>" name="<?php echo $this->get_field_name('showwidget'); ?>" selected="<?php echo $showwidget; ?>">
			  <option value="<?php echo $showwidget ?>" selected="<?php echo $showwidget; ?>"><?php echo $showwidget; ?></option>
			  <?php echo $role_options; ?>
			</select>
		</p>


		<p>
			<label for="<?php echo $this->get_field_id('showavatar'); ?>"><?php _e('Show avatar?', 'buddymenu-plugin-handle');?></label>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('showavatar'); ?>" name="<?php echo $this->get_field_name('showavatar'); ?>"<?php checked( $showavatar ); ?> />
		</p>

		<p>
		      <label for="<?php echo $this->get_field_id('welcome'); ?>"><?php _e('<span style="">Welcome Message:</span>');?></label>
		</p>
		<p>
		      <input class="widefat" id="<?php echo $this->get_field_id('welcome'); ?>" name="<?php echo $this->get_field_name('welcome'); ?>" type="text" value="<?php echo $welcome; ?>" />
		</p>

		<?php /* START: Layout Options */ ?>

		<p>
		    <label for="<?php echo $this->get_field_id('layout'); ?>"><?php _e('Horizontal or Vertical Layout?', 'buddymenu-plugin-handle');?></label>
		    <select class="select" id="<?php echo $this->get_field_id('layout'); ?>" name="<?php echo $this->get_field_name('layout'); ?>" selected="<?php echo $layout; ?>">
		      <option value="<?php echo $layout ?>" selected="<?php echo $layout; ?>"><?php echo $layout; ?></option>
		      <option value="Horizontal">Horizontal</option>
		      <option value="Vertical">Vertical</option>
		    </select>
		</p>

		<?php /* END: Layout Options */ ?>

		<p>
			<strong>When using custom slugs or custom URLs, save widget settings to display the custom slug/URL form.</strong>
		</p>
		<ul style="padding-left: 15px;list-style-type:disc;">
			<li><strong>Dynamic slugs (Custom slug (Dynamic)) link to BuddyPress pages. Enter the part of the URL that follows the username. Use lowercase letters.</strong></li>
			<li><strong>Custom URLs link to anywhere. Enter the full URL including http, ftp or whatever the protocol is.</strong></li>
		</ul>
		<p>
			<strong>When using custom icons, enter the URL of an icon in the custom icon field. The plugin's icons are all 24px wide. Leave the custom icon field blank to use the default icon.</strong>
		</p>

<?php /* START: Menu Item Selection Options */ ?>

<?php
// START: Menu Item One $menu_one
// $menu_one & $menu_icon_one & $label_one & $custom_icon_one

// START: Select Menu Item
?>
<table style="background-color:#dadada;border-top: 2px solid #ffffff;border-bottom: 2px solid #dde5ea;width:100%;">
	    <tr>
	      <td style="width:150px;">
		<label for="<?php echo $this->get_field_id('menu_one'); ?>"><?php _e('<strong>First Menu Item:</strong>', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		<select class="select" id="<?php echo $this->get_field_id('menu_one'); ?>" name="<?php echo $this->get_field_name('menu_one'); ?>" selected="<?php echo $menu_one; ?>">
		  <option value="<?php echo $menu_one ?>" selected="<?php echo $menu_one; ?>"><?php echo $menu_one; ?></option>

		  <?php echo $menu_options ?>

		</select>
	      </td>
	    </tr>
   <?php /* START: Custom Slug */ ?>
   <?php if ($menu_one=='custom_slug') {
	    ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_one_custom_slug'); ?>"><?php _e('<span style="color:red;">Custom Menu Slug:</span>');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('menu_one_custom_slug'); ?>" name="<?php echo $this->get_field_name('menu_one_custom_slug'); ?>" type="text" value="<?php echo $menu_one_custom_slug; ?>" />
	      </td>
	    </tr>
   <?php } ?>
   <?php /* START: Custom URL */ ?>
   <?php if ($menu_one=='custom_url') {
	    ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_one_custom_url'); ?>"><?php _e('<span style="color:red;">Custom Menu URL:</span>');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('menu_one_custom_url'); ?>" name="<?php echo $this->get_field_name('menu_one_custom_url'); ?>" type="text" value="<?php echo $menu_one_custom_url; ?>" />
	      </td>
	    </tr>
   <?php } ?>
   <?php /* START: Label */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('label_one'); ?>"><?php _e('Page Title:');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('label_one'); ?>" name="<?php echo $this->get_field_name('label_one'); ?>" type="text" value="<?php echo $label_one; ?>" />
	      </td>
	    </tr>
   <?php /* START: Select Icon */ ?>
	    <tr>
	      <td>
		<label for="<?php echo $this->get_field_id('menu_icon_one'); ?>"><?php _e('Icon:', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		<select class="select" id="<?php echo $this->get_field_id('menu_icon_one'); ?>" name="<?php echo $this->get_field_name('menu_icon_one'); ?>" selected="<?php echo $menu_icon_one; ?>">
		  <option value="<?php echo $menu_icon_one ?>" selected="<?php echo $menu_icon_one; ?>"><?php echo $menu_icon_one; ?></option>

		  <?php echo $icon_options; ?>

		</select>
	      </td>
	    </tr>
   <?php /* START: Custom Icon */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('custom_icon_one'); ?>"><?php _e('Custom Icon URL:');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('custom_icon_one'); ?>" name="<?php echo $this->get_field_name('custom_icon_one'); ?>" type="text" value="<?php echo $custom_icon_one; ?>" />
	      </td>
	    </tr>
   <?php /* START: User Visibility Question */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_one_lo'); ?>"><?php _e('Show to visitors: ', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		    <select class="select" id="<?php echo $this->get_field_id('menu_one_lo'); ?>" name="<?php echo $this->get_field_name('menu_one_lo'); ?>" selected="<?php echo $menu_one_lo; ?>">
			  <option value="<?php echo $menu_one_lo ?>" selected="<?php echo $menu_one_lo; ?>"><?php echo $menu_one_lo; ?></option>
			  <?php echo $role_options; ?>
		    </select>
	      </td>
	    </tr>
</table>
<?php /* END: Menu Item One */ ?>

<?php
// START: Menu Item Two $menu_two
// $menu_two & $menu_icon_two & $label_two & $custom_icon_two

// START: Select Menu Item
?>
<table style="background-color:#dadada;border-top: 2px solid #ffffff;border-bottom: 2px solid #dde5ea;width:100%;">
	    <tr>
	      <td style="width:150px;">
		<label for="<?php echo $this->get_field_id('menu_two'); ?>"><?php _e('<strong>Second Menu Item:</strong>', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		<select class="select" id="<?php echo $this->get_field_id('menu_two'); ?>" name="<?php echo $this->get_field_name('menu_two'); ?>" selected="<?php echo $menu_two; ?>">
		  <option value="<?php echo $menu_two ?>" selected="<?php echo $menu_two; ?>"><?php echo $menu_two; ?></option>

		  <?php echo $menu_options; ?>

		</select>
	      </td>
	    </tr>
   <?php /* START: Custom Slug */ ?>
   <?php if ($menu_two=='custom_slug') {
	    ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_two_custom_slug'); ?>"><?php _e('<span style="color:red;">Custom Menu Slug:</span>');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('menu_two_custom_slug'); ?>" name="<?php echo $this->get_field_name('menu_two_custom_slug'); ?>" type="text" value="<?php echo $menu_two_custom_slug; ?>" />
	      </td>
	    </tr>
   <?php } ?>
   <?php /* START: Custom URL */ ?>
   <?php if ($menu_two=='custom_url') {
	    ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_two_custom_url'); ?>"><?php _e('<span style="color:red;">Custom Menu URL:</span>');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('menu_two_custom_url'); ?>" name="<?php echo $this->get_field_name('menu_two_custom_url'); ?>" type="text" value="<?php echo $menu_two_custom_url; ?>" />
	      </td>
	    </tr>
   <?php } ?>
   <?php /* START: Label */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('label_two'); ?>"><?php _e('Page Title:');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('label_two'); ?>" name="<?php echo $this->get_field_name('label_two'); ?>" type="text" value="<?php echo $label_two; ?>" />
	      </td>
	    </tr>
   <?php /* START: Select Icon */ ?>
	    <tr>
	      <td>
		<label for="<?php echo $this->get_field_id('menu_icon_two'); ?>"><?php _e('Icon:', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		<select class="select" id="<?php echo $this->get_field_id('menu_icon_two'); ?>" name="<?php echo $this->get_field_name('menu_icon_two'); ?>" selected="<?php echo $menu_icon_two; ?>">
		  <option value="<?php echo $menu_icon_two ?>" selected="<?php echo $menu_icon_two; ?>"><?php echo $menu_icon_two; ?></option>

		  <?php echo $icon_options; ?>

		</select>
	      </td>
	    </tr>
   <?php /* START: Custom Icon */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('custom_icon_two'); ?>"><?php _e('Custom Icon URL:');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('custom_icon_two'); ?>" name="<?php echo $this->get_field_name('custom_icon_two'); ?>" type="text" value="<?php echo $custom_icon_two; ?>" />
	      </td>
	    </tr>
   <?php /* START: User Visibility Question */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_two_lo'); ?>"><?php _e('Show to visitors: ', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		    <select class="select" id="<?php echo $this->get_field_id('menu_two_lo'); ?>" name="<?php echo $this->get_field_name('menu_two_lo'); ?>" selected="<?php echo $menu_two_lo; ?>">
			  <option value="<?php echo $menu_two_lo ?>" selected="<?php echo $menu_two_lo; ?>"><?php echo $menu_two_lo; ?></option>
			  <?php echo $role_options; ?>
		    </select>
	      </td>
	    </tr>
</table>
<?php /* END: Menu Item Two */ ?>

<?php
// START: Menu Item Three $menu_three
// $menu_three & $menu_icon_three & $label_three & $custom_icon_three

// START: Select Menu Item
?>
<table style="background-color:#dadada;border-top: 2px solid #ffffff;border-bottom: 2px solid #dde5ea;width:100%;">
	    <tr>
	      <td style="width:150px;">
		<label for="<?php echo $this->get_field_id('menu_three'); ?>"><?php _e('<strong>Third Menu Item:</strong>', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		<select class="select" id="<?php echo $this->get_field_id('menu_three'); ?>" name="<?php echo $this->get_field_name('menu_three'); ?>" selected="<?php echo $menu_three; ?>">
		  <option value="<?php echo $menu_three ?>" selected="<?php echo $menu_three; ?>"><?php echo $menu_three; ?></option>

		  <?php echo $menu_options; ?>

		</select>
	      </td>
	    </tr>
   <?php /* START: Custom Slug */ ?>
   <?php if ($menu_three=='custom_slug') {
	    ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_three_custom_slug'); ?>"><?php _e('<span style="color:red;">Custom Menu Slug:</span>');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('menu_three_custom_slug'); ?>" name="<?php echo $this->get_field_name('menu_three_custom_slug'); ?>" type="text" value="<?php echo $menu_three_custom_slug; ?>" />
	      </td>
	    </tr>
   <?php } ?>
   <?php /* START: Custom URL */ ?>
   <?php if ($menu_three=='custom_url') {
	    ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_three_custom_url'); ?>"><?php _e('<span style="color:red;">Custom Menu URL:</span>');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('menu_three_custom_url'); ?>" name="<?php echo $this->get_field_name('menu_three_custom_url'); ?>" type="text" value="<?php echo $menu_three_custom_url; ?>" />
	      </td>
	    </tr>
   <?php } ?>
   <?php /* START: Label */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('label_three'); ?>"><?php _e('Page Title:');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('label_three'); ?>" name="<?php echo $this->get_field_name('label_three'); ?>" type="text" value="<?php echo $label_three; ?>" />
	      </td>
	    </tr>
   <?php /* START: Select Icon */ ?>
	    <tr>
	      <td>
		<label for="<?php echo $this->get_field_id('menu_icon_three'); ?>"><?php _e('Icon:', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		<select class="select" id="<?php echo $this->get_field_id('menu_icon_three'); ?>" name="<?php echo $this->get_field_name('menu_icon_three'); ?>" selected="<?php echo $menu_icon_three; ?>">
		  <option value="<?php echo $menu_icon_three ?>" selected="<?php echo $menu_icon_three; ?>"><?php echo $menu_icon_three; ?></option>

		  <?php echo $icon_options; ?>

		</select>
	      </td>
	    </tr>
   <?php /* START: Custom Icon */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('custom_icon_three'); ?>"><?php _e('Custom Icon URL:');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('custom_icon_three'); ?>" name="<?php echo $this->get_field_name('custom_icon_three'); ?>" type="text" value="<?php echo $custom_icon_three; ?>" />
	      </td>
	    </tr>
   <?php /* START: User Visibility Question */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_three_lo'); ?>"><?php _e('Show to visitors: ', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		    <select class="select" id="<?php echo $this->get_field_id('menu_three_lo'); ?>" name="<?php echo $this->get_field_name('menu_three_lo'); ?>" selected="<?php echo $menu_three_lo; ?>">
			  <option value="<?php echo $menu_three_lo ?>" selected="<?php echo $menu_three_lo; ?>"><?php echo $menu_three_lo; ?></option>
			  <?php echo $role_options; ?>
		    </select>
	      </td>
	    </tr>
</table>
<?php /* END: Menu Item Three */ ?>


<?php
// START: Menu Item Four $menu_four
// $menu_four & $menu_icon_four & $label_four & $custom_icon_four

// START: Select Menu Item
?>
<table style="background-color:#dadada;border-top: 2px solid #ffffff;border-bottom: 2px solid #dde5ea;width:100%;">
	    <tr>
	      <td style="width:150px;">
		<label for="<?php echo $this->get_field_id('menu_four'); ?>"><?php _e('<strong>Fourth Menu Item:</strong>', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		<select class="select" id="<?php echo $this->get_field_id('menu_four'); ?>" name="<?php echo $this->get_field_name('menu_four'); ?>" selected="<?php echo $menu_four; ?>">
		  <option value="<?php echo $menu_four ?>" selected="<?php echo $menu_four; ?>"><?php echo $menu_four; ?></option>

		  <?php echo $menu_options; ?>

		</select>
	      </td>
	    </tr>
   <?php /* START: Custom Slug */ ?>
   <?php if ($menu_four=='custom_slug') {
	    ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_four_custom_slug'); ?>"><?php _e('<span style="color:red;">Custom Menu Slug:</span>');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('menu_four_custom_slug'); ?>" name="<?php echo $this->get_field_name('menu_four_custom_slug'); ?>" type="text" value="<?php echo $menu_four_custom_slug; ?>" />
	      </td>
	    </tr>
   <?php } ?>
   <?php /* START: Custom URL */ ?>
   <?php if ($menu_four=='custom_url') {
	    ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_four_custom_url'); ?>"><?php _e('<span style="color:red;">Custom Menu URL:</span>');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('menu_four_custom_url'); ?>" name="<?php echo $this->get_field_name('menu_four_custom_url'); ?>" type="text" value="<?php echo $menu_four_custom_url; ?>" />
	      </td>
	    </tr>
   <?php } ?>
   <?php /* START: Label */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('label_four'); ?>"><?php _e('Page Title:');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('label_four'); ?>" name="<?php echo $this->get_field_name('label_four'); ?>" type="text" value="<?php echo $label_four; ?>" />
	      </td>
	    </tr>
   <?php /* START: Select Icon */ ?>
	    <tr>
	      <td>
		<label for="<?php echo $this->get_field_id('menu_icon_four'); ?>"><?php _e('Icon:', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		<select class="select" id="<?php echo $this->get_field_id('menu_icon_four'); ?>" name="<?php echo $this->get_field_name('menu_icon_four'); ?>" selected="<?php echo $menu_icon_four; ?>">
		  <option value="<?php echo $menu_icon_four ?>" selected="<?php echo $menu_icon_four; ?>"><?php echo $menu_icon_four; ?></option>

		  <?php echo $icon_options; ?>

		</select>
	      </td>
	    </tr>
   <?php /* START: Custom Icon */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('custom_icon_four'); ?>"><?php _e('Custom Icon URL:');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('custom_icon_four'); ?>" name="<?php echo $this->get_field_name('custom_icon_four'); ?>" type="text" value="<?php echo $custom_icon_four; ?>" />
	      </td>
	    </tr>
   <?php /* START: User Visibility Question */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_four_lo'); ?>"><?php _e('Show to visitors: ', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		    <select class="select" id="<?php echo $this->get_field_id('menu_four_lo'); ?>" name="<?php echo $this->get_field_name('menu_four_lo'); ?>" selected="<?php echo $menu_four_lo; ?>">
			  <option value="<?php echo $menu_four_lo ?>" selected="<?php echo $menu_four_lo; ?>"><?php echo $menu_four_lo; ?></option>
			  <?php echo $role_options; ?>
		    </select>
	      </td>
	    </tr>
</table>
<?php /* END: Menu Item Four */ ?>

<?php
// START: Menu Item Five $menu_five
// $menu_five & $menu_icon_five & $label_five & $custom_icon_five

// START: Select Menu Item
?>
<table style="background-color:#dadada;border-top: 2px solid #ffffff;border-bottom: 2px solid #dde5ea;width:100%;">
	    <tr>
	      <td style="width:150px;">
		<label for="<?php echo $this->get_field_id('menu_five'); ?>"><?php _e('<strong>Fifth Menu Item:</strong>', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		<select class="select" id="<?php echo $this->get_field_id('menu_five'); ?>" name="<?php echo $this->get_field_name('menu_five'); ?>" selected="<?php echo $menu_five; ?>">
		  <option value="<?php echo $menu_five ?>" selected="<?php echo $menu_five; ?>"><?php echo $menu_five; ?></option>

		  <?php echo $menu_options; ?>

		</select>
	      </td>
	    </tr>
   <?php /* START: Custom Slug */ ?>
   <?php if ($menu_five=='custom_slug') {
	    ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_five_custom_slug'); ?>"><?php _e('<span style="color:red;">Custom Menu Slug:</span>');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('menu_five_custom_slug'); ?>" name="<?php echo $this->get_field_name('menu_five_custom_slug'); ?>" type="text" value="<?php echo $menu_five_custom_slug; ?>" />
	      </td>
	    </tr>
   <?php } ?>
   <?php /* START: Custom URL */ ?>
   <?php if ($menu_five=='custom_url') {
	    ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_five_custom_url'); ?>"><?php _e('<span style="color:red;">Custom Menu URL:</span>');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('menu_five_custom_url'); ?>" name="<?php echo $this->get_field_name('menu_five_custom_url'); ?>" type="text" value="<?php echo $menu_five_custom_url; ?>" />
	      </td>
	    </tr>
   <?php } ?>
   <?php /* START: Label */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('label_five'); ?>"><?php _e('Page Title:');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('label_five'); ?>" name="<?php echo $this->get_field_name('label_five'); ?>" type="text" value="<?php echo $label_five; ?>" />
	      </td>
	    </tr>
    <?php /* START: Select Icon */ ?>
	    <tr>
	      <td>
		<label for="<?php echo $this->get_field_id('menu_icon_five'); ?>"><?php _e('Icon:', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		<select class="select" id="<?php echo $this->get_field_id('menu_icon_five'); ?>" name="<?php echo $this->get_field_name('menu_icon_five'); ?>" selected="<?php echo $menu_icon_five; ?>">
		  <option value="<?php echo $menu_icon_five ?>" selected="<?php echo $menu_icon_five; ?>"><?php echo $menu_icon_five; ?></option>

		  <?php echo $icon_options; ?>

		</select>
	      </td>
	    </tr>
   <?php /* START: Custom Icon */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('custom_icon_five'); ?>"><?php _e('Custom Icon URL:');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('custom_icon_five'); ?>" name="<?php echo $this->get_field_name('custom_icon_five'); ?>" type="text" value="<?php echo $custom_icon_five; ?>" />
	      </td>
	    </tr>
   <?php /* START: User Visibility Question */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_five_lo'); ?>"><?php _e('Show to visitors: ', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		    <select class="select" id="<?php echo $this->get_field_id('menu_five_lo'); ?>" name="<?php echo $this->get_field_name('menu_five_lo'); ?>" selected="<?php echo $menu_five_lo; ?>">
			  <option value="<?php echo $menu_five_lo ?>" selected="<?php echo $menu_five_lo; ?>"><?php echo $menu_five_lo; ?></option>
			  <?php echo $role_options; ?>
		    </select>
	      </td>
	    </tr>
</table>
<?php /* END: Menu Item Five */ ?>

<?php
// START: Menu Item Six $menu_six
// $menu_six & $menu_icon_six & $label_six & $custom_icon_six

// START: Select Menu Item
?>
<table style="background-color:#dadada;border-top: 2px solid #ffffff;border-bottom: 2px solid #dde5ea;width:100%;">
	    <tr>
	      <td style="width:150px;">
		<label for="<?php echo $this->get_field_id('menu_six'); ?>"><?php _e('<strong>Sixth Menu Item:</strong>', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		<select class="select" id="<?php echo $this->get_field_id('menu_six'); ?>" name="<?php echo $this->get_field_name('menu_six'); ?>" selected="<?php echo $menu_six; ?>">
		  <option value="<?php echo $menu_six ?>" selected="<?php echo $menu_six; ?>"><?php echo $menu_six; ?></option>

		  <?php echo $menu_options; ?>

		</select>
	      </td>
	    </tr>
   <?php /* START: Custom Slug */ ?>
   <?php if ($menu_six=='custom_slug') {
	    ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_six_custom_slug'); ?>"><?php _e('<span style="color:red;">Custom Menu Slug:</span>');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('menu_six_custom_slug'); ?>" name="<?php echo $this->get_field_name('menu_six_custom_slug'); ?>" type="text" value="<?php echo $menu_six_custom_slug; ?>" />
	      </td>
	    </tr>
   <?php } ?>
   <?php /* START: Custom URL */ ?>
   <?php if ($menu_six=='custom_url') {
	    ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_six_custom_url'); ?>"><?php _e('<span style="color:red;">Custom Menu URL:</span>');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('menu_six_custom_url'); ?>" name="<?php echo $this->get_field_name('menu_six_custom_url'); ?>" type="text" value="<?php echo $menu_six_custom_url; ?>" />
	      </td>
	    </tr>
   <?php } ?>
   <?php /* START: Label */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('label_six'); ?>"><?php _e('Page Title:');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('label_six'); ?>" name="<?php echo $this->get_field_name('label_six'); ?>" type="text" value="<?php echo $label_six; ?>" />
	      </td>
	    </tr>
   <?php /* START: Select Icon */ ?>
	    <tr>
	      <td>
		<label for="<?php echo $this->get_field_id('menu_icon_six'); ?>"><?php _e('Icon:', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		<select class="select" id="<?php echo $this->get_field_id('menu_icon_six'); ?>" name="<?php echo $this->get_field_name('menu_icon_six'); ?>" selected="<?php echo $menu_icon_six; ?>">
		  <option value="<?php echo $menu_icon_six ?>" selected="<?php echo $menu_icon_six; ?>"><?php echo $menu_icon_six; ?></option>

		  <?php echo $icon_options; ?>

		</select>
	      </td>
	    </tr>
   <?php /* START: Custom Icon */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('custom_icon_six'); ?>"><?php _e('Custom Icon URL:');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('custom_icon_six'); ?>" name="<?php echo $this->get_field_name('custom_icon_six'); ?>" type="text" value="<?php echo $custom_icon_six; ?>" />
	      </td>
	    </tr>
   <?php /* START: User Visibility Question */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_six_lo'); ?>"><?php _e('Show to visitors: ', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		    <select class="select" id="<?php echo $this->get_field_id('menu_six_lo'); ?>" name="<?php echo $this->get_field_name('menu_six_lo'); ?>" selected="<?php echo $menu_six_lo; ?>">
			  <option value="<?php echo $menu_six_lo ?>" selected="<?php echo $menu_six_lo; ?>"><?php echo $menu_six_lo; ?></option>
			  <?php echo $role_options; ?>
		    </select>
	      </td>
	    </tr>
</table>
<?php /* END: Menu Item Six */ ?>

<?php
// START: Menu Item Seven $menu_seven
// $menu_seven & $menu_icon_seven & $label_seven & $custom_icon_seven

// START: Select Menu Item
?>
<table style="background-color:#dadada;border-top: 2px solid #ffffff;border-bottom: 2px solid #dde5ea;width:100%;">
	    <tr>
	      <td style="width:150px;">
		<label for="<?php echo $this->get_field_id('menu_seven'); ?>"><?php _e('<strong>Seventh Menu Item:</strong>', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		<select class="select" id="<?php echo $this->get_field_id('menu_seven'); ?>" name="<?php echo $this->get_field_name('menu_seven'); ?>" selected="<?php echo $menu_seven; ?>">
		  <option value="<?php echo $menu_seven ?>" selected="<?php echo $menu_seven; ?>"><?php echo $menu_seven; ?></option>

		  <?php echo $menu_options; ?>

		</select>
	      </td>
	    </tr>
   <?php /* START: Custom Slug */ ?>
   <?php if ($menu_seven=='custom_slug') {
	    ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_seven_custom_slug'); ?>"><?php _e('<span style="color:red;">Custom Menu Slug:</span>');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('menu_seven_custom_slug'); ?>" name="<?php echo $this->get_field_name('menu_seven_custom_slug'); ?>" type="text" value="<?php echo $menu_seven_custom_slug; ?>" />
	      </td>
	    </tr>
   <?php } ?>
   <?php /* START: Custom URL */ ?>
   <?php if ($menu_seven=='custom_url') {
	    ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_seven_custom_url'); ?>"><?php _e('<span style="color:red;">Custom Menu URL:</span>');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('menu_seven_custom_url'); ?>" name="<?php echo $this->get_field_name('menu_seven_custom_url'); ?>" type="text" value="<?php echo $menu_seven_custom_url; ?>" />
	      </td>
	    </tr>
   <?php } ?>
   <?php /* START: Label */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('label_seven'); ?>"><?php _e('Page Title:');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('label_seven'); ?>" name="<?php echo $this->get_field_name('label_seven'); ?>" type="text" value="<?php echo $label_seven; ?>" />
	      </td>
	    </tr>
   <?php /* START: Select Icon */ ?>
	    <tr>
	      <td>
		<label for="<?php echo $this->get_field_id('menu_icon_seven'); ?>"><?php _e('Icon:', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		<select class="select" id="<?php echo $this->get_field_id('menu_icon_seven'); ?>" name="<?php echo $this->get_field_name('menu_icon_seven'); ?>" selected="<?php echo $menu_icon_seven; ?>">
		  <option value="<?php echo $menu_icon_seven ?>" selected="<?php echo $menu_icon_seven; ?>"><?php echo $menu_icon_seven; ?></option>

		  <?php echo $icon_options; ?>

		</select>
	      </td>
	    </tr>
   <?php /* START: Custom Icon */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('custom_icon_seven'); ?>"><?php _e('Custom Icon URL:');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('custom_icon_seven'); ?>" name="<?php echo $this->get_field_name('custom_icon_seven'); ?>" type="text" value="<?php echo $custom_icon_seven; ?>" />
	      </td>
	    </tr>
   <?php /* START: User Visibility Question */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_seven_lo'); ?>"><?php _e('Show to visitors: ', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		    <select class="select" id="<?php echo $this->get_field_id('menu_seven_lo'); ?>" name="<?php echo $this->get_field_name('menu_seven_lo'); ?>" selected="<?php echo $menu_seven_lo; ?>">
			  <option value="<?php echo $menu_seven_lo ?>" selected="<?php echo $menu_seven_lo; ?>"><?php echo $menu_seven_lo; ?></option>
			  <?php echo $role_options; ?>
		    </select>
	      </td>
	    </tr>
</table>
<?php /* END: Menu Item Seven */ ?>

<?php
// START: Menu Item Eight $menu_eight
// $menu_eight & $menu_icon_eight & $label_eight & $custom_icon_eight

// START: Select Menu Item
?>
<table style="background-color:#dadada;border-top: 2px solid #ffffff;border-bottom: 2px solid #dde5ea;width:100%;">
	    <tr>
	      <td style="width:150px;">
		<label for="<?php echo $this->get_field_id('menu_eight'); ?>"><?php _e('<strong>Eighth Menu Item:</strong>', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		<select class="select" id="<?php echo $this->get_field_id('menu_eight'); ?>" name="<?php echo $this->get_field_name('menu_eight'); ?>" selected="<?php echo $menu_eight; ?>">
		  <option value="<?php echo $menu_eight ?>" selected="<?php echo $menu_eight; ?>"><?php echo $menu_eight; ?></option>

		  <?php echo $menu_options; ?>

		</select>
	      </td>
	    </tr>
   <?php /* START: Custom Slug */ ?>
   <?php if ($menu_eight=='custom_slug') {
	    ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_eight_custom_slug'); ?>"><?php _e('<span style="color:red;">Custom Menu Slug:</span>');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('menu_eight_custom_slug'); ?>" name="<?php echo $this->get_field_name('menu_eight_custom_slug'); ?>" type="text" value="<?php echo $menu_eight_custom_slug; ?>" />
	      </td>
	    </tr>
   <?php } ?>
   <?php /* START: Custom URL */ ?>
   <?php if ($menu_eight=='custom_url') {
	    ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_eight_custom_url'); ?>"><?php _e('<span style="color:red;">Custom Menu URL:</span>');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('menu_eight_custom_url'); ?>" name="<?php echo $this->get_field_name('menu_eight_custom_url'); ?>" type="text" value="<?php echo $menu_eight_custom_url; ?>" />
	      </td>
	    </tr>
   <?php } ?>
   <?php /* START: Label */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('label_eight'); ?>"><?php _e('Page Title:');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('label_eight'); ?>" name="<?php echo $this->get_field_name('label_eight'); ?>" type="text" value="<?php echo $label_eight; ?>" />
	      </td>
	    </tr>
   <?php /* START: Select Icon */ ?>
	    <tr>
	      <td>
		<label for="<?php echo $this->get_field_id('menu_icon_eight'); ?>"><?php _e('Icon:', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		<select class="select" id="<?php echo $this->get_field_id('menu_icon_eight'); ?>" name="<?php echo $this->get_field_name('menu_icon_eight'); ?>" selected="<?php echo $menu_icon_eight; ?>">
		  <option value="<?php echo $menu_icon_eight ?>" selected="<?php echo $menu_icon_eight; ?>"><?php echo $menu_icon_eight; ?></option>

		  <?php echo $icon_options; ?>

		</select>
	      </td>
	    </tr>
   <?php /* START: Custom Icon */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('custom_icon_eight'); ?>"><?php _e('Custom Icon URL:');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('custom_icon_eight'); ?>" name="<?php echo $this->get_field_name('custom_icon_eight'); ?>" type="text" value="<?php echo $custom_icon_eight; ?>" />
	      </td>
	    </tr>
   <?php /* START: User Visibility Question */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_eight_lo'); ?>"><?php _e('Show to visitors: ', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		    <select class="select" id="<?php echo $this->get_field_id('menu_eight_lo'); ?>" name="<?php echo $this->get_field_name('menu_eight_lo'); ?>" selected="<?php echo $menu_eight_lo; ?>">
			  <option value="<?php echo $menu_eight_lo ?>" selected="<?php echo $menu_eight_lo; ?>"><?php echo $menu_eight_lo; ?></option>
			  <?php echo $role_options; ?>
		    </select>
	      </td>
	    </tr>
</table>
<?php /* END: Menu Item Eight */ ?>

<?php
// START: Menu Item Nine $menu_nine
// $menu_nine & $menu_icon_nine & $label_nine & $custom_icon_nine

// START: Select Menu Item
?>
<table style="background-color:#dadada;border-top: 2px solid #ffffff;border-bottom: 2px solid #dde5ea;width:100%;">
	    <tr>
	      <td style="width:150px;">
		<label for="<?php echo $this->get_field_id('menu_nine'); ?>"><?php _e('<strong>Ninth Menu Item:</strong>', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		<select class="select" id="<?php echo $this->get_field_id('menu_nine'); ?>" name="<?php echo $this->get_field_name('menu_nine'); ?>" selected="<?php echo $menu_nine; ?>">
		  <option value="<?php echo $menu_nine ?>" selected="<?php echo $menu_nine; ?>"><?php echo $menu_nine; ?></option>

		  <?php echo $menu_options; ?>

		</select>
	      </td>
	    </tr>
   <?php /* START: Custom Slug */ ?>
   <?php if ($menu_nine=='custom_slug') {
	    ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_nine_custom_slug'); ?>"><?php _e('<span style="color:red;">Custom Menu Slug:</span>');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('menu_nine_custom_slug'); ?>" name="<?php echo $this->get_field_name('menu_nine_custom_slug'); ?>" type="text" value="<?php echo $menu_nine_custom_slug; ?>" />
	      </td>
	    </tr>
   <?php } ?>
   <?php /* START: Custom URL */ ?>
   <?php if ($menu_nine=='custom_url') {
	    ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_nine_custom_url'); ?>"><?php _e('<span style="color:red;">Custom Menu URL:</span>');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('menu_nine_custom_url'); ?>" name="<?php echo $this->get_field_name('menu_nine_custom_url'); ?>" type="text" value="<?php echo $menu_nine_custom_url; ?>" />
	      </td>
	    </tr>
   <?php } ?>
   <?php /* START: Label */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('label_nine'); ?>"><?php _e('Page Title:');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('label_nine'); ?>" name="<?php echo $this->get_field_name('label_nine'); ?>" type="text" value="<?php echo $label_nine; ?>" />
	      </td>
	    </tr>
   <?php /* START: Select Icon */ ?>
	    <tr>
	      <td>
		<label for="<?php echo $this->get_field_id('menu_icon_nine'); ?>"><?php _e('Icon:', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		<select class="select" id="<?php echo $this->get_field_id('menu_icon_nine'); ?>" name="<?php echo $this->get_field_name('menu_icon_nine'); ?>" selected="<?php echo $menu_icon_nine; ?>">
		  <option value="<?php echo $menu_icon_nine ?>" selected="<?php echo $menu_icon_nine; ?>"><?php echo $menu_icon_nine; ?></option>

		  <?php echo $icon_options; ?>

		</select>
	      </td>
	    </tr>
   <?php /* START: Custom Icon */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('custom_icon_nine'); ?>"><?php _e('Custom Icon URL:');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('custom_icon_nine'); ?>" name="<?php echo $this->get_field_name('custom_icon_nine'); ?>" type="text" value="<?php echo $custom_icon_nine; ?>" />
	      </td>
	    </tr>
   <?php /* START: User Visibility Question */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_nine_lo'); ?>"><?php _e('Show to visitors: ', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		    <select class="select" id="<?php echo $this->get_field_id('menu_nine_lo'); ?>" name="<?php echo $this->get_field_name('menu_nine_lo'); ?>" selected="<?php echo $menu_nine_lo; ?>">
			  <option value="<?php echo $menu_nine_lo ?>" selected="<?php echo $menu_nine_lo; ?>"><?php echo $menu_nine_lo; ?></option>
			  <?php echo $role_options; ?>
		    </select>
	      </td>
	    </tr>
</table>
<?php /* END: Menu Item Nine */ ?>

<?php
// START: Menu Item Ten $menu_ten
// $menu_ten & $menu_icon_ten & $label_ten & $custom_icon_ten

// START: Select Menu Item
?>
<table style="background-color:#dadada;border-top: 2px solid #ffffff;border-bottom: 2px solid #dde5ea;width:100%;">
	    <tr>
	      <td style="width:150px;">
		<label for="<?php echo $this->get_field_id('menu_ten'); ?>"><?php _e('<strong>Tenth Menu Item:</strong>', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		<select class="select" id="<?php echo $this->get_field_id('menu_ten'); ?>" name="<?php echo $this->get_field_name('menu_ten'); ?>" selected="<?php echo $menu_ten; ?>">
		  <option value="<?php echo $menu_ten ?>" selected="<?php echo $menu_ten; ?>"><?php echo $menu_ten; ?></option>

		  <?php echo $menu_options; ?>

		</select>
	      </td>
	    </tr>
   <?php /* START: Custom Slug */ ?>
   <?php if ($menu_ten=='custom_slug') {
	    ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_ten_custom_slug'); ?>"><?php _e('<span style="color:red;">Custom Menu Slug:</span>');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('menu_ten_custom_slug'); ?>" name="<?php echo $this->get_field_name('menu_ten_custom_slug'); ?>" type="text" value="<?php echo $menu_ten_custom_slug; ?>" />
	      </td>
	    </tr>
   <?php } ?>
   <?php /* START: Custom URL */ ?>
   <?php if ($menu_ten=='custom_url') {
	    ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_ten_custom_url'); ?>"><?php _e('<span style="color:red;">Custom Menu URL:</span>');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('menu_ten_custom_url'); ?>" name="<?php echo $this->get_field_name('menu_ten_custom_url'); ?>" type="text" value="<?php echo $menu_ten_custom_url; ?>" />
	      </td>
	    </tr>
   <?php } ?>
   <?php /* START: Label */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('label_ten'); ?>"><?php _e('Page Title:');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('label_ten'); ?>" name="<?php echo $this->get_field_name('label_ten'); ?>" type="text" value="<?php echo $label_ten; ?>" />
	      </td>
	    </tr>
   <?php /* START: Select Icon */ ?>
	    <tr>
	      <td>
		<label for="<?php echo $this->get_field_id('menu_icon_ten'); ?>"><?php _e('Icon:', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		<select class="select" id="<?php echo $this->get_field_id('menu_icon_ten'); ?>" name="<?php echo $this->get_field_name('menu_icon_ten'); ?>" selected="<?php echo $menu_icon_ten; ?>">
		  <option value="<?php echo $menu_icon_ten ?>" selected="<?php echo $menu_icon_ten; ?>"><?php echo $menu_icon_ten; ?></option>

		  <?php echo $icon_options; ?>

		</select>
	      </td>
	    </tr>
   <?php /* START: Custom Icon */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('custom_icon_ten'); ?>"><?php _e('Custom Icon URL:');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('custom_icon_ten'); ?>" name="<?php echo $this->get_field_name('custom_icon_ten'); ?>" type="text" value="<?php echo $custom_icon_ten; ?>" />
	      </td>
	    </tr>
   <?php /* START: User Visibility Question */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_ten_lo'); ?>"><?php _e('Show to visitors: ', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		    <select class="select" id="<?php echo $this->get_field_id('menu_ten_lo'); ?>" name="<?php echo $this->get_field_name('menu_ten_lo'); ?>" selected="<?php echo $menu_ten_lo; ?>">
			  <option value="<?php echo $menu_ten_lo ?>" selected="<?php echo $menu_ten_lo; ?>"><?php echo $menu_ten_lo; ?></option>
			  <?php echo $role_options; ?>
		    </select>
	      </td>
	    </tr>
</table>
<?php /* END: Menu Item Ten */ ?>

<?php
// START: Menu Item Eleven $menu_eleven
// $menu_eleven & $menu_icon_eleven & $label_eleven & $custom_icon_eleven

// START: Select Menu Item
?>
<table style="background-color:#dadada;border-top: 2px solid #ffffff;border-bottom: 2px solid #dde5ea;width:100%;">
	    <tr>
	      <td style="width:150px;">
		<label for="<?php echo $this->get_field_id('menu_eleven'); ?>"><?php _e('<strong>Eleventh Menu Item:</strong>', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		<select class="select" id="<?php echo $this->get_field_id('menu_eleven'); ?>" name="<?php echo $this->get_field_name('menu_eleven'); ?>" selected="<?php echo $menu_eleven; ?>">
		  <option value="<?php echo $menu_eleven ?>" selected="<?php echo $menu_eleven; ?>"><?php echo $menu_eleven; ?></option>

		  <?php echo $menu_options; ?>

		</select>
	      </td>
	    </tr>
   <?php /* START: Custom Slug */ ?>
   <?php if ($menu_eleven=='custom_slug') {
	    ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_eleven_custom_slug'); ?>"><?php _e('<span style="color:red;">Custom Menu Slug:</span>');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('menu_eleven_custom_slug'); ?>" name="<?php echo $this->get_field_name('menu_eleven_custom_slug'); ?>" type="text" value="<?php echo $menu_eleven_custom_slug; ?>" />
	      </td>
	    </tr>
   <?php } ?>
   <?php /* START: Custom URL */ ?>
   <?php if ($menu_eleven=='custom_url') {
	    ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_eleven_custom_url'); ?>"><?php _e('<span style="color:red;">Custom Menu URL:</span>');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('menu_eleven_custom_url'); ?>" name="<?php echo $this->get_field_name('menu_eleven_custom_url'); ?>" type="text" value="<?php echo $menu_eleven_custom_url; ?>" />
	      </td>
	    </tr>
   <?php } ?>
   <?php /* START: Label */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('label_eleven'); ?>"><?php _e('Page Title:');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('label_eleven'); ?>" name="<?php echo $this->get_field_name('label_eleven'); ?>" type="text" value="<?php echo $label_eleven; ?>" />
	      </td>
	    </tr>
   <?php /* START: Select Icon */ ?>
	    <tr>
	      <td>
		<label for="<?php echo $this->get_field_id('menu_icon_eleven'); ?>"><?php _e('Icon:', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		<select class="select" id="<?php echo $this->get_field_id('menu_icon_eleven'); ?>" name="<?php echo $this->get_field_name('menu_icon_eleven'); ?>" selected="<?php echo $menu_icon_eleven; ?>">
		  <option value="<?php echo $menu_icon_eleven ?>" selected="<?php echo $menu_icon_eleven; ?>"><?php echo $menu_icon_eleven; ?></option>

		  <?php echo $icon_options; ?>

		</select>
	      </td>
	    </tr>
   <?php /* START: Custom Icon */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('custom_icon_eleven'); ?>"><?php _e('Custom Icon URL:');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('custom_icon_eleven'); ?>" name="<?php echo $this->get_field_name('custom_icon_eleven'); ?>" type="text" value="<?php echo $custom_icon_eleven; ?>" />
	      </td>
	    </tr>
   <?php /* START: User Visibility Question */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_eleven_lo'); ?>"><?php _e('Show to visitors: ', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		    <select class="select" id="<?php echo $this->get_field_id('menu_eleven_lo'); ?>" name="<?php echo $this->get_field_name('menu_eleven_lo'); ?>" selected="<?php echo $menu_eleven_lo; ?>">
			  <option value="<?php echo $menu_eleven_lo ?>" selected="<?php echo $menu_eleven_lo; ?>"><?php echo $menu_eleven_lo; ?></option>
			  <?php echo $role_options; ?>
		    </select>
	      </td>
	    </tr>
</table>
<?php /* END: Menu Item Eleven */ ?>

<?php
// START: Menu Item Twelve $menu_twelve
// $menu_twelve & $menu_icon_twelve & $label_twelve & $custom_icon_twelve

// START: Select Menu Item
?>
<table style="background-color:#dadada;border-top: 2px solid #ffffff;border-bottom: 2px solid #dde5ea;width:100%;">
	    <tr>
	      <td style="width:150px;">
		<label for="<?php echo $this->get_field_id('menu_twelve'); ?>"><?php _e('<strong>Twelfth Menu Item:</strong>', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		<select class="select" id="<?php echo $this->get_field_id('menu_twelve'); ?>" name="<?php echo $this->get_field_name('menu_twelve'); ?>" selected="<?php echo $menu_twelve; ?>">
		  <option value="<?php echo $menu_twelve ?>" selected="<?php echo $menu_twelve; ?>"><?php echo $menu_twelve; ?></option>

		  <?php echo $menu_options; ?>

		</select>
	      </td>
	    </tr>
   <?php /* START: Custom Slug */ ?>
   <?php if ($menu_twelve=='custom_slug') {
	    ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_twelve_custom_slug'); ?>"><?php _e('<span style="color:red;">Custom Menu Slug:</span>');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('menu_twelve_custom_slug'); ?>" name="<?php echo $this->get_field_name('menu_twelve_custom_slug'); ?>" type="text" value="<?php echo $menu_twelve_custom_slug; ?>" />
	      </td>
	    </tr>
   <?php } ?>
   <?php /* START: Custom URL */ ?>
   <?php if ($menu_twelve=='custom_url') {
	    ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_twelve_custom_url'); ?>"><?php _e('<span style="color:red;">Custom Menu URL:</span>');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('menu_twelve_custom_url'); ?>" name="<?php echo $this->get_field_name('menu_twelve_custom_url'); ?>" type="text" value="<?php echo $menu_twelve_custom_url; ?>" />
	      </td>
	    </tr>
   <?php } ?>
   <?php /* START: Label */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('label_twelve'); ?>"><?php _e('Page Title:');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('label_twelve'); ?>" name="<?php echo $this->get_field_name('label_twelve'); ?>" type="text" value="<?php echo $label_twelve; ?>" />
	      </td>
	    </tr>
   <?php /* START: Select Icon */ ?>
	    <tr>
	      <td>
		<label for="<?php echo $this->get_field_id('menu_icon_twelve'); ?>"><?php _e('Icon:', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		<select class="select" id="<?php echo $this->get_field_id('menu_icon_twelve'); ?>" name="<?php echo $this->get_field_name('menu_icon_twelve'); ?>" selected="<?php echo $menu_icon_twelve; ?>">
		  <option value="<?php echo $menu_icon_twelve ?>" selected="<?php echo $menu_icon_twelve; ?>"><?php echo $menu_icon_twelve; ?></option>

		  <?php echo $icon_options; ?>

		</select>
	      </td>
	    </tr>
   <?php /* START: Custom Icon */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('custom_icon_twelve'); ?>"><?php _e('Custom Icon URL:');?></label>
	      </td>
	      <td>
		    <input class="widefat" id="<?php echo $this->get_field_id('custom_icon_twelve'); ?>" name="<?php echo $this->get_field_name('custom_icon_twelve'); ?>" type="text" value="<?php echo $custom_icon_twelve; ?>" />
	      </td>
	    </tr>
   <?php /* START: User Visibility Question */ ?>
	    <tr>
	      <td>
		    <label for="<?php echo $this->get_field_id('menu_twelve_lo'); ?>"><?php _e('Show to visitors: ', 'buddymenu-plugin-handle');?></label>
	      </td>
	      <td>
		    <select class="select" id="<?php echo $this->get_field_id('menu_twelve_lo'); ?>" name="<?php echo $this->get_field_name('menu_twelve_lo'); ?>" selected="<?php echo $menu_twelve_lo; ?>">
			  <option value="<?php echo $menu_twelve_lo ?>" selected="<?php echo $menu_twelve_lo; ?>"><?php echo $menu_twelve_lo; ?></option>
			  <?php echo $role_options; ?>
		    </select>
	      </td>
	    </tr>
</table>

<?php /* END: Menu Item Twelve */ ?>

<?php /* END: Menu Item Selection Options */ ?>

		<p>
			<a href="<?php echo $helpurl; ?>" target="_blank">Help</a> | <a href="<?php echo $rateurl; ?>" target="_blank">Rate</a> | <a href="<?php echo $donateurl; ?>" target="_blank">Donate</a>
		</p>

<?php

  } //end of form
}

add_action( 'widgets_init', create_function('', 'return register_widget("buddymenu_widget_class");') );
//Register Widget


// Code for the widget's output
 function buddymenu($args = '') {
  global $wpdb;
	$defaults = array(
	    'title' => 'BuddyMenu',
	    'showtitle'=>'logged-in',
	    'showwidget'=>'logged-in',
	    'showavatar'=>'0',
	    'welcome'=>'',

	    'menu_one' => 'media',
	    'menu_one_custom_slug' => '',
	    'menu_one_custom_url' => '',
	    'label_one' => 'Media',
	    'menu_icon_one' => 'media',
	    'custom_icon_one' => '',
	    'menu_one_lo'=>'logged-in',

	    'menu_two' => 'activity',
	    'menu_two_custom_slug' => '',
	    'menu_two_custom_url' => '',
	    'label_two' => 'Activity',
	    'menu_icon_two' => 'activity',
	    'custom_icon_two' => '',
	    'menu_two_lo'=>'logged-in',

	    'menu_three' => 'forums',
	    'menu_three_custom_slug' => '',
	    'menu_three_custom_url' => '',
	    'label_three' => 'Forums',
	    'menu_icon_three' => 'forums',
	    'custom_icon_three' => '',
	    'menu_three_lo'=>'logged-in',

	    'menu_four' => 'friends',
	    'menu_four_custom_slug' => '',
	    'menu_four_custom_url' => '',
	    'label_four' => 'Friends',
	    'menu_icon_four' => 'friends',
	    'custom_icon_four' => '',
	    'menu_four_lo'=>'logged-in',

	    'menu_five' => 'messages',
	    'menu_five_custom_slug' => '',
	    'menu_five_custom_url' => '',
	    'label_five' => 'Messages',
	    'menu_icon_five' => 'messages',
	    'custom_icon_five' => '',
	    'menu_five_lo'=>'logged-in',

	    'menu_six' => 'profile',
	    'menu_six_custom_slug' => '',
	    'menu_six_custom_url' => '',
	    'label_six' => 'Profile',
	    'menu_icon_six' => 'profile',
	    'custom_icon_six' => '',
	    'menu_six_lo'=>'logged-in',

	    'menu_seven' => 'profile/edit',
	    'menu_seven_custom_slug' => '',
	    'menu_seven_custom_url' => '',
	    'label_seven' => 'Edit Profile',
	    'menu_icon_seven' => 'edit-profile',
	    'custom_icon_seven' => '',
	    'menu_seven_lo'=>'logged-in',

	    'menu_eight' => 'avatar',
	    'menu_eight_custom_slug' => '',
	    'menu_eight_custom_url' => '',
	    'label_eight' => 'Change Avatar',
	    'menu_icon_eight' => 'avatar',
	    'custom_icon_eight' => '',
	    'menu_eight_lo'=>'logged-in',

	    'menu_nine' => 'settings',
	    'menu_nine_custom_slug' => '',
	    'menu_nine_custom_url' => '',
	    'label_nine' => 'Settings',
	    'menu_icon_nine' => 'settings',
	    'custom_icon_nine' => '',
	    'menu_nine_lo'=>'logged-in',

	    'menu_ten' => 'void',
	    'menu_ten_custom_slug' => '',
	    'menu_ten_custom_url' => '',
	    'label_ten' => 'Custom Slug',
	    'menu_icon_ten' => 'void',
	    'custom_icon_ten' => '',
	    'menu_ten_lo'=>'logged-in',

	    'menu_eleven' => 'void',
	    'menu_eleven_custom_slug' => '',
	    'menu_eleven_custom_url' => '',
	    'label_eleven' => 'Custom Slug',
	    'menu_icon_eleven' => 'void',
	    'custom_icon_eleven' => '',
	    'menu_eleven_lo'=>'logged-in',

	    'menu_twelve' => 'void',
	    'menu_twelve_custom_slug' => '',
	    'menu_twelve_custom_url' => '',
	    'label_twelve' => 'Custom Slug',
	    'menu_icon_twelve' => 'void',
	    'custom_icon_twelve' => '',
	    'menu_twelve_lo'=>'logged-in',

	    'layout'=>'Vertical'
	    );

	$args = wp_parse_args( $args, $defaults );
	extract($args);

	$title = $title;
	$showtitle = $showtitle;
	$showwidget = $showwidget;
	$showavatar = (bool) $showavatar;
	$welcome = $welcome;

	$menu_one = $menu_one;
	$menu_one_custom_slug = $menu_one_custom_slug;
	$menu_one_custom_url = $menu_one_custom_url;
	$label_one = $label_one;
	$menu_icon_one = $menu_icon_one;
	$custom_icon_one = $custom_icon_one;
	$menu_one_lo = $menu_one_lo;

	$menu_two = $menu_two;
	$menu_two_custom_slug = $menu_two_custom_slug;
	$menu_two_custom_url = $menu_two_custom_url;
	$label_two = $label_two;
	$menu_icon_two = $menu_icon_two;
	$custom_icon_two = $custom_icon_two;
	$menu_two_lo = $menu_two_lo;

	$menu_three = $menu_three;
	$menu_three_custom_slug = $menu_three_custom_slug;
	$menu_three_custom_url = $menu_three_custom_url;
	$label_three = $label_three;
	$menu_icon_three = $menu_icon_three;
	$custom_icon_three = $custom_icon_three;
	$menu_three_lo = $menu_three_lo;

	$menu_four = $menu_four;
	$menu_four_custom_slug = $menu_four_custom_slug;
	$menu_four_custom_url = $menu_four_custom_url;
	$label_four = $label_four;
	$menu_icon_four = $menu_icon_four;
	$custom_icon_four = $custom_icon_four;
	$menu_four_lo = $menu_four_lo;

	$menu_five = $menu_five;
	$menu_five_custom_slug = $menu_five_custom_slug;
	$menu_five_custom_url = $menu_five_custom_url;
	$label_five = $label_five;
	$menu_icon_five = $menu_icon_five;
	$custom_icon_five = $custom_icon_five;
	$menu_five_lo = $menu_five_lo;

	$menu_six = $menu_six;
	$menu_six_custom_slug = $menu_six_custom_slug;
	$menu_six_custom_url = $menu_six_custom_url;
	$label_six = $label_six;
	$menu_icon_six = $menu_icon_six;
	$custom_icon_six = $custom_icon_six;
	$menu_six_lo = $menu_six_lo;

	$menu_seven = $menu_seven;
	$menu_seven_custom_slug = $menu_seven_custom_slug;
	$menu_seven_custom_url = $menu_seven_custom_url;
	$label_seven = $label_seven;
	$menu_icon_seven = $menu_icon_seven;
	$custom_icon_seven = $custom_icon_seven;
	$menu_seven_lo = $menu_seven_lo;

	$menu_eight = $menu_eight;
	$menu_eight_custom_slug = $menu_eight_custom_slug;
	$menu_eight_custom_url = $menu_eight_custom_url;
	$label_eight = $label_eight;
	$menu_icon_eight = $menu_icon_eight;
	$custom_icon_eight = $custom_icon_eight;
	$menu_eight_lo = $menu_eight_lo;

	$menu_nine = $menu_nine;
	$menu_nine_custom_slug = $menu_nine_custom_slug;
	$menu_nine_custom_url = $menu_nine_custom_url;
	$label_nine = $label_nine;
	$menu_icon_nine = $menu_icon_nine;
	$custom_icon_nine = $custom_icon_nine;
	$menu_nine_lo = $menu_nine_lo;

	$menu_ten = $menu_ten;
	$menu_ten_custom_slug = $menu_ten_custom_slug;
	$menu_ten_custom_url = $menu_ten_custom_url;
	$label_ten = $label_ten;
	$menu_icon_ten = $menu_icon_ten;
	$custom_icon_ten = $custom_icon_ten;
	$menu_ten_lo = $menu_ten_lo;

	$menu_eleven = $menu_eleven;
	$menu_eleven_custom_slug = $menu_eleven_custom_slug;
	$menu_eleven_custom_url = $menu_eleven_custom_url;
	$label_eleven = $label_eleven;
	$menu_icon_eleven = $menu_icon_eleven;
	$custom_icon_eleven = $custom_icon_eleven;
	$menu_eleven_lo = $menu_eleven_lo;

	$menu_twelve = $menu_twelve;
	$menu_twelve_custom_slug = $menu_twelve_custom_slug;
	$menu_twelve_custom_url = $menu_twelve_custom_url;
	$label_twelve = $label_twelve;
	$menu_icon_twelve = $menu_icon_twelve;
	$custom_icon_twelve = $custom_icon_twelve;
	$menu_twelve_lo = $menu_twelve_lo;

	$layout = $layout;
	$plugind = plugins_url( '/', __FILE__ );
	$bpuserslug = bp_loggedin_user_domain( '/' );
	$bpuseravatarurl = bp_core_fetch_avatar( array( 'item_id' => bp_loggedin_user_id(), 'type' => 'thumb', 'width' => 'false', 'height' => 'false', 'html' => 'false', 'alt' => '' ) );
?>
 	    
 	    <div class="buddymenu <?php if ( $layout == 'Vertical') { echo 'bmvertical'; } else { echo 'bmhorizontal'; } ?>">
	    <?php
		  if ( is_user_logged_in() ) {
	 		if ($showavatar) { echo '<ul>
						      <li class="bmbl-avatar" style="display:inline;"><a href="'.esc_url($bpuserslug).'profile/" alt=""><img src="'.$bpuseravatarurl.'"/></a></li>
						      <li class="bmbl-welcome" style="display:inline;margin-left:10px;vertical-align:top;"><p style="display:inline;">'.$welcome.'</p></li>
						 </ul>';
					 }

	 		if (!$showavatar && $welcome!='') { echo '<ul>
						      <li class="bmbl-welcome" ><p>'.$welcome.'</p></li>
						 </ul>';
					 }

		  }
	    ?>
	    <ul>
	    <?php if ($menu_one_lo!='no-one') {
		  if (!is_user_logged_in()) {
		    if ($menu_one_lo!='logged-out' && $menu_one_lo!='logged-in-out') {
			$menu_one='void';
		    }
		  }
		  if (is_user_logged_in()) {
		    if ($menu_one_lo!='logged-in' && $menu_one_lo!='logged-in-out') {
			$menu_one='void';
		    }
		  }
		  if (($menu_one != 'void') && ($menu_one != '')) {
		  if ($label_one=='') { $label_one = $menu_one; }
		  if ($menu_icon_one=='void') { $style=''; }
		  if ($menu_icon_one!='void') {
		    if ($menu_icon_one!='custom' && $menu_icon_one!='bp-avatar') { $icon_one=$plugind.'img/'.$menu_icon_one.'.png'; }
		    if ($menu_icon_one=='custom' && $custom_icon_one=='') { $icon_one=$plugind.'img/'.$menu_icon_one.'.png'; }
		    if ($menu_icon_one=='custom' && $custom_icon_one!='') { $icon_one=$custom_icon_one; }
		    if ($menu_icon_one=='bp-avatar') { $icon_one=$bpuseravatarurl; }
		    $style='style="background:url('.$icon_one.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"';
		  }
		  if ($menu_one=='custom_slug') {$menu_one=$bpuserslug.$menu_one_custom_slug.'/';}
		  elseif ($menu_one=='custom_url') {$menu_one=$menu_one_custom_url;}
		  elseif ($menu_one=='home') {$menu_one=home_url('/');}
		  elseif ($menu_one=='login') {$menu_one=wp_login_url();}
		  elseif ($menu_one=='logout') {$menu_one=wp_logout_url();}
		  else {$menu_one=$bpuserslug.$menu_one.'/';}
		  ?>
		  <li class="buddy-<?php echo $menu_one; ?>" ><a href="<?php echo $menu_one ?>" title="<?php esc_attr_e($menu_one); ?>" <?php echo $style; ?>><?php esc_attr_e($label_one); ?></a></li>
		<?php } }; ?>

	    <?php if ($menu_two_lo!='no-one') {
		  if (!is_user_logged_in()) {
		    if ($menu_two_lo!='logged-out' && $menu_two_lo!='logged-in-out') {
			$menu_two='void';
		    }
		  }
		  if (is_user_logged_in()) {
		    if ($menu_two_lo!='logged-in' && $menu_two_lo!='logged-in-out') {
			$menu_two='void';
		    }
		  }
		  if (($menu_two != 'void') && ($menu_two != '')) {
		  if ($label_two=='') { $label_two = $menu_two; }
		  if ($menu_icon_two=='void') { $style=''; }
		  if ($menu_icon_two!='void') {
		    if ($menu_icon_two!='custom' && $menu_icon_two!='bp-avatar') { $icon_two=$plugind.'img/'.$menu_icon_two.'.png'; }
		    if ($menu_icon_two=='custom' && $custom_icon_two=='') { $icon_two=$plugind.'img/'.$menu_icon_two.'.png'; }
		    if ($menu_icon_two=='custom' && $custom_icon_two!='') { $icon_two=$custom_icon_two; }
		    if ($menu_icon_two=='bp-avatar') { $icon_two=$bpuseravatarurl; }
		    $style='style="background:url('.$icon_two.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"';
		  }
		  if ($menu_two=='custom_slug') {$menu_two=$bpuserslug.$menu_two_custom_slug;}
		  elseif ($menu_two=='custom_url') {$menu_two=$menu_two_custom_url;}
		  elseif ($menu_two=='login') {$menu_two=wp_login_url();}
		  elseif ($menu_two=='logout') {$menu_two=wp_logout_url();}
		  else {$menu_two=$bpuserslug.$menu_two;}
		  ?>
		  <li class="buddy-<?php echo $menu_two; ?>" ><a href="<?php echo $menu_two ?>/" title="<?php esc_attr_e($menu_two); ?>" <?php echo $style; ?>><?php esc_attr_e($label_two); ?></a></li>
		<?php }}; ?>

	    <?php if ($menu_three_lo!='no-one') {
		  if (!is_user_logged_in()) {
		    if ($menu_three_lo!='logged-out' && $menu_three_lo!='logged-in-out') {
			$menu_three='void';
		    }
		  }
		  if (is_user_logged_in()) {
		    if ($menu_three_lo!='logged-in' && $menu_three_lo!='logged-in-out') {
			$menu_three='void';
		    }
		  }
		  if (($menu_three != 'void') && ($menu_three != '')) {
		  if ($label_three=='') { $label_three = $menu_three; }
		  if ($menu_icon_three=='void') { $style=''; }
		  if ($menu_icon_three!='void') {
		    if ($menu_icon_three!='custom' && $menu_icon_three!='bp-avatar') { $icon_three=$plugind.'img/'.$menu_icon_three.'.png'; }
		    if ($menu_icon_three=='custom' && $custom_icon_three=='') { $icon_three=$plugind.'img/'.$menu_icon_three.'.png'; }
		    if ($menu_icon_three=='custom' && $custom_icon_three!='') { $icon_three=$custom_icon_three; }
		    if ($menu_icon_three=='bp-avatar') { $icon_three=$bpuseravatarurl; }
		    $style='style="background:url('.$icon_three.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"';
		  }
		  if ($menu_three=='custom_slug') {$menu_three=$bpuserslug.$menu_three_custom_slug;}
		  elseif ($menu_three=='custom_url') {$menu_three=$menu_three_custom_url;}
		  elseif ($menu_three=='login') {$menu_three=wp_login_url();}
		  elseif ($menu_three=='logout') {$menu_three=wp_logout_url();}
		  else {$menu_three=$bpuserslug.$menu_three;}
		  ?>
		  <li class="buddy-<?php echo $menu_three; ?>" ><a href="<?php echo $menu_three ?>/" title="<?php esc_attr_e($menu_three); ?>" <?php echo $style; ?>><?php esc_attr_e($label_three); ?></a></li>
		<?php }}; ?>

	    <?php if ($menu_four_lo!='no-one') {
		  if (!is_user_logged_in()) {
		    if ($menu_four_lo!='logged-out' && $menu_four_lo!='logged-in-out') {
			$menu_four='void';
		    }
		  }
		  if (is_user_logged_in()) {
		    if ($menu_four_lo!='logged-in' && $menu_four_lo!='logged-in-out') {
			$menu_four='void';
		    }
		  }
		  if (($menu_four != 'void') && ($menu_four != '')) {
		  if ($label_four=='') { $label_four = $menu_four; }
		  if ($menu_icon_four=='void') { $style=''; }
		  if ($menu_icon_four!='void') {
		    if ($menu_icon_four!='custom' && $menu_icon_four!='bp-avatar') { $icon_four=$plugind.'img/'.$menu_icon_four.'.png'; }
		    if ($menu_icon_four=='custom' && $custom_icon_four=='') { $icon_four=$plugind.'img/'.$menu_icon_four.'.png'; }
		    if ($menu_icon_four=='custom' && $custom_icon_four!='') { $icon_four=$custom_icon_four; }
		    if ($menu_icon_four=='bp-avatar') { $icon_four=$bpuseravatarurl; }
		    $style='style="background:url('.$icon_four.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"';
		  }
		  if ($menu_four=='custom_slug') {$menu_four=$bpuserslug.$menu_four_custom_slug;}
		  elseif ($menu_four=='custom_url') {$menu_four=$menu_four_custom_url;}
		  elseif ($menu_four=='login') {$menu_four=wp_login_url();}
		  elseif ($menu_four=='logout') {$menu_four=wp_logout_url();}
		  else {$menu_four=$bpuserslug.$menu_four;}
		  ?>
		  <li class="buddy-<?php echo $menu_four; ?>" ><a href="<?php echo $menu_four ?>/" title="<?php esc_attr_e($menu_four); ?>" <?php echo $style; ?>><?php esc_attr_e($label_four); ?></a></li>
		<?php }}; ?>

	    <?php if ($menu_five_lo!='no-one') {
		  if (!is_user_logged_in()) {
		    if ($menu_five_lo!='logged-out' && $menu_five_lo!='logged-in-out') {
			$menu_five='void';
		    }
		  }
		  if (is_user_logged_in()) {
		    if ($menu_five_lo!='logged-in' && $menu_five_lo!='logged-in-out') {
			$menu_five='void';
		    }
		  }
		  if (($menu_five != 'void') && ($menu_five != '')) {
		  if ($label_five=='') { $label_five = $menu_five; }
		  if ($menu_icon_five=='void') { $style=''; }
		  if ($menu_icon_five!='void') {
		    if ($menu_icon_five!='custom' && $menu_icon_five!='bp-avatar') { $icon_five=$plugind.'img/'.$menu_icon_five.'.png'; }
		    if ($menu_icon_five=='custom' && $custom_icon_five=='') { $icon_five=$plugind.'img/'.$menu_icon_five.'.png'; }
		    if ($menu_icon_five=='custom' && $custom_icon_five!='') { $icon_five=$custom_icon_five; }
		    if ($menu_icon_five=='bp-avatar') { $icon_five=$bpuseravatarurl; }
		    $style='style="background:url('.$icon_five.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"';
		  }
		  if ($menu_five=='custom_slug') {$menu_five=$bpuserslug.$menu_five_custom_slug;}
		  elseif ($menu_five=='custom_url') {$menu_five=$menu_five_custom_url;}
		  elseif ($menu_five=='login') {$menu_five=wp_login_url();}
		  elseif ($menu_five=='logout') {$menu_five=wp_logout_url();}
		  else {$menu_five=$bpuserslug.$menu_five;}
		  ?>
		  <li class="buddy-<?php echo $menu_five; ?>" ><a href="<?php echo $menu_five ?>/" title="<?php esc_attr_e($menu_five); ?>" <?php echo $style; ?>><?php esc_attr_e($label_five); ?></a></li>
		<?php }}; ?>

	    <?php if ($menu_six_lo!='no-one') {
		  if (!is_user_logged_in()) {
		    if ($menu_six_lo!='logged-out' && $menu_six_lo!='logged-in-out') {
			$menu_six='void';
		    }
		  }
		  if (is_user_logged_in()) {
		    if ($menu_six_lo!='logged-in' && $menu_six_lo!='logged-in-out') {
			$menu_six='void';
		    }
		  }
		  if (($menu_six != 'void') && ($menu_six != '')) {
		  if ($label_six=='') { $label_six = $menu_six; }
		  if ($menu_icon_six=='void') { $style=''; }
		  if ($menu_icon_six!='void') {
		    if ($menu_icon_six!='custom' && $menu_icon_six!='bp-avatar') { $icon_six=$plugind.'img/'.$menu_icon_six.'.png'; }
		    if ($menu_icon_six=='custom' && $custom_icon_six=='') { $icon_six=$plugind.'img/'.$menu_icon_six.'.png'; }
		    if ($menu_icon_six=='custom' && $custom_icon_six!='') { $icon_six=$custom_icon_six; }
		    if ($menu_icon_six=='bp-avatar') { $icon_six=$bpuseravatarurl; }
		    $style='style="background:url('.$icon_six.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"';
		  }
		  if ($menu_six=='custom_slug') {$menu_six=$bpuserslug.$menu_six_custom_slug;}
		  elseif ($menu_six=='custom_url') {$menu_six=$menu_six_custom_url;}
		  elseif ($menu_six=='login') {$menu_six=wp_login_url();}
		  elseif ($menu_six=='logout') {$menu_six=wp_logout_url();}
		  else {$menu_six=$bpuserslug.$menu_six;}
		  ?>
		  <li class="buddy-<?php echo $menu_six; ?>" ><a href="<?php echo $menu_six ?>/" title="<?php esc_attr_e($menu_six); ?>" <?php echo $style; ?>><?php esc_attr_e($label_six); ?></a></li>
		<?php }}; ?>

	    <?php if ($menu_seven_lo!='no-one') {
		  if (!is_user_logged_in()) {
		    if ($menu_seven_lo!='logged-out' && $menu_seven_lo!='logged-in-out') {
			$menu_seven='void';
		    }
		  }
		  if (is_user_logged_in()) {
		    if ($menu_seven_lo!='logged-in' && $menu_seven_lo!='logged-in-out') {
			$menu_seven='void';
		    }
		  }
		  if (($menu_seven != 'void') && ($menu_seven != '')) {
		  if ($label_seven=='') { $label_seven = $menu_seven; }
		  if ($menu_icon_seven=='void') { $style=''; }
		  if ($menu_icon_seven!='void') {
		    if ($menu_icon_seven!='custom' && $menu_icon_seven!='bp-avatar') { $icon_seven=$plugind.'img/'.$menu_icon_seven.'.png'; }
		    if ($menu_icon_seven=='custom' && $custom_icon_seven=='') { $icon_seven=$plugind.'img/'.$menu_icon_seven.'.png'; }
		    if ($menu_icon_seven=='custom' && $custom_icon_seven!='') { $icon_seven=$custom_icon_seven; }
		    if ($menu_icon_seven=='bp-avatar') { $icon_seven=$bpuseravatarurl; }
		    $style='style="background:url('.$icon_seven.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"';
		  }
		  if ($menu_seven=='custom_slug') {$menu_seven=$bpuserslug.$menu_seven_custom_slug;}
		  elseif ($menu_seven=='custom_url') {$menu_seven=$menu_seven_custom_url;}
		  elseif ($menu_seven=='login') {$menu_seven=wp_login_url();}
		  elseif ($menu_seven=='logout') {$menu_seven=wp_logout_url();}
		  else {$menu_seven=$bpuserslug.$menu_seven;}
		  ?>
		  <li class="buddy-<?php echo $menu_seven; ?>" ><a href="<?php echo $menu_seven ?>/" title="<?php esc_attr_e($menu_seven); ?>" <?php echo $style; ?>><?php esc_attr_e($label_seven); ?></a></li>
		<?php }}; ?>

	    <?php if ($menu_eight_lo!='no-one') {
		  if (!is_user_logged_in()) {
		    if ($menu_eight_lo!='logged-out' && $menu_eight_lo!='logged-in-out') {
			$menu_eight='void';
		    }
		  }
		  if (is_user_logged_in()) {
		    if ($menu_eight_lo!='logged-in' && $menu_eight_lo!='logged-in-out') {
			$menu_eight='void';
		    }
		  }
		  if (($menu_eight != 'void') && ($menu_eight != '')) {
		  if ($label_eight=='') { $label_eight = $menu_eight; }
		  if ($menu_icon_eight=='void') { $style=''; }
		  if ($menu_icon_eight!='void') {
		    if ($menu_icon_eight!='custom' && $menu_icon_eight!='bp-avatar') { $icon_eight=$plugind.'img/'.$menu_icon_eight.'.png'; }
		    if ($menu_icon_eight=='custom' && $custom_icon_eight=='') { $icon_eight=$plugind.'img/'.$menu_icon_eight.'.png'; }
		    if ($menu_icon_eight=='custom' && $custom_icon_eight!='') { $icon_eight=$custom_icon_eight; }
		    if ($menu_icon_eight=='bp-avatar') { $icon_eight=$bpuseravatarurl; }
		    $style='style="background:url('.$icon_eight.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"';
		  }
		  if ($menu_eight=='custom_slug') {$menu_eight=$bpuserslug.$menu_eight_custom_slug;}
		  elseif ($menu_eight=='custom_url') {$menu_eight=$menu_eight_custom_url;}
		  elseif ($menu_eight=='login') {$menu_eight=wp_login_url();}
		  elseif ($menu_eight=='logout') {$menu_eight=wp_logout_url();}
		  else {$menu_eight=$bpuserslug.$menu_eight;}
		  ?>
		  <li class="buddy-<?php echo $menu_eight; ?>" ><a href="<?php echo $menu_eight ?>/" title="<?php esc_attr_e($menu_eight); ?>" <?php echo $style; ?>><?php esc_attr_e($label_eight); ?></a></li>
		<?php }}; ?>

	    <?php if ($menu_nine_lo!='no-one') {
		  if (!is_user_logged_in()) {
		    if ($menu_nine_lo!='logged-out' && $menu_nine_lo!='logged-in-out') {
			$menu_nine='void';
		    }
		  }
		  if (is_user_logged_in()) {
		    if ($menu_nine_lo!='logged-in' && $menu_nine_lo!='logged-in-out') {
			$menu_nine='void';
		    }
		  }
		  if (($menu_nine != 'void') && ($menu_nine != '')) {
		  if ($label_nine=='') { $label_nine = $menu_nine; }
		  if ($menu_icon_nine=='void') { $style=''; }
		  if ($menu_icon_nine!='void') {
		    if ($menu_icon_nine!='custom' && $menu_icon_nine!='bp-avatar') { $icon_nine=$plugind.'img/'.$menu_icon_nine.'.png'; }
		    if ($menu_icon_nine=='custom' && $custom_icon_nine=='') { $icon_nine=$plugind.'img/'.$menu_icon_nine.'.png'; }
		    if ($menu_icon_nine=='custom' && $custom_icon_nine!='') { $icon_nine=$custom_icon_nine; }
		    if ($menu_icon_nine=='bp-avatar') { $icon_nine=$bpuseravatarurl; }
		    $style='style="background:url('.$icon_nine.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"';
		  }
		  if ($menu_nine=='custom_slug') {$menu_nine=$bpuserslug.$menu_nine_custom_slug;}
		  elseif ($menu_nine=='custom_url') {$menu_nine=$menu_nine_custom_url;}
		  elseif ($menu_nine=='login') {$menu_nine=wp_login_url();}
		  elseif ($menu_nine=='logout') {$menu_nine=wp_logout_url();}
		  else {$menu_nine=$bpuserslug.$menu_nine;}
		  ?>
		  <li class="buddy-<?php echo $menu_nine; ?>" ><a href="<?php echo $menu_nine ?>/" title="<?php esc_attr_e($menu_nine); ?>" <?php echo $style; ?>><?php esc_attr_e($label_nine); ?></a></li>
		<?php }}; ?>

	    <?php if ($menu_ten_lo!='no-one') {
		  if (!is_user_logged_in()) {
		    if ($menu_ten_lo!='logged-out' && $menu_ten_lo!='logged-in-out') {
			$menu_ten='void';
		    }
		  }
		  if (is_user_logged_in()) {
		    if ($menu_ten_lo!='logged-in' && $menu_ten_lo!='logged-in-out') {
			$menu_ten='void';
		    }
		  }
		  if (($menu_ten != 'void') && ($menu_ten != '')) {
		  if ($label_ten=='') { $label_ten = $menu_ten; }
		  if ($menu_icon_ten=='void') { $style=''; }
		  if ($menu_icon_ten!='void') {
		    if ($menu_icon_ten!='custom' && $menu_icon_ten!='bp-avatar') { $icon_ten=$plugind.'img/'.$menu_icon_ten.'.png'; }
		    if ($menu_icon_ten=='custom' && $custom_icon_ten=='') { $icon_ten=$plugind.'img/'.$menu_icon_ten.'.png'; }
		    if ($menu_icon_ten=='custom' && $custom_icon_ten!='') { $icon_ten=$custom_icon_ten; }
		    if ($menu_icon_ten=='bp-avatar') { $icon_ten=$bpuseravatarurl; }
		    $style='style="background:url('.$icon_ten.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"';
		  }
		  if ($menu_ten=='custom_slug') {$menu_ten=$bpuserslug.$menu_ten_custom_slug;}
		  elseif ($menu_ten=='custom_url') {$menu_ten=$menu_ten_custom_url;}
		  elseif ($menu_ten=='login') {$menu_ten=wp_login_url();}
		  elseif ($menu_ten=='logout') {$menu_ten=wp_logout_url();}
		  else {$menu_ten=$bpuserslug.$menu_ten;}
		  ?>
		  <li class="buddy-<?php echo $menu_ten; ?>" ><a href="<?php echo $menu_ten ?>/" title="<?php esc_attr_e($menu_ten); ?>" <?php echo $style; ?>><?php esc_attr_e($label_ten); ?></a></li>
		<?php }}; ?>

	    <?php if ($menu_eleven_lo!='no-one') {
		  if (!is_user_logged_in()) {
		    if ($menu_eleven_lo!='logged-out' && $menu_eleven_lo!='logged-in-out') {
			$menu_eleven='void';
		    }
		  }
		  if (is_user_logged_in()) {
		    if ($menu_eleven_lo!='logged-in' && $menu_eleven_lo!='logged-in-out') {
			$menu_eleven='void';
		    }
		  }
		  if (($menu_eleven != 'void') && ($menu_eleven != '')) {
		  if ($label_eleven=='') { $label_eleven = $menu_eleven; }
		  if ($menu_icon_eleven=='void') { $style=''; }
		  if ($menu_icon_eleven!='void') {
		    if ($menu_icon_eleven!='custom' && $menu_icon_eleven!='bp-avatar') { $icon_eleven=$plugind.'img/'.$menu_icon_eleven.'.png'; }
		    if ($menu_icon_eleven=='custom' && $custom_icon_eleven=='') { $icon_eleven=$plugind.'img/'.$menu_icon_eleven.'.png'; }
		    if ($menu_icon_eleven=='custom' && $custom_icon_eleven!='') { $icon_eleven=$custom_icon_eleven; }
		    if ($menu_icon_eleven=='bp-avatar') { $icon_eleven=$bpuseravatarurl; }
		    $style='style="background:url('.$icon_eleven.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"';
		  }
		  if ($menu_eleven=='custom_slug') {$menu_eleven=$bpuserslug.$menu_eleven_custom_slug;}
		  elseif ($menu_eleven=='custom_url') {$menu_eleven=$menu_eleven_custom_url;}
		  elseif ($menu_eleven=='login') {$menu_eleven=wp_login_url();}
		  elseif ($menu_eleven=='logout') {$menu_eleven=wp_logout_url();}
		  else {$menu_eleven=$bpuserslug.$menu_eleven;}
		  ?>
		  <li class="buddy-<?php echo $menu_eleven; ?>" ><a href="<?php echo $menu_eleven ?>/" title="<?php esc_attr_e($menu_eleven); ?>" <?php echo $style; ?>><?php esc_attr_e($label_eleven); ?></a></li>
		<?php }}; ?>

	    <?php if ($menu_twelve_lo!='no-one') {
		  if (!is_user_logged_in()) {
		    if ($menu_twelve_lo!='logged-out' && $menu_twelve_lo!='logged-in-out') {
			$menu_twelve='void';
		    }
		  }
		  if (is_user_logged_in()) {
		    if ($menu_twelve_lo!='logged-in' && $menu_twelve_lo!='logged-in-out') {
			$menu_twelve='void';
		    }
		  }
		  if (($menu_twelve != 'void') && ($menu_twelve != '')) {
		  if ($label_twelve=='') { $label_twelve = $menu_twelve; }
		  if ($menu_icon_twelve=='void') { $style=''; }
		  if ($menu_icon_twelve!='void') {
		    if ($menu_icon_twelve!='custom' && $menu_icon_twelve!='bp-avatar') { $icon_twelve=$plugind.'img/'.$menu_icon_twelve.'.png'; }
		    if ($menu_icon_twelve=='custom' && $custom_icon_twelve=='') { $icon_twelve=$plugind.'img/'.$menu_icon_twelve.'.png'; }
		    if ($menu_icon_twelve=='custom' && $custom_icon_twelve!='') { $icon_twelve=$custom_icon_twelve; }
		    if ($menu_icon_twelve=='bp-avatar') { $icon_twelve=$bpuseravatarurl; }
		    $style='style="background:url('.$icon_twelve.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"';
		  }
		  if ($menu_twelve=='custom_slug') {$menu_twelve=$bpuserslug.$menu_twelve_custom_slug;}
		  elseif ($menu_twelve=='custom_url') {$menu_twelve=$menu_twelve_custom_url;}
		  elseif ($menu_twelve=='login') {$menu_twelve=wp_login_url();}
		  elseif ($menu_twelve=='logout') {$menu_twelve=wp_logout_url();}
		  else {$menu_twelve=$bpuserslug.$menu_twelve;}
		  ?>
		  <li class="buddy-<?php echo $menu_twelve; ?>" ><a href="<?php echo $menu_twelve ?>/" title="<?php esc_attr_e($menu_twelve); ?>" <?php echo $style; ?>><?php esc_attr_e($label_twelve); ?></a></li>
		<?php }}; ?>

	    </ul>
	    </div>
<?php

 } // End code for the widget's output



// Do Menu Shortcode

  function shortmenu($atts) {

	 	// Check visitor is logged in

	 	if ( is_user_logged_in() ) {

		  extract(shortcode_atts(array(
			  'bmmed' => 'Media', # Custom Titles
			  'bmact' => 'Activity',
			  'bmfor' => 'Forums',
			  'bmfri' => 'Friends',
			  'bmmsg' => 'Messages',
			  'bmpro' => 'Profile',
			  'bmedpro' => 'Edit Profile',
			  'bmchav' => 'Change Avatar',
			  'bmset' => 'Settings',
			  'bmlay' => 'Vertical',
			  'bmmedi' => 'media.png', # Custom Icons - Set to '' for no icon
			  'bmacti' => 'activity.png',
			  'bmfori' => 'forums.png',
			  'bmfrii' => 'friends.png',
			  'bmmsgi' => 'messages.png',
			  'bmproi' => 'profile.png',
			  'bmedproi' => 'edit-profile.png',
			  'bmchavi' => 'avatar.png',
			  'bmseti' => 'settings.png',
			  'bmicons' => '1', # Disable Icons
		  ), $atts));

		  $plugind = plugins_url( '/', __FILE__ );
		  $icon=$plugind.'img/';

		  if ($bmicons!='1') { $bmmedi_style=''; $bmacti_style=''; $bmfori_style=''; $bmfrii_style=''; $bmmsgi_style=''; $bmproi_style=''; $bmedproi_style=''; $bmchavi_style=''; $bmseti_style=''; }

		  if ($bmicons=='1') {

		  if ($bmmedi=='media.png') {
		      $bmmedi_style='style="background:url('.$icon.$bmmedi.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"'; }
		      else {
		      $bmmedi_style='style="background:url('.$bmmedi.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"'; }
		  if ($bmacti=='activity.png') {
		      $bmacti_style='style="background:url('.$icon.$bmacti.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"'; }
		      else {
		      $bmacti_style='style="background:url('.$bmacti.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"'; }
		  if ($bmfori=='forums.png') {
		      $bmfori_style='style="background:url('.$icon.$bmfori.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"'; }
		      else {
		      $bmfori_style='style="background:url('.$bmfori.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"'; }
		  if ($bmfrii=='friends.png') {
		      $bmfrii_style='style="background:url('.$icon.$bmfrii.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"'; }
		      else {
		      $bmfrii_style='style="background:url('.$bmfrii.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"'; }
		  if ($bmmsgi=='messages.png') {
		      $bmmsgi_style='style="background:url('.$icon.$bmmsgi.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"'; }
		      else {
		      $bmmsgi_style='style="background:url('.$bmmsgi.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"'; }
		  if ($bmproi=='profile.png') {
		      $bmproi_style='style="background:url('.$icon.$bmproi.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"'; }
		      else {
		      $bmproi_style='style="background:url('.$bmproi.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"'; }
		  if ($bmedproi=='edit-profile.png') {
		      $bmedproi_style='style="background:url('.$icon.$bmedproi.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"'; }
		      else {
		      $bmedproi_style='style="background:url('.$bmedproi.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"'; }
		  if ($bmchavi=='avatar.png') {
		      $bmchavi_style='style="background:url('.$icon.$bmchavi.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"'; }
		      else {
		      $bmchavi_style='style="background:url('.$bmchavi.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"'; }
		  if ($bmseti=='settings.png') {
		      $bmseti_style='style="background:url('.$icon.$bmseti.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"'; }
		      else {
		      $bmseti_style='style="background:url('.$bmseti.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"'; }

		  }

?>    
 	    <div class="buddymenu <?php if ( strtolower(esc_attr($bmlay)) == strtolower('Vertical') ) { echo 'bmvertical'; } else { echo 'bmhorizontal'; } ?> buddyshort">

	    <ul>
		<?php if ($bmmed !='-1') { ?>
		<li class="buddy-media"><a href="<?php echo bp_loggedin_user_domain( '/' ) ?>media/" title="<?php esc_attr_e($bmmed); ?>" <?php echo $bmmedi_style; ?>><?php esc_attr_e($bmmed); ?></a></li>
		<?php }; ?>
		<?php if ($bmact !='-1') { ?>
		<li class="buddy-activity"><a href="<?php echo bp_loggedin_user_domain( '/' ) ?>activity/" title="<?php esc_attr_e($bmact); ?>" <?php echo $bmacti_style; ?>><?php esc_attr_e($bmact); ?></a></li>
		<?php }; ?>
		<?php if ($bmfor !='-1') { ?>
		<li class="buddy-forums"><a href="<?php echo bp_loggedin_user_domain( '/' ) ?>forums/" title="<?php esc_attr_e($bmfor); ?>" <?php echo $bmfori_style; ?>><?php esc_attr_e($bmfor); ?></a></li>
		<?php }; ?>
		<?php if ($bmfri !='-1') { ?>
		<li class="buddy-friends"><a href="<?php echo bp_loggedin_user_domain( '/' ) ?>friends/" title="<?php esc_attr_e($bmfri); ?>" <?php echo $bmfrii_style; ?>><?php esc_attr_e($bmfri); ?></a></li>
		<?php }; ?>
		<?php if ($bmmsg !='-1') { ?>
		<li class="buddy-messages"><a href="<?php echo bp_loggedin_user_domain( '/' ) ?>messages/" title="<?php esc_attr_e($bmmsg); ?>" <?php echo $bmmsgi_style; ?>><?php esc_attr_e($bmmsg); ?></a></li>
		<?php }; ?>
		<?php if ($bmpro !='-1') { ?>
		<li class="buddy-profile"><a href="<?php echo bp_loggedin_user_domain( '/' ) ?>profile/" title="<?php esc_attr_e($bmpro); ?>" <?php echo $bmproi_style; ?>><?php esc_attr_e($bmpro); ?></a></li>
		<?php }; ?>
		<?php if ($bmedpro !='-1') { ?>
		<li class="buddy-profile-edit"><a href="<?php echo bp_loggedin_user_domain( '/' ) ?>profile/edit/" title="<?php esc_attr_e($bmedpro); ?>" <?php echo $bmedproi_style; ?>><?php esc_attr_e($bmedpro); ?></a></li>
		<?php }; ?>
		<?php if ($bmchav !='-1') { ?>
		<li class="buddy-change-avatar"><a href="<?php echo bp_loggedin_user_domain( '/' ) ?>profile/change-avatar/" title="<?php esc_attr_e($bmchav); ?>" <?php echo $bmchavi_style; ?>><?php esc_attr_e($bmchav); ?></a></li>
		<?php }; ?>
		<?php if ($bmset !='-1') { ?>
		<li class="buddy-settings"><a href="<?php echo bp_loggedin_user_domain( '/' ) ?>settings/" title="<?php esc_attr_e($bmset); ?>" <?php echo $bmseti_style; ?>><?php esc_attr_e($bmset); ?></a></li>
		<?php }; ?>
	    </ul>
	    </div>
<?php

	 	}
	 	// End if user logged in

  }


// Clean shortcode() Function to return it instead of echoing it

  function cleanshortmenu($atts){
    ob_start();
    shortmenu($atts);
    $output_menu=ob_get_contents();
    ob_end_clean();

  return $output_menu;

  }

  add_shortcode('buddymenu', 'cleanshortmenu');

// End Menu Shortcode





// Do Link Shortcode

  function shortlink($atts) {

	 	// Check visitor is logged in

	 	if ( is_user_logged_in() ) {

			extract(shortcode_atts(array(
				'bllink' => 'profile',
				'bltitle' => '',
				'bltext' => 'your profile',
				'blq' => '',
				'blicon' => '',
			), $atts));

			if ($blicon!='') {
			    $plugind = plugins_url( '/', __FILE__ );
			    if ($blicon=='media') {$icon=$plugind.'img/'.$blicon.'.png';}
			    elseif ($blicon=='activity') {$icon=$plugind.'img/'.$blicon.'.png';}
			    elseif ($blicon=='friends') {$icon=$plugind.'img/'.$blicon.'.png';}
			    elseif ($blicon=='messages') {$icon=$plugind.'img/'.$blicon.'.png';}
			    elseif ($blicon=='profile') {$icon=$plugind.'img/'.$blicon.'.png';}
			    elseif ($blicon=='edit-profile') {$icon=$plugind.'img/'.$blicon.'.png';}
			    elseif ($blicon=='avatar') {$icon=$plugind.'img/'.$blicon.'.png';}
			    elseif ($blicon=='settings') {$icon=$plugind.'img/'.$blicon.'.png';}
			    else {$icon=$blicon;}

			$style='style="background:url('.$icon.') no-repeat left center;background-size: 14px 14px;padding-left: 24px;"';

			} else {
			$style='';
			}


			if ( esc_attr($blq) == 'med') { $bllink='media'; $bltitle='Your Media'; $bltext='your media'; };
			if ( esc_attr($blq) == 'act') { $bllink='activity'; $bltitle='Your Activity'; $bltext='your activity'; };
			if ( esc_attr($blq) == 'for') { $bllink='forums'; $bltitle='Your Forums'; $bltext='your forums'; };
			if ( esc_attr($blq) == 'fri') { $bllink='friends'; $bltitle='Your Friends'; $bltext='your friends'; };
			if ( esc_attr($blq) == 'msg') { $bllink='messages'; $bltitle='Your Messages'; $bltext='your messages'; };
			if ( esc_attr($blq) == 'pro') { $bllink='profile'; $bltitle='Your Profile'; $bltext='your profile'; };
			if ( esc_attr($blq) == 'edpro') { $bllink='profile/edit'; $bltitle='Edit Your Profile'; $bltext='edit your profile'; };
			if ( esc_attr($blq) == 'chav') { $bllink='profile/change-avatar'; $bltitle='Change Your Avatar'; $bltext='change your avatar'; };
			if ( esc_attr($blq) == 'set') { $bllink='settings'; $bltitle='Your Settings'; $bltext='your settings'; };

?>
		<a href="<?php esc_attr_e(bp_loggedin_user_domain()) ?><?php echo esc_attr($bllink); ?>/" title="<?php esc_attr_e($bltitle); ?>" <?php echo $style; ?>><?php esc_attr_e($bltext); ?></a>
<?php

	 	}
	 	// End Visitor Logged In If
else {
	 	// If Visitor is Logged Out

			extract(shortcode_atts(array(
				'blolink' => wp_login_url(),
				'blotitle' => '',
				'blotext' => 'login to view this text',
			), $atts));
?>
	 	<a href="<?php esc_attr_e($blolink); ?>" title="<?php esc_attr_e($blotitle); ?>"><?php esc_attr_e($blotext); ?></a>
<?php
	 	}
	 	// End Visitor Logged Out If

  }

  function cleanshortlink($atts){
    ob_start();
    shortlink($atts);
    $link_output=ob_get_contents();
    ob_end_clean();

  return $link_output;

  }

add_shortcode('buddylink', 'cleanshortlink');

// End Link Shortcode
 
?>