RE_NUMBER   = new RegExp(/^[0-9]+$/);
RE_EMAIL    = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);
RE_NAME     = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);

var httpLoadProduct     = getHTTPObject();
var httpLoadCatProduct	= getHTTPObject();
var httpLoadCatagory	= getHTTPObject();
var httpLoadSubCatagory = getHTTPObject();
var httpSaveProduct 	= getHTTPObject();
var httpSaveCatagory 	= getHTTPObject();
var httpLoadGride 	= getHTTPObject();
var httpLoadCSTGride	= getHTTPObject();
var httpCopyST 		= getHTTPObject();
var httpLoadArea      	= getHTTPObject();
var httpLoadDistrict    = getHTTPObject();

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
function checkUnit(val){	
	 document.getElementById("qty_m").innerHTML = " ("+val+")";
	 document.getElementById("uprice_m").innerHTML =" (per "+val+")";
}

function checkTotalUnit(val){	
	 document.getElementById("totalqty_m").innerHTML = " ("+val+")";	
	 document.getElementById("totalqty_m1").innerHTML = " ("+val+")";
}
//===== Get Sub Category ======

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
//===== Start Product By Category ======
function getCatProductList(catagory_id){ 
	  if(catagory_id!="") { 	
		  httpLoadCatProduct.open("GET", "index.php?app=sales.order&cmd=loadcatProduct&catagory_id="+catagory_id, true);
		  httpLoadCatProduct.onreadystatechange = handleLoadProductResponse;
		  httpLoadCatProduct.send(null);
	  }
}

function handleLoadProductResponse()
{
    if(httpLoadCatProduct.readyState == 4){ 
	$('#product').html(httpLoadCatProduct.responseText);	
	$('#product').trigger('chosen:updated');
	$('#product').trigger('chosen:hiding_dropdown');
       //alert(httpLoadCatProduct.responseText);
    }    

}
//===== End Product By Category ======

function getDistrictList(division_id){ 
	  if(division_id!="") { 	
		  httpLoadDistrict.open("GET","index.php?app=customer&cmd=loadDistrict&division_id="+division_id, true);
		  httpLoadDistrict.onreadystatechange = handleDistrictResponse;
		  httpLoadDistrict.send(null);
	  }
}

function handleDistrictResponse()
{
    if(httpLoadDistrict.readyState == 4){       
       //alert(httpLoadDistrict.responseText);
       processDistrictData(httpLoadDistrict.responseText);
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

function getCatSalesTargetGride(){
	var catagory 			= document.getElementById('catagory').value;
	var date_from 			= document.getElementById('date_from').value;
	var date_to 			= document.getElementById('date_to').value;		
	if(date_from !="" && date_to!="" ) { 	

	  httpLoadCSTGride.open("GET", "index.php?app=sales_target&cmd=loadCST&catagory="+catagory+"&date_from="+date_from+"&date_to="+date_to, true);
	  httpLoadCSTGride.onreadystatechange = handleLoadCSTGrideResponse;
	  httpLoadCSTGride.send(null);
	}
}

function handleLoadCSTGrideResponse()
{
    if(httpLoadCSTGride.readyState == 4){       
       		var tbl = trim(httpLoadCSTGride.responseText);
		document.getElementById('tbs').innerHTML = tbl;
		document.getElementById('catagory').focus();
    }    

}
function copySalesTarget(){		
	var date_from 			= document.getElementById('date_from').value;
	var date_to 			= document.getElementById('date_to').value;	
	var copy_date_from 		= document.getElementById('copy_date_from').value;
	var copy_date_to 		= document.getElementById('copy_date_to').value;		
	if(date_from !="" && date_to!="" && copy_date_from !="" && copy_date_to!="") { 	
	  httpCopyST.open("GET", "index.php?app=sales_target&cmd=copySalesTarget&date_from="+date_from+"&date_to="+date_to+"&copy_date_from="+copy_date_from+"&copy_date_to="+copy_date_to, true);
	  httpCopyST.onreadystatechange = handleCopyGrideResponse;
	  httpCopyST.send(null);
	}
}

function handleCopyGrideResponse()
{
    if(httpCopyST.readyState == 4){       
       var tbl 		= trim(httpCopyST.responseText);
		document.getElementById('tbs').innerHTML = tbl;
		document.getElementById('catagory').focus();
		alert("Successfully Copy Sales Target.");
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


function salesTarget()
{
	if (includeGrid())
	{				
		prepareTargetTblGrid();
		
	}
}

function includeGrid()
{
	var frm = document.frmbuyerorder;
	formSetup(frm);
	if(validateForm(frm))
	{
		alert(MISSING_REQUIRED_FIELDS);
		return false;
	}else{
		return true;
	}
	
}

function formSetup(frm)
{
	with(frm)
	{		
		setRequiredField(division_id,	'dropdown',  'division_lbl');	
		setRequiredField(district,	'dropdown',  'district_lbl');	
		setRequiredField(area,		'dropdown',  'trt_lbl');
		setRequiredField(date_from,     'textbox',  'targetfrm_lbl');	
		setRequiredField(date_to,       'textbox',  'targetto_lbl');	
		setRequiredField(catagory,	'dropdown',  'catagory_lbl');	
		setRequiredField(target_qty,    'textbox',  'target_qty_lbl');
	}
}

function prepareTargetTblGrid()
{	
	
	var date_from 			= document.getElementById('date_from').value;
	var date_to 			= document.getElementById('date_to').value;
	var division_id 		= document.getElementById('division_id').value;			
	var districtId 			= document.getElementById('district').value;		
	var areaId 			= document.getElementById('area').value;		
	var catagory 			= document.getElementById('catagory').value;		
	var product 			= document.getElementById('product').value;		
	var target_qty 			= document.getElementById('target_qty').value;
	//alert(productid);	
	if((division_id !="" && districtId !="" && date_from !="" && date_to !="") && (catagory !="")){ 	
	 httpSaveCatagory.open("GET","index.php?app=sales_target&cmd=save_cst&division_id="+division_id+"&date_from="+date_from+"&date_to="+date_to+"&districtid="+districtId+"&areaid="+areaId+"&catagory="+catagory+"&product="+product+"&target_qty="+target_qty, true);
	 httpSaveCatagory.onreadystatechange = handleSaveCatagoryResponse;
	 httpSaveCatagory.send(null);
	}
  		  
} // End of function prepareTblGrid()

function handleSaveCatagoryResponse()
{
    if(httpSaveCatagory.readyState == 4)
    {    
		//alert(httpSaveCatagory.responseText); 
        var tbl = trim(httpSaveCatagory.responseText);
	document.getElementById('tbs').innerHTML = tbl;
	document.getElementById('catagory').focus();
		
    } 
}

var rsArr = Array();
function rowFound(idx)
{
	var psh = false;
	var arrlen = rsArr.length;
	if(rsArr.length == 0)
	{		
		rsArr.push(document.getElementById('catagory_product_id[]').value);
	}
	else
	{
		for(var i=0; i <= arrlen-1; i++)
		{
			if(rsArr[i] == idx)
			{
				alert ("You have already chosen!!"); psh = false;break;
			}else{
				rsArr.push(idx);
				psh = true;
			}
		}
	}
	return psh;	
} //End of rowFound()


// ===== end Expected Salary ==========

function deleteRecord(id)
{	
   var url_loc = "index.php?app=purchase_order&cmd=delete&id="+id;
   window.location = url_loc;
}
function checkBalance(){
	if(document.getElementById('transaction_type1').checked){
		//var transaction_type =document.getElementById('transaction_type1').value;
		var credit = parseFloat(document.getElementById('Payment_f').value);
		var balance = parseFloat(document.getElementById('balance').value);
		if(balance<credit){
			alert("You can not complete this transaction \nbecause credit amount("+credit+") is greter then Account balance("+balance+")");
			return false;
		}else{
			return true;
		}
	}else{
		return true;
	}	
	
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
