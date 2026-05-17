<?php
class DeliveryPoint
{
   function run() {     
		$cmd    = getRequest('cmd');
		$u_t_id = getFromSession('u_type_id');
		if($u_t_id == 101) 
		{      
		  switch($cmd) { 
		  	 case 'add'                	: $screen = $this->showEditor($msg); break;
      	     case 'edit'               	: $screen = $this->showEditor("Edit Page");    break;			 
      	   	 case 'doUpdate'           	: $screen = $this->showEditor($msg); break;
		     case 'delete'             	: $screen = $this->deleteItem(); break;
			 default                   	: $cmd = 'list'; $screen = $this->showEditor($msg);   break;
		  }
		}else {
      		header("location:index.php?app=user_home&msg=You are not authorised !!!");
      	} 
		
		return true;
   }
  function showEditor()
  {
	 require_once(CLASS_DIR.'/common.class.php');	
	 $comApp = new Common(); 
	 require_once(CLASS_DIR.'/common.list.class.php');	
	 $comListApp 	= new CommonList(); 
	 $delivery_pid = getRequest('id');
	 $ledger_id = getRequest('ledger_id');	

        $inventory_type = getRequest('inventory_type');
        $inventory_type_name = getRequest('inventory_type_name');
        $sales_ledger = getRequest('sales_ledger');
        $wip_ledger = getRequest('wip_ledger');
        $discount_ledger = getRequest('discount_ledger');
        $return_ledger = getRequest('return_ledger');

        $vat_ledger = getRequest('vat_ledger');
        $payable_vat_ledger = getRequest('payable_vat_ledger');
 
	 $data               = array();	

	 if($delivery_pid){
	 $TBDArr			= $comApp->getRecordInfo(DELIVERY_POINT_TBL,"delivery_pid",$delivery_pid);      
	 $TBDArr 			= parseThisValue($TBDArr);
	 $data        		= array_merge(array(),$TBDArr);		 			
	 if(getRequest('save')){
		$section = $this->getHeadType($ledger_id);
		$extraData["ledger_id"] = $ledger_id;
		$extraData["section"] = $section;
                $extraData["inventory_type"] = $inventory_type;
                $extraData["sales_ledger"] = $sales_ledger;
                $extraData["wip_ledger"] = $wip_ledger;
                $extraData["discount_ledger"] = $discount_ledger;
                $extraData["return_ledger"] = $return_ledger;
                $extraData["vat_ledger"] = $vat_ledger;
                $extraData["payable_vat_ledger"] = $payable_vat_ledger;
                $extraData["inventory_type_name"] = $inventory_type_name;

		$comApp->updateRecord(DELIVERY_POINT_TBL,"delivery_pid",$delivery_pid,"","","","","deliverypoint","list", NULL, $extraData);
		$msg="Successfully Update Record !!!";
		header("location:?app=deliverypoint&cmd=list&msg=$msg");	      	
	 } 
	} else {		
	if(getRequest('save')) {
		$accessories_id = $comApp->NewID(DELIVERY_POINT_TBL,"delivery_pid","D0000","D",5);

		$section = $this->getHeadType($ledger_id);
		$extraData["ledger_id"] = $ledger_id;
		$extraData["section"] = $section;
                $extraData["inventory_type"] = $inventory_type;
                $extraData["sales_ledger"] = $sales_ledger;
                $extraData["wip_ledger"] = $wip_ledger;
                $extraData["discount_ledger"] = $discount_ledger;
                $extraData["return_ledger"] = $return_ledger;
                $extraData["vat_ledger"] = $vat_ledger;
                $extraData["payable_vat_ledger"] = $payable_vat_ledger;
                $extraData["inventory_type_name"] = $inventory_type_name;

		$comApp->saveRecord(DELIVERY_POINT_TBL,"delivery_pid",$accessories_id,"","","created_by","created_date","deliverypoint","list", NULL, $extraData);
		$msg="Successfully Save Record !!!";
		header("location:?app=deliverypoint&cmd=list&msg=$msg");     		       		      	
	 }			 
	}
	$f1Value = getRequest('srckey');
	$from = getRequest('from'); if($from==""){ $from = 0;}
	$to = getRequest('to'); if($to==""){ $to = 45;}
	$data['brand_list']  	= $this->getRecordlist($f1Value,$from,$to);

	$data['totalrecord']  	= $comApp->getTotalRecords(DELIVERY_POINT_TBL,"delivery_pid","","delivery_point_name",$f1Value,"",""); 
	$data['catagory_list']	= $comListApp->getCatagoryList();

	//$raw_material_list = $comListApp->getAccountHeadList("Current Assets", NULL, "C000055");
	//$fg_list 	   = $comListApp->getAccountHeadList("Current Assets", NULL, "C000056");
        //$maintanance_list  = $comListApp->getAccountHeadList("Current Assets", NULL, "C000154");
	//$head_list	   = array_merge($raw_material_list, $fg_list, $maintanance_list);

	$head_list = $comListApp->getAccountList("Current Assets", "S127");

	$wip_ledger_list = $comListApp->getAccountHeadList("Current Assets", "S127", "C000057");
	$sales_ledger_list = $comListApp->getAccountHeadList("Operating Revenue", "S124", "C000127");
	$discount_ledger_list = $comListApp->getAccountHeadList("Operating Revenue", "S124", "C000128");
	$return_ledger_list = $comListApp->getAccountHeadList("Operating Revenue", "S124", "C000129");

        //$vat_on_sale_list  = $comListApp->getAccountHeadList("Current Liabilities", NULL, "C000148"); //oracle 

	$vat_on_sale_list = $comListApp->getAccountList("Current Assets", "S112", "C000061", "S300247"); // thai
        $payable_vat_list = $comListApp->getAccountList("Current Liabilities", "S162");

	$data['project_id'] 	= getFromSession('project_id');
	$data['message'] 		= $msg;
	$data['cmd']     		= getRequest('cmd'); 
	require_once(CURRENT_APP_SKIN_FILE);
	return $data[0];
   }  

    function getRecordlist($srckey, $from, $to)
    {
        if ($from == "" && $to == "") {
            $from = 0;
            $to = 50;
        }

	$project_id = getFromSession('project_id');

        //$SQL = "SELECT d.*, s.sub_id, s.sub_head_name, s.code, s.head_details FROM " . DELIVERY_POINT_TBL . " d LEFT JOIN " . SUB_ACC_HEAD_TBL . " s ON BINARY s.sub_id = d.ledger_id WHERE d.project_id = '$project_id'";

	$SQL = "
			SELECT 
				d.*,
			
				-- main ledger
				s.sub_id AS ledger_sub_id,
				s.sub_head_name AS ledger_name,
				s.code AS ledger_code,
				s.head_details AS ledger_details,
						
				-- sales ledger
				sl.sub_id AS sales_sub_id,
				sl.sub_head_name AS sales_ledger_name,
				sl.code AS sales_ledger_code,
				sl.head_details AS sales_ledger_details,

				-- wip ledger
				wipl.sub_id AS wip_sub_id,
				wipl.sub_head_name AS wip_ledger_name,
				wipl.code AS wip_ledger_code,
				wipl.head_details AS wip_ledger_details,
				
				-- discount ledger
				dl.sub_id AS discount_sub_id,
				dl.sub_head_name AS discount_ledger_name,
				dl.code AS discount_ledger_code,
				dl.head_details AS discount_ledger_details,
				
				-- return ledger
				rl.sub_id AS return_sub_id,
				rl.sub_head_name AS return_ledger_name,
				rl.code AS return_ledger_code,
				rl.head_details AS return_ledger_details,
			
				-- diposit vat ledger
				vl.sub_id AS vat_sub_id,
				vl.sub_head_name AS vat_ledger_name,
				vl.code AS vat_ledger_code,
				vl.head_details AS vat_ledger_details,

				-- payable vat ledger
				pvl.sub_id AS payable_vat_sub_id,
				pvl.sub_head_name AS payable_vat_ledger_name,
				pvl.code AS payable_vat_ledger_code,
				pvl.head_details AS payable_vat_ledger_details
			
			FROM " . DELIVERY_POINT_TBL . " d
			
			LEFT JOIN " . SUB_ACC_HEAD_TBL . " s
				ON BINARY s.sub_id = d.ledger_id
			
			LEFT JOIN " . SUB_ACC_HEAD_TBL . " sl
				ON BINARY sl.sub_id = d.sales_ledger
			
			LEFT JOIN " . SUB_ACC_HEAD_TBL . " wipl
				ON BINARY wipl.sub_id = d.wip_ledger
			
			LEFT JOIN " . SUB_ACC_HEAD_TBL . " dl
				ON BINARY dl.sub_id = d.discount_ledger
			
			LEFT JOIN " . SUB_ACC_HEAD_TBL . " rl
				ON BINARY rl.sub_id = d.return_ledger
			
			LEFT JOIN " . SUB_ACC_HEAD_TBL . " vl
				ON BINARY vl.sub_id = d.vat_ledger

			LEFT JOIN " . SUB_ACC_HEAD_TBL . " pvl
				ON BINARY pvl.sub_id = d.payable_vat_ledger
			
			WHERE d.project_id = '$project_id'
			";

        if ($srckey != "") {
            $SQL .= " AND d.delivery_point_name LIKE '%" . $srckey . "%'";
        }

        $SQL .= " ORDER BY d.delivery_pid LIMIT $from,$to";

        $res = query($SQL);

        $data = array();
        if (!empty($res)) {
            foreach ($res as $i => $v) {
                $data[$i] = $v;
            }
        }

        return $data;
    }


   function getHeadType($sub_id){
        $inventory_type = getRequest('inventory_type');
	if(!empty($inventory_type)){
		if($inventory_type == "Raw Materials"){
			return 'raw_material';
		}elseif($inventory_type == "Sales Item"){
			return 'fg';
		}elseif($inventory_type == "General Invetory"){
			return 'maintenance';
		}
	}
	$project_id = getFromSession('project_id');

        $info = array();
        $info['table'] = SUB_ACC_HEAD_TBL;
        $info['where'] = "head_type = 'Current Assets' AND project_id = '$project_id' AND sub_id='$sub_id'";
        $res = select($info);
	$data = null;

        if (count($res)) {
            foreach ($res as $i => $v) {
                $data = $v;
            }
        }

        if (!empty($data)) {
		if($data->child_head == "C000055"){
			return 'raw_material';
		}elseif($data->child_head == "C000056"){
			return 'fg';
		}elseif($data->child_head == "C000154"){
			return 'maintenance';
		}
        }

        return false; 
   }  


   function deleteItem(){
	require_once(CLASS_DIR.'/common.class.php');	
	$comApp = new Common(); 
	$delivery_pid = getRequest('id');
	$comApp->deleteRecord(DELIVERY_POINT_TBL,"delivery_pid",$delivery_pid,"deliverypoint","list"); 
   }  
} // End class
?>
