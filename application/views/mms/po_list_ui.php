
<?php

    
   
     // seasonal table............................................................................ 
     $data_seasonal  = '';
     $season_po_list = $this->Po_view_mod->get_season_po_list();  

     $status         = 'Active';
     if(!empty($season_po_list))
     {
         foreach($season_po_list as $po_season)
         {   
          
            $check_date = array(
                                'document_number' => $po_season['document_no'],
                                'store_id'        => $po_season['store_id'],
                                'vendor_code'     => $po_season['vendor_code'],
                                'vendor_name'     => $po_season['vendor_name']
                               );
            $check_date = $this->Po_view_mod->check_data($check_date );
            if(empty($check_date))
            {
              $this->Po_view_mod->insert_seasonal_data($po_season['document_no'],$po_season['store_id'],$status,$po_season['vendor_code'],$po_season['vendor_name']);
            }else{

                 }
         }
     }


      // :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::


      // reorder_report_data_po  table..............................................................
      $reorder_po_list = $this->Po_view_mod->get_reorder_po_list();

    
       if(!empty($reorder_po_list))
       {
         foreach($reorder_po_list as $po_reorder)
         {
            $check_reorder = array(
                                   'document_number' => $po_reorder['document_no'],
                                   'store_id'        => $po_reorder['store_id'],
                                   'vendor_code'     => $po_reorder['supplier_code'],
                                   'vendor_name'     => $po_reorder['supplier_name']
                                  );

             $check_reorder_list = $this->Po_view_mod->check_data_po($check_reorder);

             if(empty($check_reorder_list))
             {
              $this->Po_view_mod->insert_seasonal_data($po_reorder['document_no'],$po_reorder['store_id'],$status,$po_reorder['supplier_code'],$po_reorder['supplier_name']);
             }else{

                 }
         }
       }

 ?>


<!--  table display reorder_po.................................................................................................................. -->
<h2 style="font-size: 27px;margin-top: -17px;">
    View PO List
</h2>



<?php
$emp_info    = $this->Acct_mod->get_user_info($_SESSION['user_id']);
if($emp_info[0]['user_type'] == 'buyer')
{

 echo '<button type="button" class="btn btn-danger mb-3" style="margin-left: 988px;margin-top: -84px;" onclick="updated_status_po(\'Cancel\');">Cancel PO</button>';

}else if($emp_info[0]['user_type'] == 'category-head')
       {

        echo '<button type="button" class="btn btn-danger mb-3" style="margin-left: 952px;margin-top: -84px;" onclick="updated_status_po(\'Disapproved\');">Disapproved PO</button>';
        echo '<button type="button" class="btn btn-success mb-3" style="margin-left: 835px;margin-top: -117px;" onclick="updated_status_po(\'Approved\');">Approved PO</button>';

       }

?>
<div class="row"> 
  <div class="col-12 col-sm-6 col-lg-4" style="width: 100%;">
       <table  class="table table-striped table-bordered table-responsive" id="reorder_tbl" style="background-color: rgb(5, 68, 104); width: 100%;">
          <thead style="text-align: center;color:white;">
              <th>Store</th>
              <th>Vendor Code</th>
              <th>Vendor Name</th>
              <th>Document No</th>
              <th>Status</th>
              <th>Request by</th>
              <th>Remarks by</th>
              <th>Action</th>
          </thead>
          <tbody>
              
          </tbody>
      </table>
  </div>
</div>

<script>
    load_reorder_table();
    var dataTable_reorder;
    function load_reorder_table()
    {
     
      // Destroy the previous DataTable instance
      if (dataTable_reorder) {
        dataTable_reorder.destroy();
      }

      dataTable_reorder = $('#reorder_tbl').DataTable({
            "processing": true,
            "serverSide": true,
             "searching": true,
              "ordering": true,
                 "ajax" : {
                           "url" : "<?php echo base_url(); ?>PO_view_ctrl/reorder_table_view_server_side",
                           "type": "POST",
                           "data": function (d)
                                    {
                                     d.start        = d.start  || 0; // Start index of the records
                                     d.length       = d.length || 10; // Number of records per page
                                    }
                          },

              columns: [
                         { data: 'name' },
                         { data: 'vendor_code' },
                         { data: 'vendor_name' },
                         { data: 'document_number' },

                         {
                                data: 'status',
                                render: function(data, type, row) {
                                    // Conditionally assign a CSS class based on the 'status' value
                                    if (data === 'Active') {
                                    
                                        return '<span class="badge badge-success">' + data + '</span>'
                                        
                                    } else if (data === 'Cancel') {
                                     
                                        return '<span class="badge badge-danger">' + data + '</span>'

                                    }

                                    return data; // If none of the conditions match, return the original data
                                }
                         },

                         { data: 'requested_by' },
                         { data: 'remarks_by'},
                         {
                           data: 'remarks_by',
                           render: function (data, type, row) {
                                    if(data != null)
                                    {

                                        var keyword = 'Approved';

                                        if (data.includes(keyword)) {

                                        return `
                                                <input type="checkbox" class="row-checkbox" hidden />
                                              `;
                                        } else {

                                          return `
                                                 <input type="checkbox" class="row-checkbox" />
                                                `;
                                        }
                                   }
                                return `
                                        <input type="checkbox" class="row-checkbox" />
                                       `;
                           }
                         }
                      ],
             
              "paging"    : true,
              "pagingType": "full_numbers",
              "lengthMenu": [ [10, 25, 50, 1000], [10, 25, 50, "Max"] ],
              "pageLength": 10, 
          });


      }


      var table = $('#reorder_tbl').DataTable();
       function getCheckedValues() 
       {
           var checkboxes = $('.row-checkbox');
           var checkedValues = [];

           checkboxes.each(function () {
               var checkbox = $(this);
               var row = checkbox.closest('tr');
               var rowData = table.row(row).data();
               
               if (checkbox.prop('checked')) {
                   checkedValues.push(rowData);
               }
           });

           return checkedValues;
        }

        function updated_status_po(status)
        {

        
            var get_cancel_value    = getCheckedValues();
            if(get_cancel_value != null && get_cancel_value != '')
             {

                Swal.fire({
                            title: 'Confirmation',
                            html: '<div style="font-size: 16px;">Are you sure you want to update this data?</div><div style="font-size: 14px; color: red;"></div>',
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

                                                              type: 'POST',
                                                              url : '<?php echo base_url(); ?>PO_view_ctrl/updated_status_cancel',
                                                              data: { get_cancel_value: get_cancel_value,'status':status},
                                                              success: function(){
                                                                   table.ajax.reload();
                                                                   Swal.fire('Success', 'PO Has been Canceled.', 'success');  
                                                                   $("#select_move_type").val('');

                                                              }

                                                           });
                                                    }

                                               });

             }else{

                   Swal.fire('Info!', 'Please Select Data.', 'info');  

                  }
        }




</script>





