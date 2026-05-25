<?php 
class Report_model extends CI_Model {
		
	function __construct()
	{
		parent::__construct();
	}
	function getStartDate($num){
		$SQL="SELECT DATE_SUB(CURDATE(),INTERVAL $num DAY) as start_date";
		$query = $this->db->query($SQL);
		return $query->row()->start_date;
	}	
	function GetWeekStartDate(){		
		$query1 = $this->db->query("SELECT DAYOFWEEK(CURRENT_DATE) as week_day_num");	
		$week_day_num = ($query1->row()->week_day_num -1);

		$query2 = $this->db->query("SELECT DATE_SUB(CURDATE(),INTERVAL $week_day_num DAY) as start_date");
		$start_date = $query2->row()->start_date;
		return $start_date;
	}
		
	function GetWeekDateList(){		
		$query = $this->db->query("SELECT endOfweek_date FROM ( SELECT DATE( schedule_date + INTERVAL( 7 - DAYOFWEEK( schedule_date ) ) DAY ) endOfweek_date FROM schedule WHERE year(schedule_date)=year(CURRENT_DATE) )A GROUP BY endOfweek_date");	
		return $query;
	}		
	function TimeToSecond($duration){		
		if ($duration) {
			$query = $this->db->query("SELECT SEC_TO_TIME($duration) AS duration");	
			return $query->row()->duration;
		}else{
			return "00:00:00";
		}
	}
	
	function GetCustomerName($customer_id){
		if($customer_id >0){
			$this->db->select('account_name,address');
			$this->db->from(CUSTOMER_TBL);
			$this->db->where('account_id', $customer_id);
			$query = $this->db->get();
			$row = $query->row();
			$Customer = "Sales To: ".$row->account_name;
			if($row->address !=""){
			//$Customer.="<br>".$row->address;
			}
			return $Customer;
		}else{
			return "";
		}
	}
	
	function GetAccountName($customer_id){
		if($customer_id >0){
		    
			$this->db->select('account_name,account_details');
			$this->db->from(ACC_HEAD_TBL);
			$this->db->where('account_id', $customer_id);
			$query = $this->db->get(); 
			if($query->num_rows() >0){
			  $row = $query->row();
			  $Customer = $row->account_name;
			  if($row->account_details !=""){
			  $Customer.="<br>".$row->account_details;
			  }
			}else{
			  $Customer ="Sales Manager";  
			}
		 return $Customer;
		}else{
			return "";
		}
	}
	//====== Start NHQ Report ============
	
	function PrintWorkorder(){
		$workorder_id	= $this->input->post('workorder-id');
		$customer_id	= $this->input->post('customer-id');
		$this->db->select('bl.*,p.*,i.company_name,i.address as company_address,i.phone as company_phone,i.mobile as company_mobile,i.email as company_email,DATE_FORMAT(bl.workorder_date ,"%d-%m-%Y") as workorder_date,DATE_FORMAT(bl.delivery_date ,"%d-%m-%Y") as delivery_date',FALSE);
		$this->db->from(WORKORDER_MASTER_TBL." AS bl");
		$this->db->join(CLIENT_TBL.' AS p', 'p.customer_id=bl.customer_id','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=bl.institute_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=bl.branch_id','LEFT');	
		$this->db->where("bl.workorder_id", $workorder_id);	
		$this->db->where("bl.customer_id", $customer_id);					
		$this->db->group_by('bl.workorder_id');
		$this->db->order_by('bl.workorder_id','ASC');			
		$mrow = $this->db->get()->row(); //echo $this->db->last_query();
		$workorder_date  = $mrow->workorder_date;						
		$account_name	 = $mrow->customer_full_name;
		if($mrow->workorder_type==1){ $workorder_type="Fresh";}elseif($mrow->workorder_type==2){ $workorder_type="Renual";}else{$workorder_type="Addon";}
	    if($mrow->payment_mode==1){ $payment_mode="Cash";}elseif($mrow->payment_mode==2){ $payment_mode="Cheque";}elseif($mrow->payment_mode==3){ $payment_mode="bkash";}else{$payment_mode="Others";}				
		if($this->session->userdata('user_role')<=100){$isPrint="";}else{$isPrint="no-print";}
		$headerTop=""; $header1=""; $header2="";
		$body="";$bodyfull=""; $bodytop=""; $bodybtm=""; $sl=0; 
		$FORM_TITLE ="CUSTOMER WORKORDER"; 
		$SalesPerson="";
		if($mrow->salesman_id >0){
			$this->db->select('account_name,account_details,mobile,email');
			$this->db->from(ACC_HEAD_TBL);
			$this->db->where('account_id', $mrow->salesman_id);
			$query = $this->db->get(); 
			if($query->num_rows() >0){
			  $prow = $query->row();
			  $SalesPerson=" 
			 <tr>
				<td class='text-left' width='12%' style='border: none;font-weight: normal;'>Sales Person</td>
				<td class='text-left' width='38%' style='border: none;font-weight: normal;'>$prow->account_name</td>
				<td class='text-left' width='12%' style='border: none;font-weight: normal;'>Designation</td>
				<td class='text-left' width='38%' style='border: none;font-weight: normal;'>$prow->account_details</td>
			 </tr>			
			 <tr>
				<td class='text-left' width='12%' style='border: none;font-weight: normal;'>Mobile</td>
				<td class='text-left' width='38%' style='border: none;font-weight: normal;'>$prow->mobile</td>
				<td class='text-left' width='12%' style='border: none;font-weight: normal;'>Email</td>
				<td class='text-left' width='38%' style='border: none;font-weight: normal;'>$prow->email</td>
			 </tr>";
			}
		}
		
		$headerTop="	
		<div class='bg-white text-center p-header hide'>		
		<img class='".$isPrint."' src='".base_url().ASSETS."/img/header.png' style='max-width:100%'/>
		</div>	
		<div class='bg-light text-center'>
		<h1 class='txt-black' style='padding-bottom:0px;padding-top:0px;margin:0px'>$FORM_TITLE</h1>
		</div>
		<table width='100%' id='data-table' class='table table-hover table-bordered' style='border: none !important'>
		<thead>
		  <tr>
		  	<td width='100%' colspan='8' class='text-center' style='border: none !important;padding-bottom:1px;'>
			<table style='width: 100% !important; border: none !important'>			
			<tr>
				<td class='text-left' width='12%' style='border: none;font-weight: normal;'>Workorder No.</td>
				<td class='text-left' width='38%' style='border: none;font-weight: normal;'>$mrow->workorder_no</td>
				<td class='text-left' width='12%' style='border: none;font-weight: normal;'>Workorder Date</td>
				<td class='text-left' width='38%' style='border: none;font-weight: normal;'>$mrow->workorder_date</td>
			</tr>			
			<tr>
				<td class='text-left' width='12%' style='border: none;font-weight: normal;'>Workorder Type</td>
				<td class='text-left' width='38%' style='border: none;font-weight: normal;'>$workorder_type</td>
				<td class='text-left' width='12%' style='border: none;font-weight: normal;'>Delivery Date</td>
				<td class='text-left' width='38%' style='border: none;font-weight: normal;'>$mrow->delivery_date</td>
			</tr>			
			<tr>
				<td class='text-left' width='12%' style='border: none;font-weight: normal;'>Ref. Name</td>
				<td class='text-left' width='38%' style='border: none;font-weight: normal;'>$mrow->contact_person ($mrow->designation)</td>
				<td class='text-left' width='12%' style='border: none;font-weight: normal;'>OEM</td>
				<td class='text-left' width='38%' style='border: none;font-weight: normal;'>$mrow->oem</td>
			</tr>
			<tr>
				<td class='text-left' width='12%' style='border: none;font-weight: normal;'>Supplier Name</td>
				<td class='text-left' width='38%' style='border: none;font-weight: normal;'>$mrow->company_name</td>
				<td class='text-left' width='12%' style='border: none;font-weight: normal;'>Buyer Name</td>
				<td class='text-left' width='38%' style='border: none;font-weight: normal;'>$mrow->customer_full_name</td>
			</tr>			
			<tr>
				<td class='text-left' width='12%' style='border: none;font-weight: normal;'>Supplier Address</td>
				<td class='text-left' width='38%' style='border: none;font-weight: normal;'>$mrow->company_address</td>
				<td class='text-left' width='12%' style='border: none;font-weight: normal;'>Buyer Address</td>
				<td class='text-left' width='38%' style='border: none;font-weight: normal;'>$mrow->address</td>
			</tr>
			$SalesPerson
			</table>
			</td>			
		  </tr>	
		</thead>
		<tbody>
		<tr>
	  	<td width='100%' colspan='8' class='text-center' style='border: none !important;padding-bottom:1px;'>
		<table style='width: 99% !important;' class='table table-hover table-bordered'>
		  <tr class='bg-primary'>
			<th width='2%' class='text-center'>SL</th>		
			<th width='33%' class='text-left'>Product Description</th>			
			<th width='13%' class='text-left'>Product SKU</th>			
			<th width='12%' class='text-left'>Remarks</th>			
			<th width='10%' class='text-left'>Validity</th>						
			<th width='8%' class='text-left'>Quantity</th>			
			<th width='10%' class='text-right'>Unit Price</th>
			<th width='12%' class='text-right'>Total Price</th>
		  </tr>
		";		
		$bodyfull.=$headerTop;
						
		$header1="";
		$billing_month=01;		
		$body.= $header1;
		$GTotalQty=0; $GTotalAmount=0;
		$bdcsql = "SELECT category FROM ".WORKORDER_DETAILS_TBL." WHERE customer_id=$customer_id AND workorder_id=$workorder_id";
		$bdcsql.= " GROUP BY category ORDER BY category ASC"; 
		$cquery = $this->db->query($bdcsql); 
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
		    $body.="<tr class='bg-gray'>
		  	   <th class='text-left' colspan='8'>".$category_name."</th>
		    </tr>";
		  
    		$this->db->select('d.*', FALSE);
    		$this->db->from(WORKORDER_MASTER_TBL.' AS m');	
    		$this->db->join(WORKORDER_DETAILS_TBL.' AS d', 'd.workorder_id=m.workorder_id','LEFT');
    		$this->db->where("m.customer_id", $customer_id);
    		$this->db->where("m.workorder_id", $workorder_id);
    		$this->db->where("d.category", $crow->category);	
    					
    		$this->db->group_by('d.details_id');
    		$this->db->order_by('d.details_id','ASC');			
    		$query = $this->db->get(); //echo $this->db->last_query();
    		$TotalQty=0; $TotalAmount=0; $sl=0; $cunit=""; $unit_price=0;
    		foreach($query->result() as $row){	 
    		$sl++; $TotalQty+=$row->quantity; $TotalAmount+=$row->total_price;	 
    		$GTotalQty+=$row->quantity; $GTotalAmount+=$row->total_price;
    		$unit_price = $row->unit_price;
    		$cunit=" pcs";
    		$body.="
    		<tr>
    		   <td class='text-center'>".$sl."</td>
    		   <td class='text-left'>".$row->product_description."</td>
    		   <td class='text-left'>".$row->product_sku."</td>
    		   <td class='text-left'>".$row->remarks."</td>
    		   <td class='text-left'>".$row->validity."</td>
    		   <td class='text-left'>".$row->quantity.$cunit."</td>
    		   <td class='text-right'>".number_format($unit_price, 2, '.', ',')."</td>
    		   <td class='text-right'>".number_format($row->total_price, 2, '.', ',')."</td>
    
    		</tr>";
    		}// end foreach									
    		
    		$body.="
    	    	 <tr class='bg-light'>
    			<td colspan='7' class='text-right'><strong>Total $category_name Amount:</strong></td>";
    		$body.="
    			<td class='text-right'><strong>".number_format($TotalAmount, 2, '.', ',')."</strong></td>
    	    	 </tr>";
		} // end foreach	  	 									
    		
		$body.="
	    	 <tr class='bg-dark'>
			<td colspan='7' class='text-right'><strong>Total Amount:</strong></td>";
		$body.="
			<td class='text-right'><strong>".number_format($GTotalAmount, 2, '.', ',')."</strong></td>
	    	 </tr>";
		
		$body.="";
		if($mrow->discount_amount >0){
		$body.="
	    	 <tr class='bg-info'>
			  <td colspan='7' class='text-right'><strong>Less Discount Amount:</strong></td>
			  <td class='text-right'><strong>".number_format($mrow->discount_amount, 2, '.', ',')."</strong></td>
	    	 </tr>
	    	 <tr>
			  <td colspan='7' class='text-right'><strong>Sub Total Amount:</strong></td>
			  <td class='text-right'><strong>".number_format($mrow->sub_total, 2, '.', ',')."</strong></td>
	    	 </tr>";
		}
		
		if($mrow->vat_percentage >=0){
		    if($mrow->including_vat==0){		
		     $body.="
	             <tr class='bg-info'>
			<td colspan='7' class='text-right'><strong>Add VAT Amount ($mrow->vat_percentage%):</strong></td>
			<td class='text-right'><strong>".number_format($mrow->vat_amount, 2, '.', ',')."</strong></td>
	             </tr>";
		   }else{		
		     $body.="
	             <tr class='bg-info'>
			<td colspan='7' class='text-right'><strong>Less Including VAT Amount ($mrow->vat_percentage%):</strong></td>
			<td class='text-right'><strong>".number_format($mrow->vat_amount, 2, '.', ',')."</strong></td>
	             </tr>";

		   }		
		   $body.="
	             <tr>
			<td colspan='7' class='text-right'><strong>Grand Total Amount:</strong></td>
			<td class='text-right'><strong>".number_format($mrow->grand_total, 2, '.', ',')."</strong></td>
	             </tr>";
		}
		if($mrow->ait_percentage >0){
		    if($mrow->including_vat==0){		
		     $body.="
	              <tr class='bg-info'>
			<td colspan='7' class='text-right'><strong>Add AIT Amount ($mrow->ait_percentage%) :</strong></td>
			<td class='text-right'><strong>".number_format($mrow->ait_amount, 2, '.', ',')."</strong></td>
	              </tr>";
		    }else{		
		     $body.="
	              <tr class='bg-info'>
			<td colspan='7' class='text-right'><strong>Less Including AIT Amount ($mrow->ait_percentage%) :</strong></td>
			<td class='text-right'><strong>".number_format($mrow->ait_amount, 2, '.', ',')."</strong></td>
	              </tr>";
		    }
		    $body.="
	              <tr>
			<td colspan='7' class='text-right'><strong>Net Receivable Amount:</strong></td>
			<td class='text-right'><strong>".number_format($mrow->net_bill_amount, 2, '.', ',')."</strong></td>
	              </tr>";
		}else{		
		$body.="
	    	<tr>
			<td colspan='7' class='text-right'><strong>Net Receivable Amount:</strong></td>
			<td class='text-right'><strong>".number_format($mrow->net_bill_amount, 2, '.', ',')."</strong></td>
	    	</tr>";
		}
		$net_bill_amount = number_format($mrow->net_bill_amount, 2, '.', '');
				
		$inwords 	="";
		$numberArr 	= explode(".",$net_bill_amount);
		$number1 	= $numberArr[0];
		$number2 	= $numberArr[1];

		$inwords = $this->InWords($number1);
		if($number2 >0){
		$inwords2= $this->InWords($number2);
		$inwords.= " Taka ".$inwords2." paisa ";
		}else{ $inwords.=" Taka ";} 
		$inwords.= " Only";

		$body.="
	    	 <tr class='bg-light'>
			<td colspan='8' class='text-left'><strong>In Words : ".$inwords."</strong></td>
	    	 </tr>
	    </td>
	    </tr>
	    ";
		if($mrow->workorder_note!="NaN" || $mrow->workorder_note!=""){
		$body.="
	    	<tr class='bg-light'>
			<td colspan='8' class='text-left' style='border: none !important; padding-top:4px;padding-bottom:2px;'>
			<table width='100%' id='data-table' class='table table-bordered brand-tbl'>
			<tr>
			<td class='text-left' width='18%'><strong>Note :</strong></td>
			<td colspan='7' class='text-left' width='82%'>".nl2br($mrow->workorder_note)."</td>
			</tr>
			<tr>
			<td class='text-left' width='18%'><strong>Delivery Address :</strong></td>
			<td colspan='7' class='text-left' width='82%'>".nl2br($mrow->shipping_address)."</td>
			</tr>
			</table>
			</td>
	    	 </tr>";
		}
		if($mrow->payment_terms!="NaN" && $mrow->payment_terms!=""){
		$body.="
	    	<tr class='bg-light'>
			<td colspan='8' class='text-left' style='border: none !important;padding-top:4px;padding-bottom:2px;'>
			<table width='100%' id='data-table' class='table table-bordered brand-tbl'>
			<tr>
			<td class='text-left' width='18%'><strong>Payment Mode :</strong></td>
			<td colspan='7' class='text-left' width='82%'>".$payment_mode."</td>
			</tr>
			<tr>
			<td class='text-left' width='18%'><strong>Payment Terms :</strong></td>
			<td colspan='7' class='text-left' width='82%'>".nl2br($mrow->payment_terms)."</td>
			</tr>
			</table>
			</td>
	    	 </tr>";
		}
		//".nl2br($this->session->userdata('address'))."
		$body.="
		<tr>
	       <td colspan='5' class='text-left' style='padding-top:20px;'>
			On behalf of <strong>".$this->session->userdata('company_name')."</strong><br>
			
			<div style='padding-top:25px;padding-bottom:0px'>----------------------------</div>
			Signature & Date
	            </td>
		    <td colspan='3' class='text-right' style='padding-top:20px;'>
			On behalf of <strong>".$account_name."</strong>
			<div style='padding-top:25px;padding-bottom:0px'>----------------------------</div>
			Signature & Date 
	            </td>
	        </tr>
		</tbody>		
		<tfoot>
	        <tr>
	            <td id='footer' colspan='8' class='text-center'>&nbsp;</td>
	        </tr>
	    	</tfoot>				
		</table>
		<div id='p-footer'>
		<img class='".$isPrint." hide' src='".base_url().ASSETS."/img/footer.png' style='max-width:100%;'/>
		</div>
		";
		$bodyfull.=$body;		

		echo $bodyfull;
	}
	
	
	function PrintProformaInvoice(){
		$po_id	        = $this->input->post('po-id');
		$distributor_id	= $this->input->post('distributor-id');
		$this->db->select('bl.*,d.distributor_full_name as distributor_name,d.address as distri_address,d.contact_person as distri_contactperson,d.designation as distri_designation,d.phone as distri_phone,d.fax as distri_fax,d.mobile as distri_mobile,d.email as distri_email,im.importer_full_name as importer_name,im.address as importer_address,im.contact_person as importer_contactperson,im.designation as importer_designation,im.phone as importer_phone,im.fax as importer_fax,im.mobile as importer_mobile,im.email as importer_email, c.customer_full_name as customer_name,c.address as customer_address,c.contact_person as customer_contactperson,c.designation as customer_designation,c.phone as customer_phone,c.fax as customer_fax,c.mobile as customer_mobile,c.email as customer_email,i.company_name,i.address as company_address,i.phone as company_phone,i.mobile as company_mobile,i.email as company_email,DATE_FORMAT(bl.po_date ,"%d-%m-%Y") as po_date',FALSE);
		$this->db->from(DISTRIPO_MASTER_TBL." AS bl");
		$this->db->join(DISTRIBUTOR_TBL.' AS d', 'd.distributor_id=bl.distributor_id','LEFT');
		$this->db->join(CLIENT_TBL.' AS c', 'c.customer_id=bl.customer_id','LEFT');
		$this->db->join(IMPORTER_TBL.' AS im', 'im.importer_id=bl.importer_id','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=bl.institute_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=bl.branch_id','LEFT');	
		$this->db->where("bl.po_id", $po_id);	
		$this->db->where("bl.distributor_id", $distributor_id);	
		$this->db->where("bl.status", 1);					
		$this->db->group_by('bl.po_id');
		$this->db->order_by('bl.po_id','ASC');			
		$mrow = $this->db->get()->row(); //echo $this->db->last_query();
		$po_date  = $mrow->po_date;						
		$account_name	 = $mrow->distributor_name;
		if($mrow->payment_mode==1){ $payment_mode="Cash";}elseif($mrow->payment_mode==2){ $payment_mode="Cheque";}elseif($mrow->payment_mode==3){ $payment_mode="bkash";}elseif($mrow->payment_mode==4){ $payment_mode="TT";}else{$payment_mode="Others";}				
		if($this->session->userdata('user_role')<=100){$isPrint="";}else{$isPrint="no-print";}
		$headerTop=""; $header1=""; $header2="";
		$body="";$bodyfull=""; $bodytop=""; $bodybtm=""; $sl=0; 
		$FORM_TITLE ="PURCHASE ORDER"; 
		//<img class='".$isPrint."' src='".base_url().ASSETS."/img/header.png' style='max-width:100%'/>
		$headerTop="	
		<div class='bg-white text-center p-header hide'>		
		
		</div>	
		<div class='bg-light text-center'>
		<h1 class='txt-black' style='padding-bottom:0px;padding-top:0px;margin:0px'>$FORM_TITLE</h1>
		</div>
		<table width='100%' id='data-table' class='table table-hover table-bordered' style='border: none !important'>
		<thead>
		  <tr>
		  	<td width='100%' colspan='8' class='text-center' style='border: none !important;padding-bottom:1px;'>
			<table style='width: 100% !important; border: none !important'>			
			<tr>
				<td class='text-left' width='12%' style='border: none;font-weight: normal;'>PO NO.</td>
				<td class='text-left' width='50%' style='border: none;font-weight: normal;'>$mrow->po_no</td>
				<td class='text-right' width='38%' colspan='2' style='border: none;font-weight: normal;'>PO Date: $mrow->po_date</td>
			</tr>			
			<tr>
				<td class='text-left' width='12%' style='border: none;font-weight: bold;'>To</td>
				<td class='text-left' width='38%' colspan='3' style='border: none;font-weight: bold;'>$mrow->distributor_name</td>
			</tr>			
			<tr>
				<td class='text-left' width='12%' style='border: none;font-weight: bold;'>Attention</td>
				<td class='text-left' width='38%' colspan='3' style='border: none;font-weight: bold;'>$mrow->attention</td>
			</tr>
			<tr>
				<td class='text-left' width='12%' style='border: none;font-weight: normal;'>Subject</td>
				<td class='text-left' width='38%' colspan='3' style='border: none;font-weight: normal;'>$mrow->subject</td>
			</tr>			
			<tr>
				<td class='text-left' width='12%' colspan='4' style='border: none;font-weight: normal;'>
				Dear Sir, <br> We are pleased to place you the following purchase order:
				</td>
			</tr>
			</table>
			</td>			
		  </tr>	
		</thead>
		<tbody>
		<tr>
	  	<td width='100%' colspan='8' class='text-center' style='border: none !important;padding-bottom:1px;'>
		<table style='width: 99% !important;' class='table table-hover table-bordered'>
		  <tr class='bg-primary'>
			<th width='2%' class='text-center'>SL</th>		
			<th width='33%' class='text-left'>Product Description</th>			
			<th width='13%' class='text-left'>Product SKU</th>			
			<th width='12%' class='text-left'>Remarks</th>			
			<th width='10%' class='text-left'>Validity</th>						
			<th width='8%' class='text-left'>Quantity</th>			
			<th width='10%' class='text-right'>Unit Price</th>
			<th width='12%' class='text-right'>Total Price</th>
		  </tr>
		";		
		$bodyfull.=$headerTop;
						
		$header1="";
		$billing_month=01;		
		$body.= $header1;
										
		$this->db->select('d.*', FALSE);
		$this->db->from(DISTRIPO_MASTER_TBL.' AS m');	
		$this->db->join(DISTRIPO_DETAILS_TBL.' AS d', 'd.po_id=m.po_id','LEFT');
		$this->db->where("m.distributor_id", $distributor_id);
		$this->db->where("m.po_id", $po_id);
		$this->db->where("m.status", 1);	
					
		$this->db->group_by('d.details_id');
		$this->db->order_by('d.details_id','ASC');			
		$query = $this->db->get(); //echo $this->db->last_query();
		$TotalQty=0; $TotalAmount=0; $sl=0; $cunit=""; $unit_price=0;
		foreach($query->result() as $row){	 
		$sl++; $TotalQty+=$row->quantity; $TotalAmount+=$row->total_price;
		$unit_price = $row->unit_price;
		$cunit=" pcs";
		$body.="
		<tr>
		   <td class='text-center'>".$sl."</td>
		   <td class='text-left'>".$row->product_description."</td>
		   <td class='text-left'>".$row->product_sku."</td>
		   <td class='text-left'>".$row->remarks."</td>
		   <td class='text-left'>".$row->validity."</td>
		   <td class='text-left'>".$row->quantity.$cunit."</td>
		   <td class='text-right'>".number_format($unit_price, 2, '.', ',')."</td>
		   <td class='text-right'>".number_format($row->total_price, 2, '.', ',')."</td>

		</tr>";
		}// end foreach									
		
		$body.="
		
	    	 <tr class='bg-gray'>
			<td colspan='7' class='text-right'><strong>Total Amount:</strong></td>";
		$body.="
			<td class='text-right'><strong>".number_format($TotalAmount, 2, '.', ',')."</strong></td>
	    	 </tr>";
		
		$body.="";
		if($mrow->discount_amount >0){
		$body.="
	    	 <tr class='bg-info'>
			  <td colspan='7' class='text-right'><strong>Discount Amount:</strong></td>
			  <td class='text-right'><strong>".number_format($mrow->discount_amount, 2, '.', ',')."</strong></td>
	    	 </tr>
	    	 <tr>
			  <td colspan='7' class='text-right'><strong>Sub Total Amount:</strong></td>
			  <td class='text-right'><strong>".number_format($mrow->sub_total, 2, '.', ',')."</strong></td>
	    	 </tr>";
		}
		
		if($mrow->vat_percentage >=0){
		    if($mrow->including_vat==0){		
		     $body.="
	             <tr class='bg-info'>
			<td colspan='7' class='text-right'><strong>Add VAT Amount ($mrow->vat_percentage%):</strong></td>
			<td class='text-right'><strong>".number_format($mrow->vat_amount, 2, '.', ',')."</strong></td>
	      	     </tr>";
		   }else{		
		     $body.="
	             <tr class='bg-info'>

			<td colspan='7' class='text-right'><strong>Less Including VAT Amount ($mrow->vat_percentage%):</strong></td>
			<td class='text-right'><strong>".number_format($mrow->vat_amount, 2, '.', ',')."</strong></td>
	             </tr>";

		   }		
		   $body.="	      
	           <tr>
			<td colspan='7' class='text-right'><strong>Grand Total Amount:</strong></td>
			<td class='text-right'><strong>".number_format($mrow->grand_total, 2, '.', ',')."</strong></td>
	           </tr>";
		}

		if($mrow->ait_percentage >0){
		    if($mrow->including_vat==0){		
		    $body.="
	            <tr class='bg-info'>
			<td colspan='7' class='text-right'><strong>Add AIT Amount ($mrow->ait_percentage%) :</strong></td>
			<td class='text-right'><strong>".number_format($mrow->ait_amount, 2, '.', ',')."</strong></td>
	            </tr>";
		    }else{		
		    $body.="
	            <tr class='bg-info'>
			<td colspan='7' class='text-right'><strong>Less Including AIT Amount ($mrow->ait_percentage%) :</strong></td>
			<td class='text-right'><strong>".number_format($mrow->ait_amount, 2, '.', ',')."</strong></td>
	            </tr>";

		    }		
		    $body.="
	            <tr>
			<td colspan='7' class='text-right'><strong>Net Receivable Amount:</strong></td>
			<td class='text-right'><strong>".number_format($mrow->net_bill_amount, 2, '.', ',')."</strong></td>
	            </tr>";
		}else{		
		$body.="
	    	<tr>
			<td colspan='7' class='text-right'><strong>Net Receivable Amount:</strong></td>
			<td class='text-right'><strong>".number_format($mrow->net_bill_amount, 2, '.', ',')."</strong></td>
	    	</tr>";
		}
		$net_bill_amount = number_format($mrow->net_bill_amount, 2, '.', '');
				
		$inwords 	="";
		$numberArr 	= explode(".",$net_bill_amount);
		$number1 	= $numberArr[0];
		$number2 	= $numberArr[1];

		$inwords = $this->InWords($number1);
		if($number2 >0){
		    if($mrow->currency_id > 1){
		       $inwords2= $this->InWords($number2);
		       $inwords.= " ".$mrow->currency." ".$inwords2." Cent "; 
		    }else{
		       $inwords2= $this->InWords($number2);
		       $inwords.= " Taka ".$inwords2." Paisa ";
		    }
		}else{ 
		    if($mrow->currency_id > 1){
		        $inwords.=" ".$mrow->currency." ";
		    }else{
		       $inwords.=" Taka ";
		    }
		    
		} 
		$inwords.= " Only";

		$body.="
	    	 <tr class='bg-light'>
			<td colspan='8' class='text-left'><strong>In Words : ".$inwords."</strong></td>
	    	 </tr>
	    </td>
	    </tr>
	    </table>
		<tr>
			<td class='text-left' colspan='8' width='100%' style='border: none;font-weight: bold;'>End User Details:</td>
		</tr>
		<tr>
		    <td class='text-left' width='100%' colspan='8' style='border: none;font-weight: normal;'>
			<strong>$mrow->customer_name</strong> <br>
			$mrow->customer_address<br>
			Telephone: $mrow->customer_phone <br>
			</td>
		</tr>
		<tr>
			<td class='text-left' colspan='8' width='100%' style='border: none;font-weight: normal;'>Contact Person: $mrow->customer_contactperson</td>
		</tr>
		<tr>
			<td class='text-left' colspan='8' width='100%' style='border: none;font-weight: normal;'>Email &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: $mrow->customer_email</td>
		</tr>
	    ";
	    
		if($this->session->userdata('address')!="" || $mrow->ship_to!="" || $mrow->bill_to!=""){
		$body.="
	    	<tr class='bg-light'>
			<td colspan='8' class='text-left' style='border: none;padding-top:4px;padding-bottom:1px;'>
			<table width='100%' id='data-table' class='table table-bordered brand-tbl'>
			<tr>
			<td class='text-left' width='18%'><strong>Delivery To :</strong></td>
			<td colspan='7' class='text-left' width='82%'>".nl2br($this->session->userdata('address'))."</td>
			</tr>";
		    if($mrow->ship_to!="NaN" && $mrow->ship_to!=""){
		    $body.="
		    <tr>
			<td class='text-left' width='18%'><strong>Ship To :</strong></td>
			<td colspan='7' class='text-left' width='82%'>".nl2br($mrow->ship_to)."</td>
			</tr>";
		    }
		    if($mrow->bill_to!="NaN" && $mrow->bill_to!=""){
    		$body.="
    	    	<tr>
    			<td class='text-left' width='18%'><strong>Bill To :</strong></td>
    			<td colspan='7' class='text-left' width='82%'>".nl2br($mrow->bill_to)."</td>
    			</tr>";
    		}
		$body.="
			</table>
			</td>
	    	 </tr>";
		}
		
		
		if($mrow->payment_terms!="NaN" && $mrow->payment_terms!=""){
		$body.="
	    	<tr class='bg-light'>
			<td colspan='8' class='text-left' style='border: none;padding-top:4px;padding-bottom:2px;'>
			<table width='100%' id='data-table' class='table table-bordered brand-tbl'>
			<tr>
			<td class='text-left' width='18%'><strong>Payment Mode :</strong></td>
			<td colspan='7' class='text-left' width='82%'>".$payment_mode."</td>
			</tr>
			<tr>
			<td class='text-left' width='18%'><strong>Payment Terms :</strong></td>
			<td colspan='7' class='text-left' width='82%'>".nl2br($mrow->payment_terms)."</td>
			</tr>
			</table>
			</td>
	    	 </tr>";
		}
		//".nl2br($this->session->userdata('address'))."
		$body.="
		<tr>
	       <td colspan='5' class='text-left' style='padding-top:20px;'>
			On behalf of <strong>".$this->session->userdata('company_name')."</strong><br>
			<div style='padding-top:25px;padding-bottom:0px'>----------------------------</div>
			Signature & Date
	            </td>
		    <td colspan='3' class='text-right' style='padding-top:20px;'>
			On behalf of <strong>".$account_name."</strong>
			<div style='padding-top:25px;padding-bottom:0px'>----------------------------</div>
			Signature & Date 
	            </td>
	        </tr>
		</tbody>		
		<tfoot>
	        <tr>
	            <td id='footer' colspan='8' class='text-center'>&nbsp;</td>
	        </tr>
	    	</tfoot>				
		</table>
		<div id='p-footer'>
		
		</div>
		";
		//<img class='".$isPrint." hide' src='".base_url().ASSETS."/img/footer.png' style='max-width:100%;'/>
		$bodyfull.=$body;		

		echo $bodyfull;
	}
	
	//========= Start Importer PO ========
	
	function PrintImporterPO(){
		$po_id	        = $this->input->post('po-id'); $po_date =""; $ipo_no="";
		$importer_id	= $this->input->post('importer-id');
		$this->db->select('bl.*,d.distributor_full_name as distributor_name,d.address as distri_address,d.contact_person as distri_contactperson,d.designation as distri_designation,d.phone as distri_phone,d.fax as distri_fax,d.mobile as distri_mobile,d.email as distri_email,im.importer_full_name as importer_name,im.address as importer_address,im.contact_person as importer_contactperson,im.designation as importer_designation,im.phone as importer_phone,im.fax as importer_fax,im.mobile as importer_mobile,im.email as importer_email, c.customer_full_name as customer_name,c.address as customer_address,c.contact_person as customer_contactperson,c.designation as customer_designation,c.phone as customer_phone,c.fax as customer_fax,c.mobile as customer_mobile,c.email as customer_email,i.company_name,i.address as company_address,i.phone as company_phone,i.mobile as company_mobile,i.email as company_email,DATE_FORMAT(bl.po_date ,"%d-%m-%Y") as po_date',FALSE);
		$this->db->from(DISTRIPO_MASTER_TBL." AS bl");
		$this->db->join(DISTRIBUTOR_TBL.' AS d', 'd.distributor_id=bl.distributor_id','LEFT');
		$this->db->join(CLIENT_TBL.' AS c', 'c.customer_id=bl.customer_id','LEFT');
		$this->db->join(IMPORTER_TBL.' AS im', 'im.importer_id=bl.importer_id','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=bl.institute_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=bl.branch_id','LEFT');	
		$this->db->where("bl.po_id", $po_id);	
		$this->db->where("bl.importer_id", $importer_id);	
		$this->db->where("bl.status", 1);					
		$this->db->group_by('bl.po_id');
		$this->db->order_by('bl.po_id','ASC');			
		$mrow = $this->db->get()->row(); //echo $this->db->last_query();
		$po_date  = $mrow->po_date;
		if($mrow->ipo_no !=""){
		$ipo_no   = $mrow->ipo_no;
		}else{
		$ipo_no   = $mrow->po_no;
		}						
		$account_name	 = $mrow->importer_name;
		
		$profit_margin_percentage  = 10;
	    	$profit_margin  = (($mrow->net_bill_amount/100) * $profit_margin_percentage);
	    	
	    	$importer_value = ($mrow->net_bill_amount - ($profit_margin));
		
		if($mrow->payment_mode==1){ $payment_mode="Cash";}elseif($mrow->payment_mode==2){ $payment_mode="Cheque";}elseif($mrow->payment_mode==3){ $payment_mode="bkash";}elseif($mrow->payment_mode==4){ $payment_mode="TT";}else{$payment_mode="Others";}				
		if($this->session->userdata('user_role')<=100){$isPrint="";}else{$isPrint="no-print";}
		$headerTop=""; $header1=""; $header2="";
		$body="";$bodyfull=""; $bodytop=""; $bodybtm=""; $sl=0; 
		$FORM_TITLE ="PURCHASE ORDER"; 
		//<img class='".$isPrint."' src='".base_url().ASSETS."/img/header.png' style='max-width:100%'/>
		$headerTop="	
		<div class='bg-white text-center p-header hide'>		
		
		</div>	
		<div class='bg-light text-center'>
		<h1 class='txt-black' style='padding-bottom:0px;padding-top:0px;margin:0px'>$FORM_TITLE</h1>
		</div>
		<table width='100%' id='data-table' class='table table-hover table-bordered' style='border: none !important'>
		<thead>
		  <tr>
		  	<td width='100%' colspan='8' class='text-center' style='border: none !important;padding-bottom:1px;'>
			<table style='width: 100% !important; border: none !important'>			
			<tr>
				<td class='text-left' width='12%' style='border: none;font-weight: normal;'>PO NO.</td>
				<td class='text-left' width='50%' style='border: none;font-weight: normal;'>$ipo_no</td>
				<td class='text-right' width='38%' colspan='2' style='border: none;font-weight: normal;'>PO Date: $mrow->po_date</td>
			</tr>			
			<tr>
				<td class='text-left' width='12%' style='border: none;font-weight: bold;'>To</td>
				<td class='text-left' width='38%' colspan='3' style='border: none;font-weight: bold;'>$mrow->importer_name</td>
			</tr>			
			<tr>
				<td class='text-left' width='12%' style='border: none;font-weight: bold;'>Attention</td>
				<td class='text-left' width='38%' colspan='3' style='border: none;font-weight: bold;'>$mrow->attention</td>
			</tr>
			<tr>
				<td class='text-left' width='12%' style='border: none;font-weight: normal;'>Subject</td>
				<td class='text-left' width='38%' colspan='3' style='border: none;font-weight: normal;'>$mrow->subject</td>
			</tr>			
			<tr>
				<td class='text-left' width='12%' colspan='4' style='border: none;font-weight: normal;'>
				Dear Sir, <br> We are pleased to place you the following purchase order:
				</td>
			</tr>
			</table>
			</td>			
		  </tr>	
		</thead>
		<tbody>
		<tr>
	  	<td width='100%' colspan='8' class='text-center' style='border: none !important;padding-bottom:1px;'>
		<table style='width: 99% !important;' class='table table-hover table-bordered'>
		  <tr class='bg-primary'>
			<th width='2%' class='text-center'>SL</th>		
			<th width='33%' class='text-left'>Product Description</th>			
			<th width='13%' class='text-left'>Product SKU</th>			
			<th width='12%' class='text-left'>Remarks</th>			
			<th width='10%' class='text-left'>Validity</th>						
			<th width='8%' class='text-left'>Quantity</th>			
			<th width='10%' class='text-right'>Unit Price</th>
			<th width='12%' class='text-right'>Total Price</th>
		  </tr>
		";		
		$bodyfull.=$headerTop;
						
		$header1=""; $TotalRow=0;
		$billing_month=01;		
		$body.= $header1;
										
		$this->db->select('d.*', FALSE);
		$this->db->from(DISTRIPO_MASTER_TBL.' AS m');	
		$this->db->join(DISTRIPO_DETAILS_TBL.' AS d', 'd.po_id=m.po_id','LEFT');
		$this->db->where("m.importer_id", $importer_id);
		$this->db->where("m.po_id", $po_id);
		
		$this->db->where("m.status", 1);	
					
		$this->db->group_by('d.details_id');
		$this->db->order_by('d.details_id','ASC');			
		$query = $this->db->get(); //echo $this->db->last_query();
		$TotalRow = $query->num_rows();
		$TotalQty=0; $TotalAmount=0; $sl=0; $cunit=""; $unit_price=0;
		foreach($query->result() as $row){	 
		$sl++; $TotalQty+=$row->quantity; $TotalAmount+=$row->total_price;
		$unit_price = $row->unit_price;
		$cunit=" pcs";
		 if($sl==1){
		 $body.="
		 <tr>
		   <td class='text-center'>".$sl."</td>
		   <td class='text-left'>".$row->product_description."</td>
		   <td class='text-left'>".$row->product_sku."</td>
		   <td class='text-left'>".$row->remarks."</td>
		   <td class='text-left'>".$row->validity."</td>
		   <td class='text-left'>".$row->quantity.$cunit."</td>
		   <td class='text-right'>".number_format(0, 2, '.', ',')."</td>
		   <td class='text-right' rowspan='".$TotalRow."'>".number_format($mrow->importer_value, 2, '.', ',')."</td>
		 </tr>";
		 }else{
		 $body.="
		 <tr>
		   <td class='text-center'>".$sl."</td>
		   <td class='text-left'>".$row->product_description."</td>
		   <td class='text-left'>".$row->product_sku."</td>
		   <td class='text-left'>".$row->remarks."</td>
		   <td class='text-left'>".$row->validity."</td>
		   <td class='text-left'>".$row->quantity.$cunit."</td>
		   <td class='text-right'>".number_format(0, 2, '.', ',')."</td>
		 </tr>";
		    
		 }
		}// end foreach									
		
		$body.="
	    	 <tr class='bg-gray'>
			<td colspan='7' class='text-right'><strong>Total Amount:</strong></td>";
		$body.="
			<td class='text-right'><strong>".number_format($mrow->importer_value, 2, '.', ',')."</strong></td>
	    	 </tr>";
		
		$body.="";
		/*
		if($mrow->vat_percentage >=0){	
		$grand_total = ($importer_value - $mrow->vat_amount);
		$body.="
	      <tr class='bg-info'>
			<td colspan='7' class='text-right'><strong>Less VAT Amount :</strong></td>
			<td class='text-right'><strong>".number_format($mrow->vat_amount, 2, '.', ',')."</strong></td>
	      </tr>
	      <tr>
			<td colspan='7' class='text-right'><strong>Grand Total Amount:</strong></td>
			<td class='text-right'><strong>".number_format($grand_total, 2, '.', ',')."</strong></td>
	      </tr>";
		}
		if($mrow->ait_percentage >0){   
		$body.="
	      <tr class='bg-info'>
			<td colspan='7' class='text-right'><strong>Less AIT Amount :</strong></td>
			<td class='text-right'><strong>".number_format($mrow->ait_amount, 2, '.', ',')."</strong></td>
	      </tr>
	      <tr>
			<td colspan='7' class='text-right'><strong>Net Import Amount:</strong></td>
			<td class='text-right'><strong>".number_format($mrow->importer_value, 2, '.', ',')."</strong></td>
	      </tr>";
		}else{		
		$body.="
	    	<tr>
			<td colspan='7' class='text-right'><strong>Net Import Amount:</strong></td>
			<td class='text-right'><strong>".number_format($mrow->importer_value, 2, '.', ',')."</strong></td>
	    	</tr>";
		}
		*/
		
		$net_bill_amount = number_format($mrow->importer_value, 2, '.', '');
				
		$inwords 	="";
		$numberArr 	= explode(".",$net_bill_amount);
		$number1 	= $numberArr[0];
		$number2 	= $numberArr[1];

		$inwords = $this->InWords($number1);
		if($number2 >0){
		$inwords2= $this->InWords($number2);
		$inwords.= " Taka ".$inwords2." paisa ";
		}else{ $inwords.=" Taka ";} 
		$inwords.= " Only";

		$body.="
	    	 <tr class='bg-light'>
			<td colspan='8' class='text-left'><strong>In Words : ".$inwords."</strong></td>
	    	 </tr>
	    </td>
	    </tr>
		<tr>
			<td class='text-left' colspan='8' width='100%' style='border: none;font-weight: bold;'>End User Details:</td>
		</tr>
		<tr>
		    <td class='text-left' width='100%' colspan='8' style='border: none;font-weight: normal;'>
			<strong>$mrow->customer_name</strong> <br>
			$mrow->customer_address<br>
			Telephone: $mrow->customer_phone <br>
			</td>
		</tr>
		<tr>
			<td class='text-left' colspan='8' width='100%' style='border: none;font-weight: normal;'>Contact Person: $mrow->customer_contactperson</td>
		</tr>
		<tr>
			<td class='text-left' colspan='8' width='100%' style='border: none;font-weight: normal;'>Email &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: $mrow->customer_email</td>
		</tr>
	    ";
	    
		if($this->session->userdata('address')!="" || $mrow->ship_to!="" || $mrow->bill_to!=""){
		$body.="
	    	<tr class='bg-light'>
			<td colspan='8' class='text-left' style='border: none;padding-top:4px;padding-bottom:2px;'>
			
			<table width='100%' class='table table-bordered brand-tbl'>
			<tr>
			<td class='text-left' width='18%'><strong>Delivery To :</strong></td>
			<td colspan='7' class='text-left' width='82%'>".nl2br($this->session->userdata('address'))."</td>
			</tr>";
			
    		if($mrow->ship_to!="NaN" && $mrow->ship_to!=""){
    		    $body.="
    	    	<tr>
    			<td class='text-left' width='18%'><strong>Ship To :</strong></td>
    			<td colspan='7' class='text-left' width='82%'>".nl2br($mrow->ship_to)."</td>
    			</tr>";
    		}
    		if($mrow->bill_to!="NaN" && $mrow->bill_to!=""){
    		    $body.="
    	    	<tr>
    			<td class='text-left' width='18%'><strong>Bill To :</strong></td>
    			<td colspan='7' class='text-left' width='82%'>".nl2br($mrow->bill_to)."</td>
    			</tr>";
    		}
			$body.="
			</table>
			</td>
	    	 </tr>";
		}
		if($mrow->payment_terms!="NaN" && $mrow->payment_terms!=""){
		$body.="
	    	<tr class='bg-light'>
			<td colspan='8' class='text-left' style='border: none;padding-top:4px;padding-bottom:2px;'>
			<table width='100%' id='data-table' class='table table-bordered brand-tbl'>
			<tr>
			<td class='text-left' width='18%'><strong>Payment Mode :</strong></td>
			<td colspan='7' class='text-left' width='82%'>".$payment_mode."</td>
			</tr>
			<tr>
			<td class='text-left' width='18%'><strong>Payment Terms :</strong></td>
			<td colspan='7' class='text-left' width='82%'>".nl2br($mrow->payment_terms)."</td>
			</tr>
			</table>
			</td>
	    	 </tr>";
		}
		$body.="
		<tr>
	       <td colspan='5' class='text-left' style='padding-top:20px;'>
			On behalf of <strong>".$this->session->userdata('company_name')."</strong><br>
			<div style='padding-top:25px;padding-bottom:0px'>----------------------------</div>
			Signature & Date
	            </td>
		    <td colspan='3' class='text-right' style='padding-top:20px;'>
			On behalf of <strong>".$account_name."</strong>
			<div style='padding-top:25px;padding-bottom:0px'>----------------------------</div>
			Signature & Date 
	            </td>
	        </tr>
		</tbody>		
		<tfoot>
	        <tr>
	            <td id='footer' colspan='8' class='text-center'>&nbsp;</td>
	        </tr>
	    	</tfoot>				
		</table>
		<div id='p-footer'>
		
		</div>
		";
		//<img class='".$isPrint." hide' src='".base_url().ASSETS."/img/footer.png' style='max-width:100%;'/>
		$bodyfull.=$body;		

		echo $bodyfull;
	}
	
	//========= Start Delivery Challan ========
	
	function PrintDeliveryChallan(){
		$challan_id	    = $this->input->post('challan-id');
		$workorder_id	= $this->input->post('workorder-id');
		$this->db->select('bl.*,w.workorder_no,w.oem, c.customer_full_name as customer_name,c.address as customer_address,c.shipping_address,c.contact_person as customer_contactperson,c.designation as customer_designation,c.phone as customer_phone,c.fax as customer_fax,c.mobile as customer_mobile,c.email as customer_email,i.company_name,i.address as company_address,i.phone as company_phone,i.mobile as company_mobile,i.email as company_email,DATE_FORMAT(w.workorder_date ,"%d-%m-%Y") as workorder_date,DATE_FORMAT(bl.challan_date ,"%d-%m-%Y") as challan_date',FALSE);
		$this->db->from(CHALLAN_MASTER_TBL." AS bl");
		$this->db->join(WORKORDER_MASTER_TBL.' AS w', 'w.workorder_id=bl.workorder_id','LEFT');
		$this->db->join(CLIENT_TBL.' AS c', 'c.customer_id=bl.customer_id','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=bl.institute_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=bl.branch_id','LEFT');	
		$this->db->where("bl.challan_id", $challan_id);	
		$this->db->where("bl.workorder_id", $workorder_id);	
		//$this->db->where("bl.status", 1);					
		$this->db->group_by('bl.challan_id');
		$this->db->order_by('bl.challan_id','ASC');			
		$mrow = $this->db->get()->row(); //echo $this->db->last_query();
		$account_name	 = $mrow->customer_name;
					
		if($this->session->userdata('user_role')<=100){$isPrint="";}else{$isPrint="no-print";}
		$headerTop=""; $header1=""; $header2="";
		$body="";$bodyfull=""; $bodytop=""; $bodybtm=""; $sl=0; 
		$FORM_TITLE ="DELIVERY CHALLAN"; 
		//<img class='".$isPrint."' src='".base_url().ASSETS."/img/header.png' style='max-width:100%'/>
		$headerTop="	
		<div class='bg-white text-center p-header hide'>		
		
		</div>	
		<div class='bg-light text-center'>
		<h1 class='txt-black' style='padding-bottom:0px;padding-top:0px;margin:0px'>$FORM_TITLE</h1>
		</div>
		<table width='100%' id='data-table' class='table table-hover table-bordered' style='border: none !important'>
		<thead>
		  <tr>
		  	<td width='100%' colspan='6' class='text-center' style='border: none !important;padding-bottom:1px;'>
			<table style='width: 100% !important; border: none !important'>			
			<tr>
				<td class='text-left' width='12%' style='border: none;font-weight: normal;'>Challan No.</td>
				<td class='text-left' width='50%' style='border: none;font-weight: normal;'>$mrow->challan_no</td>
				<td class='text-right' width='38%' colspan='2' style='border: none;font-weight: normal;'>Challan Date: $mrow->challan_date</td>
			</tr>			
			<tr>
				<td class='text-left' width='12%' style='border: none;font-weight: bold;'>To</td>
				<td class='text-left' width='38%' colspan='3' style='border: none;font-weight: bold;'>$mrow->customer_name</td>
			</tr>			
			<tr>
				<td class='text-left' width='12%' style='border: none;font-weight: normal;'>Workorder No</td>
				<td class='text-left' width='38%' colspan='3' style='border: none;font-weight: normal;'>$mrow->workorder_no</td>
			</tr>			
			<tr>
				<td class='text-left' width='12%' style='border: none;font-weight: normal;'>Workorder Date</td>
				<td class='text-left' width='38%' colspan='3' style='border: none;font-weight: normal;'>$mrow->workorder_date</td>
			</tr>			
			<tr>
				<td class='text-left' width='12%' style='border: none;font-weight: normal;'>OEM</td>
				<td class='text-left' width='38%' colspan='3' style='border: none;font-weight: normal;'>$mrow->oem</td>
			</tr>
			</table>
			</td>			
		  </tr>	
		</thead>
		<tbody>
		<tr>
	  	<td width='100%' colspan='6' class='text-center' style='border: none !important;padding-bottom:1px;'>
		<table style='width: 99% !important;' class='table table-hover table-bordered'>
		  <tr class='bg-primary'>
			<th width='2%' class='text-center'>SL</th>		
			<th width='28%' class='text-left'>Product Description</th>			
			<th width='17%' class='text-left'>Product SKU</th>			
			<th width='12%' class='text-left'>License No</th>			
			<th width='16%' class='text-left'>Validity</th>			
			<th width='17%' class='text-left'>Remarks</th>						
			<th width='8%' class='text-left'>Quantity</th>
		  </tr>
		";		
		$bodyfull.=$headerTop;
						
		$header1=""; $TotalRow=0;
		$billing_month=01;		
		$body.= $header1;
										
		$this->db->select("d.details_id, d.challan_id, d.product_description, d.product_sku, d.validity as license_no, DATE_FORMAT(d.start_date, '%d %M %Y') as start_date, DATE_FORMAT(d.end_date, '%d %M %Y') as end_date, d.quantity, d.total_price, d.remarks", FALSE);
		$this->db->from(CHALLAN_MASTER_TBL.' AS m');	
		$this->db->join(CHALLAN_DETAILS_TBL.' AS d', 'd.challan_id=m.challan_id','LEFT');
		$this->db->where("m.workorder_id", $workorder_id);
		$this->db->where("m.challan_id", $challan_id);
		
		//$this->db->where("m.status", 1);	
					
		$this->db->group_by('d.details_id');
		$this->db->order_by('d.details_id','ASC');			
		$query = $this->db->get(); //echo $this->db->last_query();
		$TotalRow = $query->num_rows();
		$TotalQty=0; $TotalAmount=0; $sl=0; $cunit=""; $unit_price=0; $validity="";
		foreach($query->result() as $row){	 
		$sl++; $TotalQty+=$row->quantity;
		$validity = $row->start_date." to ".$row->end_date;
		$cunit=" pcs";
		 $body.="
		 <tr>
		   <td class='text-center'>".$sl."</td>
		   <td class='text-left'>".$row->product_description."</td>
		   <td class='text-left'>".$row->product_sku."</td>
		   <td class='text-left'>".$row->license_no."</td>
		   <td class='text-left'>".$validity."</td>
		   <td class='text-left'>".$row->remarks."</td>
		   <td class='text-right'>".$row->quantity.$cunit."</td>
		 </tr>";
		}// end foreach									
		
		$body.="
	    	 <tr class='bg-gray'>
			<td colspan='6' class='text-right'><strong>Total Quantity:</strong></td>";
		$body.="
			<td class='text-right'><strong>".number_format($TotalQty, 0, '.', ',')." ".$cunit."</strong></td>
	    	 </tr>";
		$number1 = number_format($TotalQty, 0, '.', ',');

		$inwords = $this->InWords($number1);
		$inwords.= " Pcs Only";
		
		$body.="
	    	 <tr class='bg-light'>
			<td colspan='7' class='text-left'><strong>In Words : ".$inwords."</strong></td>
	    	 </tr>
	    </td>
	    </tr>
	    
		<tr>
			<td class='text-left' colspan='7' width='100%' style='border: none;font-weight: bold;'>End User Details:</td>
		</tr>
		<tr>
		    <td class='text-left' width='100%' colspan='7' style='border: none;font-weight: normal;'>
			<strong>$mrow->customer_name</strong> <br>
			$mrow->customer_address<br>
			Telephone: $mrow->customer_phone <br>
			</td>
		</tr>
		<tr>
			<td class='text-left' colspan='7' width='100%' style='border: none;font-weight: normal;'>Contact Person: $mrow->customer_contactperson</td>
		</tr>
		<tr>
			<td class='text-left' colspan='7' width='100%' style='border: none;font-weight: normal;'>Email &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: $mrow->customer_email</td>
		</tr>
	    ";
	    
		
		if($mrow->shipping_address!="NaN" && $mrow->shipping_address!=""){
		$body.="
	    	<tr class='bg-light'>
			<td colspan='7' class='text-left' style='border: none; padding-top:4px;padding-bottom:2px;'>
			<table width='100%' id='data-table' class='table table-bordered brand-tbl'>
			<tr>
			<td class='text-left' width='18%'><strong>Delivery To :</strong></td>
			<td colspan='7' class='text-left' width='82%'>".nl2br($mrow->shipping_address)."</td>
			</tr>
			</table>
			</td>
	    	 </tr>";
		}else{
		$body.="
	    	<tr class='bg-light'>
			<td colspan='7' class='text-left' style='border: none; padding-top:4px;padding-bottom:2px;'>
			<table width='100%' id='data-table' class='table table-bordered brand-tbl'>
			<tr>
			<td class='text-left' width='18%'><strong>Delivery To :</strong></td>
			<td colspan='7' class='text-left' width='82%'>".nl2br($mrow->customer_address)."</td>
			</tr>
			</table>
			</td>
	    	 </tr>";
		}
		$body.="
		<tr>
	       <td colspan='4' class='text-left' style='padding-top:20px;'>
			On behalf of <strong>".$this->session->userdata('company_name')."</strong><br>
			".nl2br($this->session->userdata('address'))."
	            </td>
		    <td colspan='3' class='text-right' style='padding-top:20px;'>
			On behalf of <strong>".$account_name."</strong>
			<div style='padding-top:25px;padding-bottom:0px'>----------------------------</div>
			Signature & Date 
	            </td>
	        </tr>
		</tbody>		
		<tfoot>
	        <tr>
	            <td id='footer' colspan='7' class='text-center'>&nbsp;</td>
	        </tr>
	    	</tfoot>				
		</table>
		<div id='p-footer'>
		
		</div>
		";
		//<img class='".$isPrint." hide' src='".base_url().ASSETS."/img/footer.png' style='max-width:100%;'/>
		$bodyfull.=$body;		

		echo $bodyfull;
	}
	//======= Start Approval Sheet =======
	
	function PrintReqTopSheet(){
		$po_id	        = $this->input->post('po-id');
		$workorder_id	= $this->input->post('workorder-id');
		$distributor_id	= $this->input->post('distributor-id');
		
		$this->db->select('w.*,c.customer_full_name as customer_name,c.address as customer_address,c.shipping_address,c.contact_person as customer_contactperson,c.designation as customer_designation,c.phone as customer_phone,c.fax as customer_fax,c.mobile as customer_mobile,c.email as customer_email,DATE_FORMAT(w.workorder_date ,"%d %b %Y") as workorder_date',FALSE);
		$this->db->from(WORKORDER_MASTER_TBL." AS w");
		$this->db->join(CLIENT_TBL.' AS c', 'c.customer_id=w.customer_id','LEFT');
		$this->db->where("w.workorder_id", $workorder_id);	
		$this->db->where("w.status", 1);				
		$wrow = $this->db->get()->row(); //echo $this->db->last_query();
		$customer_name	 = $wrow->customer_name;
		
		
		$this->db->select('bl.*,d.distributor_full_name as distributor_name,d.address as distri_address,d.contact_person as distri_contactperson,d.designation as distri_designation,d.phone as distri_phone,d.fax as distri_fax,d.mobile as distri_mobile,d.email as distri_email,im.importer_full_name as importer_name,im.address as importer_address,im.contact_person as importer_contactperson,im.designation as importer_designation,im.phone as importer_phone,im.fax as importer_fax,im.mobile as importer_mobile,im.email as importer_email, c.customer_full_name as customer_name,c.address as customer_address,c.contact_person as customer_contactperson,c.designation as customer_designation,c.phone as customer_phone,c.fax as customer_fax,c.mobile as customer_mobile,c.email as customer_email,i.company_name,i.address as company_address,i.phone as company_phone,i.mobile as company_mobile,i.email as company_email,DATE_FORMAT(bl.po_date ,"%d %b %Y") as po_date',FALSE);
		$this->db->from(DISTRIPO_MASTER_TBL." AS bl");
		$this->db->join(DISTRIBUTOR_TBL.' AS d', 'd.distributor_id=bl.distributor_id','LEFT');
		$this->db->join(CLIENT_TBL.' AS c', 'c.customer_id=bl.customer_id','LEFT');
		$this->db->join(IMPORTER_TBL.' AS im', 'im.importer_id=bl.importer_id','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=bl.institute_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=bl.branch_id','LEFT');	
		$this->db->where("bl.po_id", $po_id);	
		$this->db->where("bl.distributor_id", $distributor_id);	
		$this->db->where("bl.status", 1);					
		$this->db->group_by('bl.po_id');
		$this->db->order_by('bl.po_id','ASC');			
		$mrow = $this->db->get()->row(); //echo $this->db->last_query();
		$po_date  = $mrow->po_date;						
		$account_name	 = $mrow->distributor_name;
		if($mrow->payment_mode==1){ $payment_mode="Cash";}elseif($mrow->payment_mode==2){ $payment_mode="Cheque";}elseif($mrow->payment_mode==3){ $payment_mode="bkash";}elseif($mrow->payment_mode==4){ $payment_mode="TT";}else{$payment_mode="Others";}				
		if($this->session->userdata('user_role')<=100){$isPrint="";}else{$isPrint="no-print";}
		$headerTop=""; $header1=""; $header2="";
		$body="";$bodyfull=""; $bodytop=""; $bodybtm=""; $sl=0; 
		$FORM_TITLE ="Payments Requisation (Approval Sheet)"; 
		//<img class='".$isPrint."' src='".base_url().ASSETS."/img/header.png' style='max-width:100%'/>
		$headerTop="	
		<div class='bg-white text-center p-header hide'>		
		
		</div>	
		<div class='bg-light text-center'>
		<h1 class='txt-black' style='padding-bottom:0px;padding-top:0px;margin:0px'>$FORM_TITLE</h1>
		</div>";
		$headerTop.="
		 <br>
		 1. Workorder:
		 <table width='100%' id='data-table' class='table table-bordered brand-tbl'>
			<tr>
			<td class='text-left' width='17%'><strong>Workorder No.</strong></td>
			<td class='text-left' width='14%'><strong>Workorder Date</strong></td>
			<td class='text-left' width='33%'><strong>End Customer</strong></td>
			<td class='text-left' width='26%'><strong>Workorder Amount (BDT)</strong></td>
			</tr>
		    <tr>
			<td class='text-left' width='17%'>$wrow->workorder_no</td>
			<td class='text-left' width='14%'>$wrow->workorder_date</td>
			<td class='text-left' width='33%'>$wrow->customer_name</td>
			<td class='text-left' width='26%'>".number_format($wrow->net_bill_amount, 2, '.', ',')."</td>
			</tr>
		  </table>";
		$headerTop.="
		 <br>
		 2. Purchase Order (PO):
		 <table width='100%' id='data-table' class='table table-bordered brand-tbl'>
			<tr>
			<td class='text-left' width='17%'><strong>Customer PO No.</strong></td>
			<td class='text-left' width='14%'><strong>PO Date</strong></td>
			<td class='text-left' width='33%'><strong>PO Amount</strong></td>
			<td class='text-left' width='14%'><strong>VAT %</strong></td>
			<td class='text-left' width='12%'><strong>AIT %</strong></td>
			</tr>
		    <tr>
			<td class='text-left' width='17%'>$mrow->po_no</td>
			<td class='text-left' width='14%'>$mrow->po_date</td>
			<td class='text-left' width='33%'>".number_format($wrow->net_bill_amount, 2, '.', ',')."</td>
			<td class='text-left' width='14%'>".number_format($wrow->vat_percentage, 2, '.', ',')."</td>
			<td class='text-left' width='12%'>".number_format($wrow->ait_percentage, 2, '.', ',')."</td>
			</tr>
		  </table>
		  <br>";
		$TotalValues = 0;
		$TotalValues = $mrow->net_bill_amount+$wrow->midman_commission;
		$headerTop.="
		 <br>
		 3. Paid To:
		 <table width='100%' id='data-table' class='table table-bordered brand-tbl'>
			<tr>
			<td class='text-left' width='17%' rowspan='2'><strong>Local Importer Name</strong></td>
			<td class='text-left' width='14%' rowspan='2'><strong>OEM</strong></td>
			<td class='text-left' width='33%' rowspan='2'><strong>Distributor</strong></td>
			<td class='text-center' width='14%' colspan='2'><strong>Value USD</strong></td>
			<td class='text-left' width='12%' rowspan='2'><strong>Total Value</strong></td>
			</tr>
			<tr>
			<td class='text-left' width='7%'><strong>Product</strong></td>
			<td class='text-left' width='7%'><strong>Charge</strong></td>
			</tr>
		    <tr>
			<td class='text-left' width='17%'>$mrow->importer_name</td>
			<td class='text-left' width='14%'>$wrow->oem</td>
			<td class='text-left' width='33%'>$mrow->distributor_name</td>
			<td class='text-left' width='7%'>".number_format($mrow->net_bill_amount, 2, '.', ',')."</td>
			<td class='text-left' width='7%'>".number_format($wrow->midman_commission, 2, '.', ',')."</td>
			<td class='text-left' width='12%'>".number_format($TotalValues, 2, '.', ',')."</td>
			</tr>
		  </table>
		  <br>";
		$headerTop.="
		<table style='width: 100% !important;' class='table table-hover table-bordered'>
		  <tr class='bg-primary'>
			<th width='2%' class='text-center'>SL</th>		
			<th width='33%' class='text-left'>Product Description</th>			
			<th width='13%' class='text-left'>Product SKU</th>			
			<th width='12%' class='text-left'>Remarks</th>			
			<th width='10%' class='text-left'>Validity</th>						
			<th width='8%' class='text-left'>Quantity</th>			
			<th width='10%' class='text-right'>Unit Price</th>
			<th width='12%' class='text-right'>Total Price</th>
		  </tr>
		";		
		$bodyfull.=$headerTop;
						
		$header1="";
		$billing_month=01;		
		$body.= $header1;
										
		$this->db->select('d.*', FALSE);
		$this->db->from(DISTRIPO_MASTER_TBL.' AS m');	
		$this->db->join(DISTRIPO_DETAILS_TBL.' AS d', 'd.po_id=m.po_id','LEFT');
		$this->db->where("m.distributor_id", $distributor_id);
		$this->db->where("m.po_id", $po_id);
		$this->db->where("m.status", 1);	
					
		$this->db->group_by('d.details_id');
		$this->db->order_by('d.details_id','ASC');			
		$query = $this->db->get(); //echo $this->db->last_query();
		$TotalQty=0; $TotalAmount=0; $sl=0; $cunit=""; $unit_price=0;
		foreach($query->result() as $row){	 
		$sl++; $TotalQty+=$row->quantity; $TotalAmount+=$row->total_price;
		$unit_price = $row->unit_price;
		$cunit=" pcs";
		$body.="
		<tr>
		   <td class='text-center'>".$sl."</td>
		   <td class='text-left'>".$row->product_description."</td>
		   <td class='text-left'>".$row->product_sku."</td>
		   <td class='text-left'>".$row->remarks."</td>
		   <td class='text-left'>".$row->validity."</td>
		   <td class='text-left'>".$row->quantity.$cunit."</td>
		   <td class='text-right'>".number_format($unit_price, 2, '.', ',')."</td>
		   <td class='text-right'>".number_format($row->total_price, 2, '.', ',')."</td>

		</tr>";
		}// end foreach									
		
		$net_bill_amount = number_format($TotalValues, 2, '.', '');
				
		$inwords 	="";
		$numberArr 	= explode(".",$net_bill_amount);
		$number1 	= $numberArr[0];
		$number2 	= $numberArr[1];

		$inwords = $this->InWords($number1);
		if($number2 >0){
		    if($mrow->currency_id > 1){
		       $inwords2= $this->InWords($number2);
		       $inwords.= " ".$mrow->currency." ".$inwords2." Cent "; 
		    }else{
		       $inwords2= $this->InWords($number2);
		       $inwords.= " Taka ".$inwords2." Paisa ";
		    }
		}else{ 
		    if($mrow->currency_id > 1){
		        $inwords.=" ".$mrow->currency." ";
		    }else{
		       $inwords.=" Taka ";
		    }
		    
		} 
		$inwords.= " Only";

		$body.="
	    	 <tr class='bg-light'>
			<td colspan='8' class='text-left'><strong>In Words : ".$inwords."</strong></td>
	    	 </tr>
	    </td>
	    </tr>
	    </table>
	    ";
		$body.="<br>
		 4. Bank Information:
			<table width='100%' id='data-table' class='table table-bordered brand-tbl'>
			<tr>
			<td class='text-left' width='20%'><strong>Bank Name</strong></td>
			<td class='text-left' width='20%'><strong>Cheque Number</strong></td>
			<td class='text-left' width='20%'><strong>Cheque Date</strong></td>
			<td class='text-left' width='20%'><strong>Cheque Amount</strong></td>
			<td class='text-left' width='20%'><strong>Remarks</strong></td>
			</tr>
			<tr>
			<td class='text-left' width='20%'>&nbsp;</td>
			<td class='text-left' width='20%'>&nbsp;</td>
			<td class='text-left' width='20%'>&nbsp;</td>
			<td class='text-left' width='20%'>&nbsp;</td>
			<td class='text-left' width='20%'>&nbsp;</td>
			</tr>
			<tr>
			<td class='text-left' width='20%'>&nbsp;</td>
			<td class='text-left' width='20%'>&nbsp;</td>
			<td class='text-left' width='20%'>&nbsp;</td>
			<td class='text-left' width='20%'>&nbsp;</td>
			<td class='text-left' width='20%'>&nbsp;</td>
			</tr>
			<tr>
			<td class='text-left' width='20%'>&nbsp;</td>
			<td class='text-left' width='20%'>&nbsp;</td>
			<td class='text-left' width='20%'>&nbsp;</td>
			<td class='text-left' width='20%'>&nbsp;</td>
			<td class='text-left' width='20%'>&nbsp;</td>
			</tr>
			</table>";
	
		//".nl2br($this->session->userdata('address'))."
		$body.="
		<table width='100%' id='data-table' class='table table-bordered brand-tbl'>
		<tr>
	       <td colspan='3' class='text-left' style='padding-top:20px;'>
			<strong>Prepared By</strong><br>
			<div style='padding-top:25px;padding-bottom:0px'>----------------------------</div>
			Signature & Date
	        </td>
		    <td colspan='3' class='text-right' style='padding-top:20px;'>
			<strong>Checked By</strong>
			<div style='padding-top:25px;padding-bottom:0px'>----------------------------</div>
			Signature & Date 
	        </td>
		    <td colspan='3' class='text-right' style='padding-top:20px;'>
			<strong>Approved By</strong>
			<div style='padding-top:25px;padding-bottom:0px'>----------------------------</div>
			Signature & Date 
	        </td>
	        </tr>
			
	        <tr>
	            <td id='footer' colspan='8' class='text-center'>&nbsp;</td>
	        </tr>				
		</table>
		<div id='p-footer'>
		
		</div>
		";
		//<img class='".$isPrint." hide' src='".base_url().ASSETS."/img/footer.png' style='max-width:100%;'/>
		$bodyfull.=$body;		

		echo $bodyfull;
	}
	function NewLineBr($str){
	    $lines = "";
	    $strArr = explode("\r\n", $str); 
		foreach($strArr as $p1val){ $lines.=$p1val."<br />"; }
		return $lines;
	}
	//====== End NHQ ======
	
	
	function GetApplicationPDFGride($pdf_mode=0){
		$filepath=""; $size=20; $orientation = "horizontal"; $code_type = "code128";
        $print = false; $sizefactor = "1"; $barcode ="";
		$application_id	= $this->input->post('application-id');	
		
		$barcode =base_url()."/barcode.php?text=".$application_id;
		//$barcode = $this->barcode($filepath, $application_id, $size, $orientation, $code_type, $print, $sizefactor );
		$this->db->select('a.*,DATE_FORMAT(a.created_time ,"%d-%M-%Y") as date_of_application,DATE_FORMAT(a.interview_date ,"%d-%M-%Y") as date_of_interview,et.exam_time,a.student_name_en as account_name,a.student_id as account_id,a.student_name_bn as bangla_name,i.company_name,i.address as ins_address, i.phone as ins_phone, i.email as ins_email,b.branch_name,b.branch_code,g.group_name,se.session_name,v.version_name,c.class_name,sf.shift_name',FALSE);
		$this->db->from(APPLICATION_TBL." AS a");
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.institute_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=a.branch_id','LEFT');
	  	$this->db->join(GROUPS_TBL.' AS g', 'g.group_id=a.group_id','LEFT');
	  	$this->db->join(VERSION_TBL.' AS v', 'v.version_id=a.version_id','LEFT');
	  	$this->db->join(SESSION_TBL.' AS se', 'se.sessions_id=a.session_id','LEFT');
	  	$this->db->join(CLASS_TBL.' AS c', 'c.class_id=a.class_id','LEFT');
	  	$this->db->join(EXAM_TIMES_TBL.' AS et', 'et.examtime_id=a.interview_time','LEFT');	
	  	$this->db->join(SHIFT_TBL.' AS sf', 'sf.institute_id=a.institute_id','LEFT');	
		$this->db->where("a.application_no", $application_id);
		$this->db->where("a.status<", 4);			
		$this->db->group_by('a.application_id');
		$this->db->order_by('a.application_id','ASC');			
		$mrow = $this->db->get()->row(); //echo $this->db->last_query();
		$interview_date    = $mrow->interview_date;
		$date_of_interview = $mrow->date_of_interview;
		
		$institute_id  	   = $mrow->institute_id;
		$branch_id  	   = $mrow->branch_id;
		
		$interviewDateRange= "Date of Interview: ".$date_of_interview.", Time: ".$mrow->exam_time;
							
		$account_name	  = $mrow->account_name;
		$present_address  = $mrow->present_address;
		$permanent_address= $mrow->permanent_address;
		$salesType 		  = "Student";
		$company_name     = $mrow->company_name;		
		if($this->session->userdata('user_role')<=100){$isPrint="";}else{$isPrint="no-print";}
		$headerTop=""; $header1=""; $header2="";
		$body="";$bodyfull=""; $bodytop=""; $bodybtm=""; $sl=0; 
		if($mrow->admission_type==2){
		$FORM_TITLE ="RE-ADMISSION APPLICATION"; $AdmissionType="Re-admission";
		}else{
		$FORM_TITLE ="NEW ADMISSION APPLICATION"; $AdmissionType="New Admission";
		}
		if($mrow->is_minority==1){$Minority="Yes";}else{$Minority="No";}
		if($mrow->is_handicapped==1){$Handicapped="Yes";}else{$Handicapped="No";}
		if($mrow->is_freedom_fighter==1){$FreedomFighter="Yes";}else{$FreedomFighter="No";}
		$Top="
		<style type='text/css'>
			body {
			font-family: 'Source Sans Pro','Helvetica Neue',Helvetica,Arial,sans-serif;
			font-weight: normal;
			color: #666666;
			font-size: 12px;
			line-height: 18px;
			text-rendering: optimizelegibility;
			}
			*::before, *::after {
			box-sizing: border-box;
			}
			.table-bordered {
				border: 1px solid #f4f4f4;
			}
			.table {
				margin-bottom: 0;
				width: 100%;
			}
			.custab {
				box-shadow: 3px 3px 2px #ccc;
				padding: 0;
				transition: all 0.5s ease 0s;
			}
			table {					
				border-collapse: collapse;
				border-spacing: 0;
				background-color: transparent;
				max-width: 100%;
			}
			.bg-light {
			  background-color: #f8f9fa !important;
			}
			.bg-dark {
			  background-color: #343a40 !important; color:#fff !important;
			   height:32px !important; padding: 7px;
			}

			.bg-primary {
				background-color: #00c0ef !important;
				color: #fff;
			}
			.bg-success {
				background-color: #dff0d8;
			}
			.bg-info {
				background-color: #d9edf7;
			}
			.bg-warning {
				background-color: #fcf8e3;
			}
			.bg-steel{
			 background-color: #dbdbc9 !important; color:#000 !important; height:32px !important; padding: 7px;
			}
			.table > caption + thead > tr:first-child > th, .table > colgroup + thead > tr:first-child > th, .table > thead:first-child > tr:first-child > th, .table > caption + thead > tr:first-child > td, .table > colgroup + thead > tr:first-child > td, .table > thead:first-child > tr:first-child > td {
				border-top: 0 none;

			}
			.table-bordered > thead > tr > th, .table-bordered > thead > tr > td {
				border-bottom-width: 2px; line-height: 1.7;
			}
			.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
				line-height: 1.7; padding-top: 7px !important; padding-bottom: 7px !important;
				vertical-align: top;
			}
			th {
				text-align: left;
				line-height: 1.9; padding-top: 7px !important; padding-bottom: 7px !important;
			}
			.table > thead > tr > td.info, .table > tbody > tr > td.info, .table > tfoot > tr > td.info, .table > thead > tr > th.info, .table > tbody > tr > th.info, .table > tfoot > tr > th.info, .table > thead > tr.info > td, .table > tbody > tr.info > td, .table > tfoot > tr.info > td, .table > thead > tr.info > th, .table > tbody > tr.info > th, .table > tfoot > tr.info > th {
				background-color: #d9edf7; line-height: 1.9; padding-top: 7px !important; padding-bottom: 7px !important;
			}
			.table-bordered > thead > tr > th, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > tbody > tr > td, .table-bordered > tfoot > tr > td {
				border: 1px solid #f4f4f4; line-height: 1.9;
				width: auto !important; padding-top: 7px !important; padding-bottom: 7px !important;
			}
			.table thead > tr > td, .table tbody > tr > td {
				vertical-align: middle; line-height: 1.9; padding-top: 7px !important; padding-bottom: 7px !important;
			}
			.text-center {
				text-align: center;
			}
			.table>tbody>tr.active>td, .table>tbody>tr>td.active, .active {
				background-color:#f5f5f5 !important; line-height: 1.9; height: 30px; font-size: 12px !important; padding-top: 7px !important; padding-bottom: 7px !important;
			}
			.table>tbody>tr.info>td, .table>tbody>tr>td.info, .info {
				background-color:#d9edf7 !important; height: 30px; line-height: 1.9; font-size: 12px !important; padding-top: 7px !important; padding-bottom: 7px !important;
			}
			.text-white{color:#fff; border-bottom: 1px solid #f4f4f4 !important;}
			.b-left{
				border-left: 1px solid #f4f4f4 !important;
				border-top: 1px solid #f4f4f4 !important;
				border-right: 1px solid #f4f4f4 !important;
			}
			.b-right{
				border-right: 1px solid #f4f4f4 !important;
				border-top: 1px solid #f4f4f4 !important;
			}
			h1, h2, h3 {
				color: #049924;
				clear:both
			}
			h1{
			  font-size: 23px !important;
			  vertical-align: middle !important;
			}
			h2{
			  font-size: 18px !important;
			}
			h3{
			  font-size: 15px !important;
			}
			h4{
			  font-size: 12px !important;
			}
			.text-left {
			  text-align: left !important;
			}
			.text-right {
			  text-align: right !important;
			}

			.text-center {
			  text-align: center !important;
			}
			.txt-black {
			    color: #000 !important;
				height: 30px; line-height: 1.9; font-size: 12px !important; padding-top: 7px !important; padding-bottom: 7px !important;
				background: none !important;
			}
			.txt-pkg {
				color: #fff !important; height: 30px; line-height: 1.9; font-size: 12px !important; padding-top: 7px !important; padding-bottom: 7px !important;
			}
			.barcodesl{font-size: 8px;font-weight: bold;}
			.date{height:35px !important;}
			.top-header{height:30px !important;}
			.header{height:25px !important;}
			.logo{padding:4px;}
			.package-header{page-break-before:always;border: 1px solid #f4f4f4;}
				
		</style>
		<table width='100%' class='table table-hover table-bordered'>
		<tbody>
		  <tr>
		  	<td width='20%' class='text-left' style='background-color: #fff;border-right:0px !important'>
				<img class='logo' src='".base_url()."/assets/img/company/md-1.png' height='75' />
			</td>
			<td class='text-center no-border' width='80%' style='background-color: #fff;border-right:0px !important'>
			    <h1 class='text-center'>".$company_name."</h1>
				".$mrow->ins_address."<br>Phone: ".$mrow->ins_phone."<br>Email: ".$mrow->ins_email."<br><br>
			</td>
		  </tr>
		  <tr>
		  	<td width='100%' colspan='2' class='text-left' style='background-color: #fff;border-right:0px !important'>
				&nbsp;
			</td>
		  </tr>
		</tbody>
		</table>";
		if($pdf_mode==1){$headerTop.=$Top;}
		
		$headerTop.="
		<div class='bg-light text-center'>
		<h2 class='txt-black' style='padding-bottom:2px;padding-top:2px;'>$FORM_TITLE</h2>
		</div>

		<table width='100%' id='data-table' class='table table-responsive table-hover table-bordered'>
		<thead class='bg-light'>
		  <tr class='bg-info'>
		  	<th width='100%' colspan='8' class='text-center' style='padding-top:2px; padding-bottom:2px;'>
			<h3 class='txt-black'>".$interviewDateRange."</h3>
			</th>
		  </tr>
		</thead>
		<tbody>";
		
		$bodyfull.=$headerTop;
		$body.="
		  <tr class='bg-light'>
		  	<th width='75%' colspan='7' class='text-center' style='border: none !important;padding-bottom:1px;'>
			<table style='width: 81% !important;' class='table table-bordered brand-tbl'>
			<tr>
			<td class='text-left' width='18%'>Application No.</td>
			<td class='text-left' width='32%'>$mrow->application_no</td>
			<td class='text-left' width='20%'>Application Date</td>
			<td class='text-left' width='30%'>$mrow->date_of_application</td>
			</tr>
			<tr>
			<td class='text-left' width='18%'>Student Name</td>
			<td class='text-left' width='32%'>$mrow->student_name_en</td>
			<td class='text-left' width='20%'>Date of Birth</td>
			<td class='text-left' width='30%'>$mrow->dob</td>
			</tr>
			<tr>
			<td class='text-left' width='18%'>Gender</td>
			<td class='text-left' width='32%'>$mrow->gender</td>
			<td class='text-left' width='20%'>Blood Group</td>
			<td class='text-left' width='30%'>$mrow->blood_group</td>
			</tr>
			<tr>
			<td class='text-left' width='18%'>Nationality</td>
			<td class='text-left' width='32%'>$mrow->nationality</td>
			<td class='text-left' width='20%'>Religion</td>
			<td class='text-left' width='30%'>$mrow->religion</td>
			</tr>
			<tr>
			<td class='text-left' width='18%'>SMS Mobile No.</td>
			<td class='text-left' width='32%'>$mrow->mobile</td>
			<td class='text-left' width='20%'>Interview Mode</td>
			<td class='text-left' width='30%'>$mrow->interview_mode</td>
			</tr>
			</table>
			</th>
			
		  	<th width='15%' class='text-center' style='border: none !important;padding-bottom:2px'>
			<table width='100%' id='data-table' class='table table-bordered brand-tbl'>			
			<tr>
			<td class='text-center' width='100%'>
			<img class='img-responsive' src='".base_url().ASSETS."/".IMG."/photo/$mrow->student_photo' style='width: 50px; height: 60px;'/>
			</td>
			</tr>
			<tr>
			<td class='text-left' width='100%'>
			<img src='".$barcode."' style='margin-top: 4px;width: 100px;height: 20px;'/><div style='height:2px;'></div>
			<div class='text-center' class='barcodesl'>$application_id</div>
			</td>
			</tr>
			</table>
			</th>			
		  </tr>
		  
		  <tr class='bg-info'>
		  	<th width='100%' colspan='8' class='text-left' style='padding-top:4px; padding-bottom:1px;'>
			<h4>Contact Information</h4>
			</th>
		  </tr>
		  <tr class='bg-light'>
		  	<th width='100%' colspan='8' class='text-center' style='border: none !important;padding-top:1px; padding-bottom:1px;'>
			<table style='width: 100% !important;' class='table table-bordered brand-tbl'>			
			<tr>
			<td class='text-left' width='20%'>Mailing Address</td>
			<td class='text-left' colspan='3' width='80%'>$mrow->present_address</td>
			</tr>
			<tr>
			<td class='text-left' width='20%'>Home District</td>
			<td class='text-left' colspan='3' width='80%'>$mrow->permanent_address</td>
			</tr>
			<tr>
			<td class='text-left' width='20%'>Phone</td>
			<td class='text-left' width='40%'>$mrow->phone</td>
			<td class='text-left' width='15%'>Email</td>
			<td class='text-left' width='45%'>$mrow->email</td>
			</tr>
			</table>
			</th>
		  </tr>
		  <tr class='bg-info'>
		  	<th width='100%' colspan='8' class='text-left' style='padding-top:4px; padding-bottom:1px;'>
			<h4>Academic Information</h4>
			</th>
		  </tr>
		  <tr class='bg-light'>
		  	<th width='100%' colspan='8' class='text-center' style='padding-top:4px; padding-bottom:1px;'>
			<table style='width: 100% !important;' class='table table-bordered brand-tbl'>			
			<tr>
			<td class='text-left' width='20%'>Institute Name</td>
			<td class='text-left' colspan='5' width='80%'>$mrow->company_name</td>
			</tr>
			<tr>
			<td class='text-left' width='20%'>Session Name</td>
			<td class='text-left' width='20%'>$mrow->session_name</td>
			<td class='text-left' width='16%'>Version Name</td>
			<td class='text-left' width='23%'>$mrow->version_name</td>
			<td class='text-left' width='15%'>Class Name</td>
			<td class='text-left' width='17%'>$mrow->class_name</td>
			</tr>
			<tr>
			<td class='text-left' width='20%'>Group Name</td>
			<td class='text-left' width='20%'>$mrow->group_name</td>
			<td class='text-left' width='16%'>Shift Name</td>
			<td class='text-left' width='23%'>$mrow->shift_name</td>
			<td class='text-left' width='15%'>Student Type</td>
			<td class='text-left' width='17%'>$mrow->std_category</td>
			</tr>
			</table>
			</th>
		  </tr>
		  
		  <tr class='bg-info'>
		  	<th width='100%' colspan='8' class='text-left' style='padding-top:4px; padding-bottom:1px;'>
			<h4>Father's Information</h4>
			</th>
		  </tr>
		  <tr class='bg-light'>
		  	<th width='100%' colspan='8' class='text-center' style='padding-top:4px; padding-bottom:1px;'>
			<table style='width: 100% !important;' class='table table-bordered brand-tbl'>			
			<tr>
			<td class='text-left' width='16%'>Father's Name</td>
			<td class='text-left' width='22%'>$mrow->fathers_name</td>
			<td class='text-left' width='16%'>Address</td>
			<td class='text-left' colspan='3' width='46%'>$mrow->fathers_address</td>
			</tr>
			<tr>
			<td class='text-left' width='16%'>Monthly Income</td>
			<td class='text-left' width='22%'>$mrow->f_income</td>
			<td class='text-left' width='16%'>Mobile</td>
			<td class='text-left' colspan='3' width='46%'>$mrow->f_mobile</td>
			</tr>
			</table>
			</th>
		  </tr>
		  
		  <tr class='bg-info'>
		  	<th width='100%' colspan='8' class='text-left' style='padding-top:4px; padding-bottom:1px;'>
			<h4>Mother's Information</h4>
			</th>
		  </tr>
		  <tr class='bg-light'>
		  	<th width='100%' colspan='8' class='text-center' style='padding-top:4px; padding-bottom:1px;'>
			<table style='width: 100% !important;' class='table table-bordered brand-tbl'>			
			<tr>
			<td class='text-left' width='16%'>Mother's Name</td>
			<td class='text-left' width='22%'>$mrow->mothers_name</td>
			<td class='text-left' width='16%'>Address</td>
			<td class='text-left' colspan='3' width='46%'>$mrow->mothers_address</td>
			</tr>
			<tr>
			<td class='text-left' width='16%'>Monthly Income</td>
			<td class='text-left' width='22%'>$mrow->m_income</td>
			<td class='text-left' width='16%'>Mobile</td>
			<td class='text-left' colspan='3' width='46%'>$mrow->m_mobile</td>
			</tr>
			</table>
			</th>
		  </tr>
		  
		  <tr class='bg-info'>
		  	<th width='100%' colspan='8' class='text-left' style='padding-top:4px; padding-bottom:1px;'>
			<h4>Guardian's Information</h4>
			</th>
		  </tr>
		  <tr class='bg-light'>
		  	<th width='100%' colspan='8' class='text-center' style='padding-top:4px; padding-bottom:1px;'>
			<table style='width: 100% !important;' class='table table-bordered brand-tbl'>			
			<tr>
			<td class='text-left' width='16%'>Guardian's Name</td>
			<td class='text-left' width='22%'>$mrow->guardian_name</td>
			<td class='text-left' width='16%'>Address</td>
			<td class='text-left' colspan='3' width='46%'>$mrow->guardian_address</td>
			</tr>
			<tr>
			<td class='text-left' width='16%'>Mobile</td>
			<td class='text-left' width='22%'>$mrow->g_mobile</td>
			<td class='text-left' width='16%'>Email</td>
			<td class='text-left' colspan='3' width='46%'>$mrow->g_email</td>
			</tr>
			</table>
			</th>
		  </tr>
		  
		  <tr class='bg-info'>
			<th width='6%' class='text-center'>SL</th>		
			<th width='40%' colspan='4'>Particulars</th>			
			<th width='18%'>Quantity</th>			
			<th width='18%'>Unit Fee</th>
			<th width='18%' class='text-right'>Total Fee</th>
		  </tr>	
		";		
		$quantity="01"; $cunit=" pcs";
		$unit_price = $mrow->net_amount;			
		$TotalAmount=0; $sl="01";		
		
		$body.="
		<tr>
		   <td class='text-center'>".$sl."</td>
		   <td class='text-left' colspan='4'>Admission Process/Admission Form Charge</td>
		   <td class='text-left'>".$quantity.$cunit."</td>
		   <td class='text-left'>".number_format(($unit_price/$quantity), 2, '.', ',')."</td>
		   <td class='text-right'>".number_format($mrow->net_amount, 2, '.', ',')."</td>
		</tr>";
		$TotalAmount+=$mrow->net_amount; $sl="02";
		$body.="
		<tr>
		   <td class='text-center'>".$sl."</td>
		   <td class='text-left' colspan='4'>Online Transaction Process Charge</td>
		   <td class='text-left'>".$quantity.$cunit."</td>
		   <td class='text-left'>".number_format($mrow->payment_charge, 2, '.', ',')."</td>
		   <td class='text-right'>".number_format($mrow->payment_charge, 2, '.', ',')."</td>
		</tr>";
		$TotalAmount+=$mrow->payment_charge;
		$body.="
	    	<tr class='bg-dark'>
			<td colspan='7' class='text-right txt-pkg'><h4>Total Payable Amount:</h4></td>";
		$body.="
			<td class='text-right txt-pkg'><h4>".number_format($TotalAmount, 2, '.', ',')."<h4></td>
	    	 </tr>";
		
		$body.="
	    	<tr class='bg-steel'>
			<td colspan='7' class='text-right txt-black'><h4>Total Paid Amount:</h4></td>
			<td class='text-right txt-black'><h4>".number_format($mrow->paid_amount, 2, '.', ',')."<h4></td>
	    	</tr>";
		
		$body.="
	    	<tr class='bg-dark'>
			<td colspan='7' class='text-right txt-pkg'><h4>Total Due Amount:</h4></td>
			<td class='text-right txt-pkg'><h4>".number_format(($TotalAmount - $mrow->paid_amount), 2, '.', ',')."<h4></td>
	    	</tr>";
	    $TotalAmount= number_format($TotalAmount, 2, '.', ',');		
		$inwords 	= "";
		$numberArr 	= explode(".",$TotalAmount);
		$number1 	= $numberArr[0];
		$number2 	= $numberArr[1];

		$inwords = $this->InWords($number1);
		if($number2 >0){
		$inwords2= $this->InWords($number2);
		$inwords.= " Taka ".$inwords2." paisa ";
		}else{ $inwords.=" Taka ";} 
		$inwords.= " Only";

		$body.="
	    	 <tr class='bg-light'>
			 <td colspan='8' class='text-left txt-black'><h4>Payable Amount In Words : ".$inwords."<h4></td>
	    	 </tr>";
		
		$body.="
		<tr>
	       <td colspan='5' class='text-left' style='padding-top:24px;'>
			On behalf of <strong>".$company_name."</strong><br>
			
	            </td>
		    <td colspan='3' class='text-right' style='padding-top:24px;'>
			On behalf of <strong>".$account_name."</strong>
			<div style='padding-top:25px;padding-bottom:0px'>----------------------------</div>
			Signature & Date 
	            </td>
	        </tr>
		</tbody>		
		<tfoot>
	        <tr>
	            <td id='footer' colspan='8' class='text-center'>&nbsp;</td>
	        </tr>
	    	</tfoot>				
		</table>
		";
		$bodyfull.=$body;		

		return $bodyfull;
	}
	
	//====== Start Salary Bill Print ========
	
	function PrintMonthlySalaryBill(){
		$bill_id		= $this->input->post('bill-id');
		$employee_id	= $this->input->post('employee-id');		
		$this->db->select('bl.*,sf.shift_name,a.employee_code,a.address as present_address,a.permanent_address,a.designation,a.fathers_name,a.mothers_name,p.phone,p.mobile,p.email,r.period_name_en as period_name,r.period_year,p.account_name,p.account_id,p.head_id,p.bangla_name,i.company_name,b.branch_name,b.branch_code,se.session_name,v.version_name,DATE_FORMAT(a.joining_date ,"%d-%m-%Y") as joining_date,DATE_FORMAT(bl.billing_date ,"%d-%m-%Y") as bill_date,bl.bill_no,bl.bill_amount,bl.discount_amount as deduction_amount,bl.net_bill_amount,bl.invoice_note1,bl.invoice_note2',FALSE);
		$this->db->from(BILL_MASTER_TBL." AS bl");
		$this->db->where("bl.bill_type", 3); 
		$this->db->join(EMPLOYEE_TBL.' AS a', 'a.employee_id=bl.account_id','LEFT');
		$this->db->join(PERIOD_TBL.' AS r', 'r.period_no=bl.billing_month','LEFT');
		$this->db->join(ACC_HEAD_TBL.' AS p', 'p.account_id=a.employee_id','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.company_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=a.branch_id','LEFT');
	  	$this->db->join(VERSION_TBL.' AS v', 'v.version_id=bl.version_id','LEFT');
	  	$this->db->join(SESSION_TBL.' AS se', 'se.sessions_id=bl.session_id','LEFT');
	  	$this->db->join(SHIFT_TBL.' AS sf', 'sf.shift_id=bl.shift_id','LEFT');
			
		$this->db->where("bl.bill_id", $bill_id);	
		$this->db->where("a.employee_id", $employee_id);					
		$this->db->group_by('bl.bill_id');
		$this->db->order_by('bl.bill_id','ASC');			
		$mrow = $this->db->get()->row(); //echo $this->db->last_query();
		$employee_code  = $mrow->head_id;
		$joining_date   = $mrow->joining_date;
		$DateRange	    = "Month of ".$mrow->period_name;
							
		$account_name	  = $mrow->account_name;
		$present_address  = $mrow->present_address;
		$permanent_address= $mrow->permanent_address;
		$salesType 		  = "Employee";
						
		if($this->session->userdata('user_role')<=100){$isPrint="";}else{$isPrint="no-print";}
		$headerTop=""; $header1=""; $header2="";
		$body="";$bodyfull=""; $bodytop=""; $bodybtm=""; $sl=0; 
		$FORM_TITLE ="Monthly Employee Salary"; $AdmissionType;
		$headerTop="	
		<div class='bg-white text-center p-header hide'>		
		<img class='".$isPrint."' src='".base_url().ASSETS."/img/header.png' style='max-width:100%'/>
		</div>	
		<div class='bg-light text-center'>
		<h1 class='txt-black' style='padding-bottom:0px;padding-top:0px;margin:0px'>$FORM_TITLE</h1>
		</div>

		<table width='100%' id='data-table' class='table table-hover table-bordered'>
		<thead class='bg-light'>		  
		  <tr class='bg-info'>
		  	<th width='100%' colspan='8' class='text-center' style='padding-top:4px; padding-bottom:1px;'>
			<h3 class='txt-des'>".$DateRange."</h3>
			</th>
		  </tr>
		  <tr class='bg-light'>
		  	<th width='100%' colspan='8' class='text-center' style='border: none !important;padding-bottom:1px;'>
			<table style='width: 100% !important;' class='table brand-tbl'>			
			<tr>
			<td class='text-left' width='15%'>Bill No.</td>
			<td class='text-left' width='18%'>$mrow->bill_no</td>
			<td class='text-left' width='15%'>Bill Date</td>
			<td class='text-left' width='23%'>$mrow->billing_date</td>
			<td class='text-left' width='15%'>Employee No.</td>
			<td class='text-left' width='20%'>$mrow->employee_code</td>
			</tr>
			<tr>
			<td class='text-left' width='15%'>Employee Name</td>
			<td class='text-left' width='18%'>$mrow->account_name</td>
			<td class='text-left' width='15%'>Designation</td>
			<td class='text-left' width='23%'>$mrow->designation</td>
			<td class='text-left' width='15%'>Fathers Name</td>
			<td class='text-left' width='20%'>$mrow->fathers_name</td>
			</tr>
			<tr>
			<td class='text-left' width='15%'>Shift</td>
			<td class='text-left' width='18%'>$mrow->shift_name</td>
			<td class='text-left' width='15%'>Session Name</td>
			<td class='text-left' width='23%'>$mrow->session_name</td>
			<td class='text-left' width='15%'>Version Name</td>
			<td class='text-left' width='20%'>$mrow->version_name</td>
			</tr>
			</table>
			</th>			
		  </tr>
		  
		  <tr class='bg-primary'>
			<th width='2%' class='text-center'>SL</th>		
			<th width='30%' colspan='5'>Particulars</th>
			<th width='50%'>Remarks</th>
			<th width='18%' class='text-right'>Amount</th>
		  </tr>	
		</thead>
		<tbody>
		";		
		$bodyfull.=$headerTop;
						
		$header1="";
		$billing_month=01;		
		$body.= $header1;
										
		$this->db->select('m.*,d.*,f.account_name,f.count_unit,DATE_FORMAT(m.billing_date ,"%d %b %y") as billing_date', FALSE);
		$this->db->from(BILL_MASTER_TBL.' AS m');	
		$this->db->join(BILL_DETAILS_TBL.' AS d', 'd.bill_id=m.bill_id','LEFT');	
		$this->db->join(ACC_HEAD_TBL.' AS f', 'f.account_id=d.fee_account','LEFT');
		$this->db->where("m.account_id", $employee_id);
		$this->db->where("m.bill_id", $bill_id);	
					
		$this->db->group_by('d.details_id');
		$this->db->order_by('d.details_id','ASC');			
		$query = $this->db->get(); //echo $this->db->last_query();
		$TotalQty=0; $TotalAmount=0; $sl=0; $cunit=""; $unit_price=0;
		foreach($query->result() as $row){	 
		$sl++; $TotalQty+=$row->quantity; $TotalAmount+=$row->total_price;
		$duration= $row->quantity;
		$unit_price = $row->unit_price;
		if($row->count_unit==20){$cunit=" ";}elseif($row->count_unit==1){$cunit=" year";}elseif($row->count_unit==2){$cunit=" month";}
		elseif($row->count_unit==3){$cunit=" days";}elseif($row->count_unit==4){$cunit=" pcs";}elseif($row->count_unit==5){$cunit=" dzn";}elseif($row->count_unit==6){$cunit=" fit";}
		
		$body.="
		<tr>
		   <td class='text-center'>".$sl."</td>
		   <td class='text-left' colspan='5'>".$row->account_name."</td>
		   <td class='text-left'>".$row->remarks."</td>
		   <td class='text-right'>".number_format($row->total_price, 0, '.', ',')."</td>

		</tr>";
		}// end foreach									
		
		$body.="
	    	 <tr class='bg-gray'>
			<td colspan='7' class='text-right'><strong>Total:</strong></td>";
		$body.="
			<td class='text-right'><strong>".number_format($TotalAmount, 0, '.', ',')."</strong></td>
	    	 </tr>";
		
		$body.="
	    	 <tr>
			<td colspan='7' class='text-right'><strong>Total Payable Amount:</strong></td>
			<td class='text-right'><strong>".number_format($mrow->bill_amount, 0, '.', ',')."</strong></td>
	    	 </tr>";
		if($mrow->deduction_amount >0){		
		$body.="
	    	 <tr class='bg-info'>
			<td colspan='7' class='text-right'><strong>Total Deduction Amount:</strong></td>
			<td class='text-right'><strong>".number_format($mrow->deduction_amount, 0, '.', ',')."</strong></td>
	    	 </tr>
	    	 <tr>
			<td colspan='7' class='text-right'><strong>Net Payable Amount:</strong></td>
			<td class='text-right'><strong>".number_format($mrow->net_bill_amount, 0, '.', ',')."</strong></td>
	    	 </tr>";
		}
		
				
		$inwords 	="";
		$numberArr 	= explode(".",$mrow->net_bill_amount);
		$number1 	= $numberArr[0];
		$number2 	= $numberArr[1];

		$inwords = $this->InWords($number1);
		if($number2 >0){
		$inwords2= $this->InWords($number2);
		$inwords.= " Taka ".$inwords2." paisa ";
		}else{ $inwords.=" Taka ";} 
		$inwords.= " Only";

		$body.="
	    	 <tr class='bg-light'>
			<td colspan='8' class='text-left'><strong>In Words : ".$inwords."</strong></td>
	    	 </tr>";				
		$body.="
		<tr>
	       <td colspan='5' class='text-left' style='padding-top:20px;'>
			On behalf of <strong>".$this->session->userdata('company_name')."</strong><br>
			
	            </td>
		    <td colspan='3' class='text-right' style='padding-top:20px;'>
			On behalf of <strong>".$account_name."</strong>
			<div style='padding-top:25px;padding-bottom:0px'>----------------------------</div>
			Signature & Date 
	            </td>
	        </tr>
		</tbody>		
		<tfoot>
	        <tr>
	            <td id='footer' colspan='8' class='text-center'>&nbsp;</td>
	        </tr>
	    	</tfoot>				
		</table>
		<div id='p-footer'>
		<img class='".$isPrint." hide' src='".base_url().ASSETS."/img/footer.png' style='max-width:100%;'/>
		</div>
		";
		$bodyfull.=$body;		

		echo $bodyfull;
	}
	
	function GetEmpProfile(){
		
		$employee_id	= $this->input->post('employee-id');		
		
		$this->db->select('a.*,p.account_id,p.group_id,p.subsidiary_level1,p.subsidiary_level2,p.head_type,p.account_name,p.bangla_name,p.nationality,p.phone,p.mobile,p.email,p.gender,p.blood_group,p.religion,i.company_name,b.branch_name,b.branch_code,q.qualification_name,DATE_FORMAT(a.appointment_date ,"%d-%m-%Y") as appointment_dates,DATE_FORMAT(a.joining_date ,"%d-%m-%Y") as joining_dates,DATE_FORMAT(p.dob ,"%d-%m-%Y") as birthday',FALSE);
		$this->db->from(EMPLOYEE_TBL." AS a");
		$this->db->join(ACC_HEAD_TBL.' AS p', 'p.account_id=a.employee_id','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.company_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=a.branch_id','LEFT');
	  	$this->db->join(QUALIFICATION_TBL.' AS q', 'q.qualification_id=a.education_qualification','LEFT');
		$this->db->where("a.employee_id", $employee_id);
		//$this->db->where("a.status", 1);			
		$this->db->group_by('a.employee_id');
		$this->db->order_by('a.employee_id','ASC');			
		$mrow = $this->db->get()->row(); //echo $this->db->last_query();
		$employee_code 	  = $mrow->employee_code;							
		$account_name	  = $mrow->account_name;
		$salesType 		  = "Employee";
		
				
		if($this->session->userdata('user_role')<=100){$isPrint="";}else{$isPrint="no-print";}
		$headerTop=""; $header1=""; $header2="";
		$body="";$bodyfull=""; $bodytop=""; $bodybtm=""; $sl=0; 
		if($mrow->employee_type==10){
		$FORM_TITLE ="EMPLOYEE PROFILE"; $EmployeeType="Employee";
		}else{
		$FORM_TITLE ="EMPLOYEE PROFILE"; $EmployeeType="Teacher";
		}
		if($mrow->appointment_type==1){$appointment_type="Full-time";}else{$appointment_type="Part-time";}
		if($mrow->status==1){$status="Active";}else{$status="Inactive";}
		$MajorSubject = ""; //$this->GetSubjectNames($mrow->major_subject);
		$Password = substr($mrow->password, 0, -2);
		$bodytop="	
		<div class='bg-white text-center p-header hide'>		
		<img class='".$isPrint."' src='".base_url().ASSETS."/img/header.png' style='max-width:100%'/>
		</div>	
		<div class='bg-light text-center'>
		<h1 class='txt-black' style='padding-bottom:0px;padding-top:0px;margin:0px'>$FORM_TITLE</h1>
		</div>
		";
		$body="<table width='100%' id='data-table' class='table table-responsive table-hover table-bordered'>
		<thead class='bg-light'>		  
		  <tr class='bg-info'>
		  	<th width='100%' colspan='9' class='text-center' style='padding-top:4px; padding-bottom:1px;'>
			<h3 class='txt-des'>Personal Information</h3>
			</th>
		  </tr>
		  <tr class='bg-light'>
		  	<th width='75%' colspan='8' class='text-center' style='border: none !important;padding-bottom:1px;'>
			<table style='width: 100% !important;' class='table table-responsive table-bordered brand-tbl'>			
			<tr>
			<td class='text-left' width='17%'>ERP ID</td>
			<td class='text-left' width='15%'>$employee_code</td>
			<td class='text-left' width='20%'>Employee Name (Bng)</td>
			<td class='text-left' width='24%'>$mrow->bangla_name</td>
			<td class='text-left' width='14%'>Blood Group</td>
			<td class='text-left' width='10%'>$mrow->blood_group</td>
			</tr>
			<tr>
			<td class='text-left' width='17%'>Employee ID</td>
			<td class='text-left' width='15%'>$mrow->card_id</td>
			<td class='text-left' width='20%'>Employee Name (Eng)</td>
			<td class='text-left' width='24%'>$mrow->account_name</td>
			<td class='text-left' width='14%'>Nationality</td>
			<td class='text-left' width='10%'>$mrow->nationality</td>
			</tr>
			<tr>
			<td class='text-left' width='17%'>Joining Date</td>
			<td class='text-left' width='15%'>$mrow->joining_dates</td>
			<td class='text-left' width='20%'>Date of Birth</td>
			<td class='text-left' width='24%'>$mrow->birthday</td>
			<td class='text-left' width='14%'>Religion</td>
			<td class='text-left' width='10%'>$mrow->religion</td>
			</tr>
			<tr>
			<td class='text-left' width='17%'>Contract No.</td>
			<td class='text-left' width='15%'>$mrow->mobile</td>
			<td class='text-left' width='20%'>Gender</td>
			<td class='text-left' width='24%'>$mrow->gender</td>
			<td class='text-left' width='14%'>Marital Status</td>
			<td class='text-left' width='10%'>$mrow->marital_status</td>
			</tr>
			</table>
			</th>
			
		  	<th width='15%' class='text-center' style='border: none !important;padding-bottom:2px'>
			<table width='100%' id='data-table' class='table table-responsive table-bordered brand-tbl'>			
			<tr>
			<td class='text-left' width='100%'>
			<img class='img-responsive' src='".base_url().ASSETS."/".IMG."/$mrow->photo' style='max-width:100%'/>
			</td>
			</tr>
			</table>
			</th>			
		  </tr>
		  
		  <tr class='bg-light'>
		  	<th width='100%' colspan='9' class='text-center' style='border: none !important;padding-top:1px; padding-bottom:1px;'>
			<table style='width: 100% !important;' class='table table-responsive table-bordered brand-tbl'>			
			<tr>
			<td class='text-left' width='15%'>Pre-Address</td>
			<td class='text-left' colspan='5' width='75%'>$mrow->address</td>
			</tr>
			<tr>
			<td class='text-left' width='15%'>Per-Address</td>
			<td class='text-left' colspan='5' width='75%'>$mrow->permanent_address</td>
			</tr>
			<tr>
			<td class='text-left' width='15%'>Father's Name</td>
			<td class='text-left' width='17%'>$mrow->fathers_name</td>
			<td class='text-left' width='20%'>Mother's Name</td>
			<td class='text-left' width='25%'>$mrow->mothers_name</td>
			<td class='text-left' width='15%'>Spouse Name</td>
			<td class='text-left' width='14%'>$mrow->spouse_name</td>
			</tr>
			<tr>
			<td class='text-left' width='15%'>Phone</td>
			<td class='text-left' width='17%'>$mrow->phone</td>
			<td class='text-left' width='20%'>Mobile</td>
			<td class='text-left' width='25%'>$mrow->mobile</td>
			<td class='text-left' width='15%'>Email</td>
			<td class='text-left' width='14%'>$mrow->email</td>
			</tr>
			<tr>
			<td class='text-left' width='15%'>Qualification</td>
			<td class='text-left' width='17%'>$mrow->qualification_name</td>
			<td class='text-left' width='20%'>Extar Qualification</td>
			<td class='text-left' width='54%' colspan='3'>$mrow->extra_qualification</td>
			</tr>
			</table>
			</th>
		  </tr>
		  <tr class='bg-primary'>
		  	<th width='100%' colspan='9' class='text-left' style='padding-top:4px; padding-bottom:1px;'>
			<h4>Appointment Information</h4>
			</th>
		  </tr>
		  <tr class='bg-light'>
		  	<th width='100%' colspan='9' class='text-center' style='padding-top:4px; padding-bottom:1px;'>
			<table style='width: 100% !important;' class='table table-responsive table-bordered brand-tbl'>			
			<tr>
			<td class='text-left' width='15%'>Institute Name</td>
			<td class='text-left' width='22%'>$mrow->company_name</td>
			<td class='text-left' width='16%'>Branch Name</td>
			<td class='text-left' width='25%'>$mrow->branch_name</td>
			<td class='text-left' width='15%'>Appointment Type</td>
			<td class='text-left' width='17%'>$appointment_type</td>
			</tr>
			<tr>
			<td class='text-left' width='15%'>Appointment Date</td>
			<td class='text-left' width='22%'>$mrow->appointment_dates</td>
			<td class='text-left' width='16%'>Designation</td>
			<td class='text-left' width='25%'>$mrow->designation</td>
			<td class='text-left' width='15%'>Weekend</td>
			<td class='text-left' width='17%'>$mrow->weekend</td>
			</tr>
			<tr>
			<td class='text-left' width='15%'>Login ID</td>
			<td class='text-left' width='22%'>$mrow->login_id</td>
			<td class='text-left' width='16%'>Password</td>
			<td class='text-left' width='25%'>$Password</td>
			<td class='text-left' width='15%'>Status</td>
			<td class='text-left' width='17%'>$status</td>
			</tr>
			</table>
			</th>
		  </tr>
		  
		  <tr class='bg-primary'>
		  	<th width='100%' colspan='9' class='text-left' style='padding-top:4px; padding-bottom:1px;'>
			<h4>Salary Structure</h4>
			</th>
		  </tr>
		  <tr class='bg-light'>
		  	<th width='100%' colspan='9' class='text-center' style='padding-top:4px; padding-bottom:1px;'>
			<table style='width: 100% !important;' class='table table-responsive table-bordered brand-tbl'>	
					
			<tr>
			<td class='text-left' width='18%'>Cash Salary</td>
			<td class='text-left' width='12%'>$mrow->cash_salary</td>
			<td class='text-left' width='18%'>T&T Allowance</td>
			<td class='text-left' width='20%'>$mrow->tnt_allowance</td>
			<td class='text-left' width='15%'>Others Payble</td>
			<td class='text-left' width='17%'>$mrow->others_payble</td>
			</tr>
			
			<tr>
			<td class='text-left' width='18%'>Total Fixed Payble</td>
			<td class='text-left' colspan='5' width='82%'>$mrow->total_fix_payble</td>
			</tr>
			
			<tr>
			<td class='text-left' width='18%'>Basic Salary</td>
			<td class='text-left' width='12%'>$mrow->basic_salary</td>
			<td class='text-left' width='18%'>Houserent Allowance</td>
			<td class='text-left' width='20%'>$mrow->houserent_allowance</td>
			<td class='text-left' width='15%'>Medical Allowance</td>
			<td class='text-left' width='17%'>$mrow->medical_allowance</td>
			</tr>
			<tr>
			<td class='text-left' width='18%'>Conveyance Allowance</td>
			<td class='text-left' width='12%'>$mrow->transport_allowance</td>
			<td class='text-left' width='18%'>Festival Bonus</td>
			<td class='text-left' width='20%'>$mrow->festival_bonus (% On basic salary)</td>
			<td class='text-left' width='15%'>Others Allowance</td>
			<td class='text-left' width='17%'>$mrow->others_allowance</td>
			</tr>
			<tr>
			<td class='text-left' width='18%'>Gross Salary</td>
			<td class='text-left' colspan='5' width='82%'>$mrow->gross_salary</td>
			</tr>
			<tr>
			<td class='text-left' width='18%'>Provident Fund</td>
			<td class='text-left' width='12%'>$mrow->provident_fund</td>
			<td class='text-left' width='18%'>Income Tax</td>
			<td class='text-left' width='20%'>$mrow->income_tax</td>
			<td class='text-left' width='15%'>Loan & Advance</td>
			<td class='text-left' width='17%'>$mrow->loan_and_adv</td>
			</tr>
			<tr>
			<td class='text-left' width='18%'>Gross Deduction</td>
			<td class='text-left' colspan='2' width='30%'>$mrow->gross_deduction</td>
			<td class='text-left' width='20%'>Net Salary</td>
			<td class='text-left' colspan='2' width='32%'>$mrow->net_salary</td>
			</tr>
			</table>
			</th>
		  </tr>	
		</thead>
		<tbody>";
		$bodybtm="		
		<tfoot>
	        <tr>
	            <td id='footer' colspan='9' class='text-center'>&nbsp;</td>
	        </tr>
	    	</tfoot>				
		</table>
		<div id='p-footer'>
		<img class='".$isPrint." hide' src='".base_url().ASSETS."/img/footer.png' style='max-width:100%;'/>
		</div>
		";
		$bodyfull.=$bodytop.$body.$bodybtm;		

		echo $bodyfull;
	}
	function GetEmpLeaveStatus(){
		
		$employee_id   = $this->input->post('employee-id');		
		$session_id    = $this->session->userdata('default_session');
		
		$this->db->select('a.*,p.account_id,p.group_id,p.subsidiary_level1,p.subsidiary_level2,p.head_type,p.account_name,p.bangla_name,p.nationality,p.phone,p.mobile,p.email,p.gender,p.blood_group,p.religion,i.company_name,b.branch_name,b.branch_code,d.department_name,DATE_FORMAT(a.appointment_date ,"%d-%m-%Y") as appointment_dates,DATE_FORMAT(a.joining_date ,"%d-%m-%Y") as joining_dates,DATE_FORMAT(p.dob ,"%d-%m-%Y") as birthday',FALSE);
		$this->db->from(EMPLOYEE_TBL." AS a");
		$this->db->join(ACC_HEAD_TBL.' AS p', 'p.account_id=a.employee_id','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.company_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=a.branch_id','LEFT');
	  	$this->db->join(DEPARTMENT_TBL.' AS d', 'd.department_id=a.department_id','LEFT');
		$this->db->where("a.employee_id", $employee_id);
		//$this->db->where("a.status", 1);			
		$this->db->group_by('a.employee_id');
		$this->db->order_by('a.employee_id','ASC');			
		$mrow = $this->db->get()->row(); //echo $this->db->last_query();
		$employee_code    = $mrow->employee_code;
		$card_id          = $mrow->card_id;							
		$company_id	      = $mrow->company_id;
		$salesType 		  = "Employee";
		
				
		if($this->session->userdata('user_role')<=100){$isPrint="";}else{$isPrint="no-print";}
		$headerTop=""; $header1=""; $header2="";
		$body="";$bodyfull=""; $bodytop=""; $bodybtm=""; $sl=0; 
		
		if($mrow->appointment_type==1){$appointment_type="Full-time";}else{$appointment_type="Part-time";}
		if($mrow->status==1){$status="Active";}else{$status="Inactive";}
		
		$Password = substr($mrow->password, 0, -2);
		$bodytop="	
		<div class='bg-white text-center p-header hide'>		
		<img class='".$isPrint."' src='".base_url().ASSETS."/img/header.png' style='max-width:100%'/>
		</div>
		";
		$body="<table width='100%' id='data-table' class='table table-hover table-bordered'>
		<thead class='bg-light'>		  
		  <tr class='bg-info'>
		  	<th width='100%' colspan='9' class='text-left' style='padding-top:4px; padding-bottom:1px;'>
			<h3 class='txt-des'>Employee Personal Information</h3>
			</th>
		  </tr>
		  <tr class='bg-light'>
		  	<th width='100%' colspan='9' class='text-center' style='border: none !important;padding-bottom:1px;'>
			<table style='width: 100% !important;' class='table table-responsive table-bordered brand-tbl'>			
			<tr>
			<td class='text-left' width='20%' style='border: 0px;padding:0.15rem;'>Employee Name</td>
			<td class='text-left' width='40%' style='border: 0px;padding:0.15rem;'>$mrow->account_name</td>
			<td rowspan='4' width='40%'>
			<table width='100%' id='data-table' class='table table-bordered brand-tbl'>			
			<tr>
			<td class='text-left' width='36%'>
			<img class='img-responsive' src='".base_url().ASSETS."/".IMG."/$mrow->photo' style='max-width:99%;height:100%'/>
			</td>
			<td class='text-left' width='32%'>
			<img class='img-responsive' src='".base_url().ASSETS."/".IMG."/addleave.png' style='max-width:99%;height:100%'/>
			</td>
			<td class='text-left' width='32%'>
			<a href='".SERVER."/employee/ViewLeaveForm/".$mrow->employee_id."'><img class='img-responsive' src='".base_url().ASSETS."/".IMG."/viewleave.png' style='max-width:99%;height:100%'/></a>
			</td>
			</tr>
			</table>
			</td>
			</tr>
			<tr>
			<td class='text-left' width='20%' style='border: 0px;padding:0.15rem;'>Designation</td>
			<td class='text-left' width='40%' style='border: 0px;padding:0.15rem;'>$mrow->designation</td>
			</tr>
			<tr>
			<td class='text-left' width='20%' style='border: 0px;padding:0.15rem;'>Department</td>
			<td class='text-left' width='40%' style='border: 0px;padding:0.15rem;'>$mrow->department_name</td>
			</tr>
			<tr>
			<td class='text-left' width='20%' style='border: 0px;padding:0.15rem;'>Joining Date</td>
			<td class='text-left' width='40%' style='border: 0px;padding:0.15rem;'>$mrow->joining_dates</td>
			</tr>
			</table>
			</th>			
		  </tr>
		  </thead>
		  <tbody>
		  <tr class='bg-primary'>
		  	<th width='100%' colspan='9' class='text-left' style='padding-top:4px; padding-bottom:1px;'>
			<h4>Leave Position</h4>
			</th>
		  </tr>
		  <tr class='bg-light'>
		  	<th width='100%' colspan='9' class='text-center' style='padding-top:4px; padding-bottom:1px;'>
			<table style='width: 100% !important;' class='table table-bordered brand-tbl'>			
			<tr class='bg-gray'>
			<td class='text-left' width='5%'>SL</td>
			<td class='text-left' width='35%'>Type of Leave</td>
			<td class='text-left' width='20%'>Total Leave</td>
			<td class='text-left' width='20%'>Leave Availed</td>
			<td class='text-left' width='20%'>Balance</td>
			</tr>";
			$lcsql = "SELECT * FROM ".LEAVE_CATEGORY_TBL." WHERE company_id = $company_id AND status=1";
		    $lcquery = $this->db->query($lcsql);
		    $sl=1; $Availed=0; $Balance=0; $TotalLeave=0; $TotalAvailed=0; $TotalBalance=0;
		    foreach($lcquery->result() as $lrow){
		    $leave_nature = $lrow->category_id;
		    $Availed = $this->GetTotalLeaveAvailed($company_id,$employee_id,$session_id,$leave_nature);
		    $TotalLeave+= $lrow->total_leave;
		    $TotalAvailed+= $Availed;
		    $Balance = ($lrow->total_leave - $Availed);
		    $TotalBalance+=$Balance;    
		$body.="	
			<tr>
			<td class='text-left' width='5%'>$sl</td>
			<td class='text-left' width='35%'>$lrow->leave_type</td>
			<td class='text-left' width='20%'>$lrow->total_leave</td>
			<td class='text-left' width='20%'>".$Availed."</td>
			<td class='text-left' width='20%'>".$Balance."</td>
			</tr>";
			$sl++;
		    }   
		$body.="	
			<tr class='bg-gray'>
			<td class='text-left' width='40%' colspan='2'>Total</td>
			<td class='text-left' width='20%'>$TotalLeave</td>
			<td class='text-left' width='20%'>$TotalAvailed</td>
			<td class='text-left' width='20%'>$TotalBalance</td>
			</tr>";
		$body.="
			</table>
			</th>
		  </tr>
		</tbody>
		";
		$bodybtm="		
		<tfoot>
	        <tr>
	            <td id='footer' colspan='9' class='text-center'>&nbsp;</td>
	        </tr>
	    </tfoot>				
		</table>
		<div id='p-footer'>
		<img class='".$isPrint." hide' src='".base_url().ASSETS."/img/footer.png' style='max-width:100%;'/>
		</div>
		";
		$bodyfull.=$bodytop.$body.$bodybtm;		

		echo $bodyfull;
	}
	function GetTotalLeaveAvailed($company_id,$emp_id,$session_id,$leave_nature){
	    $total_days=0;
	    $sql = "SELECT SUM(total_days) as total_days FROM ".LEAVE_TBL." WHERE company_id = $company_id AND employee_id = $emp_id AND session_id = $session_id AND leave_nature = $leave_nature";
		$query = $this->db->query($sql);
		if($query->num_rows() >0){
		  $total_days= $query->row()->total_days;
		}else{ $total_days=0;}
		if($total_days==""){ $total_days=0;}
		return $total_days;
	}
	
	function GetEmpLeaveForm(){
		
		$employee_id	= $this->input->post('employee-id');		
		$session_id     = $this->session->userdata('default_session');
		
		$this->db->select('a.employee_code,a.card_id,a.designation,a.photo,p.account_id,p.head_type,p.account_name,p.phone,p.mobile,p.email,p.gender,p.blood_group,i.company_id,i.company_name,i.address,i.phone as company_phone, i.email as company_email,b.branch_name,d.department_name,DATE_FORMAT(a.joining_date ,"%d-%m-%Y") as joining_dates',FALSE);
		$this->db->from(EMPLOYEE_TBL." AS a");
		$this->db->join(ACC_HEAD_TBL.' AS p', 'p.account_id=a.employee_id','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.company_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=a.branch_id','LEFT');
	  	$this->db->join(DEPARTMENT_TBL.' AS d', 'd.department_id=a.department_id','LEFT');
		$this->db->where("a.employee_id", $employee_id);
		//$this->db->where("a.status", 1);			
		$this->db->group_by('a.employee_id');
		$this->db->order_by('a.employee_id','ASC');			
		$mrow = $this->db->get()->row(); //echo $this->db->last_query();
		$card_id          = $mrow->card_id;							
		$company_id	      = $mrow->company_id;
		
				
		if($this->session->userdata('user_role')<=100){$isPrint="";}else{$isPrint="no-print";}
		$headerTop=""; $header1=""; $header2="";
		$body="";$bodyfull=""; $bodytop=""; $bodybtm=""; $sl=0; 
		
		$bodytop="	
		<div class='bg-white text-center'>		
		<div align='center'>
			  <table width='100%' border='0'  style='border:opx'>
				<tr>
					<td width='2%' height='48'>&nbsp;</td>
					<td width='20%' rowspan='3' align='left'>
					<img src='".base_url().ASSETS."/".IMG."/company/md-1.png' width='160' height='120'/> <br/> ERP ID: $mrow->employee_code</td>
					<td width='56%' align='center'><h1 style='font-size:30px; margin-bottom:0px; font-family:'Arial', Gadget, sans-serif; letter-spacing:2px'>LIRA GROUP</h1></td>
					<td width='20%' rowspan='4' align='right'>
					<img src='".base_url().ASSETS."/".IMG."/".$mrow->photo."' width='120' height='140'/>
					<br>Date: <div style='float:right; width:125px; margin-right:10px;border-bottom: 2px dotted #CCC;' >&nbsp;</div>
					</td>
					<td width='2%' height='49'>&nbsp;</td>		
				</tr>
				<tr>
					<td width='20'>&nbsp;</td>
					<td align='center'>".$mrow->address."</td>
					
				</tr>
				<tr>
				  <td width='20'>&nbsp;</td>
				  <td align='center'>
				  <div id='leaveApp' style='margin-top:18px;padding-top:2px;padding-bottom:2px'><h4>LEAVE APPLICATION</h4></div>
				  </td>
				  
				</tr>
			  </table>
			</div>
		</div>
		";
		$body="
			<table align='center' width='100%' border='0' cellspacing='0' cellpadding='2'>
			<thead class='bg-light'>
			<tr>
			  <td colspan='9' align='center'>
			  <table width='98%' cellspacing='0' cellpadding='2' class='table table-hover table-bordered'>
               	<tr class='bg-gray'>
			       <td colspan='6' id='tb'><b>Leave Information</b></td>
		        </tr>
			    <tr>
			      <td width='18%' class='tb' id='tb'>Nature of leave</td>
			      <td width='1%' class='rtb' id='rtb'>:</td>
			      <td colspan='4' class='rtb' id='rtb'>Earned / Casual/ Sick / Special / Maternity / Paternity</td>
		        </tr>
			    <tr>
			      <td class='tb' id='tb'>Leave From</td>
			      <td width='1%' class='rtb' id='rtb bb'>:</td>
			      <td width='31%' class='rtb' id='rtb bb'>&nbsp;</td>
			      <td width='17%' class='tb bb'>Leave To</td>
			      <td width='1%' class='rtb' id='rtb bb'>:</td>
			      <td width='32%' class='tb' id='rtb'>&nbsp;</td>
		        </tr>
			    <tr>
			      <td class='tb' id='tb'>Total Days</td>
			      <td class='rtb' id='rtb'>:</td>
			      <td colspan='4' class='rtb' id='rtb'>&nbsp;</td>
		        </tr>
                	<tr>
			      <td class='tb' id='tb'>Purpose of Leave</td>
			      <td class='rtb' id='rtb'>:</td>
			      <td colspan='4' class='rtb' id='rtb'>&nbsp;</td>
		        </tr>
		      </table>
			  </td>
			</tr>
		  </thead>
		  <tbody>
			<tr>
			  <td colspan='4' align='center'>
			  <div style='padding-top: 25px;'></div>
		      <table width='98%' border='0' cellspacing='0' cellpadding='3'>
			    <tr>
			      <th height='28' colspan='3' align='left'>Leave address</th>
			   </tr>
			   <tr>
			      <td width='28%'>Address</td>
			      <td width='2%'>:</td>
			      <td width='70%' style='border-bottom: 2px dotted #CCC;width: 150px;'>&nbsp;</td>
			   </tr>
			    <tr>
			      <td>&nbsp;</td>
			      <td>:</td>
			      <td style='border-bottom: 2px dotted #CCC;width: 150px;'>&nbsp;</td>
		        </tr>
                 	<tr>
			      <td>&nbsp;</td>
			      <td>:</td>
			      <td style='border-bottom: 2px dotted #CCC;width: 150px;'>&nbsp;</td>
		        </tr>
			    <tr>
			      <td>Mobile</td>
			      <td>:</td>
			      <td style='border-bottom: 2px dotted #CCC;width: 150px;'>&nbsp;</td>
		        </tr>
			    </table>
			   </td>
			   <td colspan='6' align='left'>
              	<table width='99%' border='0' cellspacing='0' cellpadding='3'>
			    <tr>
			      <th height='28' colspan='3' align='left'>Leave Applicant </th>
			    </tr>
			   <tr>
			      <td width='27%'>Signature</td>
			      <td width='2%'>:</td>
			      <td width='71%'><div class='dotLine' id='dotLine'>&nbsp;</div></td>
			    </tr>
			    <tr>
			      <td width='27%'>Name</td>
			      <td width='2%'>:</td>
			      <td width='71%'>$mrow->account_name</td>
			    </tr>
			    <tr>
			      <td>Designation</td>
			      <td>:</td>
			      <td>$mrow->designation</td>
			    </tr>
			    <tr>
			      <td>Department</td>
			      <td>:</td>
			      <td>
				  $mrow->department_name, &nbsp;
                  Employee Id: $mrow->card_id
                  </td>
			    </tr>
			    </table>
			    <div style='padding-top: 25px;'></div>
			</td>			
		  </tr>
		  <tr>
		  	<td width='100%' colspan='9' class='text-left' style='padding-top:4px; padding-bottom:1px;'>
		  	<div style='padding-top: 25px;'></div>
			<table class='table table-bordered brand-tbl' width='100%' cellspacing='0' cellpadding='3'>
		      <tr>
		        <td width='13%' height='24' nowrap='nowrap' class='tb' id='bb'>Recommended by</td>
		        <td width='1%' class='rtb' id='bb'>:</td>
		        <td width='18%' class='rtb' id='bb'>&nbsp;</td>
		        <td width='14%' nowrap='nowrap' class='tb' id='bb'>Sec. Chief</td>
		        <td width='1%' class='rtb' id='bb'>:</td>
		        <td width='19%' class='rtb' id='bb'>&nbsp;</td>
		        <td width='14%' nowrap='nowrap' class='tb' id='bb'>Dept. Head</td>
		        <td width='1%' class='rtb' id='bb'>:</td>
		        <td width='19%' class='tb' id='bb'>&nbsp;</td>
	          </tr>
	        </table>
			</td>
		  </tr>
		  <tr>
		  	<td width='100%' colspan='9' class='text-left' style='padding-top:4px; padding-bottom:1px;'>
		  	<div style='padding-top: 155px;'></div>
			<table width='100%' cellspacing='0' cellpadding='3'>
		      <tr>
		        <td width='20%' height='24' nowrap='nowrap' class='tb' id='bb'>Authorized Executive (HRD)</td>
		        <td width='1%' class='rtb' id='bb'>:</td>
		        <td width='35%' style='border-bottom: 2px dotted #CCC;width: 140px;'>&nbsp;</td>
		        <td width='8%' nowrap='nowrap' class='tb' id='bb'>Managing Director</td>
		        <td width='1%' class='rtb' id='bb'>:</td>
		        <td width='35%' style='border-bottom: 2px dotted #CCC;width: 140px;'>&nbsp;</td>
	          </tr>
		      <tr>
		        <td width='20%' style='padding-top:15px' nowrap='nowrap' class='tb' id='bb'>Chairman</td>
		        <td width='1%' class='rtb' id='bb'>:</td>
		        <td width='43%' style='border-bottom: 2px dotted #CCC;width: 110px;'>&nbsp;</td>
		        <td width='36%' colspan='2' class='rtb' id='bb'>&nbsp;</td>
	          </tr>
	        </table>
			</td>
		  </tr>
		</tbody>
		";
		$bodybtm="		
		<tfoot>
	        <tr>
	            <td id='footer' colspan='9' class='text-center'>&nbsp;</td>
	        </tr>
	    </tfoot>				
		</table>
		<div id='p-footer'>
		
		</div>
		";
		$bodyfull.=$bodytop.$body.$bodybtm;		

		echo $bodyfull;
	}
	function GetCustomerProfile(){
		
		$customer_id	= $this->input->post('customer-id');		
		
		$this->db->select('a.*,p.account_id,p.group_id,p.subsidiary_level1,p.subsidiary_level2,p.head_type,p.account_name,p.bangla_name,p.nationality,p.phone,p.mobile,p.email,p.gender,p.blood_group,p.religion,i.company_name,b.branch_name,b.branch_code,q.qualification_name,DATE_FORMAT(a.issue_date ,"%d-%m-%Y") as issue_date,DATE_FORMAT(a.expiry_date ,"%d-%m-%Y") as expiry_date,DATE_FORMAT(p.dob ,"%d-%m-%Y") as birthday',FALSE);
		$this->db->from(CLIENT_TBL." AS a");
		$this->db->join(ACC_HEAD_TBL.' AS p', 'p.account_id=a.customer_id','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.company_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.branch_id=a.branch_id','LEFT');
	  	$this->db->join(QUALIFICATION_TBL.' AS q', 'q.qualification_id=a.education_qualification','LEFT');
		$this->db->where("a.customer_id", $customer_id);
		//$this->db->where("a.status", 1);			
		$this->db->group_by('a.customer_id');
		$this->db->order_by('a.customer_id','ASC');			
		$mrow = $this->db->get()->row(); //echo $this->db->last_query();
		$student_id  	= $mrow->customer_code;							
		$account_name	  = $mrow->account_name;
		$salesType 		  = "Customer";
		
				
		if($this->session->userdata('user_role')<=100){$isPrint="";}else{$isPrint="no-print";}
		$headerTop=""; $header1=""; $header2="";
		$body="";$bodyfull=""; $bodytop=""; $bodybtm=""; $sl=0; 
		$FORM_TITLE ="CUSTOMER PROFILE"; 
		if($mrow->customer_type==1){$customer_type="Corporate";}else{$customer_type="General";}
		
		if($mrow->status==1){$status="Active";}else{$status="Inactive";}
		$agent_name = $this->getAccountName($mrow->supplier_id);
		$experience = $mrow->experience_year;
		if($mrow->experience_country!=""){
		  $experience.="<br>".$mrow->experience_country;  
		}
		if($mrow->visa_type==1){$visa_type="Business Visa";}elseif($mrow->visa_type==2){$visa_type="Tourist Visa";}elseif($mrow->visa_type==3){$visa_type="Work Visa";}elseif($mrow->visa_type==4){$visa_type="Student Visa";}elseif($mrow->visa_type==5){$visa_type="Immigration Visa";}else{$visa_type="Others Visa";}
		$Password   = substr($mrow->password, 0, -2);
		$bodytop="	
		<div class='bg-white text-center p-header hide'>		
		<img class='".$isPrint."' src='".base_url().ASSETS."/img/header.png' style='max-width:100%'/>
		</div>	
		<div class='bg-light text-center'>
		<h1 class='txt-black' style='padding-bottom:0px;padding-top:0px;margin:0px'>$FORM_TITLE</h1>
		</div>
		";
		$body="
		<table width='100%' id='data-table' class='table table-responsive table-hover table-bordered'>
		<thead class='bg-light'>		  
		  <tr class='bg-info'>
		  	<th width='100%' colspan='9' class='text-center' style='padding-top:4px; padding-bottom:1px;'>
			<h3 class='txt-des'>Personal Information</h3>
			</th>
		  </tr>
		  <tr class='bg-light'>
		  	<th width='75%' colspan='8' class='text-center' style='border: none !important;padding-bottom:1px;'>
			<table style='width: 100% !important;' class='table table-bordered brand-tbl'>	
			<tr>
			<td class='text-left' width='17%'>Customer ID.</td>
			<td class='text-left' width='15%'>$customer_id</td>
			<td class='text-left' width='20%'>Surname Name</td>
			<td class='text-left' width='24%'>$mrow->surname</td>
			<td class='text-left' width='14%'>Given Name</td>
			<td class='text-left' width='10%'>$mrow->given_name</td>
			</tr>
			<tr>
			<td class='text-left' width='17%'>Customer Full Name</td>
			<td class='text-left' width='15%'>$mrow->customer_full_name</td>
			<td class='text-left' width='20%'>Father's Name</td>
			<td class='text-left' width='24%'>$mrow->fathers_name</td>
			<td class='text-left' width='14%'>Mother's Name</td>
			<td class='text-left' width='10%'>$mrow->mothers_name</td>
			</tr>
			<tr>
			<td class='text-left' width='17%'>Blood Group</td>
			<td class='text-left' width='15%'>$mrow->blood_group</td>
			<td class='text-left' width='20%'>Nationality</td>
			<td class='text-left' width='24%'>$mrow->nationality</td>
			<td class='text-left' width='14%'>Religion</td>
			<td class='text-left' width='10%'>$mrow->religion</td>
			</tr>
			<tr>
			<td class='text-left' width='17%'>Contract No.</td>
			<td class='text-left' width='15%'>$mrow->mobile</td>
			<td class='text-left' width='20%'>Gender</td>
			<td class='text-left' width='24%'>$mrow->gender</td>
			<td class='text-left' width='14%'>Marital Status</td>
			<td class='text-left' width='10%'>$mrow->marital_status</td>
			</tr>
			<tr>
			<td class='text-left' width='17%'>Educational Qualification</td>
			<td class='text-left' width='15%'>$mrow->qualification_name</td>
			<td class='text-left' width='20%'>Extra Qualification</td>
			<td class='text-left' width='24%'>$mrow->extra_qualification</td>
			<td class='text-left' width='14%'>Email</td>
			<td class='text-left' width='10%'>$mrow->email</td>
			</tr>
			</table>
			</th>
			
		  	<th width='15%' class='text-center' style='border: none !important;padding-bottom:2px'>
			<table width='100%' id='data-table' class='table table-bordered brand-tbl'>			
			<tr>
			<td class='text-left' width='100%'>
			<img class='img-responsive' src='".base_url().ASSETS."/".IMG."/$mrow->photo' style='max-width:100%'/>
			</td>
			</tr>
			</table>
			</th>			
		  </tr>
		</thead>
		<tbody>
		  <tr class='bg-light'>
		  	<th width='100%' colspan='9' class='text-center' style='border: none !important;padding-top:1px; padding-bottom:1px;'>
			<table style='width: 100% !important;' class='table table-responsive table-bordered brand-tbl'>			
			<tr>
			<td class='text-left' width='15%'>Pre-Address</td>
			<td class='text-left' width='75%'>$mrow->address</td>
			</tr>
			<tr>
			<td class='text-left' width='15%'>Per-Address</td>
			<td class='text-left' width='75%'>$mrow->permanent_address</td>
			</tr>
			</table>
			</th>
		  </tr>	
		  <tr class='bg-light'>
		  	<th width='100%' colspan='9' class='text-center' style='border: none !important;padding-bottom:1px;'>
			<table style='width: 100% !important;' class='table table-responsive table-bordered brand-tbl'>	
			<tr>
			<td class='text-left' width='17%'>Company Name.</td>
			<td class='text-left' width='39%'>$mrow->company_name</td>
			<td class='text-left' width='20%'>Branch Name</td>
			<td class='text-left' width='24%'>$mrow->branch_name</td>
			</tr>
			<tr>
			<td class='text-left' width='17%'>Agency Name</td>
			<td class='text-left' width='15%'>$agent_name</td>
			<td class='text-left' width='20%'>Customer Type</td>
			<td class='text-left' width='24%'>$customer_type</td>
			</tr>
			<tr>
			<td class='text-left' width='17%'>Passport No</td>
			<td class='text-left' width='15%'>$mrow->passport_no</td>
			<td class='text-left' width='14%'>Passport Validity</td>
			<td class='text-left' width='10%'>$mrow->pp_validity</td>
			</tr>
			<tr>
			<td class='text-left' width='17%'>Experience Year</td>
			<td class='text-left' width='39%'>$mrow->experience_year</td>
			<td class='text-left' width='20%'>Experience Country</td>
			<td class='text-left' width='24%'>$mrow->experience_country</td>
			</tr>
			<tr>
			<td class='text-left' width='17%'>Issue Date</td>
			<td class='text-left' width='15%'>$mrow->issue_date</td>
			<td class='text-left' width='20%'>Expiry Date</td>
			<td class='text-left' width='24%'>$mrow->expiry_date</td>
			</tr>
			<tr>
			<td class='text-left' width='14%'>Visa No</td>
			<td class='text-left' width='10%'>$mrow->visa_no</td>
			<td class='text-left' width='17%'>Visa Type</td>
			<td class='text-left' width='15%'>$visa_type</td>
			</tr>
			<tr>
			<td class='text-left' width='20%'>Birth Day</td>
			<td class='text-left' width='24%'>$mrow->dob</td>
			<td class='text-left' width='14%'>Place of Birth</td>
			<td class='text-left' width='10%'>$mrow->place_of_birth</td>
			</tr>
			<tr>
			<td class='text-left' width='17%'>Login ID</td>
			<td class='text-left' width='15%'>$mrow->login_id</td>
			<td class='text-left' width='20%'>Password</td>
			<td class='text-left' width='24%'>$mrow->password</td>
			</tr>
		</table>
			</th>
		  </tr>	
		";
		$bodybtm="		
		<tfoot>
	        <tr>
	            <td id='footer' colspan='9' class='text-center'>&nbsp;</td>
	        </tr>
	    	</tfoot>				
		</table>
		<div id='p-footer'>
		<img class='".$isPrint." hide' src='".base_url().ASSETS."/img/footer.png' style='max-width:100%;'/>
		</div>
		";
		$bodyfull.=$bodytop.$body.$bodybtm;		

		echo $bodyfull;
	}
	
	function GetHolidayInfo(){
		$holiday_id	    = $this->input->post('holiday-id');		
		//$is_holiday     = $this->input->post('holiday_type');	
		$this->db->select('holiday_name,is_holiday,notice_image,DATE_FORMAT(from_date ,"%d-%m-%Y") as date_from,DATE_FORMAT(to_date ,"%d-%m-%Y") as date_to,DATEDIFF(to_date,from_date)+1 as total_days');
		$this->db->from(HOLIDAY_TBL);
		$this->db->where("holiday_id", $holiday_id);
		//$this->db->where("is_holiday", $is_holiday);			
		$this->db->where("status", 1);			
		$this->db->group_by('holiday_id');
		$this->db->order_by('to_date','DESC');			
		$mrow = $this->db->get()->row(); //echo $this->db->last_query();
		$holiday_name	= $mrow->holiday_name;
						
		if($this->session->userdata('user_role')<=100){$isPrint="";}else{$isPrint="no-print";}
		$headerTop=""; $header1=""; $header2="";
		$body="";$bodyfull=""; $sl=0; 
		if($mrow->is_holiday==1){
		$FORM_TITLE ="HOLIDAY NOTICE";
		}else{
		$FORM_TITLE ="NOTICE";
		}
		
		$body.="	
		<div class='bg-white text-center p-header hide'>		
		<img class='".$isPrint."' src='".base_url().ASSETS."/img/header.png' style='max-width:100%'/>
		</div>	
		<div class='bg-light text-center'>
		<h1 class='txt-black' style='padding-bottom:0px;padding-top:0px;margin:0px'>$FORM_TITLE</h1>
		</div>
		";
		$body.="
		<table width='100%' id='data-table' class='table table-responsive table-hover table-bordered'>
		<thead class='bg-light'>		  
		  <tr class='bg-info'>
		  	<th width='100%' class='text-center' style='padding-top:4px; padding-bottom:1px;'>
			<h3 class='txt-des'>".$mrow->holiday_name." from ".$mrow->date_from." to ".$mrow->date_to." total ".$mrow->total_days." days.</h3>
			</th>
		  </tr>
		</thead>
		<tbody>		  			
			<tr>
			<td class='text-center' width='100%'><img class='img-responsive' src='".base_url().ASSETS."/img/".$mrow->notice_image."' style='max-width:100%;'/></td>
			</tr>		  
		</tbody>";
		$body.="		
		<tfoot>
	        <tr>
	            <td id='footer' class='text-center'>&nbsp;</td>
	        </tr>
	    	</tfoot>				
		</table>
		<div id='p-footer'>
		<img class='".$isPrint." hide' src='".base_url().ASSETS."/img/footer.png' style='max-width:100%;'/>
		</div>
		";
		$bodyfull.=$body;		

		echo $bodyfull;
	}
	
	/*====== Start Common Function for pagination=======*/
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
		$paginationStr .= "<ul class='pagination'>";

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
				$paginationStr .= "<li><a onclick=nextPage($frm,$to,$pno) href='#'>&laquo;</a></li>";
			}
		} else {
			$paginationStr .= "<li><a class='disabled' href='#'>&laquo;</a></li>";
		}
		if ($totalpage >= 1) {
			$i = 1;
			$from = 0;
			$to = $block;
			while ($i <= $totalpage) {
				if ($from == 0) {
					$paginationStr .= "<li ";
					if ($i == $plink) {
						$paginationStr .= "class='active' ";
					}
					$paginationStr .= ">";
					$paginationStr .= "<a onclick=nextPage($from,$to,$i) href='#'>$i</a></li>";
				} else {
					$paginationStr .= "<li ";
					if ($i == $plink) {
						$paginationStr .= "class='active' ";
					}
					$paginationStr .= ">";
					$paginationStr .= "<a onclick=nextPage($from,$to,$i) href='#'>$i</a></li>";
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
				$paginationStr .= "<li><a onclick=nextPage($f,$t,$page) href='#'>&raquo;</a></li>";
			}
		} else {
			$paginationStr .= "<li><a class='disabled' href='#'>&raquo;</a></li>";
		}

		$paginationStr .= "</ul>";
		return $paginationStr;
	}
	function formatDate($dt)
	{
		if (trim($dt)) {
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
	function InWords($number)
	{
		if (($number < 0) || ($number > 999999999))
		{
		throw new Exception("Number is out of range");
		}
		
		$Gn = floor($number / 100000);  /* Millions (giga) */
		$number -= $Gn * 100000;
		$kn = floor($number / 1000);     /* Thousands (kilo) */
		$number -= $kn * 1000;
		$Hn = floor($number / 100);      /* Hundreds (hecto) */
		$number -= $Hn * 100;
		$Dn = floor($number / 10);       /* Tens (deca) */
		$n = $number % 10;               /* Ones */
		
		$res = "";
		
		if ($Gn)
		{
		   if($Gn >1){
		   $res .= $this->InWords($Gn) . " Lacs";
		   }else{
		   $res .= $this->InWords($Gn) . " Lac";
		   }
		}
		
		if ($kn)
		{
		$res .= (empty($res) ? "" : " ") .
		$this->InWords($kn) . " Thousand";
		}
		
		if ($Hn)
		{
		$res .= (empty($res) ? "" : " ") .
		$this->InWords($Hn) . " Hundred";
		}
		
		$ones = array("", "One", "Two", "Three", "Four", "Five", "Six",
		"Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen",
		"Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen",
		"Nineteen");
		$tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty",
		"Seventy", "Eigthy", "Ninety");
		
		if ($Dn || $n)
		{
		if (!empty($res))
		{
		$res .= " and ";
		}
		
		if ($Dn < 2)
		{
		$res .= $ones[$Dn * 10 + $n];
		}
		else
		{
		$res .= $tens[$Dn];
		
		if ($n)
		{
		$res .= "-" . $ones[$n];
		}
		}
		}
		
		if (empty($res))
		{
		$res = "zero";
		}
		return $res;
		
	}
	function previousDate($copy_date)
	{
		if ($copy_date) {
		$query = $this->db->query("SELECT DATE_SUB('$copy_date', INTERVAL 1 DAY) AS copy_date");
		return $query->row()->copy_date;
		}
	}
	function NextDate($copy_date)
	{
		if ($copy_date) {
		$query = $this->db->query("SELECT DATE_ADD('$copy_date', INTERVAL 1 DAY) AS copy_date");
		return $query->row()->copy_date;
		}
	}
	function dateDisplayFormat($input_date)
	{
		if ($input_date) {
			$query = $this->db->query("SELECT DATE_FORMAT( '$input_date', '%d %b %Y' ) AS ctc_date");				
			return $query->row()->ctc_date;
		}
	}

	function dateDisplayDayFormat($input_date)
	{
		if ($input_date) {
			$query = $this->db->query("SELECT DATE_FORMAT( '$input_date', '%a, %M %d, %Y') AS ctc_date");				
			return $query->row()->ctc_date;
		}
	}

	function dateDisplayDayMonthFormat($input_date)
	{
		if ($input_date) {
			$query = $this->db->query("SELECT DATE_FORMAT( '$input_date', '%d, %M %Y') AS ctc_date");				
			return $query->row()->ctc_date;
		}
	}
	function monthDisplayFormat($input_date)
	{
		if ($input_date) {
			$query = $this->db->query("SELECT DATE_FORMAT( '$input_date', '%M %Y' ) AS ctc_date");				
			return $query->row()->ctc_date;
		}
	}
	
    /*======End Common Function for pagination=======*/
   //End Class
}
