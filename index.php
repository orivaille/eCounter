<?php 
//
$codeVersion=1;
$codeRelease='0.0';
//PHP Version 5.5.9
//eCounter backend
//olivier rivaille
//01DEC17
//orivaille@free.fr
//
/*----------------------------------------------------------------
GPL License 
    <This program> is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>. 

+  fichier License GPL 

https://www.gnu.org/licenses/licenses.html#GPL
https://www.gnu.org/licenses/lgpl.txt
-----------------------------------------------------*/
//========================================================================
 include ('eCounter_classes.php');  
 include ('eCounter_classes2.php'); 
//========================================================================
//
if(isset($_REQUEST['L']))  { $Lg=$_REQUEST['L'];}  else {$Lg='en';}
 include ('./LANGUAGES/'.$Lg.'.php');			// include language  file
// start new page
$StartSequence=0;
$webPage = new SitePage($siteTitle, $codeRelease, $cssFile, $jsFile, $C_eCounterLive, $metaPage,$AD_deviceFeatures,$AD_textElements);
  if(!$webPage->OK) 
  { error_log("ERROR - new SitePage(".$siteTitle.",".$codeRelease.",". $cssFile.")  not done", 0); }
  //
$C_devicesFolder="DEVICES/";			// Devices Folder ( 1 per device including logs & svg gnerated) !!!!! ATTENTION  TEST !!!!!!!
$C_waitInterval = 30000;					// wait interval  milliseconds  threshold
$A_devices=array();
//
function getClientIP(){
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
    $ip = $_SERVER['REMOTE_ADDR'];
  }
  return $ip;
}
//
function ScanDirectory($Directory, &$A_devices){
  $MyDirectory = opendir($Directory) or die("ERREUR - cannot open ".$Directory."  <br/><a href='".$homePage.".php'> retour</a>\n");
  while($Entry = readdir($MyDirectory)) 
  {
	  if(is_dir($Directory.'/'.$Entry)&& $Entry != '.' && $Entry != '..') 
	  {
	   //echo "    <a href='eCounter.php?dev=".$Entry."' target='_blank' style='color:#fff; font-weight:bold;'>&nbsp;".$Entry."&nbsp;</a> \n";
	   $A_devices[]=$Entry;
	  }
  }
  closedir($MyDirectory);
}
  //
  //
  $webPage->PageHead();
  $device=""; 
  $Lg='En';
//
  $webPage->PageBodyHeader($device,$homePage,$eCounterImages_A,$eCounterImages_F,$A_unitFields,$A_inputFields,$A_inputFieldsL,$AD_deviceFeatures, $AD_liveGraphs,$StartSequence,$A_lastMeasures,$Lg,$AD_textElements);
  $s="";
  $s .="<div id='eCounterDevices'>\n";
		 $s .="  <form action='".$_SERVER['PHP_SELF'];    
		 if(isset($_REQUEST['Z'])) {$s .= "?Z=".$_REQUEST['Z'];}
		 $s .="' method='post'>\n";
		 $s .="   <select  name='Z'  id='Zarea' class='Z' placeholder=' choose area' onchange='this.form.submit();' >\n";
		 $s .="       <option value='48.534279,10.258284'>  </option>\n";
		 $s .="       <option value='48.534279,10.258284'> Europa </option>\n";
		 $s .="       <option value='37.67005,238.16917'> America </option>\n";
		 $s .="       <option value='-22.270914,166.451168'> Asia-Pacific </option>\n";
		 $s .="       </select>\n";
		 $s .="   </form>\n"; 

	echo $s; $s="";
 	ScanDirectory($C_devicesFolder, $A_devices);
  //$s .="  </ul>\n";
  $s .="</div> \n";
	echo $s; $s="";
//echo"<br/>"; print_r($A_devices);
//
$ipaddress = getClientIP();
if($_SERVER["HTTP_HOST"] == "127.0.0.1") {$ipaddress="86.71.235.106";}  //  test local

//
//echo "<br/>- ipaddress=".$ipaddress ."\n";

?>
<style type="text/css">
leaflet-div-icon {
	background: transparent;
	border: none;
}
.leaflet-marker-icon .number{
	position: relative;
	top: -37px;
	font-size: 12px;
	width: 25px;
	text-align: center;}


.Ico2 {
  background-color: rgba(255,165,0,0.8);
  width: 3rem;
  height: 3rem;
  display: block;
  left: -1.5rem;
  top: -1.5rem;
  position: relative;
  border-radius: 2rem 2rem 0rem 2rem;
  -ms-transform: rotate(45deg); /* IE 9 */
  -webkit-transform: rotate(45deg); /* Chrome, Safari, Opera */
  transform: rotate(45deg);
  border: 1px solid #FFFFFF;
}

</style>
<div id="map" style="width: 100%; height: 600px;"></div>
<script>
//----------------GeoLoc  functions ------ ip-api.com ----------
    function getJson(json)
    {
		var myLoc="n/a";
        
        var wlon='lon";d:';
        var wlat='lat";d:';
		var s = json.indexOf(wlat)
		    wlat= extractLatLng(json,wlat); 
		    wlon= extractLatLng(json,wlon); 
		var  myLoc=wlat+","+wlon;
		return myLoc;
    }

    function MMxmlHttpRequest(i)
    {
        window.MMxmlHttpRequest
        {
            xmlhttp = new XMLHttpRequest();
        };
		MyScript="http://ip-api.com/php/"+i;
		//MyScript="http://ipinfo.io/"+i+"/geo";
        xmlhttp.open("GET", MyScript, false);
        xmlhttp.send();
        loadXMLDoc = xmlhttp.responseText ;
		f = loadXMLDoc;
        var myLoc=getJson(f);
		return myLoc;
    }
   var myLoc="";
<?php
    echo" myLoc=MMxmlHttpRequest('".$ipaddress."');\n";
?>
//
function extractLatLng(l,s)
{
	var  sl = l.indexOf(s);
	wlaln=l.substring((sl+s.length),l.length); 
	wlaln=wlaln.substring(0,wlaln.indexOf(";"))
	return wlaln;
}
function floatPrecision(s)
{
	var ws=s.indexOf(".");
	var wsE=s.substring(0,ws);  wsD=s.substring(ws+1,s.length);
	var wsEN=parseInt(wsE)*Math.pow(10,	wsD.length);
	var wS=wsEN + parseInt(wsD);
		wS=(wS/(Math.pow(10,wsD.length))).toFixed(5);
	return wS;
}
//
if(myLoc.indexOf(",") > -1)
{ 
	var	virgule=myLoc.indexOf(",");
	var Slat=myLoc.substring(0,virgule);
	var Slng=myLoc.substring(virgule+1,myLoc.length);
	//alert("Slat="+Slat+"  Slng="+Slng);
	var lat=floatPrecision(Slat);
	var lng=floatPrecision(Slng);
	if(isNaN(lat) || isNaN(lng)) {myLoc="n/a";}
} 
//if(myLoc=="n/a")  {lng=0; lat=0;}
if(myLoc=="n/a")  {lng=11.66658; lat=204.25781;}
//alert("lat="+lat+"  lng="+lng);
//-----------------------------------------------------------------------
//--- Leaflet - OpenStreetMao ---- build map ----------------------------
//var map = L.map('map').setView([48.7054,2.4504], 13);
<?php

if(isset($_REQUEST['Z'])) 	{
		$coord= explode(",", $_REQUEST['Z']);
		$lat=trim($coord[0]," "); if($lat==0) {$lat=48.534279;}
		$lng=trim($coord[1]," "); if($lng==0) {$lng=10.258284;}
echo "var map = L.map('map').setView([".$lat.",".$lng."], 4);\n";
}
else {echo  "var map = L.map('map').setView([lat,lng], 4);";}
?>
//var map = L.map('map').setView([lat,lng], 4);
L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
		maxZoom: 20, minZoom: 1,
		attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
			'<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
			'Imagery © <a href="http://mapbox.com">Mapbox</a>',
		id: 'mapbox.streets'
	}).addTo(map);

cssIcon2 = L.divIcon({
                        className: 'Ico2',
                        iconAnchor: [0, 10],
                        iconSize: [24, 24]
                    });

// L.marker([48.61, 10.35828], {icon: cssIcon2}).addTo(map); 

var blackIcon = L.icon({
    iconUrl: 'images/dev-ko.svg',
    iconSize:     [40, 40], // size of the icon
    iconAnchor:   [10, 37], // point of the icon which will correspond to marker's location
    popupAnchor:  [-1, -15] // point from which the popup should open relative to the iconAnchor
});

/*
var markerClusters = L.markerClusterGroup({ chunkedLoading: true, 
spiderfyOnMaxZoom: true,
showCoverageOnHover: true,
zoomToBoundsOnClick: true,
});
*/
var markerClusters = L.markerClusterGroup({ chunkedLoading: true });
<?php
	for($i=0; $i<count($A_devices); $i++)
	{
 	 $m="";
	 $Mdevice= $A_devices[$i];
	 $xmlConfig=$C_devicesFolder.$A_devices[$i]."/".'config.xml';
	 $Mname=	getConfigdata2($xmlConfig, "name",$Mname);
	 $Mlat=		getConfigdata2($xmlConfig, "gpsN",$Mlat);
	 $Mlng=		getConfigdata2($xmlConfig, "gpsE",$Mlng); 
	 $Mtown=	getConfigdata2($xmlConfig, "Town",$Mtown);  	
	 $MlastM=	getConfigdata2($xmlConfig, "lastKwhMeasure",$MlastM); 
	 $MlastD=	getConfigdata2($xmlConfig, "lastDateMeasure",$MlastD); $MlastDD=date('d/m/Y  H:i:s', $MlastD);  
	 $Mtype=	getConfigdata2($xmlConfig, "type",$Mtype);
	 $Mcomp=	getConfigdata2($xmlConfig, "components",$Mcomp);
	 $payloadFrequency=getConfigdata2($xmlConfig, "frequency",$payloadFrequency);
	 $Mutc=		sprintf("%+d",getConfigdata2($xmlConfig, "UTC",$Mutc));
	 $Murl=		"<a href=\'eCounter.php?dev=".$Mdevice."\'>".$MlastM."</a> Kw";
	 $m.="\n";
	 $m.="var D".$Mdevice." = L.marker([".$Mlat.",".$Mlng."]\n";
	
	 //$interval=date("i",time()-$MlastD);
	 $interval=time()-$MlastD;
	 $maxInterval= 2* intval($payloadFrequency)*60;
//echo"<br> ". $Mdevice." --time=".time()."  MlastD=".$MlastD;
//echo"<br> interval=".$interval."  maxInterval=".$maxInterval."   intval(payloadFrequency)=".intval($payloadFrequency);
	 if($interval > $maxInterval) {$m.= ",{icon: blackIcon}"; }  
	 //echo"<br>========> interval=".$interval."   2x freq=".intval($payloadFrequency)*2;
	 //if($interval > (2* intval($payloadFrequency))) {$m.= ",{icon:  blackIcon}"; }

	 $m.= ",{ title: '".$MlastM."Kw'} ";
	 $m.=").addTo(map) \n";
     $m.="   .bindPopup('".$Mdevice."<br>".$Mtype."<br>".$Mname."<br>".$Mcomp."<br>frequency ".$payloadFrequency." mn<br>".$Mtown."<br>UTC ".$Mutc."<br>".$MlastDD."<br><br>".$Murl."'); \n"; 
     $m.="    markerClusters.addLayer( D".$Mdevice." ); \n"; 
	 echo $m;
	}
?>
	map.addLayer( markerClusters );
	
	var myLayer = L.geoJson().addTo(map);
	var popup = L.popup();
	function onMapClick(e) {
		popup
		    .setLatLng(e.latlng)
		    .setContent("You are at " + e.latlng.toString())
		    .openOn(map);
	}
	map.on('click', onMapClick);

    function pointMouseover(leafletEvent) {
        var layer = leafletEvent.target;
        layer.setStyle({
            weight: 2,
            color: '#666',
            fillColor: 'white'
        });
    }
</script>
<!-- ============================================================================================================ -->
<!--
<div id="sigfox1" style="width:100%; height:9%; background-color:#fff; color:rgb(35,0,104);box-shadow:  0 0 1em black; overflow: hidden;" >
	<img class='logo' src='http://www.sigfox.com/themes/sigfox/logo.svg' alt='Sigfox'  title='Sigfox' style="height: 36.0%; width: auto; margin: 0.4em 1em auto auto;" />
	   The Low Power Wide Area  Network  for the IoT 
</div>
 -->
<?
  //-- end of page
  $webPage->pageFoot($codeVersion, $codeRelease,1,FALSE);
//phpinfo();
  $webPage->pageEnd();
?>
