<?php

class FGProduction
{
    function run()
    {
        $cmd = getRequest('cmd');
        $u_t_id = getFromSession('u_type_id');
        if (($u_t_id == 102) || ($u_t_id == 105)) //2 = admin, 3 = project admin
        {
            switch ($cmd) {
                case 'out'            :
                    $this->showEditor();
                    break;
                case 'infg'            :
                    $this->showEditor4FinishProduction();
                    break;
                case 'bulk_production':
                    $this->bulkSaveProduction();
                    break;
                case 'transfer'        :
                    $this->showEditor4StockTransfer();
                    break;
                case 'pro_dtl'        :
                    $this->showEditor4ProductionDetails();
                    break;
                case 'loadProduct'        :
                    $this->loadProduct4Catagory(trim(getRequest('catagory_id')));
                    break;
                case 'print_report'        :
                    $screen = $this->showPrintEditor($msg);
                    break;
                case 'load_stock'            :
                    $this->loadProductStock(trim(getRequest('product_id')));
                    break;
                case 'get_productinfo'    :
                    $this->loadProductInfo(trim(getRequest('product_id')));
                    break;
                case 'getProductInfo'    :
                    $this->getProductInfo(trim(getRequest('product_id')));
                    break;
                case 'unapproved_rwm_list'    :
                    $this->showRWMUnapprovedList(trim(getRequest('product_id')));
                    break;
                case 'print_pending_rwm'    :
                    $this->printPendingRWM();
                    break;
                case 'approved_rwm'    :
                    $this->approvedRWM();
                    break;
                case 'edit_pending_rwm'    :
                    $this->editPendingRWM();
                case 'delete_pending_rwm'    :
                    $this->deletePendingRWM();
                    break;
                default                    :
                    $cmd = 'out';
                    $screen = $this->showEditor();
                    break;
            }
        } else if (($u_t_id == 101 || $u_t_id == 107)) // 1 = sysadmin, 2 = admin, 3 = project admin
        {
            switch ($cmd) {
                case 'out'        :
                    $this->showEditor();
                    break;
                case 'infg'        :
                    $this->showEditor4FinishProduction();
                    break;
                case 'bulk_production':
                    $this->bulkSaveProduction();
                    break;
                case 'transfer'    :
                    $this->showEditor4StockTransfer();
                    break;
                case 'pro_dtl'    :
                    $screen = $this->showEditor4ProductionDetails();
                    break;
                case 'loadProduct'    :
                    $this->loadProduct4Catagory(trim(getRequest('catagory_id')));
                    break;
                case 'print_report'    :
                    $screen = $this->showPrintEditor($msg);
                    break;
                case 'load_stock'    :
                    $this->loadProductStock(trim(getRequest('product_id')));
                    break;
                case 'get_productinfo':
                    $this->loadProductInfo(trim(getRequest('product_id')));
                    break;
                case 'getProductInfo' :
                    $this->getProductInfo(trim(getRequest('product_id')));
                    break;
                case 'unapproved_rwm_list'    :
                    $this->showRWMUnapprovedList(trim(getRequest('product_id')));
                    break;
                case 'print_pending_rwm'    :
                    $this->printPendingRWM();
                    break;
                case 'approved_rwm'    :
                    $this->approvedRWM();
                    break;
                case 'edit_pending_rwm'    :
                    $this->editPendingRWM();
                case 'delete_pending_rwm'    :
                    $this->deletePendingRWM();
                    break;
                case 'delete'        :
                    $this->deleteProduction("Delete Page");
                    break;
                default              :
                    $cmd = 'out';
                    $screen = $this->showEditor();
                    break;
            }
        } else {
            header("location:index.php?app=user_home&msg=You are not authorised !!!");
        }

        if ($cmd == 'out') {
            require_once(OUT_RAWMATERIALS_SKIN);
        }
        return true;
    }

    function showPrintEditor($msg = null)
    {
        $production_id = getRequest('production_id');
        if ($production_id) {
            $advArr = $this->getProductionMasterInfo($production_id);
            $advArr = parseThisValue($advArr);
            $data = array_merge(array(), $advArr);
            $data['item_list'] = $this->getProductionDetailsList($production_id);
            $data['message'] = $msg;
            $data['cmd'] = getRequest('cmd');
            require_once(PRINT_RAWMATERIALS_USED_SKIN);
            return true;
        } else {
            require_once(SHOW_PRINT_PRODUCTION_SKIN);
        }
    }

    function getProductionMasterInfo($id)
    {
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = PRODUCTION_MASTER_TBL . ' pm,' . DELIVERY_POINT_TBL . ' st,' . FACTORY_TBL . ' f,' . PROJECT_TBL . ' pa,' . PRODUCT_TBL . ' p,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('pm.production_id', 'pm.batch_no', 'pm.version_no', 'pa.project_name', 'pa.location', 'pm.total_value', "DATE_FORMAT(pm.used_date,'%d %b %y' ) as used_date",
            'c.curr_symble', 'st.delivery_point_name as out_store', 'f.factory_name', 'pm.created_time', 'pm.requisition_no');

        $sql = "pm.factory_id = f.factory_id  AND pm.out_store_id = st.delivery_pid  AND pm.project_id = pa.project_id AND pm.currency = c.currency_id AND pm.project_id = '" . $project_id . "' AND pm.production_id = '$id'";

        $info['where'] = $sql;
        $info['groupby'] = array("pm.production_id");
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

    function getProductionDetailsList($id)
    {
        $info = array();
        $info['table'] = PRODUCTION_DETAILS_TBL . ' pd,' . PRODUCT_TBL . ' p';
        $info['fields'] = array('pd.qty', 'pd.m_unit', 'pd.amount', 'p.product_name', 'pd.created_time');
        $sql = "pd.product = p.product_id AND pd.production_id = '$id'";
        $info['where'] = $sql;
        $info['groupby'] = array("pd.pro_detail_id");
        $info['orderby'] = array("pd.pro_detail_id asc");
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


    function getPendingProductionDetailsList($id)
    {
        $info = array();
        $info['table'] = PRODUCTION_DETAILS_TBL . ' pd,' . PRODUCT_TBL . ' p';
        $info['fields'] = array('pd.*', 'p.product_name', 'p.product_type');
        $sql = "pd.product = p.product_id AND pd.production_id = '$id'";
        $info['where'] = $sql;
        $info['groupby'] = array("pd.pro_detail_id");
        $info['orderby'] = array("pd.pro_detail_id asc");
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

    function deleteProduction()
    {

        if (userCondition()) {
            mysql_query("START TRANSACTION;");
            $project_id = getFromSession('project_id');
            $voucher_no = getRequest('id');
            $Dsql = "DELETE FROM " . PURCHASE_MASTER_TBL . " WHERE voucher_no='" . $voucher_no . "' AND project_id='" . $project_id . "'";
            $res1 = mysql_query($Dsql);
            $Csql = "DELETE FROM " . PURCHASE_DETAILS_TBL . " WHERE voucher_no='" . $voucher_no . "' AND project_id='" . $project_id . "'";
            $res2 = mysql_query($Csql);
            $Jsql = "DELETE FROM " . ACCOUNT_JOURNAL_TBL . " WHERE voucher_no='" . $voucher_no . "' AND project_id='" . $project_id . "'";
            $res3 = mysql_query($Jsql);
            $Ssql = "DELETE FROM " . STOCK_LEDGER_TBL . " WHERE voucher_no='" . $voucher_no . "' AND project_id='" . $project_id . "'";
            $res4 = mysql_query($Ssql);
            $Hsql = "DELETE FROM " . PRODUCTION_FG_TBL . " WHERE batch_no='" . $voucher_no . "' AND project_id='" . $project_id . "'";
            $res5 = mysql_query($Hsql);

            if ($res1 == 1 && $res2 == 1 && $res3 == 1 && $res4 == 1 && $res5 == 1) {
                mysql_query("COMMIT");
                header("location:index.php?app=fg.production&cmd=pro_dtl&msg=Successfully record deleted!!!");
            } else {
                mysql_query("ROLLBACK;");
                header("location:index.php?app=fg.production&cmd=pro_dtl&msg=Failed to delete record. Please try again");
            }
        } else {
            header("location:index.php?app=fg.production&cmd=pro_dtl&msg=You are not authorised !!!");
        }
    }


    function showRWMUnapprovedList()
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


        $response = $this->getUnapprovedRWMList($from, $to);
        $data['totalrecord'] = $response['total'];
        $data['record_list'] = $response['data'];

        $data['product_list'] = $comListApp->getProductList();
        $data['depo_list'] = $comListApp->getDeliveryPointList();
        require_once(UNAPPROVED_RAWMATERIALS_LIST_SKIN);
        return $data[0];

    }

    function getUnapprovedRWMList($from, $to)
    {
        $from = (int)$from;
        $to = (int)$to;

        if ($from == 0 && $to == 0) {
            $from = 0;
            $to = 500;
        }

        $job_name = getRequest('job_name');
        $job_description = getRequest('job_description');
        $requisition_no = getRequest('requisition_no');
        $product = getRequest('product');
        $store_id = getRequest('store_id');
        $project_id = getFromSession('project_id');

        $from_date = dateInputFormatYMD(getRequest('date_from'));
        $to_date = dateInputFormatYMD(getRequest('date_to'));

        // Default to_date = today if empty
        if (empty($to_date)) {
            $to_date = date('Y-m-d');
        }

        // Date condition (used_date)
        $date_cond = "";
        if (!empty($from_date)) {
            $date_cond .= " AND pm.used_date >= '$from_date'";
        }
        if (!empty($to_date)) {
            $date_cond .= " AND pm.used_date <= '$to_date'";
        }

        // Filters
        $filter = "";
        if (!empty($job_name)) {
            $filter .= " AND (pm.job_name LIKE '%$job_name%' OR jm.job_name LIKE '%$job_name%')";
        }
        if (!empty($job_description)) {
            $filter .= " AND pm.job_description LIKE '%$job_description%'";
        }
        if (!empty($requisition_no)) {
            $filter .= " AND pm.requisition_no LIKE '%$requisition_no%'";
        }
        if (!empty($product)) {
            $filter .= " AND pm.finish_product = '$product'";
        }
        if (!empty($store_id)) {
            $filter .= " AND pm.out_store_id = '$store_id'";
        }

        $filter .= " AND pm.approved_status = '0'";

        // Main data query
        $data_sql = "
        SELECT pm.production_id,
                pm.job_name as jobId,
                jm.job_name,
                pm.finish_product,
                pm.finish_qty,
                pm.out_store_id,
                pm.used_date,
                pm.requisition_no,
                pm.job_description,
                pm.description,
                pm.return_inventory,
                pm.total_value,
                po.product_code,
                po.product_name,
                po.product_desc,
                po.m_unit,
                d.delivery_point_name
            FROM " . PRODUCTION_MASTER_TBL . " pm
            LEFT JOIN " . PRODUCT_TBL . " po 
                ON po.product_id = pm.finish_product
            LEFT JOIN " . DELIVERY_POINT_TBL . " d 
                ON d.delivery_pid = pm.out_store_id
	    LEFT JOIN " . STOCK_TRANSFER_MASTER_TBL . " jm 
	        ON jm.transfer_no = pm.job_name
            WHERE pm.project_id = '$project_id'
            $date_cond
            $filter
            ORDER BY pm.used_date DESC
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
            SELECT COUNT(*) AS total_rows
            FROM " . PRODUCTION_MASTER_TBL . " pm
            LEFT JOIN " . PRODUCT_TBL . " po 
                ON po.product_id = pm.finish_product
            LEFT JOIN " . DELIVERY_POINT_TBL . " d 
                ON d.delivery_pid = pm.out_store_id
	    LEFT JOIN " . STOCK_TRANSFER_MASTER_TBL . " jm 
	        ON jm.transfer_no = pm.job_name
            WHERE pm.project_id = '$project_id'
            $date_cond
            $filter
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

    function printPendingRWM()
    {
        $production_id = getRequest('production_id');
        if ($production_id) {
            $advArr = $this->getPendingProductionMasterInfo($production_id);
            $advArr = parseThisValue($advArr);
            $data = array_merge(array(), $advArr);
            $data['item_list'] = $this->getProductionDetailsList($production_id);
            $data['message'] = $msg;
            $data['cmd'] = getRequest('cmd');
            $data['is_pending'] = true;
            require_once(PRINT_RAWMATERIALS_USED_SKIN);
            return true;
        } else {
            require_once(UNAPPROVED_RAWMATERIALS_LIST_SKIN);
        }
    }

    function getPendingProductionMasterInfo($id)
    {
        $project_id = getFromSession('project_id');
        $info = array();

        $info['table'] = PRODUCTION_MASTER_TBL . " pm
            LEFT JOIN " . DELIVERY_POINT_TBL . " st ON pm.out_store_id = st.delivery_pid
            LEFT JOIN " . FACTORY_TBL . " f ON pm.factory_id = f.factory_id
            LEFT JOIN " . PROJECT_TBL . " pa ON pm.project_id = pa.project_id
            LEFT JOIN " . PRODUCT_TBL . " p ON pm.finish_product = p.product_id
            LEFT JOIN " . CURRENCY_TBL . " c ON pm.currency = c.currency_id
	        LEFT JOIN " . STOCK_TRANSFER_MASTER_TBL . " jm ON jm.transfer_no = pm.job_name";

        $info['fields'] = array(
            'pm.production_id',
            'pm.batch_no',
            'pm.version_no',
            'pa.project_name',
            'pa.location',
            'pm.total_value',
            "DATE_FORMAT(pm.used_date,'%d %b %y') as used_date",
            'c.curr_symble',
            'st.delivery_point_name as out_store',
            'f.factory_name',
            'pm.created_time',
            'pm.requisition_no',
            'p.product_code',
            'p.product_name',
            'pm.finish_qty',
            'pm.job_name as jobId',
            'jm.job_name',
            'pm.job_description',
            'pm.description',
            'pm.return_inventory'
        );

        $info['where'] = "pm.approved_status = '0' AND pm.project_id = '" . $project_id . "' AND pm.production_id = '$id'";

        $info['groupby'] = array("pm.production_id");

        $res = select($info);

        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }

        return $data[0];
    }

    function approvedRWM()
    {
        $temp_production_id = getRequest('production_id');
        $approved_permission = getFromSession('approved_permission');

        if (getFromSession('u_type_id') != 101 && $approved_permission != 1) {
            $msg = "You are not authorized !!!";
            header("location:index.php?app=fg.production&cmd=unapproved_rwm_list&error_msg=$msg");
            exit;
        }
        $msg = "";
        if ($temp_production_id != "") {
            $production_id = "";

            $msql = "SELECT pm.*, p.unit_price, p.catagory, p.brand_code, p.m_unit, p.unit_price FROM " . PRODUCTION_MASTER_TBL . " pm LEFT JOIN " . PRODUCT_TBL . " p ON pm.finish_product = p.product_id WHERE pm.production_id = '$temp_production_id'";
            $master = mysql_fetch_object(mysql_query($msql));
            if (!empty($master->production_id)) {
                $production_id = $this->insertProductionMaster($master);
            }

            if (!empty($production_id)) {
                $msg = "Record has been approved successfully!!";
                header("location:index.php?app=fg.production&cmd=print_report&production_id=$production_id&msg=$msg");
                exit;
            } else {
                $msg = "Record not approved!!";
                header("location:index.php?app=fg.production&cmd=unapproved_rwm_list&error_msg=$msg");
                exit;
            }
        }

        $msg = "Data not found!!";
        header("location:index.php?app=fg.production&cmd=unapproved_rwm_list&error_msg=$msg");
        exit;
    }

    function editPendingRWM()
    {
        // Edit here
    }

    function deletePendingRWM()
    {
        $production_id = getRequest('production_id');
        if (getFromSession('u_type_id') != 101) {
            $msg = "You are not authorized !!!";
            header("location:index.php?app=fg.production&cmd=unapproved_rwm_list&error_msg=$msg");
            exit;
        }

        $msg = "";
        if ($production_id != "") {
            $dsql = "DELETE FROM " . PRODUCTION_DETAILS_TBL . " WHERE production_id ='" . $production_id . "'";
            mysql_query($dsql);

            $msql = "DELETE FROM " . PRODUCTION_MASTER_TBL . " WHERE production_id ='" . $production_id . "'";
            mysql_query($msql);

            $msg = "Record has been deleted successfully!!";
        } else {
            $msg = "Data not found!!";
        }

        header("location:index.php?app=fg.production&cmd=unapproved_rwm_list&error_msg=$msg");
        exit;
    }

    //======= Out Rawmaterials =======
    function showEditor($msg = null)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['finish_list'] = $comListApp->getFinishProductList();
        $data['wastage_list'] = $comListApp->getWastageProductList();
        $data['product_list'] = $this->getProductList();
        $data['cat_list'] = $this->getCatagoryList();
        $data['brand_list'] = $comListApp->getBrandList();
        $data['currency_list'] = $this->getCurrencyList();
        $data['factory_list'] = $comListApp->getProductionFactoryList();
        $data['depo_list'] = $comListApp->getDeliveryPointList(true);
        $data['batch_no'] = $this->getProductionBatchID();

        $supplierData = $comListApp->getSupplierData();
        $data['supplierData'] = json_encode($supplierData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $data['equipment_list'] = $comListApp->getAccountHeadList("Non Current Assets", "S126");

        $raw_material_list = $comListApp->getAccountHeadList("Current Assets", NULL, "C000055");
        $wip_list = $comListApp->getAccountHeadList("Current Assets", NULL, "C000057");
        $data['raw_material_list'] = array_merge($raw_material_list, $wip_list);

        $data['fg_list'] = $comListApp->getAccountHeadList("Current Assets", NULL, "C000056");
        $data['maintanance_list'] = $comListApp->getAccountHeadList("Current Assets", NULL, "C000154");

        $data['opening_list'] = $comListApp->getAccountHeadList("Opening Balance");
        $data['closing_list'] = $comListApp->getAccountHeadList("Closing Balance");
        $data['adjustments_list'] = $comListApp->getAccountHeadList("Adjustments Balance");
        $data['cost_list'] = $comListApp->getAccountHeadList("Cost Center");
        $data['job_list'] = $this->getJobList();
        $data['job_name_list'] = $this->getJobNameList();

        if (getRequest('submit')) {
            $this->insertPendingProductionMaster();
        }
        $data['cmd'] = getRequest('cmd');
        require_once(OUT_RAWMATERIALS_SKIN);
        return $data[0];
    }


    function insertProductionDetailsOld($production_id)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comlistApp = new CommonList();
        $store_id = getRequest('factory_id');
        $out_store_id = getRequest('out_store_id');
        $requestdata = array();
        $arr_catagory_product_id = array();
        $project_id = getFromSession('project_id');
        $currency = getRequest('currency');
        $arr_catagory_product_id = getRequest('input_catagory_product_id');
        $arr_brand = getRequest('input_brand');
        $arr_pvno = getRequest('input_pvoucher_no');
        $arr_m_unit = getRequest('input_m_unit');
        $arr_amount = getRequest('input_amount');
        $arr_qty = getRequest('input_qty');
        $arr_currency = getRequest('input_currency');
        $arr_invoice = getRequest('input_invoice_voucher');
        $arr_stock_ledger = getRequest('input_stock_ledger_id');

        $return_inventory = getRequest('return_inventory');

        $job_name = getRequest('job_name');
        $finish_item = getRequest('finish_item');


        for ($i = 0; $i < count($arr_catagory_product_id); $i++) {
            $catagory_product_sep = $arr_catagory_product_id[$i];
            $requestdata['project_id'] = $project_id;
            for ($j = 0; $j < count($catagory_product_sep); $j++) {
                $catagory_product = explode("###", $catagory_product_sep);
                $catagoryid = array();
                $productid = array();
                $brandid = array();
                $catagoryid['c'] = $catagory_product[0];
                $brandid['b'] = $catagory_product[1];
                $productid['p'] = $catagory_product[2];
            }
            foreach ($catagoryid as $val) {
                $requestdata['catagory'] = $val;
            }
            foreach ($brandid as $val) {
                $requestdata['brand_id'] = $val;
                $brand_id = $val;
            }
            foreach ($productid as $val) {
                $requestdata['product'] = $val;
                $product_id = $requestdata['product'];
            }
            foreach ($arr_m_unit as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $requestdata['m_unit'] = $val;
                    $m_unit = $val;
                }
            }
            foreach ($arr_pvno as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $pvoucher_no = $val;
                    $requestdata['pvoucher_no'] = $val;
                }
            }
            foreach ($arr_qty as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $requestdata['qty'] = $val;
                }
            }
            foreach ($arr_currency as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $requestdata['currency'] = $val;
                }
            }
            foreach ($arr_amount as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $requestdata['amount'] = $val;
                }
            }

            $invoice_voucher = "";
            $stock_ledger_id = "";

            foreach ($arr_invoice as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $invoice_voucher = $val;
                }
            }

            foreach ($arr_stock_ledger as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $stock_ledger_id = $val;
                }
            }

            $requestdata['created_by'] = getFromSession('userid');
            $requestdata['created_time'] = date('Y-m-d h:i:s');

            if (!empty($job_name)) {
                $requestdata['job_name'] = $job_name;
            }
            if (!empty($finish_item)) {
                $requestdata['finish_item'] = $finish_item;
            }

            $project_id = getFromSession('project_id');
            $requestdata['project_id'] = $project_id;
            $requestdata['factory_id'] = $store_id;
            $requestdata['out_store_id'] = $out_store_id;
            $requestdata['pvoucher_no'] = $pvoucher_no;
            $requestdata['production_id'] = $production_id;
            $info = array();
            $info['table'] = PRODUCTION_DETAILS_TBL;
            $info['data'] = $requestdata;
            //$info['debug']  	=  true;
            $res = insert($info);
            if ($res) {

                /// inset production invoice
                $production_details_id = mysql_insert_id();
                if ($production_details_id && !empty($invoice_voucher) && !empty($stock_ledger_id)) {
                    $productionRequestdata['production_id'] = $production_id;
                    $productionRequestdata['production_details_id'] = $production_details_id;
                    $productionRequestdata['invoice_voucher'] = $invoice_voucher;
                    $productionRequestdata['stock_ledger_id'] = $stock_ledger_id;
                    $productionRequestdata['qty'] = $requestdata['qty'];
                    $productionRequestdata['created_by'] = getFromSession('userid');
                    $productionRequestdata['created_time'] = date('Y-m-d h:i:s');
                    $productionInfo = array();
                    $productionInfo['table'] = "production_invoice_stock";
                    $productionInfo['data'] = $productionRequestdata;
                    //$info['debug']  	=  true;
                    $res = insert($productionInfo);
                }
                // end production invoice


                $Prosql = "SELECT product_type FROM " . PRODUCT_TBL . " WHERE product_id = '$product_id' AND project_id = '$project_id'";
                $Prorow = mysql_fetch_object(mysql_query($Prosql));
                $product_type = $Prorow->product_type;
                $used_qty = $requestdata['qty'];
                $used_date = formatDate(getRequest('used_date'));
                /*
                $PUSql="SELECT pur_detail_id,sales_qty FROM ".PURCHASE_DETAILS_TBL." WHERE product='$product_id' AND brand_id='$brand_id'
                AND project_id='$project_id' AND voucher_no='$pvoucher_no'";
                $Prorow = mysql_fetch_object(mysql_query($PUSql));
                $pur_detail_id 	= $Prorow->pur_detail_id;
                $TTLUsedQty 	= ($Prorow->sales_qty+$used_qty);
                $pdusql = "UPDATE ".PURCHASE_DETAILS_TBL." SET sales_qty='".$TTLUsedQty."' WHERE pur_detail_id='$pur_detail_id'";
                $pdres = mysql_query($pdusql);
                */

                if ($product_id != "") {
                    $totalCR = $this->getTotalCreditStock($product_id, getFromSession('project_id'));
                    $totalDR = $this->getTotalDebitStock($product_id, getFromSession('project_id'));

                    if (isset($return_inventory) && $return_inventory == 1) {
                        $balance = (($totalDR + $used_qty) - $totalCR);
                        $this->saveStockJournal($production_id, $pvoucher_no, $project_id, $out_store_id, $product_id, $product_type, "Used for production", $requestdata['amount'], $requestdata['m_unit'], $used_qty, 0, $balance, $used_date);
                    } else {
                        $balance = ($totalDR - ($totalCR + $used_qty));
                        $this->saveStockJournal($production_id, $pvoucher_no, $project_id, $out_store_id, $product_id, $product_type, "Used for production", $requestdata['amount'], $requestdata['m_unit'], 0, $used_qty, $balance, $used_date);
                    }
                }// end if
            }//end res
        }// end for

        //=== Stock Cr of Raw Materials =====
        $StockAmount = getRequest('total_value');
        if ($out_store_id == "D0026") {
            $StockId = $comlistApp->getWPStockId(getFromSession('project_id'));
        } else {
            $StockId = $comlistApp->getRMStockId(getFromSession('project_id'));
        }

        $inventory_type = getRequest('inventory_type');
        $inventory_id = getRequest('inventory_id');
        if ($inventory_type != "" && $inventory_id != "") {
            $StockId = $inventory_id;
        }

        $description = "Issued Item";
        $transaction_type = "Issued Raw Materials";

        switch ($inventory_type) {
            case "A000017":
                $transaction_type = "Issued Raw Materials";
                $description = "Used for Production";
                break;
            case "A000024":
                $transaction_type = "Issued Packing Materials";
                break;
            case "A000036":
                $transaction_type = "Issued Finished Goods";
                $description = "Used for Production";
                break;
            case "S126":
                $transaction_type = "Issued Assets";
                break;
            case "A007758":
                $transaction_type = "Issued Maintenance Stock";
                break;
            default:
                $transaction_type = "Issued Raw Materials";
        }

        $totalStockCr = $this->getTotalCreditAmount($StockId, getFromSession('project_id'));
        $totalStockDr = $this->getTotalDebitAmount($StockId, getFromSession('project_id'));

        if (isset($return_inventory) && $return_inventory == 1) {
            $StockBalance = (($totalStockDr + $StockAmount) - $totalStockCr);
            $comlistApp->saveAccJournal($production_id, $StockId, "Stock", $transaction_type, getFromSession('project_id'), $description, $StockAmount, 0, $StockBalance, 0, $used_date);
        } else {
            $StockBalance = ($totalStockDr - ($totalStockCr + $StockAmount));
            $comlistApp->saveAccJournal($production_id, $StockId, "Stock", $transaction_type, getFromSession('project_id'), $description, 0, $StockAmount, $StockBalance, 0, $used_date);
        }

    } //End of the function savePaymentDetails()

    function insertProductionMaster($master)
    {
        mysql_query("START TRANSACTION;");
        mysql_query("SET autocommit=0;");
        $project_id = getFromSession('project_id');
        $requestdata = array();
        $production_id = $this->createProductionID();
        if ($production_id != "") {
            $requestdata['production_id'] = $production_id;
        } else {
            $msg = "ID overflow !!!";
            header("location:index.php?app=user_home&msg=$msg");
            exit;
        }

        $job_id = $master->job_id;

        $temp_production_id = $master->production_id;

        $requestdata['approved_status'] = 1;
        $requestdata['approved_by'] = getFromSession('userid');
        $requestdata['approved_time'] = date('Y-m-d h:i:s');

        $info = array();
        $info['table'] = PRODUCTION_MASTER_TBL;
        $info['data'] = $requestdata;
        $info['where'] = "production_id ='$temp_production_id'";
        //$info['debug']  =  true;
        $res = update($info);
        if ($res) {
            if (!empty($job_id)) {
                $info = array();
                $info['table'] = "job_master";
                $requestdata['issue_voucher'] = $production_id;
                //dBug($requestdata);
                $info['data'] = $requestdata;
                $info['where'] = "id ='$job_id'";
                update($info);
            }
            $this->insertProductionDetails($production_id, $temp_production_id, $master);
            $this->SaveProductionFG($master);
            mysql_query("COMMIT;");
            return $production_id;
        } else {
            return false;
        }
    }


    function resolvedRWM(){
        $production_id = "P0009597";
        $msql = "SELECT pm.*, p.unit_price, p.catagory, p.brand_code, p.m_unit, p.unit_price FROM " . PRODUCTION_MASTER_TBL . " pm LEFT JOIN " . PRODUCT_TBL . " p ON pm.finish_product = p.product_id WHERE pm.production_id = '$production_id'";
        $master = mysql_fetch_object(mysql_query($msql));

        $out_store_id = $master->out_store_id;
        $project_id = "P0005";
        $return_inventory = $master->return_inventory;

        $result = $this->getPendingProductionDetailsList($production_id);

        foreach ($result as $val) {
            if($val->product== "P000389"){
                continue;
            }
            $stock_ledger_id = $val->stock_ledger_id;
            $invoice_voucher = $val->invoice_voucher;
            $pro_detail_id = $val->pro_detail_id;

            /// inset production invoice
            $production_details_id = $val->pro_detail_id;
            if ($production_details_id && !empty($invoice_voucher) && !empty($stock_ledger_id)) {
                $productionRequestdata['production_id'] = $production_id;
                $productionRequestdata['production_details_id'] = $production_details_id;
                $productionRequestdata['invoice_voucher'] = $invoice_voucher;
                $productionRequestdata['stock_ledger_id'] = $stock_ledger_id;
                $productionRequestdata['qty'] = $val->qty;
                $productionRequestdata['created_by'] = "saiful islam";
                $productionRequestdata['created_time'] = "2026-05-17 06:15:05";
                $productionInfo = array();
                $productionInfo['table'] = "production_invoice_stock";
                $productionInfo['data'] = $productionRequestdata;
                //$info['debug']  	=  true;
                $res = insert($productionInfo);
            }
            // end production invoice

            $product_id = $val->product;
            $pvoucher_no = $val->pvoucher_no;
            $amount = $val->amount;
            $m_unit = $val->m_unit;
            $product_type = $val->product_type;
            $used_qty = $val->qty;
            $used_date = $master->used_date;

            if ($product_id != "") {
                $totalCR = $this->getTotalCreditStock($product_id, $project_id);
                $totalDR = $this->getTotalDebitStock($product_id, $project_id);

                if (isset($return_inventory) && $return_inventory == 1) {
                    $balance = (($totalDR + $used_qty) - $totalCR);
                    $this->saveStockJournal($production_id, $pvoucher_no, $project_id, $out_store_id, $product_id, $product_type, "Used for production", $amount, $m_unit, $used_qty, 0, $balance, $used_date);
                } else {
                    $balance = ($totalDR - ($totalCR + $used_qty));
                    $this->saveStockJournal($production_id, $pvoucher_no, $project_id, $out_store_id, $product_id, $product_type, "Used for production", $amount, $m_unit, 0, $used_qty, $balance, $used_date);
                }
            }
        }
    }


    function insertProductionDetails($production_id, $temp_production_id, $master)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comlistApp = new CommonList();

        $out_store_id = $master->out_store_id;
        $project_id = getFromSession('project_id');
        $return_inventory = $master->return_inventory;

        $result = $this->getPendingProductionDetailsList($temp_production_id);
        foreach ($result as $val) {
            $stock_ledger_id = $val->stock_ledger_id;
            $invoice_voucher = $val->invoice_voucher;
            $pro_detail_id = $val->pro_detail_id;

            $requestdata = array();
            $requestdata['production_id'] = $production_id;
            $info = array();
            $info['table'] = PRODUCTION_DETAILS_TBL;
            $info['data'] = $requestdata;
            $info['where'] = "pro_detail_id ='$pro_detail_id'";
            //$info['debug']  	=  true;
            $res = update($info);

            if ($res) {
                /// inset production invoice
                $production_details_id = $val->pro_detail_id;
                if ($production_details_id && !empty($invoice_voucher) && !empty($stock_ledger_id)) {
                    $productionRequestdata['production_id'] = $production_id;
                    $productionRequestdata['production_details_id'] = $production_details_id;
                    $productionRequestdata['invoice_voucher'] = $invoice_voucher;
                    $productionRequestdata['stock_ledger_id'] = $stock_ledger_id;
                    $productionRequestdata['qty'] = $val->qty;
                    $productionRequestdata['created_by'] = getFromSession('userid');
                    $productionRequestdata['created_time'] = date('Y-m-d h:i:s');
                    $productionInfo = array();
                    $productionInfo['table'] = "production_invoice_stock";
                    $productionInfo['data'] = $productionRequestdata;
                    //$info['debug']  	=  true;
                    $res = insert($productionInfo);
                }
                // end production invoice

                $product_id = $val->product;
                $pvoucher_no = $val->pvoucher_no;
                $amount = $val->amount;
                $m_unit = $val->m_unit;
                $product_type = $val->product_type;
                $used_qty = $val->qty;
                $used_date = $master->used_date;

                if ($product_id != "") {
                    $totalCR = $this->getTotalCreditStock($product_id, $project_id);
                    $totalDR = $this->getTotalDebitStock($product_id, $project_id);

                    if (isset($return_inventory) && $return_inventory == 1) {
                        $balance = (($totalDR + $used_qty) - $totalCR);
                        $this->saveStockJournal($production_id, $pvoucher_no, $project_id, $out_store_id, $product_id, $product_type, "Used for production", $amount, $m_unit, $used_qty, 0, $balance, $used_date);
                    } else {
                        $balance = ($totalDR - ($totalCR + $used_qty));
                        $this->saveStockJournal($production_id, $pvoucher_no, $project_id, $out_store_id, $product_id, $product_type, "Used for production", $amount, $m_unit, 0, $used_qty, $balance, $used_date);
                    }
                }
            }
        }

        //=== Stock Cr of Raw Materials =====
        $StockAmount = $master->total_value;
        if ($out_store_id == "D0026") {
            $StockId = $comlistApp->getWPStockId($project_id);
        } else {
            $StockId = $comlistApp->getRMStockId($project_id);
        }

        $inventory_type = $master->inventory_type;
        $inventory_id = $master->inventory_id;
        if (!empty($inventory_id)) {
            $StockId = $inventory_id;
        }

        $description = "Issued Item";

        switch ($inventory_type) {
            case "A000017":
                $transaction_type = "Consumption";
                $description = "Used for Production";
                break;
            case "A000024":
                $transaction_type = "Issued Packing Materials";
                break;
            case "A000036":
                $transaction_type = "Issued Finished Goods";
                $description = "Used for Production";
                break;
            case "S126":
                $transaction_type = "Issued Assets";
                break;
            case "A007758":
                $transaction_type = "Issued Maintenance Stock";
                break;
            default:
                $transaction_type = "Consumption";
        }

        $totalStockCr = $this->getTotalCreditAmount($StockId, $project_id);
        $totalStockDr = $this->getTotalDebitAmount($StockId, $project_id);

        if (isset($return_inventory) && $return_inventory == 1) {
            $StockBalance = (($totalStockDr + $StockAmount) - $totalStockCr);
            $comlistApp->saveAccJournal($production_id, $StockId, "Stock", $transaction_type, $project_id, $description, $StockAmount, 0, $StockBalance, 0, $used_date);
        } else {
            $StockBalance = ($totalStockDr - ($totalStockCr + $StockAmount));
            $comlistApp->saveAccJournal($production_id, $StockId, "Stock", $transaction_type, $project_id, $description, 0, $StockAmount, $StockBalance, 0, $used_date);
        }
    }

    public function SaveProductionFG($master)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $project_id = getFromSession('project_id');
        $created_by = getFromSession('userid');

        $production_date = isset($master->used_date) ? $this->formatDate($master->used_date) : date("Y-m-d");

        $finish_product = isset($master->finish_product) ? $this->clean($master->finish_product) : "";
        $finish_qty = isset($master->finish_qty) ? $this->clean($master->finish_qty) : 0;
        $wastage_product = isset($master->wastage_product) ? $this->clean($master->wastage_product) : "";
        $wastage_qty = isset($master->wastage_qty) ? $this->clean($master->wastage_qty) : 0;
        $factory_id = isset($master->factory_id) ? $this->clean($master->factory_id) : "";
        $job_id = isset($master->job_id) ? $this->clean($master->job_id) : NULL;
        $job_name = isset($master->job_name) ? $this->clean($master->job_name) : "";
        $store_id = isset($master->in_store_id) ? $this->clean($master->in_store_id) : "";
        $inventory_type = isset($master->finish_inventory_id) ? $this->clean($master->finish_inventory_id) : "";
        $inventory_id = isset($master->finish_inventory_id) ? $this->clean($master->finish_inventory_id) : "";

        $catagory = $master->catagory;
        $brand_code = $master->brand_code;
        $m_unit = $master->m_unit;
        $unit_price = $master->unit_price;
        $production_qty = $finish_qty;
        $total_value = ($unit_price * $finish_qty);

        $production_id = $this->createFGBatchNo();
        if (empty($production_id)) {
            mysql_query("ROLLBACK");
            $msg = "ID overflow !!!";
            header("location:index.php?app=fg.production&cmd=unapproved_rwm_list&error_msg=$msg");
            exit;
        }
        $created_time = date('Y-m-d h:i:s');


        $sql = "INSERT INTO " . PRODUCTION_FG_TBL . "
        (batch_no,project_id,factory_id,store_id,production_date,finish_product,catagory,brand_code,unit_price,production_qty,
        m_unit,total_value,created_by,created_time,job_id,job_name)
        VALUES
        ('$production_id','$project_id','$factory_id','$store_id','$production_date','$finish_product',
        '$catagory','$brand_code','$unit_price','$production_qty','$m_unit','$total_value','$created_by','$created_time','$job_id','$job_name')";
        $res = mysql_query($sql);

        if ($res) {
            $balanceQty = $this->getStockBalanceQty($finish_product, $project_id, $store_id);
            $balanceF = ($balanceQty + $finish_qty);

            $this->saveStockJournal($production_id, $production_id, $project_id, $store_id, $finish_product, "Sales Item", "Production", $unit_price, $m_unit, $finish_qty, 0, $balanceF, $production_date);
            //=== Stock Dr =====

            if ($store_id == "D0026") {
                $StockId = $comListApp->getWPStockId($project_id);
            } elseif ($store_id == "D0027") {
                $StockId = $comListApp->getMXStockId($project_id);
            } else {
                $StockId = $comListApp->getFGStockId($project_id);
            }

            $StockId = $inventory_type;
            if (!empty($inventory_type) && !empty($inventory_id)) {
                $StockId = $inventory_id;
            }

            $StockPvBalance = $this->getTotalBalanceAmount($StockId, $project_id);
            $StockBalance = ($StockPvBalance + $total_value);
            $description = "FGP";
            $comListApp->saveAccJournal($production_id, $StockId, "Stock", "Finish Goods", $project_id, $description, $total_value, 0, $StockBalance, 0, $production_date);


            //wastage product
            $wProsql = "SELECT unit_price,m_unit,product_type FROM " . PRODUCT_TBL . " WHERE product_id = '$wastage_product' AND project_id = '$project_id'";
            $wproductInfo = mysql_fetch_object(mysql_query($wProsql));
            $unit_price = $wproductInfo->unit_price;
            $m_unit = $wproductInfo->m_unit;
            $product_type = $wproductInfo->product_type;

            // Stock Ledger
            $wbalanceQty = $this->getStockBalanceQty($wastage_product, $project_id, $store_id);
            $wbalanceF = ($wbalanceQty + $wastage_qty);

            $this->saveStockJournal($production_id, $production_id, $project_id, $store_id, $wastage_product, $product_type, "Wastage", $unit_price, $m_unit, $wastage_qty, 0, $wbalanceF, $production_date);

            // Account journal
            $wTotalValue = $unit_price * $wastage_qty;
            $wStockPvBalance = $this->getTotalBalanceAmount($wastage_product, $project_id);
            $wStockBalance = ($wStockPvBalance + $wTotalValue);
            $description = "Wastage";
            $comListApp->saveAccJournal($production_id, $StockId, "Stock", "Wastage", $project_id, $description, $total_value, 0, $wStockBalance, 0, $production_date);
        }

    }

    function insertPendingProductionDetails($production_id)
    {
        $store_id = getRequest('factory_id');
        $out_store_id = getRequest('out_store_id');
        $requestdata = array();
        $arr_catagory_product_id = array();
        $project_id = getFromSession('project_id');
        $arr_catagory_product_id = getRequest('input_catagory_product_id');
        $arr_pvno = getRequest('input_pvoucher_no');
        $arr_m_unit = getRequest('input_m_unit');
        $arr_amount = getRequest('input_amount');
        $arr_qty = getRequest('input_qty');
        $arr_currency = getRequest('input_currency');
        $arr_invoice = getRequest('input_invoice_voucher');
        $arr_stock_ledger = getRequest('input_stock_ledger_id');

        for ($i = 0; $i < count($arr_catagory_product_id); $i++) {
            $catagory_product_sep = $arr_catagory_product_id[$i];
            $requestdata['project_id'] = $project_id;
            for ($j = 0; $j < count($catagory_product_sep); $j++) {
                $catagory_product = explode("###", $catagory_product_sep);
                $catagoryid = array();
                $productid = array();
                $brandid = array();
                $catagoryid['c'] = $catagory_product[0];
                $brandid['b'] = $catagory_product[1];
                $productid['p'] = $catagory_product[2];
            }
            foreach ($catagoryid as $val) {
                $requestdata['catagory'] = $val;
            }
            foreach ($brandid as $val) {
                $requestdata['brand_id'] = $val;
            }
            foreach ($productid as $val) {
                $requestdata['product'] = $val;
            }
            foreach ($arr_m_unit as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $requestdata['m_unit'] = $val;
                }
            }
            foreach ($arr_pvno as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $pvoucher_no = $val;
                    $requestdata['pvoucher_no'] = $val;
                }
            }
            foreach ($arr_qty as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $requestdata['qty'] = $val;
                }
            }
            foreach ($arr_currency as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $requestdata['currency'] = $val;
                }
            }
            foreach ($arr_amount as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $requestdata['amount'] = $val;
                }
            }

            foreach ($arr_invoice as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $requestdata['invoice_voucher'] = $val;
                }
            }

            foreach ($arr_stock_ledger as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $requestdata['stock_ledger_id'] = $val;
                }
            }

            $requestdata['created_by'] = getFromSession('userid');
            $requestdata['created_time'] = date('Y-m-d h:i:s');


            $project_id = getFromSession('project_id');
            $requestdata['project_id'] = $project_id;
            $requestdata['factory_id'] = $store_id;
            $requestdata['out_store_id'] = $out_store_id;
            $requestdata['pvoucher_no'] = $pvoucher_no;
            $requestdata['production_id'] = $production_id;
            $info = array();
            $info['table'] = PRODUCTION_DETAILS_TBL;
            $info['data'] = $requestdata;
            //$info['debug']  	=  true;
            $res = insert($info);
        }// end for
    }

    function insertPendingProductionMaster()
    {
        mysql_query("START TRANSACTION;");
        mysql_query("SET autocommit=0;");
        $project_id = getFromSession('project_id');
        $requestdata = array();
        $requestdata = getUserDataSet(PRODUCTION_MASTER_TBL);
        $requestdata['used_date'] = formatDate(getRequest('used_date'));

        if (getRequest('production_type') != "Finish") {
            $requestdata['finish_qty'] = 0;
            $requestdata['production_amount'] = 0;
        }
        $requestdata['requisition_no'] = getRequest('requisition_no');
        $requestdata['description'] = getRequest('description');
        $requestdata['job_description'] = getRequest('job_description');
        $job_id = getRequest('job_id');
        if (!empty($job_id)) {
            $requestdata['job_id'] = $job_id;
        }
        $requestdata['project_id'] = getFromSession('project_id');
        $requestdata['created_by'] = getFromSession('userid');
        $production_id = $this->createTempProductionID();
        $requestdata['created_date'] = date('Y-m-d h:i:s');

        $job_name = getRequest('job_name');
        if (!empty($job_name)) {
            $requestdata['job_name'] = $job_name;
        }
        $finish_product = getRequest('finish_product');
        if (!empty($finish_product)) {
            $requestdata['finish_product'] = $finish_product;
        }
        $finish_qty = getRequest('finish_qty');
        if (!empty($finish_qty)) {
            $requestdata['finish_qty'] = $finish_qty;
        }

        $wastage_product = getRequest('wastage_product');
        if (!empty($wastage_product)) {
            $requestdata['wastage_product'] = $wastage_product;
        }
        $wastage_qty = getRequest('wastage_qty');
        if (!empty($wastage_qty)) {
            $requestdata['wastage_qty'] = $wastage_qty;
        }
        $inventory_type = getRequest('inventory_type');
        if (!empty($inventory_type)) {
            $requestdata['inventory_type'] = $inventory_type;
        }
        $inventory_id = getRequest('inventory_id');
        if (!empty($inventory_id)) {
            $requestdata['inventory_id'] = $inventory_id;
        }
        $finish_inventory_id = getRequest('finish_inventory_id');
        if (!empty($finish_inventory_id)) {
            $requestdata['finish_inventory_id'] = $finish_inventory_id;
        }
        $in_store_id = getRequest('in_store_id');
        if (!empty($in_store_id)) {
            $requestdata['in_store_id'] = $in_store_id;
        }

        $return_inventory = getRequest('return_inventory');
        if (!empty($return_inventory) && $return_inventory == 1) {
            $requestdata['return_inventory'] = 1;
        }

        if ($production_id != "") {
            $requestdata['production_id'] = $production_id;
        } else {
            $msg = "ID overflow !!!";
            header("location:index.php?app=user_home&msg=$msg");
            exit;
        }

        $info = array();
        $info['table'] = PRODUCTION_MASTER_TBL;
        $info['data'] = $requestdata;
        //$info['debug']  =  true;
        $res = insert($info);

        if ($res) {
            $this->insertPendingProductionDetails($production_id);
            mysql_query("COMMIT;");
            header("location:index.php?app=fg.production&cmd=print_pending_rwm&production_id=" . $production_id);
        } else {
            header("location:?app=fg.production&cmd=out");
        }
    }

    //=======End Out Rawmaterials =======
    //====== Start FG In ========
    function showEditor4FinishProduction($msg = null)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();

//        if (getRequest('action') == 'save') {
//            mysql_query("START TRANSACTION;");
//            mysql_query("SET autocommit=0;");
//
//            $production_id = $this->saveInFinishGoods();
//            if ($production_id != "") {
//                $this->saveFinishProduction($production_id);
//                $msg = "Successfully saved finish goods in !!!";
//                mysql_query("COMMIT;");
//                header("location:index.php?app=fg.production&cmd=infg&msg=$msg");
//
//            } else {
//                mysql_query("ROLLBACK;");
//                $msg = "Failed finish goods in !!! Please try again";
//                header("location:index.php?app=fg.production&cmd=infg&msg=$msg");
//            }
//        }

        $data['finish_list'] = $comListApp->getProductList();
        $data['cat_list'] = $this->getCatagoryList();
        $data['brand_list'] = $comListApp->getBrandList();
        $data['currency_list'] = $this->getCurrencyList();
        $data['factory_list'] = $comListApp->getProductionFactoryList();
        $data['depo_list'] = $comListApp->getDeliveryPointList(true);
        $data['batch_no'] = $this->getProductionBatchID();

        $data['cmd'] = getRequest('cmd');

        $supplierData = $comListApp->getSupplierData();
        $data['supplierData'] = json_encode($supplierData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $data['equipment_list'] = $comListApp->getAccountHeadList("Non Current Assets", "S126");

        $raw_material_list = $comListApp->getAccountHeadList("Current Assets", NULL, "C000055");
        $wip_list = $comListApp->getAccountHeadList("Current Assets", NULL, "C000057");
        $data['raw_material_list'] = array_merge($raw_material_list, $wip_list);

        $data['fg_list'] = $comListApp->getAccountHeadList("Current Assets", NULL, "C000056");
        $data['maintanance_list'] = $comListApp->getAccountHeadList("Current Assets", NULL, "C000154");

        $data['job_list'] = $this->getJobList();
        $data['job_name_list'] = $this->getJobNameList();

        require_once(IN_FINISH_GOODS_SKIN);
        return $data[0];
    }

    public function clean($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = strip_tags($data);
        $data = htmlspecialchars($data, ENT_QUOTES);
        $data = mysql_real_escape_string($data);

        return $data;
    }

    public function bulkSaveProduction()
    {
        if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering

        header('Content-Type: application/json');

        $json_input = file_get_contents("php://input");
        $post_data = json_decode($json_input, true);

        if (!isset($post_data['rows']) || !is_array($post_data['rows'])) {
            echo json_encode([
                "status" => false,
                "message" => "No production rows received"
            ]);
            exit;
        }

        $rows = $post_data['rows'];

        $project_id = getFromSession('project_id');
        $created_by = getFromSession('userid');

        mysql_query("START TRANSACTION");

        foreach ($rows as $item) {
            $production_date = isset($item['production_date']) ? $this->formatDate($item['production_date']) : date("Y-m-d");

            $finish_product = isset($item['product']) ? $this->clean($item['product']) : "";
            $finish_qty = isset($item['qty']) ? $this->clean($item['qty']) : 0;
            $finish_qty_in_kg = isset($item['qtyInKg']) ? $this->clean($item['qtyInKg']) : 0;
            $factory_id = isset($item['factory']) ? $this->clean($item['factory']) : "";
            $job_id = isset($item['jobID']) ? $this->clean($item['jobID']) : NULL;
            $job_name = isset($item['jobName']) ? $this->clean($item['jobName']) : "";
            $store_id = isset($item['store']) ? $this->clean($item['store']) : "";
            $inventory_type = isset($item['invType']) ? $this->clean($item['invType']) : "";
            $inventory_id = isset($item['invHead']) ? $this->clean($item['invHead']) : "";

            // product info
            $Prosql = "SELECT catagory, brand_code, m_unit, unit_price FROM " . PRODUCT_TBL . " WHERE product_id='$finish_product' AND project_id='$project_id'";
            $Prorow = mysql_fetch_object(mysql_query($Prosql));

            if (!$Prorow) {
                mysql_query("ROLLBACK");
                echo json_encode([
                    "status" => false,
                    "message" => "Invalid product selected"
                ]);
                exit;
            }

            $catagory = $Prorow->catagory;
            $brand_code = $Prorow->brand_code;
            $m_unit = $Prorow->m_unit;
            $unit_price = $Prorow->unit_price;
            $production_qty = $finish_qty;
            $total_value = ($unit_price * $finish_qty);

            $production_id = $this->createFGBatchNo();
            if (empty($production_id)) {
                mysql_query("ROLLBACK");
                echo json_encode([
                    "status" => false,
                    "message" => "ID overflow !!!"
                ]);
                exit;
            }
            $created_time = date('Y-m-d h:i:s');

            // insert production
            $sql = "INSERT INTO " . PRODUCTION_FG_TBL . "
        (batch_no,project_id,factory_id,store_id,production_date,finish_product,catagory,brand_code,unit_price,production_qty,
        m_unit,total_value,created_by,created_time,job_id,job_name)
        VALUES
        ('$production_id','$project_id','$factory_id','$store_id','$production_date','$finish_product',
        '$catagory','$brand_code','$unit_price','$production_qty','$m_unit','$total_value','$created_by','$created_time','$job_id','$job_name')";
            $res = mysql_query($sql);

            if (!$res) {
                mysql_query("ROLLBACK");
                echo json_encode([
                    "status" => false,
                    "message" => "Database insert failed"
                ]);
                exit;
            }

            // Save Stock + Accounting
            $this->saveBulkFinishProduction($production_id, $store_id, $finish_product, $finish_qty, $unit_price, $m_unit, $total_value, $inventory_type, $inventory_id, $production_date);
        }

        mysql_query("COMMIT");
        echo json_encode([
            "status" => true,
            "message" => "Production saved successfully",
        ]);
        exit;
    }

    function saveBulkFinishProduction($production_id, $store_id, $finish_product, $finish_qty, $unit_price, $m_unit, $total_value, $inventory_type, $inventory_id, $production_date)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();

        $project_id = getFromSession('project_id');

        $balanceQty = $this->getStockBalanceQty($finish_product, $project_id, $store_id);
        $balanceF = ($balanceQty + $finish_qty);

        $this->saveStockJournal($production_id, $production_id, $project_id, $store_id, $finish_product, "Sales Item", "Production", $unit_price, $m_unit, $finish_qty, 0, $balanceF, $production_date);
        //=== Stock Dr =====

        $StockId = $inventory_type;
        if (!empty($StockId) && !empty($inventory_id)) {
            $StockId = $inventory_id;
        } else {
            if ($store_id == "D0026") {
                $StockId = $comListApp->getWPStockId($project_id);
            } elseif ($store_id == "D0027") {
                $StockId = $comListApp->getMXStockId($project_id);
            } else {
                $StockId = $comListApp->getFGStockId($project_id);
            }
        }

        $StockPvBalance = $this->getTotalBalanceAmount($StockId, $project_id);
        $StockBalance = ($StockPvBalance + $total_value);
        $description = "FGP";
        $comListApp->saveAccJournal($production_id, $StockId, "Stock", "Finish Goods", $project_id, $description, $total_value, 0, $StockBalance, 0, $production_date);
    }

    function getJobList()
    {
        $info = array();
        $info['table'] = "job_master";
        $info['where'] = "issue_voucher IS NULL";
        //$info['debug'] = false;
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

    function getJobNameList()
    {
        $info = array();
        $info['table'] = STOCK_TRANSFER_MASTER_TBL;
        $info['fields'] = array('transfer_no', 'job_name', 'finish_item');
        $info['where'] = "job_name IS NOT NULL";
        //$info['debug'] = false;
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

    function saveInFinishGoods()
    {
        $project_id = getFromSession('project_id');
        $requestdata = array();
        $requestdata = getUserDataSet(PRODUCTION_FG_TBL);
        $requestdata['production_date'] = formatDate(getRequest('production_date'));
        $finish_product = getRequest('finish_product');
        $finish_qty = getRequest('finish_qty');
        $Prosql = "SELECT catagory,brand_code,m_unit,unit_price FROM " . PRODUCT_TBL . " WHERE product_id = '$finish_product' AND project_id = '$project_id'";
        $Prorow = mysql_fetch_object(mysql_query($Prosql));
        $requestdata['catagory'] = $Prorow->catagory;
        $requestdata['brand_code'] = $Prorow->brand_code;
        $requestdata['m_unit'] = $Prorow->m_unit;
        $requestdata['unit_price'] = $Prorow->unit_price;
        $requestdata['production_qty'] = getRequest('finish_qty');
        $requestdata['total_value'] = ($requestdata['unit_price'] * $finish_qty);

        $requestdata['project_id'] = getFromSession('project_id');
        $requestdata['created_by'] = getFromSession('userid');
        $production_id = $this->createFGBatchNo();
        $requestdata['created_time'] = date('Y-m-d h:i:s');
        if ($production_id != "") {
            $requestdata['batch_no'] = $production_id;
        } else {
            $msg = "ID overflow !!!";
            header("location:index.php?app=user_home&msg=$msg");
            exit;
        }
        $info = array();
        $info['table'] = PRODUCTION_FG_TBL;
        $info['data'] = $requestdata;
        //$info['debug']  =  true;
        $res = insert($info);
        if ($res) {
            return $production_id;
        } else {
            mysql_query("ROLLBACK;");
            $msg = "Failed finish goods in !!! Please try again";
            header("location:index.php?app=fg.production&cmd=infg&msg=$msg");
        }

    }

    function saveFinishProduction($production_id)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $store_id = getRequest('store_id');
        $project_id = getFromSession('project_id');
        $finish_product = getRequest('finish_product');
        $finish_qty = getRequest('finish_qty');
        $Prosql = "SELECT catagory,brand_code,m_unit,unit_price FROM " . PRODUCT_TBL . " WHERE product_id = '$finish_product' AND project_id = '$project_id'";
        $Prorow = mysql_fetch_object(mysql_query($Prosql));
        $catagory = $Prorow->catagory;
        $brand_id = $Prorow->brand_code;
        $m_unit = $Prorow->m_unit;
        $unit_price = $Prorow->unit_price;

        $total_value = ($unit_price * $finish_qty);
        $production_amount = ($unit_price * $finish_qty);

        $balanceQty = $this->getStockBalanceQty($finish_product, $project_id, $store_id);

        $balanceF = ($balanceQty + $finish_qty);
        $production_date = formatDate(getRequest('production_date'));

        $net_payble = $total_value;
        $purchase_date = formatDate(getRequest('production_date'));

        //$voucher_no= $this->saveInPurchaseTbl($production_id,$net_payble,$purchase_date,$catagory,$brand_id,$finish_product,$m_unit,$finish_qty,$unit_price);
        $this->saveStockJournal($production_id, $production_id, $project_id, $store_id, $finish_product, "Sales Item", "Production", $unit_price, $m_unit, $finish_qty, 0, $balanceF, $production_date);
        //=== Stock Dr =====
        $StockAmount = $total_value;


        $StockId = getRequest('inventory_type');
        if (!empty($StockId)) {
            $inventory_id = getRequest('inventory_id');
            if (!empty($inventory_id)) {
                $StockId = $inventory_id;
            }
        } else {
            if ($store_id == "D0026") {
                $StockId = $comListApp->getWPStockId(getFromSession('project_id'));
            } elseif ($store_id == "D0027") {
                $StockId = $comListApp->getMXStockId(getFromSession('project_id'));
            } else {
                $StockId = $comListApp->getFGStockId(getFromSession('project_id'));
            }
        }


        $StockPvBalance = $this->getTotalBalanceAmount($StockId, $project_id);
        $StockBalance = ($StockPvBalance + $StockAmount);
        $description = "FGP";
        $comListApp->saveAccJournal($production_id, $StockId, "Stock", "Finish Goods", getFromSession('project_id'), $description, $StockAmount, 0, $StockBalance, 0, $production_date);

    }

    function saveInPurchaseTbl($voucher_no, $net_payble, $purchase_date, $catagory, $brand_id, $product, $m_unit, $qty, $unit_price)
    {
        $created_date = date('Y-m-d h:i:s');
        $project_id = getFromSession('project_id');
        $created_by = getFromSession('userid');
        /*
        $sqlDV="INSERT INTO ".CREDIT_VOUCHAR_TBL."(voucher_no,project_id,transaction_type,vouchar_type,transaction_name,credit,list_view,created_by,created_date)
        VALUES('$voucher_no','$project_id','Production','Others Vouchar','Production','$net_payble','Hidden','$created_by','$created_date')";
        $res1= mysql_query($sqlDV);
        $sqlCV="INSERT INTO ".DEVIT_VOUCHAR_TBL."(voucher_no,project_id,transaction_type,vouchar_type,transaction_name,debit,paid_amount,due,list_view,created_by,created_date,status)
        VALUES('$voucher_no','$project_id','Production','Others Vouchar','Production','$net_payble','$net_payble','0','Hidden','$created_by','$created_date','1')";
        $res2=mysql_query($sqlCV);
        */
        $sqlM = "INSERT INTO " . PURCHASE_MASTER_TBL . "(voucher_no,project_id,purchase_date,purchase_type,total_value,net_payble,paid_amount,due,item_received_amount,created_by,created_date) 
		VALUES('$voucher_no','$project_id','$purchase_date','Production','$net_payble','$net_payble','$net_payble','0','$net_payble','$created_by','$created_date')";
        $res3 = mysql_query($sqlM);

        $sqlD = "INSERT INTO " . PURCHASE_DETAILS_TBL . "(voucher_no,project_id,catagory,brand_id,product,m_unit,unit_price,qty,rec_qty,total,created_by) 
		VALUES('$voucher_no','$project_id','$catagory','$brand_id','$product','$m_unit','$unit_price','$qty','$qty','$net_payble','$created_by')";
        $res4 = mysql_query($sqlD);

        return $voucher_no;
    }
    //========= End FG In ===========
    //=============FG Production List================
    function showEditor4ProductionDetails($msg = null)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['date_from'] = getRequest('date_from');
        $data['date_to'] = getRequest('date_to');
        $data['catagory'] = getRequest('catagory');
        $data['product'] = getRequest('product');
        $data['store_id'] = getRequest('store_id');
        $data['summaryby'] = getRequest('summaryby');
        $data['job_name'] = getRequest('job_name');
        $data['cmd'] = getRequest('cmd');

        $data['record_list'] = $this->getProductionList(getRequest('from'), getRequest('to'));
        $data['totalrecord'] = $this->getTotalProductionList();
        $data['depo_list'] = $comListApp->getDeliveryPointList();
        $data['catagory_list'] = $comListApp->getCatagoryList();
        $data['finish_list'] = $comListApp->getFinishProductList();
        $data['job_name_list'] = $this->getJobNameList();

        require_once(SHOW_PRODUCTION_FG_LIST_SKIN);
        return $data[0];
    }

    function formatDate($date)
    {

        // check if already Y-m-d
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }

        // check if d-m-Y and convert
        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $date)) {
            $d = DateTime::createFromFormat('d-m-Y', $date);
            return $d->format('Y-m-d');
        }

        // fallback (optional)
        return date('Y-m-d', strtotime($date));
    }


    function getProductionList($from, $to)
    {
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 100;
        }
        $date_from = $this->formatDate(getRequest('date_from'));
        $date_to = $this->formatDate(getRequest('date_to'));
        $catagory = getRequest('catagory');
        $product = getRequest('product');
        $store_id = getRequest('store_id');
        $summaryby = getRequest('summaryby');
        $job_name = getRequest('job_name');

        $production_type = "Finish";
        $project_id = getFromSession('project_id');
        $info = array();

        $info['table'] = PRODUCTION_FG_TBL . " pm
	    LEFT JOIN " . PROJECT_TBL . " pa ON pm.project_id = pa.project_id
	    LEFT JOIN " . PRODUCT_TBL . " p ON pm.finish_product = p.product_id
	    LEFT JOIN " . DELIVERY_POINT_TBL . " st ON pm.store_id = st.delivery_pid
	    LEFT JOIN " . FACTORY_TBL . " f ON pm.factory_id = f.factory_id
	    LEFT JOIN " . CURRENCY_TBL . " c ON pm.currency = c.currency_id
	    LEFT JOIN " . STOCK_TRANSFER_MASTER_TBL . " stm ON pm.job_name = stm.transfer_no";

        $info['fields'] = array('pm.batch_no', 'pm.finish_product', 'pa.project_name', 'pa.location', 'f.factory_name', 'f.address', 'st.delivery_point_name as in_store', 'SUM(pm.total_value) as total_value', 'pm.unit_price', 'pm.m_unit',
            'p.product_name', 'SUM(pm.production_qty) as finish_qty', "DATE_FORMAT(pm.production_date ,'%d %b %y' ) as used_date", 'pm.production_type', 'c.curr_symble', 'pm.created_by', 'pm.created_time', 'pm.job_name', 'stm.job_name as job_item_name');

        $sql = "pm.project_id = '" . $project_id . "' AND pm.production_type='" . $production_type . "'";

        if ($store_id != "") {
            $sql .= " AND pm.store_id = '$store_id'";
        }
        if ($catagory != "") {
            $sql .= " AND p.catagory = '$catagory'";
        }
        if ($product != "") {
            $sql .= " AND pm.finish_product = '$product'";
        }
        if ($job_name != "") {
            $sql .= " AND pm.job_name = '$job_name'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND pm.production_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND pm.production_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND pm.production_date BETWEEN '$date_from' AND '$date_to'";
        }

        $info['where'] = $sql;
        if ($summaryby == 1) {
            $info['groupby'] = array("pm.production_date,p.product_id");
        } elseif ($summaryby == 2) {
            $info['groupby'] = array("p.product_id");
        } else {
            $info['groupby'] = array("pm.batch_no");
        }
        $info['orderby'] = array("pm.batch_no asc LIMIT $from,$to");
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

    function getTotalProductionList()
    {
        $date_from = $this->formatDate(getRequest('date_from'));
        $date_to = $this->formatDate(getRequest('date_to'));
        $catagory = getRequest('catagory');
        $product = getRequest('product');
        $store_id = getRequest('store_id');
        $summaryby = getRequest('summaryby');
        $job_name = getRequest('job_name');

        $production_type = "Finish";
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = PRODUCTION_FG_TBL . " pm
	    LEFT JOIN " . PROJECT_TBL . " pa ON pm.project_id = pa.project_id
	    LEFT JOIN " . PRODUCT_TBL . " p ON pm.finish_product = p.product_id
	    LEFT JOIN " . DELIVERY_POINT_TBL . " st ON pm.store_id = st.delivery_pid
	    LEFT JOIN " . FACTORY_TBL . " f ON pm.factory_id = f.factory_id
	    LEFT JOIN " . CURRENCY_TBL . " c ON pm.currency = c.currency_id
	    LEFT JOIN " . STOCK_TRANSFER_MASTER_TBL . " stm ON pm.job_name = stm.transfer_no";

        $info['fields'] = array('pm.batch_no', 'pm.finish_product', 'pa.project_name', 'pa.location', 'f.factory_name', 'f.address', 'st.delivery_point_name as in_store', 'SUM(pm.total_value) as total_value', 'pm.unit_price', 'pm.m_unit',
            'p.product_name', 'SUM(pm.production_qty) as finish_qty', "DATE_FORMAT(pm.production_date ,'%d %b %y' ) as used_date", 'pm.production_type', 'c.curr_symble', 'pm.created_by', 'pm.created_time', 'pm.job_name', 'stm.job_name as job_item_name');

        $sql = "pm.project_id = '" . $project_id . "' AND pm.production_type='" . $production_type . "'";
        if ($store_id != "") {
            $sql .= " AND pm.store_id = '$store_id'";
        }
        if ($catagory != "") {
            $sql .= " AND p.catagory = '$catagory'";
        }
        if ($product != "") {
            $sql .= " AND pm.finish_product = '$product'";
        }
        if ($job_name != "") {
            $sql .= " AND pm.job_name = '$job_name'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND pm.production_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND pm.production_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND pm.production_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        if ($summaryby == 1) {
            $info['groupby'] = array("pm.production_date,p.product_id");
        } elseif ($summaryby == 2) {
            $info['groupby'] = array("p.product_id");
        } else {
            $info['groupby'] = array("pm.batch_no");
        }
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
    //=======End FG Production List=======

    //========= Stock Transfer =======
    function showEditor4StockTransfer($msg = null)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['finish_list'] = $comListApp->getFinishProductList();
        $data['cat_list'] = $this->getCatagoryList();
        $data['brand_list'] = $comListApp->getBrandList();
        $data['depo_list'] = $comListApp->getDeliveryPointList();
        if (getRequest('submit')) {
            mysql_query("START TRANSACTION;");
            mysql_query("SET autocommit=0;");
            $moveres = $this->moveStockQty();
            mysql_query("COMMIT;");
            if ($moveres) {
                header("location:index.php?app=sales.report&cmd=stock_status&msg=Successfully Transfer Stock");
            } else {
                header("location:index.php?app=fg.production&cmd=transfer&msg=Have Not Sufficient Stock Balance");
            }
        }
        $data['cmd'] = getRequest('cmd');
        $data['depo_list'] = $comListApp->getDeliveryPointList();
        require_once(STOCK_TRANSFER_SKIN);
        return $data[0];
    }

    function moveStockQty()
    {
        $project_id = getFromSession('project_id');
        $transfer_from = getRequest('transfer_from');
        $store_id = getRequest('store_id');
        $product = getRequest('transfer_product');
        $stock_qty = getRequest('stock_qty');
        $transfer_qty = getRequest('transfer_qty');
        $transfer_date = formatDate(getRequest('transfer_date'));
        if (($stock_qty > 0) && ($stock_qty >= $transfer_qty)) {
            $Pcsql = "SELECT product_type,m_unit,unit_price FROM " . PRODUCT_TBL . " WHERE product_id='$product' AND project_id='$project_id'";
            $Pcrow = mysql_fetch_object(mysql_query($Pcsql));
            $m_unit = $Pcrow->m_unit;
            $product_type = $Pcrow->product_type;
            $unit_price = $Pcrow->unit_price;
            //===== Cr Stock ======
            $totalFCR = $this->getTotalCreditStock($product, getFromSession('project_id'));
            $totalFDR = $this->getTotalDebitStock($product, getFromSession('project_id'));
            $TFbalance = ($totalFDR - ($totalFCR + $transfer_qty));
            $this->saveStockJournal($transfer_from, "TS", $project_id, $transfer_from, $product, $product_type, "Transfer Stock", $unit_price, $m_unit, 0, $transfer_qty, $TFbalance, $transfer_date);
            //===== Dr Stock ======
            $totalFCR = $this->getTotalCreditStock($product, getFromSession('project_id'));
            $totalFDR = $this->getTotalDebitStock($product, getFromSession('project_id'));
            $TTbalance = (($totalFDR + $transfer_qty) - $totalFCR);
            $this->saveStockJournal($transfer_from, "RS", $project_id, $store_id, $product, $product_type, "Received Stock", $unit_price, $m_unit, $transfer_qty, 0, $TTbalance, $transfer_date);
            return true;
        } else {
            return false;
        }
    }

    function loadProductStock($product_id)
    {
        $project_id = getFromSession('project_id');
        $transfer_stock = trim(getRequest('transfer_stock'));
        $info = array();
        $info['table'] = STORE_STOCK_VIEW;
        $info['fields'] = array('balance');
        $where = "product_id = '" . $product_id . "' AND store_id = '$transfer_stock' AND project_id = '$project_id'";
        $info['where'] = $where;
        $result = select($info);
        $data = array();
        if (count($result)) {
            foreach ($result as $key => $value) {
                $data[$key][] = $value;
            }
        }
        foreach ($data as $i => $v) {
            $str = $v[0]->balance . "#####";
        }
        echo $str;
    }

    //=========End Stock Transfer =======
    function getProductList()
    {
        $project_id = getFromSession('project_id');

        $info = array();
        $info['table'] = PRODUCT_TBL . " p 
        JOIN " . CATAGORY_TBL . " c ON p.catagory = c.catagory_code
        JOIN " . BRAND_TBL . " b ON p.brand_code = b.brand_id";

        $info['fields'] = array(
            'p.m_unit',
            'p.unit_price',
            'p.product_id',
            'p.product_code',
            'p.product_name',
            'p.catagory',
            'c.catagory_name',
            'p.brand_code',
            'b.brand_name'
        );

        $info['where'] = "p.project_id='$project_id' 
        AND p.product_type != 'Sales Item' AND p.approval_status = 1";

        $info['orderby'] = array("p.product_name ASC");

//        $info['debug'] = true;

        $res = select($info);

        return $res ?: [];
    }

    function loadProductInfo($product_id)
    {
        $project_id = getFromSession('project_id');
        $store_id = getRequest('store_id');

        $totalCRStock = $this->getTotalCreditStockQty($product_id, $project_id, $store_id);
        $totalDRStock = $this->getTotalDebitStockQty($product_id, $project_id, $store_id);
        $Stockbalance = ($totalDRStock - $totalCRStock);
        $info = array();
        $info['table'] = PRODUCT_TBL . " p," . CATAGORY_TBL . ' c,' . BRAND_TBL . ' b';
        $info['fields'] = array('p.m_unit', 'p.unit_price', 'p.catagory', 'c.catagory_name', 'p.brand_code', 'b.brand_name');
        $where = "p.catagory=c.catagory_code AND p.brand_code=b.brand_id AND p.product_id = '$product_id' AND p.project_id='$project_id'";

        $info['where'] = $where;
        $info['groupby'] = array("p.product_id");
        $result = select($info);
        $data = array();

        if (count($result)) {
            foreach ($result as $key => $value) {
                $data[$key][] = $value;
            }
        }

        foreach ($data as $i => $v) {

            $str = $v[0]->unit_price . "#####" . $Stockbalance . "#####" . $v[0]->m_unit . "#####" . $v[0]->catagory . "###" . $v[0]->catagory_name . "#####" . $v[0]->brand_code . "###" . $v[0]->brand_name;
        }

        echo $str;
    }


    function getProductInfo($product_id)
    {

        if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering

        $project_id = getFromSession('project_id');
        $store_id = getRequest('store_id');

        $totalCRStock = $this->getTotalCreditStockQty($product_id, $project_id, $store_id);
        $totalDRStock = $this->getTotalDebitStockQty($product_id, $project_id, $store_id);
        $Stockbalance = ($totalDRStock - $totalCRStock);

        $info = array();
        $info['table'] = PRODUCT_TBL . " p," . CATAGORY_TBL . ' c,' . BRAND_TBL . ' b';
        $info['fields'] = array('p.m_unit', 'p.unit_price', 'p.catagory', 'c.catagory_name', 'p.brand_code', 'b.brand_name');
        $where = "p.catagory=c.catagory_code AND p.brand_code=b.brand_id AND p.product_id = '$product_id' AND p.project_id='$project_id'";

        $info['where'] = $where;
        $info['groupby'] = array("p.product_id");
        //$info['debug'] = true;
        $result = select($info);
        $data = $result[0];


        $invoiceList = $this->getInvoiceList($store_id, $product_id, $project_id, $Stockbalance);

        $response = [
            "unit_price" => $data->unit_price,
            "Stockbalance" => $Stockbalance,
            "m_unit" => $data->m_unit,
            "catagory" => $data->catagory,
            "catagory_name" => $data->catagory_name,
            "brand_code" => $data->brand_code,
            "brand_name" => $data->brand_name,
            "invoiceList" => $invoiceList,
        ];

        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    function getInvoiceList($store_id, $product_id, $project_id, $Stockbalance)
    {
        $info = array();
        $info['table'] = STOCK_LEDGER_TBL . " sl";
        $info['fields'] = array("sl.*", "(SELECT IFNULL(SUM(pis.qty), 0) FROM production_invoice_stock pis WHERE pis.stock_ledger_id = sl.stock_id) AS production_qty");
        $info['where'] = "sl.product_id = '$product_id' AND sl.voucher_no LIKE 'PI%' AND sl.project_id = '$project_id' AND sl.store_id = '$store_id'";
        $result = select($info);

        $html = "<option value=''>Default Price</option>";

        if (!empty($result)) {
            foreach ($result as $item) {
                $stock_id = $item->stock_id;
                $unit_price = number_format($item->unit_price, 2);
                $voucher_no = $item->voucher_no;
                $production_qty = number_format($item->production_qty, 2);
                $left = (float)$Stockbalance - (float)$production_qty;

                $html .= '<option value="' . $stock_id . '" 
			    data-price="' . $unit_price . '" 
			    data-voucher_no="' . $voucher_no . '" 
			    data-left="' . $left . '" 
			    data-production_qty="' . $production_qty . '">
			    ' . $voucher_no . ' (Left: ' . $left . ')
			</option>';
            }
        }

        return $html;
    }


    function loadProduct4Catagory($catagory)
    {
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = PRODUCT_TBL;
        $info['fields'] = array('product_id', 'product_name');
        $info['where'] = "catagory = '$catagory' AND project_id = '$project_id'";
        $info['groupby'] = array("product_id");
        $info['debug'] = false;
        $result = select($info);
        $data = array();
        if (count($result)) {
            foreach ($result as $key => $value) {
                $data[$key][] = $value;
            }
        }
        foreach ($data as $i => $v) {
            $subject_idname .= $v[0]->product_id . '-' . $v[0]->product_name . ',';
        }
        echo $subject_idname;
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

    function getCashId($project_id)
    {
        $sql = "SELECT sub_id FROM " . SUB_ACC_HEAD_TBL . " WHERE head_type = 'Cash' AND project_id = '$project_id'";
        $row = mysql_fetch_object(mysql_query($sql));
        return $sub_id = $row->sub_id;
    }

    function getRecievableId($project_id)
    {
        $sql = "SELECT sub_id FROM " . SUB_ACC_HEAD_TBL . " WHERE head_type = 'Accounts Recievable' AND project_id = '$project_id'";
        $row = mysql_fetch_object(mysql_query($sql));
        return $sub_id = $row->sub_id;
    }

    function getPayableId($project_id)
    {
        $sql = "SELECT sub_id FROM " . SUB_ACC_HEAD_TBL . " WHERE head_type = 'Accounts Payable' AND project_id = '$project_id'";
        $row = mysql_fetch_object(mysql_query($sql));
        return $sub_id = $row->sub_id;
    }

    function getTotalCreditAmount($acc_head, $project_id)
    {
        $sql = "SELECT sum(`cr`) as credit_amount FROM " . ACCOUNT_JOURNAL_TBL . " WHERE sub_id = '$acc_head' AND project_id = '$project_id'";
        $row = mysql_fetch_object(mysql_query($sql));
        $credit_amount = $row->credit_amount;
        if (empty($credit_amount)) {
            $credit_amount = 0;
        }
        return $credit_amount;
    }

    function getTotalDebitAmount($acc_head, $project_id)
    {
        $sql = "SELECT sum(`dr`) as debit_amount FROM " . ACCOUNT_JOURNAL_TBL . " WHERE sub_id = '$acc_head' AND project_id = '$project_id'";
        $row = mysql_fetch_object(mysql_query($sql));
        $debit_amount = $row->debit_amount;
        if (empty($debit_amount)) {
            $debit_amount = 0;
        }
        return $debit_amount;
    }

    function getTotalCreditStockQty($acc_head, $project_id, $store_id)
    {
        $sql = "SELECT sum(`cr`) as credit_amount FROM " . STOCK_LEDGER_TBL . " WHERE product_id = '$acc_head' AND project_id = '$project_id' AND store_id='$store_id'";
        $row = mysql_fetch_object(mysql_query($sql));
        $credit_amount = $row->credit_amount;
        if (empty($credit_amount)) {
            $credit_amount = 0;
        }
        return $credit_amount;
    }

    function getTotalDebitStockQty($acc_head, $project_id, $store_id)
    {
        $sql = "SELECT sum(`dr`) as debit_amount FROM " . STOCK_LEDGER_TBL . " WHERE product_id = '$acc_head' AND project_id = '$project_id' AND store_id='$store_id'";
        $row = mysql_fetch_object(mysql_query($sql));

        $debit_amount = $row->debit_amount;
        if (empty($debit_amount)) {
            $debit_amount = 0;
        }
        return $debit_amount;
    }

    function getTotalCreditStock($acc_head, $project_id)
    {
        $sql = "SELECT sum(`cr`) as credit_amount FROM " . STOCK_LEDGER_TBL . " WHERE product_id = '$acc_head' AND project_id = '$project_id'";
        $row = mysql_fetch_object(mysql_query($sql));
        $credit_amount = $row->credit_amount;
        if (empty($credit_amount)) {
            $credit_amount = 0;
        }
        return $credit_amount;
    }

    function getTotalDebitStock($acc_head, $project_id)
    {
        $sql = "SELECT sum(`dr`) as debit_amount FROM " . STOCK_LEDGER_TBL . " WHERE product_id = '$acc_head' AND project_id = '$project_id'";
        $row = mysql_fetch_object(mysql_query($sql));
        $debit_amount = $row->debit_amount;
        if (empty($debit_amount)) {
            $debit_amount = 0;
        }
        return $debit_amount;
    }

    function getTotalBalanceAmount($acc_head, $project_id)
    {
        $sql = "SELECT (sum(`dr`) - sum(`cr`)) as balance_amount FROM " . ACCOUNT_JOURNAL_TBL . " WHERE sub_id = '$acc_head' AND project_id = '$project_id'";
        $row = mysql_fetch_object(mysql_query($sql));
        $balance_amount = $row->balance_amount;
        if (empty($balance_amount)) {
            $balance_amount = 0;
        }
        return $balance_amount;
    }

    function getStockBalanceQty($acc_head, $project_id, $store_id)
    {
        $sql = "SELECT (sum(`dr`) - sum(`cr`)) as balance_qty FROM " . STOCK_LEDGER_TBL . " WHERE product_id = '$acc_head' AND project_id = '$project_id'";
        if ($store_id != "") {
            $sql .= " AND store_id ='$store_id'";
        }
        $row = mysql_fetch_object(mysql_query($sql));
        $balance_qty = $row->balance_qty;
        if (empty($balance_qty)) {
            $balance_qty = 0;
        }
        return $balance_qty;
    }

    function saveStockJournal($voucher_no, $pvoucher_no, $project_id, $store_id, $product_id, $product_type, $note, $unit_price = NULL, $m_unit, $DR = NULL, $CR = NULL, $balance, $create_date = NULL)
    {
        $created_by = getFromSession('userid');
        $sql = "INSERT INTO " . STOCK_LEDGER_TBL . " (voucher_no,po_no,project_id,store_id,product_id,product_type,note,unit_price,m_unit,dr,cr,balance,created_by,create_date)
	 VALUES('" . $voucher_no . "','" . $pvoucher_no . "','" . $project_id . "','" . $store_id . "','" . $product_id . "','" . $product_type . "','" . $note . "','" . $unit_price . "','" . $m_unit . "','" . $DR . "','" . $CR . "','" . $balance . "','" . $created_by . "','" . $create_date . "')";
        mysql_query($sql);
    }

    function createFGBatchNo()
    {
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = PRODUCTION_FG_TBL;
        $info['fields'] = array('max(batch_no) as maxProduction');
        $res = select($info);
        $maxProductionId = 'FG000000000';
        if (count($res)) {
            foreach ($res as $v) {
                if ($v->maxProduction) {
                    $maxProductionId = $v->maxProduction;
                }
                break;
            }
        }
        $maxProductionId = generateID("FG", $maxProductionId, 11);
        return $maxProductionId;
    }

    function createProductionID()
    {
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = PRODUCTION_MASTER_TBL;
        $info['fields'] = array('max(production_id) as maxProduction');
        $info['where'] = "production_id LIKE 'P%'";
        $res = select($info);
        $maxProductionId = 'P0000000';
        if (count($res)) {
            foreach ($res as $v) {
                if ($v->maxProduction) {
                    $maxProductionId = $v->maxProduction;
                }
                break;
            }
        }
        $maxProductionId = generateID("P", $maxProductionId, 8);
        return $maxProductionId;
    }

    function createTempProductionID()
    {
        $info = array();
        $info['table'] = PRODUCTION_MASTER_TBL;
        $info['fields'] = array('max(production_id) as maxProduction');
        $info['where'] = "production_id LIKE 'TP%'";
        $res = select($info);
        $maxProductionId = 'TP0000000';
        if (count($res)) {
            foreach ($res as $v) {
                if ($v->maxProduction) {
                    $maxProductionId = $v->maxProduction;
                }
                break;
            }
        }
        $maxProductionId = generateID("TP", $maxProductionId, 8);
        return $maxProductionId;
    }

    function getProductionBatchID()
    {
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = PRODUCTION_MASTER_TBL;
        $info['fields'] = array('max(batch_no) as maxProduction');
        $info['where'] = "project_id = '$project_id'";
        $res = select($info);
        $maxProductionId = 'B000000';
        if (count($res)) {
            foreach ($res as $v) {
                if ($v->maxProduction) {
                    $maxProductionId = $v->maxProduction;
                }
                break;
            }
        }
        $maxProductionId = generateID("B", $maxProductionId, 7);
        return $maxProductionId;
    }

} // End class


?>
