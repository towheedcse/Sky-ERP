J(document).ready(function() {
	J('#h1').click(function(){
		J('#1').toggle('slow');						
								});
						   });



function showhideCashCheck(div_id)
{
	var all_div_id=new Array('Check','Cash','Recievable');
	for(var i=0; i<all_div_id.length; i++)
	{
		if(all_div_id[i]==div_id)
		{
			J('#'+all_div_id[i]).toggle('slow');			
		}
		else
		{
			J('#'+all_div_id[i]).hide('slow');
		}
	}
}

function showhideTransactionType(div_id)
{
	   //var all_div_id=new Array('Payment','Recieved');
	
		if(div_id=="Payment")
		{
			J('#Payment').toggle('slow');
			J('#Received').hide('slow');
			//document.getElementById(all_div_id[i]+"_f").className='validate[required]';		
		}
		else
		{
			J('#Received').toggle('slow');
			J('#Payment').hide('slow');
		}
	
	
}

function showhideToFrom(div_id)
{	
	if("1"==div_id)
	{		
		J('#'+'fromto').toggle('slow');			
	}
	else
	{
		J('#'+'fromto').hide('slow');
		
	}
	//==== 9 is Recieved amount=======
	if("9"==div_id)
	{			
		document.getElementById('transaction_type1').disabled=1;
		document.getElementById('transaction_type2').disabled=0;
		document.getElementById('transaction_type2').checked=1;
		document.getElementById('Payment_f').value=0;
		document.getElementById('Recieved_f').value=0;
				
		J('#Payment').hide('slow');	
		J('#Recieved').hide('slow');
		J('#Payment').toggle('slow');	
		
	}else{
		document.getElementById('transaction_type1').checked=1;
		document.getElementById('transaction_type2').disabled=1;
		document.getElementById('transaction_type1').disabled=0;
		document.getElementById('Payment_f').value=0;
		document.getElementById('Recieved_f').value=0;
		J('#Recieved').hide('slow');	
		J('#Payment').hide('slow');
		J('#Recieved').toggle('slow');	
		
		
	}
	
}

function show_hide(div_id)
{
	var all_div_id=new Array('divFab','divcolor','divSize','divTrim');
	for(var i=0; i<all_div_id.length; i++)
	{
		if(all_div_id[i]==div_id)
		{
			J('#'+all_div_id[i]).toggle('slow');
			document.getElementById('arrow_'+all_div_id[i]).src = "images/common/edit.gif";
		}
		else
		{
			J('#'+all_div_id[i]).hide('slow');
			document.getElementById('arrow_'+all_div_id[i]).src = "images/common/add.gif";
		}
	}
}

function showhideMasterOrder(div_id)
{
	var all_div_id=new Array('LC','TT');
	for(var i=0; i<all_div_id.length; i++)
	{
		if(all_div_id[i]==div_id)
		{
			J('#'+all_div_id[i]).toggle('slow');			
		}
		else
		{
			J('#'+all_div_id[i]).hide('slow');
		}
	}
}

function showHideBuyerOrder(div_id)
{
	var all_div_id=new Array('divcolor','divSize');
	for(var i=0; i<all_div_id.length; i++)
	{
		if(all_div_id[i]==div_id)
		{
			J('#'+all_div_id[i]).toggle('slow');
			document.getElementById('arrow_'+all_div_id[i]).src = "images/common/edit.gif";
		}
		else
		{
			J('#'+all_div_id[i]).hide('slow');
			document.getElementById('arrow_'+all_div_id[i]).src = "images/common/add.gif";
		}
	}
}

function show_hide_menu(div_id)
{

	var all_div_id=new Array('home','sample','order','my_account','buddies','setup','hrm_menu');

	for(var i=0; i<all_div_id.length; i++)

	{

		if(all_div_id[i]==div_id)

		{

			J('#'+all_div_id[i]).toggle('slow');

			if(document.getElementById('arrow_'+all_div_id[i]).className=='arrow_down')

			document.getElementById('arrow_'+all_div_id[i]).className='arrow_up';

			else

			document.getElementById('arrow_'+all_div_id[i]).className='arrow_down';

		}

		else

		{

			J('#'+all_div_id[i]).hide('slow');

			document.getElementById('arrow_'+all_div_id[i]).className='arrow_down';

		}

	}

}

function validation(txt){
	
	if(document.getElementById('sizespec'+txt).value!=""){
			if(totalsize>0 && totalsize <=10){
				document.getElementById('a'+txt).className='validate[required]';
			}				
			if(totalsize>1 && totalsize <=10){
				document.getElementById('b'+txt).className='validate[required]';
			}	
			if(totalsize>2 && totalsize <=10){
				document.getElementById('c'+txt).className='validate[required]';
			}
			if(totalsize>3 && totalsize <=10){
				document.getElementById('d'+txt).className='validate[required]';
			}
			if(totalsize>4 && totalsize <=10){
				document.getElementById('e'+txt).className='validate[required]';
			}
			if(totalsize>5 && totalsize <=10){
				document.getElementById('f'+txt).className='validate[required]';
			}
			if(totalsize>6 && totalsize <=10){
				document.getElementById('g'+txt).className='validate[required]';
			}
			if(totalsize>7 && totalsize <=10){
				document.getElementById('h'+txt).className='validate[required]';
			}
			if(totalsize>8 && totalsize <=10){
				document.getElementById('i'+txt).className='validate[required]';
			}			
			if(totalsize>9 && totalsize <=10){
				document.getElementById('j'+txt).className='validate[required]';
			}
		
	}else{
			if(totalsize>0 && totalsize <=10){
				document.getElementById('a'+txt).className='validate[no]';
			}				
			if(totalsize>1 && totalsize <=10){
				document.getElementById('b'+txt).className='validate[no]';
			}	
			if(totalsize>2 && totalsize <=10){
				document.getElementById('c'+txt).className='validate[no]';
			}
			if(totalsize>3 && totalsize <=10){
				document.getElementById('d'+txt).className='validate[no]';
			}
			if(totalsize>4 && totalsize <=10){
				document.getElementById('e'+txt).className='validate[no]';
			}
			if(totalsize>5 && totalsize <=10){
				document.getElementById('f'+txt).className='validate[no]';
			}
			if(totalsize>6 && totalsize <=10){
				document.getElementById('g'+txt).className='validate[no]';
			}
			if(totalsize>7 && totalsize <=10){
				document.getElementById('h'+txt).className='validate[no]';
			}
			if(totalsize>8 && totalsize <=10){
				document.getElementById('i'+txt).className='validate[no]';
			}			
			if(totalsize>9 && totalsize <=10){
				document.getElementById('j'+txt).className='validate[no]';
			}	}
				
}
function validation4Bottom(txt){
	
	if(document.getElementById('btmsizespec'+txt).value!=""){
			if(totalsize>0 && totalsize <=10){
				document.getElementById('btma'+txt).className='validate[required]';
			}				
			if(totalsize>1 && totalsize <=10){
				document.getElementById('btmb'+txt).className='validate[required]';
			}	
			if(totalsize>2 && totalsize <=10){
				document.getElementById('btmc'+txt).className='validate[required]';
			}
			if(totalsize>3 && totalsize <=10){
				document.getElementById('btmd'+txt).className='validate[required]';
			}
			if(totalsize>4 && totalsize <=10){
				document.getElementById('btme'+txt).className='validate[required]';
			}
			if(totalsize>5 && totalsize <=10){
				document.getElementById('btmf'+txt).className='validate[required]';
			}
			if(totalsize>6 && totalsize <=10){
				document.getElementById('btmg'+txt).className='validate[required]';
			}
			if(totalsize>7 && totalsize <=10){
				document.getElementById('btmh'+txt).className='validate[required]';
			}
			if(totalsize>8 && totalsize <=10){
				document.getElementById('btmi'+txt).className='validate[required]';
			}			
			if(totalsize>9 && totalsize <=10){
				document.getElementById('btmj'+txt).className='validate[required]';
			}
		
	}else{
			if(totalsize>0 && totalsize <=10){
				document.getElementById('btma'+txt).className='validate[no]';
			}				
			if(totalsize>1 && totalsize <=10){
				document.getElementById('btmb'+txt).className='validate[no]';
			}	
			if(totalsize>2 && totalsize <=10){
				document.getElementById('btmc'+txt).className='validate[no]';
			}
			if(totalsize>3 && totalsize <=10){
				document.getElementById('btmd'+txt).className='validate[no]';
			}
			if(totalsize>4 && totalsize <=10){
				document.getElementById('btme'+txt).className='validate[no]';
			}
			if(totalsize>5 && totalsize <=10){
				document.getElementById('btmf'+txt).className='validate[no]';
			}
			if(totalsize>6 && totalsize <=10){
				document.getElementById('btmg'+txt).className='validate[no]';
			}
			if(totalsize>7 && totalsize <=10){
				document.getElementById('btmh'+txt).className='validate[no]';
			}
			if(totalsize>8 && totalsize <=10){
				document.getElementById('btmi'+txt).className='validate[no]';
			}			
			if(totalsize>9 && totalsize <=10){
				document.getElementById('btmj'+txt).className='validate[no]';
			}	}
				
}
function change_task(task)

{

	document.getElementById('task').value=task;

}



function show_info()

{

	var first='	Think of a member name that';

	document.getElementById('info').innerHTML=first;

		

}



function collaps_div(div_id,edit){

J('#'+div_id).toggle('slow');	

J('#'+edit).toggle('slow');	

}