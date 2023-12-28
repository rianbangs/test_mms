
<!-- modal1 -->
<div class="modal fade text-left" id="season_modal" tabindex="-1" role="dialog" aria-labelledby="modal"
                                    aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="season_title"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <i data-feather="x"></i>
        </button>
      </div>
      <div class="modal-body" id="season_body">
        
        <div class="row">
          <div class="col-sm-12">
            <form class="form-horizontal">
              
              <div class="form-group">
                <label class="control-label col-sm-2" for="season_name">Name:</label>
                <div class="col-sm-4">
                  <input type="text" class="form-control" placeholder="Season Name" id="season_name" autocomplete="off" oninput="regex1(this)">
                </div>
                <label class="control-label col-sm-2" for="season_type">Season Type:</label>
                <div class="col-sm-4">
                  <select class="form-control" id="season_type" onchange="setPeriodSelect()">
                    <option value="Daily">Daily</option>
                    <option value="Monthly">Monthly</option>
                  </select>
                </div>
              </div>
              
              <div class="form-group">
                
                <input type="hidden" id="type_id">

                <label class="control-label col-sm-2" for="period_start_month1">Period Covered:</label>
                <div id="period_month1" class="col-sm-2">
                  <select class="form-control" id="period_start_month" onchange="setDaySelect('start')">
                    <option value="01">JAN</option>
                    <option value="02">FEB</option>
                    <option value="03">MAR</option>
                    <option value="04">APR</option>
                    <option value="05">MAY</option>
                    <option value="06">JUN</option>
                    <option value="07">JUL</option>
                    <option value="08">AUG</option>
                    <option value="09">SEP</option>
                    <option value="10">OCT</option>
                    <option value="11">NOV</option>
                    <option value="12">DEC</option>
                  </select>
                </div>
                <div id="period_day1" class="col-sm-2" style="display: none;">
                  <select class="form-control" id="period_start_day">
                    
                  </select>
                </div>

                <label class="control-label col-sm-2" for="period_end_month1">To</label>
                <div id="period_month2" class="col-sm-2">
                  <select class="form-control" id="period_end_month" onchange="setDaySelect('end')">
                    <option value="01">JAN</option>
                    <option value="02">FEB</option>
                    <option value="03">MAR</option>
                    <option value="04">APR</option>
                    <option value="05">MAY</option>
                    <option value="06">JUN</option>
                    <option value="07">JUL</option>
                    <option value="08">AUG</option>
                    <option value="09">SEP</option>
                    <option value="10">OCT</option>
                    <option value="11">NOV</option>
                    <option value="12">DEC</option>
                  </select>
                </div>
                <div id="period_day2" class="col-sm-2" style="display: none;">
                  <select class="form-control" id="period_end_day">
                    
                  </select>
                </div>

              </div>

              <div class="form-group">
                <label class="control-label col-sm-2" for="percentage">Percentage:</label>
                 <div class="col-sm-4">
                   <input type="text" class="form-control" placeholder="Percentage" id="percentage" autocomplete="off" oninput="regex2(this)">
                 </div>

                <label class="control-label col-sm-2" for="no_of_ref_year">Reference Year:</label>
                <div class="col-sm-4">
                  <input type="text" class="form-control" placeholder="No. of Reference Year" id="no_of_ref_year" autocomplete="off" oninput="regex2(this)">
                 </div>
              </div>
            </form>
          </div>
        </div>                                 
      </div>

      <div class="modal-footer">
        <button id="season_save_btn" type="button" class="btn btn-primary">
          
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


  <ul class="nav nav-tabs" style="margin-bottom: 30px;">
    <li class="active"><a href="<?php echo base_url('Dept_ctrl/page/3');?>"><b>Season Type</b></a></li>
    
    <li><a href="<?php echo base_url('Dept_ctrl/page/4');?>"><b>Seasonal Item</b></a></li>
  </ul>

 <div class="row">
  	<div class="col-sm-10"></div>
   
    <div class="col-sm-1">
        <button id="season_modal_btn" class="btn btn-primary">ADD SEASON TYPE</button>
    </div> 
    
        
</div>
 
  <div class="row">
       <div class="col-12 table-responsive" style="padding-top: 20px;">
        <table id="season_type-table" class="table table-striped table-bordered table-responsive" style="background-color: rgb(5, 68, 104);">
            <thead style="text-align: center;color:white;">
                <th>NAME</th>
                <th>TYPE</th>
                <th>PERIOD COVERED</th>
                <th>PERCENTAGE ADDITION</th>
                <th>REFERENCE YEAR</th> 
                <th>ACTION</th>
            </thead>
            <tbody>
            	
            </tbody>
        </table>
    </div>     
  </div> 

<script>
    const seasonTable = $("#season_type-table").DataTable({ "ordering": false});
  
    function setPeriodSelect(){
      const s_type = $("#season_type").val();
      
      if(s_type=="Daily"){
        $("#period_month1").attr("class","col-sm-2");
        $("#period_day1").show();
        $("#period_month2").attr("class","col-sm-2");
        $("#period_day2").show();
      }else if(s_type=="Monthly"){
        $("#period_month1").attr("class","col-sm-4");
        $("#period_day1").hide();
        $("#period_month2").attr("class","col-sm-4");
        $("#period_day2").hide();
      }

    }

    setPeriodSelect();

    function setDaySelect(ind){
      const month = parseInt($("#period_"+ind+"_month").val());
      const days = getDaysInMonth(2023, month);
      var html = '';
      for(var c=1; c<=days; c++){
        html+= '<option value="'+c.toString().padStart(1, '0')+'">'+c+'</option>';
      }

      $("#period_"+ind+"_day").html(html);
    }

    setDaySelect("start");
    setDaySelect("end");


    function getDaysInMonth(year, month) {
      // Create a new Date object for the next month's first day
      var nextMonth = new Date(year, month, 1);

      // Subtract one day to get the last day of the current month
      var lastDay = new Date(nextMonth - 1);

      // Extract and return the day component of the last day
      return lastDay.getDate();
    }

    $(function() {
        $('#season_modal_btn').click(function() { // Displays the Season Modal
          if($(this).html()=="ADD SEASON TYPE"){
            $("#season_title").html("ADD SEASON TYPE");
            $("#season_name").val("");
            $("#percentage").val("");
            $("#no_of_ref_year").val("");
            $("#season_save_btn").html("ADD");
            $("#season_modal").modal({backdrop: 'static',keyboard: false});  
          }
           
        });
    });

    $(function() {
        $('#season_save_btn').click(function() { 
          if($(this).html()=="ADD"){
              addSeasonType();
          }else{
              updateSeasonType();
          }
        
        });
          
    });

    function addSeasonType(){
      const s_name = $("#season_name").val();
      const s_type = $("#season_type").val();
      const ps_month = $("#period_start_month").val();
      const ps_day = $("#period_start_day").val();
      const pe_month = $("#period_end_month").val();
      const pe_day = $("#period_end_day").val();
      const percent = $("#percentage").val();
      const ref_year = $("#no_of_ref_year").val();

      console.log(s_name+" "+s_type+" "+ps_month+" "+ps_day+" "+pe_month+" "+pe_day+" "+percent+" "+ref_year);

      $.ajax({
          url: '<?php echo site_url('Dept_ctrl/addSeasonType')?>', 
          type: 'POST',
          data: {s_name: s_name, s_type: s_type, ps_month: ps_month, ps_day: ps_day, pe_month: pe_month, pe_day: pe_day, percent: percent, ref_year: ref_year},
          success: function(response) {
              var jObj = JSON.parse(response);
              Swal.fire({title: 'Message!', text: jObj[1], icon: jObj[0]});
              
              if(jObj[0]=="success"){
                  $("#season_name").val("");
                  $("#percentage").val("");
                  $("#no_of_ref_year").val("");
                  loadTable();
              }
              
          }

        });

    }

    function regex1(elem){ // Makes Text Field only accept Letters and Spaces
        $(elem).val($(elem).val().replace(/[^a-zA-Z\s]/g, ''));
    }

    function regex2(elem) { // Makes Text Field only accept Numbers
        $(elem).val($(elem).val().replace(/[^0-9]/g, ''));
    }
    

    loadTable();

    function loadTable(){
  
        $.ajax({
            url: '<?php echo site_url('Dept_ctrl/listSeasonTypes')?>', 
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
            var id = list[c].type_id;
            var s_name = list[c].season_name;
            var s_type = list[c].season_type;
            var p_cover = list[c].period_covered;
            var percent = list[c].percentage;
            var ref_year = list[c].no_ref_year;
            var html = '<button class="btn btn-primary" onclick="updateSeasonModal(this,'+id+')">UPDATE</button>';

            var rowNode = seasonTable.row.add([s_name,s_type,p_cover,percent,ref_year,html]).draw().node(); 
        }
          
    }

    function updateSeasonModal(elem,id){
        $(elem).prop("disabled",true);

        $.ajax({
            url: '<?php echo site_url('Dept_ctrl/listSeasonTypesDirectById')?>', 
            type: 'POST',
            data: {type_id: id},
            success: function(response) {
                var jObj = JSON.parse(response);
                console.log(jObj);
                
                $(elem).prop("disabled",false);
                $("#season_title").html("UPDATE SEASON TYPE ("+jObj.season_name+")");
                $("#type_id").val(jObj.type_id); // Hidden Input
                $("#season_name").val(jObj.season_name);
                $("#season_type").val(jObj.type_val);
                
                setPeriodSelect();
                if(jObj.type_val=="Daily"){
                  var smd = jObj.period_start.split("-"); // start month day
                  $("#period_start_month").val(smd[0]);
                  setDaySelect("start");
                  $("#period_start_day").val(smd[1]);

                  var emd = jObj.period_end.split("-"); // end month day
                  $("#period_end_month").val(emd[0]);
                  setDaySelect("end");
                  $("#period_end_day").val(emd[1]);

                }else{
                  $("#period_start_month").val(jObj.period_start);
                  $("#period_end_month").val(jObj.period_end);

                }
                
                $("#percentage").val(jObj.percentage);
                $("#no_of_ref_year").val(jObj.no_ref_year);
                $("#season_save_btn").html("UPDATE");
                $("#season_modal").modal({backdrop: 'static',keyboard: false});
                
            }

          });
        

    }

    function updateSeasonType(){
      const type_id = $("#type_id").val();
      const s_name = $("#season_name").val();
      const s_type = $("#season_type").val();
      const ps_month = $("#period_start_month").val();
      const ps_day = $("#period_start_day").val();
      const pe_month = $("#period_end_month").val();
      const pe_day = $("#period_end_day").val();
      const percent = $("#percentage").val();
      const ref_year = $("#no_of_ref_year").val();

      console.log(type_id+" "+s_name+" "+s_type+" "+ps_month+" "+ps_day+" "+pe_month+" "+pe_day+" "+percent+" "+ref_year);

      $.ajax({
          url: '<?php echo site_url('Dept_ctrl/updateSeasonType')?>', 
          type: 'POST',
          data: {type_id: type_id, s_name: s_name, s_type: s_type, ps_month: ps_month, ps_day: ps_day, pe_month: pe_month, pe_day: pe_day, percent: percent, ref_year: ref_year},
          success: function(response) {
              var jObj = JSON.parse(response);
              Swal.fire({title: 'Message!', text: jObj[1], icon: jObj[0]});
              
              if(jObj[0]=="success"){
                  loadTable();
              }
              
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

  
  
</script>