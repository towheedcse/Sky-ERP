
var httpCheckUser = getHTTPObject();


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
//==============End Email Matching=============

function showTip()
{ 
  document.getElementById('ltip').style.display = 'block';
  document.getElementById('ltip').style.visibility = 'show';  
  if(document.getElementById('ltip').style.visibility =="hidden")
  {
  	document.getElementById('ltip').style.visibility = 'show'; 	
  }
     
} 
function hideTip()
{ 	 
 document.getElementById('ltip').style.display = 'none';
 document.getElementById('ltip').style.visibility = 'show'; 	
	    
 } 
//============= Start Jobseeker Login ============
function memberLogin()
{
	var textToWrite="";
	var loginid = document.getElementById('loginid').value;
	var password  = document.getElementById('password').value;
	var direction  = document.getElementById('dir').value;
	
	if(loginid!="" && password!="")
	{
		var str = loginid+'COLSEP'+password;	 
	  	
	  	httpCheckUser.open("GET", 'index.php?app=login&cmd=memberlogin&strdata='+str+'&dir='+direction, true);
		httpCheckUser.onreadystatechange = handleUserResponse;  	
  	  	httpCheckUser.send(null);  	
		return true;
	}
	else
	{
		 if(loginid=="" && password=="")
		 {
			 textToWrite = "Please Enter Your Valid Login ID and Password !!!";
		 }
		 else if(loginid=="")
		 {
			 textToWrite = "Please Enter Your Valid Login ID !!!";
		 }
		 else if(password=="")
		 {
			 	textToWrite = "Please Enter Your Dickhunt Password !!!";
		 }
		 document.getElementById('status_msg').style.display = "block";
         document.getElementById('status_msg').innerHTML = textToWrite;
		 return false;
	}
}
function handleUserResponse()
{
    var textToWrite = ""; 
    if(httpCheckUser.readyState == 4)
    {    	
    	
      if(trim(httpCheckUser.responseText) == "Success")
      {        	 
         window.location = "index.php?app=user_home";		 
      }	  
      else if(trim(httpCheckUser.responseText)=="Invalid")
      {          	  	 
        textToWrite = "Invalid User ID and Password. Please try again !!!";
        document.getElementById('loginid').value="";
	    document.getElementById('password').value=""; 
		
      }
	  if(textToWrite!="")
	  {
		  document.getElementById('status_msg').style.display = "block";
		  document.getElementById('status_msg').innerHTML = textToWrite;
	  }
    }
    else 
    {
    	document.getElementById('status_msg').innerHTML = "Checking Existence. Please Wait...";
    }
    
}
//=====End Jobseeker Login ======

