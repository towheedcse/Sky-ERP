RE_NUMBER           = new RegExp(/^[0-9]+$/);

RE_EMAIL    = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);

RE_NAME     = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);

var httpLoadSubhead     = getHTTPObject();
var httpLoadSrcSubhead  = getHTTPObject();
var httpLoadSub2head    = getHTTPObject();
var httpLoadSrcSub2head = getHTTPObject();
var httpLoadUP 		= getHTTPObject();
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
function checkUnit(val){	
	 document.getElementById("qty_m").innerHTML = " ("+val+")";
	 document.getElementById("uprice_m").innerHTML =" (per "+val+")";
}

function getSubHeadTypeList(head_type){ 
	  if(head_type!="") { 	
		  httpLoadSubhead.open("GET","index.php?app=accounts.head&cmd=loadsubhtype&head_type="+head_type, true);
		  httpLoadSubhead.onreadystatechange = handleLoadResponse;
		  httpLoadSubhead.send(null);
	  }
}

function handleLoadResponse()
{
    if(httpLoadSubhead.readyState == 4){       
       //alert(httpLoadSubhead.responseText);
       processData(httpLoadSubhead.responseText);
       //alert(httpLoadSubhead.responseText);
    }    

}

function processData(ResponseStr)
{
	//alert(ResponseStr);
	productOption = document.getElementById('sub_head');
	while(productOption.length>0)
	{
		productOption.remove(0);
	}		

	var arrProduct = Array();
			
	arrProduct = ResponseStr.split("@@@");
	productOption.options[0]= new Option("Select One","");
	for( i=0;i<arrProduct.length-1; i++)
	{
	  var arrProductIdName = Array();
	  arrProductIdName =	arrProduct[i].split("#####");
	  productOption.options[i+1]= new Option(arrProductIdName[1],arrProductIdName[0]);
	}	
			
}
//========= Sub Head for Search =============

function getSrcSubHeadTypeList(head_type){ 
	  if(head_type!="") { 	
		  httpLoadSrcSubhead.open("GET","index.php?app=accounts.head&cmd=loadsubhtype&head_type="+head_type, true);
		  httpLoadSrcSubhead.onreadystatechange = handleLoadSubHeadResponse;
		  httpLoadSrcSubhead.send(null);
	  }
}

function handleLoadSubHeadResponse()
{
    if(httpLoadSrcSubhead.readyState == 4){       
       //alert(httpLoadSrcSubhead.responseText);
       processSubheadData(httpLoadSrcSubhead.responseText);
       //alert(httpLoadSrcSubhead.responseText);
    }    

}

function processSubheadData(ResponseStr)
{
	//alert(ResponseStr);
	SubheadOption = document.getElementById('srcsub_headtype');
	while(SubheadOption.length>0)
	{
		SubheadOption.remove(0);
	}		

	var arrSubhead = Array();
			
	arrSubhead = ResponseStr.split("@@@");
	SubheadOption.options[0]= new Option("Select One","");
	for( i=0;i<arrSubhead.length-1; i++)
	{
	  var arrSubheadIdName = Array();
	  arrSubheadIdName =	arrSubhead[i].split("#####");
	  SubheadOption.options[i+1]= new Option(arrSubheadIdName[1],arrSubheadIdName[0]);
	}	
			
}
//====== Start Subsidiary Step-2=======

function getSubHead2List(sub_head){ 
	 var head_type = document.getElementById('head_type').value;
	  if(head_type!="" && sub_head!="") { 	
		  httpLoadSub2head.open("GET","index.php?app=accounts.head&cmd=loadchildhtype&head_type="+head_type+"&sub_head="+sub_head, true);
		  httpLoadSub2head.onreadystatechange = handleLoadSub2Response;
		  httpLoadSub2head.send(null);
	  }
}

function handleLoadSub2Response()
{
    if(httpLoadSub2head.readyState == 4){       
       //alert(httpLoadSub2head.responseText);
       processSub2Data(httpLoadSub2head.responseText);
       //alert(httpLoadSub2head.responseText);
    }    

}

function processSub2Data(ResponseStr2)
{
	//alert(ResponseStr);
	productOption2 = document.getElementById('child_id');
	while(productOption2.length>0)
	{
		productOption2.remove(0);
	}		

	var arrProduct2 = Array();
			
	arrProduct2 = ResponseStr2.split("@@@");
	productOption2.options[0]= new Option("Select One","");
	for( i=0; i< arrProduct2.length-1; i++)
	{
	  var arrProductIdName2 = Array();
	  arrProductIdName2 =	arrProduct2[i].split("#####");
	  productOption2.options[i+1]= new Option(arrProductIdName2[1],arrProductIdName2[0]);
	}	
			
}
//========= Sub Head for Search =============

function getSrcSub2HeadTypeList(sub_head){ 
	  var head_type = document.getElementById('srchead_type').value;
	  if(head_type!="" && sub_head!="") { 	
		  httpLoadSrcSub2head.open("GET","index.php?app=accounts.head&cmd=loadchildhtype&head_type="+head_type+"&sub_head="+sub_head, true);
		  httpLoadSrcSub2head.onreadystatechange = handleLoadSub2ScrResponse;
		  httpLoadSrcSub2head.send(null);
	  }
}

function handleLoadSub2ScrResponse()
{
    if(httpLoadSrcSub2head.readyState == 4){       
       //alert(httpLoadSrcSubhead.responseText);
       processSubhead2Data(httpLoadSrcSub2head.responseText);
       //alert(httpLoadSrcSub2head.responseText);
    }    

}

function processSubhead2Data(ResponseStr)
{
	//alert(ResponseStr);
	SubheadOption = document.getElementById('srcchild_id');
	while(SubheadOption.length>0)
	{
		SubheadOption.remove(0);
	}		

	var arrSubhead = Array();
			
	arrSubhead = ResponseStr.split("@@@");
	SubheadOption.options[0]= new Option("Select One","");
	for( i=0;i<arrSubhead.length-1; i++)
	{
	  var arrSubheadIdName = Array();
	  arrSubheadIdName =	arrSubhead[i].split("#####");
	  SubheadOption.options[i+1]= new Option(arrSubheadIdName[1],arrSubheadIdName[0]);
	}	
			
}
//====== End Subsidiary Step-2=======

//********* End get Product List ************
function getProductDtl(product_id)
{ 
	  product_idArr 	= product_id.split("###");
	  voucher_no 		= product_idArr[0];
	  product_id 		= product_idArr[1];
	  if(product_id!="")
	  {
		  getProductSerialList(voucher_no,product_id);
		  httpLoadUP.open("GET", "index.php?app=sales&cmd=get_uprice&product_id="+product_id+"&voucher_no="+voucher_no, true);
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
	   
	   document.getElementById('stock_qty').value=contentArr[1];
	   document.getElementById('m_unit').value=contentArr[2];
	   var productcatagory=contentArr[3];	   
	   var details = contentArr[5];
	   if(details==""){
		var details = contentArr[6];
	   }
	   if(productcatagory!=0){
		   //document.getElementById('serial').value =contentArr[3];
		   document.getElementById('qty').value=1; document.getElementById('qty').disabled =true;document.getElementById('warranty').value =contentArr[4];
	   }else{
		   //document.getElementById('serial').value =contentArr[3]; 
		   document.getElementById('qty').disabled =false;document.getElementById('warranty').value =contentArr[4];
	   }
	   document.getElementById('details').value=details;
	   
    } 

}
function getProductSerialList(voucher_no,product_id){ 
	  if(product_id!=""){
		  httpLoadPS.open("GET", "index.php?app=sales&cmd=get_serial&product_id="+product_id+"&voucher_no="+voucher_no, true);
		  httpLoadPS.onreadystatechange = handlePSResponse;
		  httpLoadPS.send(null);
	  }
}

function handlePSResponse()
{
    if(httpLoadPS.readyState == 4){       
       //alert(httpLoadDistrict.responseText);
       processSerialData(httpLoadPS.responseText);
       //alert(httpLoadDistrict.responseText);
    }    

}


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
