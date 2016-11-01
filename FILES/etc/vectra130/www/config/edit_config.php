
<?php
error_reporting(0);
$_SESSION['rand'] = $rand;
$file = explode("?", $_GET['file'])[0];
$tmpFile = "/tmp/".exec("echo $(basename ".$file." )");
exec("ls ".$file." | wc -l", $exist);
?>

<div id="exec" class="panel" title="Systemdatei bearbeiten" selected="true">


</br>
<?php
if($exist[0] == 1) {
	echo "<fieldset><p>".$tmpFile."</p></fieldset>";
	exec("cp ".$file." ".$tmpFile);
	$fs = fopen($tmpFile, "r");
	while(!feof($fs)) {
	        $fileData = fread($fs, 100000);
	}
	fclose($chanConf);
	echo "<form id='screentext' title='' class='panel' action='edit_config.php' method='POST' selected='true'>";
	echo "<textarea name='conffile' rows=100 cols=55>".utf8_encode($fileData)."</textarea>";
	echo "</br></br>";
	echo "<a class='redButton' type='submit'>&Auml;nderungen &uuml;berpr&uuml;fen</a>";
	echo "<input type='hidden' name='command' value='1'>";
	echo "</br></br>";
	echo "</form>";

} else {
	echo "<fieldset><p>Datei ".$file." nicht gefunden</p></fieldset>";
}
?>
</div>
