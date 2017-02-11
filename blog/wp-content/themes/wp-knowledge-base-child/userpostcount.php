<?php

/**
 * Template Name: UserPostCount
 * Copied from page.php (the original theme's template) and http://codex.wordpress.org/Page_Templates
 * The template for displaying all pages.
 *
 * @package bbPress
 */
get_header( 'countuserposts' ); ?>
	<div id="chart_div" style="width: 100%; height: 500px;"></div>
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<?php 
			/*echo print_r($allusersusers,true);
			foreach($allusersusers as $userinfo){
                                        // Taken from http://codex.wordpress.org/Class_Reference/WP_User
                                        //echo "<p>".print_r($userinfo,true)."</p>";
                                        $userid = $userinfo->ID;
                                        $username = $userinfo->user_login;
                                        $userdispname= $userinfo->display_name;                                        
					// https://buddypress.org/support/topic/show-bbpress-topicreply-counts-on-user-profile/
                                        // wp-content/plugins/bbpress/includes/users/options.php
                                        // wp-content/plugins/bbpress/includes/users/functions.php
                                        // wp-content/plugins/buddypress/bp-members/bp-members-functions.php
                                        $topiccount = bbp_get_user_topic_count_raw($userid);
                                        $replycount = bbp_get_user_reply_count_raw($userid);
                                        $postcount = $topiccount+$replycount;
                                        //echo ",\n";
                                        //echo "['".$userdispname."', ".$postcount."]";
                                        echo "<p>".$userid." ".$username." ".$userdispname." ".$postcount."</p>";


                                }*/
			?>

		</main><!-- #main -->
	</div><!-- #primary -->
<?php get_footer(); ?>

