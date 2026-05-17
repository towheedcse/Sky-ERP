RE_NUMBER           = new RegExp(/^[0-9]+$/);
RE_EMAIL    = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);
RE_NAME     = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);
//onClick="if(addInquiry()){saveInquiry();return true;}else{ return false;}
var httpLoadSizeQty  = getHTTPObject();
var httpLoadColorQty = getHTTPObject();
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


function calSalesReturn(){	
   var totalFields = document.getElementById('ttlfields').value; 
   var curr_symble = document.getElementById('curr_symble').value; 
	var j=1;  var returnPrice =0;
	for(j; j<=totalFields; j++){
		var sold_qty		= parseFloat(document.getElementById('sold_qty'+j).value);
		var delivery_qty	= parseFloat(document.getElementById('delivery_qty'+j).value);
		var return_qty		= parseFloat(document.getElementById('return_qty'+j).value);
		var sales_unit_price= parseFloat(document.getElementById('sales_unit_price'+j).value);
		var discount_amount = parseFloat(document.getElementById('discount_amount'+j).value);
		if(return_qty>delivery_qty){ 
		//alert("Return Qty cannot greater then delivery Qty"); 
		document.getElementById('return_qty'+j).value=0;
		}
		sales_unit_price 	= (sales_unit_price - discount_amount); 
		//alert(return_qty+" "+sold_qty);
		if (return_qty==0 || return_qty==""){ return_qty=0;}
		if ((return_qty<=delivery_qty)){
		var returnAmount =(sales_unit_price*(return_qty)); 
		returnPrice+=returnAmount;
		}else{
		document.getElementById('return_qty'+j).value=0;	
		}
	}
		
	var total_sales_price 	= parseFloat(document.getElementById('total_sales_price').value);
	var due 				= parseFloat(document.getElementById('due').value); 
	if (returnPrice>0){	
	var net_return_amount 	= (returnPrice-due);
	document.getElementById("TotalReturnPrice").innerHTML = returnPrice.toFixed(2)+" "+curr_symble;
	document.getElementById("total_return_price").value =returnPrice.toFixed(2);
	document.getElementById("NetReturnAmount").innerHTML = net_return_amount.toFixed(2)+" "+curr_symble;
	document.getElementById("net_return_amount").value 	 = net_return_amount.toFixed(2);
	}else{
	document.getElementById("TotalReturnPrice").innerHTML = "";
	document.getElementById("total_return_price").value ="";
	document.getElementById("NetReturnAmount").innerHTML = "";	
	document.getElementById("net_return_amount").value 	 ="";
	}
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
}else{
strTemp = VALUE.substring(iTemp,v_length);
break;
}
iTemp = iTemp + 1;
} //End While

return strTemp;

} //End Function