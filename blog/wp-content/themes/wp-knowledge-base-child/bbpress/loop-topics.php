<?php

/**
 * Topics Loop
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<?php if ( !bbp_is_topic_tag() && !strpos($_SERVER['REQUEST_URI'], '/forums/view/') ) { ?>
  <?php do_action( 'bbp_template_before_topics_loop' ); ?>
  <a href="<?php echo add_query_arg(array('order'=>'DESC'), bbp_get_forum_permalink(bbp_get_forum_id()));?>"
  <?php if(isset($_REQUEST['order']) && $_REQUEST['order'] && $_REQUEST['order'] == 'DESC') echo 'class="order-active"'?>
  >Newest</a> |
  <a href="<?php echo add_query_arg(array('order'=>'ASC'), bbp_get_forum_permalink(bbp_get_forum_id()));?>"
  <?php if(isset($_REQUEST['order']) && $_REQUEST['order'] && $_REQUEST['order'] == 'ASC') echo 'class="order-active"'?>
  >Oldest</a> |
  <a href="<?php echo add_query_arg(array('orderby'=>'most_replies'), bbp_get_forum_permalink(bbp_get_forum_id()));?>"
  <?php if(isset($_REQUEST['orderby']) && $_REQUEST['orderby'] && $_REQUEST['orderby'] == 'most_replies') echo 'class="order-active"'?>
  >Most Replies</a>
  <?php if(bbps_is_voting_forum(bbp_get_forum_id())){ ?>
    | <a href="<?php echo add_query_arg(array('orderby'=>'most_votes'), bbp_get_forum_permalink(bbp_get_forum_id()));?>"
    <?php if(isset($_REQUEST['orderby']) && $_REQUEST['orderby'] && $_REQUEST['orderby'] == 'most_votes') echo 'class="order-active"'?>
    >Most Votes</a>
  <?php } ?>
<?php } ?>
<div id="bbp-forum-<?php bbp_forum_id(); ?>" class="bbp-topics">
<?php
if(bbp_get_forum_topic_count()>0)
    {

        $bbp_loop_args = array(
        'meta_key' => '_bbp_last_active_time', // Make sure topic has some last activity time
        'orderby' => 'meta_value', // 'meta_value', 'author', 'date', 'title', 'modified', 'parent', rand',
        'order' => 'DESC', // 'ASC', 'DESC'
        /**'orderby' => 'date',
        'order' => 'DESC',**/
        );
        if(isset($_REQUEST['order']) && $_REQUEST['order']){
           if ($_REQUEST['order'] == 'ASC'){
               $bbp_loop_args['order']='ASC';}
           else
                {$bbp_loop_args['order']='DESC';}
        }
        if(isset($_REQUEST['orderby']) && $_REQUEST['orderby']){
          if($_REQUEST['orderby']=='most_replies')
            {
            $bbp_loop_args['meta_key']='_bbp_reply_count';
            $bbp_loop_args['orderby']='meta_value_num';
            }
        }
        if(isset($_REQUEST['orderby']) && $_REQUEST['orderby']){
          if($_REQUEST['orderby']=='most_votes' && bbps_is_voting_forum(bbp_get_forum_id()) )
                {
                 $bbp_loop_args['orderby'] = 'meta_value_num';
                 $bbp_loop_args['meta_key'] = '_bbps_topic_user_votes_count';
                 $bbp_loop_args['order'] = 'DESC';
                 $bbp_loop_args['meta_query'] = array(
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
         }
   }
?>

<ul id="bbp-forum-<?php bbp_forum_id(); ?>" class="bbp-topics list-group">
        <li class="bbp-header list-group-item active">
                <ul class="forum-titles">
                        <li class="bbp-topic-title"><?php _e( 'Topics in this Forum', 'bbpress' ); ?></li>
                        <li class="bbp-topic-voice-count"><?php _e( 'Voices', 'bbpress' ); ?></li>
                        <li class="bbp-topic-reply-count"><?php bbp_show_lead_topic() ? _e( 'Replies', 'bbpress' ) : _e( 'Posts', 'bbpress' ); ?></li>
                        <li class="bbp-topic-freshness"><?php _e( 'Freshness', 'bbpress' ); ?></li>
                </ul>
                <div class="clearfix"></div>
        </li>

         <?php if ( strpos($_SERVER['REQUEST_URI'], '/forums/view/') || bbp_has_topics( $bbp_loop_args ) ) : ?>
                <?php while ( bbp_topics() ) : bbp_the_topic(); ?>

                        <?php bbp_get_template_part( 'loop', 'single-topic' ); ?>

                <?php endwhile; ?>
        <?php endif;?>

        <li class="bbp-footer list-group-item active">
                <ul class="forum-titles">
                        <li class="bbp-topic-title"><?php _e( 'Topics in this Forum', 'bbpress' ); ?></li>
                        <li class="bbp-topic-voice-count"><?php _e( 'Voices', 'bbpress' ); ?></li>
                        <li class="bbp-topic-reply-count"><?php bbp_show_lead_topic() ? _e( 'Replies', 'bbpress' ) : _e( 'Posts', 'bbpress' ); ?></li>
                        <li class="bbp-topic-freshness"><?php _e( 'Freshness', 'bbpress' ); ?></li>
                </ul>
                <div class="clearfix"></div>
        </li><!-- .bbp-footer -->

</ul><!-- #bbp-forum-<?php bbp_forum_id(); ?> -->

<?php do_action( 'bbp_template_after_topics_loop' ); ?>


