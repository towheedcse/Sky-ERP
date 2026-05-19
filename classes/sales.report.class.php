<?php

class SalesReport
{
    function run()
    {

        $cmd = getRequest('cmd');
        $u_t_id = getFromSession('u_type_id');
        if (($u_t_id == 101)) {
            switch ($cmd) {
                case 'sales_ststus'        :
                    $screen = $this->showSalesStatus("Report Page");
                    break;
                case 'product_wise_sl_list'  :
                    $screen = $this->showProductSalesList("Report Page");
                    break;
                case 'product_wise_sl_sum'   :
                    $screen = $this->showProductSalesSummary("Report Page");
                    break;
                case 'sales_details'        :
                    $screen = $this->showSalesDetails("Report Page");
                    break;
                case 'customer_sales_status' :
                    $this->showCustomerSalesDetails("Report Page");
                    break;
                case 'running_production'    :
                    $screen = $this->showRunningProduction("Report Page");
                    break;
                case 'order_list'        :
                    $this->showSalesOrder("Report Page");
                    break;
                case 'bill_list'        :
                    $this->showBillList("Report Page");
                    break;
                case 'edit_order_list'        :
                    $this->showEditSalesOrder("Report Page");
                    break;
                case 'pending_order_list'  :
                    $this->showUnapproveSalesOrder("Report Page");
                    break;
                case 'sales.delivery.order':
                    $this->showSalesOrder("Report Page");
                    break;
                case 'sal_dtl'        :
                    $screen = $this->showSalesOrder("Report Page");
                    break;
                case 'sales.return'        :
                    $screen = $this->showSalesOrder("Report Page");
                    break;
                case 'undelivered_list'      :
                    $this->showUndeliveredSalesOrder("Report Page");
                    break;
                case 'sales_delivery_list'   :
                    $screen = $this->showSalesDeliveryChallan("Report Page");
                    break;
                case 'custom_invoice_list'   :
                    $this->showCustomDeliveryChallan("Report Page");
                    break;
                case 'transfer_list'    :
                    $screen = $this->showStockTransferList("Report Page");
                    break;
                case 'pending_transfer_list'    :
                    $screen = $this->showUnapprovedStockTransferList("Report Page");
                    break;
                case 'verify_list'        :
                    $screen = $this->showStockVerifyList("Report Page");
                    break;
                case 'salesreturn_list'    :
                    $screen = $this->showSalesReturnList("Report Page");
                    break;
                case 'cashbook_list'    :
                    $screen = $this->showCashBookList("Report Page");
                    break;
                case 'bankbook_list'    :
                    $screen = $this->showBankBookList("Report Page");
                    break;
                case 'stock_status'        :
                    $screen = $this->showStockStatus("Report Page");
                    break;
                case 'order.po.status'       :
                    $screen = $this->showOrderProductionReq("Report Page");
                    break;
                case 'stock_movement_status' :
                    $this->showStockMovementStatus("Report Page");
                    break;
                case 'stock_movement_topsheet':
                    $this->showStockMovementTopsheet("Report Page");
                    break;
                case 'stock.status_by_cat'   :
                    $this->showStockStatusByCatagory("Report Page");
                    break;
                case 'stock.status_by_date'  :
                    $this->showStockStatusByDate("Report Page");
                    break;
                case 'sales.status_by_date'  :
                    $this->showSalesStatusByDate("Report Page");
                    break;

                case 'unsale.stock_by_date'  :
                    $this->showUnSalesStockStatusByDate("Report Page");
                    break;
                case 'sales.status.topsheet' :
                    $this->showSalesStatusTRTTopsheet("Report Page");
                    break;
                case 'sales.status_by_cat'    :
                    $this->showSalesStatusByCatagory("Report Page");
                    break;
                case 'sales.status_by_amount' :
                    $this->showSalesStatusByAmount("Report Page");
                    break;
                case 'sales.status_by_trt'   :
                    $this->showSalesStatusByTRT("Report Page");
                    break;
                case 'customer.status_by_trt':
                    $this->showCustomerStatusByTRT("Report Page");
                    break;
                case 'customer.status.by.trt':
                    $this->showCustomerSalesStatusByTRT();
                    break;
                case 'monthly_trt_status'    :
                    $this->showCustomerMonthlyStatusByTRT("Report Page");
                    break;
                case 'transaction_lst'        :
                    $screen = $this->showTransactionEditor();
                    break;
                case 'due_payment_list'      :
                    $screen = $this->showDuePaymentEditor();
                    break;
                case 'due_receive_list'      :
                    $screen = $this->showDueReceivableEditor();
                    break;
                case 'voucher_print'         :
                    $this->showVoucherPrintEditor(getRequest('voucher_no'));
                    break;
                case 'print_cvoucher'        :
                    $this->showCVPrintEditor($msg);
                    break;
                case 'print_udvoucher'       :
                    $screen = $this->showPrintUndeliveryVoucher($msg);
                    break;
                case 'delete_undelivery'     :
                    $this->showDeleteUndeliveryProduct(getRequest('voucher_no'));
                    break;
                case 'batchlist'        :
                    $this->showBatchList($msg);
                    break;
                case 'show.batch.po.list'    :
                    $this->showBatchProductionList();
                    break;
                case 'delete.batch.po'    :
                    $this->DeleteBatchProduction();
                    break;
                case 'pending.voucher'    :
                    $this->showPendingVoucherList();
                    break;
                case 'pending.money.receipt' :
                    $this->showPendingMoneyReceiptList();
                    break;
                case 'customer_list'         :
                    $screen = $this->showCustomerList();
                    break;
                case 'mis_dashboard_report' :
                    $screen = $this->misDAshboardReport();
                    break;
                //default                      : $cmd = 'list'; $screen = $this->showEditor($msg);   break;
            }
        } elseif (($u_t_id == 102)) {
            switch ($cmd) {
                case 'customer.status_by_trt':
                    $this->showCustomerStatusByTRT("Report Page");
                    break;
                case 'sales_ststus'        :
                    $screen = $this->showSalesStatus("Report Page");
                    break;
                case 'product_wise_sl_list'  :
                    $screen = $this->showProductSalesList("Report Page");
                    break;
                case 'product_wise_sl_sum'   :
                    $screen = $this->showProductSalesSummary("Report Page");
                    break;
                case 'sales_details'        :
                    $screen = $this->showSalesDetails("Report Page");
                    break;
                case 'customer_sales_status' :
                    $this->showCustomerSalesDetails("Report Page");
                    break;
                case 'running_production'    :
                    $screen = $this->showRunningProduction("Report Page");
                    break;
                case 'order_list'        :
                    $this->showSalesOrder("Report Page");
                    break;
                case 'bill_list'        :
                    $this->showBillList("Report Page");
                    break;
                case 'pending_order_list'  :
                    $this->showUnapproveSalesOrder("Report Page");
                    break;
                case 'sales.delivery.order':
                    $this->showSalesOrder("Report Page");
                    break;
                case 'sal_dtl'        :
                    $screen = $this->showSalesOrder("Report Page");
                    break;
                case 'sales.return'        :
                    $screen = $this->showSalesOrder("Report Page");
                    break;
                case 'undelivered_list'      :
                    $this->showUndeliveredSalesOrder("Report Page");
                    break;
                case 'sales_delivery_list'   :
                    $screen = $this->showSalesDeliveryChallan("Report Page");
                    break;
                case 'custom_invoice_list'   :
                    $this->showCustomDeliveryChallan("Report Page");
                    break;
                case 'transfer_list'    :
                    $screen = $this->showStockTransferList("Report Page");
                    break;
                case 'pending_transfer_list'    :
                    $screen = $this->showUnapprovedStockTransferList("Report Page");
                    break;
                case 'verify_list'        :
                    $screen = $this->showStockVerifyList("Report Page");
                    break;
                case 'salesreturn_list'    :
                    $screen = $this->showSalesReturnList("Report Page");
                    break;
                case 'cashbook_list'    :
                    $screen = $this->showCashBookList("Report Page");
                    break;
                case 'bankbook_list'    :
                    $screen = $this->showBankBookList("Report Page");
                    break;
                case 'stock_status'        :
                    $screen = $this->showStockStatus("Report Page");
                    break;
                case 'order.po.status'       :
                    $screen = $this->showOrderProductionReq("Report Page");
                    break;
                case 'stock_movement_status' :
                    $this->showStockMovementStatus("Report Page");
                    break;
                case 'stock_movement_topsheet':
                    $this->showStockMovementTopsheet("Report Page");
                    break;
                case 'stock.status_by_cat'   :
                    $this->showStockStatusByCatagory("Report Page");
                    break;
                case 'stock.status_by_date'  :
                    $this->showStockStatusByDate("Report Page");
                    break;
                case 'sales.status_by_date'  :
                    $this->showSalesStatusByDate("Report Page");
                    break;

                case 'unsale.stock_by_date'  :
                    $this->showUnSalesStockStatusByDate("Report Page");
                    break;
                case 'sales.status.topsheet' :
                    $this->showSalesStatusTRTTopsheet("Report Page");
                    break;
                case 'sales.status_by_cat'    :
                    $this->showSalesStatusByCatagory("Report Page");
                    break;
                case 'sales.status_by_amount' :
                    $this->showSalesStatusByAmount("Report Page");
                    break;
                case 'sales.status_by_trt'   :
                    $this->showSalesStatusByTRT("Report Page");
                    break;
                case 'customer.status.by.trt':
                    $this->showCustomerSalesStatusByTRT();
                    break;
                case 'monthly_trt_status'    :
                    $this->showCustomerMonthlyStatusByTRT("Report Page");
                    break;
                case 'transaction_lst'        :
                    $screen = $this->showTransactionEditor();
                    break;
                case 'due_payment_list'      :
                    $screen = $this->showDuePaymentEditor();
                    break;
                case 'due_receive_list'      :
                    $screen = $this->showDueReceivableEditor();
                    break;
                case 'voucher_print'         :
                    $this->showVoucherPrintEditor(getRequest('voucher_no'));
                    break;
                case 'print_cvoucher'        :
                    $this->showCVPrintEditor($msg);
                    break;
                case 'print_udvoucher'       :
                    $screen = $this->showPrintUndeliveryVoucher($msg);
                    break;
                case 'delete_undelivery'     :
                    $this->showDeleteUndeliveryProduct(getRequest('voucher_no'));
                    break;
                case 'batchlist'        :
                    $this->showBatchList($msg);
                    break;
                case 'show.batch.po.list'    :
                    $this->showBatchProductionList();
                    break;
                case 'delete.batch.po'    :
                    $this->DeleteBatchProduction();
                    break;
                case 'pending.voucher'    :
                    $this->showPendingVoucherList();
                    break;
                case 'pending.money.receipt' :
                    $this->showPendingMoneyReceiptList();
                    break;
                case 'customer_list'         :
                    $screen = $this->showCustomerList();
                    break;
                case 'mis_dashboard_report' :
                    $screen = $this->misDAshboardReport();
                    break;
            }
        } elseif (($u_t_id == 103) || ($u_t_id == 104) || ($u_t_id == 109)) {
            switch ($cmd) {
                case 'sales_ststus'        :
                    $screen = $this->showSalesStatus("Report Page");
                    break;
                case 'product_wise_sl_list'  :
                    $screen = $this->showProductSalesList("Report Page");
                    break;
                case 'product_wise_sl_sum'   :
                    $screen = $this->showProductSalesSummary("Report Page");
                    break;
                case 'sales_details'        :
                    $screen = $this->showSalesDetails("Report Page");
                    break;
                case 'customer_sales_status' :
                    $this->showCustomerSalesDetails("Report Page");
                    break;
                case 'running_production'    :
                    $screen = $this->showRunningProduction("Report Page");
                    break;
                case 'order_list'        :
                    $this->showSalesOrder("Report Page");
                    break;
                case 'bill_list'        :
                    $this->showBillList("Report Page");
                    break;
                case 'pending_order_list'  :
                    $this->showUnapproveSalesOrder("Report Page");
                    break;
                case 'sales.delivery.order':
                    $this->showSalesOrder("Report Page");
                    break;
                case 'sal_dtl'        :
                    $screen = $this->showSalesOrder("Report Page");
                    break;
                case 'sales.return'        :
                    $screen = $this->showSalesOrder("Report Page");
                    break;
                case 'undelivered_list'      :
                    $this->showUndeliveredSalesOrder("Report Page");
                    break;
                case 'sales_delivery_list'   :
                    $screen = $this->showSalesDeliveryChallan("Report Page");
                    break;
                case 'custom_invoice_list'   :
                    $this->showCustomDeliveryChallan("Report Page");
                    break;
                case 'transfer_list'    :
                    $screen = $this->showStockTransferList("Report Page");
                    break;
                case 'pending_transfer_list'    :
                    $screen = $this->showUnapprovedStockTransferList("Report Page");
                    break;
                case 'verify_list'        :
                    $screen = $this->showStockVerifyList("Report Page");
                    break;
                case 'salesreturn_list'    :
                    $screen = $this->showSalesReturnList("Report Page");
                    break;
                case 'cashbook_list'    :
                    $screen = $this->showCashBookList("Report Page");
                    break;
                case 'bankbook_list'    :
                    $screen = $this->showBankBookList("Report Page");
                    break;
                case 'stock_status'        :
                    $screen = $this->showStockStatus("Report Page");
                    break;
                case 'order.po.status'       :
                    $screen = $this->showOrderProductionReq("Report Page");
                    break;
                case 'stock_movement_status' :
                    $this->showStockMovementStatus("Report Page");
                    break;
                case 'stock_movement_topsheet':
                    $this->showStockMovementTopsheet("Report Page");
                    break;
                case 'stock.status_by_cat'   :
                    $this->showStockStatusByCatagory("Report Page");
                    break;
                case 'stock.status_by_date'  :
                    $this->showStockStatusByDate("Report Page");
                    break;
                case 'sales.status_by_date'  :
                    $this->showSalesStatusByDate("Report Page");
                    break;

                case 'unsale.stock_by_date'  :
                    $this->showUnSalesStockStatusByDate("Report Page");
                    break;
                case 'sales.status.topsheet' :
                    $this->showSalesStatusTRTTopsheet("Report Page");
                    break;
                case 'sales.status_by_cat'    :
                    $this->showSalesStatusByCatagory("Report Page");
                    break;
                case 'sales.status_by_amount' :
                    $this->showSalesStatusByAmount("Report Page");
                    break;
                case 'sales.status_by_trt'   :
                    $this->showSalesStatusByTRT("Report Page");
                    break;
                case 'customer.status.by.trt':
                    $this->showCustomerSalesStatusByTRT();
                    break;
                case 'monthly_trt_status'    :
                    $this->showCustomerMonthlyStatusByTRT("Report Page");
                    break;
                case 'transaction_lst'        :
                    $screen = $this->showTransactionEditor();
                    break;
                case 'due_payment_list'      :
                    $screen = $this->showDuePaymentEditor();
                    break;
                case 'due_receive_list'      :
                    $screen = $this->showDueReceivableEditor();
                    break;
                case 'voucher_print'         :
                    $this->showVoucherPrintEditor(getRequest('voucher_no'));
                    break;
                case 'print_cvoucher'        :
                    $this->showCVPrintEditor($msg);
                    break;
                case 'print_udvoucher'       :
                    $screen = $this->showPrintUndeliveryVoucher($msg);
                    break;
                case 'delete_undelivery'     :
                    $this->showDeleteUndeliveryProduct(getRequest('voucher_no'));
                    break;
                case 'batchlist'        :
                    $this->showBatchList($msg);
                    break;
                case 'show.batch.po.list'    :
                    $this->showBatchProductionList();
                    break;
                case 'delete.batch.po'    :
                    $this->DeleteBatchProduction();
                    break;
                case 'pending.voucher'    :
                    $this->showPendingVoucherList();
                    break;
                case 'pending.money.receipt' :
                    $this->showPendingMoneyReceiptList();
                    break;
                case 'customer_list'         :
                    $screen = $this->showCustomerList();
                    break;
                case 'mis_dashboard_report' :
                    $screen = $this->misDAshboardReport();
                    break;
            }
        } elseif (($u_t_id == 105)) {
            switch ($cmd) {

                case 'order_list'        :
                    $this->showSalesOrder("Report Page");
                    break;
                case 'bill_list'        :
                    $this->showBillList("Report Page");
                    break;
                case 'sal_dtl'        :
                    $this->showSalesOrder("Report Page");
                    break;
                case 'sales.return'        :
                    $this->showSalesOrder("Report Page");
                    break;
                case 'undelivered_list'      :
                    $this->showUndeliveredSalesOrder("Report Page");
                    break;
                case 'print_udvoucher'       :
                    $this->showPrintUndeliveryVoucher($msg);
                    break;
                case 'sales_delivery_list'   :
                    $this->showSalesDeliveryChallan("Report Page");
                    break;

                case 'custom_invoice_list'   :
                    $this->showCustomDeliveryChallan("Report Page");
                    break;
                case 'running_production'    :
                    $this->showRunningProduction("Report Page");
                    break;
                case 'transfer_list'    :
                    $this->showStockTransferList("Report Page");
                    break;
                case 'pending_transfer_list'    :
                    $screen = $this->showUnapprovedStockTransferList("Report Page");
                    break;
                case 'verify_list'        :
                    $this->showStockVerifyList("Report Page");
                    break;
                case 'stock_status'        :
                    $this->showStockStatus("Report Page");
                    break;
                case 'stock.status_by_cat'   :
                    $this->showStockStatusByCatagory("Report Page");
                    break;
                case 'stock.status_by_date'  :
                    $this->showStockStatusByDate("Report Page");
                    break;
                case 'sales.status_by_date'  :
                    $this->showSalesStatusByDate("Report Page");
                    break;
                case 'sales.status.topsheet' :
                    $this->showSalesStatusTRTTopsheet("Report Page");
                    break;
                case 'sales.status_by_cat'    :
                    $this->showSalesStatusByCatagory("Report Page");
                    break;
                case 'sales.status_by_amount' :
                    $this->showSalesStatusByAmount("Report Page");
                    break;
                case 'sales.status_by_trt'   :
                    $this->showSalesStatusByTRT("Report Page");
                    break;
                case 'batchlist'        :
                    $this->showBatchList($msg);
                    break;
                case 'customer_list'         :
                    $screen = $this->showCustomerList();
                    break;
                case 'mis_dashboard_report' :
                    $screen = $this->misDAshboardReport();
                    break;
            }
        } else if (($u_t_id == 107)) {
            switch ($cmd) {
                case 'sales_ststus'        :
                    $screen = $this->showSalesStatus("Report Page");
                    break;
                case 'product_wise_sl_list'  :
                    $screen = $this->showProductSalesList("Report Page");
                    break;

                case 'product_wise_sl_sum'   :
                    $screen = $this->showProductSalesSummary("Report Page");
                    break;
                case 'sales_details'        :
                    $screen = $this->showSalesDetails("Report Page");
                    break;
                case 'customer_sales_status' :
                    $this->showCustomerSalesDetails("Report Page");
                    break;
                case 'running_production'    :
                    $screen = $this->showRunningProduction("Report Page");
                    break;
                case 'order_list'        :
                    $this->showSalesOrder("Report Page");
                    break;
                case 'bill_list'        :
                    $this->showBillList("Report Page");
                    break;
                case 'edit_order_list'        :
                    $this->showEditSalesOrder("Report Page");
                    break;
                case 'pending_order_list'  :
                    $this->showUnapproveSalesOrder("Report Page");
                    break;
                case 'sales.delivery.order':
                    $this->showSalesOrder("Report Page");
                    break;
                case 'sal_dtl'        :
                    $screen = $this->showSalesOrder("Report Page");
                    break;
                //case 'sales.return'       	: $screen = $this->showSalesOrder("Report Page");    break;
                case 'undelivered_list'    :
                    $this->showUndeliveredSalesOrder("Report Page");
                    break;
                case 'sales_delivery_list' :
                    $screen = $this->showSalesDeliveryChallan("Report Page");
                    break;
                case 'custom_invoice_list' :
                    $this->showCustomDeliveryChallan("Report Page");
                    break;
                case 'transfer_list'    :
                    $screen = $this->showStockTransferList("Report Page");
                    break;
                case 'pending_transfer_list'    :
                    $screen = $this->showUnapprovedStockTransferList("Report Page");
                    break;
                //case 'verify_list' 	: $screen = $this->showStockVerifyList("Report Page");    break;
                case 'salesreturn_list' :
                    $screen = $this->showSalesReturnList("Report Page");
                    break;
                case 'cashbook_list'    :
                    $screen = $this->showCashBookList("Report Page");
                    break;
                case 'bankbook_list'    :
                    $screen = $this->showBankBookList("Report Page");
                    break;
                case 'stock_status'        :
                    $screen = $this->showStockStatus("Report Page");
                    break;
                case 'order.po.status'       :
                    $screen = $this->showOrderProductionReq("Report Page");
                    break;
                case 'stock_movement_status' :
                    $this->showStockMovementStatus("Report Page");
                    break;
                case 'stock_movement_topsheet':
                    $this->showStockMovementTopsheet("Report Page");
                    break;
                case 'stock.status_by_cat'   :
                    $this->showStockStatusByCatagory("Report Page");
                    break;
                case 'stock.status_by_date'  :
                    $this->showStockStatusByDate("Report Page");
                    break;
                case 'stock.status_by_date'  :
                    $this->showStockStatusByDate("Report Page");
                    break;
                case 'unsale.stock_by_date'  :
                    $this->showUnSalesStockStatusByDate("Report Page");
                    break;
                case 'sales.status.topsheet' :
                    $this->showSalesStatusTRTTopsheet("Report Page");
                    break;
                case 'sales.status_by_cat'    :
                    $this->showSalesStatusByCatagory("Report Page");
                    break;
                case 'sales.status_by_amount' :
                    $this->showSalesStatusByAmount("Report Page");
                    break;
                case 'sales.status_by_trt'   :
                    $this->showSalesStatusByTRT("Report Page");
                    break;
                case 'customer.status_by_trt':
                    $this->showCustomerStatusByTRT("Report Page");
                    break;
                case 'customer.status.by.trt':
                    $this->showCustomerSalesStatusByTRT();
                    break;
                case 'monthly_trt_status'    :
                    $this->showCustomerMonthlyStatusByTRT("Report Page");
                    break;
                case 'transaction_lst'        :
                    $screen = $this->showTransactionEditor();
                    break;
                //case 'due_payment_list'      : $screen = $this->showDuePaymentEditor();   break;
                //case 'due_receive_list'      : $screen = $this->showDueReceivableEditor();   break;
                case 'customer_list'         :
                    $screen = $this->showCustomerList();
                    break;
                case 'mis_dashboard_report' :
                    $screen = $this->misDAshboardReport();
                    break;
                case 'voucher_print'         :
                    $this->showVoucherPrintEditor(getRequest('voucher_no'));
                    break;
                case 'print_cvoucher'        :
                    $this->showCVPrintEditor($msg);
                    break;
                case 'print_udvoucher'       :
                    $screen = $this->showPrintUndeliveryVoucher($msg);
                    break;
                case 'delete_undelivery'     :
                    $this->showDeleteUndeliveryProduct(getRequest('voucher_no'));
                    break;
                case 'batchlist'        :
                    $this->showBatchList($msg);
                    break;
                case 'show.batch.po.list'    :
                    $this->showBatchProductionList();
                    break;

            }
        } else {
            header("location:index.php?app=user_home&msg=You are not authorised !!!");
        }
        if ($cmd == 'list') {
            if ($deleted = getRequest('deleted')) {
                if ($deleted == 'yes') {
                    $screen['message'] = "Item Deleted Successfully";
                } else {
                    $screen['message'] = "Item Deletion Failure";
                }
            }
            require_once(CURRENT_APP_SKIN_FILE);
        }
        return true;
    }

    function showCustomerList()
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();

        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['area_list'] = $comListApp->getDistrictList();
        $data['district_list'] = $comListApp->getDistrictList();
        $data['trt_list'] = $comListApp->getAreaList();
        $data = array();
        $project_id = getFromSession('project_id');
        if (getRequest('submit')) {
            $data['record_list'] = $this->getCusromerList(getRequest('from'), getRequest('to'));
            $data['totalrecord'] = $this->getCustomerTotalList();
        }
        require_once(SHOW_CUSTOMER_LIST_SKIN);
        return $data[0];
    }

    function getCusromerList($from, $to)
    {
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 200;
        }
        $division = getRequest('division_id');
        $district = getRequest('district');
        $area = getRequest('area');
        $customer = getRequest('customer');
        $info = array();
        $info['table'] = SUB_ACC_HEAD_TBL . ' c,' . DIVISION_TBL . ' d,' . DISTRICT_TBL . ' a,' . AREA_TBL . ' t,' . PROJECT_TBL . ' p';
        $info['fields'] = array('c.*', 'd.division_name_eng', 'a.district_name', 't.area_name', "DATE_FORMAT(c.modified_time,'%d %b %y' ) as modified_datetime");

        $csql = "c.division = d.division_id AND c.district = a.district_id AND c.area = t.area_id AND c.project_id=p.project_id AND c.`head_type` = 'Current Assets' AND c.`sub_headtype` = 'S128' AND c.`child_head` = 'C000105' AND  c.status=1 ";
        if ($division_id > 0) {
            $csql .= " AND c.division=$division_id";
        }
        if ($district_id > 0) {
            $csql .= " AND c.district=$district_id";
        }
        if ($area_id != "") {
            $csql .= " AND c.area='$area_id'";
        }
        if ($customer != "") {
            $csql .= " AND c.sub_head_name LIKE '%$customer%'";
        }

        $info['where'] = $csql;
        $info['groupby'] = array("c.sub_id");
        $info['orderby'] = array("c.sub_id ASC LIMIT $from,$to");
        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }
        return $data;
    }

    function getCustomerTotalList()
    {

        $info['table'] = SUB_ACC_HEAD_TBL . ' c,' . DIVISION_TBL . ' d,' . DISTRICT_TBL . ' a,' . AREA_TBL . ' t,' . PROJECT_TBL . ' p';
        $info['fields'] = array('c.*', 'd.division_name_eng', 'a.district_name', 't.area_name', "DATE_FORMAT(c.modified_time,'%d %b %y' ) as modified_datetime");

        $csql = "c.division = d.division_id AND c.district = a.district_id AND c.area = t.area_id AND c.project_id=p.project_id AND c.`head_type` = 'Current Assets' AND c.`sub_headtype` = 'S128' AND c.`child_head` = 'C000105' AND  c.status=1 ";
        if ($division_id > 0) {
            $csql .= " AND c.division=$division_id";
        }
        if ($district_id > 0) {
            $csql .= " AND c.district=$district_id";
        }
        if ($area_id != "") {
            $csql .= " AND c.area='$area_id'";
        }
        if ($customer != "") {
            $csql .= " AND c.sub_head_name LIKE '%$customer%'";
        }
        $info['where'] = $csql;
        $info['groupby'] = array("c.sub_id");
        $info['orderby'] = array("c.sub_id ASC");

        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            return $cnt;
        }

    }

    function showSalesStatus($msg = null)
    {
        require_once(CLASS_DIR . '/sales.class.php');
        $salesApp = new Sales();
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        require_once(CLASS_DIR . '/common.class.php');
        $comApp = new Common();
        $data = array();
        $project_id = getFromSession('project_id');
        if (getRequest('submit')) {
            $data['record_list'] = $this->getSalesStatusList(getRequest('from'), getRequest('to'));
            $data['totalrecord'] = $this->getTotalSalesStatusList(getRequest('from'), getRequest('to'));

            $page_no = getRequest('page_no');
            if (!isset($page_no) || $page_no < 1) {
                $page_no = 1;
            }
            $data['page_no'] = $page_no;
        }
        $data['catagory_list'] = $comListApp->getCatagoryList();
        $data['district_list'] = $comListApp->getDistrictList();
        $data['area_list'] = $comListApp->getAreaList();
        $data['customer_list'] = $comListApp->getCustomerList();
        require_once(CURRENT_APP_SKIN_FILE);
        return $data[0];
    }

    function getSalesStatusList($from, $to)
    {
        $page_no = getRequest('page_no');
        if (!isset($page_no) || $page_no < 1) {
            $page_no = 1;
        }

        $page_size = 500;

        // Calculate offset
        $from = ($page_no - 1) * $page_size;
        $to = $page_size;

        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $project_id = getFromSession('project_id');
        $catagory = getRequest('catagory');
        $subcatagory = getRequest('subcatagory');
        $product = getRequest('product');
        $customer = getRequest('customer');
        $division = getRequest('division_id');

        $district = getRequest('district');
        $area = getRequest('area');
        $order_by = getRequest('order_by');
        $salesby = getRequest('salesby');
        $product_catagory = getRequest('product_catagory');
        $info = array();

        // $info['table']  = SALES_MASTER_TBL.' sm,'.SALES_DETAILS_TBL.' sd,'.PROJECT_TBL.' p,'.PRODUCT_TBL.' po,'.CATAGORY_TBL.' ct,'.BRAND_TBL.' b';
        //$info['fields'] = array('sd.voucher_no','p.project_name','p.location',"DATE_FORMAT(sm.sales_date,'%d %b %y' ) as sales_date",'ct.catagory_name','b.brand_name',"sd.product","po.product_name","po.product_desc","po.m_unit","po.unit_price");

        //$sql="sm.voucher_no = sd.voucher_no AND po.product_id=sd.product AND po.catagory = ct.catagory_code AND po.brand_code = b.brand_id AND sm.project_id = sd.project_id AND sd.project_id = p.project_id AND sm.project_id = '".$project_id."' AND sd.project_id = '".$project_id."' AND sm.status=1 ";


        //if($catagory!=""){
        //	$sql.=" AND po.catagory = '$catagory'";
        //}
        //if($subcatagory!=""){
        //	$sql.=" AND po.subcatagory = '$subcatagory'";
        //}
        //if($product!=""){
        //	$sql.=" AND po.product_id = '$product'";
        //}

        //if($division!=""){
        //	$sql.=" AND sd.division = '$division'";
        //}
        //if($district!=""){
        //	$sql.=" AND sd.district = '$district'";
        //}
        //if($area!=""){
        //	$sql.=" AND sd.area = '$area'";
        //}
        //if($date_from!="" && $date_to ==""){
        //	$sql.=" AND sm.sales_date >= '$date_from'";
        //}elseif($date_from=="" && $date_to !=""){
        //	$sql.=" AND sm.sales_date <= '$date_to'";
        //}elseif($date_from!="" && $date_to !=""){
        //	$sql.=" AND sm.sales_date BETWEEN '$date_from' AND '$date_to'";
        //}
        //$info['where']  =$sql;
        //$info['groupby'] = array("po.product_id");
        //if($order_by=="Max Sales" && $salesby=="Qty"){
        //$info['orderby'] = array("SUM(sd.qty) DESC LIMIT $from,$to");
        //}elseif($order_by=="Min Sales" && $salesby=="Qty"){
        //$info['orderby'] = array("SUM(sd.qty) ASC LIMIT $from,$to");
        //}elseif($order_by=="Max Sales" && $salesby=="Amount"){
        //$info['orderby'] = array("SUM(sd.total) DESC LIMIT $from,$to");
        //}elseif($order_by=="Min Sales" && $salesby=="Amount"){
        //$info['orderby'] = array("SUM(sd.total) ASC LIMIT $from,$to");
        //}

        $info['table'] = SALES_MASTER_TBL . ' m,' . SALES_DETAILS_TBL . ' s, sales_delivery_item_master sdm, sales_delivery_item sdc,' . PRODUCT_TBL . ' po,' . SUB_ACC_HEAD_TBL . ' cu, currency c';
        // Select Fields
        $info['fields'] = array(
            "s.voucher_no",
            "po.product_id",
            "s.product",
            "po.product_code",
            "po.product_name",
            "cu.sub_head_name AS customer",
            "cu.code AS customer_code",
            "s.details AS productName",
            "po.product_desc",
            "po.m_unit",
            "po.unit_price",
            "s.qty AS sales_qty",
            "s.delivery_qty AS sales_delivery_qty",
            "sdc.delivery_qty AS delivery_qty",
            "s.total AS sales_amount",
            "sdc.total_amount AS delivery_amount",
            "c.curr_symble"
        );

        // WHERE conditions with JOINs
        $sql = "m.voucher_no = s.voucher_no
				AND m.voucher_no = sdm.voucher_no
				AND sdm.voucher_no = sdc.voucher_no
				AND s.product = sdc.product
				AND cu.sub_id = m.customer
				AND s.product = po.product_id
				AND m.currency = c.currency_id
				AND m.project_id = '$project_id'
				AND m.status = 1";

        if ($customer != "") {
            $sql .= " AND m.customer = '$customer'";
        }
        if ($catagory != "") {
            $sql .= " AND po.catagory = '$catagory'";
        }
        if ($subcatagory != "") {
            $sql .= " AND po.subcatagory = '$subcatagory'";
        }
        if ($product_catagory != "") {
            $sql .= " AND po.product_catagory = '$product_catagory'";
        }
        if ($product != "") {
            $sql .= " AND s.product = '$product'";
        }
        if ($division != "") {
            $sql .= " AND m.division = '$division'";
        }
        if ($district != "") {
            $sql .= " AND m.district = '$district'";
        }
        if ($area != "") {
            $sql .= " AND m.area = '$area'";
        }

        if ($date_from != "" && $date_to == "") {
            $sql .= " AND sdm.delivery_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND sdm.delivery_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND sdm.delivery_date BETWEEN '$date_from' AND '$date_to'";
        }

        $info['where'] = $sql;

        // GROUP BY
        $info['groupby'] = array("po.product_id", "sdm.voucher_no");

        // ORDER BY
        if ($order_by == "Max Sales" && $salesby == "Qty") {
            $info['orderby'] = array("SUM(sdc.delivery_qty) DESC LIMIT $from,$to");
        } elseif ($order_by == "Min Sales" && $salesby == "Qty") {
            $info['orderby'] = array("SUM(sdc.delivery_qty) ASC LIMIT $from,$to");
        } elseif ($order_by == "Max Sales" && $salesby == "Amount") {
            $info['orderby'] = array("SUM(sdc.total_amount) DESC LIMIT $from,$to");
        } elseif ($order_by == "Min Sales" && $salesby == "Amount") {
            $info['orderby'] = array("SUM(sdc.total_amount) ASC LIMIT $from,$to");
        }

        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }
        return $data;
    }

    function getTotalSalesStatusList($from, $to)
    {
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $project_id = getFromSession('project_id');
        $catagory = getRequest('catagory');
        $subcatagory = getRequest('subcatagory');
        $product = getRequest('product');
        $customer = getRequest('customer');
        $division = getRequest('division_id');
        $district = getRequest('district');
        $area = getRequest('area');
        $order_by = getRequest('order_by');
        $salesby = getRequest('salesby');
        $product_catagory = getRequest('product_catagory');
        $info = array();

        //$info['table']  = SALES_MASTER_TBL.' sm,'.SALES_DETAILS_TBL.' sd,'.PRODUCT_TBL.' po,'.CATAGORY_TBL.' ct,'.BRAND_TBL.' b';
        //$info['fields'] = array('sd.voucher_no','po.product_id');

        //$sql="sm.voucher_no = sd.voucher_no AND po.product_id=sd.product AND po.catagory = ct.catagory_code AND po.brand_code = b.brand_id AND sm.status=1 ";

        //if($catagory!=""){
        //	$sql.=" AND po.catagory = '$catagory'";
        //}
        //if($subcatagory!=""){
        //	$sql.=" AND po.subcatagory = '$subcatagory'";
        //}
        //if($product!=""){
        //	$sql.=" AND po.product_id = '$product'";
        //}
        //($division!=""){
        //	$sql.=" AND sd.division = '$division'";
        //}
        //	  if($district!=""){
        //            $sql.=" AND sd.district = '$district'";
        //        }
        //        if($area!=""){
        //            $sql.=" AND sd.area = '$area'";
        //        }
        //        if($date_from!="" && $date_to ==""){
        //            $sql.=" AND sm.sales_date >= '$date_from'";
        //        }elseif($date_from=="" && $date_to !=""){
        //            $sql.=" AND sm.sales_date <= '$date_to'";
        //        }elseif($date_from!="" && $date_to !=""){
        //            $sql.=" AND sm.sales_date BETWEEN '$date_from' AND '$date_to'";
        //        }
        //        $info['where']  = $sql;
        //        $info['groupby'] = array("po.product_id");
        //        if($order_by=="Max Sales" && $salesby=="Qty"){
        //            $info['orderby'] = array("SUM(sd.qty) DESC LIMIT $from,$to");
        //        }elseif($order_by=="Min Sales" && $salesby=="Qty"){
        //            $info['orderby'] = array("SUM(sd.qty) ASC LIMIT $from,$to");
        //        }elseif($order_by=="Max Sales" && $salesby=="Amount"){
        //            $info['orderby'] = array("SUM(sd.total) DESC LIMIT $from,$to");
        //        }elseif($order_by=="Min Sales" && $salesby=="Amount"){
        //            $info['orderby'] = array("SUM(sd.total) ASC LIMIT $from,$to");
        //        }

        $info['table'] = SALES_MASTER_TBL . ' m,' . SALES_DETAILS_TBL . ' s, sales_delivery_item_master sdm, sales_delivery_item sdc,' . PRODUCT_TBL . ' po,' . SUB_ACC_HEAD_TBL . ' cu, currency c';
        // Select Fields
        $info['fields'] = array(
            "s.voucher_no",
            "po.product_id",
            "s.product",
            "po.product_name",
            "cu.sub_head_name AS customer",
            "s.details AS productName",
            "po.product_desc",
            "po.m_unit",
            "po.unit_price",
            "s.qty AS sales_qty",
            "s.delivery_qty AS sales_delivery_qty",
            "sdc.delivery_qty AS delivery_qty",
            "s.total AS sales_amount",
            "sdc.total_amount AS delivery_amount",
            "c.curr_symble"
        );

        // WHERE conditions with JOINs
        $sql = "m.voucher_no = s.voucher_no
				AND m.voucher_no = sdm.voucher_no
				AND sdm.voucher_no = sdc.voucher_no
				AND s.product = sdc.product
				AND cu.sub_id = m.customer
				AND s.product = po.product_id
				AND m.currency = c.currency_id
				AND m.project_id = '$project_id'
				AND m.status = 1";

        if ($customer != "") {
            $sql .= " AND m.customer = '$customer'";
        }
        if ($catagory != "") {
            $sql .= " AND po.catagory = '$catagory'";
        }
        if ($subcatagory != "") {
            $sql .= " AND po.subcatagory = '$subcatagory'";
        }
        if ($product_catagory != "") {
            $sql .= " AND po.product_catagory = '$product_catagory'";
        }
        if ($product != "") {
            $sql .= " AND s.product = '$product'";
        }
        if ($division != "") {
            $sql .= " AND m.division = '$division'";
        }
        if ($district != "") {
            $sql .= " AND m.district = '$district'";
        }
        if ($area != "") {
            $sql .= " AND m.area = '$area'";
        }

        if ($date_from != "" && $date_to == "") {
            $sql .= " AND sdm.delivery_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND sdm.delivery_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND sdm.delivery_date BETWEEN '$date_from' AND '$date_to'";
        }

        $info['where'] = $sql;

        // GROUP BY
        $info['groupby'] = array("po.product_id", "sdm.voucher_no");

        //$info['debug']  	= true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            return $cnt;
        } else {
            return 0;
        }
    }

    //============ Product Wise Sales List ==========
    function showProductSalesList($msg = null)
    {
        require_once(CLASS_DIR . '/sales.class.php');
        $salesApp = new Sales();
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        require_once(CLASS_DIR . '/common.class.php');
        $comApp = new Common();
        $data = array();
        $project_id = getFromSession('project_id');
        if (getRequest('submit') || $_REQUEST['date_from'] != "") {
            $data['record_list'] = $this->getProductSalesList(getRequest('from'), getRequest('to'));
            $data['totalrecord'] = $this->getTotalProductSalesList();
        }
        $data['product_list'] = $comListApp->getProductList();
        $data['depo_list'] = $comListApp->getDeliveryPointList();
        $data['district_list'] = $comListApp->getDistrictList();
        $data['area_list'] = $comListApp->getAreaList();
        require_once(PRODUCT_WISE_SALES_SKIN_FILE);
        return $data[0];
    }

    function getProductSalesList($from, $to)
    {
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 200;
        }
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $project_id = getFromSession('project_id');
        $delivery_point = getRequest('delivery_point');
        $product = getRequest('product');
        $district = getRequest('district');
        $area = getRequest('area');
        $order_by = getRequest('order_by');

        $info = array();

        $info['table'] = SALES_MASTER_TBL . ' sm,' . SALES_DETAILS_TBL . ' sd,' . SALES_DELIVERY_MASTER_TBL . ' sdm,' . SUB_ACC_HEAD_TBL . ' s,' . PROJECT_TBL . ' p,' . DELIVERY_POINT_TBL . ' as dp,' . PRODUCT_TBL . ' po,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('sd.voucher_no', 'sdm.sales_delivery_master_id', 'p.project_name', 'dp.delivery_point_name', 'p.location', 'sm.customer', 's.sub_head_name', 's.head_details', "DATE_FORMAT(sm.sales_date,'%d %b %y' ) as sales_date", 'c.curr_symble', 'AVG(sd.unit_price) as unit_price', 'SUM(sd.qty) as sales_qty', 'SUM(sd.delivery_qty) as delivery_qty', 'SUM(sd.return_qty) as return_qty', 'SUM(sd.undelivery_qty) as undelivery_qty', "sd.product", "po.product_name", "po.product_desc", "po.m_unit");

        $sql = "sm.customer = s.sub_id AND sm.voucher_no = sd.voucher_no AND sm.voucher_no = sdm.voucher_no AND sm.delivery_point=dp.delivery_pid AND sd.product = po.product_id AND sd.project_id = p.project_id AND sm.currency = c.currency_id AND 
		sd.project_id = '" . $project_id . "' AND sm.status=1";

        if ($delivery_point != "") {
            $sql .= " AND sm.delivery_point = '$delivery_point'";
        }
        if ($product != "") {
            $sql .= " AND po.product_id = '$product'";
        }
        if ($district != "") {
            $sql .= " AND s.district = '$district'";
        }
        if ($area != "") {
            $sql .= " AND s.area = '$area'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND sm.sales_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND sm.sales_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND sm.sales_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['groupby'] = array("sd.sal_detail_id");
        if ($order_by == "Max Sales") {
            $info['orderby'] = array("SUM(sd.qty) DESC LIMIT $from,$to");
        } else {
            $info['orderby'] = array("SUM(sd.qty) ASC LIMIT $from,$to");
        }
        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }

        /*
		$SQL = "
		SELECT sd.voucher_no,sdm.sales_delivery_master_id,p.project_name,dp.delivery_point_name,p.location,sm.customer,COALESCE((s.sub_id), (sp.supplier_code)) as sub_id,COALESCE((s.sub_head_name), (sp.name)) as sub_head_name,COALESCE((s.head_details), (sp.address)) as head_details,DATE_FORMAT(sm.sales_date,'%d %b %y' ) as sales_date,c.curr_symble,ct.catagory_name,b.brand_name,AVG(sd.unit_price) as unit_price,SUM(sd.qty) as sales_qty,SUM(sd.delivery_qty) as delivery_qty,SUM(sd.return_qty) as return_qty,SUM(sd.undelivery_qty) as undelivery_qty,sd.product,po.product_name,po.product_desc,po.m_unit
		FROM ".SALES_MASTER_TBL." sm
		LEFT JOIN ".SALES_DETAILS_TBL." sd ON sd.voucher_no =sm.voucher_no
		LEFT JOIN ".SALES_DELIVERY_MASTER_TBL." sdm ON sdm.voucher_no =sd.voucher_no
		LEFT JOIN ".SUB_ACC_HEAD_TBL." s ON BINARY s.sub_id =sm.customer
		LEFT JOIN ".SUPPLIER_TBL." sp ON BINARY sp.supplier_code = sm.customer
		LEFT JOIN ".PROJECT_TBL." p ON p.project_id  =sm.project_id
		LEFT JOIN ".DELIVERY_POINT_TBL." dp ON dp.delivery_pid  =sm.delivery_point
		LEFT JOIN ".PRODUCT_TBL." po ON po.product_id  =sd.product
		LEFT JOIN ".CATAGORY_TBL." ct ON ct.catagory_code  =po.catagory
		LEFT JOIN ".BRAND_TBL." b ON b.brand_id  =sd.brand_id
		LEFT JOIN ".CURRENCY_TBL." c ON c.currency_id  =sm.currency

		WHERE sm.project_id = '".$project_id."'
		";

		if($delivery_point!=""){
			$SQL.=" AND sm.delivery_point = '$delivery_point'";
		}
		if($product!=""){
			$SQL.=" AND sd.product = '$product'";
		}
		if($district!=""){
			$SQL.=" AND s.district = '$district'";
		}
		if($area!=""){
			$SQL.=" AND s.area = '$area'";
		}
		if($date_from!="" && $date_to ==""){
			$SQL.=" AND sm.sales_date >= '$date_from'";
		}elseif($date_from=="" && $date_to !=""){
			$SQL.=" AND sm.sales_date <= '$date_to'";
		}elseif($date_from!="" && $date_to !=""){
			$SQL.=" AND sm.sales_date BETWEEN '$date_from' AND '$date_to'";
		}
		$SQL.=" GROUP BY sd.sal_detail_id";
		if($order_by=="Max Sales"){
		$SQL.=" ORDER BY SUM(sd.qty) DESC LIMIT $from,$to";
		}else{
		$SQL.=" ORDER BY SUM(sd.qty) ASC LIMIT $from,$to";
		}

		//echo $SQL;

		$result         = query($SQL);
		//$result = _mysql_query_wrapper($SQL);

		$data           = array();
		$cnt = count($result);
		if($cnt) {
			foreach($result as $value)  {
			$data[]	= $value;
			}
		}
		*/
        return $data;
    }

    function getTotalProductSalesList()
    {
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $project_id = getFromSession('project_id');
        $delivery_point = getRequest('delivery_point');
        $product = getRequest('product');
        $district = getRequest('district');
        $area = getRequest('area');
        $order_by = getRequest('order_by');

        $info = array();
        $info['table'] = SALES_MASTER_TBL . ' sm,' . SALES_DETAILS_TBL . ' sd,' . SALES_DELIVERY_MASTER_TBL . ' sdm,' . SUB_ACC_HEAD_TBL . ' s,' . PROJECT_TBL . ' p,' . DELIVERY_POINT_TBL . ' as dp,' . PRODUCT_TBL . ' po,' . CATAGORY_TBL . ' ct,' . BRAND_TBL . ' b,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('sd.voucher_no');

        $sql = "sm.customer = s.sub_id AND sm.voucher_no = sd.voucher_no AND sm.voucher_no = sdm.voucher_no AND sm.delivery_point=dp.delivery_pid AND BINARY sd.product = po.product_id AND sd.project_id = p.project_id AND sm.currency = c.currency_id AND 
		sd.catagory = ct.catagory_code AND sd.brand_id = b.brand_id AND sd.project_id = '" . $project_id . "' AND sm.status=1";

        if ($delivery_point != "") {
            $sql .= " AND sm.delivery_point = '$delivery_point'";
        }
        if ($product != "") {
            $sql .= " AND sd.product = '$product'";
        }
        if ($district != "") {
            $sql .= " AND s.district = '$district'";
        }
        if ($area != "") {
            $sql .= " AND s.area = '$area'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND sm.sales_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND sm.sales_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND sm.sales_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['groupby'] = array("sd.sal_detail_id");
        if ($order_by == "Max Sales") {
            $info['orderby'] = array("SUM(sd.qty) DESC");
        } else {
            $info['orderby'] = array("SUM(sd.qty) ASC");
        }
        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            return $cnt;
        } else {
            return 0;
        }
        /*
		$SQL = "
		SELECT sd.voucher_no,sdm.sales_delivery_master_id,p.project_name,dp.delivery_point_name,p.location,sm.customer,COALESCE((s.sub_id), (sp.supplier_code)) as sub_id,COALESCE((s.sub_head_name), (sp.name)) as sub_head_name,COALESCE((s.head_details), (sp.address)) as head_details,DATE_FORMAT(sm.sales_date,'%d %b %y' ) as sales_date,c.curr_symble,ct.catagory_name,b.brand_name,AVG(sd.unit_price) as unit_price,SUM(sd.qty) as sales_qty,SUM(sd.delivery_qty) as delivery_qty,SUM(sd.return_qty) as return_qty,SUM(sd.undelivery_qty) as undelivery_qty,sd.product,po.product_name,po.product_desc,po.m_unit
		FROM ".SALES_MASTER_TBL." sm
		LEFT JOIN ".SALES_DETAILS_TBL." sd ON sd.voucher_no =sm.voucher_no
		LEFT JOIN ".SALES_DELIVERY_MASTER_TBL." sdm ON sdm.voucher_no =sd.voucher_no
		LEFT JOIN ".SUB_ACC_HEAD_TBL." s ON BINARY s.sub_id =sm.customer
		LEFT JOIN ".SUPPLIER_TBL." sp ON BINARY sp.supplier_code = sm.customer
		LEFT JOIN ".PROJECT_TBL." p ON p.project_id  =sm.project_id
		LEFT JOIN ".DELIVERY_POINT_TBL." dp ON dp.delivery_pid  =sm.delivery_point
		LEFT JOIN ".PRODUCT_TBL." po ON po.product_id  =sd.product
		LEFT JOIN ".CATAGORY_TBL." ct ON ct.catagory_code  =po.catagory
		LEFT JOIN ".BRAND_TBL." b ON b.brand_id  =sd.brand_id
		LEFT JOIN ".CURRENCY_TBL." c ON c.currency_id  =sm.currency

		WHERE sm.project_id = '".$project_id."'
		";

		if($delivery_point!=""){
			$SQL.=" AND sm.delivery_point = '$delivery_point'";
		}
		if($product!=""){
			$SQL.=" AND sd.product = '$product'";
		}
		if($district!=""){
			$SQL.=" AND s.district = '$district'";
		}
		if($area!=""){
			$SQL.=" AND s.area = '$area'";
		}
		if($date_from!="" && $date_to ==""){
			$SQL.=" AND sm.sales_date >= '$date_from'";
		}elseif($date_from=="" && $date_to !=""){
			$SQL.=" AND sm.sales_date <= '$date_to'";
		}elseif($date_from!="" && $date_to !=""){
			$SQL.=" AND sm.sales_date BETWEEN '$date_from' AND '$date_to'";
		}
		$SQL.=" GROUP BY sd.sal_detail_id";
		if($order_by=="Max Sales"){
		$SQL.=" ORDER BY SUM(sd.qty) DESC";
		}else{
		$SQL.=" ORDER BY SUM(sd.qty) ASC";
		}

		//echo $SQL;

		$result         = query($SQL);
		//$result = _mysql_query_wrapper($SQL);

		$cnt = count($result);
		if($cnt) {
			return $cnt;
		}else {
		  return 0;
		}
		*/
    }

    function showProductSalesSummary($msg = null)
    {
        require_once(CLASS_DIR . '/sales.class.php');
        $salesApp = new Sales();
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        require_once(CLASS_DIR . '/common.class.php');
        $comApp = new Common();
        $data = array();
        $project_id = getFromSession('project_id');
        if (getRequest('submit') || $_REQUEST['date_from'] != "") {
            $data['record_list'] = $this->getProductSalesSummaryList(getRequest('from'), getRequest('to'));
            $data['totalrecord'] = $this->getTotalProductSalesSummaryList();
        }
        $data['catagory_list'] = $comListApp->getCatagoryList();
        $data['product_list'] = $comListApp->getProductList();
        $data['depo_list'] = $comListApp->getDeliveryPointList();
        $data['district_list'] = $comListApp->getDistrictList();
        $data['area_list'] = $comListApp->getAreaList();
        require_once(PRODUCT_WISE_SALES_SUMMARY_FILE);
        return $data[0];
    }

    function getProductSalesSummaryList($from, $to)
    {
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 200;
        }
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $project_id = getFromSession('project_id');
        $delivery_point = getRequest('delivery_point');
        $catagory = getRequest('catagory');
        $product = getRequest('product');
        $division = getRequest('division_id');
        $district = getRequest('district');
        $area = getRequest('area');
        $order_by = getRequest('order_by');

        $info = array();

        $info['table'] = SALES_MASTER_TBL . ' sm,' . SALES_DETAILS_TBL . ' sd,' . SALES_DELIVERY_MASTER_TBL . ' sdm,' . SALES_DELIVERY_CHALLAN_TBL . ' sdc,' . PROJECT_TBL . ' p,' . DELIVERY_POINT_TBL . ' as dp,' . PRODUCT_TBL . ' po,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('sd.voucher_no', 'sdm.sales_delivery_master_id', 'p.project_name', 'sm.delivery_point', 'dp.delivery_point_name', 'p.location', 'sm.customer', "DATE_FORMAT(sm.sales_date,'%d %b %y' ) as sales_date", 'c.curr_symble', 'po.unit_price as sales_price', 'AVG(sd.unit_price) as unit_price', 'SUM(sd.qty) as sales_qty', 'SUM(sdc.delivery_qty) as delivery_qty', 'SUM(sd.return_qty) as return_qty', 'SUM(sd.undelivery_qty) as undelivery_qty', "sd.product", "po.product_name", "po.product_desc", "po.m_unit");

        $sql = "sm.voucher_no = sd.voucher_no AND sm.voucher_no = sdm.voucher_no AND sdm.voucher_no=sdc.voucher_no AND sm.delivery_point=dp.delivery_pid AND sd.product = po.product_id AND sd.project_id = p.project_id AND sm.currency = c.currency_id AND 
		sd.project_id = '" . $project_id . "' AND sm.status=1";

        if ($delivery_point != "") {
            $sql .= " AND sm.delivery_point = '$delivery_point'";

        }
        if ($catagory != "") {
            $sql .= " AND po.catagory = '$catagory'";
        }
        if ($product != "") {
            $sql .= " AND po.product_id = '$product'";
        }
        if ($division != "") {
            $sql .= " AND sm.division = '$division'";
        }
        if ($district != "") {
            $sql .= " AND sm.district = '$district'";
        }
        if ($area != "") {
            $sql .= " AND sm.area = '$area'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND sm.sales_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND sm.sales_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND sm.sales_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['groupby'] = array("po.product_id");
        if ($order_by == "Max Sales") {
            $info['orderby'] = array("SUM(sd.qty) DESC LIMIT $from,$to");
        } else {
            $info['orderby'] = array("SUM(sd.qty) ASC LIMIT $from,$to");
        }
        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }


        return $data;
    }

    function getTotalProductSalesSummaryList()
    {
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $project_id = getFromSession('project_id');
        $delivery_point = getRequest('delivery_point');
        $catagory = getRequest('catagory');
        $product = getRequest('product');
        $division = getRequest('division_id');
        $district = getRequest('district');
        $area = getRequest('area');
        $order_by = getRequest('order_by');

        $info['table'] = SALES_MASTER_TBL . ' sm,' . SALES_DETAILS_TBL . ' sd,' . SALES_DELIVERY_MASTER_TBL . ' sdm,' . SALES_DELIVERY_CHALLAN_TBL . ' sdc,' . SUB_ACC_HEAD_TBL . ' s,' . PROJECT_TBL . ' p,' . DELIVERY_POINT_TBL . ' as dp,' . PRODUCT_TBL . ' po,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('sd.voucher_no', 'sdm.sales_delivery_master_id', 'p.project_name', 'dp.delivery_point_name', 'p.location', 'sm.customer', 's.sub_head_name', 's.head_details', "DATE_FORMAT(sm.sales_date,'%d %b %y' ) as sales_date", 'c.curr_symble', 'AVG(sd.unit_price) as unit_price', 'SUM(sd.qty) as sales_qty', 'SUM(sdc.delivery_qty) as delivery_qty', 'SUM(sd.return_qty) as return_qty', 'SUM(sd.undelivery_qty) as undelivery_qty', "sd.product", "po.product_name", "po.product_desc", "po.m_unit");

        $sql = "sm.customer = s.sub_id AND sm.voucher_no = sd.voucher_no AND sm.voucher_no = sdm.voucher_no AND sdm.voucher_no=sdc.voucher_no AND sm.delivery_point=dp.delivery_pid AND sd.product = po.product_id AND sd.project_id = p.project_id AND sm.currency = c.currency_id AND 
		sd.project_id = '" . $project_id . "' AND sm.status=1";

        if ($delivery_point != "") {
            $sql .= " AND sm.delivery_point = '$delivery_point'";
        }

        if ($catagory != "") {
            $sql .= " AND po.catagory = '$catagory'";
        }
        if ($product != "") {
            $sql .= " AND po.product_id = '$product'";
        }
        if ($division != "") {
            $sql .= " AND sm.division = '$division'";
        }
        if ($district != "") {
            $sql .= " AND sm.district = '$district'";
        }
        if ($area != "") {
            $sql .= " AND sm.area = '$area'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND sm.sales_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND sm.sales_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND sm.sales_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['groupby'] = array("po.product_id");
        if ($order_by == "Max Sales") {
            $info['orderby'] = array("SUM(sd.qty) DESC");
        } else {
            $info['orderby'] = array("SUM(sd.qty) ASC");
        }
        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            return $cnt;
        } else {
            return 0;
        }
    }

    //============ Stock Status By Catagory ===========
    function showStockStatusByCatagory($msg = null)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['product_list'] = $comListApp->getProductList();
        $data['depo_list'] = $comListApp->getDeliveryPointList();
        $data['catagory_list'] = $comListApp->getCatagoryList();
        require_once(STOCK_STATUS_LIST_SKIN_BY_CATAGORY);
        return $data[0];
    }

    //============ Stock Status By Catagory ===========
    function showStockStatusByDate($msg = null)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['product_list'] = $comListApp->getProductList();
        $data['depo_list'] = $comListApp->getDeliveryPointList();
        $data['catagory_list'] = $comListApp->getCatagoryList();
        require_once(STOCK_STATUS_LIST_SKIN_BY_DATE);
        return $data[0];
    }

    //============ Unsales Stock Status By Catagory ===========
    function showUnSalesStockStatusByDate($msg = null)
    { //ini_set("display_errors","on"); echo "Un";
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['product_list'] = $comListApp->getProductList();
        $data['depo_list'] = $comListApp->getDeliveryPointList();
        $data['catagory_list'] = $comListApp->getCatagoryList();
        require_once(UNSALES_PRODUCT_BY_DATE_SKIN_FILE);
        return $data[0];
    }

    //====== Start Sales Status By Date ==========

    function showSalesStatusByDate($msg = null)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['group_list'] = $this->getGroupList();
        $data['product_list'] = $comListApp->getProductList();
        $data['depo_list'] = $comListApp->getDeliveryPointList();
        $data['catagory_list'] = $comListApp->getCatagoryList();
        require_once(SALES_STATUS_BY_DATE_LIST_SKIN);
        return $data[0];
    }

    //====== Start Sales Status By Catagory ==========
    function showSalesStatusByCatagory($msg = null)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['group_list'] = $this->getGroupList();
        $data['depo_list'] = $comListApp->getDeliveryPointList();
        $data['catagory_list'] = $comListApp->getCatagoryList();
        require_once(SALES_STATUS_BY_CATAGORY_LIST_SKIN);
        return $data[0];
    }

    function showSalesStatusByAmount($msg = null)
    {
        $data = array();
        $data['cmd'] = getRequest('cmd');
        require_once(SALES_STATUS_BY_AMOUNT_LIST_SKIN);
        return $data[0];
    }


    //====== Start Sales Status By TRT ==========

    function showSalesStatusByTRT($msg = null)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['group_list'] = $this->getGroupList();
        $data['product_list'] = $comListApp->getProductList();
        $data['depo_list'] = $comListApp->getDeliveryPointList();
        $data['catagory_list'] = $comListApp->getCatagoryList();
        require_once(SALES_STATUS_BY_TRT_LIST_SKIN);
        return $data[0];
    }

    function showSalesStatusTRTTopsheet($msg = null)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['group_list'] = $this->getGroupList();
        $data['depo_list'] = $comListApp->getDeliveryPointList();
        $data['catagory_list'] = $comListApp->getCatagoryList();
        require_once(SALES_STATUS_TRT_TOPSHEET_SKIN);
        return $data[0];
    }

    function getGroupList()
    {
        $info = array();
        $info['table'] = PRODUCT_GROUP_TBL;
        //$info['fields'] = array('currency_id', 'name');
        $info['orderby'] = array("group_name ASC");
        $info['debug'] = false;
        $result = select($info);
        //dBug($result);
        $data = array();
        if (count($result)) {
            foreach ($result as $i => $v) {
                $data[$i] = $v;
            }
        }
        return $data;
    }
    //====== End Sales Status By Date ==========

    //=========== Customer Status =========

    function showCustomerStatusByTRT($msg = null)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['area_list'] = $comListApp->getDistrictList();
        $data['district_list'] = $comListApp->getDistrictList();
        $data['trt_list'] = $comListApp->getAreaList();
        $division_id = getRequest('division_id');
        $sales_collection = getRequest('sales_collection');
        if ($division_id == 8) {
            require_once(SUPPLIER_STATUS_LIST_SKIN_BY_TRT);
        } else {
            if ($sales_collection == 1) {
                require_once(TEMPLATES_SKINS . '/customer.status.by.trtForSales.html');
            } else {
                require_once(CUSTOMER_STATUS_LIST_SKIN_BY_TRT);
            }
        }

        return $data[0];
    }


    //=========== Customer Sales Status =========

    function showCustomerSalesStatusByTRT($msg = null)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['area_list'] = $comListApp->getDistrictList();
        $data['district_list'] = $comListApp->getDistrictList();
        $data['trt_list'] = $comListApp->getAreaList();
        $division_id = getRequest('division_id');
        if ($division_id == 8) {
            require_once(SUPPLIER_SALES_STATUS_SKIN_BY_TRT);
        } else {
            require_once(CUSTOMER_SALES_STATUS_SKIN_BY_TRT);
        }


        return $data[0];
    }

    //=========== Monthly Customer Status =========

    function showCustomerMonthlyStatusByTRT($msg = null)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['area_list'] = $comListApp->getDistrictList();
        $data['district_list'] = $comListApp->getDistrictList();
        $data['trt_list'] = $comListApp->getAreaList();
        require_once(CUSTOMER_MONTHLY_STATUS_LIST_SKIN);
        return $data[0];
    }

    //============ Stock Status ===========
    function showStockStatus($msg = null)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $from = getRequest('from');
        $to = getRequest('to');
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 500;
        }

        $response = $this->getStoreProductListNEW($from, $to);
        $data['totalrecord'] = $response['total'];
        $data['record_list'] = $response['data'];

        $data['main_catagory_list'] = $comListApp->getMainCatagoryList();
        $data['catagory_list'] = $comListApp->getCatagoryList();
        $data['product_list'] = $comListApp->getProductList();
        $data['depo_list'] = $comListApp->getDeliveryPointList();
        require_once(STORE_STOCK_STATUS_LIST_SKIN);
        return $data[0];
    }

    function showStockMovementStatus($msg = null)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['record_list'] = $this->getStoreProductList(getRequest('from'), getRequest('to'));
        $data['totalrecord'] = $this->getTotalStoreProductList();
        $data['product_list'] = $comListApp->getProductList();
        $data['depo_list'] = $comListApp->getDeliveryPointList();
        $data['catagory_list'] = $comListApp->getCatagoryList();
        require_once(STORE_MOVEMENT_STATUS_LIST_SKIN);
        return $data[0];
    }


    function getStoreProductListNEW($from, $to)
    {
        $from = (int)$from;
        $to = (int)$to;

        if ($from == 0 && $to == 0) {
            $from = 0;
            $to = 500;
        }

        $catagory = getRequest('catagory');
        $subcatagory = getRequest('subcatagory');
        $product = getRequest('product');
        $product_type = getRequest('product_type');
        $stock_id = getRequest('store_id');
        $project_id = getFromSession('project_id');

        $from_date = dateInputFormatYMD(getRequest('date_from'));
        $to_date = dateInputFormatYMD(getRequest('date_to'));

        // Default to_date = today if empty
        if (empty($to_date)) {
            $to_date = date('Y-m-d');
        }

        // If from_date is empty, take earliest create_date from stock_ledger
        if (empty($from_date)) {
            $query = "SELECT MIN(create_date) AS earliest_date
                   FROM " . STOCK_LEDGER_TBL . "
                   WHERE project_id = '$project_id'
                     AND create_date >= '1970-01-01'";
            $result = mysql_query($query);
            if ($result) {
                $row = mysql_fetch_assoc($result);
                $from_date = !empty($row['earliest_date']) ? $row['earliest_date'] : date('Y-m-d');
            }
        }

        // Period and opening balance conditions
        $period_cond = " AND create_date BETWEEN '$from_date' AND '$to_date'";
        $ob_cond = " AND create_date < '$from_date'";

        // ✅ Build product JOIN conditions (po.* filters go here, NOT in WHERE)
        $product_join_cond = "";
        if (!empty($catagory)) {
            $product_join_cond .= " AND po.catagory = '$catagory'";
        }
        if (!empty($subcatagory)) {
            $product_join_cond .= " AND po.subcatagory = '$subcatagory'";
        }
        if (!empty($product)) {
            $product_join_cond .= " AND po.product_id = '$product'";
        }
        if (!empty($product_type)) {
            $product_join_cond .= " AND po.product_type = '$product_type'";
        }

        // ✅ Only base.* filters remain in WHERE
        $filter = "";
        if (!empty($stock_id)) {
            $filter .= " AND base.store_id = '$stock_id'";
        }

        // Main data query
        $data_sql = "
    SELECT 
        base.store_id,
        base.product_id,
        d.delivery_point_name,
        po.product_type,
        po.product_code,
        po.product_name,
        po.product_desc,
        po.m_unit,
        po.unit_price,
        IFNULL(ob.opening_qty, 0) AS opening_qty,
        IFNULL(period.inwards_qty, 0) AS inwards_qty,
        IFNULL(period.outwards_qty, 0) AS outwards_qty,
        (IFNULL(ob.opening_qty, 0) + IFNULL(period.inwards_qty, 0) - IFNULL(period.outwards_qty, 0)) AS balance_qty
    FROM (
        SELECT DISTINCT product_id, store_id
        FROM " . STOCK_LEDGER_TBL . "
        WHERE project_id = '$project_id'
    ) base
    LEFT JOIN " . PRODUCT_TBL . " po 
        ON po.product_id = base.product_id $product_join_cond
    LEFT JOIN " . DELIVERY_POINT_TBL . " d 
        ON d.delivery_pid = base.store_id
    LEFT JOIN (
        SELECT product_id, store_id, (SUM(dr) - SUM(cr)) AS opening_qty
        FROM " . STOCK_LEDGER_TBL . "
        WHERE project_id = '$project_id' $ob_cond
        GROUP BY product_id, store_id
    ) ob ON ob.product_id = base.product_id AND ob.store_id = base.store_id
    LEFT JOIN (
        SELECT product_id, store_id, SUM(dr) AS inwards_qty, SUM(cr) AS outwards_qty
        FROM " . STOCK_LEDGER_TBL . "
        WHERE project_id = '$project_id' $period_cond
        GROUP BY product_id, store_id
    ) period ON period.product_id = base.product_id AND period.store_id = base.store_id
    WHERE 1=1 $filter
    ORDER BY po.product_name ASC, d.delivery_point_name ASC
    LIMIT $from, $to
    ";

        $result = mysql_query($data_sql);
        $data = array();

        if ($result) {
            while ($row = mysql_fetch_object($result)) {
                $data[] = $row;
            }
        }

        // Total rows count
        $count_sql = "
    SELECT COUNT(*) AS total_rows FROM (
        SELECT base.product_id, base.store_id
        FROM (
            SELECT DISTINCT product_id, store_id
            FROM " . STOCK_LEDGER_TBL . "
            WHERE project_id = '$project_id'
        ) base
        LEFT JOIN " . PRODUCT_TBL . " po 
            ON po.product_id = base.product_id $product_join_cond
        LEFT JOIN (
            SELECT product_id, store_id
            FROM " . STOCK_LEDGER_TBL . "
            WHERE project_id = '$project_id' $ob_cond
            GROUP BY product_id, store_id
        ) ob ON ob.product_id = base.product_id AND ob.store_id = base.store_id
        LEFT JOIN (
            SELECT product_id, store_id
            FROM " . STOCK_LEDGER_TBL . "
            WHERE project_id = '$project_id' $period_cond
            GROUP BY product_id, store_id
        ) period ON period.product_id = base.product_id AND period.store_id = base.store_id
        WHERE 1=1 $filter
    ) AS count_data
    ";

        $count_result = mysql_query($count_sql);
        $total_rows = 0;
        if ($count_result) {
            $row = mysql_fetch_assoc($count_result);
            $total_rows = $row['total_rows'];
        }

        return array(
            'data' => $data,
            'total' => $total_rows
        );
    }


    function getStoreProductList($from, $to)
    {
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 500;
        }
        $catagory = getRequest('catagory');
        $subcatagory = getRequest('subcatagory');
        $product = getRequest('product');
        $product_type = getRequest('product_type');
        $stock_id = getRequest('store_id');
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = STORE_STOCK_VIEW . ' s,' . PRODUCT_TBL . ' po,' . PROJECT_TBL . ' p,' . DELIVERY_POINT_TBL . ' d';
        $info['fields'] = array('s.store_id', 'p.project_name', 'p.location', 'd.delivery_point_name', 'po.product_id', 'po.product_type', 'po.product_code', 'po.product_name', 'po.product_desc', 'po.m_unit', 's.instock',
            's.outstock', 's.balance', 'po.unit_price');

        $sql = "s.product_id = po.product_id AND s.project_id = p.project_id AND s.store_id = d.delivery_pid AND s.project_id = '" . $project_id . "'";
        if ($catagory != "") {
            $sql .= " AND po.catagory = '$catagory'";
        }
        if ($subcatagory != "") {
            $sql .= " AND po.subcatagory = '$subcatagory'";
        }
        if ($product != "") {
            $sql .= " AND s.product_id = '$product'";
        }
        if ($product_type != "") {
            $sql .= " AND po.product_type = '$product_type'";
        }
        if ($stock_id != "") {
            $sql .= " AND s.store_id = '$stock_id'";
        }
        $info['where'] = $sql;
        $info['orderby'] = array("po.product_name ASC LIMIT $from,$to");
        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }
        return $data;
    }

    function getTotalStoreProductList()
    {
        $catagory = getRequest('catagory');
        $subcatagory = getRequest('subcatagory');
        $product = getRequest('product');
        $product_type = getRequest('product_type');
        $stock_id = getRequest('store_id');
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = STORE_STOCK_VIEW . ' s,' . PRODUCT_TBL . ' po,' . PROJECT_TBL . ' p,' . DELIVERY_POINT_TBL . ' d';
        $info['fields'] = array('s.store_id');

        $sql = "s.product_id = po.product_id AND s.project_id = p.project_id AND s.store_id = d.delivery_pid AND s.project_id = '" . $project_id . "'";
        if ($catagory != "") {
            $sql .= " AND po.catagory = '$catagory'";
        }
        if ($subcatagory != "") {
            $sql .= " AND po.subcatagory = '$subcatagory'";
        }
        if ($product != "") {
            $sql .= " AND s.product_id = '$product'";
        }
        if ($product_type != "") {
            $sql .= " AND po.product_type = '$product_type'";
        }
        if ($stock_id != "") {
            $sql .= " AND s.store_id = '$stock_id'";
        }
        $info['where'] = $sql;
        //$info['groupby']= array("po.product_id");
        $info['orderby'] = array("po.product_name ASC");
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            return $cnt;
        } else {
            return 0;
        }
    }

    //============ Order vs Production require ===========
    function showOrderProductionReq($msg = null)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $delivery_point = getRequest('delivery_point');
        $data['cmd'] = getRequest('cmd');
        if ($_POST['submit']) {
            $data['record_list'] = $this->getOrderProductList(getRequest('from'), getRequest('to'));
            $data['totalrecord'] = $this->getTotalOrderProductList();
        }
        $data['product_list'] = $comListApp->getProductList();
        $data['depo_list'] = $comListApp->getDeliveryPointList();
        $data['catagory_list'] = $comListApp->getCatagoryList();
        if ($delivery_point != "") {
            require_once(DEPO_ORDER_PRODUCTION_REQUIRE_LIST_SKIN);
        } else {
            require_once(ORDER_PRODUCTION_REQUIRE_LIST_SKIN);
        }
        return $data[0];
    }


    function getOrderProductList($from, $to)
    {
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 500;
        }
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $catagory = getRequest('catagory');
        $subcatagory = getRequest('subcatagory');
        $delivery_point = getRequest('delivery_point');
        $product = getRequest('product');
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = SALES_MASTER_TBL . ' m,' . SALES_DETAILS_TBL . ' s,' . PRODUCT_TBL . ' po,' . PROJECT_TBL . ' p,' . DELIVERY_POINT_TBL . ' d';
        $info['fields'] = array('m.delivery_point as store_id', 'm.project_id', 'p.project_name', 'p.location', 'd.delivery_point_name', 'po.product_id', 'po.product_type', 'po.product_name', 'po.product_desc', 'po.m_unit', 'SUM((s.qty+s.free_qty+s.undelivery_qty)) as order_qty', 'po.unit_price');

        $sql = "m.voucher_no = s.voucher_no AND s.product = po.product_id AND m.project_id = s.project_id AND m.delivery_point = d.delivery_pid AND s.project_id ='" . $project_id . "' AND s.delivery_qty =0 AND m.status=1 ";
        if ($catagory != "") {
            $sql .= " AND po.catagory = '$catagory'";
        }
        if ($subcatagory != "") {
            $sql .= " AND po.subcatagory = '$subcatagory'";
        }
        if ($delivery_point != "") {
            $sql .= " AND m.delivery_point = '$delivery_point'";
        }
        if ($product != "") {
            $sql .= " AND s.product = '$product'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND m.sales_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND m.sales_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND m.sales_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['groupby'] = array("po.product_id");
        $info['orderby'] = array("po.product_name ASC LIMIT $from,$to");
        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }
        return $data;
    }

    function getTotalOrderProductList()
    {
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $catagory = getRequest('catagory');
        $subcatagory = getRequest('subcatagory');
        $delivery_point = getRequest('delivery_point');
        $product = getRequest('product');
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = SALES_MASTER_TBL . ' m,' . SALES_DETAILS_TBL . ' s,' . PRODUCT_TBL . ' po,' . PROJECT_TBL . ' p,' . DELIVERY_POINT_TBL . ' d';
        $info['fields'] = array('m.delivery_point as store_id', 'p.project_name', 'p.location', 'd.delivery_point_name', 'po.product_id', 'po.product_type', 'po.product_name', 'po.product_desc', 'po.m_unit');

        $sql = "m.voucher_no = s.voucher_no AND s.product = po.product_id AND m.project_id = s.project_id AND m.delivery_point = d.delivery_pid AND s.project_id = '" . $project_id . "' AND s.delivery_qty =0 AND m.status=1";
        if ($catagory != "") {
            $sql .= " AND po.catagory = '$catagory'";
        }
        if ($subcatagory != "") {
            $sql .= " AND po.subcatagory = '$subcatagory'";
        }

        if ($delivery_point != "") {
            $sql .= " AND m.delivery_point = '$delivery_point'";
        }
        if ($product != "") {
            $sql .= " AND s.product = '$product'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND m.sales_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND m.sales_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND m.sales_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['groupby'] = array("po.product_id");
        $info['orderby'] = array("po.product_name ASC");
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            return $cnt;
        } else {
            return 0;
        }
    }

    function showStockMovementTopsheet($msg = null)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['record_list'] = $this->getStockLedgerStoreList();
        $data['catagory_list'] = $comListApp->getCatagoryList();
        $data['product_list'] = $comListApp->getProductList();
        $data['depo_list'] = $comListApp->getDeliveryPointList();
        require_once(STORE_MOVEMENT_TOPSHEET_SKIN);
        return $data[0];
    }

    function getStockLedgerStoreList()
    {
        $catagory = getRequest('catagory');
        $subcatagory = getRequest('subcatagory');
        $product = getRequest('product');
        $product_type = getRequest('product_type');
        $stock_id = getRequest('store_id');
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = STORE_STOCK_VIEW . ' s,' . PRODUCT_TBL . ' po,' . PROJECT_TBL . ' p,' . DELIVERY_POINT_TBL . ' d';
        $info['fields'] = array('s.store_id', 'p.project_name', 'p.location', 'd.delivery_point_name', 'po.m_unit', 'SUM(s.instock) AS instock',
            'SUM(s.outstock) AS outstock', 'SUM(s.balance) AS balance', 'AVG(po.unit_price) AS unit_price');

        $sql = "s.product_id = po.product_id AND s.project_id = p.project_id AND s.store_id = d.delivery_pid AND s.project_id = '" . $project_id . "'";
        if ($catagory != "") {
            $sql .= " AND po.catagory = '$catagory'";
        }
        if ($subcatagory != "") {
            $sql .= " AND po.subcatagory = '$subcatagory'";
        }
        if ($product != "") {
            $sql .= " AND s.product_id = '$product'";
        }
        if ($product_type != "") {
            $sql .= " AND po.product_type = '$product_type'";
        }
        $info['where'] = $sql;
        $info['groupby'] = array("s.store_id");
        $info['orderby'] = array("d.delivery_point_name ASC");
        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }
        return $data;
    }

    //============ Sales Details ==========
    function showSalesDetails($msg = null)
    {
        require_once(CLASS_DIR . '/sales.class.php');
        $salesApp = new Sales();
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        require_once(CLASS_DIR . '/common.class.php');
        $comApp = new Common();
        $data = array();
        $project_id = getFromSession('project_id');
        if (getRequest('submit') || $_REQUEST['date_from'] != "") {
            $data['record_list'] = $this->getSalesDetailsList(getRequest('from'), getRequest('to'));
            $data['totalrecord'] = $this->getTotalSalesDetailsList();
        }
        $data['district_list'] = $comListApp->getDistrictList();
        $data['area_list'] = $comListApp->getAreaList();
        $data['depo_list'] = $comListApp->getDeliveryPointList();
        require_once(SALES_DETAILS_REPORT_SKIN);
        return $data[0];
    }

    function getSalesDetailsList($from, $to)
    {
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 200;
        }
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $project_id = getFromSession('project_id');
        $delivery_point = getRequest('delivery_point');
        $district = getRequest('district');
        $area = getRequest('area');
        $salesby = getRequest('salesby');
        $order_by = getRequest('order_by');
        $info = array();
        $info['table'] = SALES_MASTER_TBL . ' sm,' . SALES_DETAILS_TBL . ' sd,' . PROJECT_TBL . ' p,' . DELIVERY_POINT_TBL . ' as dp,' . PRODUCT_TBL . ' po,' . CATAGORY_TBL . ' ct,' . BRAND_TBL . ' b,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('sd.voucher_no', 'p.project_name', 'p.location', 'dp.delivery_point_name', 'sm.customer as sub_id', "DATE_FORMAT(sm.sales_date,'%d %b %y' ) as sales_date", 'c.curr_symble', 'ct.catagory_name', 'b.brand_name', 'AVG(sd.unit_price) as unit_price', 'SUM(sd.qty) as sales_qty', 'SUM(sd.delivery_qty) as delivery_qty', 'SUM(sd.return_qty) as return_qty', "sd.product", "po.product_name", "po.product_desc", "po.m_unit");

        $sql = "sm.voucher_no = sd.voucher_no AND sm.delivery_point=dp.delivery_pid AND sd.product = po.product_id AND sd.project_id = p.project_id AND sm.currency = c.currency_id AND 
		sd.catagory = ct.catagory_code AND sd.brand_id = b.brand_id AND sd.project_id = '" . $project_id . "' AND sm.status=1";
        if ($delivery_point != "") {
            $sql .= " AND sm.delivery_point = '$delivery_point'";
        }
        if ($district != "") {
            $sql .= " AND s.district = '$district'";
        }
        if ($area != "") {
            $sql .= " AND s.area = '$area'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND sm.sales_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND sm.sales_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND sm.sales_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['groupby'] = array("sd.sal_detail_id");
        if ($order_by == "Max Sales" && $salesby == "Qty") {
            $info['orderby'] = array("SUM(sd.qty) DESC LIMIT $from,$to");
        } elseif ($order_by == "Min Sales" && $salesby == "Qty") {
            $info['orderby'] = array("SUM(sd.qty) ASC LIMIT $from,$to");
        } elseif ($order_by == "Max Sales" && $salesby == "Amount") {
            $info['orderby'] = array("SUM(sd.total) DESC LIMIT $from,$to");
        } elseif ($order_by == "Min Sales" && $salesby == "Amount") {
            $info['orderby'] = array("SUM(sd.total) ASC LIMIT $from,$to");
        }
        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }
        return $data;
    }

    function getTotalSalesDetailsList($from, $to)
    {
        //$smRow = mysql_fetch_object(mysql_query("SELECT FOUND_ROWS() as FOUND_ROWS"));
        //return $smRow->FOUND_ROWS;
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $project_id = getFromSession('project_id');
        $delivery_point = getRequest('delivery_point');
        $district = getRequest('district');
        $area = getRequest('area');
        $salesby = getRequest('salesby');
        $order_by = getRequest('order_by');
        $info = array();
        $info['table'] = SALES_MASTER_TBL . ' sm,' . SALES_DETAILS_TBL . ' sd,' . PROJECT_TBL . ' p,' . DELIVERY_POINT_TBL . ' as dp,' . PRODUCT_TBL . ' po,' . CATAGORY_TBL . ' ct,' . BRAND_TBL . ' b,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('sd.voucher_no');

        $sql = "sm.voucher_no = sd.voucher_no AND sm.delivery_point=dp.delivery_pid AND sd.product = po.product_id AND sd.project_id = p.project_id AND sm.currency = c.currency_id AND 
		sd.catagory = ct.catagory_code AND sd.brand_id = b.brand_id AND sd.project_id = '" . $project_id . "' AND sm.status=1";
        if ($delivery_point != "") {
            $sql .= " AND sm.delivery_point = '$delivery_point'";
        }
        if ($district != "") {
            $sql .= " AND s.district = '$district'";
        }
        if ($area != "") {
            $sql .= " AND s.area = '$area'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND sm.sales_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND sm.sales_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND sm.sales_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['groupby'] = array("sd.sal_detail_id");
        if ($order_by == "Max Sales" && $salesby == "Qty") {
            $info['orderby'] = array("SUM(sd.qty) DESC");
        } elseif ($order_by == "Min Sales" && $salesby == "Qty") {
            $info['orderby'] = array("SUM(sd.qty) ASC");
        } elseif ($order_by == "Max Sales" && $salesby == "Amount") {
            $info['orderby'] = array("SUM(sd.total) DESC LIMIT");
        } elseif ($order_by == "Min Sales" && $salesby == "Amount") {
            $info['orderby'] = array("SUM(sd.total) ASC");
        }
        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            return $cnt;
        } else {
            return 0;
        }
    }

    //===== Sales Summary =======
    function showCustomerSalesDetails($msg = null)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['record_list'] = $this->getCustomerSalesStatusList(getRequest('from'), getRequest('to'));
        $data['totalrecord'] = $this->getTotalCustomerSalesStatusList(getRequest('from'), getRequest('to'));
        $data['district_list'] = $comListApp->getDistrictList();
        $data['customer_list'] = $comListApp->getCustomerList();
        $data['area_list'] = $comListApp->getAreaList();
        require_once(CUSTOMER_SALES_DETAILS_SKIN);
        return $data[0];
    }

    function getCustomerSalesStatusList($from, $to)
    {
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 500;
        }
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $district = getRequest('district');
        $customer = getRequest('customer');
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = SALES_MASTER_TBL . ' pm,' . SUB_ACC_HEAD_TBL . ' s,' . PROJECT_TBL . ' p,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('pm.voucher_no', 'p.project_name', 'p.location', 's.sub_id', 's.sub_head_name', 's.head_details', 'pm.gate_pass', 'pm.track_no', 'pm.salse_type', 'pm.total_value', "DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date", 'pm.mode_of_payment', 'c.curr_symble', 'pm.bank_name', 'pm.acc_no', 'pm.check_no', 'pm.check_no', 'pm.discount', 'pm.net_payble', 'pm.paid_amount', 'pm.due', 'pm.item_delivery_amount', 'pm.return_amount', 'pm.ref_no', 'pm.created_date', "DATE_FORMAT(pm.created_date,'%d %b %y' ) as date", "pm.reference", "pm.commission_slot", "pm.total_commission", "pm.commission_adv_paid", "pm.commission_total_paid", "pm.commission_total_due", "pm.commission_status");

        $sql = "pm.customer = s.sub_id AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '" . $project_id . "' AND pm.status=1";
        if ($district != "") {
            $sql .= " AND pm.district = '$district'";
        }
        if ($customer != "") {
            $sql .= " AND pm.customer = '$customer'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND pm.sales_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND pm.sales_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND pm.sales_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['orderby'] = array("pm.voucher_no asc LIMIT $from,$to");
        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }
        return $data;
    }

    function getTotalCustomerSalesStatusList($from, $to)
    {
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 500;
        }
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $district = getRequest('district');
        $customer = getRequest('customer');
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = SALES_MASTER_TBL . ' pm,' . SUB_ACC_HEAD_TBL . ' s,' . PROJECT_TBL . ' p,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('pm.voucher_no');

        $sql = "pm.customer = s.sub_id AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '" . $project_id . "' AND pm.status=1";
        if ($district != "") {
            $sql .= " AND pm.district = '$district'";
        }
        if ($customer != "") {
            $sql .= " AND pm.customer = '$customer'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND pm.sales_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND pm.sales_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND pm.sales_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['orderby'] = array("pm.voucher_no asc LIMIT $from,$to");
        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            return $cnt;
        } else {
            return 0;
        }
    }

    //======== Running Production Report ========
    function showRunningProduction($msg = null)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        require_once(CLASS_DIR . '/common.class.php');
        $comApp = new Common();
        $data = array();
        $project_id = getFromSession('project_id');
        $data['record_list'] = $this->getRunningProductionList(getRequest('from'), getRequest('to'));
        $data['totalrecord'] = $this->getTotalRunningProductionList(getRequest('from'), getRequest('to'));
        $data['district_list'] = $comListApp->getDistrictList();
        $data['area_list'] = $comListApp->getAreaList();
        require_once(RUNNING_PRODUCTION_SKIN_FILE);
        return $data[0];
    }

    function getRunningProductionList($from, $to)
    {
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 40;
        }
        //$production_type = getRequest('production_type');
        $production_type = "Running";
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = PRODUCTION_MASTER_TBL . ' pm,' . PROJECT_TBL . ' pa,' . PRODUCT_TBL . ' p,' . FACTORY_TBL . ' f,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('pm.production_id', 'pm.batch_no', 'pm.finish_product', 'pa.project_name', 'pa.location', 'f.factory_name ', 'f.address', 'pm.total_value', 'pm.production_amount', 'pm.unit_price', 'pm.sales_price', 'pm.m_unit',
            'p.product_name', 'pm.target_qty', 'pm.finish_qty', "DATE_FORMAT(pm.used_date,'%d %b %y' ) as used_date", 'pm.production_type', 'c.curr_symble', 'pm.created_time');

        $sql = "pm.finish_product = p.product_id AND pm.project_id = pa.project_id AND pm.factory_id = f.factory_id AND pm.currency = c.currency_id AND pm.project_id = '" . $project_id . "' AND
		 pm.production_type='" . $production_type . "'";
        $info['where'] = $sql;
        $info['orderby'] = array("pm.production_id asc LIMIT $from,$to");
        //$info['debug'] = true;
        $result = select($info);
        $cnt = count($result);
        if ($cnt) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }

        return $data;
    }

    function getTotalRunningProductionList()
    {
        //$production_type = getRequest('production_type');
        $production_type = "Running";
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = PRODUCTION_MASTER_TBL . ' pm,' . PROJECT_TBL . ' pa,' . PRODUCT_TBL . ' p,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('pm.production_id');
        $sql = "pm.finish_product = p.product_id AND pm.project_id = pa.project_id AND pm.currency = c.currency_id AND pm.project_id = '" . $project_id . "' AND pm.production_type='" . $production_type . "'";
        $info['where'] = $sql;
        $info['orderby'] = array("pm.production_id");
        //$info['debug'] = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);

        if ($cnt) {
            return $cnt;
        } else {
            return 0;
        }
    }

    function showSalesOrder($msg = null)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');

        $data['supplier_list'] = $comListApp->getSupplierList();
        $customer_list = $comListApp->getCustomerList();
        $getCustomerListReceivable = $comListApp->getCustomerListReceivable();
        $merged = array_merge($customer_list, $getCustomerListReceivable);

        $unique = array();
        foreach ($merged as $item) {
            $unique[$item->sub_id] = $item;
        }

        $data['customer_list'] = array_values($unique);

        $data['district_list'] = $comListApp->getDistrictList();
        $data['depo_list'] = $comListApp->getDeliveryPointList();
        $data['area_list'] = $comListApp->getAreaList();
        if ($data['cmd'] == "sal_dtl") {
            $data['record_list'] = $this->getSalesOrderList(getRequest('from'), getRequest('to'));
            $data['totalrecord'] = $this->getTotalSalesOrderList(getRequest('from'), getRequest('to'));
            require_once(SALES_DETAILS_SKIN);
        } elseif ($data['cmd'] == "sales.return") {
            $data['record_list'] = $this->getSalesOrderList(getRequest('from'), getRequest('to'));
            $data['totalrecord'] = $this->getTotalSalesOrderList(getRequest('from'), getRequest('to'));
            require_once(SALES_SUMMARY4_RETURN_SKIN);
        } elseif ($data['cmd'] == "sales.delivery.order") {
            $data['record_list'] = $this->getSalesDeliveryOrderList(getRequest('from'), getRequest('to'));
            $data['totalrecord'] = $this->getTotalSalesDeliveryOrderList(getRequest('from'), getRequest('to'));
            require_once(SALES_ORDER_DELIVERY_LIST_SKIN);
        } else {
            $data['record_list'] = $this->getSalesPendingOrderList(getRequest('from'), getRequest('to'));
            $data['totalrecord'] = $this->getTotalSalesPendingOrderList();
            require_once(SALES_ORDER_LIST_SKIN);
        }
        return $data[0];
    }


    function showBillList($msg = null)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');

        $data['supplier_list'] = $comListApp->getSupplierList();
        $customer_list = $comListApp->getCustomerList();
        $getCustomerListReceivable = $comListApp->getCustomerListReceivable();
        $merged = array_merge($customer_list, $getCustomerListReceivable);
        $unique = array();
        foreach ($merged as $item) {
            $unique[$item->sub_id] = $item;
        }

        $data['customer_list'] = array_values($unique);

        $data['district_list'] = $comListApp->getDistrictList();
        $data['depo_list'] = $comListApp->getDeliveryPointList();
        $data['area_list'] = $comListApp->getAreaList();

        $result = $this->getBillList();

        $data['record_list'] = $result['record_list'];
        $data['totalrecord'] = $result['totalrecord'];
        require_once(TEMPLATES_SKINS . '/bill_list.html');

        return $data[0];
    }

    function getBillList()
    {
        $from = getRequest('from');
        $to = getRequest('to');

        if ($from == "" && $to == "") {
            $from = 0;
            $to = 100;
        }

        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $customer = getRequest('customer');
        $delivery_point = getRequest('delivery_point');
        $project_id = getFromSession('project_id');

        $is_aging_over = getRequest('is_aging_over');

        $today = date('Y-m-d');
        $info = array();

        $sqlSet = "SET GLOBAL group_concat_max_len = 10000";
        mysql_fetch_object(mysql_query($sqlSet));

        $info['table'] = 'bill b 
	    LEFT JOIN bill_invoices bi ON b.bill_id = bi.bill_id
	    LEFT JOIN ' . PROJECT_TBL . ' p ON b.project_id = p.project_id
	    LEFT JOIN ' . CURRENCY_TBL . ' c ON b.currency = c.currency_id';

        $info['fields'] = array(
            'b.*',
            'p.project_name',
            'p.location',
            "DATE_FORMAT(b.bill_date,'%d %b %y') as billDate",
            "DATE_FORMAT(b.aging_date,'%d %b %y') as agingDate",
            'c.curr_symble',
            "GROUP_CONCAT(bi.invoice_no SEPARATOR ', ') as invoices",
            "GROUP_CONCAT(bi.wo_no SEPARATOR ', ') as wo_numbers"
        );

        $sql = "b.project_id = '" . $project_id . "' AND b.status=1";
        if ($customer != "") {
            $sql .= " AND b.customer = '$customer'";
        }
        if ($delivery_point != "") {
            $sql .= " AND b.store_id = '$delivery_point'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND b.bill_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND b.bill_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND b.bill_date BETWEEN '$date_from' AND '$date_to'";
        }

        if ($is_aging_over == 1) {
            $sql .= " AND b.aging_date <= '$today'";
        }

        $info['where'] = $sql;
        $info['groupby'] = array("b.bill_id");

        $totalrecord = count(select($info));

        $info['orderby'] = array("b.aging_date DESC LIMIT $from,$to");
        //$info['debug']  = true;
        $result = select($info);


        $data = array();
        if (count($result)) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }

        return [
            "record_list" => $data,
            "totalrecord" => $totalrecord
        ];

    }


    //====== Start Unapproved Sales Order ======
    function showUnapproveSalesOrder($msg = null)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['customer_list'] = $comListApp->getCustomerList();
        $data['supplier_list'] = $comListApp->getSupplierList();
        $data['district_list'] = $comListApp->getDistrictList();
        $data['depo_list'] = $comListApp->getDeliveryPointList();
        $data['area_list'] = $comListApp->getAreaList();
        $data['record_list'] = $this->getUnapproveOrderList(getRequest('from'), getRequest('to'));
        $data['totalrecord'] = $this->getTotalUnapproveOrderList();

        require_once(UNAPPROVE_ORDER_LIST_SKIN);
        return $data[0];
    }

    function getUnapproveOrderList($from, $to)
    {
        $cmd = getRequest('cmd');
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 100;
        }

        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $customer = getRequest('customer');
        $delivery_point = getRequest('delivery_point');
        $division_id = getRequest('division_id');
        $district = getRequest('district');
        $project_id = getFromSession('project_id');
        $original_copy = getRequest('original_copy');
        $wo_no = getRequest('wo_no');

        $info = array();
        if ($original_copy == 1) {
            $table = SALES_MASTER_APP_TBL;
        } else {
            $table = SALES_MASTER_TBL;
        }

        $info['table'] = $table . " pm
	    LEFT JOIN " . PROJECT_TBL . " p ON pm.project_id = p.project_id
	    LEFT JOIN " . CURRENCY_TBL . " c ON pm.currency = c.currency_id";

        $info['fields'] = array('pm.voucher_no', 'p.project_name', 'p.location', 'pm.customer as sub_id', 'pm.gate_pass', 'pm.track_no', 'pm.salse_type', 'pm.total_value', "DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date", 'pm.mode_of_payment', 'c.curr_symble', 'pm.bank_name', 'pm.acc_no', 'pm.check_no', 'pm.check_no', 'pm.discount', 'pm.net_payble', 'pm.paid_amount', 'pm.due', 'pm.item_delivery_amount', 'pm.return_amount', 'pm.ref_no', 'pm.created_date', "DATE_FORMAT(pm.created_date,'%d %b %y' ) as date", "pm.reference", "pm.commission_slot", "pm.total_commission", "pm.commission_adv_paid", "pm.commission_total_paid", "pm.commission_total_due", "pm.commission_status", "pm.created_by", "pm.created_date", "pm.approved_by", "pm.checked_by", "pm.status", "pm.wo_no", "pm.ref_voucher");

        $sql = "pm.project_id = '" . $project_id . "'  AND pm.item_delivery_amount =0 AND pm.status=0";

        if ($division_id != "") {
            $sql .= " AND pm.division = '$division_id'";
        }
        if ($district != "") {
            $sql .= " AND pm.district = '$district'";
        }

        if ($customer != "") {
            $sql .= " AND pm.customer = '$customer'";
        }
        if ($delivery_point != "") {
            $sql .= " AND pm.delivery_point = '$delivery_point'";
        }
        if ($wo_no != "") {
            $wo_no = addslashes($wo_no);
            $sql .= " AND pm.ref_voucher LIKE '%$wo_no%'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND pm.sales_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND pm.sales_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND pm.sales_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['orderby'] = array("pm.sales_date DESC LIMIT $from,$to");
        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }
        return $data;
    }

    function getTotalUnapproveOrderList()
    {
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $customer = getRequest('customer');
        $delivery_point = getRequest('delivery_point');
        $division_id = getRequest('division_id');
        $district = getRequest('district');
        $project_id = getFromSession('project_id');
        $original_copy = getRequest('original_copy');
        $wo_no = getRequest('wo_no');

        $info = array();
        if ($original_copy == 1) {
            $table = SALES_MASTER_APP_TBL;
        } else {
            $table = SALES_MASTER_TBL;
        }

        $info['table'] = $table . " pm
	    LEFT JOIN " . PROJECT_TBL . " p ON pm.project_id = p.project_id
	    LEFT JOIN " . CURRENCY_TBL . " c ON pm.currency = c.currency_id";

        $info['fields'] = array('pm.voucher_no', 'p.project_name', 'p.location', 'pm.customer as sub_id', 'pm.gate_pass', 'pm.track_no', 'pm.salse_type', 'pm.total_value', "DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date", 'pm.mode_of_payment', 'c.curr_symble', 'pm.bank_name', 'pm.acc_no', 'pm.check_no', 'pm.check_no', 'pm.discount', 'pm.net_payble', 'pm.paid_amount', 'pm.due', 'pm.item_delivery_amount', 'pm.return_amount', 'pm.ref_no', 'pm.created_date', "DATE_FORMAT(pm.created_date,'%d %b %y' ) as date", "pm.reference", "pm.commission_slot", "pm.total_commission", "pm.commission_adv_paid", "pm.commission_total_paid", "pm.commission_total_due", "pm.commission_status", "pm.created_by", "pm.created_date", "pm.approved_by", "pm.checked_by", "pm.status", "pm.wo_no", "pm.ref_voucher");

        $sql = "pm.project_id = '" . $project_id . "' AND pm.item_delivery_amount =0 AND pm.status=0";

        if ($division_id != "") {
            $sql .= " AND pm.division = '$division_id'";
        }
        if ($district != "") {
            $sql .= " AND pm.district = '$district'";
        }
        if ($customer != "") {
            $sql .= " AND pm.customer = '$customer'";
        }
        if ($delivery_point != "") {
            $sql .= " AND pm.delivery_point = '$delivery_point'";
        }
        if ($wo_no != "") {
            $wo_no = addslashes($wo_no);
            $sql .= " AND pm.ref_voucher LIKE '%$wo_no%'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND pm.sales_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND pm.sales_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND pm.sales_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['orderby'] = array("pm.sales_date DESC");
        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            return $cnt;
        } else {
            return 0;
        }
    }

    //====== End Unapproved Sales Order =========
    function showBatchProductionList($msg = null)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['record_list'] = $this->getBatchProductionList(getRequest('from'), getRequest('to'));
        $data['totalrecord'] = $this->getTotalBatchProductionList();
        $data['product_list'] = $comListApp->getProductList();
        $data['depo_list'] = $comListApp->getDeliveryPointList();
        $data['machine_list'] = $comListApp->getMachineList();
        require_once(BATCH_PRODUCTION_LIST_SKIN);
        return $data[0];
    }

    function DeleteBatchProduction()
    {
        $project_id = getFromSession('project_id');
        $production_id = getRequest('id');
        $date_from = getRequest('date_from');
        $date_to = getRequest('date_to');
        $machine = getRequest('machine');
        $version_no = getRequest('version_no');
        $store_id = getRequest('store_id');
        $product = getRequest('product');

        if (getFromSession('u_type_id') == 101) {
            $sql = "SELECT * FROM " . PRODUCTION_FG_TBL . " WHERE production_id='" . $production_id . "' AND project_id='" . $project_id . "'";
            $res = mysql_query($sql);
            $num = mysql_num_rows($res);
            if ($num > 0) {
                mysql_query("START TRANSACTION;");
                mysql_query("SET autocommit=0;");
                //===== Delete Raw Materials =====
                $DSQL1 = "DELETE FROM " . PRODUCTION_DETAILS_TBL . " WHERE production_id='" . $production_id . "' AND project_id='" . $project_id . "'";
                $dres1 = mysql_query($DSQL1);
                //===== Delete Stock Ledger =====
                $DSQL2 = "DELETE FROM " . STOCK_LEDGER_TBL . " WHERE voucher_no='$production_id' AND project_id='" . $project_id . "'";
                $dres2 = mysql_query($DSQL2);
                //===== Delete Account Ledger =====
                $DSQL3 = "DELETE FROM " . ACCOUNT_JOURNAL_TBL . " WHERE voucher_no='$production_id' AND project_id='" . $project_id . "'";
                $dres3 = mysql_query($DSQL3);
                //===== Delete FG Production =====
                $DSQL4 = "DELETE FROM " . PRODUCTION_FG_TBL . " WHERE production_id='" . $production_id . "' AND project_id='" . $project_id . "'";
                $dres4 = mysql_query($DSQL4);
                $sql2 = "SELECT * FROM " . PRODUCTION_FG_TBL . " WHERE production_id='" . $production_id . "' AND project_id='" . $project_id . "'";
                $res2 = mysql_query($sql2);
                $num2 = mysql_num_rows($res2);
                if ($num2 == 0) {
                    $DSQL5 = "DELETE FROM " . PRODUCTION_MASTER_TBL . " WHERE production_id='" . $production_id . "' AND project_id='" . $project_id . "'";
                    mysql_query($DSQL5);
                }
                if (($dres1) && ($dres2) && ($dres3) && ($dres4)) {
                    mysql_query("COMMIT;");
                    $msg = "Successfully Production Deleted !!!";
                    header("location:index.php?app=sales.report&cmd=show.batch.po.list&date_from=$date_from&date_to=$date_to&machine=$machine&version_no=$version_no&product=$product&store_id=$store_id&msg=$msg");
                } else {
                    mysql_query("ROLLBACK;");
                    $msg = "Failed Delete Production. Please try again!!!";
                    header("location:index.php?app=sales.report&cmd=show.batch.po.list&date_from=$date_from&date_to=$date_to&machine=$machine&version_no=$version_no&product=$product&store_id=$store_id&msg=$msg");
                }

            }

        } else {
            $msg = "Access Denied!!!";
            header("location:index.php?app=sales.report&cmd=show.batch.po.list&date_from=$date_from&date_to=$date_to&machine=$machine&version_no=$version_no&product=$product&store_id=$store_id&msg=$msg");
        }

    }

    function getBatchProductionList($from, $to)
    {
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 500;
        }
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $machine = getRequest('machine');
        $version_no = getRequest('version_no');
        $store_id = getRequest('store_id');
        $product = getRequest('product');
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = PRODUCTION_MASTER_TBL . ' pm,' . MACHINE_TBL . ' as m,' . PROJECT_TBL . ' pa,' . FACTORY_TBL . ' f,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('pm.production_id', 'pm.batch_no', 'pm.in_store_id', 'pm.finish_product', 'pa.project_name', 'pa.location', 'f.factory_name ', 'f.address', 'pm.total_value', 'pm.production_amount', 'pm.version_no', 'pm.m_unit',
            'pm.target_qty', 'pm.finish_qty', "DATE_FORMAT(pm.used_date,'%d %b %y' ) as used_date", 'm.machine_id', 'm.machine_name', 'm.model', 'c.curr_symble', 'pm.created_time', 'pm.created_by');

        $sql = "pm.project_id = pa.project_id AND pm.factory_id = f.factory_id AND pm.machine_no = m.machine_id AND pm.currency = c.currency_id AND pm.project_id = '" . $project_id . "'";
        if ($machine != "") {
            $sql .= " AND pm.machine_no = '$machine'";
        }
        if ($version_no != "") {
            $sql .= " AND pm.version_no = '$version_no'";
        }
        if ($store_id != "") {
            $sql .= " AND pm.in_store_id = '$store_id'";
        }
        if ($product != "") {
            $sql .= " AND pm.finish_product = '$product'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND pm.used_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND pm.used_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND pm.used_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['orderby'] = array("pm.production_id asc LIMIT $from,$to");
        //$info['debug'] = true;
        $result = select($info);
        $cnt = count($result);
        if ($cnt) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }

        return $data;
    }

    function getTotalBatchProductionList()
    {
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $machine = getRequest('machine');
        $version_no = getRequest('version_no');
        $store_id = getRequest('store_id');
        $product = getRequest('product');
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = PRODUCTION_MASTER_TBL . ' pm,' . MACHINE_TBL . ' as m,' . PROJECT_TBL . ' pa,' . FACTORY_TBL . ' f,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('pm.production_id', 'pm.batch_no', 'pm.finish_product', 'pa.project_name', 'pa.location', 'f.factory_name ', 'f.address', 'pm.total_value', 'pm.production_amount', 'pm.version_no', 'pm.m_unit',
            'pm.target_qty', 'pm.finish_qty', "DATE_FORMAT(pm.used_date,'%d %b %y' ) as used_date", 'm.machine_id', 'm.machine_name', 'm.model', 'c.curr_symble', 'pm.created_time');

        $sql = "pm.project_id = pa.project_id AND pm.factory_id = f.factory_id AND pm.machine_no = m.machine_id AND pm.currency = c.currency_id AND pm.project_id = '" . $project_id . "'";
        if ($machine != "") {
            $sql .= " AND pm.machine_no = '$machine'";
        }
        if ($version_no != "") {
            $sql .= " AND pm.version_no = '$version_no'";
        }
        if ($store_id != "") {
            $sql .= " AND pm.in_store_id = '$store_id'";
        }
        if ($product != "") {
            $sql .= " AND pm.finish_product = '$product'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND pm.used_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND pm.used_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND pm.used_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['orderby'] = array("pm.production_id");
        //$info['debug'] = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);

        if ($cnt) {
            return $cnt;
        } else {
            return 0;
        }
    }

    function getSalesPendingOrderList($from, $to)
    {
        $cmd = getRequest('cmd');
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 100;
        }

        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $customer = getRequest('customer');
        $delivery_point = getRequest('delivery_point');
        $division_id = getRequest('division_id');
        $district = getRequest('district');
        $area = getRequest('area');
        $wo_no = getRequest('wo_no');
        $project_id = getFromSession('project_id');

        $info = array();
        $info['table'] = SALES_MASTER_TBL . " pm
	    LEFT JOIN " . PROJECT_TBL . " p ON pm.project_id = p.project_id
	    LEFT JOIN " . CURRENCY_TBL . " c ON pm.currency = c.currency_id";

        $info['fields'] = array('pm.voucher_no', 'p.project_name', 'p.location', 'pm.customer as sub_id', 'pm.und_wo_no', 'pm.gate_pass', 'pm.track_no', 'pm.salse_type', 'pm.total_value', "DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date", 'pm.mode_of_payment', 'c.curr_symble', 'pm.bank_name', 'pm.acc_no', 'pm.check_no', 'pm.check_no', 'pm.discount', 'pm.net_payble', 'pm.paid_amount', 'pm.due', 'pm.item_delivery_amount', 'pm.return_amount', 'pm.ref_no', 'pm.created_date', "DATE_FORMAT(pm.created_date,'%d %b %y' ) as date", "pm.reference", "pm.commission_slot", "pm.total_commission", "pm.commission_adv_paid", "pm.commission_total_paid", "pm.commission_total_due", "pm.commission_status", "pm.created_by", "pm.checked_by", "pm.approved_by", "TIME_FORMAT(pm.approved_time, '%d %b %y, %h %i %p') as approved_time", "pm.ref_voucher");

        $sql = "pm.project_id = '" . $project_id . "'  AND pm.item_delivery_amount =0 AND pm.status=1 AND pm.is_deleted=0";
        if ($division_id != "") {
            $sql .= " AND pm.division = '$division_id'";
        }
        if ($district != "") {
            $sql .= " AND pm.district = '$district'";
        }
        if ($area != "") {
            $sql .= " AND pm.area = '$area'";
        }
        if ($customer != "") {
            $sql .= " AND pm.customer = '$customer'";
        }
        if ($delivery_point != "") {
            $sql .= " AND pm.delivery_point = '$delivery_point'";
        }
        if ($wo_no != "") {
            $wo_no = addslashes($wo_no);
            $sql .= " AND pm.ref_voucher LIKE '%$wo_no%'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND pm.sales_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND pm.sales_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND pm.sales_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['orderby'] = array("pm.sales_date DESC LIMIT $from,$to");
        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }
        return $data;
    }

    function getTotalSalesPendingOrderList()
    {
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $customer = getRequest('customer');
        $delivery_point = getRequest('delivery_point');
        $division_id = getRequest('division_id');
        $district = getRequest('district');
        $wo_no = getRequest('wo_no');
        $project_id = getFromSession('project_id');

        $info = array();
        $info['table'] = SALES_MASTER_TBL . " pm
	    LEFT JOIN " . PROJECT_TBL . " p ON pm.project_id = p.project_id
	    LEFT JOIN " . CURRENCY_TBL . " c ON pm.currency = c.currency_id";

        $info['fields'] = array('pm.voucher_no', 'p.project_name', 'p.location', 'pm.customer as sub_id', 'pm.gate_pass', 'pm.track_no', 'pm.salse_type', 'pm.total_value', "DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date", 'pm.mode_of_payment', 'c.curr_symble', 'pm.bank_name', 'pm.acc_no', 'pm.check_no', 'pm.check_no', 'pm.discount', 'pm.net_payble', 'pm.paid_amount', 'pm.due', 'pm.item_delivery_amount', 'pm.return_amount', 'pm.ref_no', 'pm.created_date', "DATE_FORMAT(pm.created_date,'%d %b %y' ) as date", "pm.reference", "pm.commission_slot", "pm.total_commission", "pm.commission_adv_paid", "pm.commission_total_paid", "pm.commission_total_due", "pm.commission_status", "pm.ref_voucher");

        $sql = "pm.project_id = '" . $project_id . "' AND pm.item_delivery_amount =0 AND pm.status=1 AND pm.is_deleted=0";

        if ($division_id != "") {
            $sql .= " AND pm.division = '$division_id'";
        }
        if ($district != "") {
            $sql .= " AND pm.district = '$district'";
        }
        if ($customer != "") {
            $sql .= " AND pm.customer = '$customer'";
        }
        if ($delivery_point != "") {
            $sql .= " AND pm.delivery_point = '$delivery_point'";
        }
        if ($wo_no != "") {
            $wo_no = addslashes($wo_no);
            $sql .= " AND pm.ref_voucher LIKE '%$wo_no%'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND pm.sales_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND pm.sales_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND pm.sales_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['orderby'] = array("pm.sales_date DESC");
        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            return $cnt;
        } else {
            return 0;
        }
    }

    function showEditSalesOrder($msg = null)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['customer_list'] = $comListApp->getCustomerList();
        $data['supplier_list'] = $comListApp->getSupplierList();
        $data['district_list'] = $comListApp->getDistrictList();
        $data['depo_list'] = $comListApp->getDeliveryPointList();
        $data['area_list'] = $comListApp->getAreaList();

        $data['record_list'] = $this->getPendingEditOrderList(getRequest('from'), getRequest('to'));
        $data['totalrecord'] = $this->getTotalPendingEditOrderList();
        require_once(SALES_DELIVERY_EDIT_LIST_SKIN);

        return $data[0];
    }

    function getPendingEditOrderList($from, $to)
    {
        $cmd = getRequest('cmd');
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 100;
        }

        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $customer = getRequest('customer');
        $delivery_point = getRequest('delivery_point');
        $division_id = getRequest('division_id');
        $district = getRequest('district');
        $project_id = getFromSession('project_id');

        $info = array();
        $info['table'] = SALES_MASTER_TBL . ' pm,' . PROJECT_TBL . ' p,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('pm.voucher_no', 'p.project_name', 'p.location', 'pm.customer as sub_id', 'pm.gate_pass', 'pm.track_no', 'pm.salse_type', 'pm.total_value', "DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date", 'pm.mode_of_payment', 'c.curr_symble', 'pm.bank_name', 'pm.acc_no', 'pm.check_no', 'pm.check_no', 'pm.discount', 'pm.net_payble', 'pm.paid_amount', 'pm.due', 'pm.item_delivery_amount', 'pm.return_amount', 'pm.ref_no', 'pm.created_date', "DATE_FORMAT(pm.created_date,'%d %b %y' ) as date", "pm.reference", "pm.commission_slot", "pm.total_commission", "pm.commission_adv_paid", "pm.commission_total_paid", "pm.commission_total_due", "pm.commission_status", "pm.created_by", "pm.approved_by", "TIME_FORMAT(pm.approved_time, '%d %b %y, %h %i %p') as approved_time");

        $sql = "pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '" . $project_id . "'  AND pm.item_delivery_amount =0 AND pm.status=1 AND is_deleted=1";
        if ($division_id != "") {
            $sql .= " AND pm.division = '$division_id'";
        }
        if ($district != "") {
            $sql .= " AND pm.district = '$district'";
        }
        if ($customer != "") {
            $sql .= " AND pm.customer = '$customer'";
        }
        if ($delivery_point != "") {
            $sql .= " AND pm.delivery_point = '$delivery_point'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND pm.sales_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND pm.sales_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND pm.sales_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['orderby'] = array("pm.sales_date DESC LIMIT $from,$to");
        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }
        return $data;
    }

    function getTotalPendingEditOrderList()
    {
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $customer = getRequest('customer');
        $delivery_point = getRequest('delivery_point');
        $division_id = getRequest('division_id');
        $district = getRequest('district');
        $project_id = getFromSession('project_id');

        $info = array();
        $info['table'] = SALES_MASTER_TBL . ' pm,' . PROJECT_TBL . ' p,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('pm.voucher_no', 'p.project_name', 'p.location', 'pm.customer as sub_id', 'pm.gate_pass', 'pm.track_no', 'pm.salse_type', 'pm.total_value', "DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date", 'pm.mode_of_payment', 'c.curr_symble', 'pm.bank_name', 'pm.acc_no', 'pm.check_no', 'pm.check_no', 'pm.discount', 'pm.net_payble', 'pm.paid_amount', 'pm.due', 'pm.item_delivery_amount', 'pm.return_amount', 'pm.ref_no', 'pm.created_date', "DATE_FORMAT(pm.created_date,'%d %b %y' ) as date", "pm.reference", "pm.commission_slot", "pm.total_commission", "pm.commission_adv_paid", "pm.commission_total_paid", "pm.commission_total_due", "pm.commission_status");

        $sql = "pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '" . $project_id . "' AND pm.item_delivery_amount =0 AND pm.status=1 AND is_deleted=1";

        if ($division_id != "") {
            $sql .= " AND pm.division = '$division_id'";
        }
        if ($district != "") {
            $sql .= " AND pm.district = '$district'";
        }
        if ($customer != "") {
            $sql .= " AND pm.customer = '$customer'";
        }
        if ($delivery_point != "") {
            $sql .= " AND pm.delivery_point = '$delivery_point'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND pm.sales_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND pm.sales_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND pm.sales_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['orderby'] = array("pm.sales_date DESC");
        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            return $cnt;
        } else {
            return 0;
        }
    }

    function getSalesDeliveryOrderList($from, $to)
    {
        $cmd = getRequest('cmd');
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 100;
        }

        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $customer = getRequest('customer');
        $delivery_point = getRequest('delivery_point');
        $district = getRequest('district');
        $area = getRequest('area');
        $project_id = getFromSession('project_id');

        $info = array();
        $info['table'] = SALES_MASTER_APP_TBL . ' sm,' . SALES_MASTER_TBL . ' pm,' . SALES_DELIVERY_MASTER_TBL . ' sdm,' . DELIVERY_POINT_TBL . ' d,' . PROJECT_TBL . ' p,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('pm.voucher_no', 'p.project_name', 'd.delivery_point_name', 'p.location', 'pm.customer as sub_id', 'pm.gate_pass', 'pm.track_no', 'pm.salse_type', 'pm.total_value', "DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date", 'pm.mode_of_payment', 'c.curr_symble', 'pm.bank_name', 'pm.acc_no', 'pm.check_no', 'pm.check_no', 'sm.discount', 'sm.net_payble as order_amount', 'sm.approved_amount', 'pm.paid_amount', 'pm.due', 'sdm.total_value as item_delivery_amount', 'pm.return_amount', 'pm.ref_no', 'pm.created_date', "DATE_FORMAT(pm.created_date,'%d %b %y' ) as date", "pm.reference", "pm.commission_slot", "pm.total_commission", "pm.commission_adv_paid", "pm.commission_total_paid", "pm.commission_total_due", "pm.commission_status");

        $sql = "sm.voucher_no=pm.voucher_no AND pm.voucher_no=sdm.voucher_no AND sdm.delivery_point=d.delivery_pid AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '" . $project_id . "' AND pm.status=1";
        if ($district != "") {
            $sql .= " AND pm.district = '$district'";
        }
        if ($area != "") {
            $sql .= " AND pm.area = '$area'";
        }
        if ($customer != "") {
            $sql .= " AND pm.customer = '$customer'";
        }
        if ($delivery_point != "") {
            $sql .= " AND pm.delivery_point = '$delivery_point'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND pm.sales_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND pm.sales_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND pm.sales_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['groupby'] = array("pm.voucher_no");
        $info['orderby'] = array("pm.sales_date DESC LIMIT $from,$to");
        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }
        return $data;
    }

    function getTotalSalesDeliveryOrderList()
    {
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $customer = getRequest('customer');
        $delivery_point = getRequest('delivery_point');
        $district = getRequest('district');
        $area = getRequest('area');
        $project_id = getFromSession('project_id');

        $info = array();
        $info['table'] = SALES_MASTER_APP_TBL . ' sm,' . SALES_MASTER_TBL . ' pm,' . SALES_DELIVERY_MASTER_TBL . ' sdm,' . DELIVERY_POINT_TBL . ' d,' . PROJECT_TBL . ' p,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('pm.voucher_no', 'p.project_name', 'd.delivery_point_name', 'p.location', 'pm.customer as sub_id', 'pm.gate_pass', 'pm.track_no', 'pm.salse_type', 'pm.total_value', "DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date", 'pm.mode_of_payment', 'c.curr_symble', 'pm.bank_name', 'pm.acc_no', 'pm.check_no', 'pm.check_no', 'pm.discount', 'sm.net_payble as order_amount', 'sm.approved_amount', 'pm.paid_amount', 'pm.due', 'sdm.total_value as item_delivery_amount', 'pm.return_amount', 'pm.ref_no', 'pm.created_date', "DATE_FORMAT(pm.created_date,'%d %b %y' ) as date", "pm.reference", "pm.commission_slot", "pm.total_commission", "pm.commission_adv_paid", "pm.commission_total_paid", "pm.commission_total_due", "pm.commission_status");

        $sql = "sm.voucher_no=pm.voucher_no AND pm.voucher_no=sdm.voucher_no AND sdm.delivery_point=d.delivery_pid AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '" . $project_id . "' AND pm.status=1";
        if ($district != "") {
            $sql .= " AND pm.district = '$district'";
        }
        if ($area != "") {
            $sql .= " AND pm.area = '$area'";
        }
        if ($customer != "") {
            $sql .= " AND pm.customer = '$customer'";
        }
        if ($delivery_point != "") {
            $sql .= " AND pm.delivery_point = '$delivery_point'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND pm.sales_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND pm.sales_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND pm.sales_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['groupby'] = array("pm.voucher_no");
        $info['orderby'] = array("pm.sales_date DESC");
        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            return $cnt;
        } else {
            return 0;
        }
    }

    function showUndeliveredSalesOrder()
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['record_list'] = $this->getUDSalesOrderList(getRequest('from'), getRequest('to'));
        $data['totalrecord'] = $this->getTotalUDSalesOrderList(getRequest('from'), getRequest('to'));
        $data['customer_list'] = $comListApp->getCustomerList();
        $data['supplier_list'] = $comListApp->getSupplierList();
        $data['district_list'] = $comListApp->getDistrictList();
        $data['depo_list'] = $comListApp->getDeliveryPointList();
        require_once(SALES_UNDELIVERED_LIST_SKIN);
        return $data[0];
    }

    function getUDSalesOrderList($from, $to)
    {
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 500;
        }
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $customer = getRequest('customer');
        $delivery_point = getRequest('delivery_point');
        $district = getRequest('district');
        $area = getRequest('area');
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = SALES_MASTER_TBL . ' pm,' . SALES_DETAILS_TBL . ' sd,' . SUB_ACC_HEAD_TBL . ' s,' . PROJECT_TBL . ' p,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('pm.voucher_no', 'p.project_name', 'p.location', 'pm.customer', 's.sub_id', 's.sub_head_name', 's.head_details', 'pm.gate_pass', 'pm.track_no', 'pm.salse_type', 'pm.total_value', "DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date", 'pm.mode_of_payment', 'c.curr_symble', 'pm.bank_name', 'pm.acc_no', 'pm.check_no', 'pm.check_no', 'pm.discount', 'pm.net_payble', 'pm.paid_amount', 'pm.due', 'pm.item_delivery_amount', 'pm.return_amount', 'pm.ref_no', 'pm.created_date', "DATE_FORMAT(pm.created_date,'%d %b %y' ) as date", "pm.reference", "pm.commission_slot", "pm.total_commission", "pm.commission_adv_paid", "pm.commission_total_paid", "pm.commission_total_due", "pm.commission_status");

        $sql = "pm.customer = s.sub_id AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '" . $project_id . "' AND order_type!='Sales Opening' AND pm.voucher_no=sd.voucher_no AND sd.undelivery_qty >0 AND pm.status=1";
        if ($district != "") {
            $sql .= " AND pm.district = '$district'";
        }
        if ($area != "") {
            $sql .= " AND pm.area = '$area'";
        }
        if ($customer != "") {
            $sql .= " AND pm.customer = '$customer'";
        }
        if ($delivery_point != "") {
            $sql .= " AND pm.delivery_point = '$delivery_point'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND pm.sales_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND pm.sales_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND pm.sales_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['groupby'] = array("pm.voucher_no");
        $info['orderby'] = array("pm.sales_date DESC LIMIT $from,$to");
        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }
        return $data;
    }

    function getTotalUDSalesOrderList($from, $to)
    {
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $customer = getRequest('customer');
        $delivery_point = getRequest('delivery_point');
        $district = getRequest('district');
        $area = getRequest('area');
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = SALES_MASTER_TBL . ' pm,' . SALES_DETAILS_TBL . ' sd,' . SUB_ACC_HEAD_TBL . ' s,' . PROJECT_TBL . ' p,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('pm.voucher_no');
        $sql = "pm.customer = s.sub_id AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '" . $project_id . "' AND order_type!='Sales Opening' AND pm.voucher_no=sd.voucher_no AND sd.undelivery_qty>0  AND pm.status=1";
        if ($district != "") {
            $sql .= " AND pm.district = '$district'";
        }
        if ($area != "") {
            $sql .= " AND pm.area = '$area'";
        }
        if ($customer != "") {
            $sql .= " AND pm.customer = '$customer'";
        }
        if ($delivery_point != "") {
            $sql .= " AND pm.delivery_point = '$delivery_point'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND pm.sales_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND pm.sales_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND pm.sales_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['groupby'] = array("pm.voucher_no");
        $info['orderby'] = array("pm.sales_date DESC");
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            return $cnt;
        } else {
            return 0;
        }
    }

    function getSalesOrderList($from, $to)
    {
        $cmd = getRequest('cmd');
        if ($cmd == "sal_dtl") {
            if ($from == "" && $to == "") {
                $from = 0;
                $to = 500;
            }
        } elseif ($cmd == "sales.return") {
            if ($from == "" && $to == "") {
                $from = 0;
                $to = 500;
            }
        } else {
            if ($from == "" && $to == "") {
                $from = 0;
                $to = 100;
            }
        }


        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $customer = getRequest('customer');
        $delivery_point = getRequest('delivery_point');
        $district = getRequest('district');
        $area = getRequest('area');
        $project_id = getFromSession('project_id');
        /*
	$SQL = "
	SELECT pm.voucher_no,p.project_name,p.location,COALESCE((s.sub_id), (sp.supplier_code)) as sub_id,COALESCE((s.sub_head_name), (sp.name)) as sub_head_name,COALESCE((s.head_details), (sp.address)) as head_details,pm.gate_pass,pm.track_no,pm.salse_type,pm.total_value,DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date,pm.mode_of_payment,c.curr_symble,pm.bank_name,pm.acc_no,pm.check_no,pm.check_no,pm.discount,pm.net_payble,pm.paid_amount,pm.due,SUM(sd.qty) as order_qty,SUM(sdc.delivery_qty) as delivery_qty,pm.item_delivery_amount,pm.return_amount,pm.ref_no,pm.created_date,DATE_FORMAT(pm.created_date,'%d %b %y' ) as date,pm.reference,pm.commission_slot,pm.total_commission,pm.commission_adv_paid,pm.commission_total_paid,pm.commission_total_due,pm.commission_status
	FROM ".SALES_MASTER_TBL." pm
	LEFT JOIN ".SALES_DETAILS_TBL." sd ON sd.voucher_no =pm.voucher_no
	LEFT JOIN ".SALES_DELIVERY_CHALLAN_TBL." sdc ON sdc.sal_detail_id =sd.sal_detail_id
	LEFT JOIN ".SUB_ACC_HEAD_TBL." s ON s.sub_id =pm.customer
	LEFT JOIN ".SUPPLIER_TBL." sp ON sp.supplier_code = pm.customer
	LEFT JOIN ".PROJECT_TBL." p ON p.project_id  =pm.project_id
	LEFT JOIN ".CURRENCY_TBL." c ON c.currency_id  =pm.currency

	WHERE pm.project_id = '".$project_id."'
	";

	if($district!=""){
		$SQL.=" AND pm.district = '$district'";
	}
	if($area!=""){
		$SQL.=" AND pm.area = '$area'";
	}
	if($customer!=""){
		$SQL.=" AND pm.customer = '$customer'";
	}
	if($delivery_point!=""){
		$SQL.=" AND pm.delivery_point = '$delivery_point'";
	}
	if($date_from!="" && $date_to ==""){
		$SQL.=" AND pm.sales_date >= '$date_from'";
	}elseif($date_from=="" && $date_to !=""){
		$SQL.=" AND pm.sales_date <= '$date_to'";
	}elseif($date_from!="" && $date_to !=""){
		$SQL.=" AND pm.sales_date BETWEEN '$date_from' AND '$date_to'";
	}
	$SQL.=" GROUP BY pm.voucher_no";
	$SQL.=" ORDER BY pm.sales_date DESC LIMIT $from,$to";
	echo $SQL; exit;

	$result         = query($SQL);
	//$result = _mysql_query_wrapper($SQL);

	$data           = array();
	$cnt = count($result);
	if($cnt) {
		foreach($result as $value)  {
		$data[]	= $value;
		}
	}

	//dBug($alldata);
	return $data;
	*/

        $info = array();
        $info['table'] = SALES_MASTER_TBL . ' pm,' . PROJECT_TBL . ' p,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('pm.voucher_no', 'p.project_name', 'p.location', 'pm.customer as sub_id', 'pm.gate_pass', 'pm.track_no', 'pm.salse_type', 'pm.total_value', "DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date", 'pm.mode_of_payment', 'c.curr_symble', 'pm.bank_name', 'pm.acc_no', 'pm.check_no', 'pm.check_no', 'pm.discount', 'pm.net_payble', 'pm.paid_amount', 'pm.due', 'pm.item_delivery_amount', 'pm.return_amount', 'pm.ref_no', 'pm.created_date', "DATE_FORMAT(pm.created_date,'%d %b %y' ) as date", "pm.reference", "pm.commission_slot", "pm.total_commission", "pm.commission_adv_paid", "pm.commission_total_paid", "pm.commission_total_due", "pm.commission_status");

        $sql = "pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '" . $project_id . "' AND pm.status=1";
        if ($district != "") {
            $sql .= " AND pm.district = '$district'";
        }
        if ($area != "") {
            $sql .= " AND pm.area = '$area'";
        }
        if ($customer != "") {
            $sql .= " AND pm.customer = '$customer'";
        }
        if ($delivery_point != "") {
            $sql .= " AND pm.delivery_point = '$delivery_point'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND pm.sales_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND pm.sales_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND pm.sales_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['orderby'] = array("pm.sales_date DESC LIMIT $from,$to");
        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }
        return $data;
    }

    function getTotalSalesOrderList($from, $to)
    {
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $customer = getRequest('customer');
        $delivery_point = getRequest('delivery_point');
        $district = getRequest('district');
        $area = getRequest('area');
        $project_id = getFromSession('project_id');
        /*
	$SQL = "
	SELECT pm.voucher_no
	FROM ".SALES_MASTER_TBL." pm
	LEFT JOIN ".SUB_ACC_HEAD_TBL." s ON s.sub_id =pm.customer
	LEFT JOIN ".SUPPLIER_TBL." sp ON sp.supplier_code = pm.customer
	LEFT JOIN ".PROJECT_TBL." p ON p.project_id  =pm.project_id
	LEFT JOIN ".CURRENCY_TBL." c ON c.currency_id  =pm.currency

	WHERE pm.project_id = '".$project_id."'
	";

	if($district!=""){
		$SQL.=" AND pm.district = '$district'";
	}
	if($area!=""){
		$SQL.=" AND pm.area = '$area'";
	}
	if($customer!=""){
		$SQL.=" AND pm.customer = '$customer'";
	}
	if($delivery_point!=""){
		$SQL.=" AND pm.delivery_point = '$delivery_point'";
	}
	if($date_from!="" && $date_to ==""){
		$SQL.=" AND pm.sales_date >= '$date_from'";
	}elseif($date_from=="" && $date_to !=""){
		$SQL.=" AND pm.sales_date <= '$date_to'";
	}elseif($date_from!="" && $date_to !=""){
		$SQL.=" AND pm.sales_date BETWEEN '$date_from' AND '$date_to'";
	}
	$SQL.=" ORDER BY pm.sales_date DESC";
	//echo $SQL; die;

	$result         = query($SQL);
	//$result = _mysql_query_wrapper($SQL);

	$cnt = count($result);
	*/
        $info = array();
        $info['table'] = SALES_MASTER_TBL . ' pm,' . PROJECT_TBL . ' p,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('pm.voucher_no', 'p.project_name', 'p.location', 'pm.customer as sub_id', 'pm.gate_pass', 'pm.track_no', 'pm.salse_type', 'pm.total_value', "DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date", 'pm.mode_of_payment', 'c.curr_symble', 'pm.bank_name', 'pm.acc_no', 'pm.check_no', 'pm.check_no', 'pm.discount', 'pm.net_payble', 'pm.paid_amount', 'pm.due', 'pm.item_delivery_amount', 'pm.return_amount', 'pm.ref_no', 'pm.created_date', "DATE_FORMAT(pm.created_date,'%d %b %y' ) as date", "pm.reference", "pm.commission_slot", "pm.total_commission", "pm.commission_adv_paid", "pm.commission_total_paid", "pm.commission_total_due", "pm.commission_status");

        $sql = "pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '" . $project_id . "' AND pm.status=1";
        if ($district != "") {
            $sql .= " AND pm.district = '$district'";
        }
        if ($area != "") {
            $sql .= " AND pm.area = '$area'";
        }
        if ($customer != "") {
            $sql .= " AND pm.customer = '$customer'";
        }
        if ($delivery_point != "") {
            $sql .= " AND pm.delivery_point = '$delivery_point'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND pm.sales_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND pm.sales_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND pm.sales_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['orderby'] = array("pm.sales_date DESC");
        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            return $cnt;
        } else {
            return 0;
        }
    }

    //======= Batch Setup List======
    function showBatchList($msg = null)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['record_list'] = $this->getBatchSetupList(getRequest('from'), getRequest('to'));
        $data['totalrecord'] = $this->getTotalBatchSetupList(getRequest('from'), getRequest('to'));
        $data['product_list'] = $comListApp->getProductList();
        $data['batch_list'] = $this->getBatchList();
        require_once(PO_BATCH_SKIN_LIST);
        return $data[0];
    }

    function getBatchSetupList($from, $to)
    {
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 500;
        }
        $product = getRequest('product');
        $batch = getRequest('batch');
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = PO_BATCH_MASTER_TBL . ' pm,' . PROJECT_TBL . ' p';
        $info['fields'] = array('pm.*', 'p.project_name', 'p.location');

        $sql = "pm.project_id='" . $project_id . "'";

        if ($batch != "") {
            $sql .= " AND pm.batch_id = '$batch'";
        }
        $info['where'] = $sql;
        $info['groupby'] = array("pm.batch_id");
        $info['orderby'] = array("pm.batch_id DESC LIMIT $from,$to");
        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }
        return $data;
    }

    function getTotalBatchSetupList($from, $to)
    {
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 500;
        }
        $product = getRequest('product');
        $batch = getRequest('batch');
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = PO_BATCH_MASTER_TBL . ' pm,' . PROJECT_TBL . ' p';
        $info['fields'] = array('pm.*', 'p.project_name', 'p.location');

        $sql = "pm.project_id='" . $project_id . "'";

        if ($batch != "") {
            $sql .= " AND pm.batch_id = '$batch'";
        }
        $info['where'] = $sql;
        $info['groupby'] = array("pm.batch_id");
        $info['orderby'] = array("pm.batch_id DESC");
        //$info['debug']= true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            return $cnt;
        } else {
            return 0;
        }
    }

    function getBatchList()
    {
        $data = array();
        $info = array();
        $info['table'] = PO_BATCH_MASTER_TBL;
        $res = select($info);
        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        //dumpVar($data);
        return $data;
    }

    //============ Delivery Challan List =====

    function showSalesDeliveryChallan()
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['sdcrecord_list'] = $this->getSalesDeliveryChallanList(getRequest('from'), getRequest('to'));
        $data['totalrecord'] = $this->getTotalSalesDeliveryChallanList(getRequest('from'), getRequest('to'));
        $customer_list = $comListApp->getCustomerList();
        $receivable_list = $comListApp->getCustomerListReceivable();
        $merged = array_merge($customer_list, $receivable_list);

        $unique = array();
        foreach ($merged as $item) {
            $unique[$item->sub_id] = $item;
        }

        $data['customer_list'] = array_values($unique);

        $data['supplier_list'] = $comListApp->getSupplierList();
        $data['depo_list'] = $comListApp->getDeliveryPointList();

        require_once(SALES_DELIVERY_CHALLAN_LIST_SKIN);
        return $data[0];
    }


    function showCustomDeliveryChallan()
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['sdcrecord_list'] = $this->getSalesDeliveryChallanList(getRequest('from'), getRequest('to'));
        $data['totalrecord'] = $this->getTotalSalesDeliveryChallanList(getRequest('from'), getRequest('to'));
        $data['customer_list'] = $comListApp->getCustomerList();
        $data['supplier_list'] = $comListApp->getSupplierList();
        $data['depo_list'] = $comListApp->getDeliveryPointList();
        require_once(SALES_CUSTOM_DELIVERY_CHALLAN_LIST_SKIN);
        return $data[0];
    }


    function getSalesDeliveryChallanList($from, $to)
    {
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 100;
        }
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $customer = getRequest('customer');
        $delivery_point = getRequest('delivery_point');
        $project_id = getFromSession('project_id');

        $is_aging_over = getRequest('is_aging_over');


        $direct_invoice = 0;
        $form_type = getRequest('form_type');
        if (isset($form_type) && $form_type == "custom_invoice") {
            $direct_invoice = 1;
        }

        $today = date('Y-m-d');

        $info = array();
        $info['table'] = SALES_MASTER_TBL . ' pm,' . SALES_DELIVERY_MASTER_TBL . ' sdm,' . PROJECT_TBL . ' p,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('sdm.sales_delivery_master_id', 'pm.voucher_no', 'pm.po_no', 'pm.wo_no', 'p.project_name', 'p.location', 'pm.customer', 'pm.reference', 'pm.gate_pass', 'pm.track_no', 'pm.salse_type', 'pm.total_value', 'pm.discount', "DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date", "pm.service_charge", "DATE_FORMAT(sdm.delivery_date,'%d %b %y') as delivery_date", "pm.aging_date", "DATE_FORMAT(pm.aging_date,'%d %b %y') as agingDate", 'pm.mode_of_payment', 'c.curr_symble', 'pm.bank_name', 'pm.acc_no', 'pm.check_no', 'pm.check_no', 'pm.item_delivery_amount', 'pm.net_payble', 'pm.paid_amount', 'pm.due', 'pm.ref_no', 'pm.created_date', 'sdm.challan_no', 'sdm.consignee', 'pm.created_by', 'pm.approved_by', 'pm.approved_time', 'pm.checked_by', 'pm.general_discount_amount', 'pm.exclusive_discount_amount', 'pm.additional_discount', 'pm.additional_cost', 'pm.vat_type', 'pm.direct_invoice', 'pm.additional_vat_percent', 'pm.additional_vat_amount,pm.total_p_weight,pm.vehicle_weight');

        $sql = "pm.voucher_no=sdm.voucher_no AND pm.project_id=p.project_id AND pm.currency=c.currency_id AND pm.project_id='" . $project_id . "' AND pm.item_delivery_amount > 0 AND pm.direct_invoice = '$direct_invoice'";
        if ($customer != "") {
            $sql .= " AND pm.customer = '$customer'";
        }
        if ($delivery_point != "") {
            $sql .= " AND sdm.delivery_point = '$delivery_point'";
        }

        if ($date_from != "" && $date_to == "") {
            $sql .= " AND sdm.delivery_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND sdm.delivery_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND sdm.delivery_date BETWEEN '$date_from' AND '$date_to'";
        }

        if ($is_aging_over == 1) {
            $sql .= " AND pm.aging_date <= '$today'";
        }

        $info['where'] = $sql;
        $info['groupby'] = array("sdm.sales_delivery_master_id");
        $info['orderby'] = array("pm.voucher_no,sdm.sales_delivery_master_id DESC LIMIT $from,$to");
        //$info['debug']  = true;

        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }

        return $data;
    }

    function getTotalSalesDeliveryChallanList($from, $to)
    {
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $customer = getRequest('customer');
        $delivery_point = getRequest('delivery_point');
        $project_id = getFromSession('project_id');

        $direct_invoice = 0;
        $form_type = getRequest('form_type');
        if (isset($form_type) && $form_type == "custom_invoice") {
            $direct_invoice = 1;
        }

        $info = array();
        $info['table'] = SALES_MASTER_TBL . ' pm,' . SALES_DELIVERY_MASTER_TBL . ' sdm,' . PROJECT_TBL . ' p,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('sdm.sales_delivery_master_id');
        $sql = "pm.voucher_no=sdm.voucher_no AND pm.project_id=p.project_id AND pm.currency=c.currency_id AND pm.project_id='" . $project_id . "' AND pm.item_delivery_amount >0 AND pm.direct_invoice = '$direct_invoice'";
        if ($customer != "") {
            $sql .= " AND pm.customer = '$customer'";
        }
        if ($delivery_point != "") {
            $sql .= " AND sdm.delivery_point = '$delivery_point'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND sdm.delivery_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND sdm.delivery_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND sdm.delivery_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['groupby'] = array("sdm.sales_delivery_master_id");
        $info['orderby'] = array("pm.voucher_no,sdm.sales_delivery_master_id DESC");
        $result = select($info);
        /*
	//====== New SQL =====
	$SQL = "
	SELECT sdm.sales_delivery_master_id,pm.voucher_no,pm.po_no,pm.wo_no,p.project_name,p.location,pm.customer,COALESCE((s.sub_id), (sp.supplier_code)) as sub_id,COALESCE((s.sub_head_name), (sp.name)) as sub_head_name,COALESCE((s.head_details), (sp.address)) as head_details,COALESCE((s.phone), (sp.phone)) as phone,COALESCE((s.mobile), (sp.mobile)) as mobile,COALESCE((s.email), (sp.email)) as email,COALESCE((s.att_name1), (sp.contact_person)) as att_name1,COALESCE((s.att_designation1), (sp.designation)) as att_designation1,COALESCE((s.att_mobile1), (sp.contact_person_mobile)) as att_mobile1,pm.gate_pass,pm.track_no,pm.salse_type,pm.total_value,DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date,pm.service_charge,DATE_FORMAT(sdm.delivery_date,'%d %b %y' ) as delivery_date,pm.mode_of_payment,c.curr_symble,pm.bank_name,pm.acc_no,pm.check_no,pm.discount,pm.net_payble,pm.paid_amount,pm.due,pm.item_delivery_amount,pm.return_amount,pm.ref_no,pm.created_date,DATE_FORMAT(pm.created_date,'%d %b %y' ) as date,pm.reference,sdm.challan_no,sdm.consignee
	FROM ".SALES_MASTER_TBL." pm
	LEFT JOIN ".SALES_DELIVERY_MASTER_TBL." sdm ON BINARY sdm.voucher_no = pm.voucher_no
	LEFT JOIN ".SUB_ACC_HEAD_TBL." s ON BINARY s.sub_id = pm.customer
	LEFT JOIN ".SUPPLIER_TBL." sp ON BINARY sp.supplier_code = pm.customer
	LEFT JOIN ".PROJECT_TBL." p ON p.project_id  =pm.project_id
	LEFT JOIN ".CURRENCY_TBL." c ON c.currency_id  =pm.currency

	WHERE pm.project_id = '".$project_id."'
	";

	if($customer!=""){
		$SQL.=" AND pm.customer = '$customer'";
	}
	if($delivery_point!=""){
		$SQL.=" AND sdm.delivery_point = '$delivery_point'";
	}
	if($date_from!="" && $date_to ==""){
		$SQL.=" AND sdm.delivery_date >= '$date_from'";
	}elseif($date_from=="" && $date_to !=""){
		$SQL.=" AND sdm.delivery_date <= '$date_to'";
	}elseif($date_from!="" && $date_to !=""){
		$SQL.=" AND sdm.delivery_date BETWEEN '$date_from' AND '$date_to'";
	}
	$SQL.=" GROUP BY sdm.sales_delivery_master_id ORDER BY sales_delivery_master_id DESC";
	//echo $SQL;

	$result         = query($SQL);
	*/
        $cnt = count($result);
        if ($cnt) {
            return $cnt;
        } else {
            return 0;
        }

    }


    //============ Stock Transfer List =====
    function showStockTransferList()
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['sdcrecord_list'] = $this->getStockTransferList(getRequest('from'), getRequest('to'));
        $data['totalrecord'] = $this->getTotalStockTransferList(getRequest('from'), getRequest('to'));
        $data['depo_list'] = $comListApp->getDeliveryPointList(true);
        require_once(SHOW_STOCK_TRANSFER_LIST_SKIN);
        return $data[0];
    }

    function showUnapprovedStockTransferList()
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['sdcrecord_list'] = $this->getStockTransferList(getRequest('from'), getRequest('to'), true);
        $data['totalrecord'] = $this->getTotalStockTransferList(getRequest('from'), getRequest('to'), true);
        $data['depo_list'] = $comListApp->getDeliveryPointList(true);

        $user_store = $comListApp->getUserStore();
        $data['user_store'] = array_map('trim', explode(',', $user_store));

        require_once(SHOW_STOCK_UNAPPROVED_TRANSFER_LIST_SKIN);
        return $data[0];
    }

    function getStockTransferList($from, $to, $pending = false)
    {
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 500;
        }
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $delivery_point = getRequest('delivery_point');
        $project_id = getFromSession('project_id');

        $master_table = STOCK_TRANSFER_MASTER_TBL;
        if ($pending) {
            $master_table = "pending_stock_transfer_master";
        }

        $info = array();
        //$info['table'] = $master_table . ' tm,' . DELIVERY_POINT_TBL . ' d,' . PROJECT_TBL . ' p,' . CURRENCY_TBL . ' c';
        //$info['fields'] = array('tm.transfer_no', 'tm.transfer_from', 'tm.delivery_point', 'tm.total_amount', 'p.project_name', 'p.location', 'd.delivery_point_name', 'd.details', "DATE_FORMAT(tm.transfer_date,'%d %b %y' ) as transfer_date", 'tm.created_by', 'tm.created_date', 'tm.narration','tm.product_convert', 'tm.approved_status', 'tm.approved_by', 'tm.approved_time');

        //if ($pending) {
        //            $info['fields'] = array('tm.id', 'tm.transfer_from', 'tm.delivery_point', 'tm.total_amount', 'p.project_name', 'p.location', 'd.delivery_point_name', 'd.details', "DATE_FORMAT(tm.transfer_date,'%d %b %y' ) as transfer_date", 'tm.created_by', 'tm.created_date', 'tm.narration','tm.product_convert');
        //}

        //$sql = "tm.delivery_point=d.delivery_pid AND tm.project_id=p.project_id AND tm.currency=c.currency_id AND tm.project_id='" . $project_id . "'";

        $info['table'] = $master_table . " tm
        LEFT JOIN " . DELIVERY_POINT_TBL . " d ON tm.delivery_point = d.delivery_pid
        LEFT JOIN " . PROJECT_TBL . " p ON tm.project_id = p.project_id
        LEFT JOIN " . CURRENCY_TBL . " c ON tm.currency = c.currency_id
        LEFT JOIN " . PRODUCT_TBL . " pr ON pr.product_id = tm.finish_item";

        $info['fields'] = array('tm.transfer_no', 'tm.transfer_from', 'tm.delivery_point', 'tm.total_amount', 'p.project_name', 'p.location', 'd.delivery_point_name', 'd.details', "DATE_FORMAT(tm.transfer_date,'%d %b %y' ) as transfer_date", 'tm.created_by', 'tm.created_date', 'tm.narration', 'tm.product_convert', 'tm.approved_status', 'tm.approved_by', 'tm.approved_time', 'tm.job_name', 'pr.product_name as finish_item_name');

        if ($pending) {
            $info['fields'] = array('tm.id', 'tm.transfer_from', 'tm.delivery_point', 'tm.total_amount', 'p.project_name', 'p.location', 'd.delivery_point_name', 'd.details', "DATE_FORMAT(tm.transfer_date,'%d %b %y' ) as transfer_date", 'tm.created_by', 'tm.created_date', 'tm.narration', 'tm.product_convert', 'tm.job_name', 'pr.product_name as finish_item_name');
        }

        $sql = "tm.project_id = '" . $project_id . "'";


        if ($delivery_point != "") {
            $sql .= " AND tm.delivery_point = '$delivery_point'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND tm.transfer_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND tm.transfer_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND tm.transfer_date BETWEEN '$date_from' AND '$date_to'";
        }

        $info['where'] = $sql;
        $info['groupby'] = array("tm.transfer_no");
        $info['orderby'] = array("tm.transfer_no,tm.delivery_point DESC LIMIT $from,$to");
        if ($pending) {
            $info['groupby'] = array("tm.id");
            $info['orderby'] = array("tm.id,tm.delivery_point DESC LIMIT $from,$to");
        }
        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }

        return $data;
    }

    function getTotalStockTransferList($from, $to, $pending = false)
    {
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $delivery_point = getRequest('delivery_point');
        $project_id = getFromSession('project_id');

        $master_table = STOCK_TRANSFER_MASTER_TBL;
        if ($pending) {
            $master_table = "pending_stock_transfer_master";
        }

        $info = array();
        $info['table'] = $master_table . ' tm,' . DELIVERY_POINT_TBL . ' d,' . PROJECT_TBL . ' p,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('tm.transfer_no');
        if ($pending) {
            $info['fields'] = array('tm.id');
        }
        $sql = "tm.delivery_point=d.delivery_pid AND tm.project_id=p.project_id AND tm.currency=c.currency_id AND tm.project_id='" . $project_id . "'";
        if ($delivery_point != "") {
            $sql .= " AND tm.delivery_point = '$delivery_point'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND tm.transfer_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND tm.transfer_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND tm.transfer_date BETWEEN '$date_from' AND '$date_to'";
        }

        $info['where'] = $sql;
        $info['groupby'] = array("tm.transfer_no");
        $info['orderby'] = array("tm.transfer_no,tm.delivery_point DESC");
        if ($pending) {
            $info['groupby'] = array("tm.id");
            $info['orderby'] = array("tm.id,tm.delivery_point DESC LIMIT $from,$to");
        }
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            return $cnt;
        } else {
            return 0;
        }
    }

    //============ Stock Verify List =====
    function showStockVerifyList()
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['sdcrecord_list'] = $this->getStockVerifyList(getRequest('from'), getRequest('to'));
        $data['totalrecord'] = $this->getTotalStockVerifyList(getRequest('from'), getRequest('to'));
        $data['depo_list'] = $comListApp->getDeliveryPointList();
        require_once(SHOW_STOCK_VERIFY_LIST_SKIN);
        return $data[0];
    }

    function getStockVerifyList($from, $to)
    {
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 500;
        }
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $delivery_point = getRequest('delivery_point');
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = STOCK_VERIFY_MASTER_TBL . ' tm LEFT JOIN ' . DELIVERY_POINT_TBL . ' d ON tm.delivery_point = d.delivery_pid LEFT JOIN ' . PROJECT_TBL . ' p ON tm.project_id = p.project_id LEFT JOIN ' . CURRENCY_TBL . ' c ON tm.currency = c.currency_id';

        $info['fields'] = array('tm.verify_no', 'tm.delivery_point', 'tm.total_amount', 'p.project_name', 'p.location', 'd.delivery_point_name', 'd.details', "DATE_FORMAT(tm.verification_date,'%d %b %y') as verification_date", 'tm.created_by', 'tm.created_date'
        );

// ONLY filtering conditions go here
        $sql = "tm.project_id = '" . $project_id . "'";

        if ($delivery_point != "") {
            $sql .= " AND tm.delivery_point = '$delivery_point'";
        }

        if ($date_from != "" && $date_to == "") {
            $sql .= " AND tm.verification_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND tm.verification_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND tm.verification_date BETWEEN '$date_from' AND '$date_to'";
        }

        $info['where'] = $sql;

        $info['groupby'] = array("tm.verify_no");
        $info['orderby'] = array("tm.verify_no, tm.delivery_point DESC LIMIT $from,$to");
        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }
        return $data;
    }

    function getTotalStockVerifyList($from, $to)
    {
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $delivery_point = getRequest('delivery_point');
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = STOCK_VERIFY_MASTER_TBL . ' tm LEFT JOIN ' . DELIVERY_POINT_TBL . ' d ON tm.delivery_point = d.delivery_pid LEFT JOIN ' . PROJECT_TBL . ' p ON tm.project_id = p.project_id LEFT JOIN ' . CURRENCY_TBL . ' c ON tm.currency = c.currency_id';

        $info['fields'] = array('tm.verify_no', 'tm.delivery_point', 'tm.total_amount', 'p.project_name', 'p.location', 'd.delivery_point_name', 'd.details', "DATE_FORMAT(tm.verification_date,'%d %b %y') as verification_date", 'tm.created_by', 'tm.created_date'
        );

// ONLY filtering conditions go here
        $sql = "tm.project_id = '" . $project_id . "'";

        if ($delivery_point != "") {
            $sql .= " AND tm.delivery_point = '$delivery_point'";
        }

        if ($date_from != "" && $date_to == "") {
            $sql .= " AND tm.verification_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND tm.verification_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND tm.verification_date BETWEEN '$date_from' AND '$date_to'";
        }

        $info['where'] = $sql;
        $info['groupby'] = array("tm.verify_no");
        $info['orderby'] = array("tm.verify_no,tm.delivery_point DESC");

        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            return $cnt;
        } else {
            return 0;
        }
    }

    //============ Sales Return List =====
    function showSalesReturnList()
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['sdcrecord_list'] = $this->getSalesReturnList(getRequest('from'), getRequest('to'));
        $data['totalrecord'] = $this->getTotalSalesReturnList(getRequest('from'), getRequest('to'));
        $data['customer_list'] = $comListApp->getCustomerList();
        require_once(SHOW_SALES_RETURN_LIST_SKIN);
        return $data[0];
    }

    function getSalesReturnList($from, $to)
    {
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 500;
        }
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $customer = getRequest('customer');
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = SALES_RETURN_MASTER_TBL . ' sm,' . SUB_ACC_HEAD_TBL . ' s,' . DELIVERY_POINT_TBL . ' d,' . PROJECT_TBL . ' p,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('sm.voucher_no', 'sm.customer', 'sm.baddebts_godown', 'd.delivery_point_name', 'd.details', 'sm.intact_godown', 'sm.previour_balance', 'sm.total_sales_return', 'sm.total_baddebts', 'sm.discount_percent', 'sm.discount_amount', 'sm.net_payble', 'p.project_name', 'p.location', 's.sub_head_name', 's.head_details', 's.phone', 's.mobile', 's.email', 's.att_name1', 's.att_designation1', 's.att_mobile1', "DATE_FORMAT(sm.return_date,'%d %b %y' ) as return_date", 'sm.created_by', 'sm.created_time');

        $sql = "sm.customer=s.sub_id AND sm.baddebts_godown=d.delivery_pid AND sm.project_id=p.project_id AND sm.currency=c.currency_id 
	AND sm.project_id='" . $project_id . "' AND sm.status=1 ";
        if ($customer != "") {
            $sql .= " AND sm.customer = '$customer'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND sm.return_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND sm.return_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND sm.return_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['groupby'] = array("sm.voucher_no");
        $info['orderby'] = array("sm.voucher_no,sm.customer DESC LIMIT $from,$to");
        //$info['debug']= true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }
        return $data;
    }

    function getTotalSalesReturnList($from, $to)
    {
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $customer = getRequest('customer');
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = SALES_RETURN_MASTER_TBL . ' sm,' . SUB_ACC_HEAD_TBL . ' s,' . DELIVERY_POINT_TBL . ' d,' . PROJECT_TBL . ' p,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('sm.voucher_no');

        $sql = "sm.customer=s.sub_id AND sm.baddebts_godown=d.delivery_pid AND sm.project_id=p.project_id AND sm.currency=c.currency_id 
	AND sm.project_id='" . $project_id . "' AND sm.status=1 ";
        if ($customer != "") {
            $sql .= " AND sm.customer = '$customer'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND sm.return_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND sm.return_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND sm.return_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['groupby'] = array("sm.voucher_no");
        $info['orderby'] = array("sm.voucher_no,sm.customer DESC");
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            return $cnt;
        } else {
            return 0;
        }
    }

    //=========== Cash Book ========
    function showCashBookList()
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['record_list'] = $this->getCashBookList(getRequest('from'), getRequest('to'));
        $data['totalrecord'] = $this->getTotalCashBookList(getRequest('from'), getRequest('to'));
        $data['customer_list'] = $comListApp->getCustomerList();
        require_once(SHOW_CASH_BOOK_LIST_SKIN);
        return $data[0];
    }

    function getCashBookList($from, $to)
    {
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 500;
        }
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $branch_id = getRequest('branch_id');
        $post_by = getRequest('created_by');
        if ($date_from == "") {
            $date_from = date("Y-m-d");
        }
        if ($date_to == "") {
            $date_to = date("Y-m-d");
        }
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = DEVIT_VOUCHAR_TBL . ' t,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('t.voucher_no', 't.custom_voucher_no', 't.account_head', 't.project_id', 't.head_type', 't.received_id', 'c.curr_symble', 't.mode_of_payment', 't.bank_name', 't.acc_no', 't.check_no', "DATE_FORMAT(t.check_issue_date,'%d %b %y' ) as check_issue_date", 't.ref_no', 't.vouchar_type', 't.transaction_type', 't.transaction_name', 't.delivery_bag_qty', 't.credit', 't.debit', 't.service_charge', 't.description', "DATE_FORMAT(t.created_date,'%d %b %y' ) as created_date", 't.created_by');
        $sql = "t.currency = c.currency_id AND t.project_id = '$project_id' AND t.mode_of_payment='Cash'";
        if ($branch_id != "") {
            $sql .= " AND t.branch_id = '$branch_id'";
        }
        if ($post_by != "") {
            $sql .= " AND t.created_by = '$post_by'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND t.created_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND t.created_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND t.created_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['orderby'] = array("t.created_date,TIME(t.transaction_date) DESC LIMIT $from,$to");
        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }
        return $data;
    }

    function getTotalCashBookList($from, $to)
    {
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $branch_id = getRequest('branch_id');
        $post_by = getRequest('created_by');
        if ($date_from == "") {
            $date_from = date("Y-m-d");
        }
        if ($date_to == "") {
            $date_to = date("Y-m-d");
        }
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = DEVIT_VOUCHAR_TBL . ' t,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('t.voucher_no');
        $sql = "t.currency = c.currency_id AND t.project_id = '$project_id' AND t.mode_of_payment='Cash'";
        if ($branch_id != "") {
            $sql .= " AND t.branch_id = '$branch_id'";
        }
        if ($post_by != "") {
            $sql .= " AND t.created_by = '$post_by'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND t.created_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND t.created_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND t.created_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['orderby'] = array("t.created_date,TIME(t.transaction_date) DESC");
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            return $cnt;
        } else {
            return 0;
        }
    }

    //=========== Cash Book ========
    function showBankBookList()
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['record_list'] = $this->getBankBookList(getRequest('from'), getRequest('to'));
        $data['totalrecord'] = $this->getTotalBankBookList(getRequest('from'), getRequest('to'));
        $data['customer_list'] = $comListApp->getCustomerList();
        require_once(SHOW_BANK_BOOK_LIST_SKIN);
        return $data[0];
    }

    function getBankBookList($from, $to)
    {
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 500;
        }
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $branch_id = getRequest('branch_id');
        if ($date_from == "") {
            $date_from = date("Y-m-d");
        }
        if ($date_to == "") {
            $date_to = date("Y-m-d");
        }
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = DEVIT_VOUCHAR_TBL . ' t,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('t.voucher_no', 't.custom_voucher_no', 't.account_head', 't.project_id', 't.head_type', 't.received_id', 'c.curr_symble', 't.mode_of_payment', 't.bank_name', 't.acc_no', 't.check_no', "DATE_FORMAT(t.check_issue_date,'%d %b %y' ) as check_issue_date", 't.ref_no', 't.vouchar_type', 't.transaction_type', 't.transaction_name', 't.delivery_bag_qty', 't.credit', 't.debit', 't.service_charge', 't.description', "DATE_FORMAT(t.created_date,'%d %b %y' ) as created_date", 't.created_by');
        $sql = "t.currency = c.currency_id AND t.project_id = '$project_id' AND t.mode_of_payment='Bank'";
        if ($branch_id != "") {
            $sql .= " AND t.branch_id = '$branch_id'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND t.created_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND t.created_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND t.created_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['orderby'] = array("t.created_date,TIME(t.transaction_date) DESC LIMIT $from,$to");
        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }
        return $data;
    }

    function getTotalBankBookList($from, $to)
    {
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $branch_id = getRequest('branch_id');
        if ($date_from == "") {
            $date_from = date("Y-m-d");
        }
        if ($date_to == "") {
            $date_to = date("Y-m-d");
        }
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = DEVIT_VOUCHAR_TBL . ' t,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('t.voucher_no');
        $sql = "t.currency = c.currency_id AND t.project_id = '$project_id' AND t.mode_of_payment='Bank'";
        if ($branch_id != "") {
            $sql .= " AND t.branch_id = '$branch_id'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND t.created_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND t.created_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND t.created_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['orderby'] = array("t.created_date,TIME(t.transaction_date) DESC");
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            return $cnt;
        } else {
            return 0;
        }
    }

    //======== Pending Voucher List ========
    function showPendingVoucherList()
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['record_list'] = $this->getPendingVoucherList(getRequest('from'), getRequest('to'));
        $data['totalrecord'] = $this->getTotalPendingVoucherList();
        $data['customer_list'] = $comListApp->getCustomerList();
        require_once(SHOW_PENDING_ONLINE_VOUCHER_SKIN);
        return $data[0];
    }

    function showPendingMoneyReceiptList()
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['record_list'] = $this->getPendingVoucherList(getRequest('from'), getRequest('to'), 1);
        $data['totalrecord'] = $this->getTotalPendingVoucherList(1);
        $data['customer_list'] = $comListApp->getCustomerList();
        require_once(SHOW_PENDING_MONEY_RECEIPT_SKIN);
        return $data[0];
    }

    function getPendingVoucherList($from, $to, $moneyReceiptStatus = 0)
    {
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 500;
        }
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $account_id = getRequest('account_id');
        $post_by = getRequest('created_by');
        //if($date_from==""){ $date_from = date("Y-m-d");}
        //if($date_to==""){ $date_to = date("Y-m-d");}
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = PENDING_CVMASTER_TBL . ' m,' . PENDING_CVDETAILS_TBL . ' d';
        $info['fields'] = array("m.*", "d.*", "m.description as note", "d.edited_by as details_edit_by", "DATE_FORMAT(m.created_date,'%d %b %y' ) as created_date");
        $sql = "m.tmp_grvid = d.tmp_grvid AND m.project_id = '$project_id' AND m.is_money_recipt = '$moneyReceiptStatus' AND m.status = '0'";
        if ($account_id != "") {
            $sql .= " AND d.cr_account = '$account_id'";
        }
        if ($post_by != "") {
            $sql .= " AND d.created_by = '$post_by'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND m.created_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND m.created_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND m.created_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['groupby'] = array("m.tmp_grvid");
        $info['orderby'] = array("m.created_date,d.headtypes ASC LIMIT $from,$to");
        //$info['debug']  = true;

        $result = select($info);

        $data = array();
        $cnt = count($result);
        if ($cnt) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }

        return $data;
    }

    function getTotalPendingVoucherList($moneyReceiptStatus = 0)
    {
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $account_id = getRequest('account_id');
        $post_by = getRequest('created_by');
        //if($date_from==""){ $date_from = date("Y-m-d");}
        //if($date_to==""){ $date_to = date("Y-m-d");}
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = PENDING_CVMASTER_TBL . ' m,' . PENDING_CVDETAILS_TBL . ' d';
        $info['fields'] = array("m.*", "d.*", "DATE_FORMAT(m.created_date,'%d %b %y' ) as created_date");
        $sql = "m.tmp_grvid = d.tmp_grvid AND m.project_id = '$project_id' AND m.is_money_recipt = '$moneyReceiptStatus' AND m.status = '0'";
        if ($account_id != "") {
            $sql .= " AND d.cr_account = '$account_id'";
        }
        if ($post_by != "") {
            $sql .= " AND d.created_by = '$post_by'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND m.created_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND m.created_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND m.created_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['groupby'] = array("m.tmp_grvid");
        $info['orderby'] = array("m.created_date,d.headtypes DESC");
        $result = select($info);
        $data = array();
        $cnt = count($result);
        if ($cnt) {
            return $cnt;
        } else {
            return 0;
        }
    }

    //========Journal class function ========
    function showVoucherPrintEditor($ID)
    {
        if ($ID) {
            $advArr = $this->getDebitVoucharDetails($ID);
            $advArr = parseThisValue($advArr);
            $data = array_merge(array(), $advArr);
            $data['message'] = $msg;
            $data['cmd'] = getRequest('cmd');
            require_once(VOUCHAR_PRINT_SKIN);
        } else {
            require_once(PRINT_VOUCHAR_SKIN);
        }
        return true;
    }

    //======== Print Undelivery Voucher =======
    function showPrintUndeliveryVoucher($msg = null)
    {
        require_once(CLASS_DIR . '/sales_order.class.php');
        $salesApp = new SalesOrder();

        $voucher_no = getRequest('voucher_no');
        if ($voucher_no) {
            $advArr = $salesApp->getSalesMasterInfo($voucher_no);
            $advArr = parseThisValue($advArr);
            $data = array_merge(array(), $advArr);

            $data['item_list'] = $this->getUndeliveryProductList($voucher_no);
            $data['message'] = $msg;
            $data['cmd'] = getRequest('cmd');
            require_once(PRINT_UNDELIVERY_SALES_ORDER_SKIN);
            return true;
        }
    }

    function getUndeliveryProductList($id)
    {
        $info = array();
        $info['table'] = SALES_MASTER_TBL . ' pm,' . SALES_DETAILS_TBL . ' sd,' . PRODUCT_TBL . ' p,' . CURRENCY_TBL . ' c,' . BRAND_TBL . ' b';
        $info['fields'] = array('sd.sal_detail_id', 'sd.voucher_no', 'sd.project_id', 'sd.serial', 'sd.warranty', 'sd.catagory', 'b.brand_name', 'sd.product', 'sd.details', 'p.product_name', 'p.product_desc', 'sd.m_unit', 'sd.unit_price', 'c.curr_symble', 'sd.discount_per_qty', 'sd.qty', 'sd.free_qty', 'sd.delivery_qty', 'sd.undelivery_qty', 'sd.total_bag', 'sd.total', 'sd.created_time');
        $sql = "pm.voucher_no=sd.voucher_no AND sd.product = p.product_id AND p.brand_code = b.brand_id AND sd.currency = c.currency_id AND pm.status=1 AND sd.voucher_no = '$id' AND sd.undelivery_qty > 0";
        $info['where'] = $sql;
        $info['groupby'] = array("sd.sal_detail_id");
        $info['orderby'] = array("sd.sal_detail_id asc");
        //$info['debug']  = true;
        $result = select($info);
        $data = array();
        $cnt = count($result);

        if ($cnt) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }
        return $data;
    }

    function showDeleteUndeliveryProduct($voucher_no)
    {
        if (getFromSession('u_type_id') == 101) {
            if ($voucher_no != "") {
                $DSql = "UPDATE " . SALES_DETAILS_TBL . " SET undelivery_qty=0 WHERE voucher_no='" . $voucher_no . "' AND undelivery_qty >0";
                $res1 = mysql_query($DSql);
                if ($res1) {
                    $msg = "Successfully delete Record !!!";
                    header("location:index.php?app=sales.report&cmd=undelivered_list&msg=$msg");
                } else {
                    $msg = "Faile delete Record. Please try again !!!";
                    header("location:index.php?app=sales.report&cmd=undelivered_list&msg=$msg");
                }
            } else {
                $msg = "Faile delete Record. Please try again !!!";
                header("location:index.php?app=sales.report&cmd=undelivered_list&msg=$msg");
            }
        }
    }

    //======== End Print Undelivery Voucher =======

    function getDebitVoucharDetails($voucher_no)
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = DEVIT_VOUCHAR_TBL;
        $info['where'] = "voucher_no='" . $voucher_no . "' AND project_id = '$project_id'";
        $info['debug'] = false;
        $res = select($info);
        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        return $data[0];
    }

    //====== Contra Voucher function =====
    function showCVPrintEditor($msg = null)
    {
        require_once(CLASS_DIR . '/contra.voucher.new.class.php');
        $cvApp = new ContraVoucher();
        $contra_id = getRequest('contra_id');
        if ($contra_id) {
            $advArr = $cvApp->getContraMasterInfo($contra_id);
            $advArr = parseThisValue($advArr);
            $data = array_merge(array(), $advArr);
            $data['item_list'] = $cvApp->getContraDetails($contra_id);
            $data['message'] = $msg;
            $data['cmd'] = getRequest('cmd');
            require_once(PRNIT_NEW_CONTRA_VOUCHER_SKIN);
            return true;
        }
    }

    function showTransactionEditor($msg = null)
    {
        require_once(CLASS_DIR . '/journal.class.php');
        $jApp = new Journal();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['record_list'] = $jApp->getTransactionList(getRequest('from'), getRequest('to'));
        $data['totalrecord'] = $jApp->getTotalTransactionList(getRequest('from'), getRequest('to'));
        require_once(SHOW_TRANSACTION_LIST_SKIN);
        return $data[0];

    }

    function showDuePaymentEditor($msg = null)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        require_once(CLASS_DIR . '/sales.commission.class.php');
        $slsApp = new SalesCommission();
        require_once(CLASS_DIR . '/salary.sheet.class.php');
        $salaryApp = new SalarySheet();
        require_once(CLASS_DIR . '/sales.return.class.php');
        $slsReturnApp = new SalesReturn();
        require_once(CLASS_DIR . '/journal.class.php');
        $jApp = new Journal();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['record_list'] = $jApp->getDuePaymentList(getRequest('from'), getRequest('to'));
        $data['comission_list'] = $slsApp->getSalesCommissionList(getRequest('from'), getRequest('to'));
        $data['salary_list'] = $salaryApp->getApprovedSalaryList(getRequest('from'), getRequest('to'));
        $data['totalrecord'] = $jApp->getTotalDuePaymentList(getRequest('from'), getRequest('to'));
        $data['sr_record_list'] = $slsReturnApp->getDueSalesReturnPaybleList(getRequest('from'), getRequest('to'));
        $data['customer_list'] = $comListApp->getAccountHeadList("Customer");
        $data['reference_list'] = $comListApp->getAccountHeadList("Reference");
        $data['payable_list'] = $comListApp->getAccountHeadList("Accounts Payable");
        require_once(DUE_PAYMENT_LIST_SKIN);
        return $data[0];
    }

    function showDueReceivableEditor($msg = null)
    {
        require_once(CLASS_DIR . '/journal.class.php');
        $jApp = new Journal();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['record_list'] = $jApp->getDueReceivedList(getRequest('from'), getRequest('to'));
        $data['totalrecord'] = $jApp->getTotalDueReceivedList(getRequest('from'), getRequest('to'));
        require_once(DUE_RECEIVABLE_LIST_SKIN);
        return $data[0];
    }


//MIS Dashboard Report start


    function misDAshboardReport()
    {
        $date_from = getRequest('from');
        $date_to = getRequest('to');
        $date_to = getRequest('report');

        if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering

        $input = json_decode(file_get_contents('php://input'), true);

        $from_date = trim($input['from_date']);
        $to_date = trim($input['to_date']);
        $report_type = trim($input['report']);

        // Validate input
        if (json_last_error() !== JSON_ERROR_NONE || !isset($from_date) || !isset($to_date) || !isset($report_type)) {
            die(json_encode([
                'status' => false,
                'message' => 'Invalid JSON data received!'
            ]));
        }

        if (empty($from_date) || empty($to_date) || empty($report_type)) {
            die(json_encode([
                'status' => false,
                'message' => 'Date and report type must not be empty!'
            ]));
        }


        if ($report_type == "sales_and_collection") {
            $result = $this->getSalesAndCollectionReport($from_date, $to_date);

        } elseif ($report_type == "cash_and_bank") {
            $result = $this->getCashAndBankReport($from_date, $to_date);
        } elseif ($report_type == "receivable_and_payable") {
            $result = $this->getReceivableAndPayableReport($from_date, $to_date);
        } elseif ($report_type == "capital_and_fixedAssets") {
            $result = $this->getCapitalAndFixedAssetsReport($from_date, $to_date);
        } elseif ($report_type == "loan") {
            $result = $this->getLoanReport($from_date, $to_date);
        } elseif ($report_type == "profit_and_loss") {
            $result = $this->getProfitAndLossReport($from_date, $to_date);
        } else {
            $result = null;
        }


        if ($result) {
            $response = [
                'status' => true,
                'message' => 'Success',
                'data' => $result
            ];
        } else {
            $response = [
                'status' => false,
                'message' => 'Data not found',
                'data' => null
            ];
        }

        // Output response
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();

    }


    function getSalesAndCollectionReport($from_date, $to_date)
    {
        $project_id = getFromSession('project_id');
        $params = [
            "from_date" => $from_date,
            "to_date" => $to_date,
            "project_id" => $project_id,
            "sales_type" => "",
            "odd_date" => "",
            "minus_odd_date" => "",
            "issue_date" => "",
        ];

        $result = $this->getAllLocationData($params);
        return $result;
    }


    function getAllLocationData($params)
    {
        $allDivision = $this->getAllDivision();
        $division_ids = array_column($allDivision, 'division_id');

        $division_data = [];

        foreach ($allDivision as $item) {
            $div_id = $item["division_id"];
            $division_data[$div_id] = [
                "division_id" => $div_id,
                "division_name_bng" => $item["division_name_bng"],
                "division_name_eng" => $item["division_name_eng"],
            ];
        }

        $divsionTargetAmount = $this->getTotalTargetAmountByIds($params, 'division_id', $division_ids);
        $divsionOrderAmount = $this->getTotalOrderAmountByIds($params, 'division', $division_ids);
        $divsionSalesAmount = $this->getTotalSalesAmountByIds($params, 'division_id', $division_ids);
        $divsionReceiptAmount = $this->getTotalReceiptAmountByIds($params, 'division_id', $division_ids);


        $totalDivsionSalesAmount = 0;
        $totalDivsionReceiptAmount = 0;
        //$totalDivsionTargetAmount = 0;
        //$totalDivsionOrderAmount = 0;
        foreach ($allDivision as $item) {
            $div_id = $item["division_id"];
            $totalDivsionSalesAmount += $divsionSalesAmount[$div_id];
            $totalDivsionReceiptAmount += $divsionReceiptAmount[$div_id];
            //$totalDivsionTargetAmount += $divsionTargetAmount[$div_id];
            //$totalDivsionOrderAmount += $divsionOrderAmount[$div_id];
        }

        $totalDivsionSalesAmount = number_format(abs($totalDivsionSalesAmount), 2, '.', ',') . (($totalDivsionSalesAmount < 0) ? " Cr" : " Dr");
        $totalDivsionReceiptAmount = number_format(abs($totalDivsionReceiptAmount), 2, '.', ',') . (($totalDivsionReceiptAmount < 0) ? " Cr" : " Dr");

        return [
            "divsions" => $division_data,
            "divsionTargetAmount" => $divsionTargetAmount,
            "divsionOrderAmount" => $divsionOrderAmount,
            "divsionSalesAmount" => $divsionSalesAmount,
            "divsionReceiptAmount" => $divsionReceiptAmount,
            "totalDivsionSalesAmount" => $totalDivsionSalesAmount,
            "totalDivsionReceiptAmount" => $totalDivsionReceiptAmount,
        ];

    }


    function getAllDivision()
    {
        $project_id = getFromSession('project_id');

        // Start building SQL
        $sql = "SELECT * FROM division WHERE project_id = '$project_id' ORDER BY division_id ASC";
        // Execute query
        $gres = mysql_query($sql);

        // Fetch results
        $data = array();
        while ($row = mysql_fetch_assoc($gres)) {
            $data[] = $row;
        }

        return $data;
    }


    function getTotalSalesAmountByIds($params, $column, $ids)
    {
        $sales_type = $params['sales_type'];
        $odd_date = $params['odd_date'];
        $minus_odd_date = $params['minus_odd_date'];
        $from_date = $params['from_date'];
        $to_date = $params['to_date'];
        $project_id = $params['project_id'];

        $sales_date = ($odd_date != "" || $minus_odd_date != "") ? "sales_date" : "delivery_date";

        $ids = array_map('intval', (array)$ids);
        if (empty($ids)) return array();

        $id_list = implode(',', $ids);

        $getSql = "SELECT `$column`, SUM(sales_amount) AS sales_amount 
               FROM vw_customer_delivery_ledger 
               WHERE project_id = '" . mysql_real_escape_string($project_id) . "'
               AND `$column` IN ($id_list)";

        if ($sales_type != "") {
            $getSql .= " AND sales_type = '" . mysql_real_escape_string($sales_type) . "'";
        }

        if ($from_date != "" && $to_date == "") {
            $getSql .= " AND $sales_date >= '" . mysql_real_escape_string($from_date) . "'";
        } elseif ($from_date == "" && $to_date != "") {
            $getSql .= " AND $sales_date <= '" . mysql_real_escape_string($to_date) . "'";
        } elseif ($from_date != "" && $to_date != "") {
            $getSql .= " AND $sales_date >= '" . mysql_real_escape_string($from_date) . "'";
            $getSql .= " AND $sales_date <= '" . mysql_real_escape_string($to_date) . "'";
        }

        if ($minus_odd_date != "") {
            // assuming you meant column comparison, not a string
            $getSql .= " AND sales_date = value_date";
        }

        $getSql .= " GROUP BY `$column`";

        // Debugging — show SQL errors if any
        $gres = mysql_query($getSql);
        if (!$gres) {
            die("MySQL Error: " . mysql_error() . "<br>SQL: " . $getSql);
        }

        $sales_data = array();
        while ($row = mysql_fetch_assoc($gres)) {
            $sales_data[$row[$column]] = (float)$row['sales_amount'];
        }

        return $sales_data;
    }

    function ttgetTotalSalesAmountByIds($params, $column, $ids)
    {
        $sales_type = $params['sales_type'];
        $odd_date = $params['odd_date'];
        $minus_odd_date = $params['minus_odd_date'];
        $from_date = $params['from_date'];
        $to_date = $params['to_date'];
        $project_id = $params['project_id'];

        $sales_date = ($odd_date != "" || $minus_odd_date != "") ? "sales_date" : "delivery_date";

        // Sanitize and validate IDs
        $ids = array_map('intval', (array)$ids);
        if (empty($ids)) return array();

        // Build base SQL
        $getSql = "SELECT $column, SUM(sales_amount) AS sales_amount 
               FROM vw_customer_delivery_ledger 
               WHERE project_id = '" . mysql_real_escape_string($project_id) . "'";

        // Filter by IDs
        $id_list = implode(',', $ids);
        $getSql .= " AND $column IN ($id_list)";

        // Filter by sales type
        if ($sales_type != "") {
            $getSql .= " AND sales_type = '" . mysql_real_escape_string($sales_type) . "'";
        }

        // Date filters
        if ($from_date != "" && $to_date == "") {
            $getSql .= " AND $sales_date >= '" . mysql_real_escape_string($from_date) . "'";
        } elseif ($from_date == "" && $to_date != "") {
            $getSql .= " AND $sales_date <= '" . mysql_real_escape_string($to_date) . "'";
        } elseif ($from_date != "" && $to_date != "") {
            $getSql .= " AND $sales_date >= '" . mysql_real_escape_string($from_date) . "'";
            $getSql .= " AND $sales_date <= '" . mysql_real_escape_string($to_date) . "'";
        }

        if ($minus_odd_date != "") {
            $getSql .= " AND sales_date = value_date";
        }

        $getSql .= " GROUP BY $column";

        // Execute and fetch
        $gres = mysql_query($getSql);
        $sales_data = array();

        while ($row = mysql_fetch_assoc($gres)) {
            $sales_data[$row[$column]] = (float)$row['sales_amount'];
        }

        return $sales_data;
    }


    function getTotalReceiptAmountByIds($params, $column, $ids)
    {
        $sales_type = $params['sales_type'];
        $from_date = $params['from_date'];
        $to_date = $params['to_date'];
        $issue_date = $params['issue_date'];
        $project_id = $params['project_id'];

        $created_date = ($issue_date != "") ? "issue_date" : "created_date";

        // Sanitize IDs
        $ids = array_map('intval', (array)$ids);
        if (empty($ids)) return array();

        // --- 1️⃣ RECEIPT AMOUNT ---
        $getSql = "SELECT $column, SUM(cr) AS receipt_amount 
               FROM vw_customer_ledger 
               WHERE project_id = '" . mysql_real_escape_string($project_id) . "'
               AND cr > 0
               AND description != 'OB'
               AND adjustment = 0
               AND beddebts = 0";

        $id_list = implode(',', $ids);
        $getSql .= " AND $column IN ($id_list)";

        if ($sales_type != "") {
            $getSql .= " AND sales_type = '" . mysql_real_escape_string($sales_type) . "'";
        }

        if ($from_date != "" && $to_date == "") {
            $getSql .= " AND $created_date >= '" . mysql_real_escape_string($from_date) . "'";
        } elseif ($from_date == "" && $to_date != "") {
            $getSql .= " AND $created_date <= '" . mysql_real_escape_string($to_date) . "'";
        } elseif ($from_date != "" && $to_date != "") {
            $getSql .= " AND $created_date >= '" . mysql_real_escape_string($from_date) . "'";
            $getSql .= " AND $created_date <= '" . mysql_real_escape_string($to_date) . "'";
        }

        $getSql .= " GROUP BY $column";

        // Execute query
        $gres = mysql_query($getSql);
        $receipt_data = array();
        while ($row = mysql_fetch_assoc($gres)) {
            $receipt_data[$row[$column]] = (float)$row['receipt_amount'];
        }

        // --- 2️⃣ RETURN AMOUNT ---
        $getSql2 = "SELECT $column, SUM(return_amount) AS return_amount 
                FROM vw_customer_sales_return_details 
                WHERE project_id = '" . mysql_real_escape_string($project_id) . "'";

        $getSql2 .= " AND $column IN ($id_list)";

        if ($sales_type != "") {
            $getSql2 .= " AND sales_type = '" . mysql_real_escape_string($sales_type) . "'";
        }

        if ($from_date != "" && $to_date == "") {
            $getSql2 .= " AND return_date >= '" . mysql_real_escape_string($from_date) . "'";
        } elseif ($from_date == "" && $to_date != "") {
            $getSql2 .= " AND return_date <= '" . mysql_real_escape_string($to_date) . "'";
        } elseif ($from_date != "" && $to_date != "") {
            $getSql2 .= " AND return_date >= '" . mysql_real_escape_string($from_date) . "'";
            $getSql2 .= " AND return_date <= '" . mysql_real_escape_string($to_date) . "'";
        }

        $getSql2 .= " GROUP BY $column";

        // Execute query
        $gres2 = mysql_query($getSql2);
        $return_data = array();
        while ($row = mysql_fetch_assoc($gres2)) {
            $return_data[$row[$column]] = (float)$row['return_amount'];
        }

        // --- 3️⃣ Combine (Receipt - Return) ---
        $final_data = array();
        foreach ($ids as $id) {
            $receipt = isset($receipt_data[$id]) ? $receipt_data[$id] : 0;
            $return = isset($return_data[$id]) ? $return_data[$id] : 0;
            $total = max(0, $receipt - $return);
            $final_data[$id] = $total;
        }

        return $final_data;
    }


    function getTotalTargetAmountByIds($params, $column, $ids)
    {
        $from_date = $params['from_date'];
        $to_date = $params['to_date'];
        $project_id = mysql_real_escape_string($params['project_id']);

        // Sanitize IDs
        $ids = array_map('intval', (array)$ids);
        if (empty($ids)) return array();

        $id_list = implode(',', $ids);

        // --- Build SQL ---
        $sql = "SELECT $column, SUM(target_amount) AS targetAmount
            FROM area_sales_target
            WHERE project_id = '$project_id'
            AND $column IN ($id_list)";

        // Date filtering
        if ($from_date != "" && $to_date == "") {
            $sql .= " AND target_from >= '" . mysql_real_escape_string($from_date) . "'";
        } elseif ($from_date == "" && $to_date != "") {
            $sql .= " AND target_to <= '" . mysql_real_escape_string($to_date) . "'";
        } elseif ($from_date != "" && $to_date != "") {
            $sql .= " AND target_from >= '" . mysql_real_escape_string($from_date) . "'";
            $sql .= " AND target_to <= '" . mysql_real_escape_string($to_date) . "'";
        }

        $sql .= " GROUP BY $column";

        // Execute query
        $query = mysql_query($sql);
        $sales_data = array();

        while ($row = mysql_fetch_assoc($query)) {
            $sales_data[$row[$column]] = (float)$row['targetAmount'];
        }

        return $sales_data;
    }


    function getTotalOrderAmountByIds($params, $column, $ids)
    {
        $from_date = $params['from_date'];
        $to_date = $params['to_date'];
        $project_id = mysql_real_escape_string($params['project_id']);

        // Sanitize IDs
        $ids = array_map('intval', (array)$ids);
        if (empty($ids)) return array();

        $id_list = implode(',', $ids);

        // -------------------------------------
        // Optimized single SQL (best approach)
        // -------------------------------------
        /*$sql = "SELECT
                $column,
                (total_value
                    - (
                        general_discount_amount
                        + exclusive_discount_amount
                        + additional_discount
                        + discount
                      )
                ) AS net_total
            FROM sales_master
            WHERE project_id = '$project_id'
            AND $column IN ($id_list)";*/

        /*$sql = "SELECT $column, SUM(net_payble) AS net_total
            FROM sales_master
            WHERE project_id = '$project_id'
            AND $column IN ($id_list)";*/

        $sql = "SELECT $column, SUM(total_value - discount) AS net_total FROM sales_master
            WHERE project_id = '$project_id'
            AND $column IN ($id_list)";

        //$sql .= " AND item_delivery_amount >= '0'";
        //$sql .= " AND status >= '1'";
        //$sql .= " AND is_deleted >= '0'";

        // Date filtering
        if ($from_date != "" && $to_date == "") {
            $sql .= " AND sales_date >= '" . mysql_real_escape_string($from_date) . "'";
        } elseif ($from_date == "" && $to_date != "") {
            $sql .= " AND sales_date <= '" . mysql_real_escape_string($to_date) . "'";
        } elseif ($from_date != "" && $to_date != "") {
            $sql .= " AND sales_date BETWEEN '" . mysql_real_escape_string($from_date) . "'
                                          AND '" . mysql_real_escape_string($to_date) . "'";
        }

        $sql .= " GROUP BY $column";

        // Execute Query
        $query = mysql_query($sql);
        $sales_data = array();

        while ($row = mysql_fetch_assoc($query)) {
            $amount = (float)$row['net_total'];
            if ($amount < 0) $amount = 0; // avoid negative
            $sales_data[$row[$column]] = $amount;
        }

        return $sales_data;
    }


    function getSLClosingBalance($project_id, $head_type, $subhead_type = NULL, $childheadtype = NULL, $from_date, $to_date)
    {
        $totalAmount = 0;
        $bsql = "SELECT (SUM(a.dr)- SUM(a.cr)) AS balance FROM " . ACCOUNT_JOURNAL_TBL . " AS a," . SUB_ACC_HEAD_TBL . " AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
        if ($subhead_type != "") {
            $bsql .= " AND s.sub_headtype='$subhead_type'";
        }
        if ($childheadtype != "") {
            $bsql .= " AND s.child_head='$childheadtype'";
        }
        if ($from_date != "" && $to_date != "") {
            $bsql .= " AND a.created_date <= '$to_date'";
        } else {
            $to_date = date('Y-m-d');
            $bsql .= " AND a.created_date <= '$to_date'";
        } //echo $bsql;
        $bres = mysql_query($bsql);
        if (mysql_num_rows($bres) > 0) {
            $brow = mysql_fetch_object($bres);
            if ($brow->balance != "") {
                $totalAmount = $brow->balance;
            } else {
                $totalAmount = 0;
            }
        }
        return $totalAmount;
    }

    function getGLClosingBalance($project_id, $head_type, $subhead_type = NULL, $childheadtype = NULL, $from_date, $to_date)
    {
        $totalAmount = 0;
        $bsql = "SELECT (SUM(a.dr)- SUM(a.cr)) AS balance FROM " . ACCOUNT_JOURNAL_TBL . " AS a," . SUB_ACC_HEAD_TBL . " AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
        if ($subhead_type != "") {
            $bsql .= " AND s.sub_headtype='$subhead_type'";
        }
        if ($childheadtype != "") {
            $bsql .= " AND s.child_head='$childheadtype'";
        }
        if ($from_date != "" && $to_date != "") {
            $bsql .= " AND a.created_date <= '$to_date'";
        } else {
            $to_date = date('Y-m-d');
            $bsql .= " AND a.created_date <= '$to_date'";
        } //echo $bsql;
        $bres = mysql_query($bsql);
        if (mysql_num_rows($bres) > 0) {
            $brow = mysql_fetch_object($bres);
            if ($brow->balance != "") {
                $totalAmount = $brow->balance;
            } else {
                $totalAmount = 0;
            }
        }
        return $totalAmount;
    }

    function getISTotalSalesAmount($project_id, $head_type, $head_id = NULL, $from_date, $to_date)
    {
        $sql = "SELECT (SUM(a.dr)- SUM(a.cr)) AS TotalSales FROM " . ACCOUNT_JOURNAL_TBL . " AS a," . SUB_ACC_HEAD_TBL . " AS s WHERE BINARY 
	a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
        if ($head_id != "") {
            $sql .= " AND $head_id";
        }
        if ($from_date != "" && $to_date != "") {
            $sql .= " AND a.created_date BETWEEN '$from_date' AND '$to_date'";
        }

        $crow = mysql_fetch_object(mysql_query($sql));
        if ($crow->TotalSales != "") {
            $TotalSales = abs($crow->TotalSales);
        } else {
            $TotalSales = 0;
        }
        return $TotalSales;
    }

    function getCashAndBankReport($from_date, $to_date)
    {
        $project_id = getFromSession('project_id');
        $head_type = "Current Assets";
        $subhead_type = "S130";

        $cashInHand = $this->getSLClosingBalance($project_id, $head_type, $subhead_type, "C000064", $from_date, $to_date);
        $cashAtBank = $this->getSLClosingBalance($project_id, $head_type, $subhead_type, "C000103", $from_date, $to_date);
        $total = $cashInHand + $cashAtBank;

        $cash_in_hand = '<a href="?app=show_ledger&cmd=show_sl3_summary&head_type=Current%20Assets&subhead_type=S130&child_id=C000064&date_from=' . formatDateDMY($from_date) . '&date_to=' . formatDateDMY($to_date) . '" target="_blank">' . number_format(abs($cashInHand), 2, '.', ',') . (($cashInHand < 0) ? " Cr" : " Dr") . '</a>';

        $cash_at_bank = '<a href="?app=show_ledger&cmd=show_sl3_summary&head_type=Current%20Assets&subhead_type=S130&child_id=C000103&date_from=' . formatDateDMY($from_date) . '&date_to=' . formatDateDMY($to_date) . '" target="_blank">' . number_format(abs($cashAtBank), 2, '.', ',') . (($cashAtBank < 0) ? " Cr" : " Dr") . '</a>';

        return [
            "cash_in_hand" => $cash_in_hand,
            "cash_at_bank" => $cash_at_bank,
            "total" => number_format(abs($total), 2, '.', ',') . (($total < 0) ? " Cr" : " Dr")
        ];

    }


    function getReceivableAndPayableReport($from_date, $to_date)
    {
        $project_id = getFromSession('project_id');

        $receivable = $this->getSLClosingBalance($project_id, "Current Assets", "S128", null, $from_date, $to_date);
        $payable = $this->getSLClosingBalance($project_id, "Current Liabilities", "S137", null, $from_date, $to_date);
        $total = $receivable + $payable;

        $receivable = number_format(abs($receivable), 2, '.', ',') . (($receivable < 0) ? " Cr" : " Dr");
        $payable = number_format(abs($payable), 2, '.', ',') . (($payable < 0) ? " Cr" : " Dr");

        return [
            "receivable" => $receivable,
            "payable" => $payable,
            "total" => number_format(abs($total), 2, '.', ',') . (($total < 0) ? " Cr" : " Dr")
        ];

    }

    function getCapitalAndFixedAssetsReport($from_date, $to_date)
    {
        $project_id = getFromSession('project_id');

        $capital = $this->getSLClosingBalance($project_id, "Capital", null, null, $from_date, $to_date);
        $fixedAsset = $this->getSLClosingBalance($project_id, "Non Current Assets", null, null, $from_date, $to_date);

        $capital = number_format(abs($capital), 2, '.', ',') . (($capital < 0) ? " Cr" : " Dr");
        $fixedAsset = number_format(abs($fixedAsset), 2, '.', ',') . (($fixedAsset < 0) ? " Cr" : " Dr");

        return [
            "capital" => $capital,
            "fixedAsset" => $fixedAsset,
        ];

    }

    function getLoanReport($from_date, $to_date)
    {
        $project_id = getFromSession('project_id');

        $liability = $this->getGLClosingBalance($project_id, "Current Liabilities", null, null, $from_date, $to_date);
        $advances = $this->getSLClosingBalance($project_id, "Current Assets", "S112", null, $from_date, $to_date);

        $liability = number_format(abs($liability), 2, '.', ',') . (($liability < 0) ? " Cr" : " Dr");
        $advances = number_format(abs($advances), 2, '.', ',') . (($advances < 0) ? " Cr" : " Dr");

        return [
            "liability" => $liability,
            "advances" => $advances,
        ];

    }


    function getGroupType($from_date, $to_date)
    {
        $from = new DateTime($from_date);
        $to = new DateTime($to_date);
        $diff = $from->diff($to)->days;

        if ($diff <= 1) {
            return 'hour';
        } elseif ($diff <= 7) {
            return 'day';
        } elseif ($diff <= 31) {
            return 'day';
        } elseif ($diff <= 365) {
            return 'month';
        } else {
            return 'year';
        }
    }


    function generateDateRanges($from_date, $to_date)
    {
        $from = new DateTime($from_date);
        $to = new DateTime($to_date);
        $groupType = $this->getGroupType($from_date, $to_date);

        $ranges = [];
        $labels = [];

        switch ($groupType) {
            case 'hour':
                while ($from <= $to) {
                    $start = clone $from;
                    $end = clone $from;
                    $end->modify('+1 hour');
                    $ranges[] = [
                        'start' => $start->format('Y-m-d H:00:00'),
                        'end' => $end->format('Y-m-d H:59:59')
                    ];
                    $labels[] = $start->format('H:i');
                    $from->modify('+1 hour');
                }
                break;

            case 'day':
                while ($from <= $to) {
                    $start = clone $from;
                    $end = clone $from;
                    $end->modify('+1 day');
                    $ranges[] = [
                        'start' => $start->format('Y-m-d 00:00:00'),
                        'end' => $end->format('Y-m-d 23:59:59')
                    ];
                    $labels[] = $start->format('d M');
                    $from->modify('+1 day');
                }
                break;

            case 'month':
                while ($from <= $to) {
                    $start = new DateTime($from->format('Y-m-01 00:00:00'));
                    $end = clone $start;
                    $end->modify('last day of this month 23:59:59');
                    $ranges[] = [
                        'start' => $start->format('Y-m-d H:i:s'),
                        'end' => $end->format('Y-m-d H:i:s')
                    ];
                    $labels[] = $start->format('M');
                    $from->modify('first day of next month');
                }
                break;

            case 'year':
            default:
                while ($from <= $to) {
                    $start = new DateTime($from->format('Y-01-01 00:00:00'));
                    $end = new DateTime($from->format('Y-12-31 23:59:59'));
                    $ranges[] = [
                        'start' => $start->format('Y-m-d H:i:s'),
                        'end' => $end->format('Y-m-d H:i:s')
                    ];
                    $labels[] = $start->format('Y');
                    $from->modify('first day of January next year');
                }
                break;
        }

        return [
            'groupType' => $groupType,
            'ranges' => $ranges,
            'labels' => $labels
        ];
    }

    function generateMonthlyChartData($from_date, $to_date)
    {
        $from = new DateTime($from_date);
        $to = new DateTime($to_date);

        // Calculate difference
        $diff = $from->diff($to);

        // Determine mode: daily / weekly / monthly / yearly
        if ($diff->y == 0 && $diff->m == 0 && $diff->days <= 31) {
            //$mode = 'weekly';
            $mode = 'daily';
        } elseif ($diff->y < 1) {
            $mode = 'monthly';
        } else {
            $mode = 'yearly';
        }

        $labels = [];
        $values = [];

        /* ------------------------------------------
     *  DAILY MODE
     * ------------------------------------------ */
        if ($mode == 'daily') {

            $period = new DatePeriod($from, new DateInterval('P1D'), $to->modify('+1 day'));

            foreach ($period as $day) {

                $start = $day->format('Y-m-d');
                $end = $day->format('Y-m-d');

                $labels[] = $day->format("d M");

                $result = $this->getGrossProfit($start, $end);
                $values[] = isset($result["net_profit"]) ? $result["net_profit"] : 0;
            }
        } /* ------------------------------------------
     *  WEEKLY MODE
     * ------------------------------------------ */
        elseif ($mode == 'weekly') {

            // Each week: 7 days
            $period = new DatePeriod($from, new DateInterval('P7D'), $to);

            foreach ($period as $weekStart) {

                $weekEnd = clone $weekStart;
                $weekEnd->modify('+6 days');
                if ($weekEnd > $to) $weekEnd = clone $to;

                $labels[] = "Week " . $weekStart->format("W");

                $result = $this->getGrossProfit(
                    $weekStart->format('Y-m-d'),
                    $weekEnd->format('Y-m-d')
                );

                $values[] = isset($result["net_profit"]) ? $result["net_profit"] : 0;
            }

        } /* ------------------------------------------
     *  MONTHLY MODE
     * ------------------------------------------ */
        elseif ($mode == 'monthly') {

            $start = new DateTime($from->format('Y-m-01'));
            $end = new DateTime($to->format('Y-m-t'));

            $period = new DatePeriod($start, new DateInterval('P1M'), $end);

            foreach ($period as $monthStart) {

                $monthEnd = clone $monthStart;
                $monthEnd->modify('last day of this month');

                $labels[] = $monthStart->format("M Y");

                $result = $this->getGrossProfit(
                    $monthStart->format('Y-m-d'),
                    $monthEnd->format('Y-m-d')
                );

                $values[] = isset($result["net_profit"]) ? $result["net_profit"] : 0;
            }

        } /* ------------------------------------------
     *  YEARLY MODE
     * ------------------------------------------ */
        else {

            $start = new DateTime($from->format('Y-01-01'));
            $end = new DateTime($to->format('Y-12-31'));

            $period = new DatePeriod($start, new DateInterval('P1Y'), $end);

            foreach ($period as $yearStart) {

                $yearEnd = new DateTime($yearStart->format("Y-12-31"));

                $labels[] = $yearStart->format("Y");

                $result = $this->getGrossProfit(
                    $yearStart->format('Y-m-d'),
                    $yearEnd->format('Y-m-d')
                );

                $values[] = isset($result["net_profit"]) ? $result["net_profit"] : 0;
            }
        }

        return [
            "mode" => $mode,
            "labels" => $labels,
            "values" => $values
        ];
    }


    function generateMonthlyChartDataOld($from_date, $to_date)
    {
        // Make sure $from_date and $to_date are valid DateTime objects
        $from = new DateTime($from_date);
        $to = new DateTime($to_date);

        // Prepare base structure (12 months)
        $labels = [];
        $data = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthName = date('M', mktime(0, 0, 0, $month, 1));

            // Month start and end
            $startOfMonth = new DateTime(date("Y-$month-01 00:00:00"));
            $endOfMonth = clone $startOfMonth;
            $endOfMonth->modify('last day of this month 23:59:59');

            $labels[] = $monthName;

            // Check if the month is inside the selected range
            if ($startOfMonth >= $from && $endOfMonth <= $to) {
                // Set request dates for your existing function
                $start_date = $startOfMonth->format('Y-m-d');
                $end_date = $endOfMonth->format('Y-m-d');


                // Get your actual data
                $result = $this->getGrossProfit($start_date, $end_date);
                $value = $result["net_profit"];
            } else {
                // Month outside range → set 0
                $value = 0;
            }

            $data[] = $value;
        }

        return [
            'labels' => $labels,
            'values' => $data,
        ];
    }


    function generateDynamicDateRangeChart()
    {
        $data = [];
        $dateGroups = $this->generateDateRanges($from_date, $to_date);

        foreach ($dateGroups['ranges'] as $index => $range) {
            $start_date = $range['start'];
            $end_date = $range['end'];

            $data['labels'][] = $dateGroups['labels'][$index];
            $data['values'][] = $this->getGrossProfit($start_date, $end_date);
        }
        return $data;
    }


    function getProfitAndLossReport($from_date, $to_date)
    {
        $grosss_profit = $this->getGrossProfit($from_date, $to_date);

        return [
            "grosss_profit" => $grosss_profit,
            "chartData" => $this->generateMonthlyChartData($from_date, $to_date),
        ];

    }


    function getGrossProfit($from_date, $to_date)
    {
        $project_id = getFromSession('project_id');

        $slhead = "s.sub_headtype='S124' AND s.child_head='C000127'";
        $TotalSalesAmount = $this->getISTotalSalesAmount($project_id, "Operating Revenue", $slhead, $from_date, $to_date);

        $slhead = "s.sub_headtype='S124' AND s.child_head='C000128' AND s.sl_three_head='S300098'";
        $TotalSalesDiscount = $this->getISTotalSalesAmount($project_id, "Operating Revenue", $slhead, $from_date, $to_date);

        $TotalSalesAmount = $TotalSalesAmount + $TotalSalesDiscount;
        $TotalSalesDiscount = $TotalSalesDiscount;

        $slrhead = "s.sub_headtype='S124' AND s.child_head='C000129' AND s.sl_three_head='S300099'";
        $TotalSalesReturn = $this->getISTotalSalesAmount($project_id, "Operating Revenue", $slrhead, $from_date, $to_date);
        $vhead = "s.sub_headtype='S135' AND s.child_head='C000126' "; // AND s.sl_three_head='S300071' AND s.sub_id='A002103'
        $TotalVATAmount = $this->getISTotalSalesAmount($project_id, "Direct Expenses", $vhead, $from_date, $to_date); // VAT Lira
        //$ihead="s.sub_headtype='S121' AND s.child_head='C000131' AND s.sl_three_head='S300064' AND s.sub_id='A000027'";
        $ihead = "s.sub_headtype='S121'";
        $OthersIncome = $this->getISTotalSalesAmount($project_id, "Non-Operating Revenue", $ihead, $from_date, $to_date); // Others Income Lira
        $NetSales = ($TotalSalesAmount - ($TotalVATAmount + $TotalSalesReturn + $TotalSalesDiscount));
        $REVENUE = $NetSales;
        $vatAmount = 0;
        $cosgAmount = $this->getCogsStatementAmount($from_date, $to_date);


        $advhead = "s.`sub_headtype` = 'S139' AND s.child_head='C000120'";
        $TotalADEX = $this->getISHeadsBalance($project_id, "Indirect Expenses", $advhead, $from_date, $to_date); //Adv Exp
        $sndvhead = "s.`sub_headtype` = 'S139' AND s.child_head='C000122'";
        $TotalSDEX = $this->getISHeadsBalance($project_id, "Indirect Expenses", $sndvhead, $from_date, $to_date); // Sales & Delivery
        $finhead = "s.`sub_headtype` = 'S139' AND s.child_head='C000121'";
        $TotalFIEX = $this->getISHeadsBalance($project_id, "Indirect Expenses", $finhead, $from_date, $to_date); // Fin Exp


        $profit = true;
        $totalGrossProfit = ($REVENUE - $vatAmount - $cosgAmount);
        if ($totalGrossProfit >= 0) {
            $ProfitLossTitle = "Profit";
            $profit = true;
        } else {
            $ProfitLossTitle = "Loss";
            $profit = false;
        }

        $ProfitLossTitle = "Gross $ProfitLossTitle";
        $grosss_profit = number_format($totalGrossProfit, 2, '.', ',');


        $TotalExpense = ($TotalADEX + $TotalSDEX + $TotalFIEX);
        $OperatingProfit = ($totalGrossProfit - $TotalExpense);
        $totalNetProfit = ($OperatingProfit + $OthersIncome);

        if ($totalNetProfit >= 0) {
            $netProfitTitle = "Net Profit ";
        } else {
            $netProfitTitle = "Net Loss ";
        }
        $net_profit = number_format($totalNetProfit, 2, '.', ',');

        return [
            "profit" => $profit,
            "profitLossTitle" => $ProfitLossTitle,
            "grosss_profit" => $grosss_profit,
            "net_profit" => $net_profit,
            "net_profit_title" => $netProfitTitle,
        ];
    }


    function getISHeadsBalance($project_id, $head_type, $head_id = NULL, $from_date, $to_date)
    {
        $totalAmount = 0;

        // Base SQL
        $bsql = "SELECT (SUM(a.dr) - SUM(a.cr)) AS balance 
             FROM " . ACCOUNT_JOURNAL_TBL . " AS a, " . SUB_ACC_HEAD_TBL . " AS s 
             WHERE BINARY a.sub_id = s.sub_id
             AND s.project_id = '$project_id'
             AND s.head_type = '$head_type'";

        // Extra head filter (same logic as your original)
        if (!empty($head_id)) {
            $bsql .= " AND $head_id";
        }

        // Date filter if both given
        if (!empty($from_date) && !empty($to_date)) {
            $bsql .= " AND a.created_date BETWEEN '$from_date' AND '$to_date'";
        }

        // Execute query
        $bres = mysql_query($bsql);

        if ($bres && mysql_num_rows($bres) > 0) {
            $brow = mysql_fetch_object($bres);
            $totalAmount = ($brow->balance != "") ? $brow->balance : 0;
        }

        return $totalAmount;
    }


    function getCogsStatementAmount($from_date, $to_date)
    {
        $project_id = getFromSession('project_id');

        $rmhead = "s.sub_headtype = 'S127' AND s.child_head='C000055' AND s.sl_three_head='S300030'";
        $TotalRMOB = $this->getISProductOpeingValue($project_id, "Current Assets", $rmhead, $from_date, $to_date); // A000017 is PO RM
        $TotalRMPB = $this->getISProductPurchaseValue($project_id, "Current Assets", $rmhead, $from_date, $to_date);
        $TotalRMCB = $this->getISProductClosingValue($project_id, "Current Assets", $rmhead, $from_date, $to_date);

        $prhead = "s.sub_headtype = 'S127' AND s.child_head='C000135' AND s.sl_three_head='S300076'";
        $PurchaseReturn = $this->getISProductClosingValue($project_id, "Current Assets", $prhead, $from_date, $to_date);

        $TotalFOVC = $this->getSLDrBalance($project_id, "Direct Expenses", "S138", "C000118", $from_date, $to_date);

        $pakmhead = "s.sub_headtype = 'S139' AND s.child_head='C000155'";
        $TotalPackMat = $this->getCOGSISHeadsBalance($project_id, "Indirect Expenses", $pakmhead, $from_date, $to_date);

        $wphead = "s.sub_headtype = 'S127' AND s.child_head='C000057' AND s.sl_three_head='S300031'";
        $TotalWPOB = $this->getISProductOpeingValue($project_id, "Current Assets", $wphead, $from_date, $to_date); // A000018 is WIP
        $TotalWPCB = $this->getISProductClosingValue($project_id, "Current Assets", $wphead, $from_date, $to_date);

        $fghead = "s.sub_headtype = 'S127' AND s.child_head='C000056' AND s.sl_three_head='S300029'";
        $TotalFGOB = $this->getISProductOpeingValue($project_id, "Current Assets", $fghead, $from_date, $to_date); // A000036 is FG
        $TotalFGPB = 0;
        $TotalFGCB = $this->getISProductClosingValue($project_id, "Current Assets", $fghead, $from_date, $to_date);

        $fgstockid = "A000036";
        $TotalCOGS = $this->getSalesOfCostAmount($fgstockid, $project_id, $from_date, $to_date);

        $CostOfGS = $TotalRMOB;
        $CostOfGS += $TotalRMPB;
        $CostOfGS = ($CostOfGS - abs($PurchaseReturn));
        $CostOfGS = ($CostOfGS - $TotalRMCB);
        $CostOfGS = ($CostOfGS + $TotalFOVC);
        $CostOfGS = ($CostOfGS + $TotalWPOB);
        $CostOfGS = ($CostOfGS - $TotalWPCB);
        $CostOfGS = ($CostOfGS + $TotalFGOB);
        $CostOfGS = ($CostOfGS + $TotalFGPB);
        $AvailableSales = $CostOfGS;
        $CostOfGS = ($CostOfGS - $TotalCOGS);

        if ($CostOfGS != "") {
            $CostOfGS = ($AvailableSales - $TotalFGCB);
            $returnAmount = (float)$CostOfGS;
        } else {
            $returnAmount = 0.00;
        }

        return $returnAmount;
    }


    function getSalesOfCostAmount($acc_head, $project_id, $from_date, $to_date)
    {
        $sql = "SELECT sum(`cr`) as credit_amount FROM " . ACCOUNT_JOURNAL_TBL . " WHERE sub_id = '$acc_head' AND project_id = '$project_id'";
        if ($from_date != "" && $to_date != "") {
            $sql .= " AND created_date BETWEEN '$from_date' AND '$to_date'";
        }
        $row = mysql_fetch_object(mysql_query($sql));
        $credit_amount = $row->credit_amount;
        if (empty($credit_amount)) {
            $credit_amount = 0;
        }
        return $credit_amount;
    }


    function getCOGSISHeadsBalance($project_id, $head_type, $head_id = NULL, $from_date, $to_date)
    {
        $totalAmount = 0;
        $bsql = "SELECT SUM(a.dr) AS balance FROM " . ACCOUNT_JOURNAL_TBL . " AS a," . SUB_ACC_HEAD_TBL . " AS s WHERE BINARY a.sub_id = s.sub_id AND s.project_id = '$project_id' AND s.head_type = '$head_type' ";
        if ($head_id != "") {
            $bsql .= " AND $head_id ";
        }
        if ($from_date != "" && $to_date != "") {
            $bsql .= " AND a.created_date BETWEEN '$from_date' AND '$to_date'";
        } else {
            $bsql .= " AND a.created_date > '$from_date'";
        }
        $bres = mysql_query($bsql);
        if (mysql_num_rows($bres) > 0) {
            $brow = mysql_fetch_object($bres);
            if ($brow->balance != "") {
                $totalAmount = $brow->balance;
            } else {
                $totalAmount = 0;
            }
        }
        return $totalAmount;
    }


    function getSLDrBalance($project_id, $head_type, $subhead_type = NULL, $childheadtype = NULL, $from_date, $to_date)
    {

        $totalAmount = 0;
        $bsql = "SELECT SUM(a.dr) AS balance FROM " . ACCOUNT_JOURNAL_TBL . " AS a," . SUB_ACC_HEAD_TBL . " AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
        if ($subhead_type != "") {
            $bsql .= " AND s.sub_headtype='$subhead_type'";
        }
        if ($childheadtype != "") {
            $bsql .= " AND s.child_head='$childheadtype'";
        }
        if ($from_date != "" && $to_date != "") {
            $bsql .= " AND a.created_date BETWEEN '$from_date' AND '$to_date'";
        } else {
            $bsql .= " AND a.created_date > '$from_date'";
        }
        $bres = mysql_query($bsql);
        if (mysql_num_rows($bres) > 0) {
            $brow = mysql_fetch_object($bres);
            if ($brow->balance != "") {
                $totalAmount = $brow->balance;
            } else {
                $totalAmount = 0;
            }
        }
        return $totalAmount;
    }

    function getBSFixedAsseteBalance($project_id, $head_type, $subhead_type = NULL, $from_date, $to_date)
    {
        $totalAmount = 0;

        // Base SQL
        $bsql = "SELECT (SUM(a.dr) - SUM(a.cr)) AS balance 
              FROM " . ACCOUNT_JOURNAL_TBL . " AS a, " . SUB_ACC_HEAD_TBL . " AS s 
              WHERE BINARY a.sub_id = s.sub_id 
              AND s.project_id = '$project_id' 
              AND s.head_type = '$head_type'";

        // Sub head type filter
        if ($subhead_type != "") {
            $bsql .= " AND s.sub_headtype = '$subhead_type'";
        }

        if ($from_date != "" && $to_date != "") {
            $bsql .= " AND a.created_date BETWEEN '$from_date' AND '$to_date'";
        } else {
            $bsql .= " AND a.created_date > '$from_date'";
        }

        // Execute Query
        $bres = mysql_query($bsql);

        if ($bres && mysql_num_rows($bres) > 0) {
            $brow = mysql_fetch_object($bres);
            $totalAmount = ($brow->balance != "") ? $brow->balance : 0;
        }

        return $totalAmount;
    }


    function getISProductClosingValue($project_id, $head_type, $head_id = NULL, $from_date, $to_date)
    {
        $closing_value = 0;
        $sql = "SELECT (SUM(a.dr)- SUM(a.cr)) AS closing_value FROM " . ACCOUNT_JOURNAL_TBL . " AS a," . SUB_ACC_HEAD_TBL . " AS s WHERE BINARY 
	a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
        if ($head_id != "") {
            $sql .= " AND $head_id";
        }
        if ($to_date != "") {
            $sql .= " AND a.created_date <= '$to_date'";
        } //echo $sql;
        $bres = mysql_query($sql);

        if (mysql_num_rows($bres) > 0) {
            $brow = mysql_fetch_object($bres);
            if ($brow->closing_value != "") {
                $closing_value = $brow->closing_value;
            } else {
                $closing_value = 0;
            }
        }

        return $closing_value;
    }


    function getISProductPurchaseValue($project_id, $head_type, $head_id, $from_date, $to_date)
    {
        $totalPVAmount = 0;

        $sql = "SELECT (SUM(a.dr)) AS totalAmount FROM " . ACCOUNT_JOURNAL_TBL . " AS a," . SUB_ACC_HEAD_TBL . " AS s WHERE BINARY 
	a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
        if ($head_id != "") {
            $sql .= " AND $head_id ";
        }
        if ($from_date != "" && $to_date != "") {
            $sql .= " AND a.created_date BETWEEN '$from_date' AND '$to_date'";
        } //echo $sql;
        $bres = mysql_query($sql);
        if (mysql_num_rows($bres) > 0) {
            $brow = mysql_fetch_object($bres);
            if ($brow->totalAmount != "") {
                $totalPVAmount = $brow->totalAmount;
            } else {
                $totalPVAmount = 0;
            }
        }


        return $totalPVAmount;
    }

    function getISProductOpeingValue($project_id, $head_type, $head_id = NULL, $from_date, $to_date)
    {

        $totalAmount = 0;
        $totalAmount = 0;
        $sql = "SELECT (SUM(a.dr)- SUM(a.cr)) AS totalAmount FROM " . ACCOUNT_JOURNAL_TBL . " AS a," . SUB_ACC_HEAD_TBL . " AS s WHERE BINARY a.`sub_id` = s.`sub_id` AND s.project_id = '$project_id' AND s.`head_type` = '$head_type' ";
        if ($head_id != "") {
            $sql .= " AND $head_id";
        }
        if ($from_date != "") {
            $sql .= " AND a.created_date < '$from_date'";
        }

        $bres = mysql_query($sql);
        if (mysql_num_rows($bres) > 0) {
            $brow = mysql_fetch_object($bres);
            if ($brow->totalAmount != "") {
                $totalAmount = $brow->totalAmount;
            } else {
                $totalAmount = 0;
            }
        }

        if ($totalAmount == 0) {
            $rbsql = "SELECT opening_balance FROM " . OPENING_BALANCE_TBL . " WHERE project_id='$project_id' AND `head_type`='Raw Materials'";
            $rbres = mysql_query($rbsql);
            if (mysql_num_rows($rbres) > 0) {
                $rbrow = mysql_fetch_object($rbres);
                if ($rbrow->opening_balance != "") {
                    $totalAmount = $rbrow->opening_balance;
                } else {
                    $totalAmount = 0;
                }
            }
        }

        return $totalAmount;
    }


} // End class
?>
