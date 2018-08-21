<?php 
//
$codeVersion=1;
$codeRelease='4.0';
//PHP Version 5.5.9
//ecounter  backend
//olivier rivaille
//11NOV17 - 12JUN18
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
$Lg='en';								// default language
if(isset($_GET['L']))  { $Lg=$_GET['L'];}
 include ('./LANGUAGES/'.$Lg.'.php');			// include language  file
//echo"<br/> 32 - AD_textElements[17]=".$AD_textElements[17];
//========================================================================
 include ('eCounter_classes.php');
include ('eCounter_graphClasses.php');
 include ('eCounter_classes2.php');
//========================================================================
//
 function getUser( $userDB, $field,&$Array)  {
 // echo "<hr/> -00-".$field." ----\n";
 $dom = new DomDocument();
 $dom->load($userDB);
 $liste = $dom->getElementsByTagName($field);
  foreach($liste as $item)
  {if(is_object($liste)) { $fv=$item->firstChild->nodeValue;}
  if(is_array($liste)) { $fv=$liste[0] ;}
  if(is_string($liste)) { $fv=$liste ;}
  //echo "<br/> -2-".$item->firstChild->nodeValue  ;
  if (is_array($Array)) {$Array[intval($fv)]=$fv; return TRUE;}
	else {$Array=$fv; return $fv;}	
  }
}
// start new page
$webPage = new SitePage($siteTitle, $codeRelease, $cssFile, $jsFile, $C_eCounterLive, $metaPage,$AD_deviceFeatures,$AD_textElements);
$C_devicesFolder="DEVICES/";			// Devices Folder ( 1 per device including logs & svg gnerated) <=====!!!!!!!!!!
$C_waitInterval = 60000;					// wait interval  milliseconds
if(isset($_GET['dev'])) 
{ 
//-- get device config data -  in $AD_arrays ------
  $device=$_GET['dev'];
  $xmlConfig=$C_devicesFolder.$_GET['dev']."/".'config.xml';
  getConfigdata( $xmlConfig, "dataItem",$AD_deviceFeatures) ; 
  getConfigdata( $xmlConfig, "liveGraph",$AD_liveGraphs) ; 
  //
  if(!$webPage->OK) 
  { error_log("ERROR - new SitePage(".$siteTitle.",".$codeRelease.",". $cssFile.")  not done", 0); }
  //
  //
  $webPage->PageHead(); 
 //echo "<br/> -68- StartSequence=".$StartSequence;
 $StartSequence=0;
  $webPage->PageBodyHeader($device,$homePage,$eCounterImages_A,$eCounterImages_F,$A_unitFields,$A_inputFields,$A_inputFieldsL,$AD_deviceFeatures,$AD_liveGraphs,$StartSequence,$A_lastMeasures,$Lg,$AD_textElements);
  $s="";
  //$s .="<div id='eCounterDevices'>\n";
  $s .="<div id='affich' style='width:100%;'>\n";
  $s .="	<div id='liveTitle'> ".$webPage->textTranslate(10,$webPage->textElements)." <b>".$device."</b>  </div>\n";
  //$s .="	<hr/>\n";
  echo $s;
  $grCnt=0;  // count Graph actives #
  $s='';
  for($i=0;$i<count($AD_liveGraphs); $i++)
  { 
	if($AD_liveGraphs[$i] == TRUE)
	{
	 $grCnt++;
     $eCounterImage=$eCounterImagesFolder.$eCounterImages_A[$i];
	 $s.="	<h2 class='eCounterInfo'>  ".$A_inputFields[$i]." <img src='".$eCounterImages_F.$eCounterImages_A[$i]."' class='imgCurrentL'  /> </h2>\n";
	 $s.="    <div id='dynGraph".$i."'> \n";
	 $s.="    </div> \n";
	 $s.="    <hr />\n";
	}
  }

  $s .="  <script> \n";
  $s.="    // Call the ajax refresh every ".($C_waitInterval/1000)." seconds \n";
  echo $s;
  $s="";
  for($i=0;$i<count($AD_liveGraphs); $i++)
  {
	if($AD_liveGraphs[$i] == TRUE)
	{ 
	 $s.="    refresh(".$i.",'".$device."')\n";
	 $s.="    setInterval(\"refresh(".$i.",'".$device."')\",  ".$C_waitInterval.")\n";
	}
  }
  $s .="  </script> \n";
  ($grCnt<4) ?  $grCnt=intval(4-$grCnt) : $grCNT=1 ;
  $s .="  <hr/> <div style='width:100%; height:".intval($grCnt*2)."em;'>  </div><br/>  \n";
  $s .=" </div>   <!-- end of id='wrapper' --> \n";
  echo $s;
}  // -- end of if(isset($_GET['dev']))
else
{
 echo "ERREUR - DEVICE NON FOURNI <br/><a href='".$homePage.".php'> retour</a>\n"; 
}  
  //-- end of page
  $webPage->pageFoot($codeVersion, $codeRelease,1,FALSE);
//phpinfo();
  $webPage->pageEnd();

