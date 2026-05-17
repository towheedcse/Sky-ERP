RE_NUMBER           = new RegExp(/^[0-9]+$/);
RE_EMAIL    = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);
RE_NAME     = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);
//onClick="if(addInquiry()){saveInquiry();return true;}else{ return false;}
var httpobj      = getHTTPObject();
var httpSave      = getHTTPObject();

var stafftypeFound   = true;
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
function calculateDrAmount(val)
{		
  	  var delivery_bag_qty  = parseInt(val);
	  var costing_per_bag 	= parseFloat(document.formID.costing_per_bag.value);	  
	  var balanceQty 		= parseFloat(document.formID.balanceQty.value);
	  var service_charge  = parseInt(document.formID.service_charge.value);
	  if(service_charge==""){service_charge = 0;}
	  if(!isNaN(delivery_bag_qty))
      {  
	     if(delivery_bag_qty=='' || delivery_bag_qty==0){ delivery_bag_qty = 1;}
		 var totalvalue = (delivery_bag_qty * costing_per_bag);
		 totalvalue = totalvalue + service_charge;
		 
		 if((delivery_bag_qty>balanceQty)){ 
		 	document.formID.delivery_bag_qty.focus(); 
         	document.formID.delivery_bag_qty.value="";
			document.formID.Recieved_f.value = "0";
		 	document.getElementById('error_msg').innerHTML = "=>Delivery bag Qty can not be greater then total bag Qty !!!";  
		 }else{
			 document.formID.Recieved_f.value = totalvalue;
		 	 document.getElementById('error_msg').innerHTML = ""; 
		 }

      }else {
	  	 //highlightTableColumn('delivery_bag_qty');
		 document.getElementById('error_msg').innerHTML = "=>Please Enter Only Number !!!";         
         document.formID.delivery_bag_qty.focus(); 
         document.formID.delivery_bag_qty.value="";
	  }

}


function deleteRecord(booking_id)
{	
   var url_loc = "index.php?app=cs_received_product&cmd=delete&id="+booking_id;
    window.location = url_loc;
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