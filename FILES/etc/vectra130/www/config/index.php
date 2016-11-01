<?php
// v2.1 all
//error_reporting(0);
session_start();
$rand = "02";
$_SESSION['rand'] = $rand;
exec("cat /etc/hostname", $hostname);
$hostname = $hostname[0];
exec("cat /etc/vectra130/configs/sysconfig/config | grep ':SYSTEMTYP:' | awk -F':' {' print $3 '}", $TMP);
if($TMP[0] == "CLIENT") {
	exec("cat /etc/vectra130/configs/sysconfig/config | grep ':CLIENTTYP:' | awk -F':' {' print $3 '}", $TMP2);
	$SYSTEMTYP = $TMP2[0];
	$INFO = $SYSTEMTYP." VDR/KODI StreamingClient by Vectra130";
}
if($TMP[0] == "SERVER") {
        $SYSTEMTYP = "Server";
        $INFO = "MultiClient VDR StreamingServer by Vectra130";
}
exec("cat /etc/vectra130/VERSION", $VERSION);
exec("cat /etc/vectra130/configs/sysconfig/config | grep ':VDRVERS:' | awk -F':' {' print $3 '}", $VDRVERSION);
$VERSION = $VERSION[0];

$_SESSION['SYSTEMTYP'] = $SYSTEMTYP;
$_SESSION['hostname'] = $hostname;
$_SESSION['INFO'] = $INFO;
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $hostname;?> - <?php echo $SYSTEMTYP; ?> Config</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width initial-scale=1.0 maximum-scale=1.0 user-scalable=0"/>
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<meta name="format-detection" content="telephone=no">
	<meta http-equiv="cache-control" content="no-store,no-cache, must-revalidate,post-check=0, pre-check=0,max-age=0">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Expires" content="0">
	<link rel="apple-touch-startup-image" href="images/VDR-startup.jpg?<?php echo $rand; ?>"> 
	<link rel="apple-touch-icon" href="images/VDR-iui-icon.png?<?php echo $rand; ?>" />
 	<link rel="icon" type="image/png" href="images/VDR-iui-icon.png?<?php echo $rand; ?>">
	<link rel="stylesheet" href="iui/iui.css?<?php echo $rand; ?>" type="text/css" />
	<link rel="stylesheet" href="iui/iui-ext.css?<?php echo $rand; ?>" type="text/css" />
    	<link rel="stylesheet" href="iui/t/default/default-theme.css?<?php echo $rand; ?>"  type="text/css"/>
	<link rel="stylesheet" href="iui/iui-eigenes.css?<?php echo $rand; ?>" type="text/css" />
	<link rel="stylesheet" href="iui/iui-eigenes.css?<?php echo $rand; ?>" type="text/safari" />
	<script type="application/x-javascript" src="iui/iui.js?<?php echo $rand; ?>"></script>
<!--	<script type="text/javascript" src="iui/iui-eigenes.js?<?php echo $rand; ?>"></script> -->

</head>
<body>
<?php
unset($SYSCONFIG);
?>
    <div class="toolbar">
        <h1 id="pageTitle"> </h1>
        <a id="backButton" class="button" href="#"></a>
	<a class="button" href="#home">Home</a>
    </div>

<?php
if(eregi("MSIE", $_SERVER['HTTP_USER_AGENT'])) {
?>
<script type="text/javascript">
	alert('Die Nutzung dieses Interfaces ist mit dem InternetExplorer nicht m\u00f6glich.\nDer Aufwand f\u00fcr die Anpassung an den InternetExplorer w\u00e4re mir zu gro\u00df.\n\n!!!!!!!!!! Der InternetExplorer ist halt ein beschissener Browser !!!!!!!!!!\n\n\nBitte benutze Firefox, Safari, Opera\noder irgendeinen anderen anst\u00e4ndigen Browser');
</script>
<?php
exit();
}

exec('cat /etc/vectra130/configs/sysconfig/config', $SYSCONFIG);
?>

<div id="home" class="panel" title="<?php echo $SYSTEMTYP; ?> Config (<?php echo $hostname;?>)" selected="true">

<?php
   echo "<h2><img onclick=\"alert('".$INFO." (v".$VERSION.")')\" align='middle' src='images/VDR-icon.png'>&nbsp;&nbsp;&nbsp;</img></h2>";
?>

<ul>
<?php
//.config einlesen und verarbeiten
$SUB=1;
$SUBMENU = array();
$MENU = array();
for($i=0; $i<=count($SYSCONFIG); $i++) {
	if(substr($SYSCONFIG[$i],0,2)=="M:") {
		if(substr($SYSCONFIG[($i-1)],0,1)=="") {
			echo  "</ul><ul>";
		}
		echo "<li><a href='#SUB".$SUB."'>".substr($SYSCONFIG[$i],2)."</a></li>";
		$MENU[$SUB]=substr($SYSCONFIG[$i],2);
		$SUB++;
	}
	if((substr($SYSCONFIG[$i],0,2)=="C:") || (substr($SYSCONFIG[$i],0,2)=="I:") || (substr($SYSCONFIG[$i],0,2)=="L:") || (substr($SYSCONFIG[$i],0,2)=="H:")) {
		$SUBMENU[]=($SUB-1).":".$SYSCONFIG[$i];
	}
}
echo "</ul>";
if("$SYSTEMTYP" == "Server") {
	echo "<ul><li><a href='clients.php'>Streamingclients Online</a></li></ul>";
}
/*
if(! eregi("iPhone", $_SERVER['HTTP_USER_AGENT'])) {
	echo "<ul><li><a href='edit_channels.php'>Kanaleditor</a></li></ul>";
}
*/
echo "<ul><li><a href='wol.php'>WakeOnLan</a></li></ul>";
if("$SYSTEMTYP" == "Server") {
?>
</br><a onclick="return confirm('VDR wirklich neu starten? Alle Clients werden getrennt und alle Aufnahmen abgebrochen!!!');" href="restart.php?action=restart vdr" class='whiteButton' type='submit'>VDR neu starten</a></br>
<a onclick="return confirm('System wirklich neu starten? Alle Clients werden getrennt und alle Aufnahmen abgebrochen!!!');" href="restart.php?action=reboot" class='redButton' type='submit'><?php echo $SYSTEMTYP; ?> Neustarten</a></br>
<?php
} else {
?>
</br><a onclick="return confirm('VDR wirklich neu starten?');" href="restart.php?action=restart vdr" class='whiteButton' type='submit'>VDR neu starten</a></br>
<a onclick="return confirm('System wirklich neu starten?');" href="restart.php?action=reboot" class='redButton' type='submit'><?php echo $SYSTEMTYP; ?> Neustarten</a></br>
<?php
}
echo "</div>";

//commands einlesen und menue erstellen
echo "<div id='commands' class='panel' title='".$SYSTEMTYP." Config'>";
echo "<p>N&uuml;tzliche Befehle.</br>Die Befehle werden ausgef&uuml;hrt wenn der Link gedr&uuml;ckt wird.</p>";
exec('cat /etc/vectra130/www/config/commands', $COMMANDS);
echo "<ul>";
for($i=0; $i<count($COMMANDS); $i++) {
	if(substr($COMMANDS[$i],0 ,1) == "") {
		echo "</ul><ul>";
	}
	else {
		$COMMAND = explode(":", $COMMANDS[$i]);
		echo "<li><a title='".$COMMAND[2]."' href='exec-command.php?command=".$COMMAND[0]."&name=".$COMMAND[1]."'><font size='-1'>".$COMMAND[1]."</font size></a></li>";
	}
}
echo "</ul></div>";

//Untermenues aus .config erstellen
for($ii=1; $ii<$SUB; $ii++) {
$count=0;
echo "<form id='SUB".$ii."' class='panel' title='".$SYSTEMTYP." Config' action='exec-config.php' method='POST'><h2>".$MENU[$ii]."</h2><fieldset></fieldset>";
	$subcount=0;
	for($i=0; $i<=count($SUBMENU); $i++) {
		if(substr($SUBMENU[$i],0,1)==$ii) {
			if(substr($SUBMENU[$i],2,2)=="I:") {
					echo "</fieldset>";
				echo "<p>".substr($SUBMENU[$i],4)."</p>";
                        	echo "<fieldset>";
			}
			if(substr($SUBMENU[$i],2 ,2)=="H:"){ //Menüpunkte
					echo "</fieldset>";
				echo "<p><font color=blue><b><i><u>".substr($SUBMENU[$i],4)."</u></i></b></font></p>";
                        	echo "<fieldset>";
			}
			if(substr($SUBMENU[$i],2 ,2)=="C:"){
			$tmp=explode(":", $SUBMENU[$i]);
			if($tmp[4]=="C") {
				echo "</fieldset><ul><li title='".$tmp[8]."'><a href='".$tmp[3]."?hostname=".$hostname."&SYSTEMTYP=".$SYSTEMTYP."'>".$tmp[7]."</a></li></ul><fieldset>";
			}
			else {
			if(substr($tmp[2],0 ,9)=="VDRPLUGIN") {
				exec('ls /usr/lib/vdr/plugins/libvdr-'.substr($tmp[2],9).'.so.'.$VDRVERSION[0], $PLUG);
			}
			if(substr($tmp[2],0 ,9)!="VDRPLUGIN" || ($PLUG[0]!="" && substr($tmp[2],0 ,9)=="VDRPLUGIN")) {

	        	echo "<div class='row form' title='".$tmp[8]."'><label>".$tmp[7]."</label>";
			if($tmp[4]=="A") {
				echo "<input type='text' name='".$tmp[2]."' style='text-align:right' placeholder='".$tmp[3]."'>";
			}
			if($tmp[4]=="AP") {
		        	$re = '';
       				for($place=0; $place<strlen($tmp[3]); $place++) {
       				$re .= '*';
       				}
				$tmp[3] = $re;
				echo "<input type='password' name='".$tmp[2]."' style='text-align:right' placeholder='".$tmp[3]."'>";
			}
			if($tmp[4]=="B") {
	                        echo "<select name='".$tmp[2]."'>";
				$tmp2=explode(",", $tmp[6]);
				for($iii=0; $iii<count($tmp2); $iii++) {
					echo "<option value='".$iii."'";
//					echo "<option style='text-align:right' value='".$iii."'";
					if($iii==$tmp[3]) {
						echo " selected";
					}
					echo ">".$tmp2[$iii]."</option>";
				}
				echo "</select>";
			}

                        if($tmp[4]=="L") {
                                echo "<select name='".$tmp[2]."'>";
//                                echo "<select name='".$tmp[2]."' style='text-align:right'>";
                                $tmp2=explode(",", $tmp[6]);
                                for($iii=0; $iii<count($tmp2); $iii++) {
                                        echo "<option value='".$tmp2[$iii]."'";
//                                        echo "<option style='text-align:right' value='".$tmp2[$iii]."'";
                                        if($tmp2[$iii]==$tmp[3]) {
                                                echo " selected";
                                        }
                                        echo ">".$tmp2[$iii]."&nbsp;</option>";
                                }
				echo "</select>";
                        }
			unset($PLUG);
			}
			}
			echo "</div>";
			}
		$subcount++;
		}
	}
echo "</fieldset></br>";
echo "<a class='whiteButton' type='submit'>&Auml;nderungen &uuml;bernehmen</a><div class='spinner'></div>";

echo "</br></form>";
}
?>
</ul>
</body>
</html>
