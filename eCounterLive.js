//eCounterLive.js
//$codeVersion=1;
//$codeRelease='4.0';
//PHP Version 5.5.9
//eCounter  backend
//olivier rivaille
//10NOV17
//orivaille@free.fr
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
//-----------------------------------------------------------------------------------------------
//  html element = xxxxxB  B is the code of the graph
    function refresh(b,device)
	{
//--- ATTENTION --- TEST ---  DEVICE DANS CODE FILE !!!!!! 
		dynGraph="DEVICES/"+device+"/g"+b+".svg";
		htmlDiv="dynGraph"+b;
	//alert("<br/> resresh for - "+ htmlDiv);
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
	//alert("<br/> 200 - "+ dynGraph)
			   xml = xhttp.responseText;
			   reloadGraph(xml,b);
			}
		};
	xhttp.open("GET", dynGraph, false);
	xhttp.send();
	}
    function reloadGraph(xml,b)  
	{ 
        //Remove all child of the div
		dynArea="dynGraph"+b; 
	//alert("<br/> reloadGraph called for - "+dynArea);
        el =  document.getElementById(dynArea);
        if ( el.hasChildNodes() )
        {
            while ( el.childNodes.length >= 1 )
            {
                el.removeChild( el.firstChild );       
            } 
        }
        //Send the text to rebuild the dynGraph
        builddynGraph(xml,b);
	}

    function builddynGraph(xml,b)
    {
        dynGraph="dynGraph"+b;
        el =  document.getElementById(dynGraph);
		el.innerHTML = xml;
    }
