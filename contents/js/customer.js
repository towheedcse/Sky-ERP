RE_NUMBER           = new RegExp(/^[0-9]+$/);

RE_EMAIL    = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);

RE_NAME     = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);
var httpLoadArea      	= getHTTPObject();
var httpLoadDistrict    = getHTTPObject();
var httpLoadUP 		= getHTTPObject();
var httpLoadCatagory = getHTTPObject();
var httpLoadSubCatagory = getHTTPObject();
var httpLoadProduct 	= getHTTPObject();
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
function getProductList(catagory_id){ 
	  if(catagory_id!="") { 	
		  httpLoadProduct.open("GET", "index.php?app=sales.order&cmd=loadcatProduct&catagory_id="+catagory_id, true);
		  httpLoadProduct.onreadystatechange = handleLoadProResponse;
		  httpLoadProduct.send(null);
	  }
}

function handleLoadProResponse()
{
    if(httpLoadProduct.readyState == 4){       
        //alert(httpLoadProduct.responseText);
        $('#product').empty();        
        newProductOption = httpLoadProduct.responseText; 
        $('#product').append(newProductOption);
        $('#product').trigger("chosen:updated");
        //alert(httpLoadProduct.responseText);
    }    

}

function getAreaList(district){ 
	  if(district!="") { 	
		  httpLoadDistrict.open("GET","index.php?app=customer&cmd=loadArea&district="+district, true);
		  httpLoadDistrict.onreadystatechange = handleLoadResponse;
		  httpLoadDistrict.send(null);
	  }
}

function handleLoadResponse()
{
    if(httpLoadDistrict.readyState == 4){       
       //alert(httpLoadDistrict.responseText);
       processData(httpLoadDistrict.responseText);
       //alert(httpLoadDistrict.responseText);
    }   
}

function processData(ResponseStr)
{
		//alert(ResponseStr);
		productOption = document.getElementById('area');
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
function getDistrictList(division_id){ 
	  if(division_id!="") { 	
		  httpLoadArea.open("GET","index.php?app=customer&cmd=loadDistrict&division_id="+division_id, true);
		  httpLoadArea.onreadystatechange = handleDistrictResponse;
		  httpLoadArea.send(null);
	  }
}

function handleDistrictResponse()
{
    if(httpLoadArea.readyState == 4){       
       //alert(httpLoadDistrict.responseText);
       processDistrictData(httpLoadArea.responseText);
       //alert(httpLoadDistrict.responseText);
    }   
}

function processDistrictData(ResponseStr)
{
		//alert(ResponseStr);
		districtOption = document.getElementById('district');
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

//===== Get Sub Category ======

function getCatagoryList(catagory_id){ 
	  httpLoadCatagory.open("GET", "index.php?app=product&cmd=loadCatagory&catagory_id="+catagory_id, true);
	  httpLoadCatagory.onreadystatechange = handleLoadCatagoryResponse;
	  httpLoadCatagory.send(null);
}

function handleLoadCatagoryResponse()
{
    if(httpLoadCatagory.readyState == 4){       
       //alert(httpLoadCatagory.responseText);
       processCatagoryData(httpLoadCatagory.responseText);
    }    
}

function processCatagoryData(ResponseStr)
{
		var CatagoryOption = document.getElementById('catagory');
		while(CatagoryOption.length>0){ CatagoryOption.remove(0); }	
			
		var arrCatagory = Array(); arrCatagory = ResponseStr.split("@@@");
		CatagoryOption.options[0]= new Option("Select Catagory","");
		for( i=0;i<arrCatagory.length-1; i++)
		{
		  var arrCatagoryIdName 	= Array();  arrCatagoryIdName = arrCatagory[i].split("#####");
		  CatagoryOption.options[i+1]= new Option(arrCatagoryIdName[1],arrCatagoryIdName[0]);
		}
}


function getSubCatagoryList(catagory_id){ 
	  httpLoadSubCatagory.open("GET", "index.php?app=product&cmd=loadSubCatagory&catagory_id="+catagory_id, true);
	  httpLoadSubCatagory.onreadystatechange = handleLoadSubCatagoryResponse;
	  httpLoadSubCatagory.send(null);
}

function handleLoadSubCatagoryResponse()
{
    if(httpLoadSubCatagory.readyState == 4){       
       //alert(httpLoadSubCatagory.responseText);
       processSubCatagoryData(httpLoadSubCatagory.responseText);
    }    
}

function processSubCatagoryData(ResponseStr)
{
		SubCatagoryOption = document.getElementById('subcatagory');
		while(SubCatagoryOption.length>0){ SubCatagoryOption.remove(0); }	
			
		var arrSubCatagory = Array(); arrSubCatagory = ResponseStr.split("@@@");
		SubCatagoryOption.options[0]= new Option("Select Sub Catagory","");
		for( i=0;i<arrSubCatagory.length-1; i++)
		{
		  var arrSubCatagoryIdName 	= Array();  arrSubCatagoryIdName = arrSubCatagory[i].split("#####");
		  SubCatagoryOption.options[i+1]= new Option(arrSubCatagoryIdName[1],arrSubCatagoryIdName[0]);
		}
			
		//$('#subcatagory').trigger('chosen:updated');
}

//********* End get Product List ************
function getProductDtl(product_id)
{ 
	  product_idArr 	= product_id.split("###");
	  voucher_no 		= product_idArr[0];
	  product_id 		= product_idArr[1];
	  if(product_id!="")
	  {
		  getProductSerialList(voucher_no,product_id);
		  httpLoadUP.open("GET", "index.php?app=sales&cmd=get_uprice&product_id="+product_id+"&voucher_no="+voucher_no, true);
		  httpLoadUP.onreadystatechange = handleUPResponse;
		  httpLoadUP.send(null);
	  }
}

function handleUPResponse()
{
    if(httpLoadUP.readyState == 4){       
       //alert(httpLoadUP.responseText);
	   var UPcontent = trim(httpLoadUP.responseText);
	   contentArr	 = UPcontent.split("#####");
	   
	   document.getElementById('stock_qty').value=contentArr[1];
	   document.getElementById('m_unit').value=contentArr[2];
	   var productcatagory=contentArr[3];	   
	   var details = contentArr[5];
	   if(details==""){
		var details = contentArr[6];
	   }
	   if(productcatagory!=0){
		   //document.getElementById('serial').value =contentArr[3];
		   document.getElementById('qty').value=1; document.getElementById('qty').disabled =true;document.getElementById('warranty').value =contentArr[4];
	   }else{
		   //document.getElementById('serial').value =contentArr[3]; 
		   document.getElementById('qty').disabled =false;document.getElementById('warranty').value =contentArr[4];
	   }
	   document.getElementById('details').value=details;
	   
    } 

}
function getProductSerialList(voucher_no,product_id){ 
	  if(product_id!=""){
		  httpLoadPS.open("GET", "index.php?app=sales&cmd=get_serial&product_id="+product_id+"&voucher_no="+voucher_no, true);
		  httpLoadPS.onreadystatechange = handlePSResponse;
		  httpLoadPS.send(null);
	  }
}

function handlePSResponse()
{
    if(httpLoadPS.readyState == 4){       
       //alert(httpLoadDistrict.responseText);
       processSerialData(httpLoadPS.responseText);
       //alert(httpLoadDistrict.responseText);
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
