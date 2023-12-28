
<!-- modal1 -->
<div class="modal fade text-left" id="season_upload_modal" tabindex="-1" role="dialog" aria-labelledby="modal"
                                    aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="season_title">UPLOAD SEASONAL ITEM</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <i data-feather="x"></i>
        </button>
      </div>
      <div class="modal-body" id="season_body">
        
        <div class="row">
          <div class="col-12 table-responsive" style="padding-top: 20px; margin-left: 10px; margin-right: 10px;">
            <table id="seasonal_upload-table" class="table table-striped table-bordered table-responsive" style="background-color: rgb(5, 68, 104);">
              <thead style="text-align: center;color:white;">
                <th>VENDOR CODE</th>
                <th>ITEM NO.</th>
                <th>ITEM DESCRIPTION</th>
                <th>SEASONS</th>
              </thead>
              <tbody>
              
              </tbody>
            </table>
          </div>     
        </div> 
                                        
      </div>

      <div class="modal-footer">
        <button id="season_upload_btn" type="button" class="btn btn-primary">
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
<div class="modal fade text-left" id="season_option_modal" tabindex="-1" role="dialog" aria-labelledby="modal"
                                    aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">SEASON TYPES <span id="span_season_item"></span></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <i data-feather="x"></i>
        </button>
      </div>
      <div class="modal-body">
        
          <input type="hidden" id="item_id_hidden">
          <div class="col-12" id="season_chkbox_div">
           
          </div>

          <div class="col-12" id="var_select_div">
           
          </div>

          <div class="col-12" style="padding-top: 20px;">
           <table class="table">
             <thead>
               <th width="10%">SELECTED</th>
               <th width="10%">UOM</th>
               <th width="10%">QTY</th>
               <th>PRICE</th>
               <th>PRICE INC. VAT</th>
               <th>BARCODE</th>
             </thead>
             <tbody id="uom_select_tbody"></tbody>
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
<!-- end of modal1 -->

  <?php 
      $details = $this->Acct_mod->retrieveUserDetails(); 
      if($details["user_type"]=="dept-admin"){
  ?>
  
  <ul class="nav nav-tabs" style="margin-bottom: 30px;">

    <li><a href="<?php echo base_url('Dept_ctrl/page/3');?>"><b>Season Type</b></a></li>
    
    <li class="active"><a href="<?php echo base_url('Dept_ctrl/page/4');?>"><b>Seasonal Item</b></a></li>
    <!-- <li class="dropdown">
      <a class="dropdown-toggle" data-toggle="dropdown" href="#">Menu 1 <span class="caret"></span></a>
      <ul class="dropdown-menu">
        <li><a href="#">Submenu 1-1</a></li>
        <li><a href="#">Submenu 1-2</a></li>
        <li><a href="#">Submenu 1-3</a></li>                        
      </ul>
    </li> -->
  </ul>
  
  <?php } ?>

 <div class="row">
  	<div class="col-sm-6"></div>
    <div class="col-sm-3">
        <input type="file" id="file_select" class="btn btn-default">
    </div>

    <div class="col-sm-1" style="margin-left: 30px;">
        <button id="season_upload_modal_btn" class="btn btn-primary">UPLOAD</button>
    </div> 
     
    
        
</div>
 
  <div class="row">
       <div class="col-12 table-responsive" style="padding-top: 20px;">
        <table id="seasonal-table" class="table table-striped table-bordered table-responsive" style="background-color: rgb(5, 68, 104);">
            <thead style="text-align: center;color:white;">
                <th>VENDOR CODE</th>
                <th>ITEM NO.</th>
                <th>ITEM DESCRIPTION</th>
                <th>SEASONS</th>
                <th>PURCHASE UOM</th>
                <th>ACTION</th>
            </thead>
            <tbody>
            	
            </tbody>
        </table>
    </div>     
  </div> 

<script>
    const seasonTable = $("#seasonal-table").DataTable({ "ordering": false});
    const seasonUploadTable = $("#seasonal_upload-table").DataTable({ "ordering": false});

    loadTable();

    function loadTable(){
  
        $.ajax({
            url: '<?php echo site_url('Po_ctrl/listSeasonalItems')?>', 
            type: 'POST',
            success: function(response) {
                var jObj = JSON.parse(response);
                console.log(jObj);
                populateTable(jObj);
            }

          });
    }

    function populateTable(list){
        seasonTable.clear().draw();
        
        for(var c=0; c<list.length; c++){
            var item_id = list[c].item_id;
            var vendor_code = list[c].vendor_code;
            var item_no = list[c].item_no;
            var item_desc = list[c].item_desc;
            var uom = list[c].purch_uom;
            var season_name = list[c].season_;
            var html = '<button class="btn btn-primary" onclick="viewSeasonTypes(this,'+item_id+')"><i class="fa fa-eye"></i></button>';
            
            var rowNode = seasonTable.row.add([vendor_code,item_no,item_desc,season_name,uom,html]).draw().node();
        }
          
    }

    $(function() {
        $('#season_upload_modal_btn').click(function() { // Displays the Season Modal
          $(this).html('<img src="<?php echo base_url(); ?>assets/loader/loading-gif.gif" width="20" heigth="20">');
          $(this).prop('disabled', true);

          var formData = new FormData(); //create a FormData object
          formData.append('file_select', $('#file_select')[0].files[0]); //add the file to the FormData object

          $.ajax({
            url: '<?php echo site_url('Po_ctrl/setUpItemsForUpload')?>', 
            type: 'POST',
            data: formData, 
            processData: false, //do not process the data
            contentType: false, //do not set content type
            success: function(response) {
                $('#season_upload_modal_btn').html('UPLOAD');
                $('#season_upload_modal_btn').prop('disabled', false);
                
                var jObj = JSON.parse(response);
                console.log(jObj);
                

                if(jObj[0]=="error"){
                  Swal.fire({title: 'Message!', text: jObj[1], icon: jObj[0]});
                }else{  
                  populateUploadTable(jObj[1],jObj[2]);
                  $("#season_upload_modal").modal({backdrop: 'static',keyboard: false});  
                }
                  
            }
            
          });
          
          
           
        });
    });

    $(function() {
        $('#season_upload_btn').click(function() {
          var checked_val = []; 
        
          seasonUploadTable.column(3).nodes().to$().find('.season_chkbx:checked').map(function() {
            var values = $(this).val(); // Get values from season_chkbx
            checked_val.push(values);
          });

          console.log(checked_val);

          if(checked_val.length<1)
            Swal.fire({title: 'Message!', text: "No Tagged Seasonal Items!", icon: "error"});
          else{
            $(this).html('<img src="<?php echo base_url(); ?>assets/loader/loading-gif.gif" width="20" heigth="20">');
            $(this).prop('disabled', true);

            $.ajax({
              url: '<?php echo site_url('Po_ctrl/uploadSeasonalItems')?>', 
              type: 'POST',
              data: {checkboxes: checked_val}, 
              success: function(response) {
                  $('#season_upload_btn').html('UPLOAD');
                  $('#season_upload_btn').prop('disabled', false);
                  
                  var jObj = JSON.parse(response);
                  console.log(jObj);
                  
                  Swal.fire({title: 'Message!', text: jObj[1], icon: jObj[0]});
                  
                  if(jObj[0]=="success"){
                     $("#season_upload_modal").modal('hide');
                     loadTable();  
                  }
                     
              }
              
            });

          }
          
        
        });
          
    });

    function populateUploadTable(list,seasons){
        seasonUploadTable.clear().draw();
        
        for(var c=0; c<list.length; c++){
            var vendor_code = list[c].vendor_code;
            var item_no = list[c].item_no;
            var item_desc = list[c].item_desc;
            var values = "*"+vendor_code+"*"+item_no+"*"+item_desc;
            var html = '';
            
            for(var x=0; x<seasons.length; x++){
              html += '<input type="checkbox" class="season_chkbx" value="'+seasons[x].type_id+values+'"> '+seasons[x].season_name+' ';
            }
            
            var rowNode = seasonUploadTable.row.add([vendor_code,item_no,item_desc,html]).draw().node();
        }
          
    }

    function viewSeasonTypes(elem,id){
      $(elem).prop('disabled', true);
      
      $.ajax({
            url: '<?php echo site_url('Po_ctrl/seasonTypesByItemId')?>', 
            type: 'POST',
            data: {item_id: id}, 
            success: function(response) {
                var jObj = JSON.parse(response);
                console.log(jObj);

                $(elem).prop('disabled', false);                
                $("#item_id_hidden").val(id);
                
                var season_list = jObj[0];
                var this_list = jObj[1];
                var uom_list = jObj[2];
                var item_detail = jObj[3];
                var variant_list = jObj[4];

                var html = '';
                for(var c=0; c<season_list.length; c++){
                  var checked = '';
                  for(var x=0; x<this_list.length; x++){
                    if(season_list[c].type_id==this_list[x].type_id && this_list[x].is_active=='yes')
                      checked = 'checked';
                  }

                  html += '<input type="checkbox" value="'+season_list[c].type_id+'"'+checked+' onclick="seasonCheck(this)"> '+season_list[c].season_name+' ';
                }

                $("#season_chkbox_div").html(html);

                html = '';
                if(variant_list.length>0){
                  $("#var_select_div").attr("style","padding-top: 10px;");
                  html += 'Variant: <select id="variant_select" onchange="variant_select(this.value)">';
                  
                  for(var c=0; c<variant_list.length; c++){
                    html += '<option value="'+variant_list[c]+'">'+variant_list[c]+'</option>';
                  }

                  html += '</select>';
                }

                $("#var_select_div").html(html);
                
                html = '';
                for(var c=0; c<uom_list.length; c++){
                  var selected = '';
                  if(uom_list[c].uom==item_detail.purch_uom)
                    selected = ' checked';

                  html += '<tr><td align="center"><input type="radio" name="uom_radio" value="'+uom_list[c].uom+'"'+selected+
                          ' onclick="update_uom(this)"></td>'+
                          '<td>'+uom_list[c].uom+'</td><td>'+uom_list[c].qty_uom+
                          '</td><td>'+uom_list[c].price+'</td><td>'+uom_list[c].price_vat+'</td><td>'+
                          uom_list[c].barcode+'</td></tr>';
                }

                $("#span_season_item").html("("+item_detail.item_no+": "+item_detail.item_desc+")");
                $("#uom_select_tbody").html(html);

                $("#season_option_modal").modal({backdrop: 'static',keyboard: false});
                   
            }
            
          }); 
    } 

    function seasonCheck(elem){ // Updates the season type of an item. Whether add or set to inactive.
      var item_id = $("#item_id_hidden").val();
      var type_id = $(elem).val();
      var is_checked = $(elem).prop("checked");
      console.log(item_id+" "+type_id+" "+is_checked);

      $.ajax({
            url: '<?php echo site_url('Po_ctrl/saveSeasonOnItem')?>', 
            type: 'POST',
            data: {item_id: item_id, type_id: type_id, is_checked: is_checked}, 
            success: function(response) {
              // console.log(response);
              loadTable();
            }

          });
    }

    function update_uom(elem){
      var item_id = $("#item_id_hidden").val();
      var uom = $(elem).val();

      $.ajax({
            url: '<?php echo site_url('Po_ctrl/savePurchUom')?>', 
            type: 'POST',
            data: {item_id: item_id, purch_uom: uom}, 
            success: function(response) {
              // console.log(response);
              loadTable();
            }

          });
    }

    function variant_select(val){
      var item_id = $("#item_id_hidden").val();
      console.log(val);

      $.ajax({
            url: '<?php echo site_url('Po_ctrl/setUpVariant')?>', 
            type: 'POST',
            data: {item_id: item_id, variant: val}, 
            success: function(response) {
              // console.log(response);
              var jObj = JSON.parse(response);
              var uom_list = jObj[0];
              var item_detail = jObj[1];

              var html = '';
              for(var c=0; c<uom_list.length; c++){
                var selected = '';
                if(uom_list[c].uom==item_detail.purch_uom)
                  selected = ' checked';

                html += '<tr><td align="center"><input type="radio" name="uom_radio" value="'+uom_list[c].uom+'"'+selected+
                        ' onclick="update_uom(this)"></td>'+
                        '<td>'+uom_list[c].uom+'</td><td>'+uom_list[c].qty_uom+
                        '</td><td>'+uom_list[c].price+'</td><td>'+uom_list[c].price_vat+'</td><td>'+
                        uom_list[c].barcode+'</td></tr>';
              }

              // $("#span_season_item").html("("+item_detail.item_no+": "+item_detail.item_desc+")");
              $("#uom_select_tbody").html(html);

            }

          });

    }

    function regex1(elem){ // Makes Text Field only accept Letters and Spaces
        $(elem).val($(elem).val().replace(/[^a-zA-Z\s]/g, ''));
    }

    function regex2(elem) { // Makes Text Field only accept Numbers
        $(elem).val($(elem).val().replace(/[^0-9]/g, ''));
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


</script>