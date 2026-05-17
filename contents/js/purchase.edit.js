RE_NUMBER   = new RegExp(/^[0-9]+$/);
RE_EMAIL    = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);
RE_NAME     = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);

var httpLoadProduct     = getHTTPObject();
var httpSaveProduct 	= getHTTPObject();
var httpLoadUP 		= getHTTPObject();
var httpLoadDistrict    = getHTTPObject();
var httpLoadSubHead     = getHTTPObject();


var httpLoadChildhead   = getHTTPObject();
var httpLoadMChildhead  = getHTTPObject();
var httpLoadSub3head    = getHTTPObject();
var httpLoadMSub3head   = getHTTPObject();


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
//********* End get Product List ************

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
	  if(isNaN(qty)){ qty = 0; } if(isNaN(unit_price)){ unit_price = 0;} if(isNaN(unit_discount)){ unit_discount = 0; }
	  var discount_amount = ((unit_price/100)*unit_discount);     
		 
      var totalvalue 	= (qty * unit_price);
	  var discountAmount = ((totalvalue/100)*unit_discount);
	  totalvalue = (totalvalue-discountAmount);
	  document.getElementById('total'+sl).value = totalvalue.toFixed(2);		
	  document.getElementById('discount_amount'+sl).value = discount_amount.toFixed(2);
	  getGrandTotalAmount();
	  calDueAmount();
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
	var additional_discount = parseFloat(document.getElementById('additional_discount').value);
	if(isNaN(additional_discount)){ additional_discount = 0; }
	var net_payble = (TotalAmount-(gDiscountAmount+eDiscountAmount+additional_discount));
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
	  calDueAmount();
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
	  calDueAmount();
}

function calNetPayble()
{		
  	 var total_amount 				= parseFloat(document.orderedit.grand_total.value);
	 var general_discount_amount 	= parseFloat(document.orderedit.general_discount_amount.value);
	 var exclusive_discount_amount 	= parseFloat(document.orderedit.exclusive_discount_amount.value);
	 var additional_discount 		= parseFloat(document.orderedit.additional_discount.value);

	 var vat_amount = parseFloat(document.orderedit.vat_amount.value);
	 var AT_amount = parseFloat(document.orderedit.AT_amount.value);

	 if(isNaN(total_amount)){ total_amount = 0; } if(isNaN(general_discount_amount)){ general_discount_amount = 0; }	
	 if(isNaN(exclusive_discount_amount)){ exclusive_discount_amount = 0; } if(isNaN(additional_discount)){ additional_discount = 0; }
 if(isNaN(vat_amount)){ vat_amount = 0; }
 if(isNaN(AT_amount)){ AT_amount = 0; }

	 var netPay = ((total_amount+vat_amount+AT_amount) - (additional_discount+general_discount_amount+exclusive_discount_amount));  
	 document.orderedit.net_payble.value =  netPay.toFixed(2);
	 calDueAmount();
	      
}
function calDueAmount()
{		
  	 var net_payble = parseFloat(document.orderedit.net_payble.value);
	 var paid_amount = parseFloat(document.orderedit.paid_amount.value);
	 //alert(net_payble);alert(paid_amount);
	 
	 if(isNaN(net_payble)){ net_payble = 0; }
	 if(isNaN(paid_amount)){ paid_amount = 0;}
	 var totDue = (net_payble - paid_amount);   
	 document.orderedit.due.value = totDue.toFixed(2);      
	      
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

// ***** Start Get Head Type *******
function getSubHeadTypeList(head_type){ 
	  if(head_type!="") { 	
		  httpLoadDistrict.open("GET","index.php?app=accounts.head&cmd=loadsubhtype&head_type="+head_type, true);
		  httpLoadDistrict.onreadystatechange = handleLoadLTRResponse;
		  httpLoadDistrict.send(null);
	  }
}

function handleLoadLTRResponse(){
    if(httpLoadDistrict.readyState == 4){       
       processLTRData(httpLoadDistrict.responseText);
    }    
}

function processLTRData(ResponseStr){
	productOption = document.getElementById('sub_headtype1');
	while(productOption.length>0){
		productOption.remove(0);
	}
	var arrProduct = Array();
	arrProduct = ResponseStr.split("@@@");
	productOption.options[0]= new Option("Select SL Step-1","");
	for( i=0;i<arrProduct.length-1; i++){
	  var arrProductIdName = Array();
	  arrProductIdName =	arrProduct[i].split("#####");
	  productOption.options[i+1]= new Option(arrProductIdName[1],arrProductIdName[0]);
	}	
}



function getMarginSubHeadList(head_type){ 
	  if(head_type!="") { 	
		  httpLoadSubHead.open("GET","index.php?app=accounts.head&cmd=loadsubhtype&head_type="+head_type, true);
		  httpLoadSubHead.onreadystatechange = handleLoadMarginResponse;
		  httpLoadSubHead.send(null);
	  }
}

function handleLoadMarginResponse(){
    if(httpLoadSubHead.readyState == 4){       
       processMarginData(httpLoadSubHead.responseText);
    }    
}

function processMarginData(ResponseStr){
	productOption = document.getElementById('sub_headtype2');
	while(productOption.length>0){
		productOption.remove(0);
	}
	var arrProduct = Array();
	arrProduct = ResponseStr.split("@@@");
	productOption.options[0]= new Option("Select SL Step-1","");
	for( i=0;i<arrProduct.length-1; i++){
	  var arrProductIdName = Array();
	  arrProductIdName =	arrProduct[i].split("#####");
	  productOption.options[i+1]= new Option(arrProductIdName[1],arrProductIdName[0]);
	}	
}

//===== Child Head ======

function getChildHeadTypeList(sub_head){ 
	var head_type = document.getElementById('head_type1').value;	
	if(sub_head !="") { 	
	  httpLoadChildhead.open("GET","index.php?app=accounts.head&cmd=loadchildhtype&head_type="+head_type+"&sub_head="+sub_head, true);
	  httpLoadChildhead.onreadystatechange = handleLoadChildResponse;
	  httpLoadChildhead.send(null);
	}
}

function handleLoadChildResponse()
{
    if(httpLoadChildhead.readyState == 4){       
       //alert(httpLoadChildhead.responseText);
       processChildData(httpLoadChildhead.responseText);
       //alert(httpLoadDistrict.responseText);
    }    

}

function processChildData(ResponseStr)
{
	//alert(ResponseStr);
	childOption = document.getElementById('child_head1');
	while(childOption.length>0)
	{
		childOption.remove(0);
	}		

	var arrChild = Array();
			
	arrChild = ResponseStr.split("@@@");
	childOption.options[0]= new Option("Select SL Step-2","");
	for( i=0;i<arrChild.length-1; i++)
	{
	  var arrChildIdName = Array();
	  arrChildIdName     = arrChild[i].split("#####");
	  childOption.options[i+1]= new Option(arrChildIdName[1],arrChildIdName[0]);
	}	
			
}




function getMarginChildHeadTypeList(sub_head){ 
	var head_type = document.getElementById('head_type2').value;	
	if(sub_head !="") { 	
	  httpLoadMChildhead.open("GET","index.php?app=accounts.head&cmd=loadchildhtype&head_type="+head_type+"&sub_head="+sub_head, true);
	  httpLoadMChildhead.onreadystatechange = handleLoadMChildResponse;
	  httpLoadMChildhead.send(null);
	}
}

function handleLoadMChildResponse()
{
    if(httpLoadMChildhead.readyState == 4){
       processMChildData(httpLoadMChildhead.responseText);
    }    

}

function processMChildData(ResponseStr)
{
	//alert(ResponseStr);
	childOption = document.getElementById('child_head2');
	while(childOption.length>0)
	{
		childOption.remove(0);
	}		

	var arrChild = Array();
			
	arrChild = ResponseStr.split("@@@");
	childOption.options[0]= new Option("Select SL Step-2","");
	for( i=0;i<arrChild.length-1; i++)
	{
	  var arrChildIdName = Array();
	  arrChildIdName     = arrChild[i].split("#####");
	  childOption.options[i+1]= new Option(arrChildIdName[1],arrChildIdName[0]);
	}	
			
}

//********** End Child Head ***********

//====== Start Subsidiary Step-3 =======

function getSubHead3List(child_head){ 
	  var head_type    = document.getElementById('head_type1').value;
	  var sub_head     = document.getElementById('sub_headtype1').value;
	  if(head_type!="" && sub_head!="" && child_head!="") { 	
		  httpLoadSub3head.open("GET","index.php?app=accounts.head&cmd=loadSL3Htype&head_type="+head_type+"&sub_head="+sub_head+"&child_head="+child_head, true);
		  httpLoadSub3head.onreadystatechange = handleLoadSub3Response;
		  httpLoadSub3head.send(null); 
	  }
}

function handleLoadSub3Response()
{
    if(httpLoadSub3head.readyState == 4){ 
       processSub3Data(httpLoadSub3head.responseText);
    }    

}

function processSub3Data(ResponseStr2)
{
	//alert(ResponseStr);
	productOption2 = document.getElementById('sl_three_head1');
	while(productOption2.length>0)
	{
		productOption2.remove(0);
	}		

	var arrProduct2 = Array();
			
	arrProduct2 = ResponseStr2.split("@@@");
	productOption2.options[0]= new Option("Select SL Step-2","");
	for( i=0; i< arrProduct2.length-1; i++)
	{
	  var arrProductIdName2 = Array();
	  arrProductIdName2 =	arrProduct2[i].split("#####");
	  productOption2.options[i+1]= new Option(arrProductIdName2[1],arrProductIdName2[0]);
	}	
			
}
//========= Sub Head for Search =============

function getMarginSub3HeadTypeList(child_head){ 
	  var head_type    = document.getElementById('head_type2').value;
	  var sub_head     = document.getElementById('sub_headtype2').value;
	  if(head_type!="" && sub_head!="" && child_head!="") { 	
		  httpLoadMSub3head.open("GET","index.php?app=accounts.head&cmd=loadSL3Htype&head_type="+head_type+"&sub_head="+sub_head+"&child_head="+child_head, true);
		  httpLoadMSub3head.onreadystatechange = handleLoadMarginSub3Response;
		  httpLoadMSub3head.send(null);
	  }
}

function handleLoadMarginSub3Response()
{
    if(httpLoadMSub3head.readyState == 4){ 
       processMarginSubhead3Data(httpLoadMSub3head.responseText);
    }    

}

function processMarginSubhead3Data(ResponseStr)
{
	//alert(ResponseStr);
	SubheadOption = document.getElementById('sl_three_head2');
	while(SubheadOption.length>0)
	{
		SubheadOption.remove(0);
	}		

	var arrSubhead = Array();
			
	arrSubhead = ResponseStr.split("@@@");
	SubheadOption.options[0]= new Option("Select SL Step-3","");
	for( i=0;i<arrSubhead.length-1; i++)
	{
	  var arrSubheadIdName = Array();
	  arrSubheadIdName =	arrSubhead[i].split("#####");
	  SubheadOption.options[i+1]= new Option(arrSubheadIdName[1],arrSubheadIdName[0]);
	}	
			
}
//====== End Subsidiary Step-3=======

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
