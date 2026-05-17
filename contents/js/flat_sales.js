RE_NUMBER           = new RegExp(/^[0-9]+$/);

RE_EMAIL    = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);

RE_NAME     = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);

var httpLoadProduct      = getHTTPObject();
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

function formSetup(frm)
{
	with(frm)
	{		
		setRequiredField(m_unit,			 'dropdown',  'm_unit_lbl');
		setRequiredField(unit_price,         'textbox',   'unit_price_lbl');
		setRequiredField(qty,    			 'textbox',	 'qty_lbl');
		setRequiredField(currency,		     'textbox',	 'currency_lbl');
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
		else if(!RE_NUMBER.exec(qty.value))
		{
			highlightTableColumn('qty_lbl');
			alert(ERROR_NAME);
			return false;
		}
		else if(currency.value=="")
		{
			highlightTableColumn('currency_lbl');
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

function wantToSave()
{
	if(needSave)
	{
		if(confirm("Sure want ot submit???"))
		{
			document.frmbuyerorder.submit();
			
		}
	}
	else
	{
		alert("Empty data!!! Please enter data first...");
		return false;
	}
}

function calAmount()
{		
  	  var flat_size = document.frmbuyerorder.flat_size.value;
	  var rate 		= document.frmbuyerorder.rate.value;
	   
	  if(flat_size=='' || flat_size==0){ flat_size = 0; }
	  if(rate=='' || rate==0){ rate = 0;}
	  var total_size = parseFloat(flat_size);
	  var total_rate = parseFloat(rate);
      var totalAmount = (total_size * total_rate);
	 document.frmbuyerorder.amount.value = totalAmount.toFixed(2);
	      
}
function calCarParkingAmount()
{		
  	  var car_parking_rate = document.frmbuyerorder.car_parking_rate.value;
	  var total_car_no 		= document.frmbuyerorder.total_car_no.value;
	   
	  if(car_parking_rate==''){ car_parking_rate = 0; }
	  if(total_car_no==''){ total_car_no = 0;}
	  var parking_rate 	= parseFloat(car_parking_rate);
	  var total_car 	= parseInt(total_car_no);
      var carAmount = (parking_rate * total_car);
	 document.frmbuyerorder.car_parking.value = carAmount.toFixed(2);
	      
}

function calTotalAmount()
{		
  	  var amount 		= document.frmbuyerorder.amount.value;
	  var car_parking 	= document.frmbuyerorder.car_parking.value;
	  var utilities 	= document.frmbuyerorder.utilities.value;
	   
	     if(amount==''){ amount = 0; }
		 if(car_parking==''){ car_parking = 0;}
		 if(utilities==''){ utilities = 0;}
		 amount 		= parseFloat(amount);
		 car_parking 	= parseFloat(car_parking);
		 utilities 	= parseFloat(utilities);
         var totalvalue = (car_parking + utilities)+amount;
		 document.frmbuyerorder.total_value.value = totalvalue.toFixed(2);
      
	      
}

function calNetPayble()
{		
  	  var total_value = parseFloat(document.frmbuyerorder.total_value.value);
	  var discount = parseFloat(document.frmbuyerorder.discount.value);
	  //alert(total_value);alert(discount);
	 if(total_value=='' || total_value==0){ total_value = 0; }
	 if(discount=='' || discount==0){ discount = 0;}
	 var net_payable = (total_value - discount);   
	 document.frmbuyerorder.net_payble.value =  net_payable.toFixed(2); 
	      
}
function calDueAmount()
{		
  	  var net_payble = parseFloat(document.frmbuyerorder.net_payble.value);
	  var paid_amount = parseFloat(document.frmbuyerorder.paid_amount.value);
	  //alert(net_payble);alert(paid_amount);
	 if(net_payble=='' || net_payble==0){ net_payble = 0; }
	 if(paid_amount=='' || paid_amount==0){ paid_amount = 0;}
	 var DueAmount = (net_payble - paid_amount);   
	 document.frmbuyerorder.due.value = DueAmount.toFixed(2); 
	      
}
function calForInstallment()
{		
  	 var due = parseFloat(document.frmbuyerorder.due.value);
	 var down_payment = parseFloat(document.frmbuyerorder.down_payment.value);
	 if(due==''){ due = 0; }
	 if(down_payment==''){ down_payment = 0;}
	 var for_installment = (due - down_payment);
	 document.frmbuyerorder.for_installment.value = for_installment.toFixed(2);    
	      
}
function calPerInstallment()
{		
  	 var for_installment = parseFloat(document.frmbuyerorder.for_installment.value);
	 var installment_number = parseInt(document.frmbuyerorder.installment_number.value);
	 if(for_installment==''){ for_installment = 0; }
	 if(installment_number==''){ installment_number = 0; }
	 var per_installment = (for_installment/installment_number);
	 document.frmbuyerorder.per_installment.value = per_installment.toFixed(2);    
	      
}
// ===== end Expected Salary ==========

function deleteRecord(id)
{	
   var url_loc = "index.php?app=purchase&cmd=delete&id="+id;
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