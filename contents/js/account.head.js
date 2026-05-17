RE_NUMBER           = new RegExp(/^[0-9]+$/);

RE_EMAIL    = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);

RE_NAME     = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);

var httpLoadDistrict    = getHTTPObject();
var httpLoadSubhead     = getHTTPObject();
var httpLoadChildhead   = getHTTPObject();
var httpLoadSrcChildhead= getHTTPObject();
var httpLoadSub3head    = getHTTPObject();
var httpLoadSrcSub3head = getHTTPObject();
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
		  httpLoadDistrict.open("GET","index.php?app=accounts.head&cmd=loadsubhtype&head_type="+head_type, true);
		  httpLoadDistrict.onreadystatechange = handleLoadResponse;
		  httpLoadDistrict.send(null);
	  }
}

function handleLoadResponse()
{
    if(httpLoadDistrict.readyState == 4){       
       //alert(httpLoadDistrict.responseText);
       processData(httpLoadDistrict.responseText);
       //alert(httpLoadDistrict.responseText);
    }    

}

function processData(ResponseStr)
{
	//alert(ResponseStr);
	productOption = document.getElementById('sub_headtype');
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

function getChildHeadTypeList(sub_head){ 
	var head_type = document.getElementById('head_type').value;	
	if(sub_head !="") { 	
	  httpLoadChildhead.open("GET","index.php?app=accounts.head&cmd=loadchildhtype&head_type="+head_type+"&sub_head="+sub_head, true);
	  httpLoadChildhead.onreadystatechange = handleLoadChildResponse;
	  httpLoadChildhead.send(null);
	}
}

function handleLoadChildResponse()
{
    if(httpLoadChildhead.readyState == 4){       
       //alert(httpLoadChildhead.responseText);
       processChildData(httpLoadChildhead.responseText);
       //alert(httpLoadDistrict.responseText);
    }    

}

function processChildData(ResponseStr)
{
	//alert(ResponseStr);
	childOption = document.getElementById('child_head');
	while(childOption.length>0)
	{
		childOption.remove(0);
	}		

	var arrChild = Array();
			
	arrChild = ResponseStr.split("@@@");
	childOption.options[0]= new Option("Select Child Head","");
	for( i=0;i<arrChild.length-1; i++)
	{
	  var arrChildIdName = Array();
	  arrChildIdName     = arrChild[i].split("#####");
	  childOption.options[i+1]= new Option(arrChildIdName[1],arrChildIdName[0]);
	}	
			
}

//========= Sub Head for Search =============

function getSrcSubHeadTypeList(head_type){ 
	  if(head_type!="") { 	
		  httpLoadSubhead.open("GET","index.php?app=accounts.head&cmd=loadsubhtype&head_type="+head_type, true);
		  httpLoadSubhead.onreadystatechange = handleLoadSubHeadResponse;
		  httpLoadSubhead.send(null);
	  }
}

function handleLoadSubHeadResponse()
{
    if(httpLoadSubhead.readyState == 4){       
       //alert(httpLoadSubhead.responseText);
       processSubheadData(httpLoadSubhead.responseText);
       //alert(httpLoadSubhead.responseText);
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

//====== Src Child Head List ========


function getSrcChildHeadTypeList(sub_head){ 
	var head_type = document.getElementById('srchead_type').value;	
	if(sub_head !="") { 	
	  httpLoadSrcChildhead.open("GET","index.php?app=accounts.head&cmd=loadchildhtype&head_type="+head_type+"&sub_head="+sub_head, true);
	  httpLoadSrcChildhead.onreadystatechange = handleLoadSrcChildResponse;
	  httpLoadSrcChildhead.send(null);
	}
}

function handleLoadSrcChildResponse()
{
    if(httpLoadSrcChildhead.readyState == 4){       
       //alert(httpLoadSrcChildhead.responseText);
       processSrcChildData(httpLoadSrcChildhead.responseText);
       //alert(httpLoadSrcChildhead.responseText);
    }    

}

function processSrcChildData(ResponseStr)
{
	//alert(ResponseStr);
	childOption = document.getElementById('srcchild_id');
	while(childOption.length>0)
	{
		childOption.remove(0);
	}		

	var arrChild = Array();
			
	arrChild = ResponseStr.split("@@@");
	childOption.options[0]= new Option("Select Child Head","");
	for( i=0;i<arrChild.length-1; i++)
	{
	  var arrChildIdName = Array();
	  arrChildIdName     = arrChild[i].split("#####");
	  childOption.options[i+1]= new Option(arrChildIdName[1],arrChildIdName[0]);
	}	
			
}


//====== Start Subsidiary Step-3 =======

function getSubHead3List(child_head){ 
	  var head_type    = document.getElementById('head_type').value;
	  var sub_head     = document.getElementById('sub_headtype').value;
	  if(head_type!="" && sub_head!="" && child_head!="") { 	
		  httpLoadSub3head.open("GET","index.php?app=accounts.head&cmd=loadSL3Htype&head_type="+head_type+"&sub_head="+sub_head+"&child_head="+child_head, true);
		  httpLoadSub3head.onreadystatechange = handleLoadSub3Response;
		  httpLoadSub3head.send(null);
	  }
}

function handleLoadSub3Response()
{
    if(httpLoadSub3head.readyState == 4){       
       //alert(httpLoadSub3head.responseText);
       processSub3Data(httpLoadSub3head.responseText);
       //alert(httpLoadSub3head.responseText);
    }    

}

function processSub3Data(ResponseStr2)
{
	//alert(ResponseStr);
	productOption2 = document.getElementById('sl_three_head');
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

function getSrcSub3HeadTypeList(child_head){ 
	  var head_type    = document.getElementById('srchead_type').value;
	  var sub_head     = document.getElementById('srcsub_headtype').value;
	  if(head_type!="" && sub_head!="" && child_head!="") { 	
		  httpLoadSrcSub3head.open("GET","index.php?app=accounts.head&cmd=loadSL3Htype&head_type="+head_type+"&sub_head="+sub_head+"&child_head="+child_head, true);
		  httpLoadSrcSub3head.onreadystatechange = handleLoadSub3ScrResponse;
		  httpLoadSrcSub3head.send(null);
	  }
}

function handleLoadSub3ScrResponse()
{
    if(httpLoadSrcSub3head.readyState == 4){       
       //alert(httpLoadSrcSub3head.responseText);
       processSubhead3Data(httpLoadSrcSub3head.responseText);
       //alert(httpLoadSrcSub3head.responseText);
    }    

}

function processSubhead3Data(ResponseStr)
{
	//alert(ResponseStr);
	SubheadOption = document.getElementById('srcsl_three_head');
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
//====== End Subsidiary Step-3=======

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
