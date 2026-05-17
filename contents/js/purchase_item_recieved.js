RE_NUMBER           = new RegExp(/^[0-9]+$/);
RE_EMAIL    = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);
RE_NAME     = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);
var httpLoadProduct      = getHTTPObject();
var httpSaveProduct 	= getHTTPObject();
var httpLoadPO      	 = getHTTPObject();
var httpLoadUP			 = getHTTPObject();
var isIE            = document.all;
var rsFound = true;
var needSave = false;
var rsArr = Array();

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

function checkTotalUnit(val){		
	 document.getElementById("totalqty_m").innerHTML = " ("+val+")";	
	 document.getElementById("totalqty_m1").innerHTML = " ("+val+")";
}
//********* Product List *********

function getProductList(brandArr)
{ 
	  var voucher_no = document.getElementById('voucher_no').value; 
	  var catagoryArr = document.getElementById('catagory').value; 
	  var catagoryStr = catagoryArr.split("###");
	  var catagory_id = catagoryStr[0];
	  var brandStr = brandArr.split("###");
	  var brand_id = brandStr[0];
	  if(catagory!=""){
		  httpLoadProduct.open("GET","index.php?app=purchase_item_received&cmd=loadProduct&brand_id="+brand_id+"&catagory_id="+catagory_id+"&voucher_no="+voucher_no, true);
		  httpLoadProduct.onreadystatechange = handleLoadResponse;
		  httpLoadProduct.send(null);
	  }
}

function handleLoadResponse()
{
    if(httpLoadProduct.readyState == 4)
    {       
       //alert(httpLoadProduct.responseText);
       processData(httpLoadProduct.responseText);
       //alert(httpLoadProduct.responseText);
    }    

}

function processData(ResponseStr)
{
		//alert(ResponseStr);
		productOption = document.getElementById('product');
		while(productOption.length>0)
		{
			productOption.remove(0);
		}		

		var arrProduct = Array();
				
		arrProduct = ResponseStr.split("@@@");
		productOption.options[0]= new Option("Select One","-1");
		for( i=0;i<arrProduct.length-1; i++)
		{
		  var arrProductIdName = Array();
		  arrProductIdName =	arrProduct[i].split("#####");
		  var details = arrProductIdName[2];
		  if(details==""){
			var details = arrProductIdName[3];
		  }
		  productOption.options[i+1]= new Option(arrProductIdName[1]+'- '+details, arrProductIdName[0]+'###'+arrProductIdName[1]);
		  details="";
		}
		document.getElementById('serial').value ="";document.getElementById('warranty').value ="";
		document.getElementById('qty').value=0;	document.getElementById('serial').disabled =true;document.getElementById('warranty').disabled =true;
		document.getElementById('qty').disabled =true;
			
}
//********* End get Product List ************

//=========== Start Get Purchase Info =====
function getPurchaseOrderList(supplier)
{ 
	  if(supplier!="")
	  {
		  httpLoadPO.open("GET", 'index.php?app=purchase_item_received&cmd=loadPOInfo&supplier='+supplier, true);
		  httpLoadPO.onreadystatechange = handlePOInfoResponse;
		  httpLoadPO.send(null);
	  }
}

function handlePOInfoResponse()
{
    if(httpLoadPO.readyState == 4)
    {       
       //alert(httpLoadProduct.responseText);
       processPOData(httpLoadPO.responseText);
       //alert(httpLoadProduct.responseText);
    }    

}

function processPOData(ResponseStr)
{
		//alert(ResponseStr);
		vouchernoOption = document.getElementById('voucher_no');
		while(vouchernoOption.length>0)
		{
			vouchernoOption.remove(0);
		}		

		var arrVoucher = Array();
				
		arrVoucher = ResponseStr.split("@@@");
		vouchernoOption.options[0]= new Option("Select Order Number","-1");
		for( i=0;i<arrVoucher.length-1; i++)
		{
		  var arrVoucherIdName = Array();
		  arrVoucherIdName =	arrVoucher[i].split("#####");
		  vouchernoOption.options[i+1]= new Option(trim(arrVoucherIdName[0])+", "+arrVoucherIdName[1]+" Tk, "+arrVoucherIdName[2], trim(arrVoucherIdName[0]));
		}	
			
}

//=========== End Get Purchase Info =====
//********* End get Product List ************
function getProductDtl(product_id)
{ 
	  product_idArr 	= product_id.split("###");
	  product_id 		= product_idArr[0];
	  if(product_id!="")
	  {
		  httpLoadUP.open("GET", "index.php?app=purchase_item_received&cmd=getProductDtl&product_id="+product_id, true);
		  httpLoadUP.onreadystatechange = handleUPResponse;
		  httpLoadUP.send(null);
	  }
}

function handleUPResponse()
{
    if(httpLoadUP.readyState == 4){       
       //alert(httpLoadUP.responseText);
	   const data = JSON.parse(trim(httpLoadUP.responseText));

	   document.getElementById('product_name').value= data.product_name;
	   document.getElementById('m_unit').value= data.m_unit;
	   document.getElementById('unit_price').value= data.unit_price;
	   document.getElementById('catagory').value= data.catagory; 
	   document.getElementById('brand').value= data.brand_code;
	   document.getElementById('catagory_name').value= data.catagory_name; 
	   document.getElementById('brand_name').value= data.brand_name;
    } 

}
//********* End getProductDtl ************
function addCol(html) 
{
 var td=document.createElement('td');
 td.setAttribute('bgColor',"#D9D6BF")
 td.innerHTML=html;
 //alert("addCol"+html);
 return td;
}

function addColMSG(html) 
{
 var td=document.createElement('td');
 td.setAttribute('bgColor',"#FFCC66")
 td.innerHTML=html;
 //alert("addCol"+html);
 return td;
}

function addColHead(html) 
{
 td=document.createElement('td');
 fnt = document.createElement('font');
 fnt.setAttribute('color',"#FFFFFF");
 td.setAttribute('bgColor',"#6699FF")
 td.appendChild(fnt);
 td.innerHTML=html;
 //alert(td);
 return td;
}

function remRows(elem)
{
	obj = document.getElementById(elem);
	//alert(obj);
	obj.innerHTML = "";
}

function purchaseProcess()
{
	if (includeGrid())
	{
		prepareGrid();
	}
}

function includeGrid()
{
	var frm = document.frmbuyerorder;
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
		setRequiredField(supplier,			 'dropdown',  'supplier_lbl');	
		setRequiredField(product,          	 'dropdown',  'product_lbl');
		setRequiredField(qty,    			 'textbox',	 'qty_lbl');
	}
}
function fieldValidation(frm)
{
	with(frm)
	{
		if(!RE_DECIMAL.exec(qty.value))
		{
			highlightTableColumn('qty_lbl');
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

function prepareGrid()
{
	var catagoryid 		= document.getElementById('catagory').value;
	var catagoryname 	= document.getElementById('catagory_name').value;
	var brandid 		= document.getElementById('brand').value;
	var brandname 		= document.getElementById('brand_name').value;
	var productid 		= document.getElementById('product').value;
	var productname 	= document.getElementById('product_name').value;

	pdetails 		= document.getElementById('details').value;
	pdetails 		= pdetails.replace('"',"&rdquo;"); pdetails = pdetails.replace('"',"&rdquo;");
	pdetails 		= pdetails.replace('"',"&rdquo;"); pdetails = pdetails.replace('"',"&rdquo;");
	pdetails 		= pdetails.replace('"',"&rdquo;"); pdetails = pdetails.replace('"',"&rdquo;");
	pdetails 		= pdetails.replace('"',"&rdquo;"); pdetails = pdetails.replace('"',"&rdquo;");
	serial 			= document.getElementById('serial').value;
		
	if(serial==""){ serial = 0; }		
	var warranty 		= document.getElementById('warranty').value; 
	if(warranty==""){ warranty = 0; }
	
	var m_unit 		= document.getElementById('m_unit').value;
	var total_unit 		= document.getElementById('total_unit').value;
	var unit_price 		= parseFloat(document.getElementById('unit_price').value);
	var qty 		= parseFloat(document.getElementById('qty').value);
	var unit_discount       = parseFloat(document.getElementById('unit_discount').value); if(isNaN(unit_discount) || unit_discount==""){ unit_discount = 0; }
	var discount_amount     = parseFloat(document.getElementById('discount_amount').value);
	var free_qty		= parseFloat(document.getElementById('free_qty').value); if(isNaN(free_qty) || free_qty==""){ free_qty = 0; }
	var total		= parseFloat(document.getElementById('total').value); if(total==""){ total = 0; }
	var total_bag 	= document.getElementById('total_bag').value;
	if(unit_price==""){ unit_price = 0; } if(qty==""){ qty = 0; } if(discount_amount==""){ discount_amount = 0; } if(total_bag==""){ total_bag = 0; }
	
	var supplier 		= document.getElementById('supplier').value;
	var serial 		= document.getElementById('serial').value;
	var warranty 		= document.getElementById('warranty').value;
	var quotation_no 	= document.getElementById('quotation_no').value;
	var truck_no 		= document.getElementById('track_no').value;
	var store_id 		= document.getElementById('store_id').value;
	var purchase_date 	= document.getElementById('purchase_date').value;
	var edit_product_id 	= document.getElementById('edit_product_id').value;
	var tmp_id 		= document.getElementById('tmp_id').value;

	var po_voucher_no 	= document.getElementById('po_voucher_no').value;
	var pod_id 		= document.getElementById('pod_id').value;
	var max_qty 		= document.getElementById('max_qty').value;
	var edit_product_qty 	= document.getElementById('edit_product_qty').value;
	
	if(productid!=""){
	httpSaveProduct.open("GET","index.php?app=purchase_item_received&cmd=save_tmp_grn&supplier="+supplier+"&store_id="+store_id+"&purchase_date="+purchase_date+"&quotation_no="+quotation_no+"&truck_no="+truck_no+"&productid="+productid+"&catagoryname="+catagoryname
	+"&brandname="+brandname+"&qty="+qty+"&free_qty="+free_qty+"&unit_price="+unit_price+"&unit_discount="+unit_discount
	+"&discount_amount="+discount_amount+"&details="+pdetails+"&serial="+serial+"&warranty="+warranty+"&total="+total+"&edit_product_id="+edit_product_id+"&po_voucher_no="+po_voucher_no+"&pod_id="+pod_id+"&max_qty="+max_qty+"&edit_product_qty="+edit_product_qty+"&tmp_id="+tmp_id, true);
	 httpSaveProduct.onreadystatechange = handleSaveResponse;
	 httpSaveProduct.send(null);
	}
  
} // End of function prepareGrid()


function handleSaveResponse()
{
    if(httpSaveProduct.readyState == 4)
    {    
        const data = JSON.parse(trim(httpSaveProduct.responseText));
		
	document.getElementById('total_value').value = parseFloat(data.table);
	document.getElementById('tbs').innerHTML = data.table;
	document.getElementById('product').focus();
	resetForm();
    } 
}


function wantToSave(e)
{
e.preventDefault();
	var needSave   = true;
	
	if(needSave){
		    if(confirm("Sure want ot submit???")==true){
			const form = document.getElementById('frmbuyerorder');

        		// ✅ Force the real submit method to run, even if it's shadowed
        		HTMLFormElement.prototype.submit.call(form);
		    } else{
		        return false;
		    }
		
	}else{
		alert("Empty data!!! Please enter data first...");
		return false;
	}
}

function calTotalValue()
{		
  	  var qty = document.frmbuyerorder.qty.value;
	  var unit_price = document.frmbuyerorder.unit_price.value;
	   
	     if(qty=='' || qty==0){ qty = 0; }
		 if(unit_price=='' || unit_price==0){ unit_price = 0;}
		 
         var totalvalue = qty * unit_price;
		 document.frmbuyerorder.total.value = totalvalue.toFixed(2);
      
	      
}
function calNetPayble()
{		
  	  var total_value = parseFloat(document.frmbuyerorder.total_value.value);
	  var discount = parseFloat(document.frmbuyerorder.discount.value);
	  //alert(total_value);alert(discount);
	 if(total_value=='' || total_value==0){ total_value = 0; }
	 if(discount=='' || discount==0){ discount = 0;}
	 var netPay = (total_value - discount);  
	 document.frmbuyerorder.net_payble.value =  netPay.toFixed(2);
	      
}
// ===== end Expected Salary ==========

function deleteRecord(id)
{	
   var url_loc = "index.php?app=purchase_order&cmd=delete&id="+id;
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
