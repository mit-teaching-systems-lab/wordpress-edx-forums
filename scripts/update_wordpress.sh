# update wordpress code
# note that this merges WP code into the wp-content and root folder
#
# based on https://codex.wordpress.org/Updating_WordPress
mkdir -p build
cd build

rm -rf latest.tar.gz
rm -rf wordpress
wget http://wordpress.org/latest.tar.gz
tar xzvf latest.tar.gz
cd ..

# destructive replace
rm -rf blog/wp-admin
cp -r build/wordpress/wp-admin blog
rm -rf blog/wp-includes
cp -r build/wordpress/wp-includes blog

# copy into existing folders, possibly overwriting
cp -r build/wordpress/wp-content blog
cp build/wordpress/* blog

echo Done.