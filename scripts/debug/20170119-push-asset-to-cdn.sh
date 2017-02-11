# This is useful for updating the wp-knowledge-base theme, since its files are used but
# the W3TC push to CDN UI won't push them, since they're not literally part of the active theme.
# For a live site, this is problematic since you can't just swap themes to use the upload UI.

# gzip the asset, scp it to the remote host
gzip blog/wp-content/themes/wp-knowledge-base/js/ipt-kb.js -c --best > ./ipt-kb.js.gzip
scp ipt-kb.js.gzip forumsa-1:/var/www/html/blog/wp-content/themes/wp-knowledge-base/js/

# push to to the cdn from the remote host
# set --content-encoding
# set --content-type
ssh forumsa-1 'aws s3 cp /var/www/html/blog/wp-content/themes/wp-knowledge-base/js/ipt-kb.js.gzip s3://launching-innovation-forums/wp-content/themes/wp-knowledge-base/js/ipt-kb.js.gzip --dryrun'