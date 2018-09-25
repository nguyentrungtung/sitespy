<?php

require_once("home.php"); // loading home controller

class domain_xcode extends Home
{

    public $user_id; 
    public $download_id;   
	
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');       
 		
 		$this->user_id=$this->session->userdata('user_id');
        $this->download_id=$this->session->userdata('download_id');
        set_time_limit(0);

        $this->important_feature();

        $this->member_validity();

        if($this->session->userdata('user_type') != 'Admin' && !in_array(5,$this->module_access))
        redirect('home/login_page', 'location'); 
    }



    public function index()
    {
    	$this->domain_xcode();
    }

    public function domain_xcode()
    {
        $data['body'] = 'admin/domain/domain_xcode';
        $data['page_title'] = $this->lang->line("Domain Encoder/Decoder");
        $this->_viewcontroller($data);
    }   

    public function domain_encode_action()
    {
        $this->load->library('web_common_report');
        $domains=$this->input->post('domains', true);
        $domains=explode(',',$domains);

        $str="<table class='table table-bordered table-hover table-striped'>";
        $str.="<tr><td>SL</td><td>Domain</td><td>Encoded Domain</td></tr>"; 

        $download_path=fopen("download/domain_encode/domain_encode_{$this->user_id}_{$this->download_id}.csv", "w");
        fprintf($download_path, chr(0xEF).chr(0xBB).chr(0xBF));    

        $write_data=array();            
        $write_data[]="Domain";            
        $write_data[]="Encoded Domain"; 

        fputcsv($download_path, $write_data);

        $i = 0;
        foreach($domains as $domain) :
            $domain = trim($domain);
            if(is_valid_url($domain) === TRUE || is_valid_domain_name($domain) === TRUE) :
                $i++;
                $output = $this->web_common_report->puny_encoder($domain);
                $str.= "<tr><td>$i</td><td>$domain</td><td>$output</td></tr>";

                $write_data=array();
         
                $write_data[]=$domain;
                $write_data[]=$output;
                fputcsv($download_path, $write_data);                
            endif;    
        endforeach;        
        
        echo $str.="</table>";       
    } 

    public function domain_decode_action() 
    {
        $this->load->library('web_common_report');
        $domains=$this->input->post('domains', true);
        $domains=explode(',',$domains);

        $str="<table class='table table-bordered table-hover table-striped'>";
        $str.="<tr><td>SL</td><td>Domain</td><td>Decoded Domain</td></tr>";   

        $download_path=fopen("download/domain_decode/domain_decode_{$this->user_id}_{$this->download_id}.csv", "w");
        fprintf($download_path, chr(0xEF).chr(0xBB).chr(0xBF));    

        $write_data=array();            
        $write_data[]="Domain";            
        $write_data[]="Decoded Domain"; 

        fputcsv($download_path, $write_data);                  

        $i = 0;
        foreach($domains as $domain) :
            $domain = trim($domain);
            if(is_valid_url($domain) === TRUE || is_valid_domain_name($domain) === TRUE) :
                $i++;
                $output = $this->web_common_report->punny_decoder($domain);
                $str.= "<tr><td>$i</td><td>$domain</td><td>$output</td></tr>";

                $write_data=array();
         
                $write_data[]=$domain;
                $write_data[]=$output;
                fputcsv($download_path, $write_data);                   
            endif;    
        endforeach;        
        
        echo $str.="</table>"; 
    }    

}    