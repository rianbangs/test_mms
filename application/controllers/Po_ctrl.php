<?php

class Po_ctrl extends CI_Controller
{
      function __construct(){
         parent::__construct();
         $this->load->model('Po_mod');
         $this->load->model('Acct_mod');
      }

      // Dashboard
      function countSeasonPo(){
         $user_details = $this->Acct_mod->retrieveUserDetails();
         $count_pending = 0;   
         $count_approved = 0;

         if($user_details["store_id"]!=6 && $user_details["user_type"]=="buyer"){ // If Store Buyer
            $count_pending = $this->Po_mod->retrieveSeasonReorderBatchByUser("disapproved",true)[0]["count_"];
            $count_approved = $this->Po_mod->retrieveSeasonReorderBatchByUser("approved",true)[0]["count_"];
         }else if($user_details["user_type"]=="category-head"){ // If Category Head
            $count_pending = $this->Po_mod->retrieveSeasonReorderBatchByCategoryHead($user_details["store_id"],"disapproved",true)[0]["count_"];     
            $count_approved = $this->Po_mod->retrieveSeasonReorderBatchByCategoryHead($user_details["store_id"],"approved",true)[0]["count_"];     
         }else if($user_details["store_id"]==6 && $user_details["user_type"]=="buyer"){ // If CDC Buyer
            $count_pending = $this->Po_mod->retrieveSeasonReorderBatchByCdcSelf("disapproved",true)[0]["count_"];
            $count_approved = $this->Po_mod->retrieveSeasonReorderBatchByCdcSelf("approved",true)[0]["count_"];
            $count_pending += $this->Po_mod->retrieveSeasonReorderBatchByCdcBuyer("disapproved",true)[0]["count_"];
            $count_approved += $this->Po_mod->retrieveSeasonReorderBatchByCdcBuyer("approved",true)[0]["count_"];
         }else if($user_details["user_type"]=="corp-manager"){
            $count_pending = $this->Po_mod->retrieveSeasonReorderBatchByCorpManager("disapproved",true)[0]["count_"];
            $count_approved = $this->Po_mod->retrieveSeasonReorderBatchByCorpManager("approved",true)[0]["count_"];
         }

         echo json_encode(array("pending"=>$count_pending,"approved"=>$count_approved));
      }

      // P.O Calendar
      public function generateCalendar(){
         $year = $this->input->post("year");
         $month = $this->input->post("month");
         $prefs['template'] = '

         {table_open}<table id="calendar_tbl">{/table_open}

         {heading_row_start}<tr class="calendar_head">{/heading_row_start}

         {heading_previous_cell}<th><a href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
         {heading_title_cell}<th colspan="{colspan}" style="font-weight: bold;">{heading} 
         <span id="tbl_loader"></span></th>{/heading_title_cell}
         {heading_next_cell}<th><a href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}

         {heading_row_end}</tr>{/heading_row_end}

         {week_row_start}<tr class="calendar_head" style="text-align: center; font-weight: bold;">{/week_row_start}
         {week_day_cell}<td>{week_day}</td>{/week_day_cell}
         {week_row_end}</tr>{/week_row_end}

         {cal_row_start}<tr>{/cal_row_start}
         {cal_cell_start}<td class="tdcell" style="vertical-align: top;">{/cal_cell_start}
         {cal_cell_start_today}<td class="tcells" style="vertical-align: top;">{/cal_cell_start_today}
         {cal_cell_start_other}<td class="other-month">{/cal_cell_start_other}

         {cal_cell_content}<a href="{content}">{day}</a>{/cal_cell_content}
         {cal_cell_content_today}<div class="highlight"><a href="{content}">{day}</a></div>{/cal_cell_content_today}

         {cal_cell_no_content}<p style="font-weight: bold;">{day}</p>
         <div class="cells" id="c_{day}"></div>
         {/cal_cell_no_content}
         {cal_cell_no_content_today}<p style="font-weight: bold;">{day}</p>
         <div class="cells" id="c_{day}"></div>
         {/cal_cell_no_content_today}

         {cal_cell_blank}&nbsp;{/cal_cell_blank}

         {cal_cell_other}{day}{/cal_cel_other}

         {cal_cell_end}</td>{/cal_cell_end}
         {cal_cell_end_today}</td>{/cal_cell_end_today}
         {cal_cell_end_other}</td>{/cal_cell_end_other}
         {cal_row_end}</tr>{/cal_row_end}

         {table_close}</table>{/table_close}';

         $prefs['show_next_prev']  = FALSE;
         $prefs['day_type'] = "long";
         $prefs['start_day'] = 'monday';

         $this->load->library('calendar', $prefs);
      
         $calendar = $this->calendar->generate($year,$month);
         
         echo $calendar;
      }

      private function getLatestDateForCal($po_details){
         $dateEntries = explode('|', $po_details);
         $latestDate = null;
         $latestFreq = null;

         foreach ($dateEntries as $entry) {
             list($date, $value) = explode(':', $entry);
             if ($latestDate === null || $date > $latestDate) {
                 $latestDate = $date;
                 $latestFreq = $value;
             }
         }

         return array($latestDate,$latestFreq);
      }

      function getCalendarListItems(){
         $year = $this->input->post("year");
         $month = $this->input->post("month");
         $vendor = $this->input->post("vendor"); // group code

         $last_day = date('t', strtotime($year."-".$month."-01"));
         $first_date = date('Y-m-d', strtotime($year."-".$month."-01"));
         $last_date = date('Y-m-d', strtotime($year."-".$month."-".$last_day));
         $date1 = new DateTime($first_date);
         $date2 = new DateTime($last_date);

         $list = $this->Po_mod->retrievePoCalendar($last_date,$vendor);
         $result = array();

         foreach($list as $item){   
            $v_code = $item["no_"];
            $v_name = $item["name_"];
            $start_date = $item["start_date"];
            $end_date = $item["end_date"];
            $freq = $item["frequency"];
   
            $starting_date = new DateTime($start_date);
            
            while($starting_date <= $date2 /*&& $freq!=0*/){

               if($freq=='0'){
                  $filed = $this->countDateTagBatch($v_code,$starting_date,$vendor);
                  $result[] = array("vendor_code" => $v_code, "vendor_name" => $v_name, "start_date" => $starting_date->format("Y-m-d"), "frequency" => $freq, "filed" => $filed);
                  break;
               }

               if($starting_date >= new DateTime($end_date) && $end_date!=null){
                  break;
               }

               if($starting_date < $date1){ // Add days to date
                  $starting_date->modify('+'.$freq.' days');
               }else{
                  $filed = $this->countDateTagBatch($v_code,$starting_date,$vendor);
                  $result[] = array("vendor_code" => $v_code, "vendor_name" => $v_name, "start_date" => $starting_date->format("Y-m-d"), "frequency" => $freq, "filed" => $filed);
                  $starting_date->modify('+'.$freq.' days');
               }   
            }
            
         }

         echo json_encode($result);
      }

      private function countDateTagBatch($v_code,$starting_date,$vendor){
         $batch = $this->Po_mod->getReorderBatch($v_code,$starting_date->format("Y-m-d"),true);
         $user_details = $this->Acct_mod->retrieveUserDetails();

         if($user_details["user_type"]=="buyer" && $user_details["store_id"]==6) // If CDC User
            $batch = $this->Po_mod->getReorderBatches($v_code,$starting_date->format("Y-m-d"),$vendor,0,true);
         else if($user_details["user_type"]=="category-head")
            $batch = $this->Po_mod->getReorderBatches($v_code,$starting_date->format("Y-m-d"),$vendor,1,true);
         else if($user_details["user_type"]=="corp-manager")
            $batch = $this->Po_mod->getReorderBatches($v_code,$starting_date->format("Y-m-d"),$vendor,2,true);

         return ($batch>0);
      }


      public function breakLines(){
         if(isset($_FILES['file_select']) && isset($_POST["group_code"])) {
            $group_code = $_POST["group_code"];
            $fileName   = $_FILES['file_select']['tmp_name'];   
            $fileType   = pathinfo($_FILES['file_select']['name'], PATHINFO_EXTENSION); // Get the file extension               
            $file       = fopen($fileName,"r") or exit("Unable to open file!");
            $fileRead   = fread($file, filesize($fileName));
            $allowTypes = array("txt");
            $insert_batch = array();

            if(in_array($fileType,$allowTypes)){
               $break_eol = explode(PHP_EOL,str_replace(array('<', '>'),'',$fileRead)); // Array of EOL
               
               for($c=0; $c<count($break_eol); $c++){
                  // New Code: 77 Lines
                  $break_del = explode("|",$break_eol[$c]); // Array of |
                 
                  if(count($break_del)==77 || count($break_del)==76){ // If Break Array index equal to 77 or 76 (SM:77, SOD:76)
                     //Cleaned Variables        
                     $no_                       = $break_del[0];
                     $name_                     = $break_del[1];
                     $address_                  = $break_del[2];
                     $address_2_                = $break_del[3];
                     $city_                     = $break_del[4];
                     $contact_                  = $break_del[5];
                     $phone_no_                 = $break_del[6];
                     $telex_no_                 = $break_del[7];
                     $account_no_               = $break_del[8];
                     $territory_code_           = $break_del[9];
                     $dimension_code_1_         = $break_del[10];
                     $dimension_code_2_         = $break_del[11];
                     $budgeted_amt_             = $break_del[12];
                     $posting_group_            = $break_del[13];
                     $currency_code_            = $break_del[14];
                     $language_code_            = $break_del[15];
                     $statistics_group_         = $break_del[16];
                     $payment_terms_code_       = $break_del[17];
                     $charge_terms_code_        = $break_del[18];
                     $purchaser_code_           = $break_del[19];
                     $shipment_method_code_     = $break_del[20];
                     $shipping_agent_code_      = $break_del[21];
                     $invoice_disc_code_        = $break_del[22];
                     $country_code_             = $break_del[23];
                     $comment_                  = $break_del[24];
                     $blocked_                  = $break_del[25];
                     $pay_to_vendor_no_         = $break_del[26];
                     $priority_                 = $break_del[27];
                     $payment_method_code_      = $break_del[28];
                     $last_date_modified_       = $break_del[29];
                     $application_method_       = $break_del[30];
                     $prices_including_vat_     = $break_del[31];
                     $fax_no_                   = $break_del[32];
                     $telex_answer_back_        = $break_del[33];
                     $vat_registration_no_      = $break_del[34];
                     $gen_bus_posting_group_    = $break_del[35];
                     $post_code_                = $break_del[36];
                     $county_                   = $break_del[37];
                     $e_mail_                   = $break_del[38];
                     $home_page_                = $break_del[39];
                     $no_series_                = $break_del[40];
                     $tax_area_code_            = $break_del[41];
                     $tax_liable_               = $break_del[42];
                     $vat_bus_posting_group_    = $break_del[43];
                     $currency_filter_          = $break_del[44];
                     $primary_contact_no_       = $break_del[45];
                     $responsibility_center_    = $break_del[46];
                     $location_code_            = $break_del[47];
                     $lead_time_calculation_    = $break_del[48];
                     $queue_priority_           = $break_del[49];
                     $base_calendar_code_       = $break_del[50];
                     $bus_posting_group_        = $break_del[51];
                     $registration_id_          = $break_del[52];
                     $vendor_type_              = $break_del[53];
                     $header_company_name_      = $break_del[54];
                     $header_company_address_   = $break_del[55];
                     $contact_no_1_             = $break_del[56];
                     $contact_no_2_             = $break_del[57];
                     $tin_no_                   = $break_del[58];

                     if(count($break_del)==77){
                        $vendor_tin_               = $break_del[59];
                        $supplier_sorting_code_    = $break_del[60];
                        $otdl_                     = $break_del[61];
                        $buffer_                   = $break_del[62];
                        $freq_of_po_               = $break_del[63];
                        $active_                   = $break_del[64];
                        $invoice_to_               = $break_del[65];
                        $distributor_              = $break_del[66];
                        $principal_1_              = $break_del[67];
                        $principal_2_              = $break_del[68];
                        $principal_3_              = $break_del[69];
                        $principal_4_              = $break_del[70];
                        $principal_5_              = $break_del[71];
                        $principal_6_              = $break_del[72];
                        $principal_7_              = $break_del[73];
                        $principal_8_              = $break_del[74];
                        $principal_9_              = $break_del[75];
                        $principal_10_             = $break_del[76];
                     }else{
                        $vendor_tin_               = '';
                        $supplier_sorting_code_    = $break_del[59];
                        $otdl_                     = $break_del[60];
                        $buffer_                   = $break_del[61];
                        $freq_of_po_               = $break_del[62];
                        $active_                   = $break_del[63];
                        $invoice_to_               = $break_del[64];
                        $distributor_              = $break_del[65];
                        $principal_1_              = $break_del[66];
                        $principal_2_              = $break_del[67];
                        $principal_3_              = $break_del[68];
                        $principal_4_              = $break_del[69];
                        $principal_5_              = $break_del[70];
                        $principal_6_              = $break_del[71];
                        $principal_7_              = $break_del[72];
                        $principal_8_              = $break_del[73];
                        $principal_9_              = $break_del[74];
                        $principal_10_             = $break_del[75];
                     }

                     $insert_array["group_code"]                  = $group_code;
                     $insert_array["no_"]                         = $no_;
                     $insert_array["name_"]                       = $name_;
                     $insert_array["address"]                     = $address_;
                     $insert_array["address_2"]                   = $address_2_;
                     $insert_array["city"]                        = $city_;
                     $insert_array["contact"]                     = $contact_;
                     $insert_array["phone_no"]                    = $phone_no_;
                     $insert_array["telex_no"]                    = $telex_no_;
                     $insert_array["account_no"]                  = $account_no_;
                     $insert_array["territory_code"]              = $territory_code_ ;
                     $insert_array["dimension_code_1"]            = $dimension_code_1_;
                     $insert_array["dimension_code_2"]            = $dimension_code_2_;
                     $insert_array["budgeted_amt"]                = $budgeted_amt_;
                     $insert_array["posting_grp"]                 = $posting_group_;
                     $insert_array["currency_code"]               = $currency_code_;
                     $insert_array["language_code"]               = $language_code_;
                     $insert_array["statistics_group"]            = $statistics_group_;
                     $insert_array["payment_terms_code"]          = $payment_terms_code_;
                     $insert_array["charge_terms_code"]           = $charge_terms_code_;
                     $insert_array["purchaser_code"]              = $purchaser_code_;
                     $insert_array["shipment_method_code"]        = $shipment_method_code_;
                     $insert_array["shipping_agent_code"]         = $shipping_agent_code_;
                     $insert_array["invoice_disc_code"]           = $invoice_disc_code_;
                     $insert_array["country_code"]                = $country_code_;
                     $insert_array["comment"]                     = $comment_;
                     $insert_array["blocked"]                     = $blocked_;
                     $insert_array["pay_to_vendor_no"]            = $pay_to_vendor_no_;
                     $insert_array["priority"]                    = $priority_;
                     $insert_array["payment_method_code"]         = $payment_method_code_;
                     $insert_array["last_date_modified"]          = $last_date_modified_;
                     $insert_array["application_method"]          = $application_method_;
                     $insert_array["prices_including_vat"]        = $prices_including_vat_;
                     $insert_array["fax_no"]                      = $fax_no_;
                     $insert_array["telex_answer_back"]           = $telex_answer_back_;
                     $insert_array["vat_registration_no"]         = $vat_registration_no_;
                     $insert_array["gen_bus_posting_group"]       = $gen_bus_posting_group_;
                     $insert_array["post_code"]                   = $post_code_;
                     $insert_array["county"]                      = $county_;
                     $insert_array["e_mail"]                      = $e_mail_;
                     $insert_array["home_page"]                   = $home_page_;
                     $insert_array["no_series"]                   = $no_series_;
                     $insert_array["tax_area_code"]               = $tax_area_code_;
                     $insert_array["tax_liable"]                  = $tax_liable_;
                     $insert_array["vat_bus_posting_group"]       = $vat_bus_posting_group_;
                     $insert_array["currency_filter"]             = $currency_filter_;
                     $insert_array["primary_contact_no"]          = $primary_contact_no_;
                     $insert_array["responsibility_center"]       = $responsibility_center_;
                     $insert_array["location_code"]               = $location_code_;
                     $insert_array["lead_time_calculation"]       = $lead_time_calculation_;
                     $insert_array["queue_priority"]              = $queue_priority_;
                     $insert_array["base_calendar_code"]          = $base_calendar_code_;
                     $insert_array["bus_posting_group"]           = $bus_posting_group_;
                     $insert_array["registration_id"]             = $registration_id_;
                     $insert_array["vendor_type"]                 = $vendor_type_;
                     $insert_array["po_header_company_name"]      = $header_company_name_;
                     $insert_array["po_header_company_address"]   = $header_company_address_;
                     $insert_array["contact_no_1"]                = $contact_no_1_;
                     $insert_array["contact_no_2"]                = $contact_no_2_;
                     $insert_array["tin_no"]                      = $tin_no_;
                     $insert_array["vendor_tin"]                  = $vendor_tin_;
                     $insert_array["supplier_sorting_code"]       = $supplier_sorting_code_;
                     $insert_array["otdl"]                        = $otdl_;
                     $insert_array["buffer"]                      = $buffer_;
                     $insert_array["frequency"]                   = $freq_of_po_; 
                     $insert_array["active"]                      = $active_;
                     $insert_array["invoice_to"]                  = $invoice_to_;
                     $insert_array["distributor"]                 = $distributor_;
                     $insert_array["principal_1"]                 = $principal_1_;
                     $insert_array["principal_2"]                 = $principal_2_;
                     $insert_array["principal_3"]                 = $principal_3_;
                     $insert_array["principal_4"]                 = $principal_4_;
                     $insert_array["principal_5"]                 = $principal_5_;
                     $insert_array["principal_6"]                 = $principal_6_;
                     $insert_array["principal_7"]                 = $principal_7_;
                     $insert_array["principal_8"]                 = $principal_8_;
                     $insert_array["principal_9"]                 = $principal_9_;
                     $insert_array["principal_10"]                = $principal_10_;
                     
                     $insert_batch[] = $insert_array;
                     
                     
                  }
            
               } // For loop End

               if(count($insert_batch)>0){
                  echo json_encode(array("success",$insert_batch));

               }else
                  echo json_encode(array("error","No Data Extracted!"));

            }else
               echo json_encode(array("error","Invalid File Type!"));

         }else
            echo json_encode(array("error","No File Selected!"));
      }
     

      public function uploadCalendar(){
         // Retrieve the raw POST data
         $data = $_POST['upload_json'];

         // Decode the JSON data into an associative array
         $upload_json = json_decode($data, true);

         // Check if the decoding was successful
         if ($upload_json === null) {
            // JSON decoding failed
            http_response_code(400); // Bad Request
            echo json_encode(array("error","Invalid JSON data"));

         } else {
   
            $valid_count = 0;
            $errors = array();

            foreach ($upload_json as $entry_) {
               
               if(empty($entry_["start_date"])){ // First
                  $valid = false;
                  $errors[] = "Vendor Code: ".$entry_["no_"]." is not Inputted!"; 
               }else{
               
                  $max_date = $this->Po_mod->getMaxDate($entry_["no_"]); // Latest PO Date
                  if(empty($max_date)){
                     $valid = true;
                     $valid_count++;
                  }else{
                     $date1 = new DateTime($entry_["start_date"]);
                     $date2 = new DateTime($max_date);

                     if ($date1 <= $date2) {
                        $valid = false;
                        $errors[] = "Vendor Code: ".$entry_["no_"]." has an invalid Start Date!";
                     } else {
                        $valid = true;
                        $valid_count++;
                     }
                  }
               }

               if($valid){ // Add to array if valid
                  
                  $entry["no_"]                         = $entry_["no_"];   
                  $entry["name_"]                       = $entry_["name_"];
                  $entry["address"]                     = $entry_["address"];
                  $entry["address_2"]                   = $entry_["address_2"];
                  $entry["city"]                        = $entry_["city"];
                  $entry["contact"]                     = $entry_["contact"];
                  $entry["phone_no"]                    = $entry_["phone_no"];
                  $entry["telex_no"]                    = $entry_["telex_no"];
                  $entry["account_no"]                  = $entry_["account_no"];
                  $entry["territory_code"]              = $entry_["territory_code"];
                  $entry["dimension_code_1"]            = $entry_["dimension_code_1"];
                  $entry["dimension_code_2"]            = $entry_["dimension_code_2"];
                  $entry["budgeted_amt"]                = $entry_["budgeted_amt"];
                  $entry["posting_grp"]                 = $entry_["posting_grp"];
                  $entry["currency_code"]               = $entry_["currency_code"];
                  $entry["language_code"]               = $entry_["language_code"];
                  $entry["statistics_group"]            = $entry_["statistics_group"];
                  $entry["payment_terms_code"]          = $entry_["payment_terms_code"];
                  $entry["charge_terms_code"]           = $entry_["charge_terms_code"];
                  $entry["purchaser_code"]              = $entry_["purchaser_code"];
                  $entry["shipment_method_code"]        = $entry_["shipment_method_code"];
                  $entry["shipping_agent_code"]         = $entry_["shipping_agent_code"];
                  $entry["invoice_disc_code"]           = $entry_["invoice_disc_code"];
                  $entry["country_code"]                = $entry_["country_code"];
                  $entry["comment"]                     = $entry_["comment"];
                  $entry["blocked"]                     = $entry_["blocked"];
                  $entry["pay_to_vendor_no"]            = $entry_["pay_to_vendor_no"];
                  $entry["priority"]                    = $entry_["priority"];
                  $entry["payment_method_code"]         = $entry_["payment_method_code"];
                  $entry["last_date_modified"]          = $entry_["last_date_modified"];
                  $entry["application_method"]          = $entry_["application_method"];
                  $entry["prices_including_vat"]        = $entry_["prices_including_vat"];
                  $entry["fax_no"]                      = $entry_["fax_no"];
                  $entry["telex_answer_back"]           = $entry_["telex_answer_back"];
                  $entry["vat_registration_no"]         = $entry_["vat_registration_no"];
                  $entry["gen_bus_posting_group"]       = $entry_["gen_bus_posting_group"];
                  $entry["post_code"]                   = $entry_["post_code"];
                  $entry["county"]                      = $entry_["county"];
                  $entry["e_mail"]                      = $entry_["e_mail"];
                  $entry["home_page"]                   = $entry_["home_page"];
                  $entry["no_series"]                   = $entry_["no_series"];
                  $entry["tax_area_code"]               = $entry_["tax_area_code"];
                  $entry["tax_liable"]                  = $entry_["tax_liable"];
                  $entry["vat_bus_posting_group"]       = $entry_["vat_bus_posting_group"];
                  $entry["currency_filter"]             = $entry_["currency_filter"];
                  $entry["primary_contact_no"]          = $entry_["primary_contact_no"] ;
                  $entry["responsibility_center"]       = $entry_["responsibility_center"];
                  $entry["location_code"]               = $entry_["location_code"];
                  $entry["lead_time_calculation"]       = $entry_["lead_time_calculation"];
                  $entry["queue_priority"]              = $entry_["queue_priority"];
                  $entry["base_calendar_code"]          = $entry_["base_calendar_code"];
                  $entry["bus_posting_group"]           = $entry_["bus_posting_group"];
                  $entry["registration_id"]             = $entry_["registration_id"];
                  $entry["vendor_type"]                 = $entry_["vendor_type"];
                  $entry["po_header_company_name"]      = $entry_["po_header_company_name"];
                  $entry["po_header_company_address"]   = $entry_["po_header_company_address"];
                  $entry["contact_no_1"]                = $entry_["contact_no_1"];
                  $entry["contact_no_2"]                = $entry_["contact_no_2"];
                  $entry["tin_no"]                      = $entry_["tin_no"];
                  $entry["vendor_tin"]                  = $entry_["vendor_tin"];
                  $entry["supplier_sorting_code"]       = $entry_["supplier_sorting_code"];
                  $entry["otdl"]                        = $entry_["otdl"];
                  $entry["buffer"]                      = $entry_["buffer"];
                  $entry["active"]                      = $entry_["active"];
                  $entry["invoice_to"]                  = $entry_["invoice_to"];
                  $entry["distributor"]                 = $entry_["distributor"];
                  $entry["principal_1"]                 = $entry_["principal_1"];
                  $entry["principal_2"]                 = $entry_["principal_2"];
                  $entry["principal_3"]                 = $entry_["principal_3"];
                  $entry["principal_4"]                 = $entry_["principal_4"];
                  $entry["principal_5"]                 = $entry_["principal_5"];
                  $entry["principal_6"]                 = $entry_["principal_6"];
                  $entry["principal_7"]                 = $entry_["principal_7"];
                  $entry["principal_8"]                 = $entry_["principal_8"];
                  $entry["principal_9"]                 = $entry_["principal_9"];
                  $entry["principal_10"]                = $entry_["principal_10"];
                  

                  $po_id = $this->Po_mod->getPoID($entry["no_"]);
                  $null_count = $this->Po_mod->getPoCountNullById($po_id);

                  if($null_count>0){
                     $where["po_id"] = $po_id;
                     $this->Po_mod->updateTable("po_calendar",$entry,$where);
                  }

                  if($po_id==0){
                     $po_id = $this->Po_mod->insertToTable("po_calendar",$entry);
                  }else{
                     $where_["pd_id"] = $this->Po_mod->getLastPo($po_id)["pd_id"];
                     $edate = new DateTime($entry_["start_date"]);  
                     $edate->modify("-1 day");
                     $update_end["end_date"] = $edate->format("Y-m-d"); 
                     $this->Po_mod->updateTable("po_date",$update_end,$where_);
                  }

                  $entry_sub["group_code"]              = $entry_["group_code"]; // PO Date
                  $entry_sub["frequency"]               = $entry_["frequency"]; // PO Date
                  $entry_sub["start_date"]              = $entry_["start_date"];  // PO Date
                  $entry_sub["po_id"]                   = $po_id;
                  $this->Po_mod->insertToTable("po_date",$entry_sub);
                  

               } // If Valid end
           
            } // Foreach End

            echo json_encode(array($valid_count,$errors));
         }
      }

      // public function getPoItem(){
      //    if (!empty($_POST)) {
      //       $vendor_code = $_POST['vendor_code'];
      //       $date_tag = $_POST['date_tag'];
            
      //       $date1 = new DateTime($date_tag);
      //       $date2 = new DateTime(date("Y-m-d"));

      //       $interval = $date1->diff($date2);
      //       $dayDifference = $interval->days;
            
      //       $batch = $this->Po_mod->getReorderBatch($vendor_code,$date_tag);
      //       if(count($batch)>0){
      //          echo json_encode($batch);
      //       }else if($date1>$date2 && $dayDifference>3){
      //          echo json_encode(array("early","Early for ".$dayDifference." days."));
      //       }else if($date1<$date2 && $dayDifference>3){
      //          echo json_encode(array("late","Late for ".$dayDifference." days."));
      //       }else{
      //          echo json_encode($batch);
      //       }
            
      //    }
      // }

      public function getPoDayDiff(){
         if (!empty($_POST)) {
            $date_tag = $_POST['date_tag'];
            
            $date1 = new DateTime($date_tag);
            $date2 = new DateTime(date("Y-m-d"));

            $interval = $date1->diff($date2);
            $dayDifference = $interval->days;
            
            if($date1>$date2 && $dayDifference>3){
               echo json_encode(array("early","Early for ".$dayDifference." days."));
            }else if($date1<$date2 && $dayDifference>3){
               echo json_encode(array("late","Late for ".$dayDifference." days."));
            }else{
               echo json_encode(array("success"));
            }
            
         }
      }

      public function listBuyersUnderPO(){ 
         if (!empty($_POST)) {
            $vendor_code = $_POST['vendor_code'];
            $date_tag = $_POST['date_tag'];
            $group_code = $_POST['group_code'];

            $user_details = $this->Acct_mod->retrieveUserDetails();
            $cmd_ind = 0;
            if($user_details["user_type"]=="category-head")
               $cmd_ind = 1;
            else if($user_details["user_type"]=="corp-manager")
               $cmd_ind = 2;
            else if($user_details["user_type"]=="buyer" && $user_details["store_id"]!=6)
               $cmd_ind = 3;
            
            $buyers = $this->Po_mod->getReorderBatches($vendor_code,$date_tag,$group_code,$cmd_ind);
            echo json_encode($buyers);

         }
      }

      public function mkey_approve(){
         if (!empty($_POST)) {
            $group_code = $_POST["group_code"];
            $user = $_POST["username"];
            $pass = $_POST["password"];

            $store_id = $this->Acct_mod->retrieveUserDetails()["store_id"];
            $list = $this->Po_mod->retrieveManagerKeys($group_code,$store_id);
            $found = false;   
            
            foreach($list as $item){
               $m_user = $item["m_user"];
               $m_pass = $item["m_pass"];
               if($user==$m_user && password_verify($pass,$m_pass)){
                  $found = true;
                  break;
               }
            }

            echo json_encode(array($found,$list));

         }
      }

      private function getLatestDateForVendor($po_details){
         $dateEntries = explode('|', $po_details);
         $latestDate = null;
         $latestFreq = null;
         $latestGrp = null;

         foreach ($dateEntries as $entry) {
            list($start_date,$frequency,$group_code) = explode(':', $entry);
            if ($latestDate === null || $start_date > $latestDate) {
               $latestDate = $start_date;
               $latestFreq = $frequency;
               $latestGrp = $group_code;
            }
         }

         return array($latestDate,$latestFreq,$latestGrp);
      }

      public function listVendors(){ // Super-Admin
         $list = $this->Po_mod->retrieveVendor();
         foreach($list as &$vendor){
            list($start_date,$frequency,$group_code) = $this->getLatestDateForVendor($vendor["po_details"]);
            $vendor["group_code"] = $group_code; 
            $vendor["frequency"] = $frequency;

            $vend_type = $this->Po_mod->getVendorTypeFromCas($vendor["no_"]);
            if($vend_type==="DR & SI")
               $vend_type = "SI,DR";

            if($vendor["vend_type"] !== $vend_type){
               $vendor["vend_type"] = $vend_type;
               $this->Po_mod->updateTable("po_calendar", array("vend_type"=>$vend_type), array("po_id"=>$vendor["id_"])); 
            }
         }

         echo json_encode($list);
      }

      public function listPoDates(){ // Super-Admin
         if (!empty($_POST)) {
            $po_id = $_POST["po_id"];
            $list = $this->Po_mod->getPoDates($po_id);
            foreach($list as &$po){
               $po["start_date"] = date("F d, Y", strtotime($po["start_date"]));
               $po["end_date"] = ($po["end_date"]==null) ? "" : date("F d, Y", strtotime($po["end_date"]));
            }

            echo json_encode($list);
         }
      }

      public function setVendorApprover(){
         if (!empty($_POST)) {
            $po_id = $_POST["po_id"];
            $approver = $_POST["approver"];
            $this->Po_mod->updateTable("po_calendar",array("approver"=>$approver),array("po_id"=>$po_id));
         }
      }

      
      // Seasonal Items
      function listSeasonalItems(){ // Dept-Admin, Buyer
         $list = $this->Po_mod->getSeasonalItems();
         echo json_encode($list);
      }

      function seasonTypesByItemId(){ // Dept-Admin, Buyer
         if(!empty($_POST)){
            $item_id = $_POST["item_id"];

            $season_list = $this->Po_mod->getSeasonTypesDirect();
            $this_list = $this->Po_mod->getSeasonTypesOfItem($item_id);
            $item_detail = $this->Po_mod->getSeasonalItemDetailById($item_id);
            // $uom_list = $this->Po_mod->getItemPurchUoms($item_detail["item_no"]);
            $uom_list = $this->Po_mod->getUomsFromNav($item_detail["item_no"]);
            $variant_list = $this->Po_mod->getVariantsFromNav($item_detail["item_no"]);

            $variant = "";
            if(count($variant_list)>0)
               $variant = $variant_list[0];

            foreach ($uom_list as &$uom) {
               $unit_prices = $this->Po_mod->getUnitPricesFromNav($item_detail["item_no"],$uom["uom"],$variant); // Unit Price, Unit Price Including VAT
               $barcode = $this->Po_mod->getBarcodeFromNav($item_detail["item_no"],$uom["uom"],$variant);
               $uom["price"] = $unit_prices["unit_price"];
               $uom["price_vat"] = $unit_prices["unit_price_vat"];
               $uom["barcode"] = $barcode;
            }

            $result = array();
            $result[] = $season_list;
            $result[] = $this_list;
            $result[] = $uom_list;
            $result[] = $item_detail;
            $result[] = $variant_list;

            echo json_encode($result);
         }
      }

      function setUpVariant(){
         if(!empty($_POST)){
            $item_id = $_POST["item_id"];
            $variant = $_POST["variant"];

            $item_detail = $this->Po_mod->getSeasonalItemDetailById($item_id);
            $uom_list = $this->Po_mod->getUomsFromNav($item_detail["item_no"]);
            $variant_list = $this->Po_mod->getVariantsFromNav($item_detail["item_no"]);

            foreach ($uom_list as &$uom) {
               $unit_prices = $this->Po_mod->getUnitPricesFromNav($item_detail["item_no"],$uom["uom"],$variant); // Unit Price, Unit Price Including VAT
               $barcode = $this->Po_mod->getBarcodeFromNav($item_detail["item_no"],$uom["uom"],$variant);
               $uom["price"] = $unit_prices["unit_price"];
               $uom["price_vat"] = $unit_prices["unit_price_vat"];
               $uom["barcode"] = $barcode;
            }

            echo json_encode(array($uom_list,$item_detail));
         }
      }

      function setUpItemsForUpload(){ // Dept-Admin, Buyer
         if(isset($_FILES['file_select'])) {
            $fileName   = $_FILES['file_select']['tmp_name'];   
            $fileType   = pathinfo($_FILES['file_select']['name'], PATHINFO_EXTENSION); // Get the file extension               
            $file       = fopen($fileName,"r") or exit("Unable to open file!");
            $fileRead   = fread($file, filesize($fileName));
            $allowTypes = array("txt");

            if(in_array($fileType,$allowTypes)){
               $break_eol = explode(PHP_EOL,str_replace('"','',$fileRead)); // Array of EOL
               $upload_batch = array();

               for($c=0; $c<count($break_eol); $c++){
                  $break_del = explode("|",$break_eol[$c]); // Array of |
                 
                  if(count($break_del)==7){ // If Break Array index equal to 7
      
                     $item_no = $break_del[0];
                     $item_desc = $break_del[1];
                     $vcode = $break_del[2];

                     $existing = $this->Po_mod->getCountOfSeasonalItem($vcode,$item_no);
                     
                     if($existing==0){ // Add to array if not existing
                        $upload_array["item_no"]         = $item_no;
                        $upload_array["item_desc"]       = $item_desc;
                        $upload_array["vendor_code"]     = $vcode;
                        $upload_batch[]                  = $upload_array;
                     }
                          
                  }
            
               } // For loop End

               if(count($upload_batch)>0){
                  $season_types = $this->Po_mod->getSeasonTypesDirect();   
                  echo json_encode(array("success",$upload_batch,$season_types));
               }else
                  echo json_encode(array("error","No Data Extracted!"));
                  
               
            }else
               echo json_encode(array("error","Invalid File Type!"));

         }else
            echo json_encode(array("error","No File Selected!"));
      }

      function uploadSeasonalItems(){ // Dept-Admin, Buyer
         if(!empty($_POST)){
            $values = $_POST["checkboxes"]; // Array of String to be separated by *
            $count_insert = 0;

            for($c=0; $c<count($values); $c++){
               $split = explode("*",$values[$c]);
               $type_id = $split[0];
               $vendor_code = $split[1];
               $item_no = $split[2];
               $item_desc = $split[3];
               
               $id = $this->Po_mod->getIdOfSeasonalItem($vendor_code,$item_no);
               
               if($id==0){
                  $insert_item["vendor_code"] = $vendor_code;
                  $insert_item["item_no"] = $item_no;
                  $insert_item["item_desc"] = $item_desc;
                  // $insert_item["purch_uom"] = $this->Po_mod->getItemPurchUom($item_no);
                  $insert_item["purch_uom"] = $this->Po_mod->getTopUomFromNav($item_no)["uom"];
                  $id = $this->Po_mod->insertToTable("seasonal_item",$insert_item);
               }

               $count_tag = $this->Po_mod->getCountOfTagItem($id,$type_id);

               if($count_tag==0){
                  $insert_tag["item_id"] = $id;
                  $insert_tag["type_id"] = $type_id;
                  $count_insert++;

                  $this->Po_mod->insertToTable("seasonal_item_tag",$insert_tag);
               }
               
               
            } // For Loop End

            if($count_insert>0)
               echo json_encode(array("success", $count_insert." Item/s Tagged and Uploaded!"));
            else
               echo json_encode(array("error", "No Items Tagged and Uploaded!"));
      
            
         }
      }

      function saveSeasonOnItem(){ // Dept-Admin, Buyer
         if(!empty($_POST)){
            $item_id = $_POST["item_id"];
            $type_id = $_POST["type_id"];
            $is_checked = $_POST["is_checked"];

            $count_tag = $this->Po_mod->getCountOfTagItem($item_id,$type_id);
            
            if($count_tag==0){ // If no entry
               $insert_tag["item_id"] = $item_id;
               $insert_tag["type_id"] = $type_id;
               $this->Po_mod->insertToTable("seasonal_item_tag",$insert_tag);
            }else{
               $update_tag["is_active"] = ($is_checked=='true') ? "yes" : "no";
               $where_tag["item_id"] = $item_id;
               $where_tag["type_id"] = $type_id;
               $this->Po_mod->updateTable("seasonal_item_tag",$update_tag,$where_tag);
            }


         }
      }

      function savePurchUom(){ // Dept-Admin, Buyer
         if(!empty($_POST)){
            $item_id = $_POST["item_id"];
            $purch_uom = $_POST['purch_uom'];

            $update_tag["purch_uom"] = $purch_uom;
            $where_tag["item_id"] = $item_id;
            $this->Po_mod->updateTable("seasonal_item",$update_tag,$where_tag);            
         }
      }

      public function getSeasonDetails(){
         if(!empty($_POST)){
            $id = $_POST['type_id'];
            $row = $this->Po_mod->getSeasonTypesDirectById($id);
            echo json_encode($row);
         }
      }


      // Season Reorder
      public function getParametersForSeasonSales(){ // Buyer
         $result = array();

         $store_id = $this->Acct_mod->retrieveUserDetails()["store_id"];
         
         $store_list  = $this->Po_mod->getStoreList($store_id); 
         $season_list = $this->Po_mod->getSeasonTypesDirect();
         $vendor_list = $this->Po_mod->getSeasonalVendorsByUser();

         $result["store_list"]  = $store_list;
         $result["season_list"] = $season_list;
         $result["vendor_list"] = $vendor_list;

         echo json_encode($result);

      }

      public function generate_excel(){ // Corp Buyer - Forecast
         if(!empty($_POST)){
            $batch_id = $_POST["batch_id"];

            $batch_details = $this->Po_mod->retrieveSeasonReorderBatchById($batch_id);
            $batch_headers = $this->Po_mod->getForecastHeaders($batch_id);
            $batch_lines = $this->Po_mod->getForecastLines($batch_id,$batch_details["percentage"]);

            $years = explode(",",$batch_headers["years"]);
            $months = explode(",",$batch_headers["months"]);
            $stores = explode(",",$batch_headers["stores"]); // ICM, Plaza Marcela

            $year_now = date('Y');
            $years[] = $year_now; // Add the current year
            
            sort($years);
            sort($months);

            $months_abbr = $this->getMonthsBetween($year_now,$months[0],$months[count($months)-1],false);
            $months_full = $this->getMonthsBetween($year_now,$months[0],$months[count($months)-1],true);
            $store_span = count($years)*count($months); // gets number of span for a store column
            $headers = array("item_no","item_desc"); // Basis for plotting
            $total_bottom = array();
            
            header("content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=Forecast.xls");

            // Top Header
            $tbl =   '<table>
                     <tr><td colspan="2"><h3><b><u>'.$batch_details["vendor_code"].' - '.$batch_details["vendor_name"].
                     '</u></b></h3></td></tr>
                     <tr><td><b>FORECAST</b></td></tr>
                     <tr><td colspan="2"><b>FOR THE MONTH OF '.strtoupper($months_full[0]).' TO '.
                     strtoupper($months_full[count($months_full)-1]).' '.$year_now.'</b></td></tr>
                     </table>';
            
            // Table Header
            $tbl .= '<table border="1">
                     <tr>
                     <td rowspan="3" align="center"><b>ITEM CODE</b></td>
                     <td rowspan="3" align="center"><b>DESCRIPTION</b></td>';
            
            for($c=0; $c<count($stores); $c++){
               $tbl .= '<td colspan="'.$store_span.'" align="center"><b>'.strtoupper($stores[$c]).'</b></td>';
            }

            // Total Forecast Column
            $tbl .= '<td colspan="'.count($months).'" rowspan="2" align="center"><b>TOTAL FORECAST '.$year_now.'</b></td>';
            $tbl .= '</tr><tr>';

            for($x=0; $x<count($stores); $x++){
               for($c=0; $c<count($years); $c++){
                  $year_label = ($c==count($years)-1)? $batch_details["season"] : "Sales"; 
                  $tbl .= '<td colspan="'.count($months).'" align="center"><b>'.$years[$c].' '.$year_label.'</b></td>';
               }            
            }

            $tbl .= '</tr><tr>';
            
            for($z=0; $z<count($stores); $z++){
               for($x=0; $x<count($years); $x++){
                  for($c=0; $c<count($months_abbr); $c++){
                     $tbl .= '<td align="center"><b>'.$months_abbr[$c].'</b></td>';
                     $headers[] = $stores[$z].'_'.$years[$x].'_'.$months[$c];
                  }            
               }

               if($z==count($stores)-1){ // If last index
                  for($c=0; $c<count($months_abbr); $c++){
                     $tbl .= '<td align="center"><b>'.$months_abbr[$c].'</b></td>';
                     $headers[] = 'total_'.$months[$c];
                  }
               }
            }

            $tbl .= '</tr>';

            // Table Rows
            foreach($batch_lines as $item){
               $tbl .= '<tr>';

               for($c=0; $c<count($headers); $c++){
                  $val_ = (isset($item[$headers[$c]])) ? $item[$headers[$c]] : '0'; 
                  // $tbl .= '<td>'.$headers[$c].'-'.$val_.'</td>';
                  $tbl .= '<td>'.$val_.'</td>';

                  if($headers[$c]!="item_no" && $headers[$c]!="item_desc"){
                     if(isset($total_bottom[$headers[$c]])){ // Store total bottom on array
                        $total_bottom[$headers[$c]] += $val_;
                     }else{
                        $total_bottom[$headers[$c]] = $val_;  
                     } 
                  }          
               }
                        
               $tbl .= '</tr>';
            }

            $tbl .= '<tr><td colspan="2" align="center"><b>TOTAL</b></td>';
            // print_r($total_bottom);
            foreach($total_bottom as $key => $val){ // Total Bottom
               $tbl .= '<td>'.$val.'</td>';
            }

            $tbl .= '</tr>';

            $tbl .= '</table>';

            echo $tbl;

         }
      }

      private function getMonthsBetween($year,$m1,$m2,$is_full){
         $m_format = ($is_full) ? 'F' : 'M';
         $month1 = $year.'-'.$m1; // format: YYYY-MM
         $month2 = $year.'-'.$m2;

         $date1 = new DateTime($month1);
         $date2 = new DateTime($month2);
         $date2->add(new DateInterval('P1M'));

         $interval = new DateInterval('P1M');
         $period = new DatePeriod($date1, $interval, $date2);

         $months = array();

         foreach ($period as $date) {
            $months[] = $date->format($m_format);
         }

         return $months;
      }

      private function getDocumentNo($batch_details){
         $store_acro = strtoupper($this->Po_mod->getStore($batch_details["store_id"])["value_"]);
         return "MMSS-".$store_acro.'-'.str_pad($batch_details["batch_id"],7,"0",STR_PAD_LEFT);
      }

      public function listSeasonReorders(){
         if(!empty($_POST)){
            $opt = $_POST["opt"]; // pending or approved
            $link = $_POST["link"]; // cdc or store
            $user_details = $this->Acct_mod->retrieveUserDetails();

            if($user_details["store_id"]!=6 && $user_details["user_type"]=="buyer") // If Store Buyer
               $list = $this->Po_mod->retrieveSeasonReorderBatchByUser($opt);
            else if($user_details["user_type"]=="category-head") // If Category Head
               $list = $this->Po_mod->retrieveSeasonReorderBatchByCategoryHead($user_details["store_id"],$opt);
            else if($user_details["store_id"]==6 && $user_details["user_type"]=="buyer"){ // If CDC Buyer
               if($link=='cdc') // CDC's Own Reorders
                  $list = $this->Po_mod->retrieveSeasonReorderBatchByCdcSelf($opt);
               else // All Stores' Reorders
                  $list = $this->Po_mod->retrieveSeasonReorderBatchByCdcBuyer($opt);
            }else if($user_details["user_type"]=="corp-manager")
               $list = $this->Po_mod->retrieveSeasonReorderBatchByCorpManager($opt);
            else if($user_details["user_type"]=="incorporator")
               $list = $this->Po_mod->retrieveSeasonReorderBatchByIncorporator($opt);

            $result = array();
            foreach($list as $item_){
               $doc_no = $this->getDocumentNo($item_);

               $item["doc_no"] = $doc_no;
               $item["batch_id"] = $item_["batch_id"];
               $item["season"] = $item_["season"];
               $item["vendor_code"] = $item_["vendor_code"];
               $item["vendor_name"] = $item_["vendor_name"];
               $item["date_generated"] = date("F d, Y -- h:i a", strtotime($item_["date_generated"]));
               $item["group_code"] = $item_["group_code"];
               $item["status"] = strtoupper($item_["status"]);
               $item["nav_si_doc"] = $item_["nav_si_doc"];
               $item["nav_dr_doc"] = $item_["nav_dr_doc"];
               $item["vend_type"] = $this->Po_mod->getVendorTypeByNo($item_["vendor_code"]);

               $result[] = $item;
            }
            
            echo json_encode($result);
         }
      }

      private function extractFromSelectedFile($store_count,$vendor_code){
         if(empty($_FILES)) {
            return array("error","No File Selected!","");
         }else if(count($_FILES)!=$store_count){
            return array("error","Incomplete Selection of Files!",count($_FILES)." ".$store_count);
         }else{
            
            $ext_arr = array(); // HTML
            $ext_arr_1 = array(); // TXT

            foreach($_FILES as $fileKey => $fileData){ // fileKey => posted name, fileData => file properties

               $fileSelect = explode('_',$fileKey);
               $fileName   = $fileData['tmp_name'];   
               $fileType   = pathinfo($fileData['name'], PATHINFO_EXTENSION); // Get the file extension               
               $file       = fopen($fileName,"r") or exit("Unable to open file!");
               $fileRead   = fread($file, filesize($fileName));
               
               if(count($fileSelect)==3){ // HTML - QTY ONHAND

                  $allowTypes = array("htm","html");

                  $extract["store_id"] = $this->Po_mod->getStoreID($fileSelect[2]);
                  $store_ = $this->Po_mod->getStore($extract["store_id"]);

                  if(in_array($fileType,$allowTypes)){
                     
                     $html_txt = strip_tags($fileRead);
                     // echo $html_txt;
                     
                     $extract["date_generated"] = '';

                     $report_type = "Re-order Report ACTUAL";
                     if(strpos($html_txt,$report_type)===false) 
                        return array("error","Invalid Re-order Report File For Store ".$store_["name"]."!",$fileKey);

                     $store_found = false;
                     $rr_header = explode("^",$store_["reorder_report_header"]);
                     foreach($rr_header as $rr){
                        if(strpos($html_txt,$rr)!==false) 
                           $store_found = true;
                     }

                     if($store_found==false) // strcasecmp($extract["store_name"],$store_name)!=0
                        return array("error","Invalid Store File For Store ".$store_["name"]."!",$fileKey);

                     if(strpos($html_txt,$vendor_code)===false) 
                        return array("error","Invalid Vendor File For Store ".$store_["name"]."!",$fileKey);
                           
                     if(preg_match('/ACTUAL QTY(.*?)&nbsp/s', $html_txt, $matches))
                        $extract["date_generated"] = date('Y-m-d',strtotime(trim($matches[1])));

                     $today = new DateTime(date('Y-m-d'));
                     $date_ext = new DateTime($extract["date_generated"]);

                     // $day_diff = $today->diff($date_ext)->days;
                     // if($day_diff>1)
                     //    return array("error","Date Generation of File is Only 1 Day Apart For ".$store_["name"]."!",$fileKey);

                     $row = array(); // Item Code, UOM, Variant Code, Qty Onhand
                     $temp_row = array();

                     $html_data = explode(PHP_EOL,$html_txt);
                     $except_data = array("","&nbsp","&nbsp&nbsp");
                     $item_code_sel = null;

                     foreach($html_data as $data_){
                        if (preg_match('/^\d{6}$/', $data_)) { // Item Code
                           
                           if(isset($temp_row_data)){
                              $temp_row[] = $temp_row_data;
                           }

                           $temp_row_data = array();
                           $temp_row_data[] = $data_;

                        } else if(preg_match('/^V\d{4}$/', $data_)){ // Variant Code

                           if(isset($temp_row_data)){
                              $temp_row[] = $temp_row_data;
                           }

                           $temp_row_data = array();
                           $temp_row_data[] = $item_code_sel;
                           $temp_row_data[] = $data_;
                           
                        } else {
                           if(isset($temp_row_data) && !in_array($data_,$except_data)){
                              if (stripos($data_, "variant") !== false){
                                 $item_code_sel = $temp_row_data[0];
                              }

                              if(count($temp_row_data)<=8)
                                 $temp_row_data[] = $data_;

                           }
                        }
                     } // Foreach End


                     $item_variant = array();

                     // Final Row Array
                     foreach($temp_row as $row_data_){
                        $valuesAtIndex = array_column($item_variant, 0);
                        $foundKey = array_search($row_data_[0], $valuesAtIndex);

                        if ($foundKey !== false){
                           $row[] = array($row_data_[0],$item_variant[$foundKey][1],$row_data_[1],$row_data_[8]);
                        }else{  
                           if (stripos($row_data_[1], "variant") === false){
                              $row[] = array($row_data_[0],$row_data_[2],'',$row_data_[8]);
                           }else{
                              $item_variant[] = array($row_data_[0],$row_data_[2]);
                           }

                        }
                     } // Foreach End

         
                     if(count($row)==0)
                        return array("error","No Data Entries Found for Store ".$store_["name"]."!", $fileKey);
                     
                     $extract["rows"] = $row;
                     $ext_arr[] = $extract;

                  }else
                     return array("error","Invalid File Type(Qty On Hand) for Store ".$store_["name"]."!", $fileKey);
               
               } else { // TXT - PENDING PO

                  $allowTypes = array("txt");

                  $extract_1["store_id"] = $this->Po_mod->getStoreID($fileSelect[2]);
                  $store_ = $this->Po_mod->getStore($extract_1["store_id"]);
                  $loc_code = $store_["location_code"];
                  
                  $extract_1["rows"] = array();

                  if(in_array($fileType,$allowTypes)){
                     $breakLines = explode(PHP_EOL, str_replace('"','',$fileRead));
                     $header = array();
                     for($c=0; $c<count($breakLines); $c++){
                        $breakCol = explode(",",$breakLines[$c]);

                        if(count($breakCol)==5){ // Header
                           if($breakCol[0]==$vendor_code && strpos($breakCol[1],"SMGM")!==false && $breakCol[4]==$loc_code){         
                              $hCol['vendor_code'] = $breakCol[0];
                              $hCol['document_no'] = $breakCol[1];
                              $hCol['po_date'] = date('Y-m-d', strtotime($breakCol[2]));
                              $header[] = $hCol;
                           }
                        }

                        if(count($breakCol)==8 && count($header)>0){
                           
                           foreach($header as $hCol){
                              
                              if($breakCol[0]==$hCol['document_no'] && $breakCol[2]==$hCol['vendor_code']){
                                 $lCol["document_no"] = $hCol['document_no'];
                                 $lCol["vendor_code"] = $hCol['vendor_code'];
                                 $lCol["po_date"] = $hCol['po_date'];
                                 $lCol["item_code"] = $breakCol[3];
                                 $lCol["uom"] = $breakCol[4];
                                 $lCol["pending_qty"] = $breakCol[6];
                                 $extract_1["rows"][] = $lCol;
                                 break;
                              }
                           }
                           
                        }
                        
                     } // For End

                     if(count($extract_1["rows"])<1)
                        return array("error","No Data Found in Txt File For Store ".$store_["name"]."!",$fileKey);

                     $ext_arr_1[] = $extract_1;
                  }else
                     return array("error","Invalid File Type(Pending PO) for Store ".$store_["name"]."!", $fileKey);
               }
                  
            } // Foreach End
            
            return array("success",$ext_arr,$ext_arr_1);
         }   
            
           
      }

      private function getPreviousMonths3(){
         $ym_now = date("Y-m");
         $prev_months_3 = date("Y-m-01 00:00:00.000", strtotime("-3 months", strtotime($ym_now)));
         $prev_month = date("Y-m-d 00:00:00.000"); // Present Day
         $pm["start_date"] = $prev_months_3;
         $pm["end_date"] = $prev_month;
         return $pm;
      }

      public function seasonProgress(){
         // $store_id = 6;
         // $season = 2;
         // $years = array("2022","2021","2020");
         // $stores = array('ICM-S0001','ASC-S0001','PM-S0001'); // 'ICM-S0001','ASC-S0001'
         // $vendor_code = 'S4686';

         if(!empty($_POST)){
            // var_dump($_POST);
            $store_id = $_POST["store_id"];
            $season = $_POST["season_id"];
            $years = json_decode($_POST["years"],true);
            $stores = json_decode($_POST["stores"],true); // 'ICM-S0001','ASC-S0001'
            $vendor_code = $_POST["vendor_code"];
            $file_res = json_decode($_POST["file_res"],true);
            $list = json_decode($_POST["list"],true);
            $is_dist = $_POST["is_dist"]; // Distribution
         
            echo '<style>
                   .progress {
                     width: 100%;
                     height: 20px;
                     background-color: #f2f2f2;
                     border-radius: 10px;
                     overflow: hidden;
                   }

                   .progress-bar {
                     height: 100%;
                     background-color: #4caf50;
                     width: 0;
                     transition: width 0.5s ease-in-out;
                     position: relative;
                   }

                   .progress-text {
                     position: absolute;
                     left: 20px;
                     color: white;
                     font-weight: bold;
                     display: block;
                   }
            </style>

            <input type="hidden" id="progress-hidden">    
            <div class="progress"><div class="progress-bar" id="myProgressBar">
            <span class="progress-text" id="progressText">0%</span>
            </div></div>
            <p id="myProgressFile" style="font-style: italic;"></p>';

            // $po_res = $this->getPendingPoFromDirectory($stores,$vendor_code);
            // $progress = $po_res[0];
            // $dir_arr = $po_res[1];
            
            // if($store_id==6 && $is_dist=="true"){
            //    $booking_list = $this->getBookingDataFromSQL($po_res[0],$season,$years,$vendor_code);
            //    $progress = $booking_list[0];
            //    $list = array_merge($list,$booking_list[1]);
            // }

            // $this->insertSeasonReorderEntries($progress,$store_id,$season,$vendor_code,$list,$file_res,$dir_arr,$is_dist);

            $progress = 0;
            if($store_id==6 && $is_dist=="true"){
               $booking_list = $this->getBookingDataFromSQL($progress,$season,$years,$vendor_code);
               $progress = $booking_list[0];
               $list = array_merge($list,$booking_list[1]);
            }

            $this->insertSeasonReorderBatchLines($progress,$store_id,$season,$vendor_code,$list,$file_res,$is_dist);
         }
      }

      private function getPendingPoFromDirectory($stores,$vendor_code){
         $stores = $this->getDirStores($stores);
         $dir_arr = array();
         $prev_months_3 = $this->getPreviousMonths3();
         $progress = 0;
         
         $dir_details = array();

         for($c=0; $c<count($stores); $c++){
            $store_id      = $this->Po_mod->getStoreID($stores[$c]);
            $store_details = $this->Po_mod->getStore($store_id);
            
            $dir_arr[] = array("store_id" => $store_id, "dir_lines" => array());
            $get_dir = array();

            $poDbIds = array_column($dir_details, 'po_db_id');
            $key = array_search($store_details['po_db_id'], $poDbIds);
            
            if($key === false){ 
            
               $get_dir["po_db_id"] = $store_details['po_db_id'];
               $get_dir["store_id"][] = $store_id;
               $get_dir["file_extension"][] = $store_details['file_extension'];
               $dir_details[] = $get_dir;
            
            }else{
               
               $dir_details[$key]["store_id"][] = $store_id;
               $dir_details[$key]["file_extension"][] = $store_details['file_extension'];
    
            }
         }

         // var_dump($dir_details);
         // var_dump($dir_arr);

         $multiplier = 70 / (count($dir_details));

         $memory_limit = ini_get('memory_limit');
         ini_set('memory_limit',-1);
         ini_set('max_execution_time', 0);

         foreach($dir_details as $dir_row){

            $get_dir  = $this->Po_mod->getPoDir($dir_row['po_db_id']);

            $dir      = $get_dir['directory'];
            $dir      = str_replace('\\\\','\\\\',$dir);
            $dir      = str_replace('\\','\\',$dir);
            $username = $get_dir['username'];
            $password = $get_dir['password'];
             
            // use the 'net use' command to map the network drive with the specified credentials
            system("net use {$dir} /user:{$username} {$password} >nul");
    
            if (is_dir($dir)) {             
               // $po_arr["store_id"] = $store_id;

               // Get the list of files and directories in the directory
               // $entries = scandir($dir);
               $implode_ext = "\\*.{".implode(",", $dir_row['file_extension'])."}";
               $entries = glob($dir.$implode_ext, GLOB_BRACE);

               $filteredFiles = array_filter($entries, function ($entry) use ($prev_months_3){
                                 if(is_file($entry)){
                                    $date_modified = date("Y-m-d H:i:s", filemtime($entry));
      
                                    return ($date_modified <= $prev_months_3["end_date"] && $date_modified >= $prev_months_3["start_date"]);
                                 }   
                              }); 

               $divider = count($filteredFiles);

               if($divider>0){
                  $addend = $multiplier / $divider;
               
                  // iterate through each entry in the directory
                  foreach ($filteredFiles as $entry) {
                     // $sel_file = $dir . "\\" . $entry;
                     
                     $progress = $progress + $addend;
                     $progress_view = round($progress, 2);

                     echo '<script>';
                     echo 'document.getElementById("myProgressBar").style.width = "'.$progress.'%";';
                     echo 'document.getElementById("progressText").innerText = "'.$progress_view.' %";';
                     echo 'document.getElementById("myProgressFile").innerText = "Retrieving Data From Directory: '.$entry.'";';
                     echo '</script>';

                     ob_flush();
                     flush();
                     
                     if(is_file($entry)){
                        
                        // $file_time = filemtime($entry);
                        // $date_modified = date("Y-m-d H:i:s", $file_time);
                        $file_extension = pathinfo($entry, PATHINFO_EXTENSION);   
                        $dir_index = array_search($file_extension, $dir_row['file_extension']);
                        // check if the entry is a file with the correct file extension
                        
                        if ($dir_index !== false){ 
                        
                           // if($date_modified <= $prev_months_3["end_date"] && $date_modified >= $prev_months_3["start_date"]){   
                          
                              if (is_readable($entry)){
                                
                                 $header = array();
                                 $fh = fopen($entry,'r');
                                 while ($line = fgets($fh)) {

                                    $line     = str_replace('"','',$line);
                                    $line_exp = explode("|",$line); 

                                    if(count($line_exp) == 7) { // If header
                                       if($line_exp[5]==$vendor_code && strpos($line_exp[0],"SMGM")!==false)          
                                          $header[] = array('document_no'=>$line_exp[0],'date'=>date('Y-m-d', strtotime($line_exp[1])),'vendor'=>$line_exp[5]);

                                    }
                                                 
                                    if(count($line_exp) == 11 && count($header)>0) { // If lines                                        

                                       $storeIds = array_column($dir_arr, 'store_id');
                                       $key_ = array_search($dir_row['store_id'][$dir_index], $storeIds);

                                       $dir_line = array('document_no'=>$header[0]['document_no'],'date'=>$header[0]['date'],'vendor'=>$header[0]['vendor'],'item_code'=>$line_exp[1],'pending_qty'=>$line_exp[2],'uom'=>$line_exp[4]);    
                                       
                                       $dir_arr[$key_]["dir_lines"][] = $dir_line;
                                                                                         
                                    }  
                                    
                                 } //  While End
                              } // If is readable  
                           // } // If previous 3 months 
                        } // If correct file extension
                     } // If is file

                  } // Inner Foreach End  
               
               } else {// If divider

                  $progress = 70;
                  $progress_view = round(70, 2);

                  echo '<script>';
                  echo 'document.getElementById("myProgressBar").style.width = "'.$progress.'%";';
                  echo 'document.getElementById("progressText").innerText = "'.$progress_view.' %";';
                  echo 'document.getElementById("myProgressFile").innerText = "";';
                  echo '</script>';

                  ob_flush();
                  flush();

               } // Else End

            } else {
               // handle the error
               var_dump(array("error","Failed to open directory: {$dir}",""));

            }

            // Close the network connection
            system("net use {$dir} /delete >nul");
            
         } // Foreach End

         ini_set('memory_limit',$memory_limit );
         // var_dump($dir_arr);
         return array($progress,$dir_arr);
      }

      private function getBookingDataFromSQL($progress,$season,$years,$vendor_code){
         $list = array();
         $db_ids = $this->Po_mod->getDbDetailsNonStores();
         $addend = 50 / count($db_ids);
         for($c=0; $c<count($db_ids); $c++){
            $db_id = $db_ids[$c]["databse_id"];
            if(empty($list))
               $list = $this->Po_mod->getBookingDbFromMW($db_id,$season,$years,$vendor_code);
            else{
               $n_list = $this->Po_mod->getBookingDbFromMW($db_id,$season,$years,$vendor_code);
               $list = array_merge($list,$n_list);
            }

            $progress = $progress + $addend;
            $progress_view = round($progress, 2);

            echo '<script>';
            echo 'document.getElementById("myProgressBar").style.width = "'.$progress.'%";';
            echo 'document.getElementById("progressText").innerText = "'.$progress_view.' %";';
            echo 'document.getElementById("myProgressFile").innerText = "Retrieving Data From Booking Database: '.$db_ids[$c]["display_name"].'";';
            echo '</script>';

            ob_flush();
            flush();
         }

         // var_dump($list);
         return array($progress,$list);
      }

      private function insertSeasonReorderBatchLines($progress,$store_id,$season,$vendor_code,$list,$file_res,$is_dist){
         
         $dividend = ($store_id==6 && $is_dist=="true") ? 50 : 100;
         $addend = $dividend / count($list);

         $ext_arr = $file_res[1]; // Qty Onhand
         $ext_arr_1 = $file_res[2]; // Pending PO - PM-S0001,TAL-S0001
         
         $season_details = $this->Po_mod->getSeasonDetails($season);
         $vendor_details = $this->Po_mod->getVendorDetailsLast($vendor_code);
            
         $insert_batch["season"] = $season_details["season_name"];
         $insert_batch["vendor_code"] = $vendor_details["no_"];
         $insert_batch["vendor_name"] = $vendor_details["name_"];
         $insert_batch["date_generated"] = date("Y-m-d H:i:s");
         $insert_batch["group_code"] = $vendor_details["group_code"];
         $insert_batch["percentage"] = $season_details["percentage"];
         $insert_batch["store_id"] = $store_id;
         $insert_batch["user_id"] = $_SESSION['user_id'];
         
         $batch_id = $this->Po_mod->insertToTable("season_reorder_batch",$insert_batch);

         foreach($list as $item){

            $entry_id = $this->Po_mod->getIDSeasonReorderItemEntry($item["item_no"],$item["variant_code"],$batch_id);
            
            if($entry_id==0){
               $insert_entry["item_no"] = $item["item_no"];
               $insert_entry["item_desc"] = $item["item_desc"];
               $insert_entry["uom"] = $item["unit_of_measure"];
               $insert_entry["variant_code"] = $item["variant_code"];
               
               $unit_prices = $this->Po_mod->getUnitPricesFromNav($item["item_no"],$item["unit_of_measure"],$item["variant_code"]); // Unit Price, Unit Price Including VAT
               $prod_arr = $this->Po_mod->getItemProdFromNav($item["item_no"]); // Inventory, Gen. Prod, Vat Prod, WHT Prod Posting Group 
               $barcode = $this->Po_mod->getBarcodeFromNav($item["item_no"],$item["unit_of_measure"],$item["variant_code"]);

               $insert_entry["unit_price"] = $unit_prices["unit_price"];
               $insert_entry["unit_price_vat"] = $unit_prices["unit_price_vat"];
               $insert_entry["inventory_posting_grp"] = $prod_arr["inventory_posting_grp"];
               $insert_entry["gen_prod"] = $prod_arr["gen_prod"];
               $insert_entry["vat_prod"] = $prod_arr["vat_prod"];
               $insert_entry["wht_prod"] = $prod_arr["wht_prod"];
               $insert_entry["barcode"] = $barcode;
               $insert_entry["batch_id"] = $batch_id;
               $entry_id = $this->Po_mod->insertToTable("season_reorder_item_entry",$insert_entry);

            }// If End

            $store_entry_id = $this->Po_mod->getIDSeasonReorderStoreEntry($item["store_id"],$entry_id);
      
            if($store_entry_id==0){
               $insert_store_entry["qty_onhand"] = 0;
                
               foreach($ext_arr as $ext_){
                  if($item["store_id"]==$ext_["store_id"]){

                     $qty_onhands = $ext_["rows"];
                     for($c=0; $c<count($qty_onhands); $c++){
                        if($item["item_no"]==$qty_onhands[$c][0] && strcasecmp($item["unit_of_measure"],$qty_onhands[$c][1])==0 && strcasecmp($item["variant_code"],$qty_onhands[$c][2])==0){
                           $insert_store_entry["qty_onhand"] = $qty_onhands[$c][3];
                           break 2;
                        }

                     }
                  } 
               }

               $insert_store_entry["store_id"] = $item["store_id"];
               $insert_store_entry["entry_id"] = $entry_id;

               $store_entry_id = $this->Po_mod->insertToTable("season_reorder_store_entry",$insert_store_entry);
            }

            $insert_ref["year_ref"] = $item["year"];
            $insert_ref["month_ref"] = $item["month"];
            $insert_ref["amount"] = $item["total"];
            $insert_ref["store_entry_id"] = $store_entry_id;
            $this->Po_mod->insertToTable("season_reorder_reference",$insert_ref);

            $po_batch = array();
            // Pending Qty
            if(in_array($item["store_id"], array('1','2','3','4'))){ // ICM, ASC, PM, TAL

               $prev_months_3 = $this->getPreviousMonths3();
               $store_details = $this->Po_mod->getStore($item["store_id"]);
               $db_id = $store_details["databse_id"];

               if(in_array($item["store_id"], array('1','2'))){ // ICM, ASC,
                  $po_details = $this->Po_mod->getPendingQtyFromNav($vendor_code,$item["item_no"],$item["unit_of_measure"],$prev_months_3,$db_id);

                  foreach($po_details as $po_){
                     $count_po = $this->Po_mod->countPoByEntryIdAndDocNo($store_entry_id,$po_["document_no"]);
                     $exists = $this->checkPoInArray($po_batch,$po_["document_no"],$store_entry_id); 
                     if($count_po==0 && !$exists){
                        $insert_po["document_no"] = $po_["document_no"];
                        $insert_po["po_date"] = $po_["po_date"];
                        $insert_po["pending_qty"] = $po_["pending_qty"];
                        $insert_po["store_entry_id"] = $store_entry_id;
                        $po_batch[] = $insert_po;
                     }
                  }

               }else if(in_array($item["store_id"], array('3','4'))){ // PM, TAL
               
                  foreach($ext_arr_1 as $po){
                     if($item["store_id"]==$po["store_id"] && isset($po["rows"])){
                        
                        foreach($po["rows"] as $row_){
                           if($item["item_no"]==$row_["item_code"] && strcasecmp($item["unit_of_measure"],$row_["uom"])==0){
                              
                              $count_po = $this->Po_mod->countPoByEntryIdAndDocNo($store_entry_id,$row_["document_no"]);
                              $exists = $this->checkPoInArray($po_batch,$row_["document_no"],$store_entry_id); 
                              if($count_po==0 && !$exists){
                                 $insert_po["document_no"] = $row_["document_no"];
                                 $insert_po["po_date"] = $row_["po_date"];
                                 $insert_po["pending_qty"] = $row_["pending_qty"];
                                 $insert_po["store_entry_id"] = $store_entry_id;
                                 $po_batch[] = $insert_po;
                              }  
                              
                              $main_count++;
                           }
                        } // Foreach End
                     }
                  } // Foreach End

               }

               // Navigate to Middleware
               $mw_list = $this->Po_mod->getPendingPoFromMW($vendor_code,$item["item_no"],$item["unit_of_measure"],$prev_months_3,$db_id);
               foreach($mw_list as $mw_item){
                  $count_po = $this->Po_mod->countPoByEntryIdAndDocNo($store_entry_id,$mw_item["document_no"]);
                  $exists = $this->checkPoInArray($po_batch,$mw_item["document_no"],$store_entry_id); 
                  if($count_po==0 && !$exists){
                     $insert_po["document_no"] = $mw_item["document_no"];
                     $insert_po["po_date"] = $mw_item["date_"];
                     $insert_po["pending_qty"] = $mw_item["pending_qty"];
                     $insert_po["store_entry_id"] = $store_entry_id;
                     $po_batch[] = $insert_po;
                  }
               }

            } // If End - Pending Qty
               
            if(count($po_batch)>0)
               $this->Po_mod->insertBatchToTable("season_reorder_pending_qty",$po_batch);

            $progress = $progress + $addend;
            $progress_view = round($progress, 2);

            echo '<script>';
            echo 'document.getElementById("myProgressBar").style.width = "'.$progress.'%";';
            echo 'document.getElementById("progressText").innerText = "'.$progress_view.' %";';
            echo 'document.getElementById("myProgressFile").innerText = "Inserting Data To Inhouse Database: '.$item["item_no"].' - '.$item["item_desc"].' ('.$item["unit_of_measure"].')";';
            
            if(round($progress,0)>=100){
               echo 'document.getElementById("progressText").innerText = "100 %";';
               echo 'document.getElementById("myProgressFile").innerText = "Reorder Complete!";';

               $new_batch_details = $this->Po_mod->retrieveSeasonReorderBatchById($batch_id);
               $doc_no = $this->getDocumentNo($new_batch_details);
               
               echo 'document.getElementById("progress-hidden").value = "'.$batch_id.'|'.$doc_no.'";';

            }               

            echo '</script>';

            ob_flush();
            flush();

         }// Foreach End
         
      }

      private function checkPoInArray($po_batch,$document_no,$store_entry_id){
         $exists = false;

         foreach ($po_batch as $po) {
             if ($po['document_no'] == $document_no && $po['store_entry_id'] == $store_entry_id) {
                 $exists = true;
                 break; // Exit the loop once a match is found
             }
         }

         return $exists;
      }

      private function insertSeasonReorderEntries($progress,$store_id,$season,$vendor_code,$list,$file_res,$dir_arr,$is_dist){
         
         $dividend = ($store_id==6 && $is_dist=="true") ? 15 : 30;
         $addend = $dividend / count($list);

         $ext_arr = $file_res[1]; // Qty Onhand
         $ext_arr_1 = $file_res[2]; // Pending PO - PM-S0001,TAL-S0001
         
         $season_details = $this->Po_mod->getSeasonDetails($season);
         $vendor_details = $this->Po_mod->getVendorDetailsLast($vendor_code);
            
         $insert_batch["season"] = $season_details["season_name"];
         $insert_batch["vendor_code"] = $vendor_details["no_"];
         $insert_batch["vendor_name"] = $vendor_details["name_"];
         $insert_batch["date_generated"] = date("Y-m-d H:i:s");
         $insert_batch["group_code"] = $vendor_details["group_code"];
         $insert_batch["percentage"] = $season_details["percentage"];
         $insert_batch["store_id"] = $store_id;
         $insert_batch["user_id"] = $_SESSION['user_id'];
         
         $batch_id = $this->Po_mod->insertToTable("season_reorder_batch",$insert_batch);

         foreach($list as $item){

            $entry_id = $this->Po_mod->getIDSeasonReorderItemEntry($item["item_no"],$item["variant_code"],$batch_id);
            
            if($entry_id==0){
               $insert_entry["item_no"] = $item["item_no"];
               $insert_entry["item_desc"] = $item["item_desc"];
               $insert_entry["uom"] = $item["unit_of_measure"];
               $insert_entry["variant_code"] = $item["variant_code"];
               
               $unit_prices = $this->Po_mod->getUnitPricesFromNav($item["item_no"],$item["unit_of_measure"],$item["variant_code"]); // Unit Price, Unit Price Including VAT
               $prod_arr = $this->Po_mod->getItemProdFromNav($item["item_no"]); // Inventory, Gen. Prod, Vat Prod, WHT Prod Posting Group 
               $barcode = $this->Po_mod->getBarcodeFromNav($item["item_no"],$item["unit_of_measure"],$item["variant_code"]);

               $insert_entry["unit_price"] = $unit_prices["unit_price"];
               $insert_entry["unit_price_vat"] = $unit_prices["unit_price_vat"];
               $insert_entry["inventory_posting_grp"] = $prod_arr["inventory_posting_grp"];
               $insert_entry["gen_prod"] = $prod_arr["gen_prod"];
               $insert_entry["vat_prod"] = $prod_arr["vat_prod"];
               $insert_entry["wht_prod"] = $prod_arr["wht_prod"];
               $insert_entry["barcode"] = $barcode;
               $insert_entry["batch_id"] = $batch_id;
               $entry_id = $this->Po_mod->insertToTable("season_reorder_item_entry",$insert_entry);

            }// If End

            $store_entry_id = $this->Po_mod->getIDSeasonReorderStoreEntry($item["store_id"],$entry_id);
      
            if($store_entry_id==0){
               $insert_store_entry["qty_onhand"] = 0;
                
               foreach($ext_arr as $ext_){
                  if($item["store_id"]==$ext_["store_id"]){

                     $qty_onhands = $ext_["rows"];
                     for($c=0; $c<count($qty_onhands); $c++){
                        if($item["item_no"]==$qty_onhands[$c][0] && strcasecmp($item["unit_of_measure"],$qty_onhands[$c][1])==0 && strcasecmp($item["variant_code"],$qty_onhands[$c][2])==0){
                           $insert_store_entry["qty_onhand"] = $qty_onhands[$c][3];
                           break 2;
                        }

                     }
                  } 
               }

               $insert_store_entry["store_id"] = $item["store_id"];
               $insert_store_entry["entry_id"] = $entry_id;

               $store_entry_id = $this->Po_mod->insertToTable("season_reorder_store_entry",$insert_store_entry);
            }

            $insert_ref["year_ref"] = $item["year"];
            $insert_ref["month_ref"] = $item["month"];
            $insert_ref["amount"] = $item["total"];
            $insert_ref["store_entry_id"] = $store_entry_id;
            $this->Po_mod->insertToTable("season_reorder_reference",$insert_ref);

            $po_batch = array();
            // Pending Qty
            if(in_array($item["store_id"], array('1','2','3','4'))){ // ICM, ASC, PM, TAL

               $main_count = 0;

               if(in_array($item["store_id"], array('1','2'))){ // ICM, ASC,
                  $prev_months_3 = $this->getPreviousMonths3();
                  $store_details = $this->Po_mod->getStore($item["store_id"]);
                  $db_id = $store_details["databse_id"];

                  $po_details = $this->Po_mod->getPendingQtyFromNav($vendor_code,$item["item_no"],$item["unit_of_measure"],$prev_months_3,$db_id);

                  $main_count = count($po_details);

                  foreach($po_details as $po_){
                     $count_po = $this->Po_mod->countPoByEntryIdAndDocNo($store_entry_id,$po_["document_no"]);
                     if($count_po==0){
                        $insert_po["document_no"] = $po_["document_no"];
                        $insert_po["po_date"] = $po_["po_date"];
                        $insert_po["pending_qty"] = $po_["pending_qty"];
                        $insert_po["store_entry_id"] = $store_entry_id;
                        $po_batch[] = $insert_po;
                     }
                  }

               }else if(in_array($item["store_id"], array('3','4'))){ // PM, TAL
               
                  foreach($ext_arr_1 as $po){
                     if($item["store_id"]==$po["store_id"] && isset($po["rows"])){
                        
                        foreach($po["rows"] as $row_){
                           if($item["item_no"]==$row_["item_code"] && strcasecmp($item["unit_of_measure"],$row_["uom"])==0){
                              
                              $count_po = $this->Po_mod->countPoByEntryIdAndDocNo($store_entry_id,$row_["document_no"]);
                              if($count_po==0){
                                 $insert_po["document_no"] = $row_["document_no"];
                                 $insert_po["po_date"] = $row_["po_date"];
                                 $insert_po["pending_qty"] = $row_["pending_qty"];
                                 $insert_po["store_entry_id"] = $store_entry_id;
                                 $po_batch[] = $insert_po;
                              }  
                              
                              $main_count++;
                           }
                        } // Foreach End
                     }
                  } // Foreach End

               }

               if($main_count<1){ // Navigate to Directory
                  foreach($dir_arr as $dir_){  
                     if($item["store_id"]==$dir_["store_id"] && isset($dir_["dir_lines"])){
                        
                        foreach($dir_["dir_lines"] as $line_){ // document_no, date, vendor, item_code, pending_qty, uom
                           if($item["item_no"]==$line_["item_code"] && strcasecmp($item["unit_of_measure"],$line_["uom"])==0){
                              
                              $count_po = $this->Po_mod->countPoByEntryIdAndDocNo($store_entry_id,$line_["document_no"]);
                              if($count_po==0){
                                 $insert_po["document_no"] = $line_["document_no"];
                                 $insert_po["po_date"] = $line_["date"];
                                 $insert_po["pending_qty"] = $line_["pending_qty"];
                                 $insert_po["store_entry_id"] = $store_entry_id;
                                 $po_batch[] = $insert_po;
                              }
                           }
                        } // Foreach End
                     }
                  } // Foreach End

               }
               
            } // If End - Pending Qty
               
            if(count($po_batch)>0)
               $this->Po_mod->insertBatchToTable("season_reorder_pending_qty",$po_batch);

            $progress = $progress + $addend;
            $progress_view = round($progress, 2);

            echo '<script>';
            echo 'document.getElementById("myProgressBar").style.width = "'.$progress.'%";';
            echo 'document.getElementById("progressText").innerText = "'.$progress_view.' %";';
            echo 'document.getElementById("myProgressFile").innerText = "Inserting Data To Inhouse Database: '.$item["item_no"].' - '.$item["item_desc"].' ('.$item["unit_of_measure"].')";';
            
            if(round($progress,0)>=100){
               echo 'document.getElementById("progressText").innerText = "100 %";';
               echo 'document.getElementById("myProgressFile").innerText = "Reorder Complete!";';

               $new_batch_details = $this->Po_mod->retrieveSeasonReorderBatchById($batch_id);
               $doc_no = $this->getDocumentNo($new_batch_details);
               
               echo 'document.getElementById("progress-hidden").value = "'.$batch_id.'|'.$doc_no.'";';

            }               

            echo '</script>';

            ob_flush();
            flush();

         }// Foreach End
         
      }

      private function storeFileCount($stores){
         $count = 0;
         $two_files = array();//array("PM-S0001","TAL-S0001");
         for($c=0; $c<count($stores); $c++){
            if(in_array($stores[$c], $two_files))
               $count += 2;
            else
               $count += 1;
         }

         return $count;
      }

      private function getDirStores($stores){
         $dir_stores = array();
         $dir_stores_ = array("ICM-S0001","ASC-S0001","PM-S0001","TAL-S0001","ASC-S0015","PM-S0015");
         for($c=0; $c<count($stores); $c++){
            if(in_array($stores[$c], $dir_stores_))
               $dir_stores[] = $stores[$c];
           
         }

         return $dir_stores;
      }

      public function seasonFormSubmit(){ // CDC and Store Buyer
         if(!empty($_POST)){
            $is_dist = $_POST['is_dist']; // Distribution
            $stores = $_POST['stores']; // ICM-S0001,ASC-S0001,PM-S0001
            $season = $_POST['season'];
            $vendor = $_POST['vendor'];
            $years = $_POST['years']; // 2022,2021
            
            // Validations to add:
            // - Create season reorder on season months.
            // - Qty onhand from HTML must have same date generated or 1 day difference.
            // - Pending Qty from Textfile must be determined on store/dept.

            $vendor_code = explode("-",$vendor)[0]; // ex. S2975-VendorName

            $vend_type = $this->Po_mod->getVendorTypeFromCas($vendor_code);
            
            if($vend_type==='' || $vend_type===null){
               echo json_encode(array("error","Vendor has no existing type.",""));
               return;
            }

            $user_details = $this->Acct_mod->retrieveUserDetails();
            $store_id = $user_details["store_id"]; // User's Store ID

            $stores = explode(",",$stores); // ICM-S0001,ASC-S0001,PM-S0001
            $years = explode(",",$years);
            // $season_start = explode("-",$season_details["period_start"])[0];
            // $season_end = explode("-",$season_details["period_end"])[0];
            // $month_now = date("m");
            
            $stores_count = $this->storeFileCount($stores);
            $file_res = $this->extractFromSelectedFile($stores_count,$vendor_code);
            
            if($file_res[0]=="error"){
               echo json_encode(array("error",$file_res[1],$file_res[2]));
               return;
            }

            $list = $this->Po_mod->getRefYearSalesStores($stores,$season,$vendor_code,$years);

            if(count($list)<1){
               echo json_encode(array("error","No Data Found!",""));
            }else{
               echo json_encode(array("success",$store_id,$season,$years,$vendor_code,$stores,$file_res,$list,$is_dist));
            } // Else Success End
            
         }
      }

      private function getBookingStoreID($store_id){
         
         $booking_ids = $this->Po_mod->getStoreIdsByGrps("BOOKING");
         $ldi_ids = $this->Po_mod->getStoreIdsByGrps("LDI_DSG");
         $wdg_ids = array();
         if(in_array($store_id,$booking_ids))
            $id = $booking_ids[0];
         else if(in_array($store_id,$ldi_ids))
            $id = $ldi_ids[0];
         else if(in_array($store_id,$wdg_ids))
            $id = $wdg_ids[0];
         else
            $id = $store_id;

         return $id;
      }

      function listSeasonReorderEntries(){
         if(!empty($_POST)){

            $batch_id = $_POST["batch_id"];
            echo json_encode($this->viewSeasonReorderEntries($batch_id));

         }// End If
      }

      private function viewSeasonReorderEntries($batch_id){

         $batch_details = $this->Po_mod->retrieveSeasonReorderBatchById($batch_id);
            
         $batch_details["doc_no"] = $this->getDocumentNo($batch_details);
         $batch_details["date_generated"] = date("F d, Y -- h:i a", strtotime($batch_details["date_generated"]));
         $percentage = $batch_details["percentage"];

         $approver = $this->Po_mod->getVendorApprover($batch_details["vendor_code"]); // Category-Head, Corp-Manager
         $batch_details["approver"] = $approver;

         // New Code: 12/04/2023
         $vend_type = $this->Po_mod->getVendorTypeByNo($batch_details["vendor_code"]); // SI,DR
         $batch_details["vend_type"] = $vend_type;
         
         $headers = array();
         $header_keys = array();
         $result = array();

         $years = $this->Po_mod->getDistinctYears($batch_id); // Array
         $months = $this->Po_mod->getDistinctMonths($batch_id); // Array
         for($c=0; $c<count($years); $c++){
            for($x=0; $x<count($months); $x++){
               $headers["ym"][] = date("M", mktime(0, 0, 0, $months[$x], 1)).'_'.$years[$c];
               $header_keys["ym"][] = date("M", mktime(0, 0, 0, $months[$x], 1)).'_'.$years[$c];
            }
         }

         $parent_list = $this->listParentSeasonReorder($batch_id); // If child reorder, retrieve the entries of the parent reorder. 

         if($batch_details["store_id"]==6){ // Consolidated Store Reorders
            $stores = $this->Po_mod->getDistinctStores($batch_id); // Array
            $header_keys["compressed"] = array();

            foreach($stores as $store_){
               $headers["store"][] = strtoupper($store_["value_"]).'_';
               $header_keys["store"][] = $store_["store_id"];
               $get_store_id = $this->getBookingStoreID($store_["store_id"]);
               $get_store_name = $this->Po_mod->getGrpByStoreId($store_["store_id"]); 

               if(!in_array($get_store_id,$header_keys["compressed"])){
                  if($get_store_name=="SOD" || $get_store_name=="SM")
                     $headers["compressed"][] = strtoupper($store_["value_"]).'_';
                  else
                     $headers["compressed"][] = $get_store_name.'_';

                  $header_keys["compressed"][] = $get_store_id;
               }
            }
            
            $entry_list = $this->Po_mod->retrieveSeasonReorderBatchCdc($batch_id);

         }else{ // Store Reorder

            $entry_list = $this->Po_mod->retrieveSeasonReorderBatchStore($batch_id);
         }

         foreach($entry_list as $entry){
            $item_entry["entry_id"] = $entry["entry_id"];
            $item_entry["item_no"] = $entry["item_no"];
            $item_variant = (empty($entry["variant_code"])) ? "" : " (".$entry["variant_code"].")"; 
            $item_entry["item_desc"] = $entry["item_desc"].$item_variant;
            $item_entry["uom"] = $entry["uom"];
            
            if($batch_details["store_id"]==6){ // Consolidated Store Reorders
               
               $qty_split = explode("|",$entry["qty_onhand"]);
               $qty_onhand = 0;

               for($c=0; $c<count($qty_split); $c++){
                  $entry_split = explode(":", $qty_split[$c]); // 0 - store_id, 1 - qty_onhand
                  $qty_onhand += $entry_split[1];
               }

               $item_entry["qty_onhand"] = $qty_onhand;

               $sale_split = explode("|",$entry["sales"]);
               $store_qtys = array();
               $store_years = array();

               for($c=0; $c<count($sale_split); $c++){

                  $date_val_split = explode(":", $sale_split[$c]); // 0 - store_id, 1 - month_year, 2 - sale_qty
                  $month_year_split = explode("_", $date_val_split[1]); // 0 - month, 1 - year 
                  $year_ = $month_year_split[1];
                                   
                  if(isset($store_qtys[$date_val_split[0]]))
                     if(isset($date_val_split[2]))
                        $store_qtys[$date_val_split[0]] += $date_val_split[2];
                     else
                        $store_qtys[$date_val_split[0]] += 0;

                  else{
                     $store_qtys[$date_val_split[0]] = $date_val_split[2];
                     $store_years[$date_val_split[0]] = array();
                  }

                  if (!in_array($year_, $store_years[$date_val_split[0]])) {
                     $store_years[$date_val_split[0]][] = $year_;
                  }

               }

               $forecasted_qty = array();
               foreach ($store_qtys as $store_id => $qty_) {
                  $numberOfYears = count($store_years[$store_id]);
                  $ans_ = round(($qty_ / $numberOfYears)*(($percentage/100)+1),0);
                  $get_store_id = $this->getBookingStoreID($store_id);
                                    
                  if(isset($forecasted_qty[$get_store_id])){
                     if($qty_!=0)
                        $forecasted_qty[$get_store_id] += $ans_;
                  }else{
                     if($qty_==0)
                        $forecasted_qty[$get_store_id] = 0;
                     else
                        $forecasted_qty[$get_store_id] = $ans_;
                  }   
                     
                  $item_entry["sale_amts"] = $forecasted_qty;
               }

               $item_entry["forecast_qty"] = array_sum($forecasted_qty);
            
            }else{ // Store Reorders

               $item_entry["qty_onhand"] = $entry["qty_onhand"];
               
               $sale_split = explode("|",$entry["sales"]);
               $sale_amts = array();

               for($c=0; $c<count($sale_split); $c++){
                  $date_val_split = explode(":", $sale_split[$c]); // 0 - month_year, 1 - sale_amt
                  $month_year_split = explode("_", $date_val_split[0]); // 0 - month, 1 -year 
                  $head = date("M", mktime(0, 0, 0, $month_year_split[0], 1)).'_'.$month_year_split[1];
                  
                  if(isset($sale_amts[$head]))
                     $sale_amts[$head] += floatval($date_val_split[1]);
                  else
                     $sale_amts[$head] = floatval($date_val_split[1]);

                  $item_entry["sale_amts"] = $sale_amts; 
               }

               $no_years = count(explode("|",$entry["years"]));
               $total_amt = $entry["sum"];

               if($total_amt==0)
                  $item_entry["forecast_qty"] = 0;
               else
                  $item_entry["forecast_qty"] = round(($total_amt/$no_years)*(($percentage/100)+1),0);
            } // Else End
         
            $last_approved_qty = $this->Po_mod->getSeasonReorderChangeQtyLast($entry["entry_id"]);
            
            $item_entry["remaining"] = $item_entry["forecast_qty"]-$last_approved_qty;
            
            if(count($parent_list)>0){ // If child reorder, retrieves the remaining forecasted qty from the parent
               
               foreach($parent_list as $par){
                  if($item_entry["item_no"]==$par["item_no"]){
                     $item_entry["remaining"] = $par["remaining"]-$last_approved_qty;;
                     break;
                  }
               }
            }

            $item_entry["pending_qty"] = $this->Po_mod->getSeasonReorderPendingQty($entry["entry_id"]);
            
            $change_details = $this->Po_mod->getSeasonReorderChangeQtyHistoryLatest($entry["entry_id"]);

            if(isset($change_details)){
               if($change_details["status"]=="pending" || $change_details["status"]=="approved"){
                  $item_entry["reorder_qty"] = $change_details["adj_qty"];
                  $item_entry["reorder_qty_dr"] = $change_details["adj_qty_dr"];
               } else { // Disapproved
                  $item_entry["reorder_qty"] = $change_details["orig_qty"];
                  $item_entry["reorder_qty_dr"] = $change_details["orig_qty_dr"];
               }

            }else{

               if($vend_type=="SI,DR" || $vend_type=="SI"){
                  $item_entry["reorder_qty"] = $item_entry["remaining"]-$item_entry["qty_onhand"];
                  $item_entry["reorder_qty_dr"] = 0;
                  if($item_entry["reorder_qty"]<0)
                     $item_entry["reorder_qty"] = 0;

               }else{ // DR
                  $item_entry["reorder_qty"] = 0;
                  $item_entry["reorder_qty_dr"] = $item_entry["remaining"]-$item_entry["qty_onhand"];
                  if($item_entry["reorder_qty_dr"]<0)
                     $item_entry["reorder_qty_dr"] = 0;
               }
               

            }

            $item_entry["overstock"] = $item_entry["remaining"]-$item_entry["qty_onhand"];
            $item_entry["status"] = $this->Po_mod->getSeasonReorderEntryByAdjStatus($entry["entry_id"]);
            
            $result[] = $item_entry;
            
         }// End Foreach
         
         return array($batch_details,$headers,$header_keys,$result);
      }

      private function listParentSeasonReorder($batch_id){
         $result = array();
         
         $years = $this->Po_mod->getDistinctYears($batch_id); // Array
         $months = $this->Po_mod->getDistinctMonths($batch_id); // Array
         
         $batch_details = $this->Po_mod->retrieveSeasonReorderBatchParentById($batch_id);
         $parent_id = $batch_details["batch_id"];
         $percentage = $batch_details["percentage"];

         $child_count = $this->Po_mod->countSeasonReorderBatchChildById($batch_id);
         if($child_count>0)
            $parent_list = $this->listParentSeasonReorder($parent_id); // If parent is child reorder, retrieve the parent entries. 
         
         if($batch_details["store_id"]==6){
            $entry_list = $this->Po_mod->retrieveSeasonReorderBatchParentCdc($batch_id);
         }else{
            $entry_list = $this->Po_mod->retrieveSeasonReorderBatchParentStore($batch_id);
         }
            
         foreach($entry_list as $entry){
   
            $item_entry["item_no"] = $entry["item_no"];
            
            if($batch_details["store_id"]==6){ // Consolidated Store Reorders
               $qty_split = explode("|",$entry["qty_onhand"]);
               $qty_onhand = 0;

               for($c=0; $c<count($qty_split); $c++){
                  $entry_split = explode(":", $qty_split[$c]); // 0 - store_id, 1 - qty_onhand
                  $qty_onhand += $entry_split[1];
               }

               $item_entry["qty_onhand"] = $qty_onhand;

               $sale_split = explode("|",$entry["sales"]);
               $store_qtys = array();
               $store_years = array();

               for($c=0; $c<count($sale_split); $c++){
                  $date_val_split = explode(":", $sale_split[$c]); // 0 - store_id, 1 - month_year, 2 - sale_qty
                  $month_year_split = explode("_", $date_val_split[1]); // 0 - month, 1 - year 
                  $year_ = $month_year_split[1];
                  
                  if(isset($store_qtys[$date_val_split[0]]))
                     if(isset($date_val_split[2]))
                        $store_qtys[$date_val_split[0]] += $date_val_split[2];
                     else
                        $store_qtys[$date_val_split[0]] += 0;
                  else{
                     $store_qtys[$date_val_split[0]] = $date_val_split[2];
                     $store_years[$date_val_split[0]] = array();
                  }

                  if (!in_array($year_, $store_years[$date_val_split[0]])) {
                     $store_years[$date_val_split[0]][] = $year_;
                  }

               }

               $forecasted_qty = array();
               foreach ($store_qtys as $store_id => $qty_) {
                  $numberOfYears = count($store_years[$store_id]);
                  if($qty_==0)
                     $forecasted_qty[$store_id] = 0;
                  else
                     $forecasted_qty[$store_id] = round(($qty_ / $numberOfYears)*(($percentage/100)+1),0);

                  $item_entry["sale_amts"] = $forecasted_qty;
               }

               $item_entry["forecast_qty"] = array_sum($forecasted_qty);
            
            }else{ // Store Reorders

               $item_entry["qty_onhand"] = $entry["qty_onhand"];
               
               $sale_split = explode("|",$entry["sales"]);
               $sale_amts = array();

               for($c=0; $c<count($sale_split); $c++){
                  $date_val_split = explode(":", $sale_split[$c]); // 0 - month_year, 1 - sale_amt
                  $month_year_split = explode("_", $date_val_split[0]); // 0 - month, 1 -year 
                  $head = date("M", mktime(0, 0, 0, $month_year_split[0], 1)).'_'.$month_year_split[1];
                  
                  if(isset($sale_amts[$head]))
                     $sale_amts[$head] += floatval($date_val_split[1]);
                  else
                     $sale_amts[$head] = floatval($date_val_split[1]);

                  $item_entry["sale_amts"] = $sale_amts; 
               }

               $no_years = count(explode("|",$entry["years"]));
               $total_amt = $entry["sum"];

               if($total_amt==0)
                  $item_entry["forecast_qty"] = 0;
               else
                  $item_entry["forecast_qty"] = round(($total_amt/$no_years)*(($percentage/100)+1),0);
            } // Else End
         
            $last_approved_qty = $this->Po_mod->getSeasonReorderChangeQtyLast($entry["entry_id"]);
            
            $item_entry["remaining"] = $item_entry["forecast_qty"]-$last_approved_qty;
            
            if($child_count>0){
               if(count($parent_list)>0){ // If child reorder, retrieves the remaining forecasted qty from the parent
                     
                  foreach($parent_list as $par){
                     if($item_entry["item_no"]==$par["item_no"]){
                        $item_entry["remaining"] = $par["remaining"]-$last_approved_qty;;
                        break;
                     }
                  }
               }
            }
               
            $result[] = $item_entry;
            
         }// End Foreach - Entry

         return $result; 
      }

      function listSeasonReorderEntriesByStore(){
         if(!empty($_POST)){
            $batch_id = $_POST["batch_id"];
            $store_id = $_POST['store_id'];
            
            echo json_encode($this->viewSeasonReorderEntriesByStore($batch_id,$store_id));

         }
      }

      private function viewSeasonReorderEntriesByStore($batch_id,$store_id){
         $batch_details = $this->Po_mod->retrieveSeasonReorderBatchById($batch_id);
         $percentage = $batch_details["percentage"];

         $years = $this->Po_mod->getDistinctYears($batch_id); // Array
         $months = $this->Po_mod->getDistinctMonths($batch_id); // Array
         $headers = array();

         for($c=0; $c<count($years); $c++){
            for($x=0; $x<count($months); $x++){
               $headers[] = date("M", mktime(0, 0, 0, $months[$x], 1)).'_'.$years[$c];
            }
         }

         $result = array();
         $entry_list = $this->Po_mod->retrieveSeasonReorderBatchStore($batch_id,$store_id);
         foreach($entry_list as $entry){
            $item_entry["entry_id"] = $entry["entry_id"];
            $item_entry["item_no"] = $entry["item_no"];
            $item_variant = (empty($entry["variant_code"])) ? "" : " (".$entry["variant_code"].")";
            $item_entry["item_desc"] = $entry["item_desc"].$item_variant;
            $item_entry["uom"] = $entry["uom"];
            $item_entry["qty_onhand"] = $entry["qty_onhand"];
            
            $sale_split = explode("|",$entry["sales"]);
            $sale_amts = array();

            for($c=0; $c<count($sale_split); $c++){
               $date_val_split = explode(":", $sale_split[$c]); // 0 - month_year, 1 - sale_amt
               $month_year_split = explode("_", $date_val_split[0]); // 0 - month, 1 - year 
               $head = date("M", mktime(0, 0, 0, $month_year_split[0], 1)).'_'.$month_year_split[1];
               
               if(isset($sale_amts[$head]))
                  $sale_amts[$head] += floatval($date_val_split[1]);
               else
                  $sale_amts[$head] = floatval($date_val_split[1]);

               $item_entry["sale_amts"] = $sale_amts; 
            }

            $no_years = count(explode("|",$entry["years"]));
            $total_amt = $entry["sum"];

            if($total_amt==0)
               $item_entry["forecast_qty"] = 0;
            else
               $item_entry["forecast_qty"] = round(($total_amt/$no_years)*(($percentage/100)+1),0);
         
            
            $result[] = $item_entry;
         
         }// End 1st Foreach

         return array($result,$headers,$batch_details);
      }

      function setUpReasonAdj(){
         $list = $this->Po_mod->getReorderReasons(); 
         echo json_encode($list);
      }

      function saveQtyAdj(){
         if(!empty($_POST)){
            $vend_type = $_POST["vend_type"];
            $qtys = $_POST["qtys"]; // 2d Array
            $reasons = $_POST["reasons"]; // Array
            
            sort($reasons);
            $insert_hist = array();
            $user_details = $this->Acct_mod->retrieveUserDetails();
            
            $reason_ids = '';
            for($c=0; $c<count($reasons); $c++){
               $reason_ids.= $reasons[$c];
               if($c!=count($reasons)-1)
                  $reason_ids.= "^";
            }// End For

            for($c=0; $c<count($qtys); $c++){
               $entry_id = $qtys[$c][0];
               if($vend_type=="SI,DR"){
                  $adj_qty = $qtys[$c][1];
                  $orig_qty = $qtys[$c][2];
                  $adj_qty_dr = $qtys[$c][3];
                  $orig_qty_dr = $qtys[$c][4];
               
               }else if($vend_type=="SI"){
                  $adj_qty = $qtys[$c][1];
                  $orig_qty = $qtys[$c][2];
                  $adj_qty_dr = 0;
                  $orig_qty_dr = 0;

               }else{ // DR
                  $adj_qty = 0;
                  $orig_qty = 0;
                  $adj_qty_dr = $qtys[$c][1];
                  $orig_qty_dr = $qtys[$c][2];
               }
               
               //$reason_ids_ = $this->Po_mod->getSeasonReorderEntryByAdjReason($entry_id);
               $last_change = $this->Po_mod->getSeasonReorderChangeQtyHistoryLatest($entry_id);
               if(isset($last_change)){
                  $orig_qty = $last_change["adj_qty"];
                  $orig_qty_dr = $last_change["adj_qty_dr"];
               }

               if($adj_qty==="" || $adj_qty_dr===""){
                  $error_result = array("error","An adjusted qty is empty!");
                  break;
               }else{
                  
                  $is_cdc = $this->Po_mod->getSeasonReorderTypeByEntryId($entry_id); // yes or no
                  $is_aa = $user_details["user_type"]=="category-head" || 
                        ($user_details["store_id"]==6 && $user_details["user_type"]=="buyer" && ($is_cdc!=6 || $is_cdc==null))
                        || $user_details["user_type"]=="corp-manager" || $user_details["user_type"]=="incorporator";
                        // auto approved if CH, CDC Buyer, Corp-Manager, Incorporator

                  //if($reason_ids!=$reason_ids_ || $is_aa){
                     $insert_hist_["adj_qty"] = $adj_qty; 
                     $insert_hist_["orig_qty"] = $orig_qty; 
                     $insert_hist_["adj_qty_dr"] = $adj_qty_dr; 
                     $insert_hist_["orig_qty_dr"] = $orig_qty_dr; 
                     $insert_hist_["reason_ids"] = $reason_ids; 
                     $insert_hist_["date_inputted"] = date("Y-m-d H:i:s");
                     $insert_hist_["entry_id"] = $entry_id; 
                     $insert_hist_["user_id"] = $_SESSION["user_id"];

                     if($is_aa){  
                        $insert_hist_["status"] = "approved"; 
                        $insert_hist_["approved_by"] = $_SESSION["user_id"];
                     } 

                     $insert_hist[] = $insert_hist_;
                  //}
                  
               }

            }// End For

            if(isset($error_result))
               echo json_encode($error_result);
            else{
               if(count($insert_hist)>0){
                  $this->Po_mod->insertBatchToTable("season_reorder_change_qty_hist",$insert_hist);
               }
               
               echo json_encode(array("success","Adjustments Saved!"));
            }

         }
      }

      function listQtyAdjLog(){
         if(!empty($_POST)){
            $entry_id = $_POST["entry_id"];

            $header = $this->Po_mod->getSeasonReorderEntryById($entry_id);
            $result = array();
            
            $list = $this->Po_mod->retrieveSeasonReorderChangeQtyHistoryById($entry_id);
            foreach($list as $item_){
               $item["adj_qty"] = $item_["adj_qty"];
               $item["orig_qty"] = $item_["orig_qty"];
               $item["adj_qty_dr"] = $item_["adj_qty_dr"];
               $item["orig_qty_dr"] = $item_["orig_qty_dr"];
               $item["reasons"] = '';
               
               $reason_arr = explode("^",$item_["reason_ids"]);
               for($c=0; $c<count($reason_arr); $c++){
                  $item["reasons"] .= $this->Po_mod->getReorderReasonById($reason_arr[$c])["reason"];
                  if($c<count($reason_arr)-1)
                    $item["reasons"] .= ',';
               }

               $item["date_inputted"] = date("F d, Y -- h:i a", strtotime($item_["date_inputted"]));
               
               $user_details = $this->Acct_mod->getUserDetailsById($item_["user_id"]);
               $item["inputted_by"] = $this->Acct_mod->retrieveEmployeeName($user_details["emp_id"])["name"];
               $item["status"] = strtoupper($item_["status"]);
               
               $approver_details = $this->Acct_mod->getUserDetailsById($item_["approved_by"]);
               $item["approved_by"] = ""; 
               if(isset($approver_details))
                  $item["approved_by"] = $this->Acct_mod->retrieveEmployeeName($approver_details["emp_id"])["name"]; 

               $item["is_reorder"] = $item_["is_reorder"];

               $result[] = $item;
            }// Foreach End
         
            echo json_encode(array($header,$result));        
         }
      }

      private function getEntriesStatus($batch_id,$status_arr){ 
         $count_invalid = 0;
         $list = $this->Po_mod->retrieveSeasonReorderBatchItemEntries($batch_id);
         
         foreach($list as $entry){
            $status = $this->Po_mod->getSeasonReorderEntryByAdjStatus($entry["entry_id"]);
            if(in_array($status,$status_arr))
               $count_invalid++;
         }

         return $count_invalid;
      }

      function setStatusSeasonReorder(){
         if(!empty($_POST)){
            $batch_id = $_POST["batch_id"];
            $status = $_POST["status"]; // "approved" or "disapproved"
            $not_in = isset($_POST["not_in"]) ? $_POST['not_in']:""; 
            $user_details = $this->Acct_mod->retrieveUserDetails();
            $batch_details = $this->Po_mod->retrieveSeasonReorderBatchById($batch_id);
            
            $set_status = '';
            $err = '';

            // Store Reorders: Store Buyer => Store Category Head => Corporate Buyer
            // CDC Reorders: Corporate Buyer => Corp Category Head => Corp Manager !=> Incorporator

            if($user_details["store_id"]!=6 && $user_details["user_type"]=="buyer" && $status=="approved" 
               && $batch_details["status"]=="pending"){ // Store Buyer
               
               $count_invalid = $this->getEntriesStatus($batch_id,array(""));
               if($count_invalid==0)
                  $set_status = "approved-by-buyer";
               else
                  $err = "Reorder Invalid for Approval! (".$count_invalid.") Item/s Not Set For Reorder.";

            }else if($user_details["store_id"]!=6 && $user_details["user_type"]=="buyer" && $status=="disapproved" 
               && $batch_details["status"]=="pending"){ // Store Buyer
               
               $set_status = "cancelled";
               
            }else if($user_details["user_type"]=="category-head" && $status=="approved" && $batch_details["status"]=="approved-by-buyer"){ // Category Head
               
               $count_invalid = $this->getEntriesStatus($batch_id,array("pending","disapproved"));
               if($count_invalid==0)
                  $set_status = "approved-by-category"; 
               else
                  $err = "Reorder Invalid for Approval! (".$count_invalid.") Item/s Not Set For Reorder.";
                    
            }else if($user_details["user_type"]=="category-head" && $status=="disapproved" 
               && $batch_details["status"]=="approved-by-buyer"){ // Category Head
               
               $set_status = "disapproved-by-category";
            
            }else if($user_details["store_id"]==6 && $user_details["user_type"]=="buyer" && $status=="approved"){ // CDC Buyer
               
               if($batch_details["store_id"]!=6 && $batch_details["status"]=="approved-by-category")
                  $set_status = "approved-by-corp-buyer";
               else if($batch_details["store_id"]==6 && $batch_details["status"]=="pending"){
                  $count_invalid = $this->getEntriesStatus($batch_id,array(""));
                  if($count_invalid==0)
                     $set_status = "approved-by-buyer";
                  else
                     $err = "Reorder Invalid for Approval! (".$count_invalid.") Item/s Not Set For Reorder.";
               }
            
            }else if($user_details["store_id"]==6 && $user_details["user_type"]=="buyer" && $status=="disapproved" 
               && $batch_details["store_id"]!=6 && $batch_details["status"]=="approved-by-category"){ // CDC Buyer
               
               $set_status = "disapproved-by-corp-buyer";
            
            }else if($user_details["store_id"]==6 && $user_details["user_type"]=="buyer" && $status=="disapproved" 
               && $batch_details["store_id"]==6 && $batch_details["status"]=="pending"){ // CDC Buyer
               
               $set_status = "cancelled";
            
            }else if($user_details["user_type"]=="corp-manager" && $status=="approved" 
               && $batch_details["status"]=="approved-by-category"){ // Corp-Manager
               
               $set_status = "approved-by-corp-manager";      
            
            }else if($user_details["user_type"]=="corp-manager" && $status=="disapproved" 
               && $batch_details["status"]=="approved-by-category"){ // Corp-Manager
               
               $set_status = "disapproved-by-corp-manager";
            
            }else{
               $err = 'Document No. Already '.ucfirst($status).'!';
            }

            if($set_status!=''){
               $doc_no = '';
               
               $this->Po_mod->updateTable("season_reorder_batch", array("status"=>$set_status), array("batch_id"=>$batch_id));
               
               $insert_stat["status"] = $set_status;
               $insert_stat["date_set"] = date("Y-m-d H:i:s");
               $insert_stat["batch_id"] = $batch_id;
               $insert_stat["user_id"] = $_SESSION["user_id"];
               $this->Po_mod->insertToTable("season_reorder_status_hist",$insert_stat);


               $approver = $this->Po_mod->getVendorApprover($batch_details["vendor_code"]); // Category-Head, Corp-Manager

               if($set_status=="approved-by-corp-buyer" || ($batch_details["store_id"]==6
                  && (($set_status=="approved-by-category" && $approver=="Category-Head")
                  || ($set_status=="approved-by-corp-manager" && $approver=="Corp-Manager") 
                  || ($set_status=="approved-by-incorp" && $approver=="Corp-Manager")))) {
                  
                  $this->Po_mod->updateTable("season_reorder_batch", array("is_finalized"=>"yes"), array("batch_id"=>$batch_id));

                  $hist_id_list = $this->Po_mod->getSeasonReorderHistIdForApproval($batch_id);
                  foreach($hist_id_list as $id){
                     $hist_id = $id["last_id"];
                     $this->Po_mod->updateTable("season_reorder_change_qty_hist", array("is_reorder"=>"yes"), array("hist_id"=>$hist_id));
                  }

                  if($not_in!="")
                     $not_in = ' NOT IN (' . implode(',', $not_in) . ') ';
                  else
                     $not_in = ' ';

                  $new_batch_id = $this->newDocument($batch_details,$not_in);

                  if($new_batch_id!=0){
                     $this->Po_mod->updateTable("season_reorder_batch", array("batch_child"=>$new_batch_id), array("batch_id"=>$batch_id));

                     $new_batch_details = $this->Po_mod->retrieveSeasonReorderBatchById($new_batch_id);
                     $doc_no = $this->getDocumentNo($new_batch_details);
                  }

               } // End If Final Approve
               
               echo json_encode(array("success",$doc_no));
            
            }else{
               echo json_encode(array("error",$err));
            }
            
         }
      }

      function getSeasonReorderStatusHistory(){
         if(!empty($_POST)){
            $batch_id = $_POST["batch_id"];
            $hist = $this->Po_mod->getSeasonReorderStatusHistory($batch_id);
            
            foreach($hist as &$log){
               $log["status"] = strtoupper($log["status"]);
               $log["date_set"] = date("F d, Y -- h:i a", strtotime($log["date_set"]));
               $log["user"] = $this->Acct_mod->retrieveEmployeeName($log["emp_id"])["name"];
            }

            echo json_encode($hist);
         }
      }

      private function newDocument($batch_details,$not_in){

         $insert_batch["season"] = $batch_details["season"];
         $insert_batch["vendor_code"] = $batch_details["vendor_code"];
         $insert_batch["vendor_name"] = $batch_details["vendor_name"];
         $insert_batch["date_generated"] = date("Y-m-d H:i:s");
         $insert_batch["group_code"] = $batch_details["group_code"];
         $insert_batch["percentage"] = $batch_details["percentage"];
         $insert_batch["store_id"] = $batch_details["store_id"];
         $insert_batch["user_id"] = $batch_details['user_id'];
         
         $list = $this->Po_mod->retrieveSeasonReorderTablesJoined($batch_details["batch_id"],$not_in);

         if(count($list)>0){

            $batch_id = $this->Po_mod->insertToTable("season_reorder_batch",$insert_batch);

            foreach($list as $item){

               $entry_id = $this->Po_mod->getIDSeasonReorderItemEntry($item["item_no"],$item["variant_code"],$batch_id);
                  
               if($entry_id==0){
                  $insert_entry["item_no"] = $item["item_no"];
                  $insert_entry["item_desc"] = $item["item_desc"];
                  $insert_entry["uom"] = $item["uom"];
                  $insert_entry["variant_code"] = $item["variant_code"];

                  $unit_prices = $this->Po_mod->getUnitPricesFromNav($item["item_no"],$item["uom"],$item["variant_code"]); // Unit Price, Unit Price Including VAT
                  $prod_arr = $this->Po_mod->getItemProdFromNav($item["item_no"]); // Inventory, Gen. Prod, Vat Prod, WHT Prod Posting Group 
                  $barcode = $this->Po_mod->getBarcodeFromNav($item["item_no"],$item["uom"],$item["variant_code"]);

                  $insert_entry["unit_price"] = $unit_prices["unit_price"];
                  $insert_entry["unit_price_vat"] = $unit_prices["unit_price_vat"];
                  $insert_entry["inventory_posting_grp"] = $prod_arr["inventory_posting_grp"];
                  $insert_entry["gen_prod"] = $prod_arr["gen_prod"];
                  $insert_entry["vat_prod"] = $prod_arr["vat_prod"];
                  $insert_entry["wht_prod"] = $prod_arr["wht_prod"];
                  $insert_entry["barcode"] = $barcode;
                  $insert_entry["batch_id"] = $batch_id;
                  $entry_id = $this->Po_mod->insertToTable("season_reorder_item_entry",$insert_entry);

               }// If End

               $store_entry_id = $this->Po_mod->getIDSeasonReorderStoreEntry($item["store_id"],$entry_id);
         
               if($store_entry_id==0){
                  $insert_store_entry["qty_onhand"] = $item["qty_onhand"];
                  $insert_store_entry["store_id"] = $item["store_id"];
                  $insert_store_entry["entry_id"] = $entry_id;

                  $store_entry_id = $this->Po_mod->insertToTable("season_reorder_store_entry",$insert_store_entry);
               }

               $insert_ref["year_ref"] = $item["year_ref"];
               $insert_ref["month_ref"] = $item["month_ref"];
               $insert_ref["amount"] = $item["amount"];
               $insert_ref["store_entry_id"] = $store_entry_id;
               $this->Po_mod->insertToTable("season_reorder_reference",$insert_ref);

               // Pending Qty
               $po_batch = array();
               $po_group = $item["po_grp"];
               
               if($po_group!=null){
                  $po_group = explode("|",$po_group);

                  for($i=0; $i<count($po_group); $i++){
                     $po_ = explode(":",$po_group[$i]);
                     
                     $count_po = $this->Po_mod->countPoByEntryIdAndDocNo($store_entry_id,$po_[0]);
                     if($count_po==0){
                        $insert_po["document_no"] = $po_[0];
                        $insert_po["po_date"] = $po_[1];
                        $insert_po["pending_qty"] = $po_[2];
                        
                        if(strtotime($po_[3])!==false)
                           $insert_po["exp_del_date"] = $po_[3];

                        $insert_po["store_entry_id"] = $store_entry_id;
                        $po_batch[] = $insert_po;
                     }   
                    
                  }
               }
                           
               if(count($po_batch)>0)
                  $this->Po_mod->insertBatchToTable("season_reorder_pending_qty",$po_batch);
            }// Foreach End
            
            return $batch_id;

         } // If End

         return 0;
               
      }

      function setStatusQtyAdj(){
         if(!empty($_POST)){
            $entry_ids = $_POST["entry_ids"]; // Array
            $status = $_POST["status"];

            $user_details = $this->Acct_mod->retrieveUserDetails();
            
            for($c=0; $c<count($entry_ids); $c++){
               $entry_id = $entry_ids[$c];
               $log_details = $this->Po_mod->getSeasonReorderChangeQtyHistoryLatest($entry_id);
               
               if($log_details["status"]=="pending"){
                  $update_arr = array("status" => $status, "approved_by" => $_SESSION["user_id"]);

                  $where_arr = array("hist_id" => $log_details["hist_id"]);
            
                  $this->Po_mod->updateTable("season_reorder_change_qty_hist", $update_arr, $where_arr);
               }
               
            }// End For

            echo json_encode(array("success","Adjustments ".$status."!"));
         }
      }

      function listPendingQtyByEntry(){
         if(!empty($_POST)){
            $entry_id = $_POST["entry_id"];

            $header = $this->Po_mod->getSeasonReorderEntryById($entry_id);
            $result = array();
            
            $list = $this->Po_mod->getSeasonReorderPendingQtyDetails($entry_id);
            foreach($list as $item_){
               $count = $this->Po_mod->countCancelledPo($item_["document_no"],$item_["store_id"]);
               $item["pending_id"] = $item_["pending_id"];
               $item["store"] = strtoupper($item_["value_"]);
               $item["document_no"] = $item_["document_no"];
               $item["po_date"] = date("F d, Y", strtotime($item_["po_date"]));
               $item["uom"] = $item_["uom"];
               $item["pending_qty"] = $item_["pending_qty"];
               $item["exp_del_date"] = $item_["exp_del_date"];
               $item["exp_del_date_"] = ($item_["exp_del_date"]==null) ? "" : date("F d, Y", strtotime($item_["exp_del_date"]));
               $item["status"] = ($count>0) ? "Cancelled" : "Active";

               $result[] = $item;

            }// Foreach End
         
            echo json_encode(array($header,$result));
         }
      }

      function updateExpDelDate(){
         if(!empty($_POST)){
            $pending_id = $_POST["pending_id"];
            $exp_del_date = $_POST["exp_del_date"];

            if($exp_del_date=="")
               $exp_del_date = null;

            $update_arr["exp_del_date"] = $exp_del_date;
            $where_arr["pending_id"] = $pending_id;
            $this->Po_mod->updateTable("season_reorder_pending_qty",$update_arr,$where_arr);

            $exp_del_date_ = ($exp_del_date==null) ? "" : date("F d, Y", strtotime($exp_del_date));
            echo json_encode(array($pending_id,$exp_del_date_));

         }
      }

      function generate_txt(){
         if(!empty($_POST)){

            $batches = $_POST["batches"];
            $doc_numbers = '';
            $text_data = '';
            $text_lines = '';
            $c = 1;

            for($i=0; $i<count($batches); $i++){
               $batch_id = $batches[$i][0];
               $vend_type = $batches[$i][1];
               $nav_doc = $batches[$i][2];

               $now = date("m/d/y");

               $batch_details = $this->Po_mod->retrieveSeasonReorderBatchById($batch_id);
               // $vend_type = $this->Po_mod->getVendorTypeByNo($batch_details["vendor_code"]); // SI,DR

               if($vend_type=="SI" && ($batch_details["nav_si_doc"]===null || $batch_details["nav_si_doc"]==="")){
                  $this->Po_mod->updateTable("season_reorder_batch",array("nav_si_doc"=>$nav_doc),array("batch_id"=>$batch_id));
                  $batch_details["nav_si_doc"] = $nav_doc;
               }else if($vend_type=="DR" && ($batch_details["nav_dr_doc"]===null || $batch_details["nav_dr_doc"]==="")){ 
                  $this->Po_mod->updateTable("season_reorder_batch",array("nav_dr_doc"=>$nav_doc),array("batch_id"=>$batch_id));
                  $batch_details["nav_dr_doc"] = $nav_doc;
               }

               $vendor_code = $batch_details["vendor_code"];
               $group_code = $batch_details["group_code"];
               // $date_generated = $batch_details["date_generated"];
               $store_id = $batch_details["store_id"];


               $vendor_details = $this->Po_mod->retrieveVendorInfo($vendor_code,$group_code);
               $store_details = $this->Po_mod->getStore($store_id);
               //$doc_no = $this->getDocumentNo($batch_details);
               $doc_no = ($vend_type=="SI") ? $batch_details["nav_si_doc"]:$batch_details["nav_dr_doc"];
               
               $doc_numbers .= $doc_no.'|';

               $date_now = new DateTime($now); 
               
               if($vendor_details["payment_terms_code"]!=null)
                  $date_now->modify('+'.$vendor_details["payment_terms_code"].' days'); // Due Date = Date Now + Payment Terms

               $currency_factor = (strcasecmp($vendor_details["currency_code"],"php")==0) ? 1 : 0;

               $lead_time_factor = $vendor_details["otdl"]+$vendor_details["buffer"]+$vendor_details["frequency"];

               if($i>0)
                  $text_data .= PHP_EOL;
               
               $text_data .= '"Order"|';
               $text_data .= '"'.$doc_no.'"|';
               $text_data .= '"'.$vendor_code.'"|';
               $text_data .= '"'.$vendor_code.'"|';
               $text_data .= '"'.$vendor_details["name_"].'"|';
               $text_data .= '"'.$vendor_details["name_"].'"|';
               $text_data .= '"'.$vendor_details["address"].'"|';
               $text_data .= '"'.$vendor_details["address_2"].'"|';
               $text_data .= '"'.$vendor_details["city"].'"|';
               $text_data .= '"'.$vendor_details["contact"].'"|';
               $text_data .= '"'.$store_details["customer_name"].'"|';
               $text_data .= '"'.$store_details["customer_name"].'"|';
               $text_data .= '"'.$store_details["customer_address"].'"|';
               $text_data .= '"'.$store_details["customer_address"].'"|';
               $text_data .= '"'.$now.'"|';
               $text_data .= '"'.$now.'"|';
               $text_data .= '"Order '.$doc_no.'"|';
               $text_data .= '"'.$vendor_details["payment_terms_code"].'"|';
               $text_data .= '"'.$date_now->format("m/d/y").'"|';
               $text_data .= '"'.$store_details["location_code"].'"|';
               $text_data .= '"'.$store_details["company_code"].'"|';
               $text_data .= '"'.$store_details["department_code"].'"|';
               $text_data .= '"'.$vendor_details["posting_grp"].'"|';
               $text_data .= '"'.$vendor_details["currency_code"].'"|';
               $text_data .= '"'.$currency_factor.'"|';
               $text_data .= '"'.$vendor_details["prices_including_vat"].'"|';
               $text_data .= '"'.$vendor_details["invoice_disc_code"].'"|';
               $text_data .= '"'.$vendor_details["gen_bus_posting_group"].'"|';
               $text_data .= '"'.$vendor_details["name_"].'"|';
               $text_data .= '"'.$vendor_details["name_"].'"|';
               $text_data .= '"'.$vendor_details["address"].'"|';
               $text_data .= '"'.$vendor_details["address_2"].'"|';
               $text_data .= '"'.$vendor_details["city"].'"|';
               $text_data .= '"'.$vendor_details["contact"].'"|';
               $text_data .= '"G/L Account"|';
               $text_data .= '""|'; // No. Series
               $text_data .= '""|'; // Posting No. Series
               $text_data .= '""|'; // Receiving No. Series
               $text_data .= '"'.$vendor_details["vat_bus_posting_group"].'"|';
               $text_data .= '"1"|'; // Doc. No. Occurrence
               $text_data .= '"'.$store_details["responsibility_center"].'"|';
               $text_data .= '"'.$vendor_details["bus_posting_group"].'"|'; // WHT Bus. Posting Group
               $text_data .= '""|'; // Purch. Wksht. Rec. Inst.
               $text_data .= '""|'; // Purch. Wksht. Source Doc.
               $text_data .= '""|'; // Update Item Purchase Cost
               $text_data .= '"'.$lead_time_factor.'"|';
               $text_data .= '"'.$vendor_details["otdl"].'"|';
               $text_data .= '"'.$vendor_details["buffer"].'"|';
               $text_data .= '"'.$vendor_details["frequency"].'"|'; 
               $text_data .= '""|'; // Order Work Sheet Doc
               

               $total_amt = 0;
               $total_amt_vat = 0;
               
               $lines = $this->Po_mod->retrieveJoinedForLines($batch_id,$vend_type);

               foreach($lines as $line){

                  $vat_percent = filter_var($line["vat_prod"], FILTER_SANITIZE_NUMBER_INT); // Removes non-numeric characters
                  if($vat_percent=="")
                     $vat_percent = "0";

                  $total_amt += $line["amt"];
                  $total_amt_vat += $line["amt_vat"];

                  $text_lines .= PHP_EOL;
                  $text_lines .= '"Order"|';
                  $text_lines .= '"'.$doc_no.'"|';
                  $text_lines .= '"'.$c.'0000"|';
                  $text_lines .= '"'.$vendor_code.'"|';
                  $text_lines .= '"Item"|';
                  $text_lines .= '"'.$line["item_no"].'"|';
                  $text_lines .= '"'.$line["location_code"].'"|';
                  $text_lines .= '"'.$line["inventory_posting_grp"].'"|'; // Inventory Posting Group
                  $text_lines .= '"'.$now.'"|';
                  $text_lines .= '"'.$line["item_desc"].'"|'; // Description
                  $text_lines .= '"'.$line["uom"].'"|';
                  $text_lines .= '"'.$line["adj_qty"].'"|';
                  $text_lines .= '"'.$line["adj_qty"].'"|'; // Outstanding Qty
                  $text_lines .= '"'.$line["adj_qty"].'"|'; // Qty to Invoice
                  $text_lines .= '"'.$line["adj_qty"].'"|'; // Qty to Receive
                  $text_lines .= '"'.$line["unit_price_vat"].'"|'; 
                  $text_lines .= '"'.$line["unit_price"].'"|'; 
                  $text_lines .= '"'.$vat_percent.'"|'; // VAT %  
                  $text_lines .= '"'.$line["amt"].'"|'; 
                  $text_lines .= '"'.$line["amt_vat"].'"|'; 
                  $text_lines .= '""|'; // Unit Price(LCY)
                  $text_lines .= '"yes"|'; 
                  $text_lines .= '"'.$line["company_code"].'"|';
                  $text_lines .= '"'.$line["department_code"].'"|';
                  $text_lines .= '""|'; // Indirect Cost %
                  $text_lines .= '"'.$line["amt_vat"].'"|'; // Outstanding Amount
                  $text_lines .= '"'.$vendor_code.'"|';
                  $text_lines .= '"'.$vendor_details["gen_bus_posting_group"].'"|'; // Gen. Bus. Posting Group
                  $text_lines .= '"'.$line["gen_prod"].'"|'; // Gen. Prod. Posting Group
                  $text_lines .= '""|'; // Transaction Type : "Normal VAT"
                  $text_lines .= '"'.$vendor_details["vat_bus_posting_group"].'"|'; // VAT Bus. Posting Group
                  $text_lines .= '"'.$line["vat_prod"].'"|'; // VAT Prod. Posting Group
                  $text_lines .= '"'.$vendor_details["currency_code"].'"|';
                  $text_lines .= '"'.$line["amt_vat"].'"|'; // Outstanding Amount (LCY)
                  $text_lines .= '""|'; // VAT Base Amount
                  $text_lines .= '"'.$line["unit_price"].'"|'; 
                  $text_lines .= '""|'; // System-Created Entry
                  $text_lines .= '"'.$line["amt_vat"].'"|'; 
                  $text_lines .= '""|'; // VAT Difference
                  $text_lines .= '""|'; // Inv. Disc. Amount to Invoice
                  $text_lines .= '"'.$line["vat_prod"].'"|'; // VAT Identifier
                  $text_lines .= '"'.$line["variant_code"].'"|'; // Variant Code
                  $text_lines .= '""|'; // Bin Code
                  $text_lines .= '"'.$line["qty_per_unit_of_measure"].'"|'; // Qty. per Unit of Measure
                  $text_lines .= '"'.$line["uom"].'"|'; // Unit of Measure Code
                  $text_lines .= '"'.$line["base_qty"].'"|'; // Quantity (Base)
                  $text_lines .= '"'.$line["base_qty"].'"|'; // Outstanding Qty. (Base)
                  $text_lines .= '"'.$line["base_qty"].'"|'; // Qty. to Invoice (Base)
                  $text_lines .= '"'.$line["base_qty"].'"|'; // Qty. to Receive (Base)
                  $text_lines .= '""|'; // Qty. Rcd. Not Invoiced (Base)
                  $text_lines .= '"'.$line["responsibility_center"].'"|';
                  $text_lines .= '"'.$now.'"|';
                  $text_lines .= '"'.$now.'"|';
                  $text_lines .= '"yes"|';
                  $text_lines .= '"'.$vendor_details["bus_posting_group"].'"|'; // WHT Bus. Posting Group
                  $text_lines .= '"'.$line["wht_prod"].'"|'; // WHT Prod. Posting Group
                  $text_lines .= '"Yes"|';
                  $text_lines .= '"No"|';
                  $text_lines .= '"No"|';
                  $text_lines .= '"No"|';
                  $text_lines .= '"Unlimited"|';
                  $text_lines .= '"0D"|';
                  $text_lines .= '"'.$line["barcode"].'"';

                  $c++;
               }
               
               $purch_wksht = ($store_id==6) ? '1' : '';
               $store_wksht = ($store_id!=6) ? '1' : '';

               $text_data .= '"'.$total_amt.'"|'; // Amount
               $text_data .= '"'.$total_amt_vat.'"|'; // Amount Including VAT
               $text_data .= '"'.$date_now->format("m/d/y").'"|'; // Document Date
               $text_data .= '"Receive"|'; // Posting Type
               $text_data .= '"1"|'; // Purch. Wksht. Document '.$purch_wksht.'
               $text_data .= '"0"|'; // Store Purch. Wksht. '.$store_wksht.'
               $text_data .= '"Finalized"|'; // Purch. Wksht. Status
               $text_data .= '"Released"'; // Status

            } // For End
         
            header("Content-Type: text/plain");
            header("Content-Disposition: attachment; filename=".substr($doc_numbers, 0, -1).".txt");
            ob_clean();

            echo $text_data.PHP_EOL.$text_lines;
         
         } // If End
      }


      function generate_pdf($batch_id){
         
         $all_data = $this->viewSeasonReorderEntries($batch_id); // batch_details,headers,header_keys,result
         $batch_details = $all_data[0];
         $head_ = $all_data[1];
         $head_keys = $all_data[2];
         $result = $all_data[3];

         $unit_arr = array('mm','in');
         $size_arr = array('A4','Letter',array(215.9, 330.2)); // Long
         $orient_arr = array('P','L'); // Portrait, Landscape

         $this->ppdf = new TCPDF($orient_arr[1], 'mm', $size_arr[2], true, 'UTF-8', false); 
         $this->ppdf->SetTitle($batch_details["doc_no"]);    
         $this->ppdf->SetMargins(5, 15,5, true);
         $this->ppdf->setPrintHeader(false);
         $this->ppdf->SetFont('', '', 10, '', true);
         $this->ppdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

         $pages = "Page: ".$this->ppdf->getAliasNumPage().'/'.$this->ppdf->getAliasNbPages(); 
         $counter = 0;

         // Month Headers
         $headerName = ($batch_details["store_id"]==6) ? "TOTAL FORECAST (QTY)":"REFERENCE YEAR SALES (QTY)";
         $headers = ($batch_details["store_id"]==6) ? $head_["compressed"] : $head_["ym"];
         $headerYM = '';
         for($i=0; $i<count($headers); $i++){
            $headerYM .= '<th>'.$headers[$i].'</th>';
         }

         foreach($result as $key => $item){
            if($counter==0){
                    
               $this->ppdf->AddPage(); // Landscape
                    
                  $this->ppdf->SetFont('', 'B', 10);
                  $this->ppdf->SetXY(300, 5); // x,y
                  $this->ppdf->Cell(0, 10, $pages, 0, 1);
                  $this->ppdf->SetFont('', '', 10);

                  $tbl = ' <table width="80%" cellspacing="1" cellpadding="3" style="font-size:10px;">
                              <tr>
                                 <td align="left">Season</td>
                                 <td align="left"><b>'.$batch_details["season"].'</b></td>
                                 <td align="left">Document No.</td>
                                 <td align="left"><b>'.$batch_details["doc_no"].'</b></td> 
                              </tr>
                              <tr>
                                 <td align="left">Supplier Code</td>
                                 <td align="left"><b>'.$batch_details["vendor_code"].'</b></td>
                                 <td align="left">Date Generated</td>
                                 <td align="left"><b>'.$batch_details["date_generated"].'</b></td> 
                              </tr><tr>
                                 <td align="left">Supplier Name</td>
                                 <td align="left"><b>'.$batch_details["vendor_name"].'</b></td>
                                 <td align="left">Status</td>
                                 <td align="left"><b>'.strtoupper($batch_details["status"]).'</b></td> 
                              </tr>
                           </table>
                           <br><br>
                           <table align="center" width="100%" cellspacing="1" border="1" cellpadding="3" style="font-size:8px;">
                              <tr>
                                <th rowspan="2"><b>ITEM NO.</b></th>
                                <th rowspan="2"><b>DESCRIPTION</b></th>
                                <th rowspan="2"><b>UOM</b></th>
                                <th colspan="'.count($headers).'"><b>'.$headerName.'</b></th>
                                <th rowspan="2"><b>FORECASTED QTY</b></th>
                                <th rowspan="2"><b>REMAINING FORECASTED QTY</b></th>
                                <th rowspan="2"><b>QTY ON HAND</b></th>
                                <th rowspan="2"><b>PENDING QTY</b></th>
                                <th rowspan="2"><b>SUGGESTED REORDER QTY</b></th>
                              </tr>
                              <tr>'.$headerYM.'</tr>';
            }

            $qty_amts = '';
            $headers = ($batch_details["store_id"]==6) ? $head_keys["compressed"] : $head_keys["ym"];

            for($x=0; $x<count($headers); $x++){
               $cell = 0;
               if (isset($item["sale_amts"][$headers[$x]])) {
                 $cell = $item["sale_amts"][$headers[$x]];
               }

               $qty_amts .= '<td>'.$cell.'</td>';
            }

            $ovs = ($item["overstock"]<0) ? '<br><span style="color: red;">OVERSTOCK: '.abs($item["overstock"]).'</span>' : '';

            if($batch_details["vend_type"]=="SI,DR")
               $reorder_qty = '<b>SI:</b> '.$item["reorder_qty"].'<br><b>DR:</b> '.$item["reorder_qty_dr"];
            else if($batch_details["vend_type"]=="SI")
               $reorder_qty = '<b>SI:</b> '.$item["reorder_qty"];
            else
               $reorder_qty = '<b>DR:</b> '.$item["reorder_qty_dr"];

            $tbl.= '<tr>
                      <td>'.$item["item_no"].'</td>
                      <td>'.$item["item_desc"].'</td>
                      <td>'.$item["uom"].'</td>'
                      .$qty_amts.
                      '<td>'.$item["forecast_qty"].'</td>
                      <td>'.$item["remaining"].'</td>
                      <td>'.$item["qty_onhand"].'</td>
                      <td>'.$item["pending_qty"].'</td>
                      <td>'.$reorder_qty.$ovs.'</td>
                    </tr>';

            if($counter==5){
               $counter = 0;
               $tbl.= '</table>';
               
               if($key==count($result)-1){
                  $this->ppdf->writeHTML($tbl, true, false, false, false, '');
                  break;
               }

               $this->ppdf->writeHTML($tbl, true, false, false, false, '');
            
            }else{
               $counter++; 
            }

            if($key==count($result)-1){
               $tbl.= '</table>';
               $this->ppdf->writeHTML($tbl, true, false, false, false, '');
            }
         }

         if(
            ($batch_details["store_id"]!=6 && ($batch_details["status"]=='approved-by-category' || 
            $batch_details["status"]=='approved-by-corp-buyer')) ||
            ($batch_details["store_id"]==6 && ($batch_details["approver"]=="Category-Head" && 
            ($batch_details["status"]=='approved-by-buyer' || $batch_details["status"]=='approved-by-category')) ||
            ($batch_details["approver"]=="Corp-Manager" && 
            ($batch_details["status"]=='approved-by-category' || $batch_details["status"]=='approved-by-corp-manager')))
         ){
            $img_sign = $this->Po_mod->getSignatureImage();
            $this->ppdf->Image(base_url().$img_sign, 80, 160, 20, 20, 'PNG');
         }
         
         $corp_emp_id = $this->Acct_mod->getUserDetailsById(16)["emp_id"];
         $corp_name = $this->Acct_mod->retrieveEmployeeName($corp_emp_id)["name"];

         $incorp_emp_id = $this->Acct_mod->getUserDetailsById(12)["emp_id"];
         $incorp_name = $this->Acct_mod->retrieveEmployeeName($incorp_emp_id)["name"];

         $this->ppdf->SetXY(60, 170); // x,y
         $this->ppdf->Cell(60, 10, $corp_name, 0, 1,'C');
         $this->ppdf->SetXY(210, 170); // x,y
         $this->ppdf->Cell(60, 10, $incorp_name, 0, 1,'C');

         $this->ppdf->SetFont('', 'B', 10);
         $this->ppdf->SetXY(60, 175); // x,y
         $this->ppdf->Cell(60, 10, 'CORPORATE MANAGER', 0, 1,'C');
         $this->ppdf->SetXY(210, 175); // x,y
         $this->ppdf->Cell(60, 10, 'INCORPORATOR', 0, 1,'C');

         ob_end_clean();
         echo $this->ppdf->Output();
 
      }

      function generate_pdf_store($batch_id,$store_id){

         $all_data = $this->viewSeasonReorderEntriesByStore($batch_id,$store_id); // result,headers,batch_details
         $batch_details = $all_data[2];
         $head_ = $all_data[1];
         $result = $all_data[0];

         $store_name = $this->Po_mod->getStore($store_id)["value_"];
         $doc_no = $this->getDocumentNo($batch_details).' ('.strtoupper($store_name).')';
         $date_generated = date("F d, Y -- h:i a", strtotime($batch_details["date_generated"]));
         $approver = $this->Po_mod->getVendorApprover($batch_details["vendor_code"]); // Category-Head, Corp-Manager

         $unit_arr = array('mm','in');
         $size_arr = array('A4','Letter',array(215.9, 330.2)); // Long
         $orient_arr = array('P','L'); // Portrait, Landscape

         $this->ppdf = new TCPDF($orient_arr[1], 'mm', $size_arr[2], true, 'UTF-8', false); 
         $this->ppdf->SetTitle($doc_no);    
         $this->ppdf->SetMargins(5, 15,5, true);
         $this->ppdf->setPrintHeader(false);
         $this->ppdf->SetFont('', '', 10, '', true);
         $this->ppdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

         $pages = "Page: ".$this->ppdf->getAliasNumPage().'/'.$this->ppdf->getAliasNbPages(); 
         $counter = 0;

         // Month Headers
         $headerName = "REFERENCE YEAR SALES (QTY)";
         $headers = $head_;
         $headerYM = '';
         for($i=0; $i<count($headers); $i++){
            $headerYM .= '<th>'.$headers[$i].'</th>';
         }

         foreach($result as $key => $item){
            if($counter==0){
                    
               $this->ppdf->AddPage(); // Landscape
                    
                  $this->ppdf->SetFont('', 'B', 10);
                  $this->ppdf->SetXY(300, 5); // x,y
                  $this->ppdf->Cell(0, 10, $pages, 0, 1);
                  $this->ppdf->SetFont('', '', 10);

                  $tbl = ' <table width="80%" cellspacing="1" cellpadding="3" style="font-size:10px;">
                              <tr>
                                 <td align="left">Season</td>
                                 <td align="left"><b>'.$batch_details["season"].'</b></td>
                                 <td align="left">Document No.</td>
                                 <td align="left"><b>'.$doc_no.'</b></td> 
                              </tr>
                              <tr>
                                 <td align="left">Supplier Code</td>
                                 <td align="left"><b>'.$batch_details["vendor_code"].'</b></td>
                                 <td align="left">Date Generated</td>
                                 <td align="left"><b>'.$date_generated.'</b></td> 
                              </tr><tr>
                                 <td align="left">Supplier Name</td>
                                 <td align="left"><b>'.$batch_details["vendor_name"].'</b></td>
                                 <td align="left">Status</td>
                                 <td align="left"><b>'.strtoupper($batch_details["status"]).'</b></td> 
                              </tr>
                           </table>
                           <br><br>
                           <table align="center" width="100%" cellspacing="1" border="1" cellpadding="3" style="font-size:8px;">
                              <tr>
                                <th rowspan="2"><b>ITEM NO.</b></th>
                                <th rowspan="2"><b>DESCRIPTION</b></th>
                                <th rowspan="2"><b>UOM</b></th>
                                <th colspan="'.count($headers).'"><b>'.$headerName.'</b></th>
                                <th rowspan="2"><b>FORECASTED QTY</b></th>
                                <th rowspan="2"><b>QTY ON HAND</b></th>
                              </tr>
                              <tr>'.$headerYM.'</tr>';
            }

            $qty_amts = '';
            
            for($x=0; $x<count($headers); $x++){
               $cell = 0;
               if (isset($item["sale_amts"][$headers[$x]])) {
                 $cell = $item["sale_amts"][$headers[$x]];
               }

               $qty_amts .= '<td>'.$cell.'</td>';
            }

            
            $tbl.= '<tr>
                      <td>'.$item["item_no"].'</td>
                      <td>'.$item["item_desc"].'</td>
                      <td>'.$item["uom"].'</td>'
                      .$qty_amts.
                      '<td>'.$item["forecast_qty"].'</td>
                      <td>'.$item["qty_onhand"].'</td>
                    </tr>';

            if($counter==5){
               $counter = 0;
               $tbl.= '</table>';
               
               if($key==count($result)-1){
                  $this->ppdf->writeHTML($tbl, true, false, false, false, '');
                  break;
               }

               $this->ppdf->writeHTML($tbl, true, false, false, false, '');
            
            }else{
               $counter++; 
            }

            if($key==count($result)-1){
               $tbl.= '</table>';
               $this->ppdf->writeHTML($tbl, true, false, false, false, '');
            }
         }

         if(
            ($batch_details["store_id"]!=6 && ($batch_details["status"]=='approved-by-category' || 
            $batch_details["status"]=='approved-by-corp-buyer')) ||
            ($batch_details["store_id"]==6 && ($approver=="Category-Head" && 
            ($batch_details["status"]=='approved-by-buyer' || $batch_details["status"]=='approved-by-category')) ||
            ($approver=="Corp-Manager" && 
            ($batch_details["status"]=='approved-by-category' || $batch_details["status"]=='approved-by-corp-manager')))
         ){
            $img_sign = $this->Po_mod->getSignatureImage();
            $this->ppdf->Image(base_url().$img_sign, 80, 160, 20, 20, 'PNG');
         }
         
         $corp_emp_id = $this->Acct_mod->getUserDetailsById(16)["emp_id"];
         $corp_name = $this->Acct_mod->retrieveEmployeeName($corp_emp_id)["name"];

         $incorp_emp_id = $this->Acct_mod->getUserDetailsById(12)["emp_id"];
         $incorp_name = $this->Acct_mod->retrieveEmployeeName($incorp_emp_id)["name"];

         $this->ppdf->SetXY(60, 170); // x,y
         $this->ppdf->Cell(60, 10, $corp_name, 0, 1,'C');
         $this->ppdf->SetXY(210, 170); // x,y
         $this->ppdf->Cell(60, 10, $incorp_name, 0, 1,'C');

         $this->ppdf->SetFont('', 'B', 10);
         $this->ppdf->SetXY(60, 175); // x,y
         $this->ppdf->Cell(60, 10, 'CORPORATE MANAGER', 0, 1,'C');
         $this->ppdf->SetXY(210, 175); // x,y
         $this->ppdf->Cell(60, 10, 'INCORPORATOR', 0, 1,'C');

         ob_end_clean();
         echo $this->ppdf->Output();
      }

    
}

?>