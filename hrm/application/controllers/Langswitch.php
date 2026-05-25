<?php
class Langswitch extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
    }

    function switchLanguage($language = "") {
        $language = ($language != "") ? $language : "en";
        $this->session->set_userdata('language', $language);
        redirect(base_url()."dashboard/Userhome");
    }
}
