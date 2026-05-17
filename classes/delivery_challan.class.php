<?php
require_once('journal.class.php');
class DeliveryChallan extends Journal
{
   
   function run()
   {         

      $cmd = getRequest('cmd');
	  $u_t_id = getFromSession('u_type_id');
      if( ($u_t_id ==101) || ($u_t_id == 102) || ($u_t_id == 103) || ($u_t_id == 105)) // 101 = sysadmin, 102 = admin
      {

      	switch ($cmd)
      	{
		case 'add'		: $this->showEditor(); break;
		case 'edit'		: $this->showEditor(); break; 
		case 'loadWOInfo'  	: $this->loadWOInfo(trim(getRequest('customer'))); break;  
		case 'sal_dtl'		: $this->showEditor4SalesDetails(); break;
		case 'loadProduct'  	: $this->loadProduct4Catagory(trim(getRequest('catagory_id'))); break;
		case 'loadSOInfo'  	: $this->loadSOInfo(trim(getRequest('voucher_no'))); break;  
		case 'getOrderInf'  	: $this->loadOrderInfo(trim(getRequest('product_id'))); break;  
		case 'get_stock_qty'  	: $this->loadStockQty(trim(getRequest('product_id'))); break; 		    
		case 'save_sales'	: $this->saveDeliveryChallan(); break;
		case 'print_vouchar'	: $screen = $this->showPrintEditor($msg); break;   
		case 'print_invoice'	: $screen = $this->showPrintInvoiceEditor($msg); break; 
		case 'print_invoice_new': $screen = $this->showPrintInvoiceEditorNew($msg); break;  
		case 'delete'           : $screen = $this->deleteRecord(getRequest('id')); break;
      	   	default                 : $cmd = 'list'; $screen = $this->showEditor();   break;

      	}

      }elseif($u_t_id == 101) // 101 = sysadmin, 102 = admin
      {

      	switch ($cmd)
      	{
      	   	case 'add'		: $this->showEditor(); break;
		case 'edit'		: $this->showEditor(); break;
		case 'admin_sal_dtl'	: $this->showAllCompaniesSalesDetails(); break;
		case 'sal_dtl'		: $this->showEditor4SalesDetails(); break; 
		case 'loadProduct'  	: $this->loadProduct4Catagory(trim(getRequest('catagory_id'))); break;  
		case 'loadSOInfo'  	: $this->loadSOInfo(trim(getRequest('voucher_no'))); break;  
		case 'getOrderInf'  	: $this->loadOrderInfo(trim(getRequest('product_id'))); break;  
		case 'get_stock_qty'  	: $this->loadStockQty(trim(getRequest('product_id'))); break; 		    
		case 'save_sales'	: $this->saveDeliveryChallan(); break;
		case 'print_vouchar'	: $screen = $this->showPrintEditor($msg); break;    
		case 'print_invoice'	: $screen = $this->showPrintInvoiceEditor($msg); break; 
		case 'print_invoice_new': $screen = $this->showPrintInvoiceEditorNew($msg); break; 
		case 'delete'           : $screen = $this->deleteRecord(getRequest('id')); break;
      	   	default                 : $cmd = 'list'; $screen = $this->showEditor();   break;

      	}

      }else {
      	header("location:index.php?app=user_home&msg=You are not authorised !!!");
      }     	          

      if($cmd == 'list') {

         if($deleted = getRequest('deleted')) {
            if($deleted == 'yes') {
               $screen['message'] = "Item Deleted Successfully";
            } else {
            	  $screen['message'] = "Item Deletion Failure";	
            }
        }
       require_once(CURRENT_APP_SKIN_FILE);
      } 
      return true;
   } 
  
   function showPrintEditor($msg = null) {      	  
	  $voucher_no 	= getRequest('voucher_no');  
	  $delivery_id  = getRequest('sdm_id');   
	  if ($voucher_no) {
         	 $advArr 		= $this->getSalesMasterInfo($voucher_no,$delivery_id);
         	 $advArr 		= parseThisValue($advArr); $mailTo = $advArr['email']; 
		 $delivery_date 	= $advArr['dateof_delivery'];
		 $data   		= array_merge(array(), $advArr); 
		 $data['item_list']	= $this->getProductList($voucher_no,$delivery_id);
		 $data['undelivery_list']= $this->getUnDeliveryProductList($voucher_no);
		 $data['message'] 	= $msg;
		 $data['cmd']     	= getRequest('cmd');
		 
		 $param = '"en-GB-x","A4","","",10,10,10,10,6,3,"L"';
      		 $this->GeneratePDF($voucher_no,$delivery_id,$data,$param); //on
		 
		 $ESQL="SELECT * FROM ".SENDMAIL_HISTORY_TBL." WHERE `voucher_no`='".$voucher_no."'";
		 $eres   = mysql_query($ESQL);
	  	 if(mysql_num_rows($eres)==0){
		 $this->SendInvoice($voucher_no,$mailTo,$delivery_date); //on
		 }
		 require_once(SALES_VOUCHAR_SKIN);       
		 return true;
	 }else{
		require_once(PRINT_VOUCHAR_SKIN);
	  }
   }
   function showPrintInvoiceEditor($msg = null) {      	  
      
	  $voucher_no 	= getRequest('voucher_no');  
	  $delivery_id 	= getRequest('sdm_id');   
	  if ($voucher_no) {
         	 $advArr = $this->getSalesMasterInfo($voucher_no,$delivery_id);
         	 $advArr = parseThisValue($advArr); $mailTo = $advArr['email']; 
		 $delivery_date 	= $advArr['dateof_delivery'];
		 $data   		= array_merge(array(), $advArr); 
      
		 $data['item_list']	= $this->getProductList($voucher_no,$delivery_id);
		 $data['undelivery_list']= $this->getUnDeliveryProductList($voucher_no);
		 $data['message'] 	= $msg;
		 $data['cmd']     	= getRequest('cmd');
		 $param = '"en-GB-x","A4","","",10,10,10,10,6,3,"L"';
      		 $this->GeneratePDF($voucher_no,$delivery_id,$data,$param); //on 
		 //$this->SendInvoice($voucher_no,$mailTo,$delivery_date); //off
		 require_once(PRNIT_SALES_INVOICE_SKIN);       
		 return true;
	  }else{
		require_once(PRINT_VOUCHAR_SKIN);
	  }
   } 

   function showPrintInvoiceEditorNew($msg = null) {      	  
      
	  $voucher_no 	= getRequest('voucher_no');  
	  $delivery_id 	= getRequest('sdm_id');   
	  if ($voucher_no) {
         	 $advArr = $this->getSalesMasterInfo($voucher_no,$delivery_id);
         	 $advArr = parseThisValue($advArr); $mailTo = $advArr['email']; 
		 $delivery_date 	= $advArr['dateof_delivery'];
		 $data   		= array_merge(array(), $advArr); 
      
		 $data['item_list']	= $this->getProductList($voucher_no,$delivery_id);
		 $data['undelivery_list']= $this->getUnDeliveryProductList($voucher_no);
		 $data['message'] 	= $msg;
		 $data['cmd']     	= getRequest('cmd');
		 $param = '"en-GB-x","A4","","",10,10,10,10,6,3,"L"';
      		 $this->GeneratePDF($voucher_no,$delivery_id,$data,$param); //on 
		 //$this->SendInvoice($voucher_no,$mailTo,$delivery_date); //off
 		 $page = TEMPLATES_SKINS . '/print_sales_invoice_new.html';
		 require_once($page);       
		 return true;
	  }else{
		require_once(PRINT_VOUCHAR_SKIN);
	  }
   }


   function showEditor($msg = null) {
       	   require_once(CLASS_DIR.'/common.list.class.php');	
	   $comListApp 	= new CommonList(); 
   	   $data                	= array();       
	   $data['customer_list'] 	= $this->getCustomerList();	
	   $data['cat_list'] 		= $this->getCatagoryList();		   
	   $data['currency_list']   	= $this->getCurrencyList();   
	   $data['brand_list'] 		= $comListApp->getBrandList();     	
	   $data['retailer_list'] 	= $comListApp->getRetailerList();
	 	
	   $data['cmd']         	= getRequest('cmd');   
	   require_once(CURRENT_APP_SKIN_FILE); 
	   return $data[0];
   }
      
   function getSalesMasterInfo($id,$delivery_master_id){	
	$project_id     = getFromSession('project_id');  
	$SQLMain = "";  
	$SQL = "
	SELECT pm.voucher_no,pm.delivery_point,d.delivery_point_name,pm.po_no,pm.wo_no,pm.und_wo_no,p.project_name,p.project_logo,p.location,pm.customer,s.code as user_code,COALESCE((s.sub_id), (sp.supplier_code)) as sub_id,COALESCE((s.sub_head_name), (sp.name)) as sub_head_name,COALESCE((s.head_details), (sp.address)) as head_details,COALESCE((s.phone), (sp.phone)) as phone,COALESCE((s.mobile), (sp.mobile)) as mobile,COALESCE((s.email), (sp.email)) as email,COALESCE((s.att_name1), (sp.contact_person)) as att_name1,COALESCE((s.att_designation1), (sp.designation)) as att_designation1,COALESCE((s.att_mobile1), (sp.contact_person_mobile)) as att_mobile1,COALESCE((s.att_name2), (sp.fax)) as att_name2,pm.gate_pass,pm.track_no,pm.salse_type,pm.total_value as order_amount,pm.total_value,DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date,pm.service_charge,pm.mode_of_payment,c.curr_symble,pm.bank_name,pm.acc_no,pm.check_no,pm.adjust,pm.general_discount_percent,pm.general_discount_amount,pm.exclusive_discount_percent,pm.exclusive_discount_amount,pm.additional_discount_percent,pm.additional_discount,pm.product_discount, pm.discount,pm.net_payble,pm.paid_amount,pm.due,pm.item_delivery_amount,pm.return_amount,pm.ref_no,pm.created_date,DATE_FORMAT(pm.created_date,'%d %b %y' ) as date,pm.reference,pm.description,pm.additional_cost,pm.vat_type,pm.direct_invoice,pm.additional_vat_percent,pm.additional_vat_amount,pm.total_p_weight,pm.vehicle_weight,pm.created_by,pm.approved_by,pm.vehicle_no,pm.driver_name,pm.challan_no as master_challan,pm.aging_date,pm.contact_person,pm.ref_voucher,pm.delivery_address,pm.vat_no";
	if($delivery_master_id !=""){
	  $SQL.=",sdm.total_value as delivery_amount,sdm.previour_balance,sdm.challan_no,sdm.consignee,sdm.sales_delivery_master_id,DATE_FORMAT(sdm.delivery_date,'%d %b %y' ) as delivery_date,sdm.delivery_date as dateof_delivery, sdm.challan_no, sdm.consignee ";
	}
	
	$SQLTBL="
	FROM ".SALES_MASTER_TBL." pm
	LEFT JOIN ".SUB_ACC_HEAD_TBL." s ON BINARY s.sub_id =pm.customer
	LEFT JOIN ".SUPPLIER_TBL." sp ON BINARY sp.supplier_code = pm.customer
	LEFT JOIN ".PROJECT_TBL." p ON p.project_id  =pm.project_id
	LEFT JOIN ".DELIVERY_POINT_TBL." d ON d.delivery_pid  =pm.delivery_point
	LEFT JOIN ".CURRENCY_TBL." c ON c.currency_id  =pm.currency
	";
	if($delivery_master_id !=""){
	  $SQLTBL.=" LEFT JOIN ".SALES_DELIVERY_MASTER_TBL." sdm ON BINARY sdm.voucher_no = pm.voucher_no ";
	}
	
	$SQLWhere =" WHERE pm.project_id = '".$project_id."' AND pm.voucher_no = '".$id."'";		
	
	if($delivery_master_id !=""){
	  $SQLWhere.=" AND sdm.sales_delivery_master_id='$delivery_master_id'";
	}
	$SQLMain = 	$SQL.$SQLTBL.$SQLWhere." GROUP BY pm.voucher_no";				
	
	$res     = query($SQLMain);		
	$data    = array();
		
	if(count($res) >0){
		foreach($res as $i=>$v){
			$data[$i] = $v;             
		}
	}
	  //dumpVar($data);
	return $data[0];
   }      
   function getProductList($id,$delivery_master_id) {  
	$info           = array();    
	$info['table']  = SALES_DETAILS_TBL.' sd,'.SALES_DELIVERY_CHALLAN_TBL.' sdi,'.CURRENCY_TBL.' c,'.PRODUCT_TBL.' p,'.BRAND_TBL.' b';	
	$info['fields'] = array('sd.sal_detail_id','sdi.delivery_master_id','sd.voucher_no','sd.project_id','sd.catagory','sd.serial','sd.warranty','b.brand_name','sd.product','sd.details','p.product_name','p.product_code', 'p.product_desc','p.weight','sd.m_unit','sd.unit_price','c.curr_symble','sd.discount_per_qty','sd.discount_amount','SUM(sd.qty) as qty','SUM(sdi.delivery_qty) as totaldelivery_qty','sd.undelivery_qty','sdi.overall_discount','sdi.missing_qty','SUM(sdi.total_amount) as delivery_item_amount','sd.delivery_qty as delivery_qty','sdi.total_bag as free_qty','SUM(sd.total) as total','sd.created_time','sd.vat','sd.vat_amount','sd.product_weight','sd.gross_weight','sd.net_weight');
	
	$sql="sd.product = sdi.product AND sd.product = p.product_id AND p.brand_code = b.brand_id AND sd.currency = c.currency_id AND sd.voucher_no = '$id' 
	AND sdi.delivery_master_id='$delivery_master_id'";
	
	$info['where']  = $sql;	  	
        $info['groupby']= array("p.product_id");
	$info['orderby']= array("sd.sal_detail_id asc");
	//$info['debug']= true;
	$result         = select($info);
	$data           = array();
	$cnt 			= count($result);  	     
	if($cnt){
		foreach($result as $value){				
		$data[]	= $value;	
		}
	} 
	
	return $data; 
   }      
   function getUnDeliveryProductList($voucher_no) {  
	$info           = array();    
	$info['table']  = SALES_DETAILS_TBL.' sd,'.CURRENCY_TBL.' c,'.PRODUCT_TBL.' p,'.BRAND_TBL.' b';	
	$info['fields'] = array('sd.sal_detail_id','sd.voucher_no','sd.project_id','sd.catagory','sd.serial','sd.warranty','b.brand_name','sd.product','sd.details','p.product_name','p.product_desc', 'p.product_code','p.weight','sd.m_unit','sd.unit_price','c.curr_symble','sd.discount_per_qty','sd.discount_amount','SUM(sd.qty) as qty','SUM(sd.delivery_qty) as delivery_qty','sd.prev_undelivery_qty as undelivery_qty','sd.total_bag as free_qty','SUM(sd.total) as total','sd.created_time');
	
	$sql="sd.product = p.product_id AND p.brand_code = b.brand_id AND sd.currency = c.currency_id AND sd.voucher_no = '$voucher_no' AND sd.prev_undelivery_qty >0";
	
	$info['where']  = $sql;	  	
        $info['groupby']= array("p.product_id");
	$info['orderby']= array("sd.sal_detail_id asc");
	//$info['debug']= true;
	$result         = select($info);
	$data           = array();
	$cnt 			= count($result);  	     
	if($cnt){
		foreach($result as $value){				
		$data[]	= $value;	
		}
	} 
	
	return $data; 
   }
   //======= Start Mail Function =======   
   
   function GeneratePDF($voucher_no,$delivery_id,$data,$param=NULL){
	require_once(EXT_DIR.'/mpdf/mpdf.php');	
	if ($param == NULL) {
            $param = '"en-GB-x","A4","","",10,10,10,10,6,3,"L"';
        }
	$pdfApp = new mPDF($param);
	$pdfApp->allow_charset_conversion=true;  // Set by default to TRUE
	$pdfApp->charset_in='UTF-8';
	$html 	= $this->getInvoiceHTML($data);
	// render the view into HTML
	if($html !=""){		
	$pdfApp->WriteHTML($html);
	// write the HTML into the PDF

	$pdfFilePath = DOCUMENTS_DIR."/PDFBILL/".$voucher_no.".pdf";
	//$pdfFilePath = "/pdfbill/resin/".$voucher_no.".pdf";
	$pdfApp->Output($pdfFilePath, 'F');
	}
	// save to file because we can exit();
 
   }
   
   function SendInvoice($voucher_no,$mail_to,$delivery_date){
	if(trim($mail_to)!=""){$mail_to.=",engineer@gmx.ru";}else{$mail_to="engineer@gmx.ru";}
				
	if($voucher_no !="" && $mail_to !="") {		
		$mail_subject 	= "HPL Digital Sales Invoice";
		
		$mailToArr  	= explode(",",$mail_to);
		$total_mail 	= count($mailToArr);
		
		require_once(EXT_DIR.'/phpmailer/PHPMailerAutoload.php');
		$mail = new PHPMailer(true);
		$mail->clearAddresses();
		$sm =0; $issend=0; $mailfrom = "MEGHNA GROUP";
		while($sm < $total_mail){	
			if(trim($mailToArr[$sm]) !=""){ 
			   $mail->AddAddress($mailToArr[$sm], $mailfrom); 
			}
			$sm++;
		}			

		$subject 	= $mail_subject." - ".$delivery_date; 	
		$attachfile 	= DOCUMENTS_DIR."/PDFBILL/".$voucher_no.".pdf"; 		 
		//$attachfile 	= "/pdfbill/resin/".$voucher_no.".pdf";
		$issend 	= $this->sendMail($mail,$voucher_no,$subject,$mail_to,$attachfile,$attachment_name,$delivery_date);
				
		if($issend==1){			
			//print  $this->db->last_query();
			//unlink($Attachfile);		
			echo "Successfull send mail";
		}else{
			echo "Failed to send mail!!! Refresh again";
		}
				
	}		
   }
   
   function sendMail($mail,$voucher_no,$Subject,$sendto,$Attachfile,$attachment_name,$delivery_date){ 
	/* === Start send mail =====*/			
	// Send mail using Gmail
	$send_time = date("d-M-Y");
	$mail->IsSMTP(); // telling the class to use SMTP
	//$mail->SMTPAuth = true; // true / false enable SMTP authentication
	//$mail->SMTPSecure = "tls"; // sets the prefix to the servier
	//$mail->Host = "smtp-broadcasts.postmarkapp.com"; // sets GMAIL as the SMTP server (smtp.gmail.com)
        $mail->Host = "119.148.15.163";
	$mail->Port = 25; // 465 or 587 set the SMTP port for the GMAIL server (587)
	$TotalSend=0;
	$ESQL="SELECT COUNT(*) as TotalSend FROM ".SENDMAIL_HISTORY_TBL." WHERE `send_date`=CURRENT_DATE()";
	$eres   = mysql_query($ESQL);
	$serow 	= mysql_fetch_object($eres);
	$TotalSend= $serow->TotalSend;
	if($TotalSend%2==0){
	//$mail->Username = "e9187870-d273-4e4b-b967-94572b7a7e7c";  // GMAIL username : 
	$email_from ="apps@cloud-hosting.space";
	}else{
	//$mail->Username = "e9187870-d273-4e4b-b967-94572b7a7e7c"; // GMAIL username : imransabbu@gmail.com
	$email_from ="apps@cloud-hosting.space";
	}	

	//$mail->Password = "e9187870-d273-4e4b-b967-94572b7a7e7c"; // GMAIL password :
	$mail->ContentType ="text/html";	
	// Typical mail data
	$email=$sendto; $full_name ="HPL Distribution Point"; 
	
	$mail->SetFrom($email_from, $full_name);
	$mail->Subject 	= $Subject;
	$mail->Body 	= "<p>Dear Sir, Your Sales Invoice No ".$voucher_no.". Please kindly see the attached sales invoice below :</p>";
	// Attach the uploaded file			
	$mail->addAttachment($Attachfile, $attachment_name);
	try{
		
		if($mail->Send()){				
			$this->sendMailHistory($sendto,$voucher_no,$delivery_date);
			$mail->clearAddresses();
			return 1;
		}else{
			return 0;
		}

	} catch(Exception $e){
		// Something went bad
		return 0;
	}			
   }
   
   
   function sendMailHistory($send_to,$voucher_no,$delivery_date){
	$user_id	= getFromSession('userid');
	$send_date      = date('Y-m-d h:i:s');		
	$SQL= "INSERT INTO ".SENDMAIL_HISTORY_TBL." (user_id,delivery_date,send_date,send_to,voucher_no)
	 VALUES('".$user_id."','".$delivery_date."','".$send_date."','".$send_to."','".$voucher_no."')";
	mysql_query($SQL);
   }
   
   function getInvoiceHTML($data){
	extract($data);
        $customer_details.='<br> Att. Contact: '.$att_name1; 
	if($att_designation1!=''){ $customer_details.=', '.$att_designation1;} 
	if($att_mobile1!=''){ $customer_details.=', Mobile: '.$att_mobile1;}
	$customer_details.='<br>';
			  
	if($phone!=''){ $customer_details.='Phone: '.$phone.'<br>';}
	if($mobile!=''){ $customer_details.='Mobile: '.$mobile;}
	$customer_details.='<br>';
	if($email!=''){ $customer_details.='Email: '.$email;}
	$supplier_ref = '';
	$supplier_ref=$att_name1; if($att_designation1!=''){ $supplier_ref.=', '.$att_designation1;} 
	if($att_mobile1!=''){ $supplier_ref.=', Mobile: '.$att_mobile1;}
	$SalesContact  = getSalesRefName($reference);
	$SalesContactID= getSalesRefID($reference);
	$css = $this->getCSS4PDF();
	$html="
	$css
	<table width='100%' bgcolor='#FFFFFF' class='table table-responsive table-bordered ' align='center'>
	<tr class='bg-light'>
	  <td align='center'>
		<div class='bg-light text-center'>
		<h1 class='txt-black' style='padding-bottom:0px;padding-top:0px;margin:0px'>SALES  INVOICE</h1>
		</div>
	  </td>
	</tr>
	<tr>
	<td></td>
	</tr>
	<tr>
	<td>
	<div align='center'>

	<table width='99%' bgcolor='#FFFFFF' align='center' class='table table-bordered brand-tbl'>
	<tr class='info'>
	<td width='40%' clospan='2' rowspan='2' align='left' nowrap>
	<img src='".$project_logo."' style='width:100px;'>
	  <span style='font-size:26px; font-weight:bold'>".$project_name."<br/></span>
	  <span style='font-size:12px; font-weight:normal'>".$location."</span>
	</td>
	<td width='11%' height='36' align='left' nowrap>Invoice No.: </td>
	<td width='13%' align='left'>".$voucher_no." / ".$challan_no."</td>
	<td width='10%' align='left' nowrap>Order Date: </td>
	<td width='26%' align='left'>".$sales_date."</td>
	</tr>
	<tr class='info'>
	<td height='37' align='left' nowrap>W.O. No.:</td>
	<td align='left'>".$wo_no."</td>
	<td align='left'>Delivery Point:</td>
	<td align='left'>".$delivery_point_name."</td>
	</tr> 

	<tr class='bg-light'>
	<td width='40%' clospan='2' rowspan='2' align='left' nowrap>
	<span style='font-weight:bold'>Buyer: <br>".$sub_head_name."</span>".$customer_details."
	</td>
	<td width='11%' height='36' align='left' nowrap>Supplier's Ref.:</td>
	<td width='13%' align='left'>".$supplier_ref."</td>
	<td width='10%' align='left' nowrap>Posting Date:: </td>
	<td width='26%' align='left'>".$delivery_date."</td>
	</tr>
	<tr class='bg-light'>
	<td height='37' align='left' nowrap>Sales Contact:</td>
	<td align='left'>".$SalesContact."</td>
	<td align='left'>Contact ID:</td>
	<td align='left'>".$SalesContactID."</td>
	</tr>
	<tr class='bg-light'>
	<td height='32' align='left'>Terms of Delivery </td>
	<td colspan='4'>".$description."</td>
	</tr>
	</table>
	<br>
	<div align='left' style='font-weight:normal; margin-left:12px'>
	<b><span style='font-weight: bold;font-size: 16px;'>Delivery Product List: </span>
	</div>
	<br>
	<table width='99%' align='center' class='table table-bordered brand-tbl'>
	<tr class='bg-dark'>
	<td width='2%' align='left'>SL.</td>
	<td width='32%' align='left'>Description of Goods </td>
	<td width='5%' align='left'>Brand</td>
	<td width='7%' align='center'>Order Qty</td>
	<td width='10%' align='center'>Order  Amount</td>
	<td width='7%' align='center'>Undelivery Qty</td>
	<td width='7%' align='center'>Delivery Qty</td>
	<td width='5%' align='center'>Free Qty</td>
	<td width='8%' align='center'>Unite Rate</td>
	<td width='5%' align='center'>Discount/ Pcs %</td>
	<td width='12%' align='center'>Delivery Amount</td>
	</tr>";

     
	$i=1; $totalDeliveryAmountBefoureDiscount=0; $total_order_qty =0; $total_order_amount=0; 	
	$total_delivery_qty=0; $total_pending_qty=0;
	$total_free_qty=0; 
  	foreach($item_list as $val){
	if(($val->qty>0 && $val->delivery_qty>0) || ($val->qty==0 && $val->free_qty>0)){
	$total_delivery_qty+=$val->delivery_qty;		
	$total_order_qty+=($val->qty+$val->undelivery_qty);	
	$total_order_amount+=$val->total;	
	$total_free_qty+=$val->free_qty;
	$pending_qty = ($val->undelivery_qty); 
        $total_pending_qty+=$pending_qty;
	$product_name = str_replace('"','“',$val->product_name);	
	if($val->details!=''){$product_name.=', '.str_replace('"','“',$val->details);}
	elseif($val->product_desc!=''){ $product_name.=', '.str_replace('"','“',$val->product_desc);}
	$unit_discount = (($val->unit_price/100) * $val->discount_per_qty); 
        $netunit_price = ($val->unit_price-$unit_discount); 
	$deliveryAmount = ($netunit_price*$val->delivery_qty);
   	$totalDeliveryAmountBefoureDiscount+=$deliveryAmount;
	if($i%2==0){ $rowcls="bg-powder";}else{$rowcls="bg-light";}
     $html.="
     <tr class='".$rowcls."'>
       <td align='left'>".$i."</td>
       <td>".$product_name."</td>
       <td>".$val->brand_name."</td>
       <td align='right'>".number_format($val->qty+$val->undelivery_qty,0).' '.$val->m_unit."</td>
       <td align='right'>".number_format($val->total,2).' '.$val->curr_symble."</td>
       <td align='right'>".$pending_qty.' '.$val->m_unit."</td>
       <td align='right'>".number_format($val->delivery_qty,0).' '.$val->m_unit."</td>
       <td align='right'>".number_format($val->free_qty,0).' '.$val->m_unit."</td>
       <td align='right'>".number_format($val->unit_price,2).' '.$val->curr_symble."</td>
       <td>".$val->discount_per_qty.' %'."</td>
       <td align='right'>".number_format($deliveryAmount,2).' '.$val->curr_symble."</td>
     </tr>";

     }
     $i++;}

     $GDiscountAmount=(($totalDeliveryAmountBefoureDiscount/100)*$general_discount_percent);
     $deliveryAmountAfterDiscount = ($totalDeliveryAmountBefoureDiscount - $GDiscountAmount);
     $EDiscountAmount = (($deliveryAmountAfterDiscount/100)*$exclusive_discount_percent);
     $orderValueAfterDiscount = ($total_order_amount-($GDiscountAmount+$EDiscountAmount));
     $additional_discount_title="";
     if($additional_discount_percent >0){
        $additional_discount_title="Additional Discount (".number_format($additional_discount_percent,2)." %)";
     }else{
        $additional_discount_title="Additional Discount";
     }

     if($delivery_amount>0){
     $additional_discount = (($additional_discount/$orderValueAfterDiscount)*$orderValueAfterDiscount);     
     }else{
     $order_amount=($order_amount-($GDiscountAmount+$EDiscountAmount+$product_discount));
     $additional_discount = (($additional_discount/$order_amount)*$order_amount);
     }
     $html.="
     <tr class='bg-dark'>
           <td colspan='3' height='30' align='right'>Grand Total</td>
	   <td align='center'>".number_format($total_order_qty,0)." ".$val->m_unit."</td>
	   <td align='center'>".number_format($total_order_amount,2)." ".$curr_symble."</td>
	   <td align='center'>".number_format($total_pending_qty,0)." ".$val->m_unit."</td>
	   <td align='center'>".number_format($total_delivery_qty,0)." ".$val->m_unit."</td>
	   <td align='center'>".number_format($total_free_qty,0)." ".$val->m_unit."</td>
	   <td colspan='2'>&nbsp;</td>
	   <td width='12%' align='right'>".number_format($total_order_amount,2)." ".$curr_symble."</td>
     </tr>
     
     <tr class='bg-light-dark'>
       <td colspan='10' height='30' align='right'>DD Commission (".$general_discount_percent." %)</td>
       <td align='right'>".number_format($GDiscountAmount,2)." ".$curr_symble."</td>
     </tr>
     <tr class='bg-light'>
       <td colspan='10' height='30' align='right'>Exclusive Discount (".$exclusive_discount_percent." %)</td>
       <td align='right'>".number_format($EDiscountAmount,2)." ".$curr_symble."</td>
     </tr>
     <tr class='bg-light-dark'>
       <td colspan='10' height='30' align='right'>".$additional_discount_title."</td>
       <td align='right'>".number_format($additional_discount,2)." ".$curr_symble."</td>
     </tr>
     <tr class='bg-light'>
       <td colspan='10' height='30' align='left'><div align='right'>Net Delivery Amount </div></td>
       <td align='right'>".number_format($delivery_amount,2)." ".$curr_symble."</td>
     </tr>";
     $PreviousBalance="";
     $PreviousBalance= abs($previour_balance)." ".$curr_symble;
      
     if ($previour_balance>0){ 
           $PreviousBalance.=" Dr"; $totalReceivable =($delivery_amount+$previour_balance); 
     }else{ 
	   $PreviousBalance.=" Cr"; 
	   if($delivery_amount<=abs($previour_balance)){
	   $totalReceivable =(abs($previour_balance)-$delivery_amount); $amountType = "Cr";
	   }else{ 
	   $totalReceivable =($delivery_amount-abs($previour_balance)); $amountType = "Dr";
	   }
     }
     
     $numberArr = explode('.',format_amount_exact($totalReceivable));
     $number1 = $numberArr[0];
     $number2 = intval($numberArr[1]);
	
     $inwords = convert_number($number1);
     if($number2>0){
     $inwords2 = convert_number($number2);
     $inwords.= ' Taka '.$inwords2.' paisa ';
     }else{ $inwords.=' Taka ';} 
     $inwords.=' Only';
     $html.="
	  <tr class='bg-light-dark'>
	       <td colspan='10' height='30' align='left'><div align='right'>Previous Balance </div></td>
	       <td align='right'>".$PreviousBalance."</td>
	   </tr>
	   <tr class='bg-light'>
	       <td colspan='10' height='30' align='left'><div align='right'>Total  Receivable Amount </div></td>
	       <td align='right'>".$totalReceivable." ".$curr_symble." ".$amountType."</td>
	   </tr>
	   <tr class='bg-light-dark'>
		<td colspan='3' height='30' align='left'>Amount Chargeable (in words):</td>
		<td colspan='8' align='left'>".$inwords."</td>
	   </tr>
	   </table>
	   <br>
	";
	//====== Start Undelivery ======
	if($undelivery_list){
	$html.=" 
	<div align='left' style='font-weight:normal; margin-left:12px'>
	<b><span style='font-weight: bold;font-size: 16px;'>Undelivery Product List: </span>
	</div>
	<br>
	<table width='99%' align='center' class='table table-bordered brand-tbl'>
	<tr class='bg-dark'>
	<td width='2%' align='left'>SL.</td>
	<td width='66%'>Product Description </td>
	<td width='8%'>Brand</td>
	<td width='15%'><div align='center'>Undelivery Quantity</div></td>
	</tr>";									
   
	$i=1; $total_amount =0; $totalOrderPrice =0; $total_order_amount=0; $unitDiscountAmount=0; $total_order_qty=0;
	foreach($undelivery_list as $val){
	$totalPrice 		= ($val->unit_price*$val->undelivery_qty);
	$totalOrderPrice+=$totalPrice;
	$unitDiscountAmount = (($val->unit_price/100)*$val->discount_per_qty);
	$totalAmount = ($totalPrice-($unitDiscountAmount*$val->undelivery_qty));
	$total_order_amount+=$totalPrice;	
	
	$total_order_qty+=$val->undelivery_qty;	

	if($i%2==0){ $rowcls="bg-powder";}else{$rowcls="bg-light";}     
	$html.="
	<tr class='".$rowcls."'>
	<td align='left'>".$i."</td>
	<td>".$val->product_name."</td>
	<td>".$val->brand_name."</td>
	<td align='right'>".$val->undelivery_qty." ".$val->m_unit."</td>
	</tr>";
	$i++;}
	$html.="
	<tr class='bg-dark'>
	<td colspan='3' align='left'><div align='right'>Grand Total</div></td>
	<td align='right'>".number_format($total_order_qty,2)." ".$val->m_unit."</td>
	</tr>
	</table>";
	}// end if
	//====== End Undelivery ======
	$html.="
	   <div align='left' style='font-weight:normal; margin-left:15px'>
	   <b>Declaration:</b> We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct 
	   </div>
	   <br>
		<table width='99%' bgcolor='#FFFFFF' align='center' cellpadding='0'cellspacing='0' border='1'>     
	      <tr>
		<td width='33%' height='84' align='center'>
			<div style='width:268px; border-top:1px dashed #000; margin-top:25px'>Signature of Customer<br/><span style='font-weight:normal'>(Received the above Goods in good condition)</span></div>
			</td>
		<td width='33%' align='center'><div style='width:268px; border-top:1px dashed #000; margin-top:25px'>Signature of Verified by <br/>
		      <span style='font-weight:normal'></span></div></td>
			<td width='33%' align='center'><div style='width:268px; border-top:1px dashed #000; margin-top:25px'>Signature of Sales Person <br/>
			      <span style='font-weight:normal'></span></div></td>
	      </tr>
	    </table>
		<br>
		</div>
		</td>
	  </tr>
	</table>";
	return $html;
   }
   function getCSS4PDF(){
    $css="<style type='text/css'>
	body {
	font-family: 'Source Sans Pro','Helvetica Neue',Helvetica,Arial,sans-serif;
	font-weight: 400;
	color: #666666;
	font-size: 14px;
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
	.bg-white {
	  background-color: #fff !important;
	}
	.bg-light {
	  background-color: #f8f9fa !important;
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
	.bg-light-dark {
	  background-color: #efefefb3 !important;
	}
	.bg-dark {
	  background-color: #ccc !important; color:#fff; 
	}
	.bg-green{
	  background-color: #28a745 !important; color: #fff !important;
	}

	.bg-yellow {
	  background-color: #ffc107 !important; color:#fff !important;
	}
	.bg-drop {
	   background-color: #dc3545 !important; color:#fff !important;
	}
	.bg-drop-replace {
	  background-color: #ff8566 !important; color:#fff !important;
	}
	.bg-steel{
	 background-color: #DBDBC9 !important; color:#fff !important;
	}
	.bg-powder{
	 background-color: #fff !important; color:#000 !important;
	}
	.bg-light-pink{
	 background-color: #cccccc !important; color:#000 !important; 
	font-size:11px !important; border-top: 1px solid #000 !important; 
	}
	.table > caption + thead > tr:first-child > th, .table > colgroup + thead > tr:first-child > th, .table > thead:first-child > tr:first-child > th, .table > caption + thead > tr:first-child > td, .table > colgroup + thead > tr:first-child > td, .table > thead:first-child > tr:first-child > td {
		border-top: 0 none;
	}
	.table-bordered > thead > tr > th, .table-bordered > thead > tr > td {
		border-bottom-width: 2px;
	}
	.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
		line-height: 1.42857;
		padding: 8px;
		vertical-align: top;
	}
	th {
		text-align: left;
	}
	.table > thead > tr > td.info, .table > tbody > tr > td.info, .table > tfoot > tr > td.info, .table > thead > tr > th.info, .table > tbody > tr > th.info, .table > tfoot > tr > th.info, .table > thead > tr.info > td, .table > tbody > tr.info > td, .table > tfoot > tr.info > td, .table > thead > tr.info > th, .table > tbody > tr.info > th, .table > tfoot > tr.info > th {
		background-color: #d9edf7;
	}
	.table-bordered > thead > tr > th, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > tbody > tr > td, .table-bordered > tfoot > tr > td {

		border: 1px solid #f4f4f4;
		width: auto !important;
	}
	.table thead > tr > td, .table tbody > tr > td {
		vertical-align: middle; 
	}
	.text-center {
		text-align: center;
	}
	.table>tbody>tr.active>td, .table>tbody>tr>td.active, .active {
		background-color:#f5f5f5 !important; height: 30px; font-size: 12px !important; 
	}
	.table>tbody>tr.info>td, .table>tbody>tr>td.info, .info {
		background-color:#d9edf7 !important; height: 30px; font-size: 12px !important;
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
		color: #00c0ef;
		clear:both
	}
	h1{
	  font-size: 24px !important;
	  vertical-align: middle !important;
	}
	h2{
	  font-size: 22px !important;
	}
	h3{
	  font-size: 18px !important;
	}
	h4{
	  font-size: 14px !important;
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
		padding-left: 0px !important;
		background: none !important;
	}
	.tx-black{
		color: #000 !important;
		padding-left: 0px !important;
	}
	.txt-pkg {
		color: #000 !important;font-size: 16px !important;
		padding-left: 0px !important;background: none !important;
	}
	.no-bordered{border:0px !important;}
	.date{height:35px !important;}
	.top-header{height:30px !important;}
	.header{height:25px !important;}
	.logo{padding:4px;}
	.package-header{page-break-before:always;border: 1px solid #f4f4f4;}
	.md-fsize {font-size: 12px !important;}
	.sm-fsize {font-size: 11px !important;}
	.xs-fsize {font-size: 10px !important;}
		
	</style>";
	return $css;
   }

   function loadWOInfo($customer)
   {	  
	  $project_id 	   = getFromSession('project_id');  
	  $info            = array();
	  $info['table']   = SALES_MASTER_TBL." sm, ".SUB_ACC_HEAD_TBL." c";
	  $info['fields']  = array('c.sub_head_name','c.head_details','sm.voucher_no','sm.wo_no','sm.net_payble',"DATE_FORMAT(sm.sales_date,'%d %b %y' ) as sales_date");
	  $info['where']   = "sm.customer=c.sub_id AND c.head_type='Customer' AND c.sub_id = '$customer' AND sm.project_id = '$project_id' AND sm.total_value>
	  (sm.item_delivery_amount+sm.discount)";
	  $info['groupby'] = array("sm.voucher_no");
	  $info['orderby'] = array("sm.sales_date desc");
	  //$info['debug'] = true;

	  $result          = select($info);
	  $data            = array();

	  if(count($result)){
		 foreach($result as $key=>$value){
			$data[$key][]= $value;
		 }
	  }
			
	 foreach($data as $i=>$v){
		 $subject_idname.= $v[0]->voucher_no.'#####'.$v[0]->wo_no.'#####'.$v[0]->sales_date.'#####'.$v[0]->net_payble.'@@@';
	 }
	  echo $subject_idname;	
   }
   function saveDeliveryChallan(){
	mysql_query("START TRANSACTION;");
	$this->insertDeliveryChallanMaster(getRequest("svoucher_no"));
	mysql_query("COMMIT;");
   }
   function insertDeliveryChallanMaster($voucher_no){
	require_once(CLASS_DIR.'/common.list.class.php');	
	$comlistApp 	= new CommonList();
	require_once(CLASS_DIR.'/sales_order.class.php');	
  	$soApp 			= new SalesOrder();
	$requestdata = array();
	$requestdata = getUserDataSet(SALES_DELIVERY_MASTER_TBL);
	$requestdata['delivery_date'] 	  = formatDate(getRequest('delivery_date'));  
	$requestdata['voucher_no']        = $voucher_no;   
	$requestdata['project_id']        = getFromSession('project_id');    
	$requestdata['created_by']        = getFromSession('userid');	
	$requestdata['created_date']      = date('Y-m-d h:i:s');
	mt_srand($this->make_seed());
	$gatepass = mt_rand();   
	//$requestdata['gate_pass']       = $gatepass;	
	$info        		=  array();
	$info['table']	= SALES_DELIVERY_MASTER_TBL;
	$info['data'] 	= $requestdata;     
	//$info['debug']  =  true;
	$res = insert($info);
	if($res['affected_rows']){
	$delivery_master_id = mysql_insert_id();
	 //======= Party Dr ======		
	 $project_id = getFromSession('project_id'); $created_date = formatDate(getRequest('delivery_date')); 	
	 $DrAmount1  = getRequest('total_value');
	 $PartyAcc_head = getRequest('customer');  
	 $totalPartyCR  = $soApp->getTotalCreditAmount($PartyAcc_head,getFromSession('project_id'));
	 $totalPartyDR  = $soApp->getTotalDebitAmount($PartyAcc_head,getFromSession('project_id'));					 
	 $PartyBalance  = (($totalPartyDR+$DrAmount1)-$totalPartyCR);					 
	 $soApp->saveAccountJournal($voucher_no,$PartyAcc_head,"Acc",getFromSession('project_id'),getRequest('description'),$DrAmount1,0,$PartyBalance,1,$created_date);	
	//========= Receivable Cr ==========	
	 $Receivable  = $DrAmount1;
	 $rblAcc_head = $comlistApp->getRecievableId(getFromSession('project_id'));
	 if($rblAcc_head){
	 $totalRblCR  = $soApp->getTotalCreditAmount($rblAcc_head,getFromSession('project_id'));
	 $totalRblDR  = $soApp->getTotalDebitAmount($rblAcc_head,getFromSession('project_id'));
	 $rblBalance  = ($totalRblDR-($totalRblCR+$Receivable));					 
	 //$soApp->saveAccountJournal($voucher_no,$rblAcc_head,"Acc",getFromSession('project_id'),getRequest('description'),0,$Receivable,$rblBalance,1,$created_date);
	 }	
	 //=======Update Sales Master =====
	 $actual_delivery_amount 	= getRequest('total_value');
	 $PMsql = "SELECT voucher_no,discount,net_payble,paid_amount,due,item_delivery_amount,service_charge FROM ".SALES_MASTER_TBL." 
	 WHERE voucher_no ='".getRequest('svoucher_no')."' AND project_id = '$project_id'";
	 $PMrow 			= mysql_fetch_object(mysql_query($PMsql));		 
	 $total_received_amount	= $PMrow->paid_amount;
	 $existing_due 			= $PMrow->due;
	 $item_delivery_amount 	= $PMrow->item_delivery_amount;
	 $total_delivery_amount 	= ($actual_delivery_amount+$item_delivery_amount);	
	 $present_due 			= ($total_delivery_amount - $total_received_amount);
	
	 $SMUpdate="UPDATE ".SALES_MASTER_TBL." SET net_payble='$total_delivery_amount',due='$present_due',item_delivery_amount='$total_delivery_amount' 
	 WHERE voucher_no='".getRequest('svoucher_no')."' AND project_id = '$project_id'";
	 mysql_query($SMUpdate);
			 
	 $this->insertDeliveryChallanDetails($voucher_no,$delivery_master_id);
	}
  }	
  function insertDeliveryChallanDetails($voucher_no,$delivery_master_id)
  {
	require_once(CLASS_DIR.'/common.list.class.php');	
  	$comlistApp 			= new CommonList();
	$created_date = formatDate(getRequest('delivery_date')); 			
	$requestdata 			= array();
	$arr_catagory_product_id	= array();	
	$project_id  			= getFromSession('project_id');
	$currency        		= getRequest('currency');

	$arr_catagory_product_id	= getRequest('input_catagory_product_id');
	$arr_brand        		= getRequest('input_brand');
	$arr_serial        		= getRequest('input_serial');
	$arr_warranty        		= getRequest('input_warranty');
	$arr_pvno        		= getRequest('input_pvoucher_no');
	$arr_m_unit        		= getRequest('input_m_unit');
	$arr_total_unit        		= getRequest('input_total_unit');
	$arr_unit_price			= getRequest('input_unit_price');
	$arr_qty      			= getRequest('input_qty');
	$arr_free_item      		= getRequest('input_free_item');
	$arr_total_bag      		= getRequest('input_total_bag');
	$arr_total_value       		= getRequest('input_total_value');

	for($i=0;$i<count($arr_catagory_product_id);$i++)
	{
	  $catagory_product_sep 	= $arr_catagory_product_id[$i];		
	  $requestdata['project_id']= $project_id;       	  

	  for($j=0;$j<count($catagory_product_sep);$j++){
			$catagory_product = explode("###",$catagory_product_sep);
			$catagoryid  	  = array();
			$productid 	      = array();				
			$brandid 	      = array();						
			$serialid 		  = array();						
			$purchaseNo 	  = array();		  
			$catagoryid['c']  = $catagory_product[0];				
			$brandid['b']  	  = $catagory_product[1];				
			$productid['p']   = $catagory_product[2];			
			$serialid['s']    = $catagory_product[3];		
			$purchaseNo['po'] = $catagory_product[4];
		}

	   foreach($catagoryid as $val)
	   {
			$requestdata['catagory'] = $val;	
	   }
	   foreach($brandid as $val){
			$requestdata['brand_id']= $val; $brand_id = $val;
	   }	
	   foreach($productid as $val)
	   {
			$requestdata['product'] =$val;	
			$product_id				=$val;
	   }		   	
	   foreach($serialid as $val)
	   {
			$requestdata['serial'] =$val;	
			$serial	=$requestdata['serial'];
	   }	   
	   foreach($purchaseNo as $val){
			$requestdata['pvoucher_no']= $val; $pvoucher_no = $val;
	   }
	   foreach($arr_m_unit as $key => $val){
		  if($catagory_product_sep==$key){
			$requestdata['m_unit'] = $val;	
		  }
	   }
	   foreach($arr_total_unit as $key => $val){
   	    if($catagory_product_sep==$key){
	   	$requestdata['total_unit'] = $val;	
	    }
   	   }
	   foreach($arr_warranty as $key => $val){
		  if($catagory_product_sep==$key){
			$requestdata['warranty'] = $val;  $warranty = $val; 
		  }
	   }   	  
	   foreach($arr_unit_price as $key => $val){
		  if($catagory_product_sep==$key){
			$requestdata['unit_price'] = $val;	
		  }
	   }
	   foreach($arr_qty as $key => $val){
			if($catagory_product_sep==$key){
				 $requestdata['delivery_qty'] = $val;	$deliveryQty = $val;
			}
	   }
	   		      	  
	   foreach($arr_free_item as $key => $val){
		  if($catagory_product_sep==$key){
			$requestdata['free_qty']= $val;	$free_qty =$val;
			
		  }
	   }
	   foreach($arr_total_bag as $key => $val){
			if($catagory_product_sep==$key){
				 $requestdata['total_bag'] = $val;	
			}
	   }	
	   foreach($arr_total_value as $key => $val){
		  if($catagory_product_sep==$key){
			 $requestdata['total_amount'] = $val; 	
		  }
	   }
	   $Pcsql = "SELECT product_catagory FROM ".PRODUCT_TBL." WHERE product_id = '$product_id' AND project_id = '$project_id'";
	   $Pcrow = mysql_fetch_object(mysql_query($Pcsql));
	   $product_catagory 		= $Pcrow->product_catagory;
	   if($product_catagory=="Serial"){
		$SdSql="SELECT sal_detail_id,delivery_qty,discount_per_qty,discount_amount,purchase_price,unit_price,unit_profit FROM ".SALES_DETAILS_TBL." WHERE 
		product='$product_id' AND brand_id='$brand_id' AND project_id='$project_id' AND voucher_no='".getRequest('svoucher_no')."' AND delivery_qty=0 ORDER BY serial ASC";
	    $Sdrow = mysql_fetch_object(mysql_query($SdSql));
	    $sal_detail_id = $Sdrow->sal_detail_id;	
		$unit_price  	  	= $Sdrow->unit_price;
		$discount_per_qty  	= $Sdrow->discount_per_qty;
		$discount_amount  	= $Sdrow->discount_amount;		
		}else{
	    $SdSql="SELECT sal_detail_id,delivery_qty,discount_per_qty,discount_amount,purchase_price,unit_price,unit_profit FROM ".SALES_DETAILS_TBL." WHERE
		 product='$product_id' AND brand_id='$brand_id' AND project_id='$project_id' AND voucher_no='".getRequest('svoucher_no')."' AND serial='$serial' ";
	    $Sdrow = mysql_fetch_object(mysql_query($SdSql));
		$unit_price  	  	= $Sdrow->unit_price;
		$discount_per_qty  	= $Sdrow->discount_per_qty;
		$discount_amount  	= $Sdrow->discount_amount;
		}
		$requestdata['discount_per_qty']  = $discount_per_qty;			   		
		$requestdata['discount_amount']   = $discount_amount;	
		
	    $requestdata['created_by'] 		  = getFromSession('userid');
	    $requestdata['created_time']      = date('Y-m-d h:i:s');  
	    $project_id						  = getFromSession('project_id'); 
	    $requestdata['project_id']        = $project_id;
	    $requestdata['voucher_no']        = $voucher_no;
	    $requestdata['delivery_master_id']= $delivery_master_id;

	    $info        		=  array();
	    $info['table']		= SALES_DELIVERY_CHALLAN_TBL;
  	    $info['data'] 		= $requestdata;      
    	    $res = insert($info);	
		$totalCR  = $this->getTotalCreditStock($product_id,getFromSession('project_id'));
		$totalDR  = $this->getTotalDebitStock($product_id,getFromSession('project_id'));					 
		$balance  = ($totalDR - ($totalCR+$deliveryQty+$free_qty));	
		$delivery_date = formatDate(getRequest('delivery_date')); 
		//======= PD Update when press sales order ========
		$StockDeliveryQty = ($deliveryQty+$free_qty);
		$PUSql="SELECT pur_detail_id,unit_price,sales_qty FROM ".PURCHASE_DETAILS_TBL." WHERE product='$product_id' AND brand_id='$brand_id' 
	    AND project_id='$project_id' AND voucher_no='$pvoucher_no' AND serial='$serial'"; 
	    $Prorow = mysql_fetch_object(mysql_query($PUSql));
	    $pur_detail_id  = $Prorow->pur_detail_id;
		$requestdata['purchase_price'] = $Prorow->unit_price; $purchase_price=$Prorow->unit_price;			
	    $TTLSalesQty    = ($Prorow->sales_qty+$deliveryQty+$free_qty);
		
	    $unit_profit    = ($requestdata['unit_price']-$requestdata['purchase_price']);
		$unit_profit    = ($unit_profit-$requestdata['discount_amount']);
	    if($unit_profit>=0){
		$totalProfite = ($unit_profit*$deliveryQty);	
		//========= Direct Income Dr ==========
		$SalesIncomeId 	 = $comlistApp->getProductSalesIncomeId(getFromSession('project_id'));
		if($SalesIncomeId){
		$totalSalesIncomeCR = $this->getTotalCreditAmount($SalesIncomeId,getFromSession('project_id'));
		$totalSalesIncomeDR = $this->getTotalDebitAmount($SalesIncomeId,getFromSession('project_id'));					 
		$SalesIncomeBalance = (($totalSalesIncomeDR+$totalProfite)-$totalSalesIncomeCR);	
		$salesDtl = "Income from product sales";			 
		$comlistApp->saveAccJournal($voucher_no,$SalesIncomeId,"Acc","Direct Incomes",getFromSession('project_id'),$salesDtl,$totalProfite,0,$SalesIncomeBalance,0,$created_date);
		}//End if SalesIncomeId
		}else{
		$totalProfite = ($unit_profit*$deliveryQty);
		$totalProfite = abs($totalProfite);
		//========= Direct Income Cr ==========
		$SalesIncomeId 	 = $comlistApp->getProductSalesIncomeId(getFromSession('project_id'));
		if($SalesIncomeId){
		$totalSalesIncomeCR = $this->getTotalCreditAmount($SalesIncomeId,getFromSession('project_id'));
		$totalSalesIncomeDR = $this->getTotalDebitAmount($SalesIncomeId,getFromSession('project_id'));					 
		$SalesIncomeBalance = ($totalSalesIncomeDR-($totalSalesIncomeCR+$totalProfite));	
		$salesDtl = "loss from product sales";				 
		$comlistApp->saveAccJournal($voucher_no,$SalesIncomeId,"Acc","Direct Incomes",getFromSession('project_id'),$salesDtl,0,$totalProfite,$SalesIncomeBalance,0,$created_date);
		}//End if SalesIncomeId
		}//End else
		//=== Stock Cr =====
		$StockAmount   = ($requestdata['purchase_price']*($deliveryQty+$free_qty));
		$StockId       = $comlistApp->getStockId(getFromSession('project_id'));
		if($StockId){
		$totalStockCr  = $this->getTotalCreditAmount($StockId,getFromSession('project_id'));
		$totalStockDr  = $this->getTotalDebitAmount($StockId,getFromSession('project_id'));					 
		$StockBalance  = ($totalStockDr-($totalStockCr+$StockAmount));	$description = "Sales Product";					 
		$comlistApp->saveAccJournal($voucher_no,$StockId,"Stock","Sales Product",getFromSession('project_id'),$description,0,$StockAmount,$StockBalance,0,$created_date);
	   	}	   
	    	$pdusql = "UPDATE ".PURCHASE_DETAILS_TBL." SET sales_qty='".$TTLSalesQty."' WHERE pur_detail_id='$pur_detail_id'";
		mysql_query($pdusql);
		//=======SD Update==========			
		if($product_catagory=="Serial"){
		$SdSql="SELECT sal_detail_id FROM ".SALES_DETAILS_TBL." WHERE product='$product_id' AND brand_id='$brand_id' 
	    AND project_id='$project_id' AND voucher_no='".getRequest('svoucher_no')."' AND delivery_qty=0 ORDER BY serial ASC";
	    	$Sdrow = mysql_fetch_object(mysql_query($SdSql));
	    	$sal_detail_id = $Sdrow->sal_detail_id;
	    	$TTLDvQty = 1;
		$sdsql = "UPDATE ".SALES_DETAILS_TBL." SET pvoucher_no='$pvoucher_no',serial='$serial',warranty='$warranty',purchase_price='$purchase_price',
		unit_profit='$unit_profit',delivery_qty='".$TTLDvQty."',free_qty='$free_qty' WHERE voucher_no='".getRequest('svoucher_no')."' ";
		$sdsql.=" AND  product='$product_id' AND brand_id='$brand_id' AND project_id = '$project_id' AND sal_detail_id='$sal_detail_id'";
		mysql_query($sdsql);						
		}else{
	    	$sal_detail_id = $Sdrow->sal_detail_id;
		$PvDeliveryQty = $Sdrow->delivery_qty;
	    	$TTLDvQty = ($PvDeliveryQty+$deliveryQty);			
		$unit_profit    = ($requestdata['unit_price']-$requestdata['purchase_price']);
		$unit_profit    = ($unit_profit-$requestdata['discount_amount']);
		if(($Sdrow->purchase_price>0 && $Sdrow->unit_profit>0) && ($requestdata['purchase_price']!=$Sdrow->purchase_price)){
			$purchase_price = (($purchase_price+$Sdrow->purchase_price)/2); 
			$PreUnitProfit 	= ($unit_profit*$deliveryQty);
			$PvUnitProfit 	= ($Sdrow->unit_profit*$PvDeliveryQty);
			$unit_profit 	= (($PreUnitProfit+$PvUnitProfit)/$TTLDvQty); 				
		}
		$sdsql = "UPDATE ".SALES_DETAILS_TBL." SET pvoucher_no='$pvoucher_no',serial='$serial',warranty='$warranty',purchase_price='$purchase_price',
		unit_profit='$unit_profit', delivery_qty='".$TTLDvQty."',free_qty='$free_qty' WHERE voucher_no='".getRequest('svoucher_no')."' ";
		$sdsql.=" AND  product='$product_id' AND brand_id='$brand_id' AND project_id = '$project_id' AND sal_detail_id='$sal_detail_id'";
		mysql_query($sdsql);			
		}		
		$Prosql = "SELECT product_type FROM ".PRODUCT_TBL." WHERE product_id = '$product_id' AND project_id = '$project_id'";
		$Prorow = mysql_fetch_object(mysql_query($Prosql));
		$product_type 		= $Prorow->product_type;						
		if($product_type=="Sales Item" || $product_type=="Raw Materials"){	
		if($requestdata['discount_amount']>0){ $netSalesPrice = $requestdata['unit_price']-$requestdata['discount_amount']; }else{$netSalesPrice = $requestdata['unit_price'];}
		$this->saveStockJournal($pvoucher_no,$voucher_no,$project_id,$product_id,$serial,$warranty,$netSalesPrice,$requestdata['m_unit'],0,$StockDeliveryQty,$balance,$delivery_date);			
		}
		if($free_qty>0){
		$DrAmount = ($requestdata['purchase_price']*$free_qty);
		$description = "Gives free product with delivery challan";
		//========= Capital Cr ==========
		$created_date 	 = formatDate(getRequest('delivery_date'));
		$capital_head 	 = $comlistApp->getMainCapitalId(getFromSession('project_id'));
		if($capital_head){
		$totalCapitalCR  = $this->getTotalCreditAmount($capital_head,getFromSession('project_id'));
		$totalCapitalDR  = $this->getTotalDebitAmount($capital_head,getFromSession('project_id'));					 
		$Capitalbalance  = ($totalCapitalDR-($totalCapitalCR+$DrAmount));					 
		//$comlistApp->saveAccJournal($voucher_no,$capital_head,"Acc","Free Product",getFromSession('project_id'),$description,0,$DrAmount,$Capitalbalance,0,$delivery_date);
		}
		//========= Administrative Cost Dr ==========
		$freeItemhead 	 = $comlistApp->getAdvCostFreeItemId(getFromSession('project_id'));
		if($freeItemhead){
		$totalfreeItemCR = $this->getTotalCreditAmount($freeItemhead,getFromSession('project_id'));
		$totalfreeItemDR = $this->getTotalDebitAmount($freeItemhead,getFromSession('project_id'));					 
		$freeItemBalance = (($totalfreeItemDR+$DrAmount)-$totalfreeItemCR);					 
		$comlistApp->saveAccJournal($voucher_no,$freeItemhead,"Acc","Free Product",getFromSession('project_id'),$description,$DrAmount,0,$freeItemBalance,0,$delivery_date);
		}//End if freeItemhead
		}//End if free_qty
	   } 
	    
	   header("location:index.php?app=delivery_challan&cmd=print_vouchar&voucher_no=".$voucher_no."&sdm_id=".$delivery_master_id);
  } //End of the function savePaymentDetails()

   //========= make_seed function 4 gatepass ========
   function make_seed(){
   	list($usec, $sec) = explode(' ', microtime());
   	return (float) $sec + ((float) $usec * 100000);
   } 	
 
   function saveStockJournal($pvoucher_no,$voucher_no,$project_id,$product_id,$serial,$warranty,$unit_price=NULL,$m_unit,$DR=NULL,$CR=NULL,$balance,$create_date=NULL){
		$sql = "INSERT INTO ".STOCK_LEDGER_TBL." (po_no,voucher_no,project_id,product_id,serial,warranty,unit_price,m_unit,dr,cr,balance,create_date) VALUES('".$pvoucher_no."','".$voucher_no."','".$project_id."','".$product_id."','".$serial."','".$warranty."','".$unit_price."','".$m_unit."','".$DR."','".$CR."','".$balance."','".$create_date."')";
		mysql_query($sql);
   }
   /*
   function getSalesMasterInfo($id,$delivery_master_id){	
	$project_id     = getFromSession('project_id');  
	$info           = array();    
	$info['table']  = SALES_MASTER_TBL.' pm,'.SALES_DELIVERY_MASTER_TBL.' sdm,'.SUB_ACC_HEAD_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
	$info['fields'] = array('sdm.sales_delivery_master_id','pm.voucher_no','pm.po_no','pm.wo_no','p.project_name','p.project_logo','p.location','pm.customer','s.sub_head_name','s.head_details','s.phone','s.mobile','s.email','s.att_name1','s.att_designation1','s.att_mobile1','pm.reference','pm.gate_pass','pm.track_no','pm.salse_type','pm.total_value',"DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date","pm.service_charge","DATE_FORMAT(sdm.delivery_date,'%d %b %y' ) as delivery_date",'pm.mode_of_payment','c.curr_symble','pm.bank_name','pm.acc_no','pm.check_no','pm.check_no','pm.general_discount_percent','pm.general_discount_amount','pm.exclusive_discount_percent','pm.exclusive_discount_amount','pm.additional_discount','pm.product_discount','pm.discount','pm.net_payble','pm.paid_amount','pm.due','pm.ref_no','pm.created_date','pm.total_value as order_amount','sdm.total_value as delivery_amount','sdm.previour_balance','sdm.challan_no','sdm.consignee','pm.description',"pm.delivery_point");
	
	$sql="pm.voucher_no=sdm.voucher_no AND pm.customer = s.sub_id AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."' AND
	 pm.voucher_no = '$id' AND sdm.sales_delivery_master_id='$delivery_master_id'";
	$info['where']   = $sql;	  	
    $info['groupby'] = array("pm.voucher_no");
	//$info['debug']  = true;
	$res            =	select($info);
	if(count($res)){
		foreach($res as $i=>$v){
			$data[$i] = $v;             
		}
	}
	  //dumpVar($data);
	  return $data[0];
   } 
  */  
  
   function loadProduct4Catagory($catagory)
   {	  
	  $brand_id		   = trim(getRequest('brand_id'));
	  $voucher_no	   = trim(getRequest('svoucher_no'));
  	  $project_id 	   = getFromSession('project_id');  
	  $info            = array();
	  $info['table']   = PRODUCT_TBL.' p,'.SALES_DETAILS_TBL.' sd,'.PURCHASE_DETAILS_TBL.' pd';
	  $info['fields']  =  array('pd.voucher_no','sd.product','p.product_name','p.product_desc','sd.details',"(pd.`rec_qty`-pd.`sales_qty`) as stock");
	  $SQL = "p.product_id=pd.product AND pd.product=sd.product AND pd.`brand_id`= sd.`brand_id` AND pd.`brand_id`='$brand_id' AND sd.project_id='$project_id' AND (pd.`rec_qty`-pd.`sales_qty`)>0 AND sd.voucher_no = '".$voucher_no."'";
	  $info['where']   = $SQL; 
	  $info['groupby'] = array("pd.voucher_no,pd.product");
	  $info['orderby'] = array("pd.voucher_no ASC");
	  //$info['debug']   = true;	
	  $result          = select($info);
	  $data            = array();
	  	
	  if(count($result)){
		 foreach($result as $key=>$value){
			$data[$key][]        = $value;
		 }
	  }
			
	  foreach($data as $i=>$v){
		 $subject_idname.=trim($v[0]->voucher_no).'#####'.$v[0]->product.'#####'.$v[0]->product_name.'#####'.$v[0]->details.'#####'.$v[0]->product_desc.'#####'.$v[0]->stock.'@@@';
	  }
	  echo $subject_idname;	
   }
   function loadOrderInfo($product_id){
	$project_id = getFromSession('project_id'); 
	$voucher_no 	   = getRequest('voucher_no');
	$pvoucher_no 	   = getRequest('pvoucher_no'); 

	$info            = array();
	$info['table']   = SALES_DETAILS_TBL.' sd,'.PURCHASE_DETAILS_TBL.' pd';
	$info['fields']  =  array('sd.m_unit','pd.serial','pd.warranty','sd.discount_per_qty','sd.unit_price','sd.qty as order_qty','(sd.qty-sd.delivery_qty) as balance_qty');
	$info['where']   = "sd.product=pd.product AND sd.product = '$product_id' AND sd.voucher_no = '".$voucher_no."' AND sd.project_id = '$project_id'";
	$info['groupby'] = array("sd.voucher_no");
	//$info['debug']   = true;
	$result          = select($info);
	$data            = array();
	   
	if(count($result)){
	 foreach($result as $key=>$value){
		$data[$key][]        = $value;
	 }
	}
	$PUSql="SELECT  (rec_qty-sales_qty) as stock_qty FROM ".PURCHASE_DETAILS_TBL." WHERE product='$product_id' AND project_id='$project_id' 
	AND voucher_no='$pvoucher_no' AND rec_qty>sales_qty";
	$Prorow = mysql_fetch_object(mysql_query($PUSql));
	$stock_qty = $Prorow->stock_qty;	
	foreach($data as $i=>$v){
	 $str=$v[0]->m_unit."#####".$v[0]->serial."#####".$v[0]->warranty."#####".$v[0]->order_qty."#####".$v[0]->balance_qty."#####".$v[0]->unit_price."#####"
	 .$stock_qty."#####".$v[0]->discount_per_qty;
	}	  
	echo $str;	
   }

   function getBankAccountList($purchase_no=null)
   {
	   if($from == "" && $to == ""){$from=0; $to=40;}  
	   $data           = array();	  
	   $info           = array();
	   $info['table']  = BANK_ACCOUNT_TBL.' ba,'.BANK_TBL.' b';	
	   $info['fields'] = array('ba.bank_code','b.bank_name','ba.purchase_no','ba.account_name','ba.account_type','ba.phone','ba.fax');
	   if($purchase_no!=""){				
			$info['where']   = "ba.bank_code = b.bank_id AND ba.purchase_no = '".$purchase_no."'";
	   }else{
			$info['where']   = "ba.bank_code = b.bank_id";
	   }    
	   $info['orderby'] = array("ba.purchase_no asc LIMIT $from,$to");
	   $info['debug']   = false;			 
	   $res            =	select($info);   
	   if(count($res)){
		  foreach($res as $i=>$v){
			 $data[$i] = $v;
		  }
	   }
	   if($purchase_no==""){
		return $data; // for list
	  }else{
		return $data[0];	// for view
	  }
	
   }
   function getCurrencyList()
   {
      $info            = array();
      $info['table']   = CURRENCY_TBL;
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
   
   function getCatagoryList()
   {	
	  $project_id  		= getFromSession('project_id');
      $data 			= array(); 
      $info        		=  array();
      $info['table']	= CATAGORY_TBL;
	  $info['where']   = "project_id = '$project_id'";
      $res            	=	 select($info);      

      if(count($res))
      {
         foreach($res as $i=>$v)
         {
            $data[$i] = $v;             
         }
      }
	  //dumpVar($data);
      return $data;	
   }
 
 
  function getCustomerList()
  {		  
	  $project_id  		= getFromSession('project_id');
      $data 			= array(); 
      $info        		= array();
      $info['table']	= SUB_ACC_HEAD_TBL;
	  $info['where']  	= "head_type = 'Customer' AND project_id='".$project_id."'";	  	
      $res            	= select($info);      

      if(count($res))
      {
         foreach($res as $i=>$v)
         {
            $data[$i] = $v;             
         }
      }
	  //dumpVar($data);
      return $data;	
   }     
 
   function loadSOInfo($voucher_no)
   {	  
  	  $project_id = getFromSession('project_id');  
	  $info            = array();
	  $info['table']   = SALES_MASTER_TBL;
	  $info['fields']  =  array('consignee','salse_type','delivery_date');
	  $info['where']   = "voucher_no = '$voucher_no' AND project_id = '$project_id'";
	  //$info['debug']   = true;	
	  $result          = select($info);
	  $data            = array();

	  if(count($result)){
		 foreach($result as $key=>$value){
			$data[$key][]        = $value;
		 }
	  }				
	  foreach($data as $i=>$v){
		 $subject_idname.= $v[0]->consignee."#####".$v[0]->salse_type.'#####'.$v[0]->delivery_date;
	  }
	  echo $subject_idname;	
   }

   function loadUnitePrice($product_id){
	  $project_id = getFromSession('project_id');  		 
	  $info            = array();
	  $info['table']   = SALES_DETAILS_TBL;
	  $info['fields']  =  array('m_unit','unit_price','qty','unit_profit');
	  $info['where']   = "voucher_no = '".$_REQUEST['voucher_no']."' AND product = '$product_id' AND project_id = '$project_id'";
	  $info['groupby'] = array("voucher_no");
	  //$info['debug']   = true;
	  $result          = select($info);
	  $data            = array();
	  if(count($result)){
		 foreach($result as $key=>$value)
		 {
			$data[$key][]        = $value;
		 }
	  }
			
	  foreach($data as $i=>$v)
	  {
		 $unit_price = ($v[0]->unit_price)."#####".$v[0]->qty."#####".$v[0]->m_unit;
	  }
	  echo $unit_price;	
    }
   
    function loadStockQty($product_id){
	  $project_id = getFromSession('project_id');  
	  $voucher_no = $_REQUEST['voucher_no'];		 
	  $totalCr = $this->getTotalCreditStock($product_id,$project_id);
	  $totalDr = $this->getTotalDebitStock($product_id,$project_id);
	  $balanceQty = $totalDr - $totalCr;
	  $sql = "SELECT SUM(delivery_qty) AS delivery_qty FROM ".SALES_DELIVERY_CHALLAN_TBL." WHERE product ='$product_id' AND project_id = '$project_id' AND voucher_no = '$voucher_no'";
	  $res = mysql_query($sql);
	  if(mysql_num_rows($res)>0){
	  $row = mysql_fetch_object($res);
	  if($row->delivery_qty==""){$delivery_qty=0;}else{$delivery_qty=$row->delivery_qty;}
	  }else{
	  $delivery_qty=0;
	  }
	  $balanceQty = $balanceQty - $delivery_qty;
	  echo $balanceQty;	
    }
   
    function getTotalCreditAmount($acc_head,$project_id){
   		$sql = "SELECT sum(`cr`) as credit_amount FROM ".ACCOUNT_JOURNAL_TBL." WHERE sub_id = '$acc_head' AND project_id = '$project_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		$credit_amount = $row->credit_amount;
		if(empty($credit_amount)){
			$credit_amount = 0;
		}
		return $credit_amount;
   }
  
   function getTotalDebitAmount($acc_head,$project_id){
   		$sql = "SELECT sum(`dr`) as debit_amount FROM ".ACCOUNT_JOURNAL_TBL." WHERE sub_id = '$acc_head' AND project_id = '$project_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		$debit_amount = $row->debit_amount;
		if(empty($debit_amount)){
			$debit_amount = 0;
		}
		return $debit_amount;
   } 
   function getTotalCreditStock($acc_head,$project_id){
   		$sql = "SELECT sum(`cr`) as credit_amount FROM ".STOCK_LEDGER_TBL." WHERE product_id = '$acc_head' AND project_id = '$project_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		$credit_amount = $row->credit_amount;
		if(empty($credit_amount)){
			$credit_amount = 0;
		}
		return $credit_amount;
   }
 
   function getTotalDebitStock($acc_head,$project_id){
   		$sql = "SELECT sum(`dr`) as debit_amount FROM ".STOCK_LEDGER_TBL." WHERE product_id = '$acc_head' AND project_id = '$project_id'";
		$row = mysql_fetch_object(mysql_query($sql));
		$debit_amount = $row->debit_amount;
		if(empty($debit_amount)){
			$debit_amount = 0;
		}
		return $debit_amount;
   }
	  
   function createVoucharID(){
      $info = array();
      $info['table'] = DEVIT_VOUCHAR_TBL;
      $info['fields'] = array('max(voucher_no) as maxvoucher');
      $res = select($info);
      $maxvoucherId = 'D0000000';
      if(count($res)){
         foreach($res as $v){
         	 if($v->maxvoucher){
             $maxvoucherId = $v->maxvoucher;
             }
             break;   	
         }
      }
      $maxvoucherId = generateID("D",$maxvoucherId,8);
      return $maxvoucherId;
   }  
   function deleteRecord($id){
   	  if(getRequest('id')){ 
      	$info = array();
      	$info['table'] = BANK_ACCOUNT_TBL;
      	$info['where'] = "purchase_no='$id'";
      	$info['debug'] = false;
      	$res = delete($info);      	
      	if($res){
      	  $msg="Successfully delete Record !!!";
          header("location:?app=bank_account&cmd=view&msg=$msg");     	   

      	} else{
      		 header("location:?app=bank_account&cmd=view&cmd=list&deleted=no");
      	}      	
      }
  } 
 //================= Start Sales Details ====================

  function showEditor4SalesDetails($msg = null) {        
	  $data                		= array();
	  $data['cmd']         		= getRequest('cmd');
	  $data['record_list'] 		= $this->getSalesDetailsList(getRequest('from'),getRequest('to'));
	  $data['totalrecord']		= $this->getTotalSalesDetailsList(getRequest('from'),getRequest('to'));	
	   require_once(SALES_DETAILS_SKIN); 
	   return $data[0];

   }
   function showAllCompaniesSalesDetails($msg = null) {       

	  $data                	= array();
	  $data['cmd']         	= getRequest('cmd');
	  $data['record_list'] 	= $this->getAllSalesDetailsList(getRequest('from'),getRequest('to'));
	  $data['totalrecord']	= $this->getAllTotalSalesDetailsList(getRequest('from'),getRequest('to'));
	  require_once(ADMIN_SALES_DETAILS_SKIN); 
		
	  return $data[0];

   }

   function getSalesDetailsList($from,$to) { 

	if($from == "" && $to == ""){$from=0; $to=500;}
	$date_from 	= formatDate(getRequest('date_from'));
	$date_to 	= formatDate(getRequest('date_to'));				
	$project_id     = getFromSession('project_id');  
	$info           = array();    
	$info['table']  = SALES_MASTER_TBL.' pm,'.SUB_ACC_HEAD_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
	$info['fields'] = array('pm.voucher_no','p.project_name','p.location','s.sub_id','s.sub_head_name','s.head_details','pm.po_no','pm.wo_no','pm.gate_pass','pm.track_no','pm.salse_type','pm.total_value',"DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date","DATE_FORMAT(pm.delivery_date,'%d %b %y' ) as delivery_date",'pm.mode_of_payment','c.curr_symble','pm.bank_name','pm.acc_no','pm.check_no','pm.check_no','pm.discount','pm.net_payble','pm.paid_amount','pm.due','pm.ref_no','pm.created_date',"DATE_FORMAT(pm.created_date,'%d %b %y' ) as date");
	
	$sql="pm.customer = s.sub_id AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."'";							
	
	if($date_from!="" && $date_to ==""){

		$sql.=" AND pm.created_date >= '$date_from'";

	}elseif($date_from=="" && $date_to !=""){

		$sql.=" AND pm.created_date <= '$date_to'";

	}elseif($date_from!="" && $date_to !=""){

		$sql.=" AND pm.created_date BETWEEN '$date_from' AND '$date_to'";

	}

	$info['where']  =$sql;		

	$info['orderby'] = array("pm.voucher_no asc LIMIT $from,$to");

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

   function getTotalSalesDetailsList($from,$to) {  
		
	$date_from 	= formatDate(getRequest('date_from'));
	$date_to 	= formatDate(getRequest('date_to'));				
	$project_id     = getFromSession('project_id');  
	$info           = array();    
	$info['table']  = SALES_MASTER_TBL.' pm,'.SUB_ACC_HEAD_TBL.' s,'.PROJECT_TBL.' p,'.CURRENCY_TBL.' c';	
	$info['fields'] = array('pm.voucher_no');
	
	$sql="pm.customer = s.sub_id AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '".$project_id."'";
						
	
	if($date_from!="" && $date_to ==""){

		$sql.=" AND pm.created_date >= '$date_from'";

	}elseif($date_from=="" && $date_to !=""){

		$sql.=" AND pm.created_date <= '$date_to'";

	}elseif($date_from!="" && $date_to !=""){

		$sql.=" AND pm.created_date BETWEEN '$date_from' AND '$date_to'";

	}
	$info['where']  =$sql;	


	$info['orderby'] 	= array("pm.created_date asc");

	//$info['debug']  	= true;

	$result         	= select($info);

	$data           	= array();     

        $cnt = count($result);  	

      	if($cnt) {

        	return $cnt;

      	}else {

	  return 0;

	 }    
      

   }      

   function getAllSalesDetailsList($from,$to) { 

	if($from == "" && $to == ""){$from=0; $to=500;}
	$date_from 	= formatDate(getRequest('date_from'));
	$date_to 	= formatDate(getRequest('date_to'));				
	$project_id     = getFromSession('project_id');  
	$info           = array();    
	$info['table']  = SALES_MASTER_TBL.' pm,'.SUB_ACC_HEAD_TBL.' s,'.CURRENCY_TBL.' c';	
	$info['fields'] = array('pm.voucher_no','pm.project_id','s.sub_id','s.sub_head_name','s.head_details','pm.gate_pass','pm.track_no','pm.salse_type','pm.total_value',"DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date",'pm.mode_of_payment','c.curr_symble','pm.bank_name','pm.acc_no','pm.check_no','pm.check_no','pm.discount','pm.net_payble','pm.paid_amount','pm.due','pm.ref_no','pm.created_date',"DATE_FORMAT(pm.created_date,'%d %b %y' ) as date");
	
	$sql="pm.customer = s.sub_id AND pm.currency = c.currency_id";
						
	
	if($date_from!="" && $date_to ==""){

		$sql.=" AND pm.created_date >= '$date_from'";

	}elseif($date_from=="" && $date_to !=""){

		$sql.=" AND pm.created_date <= '$date_to'";

	}elseif($date_from!="" && $date_to !=""){

		$sql.=" AND pm.created_date BETWEEN '$date_from' AND '$date_to'";

	}

	$info['where']  =$sql;		

	$info['orderby'] = array("pm.voucher_no asc LIMIT $from,$to");

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

   function getAllTotalSalesDetailsList($from,$to) {  
		
	$date_from 	= formatDate(getRequest('date_from'));
	$date_to 	= formatDate(getRequest('date_to'));				
	$project_id     = getFromSession('project_id');  
	$info           = array();    
	$info['table']  = SALES_MASTER_TBL.' pm,'.SUB_ACC_HEAD_TBL.' s,'.CURRENCY_TBL.' c';	
	$info['fields'] = array('pm.voucher_no');
	
	$sql="pm.customer = s.sub_id AND pm.currency = c.currency_id";
						
	
	if($date_from!="" && $date_to ==""){

		$sql.=" AND pm.created_date >= '$date_from'";

	}elseif($date_from=="" && $date_to !=""){

		$sql.=" AND pm.created_date <= '$date_to'";

	}elseif($date_from!="" && $date_to !=""){

		$sql.=" AND pm.created_date BETWEEN '$date_from' AND '$date_to'";

	}
	$info['where']  =$sql;	


	$info['orderby'] 	= array("pm.created_date asc");

	//$info['debug']  	= true;

	$result         	= select($info);

	$data           	= array();     

    	$cnt = count($result);  	

	if($cnt) {

		return $cnt;

	}else {

	  return 0;

	 }       

   }      

   function savePayableCheck($voucher_no,$transaction_type,$paid_amount){
	  $requestdata = array();

	  $requestdata = getUserDataSet(PAYABLE_CHECK_TBL);	
	  $requestdata['check_no'] 			= getRequest('check_no');
	  $requestdata['check_issue_date']  = formatDate(getRequest('check_issue_date')); 
	  $requestdata['created_date']      = formatDate(getRequest('sales_date'));
	  $requestdata['acc_head'] 			= getRequest('customer'); 
	  $requestdata['head_type'] 		= "Check"; 
	  $requestdata['voucher_no']        = $voucher_no;  
	  $requestdata['paid_amount']  		= $paid_amount;   
	  $requestdata['transaction_type']  = $transaction_type;   
	  $requestdata['project_id']        = getFromSession('project_id');    
	  $requestdata['created_by']        = getFromSession('userid');

	  $info        		=  array();
	  $info['table']	= PAYABLE_CHECK_TBL;
	  $info['data'] 	= $requestdata;     
	  //$info['debug']  	=  true;
	  $res = insert($info);
		
   }
   //============== End Sales Details =============
   
 } // End class


?>
