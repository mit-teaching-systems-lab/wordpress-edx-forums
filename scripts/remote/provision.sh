# for building from a fresh ubuntu 16 image

# php
sudo apt-get update -qq && sudo apt-get install -y php php-curl php-gd php-mbstring php-mcrypt php-xml php-xmlrpc php-cgi

# mysql client and php extensions
sudo apt-get update -qq && sudo apt-get install -y mysql-client php-mysql

# util
sudo apt-get update -qq && sudo apt-get install -y zip


### nginx ###
sudo apt-get update -qq && sudo apt-get install -y nginx

# grant ownership of nginx folder
sudo chown -R ubuntu /etc/nginx
sudo adduser ubuntu www-data
sudo chgrp -R www-data /etc/nginx
sudo chmod -R g+rw /etc/nginx
sudo chmod g+s /etc/nginx


### WordPress ###
# wordpress cli, see http://wp-cli.org/
cd ~
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
chmod +x wp-cli.phar
mv wp-cli.phar wp

# create mount point for wordpress
sudo rm -rf /var/www/html/blog
sudo mkdir -p /var/www/html/blog
sudo chown ubuntu /var/www/html/blog

# grant ownership for wordpress
# via https://www.sitepoint.com/setting-up-php-behind-nginx-with-fastcgi/
sudo adduser ubuntu www-data
sudo chgrp -R www-data /var/www/html
sudo chmod -R g+rw /var/www/html
sudo chmod g+s /var/www/html
sudo mkdir -p /var/www/html/blog/wp-content/uploads
sudo chown -R www-data /var/www/html/blog/wp-content/uploads

# aws cli tools, for syncing assets to S3
# requires python and pip
# see http://docs.aws.amazon.com/cli/latest/userguide/installing.html
mkdir ~/aws
cd ~/aws
sudo apt-get install python2.7 -y
curl -O https://bootstrap.pypa.io/get-pip.py
sudo python2.7 get-pip.py
sudo pip install awscli
cd ~
mkdir ~/.aws # and also setup credentials