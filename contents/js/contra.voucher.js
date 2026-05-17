RE_NUMBER           = new RegExp(/^[0-9]+$/);

RE_EMAIL    = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);

RE_NAME     = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);

var httpSaveVoucher = getHTTPObject();
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
function checkType(val){	
	 document.getElementById("single_ah").innerHTML = val+" Account";
	 document.getElementById("single_h").innerHTML  = val;
	 if(val=="Dr"){ var multy_type = "Cr";}else if(val=="Cr"){ var multy_type = "Dr";}
	 document.getElementById("multy_ah").innerHTML = multy_type+" Account";
	 document.getElementById("multy_a").innerHTML  = multy_type+" Amount";
}
function checkBalance(){
	
	var totalcr_amount 	= parseFloat(document.getElementById('totalcr_amount').value);
	var dr_amount 		= parseFloat(document.getElementById('dr_amount').value);
	var cr_amount 		= parseFloat(document.getElementById('cr_amount').value);
	if(isNaN(totalcr_amount)){ totalcr_amount =0; alert(totalcr_amount);} if(isNaN(dr_amount)){ dr_amount =0;} if(isNaN(cr_amount)){ cr_amount =0;} 
	if((totalcr_amount+cr_amount)>dr_amount){
		alert("You can not complete this transaction \nbecause Dr Amount("+dr_amount+") is not equalto Total Cr Amount("+totalcr_amount+")");
		return false;
	}else if((totalcr_amount+cr_amount)<=dr_amount){
		if(includeGrid()){
			saveGRV();
		}		
		return true;
	}
		
}
function saveGRV()
{ 
	var custom_voucher_no 	= document.getElementById('custom_voucher_no').value;
	var headtypes 	= document.getElementById('headtypes').value;
	var dr_account 	= document.getElementById('dr_account').value;
	var dr_amount 	= document.getElementById('dr_amount').value;
	var created_date= document.getElementById('created_date').value;
	currIdName 			= document.getElementById('currency').value;
	var mode_of_payment = document.getElementById('mode_of_payment').value;
	currArr 			= currIdName.split("###");
	var currency 		= currArr[0];
	var currencyName 	= currArr[1];
	
	
	var cr_account 	= document.getElementById('cr_account').value;
	var cr_amount 	= document.getElementById('cr_amount').value;
	var acc_no 		= document.getElementById('acc_no').value;
	var check_no 	= document.getElementById('check_no').value;
	var chk_issue_date 	= document.getElementById('check_issue_date').value;	
	var bank_name 		= document.getElementById('bank_name').value;		
	var description 	= document.getElementById('description').value;		
	//var cheque_type 	= document.getElementById('cheque_type').value;	
	var cheque_type 	= "Cash Deposit";
	var vouchar_type 	= document.getElementById('vouchar_type').value;
	var transaction_type = document.getElementById('transaction_type').value;
	if(dr_account!="" && dr_amount!="" && created_date!="" && cr_account!="" && cr_amount!="" && vouchar_type!="")
	{
	httpSaveVoucher.open("GET", 'index.php?app=contra.voucher&cmd=save_tmp&headtypes='+headtypes+'&dr_account='+dr_account+'&dr_amount='+dr_amount+'&currency='+currency+'&currencyName='+currencyName+'&created_date='+created_date+'&cr_account='+cr_account+'&cr_amount='+cr_amount+'&bank_name='+bank_name+'&acc_no='+acc_no+'&check_no='+check_no+'&check_issue_date='+chk_issue_date+'&cheque_type='+cheque_type+'&vouchar_type='+vouchar_type+'&transaction_type='+transaction_type+'&description='+description+'&mode_of_payment='+mode_of_payment, true);
	 httpSaveVoucher.onreadystatechange = handleSaveResponse;
	 httpSaveVoucher.send(null);
	}
}

function handleSaveResponse()
{
    if(httpSaveVoucher.readyState == 4)
    {    
		//alert(httpSaveVoucher.responseText); 
        var GRVValue 		= trim(httpSaveVoucher.responseText);
 		var arrSaveGRV 		= GRVValue.split("####-@@@@");
		var tbl 			= arrSaveGRV[0];
	 	var totalcr_amount 	= arrSaveGRV[1];
        document.getElementById('totalcr_amount').value = totalcr_amount;
		document.getElementById('tbs').innerHTML = tbl;
    } 
}

function includeGrid()
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
		setRequiredField(created_date,       'textbox',  'created_date_lbl');
		setRequiredField(dr_account,		 'dropdown',  'dr_account_lbl');
		setRequiredField(currency,           'dropdown',  'currency_lbl');
		setRequiredField(headtypes,	 		 'dropdown',  'headtypes_lbl');
		setRequiredField(dr_amount,          'textbox',   'dr_amount_lbl');
		setRequiredField(cr_account,		 'dropdown',  'cr_account_lbl');
		setRequiredField(cr_amount,    		 'textbox',	 'cr_amount_lbl');
		setRequiredField(vouchar_type,		 'dropdown', 'vouchar_type_lbl');
	}
}
function fieldValidation(frm)
{
	with(frm)
	{
		if(!RE_DECIMAL.exec(cr_amount.value))
		{
			highlightTableColumn('cr_amount_lbl');
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