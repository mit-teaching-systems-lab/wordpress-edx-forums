# This destructively updates the remote target, deploying
# the nginx configuration and restarting nginx.
if [ "$#" -lt 1 ]; then
    echo "Example usage:"
    echo "  $ nginx_deploy.sh ubuntu@forums"
    echo
    exit 1
fi
USER_HOST_TARGET=$1
RSYNC_OPTIONS=$2
echo target: $1
echo RSYNC_OPTIONS=$RSYNC_OPTIONS

echo;echo;echo
echo ------- Deploying -------
echo nginx...
rsync -avz --progress --omit-dir-times \
  $RSYNC_OPTIONS \
  --exclude=".DS_Store" \
  --exclude="*.sh" \
  ./nginx/ -e "ssh" $USER_HOST_TARGET:/etc/nginx

echo Removing sites-enabled...
ssh $USER_HOST_TARGET 'sudo rm /etc/nginx/sites-enabled/*'

echo;echo;echo
echo ------- Reloading -------
echo Restarting nginx...
ssh $USER_HOST_TARGET 'sudo service nginx restart'

echo Done.