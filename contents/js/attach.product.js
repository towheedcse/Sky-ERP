RE_NUMBER   = new RegExp(/^[0-9]+$/);
RE_EMAIL    = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);
RE_NAME     = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);

var httpLoadProduct     = getHTTPObject();
var httpSaveProduct 	= getHTTPObject();
var httpLoadGride 		= getHTTPObject();

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

function getProductList(catagory_id){	  
	  //var catagoryStr = catagoryArr.split("###");
	  //var catagory_id = catagoryStr[0];
	  if(catagory_id !="") { 	
		  httpLoadProduct.open("GET", "index.php?app=attach.product&cmd=loadProduct&catagory_id="+catagory_id, true);
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
		var product_details="";		
		arrProduct = ResponseStr.split("@@@");
		productOption.options[0]= new Option("Select One","0");
		for( i=0;i<arrProduct.length-1; i++)
		{
		  var arrProductIdName = Array();
		  arrProductIdName  = arrProduct[i].split("#####");
		  var details 	   	= arrProductIdName[2];	
		  if(details !=""){
		  	product_details = arrProductIdName[1]+'-'+details;
		  }else{
			product_details = arrProductIdName[1];  
		  }
		  productOption.options[i+1]= new Option(product_details, arrProductIdName[0]);
		}	
			
}

function getGroupProductGride(group_id){	
	  if(group_id !="") { 	
		  httpLoadGride.open("GET", "index.php?app=attach.product&cmd=loadGPG&group_id="+group_id, true);
		  httpLoadGride.onreadystatechange = handleLoadGrideResponse;
		  httpLoadGride.send(null);
	  }
}

function handleLoadGrideResponse()
{
    if(httpLoadGride.readyState == 4){       
       var tbl 		= trim(httpLoadGride.responseText);
		document.getElementById('tbs').innerHTML = tbl;
		document.getElementById('catagory').focus();
    }    

}

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
		prepareTblGrid();
		
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
	}else{
		return true;
	}
	
}

function formSetup(frm)
{
	with(frm)
	{		
		setRequiredField(group_id,			 'dropdown',  'group_lbl');	
		setRequiredField(catagory,           'dropdown',  'catagory_lbl');
	}
}

function prepareTblGrid()
{	
	
	var group_id 			= document.getElementById('group_id').value;
	catagoryid 				= document.getElementById('catagory').value;		
	var productid 			= document.getElementById('product').value;
	//alert(productid);	
	if(group_id !="" && catagoryid!=""){	
	 httpSaveProduct.open("GET","index.php?app=attach.product&cmd=save_tmp&group_id="+group_id+"&catagoryid="+catagoryid+"&productid="+productid, true);
	 httpSaveProduct.onreadystatechange = handleSaveResponse;
	 httpSaveProduct.send(null);
	}
  		  
} // End of function prepareTblGrid()

function handleSaveResponse()
{
    if(httpSaveProduct.readyState == 4)
    {    
		//alert(httpSaveProduct.responseText); 
        var tbl 		= trim(httpSaveProduct.responseText);
		document.getElementById('tbs').innerHTML = tbl;
		document.getElementById('catagory').focus();
		
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


// ===== end Expected Salary ==========

function deleteRecord(id)
{	
   var url_loc = "index.php?app=purchase_order&cmd=delete&id="+id;
   window.location = url_loc;
}
function ClearAll()
{	
   var group_id = document.getElementById('group_id').value;
   var url_loc = "index.php?app=attach.product&cmd=clear_all&id="+group_id;
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