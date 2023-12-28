<?php

class PO_view_ctrl extends CI_Controller
{

   function __construct()
   {
     	parent::__construct();
        $this->load->library('session');
        $this->load->model('simplify/simplify','simplify');
        $this->load->model('simplify/pdf_simplify','pdf_');   
        $this->load->model('Po_mod');
        $this->load->model('Po_view_mod');
        $this->load->model('Acct_mod');
        
        if(!isset($_SESSION['user_id'])){
            redirect(base_url('Log_ctrl/index'));
            
         }else{

            if($this->Acct_mod->getUserCountById($_SESSION['user_id'])<1){
               unset($_SESSION['user_id']);
               redirect(base_url('Log_ctrl/index'));
            }else{
               $userType = $this->Acct_mod->retrieveUserDetails()["user_type"];
               if(!in_array($userType,array('buyer','category-head','corp-manager','incorporator')))
               //if($userType!="buyer")
               {
                  unset($_SESSION['user_id']);
                  redirect(base_url('Log_ctrl/index'));
               }
            }   
         }


   }

   function session_check_js(){
        $response = 'yes'; 
        
        $data['response'] = $response;
        echo json_encode($data);
     }

   		// herbert added code 8/29/2023..................................................................

	   function reorder_table_view_server_side()
	   {


	    $emp_info      = $this->Acct_mod->get_user_info($_SESSION['user_id']);
	    $start         = $this->input->post('start'); 
	    $length        = $this->input->post('length'); 
	    $searchValue   = $this->input->post('search')['value']; 
	   
	    $query = $this->db->select('*')
	                      ->from('reorder_po as po')
	                      ->join('reorder_store as str', 'po.store_id = str.store_id', 'inner')

	                      ->group_start()
	                      ->like('str.store_id', $searchValue)
	                      ->or_like('document_number', $searchValue)
	                      ->or_like('name', $searchValue)
	                      ->or_like('status', $searchValue)
	                      ->or_like('requested_by', $searchValue)
	                      ->or_like('remarks_by', $searchValue)
	                      ->or_like('vendor_code', $searchValue)
	                      ->or_like('vendor_name', $searchValue)
	                      ->group_end();

	                       if ($emp_info[0]['user_type'] == 'category-head') {
								     $this->db->where_in('status', array('Active','Cancel'));
								     $this->db->where("requested_by IS NOT NULL AND requested_by != ''");
								     $this->db->where("SUBSTRING(remarks_by, 1, 11) != 'Disapproved'");
								
								  } else if ($emp_info[0]['user_type'] == 'buyer') {


								  	  if($emp_info[0]['store_id'] != 6)
								  	  {

								  	     $this->db->where('str.store_id', $emp_info[0]['store_id']);

								  	  }

								     $this->db->where_in('status', array('Active','Cancel'));
								  }

	                       $this->db->limit($length, $start);
	                       $query = $this->db->get();

	     $totalRecords = $this->db->count_all('reorder_po');
	             $data = array(
	                           'draw'            => $this->input->post('draw'), 
	                           'recordsTotal'    => $totalRecords,
	                           'recordsFiltered' => $totalRecords,
	                           'data'            => $query->result()
	                          );

	                    echo json_encode($data);    
	   }

	   function updated_status_cancel()
	   {
	   	 $emp_info     = $this->Acct_mod->get_user_info($_SESSION['user_id']);
	   	 $emp_name     = $this->Acct_mod->retrieveEmployeeName($emp_info[0]['emp_id']);
	   	 $Active       = 'Active';
	   	 $Cancel       = 'Cancel';
	   	 $for_approval = 'For Approval to';


	   	 $status   = $_POST['status'];

	   	 if($emp_info[0]['user_type'] == 'category-head')
	   	 {
	   	 	if($status == 'Approved')
	   	 	{

	   	   	 $status = 'Approved';

	   	 	}else if($status == 'Disapproved'){

	   	 			   $status == 'Disapproved';
	   	      	 }

	   	 }else if($emp_info[0]['user_type'] == 'buyer'){


	   	   	 $status = $Active;

	   	 }

	   	 $data_cancel = $_POST['get_cancel_value'];

	   	 foreach($data_cancel as $data)
	   	 {
           if($emp_info[0]['user_type'] == 'buyer')
           {

            $this->db->set('status', $status);
            $this->db->set('requested_by', $emp_name['name']);
            $this->db->set('remarks_by', $for_approval.' '.$emp_name['name']);

           }else if($emp_info[0]['user_type'] == 'category-head'){

           		 if($status == 'Approved')
           		 {
                  
                  $this->db->set('status', $Cancel);
                  $this->db->set('remarks_by', $status.' by '.$emp_name['name']);

           		 }else if($status == 'Disapproved'){

                  $this->db->set('status', $Active);
                  $this->db->set('remarks_by', $status.' by '.$emp_name['name']);

           		 }

           }

           $this->db->where('document_number', $data['document_number']);
           $this->db->update('reorder_po');    
	   	 }
	   }
}


?>