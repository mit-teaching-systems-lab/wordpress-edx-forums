<?php

/*
 * Plugin Name: IMS Basic Learning Tools Interoperability
 * @name Load Blog Type 
 * @abstract Processes incoming requests for IMS Basic LTI and apply wordpress with blogType parametrer. This code is developed based on Chuck Severance code
 * @author Chuck Severance 
 * @author Antoni Bertran (abertranb@uoc.edu)
 * @copyright 2010-2012 Universitat Oberta de Catalunya
 * @license GPL
 * Date December 2010
*/

require_once( ABSPATH . 'wp-config.php' );

require_once( ABSPATH . WPINC . '/ms-functions.php');
require_once( ABSPATH . WPINC . '/ms-load.php');
require_once( ABSPATH  . '/wp-admin/includes/plugin.php');
require_once( ABSPATH  . '/wp-admin/includes/bookmark.php');

//require_once dirname(__FILE__).'/IMSBasicLTI/uoc-blti/bltiUocWrapper.php';
require_once dirname(__FILE__).'/blogType/blogTypeLoader.php';
require_once dirname(__FILE__).'/LTI_Tool_Provider/LTI_Tool_Provider.php';

require_once dirname(__FILE__).'/blogType/Constants.php';
require_once dirname(__FILE__).'/blogType/utils/UtilsPropertiesWP.php';

function lti_parse_request($wp){
	if ( is_basic_lti_request() ) {
		global $wpdb;
		
		$secret = lti_get_secret_from_consumer_key();
		$consumer_key = $_POST[ 'oauth_consumer_key' ] ;
		
		get_lti_hash(); // initialise the remote login hash

		$consumer = new LTI_Tool_Consumer($consumer_key);
		$tool = new LTI_Tool_Provider('lti_do_actions', $consumer->getDataConnector());
    $tool->execute();
	}
}
// Returns true if this is a Basic LTI message
// with minimum values to meet the protocol
function is_basic_lti_request() {
	$good_message_type = isset($_REQUEST["lti_message_type"]) && $_REQUEST["lti_message_type"] == "basic-lti-launch-request";
	$good_lti_version = isset($_REQUEST["lti_version"]) && $_REQUEST["lti_version"] == "LTI-1p0";
	if ($good_message_type and $good_lti_version and isset($_REQUEST["resource_link_id"]) ) return(true);
	return false;
}
function lti_do_actions($tool_provider) {
	// Insert code here to handle incoming connections - use the user
	// and resource_link properties of the $tool_provider parameter
	// to access the current user and resource link.
	// Get consumer key
	$consumer_key = $tool_provider->consumer->getKey();
  
	// Get user ID
	$user_id = $tool_provider->user->getId();
	
	$blogType = new blogTypeLoader($tool_provider->resource_link->getSetting('custom_blogtype', 'defaultType'));

	if ($blogType->error<0) {
		wp_die("BASIC LTI loading Types Aula Failed ".$blogType->error_miss);
		return ;
	}
	
	//TODO Get the list of users
	$resource_link = $tool_provider->resource_link;
	if (false) {
		//$users = $resource_link->doMembershipsService();
		//TODO test save grade
		$user = $tool_provider->user;
		//In Moodle we need to delete the dobre quote
		$result_sourcedid = str_replace('\"','"',$user->lti_result_sourcedid);
		
		$outcome = new LTI_Outcome($result_sourcedid);
		$score = 0.66;
		$outcome->setValue($score);
		$ok = $resource_link->doOutcomesService(LTI_Resource_Link::EXT_WRITE, $outcome);
		/*if (!$ok) {
			var_dump($_POST);
			var_dump($outcome);
			die();
		}*/
		//TODO read result
		$outcome_read = new LTI_Outcome($result_sourcedid);
		if ($score = $resource_link->doOutcomesService(LTI_Resource_Link::EXT_READ, $outcome_read )) {
			//Doesn't work not retrieve the score
			//$score = $outcome_read->getValue();
		}
	}
	
	
	// Set up the user...
	$userkey = getUserkeyLTI($tool_provider);
	
	$userkey = apply_filters('pre_user_login', $userkey);
	$userkey = trim($userkey);
	if ( empty($userkey) )
	wp_die('<p>Empty username</p><p>Cannot create a user without username</p>' );
	
	
	$uinfo = get_user_by('login', $userkey);
	$og_old_user_meta = null;
	if(isset($uinfo) && $uinfo!=false)
	{//CHANGED WHY DO THIS INSERT
		/*$ret_id = wp_insert_user(array(
	             'ID' => $uinfo->ID,
	             'user_login' => $userkey,
	             'user_nicename'=> $tool_provider->user->fullname,
	        	 'first_name'=> $tool_provider->user->firstname,
	        	 'last_name'=> $tool_provider->user->lastname,
	             'user_email'=> $tool_provider->user->email,
	             'user_url' => 'http://a',
	             'display_name' => $tool_provider->user->fullname,
	             'role' => get_option('default_role')
		));
		if (is_object($ret_id) && isset($ret_id->errors)){
			$msg = '';
			foreach ($ret_id->errors as $key => $error){
				$msg .= "<p><b>$key</b> ";
				foreach($error as $erroMsg){
					$msg .= "<p> $erroMsg</p>";
				}
				$msg .= "</p>";
			}
			wp_die($msg);
		} */
		// og : get the user's capabilities before they get removed, so we can restore them later 
		$og_old_user_meta = get_user_meta($uinfo->ID, 'wp_capabilities', true);
		// error_log("og caps before:".print_r(og_old_user_meta, true));
	}
	else
	{ // new user!!!!
	// og LTI: set the user_login and user_nicename to the same value,
        // , because we want the wordpress-login cookie to have the username
        // otherwise caching won't work properly!
	//$trim_name = trim($tool_provider->user->fullname);
	$trim_name =  str_replace(' ', '', $tool_provider->user->fullname);
	$ogmydefaultrole = get_option('default_role');
	//error_log("og uinfo:". $uinfo);
	//error_log("og LTI user fullname:". $tool_provider->user->fullname);
	$ret_id = wp_insert_user(array(
	             'user_login' => $userkey,
	             'user_nicename'=>  $userkey,//$trim_name,  //##CHANGED TRIM
	        	 'first_name'=> $tool_provider->user->firstname,
	        	 'last_name'=> $tool_provider->user->lastname,
	             'user_email'=> $tool_provider->user->email,//trim($tool_provider->user->fullname).'@edx.org',//##CHANGED $tool_provider->user->email,
	             'user_url' => 'http://edx.org',
	             'display_name' => $userkey //$tool_provider->user->fullname
	) );
	if (is_object($ret_id) && isset($ret_id->errors)){
		$msg = '';
		foreach ($ret_id->errors as $key => $error){
			$msg .= "<p><b>$key</b> ";
			foreach($error as $erroMsg){
				$msg .= "<p> $erroMsg</p>";
			}
			$msg .= "</p>";
		}
		wp_die($msg);
	}
	$uinfo = get_user_by('login', $userkey);
	}
	
	//Eliminem del blog Principal (si no es admin) http://jira.uoc.edu/jira/browse/BLOGA-218
	if (!$is_admin){
		//error_log("removing user caps");
		$user = new WP_User($uinfo->ID);
		//error_log("og LTI uinfo: ".print_r($uinfo, true));
		$user->remove_all_caps();
	}
	
	$_SERVER['REMOTE_USER'] = $userkey;
	$password = md5($uinfo->user_pass);
	
	// User is now authorized; force WordPress to use the generated password
	//login, set cookies, and set current user
	wp_authenticate($userkey, $password);
	wp_set_auth_cookie($user->ID, false);
	wp_set_current_user($user->ID, $userkey);
	//// og hack: I added wp_og_LTI_login in order to get wordpress to update the user specific cookie during LTI login
	//do_action("wp_og_LTI_login", $user->user_login, $user);
	// og: make sure the site works for http and https
	$mysiteurl = get_option("siteurl");
	$httpsstr = "https://";
	$httpstr = "http://";
	$siteurlsubstrpos = strlen($httpstr);
	if(substr($mysiteurl, 0, strlen($httpsstr))==$httpsstr){
		$siteurlsubstrpos = strlen($httpsstr);
	}
	$siteUrl = substr( get_option("siteurl"), $siteurlsubstrpos); // - "http://"
	//$siteUrl = substr( get_option("siteurl"), 7); // - "http://"
	$siteUrlArray = explode("/", $siteUrl);
	$domain = $siteUrlArray[0];
	unset($siteUrlArray[0]);
	//error_log("og LTI domain: ". $domain);
	
	$course = $blogType->getCoursePath($tool_provider, $siteUrlArray, $domain);
	// Get resource link ID
	$context_id = $tool_provider->resource_link->getId();
	if (isset($context_id)) {
		$course .= '-'.	$context_id;
	}
	
	$course = sanitize_user($course, true);
	//Bug wordpress doesn't get stye sheet if has a dot
	$course = str_replace('.','_',$course);
	###CHANGED
        ##$path_base = "/".implode("/",$siteUrlArray)."/".$course;
        $path_base = "/".implode("/",$siteUrlArray);
	$path_base = str_replace('//','/',$path_base);
	$path = $path_base."/";
	$path = str_replace('//','/',$path);
	
	$blog_created = false;
	$overwrite_plugins_theme = $tool_provider->resource_link->getSetting(OVERWRITE_PLUGINS_THEME, false);
	$overwrite_roles = $tool_provider->resource_link->getSetting(OVERWRITE_ROLES,false);
	
	$blog_id=domain_exists($domain, $path);
	$blog_is_new  = false;
	if ( ! isset($blog_id) ) {
		$title = __("Blog ").$blogType->getCourseName($tool_provider);
		$blog_is_new  = true;
	
		$meta = $blogType->getMetaBlog($tool_provider);
		$old_site_language = get_site_option( 'WPLANG');
		$blogType->setLanguage($tool_provider);
		$blog_id = wpmu_create_blog($domain, $path, $title, $user_id, $meta);
		update_site_option( 'WPLANG', $old_site_language );
		$blogType->checkErrorCreatingBlog($blog_id, $path);
		$blog_created = true;
	}
	
	// Connect the user to the blog
	if ( isset($blog_id) ) {
		 
		switch_to_blog($blog_id);
		ob_start();
		if ($overwrite_plugins_theme || $blog_created) {
			$blogType->loadPlugins();
			$blogType->changeTheme();
		}
		//Agafem el rol anterior
		$old_role = null;
		if (!$blog_created && !$overwrite_roles) {
			//$old_role_array = get_usermeta($user->id, 'wp_'.$blog_id.'_capabilities');
			$old_role_array = get_user_meta($user->id, 'wp_capabilities', true);
			//error_log("og caps:".print_r($old_role_array, true));
			if (is_array($values) || is_object($values)) {
				if (count($old_role_array)>0) {
					foreach ($old_role_array as $key => $value) {
						if ($value==true) {
							$old_role = $key;
						}
					}
				}
			}
		}
		if(count($uinfo->role>1)){
			//error_log("og setting old role again ".print_r($uinfo,1));
			$old_role = $uinfo->role[1];
		}
		else{
			$obj->role=get_option('default_role');
		} 
		//error_log("og old role before:".$old_role);
		remove_user_from_blog ($uinfo->ID, $blog_id);
		$obj = new stdClass();
		$obj->blog_id = $blog_id;
		$obj->userkey = $userkey;
		$obj->path_base = $path_base;
		$obj->domain = $domain;
		$obj->context = $tool_provider;
		$obj->uinfoID = $uinfo->ID;
		$obj->blog_is_new = $blog_is_new;
		if ($overwrite_roles || $old_role==null ) {
			$obj->role = $blogType->roleMapping($tool_provider->resource_link->getSetting(FIELD_ROLE_UOC_CAMPUS), $tool_provider);
		} else {
			$obj->role = $old_role;
		}

		//error_log("og old role:".$obj->role);
		$obj->role=get_option('default_role'); // oritgigo: added
		$blogType->postActions($obj);
		add_user_to_blog($blog_id, $uinfo->ID, $obj->role);
		//Si posem el restore_current_blog ens va al principi
		//    	restore_current_blog();
		ob_end_clean();
		// og : update the user's capabilities back to what they were before the changes were made
		// This code fixes a bug where the user's capabilities would reset to participant each
		// time they logged in. This code prevents this from happening by restoring the user's roles
		// after the reset happens. 
		if($og_old_user_meta != null){
			update_user_meta($uinfo->ID, 'wp_capabilities', $og_old_user_meta);
			//error_log("og caps after:".print_r($og_old_user_meta, true));
		}
		 
	}
	
	// og hack: I added wp_og_LTI_login in order to get wordpress to update the user specific cookie during LTI login
        do_action("wp_og_LTI_login", $user->user_login, $user, $og_old_user_meta);
	$redirecturl = get_option("siteurl");
	//error_log("og LTI redirect URL: ".$redirecturl);
	$redirecturl = str_replace("http://","https://", $redirecturl);
	//error_log("og LTI new redirect URL: ".$redirecturl);
	//error_log("og LTI request: ". print_r($_REQUEST,true));

	///// Deep linking based on the custom argument "myredirect" (= url)
	// wp-content/mu-plugins/LTI_Tool_Provider/LTI_Tool_Provider.php
	// http://edx.readthedocs.org/projects/edx-partner-course-staff/en/latest/exercises_tools/lti_component.html
	// taken from code in this file
	$mynewredirect =  $tool_provider->resource_link->getSetting("custom_myredirect");
	// error_log("og lti redirect ".$mynewredirect);	
	if($mynewredirect!=""){
		$redirecturl = $mynewredirect;
	}

	wp_redirect($redirecturl.'/');
	exit();
	
	
}
function lti_parse_request_OLD($wp) {
    if ( ! is_basic_lti_request() ) { 

    	$good_message_type = $_REQUEST[LTI_MESSAGE_TYPE] == LTI_MESSAGE_TYPE_VALUE;
    	$good_lti_version = $_REQUEST[LTI_VERSION] == LTI_VERSION_VALUE;
    	$resource_link_id = $_REQUEST[RESOURCE_LINK_ID];
    	if ($good_message_type && $good_lti_version && !isset($resource_link_id) ) {
    		$launch_presentation_return_url = $_REQUEST[LAUNCH_PRESENTATION_URL];
    		if (isset($launch_presentation_return_url)) {
    			header('Location: '.$launch_presentation_return_url);
    			exit();
    		}
    	}
    	return;
    }
    // See if we get a context, do not set session, do not redirect
    $secret = lti_get_secret_from_consumer_key();
    $context = new bltiUocWrapper(false, false, null, $secret);
    if ( ! $context->valid ) {
    	//var_dump($_POST);
    	echo "<hr>OAuthUtil::urldecode_rfc3986('%2B') ".OAuthUtil::urldecode_rfc3986('%2B')."<br>";
    	echo "<hr>OAuthUtil::urldecode_rfc3986('%5C') ".OAuthUtil::urldecode_rfc3986('%5C')."<br>";
    	 
        wp_die("BASIC LTI Authentication Failed, not valid request (make sure that consumer is authorized and secret is correct) ".$context->message);
        return;
    }
    $error=is_lti_error_data($context);
    if ($error!==FALSE) {
		$launch_presentation_return_url = $_REQUEST[LAUNCH_PRESENTATION_URL];
    	if (isset($launch_presentation_return_url)) {
    		$error = '<p>'.$error.'</p><p>Return to site <a href="'.$launch_presentation_return_url.'">'.$launch_presentation_return_url.'</a></p>';
    	}
    	wp_die($error,'');
    }
    
    $blogType = new blogTypeLoader($context);
    
    if ($blogType->error<0) {
       wp_die("BASIC LTI loading Types Aula Failed ".$blogType->error_miss);
       return ;
    }

    // Set up the user...
    $userkey = getUserkeyLTI($context);
    
    $userkey = apply_filters('pre_user_login', $userkey);
    $userkey = trim($userkey);
    
    if ( empty($userkey) )
    	wp_die('<p>Empty username</p><p>Cannot create a user without username</p>' );
    
  
    $uinfo = get_user_by('login', $userkey);
    if(isset($uinfo) && $uinfo!=false) 
    {
	// og LTI: set the user_login and user_nicename to the same value,
	// , because we want the wordpress-login cookie to have the username
	// otherwise caching won't work properly!
        $ret_id = wp_insert_user(array(
             'ID' => $uinfo->ID,
             'user_login' => $userkey,
             'user_nicename'=> $userkey,//$context->getUserName(),
        	 'first_name'=> $context->getUserFirstName(),
        	 'last_name'=> $context->getUserLastName(),
             'user_email'=> $context->getUserEmail(),
             'user_url' => 'http://b',
             'display_name' => $context->getUserName(),
             'role' => get_option('default_role')
          ));
	//error_log("og old role is set");
    	if (is_object($ret_id) && isset($ret_id->errors)){
    		$msg = '';
    		foreach ($ret_id->errors as $key => $error){
    			$msg .= "<p><b>$key</b> ";
    			foreach($error as $erroMsg){
    				$msg .= "<p> $erroMsg</p>";
    			}
    			$msg .= "</p>";
    		}
        	wp_die($msg);
        }
    }
    else
    { // new user!!!!
        $ret_id = wp_insert_user(array(
             'user_login' => $userkey,
             'user_nicename'=> $context->getUserName(),
             'first_name'=> $context->getUserFirstName(),
        	 'last_name'=> $context->getUserLastName(),
             'user_email'=> $context->getUserEmail(),
             'user_url' => 'http://c',
             'display_name' => $context->getUserName(),
             ) );
    	if (is_object($ret_id) && isset($ret_id->errors)){
    		$msg = '';
    		foreach ($ret_id->errors as $key => $error){
    			$msg .= "<p><b>$key</b> ";
    			foreach($error as $erroMsg){
    				$msg .= "<p> $erroMsg</p>";
    			}
    			$msg .= "</p>";
    		}
        	wp_die($msg);
        }
        $uinfo = get_user_by('login', $userkey);
    }

    //Eliminem del blog Principal (si no es admin) http://jira.uoc.edu/jira/browse/BLOGA-218
    if (!$is_admin){
    	$user = new WP_User($uinfo->ID);
		$user->remove_all_caps();
    }
    
    $_SERVER['REMOTE_USER'] = $userkey;
    $password = md5($uinfo->user_pass);
  
    // User is now authorized; force WordPress to use the generated password
    //login, set cookies, and set current user
    wp_authenticate($userkey, $password);
    wp_set_auth_cookie($user->ID, false);
    wp_set_current_user($user->ID, $userkey);
    $siteUrl = substr( get_option("siteurl"), 7); // - "http://"
    $siteUrlArray = explode("/", $siteUrl);
    $domain = $siteUrlArray[0];
    unset($siteUrlArray[0]);
    //error_log("og LTI domain: ". $domain);
    
    $course = $blogType->getCoursePath($context, $siteUrlArray, $domain);
    if (isset($context->info[RESOURCE_LINK_ID]) && $context->info[RESOURCE_LINK_ID]) {
    	$course .= '-'.	$context->info[RESOURCE_LINK_ID];
    }
    
    $course = sanitize_user($course, true);
    //Bug wordpress doesn't get stye sheet if has a dot
    $course = str_replace('.','_',$course);

    $path_base = "/".implode("/",$siteUrlArray)."/".$course;
    $path_base = str_replace('//','/',$path_base);
    $path = $path_base."/";
	$path = str_replace('//','/',$path);
    
    $blog_created = false;
    $overwrite_plugins_theme = isset($context->info[OVERWRITE_PLUGINS_THEME])?$context->info[OVERWRITE_PLUGINS_THEME]==1:false;
    $overwrite_roles = isset($context->info[OVERWRITE_ROLES])?$context->info[OVERWRITE_ROLES]==1:false;
    
    $blog_id=domain_exists($domain, $path);
    $blog_is_new  = false;
    if ( ! isset($blog_id) ) {
        $title = __("Blog ").$blogType->getCourseName($context);
    	$blog_is_new  = true;

        $meta = $blogType->getMetaBlog($context);
        $old_site_language = get_site_option( 'WPLANG');
        $blogType->setLanguage($context);
        $blog_id = wpmu_create_blog($domain, $path, $title, $user_id, $meta);
        update_site_option( 'WPLANG', $old_site_language );
		$blogType->checkErrorCreatingBlog($blog_id, $path);
		$blog_created = true;
   }

    // Connect the user to the blog
    if ( isset($blog_id) ) {
    	
    	switch_to_blog($blog_id);
    	ob_start();
    	if ($overwrite_plugins_theme || $blog_created) {
    		$blogType->loadPlugins();
	    	$blogType->changeTheme();
    	}
    	//Agafem el rol anterior 
    	$old_role = null;
    	if (!$blog_created && !$overwrite_roles) {
    		$old_role_array = get_usermeta($user->id, 'wp_'.$blog_id.'_capabilities');
    		if (count($old_role_array)>0) {
    			foreach ($old_role_array as $key => $value) {
    				if ($value==true) {
    					$old_role = $key;
    				}
    			}
    		}
    	}
    	remove_user_from_blog ($uinfo->ID, $blog_id); 
    	$obj = new stdClass();
    	$obj->blog_id = $blog_id;
    	$obj->userkey = $userkey;
    	$obj->path_base = $path_base;
    	$obj->domain = $domain;
    	$obj->context = $context;
    	$obj->uinfoID = $uinfo->ID;
    	$obj->blog_is_new = $blog_is_new;
    	if ($overwrite_roles || $old_role==null ) {
    		$obj->role = $blogType->roleMapping($context->info[FIELD_ROLE_UOC_CAMPUS], $context->info);
    	} else {
    		$obj->role = $old_role;
    	}
    	$blogType->postActions($obj);
    	add_user_to_blog($blog_id, $uinfo->ID, $obj->role);
		//Si posem el restore_current_blog ens va al principi
    	//    	restore_current_blog();
    	ob_end_clean();	
    	
    }
    
    $redirecturl = get_option("siteurl");
    //error_log("og LTI redirect URL: ".$redirecturl);
    $redirecturl = str_replace("http://","https://", $redirecturl);
    //error_log("og LTI new redirect URL: ".$redirecturl);
    wp_redirect($redirecturl);
    exit();
}

add_filter('parse_request', 'lti_parse_request');

/**
 * 
 * Gets the registered the parameter custom_userkey or standar userkey 
 * @param BLTI $context
 */
function getUserkeyLTI($tool_provider) {
	// og changed from global to id only...
	// http://www.spvsoftwareproducts.com/php/lti_tool_provider/phpdocs/classes/LTI_Tool_Provider.html
	$userkey = $tool_provider->user->getId(LTI_Tool_Provider::ID_SCOPE_ID_ONLY);
	//$userkey = $tool_provider->user->getId(LTI_Tool_Provider::ID_SCOPE_GLOBAL);
	//error_log("og LTI userkey: ".$userkey);
	$username_param = lti_get_username_parameter_from_consumer_key();
	if (isset($username_param) && $username_param && strlen($username_param)>0) {
		$userkey = $tool_provider->resource_link->getSetting($username_param, $userkey);
	}
	$userkey = str_replace(':','-',$userkey);  // TO make it past sanitize_user
	$userkey = sanitize_user($userkey);
	$userkey = trim($userkey);
	return $userkey;
}
/**
*
* Check if there is any error
* @param unknown_type $context
* @return boolean
*/
function  is_lti_error_data($tool_provider){
	$error = false;
	if (!isset($context->info[CONTEXT_ID]) || strlen($context->info[CONTEXT_ID])==0) {
		$error = "Error: lti context_id is needed. Contact with the administrator of LMS.";
	}
	else {
		$userkey = getUserkeyLTI($tool_provider);
		if ( empty($userkey) )
			$error = 'Error: Empty username. Cannot create a user without username';
	}
	return $error;
}

function lti_get_secret_from_consumer_key() {
	global $wpdb;
	lti_maybe_create_db();
	$secret = null;
	$consumer_key = $_POST[ 'oauth_consumer_key' ] ;
	$row = $wpdb->get_row( $wpdb->prepare( "SELECT secret FROM {$wpdb->ltitable} WHERE consumer_key = %s and enabled='1'", $consumer_key ) );
	if (isset($row) && isset($row->secret))
		$secret = $row->secret; 
	return $secret;
}
/**
 * 
 * get the parameter to get the username if is needed
 * @return String
 */
function lti_get_username_parameter_from_consumer_key() {
	global $wpdb;
	lti_maybe_create_db();
	$custom_username_parameter = null;
	$consumer_key = $_POST[ 'oauth_consumer_key' ] ;
	$row = $wpdb->get_row( $wpdb->prepare( "SELECT custom_username_parameter FROM {$wpdb->ltitable} WHERE consumer_key = %s and has_custom_username_parameter='1'", $consumer_key ) );
	if (isset($row) && isset($row->custom_username_parameter)) {
		$custom_username_parameter = $row->custom_username_parameter;
	}
	return $custom_username_parameter;
}

function lti_consumer_keys_admin() {
	global $wpdb, $current_site;
	if ( false == lti_site_admin() ) {
		return false;
	}

	switch( $_POST[ 'action' ] ) {
		default:
	}
	lti_maybe_create_db();
	$is_editing = false;
	echo '<h2>' . __( 'LTI: Consumers Keys', 'wordpress-mu-lti' ) . '</h2>';
	if ( !empty( $_POST[ 'action' ] ) ) {
		check_admin_referer( 'lti' );
		$consumer_key = strtolower( $_POST[ 'consumer_key' ] );
		switch( $_POST[ 'action' ] ) {
			case "edit":
				$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->ltitable} WHERE consumer_key = %s", $consumer_key ) );
				if ( $row ) {
					lti_edit( $row );
					$is_editing = true;
				} else {
					echo "<h3>" . __( 'Provider not found', 'wordpress-mu-lti' ) . "</h3>";
				}
				break;
			case "save":
				$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->ltitable} WHERE consumer_key = %s", $consumer_key ) );
				if ( $row ) {
					$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->ltitable} SET  consumer_name = %s, name = %s, secret = %s, enabled = %d, lti_version = %s, custom_username_parameter = %s, has_custom_username_parameter = %d  WHERE consumer_key = %s", $_POST[ 'consumer_name' ], $_POST[ 'name' ], $_POST[ 'secret' ], $_POST[ 'enabled' ], $_POST[ 'lti_version' ], $_POST[ 'custom_username_parameter' ], $_POST[ 'has_custom_username_parameter' ], $consumer_key ) );
					echo "<p><strong>" . __( 'Provider Updated', 'wordpress-mu-lti' ) . "</strong></p>";
				} else {
					$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->ltitable} ( `consumer_name`, `name`, `consumer_key`, `secret`, `enabled`, `lti_version`, `custom_username_parameter`, `has_custom_username_parameter`) VALUES ( %s, %s, %s, %s, %d, %s, %s, %d)", $_POST[ 'consumer_name' ], $_POST[ 'name' ], $consumer_key, $_POST[ 'secret' ], $_POST[ 'enabled' ], $_POST[ 'lti_version' ], $_POST[ 'custom_username_parameter' ], $_POST[ 'has_custom_username_parameter' ] ) );
					echo "<p><strong>" . __( 'Provider Added', 'wordpress-mu-lti' ) . "</strong></p>";
				}
				break;
			case "del":
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->ltitable} WHERE consumer_key = %s", $consumer_key ) );
				echo "<p><strong>" . __( 'Provider Deleted', 'wordpress-mu-lti' ) . "</strong></p>";
				break;
		}
	}

	if (!$is_editing) {
		echo "<h3>" . __( 'Search', 'wordpress-mu-lti' ) . "</h3>";
		$escaped_search = addslashes($_POST['search_txt']);
		$rows = $wpdb->get_results( "SELECT * FROM {$wpdb->ltitable} WHERE consumer_key LIKE '%{$escaped_search}%' OR consumer_name LIKE '%{$escaped_search}%'" );
		lti_listing( $rows, sprintf( __( "Searching for %s", 'wordpress-mu-lti' ), esc_html(  $_POST[ 'search' ] ) ) );
		echo '<form method="POST">';
		wp_nonce_field( 'lti' );
		echo '<input type="hidden" name="action" value="search" />';
		echo '<p>';
		echo _e( "Search:", 'wordpress-mu-lti' );
		echo " <input type='text' name='search_txt' value='' /></p>";
		echo "<p><input type='submit' class='button-secondary' value='" . __( 'Search', 'wordpress-mu-lti' ) . "' /></p>";
		echo "</form><br />";
		lti_edit();
		$rows = $wpdb->get_results( "SELECT * FROM {$wpdb->ltitable} LIMIT 0,20" );
		lti_listing( $rows );
	}
}


function lti_edit( $row = false ) {
	$is_new = false;
	if ( is_object( $row ) ) {
		echo "<h3>" . __( 'Edit LTI', 'wordpress-mu-lti' ) . "</h3>";
	}  else {
		echo "<h3>" . __( 'New LTI', 'wordpress-mu-lti' ) . "</h3>";
		$row = new stdClass();
		$row->consumer_name = '';
		$row->name = '';
		$row->consumer_key = '';
		$row->lti_version = '';
		$row->secret = '';
		$row->enabled = 1;
		$row->has_custom_username_parameter = 0;
		$row->custom_username_parameter = '';
		$is_new = true;
	}

	echo "<form method='POST'><input type='hidden' name='action' value='save' />";
	wp_nonce_field( 'lti' );
	echo "<table class='form-table'>\n";
	echo "<tr><th>" . __( 'Name', 'wordpress-mu-lti' ) . "</th><td><input type='text' name='name' value='{$row->name}' /></td></tr>\n";
	echo "<tr><th>" . __( 'Consumer name', 'wordpress-mu-lti' ) . "</th><td><input type='text' name='consumer_name' value='{$row->consumer_name}' /></td></tr>\n";
	echo "<tr><th>" . __( 'Consumer key', 'wordpress-mu-lti' ) . "</th><td><input type='text' name='consumer_key' value='{$row->consumer_key}' ".(!$is_new?'readonly="readonly"':'')."/></td></tr>\n";
	echo "<tr><th>" . __( 'Secret', 'wordpress-mu-lti' ) . "</th><td><input type='text' name='secret' value='{$row->secret}' /></td></tr>\n";
	echo "<tr><th>" . __( 'LTI Version', 'wordpress-mu-lti' ) . "</th><td><select name='lti_version'><option value='LTI-1p0' ".($row->lti_version=='LTI-1p0'?'selected':'').">LTI-1p0</option><option value='LTI-2p0' ".($row->lti_version=='LTI-2p0'?'selected':'').">LTI-2p0</option></select></td></tr>\n";
	
	/*echo "<tr><th>" . __( 'Consumer guid', 'wordpress-mu-lti' ) . "</th><td><input type='text' name='consumer_guid' value='{$row->consumer_guid}' /></td></tr>\n";
	*/
	echo "<tr><th>" . __( 'Custom username parameter', 'wordpress-mu-lti' ) . "</th><td><input type='text' name='custom_username_parameter' value='{$row->custom_username_parameter}' /></td></tr>\n";
	
	
	echo "<tr><th>" . __( 'Has custom username', 'wordpress-mu-lti' ) . "</th><td><input type='checkbox' name='has_custom_username_parameter' value='1' ";
	
	echo $row->has_custom_username_parameter == 1 ? 'checked=1 ' : ' ';
	echo "/></td></tr>\n";
	echo "<tr><th>" . __( 'Enabled', 'wordpress-mu-lti' ) . "</th><td><input type='checkbox' name='enabled' value='1' ";
	echo $row->enabled == 1 ? 'checked=1 ' : ' ';
	echo "/></td></tr>\n";
	echo "</table>";
	echo "<p><input type='submit' class='button-primary' value='" .__( 'Save', 'wordpress-mu-lti' ). "' /></p></form><br /><br />";
}


function lti_network_warning() {
	echo "<div id='lti-warning' class='updated fade'><p><strong>".__( 'LTI Disabled.', 'lti_network_warning' )."</strong> ".sprintf(__('You must <a href="%1$s">create a network</a> for it to work.', 'wordpress-mu-lti' ), "http://codex.wordpress.org/Create_A_Network")."</p></div>";
}

/*function lti_add_pages() {
	global $current_site, $wpdb, $wp_db_version, $wp_version;

	if ( !isset( $current_site ) && $wp_db_version >= 15260 ) {
		// WP 3.0 network hasn't been configured
		add_action('admin_notices', 'lti_network_warning');
		return false;
	}

	if ( lti_site_admin() && version_compare( $wp_version, '3.0.9', '<=' ) ) {
		if ( version_compare( $wp_version, '3.0.1', '<=' ) ) {
			add_submenu_page('wpmu-admin.php', __( 'LTI Consumer Keys', 'wordpress-mu-lti' ), __( 'LTI Consumer Keys', 'wordpress-mu-lti'), 'manage_options', 'lti_admin_page', 'lti_admin_page');
		} else {
			add_submenu_page('ms-admin.php', __( 'LTI Consumer Keys', 'wordpress-mu-lti' ), 'LTI Consumer Keys', 'manage_options', 'lti_admin_page', 'lti_admin_page');
		}
	}
}
add_action( 'admin_menu', 'lti_add_pages' );*/


function lti_network_pages() {
	add_submenu_page('settings.php', 'LTI Consumers Keys', 'LTI Consumers Keys', 'manage_options', 'lti_consumer_keys_admin', 'lti_consumer_keys_admin');
}
add_action( 'network_admin_menu', 'lti_network_pages' );

function get_lti_hash() {
	$remote_login_hash = get_site_option( 'lti_hash' );
	if ( null == $remote_login_hash ) {
		$remote_login_hash = md5( time() );
		update_site_option( 'lti_hash', $remote_login_hash );
	}
	return $remote_login_hash;
}

/**
 * 
 * Create table to store the consumers ands passwords if not exists
 */
function lti_maybe_create_db() {
	global $wpdb;

	get_lti_hash(); // initialise the remote login hash

	$wpdb->ltitable = $wpdb->base_prefix . 'lti_consumer';
	if ( lti_site_admin() ) {
		$created = 0;
		if ( $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->ltitable}'") != $wpdb->ltitable ) {
			$wpdb->query( "CREATE TABLE IF NOT EXISTS `{$wpdb->ltitable}` (
				consumer_key varchar(255) NOT NULL,
				  name varchar(45) NOT NULL,
				  secret varchar(32) NOT NULL,
				  lti_version varchar(12) DEFAULT NULL,
				  consumer_name varchar(255) DEFAULT NULL,
				  consumer_version varchar(255) DEFAULT NULL,
				  consumer_guid varchar(255) DEFAULT NULL,
				  css_path varchar(255) DEFAULT NULL,
				  protected tinyint(1) NOT NULL,
				  enabled tinyint(1) NOT NULL,
				  enable_from datetime DEFAULT NULL,
				  enable_until datetime DEFAULT NULL,
				  last_access date DEFAULT NULL,
				  custom_username_parameter varchar(255) DEFAULT NULL,
				  has_custom_username_parameter decimal(1,0) default 0,
				  created datetime NOT NULL,
				  updated datetime NOT NULL,
				  PRIMARY KEY (consumer_key)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;" );
			$wpdb->query( "CREATE TABLE IF NOT EXISTS `".$wpdb->base_prefix."lti_context` (
						  consumer_key varchar(255) NOT NULL,
						  context_id varchar(255) NOT NULL,
						  lti_context_id varchar(255) DEFAULT NULL,
						  lti_resource_id varchar(255) DEFAULT NULL,
						  title varchar(255) NOT NULL,
						  settings text,
						  primary_consumer_key varchar(255) DEFAULT NULL,
						  primary_context_id varchar(255) DEFAULT NULL,
						  share_approved tinyint(1) DEFAULT NULL,
						  created datetime NOT NULL,
						  updated datetime NOT NULL,
						  PRIMARY KEY (consumer_key, context_id)
						) ENGINE=InnoDB DEFAULT CHARSET=latin1;" );
			$wpdb->query( "CREATE TABLE IF NOT EXISTS `".$wpdb->base_prefix."lti_user` (
						  consumer_key varchar(255) NOT NULL,
						  context_id varchar(255) NOT NULL,
						  user_id varchar(255) NOT NULL,
						  lti_result_sourcedid varchar(255) NOT NULL,
						  created datetime NOT NULL,
						  updated datetime NOT NULL,
						  PRIMARY KEY (consumer_key, context_id, user_id)
						) ENGINE=InnoDB DEFAULT CHARSET=latin1;" );
			
			$wpdb->query( "CREATE TABLE IF NOT EXISTS `".$wpdb->base_prefix."lti_nonce` (
						  consumer_key varchar(255) NOT NULL,
						  value varchar(32) NOT NULL,
						  expires datetime NOT NULL,
						  PRIMARY KEY (consumer_key, value)
						) ENGINE=InnoDB DEFAULT CHARSET=latin1;" );
			
			$wpdb->query( "CREATE TABLE IF NOT EXISTS `".$wpdb->base_prefix."lti_share_key` (
						  share_key_id varchar(32) NOT NULL,
						  primary_consumer_key varchar(255) NOT NULL,
						  primary_context_id varchar(255) NOT NULL,
						  auto_approve tinyint(1) NOT NULL,
						  expires datetime NOT NULL,
						  PRIMARY KEY (share_key_id)
						) ENGINE=InnoDB DEFAULT CHARSET=latin1;" );
			
			$wpdb->query( "ALTER TABLE ".$wpdb->base_prefix."lti_context
  ADD CONSTRAINT ".$wpdb->base_prefix."lti_context_consumer_FK1 FOREIGN KEY (consumer_key)
   REFERENCES ".$wpdb->base_prefix."lti_consumer (consumer_key);" );
			$wpdb->query( "ALTER TABLE ".$wpdb->base_prefix."lti_context
  ADD CONSTRAINT ".$wpdb->base_prefix."lti_context_context_FK1 FOREIGN KEY (primary_consumer_key, primary_context_id)
   REFERENCES ".$wpdb->base_prefix."lti_context (consumer_key, context_id);" );
			$wpdb->query( "ALTER TABLE ".$wpdb->base_prefix."lti_user
  ADD CONSTRAINT ".$wpdb->base_prefix."lti_user_context_FK1 FOREIGN KEY (consumer_key, context_id)
   REFERENCES ".$wpdb->base_prefix."lti_context (consumer_key, context_id);" );
			$wpdb->query( "ALTER TABLE ".$wpdb->base_prefix."lti_nonce
  ADD CONSTRAINT ".$wpdb->base_prefix."lti_nonce_consumer_FK1 FOREIGN KEY (consumer_key)
   REFERENCES ".$wpdb->base_prefix."lti_consumer (consumer_key);" );
			$wpdb->query( "ALTER TABLE ".$wpdb->base_prefix."lti_share_key
  ADD CONSTRAINT ".$wpdb->base_prefix."lti_share_key_context_FK1 FOREIGN KEY (primary_consumer_key, primary_context_id)
   REFERENCES ".$wpdb->base_prefix."lti_context (consumer_key, context_id);" );
			
			$created = 1;
		}
		if ( $created ) {
			?> <div id="message" class="updated fade"><p><strong><?php _e( 'LTI database tables created.', 'wordpress-mu-lti' ) ?></strong></p></div> <?php
		}
	}

}

/**
 * 
 * Check if current user is admin
 */
function lti_site_admin() {
	if ( function_exists( 'is_super_admin' ) ) {
		return is_super_admin();
	} elseif ( function_exists( 'is_site_admin' ) ) {
		return is_site_admin();
	} else {
		return true;
	}
}

function lti_listing( $rows, $heading = '' ) {
	if ( $rows ) {
		if ( file_exists( ABSPATH . 'wp-admin/network/site-info.php' ) ) {
			$edit_url = network_admin_url( 'site-info.php' );
		} elseif ( file_exists( ABSPATH . 'wp-admin/ms-sites.php' ) ) {
			$edit_url = admin_url( 'ms-sites.php' );
		} else {
			$edit_url = admin_url( 'wpmu-blogs.php' );
		}
		if ( $heading != '' )
			echo "<h3>$heading</h3>";
		echo '<table class="widefat" cellspacing="0"><thead><tr><th>'.__( 'Consumer name', 'wordpress-mu-lti' ).'</th><th>'.__( 'Consumer key', 'wordpress-mu-lti' ).'</th><th>'.__( 'LTI Version', 'wordpress-mu-lti' ).'</th><th>'.__( 'Enabled', 'wordpress-mu-lti' ).'</th><th>'.__( 'Edit', 'wordpress-mu-lti' ).'</th><th>'.__( 'Delete', 'wordpress-mu-lti' ).'</th></tr></thead><tbody>';
		foreach( $rows as $row ) {
			echo "<tr><td>{$row->consumer_name}</td>";
			echo "<td>{$row->consumer_key}</td>";
			//echo $row->has_custom_username_parameter == 1 ? __( 'Yes',  'wordpress-mu-lti' ) : __( 'No',  'wordpress-mu-lti' );
			echo "<td>";
			//echo $row->custom_username_parameter;
			echo $row->lti_version;
			echo "</td><td>";
			echo $row->enabled == 1 ? __( 'Yes',  'wordpress-mu-lti' ) : __( 'No',  'wordpress-mu-lti' );
			echo "</td><td><form method='POST'><input type='hidden' name='action' value='edit' /><input type='hidden' name='consumer_key' value='{$row->consumer_key}' />";
			wp_nonce_field( 'lti' );
			echo "<input type='submit' class='button-secondary' value='" .__( 'Edit', 'wordpress-mu-lti' ). "' /></form></td><td><form method='POST'><input type='hidden' name='action' value='del' /><input type='hidden' name='consumer_key' value='{$row->consumer_key}' />";
				wp_nonce_field( 'lti' );
				echo "<input type='submit' class='button-secondary' value='" .__( 'Del', 'wordpress-mu-lti' ). "' /></form>";
				echo "</td></tr>";
			}
		echo '</table>';
	}
}
?>
