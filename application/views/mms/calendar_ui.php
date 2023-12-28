<?php $user_details = $this->Acct_mod->retrieveUserDetails(); ?>

<style>

  #calendar_tbl{
    border: 1px;
    background-color: white;
    width: 100%;
    color: black;
  }  

  #calendar_tbl,th,td{
    padding: 15px;
    border: 1px solid black;
  }

  .calendar_head{
    color: white;
    background-color: #054468;
  }

  .cells {
    width: 250px;
  }

  .wrap-text { 
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    cursor: default; /* Set the default cursor style */
  }

  .wrap-text:hover {
    cursor: pointer; /* Change cursor style on hover */
  } 

</style>

<!-- modal1 -->
<div class="modal fade text-left" id="key_modal" tabindex="-1" role="dialog" aria-labelledby="modal"
                                    aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="key_title">MANAGER'S KEY</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <i data-feather="x"></i>
        </button>
      </div>
      
      <div class="modal-body" id="key_body">
        <div class="row">
          <div class="col-sm-12">
            <div id="m_msg"></div>
            <form class="form-horizontal">
              <div class="form-group">
                <label class="control-label col-sm-3" for="m_user">Username:</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" placeholder="Username" id="m_user" autocomplete="off">
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-sm-3" for="m_pass">Password:</label>
                <div class="col-sm-8">
                  <input type="password" class="form-control" placeholder="Password" id="m_pass" autocomplete="off">
                </div>
                
              </div>
            </form>
          </div>
        </div>                               
      </div>

      <div class="modal-footer">
        <button id="approve_key_btn" type="button" class="btn btn-primary">
          APPROVE
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
<div class="modal fade text-left" id="buyer_modal" tabindex="-1" role="dialog" aria-labelledby="modal"
                                    aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="buyer_title">BUYERS</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <i data-feather="x"></i>
        </button>
      </div>
      
      <div class="modal-body" id="buyer_body">
        <div class="row">
          <div class="col-sm-12">
            <div id="b_msg"></div>

            <table class="table" id="buyer-table">
              <thead>
                <th width="100px">NAME</th>
                <th>DATE GENERATED</th>
                <th>STATUS</th>
                <th>ACTION</th>
              </thead>
              
              <tbody>
                
              </tbody>
            </table>
            
          </div>
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

  <div class="col-sm-1">
    <select id="year_sel" class="btn btn-primary">
      <?php
        $year = date("Y");
        for($c=intval($year)+1; $c>=2023; $c--){
          
          $selected = '';
          if($year==$c)
            $selected = ' selected';

          echo '<option value="'.$c.'"'.$selected.'>'.$c.'</option>';
        }
      ?>
    </select>
  </div>

  <div class="col-sm-1">
    <select id="month_sel" class="btn btn-primary">
      <option value="01">JAN</option>
      <option value="02">FEB</option>
      <option value="03">MAR</option>
      <option value="04">APR</option>
      <option value="05">MAY</option>
      <option value="06">JUN</option>
      <option value="07">JULY</option>
      <option value="08">AUG</option>
      <option value="09">SEP</option>
      <option value="10">OCT</option>
      <option value="11">NOV</option>
      <option value="12">DEC</option>
    </select>
  </div>
  
  <div class="col-sm-1">
    <select id="vendor_sel" class="btn btn-primary">
      <?php
        $vendors = $this->Po_mod->getUserGroupCodes();
        for($c=0; $c<count($vendors); $c++){
          echo '<option value="'.$vendors[$c].'">'.$vendors[$c].'</option>';  
        }
        
      ?>
    </select>
  </div>

</div>

<div class="row" style="padding-top: 20px;">
  
  <div class="col-sm-12">
    <div id="thecalendar"></div> 
  </div>

</div>

<script>
  const buyerTable = $("#buyer-table").DataTable({ "ordering": false});


  function setMonth(){
    const date = new Date();
    const month =  String(date.getMonth() + 1).padStart(2, '0');
    $("#month_sel").val(month); 
  }

  setMonth();

  function loadTable(){
    $("#thecalendar").html('<div style="text-align: center;"><img src="<?php echo base_url(); ?>assets/loader/blue loading.gif"></div>');
    const year = $("#year_sel").val();
    const month = $("#month_sel").val();
    
    $.ajax({
         type:'POST',
         url: '<?php echo site_url('Po_ctrl/generateCalendar')?>',
         data: {year: year, month: month},     
         success: function(data){
             $("#thecalendar").html(data);
             $('#calendar_tbl tr').find('td:last-child, td:nth-last-child(2)').hide(); // Hides last 2 columns
             loadDetails();
         }
    });
  }

  loadTable();

  $(function(){
    $("#year_sel").change(function(){
      loadTable();
    });
  });

  $(function(){
    $("#month_sel").change(function(){
      loadTable();
    });
  });

  $(function(){
    $("#vendor_sel").change(function(){
      loadTable();
    });
  });

  function loadDetails(){
    $("#tbl_loader").html('<img src="<?php echo base_url(); ?>assets/loader/loading-gif.gif" width="20" heigth="20">');
    const year = $("#year_sel").val();
    const month = $("#month_sel").val();
    const vendor = $("#vendor_sel").val();
    
    $.ajax({
         type:'POST',
         url: '<?php echo site_url('Po_ctrl/getCalendarListItems')?>',
         data: {year: year, month: month, vendor: vendor},     
         success: function(data){
            $("#tbl_loader").html("");
            var res = JSON.parse(data);
            // console.log(res);
            $('.cells').each(function() {
              for(var c=0; c<res.length; c++){
                var day = $(this).attr('id').substr(2).padStart(2, '0'); // Remove C_ and add leading zeroes
                var selected_date = year+"-"+month+"-"+day;
                // console.log(res[c].vendor_code+" "+res[c].vendor_name+" "+res[c].start_date+" "+selected_date); 
                
                var status = 'style="color: red;"'; // red
                if(res[c].filed)
                  status = 'style="color: green;"'; // green

                if(selected_date==res[c].start_date){
                  $(this).append('<p class="wrap-text" '+status+' onclick="list_reorder(\''+res[c].vendor_code+'\',\''+res[c].vendor_name.replace(/'/g, "\\'")+'\',\''+selected_date+'\',\''+vendor+'\')">'+res[c].vendor_code+' '+res[c].vendor_name+'</p>'); // Replace ' with \' to avoid syntax errors
                }  
                
              }
            });
            
         }
    });
  }

  var is_append = false; 

  function list_reorder(vendor_code,vendor_name,date_tag,group_code){
    
    $.ajax({
      url: '<?php echo site_url('Po_ctrl/listBuyersUnderPO')?>', 
      type: 'POST',
      data: { vendor_code: vendor_code, date_tag: date_tag, group_code: group_code }, 
      success: function(response) {
        var jObj = JSON.parse(response);
        console.log(jObj);

        <?php if($user_details["user_type"]=="buyer"){ ?>// PHP CODE

          var searchWrapper = $('#buyer-table_filter');
          if(!is_append){
            var html = '<span id="buttonSpan"><span>'; 
            searchWrapper.append(html);
            is_append = true; 
          }

          var buttonsHtml = ' <button type="button" class="btn btn-success btn-sm" onclick="open_reorder(\''+vendor_code+'\',\''+vendor_name.replace(/'/g, "\\'")+'\',\''+date_tag+'\',\''+group_code+'\')">Generate</button>';
          $('#buttonSpan').html(buttonsHtml);

        <?php } ?>// PHP CODE
        
        $("#b_msg").html("<p>"+vendor_code+" "+vendor_name+" : "+date_tag+"</p>");
        populateBuyerTable(jObj);
        $("#buyer_modal").modal({backdrop: 'static',keyboard: false});
      }
    
    });

  }

  function open_reorder(vendor_code,vendor_name,date_tag,group_code){
    console.log(vendor_code+" "+date_tag+" "+group_code);
     $.ajax({
      url: '<?php echo site_url('Po_ctrl/getPoDayDiff')?>', 
      type: 'POST',
      data: { date_tag:date_tag }, 
      success: function(response) {
        var jObj = JSON.parse(response);
        console.log(jObj);

        $("#buyer_modal").modal('hide');
        
        switch(jObj[0]){
            case "early":
            case "late":
              $("#m_msg").html("<p>"+vendor_code+" "+vendor_name+"</p><p>"+jObj[1]+" "+date_tag+"</p>");
              $("#key_modal").modal({backdrop: 'static',keyboard: false});
              $('#approve_key_btn').attr('onclick','approve(\''+vendor_code+'\',\''+vendor_name.replace(/'/g, "\\'")+'\',\''+date_tag+'\',\''+group_code+'\')'); 
              break;

            default:
              js_submit(vendor_code,vendor_name,date_tag,group_code);
              break;
        }
          
      }
    });
  }

  function approve(vendor_code,vendor_name,date_tag,group_code){
    const username = $('#m_user').val();
    const password = $('#m_pass').val();

    $.ajax({
      url: '<?php echo site_url('Po_ctrl/mkey_approve')?>', 
      type: 'POST',
      data: { group_code: group_code, username: username, password: password }, 
      success: function(response) {
        var jObj = JSON.parse(response);
        console.log(jObj);
        if(jObj[0])
          js_submit(vendor_code,vendor_name,date_tag,group_code);
        else
          Swal.fire({title: 'Message!', text: "Incorrect Credentials!", icon: 'error'});
      }
    
    });
  }

  function js_submit(vendor_code,vendor_name,date_tag,group_code){
    var form = $('<form></form>');
    form.attr('action', '<?php echo base_url('Mms_ctrl/mms_ui/3')?>');
    form.attr('method', 'POST');

    // Append hidden input fields with the desired data
    var vendor_code_field = $('<input/>', {
      type: 'hidden',
      name: 'vendor_code',
      value: vendor_code
    });
    form.append(vendor_code_field);

    var vendor_name_field = $('<input/>', {
      type: 'hidden',
      name: 'vendor_name',
      value: vendor_name
    });
    form.append(vendor_name_field);


    var date_tag_field = $('<input/>', {
      type: 'hidden',
      name: 'date_tag',
      value: date_tag
    });
    form.append(date_tag_field);

    var group_code_field = $('<input/>', {
      type: 'hidden',
      name: 'group_code',
      value: group_code
    });
    form.append(group_code_field);

    $('body').append(form);
    // Submit the form
    form.submit();
  }

  
  function populateBuyerTable(list){
      buyerTable.clear().draw();
      
      for(var c=0; c<list.length; c++){
          var buyer_name = list[c].name;
          var date_generated = list[c].date_generated;
          var status = list[c].status;
          var reorder_link = '<a class="btn btn-primary" href="<?php echo base_url('Mms_ctrl/mms_ui/4?r_no=')?>'+list[c].batch_id+'">'+
                              '<i class="fa fa-eye"></i></a>';
          
          var rowNode = buyerTable.row.add([buyer_name,date_generated,status,reorder_link]).draw().node();

          // $(rowNode).find('td').css({'color': 'black', 'font-family': 'sans-serif','text-align': 'center'});  
      }
        
  }

</script>
