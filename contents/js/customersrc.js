RE_NUMBER           = new RegExp(/^[0-9]+$/);

RE_EMAIL    = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);

RE_NAME     = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);
var httpLoadArea      	= getHTTPObject();
var httpLoadDistrict    = getHTTPObject();
var httpLoadUP 			= getHTTPObject();
var isIE            	= document.all;

var rsFound = true;
var needSave = false;

function getHTTPObject()
{

  var xmlhttp;
  if (!xmlhttp )
  {

    if(window.XMLHttpRequest) 
    {
    	try {
			      xmlhttp = new XMLHttpRequest();
          } 
          catch(e) {
			               xmlhttp = false;
                   }
     }
     else if(window.ActiveXObject)
     {
       	try {
        	    xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
      	    }
            catch(E) {
				 try {
					   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
					 } catch(e) {
								  xmlhttp = false;
								}
					 }
     }
 

  }  
 
  return xmlhttp;

}
function checkUnit(val){	
	 document.getElementById("qty_m").innerHTML = " ("+val+")";
	 document.getElementById("uprice_m").innerHTML =" (per "+val+")";
}

function getSrcTRTList(district){ 
	  if(district!="") { 	
		  httpLoadDistrict.open("GET","index.php?app=customer&cmd=loadArea&district="+district, true);
		  httpLoadDistrict.onreadystatechange = handleLoadSrcTRTResponse;
		  httpLoadDistrict.send(null);
	  }
}

function handleLoadSrcTRTResponse()
{
    if(httpLoadDistrict.readyState == 4){       
       //alert(httpLoadDistrict.responseText);
       processSrcTRTData(httpLoadDistrict.responseText);
       //alert(httpLoadDistrict.responseText);
    }   
}

function processSrcTRTData(ResponseStr)
{
		//alert(ResponseStr);
		productOption = document.getElementById('trt');
		while(productOption.length>0)
		{
			productOption.remove(0);
		}		
		var arrProduct = Array();
		arrProduct = ResponseStr.split("@@@");
		productOption.options[0]= new Option("Select One","");
		for( i=0;i<arrProduct.length-1; i++)
		{
		  var arrProductIdName = Array();
		  arrProductIdName =	arrProduct[i].split("#####");
		  productOption.options[i+1]= new Option(arrProductIdName[1],arrProductIdName[0]);
		}	
}
//****** Get District *******
function getSrcAreaList(division_id){ 
	  if(division_id!="") { 	
		  httpLoadArea.open("GET","index.php?app=customer&cmd=loadDistrict&division_id="+division_id, true);
		  httpLoadArea.onreadystatechange = handleSrcAreaResponse;
		  httpLoadArea.send(null);
	  }
}

function handleSrcAreaResponse()
{
    if(httpLoadArea.readyState == 4){       
       //alert(httpLoadDistrict.responseText);
       processSrcAreaData(httpLoadArea.responseText);
       //alert(httpLoadDistrict.responseText);
    }   
}

function processSrcAreaData(ResponseStr)
{
		//alert(ResponseStr);
		districtOption = document.getElementById('areaid');
		while(districtOption.length>0)
		{
			districtOption.remove(0);
		}		
		var arrDistrict = Array();
		arrDistrict = ResponseStr.split("@@@");
		districtOption.options[0]= new Option("Select One","");
		for( i=0;i<arrDistrict.length-1; i++)
		{
		  var arrDistrictIdName = Array();
		  arrDistrictIdName =	arrDistrict[i].split("#####");
		  districtOption.options[i+1]= new Option(arrDistrictIdName[1],arrDistrictIdName[0]);
		}	
}


//=============== Start validation ============


function Trim(TRIM_VALUE){

if(TRIM_VALUE.length < 1){

return"";

}

TRIM_VALUE = RTrim(TRIM_VALUE);

TRIM_VALUE = LTrim(TRIM_VALUE);

if(TRIM_VALUE==""){



return "";

}



else{

return TRIM_VALUE;

}

} //End Function



function RTrim(VALUE){

var w_space = String.fromCharCode(32);

var v_length = VALUE.length;

var strTemp = "";

if(v_length < 0){

return"";

}

var iTemp = v_length -1;



while(iTemp > -1){

if(VALUE.charAt(iTemp) == w_space){

}

else{

strTemp = VALUE.substring(0,iTemp +1);

break;

}

iTemp = iTemp-1;



} //End While

return strTemp;



} //End Function



function LTrim(VALUE){

var w_space = String.fromCharCode(32);

if(v_length < 1){

return"";

}

var v_length = VALUE.length;

var strTemp = "";



var iTemp = 0;



while(iTemp < v_length){

if(VALUE.charAt(iTemp) == w_space){

}

else{

strTemp = VALUE.substring(iTemp,v_length);

break;

}

iTemp = iTemp + 1;

} //End While

return strTemp;

} //End Function