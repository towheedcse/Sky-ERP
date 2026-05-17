<?php

class CommonList
{

    function sendSMS($recipients, $message)
    {
        $url = "https://corpsms.banglalink.net/bl/api/v1/smsapigw/";

        $username = "Almostafa";
        $password = "Imran@2025";
        $apicode = 5;
        $cli = "ALMOSTAFA";
        $bill_msisdn = "8801969901156";

        $timestamp = date("YmdHis"); // e.g., 20250906033045
        $random = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 6);
        $clientTransId = strtolower($timestamp . $random); // Final length: 20

        $payload = [
            "username" => $username,
            "password" => $password,
            "apicode" => $apicode,
            "msisdn" => is_array($recipients) ? $recipients : [$recipients], // ensure array
            "countrycode" => "880",
            "cli" => $cli,
            "messagetype" => "1", // 1 = Text, 2 = Unicode
            "message" => $message,
            "clienttransid" => $clientTransId,
            "bill_msisdn" => $bill_msisdn,
            "tran_type" => "T", // Transaction type
            "request_type" => "S", // S = SMS
            "rn_code" => "91"
        ];

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            return ["error" => curl_error($curl)];
        }

        curl_close($curl);

        return json_decode($response, true);
    }

    function normalizeProductName($product_code, $product_name, $seperator = "::")
    {
        $productName = $product_name;

        if (isset($product_code) && !empty($product_code)) {
            $productName = $product_code . $seperator . $product_name;
        } else {
            $stringArray = explode("-", $product_name);

            $code = "";
            $process = false;
            if (count($stringArray) > 1) {
                $firstPart = trim($stringArray[0]);

                // Case 1: It's numeric
                if (is_numeric($firstPart)) {
                    $code = $firstPart;
                    $process = true;
                } // Case 2: Starts with P or T and rest is numeric
                elseif (preg_match('/^[PTE](\d+)$/i', $firstPart, $matches)) {
                    $code = $matches[0]; // full match e.g., P123
                    $process = true;
                }
            }

            if ($process) {
                // Rebuild the name without the first part and the first dash
                array_shift($stringArray); // remove first part
                $cleanName = trim(implode("-", $stringArray));
                $productName = $code . $seperator . $cleanName;
            }
        }

        return $productName;
    }

    function normalizeUserName($user_code, $user_name, $seperator = "::")
    {
        $userName = $user_name;

        if (isset($user_code) && !empty($user_code)) {
            $userName = $user_code . $seperator . $user_name;
        } else {
            $stringArray = explode("-", $user_name);

            $code = "";
            $process = false;
            if (count($stringArray) > 1) {
                $firstPart = trim($stringArray[0]);

                // Case 1: It's numeric
                if (is_numeric($firstPart)) {
                    $code = $firstPart;
                    $process = true;
                }
            }

            if ($process) {
                // Rebuild the name without the first part and the first dash
                array_shift($stringArray); // remove first part
                $cleanName = trim(implode("-", $stringArray));
                $userName = $code . $seperator . $cleanName;
            }
        }

        return $userName;
    }

    //============== Common Function =================
    function getColorList()
    {
        $data = array();
        $info = array();
        $info['table'] = COLOR_TBL;
        $res = select($info);
        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        //dumpVar($data);
        return $data;
    }

    function getSizeList()
    {
        $data = array();
        $info = array();
        $info['table'] = SIZE_TBL;
        $res = select($info);
        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        //dumpVar($data);
        return $data;
    }

    function getCountryList()
    {
        $info = array();
        $info['table'] = COUNTRY_TBL;
        $info['fields'] = array('countrycode', 'country');
        $info['debug'] = false;
        $result = select($info);
        $data = array();
        if (count($result)) {
            foreach ($result as $i => $v) {
                $data[$i] = $v;
            }
        }
        return $data;
    }

    function getLanguage()
    {
        $info = array();
        $info['table'] = LANGUAGE_TBL;
        $info['debug'] = false;
        $result = select($info);
        $data = array();
        if (count($result)) {
            foreach ($result as $i => $v) {
                $data[$i] = $v;
            }
        }
        return $data;
    }

    function getSupplierList($fields = null)
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = SUB_ACC_HEAD_TBL;
        $info['debug'] = false;
        if (!empty($fields)) $info['fields'] = $fields;
        $info['where'] = "head_type = 'Current Liabilities' AND sub_headtype = 'S137' AND `child_head` = 'C000116'  AND project_id='" . $project_id . "'";
        $info['orderby'] = array("sub_head_name ASC");
        $res = select($info);
        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        //dumpVar($data);
        return $data;
    }

    function getCustomerList($fields = null)
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = SUB_ACC_HEAD_TBL;
        if (!empty($fields)) $info['fields'] = $fields;
        $info['where'] = "head_type = 'Current Assets' AND sub_headtype = 'S128' AND `child_head` = 'C000105' AND project_id='" . $project_id . "'";
        $info['orderby'] = array("sub_head_name ASC");
        $res = select($info);
        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        //dumpVar($data);
        return $data;
    }

    function getSupplierListPayable()
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = SUB_ACC_HEAD_TBL;
        $info['debug'] = false;
        $info['where'] = "head_type = 'Current Liabilities' AND sub_headtype = 'S150' AND project_id='" . $project_id . "'";
        $info['orderby'] = array("sub_head_name ASC");
        $res = select($info);
        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        //dumpVar($data);
        return $data;
    }

    function getCustomerListReceivable($fields = null)
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = SUB_ACC_HEAD_TBL;
        if (!empty($fields)) $info['fields'] = $fields;
        $info['where'] = "head_type = 'Current Assets' AND (sub_headtype = 'S147'OR sub_headtype = 'S128') AND project_id='" . $project_id . "'";
        $info['orderby'] = array("sub_head_name ASC");
        $res = select($info);
        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }

        //dumpVar($data);
        return $data;
    }

    function getProductionFactoryList()
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = FACTORY_TBL;
        $info['fields'] = array('factory_id', 'factory_name');
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

    function getMachineList()
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = MACHINE_TBL;
        $info['fields'] = array('machine_id', 'machine_name');
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

    function getProductList()
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = PRODUCT_TBL;
        $info['fields'] = array('product_id', 'product_code', 'catagory', 'brand_code', 'product_name', 'product_desc', 'product_type', 'product_catagory', 'm_unit');
        $info['where'] = "project_id = '$project_id' AND approval_status = 1";
        $info['orderby'] = array("product_name ASC");
        //$info['debug']  = true;
        $res = select($info);
        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        return $data;
    }

    function getFinishProductList()
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = PRODUCT_TBL;
        $info['fields'] = array('product_id', 'product_code', 'product_name');
        $info['where'] = "project_id = '$project_id' AND product_type='Sales Item' AND approval_status = 1";
        $info['orderby'] = array("product_name ASC");
        $res = select($info);
        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        return $data;
    }

    function getWastageProductList()
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = PRODUCT_TBL;
        $info['fields'] = array('product_id', 'product_code', 'product_name');
        $info['where'] = "project_id = '$project_id' AND subcatagory='S100038' AND approval_status = 1"; // wastage subcategory
        $info['orderby'] = array("product_name ASC");
        $res = select($info);
        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
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
        $data = array();
        if (count($result)) {
            foreach ($result as $i => $v) {
                $data[$i] = $v;
            }
        }
        return $data;
    }


    function getTermsAndConditionList()
    {
        $info = array();
        $info['table'] = TERMS_CONDITION_TBL;
        $info['debug'] = false;
        $result = select($info);
        $data = array();
        if (count($result)) {
            foreach ($result as $i => $v) {
                $data[$i] = $v;
            }
        }
        return $data;
    }

    function getSupplierType($sub_id)
    {
        $project_id = getFromSession('project_id');

        $info = array();
        $info['table'] = SUB_ACC_HEAD_TBL;

        $info['where'] = "
                head_type = 'Current Liabilities'
                AND project_id = '" . $project_id . "'
                AND (
                    (sub_headtype = 'S137' AND child_head = 'C000116')
                    OR
                    (sub_headtype = 'S150')
                )
                AND sub_id='$sub_id'
            ";

        $res = select($info);

        // If record found → Local
        if (!empty($res)) {
            return 'local';
        }

        // If no record → Import
        return 'import';
    }

    function getTransactionType($store_id = "", $transaction_type = "Purchase Item")
    {
        if (empty($store_id)) {
            return $transaction_type;
        }

        if (empty($project_id)) {
            $project_id = getFromSession('project_id');
        }
        $sql = "SELECT inventory_type,inventory_type_name FROM " . DELIVERY_POINT_TBL . " WHERE delivery_pid = '$store_id' AND project_id = '$project_id'";
        $row = mysql_fetch_object(mysql_query($sql));

        if (isset($row->inventory_type_name) && !empty($row->inventory_type_name)) {
            return $row->inventory_type_name;
        }
        if (isset($row->inventory_type) && !empty($row->inventory_type)) {
            switch ($row->inventory_type) {
                case "Inventory Item":
                    $transaction_type = "General Inventory";
                    break;
                case "Equipment":
                    $transaction_type = "Asset";
                    break;
                default:
                    $transaction_type = $row->inventory_type;
            }
        }
        return $transaction_type;
    }


    function getAccountList($head_type = NULL, $sub_headtype = NULL, $child_head = NULL, $sl_three_head = NULL)
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = SUB_ACC_HEAD_TBL;
        $sql = "";
        if ($head_type != "") {
            $sql = "project_id='$project_id'";
            $sql .= " AND head_type = '$head_type'";

            if ($sub_headtype != "") {
                $sql .= " AND sub_headtype = '$sub_headtype'";
            }
            if ($child_head != "") {
                $sql .= " AND child_head = '$child_head'";
            }
            if ($sl_three_head != "") {
                $sql .= " AND sl_three_head = '$sl_three_head'";
            }
        } else {
            $sql = "project_id='" . $project_id . "'";
        }
        $info['where'] = $sql;
        $info['orderby'] = array("sub_head_name ASC");
        $res = select($info);
        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        //dumpVar($data);
        return $data;
    }


    function getStoreMapLedgerID($store_id = "", $type)
    {
        if (empty($store_id)) {
            return false;
        }

        $project_id = getFromSession('project_id');

        $sql = "SELECT * FROM " . DELIVERY_POINT_TBL . " WHERE delivery_pid = '$store_id' AND project_id = '$project_id'";
        $row = mysql_fetch_object(mysql_query($sql));

        if (isset($row->delivery_pid) && !empty($row->delivery_pid)) {
            $headID = false;
            switch ($type) {
                case "account_ledger":
                    $headID = !empty($row->ledger_id) ? $row->ledger_id : false;
                    break;
                case "sales":
                    $headID = !empty($row->sales_ledger) ? $row->sales_ledger : false;
                    break;
                case "wip":
                    $headID = !empty($row->wip_ledger) ? $row->wip_ledger : false;
                    break;
                case "discount":
                    $headID = !empty($row->discount_ledger) ? $row->discount_ledger : false;
                    break;
                case "return":
                    $headID = !empty($row->return_ledger) ? $row->return_ledger : false;
                    break;
                case "vat":
                    $headID = !empty($row->vat_ledger) ? $row->vat_ledger : false;
                    break;
                case "vat_payable":
                    $headID = !empty($row->payable_vat_ledger) ? $row->payable_vat_ledger : false;
                    break;
                default:
                    $headID = false;
            }
            return $headID;
        }

        return false;
    }


    function getSupplierListCombined()
    {
        $project_id = getFromSession('project_id');

        $info = array();
        $info['table'] = SUB_ACC_HEAD_TBL;

        $info['where'] = "
                head_type = 'Current Liabilities'
                AND project_id = '" . $project_id . "'
                AND (
                    (sub_headtype = 'S137' AND child_head = 'C000116')
                    OR
                    (sub_headtype = 'S150')
                )
            ";

        $info['orderby'] = array("sub_head_name ASC");
        $info['groupby'] = array("sub_id");

        $res = select($info);
        $data = array();

        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }

        return $data;
    }


    function getSupplierData()
    {
        $info = array();
        $info['table'] = DELIVERY_POINT_TBL;
        //$info['debug'] = false;
        $result = select($info);
        $data = array();

        if (count($result)) {
            foreach ($result as $row) {
                $data[] = array(
                    'point_id' => $row->delivery_pid,
                    'sub_id' => $row->ledger_id,
                    'section' => $row->section,
                    'inventory_type' => $row->inventory_type,
                    'inventory_type_name' => $row->inventory_type_name,
                    'sales_ledger' => $row->sales_ledger,
                    'wip_ledger' => $row->wip_ledger,
                    'discount_ledger' => $row->discount_ledger,
                    'return_ledger' => $row->return_ledger,
                    'vat_ledger' => $row->vat_ledger,
                    'payable_vat_ledger' => $row->payable_vat_ledger
                );
            }
        }

        return $data;
    }


    function getImpoterList()
    {
        return array();
        $project_id = getFromSession('project_id');

        $info = array();
        $info['table'] = SUB_ACC_HEAD_TBL;

        $info['where'] = "
                head_type = 'Current Liabilities'
                AND project_id = '" . $project_id . "'
                AND sub_headtype = 'S137' AND child_head = 'C000116'
            ";

        $info['orderby'] = array("sub_head_name ASC");
        $info['groupby'] = array("sub_id");

        $res = select($info);

        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }

        return $data;
    }


    function getAccountHeadList($head_type = NULL, $sub_headtype = NULL, $child_head = NULL, $isNotSL = NULL, $isNotCL = NULL)
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = SUB_ACC_HEAD_TBL;
        $sql = "";
        if ($head_type != "") {
            $sql = "project_id='$project_id'";
            $sql .= " AND head_type = '$head_type'";

            if ($sub_headtype != "") {
                $sql .= " AND sub_headtype = '$sub_headtype'";
            }
            if ($child_head != "") {
                $sql .= " AND child_head = '$child_head'";
            }
            if ($isNotSL != "") {
                $sql .= " AND sub_headtype != '$isNotSL'";
            }
            if ($isNotCL != "") {
                $sql .= " AND child_head != '$isNotCL'";
            }
        } else {
            $sql = "project_id='" . $project_id . "'";
        }
        $info['where'] = $sql;
        $info['orderby'] = array("sub_head_name ASC");
        $res = select($info);
        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        //dumpVar($data);
        return $data;
    }

    function getCatagoryList()
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = CATAGORY_TBL;
        $info['where'] = "project_id = '$project_id'";
        $result = select($info);
        //dBug($result);
        //$info['debug']  	= true;
        $data = array();
        if (count($result)) {
            foreach ($result as $i => $v) {
                $data[$i] = $v;
            }
        }

        return $data;
    }

    function getMainCatagoryList()
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = MAIN_CATAGORY_TBL;
        $info['where'] = "project_id = '$project_id'";
        $result = select($info);
        //dBug($result);
        //$info['debug']  	= true;
        $data = array();
        if (count($result)) {
            foreach ($result as $i => $v) {
                $data[$i] = $v;
            }
        }

        return $data;
    }

    function getProductTypeList()
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = PRODUCT_TYPE_TBL;
        $info['where'] = "project_id = '$project_id'";
        //$info['debug']  	= true;
        $result = select($info);

        $data = array();
        if (count($result)) {
            foreach ($result as $i => $v) {
                $data[$i] = $v;
            }
        }

        return $data;
    }


    function getUOMList()
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = UOM_TBL;
        $info['where'] = "project_id = '$project_id'";
        $result = select($info);
        //dBug($result);
        //$info['debug']  	= true;
        $data = array();
        if (count($result)) {
            foreach ($result as $i => $v) {
                $data[$i] = $v;
            }
        }

        return $data;
    }

    function getBrandList()
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = BRAND_TBL;
        $info['where'] = "project_id = '$project_id'";
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

    function getUserStore()
    {
        $userid = getFromSession('userid');

        $info = [];
        $info['table'] = USER_TBL;
        $info['fields'] = ['store_ids'];   // ✅ only this column
        $info['where'] = "userid='" . $userid . "'";
        $info['debug'] = false;

        $res = select($info);
        $result = '';
        if (isset($res[0]) && !empty($res[0])) {
            $result = $res[0];
            $result = !empty($result->store_ids) ? $result->store_ids : '';
        }

        return $result;
    }

    function getDeliveryPointList($userStoreOnly = false)
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = DELIVERY_POINT_TBL;
        $info['fields'] = array('delivery_pid', 'delivery_point_name');

        $where = "project_id = '$project_id'";

        if ($userStoreOnly) {
            $store_ids = $this->getUserStore();

            if (!empty($store_ids)) {
                $storeIds = array_filter(
                    array_map('trim', explode(',', $store_ids)),
                    'ctype_alnum'
                );

                if (!empty($storeIds)) {
                    $where .= " AND delivery_pid IN ('" . implode("','", $storeIds) . "')";
                }
            }
        }

        $info['where'] = $where;

        $info['orderby'] = array("delivery_point_name ASC");
        $res = select($info);
        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }

        return $data;
    }

    function getReferenceList()
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = SUB_ACC_HEAD_TBL;
        $info['where'] = "head_type = 'Reference' AND project_id='" . $project_id . "'";
        $res = select($info);

        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        //dumpVar($data);
        return $data;
    }

    function getRetailerList()
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = RETAILER_TBL;
        $info['fields'] = array('retailer_id', 'retailer_name', 'address', 'mobile');
        $info['where'] = "project_id = '$project_id'";
        $res = select($info);
        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        return $data;
    }

    function getBranchList()
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = BRANCH_TBL;
        $info['where'] = "project_id='" . $project_id . "'";
        $res = select($info);

        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        //dumpVar($data);
        return $data;
    }

    function getDivisionList()
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = DIVISION_TBL;
        $info['fields'] = array('division_id', 'division_name_eng');
        $info['where'] = "project_id='" . $project_id . "'";
        $info['orderby'] = array("division_name_eng ASC");
        $res = select($info);

        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        //dumpVar($data);
        return $data;
    }

    function getDistrictList()
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = DISTRICT_TBL;
        $info['fields'] = array('district_id', 'district_name');
        $info['where'] = "project_id='" . $project_id . "'";
        $info['orderby'] = array("district_name ASC");
        $res = select($info);

        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        //dumpVar($data);
        return $data;
    }

    function getAreaList()
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = AREA_TBL;
        $info['fields'] = array('area_id', 'area_name');
        $info['where'] = "project_id='" . $project_id . "'";
        $info['orderby'] = array("area_name ASC");
        $res = select($info);
        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        //dumpVar($data);
        return $data;
    }

    function getProductCatagoryList()
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = PRODUCT_CATAGORY_TBL;
        $info['fields'] = array('pcatagory_id', 'product_catagory_name');
        $info['where'] = "project_id='" . $project_id . "'";
        $res = select($info);
        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        //dumpVar($data);
        return $data;
    }

    function getProductClassList()
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = PRUDUCT_CLASS_TBL;
        $info['fields'] = array('pclass_id', 'product_class_name');
        $info['where'] = "project_id='" . $project_id . "'";
        $res = select($info);
        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        //dumpVar($data);
        return $data;
    }

    function getClaimList()
    {
        $project_id = getFromSession('project_id');
        $data = array();
        $info = array();
        $info['table'] = WARRANTY_TBL . " w," . SUB_ACC_HEAD_TBL . " c";
        $info['fields'] = array('w.warranty_id', 'c.sub_head_name', 'w.product_desc', 'w.problemby_customer');
        $info['where'] = "w.customer_id = c.sub_id AND w.status <= 2 AND w.project_id='" . $project_id . "'";
        $info['orderby'] = array("w.warranty_id");
        $res = select($info);
        if (count($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }
        //dumpVar($data);
        return $data;
    }

    function saveAdministrativeCost($voucher_no, $DrAmount, $description, $created_date)
    {
        //========= Capital Cr ==========
        $created_date = formatDate($created_date);
        $capital_head = $this->getMainCapitalId(getFromSession('project_id'));
        $totalCapitalCR = $this->getTotalCreditAmount($capital_head, getFromSession('project_id'));
        $totalCapitalDR = $this->getTotalDebitAmount($capital_head, getFromSession('project_id'));
        $Capitalbalance = ($totalCapitalDR - ($totalCapitalCR + $DrAmount));
        $this->saveAccountJournal($voucher_no, $capital_head, "Acc", getFromSession('project_id'), $description, 0, $DrAmount, $Capitalbalance, 0, $created_date);

    }

    function getMainCapitalId($project_id)
    {
        if (empty($project_id)) {
            $project_id = getFromSession('project_id');
        }
        $sql = "SELECT sub_id FROM " . SUB_ACC_HEAD_TBL . " WHERE head_type ='Capital' AND `sub_headtype` = 'S113' AND child_head='C000123' AND sl_three_head='S300058' AND project_id = '$project_id'";
        $res = mysql_query($sql);
        if (mysql_num_rows($res) > 0) {
            $row = mysql_fetch_object($res);
            return $row->sub_id;
        } else {
            return "A000005";
        }
    }

    function getCapitalId($project_id)
    {
        if (empty($project_id)) {
            $project_id = getFromSession('project_id');
        }
        $sql = "SELECT sub_id FROM " . SUB_ACC_HEAD_TBL . " WHERE head_type ='Capital' AND `sub_headtype` = 'S113' AND child_head='C000123' AND sl_three_head='S300058' AND project_id = '$project_id'";
        $res = mysql_query($sql);
        if (mysql_num_rows($res) > 0) {
            $row = mysql_fetch_object($res);
            return $row->sub_id;
        } else {
            return "A000005";
        }
    }

    function getCashId($project_id)
    {
        if (empty($project_id)) {
            $project_id = getFromSession('project_id');
        }
        $sql = "SELECT sub_id FROM " . SUB_ACC_HEAD_TBL . " WHERE head_type = 'Current Assets' AND `sub_headtype` = 'S130' AND child_head='C000064' AND sl_three_head='S300002' AND sub_head_name LIKE '%Main Cash%' AND project_id = '$project_id'";
        $res = mysql_query($sql);
        if (mysql_num_rows($res) > 0) {
            $row = mysql_fetch_object($res);
            return $row->sub_id;
        } else {
            return "A000001";
        }
    }

    function getRecievableId($project_id)
    {
        if (empty($project_id)) {
            $project_id = getFromSession('project_id');
        }
        $sql = "SELECT sub_id FROM " . SUB_ACC_HEAD_TBL . " WHERE head_type ='Current Assets' AND `sub_headtype` = 'S128' AND child_head='C000104' AND sub_head_name LIKE 'Account Recievable' AND project_id = '$project_id'";
        $res = mysql_query($sql);
        if (mysql_num_rows($res) > 0) {
            $row = mysql_fetch_object($res);
            return $row->sub_id;
        } else {
            return "A000024";
        }
    }

    function getPayableId($project_id)
    {
        if (empty($project_id)) {
            $project_id = getFromSession('project_id');
        }
        $sql = "SELECT sub_id FROM " . SUB_ACC_HEAD_TBL . " WHERE head_type ='Current Liabilities' AND `sub_headtype` = 'S143' AND child_head='C000138' AND sl_three_head='S300110' AND sub_head_name LIKE 'Account Payable' AND project_id = '$project_id'";
        $res = mysql_query($sql);
        if (mysql_num_rows($res) > 0) {
            $row = mysql_fetch_object($res);
            return $row->sub_id;
        } else {
            return "A000028";
        }
    }

    function getCOGSAccounceId($project_id)
    {
        if (empty($project_id)) {
            $project_id = getFromSession('project_id');
        }
        $sql = "SELECT sub_id FROM " . SUB_ACC_HEAD_TBL . " WHERE head_type ='Cost Center' AND project_id = '$project_id'";
        $res = mysql_query($sql);
        if (mysql_num_rows($res) > 0) {
            $row = mysql_fetch_object($res);
            return $row->sub_id;
        } else {
            return "A006351";
        }
    }

    function getAdvCostFreeItemId($project_id)
    {
        $sql = "SELECT sub_id FROM " . SUB_ACC_HEAD_TBL . " WHERE head_type ='Administrative Cost' AND sub_head_name='Free Product' AND project_id='$project_id' 
	ORDER BY sub_id ASC";
        $res = mysql_query($sql);
        if (mysql_num_rows($res) > 0) {
            $row = mysql_fetch_object($res);
            return $row->sub_id;
        } else {
            return false;
        }
    }

    function getSalesHeadId($project_id)
    {
        if (empty($project_id)) {
            $project_id = getFromSession('project_id');
        }
        // # Revenue > Operating Revenue > Sales > Sales Product Cr
        $sql = "SELECT sub_id FROM " . SUB_ACC_HEAD_TBL . " WHERE head_type = 'Operating Revenue' AND sub_headtype ='S124' AND child_head='C000127' AND sl_three_head='S300062' AND project_id = '$project_id'";
        $res = mysql_query($sql);
        if (mysql_num_rows($res) > 0) {
            $row = mysql_fetch_object($res);
            return $row->sub_id;
        } else {
            return "A000015";
        }
    }

    function getSalesDiscountId($project_id)
    {
        if (empty($project_id)) {
            $project_id = getFromSession('project_id');
        }
        // # Revenue > Operating Revenue > Sales > Sales Discounts or Free Product Dr
        $sql = "SELECT sub_id FROM " . SUB_ACC_HEAD_TBL . " WHERE head_type ='Operating Revenue' AND `sub_headtype` = 'S124' AND child_head='C000128' AND sl_three_head='S300098' AND project_id='$project_id' ORDER BY sub_id ASC";
        $res = mysql_query($sql);
        if (mysql_num_rows($res) > 0) {
            $row = mysql_fetch_object($res);
            return $row->sub_id;
        } else {
            return "A000009";
        }
    }

    function getSalesReturnId($project_id)
    {
        if (empty($project_id)) {
            $project_id = getFromSession('project_id');
        }
        // # Revenue > Operating Revenue > Sales > Sales Return Dr
        $sql = "SELECT sub_id FROM " . SUB_ACC_HEAD_TBL . " WHERE head_type ='Operating Revenue' AND `sub_headtype` = 'S124' AND child_head='C000129' AND sl_three_head='S300099' AND project_id='$project_id' ORDER BY sub_id ASC";
        $row = mysql_fetch_object(mysql_query($sql));
        $res = mysql_query($sql);
        if (mysql_num_rows($res) > 0) {
            $row = mysql_fetch_object($res);
            return $row->sub_id;
        } else {
            return "A000016";
        }
    }

    function getOthersIncomeId($project_id)
    {
        if (empty($project_id)) {
            $project_id = getFromSession('project_id');
        }
        // # Revenue > Non-Operating Revenue > Income > Others Income Cr
        $sql = "SELECT sub_id FROM " . SUB_ACC_HEAD_TBL . " WHERE head_type = 'Non-Operating Revenue' AND sub_headtype ='S121' AND child_head='C000131' AND sl_three_head='S300064' AND project_id='$project_id' ORDER BY sub_id ASC";
        $res = mysql_query($sql);
        if (mysql_num_rows($res) > 0) {
            $row = mysql_fetch_object($res);
            return $row->sub_id;
        } else {
            return "A000027";
        }
    }


    function getPurchaseDiscountId($project_id)
    {
        if (empty($project_id)) {
            $project_id = getFromSession('project_id');
        }
        // # Current Assets  > Inventories > Discount > Purchase Discount or Free Item Cr
        $sql = "SELECT sub_id FROM " . SUB_ACC_HEAD_TBL . " WHERE head_type ='Current Assets' AND `sub_headtype` = 'S127' AND child_head='C000136' AND sl_three_head='S300075' AND project_id='$project_id' ORDER BY sub_id ASC";
        $row = mysql_fetch_object(mysql_query($sql));
        $res = mysql_query($sql);
        if (mysql_num_rows($res) > 0) {
            $row = mysql_fetch_object($res);
            return $row->sub_id;
        } else {
            return "A000037";
        }
    }

    function getPurchaseReturnId($project_id)
    {

        if (empty($project_id)) {
            $project_id = getFromSession('project_id');
        }
        // # Current Assets  > Inventories > Return > Purchase Return Cr
        $sql = "SELECT sub_id FROM " . SUB_ACC_HEAD_TBL . " WHERE head_type ='Current Assets' AND `sub_headtype` = 'S127' AND child_head='C000135' AND sl_three_head='S300076' AND project_id='$project_id' ORDER BY sub_id ASC";
        $row = mysql_fetch_object(mysql_query($sql));
        $res = mysql_query($sql);
        if (mysql_num_rows($res) > 0) {
            $row = mysql_fetch_object($res);
            return $row->sub_id;
        } else {
            return "A000038";
        }
    }

    function getFGStockId($project_id)
    {
        $sql = "SELECT sub_id FROM " . SUB_ACC_HEAD_TBL . " WHERE head_type ='Current Assets' AND `sub_headtype` = 'S127' AND child_head='C000056' AND sl_three_head='S300029' AND project_id = '$project_id' ORDER BY sub_id ASC";
        $res = mysql_query($sql);
        if (mysql_num_rows($res) > 0) {
            $row = mysql_fetch_object($res);
            return $row->sub_id;
        } else {
            return "A000036"; // old A004443
        }
    }

    function getRMStockId($project_id)
    {
        $sql = "SELECT sub_id FROM " . SUB_ACC_HEAD_TBL . " WHERE head_type ='Current Assets' AND `sub_headtype` = 'S127' AND child_head='C000055' AND sl_three_head='S300030' AND project_id = '$project_id' ORDER BY sub_id ASC";
        $res = mysql_query($sql);
        if (mysql_num_rows($res) > 0) {
            $row = mysql_fetch_object($res);
            return $row->sub_id;
        } else {
            return "A000017"; // old A004444
        }
    }

    function getWPStockId($project_id)
    {
        $sql = "SELECT sub_id FROM " . SUB_ACC_HEAD_TBL . " WHERE head_type ='Current Assets' AND `sub_headtype` = 'S127' AND child_head='C000057' AND sl_three_head='S300031' AND project_id = '$project_id' ORDER BY sub_id ASC";
        $res = mysql_query($sql);
        if (mysql_num_rows($res) > 0) {
            $row = mysql_fetch_object($res);
            return $row->sub_id;
        } else {
            return "A000018";
        }
    }

    function getMXStockId($project_id)
    {
        $sql = "SELECT sub_id FROM " . SUB_ACC_HEAD_TBL . " WHERE head_type ='Current Assets' AND `sub_headtype` = 'S127' AND child_head='C000142' AND sl_three_head='S300147' AND project_id = '$project_id' ORDER BY sub_id ASC";
        $res = mysql_query($sql);
        if (mysql_num_rows($res) > 0) {
            $row = mysql_fetch_object($res);
            return $row->sub_id;
        } else {
            return "A000021";
        }
    }

    function getWTStockId($project_id)
    {
        $sql = "SELECT sub_id FROM " . SUB_ACC_HEAD_TBL . " WHERE head_type ='Current Assets' AND `sub_headtype` = 'S127' AND child_head='C000143' AND sl_three_head='S300149' AND project_id = '$project_id' ORDER BY sub_id ASC";
        $res = mysql_query($sql);
        if (mysql_num_rows($res) > 0) {
            $row = mysql_fetch_object($res);
            return $row->sub_id;
        } else {
            return "A000003";
        }
    }

    function getBadDebtExpId($project_id)
    {
        if (empty($project_id)) {
            $user_type = getFromSession('u_type_id');
            if ($user_type == 100) {
                $project_id = getRequest('project_id');
            } else {
                $project_id = getFromSession('project_id');
            }
            if (empty($project_id)) {
                $project_id = getFromSession('project_id');
            }
        }
        // # Expenses > Indirect Expenses > Operating Expense > Administrative Expenses > Bad Debt Expenses Dr
        $sql = "SELECT sub_id FROM " . SUB_ACC_HEAD_TBL . " WHERE head_type ='Indirect Expenses' AND `sub_headtype` = 'S139' AND child_head='C000120' AND sl_three_head='S300106' AND project_id='$project_id' ORDER BY sub_id ASC";
        $row = mysql_fetch_object(mysql_query($sql));
        $res = mysql_query($sql);
        if (mysql_num_rows($res) > 0) {
            $row = mysql_fetch_object($res);
            return $row->sub_id;
        } else {
            return "A00229";
        }
    }

    //=== Not Used ===
    function getStockId($project_id)
    {
        $sql = "SELECT sub_id FROM " . SUB_ACC_HEAD_TBL . " WHERE head_type ='Stocks' AND sub_head_name='Stocks' AND project_id = '$project_id' ORDER BY sub_id ASC";
        $res = mysql_query($sql);
        if (mysql_num_rows($res) > 0) {
            $row = mysql_fetch_object($res);
            return $row->sub_id;
        } else {
            return false;
        }
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

    function getTotalCreditAmountByDate($acc_head, $project_id, $from_date)
    {
        $sql = "SELECT sum(`cr`) as credit_amount FROM " . ACCOUNT_JOURNAL_TBL . " WHERE sub_id = '$acc_head' AND project_id = '$project_id' AND created_date<'$from_date'";
        $row = mysql_fetch_object(mysql_query($sql));
        $credit_amount = $row->credit_amount;
        if (empty($credit_amount)) {
            $credit_amount = 0;
        }
        return $credit_amount;
    }

    function getTotalDebitAmountByDate($acc_head, $project_id, $from_date)
    {
        $sql = "SELECT sum(`dr`) as debit_amount FROM " . ACCOUNT_JOURNAL_TBL . " WHERE sub_id = '$acc_head' AND project_id = '$project_id' AND created_date<'$from_date'";
        $row = mysql_fetch_object(mysql_query($sql));
        $debit_amount = $row->debit_amount;
        if (empty($debit_amount)) {
            $debit_amount = 0;
        }
        return $debit_amount;
    }

    function getAccounceBalance($account_id, $project_id)
    {
        if (empty($project_id)) {
            $project_id = getFromSession('project_id');
        }
        $sql = "SELECT (sum(`dr`) - sum(`cr`)) as balance_amount FROM " . ACCOUNT_JOURNAL_TBL . " WHERE sub_id = '$account_id' AND project_id = '$project_id'";

        $row = mysql_fetch_object(mysql_query($sql));
        $balance_amount = $row->balance_amount;

        if (empty($balance_amount)) {
            $balance_amount = 0;
        }
        return $balance_amount;
    }

    function saveAccJournal($voucher_no, $sub_id, $head_type, $transaction_type, $project_id, $description, $DR = NULL, $CR = NULL, $balance, $status, $created_date, $delivery_id = NULL, $supplier_purchase_type = NULL)
    {
        $head_type = getHeadType($sub_id);
        $created_by = getFromSession('userid');
        if ($delivery_id == "") {
            $delivery_id = 0;
        }
        $sql = "INSERT INTO " . ACCOUNT_JOURNAL_TBL . " (voucher_no,delivery_id,created_date,sub_id,head_type,transaction_type,project_id,description,dr,cr,balance,status,created_by,supplier_purchase_type)
	 VALUES('" . $voucher_no . "','" . $delivery_id . "','" . $created_date . "','" . $sub_id . "','" . $head_type . "','" . $transaction_type . "','" . $project_id . "','" . $description . "','" . $DR . "','" . $CR . "','" . $balance . "','" . $status . "','" . $created_by . "','" . $supplier_purchase_type . "')";
        mysql_query($sql);
    }

    function saveVoucherAdjustHistory($voucher_no, $project_id, $adjust_tbl, $adjust_ref, $adjust_amount, $adjust_type)
    {
        $sql = "INSERT INTO " . VOUCHER_ADJUST_HISTORY_TBL . " (voucher_no,project_id,adjust_tbl,adjust_ref,adjust_amount,adjust_type)
	 VALUES('" . $voucher_no . "','" . $project_id . "','" . $adjust_tbl . "','" . $adjust_ref . "','" . $adjust_amount . "','" . $adjust_type . "')";
        mysql_query($sql);
    }

    function saveInvoiceAdjustHistory($voucher_no, $delivery_id, $project_id, $adjust_tbl, $adjust_ref, $adjust_amount, $adjust_type)
    {
        $sql = "INSERT INTO " . INVOICE_ADJUST_HISTORY_TBL . " (voucher_no,delivery_id,project_id,adjust_tbl,adjust_ref,adjust_amount,adjust_type)
	 VALUES('" . $voucher_no . "','" . $delivery_id . "','" . $project_id . "','" . $adjust_tbl . "','" . $adjust_ref . "','" . $adjust_amount . "','" . $adjust_type . "')";
        mysql_query($sql);
    }
    //======= End Common Function =========

} // End class
?>
