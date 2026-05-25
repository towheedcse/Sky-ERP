<?php 
class Subcategory_model extends CI_Model {
		
		function __construct()
		{
			parent::__construct();
		}	
		
		function InsertSubCategory(){
		    $categoryName	=$this->input->post('category');
			$subCategory	=$this->input->post('subcategory-name');
			$subid		=$this->input->post('sub-id');
			if($subid==''){
			$data = array('cid' =>$categoryName,'name' =>$subCategory);
			$this->db->insert(SUB_CATEGORY_TBL, $data);
			}else{
				$this->EditSubCategory($subid);
			}
			
			//print  $this->db->last_query();
	   }
	   //============== Category Retrive by Ajax================
	   function GetSubCategoryGrid(){
		$this->db->select('s.subid,s.cid,c.name,s.name as subname',FALSE);
		$this->db->from(SUB_CATEGORY_TBL.' AS s');		
		$this->db->join(CATEGORY_TBL.' AS c', 'c.cid=s.cid', 	'LEFT');
		$this->db->group_by('s.subid');		
		$this->db->order_by('s.name','ASC');
		$query = $this->db->get();
		echo '<table width="100%"  border="0" class="table table-responsive table-bordered table-hover custab">
			<thead>
			  <tr class="active">
				<th width="40%">Sub Category</th>
				<th width="40%">Category</th>
				<th width="20%" class="text-center">Action</th>
			  </tr>
			</thead>';
			  $i=0;
			  foreach($query->result() as $row){
			  //if($i%2==0){ $tblrow="success";}else{$tblrow="warning";}
			  echo "<tr class='default'>
				<td>".$row->subname."</td>
				<td>".$row->name."</td>
				<td class='text-center'><span data-toggle='tooltip' data-placement='top' data-original-title='Edit'> <a class='btn btn-info btn-xs' data-toggle='modal' title='Edit' onclick=myFunction('".$row->subid."') id='".$row->subid."' href='#addModal'><span class='glyphicon glyphicon-edit'> Edit</span></a></span>
				<span data-toggle='tooltip' data-placement='top' data-original-title='Delete'><a class='btn btn-danger btn-xs' data-toggle='modal' title='Delete' onclick=deleteSubCategory('".$row->subid."') id='".$row->subid."' href='#deleteModal'><span class='glyphicon glyphicon-trash'> Del</span></a></span></td>
			  </tr>";
			  $i++;
			  }
			echo '</table>';
		}
		function DelSubCategory(){
			//$id 			=$this->uri->segment(3);
			$id =$this->input->post('id');
			$this->db->where('subid',$id);
			$this->db->delete(SUB_CATEGORY_TBL);
			//$this->session->set_flashdata('msg', 'Delete successfully!');
			//redirect('subcategory', 'location');
		}
		//------------------
		function FillSubCategory(){
				$cat_id	=$this->input->post('id');
				$this->db->select('*');
				$this->db->from(SUB_CATEGORY_TBL);
				$this->db->where('subid', $cat_id);
				$query = $this->db->get();
				return $query->row();
		}
		function EditSubCategory($subid){
			$categoryName	=$this->input->post('category');
			$subCategory	=$this->input->post('subcategory-name');
			$data = array('cid' =>$categoryName,'name' =>$subCategory);
			$this->db->where('subid',$subid);
			$q=$this->db->update(SUB_CATEGORY_TBL, $data);
	   }
	   function GetSubCategoryList(){
		$this->db->select('s.subid,s.cid,c.name,s.name as subname',FALSE);
		$this->db->from(SUB_CATEGORY_TBL.' AS s');		
		$this->db->join(CATEGORY_TBL.' AS c', 'c.cid=s.cid', 	'LEFT');
		$this->db->group_by('s.subid');		
		$this->db->order_by('s.name','ASC');
		$query = $this->db->get();
		return $query;
	   }
	   function GetCategoryList(){
		$this->db->select('*');
		$this->db->from(CATEGORY_TBL);
		$this->db->order_by('name','ASC');
		$query = $this->db->get();
		return $query;
		}
	   
   //End Class
}
