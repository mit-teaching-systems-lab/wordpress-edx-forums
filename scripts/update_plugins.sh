# locally, destructively update all the plugins to the latest versions
echo 'Updating plugins locally...'
echo

rm -rf build/plugins-zip
rm -rf build/plugins
mkdir -p build/plugins-zip
mkdir -p build/plugins

# destructive replace
for i in $(ls blog/wp-content/plugins | grep -v .php); do
  printf '%-40s' "$i"

  # these two packages don't exist on the plugin registry anymore
  if [ "$i" == "bbPress-Support-Forums-master" ]
  then
    echo " SKIPPED"
    continue
  fi
  if [ "$i" == "gd-bbpress-widgets" ]
  then
    echo " SKIPPED"
    continue
  fi
  
  echo -n ' downloading...'
  wget --quiet -P build/plugins-zip http://downloads.wordpress.org/plugin/$i.zip
  echo -n ' unzipping...'
  unzip -q build/plugins-zip/$i.zip -d build/plugins
  echo -n ' replacing...'
  rm -rf blog/wp-content/plugins/$i
  cp -r build/plugins/$i blog/wp-content/plugins/$i
  echo ' done'
done

echo
echo Done.  Check any changes into source control.