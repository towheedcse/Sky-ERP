RE_NUMBER   = new RegExp(/^[0-9]+$/);
RE_EMAIL    = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);
RE_NAME     = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);

var httpLoadProduct     = getHTTPObject();
var httpSaveProduct 	= getHTTPObject();
var httpLoadUP 		= getHTTPObject();

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
function checkUnit(val){	
	 document.getElementById("qty_m").innerHTML = " ("+val+")";
	 document.getElementById("uprice_m").innerHTML =" (per "+val+")";
}

function checkTotalUnit(val){	
	 document.getElementById("totalqty_m").innerHTML = " ("+val+")";	
	 document.getElementById("totalqty_m1").innerHTML = " ("+val+")";
}

function getProductList(brandArr){ 
	  var catagoryArr = document.getElementById('catagory').value; 
	  var catagoryStr = catagoryArr.split("###");
	  var catagory_id = catagoryStr[0];
	  var brandStr = brandArr.split("###");
	  var brand_id = brandStr[0];
	  if(brand_id!="") { 	
		  httpLoadProduct.open("GET", "index.php?app=purchase.item&cmd=loadProduct&brand_id="+brand_id+"&catagory_id="+catagory_id, true);
		  httpLoadProduct.onreadystatechange = handleLoadResponse;
		  httpLoadProduct.send(null);
	  }
}

function handleLoadResponse()
{
    if(httpLoadProduct.readyState == 4){       
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
		productOption.options[0]= new Option("Select One","0");
		for( i=0;i<arrProduct.length-1; i++)
		{
		  var arrProductIdName = Array();
		  arrProductIdName  = arrProduct[i].split("#####");
		  var details 	   	= arrProductIdName[2];		  
		  productOption.options[i+1]= new Option(arrProductIdName[1]+'-'+details, arrProductIdName[0]+'###'+arrProductIdName[1]);
		}	
			
}
//********* End get Product List ************
function getProductDtl(product_id)
{ 
	  product_idArr 	= product_id.split("###");
	  product_id 		= product_idArr[0];
	  if(product_id!="")
	  {
		  httpLoadUP.open("GET", "index.php?app=purchase.item&cmd=get_dtl&product_id="+product_id, true);
		  httpLoadUP.onreadystatechange = handleUPResponse;
		  httpLoadUP.send(null);
	  }
}

function handleUPResponse()
{
    if(httpLoadUP.readyState == 4){       
       //alert(httpLoadUP.responseText);
	   var UPcontent = trim(httpLoadUP.responseText);
	   contentArr	 = UPcontent.split("#####");
	   document.getElementById('m_unit').value=contentArr[0];
	   document.getElementById('details').value=contentArr[1];

        var unit_price = document.getElementById('unit_price').value;
        if (unit_price == "" || unit_price <= 0) {
            unit_price = contentArr[2];
        }

	   document.getElementById('unit_price').value=unit_price;
	   document.getElementById('catagory').value=contentArr[3]; 
	   document.getElementById('brand').value=contentArr[4];
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

function salesProcess()
{
	if (includeGrid())
	{
		prepareTblGrid(); // prepareGrid();
		return true;
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
		
		setRequiredField(supplier,		'dropdown',  'supplier_lbl');
		setRequiredField(inventory_type,	'dropdown',  'inventorytype_lbl');	
		setRequiredField(catagory,           	'dropdown',  'catagory_lbl');
		setRequiredField(brand,			'dropdown',  'brand_lbl');
		setRequiredField(product,          	'dropdown',  'product_lbl');
		setRequiredField(unit_price,         	'textbox',   'unit_price_lbl');
		setRequiredField(qty,    		'textbox',	 'qty_lbl');
		setRequiredField(purchase_date,		'textbox',	 'purchase_date_lbl');
		setRequiredField(total,         	'textbox',   'amount_lbl');
	}
}
function fieldValidation(frm)
{
	with(frm)
	{
		if(!RE_DECIMAL.exec(unit_price.value))
		{
			highlightTableColumn('unit_price_lbl');
			alert(ERROR_NUMBER);		
			return false;
		}
		else if(!RE_DECIMAL.exec(qty.value))
		{
			highlightTableColumn('qty_lbl');
			alert(ERROR_NUMBER);
			return false;
		}else if(!RE_DECIMAL.exec(unit_discount.value))
		{
			highlightTableColumn('unit_discount_lbl');
			alert(ERROR_NUMBER);
			return false;
		}
		else if(product.value==0)
		{
			highlightTableColumn('product_lbl');			
			return false;
		}
		else
		{
			return true;
		}
	}
	return true;
}

function prepareTblGrid()
{	
	catagoryIdName 		= document.getElementById('catagory').value;
	catagory 		= catagoryIdName.split("###");
	var catagoryid 		= catagory[0];
	var catagoryname 	= catagory[1];
	
	brandIdName 		= document.getElementById('brand').value;
	brand 			= brandIdName.split("###");
	var brandid 		= brand[0];
	var brandname 		= brand[1];
	
	productIdName 		= document.getElementById('product').value;
	var product 		= productIdName.split("###");
	//pvoucher_no 		= trim(product[0]);
	pvoucher_no 		= "";
	var productid 		= trim(product[0]);
	var productname 	= product[1];
	pdetails 		= document.getElementById('details').value;
	pdetails 		= pdetails.replace('"',"&rdquo;"); pdetails = pdetails.replace('"',"&rdquo;");
	pdetails 		= pdetails.replace('"',"&rdquo;"); pdetails = pdetails.replace('"',"&rdquo;");
	pdetails 		= pdetails.replace('"',"&rdquo;"); pdetails = pdetails.replace('"',"&rdquo;");
	pdetails 		= pdetails.replace('"',"&rdquo;"); pdetails = pdetails.replace('"',"&rdquo;");
	serial 			= document.getElementById('serial').value;		
	if(serial==""){ serial = 0; }		
	catagory_product_id 	= catagoryid+'###'+brandid+'###'+productid+'###'+serial+'###'+unit_discount;
	var inventory_type 	= document.getElementById('inventory_type').value;
	var warranty 		= document.getElementById('warranty').value; if(warranty==""){ warranty = 0; }	
	var m_unit 		= document.getElementById('m_unit').value;
	var total_unit 		= document.getElementById('total_unit').value;
	var unit_price 		= parseFloat(document.getElementById('unit_price').value);
	var qty 		= parseFloat(document.getElementById('qty').value);
	var unit_discount       = parseFloat(document.getElementById('unit_discount').value); if(unit_discount==""){ unit_discount = 0; }
	var discount_amount     = parseFloat(document.getElementById('discount_amount').value);
	var free_qty		= parseFloat(document.getElementById('free_qty').value); if(free_qty==""){ free_qty = 0; }
	var total		= parseFloat(document.getElementById('total').value); if(total==""){ total = 0; }
	var total_bag 	= document.getElementById('total_bag').value;
	if(unit_price==""){ unit_price = 0; } if(qty==""){ qty = 0; } if(discount_amount==""){ discount_amount = 0; } if(total_bag==""){ total_bag = 0; }
	currencyIdName 		= document.getElementById('currency').value;
	currencyArr  		= currencyIdName.split("###");
	var currency 	 	= currencyArr[0];
	var currencyName 	= currencyArr[1];
	var supplier 		= document.getElementById('supplier').value;
	var serial 			= document.getElementById('serial').value;
	var warranty 		= document.getElementById('warranty').value;
	var store_id 		= document.getElementById('store_id').value;
	var purchase_date 	= document.getElementById('purchase_date').value;
	var edit_product_id 	= document.getElementById('edit_product_id').value;
	var tmp_id 		= document.getElementById('tmp_id').value;

	var po_voucher_no 	= document.getElementById('po_voucher_no').value;
	var pod_id 		= document.getElementById('pod_id').value;
	var max_qty 		= document.getElementById('max_qty').value;
	var edit_product_qty 	= document.getElementById('edit_product_qty').value;

	var received_date 	= ""; //document.getElementById('received_date').value;
	
	if(productid!=""){
	httpSaveProduct.open("GET","index.php?app=purchase.item&cmd=save_tmp&supplier="+supplier+"&store_id="+store_id+"&purchase_date="+purchase_date+"&inventory_type="+inventory_type+"&currency="+currency+"&currencyName="+currencyName+"&received_date="+received_date+"&productid="+productid+"&catagoryname="+catagoryname
	+"&brandname="+brandname+"&qty="+qty+"&free_qty="+free_qty+"&unit_price="+unit_price+"&unit_discount="+unit_discount
	+"&discount_amount="+discount_amount+"&details="+pdetails+"&serial="+serial+"&warranty="+warranty+"&total="+total+"&edit_product_id="+edit_product_id+"&po_voucher_no="+po_voucher_no+"&pod_id="+pod_id+"&max_qty="+max_qty+"&edit_product_qty="+edit_product_qty+"&tmp_id="+tmp_id, true);
	 httpSaveProduct.onreadystatechange = handleSaveResponse;
	 httpSaveProduct.send(null);
	}
  		  
}



function handleSaveResponse()
{
    if(httpSaveProduct.readyState == 4)
    {    
		//alert(httpSaveProduct.responseText); 
        var salesValue 		= trim(httpSaveProduct.responseText);
 		var arrSaveOrder 	= salesValue.split("####-@@@@");
		var tbl 		= arrSaveOrder[0];
	 	var total_amount 	= parseFloat(arrSaveOrder[1]);
		var discount 		= parseFloat(arrSaveOrder[2]);
		if(total_amount>0){ needSave = true;}
		document.getElementById('total_value').value = (total_amount+discount);
        document.getElementById('total_amount').value = total_amount;
		document.getElementById('net_payble').value = total_amount;
		document.getElementById('discount').value = discount;
		document.getElementById('tbs').innerHTML = tbl;
		document.getElementById('product').focus();
	    //document.getElementById('product').select();
		resetForm();
	    calculateAllValue();
    } 
}


var rsArr = Array();
function rowFound(idx)
{
	var psh = false;
	var arrlen = rsArr.length;
	if(rsArr.length == 0)
	{		
		rsArr.push(document.getElementById('catagory_product_id[]').value);
	}
	else
	{
		for(var i=0; i <= arrlen-1; i++)
		{
			if(rsArr[i] == idx)
			{
				alert ("You have already chosen!!"); psh = false;break;
			}else{
				rsArr.push(idx);
				psh = true;
			}
		}
	}
	return psh;	
} //End of rowFound()

function wantToSave(e)
{
	e.preventDefault();
	var inventory_type = $("#inventory_type").val();
	 if(inventory_type == ""){
	     alert("please select inventory type");
	     return false;
	}

	var needSave = parseFloat(document.getElementById('total_amount').value);
	//if(total_amount>0){ 
	    needSave = true;
	//}
	if(needSave)
	{
		if(confirm("Sure want ot submit???")==true){
			const form = document.getElementById('frmbuyerorder');
            		HTMLFormElement.prototype.submit.call(form);
			
		}
	}
	else
	{
		alert("Empty data!!! Please enter data first...");
	}
}

function calTotalValue()
{		
  	  var qty = parseFloat(document.frmbuyerorder.qty.value);
	  var unit_price = document.frmbuyerorder.unit_price.value;
	  var unit_discount = parseFloat(document.frmbuyerorder.unit_discount.value);
	  var product_discount = parseFloat(document.frmbuyerorder.discount.value);
	  var discount_amount = parseFloat(document.frmbuyerorder.discount_amount.value);

	  var tmp_id = document.frmbuyerorder.tmp_id.value;
	  var pod_id = document.frmbuyerorder.pod_id.value;

	  var productIdName 	= document.getElementById('product').value;
	  var product 		= productIdName.split("###");
	  var productid 	= trim(product[0]);
	  var edit_product_id   = document.frmbuyerorder.edit_product_id.value;
	  var max_qty = parseFloat(document.frmbuyerorder.max_qty.value);

	  if(tmp_id != "" && pod_id != "" && edit_product_id == productid && qty > max_qty){
		document.getElementById('qty').value = max_qty;
		qty = max_qty;
		alert(`Product Qty must be less than or equal to ${max_qty}`);
		return false;
	  }
	   
	 if(isNaN(qty)){ qty = 0; } if(isNaN(unit_price)){ unit_price = 0;}	
     var totalvalue = qty * unit_price;
	 var discountAmount = ((totalvalue/100)*unit_discount);
	 totalvalue = (totalvalue-discountAmount);
	 document.frmbuyerorder.total.value = totalvalue.toFixed(2);		
	 document.frmbuyerorder.discount_amount.value = discountAmount.toFixed(2);
}
function calGDiscount()
{		
  	 var total_amount = parseFloat(document.frmbuyerorder.total_amount.value);
	 var general_discount_percent = parseFloat(document.frmbuyerorder.general_discount_percent.value);
	 	 
	 if(isNaN(total_amount)){ total_amount = 0; } if(isNaN(general_discount_percent)){ general_discount_percent = 0;}	
	 var general_discount_amount = ((total_amount/100)*general_discount_percent);
	 var netPay = (total_amount - general_discount_amount); 
	 document.frmbuyerorder.general_discount_amount.value=general_discount_amount.toFixed(2);
	 document.frmbuyerorder.net_payble.value =  netPay.toFixed(2);
	 calNetPayble();
}

function calVat() {
    var total_amount = parseFloat(document.frmbuyerorder.total_amount.value) || 0;
    var vat_percent = parseFloat(document.frmbuyerorder.vat_percent.value) || 0;
    var net_payble = parseFloat(document.frmbuyerorder.net_payble.value) || 0;

    var vat_type = document.getElementById('total_vat_type').value; // percent | fixed

    if (isNaN(total_amount)) {
        total_amount = 0;
    }
    if (isNaN(vat_percent)) {
        vat_percent = 0;
    }
    if (isNaN(net_payble)) {
        net_payble = 0;
    }

    if (vat_percent < 0) vat_percent = 0;
    var total_vat_amount = 0;

    if (vat_type === 'percent') {
        // limit percentage
        if (vat_percent > 100) vat_percent = 100;
        document.getElementById('vat_percent').value = vat_percent;
        // calculate % VAT
        total_vat_amount = ((total_amount / 100) * vat_percent);
    } else {
        // fixed VAT amount
        total_vat_amount = vat_percent;
    }

    document.frmbuyerorder.vat_amount.value = total_vat_amount.toFixed(9);
    //document.frmbuyerorder.net_payble.value = netPay.toFixed(2);

    //calNetPayble();
}

function calVatAmount() {
    var total_amount = parseFloat(document.frmbuyerorder.total_amount.value);
    var vat_amount = parseFloat(document.frmbuyerorder.vat_amount.value);

    if (isNaN(total_amount)) {
        total_amount = 0;
    }
    if (isNaN(vat_amount)) {
        vat_amount = 0;
    }

    var vat_percent = ((vat_amount / total_amount) * 100);

    document.frmbuyerorder.vat_percent.value = vat_percent.toFixed(2);
}

function calAT() {
    var total_amount = parseFloat(document.frmbuyerorder.total_amount.value);
    var AT_percent = parseFloat(document.frmbuyerorder.AT_percent.value);

    if (isNaN(total_amount)) {
        total_amount = 0;
    }
    if (isNaN(AT_percent)) {
        AT_percent = 0;
    }

    var AT_amount = ((total_amount / 100) * AT_percent);
    document.frmbuyerorder.AT_amount.value = AT_amount.toFixed(2);
}

function calATAmount() {
    var total_amount = parseFloat(document.frmbuyerorder.total_amount.value);
    var AT_amount = parseFloat(document.frmbuyerorder.AT_amount.value);

    if (isNaN(total_amount)) {
        total_amount = 0;
    }
    if (isNaN(AT_amount)) {
        AT_amount = 0;
    }

    var AT_percent = ((AT_amount / total_amount) * 100);

    document.frmbuyerorder.AT_percent.value = AT_percent.toFixed(2);
}

function calExDiscount()
{		
  	 var total_amount 				= parseFloat(document.frmbuyerorder.total_amount.value);
	 var exclusive_discount_percent = parseFloat(document.frmbuyerorder.exclusive_discount_percent.value);
	 var general_discount_amount 	= parseFloat(document.frmbuyerorder.general_discount_amount.value);
    	 var vat_amount = parseFloat(document.frmbuyerorder.vat_amount.value);
	 
	 if(isNaN(total_amount)){ total_amount = 0; } if(isNaN(exclusive_discount_percent)){ exclusive_discount_percent = 0;}	
	 if(isNaN(general_discount_amount)){ general_discount_amount = 0; }
	    if (isNaN(vat_amount)) {
		vat_amount = 0;
	    }
	 var NetAmount = (total_amount-general_discount_amount);
	 var exclusive_discount_amount = ((NetAmount/100)*exclusive_discount_percent);
	 var netPay = (NetAmount - exclusive_discount_amount); 
	 document.frmbuyerorder.exclusive_discount_amount.value=exclusive_discount_amount.toFixed(2);
	 document.frmbuyerorder.net_payble.value =  netPay.toFixed(2);
	 calNetPayble();
}

function calculateAllValue() {
    calVat();
    calAT();
    calDueAmount();
}

function calNetPayble()
{		
	calculateAllValue();

  	 var total_amount 				= parseFloat(document.frmbuyerorder.total_amount.value);
	 var general_discount_amount 	= parseFloat(document.frmbuyerorder.general_discount_amount.value);
	 var exclusive_discount_amount 	= parseFloat(document.frmbuyerorder.exclusive_discount_amount.value);
	 var additional_discount 		= parseFloat(document.frmbuyerorder.additional_discount.value);
    	 var vat_amount = parseFloat(document.frmbuyerorder.vat_amount.value);
	 	 
	 if(isNaN(total_amount)){ total_amount = 0; } if(isNaN(additional_discount)){ additional_discount = 0;}	
	 if(isNaN(general_discount_amount)){ general_discount_amount = 0; } if(isNaN(exclusive_discount_amount)){ exclusive_discount_amount = 0;}	
if (isNaN(vat_amount)) {
        vat_amount = 0;
    } 
	 var netPay = (total_amount - (additional_discount+general_discount_amount+exclusive_discount_amount));  
	 document.frmbuyerorder.net_payble.value =  netPay.toFixed(2);
	      
}
function calDueAmount()
{		
  	 var net_payble = parseFloat(document.frmbuyerorder.net_payble.value);
	 var advanced_paid_amount = parseFloat(document.frmbuyerorder.advanced_paid_amount.value);
	 var paid_amount = parseFloat(document.frmbuyerorder.paid_amount.value);
	  //alert(net_payble);alert(paid_amount);
	 if(isNaN(net_payble)){ net_payble = 0; } if(isNaN(advanced_paid_amount)){ advanced_paid_amount = 0;}
	 if(isNaN(paid_amount)){ paid_amount = 0; } 
	 var totDue = (net_payble - (paid_amount+advanced_paid_amount));   
	 document.frmbuyerorder.due.value = totDue.toFixed(2);   	      
}
function creditSale(){
	document.frmbuyerorder.paid_amount.value = 0; 
	calDueAmount();  
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
