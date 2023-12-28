<!-- modal1 -->
<div class="modal fade text-left" id="upload_modal" tabindex="-1" role="dialog" aria-labelledby="modal"
                                    aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="upload_title">UPLOAD FORM</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <i data-feather="x"></i>
        </button>
      </div>
      <div class="modal-body" id="upload_body">
        
        <div class="col-12 table-responsive" style="padding-top: 20px;">
            <table id="upload-table" class="table table-striped table-bordered table-responsive" style="background-color: rgb(5, 68, 104);">
                <thead style="text-align: center;color:white;">
                    <th>GROUP CODE</th>
                    <th>VENDOR CODE</th>
                    <th>VENDOR NAME</th>
                    <th>FREQUENCY</th>
                    <th>START DATE</th>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>          

      </div>
      <div class="modal-footer">
        <button id="upload_po_btn1" type="button" class="btn btn-primary" onclick="uploadToCalendar()">
          UPLOAD
        </button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">
          <i class="bx bx-x d-block d-sm-none"></i>
          <span class="d-none d-sm-block ">Close</span>
        </button>
      </div>
    </div>
  </div>
</div>
<!-- end of modal1 -->

<!-- modal2 -->
<div class="modal fade text-left" id="po_date_modal" tabindex="-1" role="dialog" aria-labelledby="modal"
                                    aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="po_date_title">REMARKS</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <i data-feather="x"></i>
        </button>
      </div>
      <div class="modal-body" id="po_date_body">
        
        <div class="col-12">
            <div id="p_msg"></div>
            <table id="po_date-table" class="table table-striped table-bordered table-responsive" style="background-color: rgb(5, 68, 104);">
                <thead style="text-align: center;color:white;">
                    <th>GROUP CODE</th>
                    <th>FREQUENCY</th>
                    <th>START DATE</th>
                    <th>END DATE</th>
                </thead>
                <tbody></tbody>
            </table>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">
          <i class="bx bx-x d-block d-sm-none"></i>
          <span class="d-none d-sm-block ">Close</span>
        </button>
      </div>
    </div>
  </div>
</div>
<!-- end of modal2 -->


<div class="row">
  	<div class="col-sm-5"></div>
    <div class="col-sm-1">
        <select id="group_select" class="btn btn-primary">
            <?php 
                $grp_codes = $this->Po_mod->getUserGroupCodes();
                foreach($grp_codes as $gc) {
                    echo '<option value="'.$gc.'">'.$gc.'</option>';
                }
            ?>
        </select>    
    </div>

    <div class="col-sm-3">
        <input type="file" id="file_select" class="btn btn-default">
    </div>

    <div class="col-sm-1" style="margin-left: 30px;">
        <button id="upload_po_btn" class="btn btn-primary">UPLOAD</button>
    </div> 
    
        
</div>
 
   

  <div class="row">
       <div class="col-12 table-responsive" style="padding-top: 20px;">
        <table id="vendor-table" class="table table-striped table-bordered table-responsive" style="background-color: rgb(5, 68, 104);">
            <thead style="text-align: center;color:white;">
                <th>VENDOR CODE</th>
                <th>VENDOR NAME</th>
                <th>FREQUENCY</th>
                <th>CATEGORY</th> 
                <th>APPROVER</th> 
                <th>TYPE</th> 
                <th>REMARKS</th>
            </thead>
            <tbody>
            	
            </tbody>
        </table>
    </div>     
  </div>

<script>
    const uploadTable = $("#upload-table").DataTable({ "ordering": false});
    const vendorTable = $("#vendor-table").DataTable();
    const poDateTable = $("#po_date-table").DataTable({ "ordering": false});
    var upload_json;
    var selected_po_id = 0;

    var approve_html = '<span style="margin-left: 15px;"><input type="radio" id="option1" name="approve_rbtn" value="Category-Head" style="width: 15px; height: 15px;"> <label for="option1">Category-Head</label>&nbsp;&nbsp;<input type="radio" id="option2" name="approve_rbtn" value="Corp-Manager" style="width: 15px; height: 15px;"> <label for="option2">Corp-Manager</label></span>';
    $("#po_date-table_length").append(approve_html);


    loadTable();

    function loadTable(){
        loader();
        $.ajax({
            url: '<?php echo site_url('Po_ctrl/listVendors')?>', 
            type: 'POST',
            success: function(response) {
                Swal.close();
                var jObj = JSON.parse(response);
                populateTable(jObj);
            }

          });
    }

    function populateTable(list){
        vendorTable.clear().draw();
        
        for(var c=0; c<list.length; c++){
            var vendor_code = list[c].no_;
            var vendor_name = list[c].name_;
            var frequency= list[c].frequency;
            var group_code = list[c].group_code;
            var vtype = list[c].vend_type;
            var approver = list[c].approver;
            var po_date_btn = '<button class="btn btn-primary" onclick="viewPoDates(\''+list[c].id_+'\',\''+vendor_code+'\',\''+vendor_name.replace(/'/g, "\\'")+'\',\''+approver+'\')"><i class="fa fa-eye"></i></button>';

            var rowNode = vendorTable.row.add([vendor_code,vendor_name,frequency,group_code,approver,vtype,po_date_btn]).draw().node();

            // $(rowNode).find('td').css({'color': 'black', 'font-family': 'sans-serif','text-align': 'center'});  
        }
          
    }

    function viewPoDates(po_id,vendor_code,vendor_name,approver){
        selected_po_id = po_id;

        $("#option1").prop("checked",true);
        if(approver=="Corp-Manager")
            $("#option2").prop("checked",true);

        $.ajax({
          url: '<?php echo site_url('Po_ctrl/listPoDates')?>', 
          type: 'POST',
          data: {po_id:po_id}, 
          success: function(response) {
            var jObj = JSON.parse(response);
            console.log(jObj);
            $("#p_msg").html("<p>"+vendor_code+" "+vendor_name+"</p>");
            populatePoDateTable(jObj);
            $("#po_date_modal").modal({backdrop: 'static',keyboard: false});
            
          }
        });

    }

    function populatePoDateTable(list){
        poDateTable.clear().draw();
        
        for(var c=0; c<list.length; c++){
            var group_code = list[c].group_code;
            var frequency = list[c].frequency;    
            var start_date = list[c].start_date;
            var end_date = list[c].end_date;

            var rowNode = poDateTable.row.add([group_code,frequency,start_date,end_date]).draw().node();

            // $(rowNode).find('td').css({'color': 'black', 'font-family': 'sans-serif','text-align': 'center'});  
        }
          
    }

    function loader(){
              
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

    $(function() {
        $('#upload_po_btn').click(function() {
          var loader = '<img src="<?php echo base_url(); ?>assets/loader/loading-gif.gif" width="20" heigth="20">';
          $("#upload_po_btn").html(loader);
          $("#upload_po_btn").prop("disabled",true);

          var formData = new FormData(); //create a FormData object
          formData.append('file_select', $('#file_select')[0].files[0]); //add the file to the FormData object
          formData.append('group_code',$('#group_select').val());

          $.ajax({
            url: '<?php echo site_url('Po_ctrl/breakLines')?>', 
            type: 'POST',
            data: formData, 
            processData: false, //do not process the data
            contentType: false, //do not set content type
            success: function(response) {
                
                var jObj = JSON.parse(response);
                upload_json = jObj[1];
                //upload_json[0].start_date = "2023-06-20";
                console.log(upload_json);
                
                $("#upload_po_btn").html("UPLOAD");
                $("#upload_po_btn").prop("disabled",false);

                if(jObj[0]=="error"){
                    Swal.fire({title: 'Message!', text: jObj[1], icon: jObj[0]});
                }else{ // Success
                    populateUploadTable(upload_json);
                    $("#upload_modal").modal({backdrop: 'static',keyboard: false});  
                } 

                // if(jObj[0]=="success" || jObj[0]=="error"){
                //     $("#upload_title").html('');
                //     $("#upload_body").html('<h1 align="center">'+jObj[1]+'</h1>');
                    
                //     if(jObj[0]=="success"){
                //       loadTable();
                //       $('#file_select').val(""); // Clear Selected File
                //     }

                // }else{
                //     $("#upload_title").html('UPLOAD FORM');
                //     $("#upload_body").html(jObj[1]);
                //     $("#upload_po_btn1").show();
                // }
             
            }
            
          });
        });
    });

    function populateUploadTable(list){
        uploadTable.clear().draw();
        
        for(var c=0; c<list.length; c++){
            var group_code = list[c].group_code;
            var vendor_code = list[c].no_;
            var vendor_name = list[c].name_;
            var frequency= list[c].frequency;
            var start_date = '<input class="start_date_class" type="date" id="start_date__'+c+'">';

            var rowNode = uploadTable.row.add([group_code,vendor_code,vendor_name,frequency,start_date]).draw().node();

            // $(rowNode).find('td').css({'color': 'black', 'font-family': 'sans-serif','text-align': 'center'});  
        }
          
    }

    
  // function uploadToCalendar(){
  //   var formData = $('#upload_po_form').serialize();
    
  //   $('#upload_po_btn1').html('<img src="<?php echo base_url(); ?>assets/loader/loading-gif.gif" width="20" heigth="20">');
  //   $('#upload_po_btn1').prop('disabled', true);
    
  //   $.ajax({
  //     url: '<?php echo site_url('Po_ctrl/uploadCalendar')?>', 
  //     type: 'POST',
  //     data: formData, 
  //     success: function(response) {
  //       var jObj = JSON.parse(response);
  //       console.log(jObj);
        
  //       var msg = "<p>"+jObj[0]+"</p>";
  //       for(var c=0; c<jObj[1].length; c++){
  //         msg+= "<p>"+jObj[1][c]+"</p>";
  //       }

  //       Swal.fire({title: 'Message!', html: msg, icon: 'info'});
  //       $('#upload_po_btn1').html('UPLOAD'); // Revert Button Text
  //       $('#upload_po_btn1').prop('disabled', false); // Revert Button Enabled
        
  //       // loadTable();
  //       // $('#file_select').val(""); // Clear Selected File  
        
        
  //     }
  //   });

  // }

    function uploadToCalendar(){
        var numberOfColumns = uploadTable.columns().header().length;

        uploadTable.column(numberOfColumns-1).nodes().to$().find('.start_date_class').map(function() {
            var sd_index = $(this).attr("id").split("__")[1]; // Get start_date index
            var sd_value = $(this).val(); // Get start_date value
            upload_json[sd_index].start_date = sd_value;

        });

        // console.log(upload_json); // Global Var

        $('#upload_po_btn1').html('<img src="<?php echo base_url(); ?>assets/loader/loading-gif.gif" width="20" heigth="20">');
        $('#upload_po_btn1').prop('disabled', true);
        
        $.ajax({
          url: '<?php echo site_url('Po_ctrl/uploadCalendar')?>', 
          type: 'POST',
          data: { upload_json: JSON.stringify(upload_json) }, 
          success: function(response) {
            
            var jObj = JSON.parse(response);
            console.log(jObj);
            
            if(jObj[0]=="error"){
                Swal.fire({title: 'Message!', text: jObj[1], icon: jObj[0]});
            }else{ // Success
                
                var msg = "<p>"+jObj[0]+" entries of Data Uploaded!</p>";
                for(var c=0; c<jObj[1].length; c++){
                  msg+= "<p>"+jObj[1][c]+"</p>";
                }

                Swal.fire({title: 'Message!', html: msg, icon: 'info', 
                    preConfirm: function() {
                                if(jObj[0]>0){
                                    $("#upload_modal").modal('hide');  
                                    loadTable();
                                    $('#file_select').val(""); // Clear Selected File 
                                }
                }});
               
            }

            $('#upload_po_btn1').html('UPLOAD'); // Revert Button Text
            $('#upload_po_btn1').prop('disabled', false); // Revert Button Enabled
            
          }
        });

    }

    $(function() {
        $("input[name='approve_rbtn']").on('click', function(){
            
            var approver = $("input[name='approve_rbtn']:checked").val();

            $.ajax({
              url: '<?php echo site_url('Po_ctrl/setVendorApprover')?>', 
              type: 'POST',
              data: {po_id:selected_po_id, approver: approver}, 
              success: function(response) {
                loadTable(); 
              }
            });
            
        });
    });



</script>