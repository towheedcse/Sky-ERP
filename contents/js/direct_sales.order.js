RE_NUMBER   = new RegExp(/^[0-9]+$/);
RE_EMAIL    = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);
RE_NAME     = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);

var httpLoadProduct     = getHTTPObject();
var httpSaveProduct 	= getHTTPObject();
var httpLoadUP 		= getHTTPObject();
var httpLoadTemp 	= getHTTPObject();
var httpLoadTempSales 	= getHTTPObject();
var httpLoadUDP 	= getHTTPObject();
var httpLoadInvoice 	= getHTTPObject();
var httpDeleteTemp 	= getHTTPObject();

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

function loadCustomerOrder(){
	var customer_id		= document.getElementById('customer').value;
	
	if(customer_id !="")
	{	
		httpLoadTempSales.open("GET", "index.php?app=sales.order&cmd=get_temp_order&customer="+customer_id, true);
		httpLoadTempSales.onreadystatechange = handleCustomerResponse;
		httpLoadTempSales.send(null);
	}else{
		alert("Please Select Customer");
	}
}

function handleCustomerResponse(){
	if(httpLoadTempSales.readyState == 4){  
		//alert(httpLoadUDP.responseText); 
        	var salesValue 		= trim(httpLoadTempSales.responseText);
 		var arrSaveOrder 	= salesValue.split("####-@@@@");
		var tbl 		= arrSaveOrder[0];
	 	var total_amount 	= parseFloat(arrSaveOrder[1]);
		var discount 		= parseFloat(arrSaveOrder[2]);
		var customerbalance = parseFloat(arrSaveOrder[3]);
		var customer_limit  = parseFloat(arrSaveOrder[4]);
		if(total_amount>0){ needSave = true;}
		document.getElementById('total_value').value  	 = total_amount;
        	document.getElementById('total_amount').value 	 = total_amount;
		document.getElementById('net_payble').value   	 = total_amount;
		document.getElementById('discount').value 	 = discount;
		document.getElementById('customerbalance').value = customerbalance;
		document.getElementById('customer_limit').value  = customer_limit;		
		document.getElementById('tbs').innerHTML 	 = tbl;
		document.getElementById('product').focus();
	}
}


function loadUndeliveryOrder(){
	var customer_id		= document.getElementById('customer').value;
	var delivery_point 	= document.getElementById('delivery_point').value;
	var sales_date 		= document.getElementById('sales_date').value;
	var delivery_date 	= document.getElementById('delivery_date').value;
	var voucher_no 		= document.getElementById('undelivery_invoice').value;
	
	if(customer_id !="" && delivery_point !="" && sales_date!="" && delivery_date!="" && voucher_no!="")
	{	
		httpLoadUDP.open("GET", "index.php?app=sales.order&cmd=get_undelivery_direct&customer_id="+customer_id+"&voucher_no="+voucher_no+"&delivery_point="+delivery_point+"&sales_date="+sales_date+"&delivery_date="+delivery_date, true);
		httpLoadUDP.onreadystatechange = handleUDPResponse;
		httpLoadUDP.send(null);
	}else{
		alert("Please Select Customer, Delivery Point, Sales Date, Delivery Date");
	}
}

function handleUDPResponse(){
	if(httpLoadUDP.readyState == 4){  
		//alert(httpLoadUDP.responseText); 
        	var salesValue 		= trim(httpLoadUDP.responseText);
 		var arrSaveOrder 	= salesValue.split("####-@@@@");
		var tbl 		= arrSaveOrder[0];
	 	var total_amount 	= parseFloat(arrSaveOrder[1]);
		var discount 		= parseFloat(arrSaveOrder[2]);
		var customerbalance 	= parseFloat(arrSaveOrder[3]);
		var customer_limit  	= parseFloat(arrSaveOrder[4]);
		var general_discount_percent  	= parseFloat(arrSaveOrder[5]);
		var general_discount_amount  	= parseFloat(arrSaveOrder[6]);
		var exclusive_discount_percent 	= parseFloat(arrSaveOrder[7]);
		var exclusive_discount_amount  	= parseFloat(arrSaveOrder[8]);
		var additional_discount_percent = parseFloat(arrSaveOrder[9]);
		var additional_discount  	= parseFloat(arrSaveOrder[10]);
		var undwono = arrSaveOrder[11];

		if(total_amount>0){ needSave = true;}
		document.getElementById('total_value').value  	= total_amount;
        	document.getElementById('total_amount').value 	= total_amount;
		document.getElementById('net_payble').value   	= total_amount;
		document.getElementById('general_discount_percent').value   = general_discount_percent;
		document.getElementById('general_discount_amount').value    = general_discount_amount;
		document.getElementById('exclusive_discount_percent').value = exclusive_discount_percent;
		document.getElementById('exclusive_discount_amount').value  = exclusive_discount_amount;
		document.getElementById('additional_discount_percent').value= additional_discount_percent;
		document.getElementById('additional_discount').value 	    = additional_discount;
		document.getElementById('und_wo_no').value = undwono;

		document.getElementById('discount').value 	= discount;		
		document.getElementById('customerbalance').value= customerbalance;
		document.getElementById('customer_limit').value = customer_limit;
		document.getElementById('tbs').innerHTML 	= tbl;
		document.getElementById('product').focus();		
	}
}

function ItemDelete(temp_id){
	var customer_id		= document.getElementById('customer').value;
			
	if(temp_id !="" && customer_id)
	{
	  	if(confirm("Are you sure want ot delete???")==true){
			httpDeleteTemp.open("GET", "index.php?app=sales.order&cmd=deltemp&id="+temp_id+"&customer="+customer_id, true);
			httpDeleteTemp.onreadystatechange = handleDeleteTempResponse;
			httpDeleteTemp.send(null);			
		}
	}else{
		alert("Please select customer before delete");
	}
}

function handleDeleteTempResponse()
{
    if(httpDeleteTemp.readyState == 4){       
       //alert(httpLoadUDP.responseText); 
        var salesValue 		= trim(httpDeleteTemp.responseText);
 		var arrSaveOrder 	= salesValue.split("####-@@@@");
		var tbl 			= arrSaveOrder[0];
	 	var total_amount 	= parseFloat(arrSaveOrder[1]);
		var discount 		= parseFloat(arrSaveOrder[2]);
		var customerbalance = parseFloat(arrSaveOrder[3]);
		var customer_limit  = parseFloat(arrSaveOrder[4]);
		if(total_amount>0){ needSave = true;}
		document.getElementById('total_value').value  	= total_amount;
        	document.getElementById('total_amount').value 	= total_amount;
		document.getElementById('net_payble').value   	= total_amount;
		document.getElementById('discount').value 	  	= discount;
		document.getElementById('customerbalance').value= customerbalance;
		document.getElementById('customer_limit').value = customer_limit;
		document.getElementById('tbs').innerHTML 	  	= tbl;
		document.getElementById('product').focus();
		
    } 

}

function ItemEdit(product_id){
	if(product_id!="")
	{
	  httpLoadTemp.open("GET", "index.php?app=sales.order&cmd=get_temp_dtl&product_id="+product_id, true);
	  httpLoadTemp.onreadystatechange = handleTempResponse;
	  httpLoadTemp.send(null);
	}
}

function handleTempResponse()
{
    if(httpLoadTemp.readyState == 4){       
       //alert(httpLoadUP.responseText);
	   var Pcontent = trim(httpLoadTemp.responseText);
	   contentArr	 = Pcontent.split("#####");
	   document.getElementById('tmp_id').value=contentArr[0];

	var select = document.getElementById('product');
	var target = contentArr[1].trim();

	for (var i = 0; i < select.options.length; i++) {
	    if (select.options[i].value.indexOf(target) !== -1) {
		select.selectedIndex = i;
		break;
	    }
	}
	$('#product').trigger('chosen:updated');

	   //document.getElementById('product').value=contentArr[1]; 
	   document.getElementById('catagory').value=contentArr[2];
	   document.getElementById('brand').value=contentArr[3]; 
	   document.getElementById('details').value=contentArr[4]; 
	   document.getElementById('m_unit').value=contentArr[5];
	   document.getElementById('qty').value=contentArr[6];
	   document.getElementById('free_qty').value=contentArr[7];
	   document.getElementById('unit_price').value=contentArr[8];
	   document.getElementById('unit_discount').value=contentArr[9];
	   document.getElementById('discount_amount').value=contentArr[10];
	   document.getElementById('total').value=contentArr[11];
	   document.getElementById('unit_vat').value=parseFloat(contentArr[12]) || 0;
	   document.getElementById('unit_vat_amount').value=parseFloat(contentArr[13]) || 0;
    } 

}
function loadUndeliveryList(customer_id){ 
	  if(customer_id!="") { 	
		  httpLoadInvoice.open("GET", "index.php?app=sales.order&cmd=loadundelivery_inv&customer_id="+customer_id, true);
		  httpLoadInvoice.onreadystatechange = handleLoadInvResponse;
		  httpLoadInvoice.send(null);
	  }
}

function handleLoadInvResponse()
{
    if(httpLoadInvoice.readyState == 4){       
       //alert(httpLoadInvoice.responseText);
	const response = JSON.parse(httpLoadInvoice.responseText);
       processUndeliveryList(response.product_idname);
       //alert(httpLoadInvoice.responseText);

	if(response.aging_invoice){
	     showOrverInvoice(response.aging_invoice);
	}
    }    

}

function processUndeliveryList(ResponseInvStr)
{
		$('#undelivery_invoice').html(ResponseInvStr);
		$("#undelivery_invoice").trigger("chosen:updated");	
			
}


function showOrverInvoice(response){
	var saveBtn = document.getElementById("order-save");
	if(response.agingDate && response.agingDate != ""){
	   $('#aging_date').val(response.agingDate);
	}

	if(response.status){
	     //saveBtn.disabled = true;
	     //saveBtn.classList.add("disable");
	     document.getElementById("overdueContent").innerHTML = response.data;
             document.getElementById("overdueModal").style.display = "block";
	}else{
	     //saveBtn.disabled = false;
	     //saveBtn.classList.remove("disable");
	}
}

function closeModal()
{
    document.getElementById("overdueModal").style.display = "none";
}

window.onclick = function(event) {
    var modal = document.getElementById("overdueModal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}


function getProductList(brandArr){ 
	  var catagoryArr = document.getElementById('catagory').value; 
	  var catagoryStr = catagoryArr.split("###");
	  var catagory_id = catagoryStr[0];
	  var brandStr = brandArr.split("###");
	  var brand_id = brandStr[0];
	  if(brand_id!="") { 	
		  httpLoadProduct.open("GET", "index.php?app=sales.order&cmd=loadProduct&brand_id="+brand_id+"&catagory_id="+catagory_id, true);
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
		  httpLoadUP.open("GET", "index.php?app=sales.order&cmd=get_dtl&product_id="+product_id, true);
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
	   document.getElementById('unit_price').value=contentArr[2];
	   document.getElementById('catagory').value=contentArr[3]; 
	   document.getElementById('brand').value=contentArr[4];
calTotalValue();
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
		
		setRequiredField(customer,			 'dropdown',  'customer_lbl');	
		/*setRequiredField(catagory,           'dropdown',  'catagory_lbl');
		setRequiredField(brand,			 	 'dropdown',  'brand_lbl');*/
		setRequiredField(product,          	 'dropdown',  'product_lbl');
		setRequiredField(unit_price,         'textbox',   'unit_price_lbl');
		setRequiredField(qty,    			 'textbox',	 'qty_lbl');
		setRequiredField(sales_date,		 'textbox',	 'sales_date_lbl');
		setRequiredField(currency,		     'textbox',	 'currency_lbl');
		setRequiredField(total,         	 'textbox',   'amount_lbl');
	}
}
const RE_DECIMAL_25_11 = /^\d{1,14}(\.\d{1,16})?$/;

function fieldValidation(frm)
{
	with(frm)
	{
		if(!RE_DECIMAL_25_11.exec(unit_price.value))
		{
			highlightTableColumn('unit_price_lbl');
			alert(ERROR_NUMBER);		
			return false;
		}
		else if(!RE_DECIMAL_25_11.exec(qty.value))
		{
			highlightTableColumn('qty_lbl');
			alert(ERROR_NUMBER);
			return false;
		}else if(!RE_DECIMAL_25_11.exec(unit_discount.value))
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
	var tmp_id 			= document.getElementById('tmp_id').value;
	catagoryIdName 			= document.getElementById('catagory').value;
	catagory 			= catagoryIdName.split("###");
	var catagoryid 			= catagory[0];
	var catagoryname 		= catagory[1];
	
	brandIdName 			= document.getElementById('brand').value;
	brand 					= brandIdName.split("###");
	var brandid 			= brand[0];
	var brandname 			= brand[1];
	
	productIdName 			= document.getElementById('product').value;
	var product 				= productIdName.split("###");
	//pvoucher_no 			= trim(product[0]);
	pvoucher_no 			= "";
	var productid 			= trim(product[0]);
	var productname 		= product[1];
	pdetails 			= document.getElementById('details').value;
	pdetails 			= pdetails.replace('"',"&rdquo;"); pdetails = pdetails.replace('"',"&rdquo;");
	pdetails 			= pdetails.replace('"',"&rdquo;"); pdetails = pdetails.replace('"',"&rdquo;");
	pdetails 			= pdetails.replace('"',"&rdquo;"); pdetails = pdetails.replace('"',"&rdquo;");
	pdetails 			= pdetails.replace('"',"&rdquo;"); pdetails = pdetails.replace('"',"&rdquo;");
	serial 				= document.getElementById('serial').value;		
	if(serial==""){ serial = 0; }		
	catagory_product_id 	= catagoryid+'###'+brandid+'###'+productid+'###'+serial+'###'+unit_discount;

	var warranty 		= document.getElementById('warranty').value; if(isNaN(warranty)){ warranty = 0; }
	var m_unit 		= document.getElementById('m_unit').value;
	var total_unit 		= document.getElementById('total_unit').value;
	var unit_price 		= parseFloat(document.getElementById('unit_price').value);
	var qty 		= parseFloat(document.getElementById('qty').value);
	var unit_discount   	= parseFloat(document.getElementById('unit_discount').value); if(isNaN(unit_discount)){ unit_discount = 0; }
	var discount_amount 	= parseFloat(document.getElementById('discount_amount').value);

	var vat   	= parseFloat(document.getElementById('unit_vat').value);
	if(isNaN(vat)){ vat = 0; }
	var vat_amount 	= parseFloat(document.getElementById('unit_vat_amount').value);
	if(isNaN(vat_amount)){ vat_amount = 0; }

	var free_qty		= parseFloat(document.getElementById('free_qty').value); if(isNaN(free_qty)){ free_qty = 0; }
	var total		= parseFloat(document.getElementById('total').value); if(isNaN(total)){ total = 0; }
	var total_bag 	= document.getElementById('total_bag').value;
	if(isNaN(unit_price)){ unit_price = 0; } if(isNaN(qty)){ qty = 0; } if(isNaN(discount_amount)){ discount_amount = 0; } if(isNaN(total_bag)){ total_bag = 0; }
	currencyIdName 		= document.getElementById('currency').value;
	currencyArr  		= currencyIdName.split("###");
	var currency 	 	= currencyArr[0];
	var currencyName 	= currencyArr[1];
	var customer 		= document.getElementById('customer').value;
	var delivery_point 	= document.getElementById('delivery_point').value;
	var sales_date 		= document.getElementById('sales_date').value;
	var delivery_date 	= document.getElementById('delivery_date').value;
	
	if(productid!="" && unit_price!=""){
	httpSaveProduct.open("GET","index.php?app=sales.order&cmd=save_direct_tmp&tmp_id="+tmp_id+"&customer="+customer+"&delivery_point="+delivery_point+"&sales_date="+sales_date
	+"&currency="+currency+"&currencyName="+currencyName+"&delivery_date="+delivery_date+"&productid="+productid+"&catagoryname="+catagoryname+"&brandname="
	+brandname+"&qty="+qty+"&free_qty="+free_qty+"&unit_price="+unit_price+"&unit_discount="+unit_discount+"&discount_amount="+discount_amount+"&details="
	+pdetails+"&total="+total+"&vat="+vat+"&vat_amount="+vat_amount, true);
	 httpSaveProduct.onreadystatechange = handleSaveResponse;
	 httpSaveProduct.send(null);
	}
  		  
} // End of function prepareTblGrid()
function handleSaveResponse()
{
    
	if(httpSaveProduct.readyState == 4)
    	{    
		//alert(httpSaveProduct.responseText); 
		document.getElementById('tmp_id').value=0;
		$('#product').val("").trigger('chosen:updated');
		//document.getElementById('product').value="";
		document.getElementById('catagory').value="";
		document.getElementById('brand').value=""; 
		document.getElementById('details').value=""; 
		document.getElementById('m_unit').value="pc";
		document.getElementById('qty').value=0;
		document.getElementById('free_qty').value=0;
		document.getElementById('unit_price').value=0;
		document.getElementById('unit_discount').value=0;
		document.getElementById('discount_amount').value=0;
		document.getElementById('unit_vat').value=0;
		document.getElementById('unit_vat_amount').value=0;
		document.getElementById('total').value=0;
	   
        	var salesValue 		= trim(httpSaveProduct.responseText);
 		var arrSaveOrder 	= salesValue.split("####-@@@@");
		var tbl 			= arrSaveOrder[0];
	 	var total_amount 	= parseFloat(arrSaveOrder[1]);
		var discount 		= parseFloat(arrSaveOrder[2]);
		var customerbalance = parseFloat(arrSaveOrder[3]);
		var customer_limit  = parseFloat(arrSaveOrder[4]);
		var agingInvoice  = JSON.parse(arrSaveOrder[5]);

		showOrverInvoice(agingInvoice);

		if(total_amount>0){ needSave = true;}
		document.getElementById('total_value').value 	= total_amount;
        	document.getElementById('total_amount').value 	= total_amount;
		document.getElementById('net_payble').value 	= total_amount;
		document.getElementById('discount').value 		= discount;		
		document.getElementById('customerbalance').value= customerbalance;
		document.getElementById('customer_limit').value = customer_limit;	
		document.getElementById('tbs').innerHTML 		= tbl;
		document.getElementById('sl_no').focus();
    		document.getElementById('sl_no').select();

        calAllValue();
	   
    } 
}


function prepareGrid()
{	
	
		catagoryIdName 			= document.getElementById('catagory').value;
		catagory 				= catagoryIdName.split("###");
		catagoryid 				= catagory[0];
		catagoryname 			= catagory[1];
		
		brandIdName 			= document.getElementById('brand').value;
		brand 					= brandIdName.split("###");
		brandid 				= brand[0];
		brandname 				= brand[1];
		
		productIdName 			= document.getElementById('product').value;
		product 				= productIdName.split("###");
		//pvoucher_no 			= trim(product[0]);
		pvoucher_no 			= "";
		productid 				= trim(product[0]);
		productname 			= product[1];
		pdetails 				= document.getElementById('details').value;
		pdetails 				= pdetails.replace('"',"&rdquo;"); pdetails = pdetails.replace('"',"&rdquo;");
		pdetails 				= pdetails.replace('"',"&rdquo;"); pdetails = pdetails.replace('"',"&rdquo;");
		pdetails 				= pdetails.replace('"',"&rdquo;"); pdetails = pdetails.replace('"',"&rdquo;");
		pdetails 				= pdetails.replace('"',"&rdquo;"); pdetails = pdetails.replace('"',"&rdquo;");
		serial 					= document.getElementById('serial').value;		
		if(serial==""){ serial = 0; }		
		unit_discount = document.getElementById('unit_discount').value;
		catagory_product_id 	= catagoryid+'###'+brandid+'###'+productid+'###'+serial+'###'+unit_discount;
		//alert(catagory_product_id);
		warranty 	= document.getElementById('warranty').value;
		if(warranty==""){ warranty = 0; }
		m_unit 		= document.getElementById('m_unit').value;
		total_unit 	= document.getElementById('total_unit').value;
		
		unit_price 	= parseFloat(document.getElementById('unit_price').value);
		qty 		= parseFloat(document.getElementById('qty').value);
		discount_amount = parseFloat(document.getElementById('discount_amount').value);
		total_bag 	= document.getElementById('total_bag').value;
		if(total_bag==""){ total_bag = 0; }
		currencyIdName 	= document.getElementById('currency').value;
		currencyArr  = currencyIdName.split("###");
		currency 	 = currencyArr[0];
		currencyName = currencyArr[1];
	
		discount 	= document.getElementById('discount').value;
		total_cost 	= document.getElementById('net_payble').value;
		total_value = document.getElementById('total').value;
		
		if(total_cost!=""){
			total_cost = parseFloat(total_cost);
		
		}else{
			total_cost = 0; discount=0;
		}
 	
		if(rsFound)
		{
			processDataGrid(pvoucher_no,catagoryid,catagoryname,brandname,catagory_product_id,productid,productname,pdetails,serial,warranty,m_unit,unit_price,qty,unit_discount,total_bag,total_unit,currency,currencyName,total_value);
			rsFound = false;
			total_cost = total_cost+parseFloat(total_value);
			document.getElementById('net_payble').value=total_cost.toFixed(4);
			document.getElementById('total_amount').value=total_cost.toFixed(2);
			discount = parseFloat(discount)+discount_amount;
			document.getElementById('discount').value=discount;
			document.getElementById('total_value').value=total_cost;
		}
  	
  		if (rowFound(catagory_product_id))
  		{
			processDataGrid(pvoucher_no,catagoryid,catagoryname,brandname,catagory_product_id,productid,productname,pdetails,serial,warranty,m_unit,unit_price,qty,unit_discount,total_bag,total_unit,currency,currencyName,total_value);
			total_cost = total_cost+parseFloat(total_value);
			document.getElementById('net_payble').value=total_cost.toFixed(4);
			document.getElementById('total_amount').value=total_cost.toFixed(2);
			discount = parseFloat(discount)+discount_amount;
			document.getElementById('discount').value=discount;
			document.getElementById('total_value').value=total_cost;
		}
  
} // End of function prepareGrid()

function processDataGrid(pvoucher_no,catagoryid,catagoryname,brandname,catagory_product_id,productid,productname,pdetails,serial,warranty,m_unit,unit_price,qty,unit_discount,total_bag,total_unit,currency,currencyName,total_value)
{
	obj = document.getElementById('tbs');
    tr = document.createElement('tr');
	tr.appendChild(addCol(catagoryname+'<input type=hidden name=input_catagory_product_id[] id=catagory_product_id[] value='+catagory_product_id+'>'));
  	tr.appendChild(addCol(brandname+'<input type=hidden name=input_brand[] id=input_brand['+catagory_product_id+'] value='+brandname+'>'));
  	tr.appendChild(addCol(productname+'<input type=hidden name=input_product[] id=input_product['+catagory_product_id+'] value='+productid+'><input type=hidden name=input_pvoucher_no[] id=input_pvoucher_no['+catagory_product_id+'] value='+pvoucher_no+'><input type="hidden" name="input_pdetails['+catagory_product_id+']" id="input_pdetails['+catagory_product_id+']" value="'+pdetails+'">'));
	//tr.appendChild(addCol(serial+'<input type=hidden name=input_serial[] id=input_serial['+catagory_product_id+'] value='+serial+'>'));
	tr.appendChild(addCol(warranty+'<input type=hidden name=input_warranty[] id=input_warranty['+catagory_product_id+'] value='+warranty+'>'));
	//tr.appendChild(addCol(m_unit+'<input type=hidden name=input_m_unit['+catagory_product_id+'] id=input_m_unit['+catagory_product_id+'] value='+m_unit+'><input type=hidden name=input_total_unit['+catagory_product_id+'] id=input_total_unit['+catagory_product_id+'] value='+total_unit+'>'));
	
  	tr.appendChild(addCol(qty+' '+m_unit+'<input type=hidden name=input_qty['+catagory_product_id+'] id=input_qty['+catagory_product_id+'] value="'+qty+'"><input type=hidden name=input_total_bag['+catagory_product_id+'] id=input_total_bag['+catagory_product_id+'] value="'+total_bag+'"><input type=hidden name=input_m_unit['+catagory_product_id+'] id=input_m_unit['+catagory_product_id+'] value='+m_unit+'><input type=hidden name=input_total_unit['+catagory_product_id+'] id=input_total_unit['+catagory_product_id+'] value='+total_unit+'>'));
  	//tr.appendChild(addCol(total_bag+' '+total_unit+'<input type=hidden name=input_total_bag['+catagory_product_id+'] id=input_total_bag['+catagory_product_id+'] value="'+total_bag+'">'));
	
	//tr.appendChild(addCol(currencyName+'<input type=hidden name=input_currency['+catagory_product_id+'] id=input_currency['+catagory_product_id+'] value='+currency+'>'));
	tr.appendChild(addCol(unit_price+' '+currencyName+'<input type=hidden name=input_unit_price['+catagory_product_id+'] id=input_unit_price['+catagory_product_id+'] value='+unit_price+'>'));
	tr.appendChild(addCol(unit_discount+' % <input type="hidden" name="input_unit_discount['+catagory_product_id+']" id=input_unit_discount['+catagory_product_id+'] value="'+unit_discount+'">'));
  	tr.appendChild(addCol(total_value+' '+currencyName+'<input type=hidden name=input_total_value['+catagory_product_id+'] id=input_total_value['+catagory_product_id+'] value='+total_value+'>'));
  	
	  obj.appendChild(tr); 
		rsFound = false;
		needSave = true;
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
	var needSave   = parseFloat(document.getElementById('net_payble').value);
	var net_payble = parseFloat(document.getElementById('net_payble').value);
	var customerbalance = parseFloat(document.getElementById('customerbalance').value); if(isNaN(customerbalance)){ customerbalance = 0; }
	customerbalance+=net_payble	;
	var customerlimit   = parseFloat(document.getElementById('customer_limit').value); if(isNaN(customerlimit)){ customerlimit = 0; }	
		
	if(needSave){
		if(customerlimit >=customerbalance){
		    if(confirm("Sure want ot submit???")==true){
			const form = document.getElementById('frmbuyerorder');

        		// ✅ Force the real submit method to run, even if it's shadowed
        		HTMLFormElement.prototype.submit.call(form);
		    } else{
		        return false;
		    }
		}else{
			alert("The customer ceilling amount is over ...");
			return false;
		}
	}else{
		alert("Empty data!!! Please enter data first...");
		return false;
	}
}

function calTotalValue()
{		
  	  var qty 		 = document.frmbuyerorder.qty.value;
	  var unit_price = document.frmbuyerorder.unit_price.value;
	  var unit_discount 	= parseFloat(document.frmbuyerorder.unit_discount.value) || 0;
	  var product_discount 	= parseFloat(document.frmbuyerorder.discount.value) || 0;
	  var discount_amount 	= parseFloat(document.frmbuyerorder.discount_amount.value) || 0;

	  var unit_vat 	= parseFloat(document.frmbuyerorder.unit_vat.value) || 0;
	  
	 if(isNaN(qty)){ qty = 0; } if(isNaN(unit_price)){ unit_price = 0; } if(isNaN(product_discount)){ product_discount = 0; } if(isNaN(discount_amount)){ discount_amount = 0; }

 	if(isNaN(unit_vat)){ unit_vat = 0; }
	 	 
	 var totalvalue = (qty * unit_price);

	 var vatAmount = ((totalvalue/100)*unit_vat);
	 document.frmbuyerorder.unit_vat_amount.value = vatAmount.toFixed(2);

	 var discountAmount = ((totalvalue/100)*unit_discount);
	 document.frmbuyerorder.discount_amount.value = discountAmount.toFixed(2);

 	 totalvalue = parseFloat(totalvalue-discountAmount+vatAmount);
	 document.frmbuyerorder.total.value = totalvalue.toFixed(2);		

}
function calGDiscount()
{		
  	 var total_amount = parseFloat(document.frmbuyerorder.total_amount.value);
	 var general_discount_percent = parseFloat(document.frmbuyerorder.general_discount_percent.value);
	 
	 if(isNaN(total_amount)){ total_amount = 0; } if(isNaN(general_discount_percent)){ general_discount_percent = 0; }
	 
	 var general_discount_amount = ((total_amount/100)*general_discount_percent);
	 var netPay = (total_amount - general_discount_amount); 
	 document.frmbuyerorder.general_discount_amount.value=general_discount_amount.toFixed(2);
	 document.frmbuyerorder.net_payble.value =  netPay.toFixed(4);
calNetPayble();
}
function calExDiscount()
{		
  	 var total_amount 				= parseFloat(document.frmbuyerorder.total_amount.value);
	 var exclusive_discount_percent = parseFloat(document.frmbuyerorder.exclusive_discount_percent.value);
	 var general_discount_amount 	= parseFloat(document.frmbuyerorder.general_discount_amount.value);
	 if(isNaN(total_amount)){ total_amount = 0; } if(isNaN(exclusive_discount_percent)){ exclusive_discount_percent = 0; } if(isNaN(general_discount_amount)){ general_discount_amount = 0; }
	 
	 var NetAmount = (total_amount-general_discount_amount);
	 var exclusive_discount_amount = ((NetAmount/100)*exclusive_discount_percent);
	 var netPay = (NetAmount - exclusive_discount_amount); 
	 document.frmbuyerorder.exclusive_discount_amount.value=exclusive_discount_amount.toFixed(2);
	 document.frmbuyerorder.net_payble.value =  netPay.toFixed(4);
calNetPayble();
}

function calAdditionalDiscount()
{		
  	 var total_amount 				 = parseFloat(document.frmbuyerorder.total_amount.value);
	 var general_discount_amount 	 = parseFloat(document.frmbuyerorder.general_discount_amount.value);
	 var exclusive_discount_amount  = parseFloat(document.frmbuyerorder.exclusive_discount_amount.value);
	 var additional_discount_percent = parseFloat(document.frmbuyerorder.additional_discount_percent.value);
	 
	 if(isNaN(total_amount)){ total_amount = 0; } if(isNaN(exclusive_discount_amount)){ exclusive_discount_amount = 0; } if(isNaN(general_discount_amount)){ general_discount_amount = 0; }
	 if(isNaN(additional_discount_percent)){ additional_discount_percent = 0; }
	 var NetAmount = (total_amount-(general_discount_amount+exclusive_discount_amount));
	 var additional_discount_amount = ((NetAmount/100)*additional_discount_percent);
	 var netPay = (NetAmount - additional_discount_amount); 
	 document.frmbuyerorder.additional_discount.value=additional_discount_amount.toFixed(2);
	 document.frmbuyerorder.net_payble.value =  netPay.toFixed(4);
	 
	 var customerbalance = parseFloat(document.getElementById('customerbalance').value); if(isNaN(customerbalance)){ customerbalance = 0; }	
	 var customerlimit    = parseFloat(document.getElementById('customer_limit').value); if(isNaN(customerlimit)){ customerlimit = 0; }	
	 if(customerbalance >=0){
	    var TotalBalance = customerbalance+netPay;
	 }else{
		var TotalBalance =0;
	 }
	 if(TotalBalance >= customerlimit){
		document.getElementById("order-save").type.visibility="hidden"; //document.getElementById("order-save").type.visibility="hidden"; alert("OK");
	 }else{
		document.getElementById("order-save").type.visibility="visible"; //document.getElementById("order-save").type="submit"; // document.getElementById("order-save").disabled = true; alert("dOK");
	 }

calNetPayble();

}

function calDiscountPersent()
{	
	 var total_amount 				 = parseFloat(document.frmbuyerorder.total_amount.value);	 
	 var general_discount_amount 	 = parseFloat(document.frmbuyerorder.general_discount_amount.value);
	 var exclusive_discount_amount  = parseFloat(document.frmbuyerorder.exclusive_discount_amount.value);	 
	 var additional_discount_amount = parseFloat(document.frmbuyerorder.additional_discount.value);
	 var additional_discount_percent = parseFloat(document.frmbuyerorder.additional_discount_percent.value);
	 if(isNaN(total_amount)){ total_amount = 0; } if(isNaN(exclusive_discount_amount)){ exclusive_discount_amount = 0; } if(isNaN(general_discount_amount)){ general_discount_amount = 0; } 
	 if(isNaN(additional_discount_amount)){ additional_discount_amount = 0; } if(isNaN(additional_discount_percent)){ additional_discount_percent = 0; }
	 var NetAmount = (total_amount-(general_discount_amount+exclusive_discount_amount));
	 additional_discount_percent = ((additional_discount_amount * 100)/NetAmount);	
	 var netPay = (NetAmount-additional_discount_amount);
	 document.frmbuyerorder.net_payble.value =  netPay.toFixed(4);
	 document.frmbuyerorder.additional_discount_percent.value=additional_discount_percent.toFixed(4);
calNetPayble();
}
function calNetPayble()
{		
  	 var total_amount 				= parseFloat(document.frmbuyerorder.total_amount.value);
	 var general_discount_amount 	= parseFloat(document.frmbuyerorder.general_discount_amount.value);
	 var exclusive_discount_amount 	= parseFloat(document.frmbuyerorder.exclusive_discount_amount.value);
	 var additional_discount 	= parseFloat(document.frmbuyerorder.additional_discount.value);
	 if(isNaN(total_amount)){ total_amount = 0; } if(isNaN(general_discount_amount)){ general_discount_amount = 0; } if(isNaN(exclusive_discount_amount)){ exclusive_discount_amount = 0; }  if(isNaN(additional_discount)){ additional_discount = 0;}

	 var additional_cost = parseFloat(document.frmbuyerorder.additional_cost.value);
	 if(isNaN(additional_cost)){ additional_cost = 0;}

	 var total_vat_amount = parseFloat(document.frmbuyerorder.total_vat_amount.value);
	 if(isNaN(total_vat_amount)){ total_vat_amount = 0;}

	  
	 var netPay = ((total_amount+total_vat_amount+additional_cost) - (additional_discount+general_discount_amount+exclusive_discount_amount));  
	 document.frmbuyerorder.net_payble.value =  netPay.toFixed(4);
	 var customerbalance = document.getElementById('customerbalance').value; if(isNaN(customerbalance)){ customerbalance = 0; }	 
	 var TotalBalance = customerbalance+netPay;
	 var customerbalance = parseFloat(document.getElementById('customerbalance').value); if(isNaN(customerbalance)){ customerbalance = 0; }	 
	 var TotalBalance = customerbalance+netPay;
	 if(TotalBalance >=1000000){
		document.getElementById("order-save").type.visibility="hidden"; //document.getElementById("order-save").type.visibility="hidden"; alert("OK");
	 }else{
		document.getElementById("order-save").type.visibility="visible"; //document.getElementById("order-save").type="submit"; // document.getElementById("order-save").disabled = true; alert("dOK");
	 }
	      
}


function calAllValue() {
    calGDiscount();
    calExDiscount();
    calAdditionalDiscount();
    calVatAmount();
    calNetPayble();
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
