<?php
require_once('journal.class.php');

class PurchaseItemGRN extends Journal
{
    function run()
    {
        $cmd = getRequest('cmd');
        $u_t_id = getFromSession('u_type_id');
        if (($u_t_id == 101) || ($u_t_id == 102) || ($u_t_id == 103) || ($u_t_id == 104) || ($u_t_id == 105)) // 101 = sysadmin, 102 = admin, 103= salesman
        {
            switch ($cmd) {
                case 'add'            :
                    $this->showEditor();
                    break;
                case 'grn_order_list'    :
                    $this->showGRNOrderList();
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
                case 'get_dtl'        :
                    $this->loadProductDtl(trim(getRequest('product_id')));
                    break;
                case 'save_tmp'        :
                    $this->saveTempPurchase();
                    break;
                case 'delTempGrn'        :
                    $this->delTempPurchase();
                    break;
                case 'savePurchase'        :
                    $this->savePurchaseItem();
                    break;
                case 'print_vouchar'        :
                    $screen = $this->showPrintEditor($msg);
                    break;
                case 'delete'            :
                    $screen = $this->deleteRecord(getRequest('id'));
                    break;
                case 'check_challan_no'      :
                    $this->CheckChallanNo();
                    break;
                case 'add_spr':
                    $screen = $this->addSPREditor();
                    break;
                case 'spr_list':
                    $screen = $this->showSPRList();
                    break;
                case 'save_sprtmp':
                    $this->saveSPRTmp();
                    break;
                case 'edittempspr':
                    $this->addSPREditor();
                    break;
                case 'deltempspr':
                    $this->delTempSPR();
                    break;
                case 'spr_delete':
                    $this->delSPR();
                    break;
                case 'saveSPR':
                    $this->saveSPR();
                    break;
                case 'updateSPR':
                    $this->updateSPR();
                    break;
                case 'getSPRData':
                    $this->getSPRData();
                    break;
                case 'checkSPRVoucherNo':
                    $this->checkSPRVoucherNo();
                    break;
                case 'spr_print_vouchar'        :
                    $this->showSPRPrintVoucher();
                    break;
                case 'spr_edit'        :
                    $this->showSPREditPage();
                    break;
                case 'getEditSPRItem'        :
                    $this->getEditSPRItem();
                    break;
                case 'addSPRItem'        :
                    $this->addSPRItem();
                    break;
                case 'deleteSPRItem'        :
                    $this->deleteSPRItem();
                    break;
                case 'addPOProduct'        :
                    $this->addPOProduct();
                    break;
                case 'editTempPoItem'        :
                    $this->editTempPoItem();
                    break;
                default                   :
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
            $advArr = $this->getPurchaseMasterInfo($voucher_no);
            $advArr = parseThisValue($advArr);
            $data = array_merge(array(), $advArr);
            $data['item_list'] = $this->getGRNProductList($voucher_no);
            $data['message'] = $msg;
            $data['cmd'] = getRequest('cmd');
            require_once(GRN_PRINT_VOUCHER_SKIN);
            return true;
        } else {
            require_once(PRINT_VOUCHAR_SKIN);
        }
    }


    function addSPREditor($msg = null)
    {
        $data = array();
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();

        $supplier_list = $comListApp->getSupplierList();
        $supplier_list_payable = $comListApp->getSupplierListPayable();
        $data['supplier_list'] = array_merge($supplier_list, $supplier_list_payable);

        $data['product_list'] = $comListApp->getProductList();
        $data['depo_list'] = $comListApp->getDeliveryPointList(true);
        $data['tmp_sales'] = $this->getTempSPR();

        $temp_id = getRequest('id');
        if (isset($temp_id) && $temp_id != "") {
            $dsql = "SELECT * FROM " . TEMP_SPR_TBL . " WHERE tmp_id ='$temp_id'";
            $res = mysql_query($dsql);
            if (mysql_num_rows($res) > 0) {
                $data['editItem'] = mysql_fetch_object($res);
            }
        }

        $data['cmd'] = getRequest('cmd');
        require_once(STOCK_PURCHASE_REQUSITION_SKIN);
        return $data[0];
    }

    function getSPRList($total = false)
    {
        $from = getRequest('from');
        $to = getRequest('to');
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 100;
        }

        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $delivery_point = getRequest('delivery_point');
        $project_id = getFromSession('project_id');
        $complete_status = getRequest('complete_status');
        if ($complete_status != 1) {
            $complete_status = 0;
        }

        $info = array();
        $info['table'] = SPR_PURCHASE_MASTER_TBL . ' spm,' . PROJECT_TBL . ' p,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('spm.*', 'p.project_name', 'p.location', "DATE_FORMAT(spm.purchase_date,'%d %b %y' ) as formated_purchase_date", 'c.curr_symble', "DATE_FORMAT(spm.created_date,'%d %b %y' ) as formated_created_date");

        $sql = "spm.project_id = p.project_id AND spm.currency = c.currency_id AND spm.project_id = '" . $project_id . "'  AND spm.complete_status='" . $complete_status . "'";
        if ($delivery_point != "") {
            $sql .= " AND spm.store_id = '$delivery_point'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND spm.created_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND spm.created_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND spm.created_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        if ($total) {
            $info['orderby'] = array("spm.created_date DESC");
        } else {
            $info['orderby'] = array("spm.created_date DESC LIMIT $from,$to");
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

    function showSPRList()
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();
        $data['cmd'] = getRequest('cmd');
        $data['depo_list'] = $comListApp->getDeliveryPointList(true);

        $data['complete_status'] = getRequest('complete_status');
        $data['record_list'] = $this->getSPRList();
        $data['totalrecord'] = $this->getSPRList(true);

        require_once(STOCK_PURCHASE_REQUSITION_LIST_SKIN);
        return $data[0];
    }

    function getTempSPR()
    {
        $project_id = getFromSession('project_id');
        $str1 = "<table width='100%' align='center' bgcolor='#99CC66'>
	<tr style='color:#fff;' bgcolor='#00B000' height=28>
	  <td width='1%' nowrap><div align='left'>SL</div></td>
	  <td width='20%' nowrap><div align='left'>Product Name</div></td>
	  <td width='5%' nowrap><div align='left'>Unit</div></td>
	  <td width='10%' nowrap><div align='left'>Catagory</div></td>
	  <td width='10%' nowrap><div align='left'>Present Stock</div></td>
	  <td width='8%' nowrap><div align='left'>Request Qty</div></td>				  
	  <td width='7%' nowrap align='center'>Option</td>
	</tr>";
        $total_value = 0;
        $TotalQty = 0;
        $sl = 1;
        $getSql = "SELECT * FROM " . TEMP_SPR_TBL . " WHERE created_by = '" . getFromSession('userid') . "' AND project_id='" . $project_id . "'";
        $gres = mysql_query($getSql);
        while ($row = mysql_fetch_array($gres)) {
            extract($row);
            $total_value += $total;
            $TotalQty += $qty;
            $str2 .= "
	<tr style='color:#000000' bgcolor='#fff'>
	  <td width='1%' nowrap>$sl</td>
	  <td width='20%' nowrap align='left'>$product_name</td>
	  <td width='10%' nowrap align='left'>$munit</td>
	  <td width='10%' nowrap align='left'>$catagoryname</td>
	  <td width='10%' nowrap align='left'>$present_stock</td>
	  <td width='8%' nowrap><div align='right'>$qty</div></td>		  				  
	  <td width='7%' nowrap align='center'>
	  <a href=\"?app=purchase.item.grn&cmd=edittempspr&id=$tmp_id\">
	  <img src=\"images/common/icons/edit.gif\"></a>
	  <a href=\"?app=purchase.item.grn&cmd=deltempspr&id=$tmp_id\">
	  <img src=\"images/common/icons/delete.gif\"></a>
	  </td>
	</tr>";
            $sl++;
        }
        $str3 = "
	<tr style='color:#000;' bgcolor='#CCCCCC' height=25>
	  <td colspan='5' nowrap><div align='right'>Total </div></td>
	  <td nowrap align='right'>$TotalQty $munit</td>
	  <td nowrap>&nbsp;</td>
	</tr>
	</table>";
        $total_salesStr = $str1 . $str2 . $str3;
        return $total_salesStr;
    }

    function saveSPRTmp()
    {
        if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering

        //======= Insert into tamp ========
        $requestdata = array();
        $requestdata = getUserDataSet(TEMP_SPR_TBL);
        $project_id = getFromSession('project_id');
        $requestdata['project_id'] = $project_id;
        $requestdata['supplier'] = getRequest('supplier');
        $requestdata['store_id'] = getRequest('store_id');
        $requestdata['purchase_date'] = formatDate(getRequest('purchase_date'));
        $requestdata['unit'] = getRequest('unit');
        $requestdata['department'] = getRequest('department');

        $requestdata['warranty_note'] = getRequest('warranty_note');
        $requestdata['productid'] = getRequest('productid');

        $sql = "SELECT po.product_name,po.catagory,po.brand_code,po.m_unit, ct.catagory_name, b.brand_name FROM " . PRODUCT_TBL . " po
		LEFT JOIN " . CATAGORY_TBL . " ct ON ct.catagory_code  =po.catagory
		LEFT JOIN " . BRAND_TBL . " b ON b.brand_id  =po.brand_code WHERE product_id = '" . $requestdata['productid'] . "'";
        $row = mysql_fetch_object(mysql_query($sql));
        $requestdata['product_name'] = $row->product_name;
        $requestdata['catagory'] = $row->catagory;
        $requestdata['catagoryname'] = $row->catagory_name;
        $requestdata['brand_id'] = $row->brand_code;
        $requestdata['brandname'] = $row->brand_name;
        $requestdata['munit'] = $row->m_unit;
        $requestdata['qty'] = (float)getRequest('qty');
        $requestdata['unit_price'] = (float)getRequest('unit_price');
        $requestdata['present_stock'] = (float)getRequest('present_stock');
        $requestdata['total'] = $requestdata['qty'] * $requestdata['unit_price'];

        $requestdata['last_purchase_date'] = getRequest('last_purchase_date');
        $requestdata['created_by'] = getFromSession('userid');
        $info = array();

        $info['table'] = TEMP_SPR_TBL;
        $info['data'] = $requestdata;
        //$info['debug']  	=  true;

        $tmp_id = getRequest('tmp_id');
        if (isset($tmp_id) && $tmp_id != "") {
            $info['where'] = "tmp_id ='$tmp_id'";
            $res = update($info);

        } else {
            $res = insert($info);
        }

        echo $this->getTempSPR();;
    }

    // Create SPR voucher ID
    function createSPRVoucharID()
    {
        $manual_spr_no = getRequest('manual_spr_no');
        if ($manual_spr_no != "") {
            $sql = "SELECT 'voucher_no' FROM " . SPR_PURCHASE_MASTER_TBL . " WHERE voucher_no='$manual_spr_no'";
            $res = mysql_query($sql);

            if (mysql_num_rows($res) <= 0) {
                return $manual_spr_no;
            }
        }

        $info = array();
        $info['table'] = SPR_PURCHASE_MASTER_TBL;
        $info['fields'] = array('max(voucher_no) as maxvoucher');
        $res = select($info);
        $maxvoucherId = 'SPR00000000';
        if (count($res)) {
            foreach ($res as $v) {
                if ($v->maxvoucher) {
                    $maxvoucherId = $v->maxvoucher;
                }
                break;
            }
        }
        $maxvoucherId = generateID("SPR", $maxvoucherId, 11);
        return $maxvoucherId;
    }


    function saveSPR()
    {
        $voucher_no = $this->createSPRVoucharID();
        $project_id = getFromSession('project_id');

        if ($voucher_no) {
            //======= Insert into tamp ========
            $requestdata = array();
            $requestdata = getUserDataSet(SPR_PURCHASE_MASTER_TBL);
            $requestdata['voucher_no'] = $voucher_no;
            $requestdata['project_id'] = $project_id;
            $requestdata['store_id'] = getRequest('store_id');
            $requestdata['purchase_date'] = formatDate(getRequest('purchase_date'));
            $requestdata['unit'] = getRequest('unit');
            $requestdata['department'] = getRequest('department');
            $requestdata['warranty_note'] = getRequest('warranty_note');
            $requestdata['created_by'] = getFromSession('userid');
            $requestdata['created_date'] = date('Y-m-d');
            $requestdata['created_time'] = date('Y-m-d h:i:s');
            $info = array();

            $info['table'] = SPR_PURCHASE_MASTER_TBL;
            $info['data'] = $requestdata;
            //$info['debug']  	=  true;
            $res = insert($info);
//$res = true;
            if ($res) {
                $getSql = "SELECT * FROM " . TEMP_SPR_TBL . " WHERE created_by = '" . getFromSession('userid') . "' AND project_id='" . $project_id . "'";
                $gres = mysql_query($getSql);
                $totalAmount = 0;
                while ($row = mysql_fetch_array($gres)) {
                    $row = (object)$row;
                    $requestDetailsdata = array();
                    $requestDetailsdata = getUserDataSet(SPR_PURCHASE_DETAILS_TBL);
                    $requestDetailsdata['voucher_no'] = $voucher_no;
                    $requestDetailsdata['project_id'] = $project_id;
                    $requestDetailsdata['catagory'] = $row->catagory;
                    $requestDetailsdata['brand_id'] = $row->brand_id;
                    $requestDetailsdata['product'] = $row->productid;
                    $requestDetailsdata['m_unit'] = $row->munit;
                    $requestDetailsdata['unit_price'] = (float)$row->unit_price;
                    $requestDetailsdata['qty'] = (float)$row->qty;
                    $requestDetailsdata['init_qty'] = $requestDetailsdata['qty'];
                    $requestDetailsdata['stock_qty'] = (float)$row->present_stock;
                    $requestDetailsdata['last_purchase_date'] = $row->last_purchase_date;
                    $requestDetailsdata['total'] = (float)$row->total;
                    $totalAmount += (float)$row->total;

                    $requestDetailsdata['created_by'] = getFromSession('userid');
                    $requestDetailsdata['created_time'] = date('Y-m-d h:i:s');

                    $infoDetails = array();

                    $infoDetails['table'] = SPR_PURCHASE_DETAILS_TBL;
                    $infoDetails['data'] = $requestDetailsdata;
                    //$info['debug']  	=  true;
                    insert($infoDetails);
                }

                $requestData = array();
                $requestData['total'] = $totalAmount;
                $infoM['table'] = SPR_PURCHASE_MASTER_TBL;
                $infoM['data'] = $requestData;
                $infoM['where'] = "voucher_no='$voucher_no'";
                $infoM['debug'] = true;
                update($infoM);

                $getSql = "DELETE FROM " . TEMP_SPR_TBL . " WHERE created_by = '" . getFromSession('userid') . "' AND project_id='" . $project_id . "'";
                mysql_query($getSql);

                header("location:?app=purchase.item.grn&cmd=spr_list&msg=Record save successfully!!");
                exit();
            }
        } else {
            header("location:?app=purchase.item.grn&cmd=add_spr&msg=Voucher Overflow!!");
            exit();
        }
    }

    function delTempSPR()
    {
        $tmp_id = $_REQUEST['id'];
        if ($tmp_id != "") {
            $dsql = "DELETE FROM " . TEMP_SPR_TBL . " WHERE tmp_id ='" . $tmp_id . "'";
            mysql_query($dsql);
        }
        header("location:?app=purchase.item.grn&cmd=add_spr");
        exit();

    }

    function delSPR()
    {
        $voucher_no = $_REQUEST['voucher_no'];
        if ($voucher_no != "") {
            $dsql = "DELETE FROM " . SPR_PURCHASE_DETAILS_TBL . " WHERE voucher_no ='" . $voucher_no . "'";
            mysql_query($dsql);

            $msql = "DELETE FROM " . SPR_PURCHASE_MASTER_TBL . " WHERE voucher_no ='" . $voucher_no . "'";
            mysql_query($msql);
        }
        header("location:?app=purchase.item.grn&cmd=spr_list");
        exit();

    }


    function showEditor($msg = null)
    {
        $data = array();
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();

        $supplier_list = $comListApp->getSupplierListCombined();
        $impoter_list = $comListApp->getImpoterList();
        $cost_center_list = $comListApp->getAccountHeadList("Cost Center");
        $data['supplier_list'] = array_merge($supplier_list, $impoter_list, $cost_center_list);

        $supplierData = $comListApp->getSupplierData();
        $data['supplierData'] = json_encode($supplierData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $data['product_list'] = $comListApp->getProductList();
        $data['cat_list'] = $this->getCatagoryList();
        $data['brand_list'] = $comListApp->getBrandList();
        $data['currency_list'] = $this->getCurrencyList();
        $data['depo_list'] = $comListApp->getDeliveryPointList(true);
        $data['equipment_list'] = $comListApp->getAccountHeadList("Non Current Assets", "S126");
        $data['raw_material_list'] = $comListApp->getAccountHeadList("Current Assets", NULL, "C000055");
        $data['fg_list'] = $comListApp->getAccountHeadList("Current Assets", NULL, "C000056");
        $data['maintanance_list'] = $comListApp->getAccountHeadList("Current Assets", NULL, "C000154");
        $data['cogsheadlist'] = $comListApp->getAccountHeadList("Cost Center");
        $data['vat_type_list'] = $comListApp->getAccountList("Current Assets", "S112", "C000061", "S300247");
        $data['po_list'] = $this->getPOList();
        $data['tmp_items'] = $this->getTempGRNPurchase();

        $data['payable_list'] = $comListApp->getAccountList("Current Assets", "S127", "C000186");

        $data['cmd'] = getRequest('cmd');

        require_once(PURCHASE_ITEM_ADD_GRN_SKIN);
        return $data[0];
    }


    function showGRNOrderList()
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data = array();

        $supplier_list = $comListApp->getSupplierListCombined();
        $impoter_list = $comListApp->getImpoterList();
        $cost_center_list = $comListApp->getAccountHeadList("Cost Center");
        $data['supplier_list'] = array_merge($supplier_list, $impoter_list, $cost_center_list);

        $data['cmd'] = getRequest('cmd');

        $data['record_list'] = $this->getGRNOrderList();
        $data['totalrecord'] = $this->getGRNOrderList(true);

        require_once(GRN_ORDER_LIST_SKIN);
        return $data[0];
    }

    function getGRNOrderList($total = false)
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
        $po_no = getRequest('po_no');
        $project_id = getFromSession('project_id');

        $info = array();
        $info['table'] = PURCHASE_MASTER_TBL . ' pm,' . SUB_ACC_HEAD_TBL . ' s,' . PROJECT_TBL . ' p,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('pm.*', 'p.project_name', 'p.location', 's.sub_id', 's.sub_head_name', 's.head_details', 'pm.quotation_no', 'pm.lc_no', 'pm.lcopener', 'pm.lcopening_bank', "DATE_FORMAT(pm.lcopening_date,'%d %b %y' ) as lcopening_date", 'pm.country', 'pm.lc_details', 'pm.track_no', 'pm.van_no', 'pm.total_value', "DATE_FORMAT(pm.purchase_date,'%d %b %y' ) as purchase_date", 'pm.mode_of_payment', 'c.curr_symble', 'pm.bank_name', 'pm.acc_no', 'pm.check_no', 'pm.check_no', 'pm.discount', 'pm.net_payble', 'pm.paid_amount', 'pm.due', 'pm.ref_no', "DATE_FORMAT(pm.purchase_date,'%d %b %y' ) as date", 'pm.created_date', 'pm.grn_voucher');

        $sql = "pm.supplier = s.sub_id AND pm.project_id = p.project_id AND pm.currency = c.currency_id AND pm.project_id = '" . $project_id . "' AND pm.purchase_type!='Production' AND pm.grn_voucher IS NOT NULL";
        if ($po_no != "") {
            $sql .= " AND FIND_IN_SET('$po_no', pm.po_no)";
        }
        if ($supplier_id != "") {
            $sql .= " AND pm.supplier = '$supplier_id'";
        }
        if ($date_from != "" && $date_to == "") {
            $sql .= " AND pm.purchase_date >= '$date_from'";
        } elseif ($date_from == "" && $date_to != "") {
            $sql .= " AND pm.purchase_date <= '$date_to'";
        } elseif ($date_from != "" && $date_to != "") {
            $sql .= " AND pm.purchase_date BETWEEN '$date_from' AND '$date_to'";
        }
        $info['where'] = $sql;
        $info['orderby'] = array("pm.grn_voucher DESC LIMIT $from,$to");
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


    function getPOList()
    {
        $info = array();
        $info['table'] = PURCHASE_OREDR_MASTER_TBL;
        $info['where'] = "complete_status = 0 AND approved_status = 1";
        $info['orderby'] = array("voucher_no DESC");

        //$info['debug']  = true;
        $result = select($info);
        $data = array();

        if (count($result)) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }

        return $data;
    }


    function CheckChallanNo()
    {
        if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering

        $input = json_decode(file_get_contents('php://input'), true);

        // Validate input
        if (json_last_error() !== JSON_ERROR_NONE || !isset($input['challan_no'])) {
            die(json_encode([
                'status' => false,
                'message' => 'Invalid JSON data received'
            ]));
        }

        $challan_no = trim($input['challan_no']);

        $challanSql = "SELECT 'quotation_no' FROM " . PURCHASE_MASTER_TBL . " WHERE quotation_no='$challan_no'";
        $existingChallanSql = mysql_query($challanSql);

        $tempChallanSql = "SELECT 'quotation_no' FROM " . TEMP_PURCHASE_TBL . " WHERE quotation_no='$challan_no'";
        $existingTempChallanSql = mysql_query($tempChallanSql);

        $tempGrnSql = "SELECT 'quotation_no' FROM " . TEMP_GRN_TBL . " WHERE quotation_no='$challan_no'";
        $existingTempGrnChallanSql = mysql_query($tempGrnSql);

        if (mysql_num_rows($existingChallanSql) > 0 || mysql_num_rows($existingTempChallanSql) > 0 || mysql_num_rows($existingTempGrnChallanSql) > 0) {
            $response = [
                'status' => true,
                'message' => 'Challan No Already Exists'
            ];
        } else {
            $response = [
                'status' => false,
                'message' => 'Challan No is Available'
            ];
        }

        // Output response
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }


    function getSPRData()
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

        $info = array();
        $info['table'] = "
            " . SPR_PURCHASE_DETAILS_TBL . " spd
            INNER JOIN " . SPR_PURCHASE_MASTER_TBL . " spm ON spd.voucher_no = spm.voucher_no
            INNER JOIN " . PRODUCT_TBL . " po ON po.product_id = spd.product
            INNER JOIN " . CATAGORY_TBL . " ct ON ct.catagory_code = po.catagory
            INNER JOIN " . BRAND_TBL . " b ON b.brand_id = po.brand_code
        ";

        $info['fields'] = array(
            'spd.*',
            'po.product_name',
            'ct.catagory_name',
            'b.brand_name'
        );

        $info['where'] = "
            spm.project_id = '$project_id' 
            AND spm.voucher_no = '$voucher_no' 
            AND spd.complete_status = 0 AND spm.complete_status = 0
        ";
        $info['orderby'] = array("spd.voucher_no");

        //$info['debug']  = true;
        $result = select($info);
        $cnt = count($result);

        if ($cnt) {
            $data = array();
            foreach ($result as $value) {
                $data[] = $value;
            }
            $response = [
                'status' => true,
                'data' => $data
            ];
        } else {
            $response = [
                'status' => false,
                'data' => null
            ];
        }

        // Output response
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    function addPOProduct()
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

        $purchase_date = trim($input['purchase_date']);
        $currency = trim($input['currency']);
        $currencyName = trim($input['currencyName']);
        $quotation_no = trim($input['quotation_no']);
        $payment_note = trim($input['payment_note']);
        $warranty_note = trim($input['warranty_note']);

        if ($voucher_no != "") {
            $info = array();
            $info['table'] = PURCHASE_ORDER_DETAILS_TBL;
            $info['where'] = "voucher_no = '$voucher_no' AND complete_status = '0'";
            //$info['debug']  = true;
            $result = select($info);

            $sql = "SELECT pod.*,pom.supplier_id,pom.total_vat_percent,pom.total_vat_amount,pom.vat_type,pom.purchase_type,pom.cost_center,pom.lc_no,pom.payable_id,sprm.store_id FROM " . PURCHASE_ORDER_DETAILS_TBL . " AS pod
		LEFT JOIN " . PURCHASE_OREDR_MASTER_TBL . " AS pom ON pom.voucher_no=pod.voucher_no
		LEFT JOIN " . SPR_PURCHASE_MASTER_TBL . " AS sprm ON sprm.voucher_no=pod.spr_no WHERE pod.voucher_no = '$voucher_no' AND pod.complete_status = '0'";
            $result = mysql_query($sql);

            while ($row = mysql_fetch_assoc($result)) {
                $value = (object)$row;
                $pod_id = $value->id;
                $product_id = $value->product_id;

                $requestdata = array();
                $requestdata = getUserDataSet(TEMP_GRN_TBL);
                $requestdata['po_voucher_no'] = $voucher_no;
                $requestdata['pod_id'] = $pod_id;
                $requestdata['project_id'] = $project_id;
                $requestdata['supplier'] = $value->supplier_id;
                $requestdata['purchase_type'] = $value->purchase_type;

                $requestdata['cost_center'] = $value->cost_center;
                $requestdata['lc_no'] = $value->lc_no;
                $requestdata['payable_id'] = $value->payable_id;

                $requestdata['store_id'] = $value->store_id;
                $requestdata['purchase_date'] = formatDate($purchase_date);
                $requestdata['currency'] = $currency;
                $requestdata['currencyName'] = $currencyName;
                $requestdata['quotation_no'] = $quotation_no;
                $requestdata['payment_note'] = $payment_note;
                $requestdata['warranty_note'] = $warranty_note;

                $sql = "SELECT po.product_name,ct.catagory_name,b.brand_name FROM " . PRODUCT_TBL . " AS po
                        LEFT JOIN " . CATAGORY_TBL . " AS ct ON ct.catagory_code  = po.catagory
                        LEFT JOIN " . BRAND_TBL . " AS b ON b.brand_id  = po.brand_code";
                $sql .= " WHERE product_id = '$product_id '";

                $row = mysql_fetch_object(mysql_query($sql));

                $requestdata['productid'] = $product_id;
                $requestdata['product_name'] = $row->product_name;
                $requestdata['catagory'] = $value->catagory_id;
                $requestdata['catagoryname'] = $row->catagory_name;
                $requestdata['brand_id'] = $value->brand_id;
                $requestdata['brandname'] = $row->brand_name;
                $requestdata['munit'] = $value->m_unit;
                $requestdata['qty'] = (float)$value->qty;
                $requestdata['max_qty'] = $requestdata['qty'];
                $requestdata['unit_price'] = (float)$value->unit_price;
                $requestdata['vat_percent'] = (float)$value->vat_percent;
                $requestdata['vat_amount'] = (float)$value->vat_amount;
                $requestdata['total_vat_percent'] = (float)$value->total_vat_percent;
                $requestdata['total_vat_amount'] = (float)$value->total_vat_amount;
                $requestdata['total_vat_type'] = $value->vat_type;
                $requestdata['total'] = $requestdata['unit_price'] * $requestdata['qty'];
                $requestdata['created_by'] = getFromSession('userid');
                $info = array();
                $info['table'] = TEMP_GRN_TBL;
                $info['data'] = $requestdata;
                //$info['debug']  	=  true;
                $res = insert($info);

                $requestSPDdata['complete_status'] = 1;
                $info = array();
                $info['table'] = PURCHASE_ORDER_DETAILS_TBL;
                $info['data'] = $requestSPDdata;
                $info['where'] = "voucher_no='$voucher_no' AND id='$pod_id'";
                //$info['debug']  	=  true;
                update($info);
            }

            $info = array();
            $info['table'] = PURCHASE_ORDER_DETAILS_TBL;
            $info['where'] = "voucher_no = '$voucher_no' AND complete_status = '0'";
            //$info['debug']  = true;
            $result = select($info);

            if (count($result) <= 0) {
                $requestMasterdata['complete_status'] = 1;
                $info = array();
                $info['table'] = PURCHASE_OREDR_MASTER_TBL;
                $info['data'] = $requestMasterdata;
                $info['where'] = "voucher_no='$voucher_no'";
                //$info['debug']  	=  true;
                update($info);
            }

            $response = [
                'status' => true,
                'message' => "Product Added Successfully!!!",
                'data' => $this->getTempGRNPurchase()
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


    function editTempPoItem()
    {
        if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering

        $input = json_decode(file_get_contents('php://input'), true);

        // Validate input
        if (json_last_error() !== JSON_ERROR_NONE || !isset($input['tmpId'])) {
            die(json_encode([
                'status' => false,
                'message' => 'Invalid JSON data received'
            ]));
        }

        $project_id = getFromSession('project_id');
        $tmp_id = trim($input['tmpId']);


        if ($tmp_id != "") {
            $sql = "SELECT * FROM " . TEMP_GRN_TBL . " WHERE tmp_id = '$tmp_id'";
            $result = mysql_fetch_object(mysql_query($sql));

            if (isset($result->tmp_id)) {
                $response = [
                    'status' => true,
                    'message' => "Successful!!",
                    'data' => $result
                ];
            } else {
                $response = [
                    'status' => false,
                    'message' => "Record not found!!"
                ];
            }
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


    function checkSPRVoucherNo()
    {
        if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering

        $input = json_decode(file_get_contents('php://input'), true);

        // Validate input
        if (json_last_error() !== JSON_ERROR_NONE || !isset($input['spr_voucher_no'])) {
            die(json_encode([
                'status' => false,
                'message' => 'Invalid JSON data received'
            ]));
        }

        $project_id = getFromSession('project_id');
        $spr_voucher_no = trim($input['spr_voucher_no']);
        $prevVoucher = trim($input['prevVoucher']);

        $SPRsql = "SELECT 'voucher_no' FROM " . SPR_PURCHASE_MASTER_TBL . " WHERE voucher_no='$spr_voucher_no'";
        $SPRres = mysql_query($SPRsql);

        $POsql = "SELECT 'manual_spr_no' FROM " . PURCHASE_OREDR_MASTER_TBL . " WHERE manual_spr_no='$spr_voucher_no'";

        if (isset($prevVoucher) && $prevVoucher == $spr_voucher_no) {
            $POsql .= " AND manual_spr_no !='$prevVoucher'";
        }


        $POres = mysql_query($POsql);

        if (mysql_num_rows($SPRres) > 0 || mysql_num_rows($POres) > 0) {
            $response = [
                'status' => true,
                'message' => 'SPR No Already Exists'
            ];
        } else {
            $response = [
                'status' => false,
                'message' => 'SPR No is Available'
            ];
        }

        // Output response
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }


    function getSPRMasterInfo($voucher_no)
    {
        $project_id = getFromSession('project_id');

        $info = array();
        $info['table'] = SPR_PURCHASE_MASTER_TBL . ' spm,' . PROJECT_TBL . ' p,' . DELIVERY_POINT_TBL . ' d';
        $info['fields'] = array('spm.*', 'p.project_name', 'p.location', 'd.delivery_point_name');
        $info['where'] = "spm.project_id = p.project_id AND spm.store_id = d.delivery_pid AND spm.project_id = '$project_id' AND spm.voucher_no='$voucher_no'";

        //$info['debug']  = true;
        $res = select($info);

        $data = array();
        if (count($res) > 0) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        //dumpVar($data);
        return $data[0];
    }

    function getSPRDetailsInfo($voucher_no)
    {
        $info = array();
        $info['table'] = SPR_PURCHASE_DETAILS_TBL . ' spd,' . PRODUCT_TBL . ' p,' . CATAGORY_TBL . ' c,' . BRAND_TBL . ' b';
        $info['fields'] = array('spd.*', 'b.brand_name', 'p.product_name', 'p.product_code', 'c.catagory_name');
        $sql = "spd.product = p.product_id AND p.brand_code = b.brand_id AND p.catagory = c.catagory_code AND spd.voucher_no = '$voucher_no'";
        $info['where'] = $sql;
        $info['groupby'] = array("spd.id");
        $info['orderby'] = array("spd.id asc");
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

    function showSPRPrintVoucher()
    {
        $voucher_no = getRequest('voucher_no');
        if ($voucher_no) {
            $advArr = $this->getSPRMasterInfo($voucher_no);
            $advArr = parseThisValue($advArr);
            $data = array_merge(array(), $advArr);

            $data['item_list'] = $this->getSPRDetailsInfo($voucher_no);
            $data['cmd'] = getRequest('cmd');

            require_once(STOCK_PURCHASE_REQUSITION_VOUCHER_PRINT_SKIN);
            return true;
        } else {
            $msg = "Missing Voucher No !!!";
            header("location:index.php?app=purchase.item.grn&cmd=spr_list&error_msg=$msg");
            exit;
        }
    }

    function getSPRDetailsList($voucher_no)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $project_id = getFromSession('project_id');

        $tableRow = "";
        $tableRow .= "<table width='100%' align='center' bgcolor='#99CC66'>
	<tr style='color:#fff;' bgcolor='#00B000' height=28>
	  <td width='1%' nowrap><div align='left'>SL</div></td>
	  <td width='20%' nowrap><div align='left'>Product Name</div></td>
	  <td width='5%' nowrap><div align='left'>Unit</div></td>
	  <td width='10%' nowrap><div align='left'>Catagory</div></td>
	  <td width='10%' nowrap><div align='left'>Present Stock</div></td>
	  <td width='8%' nowrap><div align='left'>Request Qty</div></td>				  
	  <td width='7%' nowrap align='center'>Option</td>
	</tr>";

        $totalQty = 0;
        $munit = "";

        $getSql = "
		SELECT pod.*,po.product_name,po.product_code,ct.catagory_name,b.brand_name FROM " . SPR_PURCHASE_DETAILS_TBL . " AS pod
		LEFT JOIN " . PRODUCT_TBL . " AS po ON po.product_id=pod.product
		LEFT JOIN " . CATAGORY_TBL . " AS ct ON ct.catagory_code=pod.catagory
		LEFT JOIN " . BRAND_TBL . " AS b ON b.brand_id=pod.brand_id
		WHERE pod.voucher_no = '$voucher_no' AND pod.project_id = '$project_id'";

        $gres = mysql_query($getSql);
        $i = 0;
        while ($row = mysql_fetch_array($gres)) {
            extract($row);
            $totalQty += $qty;
            $stock_qty = number_format($stock_qty, 2, '.', '');
            $qty = number_format($qty, 2, '.', '');
            $unit_price = number_format($unit_price, 2, '.', '');
            $total = number_format($total, 2, '.', '');
            if (isset($m_unit) && $m_unit != "") {
                $munit = $m_unit;
            }

            $productName = (new CommonList())->normalizeProductName($product_code, $product_name);
            $i += 1;
            $tableRow .= "
            <tr style='color:#000000' bgcolor='#fff'>
		<td width='2%' nowrap align='left'>$i</td>	
              <td width='10%' nowrap align='left'>$productName</td>
              <td width='5%' nowrap align='left'>$m_unit</td>
              <td width='10%' nowrap align='left'>$catagory_name</td>
              <td width='8%' nowrap><div align='left'>$stock_qty</div></td>
              <td width='8%' nowrap align='left'>$qty</td>		  
              			  				  
              <td width='7%' nowrap align='left'>
			<a href='javascript:void(0)' onclick=editSPR('$voucher_no','$id')><img src=\"images/common/icons/edit.gif\"></a>&nbsp;
<a href='javascript:void(0)' onclick=deleteSPR('$voucher_no','$id')><img src=\"images/common/icons/delete.gif\"></a></td>
            </tr>";
        }

        $totalQty = number_format($totalQty, 2, '.', '') . " " . $munit;
        $tableRow .= "
	<tr style='color:#000;' bgcolor='#CCCCCC' height=25>
	  <td colspan='5' nowrap><div align='right'>Total </div></td>
	  <td nowrap align='left'>$totalQty</td>
	  <td nowrap>&nbsp;</td>
	</tr>
	</table>";

        return $tableRow;
    }

    function showSPREditPage()
    {
        $voucher_no = getRequest('voucher_no');
        if ($voucher_no) {
            require_once(CLASS_DIR . '/common.list.class.php');
            $comListApp = new CommonList();

            $advArr = $this->getSPRMasterInfo($voucher_no);
            $advArr = parseThisValue($advArr);
            $data = array_merge(array(), $advArr);

            $supplier_list = $comListApp->getSupplierList();
            $supplier_list_payable = $comListApp->getSupplierListPayable();
            $data['supplier_list'] = array_merge($supplier_list, $supplier_list_payable);

            $data['product_list'] = $comListApp->getProductList();
            $data['depo_list'] = $comListApp->getDeliveryPointList(true);

            $data['itemList'] = $this->getSPRDetailsList($voucher_no);
            $data['cmd'] = getRequest('cmd');

            require_once(STOCK_PURCHASE_REQUSITION_EDIT_SKIN);
            return true;
        } else {
            $msg = "Missing Voucher No !!!";
            header("location:index.php?app=purchase.item.grn&cmd=spr_list&error_msg=$msg");
            exit;
        }
    }

    function updateSPR()
    {
        if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering

        $voucher_no = getRequest('voucher_no');
        if ($voucher_no) {
            $requestdata = array();
            $requestdata = getUserDataSet(SPR_PURCHASE_MASTER_TBL);

            $requestdata['store_id'] = getRequest('store_id');
            $requestdata['purchase_date'] = dateInputFormatYMD(getRequest('purchase_date'));
            $requestdata['unit'] = getRequest('unit');
            $requestdata['department'] = getRequest('department');
            $requestdata['warranty_note'] = getRequest('warranty_note');

            $info = array();
            $info['table'] = SPR_PURCHASE_MASTER_TBL;
            $info['data'] = $requestdata;
            //$info['debug'] =  true;

            $info['where'] = "voucher_no ='$voucher_no'";
            $res = update($info);

            $msg = "Record updated successfully!!!";
            header("location:index.php?app=purchase.item.grn&cmd=spr_edit&voucher_no=$voucher_no&msg=$msg");
            exit;
        } else {
            $msg = "Missing Voucher No !!!";
            header("location:index.php?app=purchase.item.grn&cmd=spr_list&error_msg=$msg");
            exit;
        }
    }


    function addSPRItem()
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
        $id = trim($input['id']);

        $response = [
            'status' => false,
            'message' => "Data must be fillable"
        ];

        if ($voucher_no != "") {
            $catagory = trim($input['catagory']);
            $brand_id = trim($input['brand_id']);
            $product = trim($input['product']);
            $m_unit = trim($input['m_unit']);
            $unit_price = trim($input['unit_price']);
            $qty = trim($input['qty']);
            $stock_qty = trim($input['present_stock']);
            $total = trim($input['total']);

            if ($product == '' || $stock_qty == '' || $qty == '') {
                $response = [
                    'status' => false,
                    'message' => "Product Data must be fillable !!!"
                ];

                header('Content-Type: application/json');
                echo json_encode($response);
                exit();
            }

            $requestDetailsdata['catagory'] = $catagory;
            $requestDetailsdata['brand_id'] = $brand_id;
            $requestDetailsdata['product'] = $product;
            $requestDetailsdata['m_unit'] = $m_unit;
            $requestDetailsdata['unit_price'] = $unit_price;
            $requestDetailsdata['stock_qty'] = $stock_qty;
            $requestDetailsdata['qty'] = $qty;
            $requestDetailsdata['init_qty'] = $requestDetailsdata['qty'];
            $requestDetailsdata['total'] = $total;


            if ($id != "") {
                $dinfo = array();
                $dinfo['table'] = SPR_PURCHASE_DETAILS_TBL;
                $dinfo['data'] = $requestDetailsdata;
                $dinfo['where'] = "voucher_no='$voucher_no' AND id='$id'";
                //$info['debug']  	=  true;
                update($dinfo);

                $response = [
                    'status' => true,
                    'message' => "Product Updated Successfully!!!"
                ];
            } else {
                $pinfo = array();
                $pinfo['table'] = SPR_PURCHASE_DETAILS_TBL;
                $pinfo['where'] = "voucher_no = '$voucher_no' AND product = '$product'";
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

                $requestDetailsdata['voucher_no'] = $voucher_no;
                $requestDetailsdata['project_id'] = $project_id;
                $requestDetailsdata['qty'] = (float)$requestDetailsdata['qty'] + $prevQty;
                $requestDetailsdata['init_qty'] = $requestDetailsdata['qty'];
                $requestDetailsdata['total'] = $requestDetailsdata['unit_price'] * $requestDetailsdata['qty'];
                $requestDetailsdata['created_by'] = getFromSession('userid');

                $requestDetailsdata['last_purchase_date'] = $requestDetailsdata['last_purchase_date'];


                $info = array();
                $info['table'] = SPR_PURCHASE_DETAILS_TBL;
                $info['data'] = $requestDetailsdata;
                //dumpvar($info);
                //$info['debug']  	=  true;

                if ($updateRow) {
                    $info['where'] = "voucher_no = '$voucher_no' AND product = '$product'";
                    update($info);
                } else {
                    insert($info);
                }


                $response = [
                    'status' => true,
                    'message' => "Product Added Successfully!!!"
                ];
            }
            $response['data'] = $this->getSPRDetailsList($voucher_no);
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


    function getEditSPRItem()
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
        $id = trim($input['id']);

        if ($voucher_no != "" && $id != "") {
            $sql = "SELECT * FROM " . SPR_PURCHASE_DETAILS_TBL . " WHERE voucher_no='$voucher_no' AND id='$id'";
            $res = mysql_fetch_object(mysql_query($sql));

            $response = [
                'status' => true,
                'message' => "Record Deleted Successfully!!!",
                'data' => $res
            ];
        } else {
            $response = [
                'status' => false,
                'message' => "Missing Voucher No OR ID !!!"
            ];
        }

        // Output response
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }


    function deleteSPRItem()
    {
        if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering

        $input = json_decode(file_get_contents('php://input'), true);

        // Validate input
        if (json_last_error() !== JSON_ERROR_NONE || !isset($input['voucher_no']) || empty($input['id'])) {
            die(json_encode([
                'status' => false,
                'message' => 'Invalid JSON data received'
            ]));
        }

        $project_id = getFromSession('project_id');
        $voucher_no = trim($input['voucher_no']);
        $id = trim($input['id']);

        if ($voucher_no != "" && $id != "") {
            $info = array();
            $info['table'] = SPR_PURCHASE_DETAILS_TBL;
            $info['where'] = "voucher_no='$voucher_no' AND id='$id'";

            //$info['debug']  	=  true;
            delete($info);

            $response = [
                'status' => true,
                'message' => "Record Deleted Successfully!!!",
                'data' => $this->getSPRDetailsList($voucher_no)
            ];
        } else {
            $response = [
                'status' => false,
                'message' => "Missing Voucher No OR ID !!!"
            ];
        }

        // Output response
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }


    //===== Saart Save Purchase ====
    function saveTempPurchase()
    {
        if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering

        $edit_product_id = getRequest('edit_product_id');
        $tmp_id = getRequest('tmp_id');
        $po_voucher_no = getRequest('po_voucher_no');
        $pod_id = getRequest('pod_id');
        $max_qty = getRequest('max_qty');
        $edit_product_qty = getRequest('edit_product_qty');

        if ($tmp_id != "") {
            if ($edit_product_id != getRequest('productid')) {
                $requestdata = array();
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
                $requestdata['serial'] = getRequest('serial');
                $requestdata['warranty_note'] = getRequest('warranty');
                $requestdata['qty'] = getRequest('qty');
                $requestdata['free_qty'] = getRequest('free_qty');
                $requestdata['unit_price'] = getRequest('unit_price');
                $requestdata['unit_discount'] = getRequest('unit_discount');
                $requestdata['discount_amount'] = getRequest('discount_amount');
                $requestdata['total'] = getRequest('total');

                $requestdata['po_voucher_no'] = "";
                $requestdata['pod_id'] = "";
                $requestdata['max_qty'] = "";

                $info = array();
                $info['table'] = TEMP_GRN_TBL;
                $info['data'] = $requestdata;
                $info['where'] = "tmp_id ='$tmp_id'";
                //$info['debug']  	=  true;
                $res = update($info);

                $this->updatePOdetailsQty($edit_product_qty, $po_voucher_no, $pod_id, $max_qty);
            } else {
                $requestdata = array();
                $requestdata['details'] = getRequest('details');
                $requestdata['serial'] = getRequest('serial');
                $requestdata['warranty_note'] = getRequest('warranty');
                $requestdata['qty'] = getRequest('qty');
                $requestdata['free_qty'] = getRequest('free_qty');
                $requestdata['unit_price'] = getRequest('unit_price');
                $requestdata['unit_discount'] = getRequest('unit_discount');
                $requestdata['discount_amount'] = getRequest('discount_amount');
                $requestdata['total'] = getRequest('total');

                $info = array();
                $info['table'] = TEMP_GRN_TBL;
                $info['data'] = $requestdata;
                $info['where'] = "tmp_id ='$tmp_id'";
                //$info['debug']  	=  true;
                $res = update($info);

                $qty = getRequest('qty');
                if ($qty <= $max_qty) {
                    $newQty = (float)$max_qty - (float)$qty;
                    $status = 0;
                    if ($max_qty == $qty) {
                        $newQty = (float)$qty;
                        $status = 1;
                    }

                    $this->updatePOdetailsExistsProductQty($newQty, $po_voucher_no, $pod_id, $status);
                }
            }
        } else {
            //======= Insert into tamp ========	
            $requestdata = array();
            $requestdata = getUserDataSet(TEMP_GRN_TBL);
            $project_id = getFromSession('project_id');
            $requestdata['project_id'] = $project_id;
            $requestdata['supplier'] = getRequest('supplier');
            $requestdata['store_id'] = getRequest('store_id');
            $requestdata['purchase_date'] = formatDate(getRequest('purchase_date'));
            //$requestdata['received_date'] = formatDate(getRequest('received_date'));
            $requestdata['currency'] = getRequest('currency');
            $requestdata['currencyName'] = getRequest('currencyName');
            $requestdata['quotation_no'] = getRequest('quotation_no');
            //$requestdata['delivery_note'] 	= getRequest('delivery_note');
            $requestdata['payment_note'] = getRequest('payment_note');
            //$requestdata['warranty_note'] 	= getRequest('warranty_note');
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
            $requestdata['serial'] = getRequest('serial');
            $requestdata['warranty_note'] = getRequest('warranty');
            $requestdata['qty'] = getRequest('qty');
            $requestdata['free_qty'] = getRequest('free_qty');
            $requestdata['unit_price'] = getRequest('unit_price');
            $requestdata['unit_discount'] = getRequest('unit_discount');
            $requestdata['discount_amount'] = getRequest('discount_amount');
            $requestdata['total'] = getRequest('total');

            $requestdata['created_by'] = getFromSession('userid');
            $info = array();
            $info['table'] = TEMP_GRN_TBL;
            $info['data'] = $requestdata;
            //$info['debug']  	=  true;
            $res = insert($info);
        }

        $response = $this->getTempGRNPurchase();
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }


    function updatePOdetailsQty($addQty, $po_voucher_no, $pod_id, $max_qty)
    {
        if ($pod_id != "") {
            //$sql = "SELECT * FROM ".PURCHASE_ORDER_DETAILS_TBL." WHERE id = '$pod_id'";
            //$result = mysql_fetch_object(mysql_query($sql));
            //$poInitQty = (float)$result->init_qty;
            //$poTotalQty = (float)$result->qty;
            //$max_qty = (float)$max_qty;
            //$newPOQty = (float)$addQty;

            //if($poInitQty != $max_qty){
            //	$newPOQty = $poTotalQty - (float)$addQty;
            //}

            //$requestSPDdata['qty'] = $newPOQty;
            $requestSPDdata['complete_status'] = 0;
            $infoPOD = array();
            $infoPOD['table'] = PURCHASE_ORDER_DETAILS_TBL;
            $infoPOD['data'] = $requestSPDdata;
            $infoPOD['where'] = "id='$pod_id'";
            //$info['debug']  	=  true;
            update($infoPOD);
        }
        if ($po_voucher_no != "") {
            $requestMasterdata['complete_status'] = 0;
            $infoPOM = array();
            $infoPOM['table'] = PURCHASE_OREDR_MASTER_TBL;
            $infoPOM['data'] = $requestMasterdata;
            $infoPOM['where'] = "voucher_no='$po_voucher_no'";
            //$info['debug']  	=  true;
            update($infoPOM);
        }
    }

    function updatePOdetailsExistsProductQty($addQty, $po_voucher_no, $pod_id, $status = 0)
    {
        if ($pod_id != "") {
            $requestSPDdata['qty'] = $addQty;
            $requestSPDdata['complete_status'] = $status;

            $info = array();
            $info['table'] = PURCHASE_ORDER_DETAILS_TBL;
            $info['data'] = $requestSPDdata;
            $info['where'] = "id='$pod_id'";
            //$info['debug']  	=  true;
            update($info);
        }
        if ($po_voucher_no != "") {
            $requestMasterdata['complete_status'] = $status;
            $info = array();
            $info['table'] = PURCHASE_OREDR_MASTER_TBL;
            $info['data'] = $requestMasterdata;
            $info['where'] = "voucher_no='$po_voucher_no'";
            //$info['debug']  	=  true;
            update($info);
        }
    }


    function delTempPurchase()
    {
        $tmp_id = $_REQUEST['id'];
        $pod_id = $_REQUEST['pod_id'];
        $po_voucher_no = $_REQUEST['po_voucher_no'];

        if ($tmp_id != "") {
            if ($pod_id != "") {
                $sqlTmp = "SELECT * FROM " . TEMP_GRN_TBL . " WHERE tmp_id ='$tmp_id'";
                $tmpResult = mysql_fetch_object(mysql_query($sqlTmp));
                $delQty = $tmpResult->qty;

                $sql = "SELECT * FROM " . PURCHASE_ORDER_DETAILS_TBL . " WHERE id='$pod_id'";
                $spdResult = mysql_fetch_object(mysql_query($sql));
                $spdPrevQty = $spdResult->qty;
                $spdInitQty = $spdResult->init_qty;

                $newQty = (float)$spdPrevQty + (float)$delQty;
                if ($spdPrevQty < $spdInitQty && $newQty <= $spdInitQty && $spdPrevQty != $delQty) {
                    $requestSPDdata['qty'] = $newQty;
                }

                $requestSPDdata['complete_status'] = 0;
                $info = array();
                $info['table'] = PURCHASE_ORDER_DETAILS_TBL;
                $info['data'] = $requestSPDdata;
                $info['where'] = "id='$pod_id'";
                //$info['debug']  	=  true;
                update($info);
            }
            if ($po_voucher_no != "") {
                $requestMasterdata['complete_status'] = 0;
                $info = array();
                $info['table'] = PURCHASE_OREDR_MASTER_TBL;
                $info['data'] = $requestMasterdata;
                $info['where'] = "voucher_no='$po_voucher_no'";
                //$info['debug']  	=  true;
                update($info);
            }
            $dsql = "DELETE FROM " . TEMP_GRN_TBL . " WHERE tmp_id ='" . $tmp_id . "'";
            mysql_query($dsql);
        }
        header("location:?app=purchase.item.grn&cmd=add");
    }

    function getTempGRNPurchase()
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $project_id = getFromSession('project_id');
        $str1 = "<table width='100%' align='center' bgcolor='#99CC66'>
	<tr style='color:#fff;' bgcolor='#00B000' height=28>
	  <td width='1%' nowrap><div align='left'>SL</div></td>
	  <td width='20%' nowrap><div align='left'>Product Name</div></td>
	  <td width='10%' nowrap><div align='left'>Catagory</div></td>
	  <td width='10%' nowrap><div align='left'>Brand</div></td>
	  <td width='8%' nowrap><div align='right'>Serial</div></td>
	  <td width='8%' nowrap><div align='right'>Warranty</div></td>		  
	  <td width='10%' nowrap><div align='right'>Order Qty</div></td>		  
	  <td width='10%' nowrap><div align='right'>Received Qty</div></td>				  
	  <td width='7%' nowrap align='center'>Option</td>
	</tr>";
        $total_value = 0;
        $product_discount = 0;
        $TotalQty = 0;
        $TotalFreeQty = 0;
        $sl = 1;
        //$getSql		= "SELECT * FROM ".TEMP_GRN_TBL." WHERE created_by = '".getFromSession('userid')."' AND project_id='".$project_id."'";

        $getSql = "SELECT t.*, p.product_code 
           FROM " . TEMP_GRN_TBL . " t
           LEFT JOIN " . PRODUCT_TBL . " p 
               ON p.product_id = t.productid
           WHERE t.created_by = '" . getFromSession('userid') . "' 
           AND t.project_id = '" . $project_id . "'";

        $gres = mysql_query($getSql);
        $supplier_id = "";
        $cost_center = "";
        $payable_id = "";
        $storeId = "";
        $purchaseDate = "";
        $poVoucherNoArr = [];
        $purchase_type = "";
        while ($row = mysql_fetch_array($gres)) {
            extract($row);
            $TotalQty += $qty;
            $product_discount += $discount_amount;
            $total_value += $total;
            $poVoucherNoArr[] = $po_voucher_no;
            $purchase_type = $purchase_type;
            if ($supplier_id == "") {
                $supplier_id = $supplier;
            }
            if ($cost_center == "") {
                $cost_center = $cost_center;
            }
            if ($payable_id == "") {
                $payable_id = $payable_id;
            }
            if ($storeId == "") {
                $storeId = $store_id;
            }
            if ($purchaseDate == "" && $purchase_date != "0000-00-00") {
                $purchaseDate = $purchase_date;
            }
            $product_name = (new CommonList())->normalizeProductName($product_code, $product_name);
            $str2 .= "
	<tr style='color:#000000' bgcolor='#fff'>
	  <td width='1%' nowrap>$sl</td>
	  <td width='20%' nowrap align='left'>$product_name ($po_voucher_no)</td>
	  <td width='10%' nowrap align='left'>$catagoryname</td>
	  <td width='10%' nowrap align='left'>$brandname</td>
	  <td width='8%' nowrap><div align='right'>$serial</div></td>
	  <td width='8%' nowrap><div align='right'>$warranty_note</div></td>
	  <td width='10%' nowrap><div align='right'>$max_qty $munit</div></td>
	  <td width='10%' nowrap><div align='right'>$qty $munit</div></td>				  				  
	  <td width='7%' nowrap align='center'><div align='center' class='table-option'><a href=\"javascript:void(0)\" onclick=\"editTempPo('$tmp_id')\" style=\"margin: 3px 6px;\"><img src=\"images/common/icons/edit.gif\"></a><a href=\"?app=purchase.item.grn&cmd=delTempGrn&id=$tmp_id&pod_id=$pod_id&po_voucher_no=$po_voucher_no\"><img src=\"images/common/icons/delete.gif\"></a></div></td>
	</tr>";
            $sl++;
        }
        $str3 = "
	<tr style='color:#000;' bgcolor='#CCCCCC' height=25>
	  <td colspan='7' nowrap><div align='right'>Total </div></td>
	  <td nowrap align='right'>$TotalQty $munit</td>
	  <td nowrap align='center'>&nbsp;</td>
	</tr>
	</table>";
        $poVoucherNoArr = array_values(array_unique($poVoucherNoArr));
        $poVoucherNoArr = implode(",", $poVoucherNoArr);
        $poVoucherNoArr = trim($poVoucherNoArr, ",");

        if ($purchaseDate) {
            $date = DateTime::createFromFormat('Y-m-d', $purchaseDate);
            $purchaseDate = $date->format('d-m-Y');
        }

        $response = [
            "table" => $str1 . $str2 . $str3,
            "total_value" => $total_value,
            "discount" => $product_discount,
            "supplier_id" => $supplier_id,
            "cost_center" => $cost_center,
            "payable_id" => $payable_id,
            "storeId" => $storeId,
            "purchase_date" => $purchaseDate,
            "poVoucherNo" => $poVoucherNoArr,
            "purchaseType" => $purchase_type,
        ];
        return $response;
    }

    //====== End Save Purchase =====
    function insertPurchaseDetails($voucher_no)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comlistApp = new CommonList();
        $requestdata = array();
        $arr_catagory_product_id = array();
        $project_id = getFromSession('project_id');
        $currency = getRequest('currency');
        $supplier_purchase_type = getRequest('supplierPurchaseType');
        $TotalFreeAmount = 0;
        $TotalStockAmount = 0;
        $getSql = "SELECT * FROM " . TEMP_GRN_TBL . " WHERE created_by = '" . getFromSession('userid') . "' AND project_id='" . getFromSession('project_id') . "'";
        $gres = mysql_query($getSql);

        $totalVatAmount = 0;
        $totalPriceAmount = 0;
        $totalVatPercent = 0;
        $total_vat_amount = 0;
        $total_vat_type = "";
        $po_voucher_arr = [];
        if (mysql_num_rows($gres) > 0) {
            while ($row = mysql_fetch_object($gres)) {
                $requestdata['project_id'] = $project_id;
                $requestdata['catagory'] = $row->catagory;
                $requestdata['brand_id'] = $row->brand_id;
                $brand_id = $row->brand_id;
                $requestdata['product'] = $row->productid;
                $product_id = $row->productid;
                if ($row->warranty_note != "") {
                    $requestdata['warranty'] = $row->warranty_note;
                } else {
                    $requestdata['warranty'] = 0;
                }
                if ($row->serial != "") {
                    $requestdata['serial'] = $row->serial;
                } else {
                    $requestdata['serial'] = 0;
                }
                $serial = $row->serial;
                $warranty = $row->warranty;
                $requestdata['discount_per_qty'] = $row->unit_discount;
                $requestdata['details'] = $row->details;
                $requestdata['unit_price'] = $row->unit_price;
                $requestdata['qty'] = $row->qty;
                $productQty = ($row->free_qty + $row->qty);
                $requestdata['free_qty'] = $row->free_qty;
                $requestdata['rec_qty'] = $row->max_qty;
                $requestdata['m_unit'] = $row->munit;
                $requestdata['total'] = $row->total;
                $requestdata['discount_amount'] = (($row->unit_price / 100) * $requestdata['discount_per_qty']);
                $requestdata['created_by'] = getFromSession('userid');
                $requestdata['po_no'] = $row->po_voucher_no;
                $po_voucher_arr[] = $row->po_voucher_no;
                $requestdata['pod_id'] = $row->pod_id;
                $requestdata['created_date'] = date('Y-m-d h:i:s');
                $requestdata['project_id'] = $project_id;

                $totalPrice = (float)$row->unit_price * (float)$row->qty;
                $totalPriceAmount += $totalPrice;
                $vatPercent = (float)$row->vat_percent;
                if ($vatPercent > 0) {
                    $vatAmount = (($vatPercent / 100) * $totalPrice);
                    $totalVatAmount += $vatAmount;
                }

                if ($totalVatPercent <= 0) {
                    $totalVatPercent = (float)$row->total_vat_percent;
                }
                if ($total_vat_amount <= 0) {
                    $total_vat_amount = (float)$row->total_vat_amount;
                }
                if (empty($total_vat_type)) {
                    $total_vat_type = $row->total_vat_type;
                }

                $requestdata['voucher_no'] = $voucher_no;
                $requestdata['lc_no'] = $row->lc_no; //getRequest('lc_no');
                $requestdata['supplier'] = getRequest('supplier');
                $supplier = getRequest('supplier');
                $created_date = formatDate(getRequest('purchase_date'));
                $info = array();
                $info['table'] = PURCHASE_DETAILS_TBL;
                $info['data'] = $requestdata;
                //$info['debug']  	=  true;    
                $res = insert($info);


                if ($res) {

                    $m_unit = $requestdata['m_unit'];
                    $unit_price = $requestdata['unit_price'];
                    $StockAmount = ($unit_price * $productQty);
                    $TotalStockAmount += $StockAmount;
                    $this->saveAVGPurchasePrice($voucher_no, $project_id, $product_id, $unit_price);
                    $totalCR = $this->getTotalCreditStock($product_id, getFromSession('project_id'));
                    $totalDR = $this->getTotalDebitStock($product_id, getFromSession('project_id'));
                    $balance = (($totalDR + $productQty) - $totalCR);
                    $Prosql = "SELECT product_type FROM " . PRODUCT_TBL . " WHERE product_id = '$product_id' AND project_id = '$project_id'";
                    $Prorow = mysql_fetch_object(mysql_query($Prosql));
                    $product_type = $Prorow->product_type;
                    $inventory_auto_out = getFromSession('inventory_auto_out');
                    $store_id = getRequest('store_id');
                    if ($inventory_auto_out == 1 && $product_type == "Invetory Item") {
                        $this->saveStockJournal($voucher_no, $project_id, $store_id, $product_id, $product_type, $serial, $warranty, $unit_price, $m_unit, $productQty, 0, $balance, $created_date, $supplier_purchase_type);
                        $Autobalance = ($totalDR - ($totalCR + $productQty));
                        $this->saveStockJournal($voucher_no, $project_id, $store_id, $product_id, $product_type, $serial, $warranty, $unit_price, $m_unit, 0, $productQty, $Autobalance, $created_date, $supplier_purchase_type);
                    } else {
                        //=== Stock Dr =====		
                        $this->saveStockJournal($voucher_no, $project_id, $store_id, $product_id, $product_type, $serial, $warranty, $unit_price, $m_unit, $productQty, 0, $balance, $created_date, $supplier_purchase_type);
                    }
                    if ($row->free_qty > 0) {
                        $FreeDrAmount = ($unit_price * $row->free_qty);
                        $TotalFreeAmount += $FreeDrAmount;
                        $FreeDrAmount = 0;
                    }

                    $inventory_type = getRequest('inventory_type');
                    if (isset($inventory_type) && $inventory_type != "" && $inventory_type != "A000036") {
                        $productRequestData['unit_price'] = $row->unit_price;
                    }

                    $productRequestData['purchase_unit_price'] = $row->unit_price;

                    $infoData = array();
                    $infoData['table'] = PRODUCT_TBL;
                    $infoData['data'] = $productRequestData;
                    $infoData['where'] = "product_id ='" . $product_id . "'";
                    //$infoData['debug']  	=  true;
                    $productRes = update($infoData);
                }// end purchase save

            }// end while 


            $vatHeadID = getRequest('vat_type');
            $purchase_date = formatDate(getRequest('purchase_date'));

            $totalVatAmount = $total_vat_amount; // remove this if need product vat amount

            if ($total_vat_type == "percent" && $totalVatPercent > 0 && $totalPriceAmount > 0) {
                $totalVatAmount = (($totalVatPercent / 100) * $totalPriceAmount);
            } elseif ($total_vat_type == "fixed") {
                $totalVatAmount = $total_vat_amount;
            }

            $totalVatAmount = 0;
            if ($totalVatAmount > 0 && $vatHeadID != "") {
                $totalPartyCR = $comlistApp->getTotalCreditAmount($vatHeadID, $project_id);
                $totalPartyDR = $comlistApp->getTotalDebitAmount($vatHeadID, $project_id);
                $balance = (($totalPartyDR + $totalVatAmount) - $totalPartyCR);
                $transaction_type = "VAT on purchase";
                $description = "Purchase vat amount";
                //$comlistApp->saveAccJournal($voucher_no, $vatHeadID, "Purchase", $transaction_type, $project_id, $description, $totalVatAmount, 0, $balance, 0, $purchase_date, 0, $supplier_purchase_type);
            }

            //=== Stock Dr Amount =====
            if ($TotalStockAmount > 0) {
                $StockId = getRequest('inventory_type');
                if (!empty($StockId)) {
                    $inventory_id = getRequest('inventory_id');
                    if (!empty($inventory_id)) {
                        $StockId = $inventory_id;
                    }
                }
                $TotalStockAmount += $totalVatAmount;
                $TotalStock = $comlistApp->getAccounceBalance($StockId, $project_id);
                $StockBalance = ($TotalStock + $TotalStockAmount);
                $transaction_type = $comlistApp->getTransactionType(getRequest('store_id'), "Purchase Item");
                $description = "Purchase Item";
                if ($totalVatAmount > 0) {
                    $description .= " And Vat amount is " . $totalVatAmount;
                }
                $comlistApp->saveAccJournal($voucher_no, $StockId, "Stock", $transaction_type, $project_id, $description, $TotalStockAmount, 0, $StockBalance, 0, $created_date, 0, $supplier_purchase_type);
            }
            //========= Free Product Cr ==========
            if ($TotalFreeAmount > 0) {
                $description = "Receipt free with purchase item";
                $freeItemhead = $comlistApp->getPurchaseDiscountId($project_id);
                $TotalFreeBL = $comlistApp->getAccounceBalance($freeItemhead, $project_id);
                $freeItemBalance = ($TotalFreeBL - $TotalFreeAmount);
                $comlistApp->saveAccJournal($voucher_no, $freeItemhead, "Acc", "Free Item", $project_id, $description, 0, $TotalFreeAmount, $freeItemBalance, 0, $created_date, 0, $supplier_purchase_type);
            }

            if ($totalVatPercent > 0 && $totalVatAmount > 0) {
                $inputPOMData['vat_percentage'] = $totalVatPercent;
                $inputPOMData['vat_amount'] = $totalVatAmount;
            }

            //if ($totalVatAmount > 0 && $totalPriceAmount > 0) {
            //    $vatPercentage = (($totalVatAmount / $totalPriceAmount) * 100);
            //    if($vatPercentage > 0){
            //	$inputPOMData['vat_percentage'] = $vatPercentage;
            //	$inputPOMData['vat_amount'] = $totalVatAmount;
            //    }
            //}

            $po_voucher_arr = implode(',', array_unique(array_filter($po_voucher_arr)));
            $inputPOMData['po_no'] = $po_voucher_arr;

            $infoData = array();
            $infoData['table'] = PURCHASE_MASTER_TBL;
            $infoData['data'] = $inputPOMData;
            $infoData['where'] = "voucher_no ='$voucher_no'";
            //$infoData['debug']  	=  true;
            update($infoData);
        }// end if


        if ($res) {
            $dsql = "DELETE FROM " . TEMP_GRN_TBL . " WHERE created_by = '" . getFromSession('userid') . "' AND project_id='" . getFromSession('project_id') . "'";
            mysql_query($dsql);
        }
    } //End of the function insertSalesDetails()

    //==================== saveDebitVouchar ====================
    function saveDebitVouchar()
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comlistApp = new CommonList();
        $mode_of_payment = getRequest('mode_of_payment');
        $requestdata = array();
        $requestdata = getUserDataSet(DEVIT_VOUCHAR_TBL);
        if ($mode_of_payment == "Check") {
            $requestdata['bank_name'] = getRequest('bank_name');
            $requestdata['acc_no'] = getRequest('acc_no');
            $requestdata['check_no'] = getRequest('check_no');
            $requestdata['check_issue_date'] = formatDate(getRequest('check_issue_date'));
            $requestdata['account_head'] = getRequest('supplier');
            $requestdata['head_type'] = "Supplier";
            $requestdata['debit'] = getRequest('paid_amount');
            $requestdata['credit'] = 0;
        } elseif ($mode_of_payment == "Cash") {
            $requestdata['bank_name'] = "";
            $requestdata['acc_no'] = "";
            $requestdata['check_no'] = "";
            $requestdata['check_issue_date'] = "";
            $requestdata['account_head'] = getRequest('supplier');
            $requestdata['debit'] = getRequest('paid_amount');
            $requestdata['credit'] = 0;
            $requestdata['head_type'] = "Supplier";
        } elseif ($mode_of_payment == "Payable") {
            //======= Stock Cr ======
            $DrAccountId = getRequest('inventory_type');

            if (!empty($DrAccountId)) {
                $inventory_id = getRequest('inventory_id');
                if (!empty($inventory_id)) {
                    $DrAccountId = $inventory_id;
                }
            }
            $requestdata['bank_name'] = "";
            $requestdata['acc_no'] = "";
            $requestdata['check_no'] = "";
            $requestdata['check_issue_date'] = "";
            $requestdata['account_head'] = $DrAccountId;
            $requestdata['debit'] = getRequest('due');
            $requestdata['credit'] = 0;
            $requestdata['head_type'] = "Acc";
        }
        $requestdata['transaction_type'] = "Payment";
        $requestdata['project_id'] = getFromSession('project_id');
        $requestdata['created_by'] = getFromSession('userid');
        $requestdata['created_date'] = formatDate(getRequest('purchase_date'));

        $voucher_no = $this->createVoucharID();
        if ($voucher_no != "" && $voucher_no != "PI999999") {
            $requestdata['voucher_no'] = $voucher_no;
        } else {
            if ($voucher_no == "PI999999") {
                $msg = "ID overflow !!!";
                header("location:index.php?app=user_home&msg=$msg");
            } elseif ($voucher_no == "") {
                $msg = "ID is Empty !!! Try again";
                header("location:index.php?app=purchase.item.grn&msg=$msg");
            }
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
            mysql_query("ROLLBACK;");
            header("location:index.php?app=purchase.item.grn&cmd=add");
            exit;
        }

    }//EOFn  

    function saveCreditVouchar($voucher_no, $grn_voucher)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comlistApp = new CommonList();
        $mode_of_payment = getRequest('mode_of_payment');
        $project_id = getFromSession('project_id');
        $supplier_purchase_type = getRequest('supplierPurchaseType');
        $requestdata = array();
        $requestdata = getUserDataSet(CREDIT_VOUCHAR_TBL);
        if ($mode_of_payment == "Check") {
            $requestdata['bank_name'] = getRequest('bank_name');
            $requestdata['acc_no'] = getRequest('acc_no');
            $requestdata['check_no'] = getRequest('check_no');
            $requestdata['check_issue_date'] = formatDate(getRequest('check_issue_date'));
            $requestdata['account_head'] = getRequest('acc_no');
            $requestdata['debit'] = 0;
            $requestdata['credit'] = getRequest('paid_amount');
            $requestdata['head_type'] = "Check";
        } elseif ($mode_of_payment == "Cash") {
            $requestdata['bank_name'] = "";
            $requestdata['acc_no'] = "";
            $requestdata['check_no'] = "";
            $requestdata['check_issue_date'] = "";
            if (getFromSession('u_type_id') == 102) { // 102 = Pharmecy
                $requestdata['account_head'] = getFromSession('cash_id');
            } else {
                $requestdata['account_head'] = $comlistApp->getCashId($project_id);
            }
            $requestdata['debit'] = 0;
            $requestdata['credit'] = getRequest('paid_amount');
            $requestdata['head_type'] = "Acc";
        } elseif ($mode_of_payment == "Payable") {
            //======= Party Dr ======
            $requestdata['bank_name'] = "";
            $requestdata['acc_no'] = "";
            $requestdata['check_no'] = "";
            $requestdata['check_issue_date'] = "";
            $requestdata['account_head'] = getRequest('supplier');
            $requestdata['credit'] = getRequest('due');
            $requestdata['debit'] = 0;
            $requestdata['head_type'] = "Supplier";
        }
        $requestdata['transaction_type'] = "Payment";
        $requestdata['project_id'] = getFromSession('project_id');
        $requestdata['created_by'] = getFromSession('userid');
        $requestdata['created_date'] = formatDate(getRequest('purchase_date'));
        $requestdata['voucher_no'] = $voucher_no;

        $info = array();
        $info['table'] = CREDIT_VOUCHAR_TBL;
        $info['data'] = $requestdata;
        //$info['debug']  	=  true;
        $res = insert($info);
        $created_date = $requestdata['created_date'];
        $cost_center = getRequest('cost_center');

        if ($res['affected_rows']) {
            $totalVataount = $this->getGRNVatAmount();
            $CrAmount = getRequest('paid_amount');
            $due = getRequest('due');
            $project_id = getFromSession('project_id');
            $description = getRequest('description');
            if (getRequest('advanced_paid_amount') == 0 || getRequest('advanced_paid_amount') == "") {
                if ($mode_of_payment == "Cash") {
                    if (getRequest('due') > 0) {
                        //======= Supplier Cr ======	
                        $description = getRequest('description');
                        if ($description == "") {
                            $description = "Amount payable against purchase item";
                        }
                        $fullCr = getRequest('net_payble');
                        if ($totalVataount > 0) {
                            $fullCr = (float)$fullCr + $totalVataount;
                            $description .= " And Vat amount is " . $totalVataount;
                        }
                        $PartyAcc_head = getRequest('supplier');
                        $totalPartyCR = $comlistApp->getTotalCreditAmount($PartyAcc_head, $project_id);
                        $totalPartyDR = $comlistApp->getTotalDebitAmount($PartyAcc_head, $project_id);
                        $PreviousPartyBalance = ($totalPartyDR - $totalPartyCR);
                        $PartyBalance = ($totalPartyDR - ($totalPartyCR + $fullCr));
                        $this->saveAccountJournal($voucher_no, $PartyAcc_head, "Supplier", $project_id, $description, 0, $fullCr, $PartyBalance, 1, $created_date, $cost_center, $grn_voucher, $supplier_purchase_type);
                        //======= Supplier Dr ======	
                        $description = getRequest('description');
                        if ($description == "") {
                            $description = "Paid amount by cash against purchase item";
                        }
                        $DrAmount = getRequest('paid_amount');
                        $PartyAcc_head1 = getRequest('supplier');
                        $totalPartyCR1 = $comlistApp->getTotalCreditAmount($PartyAcc_head1, $project_id);
                        $totalPartyDR1 = $comlistApp->getTotalDebitAmount($PartyAcc_head1, $project_id);
                        $PartyBalance1 = (($totalPartyDR1 + $DrAmount) - $totalPartyCR1);
                        $this->saveAccountJournal($voucher_no, $PartyAcc_head1, "Supplier", $project_id, $description, $DrAmount, 0, $PartyBalance1, 1, $created_date, "", "", $supplier_purchase_type);
                        //============== Cash Cr ===============
                        $description = getRequest('description');
                        if ($description == "") {
                            $description = "Paid amount by cash against purchase item";
                        }
                        if (getFromSession('u_type_id') == 102) { // 102 = Pharmecy
                            $acc_head = getFromSession('cash_id');
                        } else {
                            $acc_head = $comlistApp->getCashId($project_id);
                        }
                        $totalCR = $comlistApp->getTotalCreditAmount($acc_head, $project_id);
                        $totalDR = $comlistApp->getTotalDebitAmount($acc_head, $project_id);
                        $balance = ($totalDR - ($totalCR + $CrAmount));
                        $this->saveAccountJournal($voucher_no, $acc_head, "Cash", $project_id, $description, 0, $CrAmount, $balance, 0, $created_date, "", "", $supplier_purchase_type);

                    } elseif (getRequest('due') == 0) {
                        //======= Supplier Cr ======	
                        $description = getRequest('description');
                        if ($description == "") {
                            $description = "Amount payable against purchase item";
                        }
                        $fullCr = getRequest('net_payble');
                        if ($totalVataount > 0) {
                            $fullCr = (float)$fullCr + $totalVataount;
                            $description .= " And Vat amount is " . $totalVataount;
                        }
                        $PartyAcc_head1 = getRequest('supplier');
                        $totalPartyCR1 = $comlistApp->getTotalCreditAmount($PartyAcc_head1, $project_id);
                        $totalPartyDR1 = $comlistApp->getTotalDebitAmount($PartyAcc_head1, $project_id);
                        $PreviousPartyBalance = ($totalPartyDR1 - $totalPartyCR1);
                        $PartyBalance1 = ($totalPartyDR1 - ($totalPartyCR1 + $fullCr));
                        $this->saveAccountJournal($voucher_no, $PartyAcc_head1, "Supplier", $project_id, $description, 0, $fullCr, $PartyBalance1, 0, $created_date, $cost_center, $grn_voucher, $supplier_purchase_type);
                        //======= Supplier Dr ======
                        $description = getRequest('description');
                        if ($description == "") {
                            $description = "Paid amount by cash against purchase item";
                        }
                        $DrAmount = getRequest('paid_amount');
                        $PartyAcc_head = getRequest('supplier');
                        $totalPartyCR = $comlistApp->getTotalCreditAmount($PartyAcc_head, $project_id);
                        $totalPartyDR = $comlistApp->getTotalDebitAmount($PartyAcc_head, $project_id);
                        $PartyBalance = (($totalPartyDR + $DrAmount) - $totalPartyCR);
                        $this->saveAccountJournal($voucher_no, $PartyAcc_head, "Supplier", $project_id, $description, $DrAmount, 0, $PartyBalance, 0, $created_date, "", "", $supplier_purchase_type);
                        //============== Cash Cr ===============
                        $description = getRequest('description');
                        if ($description == "") {
                            $description = "Paid amount by cash against purchase item";
                        }
                        if (getFromSession('u_type_id') == 102) { // 102 = Pharmecy
                            $acc_head = getFromSession('cash_id');
                        } else {
                            $acc_head = $comlistApp->getCashId($project_id);
                        }
                        $totalCR = $comlistApp->getTotalCreditAmount($acc_head, $project_id);
                        $totalDR = $comlistApp->getTotalDebitAmount($acc_head, $project_id);
                        $balance = ($totalDR - ($totalCR + $CrAmount));
                        $this->saveAccountJournal($voucher_no, $acc_head, "Cash", $project_id, $description, 0, $CrAmount, $balance, 0, $created_date, "", "", $supplier_purchase_type);
                    }
                } elseif ($mode_of_payment == "Check") {
                    //====== save payable_check ======
                    $this->savePayableCheck($voucher_no, $voucher_no, "Payment", getRequest('paid_amount'));
                    //======= Supplier Cr ======
                    $description = getRequest('description');
                    if ($description == "") {
                        $description = "Amount payable against purchase item";
                    }
                    $fullCr = getRequest('net_payble');
                    if ($totalVataount > 0) {
                        $fullCr = (float)$fullCr + $totalVataount;
                        $description .= " And Vat amount is " . $totalVataount;
                    }
                    $PartyAcc_head1 = getRequest('supplier');
                    $totalPartyCR1 = $comlistApp->getTotalCreditAmount($PartyAcc_head1, $project_id);
                    $totalPartyDR1 = $comlistApp->getTotalDebitAmount($PartyAcc_head1, $project_id);
                    $PreviousPartyBalance = ($totalPartyDR1 - $totalPartyCR1);
                    $PartyBalance1 = ($totalPartyDR1 - ($totalPartyCR1 + $fullCr));
                    $this->saveAccountJournal($voucher_no, $PartyAcc_head1, "Supplier", $project_id, $description, 0, $fullCr, $PartyBalance1, 0, $created_date, $cost_center, $grn_voucher, $supplier_purchase_type);
                    //======= Supplier Dr ======
                    $description = getRequest('description');
                    if ($description == "") {
                        $description = "Paid amount by cheque against purchase item";
                    }
                    $DrAmount = getRequest('paid_amount');
                    $PartyAcc_head = getRequest('supplier');
                    $totalPartyCR = $comlistApp->getTotalCreditAmount($PartyAcc_head, $project_id);
                    $totalPartyDR = $comlistApp->getTotalDebitAmount($PartyAcc_head, $project_id);
                    $PartyBalance = (($totalPartyDR + $DrAmount) - $totalPartyCR);
                    $this->saveAccountJournal($voucher_no, $PartyAcc_head, "Supplier", $project_id, $description, $DrAmount, 0, $PartyBalance, 0, $created_date, "", "", $supplier_purchase_type);
                } elseif ($mode_of_payment == "Payable") {
                    //======= Supplier Cr ======	
                    $description = getRequest('description');
                    if ($description == "") {
                        $description = "Amount payable against purchase item";
                    }
                    $fullCr = getRequest('net_payble');
                    if ($totalVataount > 0) {
                        $fullCr = (float)$fullCr + $totalVataount;
                        $description .= " And Vat amount is " . $totalVataount;
                    }
                    $PartyAcc_head1 = getRequest('supplier');
                    $totalPartyCR1 = $comlistApp->getTotalCreditAmount($PartyAcc_head1, $project_id);
                    $totalPartyDR1 = $comlistApp->getTotalDebitAmount($PartyAcc_head1, $project_id);
                    $PreviousPartyBalance = ($totalPartyDR1 - $totalPartyCR1);
                    $PartyBalance1 = ($totalPartyDR1 - ($totalPartyCR1 + $fullCr));
                    $this->saveAccountJournal($voucher_no, $PartyAcc_head1, "Supplier", $project_id, $description, 0, $fullCr, $PartyBalance1, 0, $created_date, $cost_center, $grn_voucher, $supplier_purchase_type);
                }

            }
            return $PreviousPartyBalance;
        } else {
            mysql_query("ROLLBACK;");
            header("location:index.php?app=purchase.item.grn&cmd=add");
            exit;
        }

    }//EOFn   

    function insertPurchaseMaster($voucher_no, $PreviousPartyBalance, $grn_voucher)
    {
        require_once(CLASS_DIR . '/purchase.class.php');
        $parchApp = new Purchase();
        $project_id = getFromSession('project_id');
        $supplier_purchase_type = getRequest('supplierPurchaseType');
        $requestdata = array();
        $requestdata = getUserDataSet(PURCHASE_MASTER_TBL);
        if ($mode_of_payment == "Check") {
            $requestdata['check_no'] = formatDate(getRequest('check_no'));
            $requestdata['check_issue_date'] = formatDate(getRequest('check_issue_date'));
        }
        if (getRequest('lcopening_date') != "") {
            $requestdata['lcopening_date'] = formatDate(getRequest('lcopening_date'));
        }
        if (getRequest('advanced_paid_amount') > 0) {
            $requestdata['purchase_type'] = "Advanced Paid";
            $requestdata['paid_amount'] = (getRequest('paid_amount') + getRequest('advanced_paid_amount'));
        } else {
            $requestdata['purchase_type'] = getRequest('purchase_type');
        }

        $requestdata['supplier_purchase_type'] = $supplier_purchase_type;

        $requestdata['cost_center'] = getRequest('cost_center');
        $requestdata['item_received_amount'] = getRequest('total_value');
        $requestdata['transaction_type'] = "Payment";
        $requestdata['purchase_date'] = formatDate(getRequest('purchase_date'));
        $requestdata['voucher_no'] = $voucher_no;
        $dueAmount = $requestdata['item_received_amount'] - getRequest('paid_amount');
        if ($PreviousPartyBalance > 0 && $dueAmount >= 0) {
            $supplier = getRequest('supplier');
            $purchase_date = formatDate(getRequest('purchase_date'));
            $restofAmount = $parchApp->saveAdjustSupplierReceibavle($supplier, $voucher_no, $dueAmount, $purchase_date);
            $adjustAmount = ($requestdata['item_received_amount'] - $restofAmount);
            $requestdata['due'] = ($requestdata['net_payble'] - $adjustAmount);
        } else {
            $adjustAmount = $PreviousPartyBalance;
        }
        $purchase_date = formatDate(getRequest('purchase_date'));
        $general_discount_amount = getRequest('general_discount_amount');
        $exclusive_discount_amount = getRequest('exclusive_discount_amount');
        $additional_discount = getRequest('additional_discount');
        $product_discount = getRequest('discount');
        $requestdata['total_value'] = getRequest('total_value');
        $requestdata['product_discount'] = getRequest('discount');
        $TotalDiscount = ($general_discount_amount + $exclusive_discount_amount + $additional_discount + $product_discount);
        $requestdata['discount'] = $TotalDiscount;

        require_once(CLASS_DIR . '/common.list.class.php');
        $comlistApp = new CommonList();

        if ($TotalDiscount > 0) {
            $DisAmount = $TotalDiscount;
            //========= Purchase Discount Cr =========
            $DiscountId = $comlistApp->getPurchaseDiscountId($project_id);
            if ($DiscountId) {
                $description = "Give discount with purchase item";
                $DiscountBL = $comlistApp->getAccounceBalance($DiscountId, $project_id);
                $DiscountBalance = ($DiscountBL - $TotalDiscount);
                $comlistApp->saveAccJournal($voucher_no, $DiscountId, "Purchase", "Purchase discount", $project_id, $description, 0, $TotalDiscount, $DiscountBalance, 0, $purchase_date, 0, $supplier_purchase_type);
            }
        }//End TotalDiscount

        $totalVatAmount = getRequest('vat_amount');
        $vatHeadID = getRequest('vat_type');
        if ($totalVatAmount > 0 && $vatHeadID != "") {
            $totalPartyCR = $comlistApp->getTotalCreditAmount($vatHeadID, $project_id);
            $totalPartyDR = $comlistApp->getTotalDebitAmount($vatHeadID, $project_id);
            $balance = (($totalPartyDR + $totalVatAmount) - $totalPartyCR);
            $transaction_type = "VAT on purchase";
            $description = "";
            $comlistApp->saveAccJournal($voucher_no, $vatHeadID, "Purchase", $transaction_type, $project_id, $description, $totalVatAmount, 0, $balance, 0, $purchase_date, 0, $supplier_purchase_type);
        }
        $totalATAmount = getRequest('AT_amount');
        $ATHeadID = getRequest('AT_type');
        if ($totalATAmount > 0 && $ATHeadID != "") {
            $totalPartyCR = $comlistApp->getTotalCreditAmount($ATHeadID, $project_id);
            $totalPartyDR = $comlistApp->getTotalDebitAmount($ATHeadID, $project_id);
            $balance = (($totalPartyDR + $totalATAmount) - $totalPartyCR);
            $transaction_type = "Advance Tax";
            $description = "";
            $comlistApp->saveAccJournal($voucher_no, $ATHeadID, "Purchase", $transaction_type, $project_id, $description, $totalATAmount, 0, $balance, 0, $purchase_date, 0, $supplier_purchase_type);
        }

        $requestdata['inventory_type'] = getRequest('inventory_type');
        $requestdata['inventory_id'] = getRequest('inventory_id');

        $requestdata['vat_percentage'] = getRequest('vat_percent');
        $requestdata['vat_amount'] = getRequest('vat_amount');
        $requestdata['at_percentage'] = getRequest('AT_percent');
        $requestdata['at_amount'] = getRequest('AT_amount');
        $requestdata['grn_voucher'] = $grn_voucher;
        $requestdata['warranty_note'] = getRequest('warranty_note');

        $requestdata['previour_balance'] = $PreviousPartyBalance;
        $requestdata['project_id'] = getFromSession('project_id');
        $requestdata['created_by'] = getFromSession('userid');
        $requestdata['created_date'] = date('Y-m-d h:i:s');
        $info = array();
        $info['table'] = PURCHASE_MASTER_TBL;
        $info['data'] = $requestdata;
        //$info['debug']  	=  true;
        $res = insert($info);

        if (!$res['affected_rows']) {
            mysql_query("ROLLBACK;");
            header("location:index.php?app=purchase.item.grn&cmd=add");
            exit;
        }
    }

    function createGRNVoucharID()
    {
        $info = array();
        $info['table'] = PURCHASE_MASTER_TBL;
        $info['fields'] = array('max(grn_voucher) as maxvoucher');
        $res = select($info);
        $maxvoucherId = 'GRN000000';
        if (count($res)) {
            foreach ($res as $v) {
                if ($v->maxvoucher) {
                    $maxvoucherId = $v->maxvoucher;
                }
                break;
            }
        }
        $maxvoucherId = generateID("GRN", $maxvoucherId, 9);
        return $maxvoucherId;
    }


    function getGRNVatAmount()
    {
        $getSql = "SELECT * FROM " . TEMP_GRN_TBL . " WHERE created_by = '" . getFromSession('userid') . "' AND project_id='" . getFromSession('project_id') . "'";
        $gres = mysql_query($getSql);

        $totalVatAmount = 0;
        $totalPriceAmount = 0;

        $total_vat_percent = 0;
        $total_vat_amount = 0;
        $total_vat_type = "";
        if (mysql_num_rows($gres) > 0) {
            while ($row = mysql_fetch_object($gres)) {
                $totalPrice = (float)$row->unit_price * (float)$row->qty;
                $totalPriceAmount += $totalPrice;
//                $vatPercent = (float)$row->vat_percent;
//                if ($vatPercent > 0) {
//                    $vatAmount = (($vatPercent / 100) * $totalPrice);
//                    $totalVatAmount += $vatAmount;
//                }

                if ($total_vat_percent <= 0) {
                    $total_vat_percent = (float)$row->total_vat_percent;
                }
                if ($total_vat_amount <= 0) {
                    $total_vat_amount = (float)$row->total_vat_amount;
                }
                if (empty($total_vat_type)) {
                    $total_vat_type = $row->total_vat_type;
                }

            }

            $totalVatAmount = (float)$total_vat_amount;
            if ($total_vat_type == "percent" && $total_vat_percent > 0 && $totalPriceAmount > 0) {
                $totalVatAmount = (($total_vat_percent / 100) * $totalPriceAmount);
            } elseif ($total_vat_type == "fixed") {
                $totalVatAmount = $total_vat_amount;
            }
        }

        //return (float)$totalVatAmount;
        return 0;
    }

    function savePurchaseItem()
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $project_id = getFromSession('project_id');

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION;");
        $store_id = getRequest('store_id');

        if ($store_id != "") {
            $grn_voucher = $this->createGRNVoucharID();
            $voucher_no = $this->saveDebitVouchar();

            $supplier_type = getRequest('supplierPurchaseType');
            if (empty($supplier_type)) {
                $supplier = getRequest('supplier');
                $supplier_type = $comListApp->getSupplierType($supplier);
            }

            $PartyAcc_head = getRequest('supplier');
            $totalPartyCR = $comListApp->getTotalCreditAmount($PartyAcc_head, $project_id);
            $totalPartyDR = $comListApp->getTotalDebitAmount($PartyAcc_head, $project_id);
            $PreviousPartyBalance = ($totalPartyDR - $totalPartyCR);

//            if ($supplier_type == 'local') {
            $PreviousPartyBalance = $this->saveCreditVouchar($voucher_no, $grn_voucher);
//            }

            $this->insertPurchaseMaster($voucher_no, $PreviousPartyBalance, $grn_voucher);
            $this->insertPurchaseDetails($voucher_no);
        }


        if ($voucher_no != "") {
            mysql_query("COMMIT;");
            header("location:index.php?app=purchase.item.grn&cmd=print_vouchar&voucher_no=" . $voucher_no);
        } else {
            mysql_query("ROLLBACK;");
            header("location:index.php?app=purchase.item.grn&cmd=add");
        }
    }

    function saveAVGPurchasePrice($voucher_no, $project_id, $product_id, $purchase_price)
    {
        $sql = "INSERT INTO " . AVG_PURCHASE_PRICE_TBL . "(voucher_no,project_id,product_id,purchase_price) 
		VALUES('" . $voucher_no . "','" . $project_id . "','" . $product_id . "','" . $purchase_price . "')";
        $ires = mysql_query($sql);
        $avg_purchase_price = 0;
        if ($ires) {
            $Prosql = "SELECT purchase_price  FROM " . AVG_PURCHASE_PRICE_TBL . " WHERE product_id = '$product_id' AND project_id = '$project_id' ORDER BY `id` DESC LIMIT 0 , 2";
            $pres = mysql_query($Prosql);
            $ttl_product = mysql_num_rows($pres);
            if ($ttl_product > 0) {
                while ($prow = mysql_fetch_object($pres)) {
                    $avg_purchase_price += $prow->purchase_price;
                }
                $avg_purchase_price = ($avg_purchase_price / $ttl_product);
            }
            if (intval($avg_purchase_price) == "") {
                $avg_purchase_price = 0;
            }

            if ($avg_purchase_price == 0) {
                $avg_purchase_price = $purchase_price;
            }
            $USQL = "UPDATE " . PRODUCT_TBL . " SET purchase_unit_price = $avg_purchase_price WHERE product_id = '$product_id' AND project_id = '$project_id'";
            //mysql_query($USQL);
        }
    }

    function saveAccountJournal($voucher_no, $sub_id, $head_type, $project_id, $description, $DR = NULL, $CR = NULL, $balance, $status = NULL, $purchare_date, $cost_center = "", $grn_voucher = "", $supplier_purchase_type = null)
    {
        $created_by = getFromSession('userid');
        $head_type = getHeadType($sub_id);
        $transaction_type = "Purchase";
        $sql = "INSERT INTO " . ACCOUNT_JOURNAL_TBL . " 
	(voucher_no,created_date,sub_id,head_type,transaction_type,project_id,description,dr,cr,balance,status,created_by,cost_center,grn_voucher,supplier_purchase_type) 
	VALUES('" . $voucher_no . "','" . $purchare_date . "','" . $sub_id . "','" . $head_type . "','" . $transaction_type . "','" . $project_id . "','" . $description . "','" .
            $DR . "','" . $CR . "','" . $balance . "','" . $status . "','" . $created_by . "','" . $cost_center . "','" . $grn_voucher . "','" . $supplier_purchase_type . "')";
        mysql_query($sql);
    }

    function getSalesMasterInfo($id)
    {

        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = SALES_MASTER_TBL . ' pm,' . SUB_ACC_HEAD_TBL . ' s,' . PROJECT_TBL . ' p,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('pm.voucher_no', 'pm.po_no', 'pm.wo_no', 'p.project_name', 'p.location', 's.sub_head_name', 's.head_details', 's.phone', 's.mobile', 's.email', 's.att_name1', 's.att_designation1', 's.att_mobile1', 'pm.reference', 'pm.gate_pass', 'pm.track_no', 'pm.salse_type', 'pm.total_value', "DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date", "DATE_FORMAT(pm.delivery_date,'%d %b %y' ) as delivery_date", 'pm.mode_of_payment', 'c.curr_symble', 'pm.bank_name', 'pm.acc_no', 'pm.check_no', 'pm.check_no', 'pm.general_discount_percent', 'pm.general_discount_amount', 'pm.exclusive_discount_percent', 'pm.exclusive_discount_amount', 'pm.additional_discount', 'pm.product_discount', 'pm.discount', 'pm.service_charge', 'pm.net_payble', 'pm.item_delivery_amount', 'pm.paid_amount', 'pm.due', 'pm.ref_no', 'pm.created_date');

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

    function getPurchaseMasterInfo($id)
    {

        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = PURCHASE_MASTER_TBL . ' pm,' . SUB_ACC_HEAD_TBL . ' s,' . PROJECT_TBL . ' p,' . CURRENCY_TBL . ' c';
        $info['fields'] = array('pm.voucher_no', 'pm.po_no', 'p.project_name', 'p.location', 'p.project_logo', 'pm.supplier', 's.sub_head_name as name', 's.head_details as address', 's.att_name1 as contact_person', 's.att_designation1 as designation', 'pm.quotation_no', 'pm.lc_no', 'pm.lcopener', 'pm.lcopening_bank', "DATE_FORMAT(pm.lcopening_date,'%d %b %y' ) as lcopening_date", 'pm.country', 'pm.lc_details', 'pm.store_id', 'pm.inventory_type', 'pm.inventory_id', 'pm.store_id as delivery_point', 'pm.quotation_no', 'pm.track_no', 'pm.van_no', 'pm.total_value', "DATE_FORMAT(pm.purchase_date,'%d %b %y' ) as purchase_date", 'pm.purchase_date as purchasedate', 'pm.mode_of_payment', 'c.curr_symble', 'pm.bank_name', 'pm.acc_no', 'pm.check_no', 'pm.check_no', 'pm.additional_discount', 'pm.product_discount', 'pm.general_discount_percent', 'pm.general_discount_amount', 'pm.exclusive_discount_percent', 'pm.exclusive_discount_amount', 'pm.discount', 'pm.net_payble', 'pm.paid_amount', 'pm.previour_balance', 'pm.due', 'pm.ref_no', 'pm.delivery_note', 'pm.payment_note', 'pm.warranty_note', 'pm.created_date', 'pm.cost_center', 'pm.vat_percentage', 'pm.vat_amount', 'pm.at_percentage', 'pm.at_amount', 'pm.grn_voucher', 'pm.created_by');

        $sql = "pm.supplier=s.sub_id AND pm.project_id=p.project_id AND pm.currency=c.currency_id AND pm.project_id='" . $project_id . "' AND pm.voucher_no='$id'";
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
        $info['fields'] = array('sd.sal_detail_id', 'sd.voucher_no', 'sd.project_id', 'sd.serial', 'sd.warranty', 'sd.catagory', 'b.brand_name', 'sd.product', 'sd.details', 'p.product_name', 'p.product_desc', 'sd.m_unit', 'sd.unit_price', 'c.curr_symble', 'sd.discount_per_qty', 'sd.qty', 'sd.delivery_qty', 'sd.total_bag', 'sd.total', 'sd.created_time');

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

    function getGRNProductList($id)
    {

        $info = array();
        $info['table'] = PURCHASE_DETAILS_TBL . ' pd,' . PRODUCT_TBL . ' p,' . CURRENCY_TBL . ' c,' . BRAND_TBL . ' b';
        $info['fields'] = array('pd.pur_detail_id', 'pd.voucher_no', 'pd.project_id', 'pd.details', 'pd.serial', 'pd.warranty', 'pd.catagory', 'b.brand_name', 'pd.product', 'p.product_name', 'p.product_code', 'p.product_desc', 'pd.m_unit', 'pd.unit_price', 'pd.discount_per_qty', 'pd.discount_amount', 'c.curr_symble', 'SUM(pd.qty) as qty', 'SUM(pd.free_qty) as free_qty', 'SUM(pd.rec_qty) AS rec_qty', 'pd.total_bag', 'SUM(pd.total) AS total', 'pd.created_time');

        $sql = "pd.product = p.product_id AND p.brand_code = b.brand_id AND pd.currency = c.currency_id AND pd.voucher_no = '$id'";

        $info['where'] = $sql;
        $info['groupby'] = array("pd.voucher_no,pd.product");
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

    function loadProductDtl($product_id)
    {
        $project_id = getFromSession('project_id');
        $store_id = getRequest('store_id');
        $info = array();
        $info['table'] = PRODUCT_TBL . " p," . CATAGORY_TBL . ' c,' . BRAND_TBL . ' b';
        $info['fields'] = array('p.m_unit', 'p.product_desc', 'p.purchase_unit_price', 'p.product_catagory', 'p.catagory', 'c.catagory_name', 'p.brand_code', 'b.brand_name');
        $info['where'] = "p.catagory =c.catagory_code AND p.brand_code =b.brand_id AND p.product_id = '$product_id'  AND p.project_id = '$project_id'";
        $info['groupby'] = array("p.product_id");
        //$info['debug']   = true;
        $result = select($info);
        $data = array();

        $sql = "SELECT (sum(`dr`) - sum(`cr`)) as balance_qty FROM stock_ledger WHERE product_id = '$product_id' AND project_id = '$project_id'";
        if ($store_id != "") {
            $sql .= " AND store_id ='$store_id'";
        }
        $row = mysql_fetch_object(mysql_query($sql));
        $balance_qty = $row->balance_qty;
        if (empty($balance_qty)) {
            $balance_qty = 0;
        }

        if (count($result)) {
            foreach ($result as $key => $value) {
                $data[$key][] = $value;
            }
        }

        foreach ($data as $i => $v) {
            $response = [
                "m_unit" => $v[0]->m_unit,
                "details" => $v[0]->product_desc,
                "unit_price" => $v[0]->purchase_unit_price,
                "catagory" => $v[0]->catagory,
                "catagory_name" => $v[0]->catagory_name,
                "brand_code" => $v[0]->brand_code,
                "brand_name" => $v[0]->brand_name,
            ];
        }


        $psql = "SELECT pm.purchase_date as last_date, SUM(qty) as last_qty  FROM purchase_details AS pd JOIN purchase_master AS pm ON pd.voucher_no = pm.voucher_no WHERE pm.purchase_date = ( SELECT MAX(purchase_date) FROM purchase_master JOIN purchase_details ON purchase_master.voucher_no = purchase_details.voucher_no WHERE purchase_master.voucher_no LIKE 'PI%' AND purchase_details.product = '$product_id') AND pd.product = '$product_id' GROUP BY pd.product";

        $pResult = $sprResult = mysql_fetch_object(mysql_query($psql));
        $lastPurchaseDate = isset($pResult->last_date) ? $pResult->last_date : "";

        $response['balance_qty'] = $balance_qty;
        $response['lastPurchaseDate'] = $lastPurchaseDate;
        $response['product_id'] = $product_id;

        echo json_encode($response);
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


    function getCustomerList()
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = SUB_ACC_HEAD_TBL;
        $info['where'] = "head_type = 'Customer' AND project_id='" . $project_id . "'";
        $res = select($info);

        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        //dumpVar($data);
        return $data;
    }

    function getRecievableId($project_id)
    {
        $sql = "SELECT sub_id FROM " . SUB_ACC_HEAD_TBL . " WHERE head_type ='Current Assets' AND `sub_headtype` = 'S128' AND child_head='C000104' AND sub_head_name LIKE 'Account Recievable' AND project_id = '$project_id'";
        $row = mysql_fetch_object(mysql_query($sql));
        return $sub_id = $row->sub_id;
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

    function getAccounceBalance($account_id, $project_id)
    {
        $sql = "SELECT (sum(`dr`) - sum(`cr`)) as balance_amount FROM " . ACCOUNT_JOURNAL_TBL . " WHERE product_id = '$product_id' AND project_id = '$project_id'";

        $row = mysql_fetch_object(mysql_query($sql));
        $balance_amount = $row->balance_amount;
        if (empty($balance_amount)) {
            $balance_amount = 0;
        }
        return $balance_amount;
    }

    function saveStockJournal($voucher_no, $project_id, $store_id, $product_id, $product_type, $serial = NULL, $warranty = NULL, $unit_price = NULL, $m_unit, $DR = NULL, $CR = NULL, $balance, $create_date = NULL, $supplier_purchase_type)
    {
        $created_by = getFromSession('userid');
        $sql = "INSERT INTO " . STOCK_LEDGER_TBL . " (voucher_no,project_id,store_id,product_id,product_type,serial,warranty,note,unit_price,m_unit,dr,cr,balance,create_date,created_by,supplier_purchase_type) VALUES('" . $voucher_no . "','" . $project_id . "','" . $store_id . "','" . $product_id . "','" . $product_type . "','" . $serial . "','" . $warranty . "','Purchase Item','" . $unit_price . "','" . $m_unit . "','" . $DR . "','" . $CR . "','" . $balance . "','" . $create_date . "','" . $created_by . "','" . $supplier_purchase_type . "')";
        mysql_query($sql);
    }

    function createVoucharID()
    {
        $info = array();
        $info['table'] = DEVIT_VOUCHAR_TBL;
        $info['fields'] = array('max(voucher_no) as maxvoucher');
        $info['where'] = "voucher_no LIKE '%PI%'";
        $res = select($info);
        $maxvoucherId = 'PI0000000';
        if (count($res)) {
            foreach ($res as $v) {
                if ($v->maxvoucher) {
                    $maxvoucherId = $v->maxvoucher;
                }
                break;
            }
        }
        $maxvoucherId = generateID("PI", $maxvoucherId, 9);
        return $maxvoucherId;
    }

    function savePayableCheck($voucher_no, $pvoucher_no, $transaction_type, $paid_amount)
    {
        $requestdata = array();
        $requestdata = getUserDataSet(PAYABLE_CHECK_TBL);
        $requestdata['check_no'] = getRequest('check_no');
        $requestdata['check_issue_date'] = formatDate(getRequest('check_issue_date'));
        $requestdata['created_date'] = formatDate(getRequest('purchase_date'));
        $requestdata['acc_head'] = getRequest('supplier');
        $requestdata['head_type'] = "Check";
        $requestdata['voucher_no'] = $voucher_no;
        $requestdata['pvoucher_no'] = $pvoucher_no;
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
