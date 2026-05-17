RE_NUMBER           = new RegExp(/^[0-9]+$/);

RE_EMAIL    = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);

RE_NAME     = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);

//onClick="if(addInquiry()){saveInquiry();return true;}else{ return false;}

var httpSave      = getHTTPObject();



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


function deleteRecord(code)
{	
   var url_loc = "index.php?app=bank&cmd=delete&id="+code;
   
   window.location = url_loc;
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