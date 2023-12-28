<!-- upload_ui.php -->

<!DOCTYPE html>
<html>
<head>
        <meta charset="utf-8">
        <title>DATA UPLOAD</title>
        <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">     
        <link href="<?php echo base_url(); ?>assets/css/datatables.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo base_url(); ?>assets/css/googleapis.css" rel="stylesheet" type="text/css"/>
        <link rel="<?php echo base_url();  ?>assets/css/sweetalert.css">
        


<!--imported -->

        <!-- <link rel="shortcut icon" type="image/png" href="../assets/img/latest.png"> -->
        <link href="<?php echo base_url(); ?>assets/css/site.min.css" rel="stylesheet"/>
        <link href="<?php echo base_url(); ?>assets/progress_bar/css/bootstrap.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>assets/progress_bar/css/font-awesome.css" rel="stylesheet">
        <script src="<?php echo base_url(); ?>assets/progress_bar/css/bootstrap-dialog.css">
        </script><link href="<?php echo base_url(); ?>assets/progress_bar/css/custom.css" ?v2="" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/progress_bar/css/bootstrap-dialog.css">
        <link href="<?php echo base_url(); ?>assets/progress_bar/css/bootstrap-datetimepicker.css?ts=<?=time()?>&quot;" rel="stylesheet">
        <link href="<?php echo base_url(); ?>assets/progress_bar/plugins/icheck-1.x/skins/square/blue.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>assets/progress_bar/css/dormcss.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>assets/progress_bar/plugins/icheck-1.x/skins/square/blue.css" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/progress_bar/js/jquery-ui/jquery-ui.css">
        <link href="<?php echo base_url(); ?>assets/progress_bar/alert/css/alert.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>assets/progress_bar/alert/themes/default/theme.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>assets/progress_bar/css/extendedcss.css?ts=<?=time()?>&quot;" rel="stylesheet">
        <!-- <link href="<?php echo base_url(); ?>assets/progress_bar/js/dataTables/jquery.dataTables.min.css?ts=<?=time()?>&quot;" rel="stylesheet"> -->
        <script src="<?php echo base_url(); ?>assets/progress_bar/js/jquery-1.10.2.js?2"></script>
        <script src="<?php echo base_url(); ?>assets/progress_bar/js/bootstrap.min.js?2"></script>
        <script src="<?php echo base_url(); ?>assets/progress_bar/js/bootstrap-dialog.js?2"></script>

        <script src="<?php echo base_url(); ?>assets/progress_bar/js/jquery.metisMenu.js?2"></script>
        <script src="<?php echo base_url(); ?>assets/progress_bar/js/dataTables/jquery.dataTables.min.js?2" type="text/javascript" charset="utf-8"></script>
        <script src="<?php echo base_url(); ?>assets/progress_bar/js/dataTablesDontDelete/jquery.dataTables.min.js?2" type="text/javascript" charset="utf-8"></script>
        <script src="<?php echo base_url(); ?>assets/progress_bar/js/ebsdeduction_function.js?<?php echo time()?>"></script>
        <script src="<?php echo base_url(); ?>assets/js/sweetalert.js"></script>    
        <script src="<?php echo base_url(); ?>assets/js/sweetalert2.all.min.js"></script>


<!-- end of imported -->
</head>
<body>
    <h1></h1>
 
    <div class="col-md-12" style="margin-top:0%;padding:3px;">
		<div class="col-md-12 pdd_1"></div>         
        <button   class="back_button btn btn-danger" onclick='back_to_posting()'  style='display:none;'>back to ebs</button> <div class="col-md-6 col-md-offset-3" style="padding: 10% 0%;">
                <div class="row" style="padding-left: 18px;">                    
                   <label class="col-md-12 pdd" style="margin:0px">
                        <img src="<?php echo base_url();?>assets/icon_index/upload_im.PNG" width="30">
                        UPLOADING FILE CONTENT
                        &nbsp;&nbsp;<img src="<?php echo base_url();?>assets/img/giphy.gif" height="20">
                    </label>
                    <span class="col-md-12 pdd fnt13 filenum">Completed file: 0 file(s)</span>
                    <span class="col-md-7 pdd fnt13 status">Status: 0% Complete </span>
                    <!-- <span class="col-md-4 pdd fnt13 toright">Processed Row:</span> -->
                    <span class="col-md-4 pdd fnt13 toright rowprocess"> 0</span>
                </div>
                <div class="progress row" style="height: 26px;margin:0px; padding:2px;"> 
                    <div id="percontent" class="progress-bar progress-bar-pimary" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                    </div>
                </div>
                <span class="col-md-12 pdd fnt13 empname" >Employee: </span>
                <span class="col-md-12 pdd fnt13 filename"></span>
                 
          </div>
     </div>   
     <?php
     			$memory_limit = ini_get('memory_limit');
			    ini_set('memory_limit',-1);
			    ini_set('max_execution_time', 0);
			    $store_loop = '';  
			    $rowproC     = 1;
			    $total_files = count($fileNames)+3; 
			    
			    
			    $current_user_login =  $this->Mms_mod->get_user_connection($_SESSION['user_id']);


                 $insert_batch['user_id']      = $_SESSION['user_id'];    
                 $insert_batch['store_id']     = $current_user_login[0]['store_id'];      
                 $insert_batch['status']       = 'Pending';  
                 $insert_batch['date_tag']     = $d_tag;
                 $insert_batch['group_code_']  = $group_code;
                 $insert_batch['date_generated'] = date('Y-m-d H:i:s');
                 $table                          = 'reorder_report_data_batch';
                 $reorder_batch                  = $this->Mms_mod->insert_table($table,$insert_batch);  
                 ob_start(null, 5024);


               

                  
                
                 $reorder_list = array();          

			     for($a=0;$a<count($file_contents);$a++)
                 {

                 	  if($rowproC >0 && $total_files >0)
                      {                                    
                         $percent = intval($rowproC/$total_files * 100)."%";                    
                      }
                      else 
                      {
                         $percent = "100%";
                      }	
                      



                      $RESS2    = '';                                                                   
                      $RESS2    = strip_tags($file_contents[$a]);          


                      if(strstr($RESS2,'Re-order'))
                      {
                           $reorder_number =  $this->Mms_mod->extract_reorder($RESS2,$store_array[$a],$reorder_batch);      
                           array_push($reorder_list,array("store"=>$store_array[$a],'reorder_number'=>$reorder_number));       
                      }
                     


                      
                      echo '<script language="JavaScript">';
                      echo '$("span.filename").text("Text FIle Name -");';
                      echo '$("div#percontent").css({"width":"'.$percent.'"});';
                      echo '$("span.status").text("Status: '.$percent.' Complete");';
                      echo '$("span.rowprocess").text("Processed Row: '.$rowproC++.' out of '.$total_files.'");';
                      echo '$("span.empname").text("Entry: ");';
                      echo '</script>';                    
	                  str_repeat(' ',1024*64);
                      flush();
                      ob_flush();
                      usleep(100);                                      
                      
                                   
                 }


                 for($a=0;$a<count($file_contents);$a++)
                 {

                 	  if($rowproC >0 && $total_files >0)
                      {                                    
                         $percent = intval($rowproC/$total_files * 100)."%";                    
                      }
                      else 
                      {
                         $percent = "100%";
                      }	
                      



                      $RESS2    = '';                                                                   
                      $RESS2    = strip_tags($file_contents[$a]);          


                   
                      if(strstr($RESS2,'Item Vendor Sales') )
                      {                      	
                      	  //if(isset($reorder_number))
                      	  foreach($reorder_list as $reord)
                      	  {
                      	  	 if($reord['store'] == $store_array[$a])
                      	  	 {                      	  	 	 
	                          	 $this->Mms_mod->extract_vendor($RESS2,$store_array[$a],$reord['reorder_number'],$reorder_batch);                                 
                      	  	 }
                      	  }
                      }   


                      
                      echo '<script language="JavaScript">';
                      echo '$("span.filename").text("Text FIle Name -");';
                      echo '$("div#percontent").css({"width":"'.$percent.'"});';
                      echo '$("span.status").text("Status: '.$percent.' Complete");';
                      echo '$("span.rowprocess").text("Processed Row: '.$rowproC++.' out of '.$total_files.'");';
                      echo '$("span.empname").text("Entry: ");';
                      echo '</script>';                    
	                  str_repeat(' ',1024*64);
                      flush();
                      ob_flush();
                      usleep(100);                                      
                      
                                   
                 }







                         $head           = $this->Mms_mod->get_entries_reorder_report_data_header_final($reorder_batch);
				         $store_handled  = $this->Mms_mod->get_entries_reorder_report_data_header_final_by_bu($reorder_batch);
				         $store          = trim($store_handled[0]['value_']);
				         $report_details = $this->Mms_mod->generate_reorder_report_mod($reorder_batch,$store,$store_handled[0]['user_id']);
				        
				         // get the past 3 month years from the current month
				         $past_3_month_years = array();
				         for($i = 1; $i <= 3; $i++) 
				         {
				             $past_month_year      = date('Y-m', strtotime("-{$i} month", strtotime($store_handled[0]['date_generated'])));
				             $past_3_month_years[] = $past_month_year;
				         }

// line 1
				if($rowproC >0 && $total_files >0)
                {                                    
                   $percent = intval($rowproC/$total_files * 100)."%";                    
                }
                else 
                {
                   $percent = "100%";
                }	        	

                 echo '<script language="JavaScript">';
                 echo '$("span.filename").text("retrieving files from SQL");';
                 echo '$("div#percontent").css({"width":"'.$percent.'"});';
                 echo '$("span.status").text("Status: '.$percent.' Complete");';
                 echo '$("span.rowprocess").text("Processed Row: '.$rowproC++.' out of '.$total_files.'");';
                 echo '$("span.empname").text("Entry: ");';
                 echo '</script>';
                 str_repeat(' ',1024*64);
                 flush();
                 ob_flush();
                 usleep(100);     
                 

				//*******************************************************pagkuha sa mga PO nga gikan sa SQL*********************************************************************************** 
				         $final_po_list = array();				        
                       
                         $databse_id    = $current_user_login[0]['databse_id'];

                         $sql_po_list   = $this->Mms_mod->get_sql_po(trim($head["supplier_code"]),$past_3_month_years[2],$past_3_month_years[0],'',$databse_id);
				         
				         if(!empty($sql_po_list))
				         {        
				             foreach($sql_po_list as $sql)
				             {            
				                  array_push($final_po_list,array("vendor"=>$sql['vendor'],'document_no'=>$sql['document_no'],'date'=>$sql['date'],'pending_qty'=>$sql['pending_qty'],'item_code'=>$sql['item_code'],'uom'=>$sql['uom']));                                     
				                
				             }
				         }
				//********************************************************************************************************************************************************************************* 
//line 2

				if($rowproC >0 && $total_files >0)
                {                                    
                   $percent = intval($rowproC/$total_files * 100)."%";                    
                }
                else 
                {
                   $percent = "100%";
                }	        	

                 echo '<script language="JavaScript">';
                 echo '$("span.filename").text("retrieving files from textfiles");';
                 echo '$("div#percontent").css({"width":"'.$percent.'"});';
                 echo '$("span.status").text("Status: '.$percent.' Complete");';
                 echo '$("span.rowprocess").text("Processed Row: '.$rowproC++.' out of '.$total_files.'");';
                 echo '$("span.empname").text("Entry: ");';
                 echo '</script>';
                 str_repeat(' ',1024*64);
                 flush();
                 ob_flush();
                 usleep(100);      
                       	
// ----------------------------------------------pagkuha sa mga PO nga gikan sa textile --------------------------------------------------------------------------------------------
				         $partial_po_list = array();
				         $item_code       = '';
// function sa pag kuha sa PO +++++++++++++++++++++++++++++++++++++++++++

				         $store_details = $this->Mms_mod->get_a_store($current_user_login[0]['value_']);
                         $get_dir       = $this->Mms_mod->get_po_directory($store_details[0]['po_db_id']);

                         $dir      = $get_dir[0]['directory'];
				         $dir      = str_replace('\\\\','\\\\',$dir);
				         $dir      = str_replace('\\','\\',$dir);
				         $username = $get_dir[0]['username'];
				         $password = $get_dir[0]['password'];
				          
				         // use the 'net use' command to map the network drive with the specified credentials
				         system("net use {$dir} /user:{$username} {$password} >nul");


				           // use the 'opendir' function to open the directory
				          if ($handle = opendir($dir."\\")) 
				          {  
				          		  // iterate through each entry in the directory
								    while (($entry = readdir($handle)) !== false) 
								    {
								    	if($rowproC >0 && $total_files >0)
										{                                    
										   $percent = intval($rowproC/$total_files * 100)."%";                    
										}
										else 
										{
										   $percent = "100%";
										}	 
								    
								        $total_files++;

				                       
								        echo '<script language="JavaScript">';
										echo '$("span.filename").text("retrieving files from textfiles");';
										echo '$("div#percontent").css({"width":"'.$percent.'"});';
										echo '$("span.status").text("Status: '.$percent.' Complete");';
										echo '$("span.rowprocess").text("Processed Row: '.$rowproC.' out of '.$total_files.'");';
										echo '$("span.empname").text("Entry: ");';
										echo '</script>';  
										str_repeat(' ',1024*64);
                     					flush(); 
										ob_flush();
									    usleep(100);	  
								    }

				          }




				          // use the 'opendir' function to open the directory
				          if ($handle = opendir($dir."\\")) 
				          {           
				                // iterate through each entry in the directory
				                $po_arr = array();
				                while (($entry = readdir($handle)) !== false) 
				                {

				                	if($rowproC >0 && $total_files >0)
									{                                    
									   $percent = intval($rowproC/$total_files * 100)."%";                    
									}
									else 
									{
									   $percent = "100%";
									}	 

				                		
				                    // check if the entry is a file with the ".cent-dc" extension
				                    $date_modified = date("Y-m-d H:i:s", filemtime($dir . "\\" . $entry));   

				                    if (is_file($dir . "\\" . $entry) && pathinfo($entry, PATHINFO_EXTENSION) == $store_details[0]['file_extension'] )                  
				                    { 
				                        // process the file
				                        if(date('Y-m',strtotime(date($date_modified))) <= date('Y-m',strtotime(date($past_3_month_years[0]))) &&  date('Y-m',strtotime(date($date_modified))) >= date('Y-m',strtotime(date($past_3_month_years[2]))) )
				                        {

				                              //echo $entry."---->date modified:".$date_modified."<br>";
				                              $header = array();
				                              $fh = fopen($dir."\\".$entry,'r');
				                              while ($line = fgets($fh)) 
				                              {
				                                    if ( !(strstr($line, '[HEADER]') || strstr($line, '[LINES]'))) 
				                                    {                                        
				                                        $line     =  str_replace('"','',$line);
				                                        $line_exp = explode("|",$line); 

				                                        if(count($line_exp) == 7) //if header
				                                        {
				                                             //array_push($header,array('document_no'=>$line_exp[0],'date'=>$line_exp[1],'vendor'=>$line_exp[5]) );          
				                                             array_push($header,array('document_no'=>$line_exp[0],'date'=>$line_exp[1],'vendor'=>$line_exp[5]) );          
				                                        }
				                                        if(count($line_exp) == 11) //if lines                                        
				                                        {

				                                             if($item_code == '')  
				                                             {
				                                                 array_push($po_arr,array('document_no'=>$header[0]['document_no'],'date'=>$header[0]['date'],'vendor'=>$header[0]['vendor'],'item_code'=>$line_exp[1],'pending_qty'=>$line_exp[2],'uom'=>$line_exp[4]));        
				                                             } 
				                                             else 
				                                             {
				                                                 if($item_code == $line_exp[1])
				                                                 {
				                                                     array_push($po_arr,array('document_no'=>$header[0]['document_no'],'date'=>$header[0]['date'],'vendor'=>$header[0]['vendor'],'item_code'=>$line_exp[1],'pending_qty'=>$line_exp[2],'uom'=>$line_exp[4]));         
				                                                 }
				                                             }
				                                             //array_push($po_lines,array('item_code'=>$line_exp[1],'pending_qty'=>$line_exp[2]));        
				                                             //array_push($po_arr,array_merge($header, $po_lines));                                                            
				                                        }  
				                                    }
				                              }											    				                               
				                        } 
				                    }


				                    echo '<script language="JavaScript">';
									echo '$("span.filename").text("retrieving files from textfiles");';
									echo '$("div#percontent").css({"width":"'.$percent.'"});';
									echo '$("span.status").text("Status: '.$percent.' Complete");';
									echo '$("span.rowprocess").text("Processed Row: '.$rowproC++.' out of '.$total_files.'");';
									echo '$("span.empname").text("Entry: ");';
									echo '</script>';
									str_repeat(' ',1024*64);
									flush();
									ob_flush();
									usleep(100);
				                }  

				               
				                //return $po_arr;
				                //ini_set('memory_limit',$memory_limit );

				          } 
				          else
				          {
				              // handle the error
				              echo "Failed to open directory: {$dir}\n";
				          }
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++





				         //$po_arr          = $this->Mms_mod->get_pending_po($store,$past_3_month_years,'');  				          
				         if(!empty($po_arr))
				         {        
				            foreach($po_arr as $po)
				            {
				                 if( (trim($po['vendor']) == trim($head["supplier_code"]) ) && !in_array($po['document_no'], array_column($final_po_list, "document_no")) )
				                 {              
				                     array_push($partial_po_list,array("vendor"=>$po['vendor'],'document_no'=>$po['document_no'],'date'=>$po['date'],'pending_qty'=>$po['pending_qty'],'item_code'=>$po['item_code'],'uom'=>$po['uom']));                   
				                 }
				            }
				         }
				// -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

//line 3
				if($rowproC >0 && $total_files >0)
                {                                    
                   $percent = intval($rowproC/$total_files * 100)."%";                    
                }
                else 
                {
                   $percent = "100%";
                }	        	

                 echo '<script language="JavaScript">';
                 echo '$("span.filename").text("consolidating PENDING PO");';
                 echo '$("div#percontent").css({"width":"'.$percent.'"});';
                 echo '$("span.status").text("Status: '.$percent.' Complete");';
                 echo '$("span.rowprocess").text("Processed Row: '.$rowproC++.' out of '.$total_files.'");';
                 echo '$("span.empname").text("Entry: ");';
                 echo '</script>';
                 str_repeat(' ',1024*64);
                 flush();
                 ob_flush();
                 usleep(100);              
                  
				// ****************************************gitapo ang mga po nga nkuha gikan sa SQL ug  po nga gikuha gikan sa textfile **************************************************************
				         if(!empty($partial_po_list))
				         {
				             foreach($partial_po_list as $partial)
				             {             
				                 array_push($final_po_list,array("vendor"=>$partial['vendor'],'document_no'=>$partial['document_no'],'date'=>$partial['date'],'pending_qty'=>$partial['pending_qty'],'item_code'=>$partial['item_code'],'uom'=>$partial['uom']));                                     
				                
				             }  
				         }
				// ************************************************************************************************************************************************************************************




				         foreach ($report_details as $details) 
				         {
				             $pending_qty = 0;

				             foreach($final_po_list as $fin)
				             {
				                 if(trim($fin['item_code']) == trim($details["item_code"]))
				                 {

				                      $document_no = trim($fin['document_no']);
				                      $item_code   = trim($fin['item_code']);
				                      $uom         = trim($fin['uom']);
				                      $date        = date('Y-m-d',strtotime($fin['date']));


				                      $po_row = $this->Mms_mod->check_smgm( $document_no,$item_code,$uom,$head['databse_id']);                       
				                      if(empty($po_row) )
				                      {
				                           //$pending_qty += $fin['pending_qty'];
				                           $pending_qty = $fin['pending_qty'];
				                           //$this->insert_reorder_report_data_po($document_no,$item_code,$details["all_ave_sales"],$details["quantity_on_hand"],$pending_qty,$reorder_batch);
				                      }
				                      else 
				                      {
				                          foreach($po_row as $po)
				                          {
				                              //$pending_qty += $po['pending_qty'];
				                              $pending_qty = $po['pending_qty'];
				                          }
				                      }

				                      $this->Mms_mod->insert_reorder_report_data_po($document_no,$item_code,$details["all_ave_sales"],$details["quantity_on_hand"],$pending_qty,$reorder_batch,$date,$uom);                              
				                 }
				             }

				            
				         }

 			echo '
 				  
 				 <script>
 				 		
 				 		setTimeout(function()
                        {		 
	 				 		Swal.fire({
	                                                      position: "center",
	                                                      icon: "success",
	                                                      title: "Data Successfully Imported",
	                                                      showConfirmButton: true                                           
	                                                    })
					    },2000);  								
                        setTimeout(function()
                        {
                             window.location.href = "'.base_url().'Mms_ctrl/mms_ui/2";

                        },5000); 
 			      </script>';	
 			   

                 ini_set('memory_limit',$memory_limit );
 
       ?>	

    </div> 	






    <!-- Rest of the content... -->
</body>
</html>