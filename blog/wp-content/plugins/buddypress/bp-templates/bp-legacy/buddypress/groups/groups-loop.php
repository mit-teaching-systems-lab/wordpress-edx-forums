<?php

/**
 * BuddyPress - Groups Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - bp_legacy_theme_object_filter()
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

?>

<?php do_action( 'bp_before_groups_loop' ); ?>

<?php if ( bp_has_groups( bp_ajax_querystring( 'groups' ) ) ) : ?>

	<div id="pag-top" class="pagination">

		<div class="pag-count" id="group-dir-count-top">

			<?php bp_groups_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="group-dir-pag-top">

			<?php bp_groups_pagination_links(); ?>

		</div>

	</div>

	<?php do_action( 'bp_before_directory_groups_list' ); ?>

	<ul id="groups-list" class="item-list" role="main">

	<?php while ( bp_groups() ) : bp_the_group(); ?>

		<li <?php bp_group_class(); ?>>
			<div class="item-avatar">
				<a href="<?php bp_group_permalink(); ?>"><?php bp_group_avatar( 'type=thumb&width=50&height=50' ); ?></a>
			</div>

			<div class="item">
				<div class="item-title"><a href="<?php bp_group_permalink(); ?>"><?php bp_group_name(); ?></a></div>
				<div class="item-meta"><span class="activity"><?php printf( __( 'active %s', 'buddypress' ), bp_get_group_last_active() ); ?></span></div>

				<div class="item-desc"><?php bp_group_description_excerpt(); ?></div>

				<?php do_action( 'bp_directory_groups_item' ); ?>

			</div>

			<div class="action">

				<?php do_action( 'bp_directory_groups_actions' ); ?>

				<div class="meta">

					<?php bp_group_type(); ?> / <?php bp_group_member_count(); ?>
				</div>
				<div class="meta" id="mytopicpostcount">
					<?php   $my_group_id = bp_get_group_id();
                                                $my_forum_ids = bbp_get_group_forum_ids( $my_group_id );
                                                //echo "og grp: ".$my_group_id." og frm: ".print_r(my_forum_ids,true);
                                                // Taken from wp-content/plugins/bbpress/includes/extend/buddypress/groups.php
                                                //$forum_ids = bbp_get_group_forum_ids( $group_id ); 
                                                $forum_id = null;
                                                // Get the first forum ID
                                                if ( !empty( $my_forum_ids ) ) {
                                                        $forum_id = (int) is_array( $my_forum_ids ) ? $my_forum_ids[0] : $my_forum_ids;
                                                        bbp_forum_topic_count($forum_id);    
                                                        echo " ";
                                                        _e( 'Topics', 'bbpress' );
                                                        echo " / ";
                                                        bbp_show_lead_topic() ? bbp_forum_reply_count($forum_id) : bbp_forum_post_count($forum_id);
                                                        echo " ";
                                                        bbp_show_lead_topic() ? _e( 'Replies', 'bbpress' ) : _e( 'Posts', 'bbpress' );
                                                }
                                                else{
                                                        echo "Group has no forum";
                                                }
                                        ?>

				</div>

			</div>

			<div class="clear"></div>
		</li>

	<?php endwhile; ?>

	</ul>

	<?php do_action( 'bp_after_directory_groups_list' ); ?>

	<div id="pag-bottom" class="pagination">

		<div class="pag-count" id="group-dir-count-bottom">

			<?php bp_groups_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="group-dir-pag-bottom">

			<?php bp_groups_pagination_links(); ?>

		</div>

	</div>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'There were no groups found.', 'buddypress' ); ?></p>
	</div>

<?php endif; ?>

<?php do_action( 'bp_after_groups_loop' ); ?>
