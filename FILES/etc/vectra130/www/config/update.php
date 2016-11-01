<?php
session_start();
$hostname = $_SESSION['hostname'];
$SYSTEMTYP = $_SESSION['SYSTEMTYP'];
$rand = $_SESSION['rand'];
$ACTION = $_GET['action'];
$UPDATEFILE = $_GET['UPDATEFILE'];
if (!isset($hostname)) {
	$hostname = $_GET['hostname'];
}
if (!isset($SYSTEMTYP)) {
        $SYSTEMTYP = $_GET['SYSTEMTYP'];
}

if( "$ACTION" == "exec" ) {
	exec ('/bin/bash /etc/vectra130/www/config/scripts/updateStreaming'.$SYSTEMTYP.'.sh update', $result);
        echo "<div class='panel' id='update_exec' title='System Update' selected='true'>";
	if( $result[0] == "OK" )
	{
	    echo "<fieldset><p><b>Systemupdate wird durchgef&uuml;hrt. System nicht ausschalten.</br>Das System startet nach dem Update automatisch neu.</b></p></fieldset>";
	    exec ('/bin/bash /etc/vectra130/update/prepare_update.sh | tee -a /etc/vectra130/update.log > /dev/tty1 &2>/dev/tty1 &');
	} else
	{
	    echo "<fieldset><p><b>Ein Problem ist aufgetreten. Das Update konnte nicht herunter geladen werden!!</b></p></fieldset>";
	}
}
else {

// update files laden
exec('/etc/vectra130/www/config/scripts/updateStreaming'.$SYSTEMTYP.'.sh check', $updateCheck);
?>
<div class='panel' id='update' title='Update - <?php echo $hostname; ?>' selected='true'>
<?php
echo "<form method='post' action='update.php?hostname=".$hostname."&SYSTEMTYP=".$SYSTEMTYP."&action=exec' enctype='multipart/form-data'>";
if( $updateCheck[0] != "" )
{
    echo '<p><b>Neues Online Update gefunden.</br></br>Aktuelle Version: v'.exec('cat /etc/vectra130/VERSION').'</br> Update Version  : v'.$updateCheck[0].'</br></br></p>';
    echo '<p>Das Update installiert im Hintergrund.</br>Je nach Gr&ouml;&szlig;e und Internetverbindung kann dies einige Minuten dauern.</br></br>Alle Frontends (VDR, Kodi) werden beendet. Nach dem Update wird das System automatisch neu gestartet.</br></br></br></b></p>';
//    echo "<a class='whiteButton' type='submit'>Herunterladen und Installieren</a><div class='spinner'></div>";
    echo "<a href='#' class='whiteButton' type='submit'>Herunterladen und Installieren</a><div class='spinner'></div>";
} else
{
    echo '<fieldset><p>Kein Online Update gefunden</br>Das System ist auf dem aktuellen Stand</p></fieldset>';
}
?>

</form>
<?php
}
?>

</div>
