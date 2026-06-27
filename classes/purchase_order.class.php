<?php

class PurchaseOrder
{

    function run()
    {

        $cmd = getRequest('cmd');
        $u_t_id = getFromSession('u_type_id');
        if (($u_t_id == 101) || ($u_t_id == 102) || ($u_t_id == 104) || ($u_t_id == 105) || ($u_t_id == 106)) // 1 = sysadmin, 2 = admin, 4 = acc, 5= production, 6=pur
        {
            switch ($cmd) {
                case 'add'                    :
                    $this->showEditor();
                    break;
                case 'addlc'                    :
                    $this->showEditor();
                    break;
                case 'edit'                    :
                    $this->showEditor();
                    break;
                case 'pur_dtl'                :
                    $this->showEditor4PurchaseDetails();
                    break;
                case 'get_munit'            :
                    $this->loadMUnite(trim(getRequest('product_id')));
                    break;
                case 'loadProduct'            :
                    $this->loadProduct4Catagory(trim(getRequest('brand_id')));
                    break;
                case 'savePurchase'        :
                    $this->saveDebitVouchar();
                    break;
                case 'print_vouchar'        :
                    $screen = $this->showPrintEditor($msg);
                    break;
                case 'delete'                :
                    $screen = $this->deleteRecord(getRequest('id'));
                    break;
                case 'savePurchaseOrder'     :
                    $this->savePurchaseOrder();
                    break;
                case 'po_list'        :
                    $this->showPurchaseOrderList();
                    break;
                case 'po_edit'        :
                    $this->showPurchaseOrderEditPage();
                    break;
                case 'po_update'        :
                    $this->addPODetails();
                    break;
                case 'po_approved'        :
                    $this->approvedPO();
                    break;
                case 'po_delete'        :
                    $this->deletePurchaseOrder();
                    break;
                case 'po_details_delete'        :
                    $this->deletePurchaseOrderDetails();
                    break;
                case 'po_print_vouchar'        :
                    $this->showPurchaseOrderPrintVoucher();
                    break;
                case 'updatePurchaseOrder'        :
                    $this->updatePurchaseOrder();
                    break;
                case 'sendInvoiceMail'        :
                    $this->sendInvoiceMail();
                    break;
                case 'po_details_edit'        :
                    $this->showEditPurchaseDetails();
                    break;
                case 'approved_po_edit'        :
                    $this->showEditApprovedPurchaseDetails();
                    break;
                case 'approved_po_delete'        :
                    $this->deleteApprovedPurchase();
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

    function showPrintEditor($msg = null)
    {

        $voucher_no = getRequest('voucher_no');
        if ($voucher_no) {
            $advArr = $this->getPurchaseMasterInfo($voucher_no);
            $advArr = parseThisValue($advArr);
            $data = array_merge(array(), $advArr);

            $data['item_list'] = $this->getProductList($voucher_no);
            $data['message'] = $msg;
            $data['cmd'] = getRequest('cmd');
            require_once(PURCHASE_VOUCHAR_SKIN);
            return true;
        } else {
            require_once(PRINT_VOUCHAR_SKIN);
        }
    }


    function getSPRList()
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = SPR_PURCHASE_MASTER_TBL;
        $info['where'] = "project_id = '$project_id' AND complete_status=0 AND approved_status=1";
        $info['orderby'] = array("voucher_no DESC");
        $res = select($info);

        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        return $data;
    }


    function showEditor($msg = null)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();

        $supplier_list = $comListApp->getSupplierListCombined();
        $impoter_list = $comListApp->getImpoterList();
        $cost_center_list = $comListApp->getAccountHeadList("Cost Center");

        $data['supplier_list'] = array_merge($supplier_list, $impoter_list, $cost_center_list);

        $supplierData = $comListApp->getSupplierData();
        $data['supplierData'] = json_encode($supplierData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $data['lc_list'] = $comListApp->getAccountList("Current Liabilities", "S137", "C000116", "S300032");
        $data['payable_list'] = $comListApp->getAccountList("Current Assets", "S127", "C000186");

        $data['cat_list'] = $this->getCatagoryList();
        $data['currency_list'] = $this->getCurrencyList();
        $data['brand_list'] = $comListApp->getBrandList();
        $data['spr_list'] = $this->getSPRList();
        $data['product_list'] = $comListApp->getProductList();
        $data['terms_list'] = $comListApp->getTermsAndConditionList();
        $data['cmd'] = getRequest('cmd');
        if (getRequest('cmd') == "addlc") {

            require_once(CLASS_DIR . '/supplier.class.php');
            $supApp = new Supplier();
            $data['country_list'] = $supApp->getCountryList();
            require_once(LC_OPENING_SKIN);
        } else {
            require_once(CURRENT_APP_SKIN_FILE);
        }
        return $data[0];
    }

    function insertPurchaseDetails($voucher_no)
    {
        $requestdata = array();
        $arr_catagory_product_id = array();

        $project_id = getFromSession('project_id');
        $currency = getRequest('currency');
        $total_value = getRequest('total_value');
        $discount = getRequest('discount');
        $discount_persent = (($discount / $total_value) * 100);
        $arr_catagory_product_id = getRequest('input_catagory_product_id');
        $arr_brandid = getRequest('input_brandid');
        $arr_brand = getRequest('input_brand');
        $arr_pdetails = getRequest('input_pdetails');
        $spr_voucher_arr = getRequest('spr_voucher_no');
        $spd_id_arr = getRequest('spd_id');
        $arr_m_unit = getRequest('input_m_unit');
        $arr_total_unit = getRequest('input_total_unit');
        $arr_unit_price = getRequest('input_unit_price');
        $arr_qty = getRequest('input_qty');
        $arr_total_bag = getRequest('input_total_bag');
        $arr_currency = getRequest('input_currency');
        $arr_total_value = getRequest('input_total_value');

        for ($i = 0; $i < count($arr_catagory_product_id); $i++) {
            $catagory_product_sep = $arr_catagory_product_id[$i];
            $requestdata['project_id'] = $project_id;

            for ($j = 0; $j < count($catagory_product_sep); $j++) {
                $catagory_product = explode("###", $catagory_product_sep);
                $catagoryid = array();
                $productid = array();
                $staff_no = array();
                $catagoryid['c'] = $catagory_product[0];
                $productid['p'] = $catagory_product[1];
            }

            foreach ($catagoryid as $val) {
                $requestdata['catagory'] = $val;
            }

            foreach ($productid as $val) {
                $requestdata['product'] = $val;
                $product_id = $val;
            }

            foreach ($arr_m_unit as $key => $val) {

                if ($catagory_product_sep == $key) {
                    $requestdata['m_unit'] = $val;
                }

            }
            foreach ($arr_brand as $key => $val) {

                if ($catagory_product_sep == $key) {
                    $requestdata['brandname'] = $val;
                }

            }
            foreach ($arr_brandid as $key => $val) {

                if ($catagory_product_sep == $key) {
                    $requestdata['brand_id'] = $val;
                }

            }
            foreach ($arr_pdetails as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $requestdata['details'] = $val;
                }
            }

            foreach ($arr_total_unit as $key => $val) {

                if ($catagory_product_sep == $key) {
                    $requestdata['total_unit'] = $val;
                }

            }

            foreach ($arr_unit_price as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $requestdata['unit_price'] = $val;
                }
            }

            foreach ($arr_qty as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $requestdata['qty'] = $val;
                    $productQty = $val;
                }
            }

            foreach ($arr_total_bag as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $requestdata['total_bag'] = $val;
                }
            }

            foreach ($arr_currency as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $requestdata['currency'] = $val;
                }
            }

            foreach ($arr_total_value as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $requestdata['total'] = $val;
                }

            }

            $spr_voucher = "";
            $spd_id = "";
            foreach ($spr_voucher_arr as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $spr_voucher = $val;
                }

            }
            foreach ($spd_id_arr as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $spd_id = $val;
                }
            }

            if ($spr_voucher != "" && $spd_id != "") {
                $requestDetailsdata['complete_status'] = 1;
                $info = array();
                $info['table'] = SPR_PURCHASE_DETAILS_TBL;
                $info['data'] = $requestDetailsdata;
                $info['where'] = "voucher_no='$spr_voucher' AND id='$spd_id'";
                $res = update($info);
            }

            $perQtyAmount = ($requestdata['total'] / $productQty);
            $requestdata['discount_per_qty'] = $discount_persent;
            $requestdata['discount_amount'] = (($perQtyAmount / 100) * $discount_persent);

            $requestdata['created_by'] = getFromSession('userid');
            $requestdata['created_date'] = date('Y-m-d h:i:s');
            $project_id = getFromSession('project_id');
            $requestdata['project_id'] = $project_id;
            $requestdata['voucher_no'] = $voucher_no;
            $requestdata['po_no'] = getRequest('po_no');
            $requestdata['lc_no'] = getRequest('lc_no');

            $info = array();
            $info['table'] = PURCHASE_DETAILS_TBL;
            $info['data'] = $requestdata;
            //dumpvar($info);
            //$info['debug']  	=  true;
            $res = insert($info);

        }

        if (count($spr_voucher_arr)) {
            $unique_voucher_arr = array_values(array_unique($spr_voucher_arr));
            foreach ($unique_voucher_arr as $spr_voucher_no) {
                $info = array();
                $info['table'] = SPR_PURCHASE_DETAILS_TBL;
                $info['fields'] = array('voucher_no', 'complete_status');
                $info['where'] = "voucher_no='$spr_voucher_no' AND complete_status=0";
                //$info['debug'] = false;
                $result = select($info);
                if (count($result) <= 0) {
                    $requestDetailsdata['complete_status'] = 1;
                    $info = array();
                    $info['table'] = SPR_PURCHASE_MASTER_TBL;
                    $info['data'] = $requestDetailsdata;
                    $info['where'] = "voucher_no='$spr_voucher_no'";
                    //$info['debug']  	=  true;
                    update($info);
                }
            }
        }

    } //End of the function savePaymentDetails()

    //==================== saveDebitVouchar ====================
    function saveDebitVouchar()
    {
        $mode_of_payment = getRequest('mode_of_payment');

        $requestdata = array();

        $requestdata = getUserDataSet(DEVIT_VOUCHAR_TBL);
        if ($mode_of_payment == "Payable") {
            $requestdata['account_head'] = $this->getPayableId(getFromSession('project_id'));
            $requestdata['debit'] = getRequest('net_payble');
            $requestdata['credit'] = 0;
            $requestdata['head_type'] = "Acc";
        }
        $requestdata['transaction_type'] = "Purchase Order";
        $requestdata['project_id'] = getFromSession('project_id');
        $requestdata['created_by'] = getFromSession('userid');
        $requestdata['created_date'] = formatDate(getRequest('purchase_date'));

        $voucher_no = $this->createVoucharID();

        if ($voucher_no != -1) {
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
            $this->saveCreditVouchar($voucher_no);
        } else {
            header("location:index.php?app=purchase_order&cmd=add");
        }

    }//EOFn

    function saveCreditVouchar($voucher_no)
    {
        $mode_of_payment = getRequest('mode_of_payment');

        $requestdata = array();

        $requestdata = getUserDataSet(CREDIT_VOUCHAR_TBL);
        if ($mode_of_payment == "Payable") {
            //======= Party Dr ======
            $requestdata['account_head'] = getRequest('supplier');
            $requestdata['credit'] = getRequest('net_payble');
            $requestdata['debit'] = 0;
            $requestdata['head_type'] = "Supplier";
        }
        $requestdata['transaction_type'] = "Payment";
        $requestdata['project_id'] = getFromSession('project_id');
        $requestdata['created_by'] = getFromSession('userid');

        $requestdata['created_date'] = formatDate(getRequest('purchase_date')); //date('Y-m-d h:i:s');
        $requestdata['voucher_no'] = $voucher_no;

        $info = array();
        $info['table'] = CREDIT_VOUCHAR_TBL;
        $info['data'] = $requestdata;
        $res = insert($info);
        $created_date = $requestdata['created_date'];

        if ($res['affected_rows']) {
            $CrAmount = getRequest('net_payble');
            if ($mode_of_payment == "Payable") {
                //======= Supplier Cr ======
                $fullCr = getRequest('net_payble');
                $PartyAcc_head1 = getRequest('supplier');
                $totalPartyCR1 = $this->getTotalCreditAmount($PartyAcc_head1, getFromSession('project_id'));
                $totalPartyDR1 = $this->getTotalDebitAmount($PartyAcc_head1, getFromSession('project_id'));
                $PartyBalance1 = ($totalPartyDR1 - ($totalPartyCR1 + $fullCr));
                $this->saveAccountJournal($voucher_no, $PartyAcc_head1, "Supplier", getFromSession('project_id'), getRequest('description'), 0, $fullCr, $PartyBalance1, 1, $created_date);

                //=========== Payable Dr ========
                $fullpayble = getRequest('net_payble');
                $payable_head = $this->getPayableId(getFromSession('project_id'));
                $totalPayableCR = $this->getTotalCreditAmount($payable_head, getFromSession('project_id'));
                $totalPayableDR = $this->getTotalDebitAmount($payable_head, getFromSession('project_id'));
                $payableBalance = (($totalPayableDR + $fullpayble) - $totalPayableCR);
                $this->saveAccountJournal($voucher_no, $payable_head, "Acc", getFromSession('project_id'), getRequest('description'), $fullpayble, 0, $payableBalance, 1, $created_date);

            }

            $this->insertPurchaseMaster($voucher_no);
            $this->insertPurchaseDetails($voucher_no);
            header("location:index.php?app=purchase_order&cmd=print_vouchar&voucher_no=" . $voucher_no);
        } else {
            header("location:index.php?app=purchase_order&cmd=add");
        }

    }//EOFn

    function insertPurchaseMaster($voucher_no)
    {
        $requestdata = array();
        $requestdata = getUserDataSet(PURCHASE_MASTER_TBL);
        if (getRequest('lcopening_date') != "") {
            $requestdata['lcopening_date'] = formatDate(getRequest('lcopening_date'));
        }
        $requestdata['purchase_type'] = "Purchase Order";
        $requestdata['purchase_date'] = formatDate(getRequest('purchase_date'));
        $requestdata['voucher_no'] = $voucher_no;
        $requestdata['project_id'] = getFromSession('project_id');
        $requestdata['created_by'] = getFromSession('userid');

        $requestdata['created_date'] = date('Y-m-d h:i:s');

        $info = array();
        $info['table'] = PURCHASE_MASTER_TBL;
        $info['data'] = $requestdata;
        //$info['debug']  	=  true;
        $res = insert($info);

    }

    function getPurchaseMasterInfo($id)
    {

        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = PURCHASE_MASTER_TBL . ' pm,' . SUB_ACC_HEAD_TBL . ' s,' . PROJECT_TBL . ' p,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('pm.voucher_no', 'p.project_name', 'p.location', 's.sub_head_name as name', 's.head_details as address', 'pm.quotation_no', 'pm.po_no', 'pm.lc_no', 'pm.lcopener', 'pm.lcopening_bank', "DATE_FORMAT(pm.lcopening_date,'%d %b %y' ) as lcopening_date", 'pm.country', 'pm.lc_details', 'pm.track_no', 'pm.van_no', 'pm.total_value', "DATE_FORMAT(pm.purchase_date,'%d %b %y' ) as purchase_date", 'pm.mode_of_payment', 'c.curr_symble', 'pm.bank_name', 'pm.acc_no', 'pm.check_no', 'pm.check_no', 'pm.discount', 'pm.net_payble', 'pm.paid_amount', 'pm.due', 'pm.ref_no', 'pm.created_date');

        $sql = "pm.supplier = s.sub_id AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '" . $project_id . "' AND pm.voucher_no = '$id'";

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


    /* Purchase Order start */
    function createPOVoucharID()
    {
        $project_id = getFromSession('project_id');

        $info['table'] = PURCHASE_OREDR_MASTER_TBL;
        $info['fields'] = array('max(voucher_no) as maxvoucher');
        $info['where'] = "voucher_no LIKE 'PO%' AND project_id='" . $project_id . "'";
        $res = select($info);
        $maxvoucherId = 'PO0000000';
        if (count($res)) {
            foreach ($res as $v) {
                if ($v->maxvoucher) {
                    $maxvoucherId = $v->maxvoucher;
                }
                break;
            }

        }
        $maxvoucherId = generateID("PO", $maxvoucherId, 9);
        return $maxvoucherId;
    }

    function updatePurchaseOrder()
    {
        $voucher_no = getRequest('voucher_no');

        if ($voucher_no != "") {
            $currency = getRequest('currency');
            $currency = explode("###", $currency);
            $currency_id = $currency[0];
            $currency_name = $currency[1];

            $raw = getRequest('terms_condition');
            $term_condition = is_array($raw) ? $raw : explode(',', $raw);
            $term_condition = array_filter($term_condition); // remove empty values

            $termCondition = implode(",", $term_condition);

            $requestdata = array();
            $requestdata = getUserDataSet(PURCHASE_OREDR_MASTER_TBL);
            $requestdata['supplier_id'] = getRequest('supplier');
            $requestdata['purchase_type'] = getRequest('purchase_type');
            $requestdata['spr_no'] = getRequest('spr_no_value');
            $requestdata['manual_spr_no'] = getRequest('manual_spr_no');
            $requestdata['order_date'] = formatDate(getRequest('order_date'));
            $requestdata['delivery_date'] = formatDate(getRequest('delivery_date'));
            $requestdata['currency'] = $currency_id;
            $requestdata['currencyName'] = $currency_name;
            $requestdata['quotation_no'] = getRequest('quotation_no');
            $requestdata['payment_note'] = getRequest('payment_note');
            $requestdata['delivery_note'] = getRequest('delivery_note');
            $requestdata['track_no'] = getRequest('track_no');
            $requestdata['van_no'] = getRequest('van_no');

            $requestdata['mode_of_payment'] = getRequest('mode_of_payment');
            $discount_amount = getRequest('discount');
            if ($discount_amount == "") {
                $discount_amount = 0;
            }
            $requestdata['discount_amount'] = $discount_amount;
            $total_value = getRequest('total_value');
            if ($total_value == "") {
                $total_value = 0;
            }
            $requestdata['remark'] = getRequest('remark');
            $requestdata['total'] = $total_value;
            $additional_cost = getRequest('additional_cost');
            if ($additional_cost == "") {
                $additional_cost = 0;
            }
            $requestdata['additional_cost'] = $additional_cost;


            $requestdata['vat_type'] = getRequest('vat_type');
            $requestdata['total_vat_percent'] = getRequest('total_vat_percent');
            $total_vat_amount = getRequest('total_vat_amount');
            $requestdata['total_vat_amount'] = $total_vat_amount;

            $requestdata['tds_type'] = getRequest('tds_type');
            $requestdata['total_tds_percent'] = getRequest('total_tds_percent');
            $total_tds_amount = getRequest('total_tds_amount');
            $requestdata['total_tds_amount'] = $total_tds_amount;

            $net_payble = getRequest('net_payble');
            if ($net_payble == "") {
                $net_payble = ($total_value + $additional_cost + $total_vat_amount) - ($total_tds_amount + $discount_amount);
            }

            $requestdata['net_payable'] = $net_payble;
            $requestdata['term_condition'] = $termCondition;

            $cost_center = "";
            $lc_no = "";
            $payable_id = "";
            if ($requestdata['purchase_type'] == "import") {
                $cost_center = getRequest('cost_center');
                $lc_no = getRequest('lc_no');
                $payable_id = getRequest('payable_id');
            }
            $requestdata['cost_center'] = $cost_center;
            $requestdata['lc_no'] = $lc_no;
            $requestdata['payable_id'] = $payable_id;

            //dd($requestdata);
            $info = array();
            $info['table'] = PURCHASE_OREDR_MASTER_TBL;
            $info['data'] = $requestdata;
            $info['where'] = "voucher_no='$voucher_no'";
            $res = update($info);

            $msg = "Record updated successfully!!!";
            header("location:index.php?app=purchase_order&cmd=po_list&msg=$msg");
            exit();
        } else {
            $msg = "Voucher not found !!!";
            header("location:index.php?app=purchase_order&cmd=po_list&error_msg=$msg");
            exit();
        }
    }

    function savePurchaseOrder()
    {
        $project_id = getFromSession('project_id');
        $voucher_no = $this->createPOVoucharID();

        if ($voucher_no && $voucher_no != "") {
            $currency = getRequest('currency');
            $currency = explode("###", $currency);
            $currency_id = $currency[0];
            $currency_name = $currency[1];

            $arr_catagory_product_id = getRequest('input_catagory_product_id');
            if (count($arr_catagory_product_id) <= 0) {
                $msg = "Please add product first !!!";
                header("location:index.php?app=purchase_order&cmd=add&msg=$msg");
                exit();
            }

            $raw = getRequest('terms_condition');
            $term_condition = is_array($raw) ? $raw : explode(',', $raw);
            $term_condition = array_filter($term_condition); // remove empty values

            $termCondition = implode(",", $term_condition);

            $requestdata = array();
            $requestdata = getUserDataSet(PURCHASE_OREDR_MASTER_TBL);
            $requestdata['voucher_no'] = $voucher_no;
            $requestdata['project_id'] = $project_id;

            $requestdata['supplier_id'] = getRequest('supplier');
            $requestdata['purchase_type'] = getRequest('purchase_type');
            $requestdata['spr_no'] = getRequest('spr_no_value');
            $requestdata['manual_spr_no'] = getRequest('manual_spr_no');
            $requestdata['order_date'] = formatDate(getRequest('order_date'));
            $requestdata['delivery_date'] = formatDate(getRequest('delivery_date'));
            $requestdata['currency'] = $currency_id;
            $requestdata['currencyName'] = $currency_name;
            $requestdata['quotation_no'] = getRequest('quotation_no');
            $requestdata['payment_note'] = getRequest('payment_note');
            $requestdata['delivery_note'] = getRequest('delivery_note');
            $requestdata['track_no'] = getRequest('track_no');
            $requestdata['van_no'] = getRequest('van_no');
            $requestdata['mode_of_payment'] = getRequest('mode_of_payment');
            $discount_amount = getRequest('discount');
            if ($discount_amount == "") {
                $discount_amount = 0;
            }
            $requestdata['discount_amount'] = $discount_amount;
            $total_value = getRequest('total_value');
            if ($total_value == "") {
                $total_value = 0;
            }
            $requestdata['remark'] = getRequest('remark');
            $requestdata['total'] = $total_value;

            $additional_cost = getRequest('additional_cost');
            if ($additional_cost == "") {
                $additional_cost = 0;
            }
            $requestdata['additional_cost'] = $additional_cost;

            $requestdata['vat_type'] = getRequest('vat_type');
            $requestdata['total_vat_percent'] = getRequest('total_vat_percent');
            $total_vat_amount = getRequest('total_vat_amount');
            $requestdata['total_vat_amount'] = $total_vat_amount;

            $requestdata['tds_type'] = getRequest('tds_type');
            $requestdata['total_tds_percent'] = getRequest('total_tds_percent');
            $total_tds_amount = getRequest('total_tds_amount');
            $requestdata['total_tds_amount'] = $total_tds_amount;

            $net_payble = getRequest('net_payble');
            if ($net_payble == "") {
                $net_payble = ($total_value + $additional_cost + $total_vat_amount) - ($total_tds_amount + $discount_amount);
            }

            $requestdata['net_payable'] = $net_payble;
            $requestdata['created_by'] = getFromSession('userid');
            $requestdata['term_condition'] = $termCondition;

            $cost_center = "";
            $lc_no = "";
            $payable_id = "";
            if ($requestdata['purchase_type'] == "import") {
                $cost_center = getRequest('cost_center');
                $lc_no = getRequest('lc_no');
                $payable_id = getRequest('payable_id');
            }
            $requestdata['cost_center'] = $cost_center;
            $requestdata['lc_no'] = $lc_no;
            $requestdata['payable_id'] = $payable_id;

            $info = array();
            $info['table'] = PURCHASE_OREDR_MASTER_TBL;
            $info['data'] = $requestdata;

            $res = insert($info);

            if ($res['affected_rows']) {
                $this->savePODetails($voucher_no);
            } else {
                $msg = "Something wrong !!!";
                header("location:index.php?app=purchase_order&cmd=add&msg=$msg");
                exit();
            }

            header("location:index.php?app=purchase_order&cmd=po_print_vouchar&voucher_no=$voucher_no&showVat=true");
            exit();
        } else {
            $msg = "ID overflow !!!";
            header("location:index.php?app=purchase_order&cmd=add&msg=$msg");
            exit();
        }

    }

    function savePODetails($voucher_no)
    {
        $requestdata = array();
        $project_id = getFromSession('project_id');
        $currency = getRequest('currency');
        $total_value = getRequest('total_value');
        $discount = getRequest('discount');
        $discount_persent = (($discount / $total_value) * 100);
        $arr_catagory_product_id = getRequest('input_catagory_product_id');
        $arr_brandid = getRequest('input_brandid');
        $arr_brand = getRequest('input_brand');
        $arr_custom_brand = getRequest('input_custom_brand');
        $arr_pdetails = getRequest('input_pdetails');
        $spr_voucher_arr = getRequest('spr_voucher_no');
        $spd_id_arr = getRequest('spd_id');
        $arr_m_unit = getRequest('input_m_unit');
        $arr_total_unit = getRequest('input_total_unit');
        $arr_unit_price = getRequest('input_unit_price');
        $arr_vat_percent = getRequest('input_vat_percent');
        $arr_vat_amount = getRequest('input_vat_amount');
        $arr_qty = getRequest('input_qty');
        $arr_max_qty = getRequest('input_max_qty');
        $arr_total_bag = getRequest('input_total_bag');
        $arr_currency = getRequest('input_currency');
        $arr_total_value = getRequest('input_total_value');

        for ($i = 0; $i < count($arr_catagory_product_id); $i++) {
            $catagory_product_sep = $arr_catagory_product_id[$i];
            $productMaxQty = null;
            $requestdata = array();
            $requestdata = getUserDataSet(PURCHASE_ORDER_DETAILS_TBL);
            $requestdata['project_id'] = $project_id;
            $requestdata['voucher_no'] = $voucher_no;

            for ($j = 0; $j < count($catagory_product_sep); $j++) {
                $catagory_product = explode("###", $catagory_product_sep);
                $catagoryid = array();
                $productid = array();
                $staff_no = array();
                $catagoryid['c'] = $catagory_product[0];
                $productid['p'] = $catagory_product[1];
            }

            foreach ($catagoryid as $val) {
                $requestdata['catagory_id'] = $val;
            }

            foreach ($productid as $val) {
                $requestdata['product_id'] = $val;
                $product_id = $val;
            }

            foreach ($arr_m_unit as $key => $val) {

                if ($catagory_product_sep == $key) {
                    $requestdata['m_unit'] = $val;
                }

            }
//            foreach ($arr_brand as $key => $val) {
//
//                if ($catagory_product_sep == $key) {
//                    $requestdata['brandname'] = $val;
//                }
//
//            }
            foreach ($arr_custom_brand as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $requestdata['custom_brand'] = $val;
                }
            }
            foreach ($arr_brandid as $key => $val) {

                if ($catagory_product_sep == $key) {
                    $requestdata['brand_id'] = $val;
                }

            }
            foreach ($arr_pdetails as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $requestdata['details'] = $val;
                }
            }

//            foreach ($arr_total_unit as $key => $val) {
//
//                if ($catagory_product_sep == $key) {
//                    $requestdata['total_unit'] = $val;
//                }
//
//            }

            foreach ($arr_unit_price as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $requestdata['unit_price'] = $val;
                }
            }

            foreach ($arr_vat_percent as $key => $val) {
                if ($catagory_product_sep == $key) {

                    $requestdata['vat_percent'] = $val == "" ? 0 : $val;
                }
            }

            foreach ($arr_vat_amount as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $requestdata['vat_amount'] = $val == "" ? 0 : $val;
                }
            }

            foreach ($arr_qty as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $requestdata['qty'] = (float)$val;
                    $requestdata['init_qty'] = $requestdata['qty'];
                    $productQty = $val;
                }
            }

            foreach ($arr_max_qty as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $productMaxQty = (float)$val;
                }
            }

//            foreach ($arr_total_bag as $key => $val) {
//                if ($catagory_product_sep == $key) {
//                    $requestdata['total_bag'] = $val;
//                }
//            }

//            foreach ($arr_currency as $key => $val) {
//                if ($catagory_product_sep == $key) {
//                    $requestdata['currency'] = $val;
//                }
//            }

            foreach ($arr_total_value as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $requestdata['total'] = $val;
                }
            }

            $spr_voucher = "";
            $spd_id = "";
            foreach ($spr_voucher_arr as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $spr_voucher = $val;
                    $requestdata['spr_no'] = !empty($val) ? $val : null;
                }
            }
            foreach ($spd_id_arr as $key => $val) {
                if ($catagory_product_sep == $key) {
                    $spd_id = $val;
                    $requestdata['spd_id'] = !empty($val) ? $val : null;
                }
            }

            if ($spr_voucher != "" && $spd_id != "") {
                if ($productMaxQty && $productMaxQty == $requestdata['qty']) {
                    $requestDetailsdata['complete_status'] = 1;
                } else {
                    $requestDetailsdata['qty'] = $productMaxQty - $requestdata['qty'];
                }

                $info = array();
                $info['table'] = SPR_PURCHASE_DETAILS_TBL;
                $info['data'] = $requestDetailsdata;
                $info['where'] = "voucher_no='$spr_voucher' AND id='$spd_id'";
                //$info['debug'] = true;
                $res = update($info);
            }

            $requestdata['created_by'] = getFromSession('userid');

            $info = array();
            $info['table'] = PURCHASE_ORDER_DETAILS_TBL;
            $info['data'] = $requestdata;
            //dumpvar($info);
            //$info['debug']  	=  true;
            insert($info);
        }

        if (count($spr_voucher_arr)) {
            $unique_voucher_arr = array_values(array_unique($spr_voucher_arr));
            foreach ($unique_voucher_arr as $spr_voucher_no) {
                $info = array();
                $info['table'] = SPR_PURCHASE_DETAILS_TBL;
                $info['fields'] = array('voucher_no', 'complete_status');
                $info['where'] = "voucher_no='$spr_voucher_no' AND complete_status=0";
                //$info['debug'] = false;
                $result = select($info);
                if (count($result) <= 0) {
                    $requestDetailsdata['complete_status'] = 1;
                    $info = array();
                    $info['table'] = SPR_PURCHASE_MASTER_TBL;
                    $info['data'] = $requestDetailsdata;
                    $info['where'] = "voucher_no='$spr_voucher_no'";
                    //$info['debug']  	=  true;
                    update($info);
                }
            }
        }
    }

    function getPurchaseOrderDetailsList($voucher_no)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $project_id = getFromSession('project_id');

        $tableRow = "";
        $total_value = 0;

        $getSql = "
		SELECT pod.*,po.product_name,po.product_code,ct.catagory_name,b.brand_name FROM " . PURCHASE_ORDER_DETAILS_TBL . " AS pod
		LEFT JOIN " . PRODUCT_TBL . " AS po ON po.product_id=pod.product_id
		LEFT JOIN " . CATAGORY_TBL . " AS ct ON ct.catagory_code=pod.catagory_id
		LEFT JOIN " . BRAND_TBL . " AS b ON b.brand_id=pod.brand_id
		WHERE pod.voucher_no = '$voucher_no' AND pod.project_id = '$project_id'";

        $gres = mysql_query($getSql);
        $rowCount = mysql_num_rows($gres);

        while ($row = mysql_fetch_array($gres)) {
            extract($row);
            $discountAmount += $discount_amount;
            $qty = $qty;
            $unit_price = $unit_price;
            //$total = $qty * $unit_price;
            $total = bcmul($qty, $unit_price, 11);
            //$total_value += $total;
            $total_value = bcadd($total_value, $total, 11);
            $total = $total;
            if (!empty($custom_brand)) {
                $brand_name = $custom_brand;
            }

            $itemLock = false;
            if ($complete_status == 1 || ($qty != $init_qty)) {
                $itemLock = true;
            }

            $productName = (new CommonList())->normalizeProductName($product_code, $product_name);

            $tableRow .= "
            <tr style='color:#000000' bgcolor='#fff'>
              <td width='10%' nowrap align='left'>$catagory_name</td>
              <td width='10%' nowrap align='left'>$brand_name</td>
              <td width='20%' nowrap align='left'>$productName</td>
              <td width='5%' nowrap align='left'>$m_unit</td>
              <td width='8%' nowrap><div align='left'>$qty</div></td>
              <td width='8%' nowrap align='left'>$unit_price $currencyName</td>	
              <td style='display:none' width='8%' nowrap><div align='left'>$vat_percent</div></td>
              <td style='display:none' width='8%' nowrap align='left'>$vat_amount</td>		  
              <td width='10%' nowrap align='left'>$total</td>";
            if ($itemLock) {
                $tableRow .= "<td width='15%' nowrap align='left' style='padding: 7px 10px;'><div class='table-option'>Not permit</div></td>
            </tr>";
            } else {
                $tableRow .= "<td width='15%' nowrap align='left' style='padding: 7px 10px;'><div class='table-option'><a href='?app=purchase_order&cmd=po_details_edit&voucher_no=$voucher_no&id=$id' class='option_link'><img src=\"images/common/icons/edit.gif\"></a><a onclick=showConfirmDelete(event,'$rowCount') href='?app=purchase_order&cmd=po_details_delete&voucher_no=$voucher_no&spr_no=$spr_no&spd_id=$spd_id&id=$id' class='option_link'><img src=\"images/common/icons/delete.gif\" style='margin-left: 10px;'></a></div></td>
            </tr>";
            }
        }

        $net_payable = (float)$total - (float)$discountAmount;
        $response = [
            'row' => $tableRow,
            'total_value' => $total_value,
        ];

        return $response;
    }


    function showPurchaseOrderEditPage()
    {
        $voucher_no = getRequest('voucher_no');
        if ($voucher_no != "") {
            require_once(CLASS_DIR . '/common.list.class.php');
            $comListApp = new CommonList();

            $advArr = $this->getPurchaseOrderMasterInfo($voucher_no);
            $advArr = parseThisValue($advArr);
            $data = array_merge(array(), $advArr);

            if ($data['approved_status'] == 1) {
                $msg = "Not Authorize to edit !!!";
                header("location:index.php?app=purchase_order&cmd=po_list&error_msg=$msg");
                exit;
            }

            $supplier_list = $comListApp->getSupplierListCombined();
            $impoter_list = $comListApp->getImpoterList();
            $cost_center_list = $comListApp->getAccountHeadList("Cost Center");

            $data['supplier_list'] = array_merge($supplier_list, $impoter_list, $cost_center_list);

            $supplierData = $comListApp->getSupplierData();
            $data['supplierData'] = json_encode($supplierData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            $data['lc_list'] = $comListApp->getAccountList("Current Liabilities", "S137", "C000116", "S300032");
            $data['payable_list'] = $comListApp->getAccountList("Current Assets", "S127", "C000186");

            $data['currency_list'] = $this->getCurrencyList();
            $data['spr_list'] = $this->getSPRList();
            $data['product_list'] = $comListApp->getProductList();
            $data['terms_list'] = $comListApp->getTermsAndConditionList();
            $data['cmd'] = getRequest('cmd');

            $data['itemList'] = $this->getPurchaseOrderDetailsList($voucher_no);

            require_once(PURCHASE_ORDER_EDIT_SKIN);
            return $data[0];
        } else {
            $msg = "Missing Voucher No !!!";
            header("location:index.php?app=purchase_order&cmd=po_list&error_msg=$msg");
            exit();
        }
    }

    function getPurchaseOrderList($total = false)
    {
        $from = getRequest('from');
        $to = getRequest('to');
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 100;
        }

        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $supplier_id = getRequest('supplier_id');
        $project_id = getFromSession('project_id');
        $complete_status = getRequest('complete_status');
        if ($complete_status != 1) {
            $complete_status = 0;
        }

        $info = array();
        $info['table'] = PURCHASE_OREDR_MASTER_TBL . ' pom,' . PROJECT_TBL . ' p';
        $info['fields'] = array('pom.*', 'p.project_name', 'p.location', "DATE_FORMAT(pom.order_date,'%d %b %y' ) as formated_order_date");

        $sql = "pom.project_id = p.project_id AND pom.project_id = '$project_id' AND pom.complete_status='$complete_status'";
        if ($supplier_id != "") {
            $sql .= " AND pom.supplier_id = '$supplier_id'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND DATE(pom.created_time) >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND DATE(pom.created_time) <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND DATE(pom.created_time) BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        if ($total) {
            $info['orderby'] = array("pom.created_time DESC");
        } else {
            $info['orderby'] = array("pom.created_time DESC LIMIT $from,$to");
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
        if ($total) {
            return $cnt;
        } else {
            return $data;
        }
    }

    function showPurchaseOrderList()
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();

        $supplier_list = $comListApp->getSupplierListCombined();
        $impoter_list = $comListApp->getImpoterList();
        $cost_center_list = $comListApp->getAccountHeadList("Cost Center");

        $data['supplier_list'] = array_merge($supplier_list, $impoter_list, $cost_center_list);

        $data['cmd'] = getRequest('cmd');
        $data['complete_status'] = getRequest('complete_status');

        $data['record_list'] = $this->getPurchaseOrderList();
        $data['totalrecord'] = $this->getPurchaseOrderList(true);

        require_once(PURCHASE_ORDER_LIST_SKIN);
        return $data[0];
    }


    function addPODetails()
    {
        if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering

        $input = json_decode(file_get_contents('php://input'), true);

        // Validate input
        if (json_last_error() !== JSON_ERROR_NONE || !isset($input['voucher_no'])) {
            die(json_encode([
                'status' => false,
                'message' => 'Invalid JSON data received'
            ]));
        }

        $project_id = getFromSession('project_id');
        $voucher_no = trim($input['voucher_no']);
        $spr_no = trim($input['spr_no']);
        $po_details_id = trim($input['po_details_id']);
        $edit_product_id = trim($input['edit_product_id']);
        $edit_max_qty = trim($input['edit_max_qty']);
        $edit_min_qty = trim($input['edit_min_qty']);
        $edit_spr_no = trim($input['edit_spr_no']);
        $edit_spd_id = trim($input['edit_spd_id']);

        $response = [
            'status' => false,
            'message' => "Data must be fillable"
        ];

        $product_id = "";
        $productData = trim($input['product_id']);
        if ($productData != "") {
            $productData = explode("###", $productData);
            $product_id = $productData[0];
        }

        if ($voucher_no != "") {
            if ($spr_no != "") {
                $info = array();
                $info['table'] = SPR_PURCHASE_DETAILS_TBL;
                $info['where'] = "voucher_no = '$spr_no' AND complete_status = '0'";
                //$info['debug']  = true;
                $result = select($info);

                if (count($result)) {
                    foreach ($result as $value) {
                        $product_id = $value->product;

                        $pinfo = array();
                        $pinfo['table'] = PURCHASE_ORDER_DETAILS_TBL;
                        $pinfo['where'] = "voucher_no = '$voucher_no' AND spr_no = '$spr_no' AND product_id = '$product_id'";
                        //$info['debug']  = true;
                        $presult = select($pinfo);
                        $updateRow = false;
                        $prevQty = 0;
                        if (count($presult)) {
                            $updateRow = true;
                            foreach ($presult as $pvalue) {
                                $prevQty += (float)$pvalue->qty;
                            }
                        }

                        $requestdata = array();
                        $spd_id = $value->id;
                        $requestdata['voucher_no'] = $voucher_no;
                        $requestdata['project_id'] = $project_id;
                        $requestdata['spr_no'] = $spr_no;
                        $requestdata['spd_id'] = $spd_id;

                        $requestdata['catagory_id'] = $value->catagory;
                        $requestdata['brand_id'] = $value->brand_id;
                        $requestdata['product_id'] = $value->product;

                        $requestdata['m_unit'] = $value->m_unit;
                        $requestdata['unit_price'] = (float)$value->unit_price;
                        $newQty = (float)$value->qty;

                        //$requestdata['qty'] = $newQty + $prevQty;
                        //$requestdata['total'] = $requestdata['unit_price'] * $requestdata['qty'];

                        $requestdata['qty'] = bcadd($newQty, $prevQty, 11);
                        $requestdata['total'] = bcmul($requestdata['unit_price'], $requestdata['qty'], 11);

                        $requestdata['created_by'] = getFromSession('userid');

                        $info = array();
                        $info['table'] = PURCHASE_ORDER_DETAILS_TBL;
                        $info['data'] = $requestdata;
                        //dumpvar($info);
                        //$info['debug']  	=  true;

                        if ($updateRow) {
                            $info['where'] = "voucher_no = '$voucher_no' AND product_id = '$product_id'";
                            update($info);
                        } else {
                            insert($info);
                        }

                        $requestSPDdata = array();
                        $requestSPDdata['complete_status'] = 1;
                        $spinfo = array();
                        $spinfo['table'] = SPR_PURCHASE_DETAILS_TBL;
                        $spinfo['data'] = $requestSPDdata;
                        $spinfo['where'] = "voucher_no='$spr_no' AND id='$spd_id'";
                        //$info['debug']  	=  true;
                        update($spinfo);
                    }
                }

                $requestMasterdata = array();
                $requestMasterdata['complete_status'] = 1;
                $minfo = array();
                $minfo['table'] = SPR_PURCHASE_MASTER_TBL;
                $minfo['data'] = $requestMasterdata;
                $minfo['where'] = "voucher_no='$spr_no'";
                //$info['debug']  	=  true;
                update($minfo);
            } else if ($po_details_id != "" && $edit_product_id != "") {
                if ($edit_product_id == $product_id) {
                    //update row info
                    $custom_brand = trim($input['custom_brand']);
                    $details = trim($input['details']);
                    $unit_price = trim($input['unit_price']);
                    $vat_percent = trim($input['vat_percent']);
                    $vat_amount = trim($input['vat_amount']);
                    $qty = trim($input['qty']);
                    $total = trim($input['total']);
                    $init_qty = $qty;

                    if (!empty($edit_min_qty) && $edit_min_qty >= 0) {
                        $init_qty = bcadd((string)$init_qty, (string)$edit_min_qty, 11);
                    }

                    if ($unit_price == '' || $qty == '' || $total == '') {
                        $response = [
                            'status' => false,
                            'message' => "Product Data must be fillable !!!"
                        ];

                        header('Content-Type: application/json');
                        echo json_encode($response);
                        exit();
                    }

                    $requestdata['custom_brand'] = $custom_brand;
                    $requestdata['details'] = $details;
                    $requestdata['unit_price'] = $unit_price;
                    $requestdata['vat_percent'] = (float)$vat_percent;
                    $requestdata['vat_amount'] = (float)$vat_amount;
                    $requestdata['qty'] = $qty;
                    $requestdata['init_qty'] = $init_qty;
                    //$requestdata['total'] = (float)$unit_price * (float)$qty;
                    $requestdata['total'] = bcmul($unit_price, $qty, 11);

                    $info = array();
                    $info['table'] = PURCHASE_ORDER_DETAILS_TBL;
                    $info['data'] = $requestdata;
                    //dumpvar($info);
                    //$info['debug']  	=  true;
                    $info['where'] = "id='$po_details_id' AND voucher_no='$voucher_no'";
                    update($info);

                    // then update SPR Qty
                    $edit_max_qty = (float)$edit_max_qty;
                    $qty = (float)$qty;
                    if ($edit_max_qty != $qty) {
                        $newQty = $edit_max_qty - $qty;
                        if ($newQty > 0) {
                            $this->updateSPRDetailsQty($edit_spd_id, $newQty, $edit_spr_no, $edit_max_qty);
                        }
                    }

                } else {
                    //update row info with new product data
                    $catagory_id = trim($input['catagory_id']);
                    $brand_id = trim($input['brand_id']);
                    $custom_brand = trim($input['custom_brand']);
                    $details = trim($input['details']);
                    $m_unit = trim($input['m_unit']);
                    $unit_price = trim($input['unit_price']);
                    $vat_percent = trim($input['vat_percent']);
                    $vat_amount = trim($input['vat_amount']);
                    $qty = trim($input['qty']);
                    $total = trim($input['total']);
                    $init_qty = $qty;

                    if (!empty($edit_min_qty) && $edit_min_qty >= 0) {
                        $init_qty = bcadd((string)$init_qty, (string)$edit_min_qty, 11);
                    }

                    if ($catagory_id == '' || $brand_id == '' || $product_id == '' || $m_unit == '' || $unit_price == '' || $qty == '' || $total == '') {
                        $response = [
                            'status' => false,
                            'message' => "Product Data must be fillable !!!"
                        ];

                        header('Content-Type: application/json');
                        echo json_encode($response);
                        exit();
                    }

                    $requestdata['spr_no'] = "";
                    $requestdata['spd_id'] = "";
                    $requestdata['catagory_id'] = $catagory_id;
                    $requestdata['brand_id'] = $brand_id;
                    $requestdata['custom_brand'] = $custom_brand;
                    $requestdata['product_id'] = $product_id;
                    $requestdata['details'] = $details;
                    $requestdata['m_unit'] = $m_unit;
                    $requestdata['unit_price'] = (float)$unit_price;
                    $requestdata['vat_percent'] = (float)$vat_percent;
                    $requestdata['vat_amount'] = (float)$vat_amount;
                    $requestdata['qty'] = (float)$qty;
                    $requestdata['init_qty'] = (float)$init_qty;
                    $requestdata['total'] = (float)$total;

                    $info = array();
                    $info['table'] = PURCHASE_ORDER_DETAILS_TBL;
                    $info['data'] = $requestdata;
                    //dumpvar($info);
                    //$info['debug']  	=  true;
                    $info['where'] = "id='$po_details_id' AND voucher_no='$voucher_no'";
                    update($info);

                    $this->updateSPRDetailsQty($edit_spd_id, $edit_max_qty, $edit_spr_no, $edit_max_qty);
                }

            } else {
                $catagory_id = trim($input['catagory_id']);
                $brand_id = trim($input['brand_id']);
                $custom_brand = trim($input['custom_brand']);
                $details = trim($input['details']);
                $m_unit = trim($input['m_unit']);
                $unit_price = trim($input['unit_price']);
                $vat_percent = trim($input['vat_percent']);
                $vat_amount = trim($input['vat_amount']);
                $qty = trim($input['qty']);
                $total = trim($input['total']);

                if ($catagory_id == '' || $brand_id == '' || $product_id == '' || $m_unit == '' || $unit_price == '' || $qty == '' || $total == '') {
                    $response = [
                        'status' => false,
                        'message' => "Product Data must be fillable !!!"
                    ];

                    header('Content-Type: application/json');
                    echo json_encode($response);
                    exit();
                }

                $requestdata['voucher_no'] = $voucher_no;
                $requestdata['project_id'] = $project_id;
                $requestdata['catagory_id'] = $catagory_id;
                $requestdata['brand_id'] = $brand_id;
                $requestdata['custom_brand'] = $custom_brand;
                $requestdata['product_id'] = $product_id;
                $requestdata['details'] = $details;
                $requestdata['m_unit'] = $m_unit;
                $requestdata['unit_price'] = (float)$unit_price;
                $requestdata['vat_percent'] = (float)$vat_percent;
                $requestdata['vat_amount'] = (float)$vat_amount;
                $requestdata['qty'] = (float)$qty;
                $requestdata['total'] = (float)$total;
                $requestdata['created_by'] = getFromSession('userid');

                $info = array();
                $info['table'] = PURCHASE_ORDER_DETAILS_TBL;
                $info['data'] = $requestdata;
                //dumpvar($info);
                //$info['debug']  	=  true;
                insert($info);
            }

            $poinfo = array();
            $poinfo['table'] = PURCHASE_ORDER_DETAILS_TBL;
            $poinfo['where'] = "voucher_no='$voucher_no'";
            //$info['debug']  = true;
            $result = select($poinfo);

            $totalAmount = 0;
            $sprNoArray = [];

            if (count($result)) {
                foreach ($result as $dvalue) {
                    $totalAmount += (float)$dvalue->total;
                    // Collect spr_no values if not empty
                    if (!empty($dvalue->spr_no)) {
                        $sprNoArray[] = $dvalue->spr_no;
                    }
                }
            }

            $totalDiscount = (float)trim($input['discount']);
            $net_payable = $totalAmount - $totalDiscount;

            // Make spr_no unique and comma-separated
            $sprNoArray = array_unique($sprNoArray);
            $sprNoString = implode(',', $sprNoArray);

            $requestPOMasterdata = array();
            $requestPOMasterdata['discount_amount'] = $totalDiscount;
            $requestPOMasterdata['total'] = $totalAmount;
            $requestPOMasterdata['net_payable'] = $net_payable;
            $requestPOMasterdata['spr_no'] = $sprNoString;
            $info = array();
            $info['table'] = PURCHASE_OREDR_MASTER_TBL;
            $info['data'] = $requestPOMasterdata;
            $info['where'] = "voucher_no='$voucher_no'";

            //$info['debug']  	=  true;
            update($info);

            $response = [
                'status' => true,
                'message' => "Product Added Successfully!!!"
            ];
        } else {
            $response = [
                'status' => false,
                'message' => "Missing Voucher No !!!"
            ];
        }

        // Output response
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();

    }


    function approvedPO()
    {
        $voucher_no = getRequest('voucher_no');
        $approved_permission = getFromSession('approved_permission');
        if ($approved_permission == 0) {
            $msg = "Not authorize to approved !!!";
            header("location:index.php?app=purchase_order&cmd=po_list&error_msg=$msg");
            exit;
        }
        if ($voucher_no != "") {
            $sql = "SELECT approved_status, complete_status FROM " . PURCHASE_OREDR_MASTER_TBL . " WHERE voucher_no='$voucher_no'";
            $res = mysql_query($sql);
            if (mysql_num_rows($res) <= 0) {
                $msg = "Record not found!!!";
                header("location:index.php?app=purchase_order&cmd=po_list&error_msg=$msg");
                exit;
            }
            $row = mysql_fetch_object($res);
            if (isset($row->approved_status) && $row->approved_status == 1) {
                $msg = "Already approved !!!";
                header("location:index.php?app=purchase_order&cmd=po_list&error_msg=$msg");
                exit;
            }

            $requestData = array();
            $requestData['approved_status'] = 1;
            $requestData['approved_by'] = getFromSession('userid');
            $infoM['table'] = PURCHASE_OREDR_MASTER_TBL;
            $infoM['data'] = $requestData;
            $infoM['where'] = "voucher_no='$voucher_no'";
            update($infoM);

            $msg = "Record Approved successfully !!!";
            header("location:index.php?app=purchase_order&cmd=po_list&msg=$msg");
            exit;
        } else {
            $msg = "Missing Voucher No !!!";
            header("location:index.php?app=purchase_order&cmd=po_list&error_msg=$msg");
            exit;
        }

    }


    function updateSPRDetailsQty($edit_spd_id, $add_qty, $edit_spr_no, $edit_max_qty)
    {

        if ($edit_spd_id != "") {
            // then update SPR full Qty back
            $poinfo = array();
            $poinfo['table'] = SPR_PURCHASE_DETAILS_TBL;
            $poinfo['where'] = "id='$edit_spd_id'";
            //$info['debug']  = true;
            $result = select($poinfo);

            $aprTotalQty = 0;
            $sprTotalInitQty = 0;

            if (count($result)) {
                foreach ($result as $sprValue) {
                    $aprTotalQty += (float)$sprValue->qty;
                    $sprTotalInitQty += (float)$sprValue->init_qty;
                }
            }

            if ($sprTotalInitQty != $edit_max_qty) {
                $newSPRQty = $aprTotalQty + (float)$add_qty;
            } else {
                $newSPRQty = (float)$add_qty;
            }

            $requestSPRDdata = array();
            $requestSPRDdata['qty'] = $newSPRQty;
            $requestSPRDdata['complete_status'] = 0;
            $sprDinfo = array();
            $sprDinfo['table'] = SPR_PURCHASE_DETAILS_TBL;
            $sprDinfo['data'] = $requestSPRDdata;
            $sprDinfo['where'] = "id='$edit_spd_id'";
            //$info['debug']  	=  true;

            update($sprDinfo);
        }

        if ($edit_spr_no != "") {
            $requestMasterdata = array();
            $requestMasterdata['complete_status'] = 0;
            $minfo = array();
            $minfo['table'] = SPR_PURCHASE_MASTER_TBL;
            $minfo['data'] = $requestMasterdata;
            $minfo['where'] = "voucher_no='$edit_spr_no'";
            //$info['debug']  	=  true;
            update($minfo);
        }
    }


    function showEditPurchaseDetails()
    {
        $id = getRequest('id');
        $voucher_no = getRequest('voucher_no');
        if ($voucher_no != "") {
            require_once(CLASS_DIR . '/common.list.class.php');
            $comListApp = new CommonList();

            $advArr = $this->getPurchaseOrderMasterInfo($voucher_no);
            $advArr = parseThisValue($advArr);
            $data = array_merge(array(), $advArr);

            $supplier_list = $comListApp->getSupplierList();
            $supplier_list_payable = $comListApp->getSupplierListPayable();
            $data['supplier_list'] = array_merge($supplier_list, $supplier_list_payable);
            $data['currency_list'] = $this->getCurrencyList();
            $data['spr_list'] = $this->getSPRList();
            $data['product_list'] = $comListApp->getProductList();
            $data['terms_list'] = $comListApp->getTermsAndConditionList();
            $data['cmd'] = getRequest('cmd');

            $data['itemList'] = $this->getPurchaseOrderDetailsList($voucher_no);
            $data['pod_details'] = $this->getPurchaseOrderItemDetailsInfo($id, $voucher_no);

            $data['approved_order_edit'] = false;
            $userType = getFromSession('u_type_id');
            if (in_array($userType, [101, 107]) && userNotAllowed() && $data['approved_status'] == 1) {
                $data['approved_order_edit'] = true;
            }

            require_once(PURCHASE_ORDER_EDIT_SKIN);
            return $data[0];
        } else {
            $msg = "Missing Voucher No !!!";
            header("location:index.php?app=purchase_order&cmd=po_list&error_msg=$msg");
            exit();
        }

    }


    function getPurchaseOrderItemDetailsInfo($id, $voucher_no)
    {
        $project_id = getFromSession('project_id');
        $getSql = "
		SELECT pod.*,po.product_name,po.product_code,ct.catagory_name,b.brand_name,pd.qty as pd_qty,pd.pod_id as pd_pod_id, (
			SELECT SUM(qty) 
			FROM " . PURCHASE_DETAILS_TBL . " 
			WHERE po_no = pod.voucher_no
		) as total_pd_qty FROM " . PURCHASE_ORDER_DETAILS_TBL . " AS pod
		LEFT JOIN " . PRODUCT_TBL . " AS po ON po.product_id=pod.product_id
		LEFT JOIN " . CATAGORY_TBL . " AS ct ON ct.catagory_code=pod.catagory_id
		LEFT JOIN " . BRAND_TBL . " AS b ON b.brand_id=pod.brand_id
		LEFT JOIN " . PURCHASE_DETAILS_TBL . " AS pd ON pd.pod_id=pod.id
		WHERE pod.id = '$id' AND pod.voucher_no = '$voucher_no' AND pod.project_id = '$project_id'";

        $result = query($getSql);

        $data = array();
        $cnt = count($result);
        if ($cnt) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }

        return $data[0];

    }


    function deleteApprovedPurchase()
    {
        $voucher_no = getRequest('voucher_no');
        if ($voucher_no) {
            $getSql = "SELECT * FROM " . PURCHASE_DETAILS_TBL . " WHERE po_no = '$voucher_no'";
            $resultCount = count(query($getSql));

            if ($resultCount) {
                $msg = "Cannot delete: this purchase order has already been used !!!";
                header("location:index.php?app=purchase_order&cmd=po_list&error_msg=$msg");
                exit;
            }

            $userType = getFromSession('u_type_id');
            if (in_array($userType, [101, 107]) && userNotAllowed() && $resultCount == 0) {
                $this->deletePurchaseOrder(false);
                $msg = "Successfully delete Record !!!";
                header("location:index.php?app=purchase_order&cmd=po_list&msg=$msg");
                exit;
            } else {
                $msg = "Not Authorize to delete !!!";
                header("location:index.php?app=purchase_order&cmd=po_list&error_msg=$msg");
                exit;
            }
        } else {
            $msg = "Missing Voucher No !!!";
            header("location:index.php?app=purchase_order&cmd=po_list&error_msg=$msg");
            exit;
        }
    }

    function showEditApprovedPurchaseDetails()
    {
        $voucher_no = getRequest('voucher_no');
        if ($voucher_no != "") {
            require_once(CLASS_DIR . '/common.list.class.php');
            $comListApp = new CommonList();

            $userType = getFromSession('u_type_id');
            if (in_array($userType, [101, 107]) && userNotAllowed()) {
                $advArr = $this->getPurchaseOrderMasterInfo($voucher_no);
                $advArr = parseThisValue($advArr);
                $data = array_merge(array(), $advArr);

                $supplier_list = $comListApp->getSupplierListCombined();
                $impoter_list = $comListApp->getImpoterList();
                $cost_center_list = $comListApp->getAccountHeadList("Cost Center");

                $data['supplier_list'] = array_merge($supplier_list, $impoter_list, $cost_center_list);

                $supplierData = $comListApp->getSupplierData();
                $data['supplierData'] = json_encode($supplierData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

                $data['currency_list'] = $this->getCurrencyList();
                $data['spr_list'] = $this->getSPRList();
                $data['product_list'] = $comListApp->getProductList();
                $data['terms_list'] = $comListApp->getTermsAndConditionList();
                $data['cmd'] = getRequest('cmd');

                $data['itemList'] = $this->getPurchaseOrderDetailsList($voucher_no);

                require_once(PURCHASE_ORDER_EDIT_SKIN);
                return $data[0];
            } else {
                $msg = "Not Authorize to edit !!!";
                header("location:index.php?app=purchase_order&cmd=po_list&error_msg=$msg");
                exit;
            }
        } else {
            $msg = "Missing Voucher No !!!";
            header("location:index.php?app=purchase_order&cmd=po_list&error_msg=$msg");
            exit();
        }
    }

    function deletePurchaseOrderDetails()
    {
        $spr_no = getRequest('spr_no');
        $spd_id = getRequest('spd_id');
        $id = getRequest('id');
        $voucher_no = getRequest('voucher_no');

        if ($id != "") {

            $getSql = "SELECT * FROM " . PURCHASE_ORDER_DETAILS_TBL . " WHERE id = '$id'";
            $result = mysql_fetch_object(query($getSql));
            $qty = $result->qty;
            $init_qty = $result->init_qty;
            $complete_status = $result->complete_status;

            if ($complete_status == 1 || ($qty != $init_qty)) {
                $msg = "Cannot delete: this purchase order has already been used !!!";
                header("location:index.php?app=purchase_order&cmd=approved_po_edit&voucher_no=$voucher_no&error_msg=$msg");
                exit;
            }


            if ($spr_no != "" && $spd_id != "") {
                // then update SPR full Qty back
                $poinfo = array();
                $poinfo['table'] = SPR_PURCHASE_DETAILS_TBL;
                $poinfo['where'] = "id='$spd_id'";
                //$info['debug']  = true;
                $result = select($poinfo);

                $aprTotalQty = 0;
                $completeStatus = 0;

                if (count($result)) {
                    foreach ($result as $sprValue) {
                        $aprTotalQty += (float)$sprValue->qty;
                        $completeStatus = $sprValue->complete_status;
                    }
                }

                if ($completeStatus == 0) {
                    $podinfo = array();
                    $podinfo['table'] = PURCHASE_ORDER_DETAILS_TBL;
                    $podinfo['where'] = "id='$id'";
                    //$info['debug']  = true;
                    $result = select($podinfo);

                    $podTotalQty = 0;

                    if (count($result)) {
                        foreach ($result as $podvalue) {
                            $podTotalQty += (float)$podvalue->qty;
                        }
                    }

                    $newQty = (float)$aprTotalQty + (float)$podTotalQty;
                    $requestDetailsdata['qty'] = $newQty;
                }

                $requestDetailsdata['complete_status'] = 0;
                $info = array();
                $info['table'] = SPR_PURCHASE_DETAILS_TBL;
                $info['data'] = $requestDetailsdata;
                $info['where'] = "voucher_no='$spr_no' AND id='$spd_id'";
                //$info['debug'] = false;
                update($info);

                $requestMasterdata['complete_status'] = 0;
                $info = array();
                $info['table'] = SPR_PURCHASE_MASTER_TBL;
                $info['data'] = $requestMasterdata;
                $info['where'] = "voucher_no='$spr_no'";
                //$info['debug'] = false;
                update($info);
            }

            $info = array();
            $info['table'] = PURCHASE_ORDER_DETAILS_TBL;
            $info['where'] = "id='$id'";
            //$info['debug'] = false;
            $res = delete($info);


            $poinfo = array();
            $poinfo['table'] = PURCHASE_ORDER_DETAILS_TBL;
            $poinfo['where'] = "voucher_no='$voucher_no'";
            //$info['debug']  = true;
            $result = select($poinfo);

            $sprNoArray = [];

            if (count($result)) {
                foreach ($result as $dvalue) {
                    // Collect spr_no values if not empty
                    if (!empty($dvalue->spr_no)) {
                        $sprNoArray[] = $dvalue->spr_no;
                    }
                }
            } else {
                //here delete po
                header("location:index.php?app=purchase_order&cmd=po_delete&voucher_no=$voucher_no");
                exit();
            }


            // Make spr_no unique and comma-separated
            $sprNoArray = array_unique($sprNoArray);
            $sprNoString = implode(',', $sprNoArray);

            $requestPOMasterdata = array();
            $requestPOMasterdata['spr_no'] = $sprNoString;
            $info = array();
            $info['table'] = PURCHASE_OREDR_MASTER_TBL;
            $info['data'] = $requestPOMasterdata;
            $info['where'] = "voucher_no='$voucher_no'";

            //$info['debug']  	=  true;
            update($info);

            $msg = "";
            header("location:index.php?app=purchase_order&cmd=po_edit&voucher_no=$voucher_no");
            exit();
        }

        $msg = "Missing voucher no!!";
        header("location:index.php?app=purchase_order&cmd=po_list&msg=$msg");
        exit();
    }

    function deletePurchaseOrder($checkApproved = true)
    {
        $voucher_no = getRequest('voucher_no');
        if ($voucher_no) {
            $advArr = $this->getPurchaseOrderMasterInfo($voucher_no);
            $data = parseThisValue($advArr);

            if ($checkApproved && $data['approved_status'] == 1) {
                $msg = "Not Authorize to delete !!!";
                header("location:index.php?app=purchase_order&cmd=po_list&error_msg=$msg");
                exit;
            }

            $info = array();
            $info['table'] = PURCHASE_ORDER_DETAILS_TBL;
            $info['where'] = "voucher_no = '$voucher_no'";
            //$info['debug']  = true;
            $result = select($info);

            $sprMasterVoucher = [];

            if (count($result)) {
                foreach ($result as $povalue) {
                    $spd_id = $povalue->spd_id;
                    $spr_no = $povalue->spr_no;
                    $delQty = $povalue->qty;
                    $sprMasterVoucher[] = $spr_no;

                    $sql = "SELECT * FROM " . SPR_PURCHASE_DETAILS_TBL . " WHERE voucher_no='$spr_no' AND id='$spd_id'";
                    $sprResult = mysql_fetch_object(mysql_query($sql));
                    $sprInitQty = $sprResult->init_qty;
                    $sprPrevQty = $sprResult->qty;
                    $newQty = (float)$sprPrevQty + (float)$delQty;
                    if ($sprInitQty == $sprPrevQty || $sprInitQty < $newQty) {
                        $newQty = (float)$sprInitQty;
                    }
                    $requestSPRdata['qty'] = $newQty;
                    $requestSPRdata['complete_status'] = 0;

                    $dinfo = array();
                    $dinfo['table'] = SPR_PURCHASE_DETAILS_TBL;
                    $dinfo['data'] = $requestSPRdata;
                    $dinfo['where'] = "voucher_no='$spr_no' AND id='$spd_id'";
                    //$info['debug']  	=  true;
                    update($dinfo);
                }
            }

            $uniqueSPRVoucher_arr = array_values(array_unique($sprMasterVoucher));
            foreach ($uniqueSPRVoucher_arr as $spr_voucher_no) {
                $requestSPRMdata['complete_status'] = 0;
                $dinfo = array();
                $dinfo['table'] = SPR_PURCHASE_MASTER_TBL;
                $dinfo['data'] = $requestSPRMdata;
                $dinfo['where'] = "voucher_no='$spr_voucher_no'";
                //$info['debug']  	=  true;
                update($dinfo);
            }


            $info = array();
            $info['table'] = PURCHASE_ORDER_DETAILS_TBL;
            $info['where'] = "voucher_no='$voucher_no'";
            //$info['debug'] = false;
            $res = delete($info);

            $info = array();
            $info['table'] = PURCHASE_OREDR_MASTER_TBL;
            $info['where'] = "voucher_no='$voucher_no'";
            //$info['debug'] = false;
            $res = delete($info);

            $msg = "Successfully delete Record !!!";
            header("location:index.php?app=purchase_order&cmd=po_list&msg=$msg");
            exit;
        } else {
            $msg = "Missing Voucher No !!!";
            header("location:index.php?app=purchase_order&cmd=po_list&error_msg=$msg");
            exit;
        }
    }

    function getPurchaseOrderMasterInfo($voucher_no)
    {
        $project_id = getFromSession('project_id');

        $info = array();

        $info['table'] = PURCHASE_OREDR_MASTER_TBL . " pom
        LEFT JOIN " . PROJECT_TBL . " p 
            ON pom.project_id = p.project_id
        LEFT JOIN " . SUB_ACC_HEAD_TBL . " s 
            ON pom.supplier_id = s.sub_id
        LEFT JOIN " . SUB_ACC_HEAD_TBL . " cost 
            ON pom.cost_center = cost.sub_id
        LEFT JOIN " . SUB_ACC_HEAD_TBL . " lc 
            ON pom.lc_no = lc.sub_id
        LEFT JOIN " . SUB_ACC_HEAD_TBL . " pay 
            ON pom.payable_id = pay.sub_id";

        $info['fields'] = array(
            'pom.*',
            'pom.cost_center',
            'pom.lc_no',
            'p.project_name',
            'p.location',
            "DATE_FORMAT(pom.order_date,'%d %b %y') as formated_order_date",
            's.sub_head_name',
            's.head_details',
            's.email',
            's.att_email1',
            's.att_email2',
            'cost.sub_head_name as cost_center_head_name',
            'lc.sub_head_name as lc_head_name',
            'pay.sub_head_name as payable_head_name'
        );

        $info['where'] = "pom.project_id = '$project_id' AND pom.voucher_no = '$voucher_no'";
        //$info['debug'] = true;
        $res = select($info);
        $data = array();

        if (count($res) > 0) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        return $data[0];
    }

    function getPurchaseOrderDetailsInfo($voucher_no)
    {
        $info = array();
        $info['table'] = PURCHASE_ORDER_DETAILS_TBL . ' pod,' . PRODUCT_TBL . ' p,' . CATAGORY_TBL . ' c,' . BRAND_TBL . ' b';
        $info['fields'] = array('pod.*', 'b.brand_name', 'p.product_name', 'p.product_code', 'c.catagory_name');
        $sql = "pod.product_id = p.product_id AND p.brand_code = b.brand_id AND p.catagory = c.catagory_code AND pod.voucher_no = '$voucher_no'";
        $info['where'] = $sql;
        $info['groupby'] = array("pod.id");
        $info['orderby'] = array("pod.id asc");
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

    function showPurchaseOrderPrintVoucher()
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();

        $voucher_no = getRequest('voucher_no');
        if ($voucher_no) {
            $advArr = $this->getPurchaseOrderMasterInfo($voucher_no);
            $advArr = parseThisValue($advArr);
            $data = array_merge(array(), $advArr);

            $data['item_list'] = $this->getPurchaseOrderDetailsInfo($voucher_no);
            $data['terms_list'] = $comListApp->getTermsAndConditionList();
            $data['cmd'] = getRequest('cmd');

            require_once(PRINT_PURCHASE_ORDER_VOUCHER_SKIN);
            return true;
        } else {
            $msg = "Missing Voucher No !!!";
            header("location:index.php?app=purchase_order&cmd=po_list&error_msg=$msg");
            exit;
        }
    }


    function sendInvoiceMail()
    {
        if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering

        $input = json_decode(file_get_contents('php://input'), true);

        // Validate input
        if (json_last_error() !== JSON_ERROR_NONE || !isset($input['content'])) {
            die(json_encode([
                'status' => false,
                'message' => 'Invalid JSON data received'
            ]));
        }

        $status = false;
        $project_id = getFromSession('project_id');
        $voucher_no = trim($input['voucher_no']);
        $content = trim($input['content']);

        if ($voucher_no != "" && $content != "") {
            $status = $this->GeneratePDF($voucher_no, $content, $param = NULL);
            if ($status) {
                $advArr = $this->getPurchaseOrderMasterInfo($voucher_no);
                $advArr = parseThisValue($advArr);
                $data = array_merge(array(), $advArr);

                $mail_to = "";
                if (trim($data['email']) != "" && trim($data['email']) != 0) {
                    $mail_to .= $data['email'] . ",";
                }
                if (trim($data['att_email1']) != "") {
                    $mail_to .= $data['att_email1'] . ",";
                }
                if (trim($data['att_email2']) != "") {
                    $mail_to .= $data['att_email2'];
                }


                if ($mail_to != "") {
                    $status = $this->SendInvoice($voucher_no, $mail_to, $content);
                } else {
                    $status = false;
                }
            }
        }

        $message = "Mail send successfully!";
        if (!$status) {
            $message = "Mail not send try again!";
        }

        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $content
        ];

        // Output response
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();

    }


    function GeneratePDF($voucher_no, $html, $param = NULL)
    {
        require_once(EXT_DIR . '/mpdf/mpdf.php');
        if ($param == NULL) {
            $param = '"en-GB-x","A4","","",10,10,10,10,6,3,"L"';
        }
        $pdfApp = new mPDF($param);
        $pdfApp->allow_charset_conversion = true;  // Set by default to TRUE
        $pdfApp->charset_in = 'UTF-8';
        // render the view into HTML

        $status = false;
        if ($html != "") {
            $pdfApp->WriteHTML($html);
            // write the HTML into the PDF

            $fileName = "/PDFBILL/POI-" . $voucher_no . ".pdf";
            $pdfFilePath = DOCUMENTS_DIR . $fileName;
            if (file_exists($pdfFilePath)) {
                unlink($pdfFilePath);
            }
            $pdfApp->Output($pdfFilePath, 'F');
            $status = true;
        }

        return $status;
    }

    function SendInvoice($voucher_no, $mail_to, $mailBody)
    {
        $mail_to = trim($mail_to);

        if ($voucher_no != "" && $mail_to != "") {
            $mail_subject = "HPL Purchase Order Invoice";

            $mailToArr = explode(",", $mail_to);
            $total_mail = count($mailToArr);

            require_once(EXT_DIR . '/phpmailer/PHPMailerAutoload.php');
            $mail = new PHPMailer(true);
            $mail->clearAddresses();
            $sm = 0;
            $issend = 0;
            $mailfrom = "Heritage Polymer";
            while ($sm < $total_mail) {
                if (trim($mailToArr[$sm]) != "") {
                    $mail->AddAddress($mailToArr[$sm], $mailfrom);
                }
                $sm++;
            }

            $attachfile = "";
            $attachment_name = "PDFBILL/POI-" . $voucher_no . ".pdf";
            $pdfFilePath = DOCUMENTS_DIR . "/" . $attachment_name;
            if (file_exists($pdfFilePath)) {
                $attachfile = DOCUMENTS_DIR . "/" . $attachment_name;
            }

            $issend = $this->sendMail($mail, $mail_subject, $mailBody, $attachfile, $attachment_name);

            if ($issend == 1) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function sendMail($mail, $Subject, $mailBody, $Attachfile, $attachment_name)
    {
        /* === Start send mail =====*/
        // Send mail using Gmail
        $send_time = date("d-M-Y");
        $mail->IsSMTP(); // telling the class to use SMTP
        //$mail->SMTPAuth = true; // true / false enable SMTP authentication
        //$mail->SMTPSecure = "tls"; // sets the prefix to the servier
        //$mail->Host = "smtp-broadcasts.postmarkapp.com"; // sets GMAIL as the SMTP server (smtp.gmail.com)
        $mail->Host = "119.148.15.163";
        $mail->Port = 25; // 465 or 587 set the SMTP port for the GMAIL server (587)

        //$mail->Username = "e9187870-d273-4e4b-b967-94572b7a7e7c"; // GMAIL username : liragroupsales@gmail.com
        $email_from = "apps@mailchannel.online";

        //$mail->Password = "e9187870-d273-4e4b-b967-94572b7a7e7c"; // GMAIL password :imran@#0088
        $mail->ContentType = "text/html";

        $full_name = "HPL Distribution Point";
        $mail->SetFrom($email_from, $full_name);

        // To address
        $mail->Subject = $Subject;
        $mail->Body = $mailBody;

        // Set the email to HTML
        $mail->ContentType = "text/html";

        // Attach the uploaded file
        $mail->addAttachment($Attachfile, $attachment_name);

        try {
            if ($mail->Send()) {
                $mail->clearAddresses();
                return true;
            } else {
                return false;
            }

        } catch (Exception $e) {
            $response = [
                'status' => false,
                'message' => $e
            ];
            header('Content-Type: application/json');
            echo json_encode($e);
            exit();

        }
    }

    /* Purchase Order end */


    function sendDBBackupMail($mail_to, $Subject, $body)
    {
        if (is_array($mail_to)) {
            $mailToArr = $mail_to;
        } elseif (is_string($mail_to)) {
            $mailToArr = explode(",", $mail_to);
        } else {
            $mailToArr = []; // fallback if it's neither
        }
        $mailToArr = array_map('trim', $mailToArr);
        $total_mail = count($mailToArr);

        require_once(EXT_DIR . '/phpmailer/PHPMailerAutoload.php');
        $mail = new PHPMailer(true);
        $mail->clearAddresses();
        $sm = 0;
        $issend = 0;
        $mailfrom = "Thai Foils & Polymer Industries Ltd";
        while ($sm < $total_mail) {
            if (trim($mailToArr[$sm]) != "") {
                $mail->AddAddress($mailToArr[$sm], $mailfrom);
            }
            $sm++;
        }

        /* === Start send mail =====*/
        // Send mail using Gmail
        $send_time = date("d-M-Y");
        $mail->IsSMTP(); // telling the class to use SMTP
        //$mail->SMTPAuth = true; // true / false enable SMTP authentication
        //$mail->SMTPSecure = "tls"; // sets the prefix to the servier
        //$mail->Host = "smtp-broadcasts.postmarkapp.com"; // sets GMAIL as the SMTP server (smtp.gmail.com)
        $mail->Host = "119.148.15.163";
        $mail->Port = 25; // 465 or 587 set the SMTP port for the GMAIL server (587)

        //$mail->Username = "e9187870-d273-4e4b-b967-94572b7a7e7c"; // GMAIL username : liragroupsales@gmail.com
        $email_from = "apps@mailchannel.online";

        //$mail->Password = "e9187870-d273-4e4b-b967-94572b7a7e7c"; // GMAIL password :imran@#0088
        $mail->ContentType = "text/html";

        $full_name = "HPL System";
        $mail->SetFrom($email_from, $full_name);

        // To address
        $mail->Subject = $Subject;
        $mail->Body = $body;

        // Set the email to HTML
        $mail->ContentType = "text/html";

        try {
            if ($mail->Send()) {
                $mail->clearAddresses();
                $response = [
                    'status' => true,
                    'message' => "Email send successfully"
                ];
            } else {
                $response = [
                    'status' => false,
                    'message' => "Email not send"
                ];
            }

        } catch (Exception $e) {
            $response = [
                'status' => false,
                'message' => $e
            ];
        }

        return $response;
    }


    function getProductList($id)
    {

        $info = array();
        $info['table'] = PURCHASE_DETAILS_TBL . ' pd,' . PRODUCT_TBL . ' p,' . CURRENCY_TBL . ' c,' . BRAND_TBL . ' b';
        $info['fields'] = array('pd.pur_detail_id', 'pd.voucher_no', 'pd.project_id', 'pd.catagory', 'b.brand_name', 'pd.product', 'pd.details', 'p.product_name', 'p.product_desc', 'pd.m_unit', 'pd.unit_price', 'c.curr_symble', 'pd.qty', 'pd.total_bag', 'pd.total', 'pd.created_time');

        $sql = "pd.product = p.product_id AND p.brand_code = b.brand_id AND pd.currency = c.currency_id AND pd.voucher_no = '$id'";

        $info['where'] = $sql;
        $info['groupby'] = array("pd.pur_detail_id");
        $info['orderby'] = array("pd.product asc");
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

    function loadProduct4Catagory($brand_id)
    {
        $catagory = trim(getRequest('catagory_id'));
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = PRODUCT_TBL;
        $info['fields'] = array('product_id', 'product_code', 'product_name', 'product_desc');
        //$info['where']   = "catagory = '$catagory' AND brand_code = '$brand_id' AND project_id = '$project_id'";
        $info['where'] = "brand_code = '$brand_id' AND project_id = '$project_id' AND approval_status = 1";
        $info['groupby'] = array("product_id");
        //$info['debug']   = true;

        $result = select($info);
        $data = array();

        if (count($result)) {
            foreach ($result as $key => $value) {
                $data[$key][] = $value;
            }
        }

        require_once(CLASS_DIR . '/common.list.class.php');
        foreach ($data as $i => $v) {
            $productName = (new CommonList())->normalizeProductName($v[0]->product_code, $v[0]->product_name);
            $subject_idname .= $v[0]->product_id . '#####' . $productName . '#####' . $v[0]->product_desc . '@@@';
        }
        echo $subject_idname;
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


    function getSupplierList()
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = SUPPLIER_TBL;
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

    function saveAccountJournal($voucher_no, $sub_id, $head_type, $project_id, $description, $DR = NULL, $CR = NULL, $balance, $status = NULL, $purchare_date = NULL)
    {
        $sql = "INSERT INTO " . ACCOUNT_JOURNAL_TBL . " (voucher_no,created_date,sub_id,head_type,project_id,description,dr,cr,balance,status) VALUES('" . $voucher_no . "','" . $purchare_date . "','" . $sub_id . "','" . $head_type . "','" . $project_id . "','" . $description . "','" . $DR . "','" . $CR . "','" . $balance . "','" . $status . "')";
        mysql_query($sql);
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

    function saveStockJournal($voucher_no, $project_id, $product_id, $unit_price = NULL, $m_unit, $DR = NULL, $CR = NULL, $balance, $create_date = NULL)
    {
        $sql = "INSERT INTO " . STOCK_LEDGER_TBL . " (voucher_no,project_id,product_id,unit_price,m_unit,dr,cr,balance,create_date) VALUES('" . $voucher_no . "','" . $project_id . "','" . $product_id . "','" . $unit_price . "','" . $m_unit . "','" . $DR . "','" . $CR . "','" . $balance . "','" . $create_date . "')";
        mysql_query($sql);
    }

    function createVoucharID()
    {
        $info = array();
        $info['table'] = DEVIT_VOUCHAR_TBL;
        $info['fields'] = array('max(voucher_no) as maxvoucher');
        $res = select($info);
        $maxvoucherId = 'D0000000';
        if (count($res)) {
            foreach ($res as $v) {
                if ($v->maxvoucher) {
                    $maxvoucherId = $v->maxvoucher;
                }
                break;
            }

        }
        $maxvoucherId = generateID("D", $maxvoucherId, 8);
        return $maxvoucherId;
    }

    function deleteRecord($id)
    {
        if (getRequest('id')) {
            $info = array();
            $info['table'] = BANK_ACCOUNT_TBL;
            $info['where'] = "purchase_no='$id'";
            $info['debug'] = false;
            $res = delete($info);

            if ($res) {
                $msg = "Successfully delete Record !!!";
                header("location:?app=bank_account&cmd=view&msg=$msg");

            } else {
                header("location:?app=bank_account&cmd=view&cmd=list&deleted=no");
            }

        }

    }

    //====================== Start Purchase Details ===============

    function showEditor4PurchaseDetails($msg = null)
    {

        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['record_list'] = $this->getPurchaseDetailsList(getRequest('from'), getRequest('to'));
        $data['totalrecord'] = $this->getTotalPurchaseDetailsList(getRequest('from'), getRequest('to'));
        require_once(PURCHASE_DETAILS_SKIN);
        return $data[0];

    }

    function getPurchaseDetailsList($from, $to)
    {

        if ($from == "" && $to == "") {
            $from = 0;
            $to = 500;
        }
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = PURCHASE_MASTER_TBL . ' pm,' . SUPPLIER_TBL . ' s,' . PROJECT_TBL . ' p,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('pm.voucher_no', 'p.project_name', 'p.location', 's.supplier_code', 's.name', 's.address', 'pm.quotation_no', 'pm.lc_no', 'pm.lcopener', 'pm.lcopening_bank', "DATE_FORMAT(pm.lcopening_date,'%d %b %y' ) as lcopening_date", 'pm.country', 'pm.lc_details', 'pm.track_no', 'pm.van_no', 'pm.total_value', "DATE_FORMAT(pm.purchase_date,'%d %b %y' ) as purchase_date", 'pm.mode_of_payment', 'c.curr_symble', 'pm.bank_name', 'pm.acc_no', 'pm.check_no', 'pm.check_no', 'pm.discount', 'pm.net_payble', 'pm.paid_amount', 'pm.due', 'pm.ref_no', "DATE_FORMAT(pm.created_date,'%d %b %y' ) as date", 'pm.created_date');

        $sql = "pm.supplier = s.supplier_code AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '" . $project_id . "'";

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

    function getTotalPurchaseDetailsList($from, $to)
    {

        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = PURCHASE_MASTER_TBL . ' pm,' . SUPPLIER_TBL . ' s,' . PROJECT_TBL . ' p,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('pm.voucher_no');

        $sql = "pm.supplier = s.supplier_code AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '" . $project_id . "'";

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
        $requestdata['created_date'] = formatDate(getRequest('purchase_date'));
        $requestdata['acc_head'] = getRequest('supplier');
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

    function loadMUnite($product_id)
    {
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = PRODUCT_TBL;
        $info['fields'] = array('m_unit', 'detail_unit');
        $info['where'] = "product_id = '$product_id' AND project_id = '$project_id'";
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
            $m_unit .= $v[0]->m_unit . "###" . $v[0]->detail_unit;
        }
        echo $m_unit;
    }

} // End class
?>
