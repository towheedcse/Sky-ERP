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
	newWindow = window.open("?app=cs_loan_distribute&cmd=get_recieved_info","myWindow","width=650, height=400, left=140, top=100,scrollbars,resizable=no,menubar=no,directories=no");
}
Array.prototype.count = function () {
return this.length;
}

function getReceivedID(id)
{				
	var received_id 				= document.getElementById('received_id'+id).value;	
	var customer_code 				= document.getElementById('party_id'+id).value;						
	var agent_code 					= document.getElementById('agent_code'+id).value;							
	var name 						= document.getElementById('name'+id).value;	
	var fname 						= document.getElementById('fname'+id).value;		
	var address 					= document.getElementById('address'+id).value;					
	var mobile 						= document.getElementById('mobile'+id).value;						
	var received_bag_qty			= document.getElementById('received_bag_qty'+id).value;	
	var rent_per_bag 				= document.getElementById('rent_per_bag'+id).value;	
	
	window.opener.document.frmbooking.received_id.value = received_id;	
	window.opener.document.frmbooking.customer_code.value = customer_code;			
	window.opener.document.frmbooking.agent_code.value = agent_code;	
	window.opener.document.frmbooking.name.value = name;
	window.opener.document.frmbooking.fname.value = fname;
	window.opener.document.frmbooking.address.value = address;	
	window.opener.document.frmbooking.mobile.value = mobile;	
	window.opener.document.frmbooking.received_bag_qty.value = received_bag_qty;
	window.opener.document.frmbooking.rent_per_bag.value = rent_per_bag;	
	close();
}

//********** End ***********

function deleteRecord(loan_id)
{	
   var url_loc = "index.php?app=cs_loan_distribute&cmd=delete&id="+loan_id;
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