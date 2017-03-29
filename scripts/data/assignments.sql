# For running remotely
# get all users, and any posts they made for each assignment
SELECT SHA2(user_login, 256), relevant_posts.*
FROM wp_users
LEFT JOIN (
  SELECT wp_posts.guid, wp_posts.post_parent, parent_wp_posts.post_title as parent_post_title, wp_posts.post_title, wp_posts.post_content, wp_posts.post_author
  FROM wp_posts
  LEFT JOIN wp_posts parent_wp_posts ON parent_wp_posts.ID = wp_posts.post_parent
  WHERE wp_posts.post_parent IN (121, 123, 151, 172, 174, 229, 245) 
) as relevant_posts on wp_users.ID = relevant_posts.post_author
;