<?php

class Vehicle
{
    function run()
    {
        $cmd = getRequest('cmd');
        $u_t_id = getFromSession('u_type_id');
        if ($u_t_id == 101 || $u_t_id == 102 || $u_t_id == 106) {
            switch ($cmd) {
                case 'add'     :
                    $screen = $this->showEditor($msg);
                    break;
                case 'edit'    :
                    $screen = $this->showEditor("Edit Page");
                    break;
                case 'doUpdate':
                    $screen = $this->showEditor($msg);
                    break;
                case 'delete'  :
                    $screen = $this->deleteItem();
                    break;
                default        :
                    $cmd = 'list';
                    $screen = $this->showEditor($msg);
                    break;
            }
        } else if ($u_t_id == 107) {
            switch ($cmd) {
                case 'add'     :
                    $screen = $this->showEditor($msg);
                    break;
                case 'edit'    :
                    $screen = $this->showEditor("Edit Page");
                    break;
                case 'doUpdate':
                    $screen = $this->showEditor($msg);
                    break;
                default        :
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
        $vehicle_id = getRequest('id');
        $data = array();

        $requestdata['vehicle_no'] = getRequest('vehicle_no');
        $requestdata['driver_name'] = getRequest('driver_name');
        $requestdata['driver_phone'] = getRequest('driver_phone');
        $requestdata["weight"] = getRequest('weight');
        $requestdata["remarks"] = getRequest('remarks');

        if ($vehicle_id) {
            $TBDArr = $comApp->getRecordInfo(VEHICLES_TBL, "id", $vehicle_id);
            $TBDArr = parseThisValue($TBDArr);
            $data = array_merge(array(), $TBDArr);
            if (getRequest('save')) {
                $requestdata['updated_at'] = date('Y-m-d h:i:s');

                $info = array();
                $info['table'] = VEHICLES_TBL;
                $info['data'] = $requestdata;
                $info['where'] = "id='$vehicle_id'";
                //$info['debug']  	=  true;
                $res = update($info);

                $msg = "Successfully Update Record !!!";
                header("location:?app=vehicle&cmd=list&msg=$msg");
            }
        } else {
            if (getRequest('save')) {
                $requestdata['created_at'] = date('Y-m-d h:i:s');

                $info = array();
                $info['table'] = VEHICLES_TBL;
                $info['data'] = $requestdata;
                //$info['debug']  	=  true;
                $res = insert($info);

                $msg = "Successfully Save Record !!!";
                header("location:?app=vehicle&cmd=list&msg=$msg");
            }
        }
        $f1Value = getRequest('srckey');
        $from = getRequest('from');
        if ($from == "") {
            $from = 0;
        }
        $to = getRequest('to');
        if ($to == "") {
            $to = 20;
        }
        $data['record_list'] = $comApp->getRecords(VEHICLES_TBL, "id", "", "vehicle_no", $f1Value, "", "", $from, $to);
        $data['totalrecord'] = $comApp->getTotalRecords(VEHICLES_TBL, "id", "", "vehicle_no", $f1Value, "", "");
        $data['message'] = $msg;
        $data['cmd'] = getRequest('cmd');
        require_once(CURRENT_APP_SKIN_FILE);
        return $data[0];
    }

    function deleteItem()
    {
        require_once(CLASS_DIR . '/common.class.php');
        $comApp = new Common();
        $vehicle_id = getRequest('id');
        $comApp->deleteRecord(VEHICLES_TBL, "id", $vehicle_id, "vehicle", "list");
    }

} // End class
?>
