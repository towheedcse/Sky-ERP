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
   var frm = document.account_head;

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
   	  setRequiredField(sub_head_name,             		 'textbox',   'sub_head_name');
	  setRequiredField(head_type,             		 	'textbox',   'head_type');
   }

}

function validateFields(frm)
{
   with(frm)
   {	   
   } 
   return true;
}

function frmSubmit()
{ 
  if(!doFormSubmit())	
  {	
  	   document.getElementById('footer_status_msg').style.display = "block";
       document.getElementById('footer_status_msg').innerHTML = "Please enter value for all red marked required field(s).";
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

	  document.account_head.id.value	= document.getElementById('sub_id'+id).value;
	  var sub_head_name 				= document.getElementById('sub_head_name'+id).value;  
	  var head_type 					= document.getElementById('head_type'+id).value;   
	  var head_details 					= document.getElementById('head_details'+id).value;  		  
	  document.account_head.sub_head_name.value 		= sub_head_name;
	  document.account_head.head_type.value 		= head_type;
	  document.account_head.head_details.value 		= head_details;
	  document.getElementById('footer_status_msg').style.display = "block";
      document.getElementById('footer_status_msg').innerHTML = "<font color='#FF3333'>Now You Can Update This Record.</font>";
	  document.getElementById('status_msg').style.display = "none";

}

function deleteRecord(eid)
{	
   var url_loc = "index.php?app=account_head&cmd=delete&id="+eid;
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