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
	   
	if(qty=='' || qty==0){ qty = 0; }
	if(unit_price=='' || unit_price==0){ unit_price = 0;}
	
	var totalvalue = qty * unit_price;
	document.frmbuyerorder.total.value = totalvalue.toFixed(2);		
}

function getProductStock()
{ 
	  var transfer_stock = document.getElementById('store_id').value;
	  var product_id = document.getElementById('product').value;
	  document.getElementById('qty').value=0;
	  if(product_id==""){var product_id = document.getElementById('product').value;}
	  if(product_id!=""){
		  httpLoadPS.open("GET", "index.php?app=production.new&cmd=load_stock&product_id="+product_id+"&transfer_stock="+transfer_stock, true);
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
	   if(parseInt(contentArr[0]) >0){
	    document.getElementById('stock_qty').value=contentArr[0];
	   }else{
		 document.getElementById('stock_qty').value=0;  
	   }
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
		
		setRequiredField(factory_id,		 'dropdown',  'factory_id_lbl');	
		setRequiredField(store_id,     		 'dropdown',  'store_id_lbl');
		setRequiredField(production_date,	 'textbox',	 'production_date_lbl');
		setRequiredField(currency,		     'textbox',	 'currency_lbl');
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
		else if(product.value==0)
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
	var factory_id 	= document.getElementById('factory_id').value;
	var store_id 	= document.getElementById('store_id').value;
	var production_date 	= document.getElementById('production_date').value;
	
	if((productid!="" && qty!="") && (qty >0)){
	httpSaveProduct.open("GET","index.php?app=production.new&cmd=save_tmp&factory_id="+factory_id+"&store_id="+store_id+"&production_date="
	+production_date+"&currency="+currency+"&currencyName="+currencyName+"&productid="+productid+"&qty="+qty+"&unit_price="+unit_price+"&total="+total, true);
	 httpSaveProduct.onreadystatechange = handleSaveResponse;
	 httpSaveProduct.send(null);
	}else{
		chkTransferQty(transfer_qty);	
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
	    document.getElementById('product').select();
    } 
}

function chkTransferQty(transfer_qty){
	var stock_qty 	 = parseFloat(document.getElementById('stock_qty').value);
	var transfer_qty = parseFloat(transfer_qty);
	if ((transfer_qty>stock_qty) || (stock_qty==0)){
	document.getElementById('transfer_qty').value="";	
	}
}
function clearStockQty(){
	document.getElementById('transfer_qty').value="";
	document.getElementById('stock_qty').value="";
}
//********* End get Product List ************

function wantToSave()
{
	var needSave = parseFloat(document.getElementById('total_amount').value);
	if(needSave>0)
	{
		var conf = confirm("Sure want ot submit???");
		
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