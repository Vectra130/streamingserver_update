
<?php
//v1.1 all
error_reporting(0);
$_SESSION['rand'] = $rand;
$wolCmd = $_GET['command'];
?>

<div id="wol" class="panel" title="WOL" selected="true">

<?php
if("$wolCmd" !== "") {

exec("/etc/vectra130/scripts/.wol_aufwecken.sh ".$wolCmd, $wolRslt);
echo "<fieldset><p>";
	echo $wolRslt[1]."</br>";
echo "</p></fieldset></br>";
echo "<fieldset><p>";
	echo $wolRslt[3]."</br>";
echo "</p></fieldset>";
echo "<fieldset><p>";
for($i=4; $i<count($wolRslt); $i++) {
	echo $wolRslt[$i]."</br>";
}
echo "</p></fieldset>";
//echo "<fieldset><p>".$wolRslt[count($wolRslt)-1]."</br></p></fieldset>";
} else {

exec("cat /etc/vectra130/configs/userconfig/wol-adressen | wc -l", $fileExist);
?>
</br>
<?php
if("$fileExist[0]" == "0") {
	echo "<fieldset><p>Keine Adressen in der Datei</br>'wol-adressen'</br>gefunden</p></fieldset>";
} else {
?>
<fieldset><p>Folgende WOL-Adressen gefunden:</p></fieldset>
<?php
	exec("/etc/vectra130/scripts/.get_wol_mac_adresses.sh");
	exec("cat /etc/vectra130/configs/userconfig/wol-adressen", $wolClient);
	if(count($wolClient) !== 0) {
		echo "<fieldset><p>";
		for($i=0; $i<count($wolClient); $i++) {
			$COMMAND = explode(" ", $wolClient[$i]);
			echo $COMMAND[0]." ".$COMMAND[1]."</br>";
		}
		echo "</p></fieldset></br>";
		echo "<fieldset><p>WOL Befehl abschicken:</p></fieldset><ul>";
		for($i=0; $i<count($wolClient); $i++) {
			$COMMAND = explode(" ", $wolClient[$i]);
//			echo "<div class='row'>";
//			echo "<label title='".$COMMAND[0]."' id='label_test1'>".$COMMAND[0]."</label>";
//			echo "<div title='".$COMMAND[0]."' class='statusLedOff' name='sw-test1' id='sw-test1' onclick=\"return confirm('\'".$COMMAND[0]."\' wirklich aufwecken?');\" href='wol.php?command=".$COMMAND[0]."><span class='statusLedOff'></span></div>";
//			echo "</div>";
			echo "<li><a href='wol.php?command=".$COMMAND[0]."'>".$COMMAND[0]."</a></li>";
		}
		echo "</ul>";
	}
}
}
?>
</div>
