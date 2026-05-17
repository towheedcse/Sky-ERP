<?php
	$pre_skin = trim(TALLY_CHALLAN,"/");
	if(!empty($pre_skin)){
	    $pre_skin = $pre_skin.'_';
	}

	define('SALES_VOUCHAR_SKIN', TEMPLATES_SKINS.'/'.$pre_skin.'print_delivery_challan_invoice.html');	
	define('PRNIT_SALES_INVOICE_SKIN', TEMPLATES_SKINS.'/'.$pre_skin.'print_sales_invoice.html');
	define('PRINT_VOUCHAR_SKIN', TEMPLATES_SKINS.'/print_voucher.html');
	define('SALES_DETAILS_SKIN', TEMPLATES_SKINS.'/sales_details_list.html');
	define('ADMIN_SALES_DETAILS_SKIN', TEMPLATES_SKINS.'/admin_sales_details_list.html');
?>
