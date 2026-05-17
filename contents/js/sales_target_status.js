RE_NUMBER   = new RegExp(/^[0-9]+$/);
RE_EMAIL    = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);
RE_NAME     = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);

var httpLoadGroupCatagory     = getHTTPObject();
var httpLoadArea      	= getHTTPObject();

var isIE            = document.all;

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

function getGroupProductCatagoryList(group_id){	
	  if(group_id !="") { 	
		  httpLoadGroupCatagory.open("GET", "index.php?app=sales_target&cmd=loadGroupCatagory&group_id="+group_id, true);
		  httpLoadGroupCatagory.onreadystatechange = handleLoadResponse;
		  httpLoadGroupCatagory.send(null);
	  }
}

function handleLoadResponse()
{
    if(httpLoadGroupCatagory.readyState == 4){       
       //alert(httpLoadGroupCatagory.responseText);
       processData(httpLoadGroupCatagory.responseText);
       //alert(httpLoadGroupCatagory.responseText);
    }    

}

function processData(ResponseStr)
{
		//alert(ResponseStr);
		productOption = document.getElementById('catagory');
		while(productOption.length>0)
		{
			productOption.remove(0);
		}		

		var arrProduct = Array();
		var product_details="";		
		arrProduct = ResponseStr.split("@@@");
		productOption.options[0]= new Option("Select One","0");
		for( i=0;i<arrProduct.length-1; i++)
		{
		  var arrProductIdName = Array();
		  arrProductIdName  = arrProduct[i].split("#####");	  
		  
		  productOption.options[i+1]= new Option(arrProductIdName[1], arrProductIdName[0]);
		}	
			
}

function getAreaList(district){ 
	  if(district!="") { 	
		  httpLoadArea.open("GET","index.php?app=customer&cmd=loadArea&district="+district, true);
		  httpLoadArea.onreadystatechange = handleLoadTRTResponse;
		  httpLoadArea.send(null);
	  }
}

function handleLoadTRTResponse()
{
    if(httpLoadArea.readyState == 4){       
       //alert(httpLoadArea.responseText);
       processTRTData(httpLoadArea.responseText);
       //alert(httpLoadArea.responseText);
    }   
}

function processTRTData(ResponseStr)
{
		//alert(ResponseStr);
		areaOption = document.getElementById('area');
		while(areaOption.length>0)
		{
			areaOption.remove(0);
		}		
		var arrArea = Array();
		arrArea = ResponseStr.split("@@@");
		areaOption.options[0]= new Option("Select One","");
		for( i=0;i<arrArea.length-1; i++)
		{
		  var arrAreaIdName = Array();
		  arrAreaIdName =	arrArea[i].split("#####");
		  areaOption.options[i+1]= new Option(arrAreaIdName[1],arrAreaIdName[0]);
		}	
}

function addCol(html) 
{
 var td=document.createElement('td');
 td.setAttribute('bgColor',"#D9D6BF")
 td.innerHTML=html;
 //alert("addCol"+html);
 return td;
}

function addColMSG(html) 
{
 var td=document.createElement('td');
 td.setAttribute('bgColor',"#FFCC66")
 td.innerHTML=html;
 //alert("addCol"+html);
 return td;
}

function addColHead(html) 
{
 td=document.createElement('td');
 fnt = document.createElement('font');
 fnt.setAttribute('color',"#FFFFFF");
 td.setAttribute('bgColor',"#6699FF")
 td.appendChild(fnt);
 td.innerHTML=html;
 //alert(td);
 return td;
}

function remRows(elem)
{
	obj = document.getElementById(elem);
	//alert(obj);
	obj.innerHTML = "";
}

//********** End ***********

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