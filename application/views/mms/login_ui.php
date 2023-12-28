<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>MMS</title>
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="shortcut icon" type="image/png" href="<?php echo base_url();?>assets/img/cart_logo.png"/>
    <link rel="bookmark" href="favicon_16.ico"/>
    <link href="<?php echo base_url(); ?>assets/css/site.min.css" rel="stylesheet"/>
    <link href="<?php echo base_url(); ?>assets/css/datatables.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url(); ?>assets/css/googleapis.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/sweetalert.css">
    <script src="<?php echo base_url(); ?>assets/js/jquery-3.6.0.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/site.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/sweetalert.js"></script>         
    <script src="<?php echo base_url(); ?>assets/js/datatables.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/sweetalert2.all.min.js"></script>

</head>
<body>
<div class="row">
    <div class="col-sm-5"></div>
    <div class="col-sm-2" style="padding: 8% 0;">
      <form class="form-horizontal" id="loginUserForm">
        <div style="margin-bottom: 5px;">
            <table width="100%">
              <tr>
                <td style="text-align: center; font-size: 100px; font-weight: bold; color: #054468;">
                  <i class="fa fa-shopping-cart"></i>
                </td>
                <td style="text-align: center; font-size: 20px; font-weight: bold; color: #054468;">
                  Merchandise Management System
                </td>
              </tr>
            </table> 
        </div>

      <fieldset>
        <div class="form-group">
            <input type="text" class="form-control" placeholder="Username" id="user" name="user">
        </div>
        <div class="form-group">
           <input type="password" class="form-control" placeholder="Password" id="pass" name="pass">
        </div>
        <button type="submit" class="btn btn-lg btn-primary btn-block">
          <span class="glyphicon glyphicon-log-in"></span> Login
        </button>
      </fieldset>
    </form>
  </div>
</div>
</body>
<script>

$(function() {
  $('#loginUserForm').submit(function(e){
    e.preventDefault();
    $.ajax({ 
            type:'POST',
            url:'<?php echo base_url('Log_ctrl/login'); ?>',
            data: $(this).serialize(),
            success: function(data){
              var res = JSON.parse(data);
              
              if(res[1]=='Success')
                location.reload();
              else
                Swal.fire({ title: '', text: res[0], icon: res[1].toLowerCase(), confirmButtonText: 'OK' });
              
            }      
       });      
  });
});

setInterval(function() {
  $.ajax({ 
            type:'POST',
            url:'<?php echo base_url('Log_ctrl/session_check_js'); ?>',
            success: function(data){
              try{
                var res = JSON.parse(data);
                console.log(res);
                  
              }catch(e){
                location.reload();
              }
                 
            }      
       }); 
}, 2000);

</script>
</html>