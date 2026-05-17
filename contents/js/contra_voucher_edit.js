RE_NUMBER           = new RegExp(/^[0-9]+$/);

RE_EMAIL    = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);

RE_NAME     = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);

var httpCompleteVoucher = getHTTPObject();
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
	// document.getElementById("single_ah").innerHTML = val+" Account";
	// document.getElementById("single_h").innerHTML  = val;
	 if(val=="Dr"){ var multy_type = "Cr";}else if(val=="Cr"){ var multy_type = "Dr";}
	 document.getElementById("multy_ah").innerHTML = val+" Account";
	 document.getElementById("multy_a").innerHTML  = val+" Amount";
}

function checkDrCr(){
	
	var totalcr_amount 	= parseFloat(document.getElementById('totalcr_amount').value);
	var totaldr_amount	= parseFloat(document.getElementById('totaldr_amount').value);
	if(isNaN(totalcr_amount)){ totalcr_amount =0; alert(totalcr_amount);} if(isNaN(totaldr_amount)){ totaldr_amount =0;}
	if(totalcr_amount!=totaldr_amount){
		alert("You can not complete this transaction \nbecause Dr Amount("+totaldr_amount+") is not equalto Total Cr Amount("+totalcr_amount+")");
		return false;
	}else if(totalcr_amount==totaldr_amount){
		if(confirm("Sure want ot submit???")==true){
			return true;
		}else{ return false;}
	}
		
}
// httpCompleteVoucher
function saveContraVoucher()
{ 
	var contra_id 		= document.getElementById('contra_id').value;
	var voucher_no 		= document.getElementById('voucher_no').value;
	var sl 				= document.getElementById('sl').value;
	var created_date	= document.getElementById('created_date').value;
	var mode_of_payment = document.getElementById('mode_of_payment').value;
	var details			= document.getElementById('details').value;
	var vouchar_type 	= document.getElementById('vouchar_type').value;
	if (document.getElementById('bank_journalY').checked) {
	  var bank_journal = document.getElementById('bank_journalY').value;
	}else{
	  var bank_journal = document.getElementById('bank_journalN').value;	
	}
	var totaldr_amount 	= parseFloat(document.getElementById('totaldr_amount').value);
	var totalcr_amount 	= parseFloat(document.getElementById('totalcr_amount').value);
	if(totaldr_amount!="" && created_date!="" && totalcr_amount!="" && mode_of_payment!="" && vouchar_type!="")
	{
		alert(contra_id+"-"+voucher_no);
	httpCompleteVoucher.open("GET", 'index.php?app=contra.voucher.edit&cmd=save_vouchar&contra_id='+contra_id+'&voucher_no='+voucher_no+'&created_date='+created_date+'&totaldr_amount='+totaldr_amount+'&totalcr_amount='+totalcr_amount+'&vouchar_type='+vouchar_type+'&bank_journal='+bank_journal+'&details='+details+'&mode_of_payment='+mode_of_payment+'&sl='+sl+'&collection_source='+collection_source, true);
	 httpCompleteVoucher.onreadystatechange = handleContraVoucherSaveResponse;
	 httpCompleteVoucher.send(null);
	}
}

function handleContraVoucherSaveResponse()
{
    if(httpCompleteVoucher.readyState == 4)
    {    
		alert(httpCompleteVoucher.responseText); 
        var CVNo 		= trim(httpCompleteVoucher.responseText);
		//window.location="index.php?app=contra.voucher.new&cmd=print_vouchar&contra_id="+CVNo;
		
    } 
}
function calTotalDrAmount(){
	var ttlfields = parseInt(document.getElementById("sl").value); 
	var j=1; var TotalDrAmount=0;
	for(j; j < ttlfields; j++){
		var dr_amount = parseFloat(document.getElementById('dr_amount'+j).value);
		if(isNaN(dr_amount)){ dr_amount = 0; } 
		TotalDrAmount+=dr_amount;
	} 
	document.getElementById('totaldr_amount').value=TotalDrAmount.toFixed(2);
}
function calTotalCrAmount(){
	var ttlfields = parseInt(document.getElementById("sl").value); 
	var j=1; var TotalCrAmount=0;
	for(j; j < ttlfields; j++){
		var cr_amount = parseFloat(document.getElementById('cr_amount'+j).value);
		if(isNaN(cr_amount)){ cr_amount = 0; } 
		TotalCrAmount+=cr_amount;
	} 
	document.getElementById('totalcr_amount').value=TotalCrAmount.toFixed(2);
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