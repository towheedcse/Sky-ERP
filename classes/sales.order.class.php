<?php
require_once('journal.class.php');

class SalesOrder extends Journal
{
    function run()
    {
        $cmd = getRequest('cmd');
        $u_t_id = getFromSession('u_type_id');
        if (($u_t_id == 101) || ($u_t_id == 102) || ($u_t_id == 103) || ($u_t_id == 105)) // 101 = sysadmin, 102 = admin, 103= salesman
        {
            switch ($cmd) {
                case 'add'            :
                    $this->showEditor();
                    break;
                case 'direct_invoice'        :
                    $this->showDirectInvoice();
                    break;
                case 'check_work_order'      :
                    $this->checkWorkOrder();
                    break;
                case 'bill_generate'         :
                    $this->billGenerate();
                    break;
                case 'get_invoice_items'     :
                    $this->getInvoiceItems();
                    break;
                case 'get_customer_invoice'  :
                    $this->getCustomerInvoiceList();
                    break;
                case 'remove_customer_invoice':
                    $this->removeCustomerInvoice();
                    break;
                case 'save_generate_bill'    :
                    $this->saveGenerateBill();
                    break;
                case 'print_bill_invoice'    :
                    $this->printBillInvoice();
                    break;
                case 'delete_bill'        :
                    $this->deleteBillInvoice();
                    break;
                case 'get_customer_invoice_items'  :
                    $this->getCustomerInvoiceItemList();
                    break;
                case 'sal_dtl'        :
                    $this->showEditor4SalesDetails();
                    break;
                case 'admin_sal_dtl'        :
                    $this->showAllCompaniesSalesDetails();
                    break;
                case 'loadProduct'        :
                    $this->loadProduct4Catagory(trim(getRequest('catagory_id')));
                    break;
                case 'loadcatProduct'    :
                    $this->loadProductbyCatagory(trim(getRequest('catagory_id')));
                    break;
                case 'loadcustomer'    :
                    $this->loadPartybyArea(trim(getRequest('area_id')));
                    break;
                case 'loadundelivery_inv'    :
                    $this->loadUndeliveredInvoice(trim(getRequest('customer_id')));
                    break;
                case 'get_temp_dtl'    :
                    $this->getTempDetails(trim(getRequest('product_id')));
                    break;
                case 'get_dtl'        :
                    $this->loadProductDtl(trim(getRequest('product_id')));
                    break;
                case 'get_undelivery'    :
                    $this->loadUndeliverySales();
                    break;
                case 'get_undelivery_direct' :
                    $this->loadUndeliverySales(true);
                    break;
                case 'get_temp_order'    :
                    $this->getTempSalesOrder();
                    break;
                case 'save_tmp'        :
                    $this->saveTempSales();
                    break;
                case 'save_direct_tmp'    :
                    $this->saveTempSales(true);
                    break;
                case 'deltemp'        :
                    $this->delTempSales();
                    break;
                case 'save_sales'        :
                    $this->saveSalesItem();
                    break;
                case 'approved'        :
                    $this->approvedSalesOrder();
                    break;
                case 'checked'               :
                    $this->checkedSalesOrder();
                    break;
                case 'print_vouchar'        :
                    $screen = $this->showPrintEditor($msg);
                    break;
                case 'delete'                :
                    $screen = $this->deleteRecord(getRequest('id'));
                    break;
                default                    :
                    $cmd = 'list';
                    $screen = $this->showEditor();
                    break;
            }
        } elseif (($u_t_id == 107)) // 101 = sysadmin, 102 = admin, 103= salesman
        {
            switch ($cmd) {
                case 'add'            :
                    $this->showEditor();
                    break;
                case 'direct_invoice'        :
                    $this->showDirectInvoice();
                    break;
                case 'check_work_order'      :
                    $this->checkWorkOrder();
                    break;
                case 'bill_generate'         :
                    $this->billGenerate();
                    break;
                case 'get_invoice_items'     :
                    $this->getInvoiceItems();
                    break;
                case 'get_customer_invoice'  :
                    $this->getCustomerInvoiceList();
                    break;
                case 'remove_customer_invoice':
                    $this->removeCustomerInvoice();
                    break;
                case 'save_generate_bill'    :
                    $this->saveGenerateBill();
                    break;
                case 'print_bill_invoice'    :
                    $this->printBillInvoice();
                    break;
                case 'delete_bill'        :
                    $this->deleteBillInvoice();
                    break;
                case 'get_customer_invoice_items'  :
                    $this->getCustomerInvoiceItemList();
                    break;
                case 'sal_dtl'        :
                    $this->showEditor4SalesDetails();
                    break;
                case 'admin_sal_dtl'        :
                    $this->showAllCompaniesSalesDetails();
                    break;
                case 'loadProduct'        :
                    $this->loadProduct4Catagory(trim(getRequest('catagory_id')));
                    break;
                case 'loadcatProduct'    :
                    $this->loadProductbyCatagory(trim(getRequest('catagory_id')));
                    break;
                case 'loadundelivery_inv'    :
                    $this->loadUndeliveredInvoice(trim(getRequest('customer_id')));
                    break;
                case 'loadcustomer'    :
                    $this->loadPartybyArea(trim(getRequest('area_id')));
                    break;
                case 'get_temp_dtl'    :
                    $this->getTempDetails(trim(getRequest('product_id')));
                    break;
                case 'get_dtl'        :
                    $this->loadProductDtl(trim(getRequest('product_id')));
                    break;
                case 'get_undelivery'    :
                    $this->loadUndeliverySales();
                    break;
                case 'get_undelivery_direct' :
                    $this->loadUndeliverySales(true);
                    break;
                case 'get_temp_order'    :
                    $this->getTempSalesOrder();
                    break;
                case 'save_tmp'        :
                    $this->saveTempSales();
                    break;
                case 'save_direct_tmp'    :
                    $this->saveTempSales(true);
                    break;
                case 'deltemp'        :
                    $this->delTempSales();
                    break;
                case 'save_sales'        :
                    $this->saveSalesItem();
                    break;
                case 'approved'        :
                    $this->approvedSalesOrder();
                    break;
                case 'checked'               :
                    $this->checkedSalesOrder();
                    break;
                case 'print_vouchar'        :
                    $screen = $this->showPrintEditor($msg);
                    break;
                default                    :
                    $cmd = 'list';
                    $screen = $this->showEditor();
                    break;
            }
        } elseif ($u_t_id == 104) // 104 = acc
        {
            switch ($cmd) {
                case 'sal_dtl'        :
                    $this->showEditor4SalesDetails();
                    break;
                case 'loadcatProduct'    :
                    $this->loadProductbyCatagory(trim(getRequest('catagory_id')));
                    break;
                case 'loadcustomer'    :
                    $this->loadPartybyArea(trim(getRequest('area_id')));
                    break;
                case 'loadundelivery_inv'    :
                    $this->loadUndeliveredInvoice(trim(getRequest('customer_id')));
                    break;
                case 'print_vouchar'        :
                    $screen = $this->showPrintEditor($msg);
                    break;
                case 'direct_invoice'        :
                    $this->showDirectInvoice();
                    break;
                case 'check_work_order'      :
                    $this->checkWorkOrder();
                    break;
                case 'bill_generate'         :
                    $this->billGenerate();
                    break;
                case 'get_invoice_items'     :
                    $this->getInvoiceItems();
                    break;
                case 'get_customer_invoice'  :
                    $this->getCustomerInvoiceList();
                    break;
                case 'remove_customer_invoice':
                    $this->removeCustomerInvoice();
                    break;
                case 'save_generate_bill'    :
                    $this->saveGenerateBill();
                    break;
                case 'print_bill_invoice'    :
                    $this->printBillInvoice();
                    break;
                case 'delete_bill'        :
                    $this->deleteBillInvoice();
                    break;
                case 'get_customer_invoice_items'  :
                    $this->getCustomerInvoiceItemList();
                    break;
                default                    :
                    $cmd = 'list';
                    $screen = $this->showEditor();
                    break;
            }

        } else {
            header("location:index.php?app=user_home&msg=You are not authorised !!!");
        }
        if ($cmd == 'list') {
            require_once(CURRENT_APP_SKIN_FILE);
        }
        return true;
    }

    function showPrintEditor($msg = null)
    {
        $voucher_no = getRequest('voucher_no');
        if ($voucher_no) {
            $advArr = $this->getSalesMasterInfo($voucher_no);
            $advArr = parseThisValue($advArr);
            $data = array_merge(array(), $advArr);
            $data['item_list'] = $this->getProductList($voucher_no);
            $data['message'] = $msg;
            $data['cmd'] = getRequest('cmd');
            require_once(SALES_VOUCHAR_SKIN);
            return true;
        } else {
            require_once(PRINT_VOUCHAR_SKIN);
        }
    }


    function showEditor($msg = null)
    {
        $data = array();
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();

        $dropdownFields = array('sub_id', 'sub_head_name');

        $customer_list = $comListApp->getCustomerList($dropdownFields);
        $supplier_list_receivable = $comListApp->getCustomerListReceivable($dropdownFields);
        $merged = array_merge($customer_list, $supplier_list_receivable);

        $unique = array();
        foreach ($merged as $item) {
            $unique[$item->sub_id] = $item;
        }

        $data['customer_list'] = array_values($unique);


        $data['supplier_list'] = $comListApp->getSupplierList($dropdownFields);
        $data['reference_list'] = $comListApp->getReferenceList();
        $data['product_list'] = $comListApp->getProductList();
        $data['cat_list'] = $this->getCatagoryList();
        $data['brand_list'] = $comListApp->getBrandList();
        $data['currency_list'] = $this->getCurrencyList();
        $data['area_list'] = $comListApp->getAreaList();
        $data['depo_list'] = $comListApp->getDeliveryPointList(true);
        $data['retailer_list'] = $comListApp->getRetailerList();
        $data['vehicle_list'] = $this->getVehicleList();
        $data['tmp_sales'] = $this->getTempSales();

        $data['cmd'] = getRequest('cmd');
        require_once(CURRENT_APP_SKIN_FILE);
        return $data[0];
    }


    function getVehicleList()
    {
        $info = array();
        $info['table'] = VEHICLES_TBL;
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


    function billGenerate($msg = null)
    {
        $data = array();
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();

        $dropdownFields = array('sub_id', 'sub_head_name');

        $customer_list = $comListApp->getCustomerList($dropdownFields);
        $supplier_list_receivable = $comListApp->getCustomerListReceivable($dropdownFields);
        $merged = array_merge($customer_list, $supplier_list_receivable);
        $unique = array();
        foreach ($merged as $item) {
            $unique[$item->sub_id] = $item;
        }

        $data['customer_list'] = array_values($unique);

        $data['supplier_list'] = $comListApp->getSupplierList($dropdownFields);
        $data['currency_list'] = $this->getCurrencyList();
        $data['area_list'] = $comListApp->getAreaList();
        $data['depo_list'] = $comListApp->getDeliveryPointList(true);
        $data['tmp_sales'] = $this->getTempSales();

        $data['cmd'] = getRequest('cmd');
        require_once(TEMPLATES_SKINS . '/bill.generate.html');
        return $data[0];
    }

    function deleteBillInvoice()
    {
        $bill_id = getRequest('bill_id');
        if (empty($bill_id)) {
            $error_msg = "Bill ID not Found!";
            header("location:?app=sales.report&cmd=bill_list&error_msg=$error_msg");
            exit();
        }

        if (!userCondition()) {
            $error_msg = "You are not authorized to delete!";
            header("location:?app=sales.report&cmd=bill_list&error_msg=$error_msg");
            exit();
        }

        $project_id = getFromSession('project_id');

        $billSql = "SELECT bill_id FROM bill WHERE bill_id = '$bill_id' AND project_id = '$project_id' AND status = '1' LIMIT 1";

        $result = mysql_query($billSql);
        $bill = mysql_fetch_assoc($result);

        if (empty($bill['bill_id'])) {
            $error_msg = "Bill record not Found!";
            header("location:?app=sales.report&cmd=bill_list&error_msg=$error_msg");
            exit();
        }

        // Delete related bill_invoices first
        $deleteInvoices = "DELETE FROM bill_invoices WHERE bill_id = '$bill_id'";
        mysql_query($deleteInvoices);

        // Delete the bill itself
        $deleteBill = "DELETE FROM bill WHERE bill_id = '$bill_id'";
        mysql_query($deleteBill);


        $msg = "Bill Deleted Successfully!";
        header("location:?app=sales.report&cmd=bill_list&msg=$msg");
        exit();

    }

    function printBillInvoice()
    {
        $bill_id = getRequest('bill_id');
        if (empty($bill_id)) {
            $error_msg = "Bill ID not Found!";
            header("location:?app=sales.report&cmd=bill_list&error_msg=$error_msg");
            exit();
        }

        $project_id = getFromSession('project_id');

        $billSql = "SELECT b.*,p.project_name,d.delivery_point_name,p.location,p.project_logo,s.sub_id,s.sub_head_name,s.head_details,s.phone,s.mobile,s.email,s.att_name1,s.att_designation1,s.att_mobile1,c.curr_symble FROM bill b
            LEFT JOIN " . SUB_ACC_HEAD_TBL . " s ON s.sub_id = b.customer
            LEFT JOIN " . PROJECT_TBL . " p ON p.project_id = b.project_id
            LEFT JOIN " . CURRENCY_TBL . " c ON c.currency_id = b.currency
	    LEFT JOIN " . DELIVERY_POINT_TBL . " d ON d.delivery_pid  =b.store_id
            WHERE b.bill_id = '$bill_id' AND b.project_id = '$project_id' AND b.status = '1' LIMIT 1";

        $result = mysql_query($billSql);
        $bill = mysql_fetch_assoc($result);

        if (empty($bill['bill_id'])) {
            $error_msg = "Bill record not Found!";
            header("location:?app=sales.report&cmd=bill_list&error_msg=$error_msg");
            exit();
        }

        $invoice_sql = "SELECT * FROM bill_invoices WHERE bill_id = '$bill_id'";

        $bill_invoices = mysql_query($invoice_sql);

        $workOrderArray = [];
        $workOrderDate = [];

        $data['bill_invoices'] = [];
        if ($bill_invoices) {
            while ($row = mysql_fetch_assoc($bill_invoices)) {
                $voucher = $row['invoice_no'];
                if (!isset($data['bill_invoices'][$voucher])) {
                    $advArr = $this->getSalesMasterInfo($voucher);
                    $advArr = parseThisValue($advArr);

                    if (!empty($advArr['ref_voucher']) && !in_array($advArr['ref_voucher'], $workOrderArray)) {
                        $workOrderArray[] = $advArr['ref_voucher'];
                    }

                    if (!empty($advArr['sales_date']) && !in_array($advArr['sales_date'], $workOrderDate)) {
                        $workOrderDate[] = $advArr['sales_date'];
                    }

                    $data['bill_invoices'][$voucher] = [
                        'voucher_no' => $voucher,
                        'master' => $advArr,
                        'details' => []
                    ];
                }

                $data['bill_invoices'][$voucher]['details'][$voucher] = $row;
            }
        }

        $data['bill'] = $bill;

        $customer_id = $bill['sub_id'];
        $checkSql = "SELECT customer_type,credit_days FROM " . SUB_ACC_HEAD_TBL . " WHERE sub_id = '" . mysql_real_escape_string($customer_id) . "' LIMIT 1";
        $checkRow = mysql_fetch_object(mysql_query($checkSql));

        $credit_days = 0;
        if (isset($checkRow->credit_days) && $checkRow->credit_days > 0) {
            $credit_days = (int)$checkRow->credit_days;
        }
        $data['credit_days'] = $credit_days;
        $data['workOrderArray'] = array_filter($workOrderArray);
        $data['workOrderDate'] = array_filter($workOrderDate);
        if (empty($bill['und_no'])) {
            $bill_id = $bill['bill_id'];
            $und_no = implode(", ", $workOrderArray);
            mysql_query("UPDATE bill SET und_no = '$und_no' WHERE bill_id = '$bill_id'");
        }

        $data['cmd'] = getRequest('cmd');
        //require_once(TEMPLATES_SKINS.'/print_bill_invoice.html');
        require_once(TEMPLATES_SKINS . '/print_bill_invoice_new.html');
        return $data[0];
    }

    function getUnDeliveryProductList($voucher_no)
    {
        $info = array();
        $info['table'] = SALES_DETAILS_TBL . ' sd,' . CURRENCY_TBL . ' c,' . PRODUCT_TBL . ' p,' . BRAND_TBL . ' b';
        $info['fields'] = array('sd.sal_detail_id', 'sd.voucher_no', 'sd.project_id', 'sd.catagory', 'sd.serial', 'sd.warranty', 'b.brand_name', 'sd.product', 'sd.details', 'p.product_name', 'p.product_desc', 'p.product_code', 'p.weight', 'sd.m_unit', 'sd.unit_price', 'c.curr_symble', 'sd.discount_per_qty', 'sd.discount_amount', 'SUM(sd.qty) as qty', 'SUM(sd.delivery_qty) as delivery_qty', 'sd.prev_undelivery_qty as undelivery_qty', 'sd.total_bag as free_qty', 'SUM(sd.total) as total', 'sd.created_time', 'sd.vat', 'sd.vat_amount');

        $sql = "sd.product = p.product_id AND p.brand_code = b.brand_id AND sd.currency = c.currency_id AND sd.voucher_no = '$voucher_no' AND sd.prev_undelivery_qty >0";

        $info['where'] = $sql;
        $info['groupby'] = array("p.product_id");
        $info['orderby'] = array("sd.sal_detail_id asc");
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

    function saveGenerateBill()
    {
        $requestdata = array();
        $bill_id = getRequest('bill_id');

        if ($bill_id == "") {
            $getGM = "SELECT * FROM bill WHERE bill_id = '$bill_id'";
            $gres = mysql_query($getGM);
            if (mysql_num_rows($gres) <= 0) {
                $msg = "Bill Record Not Found";
                header("location:?app=sales.order&cmd=bill_generate&error_msg=$msg");
            }
        }


        $requestdata['bill_date'] = formatDate(getRequest('bill_date'));
        $requestdata['aging_date'] = formatDate(getRequest('aging_date'));

        $requestdata['und_no'] = getRequest('und_wo_no') || NULL;
        if (getRequest('wo_no') != "") {
            $requestdata['und_no'] = getRequest('wo_no');
        }

        $customer = getRequest('customer');
        $requestdata['customer'] = $customer;

        $requestdata['area'] = getRequest('area');
        $requestdata['store_id'] = getRequest('delivery_point');
        $requestdata['net_payable'] = getRequest('net_payble');
        $requestdata['due'] = $requestdata['net_payable'];

        $general_discount_percent = getRequest('general_discount_percent');
        $general_discount_amount = getRequest('general_discount_amount');

        $exclusive_discount_percent = getRequest('exclusive_discount_percent');
        $exclusive_discount_amount = getRequest('exclusive_discount_amount');

        $additional_discount_percent = getRequest('additional_discount_percent');
        $additional_discoiunt_amount = getRequest('additional_discount');

        $requestdata['general_discount_percentage'] = $general_discount_percent;
        $requestdata['general_discount_amount'] = $general_discount_amount;
        $requestdata['exclusive_discount_percentage'] = $exclusive_discount_percent;
        $requestdata['exclusive_discount_amount'] = $exclusive_discount_amount;
        $requestdata['additional_discount_percentage'] = $additional_discount_percent;
        $requestdata['additional_discount_amount'] = $additional_discoiunt_amount;

        $product_discount = getRequest('discount');

        $requestdata['total_value'] = getRequest('total_value');

        $requestdata['vat_type'] = getRequest('vat_type');
        $requestdata['vat_percentage'] = getRequest('total_vat_percent');
        $requestdata['vat_amount'] = getRequest('total_vat_amount');
        $requestdata['additional_cost'] = getRequest('additional_cost');

        //$TotalDiscount = ($general_discount_amount + $exclusive_discount_amount + $additional_discount + $product_discount);


        $bill_note = getRequest('bill_note');
        $bill_note = str_replace('"', "&ldquo;", $bill_note);
        $bill_note = str_replace("'", "&#8217;", $bill_note);
        $requestdata['bill_note'] = $bill_note;
        $requestdata['project_id'] = getFromSession('project_id');
        $requestdata['created_by'] = getFromSession('userid');
        $requestdata['status'] = 1;

        $info = array();
        $info['table'] = "bill";
        $info['data'] = $requestdata;
        $info['where'] = "bill_id ='$bill_id'";
        //$info['debug']  =  false;
        $res = update($info);

        $msg = "Bill Generate Successfully";
        header("location:?app=sales.order&cmd=bill_generate&msg=$msg");
        exit();

    }


    function getCustomerInvoiceList()
    {
        if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering

        $input = json_decode(file_get_contents('php://input'), true);

        // Validate input
        if (json_last_error() !== JSON_ERROR_NONE || !isset($input['customer_id'])) {
            die(json_encode([
                'status' => false,
                'message' => 'Invalid JSON data received'
            ]));
        }

        $customer_id = trim($input['customer_id']);
        $project_id = getFromSession('project_id');
        $invoiceList = $this->invoiceCustomerInvoiceList($customer_id);
        $aging_invoice = $this->getOverdueBillInvoices($customer_id);


        $response = [
            'status' => true,
            'message' => 'Success',
            'data' => [
                "invoiceList" => $invoiceList,
                "aging_invoice" => $aging_invoice,
            ]
        ];

        // Output response
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    function invoiceCustomerInvoiceList($customer_id)
    {
        // Step 1: get all vouchers that already have a bill
        $invoice_sql = "
	    SELECT bi.invoice_no
	    FROM bill_invoices bi
	    JOIN bill b ON b.bill_id = bi.bill_id
	    WHERE b.customer = '" . $customer_id . "'
	";
        $result = mysql_query($invoice_sql);

        $invoices = [];
        while ($row = mysql_fetch_assoc($result)) {
            $invoices[] = "'" . $row['invoice_no'] . "'";
        }

        // Convert array to comma-separated string for SQL
        $invoice_list = !empty($invoices) ? implode(',', $invoices) : "''"; // empty string if no invoice


        $sql = "SELECT m.*, DATE_FORMAT(m.sales_date,'%d %b %y') AS sales_date FROM " . SALES_MASTER_TBL . " AS m
        INNER JOIN " . SALES_DELIVERY_MASTER_TBL . " AS d ON m.voucher_no = d.voucher_no
        WHERE m.customer = '$customer_id' AND m.voucher_no NOT IN ($invoice_list) GROUP BY m.voucher_no";

        $gres = mysql_query($sql);
        $product_idname = "<option value=''>Select Invoice</option>";
        while ($row = mysql_fetch_object($gres)) {
            if ($row->voucher_no != "") {
                $product_idname .= "<option value='" . $row->voucher_no . "'>" . $row->voucher_no . ", " . $row->sales_date . "</option>";
            }
        }

        return $product_idname;
    }


    function safe_json_encode($data)
    {
        array_walk_recursive($data, function (&$value) {
            if (is_string($value)) {
                $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            }
        });

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }


    function getCustomerInvoiceItemList()
    {
        if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering

        $input = json_decode(file_get_contents('php://input'), true);

        // Validate input
        if (json_last_error() !== JSON_ERROR_NONE || !isset($input['invoice_no'])) {
            die(json_encode([
                'status' => false,
                'message' => 'Invalid JSON data received'
            ]));
        }

        $invoice_no = trim($input['invoice_no']);
        $customer_id = trim($input['customer_id']);

        $wo_no = trim($input['wo_no']);
        $currency = trim($input['currency']);
        $currency_code = trim($input['currency_code']);
        $area = trim($input['area']);
        $delivery_point = trim($input['delivery_point']);
        $bill_date = trim($input['bill_date']);
        $aging_date = trim($input['aging_date']);
        $bill_note = trim($input['bill_note']);

        $project_id = getFromSession('project_id');
        $created_by = getFromSession('userid');

        $billSql = "SELECT bill_id, customer FROM bill 
            WHERE created_by = '$created_by' 
              AND project_id = '$project_id' 
              AND status = '0'
            LIMIT 1";
        $result = mysql_query($billSql);

        if ($row = mysql_fetch_assoc($result)) {
            if ($row['customer'] != $customer_id) {
                $bill_id = $row['bill_id'];

                // Delete related bill_invoices first
                $deleteInvoices = "DELETE FROM bill_invoices WHERE bill_id = '$bill_id'";
                mysql_query($deleteInvoices);

                // Delete the bill itself
                $deleteBill = "DELETE FROM bill WHERE bill_id = '$bill_id'";
                mysql_query($deleteBill);
                $bill_id = NULL;
            } else {
                // Bill exists and customer matches, you can reuse this bill_id
                $bill_id = $row['bill_id'];
            }
        }

        if (empty($bill_id)) {
            $insertBill = "INSERT INTO bill (customer, project_id,und_no, currency, currency_code, area, store_id, bill_date, aging_date, bill_note, created_by, status) 
                   VALUES ('$customer_id', '$project_id', '$wo_no', '$currency', '$currency_code', '$area', '$delivery_point', '$bill_date', '$aging_date', '$bill_note', '$created_by', '0')";
            mysql_query($insertBill);
            $bill_id = mysql_insert_id(); // get the new bill ID

            $voucher_no = 'INV' . str_pad($bill_id, 6, '0', STR_PAD_LEFT);
            mysql_query("UPDATE bill SET voucher_no = '$voucher_no' WHERE bill_id = '$bill_id'");
        } else {
            $updateBill = "UPDATE bill 
                   SET customer = '$customer_id',
                       project_id = '$project_id',
                       und_no = '$wo_no',
                       currency = '$currency',
                       currency_code = '$currency_code',
                       area = '$area',
                       store_id = '$delivery_point',
                       bill_date = '$bill_date',
                       aging_date = '$aging_date',
                       bill_note = '$bill_note'
                   WHERE bill_id = '$bill_id'";
            mysql_query($updateBill);
        }


        $invoiceSql = "
		    SELECT sd.*,sdc.unit_price,sdc.delivery_qty, p.product_name, p.product_desc, p.product_code
		    FROM " . SALES_DETAILS_TBL . " sd
		    LEFT JOIN " . PRODUCT_TBL . " p ON sd.product = p.product_id
		    JOIN " . SALES_DELIVERY_CHALLAN_TBL . " sdc ON sdc.voucher_no = sd.voucher_no AND sdc.product = sd.product
		    WHERE sd.voucher_no = '$invoice_no'
		";
        $result = mysql_query($invoiceSql);

        $snapShortData = [];
        while ($invoiceDetails = mysql_fetch_assoc($result)) {
            if (!empty($invoiceDetails['voucher_no'])) {
                $snapShortData[] = [
                    "sal_detail_id" => $invoiceDetails['sal_detail_id'],
                    "voucher_no" => $invoiceDetails['voucher_no'],
                    "customer" => $invoiceDetails['customer'],
                    "catagory" => $invoiceDetails['catagory'],
                    "brand_id" => $invoiceDetails['brand_id'],
                    "product" => $invoiceDetails['product'],
                    "product_code" => $invoiceDetails['product_code'],
                    "product_name" => $invoiceDetails['product_name'],
                    "product_desc" => $invoiceDetails['product_desc'],
                    "details" => $invoiceDetails['details'],
                    "m_unit" => $invoiceDetails['m_unit'],
                    "unit_price" => $invoiceDetails['unit_price'],
                    "purchase_price" => $invoiceDetails['purchase_price'],
                    "discount_per_qty" => $invoiceDetails['discount_per_qty'],
                    "discount_amount" => $invoiceDetails['discount_amount'],
                    "qty" => $invoiceDetails['qty'],
                    "delivery_qty" => $invoiceDetails['delivery_qty'],
                    "free_qty" => $invoiceDetails['free_qty'],
                    "undelivery_qty" => $invoiceDetails['undelivery_qty'],
                    "total" => $invoiceDetails['total'],
                    "vat" => $invoiceDetails['vat'],
                    "vat_amount" => $invoiceDetails['vat_amount'],
                    "prev_undelivery_qty" => $invoiceDetails['prev_undelivery_qty'],
                ];
            }


        }

        $invoice_snap_short = $this->safe_json_encode($snapShortData);
        $invoice_snap_short = mysql_real_escape_string($invoice_snap_short);

        // Get WO No (move outside loop if same for all)
        $wo_no = $invoice_no;

        $masterSql = "SELECT additional_cost,ref_voucher FROM " . SALES_MASTER_TBL . " WHERE voucher_no='$invoice_no'";
        $master = mysql_fetch_assoc(mysql_query($masterSql));

        if (!empty($master['ref_voucher'])) {
            $wo_no = $master['ref_voucher'];
        }
        $additional_cost = 0.0;
        if (!empty($master['additional_cost'])) {
            $additional_cost = $master['additional_cost'];
        }

        $insertInvoice = "INSERT INTO bill_invoices (bill_id, invoice_no, wo_no, invoice_snap_short,additional_cost) VALUES ('$bill_id', '$invoice_no', '$wo_no', '$invoice_snap_short','$additional_cost')";
        mysql_query($insertInvoice);


        $invoiceList = $this->invoiceCustomerInvoiceList($customer_id);
        $invoiceItemList = $this->invoiceCustomerInvoiceItemList($bill_id);

        $response = [
            'status' => true,
            'message' => 'Success',
            'data' => [
                "invoiceList" => $invoiceList,
                "invoiceItemList" => $invoiceItemList,
            ]
        ];

        // Output response
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    function getInvoiceItems()
    {
        if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering

        $project_id = getFromSession('project_id');
        $created_by = getFromSession('userid');
        $billSql = "SELECT * FROM bill WHERE created_by = '$created_by' AND project_id = '$project_id' AND status='0'";
        $result = mysql_query($billSql);
        $billData = mysql_fetch_assoc($result);


        $bill_id = $billData['bill_id'];
        $customer_id = $billData['customer'];
        $invoiceItemList = $this->invoiceCustomerInvoiceItemList($bill_id);
        $invoiceList = $this->invoiceCustomerInvoiceList($customer_id);
        $aging_invoice = $this->getOverdueBillInvoices($customer_id);

        $response = [
            'status' => true,
            'message' => 'Success',
            'data' => [
                "invoiceList" => $invoiceList,
                "invoiceItemList" => $invoiceItemList,
                "aging_invoice" => $aging_invoice,
            ]
        ];


        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    function removeCustomerInvoice()
    {
        if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering

        $input = json_decode(file_get_contents('php://input'), true);

        // Validate input
        if (json_last_error() !== JSON_ERROR_NONE || !isset($input['invoice_no'])) {
            die(json_encode([
                'status' => false,
                'message' => 'Invalid JSON data received'
            ]));
        }

        $invoice_no = trim($input['invoice_no']);
        $deleteInvoices = "DELETE FROM bill_invoices WHERE invoice_no = '$invoice_no'";
        mysql_query($deleteInvoices);

        return $this->getInvoiceItems();
    }


    function invoiceCustomerInvoiceItemList($bill_id)
    {
        $project_id = getFromSession('project_id');
        $billSql = "SELECT * FROM bill WHERE bill_id = '$bill_id' AND project_id = '$project_id'";
        $result = mysql_query($billSql);
        $billData = mysql_fetch_assoc($result);

        $invoices = [];
        $workOrder = [];

        $html = "<table width='100%' align='center' bgcolor='#99CC66'>
		<tr style='color:#fff;' bgcolor='#00B000' height=28>
		  <td width='1%' nowrap><div align='left'>SL</div></td>
		  <td width='22%' nowrap><div align='left'>Product Name</div></td>
		  <td width='10%' nowrap><div align='left'>Catagory</div></td>
		  <td width='10%' nowrap><div align='left'>Brand</div></td>
		  <td width='8%' nowrap><div align='right'>Order Qty</div></td>
		  <td width='8%' nowrap><div align='right'>Free Qty</div></td>
		  <td width='8%' nowrap><div align='right'>Rate</div></td>
		  <td width='8%' nowrap><div align='center'>Discount %</div></td>		  
		  <td width='8%' nowrap><div align='center'>VAT %</div></td>			  
		  <td width='10%' nowrap><div align='right'>Amount</div></td>				  
		  <td width='7%' nowrap align='center'>Option</td>
		</tr>";


        $total_value = 0;
        $total_additional_cost = 0;
        $product_discount = 0;
        $TotalQty = 0;
        $TotalFreeQty = 0;
        $sl = 1;

        $getSql = "SELECT * FROM bill_invoices WHERE bill_id = '$bill_id'";
        $gres = mysql_query($getSql);

        $data = [];

        while ($row = mysql_fetch_assoc($gres)) {
            $snapList = json_decode($row['invoice_snap_short'], true);
            if (!is_array($snapList)) continue;

            foreach ($snapList as $snap) {
                $product_id = !empty($snap['product']) ? $snap['product'] : '';
                $invoice_no = $row['invoice_no'];

                if (!$product_id) continue;

                // product master data
                $productSql = "
            SELECT 
                p.product_id,
                p.product_name,
                p.product_desc,
                b.brand_name,
                c.catagory_name
            FROM " . PRODUCT_TBL . " p
            LEFT JOIN " . BRAND_TBL . " b ON b.brand_id = p.brand_code
            LEFT JOIN " . CATAGORY_TBL . " c ON c.catagory_code = p.catagory
            WHERE p.product_id = '$product_id'
        ";

                $productRes = mysql_query($productSql);
                $productData = mysql_fetch_assoc($productRes);

                $data[] = array(
                    "bill_id" => $row['bill_id'],
                    "invoice_no" => $row['invoice_no'],
                    "wo_no" => $row['wo_no'],
                    "additional_cost" => $row['additional_cost'],

                    // snapshot (EACH PRODUCT)
                    "product_id" => $product_id,
                    "qty" => isset($snap['qty']) ? $snap['qty'] : 0,
                    "delivery_qty" => isset($snap['delivery_qty']) ? $snap['delivery_qty'] : 0,
                    "free_qty" => isset($snap['free_qty']) ? $snap['free_qty'] : $productData['free_qty'],
                    "undelivery_qty" => isset($snap['undelivery_qty']) ? $snap['undelivery_qty'] : 0,
                    "unit_price" => isset($snap['unit_price']) ? $snap['unit_price'] : $productData['unit_price'],
                    "discount_per_qty" => isset($productData['discount_per_qty']) ? $productData['discount_per_qty'] : $snap['discount_per_qty'],
                    "discount_amount" => isset($productData['discount_amount']) ? $productData['discount_amount'] : $snap['discount_amount'],
                    "total" => isset($snap['total']) ? $snap['total'] : $productData['total'],
                    "vat" => isset($snap['vat']) ? $snap['vat'] : $productData['vat'],
                    "vat_amount" => isset($snap['vat_amount']) ? $snap['vat_amount'] : $productData['vat_amount'],

                    // product master
                    "product_name" => isset($productData['product_name']) ? $productData['product_name'] : $snap['product_name'],
                    "product_desc" => isset($productData['product_desc']) ? $productData['product_desc'] : $snap['product_desc'],
                    "brand_name" => isset($productData['brand_name']) ? $productData['brand_name'] : $snap['brand_name'],
                    "catagory_name" => isset($productData['catagory_name']) ? $productData['catagory_name'] : $snap['catagory_name'],

                    "m_unit" => isset($snap['m_unit']) ? $snap['m_unit'] : $snap['m_unit'],
                    "product_code" => isset($snap['product_code']) ? $snap['product_code'] : $snap['product_code']
                );
            }

        }

        foreach ($data as $row) {
            extract($row);
            $qty = (float)$delivery_qty;
            $unit_price = (float)$unit_price;
            $vat = (float)$vat;

            $product_name = $product_name;

            $subtotal = $qty * $unit_price;
            $vat_amount = ($subtotal * $vat) / 100;

            $invoices[] = $invoice_no;
            $workOrder[] = $wo_no;
            $total_value += $total;
            $total_additional_cost += $additional_cost;
            $product_discount += $discount_amount;
            $TotalQty += $qty;
            $TotalFreeQty += $free_qty;
            if (isset($product_code) && !empty($product_code)) {
                $product_name = $product_code . "::" . $product_name;
            }

            $html .= "
		<tr style='color:#000000' bgcolor='#fff'>
		  <td width='1%' nowrap>$sl</td>
		  <td width='22%' nowrap align='left'>$product_name</td>
		  <td width='10%' nowrap align='left'>$catagory_name</td>
		  <td width='10%' nowrap align='left'>$brand_name</td>
		  <td width='8%' nowrap><div align='right'>$qty $munit</div></td>
		  <td width='8%' nowrap><div align='right'>$free_qty $m_unit</div></td>
		  <td width='8%' nowrap><div align='right'>$unit_price $curr_symble</div></td>				  
		  <td width='8%' nowrap><div align='center'>$discount_amount ($discount_per_qty%)</div></td>				  
		  <td width='8%' nowrap><div align='center'>$vat_amount ($vat%)</div></td>		  
		  <td width='10%' nowrap><div align='right'>$total</div></td>				  				  
		  <td width='7%' nowrap align='center'>
		  <a href=\"#\" onclick=removeInvoice('$invoice_no')><img src=\"images/common/icons/delete.gif\"></a> &nbsp;
		  </td>
		</tr>";
            $sl++;
        }

        $html .= "
		<tr style='color:#000;' bgcolor='#CCCCCC' height=25>
		  <td colspan='4' nowrap><div align='right'>Total </div></td>
		  <td nowrap align='right'>$TotalQty $m_unit</td>
		  <td nowrap align='right'>$TotalFreeQty $m_unit</td>
		  <td nowrap>&nbsp;</td>
		  <td nowrap>&nbsp;</td>
		  <td nowrap>&nbsp;</td>
		  <td nowrap align='right'>$total_value $curr_symble </td>
		  <td nowrap align='center'>&nbsp;</td>
		</tr>
		</table>";


        $invoices = !empty($invoices) ? implode(", ", array_unique(array_filter($invoices))) : '';
        $workOrder = !empty($workOrder) ? implode(", ", array_unique(array_filter($workOrder))) : '';

        return [
            "bill" => $billData,
            "invoices" => $invoices,
            "wo_no" => $workOrder,
            "table_data" => $html,
            "total_value" => $total_value,
            "total_additional_cost" => $total_additional_cost,
        ];
    }


    function showDirectInvoice($msg = null)
    {
        $data = array();
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();

        // Use slim field lists (sub_id + sub_head_name only) so that loading
        // three full account-head lists at once does not exhaust PHP memory.
        $dropdownFields = array('sub_id', 'sub_head_name');

        $customer_list = $comListApp->getCustomerList($dropdownFields);
        $supplier_list_receivable = $comListApp->getCustomerListReceivable($dropdownFields);
        $merged = array_merge($customer_list, $supplier_list_receivable);
        $unique = array();
        foreach ($merged as $item) {
            $unique[$item->sub_id] = $item;
        }

        $data['customer_list'] = array_values($unique);

        $data['supplier_list'] = $comListApp->getSupplierList($dropdownFields);
        $data['reference_list'] = $comListApp->getReferenceList();
        $data['product_list'] = $comListApp->getProductList();
        $data['cat_list'] = $this->getCatagoryList();
        $data['brand_list'] = $comListApp->getBrandList();
        $data['currency_list'] = $this->getCurrencyList();
        $data['area_list'] = $comListApp->getAreaList();
        $data['depo_list'] = $comListApp->getDeliveryPointList(true);
        $data['retailer_list'] = $comListApp->getRetailerList();
        $data['vehicle_list'] = $this->getVehicleList();
        $data['tmp_sales'] = $this->getTempSales(true);

        $data['cmd'] = getRequest('cmd');
        require_once(TEMPLATES_SKINS . '/direct_invoice.html');
        return $data[0];
    }

    function checkWorkOrder()
    {
        if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering

        $input = json_decode(file_get_contents('php://input'), true);

        // Validate input
        if (json_last_error() !== JSON_ERROR_NONE || !isset($input['work_order_no'])) {
            die(json_encode([
                'status' => false,
                'message' => 'Invalid JSON data received'
            ]));
        }

        $project_id = getFromSession('project_id');
        $work_order_no = mysql_real_escape_string(trim($input['work_order_no']));
        $msql = "SELECT wo_no FROM " . SALES_MASTER_TBL . " WHERE wo_no = '$work_order_no' AND project_id = '$project_id'";
        $result = mysql_query($msql);

        $response = [
            'status' => false,
            'message' => ''
        ];

        if ($result && mysql_num_rows($result) > 0) {
            $response['status'] = true;
            $response['message'] = 'Work order already exists';
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }


    function getTempSalesOrder()
    {
        echo $this->getTempSales();
    }

    function approvedSalesOrder()
    {
        $voucher_no = $_REQUEST['voucher_no'];
        $project_id = getFromSession('project_id');
        if ($voucher_no != "") {
            $msql = "SELECT * FROM " . SALES_MASTER_TBL . " WHERE voucher_no ='" . $voucher_no . "' AND project_id='$project_id'";
            $mrow = mysql_fetch_object(mysql_query($msql));
            $net_payble = $mrow->net_payble;
            $customer = $mrow->customer;
            date_default_timezone_set('Asia/Dhaka');
            $approved_by = getFromSession('userid');
            $approved_time = date('Y-m-d H:i:s a');
            $dsql = "UPDATE " . SALES_MASTER_TBL . " SET approved_by='$approved_by',approved_time='$approved_time',status=1 WHERE voucher_no ='" . $voucher_no . "' AND project_id='$project_id'";
            mysql_query($dsql);
            $adsql = "UPDATE " . SALES_MASTER_APP_TBL . " SET approved_amount='$net_payble',approved_time='$approved_time', WHERE voucher_no ='" . $voucher_no . "' AND project_id='$project_id'";
            mysql_query($adsql);

            $Csql = "SELECT mobile,sub_head_name FROM " . SUB_ACC_HEAD_TBL . " WHERE sub_id ='" . $customer . "' AND project_id = '$project_id'";
            $Crow = mysql_fetch_object(mysql_query($Csql));
            if (trim($Crow->mobile) != "" && trim($Crow->att_mobile1) != "") {
                $recipients = $Crow->mobile . "," . $Crow->att_mobile1;
            } elseif (trim($Crow->mobile) != "" && trim($Crow->att_mobile1) == "") {
                $recipients = $Crow->mobile;
            } elseif (trim($Crow->mobile) == "" && trim($Crow->att_mobile1) != "") {
                $recipients = $Crow->att_mobile1;
            } else {
                $recipients = "";
            }

            if ($recipients != "") {
                $message = "Dear Sir, The approved Order No " . $voucher_no . " & amount is " . $net_payble . " TK. Party code is " . $Crow->sub_head_name . ". (" . COMPANY_NAME . ")";
                $sms_text = "Dear Sir, Your Sales Invoice No " . $voucher_no . " & amount is " . $net_payble . " TK. and " . getRequest('sms_text');
                //$this->sendSMS(COMPANY_NAME,$recipients,$message);
                require_once(CLASS_DIR . '/common.list.class.php');

                $numbers = explode(",", $recipients);
                foreach ($numbers as $recipients) {
                    if ($recipients != "") {
                        $response = (new CommonList())->sendSMS($recipients, $sms_text);
                    }
                }
            }

            header("location:index.php?app=sales.report&cmd=pending_order_list&msg=Successfully Approved Sales Order");
        }

    }

    function checkedSalesOrder()
    {
        $voucher_no = $_REQUEST['voucher_no'];
        $project_id = getFromSession('project_id');
        if ($voucher_no != "") {
            $msql = "SELECT * FROM " . SALES_MASTER_TBL . " WHERE voucher_no ='" . $voucher_no . "' AND project_id='$project_id'";
            $mrow = mysql_fetch_object(mysql_query($msql));

            $userid = getFromSession('userid');
            $checked_by = $userid;
            if ($mrow->checked_by) {
                $checked_by = $mrow->checked_by . "," . $userid;
            }

            $dsql = "UPDATE " . SALES_MASTER_TBL . " SET checked_by='$checked_by' WHERE voucher_no ='" . $voucher_no . "' AND project_id='$project_id'";
            mysql_query($dsql);
            $adsql = "UPDATE " . SALES_MASTER_APP_TBL . " SET checked_by='$checked_by' WHERE voucher_no ='" . $voucher_no . "' AND project_id='$project_id'";
            mysql_query($adsql);

            header("location:index.php?app=sales.report&cmd=pending_order_list&msg=Successfully Checked Sales Order");
        } else {
            header("location:index.php?app=sales.report&cmd=pending_order_list&msg=Voucher ID Missing");
        }

    }


    function sendSMS($sender, $recipients, $message)
    {
        $token = SMS_TOKEN;
        $url = "https://24smsbd.com/api/bulkSmsApi";
        $data = array(
            'sender_id' => "1903",
            'apiKey' => "$token",
            'mobileNo' => "$recipients",
            'message' => "$message"
        ); // Add parameters in key value
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $output = curl_exec($curl);
        curl_close($curl);
        //echo $output;
    }
    /*
   function sendSMS($sender,$recipients,$message){
	$token = SMS_TOKEN;
	$url = "http://api.greenweb.com.bd/api.php";
	$data= array(
	'to'=>"$recipients",
	'message'=>"$message",
	'token'=>"$token"
	); // Add parameters in key value
	$ch = curl_init(); // Initialize cURL
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_ENCODING, '');
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$smsresult = curl_exec($ch);
	//Result
	//echo $smsresult;
	//Error Display
	//echo curl_error($ch);
  }*/
    //===== Saart Save Sales ====
    function saveTempSales($directInvoice = false)
    {
        if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering
        $str = getRequest('str');
        $strArr = explode("####", $str);
        //======= Insert into tamp ========
        $requestdata = array();
        $requestdata = getUserDataSet(TEMP_SALES_ORDER_TBL);
        $project_id = getFromSession('project_id');
        $customer = getRequest('customer');

        $aging_invoice = $this->getOverdueInvoices($customer);

        if (!$aging_invoice['status']) {
            $tmp_id = getRequest('tmp_id');
            $requestdata['project_id'] = $project_id;
            $requestdata['customer'] = getRequest('customer');
            $requestdata['delivery_point'] = getRequest('delivery_point');
            $requestdata['sales_date'] = formatDate(getRequest('sales_date'));
            $requestdata['delivery_date'] = formatDate(getRequest('delivery_date'));
            $requestdata['currency'] = getRequest('currency');
            $requestdata['currencyName'] = getRequest('currencyName');
            $requestdata['productid'] = getRequest('productid');
            $sql = "SELECT product_name,catagory,brand_code,m_unit FROM " . PRODUCT_TBL . " WHERE product_id = '" . $requestdata['productid'] . "'";
            $row = mysql_fetch_object(mysql_query($sql));
            $requestdata['product_name'] = $row->product_name;
            $requestdata['catagory'] = $row->catagory;
            $requestdata['catagoryname'] = getRequest('catagoryname');
            $requestdata['brand_id'] = $row->brand_code;
            $requestdata['brandname'] = getRequest('brandname');
            $requestdata['details'] = getRequest('details');
            $requestdata['munit'] = $row->m_unit;
            $requestdata['qty'] = getRequest('qty');
            $requestdata['free_qty'] = getRequest('free_qty');
            $requestdata['unit_price'] = getRequest('unit_price');
            $requestdata['unit_discount'] = getRequest('unit_discount');
            $requestdata['discount_amount'] = getRequest('discount_amount');
            if ($directInvoice) {
                $requestdata['direct_invoice'] = 1;
            }
            $requestdata['vat'] = getRequest('vat');
            $requestdata['vat_amount'] = getRequest('vat_amount');
            $requestdata['total'] = getRequest('total');

            $requestdata['created_by'] = getFromSession('userid');
            if ($tmp_id > 0) {
                $info = array();
                $info['table'] = TEMP_SALES_ORDER_TBL;
                $info['data'] = $requestdata;
                $info['where'] = "tmp_id ='" . $tmp_id . "'";
                //$info['debug']  	=  true;
                $res = update($info);
            } else {
                $info = array();
                $info['table'] = TEMP_SALES_ORDER_TBL;
                $info['data'] = $requestdata;
                //$info['debug']  	=  true;
                $res = insert($info);
            }
        }

        $str1 = "<table width='100%' align='center' bgcolor='#99CC66'>
		<tr style='color:#fff;' bgcolor='#00B000' height=28>
		  <td width='1%' nowrap><div align='left'>SL</div></td>
		  <td width='20%' nowrap><div align='left'>Product Name</div></td>";

        if (!$directInvoice) {
            $str1 .= "<td width='8%' nowrap><div align='left'>Catagory</div></td>
		     <td width='8%' nowrap><div align='left'>Brand</div></td>";
        }
        $str1 .= "<td width='8%' nowrap><div align='right'>Order Qty</div></td>
		  <td width='8%' nowrap><div align='right'>Free Qty</div></td>
		  <td width='8%' nowrap><div align='right'>Rate</div></td>
		  <td width='8%' nowrap><div align='center'>Discount %</div></td>";

        //if($directInvoice){
        $str1 .= "<td width='10%' nowrap><div align='left'>VAT</div></td>";
        //}

        $str1 .= "<td width='10%' nowrap><div align='right'>Amount</div></td>				  
		  <td width='12%' nowrap align='center'>Option</td>
		</tr>";
        $total_value = 0;
        $product_discount = 0;
        $sl = 1;
        $TotalQty = 0;
        $TotalFreeQty = 0;
        $direct_invoice = 0;
        if ($directInvoice) {
            $direct_invoice = 1;
        }
        $getSql = "SELECT * FROM " . TEMP_SALES_ORDER_TBL . " WHERE created_by = '" . getFromSession('userid') . "' AND direct_invoice='" . $direct_invoice . "' AND project_id='" . $project_id . "' AND customer ='" . $customer . "' ORDER BY `tmp_id` ASC";
        $gres = mysql_query($getSql);
        while ($row = mysql_fetch_array($gres)) {
            extract($row);
            $total_value += $total;
            $product_discount += $discount_amount;
            $TotalQty += $qty;
            $TotalFreeQty += $free_qty;

            $productName = $product_name;
            if ($directInvoice) {
                $productName = $product_name . $details;
            }

            $str2 .= "
		<tr style='color:#000000' bgcolor='#fff'>
		  <td width='1%' nowrap align='left'>$sl</td>
		  <td width='20%' nowrap align='left'>$productName</td>";
            if (!$directInvoice) {
                $str2 .= "<td width='8%' nowrap align='left'>$catagoryname</td>
		  <td width='8%' nowrap align='left'>$brandname</td>";
            }
            $str2 .= "<td width='8%' nowrap><div align='right'>$qty $munit</div></td>
		  <td width='8%' nowrap><div align='right'>$free_qty $munit</div></td>
		  <td width='8%' nowrap align='right'>$unit_price $currencyName</td>				  
		  <td width='8%' nowrap align='center'>$unit_discount %</td>";
            //if($directInvoice){
            $str2 .= "<td width='8%' nowrap align='center'>$vat_amount ($vat%)</td>";
            //}
            $str2 .= "<td width='10%' nowrap align='right'>$total</td>				  				  
		  <td width='12%' nowrap align='center'><div class='table-option' style='gap:2px;padding: 5px;'>
		  <a href=\"#\" onclick=\"ItemDelete($tmp_id)\"><img src=\"images/common/icons/delete.gif\"></a> &nbsp;
		  <a href=\"#\" onclick=\"ItemEdit($tmp_id)\"><img src=\"images/common/icons/edit.gif\"></a>
		  </div></td>
		</tr>";
            $sl++;
        }

        $colspan = 4;
        if ($directInvoice) {
            $colspan = 2;
        }

        $str3 = "
		<tr style='color:#000;' bgcolor='#CCCCCC' height=25>
		  <td colspan='$colspan' nowrap><div align='right'>Total </div></td>
		  <td nowrap align='right'>$TotalQty $munit</td>
		  <td nowrap align='right'>$TotalFreeQty $munit</td>
		  <td nowrap>&nbsp;</td>
		  <td nowrap>&nbsp;</td>";
        //if($directInvoice){
        $str3 .= "<td nowrap>&nbsp;</td>";
        //}
        $str3 .= "<td nowrap align='right'>$total_value $currencyName</td>
		  <td nowrap align='center'>&nbsp;</td>
		</tr>
		</table>";
        $customer_balance = $this->getCustomerBalance($customer);
        $customer_limit = $this->getCustomerSalesLimit($customer);
        $cellingType = $this->getCustomerCellingType($customer);
        if ($cellingType == "Cash") {
            $customer_limit = abs($customer_balance);
            $customer_balance = 0;
        }

        echo $str1 . $str2 . $str3 . "####-@@@@" . $total_value . "####-@@@@" . $product_discount . "####-@@@@" . $customer_balance . "####-@@@@" . $customer_limit . "####-@@@@" . json_encode($aging_invoice);
        exit();
    }

    function loadUndeliveredInvoice($customer_id)
    {
        $project_id = getFromSession('project_id');
        $sql = "SELECT m.*,DATE_FORMAT(m.sales_date,'%d %b %y' ) as sales_date FROM " . SALES_MASTER_TBL . " as m, " . SALES_DETAILS_TBL . " as s WHERE m.voucher_no = s.voucher_no AND s.

undelivery_qty >0 AND m.customer = '" . $customer_id . "' GROUP BY s.voucher_no";
        $gres = mysql_query($sql);
        $product_idname = "<option value=''>Select Undelivered Invoice</option>";
        while ($row = mysql_fetch_object($gres)) {
            if ($row->voucher_no != "") {
                $product_idname .= "<option value='" . $row->voucher_no . "'>" . $row->voucher_no . ", " . $row->sales_date . "</option>";
            }
        }

        $aging_invoice = $this->getOverdueInvoices($customer_id);

        $response = [
            "product_idname" => $product_idname,
            "aging_invoice" => $aging_invoice,
        ];
        echo json_encode($response);
    }

    function getOverdueInvoices($customer_id)
    {
        $today = date('Y-m-d');
        $agingDate = date('d-m-Y'); // current date

        $overdue_invoice = getFromSession('overdue_invoice');
        if (!$overdue_invoice) {
            return ["status" => false, "agingDate" => $agingDate, "data" => ""];
        }


        // 🔹 Step 1: Check sub_acc_head table
        $checkSql = "SELECT overdue_invoice,customer_type,credit_days FROM " . SUB_ACC_HEAD_TBL . " WHERE sub_id = '" . mysql_real_escape_string($customer_id) . "' LIMIT 1";

        $checkResult = mysql_query($checkSql);

        if (!$checkResult || mysql_num_rows($checkResult) == 0) {
            return ["status" => false, "agingDate" => $agingDate, "data" => ""];
        }

        $checkRow = mysql_fetch_object($checkResult);
        $creditDays = 0;

        if (isset($checkRow->credit_days) && $checkRow->credit_days > 0) {
            $credit_days = (int)$checkRow->credit_days;
            $customer_type = $checkRow->customer_type;
            if (isset($customer_type) && $customer_type == 'Credit') {
                $creditDays = $credit_days;
                $agingDate = date('d-m-Y', strtotime("+$credit_days days"));
            }
        }

        // 🔹 If overdue_invoice is NOT 1 → Stop here
        if ($checkRow->overdue_invoice != 1) {
            return ["status" => false, "agingDate" => $agingDate, "credit_days" => $creditDays, "data" => ""];
        }

        $sql = "SELECT m.*, 
            DATE_FORMAT(m.sales_date,'%d %b %y') as sales_date,
            DATE_FORMAT(m.aging_date,'%d %b %y') as aging_date
        FROM " . SALES_MASTER_TBL . " AS m
        WHERE m.customer = '" . $customer_id . "'
          AND m.aging_date IS NOT NULL
          AND m.aging_date > '0000-00-00'
          AND m.aging_date < '" . $today . "'
          AND m.due > 0
          AND EXISTS (
              SELECT 1 FROM " . SALES_DELIVERY_MASTER_TBL . " d
              WHERE d.voucher_no = m.voucher_no
          )
        ORDER BY m.aging_date ASC";

        $result = mysql_query($sql);

        $table = "";

        $status = false;

        if (mysql_num_rows($result) > 0) {
            $status = true;

            $table .= "<table border='1' cellpadding='5' cellspacing='0'>";
            $table .= "<tr>
                    <th>Invoice No</th>
                    <th>Sales Date</th>
                    <th>Aging Date</th>
                    <th>Total Amount</th>
                    <th>Due Amount</th>
                   </tr>";

            while ($row = mysql_fetch_object($result)) {

                $table .= "<tr>
                        <td>" . $row->voucher_no . "</td>
                        <td>" . $row->sales_date . "</td>
                        <td>" . $row->aging_date . "</td>
                        <td>" . $row->net_payble . "</td>
                        <td>" . $row->due . "</td>
                       </tr>";
            }

            $table .= "</table>";

        } else {
            $table = "<p>No overdue invoices found.</p>";
        }

        return ["status" => $status, "agingDate" => $agingDate, "credit_days" => $creditDays, "data" => $table];
    }


    function getOverdueBillInvoices($customer_id)
    {
        $today = date('Y-m-d');
        $agingDate = date('d-m-Y'); // current date

        $overdue_bill = getFromSession('overdue_bill');
        if (!$overdue_bill) {
            return ["status" => false, "agingDate" => $agingDate, "data" => ""];
        }

        // 🔹 Step 1: Check sub_acc_head table
        $checkSql = "SELECT overdue_invoice,customer_type,credit_days FROM " . SUB_ACC_HEAD_TBL . " WHERE sub_id = '" . mysql_real_escape_string($customer_id) . "' LIMIT 1";

        $checkResult = mysql_query($checkSql);

        if (!$checkResult || mysql_num_rows($checkResult) == 0) {
            return ["status" => false, "agingDate" => $agingDate, "data" => ""];
        }

        $checkRow = mysql_fetch_object($checkResult);
        $creditDays = 0;

        if (isset($checkRow->credit_days) && $checkRow->credit_days > 0) {
            $credit_days = (int)$checkRow->credit_days;
            $customer_type = $checkRow->customer_type;
            if (isset($customer_type) && $customer_type == 'Credit') {
                $creditDays = $credit_days;
                $agingDate = date('d-m-Y', strtotime("+$credit_days days"));
            }
        }

        // 🔹 If overdue_invoice is NOT 1 → Stop here
        if ($checkRow->overdue_invoice != 1) {
            return ["status" => false, "agingDate" => $agingDate, "credit_days" => $creditDays, "data" => ""];
        }

        $sql = "SELECT 
            b.*,
            b.voucher_no AS bill_voucher,
            bi.invoice_no,
            m.voucher_no,
            m.due,
            d.voucher_no AS delivery_voucher,

            DATE_FORMAT(b.bill_date,'%d %b %y') as bill_date,
            DATE_FORMAT(b.aging_date,'%d %b %y') as aging_date

        FROM bill_invoices bi

        INNER JOIN bill b 
            ON b.bill_id = bi.bill_id

        LEFT JOIN " . SALES_MASTER_TBL . " m
            ON m.voucher_no = bi.invoice_no
            AND m.due > 0

        LEFT JOIN " . SALES_DELIVERY_MASTER_TBL . " d
            ON d.voucher_no = bi.invoice_no

        WHERE b.customer = '" . $customer_id . "'
          AND b.aging_date IS NOT NULL
          AND b.aging_date > '0000-00-00'
          AND b.aging_date < '" . $today . "'
          AND b.status = 1

        ORDER BY b.aging_date ASC";

        $sql = "SELECT 
	    b.*,
	    b.voucher_no AS bill_voucher,
	    bi.invoice_no,
	    m.voucher_no AS sales_voucher,
	    m.due AS sales_due,
	    d.voucher_no AS delivery_voucher,
	    DATE_FORMAT(b.bill_date,'%d %b %y') AS bill_date,
	    DATE_FORMAT(b.aging_date,'%d %b %y') AS aging_date

	FROM bill_invoices bi

	INNER JOIN bill b
	    ON b.bill_id = bi.bill_id
	    AND b.customer = '" . $customer_id . "'
	    AND b.aging_date IS NOT NULL
	    AND b.aging_date > '0000-00-00'
	    AND b.aging_date < '" . $today . "'
	    AND b.status = 1

	LEFT JOIN " . SALES_MASTER_TBL . " m
	    ON m.voucher_no = bi.invoice_no
	    AND m.due > 0

	LEFT JOIN " . SALES_DELIVERY_MASTER_TBL . " d
	    ON d.voucher_no = bi.invoice_no

	WHERE m.voucher_no IS NOT NULL
	  AND d.voucher_no IS NOT NULL

	ORDER BY b.aging_date ASC";


        $result = mysql_query($sql);

        $table = "";
        $status = false;

        if (mysql_num_rows($result) > 0) {
            $status = true;

            $table .= "<table border='1' cellpadding='5' cellspacing='0'>";
            $table .= "<tr>
                    <th>Invoice No</th>
                    <th>Voucher No</th>
                    <th>Bill Date</th>
                    <th>Aging Date</th>
                    <th>Total Amount</th>
                    <th>Due Amount</th>
                   </tr>";

            while ($row = mysql_fetch_object($result)) {

                $table .= "<tr>
                        <td>" . $row->bill_voucher . "</td>
                        <td>" . $row->invoice_no . "</td>
                        <td>" . $row->bill_date . "</td>
                        <td>" . $row->aging_date . "</td>
                        <td>" . number_format($row->net_payable, 2) . "</td>
                        <td>" . number_format($row->net_payable, 2) . "</td>
                       </tr>";
            }

            $table .= "</table>";

        } else {
            $table = "<p>No overdue invoices found.</p>";
        }


        return ["status" => $status, "agingDate" => $agingDate, "credit_days" => $creditDays, "data" => $table];
    }


    function loadUndeliverySales($directInvoice = false)
    {
        $str = getRequest('str');
        $strArr = explode("####", $str);
        $customer_id = getRequest('customer_id');
        $voucher_no = getRequest('voucher_no');

        $direct_invoice = 0;
        if ($directInvoice) {
            $direct_invoice = 1;
        }

        //======= Insert into tamp ========
        $sql = "SELECT sal_detail_id, s.product,p.product_name,p.product_desc,p.catagory,c.catagory_name,p.brand_code,b.brand_name,p.m_unit,s.currency,s.discount_per_qty,s.discount_amount,s.free_qty,s.undelivery_qty,s.unit_price  FROM " . PRODUCT_TBL . " as p, " . CATAGORY_TBL . " as c, " . BRAND_TBL . " as b, " . SALES_DETAILS_TBL . " as s WHERE s.product = p.product_id AND p.brand_code = b.brand_id AND p.catagory = c.catagory_code AND s.undelivery_qty >0 AND s.customer = '" . $customer_id . "' AND s.voucher_no = '" . $voucher_no . "' GROUP BY s.sal_detail_id";
        $gres = mysql_query($sql);
        while ($row = mysql_fetch_object($gres)) {
            $requestdata = array();
            $requestdata = getUserDataSet(TEMP_SALES_ORDER_TBL);
            $project_id = getFromSession('project_id');
            $requestdata['project_id'] = $project_id;
            $requestdata['customer'] = getRequest('customer_id');
            $requestdata['delivery_point'] = getRequest('delivery_point');
            $requestdata['sales_date'] = formatDate(getRequest('sales_date'));
            $requestdata['delivery_date'] = formatDate(getRequest('delivery_date'));

            $requestdata['currency'] = $row->currency;
            $requestdata['currencyName'] = "Tk";
            $requestdata['productid'] = $row->product;
            $requestdata['product_name'] = $row->product_name;
            $requestdata['catagory'] = $row->catagory;
            $requestdata['catagoryname'] = $row->catagory_name;
            $requestdata['brand_id'] = $row->brand_code;
            $requestdata['brandname'] = $row->brand_name;
            $requestdata['details'] = $row->product_desc;
            $requestdata['munit'] = $row->m_unit;
            $requestdata['qty'] = $row->undelivery_qty;
            $requestdata['free_qty'] = $row->free_qty;
            $requestdata['unit_price'] = $row->unit_price;
            $requestdata['unit_discount'] = $row->discount_per_qty;
            $requestdata['discount_amount'] = $row->discount_amount;
            $requestdata['total'] = ($row->undelivery_qty * $row->unit_price);
            $requestdata['und_wo_no'] = $voucher_no;
            $requestdata['direct_invoice'] = $direct_invoice;

            $requestdata['created_by'] = getFromSession('userid');
            $info = array();
            $info['table'] = TEMP_SALES_ORDER_TBL;
            $info['data'] = $requestdata;
            //$info['debug']  	=  true;
            $res = insert($info);
            $unsql = "UPDATE " . SALES_DETAILS_TBL . " SET undelivery_qty=0, prev_undelivery_qty='" . $row->undelivery_qty . "' WHERE undelivery_qty >0 AND customer = '" . $customer_id . "' AND voucher_no = '" . $voucher_no . "' AND sal_detail_id = '" . $row->sal_detail_id . "'";
            mysql_query($unsql);
        }

        $str1 = "<table width='100%' align='center' bgcolor='#99CC66'>
		<tr style='color:#fff;' bgcolor='#00B000' height=28>
		  <td width='1%' nowrap><div align='left'>SL</div></td>
		  <td width='30%' nowrap><div align='left'>Product Name</div></td>
		  <td width='10%' nowrap><div align='left'>Catagory</div></td>
		  <td width='10%' nowrap><div align='left'>Brand</div></td>
		  <td width='8%' nowrap><div align='right'>Order Qty</div></td>
		  <td width='8%' nowrap><div align='right'>Free Qty</div></td>
		  <td width='8%' nowrap><div align='right'>Rate</div></td>
		  <td width='8%' nowrap><div align='center'>Discount %</div></td>		  
		  <td width='10%' nowrap><div align='right'>Amount</div></td>				  
		  <td width='7%' nowrap align='center'>Option</td>
		</tr>";
        $undwono = "";
        $total_value = 0;
        $product_discount = 0;
        $sl = 1;
        $TotalQty = 0;
        $TotalFreeQty = 0;
        $getSql = "SELECT * FROM " . TEMP_SALES_ORDER_TBL . " WHERE created_by = '" . getFromSession('userid') . "' AND project_id='" . $project_id . "' AND direct_invoice='" . $direct_invoice . "' AND customer ='" . $customer_id . "' ORDER BY `tmp_id` ASC";
        $gres = mysql_query($getSql);
        while ($row = mysql_fetch_array($gres)) {
            extract($row);
            if (isset($und_wo_no) && $und_wo_no != "") {
                $undwono = $und_wo_no;
            }
            $total_value += $total;
            $product_discount += $discount_amount;
            $TotalQty += $qty;
            $TotalFreeQty += $free_qty;
            $str2 .= "
		<tr style='color:#000000' bgcolor='#fff'>
		  <td width='1%' nowrap align='left'>$sl</td>
		  <td width='30%' nowrap align='left'>$product_name</td>
		  <td width='10%' nowrap align='left'>$catagoryname</td>
		  <td width='10%' nowrap align='left'>$brandname</td>
		  <td width='8%' nowrap><div align='right'>$qty $munit</div></td>
		  <td width='8%' nowrap><div align='right'>$free_qty $munit</div></td>
		  <td width='8%' nowrap align='right'>$unit_price $currencyName</td>				  
		  <td width='8%' nowrap align='center'>$unit_discount %</td>			  
		  <td width='10%' nowrap align='right'>$total</td>				  				  
		  <td width='7%' nowrap align='center'>
		  <a href=\"#\" onclick=\"ItemDelete($tmp_id)\"><img src=\"images/common/icons/delete.gif\"></a> &nbsp;
		  <a href=\"#\" onclick=\"ItemEdit($tmp_id)\"><img src=\"images/common/icons/edit.gif\"></a>
		  </td>
		</tr>";
            $sl++;
        }
        $str3 = "
		<tr style='color:#000;' bgcolor='#CCCCCC' height=25>
		  <td colspan='4' nowrap><div align='right'>Total </div></td>
		  <td nowrap align='right'>$TotalQty $munit</td>
		  <td nowrap align='right'>$TotalFreeQty $munit</td>
		  <td nowrap>&nbsp;</td>
		  <td nowrap>&nbsp;</td>
		  <td nowrap align='right'>$total_value $currencyName</td>
		  <td nowrap align='center'>&nbsp;</td>
		</tr>
		</table>";

        $customer_balance = $this->getCustomerBalance($customer_id);
        $customer_limit = $this->getCustomerSalesLimit($customer_id);
        $cellingType = $this->getCustomerCellingType($customer_id);
        if ($cellingType == "Cash") {
            $customer_limit = abs($customer_balance);
            $customer_balance = 0;
        }
        $getSql = "SELECT general_discount_percent,general_discount_amount,exclusive_discount_percent,exclusive_discount_amount,additional_discount_percent,additional_discount FROM " . SALES_MASTER_TBL . " WHERE project_id='" . $project_id . "' AND customer = '" . $customer_id . "' AND voucher_no = '" . $voucher_no . "'";

        $gres = mysql_query($getSql);
        $row = mysql_fetch_object($gres);
        $general_discount_percent = $row->general_discount_percent;
        $general_discount_amount = $row->general_discount_amount;
        $exclusive_discount_percent = $row->exclusive_discount_percent;
        $exclusive_discount_amount = $row->exclusive_discount_amount;
        $additional_discount_percent = $row->additional_discount_percent;
        $additional_discount = $row->additional_discount;

        echo $str1 . $str2 . $str3 . "####-@@@@" . $total_value . "####-@@@@" . $product_discount . "####-@@@@" . $customer_balance . "####-@@@@" . $customer_limit . "####-@@@@" . $general_discount_percent . "####-@@@@" . $general_discount_amount . "####-@@@@" . $exclusive_discount_percent . "####-@@@@" . $exclusive_discount_amount . "####-@@@@" . $additional_discount_percent . "####-@@@@" . $additional_discount . "####-@@@@" . $undwono;

    }

    function delTempSales()
    {
        $tmp_id = $_REQUEST['id'];
        if ($tmp_id != "") {
            $dsql = "DELETE FROM " . TEMP_SALES_ORDER_TBL . " WHERE tmp_id ='" . $tmp_id . "'";
            mysql_query($dsql);
        }
        echo $this->getTempSales();
    }

    function getCustomerBalance($customer)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comlistApp = new CommonList();
        $project_id = getFromSession('project_id');
        $NewSalesAmount = 0;
        if ($customer != "") {
            $PreviousPartyBalance = $comlistApp->getAccounceBalance($customer, $project_id);
            $PartyBalance = ($PreviousPartyBalance + $NewSalesAmount);
            return $PartyBalance;
        } else {
            return 0;
        }
    }

    function getCustomerSalesLimit($customer)
    {
        $head_type = getHeadType($customer);
        $project_id = getFromSession('project_id');
        if ($head_type == "Customer") {
            $getSql = "SELECT ceilling_amount FROM " . SUB_ACC_HEAD_TBL . " WHERE project_id='" . $project_id . "'
		 AND sub_id ='" . $customer . "' ";
        } else {
            $getSql = "SELECT ceilling_amount FROM " . SUB_ACC_HEAD_TBL . " WHERE project_id='" . $project_id . "' 
		 AND sub_id ='" . $customer . "' ";
        }
        $gres = mysql_query($getSql);
        $ceilling_amount = 0;
        $row = mysql_fetch_object($gres);
        $ceilling_amount = $row->ceilling_amount;
        if ($ceilling_amount >= 0) {
            return $ceilling_amount;
        } else {
            return 0;
        }
    }

    function getCustomerCellingType($customer)
    {
        $head_type = getHeadType($customer);
        $project_id = getFromSession('project_id');

        if ($head_type == "Customer") {
            $getSql = "SELECT ceilling_amount FROM " . SUB_ACC_HEAD_TBL . " WHERE project_id='" . $project_id . "'
		 AND sub_id ='" . $customer . "' ";
        } else {
            $getSql = "SELECT ceilling_amount FROM " . SUB_ACC_HEAD_TBL . " WHERE project_id='" . $project_id . "' 
		 AND sub_id ='" . $customer . "' ";
        }
        $gres = mysql_query($getSql);
        $ceilling_amount = 0;
        $row = mysql_fetch_object($gres);
        $ceilling_amount = $row->ceilling_amount;
        if ($ceilling_amount > 0) {
            return "Credit";
        } else {
            return "Cash";
        }
    }

    function getTempSales($directInvoice = false)
    {
        $project_id = getFromSession('project_id');
        $customer = getRequest('customer');

        $direct_invoice = 0;
        if ($directInvoice) {
            $direct_invoice = 1;
        }

        $str1 = "<table width='100%' align='center' bgcolor='#99CC66'>
		<tr style='color:#fff;' bgcolor='#00B000' height=28>
		  <td width='1%' nowrap><div align='left'>SL</div></td>
		  <td width='20%' nowrap><div align='left'>Product Name</div></td>";
        if (!$directInvoice) {
            $str1 .= "<td width='8%' nowrap><div align='left'>Catagory</div></td>
		  <td width='8%' nowrap><div align='left'>Brand</div></td>";
        }

        $str1 .= "<td width='8%' nowrap><div align='right'>Order Qty</div></td>
		  <td width='8%' nowrap><div align='right'>Free Qty</div></td>
		  <td width='8%' nowrap><div align='right'>Rate</div></td>
		  <td width='8%' nowrap><div align='center'>Discount %</div></td>";
        //if($directInvoice){
        $str1 .= "<td width='10%' nowrap><div align='left'>VAT %</div></td>";
        //}
        $str1 .= "<td width='10%' nowrap><div align='right'>Amount</div></td>				  
		  <td width='12%' nowrap align='center'>Option</td>
		</tr>";

        $tempCustomer = "";
        $undwono = "";
        $total_value = 0;
        $product_discount = 0;
        $TotalQty = 0;
        $TotalFreeQty = 0;
        $sl = 1;
        $getSql = "SELECT * FROM " . TEMP_SALES_ORDER_TBL . " WHERE created_by = '" . getFromSession('userid') . "' AND project_id='" . $project_id . "'";
        $getSql .= " AND direct_invoice ='" . $direct_invoice . "'";
        if ($customer != "") {
            $getSql .= " AND customer ='" . $customer . "'";
        }
        $getSql .= " ORDER BY `tmp_id` ASC";

        $gres = mysql_query($getSql);

        while ($row = mysql_fetch_array($gres)) {
            extract($row);

            if (empty($tempCustomer)) {
                $tempCustomer = $customer;
            };

            if (isset($und_wo_no) && $und_wo_no != "") {
                $undwono = $und_wo_no;
            }
            $total_value += $total;
            $product_discount += $discount_amount;
            $TotalQty += $qty;
            $TotalFreeQty += $free_qty;

            $productName = $product_name;
            if ($directInvoice) {
                $productName = $product_name . $details;
            }

            $str2 .= "
		<tr style='color:#000000' bgcolor='#fff'>
		  <td width='1%' nowrap>$sl</td>
		  <td width='16%' nowrap align='left'>$productName</td>";
            if (!$directInvoice) {
                $str2 .= "<td width='8%' nowrap align='left'>$catagoryname</td>
		  <td width='8%' nowrap align='left'>$brandname</td>";
            }
            $str2 .= "<td width='8%' nowrap><div align='right'>$qty $munit</div></td>
		  <td width='8%' nowrap><div align='right'>$free_qty $munit</div></td>
		  <td width='8%' nowrap><div align='right'>$unit_price $currencyName</div></td>				  
		  <td width='8%' nowrap><div align='center'>$unit_discount %</div></td>";
            //if($directInvoice){
            $str2 .= "<td width='10%' nowrap align='left'>$vat_amount ($vat%)</td>";
            //}
            $str2 .= "<td width='10%' nowrap><div align='right'>$total</div></td>				  				  
		  <td width='12%' nowrap align='center'><div class='table-option' style='gap:2px;padding: 5px;'>
		  <a href=\"#\" onclick=\"ItemDelete($tmp_id)\"><img src=\"images/common/icons/delete.gif\"></a> &nbsp;
		  <a href=\"#\" onclick=\"ItemEdit($tmp_id)\"><img src=\"images/common/icons/edit.gif\"></a>
		  </div></td>
		</tr>";
            $sl++;
        }


        $colspan = 4;
        if ($directInvoice) {
            $colspan = 2;
        }
        $str3 = "
		<tr style='color:#000;' bgcolor='#CCCCCC' height=25>
		  <td colspan='$colspan' nowrap><div align='right'>Total </div></td>
		  <td nowrap align='right'>$TotalQty $munit</td>
		  <td nowrap align='right'>$TotalFreeQty $munit</td>
		  <td nowrap>&nbsp;</td>
		  <td nowrap>&nbsp;</td>";
        //if($directInvoice){
        $str3 .= "<td nowrap>&nbsp;</td>";
        //}
        $str3 .= "<td nowrap align='right'>$total_value $currencyName </td>
		  <td nowrap align='center'>&nbsp;</td>
		</tr>
		</table>";

        $customer_balance = $this->getCustomerBalance($customer);
        $customer_limit = $this->getCustomerSalesLimit($customer);
        $cellingType = $this->getCustomerCellingType($customer);
        if ($cellingType == "Cash") {
            $customer_limit = abs($customer_balance);
            $customer_balance = 0;
        }

        $aging_invoice = $this->getOverdueInvoices($tempCustomer);

        $total_salesStr = $str1 . $str2 . $str3 . "####-@@@@" . $total_value . "####-@@@@" . $product_discount . "####-@@@@" . $customer_balance . "####-@@@@" . $customer_limit . "####-@@@@" . $undwono . "####-@@@@" . json_encode($aging_invoice);

        return $total_salesStr;
    }

    //====== End Save Sales =====
    function insertSalesDetails($voucher_no)
    {
        $requestdata = array();
        $arr_catagory_product_id = array();
        $project_id = getFromSession('project_id');
        $currency = getRequest('currency');
        $customer = getRequest('customer');

        $total_p_weight = 0.00;

        $direct_invoice = false;
        $directInvoice = 0;
        $invoice_type = getRequest('invoice_type');
        if (isset($invoice_type) && $invoice_type == "direct_invoice") {
            $direct_invoice = true;
            $directInvoice = 1;
        }

        $getSql = "SELECT * FROM " . TEMP_SALES_ORDER_TBL . " WHERE created_by = '" . getFromSession('userid') . "' AND project_id='" . getFromSession('project_id') . "' AND direct_invoice = '" . $directInvoice . "' AND customer = '" . $customer . "' ORDER BY `tmp_id` ASC";
        $gres = mysql_query($getSql);
        if (mysql_num_rows($gres) > 0) {
            while ($row = mysql_fetch_object($gres)) {
                $requestdata['project_id'] = $project_id;
                $requestdata['catagory'] = $row->catagory;
                $requestdata['brand_id'] = $row->brand_id;
                $brand_id = $row->brand_id;
                $requestdata['product'] = $row->productid;
                $product_id = $row->productid;
                $requestdata['discount_per_qty'] = $row->unit_discount;
                $requestdata['m_unit'] = $row->catagory;
                $requestdata['details'] = $row->details;
                $requestdata['serial'] = 0;
                $serial = 0;
                $warranty = 0;
                $requestdata['unit_price'] = $row->unit_price;
                $requestdata['qty'] = $row->qty;
                $requestdata['free_qty'] = $row->free_qty;
                $requestdata['m_unit'] = $row->munit;
                $requestdata['total'] = $row->total;
                $requestdata['catagory'] = $row->catagory;

                $vat = 0;
                $vat_amount = 0;
                //if ($direct_invoice) {
                $vat = $row->vat;
                $vat_amount = $row->vat_amount;
                //}

                $requestdata['vat'] = $vat;
                $requestdata['vat_amount'] = $vat_amount;


                $requestdata['discount_amount'] = (($requestdata['unit_price'] / 100) * $requestdata['discount_per_qty']);
                $requestdata['created_by'] = getFromSession('userid');
                $requestdata['created_date'] = date('Y-m-d h:i:s');
                $project_id = getFromSession('project_id');
                $requestdata['project_id'] = $project_id;
                if (getRequest('wo_no') != "") {
                    $requestdata['wo_no'] = getRequest('wo_no');
                } else {
                    $requestdata['wo_no'] = $voucher_no;
                }
                $requestdata['voucher_no'] = $voucher_no;
                $requestdata['lc_no'] = getRequest('lc_no');
                $requestdata['customer'] = getRequest('customer');
                $requestdata['reference'] = getRequest('reference');
                $customer = getRequest('customer');
                $CSql = "SELECT division,district,area FROM " . SUB_ACC_HEAD_TBL . " WHERE sub_id='$customer' AND project_id='$project_id' ";
                $Crow = mysql_fetch_object(mysql_query($CSql));
                $requestdata['division'] = $Crow->division;
                $requestdata['district'] = $Crow->district;
                $requestdata['area'] = $Crow->area;


                $Prosql = "SELECT product_catagory,weight FROM " . PRODUCT_TBL . " WHERE product_id = '$product_id' AND project_id = '$project_id'";
                $Prorow = mysql_fetch_object(mysql_query($Prosql));
                $product_catagory = $Prorow->product_catagory;

                $pWeight = isset($Prorow->weight) ? $Prorow->weight : 0;
                $productQty = (float)$row->qty;
                $product_Weight = (float)$pWeight * $productQty;
                $total_p_weight += $product_Weight;

                if (empty($product_Weight)) {
                    $product_Weight = 0.00;
                }
                $requestdata['product_weight'] = $product_Weight;

                if ($product_catagory == "Serial") {
                    $pq = 1;
                    while ($pq <= $productQty) {
                        $requestdata['qty'] = 1;
                        $requestdata['total'] = ($requestdata['unit_price'] * 1);
                        $requestdata['delivery_qty'] = 0;
                        $requestdata['serial'] = $pq;
                        $info = array();
                        $info['table'] = SALES_DETAILS_TBL;
                        $info['data'] = $requestdata;
                        //$info['debug']  	=  true;
                        $res = insert($info);

                        $info['table'] = SALES_DETAILS_APP_TBL;
                        insert($info);


                        $pq++;
                    }
                } else {
                    $info = array();
                    $info['table'] = SALES_DETAILS_TBL;
                    $info['data'] = $requestdata;
                    //$info['debug']  	=  true;
                    $res = insert($info);

                    $info['table'] = SALES_DETAILS_APP_TBL;
                    insert($info);
                }
            }// end while

            $SMSQL = "UPDATE " . SALES_MASTER_TBL . " SET total_p_weight='$total_p_weight' WHERE voucher_no='$voucher_no'";
            mysql_query($SMSQL);

            $SMSQL1 = "UPDATE " . SALES_DETAILS_APP_TBL . " SET total_p_weight='$total_p_weight' WHERE voucher_no='$voucher_no'";
            mysql_query($SMSQL1);
        }// end if
        if ($res) {
            $dsql = "DELETE FROM " . TEMP_SALES_ORDER_TBL . " WHERE created_by = '" . getFromSession('userid') . "' AND direct_invoice = '" . $directInvoice . "' AND project_id='" . getFromSession('project_id') . "'";
            mysql_query($dsql);
        }
    } //End of the function insertSalesDetails()

    //==================== saveDebitVouchar ====================
    function saveDebitVouchar()
    {
        $mode_of_payment = getRequest('mode_of_payment');
        $requestdata = array();
        $requestdata = getUserDataSet(DEVIT_VOUCHAR_TBL);
        if ($mode_of_payment == "Recievable") {
            //======= Party Dr ======
            $requestdata['account_head'] = getRequest('customer');
            $requestdata['debit'] = getRequest('net_payble');
            $requestdata['credit'] = 0;
            $requestdata['paid_amount'] = 0;
            $requestdata['due'] = 0;
            $requestdata['head_type'] = "Customer";
        }
        $requestdata['transaction_type'] = "Sales Order";
        $requestdata['vouchar_type'] = "Sales Order";
        $requestdata['project_id'] = getFromSession('project_id');
        $requestdata['created_by'] = getFromSession('userid');
        $requestdata['created_date'] = formatDate(getRequest('sales_date')); //date('Y-m-d h:i:s');

        $voucher_no = $this->createVoucharID();

        if ($voucher_no != "" && $voucher_no != "SI9999999") {
            $requestdata['voucher_no'] = $voucher_no;
        } else {
            $msg = "ID overflow !!!";
            header("location:index.php?app=user_home&msg=$msg");
            exit;
        }
        $info = array();
        $info['table'] = DEVIT_VOUCHAR_TBL;
        $info['data'] = $requestdata;
        //$info['debug']  	=  true;
        $res = insert($info);
        if ($res['affected_rows']) {
            return $voucher_no;
        } else {
            return "";
        }
    }//EOFn

    function saveCreditVouchar($voucher_no)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comlistApp = new CommonList();

        $mode_of_payment = getRequest('mode_of_payment');
        $requestdata = array();
        $requestdata = getUserDataSet(CREDIT_VOUCHAR_TBL);
        if ($mode_of_payment == "Recievable") {
            //======= Party Dr ======
            $requestdata['account_head'] = $comlistApp->getRecievableId(getFromSession('project_id'));
            $requestdata['credit'] = getRequest('net_payble');
            $requestdata['debit'] = 0;
            $requestdata['head_type'] = "Acc";
        }
        $requestdata['transaction_type'] = "Sales Order";
        $requestdata['head_type'] = "Acc";
        $requestdata['project_id'] = getFromSession('project_id');
        $requestdata['created_by'] = getFromSession('userid');

        $requestdata['created_date'] = formatDate(getRequest('sales_date')); //date('Y-m-d h:i:s');
        $requestdata['voucher_no'] = $voucher_no;

        $info = array();
        $info['table'] = CREDIT_VOUCHAR_TBL;
        $info['data'] = $requestdata;
        //$info['debug']  	=  true;
        $res = insert($info);
        $created_date = $requestdata['created_date'];

        if ($res['affected_rows']) {
            return true;
        } else {
            return false;
        }

    }//EOFn

    function insertSalesMaster($voucher_no)
    {
        $requestdata = array();
        $project_id = getFromSession('project_id');
        $requestdata = getUserDataSet(SALES_MASTER_TBL);
        $requestdata['transaction_type'] = "Sales Order";
        $requestdata['sales_date'] = formatDate(getRequest('sales_date'));
        $requestdata['delivery_date'] = formatDate(getRequest('delivery_date'));
        $requestdata['aging_date'] = formatDate(getRequest('aging_date'));
        $requestdata['voucher_no'] = $voucher_no;
        $requestdata['und_wo_no'] = getRequest('und_wo_no') || NULL;
        if (getRequest('wo_no') != "") {
            $requestdata['wo_no'] = getRequest('wo_no');
        } else {
            $requestdata['wo_no'] = $voucher_no;
        }

        $requestdata['ref_voucher'] = $requestdata['wo_no'];

        $requestdata['net_payble'] = getRequest('net_payble');

        //======== Sales Commission ============
        if (getRequest('reference') != "") {
            $cSql = "SELECT * FROM " . COMMISSION_SLOT_TBL . " WHERE cid=1 AND project_id='$project_id'";
            $crow = mysql_fetch_object(mysql_query($cSql));
            if ($requestdata['net_payble'] <= $crow->slot_range1) {
                $commission_slot = $crow->slot1;
            } elseif ($requestdata['net_payble'] <= $crow->slot_range2) {
                $commission_slot = $crow->slot2;
            } elseif ($requestdata['net_payble'] <= $crow->slot_range3) {
                $commission_slot = $crow->slot3;
            } elseif ($requestdata['net_payble'] <= $crow->slot_range4) {
                $commission_slot = $crow->slot4;
            }
            $total_commission = (($requestdata['net_payble'] / 100) * $commission_slot);
            $commission_total_due = $total_commission;
            $requestdata['commission_slot'] = $commission_slot;
            $requestdata['total_commission'] = $total_commission;
            $requestdata['commission_total_due'] = $commission_total_due;
        }
        $customer = getRequest('customer');
        $project_id = getFromSession('project_id');
        $CSql = "SELECT division,district,area FROM " . SUB_ACC_HEAD_TBL . " WHERE sub_id='$customer' AND project_id='$project_id' ";
        $Crow = mysql_fetch_object(mysql_query($CSql));
        $requestdata['division'] = $Crow->division;
        $requestdata['district'] = $Crow->district;
        $requestdata['area'] = $Crow->area;
        $general_discount_amount = getRequest('general_discount_amount');
        $exclusive_discount_amount = getRequest('exclusive_discount_amount');
        $additional_discount = getRequest('additional_discount');
        $product_discount = getRequest('discount');
        $requestdata['total_value'] = getRequest('total_value'); // round(getRequest('total_value'), 0);
        $requestdata['product_discount'] = getRequest('discount');

        $additional_vat_percent = 0;
        $additional_vat_amount = 0;
        $additional_cost = getRequest('additional_cost');
        $invoice_type = getRequest('invoice_type');
        if (isset($invoice_type) && $invoice_type == "direct_invoice") {
            $requestdata['direct_invoice'] = 1;
        }

        $additional_vat_percent = getRequest('total_vat_percent');
        $additional_vat_amount = getRequest('total_vat_amount');
        $requestdata['vat_type'] = getRequest('vat_type');

        if (empty($additional_cost)) {
            $additional_cost = 0.00;
        }

        $requestdata['vehicle_weight'] = 0.00;
        $vehicle_id = getRequest('vehicle_id');

        $requestdata['vehicle_id'] = (isset($vehicle_id) && $vehicle_id !== '')
            ? (int)$vehicle_id
            : 'NULL';

        $vehicle_weight = getRequest('vehicle_weight');
        if (!empty($vehicle_weight) && $vehicle_weight > 0) {
            $requestdata['vehicle_weight'] = $vehicle_weight;
        }

        $requestdata['additional_vat_percent'] = $additional_vat_percent;
        $requestdata['additional_vat_amount'] = $additional_vat_amount;
        $requestdata['additional_cost'] = $additional_cost;

        $TotalDiscount = ($general_discount_amount + $exclusive_discount_amount + $additional_discount + $product_discount);
        $requestdata['discount'] = $TotalDiscount;

        $description = getRequest('description');
        $description = str_replace('"', "&ldquo;", $description);
        $description = str_replace("'", "&#8217;", $description);
        $requestdata['description'] = $description;
        $requestdata['project_id'] = getFromSession('project_id');
        $requestdata['created_by'] = getFromSession('userid');
        $requestdata['created_date'] = date('Y-m-d h:i:s');

        $status = 0; //0=Pending, 1=Approved
        $invoice_type = getRequest('invoice_type');
        if (isset($invoice_type) && $invoice_type == "direct_invoice") {
            $status = 1;
        }
        $requestdata['status'] = $status; //0=Pending, 1=Approved
        $info = array();
        $info['table'] = SALES_MASTER_TBL;
        $info['data'] = $requestdata;
        //$info['debug']  =  true;

        $res = insert($info);

        $info['table'] = SALES_MASTER_APP_TBL;
        insert($info);
    }

    function saveSalesItem()
    {
        mysql_query("START TRANSACTION;");

        $ceiling_status = getFromSession('ceiling_status');

        $customer_limit = getRequest('customer_limit');
        $net_payble = getRequest('net_payble');
        $customerbalance = getRequest('customerbalance');
        $invoice_type = getRequest('invoice_type');

        $customer = getRequest('customer');
        $aging_invoice = $this->getOverdueInvoices($customer);
        if ($aging_invoice['status']) {
            if (isset($invoice_type) && $invoice_type == "direct_invoice") {
                header("location:index.php?app=sales.order&cmd=direct_invoice&msg=Clear over due invoice!");
                exit();
            } else {
                header("location:index.php?app=sales.order&cmd=add&msg=Clear over due invoice!");
                exit();
            }
        }

        $customerbalance += $net_payble;

        $salesStatus = true;
        if ($ceiling_status) {
            $salesStatus = $customer_limit >= $customerbalance ? true : false;
        }

        if ($salesStatus) {
            $voucher_no = $this->saveDebitVouchar();

            if ($voucher_no != "") {
                $this->saveCreditVouchar($voucher_no);
                $this->insertSalesMaster($voucher_no);
                $this->insertSalesDetails($voucher_no);
                mysql_query("COMMIT;");

                if (isset($invoice_type) && $invoice_type == "direct_invoice") {
                    $delivery_master_id = $this->saveDeliveryChallanMaster($voucher_no);

                    if ($delivery_master_id > 0) {
                        $this->saveDeliveryChallanDetails($voucher_no, $delivery_master_id);
                    } else {
                        $SQL1 = "DELETE FROM " . SALES_DELIVERY_MASTER_TBL . " WHERE `voucher_no` ='" . getRequest("voucher_no") . "' AND sales_delivery_master_id=0";
                        mysql_query($SQL1);
                        $SQL2 = "UPDATE " . SALES_DETAILS_TBL . " SET delivery_qty='0' WHERE voucher_no='" . getRequest("voucher_no") . "'";
                        mysql_query($SQL2);
                        header("location:index.php?app=sales.order&cmd=direct_invoice&msg=Please Try again");
                    }
                }
                header("location:index.php?app=sales_order&cmd=print_vouchar&voucher_no=" . $voucher_no);
            } else {
                mysql_query("ROLLBACK;");
                header("location:index.php?app=sales.order&cmd=add");
            }
        } else {
            header("location:index.php?app=sales.order&cmd=add&msg=The customer sales ceilling amount is over");
        }
    }

    function make_seed()
    {
        list($usec, $sec) = explode(' ', microtime());
        return (float)$sec + ((float)$usec * 100000);
    }

    function saveDeliveryChallanMaster($voucher_no)
    {
        $requestdata = array();
        $requestdata = getUserDataSet(SALES_DELIVERY_MASTER_TBL);
        $requestdata['delivery_date'] = formatDate(getRequest('delivery_date'));
        $requestdata['total_value'] = getRequest('total_value');
        $requestdata['voucher_no'] = $voucher_no;
        $requestdata['project_id'] = getFromSession('project_id');
        $requestdata['created_by'] = getFromSession('userid');
        $requestdata['created_date'] = date('Y-m-d h:i:s');
        mt_srand($this->make_seed());
        $gatepass = mt_rand();
        //$requestdata['gate_pass']       = $gatepass;
        $info = array();
        $info['table'] = SALES_DELIVERY_MASTER_TBL;
        $info['data'] = $requestdata;
        //$info['debug']  =  true;
        $res = insert($info);

        if ($res['affected_rows']) {
            $delivery_master_id = mysql_insert_id();
            return $delivery_master_id;
        }
    }


    function saveDeliveryChallanDetails($voucher_no, $dm_id)
    {
        mysql_query("START TRANSACTION;");

        require_once(CLASS_DIR . '/common.list.class.php');
        $comlistApp = new CommonList();

        require_once(CLASS_DIR . '/sales.class.php');
        $salesApp = new Sales();

        require_once(CLASS_DIR . '/sales.delivery.class.php');
        $SalesDelivery = new SalesDelivery();

        $customer = getRequest('customer');
        $delivery_point = getRequest('delivery_point');
        $store_id = getRequest('delivery_point');

        $general_discount_amount = getRequest('general_discount_amount'); // string
        $exclusive_discount_amount = getRequest('exclusive_discount_amount');
        $additional_discount = getRequest('additional_discount');
        $product_discount = getRequest('discount');

        $discount = bcadd($general_discount_amount, $exclusive_discount_amount, 2);
        $discount = bcadd($discount, $additional_discount, 2);
        $discount = bcadd($discount, $product_discount, 2);

        $orderValue = getRequest('total_value');
        $overall_discount = (($discount / $orderValue) * 100);
        $consignee = "";
        $delivery_date = formatDate(getRequest('delivery_date'));
        $project_id = getFromSession('project_id');
        $created_by = getFromSession('userid');

        $CSql = "SELECT division,district,area FROM " . SUB_ACC_HEAD_TBL . " WHERE sub_id ='" . $customer . "' AND project_id = '$project_id'";
        $CRes = mysql_query($CSql);
        if (mysql_num_rows($CRes) > 0) {
            $crow = mysql_fetch_object($CRes);
            $division = $crow->division;
            $district = $crow->district;
            $area = $crow->area;
        } else {
            $CSql = "SELECT division,district,area FROM " . SUPPLIER_TBL . " WHERE supplier_code ='" . $customer . "' AND project_id = '$project_id'";
            $CRes = mysql_query($CSql);
            if (mysql_num_rows($CRes) > 0) {
                $crow = mysql_fetch_object($CRes);
                $division = $crow->division;
                $district = $crow->district;
                $area = $crow->area;
            }
        }

        $totalDeliveryAmount = 0;
        $totalDeliveryQty = 0;
        $totalOrderQty = 0;
        $totalStockDeliveryAmount = 0;
        $totalProfit = 0;
        $totalLoss = 0;
        $PDAmount = 0;
        $PDQty = 0;
        $ProductFreeQty = 0;
        $TotalStockAmount = 0;
        $TotalFreeDeliveryAmount = 0;
        $newVoucher = NULL;
        $detailsTotal = 0.00;
        $detailsProductTotal = 0.00;
        $sales_details = [];
        $productDetailsList = $salesApp->getProductList($voucher_no);

        foreach ($productDetailsList as $val) {
            $sales_details_id = $val->sal_detail_id;
            $catagory = $val->catagory;
            $brand_id = $val->brand_id;
            $product = $val->product;
            $m_unit = $val->m_unit;

            $pending_qty = ($val->qty - $val->delivery_qty);
            $stock_qty = getStoreStockQty($delivery_point, $val->product);
            if ($stock_qty >= ($pending_qty + $val->free_qty)) {
                $nowdelivery_qty = $pending_qty;
            } else {
                $nowdelivery_qty = $stock_qty;
            }

            $order_qty = $val->qty;
            $delivery_qty = $nowdelivery_qty;
            $undelivery_qty = $val->undelivery_qty;
            if ($undelivery_qty > 0) {
                if (!$newVoucher) {
                    $newVoucher = $SalesDelivery->createSalesVoucharID();
                }

                $detailsResult = $SalesDelivery->insertSalesItems($newVoucher, $voucher_no, $sales_details_id, $undelivery_qty);
                $detailsTotal += $detailsResult['total'];
                $detailsProductTotal += $detailsResult['product_discount'];
                $sales_details[] = $detailsResult['sales_details'];
            }
            $freeQty = $val->free_qty;
            $Prvfree_qty = $val->free_qty;
            $sales_price = $val->unit_price;
            $totalOrderQty += $order_qty;
            $discount_per_qty = ($overall_discount / $order_qty);
            $discount_amount = (float)$val->discount_amount;
            $UDQty = ($delivery_qty + $freeQty);
            $sd_id = $sales_details_id;

            if ($UDQty > 0) {
                $Pcsql = "SELECT product_type,product_catagory,purchase_unit_price FROM " . PRODUCT_TBL . " WHERE product_id='$product' AND project_id='$project_id'";
                $Pcrow = mysql_fetch_object(mysql_query($Pcsql));
                $product_catagory = $Pcrow->product_catagory;
                $purchase_price = $Pcrow->purchase_unit_price;
                $product_type = $Pcrow->product_type;
                $serial = 0;
                $warranty = 0;

                $PuSql = "SELECT balance AS stock_qty FROM " . STORE_STOCK_VIEW . " WHERE product_id='$product' AND project_id='$project_id' AND store_id ='$delivery_point'";
                $pres = mysql_query($PuSql);
                if (mysql_num_rows($pres) > 0) {
                    $Prow = mysql_fetch_object($pres);
                    $stockQty = $Prow->stock_qty;
                    if ($stockQty >= $UDQty) {
                        $unit_profit = ($sales_price - $purchase_price);
                        $overall_discount_amount = (($sales_price / 100) * $overall_discount);
                        $unit_profit = ($unit_profit - $overall_discount_amount);
                        $deliveryAmount = (($sales_price - $overall_discount_amount) * $delivery_qty);

                        if ($avgUnitprofit == 0) {
                            $avgUnitprofit = $unit_profit;
                        }

                        $totalDeliveryAmount += $deliveryAmount;
                        $PDAmount += $deliveryAmount;
                        $PDQty += $delivery_qty;
                        $ProductFreeQty += $freeQty;
                        $totalDeliveryQty += $delivery_qty;

                        $NetSalesQty = 0;
                        $NetLoss = 0;
                        $NetSalesQty = ($delivery_qty - $freeQty);

                        if ($unit_profit > 0 && $NetSalesQty > 0) {
                            $totalProfit += ($NetSalesQty * $unit_profit);
                        } elseif ($unit_profit < 0 && $NetSalesQty > 0) {
                            $NetLoss = ($NetSalesQty * $unit_profit);
                            $totalLoss += abs($NetLoss);
                        }

                        //=== update stock ===
                        $totalCR = $SalesDelivery->getTotalCreditStock($product, getFromSession('project_id'));
                        $totalDR = $SalesDelivery->getTotalDebitStock($product, getFromSession('project_id'));
                        $balance = ($totalDR - ($totalCR + $UDQty));
                        if ($product_type == "Sales Item" || $product_type == "Raw Materials" || $product_type == "Invetory Item" || $product_type == "Equipment") {
                            if ($overall_discount_amount > 0) {
                                $netSalesPrice = ($sales_price - $overall_discount_amount);
                            } else {
                                $netSalesPrice = $sales_price;
                            }
                            $note = "Sales Delivery";
                            $SalesDelivery->saveStockJournal($voucher_no, $voucher_no, $project_id, $store_id, $product, $serial, $warranty, $note, $netSalesPrice, $m_unit, 0, $UDQty, $balance, $delivery_date, $dm_id);
                        }

                        //=== Stock Cr =====
                        $StockAmount = ($purchase_price * $UDQty);
                        if ($StockAmount > 0) {
                            $TotalStockAmount += $StockAmount;
                            $StockAmount = 0;
                        }
                        if ($freeQty > 0) {
                            $FreeDeliveryAmount = ($purchase_price * $freeQty);
                            $TotalFreeDeliveryAmount += $FreeDeliveryAmount;
                            $FreeDeliveryAmount = 0;
                        }
                    }

                    // === Save Sales Delivery Qty ===
                    $SalesDelivery->saveDeliveryChallanDtl($sd_id, $dm_id, $voucher_no, $delivery_point, $consignee, $project_id, $catagory, $brand_id, $product, $serial, $warranty, $m_unit, $sales_price, $discount_per_qty, $discount_amount, $overall_discount, $overall_discount_amount, $unit_profit, $delivery_qty, $freeQty, $deliveryAmount, $division, $district, $area, $created_by);
                    $SalesDelivery->saveSalesItems($voucher_no, $product, $purchase_price, $unit_profit, $delivery_qty, $freeQty, $sales_details_id);
                    $delivery_qty = 0;
                    $freeQty = 0;
                    $UDQty = 0;
                    $PDQty = 0;
                    $avgpo_price = 0;
                    $ProductFreeQty = 0;

                }// end stock num

            }// UDQty qty > 0

        }// end for


        if ($newVoucher) {
            $SalesDelivery->salesMaster($newVoucher, $voucher_no, $detailsTotal, $detailsProductTotal, $sales_details);
            $newVoucher = NULL;
        }

        $totalDeliveryAmount = $SalesDelivery->getTotalDeliveryAmount($dm_id, $voucher_no, $project_id);

        $netOrderAmount = (float)getRequest('total_value');
        $epsilon = 0.00000;

        if ((abs($totalDeliveryAmount - $netOrderAmount) != $epsilon) && (intval($totalDeliveryQty) == intval($totalOrderQty))) {
            if (intval($totalDeliveryAmount) >= intval($netOrderAmount)) {
                $diffAmount = ($totalDeliveryAmount - $netOrderAmount);
            } else {
                $diffAmount = ($netOrderAmount - $totalDeliveryAmount);
            }
            $product_discount = ($discount + $diffAmount);
            $updateSM = "UPDATE " . SALES_MASTER_TBL . " SET product_discount='$product_discount' WHERE voucher_no='" . $voucher_no . "' AND project_id = '$project_id'";
            mysql_query($updateSM);
            $totalDeliveryAmount = $netOrderAmount;
        }


        if ((abs($totalDeliveryAmount - $netOrderAmount) == $epsilon) && (intval($totalDeliveryQty) == intval($totalOrderQty))) {
            $created_date = $delivery_date;
            //======= Party Dr ======
            $DrAmount1 = (float)$totalDeliveryAmount;
            $PartyAcc_head = $customer;
            $description = "Sales Delivery";
            $totalPartyCR = $comlistApp->getTotalCreditAmount($PartyAcc_head, getFromSession('project_id'));
            $totalPartyDR = $comlistApp->getTotalDebitAmount($PartyAcc_head, getFromSession('project_id'));
            $PreviousPartyBalance = ($totalPartyDR - $totalPartyCR);
            $PartyBalance = (($totalPartyDR + $DrAmount1) - $totalPartyCR);
            $comlistApp->saveAccJournal($voucher_no, $PartyAcc_head, "Customer", "Buy Product", getFromSession('project_id'), $description, $DrAmount1, 0, $PartyBalance, 0, $created_date, $dm_id);

            if ($consignee != "") {
                $totalPartyCR = $comlistApp->getTotalCreditAmount($consignee, getFromSession('project_id'));
                $totalPartyDR = $comlistApp->getTotalDebitAmount($consignee, getFromSession('project_id'));
                $PreviousPartyBalance = ($totalPartyDR - $totalPartyCR);
                $PartyBalance = (($totalPartyDR + $DrAmount1) - $totalPartyCR);
                $SalesDelivery->saveRetailerJournal($voucher_no, $consignee, "Retailer", "Buy Product", getFromSession('project_id'), $description, $DrAmount1, 0, $PartyBalance, 0, $created_date, $dm_id);
            }
            //======= Start Cost Center Ledger ========
            $COGSId = $comlistApp->getCOGSAccounceId(getFromSession('project_id'));
            $description = "cost of goods sold";
            //======= AC Recievable Dr ======
            if ($COGSId) {
                $COGSAmount = $SalesDelivery->getAccounceBalance($COGSId, $project_id);
                $COGSBalance = ($COGSAmount + $TotalStockAmount);
                $comlistApp->saveAccJournal($voucher_no, $COGSId, "Cost Center", "Goods Sold", $project_id, $description, $TotalStockAmount, 0, $COGSBalance, 0, $created_date, $dm_id);
            }
            //======= AC Sales Cr ======
            $description = "Sales of goods";
            $ACSalesId = $comlistApp->getSalesHeadId(getFromSession('project_id'));
            $salesHeadID = $comlistApp->getStoreMapLedgerID($store_id, "sales");
            if ($salesHeadID) {
                $ACSalesId = $salesHeadID;
            }

            if ($ACSalesId) {
                $ACSalesAmount = $SalesDelivery->getAccounceBalance($ACSalesId, $project_id);
                $SalesBalance = ($ACSalesAmount - $DrAmount1);
                $comlistApp->saveAccJournal($voucher_no, $ACSalesId, "Sales", "Sales", $project_id, $description, 0, $DrAmount1, $SalesBalance, 0, $created_date, $dm_id);
            }

            //Vat Journal
            $CRvatAmount = getRequest('vatAmount');
            $VATonSalesID = $comlistApp->getStoreMapLedgerID($store_id, "vat");

            if ($CRvatAmount > 0 && $VATonSalesID) {
                $totalVatCR = $SalesDelivery->getTotalCreditAmount($VATonSalesID, getFromSession('project_id'));
                $totalVatDR = $SalesDelivery->getTotalDebitAmount($VATonSalesID, getFromSession('project_id'));
                $VATBalance = ($totalVatDR - ($totalVatCR + $CRvatAmount));
                $description = "VAT of goods sold";
                $comlistApp->saveAccJournal($voucher_no, $VATonSalesID, "Sales", "Sales VAT", $project_id, $description, 0, $CRvatAmount, $VATBalance, 0, $created_date, $dm_id);
            }

            // ====== End Accounts Ledger ========

            //=======Update Sales Master =====

            $PMsql = "SELECT voucher_no,discount,net_payble,paid_amount,due,item_delivery_amount,service_charge FROM " . SALES_MASTER_TBL . " WHERE voucher_no ='" . $voucher_no . "' AND project_id = '$project_id'";
            $PMrow = mysql_fetch_object(mysql_query($PMsql));
            $sales_discount = $PMrow->discount;
            $total_received_amount = $PMrow->paid_amount;
            $existing_due = (float)$PMrow->due;
            $item_delivery_amount = (float)$PMrow->item_delivery_amount;
            $total_delivery_amount = ($totalDeliveryAmount + $item_delivery_amount);

            if ($PreviousPartyBalance < 0) {
                $actual_delivery_amount = $SalesDelivery->adjustCustomerPayble($voucher_no, $customer, $totalDeliveryAmount, $dm_id);
                $adjustAmount = ($totalDeliveryAmount - $actual_delivery_amount);
                $adjustAmount = "-$adjustAmount";
                $present_due = ($existing_due + $actual_delivery_amount);
            } else {
                $actual_delivery_amount = $totalDeliveryAmount;
                $adjustAmount = $PreviousPartyBalance;
                $present_due = ($existing_due + $actual_delivery_amount);
            }
            $SDMUpSQL = "UPDATE " . SALES_DELIVERY_MASTER_TBL . " SET total_value='$totalDeliveryAmount',previour_balance='$PreviousPartyBalance',roa='$actual_delivery_amount' WHERE voucher_no='" . $voucher_no . "' AND project_id = '$project_id' AND sales_delivery_master_id='$dm_id'";
            mysql_query($SDMUpSQL);

            $SMUpdate = "UPDATE " . SALES_MASTER_TBL . " SET net_payble='$total_delivery_amount',due='$present_due',item_delivery_amount='$total_delivery_amount',adjust='$adjustAmount',is_deleted=0 WHERE voucher_no='" . $voucher_no . "' AND project_id = '$project_id'";
            mysql_query($SMUpdate);
            if ($sales_discount > 0) {
                $description = "All Discount";
                $TotalDiscount = $sales_discount;
                //========= Sales Discount Dr =========
                $DiscountId = $comlistApp->getSalesDiscountId($project_id);
                $discountHeadID = $comlistApp->getStoreMapLedgerID($store_id, "discount");
                if ($discountHeadID) {
                    $DiscountId = $discountHeadID;
                }
                if ($DiscountId) {
                    $description = "Give discount with sales";
                    $DiscountCR = $SalesDelivery->getTotalCreditAmount($DiscountId, $project_id);
                    $DiscountDR = $SalesDelivery->getTotalDebitAmount($DiscountId, $project_id);
                    $DiscountBalance = (($DiscountDR + $TotalDiscount) - $DiscountCR);
                    $comlistApp->saveAccJournal($voucher_no, $DiscountId, "Sales", "Sales Discount", $project_id, $description, $TotalDiscount, 0, $DiscountBalance, 0, $delivery_date, $dm_id);
                }

            }
            $SalesDelivery->updateSalesVoucher($voucher_no, $total_delivery_amount);

            //$SalesDelivery->saveLoss($voucher_no,$dm_id,$totalLoss,$created_date);
            //$SalesDelivery->saveProfit($voucher_no,$dm_id,$totalProfit,$created_date);

            //==== Stock Cr =====
            if ($TotalStockAmount > 0) {
                $StockId = $comlistApp->getFGStockId(getFromSession('project_id'));
                if ($StockId) {
                    $totalStockCr = $SalesDelivery->getTotalCreditAmount($StockId, getFromSession('project_id'));
                    $totalStockDr = $SalesDelivery->getTotalDebitAmount($StockId, getFromSession('project_id'));
                    $StockBalance = ($totalStockDr - ($totalStockCr + $TotalStockAmount));
                    $description = "Sales delivery challan";
                    $comlistApp->saveAccJournal($voucher_no, $StockId, "Stock", "Sales Product", $project_id, $description, 0, $TotalStockAmount, $StockBalance, 0, $delivery_date, $dm_id);
                }
            }

            if ($TotalFreeDeliveryAmount > 0) {
                $description = "Give free product with sales";
                //========= Free Product Cost Dr ==========
                $freeItemhead = $comlistApp->getSalesDiscountId($project_id);
                $discountHeadID = $comlistApp->getStoreMapLedgerID($store_id, "discount");
                if ($discountHeadID) {
                    $freeItemhead = $discountHeadID;
                }

                if ($freeItemhead) {
                    $totalfreeItemCR = $SalesDelivery->getTotalCreditAmount($freeItemhead, $project_id);
                    $totalfreeItemDR = $SalesDelivery->getTotalDebitAmount($freeItemhead, $project_id);
                    $freeItemBalance = (($totalfreeItemDR + $TotalFreeDeliveryAmount) - $totalfreeItemCR);
                    $comlistApp->saveAccJournal($voucher_no, $freeItemhead, "Acc", "Free Product", $project_id, $description, $TotalFreeDeliveryAmount, 0, $freeItemBalance, 0, $created_date);
                }
            }
            $UpCSQL = "UPDATE " . SUB_ACC_HEAD_TBL . " SET status='1' WHERE sub_id ='" . $customer . "' AND project_id = '$project_id'";
            mysql_query($UpCSQL);

            $Csql = "SELECT mobile,att_mobile1,sub_head_name FROM " . SUB_ACC_HEAD_TBL . " WHERE sub_id ='" . $customer . "' AND project_id = '$project_id'";
            $Crow = mysql_fetch_object(mysql_query($Csql));
            if (trim($Crow->mobile) != "" && trim($Crow->att_mobile1) != "") {
                $recipients = $Crow->mobile . "," . $Crow->att_mobile1;
            } elseif (trim($Crow->mobile) != "" && trim($Crow->att_mobile1) == "") {
                $recipients = $Crow->mobile;
            } elseif (trim($Crow->mobile) == "" && trim($Crow->att_mobile1) != "") {
                $recipients = $Crow->att_mobile1;
            } else {
                $recipients = "";
            }
            if ($recipients != "") {
                if ($PartyBalance > 0) {
                    $LastPartyBalance = $PartyBalance . " Dr";
                } else {
                    $LastPartyBalance = abs($PartyBalance) . " Cr";
                }
                if (getRequest('sms_text') == "") {
                    $sms_text = "Dear Sir, your delivered Invoice No " . $voucher_no . " & amount is " . $totalDeliveryAmount . " TK. Party code is " . $Crow->sub_head_name . ". (" . COMPANY_NAME . ")";
                } else {
                    $sms_text = "Dear Sir, Your Sales Invoice No " . $voucher_no . " & amount is " . $totalDeliveryAmount . " TK. and " . getRequest('sms_text') . " (" . COMPANY_NAME . ")";
                }
                //$this->sendSMS(COMPANY_NAME,$recipients,$sms_text);
                require_once(CLASS_DIR . '/common.list.class.php');

                $numbers = explode(",", $recipients);
                foreach ($numbers as $recipients) {
                    if ($recipients != "") {
                        $response = (new CommonList())->sendSMS($recipients, $sms_text);
                    }
                }
            }

            mysql_query("COMMIT;");
            header("location:index.php?app=delivery_challan&cmd=print_vouchar&voucher_no=" . $voucher_no . "&sdm_id=" . $dm_id);
            exit();
        } elseif (intval($totalDeliveryAmount) != intval($netOrderAmount)) {

            //=======Update Sales Master =====
            mysql_query("ROLLBACK;");
            $Stsql = "DELETE FROM " . STOCK_LEDGER_TBL . " WHERE voucher_no='" . $voucher_no . "' AND project_id='" . $project_id . "' AND delivery_id='$dm_id'";
            mysql_query($Stsql);
            $Sditsql = "DELETE FROM " . SALES_DELIVERY_CHALLAN_TBL . " WHERE voucher_no='" . $voucher_no . "' AND project_id='" . $project_id . "' AND delivery_master_id='$dm_id'";
            mysql_query($Sditsql);
            $Sdmtsql = "DELETE FROM " . SALES_DELIVERY_MASTER_TBL . " WHERE voucher_no='" . $voucher_no . "' AND project_id='" . $project_id . "' AND sales_delivery_master_id='$dm_id'";
            mysql_query($Sdmtsql);

            // rollback this insert
            $alesDetailsSQL = "DELETE FROM " . SALES_DETAILS_TBL . " WHERE voucher_no='" . $voucher_no . "' AND project_id='" . $project_id . "'";
            mysql_query($alesDetailsSQL);

            $alesMasterSQL = "DELETE FROM " . SALES_MASTER_TBL . " WHERE voucher_no='" . $voucher_no . "' AND project_id='" . $project_id . "'";
            mysql_query($alesMasterSQL);

            $creditVoucharSQL = "DELETE FROM " . CREDIT_VOUCHAR_TBL . " WHERE voucher_no='" . $voucher_no . "' AND project_id='" . $project_id . "'";
            mysql_query($creditVoucharSQL);

            $deditVoucharSQL = "DELETE FROM " . DEVIT_VOUCHAR_TBL . " WHERE voucher_no='" . $voucher_no . "' AND project_id='" . $project_id . "'";
            mysql_query($deditVoucharSQL);

            header("location:index.php?app=sales.order&cmd=direct_invoice&msg=Delivery Amount($totalDeliveryAmount) is not equal to Order Amount($netOrderAmount) !!! Please Try again");
            exit();
        }

    }

    function saveAccountJournal($voucher_no, $sub_id, $head_type, $project_id, $description, $DR = NULL, $CR = NULL, $balance, $status = NULL, $purchare_date = NULL)
    {
        $rsql = "SELECT head_type FROM " . SUB_ACC_HEAD_TBL . " WHERE sub_id='" . $sub_id . "'";
        $rres = mysql_query($rsql);
        $hnum = mysql_num_rows($rres);
        if ($hnum > 0) {
            $hrow = mysql_fetch_object($rres);
            $head_type = $hrow->head_type;
        } else {
            $head_type = "Supplier";
        }
        $sql = "INSERT INTO " . ACCOUNT_JOURNAL_TBL . " (voucher_no,created_date,sub_id,head_type,project_id,description,dr,cr,balance,status) VALUES('" . $voucher_no . "','" . $purchare_date . "','" . $sub_id . "','" . $head_type . "','" . $project_id . "','" . $description . "','" . $DR . "','" . $CR . "','" . $balance . "','" . $status . "')";
        mysql_query($sql);
    }

    function getSalesMasterInfo($id)
    {

        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = SALES_MASTER_TBL . ' pm,' . SUB_ACC_HEAD_TBL . ' s,' . PROJECT_TBL . ' p,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('pm.voucher_no', 'pm.po_no', 'pm.wo_no', 'p.project_name', 'p.location', 's.sub_head_name', 's.head_details', 's.phone', 's.mobile', 's.email', 's.att_name1', 's.att_designation1', 's.att_mobile1', 'pm.reference', 'pm.gate_pass', 'pm.track_no', 'pm.salse_type', 'pm.total_value', "DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date", "DATE_FORMAT(pm.delivery_date,'%d %b %y' ) as delivery_date", "pm.delivery_date as deliveryDate", "pm.sales_date as salesDate", 'pm.mode_of_payment', 'c.curr_symble', 'pm.bank_name', 'pm.acc_no', 'pm.check_no', 'pm.check_no', 'pm.general_discount_percent', 'pm.general_discount_amount', 'pm.exclusive_discount_percent', 'pm.exclusive_discount_amount', 'pm.additional_discount_percent', 'pm.additional_discount', 'pm.product_discount', 'pm.discount', 'pm.service_charge', 'pm.net_payble', 'pm.item_delivery_amount', 'pm.paid_amount', 'pm.due', 'pm.ref_no', 'pm.created_date', 'pm.additional_cost', 'pm.vat_type', 'pm.direct_invoice', 'pm.additional_vat_percent', 'pm.additional_vat_amount', 'pm.ref_voucher');

        $sql = "pm.customer = s.sub_id AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '" . $project_id . "' AND pm.voucher_no = '$id'";

        $info['where'] = $sql;
        $info['groupby'] = array("pm.voucher_no");
        //$info['debug']  = true;
        $res = select($info);
        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        //dumpVar($data);
        return $data[0];
    }

    function getProductList($id)
    {
        $info = array();
        $info['table'] = SALES_DETAILS_TBL . ' sd,' . PRODUCT_TBL . ' p,' . CURRENCY_TBL . ' c,' . BRAND_TBL . ' b';
        $info['fields'] = array('sd.*', 'b.brand_name', 'p.product_name', 'p.product_desc', 'c.curr_symble');

        $sql = "sd.product = p.product_id AND p.brand_code = b.brand_id AND sd.currency = c.currency_id AND sd.voucher_no = '$id'";

        $info['where'] = $sql;
        $info['groupby'] = array("sd.sal_detail_id");
        $info['orderby'] = array("sd.product asc");
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

    function loadProduct4Catagory($catagory)
    {
        $brand_id = trim(getRequest('brand_id'));
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = PRODUCT_TBL;
        $info['fields'] = array('product_id', 'product_name', 'product_desc');
        $info['where'] = "`brand_code`='$brand_id' AND project_id = '$project_id'";
        $info['groupby'] = array("product_id");
        //$info['debug']   = true;
        $result = select($info);
        $data = array();
        if (count($result)) {
            foreach ($result as $key => $value) {
                $data[$key][] = $value;
            }
        }
        foreach ($data as $i => $v) {
            $subject_idname .= $v[0]->product_id . '#####' . $v[0]->product_code . '-' . $v[0]->product_name . '#####' . $v[0]->product_desc . '@@@';
        }
        echo $subject_idname;
    }

    function loadProductbyCatagory($catagory_id)
    {
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = PRODUCT_TBL;
        $info['fields'] = array('product_id', 'product_code', 'product_name', 'product_desc');
        if ($catagory_id != "") {
            $info['where'] = "`catagory`='$catagory_id' AND project_id = '$project_id'";
        }
        $info['groupby'] = array("product_id");
        //$info['debug']   = true;
        $result = select($info);
        $data = array();
        if (count($result)) {
            foreach ($result as $key => $value) {
                $data[$key][] = $value;
            }
        }
        $product_idname = "<option value=''>Select Product Name</option>";

        require_once(CLASS_DIR . '/common.list.class.php');
        foreach ($data as $i => $v) {
            $productName = (new CommonList())->normalizeProductName($v[0]->product_code, $v[0]->product_name);
            if ($v[0]->product_name != "" && $v[0]->product_desc != "") {
                $product_idname .= "<option value='" . $v[0]->product_id . "'>" . $productName . ", " . $v[0]->product_desc . "</option>";
            } elseif ($v[0]->product_name != "" && $v[0]->product_desc == "") {
                $product_idname .= "<option value='" . $v[0]->product_id . "'>" . $productName . "</option>";
            }
        }
        echo $product_idname;
    }

    function loadPartybyArea($area_id)
    {
        $division = getRequest('division');
        $district = getRequest('district');
        $area = getRequest('area');

        $project_id = getFromSession('project_id');
        $subject_idname = "<option value=''>Select Customer Name</option>";
        $info = array();
        $info['table'] = SUB_ACC_HEAD_TBL;
        $info['fields'] = array('sub_id', 'sub_head_name', 'mobile');
        $sql = "project_id = '$project_id'";
        if ($division != "") {
            $sql .= " AND `division`='$division'";
        }
        if ($district != "") {
            $sql .= " AND `district`='$district'";
        }
        if ($area != "") {
            $sql .= " AND `area`='$area'";
        }

        $info['where'] = $sql;
        $info['groupby'] = array("sub_id");
        //$info['debug']   = true;
        $result = select($info);
        $data = array();
        if (count($result)) {
            foreach ($result as $key => $value) {
                $data[$key][] = $value;
            }
        }

        foreach ($data as $i => $v) {
            $subject_idname .= "<option value='" . $v[0]->sub_id . "'>" . $v[0]->sub_head_name . ", " . $v[0]->mobile . "</option>";
        }

        $info = array();
        $info['table'] = SUPPLIER_TBL;
        $info['fields'] = array('supplier_code as sub_id', 'name as sub_head_name', 'mobile');
        $info['where'] = "`district`='$area_id' AND project_id = '$project_id'";
        $info['groupby'] = array("sub_id");
        //$info['debug']   = true;
        $result = select($info);
        $data = array();
        if (count($result)) {
            foreach ($result as $key => $value) {
                $data[$key][] = $value;
            }
        }

        foreach ($data as $i => $v) {
            $subject_idname .= "<option value='" . $v[0]->sub_id . "'>" . $v[0]->sub_head_name . ", " . $v[0]->mobile . "</option>";
        }
        echo $subject_idname;
    }

    function getTempDetails($tmp_id)
    {
        $sql = "SELECT * FROM " . TEMP_SALES_ORDER_TBL . " WHERE tmp_id = '" . $tmp_id . "' AND project_id='" . getFromSession('project_id') . "'";
        $row = mysql_fetch_object(mysql_query($sql));
        $str = $row->tmp_id . "#####" . $row->productid . "###" . $row->product_name . "#####" . $row->catagory . "###" . $row->catagoryname . "#####" . $row->brand_id . "###" . $row->brandname . "#####" . $row->details . "#####" . $row->munit . "#####" . $row->qty . "#####" . $row->free_qty . "#####" . $row->unit_price . "#####" . $row->unit_discount . "#####" . $row->discount_amount . "#####" . $row->total . "#####" . $row->vat . "#####" . $row->vat_amount;
        echo $str;
    }

    function loadProductDtl($product_id)
    {
        $project_id = getFromSession('project_id');

        $customer_id = trim(getRequest('customer_id'));

        $info = array();
        $info['table'] = PRODUCT_TBL . " p," . CATAGORY_TBL . ' c,' . BRAND_TBL . ' b';
        $info['fields'] = array('p.m_unit', 'p.product_desc', 'p.unit_price', 'p.unit_old_price', 'p.product_catagory', 'p.catagory', 'c.catagory_name', 'p.brand_code', 'b.brand_name');
        $info['where'] = "p.catagory =c.catagory_code AND p.brand_code =b.brand_id AND p.product_id = '$product_id'  AND p.project_id = '$project_id'";
        $info['groupby'] = array("p.product_id");
        //$info['debug']   = true;
        $result = select($info);
        $data = array();

        if (count($result)) {
            foreach ($result as $key => $value) {
                $data[$key][] = $value;
            }
        }

        $old_price_status = $this->getProductOldPriceStatus($customer_id);

        foreach ($data as $i => $v) {
            $unit_price = $v[0]->unit_price;

            if ($old_price_status) {
                $unit_price = $v[0]->unit_old_price;
            }

            $str = $v[0]->m_unit . "#####" . $v[0]->product_desc . "#####" . $unit_price . "#####" . $v[0]->catagory . "###" . $v[0]->catagory_name . "#####" . $v[0]->brand_code . "###" . $v[0]->brand_name;
        }
        echo $str;
    }

    function getProductOldPriceStatus($customer_id = "")
    {
        $project_id = getFromSession('project_id');
        if (empty($customer_id)) {
            return false;
        }
        $Csql = "SELECT p_old_price FROM " . SUB_ACC_HEAD_TBL . " WHERE sub_id ='" . $customer_id . "' AND project_id = '$project_id'";
        $Crow = mysql_fetch_object(mysql_query($Csql));
        if (isset($Crow->p_old_price) && $Crow->p_old_price == 1) {
            return true;
        }

        return false;
    }

    function getBankAccountList($purchase_no = null)
    {
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 40;
        }
        $data = array();
        $info = array();
        $info['table'] = BANK_ACCOUNT_TBL . ' ba,' . BANK_TBL . ' b';
        $info['fields'] = array('ba.bank_code', 'b.bank_name', 'ba.purchase_no', 'ba.account_name', 'ba.account_type', 'ba.phone', 'ba.fax');
        if ($purchase_no != "") {
            $info['where'] = "ba.bank_code = b.bank_id AND ba.purchase_no = '" . $purchase_no . "'";
        } else {
            $info['where'] = "ba.bank_code = b.bank_id";
        }
        $info['orderby'] = array("ba.purchase_no asc LIMIT $from,$to");
        $info['debug'] = false;
        $res = select($info);
        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        if ($purchase_no == "") {
            return $data; // for list
        } else {
            return $data[0];    // for view
        }

    }

    function getCurrencyList()
    {
        $info = array();
        $info['table'] = CURRENCY_TBL;
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

    function getCatagoryList()
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = CATAGORY_TBL;
        $info['where'] = "project_id = '$project_id'";
        $res = select($info);
        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        //dumpVar($data);
        return $data;
    }

    function createVoucharID()
    {
        $info = array();
        $info['table'] = DEVIT_VOUCHAR_TBL;
        $info['fields'] = array('max(voucher_no) as maxvoucher');
        $info['where'] = "voucher_no LIKE '%SI%'";
        $res = select($info);
        $maxvoucherId = 'SI0000000';
        if (count($res)) {
            foreach ($res as $v) {
                if ($v->maxvoucher) {
                    $maxvoucherId = $v->maxvoucher;
                }
                break;
            }

        }

        $maxvoucherId = generateID("SI", $maxvoucherId, 9);
        return $maxvoucherId;
    }

    //================= Start Sales Details ====================

    function showEditor4SalesDetails($msg = null)
    {
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['record_list'] = $this->getSalesDetailsList(getRequest('from'), getRequest('to'));
        $data['totalrecord'] = $this->getTotalSalesDetailsList(getRequest('from'), getRequest('to'));
        require_once(SALES_DETAILS_SKIN);
        return $data[0];
    }

    function showAllCompaniesSalesDetails($msg = null)
    {
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['record_list'] = $this->getAllSalesDetailsList(getRequest('from'), getRequest('to'));
        $data['totalrecord'] = $this->getAllTotalSalesDetailsList(getRequest('from'), getRequest('to'));
        require_once(ADMIN_SALES_DETAILS_SKIN);
        return $data[0];
    }

    function getSalesDetailsList($from, $to)
    {
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 500;
        }
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = SALES_MASTER_TBL . ' pm,' . SUB_ACC_HEAD_TBL . ' s,' . PROJECT_TBL . ' p,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('pm.voucher_no', 'p.project_name', 'p.location', 's.sub_id', 's.sub_head_name', 's.head_details', 'pm.po_no', 'pm.wo_no', 'pm.gate_pass', 'pm.track_no', 'pm.salse_type', 'pm.total_value', "DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date", "DATE_FORMAT(pm.delivery_date,'%d %b %y' ) as delivery_date", 'pm.mode_of_payment', 'c.curr_symble', 'pm.bank_name', 'pm.acc_no', 'pm.check_no', 'pm.check_no', 'pm.discount', 'pm.net_payble', 'pm.paid_amount', 'pm.due', 'pm.ref_no', 'pm.created_date', "DATE_FORMAT(pm.created_date,'%d %b %y' ) as date");

        $sql = "pm.customer = s.sub_id AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '" . $project_id . "'";
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND pm.created_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND pm.created_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND pm.created_date BETWEEN '$date_from' AND '$date_to'";
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

    function getTotalSalesDetailsList($from, $to)
    {
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = SALES_MASTER_TBL . ' pm,' . SUB_ACC_HEAD_TBL . ' s,' . PROJECT_TBL . ' p,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('pm.voucher_no');
        $sql = "pm.customer = s.sub_id AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '" . $project_id . "'";
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND pm.created_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND pm.created_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND pm.created_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['orderby'] = array("pm.created_date asc");
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

    function getAllSalesDetailsList($from, $to)
    {
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 500;
        }
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = SALES_MASTER_TBL . ' pm,' . SUB_ACC_HEAD_TBL . ' s,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('pm.voucher_no', 'pm.project_id', 's.sub_id', 's.sub_head_name', 's.head_details', 'pm.gate_pass', 'pm.track_no', 'pm.salse_type', 'pm.total_value', "DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date", 'pm.mode_of_payment', 'c.curr_symble', 'pm.bank_name', 'pm.acc_no', 'pm.check_no', 'pm.check_no', 'pm.discount', 'pm.net_payble', 'pm.paid_amount', 'pm.due', 'pm.ref_no', 'pm.created_date', "DATE_FORMAT(pm.created_date,'%d %b %y' ) as date");

        $sql = "pm.customer = s.sub_id AND pm.currency = c.currency_id";
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND pm.created_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND pm.created_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND pm.created_date BETWEEN '$date_from' AND '$date_to'";
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

    function getAllTotalSalesDetailsList($from, $to)
    {
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = SALES_MASTER_TBL . ' pm,' . SUB_ACC_HEAD_TBL . ' s,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('pm.voucher_no');
        $sql = "pm.customer = s.sub_id AND pm.currency = c.currency_id";
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND pm.created_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND pm.created_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND pm.created_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['orderby'] = array("pm.created_date asc");
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

    function savePayableCheck($voucher_no, $transaction_type, $paid_amount)
    {
        $requestdata = array();
        $requestdata = getUserDataSet(PAYABLE_CHECK_TBL);
        $requestdata['check_no'] = getRequest('check_no');
        $requestdata['check_issue_date'] = formatDate(getRequest('check_issue_date'));
        $requestdata['created_date'] = formatDate(getRequest('sales_date'));
        $requestdata['acc_head'] = getRequest('customer');
        $requestdata['head_type'] = "Check";
        $requestdata['voucher_no'] = $voucher_no;
        $requestdata['paid_amount'] = $paid_amount;
        $requestdata['transaction_type'] = $transaction_type;
        $requestdata['project_id'] = getFromSession('project_id');
        $requestdata['created_by'] = getFromSession('userid');
        $info = array();
        $info['table'] = PAYABLE_CHECK_TBL;
        $info['data'] = $requestdata;
        //$info['debug']  	=  true;
        $res = insert($info);

    }
//==================== End Sales Details =====================

} // End class
?>
