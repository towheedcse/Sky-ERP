RE_NUMBER           = new RegExp(/^[0-9]+$/);

RE_EMAIL    = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);

RE_NAME     	    = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);

var httpLoadProduct = getHTTPObject();
var httpSaveProduct = getHTTPObject();
var httpLoadPS	    = getHTTPObject();
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
function isNullAndUndef(variable) {
  if(variable == null || variable == undefined || variable==""){
	return true;
  }else if(isNaN(variable)){ return true; }	
}
function checkUnit(val){	
	 document.getElementById("qty_m").innerHTML = " ("+val+")";
	 document.getElementById("amount_m").innerHTML =" (per "+val+")";
}
function calTotalValue()
{		
	var unit_price 		= parseFloat(document.getElementById('unit_price').value);
	var qty 		= parseFloat(document.getElementById('qty').value);
	var discount_percent 	= parseFloat(document.getElementById('discount_percent').value);
	var discount = 0; 
	if(isNullAndUndef(unit_price)){unit_price=0;} if(isNullAndUndef(qty)){qty=0;}   
	if(isNullAndUndef(discount_percent)){discount_percent=0;}	
	var totalvalue = (qty * unit_price);
	discount = ((totalvalue/100)*discount_percent);
	document.frmbuyerorder.total.value = totalvalue.toFixed(2);
	var net_total = (totalvalue - discount);
	document.frmbuyerorder.nettotal.value = net_total.toFixed(2);		
}

function getProductStock(product_id)
{ 
	  if(product_id!="")
	  {
		  httpLoadPS.open("GET", "index.php?app=sales.return.customerwise&cmd=load_rate&product_id="+product_id, true);
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
	   document.getElementById('unit_price').value=contentArr[0];
	   document.getElementById("qty_return").innerHTML = " "+contentArr[1];
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
		setRequiredField(customer,		 	 'dropdown',  'customer_lbl');	
		//setRequiredField(baddebts_godown,	 'dropdown',  'baddebts_godown_lbl');	
		setRequiredField(intact_godown,      'dropdown',  'intact_godown_lbl');
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
	var customer 		= document.getElementById('customer').value;
	//var baddebts_godown 	= document.getElementById('baddebts_godown').value;
	var intact_godown   	= document.getElementById('intact_godown').value;
	var baddebts_godown 	= intact_godown;
	var productid 		= document.getElementById('product').value;
	var product_status 	= document.getElementById('product_status').value;
	var unit_price 		= parseFloat(document.getElementById('unit_price').value);
	var qty 		= parseFloat(document.getElementById('qty').value);
	var discount_percent 	= parseFloat(document.getElementById('discount_percent').value);
	var total		= (unit_price*qty);
	var discount 		= ((total/100)*discount_percent);
	
	var nettotal 		= (total - discount);

	currencyIdName 		= document.getElementById('currency').value;
	currencyArr  		= currencyIdName.split("###");
	var currency 	 	= currencyArr[0];
	var currencyName 	= currencyArr[1];
	var return_date 	= document.getElementById('return_date').value;
	
	if((productid!="" && qty!="" && product_status!="")){
	httpSaveProduct.open("GET","index.php?app=sales.return.customerwise&cmd=save_tmp&customer="+customer+"&baddebts_godown="+baddebts_godown+"&intact_godown="
	+intact_godown+"&return_date="+return_date+"&currency="+currency+"&currencyName="+currencyName+"&productid="+productid+"&product_status="+product_status+"&qty="
	+qty+"&unit_price="+unit_price+"&discount_percent="+discount_percent+"&nettotal="+nettotal+"&total="+total, true);
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
	 	var total_amount 	= parseFloat(arrSaveOrder[1]);
		if(total_amount>0){ needSave = true;}
		var totalamount = (total_amount+parseFloat(arrSaveOrder[4]));
        	document.getElementById('total_amount').value = totalamount;
		document.getElementById('total_sales_return').value = parseFloat(arrSaveOrder[2]);
		document.getElementById('net_payble').value = parseFloat(arrSaveOrder[1]);
		document.getElementById('total_baddebts').value = parseFloat(arrSaveOrder[3]);
		document.getElementById('discount_amount').value = parseFloat(arrSaveOrder[4]);
		document.getElementById('tbs').innerHTML = tbl;
		document.getElementById('product').focus();
	    //document.getElementById('product').select();
    } 
}

//********* End get Product List ************
function calNetPayble()
{		
  	 var total_amount = parseFloat(document.frmbuyerorder.total_amount.value);
	 var discount_percent = parseFloat(document.frmbuyerorder.discount_percent.value);

	 if(total_amount==''){ total_amount = 0; }	 if(discount_percent==''){ discount_percent = 0;}
	 var discount_amount = ((total_amount/100)*discount_percent);
	 var netPayble = (total_amount - discount_amount); 
	 document.frmbuyerorder.discount_amount.value=discount_amount.toFixed(2);
	 document.frmbuyerorder.net_payble.value =  netPayble.toFixed(2);
}
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
