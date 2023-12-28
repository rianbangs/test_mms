<?php
/**
 * 
 */
class Log_ctrl extends CI_Controller
{
    
    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Po_mod');
        $this->load->model('Acct_mod');

        if(isset($_SESSION['user_id'])){
            if($this->Acct_mod->getUserCountById($_SESSION['user_id'])>0){
                $userType = $this->Acct_mod->retrieveUserDetails()["user_type"];
                if($userType=="super-admin")
                    redirect(base_url('Super_ctrl/page'));
                else if(in_array($userType,array('buyer','category-head','corp-manager'))  )
                    redirect(base_url('Mms_ctrl/mms_ui'));
                else if(in_array($userType,array('incorporator'))  )
                    redirect(base_url('Sales_ctrl/page'));
                else if(in_array($userType,array('dept-admin'))  )
                    redirect(base_url('Dept_ctrl/page'));
            }else
                unset($_SESSION['user_id']);

            
        }

    }

    public function index(){
        $this->load->view("mms/login_ui");
    }

    public function login(){
        $user = $_POST["user"];
        $pass = $_POST["pass"];

        $id = $this->Acct_mod->retrieveAccountID($user,$pass);

        if($id<1)
            echo json_encode(array("Wrong Credentials!","Error"));
        else{
            $_SESSION['user_id'] = $id;
            echo json_encode(array("Successfully Login!","Success"));
        }     
    }

    function session_check_js(){
        $response = 'yes'; 
        
        $data['response'] = $response;
        echo json_encode($data);
     }

    function sample_ob(){
        echo '<style>
                .progress {
                  width: 100%;
                  height: 20px;
                  background-color: #f2f2f2;
                  border-radius: 10px;
                  overflow: hidden;
                }

                .progress-bar {
                  height: 100%;
                  background-color: #4caf50;
                  width: 0;
                  transition: width 0.5s ease-in-out;
                }
        </style>

        <input type="hidden" id="progress-hidden">    
        <div class="progress"><div class="progress-bar" id="myProgressBar"></div></div>';

    
        // Enable output buffering
        // ob_start();
        // Perform your long-running task
        $totalIterations = 1000;
        $i = 1;
        while( $i <= $totalIterations ) {
            // Perform each iteration of your task
            // Calculate progress percentage
            $progress = ($i / $totalIterations) * 100;

            // Output the progress bar
            echo '<script>';
            echo 'document.getElementById("myProgressBar").style.width = "'.$progress.'%";';
            echo '</script>';

            // Flush the output buffer
            ob_flush();
            flush();

            // Simulate a delay (remove this in your actual code)
            usleep(10000);
            $i++;
    
        }
     
        echo '<script>';
        echo 'document.getElementById("progress-hidden").value = "'.$i.'";';
        echo '</script>';
        // End output buffering
        // ob_end_flush();
    }

    function sample_iframe(){
        $this->load->view("iframe");
    }

}
?>