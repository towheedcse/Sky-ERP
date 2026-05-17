<?php
/* apps/batch_setup.php */

class BatchSetup
{
    // Global DB link usually needed for mysql_real_escape_string if not using a wrapper
    var $db_link;

    function run()
    {
        // Read JSON body
        $input = json_decode(file_get_contents('php://input'), true);

        // Get cmd from JSON OR GET OR POST
        $cmd = '';

        if (!empty($input['cmd'])) {
            $cmd = $input['cmd'];
        } elseif (!empty($_POST['cmd'])) {
            $cmd = $_POST['cmd'];
        } elseif (!empty($_GET['cmd'])) {
            $cmd = $_GET['cmd'];
        }

        // Handle AJAX Requests
        // We rely on the standard header to identify AJAX
        $isAjax = (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false
        );


        if ($isAjax) {
            // 1. Clean Output Buffer FIRST to prevent HTML leaking
            while (ob_get_level()) ob_end_clean();

            // 2. Set JSON Header AFTER cleaning
            header('Content-Type: application/json');

            // 3. Handle Commands
            switch ($cmd) {
                case 'getdeliverypointlist':
                    echo $this->getDeliveryPointList();
                    break;
                case 'getproductlist':
                    echo $this->getProductList();
                    break;
                case 'currency-list':
                    echo $this->getCurrencyList();
                    break;
                case 'raw-material-product-list':
                    echo $this->getRawMaterialProductList();
                    break;
                case 'get-tmp-batch-list':
                    echo $this->getTempBatchList();
                    break;
                case 'add-temp-batch-item':
                    echo $this->saveTempBatch();
                    break;
                case 'get-temp-batch-infobyid':
                    echo $this->getTempBatchInfoById();
                    break;
                case 'add-new-batch':
                    echo $this->saveBatch();
                    break;
                case 'delete-tmp-batch-record':
                    echo $this->deleteTempBatch();
                    break;
                case 'get-batch-list':
                    echo $this->getAllBatchList();
                    break;
                case 'get-batch-infobyid':
                    echo $this->getBatchInfoById();
                    break;
                case 'get-batch-details-infobyid':
                    echo $this->getBatchDetailsInfoById();
                    break;
                case 'add-detail-batch-item':
                    echo $this->addBatchDetail();
                    break;
                case 'update-batch-record':
                    echo $this->saveBatchMaster();
                    break;
                case 'delete-batch-record':
                    echo $this->deleteBatch();
                    break;
                case 'delete-batch-detail-record':
                    echo $this->deleteBatchDetail();
                    break;
                default:
                    echo json_encode(['status' => false, 'message' => 'Invalid Command']);
                    break;
            }
            // 4. Exit immediately to prevent Page Load code from running
            exit;
        }

        // Handle Page Load
        switch ($cmd) {
            case 'add':
            case 'edit':
                $this->showEditor();
                break;
            case 'list':
                $this->showJobOrderList();
                break;
            default:
                $this->showEditor();
                break;
        }
    }

    private function getRequest($key, $default = "")
    {
        static $json = null;

        // Decode JSON only once
        if ($json === null) {
            $json = json_decode(file_get_contents('php://input'), true);
        }

        // Check JSON body first
        if (is_array($json) && isset($json[$key])) {
            return $this->clean($json[$key]);
        }

        // Then check POST
        if (isset($_POST[$key])) {
            return $this->clean($_POST[$key]);
        }

        // Then check GET (optional)
        if (isset($_GET[$key])) {
            return $this->clean($_GET[$key]);
        }

        // Return default value if not found
        return $default;
    }


    function showJobOrderList()
    {
        require_once(BATCH_LIST_SKIN);
        return true;
    }

    function showEditor()
    {
        require_once(CURRENT_APP_SKIN_FILE);
        return true;
    }

    private function clean($val)
    {
        return mysql_real_escape_string(trim($val));
    }

    // --- API Functions (Same as before, kept logic) ---

    public function getDeliveryPointList()
    {
        $project_id = getFromSession('project_id');

        $sql = "SELECT delivery_pid, delivery_point_name FROM deliverypoint WHERE project_id = '$project_id' ORDER BY delivery_point_name ASC";

        $result = mysql_query($sql);
        $selectData = [];
        $list = '<option selected value="">Select store</option>';
        if ($result && mysql_num_rows($result) > 0) {
            while ($item = mysql_fetch_assoc($result)) {
                $list .= '<option value="' . $item['delivery_pid'] . '" >' . htmlspecialchars($item['delivery_point_name']) . '</option>';
            }
        }

        return json_encode(["status" => true, "message" => "Successful", "data" => $list, "selectData" => $selectData]);
    }

    public function getProductList()
    {
        $sql = "SELECT product_id, product_name FROM product WHERE status = 1 ORDER BY product_name ASC";

        $result = mysql_query($sql);
        $list = '<option selected value="">Select product</option>';
        if ($result && mysql_num_rows($result) > 0) {
            while ($item = mysql_fetch_assoc($result)) {
                $list .= '<option value="' . $item['product_id'] . '" >' . htmlspecialchars($item['product_name']) . '</option>';
            }
        }

        return json_encode(["status" => true, "message" => "Successful", "data" => $list]);
    }

    public function getCurrencyList()
    {
        $sql = "SELECT * FROM currency ORDER BY currency_id";
        $result = mysql_query($sql);
        $list = '<option selected value="">Select currency</option>';
        if ($result && mysql_num_rows($result) > 0) {
            while ($item = mysql_fetch_assoc($result)) {
                $list .= '<option value="' . $item['currency_id'] . '" >' . $item['currency_des'] . '</option>';
            }
        }

        return json_encode(["status" => true, "message" => "Successful", "data" => $list]);
    }

    public function getRawMaterialProductList()
    {
        $project_id = getFromSession('project_id');
        $product_type = "Raw Materials";

        $sql = "SELECT product_id, product_name FROM product WHERE status = 1 AND product_type = '$product_type' ";
        if ($project_id != "") {
            $sql .= " AND project_id='$project_id'";
        }
        $sql .= "ORDER BY product_name ASC";

        $result = mysql_query($sql);
        $list = '<option selected value="">Select one</option>';
        if ($result && mysql_num_rows($result) > 0) {
            while ($item = mysql_fetch_assoc($result)) {
                $list .= '<option value="' . $item['product_id'] . '" >' . $item['product_name'] . '</option>';
            }
        }
        return json_encode(["status" => true, "message" => "Successful", "data" => $list]);
    }

    public function getTempBatchList()
    {
        $created_by = getFromSession('userid');
        $project_id = getFromSession('project_id');

        $from = $this->getRequest('from', 0);
        $to = $this->getRequest('from', 10);
        $page_no = $this->getRequest('page_no', 1);

        if (empty($from)) {
            $from = 0;
        }
        if (empty($to)) {
            $to = 10;
        }
        if (empty($page_no)) {
            $page_no = 1;
        }
        $perPage = 30;
        $offset = ($page_no - 1) * $perPage;

        $sql = "SELECT * FROM temp_batch_setup WHERE created_by = '$created_by' AND project_id = '$project_id' ORDER BY tmp_id DESC LIMIT $offset,$perPage";
        $query = mysql_query($sql);

        $countSql = "SELECT COUNT(*) as total FROM temp_batch_setup WHERE created_by = '$created_by' AND project_id = '$project_id'";
        $countRes = mysql_query($countSql);
        $totalRow = mysql_fetch_assoc($countRes);
        $totalRecord = $totalRow['total'];

        $Pagination = $this->ellipsisPagination($totalRecord, $perPage);

        $htmlContent = '<table class="table table-zebra">
                            <thead class="bg-base-200 text-base">
                            <tr>
                                <th>Sl</th>
                                <th>Product Name</th>
                                <th>Qty(gram)</th>
                                <th>Wastage(%)</th>
                                <th>Qty(kg)</th>
                                <th class="text-center">Options</th>
                            </tr>
                            </thead>
                            <tbody>';

        $sl = $offset;
        $TotalDayQty = 0;
        if ($query && mysql_num_rows($query) > 0) {
            while ($row = mysql_fetch_assoc($query)) {
                $sl++;
                $TotalDayQty += $row['total_day'];

                $htmlContent .= '<tr>
                        <td>' . $sl . '</td>
                        <td>' . htmlspecialchars($row["product_name"]) . '</td>
                        <td>' . number_format($row["day_qty"], 2, '.', '') . '</td>
                        <td>' . number_format($row["day_wastage_persent"], 2, '.', '') . '</td>
                        <td>' . number_format($row["total_day"], 2, '.', '') . ' ' . $row["m_unit"] . '</td>
                        <td>
                            <div class="flex gap-1 items-center justify-center">
                                <button class="btn bg-base-100 text-sm text-base-content border border-base-300" onclick="editTempRecord(\'' . $row["tmp_id"] . '\')"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button class="btn bg-base-100 text-sm text-base-content border border-base-300" onclick="deleteTempRecord(\'' . $row["tmp_id"] . '\')"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                </tr>';
            }
        } else {
            $htmlContent .= '<tr><td colspan="6" class="text-center">No record found</td></tr>';
        }

        $htmlContent .= '</tbody>
                        <tfoot class="text-base">
                            <tr>
                                <td colspan="4" class="text-right">Total:</td>
                                <td>' . number_format($TotalDayQty, "2") . ' kg</td>
                                <td></td>
                            </tr>
                        </tfoot>
                </table>';

        $totalInfo = ["TotalDayQty" => $TotalDayQty, "TotalNightQty" => 0, "TotalDayWastage" => 0, "TotalNightWastage" => 0];
        $data = ["status" => true, "html" => $htmlContent, "pagination" => $Pagination, "totalInfo" => $totalInfo];
        return json_encode($data);
    }

    public function saveTempBatch()
    {
        $created_by = getFromSession('userid');
        $project_id = getFromSession('project_id');

        $batch_name = $this->getRequest('batch_name');
        $finish_goods = $this->getRequest('finish_goods');

        $currency_id = $this->getRequest('currency_id', '');
        $out_from = $this->getRequest('out_from', '');

        $qty = $this->getRequest('qty', 0);
        $wastage = $this->getRequest('wastage', 0);
        $wastage_qty = $this->getRequest('wastage_qty', 0);
        $total_qty = $this->getRequest('total_qty', 0);

        $product_id = $this->getRequest('product_id', '');
        $tmp_id = $this->getRequest('tmp_id', '');


        $csql = "SELECT currency_name FROM currency WHERE currency_id = '$currency_id'";
        $cres = mysql_query($csql);
        $currencyname = ($cres && $row = mysql_fetch_assoc($cres)) ? $row['currency_name'] : "";

        $psql = "SELECT product_name, m_unit, catagory, brand_code FROM product WHERE product_id = '$product_id'";
        $pres = mysql_query($psql);
        $product_name = "";
        $catagory = "";
        $brand_id = "";
        $m_unit = "";
        if ($pres && $row = mysql_fetch_assoc($pres)) {
            $product_name = $row['product_name'];
            $catagory = $row['catagory'];
            $brand_id = $row['brand_code'];
            $m_unit = $row['m_unit'];
        }

        $day_qty = $qty;
        $day_wastage_persent = $wastage;
        $day_wastage_qty = $wastage_qty;
        $total_day = $total_qty;

        if ($tmp_id == "" || $tmp_id == 0) {
            $sql = "INSERT INTO temp_batch_setup (project_id,batch_name,finish_goods,currency,currencyname,out_from,productid,product_name,catagory,brand_id,m_unit,day_qty,day_wastage_persent,day_wastage_qty,total_day,created_by) 
                    VALUES ('$project_id','$batch_name','$finish_goods','$currency_id','$currencyname','$out_from','$product_id','$product_name','$catagory','$brand_id','$m_unit','$day_qty','$day_wastage_persent','$day_wastage_qty','$total_day','$created_by')";
            $res = mysql_query($sql);
            $msg = $res ? "Record has been saved successfully!" : "Record did not saved!";
        } else {
            $sql = "UPDATE temp_batch_setup SET project_id='$project_id', batch_name='$batch_name',finish_goods='$finish_goods',currency='$currency_id',currencyname='$currencyname',out_from='$out_from',productid='$product_id',product_name='$product_name',catagory='$catagory',brand_id='$brand_id',m_unit='$m_unit',day_qty='$day_qty',day_wastage_persent='$day_wastage_persent',day_wastage_qty='$day_wastage_qty',total_day='$total_day' WHERE tmp_id=$tmp_id";
            $res = mysql_query($sql);
            $msg = $res ? "Record has been edited successfully!" : "Record did not edited!";
        }

        $data = ["status" => ($res ? true : false), "message" => $msg];
        $data['htmlData'] = json_decode($this->getTempBatchList(), true);
        return json_encode($data);
    }

    public function getTempBatchInfoById()
    {
        $tmp_id = $this->getRequest('tmp_id', '');
        if ($tmp_id != "") {
            $sql = "SELECT * FROM temp_batch_setup WHERE tmp_id=$tmp_id";
            $res = mysql_query($sql);
            if ($res && $row = mysql_fetch_assoc($res)) {
                $dsql = "SELECT delivery_point_name FROM deliverypoint WHERE delivery_pid='{$row['out_from']}'";
                $dres = mysql_query($dsql);
                if ($dres && $drow = mysql_fetch_assoc($dres)) $row['out_from_name'] = $drow['delivery_point_name'];
                return json_encode(["status" => true, "message" => "Successful", "data" => $row]);
            }
        }
        return json_encode(["status" => false, "message" => "Record id required"]);
    }

    public function deleteTempBatch()
    {
        $deleted_id = $this->getRequest('deleted_id', '');
        if ($deleted_id !== "") {
            $sql = "DELETE FROM temp_batch_setup WHERE tmp_id=$deleted_id";
            $res = mysql_query($sql);
            return json_encode(["status" => ($res ? true : false), "message" => ($res ? "Record deleted successfully!!!" : "Record did not deleted.")]);
        }
        return json_encode(["message" => "Record id required"]);
    }

    public function saveBatch()
    {
        mysql_query("START TRANSACTION");
        $batch_id = $this->insertPOBatchMaster();
        $this->insertPOBatchDetails($batch_id);
        mysql_query("COMMIT");

        $msg = ($batch_id != "") ? "Record has been saved successfully!" : "Record did not saved!";
        $data = ["status" => ($batch_id ? true : false), "message" => $msg];
        $data['htmlData'] = json_decode($this->getTempBatchList(), true);
        return json_encode($data);
    }

    public function insertPOBatchMaster()
    {
        $created_by = getFromSession('userid');
        $project_id = getFromSession('project_id');

        $batch_name = $this->getRequest('batch_name', '');
        $finish_goods = $this->getRequest('finish_goods', '');
        $out_from = $this->getRequest('out_from', '');
        $currency_id = $this->getRequest('currency_id', '');

        $batch_id = $this->createBatchID();
        if (!is_array($finish_goods)) $finish_goods = [$finish_goods];
        foreach ($finish_goods as $fg) {
            if (empty($fg)) continue;
            $sql = "INSERT INTO production_batch_master (project_id,batch_id,batch_name,finish_goods,out_from,currency,created_by) 
                    VALUES ('$project_id','$batch_id','$batch_name','$fg','$out_from','$currency_id','$created_by')";
            mysql_query($sql);
        }
        return $batch_id;
    }

    public function createBatchID()
    {
        $sql = "SELECT max(batch_id) as maxvoucher FROM production_batch_master";
        $res = mysql_query($sql);
        $maxvoucherId = 'B0000000';
        if ($res) {
            $row = mysqli_fetch_assoc($res);

            if (!empty($row['maxvoucher'])) {
                $maxvoucherId = $row['maxvoucher'];
            }
        }
        $num = (int)substr($maxvoucherId, 1);
        $num++;
        return 'B' . str_pad($num, 8, '0', STR_PAD_LEFT);
    }

    public function insertPOBatchDetails($batch_id)
    {
        $created_by = getFromSession('userid');
        $project_id = getFromSession('project_id');
        $out_from = $this->getRequest('out_from', '');

        $sql = "SELECT * FROM temp_batch_setup WHERE created_by='$created_by' AND project_id='$project_id'";
        $res = mysql_query($sql);
        if ($res && mysql_num_rows($res) > 0) {
            while ($row = mysql_fetch_assoc($res)) {
                $catagory_id = $row['catagory'];
                $brand_id = $row['brand_id'];
                $product_id = $row['productid'];
                $day_qty = $row['day_qty'];
                $day_wastage_persent = $row['day_wastage_persent'];
                $day_wastage_qty = $row['day_wastage_qty'];
                $total_day = $row['total_day'];
                $ins = "INSERT INTO production_batch_details (batch_id,project_id,out_from,catagory_id,brand_id,product_id,day_qty,day_wastage_persent,day_wastage_qty,total_day,created_by) 
                        VALUES ('$batch_id','$project_id','$out_from','$catagory_id','$brand_id','$product_id','$day_qty','$day_wastage_persent','$day_wastage_qty','$total_day','$created_by')";
                mysql_query($ins);
            }
        }
        mysql_query("DELETE FROM temp_batch_setup WHERE created_by='$created_by' AND project_id='$project_id'");
    }


    public function getAllBatchList()
    {
        $created_by = getFromSession('userid');
        $project_id = getFromSession('project_id');

        $batch_name = $this->getRequest('batch_name', '');
        $from = $this->getRequest('from', 0);
        $to = $this->getRequest('to', 10);
        $page_no = $this->getRequest('page_no', 1);
        if (empty($from)) {
            $from = 0;
        }
        if (empty($to)) {
            $to = 30;
        }
        if (empty($page_no)) {
            $page_no = 1;
        }

        $perPage = 30;
        $offset = ($page_no - 1) * $perPage;

        // Query Master with Join to Product for Finish Goods Name
        $sql = "SELECT bm.*, p.product_name, p.unit_price FROM production_batch_master AS bm ";
        $sql .= "LEFT JOIN product AS p ON p.product_id = bm.finish_goods ";
        $sql .= "WHERE bm.project_id = '$project_id'";

        if ($batch_name != "") {
            $sql .= " AND bm.batch_name LIKE '%$batch_name%' ";
        }

        // Clone for count
        $countSql = $sql;

        $sql .= " ORDER BY bm.batch_name DESC LIMIT $offset, $perPage";
        $query = mysql_query($sql);
        $totalResult = mysql_query($countSql);
        $totalRecord = mysql_num_rows($totalResult);

        $Pagination = $this->ellipsisPagination($totalRecord, $perPage);

        $htmlContent = '<table class="table table-zebra">
                            <thead class="bg-base-200 text-base">
                            <tr>
                                <th>Sl</th>
                                <th>Batch Id</th>
                                <th>Batch Name</th>
                                <th>Finish Goods</th>
                                <th>Total Raw Qty</th>
                                <th>Total Raw Cost</th>
                                <th class="text-center">Options</th>
                            </tr>
                            </thead>
                            <tbody>';

        $sl = $offset;

        if ($query && mysql_num_rows($query) > 0) {
            while ($row = mysql_fetch_assoc($query)) {
                $sl++;
                // Calculate Total Cost
                $cost = 0;
                $batchID = $row['batch_id'];

                // Subquery to get details cost
                $psql = "SELECT pd.*, p.unit_price FROM production_batch_details AS pd 
                          LEFT JOIN product AS p ON p.product_id = pd.product_id 
                          WHERE pd.project_id = '$project_id' AND pd.batch_id = '$batchID'";
                $pquery = mysql_query($psql);
                if ($pquery) {
                    while ($prow = mysql_fetch_assoc($pquery)) {
                        $cost += ($prow['total_day'] * $prow['unit_price']);
                    }
                }

                $htmlContent .= '<tr>
                        <td>' . $sl . '</td>
                        <td>' . $row["batch_id"] . '</td>
                        <td>' . $row["batch_name"] . '</td>
                        <td>' . $row["product_name"] . '</td>
                        <td>' . $row["total_day_qty"] . '</td>
                        <td>' . number_format($cost, 2, '.', '') . '</td>
                        <td>
                            <div class="flex gap-1 items-center justify-center">
                                <button class="btn btn-sm btn-primary" onclick="printWithData(\'' . $row["batch_id"] . '\')">Print</button>
                                <button class="btn btn-sm btn-neutral" onclick="editBatchRecord(\'' . $row["batch_id"] . '\')"><i class="fa-solid fa-pen"></i></button>
                                <button class="btn btn-sm btn-danger" onclick="deleteBatchRecord(\'' . $row["batch_id"] . '\')"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                </tr>';
            }
        } else {
            $htmlContent .= '<tr><td colspan="7" class="text-center">No record found</td></tr>';
        }
        $htmlContent .= '</tbody></table>';

        $data = ["status" => true, "html" => utf8_encode($htmlContent), "pagination" => $Pagination];

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

    public function getBatchInfoById()
    {
        $batch_id = $this->getRequest('batch_id', "");

        if ($batch_id !== "") {
            // Get Master
            $bsql = "SELECT bm.*, p.product_name, d.delivery_point_name as out_from_name FROM production_batch_master AS bm ";
            $bsql .= "LEFT JOIN product AS p ON p.product_id = bm.finish_goods ";
            $bsql .= "LEFT JOIN deliverypoint AS d ON d.delivery_pid = bm.out_from ";
            $bsql .= "WHERE bm.batch_id= '$batch_id'";
            $result = mysql_query($bsql);
            $data = mysql_fetch_assoc($result);

            // Get Details (HTML)
            $htmlData = $this->getBatchDetailsList($batch_id, false);

            return json_encode(["status" => true, "data" => $data, "html" => $htmlData]);
        }
        return json_encode(["status" => false, "message" => "Record id required"]);
    }

    public function getBatchDetailsList($batch_id, $pagination = true)
    {
        $from = $this->getRequest('from', 0);
        $to = $this->getRequest('to', 10);
        $page_no = $this->getRequest('page_no', 1);
        if (empty($from)) {
            $from = 0;
        }
        if (empty($to)) {
            $to = 10;
        }
        if (empty($page_no)) {
            $page_no = 1;
        }


        $perPage = 30;
        $offset = ($page_no - 1) * $perPage;

        $sql = "SELECT bd.*, p.product_name, p.m_unit, p.unit_price FROM production_batch_details AS bd ";
        $sql .= "LEFT JOIN product AS p ON p.product_id = bd.product_id ";
        $sql .= "WHERE bd.batch_id ='$batch_id' ";

        $countSql = $sql;
        if ($pagination) {
            $sql .= " ORDER BY bd.detail_id ASC LIMIT $offset, $perPage";
        } else {
            $sql .= " ORDER BY bd.detail_id ASC";
        }

        $query = mysql_query($sql);
        $totalResult = mysql_query($countSql);
        $totalRecord = mysql_num_rows($totalResult);
        $Pagination = $this->ellipsisPagination($totalRecord, $perPage, "nextPageSec");

        // --- Build Edit Table (HTML) ---
        $htmlContent = '<table class="table table-zebra">
                            <thead class="bg-base-200 text-base">
                            <tr>
                                <th>Sl</th>
                                <th>Product Name</th>
                                <th>Qty(gram)</th>
                                <th>Wastage(%)</th>
                                <th>Qty(kg)</th>
                                <th class="text-center">Options</th>
                            </tr>
                            </thead>
                            <tbody>';

        // --- Build Print Table (HTML) ---
        $printContent = '<table class="table table-zebra">
                            <thead class="bg-base-200 text-base">
                            <tr>
                                <th>Sl</th>
                                <th>Product Name</th>
                                <th>Qty(gram)</th>
                                <th>Wastage(%)</th>
                                <th>Qty(kg)</th>
                                <th width="5%">Cost</th>
                            </tr>
                            </thead>
                            <tbody>';

        $sl = $offset;
        $totalCost = 0;

        if ($query && mysql_num_rows($query) > 0) {
            while ($row = mysql_fetch_assoc($query)) {
                $sl++;
                $qtyVal = number_format($row["day_qty"], 2, '.', '');
                $totalDayVal = number_format($row["total_day"], 2, '.', '') . " " . $row["m_unit"];

                // Edit Row
                $htmlContent .= '<tr>
                        <td>' . $sl . '</td>
                        <td>' . htmlspecialchars($row["product_name"]) . '</td>
                        <td>' . $qtyVal . '</td>
                        <td>' . number_format($row["day_wastage_persent"], 2, '.', '') . '</td>
                        <td>' . $totalDayVal . '</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-primary" onclick="editUpdateRecord(\'' . $row["detail_id"] . '\')">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteUpdateRecord(\'' . $row["detail_id"] . '\')">Del</button>
                        </td>
                </tr>';

                // Print Row
                $unitCost = $row["total_day"] * $row["unit_price"];
                $totalCost += $unitCost;
                $printContent .= '<tr>
                        <td>' . $sl . '</td>
                        <td>' . htmlspecialchars($row["product_name"]) . '</td>
                        <td>' . $qtyVal . '</td>
                        <td>' . number_format($row["day_wastage_persent"], 2, '.', '') . '</td>
                        <td>' . $totalDayVal . '</td>
                        <td>' . number_format($unitCost, 2, '.', '') . ' TK</td>
                </tr>';
            }
        } else {
            $htmlContent .= '<tr><td colspan="6" class="text-center">No details found</td></tr>';
            $printContent .= '<tr><td colspan="6" class="text-center">No details found</td></tr>';
        }

        $htmlContent .= '</tbody></table>';
        $printContent .= '</tbody>
                            <tfoot class="text-base">
                                <tr>
                                    <td colspan="4" class="text-right">Total:</td>
                                    <td class="text-center">' . number_format($totalCost, 2, '.', '') . ' TK</td>
                                </tr>
                            </tfoot>
                            </table>';

        $totalInfo = [
            "TotalDayQty" => 0, // Simplified for brevity
            "TotalNightQty" => 0,
            "TotalDayWastage" => 0,
            "TotalNightWastage" => 0,
        ];

        $data = ["html" => $htmlContent, "printContent" => $printContent, "pagination" => $Pagination, "totalInfo" => $totalInfo];
        return $data;
    }

    public function getBatchDetailsInfoById()
    {
        $detail_id = $this->getRequest('detail_id', "");
        $batch_id = $this->getRequest('batch_id', "");

        if ($detail_id !== "") {
            $sql = "SELECT bd.*, p.product_name, d.delivery_point_name as out_from_name FROM production_batch_details AS bd ";
            $sql .= "LEFT JOIN product AS p ON p.product_id = bd.product_id ";
            $sql .= "LEFT JOIN deliverypoint AS d ON d.delivery_pid = bd.out_from ";
            $sql .= "WHERE bd.batch_id= '$batch_id' AND bd.detail_id = '$detail_id'";
            $result = mysql_query($sql);
            return json_encode(["status" => true, "data" => mysql_fetch_assoc($result)]);
        }
        return json_encode(["status" => false, "message" => "Record id required"]);
    }

    public function addBatchDetail()
    {
        $detail_id = $this->getRequest('detail_id', "");
        $batch_id = $this->getRequest('batch_id', "");

        $created_by = getFromSession('userid');
        $project_id = getFromSession('project_id');

        $product_id = $this->getRequest('product_id', "");
        $qty = $this->getRequest('qty', 0);
        $wastage = $this->getRequest('wastage', 0);
        $wastage_qty = $this->getRequest('wastage_qty', 0);
        $total_qty = $this->getRequest('total_qty', 0);


        // Get Product Info for Category/Brand
        $psql = "SELECT catagory, brand_code FROM product WHERE product_id = '$product_id'";
        $product = mysql_query($psql);
        $catagory_id = "";
        $brand_id = "";
        if ($product && $row = mysql_fetch_assoc($product)) {
            $catagory_id = $row['catagory'];
            $brand_id = $row['brand_code'];
        }

        $day_qty = $qty;
        $day_wastage_persent = $wastage;
        $day_wastage_qty = $wastage_qty;
        $night_qty = $qty; // Logic from old code
        $night_wastage_persent = $wastage;
        $night_wastage_qty = $wastage_qty;
        $total_day = $total_qty;
        $total_night = $total_qty;

        if ($detail_id == "" || $detail_id == 0) {
            // Insert
            $sql = "INSERT INTO production_batch_details (project_id,batch_id,out_from,product_id,catagory_id,brand_id,day_qty,day_wastage_persent,day_wastage_qty,night_qty,night_wastage_persent,night_wastage_qty,total_day,total_night,created_by) 
                    VALUES ('$project_id','$batch_id','','$product_id','$catagory_id','$brand_id','$day_qty','$day_wastage_persent','$day_wastage_qty','$night_qty','$night_wastage_persent','$night_wastage_qty','$total_day','$total_night','$created_by')";
            $res = mysql_query($sql);
            $msg = $res ? "Record has been saved successfully!" : "Record did not saved!";
        } else {
            // Update Detail
            $sql = "UPDATE production_batch_details SET product_id ='$product_id', catagory_id='$catagory_id', brand_id='$brand_id', day_qty='$day_qty', day_wastage_persent='$day_wastage_persent', day_wastage_qty='$day_wastage_qty', night_qty='$night_qty', night_wastage_persent='$night_wastage_persent', night_wastage_qty='$night_wastage_qty', total_day='$total_day', total_night='$total_night', created_by='$created_by' WHERE detail_id = '$detail_id' AND batch_id='$batch_id'";
            $res = mysql_query($sql);
            $msg = $res ? "Record has been edited successfully!" : "Record did not edited!";
        }

        $data = ["status" => ($res ? true : false), "message" => $msg];
        $data['htmlData'] = $this->getBatchDetailsList($batch_id);
        return json_encode($data);
    }

    public function saveBatchMaster()
    {
        $batch_id = $this->getRequest('batch_id', "");

        $created_by = getFromSession('userid');
        $project_id = getFromSession('project_id');

        $batch_name = $this->getRequest('batch_name', "");
        $finish_goods = $this->getRequest('finish_goods', "");
        $currency_id = $this->getRequest('currency_id', "");
        $out_from = $this->getRequest('out_from', "");

        $total_day_wastage = $this->getRequest('total_day_wastage', "");
        $total_day_qty = $this->getRequest('total_day_qty', "");

        // Update Master
        $sql = "UPDATE production_batch_master SET project_id ='" . $project_id . "', batch_name='" . $batch_name . "', finish_goods='" . $finish_goods . "',out_from='" . $out_from . "',currency ='" . $currency_id . "',total_day_qty ='" . $total_day_qty . "',total_day_wastage ='" . $total_day_wastage . "', created_by = '" . $created_by . "' WHERE batch_id = '$batch_id'";

        $result = mysql_query($sql);
        $msg = $result ? "Record has been edited successfully!" : "Record did not edited!";

        $data = ["status" => ($result ? true : false), "message" => $msg];
        $data['htmlData'] = $this->getBatchDetailsList($batch_id);
        return json_encode($data);
    }

    public function deleteBatch()
    {
        $deleted_id = $this->getRequest('deleted_id', "");

        if ($deleted_id !== "") {
            mysql_query("DELETE FROM production_batch_details WHERE batch_id='$deleted_id'");
            mysql_query("DELETE FROM production_batch_master WHERE batch_id='$deleted_id'");
            return json_encode(["status" => true, "message" => "Record deleted successfully!!!"]);
        }
        return json_encode(["status" => false, "message" => "Record id required"]);
    }

    public function deleteBatchDetail()
    {
        $detail_id = $this->getRequest('detail_id', "");
        $batch_id = $this->getRequest('batch_id', "");

        if ($detail_id !== "" && $batch_id !== "") {
            mysql_query("DELETE FROM production_batch_details WHERE detail_id='$detail_id' AND batch_id='$batch_id'");
            return json_encode(["status" => true, "message" => "Record deleted successfully!!!"]);
        }
        return json_encode(["status" => false, "message" => "Record id required"]);
    }


    private function ellipsisPagination($totalRecord, $block, $func = "")
    {
        $callFunc = $func ?: "nextPage";

        // Get the current page and starting record number from the request
        $currentPage = $this->getRequest('page_no', "");
        $currentPage = isset($currentPage) && !empty($currentPage) ? $currentPage : 1;
        $currentPage = $currentPage == "" ? 1 : $currentPage;

        $from = $this->getRequest('from', 0);
        $from = $from == "" ? 0 : $from;

        // Define the number of records per page
        $block = $block ?: 12;

        // Calculate total pages
        $totalPage = ceil($totalRecord / $block);

        // Initialize pagination string
        $paginationStr = "";

        // Previous button
        if ($currentPage > 1) {
            $prevPage = $currentPage - 1;
            $prevFrom = ($prevPage - 1) * $block;
            $paginationStr .= "<button class='join-item pbtn btn' onclick=\"$callFunc($prevFrom, $block, $prevPage)\">&laquo;</button>";
        } else {
            $paginationStr .= "<button class='join-item pbtn btn' disabled>&laquo;</button>";
        }

        // Page buttons with ellipsis
        if ($totalPage <= 7) {
            // Less than 7 total pages, show all
            for ($i = 1; $i <= $totalPage; $i++) {
                $from = ($i - 1) * $block;
                $activeClass = $i == $currentPage ? " btn-active" : "";
                $paginationStr .= "<button class='join-item pbtn btn$activeClass' onclick=\"$callFunc($from, $block, $i)\">$i</button>";
            }
        } else {
            // More than 7 total pages, show partial with ellipsis
            if ($currentPage <= 4) {
                for ($i = 1; $i <= 5; $i++) {
                    $from = ($i - 1) * $block;
                    $activeClass = $i == $currentPage ? " btn-active" : "";
                    $paginationStr .= "<button class='join-item pbtn btn$activeClass' onclick=\"$callFunc($from, $block, $i)\">$i</button>";
                }
                $paginationStr .= "<span class='join-item pbtn btn'>...</span>";
                $from = ($totalPage - 1) * $block;
                $paginationStr .= "<button class='join-item pbtn btn' onclick=\"$callFunc($from, $block, $totalPage)\">$totalPage</button>";
            } elseif ($currentPage > 4 && $currentPage < $totalPage - 3) {
                $paginationStr .= "<button class='join-item pbtn btn' onclick=\"$callFunc(0, $block, 1)\">1</button>";
                $paginationStr .= "<span class='join-item pbtn btn'>...</span>";
                for ($i = $currentPage - 1; $i <= $currentPage + 1; $i++) {
                    $from = ($i - 1) * $block;
                    $activeClass = $i == $currentPage ? " btn-active" : "";
                    $paginationStr .= "<button class='join-item pbtn btn$activeClass' onclick=\"$callFunc($from, $block, $i)\">$i</button>";
                }
                $paginationStr .= "<span class='join-item pbtn btn'>...</span>";
                $from = ($totalPage - 1) * $block;
                $paginationStr .= "<button class='join-item pbtn btn' onclick=\"$callFunc($from, $block, $totalPage)\">$totalPage</button>";
            } else {
                $paginationStr .= "<button class='join-item pbtn btn' onclick=\"$callFunc(0, $block, 1)\">1</button>";
                $paginationStr .= "<span class='join-item pbtn btn'>...</span>";
                for ($i = $totalPage - 4; $i <= $totalPage; $i++) {
                    $from = ($i - 1) * $block;
                    $activeClass = $i == $currentPage ? " btn-active" : "";
                    $paginationStr .= "<button class='join-item pbtn btn$activeClass' onclick=\"$callFunc($from, $block, $i)\">$i</button>";
                }
            }
        }

        // Next button
        if ($currentPage < $totalPage) {
            $nextPage = (int)$currentPage + 1;
            $nextFrom = (int)$currentPage * (int)$block;
            $paginationStr .= "<button class='join-item pbtn btn' onclick=\"$callFunc($nextFrom, $block, $nextPage)\">&raquo;</button>";
        } else {
            $paginationStr .= "<button class='join-item pbtn btn' disabled>&raquo;</button>";
        }

        return $paginationStr;
    }


}

?>
