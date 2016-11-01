<?php
//v2.0 all
error_reporting(0);
session_start();
$hostname = $_SESSION['hostname'];
$SYSTEMTYP = $_SESSION['SYSTEMTYP'];
$rand = $_SESSION['rand'];
?>

<div id="exec" class="panel" title="&Auml;nderungen" selected="true">


</br>
<?php
exec('cat /etc/vectra130/configs/sysconfig/config', $SYSCONFIG);
exec("rm /etc/vectra130/configs/sysconfig/.tmp_output");
for($i=0; $i<count($SYSCONFIG); $i++) {
	$tmp=explode(":", $SYSCONFIG[$i]);
		$tmp2=$_POST[$tmp[1]];

		if(($tmp[2]!=$tmp2)&&($tmp2!="")) {
			exec("echo '".$tmp[0].":".$tmp[1].":".$tmp2.":".$tmp[3].":".$tmp[4].":".$tmp[5].":".$tmp[6].":".$tmp[7].":".$tmp[8].":".$tmp[9].":".$tmp[10].":' >> /etc/vectra130/configs/sysconfig/.tmp_output");
			$changes[]=$tmp[6];
			if($tmp[3]=="AP") {
			        $re = '';
			        for($pass = 0; $pass < strlen($tmp2); $pass++) {
				$re .= '*';
				}
				$changesval[]=$re;
			        $re = '';
			        for($pass = 0; $pass < strlen($tmp[2]); $pass++) {
				$re .= '*';
				}
				$changesvalold[]=$re;
			} else {
				$changesval[]=$tmp2;
				$changesvalold[]=$tmp[2];
			}
		}
		else {
			exec("echo '".$SYSCONFIG[$i]."' >> /etc/vectra130/configs/sysconfig/.tmp_output");
		}
}
exec('mv /etc/vectra130/configs/sysconfig/.tmp_output /etc/vectra130/configs/sysconfig/config');
if($SYSTEMTYP == "RasPi") {
	exec('/bin/bash /etc/vectra130/www/config/scripts/checklicense.sh', $checklicense);
}

if( (count($changes) != 0) || ($checklicense[0] == "CHANGE") ) {
	echo "<fieldset>&nbsp;&nbsp;Ge&auml;nderte Einstellungen:</br></br>";
	for($i=0; $i<count($changes); $i++) {
		echo "&nbsp;&nbsp;-->&nbsp;".$changes[$i]."&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'".$changesvalold[$i]."' > '".$changesval[$i]."'</br>";
	}
	if($checklicense[0] == "CHANGE") {
		echo "&nbsp;&nbsp;-->&nbsp;Licence Keys wurden aktualisiert</br>";
	}
	echo "</fieldset>";

	?>
	<fieldset><p></br>&nbsp;<?php echo $SYSTEMTYP; ?> Config wurde aktualisiert. Die &Auml;nderungen werden nach einem Neustart des VDR aktiv. &Auml;nderungen der Lizenzen oder Netzwerkeinstellungen erfordern einen Neustart des Systems. Mittels des Zur&uuml;ck-Buttons k&ouml;nnen erst noch weitere Einstellungen vorgenommen werden.</br></br></p></fieldset>
	</br><a href="restart.php?action=restart vdr" class='whiteButton' type='submit'>VDR neu starten</a><div class='spinner'></div></fieldset>
	</br><a onclick="return confirm('Wirklich neu starten?');" href="restart.php?action=reboot" class='redButton' type='submit'>System neu starten</a><div class='spinner'></div></br></fieldset>
<?php
}
else {
?>
<fieldset><p>&nbsp;Es wurden keine &Auml;nderungen vorgenommen</p></fieldset>
<?php
}
?>

</div>
