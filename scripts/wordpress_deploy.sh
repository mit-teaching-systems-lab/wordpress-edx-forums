# This destructively updates the remote target, and 
# deploys WordPress code over it.
# Some folders use the --delete option, while others only
# copy in the relevant files.
# 
# Importantly, this will not touch the wp-config.php file or
# wp-content/uploads.
#
# The plugin and theme folders are destructively updated, so
# if the WordPress configuration relies on a particular theme or
# plugin it should be disabled first before deploying.
if [ "$#" -lt 1 ]; then
    echo "Example usage:"
    echo "  $ wordpress_deploy.sh ubuntu@forums"
    echo
    exit 1
fi
USER_HOST_TARGET=$1
RSYNC_OPTIONS=$2
echo target: $1
echo RSYNC_OPTIONS=$RSYNC_OPTIONS

echo;echo;echo
echo ------- Deploying -------
echo SKIPPING ROOT FILES!
# echo root files...
# rsync -vz --omit-dir-times --no-perms --progress \
#   $RSYNC_OPTIONS \
#   ./blog/* -e "ssh" $USER_HOST_TARGET:/var/www/html/blog

echo wp-content...
ssh $USER_HOST_TARGET 'mkdir -p /var/www/html/blog/wp-content'

echo wp-content/mu-plugins...
rsync -avz --omit-dir-times --no-perms --progress \
  $RSYNC_OPTIONS \
  --delete \
  ./blog/wp-content/mu-plugins/ -e "ssh" $USER_HOST_TARGET:/var/www/html/blog/wp-content/mu-plugins

echo wp-content/plugins...
rsync -avz --omit-dir-times --no-perms --progress \
  $RSYNC_OPTIONS \
  --delete \
  ./blog/wp-content/plugins/ -e "ssh" $USER_HOST_TARGET:/var/www/html/blog/wp-content/plugins

echo wp-content/themes...
rsync -avz --omit-dir-times --no-perms --progress \
  $RSYNC_OPTIONS \
  --delete \
  ./blog/wp-content/themes/ -e "ssh" $USER_HOST_TARGET:/var/www/html/blog/wp-content/themes

echo wp-admin...
rsync -avz --omit-dir-times --no-perms --progress \
  $RSYNC_OPTIONS \
  --delete \
  ./blog/wp-admin/ -e "ssh" $USER_HOST_TARGET:/var/www/html/blog/wp-admin

echo wp-includes...
rsync -avz --omit-dir-times --no-perms --progress \
  $RSYNC_OPTIONS \
  --delete \
  ./blog/wp-includes/ -e "ssh" $USER_HOST_TARGET:/var/www/html/blog/wp-includes


echo;echo;echo
echo ------- Reloading -------
echo Checking WordPress install...
ssh $USER_HOST_TARGET '~/wp core is-installed --path=/var/www/html/blog'
if [ $? -eq 0 ]; then
  echo Activating plugins...
  ssh $USER_HOST_TARGET '~/wp plugin activate --all --network --path=/var/www/html/blog'

  echo Activating themes...
  ssh $USER_HOST_TARGET '~/wp theme enable wp-knowledge-base --network --quiet --path=/var/www/html/blog'
  ssh $USER_HOST_TARGET '~/wp theme enable wp-knowledge-base-child --network --activate --quiet --path=/var/www/html/blog'

  echo Checking for plugin updates...
  STALE_PLUGINS=$(ssh $USER_HOST_TARGET '~/wp plugin status --path=/var/www/html/blog | grep "UN " | wc --lines')
  echo " > There are $STALE_PLUGINS plugins with updates available."
else
  echo 'WordPress not installed; plugins and themes not updated.'
fi;
echo " > If you're using a CDN, you will need to push assets from plugins or themes that have been updated."
echo " > See scripts/check_cdn.sh for more."

echo Done.