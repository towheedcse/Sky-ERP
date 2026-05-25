<?php
ini_set("display_errors", "Off");
ob_start();
define('PROJECT_DIR', '');
define('COMPANY_NAME', 'Thai Foils & Polymer Industries Ltd');
define('LOGO_BRAND', 'Thai Foils & Polymer Industries Ltd');
define('TALLY_CHALLAN', 'thai');

define('URL_PORT', '');
define('APP_GUARD', false);

define('FILE_DIR', $_SERVER['DOCUMENT_ROOT'] . PROJECT_DIR);


require_once($_SERVER['DOCUMENT_ROOT'] . PROJECT_DIR . '/configs/helper/functions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . PROJECT_DIR . '/configs/common/main.conf.php');
//require_once(PROJECT_DIR.'/configs/common/main.conf.php');


//Gets the current application
$currentApp = getRequest('app');
//Checks the current application
switch ($currentApp) {
    case 'accounts.head':
    case 'trial.balance':
    case 'signup':
    case 'login':
    case 'home':
    case 'user_home':
    case 'forgot_password':
    case 'fg.out':
    case 'user':
    case 'change_pass':
    case 'agent':
    case 'employee':
    case 'party':
    case 'project':
    case 'product':
    case 'production':
    case 'fg.production':
    case 'purchase':
    case 'purchase.item':
    case 'purchase.item.grn':
    case 'purchase.edit':
    case 'purchase_order':
    case 'purchase_item_received':
    case 'supplier':
    case 'user':
    case 'bank':
    case 'bank_account':
    case 'customer_booking':
    case 'cs_received_product':
    case 'cs_loan_distribute':
    case 'cs_delivery_product':
    case 'catagory':
    case 'account_head':
    case 'subsidiary.step3':
    case 'journal':
    case 'sales':
    case 'sales.item':
    case 'sales_order':
    case 'sales.order':
    case 'delivery_challan':
    case 'district':
    case 'flat_sales':
    case 'general_vouchar':
    case 'advanced_payment_vouchar':
    case 'advanced_payment':
    case 'used_item':
    case 'po.batch.setup':
    case 'batch.production':
    case 'machine':
    case 'vehicle':
    case 'job_order':
    case 'batch_setup':
    case 'balance_sheet':
    case 'yearending':
    case 'show_ledger':
    case 'used4_feed':
    case 'sales.area.update':
    case 'child.head':
    case 'production.new':
    case 'product.group':
    case 'attach.product':
    case 'sales_target':
    case 'advanced_received':
    case 'brand':
    case "supplier.info":
    case 'warranty':
    case 'customer':
    case 'reference':
    case 'commission-setup':
    case 'branch':
    case 'sales.commission':
    case 'salary.sheet':
    case 'queeck.quotation':
    case 'area':
    case 'sub.headtype':
    case 'sales.return':
    case 'deliverypoint':
    case 'factory':
    case 'product_class':
    case 'retailer':
    case 'sales.report':
    case 'sales.delivery':
    case 'customer.opening':
    case 'customer.opening.march':
    case 'customer.opening.may16':
    case 'product.opening':
    case 'sales.delivery.missing':
    case 'groupwise.stock.transfer':
    case 'sales.return.customerwise':
    case 'bank.received.voucher':
    case 'supplier.opening':
    case 'physical.stock.verification':
    case 'voucher.edit':
    case 'sales.delivery.edit':
    case 'subcatagory':
    case 'contra.voucher':
    case 'contra.voucher.new':
    case 'contra.voucher.edit':
    case 'product.yearending':
    case 'customer.yearending':
    case 'supplier.yearending':
    case 'sales.opening':
    case 'sales.opening.by.trt':
    case 'permission.manager':
    case 'clear.yearending':
    case 'hrm_gateway':
        include_once("apps/$currentApp.php");
        break;

    case 'logout':
        session_unset();
        session_destroy();
        header("Location:?app=home");
        break;

    default:
        header("Location:?app=home");
        break;
}
?>
