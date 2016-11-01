
echo "<div id='chanedit_write' class='panel' title='Neue Kanalliste schreiben' selected='true'>";
echo "<p>Neue Kanalliste geschrieben</p>";
$chanCount = 1;
exec("echo \"".$_POST['chanConfNew']."\" > ".$chanConf.".new");


?>
</div>
