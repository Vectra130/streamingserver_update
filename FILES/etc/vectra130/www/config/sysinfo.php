<?php
exec('LANG=de_DE.UTF-8 LC_ALL=de_DE.UTF-8 /etc/vectra130/scripts/sysinfo.sh once', $info);
echo "<div class='panel' id='sysinfo' title='System Infos' selected='true'>";
echo "<fieldset><p>";
for ($i=0; $i<count($info); $i++) {
	echo "$info[$i]"."</br>";
}
echo "</p></fieldset></div>";
?>

