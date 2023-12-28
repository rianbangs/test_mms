  
<style>
        .custom-width-modal {
                                width: 1168px; /* Adjust the value as per your requirements */
                            }
              


                               
</style>
  <!-- modal here----------------------------------------------------------------------------------------- -->
              <div class="modal fade text-left" id="report_modal" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-scrollable modal-xl custom-width-modal" role="document">
                           <div class="modal-content">
                                 <div class="modal-header">
                                     <h4 class="modal-title" id="report_label"> </h4>
                                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <i data-feather="x"></i>
                                     </button>
                                 </div>
                                 <div class="modal-body" >
                                        <div id="tab">
                                        </div>
                                        <div id="report_body" style=" max-width: 2500px;overflow-x: auto;">
                                        </div>                                         
                                 </div>
                                 <div class="modal-footer" id ="footer_modal">

                                        <!-- <button type="button" class="btn btn-danger" data-dismiss="modal">
                                            <i class="bx bx-x d-block d-sm-none"></i>
                                            <span class="d-none d-sm-block ">Close</span>
                                        </button> -->
                                     <!--    <button type="button" class="btn btn-primary ml-1" data-dismiss="modal">
                                            <i class="bx bx-check d-block d-sm-none"></i>
                                            <span class="d-none d-sm-block">Accept</span>
                                        </button> -->
                                  </div>
                           </div>
                     </div>
            </div>


<!-- end of modal here----------------------------------------------------------------------------------------- -->



<?php 
	$r_no = $_GET["r_no"]; 
	$head = $this->Mms_mod->get_entries_reorder_report_data_header_final($r_no);   
    $vendor_details             = $this->Mms_mod->get_po_calendar($head["supplier_code"]);
    $current_login_user_details = $this->Mms_mod->get_user_details();
    $current_user_login         = $this->Mms_mod->get_user_connection($_SESSION['user_id']);
    $store_handled              = $this->Mms_mod->get_entries_reorder_report_data_header_final_by_bu($r_no);
    $store                      = ($store_handled[0]['value_']);
    $header_details             = $this->Mms_mod->get_reorder_report_data_header_final_details($r_no);
    $report_details             = $this->Mms_mod->generate_reorder_report_mod($r_no,$store,$store_handled[0]['user_id']);
    $po_calendar                = $this->Mms_mod->get_po_calendar($header_details[0]['supplier_code']);
?>

<div class="row" style="padding-top: 15px;">

  	<div class="col-sm-2">
  		<a class="btn btn-primary" href="<?php echo base_url('Mms_ctrl/mms_ui/2');?>">Reorder Report</a>        
  	</div>
	<div class="col-sm-2">	
		<strong>
                <p>Reorder Date<br>(PO CALENDAR)</p>
		        <br>
    		    <p>Supplier Code</p>
    		    <p>Supplier Name</p>
                <p>Date Generated</p>
        </strong>
	</div>
	<div class="col-sm-3">        
		<p><span id="r_date_span"><?php echo date('M d, Y',strtotime($head["date_tag"]));?></span></p>
		<br><br>
		<p><span id="s_code_span"><?php echo $head["supplier_code"];?></span></p>
		<p><span id="s_name_span"><?php echo $head["supplier_name"];?></span></p>
        <p><?php echo date('M d, Y -- h:i A',strtotime($head['date_generated']) ) ?> </sp>
	</div>
	<div class="col-sm-2">
        <strong>  	
    		<p>Reorder Number</p>
    		<br>
    		<p>Lead Time Factor</p>
            <p>Reorder Date (HTML)</p>
            <p>Status</p>
        </strong>    
	</div>
	<div class="col-sm-3">  	
<?php 
    $doc_number     = str_pad($head["reorder_batch"], 7, '0', STR_PAD_LEFT);
    $reord_number   = 'MMSR-'.strtoupper($head['value_']).'-'.$doc_number;

?>

		<p><span id="r_no_span"><?php echo $reord_number;?></span></p>
		<br>
		<p><span id="lead_time_span"><?php echo $head["lead_time_factor"];?></span></p>         
        <p><span id="lead_time_span"><?php echo date('M d, Y',strtotime($head["reorder_date"]));?></span></p>
        <?php
                if($head['status'] == 'Pending')
                {
                      $stats_style = 'color:red;';
                }
                else 
                if( 
                    ($po_calendar[0]['approver'] == 'Category-Head' && $head["status"] == 'Approved by-category-head')  || 
                    ($po_calendar[0]['approver'] == 'Corp-Manager' && in_array($head["status"],array('Approved by-corp-manager','Approved by-incorporator')) )                                       
                  )    
                  {
                      $stats_style = 'color:green;';                    
                  }
                  else 
                  {
                      $stats_style = 'color:blue;';                                        
                  }
                echo '<p><span style="'.$stats_style.'" id="lead_time_span"><strong>'.$head['status'].'</strong></span></p>';
         ?>
	</div>   
</div> 


  <?php
   


  	//$list = $this->Mms_mod->get_entries_reorder_report_data_lines_final($r_no);

    // $current_login_user_details = $this->Mms_mod->get_user_details();
    // $current_user_login         = $this->Mms_mod->get_user_connection($_SESSION['user_id']);
  	// $store_handled              = $this->Mms_mod->get_entries_reorder_report_data_header_final_by_bu($r_no);
    // $header_details             = $this->Mms_mod->get_reorder_report_data_header_final_details($r_no);
  	// $store                      = ($store_handled[0]['value_']);

    $non_cdc        = 0;
    $cdc            = 0;
    $store_type     = 0;
    $non_store_type = 0;
    foreach($header_details as $header)
    {
         if($header['store'] != 'cdc')
         {              
              $non_cdc +=1;
              if($header['bu_type'] == 'NON STORE')
              {
                 $non_store_type += 1;
              }
              else 
              if($header['bu_type'] == 'STORE')                
              {
                $store_type += 1;
              }

         }
         else 
         {
            $cdc +=1;
         }
    }
    

  	// $report_details             = $this->Mms_mod->generate_reorder_report_mod($r_no,$store,$store_handled[0]['user_id']);

    // $po_calendar                = $this->Mms_mod->get_po_calendar($header_details[0]['supplier_code']);


    if(($current_login_user_details[0]['user_type'] == 'category-head' && $head["status"] == 'Approved by-buyer')  || ($current_login_user_details[0]['user_type'] == 'corp-manager' && $head["status"] == 'Approved by-category-head')) 
    {
         echo  ' <div class="row">
                    <div class="col-sm-6">
                    </div>
                    <div class="col-sm-6">
                        ';

        if($current_login_user_details[0]['user_type'] == 'corp-manager' && $head["status"] == 'Approved by-category-head')
        {
                            // echo '<button type="button" class="btn btn-info" data-dismiss="modal"  style="margin-left: 128px;" onclick="approve_batch('."'".$head["reorder_batch"]."','".$current_login_user_details[0]['user_type']."','to incorp'".')">
                            //     <i class="bx bx-x d-block d-sm-none"></i>
                            //     <span class="d-none d-sm-block ">Forward to Incorporator</span>
                            // </button>';
        }           

         echo '             <button type="button" class="btn btn-success" data-dismiss="modal" style="margin-left: 35px;" onclick="approve_batch('."'".$head["reorder_batch"]."','".$current_login_user_details[0]['user_type']."','approval','".$vendor_details[0]['vend_type'].'\')">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block ">approve</span>
                            </button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal"  style="margin-left: 35px;" onclick="disapprove_batch('."'".$head["reorder_batch"]."'".')">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block ">disapprove</span>
                            </button>
                      </div>
                </div>  
                 '; 
    }
    else    
    if($current_login_user_details[0]['user_type'] == 'buyer' && in_array($head["status"] ,array('Pending','pending'))) 
    { 

          echo  ' <div class="row">
                    <div class="col-sm-7">
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-success" data-dismiss="modal" style="margin-left: 128px;" onclick="approve_batch('."'".$head["reorder_batch"]."','buyer'".')">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block ">Proceed Batch</span>
                        </button>                        
                    </div>
                    <div class="col-sm-2">
                         <button type="button" class="btn btn-danger" data-dismiss="modal" style="margin-left: 128px;" onclick="cancel_batch('."'".$head["reorder_batch"]."','buyer'".')">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block ">Cancel Reorder</span>
                        </button>  
                    </div>
                  </div>  
                 ';  
    }
    else 
    if($current_login_user_details[0]['user_type'] == 'incorporator' && $head["status"] == 'Forward to-Incorporator')         
    {
         echo '<button type="button" class="btn btn-info" data-dismiss="modal"  style="margin-left: 1175px;" onclick="approve_batch('."'".$head["reorder_batch"]."','".$current_login_user_details[0]['user_type']."','incorporator'".')">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block ">Approve Reorder</span>
                            </button>';
    }
    else 
    if($head['value_'] != 'cdc' &&  $current_login_user_details[0]['user_type'] == 'buyer' && $current_user_login[0]['value_'] == 'cdc' && in_array($head["status"], array('Approved by-category-head')))    
    {
        echo '<button type="button" class="btn btn-info" data-dismiss="modal"  style="margin-left: 1135px;" onclick="approve_batch('."'".$head["reorder_batch"]."','".$current_login_user_details[0]['user_type']."','corp-buyer'".')">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block ">Approve Store Reorder</span>
                            </button>';
    }
    else     
    if( $current_login_user_details[0]['user_type'] == 'buyer' && in_array($head["status"], array('Approved by-corp-manager','Approved by-incorporator','Approved by-corp-buyer','Approved by-category-head')) )    
      {
         if(  
              ($po_calendar[0]['approver'] == 'Category-Head' && $head["status"] == 'Approved by-category-head')  || 
              ($po_calendar[0]['approver'] == 'Corp-Manager' && in_array($head["status"],array('Approved by-corp-manager','Approved by-incorporator')) )     ||
              ($current_user_login[0]['value_'] == 'cdc')
           )
           {
                 $show_gen_textfile_button = true;
           }
           else                 
           {         
                 $show_gen_textfile_button = false;            
           }



           if($current_user_login[0]['value_'] != 'cdc' && in_array($head["status"],array('Approved by-category-head')))
           {
                 $show_gen_textfile_button = false;                        
           }
           else 
           if($current_user_login[0]['value_'] != 'cdc' && in_array($head["status"],array('Approved by-corp-buyer')))
           {
                 $show_gen_textfile_button = true;            
           }


           if($show_gen_textfile_button )
           {
              // if(strstr(strtoupper($vendor_details[0]['vend_type']),"SI") && strstr(strtoupper($vendor_details[0]['vend_type']),"DR"))
              // {
              //    $show_dr = "";

              // }
              // else 
              // {
              //    $show_dr = "hidden";
              // }


              // if(in_array($head['nav_si_doc_no'],array(null,'')))
              // {
              //    $si_input = "";
              //    $si_editable = "";
              // }
              // else 
              // {
              //    $si_input = $head['nav_si_doc_no'];
              //    $si_editable = "disabled";
              // }


              // if(in_array($head['nav_dr_doc_no'],array(null,'')))
              // {
              //    $dr_input = "";
              //    $dr_editable = "";
              // }
              // else 
              // {
              //    $dr_input = $head['nav_dr_doc_no'];
              //    $dr_editable = "disabled";
              // }


              


              if(strstr(strtoupper($po_calendar[0]['vend_type']),'SI'))
              {
                 $nav_si_doc_no =  $head['nav_si_doc_no'];
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
                 $nav_si_doc_no = '';
                 $show_si       = 'hidden';
                 $disable_si = 'disabled';
              }

                                         
              if(strstr(strtoupper($po_calendar[0]['vend_type']),'DR'))
              {
                    $nav_dr_doc_no =  $head['nav_dr_doc_no'];
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
                    $nav_dr_doc_no = '';
                    $show_dr       = 'hidden';
                    $disable_dr = 'disabled';
              }





               echo '
                     <button type="button" class="btn btn-info" data-dismiss="modal"  style="margin-left: 775px;" onclick="generate_textfile('."'".$head["reorder_batch"]."',".'\''.$vendor_details[0]['vend_type'].'\')">
                                        <i class="bx bx-x d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block ">Generate Textfile</span>
                     </button>

                     <label for="nav_si_doc_no" '.$show_si.'>SI</label><input type="text" id="nav_si_doc_no"  value="'.$nav_si_doc_no.'"  '.$show_si.'  '.$disable_si.'>
                     <label for="nav_dr_doc_no" '.$show_dr.'>DR</label><input type="text" id="nav_dr_doc_no"  value="'.$nav_dr_doc_no.'"  '.$show_dr.'  '.$disable_dr.'>
                     ';
           }
      }




     
    //$report_details = $this->Mms_mod->generate_reorder_report_mod($r_no,$store);

     // get the past 3 month years from the current month
     $past_3_month_years = array();
     for($i = 1; $i <= 3; $i++) 
     {
       $past_month_year = date('Y-m', strtotime("-{$i} month", strtotime($store_handled[0]['date_generated'])));
       $past_3_month_years[] = $past_month_year;
     }

    
 	
     

   	$month1 = $report_details[0]["month_1"];
	$month2 = $report_details[0]["month_2"];
	$month3 = $report_details[0]["month_3"];

  	$user_details = $this->Mms_mod->get_user_details(); 

    $letter_color = 'color:yellow;'; 
    $formulas     = array();
    if($store_handled[0]['value_'] == 'cdc')
    {                            

          if($non_cdc == 0) //if cdc ra ang giupload nga reorder walay ubang store
          {
                $letter                = 'D'; 
                $min_level_letter      = 'E'; 
                $qty_on_hand_letter    = 'F';
                $all_qty_letter        = 'G';
                $pending_qty_letter    = 'G';
                $suggested_qty_letter  = 'E - ( F + G )'; 
                $suggested_qty_letter2 = 'H';
          } 
          else 
          if($store_type == 0 && $non_store_type > 0) //if walay store gi checkan mga none store ra
          {
                $letter                = 'E'; 
                $min_level_letter      = 'G'; 
                $qty_on_hand_letter    = 'H';
                $all_qty_letter        = 'I';
                $pending_qty_letter    = 'I';
                $suggested_qty_letter  = 'G - ( '.$qty_on_hand_letter.' + '.$pending_qty_letter.' )'; 
                $suggested_qty_letter2 = 'J';
          }
          else     
          {
                $letter             = 'E'; 
                $min_level_letter   = 'G'; 
                $qty_on_hand_letter = 'H';
                $all_qty_letter     = 'I';
                $pending_qty_letter = 'J';
                $suggested_qty_letter  = 'G - ( I + J )'; 
                $suggested_qty_letter2 = 'K';
          }

          $all_ave_sales          = '<em style="'.$letter_color.'">F</em> &nbsp;&nbsp; <a href="#" class="eye-icon" onclick="view_all_stores_involve('."'".$report_details[0]['reorder_batch']."','consolidated_ave_sales'".')"><i class="fas fa-eye" style="color:white;"></i></a> ';                               
          $conso_ave_sales        = '<em style="'.$letter_color.'">E</em> &nbsp;&nbsp; <a href="#" class="eye-icon" onclick="view_all_stores_involve('."'".$report_details[0]['reorder_batch']."','all_store_ave_sales'".')"><i class="fas fa-eye" style="color:white;"></i></a> ';          
          $min_level       = '<em style="'.$letter_color.'">'.$min_level_letter.'</em><em> = ( '.$letter.' * Lead Time Factor )</em>';
          $qty_onhand      = '<em style="'.$letter_color.'">'.$qty_on_hand_letter.'</em>';
          $all_qty         = '<em style="'.$letter_color.'"></em> &nbsp;&nbsp; <a href="#" class="eye-icon" onclick="view_all_stores_qty_onhand('."'".$report_details[0]['reorder_batch']."','all_store_qty_onhand'".')"><i class="fas fa-eye" style="color:white;"></i></a>';
          $conso_all_qty   = '<em style="'.$letter_color.'">'.$all_qty_letter.'</em> &nbsp;&nbsp;<a href="#" class="eye-icon" onclick="view_all_stores_qty_onhand('."'".$report_details[0]['reorder_batch']."','consolidated_qty_onhand'".')"><i class="fas fa-eye" style="color:white;"></i></a>';
          $pending_qty     = '<em style="'.$letter_color.'">'.$pending_qty_letter.'</em>';
          $suggested_qty   = '<em style="'.$letter_color.'">'.$suggested_qty_letter2.'</em> <em> =  '.$suggested_qty_letter.'  </em>';
    }  
    else 
    { 

          // $min_level       = '<em style="'.$letter_color.'">'.$min_level.'</em><em> = ( D * Lead Time Factor )</em>';
          // $all_ave_sales   = '';
          // $conso_ave_sales = '';
          // $qty_onhand      = '<em style="'.$letter_color.'">'.$qty_on_hand_letter.'</em>';
          // $all_qty         = '<em style="'.$letter_color.'">'.$all_qty_letter.'</em>';
          // $pending_qty     = '<em style="'.$letter_color.'">'.$pending_qty_letter.'</em>';
          // $suggested_qty   = '<em style="'.$letter_color.'">I</em> <em> = ( E - F - H ) </em>';

        if(
              ($non_cdc == 0)  ||
              ($head['value_'] != 'cdc' && $cdc == 0)
          ) //if cdc ra ang giupload nga reorder walay ubang store
          {
                $letter             = 'D'; 
                $min_level_letter   = 'E'; 
                $qty_on_hand_letter = 'F';
                $all_qty_letter     = 'F';
                $pending_qty_letter = 'G';

                $suggested_qty_letter  = $min_level_letter.' - ( '.$all_qty_letter.' + '.$pending_qty_letter.' )'; 
                $suggested_qty_letter2 = 'H';
          } 
          else 
          {
                $letter                = 'D'; 
                $min_level_letter      = 'G'; 
                $qty_on_hand_letter    = 'H';
                $all_qty_letter        = 'I';
                $pending_qty_letter    = 'J';

                $suggested_qty_letter  = $min_level_letter.' - ( '.$all_qty_letter.' + '.$pending_qty_letter.' )'; 
                $suggested_qty_letter2 = 'K';
          }

          $all_ave_sales          = '<em style="'.$letter_color.'">F</em> &nbsp;&nbsp; <a href="#" class="eye-icon" onclick="view_all_stores_involve('."'".$report_details[0]['reorder_batch']."','consolidated_ave_sales'".')"><i class="fas fa-eye" style="color:white;"></i></a> ';                               
          $conso_ave_sales        = '<em style="'.$letter_color.'">E</em> &nbsp;&nbsp; <a href="#" class="eye-icon" onclick="view_all_stores_involve('."'".$report_details[0]['reorder_batch']."','all_store_ave_sales'".')"><i class="fas fa-eye" style="color:white;"></i></a> ';          
          $min_level       = '<em style="'.$letter_color.'">'.$min_level_letter.'</em><em> = ( '.$letter.' * Lead Time Factor )</em>';
          $qty_onhand      = '<em style="'.$letter_color.'">'.$qty_on_hand_letter.'</em>';
          $all_qty         = '<em style="'.$letter_color.'"></em> &nbsp;&nbsp; <a href="#" class="eye-icon" onclick="view_all_stores_qty_onhand('."'".$report_details[0]['reorder_batch']."','all_store_qty_onhand'".')"><i class="fas fa-eye" style="color:white;"></i></a>';
          $conso_all_qty   = '<em style="'.$letter_color.'">'.$all_qty_letter.'</em> &nbsp;&nbsp;<a href="#" class="eye-icon" onclick="view_all_stores_qty_onhand('."'".$report_details[0]['reorder_batch']."','consolidated_qty_onhand'".')"><i class="fas fa-eye" style="color:white;"></i></a>';
          $pending_qty     = '<em style="'.$letter_color.'">'.$pending_qty_letter.'</em>';
          $suggested_qty   = '<em style="'.$letter_color.'">'.$suggested_qty_letter2.'</em> <em> =  '.$suggested_qty_letter.'  </em>';
    }

    $ave_sale_qty         = '<em style="'.$letter_color.'">D</em><em> = ( A + B + C ) / 90</em>';
    $ave_sale_qty_letter  = 'D';
  	array_push($formulas,$ave_sale_qty,$min_level,$suggested_qty); 
    
    echo '
          <div class="row">
             <div class="col-sm-4">
                <button type="button" class="btn btn-success" data-dismiss="modal"  style="margin-left: 1x;margin-top:20px;" onclick="generate_pdf('.$r_no.')">
                                        <i class="bx bx-x d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block ">Generate PDF</span>
                </button> 
             </div>
          </div>
          <table border="1" style="background-color: rgb(5, 68, 104);color: white;font-size:15px;">';
    for($a = 0;$a<count($formulas);$a++)
    {
         echo '<tr>
                       <td><strong>'.$formulas[$a].'</strong></td> 
               </tr>';        
    }
    echo '</table>';


    //$fix_header_style ="position: sticky;left: 0;background-color:rgb(0 86 137);color: white;";
    $fix_header_style ="background-color: rgb(0, 86, 137);";
  ?>  
  <div class="row">
       <div class="col-12  table-responsive" style="padding-top: 20px;">  

        <!-- <table id="consolidated-table" class="table table-hover table-bordered table-responsive" style="background-color: rgb(5, 68, 104); width: 100%;"> -->
        <table id="consolidated-table" class="table table-hover table-bordered table-responsive" style="background-color: rgb(5, 68, 104); width: 100%;">
            <thead style="text-align: center;color:white;">
                <?php 
                         

                         if(
                             ($user_details[0]['user_type'] == 'buyer' && $head["status"] == 'Pending') ||
                             ($user_details[0]['user_type'] == 'category-head' && $head["status"] == 'Approved by-buyer')  ||
                             ($user_details[0]['user_type'] == 'corp-manager' && $head['status'] == 'Approved by-category-head')  || 
                             (in_array($user_details[0]['user_type'],array('category-head')) && $store_handled[0]['value_']   != 'cdc' &&   in_array($head['status'],array('Approved by-buyer'))  ) ||
                             (in_array($user_details[0]['user_type'],array('buyer')) && $store_handled[0]['value_']   != 'cdc' &&   in_array($head['status'],array('Approved by-category-head'))  ) 
                           )
                           {
                                 $sugg_reord_qty = '';
                                 $satus_color    = 'color:red;';
                           }
                           else 
                           {
                                 $sugg_reord_qty = 'text-align: left; background-color: rgb(0, 86, 137); color: white; position: sticky; right: 0px;';
                                 $satus_color    = 'color:white;';
                           }


                          $letter_style = 'text-align:center;';
                          echo  '<tr>  
                                   <th style="'.$fix_header_style.'background-color: rgb(0, 86, 137); width: 28.1944px; left: 0px; position: sticky;"></th> 
                                   <th style="'.$fix_header_style.'background-color: rgb(0, 86, 137); width: 28.1944px; left: 66px; position: sticky;"></th> 
                                   <th style="'.$fix_header_style.'background-color: rgb(0, 86, 137); width: 28.1944px; left: 180px; position: sticky;"></th>'; 


                          echo '   <th style="'.$letter_style.'"><em style="'.$letter_color.'">A</em></th> 
                                   <th style="'.$letter_style.'"><em style="'.$letter_color.'">B</em></th> 
                                   <th style="'.$letter_style.'"><em style="'.$letter_color.'">C</em></th>';            
                           

                          echo  '   
                                   <th style="'.$letter_style.'"><em style="'.$letter_color.'">'.$ave_sale_qty_letter.'</em></th> 
                                   <th style="'.$letter_style.'"><em style="'.$letter_color.'">'.$conso_ave_sales.'</em></th>
                                   <th style="'.$letter_style.'">'.$all_ave_sales.'</th> 
                                   <th style="'.$letter_style.'"><em style="'.$letter_color.'">'.$min_level_letter.'</em></th> 
                                   <th style="'.$letter_style.'">'.$qty_onhand.'</th> 
                                   <th style="'.$letter_style.'">'.$all_qty.'</th> 
                                   <th style="'.$letter_style.'">'.$conso_all_qty.'</th>
                                   <th style="'.$letter_style.'"></th> 
                                   <th style="'.$letter_style.'"></th> 
                                   <th style="'.$letter_style.'"></th> 
                                   <th style="'.$letter_style.'">'.$pending_qty.'</th> 
                                   <th style="'.$letter_style.$sugg_reord_qty.'"><em style="'.$letter_color.'">'.$suggested_qty_letter2.'</em></th>
                                   <th style="text-align: center; background-color: rgb(0, 86, 137); color: white; position: sticky; right: 0px;"></th> 
                              </tr>';  
                   ?>
                    <?php 

                       echo   '<tr>
                                    <th style="'.$fix_header_style.'" >ITEM NO</th>
                                    <th style="'.$fix_header_style.'">DESCRIPTION</th>
                                    <th style="'.$fix_header_style.'" >UOM</th>';
                      if($store_handled[0]['value_'] == 'cdc')
                      {
                         $month_header = 'OFF TAKE';
                      }
                      else 
                      {
                         $month_header = 'SALES';
                      }

                      $month_color = 'color:orange';
                      echo '<th><em style="'.$month_color.'">'.$month1.'</em> <br>'.$month_header.' QTY.</th>
                            <th><em style="'.$month_color.'">'.$month2.'</em> <br>'.$month_header.' QTY.</th>
                            <th><em style="'.$month_color.'">'.$month3.'</em> <br>'.$month_header.' QTY.</th>';
                    ?>    
                            <th >
                    <?php  
                                    $store_color = 'color:#33FF5E';
                                    if($store_handled[0]['value_'] == 'cdc')
                                    {
                                        $daily_ave = 'OFF TAKE QTY.';
                                    }
                                    else 
                                    {
                                        $daily_ave = 'SALE QTY.';
                                    }
                                    echo  '<em style="'.$store_color.'">'.strtoupper($store_handled[0]['value_']).'</em> <br>DAILY AVE. '.$daily_ave;                             
                      echo  '</th> 
                            <th>';
                                    if($store_handled[0]['value_'] == 'cdc')
                                    {
                                         echo 'ALL STORES AVE SALES ';                                                         
                                    }
                                    else 
                                    {
                                         echo 'CDC DAILY AVE. OFF TAKE QTY.'; 
                                    }
                    ?>                        
                            </th>
                            <th >
                                  <?php  /*if($store_handled[0]['value_'] == 'cdc')
                                         {                              
                                            echo  ' CONSO AVE SALES QTY.<br> '; 
                                         }      
                                         else 
                                         {                                
                                            echo  'CDC DAILY AVE. QTY. OFF TAKE';                      
                                         }*/                          
                                         echo  ' CONSO AVE SALES QTY.<br> '; 
                                   ?>
                            	   
            				</th>                       
                            <th >  
                                   MIN LEVEL QTY.
                            </th>
                            <th >
                                  <?php
                                          if($store_handled[0]['value_'] == 'cdc')
                                          {
                                              echo '<em style="'.$store_color.'">'.strtoupper($store_handled[0]['value_']).'</em> <br>QTY ON HAND';
                                          }
                                          else 
                                          {
                                              echo '<em style="'.$store_color.'">'.strtoupper($store_handled[0]['value_']).'</em> <br>QTY ON HAND';
                                          }
                                   ?>   
                            </th>
                            <th >
                                  <?php
                                          if($store_handled[0]['value_'] == 'cdc')
                                          {  
                                                echo 'ALL STORES QTY ON HAND';
                                          }  
                                          else 
                                          {
                                                echo 'CDC QTY. ON HAND';
                                          }
                                   ?>  
                            </th>
                            <th>
                                 CONSO QTY ON HAND
                            </th>
                            <th >LAST DIRECT COST</th>
                            <th >LAST RCV QTY</th>
                            <th style="width:50px;">LAST DEL DATE</th>
                            <th >PENDING QTY  </th>
                <?php 
                     echo ' <th style="width:117.778px'.$sugg_reord_qty.'"> SUGGESTED REORDER QTY </th>
                            <th style="text-align: center; background-color: rgb(0, 86, 137); color: white; position: sticky; right: 0px;">';

                                         if(
                                             ( in_array($user_details[0]['user_type'],array('category-head')) ) || 
                                             ( $head['value_'] != 'cdc' &&  $current_login_user_details[0]['user_type'] == 'buyer' && $current_user_login[0]['value_'] == 'cdc' && in_array($head["status"], array('Approved by-category-head')) )
                                           )
                                           { ?>
                                                 <button type="button" class="btn btn-info btn-sm" id="" style="padding: 6px 6px;width: 91px;margin-top: 5px;" onclick="approve_disapprove_quantity_multiple('Approved')"><i class="fa fa-edit"></i> approve</button>
                                                 <button type="button" class="btn btn-warning btn-sm" id="" style="padding: 6px 6px;margin-top: 5px;" onclick="approve_disapprove_quantity_multiple('Disapproved')"><i class="fa fa-edit"></i> disapprove</button>
                                     <?php }
                                     ?>
                                     <button type="button" class="btn btn-success btn-sm"  onclick="save_checked_sug_qty()" id="save_checked_button" style="width: 91px;margin-top: 5px;;margin-bottom:22px;"><i class="fa fa-edit"></i>save</button>    
                                     <input style="margin-left:39px;margin-bottom: 15px;" id="main_checkbox" class="checkbox" type="checkbox" name="main_checkbox" onchange="check_uncheck()">                    
                            </th>
                            <th >ACTION</th>
                   </tr>
            </thead>
            <tbody>
            	<?php
            		
            		
            		foreach ($report_details as $details) 
                    {

                        $status     = $details['status'];
                        $item_lines = $this->Mms_mod->get_quantity_on_hand($details['reorder_batch'],$details['item_code']);


                        $total_qty              = 0;
                        $quantity_on_hand       = 0;
                        $total_cdc_qty          = 0;
                        $total_store_qty        = 0;
                        $quantity_on_hand       = 0;
                        foreach($item_lines as $lines)
                        {
                           
                            //$login_Store = $this->Mms_mod->get_store_list('login store');
                            //if(in_array($lines['store'],array('asc','icm')))

                            if($lines['store'] != 'cdc')
                            {
                               
                                 
                                    //if(!empty(trim($lines['item_code'])))
                                    if(trim($lines['item_code']) != '')
                                    {                                        
                                         $get_quantity = $this->Mms_mod->get_reorder_report_data_item_vendor($lines['reorder_number'],$details['reorder_batch'],$lines['item_code']);
                                         foreach($get_quantity as $qty)
                                         {
                                              //echo "quantity_on_hand:".$lines['quantity_on_hand']."--->"."quantity:".$qty['quantity']."<br>";
                                             $uom_data = $this->Mms_mod->get_nav_uom_header($details['db_id'],$lines['quantity_on_hand'],$qty['quantity'],$lines['item_code'],$lines['uom']);                                             
                                             //($uom_data);
                                             foreach($uom_data as $uom)
                                             {
                                                  $store_qty  = number_format($uom['store_qty'],2);
                                                  $total_qty += round($store_qty,2);

                                                  if($store_handled[0]['value_'] == $lines['store'])
                                                  {
                                                      $quantity_on_hand  = $store_qty;
                                                  }
                                             }  

                                         }  


                                         if(empty($get_quantity))
                                         {
                                            $store_qty  = round($lines['quantity_on_hand'],2);                                        
                                            $total_qty += $store_qty;
                                         }

                                         $total_store_qty  += $store_qty;                                 
                                    }
                            }
                            else 
                            {                                 
                                 //echo "cdc".$lines['quantity_on_hand']."<br>";
                                 // $total_qty += $lines['quantity_on_hand'];
                                 $store_qty  = round($lines['quantity_on_hand'],2);
                                 $total_qty += $store_qty;

                                 if(in_array($lines['store'],array('cdc')))
                                 {
                                        $total_cdc_qty += $store_qty;
                                 }
                            }



                            //echo 'Login store:'.$login_Store[0]['value_']."<br>";
                            //echo $lines['quantity_on_hand'].'-->'.$lines['item_code'].'-->'.$lines['uom'].'-->'.$lines['reorder_number'].'-->'.$lines['store']."<br>";     
                        }


                        $style ="text-align:right;";

                       
                    

                        if($store_handled[0]['value_'] == 'cdc' && $non_cdc > 0)
                        {                            
                             $max_level        = $details["all_ave_sales"] * $head["lead_time_factor"]; 
                             $qty_on_hand      = $total_cdc_qty;         
                            // $total_quantity   = $total_cdc_qty+$total_store_qty;                       
                             $total_quantity   = $total_qty;                       
                        }
                        else 
                        if($store_handled[0]['value_'] == 'cdc' && $non_cdc == 0)
                        {                             
                             $max_level       = $details["maximum_level"];
                             $qty_on_hand     = $total_cdc_qty ;  
                             $total_quantity  = $total_cdc_qty ; 
                        }
                        else 
                        {
                             $max_level        = $details["maximum_level"];
                             $qty_on_hand      = str_replace('-','',$total_store_qty);  
                             $total_store_qty  = $total_cdc_qty;
                             $total_qty        = $qty_on_hand + $total_store_qty;   
                             $total_quantity   = $total_qty;  
                        }
                        

                        if(in_array($details['last_del_date'],array('0000-00-00','1970-01-01')) )
                        {
                              $last_del_date = '-';
                        }
                        else 
                        {
                             $last_del_date = date('M d, Y',strtotime($details['last_del_date']));
                        }
                        


            			echo '<tr>
            			           <td style="'.$fix_header_style.'">'.$details["item_code"]       .'</td>
            			           <td>'.$details["Item_description"].'</td>
            			           <td>'.$details["uom"]             .'</td>
            			           <td style="'.$style.'">'.number_format($details["month_sales_1"],2)    .'</td>
            			           <td style="'.$style.'">'.number_format($details["month_sales_2"],2)    .'</td>
            			           <td style="'.$style.'">'.number_format($details["month_sales_3"],2)    .'</td>
            			           <td style="'.$style.'">'.number_format($details["ave_sales"],2)        .'</td>
                                   <td style="'.$style.'">'.number_format($details["all_ave_sales"],2).'</td>
            			           <td style="'.$style.'">'.number_format($details["consolidated_ave_sales"],2).'</td>
            			           <td style="'.$style.'">'.number_format($max_level).'</td>
            			           <td style="'.$style.'">'.number_format($qty_on_hand,2).'</td>
                                   <td style="'.$style.'">'.str_replace('-','',number_format($total_store_qty,2)).'</td>            			           
                                   <td style="'.$style.'">'.str_replace('-','',number_format($total_qty,2)).'</td>
            			           <td style="'.$style.'">'.number_format($details["last_direct_cost"],2).'</td> 
            			           <td style="'.$style.'">'.number_format($details["last_rcv_qty"],2).'</td> 
            			           <td>'.$last_del_date.'</td>'; 
                                   $pending_qty = 0;
                                   $po_data = $this->Mms_mod->get_reorder_report_data_po(trim($details["item_code"]),$r_no,'active po');
                                   
                                   foreach($po_data as $po)
                                   {
                                        $select              = "*";
                                        $table               = "reorder_store";
                                        $where['databse_id'] = $po['db_id'];
                                        $store_details       = $this->Mms_mod->select($select,$table,$where);
                                        if(!empty($store_details))
                                        {
                                             $table2             = "reorder_po";
                                             $where2['store_id'] = $store_details[0]['store_id'];  
                                             $where2['document_number'] = $po['document_no'];                                            
                                             $po_tag             = $this->Mms_mod->select($select,$table2,$where2);
                                             if(!empty($po_tag))
                                             {
                                                 if($po_tag[0]['status'] == 'Active')
                                                 {
                                                     $pending_qty += $po['pending_qty'];
                                                 }
                                             }
                                        }
                                   }
                                    
                                   // if(!empty($po_data))
                                   // {
                                   //    $pending_qty = $po_data[0]['total_pend_qty'];
                                   // }                                 



                        if($pending_qty > 0)          
                        {
                             $pending_color = 'color:red;';          
                        }
                        else 
                        {
                             $pending_color = 'color:blue';          
                        }


                         // if($store_handled[0]['value_'] == 'cdc')
                         // {
                         //     $suggested_reorder_qty = round($max_level,2) - round($total_qty) - round($pending_qty);
                         // }
                         // else 
                         // {
                         //     $suggested_reorder_qty = round($max_level,2) - round($quantity_on_hand) - round($pending_qty);                            
                         // }


                         // $max_minus_qty         = $max_level - $total_quantity;   
                         // $remove_negative       = str_replace('-','',$max_minus_qty);
                         // $suggested_reorder_qty = $remove_negative - round($pending_qty); 

                         $suggested_reorder_qty = round($max_level) - ($total_quantity + round($pending_qty));
                         $overstock = round($suggested_reorder_qty);                       


                        $change_qty            = $this->Mms_mod->get_reorder_report_change_quantity_history($details['reoder_id'],'','limit');     
                        if(!empty($change_qty) && in_array($change_qty[0]['status'],array('Pending','Approved')) )
                        {
                            $sugg_qty    = number_format($change_qty[0]['inputed_quantity']);
                            $sugg_dr_qty = number_format($change_qty[0]['inputed_dr_quantity']);
                        }
                        else 
                        {
                            $sugg_qty    = number_format($suggested_reorder_qty);
                            $sugg_dr_qty = 0;
                        }

                        $remarks = '';




                        if($suggested_reorder_qty < 0)
                        {
                             $remarks  = '<br><em style="'.$satus_color.'">Over Stock '.str_replace('-','',$overstock).'</em><br>';
                             if($sugg_qty < 0)
                             {
                                  $sugg_qty = 0;
                                  $sugg_dr_qty = 0;
                             }
                        }


                        if(!empty($change_qty))
                        {
                              if(
                                 in_array($change_qty[0]['status'],array('Pending','Disapproved')) ||
                                 (in_array($user_details[0]['user_type'],array('buyer'))  && $head['value_'] != 'cdc' && $current_user_login[0]['value_'] == 'cdc' && in_array($status,array('Approved by-category-head')))   //if ang nag login kay cdc buyer then ang reorder kay store then Approved by-category-head na siya
                                )
                                {
                                     $qty_input_class = 'sug_reord_qty';
                                }
                                else 
                                {
                                     $qty_input_class = 'sug_reord_qty_approved';
                                }
                         }          
                         else 
                         {
                                 $qty_input_class = 'sug_reord_qty';                            
                         } 

                         $table = 'reorder_report_data_lines_final';

                         $sugg_qty = str_replace('-','',$sugg_qty);

                         $column_data['suggested_reord_qty'] = round($sugg_qty,2);
                         $column_filter['reoder_id']         = $details['reoder_id'];
                         $this->Mms_mod->update_table($table,$column_data,$column_filter);


                         


                         if(strstr(strtoupper($vendor_details[0]['vend_type']),"SI") && strstr(strtoupper($vendor_details[0]['vend_type']),"DR"))
                         {
                             $input = '<label for="suggested_qty_input-'.$details['reoder_id'].'">SI</label> <input type="text" class="'.$qty_input_class.' input_si"  style="color:black;width: 76px;margin-left:5px;" placeholder="'.$sugg_qty.'"  value="'.$sugg_qty.'" id="suggested_qty_input-'.$details['reoder_id'].'" disabled> 
                                       <label for="suggested_qty_input-dr-'.$details['reoder_id'].'">DR</label> <input type="text" class="'.$qty_input_class.' input_dr"  style="color:black;width: 76px;" placeholder="'.$sugg_dr_qty.'"  value="'.$sugg_dr_qty.'" id="suggested_qty_input-dr-'.$details['reoder_id'].'" disabled>
                                      ';
                         }
                         else 
                         {
                             $input = '
                                        <input type="text"  class="'.$qty_input_class.' input_si"  style="color:black;width: 82px;" placeholder="'.$sugg_qty.'"  value="'.$sugg_qty.'" id="suggested_qty_input-'.$details['reoder_id'].'" disabled>
                                        <input type="text" hidden class="'.$qty_input_class.' input_dr"  style="color:black;width: 76px;" placeholder="'.$sugg_dr_qty.'"  value="'.$sugg_dr_qty.'" id="suggested_qty_input-dr-'.$details['reoder_id'].'" disabled>
                                      ';
                         }



            			echo '     <td style="'.$style.'"><a onclick="view_pending_po('."'".trim($head["supplier_code"])."','".trim($details["item_code"])."','".$past_3_month_years[2]."','".$past_3_month_years[0]."','".$store ."','".$r_no."','".$head['databse_id']."'".')" style="cursor: pointer;'.$pending_color.'"><strong>'.number_format($pending_qty).'</strong></a></td> 
            			           <td style="text-align:left;">
                                                                  <a hidden>'.$sugg_qty.'</a>                                                                                                                                 
                             '.$input.$remarks;

                    
                              if(!empty($change_qty))
                              {
                                  if(in_array($change_qty[0]['status'],array('Pending','Disapproved')))
                                  {
                                     $color = 'red';
                                  }
                                  else 
                                  {
                                     $color = 'green';
                                  }

                                  if($satus_color == 'color:white;')
                                  {
                                     if($color == 'red')
                                     {
                                         $color = '#ff8c00';
                                     }
                                     else 
                                     {
                                         $color = '#fff500';
                                     }
                                  }

                                  echo '<a style="color:'.$color.';cursor:pointer;" onclick="change_qty_details('."'".$details['reoder_id']."','".$vendor_details[0]['vend_type'].'\')"><strong>edited  ('.$change_qty[0]['status'].')</strong></a><i style="display:none;"> edited  ('.$change_qty[0]['status'].')</i>';
                              }
                          


                        if(!empty($change_qty))
                        {   
                             $last_nag_edit = $this->Mms_mod->get_reorder_report_change_quantity_history($details['reoder_id'],'','limit');

                             if( 
                                  ( (in_array($change_qty[0]['status'],array('Approved')) && in_array($user_details[0]['user_type'],array('category-head'))) ) ||
                                  (  in_array($change_qty[0]['status'],array('Approved')) && $head['value_'] != 'cdc' &&  $current_login_user_details[0]['user_type'] == 'buyer' && $current_user_login[0]['value_'] == 'cdc' && in_array($head["status"], array('Approved by-buyer'))) ||
                                  (  $last_nag_edit[0]['approved_by'] == $current_login_user_details[0]['user_id'] && in_array($last_nag_edit[0]['status'],array('Approved')) )
                               )
                               {
                                  $show = 'no';
                               }
                               else 
                               {
                                  $show = 'yes';  
                               }
                        }    
                        else 
                        {
                            $show = 'yes';
                        }  

                        echo '</td>         

                                    <td style="text-align:center;">';
                        

                        if($show == 'yes')
                        {                           
                            echo  '          <label for="suggested_qty_checkbox-'.$details['reoder_id'].'">
                                                 <input style=""  id="suggested_qty_checkbox-'.$details['reoder_id'].'"  onchange="check_uncheck_main('."'#suggested_qty_checkbox-".$details['reoder_id']."','".$details['reoder_id']."'".')"   class="checkbox_suggested_qty" type="checkbox" name="checkbox" value="'.$details['reoder_id']."_".$sugg_qty."_".$sugg_dr_qty.'">
                                             </label>';  
                        }            


                        echo  '      </td>       
                              <td>';


                        if($show == 'yes')
                        {

                            echo '     
                                             <button type="button" class="btn btn-primary btn-sm" id="edit_btn-'.$details['reoder_id'].'" style="padding: 6px 6px;width:90px;" onclick="edit_quantity_input('."'".$details['reoder_id']."'".')"><i class="fa fa-edit"></i> edit</button>
                                             <button type="button" class="btn btn-success btn-sm" id="save_btn-'.$details['reoder_id'].'" style="padding: 6px 6px;width: 90px;display:none;" onclick="update_quantity_input('."'".$details['reoder_id']."','".round($suggested_reorder_qty)."'".')"><i class="fa fa-edit"></i> save</button>';    
                        }               
                      
                        echo       '    </td>';

            			echo  '    </tr>';	
                              
            		}
            	?>
            </tbody>
        </table>
    </div>     
  </div>

  <script>

         // // Initialize DataTable
         // var table = $('#consolidated-table').DataTable();             
         // // Hide first column




  		// var reportTable = $("#consolidated-table").DataTable({ "ordering": false});
       var reportTable = $("#consolidated-table").DataTable({
                                                                fixedColumns:
                                                                {
                                                                     left: 3,
                                                                     right: 1                                                                      
                                                                },                                                                                                                                                        
                                                                scrollCollapse: true,
                                                                scrollX: true,
                                                                scrollY: 800,
                                                                createdRow: function(row, data, dataIndex) 
                                                                {
                                                                 <?php  if( 
                                                                             ($user_details[0]['user_type'] == 'buyer' && $head["status"] == 'Pending') || 
                                                                             ($user_details[0]['user_type'] == 'category-head' && $head["status"] == 'Approved by-buyer') ||
                                                                             ($user_details[0]['user_type'] == 'corp-manager' && $head['status'] == 'Approved by-category-head')  ||                                                                                                                                                         
                                                                             (in_array($user_details[0]['user_type'],array('category-head')) && $store_handled[0]['value_']   != 'cdc' &&   in_array($head['status'],array('Approved by-buyer'))  ) ||
                                                                             (in_array($user_details[0]['user_type'],array('buyer')) && $store_handled[0]['value_']   != 'cdc' &&   in_array($head['status'],array('Approved by-category-head'))  ) 
                                                                          )
                                                                          {                                                                                 
                                                                             $columns = 'td:eq(0), th:eq(0), td:eq(1), th:eq(1), td:eq(2), th:eq(2),td:eq(18), th:eq(18)';
                                                                          }
                                                                          else 
                                                                          {
                                                                             $columns = 'td:eq(0), th:eq(0), td:eq(1), th:eq(1), td:eq(2), th:eq(2),td:eq(17), th:eq(17)';
                                                                          }
                                                                         
                                                                          // Apply styles to the first 6 columns (columns 0 to 5)                                                                        
                                                                        echo  " $(row).find('".$columns."').css(
                                                                                {                                                                            
                                                                                     'background-color': 'rgb(0, 86, 137)',
                                                                                     'color': 'white'
                                                                                });";
                                                                  ?>     

                                                                },              
                                                                "order": [

       <?php if(in_array($user_details[0]['user_type'],array('buyer'))) //if naa ang user type  ani nga array pasabot ani i sort descending ang column nga suggested reorder qty para mu top ang mga negative arun dali ra makit an sa buyer ang mga negative qty
             {
        ?>                                                         
                                                                           [17, "desc"], 
        <?php
             }
        ?>
                                                              [16, "desc"],[0, "desc"]],  
                                                              "columnDefs": [
                                                                { "orderable": false, "targets": 18 } // Disable sorting for column 17 (index 16)
                                                              ],
                                                              "lengthMenu": [10, 25, 50, 100,200,300,400,500,1000] // Custom options for records per page
                                                            });
        
         <?php 
               


                 $user_details =$this->Mms_mod->get_user_details();
                 if(
                       (in_array($user_details[0]['user_type'],array('buyer'))  && $head['value_'] == 'cdc' && in_array($status,array('Approved by-buyer','Approved by-category-head','Approved by-corp-manager','Approved by-incorporator','Approved by-corp-buyer'))) ||
                       (in_array($user_details[0]['user_type'],array('buyer'))  && $head['value_'] != 'cdc' && $current_user_login[0]['value_'] == 'cdc' && in_array($status,array('Approved by-corp-buyer'))) ||  //if ang nag login kay cdc buyer then ang reorder kay store then Approved by-corp-buyer na siya
                       (in_array($user_details[0]['user_type'],array('buyer'))  && $head['value_'] != 'cdc' && $current_user_login[0]['value_'] != 'cdc' && in_array($status,array('Approved by-buyer','Approved by-corp-buyer')))  || //if ang nag login kay si store buyer then ang reorder kay store then  Approved by-buyer or  Approved by-corp-buyer na siya
                       (in_array($user_details[0]['user_type'],array('category-head'))  && in_array($status,array('Approved by-category-head','Approved by-corp-manager','Approved by-incorporator','Approved by-corp-buyer'))) ||
                       (in_array($user_details[0]['user_type'],array('corp-manager'))  && in_array($status,array('Approved by-corp-manager','Forward to-Incorporator','Approved by-incorporator'))) ||
                       (in_array($user_details[0]['user_type'],array('incorporator'))  && in_array($status,array('Approved by-incorporator'))) 
                   )
                   { ?>
                     reportTable.columns(18).visible(false);
         <?php     }


                   $show = false;
                   foreach($header_details as $h)
                   {

                         if(
                              ($h['store']  == 'cdc') ||
                              ($head['value_'] != 'cdc' && $cdc == 0)
                           )
                           {
                              $show = true;                             
                              break;                
                           }                          
                   }  

                   if($show)
                   {
                       echo  'reportTable.columns(9).visible(true);
                              reportTable.columns(12).visible(true);';    
                   }
                   else 
                   {
                       echo  'reportTable.columns(9).visible(false);
                              reportTable.columns(12).visible(false);';                        
                   }

                   

                   if($head['value_'] != 'cdc' &&  $current_login_user_details[0]['user_type'] == 'buyer' && $current_user_login[0]['value_'] == 'cdc' && in_array($head["status"], array('Approved by-buyer')))    
                   {
                        echo 'reportTable.columns(18).visible(true);';
                   }


                   if( 
                        ($non_cdc == 0) ||
                        ($head['value_'] != 'cdc' && $cdc == 0)
                     )
                     {
                          echo "
                                 reportTable.columns(7).visible(false);
                                 reportTable.columns(8).visible(false);
                                 reportTable.columns(11).visible(false); //all store qty on hand
                                 reportTable.columns(12).visible(false); //consolidated qty on hand
                               ";
                     }


                     if(
                             ($store_type == 0 && $non_store_type > 0)
                       )
                       {
                            echo "
                                     reportTable.columns(11).visible(false); //all store qty on hand
                                     reportTable.columns(12).visible(false); //consolidated qty on hand
                                 "; 
                       }   


                   ?>     

                   


        
         reportTable.columns(19).visible(false);
       


  		function view_all_stores_involve(reorder_batch,filter)
  		{
             //$(".custom-width-modal").css("width", "1168px");
             $(".custom-width-modal").css("width", "671px");
             var loader  = ' <center><img src="<?php echo base_url(); ?>assets/img/preloader.gif" style="padding-top:120px; padding-bottom:120px;"></center>';
             $('#report_body').html(loader);
             $("#footer_modal").html('');

             if(filter == 'all_store_ave_sales')
             {
                 var modal_header =  "ALL STORE AVERAGE SALES QUANTITY";
             }
             else 
             {
                 var modal_header = "CONSOLIDATED AVERAGE SALES QUANTITY";                
             }

  			 $("#report_label").html(modal_header);

  			 $("#report_modal").modal({backdrop: 'static',keyboard: false});	
  			 $.ajax({
  			 			type:'POST',
  			 			url:'<?php echo base_url(); ?>Mms_ctrl/get_all_average_sales',
  			 			data:{
  			 					'reorder_batch':reorder_batch,
                                'filter':filter
  			 			     },
  			 			dataType:'JSON',
  			 			success: function(data)
  			 			{
                            $("#footer_modal").html(data.buttons);
  			 				$("#report_body").html(data.html);
                            $("#tab").html(data.tab);
  			 			}    
  			        });
  		}


        function view_all_stores_qty_onhand(reorder_batch,filter)
        {            
            //$(".custom-width-modal").css("width", "1073px");
            $(".custom-width-modal").css("width", "796px");
            var loader  = ' <center><img src="<?php echo base_url(); ?>assets/img/preloader.gif" style="padding-top:120px; padding-bottom:120px;"></center>';
            $('#report_body').html(loader);
            $("#footer_modal").html('');

            if(filter == 'consolidated_qty_onhand')    
            {
                 filter_ = 'CONSOLIDATED QUANTITY ON HAND';
            }
            else 
            {
                 filter_ = 'ALL STORES QUANTITY ON HAND';
            }

            $("#report_label").html(filter_);


            $("#report_modal").modal({backdrop: 'static',keyboard: false});
            $("#tab").html("");
            $.ajax({
                        type:'POST',
                        url:'<?php echo base_url(); ?>Mms_ctrl/get_all_qty_onhand',
                        data:{
                                'reorder_batch':reorder_batch,
                                'filter':filter
                             },
                        dataType:'JSON',
                        success: function(data)
                        {
                            $("#report_body").html(data.html);
                            $("#footer_modal").html(data.buttons);                            
                        }    
                  });
        }


        function view_pending_po(supplier_code,item_code,from,to,store,reorder_batch,database_id)
        {
             $(".custom-width-modal").css("width", "1168px");
             var loader  = ' <center><img src="<?php echo base_url(); ?>assets/img/preloader.gif" style="padding-top:120px; padding-bottom:120px;"></center>';
             $('#report_body').html(loader);
             $("#footer_modal").html('');
             $("#tab").html("");

             $("#report_label").html('Pending PO');
             $("#report_modal").modal({backdrop: 'static',keyboard: false});
             $.ajax({
                        type:'POST',
                        url:'<?php echo base_url(); ?>Mms_ctrl/get_pending_po',
                        data:{
                                'supplier_code':supplier_code,
                                'item_code':item_code,
                                'from':from,
                                'to':to,
                                'store':store,
                                'reorder_batch':reorder_batch,
                                'database_id':database_id
                             },
                        dataType:'JSON',
                        success: function(data)
                        {
                            $("#report_body").html(data.html);
                            $("#footer_modal").html(data.buttons);
                            //console.log(data.html);
                        }    
                  });


        }


        function update_pending_po(item_code,reorder_batch)
        {
             var selectedDates = [];
             var selectedID    = [];
             $('.pending_date').each(function() 
             {
                 var dateValue = $(this).val();
                 selectedDates.push(dateValue);
                 var inputId   = ($(this).attr('id')).replace("date_", "");
                 selectedID.push(inputId);
             });

             var pending_qty = [];
             var po_date     = [];
             for(var a=0;a<selectedID.length;a++)
             {
                pending_qty.push($('#qty_'+selectedID[a]).val());
                po_date.push($('#po_date_'+selectedID[a]).val());
             }

            // console.log(selectedDates+'--->'+selectedID+'---->'+pending_qty+'------>'+po_date);

            Swal.fire({
                          title: 'Are you sure',
                          text: "You want to save changes?",
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
                                            url:'<?php echo base_url(); ?>Mms_ctrl/update_pending_po',
                                            data:{
                                                     'exp_del_date':JSON.stringify(selectedDates),
                                                     'document_no':JSON.stringify(selectedID),   
                                                     'pending_qty':JSON.stringify(pending_qty),
                                                     'po_date':JSON.stringify(po_date),
                                                     'item_code':item_code,
                                                     'reorder_batch':reorder_batch
                                                 },
                                            dataType:'JSON',
                                            success: function(data)
                                            {
                                                     Swal.fire({
                                                             position: 'center',
                                                             icon: 'success',
                                                             title: 'successfully save',
                                                             showConfirmButton: true                                           
                                                        })  
                                            }     
                                    });
                        }

                    }); 



        }






        function validateDates(calendar_id)
        {
             // Get the current date
             var currentDate = new Date();

             // Format the current date as "yyyy-MM-dd"
             //var formattedDate = currentDate.toISOString().substr(0, 10);

             var date_selected = new Date($("#"+calendar_id).val());

             // Set the time portion of currentDate and date_selected to 0
              currentDate.setHours(0, 0, 0, 0);
             date_selected.setHours(0, 0, 0, 0);

             //console.log(dateFrom);
             
             if (date_selected.getTime() < currentDate.getTime()) 
             {
                 // alert('past date is not allowed');
                 swal_display('error','opps','Past date is not allowed');                  
                 $(document).ready(function() 
                 {
                      var currentDate = new Date();
                      var formattedDate = currentDate.toISOString().substr(0, 10);
                      $('#'+calendar_id).val(formattedDate);
                    });

             }

              // if (dateTo < dateFrom) 
              //                               {
              //                                   //alert("Date To cannot be less than Date From!");
              //                                   swal_display('error','opps','There is no transaction ahead from the current day');                                                
              //                                   document.getElementById("date_from").value = formattedDate;
              //                                   document.getElementById("date_to").value = formattedDate;
              //                               }
        }    


         function swal_display(icon,title,text)
         {
                 Swal.fire({
                                 icon: icon,
                                 title:title,
                                 html: text                                  
                             });    
         }

         function edit_quantity_input(reoder_id)
         {
                $("#suggested_qty_input-"+reoder_id).removeAttr("disabled");
                $("#save_btn-"+reoder_id).show();
                $("#edit_btn-"+reoder_id).hide();
                $("#main_checkbox").show();
                // Hide first column
                //table.columns(16).visible(true);
                $("#suggested_qty_checkbox-"+reoder_id).show();
                $('#suggested_qty_checkbox-'+reoder_id).prop('checked', false);
                $('#main_checkbox').prop('checked', false);
                $('#save_checked_button').show();

         }


         function update_quantity_input(reoder_id,suggested_reorder_qty)
         {               
                var loader  = ' <center><img src="<?php echo base_url(); ?>assets/img/preloader.gif" style="padding-top:120px; padding-bottom:120px;"></center>';
                $('#report_body').html(loader);
                $(".custom-width-modal").css("width", "512px");
                $.ajax({
                             type:'POST',
                             url:'<?php echo base_url() ?>Mms_ctrl/suggested_qty_ui',
                             data:{
                                      'reorder_id':reoder_id,
                                      'suggested_reorder_qty':suggested_reorder_qty  
                                  },
                             dataType:'JSON',
                             success: function(data)
                             {
                                 $("#report_body").html(data.html);
                                 $("#footer_modal").html(data.buttons);
                                 $("#report_label").html(data.label);
                             }     
                       });

                $("#report_modal").modal({backdrop: 'static',keyboard: false});
              
         }


         function save_reason(reorder_id,suggested_reorder_qty)
         {
                var checkedCheckboxes     = $(".reasons:checked");
                var checked_reasons       = [];
                var inputed_suggested_qty = $("#suggested_qty_input-"+reorder_id).val();
                checkedCheckboxes.each(function() 
                {
                  // Access each checked checkbox using $(this)
                  //console.log($(this).attr("id")); // Output the ID of each checked checkbox                    
                    checked_reasons.push($(this).val());
                });

                
                if(checked_reasons.length == 0)
                {
                     swal_display('error','opps','please select a reason for adjustment');
                }
                else 
                {

                        loader();

                        Swal.fire({
                                  title: 'Are you sure',
                                  text: "You want to change the quantity?",
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
                                                url:'<?php echo base_url(); ?>Mms_ctrl/save_reason',
                                                data:{
                                                        'checked_reasons': JSON.stringify(checked_reasons),   
                                                        'reorder_id':reorder_id,
                                                        'suggested_reorder_qty':suggested_reorder_qty,
                                                        'inputed_suggested_qty':inputed_suggested_qty  
                                                     },
                                                dataType:'JSON',
                                                success: function(data)
                                                {
                                                     Swal.fire({
                                                                     position: 'center',
                                                                     icon: 'success',
                                                                     title: 'Quantity successfully updated',
                                                                     showConfirmButton: true                                           
                                                                })   
                                                     setTimeout(function() 
                                                     {
                                                         swal.close();
                                                         location.reload();                                                     
                                                     }, 3000);


                                                     $("#report_modal").modal('hide');
                                                }     
                                           })
                                 }
                            });                
                }

                console.log(checked_reasons);
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


       
        function swal_display(icon,title,text)
        {
             Swal.fire({
                             icon: icon,
                             title:title,
                             text: text                                  
                         });    
        }


        function change_qty_details(reoder_id,vend_type)
        {
            $(".custom-width-modal").css("width", "1120px");
            $("#report_modal").modal({backdrop: 'static',keyboard: false}); 

            $.ajax({
                        type:'POST',
                        url:'<?php echo base_url(); ?>Mms_ctrl/change_qty_details',
                        data:{
                                'reoder_id':reoder_id,
                                'vend_type':vend_type    
                             },
                        dataType:'JSON',
                        success: function(data)
                        {
                              $("#footer_modal").html(data.buttons);
                              $("#report_body").html(data.html);
                              $("#report_label").html('Adjustment details ---'+data.report_label);
                        }     
                   });

            // $("#footer_modal").html(data.buttons);
            // $("#report_body").html(data.html);

        }


         function red_color(id)
         {
              $('#'+id).css('border-color', 'red');
         }



         function cancel_batch(reorder_batch,user_type)
         {
              Swal.fire({
                          title: 'Are you sure',
                          text: "You want to Cancel this reorder??",
                          icon: 'warning',
                          showCancelButton: true,
                          confirmButtonColor: '#3085d6',
                          cancelButtonColor: '#d33',
                          confirmButtonText: 'Yes'
                       }).then((result) => 
                       { 
                           if(result.isConfirmed) 
                           {
                                var status = 'CANCELED';
                                update_batch(reorder_batch,status);    

                           }
                       }); 
         }
        



        function approve_batch(reorder_batch,user_type,purpose,vend_type)
        {

              // Initialize the DataTable              
             dataTable       = $('#consolidated-table').DataTable();            
             var sugg_qtys   = []; 
             var enabled     = [];
             var empty_field = [];
             dataTable.rows().nodes().to$().find('input[class="sug_reord_qty"]').each(function()
             {                
                 var input_id = $(this).attr('id');
 
                 sugg_qtys.push([this.value,input_id]);  

                 if(this.value < 0)           
                 {
                    $('#'+input_id).css('border-color', 'red');
                    red_color(input_id);
                 }


                 if ($(this).is(':not(:disabled)'))
                 {
                          // Input is enabled                         
                          enabled.push(input_id);
                          red_color(input_id);
                 }


                 if(this.value == '')
                 {
                    empty_field.push(input_id);
                    red_color(input_id);
                 }
                
             });



             //console.log(sugg_qtys);

             // Check negative values in other datatable pages
            dataTable.on('draw', function() 
            {
                dataTable.rows().nodes().to$().find('input.sug_reord_qty').each(function() 
                {
                    var value = parseFloat(this.value);
                    var inputId = $(this).attr('id');

                    if (value < 0) 
                    {                         
                         red_color(inputId);
                    }
                    else 
                    {
                         $('#' + inputId).css('border-color', ''); // Remove red color if not negative                          
                    }


                    if ($(this).is(':not(:disabled)'))
                    {
                          // Input is enabled                         
                          enabled.push(inputId);
                          red_color(inputId);
                    }                   
                });
            });




            //console.log(enabled);


            var hasNegativeValues = false;

            for (var i = 0; i < sugg_qtys.length; i++) 
            {
              var value = sugg_qtys[i][0];
              
              if (value < 0) 
              {
                hasNegativeValues = true;
                break;
              }
            }

           





            var main_error     = '';
            var negative_error = '';
            if(hasNegativeValues)
            {
                 negative_error = ' (*) negative values ';       
            }
            

            var enabled_error = '';
            if(enabled.length>0) 
            {
                enabled_error = ' (*) unsaved changes ';
            }


            var empty_error = '';
            if(empty_field.length>0)
            {
                empty_error = '  (*) empty fields ';
            }           



            main_error  = 'cannot proceed, the system encountered following errors in suggested reorder quantity '+negative_error+' '+enabled_error+' '+empty_error;
            main_error  = $.trim(main_error);
             console.log(negative_error+'--->'+enabled_error+'--->'+empty_error);
            if(negative_error != '' ||  enabled_error != '' || empty_error != '')
            {                
                swal_display('error','opps',main_error);                
            }
            else 
            {      

                    $.ajax({
                                type:'POST',
                                url:'<?php echo base_url(); ?>Mms_ctrl/check_change_quantity_history',
                                data:{
                                       'reorder_batch':reorder_batch,
                                       'user_type':user_type
                                     },
                                dataType:'JSON',
                                success: function(data)
                                {
                                     if(data.message == 'proceed')
                                     {
                                         if(user_type == 'buyer' && purpose == 'corp-buyer')
                                         {
                                             var message = 'proceed';
                                             var status  = 'Approved by-corp-buyer';
                                         }
                                         else  
                                         if(user_type == 'buyer')   
                                         {
                                             var message = 'proceed';
                                             var status  = 'Approved by-buyer';
                                         }
                                         else 
                                         if(user_type == 'category-head')
                                         {
                                             var message = 'approve';
                                             var status  = 'Approved by-category-head';
                                         }
                                         else 
                                         if(user_type == 'corp-manager' && purpose == 'approval')
                                         {
                                             var message = 'approve';
                                             var status  = 'Approved by-corp-manager';
                                         }
                                         else 
                                         if(user_type == 'corp-manager' && purpose == 'to incorp')
                                         {
                                             var message = 'Forward';
                                             var status  = 'Forward to-Incorporator';
                                         }
                                         else 
                                         if(user_type == 'incorporator' && purpose == 'incorporator')                                         
                                         {
                                             var message = 'approve';
                                             var status  = 'Approved by-incorporator';
                                         }

                                         //console.log(user_type+'--->'+purpose);

                                         Swal.fire({
                                                      title: 'Are you sure',
                                                      text: "You want to "+message+" this reorder??",
                                                      icon: 'warning',
                                                      showCancelButton: true,
                                                      confirmButtonColor: '#3085d6',
                                                      cancelButtonColor: '#d33',
                                                      confirmButtonText: 'Yes'
                                                   }).then((result) => 
                                                   { 
                                                       if(result.isConfirmed) 
                                                       { 
                                                             loader_();
                                                           
                                                            update_batch(reorder_batch,status,vend_type);    

                                                       }


                                                   }); 
                                     }
                                     else 
                                     {
                                             swal_display('error','opps',data.message);
                                     }  
                                }     
                           });
            }


              
        }


        function update_si_dr(qty,id,vend_type)
        {
             $.ajax({ 
                           type:'POST',
                           url:'<?php echo base_url(); ?>Mms_ctrl/update_si_dr',
                           data:{
                                    'qty':qty,
                                    'id':id,
                                    'vend_type':vend_type
                                },
                           dataType:'JSON',
                           success: function(data)
                           {
                               console.log("success update"); 
                           }      
                    });
        }



        function update_batch(reorder_batch,status,vend_type)
        { 
             var item_list = [];
             reportTable.column(17).nodes().to$().find('input[type="text"]').each(function() 
             {   
                var inputed_qty   =  $(this).val();
                var input_id = $(this).attr('id').split('_');

                // console.log(inputed_qty+'---->'+input_id);

                var split_id =  input_id[2].split('-');

                if(split_id.length == 3)//if dr
                {
                //      console.log(split_id+"------>"+split_id[2]+"----->dr ni --->"+$(this).attr('id'));
                //      update_si_dr(inputed_qty,split_id[2],'dr');

                    var item_line = [
                                       {inp_qty: inputed_qty},
                                       {reoder_id: split_id[2] },
                                       {vnd_type:'dr'}   
                                   ];
                }
                else 
                {
                //     console.log(split_id+"------>"+split_id[1]+"----->dili ni dr --->"+$(this).attr('id'));
                //     update_si_dr(inputed_qty,split_id[1],'si');
                    var item_line = [
                                       {inp_qty: inputed_qty},
                                       {reoder_id: split_id[1] },
                                       {vnd_type:'si'}   
                                   ];
                }

                item_list.push(item_line);

             }); 

             //console.log(item_list);

             $.ajax({
                            type:'POST',
                            url:'<?php echo base_url(); ?>Mms_ctrl/update_batch',
                            data:{
                                    'reorder_batch':reorder_batch,
                                    'status':status,
                                    'item_list':JSON.stringify(item_list),  
                                 },
                            dataType:'JSON',
                            success: function(data)
                            {
                                 Swal.fire({
                                                 position: 'center',
                                                 icon: 'success',
                                                 title: 'Reorder successfully '+status,
                                                 showConfirmButton: true                                           
                                           })        

                                 setTimeout(function() 
                                 {
                                      
                                     if(status == 'CANCELED')
                                     {
                                         window.location.href = '<?php echo base_url()?>.Mms_ctrl/mms_ui/2';                                                
                                     }
                                     else 
                                     {
                                         location.reload();     
                                     }
                                 }, 2000);                               
                            }
                    });   

        }




        function disapprove_batch(reorder_batch)
        {
             Swal.fire({
                                  title: 'Are you sure',
                                  text: "You want to disapprove this reorder??",
                                  icon: 'warning',
                                  showCancelButton: true,
                                  confirmButtonColor: '#3085d6',
                                  cancelButtonColor: '#d33',
                                  confirmButtonText: 'Yes'
                            }).then((result) => 
                            { 
                                if(result.isConfirmed) 
                                {
                                      update_batch(reorder_batch,'Disapproved');     

                                }


                            });  
        }
       

        function approve_quantity_input(quantity_id,inputed_quantity,reorder_id)
        {
                Swal.fire({
                                  title: 'Are you sure',
                                  text: "You want to approve this adjustment??",
                                  icon: 'warning',
                                  showCancelButton: true,
                                  confirmButtonColor: '#3085d6',
                                  cancelButtonColor: '#d33',
                                  confirmButtonText: 'Yes'
                            }).then((result) => 
                            { 
                                 if(result.isConfirmed) 
                                 {
                                      update_reorder_report_change_quantity_history(reorder_id,'Approved');                                   
                                 }

                            });    
        }


        function disapprove_quantity_input(quantity_id,inputed_quantity,reoder_id)
        {
             console.log(reoder_id);
             Swal.fire({
                                  title: 'Are you sure',
                                  text: "You want to approve this adjustment??",
                                  icon: 'warning',
                                  showCancelButton: true,
                                  confirmButtonColor: '#3085d6',
                                  cancelButtonColor: '#d33',
                                  confirmButtonText: 'Yes'
                            }).then((result) => 
                            { 
                                 if(result.isConfirmed) 
                                 {
                                      update_reorder_report_change_quantity_history(reoder_id,'Disapproved');                                   
                                 }

                            });    
        }




        function update_reorder_report_change_quantity_history(reoder_id,status)
        {

             var checked = [reoder_id+'_'];
             $.ajax({
                                                type:'POST',
                                                url:'<?php echo base_url(); ?>Mms_ctrl/update_reorder_report_change_quantity_history',   
                                                data:{
                                                        'checked':JSON.stringify(checked),   
                                                        'status':status
                                                     }, 
                                                dataType:'JSON',
                                                success: function(data)     
                                                {
                                                     Swal.fire({
                                                                 position: 'center',
                                                                 icon: 'success',
                                                                 title: 'adjustment successfully '+status,
                                                                 showConfirmButton: true                                           
                                                                 })   
                                                     setTimeout(function() 
                                                     {
                                                         location.reload();                                                     
                                                     }, 2000);
                                                }
                                            });      
        }

  

     $(document).ready(function()
     {
          $(document).on('input', '.sug_reord_qty', function() 
          {
             var inputValue     = $(this).val();
             var sanitizedValue = inputValue.replace(/[^0-9]/g, ''); // Remove non-numeric characters
             $(this).val(sanitizedValue);
             var input_id = $(this).attr('id');                 
             $('#'+input_id).css('border-color', '');     
          });
     });

      

    //  function toggle_checkboxes(table_id, is_checked) 
    // {
    //   var table = $('#' + table_id).DataTable();
    //   table.column(0).nodes().to$().find('input[type="checkbox"]').prop('checked', is_checked);
    //  // $("#checkall, #uncheckall").toggle();
    // }

    function toggle_checkboxes(table_id, is_checked) 
    {

          var table = $('#' + table_id).DataTable();
          table.column(18).nodes().to$().find('.checkbox_suggested_qty').each(function() 
          {
               
                      var checkboxValue   = $(this).val();

                      var checkboxValue_split = checkboxValue.split('_');                      
                      var input_id        = 'suggested_qty_input-'+checkboxValue_split[0];
                      var original_amount = checkboxValue_split[1];

                      var input_dr_id        = 'suggested_qty_input-dr-'+checkboxValue_split[0];  
                      var original_dr_amount =  checkboxValue_split[2];

                      table.column(17).nodes().to$().find('.sug_reord_qty').each(function() 
                      {   
                         var inputId = $(this).attr('id');
                         if(input_id == inputId)
                         {
                             amount_inputed =  $(this).val();
                             isDisabled     = $(this).is(":disabled");
                         }

                         if(input_dr_id == inputId)
                         {
                             amount_dr_inputed =  $(this).val();
                             isDisabled        = $(this).is(":disabled");
                         }

                      }); 

                      // Check  values in other datatable pages
                      table.on('draw', function() 
                      {
                             table.column(17).nodes().to$().find('.sug_reord_qty').each(function() 
                             {
                                 var inputId = $(this).attr('id');
                                 if(input_id == inputId)
                                 {
                                      amount_inputed =  $(this).val();                                      
                                      isDisabled     = $(this).is(":disabled");
                                 }

                                 if(input_dr_id == inputId)
                                 {
                                      amount_dr_inputed =  $(this).val();                                      
                                      isDisabled        = $(this).is(":disabled");
                                 }

                             });
                      }); //hunong

                      //$(this).is(":disabled")

                      if(amount_inputed == original_amount)
                      {
                         
                         $(this).prop('checked', is_checked);
                      }
                      else 
                      if(amount_inputed != original_amount && isDisabled)
                      {
                         $(this).prop('checked', false);
                      }
                      else   
                      {
                          $(this).prop('checked', true);
                      }



                      // if(amount_dr_inputed == original_dr_amount)
                      // {
                         
                      //    $(this).prop('checked', is_checked);
                      // }
                      // else 
                      // if(amount_dr_inputed != original_dr_amount && isDisabled)
                      // {
                      //    $(this).prop('checked', false);
                      // }
                      // else   
                      // {
                      //     $(this).prop('checked', true);
                      // }

                      //console.log('input id: ' + checkboxValue_split[0]+' input value : '+checkboxValue_split[1]+ ' acutal input value'+amount_inputed);
                      
          });


          // if(is_checked)
          // {
              table.column(17).nodes().to$().find('.sug_reord_qty').each(function() 
              { 

                        


                        // Get the ID of the input field
                        var inputId         = $(this).attr('id');
                        var inputValue      = $(this).val();

                        // Get the placeholder value of the input field
                        var placeholderValue = $(this).attr('placeholder');



                        // Perform any operations with the input ID
                        //console.log('Input ID: ' + inputId+' inputed value:'+inputValue+' original value: '+placeholderValue);
                        if(is_checked == true && inputValue == placeholderValue)
                        {
                             $("#"+inputId).removeAttr("disabled");
                        }
                        else                        
                        if(is_checked == false && inputValue == placeholderValue)
                        {
                             $("#" + inputId).prop("disabled", true);                            
                        }
                        else 
                        {
                             $("#"+inputId).removeAttr("disabled");
                        }



                        // var inputId = "suggested_qty_input-"+reoder_id;
                        // var isDisabled = $("#" + inputId).is(":disabled");

                        // if (!isDisabled) 
                        // {     
                        //      var checkbox_val    = $("#suggested_qty_checkbox-"+reoder_id).val().split("_");
                        //      var original_amount = checkbox_val[1];
                        //      var amount_inputed  = $("#"+inputId).val();
                        //      if(amount_inputed == original_amount) 
                        //      {
                        //          $('#suggested_qty_checkbox-'+reoder_id).prop('checked', false);
                        //          $("#" + inputId).prop("disabled", true);  
                        //      }
                        //      else 
                        //      {
                        //           $('#suggested_qty_checkbox-'+reoder_id).prop('checked', true);
                        //      }
                        //      console.log("Input field with ID " + inputId + " is not disabled.");
                        // }

              });



                // Check  values in other datatable pages
                table.on('draw', function() 
                {
                    table.column(17).nodes().to$().find('.sug_reord_qty').each(function() 
                    {

                          


                          // Get the ID of the input field
                          var inputId      = $(this).attr('id'); 
                          var inputValue   = $(this).val();

                          // Get the placeholder value of the input field
                          var placeholderValue = $(this).attr('placeholder');

                          // Perform any operations with the input ID
                          // console.log('Input ID: ' + inputId);
                           if(is_checked == true && inputValue == placeholderValue)
                          {
                               $("#"+inputId).removeAttr("disabled");
                          }
                          else                        
                          if(is_checked == false && inputValue == placeholderValue)
                          {
                               $("#" + inputId).prop("disabled", true);                            
                          }
                          else 
                          {
                               $("#"+inputId).removeAttr("disabled");
                          }              
                    });
                });              
          //}

      // $("#checkall, #uncheckall").toggle();
    }





    function check_uncheck()
    {
        var table_id = 'consolidated-table';

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



   
    function check_uncheck_main(table_id,reoder_id)
    {
       if ($(table_id).is(':checked')) 
        {
            // Checkbox is checked
            // console.log('Checkbox is checked');
            // toggle_checkboxes(table_id, true);
            // Perform any actions for checked state
            //$('#main_checkbox').prop('checked', true);
            // checked.push($(table_id).val());          


            $("#suggested_qty_input-"+reoder_id).removeAttr("disabled");
            $("#suggested_qty_input-dr-"+reoder_id).removeAttr("disabled");

        }
        else
        {


            var inputId = "suggested_qty_input-"+reoder_id;
            var isDisabled = $("#" + inputId).is(":disabled");

            if (!isDisabled) 
            {     
                 var checkbox_val       = $("#suggested_qty_checkbox-"+reoder_id).val().split("_");
                 var original_amount    = checkbox_val[1];
                 var amount_inputed     = $("#"+inputId).val();

                 var original_dr_amount = checkbox_val[2];
                 var amount_dr_inputed     = $("#suggested_qty_input-dr-" + reoder_id).val();    

                 if(amount_inputed == original_amount && amount_dr_inputed == original_dr_amount) 
                 {
                     $('#suggested_qty_checkbox-'+reoder_id).prop('checked', false);
                     $("#" + inputId).prop("disabled", true);  

                     $("#suggested_qty_input-dr-" + reoder_id).prop("disabled", true);
                     
                 }
                 else 
                 {
                      $('#suggested_qty_checkbox-'+reoder_id).prop('checked', true);
                 }



                 console.log("Input field with ID " + inputId + " is not disabled.");
            }
            



            $('#main_checkbox').prop('checked', false);
            // Checkbox is unchecked
            // console.log('Checkbox is unchecked');
            // toggle_checkboxes(table_id, false);
            // Perform any actions for unchecked state

            // checked = $.grep(checked, function (element) {
            //   return element !== $(table_id).val();
            // });
        }    


        //console.log(checked);
    }


   function loader_()
  {
      
      Swal.fire({
                    imageUrl: '<?php echo base_url(); ?>assets/img/Cube-1s-200px.svg',
                    imageHeight: 203,
                    imageAlt: 'loading',
                    text: 'Loading, please wait',
                    allowOutsideClick:false,
                    showCancelButton: false,
                    showConfirmButton: false
                  })              
  } 



  function approve_disapprove_quantity_multiple(status)
  {
      var checked      = [];

      for (var i = 0; i < reportTable.page.info().pages; i++)
      {
        reportTable.page(i).column(18).nodes().to$().find('input[class="checkbox_suggested_qty"]:checked').each(function() 
        {
            if (!checked.includes(this.value))
            {
                 checked.push(this.value);
                 var reord_id = this.value.split('_');
                 var orig_qty = reord_id[1];   

            }
        });
      }




      if(checked.length == 0)
      { 
          swal_display('error','opps','please select an entry to approve');
      }
      else 
      {
             //console.log(data.quantity_id);

               Swal.fire({
                    title: 'Are you sure',
                    text: "You want to "+status+" this adjustment??",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
               }).then((result) => 
               { 
                   if(result.isConfirmed) 
                   {   

                         loader_();
                         $.ajax({
                                      type:'POST',
                                      url:'<?php echo base_url(); ?>Mms_ctrl/update_reorder_report_change_quantity_history',   
                                      data:{
                                              'checked':JSON.stringify(checked),                                               
                                              'status':status
                                           }, 
                                      dataType:'JSON',
                                      success: function(data)     
                                      {   
                                              Swal.fire({
                                                     position: 'center',
                                                     icon: 'success',
                                                     title: 'adjustment successfully '+status,
                                                     showConfirmButton: true                                           
                                               })     
                                               setTimeout(function() 
                                               {
                                                       swal.close();
                                                       location.reload();                                                     
                                               }, 3000);   
                                      }
                                });   
                         
                            // for(var a=0;a<checked.length;a++)
                            // {

                            //    var reoder_id =  checked[a].split('_');

                            //    $.ajax({
                            //             type:'POST',
                            //             url:'<?php echo base_url(); ?>Mms_ctrl/change_qty_details',
                            //             data:{
                            //                     'reoder_id':reoder_id[0]   
                            //                  },
                            //             dataType:'JSON',
                            //             success: function(data)
                            //             {
                                                                              
                                                                                     
                            //                             $.ajax({
                            //                                     type:'POST',
                            //                                     url:'<?php echo base_url(); ?>Mms_ctrl/update_reorder_report_change_quantity_history',   
                            //                                     data:{
                            //                                             'quantity_id':data.quantity_id,
                            //                                             'status':status
                            //                                          }, 
                            //                                     dataType:'JSON',
                            //                                     success: function(data)     
                            //                                     {   
                            //                                           console.log('counter->'+counter+' checked->'+checked.length);
                            //                                     }
                            //                                 });                                 
                                                
                            //             }     
                            //          });

                            // }
                            //      if((checked.length-1) == a )
                            //      {
                            //          Swal.fire({
                            //                          position: 'center',
                            //                          icon: 'success',
                            //                          title: 'adjustment successfully '+status,
                            //                          showConfirmButton: true                                           
                            //                    })     
                            //                    setTimeout(function() 
                            //                    {
                            //                            swal.close();
                            //                            location.reload();                                                     
                            //                    }, 3000);
                            //      }




                  }
              });  
      }

      //console.log(checked);
  }  


  function sug_reorder_input(id,proceed,column,enable_disable)
  {         
         reportTable.column(column).nodes().to$().find('input[id="'+id+'"]').each(function() 
         {   
            var inputed_qty   =  $(this).val();
            var placeholderValue = $(this).attr('placeholder');                  
            $(this).prop("disabled", enable_disable);    
            if(inputed_qty == placeholderValue)
            {
               proceed = false;
            }
         }); 

         return proceed;      
  }



   

  function save_checked_sug_qty() 
  {
     var checkedCheckboxes = [];            
     var for_update        = 0;
     reportTable.column(18).nodes().to$().find('input[type="checkbox"][class="checkbox_suggested_qty"]:checked').each(function() 
     {
        checkboxValue = $(this).val();
        if (typeof checkboxValue !== 'undefined') 
        {
             checkedCheckboxes.push(checkboxValue);  
             var reord_id = this.value.split('_');    

             var si = true;
             si     = sug_reorder_input('suggested_qty_input-'+reord_id[0], si , 17 , false);                  

             var dr = true;
             dr     = sug_reorder_input('suggested_qty_input-dr-'+reord_id[0], dr , 17 , false);

             if(si == false && dr == false)
             {
                  $(this).prop('checked', false);
                  sug_reorder_input('suggested_qty_input-'+reord_id[0], true , 17 , true);
                  sug_reorder_input('suggested_qty_input-dr-'+reord_id[0], true , 17 , true);
             }
             else 
             {
                  for_update +=1;                    
             }
        }
     });

     if(for_update > 0)
     {
         $("#report_modal").modal({backdrop: 'static',keyboard: false});
         var reoder_id             = '';
         var suggested_reorder_qty = '';
         var loader                = ' <center><img src="<?php echo base_url(); ?>assets/img/preloader.gif" style="padding-top:120px; padding-bottom:120px;"></center>';

         $('#report_body').html(loader);
         $(".custom-width-modal").css("width", "512px");
         $.ajax({
                      type:'POST',
                      url:'<?php echo base_url() ?>Mms_ctrl/suggested_qty_ui_v2',
                      data:{
                              'reorder_id':reoder_id,
                              'suggested_reorder_qty':suggested_reorder_qty  
                          },
                      dataType:'JSON',
                      success: function(data)
                      {
                         $("#report_body").html(data.html);
                         $("#footer_modal").html(data.buttons);
                         $("#report_label").html(data.label);
                      }     
         });
     }
     else 
     {
        swal_display('error','opps','please select an entry for adjustment');        
     }

      
  }

    

   
  //  function save_checked_sug_qty_() //old nga function 
  //  {
  //     var checked      = [];
  //     var negative_var = [];

  //     $('#main_checkbox').prop('checked', false);

  //     var table_id = 'consolidated-table';
      
  //     toggle_checkboxes(table_id, false);

  //    for (var i = 0; i < reportTable.page.info().pages; i++)
  //    {
  //       reportTable.page(i).column(18).nodes().to$().find('input[class="checkbox_suggested_qty"]:checked').each(function() 
  //       {
  //           if (!checked.includes(this.value))
  //           {
  //                //console.log(this.value);
  //               // checked.push(this.value);
  //                var reord_id = this.value.split('_');
  //                var orig_qty = reord_id[1];  
  //                var orig_drt_qty = reord_id[2];   

  //                // for (var a = 0; a < reportTable.page.info().pages; a++) 
  //                // {

  //                            reportTable.page(i).column(17).nodes().to$().find('input[id="suggested_qty_input-'+reord_id[0]+'"]').each(function() 
  //                            {
  //                               var inputed_qty = $(this).val();
  //                               //console.log('orig ->'+orig_qty+' inputed->'+inputed_qty);


  //                               var isDisabled = $(this).is(":disabled");

  //                               if(!isDisabled) 
  //                               {                                  
  //                                   checked.push(this.value);
  //                               }
                                


  //                               if(inputed_qty == orig_qty)
  //                               {
  //                                    $(this).prop('checked', false);
  //                               }

  //                               if(inputed_qty < 0)
  //                               {
  //                                    red_color('suggested_qty_input-'+reord_id[0]);
  //                                    negative_var.push(inputed_qty);
  //                               }                         
  //                            });


  //                            reportTable.page(i).column(17).nodes().to$().find('input[id="suggested_qty_input-dr-'+reord_id[0]+'"]').each(function() 
  //                            {
  //                               var inputed_qty = $(this).val();
  //                               //console.log('orig ->'+orig_qty+' inputed->'+inputed_qty);


  //                               var isDisabled = $(this).is(":disabled");

  //                               if(!isDisabled) 
  //                               {                                  
  //                                   checked.push(this.value);
  //                               }
                                


  //                               if(inputed_qty == orig_drt_qty)
  //                               {
  //                                    $(this).prop('checked', false);
  //                               }

  //                               if(inputed_qty < 0)
  //                               {
  //                                    red_color('suggested_qty_input-dr-'+reord_id[0]);
  //                                    negative_var.push(inputed_qty);
  //                               }                         
  //                            });


  //               // }


  //                   // Check negative values in other datatable pages
  //                   reportTable.on('draw', function() 
  //                   {
  //                        reportTable.page(i).column(17).nodes().to$().find('input[id="suggested_qty_input-'+reord_id[0]+'"]').each(function() 
  //                        {
  //                               var inputed_qty = $(this).val();

  //                               if(inputed_qty == orig_qty)
  //                               {
  //                                    $(this).prop('checked', false);
  //                               }

  //                               if(inputed_qty < 0)
  //                               {
  //                                    red_color('suggested_qty_input-'+reord_id[0]);
  //                                    //negative_var.push(inputed_qty);
  //                               }             
  //                        });


  //                        reportTable.page(i).column(17).nodes().to$().find('input[id="suggested_qty_input-dr-'+reord_id[0]+'"]').each(function() 
  //                        {
  //                               var inputed_qty = $(this).val();

  //                               if(inputed_qty == orig_drt_qty)
  //                               {
  //                                    $(this).prop('checked', false);
  //                               }

  //                               if(inputed_qty < 0)
  //                               {
  //                                    red_color('suggested_qty_input-dr-'+reord_id[0]);
  //                                    //negative_var.push(inputed_qty);
  //                               }             
  //                        });
  //                   });


  //           }
  //       });
  //    }



  //    //console.log(negative_var);


  //     if(negative_var.length >0)
  //     {
  //         swal_display('error','opps','negative values are not allowed');            
  //     }
  //     else 
  //     if(checked.length == 0)
  //     { 
  //         swal_display('error','opps','please select an entry for adjustment');
  //     }
  //     else 
  //     {
  //        $("#report_modal").modal({backdrop: 'static',keyboard: false});
  //        var reoder_id             = '';
  //        var suggested_reorder_qty = '';
  //        var loader                = ' <center><img src="<?php echo base_url(); ?>assets/img/preloader.gif" style="padding-top:120px; padding-bottom:120px;"></center>';

  //               $('#report_body').html(loader);
  //               $(".custom-width-modal").css("width", "512px");
  //               $.ajax({
  //                            type:'POST',
  //                            url:'<?php echo base_url() ?>Mms_ctrl/suggested_qty_ui_v2',
  //                            data:{
  //                                     'reorder_id':reoder_id,
  //                                     'suggested_reorder_qty':suggested_reorder_qty  
  //                                 },
  //                            dataType:'JSON',
  //                            success: function(data)
  //                            {
  //                                $("#report_body").html(data.html);
  //                                $("#footer_modal").html(data.buttons);
  //                                $("#report_label").html(data.label);
  //                            }     
  //                      });

  //     }


  //       // var pages = reportTable.page.info().pages;

  //       // for (var i = 0; i < pages; i++) {
  //       //     reportTable.page(i).column(18).nodes().to$().find('input[class="checkbox_suggested_qty"]:visible:checked').each(function() {
  //       //         // if(!checked.includes(this.value))
  //       //         // {
  //       //              checked.push(this.value);
  //       //         //}
  //       //     });
  //   //}

  //    //console.log(checked);
  // }






  function save_reason_v2()
  {       
      var checked   = [];
      var input_val = [];
      var input_dr_val = [];
      // reportTable.column(18).nodes().to$().find('input[class="checkbox_suggested_qty"]:checked').each(function() {
      // if(!checked.includes(this.value))
      // {
      //        //checked.push(this.value);
      //        var reord_id = this.value.split('_');
      //        reportTable.column(17).nodes().to$().find('input[id="suggested_qty_input-'+reord_id[0]+'"]').each(function() 
      //        {
      //            input_val.push($(this).val());
      //        });
      // }
      // });


      reportTable.column(18).nodes().to$().find('input[class="checkbox_suggested_qty"]:checked').each(function() {
      if(!checked.includes(this.value))
      {
             //checked.push(this.value);
             var chebox_val = this.value;
             var reord_id = this.value.split('_');
             reportTable.column(17).nodes().to$().find('input[id="suggested_qty_input-'+reord_id[0]+'"]').each(function() 
             {
                // input_val.push($(this).val());

                  var isDisabled = $(this).is(":disabled");

                  if(!isDisabled) 
                  {   
                         input_val.push($(this).val());                               
                         checked.push(chebox_val);
                  }   
             });


             reportTable.column(17).nodes().to$().find('input[id="suggested_qty_input-dr-'+reord_id[0]+'"]').each(function() 
             {
                // input_val.push($(this).val());

                  var isDisabled = $(this).is(":disabled");

                  if(!isDisabled) 
                  {   
                         input_dr_val.push($(this).val());                                                       
                  }   
             });
      }
      });


       

      var checkedCheckboxes = $(".reasons:checked");
      var checked_reasons   = [];      
      checkedCheckboxes.each(function() 
      {
          // Access each checked checkbox using $(this)
          //console.log($(this).attr("id")); // Output the ID of each checked checkbox                    
          checked_reasons.push($(this).val());

      });




// ---------------------------------------

      

     // for (var i = 0; i < reportTable.page.info().pages; i++)
     // {
     //    reportTable.page(i).column(18).nodes().to$().find('input[class="checkbox_suggested_qty"]:checked').each(function() 
     //    {
     //        if (!checked.includes(this.value))
     //        {
                
     //             var reord_id = this.value.split('_');
     //             var orig_qty = reord_id[1];  

              
                

     //                         reportTable.page(i).column(17).nodes().to$().find('input[id="suggested_qty_input-'+reord_id[0]+'"]').each(function() 
     //                         {
     //                            var inputed_qty = $(this).val();
     //                            var isDisabled = $(this).is(":disabled");

     //                            if(!isDisabled) 
     //                            {                                  
     //                                checked.push(inputed_qty);
     //                            }                               

                       
     //                         });
               


     //                // Check negative values in other datatable pages
     //                reportTable.on('draw', function() 
     //                {
     //                     reportTable.page(i).column(17).nodes().to$().find('input[id="suggested_qty_input-'+reord_id[0]+'"]').each(function() 
     //                     {
     //                            var inputed_qty = $(this).val();

                                  
     //                     });
     //                });


     //        }
     //    });
     // }

// -------------------------------------------------



                
      if(checked_reasons.length == 0)
      {
          swal_display('error','opps','please select a reason for adjustment');
      }
      else 
      {

            Swal.fire({
                                  title: 'Are you sure',
                                  text: "You want to save changes?",
                                  icon: 'warning',
                                  showCancelButton: true,
                                  confirmButtonColor: '#3085d6',
                                  cancelButtonColor: '#d33',
                                  confirmButtonText: 'Yes'
                            }).then((result) => 
                            { 
                                if(result.isConfirmed) 
                                {     
                                     // loader();
                                     for(var a=0;a<checked.length;a++)
                                     {
                                             var entry                    = checked[a].split("_");
                                             var reorder_id               = entry[0];
                                             var suggested_reorder_qty    = entry[1];
                                             var suggested_reorder_dr_qty = entry[2];
                                              
                                             var inputed_suggested_qty    = input_val[a];
                                             var inputed_suggested_dr_qty = input_dr_val[a];    

                                            // console.log(inputed_suggested_qty);
               
                                             $.ajax({
                                                      type:'POST',
                                                      url:'<?php echo base_url(); ?>Mms_ctrl/save_reason',
                                                      data:{
                                                              'checked_reasons': JSON.stringify(checked_reasons),   
                                                              'reorder_id':reorder_id,
                                                              'suggested_reorder_qty':suggested_reorder_qty,
                                                              'suggested_reorder_dr_qty':suggested_reorder_dr_qty,
                                                              'inputed_suggested_qty':inputed_suggested_qty ,
                                                              'inputed_suggested_dr_qty':inputed_suggested_dr_qty 
                                                           },
                                                      dataType:'JSON',
                                                      success: function(data)
                                                      {
                                                         
                                                      }     
                                                    });                                                        

                                     }

                                     Swal.fire({
                                                                           position: 'center',
                                                                           icon: 'success',
                                                                           title: 'Entries successfully updated',
                                                                           showConfirmButton: true                                           
                                               })   
                                     setTimeout(function() 
                                     {
                                          swal.close();
                                          location.reload();                                                     
                                     }, 3000);
                                     $("#report_modal").modal('hide');
                                 }
                         }); 

                                     
       }



      //console.log(checked);   
  }


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

   function generate_textfile(reorder_batch,vend_type)         
   {
         var vend_exp = vend_type.split(',');
         if($("#nav_si_doc_no").val() == '' && vend_exp.includes('SI'))
         {
             swal_display('error','opps','please provide SI navision document number');    
             red_color('nav_si_doc_no');
         }
         else
         if($("#nav_dr_doc_no").val() == '' && vend_exp.includes('DR'))
         {
             swal_display('error','opps','please provide DR navision document number');    
             red_color('nav_dr_doc_no');
             $("#nav_si_doc_no").css('border-color', ''); // Remove red color if not negative
         }
         else 
         {
             $("#nav_doc_no").css('border-color', ''); // Remove red color if not negative
             $("#nav_dr_doc_no").css('border-color', ''); // Remove red color if not negative
              var reorder_batch_arr  = [];
              reorder_batch_arr.push(reorder_batch);

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
                                 for(var a=0;a<vend_exp.length;a++)                           
                                 {                                    
                                     io.open("POST", "<?php echo base_url();?>Mms_ctrl/generate_textfile", 
                                     {                               
                                        'reorder_batch_arr':JSON.stringify(reorder_batch_arr),
                                        'vend_type':vend_exp[a],                                        
                                        'si_input':$("#nav_si_doc_no").val(),
                                        'dr_input':$("#nav_dr_doc_no").val()
                                     },"_blank");  

                                     if(vend_exp.length == (a+1))
                                     {
                                         location.reload();  
                                     }
                                 }


                             }

                        });            
         }
   }


   function generate_pdf(reorder_batch)
   {
         allRowsData =  reportTable.rows().data().toArray();
         let tableHeader = [];

         $(reportTable.table().header()).find('th').each(function() 
         {
              tableHeader.push($(this).text().trim());
         });
         
         io.open('POST', '<?php echo base_url('Mms_ctrl/generate_pdf'); ?>', {
                                                                                             allRowsData: JSON.stringify(allRowsData),
                                                                                             tableHeader: JSON.stringify(tableHeader),
                                                                                             'reorder_batch':reorder_batch                                                                          
                                                                                        }, '_blank'); 
         
   }


   function view_ave_sale_per_month(reorder_batch,month,filter)
   {
         
        var loader  = ' <center><img src="<?php echo base_url(); ?>assets/img/preloader.gif" style="padding-top:120px; padding-bottom:120px;"></center>';
        $('#report_body').html(loader);
        $.ajax({
                     type:'POST',
                     url:'<?php echo base_url(); ?>Mms_ctrl/view_ave_sale_per_month',
                     data:{
                             'reorder_batch':reorder_batch,
                             'month':month,
                             'filter':filter
                          },
                     dataType:'JSON',
                     success: function(data)
                     {
                         $("#footer_modal").html(data.buttons);
                         $("#report_body").html(data.html);
                     }     
               });
   }

  </script>