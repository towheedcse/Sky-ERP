<?php 
class Activities_model extends CI_Model {
		
	function __construct()
	{
		parent::__construct();
	}	
	
    function GetPendingCardNum(){
		$this->db->select('*');
		$this->db->from(GUEST_TBL);
		$this->db->where("status", 0);
		$query = $this->db->get();
		if($query->num_rows() >0){
			return $query->num_rows();
		}else{
			return 0;
		}//echo $this->db->last_query();
    }	
	
    function GetReceivedCardNum(){
		$this->db->select('*');
		$this->db->from(GUEST_TBL);
		$this->db->where("status", 1);
		$query = $this->db->get();
		if($query->num_rows() >0){
			return $query->num_rows();
		}else{
			return 0;
		}//echo $this->db->last_query();
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
