#!/bin/bash

BAK_DIR="/tmp/channels_bak"
KEEP_BAK="14"

#VDRCONFDIR="/root/.vdr/"
VDRCONFDIR="/tmp"

DATE="$(date +%F)"
#[ -f "$BAK_DIR/channels.conf_$DATE" ] && exit
#[ "$(pidof vdr)" != "" ] && exit

cd $VDRCONFDIR
[ ! -d $BAK_DIR ] && mkdir $BAK_DIR
cp channels.conf $BAK_DIR/channels.conf_$DATE
awk -f /etc/vectra130/scripts/sort.awk channels.conf > channels.conf.sort
#mv -f channels.conf.sort channels.conf
find $BAK_DIR -mtime +$KEEP_BAK -exec rm -v {} \;



