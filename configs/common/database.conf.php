<?php
/*******************************************************
 *  File name: database.conf.php
 *  Purpose: Database table constants + connection setup
 ********************************************************/

define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_HOST', 'thai_db');
define('DB_NAME', 'skyerp');
define('NDB_NAME', 'skyerp');

define('PRODUCTION_MODE', TRUE);

//========  Table Constants  ===================
define('USER_TBL', DB_NAME . '.user');
define('USERTYPE_TBL', DB_NAME . '.usertype');
define('DIVISION_TBL', DB_NAME . '.division');
define('DISTRICT_TBL', DB_NAME . '.district');
define('AREA_TBL', DB_NAME . '.area');
define('PARTY_INFO_TBL', DB_NAME . '.cs_party_info');
define('PARTY_TBL', DB_NAME . '.cs_party');
define('PROJECT_TBL', DB_NAME . '.project');
define('EMPLOYEE_TBL', DB_NAME . '.employee');
define('CUSTOMER_BOOKING_TBL', DB_NAME . '.cs_customer_booking');
define('CS_PRODUCT_RECEIVED_TBL', DB_NAME . '.cs_received_product');
define('CS_LOAN_DISTRIBUTE_TBL', DB_NAME . '.cs_loan_distribute');
define('CS_LOAN_RECEIVED_TBL', DB_NAME . '.cs_receive_loan');
define('CS_DELIVERY_PRODUCT_TBL', DB_NAME . '.delivery_product');
define('ACC_TRANSACTION_TBL', DB_NAME . '.delivery_product');
define('DEVIT_VOUCHAR_TBL', DB_NAME . '.cs_delivery_product');
define('CREDIT_VOUCHAR_TBL', DB_NAME . '.credit_vouchar');
define('PAYABLE_CHECK_TBL', DB_NAME . '.payable_check');
define('PRODUCT_TBL', DB_NAME . '.product');
define('PRODUCT_GROUP_TBL', DB_NAME . '.product_group');
define('GROUP_WISE_PRODUCT_TBL', DB_NAME . '.attach_product_group');
define('SUPPLIER_TBL', DB_NAME . '.supplier_info');
define('COUNTRY_TBL', DB_NAME . '.country');
define('CURRENCY_TBL', DB_NAME . '.currency');
define('MAIN_CATAGORY_TBL', DB_NAME . '.main_category');
define('CATAGORY_TBL', DB_NAME . '.catagory');
define('SUB_CATAGORY_TBL', DB_NAME . '.subcatagory');
define('BANK_TBL', DB_NAME . '.bank');
define('BANK_ACCOUNT_TBL', DB_NAME . '.bank_account');
define('GROUP_HEAD_TBL', DB_NAME . '.group_account');
define('SUB_HEAD_TYPE_TBL', DB_NAME . '.sub_headtype');
define('CHILD_HEAD_TYPE_TBL', DB_NAME . '.child_head');
define('SUBSIDIARY_STEP3_TBL', DB_NAME . '.subsidiary_step3_head');
define('SUB_ACC_HEAD_TBL', DB_NAME . '.sub_acc_head');
define('ACCOUNT_JOURNAL_TBL', DB_NAME . '.account_journal');
define('PURCHASE_MASTER_TBL', DB_NAME . '.purchase_master');
define('PURCHASE_DETAILS_TBL', DB_NAME . '.purchase_details');
define('PURCHASE_RECEIVED_TBL', DB_NAME . '.purchase_received');
define('PRODUCTION_MASTER_TBL', DB_NAME . '.production_master');
define('PRODUCTION_FG_TBL', DB_NAME . '.production_fg');
define('PRODUCTION_DETAILS_TBL', DB_NAME . '.production_details');
define('TEMP_SALES_ORDER_TBL', DB_NAME . '.temp_sales_order');
define('SALES_TARGET_TBL', DB_NAME . '.sales_target');
define('SALES_TARGET_CATAGORY_TBL', DB_NAME . '.sales_catagory_target');
define('SALES_MASTER_TBL', DB_NAME . '.sales_master');
define('SALES_DETAILS_TBL', DB_NAME . '.sales_details');
define('SALES_MASTER_APP_TBL', DB_NAME . '.sales_master_origin');
define('SALES_DETAILS_APP_TBL', DB_NAME . '.sales_details_origin');
define('SALES_DELIVERY_MASTER_TBL', DB_NAME . '.sales_delivery_item_master');
define('SALES_DELIVERY_CHALLAN_TBL', DB_NAME . '.sales_delivery_item');
define('STOCK_LEDGER_TBL', DB_NAME . '.stock_ledger');
define('BRAND_TBL', DB_NAME . '.brand');
define('WARRANTY_TBL', DB_NAME . '.warranty');
define('COMMISSION_SLOT_TBL', DB_NAME . '.commission_slot');
define('BRANCH_TBL', DB_NAME . '.branch');
define('SALARY_MASTER_TBL', DB_NAME . '.salary_master');
define('SALARY_DETAILS_TBL', DB_NAME . '.salary_details');
define('QUOTATION_MASTER_TBL', DB_NAME . '.quotation_master');
define('QUOTATION_DETAILS_TBL', DB_NAME . '.quotation_details');
define('SALES_RETURN_MASTER_TBL', DB_NAME . '.sales_return_master');
define('SALES_RETURN_TBL', DB_NAME . '.sales_return');
define('SALES_RETURN_PAYBLE_TBL', DB_NAME . '.sales_return_payble');
define('PURCHASE_RETURN_RECEIBAVLE_TBL', DB_NAME . '.purchase_return_receivable');
define('DELIVERY_POINT_TBL', DB_NAME . '.deliverypoint');
define('FACTORY_TBL', DB_NAME . '.factory');
define('PRUDUCT_CLASS_TBL', DB_NAME . '.product_class');
define('PRODUCT_TYPE_TBL', DB_NAME . '.product_type');
define('UOM_TBL', DB_NAME . '.uom');
define('RETAILER_TBL', DB_NAME . '.retailer');
define('OPENING_BALANCE_TBL', DB_NAME . '.opening_balance');
define('TEMP_STOCK_TRANSFER_TBL', DB_NAME . '.temp_stock_transfer');
define('TEMP_PRODUCTION_TBL', DB_NAME . '.temp_production');
define('STOCK_TRANSFER_MASTER_TBL', DB_NAME . '.stock_transfer_master');
define('STOCK_TRANSFER_DETAILS_TBL', DB_NAME . '.stock_transfer_details');
define('PENDING_STOCK_TRANSFER_MASTER_TBL', DB_NAME . '.pending_stock_transfer_master');
define('PENDING_STOCK_TRANSFER_DETAILS_TBL', DB_NAME . '.pending_stock_transfer_details');
define('TEMP_SALES_RETURN_TBL', DB_NAME . '.temp_sales_return');
define('TEMP_STOCK_VERIFY_TBL', DB_NAME . '.temp_stock_verify');
define('STOCK_VERIFY_MASTER_TBL', DB_NAME . '.stock_verify_master');
define('STOCK_VERIFY_DETAILS_TBL', DB_NAME . '.stock_verify_details');
define('INVOICE_ADJUST_HISTORY_TBL', DB_NAME . '.adjust_invoice_history');
define('VOUCHER_ADJUST_HISTORY_TBL', DB_NAME . '.adjust_voucher_history');
define('CONTRA_MASTER_TBL', DB_NAME . '.contra_master');
define('CONTRA_DETAILS_TBL', DB_NAME . '.contra_details');
define('PENDING_CVMASTER_TBL', DB_NAME . '.pending_contra_master');
define('PENDING_CVDETAILS_TBL', DB_NAME . '.pending_contra_details');
define('AVG_PURCHASE_PRICE_TBL', DB_NAME . '.avg_purchase_price');
define('TMP_GRVMASTER_TBL', DB_NAME . '.tmp_grv_master');
define('TMP_GRVDETAILS_TBL', DB_NAME . '.tmp_grv_details');
define('TEMP_PURCHASE_TBL', DB_NAME . '.temp_purchase_item');
define('TEMP_SPR_TBL', DB_NAME . '.temp_spr');
define('SPR_PURCHASE_DETAILS_TBL', DB_NAME . '.spr_details');
define('SPR_PURCHASE_MASTER_TBL', DB_NAME . '.spr_master');
define('PURCHASE_ORDER_DETAILS_TBL', DB_NAME . '.po_details');
define('PURCHASE_OREDR_MASTER_TBL', DB_NAME . '.po_master');
define('TERMS_CONDITION_TBL', DB_NAME . '.term_condition');
define('TEMP_GRN_TBL', DB_NAME . '.temp_grn_item');
define('PO_BATCH_MASTER_TBL', DB_NAME . '.production_batch_master');
define('PO_BATCH_DETAILS_TBL', DB_NAME . '.production_batch_details');
define('TEMP_BATCH_SETUP_TBL', DB_NAME . '.temp_batch_setup');
define('TEMP_RAWMATERIALSREQ_TBL', DB_NAME . '.temp_rawmaterialsreq');
define('MACHINE_TBL', DB_NAME . '.machine');
define('VEHICLES_TBL', DB_NAME . '.vehicles');
define('ACTIVITY_LOG_TBL', DB_NAME . '.activities_log');
define('SENDMAIL_HISTORY_TBL', DB_NAME . '.sendmail_history');

//========  Views  ===================
define('PURCHASE_ITEM_VIEW', DB_NAME . '.vw_purchase_item');
define('STORE_STOCK_VIEW', DB_NAME . '.vw_store_stock_status');
define('PRODUCT_STATUS_BY_CATAGORY_VIEW', DB_NAME . '.vw_product_vs_stock_status');
define('STOCK_STATUS_BY_DATE_VIEW', DB_NAME . '.vw_stock_status_by_date');
define('STOCK_VALUE_VIEW', DB_NAME . '.vw_stock_value');
define('STOCK_VALUE_BY_DATE_VIEW', DB_NAME . '.vw_stock_value_by_date');
define('CUSTOMER_VIEW', DB_NAME . '.vw_customer');
define('CUSTOMER_LEDGER_VIEW', DB_NAME . '.vw_customer_ledger');
define('CUSTOMER_OB_VIEW', DB_NAME . '.vw_customer_opening_balance');
define('CUSTOMER_SALES_LEDGER_VIEW', DB_NAME . '.vw_customer_delivery_ledger');
define('CUSTOMER_SALES_RETURN_DETAILS_VIEW', DB_NAME . '.vw_customer_sales_return_details');
define('CUSTOMER_SALES_RETURN_LEDGER_VIEW', DB_NAME . '.vw_customer_sales_return_ledger');
define('MONTHLY_CUSTOMER_STATUS_VIEW', DB_NAME . '.vw_monthly_customer_status');
define('DELIVERY_PRODUCT_LEDGER_VIEW', DB_NAME . '.vw_delivery_product_ledger');
define('DELIVERY_CATEGORY_LEDGER_VIEW', DB_NAME . '.vw_delivery_category_ledger');
define('DELIVERY_SALES_STATUS_VIEW', DB_NAME . '.vw_delivery_sales_status');
define('CUSTOMER_DELIVERY_SALES_STATUS_VIEW', DB_NAME . '.vw_customer_delivery_sales_status');
define('SUPPLIER_VIEW', DB_NAME . '.vw_supplier');
define('SUPPLIER_LEDGER_VIEW', DB_NAME . '.vw_supplier_ledger');
define('SUPPLIER_OB_VIEW', DB_NAME . '.vw_supplier_opening_balance');
define('SUPPLIER_SALES_LEDGER_VIEW', DB_NAME . '.vw_supplier_delivery_ledger');
define('SUPPLIER_SALES_RETURN_DETAILS_VIEW', DB_NAME . '.vw_supplier_sales_return_details');
define('SUPPLIER_SALES_RETURN_LEDGER_VIEW', DB_NAME . '.vw_supplier_sales_return_ledger');
define('SUPPLIER_DELIVERY_SALES_STATUS_VIEW', DB_NAME . '.vw_supplier_delivery_sales_status');

//========  Database Connection  ===================
define('AUTO_CONNECT_TO_DATABASE', TRUE);

if (AUTO_CONNECT_TO_DATABASE) {
    $dbcon = mysql_connect(DB_HOST, DB_USER, DB_PASS) or die("Could not connect: " . mysql_error());
    mysql_select_db(DB_NAME, $dbcon) or die("Could not find: " . mysql_error());
}

?>
