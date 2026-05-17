RE_NUMBER   = new RegExp(/^[0-9]+$/);
RE_EMAIL    = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);
RE_NAME     = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);

var httpLoadProduct     = getHTTPObject();
var httpSaveProduct 	= getHTTPObject();
var httpLoadUP 		= getHTTPObject();
var httpLoadUDP		= getHTTPObject();
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
function loadUndeliveryOrder(){
	var customer_id		= document.getElementById('customer').value;
	var voucher_no 		= document.getElementById('voucher_no').value;
	var delivery_point 	= document.getElementById('delivery_point').value;
	
	if(customer_id !="" && delivery_point !="" && voucher_no!="")
	{	
		httpLoadUDP.open("GET", "index.php?app=sales_order&cmd=add_undelivery&customer_id="+customer_id+"&delivery_point="+delivery_point+"&voucher_no="+voucher_no, true);
		httpLoadUDP.onreadystatechange = handleUDPResponse;
		httpLoadUDP.send(null);
	}else{
		alert("Please Select customer and delivery point");
	}
}

function handleUDPResponse(){
	if(httpLoadUDP.readyState == 4){ 
		var voucher_no  = trim(httpLoadUDP.responseText);
		if(voucher_no==""){
			var voucher_no 		= document.getElementById('voucher_no').value;
		}
		window.location = "index.php?app=sales_order&cmd=edit&voucher_no="+voucher_no;	
	}
}
function checkUnit(val){	
	 document.getElementById("qty_m").innerHTML = " ("+val+")";
	 document.getElementById("uprice_m").innerHTML =" (per "+val+")";
}

function checkTotalUnit(val){	
	 document.getElementById("totalqty_m").innerHTML = " ("+val+")";	
	 document.getElementById("totalqty_m1").innerHTML = " ("+val+")";
}

function getProductList(brandArr){ 
	  var catagoryArr = document.getElementById('catagory').value; 
	  var catagoryStr = catagoryArr.split("###");
	  var catagory_id = catagoryStr[0];
	  var brandStr = brandArr.split("###");
	  var brand_id = brandStr[0];
	  if(brand_id!="") { 	
		  httpLoadProduct.open("GET", "index.php?app=sales.order&cmd=loadProduct&brand_id="+brand_id+"&catagory_id="+catagory_id, true);
		  httpLoadProduct.onreadystatechange = handleLoadResponse;
		  httpLoadProduct.send(null);
	  }
}

function handleLoadResponse()
{
    if(httpLoadProduct.readyState == 4){       
       //alert(httpLoadProduct.responseText);
       processData(httpLoadProduct.responseText);
       //alert(httpLoadProduct.responseText);
    }    

}

function processData(ResponseStr)
{
		//alert(ResponseStr);
		productOption = document.getElementById('product');
		while(productOption.length>0)
		{
			productOption.remove(0);
		}		

		var arrProduct = Array();
				
		arrProduct = ResponseStr.split("@@@");
		productOption.options[0]= new Option("Select One","0");
		for( i=0;i<arrProduct.length-1; i++)
		{
		  var arrProductIdName = Array();
		  arrProductIdName  = arrProduct[i].split("#####");
		  var details 	   	= arrProductIdName[2];		  
		  productOption.options[i+1]= new Option(arrProductIdName[1]+'-'+details, arrProductIdName[0]+'###'+arrProductIdName[1]);
		}	
			
}
//********* End get Product List ************
function getProductDtl(product_id,sl)
{ 
	var customer_id = document.getElementById('customer').value;
	if(customer_id == ""){
		$('#product').val("").trigger('chosen:updated');
		alert("Select Customer First!!");
		return false;
	}

	  document.getElementById('pesl').value=sl; 
	  if(product_id!="")
	  {    
		  httpLoadUP.open("GET", "index.php?app=sales.order&cmd=get_dtl&product_id="+product_id+"&customer_id="+customer_id, true);
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
	   var psl = document.getElementById('pesl').value; 
	   document.getElementById('m_unit'+psl).value=contentArr[0];
	   //document.getElementById('details').value=contentArr[1];
	   document.getElementById('unit_price'+psl).value=contentArr[2];
	   catagoryArr	 = contentArr[3].split("###");
	   document.getElementById('catagory'+psl).value=catagoryArr[0]; 
	   brandArr	 = contentArr[4].split("###");
	   document.getElementById('brand'+psl).value=brandArr[0];
	   calTotalValue(psl);
    } 

}
//********* End getProductDtl ************

function wantToSave()
{
	var needSave = parseFloat(document.getElementById('total_amount').value);
	if(total_amount>0){ needSave = true;}
	if(needSave){
		if(confirm("Sure want ot submit???")==true){
			document.orderedit.submit();
		}
	}else{
		alert("Empty data!!! Please enter data first...");
	}
}

function calTotalValue(sl)
{		
  	  var qty = parseFloat(document.getElementById('order_qty'+sl).value);
	  var unit_price = parseFloat(document.getElementById('unit_price'+sl).value);
	  var unit_discount = parseFloat(document.getElementById('discount_per_qty'+sl).value);

	  var unit_vat 	= parseFloat(document.getElementById('vat_per_qty'+sl).value);

	  if(isNaN(qty)){ qty = 0; } if(isNaN(unit_price)){ unit_price = 0;} if(isNaN(unit_discount)){ unit_discount = 0; }

	 if(isNaN(unit_vat)){ unit_vat = 0; }


	  var discount_amount = ((unit_price/100)*unit_discount);  
          var totalvalue 	= (qty * unit_price);

	  var vatAmount = ((totalvalue/100)*unit_vat);   

	  var discountAmount = ((totalvalue/100)*unit_discount);
	  totalvalue = (totalvalue+vatAmount-discountAmount);
	  document.getElementById('total'+sl).value = totalvalue;		
	  document.getElementById('discount_amount'+sl).value = discount_amount;
	  document.getElementById('vat_amount'+sl).value = vatAmount;
	  getGrandTotalAmount();
}
function getGrandTotalAmount(){
	var ttlfields = parseInt(document.getElementById("ttlfield").value); 
	var j=1; var TotalAmount=0;
	for(j; j < ttlfields; j++){
		var orderPrice = parseFloat(document.getElementById('total'+j).value); 
		TotalAmount+=orderPrice;
	} 
	document.getElementById('grand_total').value=TotalAmount.toFixed(2); 
	var general_discount_percent = parseFloat(document.getElementById('general_discount_percent').value);
	if(isNaN(general_discount_percent)){ general_discount_percent = 0; }
	var gDiscountAmount = ((TotalAmount/100)*general_discount_percent);
	document.getElementById('general_discount_amount').value=gDiscountAmount.toFixed(2);
	
	var exclusive_discount_percent = parseFloat(document.getElementById('exclusive_discount_percent').value);
	if(isNaN(exclusive_discount_percent)){ exclusive_discount_percent = 0; }
	var eDiscountAmount = (((TotalAmount-gDiscountAmount)/100)*exclusive_discount_percent);
	
	document.getElementById('exclusive_discount_amount').value=eDiscountAmount.toFixed(2);

	//var additional_discount = parseFloat(document.getElementById('additional_discount').value);
	//if(isNaN(additional_discount)){ additional_discount = 0; }

	var additional_discount_percent = parseFloat(document.getElementById('additional_discount_percent').value);
	if(isNaN(additional_discount_percent)){ additional_discount_percent = 0; }
	var remainingAmount = TotalAmount - (gDiscountAmount + eDiscountAmount);
	var additional_discount = ((remainingAmount/100)*additional_discount_percent);
	document.getElementById('additional_discount').value=additional_discount.toFixed(2);

	var additional_cost = parseFloat(document.getElementById('additional_cost').value);
	if(isNaN(additional_cost)){ additional_cost = 0; }

	var total_vat_percent = parseFloat(document.getElementById('total_vat_percent').value);
	if(isNaN(total_vat_percent)){ total_vat_percent = 0; }
	var total_vat_amount = ((TotalAmount/100)*total_vat_percent);
	document.getElementById('total_vat_amount').value=total_vat_amount.toFixed(2);

	//var total_vat_amount = parseFloat(document.getElementById('total_vat_amount').value);
	//if(isNaN(total_vat_amount)){ total_vat_amount = 0; }

	var net_payble = ((TotalAmount+additional_cost+total_vat_amount)-(gDiscountAmount+eDiscountAmount+additional_discount));
	document.getElementById('net_payble').value=net_payble.toFixed(2);

}
function calGDiscount()
{		
  	var total_amount = parseFloat(document.orderedit.grand_total.value);
	var general_discount_percent = parseFloat(document.orderedit.general_discount_percent.value);

	if(isNaN(total_amount)){ total_amount = 0; } if(isNaN(general_discount_percent)){ general_discount_percent = 0; }	
	var general_discount_amount = ((total_amount/100)*general_discount_percent);
	var netPay = (total_amount - general_discount_amount); 
	document.orderedit.general_discount_amount.value=general_discount_amount.toFixed(2);
	 
	var exclusive_discount_percent = parseFloat(document.getElementById('exclusive_discount_percent').value);
	if(isNaN(exclusive_discount_percent)){ exclusive_discount_percent = 0; }
	var eDiscountAmount = (((total_amount-general_discount_amount)/100)*exclusive_discount_percent);
	
	document.getElementById('exclusive_discount_amount').value=eDiscountAmount.toFixed(2);
	var additional_discount = parseFloat(document.getElementById('additional_discount').value);
	if(isNaN(additional_discount)){ additional_discount = 0; }
	var net_payble = (total_amount-(general_discount_amount+eDiscountAmount+additional_discount));
	document.getElementById('net_payble').value=net_payble.toFixed(2);
calNetPayble();
}
function calExDiscount()
{		
  	 var total_amount 				= parseFloat(document.orderedit.grand_total.value);
	 var exclusive_discount_percent = parseFloat(document.orderedit.exclusive_discount_percent.value);
	 var general_discount_amount 	= parseFloat(document.orderedit.general_discount_amount.value);

	 if(isNaN(total_amount)){ total_amount = 0; } if(isNaN(general_discount_amount)){ general_discount_amount = 0; }	
	 if(isNaN(exclusive_discount_percent)){ exclusive_discount_percent = 0; }
	 var additional_discount = parseFloat(document.getElementById('additional_discount').value);
	 if(isNaN(additional_discount)){ additional_discount = 0; }
	
	 var NetAmount = (total_amount-general_discount_amount);
	 var exclusive_discount_amount = ((NetAmount/100)*exclusive_discount_percent);
	 var netPay = (NetAmount - (exclusive_discount_amount+additional_discount)); 
	 
	 document.orderedit.exclusive_discount_amount.value=exclusive_discount_amount.toFixed(2);
	 document.orderedit.net_payble.value =  netPay.toFixed(2);
calNetPayble();
}
function calAdditionalDiscount()
{		
  	 var total_amount 				 = parseFloat(document.orderedit.grand_total.value);
	 var general_discount_amount 	 = parseFloat(document.orderedit.general_discount_amount.value);
	 var exclusive_discount_amount   = parseFloat(document.orderedit.exclusive_discount_amount.value);
	 var additional_discount_percent = parseFloat(document.orderedit.additional_discount_percent.value);
	 
	 if(isNaN(total_amount)){ total_amount = 0; } if(isNaN(exclusive_discount_amount)){ exclusive_discount_amount = 0; } if(isNaN(general_discount_amount)){ general_discount_amount = 0; }
	 if(isNaN(additional_discount_percent)){ additional_discount_percent = 0; }
	 var NetAmount = (total_amount-(general_discount_amount+exclusive_discount_amount));
	 var additional_discount_amount = ((NetAmount/100)*additional_discount_percent);
	 var netPay = (NetAmount - additional_discount_amount); 
	 document.orderedit.additional_discount.value=additional_discount_amount.toFixed(2);
	 document.orderedit.net_payble.value =  netPay.toFixed(2);
calNetPayble();
}

function calDiscountPersent()
{	
	 var total_amount 		 = parseFloat(document.orderedit.grand_total.value);	 
	 var general_discount_amount 	 = parseFloat(document.orderedit.general_discount_amount.value);
	 var exclusive_discount_amount   = parseFloat(document.orderedit.exclusive_discount_amount.value);	 
	 var additional_discount_amount  = parseFloat(document.orderedit.additional_discount.value);
	 var additional_discount_percent = parseFloat(document.orderedit.additional_discount_percent.value);
	 if(isNaN(total_amount)){ total_amount = 0; } if(isNaN(exclusive_discount_amount)){ exclusive_discount_amount = 0; } if(isNaN(general_discount_amount)){ general_discount_amount = 0; } 
	 if(isNaN(additional_discount_amount)){ additional_discount_amount = 0; } if(isNaN(additional_discount_percent)){ additional_discount_percent = 0; }
	 var NetAmount = (total_amount-(general_discount_amount+exclusive_discount_amount));
	 additional_discount_percent = ((additional_discount_amount * 100)/NetAmount);	
	 var netPay = (NetAmount-additional_discount_amount);
	 document.orderedit.net_payble.value =  netPay.toFixed(2);
	 document.orderedit.additional_discount_percent.value=additional_discount_percent.toFixed(4);
calNetPayble();
}


function calculateAllValue() {
    calGDiscount();
    calExDiscount();
    calAdditionalDiscount();
    calDiscountPersent();
	calVatAmount();
}

function calNetPayble()
{		
  	 var total_amount 		= parseFloat(document.orderedit.grand_total.value);
	 var general_discount_amount 	= parseFloat(document.orderedit.general_discount_amount.value);
	 var exclusive_discount_amount 	= parseFloat(document.orderedit.exclusive_discount_amount.value);
	 var additional_discount 	= parseFloat(document.orderedit.additional_discount.value);
	 if(isNaN(total_amount)){ total_amount = 0; } if(isNaN(general_discount_amount)){ general_discount_amount = 0; }	
	 if(isNaN(exclusive_discount_amount)){ exclusive_discount_amount = 0; } if(isNaN(additional_discount)){ additional_discount = 0; }

	var additional_cost = parseFloat(document.orderedit.additional_cost.value);
    if (isNaN(additional_cost)) {
        additional_cost = 0;
    }

	var total_vat_amount = parseFloat(document.getElementById('total_vat_amount').value);
	if(isNaN(total_vat_amount)){ total_vat_amount = 0; }

	 var netPay = ((total_amount + additional_cost + total_vat_amount) - (additional_discount+general_discount_amount+exclusive_discount_amount));  
	 document.orderedit.net_payble.value =  netPay.toFixed(4);      
}
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
