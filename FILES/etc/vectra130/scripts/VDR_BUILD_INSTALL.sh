#!/bin/bash
# v2.0 all
#Script um VDR zu bauen und installieren

if [ ! -e config.h ]; then
	echo -e "\e[31m\nACHTUNG das Script muss im VDR Source Verzeichnis ausgefuehrt werden!!!"
	echo "VDR-Sourcen nicht gefunden!!!"
	echo -e "Breche ab\e[0m"
	exit 0
fi

# \e[31m = rot
# \e[32m = gruen
# \e[33m = gelb
# \e[34m = blau
# \e[0m  = schwarz

DIR=$(pwd)
LOCALEDIR=/usr/share/locale/de_DE/LC_MESSAGES
NEWVDR=/tmp/newvdr
NEWVDRTAR=$NEWVDR.tar
VDRVERSION=$(cat config.h | grep "define VDRVERSION" | awk '{ print $3 }' | sed -e 's/\"//g')
APIVERSION=$(cat config.h | grep "define APIVERSION" | awk '{ print $3 }' | sed -e 's/\"//g')
PATCHDIR=/usr/local/src/VDR-Patches/$VDRVERSION
[ -e $PATCHDIR ] || mkdir -p $PATCHDIR
[ ! -e /usr/lib/pkgconfig/vdr.pc ] && rm /usr/lib/pkgconfig/vdr.pc
ln -s $DIR/vdr.pc /usr/lib/pkgconfig/vdr.pc
removepatches(){
                        [ $(ls $PATCHDIR/ | grep ^$i- | wc -l) -gt 0 ] && patch=1
                        if [ "x$patch" == "x1" ]; then
                                for p in $(ls $PATCHDIR/ | grep ^$i-); do
					echo -e "\e[31m--->Entferne Patch: $p\e[0m"
                                        patch -RN -p1 -i $PATCHDIR/$p || PATCHFAIL=1
                                done
                        fi
}
addpatches(){
                        if [ "x$patch" == "x1" ]; then
                                for p in $(ls $PATCHDIR/ | grep ^$i-); do
					echo -e "\e[34m--->Fuege Patch hinzu: $p\e[0m"
                                        patch -N -p1 -i $PATCHDIR/$p || PATCHFAIL=1
                                done
                        fi
}

echo
echo -e "\e[34m####################################"
echo -e "# VDR Version $VDRVERSION gefunden"
echo -e "# (API Version $APIVERSION)"
echo -e "#####################################\e[0m"

#bauen
echo -e "\e[33m"
read -n 1 -p "Sourcen aktualisieren? (y/N)   " INPUT
echo -n -e "\e[m"
if [ "x$INPUT" == "xy" ]; then
	echo -e "\nAktualisiere Sourcen..."
	nb=1
	i=vdr
	UPDATE=""
	NOUPDATE=""
	UPDATEFAIL=0
	EXIT=0
	if [ -e .git ]; then
		echo -e "\n-> GIT: VDR"
		echo "-->update..."
		u=$(git pull)
		echo -e "\e[34m$u\e[0m"
		if [ "x$(echo $u)" != "xAlready up-to-date." ]; then
			removepatches
			echo "-->versuche erneutes update..."
			u=$(git pull)
	                echo -e "\e[34m$u\e[0m"
                        if [ "$(echo $u | grep -E '(files changed|file changed|Already up-to-date)' | wc -l)" == 0 ]; then
				NOUPDATE+="$i "
				ERROR+="$i "
				EXIT=$[ EXIT+1 ]
			else
				UPDATE+="$i "
			fi
			addpatches
		fi
	else
		NOUPDATE+="vdr "
	fi
	echo
        cd $DIR/PLUGINS/src/
        for i in $(ls); do
		up=0
		patch=0
		UPDATEFAIL=0
                cd $DIR/PLUGINS/src/$i
                if [ -e .git ]; then
			echo -e "\n-> GIT: "$i
	                echo "-->update..."
	                u=$(git pull)
	                echo -e "\e[34m$u\e[0m"
	                if [ "x$(echo $u)" != "xAlready up-to-date." ]; then
	                        removepatches
	                        echo "-->versuche erneutes update..."
	                        u=$(git pull)
	                        echo -e "\e[34m$u\e[0m"
	                        if [ "$(echo $u | grep -E '(files changed|file changed|Already up-to-date)' | wc -l)" == 0 ]; then
					NOUPDATE+="$i "
					ERROR+="$i "
					EXIT=$[ EXIT+1 ]
				else
					UPDATE+="$i "
#					if [ $(echo $u | grep libskindesignerapi | wc -l) != 0 ]; then
					if [ "$i" == "skindesigner" ]; then
						LIBSKINDESIGNERAPI=1
					fi
				fi
	                        addpatches
	                fi
		fi
                if [ -e .subversion ]; then
                        echo -e "\n-> SVN: "$i
                        echo "-->update..."
                        scn up || UPDATEFAIL=1
                        if [ "x$UPDATEFAIL" == "x1" ]; then
                                removepatches
                                echo "-->versuche erneutes update..."
                                svn up || EXIT=$[ EXIT+1 ]
                                addpatches
                        fi
                fi
                if [ -e .hg ]; then
                        echo -e "\n-> HG: "$i
                        echo "-->update..."
                        hg update || UPDATEFAIL=1
                        if [ "x$UPDATEFAIL" == "x1" ]; then
                                removepatches
                                echo "-->versuche erneutes update..."
                                hgupdate || EXIT=$[ EXIT+1 ]
                                addpatches
                        fi
                fi
		[[ ! -e .git && ! -e .subversion && ! -e .hg ]] && NOUPDATE+="$i " && up=1
        done
	echo -e "\n\nfolgende Pakete werden neu gebaut: $UPDATE\n"
	echo "Nicht Upgedatet: $NOUPDATE"
	if [ x$LIBSKINDESIGNERAPI == "x1" ]; then
		echo -e "\n\e[34mÄnderungen an libskindesignerapi entdeckt. Betroffene Plugins werden neu gebaut\e[0m\n"
	fi
                if [ "x$EXIT" != "x0" ]; then
			echo -n -e "\e[31m"
                        read -n 1 -p "$EXIT Update(s) fehlgeschlagen ( $ERROR)! Trotzdem fortfahren? (y/N)   " INPUT
                        if [ "x$INPUT" == "xn" ]; then
                                echo -e "\nABBRUCH"
                                exit
                        fi
			echo -n -e "\e[0m"
                fi

        cd $DIR
	[ "x$(ls PLUGINS/lib/)" != "x" ] && rm -r PLUGINS/lib/*
fi
echo -e "\e[33m"
read -n 1 -p "VDR erneut patchen? (y/N)   " INPUT
echo -n -e "\e[0m"
if [ "x$INPUT" == "xy" ]; then
	echo
	i=vdr
	patch=1
	removepatches
	addpatches
fi
echo -e "\e[33m"
read -n 1 -p "Plugins erneut patchen? (y/N)   " INPUT
echo -n -e "\e[0m"
if [ "x$INPUT" == "xy" ]; then
	patch=1
	cd $DIR/PLUGINS/src
        for i in $(ls); do
		echo -e "\n->pruefe $i"
                cd $DIR/PLUGINS/src/$i
		removepatches
                addpatches
	done
fi
echo -e "\e[33m"
read -n 1 -p "VDR neu bauen (make clean)? (y/N)   " INPUT
echo -n -e "\e[0m"
if [ "x$INPUT" == "xy" ]; then
	echo -e "\ncleane VDR..."
	make clean
fi
echo -e "\e[33m"
read -n 1 -p "Plugins neu bauen (make clean)? (y/N)   " INPUT
echo -n -e "\e[0m"
if [ "x$INPUT" == "xy" ]; then
        echo -e "\ncleane Plugins..."
	cd $DIR/PLUGINS/src/
	for i in $(ls); do
		cd $i
		make clean
		cd ..
	done
	cd $DIR
fi
echo -e "\e[33m"
read -n 1 -p "Anzahl Kerne nutzen? ($(nproc --all))   " CORES
echo -n -e "\e[0m"
if [[ $CORES -lt 1 || $CORES -gt $(nproc --all) ]]; then
	CORES=$(nproc --all)
fi

#bauen
echo -e "\nwende Aenderungen an (nutze $CORES Kerne)..."
ln -sf $DIR/ /usr/include/vdr
ln -sf $DIR/ /usr/local/include/vdr
export PKG_CONFIG_PATH=$DIR/PLUGINS/src/skindesigner/libskindesignerapi/:$PKG_CONFIG_PATH
if [ x$LIBSKINDESIGNERAPI == "x1" ]; then
	cd $DIR/PLUGINS/src/skindesigner/libskindesignerapi && make clean && make install && SdApi=1
	if [ x$SdApi != x1 ]; then
		echo -e "\e[31mFehler beim erstellen der libskindesidnerapi !!! Breche ab\e[0m"
		exit 0
	fi
fi
for i in skindesigner tvguideng weatherforecast plex; do
	if [[ x$SdApi == x1 || $(echo $UPDATE | grep $i | wc -l) != 0 ]]; then
		echo -e "\e[34mCleane $i ...\e[0m"
		[ -e $DIR/PLUGINS/src/$i ] && cd $DIR/PLUGINS/src/$i && make clean
		echo -e "\e[32m OK\e[0m\n"
	fi
done

cd $DIR
#time make -j$CORES DEBUG=1 all plugins
time make -j$CORES all plugins

[ "x$?" != "x0" ] && echo && echo -e "\e[31mBuild Fehler. ABBRUCH!!!\e[0m" && exit 0
#sleep 5


echo -e "\n\n\e[34mNeue VDR Version $VDRVERSION wurde erstellt\e[0m\n\n"
echo -n -e "\e[0m\n\n"
echo -e "\e[33m"
read -n 1 -p "Neue VDR-Version installieren? (y/N):   " INPUT
echo -n -e "\e[0m"
if [ "x$INPUT" == "xy" ]; then
	echo
	[ ! -z $(pidof -xs vdr) ] && echo suspend > /tmp/.frontendSet
	while [ ! -z $(pidof -xs vdr) ]; do
		sleep 1
	done
	echo -e "installiere...\n\n"
	make install
	if [ "$?" == 0 ]; then
		echo -e "\e[34m\n\nVDR aktualisiert\e[0m"
	else
		echo -e "\e[31m\n\nAktualisierung des VDR fehlgeschlagen"
	fi
fi
echo -e "\n\n\e[0m"
exit 0
