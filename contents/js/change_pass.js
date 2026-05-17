RE_NUMBER           = new RegExp(/^[0-9]+$/);

ERROR_NUMBER        = "Please enter a valid number.";
ERROR_COMPANY       = "Please Select a Company";
ERROR_RENEWAL_DATE  = "Please Enter Contract Renewal Date";
ERROR_MAINT_DATE    = "Please Enter Next Maintenance Date";
ERROR_MODEL         = "Please Select a Model";
ERROR_STATUS        = "Please Select Status";
ERROR_VENDOR        = "Please Select Vendor";
ERROR_CATEGORY      = "Please Select Category";
ERROR_WARRANTY_DATE = "Please Enter Warranty Expiration Date";
ERROR_SERVICE_DATE  = "Please Enter Service Date";
ERROR_INSTALL_DATE  = "Please Enter Installation Date";
ERROR_COMPANY       = "Please Select a Company";

var httpCheckTrace      = getHTTPObject();
var httpCheckReg        = getHTTPObject();
var httpCheckUser        = getHTTPObject();

/*
var httpDeleteAll   = getHTTPObject();
var httpAdd         = getHTTPObject();
var httpUpdate      = getHTTPObject();
var httpFindFile      = getHTTPObject();
*/

var isIE            = document.all;

var service_file_name = "";

var isTrace = "";
var isReg = "";
var isUser = "";

function delDoc(id)
{
  
   httpDelete.onreadystatechange = handleDelResponse;
   httpDelete.open("GET", 'installed_system_manager.php?cmd=delDoc&id='+id); 
   httpDelete.send(null);   
}

function checkRegCode()
{
   var regCode = document.signupForm.regcode.value;
   var traceId = document.signupForm.traceid.value;
   
   if(traceId=='')
   {
      alert('Please Enter Trace Id First');
   }
   else if(regCode!='')
   {
      httpCheckReg.onreadystatechange = handleRegResponse;
      httpCheckReg.open("GET", 'index.php?app=signup&cmd=checkRegCode&code='+regCode+'&traceId='+traceId, true);
      httpCheckReg.send(null);
   }
}

function handleRegResponse()
{
   var textToWrite = "";
   
   if(httpCheckReg.readyState == 4)
   {
      if(httpCheckReg.responseText=="Valid")
      {
         isReg="Valid";
         textToWrite = "Registration Code Correct. Please Proceed.";
      }
      else if(httpCheckReg.responseText=="Invalid")
      {  
      	 isReg="Invalid";
         textToWrite = "Registration Code Incorrect. Please Correct It.";
      }

      document.getElementById('response_msg').style.display = "block";
      document.getElementById('response_msg').innerHTML = textToWrite;
      

   }
   else
   {
      document.getElementById('response_msg').innerHTML = "Checking Existence. Please Wait...";
   }
}//EOFn


function checkUserId()
{
   var userId = document.signupForm.userid.value;

   if(userId!='')
   {
      httpCheckUser.onreadystatechange = handleUserResponse;
      httpCheckUser.open("GET", 'index.php?app=signup&cmd=checkUser&userId='+userId, true);
      httpCheckUser.send(null);
   }
}

function handleUserResponse()
{
   var textToWrite = "";

   if(httpCheckUser.readyState == 4)
   {

      if(httpCheckUser.responseText=="Yes")
      {
         isUser = "Valid";
         textToWrite = "User ID Available. Please Proceed To Signup.";
      }
      else if(httpCheckUser.responseText=="No")
      {
      	 isUser = "Invalid";
         textToWrite = "User ID Already Exists. Please Try Another.";
      }

      document.getElementById('response_msg').style.display = "block";
      document.getElementById('response_msg').innerHTML = textToWrite;

   }
   else
   {
      document.getElementById('response_msg').innerHTML = "Checking Existence. Please Wait...";
   }
}//EOFn

function checkTraceId(traceId)
{
  
   //var traceId = document.signupForm.traceid.value;
   
   if(traceId!='')
   {
      httpCheckTrace.onreadystatechange = handleTraceResponse;   
      httpCheckTrace.open("GET", 'index.php?app=signup&cmd=checkTraceId&id='+traceId, true); 
      httpCheckTrace.send(null);   
   }
}

function handleTraceResponse()
{
    var textToWrite = "";
    
    if(httpCheckTrace.readyState == 4)
    {
				alert(httpCheckTrace.responseText);
      if(httpCheckTrace.responseText=="Valid")
      {
      	 isTrace = "Valid";
         textToWrite = "Trace ID Correct. Please Proceed To Signup.";
      }
      else if(httpCheckTrace.responseText=="Invalid")
      {
      	 isTrace = "Invalid";
         textToWrite = "Trace ID Incorrect. Please Try Again.";
      }

      document.getElementById('response_msg').style.display = "block";
      document.getElementById('response_msg').innerHTML = textToWrite;

    }
    else 
    {
       document.getElementById('response_msg').innerHTML = "Checking Existence. Please Wait...";
    }
           
}

function handleDelResponse()
{        
                if(httpDelete.readyState == 4)
                {        
        document.getElementById('doc_delete').innerHTML = "<b>File Deleted</b>";
        document.getElementById('docUpload').innerHTML = "";
        document.docAddForm.document_file.value="";        
    }
    else 
    {
              document.getElementById('doc_delete').innerHTML = "Deleting File. Please wait...";
    }
        
} //EOFn

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


function deleteInstalledSystem(installedSystemID)
{
        httpDelete.open("GET", "installed_system_manager.php?cmd=delete&installedSystemID="+installedSystemID, true);
        httpDelete.onreadystatechange = handleDeleteResponse;
  httpDelete.send(null);
}

function deleteIntake(intakeID)
{
   var url_loc = "index.php?app=intake&cmd=delete&id="+intakeID;
   window.location = url_loc;
}

function handleDeleteResponse()
{
        
        if (httpDelete.readyState == 4)
        {
                                                
    var results = httpDelete.responseText.split(",");
    alert(httpDelete.responseText);
    if( (results[0] == "DELETE_INSTALLED_SYSTEM") && (results[1] == "SUCCESS") )
    {
                        
            var checked_vals = Array();
      checked_vals = document.chkForm.chkBox;
                                    
            var len = checked_vals.length;
            
            if(len==undefined)
            {
                     len = 1;
            }
            
            var row = document.getElementById(results[2]);
            row.parentNode.removeChild(row);
            
            len--;
            
            document.getElementById("status_msg").style.display = "block";
            document.getElementById("status_msg").innerHTML = MSG_DELETE_ONE;
            
            if(len==0)
      {
         document.getElementById('fieldset').style.display = 'none';           
      }
  
    }
          else
          {
                              
            document.getElementById("status_msg").style.display="block";
            document.getElementById("status_msg").innerHTML = MSG_NOT_DELETED;


          }
          
  }
}


function categoryChanged()
{
        var category;
        var catIndex;
        
        catIndex=document.getElementById("InstalledSystemCategory").selectedIndex;
        
        if(isIE)
        {
                category=document.getElementById("InstalledSystemCategory").options[catIndex].text;
        }
  else
  {
           category=document.getElementById("InstalledSystemCategory").options[catIndex].value;
  }            
        
        if(catIndex!=0)
        {
     window.location = "/app/installed_system_manager/installed_system_manager.php?cmd=showByCategory&category=" + category;
  }
}


function selectAll()
{
         var installed_system_table = document.getElementById("installed_systems");
         count = installed_system_table.rows.length;
 
         var select_state = 1;
         
         if(!document.getElementById("select_all").checked)
   {
           select_state = 0;
   }
 
         for(var i = 1; i < count; i++)
         installed_system_table.rows[i].cells[0].childNodes[0].checked = select_state; 
}

function allSelected()
{
        var installed_system_table = document.getElementById("installed_systems");
        count = installed_system_table.rows.length;
        
        var deSelected = 0;
        
        for(var i = 1; i < count; i++)
  {
           if(!installed_system_table.rows[i].cells[0].childNodes[0].checked)
           {
                     deSelected++;
           }
  }
  
  if(deSelected)
  {
          return 1;
  }
  
  return 0;
}

function uncheckTop()
{
        document.getElementById("select_all").checked = 0;
}

function deleteAll()
{
        
  var selectedInstalledSystems="";  
  var checked_vals = Array();
  checked_vals = document.chkForm.chkBox;    
  
  var cnt=0;
    
  var len = 0;
  
  if(checked_vals.length==undefined)
  {
    len = 1;
    
       if (checked_vals.checked)
      {
         var row = document.getElementById(checked_vals.value);
         
         selectedInstalledSystems = checked_vals.value+",";
         
         cnt++;
               
      }

  }
  else
        {
  
  len = checked_vals.length;
  
  for(var i=0;i<len;i++)
  {
            if(checked_vals[i].checked)
            {                      
                      selectedInstalledSystems=selectedInstalledSystems + checked_vals[i].value+",";
                      cnt++;
            }
 
  }
 }
  if(len==0 || cnt==0)
  { 
           document.getElementById("status_msg").style.display = "block";
     document.getElementById("status_msg").innerHTML = MSG_SELECT_NONE;           
     alert(MSG_SELECT_NONE);
  }    
  else
        {
  httpDeleteAll.open("GET", "installed_system_manager.php?cmd=deleteAllSelected&installedSystemIDs="+selectedInstalledSystems, true);
        httpDeleteAll.onreadystatechange = handleDeleteAllResponse;
  httpDeleteAll.send(null); 
  }
}


function handleDeleteAllResponse()
{
        if (httpDeleteAll.readyState == 4)
        {
     var results = Array();
     results = httpDeleteAll.responseText.split(",");     
     
     if( (results[0] == "DELETE_ALL_INSTALLED_SYSTEM") && (results[1] == "SUCCESS") )
     {                                     
        
        var checked_vals = Array();
        checked_vals = document.chkForm.chkBox;

        var cnt=0;
    
        var len = 0;
        
        if(checked_vals.length==undefined)
        {
          len = 1;
          
            if (checked_vals.checked)
            {
               var row = document.getElementById(checked_vals.value);                                                         
               
               row.parentNode.removeChild(row);         
               
               len--;
            }
        
        }        
        else
              {
            len = checked_vals.length;
            
            for(var i=0;i<len;)
            {
                      if(checked_vals[i].checked)
                      {                      
                                 var row = document.getElementById(checked_vals[i].value);
                                row.parentNode.removeChild(row);
                    len--;
                      }
                      else
                            {
                                    i++;
                            }
            
            }
            document.getElementById("status_msg").style.display="block";
                  document.getElementById("status_msg").innerHTML=MSG_DELETE_ALL;                                   
                  
        }
        
        if(len==0)
        {
           document.getElementById('fieldset').style.display = 'none';           
        }
     }
     else
     {
        window.location="/app/installed_system_manager/installed_system_manager.php?cmd=showAll";
     } 
  }                            

}


function add()
{
        //if(!validateForm())
  if(!doFormSubmit())
        return;
        
        var serialNumber            = document.getElementById("serial_number").value;
        var installationDate        = document.getElementById("installation_date").value;
        var ITSystemID              = document.getElementById("it_system_id").value;
        var lastServiceDate         = document.getElementById("last_service_date").value;
        var warrantyExpirationDate  = document.getElementById("warranty_expiration_date").value;
        var serviceContract         = document.getElementById("usc").checked;
        var purchasePrice           = document.getElementById("purchase_price").value;
        var nextMaintDate           = document.getElementById("next_maint_date").value;
        var contractRenewalDate     = document.getElementById("contract_renewal_date").value;
        var comments                = document.getElementById("comments").value;

  var systemStatus            = document.addEditForm.status.value;
        var category                = document.addEditForm.category.value;
        var customerCompany         = document.addEditForm.customer_companies.value;
        var model                   = document.addEditForm.models.value;
        var vendor                  = document.addEditForm.vendors.value;
                


  httpAdd.open("GET", "installed_system_manager.php?cmd=add&customer_company="+customerCompany+"&serial_number="+serialNumber+"&installation_date="+installationDate+"&it_system_id="+ITSystemID+"&last_service_date="+lastServiceDate+"&warranty_expiration_date="+warrantyExpirationDate+"&service_contract="+serviceContract+"&purchase_price="+purchasePrice+"&next_maint_date="+nextMaintDate+"&contract_renewal_date="+contractRenewalDate+"&comments="+comments+"&status="+systemStatus+"&category="+category+"&model="+model+"&vendor="+vendor, true);
        httpAdd.onreadystatechange = handleAddResponse;
  httpAdd.send(null);

}

function update()
{
  if(!doFormSubmit())
        return;
        
        var serialNumber            = document.getElementById("serial_number").value;
        var installationDate        = document.getElementById("installation_date").value;
        var ITSystemID              = document.getElementById("it_system_id").value;
        var lastServiceDate         = document.getElementById("last_service_date").value;
        var warrantyExpirationDate  = document.getElementById("warranty_expiration_date").value;
        var serviceContract         = document.getElementById("usc").checked;
        var purchasePrice           = document.getElementById("purchase_price").value;
        var nextMaintDate           = document.getElementById("next_maint_date").value;
        var contractRenewalDate     = document.getElementById("contract_renewal_date").value;
        var comments                = document.getElementById("comments").value;
        var installedSystemId       = document.getElementById("installed_system_id").value;
        

  var systemStatus            = document.getElementById("status").value;
        var category                = document.getElementById("category").value;
        var customerCompany         = document.getElementById("customer_companies").value;
        var model                   = document.getElementById("models").value;
        var vendor                  = document.getElementById("vendors").value;        

        httpUpdate.open("GET", "installed_system_manager.php?cmd=edit&customer_company="+customerCompany+"&serial_number="+serialNumber+"&installation_date="+installationDate+"&it_system_id="+ITSystemID+"&last_service_date="+lastServiceDate+"&warranty_expiration_date="+warrantyExpirationDate+"&service_contract="+serviceContract+"&purchase_price="+purchasePrice+"&next_maint_date="+nextMaintDate+"&contract_renewal_date="+contractRenewalDate+"&comments="+comments+"&status="+systemStatus+"&category="+category+"&model="+model+"&vendor="+vendor+"&installed_system_id="+installedSystemId, true);
        httpUpdate.onreadystatechange = Handleupdateresponse;
  httpUpdate.send(null);
 
}

function doFormSubmit()
{
   requiredFields.length = 0;

   var errCnt = 0;
   var frm = document.change_pass_frm;

   // Setup required fields
   setupForm(frm);

   // Validate form for required fields
   errCnt = validateForm(frm);

   if (errCnt)
   {
      alert(MISSING_REQUIRED_FIELDS);      
      return false;
   }
   else
   {
      if(validateFields(frm))
      {
         return true;
      }
      else
         return false;
   }
}

function setupForm(frm)
{
   with (frm)
   {
   
      setRequiredField(traceid,           'textbox',   'traceid');
      setRequiredField(regcode,        'textbox',    'regcode');
      setRequiredField(userid,          'textbox',       'userid');
      setRequiredField(password,          'textbox',     'password');
   }
}


function validateFields(frm)
{
   with(frm)
   {
      if (traceid.value == "")
      {
         highlightTableColumn('traceid');
         alert('Enter Trace Id');
         return false;
      }
      else if(regcode.value=="")
      {
         highlightTableColumn('regcode');
         alert('Enter Registration Code');
         return false;
      }
      else if(userid.value=="")
      {
         highlightTableColumn('userid');
         alert('Enter User Id');
         return false;
      }
      else if(password.value=="")
      {
         highlightTableColumn('password');
         alert('Enter Password');
         return false;
      }
      else
      {
         return true;
      }
    }
    
    
   return true;
}


function handleUpdateResponse()
{
        if (httpUpdate.readyState == 4)
        {
                //document.getElementById('debug_res').innerHTML=httpUpdate.responseText;
                
    var results = Array();
    results = httpUpdate.responseText.split(",");
    
    if( (results[0] == "INSTALLED_SYSTEM_EDIT") && (results[1] == "SUCCESS") )
    {
            window.location="/app/installed_system_manager/installed_system_manager.php?cmd=showAll&edit=success";
    }
          else
          {
                  
            document.getElementById("status_msg").style.display="block";
            document.getElementById("status_msg").innerHTML=MSG_SYS_UPDATE_FAILED;
                  
          }
          
  }
}

function handleAddResponse()
{
        if (httpAdd.readyState == 4)
        {                
    
    var results = Array();
    
    results = httpAdd.responseText.split(",");
        
    
    if( (results[0] == 'INSTALLED_SYSTEM_ADD') && (results[1] == 'SUCCESS') )
    {
            window.location="/app/installed_system_manager/installed_system_manager.php?cmd=showAll&add=success";
    }
          else
          {
                  
                   document.getElementById("status_msg").style.display="block";
             document.getElementById("status_msg").innerHTML=MSG_SYS_ADD_FAILED;
                  
          }
          
  }
}


function findInstalledSystem()
{
        var systemID       = Trim(document.getElementById("fnd_sys_id").value);
        var customerNumber = Trim(document.getElementById("fnd_cust_num").value);
        var serialNumber   = Trim(document.getElementById("fnd_serial_number").value);
        var ITSystemID     = Trim(document.getElementById("fnd_itsysid").value);
        //var ITSysCategory  = Trim(document.getElementById("InstalledSystemCategory").value);
        var catIndex;
        
        catIndex=document.getElementById("InstalledSystemCategory").selectedIndex;

  var ITSysCategory;
  
  if(isIE)
        {
                ITSysCategory=Trim(document.getElementById("InstalledSystemCategory").options[catIndex].text);
        }
  else
  {
           ITSysCategory=Trim(document.getElementById("InstalledSystemCategory").options[catIndex].value);
  }            
  
        
        if(systemID=="" && customerNumber=="" && serialNumber=="" && ITSystemID=="" && ITSysCategory=="-1")
        {
                alert("Sorry, you did not supply any value for searching. Please supply atleast one value.");
                return;
        }
        
        var findMethod;
        if(document.getElementById("sortByAll").checked)
        {
           findMethod="All"
  }
        else 
        {
           findMethod="Any";
  }
        window.location="/app/installed_system_manager/installed_system_manager.php?cmd=find&find_system_id="+systemID+"&find_customer_number="+customerNumber+"&find_serial_number="+serialNumber+"&find_it_system_id="+ITSystemID+"&find_method="+findMethod+"&find_category="+ITSysCategory;
}

function sortByLastServiceDate()
{
        sortTable(document.getElementById("installed_systems"),5);
}

function sortByWarrantyExpirationDate()
{
        sortTable(document.getElementById("installed_systems"),6);        
}

function cancel()
{
        window.location="/app/installed_system_manager/installed_system_manager.php?cmd=showAll";
}

function cancelIntake()
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

function sortTable(tableRef,col)
{
        
        tableRowNo = tableRef.rows.length-1;
        
        
        for(i=1;i<tableRowNo;i++)
        {
                for(j=i;j<tableRowNo;j++)
                {
                        if(tableRef.rows[i].cells[col].childNodes[0].nodeValue >tableRef.rows[j].cells[col].childNodes[0].nodeValue)
                        {
                        //var temp=tableRef.rows[i];
                        var temp = tableRef.replaceChild(tableRef.rows[j],tableRef.rows[i]);
                        //alert(temp);
                        tableRef.insertBefore(temp,tableRef.rows[j])
                        
                        
                        }
                }
        }
        
}

function findFileById(sys_id)
{

   httpFindFile.open("GET", "installed_system_manager.php?cmd=findFileById&installedSystemID="+sys_id, true);
   httpFindFile.onreadystatechange = handleFindFileResponse;
   httpFindFile.send(null); 

}

function handleFindFileResponse()
{
          if (httpFindFile.readyState == 4)
          {        
             res = httpFindFile.responseText;
             
             alert(res);
             
             if(res == undefined)
             {
                service_file_name = "";
             }          
             else
             {
                service_file_name = res;

             }
          }
}

function SubmitToChangePass()
{

  if(document.change_pass_frm.new_password.value != document.change_pass_frm.confirm_password.value)
  {
     alert("Password And Confirm Password Not Equal");
     return false;
  }
  else
  {        
        return true;
  } 
}

