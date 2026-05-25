<?php 
class Dashboard_model extends CI_Model {
		
	function __construct()
	{
		parent::__construct();
	}	
	function getTab(){
		$this->db->select('*');
		$this->db->from("category");
		$this->db->order_by('name','ASC');
		$query = $this->db->get();
		return $query;
	}
	function getSubMenu($cid){
		$this->db->select('*');
		$this->db->from("subcategory");
		$this->db->where('cid',$cid);
		$this->db->order_by('name','ASC');
		$query = $this->db->get();
		//print $this->db->last_query();
		return $query;
	}
	function loadSubMenu($cid){
		$this->db->select('*');
		$this->db->from("subcategory");
		$this->db->where('cid',$cid);
		$this->db->order_by('name','ASC');
		$query = $this->db->get();		
		$submenu=""; $sl=1;				 
		foreach($query->result() as $subs){
		$submenu.="<button class='btn btn-success btn-lg btn-block' onClick=getSubId('".$subs->subid."') id='s".$subs->subid."'>".$subs->name."</button>";		
		$sl++;	
		}
        	return $submenu;              
	}
	function getSubMenuChields($sid){
		$this->db->select('*');
		$this->db->from(PRODUCT_TBL);
		$this->db->where('subid',$sid);
		$this->db->order_by('name','ASC');
		$query = $this->db->get();
		$Chields="";
		if($query->num_rows() >= 1){
			foreach($query->result() as $prow){	
				$Chields.="<a href='#' class='btn btn-primary btn-lg active' data-target='.bs-example-modal-sm' data-toggle='modal' 
				onClick=getPkgId('".$prow->pid."','".$prow->price."') >".$prow->name."</a>&nbsp;";
			}
		}
		return $Chields;
	}
  	function formatDateDMY($val){
		if($val){
			$yy = substr($val,0,4);
			$mm = substr($val,5,2);
			$dd = substr($val,8,2);
			return $dd.'/'.$mm.'/'.$yy;
		}
  	}
	function dateInputFormatDMY($val){
		if($val){
			$yy = substr($val,0,4);
			$mm = substr($val,5,2);
			$dd = substr($val,8,2);
			return $dd.'-'.$mm.'-'.$yy;
		}
	}
	
   //End Class
}
