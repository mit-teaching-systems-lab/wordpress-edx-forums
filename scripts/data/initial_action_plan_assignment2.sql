# For running remotely
# Get all posts in Initial Assignment 2: Concrete Steps, omitting
# users without posts.
SELECT guid, user_email, user_nicename, post_title, post_content
FROM wp_users
LEFT JOIN (
  SELECT wp_posts.guid, wp_posts.post_parent, parent_wp_posts.post_title as parent_post_title, wp_posts.post_title, wp_posts.post_content, wp_posts.post_author
  FROM wp_posts
  LEFT JOIN wp_posts parent_wp_posts ON parent_wp_posts.ID = wp_posts.post_parent
  WHERE wp_posts.post_parent IN (174) 
) as relevant_posts on wp_users.ID = relevant_posts.post_author
WHERE post_title IS NOT NULL
;