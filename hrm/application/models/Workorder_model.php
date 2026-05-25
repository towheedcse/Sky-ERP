<?php 
class Workorder_model extends CI_Model {
		
	function __construct()
	{
		parent::__construct();
	}	
	
	function InsertDetailRecord(){
		$details_id		    = $this->input->post('details-id');
		if(empty($details_id)){
		$details_id		    = 0;
		}		    
		$workorder_id	    = $this->input->post('workorder-id');
		if(empty($workorder_id)){
			$workorder_id	= 0;
			$status		    = 1;
		}else{
			$this->db->select('status');
			$this->db->from(WORKORDER_MASTER_TBL); 
			$this->db->where('workorder_id', $workorder_id);
			$query 		= $this->db->get();
			$status		= $query->row()->status;
		}
		$workorder_no		= $this->input->post('workorder-no');
		$workorder_date		= $this->formatDate($this->input->post('workorder-date'));
		$institute_id		= $this->input->post('institute_id');
		$branch_id		    = $this->input->post('branch_id');
		$customer_id		= $this->input->post('customer_id');
		$category		    = $this->input->post('category');
		$product_description= $this->input->post('product-details'); 
		$product_sku        = $this->input->post('product-sku'); 
		$validity		    = $this->input->post('validity');
		$quantity		    = $this->input->post('quantity');
		$unit_price		    = $this->input->post('unit_price');
		$total_price		= ($quantity * $unit_price);
		$total_price 		= round($total_price,0,PHP_ROUND_HALF_UP);
		$vat_percentage		= $this->input->post('vat_percent');
		$ait_percentage		= $this->input->post('ait_percent');	
		$vat_amount         = (($total_price/100)*$vat_percentage);
		$ait_amount         = ((($total_price-$vat_amount)/100)*$ait_percentage);
		$remarks 		    = str_replace("U 0026", '&', $this->input->post('remarks'));
    	$created_by		    = $this->session->userdata('created_by');
									
		$ddata = array(
		'institute_id'    	=>$institute_id,
		'branch_id'    		=>$branch_id,
		'workorder_id'  	=>$workorder_id,
		'workorder_no' 	    =>$workorder_no,
		'workorder_date'	=>$workorder_date,
		'customer_id'   	=>$customer_id,
		'category'   	    =>$category,
		'product_description'=>$product_description,
		'product_sku'       =>$product_sku,
		'validity'    	    =>$validity,
		'quantity'    		=>$quantity,
		'unit_price'    	=>$unit_price,
		'total_price'  		=>$total_price,
		'vat_percentage'    =>$vat_percentage,
		'vat_amount'    	=>$vat_amount,
		'ait_percentage'    =>$ait_percentage,
		'ait_amount'    	=>$ait_amount,
		'remarks'  		    =>$remarks,
		'status'  		    =>$status,
		'created_by'  		=>$created_by
	    );
	   	   
	    if($customer_id >0 && $workorder_date!="" && $quantity >0){
		if($details_id ==0){ 
		 $this->db->insert(WORKORDER_DETAILS_TBL, $ddata);
		}else{
		$this->EditDetailRecord($details_id);
		}
		//print  $this->db->last_query();
	    }
		
    }
    function EditDetailRecord($details_id){		    
		$workorder_id		= $this->input->post('workorder-id');
		if(empty($workorder_id)){
			$workorder_id	= 0;
			$status		= 1;
		}else{
			$this->db->select('status');
			$this->db->from(WORKORDER_MASTER_TBL);
			$this->db->where('workorder_id', $workorder_id);
			$query 		= $this->db->get();
			$status		= $query->row()->status;
		}
		$workorder_no		=$this->input->post('workorder-no');
		$workorder_date		=$this->formatDate($this->input->post('workorder-date'));
		$institute_id		=$this->input->post('institute_id');
		$branch_id		    =$this->input->post('branch_id');
		$customer_id		=$this->input->post('customer_id');
		$category		    = $this->input->post('category');
		$product_description=$this->input->post('product-details'); 
		$product_sku        = $this->input->post('product-sku'); 
		$validity		    =$this->input->post('validity');
		$quantity		    =$this->input->post('quantity');
		$unit_price		    =$this->input->post('unit_price');
		$total_price		=($quantity * $unit_price);
		$total_price 		=round($total_price,0,PHP_ROUND_HALF_UP);
		$vat_percentage		= $this->input->post('vat_percent');
		$ait_percentage		= $this->input->post('ait_percent');	
		$vat_amount         = (($total_price/100)*$vat_percentage);
		$ait_amount         = ((($total_price-$vat_amount)/100)*$ait_percentage);	
		$remarks 		    =str_replace("U 0026", '&', $this->input->post('remarks'));
    	$modified_by		=$this->session->userdata('created_by');
		$modified_time  	=date("Y-m-d H:i:s");		
						
		$ddata = array(
		'institute_id'    	=>$institute_id,
		'branch_id'    		=>$branch_id,
		'workorder_no' 	    =>$workorder_no,
		'workorder_date'	=>$workorder_date,
		'customer_id'   	=>$customer_id,
		'category'   	    =>$category,
		'product_description'=>$product_description,
		'product_sku'       =>$product_sku,
		'validity'    	    =>$validity,
		'quantity'    		=>$quantity,
		'unit_price'    	=>$unit_price,
		'total_price'  		=>$total_price,
		'vat_percentage'    =>$vat_percentage,
		'vat_amount'    	=>$vat_amount,
		'ait_percentage'    =>$ait_percentage,
		'ait_amount'    	=>$ait_amount,
		'remarks'  		    =>$remarks,
		'status'  		    =>$status,
		'modified_by'  		=>$modified_by,
		'modified_time'		=>$modified_time
	    	);
	    	$this->db->where('workorder_id',$workorder_id);
		$this->db->where('customer_id',$customer_id);
		$this->db->where('details_id',$details_id);
		$this->db->update(WORKORDER_DETAILS_TBL, $ddata);
		//print  $this->db->last_query();
	}
		
	function saveWorkorderMaster($workorder_id){
		$institute_id		=$this->input->post('institute_id');
		$branch_id		=$this->input->post('branch_id');
		$workorder_no 		=$this->input->post('workorder_no');
		$workorder_type		=$this->input->post('workorder_type');
		$workorder_date		=$this->formatDate($this->input->post('workorder_date'));
		$delivery_date		=$this->formatDate($this->input->post('delivery_date'));		
		$customer_id		=$this->input->post('customer_id');
		$salesman_id		=$this->input->post('salesman_id');
		$oem 	            	= str_replace("U 0026", '&', $this->input->post('oem'));
		$total_bill		= $this->input->post('total_bill');
		$discount_percentage	= $this->input->post('discount_percentage');
		$discount_amount	= $this->input->post('discount_amount');
		$sub_total		= $this->input->post('sub_total');
		$including_vat		= $this->input->post('including_vat');		
		$vat_percentage		= $this->input->post('vat_percentage');	
		$vat_amount		= $this->input->post('vat_amount');	
		$grand_total		= $this->input->post('grand_total');
		$ait_percentage		= $this->input->post('ait_percentage');	
		$ait_amount		= $this->input->post('ait_amount');	
		$midman_commission	= $this->input->post('midman_commission');
		$workorder_attach	= $this->input->post('workorder_attach');
		$payment_mode		= $this->input->post('payment_mode');
		$payment_terms 	    	= str_replace("U 0026", '&', $this->input->post('payment_terms'));
		$workorder_note 	= str_replace("U 0026", '&', $this->input->post('workorder_note'));
		
		if(empty($discount_percentage)){$discount_percentage=0;} 
		if(empty($discount_amount)){$discount_amount=0;} 
		if(empty($including_vat)){$including_vat=0;}
		if($discount_percentage >0 && $discount_amount==0){
		   $discount_amount = (($total_bill/100) * $discount_percentage);
		}
		$sub_total			= ($total_bill - $discount_amount);
		if(empty($vat_percentage)){$vat_percentage=0;} if(empty($vat_amount)){$vat_amount=0;}
		if($vat_percentage >0 && $vat_amount==0){
		   $vat_amount = (($sub_total/100) * $vat_percentage);
		}
		if($including_vat==0){
		    $grand_total = ($sub_total + $vat_amount);
		}else{
		    $grand_total = ($sub_total - $vat_amount);
		}

		if(empty($ait_percentage)){$ait_percentage=0;} if(empty($ait_percentage)){$ait_percentage=0;}
		if($ait_percentage >0 && $ait_amount==0){
		   $ait_amount = (($grand_total/100) * $ait_percentage);
		}		
		
		if($including_vat==0){
		    $net_bill_amount = ($grand_total + $ait_amount);
		}else{
		    $net_bill_amount = ($grand_total - $ait_amount);
		}

		$net_bill_amount 	= round($net_bill_amount,0,PHP_ROUND_HALF_UP);
		
		$created_by		= $this->session->userdata('created_by');
		if($this->input->post('description')!=""){
			$description 	= str_replace("U 0026", '&', $this->input->post('description'));
		}else{
			$description	= "The total receivable amount of against customer workorder";
		}
		$description	= $this->db->escape_str($description);			
		if($total_bill >0){
		   		   	   
		   if($workorder_id >0){
		        $modified_time      = date("Y-m-d H:i:s");
		        $workorder_attach	= $_FILES['workorder_attach'];  
        		if($workorder_attach!=""){
        			$workorder_attach =$this->UploadAttachment($workorder_no);				
        		}else{
        			$ssql   = "SELECT workorder_attach FROM ".WORKORDER_MASTER_TBL." WHERE workorder_id = $workorder_id AND institute_id = $institute_id AND branch_id = $branch_id";
        			$squery = $this->db->query($ssql);				
        			if($squery->num_rows() >0){				   
        			   $workorder_attach = $squery->row()->workorder_attach;
        			}
        		}
			$mdata = array(
				'institute_id'    	=>$institute_id,
				'branch_id'    		=>$branch_id,
				'workorder_no'  	=>$workorder_no,
				'workorder_type' 	=>$workorder_type,
				'workorder_date'	=>$workorder_date,
				'delivery_date'   	=>$delivery_date,
				'customer_id'	    	=>$customer_id,
				'salesman_id'    	=>$salesman_id,
				'oem'    	        =>$oem,
				'total_bill'    	=>$total_bill,
				'discount_persent'  	=>$discount_percentage,
				'discount_amount'   	=>$discount_amount,
				'sub_total'  	    	=>$sub_total,
				'including_vat'  	=>$including_vat,
				'vat_percentage'  	=>$vat_percentage,
				'vat_amount'   		=>$vat_amount,
				'grand_total'  	    	=>$grand_total,
				'ait_percentage'    	=>$ait_percentage,
				'ait_amount'        	=>$ait_amount,
				'midman_commission' 	=>$midman_commission,
				'net_bill_amount'  	=>$net_bill_amount,
				'payment_mode'  	=>$payment_mode,
				'payment_terms'  	=>$payment_terms,
				'workorder_attach'  	=>$workorder_attach,
				'workorder_note'  	=>$workorder_note,
				'description'  	    	=>$description,
				'modified_by'  		=>$created_by,
				'modified_time'  	=>$modified_time
			);
			$this->db->where('workorder_id',$workorder_id);
			$this->db->where('customer_id',$customer_id);
			$this->db->where('institute_id',$institute_id);
			$this->db->update(WORKORDER_MASTER_TBL, $mdata);	
		    }else{
			   //$bill_no			= $this->getBillID($institute_id,$branch_id,$billing_month,$bill_date);
			   $workorder_attach    = $this->UploadAttachment($workorder_no);
			   //======== Save Workorder Master ========
			   $mdata = array(
					'institute_id'    	=>$institute_id,
					'branch_id'    		=>$branch_id,
					'workorder_no'  	=>$workorder_no,
					'workorder_type' 	=>$workorder_type,
					'workorder_date'	=>$workorder_date,
					'delivery_date'   	=>$delivery_date,
					'customer_id'	    	=>$customer_id,
					'salesman_id'    	=>$salesman_id,
					'oem'    	        =>$oem,
					'total_bill'    	=>$total_bill,
					'discount_persent'  	=>$discount_percentage,
					'discount_amount'   	=>$discount_amount,
					'sub_total'  	    	=>$sub_total,
					'including_vat'  	=>$including_vat,
					'vat_percentage'  	=>$vat_percentage,
					'vat_amount'   		=>$vat_amount,
					'grand_total'  	    	=>$grand_total,
					'ait_percentage'    	=>$ait_percentage,
					'ait_amount'        	=>$ait_amount,
					'midman_commission' 	=>$midman_commission,
					'net_bill_amount'  	=>$net_bill_amount,
					'payment_mode'  	=>$payment_mode,
					'payment_terms'  	=>$payment_terms,
					'workorder_attach'  	=>$workorder_attach,
					'workorder_note'  	=>$workorder_note,
					'description'  	    	=>$description,
					'created_by'  		=>$created_by
				);
				$this->db->insert(WORKORDER_MASTER_TBL, $mdata); //print $this->db->last_query();
				$workorder_id = $this->db->insert_id(); 
				//====== Update Bill Details ========
				$USQL= "UPDATE ".WORKORDER_DETAILS_TBL." SET workorder_id='".$workorder_id."' WHERE workorder_id=0 AND institute_id = $institute_id AND branch_id = $branch_id AND customer_id = $customer_id AND created_by = ".$created_by;
				$this->db->query($USQL);
		    }
			
				
		}// end if total_amount
		
	}
	//===== End Workorder ======= 
	
	function UploadAttachment($pdf_id){
	    if($_FILES['workorder_attach']){
			$targetfolder = ASSETS.'/pdf/workorder/';
		    $FileType       = pathinfo($targetfolder.basename($_FILES["workorder_attach"]["name"]),PATHINFO_EXTENSION);
	        $targetfolder   = ASSETS.'/pdf/workorder/'.$pdf_id.".".$FileType;
  
            $ok=1;
            //$file_type=$_FILES['workorder_attach']['type'];
            
            if ($FileType=="jpg" || $FileType=="jpeg" || $FileType=="gif" || $FileType=="docx" || $FileType=="doc" || $FileType=="pdf") {
            
                if(move_uploaded_file($_FILES['workorder_attach']['tmp_name'], $targetfolder))
                
                { 
                
                return $pdf_id.".".$FileType;
                
                }
                
                else {
                
                echo "Problem uploading file";
                
                }
            }else {
             echo "You may only upload pdf, docx, doc files.<br>";
            }
		  
		}else{
		    return false;
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
	
	function getBillID($institute_id,$branch_id,$billing_month,$bill_date){
		$SL=""; $TotalNo=0; $BillNo = ""; $yearArr = explode("-",$bill_date);
		$ssql = "SELECT COUNT(*) as total FROM ".WORKORDER_MASTER_TBL." WHERE institute_id =$institute_id AND branch_id = $branch_id AND billing_month=$billing_month AND status < 5";
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
	function GetAjaxDetailList(){		
		$workorder_id		= $this->input->post('workorder-id');
		if(empty($workorder_id)){
			$workorder_id	= 0;
			$status		    = 1;
		}
		$customer_id		=$this->input->post('customer_id'); 
		$institute_id		=$this->input->post('institute_id');
		$branch_id			=$this->input->post('branch_id');
		
		$menu_slug = $this->uri->segment(1);
		$hasEditPM = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM  = $this->Site_model->hasOptionPermission($menu_slug,"Delete");
		
		$bdcsql = "SELECT category FROM ".WORKORDER_DETAILS_TBL." WHERE institute_id = $institute_id AND branch_id = $branch_id AND customer_id=$customer_id AND workorder_id=$workorder_id";
		$bdcsql.= " GROUP BY category ORDER BY category ASC"; 
		$cquery = $this->db->query($bdcsql); 
		if($cquery->num_rows() >0){	
		  echo 
		  '<table width="100%"  border="0" class="table table-responsive table-bordered table-hover custab">
			<thead>
			  <tr class="active">
			  	<th width="2%">'.$this->lang->line("sl").'</th>
				<th width="25%">'.$this->lang->line("product_description").'</th>
				<th width="13%">'.$this->lang->line("product_sku").'</th>
				<th width="10%" class="text-right">'.$this->lang->line("validity").'</th>
				<th width="10%" class="text-right">'.$this->lang->line("quantity").'</th>
				<th width="10%" class="text-right">'.$this->lang->line("unit_price").'</th>
				<th width="12%" class="text-right">'.$this->lang->line("total_amount").'</th>
				<th width="12%" class="text-center">'.$this->lang->line("options").'</th>
			  </tr>
			</thead>';
			  $i=1; $GTotalBill=0; $GTotalVat=0; $GTotalAit=0; $category_name="";
			  foreach($cquery->result() as $crow){
			  if($crow->category==1){
			    $category_name = "Hardware";  
			  }elseif($crow->category==2){
			    $category_name = "Software";  
			  }elseif($crow->category==3){
			    $category_name = "Support";  
			  }elseif($crow->category==4){
			    $category_name = "Training";  
			  }elseif($crow->category==5){
			    $category_name = "AMC";  
			  }
			  echo "<tr class='bg-light'>
			  	<th colspan='8'>".$category_name."</th>
			  </tr>";
			  
			  $bdsql = "SELECT * FROM ".WORKORDER_DETAILS_TBL." WHERE institute_id = $institute_id AND branch_id = $branch_id AND customer_id=$customer_id AND workorder_id=$workorder_id AND category = $crow->category";
		      $bdsql.= " GROUP BY details_id ORDER BY details_id ASC"; //echo $bdsql;
		      $query = $this->db->query($bdsql); 
		      
		      $i=1; $TotalBill=0; $TotalVat=0; $TotalAit=0;
		      
		      foreach($query->result() as $row){
			  //if($i%2==0){ $tblrow="success";}else{$tblrow="warning";}
			  $TotalBill+=$row->total_price; $GTotalBill+=$row->total_price;
			  $TotalVat+=$row->vat_amount;   $GTotalVat+=$row->vat_amount;
			  $TotalAit+=$row->ait_amount;   $GTotalAit+=$row->ait_amount;
			  echo "<tr class='default'>
			  	<td>".$i."</td>
				<td>".$row->product_description."</td>
				<td>".$row->product_sku."</td>
				<td class='text-left'>".$row->validity."</td>
			  	<td class='text-right'>".$row->quantity."</td>
				<td class='text-right'>".$row->unit_price."</td>
				<td class='text-right'>".$row->total_price."</td>
				<td align='center'>";
				if($hasEditPM){
				echo "<span data-toggle='tooltip' data-original-title='Edit'><a class='btn btn-info btn-xs' data-toggle='modal' onclick=editRow('".$row->details_id."','".$row->workorder_id."') id='".$row->details_id."' href='#'><i class='fa fa-edit'></i></a></span> &nbsp;";
				}
				if($hasDelPM){				
				echo "<span data-toggle='tooltip' data-original-title='Delete'><a class='btn btn-danger btn-xs' data-toggle='modal' onclick=deleteRow('".$row->details_id."','".$row->workorder_id."') id='".$row->details_id."' href='#deleteDraftModal'><i class='fa fa-trash'></i></a></span>";
				}
				echo "</td>
				</tr>";
			  $i++;
			  }
			  echo "<tr class='bg-light'>
			  	<th colspan='6'>".$this->lang->line("total")." $category_name Amount</th>
				<th class='text-right'>".number_format($TotalBill, 2, '.', ',')."</th>
				<th class='text-right'>&nbsp;</th>
			  </tr>";
			  }
			  echo "<tr class='bg-light'>
			  	<th colspan='6'>".$this->lang->line("total_amount")."</th>
				<th class='text-right'>".number_format($GTotalBill, 2, '.', ',')."<input type='hidden' name='total_bill_amount' id='total_bill_amount' value='".$GTotalBill."'></th>
				<th class='text-right'>&nbsp;</th>
			  </tr>";
		  echo '</table>##&##'.$GTotalBill.'##&##'.$GTotalVat.'##&##'.$GTotalAit;
		}//end num_rows	
	}
	function GetProductDetailList(){		
		$workorder_id		= $this->input->post('workorder-id');
		if(empty($workorder_id)){
			$workorder_id	= 0;
			$status		    = 1;
		}
		$customer_id		=$this->input->post('customer_id'); 
		$institute_id		=$this->input->post('institute_id');
		$branch_id			=$this->input->post('branch_id');
		
		$menu_slug = $this->uri->segment(1);
		$hasEditPM = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM  = $this->Site_model->hasOptionPermission($menu_slug,"Delete");
		
		$bdsql = "SELECT * FROM ".WORKORDER_DETAILS_TBL." WHERE institute_id = $institute_id AND branch_id = $branch_id AND customer_id=$customer_id AND workorder_id=$workorder_id";
		$bdsql.= " GROUP BY details_id ORDER BY details_id ASC"; //echo $bdsql;
		$query = $this->db->query($bdsql); 
		$TBLGRIDE="";
		if($query->num_rows() >0){	
		  $TBLGRIDE.= 
		  '<table width="100%"  border="0" class="table table-responsive table-bordered table-hover custab">
			<thead>
			  <tr class="active">
			  	<th width="2%">'.$this->lang->line("sl").'</th>
				<th width="30%">'.$this->lang->line("product_description").'</th>
				<th width="13%">'.$this->lang->line("product_sku").'</th>
				<th width="12%" class="text-right">'.$this->lang->line("validity").'</th>
				<th width="12%" class="text-right">'.$this->lang->line("quantity").'</th>
				<th width="13%" class="text-right">'.$this->lang->line("unit_price").'</th>
				<th width="13%" class="text-right">'.$this->lang->line("total_amount").'</th>
				<th width="13%" class="text-center">'.$this->lang->line("options").'</th>
			  </tr>
			</thead>';
			  $i=1; $TotalBill=0; $TotalVat=0; $TotalAit=0;
			  foreach($query->result() as $row){
			  //if($i%2==0){ $tblrow="success";}else{$tblrow="warning";}
			  $TotalBill+=$row->total_price;
			  $TotalVat+=$row->vat_amount;
			  $TotalAit+=$row->ait_amount;
			  $TBLGRIDE.= "<tr class='default'>
			  	<td>".$i."</td>
				<td>".$row->product_description."</td>
				<td>".$row->product_sku."</td>
				<td class='text-left'>".$row->validity."</td>
			  	<td class='text-right'>".$row->quantity."</td>
				<td class='text-right'>".$row->unit_price."</td>
				<td class='text-right'>".$row->total_price."</td>
				<td align='center'>";
				if($hasEditPM){
				$TBLGRIDE.= "<span data-toggle='tooltip' data-original-title='Edit'><a class='btn btn-info btn-xs' data-toggle='modal' onclick=editRow('".$row->details_id."','".$row->workorder_id."') id='".$row->details_id."' href='#'><i class='fa fa-edit'></i></a></span> &nbsp;";
				}
				if($hasDelPM){				
				$TBLGRIDE.= "<span data-toggle='tooltip' data-original-title='Delete'><a class='btn btn-danger btn-xs' data-toggle='modal' onclick=deleteRow('".$row->details_id."','".$row->workorder_id."') id='".$row->details_id."' href='#deleteDraftModal'><i class='fa fa-trash'></i></a></span>";
				}
				$TBLGRIDE.= "</td>
				</tr>";
			  $i++;
			  }
			  $TBLGRIDE.= "<tr class='bg-light'>
			  	<th colspan='6'>".$this->lang->line("total_amount")."</th>
				<th class='text-right'>".$TotalBill."<input type='hidden' name='total_bill_amount' id='total_bill_amount' value='".$TotalBill."'></th>
				<th class='text-right'>&nbsp;</th>
			  </tr>";
		  $TBLGRIDE.= '</table>##&##'.$TotalBill.'##&##'.$TotalVat.'##&##'.$TotalAit;
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
		$hasEditPM = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM  = $this->Site_model->hasOptionPermission($menu_slug,"Delete");
		$hasPrintPM= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$srcFrom			=$this->formatDate($this->input->post('srcFrom'));
		$srcTo				=$this->formatDate($this->input->post('srcTo'));
		$institute_id		=$this->input->post('src-institute');
		$branch_id			=$this->input->post('src-branch');
		$workorder_no		=$this->input->post('src-workorder');
		$customer_id		=$this->input->post('src-customer');
		
	   	$from	=$this->input->post('from');
		$to	=$this->input->post('to');
		if($from==""){ $from=0;} if($to==""){ $to=50;}
		$this->db->select('bl.*,p.account_id,p.head_id,p.account_name,p.bangla_name,p.account_details,p.mobile,i.company_name,b.branch_name,b.branch_code,DATE_FORMAT(bl.workorder_date ,"%d-%m-%Y") as workorder_date,DATE_FORMAT(bl.delivery_date ,"%d-%m-%Y") as delivery_date',FALSE);
		$this->db->from(WORKORDER_MASTER_TBL." AS bl");
		$this->db->join(ACC_HEAD_TBL.' AS p', 'p.account_id=bl.customer_id','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=bl.institute_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=bl.branch_id','LEFT');
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
		if($customer_id >0){
			  $this->db->where("bl.customer_id", $customer_id);  	
		}
		if($workorder_no >0){
			  $this->db->where("bl.workorder_no", $workorder_no);  	
		}
		if($srcFrom!="" && $srcTo==""){
		   $this->db->where("bl.workorder_date >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
		   $this->db->where("bl.workorder_date <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
		   $this->db->where("bl.workorder_date BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}
		
		$this->db->group_by('bl.workorder_id');
		$this->db->order_by('bl.workorder_id','ASC');
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
				<th width="20%">'.$this->lang->line("workorder").' '.$this->lang->line("details").'</th>
			  	<th width="24%">'.$this->lang->line("customer").' '.$this->lang->line("details").'</th>
				<th width="8%">'.$this->lang->line("total_amount").'</th>
				<th width="8%">'.$this->lang->line("discount").'</th>
				<th width="10%">'.$this->lang->line("vat")." ".$this->lang->line("amount").'</th>
				<th width="10%">'.$this->lang->line("ait")." ".$this->lang->line("amount").'</th>
				<th width="10%">'.$this->lang->line("net_amount").'</th>
				<th width="8%" class="text-center hidden-print">'.$this->lang->line("options").'</th>
			  </tr>
			</thead>';
			  $i=1; $TotalBill=0; $TotalNetBill=0; $TotalLess=0; $TotalVat=0; $TotalAit=0; $workorder_type="";
			  foreach($query->result() as $row){
			   $TotalBill+= $row->total_bill;
			   $TotalLess+= $row->discount_amount;
			   $TotalVat+= $row->vat_amount;
			   $TotalAit+= $row->ait_amount;
			   $TotalNetBill+= $row->net_bill_amount;
			   $salesman_details = $this->GetAccountName($row->salesman_id);
			   //if($i%2==0){ $tblrow="success";}else{$tblrow="warning";}
			   if($row->workorder_type==1){ $workorder_type="Fresh";}elseif($row->workorder_type==2){ $workorder_type="Renual";}else{$workorder_type="Addon";}
			   echo "<tr class='default'>
			  	<td>".$i."</td>
				<td>Workorder No: ".$row->workorder_no."<br> Date: ".$row->workorder_date."<br> Workorder Type: ".$workorder_type."</td>
			    <td>".$row->account_name."<br> ".$row->account_details."</td>
				<td>".$row->total_bill."</td>
				<td>".$row->discount_amount."</td>
				<td>".$row->vat_amount."</td>
				<td>".$row->ait_amount."</td>
				<td>".$row->net_bill_amount."</td>
				<td class='text-center align-middle hidden-print'>";
		    	if($hasEditPM){
				echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Edit'><a class='btn btn-info btn-xs' onclick=editRecord('".$row->workorder_id."') id='".$row->workorder_id."' href='#'><i class='fas fa-edit'></i></a></span>&nbsp;";
				}
				if($hasDelPM){				
				echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Delete'><a class='btn btn-danger btn-xs' data-toggle='modal' onclick=deleteRecord('".$row->workorder_id."') id='".$row->workorder_id."' data-target='#deleteModal'><i class='fa fa-trash'></i></a></span>";
				}
				if($hasPrintPM){
				echo "<div class='clearfix'></div><br><span data-toggle='tooltip' data-original-title='View Form'><a class='btn btn-success btn-sm' target='_blank' href='".base_url()."workorder/ViewWOForm/".$row->workorder_id."/".$row->customer_id."'><i class='fa fa-print'></i> Form</a></span>";
				}
			    echo "</td>
			  </tr>";
			  $i++;
			  }
			  echo '
			  <tr class="active">
			  	<th colspan="3"  class="text-right">Grand Total</th>
				<th>'.number_format($TotalBill, 2, '.', ',').'</th>
				<th>'.number_format($TotalLess, 2, '.', ',').'</th>
				<th>'.number_format($TotalVat, 2, '.', ',').'</th>
				<th>'.number_format($TotalAit, 2, '.', ',').'</th>
				<th>'.number_format($TotalNetBill, 2, '.', ',').'</th>
				<th>&nbsp;</th>
			  </tr>';
		echo '</table>';
	    	echo "<div class='float-right'>$Pagination</div>";
	}
    
	function GetTotalRecord(){
		$srcFrom			=$this->formatDate($this->input->post('srcFrom'));
		$srcTo				=$this->formatDate($this->input->post('srcTo'));
		$institute_id		=$this->input->post('src-institute');
		$branch_id			=$this->input->post('src-branch');
		$workorder_no		=$this->input->post('src-workorder');
		$customer_id		=$this->input->post('src-customer');
		$this->db->select('bl.*,p.account_id,p.head_id,p.account_name,p.bangla_name,p.account_details,p.mobile,i.company_name,b.branch_name,b.branch_code,DATE_FORMAT(bl.workorder_date ,"%d-%m-%Y") as workorder_date,DATE_FORMAT(bl.delivery_date ,"%d-%m-%Y") as delivery_date',FALSE);
		$this->db->from(WORKORDER_MASTER_TBL." AS bl");
		$this->db->join(ACC_HEAD_TBL.' AS p', 'p.account_id=bl.customer_id','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=bl.institute_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=bl.branch_id','LEFT');
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
		if($customer_id >0){
			  $this->db->where("bl.customer_id", $customer_id);  	
		}
		if($workorder_no >0){
			  $this->db->where("bl.workorder_no", $workorder_no);  	
		}
		if($srcFrom!="" && $srcTo==""){
		   $this->db->where("bl.workorder_date >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
		   $this->db->where("bl.workorder_date <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
		   $this->db->where("bl.workorder_date BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}
		
		$this->db->group_by('bl.workorder_id');
		$this->db->order_by('bl.workorder_id','ASC');
		$query = $this->db->get();
		if($query->num_rows() >0){
			return $query->num_rows();
		}else{
			return 0;
		}//echo $this->db->last_query();
	}
	
   	function GetRecordListGrid(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');
		$hasEditPM = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM  = $this->Site_model->hasOptionPermission($menu_slug,"Delete");
		$hasPrintPM= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$srcFrom			=$this->formatDate($this->input->post('srcFrom'));
		$srcTo				=$this->formatDate($this->input->post('srcTo'));
		$institute_id		=$this->input->post('src-institute');
		$branch_id			=$this->input->post('src-branch');
		$workorder_no		=$this->input->post('src-workorder');
		$customer_id		=$this->input->post('src-customer');
		
	   	$from	=$this->input->post('from');
		$to	=$this->input->post('to');
		if($from==""){ $from=0;} if($to==""){ $to=50;}
		$this->db->select('bl.*,p.account_id,p.head_id,p.account_name,p.bangla_name,p.account_details,p.mobile,i.company_name,b.branch_name,b.branch_code,DATE_FORMAT(bl.workorder_date ,"%d-%m-%Y") as workorder_date,DATE_FORMAT(bl.delivery_date ,"%d-%m-%Y") as delivery_date',FALSE);
		$this->db->from(WORKORDER_MASTER_TBL." AS bl");
		$this->db->join(ACC_HEAD_TBL.' AS p', 'p.account_id=bl.customer_id','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=bl.institute_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=bl.branch_id','LEFT');
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
		if($customer_id >0){
			  $this->db->where("bl.customer_id", $customer_id);  	
		}
		if($workorder_no >0){
			  $this->db->where("bl.workorder_no", $workorder_no);  	
		}
		if($srcFrom!="" && $srcTo==""){
		   $this->db->where("bl.workorder_date >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
		   $this->db->where("bl.workorder_date <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
		   $this->db->where("bl.workorder_date BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}
		
		$this->db->group_by('bl.workorder_id');
		$this->db->order_by('bl.workorder_id','ASC');
		$this->db->limit($to,$from);
		$query = $this->db->get(); //print  $this->db->last_query();
		$totalrecord = $this->GetTotalRecordList();
	    	$perPage=50; $Pagination="";
	    	if($totalrecord >0){
		   	$Pagination = $this->getPagination($totalrecord,$perPage);
	    	} //print  $this->db->last_query();
		echo 
		'<table width="100%"  border="0" class="table table-responsive table-bordered table-hover custab">
			<thead>
			  <tr class="active">
			  	<th width="2%">'.$this->lang->line("sl").'</th>
				<th width="20%">'.$this->lang->line("workorder").' '.$this->lang->line("details").'</th>
			  	<th width="24%">'.$this->lang->line("customer").' '.$this->lang->line("details").'</th>
				<th width="8%">'.$this->lang->line("total_amount").'</th>
				<th width="8%">'.$this->lang->line("discount").'</th>
				<th width="10%">'.$this->lang->line("vat")." ".$this->lang->line("amount").'</th>
				<th width="10%">'.$this->lang->line("ait")." ".$this->lang->line("amount").'</th>
				<th width="10%">'.$this->lang->line("net_amount").'</th>
				<th width="8%" class="text-center hidden-print">'.$this->lang->line("options").'</th>
			  </tr>
			</thead>';
			  $i=1; $TotalBill=0; $TotalNetBill=0; $TotalLess=0; $TotalVat=0; $TotalAit=0; $workorder_type="";
			  foreach($query->result() as $row){
			   $TotalBill+= $row->total_bill;
			   $TotalLess+= $row->discount_amount;
			   $TotalVat+= $row->vat_amount;
			   $TotalAit+= $row->ait_amount;
			   $TotalNetBill+= $row->net_bill_amount;
			   $salesman_details = $this->GetAccountName($row->salesman_id);
			   //if($i%2==0){ $tblrow="success";}else{$tblrow="warning";}
			   if($row->workorder_type==1){ $workorder_type="Fresh";}elseif($row->workorder_type==2){ $workorder_type="Renual";}else{$workorder_type="Addon";}
			   echo "<tr class='default'>
			  	<td>".$i."</td>
				<td>Workorder No: ".$row->workorder_no."<br> Date: ".$row->workorder_date."<br> Workorder Type: ".$workorder_type."</td>
			    <td>".$row->account_name."<br> ".$row->account_details."</td>
				<td>".$row->total_bill."</td>
				<td>".$row->discount_amount."</td>
				<td>".$row->vat_amount."</td>
				<td>".$row->ait_amount."</td>
				<td>".$row->net_bill_amount."</td>
				<td class='text-center align-middle hidden-print'>";
				if($hasPrintPM){
				echo "<div class='clearfix'></div><br><span data-toggle='tooltip' data-original-title='View Form'><a class='btn btn-success btn-sm' target='_blank' href='".base_url()."workorder/ViewWOForm/".$row->workorder_id."/".$row->customer_id."'><i class='fa fa-print'></i> Print WO</a></span>";
				}
				
				if($row->workorder_attach!=""){
				echo "<div class='clearfix'></div><br><span data-toggle='tooltip' data-original-title='Download Att'><a class='btn btn-success btn-sm' target='_blank' href='".base_url()."/".ASSETS."/pdf/workorder/".$row->workorder_attach."'><i class='fa fa-download'></i> Download</a></span>";
				}
			    echo "</td>
			  </tr>";
			  $i++;
			  }
			  echo '
			  <tr class="active">
			  	<th colspan="3"  class="text-right">Grand Total</th>
				<th>'.number_format($TotalBill, 2, '.', ',').'</th>
				<th>'.number_format($TotalLess, 2, '.', ',').'</th>
				<th>'.number_format($TotalVat, 2, '.', ',').'</th>
				<th>'.number_format($TotalAit, 2, '.', ',').'</th>
				<th>'.number_format($TotalNetBill, 2, '.', ',').'</th>
				<th>&nbsp;</th>
			  </tr>';
		echo '</table>';
	    	echo "<div class='float-right'>$Pagination</div>";
	}
    
	function GetTotalRecordList(){
		$srcFrom			=$this->formatDate($this->input->post('srcFrom'));
		$srcTo				=$this->formatDate($this->input->post('srcTo'));
		$institute_id		=$this->input->post('src-institute');
		$branch_id			=$this->input->post('src-branch');
		$workorder_no		=$this->input->post('src-workorder');
		$customer_id		=$this->input->post('src-customer');
		$this->db->select('bl.*,p.account_id,p.head_id,p.account_name,p.bangla_name,p.account_details,p.mobile,i.company_name,b.branch_name,b.branch_code,DATE_FORMAT(bl.workorder_date ,"%d-%m-%Y") as workorder_date,DATE_FORMAT(bl.delivery_date ,"%d-%m-%Y") as delivery_date',FALSE);
		$this->db->from(WORKORDER_MASTER_TBL." AS bl");
		$this->db->join(ACC_HEAD_TBL.' AS p', 'p.account_id=bl.customer_id','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=bl.institute_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=bl.branch_id','LEFT');
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
		if($customer_id >0){
			  $this->db->where("bl.customer_id", $customer_id);  	
		}
		if($workorder_no >0){
			  $this->db->where("bl.workorder_no", $workorder_no);  	
		}
		if($srcFrom!="" && $srcTo==""){
		   $this->db->where("bl.workorder_date >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
		   $this->db->where("bl.workorder_date <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
		   $this->db->where("bl.workorder_date BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}
		
		$this->db->group_by('bl.workorder_id');
		$this->db->order_by('bl.workorder_id','ASC');
		$query = $this->db->get();
		if($query->num_rows() >0){
			return $query->num_rows();
		}else{
			return 0;
		}//echo $this->db->last_query();
	}
	function DelRowRecord(){
		$id             =$this->input->post('id');
		$workorder_id   =$this->input->post('workorder-id');
		$this->db->where('details_id',$id);
		$this->db->where('workorder_id',$workorder_id);
		$this->db->delete(WORKORDER_DETAILS_TBL); //echo $this->db->last_query();
	}
	
	function DelRecord(){
		$id =$this->input->post('id');
		$this->db->where('workorder_id',$id);
		$this->db->delete(WORKORDER_MASTER_TBL);
		$this->db->where('workorder_id',$id);
		$this->db->delete(WORKORDER_DETAILS_TBL);
	}
	
	function FillDetails(){
		$details_id	=$this->input->post('id');
		$this->db->select('*');
		$this->db->from(WORKORDER_DETAILS_TBL);
		$this->db->where('details_id', $details_id);
		$query = $this->db->get(); //echo $this->db->last_query();
		return $query->row();
	}
	
	function FillRecord(){
		$workorder_id	=$this->input->post('id');
		$this->db->select('*');
		$this->db->from(WORKORDER_MASTER_TBL);
		$this->db->where('workorder_id', $workorder_id);
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
