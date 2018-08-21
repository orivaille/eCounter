<?php 
//
//eCounter   receive Sigfox callback
//$codeRelease='1.4.0';
//olivier rivaille
//11NOV17  21MAR18  12JUN18
//orivaille@free.fr
//PHP Version 5.5.9
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
$prodVersion="";
//
//$ date +%s
//$ date --date='@2147483647'
//============================================================================================
//  webor.com.free.fr/eCounter/CallBackSigFox.php?dev=196540&time=1515847338&data=20000006700f5dfbe007068b&log
//
//  127.0.0.1/eCounter/CallBackSigFox.php?dev=999999&time=1521620675&data=2000001eb0046db2700901d4
//  127.0.0.1/eCounter/CallBackSigFox.php?dev=device&time=1521620675&data=2000001eb0046db2700901d4
//  127.0.0.1/eCounter/CallBackSigFox.php?dev=38FCCD&time=1527519041&data=2000033af0066dc4a01401df
//
//  127.0.0.1/eCounter/CallBackSigFox.php?dev=38FCCD&time=1528794359&data=200003e32c040dcd700800da
//============================================================================================
include ($prodVersion.'eCounter_classes.php');
include ($prodVersion.'eCounter_graphClasses.php');
include ($prodVersion.'eCounter_classes2.php');
include ($prodVersion.'manageDB.php'); 
if(isset($_REQUEST['L']))  { $Lg=$_REQUEST['L'];}  else {$Lg='en';}
include ('./LANGUAGES/'.$Lg.'.php');					// include language  file
//$_HIGH_VALUE=0xFFFF;
$C_devicesFolder="DEVICES/";						// Devices Folder ( 1 per device including logs & live svg graphics)		
//

$C_logname = $C_devicesFolder.$_GET['dev']."/".$_GET['dev'].'.log';
$C_log=FALSE;
//-- filter  incorrect DEVICE value received --- 21MAR18 OR ----
$incorrectDevice=FALSE;
if(strpos($_GET['dev'],"device") !== FALSE) {$incorrectDevice=TRUE;}
if(strpos($_GET['dev'],"{") !== FALSE) {$incorrectDevice=TRUE;}
if(strpos($_GET['dev'],"}") !== FALSE) {$incorrectDevice=TRUE;}
if($incorrectDevice) {
	$message="! ".$siteTitle." ! ERROR ".$_SERVER['PHP_SELF']." INCORRECT DEVICE RECIEVED ".$_GET['dev'];
	error_log($message, 0);
	die;
}
//
$URLupd=0;
if(isset($_GET['dev'])) { $URLupd++; $device=$_GET['dev'];}
if(isset($_GET['time'])) { $URLupd++;}
if(isset($_GET['data'])) { $URLupd++;}
if(isset($_GET['log'])) { $C_log=TRUE;$C_Log=$_GET['dev'].".log";} 	// set logging
//$C_maxMainDb = 9000;									// TEST  high water-mark for DB live records - override value in eCounter_classes.php
//$C_maxMainDb = 50;									// TEST  high water-mar for DB live records - override value in eCounter_classes.php
						
$mainDB="DB".$device.".db";
$tmpDB="DB".$device."tmp.db";
$filePath=$C_devicesFolder.$device."/"; 				// - read flat mainDB  file 
						
$C_DB="DB";   											///
$handle=$_HIGH_VALUE;
$ret=array();											// -- array   0  return code from DB function,  1 data
$webPage = new SitePage($siteTitle, $codeRelease, $cssFile, $jsFile, $C_eCounterLive, $metaPage,$AD_deviceFeatures,$AD_textElements);
$DB = new manageDB($device,$handle, $webPage, $filePath);
//
$mtime = microtime();
$mtime = explode(" ",$mtime);
$mtime = $mtime[1] + $mtime[0];
$starttime = $mtime;
//-----------------------------------------------
// local  geteCounterData  function :  read database and call graph builder for  live graphics
// - return :  last value 
function geteCounterData($webPage, $DB,$inputFilePath,$byteToProcess,$A_inputFields, $statusByteFieldsRange_A, &$Glabel, &$Glabels, &$Gdata, &$StartSequence, &$Gvalues,&$startDate)		
{
	$maxMeasures=49;				// *limited  measures   for building  live charts
	$inlines = '';
	//$measureInterval=0;
	$lastMeasure=0;
	$measureError=FALSE;			// measure interval error (too long,..)  will be used to change the color in graphic reporting
	$DBrecCnt=0;					// line counter ;  limit nb of records processed; used for test purpose 	
	$ret=$DB->openDB();
	if($ret[0]==TRUE) 	
		{ 
			$handle=$DB->handle;  
			//echo"<br/> - open 60 - ". $DB->mainDB." is open\n";
		}
	 else 	
		{ 
			echo"<br/><b>ERROR</b> - ". $DB->mainDB." is not open\n"; 
			die;
		}
	// --loop until maxMeasures --
	$cnt=1;
	while ($cnt<$maxMeasures)
		{
		 $ret=$DB->getNextRecord();
		 if($ret[0]==TRUE) 	
			{ 
				$line=$ret[1]; 
			   	//---- graphic work areas --------------------------------------------------------
			   	$items = explode(" ", $line);
			  	//---------------- get time field -------------------------------------------------
			   	$timeRecorded= ltrim($items[0],"\'");				// get record timestamp
			   	$dataTrimmed= $items[1];							// get Data payload
				$TYPErec=intval(substr($items[1],0,1));
  				// echo"<br/> -105- TYPErec=".$TYPErec."\n";
				if($TYPErec==2)
				 {
				   	$rcdDate=date("dMy-H:i:s", $timeRecorded); 
					if( $DBrecCnt == 0) 
						{ 
							$lastMeasure= $timeRecorded; 
							$lastDateMeasure = $timeRecorded;
							$lastData= $dataTrimmed;
						}
					 else 
						{
							$t=$timeRecorded-$lastMeasure; $lastMeasure= $timeRecorded;
						}  												//-<<<--  new ---  compute measure interval
					$rcdHour=date("H", $timeRecorded);
				   	//----------------get  data field -------------------------------------------------
				   	$A_wdt[]=$timeRecorded;								// --feed date array for  graphic label
					//-------------  process  byte demand  -------------------------------------
					$fieldValue = $webPage->byteExtract($dataTrimmed, $byteToProcess,$A_inputFields, $statusByteFieldsRange_A, $Glabel, $Glabels, $Gdata);
					//---build tables for graphic display  
			 		if(strlen($Gdata)>0) {$sepC=","; } else {$sepC="";}
					$Glabels.=$sepC."'". $timeRecorded."'";
					$Gdata.=$sepC." ".$fieldValue; 
					$Gvalues++;				
					$cnt++;
				 } // -- end of $ret[0]==TRUE
			} // -- end of  intval(substr($item[1],0,1)==2
		 else 	
			{ 	
				break;	
			}
		}
	$ret=$DB->closeDB();
	if($ret[0]==TRUE) 	
		{ 
			$line=$ret[1];  
		}
	else 	{ echo"<br/> M- close  - ". $DB->mainDB." is not closed\n"; }
	return $cnt;
} 			// end of function geteCounterData()
// 
//---------------------------------------------------------------------------------------------------
if(!file_exists($filePath))
	{
		// ----- NEW DEVICE NOT YET INITIALIZED -  CREATE DEVICE FILE STRUCTURE <============================== new ====
		// --- allow aibility  to receive payloads from Sigfox -------------------------------------------------
		//
		$startDate=time(); 					// set config <date> field
		$lkey=$device.$startDate;
		$Key= md5($lkey);															// set config <deviceKey> field
		//echo"  -  ". $startDate. "\n";
		// --- CREATE A NEW DEVICE ------------------------
		//
		$C_devicesFolder="DEVICES/";				// Devices Folder ( 1 per device including logs & svg gnerated)
		$C_templatesFolder="TEMPLATES/";			// Devices Folder ( 1 per device including logs & svg gnerated)
		$C_ownerFolder="../";
		//$device="111FFF";
		if(mkdir($C_devicesFolder.$device, 0777))	 	// create DEvice folder
		{
			$f=fopen($C_devicesFolder.$device."/".$device.".log", 'w'); fclose($f);				// create log file
			$f=fopen($C_devicesFolder.$device."/DB".$device.".db", 'w'); fclose($f);			// create database
			copy($C_templatesFolder."config.xml",$C_devicesFolder.$device."/config.xml");		// create empty config Xml
			copy($C_templatesFolder."g.svg",$C_devicesFolder.$device."/g1.svg");				// create empty svg livegraph
			copy($C_templatesFolder."g.svg",$C_devicesFolder.$device."/g3.svg");				// create empty svg livegraph
			echo"<br/> contexte created for ".$device."\n";
		}
		else {echo"<br/> <b>ERROR</b> cannot create folder ".$C_devicesFolder.$device." for ".$device."\n"; die;} 
} 
//----------------------------------------------------------------------------------------------------
//
 $webPage = new SitePage($siteTitle, $codeRelease, $cssFile,$jsFile, $C_eCounterLive,$metaPage,$AD_deviceFeatures,$AD_textElements);
if($URLupd > 2) { $logRecord= $_GET['time'] . "  " . $_GET['dev']. " " . $_GET['data'] . " OK\n";}
 else { $logRecord="ERROR-".$_SERVER["REQUEST_URI"]."\n";}
$oLog=fopen($C_logname, 'a') ;  								// open log          /* write after eof  */
//
if($oLog && $C_log)	{ fwrite($oLog, $logRecord); } 
//------------------------------------- 01AUG17  -------------------------
$xmlConfig=$C_devicesFolder.$_GET['dev']."/".'config.xml';
$Glabels =""; $Glabel="";    $Gdata= "";			// init variables for buteExtract call
//-- Get  Device Config parameters  --------------------------------------
getConfigdata( $xmlConfig, "dataItem",$AD_deviceFeatures) ; 
getConfigdata( $xmlConfig, "liveGraph",$AD_liveGraphs) ; 
getConfigdata( $xmlConfig, "transfoScript",$transfoScript) ;
getConfigdata( $xmlConfig, "lastKwhMeasure",$lastKwhMeasure) ;
getConfigdata( $xmlConfig, "lastDateMeasure",$lastDateMeasure) ;  
// 
//========================================================================
//------------ update DB  ------------------------------------------------
if($URLupd > 2) { 									//  some things are to be done 
 $mainDB=$C_devicesFolder.$_GET['dev']."/".$C_DB.$_GET['dev'].".db";
 $tempDB=$C_devicesFolder.$_GET['dev']."/".$C_DB.$_GET['dev']."tmp.db";
 $xmlConfig=$C_devicesFolder.$device."/".'config.xml';
 //
 //-- transform data frame if transformation script  set   in the config.xml file 
 //-- transform the $_GET['data'] field into  $xxxxx   variable .
 //if($transfoScript!="no")	{ }						// include DEVICE sinput transformation  php script ** unused in eCounter
 //---------------- get time field -------------------------------------------------
 $timeRecorded= $_GET['time'];					// get record timestamp
 $rcdDate=date("dMy-H:i:s", $timeRecorded); 
 $rcdHour=date("H", $timeRecorded);
 //----------------get  data field -------------------------------------------------
 $dataTrimmed= $_GET['data'];					// get  record data
 $TYPErec=intval(substr($dataTrimmed,0,1));
 //
 $ii=$io=0;
 for($i=0; $i<strlen($dataTrimmed)-1; $i++)
 {
	if($i%2 == 0)
	{
		$x=16*hexdec(substr($dataTrimmed,$i,1)) + hexdec(substr($dataTrimmed,$i+1,1));
		$A_IB[$io]=$x; 											// decimal value
		$A_IBs[$io]=substr($dataTrimmed,$i,1).substr($dataTrimmed,$i+1,1);
		$io++;
	} 
 }
 //if($transfoScript!="no")   									// transform  data in WEE format - unused in eCounter
//
//------  store last kWh measure in config.xml
//------  store last date  measure in config.xml
$lastKwhMeasure="n/a";  $byteTemp=FALSE;
$lastDateMeasure = $timeRecorded;
if($TYPErec==2) {
 //echo"<br/> -219- TYPErec=".$TYPErec."\n";
	//echo"<br> ---- AD_deviceFeatures ----";print_r($AD_deviceFeatures);echo"<br>";
	if($AD_deviceFeatures[1]==TRUE) {$byteTemp=1;}
	 else { if($AD_deviceFeatures[1]==TRUE) {$byteTemp=1;}}
	if($byteTemp)
	{
		$lastKwhMeasure=$webPage->byteExtract($dataTrimmed, $byteTemp,$A_inputFields, $statusByteFieldsRange_A, $Glabel, $Glabels, $Gdata); 
		if(!setConfigdata( $xmlConfig, "lastKwhMeasure",$lastKwhMeasure)) 
		{
			$logRecord= "ERROR -".$_SERVER["PHP_SELF"]. "setConfigData lastKwhMeasure did not work  \n";
			if($oLog && $C_log) 	{ fwrite($oLog, $logRecord);}
		}
		if(!setConfigdata( $xmlConfig, "lastDateMeasure",$lastDateMeasure)) 
		{
			$logRecord= "ERROR -".$_SERVER["PHP_SELF"]. "setConfigData lastDateMeasure did not work  \n";
			if($oLog && $C_log) 	{ fwrite($oLog, $logRecord);}
		}
	}
}
//---------------------------------------------------------------------
//--  store the incoming data into the database 
$recCount=1;
$ur=$timeRecorded." ".$dataTrimmed;			// write  callBack input data  to head of database
$ret=$DB->insertIntoDB($ur);
if($ret[0]==TRUE) 	
	{ 
		$line=$ret[1]; 
		//echo"<br/> - insert done \n";
	}
 else 	
	{ echo"<br/> insert  KO-\n";}
 //
 $inputFilePath=$C_devicesFolder.$_GET['dev']."/".$C_DB.$_GET['dev'].'.db'; 	// - read flat file coming from wget script
 // prepare Graphic workareas
  $inlines=FALSE;
  $handle=FALSE;
  $lthAnalysis=FALSE; $mAnalysis=FALSE;
  $A_wd=array();   $A_ws=array();  $A_wdt=array(); $a=array(); $A_lastMeasures=array();
//*************************************************************************
//------- read Database  --- flat file ------------------------------------ 
  $StartSequence=0; 
  $Gvalues = 0;
  $startDate=0;
  $StartRecord=$C_maxLines*$StartSequence; 				//  compute DB record  sequence 
//
	if($TYPErec==2) {
 	//echo"<br/> -265- TYPErec=".$TYPErec."\n";
	// -- generate graphics for graphics selected in config.xml
	  for($i=0; $i<count($AD_liveGraphs); $i++)
	  {
	   if($AD_liveGraphs[$i] == TRUE)						// graph to be built ?
	   { 
		$Glabels ="";
		$Gdata= ""; 
		$DBrecCnt=0;
		$Glabel= $A_inputFields[$i ];						// set graphic title
		$cnt=geteCounterData($webPage,$DB,$inputFilePath, $i, $A_inputFields, $statusByteFieldsRange_A, $Glabel, $Glabels, $Gdata, $StartSequence, $Gvalues,$startDate);
		//$webPage->SvgChart($_GET['dev'],'linearW2', 0, $i ,$Glabels,$Glabel,$Gdata,'', $A_unitFields,FALSE,$C_maxLines,$StartSequence, $startDate,$A_lastMeasures,$C_linearGraphMargin,'',           '',           '' ,          $eCounterImages_F,$eCounterImages_A, '') ;
		SvgChart($_GET['dev'],'linearW2',$webPage, 0, $i ,$Glabels,$Glabel,$Gdata,'', $A_unitFields,FALSE,$C_maxLines,$StartSequence, $startDate,$A_lastMeasures,$C_linearGraphMargin,'','','' ,$eCounterImages_F,$eCounterImages_A,'',$C_barSumInterval,$cnt) ;
		// 0 : will force default (= 100)  value for $C_linearSvgSizeY
		// $i  is the byteToProcess value  
		$logRecord= " update ".$i ." svg graphic  \n";
		if($oLog && $C_log) 	{ fwrite($oLog, $logRecord);}				// returns sign for temperatures ( bits 7, 6  Byte 1)
	   }  // -- end of  if($AD_liveGraphs[$i] == TRUE)
	  }  // -- end of for($i=0; $i<count($AD_liveGraphs);
	} // end of if($TYPErec==2)
} // end of if($URLupd > 2)
 fclose($oLog);	
//unset($xxx); 		
?>
