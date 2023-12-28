  <style>
    .radio-group label 
    {
      display: inline-block;
      margin-right: 10px;
    }
  </style>


  <!-- insert reorder_po -->
  <?php 

// seasonal table............................................................................ 
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



  <!--end of insert reorder_po -->


 

 <div class="row">
  	<div class="col-sm-10"></div>
    <div class="col-sm-2">      
	    <!-- <form id="view_reorder_form" method="post">
	        <input type="text" name="reorder_no_field" id="reorder_no_field" style="display: inline-block; padding: 0px; width: 10%;">
	        <button type="submit" class="btn btn-info" id="view-btn" style="padding: 1px 6px;"><i class="glyphicon glyphicon-upload"></i> Create New Report</button>
	    </form> -->
	   <!--   <a class="btn btn-primary" href="<?php echo base_url('Mms_ctrl/mms_ui/3');?>">Create New Report</a> -->
	</div>
        
  </div>
 
<?php 
         $get_user_details = $this->Mms_mod->get_user_details();
         $user_connection  = $this->Mms_mod->get_user_connection($get_user_details[0]['user_id']);


?>


  <div class="col-sm-12">
     <ul class="nav nav-tabs">
       <?php 
              if($user_connection[0]['value_'] == 'cdc')      
              {

                   echo '<li id="cdc_link" class="active"><a href="#" ><b>CDC Reorder</b></a></li>';
              }
       ?> 
            
            <li id="store_link"><a href="#"><b>STORE Reorder</b></a></li>
     </ul>
  </div> 
  <div class="row">
    <div class="col-sm-4">
           </div>
    <div class="col-sm-4" style="margin-bottom: 10px;">
          <div class="radio-group">
             <input class="status_radio" type="radio" id="pending" name="status" value="pending" checked>
             <label for="pending">Pending</label>
             
             <input class="status_radio" type="radio" id="approved" name="status" value="approved">
             <label for="approved">Approved</label>
          </div>
    </div>
    <div class="col-sm-2" id="button_div">
    </div>
    <div class="col-sm-2">
        <?php 
                 
                 if($get_user_details[0]['user_type'] == 'buyer')
                 {
                    echo  '<a class="btn btn-danger" id="view_old_btn"     onclick = "show_old_files_uploader()" href="#">Upload Old Sales</a> ';  

                 }

            ?>
    </div>
  </div>
  <div class="row">
       <div class="col-12 table-responsive" style="padding-top: 20px;">
        <table id="report-table" class="table table-striped table-bordered table-responsive" style="background-color: rgb(5, 68, 104);">
            <thead style="text-align: center;color:white;">
                <th>REPORT NO.</th>
                <th>RE-ORDER DATE</th>
                <th>VENDOR CODE</th>
                <th>VENDOR NAME</th>
                <th>BUSINESS UNIT</th> 
                <th>Status</th> 
                <th>REMARKS</th>
                <th style="width:107.778px;;text-align:center;">
                     ACTION
                     <input style="margin-left:39px;margin-bottom: 15px;" id="main_checkbox" class="checkbox" type="checkbox" name="main_checkbox" onchange="check_uncheck()"> 
                </th>
            </thead>
            <tbody>
            	<?php


                    if($get_user_details[0]['user_type'] == 'category-head')
                    {
                         $group_code = explode(',', $get_user_details[0]['group_code']);                         
                    }  
                    

                    
            		$list = $this->Mms_mod->get_entries_reorder_report_data_header_final_by_bu('');
                    $show_gen_textfile_button = false;
                    
            		foreach ($list as $item)
                    {                         
                         if($get_user_details[0]['user_type'] == 'category-head' && in_array($item['group_code_'],$group_code))
                         {
                            $show = 'yes'; 
                         }
                         else 
                         if(!empty($user_connection) )  
                         {
                             $batch_details = $this->Mms_mod->get_user_connection($item['user_id']);

                             if( 
                                 ($get_user_details[0]['user_type'] == 'buyer' && $user_connection[0]['value_'] != 'cdc'  && $batch_details[0]['value_'] != 'cdc' && in_array($item["status"], array('Pending','Approved by-buyer','Approved by-corp-buyer','Approved by-category-head'))) ||    //if ang nag login kay store buyer, ang ipakita ra kay store nga reorder ra
                                 ($get_user_details[0]['user_type'] == 'buyer' && $user_connection[0]['value_'] == 'cdc'  && $batch_details[0]['value_'] != 'cdc'  && in_array($item["status"], array('Approved by-category-head','Approved by-corp-buyer'))) || //if ang nag login kay corp buyer,  and ang mga reorder kay corb buyer and mga store buyer ang nag gama
                                 ($get_user_details[0]['user_type'] == 'buyer' && $user_connection[0]['value_'] == 'cdc'  && $batch_details[0]['value_'] == 'cdc'  && in_array($item["status"], array('Pending','Approved by-buyer','Approved by-category-head','Approved by-corp-manager','Approved by-incorporator'))) || //if ang nag login kay corp buyer, and ang mga reorder kay corp buyer nag gama 
                                 ($get_user_details[0]['user_type'] == 'category-head' &&  $user_connection[0]['value_'] != 'cdc' && $batch_details[0]['value_'] != 'cdc' && in_array($item["status"], array('Pending','Approved by-buyer','Approved by-category-head','Approved by-corp-buyer'))) 
                               ) 
                               {
                                 $show = 'yes'; 
                               }
                               else                                 
                               {
                                  $show = 'no';
                               }

                               if($get_user_details[0]['user_type'] == 'buyer'  && $user_connection[0]['user_id'] != $_SESSION['user_id'])  
                               {
                                  $show = 'no';
                               }
                         }
                         else 
                         if($get_user_details[0]['user_type'] != 'category-head')   
                         {
                            $show = 'yes'; 
                         }
                         else 
                         {
                            $show = 'no';
                         }

                		 if($show == 'yes')
                         {                


                             $doc_number     = str_pad($item["reorder_batch"], 7, '0', STR_PAD_LEFT);
                             $reord_number   = 'MMSR-'.strtoupper($item['value_']).'-'.$doc_number;

                        	echo "<tr>";
                			echo "<td>".$reord_number."</td>";
                			echo "<td>".date('M d, Y h:i A',strtotime($item["date_generated"]))."</td>";
                			echo "<td>".$item["supplier_code"]."</td>";
                			echo "<td>".$item["supplier_name"]."</td>";
                			echo "<td>".$item["store"]."</td>";
                            $po_calendar        = $this->Mms_mod->get_po_calendar($item['supplier_code']);
                            $current_user_login =  $this->Mms_mod->get_user_connection($_SESSION['user_id']);
                            if( $get_user_details[0]['user_type'] == 'buyer' && in_array($item["status"], array('Approved by-corp-manager','Approved by-incorporator','Approved by-corp-buyer','Approved by-category-head')) )
                            {

                                 if(  
                                      ($po_calendar[0]['approver'] == 'Category-Head' && $item["status"] == 'Approved by-category-head')  || 
                                      ($po_calendar[0]['approver'] == 'Corp-Manager' && in_array($item["status"],array('Approved by-corp-manager','Approved by-incorporator')) )     ||
                                      ($current_user_login[0]['value_'] == 'cdc')
                                   )
                                   {
                                         $show_gen_textfile_checkbox = true;
                                   }
                                   else                 
                                   {         
                                         $show_gen_textfile_checkbox = false;            
                                   }



                                   if($item['value_'] != 'cdc' && in_array($item["status"],array('Approved by-category-head')))
                                   {
                                         $show_gen_textfile_checkbox = false;                        
                                   }
                                   else 
                                   if($item['value_'] != 'cdc' && in_array($item["status"],array('Approved by-corp-buyer')))
                                   {
                                         $show_gen_textfile_checkbox = true;            
                                   }


                            }  

                            $status = "Pending";
                            if( in_array($item["status"], array('Approved by-corp-manager','Approved by-incorporator','Approved by-corp-buyer','Approved by-category-head')) )
                            {                                            
                                 if(  
                                      ($po_calendar[0]['approver'] == 'Category-Head' && $item["status"] == 'Approved by-category-head' && $item['value_'] == 'cdc')  || 
                                      ($po_calendar[0]['approver'] == 'Corp-Manager' && in_array($item["status"],array('Approved by-corp-manager','Approved by-incorporator'))  && $item['value_'] == 'cdc' )     ||
                                      ($item['value_'] != 'cdc' && $item["status"] == 'Approved by-corp-buyer') 
                                      
                                   )
                                   {
                                         $status = "Approved";
                                   }                                 
                                                                 
                             }
                            
                			echo "<td>".$status."</td>";
                            echo "<td>".$item["status"]."</td>";
                            echo '<td>
                                        <a class="btn btn-primary view" style="margin-left:39px;" href="'.base_url("Mms_ctrl/mms_ui/4?r_no=".$item["reorder_batch"]).'">VIEW</a>';
                            if( $get_user_details[0]['user_type'] == 'buyer' && in_array($item["status"], array('Approved by-corp-manager','Approved by-incorporator','Approved by-corp-buyer','Approved by-category-head')) )
                            {          
                                   if($show_gen_textfile_checkbox )
                                   {

                                         if(strstr(strtoupper($po_calendar[0]['vend_type']),'SI'))
                                         {
                                             $nav_si_doc_no =  $item['nav_si_doc_no'];
                                             $si_input      = "";
                                             $show_si       = '';
                                             if(in_array($nav_si_doc_no,array(null,'')))
                                             {
                                                 $disable_si = '';                                                
                                             }
                                             else 
                                             {
                                                 $disable_si = 'disabled';
                                             }
                                         }
                                         else 
                                         {
                                             $disable_si = '';
                                             $nav_si_doc_no = '';
                                             $show_si       = 'hidden';
                                         }

                                         
                                         if(strstr(strtoupper($po_calendar[0]['vend_type']),'DR'))
                                         {
                                             $nav_dr_doc_no =  $item['nav_dr_doc_no'];
                                             $show_dr       = '';
                                             if(in_array($nav_dr_doc_no,array(null,'')))
                                             {
                                                 $disable_dr = '';                                                
                                             }
                                             else 
                                             {
                                                 $disable_dr = 'disabled';
                                             }

                                         }
                                         else 
                                         {
                                             $disable_dr = '';         
                                             $nav_dr_doc_no = '';
                                             $show_dr       = 'hidden';
                                         }

                                         echo '   <br>
                                                  <input type="text" id="vend_type_'.$item["reorder_batch"].'" value="'.$po_calendar[0]['vend_type'].'" hidden>
                                                  <label for="si_input_'.$item["reorder_batch"].'" '.$show_si.'>SI</label><input type="text" id="si_input_'.$item["reorder_batch"].'"  value="'.$nav_si_doc_no.'"  '.$show_si.'  '.$disable_si.'>
                                                  <label for="dr_input_'.$item["reorder_batch"].'" '.$show_dr.'>DR</label><input type="text" id="dr_input_'.$item["reorder_batch"].'"  value="'.$nav_dr_doc_no.'"  '.$show_dr.'  '.$disable_dr.'>
                                               ';


                            			 echo '
                                                  
                                                   <input style=""  id="textfile_qty_checkbox-'.$item["reorder_batch"].'"  onchange="check_uncheck_main('."'#textfile_qty_checkbox-".$item["reorder_batch"]."','".$item["reorder_batch"]."'".')"   class="checkbox_textfile_qty" type="checkbox" name="checkbox" value="'.$item["reorder_batch"].'">
                                              ';                            
                                        $show_gen_textfile_button = true;     
                                   }
                            }     

                            echo '</td>';
                			echo "</tr>";	


                         }  
            		}


            	?>
            </tbody>
        </table>
    </div>     
  </div>


 <script> 

    <?php
             if($show_gen_textfile_button)
             {
                 echo '
                           var button =  '."'".'<a class="btn btn-success" style="margin-left:39px;"  href="#" onclick="generate_textfile()">Generate Textfile</a>'."'".';
                           $("#button_div").html(button);
                       ';
             }
             else 
             {
                echo '$("#main_checkbox").hide();' ;
             }
     ?>


  window.io = 
            {
                open: function(verb, url, data, target){
                    var form = document.createElement("form");
                    form.action = url;
                    form.method = verb;
                    form.target = target || "_self";
                    if (data) {
                        for (var key in data) {
                            var input = document.createElement("textarea");
                            input.name = key;
                            input.value = typeof data[key] === "object"
                                ? JSON.stringify(data[key])
                                : data[key];
                            form.appendChild(input);
                        }

                    }
                    form.style.display = 'none';
                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                }
            };  


   var reportTable = $("#report-table").DataTable({ "ordering": false});

   function swal_display(icon,title,text)
   {
           Swal.fire({
                           icon: icon,
                           title:title,
                           html: text                                  
                       });    
   }


  function generate_textfile() 
  {
      var checked = 0;
      reportTable.column(7).nodes().to$().find('input[type="checkbox"]:checked').each(function() 
      {
         checked +=1;
      });

      if(checked == 0)
      {
             swal_display('error','opps','please select reorder');   
      }
      else 
      {

         Swal.fire({
                       title: 'Are you sure',
                       text: "You want to generate textfile?",
                       icon: 'warning',
                       showCancelButton: true,
                       confirmButtonColor: '#3085d6',
                       cancelButtonColor: '#d33',
                       confirmButtonText: 'Yes'
                    }).then((result) => 
                    { 

                         if(result.isConfirmed) 
                         {         
                                  var counter_2 = 0    
                                  reportTable.column(7).nodes().to$().find('input[type="checkbox"]:checked').each(function() 
                                  {
                                       var reorder_batch = $(this).val();
                                       reportTable.column(7).nodes().to$().find('input[type="text"]#vend_type_'+reorder_batch).each(function() 
                                       {
                                             var vend_type = $(this).val();                    
                                             reportTable.column(7).nodes().to$().find('input[type="text"]#si_input_'+reorder_batch).each(function() 
                                             {
                                                 var nav_si_doc_no = $(this).val(); 
                                                 reportTable.column(7).nodes().to$().find('input[type="text"]#dr_input_'+reorder_batch).each(function() 
                                                 {
                                                     var nav_dr_doc_no     = $(this).val();
                                                     var vend_exp          = vend_type.split(',');
                                                     var reorder_batch_arr = [];
                                                     reorder_batch_arr.push(reorder_batch);

                                                     for(var a=0;a<vend_exp.length;a++)                           
                                                     {   
                                                         io.open("POST", "<?php echo base_url();?>Mms_ctrl/generate_textfile", 
                                                         {                               
                                                           'reorder_batch_arr':JSON.stringify(reorder_batch_arr),
                                                           'vend_type':vend_exp[a],                                   
                                                           'si_input':nav_si_doc_no,
                                                           'dr_input':nav_dr_doc_no
                                                         },"_blank");  
                                                     }

                                                     console.log('reorder_batch--->'+reorder_batch+' vend_type--->'+vend_type+"  nav_si_doc_no--->"+nav_si_doc_no+" nav_dr_doc_no---->"+nav_dr_doc_no+'\n'); 
                                                 });
                                             });   
                                       });
                                       counter_2+=1;

                                       if(counter_2 == checked)
                                       {
                                            location.reload();  
                                       }
                                  });
                         }

                    });   

      }


          // // Get all the checked checkboxes from all pages of the DataTable
          // var reorder_batch_arr  = [];          
          // reportTable.rows().nodes().each(function() 
          // {
          //   const checkboxes = $(this).find('td:eq(7) input[type="checkbox"]:checked');
          //   checkboxes.each(function()
          //   {
          //       if(!reorder_batch_arr.includes($(this).val()))
          //       {
          //            reorder_batch_arr.push($(this).val());    
          //       }
          //   });
          // });    


          // Swal.fire({
          //              title: 'Are you sure',
          //              text: "You want to generate textfile?",
          //              icon: 'warning',
          //              showCancelButton: true,
          //              confirmButtonColor: '#3085d6',
          //              cancelButtonColor: '#d33',
          //              confirmButtonText: 'Yes'
          //           }).then((result) => 
          //           { 

          //                if(result.isConfirmed) 
          //                {                             
          //                    io.open("POST", "<?php echo base_url();?>Mms_ctrl/generate_textfile", 
          //                    {                                 
          //                        'reorder_batch_arr':JSON.stringify(reorder_batch_arr)
          //                    },"_blank");  
          //                }

          //           });               
   }

 


   function show_old_files_uploader()
   {
        io.open("POST", "<?php echo base_url();?>Mms_ctrl/mms_ui/3", 
                         {                               
                               "vendor_code":'UPLOAD OLD SALES',
                               "vendor_name":'',
                               "date_tag":'',
                               "group_code":''
                         },"_self");  
   }



   //  function generate_textfile()         
   // {
   //       // Get all the checked checkboxes using jQuery
   //        const checkedCheckboxes = $('input[type=checkbox]:checked').map(function() 
   //        {
   //          return this.value;
   //        }).get();

   //        console.log(checkedCheckboxes);

   //        // var reorder_batch_arr  = [];
   //        // reorder_batch_arr.push(reorder_batch);

   //        // Swal.fire({
   //        //              title: 'Are you sure',
   //        //              text: "You want to generate textfile?",
   //        //              icon: 'warning',
   //        //              showCancelButton: true,
   //        //              confirmButtonColor: '#3085d6',
   //        //              cancelButtonColor: '#d33',
   //        //              confirmButtonText: 'Yes'
   //        //           }).then((result) => 
   //        //           { 
   //        //                io.open("POST", "<?php echo base_url();?>Mms_ctrl/generate_textfile", 
   //        //                {                               
   //        //                     'reorder_batch_arr':JSON.stringify(reorder_batch_arr)
   //        //                },"_blank");  

   //        //           });            
   // }


 	
 	// $(function(){
 	// 	$("#view_reorder_form").submit(function(e){
 	// 		console.log("Yes");
 	// 		e.preventDefault();
 	// 		$.ajax({
     //           type:'POST',
     //           data: $(this).serialize(),
     //           url:'<?php echo base_url(); ?>Mms_ctrl/view_reorder_report',
     //           success: function(data)
     //           {
     //           	var json_obj = JSON.parse(data);
     //                console.log(json_obj);
     //                var header = json_obj.header;
     //                var lines = json_obj.lines;

     //                if(header!=null){
     //                	$("#r_date_span").html(header.reorder_date);
     //                	$("#s_code_span").html(header.supplier_code);
     //                	$("#s_name_span").html(header.supplier_name);
     //                	$("#r_no_span").html(header.reorder_number);
     //              		$("#lead_time_span").html(header.lead_time_factor);
     //                }

     //                if(lines.length>0){
     //                	//populateTable(lines);
     //                }
                    
     //           }
     //        });
 	// 	});
 	// });

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


    function loader()
    {
              
              Swal.fire({
                            imageUrl: '<?php echo base_url(); ?>assets/mms/images/Cube-1s-200px.svg',
                            imageHeight: 203,
                            imageAlt: 'loading',
                            text: 'loading, please wait',
                            allowOutsideClick:false,
                            showCancelButton: false,
                            showConfirmButton: false
                          })              
    } 

    // Add click event listener to all elements with class "view"
    $(".view").on("click", function(event) {
      // Prevent the default behavior of the link
      event.preventDefault();

      // Your custom function here
      // For example, displaying a loader
      loader();
      
      // Get the href attribute of the clicked element
      const href = $(this).attr("href");
      
      // Redirect to the href URL after a short delay
      setTimeout(function() {
        window.location.href = href;
      }, 1000);
    });


    function approve_batch(reorder_batch)
    {
          Swal.fire({
                          title: 'Are you sure',
                          text: "You want to approve this reorder?",
                          icon: 'warning',
                          showCancelButton: true,
                          confirmButtonColor: '#3085d6',
                          cancelButtonColor: '#d33',
                          confirmButtonText: 'Yes'
                    }).then((result) => 
                    { 
                         if(result.isConfirmed) 
                         {
                             $.ajax({
                                        type:'POST',
                                        url:'<?php echo base_url(); ?>Mms_ctrl/approve_disapprove_batch',
                                        data:{
                                                'reorder_batch':reorder_batch,
                                                'status':'Approved'
                                             },
                                        dataType:'JSON',
                                        success: function(data)
                                        {
                                                 Swal.fire({
                                                                                 position: 'center',
                                                                                 icon: 'success',
                                                                                 title: 'successfully approved',
                                                                                 showConfirmButton: true                                           
                                                                            })  
                                                setTimeout(function() 
                                                {
                                                     location.reload();                                                     
                                                }, 2000);
                                        }     
                                    });


                         }
                    });   
    }



    function disapprove_batch(reorder_batch)
    {
         Swal.fire({
                          title: 'Are you sure',
                          text: "You want to disapprove this reorder?",
                          icon: 'warning',
                          showCancelButton: true,
                          confirmButtonColor: '#3085d6',
                          cancelButtonColor: '#d33',
                          confirmButtonText: 'Yes'
                    }).then((result) => 
                    { 
                         if(result.isConfirmed) 
                         {

                              $.ajax({
                                        type:'POST',
                                        url:'<?php echo base_url(); ?>Mms_ctrl/approve_disapprove_batch',
                                        data:{
                                                'reorder_batch':reorder_batch,
                                                'status':'Disapproved'
                                             },
                                        dataType:'JSON',
                                        success: function(data)
                                        {
                                                 Swal.fire({
                                                                                 position: 'center',
                                                                                 icon: 'success',
                                                                                 title: 'successfully disapproved',
                                                                                 showConfirmButton: true                                           
                                                                            })  
                                                 location.reload();
                                                 setTimeout(function() 
                                                 {
                                                     location.reload();                                                     
                                                 }, 2000);
                                        }     
                                    });                              
                         }
                    });   
    }



    function check_uncheck()
    {
        var table_id = 'report-table';

        if ($('#main_checkbox').is(':checked')) 
        {
            // Checkbox is checked
            console.log('Checkbox is checked');
            toggle_checkboxes(table_id, true);
            // Perform any actions for checked state
        }
        else
        {
            // Checkbox is unchecked
            console.log('Checkbox is unchecked');
            toggle_checkboxes(table_id, false);
            // Perform any actions for unchecked state
        }       
    }


    function toggle_checkboxes(table_id, is_checked) 
    {
          var table = $('#' + table_id).DataTable();
          table.column(7).nodes().to$().find('.checkbox_textfile_qty').each(function() 
          {
              var checkboxValue   = $(this).val();
              $(this).prop('checked', is_checked);
              console.log(checkboxValue);
          });
    }

    function check_uncheck_main(table_id,reoder_id)
    {
        if ($(table_id).is(':checked')) 
        {

        }
        else 
        {
             $('#main_checkbox').prop('checked', false);
        }
    }



    // // Add an onchange event handler using jQuery
    // $('.status_radio').change(function() 
    // {
    //     if ($(this).is(':checked')) 
    //     {
    //       console.log("Selected: " + $(this).val());
    //       // You can perform any action you want with the selected value

    //     }
    // });





    var dataTable = $('#report-table').DataTable();
    dataTable.column(5).search('Pending').draw();

    $('.status_radio').change(function() 
    {
    if ($(this).is(':checked')) {
        var selectedValue = $(this).val();
        
        // Access the DataTable instance
        
        // Clear any previous filters
        dataTable.search('').draw();

        // Apply custom filtering based on selected value
        if (selectedValue === 'pending') 
        {
            // Hide rows with 'Approved' status
            dataTable.column(5).search('Pending').draw();
        } 
        else if (selectedValue === 'approved') 
        {
            // Clear any existing column filters and redraw
            dataTable.column(5).search('Approved').draw();
        }
        
        // You can perform any additional actions with the selected value
        console.log("Selected: " + selectedValue);
    }
});

<?php 
 if($user_connection[0]['value_'] == 'cdc')      
 {     
     echo 'dataTable.column(4).search("cdc").draw();
           $("#cdc_link").addClass("active");';
 }
 else 
 {
     echo "dataTable.column(4).search('^(?!cdc$).*$', true, false).draw();
           $('#store_link').addClass('active');";
 }
 

?>

$(document).ready(function() 
{
    // Attach a click event handler to the list items
    $("ul.nav li").click(function()
    {
        // Remove the "active" class from all list items
        $("ul.nav li").removeClass("active");
        
        // Add the "active" class to the clicked list item
        $(this).addClass("active");

        // Get the ID of the clicked list item and display it
        var clickedId = $(this).attr("id");
        if(clickedId == 'cdc_link')
        {
             dataTable.column(4).search('cdc').draw();
        }
        else 
        {
             // If the clicked item has a different ID, perform a negation search for 'cdc'
             dataTable.column(4).search('^(?!cdc$).*$', true, false).draw();
        }

        console.log("Clicked ID: " + clickedId);
    });
});



 </script>