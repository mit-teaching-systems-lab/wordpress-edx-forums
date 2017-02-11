# This rsyncs up the folder of scripts to run remotely
if [ "$#" -ne 1 ]; then
    echo "Example usage:"
    echo "  $ remote_scripts_deploy.sh ubuntu@forums"
    echo
    exit 1
fi
USER_HOST_TARGET=$1


echo remote scripts...
rsync -avz --progress --omit-dir-times \
  scripts/remote -e "ssh" $USER_HOST_TARGET:~

echo config.sh...
scp config.sh $USER_HOST_TARGET:~/remote
