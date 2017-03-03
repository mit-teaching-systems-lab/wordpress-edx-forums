<?php //Opening PHP tag

// Add reporting for missing image assets.
// Requires jQuery and Rollbar to be set, otherwise logs to console.
//
// This only works after the initial page load, not for images that are
// added to the page later dynamically.
function report_missing_image_assets() {
  echo '<script type="text/javascript">
    function report(messageText, data) {
      if (window.Rollbar) {
        window.Rollbar.error(messageText, data)
      } else {
        console.warn(messageText, data);
      }
    }

    function reportFailedLoad(e) {
      report("Detected that image failed to load: " + e.src);
    }

    // Defer checking, to allow the page to load.
    //
    // Report errors on images that have already failed to load,
    // and listen for errors on images that are still loading.
    function reportImageLoadFailures() {
      setTimeout(function() {
        window.jQuery("img").toArray().forEach(function(el) {
          if (el.naturalWidth === 0 && el.naturalHeight === 0) {
            report("Found image that failed to load: " + el.src, { src: el.src });
          } else {
            window.jQuery(el).on("error", reportFailedLoad);
          }
        });      
      }, 10000);
    }

    // reportImageLoadFailures();
  </script>';
}
add_action('wp_head', 'report_missing_image_assets' );


add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {

	if (!current_user_can('administrator') && !is_admin()) {

 	 show_admin_bar(false);

	}

}



// Colin's changes
/*
if( !function_exists('dwqa_wp_knowledge_base_scripts') ){

    function dwqa_wp_knowledge_base_scripts(){

        wp_enqueue_style( 'dw-wp-knowledge-base-qa', get_stylesheet_directory_uri() . '/dwqa-templates/style.css' );

    }

 add_action( 'wp_enqueue_scripts', 'dwqa_wp_knowledge_base_scripts' );

}
*/

function kbc_forum_folder_desc() {
  $content = bbp_get_forum_content();
  if($content != '') {
     echo '<div id="desc-box">';
     echo $content;
     echo '</div>';
  }
}
add_action('bbp_template_before_single_forum', 'kbc_forum_folder_desc');
 
function kbc_bp_groups_message() {
  $descTxt = "Join an existing group or create a new group to participate in group discussions. Groups administrators have control over who can join and participate in the Groups forums.";
  // wp-content/plugins/buddypress/bp-groups/bp-groups-template.php
  // wp-content/plugins/buddypress/bp-core/bp-core-filters.php
  $workinggroupcreationbutton = bp_get_button(array(
                        'id'         => 'create_working_group',
                        'component'  => 'groups',
                        'link_text'  => __( 'Sign up for a Working Group', 'buddypress' ),
                        'link_title' => __( 'Sign up for a Working Group', 'buddypress' ),
                        'link_class' => 'button group-create bp-title-button',
                        'link_href'  => trailingslashit( bp_get_root_domain() ) . "working-group-signup-survey/",
                        'wrapper'    => false,
                )
	);
  echo '<div id="desc-box">' . $descTxt . '</div>';
}
add_action('bp_before_directory_groups_content', 'kbc_bp_groups_message', 1);
 
function kbc_bp_members_message() {
  $descTxt = "Find and connect with members who are using these forums.";
  echo '<div id="desc-box">' . $descTxt . '</div>';
}
add_action('bp_before_directory_members_page', 'kbc_bp_members_message', 1);

function kbc_remove_feature_requests()
{
  remove_action('bbp_template_before_topics_loop', 'dtbaker_vote_bbp_template_before_topics_loop');
}
add_action('after_setup_theme', 'kbc_remove_feature_requests');

function kbc_edx_login_redirect() {
  if(!strpos($_SERVER["REQUEST_URI"], 'admin-login')) {
    //http://stackoverflow.com/questions/7921229/how-do-i-read-values-from-wp-config-php
    // The edX url is set in the config file wp-config-basics.php 
    header("Location: ".MY_EDX_URL);
    exit;
  }
}
add_action('login_enqueue_scripts', 'kbc_edx_login_redirect');

/* Fix issue with Support Forums plugin
 * - can only see subscriptions to topics you start 
 */
function kbc_remove_author_lock() {
  remove_filter('bbp_has_topics_query','bbps_lock_to_author');
}
add_action('bbp_template_before_user_subscriptions', 'kbc_remove_author_lock');

function kbc_re_add_author_lock() {
  add_filter('bbp_has_topics_query','bbps_lock_to_author');
}
add_action('bbp_template_after_user_subscriptions', 'kbc_re_add_author_lock');

/* Remove forced sorting by votes on voting forums, to allow also sorting by date or replies */
remove_filter('bbp_after_has_topics_parse_args','bbps_filter_bbp_after_has_topics_parse_args',10);

/* If the list of topics is sorted, make sure the paginated links keep it sorted */
function kbc_pagination_links ($links) {
  $order_index = strpos($_SERVER['REQUEST_URI'], '?order');
  if($order_index !== false) {
    $uri_query = substr($_SERVER['REQUEST_URI'], $order_index);
    return $links.$uri_query;
  } else {
    return $links;
  }
}
add_filter('paginate_links', 'kbc_pagination_links', 100, 1);

// End Colin's changes


// Taken from http://wordpress.stackexchange.com/questions/74742/how-to-set-different-cookies-for-logged-in-admin-users-and-logge$
function set_admin_specific_cookie($user_login, $user, $userroles=NULL){
  // http://wordpress.stackexchange.com/questions/43528/how-to-get-a-buddypress-user-profile-link-and-a-certain-user-profile-field-for-t
  setcookie('uname',$user->user_login,0,'/');

  // $userroles gets passed in from LTI because $user->roles is often not set yet when this function is called from LTI. 
  // $user->roles is properly set when this function is called from admin-login
  $my_uroles = $userroles;
  if(is_null($my_uroles)){
    $my_uroles = $user->roles;
  } 
  if(user_can($user,'administrator')||(array_key_exists('administrator',$my_uroles))||(lti_site_admin())||(array_key_exists('bbp_moderator',$my_uroles))||(array_key_exists('bbp_keymaster',$my_uroles))||(array_key_exists('bbp_blocked',$my_uroles))){
    // error_log("oritgigo admin or bbp moderator");
    if(!isset($_COOKIE['disable_my_cache'])){
      // error_log("oritgigo empty cookie");
      setcookie('disable_my_cache',1,0,'/');
    }
  } else {
    // If the user is not an admin and the disable_my_cache cookie is there, remove it!
    if(isset($_COOKIE['disable_my_cache'])){
      // error_log("The user is not an admin, oritgigo clear existing cookie: disable_my_cache ".is_admin());
      setcookie('disable_my_cache',0, time()-3600,'/');
      unset($_COOKIE['disable_my_cache']);
    }
  }
}

function clear_admin_specific_cookie(){
        // error_log("oritgigo clear cache cookie");
        if(isset($_COOKIE['uname'])){
                setcookie('uname',$user->ID,time()-3600,'/');
		unset($_COOKIE['uname']);
        }
        if(isset($_COOKIE['disable_my_cache'])){
             	error_log("oritgigo clear existing cookie");
		//http://www.w3schools.com/php/php_cookies.asp
		setcookie('disable_my_cache',0, time()-3600,'/');
             	unset($_COOKIE['disable_my_cache']);
        }
}

//function set_bpdomain_cookie(){
//	error_log("domain:".bp_loggedin_user_domain( '/' ));
//}

// error_log("oritgigo here");

// see http://codex.wordpress.org/Plugin_API/Action_Reference/wp_login
// the last two arguments (10 and 2) enable set_admin_specific_cookie to get the use arguments
add_action('wp_login', 'set_admin_specific_cookie', 10,2);
add_action('wp_og_LTI_login', 'set_admin_specific_cookie', 10,3);
add_action('wp_logout', 'clear_admin_specific_cookie');
//add_action('bp_loaded', 'set_bpdomain_cookie');

function my_disable_page_cache($mypageurl){
	setrawcookie('disable_my_page_cache',$mypageurl, time()+65, $mypageurl);
}

function my_reply_update_handler(){
	//error_log("og reply id:".$_POST['bbp_reply_id']);
	//error_log("og redirect:".bbp_get_redirect_to());
	error_log("page uri:".$_SERVER['REQUEST_URI']);
	//$myreplyid = (int) $_POST['bbp_reply_id'];
	//$myredirect = bbp_get_redirect_to();
	//$myreplyurl = bbp_get_reply_url($myreplyid,$myredirect);
	//$myreplyurlpart=preg_replace("/([^\/]+$)/","",preg_replace("/^https?:\/\/[^\/]+(\/*.+\/).*/", " $1 ",$myreplyurl));
	//error_log("og reply url:".home_url());
	//error_log("og reply uri:".$myreplyurlpart);
	//if(!isset($_COOKIE['disable_my_page_cache'])){
	//setrawcookie('disable_my_page_cache',$_SERVER['REQUEST_URI'], time()+180);
	// Temporarily (until the cache times out) disable the cache for this page for this user,
	// since they just poste and we want them to be able to see their posts. 
	my_disable_page_cache($_SERVER['REQUEST_URI']);
	error_log("page uri:".$_SERVER['REQUEST_URI']);
	//}
}

function my_reply_edit_handler(){
	//error_log("og reply id:".$_POST['bbp_reply_id']);
        //error_log("og redirect:".bbp_get_redirect_to());
        //error_log("page uri:".$_SERVER['REQUEST_URI']);
	// Edit replies do not have the url that we don't want to cache (unlike new posts). 
	// In order to disable the cache for the editing user for this topic, we need to obtain
	// its url
	// Get the post id
        $myreplyid = (int) $_POST['bbp_reply_id'];
	// Get the full redirect url
        $myredirect = bbp_get_redirect_to();
        $myreplyurl = bbp_get_reply_url($myreplyid,$myredirect);
	// Get rid of the extras (such as the domain name and the #post123)
        $myreplyurlpart=trim(preg_replace("/([^\/]+$)/","",preg_replace("/^https?:\/\/[^\/]+(\/*.+\/).*/", " $1 ",$myreplyurl)));
        //error_log("og reply url:".home_url());
        error_log("og reply uri:".$myreplyurlpart);
	my_disable_page_cache($myreplyurlpart);
	error_log("og reply uri:".$myreplyurlpart);
}
function my_new_topic_handler(){
        //error_log("og reply id:".$_POST['bbp_reply_id']);
        //error_log("og redirect:".bbp_get_redirect_to());
        error_log("page uri:".$_SERVER['REQUEST_URI']);
        //$myreplyid = (int) $_POST['bbp_reply_id'];
        //$myredirect = bbp_get_redirect_to();
        //$myreplyurl = bbp_get_reply_url($myreplyid,$myredirect);
        //$myreplyurlpart=preg_replace("/([^\/]+$)/","",preg_replace("/^https?:\/\/[^\/]+(\/*.+\/).*/", " $1 ",$myreplyurl));
        //error_log("og reply url:".home_url());
        //error_log("og reply uri:".$myreplyurlpart);
        //if(!isset($_COOKIE['disable_my_page_cache'])){
        //setrawcookie('disable_my_page_cache',$_SERVER['REQUEST_URI'], time()+180);
        // Temporarily (until the cache times out) disable the cache for this page for this user,
        // since they just poste and we want them to be able to see their posts. 
        my_disable_page_cache($_SERVER['REQUEST_URI']);
        error_log("page uri:".$_SERVER['REQUEST_URI']);
        //}
}

function my_topic_edit_handler(){
        //error_log("og reply id:".$_POST['bbp_reply_id']);
        //error_log("og redirect:".bbp_get_redirect_to());
        //error_log("page uri:".$_SERVER['REQUEST_URI']);
        // Edit replies do not have the url that we don't want to cache (unlike new posts). 
        // In order to disable the cache for the editing user for this topic, we need to obtain
        // its url
        // Get the post id
        $mytopicid = (int) $_POST['bbp_topic_id'];
        // Get the full redirect url
        $myredirect = bbp_get_redirect_to();
        $myreplyurl = bbp_get_topic_permalink($mytopicid);
	$myforumid = bbp_get_topic_forum_id($mytopicid);
	error_log("topic url: ".$myreplyurl." og topic id: ".$_POST['bbp_topic_id']." og forumid: ".$myforumid);

        // Get rid of the extras (such as the domain name and the #post123)
        $myreplyurlpart=trim(preg_replace("/([^\/]+$)/","",preg_replace("/^https?:\/\/[^\/]+(\/*.+\/).*/", " $1 ",$myreplyurl)));
        $myforumurl = bbp_get_forum_permalink($myforumid);
	$myforumurlpart=trim(preg_replace("/([^\/]+$)/","",preg_replace("/^https?:\/\/[^\/]+(\/*.+\/).*/", " $1 ",$myforumurl)));
	//error_log("og reply url:".home_url());
	// Don't cache the topic so that the user can see their updated content and title
        error_log("og topic uri:".$myreplyurlpart);
        my_disable_page_cache($myreplyurlpart);
        error_log("og topic uri:".$myreplyurlpart);
	error_log("og forum uri:".$myforumurlpart);
	// Don't cache the forum so that the user can see their updated title
	my_disable_page_cache($myreplyurlpart);
}

// Create a cookie when the user posts a reply. This is done in order to prevent the cache from getting a cached version of the page for this specific user.
add_action('bbp_edit_reply', 'my_reply_edit_handler');
add_action('bbp_new_reply', 'my_reply_update_handler');
add_action('bbp_new_topic', 'my_new_topic_handler');
add_action('bbp_edit_topic', 'my_topic_edit_handler');

function my_group_creation_handler(){
	// Create a cookie that will tell varnish not to cache the group directory for this user for the next x minutes, so the user can see the group they just created.
	// error_log("group url:".bp_get_groups_directory_permalink());
	$mygroupdir = trim(preg_replace("/([^\/]+$)/","",preg_replace("/^https?:\/\/[^\/]+(\/*.+\/).*/", " $1 ",bp_get_groups_directory_permalink())));
	// error_log("group uri:".$mygroupdir);
	setrawcookie('disable_my_page_cache',$mygroupdir, time()+65, $mygroupdir);
}
add_action('groups_group_create_complete', 'my_group_creation_handler');


// Parent override
// For more information see http://www.paulund.co.uk/override-parent-theme-functions
function ipt_kb_bbp_forum_freshness_in_list( $forum_id = 0 ) {
        $og_forum_last_topic_id = bbp_get_forum_last_topic_id($forum_id);
        $og_last_topic_id = bbp_get_topic_last_active_id($og_forum_last_topic_id);
        $author_link = bbp_get_author_link( array(
                'post_id' => $og_last_topic_id,
                'type' => 'name'
        ) );
        $freshness = bbp_get_author_link( array( 'post_id' => $og_last_topic_id, 'size' => 32, 'type' => 'avatar' ) );
        ?>
<?php if ( ! empty( $freshness ) ) : ?>
<span class="pull-left thumbnail">
        <?php echo $freshness; ?>
</span>
<?php endif; ?>
<?php do_action( 'bbp_theme_before_forum_freshness_link' ); ?>
<ul class="list-unstyled ipt_kb_forum_freshness_meta">
        <li class="bbp-topic-freshness-link"><?php echo bbp_topic_freshness_link($og_forum_last_topic_id); ?>  </li>
        <li class="bbp-topic-freshness-author">
                <?php do_action( 'bbp_theme_before_topic_author' ); ?>
                <?php if ( ! empty( $author_link ) ) printf( __( 'by %s', 'ipt_kb' ), $author_link ); ?>
                <?php do_action( 'bbp_theme_after_topic_author' ); ?>
        </li>
</ul>
<?php do_action( 'bbp_theme_after_forum_freshness_link' ); ?>
        <?php
}


// https://buddypress.org/support/topic/removing-menus-not-working-in-buddypress-1-5-help-please/
//function ja_remove_navigation_tabs() {
//	global $bp;
//	//remove_action('groups_custom_create_steps', array( $this, 'maybe_create_screen' ));
// 	$bp->groups->group_creation_steps['forum']=null;
	//bp_core_remove_subnav_item(buddypress()->groups->group_creation_steps->slug,'forum');
//	bp_core_remove_subnav_item( $bp->groups->slug, 'forum' );
//}
//add_action( 'groups_custom_create_steps', 'ja_remove_navigation_tabs', 25 );

// When the group has a forum, set the group page's default tab to 'forum'. For groups that don't have a forum, the 'home' tab will be the default.
// Some of this code was copied from https://buddypress.org/support/topic/bp_groups_default_extension/
function bbg_set_group_default_extension( $ext ) {
	global $bp;
	// error_log("og extension".print_r($ext,true));
	// error_log("og extension2".print_r($bp->groups->current_group,true));
	// error_log("og extension3".print_r($bp->active_components,true)." test");
	//if ( $bp->groups->current_group->enable_forum && bp_is_active( 'forums' ) ){
	// Mystery: based on bp_is_active, forums are disabled, but the group menu is still displayed
	if ( $bp->groups->current_group->enable_forum){
		// error_log("og extension forums");
		return 'forum';
	}
	else{
		return $ext;
	}
}
add_filter( 'bp_groups_default_extension', 'bbg_set_group_default_extension' );

# Breadcrumbs
# This function channges the "Forums" breadcrumb link for non admin users in order to avoid confusion. When a non-admin user sees a link named "forums", they assume that it
# points to the root of the forums (the home page). The default "Forums" breadcrumb points to another page that lists all the forums. This page is very useful for admins,
# but it generally confuses non-admin users. As a result, we decided to make this page available for admins only and have non-admin users go to the homepage instead. 
# For more info about editing breadcrubs see https://bbpress.org/forums/topic/how-do-i-remove-first-two-parts-of-breadcrumb/
# and https://bbpress.org/forums/topic/how-do-i-edit-bbpress-breadcrumbs/
function my_filter_breadcrumbs($my_curret_crumbs){
	//error_log("og crumbs ".print_r($my_curret_crumbs,true));
	# Admins get the default forum page when clicking the "forum" breadcrub
	if(isset($_COOKIE['disable_my_cache'])){
		return $my_curret_crumbs;
	} # Non admins get the home page when clicking the "forum" breadcrub
	else{
		$my_breadcrumbhome = "/(.*a href=\")(.*)(\".*class=\"bbp-breadcrumb-home\".*)/";
		$my_breadcrumbroot = "/(.*a href=\")(.*)(\".*class=\"bbp-breadcrumb-root\".*)/";
		$my_breadcrumbhomeurl = "";
		for($i=0;$i<count($my_curret_crumbs);$i++){
			$my_breadcrumbhomematches = array();
			if(preg_match($my_breadcrumbhome,$my_curret_crumbs[$i],$my_breadcrumbhomematches)){
			if(count($my_breadcrumbhomematches>3)){
					$my_breadcrumbhomeurl = $my_breadcrumbhomematches[2];
					//error_log("og breadcrumbs found ".$my_breadcrumbhomeurl);
				}
			}
		}
		for($i=0;$i<count($my_curret_crumbs);$i++){
       	        	$my_breadcrumbrootmatches = array();
       	         	if(preg_match($my_breadcrumbroot,$my_curret_crumbs[$i],$my_breadcrumbrootmatches)){
				if(count($my_breadcrumbhomematches>4)){
       		                 	$my_curret_crumbs[$i] = $my_breadcrumbrootmatches[1].$my_breadcrumbhomeurl.$my_breadcrumbrootmatches[3];
					//error_log("og breadcrumbs ".$my_breadcrumbrootmatches[3].", ".$my_breadcrumbhomeurl.", ".$my_breadcrumbrootmatches[3]);
				}
                	}
        	}
	}

	return $my_curret_crumbs;
}
add_filter('bbp_breadcrumbs', 'my_filter_breadcrumbs');

/////////////// Group sorting
// How to add a sort option:
// 1. Add an option to the group directory's sort drop down. You can use the actions bp_groups_directory_order_options and bp_member_group_order_options to do this. 
// The functions og_num_posts_option and og_rank_option add sort options to the sort drop down. You can use these two examples as a guide.
// 2. Add your sort/metadata type to the $og_my_curr_types list in og_my_order_by_number_of_posts. This will work only if your meta type is numeric. If your meta type is not numeric,
// you'll have to add your own SQL code to og_my_order_by_number_of_posts.
// 3. Update your metadata type when needed, using some action or filter (e.g. in og_groups_update_num_posts_and_rank_options, right before an activity occurs)

// Taken from https://codex.buddypress.org/plugindev/group-meta-queries-usage-example/#filter-bp_ajax_querystring-to-eventually-extend-the-groups-query
// The code below adds the new sort options to the sort box

// Number of posts
// Add the number of posts option to the sort drop down list
function og_num_posts_option() {
    ?>
    <option value="og_num_posts"><?php _e( 'Number of Posts' ); ?></option>
    <?php
}
/* finally you create your options in the different select boxes */
// you need to do it for the Groups directory
add_action( 'bp_groups_directory_order_options', 'og_num_posts_option' );
// and for the groups tab of the user's profile
add_action( 'bp_member_group_order_options', 'og_num_posts_option' );

// Rank
// Add the rank option to the sort drop down list
function og_rank_option() {
    ?>
    <option value="og_rank"><?php _e( 'Rank' ); ?></option>
    <?php
}
/* finally you create your options in the different select boxes */
// you need to do it for the Groups directory
add_action( 'bp_groups_directory_order_options', 'og_rank_option' );
// and for the groups tab of the user's profile
add_action( 'bp_member_group_order_options', 'og_rank_option' );

// Additional sort options
// Currently none

// Code that updates and sorts the rank and number of posts 

// Taken from wp-content/plugins/buddypress/bp-groups/bp-groups-activity.php
// Update the group's rank and number of posts right before a group activity gets recorded
// Taken from wp-content/plugins/bbpress/includes/extend/buddypress/groups.php map_activity_to_group and wp-content/plugins/buddypress/bp-groups/bp-groups-activity.php
function og_groups_update_num_posts_and_rank_options($args = array() ){
        // wp-content/plugins/buddypress/bp-groups/bp-groups-forums.php
        //error_log("og group post count here ".print_r($args,true));
        //echo "og here";
        $group_id = 0;
        $og_my_postcount = 0;
        //error_log("og group post count here");
	$group = groups_get_current_group();

	//Taken from wp-content/plugins/bbpress/includes/extend/buddypress/groups.php
        // Not posting from a BuddyPress group? stop now!
        if ( !empty( $group ) ) {
                $group_id = $group->id; //bp_get_current_group_id(); //$bp->groups->current_group->id;
                error_log("og group post count id ".$group_id);
	}
	else{
		return $args;
        }
       
        //Taken from wp-content/plugins/bbpress/includes/extend/buddypress/groups.php
        $my_forum_ids = bbp_get_group_forum_ids( $my_group_id );
        $forum_id = null;
        // Get the first forum ID
        if ( !empty( $my_forum_ids ) ) {
               $forum_id = (int) is_array( $my_forum_ids ) ? $my_forum_ids[0] : $my_forum_ids;
               $og_my_postcount = bbp_show_lead_topic() ? bbp_get_forum_reply_count($forum_id) : bbp_get_forum_post_count($forum_id);
        }
	
	// Update the group's post count
        //error_log("og group post count ".$og_my_postcount);
        groups_update_groupmeta( $group_id, 'og_num_posts', $og_my_postcount );
	// Taken from p-content/plugins/buddypress/bp-groups/bp-groups-forums.php
	// Update the group's rank, based on its previous rank
	$og_rank_arg = 'og_rank';
	// Get the previous rank
	$og_prev_grp_rank = groups_get_groupmeta($group_id, $og_rank_arg);
	//error_log("og group post rank ".empty($og_prev_grp_rank));
	// If the rank doesn't exist yet, make it 0
	if(empty($og_prev_grp_rank==null)){
		$og_prev_grp_rank = 0;
	}
	// Update the rank as follows: rank = .7*prev rank + .3*current unix time
	// groups_update_groupmeta .5*og_rank+.5*lastactivitytimeinunix
	groups_update_groupmeta($group_id, $og_rank_arg, .7*$og_prev_grp_rank + .3*microtime(true));

	return $args;
}
add_filter( 'bbp_before_record_activity_parse_args', 'og_groups_update_num_posts_and_rank_options' );

// Sort by rank or number of posts by modifying the SQL query
// Taken from https://codex.buddypress.org/plugindev/add-custom-filters-to-loops-and-enjoy-them-within-your-plugin/ and wp-content/plugins/buddypress/bp-groups/bp-groups-classes.php and from the actual sort values
function og_my_order_by_number_of_posts( $sql = '', $sql_arr = '',$args){
	//error_log("og og_my_order_by_most_favorited ".$sql.": ".print_r($sql_arr,true).": ".print_r($args,true));
	// If the curret sort type matches one of the items in the list, we sort by this type
	$og_my_curr_types = array("og_num_posts","og_rank"); // You can add your own numeric meta type to this list. If your meta type is not numeric you'll have to add your own SQL code to the code below.
	if(in_array($args["type"],$og_my_curr_types)){
		$og_my_curr_type = "";
		$og_idx = array_search($args["type"],$og_my_curr_types);
		$og_my_curr_type = $og_my_curr_types[$og_idx];

		// We need to change the SQL query to include our new meta types (otherwise it'll only include total_member_count and last_activity)
		// The original SQL query looks like this:
		// SELECT DISTINCT g.id, g.*, gm1.meta_value AS total_member_count, gm2.meta_value AS last_activity
		// FROM wp_bp_groups_groupmeta gm1, wp_bp_groups_groupmeta gm2, wp_bp_groups_members m, wp_bp_groups g 
		// WHERE g.id = m.group_id AND g.id = gm1.group_id AND g.id = gm2.group_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count'
		// AND m.user_id = 90 AND m.is_confirmed = 1 AND m.is_banned = 0
		// ORDER BY last_activity DESC
		// LIMIT 0, 20
		// gm1 is used to obtain the total member count and gm2 is used to get the last activity.
		// Here we add a 3rd gm from table from which we get the meta value of our meta type (rank or number of posts). We also make sure to add it
		// to the where clause, to select it and to order by it.  
		$sql_arr["select"]=$sql_arr["select"].", cast(gm3.meta_value as unsigned) AS ".$og_my_curr_type;
		$sql_arr["from"]=$sql_arr["from"]." wp_bp_groups_groupmeta gm3,";
		$sql_arr["where"]=$sql_arr["where"]." AND gm3.meta_key = '".$og_my_curr_type."'"." AND g.id = gm3.group_id"; 
		$sql_arr[0]="ORDER BY ".$og_my_curr_type." DESC";
		//error_log("og og_my_order_by_most_favorited sql:".join( ' ', (array) $sql_arr ));
		return  join( ' ', (array) $sql_arr );
	}
	else{
		return $sql;
	}
}
add_filter( 'bp_groups_get_paged_groups_sql', 'og_my_order_by_number_of_posts' ,     10, 6 );


// Forum - specific search:
///////////////////////////
// Taken from http://sevenspark.com/tutorials/how-to-search-a-single-forum-with-bbpress
//
// Additional resources:
// https://bbpress.org/forums/topic/forum-search-inside-buddypress-group/
// https://wordpress.org/support/topic/meta_query-does-not-filter-at-all
//https://wordpress.org/support/topic/meta_query-does-not-filter-at-all
// http://codex.bbpress.org/bbp_list_forums/
// https://bbpress.org/forums/topic/how-to-display-list-of-sub-forums-on-separate-lines-instead-of-big-blob/
///////////////////////////////////////////////////////////////////////////////////////
function my_bbp_search_form(){
    /*?>
    <div class="bbp-search-form">

        <?php bbp_get_template_part( 'form', 'search' ); ?>

    </div>
    <?php */
}
add_action( 'bbp_template_before_single_forum', 'my_bbp_search_form' );


/*
 * Search only a specific forum
* Taken from http://sevenspark.com/tutorials/how-to-search-a-single-forum-with-bbpress
 */
function my_bbp_filter_search_results( $r ){
        //error_log("og my_bbp_filter_search_results ".print_r($r, true));
    //Get the submitted forum ID (from the hidden field added in step 2)
    $forum_id = sanitize_title_for_query( $_GET['bbp_search_forum_id'] );
        if(!$forum_id){
                // http://sevenspark.com/tutorials/how-to-search-a-single-forum-with-bbpress
                $forum_id = bbpress()->current_forum_id;//bbp_get_forum_id();
        }
        //error_log("og my_bbp_filter_search_results jan 2016 forumid=".$forum_id." get: ". print_r($_GET,true));
        //https://wordpress.org/support/topic/meta_query-does-not-filter-at-all
        // http://codex.bbpress.org/bbp_list_forums/
        // https://bbpress.org/forums/topic/how-to-display-list-of-sub-forums-on-separate-lines-instead-of-big-blob/
        // https://wordpress.org/support/topic/how-to-get-top-level-parent-pages
        // https://bbpress.trac.wordpress.org/attachment/ticket/2303/forums-template-tags-4.patch%E2%80%8B
        // wp-content/plugins/bbpress/includes/forums/template.php
        // https://bbpress.org/forums/topic/my-customized-sub-sub-forums/
        // wp-content/themes/wp-knowledge-base/inc/bbpress.php
        // Colin's code in this file (above)
        // http://php.net/manual/en/function.array-push.php
        // http://codex.wordpress.org/Class_Reference/WP_Meta_Query
        // https://wordpress.org/support/topic/wp-multiple-meta_query-from-array
        // http://wordpress.stackexchange.com/questions/115608/meta-query-with-array-as-value-with-multiple-arrays
        // http://stackoverflow.com/questions/18401396/how-to-pass-array-into-meta-query-value-with-advanced-custom-fields
        // wp-content/themes/wp-knowledge-base/inc/bbpress.php
        // wp-content/plugins/bbpress/includes/common/functions.php
        // and the sources above
    //If the forum ID exits, filter the query
    if( $forum_id && is_numeric( $forum_id ) ){
        // Get the sub forums and add them to the query in order to search through them too, since the forum search IS NOT RECURSIVE
        //error_log("og bbp_forum_get_subforums ".print_r(function_exists('bbp_forum_get_subforums'),true));
        $ogmysubforums = bbp_forum_get_subforums("".$forum_id);
        //bbp_forum_get_subforums("".$forum_id);
        $mysearchforums = array($forum_id);

	// Pass $mycollectedsearchforums by reference, so we can add forum ids to it
	// http://php.net/manual/en/language.references.pass.php
	function my_recursive_findsubforums($ogmyrecsubforums, &$mycollectedsearchforums){
        	if ( ! empty( $ogmyrecsubforums ) ) {
               		foreach ( $ogmyrecsubforums as $sub_forum ) {
                        	error_log("og bbp_list_forums sub: ".print_r($sub_forum->ID,true));
                        	array_push($mycollectedsearchforums, $sub_forum->ID);
				// Find the subforums of the current sub forum
				my_recursive_findsubforums(bbp_forum_get_subforums("".$sub_forum->ID), $mycollectedsearchforums);
                	}
        	}
	}
	my_recursive_findsubforums($ogmysubforums, $mysearchforums);
        //error_log("og bbp_list_forums4: ".print_r($ogmysubforums,true)." ".print_r($mysearchforums,true));
	// https://github.com/ntwb/bbPress/blob/master/src/includes/forums/template.php
	// http://phpcrossref.com/xref/bbpress/includes/forums/template.php.html#l767
	// http://stackoverflow.com/questions/12778304/wordpress-custom-query-missing-two-posts
	//echo bbp_list_forums(array('forum_id' =>"".$forum_id));
        // Recursive (search the current forum and all its sub forums)
        $r['meta_query'] = array(
            array(
                'key' => '_bbp_forum_id',
                'value' => $mysearchforums,
                'compare' => 'IN',
		'post_type' => array('forum', 'topic', 'reply'),
            )
        );


        // Non-recursive (search teh current forum and don't search its sub forums)
        //$r['meta_query'] = array(
        //    array(
        //        'key' => '_bbp_forum_id',
        //        'value' => $forum_id,
        //        'compare' => '=',
        //    )
        //);

    }

    return $r;
}

add_filter( 'bbp_after_has_search_results_parse_args' , 'my_bbp_filter_search_results' );

// Add the forum id to the redirect (otherwise we won't have it in, because itgets lost in bbp_search_results_redirect)
// Taken from http://sevenspark.com/tutorials/how-to-search-a-single-forum-with-bbpress (including the comments)
// and 
// wp-content/plugins/bbpress/includes/search/template.php
// wp-content/plugins/bbpress/includes/search/template.php
// wp-content/plugins/bbpress/includes/search/template.php
// wp-content/plugins/bbpress/includes/search/functions.php
// wp-includes/pluggable.php wp_redirect and wp_safe_redirect
// other wordpress, buddypress and bbpress sourcecode..
//function my_bbp_get_search_results_url($mysearchurl){
//function my_bbp_search_results_pagination($args){
//function my_bbp_get_search_results_url($mysearchurl){
// my_bbp_search_results_redirect
// Don't let wp redirect to the search forum page without adding the forum id!
// We can't add bbp_search_forum_id in bbp_get_search_results_url because then pagination doesn't work (see bbp_has_search_results in
// wp-content/plugins/bbpress/includes/search/template.php for more information). Basically, bbp_get_search_results_url is called before 
// pagination gets added and if we add the forum search id before the pagination string then we'll get pagination link urls that look like this: 
// https://....../search/<search terms>?bbp_search_forum_id=<forum id>/page/<page id> meaning that pagination WILL NOT WORK
// By adding the bbp_search_forum_id argument here, we prevent this from happening while making sure to pass the bbp_search_forum_id to
// the search (the search will have access to the bbp_search_forum_id but it will not affect the pagination links, so we'll never get
// the bad url structure described above)
function my_wp_redirect($mysearchurl){
	$mynewsearchurl=$mysearchurl;
	//if(array_key_exists('base', $args)){
		//$mysearchurl  = $args['base'];
       		// error_log("og my_bbp_get_search_results_url ".$mysearchurl." ".print_r($_GET,true));
		//error_log("og bbp_search_results_pagination ".$mysearchurl." ".print_r($_GET,true)." ".print_r($args,true));
		// Make sure bbp_search_forum_id doesn't get lost during redirect to search results
		if(array_key_exists('bbp_search_forum_id', $_REQUEST)){
			// We only do this when redirecting to search results
			if(bbp_get_search_results_url()===$mynewsearchurl){
				$mynewsearchurl=$mysearchurl;	
				$mybbp_search_forum_id=$_REQUEST['bbp_search_forum_id'];
				if($mybbp_search_forum_id){
					// Sdd bbp_search_forum_id to the url we are redirecting to...
	          	     		$mynewsearchurl = add_query_arg(array('bbp_search_forum_id'=>$_GET['bbp_search_forum_id']),$mynewsearchurl);
				}
			}
		}
		//$args['base'] = $mynewsearchurl;
	//}
        return $mynewsearchurl;
}

// Add the bbp_search_forum_id to the pagination links (the right way, meaning AFTER the pagination string, /page/<page#>/)
function my_bbp_search_results_pagination($args){
	if(array_key_exists('bbp_search_forum_id', $_REQUEST)){
		// Copied from wp-content/plugins/bbpress/includes/search/template.php bbp_has_search_results
		// and 
		// wp-includes/general-template.php paginate_links
		$add_args = array();
		if(array_key_exists('add_args', $args)){
			$add_args = $args['add_args'];
		}
		$add_args['bbp_search_forum_id'] = $_REQUEST['bbp_search_forum_id'];
		$args['add_args'] = $add_args;
	}
	return $args;
}

//add_filter( 'bbp_get_search_results_url' , 'my_bbp_get_search_results_url' );
// https://codex.wordpress.org/Function_Reference/add_filter 
// add_filter( 'bbp_search_results_pagination' , 'my_bbp_search_results_pagination', 20, 1);
add_filter('wp_redirect', 'my_wp_redirect', 20, 1);
add_filter('bbp_search_results_pagination', 'my_bbp_search_results_pagination', 20, 1);

////////////////////////////////////////////////////////////////////////////////////////////////// 
// Mark as unread
// Uncomment this code if you want to add content to read or unread topics that are
// listed on the topic list page)
//////////////////////////////////////////////////////////////////////////////////////////////////

// http://codex.bbpress.org/bbp_theme_before_forum_freshness_link/
// http://codex.bbpress.org/bbp_theme_before_topic_author/
// http://etivite.com/api-hooks/bbpress/trigger/do_action/bbp_theme_before_topic_freshness_link/
//add_action( 'bbp_theme_before_topic_freshness_link', 'og_before_topic_freshness_link' );
/*function og_before_topic_freshness_link() {
	//wp-content/plugins/bbpress-mark-as-read/bbp-mark-as-read.php
	$unreadpluginobj = $GLOBALS['bbp_mark_as_read'];
	// https://bbpress.trac.wordpress.org/browser/tags/2.0/bbp-themes/bbp-twentyten/bbpress/loop-single-topic.php#L16
	// https://bbpress.org/forums/topic-tag/bbp_get_topic_id/
	$mytopicid =  bbp_get_topic_id();
	// http://codex.wordpress.org/Function_Reference/get_current_user_id
	$myuserid = get_current_user_id();
	if($unreadpluginobj && $mytopicid && $myuserid){
		$addedtopicclassclass = "readunread_topic "; 
		if($unreadpluginobj->is_read($myuserid,$mytopicid)){
			$addedtopicclassclass .= "read_topic";
		}
		else{
			$addedtopicclassclass .= "unread_topic";
		}
		echo "<div class='".$addedtopicclassclass."'>  </div>";	
	}
}*/


////////////////////////////////////////////////////////////////////////////////////////////////// 
// bbpress forum voting
// The functions below enable administrators to change the voting button's text per forum
// The default text is set to the button's original text "Vote for this!"
////////////////////////////////////////////////////////////////////////////////////////////////// 

// This function adds a text box to the admin's forum editing interface. This textbox contains the voting button's text for the forum $forum_id.
// We use the action bbp_forum_metabox to add this content (which is what the bbp forum plugin does)
//http://codex.wordpress.org/Function_Reference/add_action
// Taken and copied from wp-content/plugins/bbPress-Support-Forums-master/admin/bbps-admin.php bbps_extend_forum_attributes_mb
function my_bbps_extend_forum_attributes_mb($forum_id){
	echo "<p>";
	// http://codex.wordpress.org/Function_Reference/get_post_meta
	// http://codex.wordpress.org/Function_Reference/update_post_meta
	// Inspired by wp-content/plugins/bbPress-Support-Forums-master/includes/bbps-common-functions.php bbps_is_premium_forum
	$voting_forum_button_text = get_post_meta( $forum_id, '_bbps_my_voting_btn_txt', true);
	if(empty($voting_forum_button_text)){
		$voting_forum_button_text = "Vote for this!";
	}
	// We have to use 3 different echos. If you concatinate the strings instead using "." php will mess up the order.
	echo '<strong> '; echo _e( 'Voting Button Text:', 'bbps' ); echo ' </strong>';
	echo "<input type='text' id='votingbtntxt' name='votingbtntxt' value='".$voting_forum_button_text."'/>";
	echo "</p>";
	echo "<br />";
}

add_action('bbp_forum_metabox' , 'my_bbps_extend_forum_attributes_mb', 20, 1);

// This function saves the content of the "voting button text" textbox as the new voting button's text for the forum $forum_id.
// We use the action bbp_forum_attributes_metabox_save to save the content (which is what the bbp forum plugin does)
// Taken and copied from wp-content/plugins/bbPress-Support-Forums-master/admin/bbps-admin.php bbps_forum_attributes_mb_save
function my_bbps_forum_attributes_mb_save($forum_id){
	// Update the voting button's text for this forum
	// http://codex.wordpress.org/Function_Reference/get_post_meta
	$voting_forum_button_text = $_POST['votingbtntxt'];
	// Make sure that the button can be displayed properly
	if(empty($voting_forum_button_text)){
		$voting_forum_button_text = " ";
	}
	update_post_meta($forum_id, '_bbps_my_voting_btn_txt', $voting_forum_button_text);

	return $forum_id;
}

add_action( 'bbp_forum_attributes_metabox_save' , 'my_bbps_forum_attributes_mb_save', 20, 1);

// This function adds javascript code to the page that sets the text of the voting button to the text from the forum's config options
// I used JS here because there seemed to be no other way (other than possibly creating a script an using ...enqueue_script...) to change the button's text without
// changing the plugin's code. The plugin dod not provide filters or actions to override the button's text. It didn't even use translation functions to set the button's text,
// so I couldn't use translation events to override it.
// We use the action bbp_template_before_single_topic to add the code and make sure it only get added 
// to voting forums (which is similar to what the bbp forum plugin does when it creates the voting button)
// Taken and copied from wp-content/plugins/bbPress-Support-Forums-master/admin/bbps-admin.php bbps_add_voting_forum_features
function my_bbps_add_voting_forum_features(){
	$texttorteplace = "Vote for this!";
	$forum_id = bbp_get_forum_id();
	// Taken and copied from wp-content/plugins/bbPress-Support-Forums-master/admin/bbps-admin.php bbps_modify_vote_title
	if(bbps_is_voting_forum($forum_id)){
		// http://codex.wordpress.org/Function_Reference/get_post_meta
		$voting_forum_button_text = get_post_meta( $forum_id, '_bbps_my_voting_btn_txt', true);
		// Don't change the text if this setting hasn't been set yet 
		// (and when it gets set we always make sure it's not empty)
		// wp-content/plugins/bbPress-Support-Forums-master/admin/bbps-admin.php
		if(empty($voting_forum_button_text)){
			return;
		}
		//echo "abcd".print_r($voting_forum_button_text,true);
		// http://www.w3schools.com/tags/tag_script.asp
		// http://api.jquery.com/contains-selector/
		// http://stackoverflow.com/questions/2338439/select-element-based-on-exact-text-contents
		// http://learn.jquery.com/using-jquery-core/document-ready/
		echo "<script>";
		echo "jQuery( document ).ready( function(){ ";
		echo "jQuery(\"a:contains('".$texttorteplace."')\").each(function () {";
		echo "	if(jQuery(this).text()=='".$texttorteplace."'){";
		echo "		jQuery(this).text('".$voting_forum_button_text."');";
		echo "	}";
		echo "})";
		echo "});";
		echo "</script>";
	}	
}

add_action('bbp_template_before_single_topic', 'my_bbps_add_voting_forum_features');

////////////////////////////////////////////////////////////////////////////////////////////////// 
// User group preferences
// Used Plugin:
// https://github.com/fpcorso/quiz_master_next/
//////////////////////////////////////////////////////////////////////////////////////////////////

// Taken from http://php.net/manual/en/language.oop5.php and samples
// http://php.net/manual/en/language.oop5.magic.php
// http://php.net/manual/en/language.oop5.magic.php#object.tostring
class QuestionAnswer {
	protected $m_question;
	protected $m_answer;
	protected $m_score;
	protected $m_correctanswer;
	protected $m_usercomments;
	protected $m_correctanswerinfo;
	protected $m_points;
	protected $m_maxpoints;

	public function __construct($questionanswerarray, $questioninfoarray=array()){
		// Taken from https://github.com/fpcorso/quiz_master_next/blob/master/includes/qmn_template_variables.php
		$this->m_question = $questionanswerarray[0];
		$this->m_answer = $questionanswerarray[1];
		$this->m_correctanswer = $questionanswerarray[2];
		$this->m_usercomments = $questionanswerarray[3];
		$this->m_correctanswerinfo = $questionanswerarray['id'];
		$this->m_points = $questionanswerarray['points'];
		if($questioninfoarray && count($questioninfoarray)>0){
		// http://php.net/manual/en/function.array-map.php and example 2
			$getpoints = function(&$questionanswer){
				return $questionanswer[1];
			};
			$this->m_maxpoints = max(array_map($getpoints,$questioninfoarray));
		}
		else{
			$this->m_maxpoints=-1;
		}
	}

	public function getMaxPoints(){
                return $this->m_maxpoints;
        }

	public function setMaxPoints($maxPointsArg){
                $this->m_maxpoints = $maxPointsArg;
        }

	public function getQuestion(){
		return $this->m_question;
	}

	public function getAnswer(){
                return $this->m_answer;
        }
	
	public function getCorrectAnswer(){
                return $this->m_correctanswer;
        }
	
	public function getUserComments(){
                return $this->m_usercomments;
        }
	
	public function getCorrentAnswerInfo(){
                return $this->correctanswerinfo;
        }

	public function getPoints(){
                return $this->m_points;
        }

	// http://php.net/manual/en/language.oop5.magic.php#object.tostring
	// http://php.net/manual/en/language.oop5.magic.php#object.debuginfo
	public function __toString(){
		return "Question: ".$this->getQuestion()." Answer: ".$this->getAnswer(). " Points: ".$this->getPoints()." Max points:".$this->getMaxPoints();
	}

}

class UserResponse{
	protected $m_extrainfo;
	protected $m_info;
	protected $m_answers;	
	// Takes quiz response information
	public function __construct($mlw_quiz_info, $questioninfo, $extrainfo=null){
			// Taken from https://github.com/fpcorso/quiz_master_next/blob/master/includes/qmn_results_details.php					
	                // and https://github.com/fpcorso/quiz_master_next/blob/master/includes/qmn_results.php
			$mlw_qmn_results_array = @unserialize($mlw_quiz_info->quiz_results);
                	if (is_array($mlw_qmn_results_array)){
        	                // Taken from https://github.com/fpcorso/quiz_master_next/blob/master/includes/qmn_results_details.php
                	        // and https://github.com/fpcorso/quiz_master_next/blob/master/includes/qmn_template_variables.php
                                $qmn_array_for_variables = array(
                                        'quiz_id' => $mlw_quiz_info->quiz_id,
                                        'quiz_name' => $mlw_quiz_info->quiz_name,
                                        'quiz_system' => $mlw_quiz_info->quiz_system,
                                        'user_name' => $mlw_quiz_info->name,
                                        'user_business' => $mlw_quiz_info->business,
                                        'user_email' => $mlw_quiz_info->email,
                                        'user_phone' => $mlw_quiz_info->phone,
                                        'user_id' => $mlw_quiz_info->user,
                                        'timer' => $mlw_qmn_results_array[0],
                                        'total_points' => $mlw_quiz_info->point_score,
                                        'total_score' => $mlw_quiz_info->correct_score,
                                        'total_correct' => $mlw_quiz_info->correct,
                                        'total_questions' => $mlw_quiz_info->total,
                                        'comments' => $mlw_qmn_results_array[2],
                                        'question_answers_array' => $mlw_qmn_results_array[1]
                                );
				$this->m_info = $qmn_array_for_variables;
                                $myuseranswers = $qmn_array_for_variables['question_answers_array'];
				
                                // Inspired by qmn_array_for_variables above:
				// //https://github.com/fpcorso/quiz_master_next/blob/master/includes/qmn_quiz.php
				$this->m_answers = array(
                                        'number_of_hours'=> new QuestionAnswer($myuseranswers[0]),
					'time'=> new QuestionAnswer($myuseranswers[1],@unserialize($questioninfo[1]->answer_array)),
                                        'unit'=> new QuestionAnswer($myuseranswers[2],@unserialize($questioninfo[2]->answer_array)), 
                                        'game_design_background'=> new QuestionAnswer($myuseranswers[3], @unserialize($questioninfo[3]->answer_array)),
					'game_design_background_homogeneity'=> new QuestionAnswer($myuseranswers[4], @unserialize($questioninfo[4]->answer_array)),
                                        'tech_background'=> new QuestionAnswer($myuseranswers[5], @unserialize($questioninfo[5]->answer_array)),
					'tech_background_homogeneity'=> new QuestionAnswer($myuseranswers[6], @unserialize($questioninfo[6]->answer_array)),
                                        'experience_with_online_courses'=> new QuestionAnswer($myuseranswers[7], @unserialize($questioninfo[7]->answer_array)),
					'experience_with_online_courses_homogeneity'=> new QuestionAnswer($myuseranswers[8], @unserialize($questioninfo[8]->answer_array)),
					'education'=> new QuestionAnswer($myuseranswers[9], @unserialize($questioninfo[9]->answer_array)),
                                        'education_homogeneity'=> new QuestionAnswer($myuseranswers[10], @unserialize($questioninfo[10]->answer_array))
                                );
				//error_log("strange thing".print_r($myuseranswers[4],true));
                                /*$this->m_answers = array(
                                  /      'number_of_hours'=> new QuestionAnswer($myuseranswers[0]),
                                        'start_time'=> new QuestionAnswer($myuseranswers[1],@unserialize($questioninfo[1]->answer_array)),
                                        'end_time'=> new QuestionAnswer($myuseranswers[2], @unserialize($questioninfo[2]->answer_array)),
                                        'game_design_background'=> new QuestionAnswer($myuseranswers[3], @unserialize($questioninfo[3]->answer_array)),
                                        'tech_background'=> new QuestionAnswer($myuseranswers[4], @unserialize($questioninfo[4]->answer_array)),
                                        'experience_with_online_courses'=> new QuestionAnswer($myuseranswers[5], @unserialize($questioninfo[5]->answer_array)),
                                        'education'=> new QuestionAnswer($myuseranswers[6], @unserialize($questioninfo[6]->answer_array))
                                );*/
				$this->m_extrainfo = $extrainfo;
			}
	}
	
	public function getInfo(){
		return $this->m_info;
	}

	public function getAnswers(){
		return $this->m_answers;
	}

	public function getExtraInfo(){
		return $this->m_extrainfo;
	}

	public function setExtraInfo($extrainfo){
		$this->m_extrainfo = $extrainfo;
	}

	// Send an email to the user who completed the survey
	public function sendEmailToUser($subjectline,$content){
		$useremailaddress = $this->getInfo()["user_email"];
			if($useremailaddress){
                		// wp-content/plugins/bbpress/includes/users/functions.php
                   		// bbp_add_user_subscription
                                //bbp_add_user_forum_subscription

                                //wp-content/plugins/bbpress/templates/default/bbpress-functions.php

                                // Taken from wp-content/plugins/bbPress-Support-Forums-master/includes/bbps-support-functions.php
                                //bbps_assign_topic
                                //wp_mail
                                // http://codex.wordpress.org/Function_Reference/wp_mail
                                // Send an email to the user who just got added to the group
				error_log("to: ".$useremailaddress."ttl: ".$subjectline."cnt: ".$content);
				//wp_mail("oritgigo@mit.edu", $subjectline, $content);
                                wp_mail($useremailaddress, $subjectline, $content);
				//wp_mail("oritgigo@mit.edu", $subjectline, $content."\n\n".$useremailaddress);
			}
			else{
				error_log("user has no email address: ".$this);
			}
	}
	
	// http://php.net/manual/en/function.usort.php and http://php.net/manual/en/function.usort.php Example 3
	// http://php.net/manual/en/language.oop5.object-comparison.php
	static function compare(&$obj1, &$obj2){
		return ($obj1->getAnswers()['number_of_hours']->getAnswer()>$obj2->getAnswers()['number_of_hours']->getAnswer())? +1:-1;	
	}
	
	// http://php.net/manual/en/language.oop5.magic.php#object.tostring
        // http://php.net/manual/en/language.oop5.magic.php#object.debuginfo
        public function __toString(){
		return "uname: ".$this->getInfo()['user_name']." Answers: ".$this->getAnswers()['number_of_hours'].", \n".$this->getAnswers()['time'].", ".", \n".$this->getAnswers()['unit'].", ".$this->getAnswers()['unit'].", \n".$this->getAnswers()['game_design_background'].", \n".$this->getAnswers()['tech_background'].", \n".$this->getAnswers()['experience_with_online_courses'].", \n".$this->getAnswers()['education'];
	}
}

// $groupmembers - an array of UserResponse objects
//function create_group_from_users($groupmembers, $groupnumber){

// Inspired by the buddypress group creation code
function create_new_working_group( $groupnumber){
	// http://php.net/manual/en/features.commandline.interactive.php
	// http://php.net/manual/en/function.time.php
	$mygroupname = 'Working group '. $groupnumber." ".date('M d Y');
	$mygroupdesc = 'This group was created based on the group preference questionnaire';
	// Taken from wp-content/plugins/buddypress/bp-groups/bp-groups-functions.php
	// http://php.net/manual/en/function.empty.php group_id=0 is considered empty, which makes the function create a new group
	// wp-content/plugins/buddypress/bp-groups/bp-groups-functions.php groups_create_group
	// wp-content/plugins/buddypress/bp-groups/bp-groups-actions.php 
	$groupargs = array(
		'group_id'     => 0,
		'creator_id'   => MY_WORKING_GROUP_CREATING_USER_ID,
		'name'         => $mygroupname,
		'description'  => $mygroupdesc,
		'slug'         => groups_check_slug( sanitize_title( esc_attr( $mygroupname ) ) ),
		'status'       => 'hidden',
		'enable_forum' => 1,
		'date_created' => bp_core_current_time()
	);
	// wp-content/plugins/buddypress/bp-groups/bp-groups-actions.php groups_action_create_group
	$mynewgroupid = groups_create_group($groupargs);
	if(!$mynewgroupid){
		error_log("og group creation failed");
	}
	else{
		// wp-content/plugins/buddypress/bp-groups/bp-groups-functions.php groups_edit_group_settings
		// wp-content/plugins/buddypress/bp-groups/bp-groups-forums.php
		if(bp_is_active( 'forums' )){
			error_log("og group creation is active: ".bp_is_active( 'forums' ));
			groups_new_group_forum($mynewgroupid, $mygroupname, $mygroupdesc);
		}
		else{
			// There are other ways to create group forums such as the following:
			// Copied from wp-content/plugins/bbpress/includes/extend/buddypress/groups.php
			// new_forum, edit_screen_save and other functions
			$status  = -1;
			  // Set the default forum status
                          switch ( bp_get_new_group_status() ) {
                                        case 'hidden'  :
                                                $status = bbp_get_hidden_status_id();
                                                break;
                                        case 'private' :
                                                $status = bbp_get_private_status_id();
                                                break;
                                        case 'public'  :
                                        default        :
                                                $status = bbp_get_public_status_id();
                                                break;
                        }
			//echo "status:". $status;
			// Create the initial forum
                        $forum_id = bbp_insert_forum( array(
                              'post_parent'  => bbp_get_group_forums_root_id(),
                              'post_title'   => bp_get_new_group_name(),
                              'post_content' => bp_get_new_group_description(),
                              'post_status'  => $status
                        ) );
			// Copied from wp-content/plugins/bbpress/includes/extend/buddypress/groups.php new_forum (I can't call this function because it
			// requires the BBP_Forums_Group_Extension object)
			//echo "forum id:".$forum_id;
			if ( empty($forum_id)){
				 error_log("og group forum creation failed");
				//echo "og group forum creation failed";
				return 0;
			}
			bbp_add_forum_id_to_group( $mynewgroupid, $forum_id );
			bbp_add_group_id_to_forum( $forum_id, $mynewgroupid );
		}
	}
	return $mynewgroupid;
}

// $groupmembers - an array of UserResponse objects
// $groupnumber - the number of the group (gets added to the group name)
// $additionaltext - additional text to add to the email that each member gets when the script adds them to the WP group
function create_group_from_users($groupmembers, $groupnumber, $additionaltext){
	$mynewgroupid =create_new_working_group($groupnumber);
	// some of the code was taken from wp-content/plugins/bbpress/includes/extend/buddypress/groups.php
	if(!empty($mynewgroupid)){
		foreach($groupmembers as $groupmember){
			$myuserid = $groupmember->getInfo()["user_id"];
			
			//wp-content/plugins/buddypress/bp-groups/bp-groups-template.php
			// bp_get_group_name

			// Copied from wp-content/plugins/buddypress/bp-core/bp-core-avatars.php bp_core_fetch_avatar
			$newgroup = groups_get_group( array( 'group_id' => $mynewgroupid ));
			$newgroupname  = bp_get_group_name($newgroup);
			// Copied from wp-content/plugins/buddypress/bp-activity/bp-activity-template.php
			$newgrouplink = bp_get_group_permalink($newgroup);

			error_log("og adding users to groups");
			//echo "user id to add:".$myuserid." ".$groupmember->getInfo()["user_name"];
			// some of the code was taken from wp-content/plugins/buddypress/bp-groups/bp-groups-functions.php
			// Also inspired by other buddypress code
			if(!groups_join_group($mynewgroupid,$myuserid)){
				error_log("og working groups: failed to add user".$groupmember->getInfo()["user_name"] ." to group");
			}
			else{
				//$useremailaddress = $groupmember->getInfo()["user_email"];


				error_log("sending email");
				// wp-content/plugins/bbpress/includes/users/functions.php
				// bbp_add_user_subscription
				//bbp_add_user_forum_subscription

				//wp-content/plugins/bbpress/templates/default/bbpress-functions.php

				// Taken from wp-content/plugins/bbPress-Support-Forums-master/includes/bbps-support-functions.php
				//bbps_assign_topic
				//wp_mail
				// http://codex.wordpress.org/Function_Reference/wp_mail
				// Send an email to the user who just got added to the group
				$unitpts=$groupmember->getAnswers()['unit']->getPoints();
				$wgmsg = "";		
				$wgmsg1 = "Based on the responses you provided on this week's 11.126x survey, you have been assigned to a working group. Please log-in to the forums in the edX platform before linking to your working group below.\n";
                               	$wgmsg2 = $newgrouplink;
                               	$wgmsg3 = "\n Working groups can function like a discussion group where you make connections and explore certain facets of the material.";
                                $wgmsg4 = "\n Using the group functionality, you can post, reply and add attachments to topics. In addition you can invite friends to join your group.  Finally, be sure to set your email notification preferences so that you can participate in the discussion as desired. \n";
                                $wgmsg5 = "\n Have a great week,\n";
                                $wgmsg6 = "\n The 11.126X Course Team";
				$wgmsg3unit = ""; // default value, in case the unit specific text doesn't exist yet
				//$wgmsg = $wgmsg1.$wgmsg2.$wgmsg3.$wgmsg4.$additionaltext.$wgmsg5.$wgmsg6;
				// Unit 0	
				if($unitpts==1){
					$wgmsg3unit = " To get started for Unit 0, group members should discuss their goals for participating in 11.126x. They should also take a look at each others' responses to Assignment 0.2.\n";
                                } // Unit 1
                                else if($unitpts==2){
					$wgmsg3unit = " Discuss with other group members how you determined the goals for the technology you explored in Assignment 1.1. Continue the conversation by talking about how goals (for educational technology products) are communicated to learners and others. \n";
					// $wgmsg = $wgmsg1.$wgmsg2.$wgmsg3.$wgmsg4.$additionaltext.$wgmsg5.$wgmsg6;
                                } // Unit 2
                                else if($unitpts==3){
					$wgmsg3unit = " Share the learning challenge you identified in Assignment 2.1 with your group. Brainstorm (high-level) about how you might address it and get feedback from other group members. \n"; 
                                        //$wgmsg = $wgmsg1.$wgmsg2.$wgmsg3.$wgmsg4.$additionaltext.$wgmsg5.$wgmsg6;
                                } // Unit 3
                                else if($unitpts==4){
					$wgmsg3unit = " Share your video (or alternative deliverable) with the members of your group. Discuss the learning and teaching models/theories that informed your design. Consider how your educational technology could better reflect what we know about learning. \n";
                                } // Unit 4
				else if($unitpts==5){
					$wgmsg3unit = " Share your use case scenario with the members of your group. Ask if you are missing any important dimensions of the scenario and help uncover aspects of others' scenarios they may have missed. \n";
				} // Unit 5
				else if($unitpts==6){
					$wgmsg3unit = " Talk about your own assessment experiences with group members. How have your personal experiences with assessment in school and in other settings influenced your project? \n";
				} // Unit 6
				else if($unitpts==7){
					$wgmsg3unit = " Ask each group member to review a unit from the course (so all units are covered) and note a few key takeaways. In the group's forum, share the takeaways and discuss additions/subtractions \n";
				}
				$wgmsg = $wgmsg1.$wgmsg2.$wgmsg3.$wgmsg3unit.$wgmsg4.$additionaltext.$wgmsg5.$wgmsg6;

				$groupmember->sendEmailToUser("11.126X Working Group Selection", $wgmsg);
				//wp_mail("oritgigo@mit.edu","11.133X Working Group Selection", $wgmsg);
				//$groupmember->sendEmailToUser("You have been added to a working group.", "Your working group is: \"".$newgroupname."\" .". "Group url: ".$newgrouplink.". ".$additionaltext);
				error_log("Email has been sent");
			}
		}
	}
}

// User matching scoring functions:

// This function provides a student matching score, based on the number of hours each student is willing to put in
// 1 - (Student number of hours - avg(number of hours other students in the group can put in))/maximum possible number of hours.
function score_number_of_hours($studentrespose, $responses_of_others_in_group, $max_hours){
	$max_hours=$studentrespose->getAnswers()['number_of_hours']->getMaxPoints();
	// We use getAnswer here because the answer is numeric (unlike the other questions/categories). The points represent the values.
	$student_hours = $studentrespose->getAnswers()['number_of_hours']->getAnswer();
	// http://php.net/manual/en/function.array-map.php and Example #1 and Example #2
	
	$gethours = function(&$mappedstudent){
		return $mappedstudent->getAnswers()['number_of_hours']->getAnswer();
	};
	$others_in_group = array_map($gethours, $responses_of_others_in_group);
	
	// http://php.net/manual/en/function.array-sum.php
	$score = abs($student_hours-(array_sum($others_in_group)/count($others_in_group)));
	//echo "\n hrs: ".$score."\n";
	//error_log("hrs".$score." "."max hrs: ".$studentrespose->getAnswers()['number_of_hours']->getMaxPoints());
	echo "max hrs: ".$studentrespose->getAnswers()['number_of_hours']->getMaxPoints(); 
	$score = 1-($score/$max_hours);
	echo "\n hrs: ".$score."\n";
	//error_log("hrs".$score);
	return $score;
}

function score_units($userresponse1,$userresponse2){
	// We use getPoints here because the answer is not numeric (unlike the number of hours). The points represent the values.
        $unit = $userresponse1->getAnswers()['unit']->getPoints();
        $unit = $userresponse2->getAnswers()['unit']->getPoints();
        $score = abs($unit1-$unit2);

        // 0=1, 1=.5, >1=0 since we want matching units
        $score = (2.0-min(2,$score))/2.0;
        //echo "\n times: ".$score."\n";
        return $score;
}


function score_times($userresponse1,$userresponse2){
	// We use getPoints here because the answer is not numeric (unlike the number of hours). The points represent the values.
	$time1 = $userresponse1->getAnswers()['time']->getPoints();
	$time2 = $userresponse2->getAnswers()['time']->getPoints();
	$max_time = $userresponse1->getAnswers()['time']->getMaxPoints();
	//echo "max_time ".$max_time;
	$score = abs($time1-$time2);
	// The max score and the min score are consecutive
	if($score==$max_time){
		$score = 1;
	}
			
	// 0=1, 1=.5, >1=0 since we want matching times
	$score = (2.0-min(2,$score))/2.0;
	//echo "\n times 10: ".$score."\n";
	//error_log("times 10: ".$score);
	return $score;
}


// This function computes the knowledge happiness score for a user ($userresponse1) in a pair
// $userresponse1 - the user whose happiness score is computed
// $userresponse2 - the other user
// $typeofknowledge - the name of member function that returns type of knowledge (e.g. education)
// $typeofknowledgehomogeneity - the name of member function that returns the user's group homogeneity preference for this category
// The result is computed based on the $userresponse1's homogeneity preference. If the user does not care about the group's homogeneity this function will return null (because then this
// computation does not matter).
function score_knowledge($userresponse1,$userresponse2, $typeofknowledge, $typeofknowledgehomogeneity){
	$number_of_categories = 5;
	// We use getPoints here because the answer is not numeric (unlike the number of hours). The points represent the values.
	$khomogeneity = $userresponse1->getAnswers()[$typeofknowledgehomogeneity]->getPoints();
	//error_log("typeofknowledge 5:".$typeofknowledge." ".$khomogeneity."; ".$userresponse1->getAnswers()[$typeofknowledge]->getPoints());
	//return null;	

	// return null if the user doesn't care about homogeneity
	if($khomogeneity==0){
		return null;
	}
	$is_homogenious = ($khomogeneity==1);
	//$is_homogenious = 1;
	//error_log("typeofknowledge 2:".$typeofknowledge." ".$khomogeneity);	
	$k1 = $userresponse1->getAnswers()[$typeofknowledge]->getPoints();
	$k2 = $userresponse2->getAnswers()[$typeofknowledge]->getPoints();

	if($number_of_categories==1){
		$score=0; // both students must have the same score in this case
	}
	else{
		// http://php.net/manual/en/function.pow.php
		// The larger the difference, the higher the score (in a non-linear way) - for heterogeneous categories. 
		// The larget the difference, the lower the 1-score (in a non-linear way) - for homogenious categories.
		// Larger differences are better for heterogeneous categories and smaller differences (1-larger differences) are better for homogenious categories
		$score = pow(abs($k1-$k2),3)/pow($number_of_categories-1,3);// -1 because the largest difference is $number_of_categories-1, because the lowest value of $number_of_categories is 1
	}
			
	if($is_homogenious){
		$score = 1-$score;
	}
	//echo "\n knowledge: ".$score."\n";
	return $score;
}

// $items - an array of elements
// $fncs - the functions to run on each pair
// $fncresultnames - the names of the entries where the results will be stored (must be ordered and indexed the same way as the functions. 
//              for a function that has no additional arguments simply use null or array()).
// $fncargs - an array of arrays representing additional arguments to pass into the callback functions (must be ordered and indexed the same way as the functions. 
// 		for a function that has no additional arguments simply use null or array()). Please note that all callback functions take 2 basic arguments
// 		that are provided by default and should not be passed in here: student1 and student 2 (the pair to score). The additional arguments
//		will be passed in addition to these 2 basic arguments. The first 2 arguments will always be the pair of students to score.
// This function will run the functions $fncs on each pair of elements, passing in student i, then student j and then the additional arguments 
// The result gets stored in result[$i][$j][<fnc name>]. Then the function runs in the other direction, passing in student i, then student j and then the additional arguments 
// The gets stored in result[$j][$i][<fnc name>]. 
// result[$i][$i] is set to -1
function all_pairs($items, $fncs, $fncresultnames = array(), $fncargs=array()){
	//error_log("all pairs ".count($items));
	$fncresults = array();
	for($i=0;$i<count($items);$i++){
		$fncresults[$i] = array();
		for($j=0;$j<count($items);$j++){
			$fncresults[$i][$j]=array();	
		}
	}
	$fncresults = array();
	for($i=0;$i<count($items);$i++){
		// Set [$i][$i] equal to -1
		foreach($fncs as $fnc){
			$fncresults[$i][$i][$fnc]=-1;
		}
		for($j=$i+1;$j<count($items);$j++){
			//echo "\n".$i.", ".$j."\n";
			//$result[$i][$j] =
			// http://php.net/manual/en/function.call-user-func.php
			// http://php.net/manual/en/function.call-user-func-array.php
			// Loop over the functions
			for($k=0; $k<count($fncs);$k++){
				$fnc = $fncs[$k];
				// Add any additional arguments and call the function
				// The function $fnc gets called twice, once with i as the student whose happiness we want to maximize and 
				// once with j as the student whose happiness should be maximized 
				$currfncargs = $fncargs[$k];
				$basicfncargs = array($items[$i],$items[$j]); // i,j
				$basicfncargs_otherdirection = array($items[$j],$items[$i]); // j,i since the function may not be symmetric (it may produce different results depending on the order)
				$totalfncargs = $basicfncargs; // i,j 
				$totalfncargs_otherdirection = $basicfncargs; // j,i since the function may not be symmetric (it may produce different results depending on the order)
				if($currfncargs){
					// http://php.net/manual/en/function.array-merge.php
					$totalfncargs = array_merge($basicfncargs,$currfncargs);
					$totalfncargs_otherdirection = array_merge($basicfncargs_otherdirection,$currfncargs);
				}
			///echo "\n fnc:".$fnc."\n";
				// Call the function $fnc with the arguments $totalfncargs as an array of arguments 
				// http://php.net/manual/en/function.call-user-func-array.php
				$score = call_user_func_array($fnc, $totalfncargs);
				$score_otherdirection = call_user_func_array($fnc, $totalfncargs_otherdirection);
				///echo "\n".$i.", ".$j." ".$score."\n";
				// Update results[i][j] and results[j][i] with the score
				$resultentryname = $fnc;
				if($fncresultnames){
					if(count($fncresultnames)==count($fncs)){
						$resultentryname = $fncresultnames[$k];
					}
				}
				// http://php.net/manual/en/function.array-key-exists.php
				// Make sure to store all the results (add a number to the end if an entry already exists)
				$uniqueresultentryname = $resultentryname;
				$existingnumber = 1;
				while(array_key_exists($uniqueresultentryname,$fncresults[$i][$j])){
					$uniqueresultentryname = $resultentryname.$existingnumber;
					$existingnumber+=1;
				}
				$fncresults[$i][$j][$uniqueresultentryname] = $score;
				$fncresults[$j][$i][$uniqueresultentryname] = $score_otherdirection;
				//error_log("all pairs: ".$uniqueresultentryname.": ".$score);
			}
			//$score = abs($items[$i]-$items[$j]);
			
			// 0=1, 1=.5, >1=0 since we want matching times
			//$score = (2.0-min(2,$score))/2.0;
			
		}
	}
	return $fncresults;
}


// Computes the happiness of the group
// $group - an array of group members (UserResponse objects).
// $weights - the weights of the questions
// $max_hours - the maximum possible number of hours a student can put in
// $precalculated - precalculated scores for pairs of students
// This function returns the happiness score for the group
function calc_group_happiness($group, $weights, $max_hours, $precalculated, $debug=false){
	$numberofpairs = 0;
	$totaltimescore = 0;
	$totalnumhoursscore = 0;
	$totalknowledgescore = 0;
	$numofcomputations = array(); // the number of computations per category (we'll divide by this number in order to get all the scores to be between 0 and 1)
	//error_log("in calc_group_happiness...".count($weights)." ".count($group)." ".print_r($precalculated,true));
	$totalscores = array();
	foreach($weights as $key=>$val){
		$totalscores[$key]=0;
		$numofcomputations[$key] = 0;
	}
	for($i=0;$i<count($group);$i++){
		// The student's index before shuffling the array, when the precalculate values were computed
		$initial_student_i_index = $group[$i]->getExtraInfo(); 
		for($j=$i+1;$j<count($group);$j++){
			// The student's index before shuffling the array, when the precalculate values were computed
			$initial_student_j_index = $group[$j]->getExtraInfo(); 
			// Get the precalculated scores for this pair of students
			// Go through all the precalculated values that we want (and have weights for) and compute their scores (by adding the score for each pair to the total score
			// for each category)
			// http://php.net/manual/en/function.array-keys.php
			$scorekeys = array_keys($totalscores);
			//error_log("totalscores keys: ".print_r($scorekeys,true));
			foreach($scorekeys as $scorekey){
				// http://php.net/manual/en/function.array-key-exists.php
				//error_log("idx i:". $initial_student_i_index." idx j:".$initial_student_j_index);
				//error_log("score keys2: ".print_r($scorekeys,true)." precalc keys2: ".print_r(array_keys($precalculated[$initial_student_i_index][$initial_student_j_index]),true));
				//error_log("key exists:".(array_key_exists($scorekey, $precalculated[$initial_student_i_index][$initial_student_j_index])));	
				if(array_key_exists($scorekey, $precalculated[$initial_student_i_index][$initial_student_j_index])){
				//	error_log($scorekey." ".print_r($precalculated[$initial_student_i_index][$initial_student_j_index],true). " val:". $precalculated[$initial_student_i_index][$initial_student_j_index][$key]);
					// http://php.net/manual/en/language.operators.comparison.php
					if($precalculated[$initial_student_i_index][$initial_student_j_index][$scorekey]!==null){
						$totalscores[$scorekey] = $totalscores[$scorekey] + $precalculated[$initial_student_i_index][$initial_student_j_index][$scorekey];
						$numofcomputations[$scorekey] = $numofcomputations[$scorekey]+1;
					}
				}
				// http://php.net/manual/en/language.operators.comparison.php
				if(array_key_exists($scorekey, $precalculated[$initial_student_j_index][$initial_student_i_index])){
					if($precalculated[$initial_student_j_index][$initial_student_i_index][$scorekey]!==null){
                                 	        $totalscores[$scorekey] = $totalscores[$scorekey] + $precalculated[$initial_student_j_index][$initial_student_i_index][$scorekey];
						$numofcomputations[$scorekey] = $numofcomputations[$scorekey]+1;
					}
                                }
			}
			// Get the precalculated time scores for this pair of students
			//$timesscore = $precalculated[$initial_student_i_index][$initial_student_j_index]['score_times']; 
			// Add this pair's score to the total time score
			//$totaltimescore+=$timesscore;
			// Get the precalculated knowledge scores for this pair of students
			//$knowledgescore = $precalculated[$initial_student_i_index][$initial_student_j_index]['score_knowledge'];
			// Ad tthis pair's score to the total time score
			//$totalknowledgescore+=$knowledgescore;
			$numberofpairs++;
		}
		// http://php.net/manual/en/function.array-merge.php
		// http://php.net/manual/en/function.array-slice.php
		// http://php.net/manual/en/arrayobject.getarraycopy.php
		//echo "\n group:".print_r($group,true)."\n";
		// Create an array containing the other students (excluding student $i)
		$groupsobj = new ArrayObject($group);
		$copyofgroup = $groupsobj->getArrayCopy();
		// Remove the current student from the array
		// http://php.net/manual/en/function.array-splice.php
		array_splice($copyofgroup, $i, 1);
		// Compute the score for the number of hours for matching student $i with the rest of the group
		// http://php.net/manual/en/function.array-key-exists.php
		if(array_key_exists('num_hours', $totalscores)){
			$totalscores['num_hours'] = $totalscores['num_hours'] + score_number_of_hours($group[$i],$copyofgroup, $max_hours);
			$numofcomputations['num_hours'] = $numofcomputations['num_hours']+1;
		}
		// Add this number of hours score to the total score
		//$totalnumhoursscore+=$numhoursscore;
		
	}
	// Divide the scores by the number of computations
	//$numhoursscore=$numhoursscore/count($group);
	//$totaltimescore = $totaltimescore/$numberofpairs;
	//$totalknowledgescore = $totalknowledgescore/$numberofpairs;
	// Compute the total score of the group by adding up the scores and multiplying them by their weights
	//$totalscore = $weights["num_hours"]*$numhoursscore+$weights["time_score"]*$totaltimescore+$weights["knowledge_score"]*$totalknowledgescore;
	$totalweightedscore = 0;
	if($debug){
		error_log(" scores: ". print_r($totalscores,true));
		echo "\n scores: ";
	}
	$weigtstodivideby = array(); // We only want to divide by weights of categories that have computations (some categories may have 0 computations if all users don't care about the group's skills)
	foreach($totalscores as $scorekey => $totalscoremulti){
		$totalscore = $totalscoremulti;
		if($numofcomputations[$scorekey]>0){
			// Divide each score by the number of computations in order to get it to be between 0 and 1
			$totalscore = $totalscoremulti / $numofcomputations[$scorekey];
			$weigtstodivideby[$scorekey] = $weights[$scorekey]; 
		}
		$totalweightedscore = $totalweightedscore + $totalscore*($weights[$scorekey]);
		if($debug){
			error_log($scorekey.": ".$totalscore." wt:".$weights[$scorekey]." no cmp: ".$numofcomputations[$scorekey]);
			echo "\n".$scorekey.": ".$totalscore." wt:".$weights[$scorekey]."\n";
		}
	}
	// http://php.net/manual/en/function.array-sum.php
	$totalsumofweights = array_sum($weigtstodivideby);
	if($debug){
		error_log("total weighted score before dividing by weights: ".$totalweightedscore." sum of weights:". $totalsumofweights);
	}
	// Divide the final score by the sum of all the weights in order to make it between 0 and 1
	if($totalsumofweights!=0){
		$totalweightedscore = $totalweightedscore/$totalsumofweights;
	}
	if($debug){
		error_log("total weighted score: ".$totalweightedscore);
		echo "\ntotal weighted score: ".$totalweightedscore."\n";
		error_log("number of ppl in group:".count($group));
	}
	if($totalweightedscore>1){
		error_log("error: weight is greater than 1");
	}
	//echo "\n total score: ".$totalscore." ".$numhoursscore." ".$totaltimescore." ".$totalknowledgescore."\n";
	return $totalweightedscore;
}

// Divide the users into groups based on multiple questions/categories
// The algorithm randomizes the groups multiple times and picks the arrangement that maximizes the total happiness. The questions in the questionnaire are given 
// weights that determine their influence on the users' happiness.
// $groupedusers - the users to group
// $maxrandom - the number of times to randomize groups
// $precalculatedpairscores - precalculated scores for each pair of students
// $group_size - the desired group size
// $debug - true for debug log messages (taken fromwordpress methods thhat do this)
// Return an array containing the max happiness score and an array containing the groups 
function maximize_scores($groupedusers, $maxrandom, $weights, $precalculatedpairscores, $group_size, $debug=false){
	$overall_max_happiness_score = -1;
	$overall_max_happiness_score_groups = array();
	$max_hours = 40;//max($groupedusers); todo: change
	$numberofgroups = floor(count($groupedusers)/$group_size);
	$numberofextras = count($groupedusers)%$group_size; // Extras are the remainder. We get extras when the number of users is not divisible by the group size.
	echo " num users: ".count($groupedusers);
	// Make sure to divide the users into groups so that there are no more extras than groups
	// We increase the number of groups (bu redicing the group size) until we get at least as many groups as extras 
	// The goal is never to add more than one extra per group 
	// Things are done this way, because adding one or less extras to a group is better than increasing the number of groups (or reducing the number of members per group), 
	// when possible. Since this is impossible to do when the number of extras is greater than the number of groups, we have to increase the number of groups,
	// otherwise we'd have to add more than one extra to some of the groups.
	while($numberofextras>$numberofgroups){	
		$group_size--;
        	// http://php.net/manual/en/function.floor.php
        	$numberofgroups = floor(count($groupedusers)/$group_size);
        	$numberofextras = count($groupedusers)%$group_size;
	}
	if($debug){
		error_log("num groups: ". $numberofgroups);
	}

	// If we have less than 2 groups, there's no need to run the algorithm more than once (because the result will always be the same)
	if($numberofgroups<2){
		$maxrandom = 1;
	}	
	// Shuffle the array to randomize the groups and then compute the scores (this will be done $maxrandom times)
	// http://php.net/manual/en/function.array-sum.php
	// http://php.net/manual/en/function.shuffle.php
	// http://php.net/manual/en/arrayobject.getarraycopy.php
	$shuffledobj = new ArrayObject($groupedusers);
	$shuffled = $shuffledobj->getArrayCopy();
	shuffle($shuffled);
	for($j=0;$j<$maxrandom;$j++){
		// Happiness score
		$happiness = 0;
		// Number of extras for this run
		$iternumberofextras = $numberofextras;
		// An array containing the groups	
		$groups = array();
		// The number of users who are already in groups
		$numassignedusers = 0;
		// Divide the users into groups and compute the happiness score for each group 
		for($i=0;$i<$numberofgroups;$i++){
                	$numusersingroup = $group_size;
                	// If we have extras, add one to each group, until all the extras have been assigned to a group
                	if($iternumberofextras>0){
               	        	$numusersingroup++;
               	        	$iternumberofextras--;
               		}
                	// Get the group's members (using array_slice) and add them to the $groupedusers array as a subarray 
                	// http://php.net/manual/en/function.array-slice.php
                	// http://php.net/manual/en/function.array-push.php
                	//echo "\n array slice:".print_r($groupedusers,true).", ".$numassignedusers.", ".$numusersingroup." \n";
               	 	$usersforgroup = array_slice($shuffled, $numassignedusers, $numusersingroup);
               	 	//echo "\n array slice:".print_r($usersforgroup,true).", ".$numassignedusers.", ".$numusersingroup." \n";
                	$numassignedusers+=$numusersingroup;
			if($debug){
				error_log("computing group happiness 2".count($usersforgroup));
			}
              	  	// http://php.net/manual/en/function.range.php
			// Compute the happiness score for this group
                	$curhappiness = calc_group_happiness($usersforgroup, $weights, $max_hours, $precalculatedpairscores);
			if($debug){
				error_log("group happinesss: ".$curhappiness);
			}
			// Add this group's happiness score to the total happiness score
			$happiness+=$curhappiness;
			// Add this group to the group array
                	array_push($groups, $usersforgroup);
        	}
		if(count($groups)>0){
			// Divide the happiness score by the number of groups in order to make it between 0 and 1
       			$happiness = $happiness/count($groups); 
		}
        	// Store the best groups and their total score
        	echo "\n happiness: ".$happiness." ".print_r($groups,true). "\n";
		if($debug){
			error_log("happinesss after dividing by the number of groups: ".$happiness);
		}
       		if($happiness>$overall_max_happiness_score){
        		$overall_max_happiness_score = $happiness;
        		$overall_max_happiness_score_groups = $groups;
        	}
		
		// Shuffle the student array for the next run	
		shuffle($shuffled);
	}
	
	// Return an array containing the max happiness score and an array containing the groups
	echo "\n best happiness: ".$overall_max_happiness_score." ".print_r($overall_max_happiness_score_groups,true). "\n";
	return array($overall_max_happiness_score_groups, $overall_max_happiness_score);
}

// By happiness
// $userresponses - the user responses as an array of UserResponse objects
// $group_size - the desired group size
// $maxrandom - the number of times to randomize groups. Groups are randomized $maxgroups times and we use the arrangement that results in the maximum happiness
// $groupcounterstart - the group number to start from (added to the group name) 
// NOTE: this function creates groups for 2 users or more. In case of a single user, they will not be added to a group and they will get a notification email
// explaining the situation
// If a group has less than $group_size-1 users, this function will create a WP group for them. These users will also receive a notification 
// explaining that an attempt was made to put them in a larger group, but it was not possible.
// $additionaltext - text that gets added to the notification email that each user receives when they get added to a WP group
// Additional sources: http://sandbox.onlinephpfunctions.com/
// bbpress, wordpress and buddypress sourcecode
function split_users_into_groups($userresponses, $group_size=5, $maxrandom=2, $groupcounterstart=0, $additionaltext=""){
	error_log("dividing users into groups ".count($userresponses)." randomizing ".$maxrandom." times");
	// We can't create a group from one user
	if(count($userresponses)==1){ 
		error_log("og single user in unit"); 
        	$userresponses[0]->sendEmailToUser("11.126X Working Group Selection", "Unfortunately, we were not able to match you with other people who are working on the same assignments.");
		error_log("email sent");
		return 0;
        }
	
	// Store the original index for each user (since we're shuffling and we're going to need this info for the precalculated things)
        for($i=0; $i<count($userresponses); $i++){
                $userresponses[$i]->setExtraInfo($i);
        }
	
	// TODO: Read the weights from a settings file?
	$fncnames   =array('score_times',		 			'score_knowledge',      		    	'score_knowledge',              	
					'score_knowledge',                      				'score_knowledge'); 
	$resultnames=array('times',      	        			'education', 				    	'tech_background',              
					'game_design_background',               				'experience_with_online_courses');
	$fncargs    =array(null,         		 			array('education', 'education_homogeneity'),	array('tech_background', 'tech_background_homogeneity'),       
					array('game_design_background','game_design_background_homogeneity'),	array('experience_with_online_courses', 'experience_with_online_courses_homogeneity'));
        // http://php.net/manual/en/arrayobject.getarraycopy.php
        //$resultnamescopy = new ArrayObject($groupedusers);
	$weights = array();//$resultnamescopy->getArrayCopy();
	$weights['times']=.05;
	//$weights['unit']=.5;
	$weights['education']=.1;
	$weights['tech_background']=.1;
	$weights['game_design_background']=.1;
	$weights['experience_with_online_courses']=.1;
	$weights['num_hours']=.4;
	//weights = array('num_hours'=>.4, 'times'=>.2, 'education'=>.1, 'tech_background'=>.1, 'game_design_background'=>.1, 'experience_with_online_courses'=>.1);
	$precalculatedpairscores = all_pairs($userresponses, 
				$fncnames,	
				$resultnames,	
				$fncargs);
	//error_log("all_pairs_scores:".print_r($precalculatedpairscores,true));	
	$maxhappinessandgroups = maximize_scores($userresponses, $maxrandom, $weights, $precalculatedpairscores, $group_size);
	$maxhappiness = $maxhappinessandgroups[1];
	$maxhappinessgroups = $maxhappinessandgroups[0];	
	//echo " num in group: ".$numusersingroup;
        //error_log(" num in group: ".$numusersingroup);
	error_log("max happiness: ".$maxhappiness." ".count($maxhappinessgroups));
	//$textfordebug = "\n";
	//$textfordebug = $textfordebug + "max happiness: ".$maxhappiness." ".count($maxhappinessgroups);
	$i=0;
      	for(; $i<count($maxhappinessgroups); $i++){ 
		$maxhappinessgroup = $maxhappinessgroups[$i]; 
                echo "<br/> Group ".$i.":<br/>";
                error_log("\n Group ".$i.":\n");
		//$textfordebug = $textfordebug."\n Group ".$i.":\n";
                //echo print_r($usersforgroup, true);
                for($j=0;$j<count($maxhappinessgroup);$j++){
                        echo $maxhappinessgroup[$j]."<br/>";
                        error_log($maxhappinessgroup[$j]."\n");
			//$textfordebug = $textfordebug.$maxhappinessgroup[$j]."\n"
                }
		
		// Shouldn't happen, but just in case
		if(count($maxhappinessgroup)==1){
			$groupmember->sendEmailToUser("11.126X Working Group Selection", "Unfortunately, we were not able to match you with other people who are working on the same assignments.");
			error_log("og group splitting error: single user in multiple groups");
		}
		else{
			$additionalmessage = "";
			if(count($maxhappinessgroup)<$group_size-1){
				error_log("too few users in group");
				if($additionaltext==""){
					$additionalmessage = "\n We did our best to match you with other users that are working on the same assignments. ";
					$additionalmessage = $additionalmessage . "Unfortunately, we were not able to find a larger group for you based on your preferences.\n ";
				}
			}
			// Create a wp group for these users
			create_group_from_users($maxhappinessgroup,$i+$groupcounterstart, $additionaltext.$additionalmessage);
		}
	}
	return $i;
}

// Divide the users into groups based on the number of hours: 
// This function groups the users based on the number of hours they want to dedicate to the course
// How we split the users:
// If the number of users is divisible by the group size, each group will have $group_size users.
// If the number of users is not divisible by the group size and we need to add more than one user to each
// group in order for all the users to be in a group (meaning that the remainder is greater than the number of groups), we
// reduce the group size (until we are able to add one user or less to each group).   
// If the number of users is not divisible by the group size and we need to add one user or less to each group in order to get all the users in
// groups (meaning that the remainder is less than or equal to the number of groups), we add one user or less to each group.
function split_users_into_groups_by_number_of_hours($userresponses, $group_size=5){

	// Store the original index for each user (since we're shuffling and we're going to need this info for the precalculated things)
	for($i=0; $i<count($userresponses); $i++){
		$userresponses[$i]->setExtraInfo($i);
	}

	// http://php.net/manual/en/function.usort.php
	// http://php.net/manual/en/function.sort.php
	// http://php.net/manual/en/function.array-multisort.php
	foreach($userresponses as $userresponse){
		echo "<br/>" . $userresponse->getAnswers()['number_of_hours']." "; 
	}
	echo "<br/><br/> sorted: ";
	
	// Sort the responses by the number of hours each user is willing to put in
	usort($userresponses, array('UserResponse', 'compare'));
	foreach($userresponses as $userresponse){
                echo "<br/>" . $userresponse->getAnswers()['number_of_hours']." ";
        }
	echo "<br/>";
	$groupedusers = array();

	// Get the number of groups (div) and the number of extras
	// http://php.net/manual/en/function.floor.php
	$numberofgroups = floor(count($userresponses)/$group_size);
	$numberofextras = count($userresponses)%$group_size; // Extras are the remainder. We get extras when the number of users is not divisible by the group size.
	echo " num users: ".count($userresponses);	
	// Make sure to divide the users into groups so that there are no more extras than groups
	// We increase the number of groups (bu redicing the group size) until we get at least as many groups as extras 
	// The goal is never to add more than one extra per group 
	// Things are done this way, because adding one or less extras to a group is better than increasing the number of groups (or reducing the number of members per group), 
	// when possible. Since this is impossible to do when the number of extras is greater than the number of groups, we have to increase the number of groups,
	// otherwise we'd have to add more than one extra to some of the groups.
	while($numberofextras>$numberofgroups){
		$group_size--;
		// http://php.net/manual/en/function.floor.php
		$numberofgroups = floor(count($userresponses)/$group_size);
		$numberofextras = count($userresponses)%$group_size;
	}
	//echo " ".count($userresponses)." ".$numberofgroups;
	$numassignedusers = 0;
	for($i=0;$i<$numberofgroups;$i++){
		$numusersingroup = $group_size;
		// If we have extras, add one to each group, until all the extras have been assigned to a group
		if($numberofextras>0){
			$numusersingroup++;
			$numberofextras--;
		}
		// Get the group's members (using array_slice) and add them to the $groupedusers array as a subarray 
		// http://php.net/manual/en/function.array-slice.php
		// http://php.net/manual/en/function.array-push.php
		$usersforgroup = array_slice($userresponses, $numassignedusers, $numusersingroup);
		echo " num in group: ".$numusersingroup;
		error_log(" num in group: ".$numusersingroup);
		array_push($groupedusers, $usersforgroup);
		$numassignedusers+=$numusersingroup;
		echo "<br/> Group ".$i.":<br/>";
		error_log("\n Group ".$i.":\n");
		//echo print_r($usersforgroup, true);
		for($j=0;$j<count($usersforgroup);$j++){
			echo $usersforgroup[$j]."<br/>";
			error_log($usersforgroup[$j]."\n");
		}	
		create_group_from_users($usersforgroup,$i+1); 
		
	}	
}


// Get the users' group preferences and divide them into groups
// Users are divided into groups by unit and based on their other preferences. Users who are not working on the same unit can't be in the same group.
// For each unit, we collect all the users who are working on this unit and use the function split_users_into_groups to divide them into groups.
// The script creates WP groups for these users and notifies each user when they get added to a WP group.
// Edge cases:
// 1. Only one user is working on a specific unit: the user will be added to a misfit group. This group will contain all the users who could not be matched with 
// any other user, because no other user is working on the same unit. 
// NOTE: the number of members of the misfit group does not depend on the $group_size. This group contains All the users who could not be added to any other group
// due to their unit preferences.
// 1.1 The misfit group contains one member: this member will not have a group and they will get an email explaining the situation
// 2. A group has less than $group_size-1 members: all the members in the group will be informed that they could not be added to a larger group. 
// $from_creation_time - tells the algorithm to start collecting usergroup preferences from this date (for example, you can pass in the last time when this script ran
// to process new survey results). When $from_creation_time is set to false, the script processes all the survey data collected within the past $num_days days.
// $group_size - the desired side of the groups
// $quiz_id - the quiz id of the quiz from which we get the data
function process_usergroup_preferences($from_creation_time=false,$num_days=3,$group_size=5,$quiz_id=1){
	//$num_days = MY_CREATE_WORKING_GROUP_EVERY_X_DAYS;
	// http://wordpress.stackexchange.com/questions/13707/variable-from-a-plugin-into-a-theme
	// https://github.com/fpcorso/quiz_master_next/blob/master/includes/qmn_addons.php
	// http://php.net/manual/en/function.array-key-exists.php
	$mlw_result_id = 1;
	echo "abcdefg";
	// Get the "quiz master next" plugin object
	if(array_key_exists('mlwQuizMasterNext', $GLOBALS)){
		$userprefplugin = $GLOBALS['mlwQuizMasterNext']; // This variable stores the "quiz master next" plugin object
		// Taken from https://github.com/fpcorso/quiz_master_next/blob/master/includes/qmn_results_details.php
		// and https://github.com/fpcorso/quiz_master_next/blob/master/includes/qmn_results.php
		global $wpdb;


		// If we saved the last time when the working group algorithm ran, we should get all the new surveys that were submitted since then
		// (if we don't have it, then we get all the new surveys that were submitted over the past $num_days)
		//http://codex.wordpress.org/Function_Reference/get_option
		// Actual creation time (we can't use this time to determine when to run the algorithm in the future
        	// because it can create problematic time shifts, see below).
		// replaced by the argument $from_creation_time, because the database field gets set right before this function is called
        	//$my_last_working_groups_real_creation_time = get_option( 'my_last_working_groups_real_creation_time');

		$datefrom = "DATE_SUB(NOW(), INTERVAL ".$num_days." DAY)";
        	// Copied from http://codex.wordpress.org/Function_Reference/update_option
       		if($from_creation_time!=false){
			// Convert the date to mysql format
			// Taken from http://php.net/manual/en/datetime.createfromformat.php
        	        // and http://php.net/manual/en/datetimezone.construct.php
               	 	// and http://php.net/manual/en/datetimezone.listabbreviations.php
          	      	// http://php.net/manual/en/datetimezone.construct.php
                	// http://php.net/manual/en/datetime.settimezone.php
                	// and http://php.net/manual/en/function.timezone-abbreviations-list.php user contributed commen #2
                	// and http://us.php.net/manual/en/timezones.others.php
                	// http://php.net/manual/en/class.datetime.php
                	// http://php.net/manual/en/datetime.settime.php
                	// http://php.net/manual/en/datetime.modify.php
                	// http://php.net/manual/en/datetime.gettimestamp.php
			// other php time related pages
                	$gmt = new DateTimeZone("GMT");
                	//$date = DateTime::createFromFormat('j-M-Y H:i:s', '15-Feb-2009 15:16:17', $etus);
			// http://php.net/manual/en/class.datetime.php
                	$datelastrealrun = new DateTime(); // exact date when the algorithm ran last
			$datelastrealrun->setTimezone($gmt);
                	// Set the date times to now
                	$datelastrealrun->setTimestamp($from_creation_time);
			// http://php.net/manual/en/function.date.php
	                // http://php.net/manual/en/datetime.modify.php
        	        $datefrom = "'".$datelastrealrun->format('Y-m-d H:i:s')."'";
		}
			

		// Get the group preference questionnaires that have been submitted by users over the past num_days days
		// http://dev.mysql.com/doc/refman/5.1/en/date-and-time-functions.html#function_date-sub
		// http://dev.mysql.com/doc/refman/5.1/en/date-and-time-functions.html#function_now
		// http://dev.mysql.com/doc/refman/5.1/en/date-and-time-functions.html
		// http://dev.mysql.com/doc/refman/5.5/en/date-and-time-functions.html
		// STR_TO_DATE date formatting conflicts with prepare:
		// http://codex.wordpress.org/Class_Reference/wpdb
		// https://core.trac.wordpress.org/ticket/23064
		// http://stackoverflow.com/questions/17764321/mysql-where-str-to-date-now
		// Subselect to get the most recent survey for each user
		// http://dev.mysql.com/doc/refman/5.0/en/example-maximum-column-group-row.html
		// TODO: pass in the date of the last run, because otherwise we get it after changing it
		$newquizquery = "SELECT *,NOW() as tm_now, ".$datefrom." as user_form_start, time_taken as test FROM " . $wpdb->prefix . "mlw_results as r1 WHERE r1.deleted='0' AND r1.quiz_id=" . $quiz_id . " and ".$datefrom."<STR_TO_DATE(r1.time_taken,'%%r %%m/%%d/%%Y') and STR_TO_DATE(r1.time_taken,'%%r %%m/%%d/%%Y') = (select max(STR_TO_DATE(r2.time_taken,'%%r %%m/%%d/%%Y')) from ".$wpdb->prefix . "mlw_results r2 where r1.user=r2.user and r1.quiz_id=r2.quiz_id and r1.deleted=r2.deleted) ORDER BY result_id DESC";
		error_log("og new quiz query: ". $newquizquery);
		$mlw_quiz_data = $wpdb->get_results( $wpdb->prepare($newquizquery) );

		/*$mlw_quiz_data_old = $wpdb->get_results( $wpdb->prepare( "SELECT *,NOW() as tm_now, DATE_SUB(NOW(), INTERVAL ".$num_days." DAY) as user_form_start, time_taken as test FROM " . $wpdb->prefix . "mlw_results as r1 WHERE r1.deleted='0' AND r1.quiz_id=" . $quiz_id . " and DATE_SUB(NOW(), INTERVAL ".$num_days." DAY)<STR_TO_DATE(r1.time_taken,'%%r %%m/%%d/%%Y') and STR_TO_DATE(r1.time_taken,'%%r %%m/%%d/%%Y') = (select max(STR_TO_DATE(r2.time_taken,'%%r %%m/%%d/%%Y')) from ".$wpdb->prefix . "mlw_results r2 where r1.user=r2.user and r1.quiz_id=r2.quiz_id and r1.deleted=r2.deleted) ORDER BY result_id DESC") );*/

		// Taken from https://github.com/fpcorso/quiz_master_next/blob/master/includes/qmn_quiz.php
                $order_by_sql = "ORDER BY question_order ASC";
                $questions = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."mlw_questions WHERE quiz_id=%d AND deleted=0 ". $order_by_sql,$quiz_id));
                //echo "<br/>questions: ".print_r(@unserialize($questions[4]->answer_array),true)."<br/>";

		// get the maximum number of hours a student is willing to dedicate
		$maxdedicatedhours = 0;

		error_log("num of users: ".count($mlw_quiz_data));
		// Process the responses and split the users into groups
		$userresponses = array();
		foreach($mlw_quiz_data as $mlw_quiz_info) {
			$userresponse = new UserResponse($mlw_quiz_info, $questions);
			//echo print_r($userresponse, true);
			// http://php.net/manual/en/function.array-push.php
			array_push($userresponses,$userresponse);
			$myuserid = $userresponse->getInfo()['user_name']; //$mlw_quiz_info->user;
			$myuseremail = $userresponse->getInfo()['user_email'];//$mlw_quiz_info->email;
			// http://php.net/manual/en/function.intval.php
			// http://php.net/manual/en/function.max.php
			//error_log("messed up2: ".$userresponse->getAnswers()['number_of_hours']->getAnswer()." ".$maxdedicatedhours." ".max($maxdedicatedhours, intval($userresponse->getAnswers()['number_of_hours']->getAnswer())));
			$maxdedicatedhours = max($maxdedicatedhours, intval($userresponse->getAnswers()['number_of_hours']->getAnswer()));
			echo "nameemail: ".$myuserid .", ". $myuseremail." ".$userresponse->getAnswers()['number_of_hours']->getAnswer()." andw".$userresponse->getAnswers()['education'] ;
		}
	
		error_log("max hours spent course: ".$maxdedicatedhours);
		// Set the maximum hours based on the maximum hours of the users (maximum hours doesn't have a fixed value) and divide the users into initial groups by unit
		$usersbyunit = array();	
		foreach($userresponses as $userresponse){
			//	echo "herrree: ";
			//echo $userresponse->getAnswers['number_of_hours'];
			$userresponse->getAnswers()['number_of_hours']->setMaxPoints($maxdedicatedhours); 
			$userunit = "unitpts".$userresponse->getAnswers()['unit']->getPoints(); // 'unit1'; // $userresponse->getAnswers()['unit']->getAnswer();
			// http://php.net/manual/en/function.array-key-exists.php
			if(!array_key_exists($userunit, $usersbyunit)){
				$usersbyunit[$userunit] = array();
			}
			// http://php.net/manual/en/function.array-push.php
			array_push($usersbyunit[$userunit], $userresponse);
		}
	
		// We must have users who want to be in groups in order to run the algorithm
		if(count($usersbyunit)==0){
			error_log("no users to divide into groups");
			return;
		}	
	
		$startinggroupnumber = 1; // The group number to start from (added to the group name)
		$misfits = array(); // An array of users who are working on different units
		// We run the algorithm on each unit seperately. Users who are working on one unit can't work with users who are working on another unit.
		foreach($usersbyunit as $userunit => $userresponsesbyunit){
			error_log("unit:".$userunit." users:".count($userresponsesbyunit));
			// If there's only one user who is working on this unit, add them to the misfit array
			if(count($userresponsesbyunit)==1){
				error_log("one user in unit: ".$userresponsesbyunit[0]);
				// http://php.net/manual/en/function.array-push.php
				array_push($misfits,$userresponsesbyunit[0]);	
			}
			else{	
				$numberofgroups = split_users_into_groups($userresponsesbyunit, $group_size, MY_NUMBER_OF_TIMES_TO_RANDOMIZE_GROUPS, $startinggroupnumber);
				$startinggroupnumber = $startinggroupnumber+$numberofgroups;
			}
		}
		// Create a group for the misfits
		if(count($misfits)>0){
			error_log("Has misfits");
			// Create groups from the misfits (they should all be in one group regardless of the $group_size)
			$additionalmessage = "\n We did our best to match you with other users that are working on the same assignments. ";
                        $additionalmessage = $additionalmessage . "Unfortunately, we were not able to find a larger group for you based on your preferences. \n ";
			split_users_into_groups($misfits, max(count($misfits),$group_size), MY_NUMBER_OF_TIMES_TO_RANDOMIZE_GROUPS, $startinggroupnumber, $additionalmessage);
		}
		else{
			error_log("no misfits");
		}
		wp_mail("oritgigo@mit.edu", "Done running the group script", "test 1 \n test2 and now\n"."\nanother line right here.\n\n");
	}
	
}


// For debugging - uncomment these functions to run the grouping script when the quiz result detail tab is accessed (collecting results from the 
// past MY_CREATE_WORKING_GROUP_EVERY_X_DAYS number of days)
// Taken from https://github.com/fpcorso/quiz_master_next/blob/master/includes/qmn_results_details.php
//function qmn_generate_results_details_tab_myfunc($template, $qmn_array_for_variables){
//	echo "12345";
//	process_usergroup_preferences(MY_CREATE_WORKING_GROUP_EVERY_X_DAYS, MY_WORKING_GROUP_SIZE);
//	return $template;
//}

// This filter makes qmn_generate_results_details_tab_myfunc run when the results page is accessed
// This is good for debugging
// http://codex.wordpress.org/Function_Reference/add_filter
//add_filter('mlw_qmn_template_variable_results_page', 'qmn_generate_results_details_tab_myfunc', 20, 2);

function my_create_working_groups_fnc($lastcreationtime){
	error_log("og creating working groups");
	process_usergroup_preferences($lastcreationtime,MY_CREATE_WORKING_GROUP_EVERY_X_DAYS, MY_WORKING_GROUP_SIZE);
	// schedule the next run
	//schedulegroupcreation();
}

// Taken from http://codex.wordpress.org/Function_Reference/wp_schedule_event
//add_action('my_create_working_groups', 'my_create_working_groups_fnc');

////////////////////////// Scheduling Group Creation

// Add schedules (everyxminutes and everyxdays)
// http://codex.wordpress.org/Function_Reference/add_filter
// https://core.trac.wordpress.org/browser/tags/4.1.1/src/wp-includes/cron.php#L0
function my_add_cron_schedule($mycronschedules){
	// see https://core.trac.wordpress.org/browser/tags/4.1.1/src/wp-includes/cron.php#L0 wp_get_schedules
	$mynewschedules = array();
	//error_log("og cron schedule");
	// Add every x days (1-30)
	for($i=1;$i<30;$i++){
		// http://php.net/manual/en/function.array-push.php
		$mynewschedules['every'.$i.'days'] = array( 'interval' => $i*DAY_IN_SECONDS, 'display' => __( 'Every'.$i.' Days' ) );
	}
	// wp-includes/default-constants.php
	// This is mainly for testing
	for($i=1;$i<30;$i++){
		// http://php.net/manual/en/function.array-push.php
		$mynewschedules['every'.$i.'minutes'] = array( 'interval' => $i*MINUTE_IN_SECONDS, 'display' => __( 'Every'.$i.' Minutes' ));
	}
	//error_log("og cron schedule arr ".print_r($mycronschedules,true).print_r(array_merge($mycronschedules, $mynewschedules),true));
	// taken from https://core.trac.wordpress.org/browser/tags/4.1.1/src/wp-includes/cron.php#L0 wp_get_schedules
	// http://php.net/manual/en/function.array-merge.php
	return array_merge($mycronschedules, $mynewschedules);
}

add_filter( 'cron_schedules', 'my_add_cron_schedule', 20, 1);

// Converts the config time, $mytime to seconds
// Inspired by wp-includes/cron.php's MINUTE_IN_SECONDS
// https://core.trac.wordpress.org/browser/tags/4.1.1/src/wp-includes/cron.php#L0
function my_cron_config_time_to_seconds($mytime){
	// Copied from https://core.trac.wordpress.org/browser/tags/4.1.1/src/wp-includes/cron.php#L0
	return $mytime*DAY_IN_SECONDS;
	//return $mytime*HOUR_IN_SECONDS; // * 60*60*24
	//return $mytime*MINUTE_IN_SECONDS;
	//return $mytime;
}


// http://codex.wordpress.org/Function_Reference/wp_schedule_event
// http://php.net/manual/en/function.time.php
//function schedulegroupcreation(){
//	error_log("og creating schedule 1000");
//	// http://codex.wordpress.org/Function_Reference/wp_get_schedule
//        if ( ! wp_next_scheduled('my_create_working_groups')){
//		error_log("og creating schedule and scheduling 10");
//		$newnumdays = MY_CREATE_WORKING_GROUP_EVERY_X_DAYS;
//		// http://codex.wordpress.org/Function_Reference/wp_schedule_single_event
//                // http://stackoverflow.com/questions/3052865/add-30-seconds-to-the-time-with-php
//		wp_schedule_single_event(time()+my_cron_config_time_to_seconds($newnumdays), 'schedulegroupcreationjob');
//		//wp_schedule_event(time(), 'every'.$newnumdays.'minutes', 'my_create_working_groups');
//		error_log("og creating schedule and scheduling 21 ".wp_next_scheduled('my_create_working_groups')." ".time());
//	}
//	else{
//		error_log("og cron next scheduled is there 3 ".wp_next_scheduled('my_create_working_groups')." ".time());
//		// http://www.onlineconversion.com/unix_time.htm
//                //error_log("og creating schedule and scheduling 4 ".wp_next_scheduled('my_create_working_groups')." ".time());
//		// http://codex.wordpress.org/Function_Reference/wp_unschedule_event
//	//	wp_unschedule_event(wp_next_scheduled('my_create_working_groups'), 'my_create_working_groups');
//		// http://www.onlineconversion.com/unix_time.htm
//		//error_log("og creating schedule and scheduling 4".wp_next_scheduled('my_create_working_groups')." ".time());
//	}
//}

// This function checks whether it's time to run the group creation algorithm
// Whenever the config time changes, the script will run within the next 24 hours at the new scheduled time.
// Subsequent runs will occur after enough time has passed since the last run, based on the config setting that determines 
// how often to run the script (MY_CREATE_WORKING_GROUP_EVERY_X_DAYS)
function my_check_group_creation_schedule_change_fnc(){

	//wp_clear_scheduled_hook('my_create_working_groups');
        //wp_clear_scheduled_hook('schedulegroupcreationjob');
	//http://wordpress.stackexchange.com/questions/113048/what-is-the-best-way-to-store-custom-variables
	//http://codex.wordpress.org/Function_Reference/get_option
	//http://codex.wordpress.org/Function_Reference/update_option
	//error_log("og cron 20000001");
	$curtime = time();
	//$newnumdays = MY_CREATE_WORKING_GROUP_EVERY_X_DAYS;
	// The approximate creation time (prevents time shifts, see below)
	$my_last_working_groups_creation_time = get_option( 'my_last_working_groups_creation_time');
	// Actual creation time (we can't use this time to determine when to run the algorithm in the future
	// because it can create problematic time shifts, see below).
	$my_last_working_groups_real_creation_time = get_option( 'my_last_working_groups_real_creation_time');

	// Copied from http://codex.wordpress.org/Function_Reference/update_option
	if($my_last_working_groups_creation_time==false){
		add_option('my_last_working_groups_creation_time', $curtime);
		if($my_last_working_groups_real_creation_time===false){
			add_option('my_last_working_groups_real_creation_time', $curtime);
		}
		else{
			update_option('my_last_working_groups_real_creation_time', $curtime);
		}
		my_create_working_groups_fnc();
		error_log("og cron 10000001");
	}
	else{
		// If the real group creation time doesn't exist yet, update it to the last approximate creation time
		if($my_last_working_groups_real_creation_time===false){
                        add_option('my_last_working_groups_real_creation_time', $my_last_working_groups_creation_time);
                } 

		// Taken from http://php.net/manual/en/datetime.createfromformat.php
		// and http://php.net/manual/en/datetimezone.construct.php
		// and http://php.net/manual/en/datetimezone.listabbreviations.php
		// http://php.net/manual/en/datetimezone.construct.php
		// http://php.net/manual/en/datetime.settimezone.php
		// and http://php.net/manual/en/function.timezone-abbreviations-list.php user contributed commen #2
		// and http://us.php.net/manual/en/timezones.others.php
		// http://php.net/manual/en/class.datetime.php
		// http://php.net/manual/en/datetime.settime.php
		// http://php.net/manual/en/datetime.modify.php
		// http://php.net/manual/en/datetime.gettimestamp.php
		$etus = new DateTimeZone(MY_WORKING_GROUP_TIME_ZONE);
		$gmt = new DateTimeZone("GMT");
		//$date = DateTime::createFromFormat('j-M-Y H:i:s', '15-Feb-2009 15:16:17', $etus);
		$datenow = new DateTime(); // current date and time (from the $curtime timestamp)
		$datenowetus = new DateTime(); // current date and time (from the $curtime timestamp) with the config file's time zone
		$dateconfig = new DateTime(); // nearest config time
		// Set the date times to now
		$datenow->setTimestamp($curtime);
		$datenowetus->setTimestamp($curtime);
		$dateconfig->setTimestamp($curtime);
		// Set the zone of $datenowetus to the config zone, so that we can update the time to the config time
		$datenowetus->setTimezone($etus);
		// Set the zone of the config time to the config zone, so that we can update the time to the config time
		$dateconfig->setTimezone($etus);
		// Update the time to the config time
		$dateconfig->setTime(MY_WORKING_GROUP_RUN_HOUR,MY_WORKING_GROUP_RUN_MINUTE);

		// Get today's week day (based on the config file's local time)
		// http://php.net/manual/en/function.date.php
		// http://php.net/manual/en/datetime.modify.php
		$todaysdaytext = $dateconfig->format('l');
		$shouldruntoday = false;
		// http://php.net/manual/en/function.strtoupper.php
		$todaysdaytextupper = strtoupper($todaysdaytext);
		$todayrunsetting = 'MY_WORKING_GROUP_RUN_'.$todaysdaytextupper; // the setting that specifies whether to run the script today
		// http://php.net/manual/en/function.array-push.php
		//array_push($daystorun, 'MY_WORKING_GROUP_RUN_DAY_'.$i);
		// https://codex.wordpress.org/Changing_The_Site_URL
		// http://php.net/manual/en/function.constant.php
		// Also, taken from code of plugins that check if a constant is defined
		if(defined($todayrunsetting)){
			// http://php.net/manual/en/function.constant.php
			$shouldruntoday = constant($todayrunsetting);
		}

		// Set the time zone back to GMT (because we work with GMT)
		$dateconfig->setTimezone($gmt);
		$myepsilon = MY_WORKING_GROUP_EPSILON;//10; // epsilon for the scheduled time to run the script
		$dateconfigeps = new DateTime();
		$dateconfigeps->setTimezone($gmt);
		$dateconfigeps->setTimestamp($dateconfig->getTimestamp());
		$dateconfigeps->modify("+".$myepsilon." minutes"); // the config date with the added epsilon
		// dateconfigeps: if it's within 10 minute (epsilon of the config time), the script will still run today, so there's no need to add a day
		// If the config time has passed (by more than epsilon minutes), add a day (to get to the next time the config time will happen)
		// The goal here is to get the nearest config time in the future, so that we can run the algorithm then
		if($datenow>$dateconfigeps){
		        $dateconfig -> modify('+1 day');
		}
		// http://php.net/manual/en/datetime.gettimestamp.php
		$configtimestamp = $dateconfig->getTimestamp();

		// http://php.net/manual/en/function.time.php
		// http://php.net/manual/en/function.intval.php
		$my_last_working_groups_creation_time_int = intval($my_last_working_groups_creation_time);
		//add_option('my_last_working_groups_creation_time', $curtime);
		// http://php.net/manual/en/function.gettype.php
		//error_log("og cron 10000007 ".gettype($curtime).gettype($my_last_working_groups_creation_time_int).$my_last_working_groups_creation_time_int." ".$curtime);
		//$my_last_working_groups_creation_time = get_option( 'my_last_working_groups_creation_time', $curtime);
		//error_log("og cron 10000002 ");//.($curtime-$my_last_working_groups_creation_time_int));

		
		$mytimediff = $curtime-$my_last_working_groups_creation_time_int;
		$myconfiglastrundiff = $configtimestamp - $my_last_working_groups_creation_time_int;
		$lastruntime = new DateTime();
		$lastruntime->setTimezone($gmt);
		$lastruntime->setTimestamp($my_last_working_groups_creation_time_int);
	
		// The first time the config time changes, we want to make sure to run the script on that day at the specified time
		// we get from the config file.
		// We check if the config time has changed by checking if script ran at a different time than the config time 
		// (more than 10 minutes off). The last run time is stored in the database. We also check if now is the time
		// to run the script (meaning that now = config time +-epsilon). If the config time has changed and now is the time
		// to run the script, we run it. Subsequent runs will occur based on the number of days that have passed since we last ran the
		// script and the specified number of days in the config file. 
		// config time - last run time != n*24h+- (meaning that the config time has changed)
		// https://core.trac.wordpress.org/browser/tags/4.1.1/src/wp-includes/cron.php#L0
		// http://php.net/manual/en/class.dateinterval.php
		/*
		$runconfigdiff = $dateconfig->diff($lastruntime);
		$runtimechanged = true;
		//$myepsilon = 10; // epsilon for the scheduled time to run the script
		$myupperbound = 60-$myepsilon; // upper bound with the epsilpon included
		$mylowerbound = $myepsilon; // lower bound with the epsilpon included
		// We check if the config time has changed by checking if script ran at a different time than the config time 
                // (more than 10 minutes off). This is done this way because we have no other way of knowing whether the config 
                // time settings have changed.
		// Set it equal to false if it ran exactly n days ago +- 10 minute epsilon
		if(($runconfigdiff->h==23 && $runconfigdiff->i>$myupperbound)||
		   ($runconfigdiff->h==0 && $runconfigdiff->i<$mylowerbound)){
			$runtimechanged = false;
			error_log("time hasn't changed");
		}
		// Check if now is the scheduled time to run the script (meaning that now = config time +-epsilon)
		$diffconfignow = $dateconfig->diff($datenow);
		$timetorun = false;
		if($diffconfignow -> h ==0 && $diffconfignow -> i < $mylowerbound || $diffconfignow -> h ==23 && $diffconfignow -> i > $myupperbound){
			$timetorun = true;
		}
		error_log("time diff ".$diffconfignow -> h." ".$diffconfignow -> i." ".$timetorun);
		error_log("db now time diff ".$mytimediff." "."needed: ".my_cron_config_time_to_seconds($newnumdays));
		*/

		// Copy $dateconfig to $todaysrun and change the timezone to the time zone in the settings file
		// and then get the nearest datetime on which we will run the algorithm (based on the day in the config) 
		// We need to change the time zone to the config's time zone because we will get the next run date based on the day of the week
		// and the days of the week in the config file are set based on the timezone of the config file	
		// http://php.net/manual/en/class.dateinterval.php
		// Taken from http://php.net/manual/en/datetime.createfromformat.php
                // and http://php.net/manual/en/datetimezone.construct.php
                // and http://php.net/manual/en/datetimezone.listabbreviations.php
                // http://php.net/manual/en/datetimezone.construct.php
                // http://php.net/manual/en/datetime.settimezone.php
                // and http://php.net/manual/en/function.timezone-abbreviations-list.php user contributed commen #2
                // and http://us.php.net/manual/en/timezones.others.php
                // http://php.net/manual/en/class.datetime.php
                // http://php.net/manual/en/datetime.settime.php
                // http://php.net/manual/en/datetime.modify.php
                // http://php.net/manual/en/datetime.settimestamp.php
                // http://php.net/manual/en/dateinterval.createfromdatestring.php
                // http://php.net/manual/en/function.date.php
		$todaysrun = new DateTime();
		$nextrunupper = new DateTime(); // $nextrun+epsilon
		$nextrunlower = new DateTime(); // $nextrun-epsilon
		$todaysrun->setTimezone($gmt);
		$todaysrun->setTimestamp($dateconfig->getTimestamp());
		$todaysrun->setTimezone($etus);
		error_log($dateconfig->format('M-d-Y  H:i')." ".$todaysrun->format('M-d-Y  H:i'));
		// Get the approximate time we are scheduled to run the algorithm, based on the day in the settings	
		// This function returns the config time on the closest day that we ar scheduled to run
		$nextrun = compute_next_run_day($todaysrun);
		if(!$nextrun){
			return;
		}
		error_log("nextrun:".$nextrun->format('M-d-Y  H:i'));
		$nextrunupper->setTimezone($nextrun->getTimezone());
		$nextrunlower->setTimezone($nextrun->getTimezone());
		$nextrunupper->setTimestamp($nextrun->getTimestamp());
                $nextrunlower->setTimestamp($nextrun->getTimestamp());
		$nextrunupper->modify("+".$myepsilon." minutes");
		$nextrunlower->modify("-".$myepsilon." minutes");
		// Check if the algorithm just ran (based on the last real time it ran)
		$justran = false;
		if($my_last_working_groups_real_creation_time){
			error_log("Computing just ran");
			$lastruntimereal = new DateTime();
			$lastruntimereal->setTimezone($gmt);
                        $lastruntimereal->setTimestamp(intval($my_last_working_groups_real_creation_time));
			$lastruntimereal->setTimezone($etus);
			error_log($nextrunlower->format('M-d-Y  H:i')."<".$lastruntimereal->format('M-d-Y  H:i')."<".$nextrunupper->format('M-d-Y  H:i')); 
                        $justran=(($nextrunlower<$lastruntimereal)&&($lastruntimereal<$nextrunupper));
                }
		error_log("just ran = ".$justran);
		error_log($nextrunlower->format('M-d-Y  H:i')."<".$datenowetus->format('M-d-Y  H:i')."<".$nextrunupper->format('M-d-Y  H:i'));

		// Run if $nextrun-epsilon < now < $nextrun+epsilon and if the algorithm didn't just run
		//if($justran){
		if(($nextrunlower<$datenowetus) && ($datenowetus < $nextrunupper) && (!$justran)){
		//error_log("og cron 100000023 ".$mytimediff." ".my_cron_config_time_to_seconds($newnumdays));
		// Run if the config time changed and it's time to run the group script and it's the right day
		// or if enough days have passed since out last run(in case the run time has not changed) and it's the right day
		//if((($mytimediff>=(my_cron_config_time_to_seconds($newnumdays)-5))&&(!$runtimechanged)&&($shouldruntoday)&&($timetorun))||
		//   ($runtimechanged)&&$timetorun&&$shouldruntoday){
			// Update now, so that we don't run this twice if the algorithm runs slowly
                        update_option('my_last_working_groups_creation_time', $curtime);
			update_option('my_last_working_groups_real_creation_time', $curtime);
			// Set the time zone of this run's date and time to GMT and update the approximate last run date field in the DB 
			$nextrun->setTimezone($gmt);
			update_option( 'my_last_working_groups_creation_time', $nextrun->getTimestamp());
			// If the config time has changed and it's time to run, we want 
			// to run the algorithm and set the last run time to the config time
			// since curtime isn't exactly the config time.
			// If we set the last run time to curtime, it could cause issues since it shifts 
			// the desired time (and shifting the desired time multiple times could cause issues)
			// http://php.net/manual/en/datetime.gettimestamp.php
			/*if(($runtimechanged)&&$timetorun){
				update_option( 'my_last_working_groups_creation_time', $dateconfig->getTimestamp());
			}
			else{ 	// if it has been enough days since the algorithm ran
				// set my_last_working_groups_creation_time to 
				// my_last_working_groups_creation_time +  exactly x days
				// in order to prevent future shifts in time that can cause issues
				// (this is done because the actual run time is not exactly 
				// my_last_working_groups_creation_time +  exactly x days)
				$thisruntime = new DateTime();
				$thisruntime->setTimezone($gmt);
                		$thisruntime->setTimestamp($my_last_working_groups_creation_time_int);
				$thisruntime-> modify('+'.$newnumdays.' day');
				update_option( 'my_last_working_groups_creation_time', $thisruntime->getTimestamp());
			}*/
			// Update now, so that we don't run this twice if the algorithm runs slowly
			//update_option( 'my_last_working_groups_creation_time', $curtime);
			my_create_working_groups_fnc($my_last_working_groups_real_creation_time);
			error_log("og cron 10000013");
			// TODO: change curtime to midnight, in order to get the script to run at midnight every day
			//update_option( 'my_last_working_groups_creation_time', $curtime);
		}
	}
	//wp_clear_scheduled_hook('my_create_working_groups');
	//wp_clear_scheduled_hook('schedulegroupcreationjob');
}

// This is an old function that's no longer used
// If the group creation interval change, reschedule the events accordingly
// The first run will happen tonight
//function my_check_group_creation_schedule_change_fnc2(){
//	//  http://codex.wordpress.org/Function_Reference/wp_get_schedule
//	$myschedule = wp_get_schedule('my_create_working_groups');
//	error_log("og cron reschedule ".$myschedule);
//	if($myschedule){
//		$newnumdays = MY_CREATE_WORKING_GROUP_EVERY_X_DAYS;
//		$newnumdaysschedule = 'every'.$newnumdays.'minutes';
//		error_log("og cron reschedule times 2 ".$myschedule." ".$newnumdaysschedule);
//		if($myschedule!=$newnumdaysschedule){
//			error_log("og cron reschedule times 21 ".$myschedule." ".$newnumdaysschedule);
//			// http://codex.wordpress.org/Function_Reference/wp_next_scheduled
//			$timestamp = wp_next_scheduled('my_create_working_groups');
//			// time()
//			// http://codex.wordpress.org/Function_Reference/wp_unschedule_event
//			// http://codex.wordpress.org/Function_Reference/wp_reschedule_event
//			//wp_reschedule_event(time(),$newnumdaysschedule,'my_create_working_groups');
//			// http://www.onlineconversion.com/unix_time.htm
//                	// http://codex.wordpress.org/Function_Reference/wp_unschedule_event
//        		wp_unschedule_event(wp_next_scheduled('my_create_working_groups'), 'my_create_working_groups');
//			// http://codex.wordpress.org/Function_Reference/wp_clear_scheduled_hook
//			wp_clear_scheduled_hook('my_create_working_groups');
//			//schedulegroupcreation();
//			// We can't call schedulegroupcreation immediately after removing the hook because then it will be removed
//			// http://codex.wordpress.org/Function_Reference/wp_schedule_single_event
//			// http://stackoverflow.com/questions/3052865/add-30-seconds-to-the-time-with-php
//			//wp_schedule_single_event(time()+100, 'schedulegroupcreationjob');
//		}
//	}
//}

// This action calls the my_check_group_creation_schedule_change_fnc function when my_check_group_creation_schedule_change
// fires (every minute) to check whethere the group creation algorithm needs to run
// Taken from http://codex.wordpress.org/Function_Reference/wp_schedule_event
// KR: disable "working group" feature
// add_action('my_check_group_creation_schedule_change', 'my_check_group_creation_schedule_change_fnc');

// This function sets up an even that gets called every minute to check whether the group creation algorithm needs to run
function my_start_checking_group_creation_schedule_change(){
	//error_log('og cron 500000023 ');
	//error_log('og cron 400000023 '.wp_next_scheduled('my_check_group_creation_schedule_change')." ".time());
	// http://codex.wordpress.org/Function_Reference/wp_clear_scheduled_hook
	//wp_clear_scheduled_hook('my_check_group_creation_schedule_change');
	// http://codex.wordpress.org/Function_Reference/wp_get_schedule
	if ( ! wp_next_scheduled('my_check_group_creation_schedule_change')){
		error_log('og cron 300000023 '.wp_next_scheduled('my_check_group_creation_schedule_change'));
		wp_schedule_event(time(), 'every1minutes', 'my_check_group_creation_schedule_change');
		error_log('og cron 300000023 '.wp_next_scheduled('my_check_group_creation_schedule_change'));
	}
	//wp_unschedule_event(wp_next_scheduled('my_check_group_creation_schedule_change'), 'my_check_group_creation_schedule_change');

}

// Whenever a user accesses the site, call my_start_checking_group_creation_schedule_change to check if we need to create the groups
// NOTE: Currently we have an cron job set up to access the site every minute so that this action happens every minute, even when there are no users
// on the site. This way, the grouping algorithm will run even when there are no users on the site.  
// Copied from http://codex.wordpress.org/Function_Reference/wp_schedule_event
// http://codex.wordpress.org/Plugin_API/Action_Reference/wp
// http://codex.wordpress.org/Plugin_API/Action_Reference
//add_action( 'wp', 'schedulegroupcreation' );
//add_action( 'schedulegroupcreationjob', 'schedulegroupcreation' );
add_action( 'wp', 'my_start_checking_group_creation_schedule_change' );

// Custom cron event's won't work without this function
// wp-includes/cron.php
function my_schedule_event($event){
	if(($event->hook=='my_check_group_creation_schedule_change')||($event->hook=='my_create_working_groups')||($event->hook=='schedulegroupcreationjob')){
		return $event;
	}
}
add_filter('schedule_event', 'my_schedule_event',20,1);


// Computes and returns the next day (after the datetime $fromdate) on which to run the algorithm, based on the settings
// If such a day doesn't exist, returns false (like many wordpress functions)
function compute_next_run_day($fromdate){
	$daysinaweek = 7; // The number of days in a week (in case none of the days are set to true, we don't want to get stuck in an infinite loop)
	// Copy the date
	$startdate = new DateTime();
	$startdate -> setTimezone($fromdate->getTimezone());
	$startdate -> setTimestamp($fromdate->getTimestamp());
	//$now = new DateTime();
	//$startdate -> setTimezone($fromdate->getTimezone());

	$dayfound=false;
	$idx = 0;
	while((!$dayfound)&&($idx<$daysinaweek)){
		// Get the date's day iof the week as text
        	// http://php.net/manual/en/function.date.php
        	// http://php.net/manual/en/datetime.modify.php
        	$dayofweektext = $startdate->format('l');
		//$dayofweeknumber = $startdate->format('w');
		$dayfound=false;
		// http://php.net/manual/en/function.strtoupper.php
        	$dayofweektextupper = strtoupper($dayofweektext);
		$dayofweekrunsetting = 'MY_WORKING_GROUP_RUN_'.$dayofweektextupper; // the setting that specifies whether to run the script today
                // http://php.net/manual/en/function.array-push.php
                //array_push($daystorun, 'MY_WORKING_GROUP_RUN_DAY_'.$i);
                // https://codex.wordpress.org/Changing_The_Site_URL
                // http://php.net/manual/en/function.constant.php
                // Also, taken from code of plugins that check if a constant is defined
                if(defined($dayofweekrunsetting)){
                        // http://php.net/manual/en/function.constant.php
                        $dayfound = constant($dayofweekrunsetting);
			//echo "found day:".$dayofweektextupper;
                }
		// Could be false or not in the settings
		if(!$dayfound){
			///echo "day:".$dayofweektextupper;
			// Go to the next day in order to check if the algorithm should run then
			// http://php.net/manual/en/datetime.modify.php
			$startdate-> modify('+1 day');
		}
		$idx++;
	}
	// Inspired by wordpress (its functions often return false in cases like this)
	if(!$dayfound){
		return false;
	}
	else{
		//echo " run day:".$dayofweektextupper;
		return $startdate;
	}
	
}

// Display the dates for the group survey
// Taken from wp-content/plugins/quiz-master-next/includes/qmn_quiz.php
function qmn_my_begin_quiz_form($quiz_display, $qmn_quiz_options, $qmn_array_for_variables){
	//echo "test dates comment";
	//http://wordpress.stackexchange.com/questions/113048/what-is-the-best-way-to-store-custom-variables
        //http://codex.wordpress.org/Function_Reference/get_option
        //http://codex.wordpress.org/Function_Reference/update_option
	//$newnumdays = MY_CREATE_WORKING_GROUP_EVERY_X_DAYS;
	// Copied from http://codex.wordpress.org/Function_Reference/update_option
	// Taken from http://php.net/manual/en/datetime.createfromformat.php
	// and http://php.net/manual/en/datetimezone.construct.php
	// and http://php.net/manual/en/datetimezone.listabbreviations.php
	// http://php.net/manual/en/datetimezone.construct.php
	// http://php.net/manual/en/datetime.settimezone.php
	// and http://php.net/manual/en/function.timezone-abbreviations-list.php user contributed commen #2
	// and http://us.php.net/manual/en/timezones.others.php
	// http://php.net/manual/en/class.datetime.php
	// http://php.net/manual/en/datetime.settime.php
	// http://php.net/manual/en/datetime.modify.php
	// http://php.net/manual/en/datetime.gettimestamp.php
	$gmt = new DateTimeZone("GMT");
	$etus = new DateTimeZone(MY_WORKING_GROUP_TIME_ZONE);
	$my_last_working_groups_creation_time = get_option( 'my_last_working_groups_creation_time');
	$my_last_real_working_groups_creation_time = get_option( 'my_last_working_groups_real_creation_time');
	if($my_last_working_groups_creation_time){
		// http://php.net/manual/en/function.time.php
		// http://php.net/manual/en/function.intval.php
		$my_last_working_groups_creation_time_int = intval($my_last_working_groups_creation_time);
		$lastruntime = new DateTime();
		$lastruntimeeps = new DateTime(); // last run time plus epsilon
		$thisruntime = new DateTime();
		$todaysrun = new DateTime();
		$lastruntime->setTimezone($gmt);
		$lastruntimeeps->setTimezone($gmt);
		$thisruntime->setTimezone($gmt);
		$todaysrun->setTimezone($gmt);
		// This run time will be set to my_last_working_groups_creation_time_int + newnumdays
		$thisruntime->setTimestamp($my_last_working_groups_creation_time_int);
		// If we have the real last run time, we use it. If we don't, we use the approximate time (that was set based on the desired run time)
		$lastruntime->setTimestamp($my_last_working_groups_creation_time_int);
		if($my_last_real_working_groups_creation_time){
			$lastruntime->setTimestamp(intval($my_last_real_working_groups_creation_time));
		}
		/*else{
			$lastruntime->setTimestamp($my_last_working_groups_creation_time_int);
		}*/
		// http://php.net/manual/en/function.date.php
		
		$todaysrun->setTimezone($etus);
		// http://php.net/manual/en/class.datetime.php
		$todaysrun->setTime(MY_WORKING_GROUP_RUN_HOUR, MY_WORKING_GROUP_RUN_MINUTE);	
		$now = new DateTime();
		$now->setTimezone($etus);
		// Check if the algorithm just ran
		$myepsilon = MY_WORKING_GROUP_EPSILON; // epsilon for the scheduled time to run the script
		$myepsilonrangetime = $myepsilon*2+1;
		$justran = false;
		if($my_last_real_working_groups_creation_time){
                        $lastruntimeeps->setTimestamp(intval($my_last_real_working_groups_creation_time));
			$lastruntimeeps->modify("+".$myepsilon." minutes");
			$justran=($now<$lastruntimeeps);
                }
		// Today's run must be in the future
		// If changing the time took us to the past or if the algorithm just ran, we must add a day
		//if($now>$todaysrun){
		if(($now>$todaysrun)||($justran)){
			// http://php.net/manual/en/datetime.modify.php
			$todaysrun-> modify('+1 day');	
		}
		
		// get the approximate time we are scheduled to run the algorithm, based on the day in the settings
		$nextrun = compute_next_run_day($todaysrun); //$thisruntime->modify('+'.$newnumdays.' day');
		// Taken from http://php.net/manual/en/datetime.createfromformat.php
		// and http://php.net/manual/en/datetimezone.construct.php
		// and http://php.net/manual/en/datetimezone.listabbreviations.php
		// http://php.net/manual/en/datetimezone.construct.php
		// http://php.net/manual/en/datetime.settimezone.php
		// and http://php.net/manual/en/function.timezone-abbreviations-list.php user contributed commen #2
		// and http://us.php.net/manual/en/timezones.others.php
		// http://php.net/manual/en/class.datetime.php
		// http://php.net/manual/en/datetime.settime.php
		// http://php.net/manual/en/datetime.modify.php
		// http://php.net/manual/en/datetime.settimestamp.php
		// http://php.net/manual/en/dateinterval.createfromdatestring.php
		// http://php.net/manual/en/function.date.php
		echo "<div class='quizdates'>";
		if($nextrun!=false){
			// Set the timezone back to GMT, in order to display it to the user
			$nextrun -> setTimezone($gmt);
			echo " <div class='quizdatesdebug'> Approximate questionnaire cycle dates: <br/>". $lastruntime->format('M-d-Y  H:i')." GMT - ". $nextrun->format('M-d-Y  H:i')." GMT <br/></div>";
			// We subtract the epsilon range in order to make sure the user will always know to submit their preferences before the algorithm runs
			$nextrun-> modify('-'.$myepsilonrangetime.' minute');
			echo "Please submit your preferences BEFORE ".$nextrun->format('M-d-Y H:i')." GMT if you would like to be placed in a group in this cycle. <br/>";
		}
		echo "If you submit the questionnaire multiple times during this cycle we will use your last submission. <br/> </div>";
	}
	// og remove 	
	return $quiz_display;
}

add_filter('qmn_begin_quiz_form', 'qmn_my_begin_quiz_form', 20, 3);

/////////////////////////////// Group UI 

// taken from wp-content/plugins/buddypress/bp-core/bp-core-filters.php
function my_get_group_create_button($paramarray){
	$btntxt = __( 'Create an Affinity Group', 'buddypress' );
	$paramarray['link_text'] =  $btntxt;
	$paramarray['link_title'] =  $btntxt;
	return $paramarray;
}

add_filter('bp_get_group_create_button', 'my_get_group_create_button', 20, 1);

// Show hidden groups (when needed). Currently they are not shown to their users due to css!!
// wp-content/plugins/buddypress/bp-groups/bp-groups-template.php
// bp_get_group_class (the filter below is called by bp_get_group_class in wp-content/plugins/buddypress/bp-groups/bp-groups-template.php)
// Other helpful links:
// https://bbpress.org/forums/topic/showing-buddypress-hidden-group-forums-to-group-members-on-bbpress-forum-index/
// https://buddypress.org/support/topic/showing-buddypress-hidden-group-forums-to-group-members-on-bbpress-forum-index/
// wp-content/plugins/buddypress/bp-groups/bp-groups-actions.php
// wp-content/plugins/buddypress/bp-groups/bp-groups-functions.php
function my_get_group_class($classes){
	for($i=0;$i<count($classes);$i++){
		if($classes[$i]=="hidden"){
			$classes[$i]="hidden-group";
		}
	}
	return $classes;
}
add_filter('bp_get_group_class', 'my_get_group_class', 20, 1);

////////////////////////////////////////// Post status ///////////////////////////////////////////
// sources:
// wp-content/plugins/bbpress/includes/topics/functions.php
// wp-content/plugins/bbpress/includes/core/functions.php
// wp-content/plugins/bbpress/includes/extend/buddypress/activity.php
// wp-includes/post.php
// wp-content/plugins/buddypress/bp-core/bp-core-template.php
// wp-admin/options-general.php
// wp-includes/meta.php
// wp-content/plugins/buddypress/bp-activity/bp-activity-functions.php
// wp-content/plugins/buddypress/bp-forums/bbpress/bb-includes/backpress/functions.wp-object-cache.php
// wp-admin/includes/ajax-actions.php
// wp-admin/includes/post.php
// wp-content/plugins/bbpress/includes/forums/functions.php
// wp-content/plugins/bbpress/includes/admin/topics.php
// wp-content/plugins/bbpress/includes/core/functions.php
// wp-content/plugins/bbpress/includes/topics/functions.php
// wp-content/plugins/bbpress/includes/extend/buddypress/activity.php
// Other wp, bbp, bp and plugin source code 
function my_save_post_topic($post_ID, $post, $update){
	// error_log("og in my_save_post_topic");
	if(($post==null)||(empty($post))){
		return;
	}
	if($update){
		return;
	}
	// We only care about topics that are created closed
	if($post->post_status==bbp_get_closed_status_id()){
		// For some reason bbp forgets to add this meta value to posts that are created closed.
		// This causes issues (i.e. when the user tries to open a topic that was created closed, the topic GETS DELETED!
		// Basically, bbp adds this meta tag to closed topics. When the user tries to open the topic and
		// bbp fails to find this meta tag, bbp deletes the topic, assuming that the metatag doesn't exist because 
		// the topic is invalid).
		// The code below fixes this problem
		// Copied from bbp_close_topic in wp-content/plugins/bbpress/includes/topics/functions.php
		// Add pre close status
		// NOTE: we are setting the meta status to 'publish', because the meta status is meant to preserve the
		// pre closing status. Since this post was created closed, we are adding a fake previous status
		// that will make bbp think that the topic was published before. This way, if we try to publish
		// this closed post at some point, it will be possible (basically, this is the status that 
		// the topic gets set to when it is initially published as open and then it is closed, which works fine).
		// Taken from wp-content/plugins/bbpress/includes/core/functions.php
		add_post_meta( $post_ID, '_bbp_status', bbp_get_public_status_id() );
	}
}
 
add_action('save_post_topic', 'my_save_post_topic',20,3);

// Add a random number to the end of the file's name, because file name conflicts are only handled locally which causes issues 
// when the system runs on multiple servers
// Sources:
// wp-content/plugins/bbpress/includes/replies/functions.php
// bbp_new_reply_handler
// action=bbp-new-reply
// wp-content/plugins/bbpress/includes/core/actions.php bbp_post_request ->bbp_new_reply_handler
// wp-content/plugins/bbpress/includes/replies/functions.php bbp-new-reply->bbp_new_reply_handler
// wp-content/plugins/gd-bbpress-attachments/code/attachments/front.php
// bbp_new_reply->save_reply
// wp-admin/includes/file.php wp_handle_upload
// wp-includes/functions.php wp_unique_filename
// http://php.net/manual/en/function.basename.php
// http://php.net/manual/en/reserved.variables.files.php
// http://php.net/manual/en/features.file-upload.post-method.php
// Other wp, bbpress, buddypress and gd-bbpress-attachments code
// http://php.net/manual/en/function.rand.php
function my_handle_upload_prefilter_fnc($file){
	// Copied from wp-includes/functions.php wp_unique_filename
	$filename = sanitize_file_name($file['name']);
	$info = pathinfo($filename);
	$ext = !empty($info['extension']) ? '.' . $info['extension'] : '';
	$nm = basename($filename, $ext);
	if ( $nm === $ext )
		$nm = '';
	//$nm = $file['name'];
	$file['name'] = $nm.rand(1000,getrandmax()).$ext;
	//error_log("og prefilter ".$nm." ".$ext." ".$filename." ".rand(1000,getrandmax())." ".print_r($file, true));
	return $file;
}

// https://developer.wordpress.org/reference/functions/add_filter/
add_filter('wp_handle_upload_prefilter', 'my_handle_upload_prefilter_fnc', 20, 1);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////    Group Email Notification /////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Remove the email notification tab from groups
// Copied from:
// http://wordpress.stackexchange.com/questions/14469/remove-tabs-from-buddypress-groups-and-members-pages
// and https://buddypress.org/support/topic/removing-menus-not-working-in-buddypress-1-5-help-please/
// https://buddypress.org/support/topic/removing-group-extension-link-from-the-admin-subnav-menu/
// http://wordpress.stackexchange.com/questions/13707/variable-from-a-plugin-into-a-theme
// https://github.com/fpcorso/quiz_master_next/blob/master/includes/qmn_addons.php
// http://php.net/manual/en/function.array-key-exists.php
// Other useful resources:
// http://stackoverflow.com/questions/9682521/how-do-i-get-the-nth-child-of-an-element-using-css2-selectors
// http://www.w3.org/TR/CSS21/selector.html#adjacent-selectors
// https://codex.buddypress.org/legacy/developer-resources/group-extension-api-prior-to-bp-1-8/
// http://www.generalthreat.com/2011/10/creating-a-buddypress-group-home-page/
// https://codex.buddypress.org/developer/buddypress-action-hook-sequence-during-startup/
// wp-content/plugins/buddypress/bp-core/bp-core-buddybar.php
// wp-content/plugins/buddypress/bp-groups/bp-groups-classes.php
// buddypress and wp source code and other code in this file 
function my_remove_group_tabs(){
	global $bp;
	//echo "abc".$bp->groups->slug.print_r($bp->bp_options_nav[bp_get_current_group_slug()], true);
	//bp_core_remove_subnav_item($bp->groups->slug, 'send-invites');

  // KR: added this since in bootstrapping procedure for new forum site,
  // this throws an error.
  if (function_exists('bp_get_current_group_slug')) {
	  bp_core_remove_subnav_item(bp_get_current_group_slug(), 'notifications');
  }
}

// https://codex.wordpress.org/Function_Reference/add_action
//add_action('bp_setup_nav', 'my_remove_group_tabs');
// NOTE: bp_setup_nav doesn't work because the removed menu item gets added after bp_setup_nav
// see https://codex.buddypress.org/developer/buddypress-action-hook-sequence-during-startup/ for more information
add_action('bp_screens', 'my_remove_group_tabs',99999);
?>
