#!/bin/bash
# v2.0 raspi

CONFIGTXT="/boot/config.txt"
CONFIG="/etc/vectra130/configs/userconfig/config"

#nur einmal ausfuehren!
[ $(pidof -x $(basename $0) | wc -w) -gt 2 ] && exit 0

#Prueft die Lizenzen auf veraenderung

mount -o rw,remount /boot

RET=""

for LICENSE in decode_MPG2 decode_WVC1; do
	NEW_LICENSE=""
	NOW_LICENSE=$(cat $CONFIGTXT | grep ^$LICENSE | awk -F'=' '{print $2}')

	#hinzufuegen
	ADD_LICENSE=$(cat $CONFIG | grep ^"C:add_$LICENSE" | awk -F':' '{ print $3 }')
	if [ x$(echo ${ADD_LICENSE:0:2}) == "x0x" ]; then
		if [ $(echo $NOW_LICENSE | grep $ADD_LICENSE | wc -l) == 0 ]; then
			RET=CHANGE
			NOW_LICENSE+=",$ADD_LICENSE"
		fi
	fi

	#entfernen
	DEL_LICENSE=$(cat $CONFIG | grep ^"C:del_$LICENSE" | awk -F':' '{ print $3 }')
	if [ x$(echo ${DEL_LICENSE:0:2}) == "x0x" ]; then
		for CHECK in $(echo $NOW_LICENSE | tr ',' ' '); do
			if [ x$CHECK != x$DEL_LICENSE ]; then
				if [ x$NEW_LICENSE == x ]; then
					NEW_LICENSE=$CHECK
				else
					NEW_LICENSE+=",$CHECK"
				fi
			else
				RET=CHANGE
			fi
		done
	else
		NEW_LICENSE=$NOW_LICENSE
	fi

	sed -i -e 's/'$LICENSE'=.*/'$LICENSE'='$NEW_LICENSE'/' $CONFIGTXT
done

mount -o rw,remount /boot

sed -i -e 's/\(C:.*_decode_MPG2:\).*\(:A:.*\)/\1\2/' $CONFIG
sed -i -e 's/\(C:.*_decode_WVC1:\).*\(:A:.*\)/\1\2/' $CONFIG

echo $RET
exit 0
