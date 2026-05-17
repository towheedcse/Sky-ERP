<?php
class AccountsLedger
{
  function run()
   {         

      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');
      if( ($u_t_id == 101) || ($u_t_id ==102) || ($u_t_id == 103) || ($u_t_id == 104) || ($u_t_id == 109)) // 1 = sysadmin, 2 = admin, 4 = acc
      {

      	switch ($cmd)
      	{      	   
      	   case 'show'  		: $this->showEditor(trim(getRequest('product_id'))); break; 
	   case 'inventory_day_book'  	: $this->showInventoryDayBook(trim(getRequest('product_id'))); break;
      	   case 'show_summary'  	: $this->showSummaryEditor(trim(getRequest('product_id'))); break; 
      	   case 'show_gl_summary'  	: $this->showGLSummary(); break;   
      	   case 'show_sl1_summary'  	: $this->showSL1Summary(); break;   
      	   case 'show_sl2_summary'  	: $this->showSL2Summary(); break;   
      	   case 'show_sl3_summary'  	: $this->showSL3Summary(); break;   
      	   case 'show_sl4_summary'  	: $this->showSL4Summary(); break;
  
	   case 'loadsl2'  		: $this->getAjaxSL2HeadList(trim(getRequest('head_type'))); break;  
	   case 'loadsl3'  		: $this->getAjaxSL3HeadList(trim(getRequest('head_type'))); break;  
	   case 'loadsl4'  		: $this->getAjaxSL4HeadList(trim(getRequest('head_type'))); break;
	  
      	   case 'loadProduct'  		: $this->loadProduct4Catagory(trim(getRequest('head_type'))); break;   
	   case 'print_vouchar'		: $screen = $this->showPrintEditor($msg); break;
      	   default                   	: $cmd = 'list'; $screen = $this->showEditor();   break;

      	}

      } else if ($u_t_id == 105) {
            $this->showEditor(trim(getRequest('product_id')));
      }else {
      	header("location:index.php?app=user_home&msg=You are not authorised !!!");
      }     	          

      
      return true;
   } 
   function getSubAccHeadList()
   {

      $info            = array();
 	  $project_id 	   = getFromSession('project_id');
      $info['table']   = SUB_ACC_HEAD_TBL;

      $info['fields']  = array('sub_id', 'sub_head_name','head_details','head_type'); 	
      $info['where']   =  "project_id = '$project_id'"; 
	  $info['orderby'] = array("sub_head_name ASC");

      $info['debug']   = false;

      $result          = select($info);
      //dBug($result);

      $data            = array();

      if(count($result))
      {

         foreach($result as $i=>$v)
         {
            $data[$i] = $v;            

         }

      }    

      return $data;

   }
   function getAjaxSL2HeadList($head_type){
	$project_id = getFromSession('project_id'); 
	$rsql= "SELECT sub_htid,sub_head_type FROM ".SUB_HEAD_TYPE_TBL." WHERE head_type='$head_type' AND project_id='".$project_id."'";  
	$rres = mysql_query($rsql);
	$sl2option ="";
	$sl2option="<option value=''>Select SL-2</option>";
	while($v = mysql_fetch_object($rres)){		
	$sl2option.="<option value='".$v->sub_htid."'>".$v->sub_head_type."</option>";
	}
	echo $sl2option;
   }
   function getAjaxSL3HeadList($head_type){
	$subhead_type 	= getRequest('subhead_type');
	$project_id     = getFromSession('project_id'); 
	$rsql= "SELECT child_id,child_head_name FROM ".CHILD_HEAD_TYPE_TBL." WHERE head_type='$head_type' AND sub_head='".$subhead_type."' AND project_id='".$project_id."'";  //  
	$rres = mysql_query($rsql);
	$sl2option ="";
	$sl2option="<option value=''>Select SL-3</option>";
	while($v = mysql_fetch_object($rres)){		
	$sl2option.="<option value='".$v->child_id."'>".$v->child_head_name."</option>";
	}
	echo $sl2option;
   }
   function getAjaxSL4HeadList($head_type){
	$subhead_type 	= getRequest('subhead_type');
	$child_id  	= getRequest('child_id');
	$project_id     = getFromSession('project_id'); 
	$rsql= "SELECT sl_three_id,sl_three_name FROM ".SUBSIDIARY_STEP3_TBL." WHERE head_type='$head_type' AND sub_head='".$subhead_type."' AND child_id='".$child_id."' AND project_id='".$project_id."'";  
	$rres = mysql_query($rsql);
	$sl2option ="";
	$sl2option="<option value=''>Select SL-4</option>";
	while($v = mysql_fetch_object($rres)){		
	$sl2option.="<option value='".$v->sl_three_id."'>".$v->sl_three_name."</option>";
	}
	echo $sl2option;
   }
   function showPrintEditor($msg = null) {      	  
      
	  $account_head 	= getRequest('account_head');  
	  if ($product_id) {
          $advArr 					= $this->getProductInfo($product_id);
          $advArr 					= parseThisValue($advArr); 
		  $data   				= array_merge(array(), $advArr); 
		  $data['message'] = $msg;
		  $data['cmd']     = getRequest('cmd');
		  require_once(ACCOUNT_LEDGER_SKIN);      
		  return true;
	 }else{
		require_once(PRINT_VOUCHAR_SKIN);
	  }
   }
   function showEditor($msg = NULL) {      
	$data                	= array();	 	
	$data['cmd']         	= getRequest('cmd');
	require_once(CLASS_DIR.'/common.list.class.php');	
	$clistApp = new CommonList(); 
	$data['headlist1']   	= $clistApp->getAccountHeadList("Current Assets","S130"); // Cash & Bank
	$data['headlist2']   	= $clistApp->getAccountHeadList("Current Assets","S128","C000105"); // Party
	$data['headlist3']   	= $clistApp->getAccountHeadList("Non Current Assets","","","S130","C000105"); 
	$data['headlist4']   	= $clistApp->getAccountHeadList("Current Assets","","","S130","C000105");
	$data['NLiabilities']   = $clistApp->getAccountHeadList("Non Current Liabilities"); 
	$data['CLiabilities']   = $clistApp->getAccountHeadList("Current Liabilities");		
	$data['headlist6']   	= $clistApp->getAccountHeadList("Capital");	
	$data['headlist7']   	= $clistApp->getAccountHeadList("Retained earnings");	
	$data['headlist8']   	= $clistApp->getAccountHeadList("Operating Revenue");	
	$data['headlist9']   	= $clistApp->getAccountHeadList("Non-Operating Revenue");	
	$data['headlist10']   	= $clistApp->getAccountHeadList("Direct Expenses");		
	$data['headlist11']   	= $clistApp->getAccountHeadList("Indirect Expenses");	
	$data['headlist12']   	= $clistApp->getAccountHeadList("Opening Balance");	
	$data['headlist13']   	= $clistApp->getAccountHeadList("Adjustments Balance");	
	$data['headlist14']   	= $clistApp->getAccountHeadList("Closing Balance");
	$data['cogsheadlist']   = $clistApp->getAccountHeadList("Cost Center");		
	$data['supplier_list']  = $clistApp->getSupplierList();		     	
	$data['retailer_list'] 	= $clistApp->getRetailerList();
	if(getRequest('account_head')!=""){     
	   $ID 	= getRequest('account_head');
	}elseif(getRequest('sub_id')!=""){
	   $ID 	= getRequest('sub_id');
	}
	if($ID!="") {     
		$head_type = getHeadType($ID);
		if($head_type=="Cash" && getFromSession('u_type_id')==103) {	
			$msg="Access Denied !!!";
			header("location:?app=user_home&msg=$msg");	
		}
		if($head_type=="Retailer") {				
			 $advArr = $this->getRetailerInfo($ID);
			 $advArr = parseThisValue($advArr);  
			 $data   = array_merge(array(), $advArr);	
			 //$data['head_type'] = "Bank";
		}else{				 
			 $advArr = $this->getAccountsInfo($ID);
			 $advArr = parseThisValue($advArr);  
			 $data   = array_merge(array(), $advArr);   
		}   

		$data['date_from'] = formatDate(getRequest('date_from'));
		$data['date_to'] = formatDate(getRequest('date_to'));
		require_once(ACCOUNTS_LEDGER_SKIN_FILE); 
	}else{
		require_once(CURRENT_APP_SKIN_FILE); 
	}      
	return true;
   }


   function showInventoryDayBook($msg = NULL)
    {
        $data = array();
        $data['cmd'] = getRequest('cmd');
        require_once(CLASS_DIR . '/common.list.class.php');
        $clistApp = new CommonList();
        $data['headlist1'] = $clistApp->getAccountHeadList("Current Assets", "S127");
        $data['supplier_list'] = $clistApp->getSupplierList();
        $data['retailer_list'] = $clistApp->getRetailerList();
        if (getRequest('account_head') != "") {
            $ID = getRequest('account_head');
        } elseif (getRequest('sub_id') != "") {
            $ID = getRequest('sub_id');
        }
        if ($ID != "") {
            $head_type = getHeadType($ID);
            if ($head_type == "Cash" && getFromSession('u_type_id') == 103) {
                $msg = "Access Denied !!!";
                header("location:?app=user_home&msg=$msg");
            }
            if ($head_type == "Retailer") {
                $advArr = $this->getRetailerInfo($ID);
                $advArr = parseThisValue($advArr);
                $data = array_merge(array(), $advArr);
                //$data['head_type'] = "Bank";
            } else {
                $advArr = $this->getAccountsInfo($ID);
                $advArr = parseThisValue($advArr);
                $data = array_merge(array(), $advArr);
            }

            $data['date_from'] = formatDate(getRequest('date_from'));
            $data['date_to'] = formatDate(getRequest('date_to'));
            require_once(INVENTORY_LEDGER_SKIN_FILE);
        } else {
            require_once(INVENTORY_LEDGER_SEARCH_SKIN_FILE);
        }
        return true;
    }


   function showGLSummary(){
	$data                = array();	 	
	$data['cmd']         = getRequest('cmd'); 
	$data['head_list']   = $this->getSubAccHeadList();	
	if(getRequest('gl_id') !="") {  
		$data['date_from'] = formatDate(getRequest('date_from'));
		$data['date_to']   = formatDate(getRequest('date_to'));
		if(getFromSession('u_type_id')==103) {	
			$msg="Access Denied !!!";
			header("location:?app=user_home&msg=$msg");	
		}
		require_once(GROUP_LEDGER_SUMMARY_SKIN_FILE); 
	}else{
		require_once(GROUP_LEDGER_SUMMARY_SKIN_FILE);
	}
   }   
         
   function showSL1Summary(){
	$data                = array();	 	
	$data['cmd']         = getRequest('cmd'); 
	$data['head_list']   = $this->getSubAccHeadList();	
	if(getRequest('head_type') !="") {  
		$data['date_from'] = formatDate(getRequest('date_from'));
		$data['date_to']   = formatDate(getRequest('date_to'));
		if(getFromSession('u_type_id')==103) {	
			$msg="Access Denied !!!";
			header("location:?app=user_home&msg=$msg");	
		}
		require_once(SLSTEP1_LEDGER_SUMMARY_SKIN_FILE); 
	}else{
		require_once(SLSTEP1_LEDGER_SUMMARY_SKIN_FILE);
	}
   }   
   
   function showSL2Summary(){
	$data                = array();	 	
	$data['cmd']         = getRequest('cmd'); 
	$data['head_list']   = $this->getSubAccHeadList();	
	if(getRequest('head_type') !="" && getRequest('subhead_type') !="") {  
		$data['date_from'] = formatDate(getRequest('date_from'));
		$data['date_to']   = formatDate(getRequest('date_to'));
		if(getFromSession('u_type_id')==103) {	
			$msg="Access Denied !!!";
			header("location:?app=user_home&msg=$msg");	
		}
		require_once(SLSTEP2_LEDGER_SUMMARY_SKIN_FILE); 
	}else{
		require_once(SLSTEP2_LEDGER_SUMMARY_SKIN_FILE);
	}
   }   
   
   function showSL3Summary(){
	$data                = array();	 	
	$data['cmd']         = getRequest('cmd'); 
	$data['head_list']   = $this->getSubAccHeadList();	
	if(getRequest('head_type') !="" && getRequest('subhead_type') !="" && getRequest('child_id') !="") {  
		$data['date_from'] = formatDate(getRequest('date_from'));
		$data['date_to']   = formatDate(getRequest('date_to'));
		if(getFromSession('u_type_id')==103) {	
			$msg="Access Denied !!!";
			header("location:?app=user_home&msg=$msg");	
		}
		require_once(SLSTEP3_LEDGER_SUMMARY_SKIN_FILE); 
	}else{
		require_once(SLSTEP3_LEDGER_SUMMARY_SKIN_FILE);
	}
   }   
   
   function showSL4Summary(){
	$data                = array();	 	
	$data['cmd']         = getRequest('cmd'); 
	$data['head_list']   = $this->getSubAccHeadList();	
	if(getRequest('head_type') !="" && getRequest('subhead_type') !="" && getRequest('child_id') !="" && getRequest('sl_three_id') !="") {  
		$data['date_from'] = formatDate(getRequest('date_from'));
		$data['date_to']   = formatDate(getRequest('date_to'));
		if(getFromSession('u_type_id')==103) {	
			$msg="Access Denied !!!";
			header("location:?app=user_home&msg=$msg");	
		}
		require_once(SLSTEP4_LEDGER_SUMMARY_SKIN_FILE); 
	}else{
		require_once(SLSTEP4_LEDGER_SUMMARY_SKIN_FILE);
	}
   }
   function showSummaryEditor(){
	require_once(CLASS_DIR.'/common.list.class.php');	
	$comListApp = new CommonList();       
	$data                	= array();	 	
	$data['cmd']         	= getRequest('cmd'); 
	$data['head_list']   	= $this->getSubAccHeadList();
	//$data['project_list'] 	= $comListApp->getProjectList();
	$user_type 		= getFromSession('u_type_id');	
	if(getRequest('submit')) {  
		$data['date_from'] = formatDate(getRequest('date_from'));
		$data['date_to']   = formatDate(getRequest('date_to'));		 
		require_once(ACCOUNTS_LEDGER_SUMMARY_SKIN_FILE); 
	}else{		
		require_once(ACCOUNTS_SUMMARY_SKIN_FILE); 
	}	     
	  
	return $data[0];     
        //return true;
   }
   function showAssetsSummary(){
	$data                = array();	 	
	$data['cmd']         = getRequest('cmd'); 
	$data['head_list']   = $this->getSubAccHeadList();	
	if(getRequest('head_type') !="") {  
		$data['date_from'] = formatDate(getRequest('date_from'));
		$data['date_to'] = formatDate(getRequest('date_to'));
		if(getFromSession('u_type_id')==103) {	
			$msg="Access Denied !!!";
			header("location:?app=user_home&msg=$msg");	
		}
		require_once(ASSETS_LEDGER_SUMMARY_SKIN_FILE); 
	}else{
		$msg="Access Denied !!!";
		header("location:?app=user_home&msg=$msg");
	}
   }
   function showEquitySummary(){
	$data                = array();	 	
	$data['cmd']         = getRequest('cmd'); 
	$data['head_list']   = $this->getSubAccHeadList();	
	if(getRequest('head_type') !="") {  
		$data['date_from'] = formatDate(getRequest('date_from'));
		$data['date_to'] = formatDate(getRequest('date_to'));		
		require_once(EQUITY_LEDGER_SUMMARY_SKIN_FILE); 
	}else{
		$msg="Access Denied !!!";
		header("location:?app=user_home&msg=$msg");
	}
   }
   function showEquityLiabilitySummary(){
	$data                = array();	 	
	$data['cmd']         = getRequest('cmd'); 
	$data['head_list']   = $this->getSubAccHeadList();	
	if(getRequest('head_type') !="") {  
		$data['date_from'] = formatDate(getRequest('date_from'));
		$data['date_to'] = formatDate(getRequest('date_to'));		
		require_once(EQUITY_LIABILITY_LEDGER_SUMMARY_SKIN); 
	}else{
		$msg="Access Denied !!!";
		header("location:?app=user_home&msg=$msg");
	}
   }
   function getAccountsInfo($id)
   {
       $data           =  array();                  
       $info           =  array();     
       $info['table']  =  SUB_ACC_HEAD_TBL;
       $info['where']  =  "sub_id='".$id."' ";
       $info['debug']  =  false;                     
       $res            =	select($info);
       if(count($res))
       {
          foreach($res as $i=>$v)
          {
             $data[$i] = $v;             
          }
       }
       //dumpVar($data);
       return $data[0];
   }
   
   function getRetailerInfo($id)
   {
   	   $data           =  array();                  
       $info           =  array();     
       $info['table']  =  RETAILER_TBL;
	   $info['fields']  = array('retailer_id as sub_id', 'retailer_name as sub_head_name','address as head_details','mobile'); 	
       $info['where']  =  "retailer_id='".$id."' ";
       $info['debug']  =  false;                     
       $res            =	select($info);
       if(count($res))
       {
          foreach($res as $i=>$v)
          {
             $data[$i] = $v;             
          }
       }
       //dumpVar($data);
       return $data[0];
   }
   	
   function getBankInfo($id)
   {
   	   $data           =  array();                  
       $info           =  array();     
       $info['table']  =  BANK_TBL.' b,'.BANK_ACCOUNT_TBL.' ba';
       $info['fields']  = array('b.bank_name', 'b.address','ba.bank_account_no','ba.account_name','ba.account_type','ba.branch_location','ba.phone','ba.fax'); 	
       $info['where']  =  "b.bank_id=ba.bank_code AND ba.bank_account_no= '".$id."' ";
       $info['debug']  =  false;                     
       $res            =	select($info);
       if(count($res))
       {
          foreach($res as $i=>$v)
          {
             $data[$i] = $v;             
          }
       }
       //dumpVar($data);
       return $data[0];
   }

	
   function getSuppliersInfo($id)
   {
   	   $data           =  array();                  
       $info           =  array();     
       $info['table']  =  SUPPLIER_TBL;
       $info['where']  =  "supplier_code='".$id."' ";
       $info['debug']  =  false;                     
       $res            =	select($info);
       if(count($res))
       {
          foreach($res as $i=>$v)
          {
             $data[$i] = $v;             
          }
       }
       //dumpVar($data);
       return $data[0];
   }
 
//==================== End Sales Details =====================
   
} // End class

?>
