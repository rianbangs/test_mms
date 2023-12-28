<?php
    $user_details = $this->Acct_mod->retrieveUserDetails();

    if(!isset($_GET["id"])) 
        redirect(base_url('Mms_ctrl/mms_ui/6'));
    else{ // Check if a reorder report belongs to a specific user
        
        $count = 0;

        if($user_details["store_id"]!=6 && $user_details["user_type"]=="buyer") // If Store Buyer
           $count = $this->Po_mod->countSeasonReorderBatchByUser($_GET["id"]);
        else if($user_details["user_type"]=="category-head") // If Category Head
           $count = $this->Po_mod->countSeasonReorderBatchByCategoryHead($user_details["store_id"],$_GET["id"]);
        else if($user_details["store_id"]==6 && $user_details["user_type"]=="buyer") // If CDC Buyer
           $count = $this->Po_mod->countSeasonReorderBatchByCdcBuyer($_GET["id"]);
        else if($user_details["user_type"]=="corp-manager")
            $count = $this->Po_mod->countSeasonReorderBatchByCorpManager($_GET["id"]);
        
        if($count==0)
            redirect(base_url('Mms_ctrl/mms_ui/6'));
    }
?>

 <style type="text/css">
    
    th{
        text-align: center;
    }

    .modal-full {
        width: 80%;
        max-width: 80%;
    }

 </style>

<!-- modal1 -->
<div class="modal fade text-left" id="reason_modal" tabindex="-1" role="dialog" aria-labelledby="modal"
                                    aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="reason_title">Select Reason of Adjustment</h4>
      </div>

        <div class="modal-body" id="reason_body">
        

        </div>
        <div class="modal-footer">
        <button id="save_qty_btn" class="btn btn-success"><i class="fa fa-film"></i> Save</button>
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
<div class="modal fade text-left" id="hist_modal" tabindex="-1" role="dialog" aria-labelledby="modal"
                                    aria-hidden="true">
  <div class="modal-dialog modal-full" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="hist_title">Quantity Adjustment Log <span id="qtyAdj_span"></span></h4>
      </div>

        <div class="modal-body" id="hist_body">
        
        <div class="col-12 table-responsive" style="padding-top: 20px;">
            <table id="qty_adj-table" class="table table-striped table-bordered table-responsive" style="background-color: rgb(5, 68, 104);">
                <thead style="color:white;">
                    <tr>
                        <th>ADJUSTED QTY - SI</th>
                        <th>ORIGINAL QTY - SI</th>
                        <th>ADJUSTED QTY - DR</th>
                        <th>ORIGINAL QTY - DR</th>
                        <th>REASONS</th>
                        <th>DATE INPUTTED</th>
                        <th>INPUTTED BY</th>
                        <th>STATUS</th>
                        <th>APPROVED/DISAPPROVED BY</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
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

<!-- modal3 -->
<div class="modal fade text-left" id="stores_modal" tabindex="-1" role="dialog" aria-labelledby="modal"
                                    aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Other Stores</h4>
      </div>

        <div class="modal-body">
        
        <div class="col-12 table-responsive" style="padding-top: 20px; overflow-y: auto;">
            <table id="stores-table" class="table table-striped table-bordered table-responsive" style="background-color: rgb(5, 68, 104); width: 100%;">
                <thead style="color:white;">
                    <tr>
                    <th rowspan="2">ITEM NO.</th>
                    <th rowspan="2">DESCRIPTION</th>
                    <th rowspan="2">UOM</th>
                    <th id="store_ref_column" colspan="4">REFERENCE YEAR SALES (QTY)</th>
                    <th rowspan="2">FORECASTED QTY</th>
                    <th rowspan="2">QTY ONHAND</th>
                </tr><tr id="store_year_sales_column">
                    
                </tr>
                </thead>
                <tbody>
                    
                </tbody>
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
<!-- end of modal3 -->

<!-- modal4 -->
<div class="modal fade text-left" id="pqty_modal" tabindex="-1" role="dialog" aria-labelledby="modal"
                                    aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="hist_title">Pending PO <span id="pqty_span"></span></h4>
      </div>

        <div class="modal-body" id="hist_body">
        
        <div class="col-12 table-responsive" style="padding-top: 20px;">
            <table id="pqty-table" class="table table-striped table-bordered table-responsive" style="background-color: rgb(5, 68, 104);">
                <thead style="color:white;">
                    <tr>
                        <th>STORE</th>
                        <th>DOCUMENT NO.</th>
                        <th>PO DATE</th>
                        <th>UOM</th>
                        <th>PENDING QTY</th>
                        <th>EXPECTED DEL. DATE</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
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
<!-- end of modal4 -->

<div class="row" style="padding-top: 15px;">

    <div class="col-sm-2">
        <a class="btn btn-primary" href="
        <?php 
            // if($user_details["user_type"]=="incorporator") 
            //     echo base_url('Sales_ctrl/page/15');
            // else
                echo base_url('Mms_ctrl/mms_ui/6');
        ?>
        ">
            <i class="fa fa-bars"></i>
            Reorder Report
        </a>
        <br><br>
        <button id="approve_btn" class="btn btn-success" onclick="set_status_reorder_batch(true)" style="display: none;">
            <i class="fa fa-check"></i><!-- Approve -->
        </button>
        <button id="disapprove_btn" class="btn btn-danger" onclick="set_status_reorder_batch(false)" style="display: none;">
            <i class="fa fa-remove"></i><!-- Disapprove -->
        </button>
        <button id="forward_btn" class="btn btn-dark" onclick="forward_reorder_batch()" style="display: none;">
            <i class="fa fa-mail-forward"></i>
        </button>
        <button id="generate_txt_btn" class="btn btn-dark" onclick="generate_txt()" style="display: none;">    
            <i class="fa fa-download"></i>
        </button>
        <button id="generate_excel_btn" class="btn btn-success" onclick="generate_excel()" style="display: none;">    
            <i class="fa fa-download"></i>
        </button>
        <a id="generate_pdf_link" class="btn btn-danger" href="<?php echo base_url('Po_ctrl/generate_pdf/'.$_GET["id"])?>" style="display: none;" target="_blank">
            <i class="fa fa-download"></i>
        </a>
    </div>
    <div class="col-sm-2">  
        <strong>
                <p>Season<br></p>
                <br>
                <p>Supplier Code</p>
                <p>Supplier Name</p>
                
        </strong>
    </div>
    <div class="col-sm-3">        
        <p id="season_p"></p>
        <br>
        <p id="s_code_p"></p>
        <p id="s_name_p"></p>
        
    </div>
    <div class="col-sm-2">
        <strong>    
            <p>Document No.</p>
            <br>
            <p>Date Generated</p>
            <p>Status</p>
        </strong>    
    </div>
    <div class="col-sm-3">      
        <p id="r_no_p"></p>
        <br>
        <p id="date_p"></p>
        <p id="stat_p"></p>
    </div>   
</div>

<div class="row" style="padding-top: 5px;">
    <span id="nav_si_span" style="display: none;"><b>SI</b> <input type="text" size="15" id="nav_si_tf"></span>    
    <span id="nav_dr_span" style="display: none; margin-left: 10px;"><b>DR</b> <input type="text" size="15" id="nav_dr_tf"> </span>   
</div>

<hr>

 <div class="row">
       <div class="col-12 table-responsive" style="padding-top: 20px; overflow-y: auto;">
        <table id="seasonal-table" class="table table-striped table-bordered table-responsive" style="width: 100%; background-color: rgb(5, 68, 104);">
            <thead style="color:white;" id="seasonal-thead">
                <tr>
                    <th rowspan="2">ITEM NO.</th>
                    <th rowspan="2">DESCRIPTION</th>
                    <th rowspan="2">UOM</th>
                    <th id="ref_column" colspan="4">REFERENCE YEAR SALES (QTY)</th>
                    <th rowspan="2">FORECASTED QTY</th>
                    <th rowspan="2">REMAINING FORECASTED QTY</th>
                    <th rowspan="2">QTY ONHAND</th>
                    <th rowspan="2">PENDING QTY</th>
                    <th rowspan="2">SUGGESTED REORDER QTY</th>
                    <th id="act_column" rowspan="2" style="display: none;">
                        <center>
                            <input id="checkAll" type="checkbox" onchange="checkAll()">
                        </center>
                    </th>
                </tr><tr id="year_sales_column">
                    
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>     
  </div> 

  <script>
    const dtLength = [10, 25, 50, 100, 200, 300];
    
    var seasonTable;
    var storesTable;
    var qtyAdjTable = $("#qty_adj-table").DataTable({ "ordering": false});
    var pqtyTable = $("#pqty-table").DataTable();
    var batch_id = "<?php echo $_GET["id"];?>";
    var checkedElements = [];
    var is_append = false; // Prevents the buttons beside the search field(DataTable) from appending the second time.
    var is_append1 = false; // Prevents the buttons beside the search field(DataTable) from appending the second time.
    var vend_type = "";

    $(function(){
        $("#qty_adj-table_length").append('<span style="margin-left: 10px">Legend: <span style="font-weight:bold; color: blue;">REORDERED</span></span>');
    });

    function loadTable(){
        loader();
        $.ajax({
          url: '<?php echo site_url('Po_ctrl/listSeasonReorderEntries')?>', 
          type: 'POST',
          data: {batch_id:batch_id},
          success: function(response) {
            Swal.close();
            var jObj = JSON.parse(response);
            console.log(jObj);
            
            var batch_details = jObj[0];
            $("#season_p").html(batch_details.season);
            $("#s_code_p").html(batch_details.vendor_code);
            $("#s_name_p").html(batch_details.vendor_name);
            $("#r_no_p").html(batch_details.doc_no);
            $("#date_p").html(batch_details.date_generated);
            $("#stat_p").html(batch_details.status.toUpperCase());
            
            vend_type = batch_details.vend_type;

            if((batch_details.status.includes("approved") || batch_details.status.includes("forwarded")) 
                && !batch_details.status.includes("disapproved"))
                $("#stat_p").attr("style","color:green;");
            else if(batch_details.status.includes("disapproved") || batch_details.status.includes("cancelled"))
                $("#stat_p").attr("style","color:red;");
            else
                $("#stat_p").attr("style","color:orange;");

            var headers = jObj[1].ym; // year month
            var head_keys = jObj[2].ym; // year month
            if(batch_details.store_id==6){
                headers = jObj[1].compressed;
                head_keys = jObj[2].compressed; 
            }

            $("#ref_column").attr("colspan",headers.length);
            
            var head_html = '';
            for(var c=0; c<headers.length; c++){
                head_html += '<th>'+headers[c]+'</th>';
            }
            $("#year_sales_column").html(head_html);


            if(!$.fn.DataTable.isDataTable("#seasonal-table")) // Check if dataTable is initialized
                seasonTable = $("#seasonal-table").DataTable(
                    {   lengthMenu: dtLength,
                        columnDefs: [{  targets: -1, // The last column index (zero-based)
                                        orderable: false // Make the last column not sortable
                                    }] 
                    });

            if(batch_details.store_id=="6"){ // CDC Buyer
                $("#ref_column").html('TOTAL FORECAST (QTY) <a onclick="viewStores()"><i class="fa fa-eye"></i><a>');
                headers = jObj[1].ym; // year month
                head_html = '';
                for(var c=0; c<headers.length; c++){
                    head_html += '<th>'+headers[c]+'</th>';
                }

                $("#store_ref_column").attr("colspan",headers.length);
                $("#store_year_sales_column").html(head_html);
                if(!$.fn.DataTable.isDataTable("#stores-table")) // Check if dataTable is initialized
                    storesTable = $("#stores-table").DataTable({ lengthMenu: dtLength });

                if(!is_append1){
                    var stores_val = jObj[1].store;
                    var stores_id = jObj[2].store;
                    var store_html = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select id="store_forecast_sel" onchange="viewStoreForecast()">';
                    
                    for(var c=0; c<stores_val.length; c++){
                        store_html += '<option value="'+stores_id[c]+'">'+stores_val[c]+'</option>';
                    }
                    store_html += '</select>';
                    store_html += '&nbsp;&nbsp;<span id="store_span_loader"></span>';

                    $("#stores-table_length").append(store_html);

                    var pdf_html = ' <a id="store_pdf_link" class="btn btn-danger" target="_blank"><i class="fa fa-download"></i></a>';
                    $("#stores-table_filter").append(pdf_html);

                    is_append1 = true;
                }
            }

            var numberOfColumns = seasonTable.columns().header().length;
            var searchWrapper = $('#seasonal-table_filter');
            var buttonsHtml = ' <button type="button" class="btn btn-success btn-sm" onclick="save_checked_sug_qty()"><i class="fa fa-film"></i> Save</button>';

            <?php if($user_details["store_id"]!=6 && $user_details["user_type"]=="buyer"){ // Store Buyer ?>// PHP CODE

                if(batch_details.status=="pending"){
                    if(!is_append){
                        searchWrapper.append(buttonsHtml);
                        is_append = true; 
                    }
                    
                    seasonTable.column(numberOfColumns-1).visible(true);
                    $("#act_column").show();
                    $("#approve_btn").show();
                    $("#disapprove_btn").show();
                
                }else{
                    seasonTable.column(numberOfColumns-1).visible(false);
                    $("#act_column").hide();
                    $("#approve_btn").hide();
                    $("#disapprove_btn").hide();
                } 
            
            <?php }else if($user_details["user_type"]=="category-head"){ // Category-Head ?>// PHP CODE

                if(batch_details.status=="approved-by-buyer"){
                    if(!is_append){
                        buttonsHtml = ' <button type="button" class="btn btn-success btn-sm" onclick="save_checked_sug_qty()"><i class="fa fa-film"></i> Save</button> <button type="button" id="approve_adj_btn" class="btn btn-success btn-sm" onclick="set_status_reorder_adj(true)"><i class="fa fa-check"></i><!-- Approve --></button> <button type="button" id="disapprove_adj_btn" class="btn btn-danger btn-sm" onclick="set_status_reorder_adj(false)"><i class="fa fa-remove"></i><!-- Disapprove --></button>';
            
                        searchWrapper.append(buttonsHtml);
                        is_append = true; 
                    }

                    seasonTable.column(numberOfColumns-1).visible(true);
                    $("#act_column").show();
                    $("#approve_btn").show();
                    $("#disapprove_btn").show();
                }else{
                    seasonTable.column(numberOfColumns-1).visible(false);
                    $("#act_column").hide();
                    $("#approve_btn").hide();
                    $("#disapprove_btn").hide();
                }

            <?php }else if($user_details["store_id"]==6 && $user_details["user_type"]=="buyer"){ // CDC Buyer ?>// PHP CODE
            
                if(batch_details.store_id!=6){ // Store Reorders
                    if(batch_details.status=="approved-by-category"){
                        if(!is_append){
                            searchWrapper.append(buttonsHtml);
                            is_append = true; 
                        }
                        
                        seasonTable.column(numberOfColumns-1).visible(true);
                        $("#act_column").show();
                        $("#approve_btn").show();
                        $("#disapprove_btn").show();
                        $("#generate_txt_btn").hide();
                        
                    }else if(batch_details.status=="approved-by-corp-buyer"){
                        seasonTable.column(numberOfColumns-1).visible(false);
                        $("#act_column").hide();
                        $("#approve_btn").hide();
                        $("#disapprove_btn").hide();
                        $("#generate_txt_btn").show();
                        
                        batch_details.nav_si_doc = (batch_details.nav_si_doc===null) ? "":batch_details.nav_si_doc;
                        batch_details.nav_dr_doc = (batch_details.nav_dr_doc===null) ? "":batch_details.nav_dr_doc;

                        if(vend_type=="SI,DR"){
                            $("#nav_si_span").show();
                            $("#nav_si_tf").val(batch_details.nav_si_doc);
                            $("#nav_dr_span").show();
                            $("#nav_dr_tf").val(batch_details.nav_dr_doc);
                        }else if(vend_type=="SI"){
                            $("#nav_si_span").show();
                            $("#nav_si_tf").val(batch_details.nav_si_doc);
                        }else{ // DR
                            $("#nav_dr_span").show();
                            $("#nav_dr_tf").val(batch_details.nav_dr_doc);
                        }

                        $("#nav_si_tf").prop("readonly",(batch_details.nav_si_doc!==""));
                        $("#nav_dr_tf").prop("readonly",(batch_details.nav_dr_doc!==""));

                    }else{
                        seasonTable.column(numberOfColumns-1).visible(false);
                        $("#act_column").hide();
                        $("#approve_btn").hide();
                        $("#disapprove_btn").hide();
                        $("#generate_txt_btn").hide();
                    }

                    $("#generate_pdf_link").show();

                }else{ // CDC Reorders

                    $("#generate_excel_btn").show();
                    $("#generate_pdf_link").show();

                    if(batch_details.status=="pending"){
                        if(!is_append){
                            searchWrapper.append(buttonsHtml);
                            is_append = true; 
                        }
                        
                        seasonTable.column(numberOfColumns-1).visible(true);
                        $("#act_column").show();
                        $("#approve_btn").show();
                        $("#disapprove_btn").show();
                        $("#generate_txt_btn").hide();
                    }else if(batch_details.is_finalized=='yes' && (batch_details.status=="approved-by-corp-manager" ||
                            batch_details.status=="approved-by-category")){
                        seasonTable.column(numberOfColumns-1).visible(false);
                        $("#act_column").hide();
                        $("#approve_btn").hide();
                        $("#disapprove_btn").hide();
                        $("#generate_txt_btn").show();
                        
                        batch_details.nav_si_doc = (batch_details.nav_si_doc===null) ? "":batch_details.nav_si_doc;
                        batch_details.nav_dr_doc = (batch_details.nav_dr_doc===null) ? "":batch_details.nav_dr_doc;

                        if(vend_type=="SI,DR"){
                            $("#nav_si_span").show();
                            $("#nav_si_tf").val(batch_details.nav_si_doc);
                            $("#nav_dr_span").show();
                            $("#nav_dr_tf").val(batch_details.nav_dr_doc);
                        }else if(vend_type=="SI"){
                            $("#nav_si_span").show();
                            $("#nav_si_tf").val(batch_details.nav_si_doc);
                        }else{ // DR
                            $("#nav_dr_span").show();
                            $("#nav_dr_tf").val(batch_details.nav_dr_doc);
                        }

                        $("#nav_si_tf").prop("readonly",(batch_details.nav_si_doc!==""));
                        $("#nav_dr_tf").prop("readonly",(batch_details.nav_dr_doc!==""));

                    }else if(batch_details.status=="cancelled"){
                        seasonTable.column(numberOfColumns-1).visible(false);
                        $("#act_column").hide();
                        $("#approve_btn").hide();
                        $("#disapprove_btn").hide();
                        $("#generate_txt_btn").hide();
                        $("#generate_excel_btn").hide();
                        $("#generate_pdf_link").hide();
                    }else{
                        seasonTable.column(numberOfColumns-1).visible(false);
                        $("#act_column").hide();
                        $("#approve_btn").hide();
                        $("#disapprove_btn").hide();
                        $("#generate_txt_btn").hide();
                    } 

                    
                }

            <?php }else if($user_details["user_type"]=="corp-manager"){ // Corp-Manager ?>// PHP CODE

                if(batch_details.status=="approved-by-category"){
                    if(!is_append){
                        searchWrapper.append(buttonsHtml);
                        is_append = true; 
                    }
                    
                    seasonTable.column(numberOfColumns-1).visible(true);
                    $("#act_column").show();
                    $("#approve_btn").show();
                    $("#disapprove_btn").show();
                    // $("#forward_btn").show();
                    $("#generate_txt_btn").hide();
                    
                }else{
                    seasonTable.column(numberOfColumns-1).visible(false);
                    $("#act_column").hide();
                    $("#approve_btn").hide();
                    $("#disapprove_btn").hide();
                    // $("#forward_btn").hide();
                    $("#generate_txt_btn").hide();
                }

            <?php }else if($user_details["user_type"]=="incorporator"){ // Incorporator ?>// PHP CODE
                
                if(batch_details.status=="forwarded-to-incorp"){
                    if(!is_append){
                        searchWrapper.append(buttonsHtml);
                        is_append = true; 
                    }
                    
                    seasonTable.column(numberOfColumns-1).visible(true);
                    $("#act_column").show();
                    $("#approve_btn").show();
                    $("#disapprove_btn").show();
                    
                }else{
                    seasonTable.column(numberOfColumns-1).visible(false);
                    $("#act_column").hide();
                    $("#approve_btn").hide();
                    $("#disapprove_btn").hide();
                    $("#forward_btn").hide();
                    $("#generate_txt_btn").hide();
                }

            <?php } ?>// PHP CODE
            
            populateTable(head_keys,jObj[3]);
          
            }
                
        });
    }

    loadTable();
    
    function populateTable(headers,list){
        
        seasonTable.clear().draw();
        
        for(var c=0; c<list.length; c++){
            var entry_id = list[c].entry_id;
            var item_no = list[c].item_no;
            var item_desc = list[c].item_desc;
            var uom = list[c].uom;
            var qty_onhand = list[c].qty_onhand;
            var sale_amts = list[c].sale_amts;
            var fqty = list[c].forecast_qty; // Forecasted Qty
            var rem = list[c].remaining; // Remaining Qty
            var pqty = list[c].pending_qty; // Pending Qty
            var rqty = list[c].reorder_qty; // Reorder Qty
            var rqty_dr = list[c].reorder_qty_dr; // Reorder Qty
            var ovs = list[c].overstock;
            var status = list[c].status; 
            var new_row = [item_no,item_desc,uom];

            for(var x=0; x<headers.length; x++){
                var cell = 0;
                if (sale_amts.hasOwnProperty(headers[x])) {
                    cell = sale_amts[headers[x]];
                }

                new_row.push(cell);
            }
 
            var check_box = '<center><input class="status_box" type="checkbox" onchange="checkSingle(this)" value="'+entry_id+'"></center>';
            var stat_style = '';
            if(status=="pending"){
                stat_style = 'style="color: orange; cursor: pointer;"';
            }else if(status=="approved"){
                stat_style = 'style="color: green; cursor: pointer;"';
                check_box = '';
                
                <?php if(($user_details["store_id"]==6 && $user_details["user_type"]=="buyer")
                        || ($user_details["user_type"]=="corp-manager") 
                        || ($user_details["user_type"]=="incorporator")){ // CDC Buyer, Corp-Manager, Incorporator ?>// PHP CODE
                    
                    check_box = '<center><input class="status_box" type="checkbox" onchange="checkSingle(this)" value="'+entry_id+'"></center>';

                <?php } ?>// PHP CODE

            }else if(status=="disapproved"){
                stat_style = 'style="color: red; cursor: pointer;"';
            }

            var rem_span = '<span id="rem_'+entry_id+'">'+rem+'</span><input id="hid_rem_'+entry_id+'" type="hidden" value="'+entry_id+'">';

            var pqty_link = '<a style="color: blue; cursor: pointer;" onclick="viewPoDetails('+entry_id+')">'+pqty+'</a>';
            
            var ovs_status = (ovs<0) ? '<br><span style="color: red;">OVERSTOCK: '+Math.abs(ovs)+'</span>':'';
            
            var input_si = '<input type="hidden" id="input_sug_'+entry_id+'" value="'+rqty+'"><b>SI</b> <input class="input_qty" type="text" oninput="regex2(this)" id="input_qty_'+entry_id+'" value="'+rqty+'" disabled style="width: 50px;">';

            var input_dr = '<input type="hidden" id="input_sug_dr_'+entry_id+'" value="'+rqty_dr+'"><b>DR</b> <input class="input_qty_dr" type="text" oninput="regex2(this)" id="input_qty_dr_'+entry_id+'" value="'+rqty_dr+'" disabled style="width: 50px;">';
            
            var input_qty = '<center><div style="display: inline-block; text-align: right;">';

            var show_qty = '';
            if(vend_type=="SI,DR"){
                input_qty += input_si+'<br>'+input_dr;
                show_qty = rqty+'|'+rqty_dr;
            }else if(vend_type=="SI"){
                input_qty += input_si;
                show_qty = rqty;
            }else{ // DR
                input_qty += input_dr;
                show_qty = rqty_dr;
            }

            var show_status = (status=="pending") ? status.toUpperCase()+': '+show_qty : status.toUpperCase();
            var link_status = (status!='') ? '<br><a class="link_status" id="link_'+entry_id+'" '+stat_style+' onclick="viewAdj('+entry_id+')">'+show_status+'</a>':'';


                input_qty += '</div>'+link_status+ovs_status+'</center>';
            
            new_row.push(fqty);
            new_row.push(rem_span);
            new_row.push(qty_onhand);
            new_row.push(pqty_link);
            new_row.push(input_qty);
            new_row.push(check_box);
            // console.log(new_row);

            var rowNode = seasonTable.row.add(new_row).draw().node();
            alignDigitsInTable(seasonTable,["FORECASTED QTY","REMAINING FORECASTED QTY","QTY ONHAND","PENDING QTY"],"_");
        }
          
    }

    function alignDigitsInTable(table,columns_,contains_){
        var headers = table.columns().header(); 
        table.rows().every(function () {
            var rowCells = this.cells(); 
            rowCells.every(function () {
                var cell = this.node(); 
                var columnIndex = this.index().column; 
                var columnName = headers[columnIndex].innerText.trim();
                if(columns_.indexOf(columnName)!==-1 || columnName.indexOf(contains_)!==-1)
                    cell.style.textAlign = 'right'; 
            });
        });
    }
    
    function save_checked_sug_qty(){
        var numberOfColumns = seasonTable.columns().header().length;
        checkedElements = [];

        seasonTable.column(numberOfColumns-1).nodes().to$().find('.status_box:checked').map(function() {
            var entry_id = $(this).val(); // Get entry_id from status_box
            var node_to = seasonTable.column(numberOfColumns-2).nodes().to$();
            var input_qty = node_to.find("#input_qty_"+entry_id);
            var input_sug = node_to.find("#input_sug_"+entry_id); // Original Qty
            var input_qty_dr = node_to.find("#input_qty_dr_"+entry_id);
            var input_sug_dr = node_to.find("#input_sug_dr_"+entry_id); // Original Qty

            var val_set = [entry_id];
            if(input_qty.length>0)
                val_set.push(input_qty.val());

            if(input_sug.length>0)
                val_set.push(input_sug.val());

            if(input_qty_dr.length>0)
                val_set.push(input_qty_dr.val());
            
            if(input_sug_dr.length>0)
                val_set.push(input_sug_dr.val());
            
            checkedElements.push(val_set);
            //checkedElements.push([entry_id,qty,sug]);

        });

        if(checkedElements.length<1){
             Swal.fire({title: 'Message!', text: "No Selected Items for Adjustment!", icon: "error"});
        }else{
            $("#reason_body").html('<img src="<?php echo base_url(); ?>assets/mms/images/Cube-1s-200px.svg">');
            console.log(checkedElements); 

            $.ajax({
              url: '<?php echo site_url('Po_ctrl/setUpReasonAdj')?>', 
              type: 'POST',
              success: function(response) {
                var jObj = JSON.parse(response);
                console.log(jObj);
                
                var html = '';
                for(var c=0; c<jObj.length; c++){
                    html += '<input class="reason_chk" type="checkbox" value="'+jObj[c].reason_id+'"> <label style="margin-right:20px;">'+jObj[c].reason+'</label>';
                }

                $("#reason_body").html(html);
                $("#reason_modal").modal({backdrop: 'static',keyboard: false}); 
              
              }
            });
               
        } 
        
    }

    function checkAll(){
        var checked = $('#checkAll').prop('checked');
        var numberOfColumns = seasonTable.columns().header().length;
        seasonTable.column(numberOfColumns-1).nodes().to$().find('.status_box').prop('checked', checked);
        seasonTable.column(numberOfColumns-1).nodes().to$().find('.status_box').map(function() {
            var entry_id = $(this).val(); // Get entry_id from status_box
            var input_si = seasonTable.column(numberOfColumns-2).nodes().to$().find("#input_qty_"+entry_id);
            var input_dr = seasonTable.column(numberOfColumns-2).nodes().to$().find("#input_qty_dr_"+entry_id);
            
            if(input_si.length>0)
                input_si.prop('disabled', !checked);

            if(input_dr.length>0)
                input_dr.prop('disabled', !checked);

            console.log("yes");
            
        });
    }

    function checkSingle(elem){
        uncheckMain();
        var checked = $(elem).prop('checked');
        var val = $(elem).val();
        var input_si = $("#input_qty_"+val);
        var input_dr = $("#input_qty_dr_"+val);

        if(input_si.length>0)
            input_si.prop('disabled', !checked);

        if(input_dr.length>0)
            input_dr.prop('disabled', !checked);

    }

    function uncheckMain(){
        $('#checkAll').prop('checked',false);
    }

    function regex1(elem){ // Makes Text Field only accept Letters and Spaces
        $(elem).val($(elem).val().replace(/[^a-zA-Z\s]/g, ''));
    }

    function regex2(elem) { // Makes Text Field only accept Numbers
        $(elem).val($(elem).val().replace(/[^0-9]/g, ''));
    }

    $(function(){
        $("#save_qty_btn").click(function(){
            var reason_list = $(".reason_chk:checked").map(function() {
                                  return this.value;
                                }).get();

            if(reason_list.length<1){
                Swal.fire({title: 'Message!', text: "No Selected Reasons for Adjustment!", icon: "error"});
            }else{

                $("#save_qty_btn").html('<img src="<?php echo base_url(); ?>assets/loader/loading-gif.gif" width="20" heigth="20">');
                $("#save_qty_btn").prop("disabled",true);

                $.ajax({
                  url: '<?php echo site_url('Po_ctrl/saveQtyAdj')?>', 
                  type: 'POST',
                  data: {vend_type:vend_type, qtys:checkedElements, reasons:reason_list},
                  success: function(response) {
                    var jObj = JSON.parse(response);
                    console.log(jObj);
                    
                    Swal.fire({title: 'Message!', text: jObj[1], icon: jObj[0]});
                    
                    if(jObj[0]=="success"){
                        $('#checkAll').prop('checked',false);
                        loadTable();
                    }

                    $("#save_qty_btn").html('SAVE');
                    $("#save_qty_btn").prop("disabled",false);
                    $("#reason_modal").modal("hide"); 
                  
                  }
                });
            }
               
        });
    });

    function viewAdj(entry_id){
        $("#hist_modal").modal({backdrop: 'static',keyboard: false});

        $.ajax({
              url: '<?php echo site_url('Po_ctrl/listQtyAdjLog')?>', 
              type: 'POST',
              data: {entry_id:entry_id},
              success: function(response) {
                var jObj = JSON.parse(response);
                console.log(jObj);
                var headers = jObj[0];
                $("#qtyAdj_span").html("("+jObj[0].item_no+": "+jObj[0].item_desc+")");
                populateQtyAdjTable(jObj[1]);
                
              
              }
            }); 
    }

    function populateQtyAdjTable(list){

        qtyAdjTable.clear().draw();
    
        for(var c=0; c<list.length; c++){
            var adj_qty = list[c].adj_qty;
            var orig_qty = list[c].orig_qty;
            var adj_qty_dr = list[c].adj_qty_dr;
            var orig_qty_dr = list[c].orig_qty_dr;
            var reasons = list[c].reasons;
            var date_inputted = list[c].date_inputted;
            var inputted_by = list[c].inputted_by;
            var status = list[c].status;
            var approved_by = list[c].approved_by;
            var is_reorder = list[c].is_reorder; // yes or no

            var rowNode =  qtyAdjTable.row.add([adj_qty,orig_qty,adj_qty_dr,orig_qty_dr,reasons,date_inputted,inputted_by,status,approved_by]).draw().node(); 

            if(is_reorder=='yes')
                $(rowNode).find('td').css({'color': 'blue', 'font-weight': 'bold'});

            alignDigitsInTable(qtyAdjTable,["ADJUSTED QTY","ORIGINAL QTY"],null);

        }
          
    }

    function set_status_reorder_batch(is_approve){ // Approve or Disapprove
        var approve_msg = (is_approve) ? "approve" : "disapprove";
        var approve_stat = (is_approve) ? "approved" : "disapproved";

        Swal.fire({
          title: 'Confirmation',
          text: 'Are you sure you want to '+approve_msg+' reorder report?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Confirm',
          cancelButtonText: 'Cancel'
        }).then((result) => {
          if (result.isConfirmed) {
    
            var numberOfColumns = seasonTable.columns().header().length;        
            var remNodes = seasonTable.column(numberOfColumns - 5).nodes().to$();
            var zero_rem = []; // Item entries with 0 remaining forecasted qty

            if(is_approve){
                remNodes.each(function() {
                  var qty = $(this).find("span").html(); // #rem_'entry_id'
                  var entry_id = $(this).find("input").val(); // #hid_rem_'entry_id'
                  console.log(qty+" "+entry_id);  
                  if (qty < 1) {
                    zero_rem.push(entry_id);
                  }

                });
            }
            
            console.log(zero_rem);

            if(is_approve){
                $("#approve_btn").html('<img src="<?php echo base_url(); ?>assets/loader/loading-gif.gif" width="20" heigth="20">');
                $("#approve_btn").prop("disabled",true);
            }
            
            $.ajax({
              url: '<?php echo site_url('Po_ctrl/setStatusSeasonReorder')?>', 
              type: 'POST',
              data: { batch_id:batch_id, status:approve_stat, not_in:zero_rem },
              success: function(response) {
                var jObj = JSON.parse(response);
                console.log(jObj);

                if(is_approve){
                    $("#approve_btn").html('<i class="fa fa-check"></i>');
                    $("#approve_btn").prop("disabled",false);
                }

                if(jObj[0]=="success"){
                    var msg = "Successfully "+capitalizeFirstLetter(approve_stat)+"!";
                    if(jObj[1]!="")
                        msg = "Document: "+jObj[1]+" Created!";

                    Swal.fire({ title: 'Message!',
                                text: msg,
                                icon: 'success',
                                allowOutsideClick: false,
                                allowEscapeKey: false })
                    .then((result) => {
                      if (result.isConfirmed) {
                        location.reload();
                      }
                    });

                }else
                    Swal.fire({title: 'Message!', text: jObj[1], icon: "error"});
                
              }
            }); 
            
          } 
        });
    }

    function forward_reorder_batch(){
        Swal.fire({
          title: 'Confirmation',
          text: 'Are you sure you want to forward reorder report to Incorporator?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Confirm',
          cancelButtonText: 'Cancel'
        }).then((result) => {
          if (result.isConfirmed) {
           
            $.ajax({
              url: '<?php echo site_url('Po_ctrl/setStatusSeasonReorder')?>', 
              type: 'POST',
              data: {batch_id:batch_id, status:"forwarded"},
              success: function(response) {
                var jObj = JSON.parse(response);
                console.log(jObj);
                if(jObj[0]=="success")
                    location.reload();
                
              }
            }); 
            
          } 
        });
    }

    function set_status_reorder_adj(is_approve){ // Approve or Disapprove
        var approve_msg1 = (is_approve) ? "Approval" : "Disapproval";
        var approve_msg2 = (is_approve) ? "approve" : "disapprove";
        var approve_stat = (is_approve) ? "approved" : "disapproved";
        
        var numberOfColumns = seasonTable.columns().header().length;
        checkedElements = [];

        seasonTable.column(numberOfColumns-1).nodes().to$().find('.status_box:checked').map(function() {
            var entry_id = $(this).val(); // Get entry_id from status_box
            var status = seasonTable.column(numberOfColumns-2).nodes().to$().find("#link_"+entry_id).html();
            
            if(status.split(":")[0]=="PENDING") // ex. PENDING: 1
                checkedElements.push(entry_id);

        });

        if(checkedElements.length<1){
            Swal.fire({title: 'Message!', text: "No Selected Items for "+approve_msg1+"!", icon: "error"});
        }else{
            console.log(approve_stat);

            Swal.fire({
              title: 'Confirmation',
              text: 'Are you sure you want to '+approve_msg2+' quantity adjustment?',
              icon: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Confirm',
              cancelButtonText: 'Cancel'
            }).then((result) => {
              if (result.isConfirmed) {
               
                $.ajax({
                  url: '<?php echo site_url('Po_ctrl/setStatusQtyAdj')?>', 
                  type: 'POST',
                  data: {entry_ids:checkedElements, status:approve_stat},
                  success: function(response) {
                    var jObj = JSON.parse(response);
                    console.log(jObj);
                    
                    Swal.fire({title: 'Message!', text: jObj[1], icon: jObj[0]});
                    
                    if(jObj[0]=="success"){
                        $('#checkAll').prop('checked',false);
                        loadTable();
                    }
                  }
                });

              } 
            });
               
        } 
    }


    function viewStores(){
        viewStoreForecast();
        $("#stores_modal").modal({backdrop: 'static',keyboard: false});
    }

    function viewStoreForecast(){
        $("#store_span_loader").html('<img src="<?php echo base_url(); ?>assets/loader/loading-gif.gif" width="20" heigth="20">');
        var store_id = $("#store_forecast_sel").val();
        $("#store_pdf_link").attr("href","<?php echo base_url('Po_ctrl/generate_pdf_store/'.$_GET["id"].'/')?>"+store_id);
        storesTable.clear().draw();

        $.ajax({
              url: '<?php echo site_url('Po_ctrl/listSeasonReorderEntriesByStore')?>', 
              type: 'POST',
              data: {store_id:store_id, batch_id:batch_id},
              success: function(response) {
                var jObj = JSON.parse(response);
                console.log(jObj);
                $("#store_span_loader").html('');
                populateStoreTable(jObj);
              }
            });
    }

    function populateStoreTable(lists){

        var list = lists[0];
        var headers = lists[1];

        for(var c=0; c<list.length; c++){
            var entry_id = list[c].entry_id;
            var item_no = list[c].item_no;
            var item_desc = list[c].item_desc;
            var uom = list[c].uom;
            var qty_onhand = list[c].qty_onhand;
            var sale_amts = list[c].sale_amts;
            var fqty = list[c].forecast_qty; // Forecasted Qty
            var new_row = [item_no,item_desc,uom];

            for(var x=0; x<headers.length; x++){
                var cell = 0;
                if (sale_amts.hasOwnProperty(headers[x])) {
                    cell = sale_amts[headers[x]];
                }

                new_row.push(cell);
            }

            new_row.push(fqty);
            new_row.push(qty_onhand);

            var rowNode =  storesTable.row.add(new_row).draw().node();
            alignDigitsInTable(storesTable,["FORECASTED QTY","QTY ONHAND"],"_");   
        }
          
    }

    function viewPoDetails(entry_id){
        $("#pqty_modal").modal({backdrop: 'static',keyboard: false});
        $("#pty_span_loader").html('<img src="<?php echo base_url(); ?>assets/loader/loading-gif.gif" width="20" heigth="20">');

        $.ajax({
          url: '<?php echo site_url('Po_ctrl/listPendingQtyByEntry')?>', 
          type: 'POST',
          data: {entry_id:entry_id},
          success: function(response) {
            var jObj = JSON.parse(response);
            console.log(jObj);
            $("#pqty_span_loader").html('');
            $("#pqty_span").html("("+jObj[0].item_no+": "+jObj[0].item_desc+")");
            populatePoTable(jObj[1]);
          }
        });
    }

    function populatePoTable(list){

        pqtyTable.clear().draw();

        for(var c=0; c<list.length; c++){
            var pending_id = list[c].pending_id;
            var store = list[c].store;
            var document_no = list[c].document_no;
            var po_date = list[c].po_date;
            var uom = list[c].uom;
            var pending_qty = list[c].pending_qty;
            var exp_del_date = list[c].exp_del_date;
            var exp_del_date_ = list[c].exp_del_date_; // Format: F d, Y
            var status = list[c].status;
            var date_html = exp_del_date_;
            
            <?php 
                $c = $this->Po_mod->countSeasonReorderBatchByUser($_GET["id"]);
                if($user_details["user_type"]=="buyer" && $c>0){ // Buyer 

            ?>// PHP CODE

                var hid = "none";
                if(exp_del_date==""){
                    hid = "inline";    
                }

                date_html = '<span id="span_exp_date_'+pending_id+'">'+exp_del_date_+'</span>'+
                            ' <input type="date" id="exp_date_'+pending_id+'" value="'+exp_del_date+'" style="display: '+hid+';">'+
                            ' <button class="btn btn-primary btn-sm" onclick="expDateField('+pending_id+')">'+
                            '<i class="fa fa-pencil-square-o"></i></button>';
                

            <?php } ?>// PHP CODE
            
            var rowNode =  pqtyTable.row.add([store,document_no,po_date,uom,pending_qty,date_html,status]).draw().node();
            alignDigitsInTable(pqtyTable,["PENDING QTY"],null);
        }
          
    }

    function expDateField(pending_id){
        $("#exp_date_"+pending_id).toggle();

        if($('#exp_date_'+pending_id).is(':hidden')){
            var exp_date = $("#exp_date_"+pending_id).val();

            $.ajax({
              url: '<?php echo site_url('Po_ctrl/updateExpDelDate')?>', 
              type: 'POST',
              data: {pending_id:pending_id, exp_del_date: exp_date},
              success: function(response) {
                var jObj = JSON.parse(response);
                console.log(jObj);
                $("#span_exp_date_"+jObj[0]).html(jObj[1]);
                // Swal.fire({title: 'Message!', text: "No Reorder Report Selected!", icon: "error"});
                 
              }
            });
        }

    }

    function generate_txt(){
        var batches = [];
        var nav_si = $("#nav_si_tf").val();
        var nav_dr = $("#nav_dr_tf").val();

        if(vend_type=="SI,DR" && (nav_si.trim()!=="" && nav_dr.trim()!=="")){
            batches.push([[batch_id,"SI",nav_si]]);
            batches.push([[batch_id,"DR",nav_dr]]);    
        }else if(vend_type=="SI" && nav_si.trim()!==""){
            batches.push([[batch_id,"SI",nav_si]]);
        }else if(vend_type=="DR" && nav_dr.trim()!==""){
            batches.push([[batch_id,"DR",nav_dr]]); 
        }

        if(batches.length<1)
            Swal.fire({title: 'Message!', text: "No SI/DR Document Provided!", icon: "error"});
        else{

            for(var c=0; c<batches.length; c++){
                (function (currentIndex) {

                    $.ajax({
                      url: '<?php echo site_url('Po_ctrl/generate_txt')?>', 
                      type: 'POST',
                      data: {batches:batches[c]},
                      success: function(response) {
                        if (response!="false") {
                            var blob      = new Blob([response], { type: 'text/plain' }); // 'application/vnd.ms-excel','text/plain'
                            var url       = URL.createObjectURL(blob);                                  
                            var link      = document.createElement('a');                                                       
                            link.href     = url;                                                        
                            link.download = $("#r_no_p").html()+'.txt'; // xls,txt                                                     
                            document.body.appendChild(link);                                            
                            link.click();                          
                            document.body.removeChild(link);

                        } else {
                            Swal.fire({title: 'Message!', text: "No Reorder Report Selected!", icon: "error"});
                        } 
                      }
                    });

                })(c);
            }

        }

    }

    function generate_excel(){
        
        $.ajax({
          url: '<?php echo site_url('Po_ctrl/generate_excel')?>', 
          type: 'POST',
          data: {batch_id:batch_id},
          success: function(response) {
            
            var blob      = new Blob([response], { type: 'application/vnd.ms-excel' }); // 'application/vnd.ms-excel','text/plain'
            var url       = URL.createObjectURL(blob);                                  
            var link      = document.createElement('a');                                                       
            link.href     = url;                                                        
            link.download = $("#r_no_p").html()+'.xls'; // xls,txt                                                     
            document.body.appendChild(link);                                            
            link.click();                          
            document.body.removeChild(link);

          }
        });

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

    function capitalizeFirstLetter(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

  </script>