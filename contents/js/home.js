var httpRemoveComments      = getHTTPObject();

//
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
                  } 
                  catch(e) {
                          xmlhttp = false;
                  }
            }
     }
  }
  return xmlhttp;
}
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
function _select(elid)
{
	if(elid.checked == true)
	{
		elid.checked = true;
	}
	else 
	{
		elid.checked= false;
	}
}

function _selectAll(elid)
{
	if(elid.checked == true)
	{
		elid.checked = false;
	}
	else 
	{
		elid.checked= true;
	}
}

//====== function deleteCommentsList() is used for delete message from list==========
//====== user_comments_view.html ==================================================== 	
function deleteCommentsList()
{
	sure = confirm("Are you sure want to remove row(s)???");
	var _remove = Array();
	var commentsid ='';
	var j=0;
	//alert(chk_remove_from_list);
	for(j; j<=chk_remove_from_list; j++)
	{		
		_remove[j] = document.getElementById('chk'+j);

		if ( _remove[j].checked == true)
		{
			  commentsid += _remove[j].value+',';
		}
		
	}
	//alert(commentsid);
	//removeRows(commentsid);
	if(sure)
	{
		window.location="index.php?app=home&cmd=deleteCommentsList&commentsid="+commentsid;
	}
}

//=======+++++++++==============
function showCommentsDetail(commentsid)
{
	window.location="index.php?app=home&cmd=showCommentsDetail&commentsid="+commentsid;
}

/*function removeRows(commentsid)
{
      httpRemoveComments.open("GET", 'index.php?app=home&cmd=deleteCommentsList&commentsid='+commentsid, true);
      httpRemoveComments.onreadystatechange = handleRemoveResponse;
      httpRemoveComments.send(null);
}//EO Fn 

//============================================================================================

function handleRemoveResponse()
{
    if(httpRemoveComments.readyState == 4)
    {
       //document.getElementById('status_msg').style.display = "block";
       //document.getElementById('status_msg').innerHTML = httpLoadStudent.responseText;
      var str = httpRemoveComments.responseText;
      if(trim(str) != '0')
      {
      	var tr;
      	var trid = Array();
      	var obj = document.getElementById('tbd');
      	//alert(obj.id);
      	var commentsid = str.split(",");
      	for(var i=0; i < commentsid.length; i++)
      	{
      		trid = commentsid[i];
      		//alert(commentsid[i]);
      		//var row = document.getElementById(results[2]);
    	    //row.parentNode.removeChild(row);
      		tr = document.getElementById('"'+trid+'"');
      		//alert(tr.id);
      		tr.obj.removeChild(tr);
      	}
      }
    }
    else
    {
       //document.getElementById('status_msg').innerHTML = "Checking Existence. Please Wait...";
    }
}
*/

//===========+++==============
var tr_no  = 0;
var elem = new Array();

/*function elementAdd(trid,chkid)
{
	elem[trid] = chkid;
	//alert("b4call"+elem[trid]);
}

function elementRemove(trid)
{
	elem[trid] = null;
}
*/
/*function removeTR()
{
	var sure;
	var trid;
	var chkid = String();
    var tblObj = document.getElementById("tbl");
	sure = confirm("Are you sure want to remove row(s)???");
	if(sure)
	{
		//var el_collection=eval(document.forms.form1.chk);
		//alert('el_col '+el_collection.length);
		//for (c=0;c<=el_collection.length;c++)
		for(c=0;c<elem.length;c++)
		{
			if(elem[c] != null)
			{
				chk = document.getElementById('chk'+c);
				chkid = chk.id;
				//alert('chkid ' + chkid);
				if(chk.checked == true)
				{
					trid = chkid.substr(3);
				  //alert('trid '+trid);
					tr = document.getElementById(trid);
					//alert(tr.id);
					tblObj.removeChild(tr);
					elementRemove(trid);
				}
			}
		}
	}
	elem.toString();
}
*/

/*======   This function is used for checking all message at a time    ==================
           user_comments_view.html
           Date : 
*/
function CheckAll(chk, frm)
{
	var chkall = document.getElementById('chkall');
	var tf;
	if(chkall.checked == true)
	{
		tf = true;
	}
	else
	{
		tf = false;
	}
		var el_collection = eval("document.forms."+frm+"."+chk);
		for(c=0;c<=el_collection.length;c++)
		{
			if(tf)
			{
				//el_collection[c].checked = false;
				_selectAll(el_collection[c]);
			}
			else
			{
				//el_collection[c].checked = true;
				_selectAll(el_collection[c]);
			}
		}
}//EoF CheckAll

/* =======  This function is used for validate empty field  ============================
 					  faq_user.html
 					  date : 21-09-2006
*/
function addFaq_user()
{
	var user_question = document.getElementById('user_question').value;	
	if(user_question)
	{
		return true;	
	}
	else
	{
		alert('Question can not be empty...');
		faq_frm.user_question.focus();
		return false;		
	}
	
}