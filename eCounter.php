<?php 
//
//$codeVersion=1;
//$codeRelease='4.0'
//PHP Version 5.5.9
//eCounter backend
//olivier rivaille
//04NOV17  12JUN18
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
//========================================================================
 include ('eCounter_classes.php');
 include ('eCounter_graphClasses.php');  
 include ('eCounter_classes2.php');  
 include ('manageDB.php'); 
//========================================================================
//
$C_devicesFolder="DEVICES/";									// Devices Folder ( 1 per device including logs & svg gnerated) <<<<<<<<<<<<<<<<<<<<<<<<
$C_DB="DB";   													////////--- a revoir sortir des parametres de SvgChart()  --> function dédiée à écrire
$handle=$_HIGH_VALUE;
if(isset($_GET['dev'])) {$device=$_GET['dev'];} else {header("location:http://".$_SERVER["SERVER_NAME"]."/"."eCounter/");}
$filePath=$C_devicesFolder.$device."/"; 						// - read flat mainDB  file 
$xmlConfig=$C_devicesFolder.$device."/".'config.xml';
$csvFilePrefix=$siteTitle."_".$device."_";
//
if (file_exists($filePath."/tmp"))   			//house keeping; delete  csv files in DEVICE/tmp
{
	$handle=opendir($filePath."/tmp");
	while (false !== ($f = readdir($handle))) {
		if (($f != ".") && ($f != "..")) {
			$uf=$filePath."tmp/".$f;
			unlink($uf);
			//echo "<br/> -45- ".$filePath."tmp/".$f ." deleted \n";
		}
	}
}
//
if(isset($_REQUEST['L']))  { $Lg=$_REQUEST['L'];} 
 else {
	$wlg=getConfigdata2($xmlConfig, "Language",$wlg);
	if($wlg) {
			if(phpversion()>6) {$Lg=strtolower($wlg);}
			 else {$Lg=mb_strtolower($wlg);}
		 } 
	else {$Lg='en';}	
 }
 include ('./LANGUAGES/'.$Lg.'.php');							// include language  file
//
if(isset($_GET['dev'])) 
{ 
//-- get device config data
//
  getConfigdata( $xmlConfig, "dataItem",$AD_deviceFeatures) ; 
  getConfigdata( $xmlConfig, "liveGraph",$AD_liveGraphs) ;
  $payloadFrequency=getConfigdata2($xmlConfig, "frequency",$payloadFrequency);  // frequency in minutes
  if($payloadFrequency==0) {$payloadFrequency="n/a";}
  //-- re-compute  data scope regarding the device frequency

  ($payloadFrequency=="n/a")? $payloadFrequency=15 : $payloadFrequency=$payloadFrequency ;
  $C_maxLines= intval(24* 60/$payloadFrequency);				// maximum number of point from last measure  ( 24h x 15mn  > 96 points)
//$C_maxLines= 40;				// <========== maximum number of point from last measure  ( 24h x 15mn  > 96 points)
  $C_histoMaxrec=intval(31*24*60/$payloadFrequency);			// max measure to search  for histocal view : 31j *  4 measures max  for one hour.  freque
//
 if(isset($_REQUEST['D']))	{
 //echo"<br/>_GET['D']=".$_REQUEST['D'];
	$C_barSumInterval=$_REQUEST['D'];
	if ($_REQUEST['D']==-1440) {$C_maxLines=$C_maxLines*24;} //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
 }
}  // -- end of if(isset($_GET['dev']))  
//
$StartSequence = 0;												// counter  used for backward forward navigation in the data
$NextSequence=$StartSequence+$C_maxLines;
$BackSequence=0;
//
$Gvalues = 0;													// # of DB values selected for graphic representation
$areaToProcess = 0;												// # of information byte to be displayed
// start new page
$webPage = new SitePage($siteTitle, $codeRelease, $cssFile, $jsFile, $C_eCounterLive, $metaPage,$AD_deviceFeatures,$AD_textElements);
$DB = new manageDB($device,$handle, $webPage, $filePath);
if(!$webPage->OK) 
 { error_log("ERROR - new SitePage(".$siteTitle.",".$codeRelease.",". $cssFile.")  not done", 0);}
//
//***********************************************************************
 $inputFilePath=$C_devicesFolder.$device."/".$C_DB.$device.'.db'; 	// - read device database  - flat file
//echo "<br/>>>1>>inputFilePath =".$inputFilePath  ."\n";
//***********************************************************************
// prepare Graphic workareas
 $Glabels ="";
 $GtsRadar ="";									// wind process - UNUSED in eCounter
 $Glabel= $A_inputFields[$areaToProcess ];					// set graphic title
 //echo "<br/> *** echo Glabel=".$Glabel."\n";
 $Gdata= ""; 
 //
 // prepare input data workareas
 $handle=FALSE;				
 $timeAnalysis=FALSE; 
 $longPeriodAnalysis=FALSE;
 //$A_wd=array();   $A_ws=array();  
 $A_wdt=array(); $a=array();
//
// -- analyse input requests ------------
 if(isset($_REQUEST['T'])) 	{ $timeAnalysis=$_REQUEST['T']; }		//-- hour selected for lth
 if(isset($_REQUEST['M'])) 	{ $longPeriodAnalysis=TRUE;  }	//-- lth selected
// navigation values computation  | S is the sequence of records sets ; 
//  start at O; 1 is previous sequence backwrd etc...
 if(isset($_GET['S'])) 	{ $StartSequence=$_GET['S'];}  else { $StartSequence=0; }  
 $BackSequence=$StartSequence+1; 				// backward sequence ( to the right)
 if( $StartSequence != 0)  { $NextSequence=$StartSequence-1;} else {$NextSequence=0;}
 if($timeAnalysis  || $longPeriodAnalysis )
 {
  // long period  scope limits
// echo "<br/> ***  C_histoMaxrec=".$C_histoMaxrec."\n";
  $StartRecord=$C_histoMaxrec*$StartSequence; 			//  compute DB record  sequence 
  $BackRecord=$C_histoMaxrec*$BackSequence; 			//  compute DB record  sequence 
  $NextRecord=$C_histoMaxrec*$NextSequence; 			//  compute DB record  sequence 
 }
 else
 { 
  // normal/short period scope limits
  $StartRecord=$C_maxLines*$StartSequence; 				//  compute DB record  sequence 
  $BackRecord=$C_maxLines*$BackSequence; 				//  compute DB record  sequence 
  $NextRecord=$C_maxLines*$NextSequence; 				//  compute DB record  sequence 
 //echo"<br/>C_maxLines=".$C_maxLines. "   StartRecord=".$StartRecord."  ,  BackRecord=". $BackRecord ."\n";
 }
 // extract values from eCounter database
 //echo"<br/> StartRecord=".$StartRecord."  ,  BackRecord=". $BackRecord ."\n";
 $ret=$DB->openDB();
 if($ret[0]!=TRUE) 	{ echo"<br/><b>ERROR</b> - ". $DB->mainDB." is not open\n"; die;}
  else	
 { 
  $handle=$DB->handle;  
  //echo"<br/> - open 97 - ". $DB->mainDB." is open\n";
  $lastLine=FALSE;
  $DBrecCnt=0;	$cnt=0;					// line counter ;  limit nb of records processed; used for test purpose 
  //$iIn=0;
  $wprevY=0;
  $iOut=0; 			// to number $A_XGraphCoord[] 
  $YdeltaSum=0;
  $sumDeltaTime=0;

  if (isset($_GET['F'])) 	{$areaToProcess = intval($_GET['F']);  $Glabel= $A_inputFields[$areaToProcess ];} // F = data byte to process
  if ((0 <= $areaToProcess) and ( $areaToProcess < 9)) 
  {
    if($timeAnalysis  || $longPeriodAnalysis ) {$frontier=$C_histoMaxrec;} else {$frontier=$C_maxLines;}
    while ($cnt<$frontier)
	{
	 //echo"<br/> 137 -1- cnt=".$cnt."   frontier=".$frontier;
	 //echo"<br/>  C_maxLines=".$C_maxLines."   cnt=".	$cnt."\n";
	// $iIn=0;		// used in  farea to process = 1 
	 $ret=$DB->getNextRecord(); 
	 //echo"<br> frontier=".$frontier." / cnt=".$cnt." / ";print_r($ret);
	 if($ret[0]==TRUE  &&   $ret[1]!="EOF") 	
		{ 
		 $line=$ret[1];
		 $lastLine =$line;
		 //echo"<br/>  StartRecord=".$StartRecord."   DBrecCnt =".	$DBrecCnt. "  BackRecord=".$BackRecord."\n";
		  if($DBrecCnt >=$StartRecord && $DBrecCnt<=$BackRecord)  // record in scope ?
		  {
		    // echo "<br/>DBrecCnt=".$DBrecCnt." ,StartRecord=". $StartRecord. ", BackRecord=".$BackRecord."\n";
		   	//------------------------------------------ process -----------------------------
		   	//---- graphic work areas --------------------------------------------------------
		   	$items = explode(" ", $line);
		  	//---------------- get time field -------------------------------------------------
		   	$timeRecorded= ltrim($items[0],"\'");				// get record timestamp
		   	$rcdDate=date("dMy-H:i:s", $timeRecorded); 
			$rcdHour=date("H", $timeRecorded);
		   	//----------------get  data field -------------------------------------------------
		   	$dataTrimmed= $items[1];
		   	$A_wdt[]=$timeRecorded;								// --feed date array for  graphic label
			 //----------------get  last (current) data values ------------------------------------
			$A_lastMeasures[]=$timeRecorded;  // save date of last measure in table for  front page. date is $A_lastMeasures[0]
			if((int)substr($dataTrimmed,0,1) == 2)			// frame type = 2  specific eCounter---
			{
				$fieldValue= $webPage->byteExtract($dataTrimmed, $areaToProcess, $A_inputFields, $statusByteFieldsRange_A, $Glabel, $Glabels, $Gdata);
//
//$$$$$$$$ eCounter $$$ nn.04  before  nn.7n  or nn.8n  kW  BUG turnaround $$$$$$$$$$$ 8MAR18 $$$$$$$$$$$$$$$$
				if($areaToProcess==1) {
					$wdfv=explode(".", $fieldValue); 
					if($wdfv[1]=='04') { if($wpdfv >='60') {$fieldValue=$wdfv[0].'.64';}}
					$wpdfv=	$wdfv[1]; }	
//$$$$$$$$ eCounter $$$  BUG turnaround $$$$$$$$$$$$$$$$$$$$$$$$$$$	
		 		if(strlen($Gdata)>0) {$sepC=","; } else {$sepC="";}
				if($areaToProcess==1) {
					//echo"<br/> -188 -  areaToProcess==1  fieldValue".$fieldValue." /  timeRecorded".$timeRecorded." /  iIn".$iIn;
				$infoTextSize=10.50;
				//echo"<br/> 203 C_barSumInterval=".$C_barSumInterval;
				$deltaTime=$C_barSumInterval*60; 							// millisec interval * 60 sec per minute
				//echo"<br/> -198 -  wprevY=".$wprevY." / fieldValue".$fieldValue." / delta=".round($wprevY-$fieldValue,3);
				if($wprevY==0) {
						$wPrevTime=$timeRecorded;
						$wStartX=$timeRecorded;
						$wStopX=$wStartX+$deltaTime;
						$wprevY=$fieldValue; $wprevY=$fieldValue; 
				   } else {
					//echo"<br/> -211 - ".$cnt." timeRecorded=".$timeRecorded." /  wStopX=".$wStopX." /  YdeltaSum=".$YdeltaSum;
					//echo"<br/> -212 - ".$cnt." wPrevTime=".$wPrevTime;						
						$sumDeltaTime=$sumDeltaTime +($timeRecorded-$wPrevTime); $wPrevTime=$timeRecorded;
						if($timeRecorded >= $wStopX) { 
						//echo"<br/> -212 -  wprevY=".$wprevY." / fieldValue".$fieldValue." / delta=".($wprevY-$fieldValue);
							 $YdeltaSum = $YdeltaSum +$wprevY-$fieldValue;
							 $wprevY=$fieldValue;
							} else  {
									  if( ($rcdHour==$timeAnalysis && $longPeriodAnalysis )  || $timeAnalysis == FALSE  )  
										{
								 		 $Glabels.= $sepC."'". $wStopX."'";
								 		 $Gdata.= $sepC." ".round($YdeltaSum,4);
						//echo"<br/> -220 -  YdeltaSum=".$YdeltaSum." / iOut=".$iOut."\n";
										 $iOut++;
										 $YdeltaSum=0;
										 $wprevY=$fieldValue; 		
										 $wStopX=$timeRecorded+$deltaTime;	// restore next interval limit
										}
							}
					   }
				  }  // end of $areaToProcess==1
		else 	// areaToProcess not = 1
				{
					  if( ($rcdHour==$timeAnalysis && $longPeriodAnalysis )  || $timeAnalysis == FALSE  )  
						{
					 		$Glabels.=$sepC."'". $timeRecorded."'";
					 		$Gdata.=$sepC." ".$fieldValue;
					 	}
				}
			// initiate last measures with first record values (left boxes in page)
			if($Gvalues==0) {        
	   	  		for($i=1; $i<9; $i++)  {
		  			$A_lastMeasures[$i]=$webPage->byteExtract($dataTrimmed, $i, $A_inputFields, $statusByteFieldsRange_A, $Glabel, $Glabels, $Gdata);	
				   	$startDate=$timeRecorded;
				   	//echo "<br/>  eCounter -152- A_lastMeasures[".$i."]=".$A_lastMeasures[$i]."\n";
				}
				//var_dump($A_lastMeasures);
			 } // end of  if($Gvalues==0)
			 if($areaToProcess>0 ) {$Gvalues++;}
			 $cnt++;
			}
		  }									 					// -- end of if($DBrecCnt >=$StartRecord && $DBrecCnt<=$BackRecord)
		  $DBrecCnt++;
		  }														// end of  	>getNextRecord(); if($ret[1]!="EOF") 
		 // ret[1]!="EOF" 	
		 if($ret[1]=="EOF") {$cnt=$frontier;} 					// stop looping
		} // -- end of	while ($cnt<$frontier)
		$A_lastMeasures[]= $timeRecorded; 		 				// -- SAVE STARTING DATE at the end of $A_lastMeasures  <=== NEW 12AUG17
		//echo"<br/>DBrecCnt =".	$DBrecCnt. "  BackRecord=".$BackRecord."\n";
	
      if($DBrecCnt <  $BackRecord) 								// -- no database records selected
			{
			  if($lastLine) {		
				$items = explode(" ", $lastLine);
			  	//---------------- get time field -------------------------------------------------
			   	$timeRecorded= ltrim($items[0],"\'");			// get record timestamp
			   	$rcdDate=date("dMy-H:i:s", $timeRecorded); 
			 	 //echo " pas de données  antérieures  à ".$rcdDate ."\n";
				 $BackSequence=0;  									// no backward possible					
				}							
			}
	  unset($line, $lastLine,$dataTrimmed);
  }   					//- end of if ( (0 <= $areaToProcess) and ( $areaToProcess < 10) ) 
 } 
 $DB->closeDB();
//=========================================
/*
echo"<br/> --276 -- sumDeltaTime=".$sumDeltaTime." / cnt=".$cnt."\n";
$ww=round($sumDeltaTime/$cnt,0);
echo"<br/> --278 -- ww=".$ww." / ".date(" H:i:s",$ww)." / ".date("dMy- H:i:s",$ww)."\n";
*/
//
function csvOutput($X,$Title,$Y,$csvSeparator,$filePath,$csvFilePrefix) {
// create csv file for data displayed in svg
/*
  create file :  in  DEVICES / TMP : eCounter_DEVICE_date_time_Labels.csv
  create link in page  under graph  for downloading the file.
*/
 $csvFile=$filePath."tmp/".$csvFilePrefix.time()."_".str_replace(" ","",$Title).".csv" ;
 $csvLink=FALSE;
 $handle=fopen($csvFile, 'w');
 if($handle)
 {
	$or="timestamp".$csvSeparator.$Title."\n";
	fwrite($handle, $or);
	for($i=0; $i<count($X); $i++) {
		fwrite($handle, $X[$i].$csvSeparator.$Y[$i]."\n");
	}
	fclose($handle);
 	$csvLink="<div id='csvLink'><a href='".$csvFile."' > download ".$csvSeparator." CSV </a> ".$i." lines</div>  \n";
 } 
return $csvLink ;
}
//--------------------------  build  page  -----------------------------------------------------------------------------
// 
 $webPage->PageHead(); 
 $eCounterImage=$eCounterImagesFolder.$eCounterImages_A[$areaToProcess];
 $webPage->PageBodyHeader($device,$homePage,$eCounterImages_A,$eCounterImages_F,$A_unitFields,$A_inputFields,$A_inputFieldsL,$AD_deviceFeatures, $AD_liveGraphs,$StartSequence,$A_lastMeasures,$Lg,$AD_textElements);
 $webPage->PageBody($device,$C_cBoxes,$Lg,$homePage,$eCounterImages_A,$eCounterImages_F,$A_lastMeasures,$A_unitFields,$A_inputFields,$A_inputFieldsL,$StartSequence,$BackSequence,$NextSequence,$areaToProcess,$timeAnalysis,$AD_deviceFeatures, $AD_liveGraphs,$payloadFrequency,$AD_textElements);
//-- create graphic ----
//echo"<br/> eCounter.php 254 - Gvalues count=".$Gvalues;
 $grCnt=1;																	// count for blank filler occurences  in page bottom
 if($Gvalues == 0)  {$webPage->noData($Glabel,$areaToProcess,$device,$C_devicesFolder); $grCnt=10;}  // no graphic to display ?
  else 
 { 
//echo"<br/> eCounter.php 306 - areaToProcess=".$areaToProcess ;
	if($areaToProcess == 1 )
    	{ 
		 if($timeAnalysis || $longPeriodAnalysis == TRUE )  {$chartType='linearLT';}  else  {$chartType='bar';}
		 SvgChart($device,$chartType,$webPage,$C_cBoxes,$areaToProcess,$Glabels,$Glabel,$Gdata,$GtsRadar,$A_unitFields,FALSE, $C_histoMaxrec, $StartSequence,$startDate,$A_lastMeasures, $C_linearGraphMargin,$timeAnalysis,$NextSequence, $BackSequence,$eCounterImages_F,$eCounterImages_A,$longPeriodAnalysis,$C_barSumInterval,$cnt) ;
		}	
	if($areaToProcess != 1 )
		{
		 if($timeAnalysis || $longPeriodAnalysis == TRUE )  {$chartType='linearLT';}  else  {$chartType='linear';}
		 SvgChart($device,$chartType,$webPage,$C_cBoxes,$areaToProcess,$Glabels,$Glabel,$Gdata,$GtsRadar,$A_unitFields,FALSE,$C_maxLines, $StartSequence,$startDate,$A_lastMeasures,$C_linearGraphMargin,$timeAnalysis,$NextSequence, $BackSequence,$eCounterImages_F,$eCounterImages_A,$longPeriodAnalysis,$C_barSumInterval,$cnt) ;		// line chart for other display	
		}
 //--  place of graphic
	if (file_exists($filePath."/tmp"))			// create download button if /tmp exists
	{
		  $csvSeparator=",";
		  $s=csvOutput(explode(",",$Glabels),$Glabel,explode(",",$Gdata),$csvSeparator,$filePath,$csvFilePrefix); 
		  echo $s;   $s="";
	}
//
	if($areaToProcess === 1 && $chartType==='bar')	{
		 if(isset($_REQUEST['D'])) {$C_barSumInterval = $_REQUEST['D'];$bsi="&D=".$C_barSumInterval; } //echo"<br/> _GET[D]=".$_REQUEST['D'] ."  bsi=".$bsi;
		 else {$bsi="";}
		 $s="";
		 $s  ="     <form action='".$homePage.".php?".$_SERVER['QUERY_STRING'].$bsi."'  method='post'>\n";
   		 $s .="		 <label>". $webPage->textTranslate(21,$webPage->textElements)."</label> \n";
   		 $s .="		 <select  name='D'  id='D' placeholder=' choose bar duration' onchange='this.form.submit();' > \n";
		 $s .="			  <option value='-60'";	($C_barSumInterval=="-60")? $s.=" selected='selected'" : ""; $s .= "> 60mn </option> \n";
		 $s .="			  <option value='-1440'";($C_barSumInterval=="-1440")? $s.=" selected='selected'" : ""; $s .= "> 1 ".$webPage->textTranslate(20,$webPage->textElements). "</option> \n";
		 $s .="       </select>\n";	
		 $s .="     </form>\n";
		 echo $s; $s="";
	} 
  } //-- end of else $Gvalues == 0
 //-- end of page
  $s="";
  $s .="  <div style='width:100%; height:".intval($grCnt*0.5)."em;'> &nbsp;</div>\n";	 // insert blank line depending on display content 
  echo $s;
  $webPage->pageFoot($codeVersion, $codeRelease,$grCnt,TRUE);
//phpinfo(INFO_VARIABLES);
  $webPage->pageEnd();
 //{ //header("Location: $homePage ");  } // BAD URL parameter - back to home page
?>
