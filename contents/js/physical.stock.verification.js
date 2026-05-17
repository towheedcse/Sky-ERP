RE_NUMBER           = new RegExp(/^[0-9]+$/);

RE_EMAIL    = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);

RE_NAME     = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);

var httpLoadProduct = getHTTPObject();
var httpSaveProduct = getHTTPObject();
var httpLoadPS		= getHTTPObject();
var isIE            = document.all;

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
function checkUnit(val){	
	 document.getElementById("qty_m").innerHTML = " ("+val+")";
	 document.getElementById("amount_m").innerHTML =" (per "+val+")";
}
function calTotalValue()
{		
	var qty = document.frmbuyerorder.qty.value;
	var unit_price = document.frmbuyerorder.unit_price.value;
	   
	if(isNaN(qty)){ qty = 0; } 
	if(unit_price=='' || unit_price==0){ unit_price = 0;}
	
	var totalvalue = qty * unit_price;
	document.frmbuyerorder.total.value = totalvalue.toFixed(2);		
}

function getProductStock()
{ 
	  var transfer_stock = document.getElementById('delivery_point').value;
	  var product_id     = document.getElementById('product').value;
	  document.getElementById('qty').value=0;
	  if(product_id==""){var product_id = document.getElementById('product').value;}
	  if(product_id!=""){
		  httpLoadPS.open("GET", "index.php?app=physical.stock.verification&cmd=load_stock&product_id="+product_id+"&transfer_stock="+transfer_stock, true);
		  httpLoadPS.onreadystatechange = handlePSResponse;
		  httpLoadPS.send(null);
	  }
}

function handlePSResponse()
{
    if(httpLoadPS.readyState == 4){       
       //alert(httpLoadPS.responseText);
	   var PScontent = trim(httpLoadPS.responseText);
	   contentArr	 = PScontent.split("#####");
	   
	   if(isNaN(contentArr[0]) || contentArr[0]=="" ){ var stock_qty = 0; } else{  var stock_qty = contentArr[0]; }
	   
	   document.getElementById('stock_qty').value= stock_qty;
	   document.getElementById('unit_price').value=contentArr[1];
	   document.getElementById("qty_s").innerHTML = " "+contentArr[2];
	   document.getElementById("qty_t").innerHTML = " "+contentArr[2];
	   document.getElementById('qty').focus();
	   document.getElementById('qty').select();
    } 
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
		
		setRequiredField(delivery_point,	 'dropdown',  'delivery_point_lbl');	
		setRequiredField(verification_date,	 'textbox',	 'verification_date_lbl');
		setRequiredField(product,          	 'dropdown',  'product_lbl');
		setRequiredField(unit_price,         'textbox',   'unit_price_lbl');
		setRequiredField(qty,    			 'textbox',	 'qty_lbl');
		setRequiredField(total,         	 'textbox',   'amount_lbl');
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
		else if(!RE_DECIMAL.exec(qty.value))
		{
			highlightTableColumn('qty_lbl');
			alert(ERROR_NUMBER);
			return false;
		}
		else if(product.value=="")
		{
			highlightTableColumn('product_lbl');			
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
	var productid 		= document.getElementById('product').value;
	var unit_price 		= parseFloat(document.getElementById('unit_price').value);
	var qty 			= parseFloat(document.getElementById('qty').value);
	var stock_qty 		= parseFloat(document.getElementById('stock_qty').value);
	var total			= (unit_price*qty);
	currencyIdName 		= document.getElementById('currency').value;
	currencyArr  		= currencyIdName.split("###");
	var currency 	 	= currencyArr[0];
	var currencyName 	= currencyArr[1];
	var delivery_point 	= document.getElementById('delivery_point').value;
	var verification_date 	= document.getElementById('verification_date').value;
	
	if((productid!="")){ 
	httpSaveProduct.open("GET","index.php?app=physical.stock.verification&cmd=save_tmp&delivery_point="+delivery_point+"&verification_date="
	+verification_date+"&currency="+currency+"&currencyName="+currencyName+"&productid="+productid+"&qty="+qty+"&stock_qty="+stock_qty+"&unit_price="+unit_price+"&total="+total, true);
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
		var tbl 			= arrSaveOrder[0];
	 	var total_amount 	= parseFloat(arrSaveOrder[1]);
		if(total_amount>0){ needSave = true;}
        document.getElementById('total_amount').value = total_amount;
		document.getElementById('tbs').innerHTML = tbl;
		document.getElementById('product').focus();
	    //document.getElementById('product').select();
    } 
}

function clearStockQty(){
	document.getElementById('qty').value="";
	document.getElementById('stock_qty').value="";
}
//********* End get Product List ************

function wantToSave(e)
{
	e.preventDefault();
	var inventory_type = $("#inventory_type").val();
	 if(inventory_type == ""){
	     alert("please select inventory type");
	     return false;
	}
	var needSave = parseFloat(document.getElementById('total_amount').value);
	if(needSave>0)
	{
		var conf = confirm("Sure want ot submit???");
		
		if(conf==true)
		{ 
			const form = document.getElementById('frmbuyerorder');
            		HTMLFormElement.prototype.submit.call(form);
		}else{
			return false;
		}
	}
	else
	{
		alert("Empty data!!! Please enter data first...");
		return false;
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
