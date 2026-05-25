<?php 
class Category_model extends CI_Model {
		
	function __construct()
	{
		parent::__construct();
	}	
	
	function InsertRecord(){
	    $categoryName	=$this->input->post('category_name');
		$categoryId		=$this->input->post('category_id');
		if($categoryId==''){
		$data = array('category_name' =>$categoryName);
		$this->db->insert(CATEGORY_TBL, $data);
		}else{
			$this->EditCategory($categoryId);
		}
		
		//print  $this->db->last_query();
   }
   //============== Category Retrive by Ajax================
   function GetRecordGrid(){
	$this->db->select('*');
	$this->db->from(CATEGORY_TBL);
	$this->db->order_by('category_name','ASC');
	$query = $this->db->get();
	//echo $this->db->last_query();
	echo 
	'<table width="100%"  border="0" class="table table-responsive table-bordered table-hover custab">
		<thead>
		  <tr class="active">
		  	<th width="5%">Sl.</th>
			<th width="75%">Category Name</th>
			<th width="20%" class="text-center">Action</th>
		  </tr>
		</thead>';
		  $i=0;
		  foreach($query->result() as $row){
		  //if($i%2==0){ $tblrow="success";}else{$tblrow="warning";}
		  echo "<tr class='default'>
		  	<td>".$i."</td>
			<td>".$row->category_name."</td>
			<td class='text-center'><span data-toggle='tooltip' data-placement='top' data-original-title='Edit'> <a class='btn btn-info btn-xs' data-toggle='modal' onclick=myFunction('".$row->cat_id."') id='".$row->cat_id."' href='#addModal'><span class='glyphicon glyphicon-edit'></span> Edit</a></span>&nbsp;
			<span data-toggle='tooltip' data-placement='top' data-original-title='Delete'><a class='btn btn-danger btn-xs'  data-toggle='modal' onclick=deleteRecord('".$row->cat_id."') id='".$row->cat_id."' href='#deleteModal'><span class='glyphicon glyphicon-trash'></span> Del</a></span></td>
		  </tr>";
		  $i++;
		  }
		echo '</table>';
	}
	/*function DelCategory(){
		$id 			=$this->uri->segment(3);
		$this->db->where('cid',$id);
		$this->db->delete(CATEGORY_TBL);
		$this->session->set_flashdata('msg', 'Delete successfully!');
		redirect('category', 'location');
	}*/
	function DelRecord(){
		$id =$this->input->post('id');
		$this->db->where('cat_id',$id);
		$this->db->delete(CATEGORY_TBL);
	}
	function FillRecord(){
		$cat_id	=$this->input->post('id');
		$this->db->select('*');
		$this->db->from(CATEGORY_TBL);
		$this->db->where('cat_id', $cat_id);
		$query = $this->db->get();
		return $query->row();
	}
	function EditRecord($cat_id){
		$categoryName	=$this->input->post('category_name');
		$data = array(
			   'category_name' =>$categoryName);
		$this->db->where('cat_id',$cat_id);
		$q=$this->db->update(CATEGORY_TBL, $data);
    }
    function GetRecord(){
		$this->db->select('*');
		$this->db->from(CATEGORY_TBL);
		$this->db->order_by('category_name','ASC');
		$query = $this->db->get();
		return $query;
	}
}