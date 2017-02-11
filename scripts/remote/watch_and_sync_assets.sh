#!/bin/bash
#
# This is experimental.
#
# Watch for changes on the filesystem, and then sync assets that are written to
# local disk to an S3 bucket, so that assets are available from S3 rather than
# a particular WordPress host.
# 
# This does not update the WordPress database.  It also isn't smart or robust
# about retrying or handling errors.
inotifywait -m -r -e moved_to,create /var/www/html/blog/wp-content/uploads/2017 |
  while read path action file; do
    echo "> The file '$file' appeared in directory '$path' via '$action'" >> /var/log/sync_assets.log
    aws s3 sync /var/www/html/blog/wp-content/uploads/2017/ s3://launching-innovation-forums/wp-content/uploads/2017/ >> /var/log/sync_assets.log &
  done
