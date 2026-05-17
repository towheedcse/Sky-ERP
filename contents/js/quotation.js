RE_NUMBER           = new RegExp(/^[0-9]+$/);
RE_EMAIL    = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);
RE_NAME     = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);

var httpLoadProduct      = getHTTPObject();
var httpLoadUP 			 = getHTTPObject();

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
		  httpLoadProduct.open("GET", "index.php?app=sales_order&cmd=loadProduct&brand_id="+brand_id+"&catagory_id="+catagory_id, true);
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
		productOption.options[0]= new Option("Select One","-1");
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
		  httpLoadUP.open("GET", "index.php?app=sales_order&cmd=get_dtl&product_id="+product_id, true);
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
		
		setRequiredField(customer,			 'dropdown',  'customer_lbl');	
		setRequiredField(catagory,           'dropdown',  'catagory_lbl');
		setRequiredField(brand,			 	 'dropdown',  'brand_lbl');
		setRequiredField(product,          	 'dropdown',  'product_lbl');
		setRequiredField(unit_price,         'textbox',   'unit_price_lbl');
		setRequiredField(qty,    			 'textbox',	 'qty_lbl');
		setRequiredField(sales_date,		 'textbox',	 'sales_date_lbl');
		setRequiredField(currency,		     'textbox',	 'currency_lbl');
		//setRequiredField(wo_no,         	 'textbox',   'wo_no_lbl');
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
		//serial 					= document.getElementById('serial').value;
		var serial =0;
		//if(serial==""){ serial = 0; }
		catagory_product_id 	= catagoryid+'###'+brandid+'###'+productid+'###'+0;
		//alert(catagory_product_id);
		warranty 	= document.getElementById('warranty').value;
		if(warranty==""){ warranty = 0; }
		m_unit 		= document.getElementById('m_unit').value;
		total_unit 	= document.getElementById('total_unit').value;
		
		unit_price 	= parseFloat(document.getElementById('unit_price').value);
		qty 		= parseFloat(document.getElementById('qty').value);
		total_bag 	= document.getElementById('total_bag').value;
		if(total_bag==""){ total_bag = 0; }
		currencyIdName 	= document.getElementById('currency').value;
		currencyArr  = currencyIdName.split("###");
		currency 	 = currencyArr[0];
		currencyName = currencyArr[1];
	
		total_cost 	= document.getElementById('total_value').value;
		total_value = unit_price*qty;
		total_value = total_value.toFixed(2);
		if(total_cost!=""){
			total_cost = parseFloat(total_cost);
		
		}else{
			total_cost = 0;
		}
 	
		if(rsFound)
		{
			processDataGrid(pvoucher_no,catagoryid,catagoryname,brandname,catagory_product_id,productid,productname,pdetails,serial,warranty,m_unit,unit_price,qty,total_bag,total_unit,currency,currencyName,total_value);
			rsFound = false;
			total_cost = total_cost+parseFloat(total_value);
			document.getElementById('total_value').value=total_cost;
		}
  	
  		if (rowFound(catagory_product_id))
  		{
			processDataGrid(pvoucher_no,catagoryid,catagoryname,brandname,catagory_product_id,productid,productname,pdetails,serial,warranty,m_unit,unit_price,qty,total_bag,total_unit,currency,currencyName,total_value);
			total_cost = total_cost+parseFloat(total_value);
			document.getElementById('total_value').value=total_cost;
		}
  
} // End of function prepareGrid()

function processDataGrid(pvoucher_no,catagoryid,catagoryname,brandname,catagory_product_id,productid,productname,pdetails,serial,warranty,m_unit,unit_price,qty,total_bag,total_unit,currency,currencyName,total_value)
{
	obj = document.getElementById('tbs');
    tr = document.createElement('tr');
	tr.appendChild(addCol(catagoryname+'<input type=hidden name=input_catagory_product_id[] id=catagory_product_id[] value='+catagory_product_id+'>'));
  	tr.appendChild(addCol(brandname+'<input type=hidden name=input_brand[] id=input_brand['+catagory_product_id+'] value='+brandname+'>'));
  	tr.appendChild(addCol(productname+'<input type=hidden name=input_product[] id=input_product['+catagory_product_id+'] value='+productid+'><input type=hidden name=input_pvoucher_no[] id=input_pvoucher_no['+catagory_product_id+'] value='+pvoucher_no+'><input type="hidden" name="input_pdetails['+catagory_product_id+']" id="input_pdetails['+catagory_product_id+']" value="'+pdetails+'">'));
	tr.appendChild(addCol(pdetails+'<input type=hidden name=input_serial[] id=input_serial['+catagory_product_id+'] value='+serial+'>'));
	tr.appendChild(addCol(warranty+'<input type=hidden name=input_warranty[] id=input_warranty['+catagory_product_id+'] value='+warranty+'>'));
	//tr.appendChild(addCol(m_unit+'<input type=hidden name=input_m_unit['+catagory_product_id+'] id=input_m_unit['+catagory_product_id+'] value='+m_unit+'><input type=hidden name=input_total_unit['+catagory_product_id+'] id=input_total_unit['+catagory_product_id+'] value='+total_unit+'>'));
	
  	tr.appendChild(addCol(qty+' '+m_unit+'<input type=hidden name=input_qty['+catagory_product_id+'] id=input_qty['+catagory_product_id+'] value="'+qty+'"><input type=hidden name=input_total_bag['+catagory_product_id+'] id=input_total_bag['+catagory_product_id+'] value="'+total_bag+'"><input type=hidden name=input_m_unit['+catagory_product_id+'] id=input_m_unit['+catagory_product_id+'] value='+m_unit+'><input type=hidden name=input_total_unit['+catagory_product_id+'] id=input_total_unit['+catagory_product_id+'] value='+total_unit+'>'));
  	//tr.appendChild(addCol(total_bag+' '+total_unit+'<input type=hidden name=input_total_bag['+catagory_product_id+'] id=input_total_bag['+catagory_product_id+'] value="'+total_bag+'">'));
	
	//tr.appendChild(addCol(currencyName+'<input type=hidden name=input_currency['+catagory_product_id+'] id=input_currency['+catagory_product_id+'] value='+currency+'>'));
	tr.appendChild(addCol(unit_price+' '+currencyName+'<input type=hidden name=input_unit_price['+catagory_product_id+'] id=input_unit_price['+catagory_product_id+'] value='+unit_price+'>'));
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

function wantToSave()
{
	if(needSave)
	{
		if(confirm("Sure want ot submit???")==true)
		{
			document.frmbuyerorder.submit();
			
		}
	}
	else
	{
		alert("Empty data!!! Please enter data first...");
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