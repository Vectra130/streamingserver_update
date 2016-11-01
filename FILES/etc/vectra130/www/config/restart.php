<?php
session_start();
$hostname = $_SESSION['hostname'];
$SYSTEMTYP = $_SESSION['SYSTEMTYP'];
$rand = $_SESSION['rand'];
$action = $_GET['action'];

if("$action" == "reboot") {
        exec('reboot &');
?>
	<body onload="alert('System startet jetzt neu!\n\nDiese Konfigurations-Seite kann jederzeit unter der eingestellten IP-Adresse aufgerufen werden. Diese Seite bitte schlie&szlig;en!!!');window.close();"></body>
	<?php
}
if("$action" == "restart vdr") {
	exec('killall vdr &');
?>
	<body onload="alert('VDR startet jetzt neu!\n\nDiese Konfigurations-Seite kann jederzeit unter der eingestellten IP-Adresse aufgerufen werden. Diese Seite bitte schlie&szlig;en!!!');window.close();"></body>
	<?php
}
?>
