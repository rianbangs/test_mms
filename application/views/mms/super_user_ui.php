<!-- modal -->
<div class="modal fade text-left" id="add_user_modal" tabindex="-1" role="dialog" aria-labelledby="modal"
                                    aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="add_user_title">ADD USER FORM</h4>
      </div>

      <div class="modal-body" id="add_user_body">
       
        <!-- <div class="form-group">
            <input type="text" class="form-control" placeholder="Search Lastname, Firstname" id="fullname" name="fullname">
        </div>

        <div class="form-group">
            <input type="text" class="form-control" placeholder="Username" id="username" name="username">
        </div> -->
        <div class="row">
          <div class="col-sm-12">
            <form class="form-horizontal">
              <div class="form-group">
                <label class="control-label col-sm-2" for="fullname">Full Name:</label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" placeholder="Search Lastname, Firstname" id="fullname" name="fullname" autocomplete="off">
                  <div id="dropdown" class="dropdown-menu" style="display: none;"></div>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-sm-2" for="position">Position:</label>
                <div class="col-sm-4">
                  <input type="text" class="form-control" id="position" name="position" readonly>
                </div>
                <label class="control-label col-sm-2" for="department">Department:</label>
                <div class="col-sm-4">
                  <input type="text" class="form-control" id="department" name="department" readonly>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-sm-2" for="bu">Business Unit:</label>
                 <div class="col-sm-4">
                   <input type="text" class="form-control" id="bu" name="bu" readonly>
                 </div>

                <label class="control-label col-sm-2" for="user">Username:</label>
                <div class="col-sm-4">
                  <input type="text" class="form-control" id="user" name="user" autocomplete="off" required>
                 </div>
              </div>

              <div class="form-group">
                <label class="control-label col-sm-2" for="usertype">User Type:</label>
                 <div class="col-sm-4">
                  <select class="form-control" id="usertype">
                    <option value="dept-admin">Dept. Admin</option>
                    <option value="buyer">Buyer</option>
                    <option value="category-head">Category Head</option>
                    <option value="corp-manager">Corporate Manager</option>
                    <option value="incorporator">Incorporator</option>
                  </select>
                 </div>
              </div>

              <div id="vendor-select" hidden>
                <div class="form-group ">
                  <label class="control-label col-sm-2" for="vendor">Vendor Category:</label>
                  <div class="col-sm-4">

                    <ul style="padding-left: 20px;">
                        
                      <li>
                        <div style="margin-bottom: 8px;" class="form-group">
                          <?php 
                            $gc_list = $this->Po_mod->retrieveGroupCodes();
                            foreach ($gc_list as $gc) {
                              echo '<input type="checkbox" id="tasks" name="tasks[]" value="'.$gc.'">  <label style="margin-left: 5px;">'.$gc.'</label><br>';
                            }
                          ?>
                          <!-- <input type="checkbox" id="tasks" name="tasks[]" value="SC1">  <label style="margin-left: 5px;"> SC1 </label><br>
                          <input type="checkbox" id="tasks" name="tasks[]" value="SC2">  <label style="margin-left: 5px;"> SC2 </label><br>
                          <input type="checkbox" id="tasks" name="tasks[]" value="SC3">  <label style="margin-left: 5px;"> SC3 </label><br>
                          <input type="checkbox" id="tasks" name="tasks[]" value="SC4">  <label style="margin-left: 5px;"> SC4 </label><br>
                          <input type="checkbox" id="tasks" name="tasks[]" value="SC5">  <label style="margin-left: 5px;"> SC5 </label><br>
                          <input type="checkbox" id="tasks" name="tasks[]" value="SC6">  <label style="margin-left: 5px;"> SC6 </label><br>
                          <input type="checkbox" id="tasks" name="tasks[]" value="SC7">  <label style="margin-left: 5px;"> SC7 </label><br>
                          <input type="checkbox" id="tasks" name="tasks[]" value="SC8">  <label style="margin-left: 5px;"> SC8 </label><br>
                          <input type="checkbox" id="tasks" name="tasks[]" value="SC9">  <label style="margin-left: 5px;"> SC9 </label><br>
                          <input type="checkbox" id="tasks" name="tasks[]" value="SC10"> <label style="margin-left: 5px;"> SC10</label><br>
                          <input type="checkbox" id="tasks" name="tasks[]" value="DS1">  <label style="margin-left: 5px;"> DS1 </label><br>
                          <input type="checkbox" id="tasks" name="tasks[]" value="DS2">  <label style="margin-left: 5px;"> DS2 </label><br>
                          <input type="checkbox" id="tasks" name="tasks[]" value="DS3">  <label style="margin-left: 5px;"> DS3 </label><br>
                    -->
                        </div>
                      </li>
                      
                    </ul>
                    
                  
                  </div>

                  <label class="control-label col-sm-2" for="store">Store: </label>
                  <div class="col-sm-4">
                    <select class="form-control" id="store">
                      <!-- <option value="1">ICM</option>
                      <option value="2">ASC Mall</option>
                      <option value="3">Plaza Marcela</option>
                      <option value="4">ASC Talibon</option>
                      <option value="5">Alta Citta</option>
                      <option value="6">CENTRAL DC</option> -->
                      <?php 
                            $store_list = $this->Po_mod->getSelectedStores(array(1,2,3,4,5,6));
                            foreach ($store_list as $st) {
                              echo '<option value="'.$st["store_id"].'">'.$st["display_name"].'</option>';
                            }
                      ?>
                      
                    </select>
                  
                  </div>
                </div>
              </div>

            </form>
          </div>
        </div>


      </div>
                                    

      <div class="modal-footer">
        <button id="add_user_btn" type="button" class="btn btn-primary">
          ADD
        </button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">
          <i class="bx bx-x d-block d-sm-none"></i>
          <span class="d-none d-sm-block ">Close</span>
        </button>
      </div>
    </div>
  </div>
</div>
<!-- end of modal -->  

<!-- modal -->
<div class="modal fade text-left" id="edit_user_modal" tabindex="-1" role="dialog" aria-labelledby="modal"
                                    aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="edit_user_title">UPDATE USER FORM</h4>
      </div>

      <div class="modal-body" id="edit_user_body">
       
        <form class="form-horizontal" id="editUser" method="post">
            <div id="edituser_content"></div>
        </form>

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
<!-- end of modal --> 

<!-- modal -->
<div class="modal fade text-left" id="edit_key_modal" tabindex="-1" role="dialog" aria-labelledby="modal"
                                    aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="edit_key_title">UPDATE MANAGER'S KEY FORM</h4>
      </div>

      <div class="modal-body" id="edit_key_body">
       
        <form class="form-horizontal" id="editKey" method="post">
            <div id="editkey_content"></div>
        </form>

      </div>
       
      <div class="modal-footer">
        <button id="edit_key_btn" type="button" class="btn btn-primary">
          UPDATE
        </button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">
          <i class="bx bx-x d-block d-sm-none"></i>
          <span class="d-none d-sm-block ">Close</span>
        </button>
      </div>
    </div>
  </div>
</div>
<!-- end of modal --> 

<!-- modal -->
<div class="modal fade text-left" id="add_key_modal" tabindex="-1" role="dialog" aria-labelledby="modal"
                                    aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="add_key_title">ADD MANAGER'S KEY FORM</h4>
      </div>

      <div class="modal-body" id="add_key_body">
       
        <form class="form-horizontal" id="addKey" method="post">
            <div id="addkey_content"></div>
        </form>

      </div>
       
      <div class="modal-footer">
        <button id="add_key_btn" type="button" class="btn btn-primary">
          ADD
        </button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">
          <i class="bx bx-x d-block d-sm-none"></i>
          <span class="d-none d-sm-block ">Close</span>
        </button>
      </div>
    </div>
  </div>
</div>
<!-- end of modal -->

 <div class="row">
  	<div class="col-sm-10"></div>
    <div class="col-sm-2">       
	   <button class="btn btn-primary" onclick="viewAddUserModal()">Add New User</button>
	</div>
        
  </div>
 
   

  <div class="row">
       <div class="col-12 table-responsive" style="padding-top: 20px;">
        <table id="user-table" class="table table-striped table-bordered table-responsive" style="background-color: rgb(5, 68, 104);">
            <thead style="text-align: center;color:white;">
                <th>NAME</th>
                <th>POSITION</th>
                <th>DEPARTMENT</th>
                <th>USERNAME</th>
                <th>VENDOR CATEGORY</th> 
                <th>BUSINESS UNIT</th>
                <th>USERTYPE</th>
                <th>ACTION</th>
            </thead>
            <tbody>
            	
            </tbody>
        </table>
    </div>     
  </div>


 <script>

 	//const userTable = $("#user-table").DataTable({ "ordering": false});
  function trimfield(str) 
  { 
    return str.replace(/^\s+|\s+$/g,''); 
  }
  
  var table = $('table#user-table').DataTable({
              
    "destroy": true,
    'serverSide': true,
    'processing': true,
    "ajax": {
      url: "<?php echo site_url('mms/users'); ?>",
      type: "POST",
        
    },
    "order": [ [ 0, 'asc' ]],

    "columnDefs": [{
        "targets": [7],
        "orderable": false,
        "searchable": false,
        "className": "text-left",
    },
    ]
  });

  $('table#user-table').on('click', 'a.action', function() {

    let [action, ids] = this.id.split('-');

    if (action == "edit") {

        $("div#edit_user_modal").modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });

        $.ajax({
            type: "POST",
            url: "<?= site_url('mms/edituser_content'); ?>",
            data : {
                ids
            },
            success: function(data) {

                $("div#edituser_content").html(data);
            }
        });
    }

    if (action == "editkey") {

        $("div#edit_key_modal").modal({
            backdrop: true,
            keyboard: true,
            show: true
        });

        $.ajax({
            type: "POST",
            url: "<?= site_url('mms/editkey_content'); ?>",
            data : {
                ids
            },
            success: function(data) {

                $("div#editkey_content").html(data);
            }
        });

    }

    if (action == "addkey") {

        $("div#add_key_modal").modal({
            backdrop: true,
            keyboard: true,
            show: true
        });

        $.ajax({
            type: "POST",
            url: "<?= site_url('mms/addkey_content'); ?>",
            data : {
                ids
            },
            success: function(data) {

                $("div#addkey_content").html(data);
            }
        });

    }
  });


    var emp_id = "";
 	
    function viewAddUserModal(){
        $("#add_user_modal").modal({backdrop: 'static',keyboard: false}); 
    }

    function edituser_content(ids)
    {
      $.ajax({
          url: 'mms/edituser_content',
          type: 'POST',
          data: {ids:ids},
          error: function() {
              // Swal.fire({
              //   icon: 'error',
              //   title: 'Oops...',
              //   text: 'Something went wrong!'
              // })
            alert('Something went wrong')
          },
          success: function(data) {                 
              $("#edituser_content").html(data);
          }
      });
    }
      

    
    // function viewEditUserModal(){
    //     $("#edit_user_modal").modal({backdrop: 'static',keyboard: false}); 
    // }
 	
    $(function(){
        $("#fullname").on("input", function() {
            var fullname = $("#fullname").val();   
            
            $.ajax({
              url: '<?php echo site_url('Super_ctrl/searchEmployee')?>', 
              type: 'POST',
              data: { fullname:fullname }, 
              success: function(response) {
                var jObj = JSON.parse(response);
                console.log(jObj);
                
                $("#dropdown").html("");

                if(fullname.length>0){
                    for(var c=0; c<jObj.length; c++){
                        var name = jObj[c].name;
                        var id = jObj[c].emp_id;
                        var pos = jObj[c].position;
                        var bu = jObj[c].business_unit;
                        var dept = jObj[c].dept_name;

                        var option = $('<div>')
                          .addClass('dropdown-item')
                          .css('cursor', 'pointer')
                          .text(name)
                          .click((function(name, id, pos, dept, bu) {
                              return function() {
                                emp_id = id;
                                $("#fullname").val(name);
                                $("#position").val(pos);
                                $("#department").val(dept);
                                $("#bu").val(bu);

                                $("#dropdown").hide();
                                console.log(emp_id);
                              };
                            })(name, id, pos, dept, bu));

                        $("#dropdown").append(option);
                    }

                    $("#dropdown").show();
                }else{
                    $("#dropdown").hide();
                    emp_id = "";
                    $("#position").val("");
                    $("#department").val("");
                    $("#bu").val("");
                }
                
              }
            });
        });
    });

    $(document).ready(function(){
            
      //let bu = $('#bu').select2("val");
          
      $('#usertype').change(function(){
      var usertype = $(this).val();
      //alert(usertype);
      if(usertype == 'buyer' || usertype == 'category-head'){
        $("#vendor-select").fadeIn();
        $("#store-select").fadeIn();
      }else{
        $("#vendor-select").hide();
        $("#store-select").hide();
      }

      });
    });

    $(function(){
        $(document).on('click', function(event){
            if (!$("#fullname").is(event.target) && !$("#fullname").has(event.target).length) {
                $("#dropdown").hide();
            }
        });
    });

    $(function(){
        $("#add_user_btn").on('click', function(e){

          e.preventDefault();
            let formData = $(this).serialize();

            var username  = $("#user").val();
            var usertype  = $("#usertype").val();
            var store     = $("#store").val();
            let vendor    = $("input[name = 'tasks[]']:checked").length;
            
            if(vendor == 0 && usertype == 'buyer') {
                
              // swal_message('warning','Please choose atleast one task!');
              alert('vendor required') 
              return;
            }

            if(vendor == 0 && usertype == 'category-head') {
                
              // swal_message('warning','Please choose atleast one task!');
              alert('vendor required') 
              return;
            }

            if(username == ""){
                alert('username required') 
              return;
            }
            
            var val = [];
              $(':checkbox:checked').each(function(i){
                val[i] = $(this).val();
              });
            
            console.log(emp_id+" "+username+" "+val+" "+usertype+" "+store);  

            //alert(emp_id+" "+username+" "+val+" "+usertype+" "+store);  

             $.ajax({
                type: "POST",
                url: "<?= site_url('mms/register'); ?>",
                data : {
                    emp_id    : emp_id,
                    username  : username,
                    usertype  : usertype,
                    val       : val,
                    store     : store
                    
                },
                // dataType: 'json',
                success: function(data) {
                    if(trimfield(data) == 'User-exists'){
                     
                        //swal_message('error','User already exists!');
                        alert('User-exists');
                          $("#fullname").val("");
                          $("#position").val("");
                          $("#department").val("");
                          $("#bu").val("");
                          $("#user").val("");
                          $("#usertype").val("");
                          $("#vendor").val("");
                          $("#store").val("");
                        $('div#add_user_modal').modal('hide');
                          
                        //window.setTimeout(function(){location.reload()},2000)
                    }
                    if(trimfield(data) == 'Username-exists'){
                     
                        //swal_message('error','User already exists!');
                        alert('Username-exists');
                          $("#fullname").val("");
                          $("#position").val("");
                          $("#department").val("");
                          $("#bu").val("");
                          $("#user").val("");
                          $("#usertype").val("");
                          $("#vendor").val("");
                          $("#store").val("");
                        $('div#add_user_modal').modal('hide');
                        //window.setTimeout(function(){location.reload()},2000)
                    }
                    if(trimfield(data) == 'ok'){

                        //swal_message('success','User Successfully Added!'); 
                        alert('success');
                          $("#fullname").val("");
                          $("#position").val("");
                          $("#department").val("");
                          $("#bu").val("");
                          $("#user").val("");
                          $("#usertype").val("");
                          $("#vendor").val("");
                          $("#store").val("");
                        $('div#add_user_modal').modal('hide');
                        //window.setTimeout(function(){location.reload()},2000)
                    }
                    
                }
            });          

        });
    });
    
    $(function(){
        $('#editUser').on("submit", function(e){
        // $("#add_user_btn").on('click', function(e){

          e.preventDefault();
            let formData = $(this).serialize();

            var username  = $("#username").val();
            var id        = $("#id").val();
            var password  = $("#password").val();
            var usertype  = $("#user_type").val();
            var store     = $("#store1").val();
            let vendor    = $("input[name = 'tasks[]']:checked").length;
            
            if(vendor == 0 && usertype == 'buyer') {
                
              // swal_message('warning','Please choose atleast one task!');
              alert('vendor required') 
              return;
            }

            if(vendor == 0 && usertype == 'category-head') {
                
              // swal_message('warning','Please choose atleast one task!');
              alert('vendor required') 
              return;
            }

            if(username == ""){
                alert('username required') 
              return;
            }
            
            var val = [];
              $(':checkbox:checked').each(function(i){
                val[i] = $(this).val();
              });
            
            console.log(username+" "+val+" "+store+" "+password);  

            //alert(emp_id+" "+username+" "+val+" "+usertype+" "+store);  

             $.ajax({
                type: "POST",
                url: "<?= site_url('mms/update'); ?>",
                data : {
                    id        : id,
                    username  : username,
                    usertype  : usertype,
                    password  : password,
                    val       : val,
                    store     : store
                    
                },
                // dataType: 'json',
                success: function(data) {
    
                    if(trimfield(data) == 'Username-exists'){
                     
                        //swal_message('error','User already exists!');
                        alert('Username-exists');
                          $("#fullname").val("");
                          $("#position").val("");
                          $("#department").val("");
                          $("#bu").val("");
                          $("#user").val("");
                          $("#usertype").val("");
                          $("#vendor").val("");
                          $("#store").val("");
                        $('div#edit_user_modal').modal('hide');
                        //window.setTimeout(function(){location.reload()},2000)
                    }
                    if(trimfield(data) == 'ok'){

                        //swal_message('success','User Successfully edited!'); 
                        alert('success');
                          $("#fullname").val("");
                          $("#position").val("");
                          $("#department").val("");
                          $("#bu").val("");
                          $("#user").val("");
                          $("#usertype").val("");
                          $("#vendor").val("");
                          $("#store").val("");
                        $('div#edit_user_modal').modal('hide');
                        window.setTimeout(function(){location.reload()},2000)
                    }
                    
                }
            });          

        });
    });

    $(function(){
        $("#add_key_btn").on('click', function(e){

          e.preventDefault();
            let formData = $(this).serialize();

            var user_id  = $("#id").val();
            var username  = $("#username").val();
            var password  = $("#password").val();
            
            
            console.log(user_id+" "+username+" "+password);  

            //alert(emp_id+" "+username+" "+val+" "+usertype+" "+store);  

             $.ajax({
                type: "POST",
                url: "<?= site_url('mms/saveKey'); ?>",
                data : {
                    user_id   : user_id,
                    username  : username,
                    password  : password
                    
                },
                // dataType: 'json',
                success: function(data) {
                  if(trimfield(data) == 'ok'){

                      //swal_message('success','User Successfully Added!'); 
                      alert('success');
                        
                        $("#username").val("");
                        $("#password").val("");
                        
                      $('div#add_key_modal').modal('hide');
                      window.setTimeout(function(){location.reload()},2000)
                  }
                    
                }
            });          

        });
    });

    $(function(){
        $("#edit_key_btn").on('click', function(e){

          e.preventDefault();
            let formData = $(this).serialize();

            var user_id  = $("#id").val();
            var username  = $("#username").val();
            var password  = $("#password").val();
            
            
            console.log(user_id+" "+username+" "+password);  

            //alert(emp_id+" "+username+" "+val+" "+usertype+" "+store);  

             $.ajax({
                type: "POST",
                url: "<?= site_url('mms/saveKey'); ?>",
                data : {
                    user_id   : user_id,
                    username  : username,
                    password  : password
                    
                },
                // dataType: 'json',
                success: function(data) {
                  if(trimfield(data) == 'ok'){

                      //swal_message('success','User Successfully edited!'); 
                      alert('success');
                        
                        $("#username").val("");
                        $("#password").val("");
                        
                      $('div#edit_key_modal').modal('hide');
                      //window.setTimeout(function(){location.reload()},2000)
                  }
                    
                }
            });          

        });
    });
  
 	function populateTable(entries){
        var list = entries.list;

        for(var c=0; c<list.length; c++){
            var item_code = list[c].item_code;
            var item_desc = list[c].item_desc;
            var price = list[c].price.toFixed(2);
            var qty = list[c].qty;
            var uom = list[c].uom;
            var t_price = list[c].t_price.toFixed(2);
            var rowNode = itemTable.row.add([item_code,item_desc,price,qty,uom,t_price]).draw().node();

            $(rowNode).find('td').css({'color': 'red', 'font-family': 'sans-serif','text-align': 'center'});  
        }
        
        //For Total
        var compute = entries.compute;
        var t_qty = compute.t_qty;
        var t_cost = compute.t_cost.toFixed(2);
        var finalNode = itemTable.row.add(['','','',t_qty,'',t_cost]).draw().node();
        $(finalNode).find('td').css({'color': 'black', 'font-family': 'sans-serif','text-align': 'center'});

        //For Discount
        var discount = compute.discount;
        var d_cost = compute.d_cost.toFixed(2);
        var discountNode = itemTable.row.add(['','','','<b>Discount (Php '+discount+')</b>','',d_cost]).draw().node();
        $(discountNode).find('td').css({'color': 'black', 'font-family': 'sans-serif','text-align': 'center'});
        
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