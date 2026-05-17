<?php
/* apps/job_order.php */

class Job
{
    // Global DB link usually needed for mysql_real_escape_string if not using a wrapper
    var $db_link;

    function run()
    {
        $cmd = getRequest('cmd');

        // Handle AJAX Requests
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');

            // Clean any existing output buffer to ensure pure JSON
            if (ob_get_level()) ob_end_clean();

            switch ($cmd) {
                case 'get_list':
                    echo $this->getTempJobList();
                    break;
                case 'save':
                    echo $this->addTempJobItem();
                    break;
                case 'delete':
                    echo $this->deeteTempJobItem();
                    break;
                case 'get_by_id':
                    echo $this->getTempJobByID();
                    break;
                case 'save_batch':
                    echo $this->saveJob();
                    break;
                case 'get_master_list':
                    echo $this->getJobMasterList();
                    break;
                case 'get_job_details':
                    echo $this->getJobDetails();
                    break;
                case 'delete_job':
                    echo $this->deleteJobMaster();
                    break;
                case 'get_job_for_edit':
                    echo $this->getJobForEdit();
                    break;
                case 'update_job_master':
                    echo $this->updateJobMaster();
                    break;
                default:
                    echo json_encode(['status' => false, 'message' => 'Invalid Command']);
                    break;
            }
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


    function showJobOrderList()
    {
        $data = [
            "batchList" => $this->getBatchList(),
            "machineList" => $this->getMachineList(),
            "customerList" => $this->getCustomerList(),
            "message" => getRequest('msg')
        ];

        require_once(JOB_ORDER_LIST_SKIN);
    }

    function showEditor()
    {
        $data = [
            "batchList" => $this->getBatchList(),
            "machineList" => $this->getMachineList(),
            "customerList" => $this->getCustomerList(),
            "message" => getRequest('msg')
        ];

        require_once(CURRENT_APP_SKIN_FILE);
    }

    public function getBatchList()
    {
        $batchSQL = "SELECT batch_id, batch_name FROM production_batch_master WHERE status = '1' ORDER BY batch_id DESC";
        $batchRes = mysql_query($batchSQL);
        $batchList = [];
        while ($row = mysql_fetch_assoc($batchRes)) {
            $batchList[] = $row;
        }

        return $batchList;
    }

    public function getMachineList()
    {
        $machineSQL = "SELECT machine_id, machine_name FROM machine";
        $machineRes = mysql_query($machineSQL);
        $machineList = [];
        while ($row = mysql_fetch_assoc($machineRes)) {
            $machineList[] = $row;
        }

        return $machineList;
    }

    public function getCustomerList()
    {
        $custSQL = "SELECT sub_id, sub_head_name FROM sub_acc_head";
        $custRes = mysql_query($custSQL);
        $customerList = [];
        while ($row = mysql_fetch_assoc($custRes)) {
            $customerList[] = $row;
        }

        return $customerList;
    }

    private function clean($val)
    {
        return mysql_real_escape_string(trim($val));
    }

    // 1. Save (Insert/Update)
    public function addTempJobItem()
    {
        // Get Inputs
        $created_by = getFromSession('userid');
        $project_id = getFromSession('project_id');

        $job_name = isset($_POST['job_name']) ? $this->clean($_POST['job_name']) : "";
        $job_deadline = isset($_POST['job_deadline']) ? $this->clean($_POST['job_deadline']) : "";
        $batch_id = isset($_POST['batch_id']) ? $this->clean($_POST['batch_id']) : "";
        $machine_id = isset($_POST['machine_id']) ? $this->clean($_POST['machine_id']) : "";
        $customer_id = isset($_POST['customer_id']) ? $this->clean($_POST['customer_id']) : "";
        $tmp_id = isset($_POST['job_id']) ? $this->clean($_POST['job_id']) : ""; // Hidden field ID

        // Validation
        if ($batch_id == "" || $machine_id == "" || $customer_id == "") {
            return json_encode(["status" => false, "message" => "Field must not be empty"]);
        }

        // Date Formatting (Adjust if your DB needs specific format)
        // Assuming input is Y-m-d from HTML5 date picker

        if ($tmp_id == "" || $tmp_id == 0) {
            // INSERT
            $sql = "INSERT INTO temp_job_order (project_id, name, deadline, batch_id, machine_id, customer_id, created_by) 
                    VALUES ('$project_id', '$job_name', '$job_deadline', '$batch_id', '$machine_id', '$customer_id', '$created_by')";
            $result = mysql_query($sql);

            $msg = $result ? "Record has been saved successfully!" : "Record did not saved!";
        } else {
            // UPDATE
            $sql = "UPDATE temp_job_order 
                    SET project_id = '$project_id', name = '$job_name', deadline = '$job_deadline', 
                        batch_id = '$batch_id', machine_id = '$machine_id', customer_id = '$customer_id' 
                    WHERE id = '$tmp_id'";
            $result = mysql_query($sql);

            $msg = $result ? "Record has been edited successfully!" : "Record did not edited!";
        }

        // Fetch fresh list to return
        $newList = $this->getTempJobList(true); // true = return array, don't echo
        $response = [
            "status" => ($result ? true : false),
            "message" => $msg,
            "data" => ["html" => $newList]
        ];

        return json_encode($response);
    }

    // 2. Get List & Build HTML
    public function getTempJobList($returnArray = false)
    {
        // Get Session Data if not in POST
        $created_by = getFromSession('userid');
        $project_id = getFromSession('project_id');
        $search = isset($_POST['search']) ? $this->clean($_POST['search']) : "";

        $sql = "SELECT tjo.*, pbm.batch_name, m.machine_name, c.sub_head_name 
                FROM temp_job_order AS tjo
                LEFT JOIN production_batch_master AS pbm ON pbm.batch_id = tjo.batch_id
                LEFT JOIN machine AS m ON m.machine_id = tjo.machine_id
                LEFT JOIN sub_acc_head AS c ON c.sub_id = tjo.customer_id
                WHERE tjo.created_by = '$created_by'";

        if ($project_id != "") {
            $sql .= " AND tjo.project_id = '$project_id'";
        }

        if ($search != "") {
            $sql .= " AND (tjo.name LIKE '%$search%' OR pbm.batch_name LIKE '%$search%')";
        }

        $sql .= " GROUP BY tjo.id ORDER BY tjo.id DESC";

        $query = mysql_query($sql);

        // Build HTML Table (As per your old code style)
        $htmlContent = '<table class="table table-zebra"><tbody>';

        $sl = 0;
        $hasData = false;

        if ($query && mysql_num_rows($query) > 0) {
            $hasData = true;
            while ($row = mysql_fetch_assoc($query)) {
                $sl++;
                $htmlContent .= '<tr>
                        <td>' . $sl . '</td>
                        <td>' . htmlspecialchars($row["batch_name"]) . '</td>
                        <td>' . htmlspecialchars($row["machine_name"]) . '</td>
                        <td>' . htmlspecialchars($row["sub_head_name"]) . '</td>
                        <td>
                            <div class="flex gap-1 items-center justify-center">
                                <button class="btn bg-base-100 text-sm text-base-content border border-base-300"
                                        title="Edit"
                                        onclick="editTempRecord(\'' . $row["id"] . '\')">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button class="btn bg-base-100 text-sm text-base-content border border-base-300"
                                        onclick="deleteTempRecord(\'' . $row["id"] . '\')"
                                        title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                </tr>';
            }
        }

        if (!$hasData) {
            $htmlContent .= '<tr><td colspan="7" class="text-center">No record found</td></tr>';
        }

        $htmlContent .= '</tbody></table>';

        if ($returnArray) {
            return $htmlContent;
        }

        return json_encode(["html" => $htmlContent]);
    }

    // 3. Get Single Record
    public function getTempJobByID()
    {
        $tmp_id = isset($_POST['tmp_id']) ? $this->clean($_POST['tmp_id']) : "";

        if ($tmp_id == "") {
            return json_encode(["status" => false, "message" => "Data ID missing!"]);
        }

        $sql = "SELECT * FROM temp_job_order WHERE id = '$tmp_id'";
        $query = mysql_query($sql);

        $data = [];
        if ($query && $row = mysql_fetch_assoc($query)) {
            $data = $row;
        }

        return json_encode(["status" => true, "data" => $data]);
    }

    // 4. Delete Record
    public function deeteTempJobItem()
    {
        $deleted_id = isset($_POST['deleted_id']) ? $this->clean($_POST['deleted_id']) : "";

        $sql = "DELETE FROM temp_job_order WHERE id = '$deleted_id'";
        $query = mysql_query($sql);

        $status = ($query) ? true : false;
        $message = ($query) ? "Record has been Deleted successfully" : "Record did not Deleted. Try again";

        return json_encode(["status" => $status, "message" => $message]);
    }


    public function saveJob()
    {

        $job_name = isset($_POST['job_name']) ? $this->clean($_POST['job_name']) : "";
        $job_deadline = isset($_POST['job_deadline']) ? $this->clean($_POST['job_deadline']) : "";

        // Validation
        if ($job_name == "" || $job_deadline == "") {
            return json_encode(["status" => false, "message" => "Job name and Deadline must not be empty"]);
            exit();
        }

        // Start Transaction using mysql_query
        mysql_query("START TRANSACTION");

        $voucher = $this->insertJobMaster();

        if ($voucher) {
            $this->insertJobDetails($voucher);
            mysql_query("COMMIT");
            $status = true;
            $message = "Record has been saved successfully!";
        } else {
            mysql_query("ROLLBACK");
            $status = false;
            $message = "Record did not saved! (Transaction Failed)";
        }

        $tempList = $this->getTempJobList(true);

        $data = [
            "status" => $status,
            "message" => $message,
            "htmlData" => [
                "html" => $tempList, // Sending back the temp list (now empty or updated)
                "pagination" => "",
                "totalInfo" => []
            ]
        ];

        return json_encode($data);
    }

    public function insertJobMaster()
    {
        // Get Inputs
        $created_by = getFromSession('userid');
        $project_id = getFromSession('project_id');

        $job_name = isset($_POST['job_name']) ? $this->clean($_POST['job_name']) : "";
        $job_date = isset($_POST['job_deadline']) ? $this->clean($_POST['job_deadline']) : "";

        $voucher = $this->createBatchID();
        $amount = 0;
        $status = 1;

        $insertSQL = "INSERT INTO job_master (voucher,project_id,job_name,job_date,total_amount,status,created_by) VALUES('" . $voucher . "','" . $project_id . "','" . $job_name . "','" . $job_date . "','" . $amount . "','" . $status . "','" . $created_by . "')";
        mysql_query($insertSQL);

        return $voucher;
    }

    public function createBatchID()
    {
        $sql = "SELECT max(voucher) as maxvoucher FROM job_master";
        $res = mysql_query($sql);

        $maxvoucherId = 'J0000000';
        if ($res && mysql_num_rows($res) > 0) {
            $row = mysql_fetch_assoc($res);
            if ($row['maxvoucher']) {
                $maxvoucherId = $row['maxvoucher'];
            }
        }

        // Assuming generateID is a global helper function in your project
        // If not, here is a simple implementation:
        $num = (int)substr($maxvoucherId, 1);
        $num++;
        return 'J' . str_pad($num, 8, '0', STR_PAD_LEFT);

        // OR use your existing helper:
        // return generateID("B", $maxvoucherId, 8);
    }

    public function insertJobDetails($voucher)
    {
        $created_by = getFromSession('userid');
        $project_id = getFromSession('project_id');

        $getSql = "SELECT * FROM temp_job_order WHERE created_by ='$created_by' AND project_id='$project_id'";
        $result = mysql_query($getSql);

        if (mysql_num_rows($result) > 0) {
            while ($row = mysql_fetch_assoc($result)) {
                $date = $row['deadline'];
                $batch_id = $row['batch_id'];
                $machine_id = $row['machine_id'];
                $customer_id = $row['customer_id'];
                $created_by = $row['created_by'];
                $amount = 0;

                // Insert details
                $insertSQL = "INSERT INTO job_details (voucher,date,batch_id,machine_id,customer_id,amount,created_by) VALUES('" . $voucher . "','" . $date . "','" . $batch_id . "','" . $machine_id . "','" . $customer_id . "','" . $amount . "','" . $created_by . "')";

                mysql_query($insertSQL);
            }
        }

        // Delete temp data
        $dsql = "DELETE FROM temp_job_order WHERE created_by = '$created_by' AND project_id='$project_id'";
        mysql_query($dsql);
    }


    public function getJobMasterList()
    {
        // Inputs
        $project_id = getFromSession('project_id');
        $searchName = isset($_POST['job_name']) ? $this->clean($_POST['job_name']) : "";
        $fromDate = isset($_POST['from_date']) ? $this->clean($_POST['from_date']) : "";
        $toDate = isset($_POST['to_date']) ? $this->clean($_POST['to_date']) : "";

        // Pagination
        $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
        $limit = 10; // Records per page
        $offset = ($page - 1) * $limit;

        // Query Conditions
        $where = "WHERE project_id = '$project_id'";

        if (!empty($searchName)) {
            $where .= " AND (job_name LIKE '%$searchName%' OR voucher LIKE '%$searchName%')";
        }
        if (!empty($fromDate)) {
            $where .= " AND job_date >= '$fromDate'";
        }
        if (!empty($toDate)) {
            $where .= " AND job_date <= '$toDate'";
        }

        // Get Total Count for Pagination
        $countSQL = "SELECT COUNT(*) as total FROM job_master $where";
        $countRes = mysql_query($countSQL);
        $totalCount = 0;
        if ($countRes) {
            $row = mysql_fetch_assoc($countRes);
            $totalCount = $row['total'];
        }

        // Get Data
        $sql = "SELECT * FROM job_master $where ORDER BY id DESC LIMIT $offset, $limit";
        $query = mysql_query($sql);

        // Build HTML Table
        $html = '<table class="table table-zebra"><tbody>';

        $sl = $offset + 1;
        $hasData = false;

        if ($query && mysql_num_rows($query) > 0) {
            $hasData = true;
            while ($row = mysql_fetch_assoc($query)) {
                $statusBadge = $row['status'] == 1
                    ? '<span style="color:green">Active</span>'
                    : '<span style="color:red">Inactive</span>';

                $option = '<td class="text-center">
                    <button class="btn btn-sm btn-primary" style="margin-right:5px;" 
                        onclick="editMaster(\'' . $row["id"] . '\')">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    <button class="btn btn-sm btn-success" style="margin-right:5px;" 
                        onclick="viewDetails(\'' . $row["voucher"] . '\')">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" 
                        onclick="deleteMaster(\'' . $row["id"] . '\')">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>';

                $html .= '<tr>
                        <td>' . $sl++ . '</td>
                        <td>' . htmlspecialchars($row["voucher"]) . '</td>
                        <td>' . htmlspecialchars($row["job_name"]) . '</td>
                        <td>' . htmlspecialchars($row["job_date"]) . '</td>
                        ' . $option . '
                </tr>';
            }
        }

        if (!$hasData) {
            $html .= '<tr><td colspan="7" class="text-center">No records found</td></tr>';
        }
        $html .= '</tbody></table>';

        // Build Pagination HTML
        $totalPages = ceil($totalCount / $limit);
        $paginationHtml = '<div class="flex justify-center gap-2 mt-4">';

        // Previous Button
        if ($page > 1) {
            $paginationHtml .= '<button class="btn btn-neutral btn-sm" onclick="changePage(' . ($page - 1) . ')">Prev</button>';
        }

        // Page Info
        $paginationHtml .= '<span class="p-2">Page ' . $page . ' of ' . $totalPages . '</span>';

        // Next Button
        if ($page < $totalPages) {
            $paginationHtml .= '<button class="btn btn-neutral btn-sm" onclick="changePage(' . ($page + 1) . ')">Next</button>';
        }
        $paginationHtml .= '</div>';

        return json_encode([
            "status" => true,
            "html" => $html,
            "pagination" => $paginationHtml
        ]);
    }

    // --- Get Job Details ---

    public function getJobDetails()
    {
        $voucher = isset($_POST['voucher']) ? $this->clean($_POST['voucher']) : "";

        if (empty($voucher)) {
            return json_encode(["status" => false, "message" => "Voucher missing"]);
        }

        // Join with lookup tables to get Names instead of IDs
        $sql = "SELECT jd.*, pbm.batch_name, m.machine_name, c.sub_head_name as customer_name 
                FROM job_details AS jd
                LEFT JOIN production_batch_master AS pbm ON pbm.batch_id = jd.batch_id
                LEFT JOIN machine AS m ON m.machine_id = jd.machine_id
                LEFT JOIN sub_acc_head AS c ON c.sub_id = jd.customer_id
                WHERE jd.voucher = '$voucher'";

        $query = mysql_query($sql);

        $html = '<table class="table table-zebra w-full">
                    <thead class="bg-base-200">
                    <tr>
                        <th>Batch</th>
                        <th>Machine</th>
                        <th>Customer</th>
                    </tr>
                    </thead>
                    <tbody>';

        if ($query && mysql_num_rows($query) > 0) {
            while ($row = mysql_fetch_assoc($query)) {
                $html .= '<tr>
                        <td>' . htmlspecialchars($row["batch_name"]) . '</td>
                        <td>' . htmlspecialchars($row["machine_name"]) . '</td>
                        <td>' . htmlspecialchars($row["customer_name"]) . '</td>
                </tr>';
            }
        } else {
            $html .= '<tr><td colspan="6" class="text-center">No details found</td></tr>';
        }

        $html .= '</tbody></table>';

        return json_encode(["status" => true, "html" => $html]);
    }


    public function deleteJobMaster()
    {
        $id = isset($_POST['id']) ? $this->clean($_POST['id']) : "";

        if (empty($id)) {
            return json_encode(['status' => false, 'message' => 'ID missing']);
        }

        // 1. Get the Voucher for this Job ID
        $sql = "SELECT voucher FROM job_master WHERE id = '$id'";
        $query = mysql_query($sql);

        if ($query && $row = mysql_fetch_assoc($query)) {
            $voucher = $row['voucher'];

            // 2. Delete Details first
            mysql_query("DELETE FROM job_details WHERE voucher = '$voucher'");

            // 3. Delete Master
            $delMaster = mysql_query("DELETE FROM job_master WHERE id = '$id'");

            if ($delMaster) {
                return json_encode(['status' => true, 'message' => 'Job and details deleted successfully']);
            } else {
                return json_encode(['status' => false, 'message' => 'Failed to delete master record']);
            }
        }

        return json_encode(['status' => false, 'message' => 'Job not found']);
    }

    // --- Get Job Data for Editing ---
    public function getJobForEdit()
    {
        $id = isset($_POST['id']) ? $this->clean($_POST['id']) : "";

        if (empty($id)) {
            return json_encode(['status' => false, 'message' => 'ID missing']);
        }

        // 1. Get Master
        $sql = "SELECT * FROM job_master WHERE id = '$id'";
        $query = mysql_query($sql);

        if ($query && $row = mysql_fetch_assoc($query)) {
            $voucher = $row['voucher'];

            // 2. Get Details
            $details = [];
            $detSql = "SELECT * FROM job_details WHERE voucher = '$voucher'";
            $detQuery = mysql_query($detSql);

            if ($detQuery) {
                while ($dRow = mysql_fetch_assoc($detQuery)) {
                    $details[] = $dRow;
                }
            }

            // Return Master + Details
            $row['details'] = $details;
            return json_encode(['status' => true, 'data' => $row]);
        }

        return json_encode(['status' => false, 'message' => 'Record not found']);
    }

    // --- Update Master Record ---

    public function updateJobMaster()
    {
        $job_id = isset($_POST['job_id']) ? $this->clean($_POST['job_id']) : "";
        $job_name = isset($_POST['job_name']) ? $this->clean($_POST['job_name']) : "";
        $job_date = isset($_POST['job_deadline']) ? $this->clean($_POST['job_deadline']) : "";
        $created_by = getFromSession('user_id');

        if (empty($job_id) || empty($job_name) || empty($job_date)) {
            return json_encode(['status' => false, 'message' => 'Missing required fields']);
        }

        // 1. Update Master
        $sql = "UPDATE job_master 
                 SET job_name = '$job_name', 
                     job_date = '$job_date'
                 WHERE id = '$job_id'";
        mysql_query($sql);

        // 2. Update Details (Loop through array sent from JS)
        if (isset($_POST['details']) && is_array($_POST['details'])) {
            foreach ($_POST['details'] as $detail) {
                $det_id = $this->clean($detail['id']);
                $batch = $this->clean($detail['batch_id']);
                $machine = $this->clean($detail['machine_id']);
                $customer = $this->clean($detail['customer_id']);

                // Update SQL
                $updDet = "UPDATE job_details 
                            SET batch_id = '$batch', 
                                machine_id = '$machine', 
                                customer_id = '$customer'
                            WHERE id = '$det_id'";
                mysql_query($updDet);
            }
        }

        return json_encode(['status' => true, 'message' => 'Job and Details updated successfully']);
    }

}

?>
