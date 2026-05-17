<?php

class GroupStockTransfer
{

    function run()
    {
        $cmd = getRequest('cmd');
        $u_t_id = getFromSession('u_type_id');

        if (($u_t_id == 101) || ($u_t_id == 102) || ($u_t_id == 103) || ($u_t_id == 105)) // 101 = sysadmin, 102 = admin, 103= salesman, 105=store
        {

            switch ($cmd) {
                case 'add'            :
                    $this->showEditor();
                    break;
                case 'edit'            :
                    $this->showEditEditor();
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
                    $this->saveTempSales();
                    break;
                case 'deltemp'        :
                    $this->delTempSales();
                    break;
                case 'save_transfer'        :
                    $this->saveSalesItem();
                    break;
                case 'convert_transfer'    :
                    $this->saveConvertSalesItem();
                    break;
                case 'approved_transfer'    :
                    $this->approvedTransferRecord("Report Page");
                    break;
                case 'print_challan'        :
                    $this->showPrintEditor($msg);
                    break;
                case 'pending_print_challan'        :
                    $this->showPendingPrintEditor($msg);
                    break;
                case 'print_convert_challan'    :
                    $screen = $this->showPrintConvertChallan($msg);
                    break;
                case 'pending_print_convert_challan'    :
                    $screen = $this->showPendingPrintConvertChallan($msg);
                    break;
                case 'delete'                :
                    $this->DeleteStockTransfer(getRequest('id'));
                    break;
                case 'delete_pending'                :
                    $this->DeletePendingStockTransfer(getRequest('id'));
                    break;
                case 'load_stock'            :
                    $this->loadProductStock(trim(getRequest('product_id')));
                    break;
                case 'loadstockqty'    :
                    $this->loadProductStockQty(trim(getRequest('product_id')));
                    break;
                case 'missing_transfer'    :
                    $this->MismachTransfer();
                    break;
                case 'pending_edit'        :
                    $this->showPendingEditEditor();
                    break;
                case 'pending_save_tmp'    :
                    $this->savePendingTempSales();
                    break;
                case 'pending_deltemp'     :
                    $this->pendingDelTempSales();
                    break;
                case 'pending_update_transfer'         :
                case 'pending_update_convert_transfer' :
                    $this->updatePendingFromTemp();
                    break;
                default                    :
                    $cmd = 'list';
                    $screen = $this->showEditor();
                    break;
            }
        } elseif ($u_t_id == 104) // 104 = acc
        {
            switch ($cmd) {
                case 'sal_dtl'    :
                    $this->showEditor4SalesDetails();
                    break;
                case 'print_challan'    :
                    $screen = $this->showPrintEditor($msg);
                    break;
                case 'print_convert_challan'    :
                    $screen = $this->showPrintConvertChallan($msg);
                    break;
                default              :
                    $cmd = 'list';
                    $screen = $this->showEditor();
                    break;
            }

        } elseif ($u_t_id == 107) // 104 = acc
        {
            switch ($cmd) {
                case 'delete'        :
                    $this->DeleteStockTransfer(getRequest('id'));
                    break;
                default              :
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

    //======= Start Edit Transfer Order =======
    function showEditEditor($msg = null)
    {
        $transfer_no = getRequest('transfer_no');
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        if (getRequest('submit')) {
            $product_convert = getRequest('product_convert');
            if (isset($product_convert) && $product_convert == 1) {
                $this->updateConvertProductStockTransfer();
            } else {
                $this->updateStockTransfer();
            }

        }
        if (getRequest('tid') > 0) {
            $this->deleteItem(getRequest('tid'));
        }
        if ($transfer_no) {
            $advArr = $this->getTransferMasterInfo($transfer_no);
            $advArr = parseThisValue($advArr);
            $data = array_merge(array(), $advArr);

            if ($data['product_convert']) {
                $data['item_list'] = $this->getConvertProductList($transfer_no);
            } else {
                $data['item_list'] = $this->getProductList($transfer_no);
            }

            $data['brand_list'] = $comListApp->getBrandList();
            $data['product_list'] = $comListApp->getProductList();
            $data['user_depo_list'] = $comListApp->getDeliveryPointList(true);
            $data['depo_list'] = $comListApp->getDeliveryPointList();

            $data['message'] = $msg;
            $data['cmd'] = getRequest('cmd');
            require_once(EDIT_STOCK_TRANSFER_SKIN);
            return true;
        }
    }

    //======= Start Edit Pending Transfer Order =======

    function showPendingEditEditor($msg = null)
    {
        $transfer_id = getRequest('transfer_id');
        if (!$transfer_id) {
            return;
        }

        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();

        // Pre-populate temp table with existing pending transfer items
        $this->insertPendingDetailsIntoTemp($transfer_id);

        // Load master record info
        $advArr = $this->getPendingTransferMasterInfo($transfer_id);
        $advArr = parseThisValue($advArr);
        $data = array_merge(array(), $advArr);

        // Use raw Y-m-d date so dateInputFormatDMY() works correctly in the edit skin
        $data['transfer_date'] = $advArr['transfer_date_raw'];

        // Build $data matching what the add form skin expects
        $data['product_list'] = $comListApp->getProductList();
        $data['brand_list'] = $comListApp->getBrandList();
        $data['currency_list'] = $this->getCurrencyList();
        $data['user_depo_list'] = $comListApp->getDeliveryPointList(true);
        $data['depo_list'] = $comListApp->getDeliveryPointList();
        $data['tmp_sales'] = $this->getTempSalesForPendingEdit($transfer_id);
        $data['wip_store_list'] = array();
        $data['edit_transfer_id'] = $transfer_id;
        $data['cmd'] = getRequest('cmd');
        $data['message'] = $msg;

        require_once(EDIT_PENDING_STOCK_TRANSFER_SKIN);
        return true;
    }

    // Pre-populate temp table from existing pending transfer items
    function insertPendingDetailsIntoTemp($transfer_id)
    {
        $project_id = getFromSession('project_id');
        $userid = getFromSession('userid');

        // Clear any existing temp data for this user first
        $dsql = "DELETE FROM " . TEMP_STOCK_TRANSFER_TBL . "
                 WHERE created_by = '$userid' AND project_id = '$project_id'";
        mysql_query($dsql);

        // Load pending details with all fields the temp table needs
        $getSql = "SELECT
                     sd.product        AS productid,
                     p.product_name,
                     sd.new_product_id,
                     sd.catagory,
                     cat.catagory_name AS catagoryname,
                     sd.brand_id,
                     b.brand_name      AS brandname,
                     sd.m_unit         AS munit,
                     sd.qty,
                     sd.unit_price,
                     sd.total,
                     sd.currency,
                     c.currency_name   AS currencyName,
                     sd.transfer_from,
                     sd.delivery_point,
                     sd.transfer_date
                   FROM " . PENDING_STOCK_TRANSFER_DETAILS_TBL . " sd
                   LEFT JOIN " . PRODUCT_TBL . "  p   ON p.product_id       = sd.product
                   LEFT JOIN " . CATAGORY_TBL . " cat ON cat.catagory_code  = sd.catagory
                   LEFT JOIN " . BRAND_TBL . "    b   ON b.brand_id         = sd.brand_id
                   LEFT JOIN " . CURRENCY_TBL . " c   ON c.currency_id      = sd.currency
                   WHERE sd.transfer_id = '$transfer_id' AND sd.project_id = '$project_id'";

        $gres = mysql_query($getSql);
        while ($row = mysql_fetch_object($gres)) {
            $product_name = mysql_real_escape_string($row->product_name);
            $catagoryname = mysql_real_escape_string($row->catagoryname);
            $brandname = mysql_real_escape_string($row->brandname);
            $currencyName = mysql_real_escape_string($row->currencyName);
            $new_prod = !empty($row->new_product_id) ? "'" . $row->new_product_id . "'" : "NULL";

            $isql = "INSERT INTO " . TEMP_STOCK_TRANSFER_TBL . "
                     (project_id, transfer_from, delivery_point, transfer_date, currency,
                      currencyName, productid, new_product_id, product_name, catagory,
                      catagoryname, brand_id, brandname, munit, qty, unit_price, total, created_by)
                     VALUES ('$project_id',
                             '" . $row->transfer_from . "',
                             '" . $row->delivery_point . "',
                             '" . $row->transfer_date . "',
                             '" . $row->currency . "',
                             '$currencyName',
                             '" . $row->productid . "',
                             $new_prod,
                             '$product_name',
                             '" . $row->catagory . "',
                             '$catagoryname',
                             '" . $row->brand_id . "',
                             '$brandname',
                             '" . $row->munit . "',
                             '" . $row->qty . "',
                             '" . $row->unit_price . "',
                             '" . $row->total . "',
                             '$userid')";
            mysql_query($isql);
        }
    }

    // Same as getTempSales() but delete links redirect back to the edit page
    function getTempSalesForPendingEdit($transfer_id)
    {
        $project_id = getFromSession('project_id');
        $total_value = 0;
        $TotalQty = 0;
        $sl = 1;
        $str2 = '';
        $productConvert = false;
        $munit = '';
        $currencyName = '';

        $getSql = "SELECT t.*, p.product_name AS new_product_name
                   FROM " . TEMP_STOCK_TRANSFER_TBL . " t
                   LEFT JOIN product p ON p.product_id = t.new_product_id
                   WHERE t.created_by = '" . getFromSession('userid') . "'
                   AND t.project_id = '$project_id'";

        $gres = mysql_query($getSql);
        while ($row = mysql_fetch_array($gres)) {
            extract($row);
            $new_product_id = isset($new_product_id) ? $new_product_id : '';
            if (!empty($new_product_id)) {
                $productConvert = true;
            }
            $total_value += $total;
            $TotalQty += $qty;

            // Build JS call to pre-fill the product form fields for editing
            $js_new_prod = addslashes($new_product_id);
            $js_munit = addslashes($munit);
            $edit_onclick = "editTempRow('{$tmp_id}','{$productid}','{$js_new_prod}','{$qty}','{$unit_price}','{$total}','{$js_munit}')";

            $str2 .= "<tr style='color:#000000' bgcolor='#fff'>
                        <td width='2%' nowrap align='left'>$sl</td>
                        <td width='20%' nowrap align='left'>$product_name</td>";

            if ($productConvert) {
                $str2 .= "<td width='20%' nowrap align='left'>$new_product_name</td>";
            }

            $str2 .= "<td width='10%' nowrap align='left'>$catagoryname</td>
                      <td width='10%' nowrap align='left'>$brandname</td>
                      <td width='10%' nowrap><div align='right'>$qty $munit</div></td>
                      <td width='10%' nowrap align='right'>$unit_price $currencyName</td>
                      <td width='10%' nowrap align='right'>$total</td>
                      <td width='8%' nowrap align='center'><div class='table-option'>
                            <a href=\"javascript:$edit_onclick\" title=\"Edit\">
                              <img src=\"images/common/icons/edit.gif\" width='16' border='0'>
                            </a>&nbsp;
                            <a href=\"?app=groupwise.stock.transfer&cmd=pending_deltemp&transfer_id=$transfer_id&id=$tmp_id\" title=\"Delete\">
                              <img src=\"images/common/icons/delete.gif\">
                            </a>
                        </div>
                      </td>
                    </tr>";
            $sl++;
        }

        $str1 = "<table width='100%' align='center' bgcolor='#99CC66'>
                   <tr style='color:#fff;' bgcolor='#00B000' height=28>
                     <td width='2%' nowrap><div align='left'>SL</div></td>
                     <td width='20%' nowrap><div align='left'>Product Name</div></td>";

        if ($productConvert) {
            $str1 .= "<td width='20%' nowrap><div align='left'>New Product Name</div></td>";
        }

        $str1 .= "<td width='10%' nowrap><div align='left'>Catagory</div></td>
                  <td width='10%' nowrap><div align='left'>Brand</div></td>
                  <td width='10%' nowrap><div align='right'>Transfer Qty</div></td>
                  <td width='10%' nowrap><div align='right'>Rate</div></td>
                  <td width='10%' nowrap><div align='right'>Amount</div></td>
                  <td width='8%' nowrap align='center'>Option</td>
                 </tr>";

        $str3 = "<tr style='color:#000;' bgcolor='#CCCCCC' height=25>";
        if ($productConvert) {
            $str3 .= "<td nowrap>&nbsp;</td>";
        }
        $str3 .= "<td colspan='4' nowrap><div align='right'>Total</div></td>
                  <td nowrap align='right'>$TotalQty $munit</td>
                  <td nowrap>&nbsp;</td>
                  <td nowrap align='right'>$total_value $currencyName</td>
                  <td nowrap align='center'>&nbsp;</td>
                 </tr>
                </table>";

        $total_salesStr = $str1 . $str2 . $str3
            . "####-@@@@" . $total_value
            . "####-@@@@" . 0
            . "####-@@@@" . $productConvert;

        return $total_salesStr;
    }

    function pendingDelTempSales()
    {
        $tmp_id = getRequest('id');
        $transfer_id = getRequest('transfer_id');
        if ($tmp_id != "") {
            $dsql = "DELETE FROM " . TEMP_STOCK_TRANSFER_TBL . " WHERE tmp_id = '$tmp_id'";
            mysql_query($dsql);
        }
        header("location:?app=groupwise.stock.transfer&cmd=pending_edit&transfer_id=" . $transfer_id);
        exit;
    }

    // AJAX handler: INSERT a new temp row or UPDATE an existing one, then return the refreshed item list
    function savePendingTempSales()
    {
        $transfer_id = getRequest('transfer_id');
        $tmp_id = getRequest('tmp_id');
        $project_id = getFromSession('project_id');
        $userid = getFromSession('userid');
        $transfer_from = getRequest('transfer_from');
        $delivery_point = getRequest('delivery_point');
        $transfer_date = formatDate(getRequest('transfer_date'));
        $currency = getRequest('currency');
        $currencyName = mysql_real_escape_string(getRequest('currencyName'));
        $productid = getRequest('productid');
        $new_product_id = getRequest('new_product_id');
        $qty = getRequest('qty');
        $unit_price = getRequest('unit_price');
        $total = getRequest('total');

        // Fetch product details needed for display
        $pSql = "SELECT p.product_name, p.m_unit, p.catagory,
                        cat.catagory_name, p.brand_code, b.brand_name
                 FROM " . PRODUCT_TBL . " p
                 LEFT JOIN " . CATAGORY_TBL . " cat ON cat.catagory_code = p.catagory
                 LEFT JOIN " . BRAND_TBL . "   b   ON b.brand_id = p.brand_code
                 WHERE p.product_id = '$productid' AND p.project_id = '$project_id'";
        $pRow = mysql_fetch_object(mysql_query($pSql));

        $product_name = $pRow ? mysql_real_escape_string($pRow->product_name) : '';
        $munit = $pRow ? $pRow->m_unit : '';
        $catagory = $pRow ? $pRow->catagory : '';
        $catagoryname = $pRow ? mysql_real_escape_string($pRow->catagory_name) : '';
        $brand_id = $pRow ? $pRow->brand_code : '';
        $brandname = $pRow ? mysql_real_escape_string($pRow->brand_name) : '';
        $new_prod_val = !empty($new_product_id) ? "'" . mysql_real_escape_string($new_product_id) . "'" : "NULL";

        if (!empty($tmp_id)) {
            // UPDATE existing temp row
            $usql = "UPDATE " . TEMP_STOCK_TRANSFER_TBL . "
                     SET transfer_from  = '$transfer_from',
                         delivery_point = '$delivery_point',
                         transfer_date  = '$transfer_date',
                         currency       = '$currency',
                         currencyName   = '$currencyName',
                         productid      = '$productid',
                         new_product_id = $new_prod_val,
                         product_name   = '$product_name',
                         catagory       = '$catagory',
                         catagoryname   = '$catagoryname',
                         brand_id       = '$brand_id',
                         brandname      = '$brandname',
                         munit          = '$munit',
                         qty            = '$qty',
                         unit_price     = '$unit_price',
                         total          = '$total'
                     WHERE tmp_id    = '$tmp_id'
                       AND created_by = '$userid'
                       AND project_id = '$project_id'";
            mysql_query($usql);
        } else {
            // INSERT new temp row
            $isql = "INSERT INTO " . TEMP_STOCK_TRANSFER_TBL . "
                     (project_id, transfer_from, delivery_point, transfer_date, currency,
                      currencyName, productid, new_product_id, product_name, catagory,
                      catagoryname, brand_id, brandname, munit, qty, unit_price, total, created_by)
                     VALUES ('$project_id', '$transfer_from', '$delivery_point', '$transfer_date',
                             '$currency', '$currencyName', '$productid', $new_prod_val,
                             '$product_name', '$catagory', '$catagoryname', '$brand_id',
                             '$brandname', '$munit', '$qty', '$unit_price', '$total', '$userid')";
            mysql_query($isql);
        }

        // Discard any framework HTML already buffered so the AJAX caller
        // receives only the delimited string, nothing else.
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: text/plain; charset=utf-8');
        echo $this->getTempSalesForPendingEdit($transfer_id);
        exit;
    }

    function updatePendingFromTemp()
    {
        $transfer_id = getRequest('transfer_id');
        $project_id = getFromSession('project_id');
        $userid = getFromSession('userid');
        $transfer_from = getRequest('transfer_from');
        $delivery_point = getRequest('delivery_point');
        $transfer_date = formatDate(getRequest('transfer_date'));
        $narration = trim(htmlspecialchars(getRequest('narration'), ENT_QUOTES, 'UTF-8'));
        $total_amount = getRequest('total_amount');
        $job_name = getRequest('job_name');
        $finish_item = getRequest('finish_item');
        $currency_raw = getRequest('currency');       // "1###TK" format from select
        $currencyArr = explode("###", $currency_raw);
        $currency = $currencyArr[0];
        $is_convert = (getRequest('cmd') == 'pending_update_convert_transfer') ? 1 : 0;

        if ($transfer_id == "") {
            return false;
        }

        // Validate: convert transfers must all have new_product_id
        if ($is_convert) {
            $checkSql = "SELECT 1 FROM " . TEMP_STOCK_TRANSFER_TBL . "
                         WHERE (new_product_id IS NULL OR new_product_id = '')
                         AND created_by = '$userid' AND project_id = '$project_id' LIMIT 1";
            if (mysql_num_rows(mysql_query($checkSql)) > 0) {
                header("location:index.php?app=groupwise.stock.transfer&cmd=pending_edit&transfer_id=$transfer_id&error_msg=Convert+items+missing+new+product");
                exit;
            }
        }

        // Update master record
        $set_parts = "transfer_from   = '$transfer_from',
                      delivery_point  = '$delivery_point',
                      narration       = '$narration',
                      total_amount    = '$total_amount',
                      transfer_date   = '$transfer_date',
                      product_convert = '$is_convert'";
        if (!empty($job_name)) {
            $set_parts .= ", job_name    = '" . mysql_real_escape_string($job_name) . "'";
        }
        if (!empty($finish_item)) {
            $set_parts .= ", finish_item = '" . mysql_real_escape_string($finish_item) . "'";
        }

        $usql = "UPDATE " . PENDING_STOCK_TRANSFER_MASTER_TBL . "
                 SET $set_parts
                 WHERE id = '$transfer_id' AND project_id = '$project_id'";
        mysql_query($usql);

        // Delete old detail rows
        $dsql = "DELETE FROM " . PENDING_STOCK_TRANSFER_DETAILS_TBL . "
                 WHERE transfer_id = '$transfer_id' AND project_id = '$project_id'";
        mysql_query($dsql);

        // Re-insert from temp table
        $getSql = "SELECT * FROM " . TEMP_STOCK_TRANSFER_TBL . "
                   WHERE created_by = '$userid' AND project_id = '$project_id'";
        $gres = mysql_query($getSql);
        $created_date = date('Y-m-d h:i:s');

        while ($row = mysql_fetch_object($gres)) {
            $new_prod_val = !empty($row->new_product_id)
                ? "'" . mysql_real_escape_string($row->new_product_id) . "'"
                : "NULL";

            $isql = "INSERT INTO " . PENDING_STOCK_TRANSFER_DETAILS_TBL . "
                     (transfer_id, project_id, transfer_from, delivery_point,
                      catagory, brand_id, product, new_product_id, m_unit,
                      unit_price, qty, total, currency,
                      transfer_date, created_by, created_date)
                     VALUES ('$transfer_id', '$project_id', '$transfer_from', '$delivery_point',
                             '" . $row->catagory . "', '" . $row->brand_id . "',
                             '" . $row->productid . "', $new_prod_val,
                             '" . $row->munit . "', '" . $row->unit_price . "',
                             '" . $row->qty . "', '" . $row->total . "',
                             '$currency', '$transfer_date', '$userid', '$created_date')";
            mysql_query($isql);
        }

        // Clear temp table
        $clearSql = "DELETE FROM " . TEMP_STOCK_TRANSFER_TBL . "
                     WHERE created_by = '$userid' AND project_id = '$project_id'";
        mysql_query($clearSql);

        header("location:index.php?app=sales.report&cmd=pending_transfer_list&msg=Transfer+updated+successfully");
        exit;
    }

    //======= End Edit Pending Transfer Order =======


    function updateConvertProductStockTransfer()
    {
        $transfer_no = getRequest('transfer_no');
        $delivery_point = getRequest('delivery_point');
        $ttlfield = getRequest('ttlfield');
        $transfer_from = getRequest('transfer_from');
        $transfer_date = formatDate(getRequest('transfer_date'));
        $narration = trim(htmlspecialchars(getRequest('narration'), ENT_QUOTES, 'UTF-8'));
        $total_amount = getRequest('total_amount');
        $updated_by = getFromSession('userid');
        $updated_time = date('Y-m-d h:i:s');
        $currency = getRequest('currency');
        $project_id = getFromSession('project_id');
        if ($transfer_no != "") {
            $j = 1;
            for ($j; $j < $ttlfield; $j++) {
                $transfer_id = getRequest("old_id$j");
                $catagory = getRequest("catagory$j");
                $brand_id = getRequest("brand$j");
                $product = getRequest("product$j");
                $new_product_id = getRequest("new_product_id$j");

                $m_unit = getRequest("m_unit$j");
                $unit_price = getRequest("unit_price$j");
                $qty = getRequest("qty$j");
                $total = ($unit_price * $qty);

                $Pcsql = "SELECT catagory,brand_code,m_unit FROM " . PRODUCT_TBL . " WHERE product_id='$product' AND project_id='$project_id'";
                $Pcrow = mysql_fetch_object(mysql_query($Pcsql));
                $m_unit = $Pcrow->m_unit;
                $brand_id = $Pcrow->brand_code;
                $catagory = $Pcrow->catagory;

                if ($transfer_id > 0 && $qty > 0) {
                    $usql = "UPDATE " . STOCK_TRANSFER_DETAILS_TBL . " SET transfer_from='$transfer_from', 	delivery_point='$delivery_point',catagory='$catagory',brand_id='$brand_id',product='$product',new_product_id='$new_product_id',m_unit='$m_unit',unit_price='$unit_price',qty='$qty',total='$total',transfer_date='$transfer_date',created_by='$updated_by',created_date='$updated_time' WHERE transfer_id=$transfer_id AND transfer_no='$transfer_no'";
                    mysql_query($usql);
                    $this->updateConvertProductStockJournal($transfer_no, $transfer_id, $project_id, $transfer_from, $delivery_point, $product, $qty, $new_product_id);
                } elseif ($transfer_id == 0 && $product != "" && $qty > 0) {
                    $isql = "INSERT INTO " . STOCK_TRANSFER_DETAILS_TBL . " (transfer_no,project_id,transfer_from,delivery_point,catagory,brand_id,product,m_unit,unit_price,qty,total,created_by,created_date,new_product_id) VALUES('$transfer_no','$project_id','$transfer_from','$delivery_point','$catagory','$brand_id','$product','$m_unit', '$unit_price','$qty','$total','$updated_by','$updated_time','$new_product_id')";
                    mysql_query($isql);
                    $transfer_id = mysql_insert_id();
                    $this->InsertConvertProductStockJournal($transfer_no, $transfer_id, $project_id, $transfer_from, $delivery_point, $product, $qty, $new_product_id);
                }

            }

            $usql = "UPDATE " . STOCK_TRANSFER_MASTER_TBL . " SET transfer_from='$transfer_from', delivery_point= '$delivery_point',narration='$narration',total_amount='$total_amount',transfer_date='$transfer_date',updated_by='$updated_by',updated_time='$updated_time' WHERE transfer_no ='$transfer_no'";
            $smres = mysql_query($usql);

            if ($smres) {
                $transfer_to = getRequest('delivery_point');
                require_once(CLASS_DIR . '/common.list.class.php');
                $comlistApp = new CommonList();

                $deletesql = "DELETE FROM " . ACCOUNT_JOURNAL_TBL . "  WHERE voucher_no = '$transfer_no' AND project_id='$project_id'";

                mysql_query($deletesql);

                $description = "Out Raw Materials";
                $PurchaseId = $comlistApp->getRMStockId($project_id);
                $fromAccountLedger = $comlistApp->getStoreMapLedgerID($transfer_from, "account_ledger");
                if ($fromAccountLedger) {
                    $PurchaseId = $fromAccountLedger;
                }

                if ($PurchaseId) {
                    $ACPurchaseAmount = $this->getAccounceBalance($PurchaseId, $project_id);
                    $PurchaseBalance = ($ACPurchaseAmount - $total_amount);
                    $this->saveAccountJournal($transfer_no, $PurchaseId, "Inventories", $project_id, $description, 0, $total_amount, $PurchaseBalance, 1, $transfer_date);
                }

                // ======= AC WIP Dr =================
                $description = "Transfer Raw Materials in WIP";
                $StockId = $comlistApp->getWPStockId($project_id);
                $toAccountLedger = $comlistApp->getStoreMapLedgerID($transfer_to, "account_ledger");
                if ($toAccountLedger) {
                    $StockId = $toAccountLedger;
                }

                if ($StockId) {
                    $ACPurchaseAmount = $this->getAccounceBalance($StockId, $project_id);
                    $StockBalance = ($ACPurchaseAmount + $total_amount);
                    $comlistApp->saveAccJournal($transfer_no, $StockId, "Stock", "Transfer Raw Materials", $project_id, $description, $total_amount, 0, $StockBalance, 0, $transfer_date);
                }
                return true;
            } else {
                return false;
            }
        }
    }

    function updateConvertProductStockJournal($voucher_no, $po_no, $project_id, $transfer_from, $transfer_to, $product, $transfer_qty, $new_product_id)
    {
        $Ssql = "SELECT balance FROM " . STORE_STOCK_VIEW . " WHERE product_id = '$product' AND store_id = '$transfer_from' AND project_id = '$project_id'";
        $Srow = mysql_fetch_object(mysql_query($Ssql));
        $stock_qty = $Srow->balance;
        if (($stock_qty > 0) && ($stock_qty >= $transfer_qty)) {
            $Pcsql = "SELECT product_type,m_unit,unit_price FROM " . PRODUCT_TBL . " WHERE product_id='$product' AND project_id='$project_id'";
            $Pcrow = mysql_fetch_object(mysql_query($Pcsql));
            $m_unit = $Pcrow->m_unit;
            $product_type = $Pcrow->product_type;
            $unit_price = $Pcrow->unit_price;

            if ($transfer_from != "" && $po_no > 0) {
                //===== Cr Stock ======
                $totalFDR = $this->getTotalDebitStock($product, $project_id);
                $totalFCR = $this->getTotalCreditStock($product, $project_id);
                $TFbalance = ($totalFDR - ($totalFCR + $transfer_qty));

                $note = "Transfer Stock";
                $sql1 = "UPDATE " . STOCK_LEDGER_TBL . " SET store_id='$transfer_from',product_id='$product', product_type='$product_type',note='$note',unit_price='$unit_price',m_unit='$m_unit',dr=0,cr=$transfer_qty,balance='$TFbalance' WHERE voucher_no='" . $voucher_no . "' AND po_no='" . $po_no . "'";
                mysql_query($sql1);
            }

            if ($transfer_from != "" && $po_no > 0) {
                //===== Dr Stock ======
                $totalFDR = $this->getTotalDebitStock($new_product_id, $project_id);
                $totalFCR = $this->getTotalCreditStock($new_product_id, $project_id);
                $TTbalance = (($totalFDR + $transfer_qty) - $totalFCR);

                $note = "Received Stock";
                $sql2 = "UPDATE " . STOCK_LEDGER_TBL . " SET store_id='$transfer_from',product_id='$new_product_id', product_type='$product_type',note='$note',unit_price='$unit_price',m_unit='$m_unit',dr=$transfer_qty,cr=0,balance='$TTbalance' WHERE voucher_no='" . $voucher_no . "' AND po_no='" . $po_no . "'";
                mysql_query($sql2);
            }
        }
    }

    function InsertConvertProductStockJournal($voucher_no, $po_no, $project_id, $transfer_from, $transfer_to, $product_id, $qty, $new_product_id)
    {
        $Ssql = "SELECT balance FROM " . STORE_STOCK_VIEW . " WHERE product_id = '$product_id' AND store_id = '$transfer_from' AND project_id = '$project_id'";
        $Srow = mysql_fetch_object(mysql_query($Ssql));
        $stock_qty = $Srow->balance;
        if (($stock_qty > 0) && ($stock_qty >= $qty)) {
            $Pcsql = "SELECT product_type,m_unit,unit_price FROM " . PRODUCT_TBL . " WHERE product_id='$product_id' AND project_id='$project_id'";
            $Pcrow = mysql_fetch_object(mysql_query($Pcsql));
            $m_unit = $Pcrow->m_unit;
            $product_type = $Pcrow->product_type;
            $unit_price = $Pcrow->unit_price;

            if ($transfer_from != "" && $po_no > 0) {
                //===== Cr Stock ======
                $totalFCR = $this->getTotalCreditStock($product_id, $project_id);
                $totalFDR = $this->getTotalDebitStock($product_id, $project_id);
                $TFbalance = ($totalFDR - ($totalFCR + $qty));

                $note = "Transfer Stock";
                $created_by = getFromSession('userid');
                $create_date = date('Y-m-d h:i:s');
                $DR = 0;
                $CR = $qty;
                $sql1 = "INSERT INTO " . STOCK_LEDGER_TBL . " (voucher_no,po_no,project_id,store_id,product_id,product_type,note,unit_price,m_unit,dr,cr,balance,created_by,create_date) VALUES('" . $voucher_no . "','" . $po_no . "','" . $project_id . "','" . $transfer_from . "','" . $product_id . "','" . $product_type . "','" . $note . "','" . $unit_price . "','" . $m_unit . "','" . $DR . "','" . $CR . "','" . $TFbalance . "','" . $created_by . "','" . $create_date . "')";
                mysql_query($sql1);

            }

            if ($transfer_from != "" && $po_no > 0) {
                //===== Dr Stock ======
                $totalFDR = $this->getTotalDebitStock($new_product_id, $project_id);
                $totalFCR = $this->getTotalCreditStock($new_product_id, $project_id);
                $TTbalance = (($totalFDR + $qty) - $totalFCR);

                $note = "Received Stock";
                $created_by = getFromSession('userid');
                $create_date = date('Y-m-d h:i:s');
                $DR = $qty;
                $CR = 0;
                $sql2 = "INSERT INTO " . STOCK_LEDGER_TBL . " (voucher_no,po_no,project_id,store_id,product_id,product_type,note,unit_price,m_unit,dr,cr,balance,created_by,create_date) VALUES('" . $voucher_no . "','" . $po_no . "','" . $project_id . "','" . $transfer_from . "','" . $new_product_id . "','" . $product_type . "','" . $note . "','" . $unit_price . "','" . $m_unit . "','" . $DR . "','" . $CR . "','" . $TTbalance . "','" . $created_by . "','" . $create_date . "')";
                mysql_query($sql2);
            }

        }
    }


    function updateStockTransfer()
    {
        $transfer_no = getRequest('transfer_no');
        $delivery_point = getRequest('delivery_point');
        $ttlfield = getRequest('ttlfield');
        $transfer_from = getRequest('transfer_from');
        $transfer_date = formatDate(getRequest('transfer_date'));
        $narration = trim(htmlspecialchars(getRequest('narration'), ENT_QUOTES, 'UTF-8'));
        $total_amount = getRequest('total_amount');
        $updated_by = getFromSession('userid');
        $updated_time = date('Y-m-d h:i:s');
        $currency = getRequest('currency');
        $project_id = getFromSession('project_id');
        if ($transfer_no != "") {
            $j = 1;
            for ($j; $j < $ttlfield; $j++) {
                $transfer_id = getRequest("old_id$j");
                $catagory = getRequest("catagory$j");
                $brand_id = getRequest("brand$j");
                $product = getRequest("product$j");
                $m_unit = getRequest("m_unit$j");
                $unit_price = getRequest("unit_price$j");
                $qty = getRequest("qty$j");
                $total = ($unit_price * $qty);
                $Pcsql = "SELECT catagory,brand_code,m_unit FROM " . PRODUCT_TBL . " WHERE product_id='$product' AND project_id='$project_id'";
                $Pcrow = mysql_fetch_object(mysql_query($Pcsql));
                $m_unit = $Pcrow->m_unit;
                $brand_id = $Pcrow->brand_code;
                $catagory = $Pcrow->catagory;

                if ($transfer_id > 0 && $qty > 0) {
                    $usql = "UPDATE " . STOCK_TRANSFER_DETAILS_TBL . " SET transfer_from='$transfer_from', 	delivery_point='$delivery_point',catagory='$catagory',brand_id='$brand_id',product='$product',m_unit='$m_unit',
		unit_price='$unit_price',qty='$qty',total='$total',
		transfer_date='$transfer_date',created_by='$updated_by',created_date='$updated_time' WHERE transfer_id=$transfer_id AND transfer_no='$transfer_no'";
                    mysql_query($usql);
                    $this->updateStockJournal($transfer_no, $transfer_id, $project_id, $transfer_from, $delivery_point, $product, $qty);
                } elseif ($transfer_id == 0 && $product != "" && $qty > 0) {
                    $isql = "INSERT INTO " . STOCK_TRANSFER_DETAILS_TBL . " (transfer_no,project_id,transfer_from,delivery_point,catagory,brand_id,product,m_unit,
		unit_price,qty,total,created_by,created_date) VALUES(		
		'$transfer_no','$project_id','$transfer_from','$delivery_point','$catagory','$brand_id','$product','$m_unit',
		'$unit_price','$qty','$total','$updated_by','$updated_time')";
                    mysql_query($isql);
                    $transfer_id = mysql_insert_id();
                    $this->InsertStockJournal($transfer_no, $transfer_id, $project_id, $transfer_from, $delivery_point, $product, $qty);
                }


            }

            $usql = "UPDATE " . STOCK_TRANSFER_MASTER_TBL . " SET transfer_from='$transfer_from', delivery_point= '$delivery_point',narration='$narration',total_amount='$total_amount',transfer_date='$transfer_date',updated_by='$updated_by',updated_time='$updated_time' WHERE transfer_no ='$transfer_no'";
            $smres = mysql_query($usql);

            if ($smres) {
                return true;
            } else {
                return false;
            }
        }

    }

    function updateStockJournal($voucher_no, $po_no, $project_id, $transfer_from, $transfer_to, $product_id, $qty)
    {

        $Ssql = "SELECT balance FROM " . STORE_STOCK_VIEW . " WHERE product_id = '" . $product_id . "' AND store_id = '$transfer_from' AND project_id = '$project_id'";
        $Srow = mysql_fetch_object(mysql_query($Ssql));
        $stock_qty = $Srow->balance;
        if (($stock_qty > 0) && ($stock_qty >= $qty)) {
            $Pcsql = "SELECT product_type,m_unit,unit_price FROM " . PRODUCT_TBL . " WHERE product_id='$product_id' AND project_id='$project_id'";
            $Pcrow = mysql_fetch_object(mysql_query($Pcsql));
            $m_unit = $Pcrow->m_unit;
            $product_type = $Pcrow->product_type;
            $unit_price = $Pcrow->unit_price;

            if ($transfer_from != "" && $po_no > 0) {
                //===== Cr Stock ======
                $totalFCR = $this->getTotalCreditStock($product, $project_id);
                $totalFDR = $this->getTotalDebitStock($product, $project_id);
                $TFbalance = ($totalFDR - ($totalFCR + $transfer_qty));

                $note = "Transfer Stock";
                $sql1 = "UPDATE " . STOCK_LEDGER_TBL . " SET store_id='$transfer_from',product_id='$product_id', product_type='$product_type',note='$note',unit_price='$unit_price',m_unit='$m_unit',dr=0,cr=$qty,balance='$TFbalance' WHERE voucher_no='" . $voucher_no . "' AND po_no='" . $po_no . "'";
                mysql_query($sql1);
            }

            if ($transfer_from != "" && $po_no > 0) {
                //===== Dr Stock ======
                $totalFCR = $this->getTotalCreditStock($product, $project_id);
                $totalFDR = $this->getTotalDebitStock($product, $project_id);
                $TTbalance = (($totalFDR + $transfer_qty) - $totalFCR);

                $note = "Received Stock";
                $sql2 = "UPDATE " . STOCK_LEDGER_TBL . " SET store_id='$transfer_from',product_id='$product_id', product_type='$product_type',note='$note',unit_price='$unit_price',m_unit='$m_unit',dr=$qty,cr=0,balance='$TTbalance' WHERE voucher_no='" . $voucher_no . "' AND po_no='" . $po_no . "'";
                mysql_query($sql2);
            }

        }
    }

    function InsertStockJournal($voucher_no, $po_no, $project_id, $transfer_from, $transfer_to, $product_id, $qty)
    {

        $Ssql = "SELECT balance FROM " . STORE_STOCK_VIEW . " WHERE product_id = '" . $product_id . "' AND store_id = '$transfer_from' AND project_id = '$project_id'";
        $Srow = mysql_fetch_object(mysql_query($Ssql));
        $stock_qty = $Srow->balance;
        if (($stock_qty > 0) && ($stock_qty >= $qty)) {
            $Pcsql = "SELECT product_type,m_unit,unit_price FROM " . PRODUCT_TBL . " WHERE product_id='$product_id' AND project_id='$project_id'";
            $Pcrow = mysql_fetch_object(mysql_query($Pcsql));
            $m_unit = $Pcrow->m_unit;
            $product_type = $Pcrow->product_type;
            $unit_price = $Pcrow->unit_price;

            if ($transfer_from != "" && $po_no > 0) {
                //===== Cr Stock ======
                $totalFCR = $this->getTotalCreditStock($product, $project_id);
                $totalFDR = $this->getTotalDebitStock($product, $project_id);
                $TFbalance = ($totalFDR - ($totalFCR + $transfer_qty));

                $note = "Transfer Stock";
                $created_by = getFromSession('userid');
                $create_date = date('Y-m-d h:i:s');
                $DR = 0;
                $CR = $qty;
                $sql1 = "INSERT INTO " . STOCK_LEDGER_TBL . " (voucher_no,po_no,project_id,store_id,product_id,product_type,note,unit_price,m_unit,dr,cr,balance,created_by,create_date) 
	VALUES('" . $voucher_no . "','" . $po_no . "','" . $project_id . "','" . $transfer_from . "','" . $product_id . "','" . $product_type . "','" . $note . "','" . $unit_price . "','" . $m_unit . "','" . $DR . "','" . $CR . "','" . $TFbalance . "','" . $created_by . "','" . $create_date . "')";
                mysql_query($sql1);

            }

            if ($transfer_from != "" && $po_no > 0) {
                //===== Dr Stock ======
                $totalFCR = $this->getTotalCreditStock($product, $project_id);
                $totalFDR = $this->getTotalDebitStock($product, $project_id);
                $TTbalance = (($totalFDR + $transfer_qty) - $totalFCR);

                $note = "Received Stock";
                $created_by = getFromSession('userid');
                $create_date = date('Y-m-d h:i:s');
                $DR = $qty;
                $CR = 0;
                $sql2 = "INSERT INTO " . STOCK_LEDGER_TBL . " (voucher_no,po_no,project_id,store_id,product_id,product_type,note,unit_price,m_unit,dr,cr,balance,created_by,create_date) 
	VALUES('" . $voucher_no . "','" . $po_no . "','" . $project_id . "','" . $transfer_from . "','" . $product_id . "','" . $product_type . "','" . $note . "','" . $unit_price . "','" . $m_unit . "','" . $DR . "','" . $CR . "','" . $TTbalance . "','" . $created_by . "','" . $create_date . "')";
                mysql_query($sql2);
            }

        }
    }

    function deleteItem($transfer_id)
    {
        $updated_by = getFromSession('userid');
        $updated_time = date('Y-m-d h:i:s');
        $project_id = getFromSession('project_id');
        $sql = "SELECT * FROM " . STOCK_TRANSFER_DETAILS_TBL . " WHERE transfer_id=$transfer_id 
	AND project_id='" . $project_id . "'";
        $res = mysql_query($sql);
        $num = mysql_num_rows($res);
        if ($num > 0) {
            $row = mysql_fetch_object($res);
            $total = $row->total;
            $transfer_no = $row->transfer_no;
            //===== Delete Contra Voucher Item =====
            $tsql1 = "DELETE FROM " . STOCK_TRANSFER_DETAILS_TBL . " WHERE transfer_id=$transfer_id AND transfer_no='$transfer_no' AND project_id='" . $project_id . "'";
            mysql_query($tsql1);
            $tsql2 = "DELETE FROM " . STOCK_LEDGER_TBL . " WHERE transfer_no='$transfer_no' AND po_no=$transfer_id AND project_id='" . $project_id . "'";
            mysql_query($tsql2);

            //==== Update Transfer Master =====

            $msql = "SELECT * FROM " . STOCK_TRANSFER_MASTER_TBL . " WHERE transfer_no ='$transfer_no' 
	AND project_id='" . $project_id . "'";
            $mres = mysql_query($msql);
            $mrow = mysql_fetch_object($mres);
            $total_amount = ($mrow->total_amount - $total);

            $usql = "UPDATE " . STOCK_TRANSFER_MASTER_TBL . " SET total_amount='$total_amount', updated_by='$updated_by', updated_time='$updated_time' WHERE transfer_no ='$transfer_no'";
            $smres = mysql_query($usql);

        }

    }

    //===== End Edit Transfer Order =======
    function showPrintEditor($msg = null)
    {
        $transfer_no = getRequest('transfer_no');
        if ($transfer_no) {
            $advArr = $this->getTransferMasterInfo($transfer_no);
            $advArr = parseThisValue($advArr);
            $data = array_merge(array(), $advArr);
            $data['item_list'] = $this->getProductList($transfer_no);
            $data['message'] = $msg;
            $data['cmd'] = getRequest('cmd');
            require_once(PRNIT_TRANSFER_CHALLAN_SKIN);
            return true;
        }
    }

    function showPendingPrintEditor($msg = null)
    {
        $transfer_no = getRequest('transfer_no');
        if ($transfer_no) {
            $advArr = $this->getPendingTransferMasterInfo($transfer_no);
            $advArr = parseThisValue($advArr);
            $data = array_merge(array(), $advArr);
            $data['item_list'] = $this->getPendingProductList($transfer_no);
            $data['message'] = $msg;
            $data['cmd'] = getRequest('cmd');
            require_once(PRNIT_PENDING_TRANSFER_CHALLAN_SKIN);
            return true;
        }
    }

    function showPrintConvertChallan($msg = null)
    {
        $transfer_no = getRequest('transfer_no');
        if ($transfer_no) {
            $advArr = $this->getTransferMasterInfo($transfer_no);
            $advArr = parseThisValue($advArr);
            $data = array_merge(array(), $advArr);
            $data['item_list'] = $this->getConvertProductList($transfer_no);
            $data['message'] = $msg;
            $data['cmd'] = getRequest('cmd');
            require_once(PRNIT_CONVERT_CHALLAN_SKIN);
            return true;
        }
    }


    function showPendingPrintConvertChallan($msg = null)
    {
        $transfer_no = getRequest('transfer_no');
        if ($transfer_no) {
            $advArr = $this->getPendingTransferMasterInfo($transfer_no);
            $advArr = parseThisValue($advArr);
            $data = array_merge(array(), $advArr);
            $data['item_list'] = $this->getPendingProductList($transfer_no);
            $data['message'] = $msg;
            $data['cmd'] = getRequest('cmd');
            require_once(PRNIT_PENDING_CONVERT_CHALLAN_SKIN);
            return true;
        }
    }


    function showEditor($msg = null)
    {
        $data = array();
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();

        $data['product_list'] = $comListApp->getProductList();
        $data['brand_list'] = $comListApp->getBrandList();
        $data['currency_list'] = $this->getCurrencyList();
        $data['user_depo_list'] = $comListApp->getDeliveryPointList(true);
        $data['depo_list'] = $comListApp->getDeliveryPointList();
        $data['tmp_sales'] = $this->getTempSales();

        $supplierArray = $comListApp->getSupplierData();
        $supplierMap = array();

        if (!empty($supplierArray)) {
            foreach ($supplierArray as $row) {
                if (!empty($row['sub_id'])) {
                    $supplierMap[trim($row['sub_id'])] = $row['point_id'];
                }
            }
        }

        $wip_store_list = [];
        $wip_list = $comListApp->getAccountHeadList("Current Assets", NULL, "C000057");
        foreach ($wip_list as $item) {
            $subId = trim($item->sub_id);

            if (!empty($subId) && isset($supplierMap[$subId])) {
                $wip_store_list[] = $supplierMap[$subId];
            }
        }
        $data['wip_store_list'] = $wip_store_list;

        $data['cmd'] = getRequest('cmd');
        require_once(CURRENT_APP_SKIN_FILE);
        return $data[0];
    }

    //===== Saart Save Sales ====

    function saveTempSales()
    {
        $str = getRequest('str');
        $strArr = explode("####", $str);
        //======= Insert into tamp ========
        $requestdata = array();
        $requestdata = getUserDataSet(TEMP_STOCK_TRANSFER_TBL);
        $project_id = getFromSession('project_id');
        $requestdata['project_id'] = $project_id;
        $requestdata['transfer_from'] = getRequest('transfer_from');
        $requestdata['delivery_point'] = getRequest('delivery_point');
        $requestdata['transfer_date'] = formatDate(getRequest('transfer_date'));
        $requestdata['currency'] = getRequest('currency');
        $requestdata['currencyName'] = getRequest('currencyName');
        $requestdata['productid'] = getRequest('productid');
        $requestdata['new_product_id'] = getRequest('new_product_id');

        $sql = "SELECT p.product_name,p.catagory,c.catagory_name,p.brand_code,b.brand_name,p.m_unit FROM " . PRODUCT_TBL . " as p," . CATAGORY_TBL . " as c," . BRAND_TBL . " as b 
	WHERE p.catagory=c.catagory_code AND p.brand_code=b.brand_id AND p.product_id = '" . $requestdata['productid'] . "'";
        $row = mysql_fetch_object(mysql_query($sql));
        $requestdata['product_name'] = $row->product_name;
        $requestdata['catagory'] = $row->catagory;
        $requestdata['catagoryname'] = $row->catagory_name;
        $requestdata['brand_id'] = $row->brand_code;
        $requestdata['brandname'] = $row->brand_name;
        //$requestdata['details'] 	= getRequest('details');
        $requestdata['munit'] = $row->m_unit;
        $requestdata['qty'] = getRequest('qty');
        $requestdata['unit_price'] = getRequest('unit_price');
        $requestdata['total'] = getRequest('total');

        $requestdata['created_by'] = getFromSession('userid');
        $info = array();
        $info['table'] = TEMP_STOCK_TRANSFER_TBL;
        $info['data'] = $requestdata;
        //$info['debug']  	=  true;
        $res = insert($info);

        $str1 = "<table width='100%' align='center' bgcolor='#99CC66'>
	<tr style='color:#fff;' bgcolor='#00B000' height=28>
	  <td width='2%' nowrap><div align='left'>SL</div></td>
	  <td width='20%' nowrap><div align='left'>Product Name</div></td>";

        if (!empty($requestdata['new_product_id'])) {
            $str1 .= "<td width='20%' nowrap><div align='left'>New Product Name</div></td>";
        }

        $str1 .= "<td width='10%' nowrap><div align='left'>Catagory</div></td>
	  <td width='10%' nowrap><div align='left'>Brand</div></td>
	  <td width='10%' nowrap><div align='right'>Transfer Qty</div></td>
	  <td width='10%' nowrap><div align='right'>Rate</div></td>	  
	  <td width='10%' nowrap><div align='right'>Amount</div></td>				  
	  <td width='8%' nowrap align='center'>Option</td>
	</tr>";

        $total_value = 0;
        $product_discount = 0;
        $sl = 1;
        $TotalQty = 0;
        $TotalFreeQty = 0;

        $getSql = "SELECT t.*, p.product_name AS new_product_name FROM " . TEMP_STOCK_TRANSFER_TBL . " t
            LEFT JOIN product p ON p.product_id = t.new_product_id
            WHERE t.created_by = '" . getFromSession('userid') . "' 
            AND t.project_id='" . $project_id . "'";

        $gres = mysql_query($getSql);
        while ($row = mysql_fetch_array($gres)) {
            extract($row);
            $total_value += $total;
            $TotalQty += $qty;


            $str2 .= "
	<tr style='color:#000000' bgcolor='#fff'>
	  <td width='2%' nowrap align='left'>$sl</td>
	  <td width='20%' nowrap align='left'>$product_name</td>";

            if (!empty($requestdata['new_product_id'])) {
                $str2 .= "<td width='20%' nowrap align='left'>$new_product_name</td>";
            }

            $str2 .= "<td width='10%' nowrap align='left'>$catagoryname</td>
	  <td width='10%' nowrap align='left'>$brandname</td>
	  <td width='10%' nowrap><div align='right'>$qty $munit</div></td>
	  <td width='10%' nowrap align='right'>$unit_price $currencyName</td>	  
	  <td width='10%' nowrap align='right'>$total</td>				  				  
	  <td width='8%' nowrap align='center'><a href=\"?app=groupwise.stock.transfer&cmd=deltemp&id=$tmp_id\"><img src=\"images/common/icons/delete.gif\"></a></td>
	</tr>";


            $sl++;
        }
        $str3 = "
	<tr style='color:#000;' bgcolor='#CCCCCC' height=25>";

        if (!empty($requestdata['new_product_id'])) {
            $str3 .= " <td nowrap>&nbsp;</td>";
        }

        $str3 .= "
	  <td colspan='4' nowrap><div align='right'>Total </div></td>
	  <td nowrap align='right'>$TotalQty $munit</td>
	  <td nowrap>&nbsp;</td>
	  <td nowrap align='right'>$total_value $currencyName</td>
	  <td nowrap align='center'>&nbsp;</td>
	</tr>
	</table>";

        $productConvert = false;
        if (!empty($requestdata['new_product_id'])) {
            $productConvert = true;
        }

        echo $str1 . $str2 . $str3 . "####-@@@@" . $total_value . "####-@@@@" . $productConvert;
    }

    function delTempSales()
    {
        $tmp_id = $_REQUEST['id'];
        if ($tmp_id != "") {
            $dsql = "DELETE FROM " . TEMP_STOCK_TRANSFER_TBL . " WHERE tmp_id ='" . $tmp_id . "'";
            mysql_query($dsql);
        }
        header("location:?app=groupwise.stock.transfer&cmd=add");
    }

    function getTempSales()
    {
        $project_id = getFromSession('project_id');
        $total_value = 0;
        $product_discount = 0;
        $TotalQty = 0;
        $TotalFreeQty = 0;
        $sl = 1;

        //$getSql		= "SELECT * FROM ".TEMP_STOCK_TRANSFER_TBL." WHERE created_by = '".getFromSession('userid')."' AND project_id='".$project_id."'";

        $getSql = "SELECT t.*, p.product_name AS new_product_name FROM " . TEMP_STOCK_TRANSFER_TBL . " t
            LEFT JOIN product p ON p.product_id = t.new_product_id
            WHERE t.created_by = '" . getFromSession('userid') . "' 
            AND t.project_id='" . $project_id . "'";

        $productConvert = false;
        if (!empty($requestdata['new_product_id'])) {
            $str3 .= " <td nowrap>&nbsp;</td>";
        }

        $gres = mysql_query($getSql);
        while ($row = mysql_fetch_array($gres)) {
            extract($row);

            if (!empty($new_product_id)) {
                $productConvert = true;
            }

            $total_value += $total;
            $TotalQty += $qty;
            $str2 .= "
		<tr style='color:#000000' bgcolor='#fff'>
		  <td width='2%' nowrap align='left'>$sl</td>
		  <td width='20%' nowrap align='left'>$product_name</td>";


            if ($productConvert) {
                $str2 .= "<td width='20%' nowrap align='left'>$new_product_name</td>";
            }

            $str2 .= "<td width='10%' nowrap align='left'>$catagoryname</td>
		  <td width='10%' nowrap align='left'>$brandname</td>
		  <td width='10%' nowrap><div align='right'>$qty $munit</div></td>
		  <td width='10%' nowrap align='right'>$unit_price $currencyName</td>	  
		  <td width='10%' nowrap align='right'>$total</td>				  				  
		  <td width='8%' nowrap align='center'><a href=\"?app=groupwise.stock.transfer&cmd=deltemp&id=$tmp_id\"><img src=\"images/common/icons/delete.gif\"></a></td>
		</tr>";

            $sl++;
        }

        $str1 = "<table width='100%' align='center' bgcolor='#99CC66'>
	<tr style='color:#fff;' bgcolor='#00B000' height=28>
	  <td width='2%' nowrap><div align='left'>SL</div></td>
	  <td width='20%' nowrap><div align='left'>Product Name</div></td>";

        if ($productConvert) {
            $str1 .= "<td width='20%' nowrap><div align='left'>New Product Name</div></td>";
        }

        $str1 .= "<td width='10%' nowrap><div align='left'>Catagory</div></td>
	  <td width='10%' nowrap><div align='left'>Brand</div></td>
	  <td width='10%' nowrap><div align='right'>Transfer Qty</div></td>
	  <td width='10%' nowrap><div align='right'>Rate</div></td>	  
	  <td width='10%' nowrap><div align='right'>Amount</div></td>				  
	  <td width='8%' nowrap align='center'>Option</td>
	</tr>";

        $str3 = "<tr style='color:#000;' bgcolor='#CCCCCC' height=25>";

        if ($productConvert) {
            $str3 .= "<td nowrap>&nbsp;</td>";
        }

        $str3 .= "
	  <td colspan='4' nowrap><div align='right'>Total </div></td>
	  <td nowrap align='right'>$TotalQty $munit</td>
	  <td nowrap>&nbsp;</td>
	  <td nowrap align='right'>$total_value $currencyName</td>
	  <td nowrap align='center'>&nbsp;</td>
	</tr>
	</table>";

        $total_salesStr = $str1 . $str2 . $str3 . "####-@@@@" . $total_value . "####-@@@@" . $product_discount . "####-@@@@" . $productConvert;
        return $total_salesStr;
    }

    //====== End Save Sales =====
    function moveStockQty($transfer_id, $voucher_no, $transfer_from, $store_id, $product, $transfer_qty, $transfer_date)
    {
        $project_id = getFromSession('project_id');
        $Ssql = "SELECT balance FROM " . STORE_STOCK_VIEW . " WHERE product_id = '" . $product . "' AND store_id = '$transfer_from' AND project_id = '$project_id'";
        $Srow = mysql_fetch_object(mysql_query($Ssql));
        $stock_qty = $Srow->balance;
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
            $this->saveStockJournal($voucher_no, $transfer_id, $project_id, $transfer_from, $product, $product_type, "Transfer Stock", $unit_price, $m_unit, 0, $transfer_qty, $TFbalance, $transfer_date);
            //===== Dr Stock ======
            $totalFCR = $this->getTotalCreditStock($product, getFromSession('project_id'));
            $totalFDR = $this->getTotalDebitStock($product, getFromSession('project_id'));
            $TTbalance = (($totalFDR + $transfer_qty) - $totalFCR);
            $this->saveStockJournal($voucher_no, $transfer_id, $project_id, $store_id, $product, $product_type, "Received Stock", $unit_price, $m_unit, $transfer_qty, 0, $TTbalance, $transfer_date);
            return true;
        } else {
            return false;
        }
    }

    function insertTransferDetails($voucher_no)
    {
        $requestdata = array();
        $arr_catagory_product_id = array();
        $project_id = getFromSession('project_id');
        $currency = getRequest('currency');
        $getSql = "SELECT * FROM " . TEMP_STOCK_TRANSFER_TBL . " WHERE created_by = '" . getFromSession('userid') . "' AND project_id='" . getFromSession('project_id') . "'";
        $gres = mysql_query($getSql);
        if (mysql_num_rows($gres) > 0) {
            while ($row = mysql_fetch_object($gres)) {
                $requestdata['transfer_no'] = $voucher_no;
                $requestdata['transfer_from'] = getRequest('transfer_from');
                $requestdata['delivery_point'] = getRequest('delivery_point');
                $requestdata['project_id'] = $project_id;
                $requestdata['catagory'] = $row->catagory;
                $requestdata['brand_id'] = $row->brand_id;
                $requestdata['product'] = $row->productid;
                $product_id = $row->productid;
                $requestdata['m_unit'] = $row->munit;
                //$requestdata['details'] 	= $row->details;
                $requestdata['unit_price'] = $row->unit_price;
                $requestdata['qty'] = $row->qty;
                $requestdata['total'] = $row->total;
                $requestdata['transfer_date'] = formatDate(getRequest('transfer_date'));
                $requestdata['created_by'] = getFromSession('userid');
                $requestdata['created_date'] = date('Y-m-d h:i:s');

                $info = array();
                $info['table'] = STOCK_TRANSFER_DETAILS_TBL;
                $info['data'] = $requestdata;
                //$info['debug']  	=  true;
                $res = insert($info);
                if ($res) {
                    $transfer_id = mysql_insert_id();
                    $this->moveStockQty($transfer_id, $voucher_no, $requestdata['transfer_from'], $requestdata['delivery_point'], $product_id, $requestdata['qty'], $requestdata['transfer_date']);
                }
            }// end while
        }// end if

        if ($res) {
            $dsql = "DELETE FROM " . TEMP_STOCK_TRANSFER_TBL . " WHERE created_by = '" . getFromSession('userid') . "' AND project_id='" . getFromSession('project_id') . "'";
            mysql_query($dsql);
        }
    } //End of the function insertSalesDetails()

    function insertPendingTransferDetails($transfer_id)
    {
        $requestdata = array();
        $arr_catagory_product_id = array();
        $project_id = getFromSession('project_id');
        $currency = getRequest('currency');
        $getSql = "SELECT * FROM " . TEMP_STOCK_TRANSFER_TBL . " WHERE created_by = '" . getFromSession('userid') . "' AND project_id='" . getFromSession('project_id') . "'";
        $gres = mysql_query($getSql);
        if (mysql_num_rows($gres) > 0) {
            while ($row = mysql_fetch_object($gres)) {
                $requestdata['transfer_id'] = $transfer_id;
                $requestdata['transfer_from'] = getRequest('transfer_from');
                $requestdata['delivery_point'] = getRequest('delivery_point');
                $requestdata['project_id'] = $project_id;
                $requestdata['catagory'] = $row->catagory;
                $requestdata['brand_id'] = $row->brand_id;
                $requestdata['product'] = $row->productid;
                $product_id = $row->productid;
                $requestdata['m_unit'] = $row->munit;
                //$requestdata['details'] 	= $row->details;
                $requestdata['unit_price'] = $row->unit_price;
                $requestdata['qty'] = $row->qty;
                $requestdata['total'] = $row->total;
                $requestdata['transfer_date'] = formatDate(getRequest('transfer_date'));
                $requestdata['created_by'] = getFromSession('userid');
                $requestdata['created_date'] = date('Y-m-d h:i:s');

                $job_name = getRequest('job_name');
                if (!empty($job_name)) {
                    $requestdata['job_name'] = $job_name;
                }
                $finish_item = getRequest('finish_item');
                if (!empty($finish_item)) {
                    $requestdata['finish_item'] = $finish_item;
                }

                $info = array();
                $info['table'] = PENDING_STOCK_TRANSFER_DETAILS_TBL;
                $info['data'] = $requestdata;
                //$info['debug']  	=  true;
                $res = insert($info);
            }// end while
        }// end if

        if ($res) {
            $dsql = "DELETE FROM " . TEMP_STOCK_TRANSFER_TBL . " WHERE created_by = '" . getFromSession('userid') . "' AND project_id='" . getFromSession('project_id') . "'";
            mysql_query($dsql);
        }
    } //End of the function insertSalesDetails()


    function insertTransferMaster()
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comlistApp = new CommonList();
        $project_id = getFromSession('project_id');
        $requestdata = array();
        $requestdata = getUserDataSet(STOCK_TRANSFER_MASTER_TBL);
        $requestdata['transfer_date'] = formatDate(getRequest('transfer_date'));
        $requestdata['narration'] = trim(htmlspecialchars(getRequest('narration'), ENT_QUOTES, 'UTF-8'));
        $requestdata['total_amount'] = getRequest('total_amount');

        $requestdata['project_id'] = getFromSession('project_id');
        $requestdata['created_by'] = getFromSession('userid');
        $requestdata['created_date'] = date('Y-m-d h:i:s');
        $transfer_no = $this->createVoucharID();
        $requestdata['transfer_no'] = $transfer_no;
        $info = array();
        $info['table'] = STOCK_TRANSFER_MASTER_TBL;
        $info['data'] = $requestdata;
        //$info['debug']  =  true;
        $res = insert($info);
        if ($res) {
            $transfer_from = getRequest('transfer_from');
            $transfer_to = getRequest('delivery_point');
            $transfer_date = formatDate(getRequest('transfer_date'));

            // ======= AC Transfer for production Cr =================
            $total_amount = getRequest('total_amount');
            $description = "Out Raw Materials";
            $PurchaseId = $comlistApp->getRMStockId($project_id);
            $fromAccountLedger = $comlistApp->getStoreMapLedgerID($transfer_from, "account_ledger");
            if ($fromAccountLedger) {
                $PurchaseId = $fromAccountLedger;
            }

            if ($PurchaseId) {
                $ACPurchaseAmount = $this->getAccounceBalance($PurchaseId, $project_id);
                $PurchaseBalance = ($ACPurchaseAmount - $total_amount);
                $this->saveAccountJournal($transfer_no, $PurchaseId, "Inventories", $project_id, $description, 0, $total_amount, $PurchaseBalance, 1, $transfer_date);
            }

            // ======= AC WIP Dr =================
            $description = "Transfer Raw Materials in WIP";
            $StockId = $comlistApp->getWPStockId($project_id);
            $toAccountLedger = $comlistApp->getStoreMapLedgerID($transfer_to, "account_ledger");
            if ($toAccountLedger) {
                $StockId = $toAccountLedger;
            }

            if ($StockId) {
                $ACPurchaseAmount = $this->getAccounceBalance($StockId, $project_id);
                $StockBalance = ($ACPurchaseAmount + $total_amount);
                $comlistApp->saveAccJournal($transfer_no, $StockId, "Stock", "Transfer Raw Materials", $project_id, $description, $total_amount, 0, $StockBalance, 0, $transfer_date);
            }

            return $transfer_no;
        } else {
            return 0;
        }
    }

    function insertPendingTransferMaster($product_convert = false)
    {
        $requestdata = array();
        $requestdata = getUserDataSet(PENDING_STOCK_TRANSFER_MASTER_TBL);
        $requestdata['transfer_date'] = formatDate(getRequest('transfer_date'));
        $requestdata['narration'] = trim(htmlspecialchars(getRequest('narration'), ENT_QUOTES, 'UTF-8'));
        $requestdata['total_amount'] = getRequest('total_amount');

        $job_name = getRequest('job_name');
        if (!empty($job_name)) {
            $requestdata['job_name'] = $job_name;
        }
        $finish_item = getRequest('finish_item');
        if (!empty($finish_item)) {
            $requestdata['finish_item'] = $finish_item;
        }

        $requestdata['project_id'] = getFromSession('project_id');
        $requestdata['created_by'] = getFromSession('userid');
        $requestdata['created_date'] = date('Y-m-d h:i:s');

        if ($product_convert) {
            $requestdata['product_convert'] = 1;
        }

        $info = array();
        $info['table'] = PENDING_STOCK_TRANSFER_MASTER_TBL;
        $info['data'] = $requestdata;
        //$info['debug']  =  true;
        $res = insert($info);
        if ($res) {
            return mysql_insert_id();
        } else {
            return 0;
        }
    }


    function saveSalesItemOld()
    {
        mysql_query("START TRANSACTION;");
        mysql_query("SET autocommit=0;");
        $transfer_no = $this->insertTransferMaster();
        $this->insertTransferDetails($transfer_no);
        mysql_query("COMMIT;");
        if ($transfer_no != "") {
            header("location:index.php?app=groupwise.stock.transfer&cmd=print_challan&transfer_no=" . $transfer_no);
        } else {
            header("location:index.php?app=groupwise.stock.transfer&cmd=add");
        }
    }

    function saveSalesItem()
    {
        mysql_query("START TRANSACTION;");
        mysql_query("SET autocommit=0;");
        $transfer_no = $this->insertPendingTransferMaster();
        $this->insertPendingTransferDetails($transfer_no);
        mysql_query("COMMIT;");
        if ($transfer_no != "") {
            header("location:index.php?app=groupwise.stock.transfer&cmd=pending_print_challan&transfer_no=" . $transfer_no);
        } else {
            header("location:index.php?app=groupwise.stock.transfer&cmd=add");
        }
    }

    function saveConvertSalesItemOld()
    {
        mysql_query("START TRANSACTION;");
        mysql_query("SET autocommit=0;");

        $project_id = getFromSession('project_id');
        $userid = getFromSession('userid');
        $getSql = "SELECT 1 FROM " . TEMP_STOCK_TRANSFER_TBL . " WHERE (new_product_id IS NULL OR new_product_id = '') AND created_by = '$userid' AND project_id = '$project_id' LIMIT 1";

        $gres = mysql_query($getSql);
        if (mysql_num_rows($gres) > 0) {
            $error = "Your items are not compatible for product convert because some items new product id missing";
            header("location:index.php?app=groupwise.stock.transfer&cmd=add&error_msg=$error");
            exit;
        }

        $transfer_no = $this->insertProductConvertMaster();
        $this->insertProductConvertDetails($transfer_no);

        mysql_query("COMMIT;");

        if ($transfer_no != "") {
            header("location:index.php?app=groupwise.stock.transfer&cmd=print_convert_challan&transfer_no=" . $transfer_no);
        } else {
            header("location:index.php?app=groupwise.stock.transfer&cmd=add");
        }
    }

    function saveConvertSalesItem()
    {
        mysql_query("START TRANSACTION;");
        mysql_query("SET autocommit=0;");

        $project_id = getFromSession('project_id');
        $userid = getFromSession('userid');
        $getSql = "SELECT 1 FROM " . TEMP_STOCK_TRANSFER_TBL . " WHERE (new_product_id IS NULL OR new_product_id = '') AND created_by = '$userid' AND project_id = '$project_id' LIMIT 1";

        $gres = mysql_query($getSql);
        if (mysql_num_rows($gres) > 0) {
            $error = "Your items are not compatible for product convert because some items new product id missing";
            header("location:index.php?app=groupwise.stock.transfer&cmd=add&error_msg=$error");
            exit;
        }

        $transfer_no = $this->insertPendingTransferMaster(true);
        $this->insertPendingProductConvertDetails($transfer_no);

        mysql_query("COMMIT;");

        if ($transfer_no != "") {
            header("location:index.php?app=groupwise.stock.transfer&cmd=pending_print_convert_challan&transfer_no=" . $transfer_no);
        } else {
            header("location:index.php?app=groupwise.stock.transfer&cmd=add");
        }
    }

    function insertPendingProductConvertDetails($transfer_id)
    {
        $requestdata = array();
        $project_id = getFromSession('project_id');
        $userid = getFromSession('userid');
        $getSql = "SELECT * FROM " . TEMP_STOCK_TRANSFER_TBL . " WHERE new_product_id IS NOT NULL AND new_product_id != '' AND created_by = '$userid' AND project_id='$project_id'";
        $gres = mysql_query($getSql);

        if (mysql_num_rows($gres) > 0) {
            while ($row = mysql_fetch_object($gres)) {
                $requestdata['transfer_id'] = $transfer_id;
                $requestdata['transfer_from'] = getRequest('transfer_from');
                $requestdata['delivery_point'] = getRequest('delivery_point');
                $requestdata['project_id'] = $project_id;
                $requestdata['catagory'] = $row->catagory;
                $requestdata['brand_id'] = $row->brand_id;
                $product_id = $row->productid;
                $requestdata['product'] = $product_id;
                $requestdata['m_unit'] = $row->munit;
                //$requestdata['details'] 	= $row->details;
                $requestdata['unit_price'] = $row->unit_price;
                $requestdata['qty'] = $row->qty;
                $requestdata['total'] = $row->total;
                $requestdata['transfer_date'] = formatDate(getRequest('transfer_date'));
                $requestdata['created_by'] = getFromSession('userid');
                $requestdata['created_date'] = date('Y-m-d h:i:s');
                $new_product_id = $row->new_product_id;
                $requestdata['new_product_id'] = $new_product_id;

                $job_name = getRequest('job_name');
                if (!empty($job_name)) {
                    $requestdata['job_name'] = $job_name;
                }
                $finish_item = getRequest('finish_item');
                if (!empty($finish_item)) {
                    $requestdata['finish_item'] = $finish_item;
                }

                $info = array();
                $info['table'] = PENDING_STOCK_TRANSFER_DETAILS_TBL;
                $info['data'] = $requestdata;
                //$info['debug']  	=  true;
                $res = insert($info);
            }
        }
        if ($res) {
            $dsql = "DELETE FROM " . TEMP_STOCK_TRANSFER_TBL . " WHERE new_product_id IS NOT NULL AND new_product_id != '' AND  created_by = '$userid' AND project_id='$project_id'";
            mysql_query($dsql);
        }
    }

    function insertProductConvertMaster()
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comlistApp = new CommonList();
        $project_id = getFromSession('project_id');
        $requestdata = array();
        $requestdata = getUserDataSet(STOCK_TRANSFER_MASTER_TBL);
        $requestdata['transfer_date'] = formatDate(getRequest('transfer_date'));
        $requestdata['narration'] = trim(htmlspecialchars(getRequest('narration'), ENT_QUOTES, 'UTF-8'));
        $requestdata['total_amount'] = getRequest('total_amount');
        $requestdata['project_id'] = getFromSession('project_id');
        $requestdata['created_by'] = getFromSession('userid');
        $requestdata['created_date'] = date('Y-m-d h:i:s');
        $transfer_no = $this->createVoucharID();
        $requestdata['transfer_no'] = $transfer_no;
        $requestdata['product_convert'] = 1;
        $info = array();
        $info['table'] = STOCK_TRANSFER_MASTER_TBL;
        $info['data'] = $requestdata;
        //$info['debug']  =  true;
        $res = insert($info);
        if ($res) {
            $transfer_from = getRequest('transfer_from');
            $transfer_to = getRequest('delivery_point');
            $transfer_date = formatDate(getRequest('transfer_date'));

            // ======= AC Transfer for production Cr =================
            $total_amount = getRequest('total_amount');
            $description = "Out Raw Materials";
            $PurchaseId = $comlistApp->getRMStockId($project_id);
            $fromAccountLedger = $comlistApp->getStoreMapLedgerID($transfer_from, "account_ledger");
            if ($fromAccountLedger) {
                $PurchaseId = $fromAccountLedger;
            }

            if ($PurchaseId) {
                $ACPurchaseAmount = $this->getAccounceBalance($PurchaseId, $project_id);
                $PurchaseBalance = ($ACPurchaseAmount - $total_amount);
                $this->saveAccountJournal($transfer_no, $PurchaseId, "Inventories", $project_id, $description, 0, $total_amount, $PurchaseBalance, 1, $transfer_date);
            }

            // ======= AC WIP Dr =================
            $description = "Transfer Raw Materials in WIP";
            $StockId = $comlistApp->getWPStockId($project_id);
            $toAccountLedger = $comlistApp->getStoreMapLedgerID($transfer_to, "account_ledger");
            if ($toAccountLedger) {
                $StockId = $toAccountLedger;
            }

            if ($StockId) {
                $ACPurchaseAmount = $this->getAccounceBalance($StockId, $project_id);
                $StockBalance = ($ACPurchaseAmount + $total_amount);
                $comlistApp->saveAccJournal($transfer_no, $StockId, "Stock", "Transfer Raw Materials", $project_id, $description, $total_amount, 0, $StockBalance, 0, $transfer_date);
            }

            return $transfer_no;
        } else {
            return 0;
        }
    }

    function insertProductConvertDetails($voucher_no)
    {
        $requestdata = array();
        $arr_catagory_product_id = array();
        $project_id = getFromSession('project_id');
        $userid = getFromSession('userid');
        $currency = getRequest('currency');
        $getSql = "SELECT * FROM " . TEMP_STOCK_TRANSFER_TBL . " WHERE new_product_id IS NOT NULL AND new_product_id != '' AND created_by = '$userid' AND project_id='$project_id'";
        $gres = mysql_query($getSql);

        if (mysql_num_rows($gres) > 0) {
            while ($row = mysql_fetch_object($gres)) {
                $requestdata['transfer_no'] = $voucher_no;
                $requestdata['transfer_from'] = getRequest('transfer_from');
                $requestdata['delivery_point'] = getRequest('delivery_point');
                $requestdata['project_id'] = $project_id;
                $requestdata['catagory'] = $row->catagory;
                $requestdata['brand_id'] = $row->brand_id;
                $product_id = $row->productid;
                $requestdata['product'] = $product_id;
                $requestdata['m_unit'] = $row->munit;
                //$requestdata['details'] 	= $row->details;
                $requestdata['unit_price'] = $row->unit_price;
                $requestdata['qty'] = $row->qty;
                $requestdata['total'] = $row->total;
                $requestdata['transfer_date'] = formatDate(getRequest('transfer_date'));
                $requestdata['created_by'] = getFromSession('userid');
                $requestdata['created_date'] = date('Y-m-d h:i:s');
                $new_product_id = $row->new_product_id;
                $requestdata['new_product_id'] = $new_product_id;

                $info = array();
                $info['table'] = STOCK_TRANSFER_DETAILS_TBL;
                $info['data'] = $requestdata;
                //$info['debug']  	=  true;
                $res = insert($info);
                if ($res) {
                    $transfer_id = mysql_insert_id();
                    $this->convertProductStockQty($transfer_id, $voucher_no, $requestdata['transfer_from'], $requestdata['delivery_point'], $product_id, $new_product_id, $requestdata['qty'], $requestdata['transfer_date']);
                }
            }
        }
        if ($res) {
            $dsql = "DELETE FROM " . TEMP_STOCK_TRANSFER_TBL . " WHERE new_product_id IS NOT NULL AND new_product_id != '' AND  created_by = '$userid' AND project_id='$project_id'";
            mysql_query($dsql);
        }
    }

    function convertProductStockQty($transfer_id, $voucher_no, $transfer_from, $store_id, $product, $new_product, $transfer_qty, $transfer_date)
    {
        $project_id = getFromSession('project_id');
        $Ssql = "SELECT balance FROM " . STORE_STOCK_VIEW . " WHERE product_id = '" . $product . "' AND store_id = '$transfer_from' AND project_id = '$project_id'";
        $Srow = mysql_fetch_object(mysql_query($Ssql));
        $stock_qty = $Srow->balance;

        $Pcsql = "SELECT product_name,product_id,product_type,m_unit,unit_price FROM " . PRODUCT_TBL . " WHERE product_id='$product' AND project_id='$project_id'";
        $Pcrow = mysql_fetch_object(mysql_query($Pcsql));

        if (($stock_qty > 0) && ($stock_qty >= $transfer_qty)) {

            // Old Product CR
            if (isset($Pcrow->product_id) && !empty($Pcrow->product_id)) {
                $m_unit = $Pcrow->m_unit;
                $product_type = $Pcrow->product_type;
                $unit_price = $Pcrow->unit_price;

                //===== Cr Stock ======
                $totalFDR = $this->getTotalDebitStock($product, $project_id);
                $totalFCR = $this->getTotalCreditStock($product, $project_id);
                $TFbalance = ($totalFDR - ($totalFCR + $transfer_qty));
                $this->saveStockJournal($voucher_no, $transfer_id, $project_id, $transfer_from, $product, $product_type, "Transfer Stock", $unit_price, $m_unit, 0, $transfer_qty, $TFbalance, $transfer_date);

                //===== Dr Stock ======
                $totalFCR = $this->getTotalCreditStock($product, $project_id);
                $totalFDR = $this->getTotalDebitStock($product, $project_id);
                $TTbalance = (($totalFDR + $transfer_qty) - $totalFCR);
                $this->saveStockJournal($voucher_no, $transfer_id, $project_id, $store_id, $product, $product_type, "Received Stock", $unit_price, $m_unit, $transfer_qty, 0, $TTbalance, $transfer_date);

            }


            // New Product DR
            $Pcsql = "SELECT product_id,product_type,m_unit,unit_price FROM " . PRODUCT_TBL . " WHERE product_id='$new_product' AND project_id='$project_id'";
            $Pcrow = mysql_fetch_object(mysql_query($Pcsql));

            if (isset($Pcrow->product_id) && !empty($Pcrow->product_id)) {
                $m_unit = $Pcrow->m_unit;
                $product_type = $Pcrow->product_type;
                $unit_price = $Pcrow->unit_price;


                //===== Cr Stock ======
                $totalFCR = $this->getTotalCreditStock($new_product, $project_id);
                $totalFDR = $this->getTotalDebitStock($new_product, $project_id);
                $TFbalance = ($totalFDR - ($totalFCR + $transfer_qty));
                $this->saveStockJournal($voucher_no, $transfer_id, $project_id, $transfer_from, $new_product, $product_type, "Transfer Stock", $unit_price, $m_unit, 0, $transfer_qty, $TFbalance, $transfer_date);

                //===== Dr Stock ======
                $totalFCR = $this->getTotalCreditStock($new_product, $project_id);
                $totalFDR = $this->getTotalDebitStock($new_product, $project_id);
                $TTbalance = (($totalFDR + $transfer_qty) - $totalFCR);
                $this->saveStockJournal($voucher_no, $transfer_id, $project_id, $store_id, $new_product, $product_type, "Received Stock", $unit_price, $m_unit, $transfer_qty, 0, $TTbalance, $transfer_date);
            }

            return true;
        } else {
            $product = $Pcrow->product_name;
            $msg = "$product stock is lower than transfer quantity. Present stock is $stock_qty";
            mysql_query("ROLLBACK;");
            header("location:index.php?app=sales.report&cmd=pending_transfer_list&error_msg=$msg");
            exit();
            return false;
        }
    }


    function DeleteStockTransfer($transfer_no)
    {
        $userid = getFromSession('userid');
        $created_by = getFromSession('userid');
        $project_id = getFromSession('project_id');

        $getdSql = "SELECT * FROM " . STOCK_TRANSFER_MASTER_TBL . " WHERE transfer_no='" . $transfer_no . "' AND project_id='" . $project_id . "'";
        $gdres = mysql_query($getdSql);
        $drow = mysql_fetch_object($gdres);
        $befoure_amount = $drow->total_amount;

        mysql_query("START TRANSACTION;");
        //========== Delete All ===========
        $Asql = "DELETE FROM " . ACCOUNT_JOURNAL_TBL . " WHERE binary voucher_no ='" . $transfer_no . "' AND project_id='" . $project_id . "'";
        $res = mysql_query($Asql);
        $Dsql = "DELETE FROM " . STOCK_LEDGER_TBL . " WHERE binary voucher_no ='" . $transfer_no . "' AND project_id='" . $project_id . "'";
        $res1 = mysql_query($Dsql);
        $Csql = "DELETE FROM " . STOCK_TRANSFER_DETAILS_TBL . " WHERE transfer_no='" . $transfer_no . "' AND project_id='" . $project_id . "'";
        $res2 = mysql_query($Csql);
        $Stsql = "DELETE FROM " . STOCK_TRANSFER_MASTER_TBL . " WHERE transfer_no='" . $transfer_no . "' AND project_id='" . $project_id . "'";
        $res3 = mysql_query($Stsql);
        if (($res) && ($res1) && ($res2) && ($res3)) {
            SaveActivityLog("Stock Transfer", $transfer_no, "Delete", $created_by, $befoure_amount, 0);
            mysql_query("COMMIT;");
            header("location:index.php?app=sales.report&cmd=transfer_list&msg=Successfully Deleted Stock Transfer!!!");
        } else {
            mysql_query("ROLLBACK;");
            header("location:index.php?app=sales.report&cmd=transfer_list&msg=Failed Delete Stock Transfer. Try again!!!");
        }

    }

    function DeletePendingStockTransfer($transfer_no)
    {
        $project_id = getFromSession('project_id');

        mysql_query("START TRANSACTION;");
        //========== Delete All ===========
        $Csql = "DELETE FROM " . PENDING_STOCK_TRANSFER_DETAILS_TBL . " WHERE transfer_id='" . $transfer_no . "' AND project_id='" . $project_id . "'";
        $res2 = mysql_query($Csql);
        $Stsql = "DELETE FROM " . PENDING_STOCK_TRANSFER_MASTER_TBL . " WHERE id='" . $transfer_no . "' AND project_id='" . $project_id . "'";
        $res3 = mysql_query($Stsql);
        if (($res2) && ($res3)) {
            mysql_query("COMMIT;");
            header("location:index.php?app=sales.report&cmd=pending_transfer_list&msg=Successfully Deleted Stock Transfer!!!");
        } else {
            mysql_query("ROLLBACK;");
            header("location:index.php?app=sales.report&cmd=pending_transfer_list&error_msg=Failed Delete Stock Transfer. Try again!!!");
        }

    }

    function approvedTransferRecord()
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();

        $transfer_id = getRequest('transfer_id');
        if (empty($transfer_id)) {
            header("location:index.php?app=sales.report&cmd=pending_transfer_list");
            exit();
        }

        $getSql = "SELECT * FROM " . PENDING_STOCK_TRANSFER_MASTER_TBL . " WHERE id = '$transfer_id'";
        $result = mysql_fetch_object(mysql_query($getSql));

        $toStore = $result->delivery_point;
        $user_store = $comListApp->getUserStore();
        $userStoreArray = array_map('trim', explode(',', $user_store));

        if (!in_array($toStore, $userStoreArray)) {
            header("location:index.php?app=sales.report&cmd=pending_transfer_list&error_msg=Your are not authorize!!");
            exit;
        }

        $stockCheck = $this->checkProductStock($result);
        if ($stockCheck['status']) {
            $msg = !empty($stockCheck['message']) ? $stockCheck['message'] : "";
            header("location:index.php?app=sales.report&cmd=pending_transfer_list&error_msg=$msg");
            exit();
        }

        mysql_query("START TRANSACTION;");
        mysql_query("SET autocommit=0;");

        $product_convert = $result->product_convert;
        $transfer_no = $this->insertStockTransferMaster($result);
        $this->insertStockTransferDetails($transfer_no, $result);

        mysql_query("COMMIT;");
        if ($transfer_no != "") {
            $challan = "print_challan";
            if ($product_convert == 1) {
                $challan = "print_convert_challan";
            }
            header("location:index.php?app=groupwise.stock.transfer&cmd=$challan&transfer_no=$transfer_no");
            exit();
        }
    }


    function checkProductStock($master)
    {
        $transfer_from = $master->transfer_from;
        $project_id = getFromSession('project_id');
        $transfer_master_id = $master->id;

        $sql = "SELECT 
                pd.product, 
                p.product_name,
                pd.qty AS transfer_qty,
                COALESCE(s.balance, 0) AS stock_qty 
            FROM " . PENDING_STOCK_TRANSFER_DETAILS_TBL . " pd
            
            LEFT JOIN " . PRODUCT_TBL . " p 
                ON p.product_id = pd.product
                
            LEFT JOIN " . STORE_STOCK_VIEW . " s 
                ON s.product_id = pd.product
                AND s.store_id = '$transfer_from'
                AND s.project_id = '$project_id'
                
            WHERE pd.transfer_id = '$transfer_master_id'";

        $result = mysql_query($sql);

        if (mysql_num_rows($result) > 0) {
            while ($row = mysql_fetch_object($result)) {
                $stock_qty = $row->stock_qty;
                $transfer_qty = $row->transfer_qty;

                if ($stock_qty < $transfer_qty) {
                    $product = $row->product_name;
                    $message = "$product stock is lower than transfer quantity. Present stock is $stock_qty";
                    return [
                        'status' => true,
                        'message' => $message
                    ];
                }
            }
        }

        return ['status' => false];
    }


    function insertStockTransferMaster($master)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comlistApp = new CommonList();
        $project_id = getFromSession('project_id');
        $requestdata = array();

        $transfer_no = $this->createVoucharID();

        $requestdata = getUserDataSet(STOCK_TRANSFER_MASTER_TBL);
        $requestdata['transfer_no'] = $transfer_no;
        $requestdata['project_id'] = getFromSession('project_id');
        $requestdata['transfer_from'] = $master->transfer_from;
        $requestdata['delivery_point'] = $master->delivery_point;
        $requestdata['total_amount'] = $master->total_amount;
        $requestdata['transfer_date'] = $master->transfer_date;
        $requestdata['created_by'] = $master->created_by;
        $requestdata['created_date'] = $master->created_date;
        $requestdata['transfer_to'] = $master->transfer_to;
        $requestdata['delivery_point_to'] = $master->delivery_point_to;
        $requestdata['remark'] = $master->remark;
        $requestdata['product_convert'] = $master->product_convert;;
        $requestdata['narration'] = $master->narration;

        if (!empty($master->job_name)) {
            $requestdata['job_name'] = $master->job_name;
        }
        if (!empty($master->finish_item)) {
            $requestdata['finish_item'] = $master->finish_item;
        }

        $requestdata['approved_status'] = 1;
        $requestdata['approved_by'] = getFromSession('userid');
        $requestdata['approved_time'] = date('Y-m-d h:i:s');

        $info = array();
        $info['table'] = STOCK_TRANSFER_MASTER_TBL;
        $info['data'] = $requestdata;
        //$info['debug']  =  true;
        $res = insert($info);
        if ($res) {
            $transfer_from = $master->transfer_from;
            $transfer_to = $master->delivery_point;
            $transfer_date = $master->transfer_date;

            // ======= AC Transfer for production Cr =================
            $total_amount = $master->total_amount;
            $description = "Out Raw Materials";
            $PurchaseId = $comlistApp->getRMStockId($project_id);
            $fromAccountLedger = $comlistApp->getStoreMapLedgerID($transfer_from, "account_ledger");
            if ($fromAccountLedger) {
                $PurchaseId = $fromAccountLedger;
            }

            if ($PurchaseId) {
                $ACPurchaseAmount = $this->getAccounceBalance($PurchaseId, $project_id);
                $PurchaseBalance = ($ACPurchaseAmount - $total_amount);
                $this->saveAccountJournal($transfer_no, $PurchaseId, "Inventories", $project_id, $description, 0, $total_amount, $PurchaseBalance, 1, $transfer_date);
            }

            // ======= AC WIP Dr =================
            $description = "Transfer Raw Materials in WIP";
            $StockId = $comlistApp->getWPStockId($project_id);
            $toAccountLedger = $comlistApp->getStoreMapLedgerID($transfer_to, "account_ledger");
            if ($toAccountLedger) {
                $StockId = $toAccountLedger;
            }

            if ($StockId) {
                $ACPurchaseAmount = $this->getAccounceBalance($StockId, $project_id);
                $StockBalance = ($ACPurchaseAmount + $total_amount);
                $comlistApp->saveAccJournal($transfer_no, $StockId, "Stock", "Transfer Raw Materials", $project_id, $description, $total_amount, 0, $StockBalance, 0, $transfer_date);
            }

            return $transfer_no;
        } else {
            return 0;
        }
    }

    function insertStockTransferDetails($transfer_no, $master)
    {
        $requestdata = array();
        $arr_catagory_product_id = array();
        $project_id = getFromSession('project_id');
        $userid = getFromSession('userid');
        $transfer_master_id = $master->id;
        $getSql = "SELECT * FROM " . PENDING_STOCK_TRANSFER_DETAILS_TBL . " WHERE transfer_id = '$transfer_master_id'";
        $gres = mysql_query($getSql);

        if (mysql_num_rows($gres) > 0) {
            while ($row = mysql_fetch_object($gres)) {
                $requestdata['transfer_no'] = $transfer_no;
                $requestdata['transfer_from'] = $master->transfer_from;
                $requestdata['delivery_point'] = $master->delivery_point;
                $requestdata['project_id'] = $project_id;
                $requestdata['catagory'] = $row->catagory;
                $requestdata['brand_id'] = $row->brand_id;
                $product_id = $row->product;
                $requestdata['product'] = $product_id;
                $requestdata['m_unit'] = $row->m_unit;
                //$requestdata['details'] 	= $row->details;
                $requestdata['unit_price'] = $row->unit_price;
                $requestdata['qty'] = $row->qty;
                $requestdata['total'] = $row->total;
                $requestdata['transfer_date'] = $master->transfer_date;
                $requestdata['created_by'] = $master->created_by;
                $requestdata['created_date'] = $master->created_date;
                $new_product_id = $row->new_product_id;
                if (!empty($new_product_id)) {
                    $requestdata['new_product_id'] = $new_product_id;
                }

                if (!empty($row->job_name)) {
                    $requestdata['job_name'] = $row->job_name;
                }
                if (!empty($row->finish_item)) {
                    $requestdata['finish_item'] = $row->finish_item;
                }

                $info = array();
                $info['table'] = STOCK_TRANSFER_DETAILS_TBL;
                $info['data'] = $requestdata;

                //$info['debug']  	=  true;
                $res = insert($info);
                if ($res) {
                    $transfer_id = mysql_insert_id();
                    $this->convertProductStockQty($transfer_id, $transfer_no, $requestdata['transfer_from'], $requestdata['delivery_point'], $product_id, $new_product_id, $requestdata['qty'], $requestdata['transfer_date']);
                }
            }
        }

        if ($res) {
            $dsql = "DELETE FROM " . PENDING_STOCK_TRANSFER_DETAILS_TBL . " WHERE transfer_id = '$transfer_master_id'";
            //mysql_query($dsql);
            $d = mysql_query($dsql);

            $msql = "DELETE FROM " . PENDING_STOCK_TRANSFER_MASTER_TBL . " WHERE id = '$transfer_master_id'";
            //mysql_query($msql);
            $m = mysql_query($msql);
        }
    }

    //==== Start Check Missing Stock Transfer ========
    function MismachTransfer($msg = null)
    {
        //ini_set("display_errors","on");
        $data = array();
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $data['depo_list'] = $comListApp->getDeliveryPointList();
        $data['cmd'] = getRequest('cmd');
        require_once(MISMACH_STOCK_TRANSFER_CHECK_FILE);
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        if (getRequest('submit')) {
            $this->SetMismachTransfer();
        }
        return $data[0];
    }

    function SetMismachTransfer()
    {
        $date_from = formatDate(getRequest('date_from'));
        $date_to = formatDate(getRequest('date_to'));
        $msql = "SELECT * FROM " . STOCK_TRANSFER_DETAILS_TBL . " WHERE transfer_date BETWEEN '$date_from' AND '$date_to' ORDER BY transfer_no ASC";
        $MRES = mysql_query($msql);
        $sl = 0;
        $total_transfer_amount = 0;
        $NumRow = mysql_num_rows($MRES);
        if ($NumRow > 0) {
            while ($mrow = mysql_fetch_object($MRES)) {
                $transfer_id = $mrow->transfer_id;
                $transfer_no = $mrow->transfer_no;
                $project_id = $mrow->project_id;
                $transfer_from = $mrow->transfer_from;
                $store_id = $mrow->delivery_point;
                $product_id = $mrow->product;
                $m_unit = $mrow->m_unit;
                $unit_price = $mrow->unit_price;
                $qty = $mrow->qty;
                $total = $mrow->total;
                $transfer_date = $mrow->transfer_date;
                $created_by = $mrow->created_by;
                $created_time = $mrow->created_date;
                $psql = "SELECT * FROM " . STOCK_LEDGER_TBL . " WHERE binary voucher_no ='" . $transfer_no . "' AND project_id='" . $project_id . "' AND product_id ='" . $product_id . "' AND po_no ='" . $transfer_id . "'";
                $pRES = mysql_query($psql);
                if (mysql_num_rows($pRES) == 0) {
                    //echo "<br>Missing ".$product_id."<br>";
                    //===Move Stock===
                    $Pcsql = "SELECT product_type,m_unit,unit_price FROM " . PRODUCT_TBL . " WHERE product_id='$product_id' AND project_id='$project_id'";
                    $Pcrow = mysql_fetch_object(mysql_query($Pcsql));
                    $m_unit = $Pcrow->m_unit;
                    $product_type = $Pcrow->product_type;
                    //===== Cr Stock ======
                    $totalFCR = $this->getTotalCreditStock($product_id, $project_id);
                    $totalFDR = $this->getTotalDebitStock($product_id, $project_id);
                    $TFbalance = ($totalFDR - ($totalFCR + $qty));
                    $this->saveTransferStockJournal($transfer_no, $transfer_id, $project_id, $transfer_from, $product_id, $product_type, "Transfer Stock", $unit_price, $m_unit, 0, $qty, $TFbalance, $transfer_date, $created_by, $created_time);
                    //===== Dr Stock ======
                    $totalFCR = $this->getTotalCreditStock($product_id, $project_id);
                    $totalFDR = $this->getTotalDebitStock($product, $project_id);
                    $TTbalance = (($totalFDR + $qty) - $totalFCR);
                    $this->saveTransferStockJournal($transfer_no, $transfer_id, $project_id, $store_id, $product_id, $product_type, "Received Stock", $unit_price, $m_unit, $qty, 0, $TTbalance, $transfer_date, $created_by, $created_time);
                    $total_transfer_amount += $total;
                    $sl++;
                } // end if numrow

            }// end while
        }// end if numrow

        echo "<br><br>==Successfully Set $sl Missing Transfer==";

    }

    function saveTransferStockJournal($voucher_no, $pvoucher_no, $project_id, $store_id, $product_id, $product_type, $note, $unit_price = NULL, $m_unit, $DR = NULL, $CR = NULL, $balance, $create_date, $created_by, $created_time)
    {

        $sql = "INSERT INTO " . STOCK_LEDGER_TBL . " (voucher_no,po_no,project_id,store_id,product_id,product_type,note,unit_price,m_unit,dr,cr,balance,create_date,created_by,created_time) 
	VALUES('" . $voucher_no . "','" . $pvoucher_no . "','" . $project_id . "','" . $store_id . "','" . $product_id . "','" . $product_type . "','" . $note . "','" . $unit_price . "','" . $m_unit . "','" . $DR . "','" . $CR . "','" . $balance . "','" . $create_date . "','" . $created_by . "','" . $created_time . "')";
        mysql_query($sql);
    }

    //==== End Check Missing Stock Transfer =====

    function loadProductStock($product_id)
    {
        $project_id = getFromSession('project_id');
        $transfer_stock = trim(getRequest('transfer_stock'));

        $Ssql = "SELECT balance FROM " . STORE_STOCK_VIEW . " WHERE product_id = '" . $product_id . "' AND store_id = '$transfer_stock' AND project_id = '$project_id'";
        $Srow = mysql_fetch_object(mysql_query($Ssql));

        $Prosql = "SELECT unit_price,m_unit FROM " . PRODUCT_TBL . " WHERE product_id = '$product_id' AND project_id = '$project_id'";
        $Prorow = mysql_fetch_object(mysql_query($Prosql));

        echo $Srow->balance . "#####" . $Prorow->unit_price . "#####" . $Prorow->m_unit;
    }

    function loadProductStockQty($product_id)
    {
        $project_id = getFromSession('project_id');
        $transfer_stock = trim(getRequest('transfer_stock'));
        $sl = trim(getRequest('sl'));
        $Ssql = "SELECT balance FROM " . STORE_STOCK_VIEW . " WHERE product_id = '" . $product_id . "' AND store_id = '$transfer_stock' AND project_id = '$project_id'";
        $Srow = mysql_fetch_object(mysql_query($Ssql));

        $Prosql = "SELECT unit_price,m_unit FROM " . PRODUCT_TBL . " WHERE product_id = '$product_id' AND project_id = '$project_id'";
        $Prorow = mysql_fetch_object(mysql_query($Prosql));

        echo $Srow->balance . "#####" . $Prorow->unit_price . "#####" . $Prorow->m_unit . "#####" . $sl;
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

    function getTransferMasterInfo($id)
    {
        $project_id = getFromSession('project_id');
        $info = array();
        $info['table'] = STOCK_TRANSFER_MASTER_TBL . " tm
        LEFT JOIN " . DELIVERY_POINT_TBL . " d ON tm.delivery_point = d.delivery_pid
        LEFT JOIN " . PROJECT_TBL . " p ON tm.project_id = p.project_id
        LEFT JOIN " . CURRENCY_TBL . " c ON tm.currency = c.currency_id
        LEFT JOIN " . PRODUCT_TBL . " pr ON pr.product_id = tm.finish_item";
        $info['fields'] = array('tm.transfer_no', 'tm.transfer_from', 'tm.delivery_point', 'd.delivery_point_name', 'd.details', 'p.project_name', 'p.location', 'tm.total_amount', "DATE_FORMAT(tm.transfer_date,'%d %b %y' ) as transfer_date", 'c.curr_symble', 'tm.created_by', 'tm.created_date', 'tm.product_convert', 'tm.narration', 'tm.job_name', 'pr.product_name as finish_item_name');

        $info['where'] = "tm.project_id = '$project_id' AND tm.transfer_no = '$id'";
        $info['groupby'] = array("tm.transfer_no");
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


    function getPendingTransferMasterInfo($id)
    {
        $project_id = getFromSession('project_id');

        $info = array();
        $info['table'] = PENDING_STOCK_TRANSFER_MASTER_TBL . " tm
        LEFT JOIN " . DELIVERY_POINT_TBL . " d ON tm.delivery_point = d.delivery_pid
        LEFT JOIN " . PROJECT_TBL . " p ON tm.project_id = p.project_id
        LEFT JOIN " . CURRENCY_TBL . " c ON tm.currency = c.currency_id
        LEFT JOIN " . PRODUCT_TBL . " pr ON pr.product_id = tm.finish_item";

        $info['fields'] = array(
            'tm.id',
            'tm.transfer_from',
            'tm.delivery_point',
            'd.delivery_point_name',
            'd.details',
            'p.project_name',
            'p.location',
            'tm.total_amount',
            "DATE_FORMAT(tm.transfer_date,'%d %b %y') as transfer_date",
            'tm.transfer_date as transfer_date_raw',
            'tm.currency',
            'c.curr_symble',
            'tm.created_by',
            'tm.created_date',
            'tm.product_convert',
            'tm.narration',
            'tm.job_name',
            'tm.finish_item',
            'pr.product_name as finish_item_name'
        );

        $info['where'] = "tm.project_id = '" . $project_id . "'
                      AND tm.id = '" . $id . "'";

        $info['groupby'] = array("tm.id");

        $res = select($info);

        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }

        return $data[0];
    }


    function getConvertProductList($id)
    {

        $info = array();

        $info['table'] = STOCK_TRANSFER_DETAILS_TBL . ' sd
		                LEFT JOIN ' . PRODUCT_TBL . ' p ON sd.product = p.product_id
		                LEFT JOIN ' . PRODUCT_TBL . ' np ON sd.new_product_id = np.product_id
		                LEFT JOIN ' . CURRENCY_TBL . ' c ON sd.currency = c.currency_id
		                LEFT JOIN ' . BRAND_TBL . ' b ON p.brand_code = b.brand_id';

        $info['fields'] = array(
            'sd.transfer_id',
            'sd.transfer_no',
            'sd.project_id',
            'sd.transfer_from',
            'sd.delivery_point',
            'sd.catagory',
            'sd.brand_id',
            'b.brand_name',
            'sd.product',
            'p.product_name',
            'p.product_desc',
            'p.m_unit',
            'p.product_type',
            'sd.new_product_id',
            'np.product_name AS new_product_name',
            'np.product_desc AS new_product_desc',
            'sd.unit_price',
            'c.curr_symble',
            'sd.qty',
            'sd.total'
        );

        $info['where'] = "sd.transfer_no = '$id'";
        $info['groupby'] = array("sd.transfer_id");
        $info['orderby'] = array("sd.product asc");

        $result = select($info);

        $data = array();
        if (count($result)) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }

        return $data;
    }


    function getProductList($id)
    {

        $info = array();
        $info['table'] = STOCK_TRANSFER_DETAILS_TBL . ' sd,' . PRODUCT_TBL . ' p,' . CURRENCY_TBL . ' c,' . BRAND_TBL . ' b';
        $info['fields'] = array('sd.transfer_id', 'sd.transfer_no', 'sd.project_id', 'sd.transfer_from', 'sd.delivery_point', 'sd.catagory', 'sd.brand_id', 'b.brand_name', 'sd.product',
            'p.product_name', 'p.product_desc', 'p.m_unit', 'p.product_type', 'sd.unit_price', 'c.curr_symble', 'sd.qty', 'sd.total');

        $sql = "sd.product = p.product_id AND p.brand_code = b.brand_id AND sd.currency = c.currency_id AND sd.transfer_no = '$id'";

        $info['where'] = $sql;
        $info['groupby'] = array("sd.transfer_id");
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

    function getPendingProductList($id)
    {
        $info = array();
        $info['table'] = PENDING_STOCK_TRANSFER_DETAILS_TBL . ' sd
        LEFT JOIN ' . PRODUCT_TBL . ' p ON sd.product = p.product_id
        LEFT JOIN ' . PRODUCT_TBL . ' np ON sd.new_product_id = np.product_id
        LEFT JOIN ' . BRAND_TBL . ' b ON p.brand_code = b.brand_id
        LEFT JOIN ' . CURRENCY_TBL . ' c ON sd.currency = c.currency_id';

        $info['fields'] = array(
            'sd.transfer_id',
            'sd.project_id',
            'sd.transfer_from',
            'sd.delivery_point',
            'sd.catagory',
            'sd.brand_id',
            'b.brand_name',
            'sd.product',
            'p.product_name',
            'p.product_desc',
            'p.m_unit',
            'p.product_type',

            'sd.new_product_id',
            'np.product_name as new_product_name',
            'np.product_desc as new_product_desc',
            'np.m_unit as new_product_unit',
            'np.product_type as new_product_type',

            'sd.unit_price',
            'c.curr_symble',
            'sd.qty',
            'sd.total'
        );

        $info['where'] = "sd.transfer_id = '$id'";
        $info['orderby'] = array("sd.id asc");

        //$info['debug'] = true;

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

    function saveStockJournal($voucher_no, $pvoucher_no, $project_id, $store_id, $product_id, $product_type, $note, $unit_price = NULL, $m_unit, $DR = NULL, $CR = NULL, $balance, $create_date = NULL)
    {
        $created_by = getFromSession('userid');
        $sql = "INSERT INTO " . STOCK_LEDGER_TBL . " (voucher_no,po_no,project_id,store_id,product_id,product_type,note,unit_price,m_unit,dr,cr,balance,created_by,create_date) 
	VALUES('" . $voucher_no . "','" . $pvoucher_no . "','" . $project_id . "','" . $store_id . "','" . $product_id . "','" . $product_type . "','" . $note . "','" . $unit_price . "','" . $m_unit . "','" . $DR . "','" . $CR . "','" . $balance . "','" . $created_by . "','" . $create_date . "')";
        mysql_query($sql);
    }

    function createVoucharID()
    {
        $info = array();
        $info['table'] = STOCK_TRANSFER_MASTER_TBL;
        $info['fields'] = array('max(transfer_no) as maxvoucher');
        $res = select($info);
        $maxvoucherId = 'T00000000';
        if (count($res)) {
            foreach ($res as $v) {
                if ($v->maxvoucher) {
                    $maxvoucherId = $v->maxvoucher;
                }
                break;
            }
        }

        $maxvoucherId = generateID("T", $maxvoucherId, 9);
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
