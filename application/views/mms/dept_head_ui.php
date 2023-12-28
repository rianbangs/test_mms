<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>MMS</title>
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="shortcut icon" type="image/png" href="<?php echo base_url();?>assets/img/cart_logo.png"/>
    <link rel="bookmark" href="favicon_16.ico"/>
    <link href="<?php echo base_url(); ?>assets/css/site.min.css" rel="stylesheet"/>
    <link href="<?php echo base_url(); ?>assets/css/datatables.min.css" rel="stylesheet"/>
    <link href="<?php echo base_url(); ?>assets/css/googleapis.css" rel="stylesheet"/>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/sweetalert.css">
    <link href="<?php echo base_url(); ?>assets/fontawesome/css/all.min.css" rel="stylesheet"/>
    <link href="<?php echo base_url(); ?>assets/css/select2.min.css" rel="stylesheet"/>

    <script src="<?php echo base_url(); ?>assets/js/jquery-3.6.0.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/site.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/sweetalert.js"></script>         
    <script src="<?php echo base_url(); ?>assets/js/datatables.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/sweetalert2.all.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/select2.min.js"></script>
    <style>

            @import url('https://fonts.googleapis.com/css2?family=Open+Sans&display=swap');

            *{
                list-style: none;
                text-decoration: none;
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Open Sans', sans-serif;
            }

            body{
                background: #f5f6fa;
            }

            .wrapper .sidebar{
                background: rgb(5, 68, 104);
                position: fixed;
                top: 0;
                left: 0;
                width: 225px;
                height: 100%;
                padding: 20px 0;
                transition: all 0.5s ease;
                border-right: 1px solid black;
            }

            .wrapper .sidebar .profile{
                margin-bottom: 30px;
                text-align: center;
            }

            .wrapper .sidebar .profile img{
                display: block;
                width: 100px;
                height: 100px;
                border-radius: 50%;
                margin: 0 auto;
            }

            .wrapper .sidebar .profile h3{
                color: #ffffff;
                margin: 10px 0 5px;
            }

            .wrapper .sidebar .profile p{
                color: rgb(206, 240, 253);
                font-size: 14px;
            }
            .wrapper .sidebar ul li a{
                display: block;
                padding: 13px 30px;
                border-bottom: 1px solid #10558d;
                color: rgb(241, 237, 237);
                font-size: 16px;
                position: relative;
            }

            .wrapper .sidebar ul li a .icon{
                color: #dee4ec;
                width: 30px;
                display: inline-block;
            }
            .wrapper .sidebar ul li a:hover,
            .wrapper .sidebar ul li a.selected{
                color: #0c7db1;

                background:white;
                border-right: 2px solid rgb(5, 68, 104);
            }

            .wrapper .sidebar ul li a:hover .icon,
            .wrapper .sidebar ul li a.active .icon{
                color: #0c7db1;
            }

            .wrapper .sidebar ul li a:hover:before,
            .wrapper .sidebar ul li a.active:before{
                display: block;
            }
            .wrapper .section{
                width: calc(100% - 225px);
                margin-left: 225px;
                transition: all 0.5s ease;
            }

            .wrapper .section .top_navbar{
                background: rgb(5, 68, 104);
                height: 50px;
                display: flex;
                align-items: center;
                padding: 0 30px;

            }

            .wrapper .section .top_navbar .hamburger a{
                font-size: 28px;
                color: #f4fbff;
            }

            .wrapper .section .top_navbar .hamburger a:hover{
                color: #a2ecff;
            }

            body.active .wrapper .sidebar{
                left: -225px;
            }

            body.active .wrapper .section{
                margin-left: 0;
                width: 100%;
                height: 100%;
            }
        

    </style>
</head>
<body>

    <div class="wrapper">
        <div class="section">
            <div class="top_navbar">
                <div class="hamburger">
                    <a href="#">
                        <i class="fas fa-bars"></i>
                    </a>
                </div>
            </div>

            <div id="main_div" style="padding: 30px; overflow-y: auto;">
               <?php 
                    if($active_nav == 1)
                        $this->load->view("mms/dept_dashboard_ui"); //// pagtawag sa display sa daashboard
                    if($active_nav == 2)
                        $this->load->view("mms/super_vendor_ui");
                    if($active_nav == 3)
                        $this->load->view("mms/dept_season_ui");
                    if($active_nav == 4)
                        $this->load->view("mms/dept_season_item_ui");
                    // if($active_nav == 5)
                        // $this->load->view("mms/super_uom_uploading_ui");
                ?>
            </div>

        </div>
        <!--Top menu -->
        <div class="sidebar">
            <div class="profile">
                <?php 
                    $details = $this->Acct_mod->retrieveUserDetails();
                    $photo = $this->Acct_mod->getPhoto($details["emp_id"]);
                    $src = str_replace("..", "http://172.16.161.34:8080/hrms/", $photo);
                    if($src=="")
                        $src = "https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png";

                    echo '<img src="'.$src.'" alt="profile_picture">'
                ?>
                
                <!-- <img src="https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png" alt="profile_picture"> -->
                <h3></h3>
                <?php 
                    $user   = $this->Acct_mod->retrieveEmployeeName($details["emp_id"])["name"];
                    echo '<p>'.$user.'</p>';
                    $user_type = ($details["user_type"]=="dept-admin") ? "MIS" : strtoupper($details["user_type"]);
                    echo '<p><b>'.$user_type.'</b></p>';
                ?>
            </div>
           <!--profile image & text-->

           <ul class="sidebar-menu" data-widget="tree">
 

                <li>
                  <a class="<?php echo $active_nav == 1 ? 'selected' : '#';?>" href="<?php echo base_url('Dept_ctrl/page/1');?>">
                    <i class="fas fa-tachometer-alt"></i> <span> Dashboard</span>
                  </a>
                </li>

                 <li>
                  <a class="<?php echo $active_nav == 2 ? 'selected' : '#';?>" href="<?php echo base_url('Dept_ctrl/page/2');?>">
                    <i class="fas fa-store"></i> <span> Vendors </span>
                  </a>
                </li>

                 <li>
                  <a class="<?php echo ($active_nav == 3 || $active_nav == 4) ? 'selected' : '#';?>" href="<?php echo base_url('Dept_ctrl/page/3');?>">
                    <i class="fa-regular fa-sun"></i> <span> Season Config </span>
                  </a>
                </li>

 <!--
                <li>
                   <a class="<?php echo $active_nav == 5 ? 'selected' : '#';?>" href="<?php echo base_url('Dept_ctrl/page/5');?>">
                    <i class="fas fa-upload"></i> <span> UOM Uploading </span>
                  </a>
                </li>

                 <li>
                  <a class="<?php //echo $active_nav == 1 ? 'selected' : '#';?>" href="<?php //echo base_url('Mms_ctrl/mms_ui/1');?>">
                    <i class="fa-solid fa-briefcase"></i> <span> Business Units </span>
                  </a>
                </li> -->

                 

                <li>
                    <a href="<?php echo base_url('Dept_ctrl/logout');?>">
                        <i class="fa fa-power-off"></i> <span class="item">Logout</span>
                    </a>
                </li>
            </ul>
        </div>
            <!--menu item-->
        </div>


    </div>
    

  <script>
            var hamburger = document.querySelector(".hamburger");
            hamburger.addEventListener("click", function(){
            document.querySelector("body").classList.toggle("active");
            }) 

            // setInterval(function() {
            //   $.ajax({ 
            //             type:'POST',
            //             url:'<?php echo base_url('Dept_ctrl/session_check_js'); ?>',
            //             success: function(data){
            //               try{
            //                 var res = JSON.parse(data);
            //                 console.log(res);
                              
            //               }catch(e){
            //                 location.reload();
            //               }
                             
            //             }      
            //        }); 
            // }, 2000); 
  </script>
</body>
</html>        

        

