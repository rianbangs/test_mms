<?php
/**
 * 
 */
class Super_ctrl extends CI_Controller
{
      function __construct()
      {
         parent::__construct();
         $this->load->library('session'); 
         $this->load->model('Acct_mod');
         $this->load->model('Po_mod');

         if(!isset($_SESSION['user_id'])){
            redirect(base_url('Log_ctrl/index'));
            
         }else{

            if($this->Acct_mod->getUserCountById($_SESSION['user_id'])<1){
               unset($_SESSION['user_id']);
               redirect(base_url('Log_ctrl/index'));
            }else{
               $userType = $this->Acct_mod->retrieveUserDetails()["user_type"];
               if($userType!="super-admin"){
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

      function page($index=1)
      {
         $data['active_nav'] = $index;
         $this->load->view("mms/super_head_ui",$data);         
         
      }

      function dashboardCount(){
         $count_users = $this->db->count_all("reorder_users");
         $count_vendors = $this->Po_mod->countVendors();

         echo json_encode(array("user"=>$count_users,"vendor"=>$count_vendors));

      }

      function logout(){
        unset($_SESSION['user_id']);
        redirect(base_url('Log_ctrl/index'));
     }

      function searchEmployee(){
         if(!empty($_POST)){
            $name = $_POST["fullname"];
            $list = $this->Acct_mod->retrieveEmployee($name);
            echo json_encode($list);

         }
      }

      function registerUser(){
         if(!empty($_POST)){
            $payload = $this->input->post(NULL, TRUE);
            if($this->Acct_mod->checkUserEmp_id($payload['emp_id']) == true)
            {
               echo 'User-exists';
               return false;
               
            }elseif($this->Acct_mod->checkUserName($payload['username']) == true)
            {
               echo 'Username-exists';
               return false;
               
            }else{
               if($payload['usertype'] == 'buyer' OR $payload['usertype'] == 'category-head'){
                  $group = implode(',', $payload['val']);
                  $store = $payload['store'];
               }else{
                  $group = "";
                  $store = "";
               }
               
               $data = array(
                  'emp_id'       => $this->security->xss_clean($payload['emp_id']),
                  'username'     => $this->security->xss_clean($payload['username']),
                  'group_code'   => $this->security->xss_clean($group),
                  'store_id'     => $this->security->xss_clean($store),
                  'user_type'    => $this->security->xss_clean($payload['usertype']),
                  'password'     => $this->security->xss_clean(password_hash('1234', PASSWORD_DEFAULT))
                  
               );
               $this->Acct_mod->addUser($data);
               echo 'ok';
               return false;
            }
         }
      }

      function saveKey(){
         if(!empty($_POST)){
            $payload = $this->input->post(NULL, TRUE);
            
            $data["username"] = $this->security->xss_clean($payload['username']);
            
            if($this->Acct_mod->checkKeyExist($payload['user_id']) == true){
               if($payload['password']!=""){
                  $data["password"] = $this->security->xss_clean(password_hash($payload['password'], PASSWORD_DEFAULT));
               }
               $this->Acct_mod->editKey($data, $payload['user_id']);
               echo 'ok';
               return false;
            }else{

               $data["user_id"] = $this->security->xss_clean($payload['user_id']);
               $data["password"] = $this->security->xss_clean(password_hash($payload['password'], PASSWORD_DEFAULT));
               $this->Acct_mod->addKey($data);
               echo 'ok';
               return false;
            }
            
         }
      }

      function updateUser(){
         if(!empty($_POST)){
            

            $payload = $this->input->post(NULL, TRUE);
            $user_id = $payload['id'];
            $oldDetails = $this->Acct_mod->getUserData($user_id);
            //print_r($oldDetails['username']); die();
            if($oldDetails->username != $payload['username'] && $this->Acct_mod->checkUserName($payload['username']) == true)
            {
               echo 'Username-exists';
               return false;
               
            }else{
               if($payload['usertype'] == 'buyer' OR $payload['usertype'] == 'category-head'){
                  $group = implode(',', $payload['val']);
                  $store = $payload['store'];
               }else{
                  $group = "";
                  $store = "";
               }
               
               $data["username"] = $this->security->xss_clean($payload['username']);
               $data["group_code"] = $this->security->xss_clean($group);
               $data["store_id"] = $this->security->xss_clean($store);
               $data["user_type"] = $this->security->xss_clean($payload['usertype']);
               
               if($payload['password']!=""){
                  $data["password"] = $this->security->xss_clean(password_hash($payload['password'], PASSWORD_DEFAULT));
               }

               $this->Acct_mod->updateUser2($data, $user_id);
               echo 'ok';
               return false;
            }
         }
            
         //$this->session->set_flashdata('SUCCESSMSG1', "success");
         //redirect('users');
      }

      function users() //displays the list of users in the table for Admin users
      {
         $users = $this->Acct_mod->get_users();
         //$fetch_data = $this->blacklist_model->get_blacklist();
         $data = [];

            foreach ($users as $user) {
               $userdetails = $this->Acct_mod->find_an_employee($user->emp_id);
               
               $bu = $this->Acct_mod->bu_name(@$userdetails->bunit_code, @$userdetails->company_code);
               $dept = $this->Acct_mod->dept_name(@$userdetails->bunit_code, @$userdetails->company_code, @$userdetails->dept_code);
               $id   = $user->user_id;

               $sub_array = [];
                  $sub_array[] = $userdetails->name;
                  $sub_array[] = $userdetails->position;
                  $sub_array[] = $dept->dept_name;
                  $sub_array[] = $user->username;
                  $sub_array[] = $user->group_code;
                  
                  $sub_array[] = $bu->business_unit;
                  $sub_array[] = $user->user_type;
                  
                  $key = "";

                  $key .= '
                     <a id="edit-'.$id.'" class="action" title="Modify User" style="color: orange; cursor: pointer"><i class="fa fa-edit" aria-hidden="true" ></i></a>&nbsp;&nbsp; ';

                  if($user->user_type == 'category-head'){
                     if($this->Acct_mod->checkKeyExist($id) == true){
                        $key .= '
                           | &nbsp;<a id="editkey-'.$id.'" class="action" title="Update Key" style="color: orange; cursor: pointer"><i class="fa fa-key" aria-hidden="true" ></i></a>&nbsp;&nbsp;';
                     }else{
                        $key .= '
                           | &nbsp;<a id="addkey-'.$id.'" class="action" title="Add Key" style="color: orange; cursor: pointer"><i class="fa fa-key" aria-hidden="true" ></i></a>&nbsp;&nbsp;';
                     }
                  }   
                            
                  
                  
                 
                  $sub_array[] = $key;
                       
               $data[] = $sub_array;
            }

            $output = array(  
                  "draw"                      =>     intval($_POST["draw"]),  
                  "recordsTotal"              =>     $this->Acct_mod->get_all_data(),  
                  "recordsFiltered"           =>     $this->Acct_mod->get_filtered_data(),  
                  "data"                      =>     $data  
              );  
            echo json_encode($output); 
      }

      function edituser_content()
      {
            
         $row  = $this->Acct_mod->getUserData($_POST['ids']);
         //$buid = $this->Admin_Model->getUserBu($row->bunit_code, $row->company_code);
         //$userdetails = $this->employee_model->find_an_employee($row->emp_id);
         
         $group_code = $row->group_code;
         $group_codes = explode(",",$group_code);
         
         // for($c=0; $c<count($group_codes); $c++){

         // }
               
         echo  '<div class ="row">
               <div class="col-sm-12">
                  <div class="form-group">
                     <label class="control-label col-sm-2" for="user">Username:</label>
                     <div class="col-sm-4">
                        <input type="hidden" class="form-control" name="id" id="id" autocomplete="off" value="'.$row->user_id.'" required>
                        <input type="hidden" class="form-control" name="user_type" id="user_type" autocomplete="off" value="'.$row->user_type.'" required>
                        <input type="text" class="form-control" name="username" id="username" autocomplete="off" value="'.$row->username.'" required>
                     </div>

                     <label class="control-label col-sm-2" for="password">New Password:</label>
                     <div class="col-sm-4">
                        
                        <input type="text" class="form-control" id="password" name="password">
                     </div>
                  </div>';

                  if($row->user_type == 'buyer' OR $row->user_type == 'category-head'){
                     
                     $v_sc1 = (in_array("SC1",$group_codes)) ? "checked" : "";
                     $v_sc2 = (in_array("SC2",$group_codes)) ? "checked" : "";
                     $v_sc3 = (in_array("SC3",$group_codes)) ? "checked" : "";
                     $v_sc4 = (in_array("SC4",$group_codes)) ? "checked" : "";
                     $v_sc5 = (in_array("SC5",$group_codes)) ? "checked" : "";
                     $v_sc6 = (in_array("SC6",$group_codes)) ? "checked" : "";
                     $v_sc7 = (in_array("SC7",$group_codes)) ? "checked" : "";
                     $v_sc8 = (in_array("SC8",$group_codes)) ? "checked" : "";
                     $v_sc9 = (in_array("SC9",$group_codes)) ? "checked" : "";
                     $v_sc10 = (in_array("SC10",$group_codes)) ? "checked" : "";
                     $v_ds1 = (in_array("DS1",$group_codes)) ? "checked" : "";
                     $v_ds2 = (in_array("DS2",$group_codes)) ? "checked" : "";
                     $v_ds3 = (in_array("DS3",$group_codes)) ? "checked" : "";
                     
                     $store_id = $row->store_id;
                     $s_1 = ($store_id==1) ? "selected" : "";
                     $s_2 = ($store_id==2) ? "selected" : "";
                     $s_3 = ($store_id==3) ? "selected" : "";
                     $s_4 = ($store_id==4) ? "selected" : "";
                     $s_5 = ($store_id==5) ? "selected" : "";
                     $s_6 = ($store_id==6) ? "selected" : "";

                     echo'<div class="form-group ">
                        <label class="control-label col-sm-2" for="vendor">Vendor Category:</label>
                        <div class="col-sm-4">
                           <ul >
                              <li>
                                 <div style="margin-bottom: 8px;" class="form-group">
                                    <label style="margin-left: 5px;"> SC1 </label> <input type="checkbox" id="tasks" name="tasks[]" value="SC1" '.$v_sc1.'>
                                    <label style="margin-left: 5px;"> SC2 </label> <input type="checkbox" id="tasks" name="tasks[]" value="SC2" '.$v_sc2.'>
                                    <label style="margin-left: 5px;"> SC3 </label> <input type="checkbox" id="tasks" name="tasks[]" value="SC3" '.$v_sc3.'>
                                    <label style="margin-left: 5px;"> SC4 </label> <input type="checkbox" id="tasks" name="tasks[]" value="SC4" '.$v_sc4.'>
                                    <label style="margin-left: 5px;"> SC5 </label> <input type="checkbox" id="tasks" name="tasks[]" value="SC5" '.$v_sc5.'> 
                                    <label style="margin-left: 5px;"> SC6 </label> <input type="checkbox" id="tasks" name="tasks[]" value="SC6" '.$v_sc6.'>  
                                    <label style="margin-left: 5px;"> SC7 </label> <input type="checkbox" id="tasks" name="tasks[]" value="SC7" '.$v_sc7.'> 
                                    <label style="margin-left: 5px;"> SC8 </label> <input type="checkbox" id="tasks" name="tasks[]" value="SC8" '.$v_sc8.'> 
                                    <label style="margin-left: 5px;"> SC9 </label> <input type="checkbox" id="tasks" name="tasks[]" value="SC9" '.$v_sc9.'> 
                                    <label style="margin-left: 5px;"> SC10</label> <input type="checkbox" id="tasks" name="tasks[]" value="SC10" '.$v_sc10.'> 
                                    <label style="margin-left: 5px;"> DS1 </label> <input type="checkbox" id="tasks" name="tasks[]" value="DS1" '.$v_ds1.'> 
                                    <label style="margin-left: 5px;"> DS2 </label> <input type="checkbox" id="tasks" name="tasks[]" value="DS2" '.$v_ds2.'> 
                                    <label style="margin-left: 5px;"> DS3 </label> <input type="checkbox" id="tasks" name="tasks[]" value="DS3" '.$v_ds3.'> 
                            
                                 </div>
                              </li>
                           </ul>
                        </div>

                     <label class="control-label col-sm-2" for="store">Store: </label>
                     <div class="col-sm-4">
                        <select class="form-control" id="store1">
                           <option value="1" '.$s_1.'>ICM</option>
                           <option value="2" '.$s_2.'>ASC Mall</option>
                           <option value="3" '.$s_3.'>Plaza Marcela</option>
                           <option value="4" '.$s_4.'>ASC Talibon</option>
                           <option value="5" '.$s_5.'>Alta Citta</option>
                           <option value="6" '.$s_6.'>CENTRAL DC</option>
                       </select>
                     </div>
                  </div>';
                  }

               echo'</div>
            </div>

            <button type="submit" class="btn btn-primary"  value="Submit"><i class="fa fa-save"></i> Submit</button>';
      }

      function addkey_content() //displays content for adding key
      {
         $row  = $this->Acct_mod->getUserData($_POST['ids']);
        
         echo '<div class ="row">
                  <div class="col-md-12">      
                     <div class="form-group">
                        <label class="control-label col-sm-2" for="user">Username:</label>
                        <div class="col-sm-4">
                           <input type="hidden" class="form-control" name="id" id="id" autocomplete="off" value="'.$row->user_id.'" required>
                           <input type="text" class="form-control" name="username" id="username" autocomplete="off" required>
                        </div>

                        <label class="control-label col-sm-2" for="password">Password:</label>
                        <div class="col-sm-4">
                           <input type="password" class="form-control" id="password" name="password">
                        </div>
                     </div>
                  </div> 
               </div>';
    
      }

      function editkey_content() //displays content for updating key
        {
            $row  = $this->Acct_mod->getKeyData($_POST['ids']);
           
            echo'<div class ="row">
                    <div class="col-md-12">      
                        <div class="form-group">
                           <label class="control-label col-sm-2" for="user">Username:</label>
                           <div class="col-sm-4">
                              <input type="hidden" class="form-control" name="id" id="id" autocomplete="off" value="'.$row->user_id.'" required>
                              <input type="text" class="form-control" name="username" id="username" autocomplete="off" value="'.$row->username.'" required>
                           </div>

                           <label class="control-label col-sm-2" for="password">New Password:</label>
                           <div class="col-sm-4">
                              <input type="password" class="form-control" id="password" name="password" >
                           </div>
                        </div>
                    </div>  
                </div>';
       
        }

   }
?>