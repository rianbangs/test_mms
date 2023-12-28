
<style>
    
/* upload div separator :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  @media (max-width: 700px) {
    /* Styles for extra small screens */
    .col-sm-3 {
      margin-top: 10px;
    }
    
    .form-control {
      margin-top: 10px;
    }
    
    .input-group-append {
      margin-top: 10px;
    }
  }
  
  @media (min-width: 700px) and (max-width: 900px) {
    /* Styles for small screens */
    .col-sm-3 {
      margin-top: 30px;
    }
    
    .form-control {
      margin-top: 5px;
    }
    
    .input-group-append {
      margin-top: 5px;
    }
  }
  
  .responsive-div {
      width: 100%; 
      max-width: 1600px; 
      height: 4px; 
      background-color: #0c6b99;
      position: relative; 
    }

    .line-separator {
      position: absolute;
      bottom: 0; 
      left: 0;
      width: 100%; 
      height: 0px;
      background-color: black; 
    }

    @media (max-width: 768px) {
      .responsive-div {
        width: 100%;
      }
    }

/* header saparator :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
    .responsive-div_top{
      width: 100%; 
      max-width: 1600px; 
      height: 2px; 
      background-color: #0c6b99;
      position: relative;
    }

    #main_div{
      height:810px;
    }


  .progress-bar-container {
    width: 100%;
    height: 10px;
    background-color: #ccc;
  }

  .progress-bar {
    height: 100%;
    background-color: #2196F3;
    width: 0;
  }

  .swal2-loader {
  display: none;
}

.swal2-actions {
  display: none;
}

.select2-selection.select2-selection--single {
  height: 32px;
  margin-top: -3px;
 font-size: 14px;
}

.select2-results__option{
  font-size: 14px;
}

.select2-search__field{
  font-size: 14px;
}

    </style>



 <div class="row">
 
<h3 style="margin-top: -21px;">Payments and Sales Uploading</h3>

<!-- ==================================================================================================================================================== -->
   
<div class="responsive-div_top" style="margin-top: -9px; margin-bottom: 10px;">
      <div class="line-separator"></div>
</div>

<!-- ==================================================================================================================================================== -->

    <div class="row">
      <div class="col-12 col-sm-6 col-lg-4">
        <label for="my-span" style="font-size: 14px;">Trans Payments</label>
        <div class="input-group">
          <input type="file" name="files[]" id="fileInput" class="form-control" style="width: 119%;">
          <div class="input-group-append">
            <button id="upload_payments" name="openTabButton" class="btn btn-primary">UPLOAD PAYMENTS</button>
          </div>
        </div>
      </div>
    </div>

    <div class="responsive-div_top" style="margin-top: 3px; margin-bottom: 10px;">
      <div class="line-separator"></div>
    </div>
    
    <div class="col-sm-2"style="margin-left: 9px; margin-top: -1px;">
      <select class="form-control option dynamic_select_payments" id="select_store_payments" onchange="load_payments_table();" style="width: 171px;margin-left: -24px;margin-top: 1px;"></select>
    </div> 

    <div class="col-sm-2" style="margin-left: -21px; margin-top: 4px;">
        <select class="form-control option" id="year_" onchange="load_payments_table();"></select>
    </div>

    <div class="row"> 
    <h3 style="margin-top: 0px; margin-left: 14px;">View Payments Uploaded</h3>
    <div class="col-12 col-sm-6 col-lg-4" style="width: 100%;">
         <table  class="table table-striped table-bordered table-responsive" id="payments_table" style="background-color: rgb(5, 68, 104); width: 100%;">
            <thead style="text-align: center;color:white;">
                <th>STORE</th>
                <th>DATE</th>
                <th>STORE NO</th>
                <th>TENDER TYPE</th>
                <th>CARD NO</th>
                <th>AMOUNT TENDERED</th>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
  </div>

<!-- ===================================================================================================================================================== -->

  <div class="responsive-div" style="margin-top: 10px; margin-bottom: 10px;">
         <div class="line-separator"></div>
  </div>


<!-- ===================================================================================================================================================== -->

  <div class="row">
    <div class="col-12 col-sm-6 col-lg-4">
      <label for="my-span" style="font-size: 14px;">Trans Sales</label>
      <div class="input-group">
        <input type="file" name="files[]" id="file_select_sales" class="form-control" style="width: 119%;">
        <div class="input-group-append">
          <button id="upload_sales" name="openTabButton" class="btn btn-primary">UPLOAD SALES</button>
          <!-- <button onclick="upload_sales();" name="openTabButton" class="btn btn-primary "  id="btn_sales" disabled>UPLOAD SALES</button> -->
        </div>
      </div>
    </div>
  </div>


  <div class="responsive-div_top" style="margin-top: 3px; margin-bottom: 10px;">
        <div class="line-separator"></div>
  </div>

<!-- ===================================================================================================================================================== -->
    
  <div class="col-sm-2"style="margin-left: 9px; margin-top: -1px;">
       <select class="form-control option dynamic_select_sales" id="select_store_sales" onchange="load_sales();"style="width: 171px;margin-left: -24px;margin-top: 1px;">
       </select>
  </div>    

  <div class="col-sm-2" style="margin-left: -21px; margin-top: 4px;">
        <select class="form-control option" id="year" onchange="load_sales();"></select>
  </div>


  <div class="row"> 
    <h3 style="margin-top: 0px;">View Sales Uploaded</h3>
    <div class="col-12 col-sm-6 col-lg-4" style="width: 100%;">
         <table  class="table table-striped table-bordered table-responsive" id="server_side" style="background-color: rgb(5, 68, 104); width: 100%;">
            <thead style="text-align: center;color:white;">
                <th>STORE</th>
                <th>ITEM NO</th>
                <th>DIVISION</th>
                <th>ITEM DEPT.</th>
                <th>ITEM GROUP</th>
                <th>DATE</th>
                <th>ITEM NO</th>
                <th>UOM</th>
                <th>QTY</th>
                <th>TOTAL ROUNDED AMT</th>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
  </div>

<!-- ===================================================================================================================================================== -->

  <div id="progressBarContainer" style="background: #134f84; height: 4px; margin-top: -55px;" hidden>
      <div id="progressBar" style="margin-top: 58px; "></div>
          <h3 style="margin-top: 7px; font-size: 17px;" id="header_"></h3> 
          <h3 style="margin-top: -15px;font-size: 15px;" id="data_proccess"></h3>  
          <h3 style="margin-top: -15px;font-size: 15px;" id="data_remaining_data"></h3>
      <div id="processingData" style="margin-top: -67px;margin-left: 161px;width: 911px;font-size: 14px;"></div>          
         <p id="progressText" style="font-size: 23px;"></p>
  </div>

 
        
</div> 
<script>


var dataTable_sales;
var dataTable_payments;

// function load payments table.............................................................
load_payments_table();
function load_payments_table()
{
   var year         = $("#year_").val();
   var select_store = $("#select_store_payments").val();

  // Destroy the previous DataTable instance.............................................................
  if (dataTable_payments) {
    dataTable_payments.destroy();
  }

  dataTable_payments = $('#payments_table').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": true,
        "ajax": {
                  "url": "<?php echo base_url(); ?>Sale_monitoring_ctrl/payments_table_view_service_side",
                  "type": "POST",
                  "data": function (d)
                         {
                          d.start        = d.start || 0; // Start index of the records
                          d.length       = d.length || 10; // Number of records per page
                          d.year         = year;
                          d.select_store = select_store;
                         }
                },

           columns: [
                     { data: 'store' },
                     { data: 'formatted_date' },
                     { data: 'Store_no' },
                     { data: 'Tender_type' },
                     { data: 'Card_no' },
                     { data: 'Amount_tendered',
                       render: $.fn.dataTable.render.number(',', '.', 2,'₱ ')
                     }
                    ],
         
          "paging": true,
          "pagingType": "full_numbers",
          "lengthMenu": [ [10, 25, 50, 1000], [10, 25, 50, "Max"] ],
          "pageLength": 10,
      });


  }

// function load sales table.............................................................
load_sales();
function load_sales()
{
   var year         = $("#year").val();
   var select_store = $("#select_store_sales").val();

 // Destroy the previous DataTable instance.............................................................
  if (dataTable_sales) {
    dataTable_sales.destroy();
  }

   dataTable_sales = $('#server_side').DataTable({
        "processing": true,
        "serverSide": true,
         "searching": true,
              "ajax": {
               "url": "<?php echo base_url(); ?>Sale_monitoring_ctrl/view_service_side",
              "type": "POST",
              "data": function (d) 
                      {
                        d.start        = d.start || 0; // Start index of the records.............................................................
                        d.length       = d.length || 10; // Number of records per page.............................................................
                        d.year         = year;
                        d.select_store = select_store;
                      }
                     },
           columns: [
                     { data: 'store' },
                     { data: 'item_no'},
                     { data: 'item_division'},
                     { data: 'item_department'},
                     { data: 'item_group'},
                     { data: 'formatted_date' },
                     { data: 'item_no' },
                     { data: 'unit_of_measure'},
                     { data: 'quantity' },
                     { data: 'total_rounded_amt',
                       render: $.fn.dataTable.render.number(',', '.', 2,'₱ ')
                     }
                    ],
         
          "paging": true,
          "pagingType": "full_numbers",
          "lengthMenu": [ [10, 25, 50, 1000], [10, 25, 50, "Max"] ],
          "pageLength": 10,
      });


  }

// function load select option table.............................................................
     function load_select_options(){
         
          $.ajax({
                  type:'post',
                  url:'<?php echo base_url(); ?>Sale_monitoring_ctrl/get_stores_and_years_filter',      
                  success: function(data)
                           {
                             var jObj = JSON.parse(data);
                             var day  = '';
                             var date = '';
                            // view store name.............................................................
                             var html = '<option value="">Select Store</option>';
                             for(var c=0; c<jObj[0].length; c++){
                              if(jObj[0][c].store!='')
                                html += '<option value="'+jObj[0][c].store+'">'+jObj[0][c].nav_store_val+'</option>';
                             }

                             $("#select_store_sales").html(html);
                             $("#select_store_payments").html(html);

                          }      

              });
        }

       load_select_options();
       
       $(document).ready(function() {
           $('#year_').select2(); // Apply Select2 to the select element with the 'dynamic_load' class
       });
       //function load dynamic year per store select..................................................................
       $(".dynamic_select_payments").on("change", function(event){
          var select_store_payments = $("#select_store_payments").val();

          $.ajax({
                  type:'post',
                  url:'<?php echo base_url(); ?>Sale_monitoring_ctrl/get_select_store_payments',   
                  data:{'select_store_payments':select_store_payments},
                  success: function(data)
                           {
                            // view year nav_conp.............................................................
                             var jObj = JSON.parse(data);
                             html = '<option value="">Select Year</option>';
                             for(var c=0; c<jObj[0].length; c++){
                              if(jObj[0][c].conp_date!='0')
                                html += '<option value="'+jObj[0][c].conp_date+'">'+jObj[0][c].month_payments+'</option>';
                             }
                             $("#year_").html(html);                                    
                          }      
              }); // end of ajax
       });
        
       //function dynamic load year per store sales..........................................................
       $(document).ready(function() {
          $('#year').select2(); // Apply Select2 to the select element with the 'dynamic_load' class
      });

      $(".dynamic_select_sales").on("change", function(event){
         var select_store_sales = $("#select_store_sales").val();

         $.ajax({
                  type:'post',
                  url:'<?php echo base_url(); ?>Sale_monitoring_ctrl/get_select_store_sales',   
                  data:{'select_store_sales':select_store_sales},
                  success: function(data)
                           {
                            // view year nav_cons.............................................................
                             var jObj = JSON.parse(data);
                             html = '<option value="">Select Year</option>';
                             for(var c=0; c<jObj[0].length; c++){
                              if(jObj[0][c].cons_date!='0')
                                var day  = (jObj[0][c].cons_date).split("-");
                                html += '<option value="'+jObj[0][c].cons_date+'">'+jObj[0][c].month_sales+'</option>';
                             }
                 
                             $("#year").html(html);                               
                          }      
              }); // end of ajax
      });
      
     

      document.getElementById("progressBarContainer").hidden  = true; 
      
      $("#file_select_sales").on('change', function(event){

        
        var file = $("#file_select_sales").val().split(".");

        console.log(file[1]);


        if(file[1] == 'cons')
        {
          document.getElementById("btn_sales").disabled  = false; 
        }else{
               document.getElementById("btn_sales").disabled  = true; 
             }
       });


         // function upload payments.............................................................
         $("#upload_payments").on("click", function(event)
         {
            var formData = new FormData(); 
            formData.append('file_select_p', $('#fileInput')[0].files[0]); 

            var sales = $("#file_select_sales").val().split("."); 
            var splitPath = sales[0].split("\\");
      

            var data_extension = $("#fileInput").val().split(".");
            var payment_path   = data_extension[0].split("\\");

            if(data_extension[1] == 'conp')
            {
                  if(splitPath[2] == payment_path[2])
                  {

                      loader_1();
                      $.ajax({
                              url: '<?php echo site_url('Sale_monitoring_ctrl/payments_upload')?>', 
                              type: 'POST',
                              data: formData, 
                              processData: false, 
                              contentType: false,
                              success: function(response){
                                                           Swal.close();
                                                         }
                            });
                  }else{
                        Swal.fire('Warning!', 'Please Select the same date sales upload.', 'info'); 
                       }
            }else{
                    Swal.fire('Warning!', 'Please Select File Correctly.', 'info');
                 }
         });
         

         // function upload sales.............................................................      
         $("#upload_sales").on("click", function(event)
         {          



            var lineCount = '';
            window.onbeforeunload = function()
             {
                //return "Are you sure you want to leave this page?";
             };

            var data_extension = $("#fileInput").val().split(".");
            var payment_path   = data_extension[0].split("\\");                                   

            var formData = new FormData(); 
            formData.append('file_select_s', $('#file_select_sales')[0].files[0]); 

            var sales_file = document.getElementById("file_select_sales");
            var file       = sales_file.files[0];
            var reader     = new FileReader();
            var fileName   = file.name;
            var substring  = fileName.substr(0, 8);
            
            var fileType   = '';
            if(substring == 'ASC-MIAS')
            {
                fileType = 'ASC-MIAS';

            }else{

                fileType = 'regular';
            }



            var sales = $("#file_select_sales").val().split("."); 
            var splitPath = sales[0].split("\\");  

            var substring = splitPath[2].substring(0, 8); // get the file name ASC-MIAS.............................................................


            //var store_name = ["ASC", "ASC-MIAS", "COL", "ICM", "MAN", "PM", "TAL"];

            var data_extension = $("#file_select_sales").val().split(".");
            
            
            if(data_extension[1] == 'cons')
            {    
                  if(splitPath[2] == payment_path[2])
                  {     
                            reader.onload = function (event) {


                              var fileContent = event.target.result;
                              var lines = fileContent.split("\n");


                              var arrayLength    = lines.length;
                             

                              var length_arr = [];
                                  for (var i = 20000; i <= 4000000; i += 20000) {
                                      length_arr.push(i);
                                  }

                              for(var a=0;a<length_arr.length;a++)
                                                   {
                                                      if(arrayLength < length_arr[a])
                                                      {
                                                          var number_of_loop = a+1;
                                                          break;
                                                      }
                                                      
                                                      if(a === length_arr-1)
                                                      {
                                                          var number_of_loop = a+1;                                                        
                                                      }
                                                   }   

                                var chunkSize = Math.ceil(arrayLength / number_of_loop); // Calculate the size of each chunk                     
                                var chunkedArray = []; // Initialize an empty array to store the chunks

                                for (var i = 0; i < arrayLength; i += chunkSize)
                                    {
                                       var chunk = lines.slice(i, i + chunkSize); // Get a chunk of the original array
                                       chunkedArray.push(chunk); // Push the chunk to the file_content
                                    }


                                    // Assume chunkedArray is an array of data chunks
                                      for (var a = 0; a < chunkedArray.length; a++) {
                                          // Create a container div for iframe, buttons, and drag functionality
                                          var frameContainer = document.createElement('div');
                                              frameContainer.style.position = 'absolute';
                                              frameContainer.style.borderRadius = '12px';
                                              frameContainer.style.borderColor = 'green';
                                              frameContainer.style.zIndex = a + 2;
                                              frameContainer.style.width = '80%';
                                              frameContainer.style.height = '150px';
                                              // frameContainer.style.left = '10px'; // Set left position
                                              frameContainer.style.right = '10px'; // Set left position
                                              frameContainer.style.top = (a * 210) + 'px'; // Set top position, adjust the spacing as needed

                                          // Create an iframe element
                                          var iframe = document.createElement('iframe');
                                          iframe.style.width = '94%';
                                          iframe.style.height = '100%';
                                          iframe.style.borderRadius = '12px';
                                          iframe.style.borderColor = 'green';
                                          iframe.style.marginTop = '121px';

                                          // Append the iframe to the container
                                          frameContainer.appendChild(iframe);

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

                                          // Create a form element
                                          var form = document.createElement('form');
                                          form.method = 'POST';
                                          form.action = '<?php echo base_url('Sale_monitoring_ctrl/sales_upload'); ?>';

                                          // // Append the input element to the form
                                          // form.appendChild(input);
                                          var fileContentInput = document.createElement('input');
                                          fileContentInput.type = 'hidden';
                                          fileContentInput.name = 'file_content';
                                          fileContentInput.value = JSON.stringify(chunkedArray[a]);

                                          // Create another input element for fileType
                                          var fileTypeInput = document.createElement('input');
                                          fileTypeInput.type = 'hidden';
                                          fileTypeInput.name = 'fileType';
                                          fileTypeInput.value = fileType;

                                          var fileName_ = document.createElement('input');
                                          fileName_.type = 'hidden';
                                          fileName_.name = 'file_name';
                                          fileName_.value = fileName;


                                          // Append both input elements to the form
                                          form.appendChild(fileContentInput);
                                          form.appendChild(fileTypeInput);
                                          form.appendChild(fileName_);

                                          // Append the form to the iframe
                                          iframe.contentDocument.body.appendChild(form);

                                          iframe.addEventListener('load', function () {
                                              // Remove the frameContainer after iframe completes its task
                                              document.body.removeChild(frameContainer);
                                            
                                          });

                                          // Submit the form
                                          form.submit();
                                      }
                          };

                          reader.readAsText(file);
                  }else{
                        Swal.fire('Warning!', 'Please Select the same date sales upload.', 'info'); 
                       }

            }else{
                  Swal.fire('Warning!', 'Please Select File Correctly.', 'info');
                 }

            //};
            //reader.readAsText(file); // Read the file as text

         });

           
          // function loader.............................................................   
          function loader_1()
          {
             Swal.fire({
                       imageUrl: '<?php echo base_url(); ?>assets/mms/images/Cube-1s-200px.svg',
                       imageHeight: 203,
                       imageAlt: 'loading',
                       text: 'loading, please wait',
                       allowOutsideClick:false,
                       showCancelButton: false,
                       showConfirmButton: false
                     });
          }

  
 



</script>