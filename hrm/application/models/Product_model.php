<?php 
class Product_model extends CI_Model {
		
	function __construct()
	{
		parent::__construct();
	}	

	function InsertAccountHead(){		
		$company_id = $this->input->post('institute_id');
		$branch_id 	= $this->input->post('branch_id');
		if($company_id=="" || $branch_id ==""){
			$company_id 	= $this->session->userdata('company_id');
			$branch_id 		= $this->session->userdata('branch_id'); 
		}
		$groupId			= $this->input->post('group_id');
		$account_name		= $this->input->post('product_name');
		$account_details	= $this->input->post('product_details');
		$account_type		= $this->input->post('account_type'); // 12=Inventory Item, 13=Sales Item
		$count_unit			= $this->input->post('count_unit');
		$subsidiary_level1	= 1;
		$subsidiary_level2	= 57;
		if($account_type==13){
		$subsidiary_level3	= 20;	
		}elseif($account_type==12){
		$subsidiary_level3	= 21;
		}
		$product_type = "";
		if($account_type==12 || $account_type==13){
			$product_type = "12,13";
		}else{
			$product_type = $account_type;
		}
		if($account_type==1){$prefix="M";}elseif($account_type==2){$prefix="A";}elseif($account_type==4){$prefix="C";}elseif($account_type==5){$prefix="B";}elseif($account_type==6){$prefix="T";}elseif($account_type==8){$prefix="R";}elseif($account_type==10){$prefix="E";}elseif($account_type==11){$prefix="S";}elseif($account_type==12 || $account_type==13){$prefix="I";}elseif($account_type==26){$prefix="P";}elseif($account_type==27){$prefix="L";}else{$prefix="H";}
		$head_id		= $this->getHeadID($product_type,$prefix);
    	$created_by		= $this->session->userdata('created_by');
		$accountId		= $this->input->post('product_id');

		if($accountId==""){
			$data = array(
			'company_id'    	=>$company_id,
			'branch_id'    		=>$branch_id,
			'head_id'    		=>$head_id,
			'group_id'    		=>$groupId,
			'subsidiary_level1' =>$subsidiary_level1,
			'subsidiary_level2' =>$subsidiary_level2,
			'subsidiary_level3' =>$subsidiary_level3,
			'head_type'    		=>$account_type,	
			'account_name'    	=>$account_name,
			'account_details' 	=>$account_details,
			'count_unit'     	=>$count_unit,
			'created_by'     	=>$created_by
			);
			$this->db->insert(ACC_HEAD_TBL, $data);
			return $this->db->insert_id();
		}
		//print  $this->db->last_query();
   	}
	
	function InsertRecord(){		
		$created_by			=$this->session->userdata('created_by');
		$productid			=$this->input->post('product_id'); 
		$product_code		=$this->input->post('product_code');
		
		$company_id = $this->input->post('institute_id');
		$branch_id 	= $this->input->post('branch_id');
		if($company_id=="" || $branch_id ==""){
			$company_id 	= $this->session->userdata('company_id');
			$branch_id 		= $this->session->userdata('branch_id'); 
		}
		$product_name		= $this->input->post('product_name');
		$product_details	= $this->input->post('product_details');
		$account_type		= $this->input->post('account_type'); // 12=Inventory Item, 13=Sales Item
		$count_unit			= $this->input->post('count_unit');
		$purchase_price		=$this->input->post('purchase_price');
		$sales_price		=$this->input->post('sales_price');
		$reorder_level		= $this->input->post('reorder_level');
		$status				=$this->input->post('status');
		if(empty($purchase_price)){$purchase_price=0;} if(empty($sales_price)){$sales_price=0;}
		if(empty($status)){$status=0;} if(empty($reorder_level)){$reorder_level=0;}  
		
		if($productid==''){
			$product_id 			= $this->InsertAccountHead();
			if(empty($product_code)){
				$ssql = "SELECT head_id FROM ".ACC_HEAD_TBL." WHERE account_id = $product_id AND company_id = $company_id AND branch_id = $branch_id";
				$squery = $this->db->query($ssql);				
				if($squery->num_rows() >0){				   
				   $product_code = $squery->row()->head_id;
				}
			}			
			$data = array(
			'product_id'			=>$product_id,
			'product_code'			=>$product_code,
			'company_id'    		=>$company_id,
			'branch_id'    			=>$branch_id,
			'product_name'    		=>$product_name,	
			'product_details'    	=>$product_details,
			'unit'    				=>$count_unit,
			'purchase_price'    	=>$purchase_price,	
			'sales_price'    		=>$sales_price,
			'reorder_level'			=>$reorder_level,
			'status'    			=>$status,
			'created_by'     		=>$created_by
			);
			//=== Remove empty field ====
			$data = array_filter($data);
			//=== Remove unexpected field by value (e.g value) ====
			if (($key = array_search('value', $data)) !== false) { unset($data[$key]);}						
			$this->db->insert(PRODUCT_TBL, $data);			
		}else{			
			$this->EditRecord($productid,$company_id,$branch_id);			
			$this->updateAccountHead($productid,$company_id,$branch_id);
		}		
		//print  $this->db->last_query();
   	}
	function UpdateRecord(){
		$productid			=$this->input->post('product_id');
		$institute_id		=$this->input->post('institute_id');
		$branch_id			=$this->input->post('branch_id');
		if($institute_id=="" || $branch_id ==""){
			$institute_id 	=$this->session->userdata('company_id');
			$branch_id 		=$this->session->userdata('branch_id'); 
		}		
		if($productid >0){
		$this->EditRecord($productid,$institute_id,$branch_id);			
		$this->updateAccountHead($productid,$institute_id,$branch_id);
		}
	}
	function updateAccountHead($account_id,$institute_id,$branch_id){
		    $update_by			= $this->session->userdata('created_by');
			$update_time 		= date("Y-m-d H:i:s");			
			
			$account_name		= $this->input->post('product_name');
			$account_details	= $this->input->post('product_details');
			$account_type		= $this->input->post('account_type'); // 12=Inventory Item, 13=Sales Item
			$count_unit			= $this->input->post('count_unit');
						
			$data = array(
			'account_name'    	=>$account_name,
			'bangla_name'    	=>$account_name,
			'account_details' 	=>$account_details,
			'head_type'    		=>$account_type,
			'count_unit'     	=>$count_unit,
			'update_by'     	=>$update_by,
			'update_time'     	=>$update_time
			);
			$this->db->where('account_id',$account_id);
			$this->db->where('company_id',$institute_id);
			$this->db->where('branch_id',$branch_id);
			$this->db->update(ACC_HEAD_TBL, $data); //print  $this->db->last_query();
	}
	function EditRecord($product_id,$institute_id,$branch_id){
		$modified_by		=$this->session->userdata('created_by');
		$modified_time 		=date("Y-m-d H:i:s"); 				
		
		$product_name		= $this->input->post('product_name');
		$product_details	= $this->input->post('product_details');
		$account_type		= $this->input->post('account_type'); // 12=Inventory Item, 13=Sales Item
		$count_unit			= $this->input->post('count_unit');
		
		$purchase_price		=$this->input->post('purchase_price');
		$sales_price		=$this->input->post('sales_price');
		$reorder_level		= $this->input->post('reorder_level');
		$status				=$this->input->post('status');
		if(empty($purchase_price)){$purchase_price=0;} if(empty($sales_price)){$sales_price=0;}
		if(empty($status)){$status=0;} if(empty($reorder_level)){$reorder_level=0;}   
					
		$data = array(			
			'company_id'    		=>$institute_id,
			'branch_id'    			=>$branch_id,
			'product_name'    		=>$product_name,	
			'product_details'    	=>$product_details,
			'unit'    				=>$count_unit,
			'purchase_price'    	=>$purchase_price,	
			'sales_price'    		=>$sales_price,
			'reorder_level'			=>$reorder_level,
			'status'    			=>$status,
			'modified_by'     		=>$modified_by,
			'modified_time'     	=>$modified_time
		);
		//=== Remove empty field ====
		$data = array_filter($data);
		//=== Remove unexpected field by value (e.g value) ====
		if (($key = array_search('value', $data)) !== false) { unset($data[$key]);}
		$this->db->where('id',$product_id);
		$this->db->where('company_id',$institute_id);
		$this->db->where('branch_id',$branch_id);
		$this->db->update(PRODUCT_TBL, $data); //print  $this->db->last_query();
    }
	    	
	function UploadPhoto($img_id){
		$file_name=''; $saveDir = ASSETS.'/img/photo/';
		$config['file_name']		= $img_id;
		$config['overwrite']		= TRUE;
		$config['upload_path'] 		= ASSETS.'/img/photo/';
		$config['allowed_types'] 	= 'gif|jpg|png|jpeg';
		$config['max_size'] 		= '144400';
		$config['max_width']  		= '1024';
		$config['max_height']  		= '768';
		$config['maintain_ratio']  	= FALSE;
		$config['width']  		= '185';
		$config['height']  		= '200';
		$this->load->library("upload",$config);
		//print_r($_FILES);
		foreach($_FILES as $field => $file){
			// No problems with the file
			if($file['error'] == 0){ //print_r($file);
				// So lets upload 
				if ($this->upload->do_upload($field)){ 
					$data =  $this->upload->data();					
					$file_name = $data['orig_name']; 
					//==== Resize Image ======
					$config2 = array(
						'source_image' => $data['full_path'],
						'new_image' => $saveDir,
						'maintain_ratio' => FALSE,
						'width' => 185,
						'height' => 200
					);			
					$this->load->library('image_lib', $config2);
					$this->image_lib->resize();					
					return trim("photo/".$file_name);
					                    
				}else{
					echo $errors = $this->upload->display_errors();
					return false;
				}
			}
		}// end foreach
		
	}
	
	function FillAccountHead(){
		$account_id =$this->input->post('id');
		$this->db->select('*');
		$this->db->from(ACC_HEAD_TBL);
		$this->db->where('status', 1);
		$this->db->where('account_id', $account_id);
		$query = $this->db->get();
		return $query->row();
	}
	function FillProduct(){
		$product_id =$this->input->post('id');
		$this->db->select('*');
		$this->db->from(PRODUCT_TBL);
		$this->db->where('status', 1);
		$this->db->where('id', $product_id);
		$query = $this->db->get(); //print  $this->db->last_query();
		return $query->row();
	}
	function GetAjaxProductList($product_id){		
		$PSQL= "SELECT * FROM ".ACC_HEAD_TBL." WHERE head_type IN(12,13) AND status=1 ";			
		$PSQL.= " GROUP BY account_id ORDER BY account_name ASC";
		$query = $this->db->query($PSQL);
		$options = "<option value='0'>".$this->lang->line('select')." ".$this->lang->line('product_name')."</option>";
		foreach($query->result() as $irow){			
			if($employee_id >0 && $employee_id == $irow->account_id){ 
				$selected = "selected='selected'"; 
			}else{ $selected = ""; }			
			$options.="<option  value='".$irow->account_id."' $selected >".$irow->account_name."</option>";
		}
		return $options;
	}
    
   	//============== Category Retrive by Ajax================
   	function GetRecordGrid(){
		$menu_slug= $this->uri->segment(1);
		$this->load->model('Site_model');
		$hasEditPM = $this->Site_model->hasOptionPermission($menu_slug,"Edit");
		$hasDelPM  = $this->Site_model->hasOptionPermission($menu_slug,"Delete");
		$hasPrintPM= $this->Site_model->hasOptionPermission($menu_slug,"Print");
		$company_id = $this->input->post('institute_id');
		$branch_id 	= $this->input->post('branch_id');
		if($company_id=="" || $branch_id ==""){
			$company_id 	= $this->session->userdata('company_id');
			$branch_id 		= $this->session->userdata('branch_id'); 
		}	   
		$account_type	= $this->input->post('account_type');
	   	$from	=$this->input->post('from');
		$to	=$this->input->post('to');
		if($from==""){ $from=0;} if($to==""){ $to=100;}			
		$head_types = array(12,13);	
		$this->db->select('a.*,p.account_id,p.group_id,p.subsidiary_level1,p.subsidiary_level2,p.head_type,p.account_name,p.bangla_name,p.count_unit,i.company_name,b.branch_name,b.branch_code',FALSE);
		$this->db->from(PRODUCT_TBL." AS a");
		$this->db->join(ACC_HEAD_TBL.' AS p', 'p.account_id=a.product_id','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.company_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.id=a.branch_id','LEFT');

		if($this->session->userdata('user_role') >1){
			$this->db->where("a.company_id", $this->session->userdata('company_id'));
		}else{
			$this->db->where("a.company_id", $company_id);
		}
		if($this->session->userdata('user_role') >2){
			$this->db->where("a.branch_id", $this->session->userdata('branch_id'));
		}else{
			$this->db->where("a.branch_id", $branch_id);
		}
		if($account_type >0){
			$this->db->where("p.head_type", $account_type);
		}else{
			$this->db->where_in('p.head_type',$head_types);
		}
		$this->db->group_by('a.id');
		$this->db->order_by('a.product_code','ASC');
		$this->db->limit($to,$from);
		$query = $this->db->get(); //print  $this->db->last_query();
		$totalrecord = $this->GetTotalRecord();
	    	$perPage=100; $Pagination="";
	    	if($totalrecord >0){
		   	$Pagination = $this->getPagination($totalrecord,$perPage);
	    	} //print  $this->db->last_query();
		echo 
		'<table width="100%"  border="0" class="table table-responsive table-bordered table-hover custab">
			<thead>
			  <tr class="active">
			  	<th width="2%">'.$this->lang->line("sl").'</th>
				<th width="18%">'.$this->lang->line("company").' '.$this->lang->line("details").'</th>
			  	<th width="15%">'.$this->lang->line("product_name").'</th>
				<th width="15%">'.$this->lang->line("product_details").'</th>
				<th width="8%">'.$this->lang->line("count_unit").'</th>
				<th width="10%">'.$this->lang->line("purchase_price").'</th>
				<th width="10%">'.$this->lang->line("sales_price").'</th>
				<th width="8%">'.$this->lang->line("reorder_level").'</th>
				<th width="14%" class="text-center">'.$this->lang->line("options").'</th>
			  </tr>
			</thead>';
			  $i=1; 
			  foreach($query->result() as $row){
			  //if($i%2==0){ $tblrow="success";}else{$tblrow="warning";}
			  if($row->status==1){ $tblrow="bg-success";}else{$tblrow="bg-danger";}
			  if($row->count_unit==4){ $count_unit="Pcs";}elseif($row->count_unit==5){ $count_unit="Dzn";}elseif($row->count_unit==6){ $count_unit="Fit";}elseif($row->count_unit==7){ $count_unit="Pack";}else{$count_unit="";}
			  echo "<tr class='default'>
			  	<td class='".$tblrow."'>".$i."</td>
				<td>
				".$row->company_name.",<br>".$row->branch_name."
				</td>
			  	<td>
				".$row->account_name.",<br>ID: ".$row->account_id."<br>Code: ".$row->product_code."				
				</td>
				<td>".$row->product_details."</td>
				<td>".$count_unit."</td>
				<td>".$row->purchase_price."</td>
				<td>".$row->sales_price."</td>
				<td>".$row->reorder_level." ".$count_unit."</td>
				<td class='text-center align-middle'>";
		    	if($hasEditPM){
				echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Edit'><a class='btn btn-info btn-xs' onclick=editRecord('".$row->id."') id='".$row->id."' href='#'><i class='fas fa-edit'></i></a></span>&nbsp;";
				}
				if($hasDelPM){
				echo "<span data-toggle='tooltip' data-placement='top' data-original-title='Delete'><a class='btn btn-danger btn-xs' data-toggle='modal' onclick=deleteRecord('".$row->id."') id='".$row->id."' data-target='#deleteModal'><i class='fa fa-trash'></i></a></span>";
				}				
			    echo "</td>
			  </tr>";
			  $i++;
			  }
		echo '</table>';
	    	echo "<div class='float-right'>$Pagination</div>";
	}
    
	function GetTotalRecord(){
		$company_id = $this->input->post('institute_id');
		$branch_id 	= $this->input->post('branch_id');
		if($company_id=="" || $branch_id ==""){
			$company_id 	= $this->session->userdata('company_id');
			$branch_id 		= $this->session->userdata('branch_id'); 
		}	   
		$account_type	= $this->input->post('account_type');	
		$head_types = array(12,13);	
		$this->db->select('a.*,p.account_id,p.group_id,p.subsidiary_level1,p.subsidiary_level2,p.head_type,p.account_name,p.bangla_name,p.count_unit,i.company_name,b.branch_name,b.branch_code',FALSE);
		$this->db->from(PRODUCT_TBL." AS a");
		$this->db->join(ACC_HEAD_TBL.' AS p', 'p.account_id=a.product_id','LEFT');
	  	$this->db->join(COMPANY_SETTINGS_TBL.' AS i', 'i.company_id=a.company_id','LEFT');
	  	$this->db->join(BRANCH_TBL.' AS b', 'b.id=a.branch_id','LEFT');

		if($this->session->userdata('user_role') >1){
			$this->db->where("a.company_id", $this->session->userdata('company_id'));
		}else{
			$this->db->where("a.company_id", $company_id);
		}
		if($this->session->userdata('user_role') >2){
			$this->db->where("a.branch_id", $this->session->userdata('branch_id'));
		}else{
			$this->db->where("a.branch_id", $branch_id);
		}
		if($account_type >0){
			$this->db->where("p.head_type", $account_type);
		}else{
			$this->db->where_in('p.head_type',$head_types);
		}
		$this->db->group_by('a.id');
		$this->db->order_by('a.product_code','ASC');
		$query = $this->db->get();
		if($query->num_rows() >0){
			return $query->num_rows();
		}else{
			return 0;
		}//echo $this->db->last_query();
	}
	
	function DelRecord(){
		$product_id =$this->input->post('id');
		$status =$this->input->post('status');
		if(!isset($status)){$status=0;}
		$ESQL= "UPDATE ".PRODUCT_TBL." SET status='".$status."' WHERE id='".$product_id."'";
		$this->db->query($ESQL);
		
		$ASQL= "UPDATE ".ACC_HEAD_TBL." SET status='".$status."'WHERE account_id='".$product_id."'";
		$this->db->query($ASQL);		
	}
	
    /*======Start Common Function for pagination=======*/
    function getHeadID($category,$prefix){
		$SQL = "SELECT max(head_id) AS maxhead FROM ".ACC_HEAD_TBL." WHERE head_type IN(".$category.")
		 AND `head_id` LIKE '".$prefix."%' ORDER BY `head_id` DESC";
		$query = $this->db->query($SQL);
		$maxheadId = $prefix."00000000";
		if($query->num_rows() >0){
			foreach($query->result() as $v){
				if($v->maxhead){
				 $maxheadId = $v->maxhead;
				}
				break;
			}
		}		
		return $maxheadId = $this->generateID("$prefix",$maxheadId,9);		
    }
    function generateID($priFix, $maxId, $len){
		$nextIdNum = trim($maxId,$priFix) + 1;
		$padlen = $len - (strlen($priFix) + strlen($nextIdNum)) +1 ;
    	$nextID = str_pad($priFix, $padlen, "0").$nextIdNum;	
		if	(strlen($nextID) <= $len)
			return $nextID;
		else
			return "ID over flow !!!";
    }
    function getPagination($totalrecord, $block)
    {
        $from_rs = $this->input->post('from');
        if ($from_rs == "") {
            $from_rs = 0;
        }
        if ($block == "") {
            $block = 12;
        }
        $to_rs = $from_rs + $block;
        if ($from_rs >= $block) {
            $from_rs = $from_rs + 1;
        }
        if ($from_rs == "" || $from_rs == 0) {
            $from_rs = 1;
        }
        if ($to_rs == "" || $totalrecord < $block) {
            $to_rs = $totalrecord;
        } else if ($to_rs == "" && $totalrecord > $block) {
            $to_rs = $block;
        }
        if ($to_rs > $totalrecord) {
            $to_rs = $totalrecord;
        }
        if ($totalrecord == 0) {
            $from_rs = 0;
        }

        $plink = $this->input->post('page_no');
        if ($plink == "") {
            $plink = 1;
        }
        if ($totalrecord > $block) {
            $res = $totalrecord / $block;
            $res = (int)$res;
            if (($totalrecord % $block) != 0) {
                $totalpage = $res + 1;
            } else {
                $totalpage = $res;
            }
        } else {
            $totalpage = 1;
        }
        $paginationStr = "";
        $paginationStr .= "<ul class='pagination pagination-sm m-0'>";

        if ($totalrecord > $block) {
            $two = $this->input->post('from');
            if ($two == "") {
                $two = 0;
            }
            $pno = $this->input->post('page_no');
            if ($pno == "") {
                $pno = 0;
            }
            $pno = $pno - 1;
            $frm = $two - $block;
            $to = $block;
            if ($pno <= $totalpage && $pno > 0) {
                $paginationStr .= "<li class='page-item'><a class='page-link' onclick=nextPage($frm,$to,$pno) href='#'>&laquo;</a></li>";
            }
        } else {
            $paginationStr .= "<li class='page-item disabled'><a class='page-link' href='#'>&laquo;</a></li>";
        }
        if ($totalpage >= 1) {
            $i = 1;
            $from = 0;
            $to = $block;
            while ($i <= $totalpage) {
                if ($from == 0) {
                    $paginationStr .= "<li class='page-item'";
                    if ($i == $plink) {
                        $paginationStr .= "class='active'";
                    }
                    $paginationStr .= ">";
                    $paginationStr .= "<a class='page-link' onclick=nextPage($from,$to,$i) href='#'>$i</a></li>";
                } else {
                    $paginationStr .= "<li class='page-item'";
                    if ($i == $plink) {
                        $paginationStr .= "class='active' ";
                    }
                    $paginationStr .= ">";
                    $paginationStr .= "<a class='page-link' onclick=nextPage($from,$to,$i) href='#'>$i</a></li>";
                }
                $i++;
                $from = $from + $block;
                if ($to > $totalrecord) {
                    $to = $totalrecord;
                }
            }
        }
        if ($totalrecord > $block) {
            $f = $this->input->post('from');
            $page = $this->input->post('page_no');
            $page = $page + 1;
            if ($f == "" || $f == 0) {
                $f = $block;
                $page = 2;
            } else {
                $f = $f + $block;
            }
            $t = $block;
            if ($t > $totalrecord) {
                $t = $totalrecord;
            }
            if ($page <= $totalpage) {
                $paginationStr .= "<li class='page-item'><a class='page-link' onclick=nextPage($f,$t,$page) href='#'>&raquo;</a></li>";
            }
        } else {
            $paginationStr .= "<li class='page-item disabled'><a class='page-link' href='#'>&raquo;</a></li>";
        }

        $paginationStr .= "</ul>";
        return $paginationStr;
    }

    function formatDate($dt)
    {
	if (trim($dt)) {
		$day = substr($dt, 0, 2);
		$month = substr($dt, 3, 2);
		$year = substr($dt, 6, 4);
		$hour = substr($dt, 11, 2);
		$minute = substr($dt, 14, 2);
		$second = substr($dt, 17, 2);
		$ampm = substr($dt, 20, 2);
		//echo $ampm;
		if ($hour == '' AND $minute == '' AND $second == '') {
			return $year . "-" . $month . "-" . $day;
		} else {
			if (strtoupper($ampm) == 'PM') {
				$hour = intval($hour) + 12;
				return $year . "-" . $month . "-" . $day . ' ' . $hour . ':' . $minute . ':' . $second;
			} else {
				return $year . "-" . $month . "-" . $day . ' ' . $hour . ':' . $minute . ':' . $second;
			}
		}
	}
    }

    function formatDateTimeDMY($dt)
    {
	if (trim($dt)) {
		$year = substr($dt, 0, 4);
		$month = substr($dt, 5, 2);
		$day = substr($dt, 8, 2);
		$hour = substr($dt, 11, 2);
		$minute = substr($dt, 14, 2);
		$second = substr($dt, 17, 2);
		$ampm = substr($dt, 20, 2);
		if ($hour == '' AND $minute == '' AND $second == '') {
			return $year . "-" . $month . "-" . $day;
		} else {
			if (strtoupper($ampm) == 'PM') {
				$hour = intval($hour) + 12;
				return $day . "-" . $month . "-" . $year . ' ' . $hour . ':' . $minute . ':' . $second;
			} else {
				return $day . "-" . $month . "-" . $year . ' ' . $hour . ':' . $minute . ':' . $second;
			}
		}
	}
    }
    function formatDateDMY($val)
    {
	if ($val) {
		$yy = substr($val, 0, 4);
		$mm = substr($val, 5, 2);
		$dd = substr($val, 8, 2);
		return $dd . '-' . $mm . '-' . $yy;
	}
    }
    function dateInputFormatDMY($val)
    {
		if ($val) {
			$yy = substr($val, 0, 4);
			$mm = substr($val, 5, 2);
			$dd = substr($val, 8, 2);
			return $dd . '-' . $mm . '-' . $yy;
		}
    }
}
