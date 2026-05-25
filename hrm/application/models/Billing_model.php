<?php 
class Billing_model extends CI_Model {
		
	function __construct()
	{
		parent::__construct();
	}	
	
	function InsertBillRecord(){
		$details_id		= $this->input->post('details-id');
		if(empty($details_id)){
		$details_id		= 0;
		}		    
		$bill_id		= $this->input->post('bill-id');
		if(empty($bill_id)){
			$bill_id	= 0;
			$status		= 1;
		}else{
			$this->db->select('status');
			$this->db->from(BILL_MASTER_TBL);
			$this->db->where('bill_id', $bill_id);
			$query 		= $this->db->get();
			$status		= $query->row()->status;
		}
		
		$bill_date			=$this->formatDate($this->input->post('bill-date'));
		$billing_month 		=$this->input->post('bill-period');
		$admission_id		=$this->input->post('admission_id'); 
		$institute_id		=$this->input->post('institute_id');
		$branch_id			=$this->input->post('branch_id');
		$session_id			=$this->input->post('session_id');
		$version_id			=$this->input->post('version_id');
		$class_id			=$this->input->post('class_id');
		$group_id			=$this->input->post('group_id');
		$section_id			=$this->input->post('section_id');
		$shift_id			=$this->input->post('shift_id');
		$student_id			=$this->input->post('student_id');
		$particulars_id		=$this->input->post('particulars-id');
		/*
		$discount_type		=$this->input->post('discount_type');
		$discount_percentage=$this->input->post('discount_percentage');
		$discount_amount	=$this->input->post('discount_amount');
		$total_bill			=$this->input->post('total_bill');
		*/
		$quantity			= $this->input->post('quantity');
		$unit_price			= $this->input->post('unit_price');
		$total_price		= ($quantity * $unit_price);
		$total_price 		= round($total_price,0,PHP_ROUND_HALF_UP);
		$remarks			= $this->input->post('remarks');			
    		$created_by			= $this->session->userdata('created_by');
		
		$this->db->select('student_name_en as student_id');
		$this->db->from(ADMISSION_TBL);
		$this->db->where('admission_id', $admission_id);
		$this->db->where('institute_id', $institute_id);
		$this->db->where('branch_id', $branch_id);
		$aquery 		= $this->db->get();
		$student_id		= $aquery->row()->student_id;
					
		$ddata = array(
		'bill_id'  			=>$bill_id,
		'billing_month' 	=>$billing_month,
		'billing_date'		=>$bill_date,
		'fee_account'		=>$particulars_id,
		'account_id'   		=>$student_id,
		'admission_id'		=>$admission_id,
		'institute_id'    	=>$institute_id,
		'branch_id'    		=>$branch_id,
		'session_id'    	=>$session_id,
		'version_id'    	=>$version_id,
		'class_id'    		=>$class_id,
		'group_id'    		=>$group_id,
		'section_id'    	=>$section_id,
		'quantity'    		=>$quantity,
		'unit_price'    	=>$unit_price,
		'total_price'  		=>$total_price,
		'remarks'  			=>$remarks,
		'status'  			=>$status,
		'created_by'  		=>$created_by
	    );
	   	   
		if($admission_id >0 && $bill_date!="" && $quantity >0){
			if($details_id ==0){ 
			 $this->db->insert(BILL_DETAILS_TBL, $ddata);
			}else{
			$this->EditBillRecord($details_id);
			}
			//print  $this->db->last_query();
		}		
	}
    function EditBillRecord($details_id){		    
		$bill_id		= $this->input->post('bill-id');
		if(empty($bill_id)){
			$bill_id	= 0;
			$status		= 1;
		}else{
			$this->db->select('status');
			$this->db->from(BILL_MASTER_TBL);
			$this->db->where('bill_id', $bill_id);
			$query 		= $this->db->get();
			$status		= $query->row()->status;
		}
		
		$bill_date			=$this->formatDate($this->input->post('bill-date'));
		$billing_month 		=$this->input->post('bill-period');
		$admission_id		=$this->input->post('admission_id'); 
		$institute_id		=$this->input->post('institute_id');
		$branch_id			=$this->input->post('branch_id');
		$session_id			=$this->input->post('session_id');
		$version_id			=$this->input->post('version_id');
		$class_id			=$this->input->post('class_id');
		$group_id			=$this->input->post('group_id');
		$section_id			=$this->input->post('section_id');
		$shift_id			=$this->input->post('shift_id');
		$student_id			=$this->input->post('student_id');
		$particulars_id		=$this->input->post('particulars-id');
		/*
		$discount_type		=$this->input->post('discount_type');
		$discount_percentage=$this->input->post('discount_percentage');
		$discount_amount	=$this->input->post('discount_amount');
		$total_bill			=$this->input->post('total_bill');
		*/
		$quantity			= $this->input->post('quantity');
		$unit_price			= $this->input->post('unit_price');
		$total_price		= ($quantity * $unit_price);
		$total_price 		= round($total_price,0,PHP_ROUND_HALF_UP);
		$remarks			= $this->input->post('remarks');			
    		$modified_by		= $this->session->userdata('created_by');
		$modified_time  	= date("Y-m-d H:i:s");		
		
		$this->db->select('student_name_en as student_id');
		$this->db->from(ADMISSION_TBL);
		$this->db->where('admission_id', $admission_id);
		$this->db->where('institute_id', $institute_id);
		$this->db->where('branch_id', $branch_id);
		$aquery 		= $this->db->get();
		$student_id		= $aquery->row()->student_id;
		
		$ddata = array(
		'bill_id'  			=>$bill_id,
		'billing_month' 	=>$billing_month,
		'billing_date'		=>$bill_date,
		'fee_account'		=>$particulars_id,
		'account_id'   		=>$student_id,
		'admission_id'		=>$admission_id,
		'institute_id'    	=>$institute_id,
		'branch_id'    		=>$branch_id,
		'session_id'    	=>$session_id,
		'version_id'    	=>$version_id,
		'class_id'    		=>$class_id,
		'group_id'    		=>$group_id,
		'section_id'    	=>$section_id,
		'quantity'    		=>$quantity,
		'unit_price'    	=>$unit_price,
		'total_price'  		=>$total_price,
		'remarks'  			=>$remarks,
		'status'  			=>$status,
		'modified_by'  		=>$modified_by,
		'modified_time'		=>$modified_time
	    );
	    $this->db->where('bill_id',$bill_id);
		$this->db->where('admission_id',$admission_id);
		$this->db->where('details_id',$details_id);
		$this->db->update(BILL_DETAILS_TBL, $ddata);
       //print  $this->db->last_query();
	}
		
	function saveBillMaster($bill_id){
		$bill_date			=$this->formatDate($this->input->post('bill-date'));
		$billing_month 			=$this->input->post('bill-period');
		$admission_id			=$this->input->post('admission_id'); 
		$institute_id			=$this->input->post('institute_id');
		$branch_id			=$this->input->post('branch_id');
		$session_id			=$this->input->post('session_id');
		$version_id			=$this->input->post('version_id');
		$class_id			=$this->input->post('class_id');
		$group_id			=$this->input->post('group_id');
		$section_id			=$this->input->post('section_id');
		$shift_id			=$this->input->post('shift_id');
		$credit_period			=$this->input->post('credit_period');
		
		$this->db->select('student_name_en as student_id');
		$this->db->from(ADMISSION_TBL);
		$this->db->where('admission_id', $admission_id);
		$this->db->where('institute_id', $institute_id);
		$this->db->where('branch_id', $branch_id);
		$aquery 		= $this->db->get();
		$student_id		= $aquery->row()->student_id;		
		$account_id		= $aquery->row()->student_id;
		
		$discount_type		= $this->input->post('discount_type');
		$discount_percentage	= $this->input->post('discount_percentage');
		$less_tuitionfee	= $this->input->post('less_tuitionfee');
		$discount_amount	= $this->input->post('discount_amount');
		$total_bill		= $this->input->post('total_bill');
		
		$invoice_note1		= $this->input->post('invoice_note1');
		$invoice_note2		= $this->input->post('invoice_note2');	
		$invoice_note1		= "Please issue A/C payee cheque/Pay Order/DD/BEFTN in favor of ".$this->session->userdata('company_name');
		$invoice_note2		= "Payment should be clear with in $credit_period days after receiving this invoice.";	
		if(empty($discount_percentage)){$discount_percentage=0;} if(empty($discount_amount)){$discount_amount=0;}
		if(empty($less_tuitionfee)){$less_tuitionfee=0;} if($discount_type==1){$discount_percentage=0; $less_tuitionfee=0;}
		$created_by		= $this->session->userdata('created_by');
					
		if($total_bill >0){
		   $bill_amount = $total_bill; 
		   $FeePeriod = (($billing_month * 2) - 1); 
		   //====== Start Calculate Discount ======
		   $concession_on =0;	
		   if($discount_type ==2 && $discount_percentage >0 && $discount_amount==0){
		     //===== Get Consession On ======
			 $concession_hreads = $this->session->$serdata('concession_hreads');
			 $cfsql = "SELECT SUM(fee_amount) as concession_on FROM ".COURSE_FEE_TBL." WHERE institute_id = $institute_id AND branch_id = $branch_id AND sessions_id = $session_id AND version_id=$version_id 
			 AND class_id=$class_id AND group_id=$group_id AND SUBSTRING(`fee_month`,$FeePeriod,1) > 0 AND `account_id` IN($concession_hreads)";
		     $caquery = $this->db->query($cfsql);				
			 if($caquery->num_rows() >0){
			   $concession_on 	= $caquery->row()->concession_on; 
			   if($discount_percentage >0){
			   $discount_amount = (($concession_on/100) * $discount_percentage);			   
			   $discount_amount = round($discount_amount,0,PHP_ROUND_HALF_UP);
			   $net_bill_amount = ($bill_amount - $discount_amount);
		       }			   	
			 }
		   }elseif($discount_type ==1 && $discount_amount >0){			   			   
			 $discount_amount = round($discount_amount,0,PHP_ROUND_HALF_UP);
			 $net_bill_amount = ($bill_amount - $discount_amount);   
		   }elseif($discount_type ==2 && $discount_amount >0){			   			   
			 $discount_amount = round($discount_amount,0,PHP_ROUND_HALF_UP);
			 $net_bill_amount = ($bill_amount - $discount_amount);   
		   }else{
			 $net_bill_amount = $bill_amount;  
		   }
		   if($discount_percentage >0 || $discount_amount >0){
		   $AMSQL= "UPDATE ".ADMISSION_TBL." SET discount_type=".$discount_type.",discount_percentage=".$discount_percentage.",discount_amount=".$discount_amount.",less_tuitionfee=".$less_tuitionfee.",concession_on=".$concession_on.",net_amount=".$net_bill_amount." WHERE institute_id=".$institute_id." AND branch_id =".$branch_id." AND admission_id = ".$admission_id;
		   $this->db->query($AMSQL);
		   }
		   //===== End Calculate Discount 
		   
		   $due_amount	= $net_bill_amount;
		   $description	= "Total bill of amount against monthly tution & fee";
		   	   
		   if($bill_id >0){
				$this->RollbackCollection($bill_id);
				$BSSQL= "SELECT * FROM ".BILL_MASTER_TBL." WHERE account_id = ".$account_id." AND admission_id=$admission_id AND institute_id=$institute_id AND branch_id=$branch_id AND billing_month=$billing_month AND bill_id=$bill_id";
			    $bquery = $this->db->query($BSSQL);
			    $bill_no = $bquery->row()->bill_no; $modified_time = date("Y-m-d H:i:s");
				$mdata = array(
					'bill_no'  		=>$bill_no,
					'billing_month' 	=>$billing_month,
					'billing_date'		=>$bill_date,
					'credit_period'		=>$credit_period,
					'account_id'   		=>$account_id,
					'admission_id'		=>$admission_id,
					'institute_id'    	=>$institute_id,
					'branch_id'    		=>$branch_id,
					'session_id'    	=>$session_id,
					'version_id'    	=>$version_id,
					'group_id'    		=>$group_id,
					'class_id'    		=>$class_id,
					'shift_id'		=>$shift_id,
					'section_id'    	=>$section_id,
					'bill_amount'    	=>$bill_amount,
					'discount_persent'      =>$discount_percentage,
					'discount_amount'       =>$discount_amount,
					'net_bill_amount'  	=>$net_bill_amount,
					'due_amount'  		=>$due_amount,
					'description'  		=>$description,
					'invoice_note1'  	=>$invoice_note1,
					'invoice_note2'  	=>$invoice_note2,
					'modified_by'  		=>$created_by,
					'modified_time'  	=>$modified_time
				);
				$this->db->where('bill_id',$bill_id);
				$this->db->where('admission_id',$admission_id);
				$this->db->where('account_id',$account_id);
				$this->db->where('billing_month',$billing_month);
				$this->db->update(BILL_MASTER_TBL, $mdata); 	
				$this->AdjustCollection($bill_id); 
		    }else{
			  $bill_no	= $this->getBillID($institute_id,$branch_id,$session_id,$billing_month,$bill_date);
			  $bill_type	= 2; // 1=Monthly
			  //======== Save Bill Master ========
			  $mdata = array(
				'bill_no'  		=>$bill_no,
				'billing_month' 	=>$billing_month,
				'billing_date'		=>$bill_date,
				'credit_period'		=>$credit_period,
				'account_id'   		=>$account_id,
				'admission_id'		=>$admission_id,
				'institute_id'    	=>$institute_id,
				'branch_id'    		=>$branch_id,
				'session_id'    	=>$session_id,
				'version_id'    	=>$version_id,
				'group_id'    		=>$group_id,
				'class_id'    		=>$class_id,
				'shift_id'		=>$shift_id,
				'section_id'    	=>$section_id,
				'bill_amount'    	=>$bill_amount,
				'discount_persent'      =>$discount_percentage,
				'discount_amount'       =>$discount_amount,
				'net_bill_amount'  	=>$net_bill_amount,
				'due_amount'  		=>$due_amount,
				'description'  		=>$description,
				'invoice_note1'  	=>$invoice_note1,
				'invoice_note2'  	=>$invoice_note2,
				'bill_type'		=>$bill_type,
				'created_by'  		=>$created_by
			  );
			  $this->db->insert(BILL_MASTER_TBL, $mdata); //print $this->db->last_query();
			  $bill_id = $this->db->insert_id(); 
			  //====== Update Bill Details ========
			  $USQL= "UPDATE ".BILL_DETAILS_TBL." SET bill_id='".$bill_id."' WHERE bill_id=0 AND institute_id = $institute_id AND branch_id = $branch_id AND session_id = $session_id AND version_id=$version_id 
			  AND class_id=$class_id AND group_id=$group_id AND admission_id=$admission_id AND billing_month =$billing_month AND created_by = ".$created_by;
			  $this->db->query($USQL);
		    }
			
			$contra_id=$this->SaveJV($bill_id,$admission_id,$institute_id,$branch_id,$session_id,$version_id,$group_id,$class_id,$shift_id,$section_id,$account_id,$net_bill_amount,$discount_percentage,$discount_amount,$concession_on,$bill_date,$discount_type,$description);
		    $vsql = "SELECT voucher_no FROM ".VOUCHER_MASTER_TBL." WHERE invoice_no ='".$bill_id."' AND voucher_type = 4 AND status ='1'";
			$vquery = $this->db->query($vsql);
			if($vquery->num_rows() >0){
			$voucher_no= $vquery->row()->voucher_no;
			}else{ $voucher_no="";}
			//======== Get Bill Details ========
		    $bdsql = "SELECT * FROM ".BILL_DETAILS_TBL." WHERE institute_id = $institute_id AND branch_id = $branch_id AND session_id = $session_id AND version_id=$version_id 
		    AND class_id=$class_id AND group_id=$group_id AND admission_id=$admission_id AND billing_month =$billing_month AND bill_id=$bill_id";
			
		    $bdquery = $this->db->query($bdsql);	//echo $bdsql; 		
		    foreach($bdquery->result() as $frow){			  			   
			   //======== Save Voucher Details ========
			   if($contra_id >0){
				$mode_of_payment	= 10; // 10=Others
				$voucher_type		= 4; // 4=Journal				
				$account_head 		= $frow->fee_account;
				$cr_amount    		= $frow->total_price;
			    //==== Save Cr Fee Account =====					
			    $DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
			    $DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Cr','".$account_head."','".$cr_amount."','".$voucher_type."','".$description."','".$created_by."')";
			    $this->db->query($DSQL);
			    $this->SaveAccountLedger($voucher_no,$bill_id,$bill_date,$account_head,$voucher_type,$description,$cr_amount,"Cr","I");
				
				if($account_head==8){					
					$ASQL= "UPDATE ".ADMISSION_TBL." SET admission_type=2 WHERE institute_id = $institute_id AND branch_id = $branch_id AND session_id = $session_id AND version_id=$version_id 
					AND class_id=$class_id AND group_id=$group_id AND admission_id=$admission_id";
					$this->db->query($ASQL);
					}
			   }
		    }//end foreach
		   
		}// end num_rows
		
	}
	function getAdmissionInfo($institute_id,$branch_id,$session_id,$admission_id){
		$vsql = "SELECT * FROM ".ADMISSION_TBL." WHERE institute_id = $institute_id AND branch_id = $branch_id AND session_id = $session_id AND admission_id=$admission_id";
		$vquery = $this->db->query($vsql);
		if($vquery->num_rows() >0){
		return $vquery->row();
		}else{ return false;}
	}
	function SaveJV($bill_id,$admission_id,$institute_id,$branch_id,$session_id,$version_id,$group_id,$class_id,$shift_id,$section_id,$account_head,$net_bill_amount,$discount_percentage,$discount_amount,$concession_on,$voucher_date,$discount_type,$note=NULL){			
	    $voucher_no 	= $this->getVoucherID($voucher_date);	
		$agency_percentage	= $this->input->post('agency-percentage');	
		$agency_commission	= $this->input->post('agency-commission');	
		$vat_percentage		= $this->input->post('vat-percentage');	
		$vat_amount		= $this->input->post('vat-amount');
		$billing_month 		=$this->input->post('bill-period');
		if(empty($net_bill_amount)){$net_bill_amount=0;} 		
		$net_bill_amount = round($net_bill_amount,0,PHP_ROUND_HALF_UP);
		if(empty($agency_percentage)){$agency_percentage=0;$agency_commission=0;} 		
		$agency_commission = round($agency_commission,0,PHP_ROUND_HALF_UP);
		if(empty($vat_percentage)){$vat_percentage=0;$vat_amount=0;}  		
		$vat_amount = round($vat_amount,0,PHP_ROUND_HALF_UP);

		$dr_amount 		= ($net_bill_amount);
		$cr_amount 		= ($net_bill_amount);
		
		$mode_of_payment	= 10; // 10=Others
		$voucher_type		= 4; // 4=Journal	
		$description		= "The monthly receivable amount against tution & fee ";
		if($discount_type==1){
			if($billing_month==1){
				$d_description		= "The one time discount $discount_amount tk on admission";
				$discount_head 		= $this->session->userdata('discount_head');
			}else{
			  $d_description		= "";
			  $discount_head 		= 0;
			}
		}elseif($discount_type==2){
			$d_description		= "The monthly scholarship $discount_percentage% on $concession_on TK";
			if($discount_percentage==100){
		    $discount_head 		= $this->session->userdata('full_scholarship');
			}else{
			$discount_head 		= $this->session->userdata('partial_scholarship');  
			}
		}else{
			$discount_head=0; $d_description="";
		}
		$created_by		= $this->session->userdata('created_by');
		$contra_id		= 0;
		$vsql = "SELECT contra_id,voucher_no FROM ".VOUCHER_MASTER_TBL." WHERE invoice_no ='".$bill_id."' AND voucher_type = 4 AND status ='1'";
		$vquery = $this->db->query($vsql);
		if($vquery->num_rows() >0){
		   $contra_id = $vquery->row()->contra_id;
		   $voucher_no= $vquery->row()->voucher_no;		   		         
		   //=== Update Master =====
		   $SQL= "UPDATE ".VOUCHER_MASTER_TBL." SET invoice_no=".$bill_id.",institute_id='".$institute_id."',branch_id='".$branch_id."',session_id='".$session_id."',version_id='".$version_id."',class_id='".$class_id."',group_id='".$group_id."',admission_id='".$admission_id."',dr_amount='".$dr_amount."',cr_amount='".$cr_amount."', mode_of_payment='".$mode_of_payment."', voucher_type='".$voucher_type."', description='".$description."' WHERE contra_id = ".$contra_id;
		   $this->db->query($SQL);
		   //===== Delete All Voucher Details ======
		   if($contra_id >0){
			$DLSQL1= "DELETE FROM ".VOUCHER_DETAILS_TBL." WHERE contra_id = ".$contra_id." AND invoice_no='".$bill_id."'";
			$this->db->query($DLSQL1);
			$DLSQL2= "DELETE FROM ".ACC_LEDGER_TBL." WHERE voucher_no='".$voucher_no."' AND invoice_no = ".$bill_id;
			$this->db->query($DLSQL2);   
		   }
		   
		   //==== Dr Account Update =====	
		   if($account_head >0 && $dr_amount >0){
			   $acsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$account_head."' AND contra_id = $contra_id AND status ='1'";
			   $aquery = $this->db->query($acsql);
			   if($aquery->num_rows() >0){			
			     $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET admission_id='".$admission_id."',amount='".$dr_amount."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$account_head."' AND contra_id = ".$contra_id;		   $this->db->query($DSQL);			
			     $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$account_head,$voucher_type,$description,$dr_amount,"Dr","U");
			   }else{
			    //==== Start Dr Customer Account =====					
			    $DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,admission_id,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
			    $DSQL.="VALUES('".$contra_id."','".$bill_id."','".$admission_id."','".$voucher_no."','Dr','".$account_head."','".$dr_amount."','".$voucher_type."','".$description."','".$created_by."')";
			    $this->db->query($DSQL);
			    $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$account_head,$voucher_type,$description,$dr_amount,"Dr","I");
			   }//end else
		   }// End account_head 
		   
	       //===== Update Dr Discount Account =====
		   if($discount_amount >0 && $discount_head >0){
			   $dcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$discount_head."' AND contra_id = $contra_id AND status ='1'";
			   $dquery = $this->db->query($dcsql);
			   if($dquery->num_rows() >0){			
			   $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET admission_id='".$admission_id."', amount='".$discount_amount."', voucher_type='".$voucher_type."', description='".$d_description."' WHERE account_id='".$discount_head."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
			   $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$discount_head,$voucher_type,$d_description,$discount_amount,"Dr","U");
			   }else{
			    //==== Start Dr Discount Account =====					
			    $DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,admission_id,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
			    $DSQL.="VALUES('".$contra_id."','".$bill_id."','".$admission_id."','".$voucher_no."','Dr','".$discount_head."','".$discount_amount."','".$voucher_type."','".$d_description."','".$created_by."')";
			    $this->db->query($DSQL);
			    $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$discount_head,$voucher_type,$d_description,$discount_amount,"Dr","I");
			   }//end else
		   }
		   return $contra_id;
		   
		}else{
			$SQL= "INSERT INTO ".VOUCHER_MASTER_TBL."(invoice_no,institute_id,branch_id,session_id,version_id,class_id,group_id,admission_id,voucher_no,voucher_date,dr_amount,cr_amount, 	mode_of_payment,voucher_type,description,created_by) ";
		    $SQL.="VALUES('".$bill_id."','".$institute_id."','".$branch_id."','".$session_id."','".$version_id."','".$class_id."','".$group_id."','".$admission_id."','".$voucher_no."','".$voucher_date."','".$dr_amount."','".$cr_amount."','".$mode_of_payment."','".$voucher_type."','".$description."','".$created_by."')";
		    if($voucher_no !=""){
		      $this->db->query($SQL);
		      $contra_id = $this->db->insert_id();
		      if($contra_id >0){		         
			   if($account_head>0 && $dr_amount>0){		
			    //==== Start Dr Customer Account =====					
			    $DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,admission_id,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
			    $DSQL.="VALUES('".$contra_id."','".$bill_id."','".$admission_id."','".$voucher_no."','Dr','".$account_head."','".$dr_amount."','".$voucher_type."','".$description."','".$created_by."')";
			    $this->db->query($DSQL);
			    $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$account_head,$voucher_type,$description,$dr_amount,"Dr","I");
			   } // end account_head dr
			    
			   //===== Insert Dr Discount Account =====
			   if($discount_amount >0 && $discount_head >0){
				   $dcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$discount_head."' AND contra_id = $contra_id AND status ='1'";
				   $dquery = $this->db->query($dcsql);
				   if($dquery->num_rows() >0){			
				   $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET admission_id='".$admission_id."', amount='".$discount_amount."', voucher_type='".$voucher_type."', description='".$d_description."' WHERE account_id='".$discount_head."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
				   $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$discount_head,$voucher_type,$d_description,$discount_amount,"Dr","U");
				   }else{
					//==== Start Dr Discount Account =====					
					$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,admission_id,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
					$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$admission_id."','".$voucher_no."','Dr','".$discount_head."','".$discount_amount."','".$voucher_type."','".$d_description."','".$created_by."')";
					$this->db->query($DSQL);
					$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$discount_head,$voucher_type,$d_description,$discount_amount,"Dr","I");
				   }//end else
			   }
			   return $contra_id;
			  }else{
				return 0;  
			  }			  
		    }else{
				return 0;
			}
		}//end else
	}
	function SaveAccountLedger($voucher_no,$invoice_no,$received_date,$account_id,$transaction_type,$description,$amount,$headtype,$mode){
		$created_by	= $this->session->userdata('created_by');
		if($headtype=="Dr"){$dr=$amount; $cr=0;}else{$dr=0; $cr=$amount;}
		
		if($mode=="I"){		
		$SQL="INSERT INTO ".ACC_LEDGER_TBL."(voucher_no,invoice_no,received_date, 	account_id,transaction_type,description,dr,cr,created_by) ";
		$SQL.="VALUES('".$voucher_no."','".$invoice_no."','".$received_date."','".$account_id."','".$transaction_type."','".$description."','".$dr."','".$cr."','".$created_by."')";
		$this->db->query($SQL);
		}else{
		$CSQL= "UPDATE ".ACC_LEDGER_TBL." SET transaction_type='".$transaction_type."', description='".$description."', dr='".$dr."', cr='".$cr."' WHERE account_id='".$account_id."' AND invoice_no = ".$invoice_no;
		$this->db->query($CSQL);
		}

	}
	
	function RollbackCollection($bill_id){
	  $BSQL	= "SELECT * FROM ".BILL_ADJUST_HISTORY_TBL." WHERE bill_id = '".$bill_id."'";
	  $query = $this->db->query($BSQL);
	  $BNum  = $query->num_rows();
	  $due_amount = 0;
	  if($BNum >0){
	    foreach($query->result() as $row){
		$bill_id 			= $row->bill_id; 
		$dr_account 		= $row->dr_account; 
		$adjust_tbl 		= $row->adjust_tbl; 
		$adjust_ref 		= $row->adjust_ref;  
		$adjust_amount		= $row->adjust_amount; 
		$adjust_type		= $row->adjust_type; 
		$including_vat		= $row->including_vat;
		$headtypes      	= $this->getHeadType($dr_account);
		//======= Rullback previous collection amount =====
		if($adjust_tbl=="bill_master" && $adjust_type=="+"){			 
			$HSql= "SELECT * FROM ".BILL_MASTER_TBL." WHERE bill_id = '".$bill_id."' AND bill_no='".$adjust_ref."'";
			$hquery 	= $this->db->query($HSql);
			$srow   	= $hquery->row();
			$paid_amount 	= ($srow->paid_amount-$adjust_amount);
			$due_amount 	= ($srow->due_amount+$adjust_amount); 
			$Usql="UPDATE ".BILL_MASTER_TBL." ";
			if($including_vat >0){
				$Usql.= " SET vat_paid=1,";
		    }else{
				$Usql.= "SET ";
		    }
			$Usql.="paid_amount='$paid_amount', due_amount='$due_amount' WHERE bill_id='".$bill_id."' AND bill_no='".$adjust_ref."'";
			$this->db->query($Usql); 
		} // End if adjust_tbl
		
	     }// End foreach
	  } // End if
    }
	function AdjustCollection($bill_id){
	  $BSQL	= "SELECT * FROM ".BILL_ADJUST_HISTORY_TBL." WHERE bill_id = '".$bill_id."'";
	  $query = $this->db->query($BSQL);
	  $BNum  = $query->num_rows();
	  $due_amount = 0;
	  if($BNum >0){
	    foreach($query->result() as $row){
		$bill_id 			= $row->bill_id; 
		$dr_account 		= $row->dr_account; 
		$adjust_tbl 		= $row->adjust_tbl; 
		$adjust_ref 		= $row->adjust_ref;  
		$adjust_amount		= $row->adjust_amount; 
		$adjust_type		= $row->adjust_type; 
		$including_vat		= $row->including_vat;
		$headtypes      	= $this->getHeadType($dr_account);
		//======= adjust previous collection amount =====
		if($adjust_tbl=="bill_master" && $adjust_type=="+"){			 
			$HSql= "SELECT * FROM ".BILL_MASTER_TBL." WHERE bill_id = '".$bill_id."' AND bill_no='".$adjust_ref."'";
			$hquery 	= $this->db->query($HSql);
			$srow   	= $hquery->row();
			$paid_amount 	= ($srow->paid_amount+$adjust_amount);
			$due_amount 	= ($srow->due_amount-$adjust_amount); 
			$Usql="UPDATE ".BILL_MASTER_TBL." ";
			if($including_vat >0){
				$Usql.= " SET vat_paid=1,";
		    }else{
				$Usql.= "SET ";
		    }
			$Usql.="paid_amount='$paid_amount', due_amount='$due_amount' WHERE bill_id='".$bill_id."' AND bill_no='".$adjust_ref."'";
			$this->db->query($Usql); 
		} // End if adjust_tbl
		
	     }// End foreach
	  } // End if
    }
	function getBillID($institute_id,$branch_id,$session_id,$billing_month,$bill_date){
		$SL=""; $TotalNo=0; $BillNo = ""; $yearArr = explode("-",$bill_date);
		$ssql = "SELECT COUNT(*) as total FROM ".BILL_MASTER_TBL." WHERE institute_id =$institute_id AND branch_id = $branch_id AND session_id = $session_id AND billing_month=$billing_month AND status < 5";
		$squery = $this->db->query($ssql);				
		if($squery->num_rows() >0){
		   $TotalNo = $squery->row()->total+1;		
		   if($TotalNo <10){
		      $SL="0000".$TotalNo;
		   }elseif($TotalNo <100){
		      $SL="000".$TotalNo;
		   }elseif($TotalNo <1000){
		      $SL="00".$TotalNo;
		   }elseif($TotalNo <10000){
		      $SL="0".$TotalNo;
		   }else{
		      $SL=$TotalNo;
		   }
		}else{
		      $SL="00001";
		}
		
		$psql  = "SELECT branch_code FROM ".BRANCH_TBL." WHERE branch_id =$branch_id";
		$query = $this->db->query($psql);
		//print $this->db->last_query(); exit;
		$BC =""; $BM = 0; if($billing_month <10){$BM="0".$billing_month;}else{$BM=$billing_month;}
		if($query->num_rows() == 1){
		 $row = $query->row();		
		 $BC  = $row->branch_code;
		}		
		$BillNo = $BC.$yearArr[0]."/".$BM."/".$SL;
		return $BillNo;		
	}
	function getHeadType($account_id){
        $this->db->select('head_type');
        $this->db->from(ACC_HEAD_TBL);
        $this->db->where('account_id', $account_id);
        $query = $this->db->get();
        return $query->row()->head_type; 
    }
    function getVoucherID($invoice_date){
	$invoice_id = "";
	$INVArr     = explode("-",$invoice_date);
	$INVSL      = $this->getNextVoucherNo($INVArr[0],$INVArr[1]);
	$invoice_id = $INVArr[0]."/".$INVArr[1]."/".$INVSL;
	return $invoice_id;	
    }
    function getNextVoucherNo($year,$month){
		$INVSL=""; $INVNo="";		
		if ($year !="" && $month !="") {
			$SQL = "SELECT COUNT(voucher_no) AS voucher_no FROM ".VOUCHER_MASTER_TBL." WHERE YEAR(voucher_date)='".$year."' AND MONTH(voucher_date)='".$month."'";
			$query = $this->db->query($SQL);				
			$INVNo = $query->row()->voucher_no+1;
			if($INVNo <10){
			$INVSL = "000".$INVNo;	
			}elseif($INVNo <100){
			$INVSL = "00".$INVNo;	
			}elseif($INVNo <1000){
			$INVSL = "0".$INVNo;	
			}else{
			$INVSL = $INVNo;	
			}
		}else{
			$INVSL="0001";
		}
		return $INVSL;
    }
	function GetAjaxFeeAmount(){
		$institute_id		=$this->input->post('institute_id');
		$branch_id			=$this->input->post('branch_id');
		$session_id			=$this->input->post('session_id');
		$version_id			=$this->input->post('version_id');
		$group_id			=$this->input->post('group_id');
		$class_id			=$this->input->post('class_id');		
		$admission_id		=$this->input->post('admission_id');		
		$particulars_id		=$this->input->post('particulars-id');		
		$fee_period			=$this->input->post('bill-period');
		
		if($fee_period >0){
			  $FeePeriod = (($fee_period * 2) - 1);
			  $afsql = "SELECT f.fee_amount FROM ".COURSE_FEE_TBL." as f,".ACC_HEAD_TBL." as a WHERE f.account_id = a.account_id AND f.institute_id = $institute_id AND f.branch_id = $branch_id AND f.sessions_id = $session_id AND f.version_id=$version_id 
			  AND f.class_id=$class_id AND f.group_id=$group_id AND a.account_id=$particulars_id AND SUBSTRING(f.`fee_month`,$FeePeriod,1) > 0 ";
			  $afsql.= " GROUP BY f.fee_id,f.account_id";
			  $query = $this->db->query($afsql);
			  if($query->num_rows() >0){
				 echo $query->row()->fee_amount;
			  }else{
				  echo "0";
			  }
		}else{
			echo "0";  
		}		  
	}
	
	function GetAjaxProductPurchasePrice(){
		$institute_id		=$this->input->post('institute_id');
		$branch_id			=$this->input->post('branch_id');		
		$particulars_id		=$this->input->post('particulars-id');
		
		if($particulars_id >0){
			  $afsql = "SELECT p.purchase_price FROM ".PRODUCT_TBL." as p,".ACC_HEAD_TBL." as a WHERE p.product_id = a.account_id AND p.company_id = $institute_id AND p.branch_id = $branch_id AND a.account_id=$particulars_id AND p.status > 0 ";
			  $afsql.= " GROUP BY p.product_id";
			  $query = $this->db->query($afsql);
			  if($query->num_rows() >0){
				 echo $query->row()->purchase_price;
			  }else{
				  echo "0";
			  }
		}else{
			echo "0";  
		}		  
	}
	function loadMonthlyBill(){
		$bill_id		= $this->input->post('bill-id');
		if(empty($bill_id)){
			$bill_id	= 0;
			$status		= 1;
		}else{
			$this->db->select('status');
			$this->db->from(BILL_MASTER_TBL);
			$this->db->where('bill_id', $bill_id);
			$bquery 		= $this->db->get();
			$status			= $bquery->row()->status;
		}
		
		$bill_date			=$this->formatDate($this->input->post('bill_date'));
		$billing_month 		=$this->input->post('bill-period');
		$admission_id		=$this->input->post('admission_id'); 
		$institute_id		=$this->input->post('institute_id');
		$branch_id			=$this->input->post('branch_id');
		$session_id			=$this->input->post('session_id');
		$version_id			=$this->input->post('version_id');
		$class_id			=$this->input->post('class_id');
		$group_id			=$this->input->post('group_id');
		$shift_id			=$this->input->post('shift_id');
		$section_id			=$this->input->post('section_id');
		
		$quantity			= $this->input->post('quantity');
		$unit_price			= $this->input->post('unit_price');
		$total_price		= ($quantity * $unit_price);
		$total_price 		= round($total_price,0,PHP_ROUND_HALF_UP);
		$remarks			= $this->input->post('remarks');			
    		$created_by			= $this->session->userdata('created_by');
		
		$this->db->select('student_name_en as student_id');
		$this->db->from(ADMISSION_TBL);
		$this->db->where('admission_id', $admission_id);
		$this->db->where('institute_id', $institute_id);
		$this->db->where('branch_id', $branch_id);
		$aquery 		= $this->db->get();
		$student_id		= $aquery->row()->student_id;
		$FeePeriod 		= (($billing_month * 2) - 1);
		
		$bdsql = "SELECT a.account_name,a.account_details,b.* FROM ".BILL_DETAILS_TBL." as b,".ACC_HEAD_TBL." as a WHERE b.fee_account = a.account_id AND b.institute_id = $institute_id AND b.branch_id = $branch_id AND b.session_id = $session_id AND b.version_id=$version_id 
		AND b.class_id=$class_id AND b.group_id=$group_id AND admission_id=$admission_id AND b.`billing_month`=$billing_month ";
		$bdsql.= " GROUP BY b.details_id ORDER BY a.account_name ASC";
		$query = $this->db->query($bdsql); 
		$bnum = $query->num_rows();
		
		if($bill_id == 0 && $bnum ==0 && $bill_date!=""){
			$afsql = "SELECT a.account_name,a.account_details,f.* FROM ".COURSE_FEE_TBL." as f,".ACC_HEAD_TBL." as a WHERE f.account_id = a.account_id AND f.institute_id = $institute_id AND f.branch_id = $branch_id AND f.sessions_id = $session_id AND f.version_id=$version_id 
			AND f.class_id=$class_id AND f.group_id=$group_id AND SUBSTRING(f.`fee_month`,$FeePeriod,1) > 0 ";
			$afsql.= " GROUP BY f.fee_id,f.account_id ORDER BY a.account_name ASC";
			$query = $this->db->query($afsql);
			foreach($query->result() as $row){
			  $fee_id			= $row->fee_id;
			  $particulars_id	= $row->account_id;
			  $quantity 		= 1;
			  $unit_price		= $row->fee_amount;
			  $total_price		= ($quantity * $unit_price);
			  $total_price 		= round($total_price,0,PHP_ROUND_HALF_UP);
			  $remarks			= "Monthly Fee";
			  $ddata = array(
				'bill_id'  		=>$bill_id,
				'billing_month' 	=>$billing_month,
				'billing_date'		=>$bill_date,
				'fee_account'		=>$particulars_id,
				'account_id'   		=>$student_id,
				'admission_id'		=>$admission_id,
				'institute_id'    	=>$institute_id,
				'branch_id'    		=>$branch_id,
				'session_id'    	=>$session_id,
				'version_id'    	=>$version_id,
				'class_id'    		=>$class_id,
				'group_id'    		=>$group_id,
				'section_id'    	=>$section_id,
				'quantity'    		=>$quantity,
				'unit_price'    	=>$unit_price,
				'total_price'  		=>$total_price,
				'remarks'  		=>$remarks,
				'status'  		=>$status,
				'created_by'  		=>$created_by
				);	
				$this->db->insert(BILL_DETAILS_TBL, $ddata);
			}		
			
			//print  $this->db->last_query();
		}
	}
	function GetAjaxBillList(){		
		$bill_id		= $this->input->post('bill-id');
		if(empty($bill_id)){
			$bill_id	= 0;
			$status		= 1;
		}		
		$bill_date			=$this->formatDate($this->input->post('bill_date'));
		$billing_month 		=$this->input->post('bill-period');
		$admission_id		=$this->input->post('admission_id'); 
		$institute_id		=$this->input->post('institute_id');
		$branch_id			=$this->input->post('branch_id');
		$session_id			=$this->input->post('session_id');
		$version_id			=$this->input->post('version_id');
		$class_id			=$this->input->post('class_id');
		$group_id			=$this->input->post('group_id');
		$shift_id			=$this->input->post('shift_id');
		$section_id			=$this->input->post('section_id');
		
		$this->loadMonthlyBill(); $menu_slug= $this->uri->segment(1);
		$hasEditPM = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM  = $this->Site_model->hasOptionPermission($menu_slug,"Delete");
		
		$bdsql = "SELECT b.*, a.account_name,a.account_details,p.period_name_en as period_name,p.period_year FROM ".BILL_DETAILS_TBL." as b,".ACC_HEAD_TBL." as a,".PERIOD_TBL." as p WHERE b.fee_account = a.account_id AND b.billing_month=p.period_id AND b.institute_id = $institute_id AND b.branch_id = $branch_id AND b.session_id = $session_id AND b.version_id=$version_id 
		AND b.class_id=$class_id AND b.group_id=$group_id AND b.admission_id=$admission_id AND b.`billing_month`=$billing_month";
		$bdsql.= " GROUP BY b.details_id ORDER BY a.account_name ASC";
		$query = $this->db->query($bdsql); 
		if($query->num_rows() >0){	
		  echo 
		  '<table width="100%"  border="0" class="table table-responsive table-bordered table-hover custab">
			<thead>
			  <tr class="active">
			  	<th width="2%">'.$this->lang->line("sl").'</th>
				<th width="15%">'.$this->lang->line("fee_period").'</th>
				<th width="32%">'.$this->lang->line("particulars").'</th>
				<th width="13%" class="text-right">'.$this->lang->line("quantity").'</th>
				<th width="13%" class="text-right">'.$this->lang->line("unit_price").'</th>
				<th width="13%" class="text-right">'.$this->lang->line("total_amount").'</th>
				<th width="12%" class="text-center">'.$this->lang->line("options").'</th>
			  </tr>
			</thead>';
			  $i=1; $TotalBill=0;
			  foreach($query->result() as $row){
			  //if($i%2==0){ $tblrow="success";}else{$tblrow="warning";}
			  $TotalBill+=$row->total_price;
			  echo "<tr class='default'>
			  	<td>".$i."</td>
				<td>".$row->period_name." - ".$row->period_year."</td>
				<td>".$row->account_name."</td>
			  	<td class='text-right'>".$row->quantity."</td>
				<td class='text-right'>".$row->unit_price."</td>
				<td class='text-right'>".$row->total_price."</td>
				<td align='center'>";
				if($hasEditPM){
				echo "<span data-toggle='tooltip' data-original-title='Edit'><a class='btn btn-info btn-xs' data-toggle='modal' onclick=editRow('".$row->details_id."','".$row->bill_id."') id='".$row->details_id."' href='#'><i class='fa fa-edit'></i></a></span> &nbsp;";
				}
				if($hasDelPM){				
				echo "<span data-toggle='tooltip' data-original-title='Delete'><a class='btn btn-danger btn-xs' data-toggle='modal' onclick=deleteRow('".$row->details_id."','".$row->bill_id."') id='".$row->details_id."' href='#deleteDraftModal'><i class='fa fa-trash'></i></a></span>";
				}
				echo "</td>
				</tr>";
			  $i++;
			  }
			  echo "<tr class='bg-light'>
			  	<th colspan='5'>".$this->lang->line("total_amount")."</th>
				<th class='text-right'>".$TotalBill."<input type='hidden' name='total_bill' id='total_bill' value='".$TotalBill."'></th>
				<th class='text-right'>&nbsp;</th>
			  </tr>";
		  echo '</table>';
		}//end num_rows	
	}
	
	function GetAjaxConcessionDetails(){
		$institute_id		=$this->input->post('institute_id');
		$branch_id			=$this->input->post('branch_id');
		$session_id			=$this->input->post('session_id');
		$version_id			=$this->input->post('version_id');
		$group_id			=$this->input->post('group_id');
		$class_id			=$this->input->post('class_id');
		$fee_period			=$this->input->post('bill-period');	
		$admission_id	 	=$this->input->post('admission_id');		
		$bill_amount		=$this->input->post('total_bill');
		$asql = "SELECT discount_type,discount_percentage,discount_amount FROM ".ADMISSION_TBL." WHERE institute_id = $institute_id AND branch_id = $branch_id AND session_id = $session_id AND version_id = $version_id AND admission_id = $admission_id AND status = 1";
		$aquery = $this->db->query($asql);				
		if($aquery->num_rows() >0){				
			$discount_type 		= $aquery->row()->discount_type;
			$discount_percentage= $aquery->row()->discount_percentage;
			$discount_amount 	= $aquery->row()->discount_amount;
		}			
		
		if($institute_id >0 && $session_id >0){
			if($fee_period >0){
				$FeePeriod = (($fee_period * 2) - 1);					  
				$concession_on =0;	
				if($discount_percentage >0){
				  //===== Get Concession On ======
				  $concession_hreads = $this->session->userdata('concession_hreads');
				  $cfsql = "SELECT SUM(fee_amount) as concession_on FROM ".COURSE_FEE_TBL." WHERE institute_id = $institute_id AND branch_id = $branch_id AND sessions_id = $session_id AND version_id=$version_id 
				  AND class_id=$class_id AND group_id=$group_id AND SUBSTRING(`fee_month`,$FeePeriod,1) > 0 AND `account_id` IN($concession_hreads)";
				  $caquery = $this->db->query($cfsql);				
				  if($caquery->num_rows() >0){
				   $concession_on 	= $caquery->row()->concession_on;
				   return $concession_on."##&##".$discount_type."##&##".$discount_percentage."##&##".$discount_amount;				  
				  }else{
					  return $bill_amount."##&##".$discount_type."##&##".$discount_percentage."##&##".$discount_amount;
				  }
				}elseif($discount_percentage ==0){
					return $bill_amount."##&##".$discount_type."##&##".$discount_percentage."##&##".$discount_amount;  
				}
			}else{
				return $bill_amount."##&##".$discount_type."##&##".$discount_percentage."##&##".$discount_amount;
			}
		}
	}
	
	function GetAjaxConcessionOn(){
		$institute_id		=$this->input->post('institute_id');
		$branch_id			=$this->input->post('branch_id');
		$session_id			=$this->input->post('session_id');
		$version_id			=$this->input->post('version_id');
		$group_id			=$this->input->post('group_id');
		$class_id			=$this->input->post('class_id');
		$fee_period			=$this->input->post('bill-period');		
		$discount_percentage=$this->input->post('discount_percentage');		
		$bill_amount		=$this->input->post('total_bill');
		if(empty($fee_period)){$fee_period=0;}
		if($institute_id >0 && $session_id >0){
			$ssql = "SELECT MONTH(`session_start`) as session_start_date,session_start as bill_date FROM ".SESSION_TBL." WHERE sessions_id = $session_id AND institute_id = $institute_id AND session_status = 1";
			$squery = $this->db->query($ssql);				
			if($squery->num_rows() >0){				
				if($fee_period >0){
					  $FeePeriod = (($fee_period * 2) - 1);
				}else{
					  $FeePeriod = (($squery->row()->session_start_date * 2) - 1);
				}
					  
				$concession_on =0;	
				if($discount_percentage >0){
				  //===== Get Concession On ======
				  $concession_hreads = $this->session->userdata('concession_hreads');
				  $cfsql = "SELECT SUM(fee_amount) as concession_on FROM ".COURSE_FEE_TBL." WHERE institute_id = $institute_id AND branch_id = $branch_id AND sessions_id = $session_id AND version_id=$version_id 
				  AND class_id=$class_id AND group_id=$group_id AND SUBSTRING(`fee_month`,$FeePeriod,1) > 0 AND `account_id` IN($concession_hreads)";
				  $caquery = $this->db->query($cfsql);				
				  if($caquery->num_rows() >0){
				   $concession_on 	= $caquery->row()->concession_on;
				   return $concession_on;				  
				  }else{
					  return $bill_amount;
				  }
				}elseif($discount_percentage ==0){
					return $bill_amount;  
				}
			}else{
				return $bill_amount;
			}
		}
	}
	//===== Start Generate Bill ======
		
	function GenerateBill4All(){
		$bill_date			=$this->formatDate($this->input->post('bill-date'));
		$billing_month 			=$this->input->post('bill-period');
		$institute_id			=$this->input->post('institute_id');
		$branch_id			=$this->input->post('branch_id');
		$session_id			=$this->input->post('session_id');
		$version_id			=$this->input->post('version_id');
		$class_id			=$this->input->post('class_id');
		$group_id			=$this->input->post('group_id');
		$credit_period			=$this->input->post('credit_period');
		$created_by			=$this->session->userdata('created_by');
		$FeePeriod 			=(($billing_month * 2) - 1); 
		
		$fdsql = "SELECT * FROM ".COURSE_FEE_TBL." WHERE institute_id = $institute_id AND branch_id = $branch_id AND sessions_id = $session_id AND version_id=$version_id 
		AND class_id=$class_id AND group_id=$group_id AND SUBSTRING(`fee_month`,$FeePeriod,1) > 0";		
		$fdquery = $this->db->query($fdsql);	//echo $fdsql; 
		   
		$this->db->select('admission_id,section_id,shift_id,student_name_en as student_id,discount_type,discount_percentage,discount_amount,less_tuitionfee');
		$this->db->from(ADMISSION_TBL);
		$this->db->where('institute_id', $institute_id);
		$this->db->where('branch_id', $branch_id);
		$this->db->where('session_id', $session_id);
		$this->db->where('version_id', $version_id);
		$this->db->where('class_id', $class_id);
		$this->db->where('group_id', $group_id);		
		$aquery = $this->db->get();
		if($aquery->num_rows() >0){
		   foreach($aquery->result() as $arow){
		   $admission_id		= $arow->admission_id;
		   $student_id			= $arow->student_id;		
		   $account_id			= $arow->student_id;
		   $section_id			= $arow->section_id;
		   $shift_id			= $arow->shift_id;
		   $discount_type		= $arow->discount_type;
		   $discount_percentage 	= $arow->discount_percentage;
		   $less_tuitionfee 		= $arow->less_tuitionfee;
		   $discount_amount	    	= $arow->discount_amount;
		   //==== bill was created? =====
		   $bmsql = "SELECT * FROM ".BILL_MASTER_TBL." WHERE institute_id = $institute_id AND branch_id = $branch_id AND session_id = $session_id AND version_id=$version_id 
		   AND class_id=$class_id AND group_id=$group_id AND admission_id=$admission_id AND billing_month =$billing_month ";			
		   $bmquery = $this->db->query($bmsql);
		   if($bmquery->num_rows()==0){	
		   //======== Save Bill Details ========
		   $bill_id = 0; $total_bill = 0;
		   		
		   foreach($fdquery->result() as $frow){
		     $ddata = array(
			 'bill_id'  		=>$bill_id,
			 'billing_month' 	=>$billing_month,
			 'billing_date'		=>$bill_date,
			 'fee_id'		=>$frow->fee_id,
			 'fee_account'		=>$frow->account_id,
			 'account_id'   	=>$account_id,
			 'admission_id'		=>$admission_id,
			 'institute_id'    	=>$institute_id,
			 'branch_id'    	=>$branch_id,
			 'session_id'    	=>$session_id,
			 'version_id'    	=>$version_id,
			 'class_id'    		=>$class_id,
			 'group_id'    		=>$group_id,
			 'section_id'    	=>$section_id,
			 'unit_price'    	=>$frow->fee_amount,
			 'total_price'  	=>$frow->fee_amount,
			 'created_by'  		=>$created_by
		     );
		     $this->db->insert(BILL_DETAILS_TBL, $ddata);
		     $total_bill+=$frow->fee_amount;
		   }//end foreach
		   //===== Start Absent Fine ======
		   $absent_fine =0; $total_absent_fine =0; $attendance_month =0; $absent_head =0;
		   $attendance_month = ($billing_month - 1);
		   $absent_head =$this->session->userdata('absent_head');
		   if($attendance_month >0 && $absent_head >0){
			   $acfsql = "SELECT * FROM ".COURSE_FINE_TBL." WHERE institute_id = $institute_id AND branch_id = $branch_id AND sessions_id = $session_id AND version_id=$version_id 
			   AND class_id=$class_id AND group_id=$group_id AND absent_fine >0";
			   $acfquery = $this->db->query($acfsql);				
			   if($acfquery->num_rows() >0){
				   $absent_fine 	   = $acfquery->row()->absent_fine; $quantity=0;
				   $atsql = "SELECT * FROM ".ATTENDANCE_TBL." WHERE institute_id = $institute_id AND branch_id = $branch_id AND session_id = $session_id AND version_id=$version_id 
				   AND class_id=$class_id AND group_id=$group_id AND account_id =$admission_id AND MONTH(`attendance_date`)=$attendance_month AND `present`=0 AND fine_count=0";
				   $atquery = $this->db->query($atsql);				
				   if($atquery->num_rows() >0){ 
				     	 $quantity = $atquery->num_rows();
					 $total_absent_fine = ($quantity * $absent_fine);
					 $adata = array(
					 'bill_id'  		=>$bill_id,
					 'billing_month' 	=>$billing_month,
					 'billing_date'		=>$bill_date,
					 'fee_id'		=>0,
					 'fee_account'		=>$absent_head,
					 'account_id'   	=>$account_id,
					 'admission_id'		=>$admission_id,
					 'institute_id'    	=>$institute_id,
					 'branch_id'    	=>$branch_id,
					 'session_id'    	=>$session_id,
					 'version_id'    	=>$version_id,
					 'class_id'    		=>$class_id,
					 'group_id'    		=>$group_id,
					 'section_id'    	=>$section_id,
					 'quantity'		=>$quantity,
					 'unit_price'    	=>$absent_fine,
					 'total_price'  	=>$total_absent_fine,
					 'created_by'  		=>$created_by
					 );
					 $this->db->insert(BILL_DETAILS_TBL, $adata);
					 $total_bill+=$total_absent_fine;
					 /*
					 $atusql = "UPDATE ".ATTENDANCE_TBL." SET fine_count=1 WHERE institute_id = $institute_id AND branch_id = $branch_id AND session_id = $session_id AND version_id=$version_id 
				     AND class_id=$class_id AND group_id=$group_id AND account_id =$admission_id AND MONTH(`attendance_date`)=$attendance_month AND `present`=0 AND fine_count=0";
				     $this->db->query($atusql);
					 */
				   }
			   }			   
		   }
		   $invoice_note1		= "Please issue A/C payee cheque/Pay Order/DD/BEFTN in favor of ".$this->session->userdata('company_name');
		   $invoice_note2		= "Payment should be clear with in $credit_period days after receiving this invoice.";		
		
		   if(empty($discount_percentage)){$discount_percentage=0;} if(empty($discount_amount)){$discount_amount=0;}
		   		   
		   $bill_amount = $total_bill; 		   
		   //====== Start Calculate Discount ======
		   $concession_on =0;	
		   if($discount_percentage >0 && $discount_type==2){
		     //===== Get Consession On ======
			 $concession_hreads = $this->session->userdata('concession_hreads');
			 $cfsql = "SELECT SUM(fee_amount) as concession_on FROM ".COURSE_FEE_TBL." WHERE institute_id = $institute_id AND branch_id = $branch_id AND sessions_id = $session_id AND version_id=$version_id 
			 AND class_id=$class_id AND group_id=$group_id AND SUBSTRING(`fee_month`,$FeePeriod,1) > 0 AND `account_id` IN($concession_hreads)";
		     $caquery = $this->db->query($cfsql);				
			 if($caquery->num_rows() >0){
			   $concession_on 	= $caquery->row()->concession_on; 
			   if($discount_percentage >0 && $less_tuitionfee==0){
			   $discount_amount = (($concession_on/100) * $discount_percentage);
			   $net_bill_amount = ($bill_amount - $discount_amount);
		       }elseif($discount_percentage >0 && $less_tuitionfee >0){
			   $discount_amount = $less_tuitionfee;
			   $net_bill_amount = ($bill_amount - $discount_amount);
			   }
			 }
		   }elseif($discount_percentage ==0 && $discount_amount >0 && $discount_type==1){
			 if($discount_type==1 && $billing_month==1){
			   $net_bill_amount = ($bill_amount - $discount_amount); 
			 }else{ $net_bill_amount = ($bill_amount - 0); $discount_amount=0;}			 
		   }else{
			 $net_bill_amount = $bill_amount;  
		   }
		   //===== End Calculate Discount 
		   
		   $due_amount	= $net_bill_amount;
		   $description	= "Total bill of amount against monthly tution & fee";
		   	   
		   $bill_no	= $this->getBillID($institute_id,$branch_id,$session_id,$billing_month,$bill_date);
		   if($billing_month==1){
		       $bill_type	= 1; // 1=Monthly 1st Bill
		   }else{
		       $bill_type	= 2; // 2=Monthly
		   }
		   //======== Save Bill Master ========
		   $mdata = array(
				'bill_no'  		=>$bill_no,
				'billing_month' 	=>$billing_month,
				'billing_date'		=>$bill_date,
				'credit_period'		=>$credit_period,
				'account_id'   		=>$account_id,
				'admission_id'		=>$admission_id,
				'institute_id'    	=>$institute_id,
				'branch_id'    		=>$branch_id,
				'session_id'    	=>$session_id,
				'version_id'    	=>$version_id,
				'group_id'    		=>$group_id,
				'class_id'    		=>$class_id,
				'shift_id'		=>$shift_id,
				'section_id'    	=>$section_id,
				'bill_amount'    	=>$bill_amount,
				'discount_persent'  	=>$discount_percentage,
				'discount_amount'   	=>$discount_amount,
				'net_bill_amount'  	=>$net_bill_amount,
				'due_amount'  		=>$due_amount,
				'description'  		=>$description,
				'invoice_note1'  	=>$invoice_note1,
				'invoice_note2'  	=>$invoice_note2,
				'bill_type'		=>$bill_type,
				'created_by'  		=>$created_by
			);
			$this->db->insert(BILL_MASTER_TBL, $mdata); //print $this->db->last_query();
			$bill_id = $this->db->insert_id(); 
			//====== Update Bill Details ========
			$USQL= "UPDATE ".BILL_DETAILS_TBL." SET bill_id='".$bill_id."' WHERE bill_id=0 AND institute_id = $institute_id AND branch_id = $branch_id AND session_id = $session_id AND version_id=$version_id 
			AND class_id=$class_id AND group_id=$group_id AND admission_id=$admission_id AND billing_month =$billing_month AND created_by = ".$created_by;
			$this->db->query($USQL);
			if(empty($admission_id)){$admission_id=0;}if(empty($institute_id)){$institute_id=0;}if(empty($branch_id)){$branch_id=0;}if(empty($session_id)){$session_id=0;}
			if(empty($version_id)){$version_id=0;}if(empty($group_id)){$group_id=0;}if(empty($class_id)){$class_id=0;}if(empty($shift_id)){$shift_id=0;}if(empty($section_id)){$section_id=0;}
			$contra_id=$this->SaveJV($bill_id,$admission_id,$institute_id,$branch_id,$session_id,$version_id,$group_id,$class_id,$shift_id,$section_id,$account_id,$net_bill_amount,$discount_percentage,$discount_amount,$concession_on,$bill_date,$discount_type,$description);
		        $CUSQL= "UPDATE ".VOUCHER_MASTER_TBL." SET period_id='".$billing_month."', account_id=$account_id WHERE contra_id=$contra_id AND institute_id = $institute_id AND branch_id = $branch_id AND session_id = $session_id AND version_id=$version_id AND class_id=$class_id AND group_id=$group_id AND admission_id=$admission_id";
			$this->db->query($CUSQL);
			
			$vsql = "SELECT voucher_no FROM ".VOUCHER_MASTER_TBL." WHERE invoice_no ='".$bill_id."' AND voucher_type = 4 AND status ='1'";
			$vquery = $this->db->query($vsql);
			if($vquery->num_rows() >0){
			$voucher_no= $vquery->row()->voucher_no;
			}else{ $voucher_no="";}
			//======== Get Bill Details ========
		    $bdsql = "SELECT * FROM ".BILL_DETAILS_TBL." WHERE institute_id = $institute_id AND branch_id = $branch_id AND session_id = $session_id AND version_id=$version_id 
		    AND class_id=$class_id AND group_id=$group_id AND admission_id=$admission_id AND billing_month =$billing_month AND bill_id=$bill_id";
			
		    $bdquery = $this->db->query($bdsql);	//echo $bdsql; 		
		    foreach($bdquery->result() as $frow){			  			   
			   //======== Save Voucher Details ========
			   if($contra_id >0){
				$mode_of_payment	= 10; // 10=Others
				$voucher_type		= 4; // 4=Journal				
				$account_head 		= $frow->fee_account;
				$cr_amount    		= $frow->total_price;
			    //==== Save Cr Fee Account =====					
			    $DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,admission_id,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
			    $DSQL.="VALUES('".$contra_id."','".$bill_id."','".$admission_id."','".$voucher_no."','Cr','".$account_head."','".$cr_amount."','".$voucher_type."','".$description."','".$created_by."')";
			    $this->db->query($DSQL);
			    $this->SaveAccountLedger($voucher_no,$bill_id,$bill_date,$account_head,$voucher_type,$description,$cr_amount,"Cr","I");
			   }
		    }//end foreach
			}//end bill num	row
		  }//end admission foreach	
		}// end admission num_rows		
	}
	
	//======== End Generate Bill ==========

	//======== Start Due Process ==========		
	function AllDueBillProcess(){
		$process_date			=$this->formatDate($this->input->post('process-date'));
		$billing_period 		=$this->input->post('bill-period');
		$institute_id			=$this->input->post('institute_id');
		$branch_id			=$this->input->post('branch_id');
		$session_id			=$this->input->post('session_id');
		$version_id			=$this->input->post('version_id');
		$class_id			=$this->input->post('class_id');
		$group_id			=$this->input->post('group_id');
		$credit_period			=$this->input->post('credit_period');
		$created_by			=$this->session->userdata('created_by');
		$FeePeriod 			=(($billing_month * 2) - 1);
		$late_payment_fine=0; $due_payment_fine=0; $defaulter_fine=0; $due_quantity=0; $total_bill = 0;
		$due_fine_head =0; $due_fine_amount=0;
		if($institute_id>0 && $branch_id>0 && $session_id>0 && $version_id>0 && $class_id>0 && $group_id >0){
		 $dbfsql = "SELECT * FROM ".COURSE_FINE_TBL." WHERE institute_id = $institute_id AND branch_id = $branch_id AND sessions_id = $session_id AND version_id=$version_id 
		 AND class_id=$class_id AND group_id=$group_id";
		 $dbfquery = $this->db->query($dbfsql);				
		 if($dbfquery->num_rows() >0){
		   $late_payment_fine = $dbfquery->row()->late_payment_fine;		   
		   $due_payment_fine  = $dbfquery->row()->due_payment_fine;		   
		   $defaulter_fine    = $dbfquery->row()->defaulter_fine;
		 }
		}
				   
		$this->db->select("*,DATEDIFF('".$process_date."', billing_date) as due_days");
		$this->db->from(BILL_MASTER_TBL);
		$this->db->where('institute_id', $institute_id);
		$this->db->where('branch_id', $branch_id);
		$this->db->where('session_id', $session_id);
		$this->db->where('version_id', $version_id);
		if($class_id >0){
		$this->db->where('class_id', $class_id);
		}
		if($group_id >0){
		$this->db->where('group_id', $group_id);
		}
		if($billing_period >0){
		$this->db->where('billing_month', $billing_period);
		}	
		$this->db->where("DATEDIFF('".$process_date."', billing_date) >=", $credit_period);
		$this->db->where('due_amount >', 0);
		$this->db->where('due_count', 0);
		$this->db->where('status <', 5);
		$this->db->group_by('bill_id');
		$this->db->order_by('billing_date,admission_id','DESC');		
		$bmquery = $this->db->get(); //print $this->db->last_query(); exit;
		if($bmquery->num_rows() >0){		   		   
		   foreach($bmquery->result() as $bmrow){
		   $bill_id		= $bmrow->bill_id;
		   $bill_no 		= $bmrow->bill_no;
		   $admission_id	= $bmrow->admission_id;
		   $billing_month	= $bmrow->billing_month;
		   $billing_date	= $bmrow->billing_date;
		   if($credit_period==0){
		   $credit_period	= $bmrow->credit_period;
		   }
		   $student_id		= $bmrow->account_id;
		   $account_id		= $bmrow->account_id;
		   $class_id		= $bmrow->class_id;
		   $group_id		= $bmrow->group_id;
		   $shift_id		= $bmrow->shift_id;
		   $section_id		= $bmrow->section_id;
		   $shift_id		= $bmrow->shift_id;
		   $total_bill		= $bmrow->bill_amount;
		   $due_count		= $bmrow->due_count;

		   $bmcsql = "SELECT * FROM ".BILL_MASTER_TBL." WHERE institute_id = $institute_id AND branch_id = $branch_id AND session_id = $session_id AND version_id=$version_id 
		    AND class_id=$class_id AND group_id=$group_id AND admission_id=$admission_id AND status < 5 
		    AND DATEDIFF('".$process_date."', billing_date) >= $credit_period AND due_amount >0";
			
		   $bmcquery = $this->db->query($bmcsql);
		   if($bmcquery->num_rows() >0){
			$due_quantity = $bmcquery->num_rows();
			//===== Start Save Process ===
			if($late_payment_fine==0 || $due_payment_fine==0 || $defaulter_fine==0){
			    $dbfsql = "SELECT * FROM ".COURSE_FINE_TBL." WHERE institute_id = $institute_id AND branch_id = $branch_id AND sessions_id = $session_id AND version_id=$version_id 
			    AND class_id=$class_id AND group_id=$group_id";
			    $dbfquery = $this->db->query($dbfsql);				
			    if($dbfquery->num_rows() >0){
			    $late_payment_fine = $dbfquery->row()->late_payment_fine;		   
			    $due_payment_fine  = $dbfquery->row()->due_payment_fine;		   
			    $defaulter_fine    = $dbfquery->row()->defaulter_fine;
			    }
			 }
			   
			 if($due_quantity==1){
			     $due_fine_head   = $this->session->userdata('late_payment_head');
			     $due_fine_amount = $late_payment_fine;
			 }elseif($due_quantity==2){
			     $due_fine_head   = $this->session->userdata('due_payment_head');
			     $due_fine_amount = $due_payment_fine;
			 }elseif($due_quantity>=3){
			     $due_fine_head   = $this->session->userdata('defaulter_head');
			     $due_fine_amount = $defaulter_fine;
			 }
			   
			 if($due_quantity >0 && $due_fine_head>0 && $due_fine_amount>0 && $due_count==0){

			     $asql = "SELECT * FROM ".ADMISSION_TBL." WHERE institute_id = $institute_id AND branch_id = $branch_id AND session_id = $session_id AND version_id=$version_id AND admission_id=$admission_id AND status=1";
			     $aquery = $this->db->query($asql);				
			     if($aquery->num_rows() >0){
			       $discount_type 	 	= $aquery->row()->discount_type; 
			       $discount_percentage 	= $aquery->row()->discount_percentage;
			       $discount_amount 	= $aquery->row()->discount_amount;
			       $less_tuitionfee 	= $aquery->row()->less_tuitionfee;
			       $account_id 		= $aquery->row()->student_name_en;
			       if($discount_type==1){$discount_percentage=0; $less_tuitionfee=0;}
			     }
			     //======== Start Save Bill Details (Fine) ========		   
			     $adata = array(
				 'bill_id'  		=>$bill_id,
				 'billing_month' 	=>$billing_month,
				 'billing_date'		=>$billing_date,
				 'fee_id'		=>0,
				 'fee_account'		=>$due_fine_head,
				 'account_id'   	=>$account_id,
				 'admission_id'		=>$admission_id,
				 'institute_id'    	=>$institute_id,
				 'branch_id'    	=>$branch_id,
				 'session_id'    	=>$session_id,
				 'version_id'    	=>$version_id,
				 'class_id'    		=>$class_id,
				 'group_id'    		=>$group_id,
				 'section_id'    	=>$section_id,
				 'unit_price'    	=>$due_fine_amount,
				 'total_price'  	=>$due_fine_amount,
				 'created_by'  		=>$created_by
			     );
			     $this->db->insert(BILL_DETAILS_TBL, $adata);
			     $total_bill+=$due_fine_amount;
			   
			     $invoice_note1	= "Please issue A/C payee cheque/Pay Order/DD/BEFTN in favor of ".$this->session->userdata('company_name');
			     $invoice_note2	= "Payment should be clear with in $credit_period days after receiving this invoice.";		
					   		   		   
			     $bill_amount = $total_bill; 		   
			   
			     if($total_bill >0){
			       $bill_amount = $total_bill; 
			       $FeePeriod = (($billing_month * 2) - 1); 
			       //====== Start Calculate Discount ======
			       $concession_on =0;	
			       if($discount_type ==2 && $discount_percentage >0 && $discount_amount==0){
				  //===== Get Consession On ======
				  $concession_hreads = $this->session->$serdata('concession_hreads');
				  $cfsql = "SELECT SUM(fee_amount) as concession_on FROM ".COURSE_FEE_TBL." WHERE institute_id = $institute_id AND branch_id = $branch_id AND sessions_id = $session_id AND version_id=$version_id 
				  AND class_id=$class_id AND group_id=$group_id AND SUBSTRING(`fee_month`,$FeePeriod,1) > 0 AND `account_id` IN($concession_hreads)";
				  $caquery = $this->db->query($cfsql);				
				  if($caquery->num_rows() >0){
				    $concession_on 	= $caquery->row()->concession_on; 
				    if($discount_percentage >0){
				    $discount_amount = (($concession_on/100) * $discount_percentage);
				    $discount_amount = round($discount_amount,0,PHP_ROUND_HALF_UP);
				    $net_bill_amount = ($bill_amount - $discount_amount);
				    }			   	
				  }
			      }elseif($discount_type ==1 && $discount_amount >0){			   			   
				 $discount_amount = round($discount_amount,0,PHP_ROUND_HALF_UP);
				 $net_bill_amount = ($bill_amount - $discount_amount);   
			      }elseif($discount_type ==2 && $discount_amount >0){			   			   
				 $discount_amount = round($discount_amount,0,PHP_ROUND_HALF_UP);
				 $net_bill_amount = ($bill_amount - $discount_amount);   
			      }else{
				 $net_bill_amount = $bill_amount;  
			      }
			      //===== End Calculate Discount 
			     
			     }// end if total_bill
			   		   
			     $due_amount	= $net_bill_amount;
			     $description	= "Total bill of amount against monthly tution & fee";
			   	   		   
			     $this->RollbackCollection($bill_id);
			   
			     $modified_time = date("Y-m-d H:i:s");
			     $mdata = array(
				'bill_no'  		=>$bill_no,
				'billing_month' 	=>$billing_month,
				'billing_date'		=>$billing_date,
				'credit_period'		=>$credit_period,
				'account_id'   		=>$account_id,
				'admission_id'		=>$admission_id,
				'institute_id'    	=>$institute_id,
				'branch_id'    		=>$branch_id,
				'session_id'    	=>$session_id,
				'version_id'    	=>$version_id,
				'group_id'    		=>$group_id,
				'class_id'    		=>$class_id,
				'shift_id'		=>$shift_id,
				'section_id'    	=>$section_id,
				'bill_amount'    	=>$bill_amount,
				'discount_persent'  	=>$discount_percentage,
				'discount_amount'   	=>$discount_amount,
				'net_bill_amount'  	=>$net_bill_amount,
				'due_amount'  		=>$due_amount,
				'description'  		=>$description,
				'invoice_note1'  	=>$invoice_note1,
				'invoice_note2'  	=>$invoice_note2,
				'due_count'		=>1,
				'process_date' 		=>$process_date,
				'modified_by'  		=>$created_by,
				'modified_time'  	=>$modified_time
			     );
			     $this->db->where('bill_id',$bill_id);
			     $this->db->where('admission_id',$admission_id);
			     $this->db->where('account_id',$account_id);
			     $this->db->where('billing_month',$billing_month);
			     $this->db->update(BILL_MASTER_TBL, $mdata);	
			     $this->AdjustCollection($bill_id); 

			     $contra_id=$this->SaveJV($bill_id,$admission_id,$institute_id,$branch_id,$session_id,$version_id,$group_id,$class_id,$shift_id,$section_id,$account_id,$net_bill_amount,$discount_percentage,$discount_amount,$concession_on,$billing_date,$discount_type,$description);
			     $vsql = "SELECT voucher_no FROM ".VOUCHER_MASTER_TBL." WHERE invoice_no ='".$bill_id."' AND voucher_type = 4 AND status ='1'";
			     $vquery = $this->db->query($vsql);
			     if($vquery->num_rows() >0){
			     $voucher_no= $vquery->row()->voucher_no;
			     }else{ $voucher_no="";}
			     //======== Get Bill Details ========
			     $bdsql = "SELECT * FROM ".BILL_DETAILS_TBL." WHERE institute_id = $institute_id AND branch_id = $branch_id AND session_id = $session_id AND version_id=$version_id 
			   AND class_id=$class_id AND group_id=$group_id AND admission_id=$admission_id AND billing_month =$billing_month AND bill_id=$bill_id";
			
			      $bdquery = $this->db->query($bdsql);	//echo $bdsql; 		
			      foreach($bdquery->result() as $frow){		  			   
				 //======== Save Voucher Details ========
				 if($contra_id >0){
				   $mode_of_payment	= 10; // 10=Others
				   $voucher_type	= 4; // 4=Journal				
				   $account_head 	= $frow->fee_account;
				   $cr_amount    	= $frow->total_price;
				   //==== Save Cr Fee Account =====					
				   $DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
				   $DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Cr','".$account_head."','".$cr_amount."','".$voucher_type."','".$description."','".$created_by."')";
				   $this->db->query($DSQL);
				   $this->SaveAccountLedger($voucher_no,$bill_id,$billing_date,$account_head,$voucher_type,$description,$cr_amount,"Cr","I");				
				
				 }//end if contra_id
			       }//end foreach
	  		       
			  }//end if due qty
			 //===== End Save Process =====
		     } // end if bmcquery->num_rows
		   
		 }//end bill foreach	
	     }// end bill num rows		
	}
	//========== Retrive by Ajax ==========
   	function GetProcessRecordGrid(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');
		$hasEditPM = false;
		$hasDelPM  = false;
		$hasPrintPM= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$srcFrom		=$this->formatDate($this->input->post('srcFrom'));
		$srcTo			=$this->formatDate($this->input->post('srcTo'));
		$billing_month 		=$this->input->post('src-period');
		$admission_id		=$this->input->post('src-admission'); 
		$institute_id		=$this->input->post('src-institute');
		$branch_id		=$this->input->post('src-branch');
		$session_id		=$this->input->post('src-session');
		$version_id		=$this->input->post('src-version');
		$class_id		=$this->input->post('src-class');
		$group_id		=$this->input->post('src-group');
		$shift_id		=$this->input->post('src-shift');
		$section_id		=$this->input->post('src-section');
			   	
		$this->db->select('bl.*,a.admission_no,a.student_photo,a.roll_no,a.present_address,a.permanent_address,a.gender,a.phone,a.mobile,a.email,a.fathers_name,a.discount_type,r.period_name_en as period_name,r.period_year, p.account_name,p.account_id,p.bangla_name,i.company_name,b.branch_name,b.branch_code,g.group_name,se.session_name,v.version_name,c.class_name,sc.section_name,DATE_FORMAT(bl.billing_date ,"%d-%m-%Y") as bill_date',FALSE);
		$this->db->from(BILL_MASTER_TBL." AS bl");		
		$this->db->where("bl.admission_id >", 0);
		$this->db->join(ADMISSION_TBL.' AS a', 'a.admission_id=bl.admission_id','LEFT');
		$this->db->join(PERIOD_TBL.' AS r', 'r.period_no=bl.billing_month','LEFT');
		$this->db->join(ACC_HEAD_TBL.' AS p', 'p.account_id=a.student_name_en','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.institute_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=a.branch_id','LEFT');
	  	$this->db->join(GROUPS_TBL.' AS g', 'g.group_id=a.group_id','LEFT');
	  	$this->db->join(VERSION_TBL.' AS v', 'v.version_id=a.version_id','LEFT');
	  	$this->db->join(SESSION_TBL.' AS se', 'se.sessions_id=a.session_id','LEFT');
	  	$this->db->join(CLASS_TBL.' AS c', 'c.class_id=a.class_id','LEFT');
	  	$this->db->join(SECTION_TBL.' AS sc', 'sc.section_id=a.section_id','LEFT');
		if($this->session->userdata('user_role') >1){
			$this->db->where("bl.institute_id", $this->session->userdata('company_id'));  
		}else{
			if($institute_id >0){
			  $this->db->where("bl.institute_id", $institute_id);  	
			}
		}
		if($this->session->userdata('user_role') >2){
			$this->db->where("bl.branch_id", $this->session->userdata('branch_id'));  
		}else{
			if($branch_id >0){
			  $this->db->where("bl.branch_id", $branch_id);  	
			}			
		}
		if($this->session->userdata('user_role') >4){
			$this->db->where("p.account_id", $this->session->userdata('user_ref_id')); 
		}
		if($billing_month >0){
			$this->db->where("bl.billing_month", $billing_month);  	
		}
		if($session_id >0){
			  $this->db->where("bl.session_id", $session_id);  	
		}
		if($version_id >0){
			  $this->db->where("bl.version_id", $version_id);  	
		}
		if($class_id >0){
			  $this->db->where("bl.class_id", $class_id);  	
		}
		if($group_id >0){
			  $this->db->where("bl.group_id", $group_id);  	
		}
		if($shift_id >0){
			  $this->db->where("bl.shift_id", $shift_id);  	
		}
		if($section_id >0){
			  $this->db->where("bl.section_id", $section_id);  	
		}
		if($admission_id >0){
			  $this->db->where("bl.admission_id", $admission_id);  	
		}
		if($srcFrom!="" && $srcTo==""){
		   $this->db->where("bl.billing_date >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
		   $this->db->where("bl.billing_date <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
		   $this->db->where("bl.billing_date BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}
		$this->db->where('bl.due_amount >', 0);
		$this->db->where('bl.due_count', 1);
		$this->db->group_by('bl.bill_id');
		$this->db->order_by('bl.billing_date,bl.admission_id','DESC');
		$this->db->limit($to,$from);
		$query = $this->db->get(); //print  $this->db->last_query();
		$totalrecord = $this->GetTotalProcessRecord();
	    	$perPage=50; $Pagination="";
	    	if($totalrecord >0){
		   	$Pagination = $this->getPagination($totalrecord,$perPage);
	    	} //print  $this->db->last_query();
		echo 
		'<table width="100%"  border="0" class="table table-responsive table-bordered table-hover custab">
			<thead>
			  <tr class="active">
			  	<th width="2%">'.$this->lang->line("sl").'</th>
				<th width="13%">'.$this->lang->line("billing").' '.$this->lang->line("details").'</th>
			  	<th width="17%">'.$this->lang->line("students").' '.$this->lang->line("details").'</th>
				<th width="18%">'.$this->lang->line("contact").' '.$this->lang->line("address").'</th>
				<th width="12%">'.$this->lang->line("total_amount").'</th>
				<th width="10%">'.$this->lang->line("net_amount").'</th>
				<th width="10%">'.$this->lang->line("paid_amount").'</th>
				<th width="10%">'.$this->lang->line("due_amount").'</th>
				<th width="8%" class="text-center hidden-print">'.$this->lang->line("options").'</th>
			  </tr>
			</thead>';
			  $i=1; if(empty($this->input->post('from'))){ $i=1; }else{ $i= $this->input->post('from')+1;}
			  foreach($query->result() as $row){
			  //if($i%2==0){ $tblrow="success";}else{$tblrow="warning";}
			  echo "<tr class='default'>
			  	<td>".$i."</td>
				<td>
				".$row->bill_no."<br> Date: ".$row->bill_date."<br>Month: ".$row->period_name." - ".$row->period_year."
				</td>
			  	<td>				
				".$row->account_name."<br>AID: ".$row->admission_no."<br>SID: ".$row->account_id.", Roll: ".$row->roll_no."<br>Class: ".$row->class_name.", Sec: ".$row->section_name."				
				</td>
				<td>Pre: ".$row->present_address."<br>Per: ".$row->permanent_address."<br><i class='hidden-print fas fa-mobile'></i> ".$row->mobile."<br><i class='hidden-print fas fa-envelope'></i> ".$row->email."</td>
				<td>".$row->bill_amount;
				if($row->discount_amount >0){
				echo "<br>Les: ".$row->discount_amount;
				}
				echo "</td>
				<td>".$row->net_bill_amount."</td>
				<td>".$row->paid_amount."</td>
				<td>".$row->due_amount."</td>
				<td class='text-center align-middle hidden-print'>";
		    	if($hasEditPM){
				echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Edit'><a class='btn btn-info btn-xs' onclick=editRecord('".$row->bill_id."') id='".$row->bill_id."' href='#'><i class='fas fa-edit'></i></a></span>&nbsp;";
				}
				if($hasDelPM){				
				echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Delete'><a class='btn btn-danger btn-xs' data-toggle='modal' onclick=deleteRecord('".$row->bill_id."') id='".$row->bill_id."' data-target='#deleteModal'><i class='fa fa-trash'></i></a></span>";
				}
				if($hasPrintPM){
				echo "<div class='clearfix'></div><br><span data-toggle='tooltip' data-original-title='View Form'><a class='btn btn-success btn-sm hidden-print' target='_blank' href='".base_url()."billing/ViewBillForm/".$row->bill_id."/".$row->admission_id."'><i class='fa fa-print'></i> Form</a></span>";
				}
			    echo "</td>
			  </tr>";
			  $i++;
			  }
		echo '</table>';
	    	echo "<div class='float-right'>$Pagination</div>";
	}
	function GetTotalProcessRecord(){		
		$srcFrom		=$this->formatDate($this->input->post('srcFrom'));
		$srcTo			=$this->formatDate($this->input->post('srcTo'));
		$billing_month 		=$this->input->post('src-period');
		$admission_id		=$this->input->post('src-admission'); 
		$institute_id		=$this->input->post('src-institute');
		$branch_id		=$this->input->post('src-branch');
		$session_id		=$this->input->post('src-session');
		$version_id		=$this->input->post('src-version');
		$class_id		=$this->input->post('src-class');
		$group_id		=$this->input->post('src-group');
		$shift_id		=$this->input->post('src-shift');
		$section_id		=$this->input->post('src-section');
			   	
		$this->db->select('bl.*,a.admission_no,a.student_photo,a.roll_no,a.present_address,a.permanent_address,a.gender,a.phone,a.mobile,a.email,a.fathers_name,a.discount_type,r.period_name_en as period_name,r.period_year, p.account_name,p.account_id,p.bangla_name,i.company_name,b.branch_name,b.branch_code,g.group_name,se.session_name,v.version_name,c.class_name,sc.section_name,DATE_FORMAT(bl.billing_date ,"%d-%m-%Y") as bill_date',FALSE);
		$this->db->from(BILL_MASTER_TBL." AS bl");		
		$this->db->where("bl.admission_id >", 0);
		$this->db->join(ADMISSION_TBL.' AS a', 'a.admission_id=bl.admission_id','LEFT');
		$this->db->join(PERIOD_TBL.' AS r', 'r.period_no=bl.billing_month','LEFT');
		$this->db->join(ACC_HEAD_TBL.' AS p', 'p.account_id=a.student_name_en','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.institute_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=a.branch_id','LEFT');
	  	$this->db->join(GROUPS_TBL.' AS g', 'g.group_id=a.group_id','LEFT');
	  	$this->db->join(VERSION_TBL.' AS v', 'v.version_id=a.version_id','LEFT');
	  	$this->db->join(SESSION_TBL.' AS se', 'se.sessions_id=a.session_id','LEFT');
	  	$this->db->join(CLASS_TBL.' AS c', 'c.class_id=a.class_id','LEFT');
	  	$this->db->join(SECTION_TBL.' AS sc', 'sc.section_id=a.section_id','LEFT');
		if($this->session->userdata('user_role') >1){
			$this->db->where("bl.institute_id", $this->session->userdata('company_id'));  
		}else{
			if($institute_id >0){
			  $this->db->where("bl.institute_id", $institute_id);  	
			}
		}
		if($this->session->userdata('user_role') >2){
			$this->db->where("bl.branch_id", $this->session->userdata('branch_id'));  
		}else{
			if($branch_id >0){
			  $this->db->where("bl.branch_id", $branch_id);  	
			}			
		}
		if($this->session->userdata('user_role') >4){
			$this->db->where("p.account_id", $this->session->userdata('user_ref_id')); 
		}
		if($billing_month >0){
			$this->db->where("bl.billing_month", $billing_month);  	
		}
		if($session_id >0){
			  $this->db->where("bl.session_id", $session_id);  	
		}
		if($version_id >0){
			  $this->db->where("bl.version_id", $version_id);  	
		}
		if($class_id >0){
			  $this->db->where("bl.class_id", $class_id);  	
		}
		if($group_id >0){
			  $this->db->where("bl.group_id", $group_id);  	
		}
		if($shift_id >0){
			  $this->db->where("bl.shift_id", $shift_id);  	
		}
		if($section_id >0){
			  $this->db->where("bl.section_id", $section_id);  	
		}
		if($admission_id >0){
			  $this->db->where("bl.admission_id", $admission_id);  	
		}
		if($srcFrom!="" && $srcTo==""){
		   $this->db->where("bl.billing_date >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
		   $this->db->where("bl.billing_date <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
		   $this->db->where("bl.billing_date BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}
		$this->db->where('bl.due_amount >', 0);
		$this->db->where('bl.due_count', 1);
		$this->db->group_by('bl.bill_id');
		$this->db->order_by('bl.billing_date,bl.admission_id','DESC');
		$query = $this->db->get();
		if($query->num_rows() >0){
			return $query->num_rows();
		}else{
			return 0;
		}//echo $this->db->last_query();
	}
	//======== End Due Process ============

   	//========== Retrive Records by Ajax ==========
   	function GetRecordGrid(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');
		$hasEditPM = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM  = $this->Site_model->hasOptionPermission($menu_slug,"Delete");
		$hasPrintPM= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$srcFrom		=$this->formatDate($this->input->post('srcFrom'));
		$srcTo			=$this->formatDate($this->input->post('srcTo'));
		$billing_month 		=$this->input->post('src-period');
		$admission_id		=$this->input->post('src-admission'); 
		$institute_id		=$this->input->post('src-institute');
		$branch_id		=$this->input->post('src-branch');
		$session_id		=$this->input->post('src-session');
		$version_id		=$this->input->post('src-version');
		$class_id		=$this->input->post('src-class');
		$group_id		=$this->input->post('src-group');
		$shift_id		=$this->input->post('src-shift');
		$section_id		=$this->input->post('src-section');
		
	   	$from	=$this->input->post('from');
		$to	    =$this->input->post('to');
		if($from==""){ $from=0;} if($to==""){ $to=100;}
		$this->db->select('bl.*,a.admission_no,a.student_photo,a.roll_no,a.present_address,a.permanent_address,a.gender,a.phone,a.mobile,a.email,a.fathers_name,a.discount_type,r.period_name_en as period_name,r.period_year,p.account_name,p.account_id,p.bangla_name,i.company_name,b.branch_name,b.branch_code,g.group_name,se.session_name,v.version_name,c.class_name,sc.section_name,DATE_FORMAT(bl.billing_date ,"%d-%m-%Y") as bill_date,DATE_FORMAT(bl.billing_date ,"%Y") as bill_year',FALSE);
		$this->db->from(BILL_MASTER_TBL." AS bl");
		$this->db->where("bl.admission_id >", 0); 
		$this->db->join(ADMISSION_TBL.' AS a', 'a.admission_id=bl.admission_id','LEFT');
		$this->db->join(PERIOD_TBL.' AS r', 'r.period_no=bl.billing_month','LEFT');
		$this->db->join(ACC_HEAD_TBL.' AS p', 'p.account_id=a.student_name_en','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.institute_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=a.branch_id','LEFT');
	  	$this->db->join(GROUPS_TBL.' AS g', 'g.group_id=a.group_id','LEFT');
	  	$this->db->join(VERSION_TBL.' AS v', 'v.version_id=a.version_id','LEFT');
	  	$this->db->join(SESSION_TBL.' AS se', 'se.sessions_id=a.session_id','LEFT');
	  	$this->db->join(CLASS_TBL.' AS c', 'c.class_id=a.class_id','LEFT');
	  	$this->db->join(SECTION_TBL.' AS sc', 'sc.section_id=a.section_id','LEFT');
		if($this->session->userdata('user_role') >1){
			$this->db->where("bl.institute_id", $this->session->userdata('company_id'));  
		}else{
			if($institute_id >0){
			  $this->db->where("bl.institute_id", $institute_id);  	
			}
		}
		if($this->session->userdata('user_role') >2){
			$this->db->where("bl.branch_id", $this->session->userdata('branch_id'));  
			$this->db->where("r.branch_id", $this->session->userdata('branch_id')); 
		}else{
			if($branch_id >0){
			  $this->db->where("bl.branch_id", $branch_id); 
			  $this->db->where("r.branch_id", $branch_id);  	
			}			
		}
		if($this->session->userdata('user_role') >4){
			$this->db->where("p.account_id", $this->session->userdata('user_ref_id')); 
		}
		if($billing_month >0){
			  $this->db->where("bl.billing_month", $billing_month);  
			  $this->db->where("r.period_no", $billing_month); 	
		}
		if($admission_id >0){
			  $this->db->where("bl.admission_id", $admission_id);  	
		}
		if($session_id >0){
			  $this->db->where("bl.session_id", $session_id);  	
		}
		if($version_id >0){
			  $this->db->where("bl.version_id", $version_id);  	
		}
		if($class_id >0){
			  $this->db->where("bl.class_id", $class_id);  	
		}
		if($group_id >0){
			  $this->db->where("bl.group_id", $group_id);  	
		}
		if($shift_id >0){
			  $this->db->where("bl.shift_id", $shift_id);  	
		}
		if($section_id >0){
			  $this->db->where("bl.section_id", $section_id);  	
		}
		if($srcFrom!="" && $srcTo==""){
		   $this->db->where("bl.billing_date >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
		   $this->db->where("bl.billing_date <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
		   $this->db->where("bl.billing_date BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}
		
		$this->db->group_by('bl.bill_id');
		$this->db->order_by('bl.bill_id','ASC');
		$this->db->limit($to,$from);
		$query = $this->db->get(); //print  $this->db->last_query();
		$totalrecord = $this->GetTotalRecord();
		$perPage=100; $Pagination=""; 
		if($totalrecord >0){
		$Pagination = $this->getPagination($totalrecord,$perPage);
		} //print  $this->db->last_query();
		echo 
		'<table width="100%"  border="0" class="table table-responsive table-bordered table-hover custab">
			<thead>
			  <tr class="active">
			  	<th width="2%">'.$this->lang->line("sl").'</th>
				<th width="13%">'.$this->lang->line("billing").' '.$this->lang->line("details").'</th>
			  	<th width="18%">'.$this->lang->line("students").' '.$this->lang->line("details").'</th>
				<th width="17%">'.$this->lang->line("contact").' '.$this->lang->line("address").'</th>
				<th width="11%">'.$this->lang->line("total_amount").'</th>
				<th width="10%">'.$this->lang->line("net_amount").'</th>
				<th width="11%">'.$this->lang->line("paid_amount").'</th>
				<th width="10%">'.$this->lang->line("due_amount").'</th>
				<th width="8%" class="text-center hidden-print">'.$this->lang->line("options").'</th>
			  </tr>
			</thead>';
			  if($from>0){$i=$from+1;}else{$i=1;} $TotalBill=0; $TotalNetBill=0; $TotalPaid=0; $TotalDue=0; $bill_year="";
			  foreach($query->result() as $row){
			  $sessionArr = explode(" ",$row->session_name);
			  $sessionYearArr = explode("-",$sessionArr[3]);
			  if($row->class_id >13){
				  if($row->billing_month >3){$bill_year=substr($row->bill_year,0,2).$sessionYearArr[0];}else{$bill_year=substr($row->bill_year,0,2).$sessionYearArr[1];}
			  }else{
				 $bill_year = $row->bill_year; 
			  }
			  /*
			  $BArr = explode("/",$row->bill_no);
			  if($i<10){$bill_no=$BArr[0]."/".$BArr[1]."/000$i";}
			  elseif($i>=10 && $i<100){$bill_no=$BArr[0]."/".$BArr[1]."/00$i";}
			  elseif($i>=100 && $i<1000){$bill_no=$BArr[0]."/".$BArr[1]."/0$i";}
			  $bdsql = "UPDATE ".BILL_MASTER_TBL." SET bill_no='".$bill_no."' WHERE bill_id = ".$row->bill_id;
		      $this->db->query($bdsql);
			  */
			  //if($i%2==0){ $tblrow="success";}else{$tblrow="warning";}
			  if($row->paid_amount >0){$disabled="disabled";}else{$disabled="";}
			  $TotalBill+=$row->bill_amount; $TotalNetBill+=$row->net_bill_amount; $TotalPaid+=$row->paid_amount; $TotalDue+=$row->due_amount;
			  echo "<tr class='default'>
			  	<td>".$i."</td>
				<td>
				".$row->bill_no."<br> Date: ".$row->bill_date."<br>Mo: ".$row->period_name." ".$bill_year."
				</td>
			  	<td>
				
				".$row->account_name."<br>AID: ".$row->admission_no."<br>SID: ".$row->account_id.", Roll: ".$row->roll_no."<br>Class: ".$row->class_name.", ".$row->section_name."				
				</td>
				<td>".$row->present_address."<br><i class='hidden-print fas fa-mobile'></i> ".$row->mobile."<br>".$row->email."</td>
				<td>".$row->bill_amount;
				if($row->discount_amount >0){
				echo "<br>Les: ".$row->discount_amount;
				}
				echo "</td>
				<td>".$row->net_bill_amount."</td>
				<td>".$row->paid_amount."</td>
				<td>".$row->due_amount."</td>
				<td class='text-center align-middle hidden-print'>";
		    	if($hasEditPM){
				echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Edit'><a class='btn btn-info btn-xs' onclick=editRecord('".$row->bill_id."') id='".$row->bill_id."' href='#'><i class='fas fa-edit'></i></a></span>&nbsp;";
				}
				if($hasDelPM){				
				echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Delete'><a class='btn btn-danger btn-xs $disabled' data-toggle='modal' onclick=deleteRecord('".$row->bill_id."') id='".$row->bill_id."' data-target='#deleteModal'><i class='fa fa-trash'></i></a></span>";
				}
				if($hasPrintPM){
				echo "<div class='clearfix'></div><br><span data-toggle='tooltip' data-original-title='View Form'><a class='btn btn-success btn-sm' target='_blank' href='".base_url()."billing/ViewBillForm/".$row->bill_id."/".$row->admission_id."'><i class='fa fa-print'></i> Form</a></span>";
				}
			    echo "</td>
			  </tr>";
			  $i++;
			  }			  
			  echo "
			  <tr>
			    <th colspan='4' class='align-middle text-right'>Grand Total(Per Page): </th>
				<th class='text-left'>".number_format($TotalBill, 2, '.', ',')."</th>
				<th>".number_format($TotalNetBill, 2, '.', ',')."</th>
				<th>".number_format($TotalPaid, 2, '.', ',')."</th>
				<th>".number_format($TotalDue, 2, '.', ',')."</th>
				<th>&nbsp;</td>
			  </tr>";
		    echo '</table>';
	    	echo "<div class='float-right'>$Pagination</div>";
	}
        //========== Retrive by Ajax ==========
   	function GetGenerateRecordGrid(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');
		$hasEditPM = false;
		$hasDelPM  = false;
		$hasPrintPM= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$srcFrom		=$this->formatDate($this->input->post('srcFrom'));
		$srcTo			=$this->formatDate($this->input->post('srcTo'));
		$billing_month 		=$this->input->post('src-period');
		$admission_id		=$this->input->post('src-admission'); 
		$institute_id		=$this->input->post('src-institute');
		$branch_id		=$this->input->post('src-branch');
		$session_id		=$this->input->post('src-session');
		$version_id		=$this->input->post('src-version');
		$class_id		=$this->input->post('src-class');
		$group_id		=$this->input->post('src-group');
		$shift_id		=$this->input->post('src-shift');
		$section_id		=$this->input->post('src-section');
		
	   	$from	=$this->input->post('from');
		$to	=$this->input->post('to');
		if($from==""){ $from=0;} if($to==""){ $to=50;}
		$this->db->select('bl.*,a.admission_no,a.student_photo,a.roll_no,a.present_address,a.permanent_address,a.gender,a.phone,a.mobile,a.email,a.fathers_name,a.discount_type,r.period_name_en as period_name,r.period_year, p.account_name,p.account_id,p.bangla_name,i.company_name,b.branch_name,b.branch_code,g.group_name,se.session_name,v.version_name,c.class_name,sc.section_name,DATE_FORMAT(bl.billing_date ,"%d-%m-%Y") as bill_date',FALSE);
		$this->db->from(BILL_MASTER_TBL." AS bl");		
		$this->db->where("bl.admission_id >", 0);
		$this->db->join(ADMISSION_TBL.' AS a', 'a.admission_id=bl.admission_id','LEFT');
		$this->db->join(PERIOD_TBL.' AS r', 'r.period_no=bl.billing_month','LEFT');
		$this->db->join(ACC_HEAD_TBL.' AS p', 'p.account_id=a.student_name_en','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.institute_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=a.branch_id','LEFT');
	  	$this->db->join(GROUPS_TBL.' AS g', 'g.group_id=a.group_id','LEFT');
	  	$this->db->join(VERSION_TBL.' AS v', 'v.version_id=a.version_id','LEFT');
	  	$this->db->join(SESSION_TBL.' AS se', 'se.sessions_id=a.session_id','LEFT');
	  	$this->db->join(CLASS_TBL.' AS c', 'c.class_id=a.class_id','LEFT');
	  	$this->db->join(SECTION_TBL.' AS sc', 'sc.section_id=a.section_id','LEFT');
		if($this->session->userdata('user_role') >1){
			$this->db->where("bl.institute_id", $this->session->userdata('company_id'));  
		}else{
			if($institute_id >0){
			  $this->db->where("bl.institute_id", $institute_id);  	
			}
		}
		if($this->session->userdata('user_role') >2){
			$this->db->where("bl.branch_id", $this->session->userdata('branch_id'));  
		}else{
			if($branch_id >0){
			  $this->db->where("bl.branch_id", $branch_id);  	
			}			
		}
		if($this->session->userdata('user_role') >4){
			$this->db->where("p.account_id", $this->session->userdata('user_ref_id')); 
		}
		if($billing_month >0){
			  $this->db->where("bl.billing_month", $billing_month);  	
		}
		if($admission_id >0){
			  $this->db->where("bl.admission_id", $admission_id);  	
		}
		if($session_id >0){
			  $this->db->where("bl.session_id", $session_id);  	
		}
		if($version_id >0){
			  $this->db->where("bl.version_id", $version_id);  	
		}
		if($class_id >0){
			  $this->db->where("bl.class_id", $class_id);  	
		}
		if($group_id >0){
			  $this->db->where("bl.group_id", $group_id);  	
		}
		if($shift_id >0){
			  $this->db->where("bl.shift_id", $shift_id);  	
		}
		if($section_id >0){
			  $this->db->where("bl.section_id", $section_id);  	
		}
		if($srcFrom!="" && $srcTo==""){
		   $this->db->where("bl.billing_date >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
		   $this->db->where("bl.billing_date <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
		   $this->db->where("bl.billing_date BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}
		$this->db->group_by('bl.bill_id');
		$this->db->order_by('bl.bill_id','ASC');
		$this->db->limit($to,$from);
		$query = $this->db->get(); //print  $this->db->last_query();
		$totalrecord = $this->GetTotalRecord();
	    	$perPage=50; $Pagination="";
	    	if($totalrecord >0){
		   	$Pagination = $this->getPagination($totalrecord,$perPage);
	    	} //print  $this->db->last_query();
		echo 
		'<table width="100%"  border="0" class="table table-responsive table-bordered table-hover custab">
			<thead>
			  <tr class="active">
			  	<th width="2%">'.$this->lang->line("sl").'</th>
				<th width="13%">'.$this->lang->line("billing").' '.$this->lang->line("details").'</th>
			  	<th width="17%">'.$this->lang->line("students").' '.$this->lang->line("details").'</th>
				<th width="18%">'.$this->lang->line("contact").' '.$this->lang->line("address").'</th>
				<th width="12%">'.$this->lang->line("total_amount").'</th>
				<th width="10%">'.$this->lang->line("net_amount").'</th>
				<th width="10%">'.$this->lang->line("paid_amount").'</th>
				<th width="10%">'.$this->lang->line("due_amount").'</th>
				<th width="8%" class="text-center hidden-print">'.$this->lang->line("options").'</th>
			  </tr>
			</thead>';
			  $i=1; if(empty($this->input->post('from'))){ $i=1; }else{ $i= $this->input->post('from')+1;}
			  foreach($query->result() as $row){
			  //if($i%2==0){ $tblrow="success";}else{$tblrow="warning";}
			  echo "<tr class='default'>
			  	<td>".$i."</td>
				<td>
				".$row->bill_no."<br> Date: ".$row->bill_date."<br>Month: ".$row->period_name." - ".$row->period_year."
				</td>
			  	<td>				
				".$row->account_name."<br>AID: ".$row->admission_no."<br>SID: ".$row->account_id.", Roll: ".$row->roll_no."<br>Class: ".$row->class_name.", Sec: ".$row->section_name."				
				</td>
				<td>Pre: ".$row->present_address."<br>Per: ".$row->permanent_address."<br><i class='hidden-print fas fa-mobile'></i> ".$row->mobile."<br><i class='hidden-print fas fa-envelope'></i> ".$row->email."</td>
				<td>".$row->bill_amount;
				if($row->discount_amount >0){
				echo "<br>Les: ".$row->discount_amount;
				}
				echo "</td>
				<td>".$row->net_bill_amount."</td>
				<td>".$row->paid_amount."</td>
				<td>".$row->due_amount."</td>
				<td class='text-center align-middle hidden-print'>";
		    	if($hasEditPM){
				echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Edit'><a class='btn btn-info btn-xs' onclick=editRecord('".$row->bill_id."') id='".$row->bill_id."' href='#'><i class='fas fa-edit'></i></a></span>&nbsp;";
				}
				if($hasDelPM){				
				echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Delete'><a class='btn btn-danger btn-xs' data-toggle='modal' onclick=deleteRecord('".$row->bill_id."') id='".$row->bill_id."' data-target='#deleteModal'><i class='fa fa-trash'></i></a></span>";
				}
				if($hasPrintPM){
				echo "<div class='clearfix'></div><br><span data-toggle='tooltip' data-original-title='View Form'><a class='btn btn-success btn-sm hidden-print' target='_blank' href='".base_url()."billing/ViewBillForm/".$row->bill_id."/".$row->admission_id."'><i class='fa fa-print'></i> Form</a></span>";
				}
			    echo "</td>
			  </tr>";
			  $i++;
			  }
		echo '</table>';
	    	echo "<div class='float-right'>$Pagination</div>";
	}
	function GetTotalRecord(){		
		$srcFrom			=$this->formatDate($this->input->post('srcFrom'));
		$srcTo				=$this->formatDate($this->input->post('srcTo'));
		$billing_month 		=$this->input->post('src-period');
		$admission_id		=$this->input->post('src-admission'); 
		$institute_id		=$this->input->post('src-institute');
		$branch_id			=$this->input->post('src-branch');
		$session_id			=$this->input->post('src-session');
		$version_id			=$this->input->post('src-version');
		$class_id			=$this->input->post('src-class');
		$group_id			=$this->input->post('src-group');
		$shift_id			=$this->input->post('src-shift');
		$section_id			=$this->input->post('src-section');
		
		$this->db->select('bl.*',FALSE);
		$this->db->from(BILL_MASTER_TBL." AS bl");
		$this->db->where("bl.admission_id >", 0);
		$this->db->join(ADMISSION_TBL.' AS a', 'a.admission_id=bl.admission_id','LEFT');
		$this->db->join(PERIOD_TBL.' AS r', 'r.period_no=bl.billing_month','LEFT');
		$this->db->join(ACC_HEAD_TBL.' AS p', 'p.account_id=a.student_name_en','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.institute_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=a.branch_id','LEFT');
	  	$this->db->join(GROUPS_TBL.' AS g', 'g.group_id=a.group_id','LEFT');
	  	$this->db->join(VERSION_TBL.' AS v', 'v.version_id=a.version_id','LEFT');
	  	$this->db->join(SESSION_TBL.' AS se', 'se.sessions_id=a.session_id','LEFT');
	  	$this->db->join(CLASS_TBL.' AS c', 'c.class_id=a.class_id','LEFT');
	  	$this->db->join(SECTION_TBL.' AS sc', 'sc.section_id=a.section_id','LEFT');
		if($this->session->userdata('user_role') >1){
			$this->db->where("bl.institute_id", $this->session->userdata('company_id'));  
		}else{
			if($institute_id >0){
			  $this->db->where("bl.institute_id", $institute_id);  	
			}
		}
		if($this->session->userdata('user_role') >2){
			$this->db->where("bl.branch_id", $this->session->userdata('branch_id'));  
		}else{
			if($branch_id >0){
			  $this->db->where("bl.branch_id", $branch_id);  	
			}			
		}
		if($this->session->userdata('user_role') >4){
			$this->db->where("p.account_id", $this->session->userdata('user_ref_id')); 
		}
		if($billing_month >0){
			  $this->db->where("bl.billing_month", $billing_month);  	
		}
		if($admission_id >0){
			  $this->db->where("bl.admission_id", $admission_id);  	
		}
		if($session_id >0){
			  $this->db->where("bl.session_id", $session_id);  	
		}
		if($version_id >0){
			  $this->db->where("bl.version_id", $version_id);  	
		}
		if($class_id >0){
			  $this->db->where("bl.class_id", $class_id);  	
		}
		if($group_id >0){
			  $this->db->where("bl.group_id", $group_id);  	
		}
		if($shift_id >0){
			  $this->db->where("bl.shift_id", $shift_id);  	
		}
		if($section_id >0){
			  $this->db->where("bl.section_id", $section_id);  	
		}
		if($srcFrom!="" && $srcTo==""){
		   $this->db->where("bl.billing_date >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
		   $this->db->where("bl.billing_date <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
		   $this->db->where("bl.billing_date BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}
		
		$this->db->group_by('bl.bill_id');
		$this->db->order_by('bl.bill_id','ASC');
		$query = $this->db->get();
		if($query->num_rows() >0){
			return $query->num_rows();
		}else{
			return 0;
		}//echo $this->db->last_query();
	}
	
	function DelRecord(){
		$id =$this->input->post('id');
		$this->db->select('*',FALSE);
		$this->db->from(BILL_MASTER_TBL);
		$this->db->where("admission_id >", 0);
		$this->db->where("paid_amount >", 0);
		$this->db->where('bill_id',$id);
		if($this->session->userdata('user_role') >1){
			$this->db->where("institute_id", $this->session->userdata('company_id'));  
		}else{
			if($institute_id >0){
			  $this->db->where("institute_id", $institute_id);  	
			}
		}		
		$this->db->group_by('bill_id');
		$this->db->order_by('bill_id','ASC');
		$query = $this->db->get();
		if($query->num_rows() >0){	
			$this->RollbackCollection($bill_id);
		}
		
		$this->db->where('bill_id',$id);
		$this->db->delete(BILL_MASTER_TBL);
		$this->db->where('bill_id',$id);
		$this->db->delete(BILL_DETAILS_TBL);
		$this->db->where('invoice_no',$id);
		$this->db->delete(VOUCHER_MASTER_TBL);
		$this->db->where('invoice_no',$id);
		$this->db->delete(VOUCHER_DETAILS_TBL);
	}
	
	function deleteRow(){
		$id =$this->input->post('id');
		$this->db->where('details_id',$id);
		$this->db->delete(BILL_DETAILS_TBL);
	}
	function FillDetails(){
		$details_id	=$this->input->post('id');
		$this->db->select('*');
		$this->db->from(BILL_DETAILS_TBL);
		$this->db->where('details_id', $details_id);
		$query = $this->db->get(); //echo $this->db->last_query();
		return $query->row();
	}
	
	function FillRecord(){
		$bill_id	=$this->input->post('id');
		$this->db->select('*');
		$this->db->from(BILL_MASTER_TBL);
		$this->db->where('bill_id', $bill_id);
		$query = $this->db->get(); //echo $this->db->last_query();
		return $query->row();
	}
        //====== Start Salary Bill List =======
   	function GetSalaryBillRecordGrid(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');
		$hasEditPM 	= $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM  	= $this->Site_model->hasOptionPermission($menu_slug,"Delete");
		$hasPrintPM	= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$srcFrom			=$this->formatDate($this->input->post('srcFrom'));
		$srcTo				=$this->formatDate($this->input->post('srcTo'));
		$billing_month 		=$this->input->post('src-period');
		$employee_id		=$this->input->post('src-employee'); 
		$institute_id		=$this->input->post('src-institute');
		$branch_id			=$this->input->post('src-branch');
		$session_id			=$this->input->post('src-session');
		$version_id			=$this->input->post('src-version');
		$shift_id			=$this->input->post('src-shift');
		
	   	$from	=$this->input->post('from');
		$to	=$this->input->post('to');
		if($from==""){ $from=0;} if($to==""){ $to=50;}
		$this->db->select('bl.*,a.employee_code,a.address as present_address,a.permanent_address,a.designation,a.fathers_name,a.mothers_name,p.phone,p.mobile,p.email,r.period_name_en as period_name,r.period_year,p.account_name,p.account_id,p.bangla_name,i.company_name,b.branch_name,b.branch_code,se.session_name,v.version_name,DATE_FORMAT(bl.billing_date ,"%d-%m-%Y") as bill_date',FALSE);
		$this->db->from(BILL_MASTER_TBL." AS bl");
		$this->db->where("bl.bill_type", 3); 
		$this->db->join(EMPLOYEE_TBL.' AS a', 'a.employee_id=bl.account_id','LEFT');
		$this->db->join(PERIOD_TBL.' AS r', 'r.period_no=bl.billing_month','LEFT');
		$this->db->join(ACC_HEAD_TBL.' AS p', 'p.account_id=a.employee_id','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.company_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=a.branch_id','LEFT');
	  	$this->db->join(VERSION_TBL.' AS v', 'v.version_id=bl.version_id','LEFT');
	  	$this->db->join(SESSION_TBL.' AS se', 'se.sessions_id=bl.session_id','LEFT');
		if($this->session->userdata('user_role') >1){
			$this->db->where("bl.institute_id", $this->session->userdata('company_id'));  
		}else{
			if($institute_id >0){
			  $this->db->where("bl.institute_id", $institute_id);  	
			}
		}
		if($this->session->userdata('user_role') >2){
			$this->db->where("bl.branch_id", $this->session->userdata('branch_id'));  
		}else{
			if($branch_id >0){
			  $this->db->where("bl.branch_id", $branch_id);  	
			}			
		}
		if($this->session->userdata('user_role') >4){
			$this->db->where("p.account_id", $this->session->userdata('user_ref_id')); 
		}
		if($billing_month >0){
			  $this->db->where("bl.billing_month", $billing_month);  	
		}
		if($employee_id >0){
			  $this->db->where("bl.employee_id", $employee_id);  	
		}
		if($session_id >0){
			  $this->db->where("bl.session_id", $session_id);  	
		}
		if($version_id >0){
			  $this->db->where("bl.version_id", $version_id);  	
		}
		if($shift_id >0){
			  $this->db->where("bl.shift_id", $shift_id);  	
		}
		if($srcFrom!="" && $srcTo==""){
		   $this->db->where("bl.billing_date >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
		   $this->db->where("bl.billing_date <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
		   $this->db->where("bl.billing_date BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}
		
		$this->db->group_by('bl.bill_id');
		$this->db->order_by('bl.bill_id','ASC');
		$this->db->limit($to,$from);
		$query = $this->db->get(); //print  $this->db->last_query();
		$totalrecord = $this->GetTotalSalaryBillRecordGrid();
	    	$perPage=50; $Pagination="";
	    	if($totalrecord >0){
		   	$Pagination = $this->getPagination($totalrecord,$perPage);
	    	} //print  $this->db->last_query();
		echo 
		'<table width="100%"  border="0" class="table table-responsive table-bordered table-hover custab">
			<thead>
			  <tr class="active">
			  	<th width="2%">'.$this->lang->line("sl").'</th>
				<th width="13%">'.$this->lang->line("billing").' '.$this->lang->line("details").'</th>
			  	<th width="17%">'.$this->lang->line("employee").' '.$this->lang->line("details").'</th>
				<th width="18%">'.$this->lang->line("contact").' '.$this->lang->line("address").'</th>
				<th width="12%">'.$this->lang->line("total_amount").'</th>
				<th width="10%">'.$this->lang->line("net_amount").'</th>
				<th width="10%">'.$this->lang->line("paid_amount").'</th>
				<th width="10%">'.$this->lang->line("due_amount").'</th>
				<th width="8%" class="text-center hidden-print">'.$this->lang->line("options").'</th>
			  </tr>
			</thead>';
			  $i=1; if(empty($this->input->post('from'))){ $i=1; }else{ $i= $this->input->post('from')+1;}
			  foreach($query->result() as $row){
			  //if($i%2==0){ $tblrow="success";}else{$tblrow="warning";}
			  echo "<tr class='default'>
			  	<td>".$i."</td>
				<td>
				".$row->bill_no."<br> Date: ".$row->bill_date."<br>Month: ".$row->period_name." - ".$row->period_year."
				</td>
			  	<td>				
				".$row->account_name."<br>".$row->designation."<br>ID: ".$row->employee_code."				
				</td>
				<td>Pre: ".$row->present_address."<br>Per: ".$row->permanent_address."<br><i class='hidden-print fas fa-mobile'></i> ".$row->mobile."<br><i class='hidden-print fas fa-envelope'></i> ".$row->email."</td>
				<td>".$row->bill_amount;
				if($row->discount_amount >0){
				echo "<br>Les: ".$row->discount_amount;
				}
				echo "</td>
				<td>".$row->net_bill_amount."</td>
				<td>".$row->paid_amount."</td>
				<td>".$row->due_amount."</td>
				<td class='text-center align-middle hidden-print'>";
		    	if($hasEditPM){
				echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Edit'><a class='btn btn-info btn-xs disabled' onclick=editRecord('".$row->bill_id."') id='".$row->bill_id."' href='#'><i class='fas fa-edit'></i></a></span>&nbsp;";
				}
				if($hasDelPM){				
				echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Delete'><a class='btn btn-danger btn-xs' data-toggle='modal' onclick=deleteRecord('".$row->bill_id."') id='".$row->bill_id."' data-target='#deleteModal'><i class='fa fa-trash'></i></a></span>";
				}
				if($hasPrintPM){
				echo "<div class='clearfix'></div><br><span data-toggle='tooltip' data-original-title='View Form'><a class='btn btn-success btn-sm' target='_blank' href='".base_url()."salary_bill/ViewBillForm/".$row->bill_id."/".$row->account_id."'><i class='fa fa-print'></i> Form</a></span>";
				}
			    echo "</td>
			  </tr>";
			  $i++;
			  }
		echo '</table>';
	    echo "<div class='float-right'>$Pagination</div>";
	}
	function GetTotalSalaryBillRecordGrid(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');
		$hasEditPM = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM  = $this->Site_model->hasOptionPermission($menu_slug,"Delete");
		$hasPrintPM= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$srcFrom			=$this->formatDate($this->input->post('srcFrom'));
		$srcTo				=$this->formatDate($this->input->post('srcTo'));
		$billing_month 		=$this->input->post('src-period');
		$employee_id		=$this->input->post('src-employee'); 
		$institute_id		=$this->input->post('src-institute');
		$branch_id			=$this->input->post('src-branch');
		$session_id			=$this->input->post('src-session');
		$version_id			=$this->input->post('src-version');
		$shift_id			=$this->input->post('src-shift');
		
		$this->db->select('bl.*,a.employee_code,a.address as present_address,a.permanent_address,a.designation,a.fathers_name,a.mothers_name,p.phone,p.mobile,p.email,r.period_name_en as period_name,r.period_year,p.account_name,p.account_id,p.bangla_name,i.company_name,b.branch_name,b.branch_code,se.session_name,v.version_name,DATE_FORMAT(bl.billing_date ,"%d-%m-%Y") as bill_date',FALSE);
		$this->db->from(BILL_MASTER_TBL." AS bl");
		$this->db->where("bl.bill_type", 3); 
		$this->db->join(EMPLOYEE_TBL.' AS a', 'a.employee_id=bl.account_id','LEFT');
		$this->db->join(PERIOD_TBL.' AS r', 'r.period_no=bl.billing_month','LEFT');
		$this->db->join(ACC_HEAD_TBL.' AS p', 'p.account_id=a.employee_id','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.company_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=a.branch_id','LEFT');
	  	$this->db->join(VERSION_TBL.' AS v', 'v.version_id=bl.version_id','LEFT');
	  	$this->db->join(SESSION_TBL.' AS se', 'se.sessions_id=bl.session_id','LEFT');
		if($this->session->userdata('user_role') >1){
			$this->db->where("bl.institute_id", $this->session->userdata('company_id'));  
		}else{
			if($institute_id >0){
			  $this->db->where("bl.institute_id", $institute_id);  	
			}
		}
		if($this->session->userdata('user_role') >2){
			$this->db->where("bl.branch_id", $this->session->userdata('branch_id'));  
		}else{
			if($branch_id >0){
			  $this->db->where("bl.branch_id", $branch_id);  	
			}			
		}
		if($this->session->userdata('user_role') >4){
			$this->db->where("p.account_id", $this->session->userdata('user_ref_id')); 
		}
		if($billing_month >0){
			  $this->db->where("bl.billing_month", $billing_month);  	
		}
		if($employee_id >0){
			  $this->db->where("bl.employee_id", $employee_id);  	
		}
		if($session_id >0){
			  $this->db->where("bl.session_id", $session_id);  	
		}
		if($version_id >0){
			  $this->db->where("bl.version_id", $version_id);  	
		}
		if($shift_id >0){
			  $this->db->where("bl.shift_id", $shift_id);  	
		}
		if($srcFrom!="" && $srcTo==""){
		   $this->db->where("bl.billing_date >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
		   $this->db->where("bl.billing_date <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
		   $this->db->where("bl.billing_date BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}
		
		$this->db->group_by('bl.bill_id');
		$this->db->order_by('bl.bill_id','ASC');
		$query = $this->db->get();
		if($query->num_rows() >0){
			return $query->num_rows();
		}else{
			return 0;
		}//echo $this->db->last_query();
	}
    function getPagination($totalrecord, $block)
    {
        $from_rs = $this->input->post('from');
        if ($from_rs == "") {
            $from_rs = 0;
        }
        if ($block == "") {
            $block = 12;
        }
        $to_rs = $from_rs + $block;
        if ($from_rs >= $block) {
            $from_rs = $from_rs + 1;
        }
        if ($from_rs == "" || $from_rs == 0) {
            $from_rs = 1;
        }
        if ($to_rs == "" || $totalrecord < $block) {
            $to_rs = $totalrecord;
        } else if ($to_rs == "" && $totalrecord > $block) {
            $to_rs = $block;
        }
        if ($to_rs > $totalrecord) {
            $to_rs = $totalrecord;
        }
        if ($totalrecord == 0) {
            $from_rs = 0;
        }

        $plink = $this->input->post('page_no');
        if ($plink == "") {
            $plink = 1;
        }
        if ($totalrecord > $block) {
            $res = $totalrecord / $block;
            $res = (int)$res;
            if (($totalrecord % $block) != 0) {
                $totalpage = $res + 1;
            } else {
                $totalpage = $res;
            }
        } else {
            $totalpage = 1;
        }
        $paginationStr = "";
        $paginationStr .= "<ul class='pagination pagination-sm m-0'>";

        if ($totalrecord > $block) {
            $two = $this->input->post('from');
            if ($two == "") {
                $two = 0;
            }
            $pno = $this->input->post('page_no');
            if ($pno == "") {
                $pno = 0;
            }
            $pno = $pno - 1;
            $frm = $two - $block;
            $to = $block;
            if ($pno <= $totalpage && $pno > 0) {
                $paginationStr .= "<li class='page-item'><a class='page-link' onclick=nextPage($frm,$to,$pno) href='#'>&laquo;</a></li>";
            }
        } else {
            $paginationStr .= "<li class='page-item disabled'><a class='page-link' href='#'>&laquo;</a></li>";
        }
        if ($totalpage >= 1) {
            $i = 1;
            $from = 0;
            $to = $block;
            while ($i <= $totalpage) {
                if ($from == 0) {
                    $paginationStr .= "<li class='page-item'>";
                    $paginationStr .= "<a ";
					if ($i == $plink) {
                        $paginationStr .= "class='active-link'";
                    }else{
						$paginationStr .= "class='page-link'";
					}
					$paginationStr .= " onclick=nextPage($from,$to,$i) href='#'>$i</a></li>";
                } else {
                    $paginationStr .= "<li class='page-item'>";
                    $paginationStr .= "<a ";
					if ($i == $plink) {
                        $paginationStr .= "class='active-link'";
                    }else{
						$paginationStr .= "class='page-link'";
					}
					$paginationStr .= " onclick=nextPage($from,$to,$i) href='#'>$i</a></li>";
                }
                $i++;
                $from = $from + $block;
                if ($to > $totalrecord) {
                    $to = $totalrecord;
                }
            }
        }
        if ($totalrecord > $block) {
            $f = $this->input->post('from');
            $page = $this->input->post('page_no');
            $page = $page + 1;
            if ($f == "" || $f == 0) {
                $f = $block;
                $page = 2;
            } else {
                $f = $f + $block;
            }
            $t = $block;
            if ($t > $totalrecord) {
                $t = $totalrecord;
            }
            if ($page <= $totalpage) {
                $paginationStr .= "<li class='page-item'><a class='page-link' onclick=nextPage($f,$t,$page) href='#'>&raquo;</a></li>";
            }
        } else {
            $paginationStr .= "<li class='page-item disabled'><a class='page-link' href='#'>&raquo;</a></li>";
        }

        $paginationStr .= "</ul>";
        return $paginationStr;
    }

    function formatDate($dt)
    {
	if (trim($dt) !="") {
		$day = substr($dt, 0, 2);
		$month = substr($dt, 3, 2);
		$year = substr($dt, 6, 4);
		$hour = substr($dt, 11, 2);
		$minute = substr($dt, 14, 2);
		$second = substr($dt, 17, 2);
		$ampm = substr($dt, 20, 2);
		//echo $ampm;
		if ($hour == '' AND $minute == '' AND $second == '') {
			return $year . "-" . $month . "-" . $day;
		} else {
			if (strtoupper($ampm) == 'PM') {
				$hour = intval($hour) + 12;
				return $year . "-" . $month . "-" . $day . ' ' . $hour . ':' . $minute . ':' . $second;
			} else {
				return $year . "-" . $month . "-" . $day . ' ' . $hour . ':' . $minute . ':' . $second;
			}
		}
	}
    }

    function formatDateTimeDMY($dt)
    {
	if (trim($dt)) {
		$year = substr($dt, 0, 4);
		$month = substr($dt, 5, 2);
		$day = substr($dt, 8, 2);
		$hour = substr($dt, 11, 2);
		$minute = substr($dt, 14, 2);
		$second = substr($dt, 17, 2);
		$ampm = substr($dt, 20, 2);
		if ($hour == '' AND $minute == '' AND $second == '') {
			return $year . "-" . $month . "-" . $day;
		} else {
			if (strtoupper($ampm) == 'PM') {
				$hour = intval($hour) + 12;
				return $day . "-" . $month . "-" . $year . ' ' . $hour . ':' . $minute . ':' . $second;
			} else {
				return $day . "-" . $month . "-" . $year . ' ' . $hour . ':' . $minute . ':' . $second;
			}
		}
	}
    }
    function formatDateDMY($val)
    {
	if ($val) {
		$yy = substr($val, 0, 4);
		$mm = substr($val, 5, 2);
		$dd = substr($val, 8, 2);
		return $dd . '-' . $mm . '-' . $yy;
	}
    }
    function dateInputFormatDMY($val)
    {
		if ($val) {
			$yy = substr($val, 0, 4);
			$mm = substr($val, 5, 2);
			$dd = substr($val, 8, 2);
			return $dd . '-' . $mm . '-' . $yy;
		}
    }
}

