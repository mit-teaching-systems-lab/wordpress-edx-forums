<?php

/**
 * Topics Loop
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<?php do_action( 'bbp_template_before_topics_loop' ); ?>
<?php echo "<p>ECHO</p>"; ?>
 <a href="<?php echo add_query_arg(array('order'=>'ASC'), bbp_get_forum_permalink($forum_id));?>">Ascending</a> |
 <a href="<?php echo add_query_arg(array('orderby'=>'most_replies'), bbp_get_forum_permalink($forum_id));?>">MostReplies</a>
<?php if(bbps_is_voting_forum(bbp_get_forum_id())){ ?>
    <a href="<?php echo add_query_arg(array('orderby'=>'most_votes'), bbp_get_forum_permalink($forum_id));?>">Most Votes</a>
<?php } ?>
<?php    if(bbps_is_voting_forum(bbp_get_forum_id())){echo bbp_get_forum_id();?><p>VOTE</p><?php } ?>
<?php echo "ECHO" ?>
<div id="bbp-forum-<?php bbp_forum_id(); ?>" class="bbp-topics">
<?php 
if(bbp_get_forum_topic_count()>0)
    {       

        $bbp_loop_args = array(
        'orderby' => 'date',
        'order' => 'DESC',
        );
	if(isset($_REQUEST['order']) && $_REQUEST['order']){
            $bbp_loop_args['order']='ASC';
        }
	if(isset($_REQUEST['orderby']) && $_REQUEST['orderby']){
	  if($_REQUEST['orderby']=='most_replies')
            {
            $bbp_loop_args['meta_key']='_bbp_reply_count';
	    $bbp_loop_args['orderby']='meta_value_num';
            }
        }
	if(isset($_REQUEST['orderby']) && $_REQUEST['orderby']){
          if($_REQUEST['orderby']=='most_votes')		
		{	
		 $args['orderby'] = 'meta_value_num';
                 $args['meta_key'] = '_bbps_topic_user_votes_count';
       		 $args['meta_query'] = array(
                    relation' => 'OR',
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
	<div class="bbp-body modal firmasite-modal-static">
   		<div class="modal-dialog">
        <div class="modal-content">
	<?php if ( bbp_has_topics( $bbp_loop_args ) ) : ?>
		<?php while ( bbp_topics() ) : bbp_the_topic(); ?>

			<?php bbp_get_template_part( 'loop', 'single-topic' ); ?>

		<?php endwhile; ?>
   	<?php endif;?>
        </div>
        </div>
	</div>


</div><!-- #bbp-forum-<?php bbp_forum_id(); ?> -->

<?php do_action( 'bbp_template_after_topics_loop' ); ?>
