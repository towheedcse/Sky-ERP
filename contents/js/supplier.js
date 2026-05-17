RE_NUMBER           = new RegExp(/^[0-9]+$/);
RE_EMAIL    = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);
RE_NAME     = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);
//onClick="if(addInquiry()){saveInquiry();return true;}else{ return false;}
var httpSaveInquiry      = getHTTPObject();

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

function doFormSubmit()
{
   requiredFields.length = 0;
   var errCnt = 0;
   var frm = document.supplier;

   // Setup required fields
   setupForm(frm);

   // Validate form for required fields
   errCnt = validateForm(frm);

   if (errCnt)
   {
      alert(MISSING_REQUIRED_FIELDS); 
      return false;
   }
   else
   {
      if(validateFields(frm))
      {
         return true;
      }
      else
         return false;
   }

}

function setupForm(frm)
{
   with (frm)
   {  
   	  setRequiredField(name,             		 		 'textbox',   'name');  
   	  setRequiredField(address,             		 	 'textbox',   'address');
	  setRequiredField(country,                     	 'dropdown',   'country');  
   	  setRequiredField(phone,             		 		 'textbox',   'phone');  
   	  setRequiredField(mobile,             		 		 'textbox',   'mobile'); 
   	  setRequiredField(email,             		 		 'textbox',   'email');  
   	  setRequiredField(type_of_business,             	 'dropdown',   'type_of_business');  
   }

}

function validateFields(frm)
{
   with(frm)
   {	
   	 if(!RE_EMAIL.exec(email.value))
     {
	 	highlightTableColumn('email');
         alert(ERROR_EMAIL);
         return false;
     }	 	 
     else if(RE_NAME.exec(name.value))
     {
         highlightTableColumn('name');
         alert(ERROR_NAME);
         return false;
      }
	  else if(country.value ==0)
      {
         highlightTableColumn('country');
         alert(ERROR_NAME);
         return false;
      }
	  else if(type_of_business.value ==0)
      {
         highlightTableColumn('type_of_business');
         alert("Please select type of business");
         return false;
      }            
      else
      {
         return true;
      }
   } 
   return true;
}

function frmSubmit()
{ 
  if(!doFormSubmit())	
  {	
  	   document.getElementById('footer_status_msg').style.display = "block";
       document.getElementById('footer_status_msg').innerHTML = "<font color='#FF3333'>Please enter value for all red marked required field(s).</font>";
	   document.getElementById('status_msg').style.display = "block";
       document.getElementById('status_msg').innerHTML = "Please enter value for all red marked required field(s).";
	   return false;

  } else {		
      document.getElementById('status_msg').style.display = "block";
      document.getElementById('status_msg').innerHTML ="";
      return true;

  }  
}
//=========== End Personal Info ==============

function editSetting(id){	  	   

	  document.supplier.id.value	= document.getElementById('supplier_code'+id).value;
	  var name 						= document.getElementById('name'+id).value; 
	  var address 					= document.getElementById('address'+id).value;  	 
	  var country 					= document.getElementById('country'+id).value;   	 
	  var phone 					= document.getElementById('phone'+id).value; 	 
	  var mobile					= document.getElementById('mobile'+id).value;    	 
	  var email 					= document.getElementById('email'+id).value; 	 
	  var fax						= document.getElementById('fax'+id).value;	  
	   	 
	  var contact_person				= document.getElementById('contact_person'+id).value;    	 
	  var designation 					= document.getElementById('designation'+id).value; 	 
	  var type_of_business				= document.getElementById('type_of_business'+id).value; 	 
	  var type_of_commodity				= document.getElementById('type_of_commodity'+id).value;   	
	    
	  if(country==""){country=0;}  
	  if(type_of_business==""){type_of_business=0;}  
	  
	  document.supplier.name.value 			= name;  
	  document.supplier.address.value 		= address;  
	  document.supplier.country.value 		= country;  
	  document.supplier.phone.value 		= phone;   
	  document.supplier.mobile.value 		= mobile;  
	  document.supplier.email.value 		= email;  
	  document.supplier.fax.value 			= fax;
	  
	    
	  document.supplier.contact_person.value 	= contact_person;   
	  document.supplier.designation.value 		= designation;  
	  document.supplier.type_of_business.value 	= type_of_business;  
	  document.supplier.type_of_commodity.value = type_of_commodity;
	  
	  document.getElementById('footer_status_msg').style.display = "block";
      document.getElementById('footer_status_msg').innerHTML = "<font color='#FF3333'>Now You Can Update This Record.</font>";
	  document.getElementById('status_msg').style.display = "none";

}

function deleteRecord(eid)
{	
   var url_loc = "index.php?app=supplier&cmd=delete&id="+eid;
   window.location = url_loc;

}

//=========== End Top Skills ===========

function cancelStyle()
{
	window.location=CANCEL_URL;
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
}
else{
strTemp = VALUE.substring(iTemp,v_length);
break;
}

iTemp = iTemp + 1;

} //End While

return strTemp;

} //End Function

function sortTable(tableRef,col)
{	
	tableRowNo = tableRef.rows.length-1;

	for(i=1;i<tableRowNo;i++)
	{
		for(j=i;jtableRowNo;j++)
		{
			if(tableRef.rows[i].cells[col].childNodes[0].nodeValue >tableRef.rows[j].cells[col].childNodes[0].nodeValue)
			{
			//var temp=tableRef.rows[i];
			var temp = tableRef.replaceChild(tableRef.rows[j],tableRef.rows[i]);
			//alert(temp);
			tableRef.insertBefore(temp,tableRef.rows[j])
			}
		}
	}
}

//================ End ===========================