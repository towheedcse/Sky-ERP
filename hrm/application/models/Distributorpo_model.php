<?php 
class Distributorpo_model extends CI_Model {
		
	function __construct()
	{
		parent::__construct();
	}	
	
	function InsertDetailRecord(){
		$details_id		    = $this->input->post('details-id');
		if(empty($details_id)){
		$details_id		    = 0;
		}		    
		$po_id	            = $this->input->post('po-id');
		if(empty($po_id)){
			$po_id	        = 0;
			$status		    = 0;
		}else{
			$this->db->select('status');
			$this->db->from(DISTRIPO_MASTER_TBL); 
			$this->db->where('po_id', $po_id);
			$query 		= $this->db->get();
			$status		= $query->row()->status;
		}
		$institute_id		= $this->input->post('institute-id');
		$branch_id		    = $this->input->post('branch-id');
		$distributor_id		= $this->input->post('distributor-id');
		$importer_id		= $this->input->post('importer-id');
		$customer_id		= $this->input->post('customer-id');
		$workorder_id		= $this->input->post('workorder-id');
		$product_description= $this->input->post('product-details');
		$product_sku        = $this->input->post('product-sku');  
		$validity		    = $this->input->post('validity');
		$quantity		    = $this->input->post('quantity');
		$unit_price		    = $this->input->post('unit-price');
		$total_price		= ($quantity * $unit_price);
		$total_price 		= round($total_price,2,PHP_ROUND_HALF_UP);
		$vat_percentage		= $this->input->post('vat_percent');
		$ait_percentage		= $this->input->post('ait_percent');	
		$vat_amount         = (($total_price/100)*$vat_percentage);
		$ait_amount         = ((($total_price-$vat_amount)/100)*$ait_percentage);		
		$remarks 		    = str_replace("U 0026", '&', $this->input->post('remarks'));			
    	$created_by		    = $this->session->userdata('created_by');
		
		if(empty($importer_id)){ $importer_id = 0;}
		
		$ddata = array(
		'po_id'    	        =>$po_id,
		'institute_id'    	=>$institute_id,
		'branch_id'    		=>$branch_id,
		'distributor_id'  	=>$distributor_id,
		'importer_id'  	    =>$importer_id,
		'customer_id'   	=>$customer_id,
		'workorder_id'  	=>$workorder_id,
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
	   	   
		if($distributor_id >0 && $customer_id >0 && $workorder_id >0 && $product_description!="" && $quantity >0){
			if($details_id ==0){ 
			 $this->db->insert(DISTRIPO_DETAILS_TBL, $ddata);
			}else{
			$this->EditDetailRecord($details_id);
			}
			//print  $this->db->last_query();
		}
		
	}
	
	function GetSaveWODetailRecord($workorder_id){
	    $po_id	            = $this->input->post('po-id');
		if(empty($po_id)){
			$po_id	        = 0;
			$status		    = 0;
		}else{
			$this->db->select('status');
			$this->db->from(DISTRIPO_MASTER_TBL); 
			$this->db->where('po_id', $po_id);
			$query 		= $this->db->get();
			$status		= $query->row()->status;
		}
		$CSQL   = "SELECT * FROM ".DISTRIPO_DETAILS_TBL." WHERE workorder_id=$workorder_id AND status =1";
    	$dquery = $this->db->query($CSQL); 
    	    	
	    $DSQL="SELECT * FROM ".WORKORDER_DETAILS_TBL." WHERE workorder_id=$workorder_id AND po_created =0 AND status=1";
		
		$DSQL.= " GROUP BY details_id";
		$DSQL.= " ORDER BY details_id ASC";
		
		$query = $this->db->query($DSQL); 
		if($query->num_rows() >0 && $dquery->num_rows()==0 && $po_id==0){
		  foreach($query->result() as $irow){
		    $institute_id		= $irow->institute_id;
    		$branch_id		    = $irow->branch_id;
    		$distributor_id		= $this->input->post('distributor-id');
    		$importer_id		= $this->input->post('importer-id');
    		$customer_id		= $this->input->post('customer-id');
    		$workorder_id		= $workorder_id;
    		$product_description= $irow->product_description;
    		$product_sku        = $irow->product_sku;  
    		$validity		    = $irow->validity;
    		$quantity		    = $irow->quantity;
    		$unit_price		    = $irow->unit_price;
    		$total_price		= ($quantity * $unit_price);
    		$total_price 		= round($total_price,2,PHP_ROUND_HALF_UP);
    		$vat_percentage		= $irow->vat_percentage;
    		$ait_percentage		= $irow->ait_percentage;	
    		$vat_amount         = (($total_price/100)*$vat_percentage);
    		$ait_amount         = ((($total_price-$vat_amount)/100)*$ait_percentage);		
    		$remarks 		    = $irow->remarks;			
        	$created_by		    = $this->session->userdata('created_by');
    									
    		$ddata = array(
    		'po_id'    	        =>$po_id,
    		'institute_id'    	=>$institute_id,
    		'branch_id'    		=>$branch_id,
    		'distributor_id'  	=>$distributor_id,
    		'importer_id'  	    =>$importer_id,
    		'customer_id'   	=>$customer_id,
    		'workorder_id'  	=>$workorder_id,
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
			$this->db->insert(DISTRIPO_DETAILS_TBL, $ddata);
		     
		    //print  $this->db->last_query();		     

		   }// end foreach
		   $WESQL= "UPDATE ".WORKORDER_DETAILS_TBL." SET po_created ='1' WHERE workorder_id=$workorder_id AND status =1 ";
		   $this->db->query($WESQL);	
    	}else{
    	    
    	    $CSQL   = "SELECT * FROM ".DISTRIPO_DETAILS_TBL." WHERE workorder_id=$workorder_id AND status =1";
    	    $dquery = $this->db->query($CSQL);
    	    if($query->num_rows()==0){		
			 $DSQL="SELECT * FROM ".WORKORDER_DETAILS_TBL." WHERE workorder_id=$workorder_id AND po_created < 2 AND status=1";
			
			 $DSQL.= " GROUP BY details_id";
			 $DSQL.= " ORDER BY details_id ASC";
			 $query = $this->db->query($DSQL); //echo $DSQL;
			 if($query->num_rows() >0){			  
			    foreach($query->result() as $irow){	
    		    $institute_id		= $irow->institute_id;
        		$branch_id		    = $irow->branch_id;
        		$distributor_id		= $this->input->post('distributor-id');
        		$importer_id		= $this->input->post('importer-id');
        		$customer_id		= $this->input->post('customer-id');
        		$workorder_id		= $workorder_id;
        		$product_description= $irow->product_description;
        		$product_sku        = $irow->product_sku;  
        		$validity		    = $irow->validity;
        		$quantity		    = $irow->quantity;
        		$unit_price		    = $irow->unit_price;
        		$total_price		= ($quantity * $unit_price);
        		$total_price 		= round($total_price,2,PHP_ROUND_HALF_UP);
        		$vat_percentage		= $irow->vat_percentage;
        		$ait_percentage		= $irow->ait_percentage;	
        		$vat_amount         = (($total_price/100)*$vat_percentage);
        		$ait_amount         = ((($total_price-$vat_amount)/100)*$ait_percentage);		
        		$remarks 		    = $irow->remarks;
    			
    			$created_by		    = $this->session->userdata('created_by');
    						
    	    	$ddata = array(
        		'po_id'    	        =>$po_id,
        		'institute_id'    	=>$institute_id,
        		'branch_id'    		=>$branch_id,
        		'distributor_id'  	=>$distributor_id,
        		'importer_id'  	    =>$importer_id,
        		'customer_id'   	=>$customer_id,
        		'workorder_id'  	=>$workorder_id,
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
    			$this->db->insert(DISTRIPO_DETAILS_TBL, $ddata);			     	
			    //print  $this->db->last_query();		     

			    }// end foreach
			    $WESQL= "UPDATE ".WORKORDER_DETAILS_TBL." SET po_created ='1' WHERE workorder_id=$workorder_id AND status =1 ";
		        $this->db->query($WESQL);	
	    	   } //end if num row
	    		
	    	  }//end if num row
            
		}// end else 
		
	}
	
    function EditDetailRecord($details_id){
		if(empty($details_id)){
		$details_id		= 0;
		}		    
		$po_id	        = $this->input->post('po-id');
		if(empty($po_id)){
			$po_id	    = 0;
			$status		= 0;
		}else{
			$this->db->select('status');
			$this->db->from(DISTRIPO_MASTER_TBL); 
			$this->db->where('po_id', $po_id);
			$query 		= $this->db->get();
			$status		= $query->row()->status;
		}
		$institute_id		= $this->input->post('institute-id');
		$branch_id			= $this->input->post('branch-id');
		$distributor_id		= $this->input->post('distributor-id');
		$importer_id		= $this->input->post('importer-id');
		$customer_id		= $this->input->post('customer-id');
		$workorder_id		= $this->input->post('workorder-id');
		$product_description= $this->input->post('product-details');
		$product_sku        = $this->input->post('product-sku'); 
		$validity		    = $this->input->post('validity');
		$quantity		    = $this->input->post('quantity');
		$unit_price		    = $this->input->post('unit-price');
		$total_price		= ($quantity * $unit_price);
		$total_price 		= round($total_price,2,PHP_ROUND_HALF_UP);
		$vat_percentage		= $this->input->post('vat_percent');
		$ait_percentage		= $this->input->post('ait_percent');	
		$vat_amount         = (($total_price/100)*$vat_percentage);
		$ait_amount         = ((($total_price-$vat_amount)/100)*$ait_percentage);		
		$remarks 		    = str_replace("U 0026", '&', $this->input->post('remarks'));
    	$modified_by		= $this->session->userdata('created_by');
		$modified_time  	= date("Y-m-d H:i:s");		
						
		$ddata = array(
		'institute_id'    	=>$institute_id,
		'branch_id'    		=>$branch_id,
		'distributor_id'  	=>$distributor_id,
		'importer_id'  	    =>$importer_id,
		'customer_id'   	=>$customer_id,
		'workorder_id'  	=>$workorder_id,
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
	    $this->db->where('po_id',$po_id);
		$this->db->where('distributor_id',$distributor_id);
		$this->db->where('customer_id',$customer_id);
		$this->db->where('details_id',$details_id);
		$this->db->update(DISTRIPO_DETAILS_TBL, $ddata);
       	//print  $this->db->last_query();
	}
		
	function savePOMaster($po_id){
		$institute_id		= $this->input->post('institute_id');
		$branch_id		    = $this->input->post('branch_id');
		$distributor_id		= $this->input->post('distributor_id');
		$importer_id		= $this->input->post('importer_id');
		$customer_id		= $this->input->post('customer_id');
		$workorder_id		= $this->input->post('workorder_id');
		$po_date		    = $this->formatDate($this->input->post('po_date'));		
		$currency		    = $this->input->post('currency');
		$currency_id		= $this->input->post('currency_id');
		$attention          = str_replace("U 0026", '&', $this->input->post('attention'));
		$subject            = str_replace("U 0026", '&', $this->input->post('subject'));
		$total_bill		    = $this->input->post('total_bill');
		$discount_percentage= $this->input->post('discount_percentage');
		$discount_amount	= $this->input->post('discount_amount');
		$sub_total		    = $this->input->post('sub_total');
		$including_vat		= $this->input->post('including_vat');	
		$vat_percentage		= $this->input->post('vat_percentage');	
		$vat_amount		    = $this->input->post('vat_amount');	
		$grand_total		= $this->input->post('grand_total');
		$ait_percentage		= $this->input->post('ait_percentage');	
		$ait_amount		    = $this->input->post('ait_amount');
		$offer_attach	    = $this->input->post('offer_attach');
		$payment_mode		= $this->input->post('payment_mode');
		$payment_terms 	    = str_replace("U 0026", '&', $this->input->post('payment_terms'));
		$delivery_to 	    = str_replace("U 0026", '&', $this->input->post('delivery_to'));
		$ship_to 	        = str_replace("U 0026", '&', $this->input->post('ship_to'));
		$bill_to 	        = str_replace("U 0026", '&', $this->input->post('bill_to'));
		$status		        = $this->input->post('status');
		if(empty($importer_id)){ $importer_id = 0;}
		if(empty($discount_percentage)){$discount_percentage=0;} 
		if(empty($discount_amount)){$discount_amount=0;}
		if(empty($including_vat)){$including_vat=0;}
		if($discount_percentage >0 && $discount_amount==0){
		   $discount_amount = (($total_bill/100) * $discount_percentage);
		}
		$sub_total		= ($total_bill - $discount_amount);
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
		
		$net_bill_amount 	 = round($net_bill_amount,0,PHP_ROUND_HALF_UP);
		
		$created_by		= $this->session->userdata('created_by');
		if($importer_id >0 && $currency_id >1){
		    $profit_margin_percentage  = 10;
		    $profit_margin  = (($net_bill_amount/100) * $profit_margin_percentage);
			$importer_value = ($net_bill_amount - ($profit_margin+$vat_amount+$ait_amount));
		}else{
			$importer_value = 0;
		}
				
		if($total_bill >0){
		   		   	   
		   if($po_id >0){
		        $modified_time  = date("Y-m-d H:i:s");
		        $offer_attach	= $_FILES['offer_attach'];  
        		if($offer_attach!=""){
        		    $ssql       = "SELECT po_no FROM ".DISTRIPO_MASTER_TBL." WHERE po_id = $po_id AND institute_id = $institute_id AND branch_id = $branch_id";
        			$squery     = $this->db->query($ssql);				
        			if($squery->num_rows() >0){				   
        			   $po_no   = $squery->row()->po_no;
        			}
        			$offer_attach =$this->UploadAttachment($po_no);				
        		}else{
        			$ssql       = "SELECT offer_attach FROM ".DISTRIPO_MASTER_TBL." WHERE po_id = $po_id AND institute_id = $institute_id AND branch_id = $branch_id";
        			$squery     = $this->db->query($ssql);				
        			if($squery->num_rows() >0){				   
        			   $offer_attach = $squery->row()->offer_attach;
        			}
        		}
			$mdata = array(
				'institute_id'      =>$institute_id,
				'branch_id'    	    =>$branch_id,
				'distributor_id'    =>$distributor_id,
				'importer_id' 	    =>$importer_id,
				'customer_id'	    =>$customer_id,
				'workorder_id'	    =>$workorder_id,
				'po_date'   	    =>$po_date,
				'currency_id'       =>$currency_id,
				'currency'    	    =>$currency,
				'attention'    	    =>$attention,
				'subject'    	    =>$subject,
				'total_bill'        =>$total_bill,
				'discount_persent'  =>$discount_percentage,
				'discount_amount'   =>$discount_amount,
				'sub_total'  	    =>$sub_total,
				'including_vat'     =>$including_vat,
				'vat_percentage'    =>$vat_percentage,
				'vat_amount'   	    =>$vat_amount,
				'grand_total'  	    =>$grand_total,
				'ait_percentage'    =>$ait_percentage,
				'ait_amount'        =>$ait_amount,
				'net_bill_amount'   =>$net_bill_amount,
				'importer_value'    =>$importer_value,
				'offer_attach'      =>$offer_attach,
				'payment_mode'      =>$payment_mode,
				'payment_terms'     =>$payment_terms,
				'delivery_to'  	    =>$delivery_to,
				'ship_to'  	        =>$ship_to,
				'bill_to'  	        =>$bill_to,
	            'status'  	        =>$status,
				'modified_by'  	    =>$created_by,
				'modified_time'     =>$modified_time
			);
			$this->db->where('po_id',$po_id);
			$this->db->where('distributor_id',$distributor_id);
			$this->db->where('workorder_id',$workorder_id);
			$this->db->update(DISTRIPO_MASTER_TBL, $mdata);	
		    }else{
		       $podateArr   = explode("-",$po_date);
			   $po_no       = $this->getBillID($institute_id,$branch_id,$podateArr[1],$po_date);
			   if($importer_id >0){			   
			   $ipo_no      = $this->getImpoterPONo($institute_id,$branch_id,$podateArr[1],$po_date);
			   }else{ $ipo_no="";}
			   $offer_attach= $this->UploadAttachment($po_no);
			   //======== Save Distri PO Master ========
			   $mdata = array(
				'institute_id'      =>$institute_id,
				'branch_id'    	    =>$branch_id,
				'distributor_id'    =>$distributor_id,
				'importer_id' 	    =>$importer_id,
				'customer_id'	    =>$customer_id,
				'workorder_id'	    =>$workorder_id,
				'po_no'   	        =>$po_no,
				'ipo_no'   	        =>$ipo_no,
				'po_date'   	    =>$po_date,
				'currency_id'       =>$currency_id,
				'currency'    	    =>$currency,
				'attention'    	    =>$attention,
				'subject'    	    =>$subject,
				'total_bill'        =>$total_bill,
				'discount_persent'  =>$discount_percentage,
				'discount_amount'   =>$discount_amount,
				'sub_total'  	    =>$sub_total,
				'including_vat'     =>$including_vat,
				'vat_percentage'    =>$vat_percentage,
				'vat_amount'   	    =>$vat_amount,
				'grand_total'  	    =>$grand_total,
				'ait_percentage'    =>$ait_percentage,
				'ait_amount'        =>$ait_amount,
				'net_bill_amount'   =>$net_bill_amount,
				'importer_value'    =>$importer_value,
				'offer_attach'      =>$offer_attach,
				'payment_mode'      =>$payment_mode,
				'payment_terms'     =>$payment_terms,
				'delivery_to'  	    =>$delivery_to,
				'ship_to'  	        =>$ship_to,
				'bill_to'  	        =>$bill_to,
	            'status'  	        =>$status,
				'created_by'  	    =>$created_by
			   );
			   $this->db->insert(DISTRIPO_MASTER_TBL, $mdata); //print $this->db->last_query();
			   $po_id = $this->db->insert_id(); 
			   //====== Update Bill Details ========
			   $USQL= "UPDATE ".DISTRIPO_DETAILS_TBL." SET po_id='".$po_id."' WHERE po_id=0 AND distributor_id = $distributor_id AND customer_id = $customer_id AND workorder_id = $workorder_id  AND created_by = ".$created_by;
			   $this->db->query($USQL);
			   $WESQL= "UPDATE ".WORKORDER_DETAILS_TBL." SET po_created ='2' WHERE workorder_id=$workorder_id AND status =1 ";
		       $this->db->query($WESQL);
		    }
			
				
		}// end if total_amount
		
	}
	//===== End Workorder ======= 
	
	function UploadAttachment($pdf_id){
	    if($_FILES['offer_attach']){
			$targetfolder   = ASSETS.'/pdf/distioffer/';
		    $FileType       = pathinfo($targetfolder.basename($_FILES["offer_attach"]["name"]),PATHINFO_EXTENSION);
	        $targetfolder   = ASSETS.'/pdf/distioffer/'.$pdf_id.".".$FileType;
  
            $ok=1;
            //$file_type=$_FILES['offer_attach']['type'];
            
            if ($FileType=="jpg" || $FileType=="jpeg" || $FileType=="gif" || $FileType=="docx" || $FileType=="doc" || $FileType=="pdf") {
            
                if(move_uploaded_file($_FILES['offer_attach']['tmp_name'], $targetfolder))
                
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
	
    function GetAccountId($group_id,$subsidiary_level1,$subsidiary_level2,$subsidiary_level3,$head_type=NULL)
    {		
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
		$ssql = "SELECT COUNT(*) as total FROM ".DISTRIPO_MASTER_TBL." WHERE institute_id =$institute_id AND branch_id = $branch_id AND status < 5";
		$squery = $this->db->query($ssql);				
		if($squery->num_rows() >0){
		   $TotalNo = $squery->row()->total+1;		
		   if($TotalNo <10){
		      $SL="00000".$TotalNo;
		   }elseif($TotalNo <100){
		      $SL="0000".$TotalNo;
		   }elseif($TotalNo <1000){
		      $SL="000".$TotalNo;
		   }elseif($TotalNo <10000){
		      $SL="00".$TotalNo;
		   }elseif($TotalNo <100000){
		      $SL="0".$TotalNo;
		   }else{
		      $SL=$TotalNo;
		   }
		}else{
		      $SL="000001";
		}
		
		$psql  = "SELECT branch_code FROM ".BRANCH_TBL." WHERE branch_id =$branch_id";
		$query = $this->db->query($psql);
		//print $this->db->last_query(); exit;
		$BC =""; $BM = 0; $BM=$billing_month;
		if($query->num_rows() == 1){
		 $row = $query->row();		
		 $BC  = $row->branch_code;
		}		
		$BillNo = $BC.$yearArr[0]."/".$BM."/".$SL;
		return $BillNo;		
	}
    
    function getImpoterPONo($institute_id,$branch_id,$billing_month,$bill_date){
		$SL=""; $TotalNo=0; $BillNo = ""; $yearArr = explode("-",$bill_date);
		$ssql = "SELECT COUNT(*) as total FROM ".DISTRIPO_MASTER_TBL." WHERE institute_id =$institute_id AND branch_id = $branch_id AND importer_id >0 AND status < 5 ";
		$squery = $this->db->query($ssql);				
		if($squery->num_rows() >0){
		   $TotalNo = $squery->row()->total+1;		
		   if($TotalNo <10){
		      $SL="00000".$TotalNo;
		   }elseif($TotalNo <100){
		      $SL="0000".$TotalNo;
		   }elseif($TotalNo <1000){
		      $SL="000".$TotalNo;
		   }elseif($TotalNo <10000){
		      $SL="00".$TotalNo;
		   }elseif($TotalNo <100000){
		      $SL="0".$TotalNo;
		   }else{
		      $SL=$TotalNo;
		   }
		}else{
		      $SL="000001";
		}
		
		$psql  = "SELECT branch_code FROM ".BRANCH_TBL." WHERE branch_id =$branch_id";
		$query = $this->db->query($psql);
		//print $this->db->last_query(); exit;
		$BC =""; $BM = 0; $BM=$billing_month;
		if($query->num_rows() == 1){
		 $row = $query->row();		
		 $BC  = $row->branch_code;
		}		
		$BillNo = $BC."-I".$yearArr[0]."/".$BM."/".$SL;
		return $BillNo;		
	}
	function getHeadType($account_id){
        $this->db->select('head_type');
        $this->db->from(ACC_HEAD_TBL);
        $this->db->where('account_id', $account_id);
        $query = $this->db->get();
        return $query->row()->head_type; 
    }
    function getAjaxDistriInfo(){		
		$distributor_id	= $this->input->post('id');
		
        $this->db->select('d.*,c.currency_id,c.currency_name');
        $this->db->from(DISTRIBUTOR_TBL." as d");
		$this->db->join(CURRENCY_TBL.' AS c', 'c.currency_id=d.currency','LEFT');
        $this->db->where('distributor_id', $distributor_id);
        $query = $this->db->get(); //print $this->db->last_query();
        return $query->row();   
        
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
		$po_id		= $this->input->post('po-id');
		if(empty($po_id)){
			$po_id	= 0;
			$status	= 1;
		}
		
		$institute_id		= $this->input->post('institute-id');
		$branch_id			= $this->input->post('branch-id');
		$distributor_id		= $this->input->post('distributor-id');
		$importer_id		= $this->input->post('importer-id');
		$customer_id		= $this->input->post('customer-id');
		$workorder_id		= $this->input->post('workorder-id');
		
		$menu_slug = $this->uri->segment(1);
		$hasEditPM = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM  = $this->Site_model->hasOptionPermission($menu_slug,"Delete");
		
		$bdsql = "SELECT * FROM ".DISTRIPO_DETAILS_TBL." WHERE institute_id = $institute_id AND branch_id = $branch_id AND distributor_id=$distributor_id AND customer_id=$customer_id AND workorder_id=$workorder_id AND po_id=$po_id";
		$bdsql.= " GROUP BY details_id ORDER BY details_id ASC"; //echo $bdsql;
		$query = $this->db->query($bdsql); 
		if($query->num_rows() >0){	
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
			  $i=1; $TotalBill=0; $TotalVat=0; $TotalAit=0;
			  foreach($query->result() as $row){
			  //if($i%2==0){ $tblrow="success";}else{$tblrow="warning";}
			  $TotalBill+=$row->total_price;
			  $TotalVat+=$row->vat_amount;
			  $TotalAit+=$row->ait_amount;
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
				echo "<span data-toggle='tooltip' data-original-title='Edit'><a class='btn btn-info btn-xs' data-toggle='modal' onclick=editRow('".$row->details_id."','".$row->po_id."') id='".$row->details_id."' href='#'><i class='fa fa-edit'></i></a></span> &nbsp;";
				}
				if($hasDelPM){				
				echo "<span data-toggle='tooltip' data-original-title='Delete'><a class='btn btn-danger btn-xs' data-toggle='modal' onclick=deleteRow('".$row->details_id."','".$row->po_id."') id='".$row->details_id."' href='#deleteDraftModal'><i class='fa fa-trash'></i></a></span>";
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
		  echo '</table>##&##'.$TotalBill.'##&##'.$TotalVat.'##&##'.$TotalAit;
		}//end num_rows	
	}
	function GetProductDetailList($po_id,$distributor_id,$customer_id,$workorder_id){
		
		$menu_slug = $this->uri->segment(1);
		$hasEditPM = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM  = $this->Site_model->hasOptionPermission($menu_slug,"Delete");
		
		$bdsql = "SELECT * FROM ".DISTRIPO_DETAILS_TBL." WHERE distributor_id=$distributor_id AND customer_id=$customer_id AND workorder_id=$workorder_id AND po_id=$po_id";
		$bdsql.= " GROUP BY details_id ORDER BY details_id ASC"; //echo $bdsql;
		$query = $this->db->query($bdsql); 
		$TBLGRIDE="";
		if($query->num_rows() >0){	
		  $TBLGRIDE.= 
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
				$TBLGRIDE.= "<span data-toggle='tooltip' data-original-title='Edit'><a class='btn btn-info btn-xs' data-toggle='modal' onclick=editRow('".$row->details_id."','".$row->po_id."') id='".$row->details_id."' href='#'><i class='fa fa-edit'></i></a></span> &nbsp;";
				}
				if($hasDelPM){				
				$TBLGRIDE.= "<span data-toggle='tooltip' data-original-title='Delete'><a class='btn btn-danger btn-xs' data-toggle='modal' onclick=deleteRow('".$row->details_id."','".$row->po_id."') id='".$row->details_id."' href='#deleteDraftModal'><i class='fa fa-trash'></i></a></span>";
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
		$hasEditPM  = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM   = $this->Site_model->hasOptionPermission($menu_slug,"Delete");
		$hasApprovPM= $this->Site_model->hasOptionPermission($menu_slug,"Approved");
		$hasPrintPM = $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$srcFrom			=$this->formatDate($this->input->post('srcFrom'));
		$srcTo				=$this->formatDate($this->input->post('srcTo'));
		$institute_id		=$this->input->post('src-institute');
		$branch_id			=$this->input->post('src-branch');
		$po_no		        =$this->input->post('src-po_no');
		$distributor_id		=$this->input->post('src-distributor');
		
	   	$from	=$this->input->post('from');
		$to	=$this->input->post('to');
		if($from==""){ $from=0;} if($to==""){ $to=50;}
		$this->db->select('bl.*,p.distributor_full_name,p.address,p.phone,p.fax,p.mobile,p.mobile,c.customer_full_name,i.company_name,b.branch_name,b.branch_code,DATE_FORMAT(bl.po_date ,"%d-%m-%Y") as po_date',FALSE);
		$this->db->from(DISTRIPO_MASTER_TBL." AS bl");
		$this->db->join(DISTRIBUTOR_TBL.' AS p', 'p.distributor_id=bl.distributor_id','LEFT');
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
			$this->db->where("p.account_id", $this->session->userdata('user_ref_id')); 
		}
		if($distributor_id >0){
			  $this->db->where("bl.distributor_id", $distributor_id);  	
		}
		if($po_no >0){
			  $this->db->where("bl.po_no", $po_no);  	
		}
		if($srcFrom!="" && $srcTo==""){
		   $this->db->where("bl.po_date >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
		   $this->db->where("bl.po_date <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
		   $this->db->where("bl.po_date BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}
		
		$this->db->group_by('bl.po_id');
		$this->db->order_by('bl.po_id','ASC');
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
				<th width="20%">'.$this->lang->line("distri_po").' '.$this->lang->line("details").'</th>
			  	<th width="24%">'.$this->lang->line("distributor").' '.$this->lang->line("details").'</th>
				<th width="8%">'.$this->lang->line("total_amount").'</th>
				<th width="7%">'.$this->lang->line("discount").'</th>
				<th width="7%">'.$this->lang->line("vat")." ".$this->lang->line("amount").'</th>
				<th width="7%">'.$this->lang->line("ait")." ".$this->lang->line("amount").'</th>
				<th width="10%">'.$this->lang->line("net_amount").'</th>
				<th width="11%" class="text-center hidden-print">'.$this->lang->line("options").'</th>
			  </tr>
			</thead>';
			  $i=1; $TotalBill=0; $TotalNetBill=0; $TotalLess=0; $TotalVat=0; $TotalAit=0; $payment_mode="";
			  foreach($query->result() as $row){
			   $TotalBill+= $row->total_bill;
			   $TotalLess+= $row->discount_amount;
			   $TotalVat+= $row->vat_amount;
			   $TotalAit+= $row->ait_amount;
			   $TotalNetBill+= $row->net_bill_amount;
			   //if($i%2==0){ $tblrow="success";}else{$tblrow="warning";}
			   if($row->payment_mode==1){ $payment_mode="Cash";}elseif($row->payment_mode==2){ $payment_mode="Cheque";}elseif($row->payment_mode==3){ $payment_mode="bKash";}elseif($row->payment_mode==4){ $payment_mode="TT";}else{$payment_mode="Others";}
			   echo "<tr class='default'>
			  	<td>".$i."</td>
				<td>PO No: ".$row->po_no."<br> Date: ".$row->po_date."<br> Mode: ".$payment_mode."</td>
			    <td>".$row->distributor_full_name."<br> ".$row->address."</td>
				<td>".$row->total_bill."</td>
				<td>".$row->discount_amount."</td>
				<td>".$row->vat_amount."</td>
				<td>".$row->ait_amount."</td>
				<td>".$row->net_bill_amount."</td>
				<td class='text-center align-middle hidden-print'>";
		    	if($hasEditPM){
				echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Edit'><a class='btn btn-info btn-xs' onclick=editRecord('".$row->po_id."') id='".$row->po_id."' href='#'><i class='fas fa-edit'></i></a></span>&nbsp;";
				}
				if($hasDelPM){				
				echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Delete'><a class='btn btn-danger btn-xs' data-toggle='modal' onclick=deleteRecord('".$row->po_id."') id='".$row->po_id."' data-target='#deleteModal'><i class='fa fa-trash'></i></a></span>";
				}
				if($hasApprovPM){
				    if($row->status==0){
				    echo "<div class='clearfix' style='margin-top:6px'></div><span data-toggle='tooltip' data-placement='top' data-original-title='Approve'><a class='btn btn-warning btn-xs' data-toggle='modal' onclick=approvePO('".$row->po_id."','".$row->status."') id='".$row->po_id."' data-target='#approveModal'><i class='fa fa-times'></i></a></span>";
				    }elseif($row->status==1){
				    echo "<div class='clearfix' style='margin-top:6px'></div><span data-toggle='tooltip' data-placement='top' data-original-title='Upapprove'><a class='btn btn-success btn-xs' data-toggle='modal' onclick=unapprovePO('".$row->po_id."','".$row->status."') id='".$row->po_id."' data-target='#unapproveModal'><i class='fa fa-check'></i></a></span>";
				    }
				    echo "&nbsp;<span data-toggle='tooltip' data-original-title='View'><a class='btn btn-success btn-xs' target='_blank' href='".base_url()."distributorpo/ViewPOForm/".$row->po_id."/".$row->distributor_id."'><i class='fa fa-print'></i></a></span>";
				    echo "<div class='clearfix' style='margin-top:6px'></div><span data-toggle='tooltip' data-original-title='Top Sheet'><a class='btn btn-warning btn-sm' target='_blank' href='".base_url()."distributorpo/ViewReqForm/".$row->po_id."/".$row->workorder_id."/".$row->distributor_id."/".$row->importer_id."'>Top Sheet</a></span>";
				}else{
				 if($hasPrintPM){
				 echo "<div class='clearfix'></div><br><span data-toggle='tooltip' data-original-title='View Form'><a class='btn btn-success btn-sm' target='_blank' href='".base_url()."distributorpo/ViewPOForm/".$row->po_id."/".$row->distributor_id."'><i class='fa fa-print'></i> Form</a></span>";
				 }
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
		$po_no		        =$this->input->post('src-po_no');
		$distributor_id		=$this->input->post('src-distributor');
		
		$this->db->select('bl.*,p.distributor_full_name,p.address,p.phone,p.fax,p.mobile,p.mobile,c.customer_full_name,i.company_name,b.branch_name,b.branch_code,DATE_FORMAT(bl.po_date ,"%d-%m-%Y") as po_date',FALSE);
		$this->db->from(DISTRIPO_MASTER_TBL." AS bl");
		$this->db->join(DISTRIBUTOR_TBL.' AS p', 'p.distributor_id=bl.distributor_id','LEFT');
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
			$this->db->where("p.account_id", $this->session->userdata('user_ref_id')); 
		}
		if($distributor_id >0){
			  $this->db->where("bl.distributor_id", $distributor_id);  	
		}
		if($po_no >0){
			  $this->db->where("bl.po_no", $po_no);  	
		}
		if($srcFrom!="" && $srcTo==""){
		   $this->db->where("bl.po_date >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
		   $this->db->where("bl.po_date <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
		   $this->db->where("bl.po_date BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}
		
		$this->db->group_by('bl.po_id');
		$this->db->order_by('bl.po_id','ASC');
		$query = $this->db->get();
		if($query->num_rows() >0){
			return $query->num_rows();
		}else{
			return 0;
		}//echo $this->db->last_query();
	}
	
   	function GetDistriPOGridList(){
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
		$po_no		        =$this->input->post('src-po_no');
		$distributor_id		=$this->input->post('src-distributor');
		
	   	$from	=$this->input->post('from');
		$to	=$this->input->post('to');
		if($from==""){ $from=0;} if($to==""){ $to=50;}
		$this->db->select('bl.*,p.distributor_full_name,p.address,p.phone,p.fax,p.mobile,p.mobile,c.customer_full_name,i.company_name,b.branch_name,b.branch_code,DATE_FORMAT(bl.po_date ,"%d-%m-%Y") as po_date',FALSE);
		$this->db->from(DISTRIPO_MASTER_TBL." AS bl");
		$this->db->join(DISTRIBUTOR_TBL.' AS p', 'p.distributor_id=bl.distributor_id','LEFT');
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
			$this->db->where("p.account_id", $this->session->userdata('user_ref_id')); 
		}
		if($distributor_id >0){
			  $this->db->where("bl.distributor_id", $distributor_id);  	
		}
		if($po_no >0){
			  $this->db->where("bl.po_no", $po_no);  	
		}
		if($srcFrom!="" && $srcTo==""){
		   $this->db->where("bl.po_date >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
		   $this->db->where("bl.po_date <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
		   $this->db->where("bl.po_date BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}
		
		$this->db->group_by('bl.po_id');
		$this->db->order_by('bl.po_id','ASC');
		$this->db->limit($to,$from);
		$query = $this->db->get(); //print  $this->db->last_query();
		$totalrecord = $this->GetTotalDistriPORecord(); 
	    	$perPage=50; $Pagination="";
	    	if($totalrecord >0){
		   	$Pagination = $this->getPagination($totalrecord,$perPage);
	    	} //print  $this->db->last_query();
		echo 
		'<table width="100%"  border="0" class="table table-responsive table-bordered table-hover custab">
			<thead>
			  <tr class="active">
			  	<th width="2%">'.$this->lang->line("sl").'</th>
				<th width="20%">'.$this->lang->line("distri_po").' '.$this->lang->line("details").'</th>
			  	<th width="24%">'.$this->lang->line("distributor").' '.$this->lang->line("details").'</th>
				<th width="8%">'.$this->lang->line("total_amount").'</th>
				<th width="8%">'.$this->lang->line("discount").'</th>
				<th width="10%">'.$this->lang->line("vat")." ".$this->lang->line("amount").'</th>
				<th width="10%">'.$this->lang->line("ait")." ".$this->lang->line("amount").'</th>
				<th width="10%">'.$this->lang->line("net_amount").'</th>
				<th width="8%" class="text-center hidden-print">'.$this->lang->line("options").'</th>
			  </tr>
			</thead>';
			  $i=1; $TotalBill=0; $TotalNetBill=0; $TotalLess=0; $TotalVat=0; $TotalAit=0; $payment_mode="";
			  foreach($query->result() as $row){
			   $TotalBill+= $row->total_bill;
			   $TotalLess+= $row->discount_amount;
			   $TotalVat+= $row->vat_amount;
			   $TotalAit+= $row->ait_amount;
			   $TotalNetBill+= $row->net_bill_amount;
			   //if($i%2==0){ $tblrow="success";}else{$tblrow="warning";}
			   if($row->payment_mode==1){ $payment_mode="Cash";}elseif($row->payment_mode==2){ $payment_mode="Cheque";}elseif($row->payment_mode==3){ $payment_mode="bKash";}elseif($row->payment_mode==4){ $payment_mode="TT";}else{$payment_mode="Others";}
			   echo "<tr class='default'>
			  	<td>".$i."</td>
				<td>PO No: ".$row->po_no."<br> Date: ".$row->po_date."<br> Mode: ".$payment_mode."</td>
			    <td>".$row->distributor_full_name."<br> ".$row->address."</td>
				<td>".$row->total_bill."</td>
				<td>".$row->discount_amount."</td>
				<td>".$row->vat_amount."</td>
				<td>".$row->ait_amount."</td>
				<td>".$row->net_bill_amount."</td>
				<td class='text-center align-middle hidden-print'>";
				 if($hasPrintPM){
				 echo "<div class='clearfix'></div><br><span data-toggle='tooltip' data-original-title='View Form'><a class='btn btn-success btn-sm' target='_blank' href='".base_url()."distributorpo/ViewPOForm/".$row->po_id."/".$row->distributor_id."'><i class='fa fa-print'></i> Print DPO</a></span>";
				 }
				
				if($row->offer_attach!=""){
				echo "<div class='clearfix'></div><br><span data-toggle='tooltip' data-original-title='Download Att'><a class='btn btn-success btn-sm' target='_blank' href='".base_url()."/".ASSETS."/pdf/distioffer/".$row->offer_attach."'><i class='fa fa-download'></i> Download</a></span>";
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
    
	function GetTotalDistriPORecord(){
		$srcFrom			=$this->formatDate($this->input->post('srcFrom'));
		$srcTo				=$this->formatDate($this->input->post('srcTo'));
		$institute_id		=$this->input->post('src-institute');
		$branch_id			=$this->input->post('src-branch');
		$po_no		        =$this->input->post('src-po_no');
		$distributor_id		=$this->input->post('src-distributor');
		
		$this->db->select('bl.*,p.distributor_full_name,p.address,p.phone,p.fax,p.mobile,p.mobile,c.customer_full_name,i.company_name,b.branch_name,b.branch_code,DATE_FORMAT(bl.po_date ,"%d-%m-%Y") as po_date',FALSE);
		$this->db->from(DISTRIPO_MASTER_TBL." AS bl");
		$this->db->join(DISTRIBUTOR_TBL.' AS p', 'p.distributor_id=bl.distributor_id','LEFT');
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
			$this->db->where("p.account_id", $this->session->userdata('user_ref_id')); 
		}
		if($distributor_id >0){
			  $this->db->where("bl.distributor_id", $distributor_id);  	
		}
		if($po_no >0){
			  $this->db->where("bl.po_no", $po_no);  	
		}
		if($srcFrom!="" && $srcTo==""){
		   $this->db->where("bl.po_date >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
		   $this->db->where("bl.po_date <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
		   $this->db->where("bl.po_date BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}
		
		$this->db->group_by('bl.po_id');
		$this->db->order_by('bl.po_id','ASC');
		$query = $this->db->get();
		if($query->num_rows() >0){
			return $query->num_rows();
		}else{
			return 0;
		}//echo $this->db->last_query();
	}
	//====== Start Importer PO List ======
	
   	function GetImporterPOGridList(){
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
		$po_no		        =$this->input->post('src-po_no');
		$importer_id		=$this->input->post('src-importer');
		
	   	$from	=$this->input->post('from');
		$to	=$this->input->post('to');
		if($from==""){ $from=0;} if($to==""){ $to=50;}
		$this->db->select('bl.*,p.importer_full_name,p.address,p.phone,p.fax,p.mobile,p.mobile,c.customer_full_name,i.company_name,b.branch_name,b.branch_code,DATE_FORMAT(bl.po_date ,"%d-%m-%Y") as po_date',FALSE);
		$this->db->from(DISTRIPO_MASTER_TBL." AS bl");
		$this->db->join(IMPORTER_TBL.' AS p', 'p.importer_id=bl.importer_id','LEFT');
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
			$this->db->where("p.account_id", $this->session->userdata('user_ref_id')); 
		}
		if($importer_id >0){
			  $this->db->where("bl.importer_id", $importer_id);  	
		}
		if($po_no >0){
			  $this->db->where("bl.po_no", $po_no);  	
		}
		if($srcFrom!="" && $srcTo==""){
		   $this->db->where("bl.po_date >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
		   $this->db->where("bl.po_date <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
		   $this->db->where("bl.po_date BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}
		$this->db->where("bl.importer_value >0");
		$this->db->where("bl.currency_id >1");
		$this->db->group_by('bl.po_id');
		$this->db->order_by('bl.po_id','ASC');
		$this->db->limit($to,$from);
		$query = $this->db->get(); //print  $this->db->last_query();
		$totalrecord = $this->GetTotalImporterPORecord();
	    	$perPage=50; $Pagination="";
	    	if($totalrecord >0){
		   	$Pagination = $this->getPagination($totalrecord,$perPage);
	    	} //print  $this->db->last_query();
		echo 
		'<table width="100%"  border="0" class="table table-responsive table-bordered table-hover custab">
			<thead>
			  <tr class="active">
			  	<th width="2%">'.$this->lang->line("sl").'</th>
				<th width="20%">'.$this->lang->line("importer_po").' '.$this->lang->line("details").'</th>
			  	<th width="24%">'.$this->lang->line("importer").' '.$this->lang->line("details").'</th>
				<th width="8%">'.$this->lang->line("total_amount").'</th>
				<th width="8%">'.$this->lang->line("discount").'</th>
				<th width="10%">'.$this->lang->line("vat")." ".$this->lang->line("amount").' (-)</th>
				<th width="10%">'.$this->lang->line("ait")." ".$this->lang->line("amount").' (-)</th>
				<th width="10%">'.$this->lang->line("importer_value").'</th>
				<th width="8%" class="text-center hidden-print">'.$this->lang->line("options").'</th>
			  </tr>
			</thead>';
			  $i=1; $TotalBill=0; $TotalNetBill=0; $TotalLess=0; $TotalVat=0; $TotalAit=0; $payment_mode="";
			  foreach($query->result() as $row){
			   $TotalBill+= $row->sub_total;
			   $TotalLess+= (($row->net_bill_amount/100)*10);
			   $TotalVat+= $row->vat_amount;
			   $TotalAit+= $row->ait_amount;
			   $TotalNetBill+= $row->importer_value;
			   $discount_amount = (($row->net_bill_amount/100)*10);
			   //if($i%2==0){ $tblrow="success";}else{$tblrow="warning";}
			   if($row->payment_mode==1){ $payment_mode="Cash";}elseif($row->payment_mode==2){ $payment_mode="Cheque";}elseif($row->payment_mode==3){ $payment_mode="bKash";}elseif($row->payment_mode==4){ $payment_mode="TT";}else{$payment_mode="Others";}
			   echo "<tr class='default'>
			  	<td>".$i."</td>
				<td>PO No: ".$row->po_no."<br> Date: ".$row->po_date."<br> Mode: ".$payment_mode."</td>
			    <td>".$row->importer_full_name."<br> ".$row->address."</td>
				<td>".$row->net_bill_amount."</td>
				<td>".$discount_amount."</td>
				<td>".$row->vat_amount."</td>
				<td>".$row->ait_amount."</td>
				<td>".$row->importer_value."</td>
				<td class='text-center align-middle hidden-print'>";
				 if($hasPrintPM){
				 echo "<div class='clearfix'></div><br><span data-toggle='tooltip' data-original-title='View Form'><a class='btn btn-success btn-sm' target='_blank' href='".base_url()."distributorpo/ViewIPOForm/".$row->po_id."/".$row->importer_id."'><i class='fa fa-print'></i> Print IPO</a></span>";
				 }
				
				if($row->offer_attach!=""){
				echo "<div class='clearfix'></div><br><span data-toggle='tooltip' data-original-title='Download Att'><a class='btn btn-success btn-sm' target='_blank' href='".base_url()."/".ASSETS."/pdf/distioffer/".$row->offer_attach."'><i class='fa fa-download'></i> Download</a></span>";
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
    
	function GetTotalImporterPORecord(){
		$srcFrom			=$this->formatDate($this->input->post('srcFrom'));
		$srcTo				=$this->formatDate($this->input->post('srcTo'));
		$institute_id		=$this->input->post('src-institute');
		$branch_id			=$this->input->post('src-branch');
		$po_no		        =$this->input->post('src-po_no');
		$importer_id		=$this->input->post('src-importer');
		
		
		$this->db->select('bl.*,p.importer_full_name,p.address,p.phone,p.fax,p.mobile,p.mobile,c.customer_full_name,i.company_name,b.branch_name,b.branch_code,DATE_FORMAT(bl.po_date ,"%d-%m-%Y") as po_date',FALSE);
		$this->db->from(DISTRIPO_MASTER_TBL." AS bl");
		$this->db->join(IMPORTER_TBL.' AS p', 'p.importer_id=bl.importer_id','LEFT');
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
			$this->db->where("p.account_id", $this->session->userdata('user_ref_id')); 
		}
		if($importer_id >0){
			  $this->db->where("bl.importer_id", $importer_id);  	
		}
		if($po_no >0){
			  $this->db->where("bl.po_no", $po_no);  	
		}
		if($srcFrom!="" && $srcTo==""){
		   $this->db->where("bl.po_date >=", $srcFrom);
		}elseif($srcFrom=="" && $srcTo!=""){
		   $this->db->where("bl.po_date <=", $srcTo);
		}elseif($srcFrom!="" && $srcTo!=""){
		   $this->db->where("bl.po_date BETWEEN '$srcFrom' AND '$srcTo'",NULL,FALSE);
		}
		$this->db->where("bl.importer_value >0");
		$this->db->where("bl.currency_id >1");
		$this->db->group_by('bl.po_id');
		$this->db->order_by('bl.po_id','ASC');
		$query = $this->db->get();
		if($query->num_rows() >0){
			return $query->num_rows();
		}else{
			return 0;
		}//echo $this->db->last_query();
	}
	function DelRowRecord(){
		$id    =$this->input->post('id');
		$po_id =$this->input->post('po-id');
		$this->db->where('details_id',$id);
		$this->db->where('po_id',$po_id);
		$this->db->delete(DISTRIPO_DETAILS_TBL); //echo $this->db->last_query();
	}
	
	function DelRecord(){
		$id = $this->input->post('id');
		$this->db->select('*');
		$this->db->from(DISTRIPO_MASTER_TBL);
		$this->db->order_by('po_id','DESC');
		$this->db->limit(1,0);
		$query = $this->db->get();
		if($query->num_rows() >0){
			$po_id = $query->row()->po_id;
		}else{
			$po_id = 0;
		}
		if($id==$po_id){
		$this->db->where('po_id',$id);
		$this->db->delete(DISTRIPO_MASTER_TBL);
		$this->db->where('po_id',$id);
		$this->db->delete(DISTRIPO_DETAILS_TBL);
		}else{
			$USQL= "UPDATE ".DISTRIPO_MASTER_TBL." SET status='3' WHERE po_id=$id";
			$this->db->query($USQL);
			$USQL= "UPDATE ".DISTRIPO_DETAILS_TBL." SET status='3' WHERE po_id=$id";
			$this->db->query($USQL);
		}
	}
	
	function ApprovePO(){
		$id = $this->input->post('id');
		if($id>0){
    		$this->db->select('*');
    		$this->db->from(DISTRIPO_MASTER_TBL);
    		$this->db->where('po_id',$id);
    		$this->db->where('status',0);
    		$query = $this->db->get();
    		if($query->num_rows() >0){
    			$USQL= "UPDATE ".DISTRIPO_MASTER_TBL." SET status='1' WHERE po_id=$id";
    			$this->db->query($USQL);
    			$USQL= "UPDATE ".DISTRIPO_DETAILS_TBL." SET status='1' WHERE po_id=$id";
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
    		$this->db->from(DISTRIPO_MASTER_TBL);
    		$this->db->where('po_id',$id);
    		$this->db->where('status',1);
    		$query = $this->db->get();
    		if($query->num_rows() >0){
    			$USQL= "UPDATE ".DISTRIPO_MASTER_TBL." SET status='0' WHERE po_id=$id";
    			$this->db->query($USQL);
    			$USQL= "UPDATE ".DISTRIPO_DETAILS_TBL." SET status='0' WHERE po_id=$id";
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
		$this->db->from(DISTRIPO_DETAILS_TBL);
		$this->db->where('details_id', $details_id);
		$query = $this->db->get(); //echo $this->db->last_query();
		return $query->row();
	}
	
	function FillRecord(){
		$po_id	=$this->input->post('id');
		$this->db->select('*');
		$this->db->from(DISTRIPO_MASTER_TBL);
		$this->db->where('po_id', $po_id);
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
