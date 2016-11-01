#/bin/bash
#Updatefile zum updaten des StreamingServer

UPDATEDIR="/etc/vectra130/update/git_update_files"
GITREPO="https://github.com/Vectra130/streamingserver_update.git"

clone_update()
{
	#update herunterladen
	[ -e $UPDATEDIR ] && rm -r $UPDATEDIR
	git clone --depth 1 $GITREPO $UPDATEDIR
}

pull_update()
{
	#pull
	cd $UPDATEDIR
	git pull --depth 1
}

stop_streamingserver()
{
#StreamingAerver beenden
if [ x$(cat /tmp/.frontendSet) != xsuspend ]; then
	echo suspend > /tmp/.frontendSet
	while [ $(pidof -xs vdr | wc -w) != 0 ]; do
		sleep 1
	done
	while [ $(pidof -xs kodi.bin | wc -w) != 0 ]; do
		sleep 1
	done
fi
systemctl stop streamingserver
systemctl stop lirc
sleep 2
count=0
while [ $(pidof -xs StreamingServer | wc -w) != 0 ]; do
	sleep 1
	killall StreamingServer
	count=$[ count + 1 ]
	if [ count > 30 ]; then
		killall -9 StreamingServer
	fi
done

}

install_update()
{
	# fuehre update aus
	[ ! -e $UPDATEDIR/update.sh ] && exit 1
	chmod +x $UPDATEDIR/update.sh
	$UPDATEDIR/update.sh
	if [ x$? == x0 ]; then
		echo OK
	fi
}

stop_streamingserver
if [ -d $UPDATEDIR/.git ]; then
	pull_update
else
	clone_update
fi
install_update

exit 0
