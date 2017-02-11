<?php

/**
 * Template Name: UserGroupCount
 * Copied from page.php (the original theme's template) and http://codex.wordpress.org/Page_Templates
 * The template for displaying all pages.
 *
 * @package bbPress
 */
get_header( 'usergroupcount' ); ?>
        <div id="chart_div" style="width: 100%; height: 500px;"></div>
        <div id="primary" class="content-area">
                <main id="main" class="site-main" role="main">
                        <?php
                                // wp-content/plugins/buddypress/bp-members/bp-members-functions.php bp_core_get_users and other functions
                                // http://codex.wordpress.org/Function_Reference/get_users
                                $allusers = bp_core_get_users(array('per_page'=>0));
                                //$allusers = bp_core_get_users(array('per_page'=>100));
                                $allusersusers = $allusers["users"];
                                echo $allusers["total"]." users";
                                echo "<table>";
                                echo "<tr>"."<th> User ID</th> <th> Username </th><th> User Disp Name </th><th>Group Ids</th></tr>";
                                foreach($allusersusers as $myuser){
                                        // Taken from http://codex.wordpress.org/Class_Reference/WP_User
                                        $userid = $myuser->ID;
                                        $username = $myuser->user_login;
                                        $userdispname= $myuser->display_name;
                                        // BP_Group_Member_Query
                                        // bp_group_list_admins
                                        // wp-content/plugins/buddypress/bp-groups/bp-groups-template.php
                                        // bp_group_all_members_permalink
                                        // bp_get_group_all_members_permalink

                                        // wp-content/plugins/buddypress/bp-templates/bp-legacy/buddypress/groups/index.php
                                        // bp_get_total_group_count_for_user

                                        // groups/groups-loop

                                        // wp-content/plugins/buddypress/bp-groups/bp-groups-template.php 

                                        // Copied from:
                                        // wp-content/plugins/buddypress-docs/includes/access-query.php set_up_user_groups
                                        // wp-content/plugins/buddypress-group-tags/bp-group-tags.php
                                        // wp-content/plugins/buddypress/bp-groups/bp-groups-classes.php filter_user_groups
                                        // http://php.net/implode
                                        $theusergroups = BP_Groups_Member::get_group_ids( $userid );
					echo "<tr>"."<td>".$userid. "</td> <td>".$username."</td> <td>". $userdispname."</td> <td>".implode( ',', wp_parse_id_list($theusergroups['groups']))."</td></tr>";
                                }
                                echo "</table>";

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

