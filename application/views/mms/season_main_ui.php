
<?php 
    $user_details = $this->Acct_mod->retrieveUserDetails();
?>

<style>
    .disabled {
        opacity: 0.5; /* Reduce the opacity to make it appear disabled */
        pointer-events: none; /* Prevent mouse events on the disabled div */
    }
    
</style>

<!-- modal -->
<div class="modal fade text-left" id="add_reorder_modal" tabindex="-1" role="dialog" aria-labelledby="modal"
                                    aria-hidden="true">
  <div id="add_reorder_dialog" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="add_reorder_title">SEASON REORDER</h4>
      </div>

        <div class="modal-body" id="add_reorder_body">
        
            <div class="row" style="margin-bottom: 10px;">
                <div class="col-sm-12">
                     <fieldset id="store_select_div" data-role="controlgroup" data-type="horizontal">
                     </fieldset>
                </div>
             </div>

             <div class="row" style="margin-bottom: 10px;">
                <div class="col-sm-5">
                    <select class="form-control" id="season_select" onchange="setMaxYear()">
                    </select>
                </div>
                <div class="col-sm-7">
                    <input class="form-control" type="text" id="vendor_text" autocomplete="off" placeholder="Search Vendor">
                    <div id="dropdown" class="dropdown-menu" style="display: none;"></div>
                </div>
             </div>

             <div class="row" style="margin-bottom: 15px;">   
                <div class="col-sm-12">
                    <fieldset id="year_select_div" data-role="controlgroup" data-type="horizontal">
                     </fieldset>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-4" style="font-weight: bold; text-align: center;">STORE</div>
                <div class="col-sm-4" style="font-weight: bold; text-align: center;">QTY ONHAND (HTML)</div>
                <div class="col-sm-4" style="font-weight: bold; text-align: center;" id="pqty_label"></div>
            </div>
            <hr>
            <div id="multiple_store_div"></div>

        </div>
        <div class="modal-footer">
            <iframe name="progress_frame" id="progress_frame" height="100%" width="100%" frameborder="0"></iframe>
            <span id="timer" style="font-weight: bold; margin-right: 10px; font-size: 20px;"></span>
            <button id="generate_btn" class="btn btn-primary"></button>
            <button id="close_reorder_modal_btn" type="button" class="btn btn-danger" data-dismiss="modal">
                <i class="bx bx-x d-block d-sm-none"></i>
                <span class="d-none d-sm-block ">Close</span>
            </button>
      </div>
    </div>
  </div>
</div>
<!-- end of modal -->

<!-- modal3 -->
<div class="modal fade text-left" id="status_modal" tabindex="-1" role="dialog" aria-labelledby="modal"
                                    aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">STATUS HISTORY (<span id="span_status_hist"></span>)</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <i data-feather="x"></i>
        </button>
      </div>
      
      <div class="modal-body">
            <table class="table">
                <thead>
                    <th>STATUS</th>
                    <th>DATE SET</th>
                    <th>APPROVED/DISAPPROVED BY</th>
                </thead>
                <tbody id="status_hist_tbody"></tbody>
            </table>                          
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
<!-- end of modal3 -->

<?php if($user_details["store_id"]==6 && $user_details["user_type"]=="buyer"){ // IF CDC Buyer ?>

<ul class="nav nav-tabs" style="margin-bottom: 30px;">
    <li id="cdc_link" class="active"><a href="#" onclick="setSelectLink('cdc_link')"><b>CDC Reorder</b></a></li>
    
    <li id="store_link"><a href="#" onclick="setSelectLink('store_link')"><b>STORE Reorder</b></a></li>
</ul>

<?php } ?>

 <div class="row">
     <div class="col-sm-10">
        <input type="radio" id="option1" name="options" value="pending" style="width: 20px; height: 20px;" checked> 
        <label for="option1">Pending</label>
        &nbsp;&nbsp;
        <input type="radio" id="option2" name="options" value="approved" style="width: 20px; height: 20px;"> 
        <label for="option2">Approved</label>
     </div>
     <div class="col-sm-2">

<?php if($user_details["user_type"]=="buyer"){ // IF CDC or Store Buyer ?>

        <button id="reorder_modal_btn" type="button" class="btn btn-primary" onclick="viewReorderModal()">
          <i class="fa fa-edit"></i> REORDER
        </button>

<?php } ?>

     </div>
 </div>

 <div class="row">
       <div class="col-12 table-responsive" style="padding-top: 20px;">
        <table id="seasonal-table" class="table table-striped table-bordered table-responsive" style="background-color: rgb(5, 68, 104);">
            <thead style="text-align: center;color:white;" id="seasonal-thead">
               <th>DOCUMENT NO.</th>
               <th>SEASON</th>
               <th>VENDOR CODE</th>
               <th>VENDOR NAME</th>
               <th>DATE GENERATED</th>
               <th>GROUP CODE</th>
               <th>STATUS</th>
               <th>
                ACTION&nbsp;&nbsp; 
                <input id="checkAll" type="checkbox" onchange="checkAll()" style="display: none;">
               </th>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>     
  </div>

<script>
    var max_year = 0;
    var vendor_list; 
    var selected_vendor = '';
    var seasonTable = $("#seasonal-table").DataTable( { columnDefs: [{  targets: -1, // The last column index (zero-based)
                                                                        orderable: false // Make the last column not sortable
                                                                    }] 
                                                    });

    $('#seasonal-table_filter').append(' <span id="generate_btn_span"></span>');

    $(function(){
        $("#vendor_text").on("input", function() {
            var vendor = $("#vendor_text").val();   
             
            $("#dropdown").html("");

            if(vendor.length>0){
                for(var c=0; c<vendor_list.length; c++){
                    if(vendor_list[c].toLowerCase().includes(vendor.toLowerCase())){

                        var option = $('<div>')
                          .addClass('dropdown-item')
                          .css('cursor', 'pointer')
                          .text(vendor_list[c])
                          .click((function(sel) {
                              return function() {
                                selected_vendor = sel;
                                $("#vendor_text").val(sel);
                                $("#vendor_text").attr("style","border-color: green;");
                                $("#dropdown").hide();
                                console.log(selected_vendor);
                              };
                            })(vendor_list[c]));

                        $("#dropdown").append(option);

                    }
                }

                $("#dropdown").show();

            }else{
                $("#dropdown").hide();
                $("#vendor_text").attr("style","");
                selected_vendor = "";
            
            }
                   
        });
    });

    $(function(){
        $(document).on('click', function(event){ // Remove Dropdown on click
            if (!$("#vendor_text").is(event.target) && !$("#vendor_text").has(event.target).length) {
                $("#dropdown").hide();
            }
        });
    });

    function loadParameters(){
        $("#generate_btn").html('<img src="<?php echo base_url(); ?>assets/loader/loading-gif.gif" width="20" heigth="20">');
        $("#season_select").prop("disabled",true);
        $("#generate_btn").prop("disabled",true);

        $.ajax({
          url: '<?php echo site_url('Po_ctrl/getParametersForSeasonSales')?>', 
          type: 'POST',
          success: function(response) {
            var jObj = JSON.parse(response);
            console.log(jObj);   

            // Stores
            var disp1 = 'inline';
            var disp2 = 'checked';
            
            if(jObj.store_list.length>1){
                disp1 = 'none';
                disp2 = '';
            }

            var html = '';
            var html_ = '';
            var html__ = '';

            var dept_grp = [];
            var dept_html = '';
            for(var c=0; c<jObj.store_list.length; c++){ // Radio Button
                if(!dept_grp.includes(jObj.store_list[c].group_)){
                    dept_grp.push(jObj.store_list[c].group_);
                    dept_html += '<span class="dept_grp"><input type="radio" name="dept_grp" value="'+jObj.store_list[c].group_+'" style="width: 20px; height: 20px;" onclick="viewDeptSelect(this)" '+disp2+'> <label>'+jObj.store_list[c].group_+
                        '</label></span>&nbsp;&nbsp;';
                
                }
            }

            html += dept_html+'&emsp;&emsp;';

            <?php if($user_details["store_id"]==6 && $user_details["user_type"]=="buyer"){ // IF CDC Buyer ?> // PHP CODE
                
                html += '<span><input type="checkbox" id="dist_chk" value="distribution" style="width: 20px; height: 20px;" '+
                    '> <label>DISTRIBUTION</label></span><br>';

            <?php } ?> // PHP CODE 

            for(var c=0; c<jObj.store_list.length; c++){ // Check Boxes
                
                html += '<span class="stores_span_ stores_span_'+jObj.store_list[c].group_+'" style="display: none;">'+
                    '<input type="checkbox" class="stores_chk_ stores_chk_'+jObj.store_list[c].group_+'" value="'+
                    jObj.store_list[c].store_no+
                    '" style="width: 20px; height: 20px;" onchange="toggleFile(this)" '+disp2+
                    '> <label>'+jObj.store_list[c].name+'</label></span>&emsp;&emsp;';  
                    
                html_ += '<div id="store_file_div_'+jObj.store_list[c].store_no+
                    '" class="row store_file_div__" style="margin-bottom: 10px; display: '+disp1+';"><div class="col-sm-4">';
                
                html_ += '<p style="padding: 4px; border: 1px solid gray; text-align: center; font-size: 1vw;">'+
                    jObj.store_list[c].name+'</p>';
                
                if(jObj.store_list[c].store_no=="PM-S0001" || jObj.store_list[c].store_no=="TAL-S0001"){
                    html__ = '<div class="col-sm-4"><input type="file" id="file_select_'+jObj.store_list[c].store_no+
                    '_1" class="form-control file_select__" onclick="clear_file_color(this)"></div>';
                    $("#pqty_label").html("PENDING QTY (TXT)");
                }else{
                    html__ = '';
                }

                html_ += '</div><div class="col-sm-4"><input type="file" id="file_select_'+jObj.store_list[c].store_no+
                    '" class="form-control file_select__" onclick="clear_file_color(this)"></div>'+html__+'</div>';
            }

        
            $("#store_select_div").html(html);
            $("#multiple_store_div").html(html_);

            if(dept_grp.length>1){  
                $(".dept_grp").show();
            }else{
                $(".dept_grp").hide();
            }

            
            // Seasons
            html = '<option value="" disabled selected>SEASON</option>';
            for(var c=0; c<jObj.season_list.length; c++){
                html += '<option value="'+jObj.season_list[c].type_id+'">'+jObj.season_list[c].season_name+'</option>';
            }

            $("#season_select").prop("disabled",false);
            $("#season_select").html(html);

            // Vendors
            vendor_list = [];
            for(var c=0; c<jObj.vendor_list.length; c++){
                vendor_list.push(jObj.vendor_list[c].no_+'-'+jObj.vendor_list[c].name_);
            }

            console.log(vendor_list);

            // Year
            var now = new Date();
            var currentYear = now.getFullYear();

            html = '';
            for(var c=(currentYear-1); c>=2019; c--){
                html += '<input type="checkbox" class="years_chk" value="'+c+'" style="width: 20px; height: 20px;" onchange="years_chk(this)"> <label>'+c+'</label>&emsp;&emsp;';
            }

            $("#year_select_div").html(html);

            $("#generate_btn").prop("disabled",false);
            $("#generate_btn").html("GENERATE");
              
          }

        });

    }

    loadParameters();

    function setMaxYear(){
        var type_id = $("#season_select").val();
        
        $.ajax({
          url: '<?php echo site_url('Po_ctrl/getSeasonDetails')?>', 
          type: 'POST',
          data: {type_id:type_id},
          success: function(response) {
            var jObj = JSON.parse(response);
            console.log(jObj.no_ref_year);
            max_year = jObj.no_ref_year;
            $('.years_chk').prop('checked',false);
          }
        });
    }

    function years_chk(elem){
        var checkedCount = $('.years_chk:checked').length;
        if(checkedCount>max_year)
            $(elem).prop("checked",false);

    }

    function viewDeptSelect(elem){
        var dept = $(elem).val();
        $(".stores_span_").hide();
        $(".store_file_div__").hide();
        $(".stores_span_"+dept).show();
        $('.stores_chk_'+dept+':checked').map(function() {
            var store = $(this).val();
            $("#store_file_div_"+store).show();
        });

    }

    function toggleFile(elem){ // checkbox
        var store = $(elem).val();
        if($(elem).prop("checked"))
            $("#store_file_div_"+store).show();
        else
            $("#store_file_div_"+store).hide();
    }

    loadTable();

    const timerElement = document.getElementById('timer');
    var minutes_ = 0;

    // Function to update the timer element
    function updateTimerDisplay(remainingTime) {
        // Calculate minutes and seconds
        const minutes = Math.floor(remainingTime / 60);
        const seconds = remainingTime % 60;

        // Format minutes and seconds with leading zeros
        const formattedMinutes = minutes.toString().padStart(2, '0');
        const formattedSeconds = seconds.toString().padStart(2, '0');

        // Update the timer element content
        timerElement.textContent = `${formattedMinutes}:${formattedSeconds}`;
    }


    $(function(){
        $('#generate_btn').on('click', function(){
            
            var is_dist = $('#dist_chk').prop('checked'); // Distribution

            var storeValues = [];
            var dept = $("input[name='dept_grp']:checked").val(); 
        
            $('.stores_chk_'+dept+':checked').map(function() {
                storeValues.push($(this).val());
            });
            
            console.log($('.stores_chk_').length);
            console.log(storeValues);

            var yearValues = [];
            $('.years_chk:checked').map(function() {
                yearValues.push($(this).val());
            });

            if($('.stores_chk_').length>1 && storeValues.length==0){
                Swal.fire({title: 'Message!', text: "Pls Select Store/s!", icon: "error"});
            }else if(selected_vendor==''){
                Swal.fire({title: 'Message!', text: "Pls Select Vendor!", icon: "error"});
            }else if(yearValues.length==0){
                Swal.fire({title: 'Message!', text: "Pls Select Year/s!", icon: "error"});
            }else{
                $("#generate_btn").html('<img src="<?php echo base_url(); ?>assets/loader/loading-gif.gif" width="20" heigth="20">');
                $('#generate_btn').prop("disabled",true);
                $('#close_reorder_modal_btn').prop("disabled",true);

                var season = $("#season_select").val();
                var vendor = $("#vendor_select").val();

                var formData = new FormData(); //create a FormData object

                for (var i = 0; i < storeValues.length; i++) {
                    
                    formData.append('file_select_'+storeValues[i], $('#file_select_'+storeValues[i])[0].files[0]);
                    
                    if(storeValues[i]=="PM-S0001" || storeValues[i]=="TAL-S0001"){
                        formData.append('file_select_'+storeValues[i]+'_1', $('#file_select_'+storeValues[i]+'_1')[0].files[0]);
                    }
                }
                    
                formData.append('is_dist',is_dist);    
                formData.append('stores',storeValues);
                formData.append('season',season);
                formData.append('vendor',selected_vendor);
                formData.append('years',yearValues);

                $.ajax({
                    url: '<?php echo site_url('Po_ctrl/seasonFormSubmit') ?>', 
                    type: 'POST',
                    data: formData,
                    processData: false, //do not process the data
                    contentType: false, //do not set content type
                    success: function(response) {
                        
                        try{

                            var jObj = JSON.parse(response);
                            console.log(jObj);

                            if(jObj[0]=="success"){
                                
                                // $('#close_reorder_modal_btn').prop("disabled",false);
                                // $("#generate_btn").prop("disabled",false);
                                // $("#generate_btn").html("GENERATE");
                                
                                const countdownInterval = setInterval(function() {
                                    updateTimerDisplay(minutes_);
                                    minutes_++;
                                }, 1000); // Update every 1 second

                                $("#add_reorder_body").addClass("disabled");
                                
                                var formFrame = {
                                                    store_id: jObj[1],
                                                    season_id: jObj[2],
                                                    years: JSON.stringify(jObj[3]),
                                                    vendor_code: jObj[4],
                                                    stores: JSON.stringify(jObj[5]),
                                                    file_res: JSON.stringify(jObj[6]),
                                                    list: JSON.stringify(jObj[7]),
                                                    is_dist: jObj[8]
                                };

                                js_submit(formFrame);

                                $("#progress_frame").on("load", function(){
                                    var iframeDocument = $(this).contents();
                                    var elementInsideIframe = iframeDocument.find("#progress-hidden");
                                    var reorder_result = elementInsideIframe.val().split("|");
                                    console.log(reorder_result);

                                    clearInterval(countdownInterval);
                                    minutes_ = 0;

                                    Swal.fire({ title: 'Message!',
                                                text: "Document: "+reorder_result[1]+" Created!",
                                                icon: 'success',
                                                allowOutsideClick: false,
                                                allowEscapeKey: false })
                                    .then((result) => {
                                      if (result.isConfirmed) {
                                         location.href = "<?php echo base_url('Mms_ctrl/mms_ui/7?id='); ?>"+reorder_result[0];
                                      }
                                    });
                                });
                                

                            }else{
                                Swal.fire({title: 'Message!', text: jObj[1], icon: jObj[0]});
                                $("#"+jObj[2]).attr("style","border-color:red;");
                                $('#close_reorder_modal_btn').prop("disabled",false);
                                $("#generate_btn").prop("disabled",false);
                                $("#generate_btn").html("GENERATE");
                            }

                        }catch(err){

                            Swal.fire({ title: 'Error!',
                                    text: "Error: "+err,
                                    icon: 'error',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false })
                            .then((result) => {
                                if (result.isConfirmed) {
                                    // location.reload();
                                }
                            });

                            $('#close_reorder_modal_btn').prop("disabled",false);
                            $("#generate_btn").prop("disabled",false);
                            $("#generate_btn").html("GENERATE");
                    
                        }
                        
                    },

                    error: function(xhr, status, error) {
                        
                        Swal.fire({ title: 'Error!',
                                    text: "Status: "+status+", Error: "+error,
                                    icon: 'error',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false })
                        .then((result) => {
                            if (result.isConfirmed) {
                                // location.reload();
                            }
                        });
                    }

                });
            }

        });

    });

    
    function js_submit(formFrame){
        
        var form = $('<form></form>', {
            action: '<?php echo base_url('Po_ctrl/seasonProgress'); ?>',
            method: 'POST',
            target: 'progress_frame'
        });

        // Append hidden input fields with the data
        $.each(formFrame, function(name, value) {
            $('<input>').attr({
                type: 'hidden',
                name: name,
                value: value
            }).appendTo(form);
        });

        $('body').append(form);
        form.submit();
        form.remove();
    }

    function clear_file_color(elem){
        $(elem).attr("style","");
    }
    
    // function resetForm(){
        // selected_vendor = "";
        // max_year = 0;
        // $('.progress').css('width', '100%');
        // $('.stores_chk').prop("checked",false);
        // $("#season_select").val("");
        // $("#vendor_text").val("");
        // $("#vendor_text").attr("style","");
        // $('.years_chk').prop("checked",false);
        // $('.store_file_div__').hide();
        // $('.file_select__').val("");
        // $("#add_reorder_modal").modal("hide");
        // loadTable();
    // }

    function viewReorderModal(){
        $('.progress').css('width', '0%');
        $("#add_reorder_modal").modal({backdrop: 'static',keyboard: false}); 
    }

    $(function(){
        $("input[name='options']").on('click', function(){
            loadTable();
            uncheckMain();
        });

    });

    function loadTable(){
        var selectedRadio = '';
        var selectedLink = getSelectLink(); // CDC or Store

        if($("input[name='options']").length) 
            selectedRadio = $("input[name='options']:checked").val();
        
        loader();
        $("#checkAll").hide();

        $.ajax({
            url: '<?php echo site_url('Po_ctrl/listSeasonReorders')?>', 
            type: 'POST',
            data: {opt:selectedRadio, link:selectedLink},
            success: function(response) {
                Swal.close();
                var jObj = JSON.parse(response);
                populateTable(jObj);
            }

          });
    }

    function populateTable(list){
        seasonTable.clear().draw();
        var selectedLink = getSelectLink();
        var selectedRadio = '';
        if($("input[name='options']").length) 
            selectedRadio = $("input[name='options']:checked").val();

        <?php if($user_details["store_id"]==6 && $user_details["user_type"]=="buyer"){ // IF CDC Buyer ?>

            if(selectedRadio=="approved" && list.length>0){
                var gen_btn = '<button id="generate_txt_btn" class="btn btn-dark btn-sm" onclick="generate_txt()">'+    
                            '<i class="fa fa-download"></i></button>';
                $("#generate_btn_span").html(gen_btn);
                $("#checkAll").show();
            }else{
                $("#generate_btn_span").html('');
            }

        <?php } ?>

        for(var c=0; c<list.length; c++){
            var batch_id = list[c].batch_id;
            var doc_no = '<span id="doc_span_'+batch_id+'">'+list[c].doc_no+'</span>';
            var season = list[c].season;
            var vendor_code = list[c].vendor_code;
            var vendor_name = list[c].vendor_name;
            var date_generated = list[c].date_generated;
            var group_code = list[c].group_code;
            var status = list[c].status;

            <?php if($user_details["store_id"]==6 && $user_details["user_type"]=="buyer"){ // IF CDC Buyer ?>

                if(selectedRadio=="approved"){

                    list[c].nav_si_doc = (list[c].nav_si_doc===null) ? "" : list[c].nav_si_doc;
                    list[c].nav_dr_doc = (list[c].nav_dr_doc===null) ? "" : list[c].nav_dr_doc;

                    doc_no += '<div style="text-align: right;">';

                    if(list[c].vend_type=="SI,DR"){
                        var read_si = (list[c].nav_si_doc==="") ? "" : " readonly";
                        var read_dr = (list[c].nav_dr_doc==="") ? "" : " readonly";
                        doc_no += '<b>SI</b> <input type="text" size="15" id="nav_si_'+batch_id+'" value="'+list[c].nav_si_doc+'"'+read_si+'>';
                        doc_no += '<br><b>DR</b> <input type="text" size="15" id="nav_dr_'+batch_id+'" value="'+list[c].nav_dr_doc+'"'+read_dr+'>';
                    }else if(list[c].vend_type=="SI"){
                        var read_si = (list[c].nav_si_doc==="") ? "" : " readonly";
                        doc_no += '<b>SI</b> <input type="text" size="15" id="nav_si_'+batch_id+'" value="'+list[c].nav_si_doc+'"'+read_si+'>';
                    }else{ // DR
                        var read_dr = (list[c].nav_dr_doc==="") ? "" : " readonly";
                        doc_no += '<b>DR</b> <input type="text" size="15" id="nav_dr_'+batch_id+'" value="'+list[c].nav_dr_doc+'"'+read_dr+'>';
                    }

                    doc_no += '</div>';
                }

            <?php } ?>

            var stat_style = '';
            
            if((status.includes("APPROVED") || status.includes("FORWARDED")) && !status.includes("DISAPPROVED"))
                stat_style = 'style="color:green; cursor: pointer;"';
            else if(status.includes("DISAPPROVED") || status.includes("CANCELLED"))
                stat_style = 'style="color:red; cursor: pointer;"';
            else
                stat_style = 'style="color:orange; cursor: pointer;"';

            var status_hist = '<a '+stat_style+' onclick="viewStatusHist('+batch_id+')">'+status+'</a>';
            var view_link = '<a class="btn btn-primary" href="<?php echo base_url('Mms_ctrl/mms_ui/7?id=');?>'+batch_id+'">'+
                            '<i class="fa fa-eye"></i></a>';

            <?php if($user_details["user_type"]=="incorporator"){ // IF Incorporator ?>

                view_link = '<a class="btn btn-primary" href="<?php echo base_url('Sales_ctrl/page/16?id=');?>'+batch_id+'">'+
                            '<i class="fa fa-eye"></i></a>';

            <?php }else if($user_details["store_id"]==6 && $user_details["user_type"]=="buyer"){ // IF CDC Buyer ?>
                
                if(selectedRadio=="approved"){
                    // if(selectedLink=="cdc"){
                    //     view_link += '&nbsp;&nbsp;<button class="btn btn-warning" onclick="viewManagersKey('+batch_id+',\''+group_code
                    //             +'\')"><i class="fa fa-envelope-open"></i></button>';
                    // }

                    view_link += '&nbsp;&nbsp;<input class="status_box" type="checkbox" onchange="checkSingle(this)" value="'+batch_id+'">';
                }
                 
            <?php }else if($user_details["store_id"]!=6 && $user_details["user_type"]=="buyer"){ // IF Store Buyer ?>

                // if(selectedRadio=="approved" && selectedLink=="store"){
                //     view_link += '&nbsp;&nbsp;<button class="btn btn-warning" onclick="viewManagersKey('+batch_id+',\''+group_code
                //             +'\')"><i class="fa fa-envelope-open"></i></button>';
                // }

            <?php } ?>  

            var rowNode = seasonTable.row.add([doc_no,season,vendor_code,vendor_name,date_generated,group_code,status_hist,view_link]).draw().node();  
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

    function setSelectLink(link){
        if(link=='cdc_link'){
            $('#cdc_link').attr("class","active");
            $('#store_link').attr("class","");
            $('#reorder_modal_btn').show();
        }else{ // store_link
            $('#cdc_link').attr("class","");
            $('#store_link').attr("class","active");
            $('#reorder_modal_btn').hide();
        }

        loadTable();
        uncheckMain();
    }

    function getSelectLink(){
        var link = 'store';

        if($("#cdc_link").length){
            if($('#cdc_link').attr("class")=="active"){
                link = "cdc";
            }
        }
        
        return link;
    } 
 
    function checkAll(){
        var checked = $('#checkAll').prop('checked');
        var numberOfColumns = seasonTable.columns().header().length;
        seasonTable.column(numberOfColumns-1).nodes().to$().find('.status_box').prop('checked', checked);
    }

    function checkSingle(elem){
        uncheckMain();
        // var checked = $(elem).prop('checked');
        // var val = $(elem).val();
        // $("#input_qty_"+val).prop("disabled",!checked);
    }

    function uncheckMain(){
        $('#checkAll').prop('checked',false);
    }

    // function viewManagersKey(batch_id,group_code){
    //     $("#key_modal").modal({backdrop: 'static',keyboard: false});
    //     $('#approve_key_btn').attr('onclick','confirmManagersKey('+batch_id+',\''+group_code+'\')');
    // }

    // function confirmManagersKey(batch_id,group_code){
    //     const username = $('#m_user').val();
    //     const password = $('#m_pass').val();

    //     $.ajax({
    //       url: '<?php echo site_url('Po_ctrl/mkey_approve')?>', 
    //       type: 'POST',
    //       data: { group_code: group_code, username: username, password: password }, 
    //       success: function(response) {
    //         var jObj = JSON.parse(response);
    //         console.log(jObj);
    //         if(jObj[0])
    //           reopenReorder(batch_id);
    //         else
    //           Swal.fire({title: 'Message!', text: "Incorrect Credentials!", icon: 'error'});
    //       }
        
    //     });
    // }

    function viewStatusHist(batch_id){
        var doc_no = $('#doc_span_'+batch_id).html();
        var html = '';

        $("#span_status_hist").html(doc_no)
        $("#status_modal").modal({backdrop: 'static',keyboard: false});

        $.ajax({
          url: '<?php echo site_url('Po_ctrl/getSeasonReorderStatusHistory')?>', 
          type: 'POST',
          data: {batch_id:batch_id},
          success: function(response) {
            var jObj = JSON.parse(response);
            console.log(jObj);
            
            for(var c=0; c<jObj.length; c++){
                html += '<tr><td>'+jObj[c].status+'</td><td>'+jObj[c].date_set+'</td><td>'+jObj[c].user+'</td></tr>';
            }
            
            $("#status_hist_tbody").html(html);
          }
        });
    }

    // function reopenReorder(batch_id){
    //     var doc_no = $('#doc_span_'+batch_id).html();

    //     Swal.fire({
    //       title: 'Confirmation',
    //       text: 'Are you sure you want to reopen Season Reorder ('+doc_no+')?',
    //       icon: 'warning',
    //       showCancelButton: true,
    //       confirmButtonText: 'Confirm',
    //       cancelButtonText: 'Cancel'
    //     }).then((result) => {
    //       if (result.isConfirmed) {
           
    //         $.ajax({
    //           url: '<?php echo site_url('Po_ctrl/setStatusSeasonReorder')?>', 
    //           type: 'POST',
    //           data: {batch_id:batch_id, status:"pending"},
    //           success: function(response) {
    //             var jObj = JSON.parse(response);
    //             console.log(jObj);
                
    //             if(jObj[0]=="success"){
    //                 loadTable();
    //                 $("#key_modal").modal('hide');
    //                 $('#m_user').val("");
    //                 $('#m_pass').val("");
    //             }
    //           }
    //         });

    //       }else{
    //         $('#m_user').val("");
    //         $('#m_pass').val("");
    //       } 

    //     });
    // }

    function generate_txt(){
        var checkedValues = []; // Stores arrays
        var stores = [];
        var navValues = []; // Stores arrays
        var numberOfColumns = seasonTable.columns().header().length;

        // Determine how many stores
        seasonTable.column(numberOfColumns-1).nodes().to$().find('.status_box:checked').each(function() {
            var batch_id = $(this).val();
            var doc_number = seasonTable.column(0).nodes().to$().find('#doc_span_'+batch_id).text();
            var store = doc_number.split("-")[1]; // ex. MMSS-ASC-0000007
            
            var nav_si = seasonTable.column(0).nodes().to$().find('#nav_si_'+batch_id);
            var nav_dr = seasonTable.column(0).nodes().to$().find('#nav_dr_'+batch_id);

            if(!stores.includes(store+"-SI") && nav_si.length>0){
                stores.push(store+"-SI");
                checkedValues.push([]);
            }

            if(!stores.includes(store+"-DR") && nav_dr.length>0){
                stores.push(store+"-DR");
                checkedValues.push([]);
            }

        });

        console.log(stores);

        seasonTable.column(numberOfColumns-1).nodes().to$().find('.status_box:checked').each(function() {
            var batch_id = $(this).val();
            var doc_number = seasonTable.column(0).nodes().to$().find('#doc_span_'+batch_id).text();
            var store = doc_number.split("-")[1]; // ex. MMSS-ASC-0000007

            var nav_val = [];
            var nav_si = seasonTable.column(0).nodes().to$().find('#nav_si_'+batch_id);
            var nav_dr = seasonTable.column(0).nodes().to$().find('#nav_dr_'+batch_id);
            
            var nav_si_doc = '';
            if(nav_si.length>0){
                nav_si_doc = nav_si.val();
                nav_val.push(nav_si_doc);
            }

            var nav_dr_doc = '';
            if(nav_dr.length>0){
                nav_dr_doc = nav_dr.val();
                nav_val.push(nav_dr_doc);
            }

            navValues.push(nav_val);

            for(var c=0; c<stores.length; c++){
                if(stores[c]==store+"-SI"){
                    if(nav_si_doc!==""){
                        checkedValues[c].push([batch_id,"SI",nav_si_doc]);
                        break;
                    }
                }
            }

            for(var c=0; c<stores.length; c++){
                if(stores[c]==store+"-DR"){                
                    if(nav_dr_doc!==""){
                        checkedValues[c].push([batch_id,"DR",nav_dr_doc]);
                        break;
                    }
                }
            }

        });

        console.log(checkedValues);

        if(checkedValues.length<1){
            Swal.fire({title: 'Message!', text: "No Reorder Report Selected!", icon: "error"});
        }else if(!checkEmptyInArray(navValues)){
            Swal.fire({title: 'Message!', text: "An SI/DR textfield is not inputted for a document!", icon: "error"});
        }else{

            for(var c=0; c<checkedValues.length; c++){
                (function (currentIndex) {
                    var doc_numbers = '';
                    for(var x=0; x<checkedValues[currentIndex].length; x++){
                        doc_numbers += seasonTable.column(0).nodes().to$().find('#doc_span_'+checkedValues[currentIndex][x]).text()+"_";
                    }
                    var filename = doc_numbers.slice(0, -1);

                    $.ajax({
                      url: '<?php echo site_url('Po_ctrl/generate_txt')?>', 
                      type: 'POST',
                      data: {batches:checkedValues[c]},
                      success: function(response) {
                        var blob      = new Blob([response], { type: 'text/plain' }); // 'application/vnd.ms-excel','text/plain'
                        var url       = URL.createObjectURL(blob);                                  
                        var link      = document.createElement('a');                                                       
                        link.href     = url;                                                        
                        link.download = filename+'.txt'; // xls,txt                                                     
                        document.body.appendChild(link);                                            
                        link.click();                          
                        document.body.removeChild(link);
                      }
                    });
                })(c);
            }
        }
            
    }

    function checkEmptyInArray(new_arr){
        if(new_arr.length<1)
            return false;

        for(var c=0; c<new_arr.length; c++){
            var in_arr = new_arr[c];
            for(var x=0; x<in_arr.length; x++){
                if(in_arr[x].trim()==="")
                    return false;
            }
        }

        return true;
    }

</script>    