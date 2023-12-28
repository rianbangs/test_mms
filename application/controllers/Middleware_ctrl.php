<?php
/**
 * 
 */
class Middleware_ctrl extends CI_Controller
{
     function __construct()
     {
           parent::__construct();
           $this->load->model('Mms_mod');    
     }



     
     public function textfile_middleware()
     {
         $memory_limit = ini_get('memory_limit');
         ini_set('memory_limit',-1);
         ini_set('max_execution_time', 0);


         // get the past 3 month years from the current month
         $past_3_month_years = array();
         for($i = 1; $i <= 4; $i++) 
         {             
             $html_date = date('Y-m-01', strtotime('first day of next month'));
             $past_month_year      = date('Y-m', strtotime("-{$i} month", strtotime($html_date)));
             $past_3_month_years[] = $past_month_year;           
         }      


         $from_day = date('Y-m',strtotime(date($past_3_month_years[3])));
         $to_day   = date('Y-m',strtotime(date($past_3_month_years[0])));

          
         echo ' <!DOCTYPE html>
                    <html>
                    <head>
                            <meta charset="utf-8">
                            <title>DATA UPLOAD</title>
                            <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">                                     
                            
                          
                            <link href="'.base_url().'assets/progress_bar/css/bootstrap.css" rel="stylesheet">                      
                            <link rel="stylesheet" href="'. base_url().'assets/progress_bar/js/jquery-ui/jquery-ui.css">
                            <link href="'. base_url().'assets/progress_bar/alert/css/alert.css" rel="stylesheet">
                            <link href="'. base_url().'assets/progress_bar/alert/themes/default/theme.css" rel="stylesheet">
                            <link href="'. base_url().'assets/progress_bar/css/extendedcss.css?ts=<?=time()?>&quot;" rel="stylesheet">        
                            <script src="'. base_url().'assets/progress_bar/js/jquery-1.10.2.js?2"></script>
                            <script src="'. base_url().'assets/progress_bar/js/bootstrap.min.js?2"></script>
                            <script src="'. base_url().'assets/progress_bar/js/bootstrap-dialog.js?2"></script>

                            <script src="'. base_url().'assets/progress_bar/js/jquery.metisMenu.js?2"></script>
                            <script src="'. base_url().'assets/progress_bar/js/dataTables/jquery.dataTables.min.js?2" type="text/javascript" charset="utf-8"></script>
                            <script src="'. base_url().'assets/progress_bar/js/dataTablesDontDelete/jquery.dataTables.min.js?2" type="text/javascript" charset="utf-8"></script>
                            <script src="'. base_url().'assets/progress_bar/js/ebsdeduction_function.js?<?php echo time()?>"></script>
                            <script src="'. base_url().'assets/js/sweetalert.js"></script>    
                            <script src="'. base_url().'assets/js/sweetalert2.all.min.js"></script>
                    
                    </head>   
                     
                    
                    <div class="row" style="margin-left: 22px;">                       
                                <div class="row" >                    
                                   <label class="col-md-12 pdd" style="margin:0px">
                                        <img src="'.base_url().'assets/icon_index/upload_im.PNG" width="30">                                         
                                        &nbsp;&nbsp;<img src="'.base_url().'assets/img/giphy.gif" height="20">
                                    </label>
                                    
                                    <span class="col-md-7 pdd fnt13 status">Status: 0% Complete </span>
                                    <!-- <span class="col-md-4 pdd fnt13 toright">Processed Row:</span> -->
                                    <span class="col-md-4 pdd fnt13 toright rowprocess"> 0</span>
                                </div>
                                <div class="progress row" style="height: 26px;margin:0px; padding:2px;"> 
                                    <div id="percontent" class="progress-bar progress-bar-pimary" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                                    </div>
                                </div>
                                <span class="col-md-12 pdd fnt13 empname" >Entry: </span>
                                <span class="col-md-12 pdd fnt13 filename"></span>                        
                     </div>   

                     ';
             flush();
             ob_flush();       

             $directory_list = $this->Mms_mod->get_all_po_directory_by_store();         
             $total_files    = count($directory_list); 
             $rowproC        = 1;


              
             foreach($directory_list as $get_dir)
             {
                 if($rowproC >0 && $total_files >0)
                 {                                    
                     $percent = intval($rowproC/$total_files * 100)."%";                    
                 }
                 else 
                 {
                     $percent = "100%";
                 }

                 $mms_dir           = $get_dir['mms_directory'];
                 $mms_dir           = str_replace('\\\\','\\\\',$mms_dir);
                 $mms_dir           = str_replace('\\','\\',$mms_dir);
                 $mms_username      = $get_dir['mms_dir_username'];
                 $mms_password      = $get_dir['mms_dir_password'];
                             
                 //use the 'net use' command to map the network drive with the specified credentials
                 system("net use {$mms_dir} /user:{$mms_username} {$mms_password} >nul");

                 $to_dir      = str_replace('\\\\', '-', $mms_dir);
                 $to_dir      = str_replace('\\', '-', $to_dir);


                 $directory_arr = array(
                                          array("dir"=>$get_dir['directory'], "username"=>$get_dir['username'], "password"=> $get_dir['password'],"file_extension"=>$get_dir['file_extension']), 
                                          array("dir"=>$get_dir['archive_directory'], "username"=>$get_dir['archive_username'], "password"=>$get_dir['archive_password'],"file_extension"=>$get_dir['file_extension']."-PST")
                                        );

                 foreach($directory_arr as $dir_arr)
                 {
                     $dir           = $dir_arr['dir'];
                     $dir           = str_replace('\\\\','\\\\',$dir);
                     $dir           = str_replace('\\','\\',$dir);
                     $username      = $dir_arr['username'];
                     $password      = $dir_arr['password'];
                     $from_dir      = str_replace('\\\\', '-', $dir);
                     $from_dir      = str_replace('\\', '-', $from_dir);
                     
                     //use the 'net use' command to map the network drive with the specified credentials
                     system("net use {$dir} /user:{$username} {$password} >nul");


                     if ($handle = opendir($dir."\\")) 
                     {
                         // Use glob to find file extensions files in the opened directory
                         $txtFiles = glob($dir . "\\SMGM*." . $dir_arr['file_extension']);


                         //check if naa ba siyay nakit an nga ingani nga extension
                         if(!empty($txtFiles)) 
                         {
                              // Filter the files based on date modified
                              $filteredFiles = array_filter($txtFiles, function ($txtFile)  use ($from_day, $to_day)
                              {
                                   // Get the date modified of the file in 'Y-m-d' format
                                   $dateModified = date('Y-m-d', filemtime($txtFile));
                                   // Check if the file was modified between $from_day and $to_day
                                   $from_ = date('Y-m-d', strtotime($from_day));
                                   $to_   = date('Y-m-t', strtotime($to_day));    
                                   // echo $from_." ---".$to_."<br>";                                                   

                                   return ($dateModified >= $from_ && $dateModified <= $to_);
                              });

                              echo '<script language="JavaScript">';    
                              echo '$("div#percontent").css({"width":"'.$percent.'"});';                                    
                              echo '$("span.filename").text("Copying textfile from '.$from_dir.'    TO      '.$to_dir.'");';
                              echo '$("span.rowprocess").text("Processed Row: '.$rowproC.' out of '.$total_files.'");';      
                              echo '</script>';
                              flush();
                              ob_flush();

                              //if naay nakit an nga mga files nga within sa  $from_day ug $to_day
                              if (!empty($filteredFiles)) 
                              {
                                  foreach ($filteredFiles as $txtFile) 
                                  {
                                      $file_name     = basename($txtFile);
                                      $exp_file_name = explode(".",$file_name);
                                      // echo $file_name."<br>";  

                                      $check_data['document_number'] = $exp_file_name[0];
                                      $check_po = $this->Mms_mod->get_reorder_po($check_data);

                                      $proceed  = false;

                                      if( (empty($check_po)) ||
                                          (!empty($check_po) && $check_po[0]['status'] != 'Cancel')
                                        )
                                      {
                                         $proceed = true;
                                      }
                                      else 
                                      {
                                         // Rename the file by adding an underscore to its name
                                         $newFileName = $file_name."_CANCELED";
                                         $newFilePath = $dir."\\".$newFileName;
                                         rename($dir."\\".$file_name, $newFilePath);  

                                         $search_header = $this->Mms_mod->$this->Mms_mod->search_mms_middleware_header('',$file_name,'');
                                         if(!empty($search_header))
                                         {
                                             $table = 'mms_middleware_header';
                                             $column_data = array();
                                             $column_data['status']          = 'CANCELED';
                                             $column_filter['textfile_name'] = $file_name;
                                             $this->Mms_mod->update_mdl_table($table,$column_data,$column_filter);
                                         }

                                      }


                                      if($proceed == true)
                                      {            
                                                          
                                          echo '<script language="JavaScript">';
                                          echo '$("div#percontent").css({"width":"'.$percent.'"});';
                                          echo '$("span.empname").text("Entry: ' . $file_name . '");';
                                          echo '$("span.filename").text("Copying textfile from '.$from_dir.'    TO      '.$to_dir.'");';
                                          echo '$("span.rowprocess").text("Processed Row: '.$rowproC.' out of '.$total_files.'");';      
                                          echo '</script>';  
                                          flush();
                                          ob_flush();                                 

                                          // Copy the file to the specified directory
                                          // $destinationDirectory = '\\\\172.16.161.206\\MMS_Txt\\PO\\';

                                          $newFilePath = $mms_dir.$file_name;
                                          copy($txtFile, $newFilePath);                                     
                                      }

                                      echo '<script language="JavaScript">';
                                      echo '$("div#percontent").css({"width":"'.$percent.'"});';                                      
                                      echo '$("span.filename").text("Copying textfile from '.$from_dir.'    TO      '.$to_dir.'");';
                                      echo '$("span.rowprocess").text("Processed Row: '.$rowproC.' out of '.$total_files.'");';      
                                      echo '</script>';   
                                      flush();
                                      ob_flush();
                                 }
                              }
                          }
                     } 
                     else 
                     {
                        // Handle the error
                        echo "Failed to open directory: {$dir}\n";
                     }  

                     echo '<script language="JavaScript">';        
                     echo '$("div#percontent").css({"width":"'.$percent.'"});';                                
                     echo '$("span.filename").text("Copying textfile from '.$from_dir.'    TO      '.$to_dir.'");';
                     echo '$("span.rowprocess").text("Processed Row: '.$rowproC.' out of '.$total_files.'");';      
                     echo '</script>'; 
                     flush();
                     ob_flush(); 

                 }

                 echo '<script language="JavaScript">';
                 echo '$("span.filename").text("Copying textfile from '.$from_dir.'    TO      '.$to_dir.'");';
                 echo '$("div#percontent").css({"width":"'.$percent.'"});';
                 echo '$("span.status").text("Status: '.$percent.' Complete");';
                 echo '$("span.rowprocess").text("Processed Row: '.$rowproC++.' out of '.$total_files.'");';                            
                 echo '</script>';            
                 flush();
                 ob_flush(); 
             }
             
         echo '<script language="JavaScript">';                
         // echo 'window.location.href = "'.base_url().'Middleware_ctrl/middleware"';
         echo '</script>';            
         ini_set('memory_limit',$memory_limit );     
     }







     function get_textfile_middleware()
     {
         $memory_limit = ini_get('memory_limit');
         ini_set('memory_limit',-1);
         ini_set('max_execution_time', 0);


         // get the past 3 month years from the current month
         $past_3_month_years = array();
         for($i = 1; $i <= 4; $i++) 
         {             
             $html_date = date('Y-m-01', strtotime('first day of next month'));
             $past_month_year      = date('Y-m', strtotime("-{$i} month", strtotime($html_date)));
             $past_3_month_years[] = $past_month_year;           
         }      


         $from_day = date('Y-m',strtotime(date($past_3_month_years[3])));
         $to_day   = date('Y-m',strtotime(date($past_3_month_years[0])));

         $from_ = date('Y-m-d', strtotime($from_day));
         $to_ = date('Y-m-t', strtotime($to_day)); 

         echo ' <!DOCTYPE html>
                    <html>
                    <head>
                            <meta charset="utf-8">
                            <title>DATA UPLOAD</title>
                            <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">                                     
                            
                          
                            <link href="'.base_url().'assets/progress_bar/css/bootstrap.css" rel="stylesheet">                      
                            <link rel="stylesheet" href="'. base_url().'assets/progress_bar/js/jquery-ui/jquery-ui.css">
                            <link href="'. base_url().'assets/progress_bar/alert/css/alert.css" rel="stylesheet">
                            <link href="'. base_url().'assets/progress_bar/alert/themes/default/theme.css" rel="stylesheet">
                            <link href="'. base_url().'assets/progress_bar/css/extendedcss.css?ts=<?=time()?>&quot;" rel="stylesheet">        
                            <script src="'. base_url().'assets/progress_bar/js/jquery-1.10.2.js?2"></script>
                            <script src="'. base_url().'assets/progress_bar/js/bootstrap.min.js?2"></script>
                            <script src="'. base_url().'assets/progress_bar/js/bootstrap-dialog.js?2"></script>

                            <script src="'. base_url().'assets/progress_bar/js/jquery.metisMenu.js?2"></script>
                            <script src="'. base_url().'assets/progress_bar/js/dataTables/jquery.dataTables.min.js?2" type="text/javascript" charset="utf-8"></script>
                            <script src="'. base_url().'assets/progress_bar/js/dataTablesDontDelete/jquery.dataTables.min.js?2" type="text/javascript" charset="utf-8"></script>
                            <script src="'. base_url().'assets/progress_bar/js/ebsdeduction_function.js?<?php echo time()?>"></script>
                            <script src="'. base_url().'assets/js/sweetalert.js"></script>    
                            <script src="'. base_url().'assets/js/sweetalert2.all.min.js"></script>
                    
                    </head>   
                     
                    
                    <div class="row" style="margin-left: 22px;">                       
                                <div class="row" >                    
                                   <label class="col-md-12 pdd" style="margin:0px">
                                        <img src="'.base_url().'assets/icon_index/upload_im.PNG" width="30">                                         
                                        &nbsp;&nbsp;<img src="'.base_url().'assets/img/giphy.gif" height="20">
                                    </label>
                                    
                                    <span class="col-md-7 pdd fnt13 status">Status: 0% Complete </span>
                                    <!-- <span class="col-md-4 pdd fnt13 toright">Processed Row:</span> -->
                                    <span class="col-md-4 pdd fnt13 toright rowprocess"> 0</span>
                                </div>
                                <div class="progress row" style="height: 26px;margin:0px; padding:2px;"> 
                                    <div id="percontent" class="progress-bar progress-bar-pimary" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                                    </div>
                                </div>
                                <span class="col-md-12 pdd fnt13 empname" >Entry: </span>
                                <span class="col-md-12 pdd fnt13 filename"></span>                        
                     </div>   

                     ';
             flush();
             ob_flush(); 

             
             $directory_list = $this->Mms_mod->get_all_po_directory_by_store();     
             $total_files    = count($directory_list); 
             $rowproC        = 1;
              
              
             foreach($directory_list as $get_dir)
             {
                 if($rowproC >0 && $total_files >0)
                 {                                    
                     $percent = intval($rowproC/$total_files * 100)."%";                    
                 }
                 else 
                 {
                     $percent = "100%";
                 }


                 $mms_dir           = $get_dir['mms_directory'];
                 $mms_dir           = str_replace('\\\\','\\\\',$mms_dir);
                 $mms_dir           = str_replace('\\','\\',$mms_dir);
                 $mms_username      = $get_dir['mms_dir_username'];
                 $mms_password      = $get_dir['mms_dir_password'];
                             
                 //use the 'net use' command to map the network drive with the specified credentials
                 system("net use {$mms_dir} /user:{$mms_username} {$mms_password} >nul");

                 
                 echo '<script language="JavaScript">';
                
                 echo '$("span.filename").text("Updating PO database");';
                 echo '$("span.rowprocess").text("Processed Row: '.$rowproC.' out of '.$total_files.'");';      
                 echo '</script>';
                 flush();
                 ob_flush();


                 if ($handle = opendir($mms_dir."\\")) 
                 {
                      // Use glob to find file extensions files in the opened directory                      
                      $txtFiles = glob($mms_dir . "\\*.{"
                                                . $get_dir['file_extension'] . ","   // Include files with the specified extension
                                                . $get_dir['file_extension'] . "-PST"   // Include files with the extension + "-PST"
                                                . "}", GLOB_BRACE);

                     echo '<script language="JavaScript">';                     
                     echo '$("span.filename").text("Updating PO database");';
                     echo '$("span.rowprocess").text("Processed Row: '.$rowproC.' out of '.$total_files.'");';      
                     echo '</script>';
                     flush();
                     ob_flush();

                      //check if naa ba siyay nakit an nga ingani nga extension
                      if(!empty($txtFiles)) 
                      {
                         echo '<script language="JavaScript">';                         
                         echo '$("span.filename").text("Updating PO database");';
                         echo '$("span.rowprocess").text("Processed Row: '.$rowproC.' out of '.$total_files.'");';      
                         echo '</script>';
                         flush();
                         ob_flush();

                         foreach ($txtFiles as $file) 
                         {                    
                               

                              $file_name     = basename($file);
                              $exp_file_name = explode(".",$file_name);

                              // Read the content of the text file
                              $fileContent = file_get_contents($file);
                              // Explode the content into an array of lines using EOL
                              $lines = explode(PHP_EOL, $fileContent); 


                              for($a=0;$a<count($lines);$a++)
                              {
                                 if ( !(strstr($lines[$a], '[HEADER]') || strstr($lines[$a], '[LINES]'))) 
                                 {
                                   $line     =  str_replace('"','',$lines[$a]);
                                   $line_exp = explode("|",$line); 

                                   
                                   if(count($line_exp) == 7) //if header
                                   {
                                        $table         = 'mms_middleware_header'; 
                                        $search_header = $this->Mms_mod->search_mms_middleware_header($line_exp[0],'',$get_dir['databse_id']);

                                        $posting_date  = date('Y-m-d',strtotime($line_exp[1])); 

                                        $column_data = array();
                                        $column_data['document_no']   = $line_exp[0];
                                        $column_data['date_']         = $posting_date;
                                        $column_data['vendor']        = $line_exp[5];  
                                        $column_data['db_id']         = $get_dir['databse_id'];
                                        $column_data['textfile_name'] = $file_name;

                                        if($posting_date >= $from_ && $posting_date <= $to_)
                                        {
                                           if(empty($search_header))
                                           {
                                            $hd_id   = $this->Mms_mod->insert_mdl_table($table,$column_data);
                                            $purpose = 'insert';
                                           }
                                           else 
                                           {
                                            $column_filter = array();
                                            $hd_id                        = $search_header[0]['hd_id']; 
                                            $column_filter['document_no'] = $line_exp[0]; 
                                            $column_filter['db_id']       = $get_dir['databse_id'];                                               
                                            $this->Mms_mod->update_mdl_table($table,$column_data,$column_filter);
                                            $purpose = 'update';
                                           }

                                           // echo "Processing file:". $file_name."----date from:".$from_." to:".$to_."------posting date:".$posting_date."<br>";  

                                           echo '<script language="JavaScript">';      
                                           echo '$("span.empname").text("Entry: ' . $file_name . '");';                   
                                           echo '$("span.filename").text("Updating PO database");';
                                           echo '$("span.rowprocess").text("Processed Row: '.$rowproC.' out of '.$total_files.'");';      
                                           echo '</script>';
                                           flush();
                                           ob_flush();
                                        }
                                        else 
                                        {
                                           break;
                                        }

                                   }
                                   else 
                                   if(count($line_exp) == 11) //if lines      
                                   {
                                       $table       = "mms_middleware_lines";

                                       $column_data = array();

                                       $column_data['item_code']   = $line_exp[1];
                                       $column_data['pending_qty'] = $line_exp[2];
                                       $column_data['uom']         = $line_exp[4];

                                        
                                       if($purpose == 'insert')
                                       {
                                           $column_data['hd_id'] = $hd_id;
                                           $this->Mms_mod->insert_mdl_table($table,$column_data);                                                 
                                       }
                                       else 
                                       {
                                          $column_filter = array(); 
                                          $column_filter['hd_id']     = $hd_id;
                                          $column_filter['item_code'] = $line_exp[1];   
                                          $this->Mms_mod->update_mdl_table($table,$column_data,$column_filter);
                                       }
                                   }    
                                 }
                              }

                             echo '<script language="JavaScript">';                                                
                             echo '$("span.filename").text("Updating PO database");';
                             echo '$("span.rowprocess").text("Processed Row: '.$rowproC.' out of '.$total_files.'");';      
                             echo '</script>';
                             flush();
                             ob_flush();
                         }             

                         echo '<script language="JavaScript">';
                         echo '$("span.empname").text("Entry: ' . $file_name . '");';
                         echo '$("span.filename").text("Updating PO database");';
                         echo '$("span.rowprocess").text("Processed Row: '.$rowproC.' out of '.$total_files.'");';      
                         echo '</script>';
                         flush();
                         ob_flush();                          
                      }
                 }
                 else 
                 {
                     // Handle the error
                     echo "Failed to open directory: {$dir}\n";
                 } 

                 echo '<script language="JavaScript">';
                 echo '$("span.filename").text("Updating PO database");';
                 echo '$("div#percontent").css({"width":"'.$percent.'"});';
                 echo '$("span.status").text("Status: '.$percent.' Complete");';
                 echo '$("span.rowprocess").text("Processed Row: '.$rowproC++.' out of '.$total_files.'");';                            
                 echo '</script>';            
                 flush();
                 ob_flush(); 
             }

         // echo '<script language="JavaScript">';             
         // echo 'window.location.href = "'.base_url().'Middleware_ctrl/textfile_middleware"';
         // echo '</script>';       
         ini_set('memory_limit',$memory_limit );   
     }







     public function middleware()
     {
         $memory_limit = ini_get('memory_limit');
         ini_set('memory_limit',-1);
         ini_set('max_execution_time', 0);

         echo ' <!DOCTYPE html>
                    <html>
                    <head>
                            <meta charset="utf-8">
                            <title>DATA UPLOAD</title>
                            <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">   
                            <link href="'.base_url().'assets/css/datatables.min.css" rel="stylesheet" type="text/css"/>
                            <link href="'.base_url().'assets/css/googleapis.css" rel="stylesheet" type="text/css"/>
                            <link rel="'.base_url().'assets/css/sweetalert.css">                   
                            
                            <link href="'.base_url().'assets/css/site.min.css" rel="stylesheet"/>
                            <link href="'.base_url().'assets/progress_bar/css/bootstrap.css" rel="stylesheet">
                            <link href="'.base_url().'assets/progress_bar/css/font-awesome.css" rel="stylesheet">
                            <script src="'.base_url().'assets/progress_bar/css/bootstrap-dialog.css">
                            </script><link href="'.base_url().'assets/progress_bar/css/custom.css" ?v2="" rel="stylesheet">
                            <link rel="stylesheet" type="text/css" href="'.base_url().'assets/progress_bar/css/bootstrap-dialog.css">
                            <link href="'.base_url().'assets/progress_bar/css/bootstrap-datetimepicker.css?ts=<?=time()?>&quot;" rel="stylesheet">
                            <link href="'.base_url().'assets/progress_bar/plugins/icheck-1.x/skins/square/blue.css" rel="stylesheet">
                            <link href="'. base_url().'assets/progress_bar/css/dormcss.css" rel="stylesheet">
                            <link href="'. base_url().'assets/progress_bar/plugins/icheck-1.x/skins/square/blue.css" rel="stylesheet">
                            <link rel="stylesheet" href="'. base_url().'assets/progress_bar/js/jquery-ui/jquery-ui.css">
                            <link href="'. base_url().'assets/progress_bar/alert/css/alert.css" rel="stylesheet">
                            <link href="'. base_url().'assets/progress_bar/alert/themes/default/theme.css" rel="stylesheet">
                            <link href="'. base_url().'assets/progress_bar/css/extendedcss.css?ts=<?=time()?>&quot;" rel="stylesheet">        
                            <script src="'. base_url().'assets/progress_bar/js/jquery-1.10.2.js?2"></script>
                            <script src="'. base_url().'assets/progress_bar/js/bootstrap.min.js?2"></script>
                            <script src="'. base_url().'assets/progress_bar/js/bootstrap-dialog.js?2"></script>

                            <script src="'. base_url().'assets/progress_bar/js/jquery.metisMenu.js?2"></script>
                            <script src="'. base_url().'assets/progress_bar/js/dataTables/jquery.dataTables.min.js?2" type="text/javascript" charset="utf-8"></script>
                            <script src="'. base_url().'assets/progress_bar/js/dataTablesDontDelete/jquery.dataTables.min.js?2" type="text/javascript" charset="utf-8"></script>
                            <script src="'. base_url().'assets/progress_bar/js/ebsdeduction_function.js?<?php echo time()?>"></script>
                            <script src="'. base_url().'assets/js/sweetalert.js"></script>    
                            <script src="'. base_url().'assets/js/sweetalert2.all.min.js"></script>
                    
                    </head>   
                     
                    
                    <div class="row" style="margin-left: 22px;">                       
                                <div class="row" >                    
                                   <label class="col-md-12 pdd" style="margin:0px">
                                        <img src="'.base_url().'assets/icon_index/upload_im.PNG" width="30">                                         
                                        &nbsp;&nbsp;<img src="'.base_url().'assets/img/giphy.gif" height="20">
                                    </label>
                                    
                                    <span class="col-md-7 pdd fnt13 status">Status: 0% Complete </span>
                                    <!-- <span class="col-md-4 pdd fnt13 toright">Processed Row:</span> -->
                                    <span class="col-md-4 pdd fnt13 toright rowprocess"> 0</span>
                                </div>
                                <div class="progress row" style="height: 26px;margin:0px; padding:2px;"> 
                                    <div id="percontent" class="progress-bar progress-bar-pimary" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                                    </div>
                                </div>
                                <span class="col-md-12 pdd fnt13 empname" >Entry: </span>
                                <span class="col-md-12 pdd fnt13 filename"></span>                        
                     </div>   

                     ';





                     // get the past 3 month years from the current month
                     $past_3_month_years = array();
                     for($i = 1; $i <= 3; $i++) 
                     {
                         $html_date = date('Y-m-01');

                         $past_month_year      = date('Y-m', strtotime("-{$i} month", strtotime($html_date)));
                         $past_3_month_years[] = $past_month_year;
                     }         
                    
                     // var_dump($past_3_month_years);

                     $exp_yr_m_1 = explode("-",$past_3_month_years[2]);  
                     $exp_yr_m_2 = explode("-",$past_3_month_years[1]);  
                     $exp_yr_m_3 = explode("-",$past_3_month_years[0]);  


                    $select                   = '*';
                    $table_id                 = 'reorder_store';
                    $where_booking['bu_type'] = 'NON STORE'; 
                    $booking_srv_list         = $this->Mms_mod->select($select,$table_id,$where_booking);
                    
                    $total_files = count($booking_srv_list); 
                    $rowproC     = 1;

                     

                        foreach($booking_srv_list as $book_server)
                        {
                             if($rowproC >0 && $total_files >0)
                             {                                    
                               $percent = intval($rowproC/$total_files * 100)."%";                    
                             }
                             else 
                             {
                               $percent = "100%";
                             } 

                             // echo $book_server['databse_id']."<br>";

                             $select              = '*';
                             $table_db            = 'database';
                             $where_db['db_id']   = $book_server['databse_id'];
                             $get_connection      = $this->Mms_mod->select($select,$table_db,$where_db);

                             foreach($get_connection  as $con)
                             {
                                 $username    = $con['username'];
                                 $password    = $con['password']; 
                                 $connection  = $con['db_name'];
                                 $sub_db_name = $con['sub_db_name'];                               
                             }

                             $connect = odbc_connect($connection, $username, $password);

                             $table_1 = '['.$sub_db_name.'$Sales Invoice Header]';       
                             $table_2 = '['.$sub_db_name.'$Sales Invoice Line]';  

                             $vendor_list = $this->Mms_mod->get_all_vendor_po_calendar();
                             

                             $last_entry = $this->Mms_mod->get_last_entry_mms_middleware($book_server['databse_id']);

                             if(!empty($last_entry))
                             {
                                 $where = "AND [Document No_] >= '".$last_entry[0]['document_no']."'";
                             }
                             else 
                             {
                                 $where = '';
                             }


                             foreach($vendor_list as $list)
                             {                                 

                                 $table_query  = "  
                                                    SELECT
                                                            TOP 5 
                                                            line.[Document No_],
                                                            line.[Quantity],
                                                            line.[No_],
                                                            line.[Description],
                                                            line.[Unit of Measure],
                                                            line.[Vendor No_],
                                                            line.[Variant Code],
                                                            YEAR([Posting Date]) AS [Year],
                                                            MONTH([Posting Date]) AS [Month],
                                                            CONVERT(VARCHAR(10), [Posting Date], 23) AS posting_date

                                                    FROM 
                                                           ".$table_1."  as head
                                                    INNER JOIN  ".$table_2." AS line ON line.[Document No_] = head.[No_]                                                
                                                    WHERE 
                                                           (
                                                             -- (YEAR([Posting Date]) = '".$exp_yr_m_1[0]."' AND MONTH([Posting Date]) = '".$exp_yr_m_1[1]."' ) OR
                                                             -- (YEAR([Posting Date]) = '".$exp_yr_m_2[0]."' AND MONTH([Posting Date]) = '".$exp_yr_m_2[1]."' ) OR
                                                             -- (YEAR([Posting Date]) = '".$exp_yr_m_3[0]."' AND MONTH([Posting Date]) = '".$exp_yr_m_3[1]."' ) 

                                                                [Posting Date] >= '2019-01-01' AND [Posting Date] <= '2023-12-31' -- temporary
                                                           )
                                                    AND 
                                                           line.[Vendor No_] = '".$list['no_']."'                                                     

                                                           ".$where;                                                


                                 $table_hd_ln_row    = odbc_exec($connect, $table_query);

                                 if(odbc_num_rows($table_hd_ln_row) > 0)
                                 {                                  
                                     while ($hd_ln_row = odbc_fetch_array($table_hd_ln_row))
                                     {                                     
                                           // echo  'VENDOR-->'.$list['no_'].'---->'.$hd_ln_row['No_'].'--->'.$hd_ln_row['Description'].'--->'.$hd_ln_row['Unit of Measure'].'--->'.$hd_ln_row['Year'].'--->'.$hd_ln_row['Month'].'--->'.$hd_ln_row['Quantity'].'---->'.$hd_ln_row['Vendor No_'].'<br>';
                                          $table= 'mms_middleware';
                                          $insert_data['db_id']           = $book_server['databse_id'];
                                          $insert_data['document_no']     = $hd_ln_row['Document No_'];
                                          $insert_data['no_']             = $hd_ln_row['No_'];  
                                          $insert_data['description']     = $hd_ln_row['Description'];
                                          $insert_data['unit_of_measure'] = $hd_ln_row['Unit of Measure'];
                                          $insert_data['year']            = $hd_ln_row['Year'];
                                          $insert_data['month']           = $hd_ln_row['Month'];
                                          $insert_data['variant_code']    = $hd_ln_row['Variant Code'];
                                          $insert_data['posting_date']    = $hd_ln_row['posting_date'];

                                          if (substr($hd_ln_row['Quantity'], 0, 1) === '.') 
                                          {                                                 
                                                 $qty = '0.00';
                                          }
                                          else
                                          {
                                                 $qty = $hd_ln_row['Quantity'];                                                 
                                          }
                                          
                                          $insert_data['quantity']        = $qty;
                                          
                                          $insert_data['vendor_no']       = $hd_ln_row['Vendor No_'];

                                           
                                          $check_mdl = $this->Mms_mod->check_middleware($book_server['databse_id'],$hd_ln_row['Document No_'],$hd_ln_row['No_']);
                                          if(empty($check_mdl))
                                          {
                                              $this->Mms_mod->insert_mdl_table($table,$insert_data);
                                          }
                                          else 
                                          { 
                                              $column_filter['db_id'] = $book_server['databse_id'];
                                              $column_filter['document_no'] = $hd_ln_row['Document No_'];
                                              $column_filter['no_'] = $hd_ln_row['No_'];
                                              $this->Mms_mod->update_mdl_table($table,$insert_data,$column_filter);
                                          }

                                          echo '<script language="JavaScript">';
                                          echo '$("span.empname").text("Entry:'.$hd_ln_row['Document No_'].' ");';   
                                          echo '</script>';            
                                     }
                                 }     

                             }

                             echo '<script language="JavaScript">';
                             echo '$("span.filename").text("Inserting Reorder Batch");';
                             echo '$("div#percontent").css({"width":"'.$percent.'"});';
                             echo '$("span.status").text("Status: '.$percent.' Complete");';
                             echo '$("span.rowprocess").text("Processed Row: '.$rowproC++.' out of '.$total_files.'");';                            
                             echo '</script>';            
                             flush();
                             ob_flush();
                        }

         echo '<script language="JavaScript">';
         // echo 'window.location.href = "'.base_url().'Middleware_ctrl/get_textfile_middleware"';         
         echo '</script>';            
         ini_set('memory_limit',$memory_limit );           

     }
 }    