<?php
//
//$codeVersion=1;
//$codeRelease='4.0';
//PHP Version 5.5.9
//eCounter  backend
//olivier rivaille
//04NOV17 - 26FEB18 - 12JUN18
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
//
//==============  SVG METHODS =========================================================================================
//
//-----------------------------------------------------------------------
//--  head of  graphic     called by  svgChart  (see below)                                             --
//-----------------------------------------------------------------------
function headSvg($chartType,$device,$webPage,$chartSvgSizeX,$chartSvgSizeY,$Glabel,$linearGraphMargin,$Ymax,$Ymin,$A_unitFields,$areaToProcess,$XYcount,$C_maxLines,$A_lastMeasures,$Ycorr,$longPeriodAnalysis,$StartSequence,$NextSequence, $BackSequence,$eCounterImages_F,$eCounterImages_A,$mAnalysis,$svgFile) 
{
 $infoTextSize=09.5;  // size in pixels 
 $infoTextMinY=$infoTextMaxX=$infoTextMaxY=0;
 $viewBoxY=0;
//echo"<br/> --40-- infoTextMinY=".$infoTextMinY." / infoTextMaxY=".$infoTextMaxY."\n";
 if($chartType=='radar') { $infoTextSize=$infoTextSize*0.885;}
 $k=$webPage->infoTextMin($chartType,$infoTextSize,$chartSvgSizeX,$linearGraphMargin,$infoTextMinY,$infoTextMaxX,$infoTextMaxY);
 $s=""; 
//echo"<br/> --44-- infoTextMinY=".$infoTextMinY." / infoTextMaxY=".$infoTextMaxY."\n";
if($chartType!='linearW2')  {		      // live svg linear graphics
  $s=" <div id='-- '>\n";
//-- show  navigation arrows
  if($BackSequence>0)		{ 
    	$s .="  <div id='h_backward'>\n"; // left button
    	$s .="      <a href='".$_SERVER['PHP_SELF']."?F=".$areaToProcess."&S=".$BackSequence;
		if($mAnalysis) { $s.="&M"; }
		if($longPeriodAnalysis) {$s .= "&T=".$longPeriodAnalysis;} 
    	$s .="&dev=".$device;
    	$s .="'> <img src='images/backward.svg' alt='".$webPage->textTranslate(4,$webPage->textElements)."'  title='".$webPage->textTranslate(5,$webPage->textElements)."' /></a>\n";
    	$s .="  </div>   <!-- end of  h_backward -->\n";
  } 
  if( $StartSequence > 0 )     { 
    	$s .="  <div id='h_forward'>\n";// right button
    	$s .="      <a href='".$_SERVER['PHP_SELF']."?F=".$areaToProcess."&S=".$NextSequence;
		if($longPeriodAnalysis) {$s .= "&T=".$longPeriodAnalysis;} 
		if($mAnalysis) { $s.="&M"; }
    	$s .="&dev=".$device;
    	$s .="'> <img src='images/forward.svg'  alt='dates postérieures'  title='dates postérieures' /></a>\n";
    	$s .="  </div>   <!-- end of  h_forward -->\n";
  }
 // graphic title   with date scope
 $s.="  <div id='svgTitle'> " .$Glabel . " <img src='".$eCounterImages_F.$eCounterImages_A[$areaToProcess]."' class='imgTitle' />";
 if($chartType=='linearLT')  {
  $j= $A_lastMeasures[0] - $A_lastMeasures[count($A_lastMeasures) -1]; 
  $j=intval($j/86400);       			//---- compute nb of days depending on measure interval value
  if($j==0) {$j=1;}
  $s.="  ".$webPage->textTranslate(18,$webPage->textElements). " ".$j." ".$webPage->textTranslate(19,$webPage->textElements);
  if($longPeriodAnalysis!=FALSE) {$s.=" ".$longPeriodAnalysis. "h </div>\n";}
 }
 $s.=" </div>\n";
 }  // -- end of $chartType!='linearW2'
 if($chartType=='radar') {
  $s.="  <svg  id='svg2' width='".intval($chartSvgSizeX*1.88)."' height='".intval($chartSvgSizeY)."' viewBox='0 0 ".intval($chartSvgSizeX)." ".intval($chartSvgSizeY)."' style='stroke:blue; stroke-width:0;'  lns='http://www.w3.org/2000/svg'>\n";
  }
  else
  { 
	$adjustLW2=1.101;  	$viewBoxY=-8;   $viewBoxX=5;
	$adjustMinMax=-20;
	if($chartType=='linearW2')  {
$viewBoxY=-11;   $viewBoxX=-9;
	 $s.="  <svg  id='svg2s' width='".intval(($chartSvgSizeX+ $linearGraphMargin*2))."' height='".intval(($chartSvgSizeY+ $linearGraphMargin*2))."' viewBox='".$viewBoxX." ".$viewBoxY." ".intval(($chartSvgSizeX)*$adjustLW2)." ".intval(($chartSvgSizeY)*$adjustLW2)."' style='stroke:blue; stroke-width:0;'  lns='http://www.w3.org/2000/svg'>\n";	
	 $webPage->writeSvgGraph($svgFile,$s); } 
 	else {  $s.="  <svg  id='svg2' width='".intval($chartSvgSizeX+ $linearGraphMargin*8)."' height='".intval($chartSvgSizeY+ $linearGraphMargin*12)."' viewBox='0 ".$viewBoxY." ".intval($chartSvgSizeX+ $linearGraphMargin*2)." ".intval($chartSvgSizeY)."' style='stroke:blue; stroke-width:0;'  lns='http://www.w3.org/2000/svg'>\n"; }
  }
  $s.="		<!--  min & max values xxx-->\n";
  $s.="		<text x='".$infoTextMaxX."' y='".$infoTextMaxY."' style='fill:black;font-size:".$infoTextSize."px;font-weight:bold;text-anchor: middle' >".$webPage->textTranslate(6,$webPage->textElements)."=".$Ymax." ".$A_unitFields[$areaToProcess]."</text>\n";
  $s.="		<text x='".$infoTextMaxX."' y='".$infoTextMinY."' style='fill:black;font-size:".$infoTextSize."px;font-weight:bold;text-anchor: middle' >".$webPage->textTranslate(7,$webPage->textElements)."=".$Ymin." ".$A_unitFields[$areaToProcess]."</text>\n";
  if($chartType!='linearW2') {echo $s; }
}
//
function radarSvgStart($chartType,$C_radarSvgSize,$C_markUnit,$C_maxYdataValue,$Glabel,$YtailorRatio, $StartDate) {
//-----------------------------------------------------------------------
//--  radar  graphic                                                   --
//-----------------------------------------------------------------------
/*---  marqueurs ellipses  ---------*/
 $s ="	 <g>\n";
  $radiusStep=intval(($C_radarSvgSize/2)/$C_markUnit);
  for($i=1; $i<10; $i++) {
  $radius=intval( $radiusStep * ($i));
  $s.="    	<ellipse id='ellipse' cx='".intval($C_radarSvgSize/2)."' cy='".intval($C_radarSvgSize/2)."' rx='".$radius."' ry='".$radius."' stroke='#666' fill='transparent'  stroke-width='0.2px'  />\n";
  }
 $s.="	 </g>\n";
 echo $s;
 unset($s);
 $adjustX=4;
 $adjustY=4;
 $radarMarkOrigin=$C_markUnit;
 $radarMarkOrigin=$C_markUnit;
 $radarMarkWidth=8;							// horizontal mark width
 $s="";
 $s.="	<g>\n";
 $s.="		<text x='".intval($C_radarSvgSize/2)."' y='".$radarMarkOrigin."' style='fill:black;font-size:10px;font-weight:bold;text-anchor: middle' >N</text>\n";
 $s.="		<text x='".intval($C_radarSvgSize/2)."' y='".intval($C_radarSvgSize-$adjustY)."' style='fill:black;font-size:10px;font-weight:bold;text-anchor: middle' >S</text>\n";
 $s.="		<text x='".intval($radarMarkOrigin-$adjustX)."' y='".intval($C_radarSvgSize/2+$adjustY)."' style='fill:black;font-size:10px;font-weight:bold;text-anchor: middle' >W</text>\n";
 $s.="		<text x='".intval($C_radarSvgSize-($radarMarkOrigin-$adjustX))."' y='".intval($C_radarSvgSize/2+$adjustY)."' style='fill:black;font-size:10px;font-weight:bold;text-anchor: middle' >E</text>\n";
 $s.="	</g>\n";
 $s.="	<g  fill='black' >\n";
 $s.="  		<line x1='".intval($C_radarSvgSize/2)."' y1='".$C_markUnit."' x2='".intval($C_radarSvgSize/2)."' y2='".intval($C_radarSvgSize-$C_markUnit)."'  style='fill:none;stroke:black;stroke-width:0.2px;' />\n";
 $s.="  		<line x1='".$C_markUnit."' y1='".intval($C_radarSvgSize/2)."' x2='".intval($C_radarSvgSize-$C_markUnit)."' y2='".intval($C_radarSvgSize/2)."'  style='fill:none;stroke:black;stroke-width:0.2px;' />\n";
 echo $s; unset($s); 
 $s=""; 
/*---  marqueurs repéres  ---------*/
 for($i=0; $i<9; $i++) {
  $radius=$radiusStep * $i;
  $y1label=round(($radiusStep*$i) / $YtailorRatio,1);
  $x=($C_radarSvgSize/2) - ($radarMarkWidth/2);
  $y1Coord=($C_radarSvgSize/2)-$radius;
  $s.="				<text x='".intval($x-$adjustX*3) ."' y='".$y1Coord."' style='fill:black;font-size:5.2px;' >". $y1label." </text>\n";
  $s.="  			<line x1='". $x ."' y1='".$y1Coord."' x2='".intval($x+$radarMarkWidth) ."' y2='".$y1Coord."'  style='fill:none;stroke:black;stroke-width:0.4px;' />\n";
 }
 $s.="    </g>\n";
/*---  marqueurs repére angle  ---------*/
 $yk=intval($C_radarSvgSize/2);
 $angleMarkLength=$radiusStep;
 $Irotate=30;
 $Itranslate=$radiusStep*($C_markUnit-2);
 $s.="	<g>\n";
 for($i=1; $i<12; $i++) {
 $s.=" 		<line x1='".$yk."' y1='".$yk."' x2='".intval(($C_radarSvgSize/2)+$angleMarkLength)."' y2='".$yk."' style='fill:none;stroke:#555;stroke-width:0.4px;' transform='rotate(".$Irotate*$i.",".$yk.",".$yk.") translate(".$Itranslate.", 0)'  /> \n";
  } 
 $s.="</g>\n";
 echo $s;
 unset($yk, $s);
}  //-- end of radarSvgStart()
//
function graphVerticalLines($chartType,$webPage,$XYcount,$C_markUnit,$C_linearSvgSizeX,$C_linearSvgSizeY,$Xzero,$C_linearGraphMargin,$XtailorRatio,$A_Xdata_label,$Xshift,$svgFile) 
{ 
 $markerIncrement=1;
 $textSize= 5.765431;
 if($chartType == 'linearW2')  {$textSize=$textSize * 1.75;  $textAnchor='middle'; $textX=2;} 
 if($XYcount> 25) {$markerIncrement=5;}
 if($XYcount> 45) {$markerIncrement=20;}
 if($XYcount> 200) {$markerIncrement=100;}
 $roundDecimal=2;
 $markSpace=round((($C_linearSvgSizeX-$C_linearGraphMargin)/$C_markUnit)*$markerIncrement, 0);
 $s  ="		<!--   vertical lines  -->\n	<g>\n";
 for($i=0; $i<round($C_markUnit/$markerIncrement, 0)+1; $i++)  {
  $s .="   <line x1='".intval(($i*$markSpace)+$C_linearGraphMargin)."' y1='0' x2='".intval(($i*$markSpace)+$C_linearGraphMargin)."' y2='".$C_linearSvgSizeY."'  style='fill:none;stroke:#ccc;stroke-width:0.2px;' />\n";
 }
 $s .="	</g>\n";
 if($chartType=='linearW2')  {$webPage->writeSvgGraph($svgFile,$s);}  else {echo $s;}

} //-- end of graphVerticalLines()
//-----------------------------------------------------------------------
//--  linear  graphic                                                  --
//-----------------------------------------------------------------------
function linearSvgStart($chartType,$webPage,$XYcount,$C_markUnit,$C_linearSvgSizeX,$C_linearSvgSizeY,$Xzero,$Yzero,$C_linearGraphMargin,$XtailorRatio,$YtailorRatio,$A_Xdata_label,$Yshift,$Xshift,$intSpacing,$svgFile,$A_Ymark,$Ymin) 
{ 
 $textAnchor='middle';							// xAxis values svg text anchor
 $textAnchor='start';							// xAxis values svg text anchor
 $textX=3.006;									// yAxis values ordonnée
 $textSize= 5.865431;
 if($chartType == 'linearW2')  {
	$textSize=$textSize * 1.28;
	$textAnchor='left';
	$textX=-5.6;
 } 
//  -----------------------------------------------------------------
// --- create Y labels                                           ----
//  -----------------------------------------------------------------
 $l  ="		<!--       Y labels     -->\n";
 $l .="	<g>\n";
 $s ="		<!--   horizontal lines  -->\n	<g>\n";
//--------------------------------  a changer --------- Y marks -----------------v----
  $l .="		<!--   Y marks  -->\n";
  for($i=0; $i<count($A_Ymark); $i++)   
    {
//echo"<br/> --188 -- Yshift=".$Yshift." / A_Ymark[$i]=".$A_Ymark[$i]." / YtailorRatio=".$YtailorRatio." / Yzero=".$Yzero."\n";
		if($Ymin>=0) {$Yc = round(($C_linearSvgSizeY - $Yshift - ($A_Ymark[$i] - $Yzero)*$YtailorRatio),2);}
		else  {$Yc = round(($C_linearSvgSizeY - $Yshift - ($A_Ymark[$i] + $Yzero)*$YtailorRatio),2);}
		$label=  round($A_Ymark[$i],2);           
		$s .="   <line x1='".($C_linearGraphMargin)."' y1='".$Yc."' x2='".($C_linearSvgSizeX+$C_linearGraphMargin)."' y2='".$Yc."'  style='fill:none;stroke:#ccc; stroke-width:0.2px;' />\n";   /*     */
		$l .="	 <text x='".$textX."' y='".$Yc."' style='fill :black;font-size:".$textSize."px;font-weight:normal;text-anchor: ".$textAnchor."' >".$label."</text>\n";
    }
 $l .="	</g>\n";
if ($Ymin<0) {
 	$l .="		<!--   zero mark text & line -->\n	<g>\n";
 	$ZeroMark = round(($C_linearSvgSizeY - $Yshift - ($Yzero *$YtailorRatio)),0);
 	$l .="	 <text x='".$textX."' y='".$ZeroMark ."' style='fill :black;font-size:".$textSize."px;font-weight:normal;text-anchor: ".$textAnchor."' >0</text>\n";
	$l .="     <line x1='".($C_linearGraphMargin)."' y1='".$ZeroMark."' x2='".($C_linearSvgSizeX+$C_linearGraphMargin)."' y2='".$ZeroMark."'  style='fill:none;stroke:#ccf; stroke-width:0.2px;' />\n";   /*      */
 	$l .="	</g>\n";
	}
 $s .="	</g>\n";

if($chartType=='linearW2')  {$webPage->writeSvgGraph($svgFile,$s); $webPage->writeSvgGraph($svgFile,$l);}
 else
  { 
 	echo $s; echo $l;
  }
 unset($l, $A_Ymark);
 graphVerticalLines($chartType,$webPage,$XYcount,$C_markUnit,$C_linearSvgSizeX,$C_linearSvgSizeY,$Xzero,$C_linearGraphMargin,$XtailorRatio,$A_Xdata_label,$Xshift,$svgFile);
 unset($l);
} //-- end of linearSvgStart()
//-----------------------------------------------------------------------
//--  bar  graphic                                                  --
//-----------------------------------------------------------------------
function barSvgStart($chartType,$webPage,$XYcount,$C_markUnit,$C_linearSvgSizeX,$C_linearSvgSizeY,$Xzero,$Yzero,$C_linearGraphMargin,$XtailorRatio,$YtailorRatio,$A_Xdata_label,$Yshift,$Xshift,$intSpacing,$svgFile,$A_Ymark,$barWidth,$Ymin) 
{ 
 $markerIncrement=1;
 $textAnchor='middle';							// xAxis values svg text anchor
 $textAnchor='start';							// xAxis values svg text anchor
 $textX=3.9876;								// yAxis values ordonnée
 $textSize= 5.865431;
 if($XYcount> 25) {$markerIncrement=5;}
 if($XYcount> 40) {$markerIncrement=50;}
 if($XYcount> 200) {$markerIncrement=100;}
 $roundDecimal=2;
//  -----------------------------------------------------------------
// --- create Y labels                                           ----
//  -----------------------------------------------------------------
 $l  ="		<!--       Y labels     -->\n";
 $l .="	<g>\n";
 $s ="		<!--   horizontal lines  -->\n	<g>\n";
 for($i=0; $i<count($A_Ymark); $i++)   
    {
		if($Ymin>=0) {$Yc = round(($C_linearSvgSizeY - $Yshift - ($A_Ymark[$i] - $Yzero)*$YtailorRatio),2);}
		else  {$Yc = round(($C_linearSvgSizeY - $Yshift - ($A_Ymark[$i] + $Yzero)*$YtailorRatio),2);}
		$label=  round($A_Ymark[$i],2);              
		$s .="   <line x1='".($C_linearGraphMargin)."' y1='".$Yc."' x2='".($C_linearSvgSizeX-$barWidth+$C_linearGraphMargin)."' y2='".$Yc."'  style='fill:none;stroke:#ccc; stroke-width:0.2px;' />\n";   /* # blaq  */
		$l .="	 <text x='".$textX."' y='".$Yc."' style='fill :black;font-size:".$textSize."px;font-weight:normal;text-anchor: ".$textAnchor."' >".$label."</text>\n";
    }
 $s .="	</g>\n";
 $l .="	</g>\n";
if($chartType=='linearW2')  {$webPage->writeSvgGraph($svgFile,$s); $webPage->writeSvgGraph($svgFile,$l);}
 else
  { 
 	echo $s; echo $l;
  }
 unset($l);
/* -- create Y mark spaces  ---- */ 
 $markSpace=round((($C_linearSvgSizeX-$C_linearGraphMargin)/$C_markUnit)*$markerIncrement, 0);
 $s  ="		<!--   vertical lines  -->\n	<g>\n";
 for($i=0; $i<round($C_markUnit/$markerIncrement, 0)+1; $i++)  {
  $s .="   <line x1='".intval(($i*$markSpace)+$C_linearGraphMargin)."' y1='0' x2='".intval(($i*$markSpace)+$C_linearGraphMargin)."' y2='".$C_linearSvgSizeY."'  style='fill:none;stroke:#ccc;stroke-width:0.2px;' />\n";
 }
 $s .="	</g>\n";
 //if($chartType=='linearW2')  {  $webPage->writeSvgGraph($svgFile,$s);}  else { echo $s;}
 echo $s;
 unset($l);
} //-- end of barSvgStart()
// 
function SvgEnd($chartType,$webPage,$C_linearSvgSizeX,$C_linearSvgSizeY,$Xzero,$Yzero,$C_linearGraphMargin,$XtailorRatio,$YtailorRatio,$Yshift,$Xshift,$svgFile) 
{
 if($chartType=='linear' || $chartType=='linearLT' || $chartType=='linearW2' || $chartType=='bar') 
 {
	  if($chartType=='linearW2') { $AxisStrokeColor="blue";} else { $AxisStrokeColor="black";}
	  $AxisStrokeWidth=0.8765443;  
 	  $s  ="		<!--   axis lines  -->\n";
	  $s .="	<g>\n";
 	  $s .="   <line x1='".$C_linearGraphMargin."' y1='".intval($C_linearSvgSizeY+ ($Yshift)/$YtailorRatio)."' x2='".intval($C_linearSvgSizeX+$C_linearGraphMargin)."' y2='".intval($C_linearSvgSizeY+ ($Yshift)/$YtailorRatio)."'  style='fill:none;stroke:".$AxisStrokeColor.";stroke-width:".$AxisStrokeWidth."px;' />\n";
 	  $s .="   <line x1='".$C_linearGraphMargin."' y1='0' x2='".$C_linearGraphMargin."' y2='".$C_linearSvgSizeY."'";	 
 	  $s .="  style='fill:none;stroke:".$AxisStrokeColor.";stroke-width:".$AxisStrokeWidth."px;' />\n";
 	  $s .="	</g>\n";
      if($chartType=='linearW2')  {$webPage->writeSvgGraph($svgFile,$s);}  else { echo $s;}
  }
 $s="\n";
 $s.="  </svg>\n";
 if($chartType=='linearW2')  {$webPage->writeSvgGraph($svgFile,$s);  $webPage->closeSvgGraph($svgFile);} 
  else { 
	 $s.="  </div>\n";
	 echo $s;
	}
 unset($s);
}  //-- end of SvgEnd()
//------------------ BUILD THE SVG CHART -------------------------------------------------------
function SvgChart($device,$chartType, $webPage, $C_cBoxes,$areaToProcess,$Glabels,$Glabel,$Gdata,$GtsRadar,$A_unitFields,$A_wdt,$C_maxLines,$StartSequence, $StartDate,$A_lastMeasures,$C_linearGraphMargin,$longPeriodAnalysis,$NextSequence, $BackSequence,$eCounterImages_F,$eCounterImages_A,$mAnalysis,$C_barSumInterval,$cnt) 
{
//echo"<br/> ***** Glabels=".print_r($Glabels);
//echo"<br/> ***** Gdata=".print_r($Gdata);
//
// $chartType : radar / linear / linearLT (suivi heure  sur 1 mois).. linearW2 (refresh auto out temp),   bar, bar /Interval
// $Glabels : X values  ; for radar : wind direction  meteo angle
// $Gdata  : Y values ; for radar : wind speed 
// $Glabel : graphic title
// $GtsRadar : Timestamp for radar graphs
// $longPeriodAnalysis : 31 days trend  graphic full day o by hour in the day: nos point values put in the graphic
// Cette fonction crée un diagramme en SVG  (Linear ou radar)
// calcul des coordonnées polaires  SVG du radar  à partir de Direction et Vitesses du vent
// les angles meteo sont d'abord convertis en angle trigonometrique 
//
$C_angleTrigoConstant=450;					// cosnstante : conversion angle girouette -> angle trigo.
$C_radarSvgSize=240;						// constante  : taille du radar (carré)
$C_maxYdataValue=100;				   		// relative value ie 10 * radarUnit
$C_linearSvgSizeX=580;						// constante : largeur X graph lineaire
$C_linearSvgSizeY=190;						// constante : largeur Y graph lineaire
$C_barStrokeWidth=1.5;						// bar chart stoke width in pixels
//-------  COMPUTE  GRAPHIC HEIGHT ---------------------------------
$C_GHconst1=41;
$C_GHconst2=8;
$C_GHconst3=14;
if($C_cBoxes==0) {$C_linearSvgSizeY=195;}
if(0<$C_cBoxes  && $C_cBoxes<4) {$C_linearSvgSizeY=$C_cBoxes*$C_GHconst1 - ($C_cBoxes * $C_GHconst2);}
if($C_cBoxes>4) {$C_linearSvgSizeY=$C_cBoxes*$C_GHconst1 - ($C_cBoxes * $C_GHconst3);}
//
$C_linearGraphMargin=25;				// left  graph pixels margin  between left  border and Y axis 
$C_markUnit=5;							// nombre de reperes intermediaires
$graphPointT=0.97;						// font-size for Y value 
$graphPointTs=0.87;						//font-size for point date vakue
//
$A_XGraphCoord=array();					// coordonnées  X calculated to fit positive values for graph
$A_YGraphCoord=array();					// coordonnées  Y calculated to fit positive values for graph
$A_YGraphBarHeight=array();				// Hauteur barre   Y si grapg = bar chart
$A_Ydata_tailored=array();
$A_Xdata_label=array();					// array to save x value when x is a date (long string)
$A_Ydata=array();						// working area  to allow redaing graphs from left to right
$A_Xdata=array();						// working area  to allow reading graphs from left to right
$A_Tdata=array();						// working area  timestamp for radar graphs
$A_workingArray=array();					// 
$Ymax=0; 								// get maximum value of Y data
$Xmax=0; 								// get maximum value of X data
$Ymin=0; 								// get minimum value of Y data
$Xmin=0; 								// get minimum value of X data
//
$lastMeasure=0;
$measureError=FALSE;					// measure interval error (too long,..)  will be used to change the color in graphic reporting
//
$svgFile=FALSE;							// svg File ID
$C_svgFileName="DEVICES/".$device."/g".$areaToProcess.".svg"; 	// svg  graph for automatic refresh 
//-- tailor graphic scale to max Y value  & max X value except for radar -----------
$A_workingArray=explode(",",$Glabels);
for($i=(count($A_workingArray)-1);$i>=0;$i--) {$A_Xdata[].=$A_workingArray[$i];	}		// return data for reading from left to right
unset($Glabels);
$A_workingArray=explode(",",$Gdata);
for($i=(count($A_workingArray)-1);$i>=0;$i--) {$A_Ydata[].=$A_workingArray[$i];	}		// return data for reading from left to right
unset($Gdata);
$A_workingArray=explode(",",$GtsRadar);
for($i=(count($A_workingArray)-1);$i>=0;$i--) {$A_Tdata[].=$A_workingArray[$i];	}
unset($GtsRadar);
//--  $A_Xdata  & $A_Ydata have the same size 
unset($A_workingArray);  
//
//----- extract  data  ---------------------------------------------------------------
//-- compute min and max values, mesure interval value, 
//-- compute X & Y ratios,
//-- compute SVG tailored coordinates
//
if($chartType== 'linearW2') { $svgFile=$webPage->openSvgGraph($C_svgFileName); }			// open file for dynamic graphic  process
$countEqual=1;
$intSpacing=FALSE;
$measureInterval=0;
for($i=0; $i<count($A_Xdata); $i++) { 
	if( $i == 0) { 
        $lastMeasure= rtrim(ltrim($A_Xdata[$i], "\'"), "\'"); 
	    $mcnt=0;														//  mcnt = valid measures count
      }
	 if($chartType=='linear' || $chartType=='linearLT' || $chartType=='linearW2'|| $chartType=='bar') {
	   $A_Xdata[$i]= rtrim(ltrim($A_Xdata[$i], "\'"), "\'");
	   if($webPage->is_timestamp($A_Xdata[$i])) 
		{
	 	 if($i > 0) {
			$t=$A_Xdata[$i]-$lastMeasure; $lastMeasure= $A_Xdata[$i];
			if($chartType=='linear' || $chartType=='linearLT'|| $chartType=='linearW2') {
					if($t > 3700 ||  $t ==0) {$measureError=TRUE; }   	// cancel invalid measures  2399 for 40mn, 3599 for 1h
			 		else {$measureInterval +=$t; $mcnt++;}
			}
			if($chartType=='bar') {
					if(($t > abs($C_barSumInterval*60)*1.2) ||  $t ==0) {$measureError=TRUE;} 
			 		else {$measureInterval +=$t; $mcnt++;}
		  	}
		  }  
		 $A_Xdata_label[$i]=date("dMy-H:i:s", $A_Xdata[$i]);
		 $A_Xdata[$i] = $i;
		}
	 }
	 if($i == 0) {$Ymin=$A_Ydata[$i];$Xmin=$A_Xdata[$i];}				// init min values
	 if($i >0) { if($A_Ydata[$i]==$A_Ydata[$i-1]) {$countEqual++ ;} }	// init min values
	 if($A_Ydata[$i]>$Ymax) {$Ymax=$A_Ydata[$i];}						// keep max value of Y coordinate ( radius for radar)
	 if($A_Xdata[$i]>$Xmax) {$Xmax=$A_Xdata[$i];}						// keep max value of X  coordinate ( angle for radar)
	 if($A_Ydata[$i]<$Ymin) {$Ymin=$A_Ydata[$i];}						// keep min value of Y coordinate ( radius for radar)
	 if($A_Xdata[$i]<$Xmin) {$Xmin=$A_Xdata[$i];}						// keep min value of X  coordinate ( angle for radar)
			
}  // end of for($i=0; $i<count($A_Xdata); $i++)

 $XYcount=count($A_Xdata);
 if($mcnt!=0) {
	if($chartType=='bar') {$measureInterval=abs($measureInterval/$cnt);}
	else {$measureInterval=abs($measureInterval/ $mcnt);}
 }

 $S_measureInterval = gmdate(" H:i:s",$measureInterval);
 $heure = intval(abs($measureInterval / 3600));
 $measureInterval= $measureInterval - ($heure * 3600);
 $minute = intval(abs($measureInterval/ 60));
 $measureInterval = $measureInterval - ($minute * 60);
 $seconde = abs($measureInterval);
//
 $Ycorr=0; 														//=======  adjust min & max ======
 $YYmax=$Ymax;				// backup Ymax value before adding y graph margin						// save data value
 $YYmin=$Ymin;				// backup Ymin value before adding y graph margin
 if($YYmax == $YYmin) {$Ycorr=$YYmax/4; if($YYmax==0) {$Ycorr=1;}}   //  <<<<<=======
 if($Xmax == $Xmin) {$Xmin=$Xmax/2;} 
 $Ymax+=$Ycorr;
 $Ymin-=$Ycorr;
 $deltaY=$Ymax-$Ymin;
 $incrY=$deltaY/$C_markUnit;
 $Ymax=$Ymax+2*$incrY; 
 if($incrY <= $Ymin) {$Ymin=$Ymin-$incrY;}
//
// compute Ymarks for y axis  ------------------------- 
 $A_Ymark=array();
 $A_Ymark[0]=$Ymin;
 for ($i=1; $i < $C_markUnit +2; $i++) {
	$A_Ymark[$i]=$A_Ymark[$i-1]+$incrY;
 }
// compute Yratio Xratio and Yzero ------------------------- 
 $YtailorRatio=1;
 if(($deltaY)!=0) {$YtailorRatio=$C_linearSvgSizeY / ($Ymax-$Ymin);}  // ---- NEW NEW NEW ------
 if ($Ymin<0) {$Yzero=abs($YYmin);} else {$Yzero=$YYmin;}  
 $Yshift=$incrY*$YtailorRatio;
//
 $XtailorRatio=1; 
 if($Xmin<0)  {$Xzero=abs($Xmin); $Xshift=$Xzero;}  else {$Xzero=$Xmin; $Xshift=-$Xzero;}
 if($deltaY!=0) {
	if($chartType=='bar') {$XtailorRatio=($C_linearSvgSizeX-($C_linearSvgSizeX/($XYcount))-$C_linearGraphMargin) / ($Xmax-$Xmin);}
	 else {$XtailorRatio=($C_linearSvgSizeX-(0*$C_linearGraphMargin)) / ($Xmax-$Xmin);}
  } 
//----- linear & bar process ------- compute SVG tailored coordinates -------------------
$barNumber= count($A_Xdata)-0;
if($barNumber>=2) { $bn=$barNumber;}
$barWidth= round((($C_linearSvgSizeX-$C_linearGraphMargin)/$barNumber - ($C_barStrokeWidth *2)),1);
if($chartType== 'linear' || $chartType== 'linearLT' || $chartType== 'linearW2'  || $chartType== 'bar') 
 {
 for($i=0; $i<count($A_Xdata); $i++) {
	if($chartType=='bar') {
		$wBarStroke= ($i >0)? $C_barStrokeWidth*$i*2/$XtailorRatio:0;
		$A_XGraphCoord[$i] = round(($XtailorRatio*($A_Xdata[$i]+$Xshift)),2)+$wBarStroke;
		if($Ymin < 0) 
			  {$barH = $C_linearSvgSizeY -($A_Ydata[$i]-$Yzero*($Ymin/abs($Ymin)))*$YtailorRatio ;}		// barH  SVG coordinate	
	    else  {
				if($Ymin>0) {$barH = $C_linearSvgSizeY -($A_Ydata[$i]-$Yzero*($Ymin/abs($Ymin)))*$YtailorRatio;}
				else  {$barH = $C_linearSvgSizeY -($A_Ydata[$i]-$Yzero*($Ymin))*$YtailorRatio;}
			   }	// barH  SVG coordinate
		// bar : compute high point   ; bar-high (barH) point is the same as linear
		$A_YGraphCoord[$i] = $barH - $Yshift; 
		$A_YGraphBarHeight[$i] = abs($A_Ydata[$i] -$Yzero)*$YtailorRatio + $Yshift; 		// bar height
		}

	if($chartType=='linear' || $chartType=='linearLT' || $chartType=='linearW2')  {
		$A_XGraphCoord[$i] = round(($XtailorRatio*($A_Xdata[$i]+$Xshift)),2);
 		if($Ymin < 0) 
			{$A_YGraphCoord[$i] = $C_linearSvgSizeY - $Yshift - ($A_Ydata[$i]+$Yzero ) *$YtailorRatio; }	// barH  SVG coordinate
		  else  {$A_YGraphCoord[$i] = $C_linearSvgSizeY - $Yshift - ($A_Ydata[$i]-$Yzero)*$YtailorRatio ; }	// barH  SVG coordinate
		}
	  }  // end of for $i
	 $chartSvgSizeX=$C_linearSvgSizeX;									// set svg sizes
	 $chartSvgSizeY=$C_linearSvgSizeY;
} // end of $chartType=='linear' & 'bar'
//
//----- radar process ---------------------------------------------------------------
if($chartType=='radar') {
	if($YYmax != 0) {$YtailorRatio=$C_maxYdataValue/ $YYmax; }        					// computed regarding a 100 maximum value
	$A_Ydata_tailored=array();
	 for($i=0; $i<count($A_Xdata); $i++) {
		$A_Ydata_taiinfoTextSizelored[$i]=$A_Ydata[$i] * $YtailorRatio;						// tailor Rayon value
	 }  // end of for $i
	 $XgraphMax=0;   $YGraphMax=0; 
	 for($i=0; $i<count($A_Xdata); $i++) {
		if($A_Xdata[$i] < 90) {$A_Xdata[$i]=$A_Xdata[$i]+360;} 							// conversion en angle trogonometrique	
		$A_Xdata[$i]= $C_angleTrigoConstant-$A_Xdata[$i];
		$A_XGraphCoord[$i]= intval(cos(deg2rad($A_Xdata[$i])) * $A_Ydata_tailored[$i] )+ ($C_radarSvgSize/2)  ;	// calcul des coordonnées cartesiennes
		$A_YGraphCoord[$i]= intval(Sin(deg2rad($A_Xdata[$i])) * $A_Ydata_tailored[$i] )   ;
		if($A_YGraphCoord[$i] >= 0) {$A_YGraphCoord[$i]=intval( $C_radarSvgSize/2 - $A_YGraphCoord[$i]);}	// translation of negative coordinates
		     else {$A_YGraphCoord[$i]=intval( $C_radarSvgSize/2 + abs($A_YGraphCoord[$i]));}
	 }  // end of for $i
	 unset($A_Ydata_tailored); 					// free working memory space
	 $chartSvgSizeX=$C_radarSvgSize;			// set svg sizes
	 $chartSvgSizeY=$C_radarSvgSize;
} // end of $chartType=='radar'
//
//-------------------------------------------   CREATE  SVG   --------------------
//------ head and structure lines of  SVG axes   ---------------------------------
headSvg($chartType,$device,$webPage,$chartSvgSizeX,$chartSvgSizeY,$Glabel,$C_linearGraphMargin,$YYmax,$YYmin,$A_unitFields,$areaToProcess,$XYcount,$C_maxLines,$A_lastMeasures,$Ycorr,$longPeriodAnalysis,$StartSequence,$NextSequence, $BackSequence,$eCounterImages_F,$eCounterImages_A,$mAnalysis,$svgFile);    // radar has same value for width & height
if($chartType== 'radar')  {
 radarSvgStart($chartType,$C_radarSvgSize,10,$C_maxYdataValue,$Glabel,$YtailorRatio, $StartDate);
 }
if($chartType== 'bar') {
	barSvgStart($chartType,$webPage,$XYcount,$C_markUnit,$C_linearSvgSizeX,$C_linearSvgSizeY,$Xzero,$Yzero,$C_linearGraphMargin,$XtailorRatio,$YtailorRatio,$A_Xdata_label,$Yshift,$Xshift,$intSpacing,$svgFile,$A_Ymark,$barWidth,$Ymin);
 }
if($chartType=='linear' || $chartType=='linearLT') { 
	linearSvgStart($chartType,$webPage,$XYcount,$C_markUnit,$C_linearSvgSizeX,$C_linearSvgSizeY,$Xzero,$Yzero,$C_linearGraphMargin,$XtailorRatio,$YtailorRatio,$A_Xdata_label,$Yshift,$Xshift,$intSpacing,$svgFile,$A_Ymark,$Ymin);
 }
if($chartType=='linearW2') { 
	linearSvgStart($chartType,$webPage,$XYcount,$C_markUnit,$C_linearSvgSizeX,$C_linearSvgSizeY,$Xzero,$Yzero,$C_linearGraphMargin,$XtailorRatio,$YtailorRatio,$A_Xdata_label,$Yshift,$Xshift,$intSpacing,$svgFile,$A_Ymark,$Ymin);
 }
//------  POLYGON  POINTS  ---------------------------------------------------------
//--- CONSTANTS ---------------------------------------
    $points="";
    $cssPoints="";
    $textRotateAngle=65;
    $textAnchor='start';
    $textYadjust=6;
    $textFontSize=6.41;
    $unitField=$A_unitFields[$areaToProcess];   /* set unit measure value  */
    //--  set Xlabel orientation angle  depending on nb of points ---- 
      if(count($A_Xdata_label)>0) {
       if(count($A_Xdata_label)>10) {
	    $textYadjust=2;
	    if(strlen($A_Xdata_label[0]) > 10) {
		    $textRotateAngle=40; $textAnchor='start';
		    if(strlen($A_Xdata_label[0]) > 8) {$textRotateAngle=60;$textFontSize=7;}
		    if(strlen($A_Xdata_label[0]) > 12) {$textRotateAngle=75;$textFontSize=6;}
	    }
       }
      }
    // --- build polygon/polyline linear  graph ------------------------------------
    if(count($A_XGraphCoord)>40) { $skip1on2=TRUE;} else {$skip1on2=FALSE;}
    if(count($A_XGraphCoord)<30) {$skip1on2=FALSE;}
    $pointRectHeight=25;			// dimensions of rectangle surrounding point label
    $pointRectWidth=46;
    $pointRectCenterY= $pointRectHeight/2;  
    $pointRectCenterX= $pointRectWidth/2;
    $limitX=(($C_linearSvgSizeX-$C_linearGraphMargin+2)*$pointRectWidth)/$C_markUnit;
    $limitY=(($C_linearSvgSizeY+2)*$pointRectHeight)/$C_markUnit; 
    $pointRadius=1.54321;											// rayon du point de données
    $pointRadiusCurrent=3.715;										// rayon du point de données - valeur current = valeur du jour
    if($chartType  == 'linearW2')  { $pointRadius = $pointRadius*2.1; }

	$polygon=" <defs>\n";
	$polygon.="  <lineargradient id='vertical' x1='0%' y1='0%' x2='0%' y2='100%'>\n";
	$polygon.="  <stop offset='0%' stop-color='rgba(12, 0, 147, 0.30)' />\n";
	$polygon.="  <stop offset='100%' stop-color='white' />\n";
	$polygon.="  </lineargradient>\n";
	$polygon.=" </defs>\n";
	if($chartType=='linear' || $chartType=='linearLT' || $chartType=='linearW2') {
		$polygon.="	<polygon points='" ;
		if($chartType  == 'linearW2') {$polygon="	<polyline points='" ;}
		//$polygon="	<path d='M" ;
		$l  ="		<!--   X axis values  -->\n";				// X labels
		$l .="	<g  fill='black'>\n";
	}
	if($chartType== 'bar') {
		$infoTextSize=10.50;
		$barNumber= count($A_XGraphCoord);
		$polygon.=" <line x1='".$C_linearGraphMargin."' y1='".($C_linearSvgSizeY- $Yshift)."' x2='".($C_linearSvgSizeX) ."' y2='".($C_linearSvgSizeY- $Yshift)."' style='stroke:rgb(0,0,0);stroke-width:1' />\n";
		$l  ="		<!--   X axis values  -->\n";				// X labels
		$l .="	<g>\n";
	}
//- loop in X coordinates ----------------------------------------------------------------------
	for($i=0; $i<count($A_XGraphCoord); $i++) {
		  if(($i == 0) AND ($chartType  == 'linear' || $chartType  == 'linearLT'|| $chartType  == 'bar')) 
			{	
			$fromDate=$A_Xdata_label[$i];
 //echo"<br/> --544-- Ymin=".$Ymin." / Yzero=".$Yzero." / Yshift=".$Yshift." \n";
			if($chartType  == 'linear' || $chartType  == 'linearLT') {
				if($Ymin<0) {$polygon.=	$A_XGraphCoord[$i]+$C_linearGraphMargin.",".($C_linearSvgSizeY- $Yzero*$YtailorRatio -$Yshift)." ";}
  				else 		{$polygon.=	$A_XGraphCoord[$i]+$C_linearGraphMargin.",".($C_linearSvgSizeY)." ";}
			}
		   } 	
		   if($chartType  != 'radar')	{$toDate=$A_Xdata_label[$i];} //$YtailorRatio*$Yc  <<<<======  turnaround   !!!!!	// create base y axis points for finear graphics
		   if($chartType  == 'radar') 	{$polygon.=$A_XGraphCoord[$i].",".$A_YGraphCoord[$i]." "; }
		   if($chartType  == 'linear' || $chartType  == 'linearLT' || $chartType  == 'linearW2' )  
			{ $polygon.=$A_XGraphCoord[$i]+$C_linearGraphMargin.",".$A_YGraphCoord[$i]." ";  }
		   $j=$i+1;			  							// $j is used to name the id of the graph points
		   if($chartType  == 'linear'  || $chartType  == 'bar' ) {
		    // --- create Xlabel data  ------
			   $lw  ="   <line x1='".($A_XGraphCoord[$i]+$C_linearGraphMargin)."' y1='".intval($C_linearSvgSizeY-$Yshift)."' x2='".($A_XGraphCoord[$i]+$C_linearGraphMargin)."' y2='".intval($C_linearSvgSizeY- $Yshift)."'  style='fill:none;stroke:#f00;stroke-width:0.2px;' />\n";
			   if($textRotateAngle>0) {$xCoord=$A_XGraphCoord[$i]+$C_linearGraphMargin;}    else {$xCoord=$A_XGraphCoord[$i];}
				$lw .="	  <text x='".$xCoord."' y='".intval($C_linearSvgSizeY+$textYadjust)."' transform='rotate(".$textRotateAngle.",".($A_XGraphCoord[$i]+$C_linearGraphMargin).",".intval($C_linearSvgSizeY+$textYadjust +0).")' style='fill:black;font-size:".$textFontSize."px;font-weight:normal;text-anchor: ".$textAnchor."' >".$A_Xdata_label[$i]."</text>\n";
			   if($skip1on2 && (($i % 2) ==0)) {$l .= "";  } 	else {$l .= $lw;}					// if more than 40 points  skip one  X label on TEN  (1on10)
		   }  //-- end of $chartType  == 'linear' || $chartType  == 'bar' )

		 if($chartType  == 'linearLT') {
				// --- create Xlabel data  ----------------------------------------
			   $lw  ="   <line x1='".($A_XGraphCoord[$i]+$C_linearGraphMargin)."' y1='".intval($C_linearSvgSizeY)."' x2='".($A_XGraphCoord[$i]+$C_linearGraphMargin)."' y2='".intval($C_linearSvgSizeY-4)."'  ";
			   $lw  .="  style='fill:none;stroke:#f00;stroke-width:0.2px;' />\n";
			   if($textRotateAngle>0) {$xCoord=$A_XGraphCoord[$i]+$C_linearGraphMargin;}    else {$xCoord=$A_XGraphCoord[$i];}
			   $lw .="	  <text x='".$xCoord."' y='".intval($C_linearSvgSizeY+$textYadjust)."' transform='rotate(".$textRotateAngle.",".($A_XGraphCoord[$i]+$C_linearGraphMargin).",".intval($C_linearSvgSizeY+$textYadjust +0).")' style='fill:black;font-size:".$textFontSize."px;font-weight:normal;text-anchor: ".$textAnchor."' >".$A_Xdata_label[$i]."</text>\n";
			   if(($mAnalysis==TRUE) && (($i % 50) == 0)) {$l .= $lw; } 	else {$l .= "";  } 				// if more than 40 points  skip one  X label on TEN  (1on10)
			   if( $longPeriodAnalysis != FALSE) {	if(($i % 5) ==0) {$l .= $lw;} }	else {$l .= "";  } 			// if more than 40 points  skip one  X label on TEN  (1on10)
		 }  //-- end of $chartType  == 'linearLT')
		 unset($lw);
		//  -----------------------------------------------------------------
		// --- create SVG measure points                                 ----
		//  -----------------------------------------------------------------
		// --   print measure point except for Monthly graphic -------
		 $infoTextSize=10.50;
		 $infoTextMinY=$infoTextMaxX=$infoTextMaxY=0;
		 if($mAnalysis==FALSE) { 
   		      ($chartType =='bar')? $v='polygon': $v='points'; 
		      $$v.="	<g id='point" .$j. "' fill='none'>\n"; 		    /* --- insert measure point  --*/
		      $unitField=$A_unitFields[$areaToProcess];                     /* set unit measure value  */
		      if($chartType  == 'radar') {     
			     if($A_Tdata[$i]==$StartDate) { 
				    $points.="    	<ellipse id='gp".$j."E' cx='".$A_XGraphCoord[$i]."' cy='".$A_YGraphCoord[$i]."' rx='".$pointRadiusCurrent."px' ry='".$pointRadiusCurrent."px' stroke='white'  fill='rgba(48,63,159, 1.0)' stroke-width='2'  class='graphPointE'/>\n";
			       }
			      else {$points.="    	<ellipse id='gp".$j."E' cx='".$A_XGraphCoord[$i]."' cy='".$A_YGraphCoord[$i]."' rx='".$pointRadius."px' ry='".$pointRadius."px' stroke='white'  fill='rgba(48,63,159, 1.0)' stroke-width='2'  class='graphPointE'/>\n"; }
			    }
		      if($chartType  == 'linear' || $chartType  == 'linearLT' || $chartType  == 'linearW2') 
			     {
			      $v=$A_XGraphCoord[$i]+$C_linearGraphMargin;
			      $points.="    	<ellipse id='gp".$j."E' cx='".$v."' cy='".$A_YGraphCoord[$i]."' rx='".$pointRadius."px' ry='".$pointRadius."px' stroke='white'  fill='rgba(48,63,159, 1.0)' stroke-width='2'  class='graphPointE'/>\n"; 
			     }
		      if($chartType  == 'linear' || $chartType  == 'radar' || $chartType=='linearLT' || $chartType  == 'linearW2'  || $chartType  == 'bar')
			    {
			        if($chartType=='linear' || $chartType=='linearLT' || $chartType  == 'linearW2' || $chartType  == 'bar')  { 
				        $infoPointX=0.8*$chartSvgSizeX/4; 
				        $webPage->infoTextMin($chartType,$infoTextSize,$chartSvgSizeX,$C_linearGraphMargin,$infoTextMinY,$infoTextMaxX,$infoTextMaxY);
				        $infoPointY=$infoTextMinY+15;
				        if($chartType == 'linearW2') 
				        { 
				          $infoPointX=$chartSvgSizeX/2; $infoPointY=($chartSvgSizeY-10)/5 ;
				        } 
			        } 
			        if($chartType == 'bar')  { 
				        $j=$i+1;	
				        $polygon.="     <rect id='gp".$j."R'  x='".($A_XGraphCoord[$i]+$C_linearGraphMargin)."' y='".($A_YGraphCoord[$i])."' width='".$barWidth."' height='".($A_YGraphBarHeight[$i]) ."' fill='url(#vertical)' stroke-width='".$C_barStrokeWidth."' stroke='rgb(50,50,50)'  rx='1.2'    class='graphPointR'/>\n";	
				        $polygon.="     <text id='gp".$j."T'  x='".$infoPointX."' y='".intval($infoPointY-10.1)."'  class='graphPointT'   style='fill:red; font-size:".$graphPointT."em;text-anchor: start'>".$A_Ydata[$i]." ".$unitField."</text>\n";	
			        }   			  
                }
			    if($chartType=='radar') { $infoPointY=24 ; $infoPointX=0.008*$chartSvgSizeX/4 ;}
		     	 /* --- design a rect to surround label --*/
		      	$adjustX=0; 
		     	($chartType  == 'linear') ? $adjustY=-0.320*$pointRectHeight : $adjustY=-0.300*$pointRectHeight; 
		      	$gpRX=intval($A_XGraphCoord[$i]-$pointRectCenterX+$adjustX);
		     	$gpRY=intval($A_YGraphCoord[$i]-$pointRectCenterY+$adjustY);
		      	/*------ tailor position X of label depending on the distance to the extremities of the graphic ----------------*/
		      	if($chartType  == 'linear') {
		      		if($gpRX< 0)  {$adjustX= abs($gpRX); }
		      		if(($gpRX+$pointRectWidth)>=$C_linearSvgSizeX) {$adjustX= -($C_linearSvgSizeX-$A_XGraphCoord[$i]+$pointRectWidth/2); }
		      	}
		      	if($chartType  == 'radar') {$adjustX-=9.55;}
		     	 $gpRX+=$adjustX;
		      if($chartType  == 'linear' || $chartType  == 'linearW2' ||  $chartType  == 'radar') 
			   {
		  /* --- insert label with measure unit --*/
		      	$gpTlabel=$A_Ydata[$i]." ".$unitField;
		      	$gpTCenterX=round(($pointRectWidth/2), 2);  
		      	$gpTCenterY=round((($pointRectHeight + 3)/3), 2);   // assuming the text police height equals the css definition : .graphPointT
		      	$points.="		<text id='gp".$j."T' x='".$infoPointX."' y='".intval($infoPointY-10.1)."' class='graphPointT'   style='fill:red; font-size:".$graphPointT."em;' >".$A_Ydata[$i]." ".$unitField."</text>\n";
		      	$gpTCenterX=round(($pointRectWidth/2), 2);  

		      	$classPointTs='graphPointTs';
		      	($chartType  == 'linear'|| $chartType=='linearLT'|| $chartType  == 'linearW2') ? $e=$A_Xdata_label[$i] : $e=date('d M Y - H:i:s', $A_wdt[$i]) ;
			    $A_lastMeasure=$e;   									// save last measure date 
		      	$points.="		<text id='gp".$j."Ts' x='".$infoPointX."' y='".intval($infoPointY)."'   class='".$classPointTs."' style='fill:black; font-size:".$graphPointTs."em;' >".$e."</text>\n";
		       } // -- end of $chartType  == 'linear' || $chartType  == 'radar' 
			  ($chartType =='bar')? $v='polygon': $v='points'; 		      $$v.="	</g>\n";
		 } // -- end of $mAnalysis==FALSE
	 } // -- end of for($i=0; $i<count($A_XGraphCoord); $i++) ------------------------------
	 unset($A_Tdata,$A_YGraphBarHeight,$A_YGraphCoord);				//-- free memory ======= 
	//-- create ending  polygon point
	if($chartType  == 'linear' || $chartType  == 'linearLT') {
			if($Ymin<0)  	{$polygon.=	$A_XGraphCoord[$i-1]+$C_linearGraphMargin.",".($C_linearSvgSizeY- $Yzero*$YtailorRatio -$Yshift );}
			   else 		{$polygon.=	$A_XGraphCoord[$i-1]+$C_linearGraphMargin.",".($C_linearSvgSizeY );}
	}
	if($chartType  == 'linear' || $chartType  == 'linearLT' || $chartType=='radar') {  
		$polygon.=	"'  fill='url(#vertical)' style='stroke:rgba(48,63,159, 0.7);stroke-width:1.8px;'  /> \n"; 
		}
	if($chartType  == 'linearW2') { 	
		$polygon.=	"'  style='fill:none ;stroke:rgba(48,63,159, 1.0);stroke-width:2.2px;'  /> \n";   
		}
	//$polygon.=	"z'  style='fill:rgba(48,63,159, 0.50);stroke:rgba(48,63,159, 0.7);stroke-width:0.8px;'  /> \n";
	 if($chartType=='linearW2')  {   
		$webPage->writeSvgGraph($svgFile, $polygon);
		$webPage->writeSvgGraph($svgFile, $points);
	  }
	 else
	  { 
		echo $polygon;					// write polygon data
	 	$l .="	</g>\n"; 
		echo $l;						// write X label data
		echo $points;					// write graphic points definition
	  }
 //} //-- end of $chartType=='linear' || $chartType=='linearLT' || $chartType=='linearW2' || $chartType=='bar' )
//
// ================================== graph information =========================================================
//----------  measure interval -------------------------
$measureError? $errorColor="orange" : $errorColor="black";
if($chartType  == 'linear' || $chartType  == 'linearLT' || $chartType  == 'bar' ) {
     $ratioT=1.3;
     $s="	<!-- graphic information -->\n";
     $t=" ".$webPage->textTranslate(11,$webPage->textElements)." ".$fromDate." ".$webPage->textTranslate(12,$webPage->textElements)." ".$toDate;
     $s.="	<text x='".intval($C_linearGraphMargin+($C_linearSvgSizeX-strlen($t))/2)."' y='". intval($infoTextMaxY-30.0). "' style='fill:black;font-size:" .intval($infoTextSize/$ratioT). "px;font-weight:normal;text-anchor: middle'> " .$t. "</text>\n";
     $t= "".$webPage->textTranslate(8,$webPage->textElements)." " .$S_measureInterval;
     $s.="	<text x='".intval($C_linearGraphMargin+($C_linearSvgSizeX-strlen($t))/2)."' y='". intval($infoTextMaxY-15.0). "' style='fill:".$errorColor.";font-size:" .intval($infoTextSize/$ratioT). "px;font-weight:normal;text-anchor: middle'> " .$t. "</text>\n";
     echo $s;
}
 if($chartType=='linearW2')  {
	$Ylmp=intval($C_linearSvgSizeY+ ($Yzero+$Yshift)/$YtailorRatio);
	$YlmpTextSize=8;
 	$s ="	<text x='110' y='".intval($Ylmp+$YlmpTextSize*1.5)."' style='fill:".$errorColor.";font-size:".$YlmpTextSize."px;font-weight:normal;text-anchor: ".$textAnchor."' >".$webPage->textTranslate(8,$webPage->textElements)." : ".$S_measureInterval."</text>\n";
    $webPage->writeSvgGraph($svgFile, $s); 
 	$s ="	<text x='310' y='".intval($Ylmp+$YlmpTextSize*1.5)."' style='fill:black;font-size:".$YlmpTextSize."px;font-weight:normal;text-anchor: ".$textAnchor."' >".$webPage->textTranslate(9,$webPage->textElements)." : ".$A_lastMeasure."</text>\n";
    $webPage->writeSvgGraph($svgFile, $s); 
    $s="	<text x='0' y='".intval($Ylmp+$YlmpTextSize*1.5)."' style='fill:black;font-size:".intval($YlmpTextSize*1.25)."px;font-weight:bold;text-anchor: start' >" .$A_unitFields[$areaToProcess]."</text>\n";  
    $webPage->writeSvgGraph($svgFile, $s);
 }
unset($points,$l,$s,$t,$polygon);
 SvgEnd($chartType,$webPage,$C_linearSvgSizeX,$C_linearSvgSizeY,$Xzero,$Yzero,$C_linearGraphMargin,$XtailorRatio,$YtailorRatio,$Yshift,$Xshift,$svgFile);
}  //-- end of SvgChart
