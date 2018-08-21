<?php
//
//MyMeteo   receive Sigfox callback
//olivier rivaille
//15AUG17
//orivaille@free.fr
//PHP Version 5.5.9
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
// ERR1 : cannot open database ( error on open)
// ERR9 : cannot close database ( error on close)
//
class manageDB 
{
 function manageDB($device,$handle, $webPage, $filePath) 
  { 
   	$this->device=$device; 							// Device sending dat
   	$this->handle=$handle;
	$this->filePath=$filePath;						// path to the DB 
	$this->mainDB="DB".$device.".db";				// main DB name
	$this->tmpDB="DB".$device."tmp.db";				// backup DB name
	$this->HWM=9000;								// DB High Water Mark
	$this->webPage=$webPage;
    $this->OK=TRUE;    
  }
//
//
function openDB()
 {
	$f=$this->filePath.$this->mainDB;
 	if(file_exists($f))
 	{
		//--  open  Device database -----------------------
		//-- retruns  array  [0] = return code, [1} data or Error code  
		 $this->handle = fopen ($f, "r");
	 	 if($this->handle == FALSE) {return array(FALSE, "ERR1");}  	// cannot open database  return ERR1 
		 else {return array(TRUE,$this->handle);}
	}
	else {
		return array(FALSE, "ERR1");
	 }
 } // -- end of openDB method
//
function closeDB()
 {
	 $r = fclose ($this->handle);
 	 if($r == FALSE) {return array(FALSE, "ERR9");}  	// cannot open database  return ERR9
	 else {return array(TRUE,$r);}
 } // -- end of openDB method
//
function getNextRecord()
 {
  //-- get next database record : manages the switch to next file if occurs.
  //- retruns  array  [0] = return code  FALSE when EOF  ,
  // ERR1 when cannot open next file
  //  
  //echo"<br/> getNextRecord()=".$this->handle."\n";
  if( !feof($this->handle))
  {
     $l = fgets($this->handle);
	 if($l != FALSE)
     { 
	  	$r=explode(" ", $l);
		//echo"<br/> record=".$l."\n";
		if(ltrim($r[0],"\'") != "ffffffffff")   				// link to next arch DB ?
		 {
			//echo"<br/>not  ffffffffff record\n";
			return array(TRUE,$l);								// return ret code = TRUE  + data read
		 }					
		  else					
		 {	
			//echo"<br/> ffffffffff read - switching to next DB ".$r[1]."\n";
		  	if(fclose($this->handle))
		  	{  								// open  next archDB name
		   		$a=$this->filePath.trim($r[1],"\n");
				//echo"<br/> switching to next DB ".$a."\n";
				//die;
				//echo"<br/> opening ".$a."\n";
		   		$this->handle = fopen ($a, "r");
		   		if($this->handle)
					{ 	//echo"<br/> ".$a." opened - calling getNextRecord\n"; 
						return $this->getNextRecord();}
			 	 else 
					{	//echo"<br/> ERR1 -cannot open ".$a."\n";	
						return array(FALSE, "ERR1");  	// cannot open database  returne ERR1 
						//die;
					}
		  	}
		     else 
		  	{
				echo"<br/> ERR9 -cannot close current open DB \n";			
				return array(FALSE, "ERR1");
		  	}
		 } // end of else 	// ffffffffff  read 
	  } // end of if($l != FALSE)
  }  // end of not eof
	else {return array(FALSE, "EOF");}
 } // -- end of  getNextRecord()  method

function insertIntoDB($record ) 
{
 //-- insert new record ---------------------------------------------
 $recCount=1;
 $rc=array(FALSE, FALSE);
 $arDB=FALSE;
 $mDB=$this->filePath.$this->mainDB;
 $tmpDB=$this->filePath.$this->tmpDB;
 if(rename( $mDB, $tmpDB)) 				// backup dartabase - remane before to create a new database including the incoming record plus all previous ones creation
 {
  $of=fopen($mDB, 'w'); 
  $if=fopen($tmpDB,"r");
  if($of && $if)
	{
	  //fwrite($of, $_GET['time']." ".$_GET['data']."\n");
	  fwrite($of, $record."\n");			// write  callBack input data  to head of database
	  while (!feof($if)) 
		{
			$ir= fgets($if);
			fwrite($of, $ir);
			$recCount++;
		}
	   fclose($if);  fclose($of);
	   $rc= array(TRUE, $record);	
//
	  if($recCount > $this->HWM)									// limit reached ?
		{
		  $arDB="DB".$this->device."_". date("dmyh").".db";   	// DBxxxxxx__99MMM99.db 
		  $archDB=$this->filePath.$arDB;   						// DBxxxxxx__99MMM99.db 
		  if(rename( $mDB, $archDB))						// switch to a new DB (same name) 
			{
		 	 $of=fopen($mDB, 'w'); 
		  	 //fwrite($of, $_GET['time']." ".$_GET['data']."\n");
		  	 fwrite($of, "ffffffffff"." ".$arDB ."\n");			// write  link : name of the "next database"
		 	 fclose($of);
			 //echo"<br/> new ".$archDB ." created \n";
			 $rc= array(TRUE, "$arDB");
			}
		  else 
			{
			 echo "<br/><b>ERROR</b> cannot rename ". $mDB. " in ".$archDB. " \n"; 
			 error_log("ERROR - ".$this->device." - cannot rename and switch to a new DB ", 0);
			 $rc= array(FALSE, "ERR3");
			}
	 //return array(TRUE, $arDB);
		} // end of $recCount > $this->HWM
	}
	 else 
	{
		echo "<br/><b>ERROR</b> cannot open  ". $mDB. " and/or ".$tmpDB. " \n"; 
		error_log("ERROR - cannot open  ". $mDB. " and/or ".$tmpDB ." ", 0);
		$rc= array(FALSE, "ERR3");
	}
 } // end of if rename
 return $rc;
} // end of insertIntoDB($record )
}  // end of manageDB class   
?>
