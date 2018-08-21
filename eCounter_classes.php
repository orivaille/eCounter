<?php
//
$codeVersion=1;
$codeRelease='4.0';
//PHP Version 5.5.9
//eCounter  backend
//olivier rivaille
//04NOV17 - 26FEB18 -12JUN18
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

https://www.gnu.org/licenses/licenses.html#GPL
https://www.gnu.org/licenses/lgpl.txt
-----------------------------------------------------*/
//--------------------------------------------------------
//- CONSTANTS 
//
$siteTitle="eCounter";
//============================================
// language values are included by caller  with include LANGUAGES/[ll].php file 
//============================================
$A_lastMeasures = array(); 						// last measure ; filled by Process  at start
$statusByteFieldsRange_A = array(0,1,2,3,4,5,6,7,8);  //  unsefull in eCounter
$eCounterImagesFolder="images/";
$eCounterImages_A = array("","counter1.svg","counter1.svg","onoff.svg","counter1.svg","counter1.svg","counter1.svg","hashtag.svg","counter1.svg");
$eCounterImages_F = "images/";
//===========================================
$C_maxLines= 96;							// maximum number of point from last measure  ( 24h x 15mn  > 96 points)
//$C_maxLines= 8;							// maximum number of point from last measure  ( 24h x 15mn  > 96 points) 
$C_histoMaxrec=31*24*4;						// max measure to search  for histocal view : 31j *  4 measures max  for one hour.  frequency 15mn
//$C_histoMaxrec=20;						// max measure to search  for histocal view : 31j *  4 measures max  for
$_HIGH_VALUE=0xFFFF;						// used to initiate handles ( CallBack & eCounter8)
$C_barSumInterval=-60;						// interval to sum value in minutes ( negative)			
//===========================================
$C_linearGraphMargin=35;					// 	left  graph margin  between left border and Y axis 
//
$C_devicesFolder="../DEVICES/";					// Devices Folder ( 1 per device including logs & svg gnerated)
$C_DB="DB";									// Prefix of Device Database
// css 
$C_cBoxes=0;								// boxes in the "current" div area  build by PageBody() 
$cssFile="eCounter";						// css file name
$jsFile="eCounterLive";						// javascript file name
$C_eCounterLive="eCounterLive";
$homePage = "eCounter";						// home page name
$metaPage = "index";						// home meta page name
$AD_deviceFeatures=array(FALSE,FALSE,FALSE,FALSE,FALSE,FALSE,FALSE,FALSE,FALSE,FALSE,FALSE,FALSE);	// meteo features of the device described in config.xml 
$AD_liveGraphs=array(FALSE,FALSE,FALSE,FALSE,FALSE,FALSE,FALSE,FALSE,FALSE,FALSE,FALSE,FALSE);		// live graphs selected fot the device  from config.xml
//
//============================================================================
class SitePage 
{
 function SitePage($siteName, $siteVer,  $Css, $jsFile, $C_eCounterLive, $metaPage,$AD_deviceFeatures,$AD_textElements) 
  { 
   	$this->webSiteName=$siteName; 
   	$this->webSiteVersion=$siteVer;
    //$this->user=$username;   
   	//$this->pswd=$password;     
   	$this->css=$Css; 
	$this->livePage=$C_eCounterLive.".php";
	$this->metaPage=$metaPage.".php";
	$this->deviceFeatures=$AD_deviceFeatures;
	$this->textElements=$AD_textElements;
   	$this->javascript=$jsFile .".js";   
    $this->OK=TRUE;    
 }
function textTranslate($item, $dictionnary) {
/*  returns item in the langage contained by dictionary.
 dictionary is imported by an include statement in the page  fepending on the laguage selected ( $Lg).
*/
return $dictionnary[$item];
}

//--   PageHead()   builds the html <head> sentences
//					it can generate  css & js links  for Leaflet openstreet maps  or  Google  maps  depending on scipt name 
function PageHead()
 {
	$s ="<!DOCTYPE html> \n";
 	$s .="<html>\n";
 	$s .="<head>\n";
 	$s .="<title>" . $this->webSiteName." ".$this->webSiteVersion. "</title>\n";
 	$s .="<meta  charset='utf-8' />\n";
 	$s .="<meta name='viewport' content='initial-scale=1.0'>\n";
 	$s .="<meta name='viewport' content='width=device-width'>\n";
  	$s .="<meta name='description' content='power counter'/>\n";
 	$s .="<meta name='keywords' content='electricity, counter, power' />\n";
 	$s .=" <meta http-equiv='Content-Language' content='en' />\n";
 	$s .=" <meta http-equiv='X-UA-Compatible' content='IE=edge'>\n";
 	$s .="<link rel='stylesheet' type='text/css' href='".$this->css.".css' />\n";
	//echo basename($_SERVER["PHP_SELF"]) ."<br/>" .$this->metaPage."<br/>";
	if(basename($_SERVER["PHP_SELF"]) == $this->livePage) 
		{$s .="<script type='text/javascript' src='".$this->javascript."'></script> \n" ;}
 	if(basename($_SERVER["PHP_SELF"]) == "eCounterG.php") 
		{$s .="<script src=\"https://maps.googleapis.com/maps/api/js?v=3.exp&key=AIzaSyAd-dRKRJHXSxD2MY8D3rdRkWvhyYVN8XU\"></script> \n";}
// 	if(basename($_SERVER["PHP_SELF"]) == "eCounterL.php")
	if(basename($_SERVER["PHP_SELF"]) == $this->metaPage) 
		{
		 $s .="<link rel='stylesheet' href='https://unpkg.com/leaflet@1.0.3/dist/leaflet.css' integrity='sha512-07I2e+7D8p6he1SIM+1twR5TIrhUQn9+I6yjqD53JQjFiMf8EtC93ty0/5vJTZGF8aAocvHYNEDJajGdNx1IsQ==' crossorigin='' />\n";
		 $s .="<script src='https://unpkg.com/leaflet@1.0.3/dist/leaflet-src.js' integrity='sha512-WXoSHqw/t26DszhdMhOXOkI7qCiv5QWXhH9R7CgvgZMHz1ImlkVQ3uNsiQKu5wwbbxtPzFXd1hK4tzno2VqhpA==' crossorigin=''></script>\n";
		 $s .="<link rel='stylesheet' type='text/css' href='https://leaflet.github.io/Leaflet.markercluster/dist/MarkerCluster.css' />\n";
		 $s .="<link rel='stylesheet' type='text/css' href='https://leaflet.github.io/Leaflet.markercluster/dist/MarkerCluster.Default.css' />\n";
		 $s .="<script type='text/javascript' src='https://leaflet.github.io/Leaflet.markercluster/dist/leaflet.markercluster-src.js' ></script>\n";
}	
	$s .="</head>\n";
 	echo $s;
 } 
//--  set links depending on features declared for the device in config.xml -dataItem.
//  setFeaturesLinks : if feature is TRUE, set  the item title  and link to graph builder and navigation
//
function  setFeaturesLinks($i,$ii,$Lg,$AD_deviceFeatures,$device,$homePage,$StartSequence,$A_lastMeasures,$A_unitFields,$A_inputFields,$A_inputFieldsL,$AD_textElements) { 
  if($i==3) { if($AD_deviceFeatures[$i]==TRUE) {$featureLink = "<a href='".$homePage.".php?F=".$ii."&S=".$StartSequence."&dev=".$device."&L=".$Lg."'>" ;}}
	else { $featureLink = $AD_deviceFeatures[$i]==TRUE?"<a href='".$homePage.".php?F=".$ii."&S=".$StartSequence."&dev=".$device."&L=".$Lg."'>".$A_inputFieldsL[$i]."<br/><b>".$A_lastMeasures[$i]."</b> ".$A_unitFields[$ii]."</a>" : $A_inputFieldsL[$i]." n/a";}
  //echo"<br/>-94- ". $i. " , ".$ii." ," .$A_inputFieldsL[$i] ." : ".$AD_deviceFeatures[$i]."\n";
   return $featureLink;
}
//--  gets the feature implementation ; disable  long term analysis (lth 31 days)  depending on features declared for the device in confog.xml -dataItem.
function getFeatures2($i,$AD_deviceFeatures) { 
   $featureLth = $AD_deviceFeatures[$i]==TRUE ? TRUE :FALSE;
  //echo "<br/> -117-  AD_deviceFeatures[".$i."]=".$AD_deviceFeatures[$i]."\n";
   return $featureLth;
}
function getOpacity($f,$AD_deviceFeatures) { 
	/*  full opacity if feature present; 30% opacity else;  */
	if($this->getFeatures2($f,$AD_deviceFeatures)==FALSE) {$o=0.3;} else {$o=1;}
	return $o;
}

function PageBodyHeader($device,$homePage,$eCounterImages_A,$eCounterImages_F,$A_unitFields,$A_inputFields,$A_inputFieldsL,$AD_deviceFeatures, $AD_liveGraphs,$StartSequence,$A_lastMeasures,$Lg,$AD_textElements) 
 { 
	$C_cBoxes=0;						// count ofnavigation boxes in the "current"  div area
	$imgOpacity=1;							// item icon default opacity
    $s  ="";
	$s .="<body>\n";
	$s .="<header id='header'>\n";
	$s .="  <div id='logoHeader'>\n"; 
	$s .="  	<a href='".$this->metaPage."'><img src='images/eradio2S.svg' id='eCounterLogo' alt='". $this->webSiteName."'/></a>\n";
	$s .="  </div>\n";
    if($this->getFeatures2(3,$AD_deviceFeatures))
		{
	//echo"<br/> -151- Lg, AD_textElements(16) =".$Lg. ",".$AD_textElements[16]."   --".count($A_lastMeasures)."-<br/>".print_r($A_lastMeasures);
		if(count($A_lastMeasures)>0) 	{
			$s .="   <div id ='hFeature3'>\n";
			if($A_lastMeasures[3]==1) {$wonoff="onoffG.svg";$alt=$AD_textElements[15];} else {$wonoff="onoffR.svg";$alt=$AD_textElements[16];}
			$daysBack=mktime(date("H"), date("i"), date("s"), date("n"), date("j")-1, date("Y"));
			if($A_lastMeasures[0] < $daysBack) {$wonoff="onoffO.svg";$alt=$AD_textElements[17];}
			$s .="	". $this->setFeaturesLinks(3,3,$Lg,$AD_deviceFeatures,$device,$homePage,$StartSequence,$A_lastMeasures,$A_unitFields,$A_inputFields,$A_inputFieldsL,$AD_textElements)."\n";
			$s .=" 	  <img src='".$eCounterImages_F.$wonoff."' class='imgCurrent'   style='margin: 1.110em 0 0 0;  opacity:". $this->getOpacity(3,$AD_deviceFeatures)."' alt='".$alt."' title='".$alt."'  /></a>\n";
			$s .="   </div>\n";
			$C_cBoxes++;
			unset($wonoff,$alt);
		 }
		} 
	$s .="  <div id='h_title'>\n";
	if($_SERVER["PHP_SELF"]==$this->metaPage."php") { $s .="	<a href='".$this->metaPage."'> <h2>".$this->webSiteName." </h2></a>\n";}
	 else {
			if($device!="") 
				{ $s .="	<a href='".$homePage.".php?dev=".$device."'> <h2>".$this->webSiteName." </h2></a>\n"; }
			else {$s .="	<a href='".$this->metaPage."'> <h2>".$this->webSiteName." </h2></a>\n";}
		}
	$s .="  </div>   <!-- end of  h_title -->\n";
	$s .="  <div id='Date'>\n";
//------- language --- 08JAN18 - OR -- 
		if($Lg!="") 
			{ 	$s .="     <form action='".$_SERVER["SCRIPT_NAME"]; 
				$s .="?"; $i=0;
				foreach ($_REQUEST as $v => $value) {if($v!="L") { if($i>0) { $s.="&";} $s.=$v."=".$value;$i++;}}
				$s .= "&L=".$Lg;
				$s .= "' method='post'>\n"; 
			}
		else {$s .="     <form action='".$this->metaPage.".php'  method='post'>\n";}
//
   		 $s .="		 <select  name='L'  id='Lang' class='Lg' placeholder=' choose language' onchange='this.form.submit();' > \n";
		 $s .="			  <option value='en'";	($Lg=="en")? $s.=" selected='selected'" : ""; $s .= "> English  </option> \n";
		 $s .="			  <option value='sq'";	($Lg=="sq")? $s.=" selected='selected'" : ""; $s .= "> Shqip    </option> \n";
		 $s .="			  <option value='fr'";	($Lg=="fr")? $s.=" selected='selected'" : ""; $s .= "> Français </option> \n";
		 $s .="       </select>\n";	
		 $s .="     </form>\n"; 
	$s .="     <b>".$device."</b><br/>   ".date('dMy-H:i')." \n";
//--------  langage
	$s .="  </div>\n";
//	$s .="  <div style='clear: both; float: none; height: 0;'></div>\n";
	$s .="</header>\n";
//	$s .="<div id='wrapper' style='height:100%;'>\n";
	$s .="<div id='wrapper'>\n";
	echo $s;
 }
function PageBody($device,&$C_cBoxes,$Lg,$homePage,$eCounterImages_A,$eCounterImages_F,$A_lastMeasures,$A_unitFields,$A_inputFields,$A_inputFieldsL,$StartSequence,$BackSequence,$NextSequence,$areaToProcess,$lthAnalysis,$AD_deviceFeatures, $AD_liveGraphs,$payloadFrequency,$AD_textElements) 
 { 
	//$C_cBoxes=0;						// count ofnavigation boxes in the "current"  div area
	$imgOpacity=1;							// item icon default opacity
	$s="";
	$s .=" <div id='current'>\n";
    if($this->getFeatures2(1,$AD_deviceFeatures) )
		{
			$s .="   <div id ='feature1'>\n";
		//	$s .=" 	Kwh\n";
			$s .=" 	  <img src='".$eCounterImages_F.$eCounterImages_A[1]."' class='imgCurrent'   style=' opacity:". $this->getOpacity(2,$AD_deviceFeatures).";' />\n";
			$s .="	  <p class='itemButton'>".$this->setFeaturesLinks(1,1,$Lg,$AD_deviceFeatures,$device,$homePage,$StartSequence,$A_lastMeasures,$A_unitFields,$A_inputFields,$A_inputFieldsL,$AD_textElements)."</p>\n";
			$s .="   </div>\n";
			$C_cBoxes++;
		}
    if($this->getFeatures2(2,$AD_deviceFeatures) )
		{
			$s .="   <div id ='feature2'>\n";
		//	$s .=" 	Kwh\n";
			$s .=" 	  <img src='".$eCounterImages_F.$eCounterImages_A[2]."' class='imgCurrent'   style=' opacity:". $this->getOpacity(2,$AD_deviceFeatures).";' />\n";
			$s .="	  <p class='itemButton'>".$this->setFeaturesLinks(2,2,$Lg,$AD_deviceFeatures,$device,$homePage,$StartSequence,$A_lastMeasures,$A_unitFields,$A_inputFields,$A_inputFieldsL,$AD_textElements)."</p>\n";
			$s .="   </div>\n";
			$C_cBoxes++;
		}
/*
    if($this->getFeatures2(3,$AD_deviceFeatures))
		{
			$s .="   <div id ='feature3'>\n";
		//	$s .=" 	O or 1 \n";
			$s .=" 	  <img src='".$eCounterImages_F.$eCounterImages_A[3]."' class='imgCurrent'   style=' opacity:". $this->getOpacity(3,$AD_deviceFeatures).";' />\n";
			$s .="	  <p class='itemButton'>".$this->setFeaturesLinks(3,3,$Lg,$AD_deviceFeatures,$device,$homePage,$StartSequence,$A_lastMeasures,$A_unitFields,$A_inputFields,$A_inputFieldsL,$AD_textElements)."</p>\n";
			$s .="   </div>\n";
			$C_cBoxes++;
		}
 */
    if($this->getFeatures2(4,$AD_deviceFeatures))
		{
			$s .="   <div id ='feature4'>\n";
		//	$s .=" 	Irms\n";
			$s .=" 	  <img src='".$eCounterImages_F.$eCounterImages_A[4]."' class='imgCurrent'   style=' opacity:". $this->getOpacity(4,$AD_deviceFeatures).";' />\n";
			$s .="	  <p class='itemButton'>".$this->setFeaturesLinks(4,4,$Lg,$AD_deviceFeatures,$device,$homePage,$StartSequence,$A_lastMeasures,$A_unitFields,$A_inputFields,$A_inputFieldsL,$AD_textElements)."</p>\n";
			$s .="   </div>\n";
			$C_cBoxes++;
		}
    if($this->getFeatures2(5,$AD_deviceFeatures) )
		{
			$s .="   <div id ='feature5'>\n";
		//	$s .=" 	PF\n";
			$s .=" 	  <img src='".$eCounterImages_F.$eCounterImages_A[5]."' class='imgCurrent'   style=' opacity:". $this->getOpacity(5,$AD_deviceFeatures).";' />\n";
			$s .="	  <p class='itemButton'>".$this->setFeaturesLinks(5,5,$Lg,$AD_deviceFeatures,$device,$homePage,$StartSequence,$A_lastMeasures,$A_unitFields,$A_inputFields,$A_inputFieldsL,$AD_textElements)."</p>\n";
			$s .="   </div>\n";
		}
    if($this->getFeatures2(6,$AD_deviceFeatures) )
		{
			$s .="   <div id ='feature6'>\n";
		//	$s .=" 	Kw\n";
			$s .=" 	  <img src='".$eCounterImages_F.$eCounterImages_A[6]."' class='imgCurrent'   style=' opacity:". $this->getOpacity(6,$AD_deviceFeatures).";' />\n";
			$s .="	  <p class='itemButton'>".$this->setFeaturesLinks(6,6,$Lg,$AD_deviceFeatures,$device,$homePage,$StartSequence,$A_lastMeasures,$A_unitFields,$A_inputFields,$A_inputFieldsL,$AD_textElements)."</p>\n";
			$s .="   </div>\n";
		}
    if($this->getFeatures2(7,$AD_deviceFeatures) )
		{
			$s .="   <div id ='feature7'>\n";
		//	$s .=" 	Kw\n";
			$s .=" 	  <img src='".$eCounterImages_F.$eCounterImages_A[7]."' class='imgCurrent'   style=' opacity:". $this->getOpacity(6,$AD_deviceFeatures).";' />\n";
			$s .="	  <p class='itemButton'>".$this->setFeaturesLinks(7,7,$Lg,$AD_deviceFeatures,$device,$homePage,$StartSequence,$A_lastMeasures,$A_unitFields,$A_inputFields,$A_inputFieldsL,$AD_textElements)."</p>\n";
			$s .="   </div>\n";
		}
    if($this->getFeatures2(8,$AD_deviceFeatures) )
		{
			$s .="   <div id ='feature8'>\n";
		//	$s .=" 	Kw\n";
			$s .=" 	  <img src='".$eCounterImages_F.$eCounterImages_A[8]."' class='imgCurrent'   style=' opacity:". $this->getOpacity(8,$AD_deviceFeatures).";' />\n";
			$s .="	  <p class='itemButton'>".$this->setFeaturesLinks(8,8,$Lg,$AD_deviceFeatures,$device,$homePage,$StartSequence,$A_lastMeasures,$A_unitFields,$A_inputFields,$A_inputFieldsL,$AD_textElements)."</p>\n";
			$s .="   </div>\n";
		}

    if($this->getFeatures2($areaToProcess,$AD_deviceFeatures))
	{
		 $currentHour = date('H');
		 $j= $A_lastMeasures[0] - $A_lastMeasures[count($A_lastMeasures)-1]; 
  		 $j=intval($j/86400);       //---- compute nb of days depending on measure interval value
	$j=31;
		 $s .="   <div id ='timeSelect' > \n";
		 $s .="     <form action='".$homePage.".php?";    //  -----  attention   autre script
		 if(isset($_REQUEST['S'])) {$s .= "S=".$_REQUEST['S'];}
		 if(isset($_REQUEST['F'])) {$s .= "&F=".$_REQUEST['F'];}
		 if(isset($_REQUEST['T'])) {$s .= "&T=".$_REQUEST['T'];}
		 $s .= "&M=M";
		 $s .="&dev=".$device;
		 $s .="&L=".$Lg;
		 $s .="' method='post'>\n";
		 $s .="	  <p class='itemButton'> \n";

   		 $s .="<input class='ltButton' type='submit' value=' ".$j." ".$this->textTranslate(2,$this->textElements)."' /> \n";
		 // $s .="       <input type='image' src='images/Glth.svg' class='img31j'/> \n";
		 $s .="      <select  name='T'  id='T' class='T'>\n";
		 for($i=0; $i<24; $i++) {
		  if($i == $currentHour)  { $s .="       <option selected> ".$i."</option>\n"; }   else { $s .="       <option> ".$i."</option>\n";}
		  }
		 $s .="       </select>h\n";
		 $s .="	  </p> \n" ;	
		 $s .="     </form>\n"; 

		 $s .="     <form action='".$homePage.".php?";    //  -----  attention   autre script
		 if(isset($_REQUEST['S'])) {$s .= "S=".$_REQUEST['S'];}
		 if(isset($_REQUEST['F'])) {$s .= "&F=".$_REQUEST['F'];}
		 $s .= "&M=M";
		 $s .="&dev=".$device;
		 $s .="&L=".$Lg;
		 $s .="' method='post'>\n";
		 $s .="	  <p class='itemButton'> \n";

   		 $s .="<input class='ltButton' type='submit' value=' ".$j." ".$this->textTranslate(2,$this->textElements)."' /> \n";   		 
		 $s .="	  </p> \n" ;	
		 $s .="     </form>\n"; 
		 $s .="   </div>\n";
 		$C_cBoxes++;
	} 		// end of if(getFeatures2($areaToProcess,$AD_deviceFeatures))
//
//		$s .=" 	Live Graphics\n";
		 $lgOK=FALSE;
		  for($i=0;$i<count($AD_liveGraphs); $i++)
		  { 
			if($AD_liveGraphs[$i] == TRUE)
			{
			 if($lgOK==FALSE) 
				{
					$lgOK=TRUE; $s .="   <div id ='Lgraph'>\n ";
					$s.="	  <a href='".$this->livePage."?dev=".$device."&L=".$Lg."' target='_blank'>";
				}
			 $s .=" <img src='".$eCounterImages_F.$eCounterImages_A[$i]."' class='imgCurrent' style='opacity:". $this->getOpacity($i,$AD_deviceFeatures).";' />";
			}
		  }
		$s.= "    </a>\n";
		$s.="	  <p class='itemButton'>".$this->textTranslate(10,$this->textElements)."<br/>";
		$s.="	   <em>".$this->textTranslate(3,$this->textElements)." : ".$payloadFrequency."mn</em>";
		$s.="	  </p>";
		$s .="   </div>\n";
		$C_cBoxes++;
	//-- informatif(isset($_REQUEST['L']))  { $Lg=$_REQUEST['L'];}  else {$Lg='en';}
 include ('./LANGUAGES/'.$Lg.'.php');			// include language  fileion and partners --------------------------------------------------------------

			$s .="   <div id ='filler'>\n";;
			//$s .=" 	  &nbsp;\n";
			$s .="   </div>\n";
			$C_cBoxes++; 

	$s .="</div>\n";
	$s .="<div id='affich'>\n";
	echo $s;  unset($s);
}
function pageFoot($codeVersion, $codeRelease,$c,$affich)
 {
    $s ="";
    if($affich) {$s  .=" </div>\n";}		  // end of avec div id='affich'  . to be managed by caller
    $s.=" <footer id='footer'> \n";
    //
	//-- information and partners -------------------------------------------------------------- 
	//$s .="   <div id ='powered' class='res'>\n";
	$s .="  <span > powered by  </span>\n";
	$s .=" 	<a href='http://www.sigfox.com' class='poweredLink' target='_blank'>  <img class='logo' src='http://www.sigfox.com/themes/sigfox/logo.svg' alt='Sigfox'  title='Sigfox'/></a>\n";
	$s .=" 	<a href='https://www.ismac-nc.net/wp/' class='poweredLink' target='_blank'>  <img class='logo' src='images/cropped-cropped-ismac-logo-e1497789160799-2s.png' alt='iSMAC'  title='iSMAC'/></a>\n";
    $s .= " <span style=' margin: 0 2em 0 8em;'>".$this->webSiteName." ".$codeVersion.".".$codeRelease." </span>\n";
    $s .=" </footer> \n";
    $s .="</div>    <!-- end of id='wrapper' --> \n";
    echo $s;
}
//---------- fin de page -----------------------------------------------------
function pageEnd()
 {
    $s  ="";
    echo $s; $s="";
	//include ('eCounter8.js');
    $s  .=" </body>\n</html>";   /// sans div id='affich'
    echo $s;	
 }

//---------- Extract Byte ----------------------------------------------------
function hexaStrToHexa($strH, $_L)
 {
// $strH 	: string hexadecimal to be transformed into decimal number
// $_L		: length of $strH given by caller 
	$decValue=0;
	for($i=0; $i<$_L; $i++)
		{
			$decValue+=pow(16,$i)*hexdec(substr($strH,(($_L-1)-$i),1));
		}
	$Hvalue= base_convert( $decValue, 10, 16);
	return $decValue;
 }
function byteExtract($dataTrimmed, $areaToProcess, $A_inputFields, $statusByteFieldsRange_A, $Glabel, $Glabels, $Gdata)
// returns decimal value of the selected byte $areaToProcess
{
 $areaProcessStatus=FALSE;
 if(is_int($areaToProcess)  ) 
 {   
	// check if byte can be processed 
   /* 
	INPUT FIELDS :
	$dataTrimmed : trimmed "data" fiels from message record. provided by caller.
	$areaToProcess : position of byte to process (extract)  regarding byte map below: 

	All figures are Interger Values  scale 100 ( divide by 100 to get the mesaured value).

 	- 1 : n/a 	TYPE record   	Byte 1-1  -  bit 7-4 -- extracted automatically   Value = 2
 	- 2 : ok	Kwh  			Byte 1    -  bit 4-0 
								Byte 2	  -  bit 7-0  
								Byte 3	  -  bit 7-0  
								Byte 4	  -  bit 7-0  
 	- 3 : ok	Vrms  			Byte 5    -  bit 6-0	bit 7  set to zero  by AND op 
								Byte 6	  -  bit 7-0  
 	- 4 : ok	Irms  			Byte 7    -  bit 5-0	bit 7 & 6  set to zero  by AND op 
								Byte 8	  -  bit 7-0  
 	- 5 : ok	PF  			Byte 9    -  bit 6-0	bit 1  set to zero  by AND op 
 	- 6 : ok	Kw  			Byte 10   -  bit 3-0	bit 7,6,5,4  set to zero  by AND op 
								Byte 11	  -  bit 7-0  
 	- 7: n/a	unused 			Byte 12   -  bit 7-0	
	*/
//
//------------- get  data field status byte ------------------------------------
//

	$TYPErec=intval(substr($dataTrimmed,0,1));					// left byte ( i.e.  1 caracter) from data field <==
	$returnValue=FALSE;
	$hexValue=0X0000;
 	if($TYPErec==2) 											// is TYPE == 2 ?
	{
//------------------------------------------------------------------------
//--              process bytes                                         --
//------------------------------------------------------------------------
	/*      */
 		switch ($areaToProcess) {
		case '0':						// Uplink type (=2) 
			$returnValue= (int)substr($dataTrimmed,0,1);
        	break;
    	case '1':						// Active Energy kWh Int,dec
			//echo"<br/>-------- Kwh Int ----------- \n";
			$hexValue=substr($dataTrimmed,1,7);
			$Hvalue= $this->hexaStrToHexa($hexValue, 7);
			//echo"<br/>---------------------------- \n";
			$int=$Hvalue >> 1; 		// shift right  

			//echo"<br/>-------- Kwh Dec ----------- \n";
			$hexValue=substr($dataTrimmed,7,3);
			$HV1= "000". substr(sprintf("%04b",hexdec(substr($dataTrimmed,7,1))), 3,1) ;		
			$HV2=sprintf("%04b",hexdec(substr($dataTrimmed,8,1)));
			$HV3=sprintf("%04b",hexdec(substr($dataTrimmed,9,1)));
			$HVn=substr($HV1.$HV2.$HV3, 0, 10);
			$dec=bindec($HVn);
			if($dec<10){$dec='0'.$dec;}
			$returnValue= $int.'.'.$dec;
        	break;

    	case '2':
			//echo"<br/>-------- Amp  rms ----------- \n";
			$HV1= "00". substr(sprintf("%04b",hexdec(substr($dataTrimmed,9,1))), 2,2) ;		
			$HV2=sprintf("%04b",hexdec(substr($dataTrimmed,10,1)));
			$HV3=sprintf("%04b",hexdec(substr($dataTrimmed,11,1)));
			$HV4=sprintf("%04b",hexdec(substr($dataTrimmed,12,1)));
			$HVn=substr($HV1.$HV2.$HV3.$HV4, 0, 16);
			$dec=bindec($HVn) / 100;
			$returnValue=$dec;
        	break;
    	case '3':
			//echo"<br/>-------- Relay Status ----------- \n";
			$HV1= "000".substr(sprintf("%04b",hexdec(substr($dataTrimmed,13,1))), 0,1) ;		
			$dec=bindec($HV1);
			$returnValue=$dec;
        	break;
    	case '4':
			//echo"<br/>-------- Volt rms ----------- \n";
			$HV1= "0". substr(sprintf("%04b",hexdec(substr($dataTrimmed,13,1))), 1,3) ;		
			$HV2=sprintf("%04b",hexdec(substr($dataTrimmed,14,1)));
			$HV3=sprintf("%04b",hexdec(substr($dataTrimmed,15,1)));
			$HV4=sprintf("%04b",hexdec(substr($dataTrimmed,16,1)));
			$HVn=substr($HV1.$HV2.$HV3.$HV4, 0, 16);
			$dec=bindec($HVn) / 100;
			$returnValue=$dec;
        	break;

    	case '5':
			//echo"<br/>-------- Active Power Kw ----------- \n";
			$hexValue=substr($dataTrimmed,17,3);
			$Hvalue= $this->hexaStrToHexa($hexValue, 3);
			$dec=$Hvalue / 100;
			$returnValue=$dec;
        	break;

    	case '6':
			//echo"<br/>-------- Reactive Power kVar ----------- \n";
			$hexValue=substr($dataTrimmed,20,2);
			$Hvalue= $this->hexaStrToHexa($hexValue, 2);
			$dec=$Hvalue / 10;
			if(substr(sprintf("%04b",hexdec(substr($dataTrimmed,22,1))), 0,1)  == "1"){$dec = -$dec;}
			$returnValue=$dec;
        	break;
    	case '7':
			//echo"<br/>-------- Power Factor ----------- \n";
			$HV1= "0". substr(sprintf("%04b",hexdec(substr($dataTrimmed,22,1))), 1,3) ;		
			$HV2=sprintf("%04b",hexdec(substr($dataTrimmed,23,1)));
			$HVn=substr($HV1.$HV2, 0, 8);
			$dec=bindec($HVn) / 100;
			$returnValue=$dec;
        	break;

    	case '8':
			//echo"<br/>-------- Apparent Power  active power +abs(reactive power)----------- \n";
			$hexValue=substr($dataTrimmed,17,3);
			$Hvalue= $this->hexaStrToHexa($hexValue, 3);
			$dec=$Hvalue / 100;

			$hexValue=substr($dataTrimmed,20,2);
			$Hvalue= $this->hexaStrToHexa($hexValue, 2);
			$dec=$dec+($Hvalue / 10);

			$returnValue= $dec;
        	break;

		default:
			$returnValue=$dec;
		}   // -- end of switch
    	$areaProcessStatus=TRUE;
 	}  // -- end of statusbit !=0
  return $returnValue;    // TBD
  }
  else
  {}
 }
//------  -------------------------------------------------------

// ---validte timestamp string ----------------------------------
function is_timestamp($timestamp)
{
	$check = (is_int($timestamp) OR is_float($timestamp))
		? $timestamp
		: (string) (int) $timestamp;
	return  ($check === $timestamp)
        	AND ( (int) $timestamp <=  PHP_INT_MAX)
        	AND ( (int) $timestamp >= ~PHP_INT_MAX);
}
// infoTextMin()  compute the coordinate of graphic infotext 
function infoTextMin($chartType,$infoTextSize,$chartSvgSizeX,$linearGraphMargin,&$infoTextMinY,&$infoTextMaxX,&$infoTextMaxY)   {
 ($chartType=='linear' || $chartType=='linearLT' || $chartType=='bar')? $infoTextMaxX=$linearGraphMargin+3*$chartSvgSizeX/4 : $infoTextMaxX=4.1*$chartSvgSizeX/4 ;
 ($chartType=='linear' || $chartType=='linearLT' || $chartType=='bar')? $infoTextMaxY=-54 : $infoTextMaxY=54 ;
 $infoTextMinY=$infoTextMaxY-$infoTextSize*1.24;
 return ;
}
//
//
//-------------------------------------------    -----------------------------------------
	function noData($Glabel,$areaToProcess,$device,$C_devicesFolder ) 
	 { 
	    $xmlConfig=$C_devicesFolder.$device."/".'config.xml';
	    $Mname =	getConfigdata2($xmlConfig, "name",$Mname);
	    $intHello=$device."<br />".$this->textTranslate(13,$this->textElements)."<br/>".$Mname;
		($areaToProcess==0)? $t=$intHello : $t=" ".$this->textTranslate(14,$this->textElements)." ". $Glabel;

		$s  ="";
		$s .="  <div id='nodata'> \n";  
		$s .="    	<h2 class='msgInfo'> ".$t." </h2> \n"; 
		$s .="  </div> <!-- end of nodata -->\n"; 
		echo $s; unset($s, $t);
	 }
	function openSvgGraph($C_svgFileName)  {
	  $svgf =fopen($C_svgFileName, "w");
	  if(!$svgf) {echo "<br/> ERROR - cannot open $C_svgFileName file \n";} 
	  return $svgf;
	}  
	function writeSvgGraph($file,$record)  {
	  fwrite($file,$record);
	}
	function closeSvgGraph($file)  {
	  if($file) {fclose($file);}
	}
} // fin class
?>
