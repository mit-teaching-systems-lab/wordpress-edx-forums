<?php

/**
 * Template Name: UserPostCountbkup
 * Copied from page.php (the original theme's template) and http://codex.wordpress.org/Page_Templates
 * The template for displaying all pages.
 *
 * @package bbPress
 */
get_header( 'bbpress' ); ?>
	<div id="primary" class="content-area col-md-8">
		<main id="main" class="site-main" role="main">
			<!-- Taken from https://developers.google.com/chart/interactive/docs/gallery/histogram -->
				<?php 
				$paddingpx = 5;
				$allusers = bp_core_get_users(array('per_page'=>0));
				$allusersusers = $allusers["users"];
				$sortedusers = array();
				//echo print_r($allusers,true); 
				echo "<table>";
				echo "<tr><th style='padding:".$paddingpx."px'>Username</th><th style='padding=".$paddingpx."px'>Post count</th></tr>";
				foreach($allusersusers as $userinfo){
					// Taken from http://codex.wordpress.org/Class_Reference/WP_User
					//echo "<p>".print_r($userinfo,true)."</p>";
					$userid = $userinfo->ID;
					$username = $userinfo->user_login;
					$userdispname= $userinfo->display_name;
					// https://buddypress.org/support/topic/show-bbpress-topicreply-counts-on-user-profile/
					// wp-content/plugins/bbpress/includes/users/options.php
					// wp-content/plugins/bbpress/includes/users/functions.php
					$topiccount = bbp_get_user_topic_count_raw($userid);
					$replycount = bbp_get_user_reply_count_raw($userid);
					$postcount = $topiccount+$replycount;
					// http://php.net/manual/en/function.array-push.php
					$sortedusers[$username]=$postcount;
				}
				// http://php.net/manual/en/function.asort.php
				// http://php.net/manual/en/function.arsort.php
				// http://php.net/manual/en/array.sorting.php
				arsort($sortedusers);
				foreach($sortedusers as $username=>$postcount){
					echo "<tr><td style='padding:".$paddingpx."px'>".$username."</td> <td style='padding:".$paddingpx."px'>".$postcount."</td></tr>";
				}
				echo "</table>";
				//endwhile; // end of the loop. 
			?>
		</main><!-- #main -->
	</div><!-- #primary -->
<?php get_sidebar( 'bbpress' ); ?>
<?php get_footer(); ?>

