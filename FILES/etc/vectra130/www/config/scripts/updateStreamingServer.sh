#!/bin/bash

#StreamingServer Update Script


ACTION=$1
UPDATEDIR="/etc/vectra130/update"
GITREPO="https://github.com/Vectra130/streamingserver_update.git"


if [ x$ACTION == xcheck ]; then
	# pruefe git auf update
	[ -e $UPDATEDIR ] && rm -r $UPDATEDIR
	wget https://raw.githubusercontent.com/Vectra130/streamingserver_update/master/VERSION -P $UPDATEDIR
	VERSNOW=$(cat /etc/vectra130/VERSION)
	VERSNOW1=$(printf "%02d" $(echo $VERSNOW | awk -F. '{ print $1 }'))
	VERSNOW2=$(printf "%02d" $(echo $VERSNOW | awk -F. '{ print $2 }'))
	VERSNOW3=$(printf "%02d" $(echo $VERSNOW | awk -F. '{ print $3 }'))
	VERSNOWNUM=$VERSNOW1$VERSNOW2$VERSNOW3

	VERSNEW=$(cat $UPDATEDIR/VERSION)
	VERSNEW1=$(printf "%02d" $(echo $VERSNEW | awk -F. '{ print $1 }'))
	VERSNEW2=$(printf "%02d" $(echo $VERSNEW | awk -F. '{ print $2 }'))
	VERSNEW3=$(printf "%02d" $(echo $VERSNEW | awk -F. '{ print $3 }'))
	VERSNEWNUM=$VERSNEW1$VERSNEW2$VERSNEW3
	[ "x$VERSNOWNUM" == "x" ] && exit 1
	[ "x$VERSNEWNUM" == "x" ] && exit 1
	[ $VERSNOWNUM -ge $VERSNEWNUM ] && exit 1
	echo $VERSNEW
fi

if [ x$ACTION == xupdate ]; then
	date > /etc/vectra130/update.log
	wget https://raw.githubusercontent.com/Vectra130/streamingserver_update/master/prepare_update.sh -P $UPDATEDIR
	if [ -e $UPDATEDIR/prepare_update.sh ]; then
		chmod +x $UPDATEDIR/prepare_update.sh
		echo OK
	fi
fi

exit 0
