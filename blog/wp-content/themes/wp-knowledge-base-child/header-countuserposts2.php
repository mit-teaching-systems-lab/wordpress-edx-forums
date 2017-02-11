<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package iPanelThemes Knowledgebase
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<!-- Taken from https://developers.google.com/chart/interactive/docs/gallery/linechart -->
<script type="text/javascript"
          src="https://www.google.com/jsapi?autoload={
            'modules':[{
              'name':'visualization',
              'version':'1',
              'packages':['corechart']
            }]
          }"></script>
<script type="text/javascript">
                        google.setOnLoadCallback(drawChart);
                        function drawChart() {
                        	var data = google.visualization.arrayToDataTable([
                       		['username', 'numberofposts'] <?php
                                //$numpostsnumpeople =array();
				// wp-content/plugins/buddypress/bp-members/bp-members-functions.php
				// http://codex.wordpress.org/Function_Reference/get_users 
                                $allusers = bp_core_get_users(array('per_page'=>0));
                                //$allusers = bp_core_get_users(array('per_page'=>100));
				$allusersusers = $allusers["users"];
				$sortedusers = array();
		
                                //echo print_r($allusers,true); 
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
					// http://php.net/manual/en/function.array-push.php
                                        $sortedusers[$username]=$postcount;
				}
				
				// http://php.net/manual/en/function.asort.php
                                // http://php.net/manual/en/function.arsort.php
                                // http://php.net/manual/en/array.sorting.php
                                arsort($sortedusers);
                                foreach($sortedusers as $username=>$postcount){				
                                        echo ",\n";
                                        echo "['".$username."', ".$postcount."]";
                                        //echo "<p>".$userid." ".$username." ".$userdispname." ".$postcount."</p>";


                                }
                                echo "]);\n";
                                //endwhile; // end of the loop. 
                        	?>
                        	var options = {
                           	 title: 'Number of posts per user ('+<?php echo $allusers["total"]; ?>+' users)',
				 curveType: 'function',
                          	 legend: { position: 'bottom' },
                        	};

                   		var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
                   		chart.draw(data, options);
                	}
</script>
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
