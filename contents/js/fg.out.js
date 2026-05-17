RE_NUMBER           = new RegExp(/^[0-9]+$/);

RE_EMAIL    = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);

RE_NAME     = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);

var httpLoadProduct = getHTTPObject();
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
	 document.getElementById("amount_m").innerHTML =" (per "+val+")";
}

//********* Product List *********
function getProductList(brandArr)
{ 
	  var catagoryArr = document.getElementById('catagory').value; 
	  var catagoryStr = catagoryArr.split("###");
	  var catagory_id = catagoryStr[0];
	  var brandStr = brandArr.split("###");
	  var brand_id = brandStr[0];
	  if(brand_id!="")
	  { 	
		  httpLoadProduct.open("GET", "index.php?app=sales&cmd=loadProduct&brand_id="+brand_id+"&catagory_id="+catagory_id, true);
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
		  productOption.options[i+1]= new Option(arrProductIdName[2], arrProductIdName[0]+'###'+arrProductIdName[1]+'###'+arrProductIdName[2]);
		}	
			
}
function getProductDtl(product_id)
{ 
	  product_idArr 	= product_id.split("###");
	  product_id 		= product_idArr[0];
	  var store_id 		= document.getElementById('out_store_id').value;
	  //alert(voucher_no+' '+product_id);
	  if(product_id !="")
	  {
		  httpLoadUP.open("GET", "index.php?app=fg.production&cmd=get_productinfo&product_id="+product_id+"&store_id="+store_id, true);
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
	   document.getElementById('amount').value=contentArr[0];    
	   document.getElementById('stock_qty').value=contentArr[1];
	   document.getElementById('m_unit').value=contentArr[2];  
	   document.getElementById('catagory').value=contentArr[3];  
	   document.getElementById('brand').value=contentArr[4];  
	   
    } 

}
//********* End get Product List ************

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
		setRequiredField(catagory,           'dropdown',  'catagory_lbl');
		setRequiredField(brand,			 	 'dropdown',  'brand_lbl');
		setRequiredField(product,          	 'dropdown',  'product_lbl');
		setRequiredField(m_unit,			 'dropdown',  'm_unit_lbl');
		setRequiredField(amount,         	 'textbox',   'amount_lbl');
		setRequiredField(out_qty,    		 'textbox',	 'out_qty_lbl');
		setRequiredField(qty,    			 'textbox',	 'qty_lbl');
	}
}
function fieldValidation(frm)
{
	with(frm)
	{
		if(qty.value=="")
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
		var pvoucher_no 		= "";
		productIdName 			= document.getElementById('product').value;
		product 				= productIdName.split("###");
		productid 				= trim(product[0]);
		productname 			= product[1];
		
		catagory_product_id 	= catagoryid+'###'+brandid+'###'+productid;
		
		m_unit 		= document.getElementById('m_unit').value;
		
		amount 	= parseFloat(document.getElementById('amount').value);
		qty 	= parseFloat(document.getElementById('qty').value);
		
		var currency 	= document.getElementById('currency').value; var currencyName = "BDT";
		//if(currency=="1"){ currencyName = "BDT";}	
		
		total_cost 	= document.getElementById('total_value').value;
		total_qty 	= parseFloat(document.getElementById('total_qty').value);
		var total_value = (amount*qty);
		total_value = total_value.toFixed(2);
		if(total_cost!=""){
			total_cost = parseFloat(total_cost);		
		}else{
			total_cost = 0; total_qty=0;
		}
		
		if(rsFound)
		{
		  processDataGrid(catagoryid,catagoryname,brandid,brandname,catagory_product_id,productid,productname,pvoucher_no,m_unit,qty,currency,currencyName,amount,total_value);
			rsFound = false;
			var total_cost = total_cost+parseFloat(total_value); var total_qty=total_qty+qty;
			document.getElementById('total_value').value=total_cost; document.getElementById('total_qty').value=total_qty;
		}
  	
  		if (rowFound(catagory_product_id))
  		{
		  processDataGrid(catagoryid,catagoryname,brandid,brandname,catagory_product_id,productid,productname,pvoucher_no,m_unit,qty,currency,currencyName,amount,total_value);
			var total_cost = total_cost+parseFloat(total_value); var total_qty=total_qty+qty;
			document.getElementById('total_value').value=total_cost; document.getElementById('total_qty').value=total_qty;
		}
  
} // End of function prepareGrid()

function processDataGrid(catagoryid,catagoryname,brandid,brandname,catagory_product_id,productid,productname,pvoucher_no,m_unit,qty,currency,currencyName,amount,total_value)
{
	obj = document.getElementById('tbs');
    tr = document.createElement('tr');
	tr.appendChild(addCol(catagoryname+'<input type=hidden name=input_catagory_product_id[] id=catagory_product_id[] value='+catagory_product_id+'>'));
	tr.appendChild(addCol(brandname+'<input type=hidden name=input_brand['+catagory_product_id+'] id=input_brand['+catagory_product_id+'] value='+brandname+'>'));
  	tr.appendChild(addCol(productname+'<input type=hidden name=input_product['+catagory_product_id+'] id=input_product['+catagory_product_id+'] value='+product+'>'));
  	
	tr.appendChild(addCol(m_unit+'<input type=hidden name=input_m_unit['+catagory_product_id+'] id=input_m_unit['+catagory_product_id+'] value='+m_unit+'><input type=hidden name=input_pvoucher_no['+catagory_product_id+'] id=input_pvoucher_no['+catagory_product_id+'] value='+pvoucher_no+'>'));
  	tr.appendChild(addCol(qty+' '+m_unit+'<input type=hidden name=input_qty['+catagory_product_id+'] id=input_qty['+catagory_product_id+'] value="'+qty+'">'));
	tr.appendChild(addCol(currencyName+'<input type=hidden name=input_currency['+catagory_product_id+'] id=input_currency['+catagory_product_id+'] value='+currency+'><input type=hidden name=input_amount['+catagory_product_id+'] id=input_amount['+catagory_product_id+'] value='+amount+'>'));  	
	//tr.appendChild(addCol(total_value+'<input type=hidden name=input_amount['+catagory_product_id+'] id=input_amount['+catagory_product_id+'] value='+amount+'>'));
  	
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
function calTotalQty()
{		
  	 var out_qty = parseFloat(document.frmbuyerorder.out_qty.value);
	  //alert(total_value);alert(discount);
	 if(out_qty=='' || out_qty==0){ out_qty = 0; }
 
	 var qty = out_qty; //((out_qty/1000));  
	 document.frmbuyerorder.qty.value =  qty.toFixed(3);   
	      
}
function CostOfGoods()
{		
  	 var overhead_cost = parseFloat(document.frmbuyerorder.overhead_cost.value);
	 var total_value = parseFloat(document.frmbuyerorder.total_value.value);
	  //alert(total_value);alert(discount);
	 if(total_value=='' || total_value==0){ total_value = 0; }
	 if(overhead_cost=='' || overhead_cost==0){ overhead_cost = 0;}
	 
	 var netPay = (total_value + overhead_cost);  
	 document.frmbuyerorder.sold_cost.value =  netPay.toFixed(2);   
	      
}
function wantToSave()
{
	if(needSave)
	{
		if(confirm("Sure want ot submit???"))
		{
			document.frmbuyerorder.submit();
			
		}
	}
	else
	{
		alert("Empty data!!! Please enter data first...");
	}
}


function deleteRecord(id)
{	
   var url_loc = "index.php?app=purchase&cmd=delete&id="+id;
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
