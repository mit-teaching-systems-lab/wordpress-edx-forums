<?php
/*
Plugin Name: GetShopped Support Forum Plugin
Plugin URI: http://getshopped.org
Description: Turn your new bb-Press 2.0 forums into support forums
Author: dtbaker, Mychelle, GetShopped, mufasa
Version: 3.0.3
*/

//////
// Activate and Deactive functions
/////
function bbps_activate() {
	register_uninstall_hook( __FILE__, 'bbps_uninstall' );

	//include the options page now so we can save the options on activation.
	include_once( plugin_dir_path(__FILE__).'includes/bbps-core-options.php' );
	do_action( 'bbps-activation' );
}
	register_activation_hook( __FILE__ , 'bbps_activate' );


//want toi remove the options etc TO DO
function bbps_uninstall(){

}

////////
// BBPS Main plugin Setup
///////

/*
* WEBMAN EDITED CODE STARTS HERE (1)
* www.webmandesign.eu
*/
/*
function bbps_setup
called by the init hook.
uses:
@function bbps_define_constants
@function bbps_includes
add any additional setup function calls into here
*/
function bbps_setup(){
	bbps_define_constants(); // defines all constants file paths etc
	bbps_includes(); // includes all plugin files
}
bbps_setup(); // couldn't get it working in a hook with the vote filtering
//add_action( 'plugins_loaded', 'bbps_setup');
/*
* WEBMAN EDITED CODE ENDS HERE (1)
* www.webmandesign.eu
*/

/*
function bbps_define_constants
simply defines the contants
@return: nothing
*/
function bbps_define_constants(){
	define( "BBPS_PATH", plugin_dir_path(__FILE__) );
	define( "BBPS_ADMIN_PATH", BBPS_PATH.'admin/' );
	define( "BBPS_TEMPLATE_PATH", BBPS_PATH.'templates/' );
	define( "BBPS_INCLUDES_PATH", BBPS_PATH.'includes/' );
	define( "BBPS_WIDGETS_PATH", BBPS_PATH.'widgets/' );
	define( "BBPS_TEMPLATES_URL", plugins_url('templates', __FILE__) );
	define( "BBPS_WIDGETS_URL", plugins_url('widgets', __FILE__) );
}

/*
function bbps_includes
includes all the files to add more files simply
add the file name to the correct array
@return: nothing
*/
function bbps_includes(){

	//admin folder
	if ( is_admin() ){
		$admin_files = array(
				'bbps-admin',	//meta box and save functions
				'bbps-settings', //Settings section content prints out under the bb-press forum settings
				);

		foreach ($admin_files as $file){
			include(BBPS_ADMIN_PATH . $file .'.php');
		}
	}

	//includes folder
	$include_files = array(
			'bbps-common-functions',		// common functions used through the plugin
			'bbps-support-functions',		//functions relating the the update and edit of the topic status
			'bbps-vote-functions',		//functions relating the the update and edit of the topic status
			'bbps-core-options',			//sets up the core options
			'bbps-user-ranking-functions', // contains functions relating to the user ranking
			'bbps-premium-forum'			//functions relating to the premium restricted forums
			);

	foreach ($include_files as $file){
		include_once(BBPS_INCLUDES_PATH.$file. '.php');
	}

	//widgets folder
		$widget_files = array(
			'bbps-forum-hours-widget',	//forum hours widget - display the opening hour of your forum
			'bbps-resolved-count-widget', //resolved topic count
			'bbps-urgent-topics-widget', //shows a list of urgent topics to forum mods and admin
			'bbps-recently-resolved-widget', //shows a list of recently resolved topics
			'bbps-claimed-topics-widget', //show a list of topics claimed by the user
			'bbps-register-widget', // ajax regiser widget (dtbaker)
		);
	foreach ($widget_files as $file){
		include_once(BBPS_WIDGETS_PATH.$file. '.php');
	}

}

/*
* WEBMAN EDITED CODE STARTS HERE (2)
* www.webmandesign.eu
*/
/*
Load the CSS here
*/
function bbps_stylesheet() {
	// Load theme stylesheet
	wp_enqueue_style( 'bbps-style.css', BBPS_TEMPLATES_URL.'/css/bbps-style.css' , false, '', 'screen' );
}
add_action( 'wp_enqueue_scripts', 'bbps_stylesheet' );
/*
* WEBMAN EDITED CODE ENDS HERE (2)
* www.webmandesign.eu
*/

//register the widgets

add_action('widgets_init', 'bbps_register_widgets');

function bbps_register_widgets(){
	register_widget('bbps_support_hours_widget');
	register_widget('bbps_support_resolved_count_widget');
	register_widget('bbps_support_urgent_topics_widget');
	register_widget('bbps_support_recently_resolved_widget');
	register_widget('bbps_claimed_topics_widget');
	register_widget('bbps_support_register_widget');
}



//Replace the user registration welcome email
if ( !function_exists('wp_new_user_notification') ) :
/**
 * Notify the blog admin of a new user, normally via email.
 *
 * @since 2.0
 *
 * @param int $user_id User ID
 * @param string $plaintext_pass Optional. The user's plaintext password
 */
function wp_new_user_notification($user_id, $plaintext_pass = '') {
	global $LoginWithAjax;

	//Copied out of /wp-includes/pluggable.php
	$user = new WP_User($user_id);

	$user_login = stripslashes($user->user_login);
	$user_email = stripslashes($user->user_email);

	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$message  = sprintf(__('New user registration on your blog %s:'), $blogname) . "\r\n\r\n";
	$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
	$message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";

	@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);

	if ( empty($plaintext_pass) )
		return;

	// dtbaker!
    $message = get_option('_bbps_notification_message');
    $message = str_replace('%USERNAME%', $user_login, $message);
    $message = str_replace('%PASSWORD%', $plaintext_pass, $message);
    $message = str_replace('%BLOGNAME%', $blogname, $message);
    $message = str_replace('%BLOGURL%', get_bloginfo('wpurl'), $message);

    $subject = get_option('_bbps_notification_subject');
    $subject = str_replace('%BLOGNAME%', $blogname, $subject);
    $subject = str_replace('%BLOGURL%', get_bloginfo('wpurl'), $subject);

    wp_mail($user_email, $subject, $message);
}
endif;