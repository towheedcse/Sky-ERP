<?php

class Catagory
{
    function run()
    {
        $cmd = getRequest('cmd');
        $u_t_id = getFromSession('u_type_id');
        if ($u_t_id == 101) {
            switch ($cmd) {
                case 'add'                    :
                    $screen = $this->showEditor($msg);
                    break;
                case 'edit'                :
                    $screen = $this->showEditor("Edit Page");
                    break;
                case 'doUpdate'            :
                    $screen = $this->showEditor($msg);
                    break;
                case 'delete'                :
                    $screen = $this->deleteItem();
                case 'add_main'                    :
                    $screen = $this->showMainEditor($msg);
                    break;
                case 'edit_main'                :
                    $screen = $this->showMainEditor("Edit Page");
                    break;
                case 'getSubcatagory'                :
                    $screen = $this->getSubcatagory();
                    break;
                case 'doUpdate_main'            :
                    $screen = $this->showMainEditor($msg);
                    break;
                case 'delete_main'                :
                    $screen = $this->deleteMainItem();
                    break;
                default                    :
                    $cmd = 'list';
                    $screen = $this->showEditor($msg);
                    break;
            }
        } else if ($u_t_id == 107) {
            switch ($cmd) {
                case 'add'                    :
                    $screen = $this->showEditor($msg);
                    break;
                case 'edit'                :
                    $screen = $this->showEditor("Edit Page");
                    break;
                case 'doUpdate'            :
                    $screen = $this->showEditor($msg);
                    break;
                case 'add_main'                    :
                    $screen = $this->showMainEditor($msg);
                    break;
                case 'edit_main'                :
                    $screen = $this->showMainEditor("Edit Page");
                    break;
                case 'getSubcatagory'                :
                    $screen = $this->getSubcatagory();
                    break;
                case 'doUpdate_main'            :
                    $screen = $this->showMainEditor($msg);
                    break;
                default                    :
                    $cmd = 'list';
                    $screen = $this->showEditor($msg);
                    break;
            }
        } else {
            header("location:index.php?app=user_home&msg=You are not authorised !!!");
        }

        return true;
    }

    function showEditor()
    {
        require_once(CLASS_DIR . '/common.class.php');
        $comApp = new Common();
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $catagory_code = getRequest('id');
        $data = array();
        if ($catagory_code) {
            $TBDArr = $comApp->getRecordInfo(CATAGORY_TBL, "catagory_code", $catagory_code);
            $TBDArr = parseThisValue($TBDArr);
            $data = array_merge(array(), $TBDArr);
            if (getRequest('save')) {
                $comApp->updateRecord(CATAGORY_TBL, "catagory_code", $catagory_code, "", "", "modefied_by", "modefied_date", "catagory", "list");
                $msg = "Successfully Update Record !!!";
                header("location:?app=catagory&cmd=list&msg=$msg");
            }
        } else {
            if (getRequest('save')) {
                $accessories_id = $comApp->NewID(CATAGORY_TBL, "catagory_code", "C310000", "C", 7);
                $comApp->saveRecord(CATAGORY_TBL, "catagory_code", $accessories_id, "", "", "created_by", "created_time", "catagory", "list");
                $msg = "Successfully Save Record !!!";
                header("location:?app=catagory&cmd=list&msg=$msg");
            }
        }
        $f1Value = getRequest('srckey');
        $from = getRequest('from');
        if ($from == "") {
            $from = 0;
        }
        $to = getRequest('to');
        if ($to == "") {
            $to = 45;
        }
	$result = $this->getCatagoryList($from,$to);

        $data['catagory_list'] = $result['data'];
        $data['totalrecord'] = $result['total'];
        $data['main_catagory_list'] = $comListApp->getMainCatagoryList();
        $data['project_id'] = getFromSession('project_id');
        $data['message'] = $msg;
        $data['cmd'] = getRequest('cmd');
        require_once(CURRENT_APP_SKIN_FILE);
        return $data[0];
    }


    function getCatagoryList($from, $to)
    {
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 45;
        }

        $srcmaincatagory = trim(getRequest('srcmaincatagory'));
        $srckey = trim(getRequest('srckey'));

	if ($srcmaincatagory != "" || $srckey != "") {
	    $from = 0;
	}

        $project_id = getFromSession('project_id');
        $info = array();
	$info['table'] = CATAGORY_TBL . " c LEFT JOIN " . MAIN_CATAGORY_TBL . " mc ON c.main_catagory_id = mc.main_cat_code";
	$info['fields'] = array( 'c.*', 'mc.main_cat_name AS main_catagory_name');
	$sql = "c.project_id = '$project_id'";
        if ($srcmaincatagory != "") {
            $sql .= " AND c.main_catagory_id = '$srcmaincatagory'";
        }
        if ($srckey != "") {
            $sql .= " AND c.catagory_name LIKE '%$srckey%'";
        }
        $info['where'] = $sql;

	// ---------- COUNT QUERY ----------
        $countInfo = array();
        $countInfo['table'] =
            CATAGORY_TBL . " c
         LEFT JOIN " . MAIN_CATAGORY_TBL . " mc
            ON c.main_catagory_id = mc.main_cat_id";

        $countInfo['fields'] = array("COUNT(DISTINCT c.catagory_code) AS total");
        $countInfo['where'] = $sql;

        $countResult = select($countInfo);
        $total = isset($countResult[0]->total) ? $countResult[0]->total : 0;

        $info['orderby'] = array("c.catagory_code DESC LIMIT $from,$to");
        //$info['debug']  = true;
        $result = select($info);

        $data = array();
        $cnt = count($result);
		
        if ($cnt) {
            foreach ($result as $value) {
                $data[] = $value;
            }
        }
        return ["data" => $data, "total" => $total];
    }



    function deleteItem()
    {
        require_once(CLASS_DIR . '/common.class.php');
        $comApp = new Common();
	if(!userCondition(true)){
		$msg = "You are not authorized !!!";
	      	header("location:index.php?app=catagory&msg=$msg");
	      	exit;
        }

        $catagory_code = getRequest('id');
        $comApp->deleteRecord(CATAGORY_TBL, "catagory_code", $catagory_code, "catagory", "list");
    }


    function showMainEditor()
    {
        require_once(CLASS_DIR . '/common.class.php');
        $comApp = new Common();
        require_once(CLASS_DIR . '/common.list.class.php');
        $comListApp = new CommonList();
        $catagory_code = getRequest('id');
        $data = array();
        if ($catagory_code) {
            $TBDArr = $comApp->getRecordInfo(MAIN_CATAGORY_TBL, "main_cat_code", $catagory_code);
            $TBDArr = parseThisValue($TBDArr);
            $data = array_merge(array(), $TBDArr);
            if (getRequest('save')) {
                $comApp->updateRecord(MAIN_CATAGORY_TBL, "main_cat_code", $catagory_code, "", "", "modefied_by", "modefied_date", "catagory", "add_main");
                $msg = "Successfully Update Record !!!";
                header("location:?app=catagory&cmd=add_main&msg=$msg");
            }
        } else {
            if (getRequest('save')) {
                $accessories_id = $comApp->NewID(MAIN_CATAGORY_TBL, "main_cat_code", "CM21000", "CM", 7);
                $comApp->saveRecord(MAIN_CATAGORY_TBL, "main_cat_code", $accessories_id, "", "", "created_by", "created_time", "catagory", "add_main");
                $msg = "Successfully Save Record !!!";
                header("location:?app=catagory&cmd=add_main&msg=$msg");
            }
        }
        $f1Value = getRequest('srckey');
        $from = getRequest('from');
        if ($from == "") {
            $from = 0;
        }
        $to = getRequest('to');
        if ($to == "") {
            $to = 45;
        }
        $data['catagory_list'] = $comApp->getRecords(MAIN_CATAGORY_TBL, "main_cat_code", "", "main_cat_name", $f1Value, "", "", $from, $to);
        $data['totalrecord'] = $comApp->getTotalRecords(MAIN_CATAGORY_TBL, "main_cat_code", "", "main_cat_name", $f1Value, "", "");
        $data['project_id'] = getFromSession('project_id');
        $data['message'] = $msg;
        $data['cmd'] = getRequest('cmd');
        require_once(TEMPLATES_SKINS . '/main_catagory.html');
        return $data[0];
    }

    function deleteMainItem()
    {
        require_once(CLASS_DIR . '/common.class.php');
        $comApp = new Common();
	if(!userCondition(true)){
		$msg = "You are not authorized !!!";
	      	header("location:index.php?app=catagory&cmd=add_main&msg=$msg");
	      	exit;
	    }

        $catagory_code = getRequest('id');
        $comApp->deleteRecord(MAIN_CATAGORY_TBL, "main_cat_code", $catagory_code, "catagory", "add_main");
    }


     function getSubcatagory(){
	$project_id = getFromSession('project_id'); 

	if (ob_get_level()) ob_end_clean();
        ob_start(); // Start buffering

        $input = json_decode(file_get_contents('php://input'), true);

        // Validate input
        if (json_last_error() !== JSON_ERROR_NONE || !isset($input['main_catagory_id'])) {
            die(json_encode([
                'status' => false,
                'message' => 'Invalid JSON data received'
            ]));
        }

        $main_catagory_id = trim($input['main_catagory_id']);

 
	$rsql= "SELECT catagory_code,catagory_name FROM ".CATAGORY_TBL." WHERE main_catagory_id='$main_catagory_id' AND project_id='".$project_id."'";  
	$rres = mysql_query($rsql);
	$html ="";
	$html="<option value=''>Select Option</option>";
	while($v = mysql_fetch_object($rres)){		
	$html.="<option value='".$v->catagory_code."'>".$v->catagory_name."</option>";
	}
	
       $response = [
           'status' => true,
           'data' => $html
       ];
        

        // Output response
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
   }




} // End class
?>

