<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pdf {

    private $CI; 
    public function __construct()
    {
		// Assign the CodeIgniter super-object
		$this->CI =& get_instance();
    }

    function load() {
        //include_once APPPATH.'/third_party/mpdf/mpdf.php';
        require_once(APPPATH."/third_party/mpdf/autoload.php");  
        //if ($params == NULL){ $param = '"en-GB-x","A4","","",10,10,10,10,6,3'; }
        //echo "Load"; 
        //return new mPDF($param);
        return $mpdf = new \Mpdf\Mpdf();
    }

}