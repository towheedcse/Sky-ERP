RE_NUMBER           = new RegExp(/^[0-9]+$/);
RE_EMAIL    		= new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);
RE_NAME     		= new RegExp(/[^A-Z^a-z^ ^\.\^]$/);
var httpchkemail     = getHTTPObject();

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

//============= Start chkEmailExistence for Signup ============
function chkUserExistence()
{
	var textToWrite="";	
	var userid = document.userinfo.userid.value;
	var ln = userid.length;		
	 
	if(userid!="" && ln >=6 && ln <20)
	{			  	
	  	httpchkemail.open("GET", 'index.php?app=user&cmd=checkUser&userid='+userid, true);
		httpchkemail.onreadystatechange = handleUserResponse;  	
  	  	httpchkemail.send(null);  	
		return true;
	}
	else
	{
		 if(userid=="")
		 {
			 textToWrite = "Please Enter login Id !!!";
			 highlightTableColumn('userid'); 
		 }
		 else if(userid!="" && (ln<6 || ln >30))
		 {
		 	textToWrite = "Please Enter login Id between 6 to 30 characters!!!";
			highlightTableColumn('userid'); 
		 }
		 document.userinfo.userid.focus(); 
		 document.userinfo.userid.select(); 		
		 document.getElementById('status_msg').style.display = "block";
         document.getElementById('status_msg').innerHTML = textToWrite;
		 return false;
	}
}
function handleUserResponse()
{
    var textToWrite = "";
	//alert(trim(httpchkemail.responseText)); 	
    if(httpchkemail.readyState == 4)
    {    	
      
      if(trim(httpchkemail.responseText) == "valid")
      {        	 
         	 textToWrite = "Abailable this login Id";	
			 resetTableColumn('userid');
      }
      else if(trim(httpchkemail.responseText)=="Invalid")
      {          	  	 
        textToWrite = "This login Id has been already taken. Please try diffrent !!!";             
	    highlightTableColumn('userid');        
		//document.userinfo.userid.focus(); 
		//document.userinfo.userid.select(); 		
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
//=====End chkEmailExistence ======

function checkEmail()
{	
 	 var email = document.userinfo.email.value;
 	 if(!RE_EMAIL.exec(email))
     {
	 	highlightTableColumn('email');
        alert(ERROR_EMAIL);		 
		document.userinfo.email.focus(); 
		document.userinfo.email.select();     
		return false;
     }
	 else
	 {	
	 		resetTableColumn('email');
			return true;
	 }
 }
 
 function checkReEmail()
 {	 
 	 var email = document.userinfo.email.value; 	 
 	 var re_type_email = document.userinfo.re_type_email.value;
	 
 	 if(!RE_EMAIL.exec(re_type_email) || (email!=re_type_email))
     {
	 	highlightTableColumn('re_type_email');
        alert("Your Email and Re-enter Email is not same");		 
		document.userinfo.re_type_email.focus(); 
		document.userinfo.re_type_email.select(); 
		return false;
        
     }
	 else
	 {
	 	resetTableColumn('re_type_email');
		return true;
	 }
 }
 
function checkPassword()
 {
	var password = document.userinfo.password.value;
	var ln = password.length;	
	
	 if(password=="" || ln<6 || ln>20)
	 {        
			 highlightTableColumn('password');
			 alert('Please enter valid password!!!');
			 document.userinfo.password.focus(); 
			 document.userinfo.password.select();  
			 return false;
	  }	  	  
	  else
	  {
		resetTableColumn('password');
		return true;
	  }
	      
}

function onSubmit()
{
 	if(!checkPassword()|| !checkRePassword())
	{
	return false;
	}
	else{
	return true;
	}
}
//======================
function checkRePassword()
{
	var password = document.userinfo.password.value;
	var conf_password = document.userinfo.conf_password.value;	
	var cln = conf_password.length;	
	
	 if(conf_password!=password)
	 {        
			 highlightTableColumn('conf_password');
			 alert('Your password and re-type password is not same!!!');
			 document.userinfo.conf_password.focus(); 
			 document.userinfo.conf_password.select(); 
			 return false;
	  }	  	  
	  else
	  {
		resetTableColumn('conf_password');
		return true;
	  }
	      
}
function checkState()
{	  	 
	 var state_code = document.userinfo.state_code.value;	
	
 	if(state_code==""||state_code==0)
     {
	 	highlightTableColumn('state_code');
        alert("Please select your state");				
		document.userinfo.state_code.focus(); 		
		resetTableColumn('country');
		return false;
     }
	 else
	 {		       
	 		resetTableColumn('state_code');
			return true;
	 }	
	
 }
function checkWherehear()
{	  	 
	 var wherehear = document.userinfo.wherehear.value;
	
 	if(wherehear=="")
    {
	 	highlightTableColumn('wherehear');
        alert("Please select i learnt about DickHunt.net from...");		
		document.userinfo.wherehear.focus(); 
		resetTableColumn('state_code');
		return false;
     } 	 
	 else
	 {		       
	 		resetTableColumn('wherehear');
			return true;
	 }	
	
 }

function checkAgreement()
{	   
	 var agree_terms = document.userinfo.agree_terms.checked;
	 	
	 if(agree_terms==false)
	 {		
	    highlightTableColumn('agree_terms');	
		alert("Please select I am agree");		
		document.userinfo.agree_terms.focus();
		resetTableColumn('wherehear');
		return false;
	 }
	 else {	 		
			resetTableColumn('agree_terms');
			return true;
	 }	
 }
function checkAge18()
{	  	 
	 var age_18 = document.userinfo.age_18.checked;
	
 	 if(age_18==false)
     {	
		highlightTableColumn('age_18');
		alert("Please you certify that I am at least 18 years of age");		
		resetTableColumn('agree_terms');
		document.userinfo.age_18.focus();
		return false;
		
     }	
	 else
	 {		       
	 		resetTableColumn('age_18');
			resetTableColumn('agree_terms');
			return true;
	 }	
 }
function submitAccountfrm()
{  
	if( !chkUserExistence() || !checkEmail() || !checkReEmail() || !checkPassword() || !checkRePassword() || !checkState() || !checkWherehear()|| !checkAgreement() || !checkAge18())
	{
		return false;
	}
	else{
		return true;
	}
}

//========= Start Step 2===============

function checkHeadline()
{	  	 
	 var headline = document.userinfo.headline.value;
	
 	if(headline=="")
    {
	 	highlightTableColumn('headline');
        alert(ERROR_DESC);		
		document.userinfo.headline.focus(); 		
		return false;
     } 	 
	 else
	 {		       
	 		resetTableColumn('headline');
			return true;
	 }	
	
 }
function checkProfileText()
{	  	 
	 var profile_text = document.userinfo.profile_text.value;
	
 	if(profile_text=="")
    {
	 	highlightTableColumn('profile_text');
        alert(ERROR_DESC);		
		document.userinfo.profile_text.focus(); 
		resetTableColumn('headline');
		return false;
     } 	 
	 else
	 {		       
	 		resetTableColumn('profile_text');
			resetTableColumn('headline');
			return true;
	 }	
	
 }
function viewProfile()
{	  
    var vuserid = document.userinfo.userid.value;
	var vbuild = document.userinfo.build.value;
	if(vbuild==1) { vbuild = "Ask Me";} else if(vbuild==2){ vbuild = "Slim";}else if(vbuild==3){ vbuild = "Swimmers";}
	else if(vbuild==4){ vbuild = "Athletic";}else if(vbuild==5){ vbuild = "Average";}else if(vbuild==6){ vbuild = "Muscular";}
	else if(vbuild==7){ vbuild = "Heavy Set";} else if(vbuild==8){ vbuild = "Bear";}
	
	var vposition = document.userinfo.position.value;
	if(vposition==1) { vposition = "Ask Me";} else if(vposition==2){ vposition = "Top";}else if(vposition==3){ vposition = "Bottom";}
	else if(vposition==4){ vposition = "Versatile";}else if(vposition==5){ vposition = "Into Oral Only";}else if(vposition==6){ vposition = "Into JO Only";}
	else if(vbuild==7){ vbuild = "Top/Vers.";} else if(vbuild==8){ vbuild = "Bottom/Vers.";}
	
	var vsexwhere = document.userinfo.sexwhere.value;
	if(vsexwhere==1) { vsexwhere = "Ask Me";} else if(vsexwhere==2){ vsexwhere = "At Your Place";}else if(vsexwhere==3){ vsexwhere = "At My Place";}
	else if(vposition==4){ vposition = "In ublic";}else if(vposition==5){ vposition = "Anywhere";}
																
														
	document.getElementById("vuserid").innerHTML = "<strong>"+vuserid+"</strong>";
	document.getElementById("vheadline").innerHTML= document.userinfo.headline.value;	 
	document.getElementById("vage").innerHTML="Age: "+ document.userinfo.age.value;	 
	document.getElementById("vbuild").innerHTML= "Build: "+vbuild;	 
	document.getElementById("vposition").innerHTML= "Position: "+vposition;	 
	document.getElementById("vsexwhere").innerHTML= "Prefer to meet: "+vsexwhere;	 
	 
}
	 
function submitProfilefrm()
{
	if( !checkHeadline() || !checkProfileText())
	{
		return false;
	}
	else{
		viewProfile();
		return true;
	}
}

function checkPhoto()
{	  	 
	 var public_picture = document.userinfo.public_picture.value;
	
 	if(public_picture=="")
    {
	 	highlightTableColumn('public_picture');
        alert("Please select at list one public pictr");		
		document.userinfo.public_picture.focus();
		return false;
     } 	 
	 else
	 {		       
	 		resetTableColumn('public_picture');
			return true;
	 }	
	
 }
function save()
{
	if(!checkPhoto())
	{
		return false;
	}
	else{
		return true;
	}
}

function cancelEmployer()
{
	window.location=CANCEL_URL;
}

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

/*
function addJobseeker_signup()
{  
   setJobseeker_signup();
  if(!doFormSubmit())
	{
	   return false;
	} 
  else
  {	
     return true;
  }  
 // document.frmemployer.Submit();  
}
*/
 function showAccountSetting(elid)
 {
    //alert(elid);
     
	 if(elid =="Save and Next" && submitAccountfrm())
	 {
	  document.getElementById('account').style.display = 'none';
	  document.getElementById('profile').style.display = 'block'; 
	  document.getElementById('photo').style.display = 'none'; 
	  return false;
	 }
	 else if(elid=="Save and Continue" && submitProfilefrm())
	 {
	   document.getElementById('account').style.display = 'none';
	   document.getElementById('profile').style.display = 'none'; 
	   document.getElementById('photo').style.display = 'block'; 
	    return false;
	 }
	 else if(elid==" Save " && save()){
	 
	  //document.userinfo.submit();  
	  document.getElementById('account').style.display = 'block';	 
	  document.getElementById('profile').style.display = 'none'; 
	  document.getElementById('photo').style.display = 'none'; 
	  return true;
	 }
	 else if(elid=="<< Back ")
	 {
	   document.getElementById('account').style.display = 'none';
	   document.getElementById('profile').style.display = 'block'; 
	   document.getElementById('photo').style.display = 'none'; 
	    return false;
	 }
	 else if(elid=="< Back ")
	 {
	   document.getElementById('account').style.display = 'block';
	   document.getElementById('profile').style.display = 'none'; 
	   document.getElementById('photo').style.display = 'none'; 
	    return false;
	 }		
	   
     
 } 
 //========== for add buddy========
function checkfrm()
{	  	 
	 var buddy_notes = document.addbuddy.buddy_notes.value;	
	
 	if(buddy_notes=="")
     {
	 	highlightTableColumn('buddy_notes');
        alert("Please write buddy notes");				
		document.addbuddy.buddy_notes.focus(); 		
		return false;
     }
	 else
	 {		       
	 		resetTableColumn('buddy_notes');
			return true;
	 }	
	
 }
 
 //========= Help ============
 
 //===========  Help Info ===========
 function show(id)
 {
 	//var onlinecontact = id.style.display; 
	var eid =document.getElementById(id).style.display;      
	 //alert(eid);
	 if(eid =="none")
	 {
	  document.getElementById(id).style.display = 'block';
	  document.getElementById('add'+id).src = "images/common/edit.gif";
	 }
	 else
	 {
	  document.getElementById(id).style.display = 'none'; 
	  document.getElementById('add'+id).src = "images/common/add.gif";
	 }
	 //alert(onlinecontact);	  
     
 }
   
//============= Start getHelpDesc for Help ============
function getHelpDesc(hid){
		//alert(hid);
		httpchkemail.open("GET", 'index.php?app=signup&cmd=view_helpdesc&help_id='+hid, true);
		httpchkemail.onreadystatechange = handleHelpResponse;  	
  	  	httpchkemail.send(null);  	
}

function handleHelpResponse()
{   
	//alert(trim(httpchkemail.responseText)); 	
    if(httpchkemail.readyState == 4)
    {        
      if(trim(httpchkemail.responseText) != "")
      {   
	        var desc_arr = Array(); 
	  	    var str =trim(httpchkemail.responseText).split("###");
			var desc = str[1];
	        desc_arr = desc.split("\n");	
			var description="";
			for(var i = 0; i < desc_arr.length; i++)
			{		
				description = description + "<p>"+ desc_arr[i] + "</p>";
				
			}
			if(description!="") {	    	 
           		document.getElementById('description').innerHTML=description;					 
			    document.getElementById('title').innerHTML="<b>"+str[0]+"</b>";
		    }
			else{
			 document.getElementById('description').innerHTML=desc;	 
			 document.getElementById('title').innerHTML="<b>"+str[0]+"</b>";
			 }
		document.getElementById("body").style.display = 'block';				 
      }
    }
    else 
    {
    	document.getElementById('status_msg').innerHTML = "Checking Existence. Please Wait...";
    }
    
}
//=====End getHelpDesc ======