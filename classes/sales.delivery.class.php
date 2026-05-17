<?php

class SalesDelivery
{

    function run()
    {

        $cmd = getRequest('cmd');
        $u_t_id = getFromSession('u_type_id');
        if (($u_t_id == 101) || ($u_t_id == 102) || ($u_t_id == 103) || ($u_t_id == 105)) {
            switch ($cmd) {
                case 'delivery'              :
                    $screen = $this->showEditor("Edit Page");
                    break;
                case 'edit_order'            :
                    $screen = $this->EditOrder();
                    break;
                case 'edit_qty'                :
                    $screen = $this->editUndeliveryQty();
                    break;
                case 'list'                :
                    $screen = $this->showEditor($msg);
                    break;
                default                      :
                    $cmd = 'list';
                    $screen = $this->showEditor($msg);
                    break;
            }
        } elseif (($u_t_id == 107)) {
            switch ($cmd) {
                case 'delivery'              :
                    $screen = $this->showEditor("Edit Page");
                    break;
                case 'edit_order'            :
                    $screen = $this->EditOrder();
                    break;
                case 'edit_qty'                :
                    $screen = $this->editUndeliveryQty();
                    break;
                case 'list'                :
                    $screen = $this->showEditor($msg);
                    break;
                default                      :
                    $cmd = 'list';
                    $screen = $this->showEditor($msg);
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

    function editUndeliveryQty()
    {
        $detail_id = getRequest('sal_detail_id');
        $qty = getRequest('qty');
        $undelivery_qty = getRequest('undelivery_qty');
        $total = getRequest('total');
        $details = getRequest('details_text');
        $details = mysql_real_escape_string($details);
        $gross_weight = getRequest('gross_weight');
        $net_weight = getRequest('net_weight');
        $vat_amount = getRequest('vat_amount');
        $discount_amount = getRequest('product_discount');

        $EditSD = "UPDATE " . SALES_DETAILS_TBL . " SET qty='$qty', undelivery_qty='$undelivery_qty', total='$total',discount_amount='$discount_amount',vat_amount='$vat_amount',details='$details',gross_weight='$gross_weight',net_weight='$net_weight' WHERE sal_detail_id='" . $detail_id . "'";
        $res = mysql_query($EditSD);
    }

    function EditOrder()
    {
        $voucher_no = getRequest('voucher_no');
        $total_value = getRequest('total_value');
        $net_payble = getRequest('net_payble');
        $product_discount = getRequest('product_discount');
        $general_discount = getRequest('general_discount');
        $exclusive_discount = getRequest('exclusive_discount');
        $additional_discount = getRequest('additional_discount');
        $total_discount = getRequest('total_discount');
        $project_id = getFromSession('project_id');

        $additional_cost = getRequest('additional_cost');
        $vat_type = getRequest('vat_type');
        $additional_vat_percent = getRequest('vat_percent');
        $additional_vat_amount = getRequest('vat_amount');

        $delivery_date = formatDate(getRequest('delivery_date'));
        $aging_date = formatDate(getRequest('aging_date'));

        $description = getRequest('terms');
        $vat_no = getRequest('vat_no');
        $sms_text = getRequest('sms_text');
        $vehicle_no = getRequest('vehicle_no');
        $driver_name = getRequest('driver_name');
        $contact_person = getRequest('contact_person');
        $delivery_address = getRequest('delivery_address');
        $ref_voucher = getRequest('ref_voucher');

        $EditSM = "UPDATE " . SALES_MASTER_TBL . " SET total_value='$total_value',general_discount_amount='$general_discount',delivery_date='$delivery_date',aging_date='$aging_date',exclusive_discount_amount='$exclusive_discount',additional_discount='$additional_discount',product_discount='$product_discount',discount='$total_discount',net_payble='$net_payble',additional_cost='$additional_cost',vat_type='$vat_type',additional_vat_percent='$additional_vat_percent',description='$description',vat_no='$vat_no',vehicle_no='$vehicle_no',driver_name='$driver_name',contact_person='$contact_person',delivery_address='$delivery_address',wo_no='$ref_voucher',ref_voucher='$ref_voucher',additional_vat_amount='$additional_vat_amount' WHERE voucher_no='" . $voucher_no . "' AND project_id = '$project_id'";
        mysql_query($EditSM);
        echo $voucher_no;
    }

    function showEditor($msg = null)
    {
        require_once(CLASS_DIR . '/sales.class.php');
        $salesApp = new Sales();
        require_once(CLASS_DIR . '/common.list.class.php');
        $comlistApp = new CommonList();

        if (getRequest('submit')) {
            $voucher_no = getRequest("voucher_no");
            $project_id = getFromSession('project_id');
            $DcSql = "SELECT * FROM " . SALES_DELIVERY_MASTER_TBL . " WHERE voucher_no='$voucher_no' AND project_id='$project_id'";
            $dcres = mysql_query($DcSql);
            if (mysql_num_rows($dcres) == 0) {
                $this->saveDeliveryChallan();
            } else {
                header("location:index.php?app=sales.delivery&cmd=delivery&voucher_no=" . getRequest("voucher_no") . "&msg=Double delivery cannot be processed");
            }
        } else {
            $voucher_no = getRequest('voucher_no');
            $data['cmd'] = getRequest('cmd');
            $advArr = $salesApp->getSalesMasterInfo($voucher_no);
            $advArr = parseThisValue($advArr);
            $data = array_merge($advArr);
            $data['item_list'] = $salesApp->getProductList($voucher_no);
            $data['retailer_list'] = $comlistApp->getRetailerList();

            $customer_id = $data['customer'];
            $wo_no = $data['wo_no'];
            $order_date = $data['order_date'];
            $created_date = $data['created_date'];

            $product_ids = [];

            foreach ($data['item_list'] as $item) {
                $product_ids[] = $item->product;
            }

            $product_ids_string = "'" . implode("','", $product_ids) . "'";

            $wo_sql = "SELECT pm.wo_no,pm.ref_voucher,pm.voucher_no,pm.sales_date,pm.created_date,c.sub_head_name as customer, p.product_name as product FROM " . SALES_MASTER_TBL . " pm
		JOIN " . SALES_DETAILS_TBL . " sd ON pm.voucher_no = sd.voucher_no
		LEFT JOIN " . SUB_ACC_HEAD_TBL . " c ON c.sub_id = pm.customer
		LEFT JOIN " . PRODUCT_TBL . " p ON p.product_id = sd.product
		WHERE pm.customer = '$customer_id'
		AND sd.product IN ($product_ids_string)
		AND (
		   pm.sales_date < '$order_date'
		   OR (pm.sales_date = '$order_date' AND pm.created_date < '$created_date')
		)
		AND pm.item_delivery_amount = 0
		AND pm.status = 1
		AND pm.is_deleted = 0
		GROUP BY pm.wo_no, pm.voucher_no
		ORDER BY pm.created_date ASC
		";

            $res = mysql_query($wo_sql);
            $older_vouchers = [];
            $older_found = false;
            while ($row = mysql_fetch_object($res)) {
                $older_found = true;
                $older_vouchers[] = $row;
            }

            if (isset($data['is_deleted']) && $data['is_deleted'] == 1) {
                $older_found = false;
            }

            $data['old_wo_status'] = false; //$older_found;
            $data['old_wo_list'] = $older_vouchers;

            $data['credit_days'] = 0;
            $checkSql = "SELECT overdue_invoice,customer_type,credit_days FROM " . SUB_ACC_HEAD_TBL . " WHERE sub_id = '$customer_id' LIMIT 1";

            $checkRow = mysql_fetch_object(mysql_query($checkSql));
            if (isset($checkRow->credit_days) && $checkRow->credit_days > 0) {
                $credit_days = (int)$checkRow->credit_days;
                $customer_type = $checkRow->customer_type;
                if (isset($customer_type) && $customer_type == 'Credit') {
                    $data['credit_days'] = $credit_days;
                }
            }

        }
        require_once(CURRENT_APP_SKIN_FILE);
        return $data[0];
    }

    function saveDeliveryChallan()
    {
        mysql_query("START TRANSACTION;");
        $delivery_master_id = $this->insertDeliveryChallanMaster(getRequest("voucher_no"));
        if ($delivery_master_id > 0) {
            $this->insertDeliveryChallanDetails(getRequest("voucher_no"), $delivery_master_id);
        } else {
            $SQL1 = "DELETE FROM " . SALES_DELIVERY_MASTER_TBL . " WHERE `voucher_no` ='" . getRequest("voucher_no") . "' AND sales_delivery_master_id=0";
            mysql_query($SQL1);
            $SQL2 = "UPDATE " . SALES_DETAILS_TBL . " SET delivery_qty='0' WHERE voucher_no='" . getRequest("voucher_no") . "'";
            mysql_query($SQL2);
            header("location:index.php?app=sales.delivery&cmd=delivery&voucher_no=" . getRequest("voucher_no") . "&msg=Please Try again");
        }
    }

    function insertDeliveryChallanMaster($voucher_no)
    {
        $requestdata = array();
        $requestdata = getUserDataSet(SALES_DELIVERY_MASTER_TBL);
        $requestdata['delivery_date'] = formatDate(getRequest('delivery_date'));
        $requestdata['total_value'] = getRequest('total_delivery_amount');
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

    function insertDeliveryChallanDetails($voucher_no, $dm_id)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comlistApp = new CommonList();
        require_once(CLASS_DIR . '/sales_order.class.php');
        $soApp = new SalesOrder();

        $customer = getRequest('customer');
        $voucher_no = getRequest('voucher_no');
        $delivery_point = getRequest('delivery_point');
        $store_id = getRequest('delivery_point');
        $discount = getRequest('discount');
        $orderValue = getRequest('total_sales_price');
        $overall_discount = (($discount / $orderValue) * 100);
        $consignee = getRequest('consignee');
        $totalfields = getRequest('ttlfields');
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
        $j = 1;
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

        for ($j; $j <= $totalfields; $j++) {
            $sales_details_id = getRequest("details_id$j");
            $catagory = getRequest("catagory$j");
            $brand_id = getRequest("brand_id$j");
            $product = getRequest("product$j");
            $m_unit = getRequest("m_unit$j");
            $stock_qty = getRequest("stock_qty$j");
            $order_qty = getRequest("order_qty$j");
            $delivery_qty = getRequest("delivery_qty$j");
            $undelivery_qty = getRequest("undelivery_qty$j");
            if ($undelivery_qty > 0) {
                if (!$newVoucher) {
                    $newVoucher = $this->createSalesVoucharID();
                }

                $detailsResult = $this->insertSalesItems($newVoucher, $voucher_no, $sales_details_id, $undelivery_qty);
                $detailsTotal += $detailsResult['total'];
                $detailsProductTotal += $detailsResult['product_discount'];
                $sales_details[] = $detailsResult['sales_details'];
            }
            $freeQty = getRequest("free_qty$j");
            $Prvfree_qty = getRequest("Prvfree_qty$j");
            $sales_price = getRequest("unit_price$j");

            $SDUSql = "UPDATE " . SALES_DETAILS_TBL . " SET is_order_complete='1' WHERE voucher_no='$voucher_no' AND sal_detail_id='$sales_details_id'";
            mysql_query($SDUSql);

            $totalOrderQty += $order_qty;
            $discount_per_qty = ($overall_discount / $order_qty);
            $discount_amount = getRequest("discount_amount$j");
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
                        $totalCR = $this->getTotalCreditStock($product, getFromSession('project_id'));
                        $totalDR = $this->getTotalDebitStock($product, getFromSession('project_id'));
                        $balance = ($totalDR - ($totalCR + $UDQty));
                        if ($product_type == "Sales Item" || $product_type == "Raw Materials" || $product_type == "Invetory Item" || $product_type == "Equipment") {
                            if ($overall_discount_amount > 0) {
                                $netSalesPrice = ($sales_price - $overall_discount_amount);
                            } else {
                                $netSalesPrice = $sales_price;
                            }
                            $note = "Sales Delivery";
                            $this->saveStockJournal($voucher_no, $voucher_no, $project_id, $store_id, $product, $serial, $warranty, $note, $netSalesPrice, $m_unit, 0, $UDQty, $balance, $delivery_date, $dm_id);
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
                    $consignee = getRequest('consignee');
                    // === Save Sales Delivery Qty ===	
                    $this->saveDeliveryChallanDtl($sd_id, $dm_id, $voucher_no, $delivery_point, $consignee, $project_id, $catagory, $brand_id, $product, $serial, $warranty, $m_unit, $sales_price, $discount_per_qty, $discount_amount, $overall_discount, $overall_discount_amount, $unit_profit, $delivery_qty, $freeQty, $deliveryAmount, $division, $district, $area, $created_by);
                    $this->saveSalesItems($voucher_no, $product, $purchase_price, $unit_profit, $delivery_qty, $freeQty, $sales_details_id);
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
            $this->salesMaster($newVoucher, $voucher_no, $detailsTotal, $detailsProductTotal, $sales_details);
        }

        $totalDeliveryAmount = $this->getTotalDeliveryAmount($dm_id, $voucher_no, $project_id);
        $netOrderAmount = getRequest('netOrderAmount');
        $totalDeliveryAmount = number_format($totalDeliveryAmount, 2, '.', '');
        $netOrderAmount = number_format($netOrderAmount, 2, '.', '');
        $epsilon = 0.00000;

        if ((abs($totalDeliveryAmount - $netOrderAmount) != $epsilon) && (intval($totalDeliveryQty) == intval($totalOrderQty))) {
            if (intval($totalDeliveryAmount) >= intval($netOrderAmount)) {
                $diffAmount = ($totalDeliveryAmount - $netOrderAmount);
            } else {
                $diffAmount = ($netOrderAmount - $totalDeliveryAmount);
            }
            $product_discount = (getRequest('discount') + $diffAmount);
            $updateSM = "UPDATE " . SALES_MASTER_TBL . " SET product_discount='$product_discount' WHERE voucher_no='" . $voucher_no . "' AND project_id = '$project_id'";
            mysql_query($updateSM);
            $totalDeliveryAmount = $netOrderAmount;
        }


        if ((abs($totalDeliveryAmount - $netOrderAmount) == $epsilon) && (intval($totalDeliveryQty) == intval($totalOrderQty))) {
            $created_date = $delivery_date;
            //======= Party Dr ======		
            $DrAmount1 = $totalDeliveryAmount;
            $PartyAcc_head = getRequest('customer');
            $description = "Sales Delivery";
            $totalPartyCR = $soApp->getTotalCreditAmount($PartyAcc_head, getFromSession('project_id'));
            $totalPartyDR = $soApp->getTotalDebitAmount($PartyAcc_head, getFromSession('project_id'));
            $PreviousPartyBalance = ($totalPartyDR - $totalPartyCR);
            $PartyBalance = (($totalPartyDR + $DrAmount1) - $totalPartyCR);
            $comlistApp->saveAccJournal($voucher_no, $PartyAcc_head, "Customer", "Buy Product", getFromSession('project_id'), $description, $DrAmount1, 0, $PartyBalance, 0, $created_date, $dm_id);

            $consignee = getRequest('consignee');
            if ($consignee != "") {
                $totalPartyCR = $soApp->getTotalCreditAmount($consignee, getFromSession('project_id'));
                $totalPartyDR = $soApp->getTotalDebitAmount($consignee, getFromSession('project_id'));
                $PreviousPartyBalance = ($totalPartyDR - $totalPartyCR);
                $PartyBalance = (($totalPartyDR + $DrAmount1) - $totalPartyCR);
                $this->saveRetailerJournal($voucher_no, $consignee, "Retailer", "Buy Product", getFromSession('project_id'), $description, $DrAmount1, 0, $PartyBalance, 0, $created_date, $dm_id);
            }
            //======= Start Cost Center Ledger ========
            $COGSId = $comlistApp->getCOGSAccounceId(getFromSession('project_id'));
            $description = "cost of goods sold";
            //======= AC Recievable Dr ======
            if ($COGSId) {
                $COGSAmount = $this->getAccounceBalance($COGSId, $project_id);
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
                $ACSalesAmount = $this->getAccounceBalance($ACSalesId, $project_id);
                $SalesBalance = ($ACSalesAmount - $DrAmount1);
                $comlistApp->saveAccJournal($voucher_no, $ACSalesId, "Sales", "Sales", $project_id, $description, 0, $DrAmount1, $SalesBalance, 0, $created_date, $dm_id);
            }

            //Vat Journal
            $CRvatAmount = getRequest('vatAmount');
            $VATonSalesID = $comlistApp->getStoreMapLedgerID($store_id, "vat_payable");

            if ($CRvatAmount > 0 && $VATonSalesID) {
                $totalVatCR = $this->getTotalCreditAmount($VATonSalesID, getFromSession('project_id'));
                $totalVatDR = $this->getTotalDebitAmount($VATonSalesID, getFromSession('project_id'));
                $VATBalance = ($totalVatDR - ($totalVatCR + $CRvatAmount));
                $description = "VAT of goods sold";
                $comlistApp->saveAccJournal($voucher_no, $VATonSalesID, "Sales", "Sales VAT", $project_id, $description, 0, $CRvatAmount, $VATBalance, 0, $created_date, $dm_id);
            }
            // ====== End Accounts Ledger ========

            //=======Update Sales Master =====

            $PMsql = "SELECT voucher_no,discount,net_payble,paid_amount,due,item_delivery_amount,service_charge FROM " . SALES_MASTER_TBL . " 
	 WHERE voucher_no ='" . $voucher_no . "' AND project_id = '$project_id'";
            $PMrow = mysql_fetch_object(mysql_query($PMsql));
            $sales_discount = $PMrow->discount;
            $total_received_amount = $PMrow->paid_amount;
            $existing_due = $PMrow->due;
            $item_delivery_amount = $PMrow->item_delivery_amount;
            $total_delivery_amount = ($totalDeliveryAmount + $item_delivery_amount);

            if ($PreviousPartyBalance < 0) {
                $actual_delivery_amount = $this->adjustCustomerPayble($voucher_no, getRequest('customer'), $totalDeliveryAmount, $dm_id);
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

            $aging_date = formatDate(getRequest('aging_date'));


            $SMUpdate = "UPDATE " . SALES_MASTER_TBL . " SET net_payble='$total_delivery_amount',due='$present_due',aging_date='$aging_date',item_delivery_amount='$total_delivery_amount',
	 adjust='$adjustAmount',next_voucher='$newVoucher',is_deleted=0 WHERE voucher_no='" . $voucher_no . "' AND project_id = '$project_id'";
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
                    $DiscountCR = $this->getTotalCreditAmount($DiscountId, $project_id);
                    $DiscountDR = $this->getTotalDebitAmount($DiscountId, $project_id);
                    $DiscountBalance = (($DiscountDR + $TotalDiscount) - $DiscountCR);
                    $comlistApp->saveAccJournal($voucher_no, $DiscountId, "Sales", "Sales Discount", $project_id, $description, $TotalDiscount, 0, $DiscountBalance, 0, $delivery_date, $dm_id);
                }

            }
            $this->updateSalesVoucher($voucher_no, $total_delivery_amount);
            $customer = getRequest('customer');

            //$this->saveLoss($voucher_no,$dm_id,$totalLoss,$created_date);
            //$this->saveProfit($voucher_no,$dm_id,$totalProfit,$created_date);

            //==== Stock Cr =====
            if ($TotalStockAmount > 0) {
                $StockId = $comlistApp->getFGStockId(getFromSession('project_id'));
                if ($StockId) {
                    $totalStockCr = $this->getTotalCreditAmount($StockId, getFromSession('project_id'));
                    $totalStockDr = $this->getTotalDebitAmount($StockId, getFromSession('project_id'));
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
                    $totalfreeItemCR = $this->getTotalCreditAmount($freeItemhead, $project_id);
                    $totalfreeItemDR = $this->getTotalDebitAmount($freeItemhead, $project_id);
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

        } elseif (intval($totalDeliveryAmount) != intval($TotalNetReceivable)) {
            //=======Update Sales Master =====	
            mysql_query("ROLLBACK;");
            $Stsql = "DELETE FROM " . STOCK_LEDGER_TBL . " WHERE voucher_no='" . $voucher_no . "' AND project_id='" . $project_id . "' AND delivery_id='$dm_id'";
            mysql_query($Stsql);
            $Sditsql = "DELETE FROM " . SALES_DELIVERY_CHALLAN_TBL . " WHERE voucher_no='" . $voucher_no . "' AND project_id='" . $project_id . "' 
	AND delivery_master_id='$dm_id'";
            mysql_query($Sditsql);
            $Sdmtsql = "DELETE FROM " . SALES_DELIVERY_MASTER_TBL . " WHERE voucher_no='" . $voucher_no . "' AND project_id='" . $project_id . "' 
	AND sales_delivery_master_id='$dm_id'";
            mysql_query($Sdmtsql);
            header("location:index.php?app=sales.delivery&cmd=delivery&voucher_no=" . $voucher_no . "&msg=Delivery Amount($totalDeliveryAmount) is not equal to Order Amount($netOrderAmount) !!! Please Try again");
        }

    } //End of the function savePaymentDetails()

    function saveSalesItems($voucher_no, $product, $purchase_price, $unit_profit, $delivery_qty, $free_qty, $sales_details_id)
    {
        $sduSql = "UPDATE " . SALES_DETAILS_TBL . " SET purchase_price='$purchase_price',unit_profit='$unit_profit',delivery_qty='" . $delivery_qty . "',free_qty='$free_qty' WHERE voucher_no='" . $voucher_no . "'";
        $sduSql .= " AND  product='$product' AND sal_detail_id='$sales_details_id'";
        mysql_query($sduSql);
    }

    /*  
    function sendSMS($sender,$recipients,$message){	
      $token = SMS_TOKEN;
      $url = 'https://24bulksms.com/24bulksms/api/api-sms-send';
      $data= array(
      'sender_id'=>"203",
      'apiKey'=>"$token",
      'mobileNo'=>"$recipients",
      'message'=>"$message"
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
  
    */

    function sendSMS($sender, $recipients, $message)
    {
        $token = SMS_TOKEN;
        $url = "http://api.greenweb.com.bd/api.php";
        $data = array(
            'to' => "$recipients",
            'message' => "$message",
            'token' => "$token"
        ); // Add parameters in key value
        $ch = curl_init(); // Initialize cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $smsresult = curl_exec($ch);
        //Result
        //echo $smsresult;
        //Error Display
        //echo curl_error($ch);
    }/*
  function sendSMS($sender,$recipients,$message){
	$postUrl = "http://193.105.74.59/api/sendsms/xml";
	
	// XML-formatted data
	$xmlString =
	"<SMS>
	<authentification>
		<username>parksoft</username>
		<password>Imran0088</password>
	</authentification>
	<message>
		<sender>$sender</sender>
		<text>$message</text>
	</message>
	<recipients>
	<gsm>$recipients</gsm>
	</recipients>
	</SMS>";
	
	// previously formatted XML data becomes value of "XML" POST variable
	$fields = "XML=" . urlencode($xmlString);
	
	// in this example, POST request was made using PHP's CURL
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $postUrl);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	
	// response of the POST request
	$response = curl_exec($ch);
	curl_close($ch);  
  }
  */
    function saveRetailerJournal($voucher_no, $sub_id, $head_type, $transaction_type, $project_id, $description, $DR = NULL, $CR = NULL, $balance, $status, $created_date, $delivery_id = NULL)
    {
        $head_type = "Retailer";
        $created_by = getFromSession('userid');
        if ($delivery_id == "") {
            $delivery_id = 0;
        }
        $sql = "INSERT INTO " . ACCOUNT_JOURNAL_TBL . " (voucher_no,delivery_id,created_date,sub_id,head_type,transaction_type,project_id,description,dr,cr,balance,status,created_by)
	 VALUES('" . $voucher_no . "','" . $delivery_id . "','" . $created_date . "','" . $sub_id . "','" . $head_type . "','" . $transaction_type . "','" . $project_id . "','" . $description . "','" . $DR . "','" . $CR . "','" . $balance . "','" . $status . "','" . $created_by . "')";
        mysql_query($sql);
    }

    function updateSalesVoucher($voucher_no, $totalDeliveryAmount)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comlistApp = new CommonList();
        $project_id = getFromSession('project_id');
        $dvsql = "SELECT * FROM " . DEVIT_VOUCHAR_TBL . " WHERE voucher_no = '$voucher_no' ";
        $dvres = mysql_query($dvsql);
        if (mysql_num_rows($dvres) > 0) {
            $DrVUpdate = "UPDATE " . DEVIT_VOUCHAR_TBL . " SET debit='$totalDeliveryAmount' WHERE voucher_no='" . $voucher_no . "' AND project_id = '$project_id'";
            mysql_query($DrVUpdate);
            $CrVUpdate = "UPDATE " . CREDIT_VOUCHAR_TBL . " SET credit='$totalDeliveryAmount' WHERE voucher_no='" . $voucher_no . "' AND project_id = '$project_id'";
            mysql_query($CrVUpdate);
        } else {
            $smsql = "SELECT * FROM " . SALES_MASTER_TBL . " WHERE voucher_no = '$voucher_no'";
            $smres = mysql_query($smsql);
            $smrow = mysql_fetch_object($smres);
            $branch_id = getFromSession('branch_id');
            $account_head = $smrow->customer;
            $created_date = $smrow->sales_date;
            $mode_of_payment = "Recievable";
            $transaction_name = "Sales Order";
            $debit = $smrow->net_payble;
            $headtype = "Customer";
            $transaction_type = "Sales Order";
            $vouchar_type = "Sales Order";
            $description = "";
            $created_by = getFromSession('userid');

            $DV = "INSERT INTO " . DEVIT_VOUCHAR_TBL . " (voucher_no,account_head,project_id,branch_id,head_type,mode_of_payment,transaction_type,
	vouchar_type,transaction_name,debit,description,created_by,created_date) VALUES('$voucher_no','$account_head','$project_id','$branch_id',
	'$headtype','$mode_of_payment','$transaction_type','$vouchar_type','$transaction_name','$debit','$description','$created_by','$created_date')";
            mysql_query($DV);

            $account_head = $comlistApp->getRecievableId(getFromSession('project_id'));
            $head_type = "Acc";
            $credit = $debit;
            $debit = 0;
            $vouchar_type = "Others Vouchar";
            $CV = "INSERT INTO " . CREDIT_VOUCHAR_TBL . " (voucher_no,account_head,head_type,project_id,branch_id,mode_of_payment,transaction_type,
	vouchar_type,transaction_name,credit,debit,description,created_by,created_date) VALUES('$voucher_no','$account_head','$head_type',
	'$project_id','$branch_id','$mode_of_payment','$transaction_type','$vouchar_type','$transaction_name','$credit','$debit',
	'$description','$created_by','$created_date')";
            mysql_query($CV);
        }
    }

    function adjustCustomerPayble($NewVoucherNo, $account_head, $CrAmount, $delivery_id)
    {
        $project_id = getFromSession('project_id');
        require_once(CLASS_DIR . '/common.list.class.php');
        $clistApp = new CommonList();
        //===== for Opening Balance ========
        if ($CrAmount > 0) {
            $rsql = "SELECT dr.voucher_no,cr.credit as debit,dr.paid_amount,dr.due FROM " . CREDIT_VOUCHAR_TBL . " as cr," . DEVIT_VOUCHAR_TBL . " as dr 
	WHERE dr.voucher_no=cr.voucher_no AND cr.account_head='" . $account_head . "' AND cr.vouchar_type='Payable Vouchar' AND dr.due >0 AND dr.status=0";
            $rres = mysql_query($rsql);
            while ($srow = mysql_fetch_object($rres)) {
                $voucher_no = $srow->voucher_no;
                if ($CrAmount >= $srow->due && $srow->due > 0) {
                    $CrAmount = ($CrAmount - $srow->due);
                    $adjustAmount = $srow->due;
                    $totalPaidAmount = ($srow->paid_amount + $srow->due);
                    if ($totalPaidAmount == $srow->debit) {
                        $pusql = "UPDATE " . DEVIT_VOUCHAR_TBL . " SET paid_amount='" . $totalPaidAmount . "',due='0',`status`=1 WHERE voucher_no='$voucher_no'";
                        mysql_query($pusql);
                        $clistApp->saveInvoiceAdjustHistory($NewVoucherNo, $delivery_id, $project_id, DEVIT_VOUCHAR_TBL, $voucher_no, $adjustAmount, "-");
                    }
                } elseif (($CrAmount < $srow->due) && ($srow->due > 0 && $CrAmount > 0)) {
                    $presentDue = ($srow->due - $CrAmount);
                    $PaidAmount = ($srow->paid_amount + $CrAmount);
                    if ($PaidAmount < $srow->debit) {
                        $adjustAmount = $CrAmount;
                        $CrAmount = 0;
                        $pusql2 = "UPDATE " . DEVIT_VOUCHAR_TBL . " SET paid_amount='" . $PaidAmount . "',due='$presentDue',`status`=0 WHERE voucher_no='$voucher_no'";
                        mysql_query($pusql2);
                        $clistApp->saveInvoiceAdjustHistory($NewVoucherNo, $delivery_id, $project_id, DEVIT_VOUCHAR_TBL, $voucher_no, $adjustAmount, "-");
                    }
                    break;
                }
            }// end while
        } //============End CrAmount >0 ===========

        //=======Customer can be Payble for his Sales Return, Beddebs, Adv Paid ======= 
        if ($CrAmount > 0) {
            $SRPSql = "SELECT return_id,customer_id,return_amount,paid_amount,due FROM " . SALES_RETURN_PAYBLE_TBL . " WHERE customer_id ='" . $account_head . "' 
	 AND project_id = '$project_id' AND paid_amount < return_amount AND due >0  ORDER BY return_id ASC"; // AND fyear='$fyear'
            $SRPRes = mysql_query($SRPSql);
            while ($srprow = mysql_fetch_object($SRPRes)) {
                $return_id = $srprow->return_id;
                $net_payble = $srprow->return_amount;
                $paid_amount = $srprow->paid_amount;
                $existing_due = $srprow->due;
                if (($CrAmount >= $existing_due)) {
                    $CrAmount = $CrAmount - $existing_due;
                    if ($existing_due > 0) {
                        $total_paid = ($paid_amount + $existing_due);
                        $SRUpSql = "UPDATE " . SALES_RETURN_PAYBLE_TBL . " SET paid_amount=$total_paid, due=0  WHERE return_id ='$return_id' AND project_id = '$project_id'";
                        mysql_query($SRUpSql);
                        $clistApp->saveInvoiceAdjustHistory($NewVoucherNo, $delivery_id, $project_id, SALES_RETURN_PAYBLE_TBL, $return_id, $existing_due, "-");
                    }
                } elseif (($CrAmount < $existing_due)) {
                    if ($existing_due > 0 && $CrAmount > 0) {
                        $totalpaid = ($paid_amount + $CrAmount);
                        $present_due = ($existing_due - $CrAmount);
                        $adjustAmount = $CrAmount;
                        $CrAmount = 0;
                        $SRPUpdate = "UPDATE " . SALES_RETURN_PAYBLE_TBL . " SET paid_amount=$totalpaid,due=$present_due WHERE return_id='$return_id' AND project_id='$project_id'";
                        mysql_query($SRPUpdate);
                        $clistApp->saveInvoiceAdjustHistory($NewVoucherNo, $delivery_id, $project_id, SALES_RETURN_PAYBLE_TBL, $return_id, $adjustAmount, "-");
                    }
                    break;
                }
            } // end while
        } // end $CrAmount>0
        //====== Make Customer Receibavle if Delivery Amount is greater then his Payble ======
        if ($CrAmount > 0) {
            return $CrAmount;
        } else {
            return 0;
        }
    }


    function getTotalDeliveryAmount($delivery_mid, $voucher_no, $project_id)
    {
        $SDCSql = "SELECT SUM(total_amount) as delivery_Amount FROM " . SALES_DELIVERY_CHALLAN_TBL . " WHERE delivery_master_id ='" . $delivery_mid . "' AND project_id = '$project_id' AND 
   voucher_no='$voucher_no'";
        $SDCRes = mysql_query($SDCSql);
        $srprow = mysql_fetch_object($SDCRes);
        if ($srprow->delivery_Amount > 0) {
            return $srprow->delivery_Amount;
        } else {
            return 0;
        }
    }

    function saveDeliveryChallanDtl($sd_id, $dm_id, $voucher_no, $delivery_point, $consignee, $project_id, $catagory, $brand_id, $product, $serial, $warranty, $m_unit, $unit_price, $discount_per_qty, $discount_amount, $overall_discount, $overall_discount_amount, $unit_profit, $delivery_qty, $free_qty, $total_amount, $division, $district, $area, $created_by)
    {
        $sql = "INSERT INTO " . SALES_DELIVERY_CHALLAN_TBL . " (delivery_master_id,voucher_no,sal_detail_id,delivery_point,consignee,project_id,catagory,brand_id,product,serial,warranty,m_unit,unit_price,discount_per_qty,discount_amount,overall_discount,overall_discount_amount,unit_profit,delivery_qty,total_bag,total_amount,division,district,area,created_by) 
	VALUES('" . $dm_id . "','" . $voucher_no . "','" . $sd_id . "','" . $delivery_point . "','" . $consignee . "','" . $project_id . "','" . $catagory . "','" . $brand_id . "','" . $product . "','" . $serial . "','" . $warranty . "','" . $m_unit . "','" . $unit_price . "','" . $discount_per_qty . "','" . $discount_amount . "','" . $overall_discount . "','" . $overall_discount_amount . "','" . $unit_profit . "','" . $delivery_qty . "','" . $free_qty . "','" . $total_amount . "','" . $division . "','" . $district . "','" . $area . "','" . $created_by . "')";
        mysql_query($sql);
    }

    function saveLoss($voucher_no, $dm_id, $TotalLoss, $created_date)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comlistApp = new CommonList();
        $SalesIncomeId = $comlistApp->getProductSalesIncomeId(getFromSession('project_id'));
        if ($TotalLoss > 0 && $SalesIncomeId != "") {
            //========= Direct Income Cr ==========	
            $totalSalesIncomeCR = $this->getTotalCreditAmount($SalesIncomeId, getFromSession('project_id'));
            $totalSalesIncomeDR = $this->getTotalDebitAmount($SalesIncomeId, getFromSession('project_id'));
            $SalesIncomeBalance = ($totalSalesIncomeDR - ($totalSalesIncomeCR + $TotalLoss));
            $salesDtl = "Loss from sales delivery no- $dm_id";
            $comlistApp->saveAccJournal($voucher_no, $SalesIncomeId, "Acc", "Direct Loss", getFromSession('project_id'), $salesDtl, 0, $TotalLoss, $SalesIncomeBalance, 0, $created_date, $dm_id);
        }
    }

    function saveProfit($voucher_no, $dm_id, $TotalProfit, $created_date)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comlistApp = new CommonList();
        $SalesIncomeId = $comlistApp->getProductSalesIncomeId(getFromSession('project_id'));
        if ($TotalProfit > 0 && $SalesIncomeId != "") {
            //========= Direct Income Dr ==========	
            $totalSalesIncomeCR = $this->getTotalCreditAmount($SalesIncomeId, getFromSession('project_id'));
            $totalSalesIncomeDR = $this->getTotalDebitAmount($SalesIncomeId, getFromSession('project_id'));
            $SalesIncomeBalance = (($totalSalesIncomeDR + $TotalProfit) - $totalSalesIncomeCR);
            $salesDtl = "Income from sales delivery no- $dm_id";
            $comlistApp->saveAccJournal($voucher_no, $SalesIncomeId, "Acc", "Direct Incomes", getFromSession('project_id'), $salesDtl, $TotalProfit, 0, $SalesIncomeBalance, 0, $created_date, $dm_id);
        }
    }

    //========= make_seed function 4 gatepass ========
    function make_seed()
    {
        list($usec, $sec) = explode(' ', microtime());
        return (float)$sec + ((float)$usec * 100000);
    }

    function saveStockJournal($pvoucher_no, $voucher_no, $project_id, $store_id, $product_id, $serial = NULL, $warranty = NULL, $note, $unit_price = NULL, $m_unit, $DR = NULL, $CR = NULL, $balance, $create_date = NULL, $sdmid = NULL)
    {
        $created_by = getFromSession('userid');
        $sql = "INSERT INTO " . STOCK_LEDGER_TBL . " (po_no,voucher_no,project_id,store_id,delivery_id,product_id,serial,warranty,note,unit_price,m_unit,dr,cr,balance,created_by,create_date) 
	VALUES('" . $pvoucher_no . "','" . $voucher_no . "','" . $project_id . "','" . $store_id . "','" . $sdmid . "','" . $product_id . "','" . $serial . "','" . $warranty . "','" . $note . "','" . $unit_price . "','" . $m_unit . "','" . $DR . "','" . $CR . "','" . $balance . "','" . $created_by . "','" . $create_date . "')";
        mysql_query($sql);
    }

    function getSalesMasterInfo($id, $delivery_master_id = NULL)
    {
        $project_id = getFromSession('project_id');
        $SQLMain = "";
        $SQL = "
		SELECT pm.voucher_no,pm.delivery_point,pm.po_no,pm.wo_no,p.project_name,p.location,pm.customer,COALESCE((s.sub_id), (sp.supplier_code)) as sub_id,COALESCE((s.sub_head_name), (sp.name)) as sub_head_name,COALESCE((s.head_details), (sp.address)) as head_details,COALESCE((s.phone), (sp.phone)) as phone,COALESCE((s.mobile), (sp.mobile)) as mobile,COALESCE((s.email), (sp.email)) as email,COALESCE((s.att_name1), (sp.contact_person)) as att_name1,COALESCE((s.att_designation1), (sp.designation)) as att_designation1,COALESCE((s.att_mobile1), (sp.contact_person_mobile)) as att_mobile1,pm.gate_pass,pm.track_no,pm.salse_type,pm.total_value,DATE_FORMAT(pm.sales_date,'%d %b %y' ) as sales_date,DATE_FORMAT(pm.delivery_date,'%d %b %y' ) as value_date,pm.service_charge,pm.mode_of_payment,c.curr_symble,pm.bank_name,pm.acc_no,pm.check_no, pm.general_discount_percent,pm.general_discount_amount,pm.exclusive_discount_percent,pm.exclusive_discount_amount,pm.additional_discount_percent,pm.additional_discount,pm.product_discount, pm.discount,pm.net_payble,pm.paid_amount,pm.due,pm.item_delivery_amount,pm.return_amount,pm.ref_no,pm.created_date,DATE_FORMAT(pm.created_date,'%d %b %y' ) as date,pm.reference,pm.commission_slot,pm.total_commission,pm.commission_adv_paid,pm.commission_total_paid,pm.commission_total_due,pm.commission_status,pm.additional_vat_percent,pm.additional_vat_amount";
        if ($delivery_master_id != "") {
            $SQL .= ",DATE_FORMAT(sdm.delivery_date,'%d %b %y' ) as delivery_date, sdm.challan_no, sdm.consignee ";
        }

        $SQLTBL = "
		FROM " . SALES_MASTER_TBL . " pm
		LEFT JOIN " . SUB_ACC_HEAD_TBL . " s ON BINARY s.sub_id =pm.customer
		LEFT JOIN " . SUPPLIER_TBL . " sp ON BINARY sp.supplier_code = pm.customer
		LEFT JOIN " . PROJECT_TBL . " p ON p.project_id  =pm.project_id
		LEFT JOIN " . CURRENCY_TBL . " c ON c.currency_id  =pm.currency
		";
        if ($delivery_master_id != "") {
            $SQLTBL .= " LEFT JOIN " . SALES_DELIVERY_MASTER_TBL . " sdm ON BINARY sdm.voucher_no = pm.voucher_no ";
        }

        $SQLWhere = " WHERE pm.project_id = '" . $project_id . "' AND pm.voucher_no = '" . $id . "'";

        if ($delivery_master_id != "") {
            $SQLWhere .= " AND sdm.sales_delivery_master_id='$delivery_master_id'";
        }
        $SQLMain = $SQL . $SQLTBL . $SQLWhere . " GROUP BY pm.voucher_no";
        //echo $SQLMain;
        $res = query($SQLMain);
        $data = array();

        if (count($res) > 0) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        //dumpVar($data);
        return $data[0];
    }

    function getProductList($id, $delivery_master_id)
    {
        $info = array();
        $info['table'] = SALES_DETAILS_TBL . ' sd,' . SALES_DELIVERY_CHALLAN_TBL . ' sdi,' . CURRENCY_TBL . ' c,' . PRODUCT_TBL . ' p,' . BRAND_TBL . ' b';
        $info['fields'] = array('sd.sal_detail_id', 'sd.voucher_no', 'sd.project_id', 'sd.catagory', 'sd.serial', 'sd.warranty', 'b.brand_name', 'sd.product', 'sd.details', 'p.product_name', 'p.product_desc', 'sd.m_unit', 'sd.unit_price', 'c.curr_symble', 'sd.discount_per_qty', 'sd.discount_amount', 'sd.qty', 'SUM(sdi.delivery_qty) as delivery_qty', 'sdi.total_amount as delivery_item_amount', 'sd.delivery_qty as totaldelivery_qty', 'sd.total_bag', 'sd.total', 'sd.created_time');

        $sql = "sd.product = sdi.product AND sd.product = p.product_id AND p.brand_code = b.brand_id AND sd.currency = c.currency_id AND sd.voucher_no = '$id' 
		AND sdi.delivery_master_id='$delivery_master_id'";

        $info['where'] = $sql;
        $info['groupby'] = array("sd.sal_detail_id");
        $info['orderby'] = array("sd.product asc");
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

    function loadStockQty($product_id)
    {
        $project_id = getFromSession('project_id');
        $voucher_no = $_REQUEST['voucher_no'];
        $totalCr = $this->getTotalCreditStock($product_id, $project_id);
        $totalDr = $this->getTotalDebitStock($product_id, $project_id);
        $balanceQty = $totalDr - $totalCr;
        $sql = "SELECT SUM(delivery_qty) AS delivery_qty FROM " . SALES_DELIVERY_CHALLAN_TBL . " WHERE product ='$product_id' AND project_id = '$project_id' AND voucher_no = '$voucher_no'";
        $res = mysql_query($sql);
        if (mysql_num_rows($res) > 0) {
            $row = mysql_fetch_object($res);
            if ($row->delivery_qty == "") {
                $delivery_qty = 0;
            } else {
                $delivery_qty = $row->delivery_qty;
            }
        } else {
            $delivery_qty = 0;
        }
        $balanceQty = $balanceQty - $delivery_qty;
        echo $balanceQty;
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

    function getAccounceBalance($account_id, $project_id)
    {
        $sql = "SELECT (sum(`dr`) - sum(`cr`)) as balance_amount FROM " . ACCOUNT_JOURNAL_TBL . " WHERE product_id = '$account_id' AND project_id = '$project_id'";

        $row = mysql_fetch_object(mysql_query($sql));
        $balance_amount = $row->balance_amount;
        if (empty($balance_amount)) {
            $balance_amount = 0;
        }
        return $balance_amount;
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

    function createSalesVoucharID()
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

    function saveDebitVouchar($voucher_no, $master)
    {
        $mode_of_payment = $master->mode_of_payment;
        $requestdata = array();
        $requestdata = getUserDataSet(DEVIT_VOUCHAR_TBL);
        if ($mode_of_payment == "Recievable") {
            //======= Party Dr ======
            $requestdata['account_head'] = $master->customer;
            $requestdata['debit'] = $master->net_payble;
            $requestdata['credit'] = 0;
            $requestdata['paid_amount'] = 0;
            $requestdata['due'] = 0;
            $requestdata['head_type'] = "Customer";
        }
        $requestdata['transaction_type'] = "Sales Order";
        $requestdata['vouchar_type'] = "Sales Order";
        $requestdata['project_id'] = $master->project_id;
        $requestdata['created_by'] = $master->created_by;
        $requestdata['created_date'] = $master->sales_date; //date('Y-m-d h:i:s');
        $requestdata['voucher_no'] = $voucher_no;

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
    }

    function saveCreditVouchar($voucher_no, $master)
    {
        require_once(CLASS_DIR . '/common.list.class.php');
        $comlistApp = new CommonList();

        $mode_of_payment = $master->mode_of_payment;
        $requestdata = array();
        $requestdata = getUserDataSet(CREDIT_VOUCHAR_TBL);
        if ($mode_of_payment == "Recievable") {
            //======= Party Dr ======
            $requestdata['account_head'] = $comlistApp->getRecievableId(getFromSession('project_id'));
            $requestdata['credit'] = $master->net_payble;
            $requestdata['debit'] = 0;
        }
        $requestdata['transaction_type'] = "Sales Order";
        $requestdata['head_type'] = "Acc";
        $requestdata['project_id'] = $master->project_id;
        $requestdata['created_by'] = $master->created_by;

        $requestdata['created_date'] = $master->sales_date;
        $requestdata['voucher_no'] = $voucher_no;

        $info = array();
        $info['table'] = CREDIT_VOUCHAR_TBL;
        $info['data'] = $requestdata;
        //$info['debug']  	=  true;
        $res = insert($info);
    }


    function insertSalesItems($voucher_no, $existing_voucher_no, $sales_details_id, $undelivery_qty)
    {
        $sql = "SELECT * FROM " . SALES_DETAILS_TBL . " WHERE voucher_no = '$existing_voucher_no' AND sal_detail_id = '$sales_details_id'";
        $gres = mysql_query($sql);
        $row = mysql_fetch_object($gres);

        $detailsResult['total'] = 0;
        $detailsResult['product_discount'] = 0;

        if (mysql_num_rows($gres) > 0) {
            $requestdata = array();
            $requestdata['project_id'] = $row->project_id;
            $requestdata['brand_id'] = $row->brand_id;
            $requestdata['product'] = $row->product;
            $requestdata['discount_per_qty'] = (float)$row->discount_per_qty;
            $requestdata['details'] = $row->details;
            $requestdata['serial'] = $row->serial;
            $requestdata['unit_price'] = (float)$row->unit_price;
            $requestdata['qty'] = (float)$undelivery_qty;
            $requestdata['free_qty'] = (float)$row->free_qty;
            $requestdata['m_unit'] = $row->m_unit;

            $totalAmount = ($requestdata['unit_price'] * $requestdata['qty']);
            $detailsResult['total'] = $totalAmount;

            $discount_amount = (($requestdata['unit_price'] / 100) * $requestdata['discount_per_qty'] * $requestdata['qty']);
            $detailsResult['product_discount'] = $discount_amount;


            $requestdata['vat'] = $row->vat;
            if (isset($row->vat) && $row->vat > 0) {
                $vatAmount = ($totalAmount * $row->vat) / 100;
                $requestdata['vat_amount'] = $vatAmount;
                $totalAmount += $vatAmount;
            }

            $total = ($totalAmount - $discount_amount);
            $requestdata['total'] = $total;
            $requestdata['catagory'] = $row->catagory;
            $requestdata['discount_amount'] = $discount_amount;
            $requestdata['created_by'] = $row->created_by; //getFromSession('userid');
            $requestdata['created_date'] = date('Y-m-d h:i:s');
            $requestdata['wo_no'] = $voucher_no;
            $requestdata['voucher_no'] = $voucher_no;
            $requestdata['lc_no'] = $row->lc_no;
            $requestdata['customer'] = $row->customer;
            $requestdata['reference'] = $row->reference;
            $requestdata['division'] = $row->division;
            $requestdata['district'] = $row->district;
            $requestdata['area'] = $row->area;

            $info = array();
            $info['table'] = SALES_DETAILS_TBL;
            $info['data'] = $requestdata;
            $info['debug'] = true;
            $res = insert($info);

            $requestUpdateData['undelivery_qty'] = 0;
            $requestUpdateData['prev_undelivery_qty'] = $undelivery_qty;
            $infoUp = array();
            $infoUp['table'] = SALES_DETAILS_TBL;
            $infoUp['data'] = $requestUpdateData;
            $infoUp['where'] = "voucher_no = '$existing_voucher_no' AND sal_detail_id = '$sales_details_id'";
            //$infoUp['debug'] = true;
            update($infoUp);

            $requestdata['prev_undelivery_qty'] = $undelivery_qty;
            $detailsResult['sales_details'] = $requestdata;
        }

        return $detailsResult;
    }

    function salesMaster($voucher_no, $existing_voucher_no, $total_value = 0.00, $product_discount = 0.00, $sales_details = [])
    {
        $project_id = getFromSession('project_id');
        $msql = "SELECT * FROM " . SALES_MASTER_TBL . " WHERE voucher_no = '$existing_voucher_no' AND project_id = '$project_id'";
        $mres = mysql_query($msql);
        $mrow = mysql_fetch_object($mres);

        if (mysql_num_rows($mres) > 0) {
            $requestMasterdata = array();
            $requestMasterdata = getUserDataSet(SALES_MASTER_TBL);
            $requestMasterdata['transaction_type'] = $mrow->transaction_type;


            $requestMasterdata['voucher_no'] = $voucher_no;
            $requestMasterdata['po_no'] = $mrow->po_no;;
            $requestMasterdata['wo_no'] = $voucher_no;
            $requestMasterdata['und_wo_no'] = $existing_voucher_no;

            $ref_voucher = $mrow->wo_no;
            if (!empty($mrow->ref_voucher)) {
                $ref_voucher = $mrow->ref_voucher;
            }
            $requestMasterdata['ref_voucher'] = $ref_voucher;

            $requestMasterdata['project_id'] = $mrow->project_id;
            $requestMasterdata['division'] = $mrow->division;
            $requestMasterdata['district'] = $mrow->district;
            $requestMasterdata['area'] = $mrow->area;
            $requestMasterdata['delivery_point'] = $mrow->delivery_point;
            $requestMasterdata['customer'] = $mrow->customer;
            $requestMasterdata['reference'] = $mrow->reference;
            $requestMasterdata['gate_pass'] = $mrow->gate_pass;
            $requestMasterdata['track_no'] = $mrow->track_no;
            $requestMasterdata['salse_type'] = $mrow->salse_type;
            $requestMasterdata['order_type'] = $mrow->order_type;
            $requestMasterdata['sales_date'] = $mrow->sales_date;
            $requestMasterdata['delivery_date'] = $mrow->delivery_date;

            $requestMasterdata['total_value'] = $total_value;
            $requestMasterdata['mode_of_payment'] = $mrow->mode_of_payment;

            $afterDiscountTotal = $requestMasterdata['total_value'];
            $requestMasterdata['general_discount_percent'] = $mrow->general_discount_percent;
            $requestMasterdata['general_discount_amount'] = (($afterDiscountTotal / 100) * $requestMasterdata['general_discount_percent']);
            $afterDiscountTotal -= $requestMasterdata['general_discount_amount'];

            $requestMasterdata['exclusive_discount_percent'] = $mrow->exclusive_discount_percent;
            $requestMasterdata['exclusive_discount_amount'] = (($afterDiscountTotal / 100) * $requestMasterdata['exclusive_discount_percent']);
            $afterDiscountTotal -= $requestMasterdata['exclusive_discount_amount'];

            $requestMasterdata['additional_discount_percent'] = $mrow->additional_discount_percent;
            $requestMasterdata['additional_discount'] = (($afterDiscountTotal / 100) * $requestMasterdata['additional_discount_percent']);


            $requestMasterdata['product_discount'] = $product_discount;
            $requestMasterdata['discount'] = ($requestMasterdata['general_discount_amount'] + $requestMasterdata['exclusive_discount_amount'] + $requestMasterdata['additional_discount'] + $product_discount);
            $requestMasterdata['net_payble'] = $requestMasterdata['total_value'] - $requestMasterdata['discount'];
            $requestMasterdata['due'] = $requestMasterdata['net_payble'];

            $requestMasterdata['description'] = $mrow->description;
            $requestMasterdata['created_by'] = $mrow->created_by;;
            $requestMasterdata['created_date'] = date('Y-m-d h:i:s');

            $order_snapshot = [
                'master' => $requestMasterdata,
                'details' => $sales_details
            ];

            $requestMasterdata['order_snapshot'] = json_encode($order_snapshot);

            $info = array();
            $info['table'] = SALES_MASTER_TBL;
            $info['data'] = $requestMasterdata;
            $info['debug'] = true;
            $res = insert($info);
        }

        $this->saveDebitVouchar($voucher_no, $mrow);
        $this->saveCreditVouchar($voucher_no, $mrow);
    }


} // End class
?>
