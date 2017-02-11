# Warning: this will destroy the database it points to!
# For setting up wordpress for the forums.  Run this on the
# remote host.
source ~/remote/config.sh
if [ -z "$WP_ADMIN_PASSWORD" ]; then echo "Need to set WP_ADMIN_PASSWORD"; exit 1; fi 
if [ -z "$WP_ADMIN_EMAIL" ]; then echo "Need to set ADMIN_EMAIL"; exit 1; fi 
if [ -z "$WP_DOMAIN" ]; then echo "Need to set WP_DOMAIN"; exit 1; fi 
if [ -z "$WP_PATH" ]; then echo "Need to set WP_PATH"; exit 1; fi 
if [ -z "$WP_DB_NAME" ]; then echo "Need to set WP_DB_NAME"; exit 1; fi 
if [ -z "$WP_DB_USER" ]; then echo "Need to set WP_DB_USER"; exit 1; fi 
if [ -z "$WP_DB_PASS" ]; then echo "Need to set WP_DB_PASS"; exit 1; fi 
if [ -z "$WP_DB_HOST" ]; then echo "Need to set WP_DB_HOST"; exit 1; fi 
if [ -z "$WP_TITLE" ]; then echo "Need to set WP_TITLE"; exit 1; fi 


# wp CLI doesn't support HHVM, no plans to
# see https://github.com/wp-cli/wp-cli/issues/2107
rm $WP_PATH/wp-config.php
~/wp core config --path=$WP_PATH \
  --dbname=$WP_DB_NAME \
  --dbuser=$WP_DB_USER \
  --dbpass=$WP_DB_PASS \
  --dbhost=$WP_DB_HOST \
  --dbcharset="utf8" \
  --extra-php='require_once(ABSPATH . "forums-config.php");';
~/wp db drop --path=$WP_PATH --yes
~/wp db create --path=$WP_PATH
~/wp core multisite-install --path=$WP_PATH \
  --url="https://$WP_DOMAIN" \
  --title="$WP_TITLE" \
  --admin_user=tsl-admin --admin_password=$WP_ADMIN_PASSWORD --admin_email=$WP_ADMIN_EMAIL;
