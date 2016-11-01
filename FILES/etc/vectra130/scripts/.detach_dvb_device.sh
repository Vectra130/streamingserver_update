#!/bin/bash
ADAPTER=$1
ADAPTERNUMBER=$(echo $ADAPTER | sed -e 's!/dev/dvb/adapter\(.*\)/frontend.*!\1!')
dbus-send --system --type=method_call --dest=de.tvdr.vdr --print-reply /Plugins/dynamite de.tvdr.vdr.plugin.SVDRPCommand string:'fdtd' string:$1
#echo $ADAPTERNUMBER >> /tmp/.detachDvbAdapter
exit 0
