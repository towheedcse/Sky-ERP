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


/*****************  helper function start  *******************/
const PROJECTID = "P0005";

function GetDeliveryPointList()
{
    $project_id = PROJECTID;
    $sql = "SELECT delivery_point_name,delivery_pid FROM deliverypoint WHERE project_id = '$project_id' ORDER BY delivery_point_name ASC";

    $list = '';
    $result = getData($sql);
    if (mysqli_num_rows($result) > 0) {
        while ($item = mysqli_fetch_object($result)) {
            $list .= '<option value="' . $item->delivery_pid . '">' . $item->delivery_point_name . '</option>';
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


function GetCategoryList()
{
    $project_id = PROJECTID;
    $sql = "SELECT * FROM catagory WHERE project_id = '$project_id' ORDER BY catagory_name ASC";

    $list = '';
    $result = getData($sql);
    if (mysqli_num_rows($result) > 0) {
        while ($item = mysqli_fetch_object($result)) {
            $list .= '<option value="' . $item->catagory_code . '">' . $item->catagory_name . '</option>';
        }
    }

    return $list;
}

function GetCustomerList()
{
    $project_id = PROJECTID;

    $sql = "SELECT * FROM sub_acc_head WHERE project_id = '$project_id' AND head_type = 'Current Assets' AND sub_headtype = 'S128' AND child_head = 'C000105' ORDER BY sub_head_name ASC";


    $result = getData($sql);
    if (mysqli_num_rows($result) > 0) {
        while ($item = mysqli_fetch_object($result)) {

	   $sub_id = $item->sub_id;	
	   $sub_head_name = $item->sub_head_name;	
	   $code = $item->code;

  	   $sub_head_name = normalizeUserName($code,$sub_head_name);
            
	   $list .= '<option value="' . $sub_id . '">' . $sub_head_name . '</option>';
        }
    }

    $sql2 = "SELECT * FROM sub_acc_head WHERE project_id = '$project_id' AND head_type = 'Current Assets' AND sub_headtype = 'S147' ORDER BY sub_head_name ASC";

    $result2 = getData($sql2);
    if (mysqli_num_rows($result2) > 0) {
        while ($item = mysqli_fetch_object($result2)) {
	   $sub_id = $item->sub_id;	
	   $sub_head_name = $item->sub_head_name;	
	   $code = $item->code;

  	   $sub_head_name = normalizeUserName($code,$sub_head_name);
            
	   $list .= '<option value="' . $sub_id . '">' . $sub_head_name . '</option>';
        }
    }

    return $list;
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

function dayDiff($startDate, $endDate)
{
    $start = new DateTime($startDate);
    $end   = new DateTime($endDate);

    // Ensure aging date is always after sales date
    if ($end < $start) {
        return "-"; // or you can return an error message
    }

    $diff = $start->diff($end);
    return $diff->days; // returns total number of days difference
}

function daysPastDue($agingDate)
{
    // If aging date is empty or invalid
    if ($agingDate == "0000-00-00" || empty($agingDate)) {
        return "-"; // No due date
    }

    $today = new DateTime();           // Current date
    $aging = new DateTime($agingDate); // Last payment date

    // If today is before or equal to aging date → no past due
    if ($today <= $aging) {
        return "-";
    }

    // Calculate difference
    $diff = $aging->diff($today);
    return $diff->days;
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
    $currentPage = isset($_POST['page_no']) && !empty($_POST['page_no']) ? intval($_POST['page_no']) : 1;
    $from = isset($_POST['from']) ? intval($_POST['from']) : 0;

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




// Handle AJAX request to get subcategories
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json; charset=UTF-8');
    if (ob_get_length()) ob_end_clean();

    if ($_POST['action'] === 'get_subcategories') {
        $category_code = isset($_POST['category_code']) ? $_POST['category_code'] : '';
        $project_id = PROJECTID;

        $list = '<option value="" selected>Select Sub Category</option>';

        if ($category_code !== '') {
            $sql = "SELECT * FROM subcatagory WHERE catagory_id = '$category_code' AND project_id = '$project_id' ORDER BY subcatagory_name ASC";
            $result = getData($sql);

            if (mysqli_num_rows($result) > 0) {
                while ($item = mysqli_fetch_object($result)) {
                    $list .= '<option value="' . $item->subcatagory_id . '">' . $item->subcatagory_name . '</option>';
                }
            }
        }

        echo json_encode(['data' => $list]);
        exit;
    }

    if ($_POST['action'] === 'update_aging_date') {
        $aging_date = isset($_POST['aging_date']) ? $_POST['aging_date'] : '';
        $voucher_no = isset($_POST['voucher_no']) ? $_POST['voucher_no'] : '';
        $project_id = PROJECTID;

        if (empty($voucher_no)) {
            echo json_encode(['status' => false, 'message' => 'Voucher number missing']);
            exit;
        }

        if (empty($aging_date)) {
            echo json_encode(['status' => false, 'message' => 'Aging Date missing']);
            exit;
        }

        // Convert date to Y-m-d if needed
        if (!empty($aging_date)) {
            $aging_date = date('Y-m-d', strtotime($aging_date));
        }

        // Escape values (IMPORTANT for security)
        $voucher_no = mysqli_real_escape_string($connection, $voucher_no);
        $project_id = mysqli_real_escape_string($connection, $project_id);

        if (!empty($aging_date)) {
            $sql = "UPDATE sales_master SET aging_date = '$aging_date' WHERE voucher_no = '$voucher_no' AND project_id = '$project_id'";
        }

        getData($sql);

        echo json_encode(['status' => true, "message" => "Success"]);
        exit;
    }




    if ($_POST['action'] === 'get_aging_report') {
        $perPage = 100;

        $from = isset($_POST['from']) ? intval($_POST['from']) : 0;
        $to = isset($_POST['to']) ? intval($_POST['to']) : $perPage;

        if ($from == "") {
            $from = 0;
        }
        if ($to == "") {
            $to = $perPage;
        }

        $date_from = !empty($_POST['date_from']) ? getFormatDateDMY($_POST['date_from']) : '';
        $date_to = !empty($_POST['date_to']) ? getFormatDateDMY($_POST['date_to']) : '';

        //$category = isset($_POST['category_id']) ? $_POST['category_id'] : '';
        //$subcategory = isset($_POST['subCategory_id']) ? $_POST['subCategory_id'] : '';
        //$delivery_point = isset($_POST['delivery_point_id']) ? $_POST['delivery_point_id'] : '';
        $customer_id = isset($_POST['customer_id']) ? $_POST['customer_id'] : '';
        $project_id = PROJECTID;

	$where = "m.status = 1 AND m.project_id ='$project_id'";

        
        if ($customer_id != "") {
            $where .= " AND m.customer = '$customer_id'";
        }
        if ($date_from && !$date_to) {
            $where .= " AND m.sales_date >= '$date_from'";
        } elseif (!$date_from && $date_to) {
            $where .= " AND m.sales_date <= '$date_to'";
        } elseif ($date_from && $date_to) {
            $where .= " AND m.sales_date BETWEEN '$date_from' AND '$date_to'";
        }

	// Filter only valid aging_date
	$where .= " AND m.aging_date != '0000-00-00'";

        $sql = "SELECT m.project_id,p.project_name,p.location,m.sales_date as sales_date,m.voucher_no as invoice,m.net_payble,aj_last.cr as paid_amount,m.aging_date, c.sub_head_name as party_name, c.code as party_code, c.sub_id
            FROM sales_master AS m  
            LEFT JOIN project AS p ON p.project_id = m.project_id 
            LEFT JOIN sub_acc_head AS c ON m.customer = c.sub_id 
    	    LEFT JOIN (
    SELECT aj1.sub_id, aj1.cr
    FROM account_journal aj1
    INNER JOIN (
        SELECT sub_id, MAX(created_date) AS last_date
        FROM account_journal
        WHERE transaction_type = 'Received'
          AND created_date BETWEEN '$date_from' AND '$date_to'
        GROUP BY sub_id
    ) aj2 ON aj1.sub_id = aj2.sub_id AND aj1.created_date = aj2.last_date
    WHERE aj1.transaction_type = 'Received'
) aj_last ON aj_last.sub_id = c.sub_id
            WHERE $where";

        $grandSql = "SELECT SUM(m.net_payble) AS grand_net,
        SUM(m.paid_amount) AS grand_paid,
        SUM(m.net_payble - m.paid_amount) AS grand_outstanding, m.project_id,p.project_name,p.location,m.sales_date as sales_date,m.voucher_no as invoice,m.net_payble,aj_last.cr as paid_amount,m.aging_date, c.sub_head_name as party_name, c.code as party_code, c.sub_id
            FROM sales_master AS m  
            LEFT JOIN project AS p ON p.project_id = m.project_id 
            LEFT JOIN sub_acc_head AS c ON m.customer = c.sub_id  
    	    LEFT JOIN (
    SELECT aj1.sub_id, aj1.cr
    FROM account_journal aj1
    INNER JOIN (
        SELECT sub_id, MAX(created_date) AS last_date
        FROM account_journal
        WHERE transaction_type = 'Received'
          AND created_date BETWEEN '$date_from' AND '$date_to'
        GROUP BY sub_id
    ) aj2 ON aj1.sub_id = aj2.sub_id AND aj1.created_date = aj2.last_date
    WHERE aj1.transaction_type = 'Received'
) aj_last ON aj_last.sub_id = c.sub_id
            WHERE $where";

	

        $sql .= " GROUP BY m.voucher_no";

        // Get total records for pagination
        $totalResult = getData($sql); // assume this returns mysqli_result
        $totalRecord = mysqli_num_rows($totalResult);

	$grandResult = getData($grandSql);
	$grandTotals = mysqli_fetch_assoc($grandResult);

        // Now limit for page
        $sql .= " ORDER BY m.sales_date DESC LIMIT $from,$to";
        $result = getData($sql);

        $Pagination = '';
        if ($totalRecord > 0) {
            $Pagination = ellipsisPagination($totalRecord, $perPage, 'nextPage');
        }

        $htmlTableHead = '<div class="text-3xl text-center">(Heritage Polymer & Lami Tubes Ltd)</div>
                <div class="text-center">
                    <span class="text-primary">Sonargaon , Narayanganj, Bangladesh, 1440</span>
                </div>';

        $htmlContent = '<table class="table table-zebra">
                        <thead class="bg-base-200 text-base">
                        <tr>
                        <th>Sl</th>
		          <th>
		            <span class="arrow-icons">
		              <div>
		                <p>Customer</p>
		                <p>Name</p>
		              </div>
		              <i class="fa-solid fa-caret-down"></i>
		            </span>
		          </th>
		          <th>
		            <span class="arrow-icons">
		              Invoice #
		              <i class="fa-solid fa-caret-down"></i>
		            </span>
		          </th>
		          <th>
		            <span class="arrow-icons">
		              <div>
		                <p>Invoice</p>
		                <p>Date</p>
		              </div>

		              <i class="fa-solid fa-caret-down"></i>
		            </span>
		          </th>
		          <th>
		            <span class="arrow-icons">
		              <div>
		                <p>Invoice</p>
		                <p>Amount</p>
		              </div>

		              <i class="fa-solid fa-caret-down"></i>
		            </span>
		          </th>
		          <th>
		            <span class="arrow-icons">
		              <div>
		                <p>Payment</p>
		                <p>Terms</p>
		              </div>

		              <i class="fa-solid fa-caret-down"></i>
		            </span>
		          </th>
		          <th>
		            <span class="arrow-icons">
		              <div>
		                <p>Amount</p>
		                <p>Received</p>
		              </div>

		              <i class="fa-solid fa-caret-down"></i>
		            </span>
		          </th>
		          <th>
		            <span class="arrow-icons">
		              <div>
		                <p>Date</p>
		                <p>Received</p>
		              </div>

		              <i class="fa-solid fa-caret-down"></i>
		            </span>
		          </th>
		          <th>
		            <span class="arrow-icons">
		              <div>
		                <p>Due</p>
		                <p>Date</p>
		              </div>

		              <i class="fa-solid fa-caret-down"></i>
		            </span>
		          </th>
		          <th>
		            <span class="arrow-icons">
		              <div>
		                <p>Amount</p>
		                <p>Outstanding</p>
		              </div>

		              <i class="fa-solid fa-caret-down"></i>
		            </span>
		          </th>
		          <th>
		            <span class="arrow-icons">
		              <div>
		                <p>Days Past</p>
		                <p>Due Date</p>
		              </div>

		              <i class="fa-solid fa-caret-down"></i>
		            </span>
		          </th>
		        </tr>
                        </thead>
                        <tbody>';

$page_total_net = 0;
$page_total_paid = 0;
$page_total_outstanding = 0;

$grand_total_net = 0;
$grand_total_paid = 0;
$grand_total_outstanding = 0;


$grand_total_net = (float)$grandTotals["grand_net"];
$grand_total_paid = (float)$grandTotals["grand_paid"];
$grand_total_outstanding = (float)$grandTotals["grand_outstanding"];


        $sl = $from;
        if ($result && mysqli_num_rows($result) > 0) {
            $firstRow = mysqli_fetch_assoc($result);
            $htmlTableHead = '<div class="text-3xl text-center">' . $firstRow['project_name'] . '</div>
                <div class="text-center">
                    <span class="text-primary">' . $firstRow['location'] . '</span>
                </div>
                <div class="text-center">
                    <span>Date: ' . date('d M Y', strtotime($date_from)) . ' to ' . date('d M Y', strtotime($date_to)) . '</span>
                </div>';

            mysqli_data_seek($result, 0); // rewind result pointer
            while ($row = mysqli_fetch_assoc($result)) {
                $sl++;
                $party_code = $row["party_code"];
                $party_name = $row["party_name"];
                $sub_id = $row["sub_id"];
                $customer = normalizeUserName($party_code,$party_name);
                $invoice = $row["invoice"];
                $invoice_date = $row["sales_date"];
                $net_payble = $row["net_payble"];
                $paid_amount = $row["paid_amount"];
                $aging_date = $row["aging_date"];
                $invoice_date = $row["sales_date"];
		$payment_terms = dayDiff($invoice_date, $aging_date);
		$amountOutstanding = (float)$net_payble - (float)$paid_amount;
		$day_past_due = daysPastDue($aging_date);

		if ($aging_date == "0000-00-00" || empty($aging_date)) {
        		$aging_date = "-"; // No due date
    		}
		
	$changeAgingBtn .= '<span class="btn btn-info editAgingBtn" onclick=handleAgingModal("'.$invoice.'","'.$aging_date.'")> <i class="fa fa-edit"></i>Edit</span>';

		$page_total_net += $net_payble;
		$page_total_paid += $paid_amount;
		$page_total_outstanding += $amountOutstanding;

                $htmlContent .= '<tr>
                <td>' . $sl . '</th>
                <th><a href="?app=show_ledger&cmd=show&account_head='. $sub_id .'&date_from='. dateInputFormatDMY($invoice_date) .'" target="_blank">' . $customer . '</a></th>
                <td><a href="?app=sales_order&cmd=print_vouchar&voucher_no=' . $invoice . '" target="_blank">' . $invoice . '</a></td>
                <td>' . $invoice_date . '</td>
                <td>' . number_format($net_payble, 2) . ' TK</td>
                <td>' . $payment_terms . '</td>
                <td>' . number_format($paid_amount, 2) . ' TK</td>
                <td>' . $invoice_date . '</td>
                <td>' . $aging_date . "</br>" . $changeAgingBtn. '</td>
                <td>' . number_format($amountOutstanding, 2) . ' TK</td>
                <td>' . $day_past_due . '</td>
            </tr>';
            }

	    $htmlContent .= '
		<tr class="page-total bg-gray-200 font-bold">
		    <td colspan="4" class="text-right">Page Total:</td>
		    <td>' . number_format($page_total_net, 2) . ' TK</td>
		    <td></td>
		    <td>' . number_format($page_total_paid, 2) . ' TK</td>
		    <td></td>
		    <td></td>
		    <td>' . number_format($page_total_outstanding, 2) . ' TK</td>
		    <td></td>
		</tr>';

	    $htmlContent .= '
		<tr class="grand-total bg-green-200 font-bold">
		    <td colspan="4" class="text-right">Grand Total:</td>
		    <td>' . number_format($grand_total_net, 2) . ' TK</td>
		    <td></td>
		    <td>' . number_format($grand_total_paid, 2) . ' TK</td>
		    <td></td>
		    <td></td>
		    <td>' . number_format($grand_total_outstanding, 2) . ' TK</td>
		    <td></td>
		</tr>';

        } else {
            $htmlContent .= '<tr><td colspan="11" class="text-center">No record found</td></tr>';
        }

        $htmlContent .= '</tbody></table>';

        $data = [
            "status" => true,
            "data" => [
                "html" => $htmlContent,
                "pagination" => $Pagination,
                "tableHead" => $htmlTableHead
            ]
        ];

        echo json_encode($data);
        exit;

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


<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
            integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
            crossorigin="anonymous"
            referrerpolicy="no-referrer"></script>

 
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/uuid/dist/umd/uuidv4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <link rel="stylesheet" href="<?=REL_CONTENT_DIR?>/css/tailwindcss.css" type="text/css">

<script>
function printContent(id){
str=document.getElementById(id).innerHTML
newwin=window.open('','printwin','left=50,top=5,width=1200,height=895')
newwin.document.write('<HTML>\n<HEAD>\n')
newwin.document.write('<TITLE>Sales Target Status</TITLE>\n')
newwin.document.write('<script>\n')
newwin.document.write('function chkstate(){\n')
newwin.document.write('if(document.readyState=="complete"){\n')
newwin.document.write('window.close()\n')
newwin.document.write('}\n')
newwin.document.write('else{\n')
newwin.document.write('setTimeout("chkstate()",2000)\n')
newwin.document.write('}\n')
newwin.document.write('}\n')
newwin.document.write('function print_win(){\n')
newwin.document.write('window.print();\n')
newwin.document.write('chkstate();\n')
newwin.document.write('}\n')
newwin.document.write('<\/script>\n')
newwin.document.write("<style>.bngFont{font: 128% SutonnyMJ;}#tableborder_btm_right{border-right::1px solid #CCCCCC;border-bottom:1px solid #CCCCCC;}#tableborder_btm{border-right::1px solid #CCCCCC;border-bottom:1px solid #CCCCCC;}table,td{border-color: #999999;border-style: solid;}table{border-width: 0 0 1px 1px;border-spacing: 0;border-collapse: collapse;}td{margin: 0;padding: 4px;border-width: 1px 1px 0 0;}</style>")
newwin.document.write('</HEAD>\n')
newwin.document.write('<BODY onload="print_win()">\n')
newwin.document.write(str)
newwin.document.write('</BODY>\n')
newwin.document.write('</HTML>\n')
newwin.document.close()
}

</script>


<style>
thead.text-base.bg-base-200 {
    background-color: #1f29371a !important;
}

.p-6.shadow-md.bg-base-100.rounded-xl {
    background-color: #fff;
}
.editAgingBtn {
    font-size: 12px !important;
    padding: 0px 5px;
    min-height: 20px;
    height: 30px;
    margin-top: 6px;
}
.custom-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
}

.custom-modal-content {
    background: #fff;
    width: 80%;
    margin: 5% auto;
    padding: 20px;
    border-radius: 5px;
    max-height: 80%;
    overflow-y: auto;
    padding-top: 0;
}

.custom-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.close-btn {
    cursor: pointer;
    font-size: 22px;
    font-weight: bold;
}
div#overdueContent table {
    width: 100%;
}

div#overdueContent table td {
    border: 1px solid #818080;
}

div#overdueContent table th {
    border: 1px solid #818080;
}
</style>

<!-- Custom Modal -->
<div id="agingModal" class="custom-modal">
    <div class="custom-modal-content">
        
        <div class="custom-modal-header">
            <h3>Change Aging Date</h3>
            <span class="close-btn" onclick="closeModal()">&times;</span>
        </div>

        <div class="custom-modal-body">
    	    <form onsubmit="return false">
                <div class="flex flex-wrap items-center gap-2">
		    <div id="modal_error"></div>
                    <label class="form-control w-full min-w-[100px] flex-1 relative">
                        <div class="label">
                            <span class="label-text">Aging Date</span>
                        </div>
                        <input type="date" id="new_aging_date" class="custom-input"/>
                        <input type="hidden" id="aging_voucher" value=""/>
                    </label>
		    <label class="form-control w-full flex">
		         <div class="flex gap-1 mt-3" style="justify-content: right;">
		            <button class="btn btn-neutral text-white" onclick="closeModal()">Close</button>
		            <button class="btn btn-primary text-white" id="handleSaveAging">Save</button>
		         </div>
		    </label>
		</div>
	     </form>
        </div>

    </div>
</div>
<div class="bg-base-200" style="height:fit-content;background-color: #e4e7e7 !important;">
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
            <h1 class="mb-3 text-2xl font-bold">Aging Report</h1>
            <form onsubmit="return false">
                <div class="flex flex-wrap items-center gap-2">
                    <label class="form-control w-full min-w-[200px] flex-1 relative">
                        <div class="label">
                            <span class="label-text">From</span>
                        </div>
                        <input type="date" id="date_from" class="custom-input"/>
                    </label>
                    <label class="form-control w-full min-w-[200px] flex-1 relative">
                        <div class="label">
                            <span class="label-text">To</span>
                        </div>
                        <input type="date" id="date_to" class="custom-input"/>
                    </label>
                    <!-- <label class="form-control w-full min-w-[150px] flex-1 select2Parent2">
                        <div class="label">
                            <span class="capitalize label-text">Category</span>
                        </div>
                        <select class="custom-select category_list" id="category_id">
                            <option value="" selected>Select Category</option>
                            <?//= GetCategoryList(); ?>
                        </select>
                    </label>
                    <label class="form-control w-full min-w-[150px] flex-1 select2Parent3">
                        <div class="label">
                            <span class="capitalize label-text">Sub Category</span>
                        </div>
                        <select class="custom-select subCategory_list" id="subCategory_id">
                            <option value="" selected>Select Sub Category</option>
                            <?php //GetSubCategoryList(); ?>
                        </select>
                    </label>
                    <label class="form-control w-full min-w-[150px] flex-1 select2Parent4">
                        <div class="label">
                            <span class="capitalize label-text">Depo Name</span>
                        </div>
                        <select class="custom-select deliveryPointList" id="delivery_point_id">
                            <option value="" selected>Select Depo Name</option>
                            <?//= GetDeliveryPointList(); ?>
                        </select>
                    </label> -->
                    <label class="form-control w-full min-w-[150px] flex-1 select2Parent5">
                        <div class="label">
                            <span class="capitalize label-text">Customer</span>
                        </div>
                        <select class="custom-select customer_list" id="customer_id">
                            <option value="" selected>Select Customer</option>
                            <?= GetCustomerList(); ?>
                        </select>
                    </label>
                </div>
                <div class="flex gap-1 mt-3">
                    <button class="btn btn-primary text-white" id="handleSearch" type="submit">Submit</button>
                    <button class="btn btn-neutral text-white" id="reset" type="reset">Clear</button>
                </div>
            </form>
        </div>
        <input type="hidden" id="frm" value=""/>
        <input type="hidden" id="to" value=""/>
        <input type="hidden" id="pno" value=""/>
        <div class="p-6 mt-5 shadow-md bg-base-100 rounded-xl">
            <div class="flex flex-wrap w-full mb-2">
                <h2 class="mb-3 text-xl font-bold">
                    Accounts Receivable and Aging
                </h2>
                <div class="join shadow-md ml-auto mr-auto sm:mr-0" id="paginationList">

                </div>
            </div>
	    <div id="printDiv">
            <div class="space-y-1 mb-4" id="tableHeadInfo">
                <div class="text-3xl text-center">(Heritage Polymer & Lami Tubes Ltd)</div>
                <div class="text-center">
                    <span class="text-primary">Sonargaon , Narayanganj, Bangladesh, 1440</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="table table-zebra" id="tableData">
                    <!-- head -->
                    <thead class="text-base bg-base-200">
                    <tr>
                        <th>Sl</th>
                  <th>
                    <span class="arrow-icons">
                      <div>
                        <p>Customer</p>
                        <p>Name</p>
                      </div>
                      <i class="fa-solid fa-caret-down"></i>
                    </span>
                  </th>
                  <th>
                    <span class="arrow-icons">
                      Invoice #
                      <i class="fa-solid fa-caret-down"></i>
                    </span>
                  </th>
                  <th>
                    <span class="arrow-icons">
                      <div>
                        <p>Invoice</p>
                        <p>Date</p>
                      </div>

                      <i class="fa-solid fa-caret-down"></i>
                    </span>
                  </th>
                  <th>
                    <span class="arrow-icons">
                      <div>
                        <p>Invoice</p>
                        <p>Amount</p>
                      </div>

                      <i class="fa-solid fa-caret-down"></i>
                    </span>
                  </th>
                  <th>
                    <span class="arrow-icons">
                      <div>
                        <p>Payment</p>
                        <p>Terms</p>
                      </div>

                      <i class="fa-solid fa-caret-down"></i>
                    </span>
                  </th>
                  <th>
                    <span class="arrow-icons">
                      <div>
                        <p>Amount</p>
                        <p>Received</p>
                      </div>

                      <i class="fa-solid fa-caret-down"></i>
                    </span>
                  </th>
                  <th>
                    <span class="arrow-icons">
                      <div>
                        <p>Date</p>
                        <p>Received</p>
                      </div>

                      <i class="fa-solid fa-caret-down"></i>
                    </span>
                  </th>
                  <th>
                    <span class="arrow-icons">
                      <div>
                        <p>Due</p>
                        <p>Date</p>
                      </div>

                      <i class="fa-solid fa-caret-down"></i>
                    </span>
                  </th>
                  <th>
                    <span class="arrow-icons">
                      <div>
                        <p>Amount</p>
                        <p>Outstanding</p>
                      </div>

                      <i class="fa-solid fa-caret-down"></i>
                    </span>
                  </th>
                  <th>
                    <span class="arrow-icons">
                      <div>
                        <p>Days Past</p>
                        <p>Due Date</p>
                      </div>

                      <i class="fa-solid fa-caret-down"></i>
                    </span>
                  </th>
                </tr>
                    </thead>
                    <tbody>
                    <tr class="text-center">
                        <th colspan="11">No record found</th>
                    </tr>
                    </tbody>
                </table>
            </div>
	    </div>
		<div class="flex justify-center mt-3 gap-3">
            <button class="btn btn-neutral" id="printTable" style="color: #fff !important;">Print</button>
	    <!-- <button class="btn btn-primary" id="exportTableToCSV" style="color: #fff !important;">Export CSV</button> -->
          </div>
        </div>
        
    </div>
</div>

<script>
jQuery.noConflict();
(function ($) {

    let stocks = [];

    $('#category_id').on('change', function () {
        const categoryCode = $(this).val();

        $('#subCategory_id').html('<option>Loading...</option>');

        const formData = new URLSearchParams();
        formData.append('action', 'get_subcategories');
        formData.append('category_code', categoryCode);

        axios.post('', formData)
            .then(function (response) {
                $('#subCategory_id').html(response.data.data);
            })
            .catch(function (error) {
                $('#subCategory_id').html('<option>Select Sub Categories</option>');
            });
    });

    const formatDate = () => {
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

    // Get aging report
    const getAgingReportList = (frm = "", to = "", pno = "") => {
        let date_from = $('#date_from').val();
        let date_to = $('#date_to').val();
        //let category_id = $('#category_id').val();
        //let subCategory_id = $('#subCategory_id').val();
        ///let delivery_point_id = $('#delivery_point_id').val();
        let customer_id = $('#customer_id').val();

        const formData = new URLSearchParams();
        formData.append('action', 'get_aging_report');
        formData.append('date_from', date_from);
        formData.append('date_to', date_to);
        //formData.append('category_id', category_id);
        //formData.append('subCategory_id', subCategory_id);
        //formData.append('delivery_point_id', delivery_point_id);
        formData.append('customer_id', customer_id);
        formData.append('from', frm);
        formData.append('to', to);
        formData.append('page_no', pno);

        axios.post("", formData).then(function (response) {
            if (response.data.status) {
                $("#handleSearch").prop("disabled", false);

                $('#tableHeadInfo').html(response.data.data.tableHead)
                $('#tableData').html(response.data.data.html)
                $('#paginationList').html(response.data.data.pagination);
                $('#danger-alert').hide();
            }
        })
    }

    $(document).ready(function () {
        $('#date_from').val(formatDate());
        $('#date_to').val(formatDate());
        $('#alert').hide();
        $('#danger-alert').hide();
        getAgingReportList();

        //$(".category_list").select2({
        //    width: '100%',
        //    dropdownParent: $('.select2Parent2')
        //});

        //$(".subCategory_list").select2({
        //    width: '100%',
        //    dropdownParent: $('.select2Parent3')
        //});

        //$(".deliveryPointList").select2({
        //    width: '100%',
        //    dropdownParent: $('.select2Parent4')
        //});

        $(".customer_list").select2({
            width: '100%',
            dropdownParent: $('.select2Parent5')
        });
    });

    // Clear form input field
    const ClearFields = () => {
        $('#date_from').val(formatDate());
        $('#date_to').val(formatDate());
        //$('#category_id').val("").trigger("change");
        //$('#subCategory_id').val("").trigger("change");
        //$('#delivery_point_id').val("").trigger("change");
        $('#customer_id').val("").trigger("change");
    }

    // handle form reset function
    $('#reset').click(function (e) {
        e.preventDefault();
        $('#alert').hide();
        $('#danger-alert').hide();
        ClearFields();

        $("#handleSearch").prop("disabled", true);
        getAgingReportList();
    });

    // Handle search function
    $('#handleSearch').click(function (e) {
        e.preventDefault();
        $("#handleSearch").prop("disabled", true);
        getAgingReportList();
    });

    /* Pagination Next Page */
    window.nextPage = (frm, to, pno) => {
        $('#frm').val(frm);
        $('#to').val(to);
        $('#pno').val(pno);
        getAgingReportList(frm, to, pno);
        return false;
    }

    window.handleAgingModal = (voucher_no,date) => {
   	$("#new_aging_date").val(date);
	$("#aging_voucher").val(voucher_no);
     	document.getElementById("agingModal").style.display = "block";
    }

	window.closeModal = () =>{
	    document.getElementById("agingModal").style.display = "none";
	}

	window.onclick = function(event) {
	    var modal = document.getElementById("agingModal");
	    if (event.target == modal) {
		modal.style.display = "none";
	    }
	}

    $('#handleSaveAging').click(function (e) {
            e.preventDefault();
	    $('#modal_error').html('');
            var new_aging_date = $("#new_aging_date").val();
            var aging_voucher = $("#aging_voucher").val();

            const formData = new URLSearchParams();
            formData.append('action', 'update_aging_date');
            formData.append('aging_date', new_aging_date);
            formData.append('voucher_no', aging_voucher);

            axios.post('', formData)
                .then(function (response) {
                    if(response.data.status){
			  getAgingReportList();
			  $("#new_aging_date").val("");
			  $("#aging_voucher").val("");
			  document.getElementById("agingModal").style.display = "none";
		    }else{
			$('#modal_error').html('<p class="alert alert-danger text-danger">' + response.data.message + '</p>');
		    }
                })
                .catch(function (error) {
                    $('#modal_error').html('<p class="alert alert-danger text-danger">' + error + '</p>');
                });
        });


    $('#closeBtn').click(function (e) {
        e.preventDefault();
        $("#handelClose").submit();
    });

$('#printTable').click(function (e) {
    //const printContents = document.getElementById("printDiv").innerHTML;
    //const originalContents = document.body.innerHTML;
printContent("printDiv");
    //document.body.innerHTML = printContents;
    //window.print();

});


$('#exportTableToCSV').click(function (e) {
    var filename = "aging-report-data.csv";
    var csv = [];
    var rows = document.querySelectorAll("#tableData tr");

    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll("th, td");

        for (var j = 0; j < cols.length; j++) {
            let text = cols[j].innerText.replace(/,/g, ""); // remove commas
            row.push('"' + text + '"'); // wrap in quotes
        }
        csv.push(row.join(","));
    }

    // Download CSV
    var csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
    var downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
});

    
})(jQuery);

</script>



