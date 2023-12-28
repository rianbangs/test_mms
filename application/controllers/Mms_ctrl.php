<?php
/**
 * 
 */
class Mms_ctrl extends CI_Controller
{
     function __construct()
     {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('simplify/simplify','simplify');
        $this->load->model('simplify/pdf_simplify','pdf_');   
        $this->load->model('Po_mod');
        $this->load->model('Mms_mod');
        $this->load->model('Acct_mod');
        $this->load->model('Po_view_mod');  


        if(!isset($_SESSION['user_id'])){
            redirect(base_url('Log_ctrl/index'));
            
         }else{

            if($this->Acct_mod->getUserCountById($_SESSION['user_id'])<1){
               unset($_SESSION['user_id']);
               redirect(base_url('Log_ctrl/index'));
            }else{
               $userType = $this->Acct_mod->retrieveUserDetails()["user_type"];
               if(!in_array($userType,array('buyer','category-head','corp-manager','incorporator')))
               //if($userType!="buyer")
               {
                  unset($_SESSION['user_id']);
                  redirect(base_url('Log_ctrl/index'));
               }
            }   
         }
     }

     function session_check_js(){
        $response = 'yes'; 
        
        $data['response'] = $response;
        echo json_encode($data);
     }
     
     function mms_ui($index=1)
     {
        $data['active_nav'] = $index;
        $this->load->view("mms/mms_head_ui",$data);         
          
     }




// gi add rani na code
     function logout(){
        unset($_SESSION['user_id']);
        redirect(base_url('Log_ctrl/index'));
     }
//////////////////////////

     function load_reorder_list_table()
     {
         $html               = '';
         $table_id           = 'reorder_table_list';
         $table_header       = array('SUPPLIER CODE',"SUPPLIER NAME",'REORDER DATE','STORE');
         $style_header       = array('','','','');
         $html              .= $this->simplify->populate_header_table($table_id,$table_header,$style_header);
         $supplier_code_data = $this->Mms_mod->get_supplier_code();

         foreach($supplier_code_data as $s_code)
         {
              $reorder_data = $this->Mms_mod->get_reorder_report_data($s_code['supplier_code'],$s_code['store']);
              foreach($reorder_data as $reorder)
              {
                 if($reorder['value_type'] == 'supplier_code_number')
                 {
                     $supplier_code_number = $reorder['value_name'];
                 }
                 else 
                 if($reorder['value_type'] == 'supplier_name')
                 {
                     $supplier_name = $reorder['value_name'];    
                 }
                 else 
                 if($reorder['value_type'] == 'date generated')
                 {
                     $date_generated = $reorder['value_name'];    
                 }
                  
                 $store = $reorder['store'];

              }
              $row1   = array(
                                 $supplier_code_number,
                                 $supplier_name,
                                 $date_generated,
                                 $store  
                              );
                           
               $style1 = array(
                                   "text-align:center;",
                                   "text-align:center;",
                                   "text-align:center;",
                                   "text-align:center;" 
                              );
               $tr_class ='tr_';
               $html  .= $this->simplify->populate_table_rows($row1,$style1,$tr_class); 
         }


          


         $html .= '
                              </tbody>
                          </table>
                          <br><br>
                       <script>
                               $("#'.$table_id.'").dataTable({                            
                                                                 "order":false                                  
                                                              });
                   </script>';
                                  
         $data['html'] = $html;

         echo json_encode($data);
     }


   

     public function value_type_checker($value_type,$strip,$previous)
     {        



          if(strstr($strip, 'SubTotal'))
          {
              $value_type = 'SubTotal';   
          }
          else 
          if(strstr($value_type, 'SubTotal'))                    
          {
              $value_type = 'SubTot_store';                  
          }
          else 
          if(strstr($value_type, 'SubTot_store'))                    
          {                  
              $value_type = 'SubTot_prev-1';        
          }
          else 
          if(strstr($value_type, 'SubTot_prev'))       
          {
              $extract_counter = explode("-",$value_type);   
              if($extract_counter[1] < $previous)
              {               
                 $count      = $extract_counter[1] +1;
                 $value_type = 'SubTot_prev-'.$count;
              }
              else 
              {
                $value_type = "subtot_av_sales";
              }
          }
          else
          if($value_type == "subtot_av_sales")
          {
              $value_type = "subtot_max-level";             
          }
          else     
          if($value_type == "subtot_max-level")
          {               
              $value_type = "subtot_qty_onhand";             
          }
          else 
          if($value_type == "subtot_qty_onhand")
          {
              $value_type = "subtot_reorder-qty";                          
          }
          else 
          if($strip == "TOTAL for")
          {
              $value_type = "TOTAL for";                                       
          }
          else 
          if($value_type == "TOTAL for")
          {
              $value_type = "TOT-for-sup_code";                                                   
          }
          else 
          if($value_type == "TOT-for-sup_code")
          {
              $value_type = "TOTAL for_prev-1";                                                               
          }          
          else 
          if(strstr($value_type, 'TOTAL for_prev'))       
          {
              $extract_counter = explode("-",$value_type);   
              if($extract_counter[1] < $previous)
              {               
                 $count      = $extract_counter[1] +1;
                 $value_type = 'TOTAL for_prev-'.$count;
              }
              else 
              {
                $value_type = "Tot_for_av_sales";
              }
          }         
          else 
          if($value_type == "Tot_for_av_sales")            
          {
              $value_type = "Tot_for_max-level";             
          }
          else 
          if($value_type == "Tot_for_max-level")            
          {
              $value_type = "Tot_for_qty_onhand";             
          } 
          else 
          if($value_type == "Tot_for_qty_onhand")            
          {
              $value_type = "Tot_for_reorder-qty";                         
          }
          else 
          if(strstr($strip, 'Prepared By:'))       
          {
              $value_type = "PreparedBy";
          }
          else 
          if($value_type == "PreparedBy")  
          {
              $value_type = "Prepared_By_name";              
          }
          else 
          if(strstr($strip, 'Run Time:'))                   
          {
              $strip      = str_replace('Run Time:  ','',$strip);
              $value_type = "Run Time:";                           
          }
          else 
          if(strstr($strip, 'Run Date:'))                   
          {
              $strip      = str_replace('Run Date: ','',$strip);
              $value_type = "Run Date: ";  
          }
          else 
          if(strstr($strip, 'PAGE NO:'))
          {
             $value_type = "page_number"; 
          }
          else 
          if($value_type == "page_number" && is_numeric($strip) )  
          {                          
             $value_type = "page_number";           
          }
          else           
          if($value_type == "page_number" &&  strstr($strip, '-') )  
          {
             $value_type = "store";
          }
          else 
          if($value_type == "store" &&  strstr($strip, '-') )  
          {
             $value_type = "report";            
          }
          else 
          if($value_type == "report" &&  strstr($strip, '&nbsp') )  
          {
             $strip      = str_replace('&nbsp','',$strip);
             $strip      = date('Y-m-d',strtotime($strip));
             $value_type = "date generated";                         
          }
          else 
          if(strstr($strip, 'SUPPLIER CODE:'))          
          {
             $value_type = "supplier_code";                                       
          } 
          else  
          if($value_type == "supplier_code" &&  strstr($strip, 'S') )          
          {
             $value_type = "supplier_code_number";                                                     
          }
          else
          if($value_type == 'supplier_code_number')  
          {
             $value_type = "supplier_name";                                                                  
          }
          else 
          if($value_type == "supplier_name" &&  strstr($strip, 'LEAD TIME FACTOR:') )                    
          {
             $strip      = str_replace('LEAD TIME FACTOR:','',$strip);
             $value_type = "lead_time_factor";
          }  
          else 
          if(strstr($strip, 'PREVIOUS'))                              
          {
             $strip_arr = explode(" ",$strip);
             $previous  = $strip_arr[1];              
          }
          else 
          if(strstr($strip, 'PO QTY'))                    
          {
             $value_type = "PO QTY header";             
          }  
          else 
          if(strstr($strip, 'Pending'))                    
          {
             $value_type = "Pending";                          
          }
          else 
          if($value_type == "Pending" && strstr($strip,'&nbsp') )
          {            
             $value_type = "Pending";                          
            // $value_type = date('m', strtotime($date));            
          }
          else 
          if($value_type == "Pending" || $value_type == "Pending-month")
          {
                $date = $strip.' 01 '.date('Y');
                $date = date('Y-m-d', strtotime($date));

                if($date != '1970-01-01')
                {                   
                   $value_type = "Pending-month"; 
                }
                else 
                {
                   $value_type = "item_code";           
                }
          }
          else 
          if($value_type == "item_code")
          {
             $value_type = "Description";
          }           
          else 
          if($value_type == "Description")            
          {
             $value_type = "Unit_of_measure";
          } 
          else 
          if($value_type == "Unit_of_measure")            
          {
             $value_type = 'PREV_qty-1'; 
          }
          else 
          if(strstr($value_type, 'PREV_qty'))            
          {
             $extract_counter = explode("-",$value_type);   
             if($extract_counter[1] < $previous)
             {               
                $count      = $extract_counter[1] +1;
                $value_type = 'PREV_qty-'.$count;
             }
             else 
             {
               $value_type = "ave_sales";
             }
          }  
          else 
          if(strstr($value_type, 'ave_sales'))            
          {
               $value_type = "max_level";             
          }  
          else 
          if(strstr($value_type, 'max_level'))            
          {
               $value_type = "qty_on_hand";                         
          }     
          else 
          if(strstr($value_type, 'qty_on_hand'))            
          {
               $value_type = "las_dir_cost";                                     
          }
          else 
          if(strstr($value_type, 'las_dir_cost'))            
          {
               $value_type = "last_recv_qty";                                                 
          }
          else 
          if(strstr($value_type, 'last_recv_qty'))                    
          {
             $value_type = "LP_UOM";                                                             
          }  
          else 
          if(strstr($value_type, 'LP_UOM'))                    
          {
             $value_type = "last_del_date"; 
          }
          else  
          if(strstr($value_type, 'last_del_date'))                    
          {
             $value_type = "reorder_qty";              
          }
          else 
          if(strstr($value_type, 'reorder_qty'))                    
          {
             $value_type = "po_qty";              
          }
          else 
          if(strstr($value_type, 'po_qty'))                    
          {
             $value_type = "Pending";              
          }
          else             
          {            
             $value_type = "";           
          }


          if($strip == '&nbsp')
          {
             $strip = '';
          } 

          $return_data = array($value_type,$strip,$previous);

          return  $return_data;
     }



     public function extract_vendor($RESS2,$store,$reorder_number,$reorder_batch)
     {
          var_dump($RESS2,$store,$reorder_number,$reorder_batch);  
          $store_name = $this->Mms_mod->get_a_store($store);  

          $html_exp = explode($store_name[0]['name'],$RESS2);  

          for($a=1;$a<count($html_exp);$a++)
          {              
               $exp_header_column = explode("\n", $html_exp[$a]); 
               //$item_code = $exp_header_column[68];
               for($b=68;$b<count($exp_header_column);$b+=22)
               {
                  $item_code   = trim($exp_header_column[$b]);
                  $description = $exp_header_column[$b+2];
                  @$quantity   = $exp_header_column[$b+7];
                  @$net_w_vat  = $exp_header_column[$b+14];
                  @$discount   = $exp_header_column[$b+18];

                  
                  if(!empty($item_code))
                  {
                      //echo $item_code."--".$description.'--'.$qty.'--'.$net_w_vat."--".$discount."<br>";
                      $table                           = 'reorder_report_data_item_vendor';
                      $insert_batch['item_code']       = trim($item_code);
                      $insert_batch['description']     = trim($description);
                      $quantity                        = str_replace(',','',$quantity);
                      $insert_batch['quantity']        = trim($quantity);
                      $net_w_vat                       = str_replace(',','',$net_w_vat);
                      $insert_batch['net_sales_w_vat'] = trim($net_w_vat);                                           
                      $discount                        = str_replace(array('-', ','), '', $discount);
                      $insert_batch['discount']        = trim($discount);
                      $insert_batch['reorder_number']  = trim($reorder_number);
                      $insert_batch['reorder_batch']   = trim($reorder_batch);

                      $this->Mms_mod->insert_table($table,$insert_batch);  


                  }

               }
               //($exp_header_column);              
          }
           
           
     }




     public function extract_reorder($RESS2,$store,$reorder_batch)
     {  
         $clean_row_arr = array();

         $html_exp       = explode("_________________________",$RESS2);

         $exp_header_column = explode("\n", $html_exp[0]);
         $header_arr =  array_splice($exp_header_column,0, -18); // Remove the last 18 indexes  

         //($header_arr);

         $exp_lead = explode(':',$header_arr[44]);
         $lead_time_factor = trim($exp_lead[1]);

         
         //$insert_data['reorder_date'] = date('Y-m-d'); 
         $insert_data['supplier_code'] =trim($header_arr[37]); 
         $insert_data['supplier_name'] =trim($header_arr[42]); 

         $insert_data['lead_time_factor'] = trim($lead_time_factor); 
         $insert_data['month_1']          = trim($header_arr[99]);
         $insert_data['month_2']          = trim($header_arr[101]);
         $insert_data['month_3']          = trim($header_arr[103]);
         $insert_data['reorder_batch']    = trim($reorder_batch);
         $insert_data['store']            = trim($store);
 
         
         $table ='reorder_report_data_header_final';
         $reorder_number = $this->Mms_mod->insert_table($table,$insert_data);

         


         for($a=0;$a<count($html_exp);$a++)
         {
             $exp_per_column = explode("\n", $html_exp[$a]);      
             $clean_row      = array_slice($exp_per_column, -18);     
             
             $insert_data_lines['reorder_number']     = trim($reorder_number);
             $insert_data_lines['item_code']          = trim($clean_row[0]);
             $insert_data_lines['Item_description']   = trim($clean_row[1]);
             $insert_data_lines['uom']                = trim($clean_row[2]);
             $insert_data_lines['month_sales_1']      = str_replace(',','',trim($clean_row[3]));
             $insert_data_lines['month_sales_2']      = str_replace(',','',trim($clean_row[4]));
             $insert_data_lines['month_sales_3']      = str_replace(',','',trim($clean_row[5]));
             $insert_data_lines['ave_sales']          = str_replace(',','',trim($clean_row[6]));
             $insert_data_lines['maximum_level']      = str_replace(',','',trim($clean_row[7]));
             $insert_data_lines['quantity_on_hand']   = str_replace(',','',trim($clean_row[8]));
             $insert_data_lines['last_direct_cost']   = str_replace(',','',trim($clean_row[9]));
             $insert_data_lines['last_rcv_qty']       = str_replace(',','',trim($clean_row[11]));
             

             if(strstr($clean_row[14],'/'))
             {                
                 // $date_exp                            = explode('/',$clean_row[14]);
                 // $last_del_date                       = date('Y-m-d',strtotime(date($date_exp[2].'-'.$date_exp[0].'-'.$date_exp[1])));

                 $dateString    = $clean_row[14];
                 $dateTimestamp = strtotime($dateString);
                 $last_del_date = date('Y-m-d', $dateTimestamp);
             }
             else 
             {
                 $last_del_date                       = $clean_row[14];
             }

             $insert_data_lines['last_del_date']      = $last_del_date;

             $table = 'reorder_report_data_lines_final';
             if($insert_data_lines['item_code']  != '')
             {
                 $this->Mms_mod->insert_table($table,$insert_data_lines);
             }

             

            // $this->insert_reorder_report_data_lines_final($insert_batch_data);             


             // array_push($clean_row_arr, array(
             //                                    "item_code"   => $clean_row[0],
             //                                    "description" => $clean_row[1],
             //                                    "uom"         => $clean_row[2],
             //                                    "month_1"     => $clean_row[3],
             //                                    "month_2"     => $clean_row[4],
             //                                    "month_3"     => $clean_row[5],
             //                                    "ave_sales"   => $clean_row[6],
             //                                    "max_level"   => $clean_row[7],
             //                                    "qty_on_hand"   => $clean_row[8],
             //                                    "last_dir_cost" => $clean_row[9],                                                
             //                                    "last_recv_qty" => $clean_row[10],                                                
             //                                    "last_recv"     => $clean_row[11],                                                                                                
             //                                    "UOM"           => $clean_row[12],                                                                                                
             //                                    "LP"            => $clean_row[13],                                                                                                
             //                                    "last_date"     => $clean_row[14],                                                                                                 
             //                                    "reorder_qty"   => $clean_row[15],                                                                                                 
             //                                    "po_qty"        => $clean_row[16],                                                                                                 
             //                                    "pending_qty"   => $clean_row[17]                                                                                            
             //                                 ) );
                             
                               
             

         }

         
         //($header_arr);
         //($clean_row_arr);
         return $reorder_number;

     }  


     public function archive_table()
     {
          // $header   = array('Supplier Code','Supplier Name','Reorder Date','Month 1','Month 2','Month 3','Item Code','Description','Unit of Measure');
          // $html     = $this->simplify->populate_header_table($table_id,$header);  
          $table_id = 'ave_sales_per_month_tbl';

          // $html     = '<table id="'.$table_id.'" class="table table-striped table-bordered table-responsive dataTable no-footer" style="background-color: rgb(5, 68, 104);" aria-describedby="report-table_info">
          //                 <thead style="text-align: center;color:white;">
          //                   <tr>
          //                       <th rowspan="2">Supplier Code</th>
          //                       <th rowspan="2">Supplier Name</th>
          //                       <th rowspan="2">Reorder Date</th>
          //                       <th colspan="2">Month 1</th>
          //                       <th colspan="2">Month 2</th>
          //                       <th colspan="2">Month 3</th>
          //                       <th rowspan="2">Item Code</th>
          //                       <th rowspan="2">Description</th>
          //                       <th rowspan="2">Unit of Measure</th>
          //                       <th rowspan="2">Store</th>                                
          //                   </tr>
          //                   <tr>
          //                       <th>Month</th>
          //                       <th>Quantity</th>
          //                       <th>Month</th>
          //                       <th>Quantity</th>
          //                       <th>Month</th>
          //                       <th>Quantity</th>                                
          //                   </tr>
          //               </thead>
          //              '; 
          $html     = '<table id="'.$table_id.'" class="table table-striped table-bordered table-responsive dataTable no-footer" style="background-color: rgb(5, 68, 104);" aria-describedby="report-table_info">
                          <thead style="text-align: center;color:white;">
                            <tr>
                                <th>Supplier Code</th>
                                <th>Supplier Name</th>
                                <th>Reorder Date</th>                                
                                <th>Store</th>                                
                            </tr>                             
                        </thead>
                       '; 

          $archive_details = $this->Mms_mod->get_archive_table();
          

          foreach($archive_details as $arc)
          {

                  $rows = array(
                                   $arc['supplier_code'],
                                   $arc['supplier_name'], 
                                   $arc['reorder_date'], 
                                   // $arc['month_1'],$arc['month_sales_1'], 
                                   // $arc['month_2'],$arc['month_sales_2'], 
                                   // $arc['month_3'],$arc['month_sales_3'], 
                                   // $arc['item_code'], 
                                   // $arc['Item_description'], 
                                   // $arc['uom'],
                                   strtoupper($arc['store'])
                               );

                 $style = array(
                                   "text-align:center;",
                                   "text-align:center;",
                                   "text-align:center;",
                                   "text-align:center;"
                                   // "text-align:right;",
                                   // "text-align:right;",
                                   // "text-align:right;",
                                   // "text-align:right;",
                                   // "text-align:right;",
                                   // "text-align:right;",
                                   // "text-align:right;",
                                   // "text-align:right;",
                                   // "text-align:right;"                                   
                               ); 
                 

                 $tr_class = '';                 
                 $html .= $this->simplify->populate_table_rows($rows,$style,$tr_class);
          }

          $html .= '     </tbody>
                     </table>
                     <script>
                        $("#'.$table_id.'").DataTable({ "ordering": false});
                     </script>
                     ';     

          $data['html']  = $html;
          echo json_encode($data);
     }





     public function middleware()
     {
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
                                 $where = "AND [Document No_] >= ".$last_entry[0]['document_no'];
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
                                                            YEAR([Posting Date]) AS [Year],
                                                            MONTH([Posting Date]) AS [Month]

                                                    FROM 
                                                           ".$table_1."  as head
                                                    INNER JOIN  ".$table_2." AS line ON line.[Document No_] = head.[No_]                                                
                                                    WHERE 
                                                           (
                                                              (YEAR([Posting Date]) = '".$exp_yr_m_1[0]."' AND MONTH([Posting Date]) = '".$exp_yr_m_1[1]."' ) OR
                                                              (YEAR([Posting Date]) = '".$exp_yr_m_2[0]."' AND MONTH([Posting Date]) = '".$exp_yr_m_2[1]."' ) OR
                                                              (YEAR([Posting Date]) = '".$exp_yr_m_3[0]."' AND MONTH([Posting Date]) = '".$exp_yr_m_3[1]."' ) 
                                                           )
                                                    AND 
                                                           line.[Vendor No_] = '".$list['no_']."'                                                     

                                                           ".$where;                                                


                                 $table_hd_ln_row    = odbc_exec($connect, $table_query);

                                 if(odbc_num_rows($table_hd_ln_row) > 0)
                                 {                                  
                                     while ($hd_ln_row = odbc_fetch_array($table_hd_ln_row))
                                     {                                          
                                           
                                          // array_push($row_data_arr,array(
                                          //                                  "item_code"   =>$hd_ln_row['No_'], 
                                          //                                  "Description" =>$hd_ln_row['Description'],
                                          //                                  "uom"         =>$hd_ln_row['Unit of Measure'], 
                                          //                                  "year"        =>$hd_ln_row['Year'],
                                          //                                  "month"       =>$hd_ln_row['Month'],                                                                           
                                          //                                  "tot_qty"     =>$hd_ln_row['Quantity']
                                          //                               ));
                                          echo  'VENDOR-->'.$list['no_'].'---->'.$hd_ln_row['No_'].'--->'.$hd_ln_row['Description'].'--->'.$hd_ln_row['Unit of Measure'].'--->'.$hd_ln_row['Year'].'--->'.$hd_ln_row['Month'].'--->'.$hd_ln_row['Quantity'].'<br>';
                                          $table= 'mms_middleware';
                                          $insert_data['db_id']           = $book_server['databse_id'];
                                          $insert_data['document_no']     = $hd_ln_row['Document No_'];
                                          $insert_data['no_']             = $hd_ln_row['No_'];  
                                          $insert_data['description']     = $hd_ln_row['Description'];
                                          $insert_data['unit_of_measure'] = $hd_ln_row['Unit of Measure'];
                                          $insert_data['year']            = $hd_ln_row['Year'];
                                          $insert_data['month']           = $hd_ln_row['Month'];
                                          $insert_data['quantity']        = $hd_ln_row['Quantity'];
                                          $insert_data['vendor_no']       = $hd_ln_row['Vendor No_'];
                                           
                                          $this->Mms_mod->insert_mdl_table($table,$insert_data);

                                     }
                                 }     

                             }

                             echo '<script language="JavaScript">';
                             echo '$("span.filename").text("Inserting Reorder Batch");';
                             echo '$("div#percontent").css({"width":"'.$percent.'"});';
                             echo '$("span.status").text("Status: '.$percent.' Complete");';
                             echo '$("span.rowprocess").text("Processed Row: '.$rowproC++.' out of '.$total_files.'");';
                             echo '$("span.empname").text("Entry: ");';
                             echo '</script>';            
                             flush();
                             ob_flush();
                        }



     }









     public function extract_file_V6()
     {  
            $apilon_booking      = $_POST['apilon_booking'];


            $store_arr           = $_POST['store_arr'];
            $data['store_array'] = json_decode($store_arr);

           
            $file_content = $_POST['file_content'];
            $data['file_contents'] = json_decode($file_content);

            $file_list             = $_POST['file_list'];
            $data['fileNames']     = json_decode($file_list);
 
            $data['v_code']        = $_POST['v_code'];
            $data['d_tag']         = $_POST['d_tag'];
            $data['group_code']    = $_POST['group_code'];            

            //$this->load->view('mms/upload_ui', $data);

            $memory_limit = ini_get('memory_limit');
            ini_set('memory_limit',-1);
            ini_set('max_execution_time', 0);

            $file_contents = json_decode($file_content);
            $store_array   = json_decode($store_arr);
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



                    // <div class="col-md-12" style="margin-top:0%;padding:3px;">
                    //     <div class="col-md-12 pdd_1"></div>         
                    //     <button   class="back_button btn btn-danger" onclick="back_to_posting()"  style="display:none;">back to ebs</button> <div class="col-md-6 col-md-offset-3" style="padding: 10% 0%;">
                    //             <div class="row" style="padding-left: 18px;">                    
                    //                <label class="col-md-12 pdd" style="margin:0px">
                    //                     <img src="'.base_url().'assets/icon_index/upload_im.PNG" width="30">                                         
                    //                     &nbsp;&nbsp;<img src="'.base_url().'assets/img/giphy.gif" height="20">
                    //                 </label>
                                    
                    //                 <span class="col-md-7 pdd fnt13 status">Status: 0% Complete </span>
                    //                 <!-- <span class="col-md-4 pdd fnt13 toright">Processed Row:</span> -->
                    //                 <span class="col-md-4 pdd fnt13 toright rowprocess"> 0</span>
                    //             </div>
                    //             <div class="progress row" style="height: 26px;margin:0px; padding:2px;"> 
                    //                 <div id="percontent" class="progress-bar progress-bar-pimary" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                    //                 </div>
                    //             </div>
                    //             <span class="col-md-12 pdd fnt13 empname" >Entry: </span>
                    //             <span class="col-md-12 pdd fnt13 filename"></span>
                    //       </div>
                    //  </div>          




             flush();
             ob_flush();
             //usleep(100);           

             $current_user_login =  $this->Mms_mod->get_user_connection($_SESSION['user_id']);


             $insert_batch['user_id']        = $_SESSION['user_id'];    
             $insert_batch['store_id']       = $current_user_login[0]['store_id'];      

             if($_POST['v_code'] != 'UPLOAD OLD SALES')
             {
                 $insert_batch['status']     = 'Pending';  
                 $insert_batch['date_tag']   = $data['d_tag'] ;
                 $total_files = 7;
             }
             else 
             {
                 $insert_batch['status']     = 'ARCHIVE';                  
                 $insert_batch['date_tag']   = date('Y-m-d'); ;             
                 $total_files = 3;
             }


             $insert_batch['group_code_']    = $data['group_code'];
             $insert_batch['date_generated'] = date('Y-m-d H:i:s');
             $table                          = 'reorder_report_data_batch';
             $reorder_batch                  = $this->Mms_mod->insert_table($table,$insert_batch);  


             $rowproC     = 1;
// ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ 1  ^^^^^^^^^^^^^^
             if($rowproC >0 && $total_files >0)
             {                                    
                  $percent = intval($rowproC/$total_files * 100)."%";                    
             }
             else 
             {
                  $percent = "100%";
             }   


             echo '<script language="JavaScript">';
             echo '$("span.filename").text("Inserting Reorder Batch");';
             echo '$("div#percontent").css({"width":"'.$percent.'"});';
             echo '$("span.status").text("Status: '.$percent.' Complete");';
             echo '$("span.rowprocess").text("Processed Row: '.$rowproC++.' out of '.$total_files.'");';
             echo '$("span.empname").text("Entry: ");';
             echo '</script>';            
             flush();
             ob_flush();
             //usleep(100);


             
             $reorder_list = array();

             for($a=0;$a<count($file_contents);$a++)
             {
// ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ 2  ^^^^^^^^^^^^^^
                 if($rowproC >0 && $total_files >0)
                 {                                    
                   $percent = intval($rowproC/$total_files * 100)."%";                    
                 }
                 else 
                 {
                   $percent = "100%";
                 } 

                 $RESS2 = '';                                                                   
                 $RESS2 = strip_tags($file_contents[$a]);          


                 if(strstr($RESS2,'Re-order'))
                 {
                      $reorder_number =  $this->Mms_mod->extract_reorderV2($RESS2,$reorder_batch);
                      array_push($reorder_list,array("store"=>$store_array[$a],'reorder_number'=>$reorder_number));
                     //$reorder_number =  $this->Mms_mod->extract_reorder($RESS2,$store_array[$a],$reorder_batch);   //old                     
                 }

                 echo '<script language="JavaScript">';
                 echo '$("span.filename").text("Inserting Header and Reorder lines");';
                 echo '$("div#percontent").css({"width":"'.$percent.'"});';
                 echo '$("span.status").text("Status: '.$percent.' Complete");';  
                 echo '$("span.rowprocess").text("Processed Row: '.$rowproC.' out of '.$total_files.'");';
                 echo '$("span.empname").text("Entry: ");';                                
                 echo '</script>';               
                 flush();
                 ob_flush();
                 //usleep(100);                  
                                   
             }

             echo '<script language="JavaScript">';
             echo '$("span.filename").text("Inserting Header and Reorder lines");';
             echo '$("div#percontent").css({"width":"'.$percent.'"});';
             echo '$("span.status").text("Status: '.$percent.' Complete");';
             echo '$("span.rowprocess").text("Processed Row: '.$rowproC++.' out of '.($total_files).'");';
             echo '$("span.empname").text("Entry: ");';
             echo '</script>';                                
             flush();
             ob_flush();
             //usleep(100);



             $store_handled  = $this->Mms_mod->get_entries_reorder_report_data_header_final_by_bu($reorder_batch);
             $store          = trim($store_handled[0]['value_']);
             $report_details = $this->Mms_mod->generate_reorder_report_mod($reorder_batch,$store,$store_handled[0]['user_id']);
                        
             // get the past 3 month years from the current month
             $past_3_month_years = array();
             for($i = 1; $i <= 3; $i++) 
             {
                 $html_date = date('Y-m-01',strtotime($store_handled[0]['reorder_date']));

                 $past_month_year      = date('Y-m', strtotime("-{$i} month", strtotime($html_date)));
                 $past_3_month_years[] = $past_month_year;
             }         
            


// ----mga booking server ni..i insert ni apil if cdc ang nag login then supermarket pud siya ----------------testing----------------------------------------------------------------------------
             $vend_no    = $store_handled[0]['supplier_code']; // 'S3590';

             $db_details = $this->Mms_mod->get_connection($current_user_login[0]['databse_id']);
             if($current_user_login[0]['value_'] == 'cdc' && $db_details[0]['department'] == 'SM'  && $_POST['v_code'] != 'UPLOAD OLD SALES'   &&  $apilon_booking == 1)
             {

                    $select                   = '*';
                    $table_id                 = 'reorder_store';
                    $where_booking['bu_type'] = 'NON STORE'; 
                    $booking_srv_list         = $this->Mms_mod->select($select,$table_id,$where_booking);
  
                    foreach($booking_srv_list as $book_server)
                    {
                         $select              = '*';
                         $table_db            = 'database';
                         $where_db['db_id']   = $book_server['databse_id'];
                         $get_connection      = $this->Mms_mod->select($select,$table_db,$where_db);

                         $insert_data_header['supplier_code']    = $vend_no; 
                         $insert_data_header['supplier_name']    = $store_handled[0]['supplier_name']; 
                         $insert_data_header['lead_time_factor'] = $store_handled[0]['lead_time_factor'];
                         

                         $insert_data_header['month_1']          = strtoupper(date('M',strtotime($past_3_month_years[2])));
                         $insert_data_header['month_2']          = strtoupper(date('M',strtotime($past_3_month_years[1])));
                         $insert_data_header['month_3']          = strtoupper(date('M',strtotime($past_3_month_years[0])));

                         $insert_data_header['reorder_batch']    = $reorder_batch;                         
                         $table_header                           = 'reorder_report_data_header_final';

                         foreach($get_connection  as $con)
                         {
                             $username    = $con['username'];
                             $password    = $con['password']; 
                             $connection  = $con['db_name'];
                             $sub_db_name = $con['sub_db_name'];

                             $insert_data_header['store'] = $con['store'];   
                             $insert_data_header['db_id'] = $con['db_id'];  
                         }

                            
                         $reorder_number = $this->Mms_mod->insert_table($table_header,$insert_data_header);



                         // $connect = odbc_connect($connection, $username, $password);

                         // $table_1 = '['.$sub_db_name.'$Sales Invoice Header]';       
                         // $table_2 = '['.$sub_db_name.'$Sales Invoice Line]';   
                        

                             //var_dump($past_3_month_years);
                         

                             foreach($report_details as $rep_det)
                             {            

                                 $exp_yr_m_1     = explode("-",$past_3_month_years[2]);  
                                 $exp_yr_m_2     = explode("-",$past_3_month_years[1]);  
                                 $exp_yr_m_3     = explode("-",$past_3_month_years[0]);  
                                 $row_data_arr   = array();
                                 $item_code_arr  = array();
                                                                         


                                 $mdl_data =  $this->Mms_mod->get_middleware_data($exp_yr_m_1[0],$exp_yr_m_2[0],$exp_yr_m_3[0],number_format($exp_yr_m_1[1]),number_format($exp_yr_m_2[1]),number_format($exp_yr_m_3[1]),$vend_no,$rep_det['item_code']);

                                 if(!empty($mdl_data))
                                 {                                  
                                     foreach ($mdl_data as $hd_ln_row)
                                     {
                                           
                                           
                                          array_push($row_data_arr,array(
                                                                           "item_code"   =>$hd_ln_row['no_'], 
                                                                           "Description" =>$hd_ln_row['description'],
                                                                           "uom"         =>$hd_ln_row['unit_of_measure'], 
                                                                           "year"        =>$hd_ln_row['year'],
                                                                           "month"       =>$hd_ln_row['month'],                                                                           
                                                                           "tot_qty"     =>$hd_ln_row['quantity'],
                                                                           "variant_code"=>$hd_ln_row['variant_code']
                                                                        ));

                                          if(!in_array($hd_ln_row['no_'],$item_code_arr))
                                          {
                                             array_push($item_code_arr,$hd_ln_row['no_']);
                                          }

                                          echo '<script language="JavaScript">';
                                          echo '$("span.filename").text("Fetching Header and Reorder lines from server: '.$book_server['display_name'].' - item code: '.$hd_ln_row['no_'].'");';                            
                                          echo '</script>';                                
                                          flush();
                                          ob_flush();
                                         // usleep(100);

                                          
                                     }


                                     for($itm=0;$itm<count($item_code_arr);$itm++)
                                     {
                                         $insert_data_lines = array();


                                         $three_months = array();
                                         for($pst_m=0;$pst_m<count($past_3_month_years);$pst_m++)
                                         {                       
                                             $exp_m_yr     = explode('-',$past_3_month_years[$pst_m]); 
                                             
                                             $temp_tot_qty = 0;
                                             foreach($row_data_arr as  $rw)
                                             {
                                                 if($item_code_arr[$itm] == $rw['item_code'] && $rw['year'] == $exp_m_yr[0] && round($rw['month']) == round($exp_m_yr[1]) )
                                                 {                                                      
                                                      $temp_tot_qty += $rw['tot_qty'];
                                                      $description   = $rw['Description'];
                                                      $uom           = $rw['uom'];
                                                 }
                                             }
                                             array_push($three_months,$temp_tot_qty);
                                         }


                                          $insert_data_lines['reorder_number']   = $reorder_number;
                                          $insert_data_lines['item_code']        = $item_code_arr[$itm];
                                          $insert_data_lines['Item_description'] = $description;                                   
                                          $insert_data_lines['uom']              = $rep_det['uom']; 

                                          $final_qty_1 = $this->Mms_mod->convert_unit_of_measure($book_server['databse_id'],$item_code_arr[$itm],$uom,$rep_det['uom'],$three_months[2]);
                                          $insert_data_lines['month_sales_1']    = $final_qty_1;

                                          $final_qty_2 = $this->Mms_mod->convert_unit_of_measure($book_server['databse_id'],$item_code_arr[$itm],$uom,$rep_det['uom'],$three_months[1]);
                                          $insert_data_lines['month_sales_2']    = $final_qty_2;

                                          $final_qty_3 = $this->Mms_mod->convert_unit_of_measure($book_server['databse_id'],$item_code_arr[$itm],$uom,$rep_det['uom'],$three_months[0]);
                                          $insert_data_lines['month_sales_3']    = $final_qty_3;





                                          $ave_sales = ($final_qty_1 + $final_qty_2 + $final_qty_3) / 90;

                                          $insert_data_lines['ave_sales']        = $ave_sales;

                                          $table_lines = 'reorder_report_data_lines_final';

                                          $this->Mms_mod->insert_table($table_lines,$insert_data_lines);

                                         

                                          echo '<script language="JavaScript">';
                                          echo '$("span.filename").text("Inserting Header and Reorder lines from server: '.$book_server['display_name'].' - item code: '.$item_code_arr[$itm].'");';                            
                                          echo '</script>';                                
                                          flush();
                                          ob_flush();
                                          //usleep(100);
                                     }

                                     //var_dump($row_data_arr);
                                 }
                                 else 
                                 {
                                          $insert_data_lines = array();

                                          $insert_data_lines['reorder_number']   = $reorder_number;
                                          $insert_data_lines['item_code']        = $rep_det['item_code'];
                                          $insert_data_lines['Item_description'] = $rep_det['Item_description'];
                                          $insert_data_lines['uom']              = $rep_det['uom']; 

                                          $insert_data_lines['month_sales_1']    = '0.00';
                                          $insert_data_lines['month_sales_2']    = '0.00';
                                          $insert_data_lines['month_sales_3']    = '0.00';
                                          $insert_data_lines['ave_sales']        = '0.00';
                                          $table_lines                           = 'reorder_report_data_lines_final';
                                          $this->Mms_mod->insert_table($table_lines,$insert_data_lines);

                                          echo '<script language="JavaScript">';
                                          echo '$("span.filename").text("Inserting Header and Reorder lines from server: '.$book_server['display_name'].' - item code: '.$rep_det['item_code'].'");';                            
                                          echo '</script>';                                
                                          flush();
                                          ob_flush();
                                 }
                             }

                    }

             }
// ----end of mga booking server ni..i insert ni apil if cdc ang nag login then supermarket pud siya ----------------original----------------------------------------------------------------------------





// ----mga booking server ni..i insert ni apil if cdc ang nag login then supermarket pud siya ----------------original----------------------------------------------------------------------------

             
             //var_dump($report_details);

             // $vend_no    = $store_handled[0]['supplier_code']; // 'S3590';

             // $db_details = $this->Mms_mod->get_connection($current_user_login[0]['databse_id']);
             // if($current_user_login[0]['value_'] == 'cdc' && $db_details[0]['department'] == 'SM'  && $_POST['v_code'] != 'UPLOAD OLD SALES'   &&  $apilon_booking == 1)
             // {

             //        $select                   = '*';
             //        $table_id                 = 'reorder_store';
             //        $where_booking['bu_type'] = 'NON STORE'; 
             //        $booking_srv_list         = $this->Mms_mod->select($select,$table_id,$where_booking);
  
             //        foreach($booking_srv_list as $book_server)
             //        {
             //             $select              = '*';
             //             $table_db            = 'database';
             //             $where_db['db_id']   = $book_server['databse_id'];
             //             $get_connection      = $this->Mms_mod->select($select,$table_db,$where_db);

             //             $insert_data_header['supplier_code']    = $vend_no; 
             //             $insert_data_header['supplier_name']    = $store_handled[0]['supplier_name']; 
             //             $insert_data_header['lead_time_factor'] = $store_handled[0]['lead_time_factor'];
                         

             //             $insert_data_header['month_1']          = strtoupper(date('M',strtotime($past_3_month_years[2])));
             //             $insert_data_header['month_2']          = strtoupper(date('M',strtotime($past_3_month_years[1])));
             //             $insert_data_header['month_3']          = strtoupper(date('M',strtotime($past_3_month_years[0])));

             //             $insert_data_header['reorder_batch']    = $reorder_batch;                         
             //             $table_header                           = 'reorder_report_data_header_final';

             //             foreach($get_connection  as $con)
             //             {
             //                 $username    = $con['username'];
             //                 $password    = $con['password']; 
             //                 $connection  = $con['db_name'];
             //                 $sub_db_name = $con['sub_db_name'];

             //                 $insert_data_header['store'] = $con['store'];   
             //                 $insert_data_header['db_id'] = $con['db_id'];  
             //             }

                            
             //             $reorder_number = $this->Mms_mod->insert_table($table_header,$insert_data_header);



             //             $connect = odbc_connect($connection, $username, $password);

             //             $table_1 = '['.$sub_db_name.'$Sales Invoice Header]';       
             //             $table_2 = '['.$sub_db_name.'$Sales Invoice Line]';   
                        

             //                 //var_dump($past_3_month_years);
                         

             //                 foreach($report_details as $rep_det)
             //                 {            

             //                     $exp_yr_m_1     = explode("-",$past_3_month_years[2]);  
             //                     $exp_yr_m_2     = explode("-",$past_3_month_years[1]);  
             //                     $exp_yr_m_3     = explode("-",$past_3_month_years[0]);  
             //                     $row_data_arr   = array();
             //                     $item_code_arr  = array();
             //                     $table_query  = "  
             //                                        SELECT
             //                                                line.[Quantity],
             //                                                line.[No_],
             //                                                line.[Description],
             //                                                line.[Unit of Measure],
             //                                                YEAR([Posting Date]) AS [Year],
             //                                                MONTH([Posting Date]) AS [Month]

             //                                        FROM 
             //                                               ".$table_1."  as head
             //                                        INNER JOIN  ".$table_2." AS line ON line.[Document No_] = head.[No_]                                                
             //                                        WHERE 
             //                                               (
             //                                                  (YEAR([Posting Date]) = '".$exp_yr_m_1[0]."' AND MONTH([Posting Date]) = '".$exp_yr_m_1[1]."' ) OR
             //                                                  (YEAR([Posting Date]) = '".$exp_yr_m_2[0]."' AND MONTH([Posting Date]) = '".$exp_yr_m_2[1]."' ) OR
             //                                                  (YEAR([Posting Date]) = '".$exp_yr_m_3[0]."' AND MONTH([Posting Date]) = '".$exp_yr_m_3[1]."' ) 
             //                                               )
             //                                        AND 
             //                                               line.[Vendor No_] = '".$vend_no."'   
             //                                        AND 
             //                                               line.[No_] = '".$rep_det['item_code']."'  

             //                                               ";                                                


             //                     $table_hd_ln_row    = odbc_exec($connect, $table_query);

             //                    // echo 'num rows:'.odbc_num_rows($table_hd_ln_row).'<br>'; 

             //                     if(odbc_num_rows($table_hd_ln_row) > 0)
             //                     {                                  
             //                         while ($hd_ln_row = odbc_fetch_array($table_hd_ln_row))
             //                         {
                                           
                                           
             //                              array_push($row_data_arr,array(
             //                                                               "item_code"   =>$hd_ln_row['No_'], 
             //                                                               "Description" =>$hd_ln_row['Description'],
             //                                                               "uom"         =>$hd_ln_row['Unit of Measure'], 
             //                                                               "year"        =>$hd_ln_row['Year'],
             //                                                               "month"       =>$hd_ln_row['Month'],                                                                           
             //                                                               "tot_qty"     =>$hd_ln_row['Quantity']
             //                                                            ));

             //                              if(!in_array($hd_ln_row['No_'],$item_code_arr))
             //                              {
             //                                 array_push($item_code_arr,$hd_ln_row['No_']);
             //                              }

             //                              echo '<script language="JavaScript">';
             //                              echo '$("span.filename").text("Fetching Header and Reorder lines from server: '.$book_server['display_name'].' - item code: '.$hd_ln_row['No_'].'");';                            
             //                              echo '</script>';                                
             //                              flush();
             //                              ob_flush();
             //                             // usleep(100);

                                          
             //                         }


             //                         for($itm=0;$itm<count($item_code_arr);$itm++)
             //                         {
             //                             $insert_data_lines = array();


             //                             $three_months = array();
             //                             for($pst_m=0;$pst_m<count($past_3_month_years);$pst_m++)
             //                             {                       
             //                                 $exp_m_yr     = explode('-',$past_3_month_years[$pst_m]); 
                                             
             //                                 $temp_tot_qty = 0;
             //                                 foreach($row_data_arr as  $rw)
             //                                 {
             //                                     if($item_code_arr[$itm] == $rw['item_code'] && $rw['year'] == $exp_m_yr[0] && round($rw['month']) == round($exp_m_yr[1]) )
             //                                     {                                                      
             //                                          $temp_tot_qty += $rw['tot_qty'];
             //                                          $description   = $rw['Description'];
             //                                          $uom           = $rw['uom'];
             //                                     }
             //                                 }
             //                                 array_push($three_months,$temp_tot_qty);
             //                             }


             //                              $insert_data_lines['reorder_number']   = $reorder_number;
             //                              $insert_data_lines['item_code']        = $item_code_arr[$itm];
             //                              $insert_data_lines['Item_description'] = $description;                                   
             //                              $insert_data_lines['uom']              = $rep_det['uom']; 

             //                              $final_qty_1 = $this->Mms_mod->convert_unit_of_measure($book_server['databse_id'],$item_code_arr[$itm],$uom,$rep_det['uom'],$three_months[2]);
             //                              $insert_data_lines['month_sales_1']    = $final_qty_1;

             //                              $final_qty_2 = $this->Mms_mod->convert_unit_of_measure($book_server['databse_id'],$item_code_arr[$itm],$uom,$rep_det['uom'],$three_months[1]);
             //                              $insert_data_lines['month_sales_2']    = $final_qty_2;

             //                              $final_qty_3 = $this->Mms_mod->convert_unit_of_measure($book_server['databse_id'],$item_code_arr[$itm],$uom,$rep_det['uom'],$three_months[0]);
             //                              $insert_data_lines['month_sales_3']    = $final_qty_3;





             //                              $ave_sales = ($final_qty_1 + $final_qty_2 + $final_qty_3) / 90;

             //                              $insert_data_lines['ave_sales']        = $ave_sales;

             //                              $table_lines = 'reorder_report_data_lines_final';

             //                              $this->Mms_mod->insert_table($table_lines,$insert_data_lines);

                                         

             //                              echo '<script language="JavaScript">';
             //                              echo '$("span.filename").text("Inserting Header and Reorder lines from server: '.$book_server['display_name'].' - item code: '.$item_code_arr[$itm].'");';                            
             //                              echo '</script>';                                
             //                              flush();
             //                              ob_flush();
             //                              //usleep(100);
             //                         }

             //                         //var_dump($row_data_arr);
             //                     }
             //                     else 
             //                     {
             //                              $insert_data_lines = array();

             //                              $insert_data_lines['reorder_number']   = $reorder_number;
             //                              $insert_data_lines['item_code']        = $rep_det['item_code'];
             //                              $insert_data_lines['Item_description'] = $rep_det['Item_description'];
             //                              $insert_data_lines['uom']              = $rep_det['uom']; 

             //                              $insert_data_lines['month_sales_1']    = '0.00';
             //                              $insert_data_lines['month_sales_2']    = '0.00';
             //                              $insert_data_lines['month_sales_3']    = '0.00';
             //                              $insert_data_lines['ave_sales']        = '0.00';
             //                              $table_lines                           = 'reorder_report_data_lines_final';
             //                              $this->Mms_mod->insert_table($table_lines,$insert_data_lines);

             //                              echo '<script language="JavaScript">';
             //                              echo '$("span.filename").text("Inserting Header and Reorder lines from server: '.$book_server['display_name'].' - item code: '.$rep_det['item_code'].'");';                            
             //                              echo '</script>';                                
             //                              flush();
             //                              ob_flush();
             //                     }
             //                 }

             //        }

             // } 
// ----end of booking server loop original------------------------------------------------------------ --------------------------------------------------------------------------------------------






             for($a=0;$a<count($file_contents);$a++)
             {
// ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ 3  ^^^^^^^^^^^^^^
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

                   
                 if(strstr($RESS2,'Item Vendor Sales') &&  $_POST['v_code'] != 'UPLOAD OLD SALES' )
                 {                    
                     foreach($reorder_list as $reord)
                     {
                         if($reord['store'] == $store_array[$a])
                         {                                   
                             $this->Mms_mod->extract_vendor($RESS2,$store_array[$a],$reord['reorder_number'],$reorder_batch);                                 
                         }
                     }
                 }   


                      
                 echo '<script language="JavaScript">';
                 echo '$("span.filename").text("Inserting Item vendor");';
                 echo '$("div#percontent").css({"width":"'.$percent.'"});';
                 echo '$("span.status").text("Status: '.$percent.' Complete");';
                 echo '$("span.rowprocess").text("Processed Row: '.$rowproC.' out of '.$total_files.'");';
                 echo '$("span.empname").text("Entry: ");';
                 echo '</script>';                                     
                 flush();
                 ob_flush();
                 //usleep(100);                                      
                      
                                   
             }

             echo '<script language="JavaScript">';             
             echo '$("div#percontent").css({"width":"'.$percent.'"});';
             echo '$("span.status").text("Status: '.$percent.' Complete");';
             echo '$("span.rowprocess").text("Processed Row: '.$rowproC++.' out of '.($total_files).'");';
             echo '$("span.empname").text("Entry: ");';
             echo '</script>';       
             flush();
             ob_flush();
             //usleep(100);   


            
// ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ 4  ^^^^^^^^^^^^^^
             $final_po_list = array();  
             if($_POST['v_code'] != 'UPLOAD OLD SALES')
             {

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


                     $RESS2 = '';                                                                   
                     $RESS2 = strip_tags($file_contents[$a]);  
                     if(strstr($RESS2,'","') )  //if native sya nya ga upload siya ug purchase line ug purchase header
                     {
                         //var_dump($store_array[$a],$reorder_batch);
                         $table                      = 'reorder_report_data_header_final';
                         $where_fin['store']         = $store_array[$a];
                         $where_fin['reorder_batch'] = $reorder_batch;
                         $select                     = '*';
                         $header_final_details       = $this->Mms_mod->select($select,$table,$where_fin);

                         $header_arr   = array();
                         $line_arr     = array();
                         $explode_rows = explode("\n", $RESS2);
                         for($b=0;$b<count($explode_rows);$b++)
                         {
                             $explode_columns =  explode('","',$explode_rows[$b]);  
                             if(count($explode_columns) == 8)
                             {                               
                                  array_push($line_arr,array("vendor"=>$explode_columns[2],'document_no'=>str_replace('"',"",$explode_columns[0]),'pending_qty'=>$explode_columns[6],'item_code'=>$explode_columns[3],'uom'=>$explode_columns[4]) );
                             }
                             else 
                             if(count($explode_columns) == 5)
                             {  
                                  array_push($header_arr,array("vendor"=>str_replace('"','',$explode_columns[0]),'document_no'=>$explode_columns[1],'date'=>$explode_columns[2] ) ); 
                             }
                         }  


                         foreach($header_arr as $head)
                         {
                             foreach($line_arr as $ln)
                             {
                                 if($head['vendor'] == $ln['vendor'] && $head['document_no'] == $ln['document_no'])
                                 {
                                      array_push($final_po_list,array("db_id"=>$header_final_details[0]['db_id'],"vendor"=>$head['vendor'],'document_no'=>$head['document_no'],'date'=>$head['date'],'pending_qty'=>$ln['pending_qty'],'item_code'=>$ln['item_code'],'uom'=>$ln['uom']));                                     
                                 }
                             }
                         }
                         echo '<script language="JavaScript">';
                         echo '$("span.filename").text("Inserting Item vendor from Native Navision");';
                         echo '$("div#percontent").css({"width":"'.$percent.'"});';
                         echo '$("span.status").text("Status: '.$percent.' Complete");';
                         echo '$("span.rowprocess").text("Processed Row: '.$rowproC.' out of '.$total_files.'");';
                         echo '$("span.empname").text("Entry: ");';
                         echo '</script>';                                     
                         flush();
                         ob_flush();
                         //usleep(100);   
                            
                     }
                 }


             
                 echo '<script language="JavaScript">';
                 echo '$("span.filename").text("Inserting Item vendor from Native Navision");';
                 echo '$("div#percontent").css({"width":"'.$percent.'"});';
                 echo '$("span.status").text("Status: '.$percent.' Complete");';
                 echo '$("span.rowprocess").text("Processed Row: '.$rowproC++.' out of '.($total_files).'");';
                 echo '$("span.empname").text("Entry: ");';
                 echo '</script>';                                     
                 flush();
                 ob_flush();
                 //usleep(100);   
             }   



             
             if($_POST['v_code'] != 'UPLOAD OLD SALES')
             {

                     $reorder_report_data_header_final_DETAILS = $this->Mms_mod->get_reorder_report_data_header_final_details($reorder_batch); 

// ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ 5  ^^^^^^^^^^^^^^

                     if($rowproC >0 && $total_files >0)
                     {                                    
                     $percent = intval($rowproC/$total_files * 100)."%";                    
                     }
                     else 
                     {
                     $percent = "100%";
                     } 



                     foreach($reorder_report_data_header_final_DETAILS as $det)
                     {                
                         $where_reord_store['value_']     = $det['store'];
                         $where_reord_store['databse_id'] = $det['db_id'];
                         $table                           = 'reorder_store';
                         $select                          = '*';
                         $store_details = $this->Mms_mod->select($select,$table,$where_reord_store);
                         $db_details    = $this->Mms_mod->get_connection($det['db_id']);

                         if($db_details[0]['nav_type'] == 'SQL' && $det['bu_type'] != 'NON STORE')
                         {
                                //*******************************************************pagkuha sa mga PO nga gikan sa SQL***********************************************************************************                                            
                               
                                 $databse_id    = $det['db_id'];

                                 $sql_po_list   = $this->Mms_mod->get_sql_po(trim($det["supplier_code"]),$past_3_month_years[2],$past_3_month_years[0],'',$databse_id);
                                 
                                 if(!empty($sql_po_list))
                                 {        
                                     foreach($sql_po_list as $sql)
                                     {      
                                         $proceed = true;

                                         // $search_po = $this->Mms_mod->search_mms_middleware_header($sql['document_no'],'',$databse_id);
                                         // if(!empty($search_po))
                                         // {
                                         //     if(strstr($search_po[0]['textfile_name'],'-PST'))
                                         //     {
                                         //         $proceed = false;
                                         //     }
                                         // }


                                         if(substr($sql['document_no'], 0, 4) === "SMGM" && $proceed == true)      
                                         {
                                              array_push($final_po_list,array("db_id"=>$databse_id,"vendor"=>$sql['vendor'],'document_no'=>$sql['document_no'],'date'=>$sql['date'],'pending_qty'=>$sql['pending_qty'],'item_code'=>$sql['item_code'],'uom'=>$sql['uom']));                                     
                                            
                                         
                                              echo '<script language="JavaScript">';
                                              echo '$("span.filename").text("Fetching Pending PO in SQL '.$db_details[0]['db_name'].' ");';
                                              echo '$("div#percontent").css({"width":"'.$percent.'"});';
                                              echo '$("span.status").text("Status: '.$percent.' Complete");';
                                              echo '$("span.rowprocess").text("Processed Row: '.$rowproC.' out of '.$total_files.'");';
                                              echo '$("span.empname").text("Entry: ");';
                                              echo '</script>';                                     
                                              flush();
                                              ob_flush();
                                              //usleep(100);    
                                         }  
                                     }
                                 }
                               //********************************************************************************************************************************************************************************* 
                         }
                          
                     }

             echo '<script language="JavaScript">';             
             echo '$("div#percontent").css({"width":"'.$percent.'"});';
             echo '$("span.status").text("Status: '.$percent.' Complete");';
             echo '$("span.rowprocess").text("Processed Row: '.$rowproC++.' out of '.($total_files).'");';
             echo '$("span.empname").text("Entry: ");';
             echo '</script>';                                
             flush();
             ob_flush();
             //usleep(100);
             }               

            $from_day = date('Y-m',strtotime(date($past_3_month_years[2])));
            $to_day   = date('Y-m',strtotime(date($past_3_month_years[0])));
            

// ----------------------------------------------pagkuha sa mga PO nga gikan sa textile -------------originial-------------------------------------------------------------------------------
//              if($_POST['v_code'] != 'UPLOAD OLD SALES')
//              {

// // ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ 6  ^^^^^^^^^^^^^^
//                      if($rowproC >0 && $total_files >0)
//                      {                                    
//                      $percent = intval($rowproC/$total_files * 100)."%";                    
//                      }
//                      else 
//                      {
//                      $percent = "100%";
//                      } 


//                      $partial_po_list      = array();
//                      $directory_arr        = array();
//                      $file_extension_array = array(); 
//                      foreach($reorder_report_data_header_final_DETAILS as $det)  //loop sa mga store involve sa reorder kadtong gi checkan.. reorder_report_data_header_final nga table akong gikuhaan ani
//                      {

//                             if($det['bu_type'] != 'NON STORE')
//                             {
                                
//                                   $item_code                 = '';                 
                                  
//                                   $table                     = 'reorder_store';
//                                   $where_store['value_']     = $det['store'];
//                                   $where_store['databse_id'] = $det['db_id'];
//                                   $select                    = '*';
//                                   $store_details = $this->Mms_mod->select($select,$table,$where_store);

//                                   $get_dir       = $this->Mms_mod->get_po_directory($store_details[0]['po_db_id']);                                 


//                                   $dir           = $get_dir[0]['directory'];
//                                   $dir           = str_replace('\\\\','\\\\',$dir);
//                                   $dir           = str_replace('\\','\\',$dir);
//                                   $username      = $get_dir[0]['username'];
//                                   $password      = $get_dir[0]['password'];
                                  
//                                   //use the 'net use' command to map the network drive with the specified credentials
//                                   system("net use {$dir} /user:{$username} {$password} >nul");

//                                   if ($handle = opendir($dir."\\")) 
//                                   {
//                                         $po_arr = array();

//                                         // Use glob to find file extensions files in the opened directory
//                                         $txtFiles = glob($dir."\\*.".$store_details[0]['file_extension']);

//                                         // Check if any .txt files were found
//                                         if (!empty($txtFiles)) 
//                                         {

//                                              // Filter the files based on date modified
//                                              $filteredFiles = array_filter($txtFiles, function ($txtFile)  use ($from_day, $to_day)
//                                              {
//                                                     // Get the date modified of the file in 'Y-m-d' format
//                                                     $dateModified = date('Y-m-d', filemtime($txtFile));

//                                                      // Check if the file was modified between $from_day and $to_day
//                                                         $from_ = date('Y-m-d', strtotime($from_day));
//                                                         $to_ = date('Y-m-d', strtotime($to_day));                                                       

//                                                         return ($dateModified >= $from_ && $dateModified <= $to_);
//                                              });


//                                               if (!empty($filteredFiles)) 
//                                               {
//                                                 foreach ($filteredFiles as $txtFile) 
//                                                 {
//                                                     $file_name     = basename($txtFile);
//                                                     $exp_file_name = explode(".",$file_name);

//                                                     $check_data['document_number'] = $exp_file_name[0];
//                                                     $check_po = $this->Mms_mod->get_reorder_po($check_data);

//                                                     $proceed  = false;

//                                                     if( (empty($check_po)) ||
//                                                         (!empty($check_po) && $check_po[0]['status'] != 'Cancel')
//                                                       )
//                                                       {
//                                                          $proceed = true;
//                                                       }
//                                                       else 
//                                                       {
//                                                             // Rename the file by adding an underscore to its name
//                                                             $newFileName = $file_name."_CANCELED";
//                                                             $newFilePath = $dir."\\".$newFileName;
//                                                             rename($dir."\\".$file_name, $newFilePath);
//                                                             // if () 
//                                                             // {
//                                                             //     echo "File renamed: $newFileName<br>";
//                                                             // } else {
//                                                             //     echo "Failed to rename file: $file<br>";
//                                                             // }
//                                                       }
                                                    

//                                                       if($proceed == true)
//                                                       {

//                                                             // You can perform further actions with the found files here
//                                                             // Read the content of the text file
//                                                             $fileContent = file_get_contents($txtFile);

                                                            
//                                                             // Explode the content into an array of lines using EOL
//                                                             $lines = explode(PHP_EOL, $fileContent);

//                                                             $header = array();
//                                                             for($a=0;$a<count($lines);$a++)
//                                                             {
//                                                                 if ( !(strstr($lines[$a], '[HEADER]') || strstr($lines[$a], '[LINES]'))) 
//                                                                 {                                        
//                                                                                 $line     =  str_replace('"','',$lines[$a]);
//                                                                                 $line_exp = explode("|",$line); 

//                                                                                 if(count($line_exp) == 7) //if header
//                                                                                 {                                                            
//                                                                                      array_push($header,array('document_no'=>$line_exp[0],'date'=>$line_exp[1],'vendor'=>$line_exp[5]) );          
//                                                                                 }
//                                                                                 if(count($line_exp) == 11) //if lines                                        
//                                                                                 {

//                                                                                      if($item_code == '')  
//                                                                                      {
//                                                                                          array_push($po_arr,array('document_no'=>$header[0]['document_no'],'date'=>$header[0]['date'],'vendor'=>$header[0]['vendor'],'item_code'=>$line_exp[1],'pending_qty'=>$line_exp[2],'uom'=>$line_exp[4]));        
//                                                                                      } 
//                                                                                      else 
//                                                                                      {
//                                                                                          if($item_code == $line_exp[1])
//                                                                                          {
//                                                                                              array_push($po_arr,array('document_no'=>$header[0]['document_no'],'date'=>$header[0]['date'],'vendor'=>$header[0]['vendor'],'item_code'=>$line_exp[1],'pending_qty'=>$line_exp[2],'uom'=>$line_exp[4]));         
//                                                                                          }
//                                                                                      }                                                                                                                       
//                                                                                 }  
//                                                                                 echo '<script language="JavaScript">';
//                                                                                 echo '$("span.filename").text("retrieving data from textfiles in  Store '.strtoupper($det['store']).' --- document number:'.$line_exp[0].'");';                                                       
//                                                                                 echo '$("span.empname").text("Entry: ");';
//                                                                                 echo '</script>';                                                        
//                                                                                 flush();
//                                                                                 ob_flush();
//                                                                                 //usleep(100);
//                                                                  }
//                                                             }

//                                                       }

//                                                 }
//                                             } else {
//                                                 echo "File not modified between January and March.<br>";
//                                             }



                                            
//                                         }
//                                         else
//                                         {
//                                             echo "No .txt files found in directory: {$dir}<br>";
//                                         }
//                                   } 
//                                   else 
//                                   {
//                                         // Handle the error
//                                         echo "Failed to open directory: {$dir}\n";
//                                   }


//                                     if(!empty($po_arr))
//                                    {        
//                                          foreach($po_arr as $po)
//                                          {
//                                               if( (trim($po['vendor']) == trim($det["supplier_code"]) ) && !in_array($po['document_no'], array_column($final_po_list, "document_no")) )
//                                               {              
//                                                      array_push($partial_po_list,array("db_id"=>$det['db_id'],"vendor"=>$po['vendor'],'document_no'=>$po['document_no'],'date'=>$po['date'],'pending_qty'=>$po['pending_qty'],'item_code'=>$po['item_code'],'uom'=>$po['uom']));                   
//                                               }
//                                          }
//                                    }                         

//                             }    
                         
//                      } 




                   

                    

//                      echo '<script language="JavaScript">';
//                      echo '$("div#percontent").css({"width":"'.$percent.'"});';
//                      echo '$("span.status").text("Status: '.$percent.' Complete");';
//                      echo '$("span.rowprocess").text("Processed Row: '.$rowproC++.' out of '.($total_files+1).'");';
//                      echo '$("span.empname").text("Entry: ");';
//                      echo '</script>';            
//                      str_repeat(' ',1024*64);
//                      flush();
//                      ob_flush();
//                      //usleep(100);
//              }  
// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


// ----------------------------------------------pagkuha sa mga PO nga gikan sa textile -------------updated-------------------------------------------------------------------------------
             if($_POST['v_code'] != 'UPLOAD OLD SALES')
             { 
                 if($rowproC >0 && $total_files >0)
                 {                                    
                     $percent = intval($rowproC/$total_files * 100)."%";                    
                 }
                 else 
                 {
                     $percent = "100%";
                 } 

                 $partial_po_list = array();
                 $from_           = date('Y-m-d', strtotime($from_day));
                 $to_             = date('Y-m-t', strtotime($to_day));     

                 

                 foreach($reorder_report_data_header_final_DETAILS as $det)  //loop sa mga store involve sa reorder kadtong gi checkan.. reorder_report_data_header_final nga table akong gikuhaan ani
                 {
                      $textfile_list =  $this->Mms_mod->get_smgm_textfiles($from_,$to_,$det["supplier_code"],$det['db_id']);
                      // echo $from_."*****".$to_."****".$det["supplier_code"]."****".$det['db_id']."*****<br>";  
                      foreach($textfile_list as $list)
                      {
                          // if(!in_array($list['document_no'], array_column($partial_po_list, "document_no")))
                          // {
                             // echo $list['document_no']."----->".$list['textfile_name']."---> db_id:".$det['db_id']."<br>";
                             array_push($partial_po_list,array("db_id"=>$list['db_id'],"vendor"=>$list['vendor'],'document_no'=>$list['document_no'],'date'=>$list['date_'],'pending_qty'=>$list['pending_qty'],'item_code'=>$list['item_code'],'uom'=>$list['uom']));                   
                          // }
                          echo '<script language="JavaScript">';
                          echo '$("div#percontent").css({"width":"'.$percent.'"});';
                          echo '$("span.status").text("Status: '.$percent.' Complete");';    
                          echo '</script>';            
                      }
                 }    

                 echo '<script language="JavaScript">';
                 echo '$("div#percontent").css({"width":"'.$percent.'"});';
                 echo '$("span.status").text("Status: '.$percent.' Complete");';
                 echo '$("span.rowprocess").text("Processed Row: '.$rowproC++.' out of '.($total_files).'");';
                 echo '$("span.empname").text("Entry: ");';
                 echo '</script>';            
                 str_repeat(' ',1024*64);
                 flush();
                 ob_flush();
             }    
// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------






 // ****************************************gitapo ang mga po nga nkuha gikan sa SQL ug  po nga gikuha gikan sa textfile **************************************************************
             if(!empty($partial_po_list)  && $_POST['v_code'] != 'UPLOAD OLD SALES')
             {
                 foreach($partial_po_list as $partial)
                 {             
                     array_push($final_po_list,array("db_id"=>$partial['db_id'],"vendor"=>$partial['vendor'],'document_no'=>$partial['document_no'],'date'=>$partial['date'],'pending_qty'=>$partial['pending_qty'],'item_code'=>$partial['item_code'],'uom'=>$partial['uom']));                                     
                    
                 }  
             }
 // ************************************************************************************************************************************************************************************


             if($_POST['v_code'] != 'UPLOAD OLD SALES')
             {

// ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ 7  ^^^^^^^^^^^^^^
                    if($rowproC >0 && $total_files >0)
                    {                                    
                         $percent = intval($rowproC/$total_files * 100)."%";                    
                    }
                    else 
                    {
                         $percent = "100%";
                    } 

                    foreach ($report_details as $details)   //inserting na sa data adto sa table reorder_report_data_po
                    {
                         $pending_qty = 0;

                         foreach($final_po_list as $fin)
                         {
                             if(trim($fin['item_code']) == trim($details["item_code"]))
                             {
                                  

                                  $table                                = 'reorder_report_data_po';
                                  $data_rep_po['document_no']           = trim($fin['document_no']);
                                  $data_rep_po['item_code']             = trim($fin['item_code']);
                                  $data_rep_po['all_store_ave_sales']   = $details["all_ave_sales"];
                                  $data_rep_po['all_store_qty_on_hand'] = $details["quantity_on_hand"];
                                  $data_rep_po['pending_qty']           = $fin['pending_qty'];
                                  $data_rep_po['reorder_batch']         = $reorder_batch;
                                  $data_rep_po['po_date']               = date('Y-m-d',strtotime($fin['date']));
                                  $data_rep_po['uom']                   = trim($fin['uom']);
                                  $data_rep_po['db_id']                 = $fin['db_id']; 

                                  $this->Mms_mod->insert($table,$data_rep_po);    
                                  $database_details = $this->Mms_mod->get_connection($fin['db_id']);                   

                                  echo '<script language="JavaScript">';                                  
                                  echo '$("div#percontent").css({"width":"'.$percent.'"});';
                                  echo '$("span.status").text("Status: '.$percent.' Complete");';    
                                  echo '$("span.filename").text("inserting Pending PO to table reorder_report_data_po  Store '.strtoupper($database_details[0]['display_name']).' --- document number:'.trim($fin['document_no']).'");';                                                                                 
                                  echo '$("span.rowprocess").text("Processed Row: '.$rowproC.' out of '.$total_files.'");';    
                                  echo '</script>';                                                        
                                  flush();
                                  ob_flush();                    
                             }
                         }

                        
                     }

                     echo '<script language="JavaScript">';             
                     echo '$("div#percontent").css({"width":"'.$percent.'"});';
                     echo '$("span.status").text("Status: '.$percent.' Complete");';
                     echo '$("span.rowprocess").text("Processed Row: '.$rowproC++.' out of '.($total_files).'");';            
                     echo '</script>';            
                     str_repeat(' ',1024*64);
                     flush();
                     ob_flush();
                     //usleep(100);
             }   

             echo '
                  
                 <script>

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
                                form.style.display = "none";
                                document.body.appendChild(form);
                                form.submit();
                                document.body.removeChild(form);
                            }
                        };  

                        
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
                        {';

                            if($_POST['v_code'] != 'UPLOAD OLD SALES')
                            {
                                  echo 'window.location.href = "'.base_url().'Mms_ctrl/mms_ui/2"';
                            }                 
                            else 
                            {
                                  echo ' io.open("POST", "'.base_url().'Mms_ctrl/mms_ui/3", 
                                         {                               
                                               "vendor_code":"UPLOAD OLD SALES",
                                               "vendor_name":"",
                                               "date_tag":"",
                                               "group_code":""
                                         },"_self");  ';
                            }

             echo  '    },5000); 
                  </script>';   
               

             ini_set('memory_limit',$memory_limit );



     }




     public function extract_file_V5()
     {  
            $apilon_booking      = $_POST['apilon_booking'];


            $store_arr           = $_POST['store_arr'];
            $data['store_array'] = json_decode($store_arr);

           
            $file_content = $_POST['file_content'];
            $data['file_contents'] = json_decode($file_content);

            $file_list             = $_POST['file_list'];
            $data['fileNames']     = json_decode($file_list);
 
            $data['v_code']        = $_POST['v_code'];
            $data['d_tag']         = $_POST['d_tag'];
            $data['group_code']    = $_POST['group_code'];            

            //$this->load->view('mms/upload_ui', $data);

            $memory_limit = ini_get('memory_limit');
            ini_set('memory_limit',-1);
            ini_set('max_execution_time', 0);

            $file_contents = json_decode($file_content);
            $store_array   = json_decode($store_arr);
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
                     
                    
                    <div class="col-md-12" style="margin-top:0%;padding:3px;">
                        <div class="col-md-12 pdd_1"></div>         
                        <button   class="back_button btn btn-danger" onclick="back_to_posting()"  style="display:none;">back to ebs</button> <div class="col-md-6 col-md-offset-3" style="padding: 10% 0%;">
                                <div class="row" style="padding-left: 18px;">                    
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
                     </div>   

                     ';

             flush();
             ob_flush();
             //usleep(100);           

             $current_user_login =  $this->Mms_mod->get_user_connection($_SESSION['user_id']);


             $insert_batch['user_id']        = $_SESSION['user_id'];    
             $insert_batch['store_id']       = $current_user_login[0]['store_id'];      

             if($_POST['v_code'] != 'UPLOAD OLD SALES')
             {
                 $insert_batch['status']     = 'Pending';  
                 $insert_batch['date_tag']   = $data['d_tag'] ;
                 $total_files = 7;
             }
             else 
             {
                 $insert_batch['status']     = 'ARCHIVE';                  
                 $insert_batch['date_tag']   = date('Y-m-d'); ;             
                 $total_files = 3;
             }


             $insert_batch['group_code_']    = $data['group_code'];
             $insert_batch['date_generated'] = date('Y-m-d H:i:s');
             $table                          = 'reorder_report_data_batch';
             $reorder_batch                  = $this->Mms_mod->insert_table($table,$insert_batch);  


             $rowproC     = 1;
// ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ 1  ^^^^^^^^^^^^^^
             if($rowproC >0 && $total_files >0)
             {                                    
                  $percent = intval($rowproC/$total_files * 100)."%";                    
             }
             else 
             {
                  $percent = "100%";
             }   


             echo '<script language="JavaScript">';
             echo '$("span.filename").text("Inserting Reorder Batch");';
             echo '$("div#percontent").css({"width":"'.$percent.'"});';
             echo '$("span.status").text("Status: '.$percent.' Complete");';
             echo '$("span.rowprocess").text("Processed Row: '.$rowproC++.' out of '.$total_files.'");';
             echo '$("span.empname").text("Entry: ");';
             echo '</script>';            
             flush();
             ob_flush();
             //usleep(100);


             
             $reorder_list = array();

             for($a=0;$a<count($file_contents);$a++)
             {
// ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ 2  ^^^^^^^^^^^^^^
                 if($rowproC >0 && $total_files >0)
                 {                                    
                   $percent = intval($rowproC/$total_files * 100)."%";                    
                 }
                 else 
                 {
                   $percent = "100%";
                 } 

                 $RESS2 = '';                                                                   
                 $RESS2 = strip_tags($file_contents[$a]);          


                 if(strstr($RESS2,'Re-order'))
                 {
                      $reorder_number =  $this->Mms_mod->extract_reorderV2($RESS2,$reorder_batch);
                      array_push($reorder_list,array("store"=>$store_array[$a],'reorder_number'=>$reorder_number));
                     //$reorder_number =  $this->Mms_mod->extract_reorder($RESS2,$store_array[$a],$reorder_batch);   //old                     
                 }

                 echo '<script language="JavaScript">';
                 echo '$("span.filename").text("Inserting Header and Reorder lines");';
                 echo '$("div#percontent").css({"width":"'.$percent.'"});';
                 echo '$("span.status").text("Status: '.$percent.' Complete");';  
                 echo '$("span.rowprocess").text("Processed Row: '.$rowproC.' out of '.$total_files.'");';
                 echo '$("span.empname").text("Entry: ");';                                
                 echo '</script>';               
                 flush();
                 ob_flush();
                 //usleep(100);                  
                                   
             }

             echo '<script language="JavaScript">';
             echo '$("span.filename").text("Inserting Header and Reorder lines");';
             echo '$("div#percontent").css({"width":"'.$percent.'"});';
             echo '$("span.status").text("Status: '.$percent.' Complete");';
             echo '$("span.rowprocess").text("Processed Row: '.$rowproC++.' out of '.$total_files.'");';
             echo '$("span.empname").text("Entry: ");';
             echo '</script>';                                
             flush();
             ob_flush();
             //usleep(100);



             $store_handled  = $this->Mms_mod->get_entries_reorder_report_data_header_final_by_bu($reorder_batch);
             $store          = trim($store_handled[0]['value_']);
             $report_details = $this->Mms_mod->generate_reorder_report_mod($reorder_batch,$store,$store_handled[0]['user_id']);
                        
             // get the past 3 month years from the current month
             $past_3_month_years = array();
             for($i = 1; $i <= 3; $i++) 
             {
                 $html_date = date('Y-m-01',strtotime($store_handled[0]['reorder_date']));

                 $past_month_year      = date('Y-m', strtotime("-{$i} month", strtotime($html_date)));
                 $past_3_month_years[] = $past_month_year;
             }         
            

// ----mga booking server ni..i insert ni apil if cdc ang nag login then supermarket pud siya --------------------------------------------------------------------------------------------

             
             //var_dump($report_details);

             $vend_no    = $store_handled[0]['supplier_code']; // 'S3590';

             $db_details = $this->Mms_mod->get_connection($current_user_login[0]['databse_id']);
             if($current_user_login[0]['value_'] == 'cdc' && $db_details[0]['department'] == 'SM'  && $_POST['v_code'] != 'UPLOAD OLD SALES'   &&  $apilon_booking == 1)
             {

                    $select                   = '*';
                    $table_id                 = 'reorder_store';
                    $where_booking['bu_type'] = 'NON STORE'; 
                    $booking_srv_list         = $this->Mms_mod->select($select,$table_id,$where_booking);
  
                    foreach($booking_srv_list as $book_server)
                    {
                         $select              = '*';
                         $table_db            = 'database';
                         $where_db['db_id']   = $book_server['databse_id'];
                         $get_connection      = $this->Mms_mod->select($select,$table_db,$where_db);

                         $insert_data_header['supplier_code']    = $vend_no; 
                         $insert_data_header['supplier_name']    = $store_handled[0]['supplier_name']; 
                         $insert_data_header['lead_time_factor'] = $store_handled[0]['lead_time_factor'];
                         

                         $insert_data_header['month_1']          = strtoupper(date('M',strtotime($past_3_month_years[2])));
                         $insert_data_header['month_2']          = strtoupper(date('M',strtotime($past_3_month_years[1])));
                         $insert_data_header['month_3']          = strtoupper(date('M',strtotime($past_3_month_years[0])));

                         $insert_data_header['reorder_batch']    = $reorder_batch;                         
                         $table_header                           = 'reorder_report_data_header_final';

                         foreach($get_connection  as $con)
                         {
                             $username    = $con['username'];
                             $password    = $con['password']; 
                             $connection  = $con['db_name'];
                             $sub_db_name = $con['sub_db_name'];

                             $insert_data_header['store'] = $con['store'];   
                             $insert_data_header['db_id'] = $con['db_id'];  
                         }

                            
                         $reorder_number = $this->Mms_mod->insert_table($table_header,$insert_data_header);



                         $connect = odbc_connect($connection, $username, $password);

                         $table_1 = '['.$sub_db_name.'$Sales Invoice Header]';       
                         $table_2 = '['.$sub_db_name.'$Sales Invoice Line]';   
                        

                             //var_dump($past_3_month_years);
                         

                             foreach($report_details as $rep_det)
                             {            

                                 $exp_yr_m_1     = explode("-",$past_3_month_years[2]);  
                                 $exp_yr_m_2     = explode("-",$past_3_month_years[1]);  
                                 $exp_yr_m_3     = explode("-",$past_3_month_years[0]);  
                                 $row_data_arr   = array();
                                 $item_code_arr  = array();
                                 $table_query  = "  
                                                    SELECT
                                                            line.[Quantity],
                                                            line.[No_],
                                                            line.[Description],
                                                            line.[Unit of Measure],
                                                            YEAR([Posting Date]) AS [Year],
                                                            MONTH([Posting Date]) AS [Month]

                                                    FROM 
                                                           ".$table_1."  as head
                                                    INNER JOIN  ".$table_2." AS line ON line.[Document No_] = head.[No_]                                                
                                                    WHERE 
                                                           (
                                                              (YEAR([Posting Date]) = '".$exp_yr_m_1[0]."' AND MONTH([Posting Date]) = '".$exp_yr_m_1[1]."' ) OR
                                                              (YEAR([Posting Date]) = '".$exp_yr_m_2[0]."' AND MONTH([Posting Date]) = '".$exp_yr_m_2[1]."' ) OR
                                                              (YEAR([Posting Date]) = '".$exp_yr_m_3[0]."' AND MONTH([Posting Date]) = '".$exp_yr_m_3[1]."' ) 
                                                           )
                                                    AND 
                                                           line.[Vendor No_] = '".$vend_no."'   
                                                    AND 
                                                           line.[No_] = '".$rep_det['item_code']."'  

                                                           ";                                                


                                 $table_hd_ln_row    = odbc_exec($connect, $table_query);

                                // echo 'num rows:'.odbc_num_rows($table_hd_ln_row).'<br>'; 

                                 if(odbc_num_rows($table_hd_ln_row) > 0)
                                 {                                  
                                     while ($hd_ln_row = odbc_fetch_array($table_hd_ln_row))
                                     {
                                           
                                           
                                          array_push($row_data_arr,array(
                                                                           "item_code"   =>$hd_ln_row['No_'], 
                                                                           "Description" =>$hd_ln_row['Description'],
                                                                           "uom"         =>$hd_ln_row['Unit of Measure'], 
                                                                           "year"        =>$hd_ln_row['Year'],
                                                                           "month"       =>$hd_ln_row['Month'],                                                                           
                                                                           "tot_qty"     =>$hd_ln_row['Quantity']
                                                                        ));

                                          if(!in_array($hd_ln_row['No_'],$item_code_arr))
                                          {
                                             array_push($item_code_arr,$hd_ln_row['No_']);
                                          }

                                          echo '<script language="JavaScript">';
                                          echo '$("span.filename").text("Fetching Header and Reorder lines from server: '.$book_server['display_name'].' - item code: '.$hd_ln_row['No_'].'");';                            
                                          echo '</script>';                                
                                          flush();
                                          ob_flush();
                                         // usleep(100);

                                          
                                     }


                                     for($itm=0;$itm<count($item_code_arr);$itm++)
                                     {
                                         $insert_data_lines = array();


                                         $three_months = array();
                                         for($pst_m=0;$pst_m<count($past_3_month_years);$pst_m++)
                                         {                       
                                             $exp_m_yr     = explode('-',$past_3_month_years[$pst_m]); 
                                             
                                             $temp_tot_qty = 0;
                                             foreach($row_data_arr as  $rw)
                                             {
                                                 if($item_code_arr[$itm] == $rw['item_code'] && $rw['year'] == $exp_m_yr[0] && round($rw['month']) == round($exp_m_yr[1]) )
                                                 {                                                      
                                                      $temp_tot_qty += $rw['tot_qty'];
                                                      $description   = $rw['Description'];
                                                      $uom           = $rw['uom'];
                                                 }
                                             }
                                             array_push($three_months,$temp_tot_qty);
                                         }


                                          $insert_data_lines['reorder_number']   = $reorder_number;
                                          $insert_data_lines['item_code']        = $item_code_arr[$itm];
                                          $insert_data_lines['Item_description'] = $description;                                   
                                          $insert_data_lines['uom']              = $rep_det['uom']; 

                                          $final_qty_1 = $this->Mms_mod->convert_unit_of_measure($book_server['databse_id'],$item_code_arr[$itm],$uom,$rep_det['uom'],$three_months[2]);
                                          $insert_data_lines['month_sales_1']    = $final_qty_1;

                                          $final_qty_2 = $this->Mms_mod->convert_unit_of_measure($book_server['databse_id'],$item_code_arr[$itm],$uom,$rep_det['uom'],$three_months[1]);
                                          $insert_data_lines['month_sales_2']    = $final_qty_2;

                                          $final_qty_3 = $this->Mms_mod->convert_unit_of_measure($book_server['databse_id'],$item_code_arr[$itm],$uom,$rep_det['uom'],$three_months[0]);
                                          $insert_data_lines['month_sales_3']    = $final_qty_3;





                                          $ave_sales = ($final_qty_1 + $final_qty_2 + $final_qty_3) / 90;

                                          $insert_data_lines['ave_sales']        = $ave_sales;

                                          $table_lines = 'reorder_report_data_lines_final';

                                          $this->Mms_mod->insert_table($table_lines,$insert_data_lines);

                                         

                                          echo '<script language="JavaScript">';
                                          echo '$("span.filename").text("Inserting Header and Reorder lines from server: '.$book_server['display_name'].' - item code: '.$item_code_arr[$itm].'");';                            
                                          echo '</script>';                                
                                          flush();
                                          ob_flush();
                                          //usleep(100);
                                     }

                                     //var_dump($row_data_arr);
                                 }
                                 else 
                                 {
                                          $insert_data_lines = array();

                                          $insert_data_lines['reorder_number']   = $reorder_number;
                                          $insert_data_lines['item_code']        = $rep_det['item_code'];
                                          $insert_data_lines['Item_description'] = $rep_det['Item_description'];
                                          $insert_data_lines['uom']              = $rep_det['uom']; 

                                          $insert_data_lines['month_sales_1']    = '0.00';
                                          $insert_data_lines['month_sales_2']    = '0.00';
                                          $insert_data_lines['month_sales_3']    = '0.00';
                                          $insert_data_lines['ave_sales']        = '0.00';
                                          $table_lines                           = 'reorder_report_data_lines_final';
                                          $this->Mms_mod->insert_table($table_lines,$insert_data_lines);

                                          echo '<script language="JavaScript">';
                                          echo '$("span.filename").text("Inserting Header and Reorder lines from server: '.$book_server['display_name'].' - item code: '.$rep_det['item_code'].'");';                            
                                          echo '</script>';                                
                                          flush();
                                          ob_flush();
                                 }
                             }

                    }

             }
// ----end of booking server loop------------------------------------------------------------ --------------------------------------------------------------------------------------------






             for($a=0;$a<count($file_contents);$a++)
             {
// ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ 3  ^^^^^^^^^^^^^^
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

                   
                 if(strstr($RESS2,'Item Vendor Sales') &&  $_POST['v_code'] != 'UPLOAD OLD SALES' )
                 {                    
                     foreach($reorder_list as $reord)
                     {
                         if($reord['store'] == $store_array[$a])
                         {                                   
                             $this->Mms_mod->extract_vendor($RESS2,$store_array[$a],$reord['reorder_number'],$reorder_batch);                                 
                         }
                     }
                 }   


                      
                 echo '<script language="JavaScript">';
                 echo '$("span.filename").text("Inserting Item vendor");';
                 echo '$("div#percontent").css({"width":"'.$percent.'"});';
                 echo '$("span.status").text("Status: '.$percent.' Complete");';
                 echo '$("span.rowprocess").text("Processed Row: '.$rowproC.' out of '.$total_files.'");';
                 echo '$("span.empname").text("Entry: ");';
                 echo '</script>';                                     
                 flush();
                 ob_flush();
                 //usleep(100);                                      
                      
                                   
             }

             echo '<script language="JavaScript">';
             echo '$("span.rowprocess").text("Processed Row: '.$rowproC++.' out of '.$total_files.'");';
             echo '$("span.empname").text("Entry: ");';
             echo '</script>';       
             flush();
             ob_flush();
             //usleep(100);   


            
// ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ 4  ^^^^^^^^^^^^^^
             $final_po_list = array();  
             if($_POST['v_code'] != 'UPLOAD OLD SALES')
             {

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


                     $RESS2 = '';                                                                   
                     $RESS2 = strip_tags($file_contents[$a]);  
                     if(strstr($RESS2,'","') )  //if native sya nya ga upload siya ug purchase line ug purchase header
                     {
                         //var_dump($store_array[$a],$reorder_batch);
                         $table                      = 'reorder_report_data_header_final';
                         $where_fin['store']         = $store_array[$a];
                         $where_fin['reorder_batch'] = $reorder_batch;
                         $select                     = '*';
                         $header_final_details       = $this->Mms_mod->select($select,$table,$where_fin);

                         $header_arr   = array();
                         $line_arr     = array();
                         $explode_rows = explode("\n", $RESS2);
                         for($b=0;$b<count($explode_rows);$b++)
                         {
                             $explode_columns =  explode('","',$explode_rows[$b]);  
                             if(count($explode_columns) == 8)
                             {                               
                                  array_push($line_arr,array("vendor"=>$explode_columns[2],'document_no'=>str_replace('"',"",$explode_columns[0]),'pending_qty'=>$explode_columns[6],'item_code'=>$explode_columns[3],'uom'=>$explode_columns[4]) );
                             }
                             else 
                             if(count($explode_columns) == 5)
                             {  
                                  array_push($header_arr,array("vendor"=>str_replace('"','',$explode_columns[0]),'document_no'=>$explode_columns[1],'date'=>$explode_columns[2] ) ); 
                             }
                         }  


                         foreach($header_arr as $head)
                         {
                             foreach($line_arr as $ln)
                             {
                                 if($head['vendor'] == $ln['vendor'] && $head['document_no'] == $ln['document_no'])
                                 {
                                      array_push($final_po_list,array("db_id"=>$header_final_details[0]['db_id'],"vendor"=>$head['vendor'],'document_no'=>$head['document_no'],'date'=>$head['date'],'pending_qty'=>$ln['pending_qty'],'item_code'=>$ln['item_code'],'uom'=>$ln['uom']));                                     
                                 }
                             }
                         }
                         echo '<script language="JavaScript">';
                         echo '$("span.filename").text("Inserting Item vendor");';
                         echo '$("div#percontent").css({"width":"'.$percent.'"});';
                         echo '$("span.status").text("Status: '.$percent.' Complete");';
                         echo '$("span.rowprocess").text("Processed Row: '.$rowproC.' out of '.$total_files.'");';
                         echo '$("span.empname").text("Entry: ");';
                         echo '</script>';                                     
                         flush();
                         ob_flush();
                         //usleep(100);   
                            
                     }
                 }


             
                 echo '<script language="JavaScript">';
                 echo '$("span.filename").text("Inserting Item vendor");';
                 echo '$("div#percontent").css({"width":"'.$percent.'"});';
                 echo '$("span.status").text("Status: '.$percent.' Complete");';
                 echo '$("span.rowprocess").text("Processed Row: '.$rowproC++.' out of '.$total_files.'");';
                 echo '$("span.empname").text("Entry: ");';
                 echo '</script>';                                     
                 flush();
                 ob_flush();
                 //usleep(100);   
             }   



             
             if($_POST['v_code'] != 'UPLOAD OLD SALES')
             {

                     $reorder_report_data_header_final_DETAILS = $this->Mms_mod->get_reorder_report_data_header_final_details($reorder_batch); 

// ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ 5  ^^^^^^^^^^^^^^

                     if($rowproC >0 && $total_files >0)
                     {                                    
                     $percent = intval($rowproC/$total_files * 100)."%";                    
                     }
                     else 
                     {
                     $percent = "100%";
                     } 



                     foreach($reorder_report_data_header_final_DETAILS as $det)
                     {                
                         $where_reord_store['value_']     = $det['store'];
                         $where_reord_store['databse_id'] = $det['db_id'];
                         $table                           = 'reorder_store';
                         $select                          = '*';
                         $store_details = $this->Mms_mod->select($select,$table,$where_reord_store);
                         $db_details    = $this->Mms_mod->get_connection($det['db_id']);

                         if($db_details[0]['nav_type'] == 'SQL' && $det['bu_type'] != 'NON STORE')
                         {
                                //*******************************************************pagkuha sa mga PO nga gikan sa SQL***********************************************************************************                                            
                               
                                 $databse_id    = $det['db_id'];

                                 $sql_po_list   = $this->Mms_mod->get_sql_po(trim($det["supplier_code"]),$past_3_month_years[2],$past_3_month_years[0],'',$databse_id);
                                 
                                 if(!empty($sql_po_list))
                                 {        
                                     foreach($sql_po_list as $sql)
                                     {      

                                         if(substr($sql['document_no'], 0, 4) === "SMGM")      
                                         {
                                              array_push($final_po_list,array("db_id"=>$databse_id,"vendor"=>$sql['vendor'],'document_no'=>$sql['document_no'],'date'=>$sql['date'],'pending_qty'=>$sql['pending_qty'],'item_code'=>$sql['item_code'],'uom'=>$sql['uom']));                                     
                                            
                                         
                                              echo '<script language="JavaScript">';
                                              echo '$("span.filename").text("Fetching Pending PO in SQL '.$db_details[0]['db_name'].' ");';
                                              echo '$("div#percontent").css({"width":"'.$percent.'"});';
                                              echo '$("span.status").text("Status: '.$percent.' Complete");';
                                              echo '$("span.rowprocess").text("Processed Row: '.$rowproC.' out of '.$total_files.'");';
                                              echo '$("span.empname").text("Entry: ");';
                                              echo '</script>';                                     
                                              flush();
                                              ob_flush();
                                              //usleep(100);    
                                         }  
                                     }
                                 }
                               //********************************************************************************************************************************************************************************* 
                         }
                          
                     }

             echo '<script language="JavaScript">';             
             echo '$("div#percontent").css({"width":"'.$percent.'"});';
             echo '$("span.status").text("Status: '.$percent.' Complete");';
             echo '$("span.rowprocess").text("Processed Row: '.$rowproC++.' out of '.$total_files.'");';
             echo '$("span.empname").text("Entry: ");';
             echo '</script>';                                
             flush();
             ob_flush();
             //usleep(100);
             }   



// ----------------------------------------------pagkuha sa mga PO nga gikan sa textile --------------------------------------------------------------------------------------------
             if($_POST['v_code'] != 'UPLOAD OLD SALES')
             {

// ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ 6  ^^^^^^^^^^^^^^
                     if($rowproC >0 && $total_files >0)
                     {                                    
                     $percent = intval($rowproC/$total_files * 100)."%";                    
                     }
                     else 
                     {
                     $percent = "100%";
                     } 

                     $partial_po_list = array();
                     foreach($reorder_report_data_header_final_DETAILS as $det)  //loop sa mga store involve sa reorder kadtong gi checkan.. reorder_report_data_header_final nga table akong gikuhaan ani
                     {

                            if($det['bu_type'] != 'NON STORE')
                            {
                                
                                  $item_code       = '';                 
                                  
                                  $table                     = 'reorder_store';
                                  $where_store['value_']     = $det['store'];
                                  $where_store['databse_id'] = $det['db_id'];
                                  $select                    = '*';
                                  $store_details       = $this->Mms_mod->select($select,$table,$where_store);

                                  $get_dir       = $this->Mms_mod->get_po_directory($store_details[0]['po_db_id']);
                                  $dir           = $get_dir[0]['directory'];
                                  $dir           = str_replace('\\\\','\\\\',$dir);
                                  $dir           = str_replace('\\','\\',$dir);
                                  $username      = $get_dir[0]['username'];
                                  $password      = $get_dir[0]['password'];
                                  
                                  // use the 'net use' command to map the network drive with the specified credentials
                                  system("net use {$dir} /user:{$username} {$password} >nul");


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

                                                    if (is_file($dir . "\\" . $entry) && pathinfo($entry, PATHINFO_EXTENSION) == $store_details[0]['file_extension']  && substr($entry, 0, 4) === "SMGM")                  
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
                                                                        }  
                                                                        echo '<script language="JavaScript">';
                                                                        echo '$("span.filename").text("retrieving data from textfiles in  Store '.strtoupper($det['store']).' --- document number:'.$line_exp[0].'");';                                                       
                                                                        echo '$("span.empname").text("Entry: ");';
                                                                        echo '</script>';                                                        
                                                                        flush();
                                                                        ob_flush();
                                                                        //usleep(100);
                                                                    }
                                                              }                                                                                            
                                                        } 
                                                    }


                                                }                          

                                   } 
                                   else
                                   {
                                         // handle the error
                                         echo "Failed to open directory: {$dir}\n";
                                   }


                                   if(!empty($po_arr))
                                   {        
                                         foreach($po_arr as $po)
                                         {
                                              if( (trim($po['vendor']) == trim($det["supplier_code"]) ) && !in_array($po['document_no'], array_column($final_po_list, "document_no")) )
                                              {              
                                                     array_push($partial_po_list,array("db_id"=>$det['db_id'],"vendor"=>$po['vendor'],'document_no'=>$po['document_no'],'date'=>$po['date'],'pending_qty'=>$po['pending_qty'],'item_code'=>$po['item_code'],'uom'=>$po['uom']));                   
                                              }
                                         }
                                   }

                            }    
                         
                     } 

                     echo '<script language="JavaScript">';
                     echo '$("div#percontent").css({"width":"'.$percent.'"});';
                     echo '$("span.status").text("Status: '.$percent.' Complete");';
                     echo '$("span.rowprocess").text("Processed Row: '.$rowproC++.' out of '.$total_files.'");';
                     echo '$("span.empname").text("Entry: ");';
                     echo '</script>';            
                     str_repeat(' ',1024*64);
                     flush();
                     ob_flush();
                     //usleep(100);
             }  
// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

 // ****************************************gitapo ang mga po nga nkuha gikan sa SQL ug  po nga gikuha gikan sa textfile **************************************************************
             if(!empty($partial_po_list)  && $_POST['v_code'] != 'UPLOAD OLD SALES')
             {
                 foreach($partial_po_list as $partial)
                 {             
                     array_push($final_po_list,array("db_id"=>$partial['db_id'],"vendor"=>$partial['vendor'],'document_no'=>$partial['document_no'],'date'=>$partial['date'],'pending_qty'=>$partial['pending_qty'],'item_code'=>$partial['item_code'],'uom'=>$partial['uom']));                                     
                    
                 }  
             }
 // ************************************************************************************************************************************************************************************


             if($_POST['v_code'] != 'UPLOAD OLD SALES')
             {

// ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ 7  ^^^^^^^^^^^^^^
                     foreach ($report_details as $details)   //inserting na sa data adto sa table reorder_report_data_po
                     {
                         $pending_qty = 0;

                         foreach($final_po_list as $fin)
                         {
                             if(trim($fin['item_code']) == trim($details["item_code"]))
                             {
                                  if($rowproC >0 && $total_files >0)
                                  {                                    
                                         $percent = intval($rowproC/$total_files * 100)."%";                    
                                  }
                                  else 
                                  {
                                         $percent = "100%";
                                  } 

                                  $table                                = 'reorder_report_data_po';
                                  $data_rep_po['document_no']           = trim($fin['document_no']);
                                  $data_rep_po['item_code']             = trim($fin['item_code']);
                                  $data_rep_po['all_store_ave_sales']   = $details["all_ave_sales"];
                                  $data_rep_po['all_store_qty_on_hand'] = $details["quantity_on_hand"];
                                  $data_rep_po['pending_qty']           = $fin['pending_qty'];
                                  $data_rep_po['reorder_batch']         = $reorder_batch;
                                  $data_rep_po['po_date']               = date('Y-m-d',strtotime($fin['date']));
                                  $data_rep_po['uom']                   = trim($fin['uom']);
                                  $data_rep_po['db_id']                 = $fin['db_id']; 

                                  $this->Mms_mod->insert($table,$data_rep_po);    
                                  $database_details = $this->Mms_mod->get_connection($fin['db_id']);                   

                                  echo '<script language="JavaScript">';
                                  echo '$("span.filename").text("inserting Pending PO to table reorder_report_data_po  Store '.strtoupper($database_details[0]['display_name']).' --- document number:'.trim($fin['document_no']).'");';                                                                                 
                                  echo '</script>';                                                        
                                  flush();
                                  ob_flush();                    
                             }
                         }

                        
                     }

                     echo '<script language="JavaScript">';             
                     echo '$("div#percontent").css({"width":"'.$percent.'"});';
                     echo '$("span.status").text("Status: '.$percent.' Complete");';
                     echo '$("span.rowprocess").text("Processed Row: '.$rowproC++.' out of '.$total_files.'");';            
                     echo '</script>';            
                     str_repeat(' ',1024*64);
                     flush();
                     ob_flush();
                     //usleep(100);
             }   

             echo '
                  
                 <script>

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
                                form.style.display = "none";
                                document.body.appendChild(form);
                                form.submit();
                                document.body.removeChild(form);
                            }
                        };  

                        
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
                        {';

                            if($_POST['v_code'] != 'UPLOAD OLD SALES')
                            {
                                  echo 'window.location.href = "'.base_url().'Mms_ctrl/mms_ui/2"';
                            }                 
                            else 
                            {
                                  echo ' io.open("POST", "'.base_url().'Mms_ctrl/mms_ui/3", 
                                         {                               
                                               "vendor_code":"UPLOAD OLD SALES",
                                               "vendor_name":"",
                                               "date_tag":"",
                                               "group_code":""
                                         },"_self");  ';
                            }

             echo  '    },5000); 
                  </script>';   
               

             ini_set('memory_limit',$memory_limit );



     }









    public function extract_file_V4()
    {
        // Access the files using $_FILES['files']
        $store_arr = json_decode($this->input->get('store_arr'));
        $v_code = $_GET['v_code'];
        $d_tag = $_GET['d_tag'];
        $group_code = $_GET['group_code'];

        // Get the file data from $_FILES['files']
        $fileData = $_FILES['files'];

        // Get the file names and content
        $fileNames = $fileData['name'];
        $fileContent = array();

        // Read the file content
        foreach ($fileData['tmp_name'] as $index => $tmpName) 
        {
            $fileContent[]    = file_get_contents($tmpName);
            $originalFileName = $fileNames[$index];
            $fileExtensions[] = pathinfo($originalFileName, PATHINFO_EXTENSION);
        }

        // Pass the data to the view
        $data = array(
            'fileNames' => $fileNames,
            'fileContent' => $fileContent,
            'fileExtensions' => $fileExtensions,
            'v_code' => $v_code,
            'd_tag' => $d_tag,
            'group_code' => $group_code,
            'store_arr' => $store_arr
        );

        $this->load->view('mms/upload_ui', $data);
        //$this->upload_files($data);
    }




    function upload_files($data)
    {
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
                    
                    </head>   
                     <h1>PLEASE WAIT. DATA IS UPLOADING</h1>
                    
                    <div class="col-md-12" style="margin-top:0%;padding:3px;">
                        <div class="col-md-12 pdd_1"></div>         
                        <button   class="back_button btn btn-danger" onclick="back_to_posting()"  style="display:none;">back to ebs</button> <div class="col-md-6 col-md-offset-3" style="padding: 10% 0%;">
                                <div class="row" style="padding-left: 18px;">                    
                                   <label class="col-md-12 pdd" style="margin:0px">
                                        <img src="'.base_url().'assets/icon_index/upload_im.PNG" width="30">
                                        UPLOADING FILE CONTENT
                                        &nbsp;&nbsp;<img src="'.base_url().'assets/img/giphy.gif" height="20">
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

                     ';

                     $total_files = 1000;
                     $rowproC     = 1;
                       for($a=0;$a<1000;$a++)
                     {
                          if($rowproC >0 && $total_files >0)
                          {                                    
                             $percent = intval($rowproC/$total_files * 100)."%";                    
                          }
                          else 
                          {
                             $percent = "100%";
                          } 

                        

                          echo '<script language="JavaScript">';
                          echo '$("span.filename").text("Text FIle Name -" );';
                          echo '$("div#percontent").css({"width":"'.$percent.'"});';
                          echo '$("span.status").text("Status: '.$percent.' Complete");';
                          echo '$("span.rowprocess").text("Processed Row: '.$rowproC++.' out of '.$total_files.'");';
                          echo '$("span.empname").text("Entry: ");';
                          echo '</script>';                      
                          flush();
                          ob_flush();
                          usleep(100);   


                          
                          
                     }


      echo '       </div>    






                <!-- Rest of the content... -->
                </body>
             </html>';           
    }






    function extract_file_checking()
    {
            $store_arr        = json_decode($this->input->get('store_arr'));
            $checbox_type_arr = json_decode($this->input->get('checbox_type_arr'));
            $paring     = array();
            $store_loop = '';    
            $v_code     = $_GET['v_code'];
            $error      = false;

            $calendar_date = $_GET['d_tag'];

            

            for($i=0; $i<count($_FILES['files']['name']); $i++)
            {
                 $fileName   = $_FILES['files']['tmp_name'][$i];   
                 $fileType   = pathinfo($_FILES['files']['name'][$i], PATHINFO_EXTENSION); // Get the file extension               
                 $file       = fopen($fileName,"r") or exit("Unable to open file!");
                 $RESS2      = strip_tags(fread($file, filesize($fileName)));
                 $allowTypes = array('html',"htm",'txt'); 

                  
                 if(in_array($fileType, $allowTypes))
                 {
                      $store_name = $this->Mms_mod->get_a_store($store_arr[$i]);       

                      if($checbox_type_arr[$i] == '_txt_file')
                      {                              

                            $keywords = explode('^',$store_name[0]['reorder_report_header']);
                            $exists   = false; 
                            foreach ($keywords as $keyword) 
                            {
                                if (strpos($RESS2, $keyword) !== false)
                                {
                                   $exists   = true;
                                   break; 
                                }                                
                            }

                            

                             
                             if( !strstr($RESS2,'Re-order') || $exists == false || (!strstr($RESS2,$v_code) && $v_code != 'UPLOAD OLD SALES' ) ) 
                             {
                                 $response      = 'please select a correct reorder report file in '.$store_name[0]['name'];
                                 $data['field'] = $store_arr[$i].'_txt_file';
                                 break;
                             }
                             else 
                             {
                                 $response = 'success';                        
                             }

                      }
                      else 
                      if($checbox_type_arr[$i] == '_vendor_txt_file')
                      {
                          if( !strstr($RESS2,'Item Vendor Sales') || !strstr($RESS2,$store_name[0]['name']) || !strstr($RESS2,$v_code) ) 
                         {
                             $response      = "please select a correct Item Vendor Sales Report file in ".$store_name[0]['name'];
                             $data['field'] = $store_arr[$i].'_vendor_txt_file';
                             break;
                         }
                         else 
                         {
                             $response = 'success';                                
                         }
                      }
                      else
                      if($checbox_type_arr[$i] = '_pend_po_txt_file')  
                      {
                           
                          // if(strstr($RESS2,$store_name[0]['file_extension']))
                          // {
                          //    $explode_rows = explode("\n", $RESS2);
                          //    $response = 'success';    
                          // } 
                          // else 
                          // {
                          //    $response       = 'location code '.$store_name[0]['file_extension'].' not found in the textfile you selected in '.$store_name[0]['display_name'].' Purchase Line & Purchase Header';
                          //    $data['field'] = $store_arr[$i].'_pend_po_txt_file';
                             
                          //    break;
                          // }                           
                             $response = 'success';    
                          
                      }


                 }
                 else 
                 {
                     $response = 'please select html or txt file'; 
                     break;
                 }
            }   



            $_reorder_list = array();   
            
            if($response == 'success')
            {
                 for($i=0; $i<count($_FILES['files']['name']); $i++)
                 {
                     $fileName   = $_FILES['files']['tmp_name'][$i];                      
                     $file       = fopen($fileName,"r") or exit("Unable to open file!");
                     $RESS2      = strip_tags(fread($file, filesize($fileName)));                     


                     if(strstr($RESS2,'Re-order'))
                     {
                         $current_user_login =  $this->Mms_mod->get_user_connection($_SESSION['user_id']);

                         $html_exp           = explode("_________________________",$RESS2);
                         $exp_header_column  = explode("\n", $html_exp[0]);
                         $header_arr         = array_splice($exp_header_column,0, -18); // Remove the last 18 indexes  
                         

                         // Step 1: Remove &nbsp; from the string
                         $clean_date   = str_replace('&nbsp', '', $header_arr[29]);
                         // Step 2: Convert the remaining string into a date format
                         $timestamp    = strtotime($clean_date);
                         $reorder_date = date('Y-m-d', $timestamp);

                         if($current_user_login[0]['value_'] == $store_arr[$i])
                         {
                           $source = 'yes';
                         }
                         else 
                         {
                           $source = 'no';
                         }
                         
                         array_push($_reorder_list,array("source"=>$source,"reorder_date"=>$reorder_date,"store"=>$store_arr[$i]));     
                     }
                 }

                 if(!empty($_reorder_list))
                 {
                     foreach($_reorder_list as $reord)
                     {
                         if($reord['source'] == 'yes')
                         {
                              $source_date  = $reord['reorder_date'];   //mao ni ang date sa reorder nga main reorder sa nag upload .sample CDC incharge siya.so kani nga date  ani dapat ma store ang reorder date ni CDC
                              $source_store = $reord['store'];
                         }
                     }

                     foreach($_reorder_list as $ord)
                     {
                         
                         $select_diff = 'DATEDIFF("'.$source_date.'","'.$ord['reorder_date'].'") AS date_difference';
                         $table       = '';
                         $where       = '';
                         $difference  = $this->Mms_mod->select($select_diff,$table,$where);


                          if($ord['reorder_date'] == '1970-01-01')
                          {
                                 $response      = 'Please Input the valid Reorder Report in '.strtoupper($ord['store']); 
                                 $data['field'] = $ord['store'].'_txt_file';
                                 break;
                          } 
                          else 
                          if( $difference[0]['date_difference'] < -1)
                          {
                                 $response      = strtoupper($ord['store']).' Reorder Date must be equal or 1 day behind from Re-order date of '.strtoupper($source_store); 
                                 $data['field'] = $ord['store'].'_txt_file';
                                 break;
                          }
                          else 
                          if($difference[0]['date_difference'] > 1)  
                          {
                                 $response      = strtoupper($ord['store']).' Reorder Date must be equal or 1 day ahead from Re-order date of '.strtoupper($source_store); 
                                 $data['field'] = $ord['store'].'_txt_file';
                                 break;
                          }
                          else
                          {
                              $response = 'success';
                          }

                         // var_dump($response);

                         // var_dump($difference[0]['date_difference']);
                     }
                 }



            }

             // var_dump($source_date);
             // var_dump($calendar_date);

             // var_dump($_reorder_list);


             

         $data['response'] = $response;
         echo json_encode($data);

    }






     public function  extract_file_V2($store) //original controller
     {
         $memory_limit = ini_get('memory_limit');
         ini_set('memory_limit',-1);
         ini_set('max_execution_time', 0);    

         

          

          $store_arr = json_decode($this->input->get('store_arr'));
          

           $v_code     = $_GET['v_code'];
           $d_tag      = $_GET['d_tag'];
           $group_code = $_GET['group_code'];

          
            $paring   = array();
            $store_loop = '';    

            for($i=0; $i<count($_FILES['files']['name']); $i++)
            {
                 $fileName   = $_FILES['files']['tmp_name'][$i];   
                 $fileType   = pathinfo($_FILES['files']['name'][$i], PATHINFO_EXTENSION); // Get the file extension               
                 $file       = fopen($fileName,"r") or exit("Unable to open file!");
                 $RESS2      = strip_tags(fread($file, filesize($fileName)));
                 $allowTypes = array('html',"htm"); 

                   
                 if(in_array($fileType, $allowTypes))
                 {

                      $store_name = $this->Mms_mod->get_a_store($store_arr[$i]);                      
                      if(strstr($RESS2,'Re-order') && strstr($RESS2,$store_name[0]['name']) && strstr($RESS2,$v_code) )
                      {
                         $response = 'success';   
                         if(in_array($store_arr[$i],array('icm','asc')))
                         {
                             array_push($paring,array('store'=>$store_arr[$i],'report'=>'Re-order','store_name'=>$store_name[0]['name']));
                             $store_loop = 'Re-order';
                         }
                      }  
                      else                     
                      if(strstr($RESS2,'Item Vendor Sales') && strstr($RESS2,$store_name[0]['name']) && strstr($RESS2,$v_code) )
                      {
                          
                         $response = 'success';   
                         if(in_array($store_arr[$i],array('icm','asc')))
                         {
                             array_push($paring,array('store'=>$store_arr[$i],'report'=>'Item Vendor Sales','store_name'=>$store_name[0]['name']));
                             $store_loop = 'Item Vendor Sales';
                         }
                      }
                      else 
                      if(strstr($RESS2,'Item Vendor Sales') && !strstr($RESS2,$store_name[0]['name']))                                            
                      {
                         $response      = "please select a correct Item Vendor Sales Report file in ".$store_name[0]['name'];
                         $data['field'] = $store_arr[$i].'_vendor_txt_file';

                         if(in_array($store_arr[$i],array('icm','asc')))
                         {                             
                              if($store_loop == 'Item Vendor Sales' || $store_loop == '')
                              {
                                  $response      = 'please select a correct reorder report file in '.$store_name[0]['name'];
                                  $data['field'] = $store_arr[$i].'_txt_file';

                              }
                              // else 
                              // if($store_loop == '')
                              // {

                              // }
                         }  
                         else 
                         if(in_array($store_arr[$i],array('cdc')))                            
                         {
                              $response      = 'please select a correct reorder report file in '.$store_name[0]['name'];
                              $data['field'] = $store_arr[$i].'_txt_file';
                         }

                         break;
                      } 
                      else 
                      if(strstr($RESS2,'Re-order') && !strstr($RESS2,$store_name[0]['name']))                                            
                      {
                         $response      = 'please select a correct reorder report file in '.$store_name[0]['name'];
                         $data['field'] = $store_arr[$i].'_txt_file';
                         if(in_array($store_arr[$i],array('icm','asc')))
                         {
                             if($store_loop == 'Re-order')
                             {
                                  $response      = "please select a correct Item Vendor Sales Report file in ".$store_name[0]['name'];
                                  $data['field'] = $store_arr[$i].'_vendor_txt_file';

                             }
                         }  

                         break;                        
                      }

                 }
                 else 
                 {
                     $response = 'please select html file'; 
                     break;
                 }
            }


            




            if(!empty($paring))
            {
                 //($paring);
                 $store = '';
                 $report = '';
                 foreach($paring as $par)
                 {
                    if($store=='')
                    {
                         $store  = $par['store'];
                         $report = $par['report']; 
                         if($report == 'Item Vendor Sales')
                         {  
                            $response      = 'please select a correct reorder report file in '.$par['store_name'];
                            $data['field'] = $par['store'].'_txt_file';                            
                            break;
                         }                          
                    }
                    else 
                    if($store == $par['store'] && $report == $par['report'])    
                    {
                         $response = "please select a correct Item Vendor Sales Report file in ".$par['store_name'];
                         $data['field'] = $par['store'].'_vendor_txt_file';                          
                         break;     
                    }
                    else 
                    {   
                        $store  = '';
                        $report = '';
                    }
                 }
            }


            

            if($response == 'success')
            {               
                 $current_user_login =  $this->Mms_mod->get_user_connection($_SESSION['user_id']);


                 $insert_batch['user_id']      = $_SESSION['user_id'];    
                 $insert_batch['store_id']     = $current_user_login[0]['store_id'];      
                 $insert_batch['status']       = 'pending';  
                 $insert_batch['date_tag']     = $d_tag;
                 $insert_batch['group_code_']  = $group_code;
                 $insert_batch['date_generated'] = date('Y-m-d H:i:s');
                 $table                        = 'reorder_report_data_batch';
                 $reorder_batch = $this->Mms_mod->insert_table($table,$insert_batch);  


                 for($i=0; $i<count($_FILES['files']['name']); $i++)
                 {
                      $RESS2 = ''; 

                      if(!empty($_FILES['files']['tmp_name'])) 
                      {
                             $fileName = $_FILES['files']['tmp_name'][$i];                
                             $file     = fopen($fileName,"r") or exit("Unable to open file!");
                             $RESS2    = strip_tags(fread($file, filesize($fileName)));
                             fclose($file);

                             if(strstr($RESS2,'Re-order'))
                             {
                                $reorder_number =  $this->extract_reorder($RESS2,$store_arr[$i],$reorder_batch);
                             }
                             else 
                             if(strstr($RESS2,'Item Vendor Sales') )
                             {
                                 $this->extract_vendor($RESS2,$store_arr[$i],$reorder_number,$reorder_batch);                                 
                             }   
                             
                             $response = 'success';                    
                      }             
                 }


                 $this->get_sql_textfile_data($reorder_batch);

          
            }



         ini_set('memory_limit',$memory_limit );
         $data['response'] = $response;
         echo json_encode($data);
     }







     function get_sql_textfile_data($reorder_batch)
     {
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


//*******************************************************pagkuha sa mga PO nga gikan sa SQL*********************************************************************************** 
         $final_po_list = array();
         $sql_po_list   = $this->Mms_mod->get_sql_po(trim($head["supplier_code"]),$past_3_month_years[2],$past_3_month_years[0],'',$head['databse_id']);
         
         if(!empty($sql_po_list))
         {        
             foreach($sql_po_list as $sql)
             {            
                  array_push($final_po_list,array("vendor"=>$sql['vendor'],'document_no'=>$sql['document_no'],'date'=>$sql['date'],'pending_qty'=>$sql['pending_qty'],'item_code'=>$sql['item_code'],'uom'=>$sql['uom']));                                     
                
             }
         }
//********************************************************************************************************************************************************************************* 


// ----------------------------------------------pagkuha sa mga PO nga gikan sa textile --------------------------------------------------------------------------------------------
         $partial_po_list = array();
         $po_arr          = $this->Mms_mod->get_pending_po($store,$past_3_month_years,'');   
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

                      $this->insert_reorder_report_data_po($document_no,$item_code,$details["all_ave_sales"],$details["quantity_on_hand"],$pending_qty,$reorder_batch,$date,$uom);                              
                 }
             }

            
         }





     }


     function insert_reorder_report_data_po($document_no,$item_code,$all_store_ave_sales,$all_store_qty_on_hand,$pending_qty,$reorder_batch,$date,$uom)
     {
             $insert_data['document_no']           = $document_no; 
             $insert_data['item_code']             = $item_code; 
             $insert_data['all_store_ave_sales']   = $all_store_ave_sales; 
             $insert_data['all_store_qty_on_hand'] = $all_store_qty_on_hand; 
             $insert_data['pending_qty']           = $pending_qty; 
             $insert_data['reorder_batch']         = $reorder_batch; 
             $insert_data['po_date']               = $date;
             $insert_data['uom']                   = $uom;

             $table                                = 'reorder_report_data_po';
             $this->Mms_mod->insert_table($table,$insert_data);
     }




     // function insert_batch()
     // {
     //         $insert_batch['user_id']  = $_SESSION['user_id'];            
     //         $insert_batch['status']   = 'pending';  
     //         $table                    = 'reorder_report_data_batch';
     //         $reorder_batch = $this->Mms_mod->insert_table($table,$insert_batch);  

     //         $data['reorder_batch'] = $reorder_batch;

     //         echo json_encode($data);
     // }



     

     public function extract_file($store)
     {
          $this->check_for_truncate_reorder();
          $this->check_for_truncate_vendor_sales();   



          $memory_limit = ini_get('memory_limit');
          ini_set('memory_limit',-1);
          ini_set('max_execution_time', 0);
          if(count($_FILES['files']['name']) < 3)
          {
              for($i=0; $i<count($_FILES['files']['name']); $i++)
              {
                    $RESS2 = ''; 
                    if(!empty($_FILES["files"]["name"]))
                    {   
                          $PDFfileName = basename($_FILES["files"]["name"][$i]); 
                          $PDFfileType = pathinfo($PDFfileName, PATHINFO_EXTENSION); 
                          $allowTypes  = array('html',"htm"); 
                          
                           
                          if(in_array($PDFfileType, $allowTypes))
                          {                    
                               if(!empty($_FILES['files']['tmp_name'])):
                    
                                 $fileName = $_FILES['files']['tmp_name'][$i];                
                                 $file     = fopen($fileName,"r") or exit("Unable to open file!");
                                 while(!feof($file)) 
                                 {
                                     @$RESS2 .= fgets($file). "";
                                 }
                               endif; 
                         
            
                               if(strstr($RESS2,'REORDER REPORT - ACTUAL QTY'))
                               {                                 
                                     $response = $this->extract_REORDER_REPORT($RESS2);                                                     
                               }
                               else 
                               if(strstr($RESS2,'Item Vendor Sales Report'))
                               {                    
                                     $response = $this->extract_Item_Vendor_Sales_Report($RESS2);                                                           
                               }
                               else
                               {
                                     $response = 'no valid report found'; 
                               }
                          }
                          else
                          { 
                                 $response = 'only html file is allowed to upload.'; 
                          } 
                    }
              }

              $check_item_vendor_sales         = $this->Mms_mod->check_item_vendor_sales_report_filter('');      
              $check_reorder_report_data_table = $this->Mms_mod->check_reorder_report('');
              
              if(!empty($check_reorder_report_data_table)) //if wala gi apil ug select ang file nga REORDER REPORT
              {            
                 if(empty($check_item_vendor_sales)) //if walay entry si user ani nga table  item_vendor_sales_report_filter
                 {
                     $response           = 'missing  ITEM VENDOR SALES REPORT.  please upload the ITEM VENDOR SALES REPORT together with the REORDER REPORT'; 
                     $this->check_for_truncate_reorder();                     
                 }      
                 else 
                 {

                     $store_reorder_arr = array();
                     $store_vendor_arr  = array();
                     foreach($check_reorder_report_data_table as $reorder)   
                     {
                         if($reorder['value_type'] == 'store')
                         {
                             $store_reorder = explode('-',$reorder['value_name']);
                             array_push($store_reorder_arr,$store_reorder[0]);
                         }  
                     }

                     foreach($check_item_vendor_sales as $vendor)
                     {
                         $store_vendor = explode('-',$vendor['store']);
                         array_push($store_vendor_arr,$store_vendor[0]);
                     }


                    $difference = array_diff($store_reorder_arr,$store_vendor_arr);

                    if (!empty($difference)) 
                    {
                         $response = 'REORDER REPORT and ITEM VENDOR SALES REPORT does not match';
                         $this->check_for_truncate_reorder();
                         $this->check_for_truncate_vendor_sales();
                    }
                    else 
                    {
                         $response = 'success'; 
                         $insert_count = $this->consolidate_report();
                         // if($insert_count>0){
                            $this->Mms_mod->truncate("reorder_report_data");
                            $this->Mms_mod->truncate("item_vendor_sales_report_entry");
                            $this->Mms_mod->truncate("item_vendor_sales_report_filter");
                         // }

                    }
                     
                 }         
                         

              }
              else //if wala gi apil ug select ang ITEM VENDOR SALES REPORT
              {
                   $response                = 'missing  REORDER REPORT.  please upload the REORDER REPORT together with the ITEM VENDOR SALES REPORT';               
                   if(!empty($check_item_vendor_sales))
                   {
                         $this->check_for_truncate_vendor_sales();                         
                   }
              }
          }
          else 
          {
             $response = 'Please Select 2 files only';             
          }  

          ini_set('memory_limit',$memory_limit );
          $data['response'] = $response;
           echo json_encode($data);
     }

/* Stephanie and Sir Gershom Code ---------------------------------------------------------------------------*/

     function consolidate_report()
     {  
        $insert_array = array();
        $header = array();
        $months = array();
        $column = array();
        $h_count = 1;
        $m_count = 1;
        $reorder_data = $this->Mms_mod->get_reorder_report_data_all(); //code ni ni sir ryan daan

        $header["user_id"] = $_SESSION['user_id'];
        $header["reorder_date"] = date("Y-m-d");

        foreach($reorder_data as $reorder){
            $value_name = $reorder["value_name"];
            $value_type = $reorder["value_type"];

            if ($value_type == "supplier_code_number") {
                if($h_count < 4){
                    $header["supplier_code"] = $value_name;
                }
            }else if ($value_type == "supplier_name") {
                if($h_count < 4){
                    $header["supplier_name"] = $value_name;
                }
            }else if ($value_type == "lead_time_factor") {
                if($h_count < 4){
                    $header["lead_time_factor"] = round(trim($value_name),0);
                    $h_count++;
                }
            }else if($value_type == "item_code"){
                $column["item_code"] = $value_name;
                $column["qty"] = $this->Mms_mod->get_qty_item_vendor_by_code($value_name);  
            }
            else if ($value_type == "Description") {
                $column["Item_description"] = $value_name;
            }
            else if ($value_type == "Unit_of_measure") {
                $column["uom"] = $value_name;
            }
            else if ($value_type == "Pending-month") {
                if($m_count < 4){
                    $months["month_".$m_count] = $value_name;
                    $m_count++;
                }
            }
            else if ($value_type == "PREV_qty-1") {
                $column["month_sales_1"] = round($value_name,2);
            }
            else if ($value_type == "PREV_qty-2") {
                $column["month_sales_2"] = round($value_name,2);
            }
            else if ($value_type == "PREV_qty-3") {
                $column["month_sales_3"] = round($value_name,2);
            }
            else if ($value_type == "ave_sales") {
                $column["daily_ave_sales"] = round($value_name,2);
            }
            else if ($value_type == "max_level") {
                $column["maximum_level"] = round($value_name,2);
            }
            else if ($value_type == "qty_on_hand") {
                $column["qty_onhand"] = round($value_name,2);
                $insert_array[] = $column;
                $column = array();
            }
        }
        
        $insert_array[] = $months;
        //echo json_encode($insert_array);
        $r_no = $this->Mms_mod->insert_reorder_report_data_header_final($header);        

        $limit = count($insert_array)-1;
        $insert_batch_data = array();
        for($c=0; $c<$limit; $c++){
            $insert_data["reorder_number"] = $r_no;
            $insert_data["item_code"] = $insert_array[$c]["item_code"];
            $insert_data["Item_description"] = $insert_array[$c]["Item_description"];
            $insert_data["month_1"] = $insert_array[$limit]["month_1"];
            $insert_data["month_sales_1"] = $insert_array[$c]["month_sales_1"];
            $insert_data["month_2"] = $insert_array[$limit]["month_2"];
            $insert_data["month_sales_2"] = $insert_array[$c]["month_sales_2"];
            $insert_data["month_3"] = $insert_array[$limit]["month_3"];
            $insert_data["month_sales_3"] = $insert_array[$c]["month_sales_3"];
            $insert_data["total_sales"] = $insert_array[$c]["month_sales_1"]+$insert_array[$c]["month_sales_2"]+$insert_array[$c]["month_sales_3"]; // Sum of Monthly Sales 1-3
            $insert_data["daily_ave_sales"] = $insert_array[$c]["daily_ave_sales"];
            $insert_data["maximum_level"] = $insert_array[$c]["maximum_level"];

            $qty = $insert_array[$c]["qty"];
            if($qty<1){
                $insert_data["quantity_on_hand"] = $insert_array[$c]["qty_onhand"];
            }else{
                preg_match_all('/\d+\.?\d*/', $insert_array[$c]["uom"], $matches);
                $qty_val = $matches[0]; // Array
                if(count($qty_val)<1){
                    $pc = 6; // Assumed pieces
                }else{
                    $pc = round($qty_val[0],0);
                }

                $A = $qty/$pc;
                $insert_data["quantity_on_hand"] = $insert_array[$c]["qty_onhand"]-$A;

            }

            $insert_data["reorder_quantity"] = $insert_data["maximum_level"]-$insert_data["quantity_on_hand"];
            // Unfinished!!

            $insert_batch_data[] = $insert_data;

        }

        $this->Mms_mod->insert_reorder_report_data_lines_final($insert_batch_data);


    }

    // Formulas
    // A = qty/case 
    // f_qty_onhand = qty_onhand - A

    // Extract number
    // preg_match_all('/\d+\.?\d*/', $string, $matches);
    // print_r($matches[0]);

/* End of the Code -----------------------------------------------------------------------------------------*/

     function check_for_truncate_reorder()
     {
          $check_for_truncate = $this->Mms_mod->check_reorder_report('truncate');
          if(empty($check_for_truncate)) //if siya ra nga user naa sa mpdi.reorder_report_data nga table .i truncate siya
          {
             $this->Mms_mod->truncate('mpdi.reorder_report_data');
          }
          else  //if naay laeng user aside niya sa table nga mpdi.reorder_report_data .. idelete ra ang entry nga iyaha ra kay basin nag generate pud ang laeng user
          {                     
             
             foreach($check_reorder_report_data_table as $reorder)
             {
                 $reorder_id_arr = array();
                 array_push($reorder_id_arr,$reorder['reorder_id']);
                 $this->Mms_mod->delete_entry('mpdi.reorder_report_data',$reorder_id_arr,'reorder_id');   
             }
          }
     }


     function check_for_truncate_vendor_sales()
     {
         $check_for_truncate =  $this->Mms_mod->check_item_vendor_sales_report_filter('truncate');
         if(empty($check_for_truncate)) //if siya ra nga user naa sa item_vendor_sales_report_filter  nga table..i truncate ang table nga  item_vendor_sales_report_filter ug  item_vendor_sales_report_entry
         {
             $this->Mms_mod->truncate('item_vendor_sales_report_filter');
             $this->Mms_mod->truncate('item_vendor_sales_report_entry');
         }
         else  //if naay laeng user aside niya sa table nga item_vendor_sales_report_filter .. idelete ra ang entry nga iyaha ra kay basin nag generate pud ang laeng user
         {                         
             $filter_id_arr = array();
             foreach($check_item_vendor_sales as $filter)
             {
                 array_push($filter_id_arr,$filter['filter_id']);
             }
             $this->Mms_mod->delete_entry('mpdi.item_vendor_sales_report_filter',$filter_id_arr,'filter_id');
            
             $vendor_sales_data =  $this->Mms_mod->get_item_vendor_sales_report_entry();
             $vendor_sales_arr  =  array();
             foreach($vendor_sales_data as $vendor)
             {
                array_push($vendor_sales_arr,$vendor['entry_id']);
             }
             $this->Mms_mod->delete_entry('mpdi.item_vendor_sales_report_entry',$vendor_sales_arr,'entry_id');
         }
     }





     function extract_REORDER_REPORT($RESS2)
     {
         $response           = '';
         $kuhag_double_qoute = preg_replace('/"/', "", $RESS2);    
         $a_cells            = array_slice(preg_split('/(?:<\/td>\s*|)<td[^>]*>/iu', $kuhag_double_qoute), 1);
         $key                = array_search("<B><FONT SIZE=1 FACE=Helvetica>SUPPLIER CODE:<BR></FONT></B>",$a_cells); //get the index of this specific string
         $supplier_code      = strip_tags($a_cells[$key+2]); //get the supplier code
         $store              = strip_tags($a_cells[6]); 
         $response           = 'success';                           
         $value_type         = '';
         $previous           = 0; 
         $line_counter       = 0;
         $line_number        = 0;
         $status             = '';

         for ($a=0; $a <count($a_cells) ; $a++) 
         { 
            $strip      =  strip_tags($a_cells[$a]);                                  

            if(!empty($strip) && ctype_space($strip) == false)
            {                                                        
               $check_dta  = $this->Mms_mod->check_reorder_report_data($supplier_code,$strip,$value_type,$a,$store);
               if(empty($check_dta))
               {               
                    $value_return = $this->value_type_checker($value_type,$strip,$previous);    
                    $value_type   = $value_return[0];              
                    $strip        = $value_return[1];  
                    $previous     = $value_return[2];              
                    if($value_type == 'item_code')    
                    {
                        $status        = 'start count';
                        $line_counter += 1; 
                        $line_number   = $line_counter;
                    } 
                    else 
                    if($status == 'start count')    
                    {
                        $line_number = $line_counter;
                    }
                    //ani nihunong 

                    $this->Mms_mod->insert_reorder_report_data($supplier_code,$strip,$value_type,$a,$store,$line_number);                        
               }
               else 
               {
                 $response = "some data are already uploaded";
               }
            }
         } 

         return $response;
     }


     function extract_Item_Vendor_Sales_Report($htmlfile)
     {
        $DOM            = new DOMDocument();
        $internalErrors = libxml_use_internal_errors(true);
        $DOM->loadHTML($htmlfile);
        libxml_use_internal_errors($internalErrors);
        $validate       = true;
        $items          = $DOM->getElementsByTagName('font');
        $details        = array();
        $item_vendor_no = '';

        if($items->length<20)
        {            
             $validate = false;
        }    
        else
        { // With length start 
          //For Headers
             for($i = 0; $i<15; $i++)
             {

                 switch ($i)
                 {
/*rian coode ---------------------------------------------------------------------------*/                     
                     case 0:
                              $details["store"] = $items->item($i)->nodeValue;  
                               break;                           
/*end of rian coode -------------------------------------------------------------------*/                     
                     case 8:
                             if(preg_match("/^[S][0-9]{4}$/", $items->item($i)->nodeValue))
                             {
                                 $details["item_vendor_no"] = $items->item($i)->nodeValue;
                             }
                             else
                             {
                                 $validate = false;
                                 break;
                             }
                     case 11:
                             $details["date_filter"] = date("Y-m-d", strtotime(trim($items->item($i)->nodeValue," ")));
                             break;
                     case 13:
                           $details["store_filter"] = $items->item($i)->nodeValue;
                           break;                       
                 }
             }

            //For Footers               
            for($i = $items->length-1; $i>$items->length-4; $i--)
            {
                 $split = explode(": ",$items->item($i)->nodeValue);             
                 if($i==$items->length-1)
                 {
                     if(count($split)==1)
                     {
                        $validate = false;
                     }
                     else
                     {
                        $details["run_datetime"] = date("Y-m-d", strtotime(trim($split[1]," ")));
                     }
                 }
                 else
                 if($i==$items->length-2)
                 {
                     if(count($split)==1)
                     {
                          $validate = false;                    
                     }
                     else
                     {
                          $details["run_datetime"] .= " ".date("H:i:s", strtotime(trim($split[1]," ")));                    
                     }
                 }
                 else
                 {
                     $details["prepared_by"] = $items->item($i)->nodeValue;
                 }     
/*rian code --------------------------------------------------*/
                 $details['user_id'] = $_SESSION['user_id'];        
/*end of rian code ------------------------------------------*/
            }
               
            //For Rows
            $columns     = array("item_no","extended_description","qty","net_sale_w_vat", "discount_given");
            $identifiers = array("tdalign=Rightvalign=Middlesize=2face=Helvetica",
                                   "tdvalign=Middlesize=1face=Helvetica7",
                                   "tdwidth=97%align=Rightvalign=Middlesize=2face=Helvetica",
                                   "tdwidth=100%align=Rightvalign=Middlesize=2face=Helvetica",
                                   "tdalign=Rightvalign=Middlesize=2face=Helvetica");
            $entries = array();
            $hash    = array();
            $ind     = 0;
                  
            for ($i = 0; $i < $items->length; $i++)
            {
                     $parent = $items->item($i)->parentNode;
                     $p_attr = $parent->attributes;
                     $attr = $items->item($i)->attributes;
                     $cellVal = trim($items->item($i)->nodeValue," "); // Retrieve value from font
                     
                     $attr_val = $parent->nodeName; // td
                     for($p = 0; $p<$p_attr->length; $p++)
                     {
                        $attr_val .= $p_attr->item($p)->nodeName."=".$p_attr->item($p)->nodeValue; // Retrieve attributes from td
                     }

                     for($c = 0; $c<$attr->length; $c++)
                     {
                        $attr_val .= $attr->item($c)->nodeName."=".$attr->item($c)->nodeValue; // Retrieve attributes from font
                     }
                     
                     if($ind===5)
                     {
                        $ind = 0;
                        
                        array_push($entries,$hash);
                        $hash = array();
                     }

                     if(in_array($attr_val, $identifiers))
                     {
                        if($ind==0)
                        {
                           if(!preg_match("/^[0-9]{6}$/", $cellVal))
                              $validate = false;
                        }else if($ind==2 || $ind==3 || $ind==4)
                        {
                           $cellVal = str_replace(",","",$cellVal); // Remove commas from decimal values
                           if(!is_numeric($cellVal))
                              $validate = false;
                        }

                        $hash[$columns[$ind]] = $cellVal;
                        $ind++;  
                     }
             }
        } // With length end
        if($validate)
        {
            //($details);
            $this->upload_mod->insertItemVendorSales($details,$entries);
            return 'success';
        }
        else
        {
             return "Invalid Format Encountered!";
        }
     }


/* Stephanie and Sir Gershom Code ---------------------------------------------------------------------------*/

     function list_reorder_data_ui()
     {
        $result = $this->Mms_mod->get_reorder_report_data_all();
        echo "<table>";
        foreach($result as $row){
            echo "<tr>";
            echo "<td>".$row["value_name"]."</td>";
            echo "<td>".$row["value_type"]."</td>";
            echo "</tr>";
        }
        echo "</table>";
     }

     function view_reorder_report(){
        $r_no = $_POST["reorder_no_field"];
        $results = array();
        $results["header"] = $this->Mms_mod->get_entries_reorder_report_data_header_final($r_no);
        $results["lines"] = $this->Mms_mod->get_entries_reorder_report_data_lines_final($r_no);

        echo json_encode($results);
     }

     function getreoderfinal(){
        $result = array();
        $result = $this->Mms_mod->get_entries_reorder_report_data_lines_final();
        echo json_encode($result);

    }

   
/* End of the Code ------------------------------------------------------------------------------------------*/

    function get_all_qty_onhand()
    {
         $reorder_batch    = $_POST['reorder_batch'];
         $filter           = $_POST['filter'];
         $html             = "";
         $details          = $this->Mms_mod->get_all_average_sales($reorder_batch);
         $stores           = array();
         $quantity_on_hand = array();
         $items            = array(); 

         $store_details    =  $this->store_items($details,$stores,$quantity_on_hand,$items,'quantity',$html,$filter);

         $stores           =  $store_details[0];  
         $quantity_on_hand =  $store_details[1];  
         $items            =  $store_details[2];  
         $html             =  $store_details[3];  


         $data['html']    = $html;
         $buttons         = '<button type="button" class="btn btn-danger" data-dismiss="modal">
                                            <i class="bx bx-x d-block d-sm-none"></i>
                                            <span class="d-none d-sm-block ">CLOSE</span>
                                        </button>';            
         $data['buttons'] = $buttons;  
         echo json_encode($data);



    }




    function view_ave_sale_per_month()
    {
         $reorder_batch = $_POST['reorder_batch'];
         $filter        = $_POST['filter']; 
         $month         = $_POST['month'];
         $lines_details = $this->Mms_mod->get_ave_sales_per_month($reorder_batch);

         $first_3_columns = array();
         $store_sale_qty  = array();
         $stores          = array();
         $header          = array("Item No.","Item Description","UOM");
         $style_header    = array('text-align: center; background-color: rgb(0, 86, 137); color: white; left: 0px; position: sticky;',
                                  'text-align: center; background-color: rgb(0, 86, 137); color: white; left: 70px; position: sticky;',
                                  'text-align: center; background-color: rgb(0, 86, 137); color: white; left: 232px; position: sticky;');
         foreach($lines_details as $line)
         {
             if(!in_array($line['item_code'], array_column($first_3_columns, "item_code")) )   
             {
                 array_push($first_3_columns,array("item_code"=>$line['item_code'],"Item_description"=>$line['Item_description'],"uom"=>$line['uom']) );
             } 

             array_push($store_sale_qty,array("db_id"=>$line['db_id'],"item_code"=>$line['item_code'],"uom"=>$line['uom'],"Qty"=>$line[$month]) );



             if(!in_array($line['db_id'],array_column($stores,"db_id")) ) 
             {
                  $user_details = $this->Mms_mod->get_user_connection($line['user_id']);

                  $proceed = false;
                  if(
                       ($filter == 'consolidated_ave_sales' && $user_details[0]['value_'] == 'cdc')  || 
                       ($filter == 'all_store_ave_sales'    && $line['db_id'] != 20 && $user_details[0]['value_'] == 'cdc') ||
                       ($filter == 'consolidated_ave_sales' && $user_details[0]['value_'] != 'cdc')  || 
                       ($filter == 'all_store_ave_sales'    && $line['db_id'] == 20 && $user_details[0]['value_'] != 'cdc')

                    )
                    {
                        $proceed = true;
                    }
                  
                  if($proceed)
                  {
                     array_push($stores,array("db_id"=>$line['db_id']) );                 
                     array_push($header,$line['display_name']);
                     $style_header[] = '';
                  }

             }


             //echo $line['item_code'].'------'.$line[$month]."<br>";
         }
         array_push($header,"TOTAL AVE. SALES QTY");
         $style_header[] = '';

          $table_id = 'ave_sales_per_month_tbl';
          $html     = $this->simplify->populate_header_table($table_id,$header,$style_header);        
           
           $store_total    = array(); 
           $over_all_total = 0;
           foreach($first_3_columns as $first_3)
           {

                 $total = 0;
                 $rows  = array(
                                  $first_3['item_code'],
                                  $first_3['Item_description'],
                                  $first_3['uom']
                              );
                 $style = array(
                                  "text-align:center;",
                                  "text-align:center;",
                                  "text-align:center;"
                               );
                 foreach($stores as $str)
                 {
                      $naay_item = 'wala';
                      foreach($store_sale_qty as $str_qty)
                      {
                          if($str_qty['db_id'] == $str['db_id'] && $str_qty['item_code'] == $first_3['item_code'] &&  $str_qty['uom'] == $first_3['uom'])
                          {
                             array_push($rows,$str_qty['Qty']);
                             array_push($style,"text-align:right;");
                             $total          += $str_qty['Qty'];
                             $over_all_total += $str_qty['Qty'];
                             array_push($store_total,array("db_id"=>$str['db_id'],"qty"=>$str_qty['Qty']) );
                             $naay_item = 'naa';
                          }
                      }

                      if($naay_item == 'wala')
                      {
                             array_push($rows,'0.00');
                             array_push($style,"text-align:right;");
                      }
                 }

                 array_push($rows,$total);
                 array_push($style,"text-align:right;");

                 $tr_class = '';
                 $html .= $this->simplify->populate_table_rows($rows,$style,$tr_class);
           }



          $html    .= '   </tbody>
                         <tfoot>
                            <tr style="color:white;">
                              <th style="text-align: center; background-color: rgb(0, 86, 137); color: white; left: 0px; position: sticky;"></th>
                              <th style="text-align: center; background-color: rgb(0, 86, 137); color: white; left: 71px; position: sticky;"></th>
                              <th style="text-align: center; background-color: rgb(0, 86, 137); color: white; left: 238px; position: sticky;">TOTAL AVE. SALES QTY</th>';
                         
                               foreach($stores as $str)
                               {
                                  $total = 0;  
                                  foreach($store_total as $tot)
                                  {
                                     if($str['db_id'] ==  $tot['db_id'])
                                     {
                                         $total += $tot['qty'];
                                     }
                                  }

                                  $html .= '<th style="text-align:right;">'.number_format($total,2).'</th>';
                               } 

          $html    .= '       <th style="text-align: right; background-color: rgb(0, 86, 137); color: white; position: sticky; right: 0px;">'.number_format($over_all_total,2).'</th>                              
                            </tr>
                        </tfoot>
                     </table>
                     <script>
                        $("#'.$table_id.'").DataTable({ 
                            
                                                                    fixedColumns: {
                                                                            left: 3,
                                                                            right: 1
                                                                        },
                                                                        scrollCollapse: true,
                                                                        scrollY: 250,
                                                                        scrollX: true,
                                                                        createdRow: function(row, data, dataIndex) 
                                                                        {

                                                                            var lastColumnIndex = data.length - 1; // Index of the last column
                                                                            var lastColumnCell = $(row).find("td:eq(" + lastColumnIndex + ")");
                                                                            var lastColumnHeader = $("th:eq(" + lastColumnIndex + ")", $("#'.$table_id.'").find("thead"));
                                                                            var lastColumnFooter = $("td:eq(" + lastColumnIndex + ")", $("#'.$table_id.'").find("tfoot"));
                                                                            
                                                                            $(lastColumnCell).css({
                                                                                "background-color": "rgb(0, 86, 137)",
                                                                                "color": "white"
                                                                            });     

                                                                            $(row).find("td:eq(0), th:eq(0), td:eq(1), th:eq(1), td:eq(2), th:eq(2)").css({
                                                                                "background-color": "rgb(0, 86, 137)",
                                                                                "color": "white"
                                                                            });


                                                                             $(lastColumnHeader).css({
                                                                                    "background-color": "rgb(0, 86, 137)",
                                                                                    "color": "white"
                                                                             });





                                                                        }, 
                                                                        dom: "Blfrtip", // Add the "B" for Buttons
                                                                        buttons: [
                                                                            {
                                                                                extend: "copy",
                                                                                title: "ALL STORE AVERAGE SALES QUANTITY",
                                                                                footer:true

                                                                            },
                                                                            {
                                                                                extend: "csv",
                                                                                title: "ALL STORE AVERAGE SALES QUANTITY",
                                                                                footer:true
                                                                                
                                                                            },
                                                                            {
                                                                                extend: "excel",
                                                                                title: "ALL STORE AVERAGE SALES QUANTITY",
                                                                                footer:true
                                                                            },
                                                                            {
                                                                                extend: "pdf",
                                                                                title: "ALL STORE AVERAGE SALES QUANTITY",
                                                                                // exportOptions:  //mao ni gamiton kung mag specify ka ug unsa ra nga column imung ipa generate
                                                                                // {
                                                                                //         columns: [0,1,2] // Specify the columns you want to export (0-based index)
                                                                                // }
                                                                                footer:true

                                                                             }
                                                                             // ,
                                                                            // {
                                                                            //     extend:"print",
                                                                            //     title:"ALL STORE AVERAGE SALES QUANTITY"
                                                                            // }
                                                                        ]
                                                      });
                     </script>
                     ';


          $buttons          = '<button type="button" class="btn btn-danger" data-dismiss="modal">
                                            <i class="bx bx-x d-block d-sm-none"></i>
                                            <span class="d-none d-sm-block ">CLOSE</span>
                                        </button>';            
          $data['buttons'] = $buttons;             


          $data['html'] = $html;
          echo json_encode($data);           
    }




    function store_items($details,$stores,$datas,$items,$purpose,$html,$filter)
    {

          

        // $html.= ' 
        //                 <table border="1">
        //                     <tr>
        //                         <th rowspan="2">Item No.</th>
        //                         <th rowspan="2">Item Description</th>
        //                         <th rowspan="2">UOM</th>
        //                         <th colspan="3">ICM</th>
        //                         <th colspan="3">Plaza Marcela</th>
        //                         <th colspan="3">CENTRAL DC</th>
        //                     </tr>
        //                     <tr>
        //                         <th>Jan</th>
        //                         <th>Feb</th>
        //                         <th>Mar</th>
        //                         <th>Jan</th>
        //                         <th>Feb</th>
        //                         <th>Mar</th>
        //                         <th>Jan</th>
        //                         <th>Feb</th>
        //                         <th>Mar</th>
        //                     </tr>
        //                     <tr>
        //                         <td>1</td>
        //                         <td>Product A</td>
        //                         <td>Each</td>
        //                         <td>100</td>
        //                         <td>150</td>
        //                         <td>200</td>
        //                         <td>120</td>
        //                         <td>180</td>
        //                         <td>240</td>
        //                         <td>80</td>
        //                         <td>120</td>
        //                         <td>160</td>
        //                     </tr>
        //                     <!-- Add more rows as needed -->
        //                 </table>
        //            ';

         
        $store_db_id    = array();
        foreach($details as $det)
        {           

            $month_1        = $det['month_1'];
            $month_2        = $det['month_2'];
            $month_3        = $det['month_3'];
            $reorder_batch  = $det['reorder_batch'];
            if(!in_array($det['display_name'], $stores))
            {    
                 $user_details = $this->Mms_mod->get_user_connection($det['user_id']);


                 $proceed = false; 
                 if(
                     ($purpose == 'average_sales' && $filter=='all_store_ave_sales' && $det['db_id'] != 20  && $user_details[0]['value_'] == 'cdc') ||
                     ($purpose == 'quantity' && $filter == 'all_store_qty_onhand'   && $det['db_id'] != 20  && $det['bu_type'] != 'NON STORE'  && $user_details[0]['value_'] == 'cdc') ||
                     ($purpose == 'average_sales' && $filter=='all_store_ave_sales' && $det['db_id'] == 20  && $user_details[0]['value_'] != 'cdc') ||
                     ($purpose == 'quantity' && $filter == 'all_store_qty_onhand'   && $det['db_id'] == 20  && $det['bu_type'] != 'NON STORE'  && $user_details[0]['value_'] != 'cdc')
                   )                    
                   {
                         $proceed = true; 
                   }
                 else 
                 if(
                       ($purpose == 'average_sales' && $filter=='consolidated_ave_sales') || 
                       ($purpose == 'quantity'      && $det['bu_type'] != 'NON STORE' && $filter=='consolidated_qty_onhand') 
                   )
                   {
                       $proceed = true;
                   }
                  

                 if($proceed)
                 {                    
                     array_push($stores,$det['display_name']);
                     array_push($store_db_id,array("db_id"=>$det['db_id']));
                 } 

            }



            if($purpose == 'quantity')
            {
                 array_push($datas,array("db_id"=>$det['db_id'],"month_sales_1"=>$det['month_sales_1'],"month_sales_2"=>$det['month_sales_2'],"month_sales_3"=>$det['month_sales_3'],"item_code"=>$det['item_code'],"uom"=>$det['uom'],"ave_sales"=>$det['ave_sales'],"quantity_on_hand"=>$det['quantity_on_hand'],"store"=>$det['display_name'],"reorder_number"=>$det['reorder_number'],"reorder_batch"=>$det['reorder_batch'],"uom"=>$det['uom']));
            }
            else 
            {
                 array_push($datas,array("db_id"=>$det['db_id'],"month_sales_1"=>$det['month_sales_1'],"month_sales_2"=>$det['month_sales_2'],"month_sales_3"=>$det['month_sales_3'],"item_code"=>$det['item_code'],"uom"=>$det['uom'],"ave_sales"=>$det['ave_sales'],"store"=>$det['display_name']));
            }



            // // Loop through the $items array and check if the target name exists
             $exists = false;

            if(count($items)>0)
            {

                foreach ($items as $it)
                {
                    if ($it['item_code'] === $det['item_code']) 
                    {
                        $exists = true;
                        break;
                    }
                }

                if (!$exists) 
                {
                    array_push($items,array("db_id"=>$det['db_id'],"item_code"=>$det['item_code'],"uom"=>$det['uom'],"Item_description"=>$det['Item_description']) );            
                }  
            }
            else 
            {
                    array_push($items,array("db_id"=>$det['db_id'],"item_code"=>$det['item_code'],"uom"=>$det['uom'],"Item_description"=>$det['Item_description']) );                            
            }         



        }

         



         //($stores,$quantity_on_hand,$items);
         $table_id       = 'details_modal';
         $header         = array("Item No.","Item Description","UOM");
         $style_header   = array(
                                     "text-align: center; background-color: rgb(0, 86, 137); color: white; left: 0px; position: sticky;", 
                                     "text-align: center; background-color: rgb(0, 86, 137); color: white; left: 64px; position: sticky;",
                                     "text-align: center; background-color: rgb(0, 86, 137); color: white; left: 167px; position: sticky;" 
                                );

         for($hd=0;$hd<count($stores);$hd++)
         {
             array_push($style_header,'');
         }



         // Get the index of the last element in the $header array
         $lastIndex = count($header) - 1;


         if($purpose == 'average_sales')
         {
            // $html  .=  '<table id="'.$table_id.'" class="table  table-hover table-bordered dataTable no-footer" style="background-color: rgb(5, 68, 104);">  //gamita ni if mag rowspan ka
            //                 <thead style="text-align: center;color:white;">
            //                     <tr>
            //                        <th rowspan="2">Item No.</th>
            //                        <th rowspan="2">Item Description</th>
            //                        <th rowspan="2">UOM</th>';  

            //                        for($a=0;$a<count($stores);$a++)
            //                        {
            //                              $html .= '<th colspan="4">'.$stores[$a].'</th>';
            //                        }
            // $html .=           '</tr>
            //                     <tr>';      

            //                        for($a=0;$a<count($stores);$a++)
            //                        {
            //                             $html .= '
                                                   
            //                                            <th>'.$month_1.'</th>
            //                                            <th>'.$month_2.'</th>
            //                                            <th>'.$month_3.'</th>
            //                                            <th style="background-color: rgb(2 126 197);"> ave. sales qty</th>
            //                                        ';
            //                        }


            //  $html  .=  '       </tr>
            //                   </thead>
            //              <tbody>    
            //             ';

            // Merge the $store array into the $header array after the last index
             $header = array_merge($header, $stores);             
         }
         else 
         {
             // Merge the $store array into the $header array after the last index
             $header = array_merge($header, $stores);             
         }


         array_push($header,'TOTAL QTY.');
         $style_header[] = '';

         $html .= $this->simplify->populate_header_table($table_id,$header,$style_header); 

         $tab = '';

         if($purpose == 'average_sales')
         {

             $tab .= '<style>
                            .nav li.active 
                             {
                                 background-color: #ccc; /* Change the color here to the desired highlight color */
                             }
                       </style>';


             $tab.= '<ul class="nav nav-tabs" style="margin-bottom: 30px;">                  
                         <li id="cdc_link" class="active" ><a style="cursor:pointer;" href="#" onclick="view_all_stores_involve('."'".$reorder_batch."','".$filter."'".')"><b>HOME</b></a></li> 
                         <li id="cdc_link" class="" ><a style="cursor:pointer;" href="#" onclick="view_ave_sale_per_month('."'".$reorder_batch."','month_sales_1','".$filter."'".')"><b>'.$month_1.'</b></a></li> 
                         <li id="cdc_link" class="" ><a style="cursor:pointer;" href="#" onclick="view_ave_sale_per_month('."'".$reorder_batch."','month_sales_2','".$filter."'".')"><b>'.$month_2.'</b></a></li> 
                         <li id="cdc_link" class="" ><a style="cursor:pointer;" href="#" onclick="view_ave_sale_per_month('."'".$reorder_batch."','month_sales_3','".$filter."'".')"><b>'.$month_3.'</b></a></li>';
                  
                            
             $tab .=  '  
                      </ul>

                      <script>
                      $(document).ready(function() 
                      {
                            // Add click event listener to each "li" element
                            $(".nav li").click(function() {
                                // Remove the "active" class from all "li" elements
                                $(".nav li").removeClass("active");

                                // Add the "active" class to the clicked "li" element
                                $(this).addClass("active");
                            });
                      });
                     </script>';


         }
         






           $store_qty = array();


           $over_all_total = 0;  
           foreach ($items as $it)
           {
               

               $rows = array(                            
                               $it['item_code'],
                               $it['Item_description'],      
                               $it['uom']
                            );
               $style = array(
                                'text-align:center;',
                                'text-align:center;',
                                'text-align:center;'                             
                             );




               $row_total = 0;
               for($a=0;$a<count($stores);$a++)
               {
                    $temp_qty = 'none';
                    foreach($datas as $dt)
                    {                         
                         if($dt['item_code'] == $it['item_code'] && $stores[$a] == $dt['store'])
                         {                             

                              
                             if($purpose == 'quantity')
                             {
                                 $total_qty = 0;

                                 if(in_array(strtoupper($dt['store']),array('ASC MALL','ICM')))
                                 {
                                    
                                       
                                         //if(!empty(trim($it['item_code']))) //ayaw idungan ang trim ug empty kay mabuang ang server hahhaha
                                         if(trim($it['item_code']) != '')
                                         {                                        
                                              $get_quantity = $this->Mms_mod->get_reorder_report_data_item_vendor($dt['reorder_number'],$dt['reorder_batch'],$it['item_code']);
                                              foreach($get_quantity as $qty)
                                              {
                                                   //echo "quantity_on_hand:".$lines['quantity_on_hand']."--->"."quantity:".$qty['quantity']."<br>";
                                                  $uom_data = $this->Mms_mod->get_nav_uom_header($it['db_id'],$dt['quantity_on_hand'],$qty['quantity'],$it['item_code'],$dt['uom']);                                             
                                                  //($uom_data);
                                                  foreach($uom_data as $uom)
                                                  {
                                                       $total_qty = $uom['store_qty'];
                                                  }  
                                              }  

                                              if($total_qty == 0)
                                              {
                                                  $total_qty = $dt['quantity_on_hand'];
                                              }

                                         }
                                 }
                                 else 
                                 {
                                     //echo "cdc".$lines['quantity_on_hand']."<br>";
                                     $total_qty = $dt['quantity_on_hand'];
                                 }

                                 $total_qty      = str_replace('-','',$total_qty);

                                 $row_total      += round($total_qty,2); 
                                 $over_all_total += round($total_qty,2);                                                                      
                                 array_push($store_qty,array("db_id"=>$dt['db_id'],"qty"=>round($total_qty,2)) );
                                 $temp_qty = $total_qty;
                               
                                 if($total_qty == '') //if walay sulod
                                 {
                                    $total_qty = 0.00;
                                 }

                                 array_push($rows,number_format($total_qty,2));
                                 array_push($style, 'text-align:right;'); 

                                 // array_push($rows,$dt['quantity_on_hand']);
                             }
                             else 
                             if($purpose == 'average_sales')
                             {

                                  //$partial_total   = ($dt['month_sales_1'] + $dt['month_sales_2'] + $dt['month_sales_3']);
                                  $partial_total   = $dt['ave_sales'];
                                  $row_total      += round($partial_total,2);
                                  $over_all_total += round($partial_total,2);
                                  //array_push($rows,$dt['month_sales_1'],$dt['month_sales_2'],$dt['month_sales_3'],number_format($dt['ave_sales'],2));
                                  array_push($store_qty,array("db_id"=>$dt['db_id'],"qty"=>round($partial_total,2)) );
                                   $temp_qty = $partial_total; 
                                  array_push($rows,number_format($partial_total,2)); //gamita ni if mag rowspan ka
                                  array_push($style, 'text-align:right;'); 

                             }       

                              //array_push($style, 'text-align:right;','text-align:right;','text-align:right;','text-align:right;'); //gamita ni if mag rowspan ka

                         }                          
                        
                    }

                    if($temp_qty == 'none')
                    {
                         array_push($rows,'0.00'); //gamita ni if mag rowspan ka
                         array_push($style, 'text-align:right;'); 
                    }

               }
               //var_dump($rows);
               array_push($rows,number_format($row_total,2));
               array_push($style, 'text-align:right;');    



               $tr_class = '';

               $html .= $this->simplify->populate_table_rows($rows,$style,$tr_class);
           }

            

           $html .= '   </tbody>
                        <tfoot>
                            <tr style="color:white;">
                                <th style="'.$style_header[0].'"></th>
                                <th style="'.$style_header[1].'"></th>
                                <th style="'.$style_header[2].'">TOTAL QTY.</th>';
                                 
                                foreach($store_db_id  as $str)
                                {
                                     $total_qty = 0;
                                     foreach($store_qty as $qty)
                                     {

                                         if($str['db_id'] == $qty['db_id'])
                                         {
                                            $total_qty += $qty['qty'];
                                         }
                                     }
                                     $html .='<th style="text-align:right;">'.number_format($total_qty,2).'</th>';
                                }

                                
           $html .='           <th style="text-align:right;background-color:rgb(0, 86, 137);color:white">'.number_format($over_all_total,2).'</th>  
                           </tr>
                        <tfoot>        
                     </table>                     
                        
                     <script>
                       

                        $("#'.$table_id.'").DataTable({
                                                         fixedColumns: {
                                                                            left: 3,
                                                                            right: 1
                                                                        },
                                                                        scrollCollapse: true,
                                                                        scrollY: 500,
                                                                        scrollX: true,
                                                                        createdRow: function(row, data, dataIndex) 
                                                                        {

                                                                            var lastColumnIndex = data.length - 1; // Index of the last column
                                                                            var lastColumnCell = $(row).find("td:eq(" + lastColumnIndex + ")");
                                                                            var lastColumnHeader = $("th:eq(" + lastColumnIndex + ")", $("#'.$table_id.'").find("thead"));
                                                                            var lastColumnFooter = $("td:eq(" + lastColumnIndex + ")", $("#'.$table_id.'").find("tfoot"));
                                                                            
                                                                            $(lastColumnCell).css({
                                                                                "background-color": "rgb(0, 86, 137)",
                                                                                "color": "white"
                                                                            });     

                                                                            $(row).find("td:eq(0), th:eq(0), td:eq(1), th:eq(1), td:eq(2), th:eq(2)").css({
                                                                                "background-color": "rgb(0, 86, 137)",
                                                                                "color": "white"
                                                                            });


                                                                             $(lastColumnHeader).css({
                                                                                    "background-color": "rgb(0, 86, 137)",
                                                                                    "color": "white"
                                                                             });





                                                                        },
                                                                        dom: "Blfrtip", // Add the "B" for Buttons
                                                                        buttons: [
                                                                            {
                                                                                extend: "copy",
                                                                                title: "ALL STORE AVERAGE SALES QUANTITY",
                                                                                footer: true 
                                                                            },
                                                                            {
                                                                                extend: "csv",
                                                                                title: "ALL STORE AVERAGE SALES QUANTITY",
                                                                                footer: true
                                                                            },
                                                                            {
                                                                                extend: "excel",
                                                                                title: "ALL STORE AVERAGE SALES QUANTITY",
                                                                                footer: true
                                                                            },
                                                                            {
                                                                                extend: "pdf",
                                                                                title: "ALL STORE AVERAGE SALES QUANTITY",
                                                                                // exportOptions:  //mao ni gamiton kung mag specify ka ug unsa ra nga column imung ipa generate
                                                                                // {
                                                                                //         columns: [0,1,2] // Specify the columns you want to export (0-based index)
                                                                                // }
                                                                                footer: true

                                                                            }
                                                                            // ,
                                                                            // {
                                                                            //     extend:"print",
                                                                            //     title:"ALL STORE AVERAGE SALES QUANTITY",
                                                                            //     footer: true
                                                                            // } 
                                                                        ]
                                                      });
                     </script>
                     ';                

                

        return array($stores,$datas,$items,$html,$tab);
    }







   function get_all_average_sales()
   {
       $reorder_batch = $_POST['reorder_batch'];
       $html          = "";
       $details       = $this->Mms_mod->get_all_average_sales($reorder_batch);
       $stores        = array();
       $ave_sales     = array();
       $items         = array();  
       $filter        = $_POST['filter'];

       $store_details    =  $this->store_items($details,$stores,$ave_sales,$items,'average_sales',$html,$filter);

       $stores           =  $store_details[0];  
       $quantity_on_hand =  $store_details[1];  
       $items            =  $store_details[2];  
       $html             =  $store_details[3];  
       $tab              =  $store_details[4];  
       $buttons          = '<button type="button" class="btn btn-danger" data-dismiss="modal">
                                            <i class="bx bx-x d-block d-sm-none"></i>
                                            <span class="d-none d-sm-block ">CLOSE</span>
                                        </button>';            
       $data['buttons'] = $buttons;  
       $data['tab']     = $tab; 

       $data['html']  = $html;
       echo json_encode($data);
       // foreach($details as $det)
       // {
       //      if(!in_array($det['display_name'], $stores))
       //      {
       //           array_push($stores,$det['display_name']);
       //      }

       //      array_push($ave_sales,array("item_code"=>$det['item_code'],"ave_sales"=>$det['ave_sales'],"store"=>$det['display_name']));

       //      // // Loop through the $fruits array and check if the target name exists
       //       $exists = false;

       //      if(count($items)>0)
       //      {

       //          foreach ($items as $it)
       //          {
       //              if ($it['item_code'] === $det['item_code']) 
       //              {
       //                  $exists = true;
       //                  break;
       //              }
       //          }

       //          if (!$exists) 
       //          {
       //              array_push($items,array("item_code"=>$det['item_code'],"Item_description"=>$det['Item_description']) );            
       //          }  
       //      }
       //      else 
       //      {
       //              array_push($items,array("item_code"=>$det['item_code'],"Item_description"=>$det['Item_description']) );                            
       //      }         


       // }


      

       // $table_id  = 'ave_sales_stores';
       // $header    = array("Item No.","Item Description");
       // // Get the index of the last element in the $header array
       // $lastIndex = count($header) - 1;
       // // Merge the $store array into the $header array after the last index
       // $header = array_merge($header, $stores);


       // $html .= $this->simplify->populate_header_table($table_id,$header); 


       // foreach ($items as $it)
       // {
       //     $rows = array(                            
       //                     $it['item_code'],
       //                     $it['Item_description']      
       //                  );
       //     $style = array(
       //                      'text-align:center;',
       //                      'text-align:center;'                             
       //                   );

       //     for($a=0;$a<count($stores);$a++)
       //     {
               
       //          foreach($ave_sales as $ave)
       //          {
       //               if($ave['item_code'] == $it['item_code'] && $stores[$a] == $ave['store'])
       //               {
       //                    array_push($rows,$ave['ave_sales']);
       //                    array_push($style, 'text-align:right;');

       //               }
       //          }


       //     }




       //     $tr_class = '';
       //     $html .= $this->simplify->populate_table_rows($rows,$style,$tr_class);
       // }


       // $html .= '   </tbody>
       //           </table>
       //           <script>
       //              $("#'.$table_id.'").DataTable({ "ordering": false});
       //           </script>
       //           ';     

       // $data['html']  = $html;
       // echo json_encode($data);
   }


   function get_pending_po()
   {
         $supplier_code = $_POST['supplier_code'];
         $item_code     = $_POST['item_code'];
         $from          = $_POST['from'];
         $to            = $_POST['to'];
         $store         = $_POST['store'];
         $reorder_batch = $_POST['reorder_batch'];
         $database_id    = $_POST['database_id'];

         $past_3_month_years = array($to,'empty',$from);
         $header_details     = $this->Mms_mod->get_entries_reorder_report_data_header_final($reorder_batch);


         // $partial_po_list    = array();
         // $final_po_list      = array();

         // $sql_po_list = $this->Mms_mod->get_sql_po(trim($supplier_code),$from,$to,$item_code,$database_id);//pagkuha sa mga PO nga gikan sa SQL
         // if(!empty($sql_po_list))
         // {        
         //     foreach($sql_po_list as $sql)
         //     {                 
         //         array_push($final_po_list,array("vendor"=>$sql['vendor'],'document_no'=>$sql['document_no'],'date'=>$sql['date'],'pending_qty'=>$sql['pending_qty'],'item_code'=>$sql['item_code'],'uom'=>$sql['uom']));  
         //     }
         // }

         // $partial_po_list = array();
         // $po_arr      = $this->Mms_mod->get_pending_po($store,$past_3_month_years,$item_code);  //pagkuha sa mga PO nga gikan sa textile 
         // if(!empty($po_arr))
         // {        
         //        foreach($po_arr as $po)
         //        {
         //             if( (trim($po['vendor']) == trim($supplier_code) ) && !in_array($po['document_no'], array_column($final_po_list, "document_no")) )
         //             {              
         //                 array_push($partial_po_list,array("vendor"=>$po['vendor'],'document_no'=>$po['document_no'],'date'=>$po['date'],'pending_qty'=>$po['pending_qty'],'item_code'=>$po['item_code'],'uom'=>$po['uom']));                   
         //             }
         //        }
         // }


         // if(!empty($partial_po_list))
         // {
         //     foreach($partial_po_list as $partial)
         //     {             
         //       array_push($final_po_list,array("vendor"=>$partial['vendor'],'document_no'=>$partial['document_no'],'date'=>$partial['date'],'pending_qty'=>$partial['pending_qty'],'item_code'=>$partial['item_code'],'uom'=>$partial['uom']));                                     
              
         //     }  
         // }

         $final_po_list = $this->Mms_mod->get_reorder_report_data_po(trim($item_code),$reorder_batch,'all');
         

         
         $html      = '';
           
         $table_id  = 'pending_po_modal';
         $header    = array('Source',"Document No.","PO Date","UOM","Pending Qty","Expected Del. Date","PO Status");        

         $style_header = array('','','','','','','');   

         $html .= $this->simplify->populate_header_table($table_id,$header,$style_header); 

         $user_details = $this->Mms_mod->get_user_details();   



             foreach($final_po_list as $po_line)
             {
                 //$po_date         = date('Y-m-d',strtotime($po_line['date'])).'<input style="display:none;"  id="po_date_'.$po_line['document_no'].'" value="'.date('Y-m-d',strtotime($po_line['date'])).'">';
                 $po_date         = date('Y-m-d',strtotime($po_line['po_date'])).'<input style="display:none;"  id="po_date_'.$po_line['document_no'].'" value="'.date('Y-m-d',strtotime($po_line['po_date'])).'">';
                 $pending_qty     = number_format($po_line['pending_qty']).'<input style="display:none;" id="qty_'.$po_line['document_no'].'" value="'.round($po_line['pending_qty']).'">';

                 //$check_line      = $this->Mms_mod->check_reorder_report_pending_qty('',$po_line['document_no'],$po_line['item_code'],'','','',$_SESSION['user_id']);

                 $check_line      = $this->Mms_mod->check_reorder_report_pending_qty('',$po_line['document_no'],$po_line['item_code'],'','','',$header_details['user_id']);

                 if(!empty($check_line) )
                 {
                     $default_date = 'value="'.$check_line[0]['expected_delivery_date'].'"';       
                 }
                 else 
                 {
                     $default_date = '';                           
                 }

                 if($user_details[0]['user_type'] == 'buyer')
                 {
                    $disable = '';
                 }
                 else 
                 {
                    $disable = 'disabled';                    
                 }


                 $select                   = '*';
                 $table                    = 'reorder_po';
                 $where['document_number'] = $po_line['document_no'];

                 $po_details = $this->Mms_mod->select($select,$table,$where);


                 $database_details = $this->Mms_mod->get_connection($po_line['db_id']);

                 if(!empty($po_details))
                 {
                     if($po_details[0]['status'] == 'Cancel')
                     {
                         $color = "color:red";
                     }
                     else 
                     {
                         $color = "color:blue";                        
                     }
                     $status =  '<strong style="'.$color.'">'.$po_details[0]['status'].'</strong>';
                 }
                 else 
                 {
                     $status = "<strong>For Tagging</strong>";
                 }


                 $expted_del_date = '<input '.$disable.' type="date"  '.$default_date.'  id="date_'.$po_line['document_no'].'" class="pending_date"  name="date" style="font-family:sans-serif; margin-right: 10px;" onchange="validateDates('."'date_".$po_line['document_no']."'".')">';
                     $rows = array( 
                                     $database_details[0]['store'].' '.$database_details[0]['department'], 
                                     $po_line['document_no'],
                                     $po_date,
                                     $po_line['uom'],
                                     $pending_qty,
                                     $expted_del_date,
                                     $status
                                  );   
                     $style = array(
                                        'text-align:center;',
                                        'text-align:center;',
                                        'text-align:center;',
                                        'text-align:center;',
                                        'text-align:right;',
                                        'text-align:center;',
                                        'text-align:center;'
                                   );

                 $tr_class = '';
                 $html .= $this->simplify->populate_table_rows($rows,$style,$tr_class);
             }





         $html .= '   </tbody>
                     </table>
                     <script>
                        $("#'.$table_id.'").DataTable({ "ordering": false});
                     </script>
                     ';

         $buttons      = '';         
         
         

         if($user_details[0]['user_type'] == 'buyer')
         {            
             $buttons      .= '
                                <button type="button" onclick="update_pending_po('."'".$item_code."','".$reorder_batch."'".')" class="btn btn-primary" style="background-color: rgb(5, 68, 104);">
                                     UPDATE
                                </button> ';   
         }

          $buttons     .= '  <button type="button" class="btn btn-danger" data-dismiss="modal">
                                 <i class="bx bx-x d-block d-sm-none"></i>
                                 <span class="d-none d-sm-block ">CLOSE</span>
                             </button>';            
         $data['buttons'] = $buttons;                               
         $data['html']    = $html;   

        echo json_encode($data); 

          

   }


   function suggested_qty_ui()
   {
        $reorder_id            = $_POST['reorder_id'];
        $suggested_reorder_qty = $_POST['suggested_reorder_qty'];
        $html                  = '';
        $reason_list           = $this->Mms_mod->get_reason('','');
        foreach($reason_list as $res)
        {
            $html .= '<input class="reasons" type="checkbox" id="Checkbox'.$res['reason_id'].'"  value="'.$res['reason_id'].'">
                      <label for="myCheckbox" style="margin-right:20px;">'.$res['reason'].'</label>';
        }

        

        $buttons      = '                            
                            <button type="button" class="btn btn-success btn-sm"  style="padding: 6px; width: 67px;" onclick="save_reason('."'".$reorder_id ."','".$suggested_reorder_qty."'".')"><i class="fa fa-edit"></i> save</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">
                                            <i class="bx bx-x d-block d-sm-none"></i>
                                            <span class="d-none d-sm-block ">CLOSE</span>
                            </button>
                            ';            
         $data['buttons'] = $buttons;    
         $data['label']   = 'Select reason of adjustment';                           
         $data['html']    = $html;  

         echo json_encode($data); 
   }


   function suggested_qty_ui_v2()
   {
        $reorder_id            = $_POST['reorder_id'];
        $suggested_reorder_qty = $_POST['suggested_reorder_qty'];
        $html                  = '';
        $reason_list           = $this->Mms_mod->get_reason('','');
        foreach($reason_list as $res)
        {
            $html .= '<div class="col-sm-6">
                        <input class="reasons" type="checkbox" id="Checkbox'.$res['reason_id'].'"  value="'.$res['reason_id'].'">
                        <label for="myCheckbox" style="margin-right:20px;">'.$res['reason'].'</label>
                      </div>  
                     ';
        }

        

        $buttons      = '                            
                            <button type="button" class="btn btn-success btn-sm"  style="padding: 6px; width: 67px;" onclick="save_reason_v2()"><i class="fa fa-edit"></i> save</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">
                                            <i class="bx bx-x d-block d-sm-none"></i>
                                            <span class="d-none d-sm-block ">CLOSE</span>
                            </button>
                            ';            
         $data['buttons'] = $buttons;    
         $data['label']   = 'Select reason of adjustment';                           
         $data['html']    = $html;  

         echo json_encode($data); 
   }




   function save_reason()
   {    
            $checked_reasons          = json_decode($_POST['checked_reasons']);
            $reorder_id               = $_POST['reorder_id'];
            $suggested_reorder_qty    = $_POST['suggested_reorder_qty'];
            $inputed_suggested_qty    = $_POST['inputed_suggested_qty'];

            $suggested_reorder_dr_qty = $_POST['suggested_reorder_dr_qty'];
            $inputed_suggested_dr_qty = $_POST['inputed_suggested_dr_qty']; 




            $reason_id             = '';
            for($a=0;$a<count($checked_reasons);$a++)
            {
                $reason_id .= $checked_reasons[$a].'^';
            }

            $reason_id = substr($reason_id, 0, -1);

            //($checked_reasons,$reorder_id,$suggested_reorder_qty,$inputed_suggested_qty);
            $data['message'] = 'success';

            $table                              = 'reorder_report_change_quantity_history';
            $insert_data['inputed_quantity']    =  str_replace(',','',$inputed_suggested_qty);
            $insert_data['original_quantity']   = str_replace(',','',$suggested_reorder_qty);
            $insert_data['inputed_dr_quantity'] = $inputed_suggested_dr_qty;            
            $insert_data['original_dr_quantity'] = $suggested_reorder_dr_qty;
            $insert_data['date_inputed']        = date('Y-m-d H:i:s');//date('Y-m-d h:i:s A'); para ma kita ang am or pm
            $insert_data['reorder_id']          = $reorder_id;
            $insert_data['reason_id']           = $reason_id;
            $insert_data['hist_user_id']        = $_SESSION['user_id'];


            $current_user_login = $this->Mms_mod->get_user_details();

            if(in_array($current_user_login[0]['user_type'],array('category-head','corp-manager','incorporator','buyer')))
            {
                 $current_user_Details =  $this->Mms_mod->get_user_connection($_SESSION['user_id']);
                 if(empty($current_user_Details))
                 {
                     $insert_data['status']      = 'Approved';
                     $insert_data['approved_by'] = $_SESSION['user_id'];
                 }
                 else 
                 {
                     $qty_hist = $this->Mms_mod->get_reorder_report_data_lines_final_single_line($reorder_id);

                     if($current_user_login[0]['user_type'] == 'category-head' || ($current_user_Details[0]['value_'] == 'cdc' && $qty_hist[0]['store']  != 'cdc') )
                     {
                         $insert_data['status']      = 'Approved';
                         $insert_data['approved_by'] = $_SESSION['user_id'];
                     }
                     else 
                     {
                         $insert_data['status']  = 'Pending';
                     }
                 }

            }
            else 
            {
                 $insert_data['status']  = 'Pending';
            }


            $this->Mms_mod->update_reorder_report_change_quantity_history($reorder_id,'clear pending');


            $this->Mms_mod->insert_table($table,$insert_data);

            echo json_encode($data);
   }



   function update_pending_po()
   {
         $exp_del_date  = json_decode($_POST['exp_del_date']);
         $document_no   = json_decode($_POST['document_no']);
         $pending_qty   = json_decode($_POST['pending_qty']);
         $po_date       = json_decode($_POST['po_date']);
         $item_code     = $_POST['item_code'];
         $reorder_batch = $_POST['reorder_batch'];

         for($a=0;$a<count($document_no);$a++)
         {
              $check_line = $this->Mms_mod->check_reorder_report_pending_qty($reorder_batch,$document_no[$a],$item_code,$po_date[$a],$pending_qty[$a],'',$_SESSION['user_id']);
              if(empty($check_line))
              {
                     $insert_data['reorder_batch'] = $reorder_batch;
                     $insert_data['document_no']   = $document_no[$a];
                     $insert_data['item_code']     = $item_code;
                     $insert_data['po_date']       = $po_date[$a];
                     $insert_data['pending_qty']   = $pending_qty[$a];
                     $insert_data['expected_delivery_date'] = $exp_del_date[$a];
                     $insert_data['user_id']                = $_SESSION['user_id'];
                     $table = 'reorder_report_pending_qty';


                     $this->Mms_mod->insert_table($table,$insert_data);
              }
              else 
              {
                    $this->Mms_mod->update_reorder_report_pending_qty($reorder_batch,$document_no[$a],$item_code,$po_date[$a],$pending_qty[$a],$exp_del_date[$a],$_SESSION['user_id'],$check_line[0]['pending_id']);
              }

         }

         $data['message']= 'success';
         echo json_encode($data);        
   }


   function approve_disapprove_batch()
   {
       $reorder_batch = $_POST['reorder_batch'];
       $status        = $_POST['status'];
       $this->Mms_mod->update_reorder_report_data_batch($status,$reorder_batch);

       $data['message'] = 'success';
       echo json_encode($data);
   }






   function change_qty_details()
   {
        $reoder_id = $_POST['reoder_id'];
        $vend_type = $_POST['vend_type'];

        $html      = '';

        $current_user_login =  $this->Mms_mod->get_user_connection($_SESSION['user_id']);

        $quantity_details =  $this->Mms_mod->get_reorder_report_change_quantity_history($reoder_id,'','');

        $data['report_label'] = $quantity_details[0]['Item_description'];


        if(strstr(strtoupper($vend_type),"SI") && strstr(strtoupper($vend_type),"DR"))
        {
             $header = array('Adjusted By','Date of Adjustment','Adjusted SI Quantity','Original SI Quantity','Adjusted DR Quantity','Original DR Quantity','Reasons for adjustment','status','Remarks by');
             $style_header = array('','','','','','','','','');
        }
        else 
        {
             $header = array('Adjusted By','Date of Adjustment','Adjusted Quantity','Original Quantity','Reasons for adjustment','status','Remarks by');
             $style_header = array('','','','','','','');
        }



        $table_id     = 'adjustment_history_table';
        $html        .= $this->simplify->populate_header_table($table_id,$header,$style_header);


        foreach($quantity_details as $qty)
        {
              $reason_arr        =  explode('^',$qty['reason_id']);  
              $date_inputed      = date('M d, Y  -- h:i  A',strtotime(date($qty['date_inputed'])));

              $inputed_quantity  = $qty['inputed_quantity'];
              $original_quantity = $qty['original_quantity'];

              $inputed_dr_quantity  = $qty['inputed_dr_quantity'];
              $original_dr_quantity = $qty['original_dr_quantity'];              

              $approved_by       = $qty['approved_by'];
              $status            = $qty['status'];
              $quantity_id       = $qty['quantity_id'];
              $reasons_list      = $this->Mms_mod->get_reason('reason details',$reason_arr);       
              $emp_details       =   $this->simplify->get_employee_details($qty['emp_id']);



              $get_approved_emp_id   = $this->Mms_mod->get_reorder_report_change_quantity_history('',$quantity_id,'category_head');
              if(!empty($get_approved_emp_id))
              {
                  $category_head_details = $this->simplify->get_employee_details($get_approved_emp_id[0]['emp_id']); 
                  $category_head         = $category_head_details[0]['name'];
              }
              else 
              {
                  $category_head         = '';
              }

              


              $adjustment_reasons    = '';  
              foreach($reasons_list as $res)
              {
                  $adjustment_reasons .= '
                                             <div class="col-sm-6"><i class="fas fa-pencil-alt"></i>
                                                      '.$res['reason'].'
                                             </div>     
                                         ';
              }    

              


              $row1   = array(
                                 $emp_details[0]['name'],
                                 $date_inputed,
                                 $inputed_quantity, 
                                 $original_quantity,
                                 $adjustment_reasons,
                                 $status,
                                 $category_head
                              );

                           
               $style1 = array(
                                   "text-align:center;",
                                   "text-align:center;",
                                   "text-align:center;",
                                   "text-align:center;", 
                                   "text-align:center;", 
                                   "text-align:center;", 
                                   "text-align:center;"
                              );


              if(strstr(strtoupper($vend_type),"SI") && strstr(strtoupper($vend_type),"DR"))
              {
                 // Insert $newHeader after index 4
                 array_splice($row1, 4, 0, $inputed_dr_quantity);
                 array_splice($style1, 4, 0, "text-align:center;");

                 array_splice($row1, 5, 0, $original_dr_quantity);
                 array_splice($style1, 5, 0, "text-align:center;");
              }

                


               $tr_class ='tr_';
               $html  .= $this->simplify->populate_table_rows($row1,$style1,$tr_class); 
        }

        $data['quantity_id'] = $quantity_id;

        $html    .= '
                              </tbody>
                          </table>
                          <br><br>
                       <script>
                               $("#'.$table_id.'").dataTable({                            
                                                                 "order":false                                  
                                                              });
                       </script>';
 




        // foreach($quantity_details as $qty)
        // {
        //       $reason_arr        =  explode('^',$qty['reason_id']);  
        //       $date_inputed      = date('M d, Y  -- h:i  A',strtotime(date($qty['date_inputed'])));
        //       $inputed_quantity  = $qty['inputed_quantity'];
        //       $original_quantity = $qty['original_quantity'];
        //       $approved_by       = $qty['approved_by'];
        //       $status            = $qty['status'];
        //       $quantity_id       = $qty['quantity_id'];
        // }

       //  $reasons_list = $this->Mms_mod->get_reason('reason details',$reason_arr);
        
        
       //  $emp_details =   $this->simplify->get_employee_details($quantity_details[0]['emp_id']);

       //  $style = 'font-size:15px;';
       //  $html .= '<div class="row" style="'.$style.'">
       //              <div class="col-sm-6">
       //                    <strong>Name:</strong> '.$emp_details[0]['name'].'  
       //              </div>    
       //              <div class="col-sm-6">
       //                    <strong>Date of adjustment:</strong> '.$date_inputed.'
       //              </div>                      
       //           </div>
       //           <div class="row" style="'.$style.'">
       //              <div class="col-sm-6">
       //                   <strong>Adjusted Quantity:</strong> '.$inputed_quantity.'
       //              </div>
       //              <div class="col-sm-6">
       //                   <strong>Original Quantity:</strong> '.$original_quantity.'
       //              </div>';
       // $html  .= '
       //             <div class="col-sm-6">
       //                  <div class="row">
       //                      <div class="col-sm-9">
       //                          <strong><h4>Reasons for adjustment:</h4></strong>
       //                      </div>   
       //                  </div>
       //                  <div class="row">';
       // foreach($reasons_list as $res)
       // {
       //        $html .= '
       //                     <div class="col-sm-6"><i class="fas fa-pencil-alt"></i>
       //                              '.$res['reason'].'
       //                     </div>     
       //                 ';
       // }                 


       // $html  .= '      </div>
       //             </div>
       //           ';    


       // if($status != 'Pending' )  
       // {
       //     $get_approved_emp_id = $this->Mms_mod->get_reorder_report_change_quantity_history('',$quantity_id,'category_head');            

       //     $emp_details =   $this->simplify->get_employee_details($get_approved_emp_id[0]['emp_id']); 
           
                
       //     $html .= '
       //               <div class="col-sm-12">
       //               </div>
       //               <div class="col-sm-12">
       //                   <div class="row">
       //                       <div class="col-sm-6">
       //                          <strong>'.$status.' by:</strong> '.$emp_details[0]['name'].'
       //                       </div> 
       //                   </div>   
       //               </div>
       //              '; 
       // }                   

       // $html  .=' </div>
       //          ';
         

        $buttons = ''; 
        if($status == 'Pending' && in_array($current_user_login[0]['user_type'],array('category-head')))        
        {

        $buttons = '<button type="button" class="btn btn-success btn-sm" id="approve_btn-'.$quantity_id.'" style="padding: 6px 6px;width: 90px;margin-top: 16px;" onclick="approve_quantity_input('."'".$quantity_id."','".round($quantity_details[0]['inputed_quantity'])."','".$reoder_id."'".')"><i class="fa fa-edit"></i> approve</button>
                    <button type="button" class="btn btn-warning btn-sm" id="disapprove-'.$quantity_id.'" style="padding: 6px 6px;margin-top: 16px;" onclick="disapprove_quantity_input('."'".$quantity_id."','".round($quantity_details[0]['inputed_quantity'])."','".$reoder_id."'".')"><i class="fa fa-edit"></i> disapprove</button>';
        }    
        $buttons .=' <button type="button" class="btn btn-danger btn-sm"  data-dismiss="modal" style="padding: 6px 6px;margin-top: 16px;" > CLOSE</button>';
                     
           
       
        

       $data['buttons'] = $buttons;  
       $data['html']    = $html;

       echo json_encode($data);

         
   }



   function check_change_quantity_history()
   {
         $reorder_batch = $_POST['reorder_batch'];
         $user_type     = $_POST['user_type'];

         $check_history = $this->Mms_mod->check_change_quantity_history($reorder_batch);


         if(empty($check_history))
         {
              $message = 'proceed';  
         }
         else 
         {
              if($user_type == 'buyer')
              {
                  $current_user_login =  $this->Mms_mod->get_user_connection($_SESSION['user_id']);
                  $batch_user_id      =  $this->Mms_mod->get_user_connection($check_history[0]['user_id']);


                  if($current_user_login[0]['value_'] == 'cdc'  && $batch_user_id[0]['value_'] != 'cdc')
                  {
                     $message = 'cannot proceed. there are pending requests for adjustment in suggested reorder quantity';
                  }
                  else 
                  {
                     $message = 'proceed';  
                  }
              }
              else 
              {
                  $message = 'cannot proceed. there are pending requests for adjustment in suggested reorder quantity';
              }
         }


         $data['message'] = $message;
         echo json_encode($data);
   }



   function update_si_dr($qty,$id,$vend_type)
   {
         // $qty       = $_POST['qty'];
         // $id        = $_POST['id'];
         // $vend_type = $_POST['vend_type'];

         $table = 'reorder_report_data_lines_final';   
         if($vend_type == 'si')
         {
            $column_data['suggested_reord_qty'] = $qty;
         }
         else 
         {
            $column_data['suggested_reord_qty_dr'] = $qty;            
         }

         $column_filter['reoder_id'] = $id;
         $this->Mms_mod->update_table($table,$column_data,$column_filter);
   }



   function update_batch()
   {
        $reorder_batch = $_POST['reorder_batch'];
        $status = $_POST['status'];
        $item_list_json = $_POST['item_list'];

        // Decode the JSON string to an associative array
        $item_list = json_decode($item_list_json, true);

        
        for($a=0;$a<count($item_list);$a++)
        {
            // var_dump($item_list[$a][0]['inp_qty'],$item_list[$a][1]['reoder_id'],$item_list[$a][2]['vnd_type']);
            $this->update_si_dr($item_list[$a][0]['inp_qty'] , $item_list[$a][1]['reoder_id'] , $item_list[$a][2]['vnd_type']);
        }


        $reorder_batch = $_POST['reorder_batch'];
        $status        = $_POST['status'];
        $this->Mms_mod->update_batch($status,$reorder_batch);

        $data['message'] = 'success';
        echo json_encode($data);
   }


   function update_reorder_report_change_quantity_history()
   {
         //$quantity_id = $_POST['quantity_id'];
         $status      = $_POST['status'];
         $checked     = $_POST['checked'];
         $checked_arr = json_decode($checked);


         for($a=0;$a<count($checked_arr);$a++)
         {
             $reoder_id = explode('_', $checked_arr[$a]);


             $quantity_details =  $this->Mms_mod->get_reorder_report_change_quantity_history($reoder_id[0],'','');
             
             if(!empty($quantity_details))
             {
                 $this->Mms_mod->update_reorder_report_change_quantity_history($quantity_details[0]['quantity_id'],$status);            
             }

         }
         
         $data['message'] = 'success';

         echo json_encode($data);
   }


   function generate_textfile()
   {
          $vend_type     = $_POST['vend_type'];
          $nav_si_doc_no = $_POST['si_input'];
          $nav_dr_doc_no = $_POST['dr_input'];
          





          $reorder_batch_arr  = $_POST['reorder_batch_arr'];
          $reorder_batch_list = json_decode($reorder_batch_arr);

          $memory_limit = ini_get('memory_limit');
          ini_set('memory_limit',-1);
          ini_set('max_execution_time', 0);
          $new_line .= PHP_EOL;  //pang new line ni


          for($a=0;$a<count($reorder_batch_list);$a++)
          {
              $table = 'reorder_report_data_batch';
              $column_data['nav_si_doc_no'] = $nav_si_doc_no;
              $column_data['nav_dr_doc_no'] = $nav_dr_doc_no;
              $column_filter['reorder_batch'] = $reorder_batch_list[$a]; 

              var_dump($column_data);
              $this->Mms_mod->update_table($table,$column_data,$column_filter);


             $store_handled  = $this->Mms_mod->get_entries_reorder_report_data_header_final_by_bu($reorder_batch_list[$a]);
             $store          = ($store_handled[0]['value_']);
             $report_details = $this->Mms_mod->generate_reorder_report_mod($reorder_batch_list[$a],$store,$store_handled[0]['user_id']);

             $tot_amount      = 0;
             $tot_amt_inc_vat = 0;
             foreach($report_details as $det)
             {
                 if(strtoupper($vend_type) == 'SI') 
                 {
                    $qty = $det['suggested_reord_qty'];  
                    $nav_document_no = "SI_".$nav_si_doc_no;  
                    $document_number = $nav_si_doc_no;
                 }
                 else 
                 {
                    $qty = $det['suggested_reord_qty_dr'];  
                    $nav_document_no = "DR_".$nav_dr_doc_no;  
                    $document_number = $nav_dr_doc_no;
                 }


                 $tot_amount      += ($det['unit_price']          * $qty);
                 $tot_amt_inc_vat += ($det['unit_price_incl_vat'] * $qty);
                 //$currency_code   = $det['currency_code'];
             }



              //echo "hello world".$reorder_batch_list[$a];
              $reorder_details = $this->Mms_mod->get_entries_reorder_report_data_header_final($reorder_batch_list[$a]);

              $doc_number       = str_pad($reorder_batch_list[$a], 7, '0', STR_PAD_LEFT);
              $store            = $reorder_details['value_'];
              $supplier_code    = $reorder_details['supplier_code'];
              $supplier_details = $this->Mms_mod->get_po_calendar($supplier_code);  
              $date_generated   = date('m/d/y');
              $date_today       = date('Y-m-d');

              if($supplier_details[0]['payment_terms_code'] == '')
              {
                 $days_to_add = 0;                
              }
              else 
              {
                 $days_to_add = str_replace(array('days', 'DAYS'), '', $supplier_details[0]['payment_terms_code']);               
              }
              $due_date = date('m/d/y', strtotime('+' . $days_to_add . ' days', strtotime($date_today)));


              $currency_code = $supplier_details[0]['currency_filter'];
              if(strtoupper($currency_code) == 'PHP')
              {
                 $currency_factor = 1;
              }
              else 
              {
                 $currency_factor = 0;                
              }



              $num_generated                   = $reorder_details['number_generated']+1;
              $update_data['number_generated'] = $num_generated;
              $this->Mms_mod->update_number_generated($num_generated,$reorder_batch_list[$a]);


              $otdl             = $supplier_details[0]['otdl'];
              $buffer           = $supplier_details[0]['buffer'];
              $frequency        = $supplier_details[0]['frequency'];

              $lead_time_factor = $otdl+$buffer+$frequency;

              
               $purch = 1;
               $Store_purch = 0;  



              header('Content-Type: text/plain');
              header('Content-Disposition: attachment; filename="MMSR-'.strtoupper($store).'_'.$doc_number.'-'.$nav_document_no.'.txt"');
              header("Content-Transfer-Encoding: binary");
              ob_clean();

              

               // echo '"Order"|"MMSR-'.strtoupper($store).'-'.$doc_number.'"|"'.
               echo '"Order"|"'.$document_number.'"|"'.
                    $supplier_code.'"|"'.
                    $supplier_code.'"|"'.
                    $supplier_details[0]['name_'].'"|"'.
                    $supplier_details[0]['name_'].'"|"'.
                    $supplier_details[0]['address'].'"|"'.
                    $supplier_details[0]['address_2'].'"|"'.
                    $supplier_details[0]['city'].'"|"'.
                    $supplier_details[0]['contact'].'"|"'.
                    $reorder_details['customer_name'].'"|"'.
                    $reorder_details['customer_name'].'"|"'.                    
                    $reorder_details['customer_address'].'"|"'.                    
                    $reorder_details['customer_address'].'"|"'.   
                    $date_generated.'"|"'.  
                    $date_generated.'"|"'.  
                    'Order MMSR-'.strtoupper($store).'-'.$doc_number.'"|"'.
                    $supplier_details[0]['payment_terms_code'].'"|"'.
                    $due_date.'"|"'.
                    $reorder_details['location_code'].'"|"'. 
                    $reorder_details['company_code'].'"|"'. 
                    $reorder_details['department_code'].'"|"'. 
                    $supplier_details[0]['posting_grp'].'"|"'.
                    $currency_code.'"|"'.
                    $currency_factor.'"|"'.
                    $supplier_details[0]['prices_including_vat'].'"|"'.
                    $supplier_details[0]['invoice_disc_code'].'"|"'.
                    $supplier_details[0]['gen_bus_posting_group'].'"|"'.
                    $supplier_details[0]['name_'].'"|"'.
                    $supplier_details[0]['name_'].'"|"'.
                    $supplier_details[0]['address'].'"|"'.
                    $supplier_details[0]['address_2'].'"|"'.
                    $supplier_details[0]['city'].'"|"'.
                    $supplier_details[0]['contact'].'"|"G/L Account"|""|""|""|"'.
                    $supplier_details[0]['vat_bus_posting_group'].'"|"'.
                    $num_generated.'"|"'.
                    $reorder_details['responsibility_center'].'"|"'.
                    $supplier_details[0]['bus_posting_group'].'"|""|""|""|"'.
                    $lead_time_factor.'"|"'.
                    $otdl.'"|"'.
                    $buffer.'"|"'.
                    $frequency.'"|""|"'.$tot_amount.'"|"'.$tot_amt_inc_vat.'"|"'.
                    $date_generated.'"|"Receive"|"'.$purch.'"|"'.$Store_purch.'"|"Finalized"|"Released"'
                    .$new_line;


          }

         // echo "\n";
          $line_number = 10000;
          for($a=0;$a<count($reorder_batch_list);$a++)
          {
                 $store_handled  = $this->Mms_mod->get_entries_reorder_report_data_header_final_by_bu($reorder_batch_list[$a]);
                 $store          = ($store_handled[0]['value_']);
                 $report_details = $this->Mms_mod->generate_reorder_report_mod($reorder_batch_list[$a],$store,$store_handled[0]['user_id']);
                 $doc_number     = str_pad($reorder_batch_list[$a], 7, '0', STR_PAD_LEFT);


                 foreach($report_details as $det)
                 {

                     if(strtoupper($vend_type) == 'SI') 
                     {
                        $qty = $det['suggested_reord_qty'];                          
                     }
                     else 
                     {
                        $qty = $det['suggested_reord_qty_dr'];                           
                     }   


                    
                     if(in_array($det['department'],array('SM','SOD')) ) //if supermarket ang reorder
                     {
                         //$item_details = $this->Mms_mod->get_item_details_sql($det['db_id'],$det['item_code']);

                         $inventory_posting_group = '';
                         $description             = '';
                         $vat_prod_posting_grp    = '';
                         $gen_prod_posting_grp    = '';
                         $wht_prod_posting_grp    = '';

                         $item_details = $this->Mms_mod->get_item_details_sql(5,$det['item_code'],$det['uom']); //db_id 5 kay  ICM_SM_SERVER_POS_SQL  supermarket POS Server ni siya
                          

                         foreach($item_details as $item_det)
                         {
                             $inventory_posting_group = $item_det['inventory_posting_group'];
                             $description             = $item_det['description'];
                             $vat_prod_posting_grp    = $item_det['vat_prod_posting_grp'];
                             $gen_prod_posting_grp    = $item_det['gen_prod_posting_grp'];
                             $wht_prod_posting_grp    = $item_det['wht_prod_posting_grp'];
                         } 
                     }



                     if(!isset($vat_prod_posting_grp))
                     {
                         $vat_percent = 'No setup';                        
                     }
                     else
                     if($vat_prod_posting_grp == 'VAT12')
                     {
                         $vat_percent = 12;
                     }
                     else 
                     if($vat_prod_posting_grp == 'NO VAT')
                     {
                         $vat_percent = 0;                        
                     }
                     else 
                     if($vat_prod_posting_grp == 'VAT10')
                     {
                         $vat_percent = 10;
                     }
                     else 
                     {
                        $vat_percent = $vat_prod_posting_grp;
                     }


                     // $amount          = $det['unit_price']          * $det['suggested_reord_qty'];
                     // $amount_incl_vat = $det['unit_price_incl_vat'] * $det['suggested_reord_qty'];
                     $amount          = $det['unit_price']          * $qty;
                     $amount_incl_vat = $det['unit_price_incl_vat'] * $qty;



                     
                     
                      $uom_details = $this->Mms_mod->get_item_uom_details($det['department'],$det['item_code'],$det['uom']);

                      if(count($uom_details) > 0)
                      {
                          foreach($uom_details as $uom_dets)
                          {
                             $qty_per_unit_of_measure = round($uom_dets['qty_uom']);
                          }
                      }
                      else 
                      {                        
                          $qty_per_unit_of_measure = 'UOM NOT FOUND OR ITEM NOT FOUND';
                      }



                     // $quantity_base           = $qty_per_unit_of_measure * $det['suggested_reord_qty']; 
                     $quantity_base           = $qty_per_unit_of_measure * $qty; 


 
                     // if($det['suggested_reord_qty'] > 0.00)
                     if($qty > 0.00)
                     {  
                         if(strstr($det['item_code'],'-'))
                         {
                             $exp_item  = explode('-',$det['item_code']);
                             $item_code = $exp_item[0]; 
                             $variant   = $exp_item[1]; 
                         }
                         else 
                         {
                             $item_code = $det['item_code']; 
                             $variant   = '';   
                         }


                        // echo  $new_line.'"Order"|"MMSR-'.strtoupper($store).'-'.$doc_number.'"|"'.
                        echo  $new_line.'"Order"|"'.$document_number.'"|"'.
                              $line_number.'"|"'.
                              $det['supplier_code'].'"|"'.
                              'item'.'"|"'.
                              $item_code.'"|"'.
                              $det['location_code'].'"|"'.
                              $inventory_posting_group.'"|"'.
                              $date_generated.'"|"'.
                              $description.'"|"'.
                              $det['uom'].'"|"'.
                              $qty.'"|"'.
                              $qty.'"|"'.
                              $qty.'"|"'.
                              $qty.'"|"'.
                              $det['unit_price_incl_vat'].'"|"'.
                              $det['unit_price'].'"|"'.                              
                              $vat_percent.'"|"'.
                              round($amount,2).'"|"'.
                              round($amount_incl_vat,2).'"|""|"'.
                              'yes'.'"|"'.
                              $det['company_code'].'"|"'.
                              $det['department_code'].'"|"'.
                              '"|"'.
                              round($amount_incl_vat,2).'"|"'.
                              $det['supplier_code'].'"|"'.
                              $supplier_details[0]['gen_bus_posting_group'].'"|"'.
                              $gen_prod_posting_grp.'"|"'.
                              '"|"'.
                              $supplier_details[0]['vat_bus_posting_group'].'"|"'.
                              $vat_prod_posting_grp.'"|"'.
                              $currency_code.'"|"'.
                              round($amount_incl_vat,2).'"|"'.
                              '"|"'.
                              $det['unit_price'].'"|"'.
                              '"|"'.
                              round($amount_incl_vat,2).'"|"'.
                              '"|"'.
                              '"|"'.
                              $vat_prod_posting_grp.'"|"'.
                              $variant.'"|"'.
                              '"|"'.
                              $qty_per_unit_of_measure.'"|"'.
                              $det['uom'].'"|"'.
                              $quantity_base.'"|"'.
                              $quantity_base.'"|"'.
                              $quantity_base.'"|"'.
                              $quantity_base.'"|"'.
                              '"|"'.
                              $det['responsibility_center'].'"|"'.
                              $date_generated.'"|"'.
                              $date_generated.'"|"'.
                              'yes'.'"|"'.                              
                              $supplier_details[0]['bus_posting_group'].'"|"'.
                              $wht_prod_posting_grp.'"|"'.
                              'Yes'.'"|"'.
                              'No'.'"|"'.
                              'No'.'"|"'.
                              'No'.'"|"'.
                              'Unlimited'.'"|"'.
                              '0D'.'"|"'.
                              $det['barcode'].'"'
                              ;
                        $line_number += 10000;     
                     }


                 }

           }



          ini_set('memory_limit',$memory_limit );

   }


   function generate_pdf()
   {
        $allRowsData        = json_decode($_POST['allRowsData'], true);
        $tableHeader        = json_decode($_POST['tableHeader'], true);
        $reorder_batch      = $_POST['reorder_batch']; 
        


        $totalElements = count($allRowsData);
        $lastIndex = $totalElements - 1;
        // $lastKey = $allRowsData[$lastIndex];
        // var_dump($lastIndex);


        $current_user_login =  $this->Mms_mod->get_user_details();
         var_dump($tableHeader);

         $this->ppdf = new TCPDF();
         $this->ppdf->SetTitle("Rerorder Report");            
         $this->ppdf->SetMargins(5, 15, 5, true);         
         $this->ppdf->setPrintHeader(false);
         $this->ppdf->SetFont('', '', 10, '', true);                    
         $this->ppdf->AddPage("L",array(215.9, 330.2)); //long bond paper
         $this->ppdf->SetAutoPageBreak(true);
         $head = $this->Mms_mod->get_entries_reorder_report_data_header_final($reorder_batch);


         $header_details = $this->Mms_mod->get_reorder_report_data_header_final_details($reorder_batch);
         $non_cdc        = 0;
         $cdc            = 0;
         $store_type     = 0;
         $non_store_type = 0;
         foreach($header_details as $hd)
         {
             if($hd['store'] != 'cdc')
             {
                  $non_cdc +=1;   
                  if($hd['bu_type'] == 'NON STORE')
                  {
                     $non_store_type += 1;
                  }
                  else 
                  if($hd['bu_type'] == 'STORE')                
                  {
                    $store_type += 1;
                  }
             }
             else 
             {
                  $cdc +=1;
             }
         }

         



         $border                = 1;
         $column_header_1_style = 'width:150px;font-size:9px;';
         $column_header_2_style = 'width:280px;font-size:9px;';
         $column_header_3_style = 'width:100px;font-size:9px;';
         $table_header_style = 'background-color:yellow;';
         $header_style = 'text-align:center;font-weight: bold;font-size:9px;color:black;'; 
         $tbl          = ' 
                               <table cellspacing="1" cellpadding="1" border="0"  style="text-align: center;font-size:15px;color:black;">
                                    <tr>
                                        <td style="width:700px">
                                             <strong>Reorder Report</strong>
                                        </td>
                                    </tr>
                               </table>
                               <table cellspacing="1" cellpadding="1" border="0"  style="text-align: left;color:black;">
                                   <tr>
                                        <td style="'.$column_header_1_style.'">
                                             <strong>Reorder Date (PO CALENDAR)</strong>
                                        </td>                                         
                                        <td style="'.$column_header_2_style.'">
                                             '.date('M d, Y',strtotime($head["date_tag"])).'
                                        </td>
                                        <td style="'.$column_header_3_style.'">                                        
                                             <strong>Reorder Number</strong>
                                        </td>
                                        <td>
                                            '.$head["reorder_batch"].'
                                        </td>
                                   </tr>                                 
                               </table>
                               <table cellspacing="1" cellpadding="1" border="0"  style="text-align: left;color:black;">
                                   <tr>
                                        <td style="'.$column_header_1_style.'">
                                            <strong>Supplier Code</strong>
                                        </td>
                                        <td style="'.$column_header_2_style.'">
                                            '.$head["supplier_code"].'
                                        </td>
                                        <td style="'.$column_header_3_style.'">   
                                             <strong>Date Generated</strong>                                     
                                        </td>
                                        <td>
                                             '.date('M d, Y -- h:i A',strtotime($head['date_generated']) ).'   
                                        </td>
                                   </tr>
                               </table>
                               <table cellspacing="1" cellpadding="1" border="0"  style="text-align: left;color:black;">
                                  <tr>
                                        <td style="'.$column_header_1_style.'">
                                            <strong>Supplier Name</strong>
                                        </td>
                                        <td style="'.$column_header_2_style.'">
                                            '.$head["supplier_name"].'
                                        </td>
                                        <td style="'.$column_header_3_style.'">      
                                             <strong>Lead Time Factor</strong>                                  
                                        </td>
                                        <td>
                                             '.$head["lead_time_factor"].'
                                        </td>
                                  </tr>
                               </table>
                               <table cellspacing="1" cellpadding="1" border="'.$border.'"   >
                                   <tr style="'.$table_header_style.'">';  

                         if($store_type == 0 && $non_store_type > 0)
                         {
                             $width_arr =  array(35,120,50,50,50,50,50,50,50,50,50,50,50,60,45,78);
                         }  
                         else         
                         if($non_cdc > 0  && $cdc > 0)
                         {
                              $width_arr =  array(35,130,42,42,42,42,42,42,42,42,42,42,42,42,42,50,44,78);
                         }  
                         else 
                         {
                              $width_arr =  array(35,178,54,54,54,54,54,54,54,54,54,60,45,78);
                         }        

                         $print = false;   
                         $indx  = 0;
                         for($b=0;$b<count($tableHeader);$b++)
                         {
                             if($tableHeader[ $b] == 'ITEM NO')
                             {
                                $print = true; 
                             }

                             if($print)
                             {
                                $tbl .= '<th style="'.$header_style.'width:'.$width_arr[$indx].'px;">'.$tableHeader[$b].'</th>';
                                $indx += 1;
                             }

                             if($tableHeader[ $b] == 'SUGGESTED REORDER QTY')
                             {
                                 $print = false;   
                             }
                         }                                                      

                         $tbl .= '       </tr>';                    

                                    
                    
                                   $loop= 0;
                                   $line_counter=1;
                                   $page_counter = 1;
                                   $row_style    = 'font-size:9px;'; 
                                   foreach($allRowsData  as $key => $data)
                                   {

                                          preg_match('/<strong>(\d+)<\/strong>/', $data[16], $matches);
                                          $pending_qty = $matches[1];

                                          preg_match('/value="([^"]+)"/', $data[17], $get_qty);
                                          $suggested_qty = $get_qty[1];

                                          preg_match('/<em[^>]*>(.*?)<\/em>/', $data[17], $get_overstock); //pag kuha sa overstock nga label
                                          $overstock = '<br><em style="color:red;">'.$get_overstock[1].'</em>';
                                        

                                          preg_match('/<i[^>]*>(.*?)<\/i>/', $data[17], $stat); //pag kuha sa status nga label
                                          $status    = '<br><strong style="color:red;">'.$stat[1].'</strong>';



                                         if($store_type == 0 && $non_store_type > 0)
                                         {
                                                 $tbl .= '<tr>
                                                             <td style="'.$row_style.'text-align:center;">'.$data[0].'</td>
                                                             <td style="'.$row_style.'text-align:center;height: 30px;">'.$data[1].'</td>
                                                             <td style="'.$row_style.'text-align:center;">'.$data[2].'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$data[3].'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$data[4].'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$data[5].'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$data[6].'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$data[7].'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$data[8].'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$data[9].'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$data[10].'</td>                                                           
                                                             <td style="'.$row_style.'text-align:center;">'.$data[13].'</td>
                                                             <td style="'.$row_style.'text-align:center;">'.$data[14].'</td>
                                                             <td style="'.$row_style.'text-align:center;">'.$data[15].'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$pending_qty.'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$suggested_qty.$overstock.$status.'</td>
                                                     </tr>'; 
                                         }
                                         else 
                                         if($non_cdc > 0 && $cdc >0)   
                                         {                                           
                                             // preg_match('/<strong>(\d+)<\/strong>/', $data[16], $matches);
                                             // $pending_qty = $matches[1];

                                             // preg_match('/value="([^"]+)"/', $data[17], $get_qty);
                                             // $suggested_qty = $get_qty[1];

                                             // preg_match('/<em[^>]*>(.*?)<\/em>/', $data[17], $get_overstock); //pag kuha sa overstock nga label
                                             // $overstock = '<br><em style="color:red;">'.$get_overstock[1].'</em>';
                                        

                                             // preg_match('/<i[^>]*>(.*?)<\/i>/', $data[17], $stat); //pag kuha sa status nga label
                                             // $status    = '<br><strong style="color:red;">'.$stat[1].'</strong>';

                                         
                                             $tbl .= '<tr>
                                                             <td style="'.$row_style.'text-align:center;">'.$data[0].'</td>
                                                             <td style="'.$row_style.'text-align:center;height: 30px;">'.$data[1].'</td>
                                                             <td style="'.$row_style.'text-align:center;">'.$data[2].'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$data[3].'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$data[4].'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$data[5].'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$data[6].'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$data[7].'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$data[8].'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$data[9].'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$data[10].'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$data[11].'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$data[12].'</td>
                                                             <td style="'.$row_style.'text-align:center;">'.$data[13].'</td>
                                                             <td style="'.$row_style.'text-align:center;">'.$data[14].'</td>
                                                             <td style="'.$row_style.'text-align:center;">'.$data[15].'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$pending_qty.'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$suggested_qty.$overstock.$status.'</td>
                                                     </tr>'; 
                                         }   
                                         else                                             
                                         {
                                             // preg_match('/<strong>(\d+)<\/strong>/', $data[16], $matches);
                                             // $pending_qty = $matches[1];

                                             // preg_match('/value="([^"]+)"/', $data[17], $get_qty);
                                             // $suggested_qty = $get_qty[1]; 

                                             // preg_match('/<em[^>]*>(.*?)<\/em>/', $data[17], $get_overstock); //pag kuha sa overstock nga label
                                             // $overstock = '<br><em style="color:red;">'.$get_overstock[1].'</em>';
                                        

                                             // preg_match('/<i[^>]*>(.*?)<\/i>/', $data[17], $stat); //pag kuha sa status nga label
                                             // $status    = '<br><strong style="color:red;">'.$stat[1].'</strong>';

                                            $tbl .= '<tr>
                                                             <td style="'.$row_style.'text-align:center;">'.$data[0].'</td>
                                                             <td style="'.$row_style.'text-align:center;height: 30px;">'.$data[1].'</td>
                                                             <td style="'.$row_style.'text-align:center;">'.$data[2].'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$data[3].'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$data[4].'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$data[5].'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$data[6].'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$data[9].'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$data[10].'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$data[13].'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$data[14].'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$data[15].'</td>                                                                                                                          
                                                             <td style="'.$row_style.'text-align:right;">'.$pending_qty.'</td>
                                                             <td style="'.$row_style.'text-align:right;">'.$suggested_qty.$overstock.$status.'</td>
                                                     </tr>';
                                         }

                                          
                                         if($page_counter == 1) 
                                         {                                              
                                             $total_lines = 9;                                                
                                         }
                                         else
                                         {
                                             $total_lines = 11;
                                         }
                                        



                                         if($line_counter == $total_lines)        
                                         {
                                             $tbl .= '</table>';    
                                             $tbl .= '<br><table><tr><td align="center">Page '.$this->ppdf->getAliasNumPage().' of '.$this->ppdf->getAliasNbPages().'</td></tr></table>';  
                                             //echo $tbl;                        
                                             $this->ppdf->writeHTML($tbl, true, false, false, false, '');  
                                             $this->ppdf->AddPage("L");
                                             $tbl = '' ;

                                              if ($key != $lastIndex)  //if dili pa ni mao ang last loop, printan siya ug header table kay naa pamay row i display
                                              {
                                                      
                                                     $tbl .= '
                                                                <table cellspacing="1" cellpadding="1" border="'.$border.'"   >
                                                                    <tr style="'.$table_header_style.'">';   

                                                                 $print= false;   
                                                                 $indx = 0;
                                                                 for($b=0;$b<count($tableHeader);$b++)
                                                                 {
                                                                      if($tableHeader[ $b] == 'ITEM NO')
                                                                      {
                                                                         $print = true; 
                                                                      }

                                                                      if($print)
                                                                      {
                                                                         $tbl .= '<th style="'.$header_style.'width:'.$width_arr[$indx].'px;">'.$tableHeader[$b].'</th>';
                                                                         $indx += 1;
                                                                      }

                                                                      if($tableHeader[ $b] == 'SUGGESTED REORDER QTY')
                                                                      {
                                                                          $print = false;   
                                                                      }
                                                                 }                                                      

                                                      $tbl .= '       </tr>';                                              

                                               }

                                             $line_counter=1;  
                                             $page_counter+=1;    
                                         }
                                         else 
                                         {
                                            $line_counter+=1;
                                         }


                                         // if($loop>20)
                                         // {
                                         //    break;
                                         //    $loop=0;
                                         // }        
                                         // else 
                                         // {
                                         //    $loop +=1;
                                         // }
                                   }               

           $tbl .= '</table>';          

           $po_calendar = $this->Mms_mod->get_po_calendar($head['supplier_code']);

           if(
                ($head['value_'] == 'cdc' && $po_calendar[0]['approver'] == 'Category-Head' && in_array($head['status'],array('Approved by-buyer','Approved by-category-head','Approved by-corp-manager')) )  ||
                ($head['value_'] == 'cdc' && $po_calendar[0]['approver'] == 'Corp-Manager'  && in_array($head['status'],array('Approved by-category-head','Approved by-corp-manager')))   ||
                ($head['value_'] != 'cdc' && $head[0]['status'] == 'Approved by-category-head') ||
                ($head['value_'] != 'cdc' && in_array($head['status'],array('Approved by-category-head','Approved by-corp-buyer'))) //if store ni nga reorder
             )   
             {
                    $show = true;
             }
             else 
             {
                    $show  = false;
             }

             $get_user_list = $this->Mms_mod->get_user_connection('');


             foreach($get_user_list as $usr)
             {
                 if($usr['user_type'] == 'corp-manager')
                 {                    
                     $emp_id      = $usr['emp_id'];
                     $emp_details = $this->Mms_mod->get_emp_details($emp_id);
                     $name        = $emp_details[0]['name'];
                     $position    = $emp_details[0]['position'];
                     $signature   = $usr['signature'];
                 }
             }

             $get_incorp = $this->Mms_mod->get_emp_details('02240-2014'); //si 2A ni Uy, Lolito 
             
             

           $tbl .= '
                      
                     <table border="0" style="text-align:center;">
                            <tr>
                                <th style="width:200px;"></th>
                                <th style="width:400px;"></th>
                                <th style="width:200px;"></th>                                
                            </tr>
                            <tr>
                                 <td>';
                             if($show)
                             {
                                  $tbl.=  '<img src="'.base_url().$signature.'" style="width:50px;height:50px;">';
                             }    
                                 

           $tbl .= '            </td>  
                                <td></td>
                                 <td></td>
                            </tr>
                            <tr>
                                 <td style=" border: none;border-bottom: 1px solid black;">'.$name.'</td>   
                                 <td></td>   
                                 <td style=" border: none;border-bottom: 1px solid black;">'.$get_incorp[0]['name'].'</td>   
                            </tr>
                            <tr>
                                 <td><b>'.$position.'</b></td>   
                                 <td></td>   
                                 <td><b>'.$get_incorp[0]['position'].'</b></td>   
                            </tr>
                     </table>
                   ';
           $tbl .= '<br><table><tr><td align="center">Page '.$this->ppdf->getAliasNumPage().' of '.$this->ppdf->getAliasNbPages().'</td></tr></table>';   
         
          $this->ppdf->writeHTML($tbl, true, false, false, false, '');           

          ob_end_clean();
          $this->ppdf->Output();

         
   }


   // herbert added code 8/29/2023..................................................................

   function reorder_table_view_server_side()
   {
    $start         = $this->input->post('start'); 
    $length        = $this->input->post('length'); 
    $searchValue   = $this->input->post('search')['value']; 
   
    $query = $this->db->select('*')
                      ->from('reorder_po as po')
                      ->join('reorder_store as str', 'po.store_id = str.store_id', 'inner')
                      ->group_start()
                      ->like('str.store_id', $searchValue)
                      ->or_like('document_number', $searchValue)
                      ->or_like('name', $searchValue)
                      ->or_like('status', $searchValue)
                      ->or_like('requested_by', $searchValue)
                      ->or_like('remarks_by', $searchValue)
                      ->group_end();

                      $this->db->limit($length, $start);
                      $query = $this->db->get();

     $totalRecords = $this->db->count_all('reorder_po');
             $data = array(
                           'draw'            => $this->input->post('draw'), 
                           'recordsTotal'    => $totalRecords,
                           'recordsFiltered' => $totalRecords,
                           'data'            => $query->result()
                          );

                    echo json_encode($data);    
   }
    
       
   

}