  

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

.button {
  background-color: #4CAF50; /* Green */
  border: none;
  color: white;
  padding: 0px 29px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 13px;
  margin:-5px 20px;
  transition-duration: 0.4s;
  cursor: pointer;
}

.button1 {
  background-color: white; 
  color: black; 
  border: 2px solid #157297;
}

.button1:hover {
  background-color: #157297;
  color: white;
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

.swal2-popup .progress-container {
  width: 100%;
  height: 20px;
  background-color: #f5f5f5;
  border-radius: 5px;
  overflow: hidden;
}

.swal2-popup .progress-bar {
  height: 100%;
  background-color: #4e73df;
}


</style>

 <div class="row">
 
<h3 style="margin-top: -21px;">UOM LIST</h3>

<!-- ==================================================================================================================================================== -->
   
<div class="responsive-div_top" style="margin-top: -9px; margin-bottom: 10px;">
      <div class="line-separator"></div>
</div>

<!-- ==================================================================================================================================================== -->

<div class="row">
    <div class="col-12 col-sm-6 col-lg-4">
        <label for="my-span" style="font-size: 14px;">UPLOAD UOM</label>
        <div class="input-group">
            <input type="file" name="files[]" id="fileInput" class="form-control" style="width: 119%;">
              <div class="input-group-append">
                 <button id="upload_oum" name="openTabButton" class="btn btn-primary">UPLOAD</button>
                 <!-- <button id="sample_" onclick="js_submit();" name="openTabButton" class="btn btn-primary">SAMPLE</button> -->
             </div>
       </div>
   </div>

   <div class="col-sm-2" style="margin-left: -51px; margin-top: 25px;">
    <select class="form-control option" id="select_dept" >
       <option value="">Select Dept</option>    
       <option value="HF">Home Fashion</option>
       <option value="FX">Fixrite</option>
       <option value="SM">Supermarket</option>
    </select>
  </div>
</div>

<!-- ==================================================================================================================================================== -->

<div class="responsive-div_top" style="margin-top: 3px; margin-bottom: 10px;">
  <div class="line-separator"></div>
</div>

<div class="col-sm-2"style="margin-left: 9px; margin-top: -1px;">
  <select class="form-control option dynamic_load"  id="select_store_" onchange="load_oum_table();" style="width: 171px;margin-left: -24px;margin-top: 1px;"></select>
</div> 

<div class="col-sm-2" style="margin-left: -21px; margin-top: 4px;">
    <select class="form-control option "  id="code" onchange="load_oum_table();"></select>
</div>

<!-- ==================================================================================================================================================== -->
<div class="row"> 
  <div class="col-12 col-sm-6 col-lg-4" style="width: 100%;">
       <table  class="table table-striped table-bordered table-responsive" id="uom_table" style="background-color: rgb(5, 68, 104); width: 100%;">
          <thead style="text-align: center;color:white;">
              <th>ID</th>
              <th>DEPT</th>
              <th>ITEM NO</th>
              <th style="width: 206px;">ITEM DESCRIPTION</th>
              <th>CODE</th>
              <th>QTY PER UOM</th>
              <th>LENGTH</th>
              <th>WIDTH</th>
              <th>HEIGHT</th>
              <th>CUBAGE</th>
              <th>WEIGHT</th>
              <!-- <th>PRIMARY KEY</th> -->
          </thead>
          <tbody>
              
          </tbody>
      </table>
  </div>
</div>

<script>


var dataTable_sales;
var dataTable_payments;

// function load payments table......................................................
load_oum_table();
function load_oum_table()
{
   var code         = $("#code").val();
   var select_store = $("#select_store_").val();

    
  // Destroy the previous DataTable instance
  if (dataTable_payments) {
    dataTable_payments.destroy();
  }

  dataTable_payments = $('#uom_table').DataTable({
        "processing": true,
        "serverSide": true,
         "searching": true,
          "ordering": true,
             "ajax" : {
                       "url" : "<?php echo base_url(); ?>Sale_monitoring_ctrl/uom_table_view_server_side",
                       "type": "POST",
                       "data": function (d)
                                {
                                 d.start        = d.start  || 0; // Start index of the records
                                 d.length       = d.length || 10; // Number of records per page
                                 d.code         = code;
                                 d.select_store = select_store;
                                }
                      },

          columns: [
                     { data: 'nav_UOM_header_id' },
                     { data: 'store' },
                     { data: 'Item_No' },
                     { data: 'description' },
                     { data: 'code', className: 'editable' },
                     { data: 'qty_per_unit_of_measure'},
                     { data: 'length' },
                     { data: 'width' },
                     { data: 'height' },
                     { data: 'cubage' },
                     { data: 'weight' }
                     //{ data: 'primary_key' }
                  ],
         
          "paging"    : true,
          "pagingType": "full_numbers",
          "lengthMenu": [ [10, 25, 50, 1000], [10, 25, 50, "Max"] ],
          "pageLength": 10, 
      });


  }


  // Enable editing on table cells ....................................
  $('#uom_table').on('click', 'tbody td.editable', function () {
    var cell = $(this);
    var input = $('<input type="text" style="width: 60px;">');
    input.val(cell.text());
    cell.html(input);
    input.focus();
   
    var originalValue = cell.text();
    input.on('blur', function () {
      var value  = $(this).val();
      var row    = dataTable_payments.row(cell.closest('tr'));
      var column = dataTable_payments.column(cell.index());

      row.data()[column.index()] = value;
      row.invalidate();
      row.draw();

      // Perform an AJAX request to update the server-side data with the new value ................................
      var rowData = row.data();
      updateUOMData(rowData);
    });

    input.on('keydown', function (e) {
      if (e.keyCode === 13) {
        $(this).blur();
      }
    });
  });

  // function update UOM.................................................
  function updateUOMData(rowData)
  {  
        console.log(rowData[4]);
      if(rowData['code'] == rowData['4'])
      {
       load_oum_table();
      }else{
              if(rowData['4'] != '')
                {
                  Swal.fire({
                            title: 'Confirmation',
                            html: '<div style="font-size: 16px;">Are you sure you want to update this data?</div><div style="font-size: 14px; color: red;">' + JSON.stringify(rowData['code'])+' '+'to' +' '+ JSON.stringify(rowData['4']) + '</div>',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes',
                            cancelButtonText: 'Cancel'
                           }).then((result) => {
                                                if (result.isConfirmed) 
                                                 {
                                                  $.ajax({
                                                    url: '<?php echo base_url(); ?>Sale_monitoring_ctrl/update_uom',
                                                    type: 'POST',
                                                    data: rowData,
                                                    success: function (response) {
                                                      load_oum_table();
                                                      Swal.fire('Success!', 'Successfully updated.', 'success');
                                                    },
                                                     error: function (xhr, status, error) {
                                                    }
                                                  });
                                                   
                                                 }
                                              });
                  }else{
                        Swal.fire('info!', 'Please input data.', 'warning');
                       }
         } // end of else
 } // end of updateUOMData





 // function load select option table.................................................
 function load_select_store()
 {

    $.ajax({
            type:'post',
            url:'<?php echo base_url(); ?>Sale_monitoring_ctrl/get_uom_table_filter',   
            success: function(data)
                     {
                       var jObj = JSON.parse(data);
                      // view store ...............................................
                       var html = '<option value="">Select Department</option>';
                       for(var c=0; c<jObj[0].length; c++){
                        if(jObj[0][c].store!='')
                          html += '<option value="'+jObj[0][c].store+'">'+jObj[0][c].store+'</option>';
                       }

                       $("#select_store_").html(html);                                             
                    }      
        }); // end of ajax

  } // end of load_select_options

 load_select_store();

$(document).ready(function() {
  $('#code').select2({
    width: '150px', // Customize the width of the dropdown
    dropdownCssClass: 'custom-dropdown', // Add a custom CSS class for further customization
    containerCssClass: 'custom-container' // Add a custom CSS class for further customization
  });
});

$(".dynamic_load").on("change", function(event) {
  var select_store = $("#select_store_").val();
  $.ajax({
    type: 'post',
    url: '<?php echo base_url(); ?>Sale_monitoring_ctrl/get_uom_table_filter_code',
    data: {
      'select_store': select_store
    },
    success: function(data) {
      var jObj = JSON.parse(data);
      // view code ...............................................
      var html = '<option value="">Select Code</option>';
      for (var c = 0; c < jObj[0].length; c++) {
        if (jObj[0][c].code != '')
          html += '<option value="' + jObj[0][c].code + '">' + jObj[0][c].code + '</option>';
      }
      $("#code").html(html);
    }
  });
});





//function upload payments............................................
$("#upload_oum").on("click", function(event)
{
     var select_dept = $("#select_dept").val();
     var fileInput   = document.getElementById("fileInput");
     var file        = fileInput.files[0];
   
     var file_       = fileInput.value;
     var files_      = file_.split("\\");
     var file_ex     = files_[2].split(".");
    
     var hfValue     = file_ex[0].substring(0, 2);
     
     if(hfValue.toLowerCase() === select_dept.toLowerCase())
     {
       //Create a FormData object and append the file .......................
       var formData = new FormData();
       formData.append('file', file);
       formData.append('select_dept', select_dept);
     
       loader_1();
       $.ajax({
                url: "<?php echo site_url('Sale_monitoring_ctrl/uom_upload')?>",
                type: "POST",
                data: formData, // Send the FormData object ........................
                processData: false,
                contentType: false,
                success: function(response) 
                         {
                          Swal.close();
                          console.log("Data inserted successfully");
                         } 
            }); // end of ajax

     }else{
           Swal.fire('Warning!', 'Please Select File Correctly.', 'info');
          }
     

 
});

// function loader..................................................................     
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



 function js_submit() {

  var fileInput = $("#fileInput")[0];
  var file = fileInput.files[0];
  

  // Check if a file has been selected
  if (!file) {
    console.log("No file selected");
    return;
  }
  
  form.attr('action', '<?php echo base_url("Super_ctrl/page/7")?>');
  form.attr('method', 'POST');
  form.attr('enctype', 'multipart/form-data');
  form.attr('target', '_blank');
  

  form.append($('<input>', {
    type: 'file',
    name: 'sample',
    files: [file]
  }));

  $('body').append(form);
  form.submit();
}


</script> 