<?php
	$pre_skin = trim(TALLY_CHALLAN,"/");
	if(!empty($pre_skin)){
	    $pre_skin = $pre_skin.'_';
	}
	define('SALES_VOUCHAR_SKIN', TEMPLATES_SKINS.'/'.$pre_skin.'print_sales_order_invoice.html');

	define('SALES_ORDER_EDIT_SKIN_FILE', TEMPLATES_SKINS.'/sales_order_edit.html');
	define('PRINT_VOUCHAR_SKIN', TEMPLATES_SKINS.'/print_voucher.html');
	define('SALES_DETAILS_SKIN', TEMPLATES_SKINS.'/sales_details_list.html');
	define('ADMIN_SALES_DETAILS_SKIN', TEMPLATES_SKINS.'/admin_sales_details_list.html');
?>
