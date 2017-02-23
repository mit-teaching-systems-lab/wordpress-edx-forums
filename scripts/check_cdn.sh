# This script checks for mismatches between what assets are on a WordPress instance
# and what assets are in S3, at particular paths specific below.  You can use
# --dryrun to debug, or leave out options to perform the sync.
#
# This requires credentials that can access this S3 bucket be present on on the
# remote machine in ~/.aws 
if [ "$#" -lt 1 ]; then
    echo "Example usage:"
    echo "  $ check_cdn.sh forumsa launching-innovation-assets --dryrun"
    echo
    exit 1
fi
USER_HOST_TARGET=$1
S3_BUCKET_NAME=$2
OPTIONS=$3

# Repair any uploads that are on the WordPress instance, but not pushed to S3
# Uploads
ssh $USER_HOST_TARGET "aws s3 sync /var/www/html/blog/wp-content/uploads/2017/ s3://$S3_BUCKET_NAME/wp-content/uploads/2017/ $OPTIONS"

# Avatars
ssh $USER_HOST_TARGET "aws s3 sync /var/www/html/blog/wp-content/uploads/avatars/ s3://$S3_BUCKET_NAME/wp-content/uploads/avatars/ $OPTIONS"

# I didn't look at why, but the W3TC theme uploader UI doesn't seem to upload the woff font files (that extension
# is listed in the admin UI).  Working around by uploading them manually.
ssh $USER_HOST_TARGET "aws s3 sync /var/www/html/blog/wp-content/themes/ s3://$S3_BUCKET_NAME/wp-content/themes/ --exclude '*' --include 'wp-knowledge-base*/*.woff' $OPTIONS"
