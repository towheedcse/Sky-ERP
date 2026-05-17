RE_NUMBER   = new RegExp(/^[0-9]+$/);
RE_EMAIL    = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);

RE_NAME     = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);

var httpLoadProduct 	= getHTTPObject();
var httpLoadBatch   	= getHTTPObject();
var httpSaveProduct 	= getHTTPObject();
var httpDeleteItem	= getHTTPObject();
var httpLoadBatchQty	= getHTTPObject();
var httpLoadBatchInfo	= getHTTPObject();
var httpLoadMachine	= getHTTPObject();
var httpDetailsPM	= getHTTPObject();
var isIE            	= document.all;

var rsFound = true;

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
function GetPMDetails(){
	var machine_no    = document.getElementById('machine_no').value;
	var version_no    = document.getElementById('version_no').value;		
	if(machine_no !="" && version_no!="")
	{
	  	httpDetailsPM.open("GET", "index.php?app=batch.production&cmd=get_po_dtl&production_id="+production_id+"&machine_no="+machine_no+"&version_no="+version_no, true);
		httpDetailsPM.onreadystatechange = handlePMDetailsResponse;
		httpDetailsPM.send(null);
	}
}

function handlePMDetailsResponse()
{
    if(httpDetailsPM.readyState == 4){       
       //alert(httpDetailsPM.responseText); 
	var salesValue 		= trim(httpDetailsPM.responseText);
	var arrSaveOrder    	= salesValue.split("####-@@@@");
	var tbl 		= arrSaveOrder[0];
	var production_id	= arrSaveOrder[1];
 	var total_value 	= parseFloat(arrSaveOrder[2]);
	var in_stock 		= arrSaveOrder[3];
	var out_stock		= arrSaveOrder[4];
	document.getElementById('in_stock').value  = in_stock;
	document.getElementById('out_stock').value = out_stock;
	document.getElementById('production_id').value = production_id;
        document.getElementById('total_value').value = total_value;

	document.getElementById('batch_no').value = "";
        document.getElementById('used_qty').value = 0;
        document.getElementById('wastage').value = 0;

	document.getElementById('finish_product').value = 0;
        document.getElementById('total_used_qty').value = 0;
        document.getElementById('production_status').value = "";
	document.getElementById('tbs').innerHTML = tbl;
	document.getElementById('product').focus();
		
    } 

}

function getProductBatch(product_id){	
	  if(product_id !="") { 	
		  httpLoadBatch.open("GET", "index.php?app=batch.production&cmd=loadProductBatch&product_id="+product_id, true);
		  httpLoadBatch.onreadystatechange = handleLoadResponse;
		  httpLoadBatch.send(null);
	  }
}

function handleLoadResponse()
{
    if(httpLoadBatch.readyState == 4){       
       //alert(httpLoadBatch.responseText);
       processData(httpLoadBatch.responseText);
       //alert(httpLoadBatch.responseText);
    }    

}

function processData(ResponseStr)
{
		//alert(ResponseStr);
		productOption = document.getElementById('batch_no');
		while(productOption.length>0)
		{
			productOption.remove(0);
		}		

		var arrProduct = Array();
		var product_details="";		
		arrProduct = ResponseStr.split("@@@");
		productOption.options[0]= new Option("Select Production Batch","0");
		for( i=0;i<arrProduct.length-1; i++)
		{
		  var arrProductIdName = Array();
		  arrProductIdName  = arrProduct[i].split("#####");
		  var details 	    = arrProductIdName[2];	
		  product_details   = arrProductIdName[1];
		  productOption.options[i+1]= new Option(product_details, arrProductIdName[0]);
		}	
			
}
//********* End get Product List ************
function getBatchDtl(batch_id)
{ 
	  var product = document.getElementById('product').value;
	  var shift   = document.getElementById('version_no').value;
	  //alert(batch_id+product+shift);
	  if(batch_id!="" && shift!="" && product!="")
	  {
		  httpLoadBatchQty.open("GET", "index.php?app=batch.production&cmd=get_dtl&batch_id="+batch_id+"&product="+product+"&shift="+shift, true);
		  httpLoadBatchQty.onreadystatechange = handleBDResponse;
		  httpLoadBatchQty.send(null);
	  }
}

function handleBDResponse()
{
    if(httpLoadBatchQty.readyState == 4){       
       //alert(httpLoadBatchQty.responseText);
	   var used_qty = trim(httpLoadBatchQty.responseText);   
	   document.getElementById('used_qty').value=used_qty;
    } 

}

function getBatchInfo()
{ 
	  GetPMDetails();
	  var product  = document.getElementById('product').value;
	  var batch_id = document.getElementById('batch_no').value;
	  var shift    = document.getElementById('version_no').value;
	  //alert(batch_id+product+shift);
	  if(batch_id!="" && shift!="" && product!="")
	  {
		  httpLoadBatchInfo.open("GET", "index.php?app=batch.production&cmd=get_dtl&batch_id="+batch_id+"&product="+product+"&shift="+shift, true);
		  httpLoadBatchInfo.onreadystatechange = handleBIResponse;
		  httpLoadBatchInfo.send(null);
	  }
}

function handleBIResponse()
{
    if(httpLoadBatchInfo.readyState == 4){       
       //alert(httpLoadBatchQty.responseText);
	   var used_qty = trim(httpLoadBatchInfo.responseText);   
	   document.getElementById('used_qty').value=used_qty;
    } 

}


function getMachineInfo(machine_id)
{ 	  
	  GetPMDetails();
	  if(machine_id !="")
	  {
		  httpLoadMachine.open("GET", "index.php?app=batch.production&cmd=getdaycapacity&machine_id="+machine_id, true);
		  httpLoadMachine.onreadystatechange = handleMachineResponse;
		  httpLoadMachine.send(null);
	  }
}

function handleMachineResponse()
{
    if(httpLoadMachine.readyState == 4){       
       //alert(httpLoadBatchQty.responseText);
	   var daily_capacity = trim(httpLoadMachine.responseText);   
	   document.getElementById('daily_capacity').value=daily_capacity;
    } 

}
function calWastageQty()
{		
	var qty     = parseFloat(document.frmbuyerorder.used_qty.value);
	var wastage = parseFloat(document.frmbuyerorder.wastage.value);	
	   
	if(isNaN(qty)){ qty = 0; } if(isNaN(wastage)){ wastage = 0;}	
	var wastage_qty = ((qty /100) * wastage);
	document.frmbuyerorder.wastage_qty.value = wastage_qty.toFixed(4);	
	calTotalUsedQty();	
}

function calTotalUsedQty(){
	var used_qty 		= parseFloat(document.getElementById('used_qty').value);
	var wastage_qty 	= parseFloat(document.getElementById('wastage_qty').value);
	var daily_capacity 	= parseInt(document.getElementById('daily_capacity').value);
	var finish_product 	= parseFloat(document.getElementById('finish_product').value);
	if(isNaN(used_qty)){ used_qty = 0; } if(isNaN(finish_product)){ finish_product = 0;}
	if(isNaN(wastage_qty)){ wastage_qty = 0;} if(isNaN(daily_capacity)){ daily_capacity = 0;}
	var total_used_qty 	= (((used_qty+wastage_qty)*finish_product));
	
	document.frmbuyerorder.total_used_qty.value = (total_used_qty).toFixed(2);
	if(finish_product > daily_capacity){
	var production_status = (finish_product - daily_capacity);
	document.frmbuyerorder.production_status.value = production_status+" Plus";
	}else if(finish_product < daily_capacity){
	var production_status = (daily_capacity-finish_product);
	document.frmbuyerorder.production_status.value = production_status+" Minus";
	}else if(finish_product == daily_capacity){
	document.frmbuyerorder.production_status.value = "100%";
	}
}
function checkUnit(val){	
	 document.getElementById("qty_m").innerHTML = " ("+val+")";
	 document.getElementById("amount_m").innerHTML =" (per "+val+")";
}


function salesProcess()
{
	if (includeGrid())
	{
		prepareTblGrid(); // prepareGrid();
		return true;
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
	}
	else
	{
		if(fieldValidation(frm))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}

function formSetup(frm)
{
	with(frm)
	{	
		setRequiredField(factory_id,		 'textbox',   'factory_id_lbl');
		setRequiredField(used_date,     	 'textbox',  'used_date_lbl');
		setRequiredField(version_no,		 'dropdown',   'version_no_lbl');
		setRequiredField(machine_no,     	 'dropdown',  'machine_no_lbl');
		setRequiredField(in_stock,    	 	 'dropdown',   'in_stock_lbl');
		setRequiredField(out_stock,    	 	 'dropdown',   'out_stock_lbl');
		setRequiredField(in_stock,    	 	 'dropdown',   'in_stock_lbl');
		setRequiredField(product,          	 'dropdown',  'product_lbl');
		setRequiredField(batch_no,          	 'dropdown',  'batch_lbl');
	}
}
function fieldValidation(frm)
{
	with(frm)
	{
		if(!RE_DECIMAL.exec(finish_product.value))
		{
			highlightTableColumn('qty_lbl');
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

function prepareTblGrid()
{	
	var factory_id 		= document.getElementById('factory_id').value;
	var used_date 		= document.getElementById('used_date').value;
	var version_no 		= document.getElementById('version_no').value;
	var machine_no 		= document.getElementById('machine_no').value;
	var in_stock 		= document.getElementById('in_stock').value;
	var out_stock 		= document.getElementById('out_stock').value;
	var productid 		= document.getElementById('product').value;
	var batch_no 		= document.getElementById('batch_no').value;
	var used_qty 		= parseFloat(document.getElementById('used_qty').value);
	var wastage	 	= parseInt(document.getElementById('wastage').value);
	var wastage_qty 	= parseFloat(document.getElementById('wastage_qty').value);
	var target_qty 		= parseInt(document.getElementById('daily_capacity').value);
	var production_qty 	= parseFloat(document.getElementById('finish_product').value);
	var total_used_qty 	= parseFloat(document.getElementById('total_used_qty').value);
	var production_status 	= document.getElementById('production_status').value;
	var total_value 	= document.getElementById('total_value').value;
	var production_id 	= document.getElementById('production_id').value;	

	
	if((productid!="" && batch_no!="" && production_qty >0)){ 
	httpSaveProduct.open("GET","index.php?app=batch.production&cmd=save_po&factory_id="+factory_id+"&used_date="+used_date+"&version_no="+version_no+"&machine_no="+machine_no+"&in_stock="+in_stock+"&out_stock="+out_stock+"&productid="+productid+"&batch_no="+batch_no+"&used_qty="+used_qty+"&wastage="+wastage+"&wastage_qty="+wastage_qty+"&target_qty="+target_qty+"&production_qty="+production_qty+"&total_used_qty="+total_used_qty+"&production_status="+production_status+"&total_value="+total_value+"&production_id="+production_id, true);
	 httpSaveProduct.onreadystatechange = handleSaveResponse;
	 httpSaveProduct.send(null);
	}
  		  
} // End of function prepareTblGrid()
function handleSaveResponse()
{
    if(httpSaveProduct.readyState == 4)
    {    
	//alert(httpSaveProduct.responseText); 
        var salesValue 		= trim(httpSaveProduct.responseText);
	var arrSaveOrder 	= salesValue.split("####-@@@@");
	var tbl 		= arrSaveOrder[0];
	var production_id	= arrSaveOrder[1];
 	var total_value		= parseFloat(arrSaveOrder[2]);
	if(total_value >0){ needSave = true;}
	document.getElementById('production_id').value = production_id;
        document.getElementById('total_value').value = total_value;

	document.getElementById('batch_no').value = "";
        document.getElementById('used_qty').value = 0;
        document.getElementById('wastage').value = 0;

	document.getElementById('finish_product').value = 0;
        document.getElementById('total_used_qty').value = 0;
        document.getElementById('production_status').value = "";

	document.getElementById('tbs').innerHTML = tbl;
	document.getElementById('product').focus();
    } 
}

function ItemDelete(temp_id){
	var production_id = document.getElementById('production_id').value;
	var machine_no    = document.getElementById('machine_no').value;
	var version_no    = document.getElementById('version_no').value;		
	if(temp_id !="" && production_id)
	{
	  	if(confirm("Are you sure want ot delete???")==true){
			httpDeleteItem.open("GET", "index.php?app=batch.production&cmd=delete&id="+temp_id+"&production_id="+production_id+"&machine_no="+machine_no+"&version_no="+version_no, true);
			httpDeleteItem.onreadystatechange = handleDeleteItemResponse;
			httpDeleteItem.send(null);			
		}
	}
}

function handleDeleteItemResponse()
{
    if(httpDeleteItem.readyState == 4){       
       //alert(httpDeleteItem.responseText); 
	var salesValue 		= trim(httpDeleteItem.responseText);
	var arrSaveOrder    	= salesValue.split("####-@@@@");
	var tbl 		= arrSaveOrder[0];
	var production_id	= arrSaveOrder[1];
 	var total_value = parseFloat(arrSaveOrder[2]);
	if(total_value >0){ needSave = true;}
	document.getElementById('production_id').value = production_id;
        document.getElementById('total_value').value = total_value;

	document.getElementById('batch_no').value = "";
        document.getElementById('used_qty').value = 0;
        document.getElementById('wastage').value = 0;

	document.getElementById('finish_product').value = 0;
        document.getElementById('total_used_qty').value = 0;
        document.getElementById('production_status').value = "";
	document.getElementById('tbs').innerHTML = tbl;
	document.getElementById('product').focus();
		
    } 

}


function clearStockQty(){
	document.getElementById('transfer_qty').value="";
	document.getElementById('stock_qty').value="";
}

function wantToSave()
{
	var production_amount   = parseFloat(document.getElementById('production_amount').value);
	if(production_amount >0)
	{
		var conf = confirm("Sure want ot Print View???");
		
		if(conf==true)
		{ 
			document.frmbuyerorder.submit();
			return true; 
		}else{
			return false;
		}
	}
	else
	{
		alert("Empty data!!! Please enter data first...");
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
