<?php
//
//PHP Version 5.5.9
//eCounter  backend
//olivier rivaille
//04NOV17
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
//-----------------------------------------------------------------
//- GET DATA FROM config.xml file
//-----------------------------------------------------------------
// argument #1  : xml file path
// argument #2  : fields to be extracted from config.xml
// argument #3  : area for result : Array of simple variable.
//
 function getConfigdata2( $xmlConfig, $field,&$Array)  
{
 // echo "<hr/> -00-".$field." ----\n";
 $dom = new DomDocument();
 if(file_exists($xmlConfig))
 {
	 $dom = new DomDocument();	
	 $dom->load($xmlConfig);
	 $liste = $dom->getElementsByTagName($field);
	 $i=0;
	  foreach($liste as $item)
	  {
		if($item->firstChild)   	
		{ $fv=$item->firstChild->nodeValue;}  else { $fv= FALSE;}
	   //echo "<br/> -getConfigdata2()".$i."-".$item->firstChild->nodeValue  ;
	   if (is_array($Array)) {$Array[$i] =$fv; }
		else {$Array=$fv; return $fv;}	
		$i++;
	  }
	  unset($dom);
	  return $Array;
  }
 else {return FALSE;}
}
//-----------------------------------------------------------------
//- GET DATA FROM config.xml file
//-----------------------------------------------------------------
// argument #1  : xml file path
// argument #2  : fiels to beextracted from config.xml
// argument #3  : area for result : Array or    simple variable.
//
 function getConfigdata( $xmlConfig, $field,&$Array)  {
 if(file_exists($xmlConfig))
 {
  $c=0; 
  $dom = new DomDocument();
  $dom->load($xmlConfig);
  $liste = $dom->getElementsByTagName($field);
  foreach($liste as $item)
  {
	if(is_object($liste)) { $fv=$item->firstChild->nodeValue;}
	if(is_array($liste)) { $fv=$liste[0] ;}
 	if(is_string($liste)) { $fv=$liste ;}
	//echo "<br/> -getConfigData-".$item->firstChild->nodeValue  ;
	if(is_array($Array)){$Array[intval($fv)]=TRUE;}
	else {$Array = $fv;}
	$c++;
  unset($dom);	
   }
 }
 else {return FALSE;}
}
//
//-----------------------------------------------------------------
//- SET DATA TO config.xml file
//-----------------------------------------------------------------
// argument #1  : xml file path
// argument #2  : fiels to beextracted from config.xml
// argument #3  : area for result : Array of simple variable.
//
 function setConfigdata( $xmlConfig, $field,$value)  { 
  //echo "<br/> setConfigdata -- field=".$field." -value=".$value." \n"; 
  //$smlfile=file_get_contents($xmlConfig);
  $config = simplexml_load_file($xmlConfig);
	//echo"<br>  config before: ";echo $config->asXML();
	$config->$field = $value;
	//echo"<br> config after :";echo $config->asXML();
	if($config->asXML($xmlConfig)) {return TRUE;}
	 else {return FALSE;}
}

//-----------------------------------------------------------------

function myPrintHexa($A,$d,$t)
{
 // FALSE : A  array values hexa, d string data description, t=FALSE 
 // TRUE  : A array values hexa with data description , t=TRUE
 for($k=0;$k<count($A); $k++)
	{
	if($t==FALSE) { if($k==0){echo"<hr/>-- " .$d." --";} echo " " .sprintf("%02x", $A[$k]);}
    if($t==TRUE)  
		{
		 if($k==0) {echo"<hr/><table><tr>\n"; for($n=0;$n<count($d);$n++) {echo"<td>".$d[$n]."</td>";} echo"</tr>\n<tr>"; }
		 echo"<td>".sprintf("%02x", $A[$k])."</td>"; 
		 if($k==(count($A)-1)) {echo"</tr></table>\n";}
		}
	}
 echo"\n <hr/> \n" ;
}
//-----------------------------------------------------------------
//-----------------------------------------------------------------
function myPrintHexa2($A,$d,$t)
{
 // FALSE : A  array values hexa, d string data description, t=FALSE 
 // TRUE  : A array values hexa with data description , t=TRUE
 for($k=0;$k<count($A); $k++)
	{
	if($t==FALSE) { if($k==0){echo"<hr/>-- " .$d." --";} echo " " .$A[$k];}
    if($t==TRUE)  
		{
		 if($k==0) {echo"<hr/><table><tr>\n"; for($n=0;$n<count($d);$n++) {echo"<td>".$d[$n]."</td>";} echo"</tr>\n<tr>"; }
		 echo"<td>". $A[$k]."</td>"; 
		 if($k==(count($A)-1)) {echo"</tr></table>\n";}
		}
	}
 echo"\n <hr/> \n" ;
}
//-----------------------------------------------------------------
function myPrintBin($A,$t)
{
 echo"<br/>".sprintf("%08b", $A)." ".$t."\n"; 
}
//-----------------------------------------------------------------
?>
