<?php
//			error_reporting(0);
			require_once "/var/www/iUI-myhome/iui/AvrNetIoEthersexClass.php";
			function set_switch($step) {
				global $location;
				global $relais;
				global $visible;
				global $direct_room;
				if ("$direct_room" == "") {
					$direct_room = $_GET['direct_room'];
				}
				
				exec('sudo cat /iUI-myhome/myhome/'.$location.'/switch.config', $switch_config);
			
				for($relais = 2; $relais < count($switch_config); $relais++) {
//	echo date("s")." ".$switch_config[$relais];
					$switch = explode("|", $switch_config[$relais]);
					$before = explode("|", $switch_config[$relais - 1]);
					$next = explode("|", $switch_config[$relais + 1]);

					if("$switch[2]" == "visible") {
						$display = $visible;
					}
					if("$switch[2]" == "invisible") {
						if($visible == "block") {
							$display = "none";
						} else {
							$display = "block";
						}
					}
		if("$switch[0]" == "Gruppe") {
						echo "<fieldset name='".$switch[2]."' style='display: ".$display."'>";
						echo "<li class='group'>".$switch[1]."</li>";
						if("$next[0]" == "Gruppe" || "$next[0]" == "") {
							echo "</fieldset>";
						}
					}
if("$switch[0]" == "Schalter" || "$switch[0]" == "Taster" || "$switch[0]" == "Eltako" || "$switch[0]" == "Jalousie" || "$switch[0]" == "Heizung" || "$switch[0]" == "Status" || "$switch[0]" == "Timer") {

$name		= $switch[1];
$typ		= $switch[0];
$id		= sprintf("%'02d",$relais);
$extra1		= $switch[2];
$karte_id	= $switch[3];
if ($switch[4] >= 1 && $switch[4] <= 8) {
	$port_id	= $switch[4];
	$relais_block = 2;
}
						if ($switch[4] >= 9 && $switch[4] <= 16) {
							$port_id	= (int)$switch[4] - 8;
							$relais_block = 3;
						}
						if ($switch[4] >= 17 && $switch[4] <= 24) {
							$port_id	= (int)$switch[4] - 16;
							$relais_block = 0;
						}
						if ($switch[4] >= 25 && $switch[4] <= 26) {
							$port_id	= (int)$switch[4] - 24;
							$relais_block = 1;
						}
$karte2_id	= $switch[5];						
$port2_id	= $switch[6];						
$extra2		= $switch[7];						
$extra3		= $switch[8];						

if("$relais" == 2 ) {
	echo "<fieldset name='".$extra1."' style='display: ".$display."'>";
}

echo "<div class='row'>";

echo "<label id='label_".$name."'>".$name."</label>";

	get_status($name,$typ,$id,$karte_id,$port_id,$karte2_id,$port2_id,$extra1,$extra2,$extra3,$switch[4],$relais_block);

echo "</div>";
						
						if("$next[0]" == "Gruppe" || "$next[0]" == "") {
							echo "</fieldset>";
						}

					}
				}
			if ("$direct_room" != "true") {
				echo "<ul><li class='group'><a href='/iUI-myhome/myhome/includes/relais_edit.php?location=".$location."'>&nbsp;</a></li></ul>";
			} else {
			}
			}

			function get_status($name,$typ,$id,$karte_id,$port_id,$karte2_id,$port2_id,$extra1,$extra2,$extra3,$switch,$relais_block) {
			
				global $location;
				
				if($typ == "Status") {
					$avr = new AvrNetIo("192.168.1.".$karte_id);
					if ($avr->connect()) {
					$exec_status = $avr->getPin($relais_block, $port_id);
					if($exec_status == "0") {
					echo "<div name='".$typ."' id='".$typ."_".$location."_".$name."'  class='statusLedOff'></div>";
					} else if($exec_status == "1") {
					echo "<div name='".$typ."' id='".$typ."_".$location."_".$name."'  class='statusLedOn'></div>";
					} else {
					echo "<div name='".$typ."' id='".$typ."_".$location."_".$name."' class='toggleOffline'></div>";
					}
					$avr->disconnect();
					} else {
						echo "<div name='".$typ."' id='".$typ."_".$location."_".$name."' class='toggleOffline'></div>";
					}
				} else {
				if("$karte_id" != "" && "$karte_id" >= 231 && "$karte_id" <= 240 && "$extra3" != "TempOnly") {
					$avr = new AvrNetIo("192.168.1.".$karte_id);
					if ($avr->connect()) {
					$status_relais = $avr->getPin($relais_block, $port_id);
				if("$status_relais" == "1") {
					$status 	= "true";
					$visible 	= "block";
					$led		= "On";
					exec('sudo echo 1 > /iUI-myhome/myhome/'.$location.'/.status_relais'.$id);

				}
				if("$status_relais" == "0") {
					$status 	= "false";
					$visible 	= "none";
					$led		= "Off";
					exec('sudo echo 0 > /iUI-myhome/myhome/'.$location.'/.status_relais'.$id);
				}
				if("$status_relais" != "0" && "$status_relais" != "1" && "$karte_id" != "all") {
					echo "<div name='".$typ."' id='".$typ."_".$location."_".$name."' class='toggleOffline'></div>";
				} else {
				if($typ == "Schalter") {
					echo "<div id='".$typ."_".$location."_".$name."' name='".$typ."' class='toggle' toggled=".$status."><span class='thumb'></span><span class='toggleOn'";?> onclick="setRequest('<?php echo $id."_off_".$location."_".$name."_".$typ; ?>');"<?php echo ">l</span><span class='toggleOff'";?> onclick="setRequest('<?php echo $id."_on_".$location."_".$name."_".$typ; ?>');"<?php echo ">O</span></div>";
				}
				if($typ == "Timer") {
					exec("sudo cat /iUI-myhome/myhome/".$location."/.status_timer", $status_timer);
					if ($status_timer[0] == "1") {
						$color	= "#00aa00";
					} else {
						$color	= "#666666";
					}
					echo "<div id='".$typ."_".$location."_".$name."' name='".$typ."' class='toggle' toggled=".$status."><span class='thumb'></span><span class='toggleOn'";?> onclick="setRequest('<?php echo $id."_off_".$location."_".$name."_".$typ; ?>');"<?php echo ">l</span><span class='toggleOff'";?> onclick="setRequest('<?php echo $id."_on_".$location."_".$name."_".$typ; ?>');"<?php echo ">O</span></div>";
					echo "<div style='margin-right: 90px;' name='".$typ."' class='togglePush'><a href='myhome/includes/timer.php?location=".$location."&typ=Timer' id='".$typ."_".$location."_".$name."_status_timer' style='text-decoration: none; color: ".$color.";'><b>Timer</b></a></div>";
				}
				if($typ == "Eltako") {
					echo "<div name='".$typ."' id='".$typ."_".$location."_".$name."' "; ?> onclick="setRequest('<?php echo $id."_push_".$location."_".$name."_".$typ; ?>'); return false;" <?php echo " class='togglePush'><font color=#dddddd><b>&Pi;</b></font></div>";
				}
				if($typ == "Taster") {
					echo "<div name='".$typ."' id='".$typ."_".$location."_".$name."' "; ?> onclick="setRequest('<?php echo $id."_push_".$location."_".$name."_".$typ; ?>'); return false;" <?php echo " class='togglePush'></div>";
				}
				if($typ == "Jalousie") {
					$exec_status	 = $avr->getPin($relais_block, $port_id);
					$up_status	 = $avr->getPin($relais_block, ($port_id + 1));
					if("$exec_status" == "1") {
						if ("$up_status" == "1") {
							$jal_up		= "Activ";
							$jal_down	= "";
						} else {
							$jal_up		= "";
							$jal_down	= "Activ";
						}
					}
					echo "<div name='".$typ."_down' id='".$typ."_".$location."_".$name."-down' "; ?> onclick="setRequest('<?php echo $id."_down_".$location."_".$name."_".$typ; ?>'); return false;" <?php echo " class='toggleJal down".$jal_down."'></div>";
					echo "<div name='".$typ."_stop' id='".$typ."_".$location."_".$name."-stop' "; ?> onclick="setRequest('<?php echo $id."_stop_".$location."_".$name."_".$typ; ?>'); return false;" <?php echo " class='toggleJal stop'></div>";
					echo "<div name='".$typ."_up' id='".$typ."_".$location."_".$name."-up'"; ?> onclick="setRequest('<?php echo $id."_up_".$location."_".$name."_".$typ; ?>'); return false;" <?php echo " class='toggleJal up".$jal_up."'></div>";
				}
				if($typ == "Heizung") {
				  if($karte_id != $karte2_id) {
					$avr->disconnect();
					$avr = new AvrNetIo("192.168.1.".$karte2_id);
					if ($avr->connect()) {
						$ist_temp = $avr->getOneWire($extra3);
						$avr->disconnect();
						$avr = new AvrNetIo("192.168.1.".$karte_id);
						$avr->connect();
					}
				  } else {
						$ist_temp = $avr->getOneWire($extra3);
				  }
					$status_heizung[0] = $avr->getPin("0", $port_id);
					exec("sudo cat /iUI-myhome/myhome/".$location."/.soll_temp", $soll_temp);
					exec("sudo cat /iUI-myhome/myhome/".$location."/.status_timer", $status_timer);
					exec("sudo cat /iUI-myhome/myhome/".$location."/.set_heizung", $set_heizung);
				$status_relais = $status_heizung[0];
				if("$status_relais" == "1") {
					$status = "true";
					$visibility = "block";
				}
					else {
					$status = "false";
					$visibility = "none";
				}
				echo "<label>Thermostat</label>";
				echo "<div id='".$typ."_".$location."_".$name."' name='".$typ."' class='toggle' toggled=".$status."><span class='thumb'></span><span class='toggleOn' onclick=\"thermostat('set_0_".$location."_".$karte_id."_".$port_id."');  visible_einblenden('none_thermostat');\" >l</span><span class='toggleOff'  onclick=\"thermostat('set_1_".$location."_".$karte_id."_".$port_id."');  visible_einblenden('block_thermostat');\" >O</span></div>";
					echo "<div id='".$typ."_".$location."_".$name."_button' style='margin-right: 92px; display: ".$visibility.";' name='visiblethermostat' class='togglePush'><a href='myhome/includes/timer.php?location=".$location."&typ=Heizung' style='text-decoration: none;'><font color=#666><b>Timer</b></font></a></div>";
		
					echo "</div>";
					echo "<div class='row'><div id='".$typ."_".$location."_".$name."_isttemp' class='togglePush'><a href='myhome/includes/Temperaturverlauf.php?location=".$name."&onewire_id=".$extra3."' style='text-decoration: none; color: black;'>".$ist_temp."&deg; C";
					if ($ist_temp < 5) {
						echo "*";
					}
					echo "</a></div>";
					echo "<div id='".$typ."_".$location."_".$name."_solltemp' name='visiblethermostat' style='float: right; margin-top: 9px; margin-right: 10px; display: ".$visibility.";' class='row'>Soll:<select id='".$typ."_".$location."_".$name."_solltempSelect' onChange=\"thermostat('temp_'+this.value+'_".$location."')\"; name='temp'>";
					for ($temp = 5; $temp <= 30; $temp++) {
						echo "<option value='".$temp."'";
						if($temp == $soll_temp[0]) {
							echo " selected";
						}
						echo ">".$temp."&deg; C</option>";
					}
					echo "</select></div>";
					echo "<div style='float: left; margin-left: 15px; display: ".$visibility.";' name='visiblethermostat' id='".$typ."_".$location."_".$name."_status' class='statusLed".$led."'></div>";
					if ("$ist_temp" < 5) {
						$freeze_visibility = "block";
					} else {
						$freeze_visibility = "none";
					}
					echo "<img style='float: left; margin-left: 2px; margin-top: 9px; display: ".$visibility.";' name='visiblethermostat' id='".$typ."_".$location."_".$name."_timer' src='/iUI-myhome/icons/Timer_icon".$status_timer[0].$set_heizung[0].".gif'></img>";
				}
				
				}
					$avr->disconnect();
				} else {
					echo "<div name='".$typ."' id='".$typ."_".$location."_".$name."' class='toggleOffline'></div>";
				}
				} else {
				if ("$typ" == "Heizung" && "$extra2" == "TempOnly" && "$karte2_id" != "" && "$extra3" != "") {
					$avr = new AvrNetIo("192.168.1.".$karte2_id);
					if ($avr->connect()) {
						$ist_temp = $avr->getOneWire($extra3);
						echo "<label>".$name."</label>";
						echo "<div id='".$typ."_".$location."_".$name."_isttemp' name='".$typ."' class='togglePush'><a href='myhome/includes/Temperaturverlauf.php?location=".$name."&onewire_id=".$extra3."' style='text-decoration: none; color: black;'>".$ist_temp."&deg; C";
					if ($ist_temp < 5) {
						echo "*";
					}
					echo "</a></div>";
						$avr->disconnect();
					} else {
						echo "<div name='".$typ."' id='".$typ."_".$location."_".$name."_isttemp' class='toggleOffline'></div>";
					}
				} else 

//  Zentralschaltungen
				if("$karte_id" == "all") {
					if($typ == "Jalousie") {
						echo "<div name='".$typ."_down' id='".$typ."_".$location."_".$name."-down' "; ?> onclick="setRequest('<?php echo $id."_down_".$location."_".$name."_".$typ; ?>'); return false;" <?php echo " class='toggleJal down'></div>";
						echo "<div name='".$typ."_stop' id='".$typ."_".$location."_".$name."-stop' "; ?> onclick="setRequest('<?php echo $id."_stop_".$location."_".$name."_".$typ; ?>'); return false;" <?php echo " class='toggleJal stop'></div>";
						echo "<div name='".$typ."_up' id='".$typ."_".$location."_".$name."-up'"; ?> onclick="setRequest('<?php echo $id."_up_".$location."_".$name."_".$typ; ?>'); return false;" <?php echo " class='toggleJal up'></div>";
				}
				
// Ende Zentralschaltungen				
				} else {
					echo "<div name='".$typ."' id='".$typ."_".$location."_".$name."' class='toggleOffline'></div>";
				}

				
				
				}
				}
			}
?>
