RE_NUMBER   = new RegExp(/^[0-9]+$/);
RE_EMAIL    = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);
RE_NAME     = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);
//onClick="if(addInquiry()){saveInquiry();return true;}else{ return false;}
var httpLoadVIP     = getHTTPObject();
var httpLoadAC      = getHTTPObject();
var stafftypeFound  = true;
var isIE            = document.all;
var rsFound  	    = true;
var needSave 	    = false;

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

function getVIPList(vip_src){ 
	  httpLoadVIP.open("GET", "index.php?app=show_ledger&cmd=loadhead&sub_id="+vip_src, true);
	  httpLoadVIP.onreadystatechange = handleLoadVIPResponse;
	  httpLoadVIP.send(null);
}

function handleLoadVIPResponse()
{
    if(httpLoadVIP.readyState == 4){       
       //alert(httpLoadVIP.responseText);
       processVIPData(httpLoadVIP.responseText);
    }    
}

function processVIPData(ResponseStr)
{
	vipOption = document.getElementById('dr_account');
	while(vipOption.length>0){ vipOption.remove(0); }	
		
	var arrVip = Array(); arrVip = ResponseStr.split("@@@");
	vipOption.options[0]= new Option("Select Account Head","");
	for( i=0;i<arrVip.length-1; i++)
	{
	  var arrVipIdName 	= Array();  arrVipIdName = arrVip[i].split("#####");
	  vipOption.options[i+1]= new Option(arrVipIdName[0]+' , '+arrVipIdName[1],arrVipIdName[0]);
	}	
}

function getAccList(cr_src){ 
	  httpLoadAC.open("GET", "index.php?app=show_ledger&cmd=loadhead&sub_id="+cr_src, true);
	  httpLoadAC.onreadystatechange = handleLoadACResponse;
	  httpLoadAC.send(null);
}

function handleLoadACResponse()
{
    if(httpLoadAC.readyState == 4){       
       //alert(httpLoadVIP.responseText);
       processACData(httpLoadAC.responseText);
    }    
}

function processACData(ResponseStr)
{
	vipOption = document.getElementById('cr_account');
	while(vipOption.length>0){ vipOption.remove(0); }	
		
	var arrVip = Array(); arrVip = ResponseStr.split("@@@");
	vipOption.options[0]= new Option("Select Cr Account Head","");
	for( i=0;i<arrVip.length-1; i++)
	{
	  var arrVipIdName 	= Array();  arrVipIdName = arrVip[i].split("#####");
	  vipOption.options[i+1]= new Option(arrVipIdName[0]+' , '+arrVipIdName[1],arrVipIdName[0]);
	}	
}


function formSetups()
{
	var frm = document.frmjournal;
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
	setRequiredField(vouchar_type,		'dropdown',  'vouchar_type_lbl');	
	setRequiredField(dr_account,            'dropdown',  'dr_account_lbl');
	setRequiredField(cr_account,		'dropdown',  'cr_account_lbl');
	setRequiredField(amount,         	'textbox',   'amount_lbl');
	}
}
function fieldValidation(frm)
{
	with(frm)
	{
		if(!RE_DECIMAL.exec(unit_price.value))
		{
			highlightTableColumn('amount_lbl');
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
