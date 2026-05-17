RE_NUMBER   = new RegExp(/^[0-9]+$/);
RE_EMAIL    = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);
RE_NAME     = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);
//onClick="if(addInquiry()){saveInquiry();return true;}else{ return false;}
var httpSaveInquiry 		= getHTTPObject();
var httpLoadCatagory		= getHTTPObject();
var httpLoadSubCatagory   	= getHTTPObject();
var httpLoadSrcCatagory   	= getHTTPObject();
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


function getModelList(brand_id){ 
	  httpLoadCatagory.open("GET", "index.php?app=sales.item&cmd=loadModel&brand_id="+brand_id, true);
	  httpLoadCatagory.onreadystatechange = handleLoadModelResponse;
	  httpLoadCatagory.send(null);
}

function handleLoadModelResponse()
{
    if(httpLoadCatagory.readyState == 4){       
       //alert(httpLoadModel.responseText);
       processModelData(httpLoadCatagory.responseText);
    }    
}

function processModelData(ResponseStr)
{
		ModelOption = document.getElementById('model_id');
		while(ModelOption.length>0){ ModelOption.remove(0); }	
			
		var arrModel = Array(); arrModel = ResponseStr.split("@@@");
		ModelOption.options[0]= new Option("Select Brand Model","");
		for( i=0;i<arrModel.length-1; i++)
		{
		  var arrModelIdName 	= Array();  arrModelIdName = arrModel[i].split("#####"); 
		  ModelOption.options[i+1]= new Option(arrModelIdName[1],arrModelIdName[0]);
		}	
		$('#model_id').trigger('chosen:updated');
}
function getSrcModelList(brand_id){ 
	  httpLoadModel.open("GET", "index.php?app=sales.item&cmd=loadModel&brand_id="+brand_id, true);
	  httpLoadModel.onreadystatechange = handleLoadSrcModelResponse;
	  httpLoadModel.send(null);
}

function handleLoadSrcModelResponse()
{
    if(httpLoadModel.readyState == 4){       
       //alert(httpLoadModel.responseText);
       processSrcModelData(httpLoadModel.responseText);
    }    
}

function processSrcModelData(ResponseStr)
{
	ModelOption = document.getElementById('src_model_id');
	while(ModelOption.length>0){ ModelOption.remove(0); }	
		
	var arrModel = Array(); arrModel = ResponseStr.split("@@@");
	ModelOption.options[0]= new Option("Select Brand Model","");
	for( i=0;i<arrModel.length-1; i++)
	{
	  var arrModelIdName 	= Array();  arrModelIdName = arrModel[i].split("#####"); 
	  ModelOption.options[i+1]= new Option(arrModelIdName[1],arrModelIdName[0]);
	}	
	$('#src_model_id').trigger('chosen:updated');
}
//********* End Model ************

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
	CatagoryOption = document.getElementById('catagory');
	while(CatagoryOption.length>0){ CatagoryOption.remove(0); }	
		
	var arrCatagory = Array(); 
	arrCatagory = ResponseStr.split("@@@");
	CatagoryOption.options[0]= new Option("Select Catagory","");
	for( i=0;i<arrCatagory.length-1; i++)
	{
	  var arrCatagoryIdName 	= Array();  
	  arrCatagoryIdName = arrCatagory[i].split("#####");
	  CatagoryOption.options[i+1]= new Option(arrCatagoryIdName[1],arrCatagoryIdName[0]);
	}

    processSubCatagoryData("");
		
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

function getSrcCatagoryList(catagory_id){ 
	  httpLoadSrcCatagory.open("GET", "index.php?app=product&cmd=loadCatagory&catagory_id="+catagory_id, true);
	  httpLoadSrcCatagory.onreadystatechange = handleLoadSrcCatagoryResponse;
	  httpLoadSrcCatagory.send(null);
}

function handleLoadSrcCatagoryResponse()
{
    if(httpLoadSrcCatagory.readyState == 4){       
       //alert(httpLoadModel.responseText);
       processSrcCatagoryData(httpLoadSrcCatagory.responseText);
    }    
}

function processSrcCatagoryData(ResponseStr)
{
	var CatagoryOption = document.getElementById('srccatagory');
	while(CatagoryOption.length>0){ CatagoryOption.remove(0); }	
		
	var arrCatagory = Array(); arrCatagory = ResponseStr.split("@@@");
	CatagoryOption.options[0]= new Option("Select Catagory","");
	for( i=0;i<arrCatagory.length-1; i++)
	{
	  var arrCatagoryIdName 	= Array();  
	  arrCatagoryIdName = arrCatagory[i].split("#####"); 
	  CatagoryOption.options[i+1]= new Option(arrCatagoryIdName[1],arrCatagoryIdName[0]);
	}	
	$('#srccatagory').trigger('chosen:updated');

	processSrcSubCatagoryData("");

}

function getSrcSubCatagoryList(catagory_id){ 
	  httpLoadSubCatagory.open("GET", "index.php?app=product&cmd=loadSubCatagory&catagory_id="+catagory_id, true);
	  httpLoadSubCatagory.onreadystatechange = handleLoadSrcSubCatagoryResponse;
	  httpLoadSubCatagory.send(null);
}

function handleLoadSrcSubCatagoryResponse()
{
    if(httpLoadSubCatagory.readyState == 4){       
       //alert(httpLoadModel.responseText);
       processSrcSubCatagoryData(httpLoadSubCatagory.responseText);
    }    
}

function processSrcSubCatagoryData(ResponseStr)
{
		SubCatagoryOption = document.getElementById('src_subcatagory');
		while(SubCatagoryOption.length>0){ SubCatagoryOption.remove(0); }	
			
		var arrSubCatagory = Array(); arrSubCatagory = ResponseStr.split("@@@");
		SubCatagoryOption.options[0]= new Option("Select Sub Catagory","");
		for( i=0;i<arrSubCatagory.length-1; i++)
		{
		  var arrSubCatagoryIdName 	= Array();  arrSubCatagoryIdName = arrSubCatagory[i].split("#####"); 
		  SubCatagoryOption.options[i+1]= new Option(arrSubCatagoryIdName[1],arrSubCatagoryIdName[0]);
		}	
		$('#src_subcatagory').trigger('chosen:updated');
}

//********* End Catagory ************

function doFormSubmit()
{
   requiredFields.length = 0;
   var errCnt = 0;
   var frm = document.setupfrm;

   // Setup required fields
   formSetup(frm);

   // Validate form for required fields
   errCnt = validateForm(frm);

   if (errCnt)
   {
      alert(MISSING_REQUIRED_FIELDS); 
      return false;
   }
   else
   {
      if(validateFields(frm))
      {
         return true;
      }
      else
         return false;
   }

}

function formSetup(frm)
{
	with(frm)
	{			
		setRequiredField(brand_code,		 'dropdown',  'brand_lbl');
		setRequiredField(main_catagory,           'dropdown',  'main_catagory_lbl');	
		setRequiredField(catagory,           'dropdown',  'catagory_lbl');
		setRequiredField(product_name,       'textbox',   'product_lbl');
	}
}

function fieldValidation(frm)
{
	with(frm)
	{
		if(!RE_DECIMAL.exec(unit_price.value))
		{
			highlightTableColumn('unit_price_lbl');
			alert(ERROR_NUMBER);		
			return false;
		}
		else
		{
			return true;
		}
	}
	return true;
}

function frmSubmit()
{ 
  if(!doFormSubmit())	
  {	
  	   return false;

  } else {		
	var unit_price 	= parseInt(document.getElementById('unit_price').value);
	if(isNaN(unit_price)){ unit_price =0; }
	if(unit_price >0 )
	{ 
		if(confirm("Do You Sure want ot submit???")==true){
			document.setupfrm.submit();
			return true;
		}else{			
			return false;
		}
	}
	else
	{
		alert("Please enter all required data...");
		return false;
	}
  }  
}
//=========== End Personal Info ==============

function deleteRecord(eid)
{	
   var url_loc = "index.php?app=product&cmd=delete&id="+eid;
   window.location = url_loc;

}

//=========== End Top Skills ===========

function cancelStyle()
{
	window.location=CANCEL_URL;
}

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

//===== End =====
