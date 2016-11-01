/* Scrollen unterbinden */
		function scroll_lock() {
		
			document.addEventListener("touchmove",function(a){a.preventDefault();},false);
			
		}

/*************************************************************************************************/


/* MyHome Schalter und Statusanzeigen aktualisieren */

function switch_refresh() {
				var request = false;

				delete Schalter;
				delete Status;
				delete Heizung;
				delete Timer;
				delete RelaisStatus;
				
				delete Refresh;
				
				var Status = document.getElementsByName('Status');
				var Schalter = document.getElementsByName('Schalter');
				var Taster = document.getElementsByName('Taster');
				var Eltako = document.getElementsByName('Eltako');
				var Timer = document.getElementsByName('Timer');
				var Heizung = document.getElementsByName('Heizung');
				var Jalousie_down = document.getElementsByName('Jalousie_down');
				var RelaisStatusInfo = document.getElementsByName('relais_statusinfo');
				if (Status) {
					for (i = 0 ; i < Status.length ; i++) {
						if (Status[i].className != "toggleOffline") {
							var Refresh = Refresh + "|" + Status[i].id;
						}
					}
				}
				if (Schalter) {
					for (i = 0 ; i < Schalter.length ; i++) {
						if (Schalter[i].className != "toggleOffline") {
							var Refresh = Refresh + "|" + Schalter[i].id;
						}
					}
				}
				if (Taster) {
					for (i = 0 ; i < Taster.length ; i++) {
						if (Taster[i].className != "toggleOffline") {
							if (Taster[i].className == "togglePushed") {
								Taster[i].className = "togglePush";
							}
						}
					}
				}
				if (Eltako) {
					for (i = 0 ; i < Eltako.length ; i++) {
						if (Eltako[i].className != "toggleOffline") {
							if (Eltako[i].className == "togglePushed") {
								Eltako[i].className = "togglePush";
							}
						}
					}
				}
				if (Timer) {
					for (i = 0 ; i < Timer.length ; i++) {
						if (Timer[i].className != "toggleOffline") {
							var Refresh = Refresh + "|" + Timer[i].id;
						}
					}
				}
				if (Heizung) {
					for (i = 0 ; i < Heizung.length ; i++) {
						if (Heizung[i].className != "toggleOffline") {
							var Refresh = Refresh + "|" + Heizung[i].id;
						}
					}
				}

				if (Jalousie_down) {
//					var Jalousie_stop = document.getElementsByName('Jalousie_stop');
//					var Jalousie_up = document.getElementsByName('Jalousie_up');
					for (i = 0 ; i < Jalousie_down.length ; i++) {
//						if (Jalousie_down[i].className != "toggleOffline") {
//							if (Jalousie_down[i].className == "toggleJal downPushed") {
//								Jalousie_down[i].className = "toggleJal down";
								var Refresh = Refresh + "|" + Jalousie_down[i].id;
//								alert(Jalousie_down[i].id);
//							}
//							if (Jalousie_stop[i].className == "toggleJal stopPushed") {
//								Jalousie_stop[i].className = "toggleJal stop";
//							}
//							if (Jalousie_up[i].className == "toggleJal upPushed") {
//								Jalousie_up[i].className = "toggleJal up";
//							}
//						}
					}
				}
				if (RelaisStatusInfo) {
					for (i = 0 ; i < RelaisStatusInfo.length / 16 ; i++) {
							var Refresh = Refresh + "|" + RelaisStatusInfo[0].id;
					}
				}

				if (Refresh) {
//				alert(Refresh);
				setRequest_Refresh(Refresh);
				}
	window.setTimeout(function () { switch_refresh(); }, 10000); // Alle x/1000 Sekunden neu nach änderungen suchen

}

	// Request senden
	function setRequest_Refresh(Refresh) {
		// Request erzeugen
			request = new XMLHttpRequest(); // Mozilla, Safari, Opera

		// überprüfen, ob Request erzeugt wurde
		if (!request) {
			alert("Kann keine XMLHTTP-Instanz erzeugen");
			return false;
		} else {
			var url = "./myhome/includes/refresh.php";
			// Request öffnen
			request.open('post', url + "?" +(Math.random()*100), true);
			// Requestheader senden
			request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			// Request senden
//			alert(Refresh);
			request.send('refresh='+Refresh);
			// Request auswerten
			request.onreadystatechange = interpretRequest_Refresh;
		}
	}

	// Request auswerten
	function interpretRequest_Refresh() {
		switch (request.readyState) {
			// wenn der readyState 4 und der request.status 200 ist, dann ist alles korrekt gelaufen
			case 4:
				if (request.status != 200) {
//					alert("Der Request wurde abgeschlossen, ist aber nicht OK\nFehler:"+request.status);
				} else {
					var content = request.responseText;
	delete getswitch;
	var getswitch = content.split("|");
//	alert(content);

	for (i=0; i<getswitch.length - 1; i++) {
			delete getexec;
			var getexec = getswitch[i].split("_");
//			alert("meldung: " + getswitch[i]);
			
		if (getexec[0] == "OK" || getexec[0] == " OK") { // Wenn Rückmeldung vom PHP-Script OK
				if (getexec[1] == "Schalter" || getexec[1] == "Timer") {
					var Schalter = document.getElementById(getexec[1]+"_"+getexec[2]+"_"+getexec[3]);
					if (Schalter.getAttribute('toggled') != getexec[4]) {
						Schalter.setAttribute('toggled', getexec[4]);
					}
					if (getexec[1] == "Timer") {
					var Schalter_Timer = document.getElementById(getexec[1]+"_"+getexec[2]+"_"+getexec[3]+"_status_timer");
						if (getexec[5] == 1) {
							Schalter_Timer.style.color = "#00aa00";
						} else {
							Schalter_Timer.style.color = "#666666";
						}						
					}
				}
				
				if (getexec[1] == "Status") {
					var Status = document.getElementById(getexec[1]+"_"+getexec[2]+"_"+getexec[3]);
					if (Status.className != getexec[4]) {
						Status.className = getexec[4];
					}
				}
				
				if (getexec[1] == "Heizung") {
//			alert("meldung: " + getswitch[i]);

					var Status = document.getElementById(getexec[1]+"_"+getexec[2]+"_"+getexec[3]+"_status");
					var Timer = document.getElementById(getexec[1]+"_"+getexec[2]+"_"+getexec[3]+"_timer");
					var Heizung = document.getElementById(getexec[1]+"_"+getexec[2]+"_"+getexec[3]);
					var Button = document.getElementById(getexec[1]+"_"+getexec[2]+"_"+getexec[3]+"_button");
					var Solltemp = document.getElementById(getexec[1]+"_"+getexec[2]+"_"+getexec[3]+"_solltemp");
					var SolltempSelect = document.getElementById(getexec[1]+"_"+getexec[2]+"_"+getexec[3]+"_solltempSelect");
					var Isttemp = document.getElementById(getexec[1]+"_"+getexec[2]+"_"+getexec[3]+"_isttemp");
//					var Isttemp_freeze = document.getElementById(getexec[1]+"_"+getexec[2]+"_"+getexec[3]+"_freeze");

					if (Status) {
						if (Status.className != getexec[4]) {
							Status.className = getexec[4];
						}
					Status.style.display = getexec[6];
					}					
					if (Heizung) {
						if (Heizung.getAttribute('toggled') != getexec[5]) {
							Heizung.setAttribute('toggled', getexec[5]);
						}
					}
					if (SolltempSelect) {
						if (SolltempSelect.selectedIndex != getexec[7]) {
							SolltempSelect.selectedIndex = getexec[7];
						}
					}
					if (Button) {
						Button.style.display = getexec[6];
					}
					if (Solltemp) {
						Solltemp.style.display = getexec[6];
					}
					if (Timer) {
						Timer.src = "/iUI-myhome/icons/Timer_icon" + getexec[9] + getexec[10] + ".gif";
					}
					if (Isttemp) {
//					alert(Isttemp.childNodes[0].innerHTML);
						if (getexec[8] < 5) {
							Isttemp.childNodes[0].innerHTML = getexec[8] + "&deg; C*";
						} else {
							Isttemp.childNodes[0].innerHTML = getexec[8] + "&deg; C";
						}
					}
//					if (Isttemp_freeze) {
//						if (getexec[8] < 5 && "getexec[8]" != "") {
//							Isttemp_freeze.style.display = "block";
//						} else {
//							Isttemp_freeze.style.display = "none";
//						}
//					}

				}
				if (getexec[1] == "Jalousie") {
					var Jalousie_up = document.getElementById(getexec[1]+"_"+getexec[2]+"_"+getexec[3]+"-up");
					var Jalousie_down = document.getElementById(getexec[1]+"_"+getexec[2]+"_"+getexec[3]+"-down");
					var Jalousie_stop = document.getElementById(getexec[1]+"_"+getexec[2]+"_"+getexec[3]+"-stop");
//			alert(Jalousie_up.className);
//alert(getexec[1]+"_"+getexec[2]+"_"+getexec[3]);

					if (getexec[4] == "up") {
						Jalousie_up.className	= "toggleJal upActiv";
						Jalousie_down.className = "toggleJal down";
						Jalousie_stop.className = "toggleJal stop";
					}
					if (getexec[4] == "down") {
						Jalousie_up.className	= "toggleJal up";
						Jalousie_down.className = "toggleJal downActiv";
						Jalousie_stop.className = "toggleJal stop";
					}
					if (getexec[4] == "false") {
						Jalousie_up.className	= "toggleJal up";
						Jalousie_down.className = "toggleJal down";
						Jalousie_stop.className = "toggleJal stop";
					}
				}
				if (getexec[1] == "RelaisStatus") {
					for (ii=1; ii<=8; ii++) {
						var Status = document.getElementById("Out_"+getexec[2]+"_"+ii);
						if (Status.className != getexec[ii+2]) {
							Status.className = getexec[ii+2];
						}
					}
					for (ii=1; ii<=8; ii++) {
						var Statusa = document.getElementById("aIn_"+getexec[2]+"_"+ii);
						var Statusd = document.getElementById("dIn_"+getexec[2]+"_"+ii);
							Statusa.innerHTML = getexec[(ii*2)+9];
						if (Statusd.className != getexec[(ii*2)+10]) {
							Statusd.className = getexec[(ii*2)+10];
						}
					}
//					alert(Status.className);
/*					for (ii=1; iii<=4; i++) {
						var Status = document.getElementById("dIn_"+getexec[2]+"_"+ii);
						if (Status.className != getexec[ii+14]) {
							Status.className = getexec[ii+14];
						}
					}*/
				}
				
				}
				}
				}			
				break;
			default:
				break;
		}
	}


/*************************************************************************************************/

/* Index-Elemente ein-/ausblenden bei Videos */
function indexeinblenden(index) {
img_up = "http://" + location.host + "/iUI-myhome/icons/arrow_up.gif";
img_down = "http://" + location.host + "/iUI-myhome/icons/arrow_down.gif";
if(document.getElementById(index).id=='imgidall') {
 if(document.getElementById('imgidall').src == img_down) {
		document.getElementById('imgidall').src = img_up;
		document.getElementsByName('auf')[0].innerHTML = "Alle ausblenden";
		document.getElementsByName('idNUM')[0].src = img_up;
		document.getElementById("idNUM").style.display='block';
			for (i = 1; i <= 26; i++) {
				document.getElementsByName('id'+String.fromCharCode(i+64))[0].src = img_up;
				document.getElementById('id'+String.fromCharCode(i+64)).style.display='block';
			}
 }
  else {
		document.getElementById('imgidall').src = img_down;
		document.getElementsByName('auf')[0].innerHTML = "Alle einblenden";
		document.getElementsByName('idNUM')[0].src = img_down;
		document.getElementById("idNUM").style.display='none';
			for (i = 1; i <= 26; i++) {
				document.getElementsByName('id'+String.fromCharCode(i+64))[0].src = img_down;
				document.getElementById('id'+String.fromCharCode(i+64)).style.display='none';
			}
  }
}

else {	
 if(document.getElementById(index).style.display=='none') {
  document.getElementById(index).style.display='block';
		document.getElementsByName(index)[0].src = img_up;
 }
  else {
  document.getElementById(index).style.display='none';
		document.getElementsByName(index)[0].src = img_down;
  }
}
}
/*************************************************************************************************/

/* Elemente ein-/ausblenden bei Ping */
function ping_einblenden(index) {
if(document.getElementById(index).id=='address') {
		document.getElementById("address").style.visibility='visible';
		document.getElementById("address1").style.visibility='hidden';
			if(document.getElementsByName("address")[0].value.length > 6) {
	document.getElementById("submit").type='submit';
	document.getElementById("submit").style.opacity='1.0';
			}
			else {
	document.getElementById("submit").type='cancel';
	document.getElementById("submit").style.opacity='0.2';
			}
}
if(document.getElementById(index).id=='address1') {
		document.getElementById("address").style.visibility='hidden';
		document.getElementById("address1").style.visibility='visible';
	document.getElementById("submit").type='submit';
	document.getElementById("submit").style.opacity='1.0';
}
}
/*************************************************************************************************/

/* Suchbutton ein-/ausblenden */
function search_button() {
if((document.getElementsByName("videos")[0].checked || document.getElementsByName("musik")[0].checked || document.getElementsByName("fotos")[0].checked) && document.getElementsByName("search")[0].value != "Videos, Fotos und Musik ..." && document.getElementsByName("search")[0].value != "  " && document.getElementsByName("search")[0].value != " " && document.getElementsByName("search")[0].value != "") {
	document.getElementById("submit").type='submit';
	document.getElementById("submit").style.opacity='1.0';
	document.getElementById("submit").className='bigblueButton';
}
else {
	document.getElementById("submit").type='cancel';
	document.getElementById("submit").style.opacity='0.2';
	document.getElementById("submit").className='grayButton';
}
}
/*************************************************************************************************/

/* Sendenbutton ein-/ausblenden bei Screentext */
function screentext() {
if((document.getElementsByName("vdr1")[0].checked || document.getElementsByName("vdr2")[0].checked) && document.getElementsByName("text")[0].value != "Text eingeben..." && document.getElementsByName("text")[0].value != "  " && document.getElementsByName("text")[0].value != " " && document.getElementsByName("text")[0].value != "") {
	document.getElementById("submit").type='submit';
	document.getElementById("submit").style.opacity='1.0';
	document.getElementById("submit").className='bigblueButton';
}
else {
	document.getElementById("submit").type='cancel';
	document.getElementById("submit").style.opacity='0.2';
	document.getElementById("submit").className='grayButton';
}
}
/*************************************************************************************************/

/* Vectranet - Offline/Online Zeilen einblenden */
function vectranet() {
if(document.getElementById("online").checked) {
var stat = document.getElementsByName('online');
	for (var i=0; i<stat.length; i++) {
		stat[i].style.display = "table-row";
	}
}
else {
var stat = document.getElementsByName('online');
	for (var i=0; i<stat.length; i++) {
		stat[i].style.display = "none";
	}
}

if(document.getElementById("offline").checked) {
var stat = document.getElementsByName('offline');
	for (var i=0; i<stat.length; i++) {
		stat[i].style.display = "table-row";
	}
}
else {
var stat = document.getElementsByName('offline');
	for (var i=0; i<stat.length; i++) {
		stat[i].style.display = "none";
	}
}

if(window.orientation == 0) {
alert('F\u00fcr eine bessere Darstellung das Smartphone quer halten');
}
        var table = document.getElementById('thetable');   
        var rows = table.getElementsByTagName("tr"); 
		var j = 0;
        for(var i = 1; i < rows.length-1; i++){
      //manipulate rows
if(rows[i].style.display != 'none') {
		if(j % 2 == 0){
            rows[i].className = "even";
          }else{
            rows[i].className = "odd";
          }
j++;
}
		}
}
/*************************************************************************************************/

/* Schalter für Relaiskarte */
	var request = false;

	// Request senden
	function setRequest(id) {
		// Request erzeugen
			request = new XMLHttpRequest(); // Mozilla, Safari, Opera

		// überprüfen, ob Request erzeugt wurde
		if (!request) {
			alert("Kann keine XMLHTTP-Instanz erzeugen");
			return false;
		} else {
			var url = "myhome/includes/relais_exec.php";
			var push = id.split("_");
			if (push[1] == "push") {
				document.getElementById(push[4]+'_'+push[2]+'_'+push[3]).className = 'togglePushed';
//				alert(push[4]+'_'+push[2]+'_'+push[3]);
			}
			if (push[1] == "down" || push[1] == "stop" || push[1] == "up") {
				document.getElementById(push[4]+'_'+push[2]+'_'+push[3]+'-'+push[1]).className = 'toggleJal '+push[1]+'Pushed';
			}
			// Request öffnen
			request.open('post', url + "?" +(Math.random()*100), true);
			// Requestheader senden
			request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			// Request senden
//			request.set('Pragma', 'no-cache');
//			alert(id);
			request.send('status='+id);
			// Request auswerten
			request.onreadystatechange = interpretRequest_switches;
		}
	}

	// Request auswerten
	function interpretRequest_switches() {
		switch (request.readyState) {
			// wenn der readyState 4 und der request.status 200 ist, dann ist alles korrekt gelaufen
			case 4:
				if (request.status != 200) {
					alert("Der Request wurde abgeschlossen, ist aber nicht OK\nFehler:"+request.status);
				} else {
					var content = request.responseText;
		if (!content || content.substr(1,2) != "OK") { // Wenn Rückmeldung vom PHP-Script nicht OK
			var fail = content.split("_");
			alert('Schaltaktion f\u00fcr >' + fail[2] + '< wurde nicht ausgef\u00fchrt!\nRelais 192.168.1.' + fail[6] + ' Port ' + fail[7] + ' konnte nicht angesteuert werden!\nRelais Offline?\n' + content);
				var Taster = document.getElementsByName('Taster');
				var Jalousie = document.getElementById(fail[5] + "_" + fail[4] + "_" + fail[2] + "-" + fail[1]);
				var Schalter = document.getElementById(fail[5] + "_" + fail[4] + "_" + fail[2]);
				if (Taster) {
					for (i = 0 ; i < Taster.length ; i++) {
					Taster[i].className = 'togglePush';
					}
				}
				if (Jalousie) {
					Jalousie.className = 'toggleJal ' + fail[1];
				}
				if (Schalter) {
					if (fail[1] == "on") {
					Schalter.setAttribute('toggled', 'false');
					if (document.getElementsByName('visible')) {
						visible_einblenden('none');
					}
					}
					if (fail[1] == "off") {
					Schalter.setAttribute('toggled', 'true');
					if (document.getElementsByName('visible')) {
						visible_einblenden('block');
					}
					}
				}
		} else {
		if (content.substr(0,4) == "BUSY") { // Wenn Rückmeldung vom PHP-Script BUSY
			var busy = content.split("_");
			alert('Eine Schaltaktion f\u00fcr >' + busy[2] + '< wird gerade ausgef\u00fchrt!\nIn einigen Sekunden nochmal probieren');
				var Jalousie = document.getElementById(busy[5] + "_" + busy[4] + "_" + busy[2] + "-" + busy[1]);
				if (Jalousie) {
					Jalousie.className = 'toggleJal ' + busy[1];
				}
		} else {


			// den Inhalt des Requests in Array schreiben
			var push = content.split("_");
			// Kontrollfenster öffnen
//			alert(push[5].substr(0,5)+'_'+push[4]+'_'+push[2]);
//			alert('Schalter ID: '+push[2]+'\nSchalter Name: '+push[1]+'\nSchalter Typ: '+push[4]+'\nSchalter Status: '+push[0]+'\nOrdner: '+push[3]);
			// Taster zurücksetzen
			if (push[1] == "push") {
				document.getElementById(push[5]+'_'+push[4]+'_'+push[2]).className = 'togglePush';
			}
			if (push[1] == "down" || push[1] == "stop" || push[1] == "up") {
				document.getElementById(push[5]+'_'+push[4]+'_'+push[2]+'-'+push[1]).className = 'toggleJal '+push[1];
			}
				document.getElementById('label_'+push[2]).setAttribute("style", "text-decoration:underline;");
				window.setTimeout(function () { origfont('label_'+push[2]); }, 500);
				}
				}
				}
				break;
			default:
				break;
		}
	}

/*************************************************************************************************/
/* Schrift in Original zurückversetzen */
	function origfont(name) {
				document.getElementById(name).setAttribute("style", "text-decoration:none;");
	}
	
/*************************************************************************************************/
/* Elemente mit dem Namen "visible/invisible" ein/ausblenden */

	function visible_einblenden(status) {
		if (status.split("_")[0] == "block") {
			status2 = "none";
		} else {
			status2 = "block";
		}
		if(!status.split("_")[1]) {
			var visible = document.getElementsByName('visible');
			var invisible = document.getElementsByName('invisible');
		} else {
			var visible = document.getElementsByName('visible'+status.split("_")[1]);
			var invisible = document.getElementsByName('invisible'+status.split("_")[1]);
		}
		for (i = 0; i < visible.length; i++) {
			visible[i].style.display = status.split("_")[0];
		}
		for (i = 0; i < invisible.length; i++) {
			invisible[i].style.display = status2;
		}
	}

/*************************************************************************************************/
/* Cronjobs in myhome_timer_* aktivieren/deaktivieren */

	var request = false;

	// Request senden
	function timer(input) {
		// Request erzeugen
			request = new XMLHttpRequest(); // Mozilla, Safari, Opera

		// überprüfen, ob Request erzeugt wurde
		if (!request) {
			alert("Kann keine XMLHTTP-Instanz erzeugen");
			return false;
		} else {
			var url = "myhome/includes/timer_exec.php";
			// Request öffnen
			request.open('post', url + "?" +(Math.random()*100), true);
			// Requestheader senden
			request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			// Request senden
//			alert(input);
			request.send('status='+input);
			// Request auswerten
			request.onreadystatechange = interpretRequest_timer;
		}
	}

	// Request auswerten
	function interpretRequest_timer() {
		switch (request.readyState) {
			// wenn der readyState 4 und der request.status 200 ist, dann ist alles korrekt gelaufen
			case 4:
				if (request.status != 200) {
					alert("Der Request wurde abgeschlossen, ist aber nicht OK\nFehler:"+request.status);
				} else {
					var content = request.responseText;
//					alert(content);
		if (!content) { // Wenn Rückmeldung vom PHP-Script nicht OK
			alert('Aktion fehlgeschlagen');
		} else {
			// Kontrollfenster öffnen
//			alert(content);
		if(content.split("_")[1] == "master" && content.split("_")[2] != "noInfo") {
			alert('Timer ' + content.split("_")[0]);
		}
				}
				}
				break;
			default:
				break;
		}
	}

/*************************************************************************************************/
/* Soll Temperatur bei Thermostat ändern und Thermostat de-/aktivieren */

	var request = false;

	// Request senden
	function thermostat(input) {
		// Request erzeugen
			request = new XMLHttpRequest(); // Mozilla, Safari, Opera

		// überprüfen, ob Request erzeugt wurde
		if (!request) {
			alert("Kann keine XMLHTTP-Instanz erzeugen");
			return false;
		} else {
			var url = "myhome/includes/heizung_exec.php";
			// Request öffnen
			request.open('post', url + "?" +(Math.random()*100), true);
			// Requestheader senden
			request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			// Request senden
//			alert(input);
			request.send('set='+input);
			// Request auswerten
			request.onreadystatechange = interpretRequest;
		}
	}

	// Request auswerten
	function interpretRequest() {
		switch (request.readyState) {
			// wenn der readyState 4 und der request.status 200 ist, dann ist alles korrekt gelaufen
			case 4:
				if (request.status != 200) {
					alert("Der Request wurde abgeschlossen, ist aber nicht OK\nFehler:"+request.status);
				} else {
					var content = request.responseText;
//					alert(content);
		if (!content) { // Wenn Rückmeldung vom PHP-Script OK
			alert('Aktion fehlgeschlagen');
		}
		}
				break;
			default:
				break;
		}
	}

/*************************************************************************************************/

/* Relais-Zuordnung ändern */

	var request = false;

	// Request senden
	function relais_edit(input) {
		// Request erzeugen
			request = new XMLHttpRequest(); // Mozilla, Safari, Opera

		// überprüfen, ob Request erzeugt wurde
		if (!request) {
			alert("Kann keine XMLHTTP-Instanz erzeugen");
			return false;
		} else {
			var url = "myhome/includes/relais_edit.php";
			// Request öffnen
			request.open('post', url + "?" +(Math.random()*100), true);
			// Requestheader senden
			request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			// Request senden
			request.send('set='+input+'&exec=1');
//			alert(input);
			// Request auswerten
			request.onreadystatechange = interpretRequest;
		}
	}

/*************************************************************************************************/

/* Bei Inaktivität auf Startseite springen */

// Wartezeitzeit bis zur Weiterleitung
var Timeout_Wartezeit = 600;
var Timeout_CounterSec = Timeout_Wartezeit;

function Timeout_Timer(ZielURL) {
Timeout_CounterSec --;

// weiterleitung bei timeout
if (!Timeout_CounterSec) {
//window.location.href = ZielURL;
window.location.reload();
}
else Timeout_down = setTimeout("Timeout_Timer()", 1000);
}

/*************************************************************************************************/

