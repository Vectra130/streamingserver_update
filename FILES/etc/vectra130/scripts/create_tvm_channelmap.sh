#!/bin/bash

#channelmap aus channels.conf erstellen
CHANNELSCONF=/root/.vdr/channels.conf
TMPCHANNELMAP=/tmp/epgd_tvm_channelmap.conf
awk -F : -f - $CHANNELSCONF <<EOF >$TMPCHANNELMAP
{ name=gensub("[;,].*$","","g",\$1);
  freq=\$2; parms=\$3; src=\$4
  sid=\$10; nid=\$11; tid=\$12; rid=\$13
  if (nid=="0" && tid=="0")
  {
    tid = freq
    if (index(parms,"H")>0)      tid += 100000
    else if (index(parms,"V")>0) tid += 200000
    else if (index(parms,"L")>0) tid += 300000
    else if (index(parms,"R")>0) tid += 400000
  }
  if (rid!="0") sid=sid "-" rid
  id=src "-" nid "-" tid "-" sid
  print "???=" id "\t\t// " name 
}
EOF

#tvm Senderliste holen
cd /tmp
rm ./datainfo.txt*
wget http://wwwa.tvmovie.de/static/tvghost/html/onlinedata/cftv520/datainfo.txt
echo -e $(cat -e /tmp/datainfo.txt | sed -e 's/\^M/\\n/g') > /tmp/tmp
cat /tmp/tmp | tail -n +4 > /tmp/tmp2 && rm /tmp/tmp
rm /tmp/datainfo.txt
while read i; do
	if [ "x$a" == "x" ]; then
		a="$i"
	else
		echo "$a:$i" >> /tmp/datainfo.txt
		a=""
	fi
done < "/tmp/tmp2"
rm /tmp/tmp2

#tvm Nummern in channelmap eintragen
rm /tmp/channelmap-test.conf
rm /tmp/NOTFOUND
while read line; do
	found=0
	if [ ! -z "$(echo $line | grep '???=S19.2E')" ]; then
#		echo "-> $line"
		while read test; do
		TMP=$(echo "$test" | awk -F: '{ print $1 }' | sed -f /etc/vectra130/scripts/create_tvm_channelmap.sed)
		TMP2=$(echo "$line" | grep -i "$TMP")
		if [ ! -z "$TMP2" ]; then
			NEWLINE=$(echo $line | sed -e "s/\?/tvm:"$(echo $test | awk -F: '{ print $2 }')":1:0/" -e  's/?//g' -e 's!\ //!\ \ \ \t//!')
			echo "$NEWLINE" >> /tmp/channelmap-test.conf
			NEWLINE2=$(echo $line | sed -e "s/\?/vdr:000:0:0/" -e  's/?//g' -e 's!\ //!\ \ \ \t//!')
			echo "$NEWLINE2" >> /tmp/channelmap-test.conf
			sed -i -e 's/'$test'/BEREITS:GENUTZT/' /tmp/datainfo.txt
			found=1
			echo "-----> Create Line:    $NEWLINE"
			echo "   -->                 $NEWLINE2"
		fi
		done < "/tmp/datainfo.txt"
		if [ "x$found" != "x1" ]; then
			NEWLINE=$(echo $line | sed -e 's/\?/vdr:000:0:0/' -e 's/?//g' -e 's!\ //!  \t\t//!')
			echo "$NEWLINE" >> /tmp/channelmap-test.conf
			echo "s//"$(echo "$line" | grep "???" | awk -F/ '{ print $3 }' | sed 's/^ //')"/" >> /tmp/NOTFOUND
			echo "-----> Create Line:    $NEWLINE"
		fi
	fi
done < $TMPCHANNELMAP
echo -e "//Update ueber DVB" > /tmp/channelmap.conf
cat /tmp/channelmap-test.conf | grep "vdr:000:0:0" >> /tmp/channelmap.conf
echo -e "\n//Update ueber TVMovie" >> /tmp/channelmap.conf
cat /tmp/channelmap-test.conf | grep "tvm:" >> /tmp/channelmap.conf
rm /tmp/channelmap-test.conf
sed -i -e 's/=/ = /' /tmp/channelmap.conf
