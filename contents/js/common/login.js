
var httpChkLogin = getHTTPObject();


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



 
function saveEnquiry()
{	
	
	var desig 				= document.getElementById('desig').value;
	var org_phone 		= document.getElementById('org_phone').value;
 	
   var dataStr = masterStr+'_BLK_'+courseStr+'_DBLKSEP_'+shiftStr;
   
 	 httpSaveEnquiry.open("GET", 'index.php?app=enquiry&cmd=saveEnquiry&dataStr='+dataStr, true);
   httpSaveEnquiry.onreadystatechange = handleEnquiryResponse;
   httpSaveEnquiry.send(null);
  
}//=========End of saveEnquiry()==========================


function handleEnquiryResponse()
{
		httpSaveEnquiry.readyState;
		
    if(httpSaveEnquiry.readyState == 4)
    {
    	alert(httpSaveEnquiry.responseText);
    }
    
}
function chkValidation()
{
	var login  = document.getElementById('loginid').value;
	alert(login);
	var pass   = document.getElementById('password').value;
	alert(pass);
	var strData = login+'_SAP_'+pass;
	httpChkLogin.open("GET", 'index.php?app=login&cmd=chkValidation&strData='+strData, true);
  httpChkLogin.onreadystatechange = handleChkLoginResponse;
  httpChkLogin.send(null);
}
function handleChkLoginResponse()
{
	  httpChkLogin.readyState;
		
    if(httpChkLogin.readyState == 4)
    {
    	alert(httpChkLogin.responseText);
    }
	
}

