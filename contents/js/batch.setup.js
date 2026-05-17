RE_NUMBER   = new RegExp(/^[0-9]+$/);
RE_EMAIL    = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);

RE_NAME     = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);

var httpLoadProduct = getHTTPObject();
var httpSaveProduct = getHTTPObject();
var httpLoadPS	    = getHTTPObject();
var httpLoadPSE	    = getHTTPObject();
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
function calTotalDayQty()
{		
	var qty = parseFloat(document.frmbuyerorder.day_qty.value);
	var day_wastage_qty = parseFloat(document.frmbuyerorder.day_wastage_qty.value);
	   
	if(isNaN(qty)){ qty = 0; } if(isNaN(day_wastage_qty)){ day_wastage_qty = 0;}
	
	var totalvalue = qty + day_wastage_qty;
	document.frmbuyerorder.total_day.value = (totalvalue/1000).toFixed(4);		
}
function calDayWastage()
{		
	var qty 	= parseFloat(document.frmbuyerorder.day_qty.value);
	var day_wastage = parseFloat(document.frmbuyerorder.day_wastage.value);	
	   
	if(isNaN(qty)){ qty = 0; } if(isNaN(day_wastage)){ day_wastage = 0;}	
	var day_wastage_qty = ((qty /100) * day_wastage);
	document.frmbuyerorder.day_wastage_qty.value = day_wastage_qty.toFixed(4);	
	calTotalDayQty();	
}

function calTotalNightQty()
{		
	var qty = parseFloat(document.frmbuyerorder.night_qty.value);
	var night_wastage_qty = parseFloat(document.frmbuyerorder.night_wastage_qty.value);
	   
	if(isNaN(qty)){ qty = 0; } if(isNaN(night_wastage_qty)){ night_wastage_qty = 0;}
	
	var totalvalue = qty + night_wastage_qty;
	document.frmbuyerorder.total_night.value = (totalvalue/1000).toFixed(4);		
}
function calNightWastage()
{		
	var qty 	= parseFloat(document.frmbuyerorder.night_qty.value);
	var day_wastage = parseFloat(document.frmbuyerorder.night_wastage.value);	
	   
	if(isNaN(qty)){ qty = 0; } if(isNaN(day_wastage)){ day_wastage = 0;}	
	var night_wastage_qty = ((qty /100) * day_wastage);
	document.frmbuyerorder.night_wastage_qty.value = night_wastage_qty.toFixed(4);	
	calTotalNightQty();	
}

/* ==== For Edit ==== */

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
		setRequiredField(batch_name,		 'textbox',   'batch_name_lbl');
		setRequiredField(finish_goods,     	 'dropdown',  'finish_goods_lbl');
		setRequiredField(currency,		 'dropdown',   'currency_lbl');
		setRequiredField(out_from,     	 	 'dropdown',  'out_from_lbl');
		setRequiredField(product,          	 'dropdown',  'product_lbl');
		//setRequiredField(day_qty,    		 'textbox',   'qty_day_lbl');
		//setRequiredField(day_wastage,          'textbox',   'day_wastage_lbl');
		//setRequiredField(night_qty,         	 'textbox',   'night_qty_lbl');
		//setRequiredField(night_wastage,        'textbox',   'night_wastage_lbl');
		//setRequiredField(total_day,         	 'textbox',   'total_day_lbl');
		//setRequiredField(total_night,          'textbox',   'total_night_lbl');
	}
}
function fieldValidation(frm)
{
	with(frm)
	{
		if(!RE_DECIMAL.exec(day_qty.value))
		{
			highlightTableColumn('qty_day_lbl');
			alert(ERROR_NUMBER);		
			return false;
		}
		else if(!RE_DECIMAL.exec(night_qty.value))
		{
			highlightTableColumn('qty_night_lbl');
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
	var productid 		= document.getElementById('product').value;
	var day_qty 		= parseFloat(document.getElementById('day_qty').value);
	var day_wastage 	= parseInt(document.getElementById('day_wastage').value);
	var day_wastage_qty 	= parseFloat(document.getElementById('day_wastage_qty').value);

	
	var night_qty 		= parseFloat(document.getElementById('night_qty').value);
	var night_wastage 	= parseInt(document.getElementById('night_wastage').value);
	var night_wastage_qty 	= parseFloat(document.getElementById('night_wastage_qty').value);


	var total_day 		= parseFloat(document.getElementById('total_day').value);
	var total_night 	= parseFloat(document.getElementById('total_night').value);

	
	currencyIdName 		= document.getElementById('currency').value;
	currencyArr  		= currencyIdName.split("###");
	var currency 	 	= currencyArr[0];
	var currencyName 	= currencyArr[1];

	var batch_name 		= document.getElementById('batch_name').value;
	var finish_goods 	= document.getElementById('finish_goods').value;
	var out_from 		= document.getElementById('out_from').value;
	
	if((productid!="" && batch_name!="" && finish_goods!="")){ 
	httpSaveProduct.open("GET","index.php?app=po.batch.setup&cmd=save_tmp&batch_name="+batch_name+"&finish_goods="+finish_goods+"&currency="+currency+"&currencyName="+currencyName+"&out_from="+out_from+"&productid="+productid+"&day_qty="+day_qty+"&day_wastage="+day_wastage+"&day_wastage_qty="+day_wastage_qty+"&night_qty="+night_qty+"&night_wastage="+night_wastage+"&night_wastage_qty="+night_wastage_qty+"&total_day="+total_day+"&total_night="+total_night, true);
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
 	var total_day_qty 	= parseFloat(arrSaveOrder[1]);
	var total_night_qty 	= parseFloat(arrSaveOrder[2]);
	var total_day_wastage 	= parseFloat(arrSaveOrder[3]);
	var total_night_wastage	= parseFloat(arrSaveOrder[4]);
	if(total_day_qty >0 || total_night_qty >0){ needSave = true;}
        document.getElementById('total_day_qty').value = total_day_qty;
        document.getElementById('total_night_qty').value = total_night_qty;
        document.getElementById('total_day_wastage').value = (total_day_wastage/1000);
        document.getElementById('total_night_wastage').value = (total_night_wastage/1000);

	document.getElementById('day_qty').value = 0;
        document.getElementById('day_wastage').value = 0;
        document.getElementById('day_wastage_qty').value = 0;

	document.getElementById('night_qty').value = 0;
        document.getElementById('night_wastage').value = 0;
        document.getElementById('night_wastage_qty').value = 0;

        document.getElementById('total_day').value = 0;
        document.getElementById('total_night').value = 0;

	document.getElementById('tbs').innerHTML = tbl;
	document.getElementById('product').focus();
	document.getElementById('product').select();
    } 
}


function clearStockQty(){
	document.getElementById('transfer_qty').value="";
	document.getElementById('stock_qty').value="";
}
//********* End get Product List ************

function wantToSave()
{
	var total_day_qty   = parseFloat(document.getElementById('total_day_qty').value);
	var total_night_qty = parseFloat(document.getElementById('total_night_qty').value);
	if(total_day_qty >0 || total_night_qty >0)
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
