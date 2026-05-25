<?php 
class Deliverychallan_model extends CI_Model {
		
	function __construct()
	{
		parent::__construct();
	}	
	
	function InsertDetailRecord(){
		$details_id		    = $this->input->post('details-id');
		if(empty($details_id)){
		$details_id		    = 0;
		}		    
		$challan_id	        = $this->input->post('challan-id');
		if(empty($challan_id)){
			$challan_id	    = 0;
			$status		    = 0;
		}else{
			$this->db->select('status');
			$this->db->from(CHALLAN_MASTER_TBL); 
			$this->db->where('challan_id', $challan_id);
			$query 		= $this->db->get();
			$status		= $query->row()->status;
		}
		$institute_id		= $this->input->post('institute-id');
		$branch_id			= $this->input->post('branch-id');
		$customer_id		= $this->input->post('customer-id');
		$workorder_id		= $this->input->post('workorder-id');
		$product_description= $this->input->post('product-details');
		$product_sku        = $this->input->post('product-sku');  
		$validity		    = $this->input->post('validity');
		$start_date		    = $this->formatDate($this->input->post('start_date'));
		$end_date		    = $this->formatDate($this->input->post('end_date'));
		$quantity			= $this->input->post('quantity');
		$unit_price			= $this->input->post('unit-price');
		if($unit_price >0){
		$total_price		= ($quantity * $unit_price);
		$total_price 		= round($total_price,2,PHP_ROUND_HALF_UP);
		}else{
		$total_price		= 0;    
		}		
		$remarks 			= str_replace("U 0026", '&', $this->input->post('remarks'));			
    	$created_by			= $this->session->userdata('created_by');
									
		$ddata = array(
		'challan_id'    	=>$challan_id,
		'institute_id'    	=>$institute_id,
		'branch_id'    		=>$branch_id,
		'customer_id'   	=>$customer_id,
		'workorder_id'  	=>$workorder_id,
		'product_description'=>$product_description,
		'product_sku'       =>$product_sku,
		'validity'    	    =>$validity,
		'start_date'    	=>$start_date,
		'end_date'    	    =>$end_date,
		'quantity'    		=>$quantity,
		'unit_price'    	=>$unit_price,
		'total_price'  		=>$total_price,
		'remarks'  			=>$remarks,
		'status'  			=>$status,
		'created_by'  		=>$created_by
	    );
	   	   
		if($customer_id >0 && $workorder_id >0 && $product_description!="" && $quantity >0){
			if($details_id ==0){ 
			 $this->db->insert(CHALLAN_DETAILS_TBL, $ddata);
			}else{
			$this->EditDetailRecord($details_id);
			}
			//print  $this->db->last_query();
		}
		
	}
    function EditDetailRecord($details_id){
		if(empty($details_id)){
		$details_id		    = 0;
		}		    
		$challan_id	        = $this->input->post('challan-id');
		if(empty($challan_id)){
			$challan_id	    = 0;
			$status		    = 0;
		}else{
			$this->db->select('status');
			$this->db->from(CHALLAN_MASTER_TBL); 
			$this->db->where('challan_id', $challan_id);
			$query 		= $this->db->get();
			$status		= $query->row()->status;
		}
		$institute_id		= $this->input->post('institute-id');
		$branch_id			= $this->input->post('branch-id');
		$customer_id		= $this->input->post('customer-id');
		$workorder_id		= $this->input->post('workorder-id');
		$product_description= $this->input->post('product-details');
		$product_sku        = $this->input->post('product-sku'); 
		$validity		    = $this->input->post('validity');
		$start_date		    = $this->formatDate($this->input->post('start_date'));
		$end_date		    = $this->formatDate($this->input->post('end_date'));
		$quantity			= $this->input->post('quantity');
		$unit_price			= $this->input->post('unit-price');
		if($unit_price >0){
		$total_price		= ($quantity * $unit_price);
		$total_price 		= round($total_price,2,PHP_ROUND_HALF_UP);
		}else{
		$total_price		= 0;    
		}		
		$remarks 			= str_replace("U 0026", '&', $this->input->post('remarks'));
    	$modified_by		=$this->session->userdata('created_by');
		$modified_time  	=date("Y-m-d H:i:s");		
						
		$ddata = array(
		'institute_id'    	=>$institute_id,
		'branch_id'    		=>$branch_id,
		'customer_id'   	=>$customer_id,
		'workorder_id'  	=>$workorder_id,
		'product_description'=>$product_description,
		'product_sku'       =>$product_sku,
		'validity'    	    =>$validity,
		'start_date'    	=>$start_date,
		'end_date'    	    =>$end_date,
		'quantity'    		=>$quantity,
		'unit_price'    	=>$unit_price,
		'total_price'  		=>$total_price,
		'remarks'  			=>$remarks,
		'status'  			=>$status,
		'modified_by'  		=>$modified_by,
		'modified_time'		=>$modified_time
	    );
		$this->db->where('challan_id',$challan_id);
		$this->db->where('details_id',$details_id);
		$this->db->update(CHALLAN_DETAILS_TBL, $ddata);
        //print  $this->db->last_query();
	}
		
	function saveDCMaster($challan_id){
		$institute_id		= $this->input->post('institute_id');
		$branch_id			= $this->input->post('branch_id');
		$customer_id		= $this->input->post('customer_id');
		$workorder_id		= $this->input->post('workorder_id');
		$challan_date		= $this->formatDate($this->input->post('challan_date'));
		$delivery_address 	= str_replace("U 0026", '&', $this->input->post('delivery_address'));
		$delivery_note 	    = str_replace("U 0026", '&', $this->input->post('delivery_note'));
		$status		        = $this->input->post('status');
		
		$created_by			= $this->session->userdata('created_by');
				
		if($challan_date !="" && $workorder_id>0){
		   		   	   
		   if($challan_id >0){
		        $modified_time  = date("Y-m-d H:i:s");
				$mdata = array(
					'institute_id'    	=>$institute_id,
					'branch_id'    		=>$branch_id,
					'customer_id'	    =>$customer_id,
					'workorder_id'	    =>$workorder_id,
					'challan_date'   	=>$challan_date,
					'delivery_address'  =>$delivery_address,
					'delivery_note'  	=>$delivery_note,
		            'status'  			=>$status,
					'modified_by'  		=>$created_by,
					'modified_time'  	=>$modified_time
				);
				$this->db->where('challan_id',$challan_id);
				$this->db->update(CHALLAN_MASTER_TBL, $mdata);	
		    }else{
		       $challandateArr      = explode("-",$challan_date);
			   $challan_no			= $this->getBillID($branch_id,$customer_id,$workorder_id,$challandateArr[1],$challan_date);
			   //======== Save Distri PO Master ========
			   $mdata = array(
					'institute_id'    	=>$institute_id,
					'branch_id'    		=>$branch_id,
					'customer_id'	    =>$customer_id,
					'workorder_id'	    =>$workorder_id,
					'challan_no'   	    =>$challan_no,
					'challan_date'   	=>$challan_date,
					'delivery_address'  =>$delivery_address,
					'delivery_note'  	=>$delivery_note,
		            'status'  			=>$status,
					'created_by'  		=>$created_by
				);
				$this->db->insert(CHALLAN_MASTER_TBL, $mdata); //print $this->db->last_query();
				$challan_id = $this->db->insert_id(); 
				//====== Update Bill Details ========
				$USQL= "UPDATE ".CHALLAN_DETAILS_TBL." SET challan_id='".$challan_id."',challan_no='".$challan_no."' WHERE challan_id=0 AND customer_id = $customer_id AND workorder_id = $workorder_id  AND created_by = ".$created_by;
				$this->db->query($USQL);
		    }
			
		}// end if total_amount
		
	}
	//===== End Workorder =======
	
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
	
	function getBillID($branch_id,$customer_id,$workorder_id,$billing_month,$bill_date){
		$SL=""; $TotalNo=0; $BillNo = ""; $yearArr = explode("-",$bill_date);
		$ssql = "SELECT COUNT(*) as total FROM ".CHALLAN_MASTER_TBL." WHERE branch_id = $branch_id AND customer_id = $customer_id AND workorder_id = $workorder_id AND status < 5";
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
		$BC =""; $DCL = "DCL"; $BM = 0; $BM=$billing_month;
		if($query->num_rows() == 1){
		 $row = $query->row();		
		 $BC  = $row->branch_code;
		}		
		$BillNo = $BC."-".$DCL.$yearArr[0]."/".$BM."/".$SL;
		return $BillNo;		
	}
	function getHeadType($account_id){
        $this->db->select('head_type');
        $this->db->from(ACC_HEAD_TBL);
        $this->db->where('account_id', $account_id);
        $query = $this->db->get();
        return $query->row()->head_type; 
    }
    function getWorkorderList($customer_id,$workorder_id){
		$this->db->select('*');
		$this->db->from(WORKORDER_MASTER_TBL);
		if($customer_id >0){
			$this->db->where("customer_id", $customer_id);
		}
		$this->db->order_by('workorder_id','ASC');
		$query = $this->db->get(); //echo $this->db->last_query();
		$Count = "";
		$options = "<option value='0'>Select Workorder List</option>";
		foreach($query->result() as $irow){
			if($workorder_id >0 && $workorder_id == $irow->workorder_id){ 
				$selected = "selected='selected'"; 
			}else{ $selected = ""; }
			$options.="<option  value='".$irow->workorder_id."' $selected >".$irow->workorder_no."</option>";
		}
		return $options;
    }
    function getAjaxWorkorderList(){
		$customer_id 	=$this->input->post('id');
		$workorder_id 	=$this->input->post('workorder_id');
		$this->db->select('*');
		$this->db->from(WORKORDER_MASTER_TBL);
		if($customer_id >0){
			$this->db->where("customer_id", $customer_id);
		}
		$this->db->order_by('workorder_id','ASC');
		$query = $this->db->get(); //echo $this->db->last_query();
		$Count = "";
		$options = "<option value='0'>Select Workorder List</option>";
		foreach($query->result() as $irow){
			if($workorder_id >0 && $workorder_id == $irow->workorder_id){ 
				$selected = "selected='selected'"; 
			}else{ $selected = ""; }
				
			$options.="<option  value='".$irow->workorder_id."' $selected >".$irow->workorder_no."</option>";
		}
		return $options;
    }
	function GetAjaxDetailList(){		
		$challan_id		= $this->input->post('challan-id');
		if(empty($challan_id)){
			$challan_id	= 0;
			$status	= 1;
		}
		
		$institute_id		= $this->input->post('institute-id');
		$branch_id			= $this->input->post('branch-id');
		$customer_id		= $this->input->post('customer-id');
		$workorder_id		= $this->input->post('workorder-id');
		
		$menu_slug = $this->uri->segment(1);
		$hasEditPM = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM  = $this->Site_model->hasOptionPermission($menu_slug,"Delete");
		
		$bdsql = "SELECT details_id, challan_id, product_description, product_sku, validity as license_no, DATE_FORMAT(start_date, '%d %M %Y') as start_date, DATE_FORMAT(end_date, '%d %M %Y') as end_date, quantity, total_price, remarks FROM ".CHALLAN_DETAILS_TBL." WHERE institute_id = $institute_id AND branch_id = $branch_id AND customer_id=$customer_id AND workorder_id=$workorder_id AND challan_id=$challan_id";
		$bdsql.= " GROUP BY details_id ORDER BY details_id ASC"; //echo $bdsql;
		$query = $this->db->query($bdsql); 
		if($query->num_rows() >0){	
		  echo 
		  '<table width="100%"  border="0" class="table table-responsive table-bordered table-hover custab">
			<thead>
			  <tr class="active">
			  	<th width="2%">'.$this->lang->line("sl").'</th>
				<th width="26%">'.$this->lang->line("product_description").'</th>
				<th width="15%">'.$this->lang->line("product_sku").'</th>
				<th width="12%">'.$this->lang->line("license_no").'</th>
				<th width="15%">'.$this->lang->line("validity").'</th>
				<th width="8%" class="text-right">'.$this->lang->line("quantity").'</th>
			    <th width="10%">'.$this->lang->line("remarks").'</th>
				<th width="12%" class="text-center">'.$this->lang->line("options").'</th>
			  </tr>
			</thead>';
			  $i=1; $TotalBill=0; $validity = "";
			  foreach($query->result() as $row){
			  //if($i%2==0){ $tblrow="success";}else{$tblrow="warning";}
			  $TotalBill+=$row->total_price;
			  $validity = $row->start_date." to ".$row->end_date;
			  echo "<tr class='default'>
			  	<td>".$i."</td>
				<td>".$row->product_description."</td>
				<td>".$row->product_sku."</td>
				<td>".$row->license_no."</td>
				<td class='text-left'>".$validity."</td>
			  	<td class='text-right'>".$row->quantity."</td>
				<td class='text-left'>".$row->remarks."</td>
				<td align='center'>";
				if($hasEditPM){
				echo "<span data-toggle='tooltip' data-original-title='Edit'><a class='btn btn-info btn-xs' data-toggle='modal' onclick=editRow('".$row->details_id."','".$row->challan_id."') id='".$row->details_id."' href='#'><i class='fa fa-edit'></i></a></span> &nbsp;";
				}
				if($hasDelPM){				
				echo "<span data-toggle='tooltip' data-original-title='Delete'><a class='btn btn-danger btn-xs' data-toggle='modal' onclick=deleteRow('".$row->details_id."','".$row->challan_id."') id='".$row->details_id."' href='#deleteDraftModal'><i class='fa fa-trash'></i></a></span>";
				}
				echo "</td>
				</tr>";
			  $i++;
			  }
		  echo '</table>';
		}//end num_rows	
	}
	function GetProductDetailList($challan_id,$customer_id,$workorder_id){
		
		$menu_slug = $this->uri->segment(1);
		$hasEditPM = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM  = $this->Site_model->hasOptionPermission($menu_slug,"Delete");
		
		$bdsql = "SELECT details_id, challan_id, product_description, product_sku, validity as license_no, DATE_FORMAT(start_date, '%d %M %Y') as start_date, DATE_FORMAT(end_date, '%d %M %Y') as end_date, quantity, total_price, remarks FROM ".CHALLAN_DETAILS_TBL." WHERE customer_id=$customer_id AND workorder_id=$workorder_id AND challan_id=$challan_id";
		$bdsql.= " GROUP BY details_id ORDER BY details_id ASC"; //echo $bdsql;
		$query = $this->db->query($bdsql); 
		$TBLGRIDE="";
		if($query->num_rows() >0){	
		  $TBLGRIDE.= 
		  '<table width="100%"  border="0" class="table table-responsive table-bordered table-hover custab">
			<thead>
			  <tr class="active">
			  	<th width="2%">'.$this->lang->line("sl").'</th>
				<th width="26%">'.$this->lang->line("product_description").'</th>
				<th width="15%">'.$this->lang->line("product_sku").'</th>
				<th width="12%">'.$this->lang->line("license_no").'</th>
				<th width="15%" class="text-left">'.$this->lang->line("validity").'</th>
				<th width="8%" class="text-right">'.$this->lang->line("quantity").'</th>
			    <th width="10%">'.$this->lang->line("remarks").'</th>
				<th width="12%" class="text-center">'.$this->lang->line("options").'</th>
			  </tr>
			</thead>';
			  $i=1; $TotalBill=0; $validity = "";
			  foreach($query->result() as $row){
			  //if($i%2==0){ $tblrow="success";}else{$tblrow="warning";}
			  $TotalBill+=$row->total_price;
			  $validity = $row->start_date." to ".$row->end_date;
			  $TBLGRIDE.= "<tr class='default'>
			  	<td>".$i."</td>
				<td>".$row->product_description."</td>
				<td>".$row->product_sku."</td>
				<td>".$row->license_no."</td>
				<td class='text-left'>".$validity."</td>
			  	<td class='text-right'>".$row->quantity."</td>
				<td class='text-right'>".$row->remarks."</td>
				<td align='center'>";
				if($hasEditPM){
				$TBLGRIDE.= "<span data-toggle='tooltip' data-original-title='Edit'><a class='btn btn-info btn-xs' data-toggle='modal' onclick=editRow('".$row->details_id."','".$row->challan_id."') id='".$row->details_id."' href='#'><i class='fa fa-edit'></i></a></span> &nbsp;";
				}
				if($hasDelPM){				
				$TBLGRIDE.= "<span data-toggle='tooltip' data-original-title='Delete'><a class='btn btn-danger btn-xs' data-toggle='modal' onclick=deleteRow('".$row->details_id."','".$row->challan_id."') id='".$row->details_id."' href='#deleteDraftModal'><i class='fa fa-trash'></i></a></span>";
				}
				$TBLGRIDE.= "</td>
				</tr>";
			  $i++;
			  }
		  $TBLGRIDE.= '</table>';
		  return $TBLGRIDE;
		}//end num_rows	
	}
	function GetAccountName($customer_id){
		if($customer_id >0){
			$this->db->select('account_name,account_details');
		    $this->db->from(ACC_HEAD_TBL);
			$this->db->where('account_id', $customer_id);
			$query = $this->db->get();
			//print  $this->db->last_query();
			$Customer ="";
			if($query->num_rows() >0){
    			$row = $query->row();
    			$Customer = $row->account_name;
    			if($row->account_details !=""){
    			$Customer.="<br>".$row->account_details;
    			}
			}
			return $Customer;
		}else{
			return "";
		}
	}	
   	//========== Retrive by Ajax ==========
   	function GetRecordGrid(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');
		$hasEditPM  = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM   = $this->Site_model->hasOptionPermission($menu_slug,"Delete");
		$hasApprovPM= $this->Site_model->hasOptionPermission($menu_slug,"Approved");
		$hasPrintPM = $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$srcFrom			=$this->formatDate($this->input->post('srcFrom'));
		$srcTo				=$this->formatDate($this->input->post('srcTo'));
		$institute_id		=$this->input->post('src-institute');
		$branch_id			=$this->input->post('src-branch');
		$customer_id		=$this->input->post('src-customer');
		$workorder_id		=$this->input->post('src-workorder');
		
	   	$from	=$this->input->post('from');
		$to	=$this->input->post('to');
		if($from==""){ $from=0;} if($to==""){ $to=50;}
		$this->db->select('bl.*,w.workorder_no,c.customer_full_name,c.address,c.phone,c.fax,c.mobile,c.mobile,i.company_name,b.branch_name,b.branch_code,DATE_FORMAT(bl.challan_date ,"%d-%m-%Y") as challan_date,DATE_FORMAT(w.workorder_date ,"%d-%m-%Y") as workorder_date',FALSE);
		$this->db->from(CHALLAN_MASTER_TBL." AS bl");
		$this->db->join(WORKORDER_MASTER_TBL.' AS w', 'w.workorder_id=bl.workorder_id','LEFT');
		$this->db->join(CLIENT_TBL.' AS c', 'c.customer_id=bl.customer_id','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=bl.institute_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=bl.branch_id','LEFT');
	  	$this->db->where("bl.status < 3");
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
			$this->db->where("c.account_id", $this->session->userdata('user_ref_id')); 
		}
		if($customer_id >0){
			  $this->db->where("bl.customer_id", $customer_id);  	
		}
		if($workorder_id >0){
			  $this->db->where("bl.workorder_id", $workorder_id);  	
		}
		if($srcFrom!="" && $srcTo==""){
		   $this->db->where("bl.challan_date >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
		   $this->db->where("bl.challan_date <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
		   $this->db->where("bl.challan_date BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}
		
		$this->db->group_by('bl.challan_id');
		$this->db->order_by('bl.challan_id','ASC');
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
			  	<th width="35%">'.$this->lang->line("customer").' '.$this->lang->line("details").'</th>
				<th width="15%">'.$this->lang->line("workorder_no").'</th>
				<th width="15%">'.$this->lang->line("workorder_date").'</th>
				<th width="15%">'.$this->lang->line("challan_no").'</th>
				<th width="10%">'.$this->lang->line("challan_date").'</th>
				<th width="8%" class="text-center hidden-print">'.$this->lang->line("options").'</th>
			  </tr>
			</thead>';
			  $i=1; $TotalBill=0; $TotalNetBill=0; $TotalLess=0; $TotalVat=0; $TotalAit=0; $payment_mode="";
			  foreach($query->result() as $row){
			   //if($i%2==0){ $tblrow="success";}else{$tblrow="warning";}
			   echo "<tr class='default'>
			  	<td>".$i."</td>
			    <td>".$row->customer_full_name."<br> ".$row->address."</td>
				<td>".$row->workorder_no."</td>
				<td>".$row->workorder_date."</td>
				<td>".$row->challan_no."</td>
				<td>".$row->challan_date."</td>
				<td class='text-center align-middle hidden-print'>";
		    	if($hasEditPM){
				echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Edit'><a class='btn btn-info btn-xs' onclick=editRecord('".$row->challan_id."') id='".$row->challan_id."' href='#'><i class='fas fa-edit'></i></a></span>&nbsp;";
				}
				if($hasDelPM){				
				echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Delete'><a class='btn btn-danger btn-xs' data-toggle='modal' onclick=deleteRecord('".$row->challan_id."') id='".$row->challan_id."' data-target='#deleteModal'><i class='fa fa-trash'></i></a></span>";
				}
				if($hasApprovPM){
				    if($row->status==0){
				    echo "<div class='clearfix'></div><br><span data-toggle='tooltip' data-placement='top' data-original-title='Approve'><a class='btn btn-warning btn-xs' data-toggle='modal' onclick=ApproveDC('".$row->challan_id."','".$row->status."') id='".$row->challan_id."' data-target='#approveModal'><i class='fa fa-times'></i></a></span>";
				    }elseif($row->status==1){
				    echo "<div class='clearfix'></div><br><span data-toggle='tooltip' data-placement='top' data-original-title='Upapprove'><a class='btn btn-success btn-xs' data-toggle='modal' onclick=UnapproveDC('".$row->challan_id."','".$row->status."') id='".$row->challan_id."' data-target='#unapproveModal'><i class='fa fa-check'></i></a></span>";
				    }
				    echo "&nbsp;<span data-toggle='tooltip' data-original-title='View'><a class='btn btn-success btn-xs' target='_blank' href='".base_url()."deliverychallan/ViewDCForm/".$row->challan_id."/".$row->workorder_id."'><i class='fa fa-print'></i></a></span>";
				}else{
				 if($hasPrintPM){
				 echo "<div class='clearfix'></div><br><span data-toggle='tooltip' data-original-title='View Form'><a class='btn btn-success btn-sm' target='_blank' href='".base_url()."deliverychallan/ViewDCForm/".$row->challan_id."/".$row->workorder_id."'><i class='fa fa-print'></i> Form</a></span>";
				 }
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
		$institute_id		=$this->input->post('src-institute');
		$branch_id			=$this->input->post('src-branch');
		$customer_id		=$this->input->post('src-customer');
		$workorder_id		=$this->input->post('src-workorder');
		
		$this->db->select('bl.*,c.customer_full_name,c.address,c.phone,c.fax,c.mobile,c.mobile,i.company_name,b.branch_name,b.branch_code,DATE_FORMAT(bl.challan_date ,"%d-%m-%Y") as challan_date',FALSE);
		$this->db->from(CHALLAN_MASTER_TBL." AS bl");
		$this->db->join(CLIENT_TBL.' AS c', 'c.customer_id=bl.customer_id','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=bl.institute_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=bl.branch_id','LEFT');
	  	$this->db->where("bl.status < 3");
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
			$this->db->where("c.account_id", $this->session->userdata('user_ref_id')); 
		}
		if($customer_id >0){
			  $this->db->where("bl.customer_id", $customer_id);  	
		}
		if($workorder_id >0){
			  $this->db->where("bl.workorder_id", $workorder_id);  	
		}
		if($srcFrom!="" && $srcTo==""){
		   $this->db->where("bl.challan_date >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
		   $this->db->where("bl.challan_date <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
		   $this->db->where("bl.challan_date BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}
		
		$this->db->group_by('bl.challan_id');
		$this->db->order_by('bl.challan_id','ASC');
		$query = $this->db->get();
		if($query->num_rows() >0){
			return $query->num_rows();
		}else{
			return 0;
		}//echo $this->db->last_query();
	}
	
   	function GetChallanGridList(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');
		$hasEditPM  = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM   = $this->Site_model->hasOptionPermission($menu_slug,"Delete");
		$hasApprovPM= $this->Site_model->hasOptionPermission($menu_slug,"Approved");
		$hasPrintPM = $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$srcFrom			=$this->formatDate($this->input->post('srcFrom'));
		$srcTo				=$this->formatDate($this->input->post('srcTo'));
		$institute_id		=$this->input->post('src-institute');
		$branch_id			=$this->input->post('src-branch');
		$customer_id		=$this->input->post('src-customer');
		$workorder_id		=$this->input->post('src-workorder');
		
	   	$from	=$this->input->post('from');
		$to	=$this->input->post('to');
		if($from==""){ $from=0;} if($to==""){ $to=50;}
		$this->db->select('bl.*,c.customer_full_name,c.address,c.phone,c.fax,c.mobile,c.mobile,i.company_name,b.branch_name,b.branch_code,DATE_FORMAT(bl.po_date ,"%d-%m-%Y") as po_date',FALSE);
		$this->db->from(CHALLAN_MASTER_TBL." AS bl");
		$this->db->join(CLIENT_TBL.' AS c', 'c.customer_id=bl.customer_id','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=bl.institute_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=bl.branch_id','LEFT');
	  	$this->db->where("bl.status < 3");
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
			$this->db->where("c.account_id", $this->session->userdata('user_ref_id')); 
		}
		if($customer_id >0){
			  $this->db->where("bl.customer_id", $customer_id);  	
		}
		if($workorder_id >0){
			  $this->db->where("bl.workorder_id", $workorder_id);  	
		}
		if($srcFrom!="" && $srcTo==""){
		   $this->db->where("bl.challan_date >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
		   $this->db->where("bl.challan_date <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
		   $this->db->where("bl.challan_date BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}
		
		$this->db->group_by('bl.challan_id');
		$this->db->order_by('bl.challan_id','ASC');
		$this->db->limit($to,$from);
		$query = $this->db->get(); //print  $this->db->last_query();
		$totalrecord = $this->GetTotalChallanRecord(); 
	    	$perPage=50; $Pagination="";
	    	if($totalrecord >0){
		   	$Pagination = $this->getPagination($totalrecord,$perPage);
	    	} //print  $this->db->last_query();
		echo 
		'<table width="100%"  border="0" class="table table-responsive table-bordered table-hover custab">
			<thead>
			  <tr class="active">
			  	<th width="2%">'.$this->lang->line("sl").'</th>
			  	<th width="35%">'.$this->lang->line("customer").' '.$this->lang->line("details").'</th>
				<th width="15%">'.$this->lang->line("workorder_no").'</th>
				<th width="15%">'.$this->lang->line("workorder_date").'</th>
				<th width="15%">'.$this->lang->line("challan_no").'</th>
				<th width="10%">'.$this->lang->line("challan_date").'</th>
				<th width="8%" class="text-center hidden-print">'.$this->lang->line("options").'</th>
			  </tr>
			</thead>';
			  $i=1; $TotalBill=0; $TotalNetBill=0; $TotalLess=0; $TotalVat=0; $TotalAit=0; $payment_mode="";
			  foreach($query->result() as $row){
			   //if($i%2==0){ $tblrow="success";}else{$tblrow="warning";}echo "<tr class='default'>
			   echo "<tr class='default'>
			  	<td>".$i."</td>
			    <td>".$row->customer_full_name."<br> ".$row->address."</td>
				<td>".$row->workorder_no."</td>
				<td>".$row->workorder_date."</td>
				<td>".$row->challan_no."</td>
				<td>".$row->challan_date."</td>
				<td class='text-center align-middle hidden-print'>";
				 if($hasPrintPM){
				 echo "<div class='clearfix'></div><br><span data-toggle='tooltip' data-original-title='View Form'><a class='btn btn-success btn-sm' target='_blank' href='".base_url()."deliverychallan/ViewDCForm/".$row->challan_id."/".$row->workorder_id."'><i class='fa fa-print'></i> Form</a></span>";
				 }
			    echo "</td>
			  </tr>";
			  $i++;
			  }
		echo '</table>';
	    	echo "<div class='float-right'>$Pagination</div>";
	}
    
	function GetTotalChallanRecord(){
		$srcFrom			=$this->formatDate($this->input->post('srcFrom'));
		$srcTo				=$this->formatDate($this->input->post('srcTo'));
		$institute_id		=$this->input->post('src-institute');
		$branch_id			=$this->input->post('src-branch');
		$customer_id		=$this->input->post('src-customer');
		$workorder_id		=$this->input->post('src-workorder');
		
		$this->db->select('bl.*,c.customer_full_name,c.address,c.phone,c.fax,c.mobile,c.mobile,i.company_name,b.branch_name,b.branch_code,DATE_FORMAT(bl.po_date ,"%d-%m-%Y") as po_date',FALSE);
		$this->db->from(CHALLAN_MASTER_TBL." AS bl");
		$this->db->join(CLIENT_TBL.' AS c', 'c.customer_id=bl.customer_id','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=bl.institute_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=bl.branch_id','LEFT');
	  	$this->db->where("bl.status < 3");
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
			$this->db->where("c.account_id", $this->session->userdata('user_ref_id')); 
		}
		if($customer_id >0){
			  $this->db->where("bl.customer_id", $customer_id);  	
		}
		if($workorder_id >0){
			  $this->db->where("bl.workorder_id", $workorder_id);  	
		}
		if($srcFrom!="" && $srcTo==""){
		   $this->db->where("bl.challan_date >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
		   $this->db->where("bl.challan_date <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
		   $this->db->where("bl.challan_date BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}
		
		$this->db->group_by('bl.challan_id');
		$this->db->order_by('bl.challan_id','ASC');
		$query = $this->db->get();
		if($query->num_rows() >0){
			return $query->num_rows();
		}else{
			return 0;
		}//echo $this->db->last_query();
	}
	
	function DelRowRecord(){
		$id         =$this->input->post('id');
		$challan_id =$this->input->post('challan-id');
		$this->db->where('details_id',$id);
		$this->db->where('challan_id',$challan_id);
		$this->db->delete(CHALLAN_DETAILS_TBL); //echo $this->db->last_query();
	}
	
	function DelRecord(){
		$id = $this->input->post('id');
		$this->db->select('*');
		$this->db->from(CHALLAN_MASTER_TBL);
		$this->db->order_by('challan_id','DESC');
		$this->db->limit(1,0);
		$query = $this->db->get();
		if($query->num_rows() >0){
			$challan_id = $query->row()->challan_id;
		}else{
			$challan_id = 0;
		}
		if($id==$challan_id){
		$this->db->where('challan_id',$id);
		$this->db->delete(CHALLAN_MASTER_TBL);
		$this->db->where('challan_id',$id);
		$this->db->delete(CHALLAN_DETAILS_TBL);
		}else{
			$USQL= "UPDATE ".CHALLAN_MASTER_TBL." SET status='3' WHERE challan_id=$id";
			$this->db->query($USQL);
			$USQL= "UPDATE ".CHALLAN_DETAILS_TBL." SET status='3' WHERE challan_id=$id";
			$this->db->query($USQL);
		}
	}
	
	function ApprovePO(){
		$id = $this->input->post('id');
		if($id>0){
    		$this->db->select('*');
    		$this->db->from(CHALLAN_MASTER_TBL);
    		$this->db->where('challan_id',$id);
    		$this->db->where('status',0);
    		$query = $this->db->get();
    		if($query->num_rows() >0){
    			$USQL= "UPDATE ".CHALLAN_MASTER_TBL." SET status='1' WHERE challan_id=$id";
    			$this->db->query($USQL);
    			$USQL= "UPDATE ".CHALLAN_DETAILS_TBL." SET status='1' WHERE challan_id=$id";
    			$this->db->query($USQL);
    			return true;
    		}else{
    		   return true; 
    		}
		}else{
    		   return true; 
    	}
	}
	
	function UnapprovePO(){
		$id = $this->input->post('id');
		if($id>0){
    		$this->db->select('*');
    		$this->db->from(CHALLAN_MASTER_TBL);
    		$this->db->where('challan_id',$id);
    		$this->db->where('status',1);
    		$query = $this->db->get();
    		if($query->num_rows() >0){
    			$USQL= "UPDATE ".CHALLAN_MASTER_TBL." SET status='0' WHERE challan_id=$id";
    			$this->db->query($USQL);
    			$USQL= "UPDATE ".CHALLAN_DETAILS_TBL." SET status='0' WHERE challan_id=$id";
    			$this->db->query($USQL);
    			return true;
    		}else{
    		   return true; 
    		}
		}else{
    		   return true; 
    	}
	}
	function FillDetails(){
		$details_id	=$this->input->post('id');
		$this->db->select('*');
		$this->db->from(CHALLAN_DETAILS_TBL);
		$this->db->where('details_id', $details_id);
		$query = $this->db->get(); //echo $this->db->last_query();
		return $query->row();
	}
	
	function FillRecord(){
		$challan_id	=$this->input->post('id');
		$this->db->select('*');
		$this->db->from(CHALLAN_MASTER_TBL);
		$this->db->where('challan_id', $challan_id);
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
