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

//******* Start get Sample ********
function OpenWin_onclick()
{	
	//newWindow = window.open("?app=registration&cmd=showlist","myWindow","width=500, height=320, left=200, top=100,scrollbars,menubar=yes,directories=yes,resizable");
	newWindow = window.open("?app=customer_booking&cmd=get_customer","myWindow","width=650, height=400, left=140, top=100,scrollbars,resizable,menubar=yes,directories=yes");
}
Array.prototype.count = function () {
return this.length;
}

function getCustomerID(id)
{				
	var customer_code 				= document.getElementById('party_id'+id).value;						
	var name 						= document.getElementById('name'+id).value;	
	var fname 						= document.getElementById('fname'+id).value;		
	var address 					= document.getElementById('address'+id).value;					
	var mobile 						= document.getElementById('mobile'+id).value;			
		
	window.opener.document.frmbooking.customer_code.value = customer_code;	
	window.opener.document.frmbooking.name.value = name;
	window.opener.document.frmbooking.fname.value = fname;
	window.opener.document.frmbooking.address.value = address;	
	window.opener.document.frmbooking.mobile.value = mobile;
	close();
}

//********** End ***********
//=============== Start validation ============

function calTotalCarring()
{		
  	  var bag_qty = document.frmbooking.bag_qty.value;
	  var carring_per_bag = document.frmbooking.carring_per_bag.value;
	  if(isNaN(carring_per_bag))
      {  
	     highlightTableColumn('carring_per_bag');
         alert('Please Enter Only Number !!!');
         document.frmbooking.carring_per_bag.focus(); 
         document.frmbooking.carring_per_bag.value="";
      }else if(isNaN(bag_qty)) {
	  	 highlightTableColumn('bag_qty');
         alert('Please Enter Only Number !!!');
         document.frmbooking.bag_qty.focus(); 
         document.frmbooking.bag_qty.value="";
	  }
	  else
      { 
	  	 resetTableColumn('bag_qty');
		 resetTableColumn('carring_per_bag');
	     if(bag_qty==''){ bag_qty = 0; document.frmbooking.bag_qty.value=0;}
		 if(carring_per_bag==''){ carring_per_bag = 0;document.frmbooking.carring_per_bag.value=0;}
		 var totalvalue = parseInt(bag_qty) * parseFloat(carring_per_bag);
		 document.frmbooking.total_carring_cost.value = totalvalue;

      }

}

function calTotalEmptyBagCost()
{		
  	  var empty_bag_qty = document.frmbooking.empty_bag_qty.value;
	  var empty_bag_u_price = document.frmbooking.empty_bag_u_price.value;
	  var bag_qty = document.frmbooking.bag_qty.value;
	  
	  if(isNaN(empty_bag_u_price))
      {  
	     highlightTableColumn('empty_bag_u_price');
         alert('Please Enter Only Number !!!');
         document.frmbooking.empty_bag_u_price.focus(); 
         document.frmbooking.empty_bag_u_price.value="";
      }else if(isNaN(empty_bag_qty)) {
	  	 highlightTableColumn('empty_bag_qty');
         alert('Please Enter Only Number !!!');
         document.frmbooking.empty_bag_qty.focus(); 
         document.frmbooking.empty_bag_qty.value="";
	  }
	  else
      { 
	  	if(parseInt(bag_qty)>=parseInt(empty_bag_qty)){
			 resetTableColumn('empty_bag_qty');
			 resetTableColumn('empty_bag_u_price');
			 if(empty_bag_qty==''){ empty_bag_qty = 0; document.frmbooking.empty_bag_qty.value=0;}
			 if(empty_bag_u_price==''){ empty_bag_u_price = 0;document.frmbooking.empty_bag_u_price.value=0;}
			 var totalvalue1 = parseInt(empty_bag_qty) * parseFloat(empty_bag_u_price);
			 document.frmbooking.total_empty_bag_cost.value = totalvalue1;
		}else{
			 highlightTableColumn('empty_bag_qty');
			 alert('Empty bag qty cannot be greater than total booking bag !!!');
			 document.frmbooking.empty_bag_qty.focus(); 
			 document.frmbooking.empty_bag_qty.value="";
		}

      }
}

function deleteRecord(booking_id)
{	
   var url_loc = "index.php?app=customer_booking&cmd=delete&id="+booking_id;
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