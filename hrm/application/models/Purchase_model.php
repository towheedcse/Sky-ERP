<?php 
class Purchase_model extends CI_Model {
		
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
		$inventory_type		=1; // 0=N0, 1=Purchase In, 2=Physical In, 3=Sales Out, 4=Physical Out
		$bill_date			=$this->formatDate($this->input->post('bill-date'));
		$billing_month 		=$this->input->post('bill-period'); 
		$institute_id		=$this->input->post('institute_id');
		$branch_id			=$this->input->post('branch_id');
		$session_id			=$this->input->post('session_id');
		$store_id			=$this->input->post('store_id');
		$supplier_id		=$this->input->post('supplier_id');
		$particulars_id		=$this->input->post('particulars-id');
		$quantity			= $this->input->post('quantity');
		$free_qty			= $this->input->post('free_qty');
		$unit_price			= $this->input->post('unit_price');
		$total_price		= ($quantity * $unit_price);
		$total_price 		= round($total_price,0,PHP_ROUND_HALF_UP);
		$remarks			= $this->input->post('remarks');			
    	$created_by			= $this->session->userdata('created_by');
									
		$ddata = array(
		'bill_id'  			=>$bill_id,
		'billing_month' 	=>$billing_month,
		'billing_date'		=>$bill_date,
		'fee_account'		=>$particulars_id,
		'account_id'   		=>$supplier_id,
		'institute_id'    	=>$institute_id,
		'branch_id'    		=>$branch_id,
		'session_id'    	=>$session_id,
		'store_id'    		=>$store_id,
		'inventory_type'    =>$inventory_type,
		'quantity'    		=>$quantity,
		'free_qty'    		=>$free_qty,
		'unit_price'    	=>$unit_price,
		'total_price'  		=>$total_price,
		'remarks'  			=>$remarks,
		'status'  			=>$status,
		'created_by'  		=>$created_by
	    );
	   	   
		if($supplier_id >0 && $bill_date!="" && $quantity >0){
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
		$inventory_type		=1; // 0=N0, 1=Purchase In, 2=Physical In, 3=Sales Out, 4=Physical Out
		$bill_date			=$this->formatDate($this->input->post('bill-date'));
		$billing_month 		=$this->input->post('bill-period'); 
		$institute_id		=$this->input->post('institute_id');
		$branch_id			=$this->input->post('branch_id');
		$session_id			=$this->input->post('session_id');
		$store_id			=$this->input->post('store_id');
		$supplier_id		=$this->input->post('supplier_id');
		$particulars_id		=$this->input->post('particulars-id');
		$quantity			= $this->input->post('quantity');
		$free_qty			= $this->input->post('free_qty');
		$unit_price			= $this->input->post('unit_price');
		$total_price		= ($quantity * $unit_price);
		$total_price 		= round($total_price,0,PHP_ROUND_HALF_UP);
		$remarks			= $this->input->post('remarks');			
    	$modified_by		= $this->session->userdata('created_by');
		$modified_time  	= date("Y-m-d H:i:s");		
						
		$ddata = array(
		'bill_id'  			=>$bill_id,
		'billing_month' 	=>$billing_month,
		'billing_date'		=>$bill_date,
		'fee_account'		=>$particulars_id,
		'account_id'   		=>$supplier_id,
		'institute_id'    	=>$institute_id,
		'branch_id'    		=>$branch_id,
		'session_id'    	=>$session_id,
		'store_id'    		=>$store_id,
		'inventory_type'    =>$inventory_type,
		'quantity'    		=>$quantity,
		'free_qty'    		=>$free_qty,
		'unit_price'    	=>$unit_price,
		'total_price'  		=>$total_price,
		'remarks'  			=>$remarks,
		'status'  			=>$status,
		'modified_by'  		=>$modified_by,
		'modified_time'		=>$modified_time
	    );
	    $this->db->where('bill_id',$bill_id);
		$this->db->where('account_id',$supplier_id);
		$this->db->where('details_id',$details_id);
		$this->db->update(BILL_DETAILS_TBL, $ddata);
       //print  $this->db->last_query();
	}
		
	function saveBillMaster($bill_id){
		$bill_date			=$this->formatDate($this->input->post('bill-date'));
		$billing_month 		=$this->input->post('bill-period');
		$institute_id		=$this->input->post('institute_id');
		$branch_id			=$this->input->post('branch_id');
		$session_id			=$this->input->post('session_id');		
		$supplier_id		=$this->input->post('supplier_id');
		$account_id			=$this->input->post('supplier_id');
		$store_id			=$this->input->post('store_id');
		
		$total_bill			= $this->input->post('total_bill');
		$discount_percentage= $this->input->post('discount_percentage');
		$discount_amount	= $this->input->post('discount_amount');	
		$vat_percentage		= $this->input->post('vat_percentage');	
		$vat_amount			= $this->input->post('vat_amount');
		$invoice_note1		= $this->input->post('invoice_note1');
		$invoice_note2		= $this->input->post('invoice_note2');		
		
		if(empty($discount_percentage)){$discount_percentage=0;} if(empty($discount_amount)){$discount_amount=0;}
		if($discount_percentage >0 && $discount_amount==0){
		   $discount_amount = (($total_bill/100) * $discount_percentage);
		}
		$sub_total			= ($total_bill - $discount_amount);
		if(empty($vat_percentage)){$vat_percentage=0;} if(empty($vat_amount)){$vat_amount=0;}
		if($vat_percentage >0 && $vat_amount==0){
		   $vat_amount = (($sub_total/100) * $vat_percentage);
		}
		$net_bill_amount 	= ($sub_total + $vat_amount);
		$net_bill_amount 	= round($net_bill_amount,0,PHP_ROUND_HALF_UP);
		$bill_type			= 4; // 4=Purchase
		$created_by			= $this->session->userdata('created_by');
		if($this->input->post('description')!=""){
			$description 	= str_replace("U 0026", '&', $this->input->post('description'));
		}else{
			$description	= "Total bill of payable amount against purchase item";
		}
		$description	= $this->db->escape_str($description);		
		if($total_bill >0){		   
		   $due_amount	= $net_bill_amount;  
		   	   
		   if($bill_id >0){
			    $this->RollbackCollection($bill_id);
						
				$BSSQL= "SELECT * FROM ".BILL_MASTER_TBL." WHERE account_id = ".$account_id." AND institute_id=$institute_id AND branch_id=$branch_id AND bill_id=$bill_id";
			    $bquery = $this->db->query($BSSQL);
			    $bill_no = $bquery->row()->bill_no; $modified_time = date("Y-m-d H:i:s");
				$mdata = array(
					'bill_no'  			=>$bill_no,
					'billing_month' 	=>$billing_month,
					'billing_date'		=>$bill_date,
					'account_id'   		=>$account_id,
					'institute_id'    	=>$institute_id,
					'branch_id'    		=>$branch_id,
					'store_id'    		=>$store_id,
					'session_id'    	=>$session_id,
					'bill_amount'    	=>$total_bill,
					'discount_persent'  =>$discount_percentage,
					'discount_amount'   =>$discount_amount,
					'vat_percentage'  	=>$vat_percentage,
					'vat_amount'   		=>$vat_amount,
					'net_bill_amount'  	=>$net_bill_amount,
					'due_amount'  		=>$due_amount,
					'description'  		=>$description,
					'invoice_note1'  	=>$invoice_note1,
					'invoice_note2'  	=>$invoice_note2,
					'bill_type'			=>$bill_type,
					'modified_by'  		=>$created_by,
					'modified_time'  	=>$modified_time
				);
				$this->db->where('bill_id',$bill_id);
				$this->db->where('account_id',$account_id);
				$this->db->where('institute_id',$institute_id);
				$this->db->update(BILL_MASTER_TBL, $mdata);	
				$this->AdjustCollection($bill_id); 
		    }else{
			   $bill_no			= $this->getBillID($institute_id,$branch_id,$session_id,$billing_month,$bill_date);
			   $bill_type		= 4; // 4=Purchase
			   //======== Save Bill Master ========
			   $mdata = array(
					'bill_no'  			=>$bill_no,
					'billing_month' 	=>$billing_month,
					'billing_date'		=>$bill_date,
					'account_id'   		=>$account_id,
					'institute_id'    	=>$institute_id,
					'branch_id'    		=>$branch_id,
					'store_id'    		=>$store_id,
					'session_id'    	=>$session_id,
					'bill_amount'    	=>$total_bill,
					'discount_persent'  =>$discount_percentage,
					'discount_amount'   =>$discount_amount,
					'vat_percentage'  	=>$vat_percentage,
					'vat_amount'   		=>$vat_amount,
					'net_bill_amount'  	=>$net_bill_amount,
					'due_amount'  		=>$due_amount,
					'description'  		=>$description,
					'invoice_note1'  	=>$invoice_note1,
					'invoice_note2'  	=>$invoice_note2,
					'bill_type'			=>$bill_type,
					'created_by'  		=>$created_by
				);
				$this->db->insert(BILL_MASTER_TBL, $mdata); //print $this->db->last_query();
				$bill_id = $this->db->insert_id(); 
				//====== Update Bill Details ========
				$USQL= "UPDATE ".BILL_DETAILS_TBL." SET bill_id='".$bill_id."', store_id='".$store_id."', inventory_type='1' WHERE bill_id=0 AND institute_id = $institute_id AND branch_id = $branch_id AND session_id = $session_id AND account_id=$account_id AND created_by = ".$created_by;
				$this->db->query($USQL);
		    }
			
			$contra_id=$this->SaveJV($bill_id,$store_id,$institute_id,$branch_id,$session_id,$account_id,$net_bill_amount,$discount_percentage,$discount_amount,$total_bill,$bill_date,$description);
		    $vsql = "SELECT voucher_no FROM ".VOUCHER_MASTER_TBL." WHERE invoice_no ='".$bill_id."' AND voucher_type = 3 AND status ='1'";
			$vquery = $this->db->query($vsql);
			if($vquery->num_rows() >0){
			$voucher_no= $vquery->row()->voucher_no;
			}else{ $voucher_no="";} $total_quantity=0;
			//======== Get Bill Details ========
		    $bdsql = "SELECT * FROM ".BILL_DETAILS_TBL." WHERE institute_id = $institute_id AND branch_id = $branch_id AND session_id = $session_id AND account_id=$account_id AND bill_id=$bill_id";
			$overall_discount 	= (($discount_amount/$total_bill)*100);
			$overall_vat 		= (($vat_amount/$sub_total)*100);			
		    $bdquery = $this->db->query($bdsql);	//echo $bdsql; 		
		    foreach($bdquery->result() as $frow){			  			   
			   //======== Save Voucher Details ========
			   if($contra_id >0){
				$mode_of_payment	= 10; // 10=Others
				$voucher_type		= 3; // 3=Journal				
				$product_id 		= $frow->fee_account;				
				$account_head 		= $frow->fee_account;
				$cr_amount    		= $frow->total_price;
				$quantity			= $frow->quantity;
				$free_qty			= $frow->free_qty;
				$total_quantity		= ($quantity+$free_qty);
				if($overall_discount >0){
					$unit_discount		= (($frow->total_price/100)* $overall_discount);
					//$cr_amount    	= ($frow->total_price - $unit_discount);
				}else{
					$unit_discount		= 0;
				}
				$net_price =0; $unit_vat=0;
				if($vat_amount >0){					
					$net_price    		= ($frow->total_price - $unit_discount);
					$unit_vat		 	= (($net_price/100)* $overall_vat);
					$cr_amount    		= ($net_price + $unit_vat + $unit_discount);
				}
				$cr_amount = round($cr_amount,0,PHP_ROUND_HALF_UP);
				$purchase_price = ($cr_amount/$total_quantity);
			    //==== Save Dr Stock Account =====					
			    $DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
			    $DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Dr','".$account_head."','".$cr_amount."','".$voucher_type."','".$description."','".$created_by."')";
			    $this->db->query($DSQL);
			    $this->SaveAccountLedger($voucher_no,$bill_id,$bill_date,$account_head,$voucher_type,$description,$cr_amount,"Dr","I");
				$this->SaveAVGPurchasePrice($bill_id,$institute_id,$branch_id,$session_id,$store_id,$product_id,$purchase_price);
			   }
		    }//end foreach
		   
		}// end num_rows
		
	}
	function SaveJV($bill_id,$store_id,$institute_id,$branch_id,$session_id,$account_head,$net_bill_amount,$discount_percentage,$discount_amount,$bill_amount,$voucher_date,$note=NULL){			
	    $voucher_no 		= $this->getVoucherID($voucher_date);	
		$agency_percentage	= 0;	
		$agency_commission	= 0;	
		$vat_percentage		= $this->input->post('vat_percentage');	
		$vat_amount			= $this->input->post('vat_amount');
		$billing_month 		= $this->input->post('bill-period');
		$description		= $this->input->post('description');	
		
		if(empty($net_bill_amount)){$net_bill_amount=0;} 		
		$net_bill_amount = round($net_bill_amount,0,PHP_ROUND_HALF_UP);
		if(empty($agency_percentage)){$agency_percentage=0;$agency_commission=0;} 		
		$agency_commission = round($agency_commission,0,PHP_ROUND_HALF_UP);
		if(empty($vat_percentage)){$vat_percentage=0;} if(empty($vat_amount)){$vat_amount=0;} 		
		$vat_amount = round($vat_amount,0,PHP_ROUND_HALF_UP);
		if($vat_amount >0){
		$dr_amount 			= ($net_bill_amount-$vat_amount);
		$cr_amount 			= ($net_bill_amount-$vat_amount);
		}else{
		$dr_amount 			= ($net_bill_amount);
		$cr_amount 			= ($net_bill_amount);
		}
		
		$mode_of_payment	= 10; // 10=Others
		$voucher_type		= 3; // 3=Journal
		if(empty($description)){
		$description		= "Total bill of payable amount against purchase item";
		}
		if($discount_amount >0 ){
			$d_description		= "The discount $discount_amount tk on purchase invoice";
			$discount_head 		= $this->session->userdata('discount_head');
		}else{
			$discount_head=0; $d_description="";
		}
		$created_by		= $this->session->userdata('created_by');
		$contra_id		= 0;
		$vsql = "SELECT contra_id,voucher_no FROM ".VOUCHER_MASTER_TBL." WHERE invoice_no ='".$bill_id."' AND voucher_type = 3 AND status ='1'";
		$vquery = $this->db->query($vsql);
		if($vquery->num_rows() >0){
		    $contra_id = $vquery->row()->contra_id;
		    $voucher_no= $vquery->row()->voucher_no;		   		         
		    //=== Update Master =====
		    $SQL= "UPDATE ".VOUCHER_MASTER_TBL." SET invoice_no=".$bill_id.",institute_id='".$institute_id."',branch_id='".$branch_id."',store_id='".$store_id."',session_id='".$session_id."',account_id='".$account_head."',dr_amount='".$dr_amount."',cr_amount='".$cr_amount."', mode_of_payment='".$mode_of_payment."', voucher_type='".$voucher_type."', description='".$description."' WHERE contra_id = ".$contra_id;
		    $this->db->query($SQL);
		    //===== Delete All Voucher Details ======
		    if($contra_id >0){
			 $DLSQL1= "DELETE FROM ".VOUCHER_DETAILS_TBL." WHERE contra_id = ".$contra_id." AND invoice_no='".$bill_id."'";
			 $this->db->query($DLSQL1);
			 $DLSQL2= "DELETE FROM ".ACC_LEDGER_TBL." WHERE voucher_no='".$voucher_no."' AND invoice_no = ".$bill_id;
			 $this->db->query($DLSQL2);   
		    }
		   
		    //==== Cr Account Update =====	
		    if($account_head >0 && $dr_amount >0){
			   $acsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$account_head."' AND contra_id = $contra_id AND status ='1'";
			   $aquery = $this->db->query($acsql);
			   if($aquery->num_rows() >0){			
			     $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$dr_amount."', voucher_type='".$voucher_type."', description='".$description."' WHERE account_id='".$account_head."' AND headtypes='Cr' AND contra_id = ".$contra_id; 
				 $this->db->query($DSQL);			
			     $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$account_head,$voucher_type,$description,$dr_amount,"Cr","U");
			   }else{
			    //==== Start Cr Supplier Account =====					
			    $DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
			    $DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Cr','".$account_head."','".$dr_amount."','".$voucher_type."','".$description."','".$created_by."')";
			    $this->db->query($DSQL);
			    $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$account_head,$voucher_type,$description,$dr_amount,"Cr","I");
			   }//end else
		    }// End account_head 
				
		    //===== Cr Discount Account Update=====
		    if($discount_amount >0 && $discount_head >0){
			   $dcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$discount_head."' AND contra_id = $contra_id AND status ='1'";
			   $dquery = $this->db->query($dcsql);
			   if($dquery->num_rows() >0){			
			   $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$discount_amount."', voucher_type='".$voucher_type."', description='".$d_description."' WHERE account_id='".$discount_head."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
			   $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$discount_head,$voucher_type,$d_description,$discount_amount,"Cr","U");
			   }else{
			    //==== Start Cr Discount Account =====					
			    $DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
			    $DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Cr','".$discount_head."','".$discount_amount."','".$voucher_type."','".$d_description."','".$created_by."')";
			    $this->db->query($DSQL);
			    $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$discount_head,$voucher_type,$d_description,$discount_amount,"Cr","I");
			   }//end else
		    }			
		    //==== Cr VAT Payable Account =====	
			if($vat_amount >0){
			   $vat_head = $this->GetAccountId(6,11,41,17,6);
			   $vcsql    = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$vat_head."' AND contra_id = $contra_id AND status ='1'";
			   $vquery   = $this->db->query($vcsql);
			   $vat_description = "The VAT amount payable against purchase items";
			   if($vquery->num_rows() >0){			
			    $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$vat_amount."', voucher_type='".$voucher_type."', description='".$vat_description."' WHERE account_id='".$vat_head."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
			    $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$vat_head,$voucher_type,$vat_description,$vat_amount,"Cr","U");
			   }else{
			    //==== Start Cr VAT Payable Account =====					
			    $vat_head = $this->GetAccountId(6,11,41,17,6);			  
				$CSQL2="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no, voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
				$CSQL2.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Cr','".$vat_head."','".$vat_amount."','".$voucher_type."','".$vat_description."','".$created_by."')";
				$this->db->query($CSQL2);
				$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$vat_head,$voucher_type,$vat_description,$vat_amount,"Cr","I");
			   }//end else		   
			
			}// End if Payable vat_amount
						
		    return $contra_id;
		   
		}else{
			$SQL= "INSERT INTO ".VOUCHER_MASTER_TBL."(invoice_no,institute_id,branch_id,store_id,session_id,account_id,voucher_no,voucher_date,dr_amount,cr_amount,mode_of_payment,voucher_type,description,created_by) ";
		    $SQL.="VALUES('".$bill_id."','".$institute_id."','".$branch_id."','".$store_id."','".$session_id."','".$account_head."','".$voucher_no."','".$voucher_date."','".$dr_amount."','".$cr_amount."','".$mode_of_payment."','".$voucher_type."','".$description."','".$created_by."')";
		    if($voucher_no !=""){
		      $this->db->query($SQL);
		      $contra_id = $this->db->insert_id();
		      if($contra_id >0){		         
			    if($account_head>0 && $dr_amount>0){		
			     //==== Start Cr Sopplier Account =====					
			     $DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
			     $DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Cr','".$account_head."','".$dr_amount."','".$voucher_type."','".$description."','".$created_by."')";
			     $this->db->query($DSQL);
			     $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$account_head,$voucher_type,$description,$dr_amount,"Cr","I");
			    } // end account_head dr
			    
			    //===== Insert Cr Discount Account =====
			    if($discount_amount >0 && $discount_head >0){
				   $dcsql = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$discount_head."' AND contra_id = $contra_id AND status ='1'";
				   $dquery = $this->db->query($dcsql);
				   if($dquery->num_rows() >0){			
				   $DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$discount_amount."', voucher_type='".$voucher_type."', description='".$d_description."' WHERE account_id='".$discount_head."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
				   $this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$discount_head,$voucher_type,$d_description,$discount_amount,"Cr","U");
				   }else{
					//==== Start Cr Discount Account =====					
					$DSQL="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no,voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
					$DSQL.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Cr','".$discount_head."','".$discount_amount."','".$voucher_type."','".$d_description."','".$created_by."')";
					$this->db->query($DSQL);
					$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$discount_head,$voucher_type,$d_description,$discount_amount,"Cr","I");
				   }//end else
			    }
			
			    //==== Cr VAT Payable Account =====	
			    if($vat_amount >0){
				   $vat_head = $this->GetAccountId(6,11,41,17,6);
				   $vcsql    = "SELECT * FROM ".VOUCHER_DETAILS_TBL." WHERE account_id ='".$vat_head."' AND contra_id = $contra_id AND status ='1'";
				   $vquery   = $this->db->query($vcsql);
				   $vat_description = "The VAT amount payable against purchase items";
				   if($vquery->num_rows() >0){			
					$DSQL= "UPDATE ".VOUCHER_DETAILS_TBL." SET amount='".$vat_amount."', voucher_type='".$voucher_type."', description='".$vat_description."' WHERE account_id='".$vat_head."' AND contra_id = ".$contra_id; $this->db->query($DSQL);			
					$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$vat_head,$voucher_type,$vat_description,$vat_amount,"Cr","U");
				   }else{
					//==== Start Cr VAT Payable Account =====					
					$vat_head = $this->GetAccountId(6,11,41,17,6);			  
					$CSQL2="INSERT INTO ".VOUCHER_DETAILS_TBL."(contra_id,invoice_no, voucher_no,headtypes,account_id,amount,voucher_type,description,created_by) ";
					$CSQL2.="VALUES('".$contra_id."','".$bill_id."','".$voucher_no."','Cr','".$vat_head."','".$vat_amount."','".$voucher_type."','".$vat_description."','".$created_by."')";
					$this->db->query($CSQL2);
					$this->SaveAccountLedger($voucher_no,$bill_id,$voucher_date,$vat_head,$voucher_type,$vat_description,$vat_amount,"Cr","I");
				   }//end else		   
				
			    }// End if Payable vat_amount
			   			
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
	function GetStockBalanceQty($institute_id,$branch_id,$session_id,$store_id,$product_id){
		$balance_qty=0;
		$DSQL="SELECT sum(`quantity`) as balance_qty FROM ".BILL_DETAILS_TBL." WHERE `inventory_type` IN(1,2) AND fee_account='".$product_id."' AND institute_id='".$institute_id."' AND branch_id='".$branch_id."' AND store_id='".$store_id."'";
		$dquery = $this->db->query($DSQL);
		$dr_quantity=0;
		if($dquery->num_rows() >0){
			$dr_quantity = $dquery->row()->balance_qty;
		}else{
			$dr_quantity = 0;
		}
		$CSQL="SELECT sum(`quantity`) as balance_qty FROM ".BILL_DETAILS_TBL." WHERE `inventory_type` IN(1,2) AND fee_account='".$product_id."' AND institute_id='".$institute_id."' AND branch_id='".$branch_id."' AND store_id='".$store_id."'";
		$cquery = $this->db->query($CSQL);
		$cr_quantity=0;
		if($cquery->num_rows() >0){
			$cr_quantity = $cquery->row()->balance_qty;
		}else{
			$cr_quantity = 0;
		}
		$balance_qty =($dr_quantity - $cr_quantity);
		if(empty($balance_qty)){$balance_qty=0;}
		return $balance_qty;
	}
	function SaveAVGPurchasePrice($bill_id,$institute_id,$branch_id,$session_id,$store_id,$product_id,$purchase_price){
		
		$stock_qty = $this->GetStockBalanceQty($institute_id,$branch_id,$session_id,$store_id,$product_id);
		if($stock_qty <=0){
		$dsql = "DELETE FROM ".AVG_PURCHASE_PRICE_TBL." WHERE product_id='".$product_id."' AND institute_id='".$institute_id."' AND branch_id='".$branch_id."'";
		$this->db->query($dsql);
		}

		$asql = "INSERT INTO ".AVG_PURCHASE_PRICE_TBL."(institute_id,branch_id,session_id,bill_id,product_id,purchase_price) 
		VALUES('".$institute_id."','".$branch_id."','".$session_id."','".$bill_id."','".$product_id."','".$purchase_price."')"; 
		$this->db->query($asql);
		
		$avg_purchase_price=0; 
		if($this->db->affected_rows() >0){
			$Prosql = "SELECT purchase_price  FROM ".AVG_PURCHASE_PRICE_TBL." WHERE product_id='".$product_id."' AND institute_id='".$institute_id."' AND branch_id='".$branch_id."' ORDER BY `id` DESC LIMIT 0 , 2";
			$pquery = $this->db->query($Prosql);
			$ttl_product = $pquery->num_rows();
			if($ttl_product >0){
				if($ttl_product ==1){
					$avg_purchase_price = $pquery->row()->purchase_price;
				}elseif($ttl_product > 1){
					foreach($pquery->result() as $arow){
					$avg_purchase_price += $arow->purchase_price;
					}
				}		
				$avg_purchase_price = ($avg_purchase_price / $ttl_product);
			}
			if(intval($avg_purchase_price)==""){ $avg_purchase_price=0;}			
			
			if($avg_purchase_price ==0){
				$avg_purchase_price = $purchase_price;
			}
			$USQL 	= "UPDATE ".PRODUCT_TBL." SET purchase_avg_price = $avg_purchase_price WHERE product_id='".$product_id."' AND company_id='".$institute_id."' AND branch_id='".$branch_id."'";
			$this->db->query($USQL);
		}
    }
    function GetAccountId($group_id,$subsidiary_level1,$subsidiary_level2,$subsidiary_level3,$head_type=NULL){		
        $this->db->select('*');
        $this->db->from(ACC_HEAD_TBL);
		if($group_id >0){
			$this->db->where("group_id", $group_id);
		}
		if($subsidiary_level1 >0){
			$this->db->where("subsidiary_level1", $subsidiary_level1);
		}
		if($subsidiary_level2 >0){
			$this->db->where("subsidiary_level2", $subsidiary_level2);
		}
		if($subsidiary_level3 >0){
			$this->db->where("subsidiary_level3", $subsidiary_level3);
		}		
		if($head_type >0){
			$this->db->where("head_type", $head_type);
		}
		$this->db->where('status',1);
        $aquery = $this->db->get();
		return $aquery->row()->account_id;         
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
		//======= adjust previous collection amount =====
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
	function GetAjaxBillList(){		
		$bill_id		= $this->input->post('bill-id');
		if(empty($bill_id)){
			$bill_id	= 0;
			$status		= 1;
		}		
		$bill_date			=$this->formatDate($this->input->post('bill_date'));
		$billing_month 		=$this->input->post('bill-period');
		$supplier_id		=$this->input->post('supplier_id'); 
		$institute_id		=$this->input->post('institute_id');
		$branch_id			=$this->input->post('branch_id');
		$session_id			=$this->input->post('session_id');
		
		$menu_slug= $this->uri->segment(1);
		$hasEditPM = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM  = $this->Site_model->hasOptionPermission($menu_slug,"Delete");
		
		$bdsql = "SELECT b.*, a.account_name,a.account_details,p.period_name_en as period_name,p.period_year FROM ".BILL_DETAILS_TBL." as b,".ACC_HEAD_TBL." as a,".PERIOD_TBL." as p WHERE b.fee_account = a.account_id AND b.billing_month=p.period_id AND b.institute_id = $institute_id AND b.branch_id = $branch_id AND b.session_id = $session_id AND b.account_id=$supplier_id AND b.`billing_month`=$billing_month AND bill_id=$bill_id";
		$bdsql.= " GROUP BY b.details_id ORDER BY a.account_name ASC"; //echo $bdsql;
		$query = $this->db->query($bdsql); 
		if($query->num_rows() >0){	
		  echo 
		  '<table width="100%"  border="0" class="table table-responsive table-bordered table-hover custab">
			<thead>
			  <tr class="active">
			  	<th width="2%">'.$this->lang->line("sl").'</th>
				<th width="10%">'.$this->lang->line("month_name").'</th>
				<th width="25%">'.$this->lang->line("particulars").'</th>
				<th width="12%" class="text-right">'.$this->lang->line("quantity").'</th>
				<th width="12%" class="text-right">'.$this->lang->line("free_qty").'</th>
				<th width="13%" class="text-right">'.$this->lang->line("unit_price").'</th>
				<th width="13%" class="text-right">'.$this->lang->line("total_amount").'</th>
				<th width="13%" class="text-center">'.$this->lang->line("options").'</th>
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
			  	<td class='text-right'>".$row->free_qty."</td>
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
			  	<th colspan='6'>".$this->lang->line("total_amount")."</th>
				<th class='text-right'>".$TotalBill."<input type='hidden' name='total_bill_amount' id='total_bill_amount' value='".$TotalBill."'></th>
				<th class='text-right'>&nbsp;</th>
			  </tr>";
		  echo '</table>##&##'.$TotalBill;
		}//end num_rows	
	}
		
   	//========== Retrive by Ajax ==========
   	function GetRecordGrid(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');
		$hasEditPM = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM  = $this->Site_model->hasOptionPermission($menu_slug,"Delete");
		$hasPrintPM= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$srcFrom			=$this->formatDate($this->input->post('srcFrom'));
		$srcTo				=$this->formatDate($this->input->post('srcTo'));
		$billing_month 		=$this->input->post('src-period');
		$supplier_id		=$this->input->post('src-supplier'); 
		$institute_id		=$this->input->post('src-institute');
		$branch_id			=$this->input->post('src-branch');
		$session_id			=$this->input->post('src-session');
		$store_id			=$this->input->post('src-store');
		
	   	$from	=$this->input->post('from');
		$to	=$this->input->post('to');
		if($from==""){ $from=0;} if($to==""){ $to=50;}
		$this->db->select('bl.*,p.account_id,p.head_id,p.account_name,p.bangla_name,p.account_details,p.mobile,r.period_name_en as period_name,r.period_year,i.company_name,b.branch_name,b.branch_code,se.session_name,st.store_name,DATE_FORMAT(bl.billing_date ,"%d-%m-%Y") as bill_date',FALSE);
		$this->db->from(BILL_MASTER_TBL." AS bl");
		$this->db->where("bl.bill_type", 4);
		$this->db->join(PERIOD_TBL.' AS r', 'r.period_no=bl.billing_month','LEFT');
		$this->db->join(ACC_HEAD_TBL.' AS p', 'p.account_id=bl.account_id','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=bl.institute_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=bl.branch_id','LEFT');
	  	$this->db->join(SESSION_TBL.' AS se', 'se.sessions_id=bl.session_id','LEFT');
	  	$this->db->join(STORE_TBL.' AS st', 'st.store_id=bl.store_id','LEFT');
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
		if($supplier_id >0){
			  $this->db->where("bl.account_id", $supplier_id);  	
		}
		if($session_id >0){
			  $this->db->where("bl.session_id", $session_id);  	
		}
		if($store_id >0){
			  $this->db->where("bl.store_id", $store_id);  	
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
			  	<th width="15%">'.$this->lang->line("supplier").' '.$this->lang->line("details").'</th>
				<th width="10%">'.$this->lang->line("total_amount").'</th>
				<th width="12%">'.$this->lang->line("discount_amount").'</th>
				<th width="10%">'.$this->lang->line("vat")." ".$this->lang->line("amount").'</th>
				<th width="10%">'.$this->lang->line("net_amount").'</th>
				<th width="10%">'.$this->lang->line("paid_amount").'</th>
				<th width="10%">'.$this->lang->line("due_amount").'</th>
				<th width="8%" class="text-center hidden-print">'.$this->lang->line("options").'</th>
			  </tr>
			</thead>';
			  $i=1;
			  foreach($query->result() as $row){
			  //if($i%2==0){ $tblrow="success";}else{$tblrow="warning";}
			  echo "<tr class='default'>
			  	<td>".$i."</td>
				<td>
				".$row->bill_no."<br> Date: ".$row->bill_date."<br>Month: ".$row->period_name." - ".$row->period_year."
				</td>
			  	<td>				
				".$row->account_name.",<br>".$row->account_details.", Code: ".$row->head_id."				
				</td>
				<td>".$row->bill_amount."</td>
				<td>".$row->discount_amount."</td>
				<td>".$row->vat_amount."</td>
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
				echo "<div class='clearfix'></div><br><span data-toggle='tooltip' data-original-title='View Form'><a class='btn btn-success btn-sm' target='_blank' href='".base_url()."purchase/ViewBillForm/".$row->bill_id."/".$row->account_id."'><i class='fa fa-print'></i> Form</a></span>";
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
		$supplier_id		=$this->input->post('src-supplier'); 
		$institute_id		=$this->input->post('src-institute');
		$branch_id			=$this->input->post('src-branch');
		$session_id			=$this->input->post('src-session');
		$store_id			=$this->input->post('src-store');
		$this->db->select('bl.*,p.account_id,p.head_id,p.account_name,p.bangla_name,p.account_details,p.mobile,r.period_name_en as period_name,r.period_year,i.company_name,b.branch_name,b.branch_code,DATE_FORMAT(bl.billing_date ,"%d-%m-%Y") as bill_date',FALSE);
		$this->db->from(BILL_MASTER_TBL." AS bl");
		$this->db->where("bl.bill_type", 4);
		$this->db->join(PERIOD_TBL.' AS r', 'r.period_no=bl.billing_month','LEFT');
		$this->db->join(ACC_HEAD_TBL.' AS p', 'p.account_id=bl.account_id','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=bl.institute_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=bl.branch_id','LEFT');
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
		if($supplier_id >0){
			  $this->db->where("bl.account_id", $supplier_id);  	
		}
		if($session_id >0){
			  $this->db->where("bl.session_id", $session_id);  	
		}
		if($store_id >0){
			  $this->db->where("bl.store_id", $store_id);  	
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
		$this->db->where('bill_id',$id);
		$this->db->delete(BILL_MASTER_TBL);
		$this->db->where('bill_id',$id);
		$this->db->delete(BILL_DETAILS_TBL);
		$this->db->where('invoice_no',$id);
		$this->db->delete(VOUCHER_MASTER_TBL);
		$this->db->where('invoice_no',$id);
		$this->db->delete(VOUCHER_DETAILS_TBL);
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
		$query = $this->db->get();
		return $query->row();
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
                    $paginationStr .= "<li class='page-item'";
                    if ($i == $plink) {
                        $paginationStr .= "class='active'";
                    }
                    $paginationStr .= ">";
                    $paginationStr .= "<a class='page-link' onclick=nextPage($from,$to,$i) href='#'>$i</a></li>";
                } else {
                    $paginationStr .= "<li class='page-item'";
                    if ($i == $plink) {
                        $paginationStr .= "class='active' ";
                    }
                    $paginationStr .= ">";
                    $paginationStr .= "<a class='page-link' onclick=nextPage($from,$to,$i) href='#'>$i</a></li>";
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
