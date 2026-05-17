<?php
function saveTopSizeSpection($order_ref_no,$totalsize){
   		for($x=0; $x<=9;$x++){
			$size_val ="";
			$size_spec_code = getRequest('sizespec'.$x);
			if($totalsize>0 && $totalsize <=10){
				$a = getRequest('a'.$x);
				if($a!=""){ $size_val.=$a."#";}
			}				
			if($totalsize>1 && $totalsize <=10){
				$b = getRequest('b'.$x);
				if($b!=""){ $size_val.=$b."#";}
			}	
			if($totalsize>2 && $totalsize <=10){
				$c = getRequest('c'.$x);
				if($c!=""){ $size_val.=$c."#";}
			}
			if($totalsize>3 && $totalsize <=10){
				$d = getRequest('d'.$x);
				if($d!=""){ $size_val.=$d."#";}
			}
			if($totalsize>4 && $totalsize <=10){
				$e = getRequest('e'.$x);
				if($e!=""){ $size_val.=$e."#";}
			}
			if($totalsize>5 && $totalsize <=10){
				$f = getRequest('f'.$x);
				if($f!=""){ $size_val.=$f."#";}
			}
			if($totalsize>6 && $totalsize <=10){
				$g = getRequest('g'.$x);
				if($g!=""){ $size_val.=$g."#";}
			}
			if($totalsize>7 && $totalsize <=10){
				$h = getRequest('h'.$x);
				if($h!=""){ $size_val.=$h."#";}
			}
			if($totalsize>8 && $totalsize <=10){
				$i = getRequest('i'.$x);
				if($i!=""){ $size_val.=$i."#";}
			}			
			if($totalsize>9 && $totalsize <=10){
				$j = getRequest('j'.$x);
				if($j!=""){ $size_val.=$j."#";}
			}
			// ==== for save === 
			
			if($size_spec_code!="" && $size_val!=""){
				$ordersize_spec_no = "TOP-".$order_ref_no.$size_spec_code;
				$size_val 			= substr($size_val, 0, -1);
				$type 				= "TOP";
				$created_by			= getFromSession('userid');
		    	$created_date       = date('Y-m-d h:i:s');
				
				$sql = "INSERT INTO ".ORDER_SIZE_SPEC_TBL." (`ordersize_spec_no`, `order_ref_no`, `type`, `size_spec_code`, `size_val`, `created_by`, `created_date`) VALUES ('$ordersize_spec_no', '$order_ref_no', '$type', '$size_spec_code', '$size_val', '$created_by', '$created_date')";
				mysql_query($sql);
			}
			
		}//end for		
   }
   function saveBottomSizeSpection($order_ref_no,$totalsize){
   		for($x=0; $x<=9;$x++){
			$size_val ="";
			$size_spec_code = getRequest('btmsizespec'.$x);
			if($totalsize>0 && $totalsize <=10){
				$a = getRequest('btma'.$x);
				if($a!=""){ $size_val.=$a."#";}
			}				
			if($totalsize>1 && $totalsize <=10){
				$b = getRequest('btmb'.$x);
				if($b!=""){ $size_val.=$b."#";}
			}	
			if($totalsize>2 && $totalsize <=10){
				$c = getRequest('btmc'.$x);
				if($c!=""){ $size_val.=$c."#";}
			}
			if($totalsize>3 && $totalsize <=10){
				$d = getRequest('btmd'.$x);
				if($d!=""){ $size_val.=$d."#";}
			}
			if($totalsize>4 && $totalsize <=10){
				$e = getRequest('btme'.$x);
				if($e!=""){ $size_val.=$e."#";}
			}
			if($totalsize>5 && $totalsize <=10){
				$f = getRequest('btmf'.$x);
				if($f!=""){ $size_val.=$f."#";}
			}
			if($totalsize>6 && $totalsize <=10){
				$g = getRequest('btmg'.$x);
				if($g!=""){ $size_val.=$g."#";}
			}
			if($totalsize>7 && $totalsize <=10){
				$h = getRequest('btmh'.$x);
				if($h!=""){ $size_val.=$h."#";}
			}
			if($totalsize>8 && $totalsize <=10){
				$i = getRequest('btmi'.$x);
				if($i!=""){ $size_val.=$i."#";}
			}			
			if($totalsize>9 && $totalsize <=10){
				$j = getRequest('btmj'.$x);
				if($j!=""){ $size_val.=$j."#";}
			}
			// ==== for save === 
			
			if($size_spec_code!="" && $size_val!=""){
				$ordersize_spec_no = "BOTTOM-".$order_ref_no.$size_spec_code;
				$size_val 			= substr($size_val, 0, -1);
				$type 				= "BOTTOM";
				$created_by			= getFromSession('userid');
		    	$created_date       = date('Y-m-d h:i:s');
				
				$sql = "INSERT INTO ".ORDER_SIZE_SPEC_TBL." (`ordersize_spec_no`, `order_ref_no`, `type`, `size_spec_code`, `size_val`, `created_by`, `created_date`) VALUES ('$ordersize_spec_no', '$order_ref_no', '$type', '$size_spec_code', '$size_val', '$created_by', '$created_date')";
				mysql_query($sql);
			}
			
		}//end for		
   }
      
?>
