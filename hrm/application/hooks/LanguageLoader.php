<?php
class LanguageLoader
{
    function initialize() {
        $ci =& get_instance();
        $ci->load->helper('language');

		$site_lang = $ci->session->userdata('language'); 
        if (isset($site_lang)) {
            $ci->lang->load('back',$ci->session->userdata('language'));
        } else {
            $ci->lang->load('back','en');
        }
    }
}
