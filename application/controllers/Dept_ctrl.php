<?php
/**
 * 
 */
class Dept_ctrl extends CI_Controller
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
               if($userType!="dept-admin"){
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
         $this->load->view("mms/dept_head_ui",$data);         
         
      }

      function logout(){
        unset($_SESSION['user_id']);
        redirect(base_url('Log_ctrl/index'));
      }

      function dashboardCount(){
         $count_seasonal = $this->Po_mod->countSeasonalItems();
         $count_vendors = $this->Po_mod->countVendors();

         echo json_encode(array("seasonal"=>$count_seasonal,"vendor"=>$count_vendors));

      }
     
      function addSeasonType(){
         if(!empty($_POST)){
            $season_name = $_POST["s_name"];
            $season_type = $_POST["s_type"];
            $period_start_month = $_POST["ps_month"];
            $period_start_day = $_POST["ps_day"];
            $period_end_month = $_POST["pe_month"];
            $period_end_day = $_POST["pe_day"];
            $percentage = $_POST["percent"];
            $no_ref_year = $_POST["ref_year"];

            $period_start = ($season_type=="Daily") ? $period_start_month."-".$period_start_day : $period_start_month;
            $period_end = ($season_type=="Daily") ? $period_end_month."-".$period_end_day : $period_end_month;

            //For Daily Comparison
            $date1 = new DateTime(date('Y').'-'.$period_start);
            $date2 = new DateTime(date('Y').'-'.$period_end);

            if($season_name=="" || ctype_space($season_name))
               echo json_encode(array("error","Pls Input Season Name!"));
            else if($season_type=="Daily" && $date1>$date2)
               echo json_encode(array("error","Period Start Must Be Less Than Period End!"));
            else if($season_type=="Monthly" && $period_start>$period_end)
               echo json_encode(array("error","Period Start Must Be Less Than Period End!"));
            else if($percentage=="" || ctype_space($percentage))
               echo json_encode(array("error","Pls Input Percentage!"));
            else if($no_ref_year=="" || ctype_space($no_ref_year))
               echo json_encode(array("error","Pls Input No. of Reference Year!"));
            else{
               $table = "season_type";
               
               $insert_array["season_name"] = $season_name;
               $insert_array["type_val"] = $season_type;
               $insert_array["period_start"] = $period_start;
               $insert_array["period_end"] = $period_end;
               $insert_array["percentage"] = $percentage;
               $insert_array["no_ref_year"] = $no_ref_year;

               $this->Po_mod->insertToTable($table,$insert_array);
               echo json_encode(array("success","Successfully Added!"));
            }
            
         }
      }

      function listSeasonTypes(){
         $list = $this->Po_mod->getSeasonTypes();
         echo json_encode($list);
      }

      function listSeasonTypesDirectById(){
         if(!empty($_POST)){
            $id = $_POST["type_id"];
            $list = $this->Po_mod->getSeasonTypesDirectById($id);
            echo json_encode($list);
         }
         
      }

      function updateSeasonType(){
         if(!empty($_POST)){
            $type_id = $_POST["type_id"];
            $season_name = $_POST["s_name"];
            $season_type = $_POST["s_type"];
            $period_start_month = $_POST["ps_month"];
            $period_start_day = $_POST["ps_day"];
            $period_end_month = $_POST["pe_month"];
            $period_end_day = $_POST["pe_day"];
            $percentage = $_POST["percent"];
            $no_ref_year = $_POST["ref_year"];

            $period_start = ($season_type=="Daily") ? $period_start_month."-".$period_start_day : $period_start_month;
            $period_end = ($season_type=="Daily") ? $period_end_month."-".$period_end_day : $period_end_month;

            //For Daily Comparison
            $date1 = new DateTime(date('Y').'-'.$period_start);
            $date2 = new DateTime(date('Y').'-'.$period_end);

            if($season_name=="" || ctype_space($season_name))
               echo json_encode(array("error","Pls Input Season Name!"));
            else if($season_type=="Daily" && $date1>$date2)
               echo json_encode(array("error","Period Start Must Be Less Than Period End!"));
            else if($season_type=="Monthly" && $period_start>$period_end)
               echo json_encode(array("error","Period Start Must Be Less Than Period End!"));
            else if($percentage=="" || ctype_space($percentage))
               echo json_encode(array("error","Pls Input Percentage!"));
            else if($no_ref_year=="" || ctype_space($no_ref_year))
               echo json_encode(array("error","Pls Input No. of Reference Year!"));
            else{
               $table = "season_type";
               
               $update_array["season_name"] = $season_name;
               $update_array["type_val"] = $season_type;
               $update_array["period_start"] = $period_start;
               $update_array["period_end"] = $period_end;
               $update_array["percentage"] = $percentage;
               $update_array["no_ref_year"] = $no_ref_year;

               $where_array["type_id"] = $type_id;

               $this->Po_mod->updateTable($table,$update_array,$where_array);
               echo json_encode(array("success","Successfully Updated!"));
            }
            
         }
      }

      


   }
?>