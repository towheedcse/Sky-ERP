<?php
class SalarySheet
{
   function run() {     
		$cmd    = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
		if($u_t_id == 101||$u_t_id ==102||$u_t_id ==104||$u_t_id ==107)  // 107 = hr
		{      
		  switch($cmd) { 
		  	 case 'add'                	: $screen = $this->showEditor($msg); break;
      	     case 'edit'               	: $screen = $this->showEditEditor("Edit Page");    break;			 
      	   	 case 'doUpdate'           	: $screen = $this->showEditEditor($msg); break;			 
      	   	 case 'sapp'           		: $screen = $this->showEditor($msg); break;		 
      	   	 case 'dtl.list'           	: $screen = $this->showDetailList($msg); break;			 
			 default                   	: $cmd = 'list'; $screen = $this->showList($msg);   break;
		  }
		}else {
      		header("location:index.php?app=user_home&msg=You are not authorised !!!");
      	} 
		
		return true;
   }
  function showEditor()
  {
	 require_once(CLASS_DIR.'/common.class.php');	
	 $comApp = new Common(); 
	 require_once(CLASS_DIR.'/common.list.class.php');	
	 $comListApp 	= new CommonList();  
	 $cmd    		= getRequest('cmd');
	 $salary_month 	= getRequest('salary_month');
	 $salary_year 	= getRequest('salary_year');	 	 
	 $data               = array();		
	 if($salary_month!="" && $salary_year!=""){
		$Fsql = "SELECT * FROM ".SALARY_MASTER_TBL." WHERE salary_month='$salary_month' AND salary_year='$salary_year' AND project_id = '$project_id'";
		$num  = mysql_num_rows(mysql_query($Fsql));
		if($num==0){
		 	if(getRequest('save')){				
				$this->generateSalary($salary_month,$salary_year);				
				$msg="Salary Sheet Successfully Created!!!";
				header("location:index.php?app=salary.sheet&cmd=list&msg=$msg"); 	      	
			 }
		}
	}
	$salary_id = getRequest('salary_id');
	if($cmd=="sapp" && $salary_id!=""){	 
	 $this->approvedSalary($salary_id);
	 $msg="Approved Salary Sheet Successfully!!!";
	 header("location:index.php?app=salary.sheet&cmd=list&msg=$msg"); 
	}  
	$data['project_id'] 	= getFromSession('project_id');
	$data['message'] 		= $msg;
	$data['cmd']     		= getRequest('cmd'); 
	require_once(CURRENT_APP_SKIN_FILE);
	return $data[0];
   } 
   function showEditEditor()
  {
	 require_once(CLASS_DIR.'/common.class.php');	
	 $comApp = new Common(); 
	 require_once(CLASS_DIR.'/common.list.class.php');	
	 $comListApp 	= new CommonList(); 	 
	 
	 $cmd    		= getRequest('cmd');
	 $detail_id 	= getRequest('id'); 
	 $salary_id	    = getRequest('salary_id'); 
	 $data               = array();		
	 if(getRequest('id')!=""){
		 $TBDArr			= $comApp->getRecordInfo(SALARY_DETAILS_TBL,"detail_id",$detail_id);      
		 $TBDArr 			= parseThisValue($TBDArr);
		 $data        		= array_merge(array(),$TBDArr);				
		 if(getRequest('save')){
			$comApp->updateRecord(SALARY_DETAILS_TBL,"detail_id",$detail_id,"","","","","salary.sheet","dtl.list&salary_id=$salary_id");
		 }		 
		
	} 
	$data['project_id'] 	= getFromSession('project_id');
	$data['message'] 		= $msg;
	$data['cmd']     		= getRequest('cmd'); 
	require_once(SALARY_EDIT_SKIN);
	return $data[0];
   }
   function generateSalary($salary_month,$salary_year){
   		
		$project_id = getFromSession('project_id');
		$Fsql = "SELECT * FROM ".SALARY_MASTER_TBL." WHERE salary_month='$salary_month' AND salary_year='$salary_year' AND project_id = '$project_id'";
		$num  = mysql_num_rows(mysql_query($Fsql));
		if($num==0){			
			mysql_query("START TRANSACTION;");	
			$salary_id = $this->createVoucharID();
			$created_by 		= getFromSession('userid');
			$created_date 		= date('Y-m-d h:i:s');	
			$SQLM="INSERT INTO ".SALARY_MASTER_TBL."(salary_id,project_id,salary_month,salary_year,created_by,created_date) 
			VALUES('$salary_id','$project_id','$salary_month','$salary_year','$created_by','$created_date')";
			$resm = mysql_query($SQLM);
			if($resm){
				$SQLE="SELECT s.`sub_id`,e.`employee_id`,e.name, e.`f_name`, e.`joining_date`, e.`department`, e.`designation`, e.`gross_salary` FROM `sub_acc_head` AS s, `employee` AS e
				WHERE e.`employee_id` = s.`employee_id` AND e.`resign_date` IS NULL";
				$ResE = mysql_query($SQLE);
				while($row = mysql_fetch_object($ResE)){
				$employee_accid 	= $row->sub_id;
				$employee_id 		= $row->employee_id;
				$employee_name 		= $row->name;
				$f_name 			= $row->f_name;
				$designation 		= $row->designation;
				$department 		= $row->department;
				$gross_salary 		= $row->gross_salary;
				$net_payble			= $row->gross_salary;
				$total_due			= $net_payble;		
				
				$SQLd="INSERT INTO ".SALARY_DETAILS_TBL."(salary_id,project_id,employee_id,employee_accid,employee_name,designation,department,salary_month,salary_year,gross_salary,
				net_payble,total_due) VALUES('$salary_id','$project_id','$employee_id','$employee_accid','$employee_name','$designation','$department','$salary_month',
				'$salary_year','$gross_salary','$net_payble','$total_due')";
				mysql_query($SQLd);
				}
			}
			mysql_query("COMMIT;");	
			header("location:index.php?app=salary.sheet&cmd=list&msg=Salary Sheet Successfully Created!!!");
		}else{
		header("location:index.php?app=salary.sheet&cmd=list");
		}		
   } 
   function createVoucharID()
   {
      $info = array();
      $info['table'] = DEVIT_VOUCHAR_TBL;
      $info['fields'] = array('max(voucher_no) as maxvoucher');      
      $res = select($info);      
      $maxvoucherId = 'D0000000';      
      if(count($res))
      {
         foreach($res as $v)
         {
         	 if($v->maxvoucher)
         	 {
             $maxvoucherId = $v->maxvoucher;
             }
             break;   	
         }      
      }      
      $maxvoucherId = generateID("D",$maxvoucherId,8);
      return $maxvoucherId;
  }
   function approvedSalary($salary_id){
   	    require_once(CLASS_DIR.'/sales.class.php');	
	    $slsApp = new Sales();
	    $project_id = getFromSession('project_id');
		mysql_query("START TRANSACTION;"); 
		if($salary_id!=""){
			$TotalSalary = 0; $TotalPaidSalary = 0; $dueSalary =0;
			$sql = "SELECT * FROM ".SALARY_DETAILS_TBL." WHERE salary_id='$salary_id' AND project_id = '$project_id' AND status='Pending'";
			$res = mysql_query($sql);
			while($srow = mysql_fetch_object($res)){
			$total_paid = 0;
			$detail_id 		= $srow->detail_id;
			$PartyAcc_head 	= $srow->employee_accid;
			$CrAmount 		= $srow->net_payble;
			$details 		= "Salary of ".$srow->salary_month." ".$srow->salary_year;
			$TotalSalary+=$CrAmount;
			$created_date  = date('Y-m-d h:i:s');		
			$totalPartyCR  = $slsApp->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
			$totalPartyDR  = $slsApp->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));					 
			$PartyBalance  = ($totalPartyDR-($totalPartyCR+$CrAmount));	
			//======= Party Cr ======					 
			$slsApp->saveAccountJournal($salary_id,$PartyAcc_head,"Acc",getFromSession('project_id'),$details,0,$CrAmount,$PartyBalance,1,$created_date);
			//===== Salary will be Dr (Pending) =======
			
			$restOfAmount = $this->adjustSalaryFromAdvanced($CrAmount,$project_id,$PartyAcc_head,$created_date);
			$dueSalary+=$restOfAmount;
			$total_paid = ($CrAmount-$restOfAmount);
			$TotalPaidSalary+=$total_paid;
			$sdsql = "UPDATE ".SALARY_DETAILS_TBL." SET total_paid='$total_paid',total_due='$restOfAmount', status='Approved' WHERE detail_id='$detail_id'";
			mysql_query($sdsql);			
			}
			$approved_date  = date('Y-m-d h:i:s');
			$approved_by 	= getFromSession('userid');	
			$smusql="UPDATE ".SALARY_MASTER_TBL." SET total_salary='$TotalSalary',total_paid='$TotalPaidSalary',total_due='$dueSalary',approved_by='$approved_by',
			 approved_date='$approved_date',status='Approved' WHERE salary_id='$salary_id'";
			$smres = mysql_query($smusql);
			if($smres){
			mysql_query("COMMIT;");	
			}else{mysql_query("ROLLBACK;");	}			
		}		
		
  }
  function adjustSalaryFromAdvanced($DrAmount,$project_id,$cr_account,$approved_date){
	if($DrAmount>0){
	$SRPSql="SELECT return_id,voucher_no,supplier,return_amount,paid_amount,due FROM ".PURCHASE_RETURN_RECEIBAVLE_TBL." WHERE supplier ='".$cr_account."' 
	 AND project_id = '$project_id' AND paid_amount < return_amount AND due >0 AND payment_source='Advanced Payment Against Salary' 
	 ORDER BY return_id ASC"; // AND fyear='$fyear'
	$SRPRes = mysql_query($SRPSql);
	if(mysql_num_rows($SRPRes)>0){
		while($srprow = mysql_fetch_object($SRPRes)){
			$return_id 		= $srprow->return_id;
			$voucher_no 	= $srprow->voucher_no;
			$net_payble 	= $srprow->return_amount;
			$paid_amount 	= $srprow->paid_amount;
			$existing_due 	= $srprow->due;
			if(($DrAmount>=$existing_due)){
				$DrAmount 		= $DrAmount - $existing_due;
				if($existing_due>0){						
				$total_paid = ($paid_amount + $existing_due); 
				$SRUpSql = "UPDATE ".PURCHASE_RETURN_RECEIBAVLE_TBL." SET paid_amount=$total_paid, due=0  WHERE return_id ='$return_id' AND project_id = '$project_id'";
				$upres = mysql_query($SRUpSql);
				if($upres){
					$details = "Adjust from Advanced Salary";
					$this->CapitalCr($voucher_no,$existing_due,$details,$approved_date);
				}
				} 
			}elseif(($DrAmount<$existing_due)){					
				if($existing_due>0 && $DrAmount>0){
				$totalpaid 	 = ($paid_amount + $DrAmount); 
				$present_due = ($existing_due - $DrAmount);
				$DrAmount 	 =  0;
				$SRPUpdate="UPDATE ".PURCHASE_RETURN_RECEIBAVLE_TBL." SET paid_amount=$totalpaid,due=$present_due WHERE return_id='$return_id' 
				AND project_id= '$project_id'";
				$upres = mysql_query($SRPUpdate);
				if($upres){
					$details = "Adjust from Advanced Salary";
					$this->CapitalCr($voucher_no,$DrAmount,$details,$approved_date);
				}
				}
				break;
			}
			
		} // end while
	}// end num_rows
	}// end $DrAmount>0
	return $DrAmount;
  }
  function CapitalCr($voucher_no,$CrAmount,$details,$created_date){
  	require_once(CLASS_DIR.'/sales.class.php');	
	$slsApp = new Sales();
	//======= Cr Capital ======
  	$capital_head 	 = $slsApp->getCapitalId(getFromSession('project_id'));
	$totalCapitalCR  = $slsApp->getTotalCreditAmount($capital_head,getFromSession('project_id'));
	$totalCapitalDR  = $slsApp->getTotalDebitAmount($capital_head,getFromSession('project_id'));					 
	$Capitalbalance  = ($totalCapitalDR-($totalCapitalCR+$CrAmount));	
	$slsApp->saveAccountJournal($voucher_no,$capital_head,"Capital",getFromSession('project_id'),$details,0,$CrAmount,$Capitalbalance,1,$created_date);				 
  }
  function showList()
  {
	 $data                				= array();
	 $data['cmd']         				= getRequest('cmd');
	 $data['record_list'] 				= $this->getSalaryList(getRequest('from'),getRequest('to'));
	 //$data['totalrecord']				= $this->getTotalSalesDetailsList(getRequest('from'),getRequest('to'));	 
	 require_once(SALARY_SHEET_LIST_SKIN);
	 return $data[0];
 } 
 function showDetailList(){
 	$data                				= array();
	 $data['cmd']         				= getRequest('cmd');
	 $data['record_list'] 				= $this->getDetailSalaryList(getRequest('from'),getRequest('to'));
	 //$data['totalrecord']				= $this->getTotalSalesDetailsList(getRequest('from'),getRequest('to'));	 
	 require_once(SALARY_SHEET_DTL_LIST_SKIN);
	 return $data[0];
 }  
 function getSalaryList($from,$to){ 
		if($from == "" && $to == ""){$from=0; $to=1000;}
		$date_from 		= formatDate(getRequest('date_from'));
		$date_to 		= formatDate(getRequest('date_to'));				
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = SALARY_MASTER_TBL.' sm,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('sm.salary_id','p.project_name','p.location','sm.salary_month','sm.salary_year','c.curr_symble','sm.total_salary','sm.total_paid','sm.total_due',"DATE_FORMAT(sm.created_date,'%d %b %y' ) as created_date",'sm.status','sm.created_by',"DATE_FORMAT(sm.approved_date,'%d %b %y' ) as approved_date","sm.approved_by","sm.status");
		
		$sql="sm.project_id = p.project_id AND sm.currency = c.currency_id AND sm.project_id = '".$project_id."' ";
							
		if($date_from!="" && $date_to ==""){
			$sql.=" AND sm.created_date >= '$date_from'";
		}elseif($date_from=="" && $date_to !=""){
			$sql.=" AND sm.created_date <= '$date_to'";
		}elseif($date_from!="" && $date_to !=""){
			$sql.=" AND sm.created_date BETWEEN '$date_from' AND '$date_to'";
		}
		$info['where']  =$sql;		
		$info['orderby'] = array("sm.salary_id asc LIMIT $from,$to");
		//$info['debug']  = true;
		$result         = select($info);
		$data           = array();
		$cnt = count($result);  	     
		if($cnt) {
			foreach($result as $value)  {				
			$data[]	= $value;	
			}
		} 
		return $data; 
  }
    
 function getDetailSalaryList($from,$to){ 
		if($from == "" && $to == ""){$from=0; $to=1000;}
		$salary_id 		= getRequest('salary_id');				
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = SALARY_MASTER_TBL.' sm,'.SALARY_DETAILS_TBL.' sd,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('sm.salary_id','p.project_name','p.location','sd.detail_id','sd.employee_id','sd.employee_accid','sd.employee_name','sd.designation','sd.department','sd.salary_month','sd.salary_year','c.curr_symble','sd.gross_salary','sd.net_payble','sd.total_paid','sd.total_due',"DATE_FORMAT(sm.created_date,'%d %b %y' ) as created_date",'sm.created_by',"DATE_FORMAT(sm.approved_date,'%d %b %y' ) as approved_date","sm.approved_by","sm.status");		
						
				
		$sql="sm.salary_id=sd.salary_id AND sm.project_id = p.project_id AND sm.currency = c.currency_id AND sm.project_id = '".$project_id."' AND sd.salary_id='$salary_id'";
							
		
		$info['where']  =$sql;		
		$info['orderby'] = array("sd.employee_name ASC LIMIT $from,$to");
		//$info['debug']  = true;
		$result         = select($info);
		$data           = array();
		$cnt = count($result);  	     
		if($cnt) {
			foreach($result as $value)  {				
			$data[]	= $value;	
			}
		} 
		return $data; 
  }
  function getApprovedSalaryList($from,$to){ 
		if($from == "" && $to == ""){$from=0; $to=8000;}		
		$project_id     = getFromSession('project_id');  
		$info           = array();    
		$info['table']  = SALARY_MASTER_TBL.' sm,'.SALARY_DETAILS_TBL.' sd,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
		$info['fields'] = array('sm.salary_id as voucher_no','p.project_name','p.location','sd.detail_id','sd.employee_id','sd.employee_accid','sd.employee_name','sd.designation','sd.department','sd.salary_month','sd.salary_year','c.curr_symble','sd.gross_salary','sd.net_payble','sd.total_paid','sd.total_due',"DATE_FORMAT(sm.created_date,'%d %b %y' ) as created_date",'sm.created_by',"DATE_FORMAT(sm.approved_date,'%d %b %y' ) as approved_date","sm.approved_by","sm.status");		
						
				
		$sql="sm.salary_id=sd.salary_id AND sm.project_id = p.project_id AND sm.currency = c.currency_id AND 
		sm.project_id = '".$project_id."' AND sd.status='Approved' AND sd.total_due>0";
							
		
		$info['where']  =$sql;		
		$info['orderby'] = array("sd.employee_name ASC LIMIT $from,$to");
		//$info['debug']  = true;
		$result         = select($info);
		$data           = array();
		$cnt = count($result);  	     
		if($cnt) {
			foreach($result as $value)  {				
			$data[]	= $value;	
			}
		} 
		return $data; 
  }
  function updateSalaryTbl($voucher_no,$esref_no,$detail_id,$CrAmount){
	$psql= "SELECT total_salary,total_paid as paid_amount,total_due as due FROM ".SALARY_MASTER_TBL." WHERE 
	salary_id  = '".$esref_no."' AND total_due >0 AND status='Approved'";
	$pres = mysql_query($psql);
	if(mysql_num_rows($pres)>0){
		$srow = mysql_fetch_object($pres);						
		$due = $srow->due;
		$total_salary = $srow->total_salary;
		$paidAmount = $srow->paid_amount;
		$total_due = $total_salary - ($paidAmount + $CrAmount);
		$total_paid = ($paidAmount + $CrAmount);
		$pusql= "UPDATE ".SALARY_MASTER_TBL." SET total_paid ='".$total_paid."',total_due ='".$total_due."' WHERE salary_id = '".$esref_no."' AND total_due >0 AND status='Approved'";
		$pures = mysql_query($pusql);
	}
	$sdsql= "SELECT net_payble,total_paid as paid_amount,total_due as due FROM ".SALARY_DETAILS_TBL." WHERE salary_id  = '".$esref_no."' AND
	 detail_id='$detail_id' AND total_due >0 AND status='Approved'";
	$sdres = mysql_query($sdsql);
	if(mysql_num_rows($sdres)>0){
		$sdrow = mysql_fetch_object($sdres);						
		$due = $sdrow->due;
		$net_payble = $sdrow->net_payble;
		$paidAmount = $sdrow->paid_amount;
		$total_due = $net_payble - ($paidAmount + $CrAmount);
		$total_paid = ($paidAmount + $CrAmount);
		$sdusql= "UPDATE ".SALARY_DETAILS_TBL." SET total_paid ='".$total_paid."',total_due ='".$total_due."' WHERE detail_id='$detail_id' AND total_due >0 AND status='Approved'";
		mysql_query($sdusql);	
	}	
  }  
} // End class
?>