<?php
//error_reporting(0);
session_start();
$rand = "01";
$_SESSION['rand'] = $rand;
exec("cat /etc/hostname", $hostname);
$hostname = $hostname[0];
exec("cat /etc/vectra130/configs/sysconfig/config | grep ':SYSTEMTYP:' | awk -F':' {' print $3 '}", $TMP);
if($TMP[0] == "CLIENT") {
	exec("cat /etc/vectra130/configs/sysconfig/config | grep ':CLIENTTYP:' | awk -F':' {' print $3 '}", $TMP2);
	$SYSTEMTYP = $TMP2[0];
	$INFO = $SYSTEMTYP."VDR/Kodi StreamingClient by Vectra130";
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
	<title><?php echo $hostname;?> - <?php echo $SYSTEMTYP; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width initial-scale=1.0 maximum-scale=1.0 user-scalable=0"/>
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<meta name="format-detection" content="telephone=no">
	<meta http-equiv="cache-control" content="no-store,no-cache, must-revalidate,post-check=0, pre-check=0,max-age=0">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Expires" content="0">
	<link rel="apple-touch-startup-image" href="images/<?php echo $SYSTEMTYP; ?>-startup.jpg?<?php echo $rand; ?>"> 
	<link rel="apple-touch-icon" href="images/<?php echo $SYSTEMTYP; ?>-iui-icon.png?<?php echo $rand; ?>" />
 	<link rel="icon" type="image/png" href="images/<?php echo $SYSTEMTYP; ?>-iui-icon.png?<?php echo $rand; ?>">
	<link rel="stylesheet" href="iui/iui.css?<?php echo $rand; ?>" type="text/css" />
	<link rel="stylesheet" href="iui/iui-ext.css?<?php echo $rand; ?>" type="text/css" />
    	<link rel="stylesheet" href="iui/t/default/default-theme.css?<?php echo $rand; ?>"  type="text/css"/>
	<link rel="stylesheet" href="iui/iui-eigenes.css?<?php echo $rand; ?>" type="text/css" />
	<link rel="stylesheet" href="iui/iui-eigenes.css?<?php echo $rand; ?>" type="text/safari" />
	<script type="application/x-javascript" src="iui/iui.js?<?php echo $rand; ?>"></script>
	<script type="text/javascript" src="iui/iui-eigenes.js?<?php echo $rand; ?>"></script>

</head>
<body>
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
?>

<div id="home" class="panel" title="<?php echo $SYSTEMTYP; ?> (<?php echo $hostname;?>)" selected="true">

<?php
   echo "<h2><img onclick=\"alert('".$INFO." (v".$VERSION.")')\" align='middle' src='images/".$SYSTEMTYP."-icon.png'>&nbsp;&nbsp;&nbsp;</img></h2>";
?>

<?php
exec("ifconfig | grep 'inet addr' | grep -v '255.0.0.0' | awk '{ print $2 }' | awk -F: '{ print $2 }'", $IP);
if("$SYSTEMTYP" == "Server") {
    exec("pidof -xs epgd | wc -w", $checkEpgd);
    exec("vdr --showargs | grep 'plugin=dvbapi' | wc -w", $checkOscam);
    exec("vdr --showargs | grep 'plugin=live' | wc -w", $checkLive);
    exec("cat /etc/vectra130/configs/sysconfig/config | grep ':USEPLEX:1:' | wc -l", $checkPlex);
    exec("pidof -xs mysqld | wc -w", $checkMysql);
    exec("pidof -xs monitorix-httpd | wc -w", $checkMonitorix);
	echo "<ul><li><a target='_blank' href='istreamdev'>iStreamdev (Smartphone/Tablet)</a></li>";
	echo "<li><a target='_blank' href='http://".$IP[0].":3000'>Streamdev (Laptop)</a></li>";
    if("$checkLive[0]" == "1") {
	echo "<li><a target='_blank' href='http://".$IP[0].":8008'>Live Webinterface</a></li>";
    }
    if("$checkEpgd[0]" == "1" && "$checkMysql[0]" == "1") {
	echo "<li><a target='_blank' href='http://".$IP[0].":9999'>Epgd WebIf</a></li>";
    }
    if("$checkOscam[0]" == "1") {
	echo "<li><a target='_blank' href='http://".$IP[0].":8888'>Oscam CardServer</a></li>";
    }
    if("$checkPlex[0]" == "1") {
	echo "<li><a target='_blank' href='http://".$IP[0].":32400/web'>Plex Media Server</a></li>";
    }
	echo "<li><a target='_blank' href='phpsysinfo'>PhpSysInfo</a></li>";
    if("$checkMysql[0]" == "1") {
	echo "<li><a target='_blank' href='../phpmyadmin'>PhpMyAdmin</a></li>";
    }
    if("$checkMonitorix[0]" == "1") {
	echo "<li><a target='_blank' href='http://".$IP[0].":8080/monitorix-cgi/monitorix.cgi?mode=localhost&graph=all&when=1day&color=black'>Monitorix</a></li>";
    }
//	echo "<li><a target='_blank' href='https://".$IP[0].":10000'>Webmin Administration</a></li>";
	echo "</ul>";
}
?>
<ul><li><a target='_webapp' href='index.php'>System Konfiguration</a></li></ul>
<?php
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
?>
</div></body>
