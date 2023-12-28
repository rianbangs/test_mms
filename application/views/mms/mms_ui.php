 <?php
        $user_details  =  $this->Mms_mod->get_store_list('store');
        $store_list    = $this->Mms_mod->get_store_list('');
        
  ?> 

<div class="row">
   <div class="col-sm-3">
       <a class="btn btn-primary" href="<?php echo base_url('Mms_ctrl/mms_ui/2');?>">BACK</a>
   </div>
   <div class="col-sm-7">
   </div>
  <!--  <div class="col-sm-2">
       <a class="btn btn-danger" id="view_old_btn"     onclick = "show_old_files_uploader()" href="#">Upload Old Sales</a>           
       <a class="btn btn-warning" id="view_latest_btn"  onclick = "show_latest_files_uploader()" href="#">Upload Latest Sales</a>           
   </div> -->
</div>
<?php
  if($user_details[0]['value_'] == 'cdc')
  {              
?>
    <!--  <div class="row">
        <div class="col-sm-2">
        </div>
        <div class="col-sm-10">
               <?php 
                      foreach($store_list as $store)
                      {
                           if(in_array($store['bu_type'],array('STORE','NON STORE-DC')))
                           {                            
                               echo  '<div class="col-sm-2">
                                         <label>
                                             <input type="radio" name="options" value="'.$store['store_id'].'">'.$store['name'].' 
                                         </label>
                                      </div>'; 
                            }  
                      }
               ?>                
              
        </div>
         
     </div>  -->
 <?php
   }
  ?>
<div class="row">

       
    <div class="col-sm-10" >

        <body>
          <fieldset data-role="controlgroup" data-type="horizontal">
              <?php 
                  if (!empty($_POST)) {
                    $vendor_code = $_POST["vendor_code"];
                    $vendor_name = $_POST["vendor_name"];
                    $date_tag = $_POST["date_tag"];
                    $group_code = $_POST["group_code"];

                    if($vendor_code == 'UPLOAD OLD SALES')
                    {
                       echo '<p style="display: inline-block; padding: 0px; border: 1px solid gray; text-align: center; font-size: 1vw;"><strong>'.$vendor_code.'</strong></p>';
                    }
                    else 
                    {
                       echo '<p style="display: inline-block; padding: 0px; border: 1px solid gray; text-align: center; font-size: 1vw;">'.$vendor_code.' - '.$vendor_name.' ('.$group_code.') : '.$date_tag.'</p>';
                    }
                   
                    echo '<input type="hidden" id="v_code" value="'.$vendor_code.'">';
                    echo '<input type="hidden" id="d_tag" value="'.$date_tag.'">';
                     echo '<input type="hidden" id="group_code" value="'.$group_code.'">';
                  }else
                    redirect(base_url('Mms_ctrl/mms_ui/5'));
              ?>
              <br>
              <label style="font-size: 1vw;"><b>Select Store</b></label>
                <br>

                <style>
                  input.medium 
                  {
                    width: 20px;
                    height: 20px;
                  }

                  #icm_div, #asc_div, #pm_div, #tal_div, #alt_div, #ubay_div{
                    display: none;
                  }
                </style>
                  <?php
                          

                          //$store_list    = $this->Mms_mod->get_store_list($user_details[0]['value_']);
                          //$store_list    = $this->Mms_mod->get_store_list('');
                          foreach($store_list as $store)
                          {          
                               if($user_details[0]['value_'] != $store['value_'])              
                               {
                                  $checked = '';
                                  $display = '';
                               }
                               else 
                               {
                                  $checked = 'checked';
                                  $display = 'display:none;';                        
                               }

                               if($user_details[0]['value_'] == $store['value_'] || $store['value_'] == 'cdc' && !in_array($store['bu_type'],array('NON STORE')))
                               {                                
                                   echo ' <input type="checkbox" class="medium stores" name="'.$store['value_'].'" id="'.$store['value_'].'" value="'.$store['value_'].'"  style="'.$display.'"  '.$checked.'>
                                           <label for="'.$store['value_'].'"  style="'.$display.'">'.$store['name'].'</label> &emsp;&emsp;';
                               }
                               else 
                               if($user_details[0]['value_'] == 'cdc' && !in_array($store['bu_type'],array('NON STORE')) ) 
                               {
                                  echo ' <input type="checkbox" class="medium stores" name="'.$store['value_'].'" id="'.$store['value_'].'" value="'.$store['value_'].'"  style="'.$display.'"  '.$checked.'>
                                           <label for="'.$store['value_'].'"  style="'.$display.'">'.$store['name'].'</label> &emsp;&emsp;';
                               }                                
                              
                          }


                          // $user_details  =  $this->Mms_mod->get_store_list('');

                          // $store_list    = $this->Mms_mod->get_store_list($user_details[0]['value_']);
                          // foreach($store_list as $store)
                          // {          
                          //      if($user_details[0]['value_'] != $store['value_'])              
                          //      {
                          //         $checked = '';
                          //         $display = '';
                          //      }
                          //      else 
                          //      {
                          //         $checked = 'checked';
                          //         $display = 'display:none;';
                          //      }
                          //      echo ' <input type="checkbox" class="medium" name="'.$store['value_'].'" id="'.$store['value_'].'" value="'.$store['value_'].'"  style="'.$display.'"  '.$checked.'>
                          //             <label for="'.$store['value_'].'"  style="'.$display.'">'.$store['name'].'</label> &emsp;&emsp;';
                              
                          // }

                      if($user_details[0]['value_'] == 'cdc')
                      {
                            echo ' <input type="checkbox" class="medium booking_server" name="booking_server" id="booking_server"   style="'.$display.'" >
                                               <label for="booking_server"  style="'.$display.'">Distribution</label> &emsp;&emsp;';
                      }      
                   ?>

                  <!-- <input type="checkbox" class="medium" name="icm" id="icm2" value="icm">
                  <label for="icm2">ICM</label> &emsp;&emsp;
                  <input type="checkbox" class="medium" name="ascmall" id="ascmall3" value="asc">
                  <label for="ascmall3">ASC MALL</label> &emsp;&emsp;
                  <input type="checkbox" class="medium" name="pm" id="pm4" value="pm">
                  <label for="pm4">PLAZA MARCELA</label> &emsp;&emsp;
                  <input type="checkbox" class="medium" name="asctalibon" id="asctalibon5" value="tal">
                  <label for="asctalibon5">ASC TALIBON</label> &emsp;&emsp;
                  <input type="checkbox" class="medium" name="alta" id="alta6" value="alta">
                  <label for="alta6">ALTA CITTA</label> &emsp;&emsp;
                  <input type="checkbox" class="medium" name="ubaydc" id="ubaydc7" value="ubay">
                  <label for="ubaydc7">UBAY DC</label> &emsp;&emsp; -->

                   

          </fieldset>
        </body>
      </div>

 <!-- CDC  -->

      <div class="col-sm-12" style="text-align: left">
        <br>
        <br>

          <div class="row">
            <div class="col-sm-2">
              <label style="font-size: 1vw;" for="store">Store</label>
            </div>
            <div class="col-sm-2"> 
              <label style="font-size: 1vw;" for="r_report">Reorder Report</label>
            </div>
<?php        if($vendor_code != 'UPLOAD OLD SALES')
             {
                 echo ' <div class="col-sm-2 gen_report_old_fields">
                            <label style="font-size: 1vw;" for="vendor">Unposted Sales</label>
                        </div>  
                       <div class="col-sm-2 gen_report_old_fields">
                            <label style="font-size: 1vw;" for="purch_line_po_header">Pending PO</label>
                       </div>';  
             }
?>            
          </div>

          <?php   

                 //var_dump($user_details[0]['value_'] );
                 foreach($store_list  as $store)
                 {
                      if($user_details[0]['value_'] != $store['value_'])         
                      {
                         $display = '';
                      }
                      else 
                      {
                         $display = 'display:none;';                        
                                              
                      }


                      if($user_details[0]['value_'] == $store['value_'] || $store['value_'] == 'cdc' && !in_array($store['bu_type'],array('NON STORE')) )
                      {

                        
                          echo '
                               
                                <div id="'.$store['value_'].'_div" class="row"   style="'.$display.'">   
                                  <div class="col-sm-2 ">         
                                    <p style="display: inline-block; padding: 0px; width: 93%; border: 1px solid gray; text-align: center; font-size: 1vw;">'.$store['name'].'</p>
                                    &emsp;&emsp;&emsp; 
                                  </div>                                    
                                  <div class="col-sm-2">         
                                    <input type="file" class="btn" onchange="revert_color('."'".$store['value_']."_txt_file'".')" name="files[]" id="'.$store['value_'].'_txt_file"  style="display: inline-block; padding: 1px; width: 78%;">
                                  </div>';
                         if(in_array($store['value_'], array('icm','asc'))  && $vendor_code != 'UPLOAD OLD SALES')
                         {
                             echo ' 
                                    <div class="col-sm-2 gen_report_old_fields"> 
                                      <input type="file" class="btn" onchange="revert_color('."'".$store['value_']."_vendor_txt_file'".')"  name="files[]" id="'.$store['value_'].'_vendor_txt_file"  style="display: inline-block; padding: 1px; width: 78%;"> 
                                    </div>
                                  ';
                         }        

                         if($store['nav_type'] == 'NATIVE' && $vendor_code != 'UPLOAD OLD SALES')     
                         {
                              echo ' 
                                    <div class="col-sm-2 gen_report_old_fields"> 
                                      <input type="file" class="btn" onchange="revert_color('."'".$store['value_']."_pend_po_txt_file'".')"  name="files[]" id="'.$store['value_'].'_pend_po_txt_file"  style="display: inline-block; padding: 1px; width: 78%;"> 
                                    </div>
                                  ';
                         }

                          echo  '           
                                </div>';

                          echo '<script>  
                                             
                                             $(function(){
                                                          $("#'.$store['value_'].'").change(function(){
                                                              if($(this).is(":checked")){
                                                                  $("#'.$store['value_'].'_div").show();
                                                              } else{
                                                                  $("#'.$store['value_'].'_div").hide();
                                                              }
                                                          });
                                                        });';

                           if($user_details[0]['value_'] == $store['value_'])                             
                           {
                                echo ' $("#'.$store['value_'].'_div").show();';
                           }
                           else 
                           if($user_details[0]['value_'] != 'cdc') 
                           {
                                echo  '$("#'.$store['value_'].'_div").hide();';
                           }

                           echo   '</script>';                                  
                      }
                      else 
                      if($user_details[0]['value_'] == 'cdc' && !in_array($store['bu_type'],array('NON STORE')) ) 
                      {
                           

                           echo '
                               
                                <div id="'.$store['value_'].'_div"  class="row"  style="'.$display.' white-space: nowrap;"> 
                                  <div class="col-sm-2"> 
                                    <p style="display: inline-block; padding: 0px; width: 93%; border: 1px solid gray; text-align: center; font-size: 1vw;">'.$store['name'].'</p>
                                    &emsp;&emsp;&emsp;
                                  </div>  
                                  <div class="col-sm-2">    
                                    <input type="file" class="btn" onchange="revert_color('."'".$store['value_']."_txt_file'".')"  name="files[]" id="'.$store['value_'].'_txt_file"  style="display: inline-block; padding: 1px; width: 78%;">
                                  </div>';
                                if(in_array($store['value_'], array('icm','asc'))  && $vendor_code != 'UPLOAD OLD SALES')
                                {
                                     echo ' 
                                            <div class="col-sm-2 gen_report_old_fields"> 
                                              <input type="file" class="btn" onchange="revert_color('."'".$store['value_']."_vendor_txt_file'".')" name="files[]" id="'.$store['value_'].'_vendor_txt_file"  style="display: inline-block; padding: 1px; width: 78%;"> 
                                            </div>
                                          ';
                                }    
                                else 
                                if($vendor_code != 'UPLOAD OLD SALES')  
                                {
                                     echo ' <div class="col-sm-2 gen_report_old_fields"> 
                                                 
                                            </div>
                                          ';
                                }


                               if($store['nav_type'] == 'NATIVE' && $vendor_code != 'UPLOAD OLD SALES')     
                               {
                                    echo ' 
                                          <div class="col-sm-2 gen_report_old_fields"> 
                                            <input type="file" class="btn" onchange="revert_color('."'".$store['value_']."_pend_po_txt_file'".')"  name="files[]" id="'.$store['value_'].'_pend_po_txt_file"  style="display: inline-block; padding: 1px; width: 78%;"> 
                                          </div>
                                        ';
                               } 

                           echo  '</div>';
                           echo '<script>  
                                             
                                             $(function(){
                                                          $("#'.$store['value_'].'").change(function(){
                                                              if($(this).is(":checked")){
                                                                  $("#'.$store['value_'].'_div").show();
                                                              } else{
                                                                  $("#'.$store['value_'].'_div").hide();
                                                              }
                                                          });
                                                        });';    
                           if($store['value_'] != 'cdc') 
                           {
                                echo  '$("#'.$store['value_'].'_div").hide();';
                           }         
                           echo   '</script>';                           
                      }

                 }
           ?>

          <br><br><br>
          <?php
                 if($vendor_code == 'UPLOAD OLD SALES')  
                 {
                      echo '<button type="button" class="btn btn-success"    style="padding: 6px 6px;" onclick="extract_file('."'old sale'".')"><i class="glyphicon glyphicon-upload"></i> Upload Old Sales</button>';
                 }
                 else 
                 {
                      echo '<button type="button" class="btn btn-primary" id="gen_report_latest" style="padding: 6px 6px;" onclick="extract_file('."'new sale'".')"><i class="glyphicon glyphicon-upload"></i> Generate Report</button>';
                 }
           ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12" id="archive_table">

        </div>
    </div>

  <script>


  var checked_errors   = []; 
  var store_arr        = [];
  var checbox_type_arr = [];

 





 window.io = {
                open: function(verb, url, data, target){
                    var form = document.createElement("form");
                    form.action = url;
                    form.method = verb;
                    form.target = target || "_self";
                    if (data) {
                        for (var key in data) {
                            var input = document.createElement("textarea");
                            input.name = key;
                            input.value = typeof data[key] === "object"
                                ? JSON.stringify(data[key])
                                : data[key];
                            form.appendChild(input);
                        }

                    }
                    form.style.display = 'none';
                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                }
            };



 
  // middleware();//ifram middleware


// // Example of using a Web Worker
// const worker = new Worker('<?php echo base_url('assets/js/workers.js'); ?>');

// // Listen for messages from the worker
// worker.onmessage = (event) => {
//   console.log('Received message from worker:', event.data);
// };

// // Post a message to the worker
// worker.postMessage('Start time-consuming task');






function middleware()
{ 
        console.log("nisud");

        // Create a container div for the iframe
        var frameContainer = document.createElement('div');

        // Create an iframe element
        var iframe = document.createElement('iframe');
        iframe.name = 'myIframeMiddleware'; // Set a name for the iframe
        iframe.style.width = '231%';
        iframe.style.height = '100%'; // Set your desired height

        // Set the source URL directly
        iframe.src = 'http://172.16.192.150/navision/mms_middleware/';

        // Append the iframe to the container
        frameContainer.appendChild(iframe);

        // Append the container to the body or any other container element
        document.body.appendChild(frameContainer);

        // Center the iframe on the page using CSS
        frameContainer.style.position = 'absolute';
        frameContainer.style.top = '133%';
        frameContainer.style.left = '50%';
        frameContainer.style.transform = 'translate(-50%, -50%)';


        // console.log("nisud");

        // // Create a container div for the iframe
        // var frameContainer = document.createElement('div');

        // // Create an iframe element
        // var iframe = document.createElement('iframe');
        // iframe.name = 'myIframeMiddleware'; // Set a name for the iframe
        // iframe.style.width = '160%';
        // iframe.style.height = '100%'; // Set your desired height

        // // Set attributes for the iframe
        // // iframe.src = '<?php echo base_url('Mms_ctrl/middleware'); ?>';
        // iframe.src = 'http://172.16.45.130:3000';

        // // Create a form element
        // var form = document.createElement('form');
        // form.method = 'POST';
        // // form.action = '<?php echo base_url('Mms_ctrl/middleware'); ?>';
        // form.action = ' http://172.16.45.130:3000';
        // form.target = 'myIframeMiddleware'; // Set the target to the iframe

        // // Data to be sent
        // var formData = {
        //     "test": 'test'
             
        // };

        // // Append the iframe to the container
        // frameContainer.appendChild(iframe);




        // // Append the container to the body or any other container element
        // document.body.appendChild(frameContainer);

        // // Append the form to the iframe
        // iframe.appendChild(form);

        // // Center the iframe on the page using CSS
        // frameContainer.style.position = 'absolute';
        // frameContainer.style.top = '133%';
        // frameContainer.style.left = '50%';
        // frameContainer.style.transform = 'translate(-50%, -50%)';


        // // Submit the form
        // form.submit();

}





function progress_bar_(apilon_booking,v_code,d_tag,group_code,store_arr,file_list,file_content) 
{  


        // Create a container div for the iframe
        var frameContainer = document.createElement('div');       

        // Create an iframe element
        var iframe = document.createElement('iframe');
        iframe.name = 'myIframe'; // Set a name for the iframe

        // Set attributes for the iframe
        iframe.src = '<?php echo base_url('Mms_ctrl/extract_file_V6'); ?>';       
        iframe.style.width = '50%';
        iframe.style.height = '100%'; // Set your desired height

     



         // Create maximize button
        var maximizeButton = document.createElement('button');
        maximizeButton.innerHTML = '<i class="fas fa-window-maximize" style="font-size:35px;"></i>';
        maximizeButton.onclick = function () 
        {
            maximizeButton.style.marginRight = '49px';
            maximizeButton.style.position = 'absolute';
            maximizeButton.style.top = '0';
            maximizeButton.style.right = '0';     
            iframe.style.width = '100%';
            iframe.style.height = '100%';
            frameContainer.style.position = 'absolute';
            frameContainer.style.top = '50%';
            frameContainer.style.left = '50%';
            frameContainer.style.transform = 'translate(-50%, -50%)';
            frameContainer.style.zIndex = '5';
            frameContainer.style.cursor = 'move';
        };

        // Create minimize button
        var minimizeButton = document.createElement('button');
        minimizeButton.innerHTML = '<i class="fas fa-window-minimize" style="font-size:35px;"></i>';
        minimizeButton.onclick = function () 
        {
            minimizeButton.style.marginLeft = '-40px';
            minimizeButton.style.position = 'absolute';
            minimizeButton.style.top = '0';
            minimizeButton.style.right = '1';

            maximizeButton.style.marginRight = '549px';
            maximizeButton.style.position = 'absolute';
            maximizeButton.style.top = '0';
            maximizeButton.style.right = '0';         

            iframe.style.width = '50%';
            iframe.style.height = '50px';
            frameContainer.style.position = 'absolute';
            frameContainer.style.inset = '1027.44px 0px 0px 1404.73px';
            frameContainer.style.transform = 'translate(-50%, -50%)';
            frameContainer.style.zIndex = '5';
            frameContainer.style.cursor = 'move';
        };
         
        
 
            
         frameContainer.style.position = 'absolute';
         frameContainer.style.top = '50%';
         frameContainer.style.left = '50%';
         frameContainer.style.inset = '1027.44px 0px 0px 1404.73px';
         frameContainer.style.transform = 'translate(-50%, -50%)';
         frameContainer.style.zIndex = '5';
         frameContainer.style.cursor = 'move';


        // Append the iframe to the container
        frameContainer.appendChild(iframe);

       

        // Append the buttons to the container
        frameContainer.appendChild(maximizeButton);
        frameContainer.appendChild(minimizeButton);

        // Append the container to the body or any other container element
        document.body.appendChild(frameContainer);

        // Add drag functionality
        var isDragging = false;
        var offsetX, offsetY;

        frameContainer.addEventListener('mousedown', function (e) {
            isDragging = true;
            offsetX = e.clientX - frameContainer.getBoundingClientRect().left;
            offsetY = e.clientY - frameContainer.getBoundingClientRect().top;
        });

        document.addEventListener('mousemove', function (e) {
            if (isDragging) {
                frameContainer.style.left = e.clientX - offsetX + 'px';
                frameContainer.style.top = e.clientY - offsetY + 'px';
            }
        });

        document.addEventListener('mouseup', function () {
            isDragging = false;
        });



        // Create an iframe element
        var iframe = document.createElement('iframe');
        iframe.style.display = 'none'; // Hide the iframe
        iframe.name = 'myIframe'; // Set a name for the iframe

        // Create a form element
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo base_url('Mms_ctrl/extract_file_V6'); ?>';
        form.target = 'myIframe'; // Set the target to the iframe

        // Data to be sent
        var formData = {
            'store_arr': JSON.stringify(store_arr),
            'group_code': group_code,
            'apilon_booking': apilon_booking,
            'v_code': v_code,
            'd_tag': d_tag,
            'file_list': JSON.stringify(file_list),
            'file_content': JSON.stringify(file_content)
        };

        // Function to create and append input elements
        function addInput(name, value)
        {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            form.appendChild(input);
        }

        // Loop through formData and add input elements
        for (var key in formData) 
        {
            if (formData.hasOwnProperty(key)) 
            {
                addInput(key, formData[key]);
            }
        }

        // Append the form to the iframe
        iframe.appendChild(form);

        // Append the iframe to the document body
        document.body.appendChild(iframe);

        // Submit the form
        form.submit();



        // // Create a form element
        // var form = document.createElement('form');
        // form.method = 'POST';
        // form.action = '<?php echo base_url('Mms_ctrl/extract_file_V6'); ?>';
        // form.target = 'myIframe'; // Set the target to the iframe

        // // Create input elements
        // var input1 = document.createElement('input');
        // input1.type = 'hidden';
        // input1.name = 'store_arr';
        // input1.value = JSON.stringify(store_arr);

        // var input2 = document.createElement('input');
        // input2.type = 'hidden';
        // input2.name = 'group_code';
        // input2.value = group_code;

        // var input3 = document.createElement('input');
        // input3.type = 'hidden';
        // input3.name = 'apilon_booking';
        // input3.value = apilon_booking;


        // var input4 = document.createElement('input');
        // input4.type = 'hidden';
        // input4.name = 'v_code';
        // input4.value = v_code;

        // var input5 = document.createElement('input');
        // input5.type = 'hidden';
        // input5.name = 'd_tag';
        // input5.value = d_tag;

        // var input6 = document.createElement('input');
        // input6.type = 'hidden';
        // input6.name = 'file_list';
        // input6.value = JSON.stringify(file_list);


        // var input7 = document.createElement('input');
        // input7.type = 'hidden';
        // input7.name = 'file_content';
        // input7.value = JSON.stringify(file_content);


        // // Append input elements to the form
        // form.appendChild(input1);
        // form.appendChild(input2);
        // form.appendChild(input3);
        // form.appendChild(input4);
        // form.appendChild(input5);
        // form.appendChild(input6);
        // form.appendChild(input7);

        // // Append the form to the iframe
        // iframe.appendChild(form);

        // // Append the iframe to the document body
        // document.body.appendChild(iframe);

        // // // Submit the form
        // form.submit();
}






function progress_bar(apilon_booking,v_code,d_tag,group_code,store_arr,file_list,file_content) 
{  

        // Create a container div for the iframe
        var frameContainer = document.createElement('div');

        // Create an iframe element
        var iframe = document.createElement('iframe');
        iframe.name = 'myIframe'; // Set a name for the iframe

        // Set attributes for the iframe
        iframe.src = '<?php echo base_url('Mms_ctrl/extract_file_V6'); ?>';

        // Set styles for the container to center it on the page
        frameContainer.style.position = 'fixed';
        frameContainer.style.top = '50%';
        frameContainer.style.left = '50%';
        frameContainer.style.transform = 'translate(-50%, -50%)';

        // Set width and height for the container
        frameContainer.style.width = '864px'; // Set your desired width
        frameContainer.style.height = '118px'; // Set your desired height

        // Set styles for the iframe
        iframe.style.width = '100%'; // Set iframe width to 100% of its container
        iframe.style.height = '100%'; // Set iframe height to 100% of its container

        // Append the iframe to the container
        frameContainer.appendChild(iframe);

        // Append the container to the body or any other container element
        document.body.appendChild(frameContainer);

        // Add "load" event listener to the iframe
        iframe.addEventListener('load', function () 
        {
            // This function will be called when the iframe content is completely loaded
            console.log('Iframe content is loaded');             
            // Option 1: Hide the frameContainer
            // frameContainer.style.display = 'none';

            // Option 2: Remove the frameContainer from the DOM
            frameContainer.remove();
            Swal.fire({
                       icon: 'success',
                       title: 'success',
                       text: 'Successfully uploaded'                                  
                   }); 
            
            setTimeout(function()
            {        
                 window.location.href = '<?php echo base_url();?>Mms_ctrl/mms_ui/2';

            },2000);
             
        });






        // Add drag functionality
        var isDragging = false;
        var offsetX, offsetY;

        frameContainer.addEventListener('mousedown', function (e) {
            isDragging = true;
            offsetX = e.clientX - frameContainer.getBoundingClientRect().left;
            offsetY = e.clientY - frameContainer.getBoundingClientRect().top;
        });

        document.addEventListener('mousemove', function (e) {
            if (isDragging) {
                frameContainer.style.left = e.clientX - offsetX + 'px';
                frameContainer.style.top = e.clientY - offsetY + 'px';
            }
        });

        document.addEventListener('mouseup', function () {
            isDragging = false;
        });



        // Create an iframe element
        var iframe = document.createElement('iframe');
        iframe.style.display = 'none'; // Hide the iframe
        iframe.name = 'myIframe'; // Set a name for the iframe





        // Create a form element
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo base_url('Mms_ctrl/extract_file_V6'); ?>';
        form.target = 'myIframe'; // Set the target to the iframe







        // Data to be sent
        var formData = {
            'store_arr': JSON.stringify(store_arr),
            'group_code': group_code,
            'apilon_booking': apilon_booking,
            'v_code': v_code,
            'd_tag': d_tag,
            'file_list': JSON.stringify(file_list),
            'file_content': JSON.stringify(file_content)
        };

        // Function to create and append input elements
        function addInput(name, value)
        {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            form.appendChild(input);
        }

        // Loop through formData and add input elements
        for (var key in formData) 
        {
            if (formData.hasOwnProperty(key)) 
            {
                addInput(key, formData[key]);
            }
        }

        // Append the form to the iframe
        iframe.appendChild(form);

        // Append the iframe to the document body
        document.body.appendChild(iframe);

        // Submit the form
        form.submit();


        


}





function extract_file(sale_type) //original
{
      console.log(sale_type);

      // // Get all the checked checkboxes using jQuery
      // const checkedCheckboxes = $('input[type=checkbox]:checked').map(function() 
      // {
      //   return this.value;
      // }).get();


      const checkedCheckboxes = $('.stores:checked').map(function() 
      {
            return this.value;
      }).get();


      const booking_server = $('.booking_server:checked').map(function() 
      {
            return this.value;
      }).get();


      var apilon_booking =  booking_server.length;



      checked_errors.length   = 0;
      store_arr.length        = 0;
      checbox_type_arr.length = 0;
      

       

      for(var a=0;a<checkedCheckboxes.length;a++)
      {
          var txt_data = new FormData();
          // old original------------------------------------------------
          // var input = $('#'+checkedCheckboxes[a]+'_txt_file')[0];         
          // store_arr.push(checkedCheckboxes[a]);
          // checbox_type_arr.push('_txt_file');
          //end of original------------------------------------------------

          
         
          var input = $('#'+checkedCheckboxes[a]+'_txt_file')[0];
          if(input.files.length > 0)
          {
              store_arr.push(checkedCheckboxes[a]);
              checbox_type_arr.push('_txt_file');
          } 
         

           
         
          $.each(input.files, function(i, file)
          {
              txt_data.append('files[]', file);
          });   

          if(input.files.length == 0)
          {
             checked_errors.push(checkedCheckboxes[a]); //pag kuha sa mga  input nga  wala pa nag select ug file
             red_color(checkedCheckboxes[a]+'_txt_file'); //pag red sa mga wala pa nag select ug file
          }


<?php
          if($vendor_code != 'UPLOAD OLD SALES') 
          { ?>
              if(checkedCheckboxes[a] == 'asc' || checkedCheckboxes[a] == 'icm') // if icm ug asc siya dapat mu select ug vendor
              {

                   var input = $('#'+checkedCheckboxes[a]+'_vendor_txt_file')[0];
                   $.each(input.files, function(i, file)
                   {
                       txt_data.append('files[]', file);
                   });   

                   if(input.files.length == 0)
                   {
                       //checked_errors.push(checkedCheckboxes[a]); //pag kuha sa mga  input nga  wala pa nag select ug file
                       //red_color(checkedCheckboxes[a]+'_vendor_txt_file');//pag red sa mga wala pa nag select ug file
                   }
                   else 
                   {                   
                       store_arr.push(checkedCheckboxes[a]);
                       checbox_type_arr.push('_vendor_txt_file');
                   }                 
              }  
          



              var element = $('#'+checkedCheckboxes[a]+'_pend_po_txt_file');
              if (element.length > 0) 
              {
                  var input = element[0];
                  $.each(input.files, function(i, file) 
                  {
                      txt_data.append('files[]', file);
                  });

                  if(input.files.length == 0)
                  {

                  }
                  else 
                  {
                      store_arr.push(checkedCheckboxes[a]);
                      checbox_type_arr.push('_pend_po_txt_file');
                  }

              }
<?php     } ?>
          //console.log('pending po- '+element.length);

      }

      console.log(store_arr);

      if(checked_errors.length > 0)
      {
         Swal.fire({
                       icon: 'error',
                       title: '',
                       text: 'Please input file'                                  
                   });   
      }
      else 
      {
            var txt_data = new FormData();
            for (var a = 0; a < checkedCheckboxes.length; a++) 
            {


              
                var input = $('#' + checkedCheckboxes[a] + '_txt_file')[0];
                $.each(input.files, function (i, file) 
                {
                    txt_data.append('files[]', file);
                });


<?php          if($vendor_code != 'UPLOAD OLD SALES') 
               { ?>
                    if (checkedCheckboxes[a] == 'asc' || checkedCheckboxes[a] == 'icm') 
                    {
                        var vendorInput = $('#' + checkedCheckboxes[a] + '_vendor_txt_file')[0];
                        $.each(vendorInput.files, function (i, file) {
                            txt_data.append('files[]', file);
                        });
                    }


                    var element = $('#'+checkedCheckboxes[a]+'_pend_po_txt_file');
                    if (element.length > 0) 
                    {
                        var po_input = element[0];
                        $.each(po_input.files, function(i, file) 
                        {
                             txt_data.append('files[]', file);
                        });                    
                    }
<?php          } ?>

            }



            // Assuming you have the file data stored in the txt_data variable as a FormData object
            var file_list    = [];
            var file_content = [];
            for (const entry of txt_data.entries()) 
            {
                const fieldName = entry[0]; // Get the field name
                const file = entry[1]; // Get the file object

                // Access file properties
                const fileName = file.name; // File name
                const fileSize = file.size; // File size in bytes
                const fileType = file.type; // File type

                //console.log("Field Name:", fieldName);
                // console.log("File Name:", fileName);
                // console.log("File Size:", fileSize);
                // console.log("File Type:", fileType);
                 
                file_list.push(fileName);           

                // You can perform further operations with the file, such as reading its contents
                // For example, to read the file as text:
                const reader = new FileReader();
                reader.onload = function(event) {
                    const fileContent = event.target.result; // The file content
                    // console.log("File Content:", fileContent);
                    file_content.push(fileContent);
                };
                reader.readAsText(file); // Read the file as text
            }

           // console.log(file_list,file_content);



             $.ajax({
                                 type:'POST',
                                 // url: '<?php echo site_url('Mms_ctrl/extract_file_V2/')?>'+checkedCheckboxes[a]+ '?store_arr=' + JSON.stringify(store_arr),
                                 url: '<?php echo site_url('Mms_ctrl/extract_file_checking/')?>'+checkedCheckboxes[a]+ '?store_arr=' + JSON.stringify(store_arr)+'&checbox_type_arr='+ JSON.stringify(checbox_type_arr)+ '&v_code=' +$("#v_code").val()+'&d_tag='+$('#d_tag').val()+'&group_code='+$('#group_code').val(),
                                 data: txt_data,
                                 contentType: false,
                                 processData: false, 
                                 dataType:'JSON',     
                                 success: function(data)
                                 {
                                     swal.close();
                                     if(data.response == 'success')
                                     {                             
                                               loader_();


                                                      

                                               io.open('POST', '<?php echo base_url('Mms_ctrl/extract_file_V6'); ?>', { 
                                                                                                                               'apilon_booking':apilon_booking,
                                                                                                                               'v_code':$("#v_code").val(), 
                                                                                                                               'd_tag':$('#d_tag').val(),
                                                                                                                               'group_code':$('#group_code').val(),
                                                                                                                                store_arr:JSON.stringify(store_arr),
                                                                                                                                file_list:JSON.stringify(file_list),
                                                                                                                                file_content:JSON.stringify(file_content)

                                                                                                                      },'_self');       

                                               // progress_bar(apilon_booking,$("#v_code").val(),$('#d_tag').val(),$('#group_code').val(),store_arr,file_list,file_content) ;

                                             //load_table();
                                     }
                                     else 
                                     {
                                              $('#'+data.field).css('border-color', 'red');
                                               
                                               Swal.fire({
                                                  icon: 'error',
                                                  title: '',
                                                  text: data.response                                       
                                                });                                   
                                     }
                                 }
                            });


      }



}





/* Stephanie and Sir Gershom Code ---------------------------------------------------------------------------*/

  function generate_report()
  {
    $.ajax({
               type:'POST',
               url:'<?php echo base_url(); ?>Mms_ctrl/consolidate_report',
               dataType:'JSON',
               success: function(data)
               {
                    console.log(data);
                  //$("#table_here").html(data.html);
               }
            });

  }

/* End of the Code -----------------------------------------------------------------------------------------*/

  function loader_()
  {
      
      Swal.fire({
                    imageUrl: '<?php echo base_url(); ?>assets/img/Cube-1s-200px.svg',
                    imageHeight: 203,
                    imageAlt: 'loading',
                    text: 'Uploading, please wait',
                    allowOutsideClick:false,
                    showCancelButton: false,
                    showConfirmButton: false
                  })              
  } 

  //load_table();
  function load_table()
  {
     $.ajax({
               type:'POST',
               url:'<?php echo base_url(); ?>Mms_ctrl/load_reorder_list_table',
               dataType:'JSON',
               success: function(data)
               {
                  $("#table_here").html(data.html);
               }
            });
  }



 function revert_color(store)
 {
      $('#'+store).css('border-color', '');
 }

 function red_color(store)
 {
      $('#'+store).css('border-color', 'red');
 }



<?php 
     if($vendor_code == 'UPLOAD OLD SALES') 
     {
          echo 'load_archive_table();';
     }
?>


     function load_archive_table()
     {

          var loader  = ' <center><img src="<?php echo base_url(); ?>assets/img/preloader.gif" style="padding-top:120px; padding-bottom:120px;"></center>';
          $('#archive_table').html(loader);
          $.ajax({
                      type:'POST',
                      url:'<?php echo base_url(); ?>Mms_ctrl/archive_table',                      
                      dataType:'JSON',
                      success: function(data)
                      {
                          $("#archive_table").html(data.html);
                      }                           
                 });
     }



    </script>