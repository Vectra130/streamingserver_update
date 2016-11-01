<?php
error_reporting(0);
session_start();
$hostname = $_SESSION['hostname'];
$SYSTEMTYP = $_SESSION['SYSTEMTYP'];
$rand = $_SESSION['rand'];
$COMMAND = $_GET['command'];
$NAME =$_GET['name'];
?>
<!--<!DOCTYPE html>
<html>
<head>
        <title><? echo $SYSTEMTYP; ?> Config (<?php echo $hostname;?>)</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width initial-scale=1.0 maximum-scale=1.0 user-scalable=0"/>
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black" />
        <meta name="format-detection" content="telephone=no">
        <meta http-equiv="cache-control" content="no-store,no-cache, must-revalidate,post-check=0, pre-check=0,max-age=0">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">
        <link rel="apple-touch-startup-image" href="images/<? echo $SYSTEMTYP; ?>-startup.jpg?<?php echo $rand; ?>">
        <link rel="apple-touch-icon" href="images/<? echo $SYSTEMTYP; ?>-iui-icon.png?<?php echo $rand; ?>" />
        <link rel="icon" type="image/png" href="images/<? echo $SYSTEMTYP; ?>-iui-icon.png?<?php echo $rand; ?>">
        <link rel="stylesheet" href="iui/iui.css?<?php echo $rand; ?>" type="text/css" />
        <link rel="stylesheet" href="iui/iui-ext.css?<?php echo $rand; ?>" type="text/css" />
        <link rel="stylesheet" href="iui/t/default/default-theme.css?<?php echo $rand; ?>"  type="text/css"/>
        <link rel="stylesheet" href="iui/iui-eigenes.css?<?php echo $rand; ?>" type="text/css" />
        <link rel="stylesheet" href="iui/iui-eigenes.css?<?php echo $rand; ?>" type="text/safari" />
        <script type="application/x-javascript" src="iui/iui.js?<?php echo $rand; ?>"></script>
        <script type="text/javascript" src="iui/iui-eigenes.js?<?php echo $rand; ?>"></script>

</head>
<body>-->
    <div class="toolbar">
        <h1 id="pageTitle"> </h1>
        <a id="backButton" class="button" href="#"></a>
        <a class="button" href="#home">Home</a>
    </div>


<div id="exec-command" class="panel" title="Ausf&uuml;hrung" selected="true">


</br>
<?php
exec($COMMAND, $BACK);
echo "<fieldset><p>Befehl '".$NAME."' wurde ausgef&uuml;hrt.</br></p></fieldset>";
if(count($BACK) == 1) {
	echo "<fieldset><p><u>R&uuml;ckmeldung:</u><b></br>";
	echo $BACK[count($BACK)-1];
	echo "</br></br></b></p></fieldset>";
}
if(count($BACK) >= 2) {
	echo "<fieldset><p><u>R&uuml;ckmeldung:</u><b></br>";
	for($i=0; $i<count($BACK); $i++) {
		echo $BACK[$i]."</br>";
	}
	echo "</br></br></b></p></fieldset>";
}
?>
<!--</br>
<a onclick='window.close();' class='grayButton'>OK</a>
</br>-->
</div>
<!--</body>
</html>-->
