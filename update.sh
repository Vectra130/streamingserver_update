#/bin/bash
#1.13.1
#Updatefile zum updaten des StreamingServers

error_exit()
{
echo -e "\n\n\e[31mUPDATE FEHLGESCHLAGEN!!!!!\n\n\e[0mDie letzten 20 Log Einträge:\n##########" # | tee -a /dev/tty1
tail -20 /etc/vectra130/update.log
echo "##########"
date >> $DLOG
exit 2
}

create_update()
{
UPDATEDIR="/usr/local/src/cplusplus/VDR/StreamingServer/UPDATE"
UPDATEFILESDIR="$UPDATEDIR/FILES"
SYSTEMDDIR="/etc/systemd/system"
BINDIR="/usr/bin"
VERSION=$(cat /etc/vectra130/VERSION)
read -n 1 -p "Update Version $VERSION ok? [Y/n] " CHECK
if [ x$CHECK == xn ]; then
	echo
	read -p "Neue Update Version eingeben: " VERSION
fi
[ x$VERSION == x ] && exit 2
echo

# up = update
# cf = force kopieren
# rc = original(ordner) vorher komplett löschen, dann kopieren
# rm = nur löschen
# nf = touch
# sl = symlink
# nd = ordner anlegen

echo Erstelle Update $VERSION ...

echo $VERSION > VERSION
while read -r line; do
	[ $(echo $line | grep -E "^up |^cf |^rc " | wc -l) == 0 ] && continue
	DIR="$UPDATEFILESDIR/$(dirname ${line:3})"
	[ ! -e $DIR ] && mkdir -p $DIR
	echo "--> kopiere ${line:3} (option:${line:0:2})"
	cp -rau ${line:3} $DIR
done < $UPDATEDIR/file_tree

[ ! -e $UPDATEFILESDIR/debconf ] && mkdir $UPDATEFILESDIR/debconf
debconf-get-selections > $UPDATEFILESDIR/debconf/selections
#echo Archiv packen...
[ -e ${UPDATEDIR}/FILES.tar ] && rm ${UPDATEDIR}/FILES.tar
du -hs $UPDATEFILESDIR | awk '{ print $1 "B" }' > $UPDATEDIR/size_FILES
#tar cfpz ${UPDATEDIR}/FILES.tar $UPDATEFILESDIR && rm -r $UPDATEFILESDIR && echo ok
#du -hs $UPDATEDIR/FILES.tar | awk '{ print $1 "B" }' > $UPDATEDIR/size_FILES_TAR
du -hs $UPDATEDIR --exclude=.git | awk '{ print $1 "B" }' > $UPDATEDIR/size_DOWNLOAD
echo "FILES: $(cat /$UPDATEDIR/size_FILES)"
#echo "TAR:   $(cat /$UPDATEDIR/size_FILES_TAR)"
echo "UPDATE:$(cat /$UPDATEDIR/size_DOWNLOAD)"
}

upload_update()
{
VERSION=$(cat VERSION)
echo -e "\n-- aktualisiere git ..."
git add -A -v
read -p "Commit Info: " COMMIT
echo
[ "x$COMMIT" == x ] && COMMIT=$VERSION
git commit -m "$COMMIT"
git push && echo "--- Version $VERSION hoch geladen"
}

install_update()
{
UPDATEDIR="/etc/vectra130/update/git_update_files"
UPDATEFILESDIR="$UPDATEDIR/FILES"
SYSTEMDDIR="/etc/systemd/system"
BINDIR="/usr/bin"
TTY="> /dev/tty1"
LOG="tee -a /dev/tty1 | tee -a /etc/vectra130/update.log"
DLOG="/etc/vectra130/update.log"

cd $UPDATEDIR
#updateinfos zeigen
chvt 1
echo -e "\e[3J" #| $LOG
echo -e "\n\e[34m############################## UPDATEVERLAUF ##############################\e[0m\n" #| $LOG
echo -e "\n\e[33m########## Updatefiles heruntergeladen\e[0m" #| $LOG

#echo -e "\n\e[33m########## Updatefiles entpacken ...\e[0m" #| $LOG
#tar xfpz ${UPDATEDIR}/FILES.tar && rm -r $UPDATEDIR/FILES.tar || error_exit

echo -e "\n\e[33m########## Erstelle read-write Filesystem ...\e[0m" #| $LOG
mount -o rw,remount /
if [ $? -ne 0 ]; then error_exit; fi
mount -o rw,remount /boot
if [ $? -ne 0 ]; then error_exit; fi
mount -o remount,size=256M /tmp
if [ $? -ne 0 ]; then error_exit; fi

#system
echo -e "\n\e[33m########## Aktualisiere Quellen ...\e[0m" #| $LOG
cp -ra FILES/etc/apt/* /etc/apt/
if [ $? -ne 0 ]; then error_exit; fi
#apt-key adv --keyserver keyserver.ubuntu.com --recv-key 5243CDED
aptitude -y update
dpkg --configure -a
debconf-set-selections FILES/debconf/selections
if [ $? -ne 0 ]; then error_exit; fi

#swapfile
if [ ! -e /etc/vectra130/swapfile ]; then
	echo -e "\n\e[33m########## Erstelle Swap File ...\e[0m" #| $LOG
	dd if=/dev/zero of=/etc/vectra130/swapfile bs=1M count=2048
	chown root:root /etc/vectra130/swapfile
	chown 0600 /etc/vectra130/swapfile
	mkswap /etc/vectra130/swapfile
	swapon /etc/vectra130/swapfile
fi

#proftpd
echo -e "\n\e[33m########## Aktualisiere proftpd ...\e[0m" #| $LOG
aptitude -y --no-gui install proftpd-basic
if [ $? -ne 0 ]; then error_exit; fi

# dateien kopieren
echo -e "\n\e[33m########## Kopiere Files ($(du -hs | awk '{ print $1 }')B) ...\e[0m" #| $LOG
# up = update
# cf = force kopieren
# rc = original(ordner) vorher komplett löschen, dann kopieren
# rm = nur löschen
# nf = touch
# sl = symlink
# nd = ordner anlegen
while read -r line; do
	[ $(echo $line | grep -E "^up |^cf |^rc |^rm |^nf |^sl |^nd " | wc -l) == 0 ] && continue
	if [ x${line:0:2} == xup ]; then
		DIR="$(dirname ${line:3})"
		[ ! -e $DIR ] && mkdir -p $DIR
#		echo "--> kopiere ${line:3} (option:${line:0:2})" >> $DLOG
		cp -rauv $UPDATEFILESDIR/${line:3} $DIR >> $DLOG
		if [ $? -ne 0 ]; then error_exit; fi
	fi
	if [ x${line:0:2} == xcf ]; then
		DIR="$(dirname ${line:3})"
		[ ! -e $DIR ] && mkdir -p $DIR
#		echo "--> kopiere ${line:3} (option:${line:0:2})" >> $DLOG
		cp -rafv $UPDATEFILESDIR/${line:3} $DIR >> $DLOG
		if [ $? -ne 0 ]; then error_exit; fi
	fi
	if [ x${line:0:2} == xrc ]; then
		[ -e ${line:3} ] && rm -r ${line:3}
		DIR="$(dirname ${line:3})"
		[ ! -e $DIR ] && mkdir -p $DIR
#		echo "--> kopiere ${line:3} (option:${line:0:2})" >> $DLOG
		cp -rav $UPDATEFILESDIR/${line:3} $DIR >> $DLOG
		if [ $? -ne 0 ]; then error_exit; fi
	fi
	if [ x${line:0:2} == xrm ]; then
		[ -e ${line:3} ] && rm -r ${line:3}
		echo "--> lösche ${line:3} (option:${line:0:2})" >> $DLOG
	fi
	if [ x${line:0:2} == xnf ]; then
		DIR="$(dirname ${line:3})"
		[ ! -e $DIR ] && mkdir -p $DIR
		echo "--> touch ${line:3} (option:${line:0:2})" >> $DLOG
		touch ${line:3}
	fi
	if [ x${line:0:2} == xsl ]; then
		line1=$(echo $line | awk '{ print $2 }')
		line2=$(echo $line | awk '{ print $3 }')
		DIR="$(dirname $line2)"
		[ ! -e $DIR ] && mkdir -p $DIR
#		echo "--> symlink $line1 -> $line2 (option:${line:0:2})" >> $DLOG
		ln -sfv $line1 $line2 >> $DLOG
	fi
	if [ x${line:0:2} == xnd ]; then
		DIR="${line:3}"
		[ ! -e $DIR ] && mkdir -p $DIR
		echo "--> mkdir ${line:3} (option:${line:0:2})" >> $DLOG
	fi
done < $UPDATEDIR/file_tree

#systemctl
echo -e "\n\e[33m########## Aktualisiere systemctl ...\e[0m" #| $LOG
systemctl daemon-reload
systemctl disable syslog
systemctl disable syslog-ng
systemctl enable streamingserver
systemctl enable streamingserver-boot
systemctl disable vdr

#richtige user anlegen
echo -e "\n\e[33m########## Aktualisiere User ...\e[0m" #| $LOG
deluser ftp
delgroup ftp
if [ $(cat /etc/passwd | grep ^"vdr:x:1001:1001::/etc/vectra130/configs/userconfig:/bin/bash" | wc -l) != 1 ];then
	deluser vdr
	delgroup vdr
	addgroup --gid 1001 vdr >> $DLOG
	if [ $? -ne 0 ]; then error_exit; fi
	adduser --no-create-home --uid 1001 --gid 1001 --home /etc/vectra130/configs/userconfig --shell /bin/bash --disabled-password --disabled-login --system vdr >> $DLOG
	if [ $? -ne 0 ]; then error_exit; fi
fi
echo "vdr:vdr" | chpasswd >> $DLOG
if [ $? -ne 0 ]; then error_exit; fi
usermod -a -G video,audio,sudo,cdrom,plugdev,users,dialout,dip,input vdr >> $DLOG
if [ $? -ne 0 ]; then error_exit; fi

#datei rechte vergeben
echo -e "\n\e[33m########## Aktualisiere User Rechte ...\e[0m" #| $LOG
chown -R vdr:vdr /etc/vectra130/configs/vdrconfig
if [ $? -ne 0 ]; then error_exit; fi
chown -R vdr:vdr /etc/vectra130/configs/userconfig
if [ $? -ne 0 ]; then error_exit; fi
chown -R vdr:vdr /etc/vectra130/data/vdr
if [ $? -ne 0 ]; then error_exit; fi
chown -R vdr:vdr /usr/*/vdr
if [ $? -ne 0 ]; then error_exit; fi
chown -R vdr:vdr /vdrvideo0?
if [ $? -ne 0 ]; then error_exit; fi
chmod 777 /vdrvideo0?
if [ $? -ne 0 ]; then error_exit; fi

#aufräumen
echo -e "\n\e[33m########## Räume auf uns schließe Update ab ...\e[0m" #| $LOG
apt-get -y autoclean
apt-get -y autoremove
apt-get clean
cp -av /etc/vectra130/update/VERSION /etc/vectra130/VERSION >> $DLOG
if [ $? -ne 0 ]; then error_exit; fi
#rm -r /etc/vectra130/update/*
echo -e "\n\n\n\e[32m############################## Update beendet, starte neu ... ##############################\e[0m\n" > $TTfY

date >> $DLOG
sleep 10
reboot
exit 0
}

if [ x$1 == xcreate ]; then
	create_update
	read -n1 -p "Upload [Y/n]?" READ
	[ x$READ != xn ] && upload_update
	echo
	exit 0
fi
if [ x$1 == xupload ]; then
	upload_update
	exit 0
fi
install_update
