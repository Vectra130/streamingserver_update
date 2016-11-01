<?php
// v1.2
error_reporting(0);
$_SESSION['rand'] = $rand;
$editCmd = $_POST['command'];
$chanConfNew = explode('|||', preg_replace('/(\n|\r)/','|||',$_POST['chanConfNew']));
$chanConf = "/etc/vectra130/www/config/tmp/channels.conf";
exec("[ ! -d /etc/vectra130/www/config/tmp ] && mkdir -p /etc/vectra130/www/config/tmp");

if("$editCmd" == "") { // channels.conf einlesen

exec("/etc/vectra130/scripts/.edit_channels_webif.sh read");
exec("cat ".$chanConf, $chanConfNow);
for($i=0; $i<count($chanConfNow); $i++) {
        if(substr($chanConfNow[$i],0 ,1) == ":") {
		$countGroup[] = $chanConfNow[$i];
	} else {
		$countChan[] = $chanConfNow[$i];
	}
}
echo "<div id='chanedit' class='panel' title='Kan&auml;le bearbeiten' selected='true'>";
echo "<p>Aktuelle Kanalliste (".count($countChan)." Kan&auml;le in ".count($countGroup)." Gruppen):</p>";
$chanCount = 1;
unset($chanGroup);
for($i=0; $i<count($chanConfNow); $i++) {
	if(substr($chanConfNow[$i],0 ,1) == ":") {
		if(substr($chanConfNow[$i],1 ,1) == "@") {
			$tmpChan = explode(" ", $chanConfNow[$i]);
			$chanCount = substr($tmpChan[0],2);
			echo "<fieldset><li class='group'>".utf8_encode($tmpChan[1].' '.$tmpChan[2].' '.$tmpChan[3].' '.$tmpChan[4].' '.$tmpChan[5])."</li>";
		} else {
			echo "<fieldset><li class='group'>".utf8_encode(explode(":", $chanConfNow[$i])[1])."</li>";
		}
	} else {
		echo "<font size=1px><b>&nbsp;&nbsp;".$chanCount."</b> - ".utf8_encode(explode('|', explode(':', explode(',', explode(';', $chanConfNow[$i])[0])[0])[0])[0])."</font></br>";
		$chanCount++;
		if(substr($chanConfNow[$i+1],0 ,1) == ":") {
			echo "</fieldset>";
		}
	}
}
echo "</fieldset>";
$fs = fopen($chanConf, "r");
while(!feof($fs)) {
	$fileData = fread($fs, 100000);
}
fclose($chanConf);
echo "</br><h2><u>Kanalliste bearbeiten</u></h2>";
echo "<h2>Hier k&ouml;nnen die &Auml;nderungen vorgenommen werden</h2>";
echo "<h2>Fertige Eintr&auml;ge gibt es zum Beispiel bei <a href='http://channelpedia.yavdr.com/gen/DVB-S/S19.2E/de/' target='_blank'>Channelpedia</a></h2>";
echo "<form id='screentext' title='' class='panel' action='edit_channels.php' method='POST' selected='true'>";
echo "<textarea name='chanConfNew' rows=100 cols=163>".utf8_encode($fileData)."</textarea>";
echo "</br></br>";
echo "<a class='redButton' type='submit'>&Auml;nderungen &uuml;berpr&uuml;fen</a>";
echo "<input type='hidden' name='command' value='1'>";
echo "</br></br>";
echo "</form>";
}

if("$editCmd" == "1") { // channels.conf prüfen
echo "<div id='chanedit_check' class='panel' title='Neue Kanalliste' selected='true'>";
echo "<p>Neue Kanalliste pr&uuml;fen:</p>";
exec("echo \"".utf8_decode($_POST['chanConfNew'])."\" > ".$chanConf.".new");
exec ("diff -u ".$chanConf." ".$chanConf.".new", $chanDiff);
if(count($chanDiff) > 7) {
	echo "<fieldset>";
	echo "<li class='group'>&Auml;nderungen</li>";
	for($i=2; $i<count($chanDiff); $i++) {
		if((substr($chanDiff[$i], 0, 1) == "+" || substr($chanDiff[$i], 0, 1) == "-") && strlen($chanDiff[$i]) > 1) {
			$chanDiff[$i] = str_replace("+", " color='green'>&nbsp;&nbsp;+ ", substr($chanDiff[$i],0 ,1)).substr($chanDiff[$i], 1);
			$chanDiff[$i] = str_replace("-", " color='red'>&nbsp;&nbsp;- ",  substr($chanDiff[$i],0 ,1)).substr($chanDiff[$i], 1);
			echo "<font size=1px".utf8_encode($chanDiff[$i])."</br></font>";
		}
	}
	echo "</fieldset>";
} else {
	echo "<fieldset><p>Es wurden keine &Auml;nderungen an der Kanalliste vorgenommen</p></fieldset>";
}
$chanCount = 1;
for($i=0; $i<count($chanConfNew)-1; $i++) {
        if(substr($chanConfNew[$i],0 ,1) == ":") {
		if(substr($chanConfNew[$i],1 ,1) == "@") {
			$tmpChan = explode(" ", $chanConfNew[$i]);
			$chanCount = substr($tmpChan[0],2);
			echo "<fieldset><li class='group'>".$tmpChan[1].' '.$tmpChan[2].' '.$tmpChan[3].' '.$tmpChan[4].' '.$tmpChan[5]."</li>";
		} else {
                	echo "<fieldset><li class='group'>".explode(":", $chanConfNew[$i])[1]."</li>";
		}
        } else {
                echo "<font size=1px><b>&nbsp;&nbsp;".$chanCount."</b> - ".explode('|', explode(':', explode(',', explode(';', $chanConfNew[$i])[0])[0])[0])[0]."</font></br>";
                $chanCount++;
                if(substr($chanConfNew[$i+1],0 ,1) == ":") {
                        echo "</fieldset>";
                }
        }
}
echo "</fieldset>";
echo "</br>";
echo "<h2>Zum Speichern wird der VDR automatisch neu gestartet</h2>";
echo "<form id='channels' title='' class='panel' action='edit_channels.php' method='POST' selected='true'>";
echo "<a class='redButton' type='submit'>&Auml;nderungen speichern</a>";
echo "<input type='hidden' name='chanConfNew' value='".utf8_decode($_POST['chanConfNew'])."'>";
echo "<input type='hidden' name='command' value='2'>";
echo "</br></br>";
echo "</form>";

}

if("$editCmd" == "2") { // channels.conf schreiben
echo "<div id='chanedit_write' class='panel' title='Neue Kanalliste schreiben' selected='true'>";
exec("/etc/vectra130/scripts/.edit_channels_webif.sh write >/dev/null &2>/dev/null &");
echo "<fieldset><p>Neue Kanalliste wird geschrieben</br>VDR startet anschlie&szlig;end neu ...</p></fieldset>";

}

echo "</div>";
?>
