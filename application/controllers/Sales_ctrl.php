<?php
/**
 * 
 */
class Sales_ctrl extends CI_Controller
{
    
    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Acct_mod');
        $this->load->model('Po_mod');
        $this->load->model('Sales_monitoring_mod');
        $this->load->model('Mms_mod');
        
        if(!isset($_SESSION['user_id'])){
            redirect(base_url('Log_ctrl/index'));
            
        }else{
            
            if($this->Acct_mod->getUserCountById($_SESSION['user_id'])<1){
               unset($_SESSION['user_id']);
               redirect(base_url('Log_ctrl/index'));
            }else{
               $userType = $this->Acct_mod->retrieveUserDetails()["user_type"];
               if($userType!="incorporator"){
                  unset($_SESSION['user_id']);
                  redirect(base_url('Log_ctrl/index'));
               }
            } 
        }

    }

    function page($index=14){
        
       $data['active_nav'] = $index;
       $this->load->view("sales_monitoring/sales_head_ui",$data);         
         
    }

    function load_dept_and_group(){
        $dept = $this->Acct_mod->select_dept();
        $group = $this->Acct_mod->select_group();

        echo json_encode(array($dept,$group));
    }

    function logout(){
        unset($_SESSION['user_id']);
        redirect(base_url('Log_ctrl/index'));
    }

    function session_check_js(){
        $response = 'yes'; 
        
        $data['response'] = $response;
        echo json_encode($data);
    }



}

?>