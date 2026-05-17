<?php

require_once(__DIR__."/../../index.php");
require_once($_SERVER['DOCUMENT_ROOT'] . PROJECT_DIR . '/configs/common/main.conf.php');
require_once($_SERVER['DOCUMENT_ROOT'] . PROJECT_DIR . '/configs/common/database.conf.php');
require_once(CLASS_DIR . '/common.list.class.php');


// Create a connection
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check the connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// get data from database
function getData($sql)
{
    global $connection;

    // Execute the query
    $result = mysqli_query($connection, $sql);

    // Check if the query was successful
    if (!$result) {
        die("Query failed: " . mysqli_error($connection));
    }

    return $result;
}

function executeQuery($sql)
{
    global $connection;

    $result = mysqli_query($connection, $sql);

    if (!$result) {
        die("Query failed: " . mysqli_error($connection));
    }

    return true;
}


/*****************  helper function start  *******************/
const PROJECTID = "P0005";

function getUser()
{
    $created_by = "imran";
    if (function_exists("getRequest")) {
        $created_by = getRequest('userid');
    }

    return $created_by;
}

function getFactoryList()
{
    $project_id = PROJECTID;
    $sql = "SELECT * FROM factory WHERE project_id = '$project_id'";

    $list = '';
    $result = getData($sql);
    if (mysqli_num_rows($result) > 0) {
        while ($item = mysqli_fetch_object($result)) {
            $list .= '<option value="' . $item->factory_id . '">' . $item->factory_name . '</option>';
        }
    }

    return $list;
}

function GetDeliveryPointList($select_point = "")
{
    $project_id = PROJECTID;
    $sql = "SELECT delivery_point_name,delivery_pid FROM deliverypoint WHERE project_id = '$project_id' ORDER BY delivery_point_name ASC";

    $list = '';
    $result = getData($sql);
    if (mysqli_num_rows($result) > 0) {
        while ($item = mysqli_fetch_object($result)) {
            if ($select_point != "" && $select_point == $item['delivery_point_name']) {
                $list .= '<option selected value="' . $item->delivery_pid . '">' . $item->delivery_point_name . '</option>';
            } else {
                $list .= '<option value="' . $item->delivery_pid . '">' . $item->delivery_point_name . '</option>';
            }
        }
    }

    return $list;
}

function GetProductList()
{
    $project_id = PROJECTID;
    $sql = "SELECT product_id,product_code,product_name FROM product WHERE status = 1 AND project_id = '$project_id' ORDER BY product_name ASC";

    $list = '';
    $result = getData($sql);
    if (mysqli_num_rows($result) > 0) {
        while ($item = mysqli_fetch_object($result)) {
            $list .= '<option value="' . $item->product_id . '">' . (new CommonList())->normalizeProductName($item->product_code, $item->product_name) . '</option>';
        }
    }

    return $list;
}

function getFormatDateDMY($originalDate)
{
    if ($originalDate == "") {
        return "";
    }
    return date("Y-m-d", strtotime($originalDate));
}

function ellipsisPagination($totalRecord, $block = 12, $func = "nextPage")
{
    $callFunc = $func ?: "nextPage";

    // Handle POST variables safely
    $currentPage = isset($_POST['page_no']) && !empty(isset($_POST['page_no'])) ? intval($_POST['page_no']) : 1;
    $from = isset($_POST['from']) ? intval($_POST['from']) : 0;
    if ($currentPage == 0) {
        $currentPage = 1;
    }

    $block = $block ?: 12;
    $totalPage = ceil($totalRecord / $block);
    $paginationStr = "";

    // Previous
    if ($currentPage > 1) {
        $prevPage = $currentPage - 1;
        $prevFrom = ($prevPage - 1) * $block;
        $paginationStr .= "<button class='join-item btn' onclick=\"$callFunc($prevFrom, $block, $prevPage)\">&laquo;</button>";
    } else {
        $paginationStr .= "<button class='join-item btn' disabled>&laquo;</button>";
    }

    // Ellipsis logic
    if ($totalPage <= 7) {
        for ($i = 1; $i <= $totalPage; $i++) {
            $from = ($i - 1) * $block;
            $activeClass = $i == $currentPage ? " btn-active" : "";
            $paginationStr .= "<button class='join-item btn$activeClass' onclick=\"$callFunc($from, $block, $i)\">$i</button>";
        }
    } else {
        if ($currentPage <= 4) {
            for ($i = 1; $i <= 5; $i++) {
                $from = ($i - 1) * $block;
                $activeClass = $i == $currentPage ? " btn-active" : "";
                $paginationStr .= "<button class='join-item btn$activeClass' onclick=\"$callFunc($from, $block, $i)\">$i</button>";
            }
            $paginationStr .= "<span class='join-item btn'>...</span>";
            $from = ($totalPage - 1) * $block;
            $paginationStr .= "<button class='join-item btn' onclick=\"$callFunc($from, $block, $totalPage)\">$totalPage</button>";
        } elseif ($currentPage > 4 && $currentPage < $totalPage - 3) {
            $paginationStr .= "<button class='join-item btn' onclick=\"$callFunc(0, $block, 1)\">1</button>";
            $paginationStr .= "<span class='join-item btn'>...</span>";
            for ($i = $currentPage - 1; $i <= $currentPage + 1; $i++) {
                $from = ($i - 1) * $block;
                $activeClass = $i == $currentPage ? " btn-active" : "";
                $paginationStr .= "<button class='join-item btn$activeClass' onclick=\"$callFunc($from, $block, $i)\">$i</button>";
            }
            $paginationStr .= "<span class='join-item btn'>...</span>";
            $from = ($totalPage - 1) * $block;
            $paginationStr .= "<button class='join-item btn' onclick=\"$callFunc($from, $block, $totalPage)\">$totalPage</button>";
        } else {
            $paginationStr .= "<button class='join-item btn' onclick=\"$callFunc(0, $block, 1)\">1</button>";
            $paginationStr .= "<span class='join-item btn'>...</span>";
            for ($i = $totalPage - 4; $i <= $totalPage; $i++) {
                $from = ($i - 1) * $block;
                $activeClass = $i == $currentPage ? " btn-active" : "";
                $paginationStr .= "<button class='join-item btn$activeClass' onclick=\"$callFunc($from, $block, $i)\">$i</button>";
            }
        }
    }

    // Next
    if ($currentPage < $totalPage) {
        $nextPage = $currentPage + 1;
        $nextFrom = ($currentPage) * $block;
        $paginationStr .= "<button class='join-item btn' onclick=\"$callFunc($nextFrom, $block, $nextPage)\">&raquo;</button>";
    } else {
        $paginationStr .= "<button class='join-item btn' disabled>&raquo;</button>";
    }

    return $paginationStr;
}

function createCFGBatchNo()
{
    $sql = "SELECT max(voucher_no) as maxVoucher FROM stock_ledger WHERE voucher_no LIKE 'CFG%'";
    $res = getData($sql);

    $maxVoucherId = 'CFG000000000';
    if ($res && mysqli_num_rows($res) > 0) {
        while ($v = mysqli_fetch_assoc($res)) {
            if ($v['maxVoucher']) {
                $maxVoucherId = $v['maxVoucher'];
            }
            break;
        }
    }
    $maxVoucherId = generateNewID("CFG", $maxVoucherId, 11);
    return $maxVoucherId;
}

function generateNewID($priFix, $maxId, $len)
{
    $nextIdNum = trim($maxId, $priFix) + 1;
    $padlen = $len - (strlen($priFix) + strlen($nextIdNum)) + 1;
    $nextID = str_pad($priFix, $padlen, "0") . $nextIdNum;
    if (strlen($nextID) <= $len) {
        return $nextID;
    } else {
        return "ID over flow !!!";
    }
}

function getStockBalanceQty($acc_head, $project_id, $store_id)
{
    $sql = "SELECT (sum(`dr`) - sum(`cr`)) as balance_qty FROM stock_ledger WHERE product_id = '$acc_head' AND project_id = '$project_id'";
    if ($store_id != "") {
        $sql .= " AND store_id ='$store_id'";
    }
    $result = getData($sql);
    $row = mysqli_fetch_assoc($result);
    $balance_qty = isset($row['balance_qty']) && !empty($row['balance_qty']) ? $row['balance_qty'] : 0;

    return $balance_qty;
}

function saveStockJournal($voucher_no, $pvoucher_no, $project_id, $store_id, $product_id, $product_type, $note, $unit_price, $m_unit, $DR, $CR, $balance, $create_date)
{
    $created_by = getUser();

    $sql = "INSERT INTO stock_ledger (voucher_no,po_no,project_id,store_id,product_id,product_type,note,unit_price,m_unit,dr,cr,balance,created_by,create_date) VALUES('" . $voucher_no . "','" . $pvoucher_no . "','" . $project_id . "','" . $store_id . "','" . $product_id . "','" . $product_type . "','" . $note . "','" . $unit_price . "','" . $m_unit . "','" . $DR . "','" . $CR . "','" . $balance . "','" . $created_by . "','" . $create_date . "')";
    executeQuery($sql);
}

function saveAccJournal($voucher_no, $sub_id, $head_type, $transaction_type, $project_id, $description, $DR, $CR, $balance, $status, $created_date, $delivery_id)
{
    $created_by = getUser();
    if ($delivery_id == "") {
        $delivery_id = 0;
    }
    $sql = "INSERT INTO account_journal (voucher_no,delivery_id,created_date,sub_id,head_type,transaction_type,project_id,description,dr,cr,balance,status,created_by)
	 VALUES('" . $voucher_no . "','" . $delivery_id . "','" . $created_date . "','" . $sub_id . "','" . $head_type . "','" . $transaction_type . "','" . $project_id . "','" . $description . "','" . $DR . "','" . $CR . "','" . $balance . "','" . $status . "','" . $created_by . "')";
    executeQuery($sql);
}

function getTotalBalanceAmount($acc_head, $project_id)
{
    $sql = "SELECT (sum(`dr`) - sum(`cr`)) as balance_amount FROM account_journal WHERE sub_id = '$acc_head' AND project_id = '$project_id'";
    $result = getData($sql);
    $row = mysqli_fetch_assoc($result);

    $balance_amount = isset($row['balance_amount']) && !empty($row['balance_amount']) ? $row['balance_amount'] : 0;
    return $balance_amount;
}

function getWPStockId($project_id)
{
    $sql = "SELECT sub_id FROM sub_acc_head WHERE head_type ='Current Assets' AND `sub_headtype` = 'S127' AND child_head='C000057' AND sl_three_head='S300031' AND project_id = '$project_id' ORDER BY sub_id ASC";
    $result = getData($sql);
    $row = mysqli_fetch_assoc($result);

    if (isset($row['sub_id'])) {
        return $row['sub_id'];
    } else {
        return "A000018";
    }
}

function getMXStockId($project_id)
{
    $sql = "SELECT sub_id FROM sub_acc_head WHERE head_type ='Current Assets' AND `sub_headtype` = 'S127' AND child_head='C000142' AND sl_three_head='S300147' AND project_id = '$project_id' ORDER BY sub_id ASC";
    $result = getData($sql);
    $row = mysqli_fetch_assoc($result);

    if (isset($row['sub_id'])) {
        return $row['sub_id'];
    } else {
        return "A000021";
    }
}

function getFGStockId($project_id)
{
    $sql = "SELECT sub_id FROM sub_acc_head WHERE head_type ='Current Assets' AND `sub_headtype` = 'S127' AND child_head='C000056' AND sl_three_head='S300029' AND project_id = '$project_id' ORDER BY sub_id ASC";
    $result = getData($sql);
    $row = mysqli_fetch_assoc($result);

    if (isset($row['sub_id'])) {
        return $row['sub_id'];
    } else {
        return "A000036";
    }
}


// Handle AJAX request to get subcategories
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json; charset=UTF-8');
    if (ob_get_length()) ob_end_clean();

    if ($_POST['action'] === 'get_customize_production_list') {
        $perPage = 30;

        $from = isset($_POST['from']) ? intval($_POST['from']) : 0;
        $to = isset($_POST['to']) ? intval($_POST['to']) : $perPage;

        $project_id = PROJECTID;
        $created_by = getUser();

        if ($from == "") {
            $from = 0;
        }
        if ($to == "") {
            $to = $perPage;
        }

        $sql = "SELECT tcp.*,p.product_name,p.m_unit,np.product_name as new_product,np.m_unit as new_m_unit,f.factory_name,d.delivery_point_name as in_store,od.delivery_point_name as out_store FROM temp_customize_production as tcp";
        $sql .= " LEFT JOIN product as p ON p.product_id = tcp.product_id";
        $sql .= " LEFT JOIN product as np ON np.product_id = tcp.new_product_id";
        $sql .= " LEFT JOIN factory as f ON f.factory_id = tcp.factory_id";
        $sql .= " LEFT JOIN deliverypoint as d ON d.delivery_pid = tcp.in_store_id";
        $sql .= " LEFT JOIN deliverypoint as od ON od.delivery_pid = tcp.out_store_id";
        $sql .= " WHERE tcp.project_id='$project_id' AND tcp.created_by = '$created_by' AND tcp.status = '0'";

        // Get total records for pagination
        $totalResult = getData($sql); // assume this returns mysqli_result
        $totalRecord = mysqli_num_rows($totalResult);

        // Now limit for page
        $sql .= " ORDER BY tcp.id DESC LIMIT $from,$to";
        $result = getData($sql);

        $pagination = "";
        if ($totalRecord > 0) {
            $pagination = ellipsisPagination($totalRecord, $perPage);
        }

        $sl = $from;
        $htmlContent = '<table class="table table-zebra">
                            <thead class="bg-base-200 text-base" >
                            <tr>
                                <th>Sl</th>
                                <th>Date</th>
                                <th>Factory Name</th>
                                <th>Shift</th>
                                <th>In Store</th>
                                <th>Out Store</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>New Product</th>
                                <th>Qty</th>
                                <th class="text-center">Options</th>
                            </tr>
                            </thead>
                            <tbody>';

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $sl++;
                $printBtn = '<button
                                        class="btn bg-base-100 text-sm text-base-content border border-base-300"
                                      
                                        title="Print"
                                        onclick=printContent("' . $row["id"] . '")
                                >
                                    <i class="fa-solid fa-file-circle-question"></i>
                                </button>';
                $editBtn = '<button
                                        class="btn bg-base-100 text-sm text-base-content border border-base-300"
                                        type="button"
                                        title="Edit"
                                        onclick=handleEdit("' . $row["id"] . '")
                                >
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>';
                $deleteBtn = '<button
                                class="btn bg-base-100 text-sm text-base-content border border-base-300"
                                onclick=deleteProductionRecord("' . $row["id"] . '")
                                title="Delete"
                                >
                                    <i class="fa-solid fa-trash"></i>
                                </button>';

                $htmlContent .= '<tr>
                        <td> ' . $sl . '</td>
                        <td>' . $row["production_date"] . '</td>
                        <td>' . $row["factory_name"] . '</td>
                        <td>' . $row["shift"] . '</td>
                        <td>' . $row["in_store"] . '</td>
                        <td>' . $row["out_store"] . '</td>
                        <td>' . $row["product_name"] . '</td>
                        <td>' . $row["product_qty"] . " " . $row["m_unit"] . '</td>
                        <td>' . $row["new_product"] . '</td>
                        <td>' . $row["new_product_qty"] . " " . $row["new_m_unit"] . '</td>
                        <td>
                            <div class="flex gap-1 items-center justify-center">
                                ' . $editBtn . '
                                ' . $deleteBtn . '
                            </div>
                        </td>
                </tr>';
            }
        } else {
            $htmlContent .= '<tr><td colspan = "11" class="text-center"> No record found </td></tr>';
        }

        $htmlContent .= '</tbody></table>';

        echo json_encode([
            "status" => true,
            "data" => [
                "html" => $htmlContent,
                "pagination" => $pagination
            ]
        ]);
        exit;

    }

    if ($_POST['action'] === 'add_customize_production') {
        $project_id = PROJECTID;
        $factory_id = isset($_POST['factory_id']) ? $_POST['factory_id'] : '';
        $production_date = isset($_POST['production_date']) ? $_POST['production_date'] : '';
        $in_store_id = isset($_POST['in_stock']) ? $_POST['in_stock'] : '';
        $out_store_id = isset($_POST['out_stock']) ? $_POST['out_stock'] : '';
        $shift = isset($_POST['shift']) ? $_POST['shift'] : '';
        $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : '';
        $product_qty = isset($_POST['product_qty']) ? $_POST['product_qty'] : '';
        $new_product_id = isset($_POST['new_product_id']) ? $_POST['new_product_id'] : '';
        $new_product_qty = isset($_POST['new_product_qty']) ? $_POST['new_product_qty'] : '';
        $edit_id = isset($_POST['edit_id']) ? $_POST['edit_id'] : '';

        $created_by = getUser();

        if ($factory_id == "" || $production_date == "" || $in_store_id == "" || $out_store_id == "" || $shift == "" || $product_id == "" || $product_qty == "" || $new_product_id == "" || $new_product_qty == "") {
            echo json_encode(["status" => false, "message" => "Field must not be empty!"]);
            exit;
        }

        $status = false;
        $attr = "saved";
        $message = "Record did not $attr!";
        try {
            if ($edit_id != "") {
                $updateSQL = "UPDATE temp_customize_production SET
                            project_id ='$project_id',
                            factory_id='$factory_id',
                            in_store_id='$in_store_id',
                            out_store_id ='$out_store_id',
                            shift ='$shift',
                            production_date ='$production_date',
                            product_id ='$product_id',
                            product_qty ='$product_qty',
                            new_product_id ='$new_product_id',
                            new_product_qty ='$new_product_qty'
                             WHERE id = '$edit_id'";

                executeQuery($updateSQL);
                $attr = "updated";
            } else {
                $insertSQL = "INSERT INTO temp_customize_production (project_id,factory_id,in_store_id,out_store_id,shift,production_date,product_id,product_qty,new_product_id,new_product_qty,created_by) VALUES('" . $project_id . "','" . $factory_id . "','" . $in_store_id . "','" . $out_store_id . "','" . $shift . "','" . $production_date . "','" . $product_id . "','" . $product_qty . "','" . $new_product_id . "','" . $new_product_qty . "','" . $created_by . "')";

                executeQuery($insertSQL);
                $attr = "saved";
            }
            $status = true;
            $message = "Record has been $attr successfully!";
        } catch (\Exception $exception) {
            $status = false;
            $message = "Process did not complete please try again!!";
        }

        echo json_encode(["status" => $status, "message" => $message]);
        exit;
    }

    if ($_POST['action'] === 'get_customize_production_info') {
        $edit_id = isset($_POST['edit_id']) ? $_POST['edit_id'] : '';
        $project_id = PROJECTID;

        if ($edit_id == "") {
            echo json_encode(["status" => false, "message" => "Production ID missing!"]);
            exit;
        }

        $sql = "SELECT tcp.*,p.product_name,p.m_unit,np.product_name as new_product,np.m_unit as new_m_unit,f.factory_name,d.delivery_point_name as in_store,od.delivery_point_name as out_store FROM temp_customize_production as tcp";
        $sql .= " LEFT JOIN product as p ON p.product_id = tcp.product_id";
        $sql .= " LEFT JOIN product as np ON np.product_id = tcp.new_product_id";
        $sql .= " LEFT JOIN factory as f ON f.factory_id = tcp.factory_id";
        $sql .= " LEFT JOIN deliverypoint as d ON d.delivery_pid = tcp.in_store_id";
        $sql .= " LEFT JOIN deliverypoint as od ON od.delivery_pid = tcp.out_store_id";
        $sql .= " WHERE tcp.id = '$edit_id'";

        $result = getData($sql);
        $data = mysqli_fetch_object($result);

        echo json_encode(["status" => true, "message" => "Successful", "data" => $data]);
        exit;
    }

    if ($_POST['action'] === 'delete_production_customize_record') {
        $deleted_id = isset($_POST['deleted_id']) ? $_POST['deleted_id'] : "";
        $project_id = PROJECTID;

        if ($deleted_id !== "" && $project_id !== "") {
            mysqli_begin_transaction($connection);

            try {
                $sql = "DELETE FROM temp_customize_production WHERE id = '$deleted_id' AND project_id='$project_id'";
                executeQuery($sql);

                mysqli_commit($connection);
                echo json_encode(["status" => true, "message" => "Record deleted successfully!!!"]);
                exit;
            } catch (\Exception $e) {
                mysqli_rollback($connection);
                echo json_encode(["status" => false, "message" => "Record did not deleted. Try again!"]);
                exit;
            }
        } else {
            echo json_encode(["status" => false, "message" => "Record id required"]);
            exit;
        }
    }

    if ($_POST['action'] === 'save_customize_production') {
        $project_id = PROJECTID;
        $created_by = getUser();

        if ($created_by !== "") {
            mysqli_begin_transaction($connection);

            try {
                $sql = "SELECT * FROM temp_customize_production WHERE created_by='$created_by'";
                $result = getData($sql);

                if ($result && mysqli_num_rows($result) > 0) {
                    while ($item = mysqli_fetch_assoc($result)) {
                        $tcpId = $item['id'];

                        $sql = "SELECT tcp.*,p.product_name,p.m_unit,p.unit_price,np.product_name as new_product,np.m_unit as new_m_unit,np.unit_price as new_unit_price,f.factory_name,d.delivery_point_name as in_store,od.delivery_point_name as out_store FROM temp_customize_production as tcp";
                        $sql .= " LEFT JOIN product as p ON p.product_id = tcp.product_id";
                        $sql .= " LEFT JOIN product as np ON np.product_id = tcp.new_product_id";
                        $sql .= " LEFT JOIN factory as f ON f.factory_id = tcp.factory_id";
                        $sql .= " LEFT JOIN deliverypoint as d ON d.delivery_pid = tcp.in_store_id";
                        $sql .= " LEFT JOIN deliverypoint as od ON od.delivery_pid = tcp.out_store_id";
                        $sql .= " WHERE tcp.id = '$tcpId'";

                        $result = getData($sql);
                        $data = mysqli_fetch_object($result);
                        $production_date = $data->production_date;

                        // Old product
                        $production_id = createCFGBatchNo();
                        $old_product_id = $data->product_id;
                        $old_product_qty = $data->product_qty;
                        $out_store = $data->out_store_id;
                        $unit_price = $data->unit_price;
                        $m_unit = $data->m_unit;
                        $old_product_name = $data->product_name;

                        // Stock
                        $balanceQty = getStockBalanceQty($old_product_id, $project_id, $out_store);

                        if ($balanceQty < $old_product_qty) {
                            $stockMsg = "($old_product_name) Stock quantity is lower than Production Quantity";

                            mysqli_rollback($connection);
                            echo json_encode(["status" => false, "message" => $stockMsg]);
                            exit;
                        }

                        $balanceF = ($balanceQty - $old_product_qty);
                        saveStockJournal($production_id, "", $project_id, $out_store, $old_product_id, "Raw Materials", "Production", $unit_price, $m_unit, 0, $old_product_qty, $balanceF, $production_date);

                        $StockId = "A000017";
                        $StockAmount = ((float)$unit_price * (float)$old_product_qty);
                        $StockPvBalance = getTotalBalanceAmount($StockId, $project_id);
                        $StockBalance = ($StockPvBalance - $StockAmount);
                        $description = "Item Transfer";
                        saveAccJournal($production_id, $StockId, "Stock", "Used Raw Materials", $project_id, $description, 0, $StockAmount, $StockBalance, 0, $production_date, "");

                        // new product
                        $production_id = createCFGBatchNo();
                        $new_product_id = $data->new_product_id;
                        $new_product_qty = $data->new_product_qty;
                        $in_store = $data->in_store_id;
                        $new_unit_price = $data->new_unit_price;
                        $new_m_unit = $data->new_m_unit;

                        // Stock
                        $balanceQty = getStockBalanceQty($new_product_id, $project_id, $in_store);
                        $balanceF = ($balanceQty + $new_product_qty);
                        saveStockJournal($production_id, "", $project_id, $in_store, $new_product_id, "Sales Item", "Production", $new_unit_price, $new_m_unit, $new_product_qty, 0, $balanceF, $production_date);

                        // Account journal
                        if ($in_store == "D0026") {
                            $StockId = getWPStockId($project_id);
                        } elseif ($in_store == "D0027") {
                            $StockId = getMXStockId($project_id);
                        } else {
                            $StockId = getFGStockId($project_id);
                        }
                        $StockAmount = ((float)$new_unit_price * (float)$new_product_qty);
                        $StockPvBalance = getTotalBalanceAmount($StockId, $project_id);
                        $StockBalance = ($StockPvBalance + $StockAmount);
                        $description = "FGP";
                        saveAccJournal($production_id, $StockId, "Stock", "Finish Goods", $project_id, $description, $StockAmount, 0, $StockBalance, 0, $production_date, "");

                        // Delete temp item
                        $deleteSQL = "DELETE FROM temp_customize_production WHERE id = '$tcpId'";
                        executeQuery($deleteSQL);
                    }
                }

                mysqli_commit($connection);
                echo json_encode(["status" => true, "message" => "Process has been completed successfully!!"]);
                exit;
            } catch (\Exception $e) {
                mysqli_rollback($connection);
                echo json_encode(["status" => false, "message" => "Process did not completed. Try again!"]);
                exit;
            }
        } else {
            echo json_encode(["status" => false, "message" => "User id missing!"]);
            exit;
        }
    }

}

?>

<link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
/>

<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.4/dist/sweetalert2.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet"/>
<link rel="stylesheet" type="text/css"
      href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.8/dist/sweetalert2.all.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/uuid/dist/umd/uuidv4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<link rel="stylesheet" href="<?=REL_CONTENT_DIR?>/css/customize_production.css" type="text/css">


<div class="bg-base-200" style="height:fit-content;">
    <div class="main-sec" style="padding-top: 40px;width:100%;height:100%">
        <div class="p-6 shadow-md bg-base-100 rounded-xl">
            <div id="danger-alert" style="display: none"
                 class="flex items-center p-4 mb-7 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800"
                 role="alert">

            </div>

            <div id="alert" style="display: none"
                 class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 dark:border-green-800"
                 role="alert">

            </div>
            <h1 class="mb-3 text-2xl font-bold">Customize Production/Inter Transfer</h1>
            <form action="">
                <div class="flex flex-wrap items-center gap-2">
                    <label class="form-control w-full min-w-[150px] flex-1">
                        <div class="label">
                            <span class="label-text">Production Factory</span>
                        </div>
                        <select class="custom-select factory_list" id="factory_id">
                            <option disabled selected value="">Select one</option>
                            <?= getFactoryList(); ?>
                        </select>
                    </label>
                    <label class="form-control w-full min-w-[200px] flex-1">
                        <div class="label">
                            <span class="label-text">Production Date</span>
                        </div>
                        <input
                                type="date"
                                placeholder="Type here"
                                class="custom-input"
                                id="production_date"
                        />
                    </label>
                    <label class="form-control w-full min-w-[150px] flex-1">
                        <div class="label">
                            <span class="label-text">Production Shift</span>
                        </div>
                        <select class="custom-select" id="shift">
                            <option disabled selected value="">Select Shift Name</option>
                            <option value="Day">Day</option>
                            <option value="Night">Night</option>
                        </select>
                    </label>
                    <label class="form-control w-full min-w-[150px] flex-1 select2Parent2">
                        <div class="label">Stock In Wearhouse</div>
                        <select class="custom-select inStock_list" id="inStock">
                            <option value="" selected>Select one</option>
                            <?= GetDeliveryPointList(); ?>
                        </select>
                    </label>
                    <label class="form-control w-full min-w-[150px] flex-1 select2Parent3">
                        <div class="label">Stock Out Wearhouse</div>
                        <select class="custom-select outStock_list" id="outStock">
                            <option value="" selected>Select one</option>
                            <?= GetDeliveryPointList(); ?>
                        </select>
                    </label>
                </div>
                <div class="p-4 mt-8">
                    <div class="mb-1 text-lg">Production Details</div>
                    <div class="flex flex-wrap items-end gap-2">
                        <label class="form-control w-full min-w-[150px] flex-1 select2Parent4">
                            <div class="label">Product Out</div>
                            <select class="custom-select product_list" id="product_id">
                                <option value="" selected>Select Product</option>
                                <?= GetProductList(); ?>
                            </select>
                        </label>
                        <label class="form-control w-full min-w-[150px] flex-1">
                            <div class="label">
                                <span class="label-text">Out Product Qty</span>
                            </div>
                            <input
                                    type="number"
                                    placeholder="1"
                                    class="custom-input"
                                    id="product_qty"
                                    value="1"
                                    min="1"
                            />
                        </label>
                        <label class="form-control w-full min-w-[150px] flex-1 select2Parent5">
                            <div class="label">Product In</div>
                            <select class="custom-select new_product_list" id="new_product_id">
                                <option value="" selected>Select Product</option>
                                <?= GetProductList(); ?>
                            </select>
                        </label>
                        <label class="form-control w-full min-w-[150px] flex-1">
                            <div class="label">
                                <span class="label-text">In Product Qty</span>
                            </div>
                            <input
                                    type="number"
                                    placeholder="1"
                                    class="custom-input"
                                    id="new_product_qty"
                                    value="1"
                                    min="1"
                            />
                        </label>

                        <input type="hidden" name="edit_id" id="edit_id" value=""/>
                        </label>
                        <button id="addItem" class="btn btn-primary" type="button">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="p-6 mt-5 shadow-md bg-base-100 rounded-xl">
            <input type="hidden" id="frm" value=""/>
            <input type="hidden" id="to" value=""/>
            <input type="hidden" id="pno" value=""/>
            <div class="flex flex-wrap w-full mb-2">
                <h2 class="mb-3 text-xl font-bold">Batch</h2>
                <div class="join shadow-md ml-auto mr-auto sm:mr-0" id="paginationList">

                </div>
            </div>
            <div class="overflow-x-auto" id="tableData">
                <table class="table table-zebra">
                    <!-- head -->
                    <thead class="text-base bg-base-200">
                    <tr>
                        <th>Sl</th>
                        <th>Date</th>
                        <th>Factory Name</th>
                        <th>Shift</th>
                        <th>In Store</th>
                        <th>Out Store</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>New Product</th>
                        <th>Qty</th>
                        <th class="text-center">Options</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="text-center">
                        <th colspan="11">No record found</th>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex gap-1 mt-3">
                <button class="btn btn-primary" id="handleSubmit" type="submit">Submit</button>
                <button class="btn btn-neutral" id="reset" type="reset">Clear</button>
            </div>
        </div>
    </div>
</div>



<script>
    jQuery.noConflict();
    (function ($) {
        const todayDate = () => {
            let date = new Date().toLocaleDateString();
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2)
                month = '0' + month;
            if (day.length < 2)
                day = '0' + day;

            return [year, month, day].join('-');
        }

        // Get Product Stock list
        const getAllProductionList = (frm = "", to = "", pno = "") => {
            const formData = new URLSearchParams();
            formData.append('action', 'get_customize_production_list');
            formData.append('from', frm);
            formData.append('to', to);
            formData.append('page_no', pno);

            axios.post("", formData).then(function (response) {
                if (response.data.status) {
                    $("#tableData").html(response.data.data.html);
                    $('#paginationList').html(response.data.data.pagination);
                }
            })
        }

        $(document).ready(function () {
            $('#production_date').val(todayDate());
            $('#alert').hide();
            $('#danger-alert').hide();
            getAllProductionList();

            $(".inStock_list").select2({
                width: '100%',
                dropdownParent: $('.select2Parent2')
            });

            $(".outStock_list").select2({
                width: '100%',
                dropdownParent: $('.select2Parent3')
            });

            $(".product_list").select2({
                width: '100%',
                dropdownParent: $('.select2Parent4')
            });

            $(".new_product_list").select2({
                width: '100%',
                dropdownParent: $('.select2Parent5')
            });
        });

        // Reset all field
        $("#reset").click(function (e) {
            e.preventDefault();

            $('#alert').hide();
            $('#danger-alert').hide();

            clearAllField();
        })

        const clearfield = () => {
            $('#product_id').val("").trigger("change");
            $('#new_product_id').val("").trigger("change");
            $('#product_qty').val("1");
            $('#new_product_qty').val("1");
            $('#edit_id').val("");
        }

        const clearAllField = () => {
            $("#production_date").val(todayDate());

            $('#factory_id').val("");
            $('#shift').val("");

            $('#inStock').val("").trigger("change");
            $('#outStock').val("").trigger("change");

            $('#product_id').val("").trigger("change");
            $('#new_product_id').val("").trigger("change");
            $('#product_qty').val("1");
            $('#new_product_qty').val("1");
            $('#edit_id').val("");
        }

        // Add item
        $("#addItem").click(function (e) {
            e.preventDefault();
            $("#addItem").prop("disabled", true);

            let factory_id = document.getElementById('factory_id').value;
            let production_date = document.getElementById('production_date').value;
            let shift = document.getElementById('shift').value;
            let in_stock = document.getElementById('inStock').value;
            let out_stock = document.getElementById('outStock').value;

            let product_id = document.getElementById('product_id').value;
            let product_qty = document.getElementById('product_qty').value;
            let new_product_id = document.getElementById('new_product_id').value;
            let new_product_qty = document.getElementById('new_product_qty').value;

            let edit_id = $("#edit_id").val();

            const formData = new URLSearchParams();
            formData.append('action', 'add_customize_production');
            formData.append('factory_id', factory_id);
            formData.append('production_date', production_date);
            formData.append('shift', shift);
            formData.append('in_stock', in_stock);
            formData.append('out_stock', out_stock);
            formData.append('product_id', product_id);
            formData.append('product_qty', product_qty);
            formData.append('new_product_id', new_product_id);
            formData.append('new_product_qty', new_product_qty);
            formData.append('edit_id', edit_id);

            axios.post("", formData).then(function (response) {
                if (response.data.status) {
                    $("#addItem").prop("disabled", false);
                    $('#danger-alert').hide();
                    $('#alert').show();
                    $('#alert').html(response.data.message);
                    const frm = $('#frm').val();
                    const to = $('#to').val();
                    const pno = $('#pno').val();
                    getAllProductionList(frm, to, pno);
                    clearfield();
                } else {
                    $("#addItem").prop("disabled", false);
                    $('#alert').hide();
                    $('#danger-alert').show();
                    $('#danger-alert').html(response.data.message);
                }
            })
        })

        // Save All Item
        $("#handleSubmit").click(function (e) {
            e.preventDefault();
            $("#handleSubmit").prop("disabled", true);

            const formData = new URLSearchParams();
            formData.append('action', 'save_customize_production');

            axios.post("", formData).then(function (response) {
                if (response.data.status) {
                    $("#handleSubmit").prop("disabled", false);
                    $('#danger-alert').hide();
                    $('#alert').show();
                    $('#alert').html(response.data.message);
                    const frm = $('#frm').val();
                    const to = $('#to').val();
                    const pno = $('#pno').val();
                    clearAllField();
                    getAllProductionList(frm, to, pno);
                } else {
                    $("#handleSubmit").prop("disabled", false);
                    $('#alert').hide();
                    $('#danger-alert').show();
                    $('#danger-alert').html(response.data.message);
                }
            })
        })

        /* Pagination Next Page */
        window.nextPage = (frm, to, pno) => {
            $('#frm').val(frm);
            $('#to').val(to);
            $('#pno').val(pno);
            getAllProductionList(frm, to, pno);
            return false;
        }

        window.deleteProductionRecord = (id) => {
            Swal.fire({
                title: "Are you sure?",
                text: "You want to delete this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteRecord(id);
                }
            });

            return false;
        }

        const deleteRecord = (deleted_id) => {
            $('#alert').hide();
            $('#danger-alert').hide();

            if (deleted_id != "") {
                const formData = new URLSearchParams();
                formData.append('action', 'delete_production_customize_record');
                formData.append('deleted_id', deleted_id);

                axios.post("", formData).then(function (response) {
                    if (response.data.status == true) {
                        $('#alert').show(1000);
                        $('#alert').html('Record deleted successfully!!!');
                        const frm = $('#frm').val();
                        const to = $('#to').val();
                        const pno = $('#pno').val();
                        getAllProductionList(frm, to, pno);
                    } else {
                        $('#danger-alert').show(1000);
                        $('#danger-alert').html('Record did not deleted. Try again!');
                    }
                })
                return false;
            }
        }

        window.handleEdit = (id) => {
            const formData = new URLSearchParams();
            formData.append('action', 'get_customize_production_info');
            formData.append('edit_id', id);

            axios.post("", formData).then(function (response) {
                const data = response.data.data || [];
                $("#edit_id").val(data.id);
                $("#production_date").val(data.production_date);
                $('#factory_id').val(data.factory_id);
                $('#shift').val(data.shift);
                $('#inStock').val(data.in_store_id).trigger("change");
                $('#outStock').val(data.out_store_id).trigger("change");

                $('#product_id').val(data.product_id).trigger("change");
                $('#product_qty').val(data.product_qty);

                $('#new_product_id').val(data.new_product_id).trigger("change");
                $('#new_product_qty').val(data.new_product_qty);
            });
        }

    })(jQuery);
</script>



