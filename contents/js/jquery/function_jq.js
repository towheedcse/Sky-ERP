// JavaScript Document

J(document).ready(function() {
	J('#chk_vc').click(function(){
			
			if(document.getElementById('chk_vc').checked)
				J('#div_vc').show('slow');
			else
				J('#div_vc').hide('slow');
	});
});

// For Angel
J(document).ready(function() {
	J('#chk_angel').click(function(){
			if(document.getElementById('chk_angel').checked)
				J('#div_angel').show('slow');
			else
				J('#div_angel').hide('slow');
	});
});

// For Angel
J(document).ready(function() {
	J('#chk_grants').click(function(){
			if(document.getElementById('chk_grants').checked)
				J('#div_grants').show('slow');
			else
				J('#div_grants').hide('slow');
	});
});

// For Angel
J(document).ready(function() {
	J('#chk_loans').click(function(){
			if(document.getElementById('chk_loans').checked)
				J('#div_loans').show('slow');
			else
				J('#div_loans').hide('slow');
	});
});

//
J(document).ready(function() {
	J('#signup_investor').submit(function(){
			var checked=false;
			if(document.getElementById('chk_vc').checked)
				checked=true;
				
			if(document.getElementById('chk_angel').checked)
				checked=true;
				
			if(document.getElementById('chk_grants').checked)
				checked=true;
				
			if(document.getElementById('chk_loans').checked)
				checked=true;

					if(!checked){
						if(!confirm("Are you sure you wouldn't choose any investor type?")){
							return false;
						}
					}

	});
});

J(document).ready(function() {
	J('#schedule_time').blur(function(){
			document.pitchsetup.sch_time.value=document.pitchsetup.schedule_time.options[document.pitchsetup.schedule_time.selectedIndex].value;

	});
});

J(document).ready(function() {
	J('#pitchsetup').submit(function(){
			if(document.pitchsetup.schedule_time.disabled){
				alert('No Time slot is available');
				return false;
			}
			else if(document.pitchsetup.sch_time.value==''){
				alert('Please select a time first');
				return false;
			}

	});
});
/*
J(document).ready(function() {
	J('#edit_profile').submit(function(){
			if(document.getElementById('chk_vc').checked){
				var name=document.edit_profile.vc_com_name.value;
				alert(name);
				if(name==""){
					alert('Please put a company name');
					return false;
				}

				
			}
			else if(document.getElementById('chk_angel').checked){
				alert('Please put a group name and website');
				return false;				
			}
			else if(document.getElementById('chk_grants').checked){
				alert('Please put a organisation name and website');
				return false;				
			}
			else if(document.getElementById('chk_loans').checked){
				alert('Please put a institution name and website');
				return false;				
			}			
			
	});
});
*/

function checkAll( n, fldName ) {
  if (!fldName) {
     fldName = 'cb';
  }
	var f = document.sentItems;
	var c = f.toggle.checked;
	var n2 = 0;
	for (i=0; i < n; i++) {
		cb = eval( 'f.' + fldName + '' + i );
		if (cb) {
			cb.checked = c;
			n2++;
		}
	}
	if (c) {
		document.sentItems.boxchecked.value = n2;
	} else {
		document.sentItems.boxchecked.value = 0;
	}
}

function isChecked(isitchecked){
	if (isitchecked == true){
		document.sentItems.boxchecked.value++;
	}
	else {
		document.sentItems.boxchecked.value--;
	}
}
function submitbutton(pressbutton) {
	submitform(pressbutton);
}

/**
* Submit the admin form
*/
function submitform(pressbutton){
	if (typeof document.sentItems.onsubmit == "function") {
		document.sentItems.onsubmit();
	}
	document.sentItems.submit();
}

function submitSentItemForm(){
if(document.sentItems.boxchecked.value==0){
	alert('Please make a selection from the list to delete');
	return false;
	}
	else{
		if(confirm('Are you sure to delete message?')){
			
			}
		else{return false;}
	}	
	
}

J(document).ready(function() {
	J('#private_meeting_step3').submit(function(){
			if(document.private_meeting_step3.message.value==""){
				alert('Please put a meesage');
				return false;
			}
	});
});

J(document).ready(function() {
	J('#private_meeting_step2').submit(function(){
			var reg = new RegExp("^[a-zA-Z0-9_\.\-]+\@([a-zA-Z0-9\-]+\.)+[a-zA-Z0-9]{2,4}$");
			if(document.private_meeting_step2.investor_email.value=="" ){
				alert('You must put an email address');
				return false;
			}
			else if(!reg.test(document.private_meeting_step2.investor_email.value)){
				alert('Put an valid email address');
				return false;
			}				
	});
});