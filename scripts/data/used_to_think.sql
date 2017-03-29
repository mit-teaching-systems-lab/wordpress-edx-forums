# For all text and attachments for a particular topic
# get all text including replies, count of replies and count of attachments
SELECT SHA2(user_login, 256), wp_posts.guid, wp_posts.post_title, CONCAT(wp_posts.post_content, '\n\n', IFNULL(replies_text, '')) as all_text, IFNULL(replies_count, 0) as replies_count, IFNULL(attachments_count, 0) as attachments_count
FROM wp_posts
LEFT JOIN wp_users ON wp_users.ID = wp_posts.post_author
LEFT JOIN (
  SELECT wp_posts.post_parent, IFNULL(COUNT(*), 0) as replies_count, IFNULL(CONCAT('>> Replies below >>\n', GROUP_CONCAT(wp_posts.post_content SEPARATOR '\n\n')), '') as replies_text
  FROM wp_posts
  GROUP BY wp_posts.post_parent
  ORDER BY post_date ASC
) as replies_table on wp_posts.ID = replies_table.post_parent
LEFT JOIN (
  SELECT wp_posts.post_parent, IFNULL(COUNT(*), 0) as attachments_count
  FROM wp_posts
  WHERE wp_posts.post_type = 'attachment'
  GROUP BY wp_posts.post_parent
  ORDER BY post_date ASC
) as attachments_table on wp_posts.ID = attachments_table.post_parent
WHERE wp_posts.post_parent IN (243);