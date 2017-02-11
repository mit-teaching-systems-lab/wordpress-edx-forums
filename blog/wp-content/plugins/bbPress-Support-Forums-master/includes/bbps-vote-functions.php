<?php
/* 
bbps - vote functions
*/


add_action('bbp_template_before_topics_loop', 'dtbaker_vote_bbp_template_before_topics_loop');
function dtbaker_vote_bbp_template_before_topics_loop(){
    // a tab to display resolved or unresilved voted items within this forum.
    $forum_id = bbp_get_forum_id();
    if(bbps_is_voting_forum($forum_id)){
        ?>
        <a href="<?php echo add_query_arg(array('show_resolved'=>0), bbp_get_forum_permalink($forum_id));?>">Pending Feature Requests</a> |
        <a href="<?php echo add_query_arg(array('show_resolved'=>1), bbp_get_forum_permalink($forum_id));?>">Resolved Requests</a>
        <?php
    }
}
add_filter('bbp_topic_pagination' , 'dtbaker_vote_bbp_topic_pagination' , 10 , 1);
function dtbaker_vote_bbp_topic_pagination($options){
	if (bbps_is_voting_forum(bbp_get_forum_id())){
        if(isset($_REQUEST['show_resolved']) && $_REQUEST['show_resolved']){
            $options['add_args']=array('show_resolved'=>1);
        }
    }
    return $options;
}

add_action('bbp_template_before_single_topic', 'bbps_add_voting_forum_features');
function bbps_add_voting_forum_features(){
	//only display all this stuff if the support forum option has been selected.
	if (bbps_is_voting_forum(bbp_get_forum_id())){
        $topic_id = bbp_get_topic_id();
        $forum_id = bbp_get_forum_id();
        $user_id = get_current_user_id();


        if ( (isset($_GET['action']) && isset($_GET['topic_id']) && $_GET['action'] == 'bbps_vote_for_topic')  )
            bbps_vote_topic();

        if ( (isset($_GET['action']) && isset($_GET['topic_id']) && $_GET['action'] == 'bbps_unvote_for_topic')  )
            bbps_unvote_topic();

        $votes = bbps_get_topic_votes($topic_id);
        ?>
        <div class="row">
        <div id="bbps_voting_forum_options" class="col-md-6">
            <div class="well">
            <?php
        //get out the option to tell us who is allowed to view and update the drop down list.
         ?>
            Votes: <?php echo count($votes);
            if(is_user_logged_in()){
            if(in_array($user_id,$votes)){
                $vote_uri = add_query_arg( array( 'action' => 'bbps_unvote_for_topic', 'topic_id' => $topic_id ) );
                ?>
                Vote Successful. Thanks! (<a href="<?php echo $vote_uri;?>">undo vote</a>)
            <?php }else{
                $vote_uri = add_query_arg( array( 'action' => 'bbps_vote_for_topic', 'topic_id' => $topic_id ) );
                ?>
                <a href="<?php echo $vote_uri;?>" class="btn btn-primary">Vote for this!</a>
                <?php }
            }else{
                echo '(please login to vote)';
            }?>
        </div>
        </div>
        </div> <!-- row -->
    <?php
	}
}


function bbps_get_topic_votes($topic_id){
	$votes = trim(get_post_meta( $topic_id, '_bbps_topic_user_votes', true ));
    if(strlen($votes)){
        $votes = explode(',',$votes);
    }else{
        $votes = array();
    }
	//to do not hard code these if we let the users add their own satus
	return $votes;
}


// adds a class and status to the front of the topic title
function bbps_modify_vote_title($title, $topic_id = 0){
    $topic_id = bbp_get_topic_id( $topic_id );
    $forum_id = bbp_get_forum_id();
    if(bbps_is_voting_forum($forum_id)){
        $votes = bbps_get_topic_votes($topic_id);
        if(count($votes)){
            echo ' <span class="badge badge-info">Votes: '.count($votes) .'</span> ';
        }
    }

}
add_action('bbp_theme_before_topic_title', 'bbps_modify_vote_title');



function bbps_vote_topic(){
    if(is_user_logged_in()){
        $user_id = get_current_user_id();
        if($user_id){
            $topic_id = bbp_get_topic_id();
            $forum_id = bbp_get_forum_id();
            if(bbps_is_voting_forum($forum_id)){
                $votes = bbps_get_topic_votes($topic_id);
                if(!in_array($user_id, $votes)){
                    $votes[]=$user_id;
                    update_post_meta($topic_id, '_bbps_topic_user_votes', implode(',',$votes));
                    update_post_meta($topic_id, '_bbps_topic_user_votes_count', count($votes));
                }
            }
        }
    }
}

function bbps_unvote_topic(){
	if(is_user_logged_in()){
        $user_id = get_current_user_id();
        if($user_id){
            $topic_id = bbp_get_topic_id();
            $forum_id = bbp_get_forum_id();
            if(bbps_is_voting_forum($forum_id)){
                $votes = bbps_get_topic_votes($topic_id);
                $key = array_search($user_id, $votes);
                if($key !== false){
                    unset($votes[$key]);
                    update_post_meta($topic_id, '_bbps_topic_user_votes', implode(',',$votes));
                    update_post_meta($topic_id, '_bbps_topic_user_votes_count', count($votes));
                }
            }
        }
    }
}

function dtbaker_filter_topics_vote_custom_order($clauses) {
    global $wp_query;
    // check for order by custom_order

    //if($_SERVER['REMOTE_ADDR'] == '124.191.165.183'){
    //print_r($wp_query);
        //echo '<pre>';
        /*if($_SERVER['REMOTE_ADDR'] == '124.191.165.183'){
            echo '<pre>';
            print_r($clauses);
        }*/
        if(preg_match('#([a-zA-Z_0-9]*postmeta)\.meta_key = \'_bbps_topic_user_votes_count\'#',$clauses['where'],$matches)){
            //print_r($clauses);
            //print_r($matches);
            // change the inner join to a left outer join,
            // and change the where so it is applied to the join, not the results of the query
            // ON (all_5_posts.ID = all_5_postmeta.post_id)
            $clauses['where'] = preg_replace('#\n#',' ',$clauses['where']);
            $join_matches = preg_split("#\n#",$clauses['join']);
                $clauses['join'] = '';
                /*if($_SERVER['REMOTE_ADDR'] == '124.191.165.183'){
                    print_r($join_matches);
                }*/
                foreach($join_matches as $join_match_id => $join_match){
                    if(strpos($join_match,$matches[1].'.post_id') !== false){
                        $join_matches[$join_match_id] = str_replace('INNER JOIN','LEFT OUTER JOIN',$join_matches[$join_match_id]);
                        $clauses['where'] = str_replace($matches[0],'1',$clauses['where']);
                        $join_matches[$join_match_id] .= ' AND '.$matches[0].' ';
                    }
                    $clauses['join'] .= $join_matches[$join_match_id].' ';
                }
                $clauses['where'] = str_replace('1 OR ','',$clauses['where']);

            //print_r($clauses);
        }
    /*if($_SERVER['REMOTE_ADDR'] == '124.191.165.183'){
        print_r($clauses);
        echo '</pre>';
    }*/

   /* }else{
        //if ($wp_query->get('meta_key') == '_bbps_topic_user_votes_count' && $wp_query->get('orderby') == 'meta_value_num')
        if(preg_match('#([a-zA-Z_0-9]*postmeta)\.meta_key = \'_bbps_topic_user_votes_count\'#',$clauses['where'],$matches)){
            // change the inner join to a left outer join,
            // and change the where so it is applied to the join, not the results of the query
            // ON (all_5_posts.ID = all_5_postmeta.post_id)
            $clauses['join'] = preg_replace('#INNER JOIN#', 'LEFT OUTER JOIN', $clauses['join']).$clauses['where'];
            //print_r($matches);
            //$clauses['where'] = str_replace($matches[0], $matches[0] .' OR '.$matches[1].'.meta_key IS NULL', $clauses['where']); //.$clauses['where'];
            $clauses['where'] = '';
        }
    }*/
    return $clauses;
}
add_filter('get_meta_sql', 'dtbaker_filter_topics_vote_custom_order', 10, 1);
/*function dtbaker_filter_topics_vote_custom_order_by($orderby) {

    $forum_id = bbp_get_forum_id();
    echo 'Forum: '.$forum_id;
    if($forum_id && bbps_is_voting_forum($forum_id)){
        $orderby .= '';
    }
    return $orderby;
}
add_filter('posts_orderby', 'dtbaker_filter_topics_vote_custom_order_by', 10, 1);*/
function bbps_filter_bbp_after_has_topics_parse_args($args){
    $forum_id = bbp_get_forum_id();
    if($forum_id && bbps_is_voting_forum($forum_id)){
        //if($_SERVER['REMOTE_ADDR'] == '124.191.165.183'){

        $args['meta_query'] = array();
        if(isset($_REQUEST['show_resolved']) && $_REQUEST['show_resolved']){
            $args['meta_query'][] = array(
                'key' => '_bbps_topic_status',
                'value' => 2,
                'compare' => '='
            );
        }else{
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = '_bbps_topic_user_votes_count';
            $args['order'] = 'DESC';
            $args['meta_query'] = array(
                'relation' => 'OR',
                array(
                    'key' => '_bbps_topic_status',
                    'compare' => 'NOT EXISTS',
                    'value' => '2',
                ),
                array(
                    'key' => '_bbps_topic_status',
                    'value' => 2,
                    'compare' => '!='
                )
            );
        }
        //}
    }
    return $args;
}
add_filter('bbp_after_has_topics_parse_args','bbps_filter_bbp_after_has_topics_parse_args',10,1);
