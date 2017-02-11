<?php
  // This is a command-line script for patching up
  // Groups that were created without forums.
  // Code is adapted from ../extend/buddypress/groups.php
  //
  // We probably won't need this again.  See below for usage.
  function create_group_forum($group_id) {
    $group = groups_get_group('group_id=' . $group_id);
    echo("\nKR: group = " . print_r($group, true));

    // lookup if there's a forum already
    $forum_ids = bbp_get_group_forum_ids($group_id);
    $forum_id = (!empty($forum_ids))
      ? (int) is_array($forum_ids) ? $forum_ids[0] : $forum_ids
      : 0;

    echo("\nKR: forum_id: = " . print_r($forum_id, true));
    if ($forum_id != 0) {
      echo("\nKR: aborting...");
      exit(1);
    }

    // Set the default forum status
    switch (bp_get_new_group_status()) {
      case 'hidden':
        $status = bbp_get_hidden_status_id();
        break;
      case 'private':
        $status = bbp_get_private_status_id();
        break;
      case 'public':
      default:
        $status = bbp_get_public_status_id();
        break;
    }
    echo("\nKR: status = " . $status);


    // get ready to create forum
    $args = array(
      'post_parent'  => bbp_get_group_forums_root_id(),
      'post_title'   => $group->name,
      'post_content' => $group->description,
      'post_status'  => $status
    );
    echo("\nKR: args = " . print_r($args, true));

    actually_create_group_forum($group_id, $args);

  }

  // actually create it!
  function actually_create_group_forum($group_id, $args) {
    echo("\nKR: bbp_insert_forum...");
    $forum_id = bbp_insert_forum($args);
    echo("\nKR: forum_id = " . $forum_id);

    echo("\nKR: bbp_add_forum_id_to_group...");
    bbp_add_forum_id_to_group($group_id, $forum_id);

    echo("\nKR: bbp_add_group_id_to_forum...");
    bbp_add_group_id_to_forum($forum_id, $group_id);

    // Update forum active
    echo("\nKR: groups_update_groupmeta...");
    groups_update_groupmeta($group_id, '_bbp_forum_enabled_' . $forum_id, true );

    // Toggle forum on
    echo("\nKR: toggle_group_forum...");
    $g = new BBP_Forums_Group_Extension();
    $g->toggle_group_forum($group_id, true);

    echo("\n\n...created...\n\n");
  }


  // 1. sql to find groups without forums...
  // mysql> select id from wp_bp_groups where id not in (select distinct group_id from wp_bp_groups_groupmeta where meta_key='forum_id') ORDER BY id asc;

  // 2. then call the function here...
  // create_group_forum($group_id);
  echo("\n\nDone.\n");
?>