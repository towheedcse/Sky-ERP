RE_NUMBER           = new RegExp(/^[0-9]+$/);

RE_EMAIL    = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);

RE_NAME     = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);

var httpSaveVoucher 	= getHTTPObject();
var httpCompleteVoucher = getHTTPObject();
var httpLoadTemp 	= getHTTPObject();
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
function checkType(val){	
	// document.getElementById("single_ah").innerHTML = val+" Account";
	// document.getElementById("single_h").innerHTML  = val;
	 if(val=="Dr"){ var multy_type = "Cr";}else if(val=="Cr"){ var multy_type = "Dr";}
	 document.getElementById("multy_ah").innerHTML = val+" Account";
	 document.getElementById("multy_a").innerHTML  = val+" Amount";
}
function checkBalance(){

	if(includeGrid()){

		if($('#dealer_payment').prop('checked')) {
			var mr_no = document.getElementById('description').value;
			$.ajax({
				type: 'POST',
				url: "index.php?app=contra.voucher.new&cmd=check-mr",
				data: "dealer-payment=1&mr-number="+mr_no,
				success: function(option){
					if(trim(option)==0){
					  saveGRV();
					}else{
					  alert("Duplicate MR Number!!!");
					}
							
				}//Success			
			});// ajax
		}else{
			saveGRV();
		}
	}		
	return true;
		
}

function handleTempResponse()
{
    if(httpLoadTemp.readyState == 4){       
       //alert(httpLoadUP.responseText);
	   var Pcontent = trim(httpLoadTemp.responseText);
	   contentArr	= Pcontent.split("#####");
	   document.getElementById('tmp_id').value=contentArr[0];
	   document.getElementById('headtypes').value=contentArr[1]; 
	   document.getElementById('cr_account').value=contentArr[2];
	   document.getElementById('consignee').value=contentArr[3];
	   document.getElementById('cr_amount').value=contentArr[4]; 
	   document.getElementById('bank_name').value=contentArr[5];
	   document.getElementById('acc_no').value=contentArr[6];
	   document.getElementById('check_no').value=contentArr[7];
	   document.getElementById('check_issue_date').value=contentArr[8];
	   document.getElementById('description').value=contentArr[9];
	   $('#cr_account').trigger('chosen:updated');
	   $('#consignee').trigger('chosen:updated');
	   document.getElementById('headtypes').focus();
    	   document.getElementById('headtypes').select();
    } 

}

function checkDrCr(){
	var totalcr_amount 	= parseFloat(document.getElementById('totalcr_amount').value);
	var totaldr_amount	= parseFloat(document.getElementById('totaldr_amount').value);
	if(isNaN(totalcr_amount)){ totalcr_amount =0;} 
	if(isNaN(totaldr_amount)){ totaldr_amount =0;}
	if(totalcr_amount != totaldr_amount){
		alert("You can not complete this transaction \nbecause Dr Amount("+totaldr_amount+") is not equalto Total Cr Amount("+totalcr_amount+")");
		return false;
	}else if(totalcr_amount == totaldr_amount && totalcr_amount >0){
		var costCenterRequired = document.getElementById('costCenterRequired').value;
		var cost_center = document.getElementById('cost_center').value;
        	if (costCenterRequired == 1 && cost_center === "") {
		    alert("Cost Center is required");
		    return false;
		}
		if(confirm("Sure want ot submit???")==true){
			saveContraVoucher(); return true;
		}else{ return false;}
	}
		
}
function ItemEdit(item_id){
	if(item_id !="")
	{
	  httpLoadTemp.open("GET", "index.php?app=contra.voucher.new&&cmd=get_temp_dtl&item_id="+item_id, true);
	  httpLoadTemp.onreadystatechange = handleTempResponse;
	  httpLoadTemp.send(null);
	}
}

function handleTempResponse()
{
    if(httpLoadTemp.readyState == 4){       
       //alert(httpLoadUP.responseText);
	   var Pcontent = trim(httpLoadTemp.responseText);
	   contentArr	= Pcontent.split("#####");
	   document.getElementById('tmp_id').value=contentArr[0];
	   document.getElementById('headtypes').value=contentArr[1]; 
	   document.getElementById('cr_account').value=contentArr[2];
	   document.getElementById('consignee').value=contentArr[3];
	   document.getElementById('cr_amount').value=contentArr[4]; 
	   document.getElementById('bank_name').value=contentArr[5];
	   document.getElementById('acc_no').value=contentArr[6];
	   document.getElementById('check_no').value=contentArr[7];
	   document.getElementById('check_issue_date').value=contentArr[8];
	   document.getElementById('description').value=contentArr[9];
	   document.getElementById('due_invoice').value=contentArr[10];
	   $('#cr_account').trigger('chosen:updated');
	   $('#consignee').trigger('chosen:updated');
           $('#due_invoice').trigger('change');
	   document.getElementById('headtypes').focus();
    	   document.getElementById('headtypes').select();
    } 

}

function saveGRV()
{ 
	var mr_no 	= document.getElementById('mr_no').value;
	var headtypes 	= document.getElementById('headtypes').value;
	var dr_account 	= "";// document.getElementById('dr_account').value;
	var dr_amount 	= 0; //document.getElementById('dr_amount').value;
	var created_date= document.getElementById('created_date').value;
	currIdName 	= document.getElementById('currency').value;
	var mode_of_payment = document.getElementById('mode_of_payment').value;
	currArr 	= currIdName.split("###");
	var currency 	= currArr[0];
	var currencyName= currArr[1];
	var tmp_id	= document.getElementById('tmp_id').value;
	
	var cr_account 	= document.getElementById('cr_account').value;
	var consignee 	= document.getElementById('consignee').value;
    	var due_invoice = document.getElementById('due_invoice').value;
	
	var cr_amount 	= document.getElementById('cr_amount').value;
	var acc_no 	= document.getElementById('acc_no').value;
	var check_no 	= document.getElementById('check_no').value;
	var chk_issue_date 	= document.getElementById('check_issue_date').value;	
	var bank_name 		= document.getElementById('bank_name').value;		
	var description 	= document.getElementById('description').value;		
	//var cheque_type 	= document.getElementById('cheque_type').value;	
	var cheque_type 	= "Cash Deposit";
	var vouchar_type 	= document.getElementById('vouchar_type').value;
	if (document.getElementById('bank_journalY').checked) {
	  var bank_journal = document.getElementById('bank_journalY').value;
	}else{
	  var bank_journal = document.getElementById('bank_journalN').value;	
	}
	var dealer_payment =0;
	if($('#dealer_payment').prop('checked')) {
	 dealer_payment =1;
	}
	if(headtypes!="" && created_date!="" && cr_account!="" && cr_amount!="" && vouchar_type!="")
	{
	 httpSaveVoucher.open("GET", 'index.php?app=contra.voucher.new&cmd=save_tmp&headtypes='+headtypes+'&dr_account='+dr_account+'&dr_amount='+dr_amount+'&currency='+currency+'&currencyName='+currencyName+'&created_date='+created_date+'&cr_account='+cr_account+'&consignee='+consignee+'&due_invoice='+due_invoice+'&cr_amount='+cr_amount+'&bank_name='+bank_name+'&acc_no='+acc_no+'&check_no='+check_no+'&check_issue_date='+chk_issue_date+'&cheque_type='+cheque_type+'&vouchar_type='+vouchar_type+'&bank_journal='+bank_journal+'&description='+description+'&mode_of_payment='+mode_of_payment+'&dealer_payment='+dealer_payment+"&tmp_id="+tmp_id + "&mr_no=" + mr_no, true);
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
	var tbl 		= arrSaveGRV[0];
 	var totalcr_amount 	= arrSaveGRV[1];
 	var totaldr_amount 	= arrSaveGRV[2];
	var costCenterRequired  = arrSaveGRV[3];
	document.getElementById('totalcr_amount').value = totalcr_amount;
	document.getElementById('totaldr_amount').value = totaldr_amount;
	document.getElementById('tbs').innerHTML = tbl;
	document.getElementById('headtypes').focus();
	document.getElementById('tmp_id').value=0;
	document.getElementById('costCenterRequired').value = costCenterRequired;
    	//document.getElementById('headtypes').select();
        $('#cr_account').val("").trigger('chosen:updated');
        $('#consignee').val("").trigger('chosen:updated');
        $('#due_invoice').val("").trigger('change');
    } 
}
// httpCompleteVoucher
function saveContraVoucher()
{ 
	var mr_no 		= document.getElementById('mr_no').value;
	var created_date	= document.getElementById('created_date').value;
	currIdName 		= document.getElementById('currency').value;
	var mode_of_payment = document.getElementById('mode_of_payment').value;
	currArr 		= currIdName.split("###");
	var currency 		= currArr[0];
	var currencyName 	= currArr[1];
	var collection_source	= document.getElementById('collection_source').value;
	var details		= document.getElementById('details').value;
	var vouchar_type 	= document.getElementById('vouchar_type').value;
	if (document.getElementById('bank_journalY').checked) {
	  var bank_journal = document.getElementById('bank_journalY').value;
	}else{
	  var bank_journal = document.getElementById('bank_journalN').value;	
	}
	var adjustment 		= parseInt(document.getElementById('adjustment').value);
	var beddebts 		= parseInt(document.getElementById('beddebts').value);
	var cost_center 	= document.getElementById('cost_center').value;
	var totaldr_amount 	= parseFloat(document.getElementById('totaldr_amount').value);
	var totalcr_amount 	= parseFloat(document.getElementById('totalcr_amount').value);
	var attachment          = document.getElementById('attachment').files[0];

	if(created_date!="" && totalcr_amount!="" && mode_of_payment!="" && vouchar_type!="")
	{
		// Prepare form data for POST request
		var formData = new FormData();
		formData.append('mr_no', mr_no);
		formData.append('currency', currency);
		formData.append('currencyName', currencyName);
		formData.append('created_date', created_date);
		formData.append('totaldr_amount', totaldr_amount);
		formData.append('totalcr_amount', totalcr_amount);
		formData.append('vouchar_type', vouchar_type);
		formData.append('bank_journal', bank_journal);
		formData.append('details', details);
		formData.append('adjustment', adjustment);
		formData.append('beddebts', beddebts);
		formData.append('mode_of_payment', mode_of_payment);
		formData.append('collection_source', collection_source);
		formData.append('cost_center', cost_center);

		// Append the file (if present)
		if (attachment) {
		    formData.append('attachment', attachment);
		}

		// Send POST request to server
		httpCompleteVoucher.open("POST", 'index.php?app=contra.voucher.new&cmd=save_pending_vouchar', true);
		httpCompleteVoucher.onreadystatechange = handleContraVoucherSaveResponse;
		httpCompleteVoucher.send(formData);

	}
}

function handleContraVoucherSaveResponse()
{
    if(httpCompleteVoucher.readyState == 4)
    {    
	//alert(httpCompleteVoucher.responseText); 
        var CVNo = trim(httpCompleteVoucher.responseText);

	if (CVNo && !isNaN(CVNo)) {
            // Redirect to the print voucher page using the contra_id
	    window.location = "index.php?app=contra.voucher.new&cmd=print_pending_vouchar&tmp_grvid=" + CVNo;
        } else {
            // Handle the case when there is an error (invalid CVNo or other issue)
            alert('Error: creating the voucher try again.');
        }
		
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
		setRequiredField(created_date,       	 'textbox',   'created_date_lbl');
		setRequiredField(currency,           	 'dropdown',  'currency_lbl');
		setRequiredField(headtypes,	     	 'dropdown',  'headtypes_lbl');
		setRequiredField(cr_account,		 'dropdown',  'cr_account_lbl');
		setRequiredField(cr_amount,    		 'textbox',   'cr_amount_lbl');
		setRequiredField(vouchar_type,		 'dropdown',  'vouchar_type_lbl');
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
