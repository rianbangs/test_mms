<?php

class Po_mod extends CI_Model{

     function __construct(){
        parent::__construct();
        $this->load->model('Acct_mod');
        $this->cas = $this->load->database('cas', TRUE);
        $this->mw = $this->load->database('middleware', TRUE);
       
     }

     function insertToTable($table,$insert_array){
          $this->db->insert($table,$insert_array);
          $insert_id = $this->db->insert_id();
          return $insert_id;
     }

     function insertBatchToTable($table,$insert_batch){
          $this->db->insert_batch($table,$insert_batch);
     }

     function updateTable($table,$update_array,$where_array){
          $this->db->update($table,$update_array,$where_array);
     }

     function updateBatchToTable($table,$update_array,$where_str){
          $this->db->update_batch($table,$update_array,$where_str);
     }

     function getStore($store_id){
          $query = $this->db->query("SELECT * FROM reorder_store WHERE store_id=?", array($store_id));
          $row = $query->row_array();
          return $row;
     }

     function getStoreList($store_id){
          $cmd = "SELECT a.store_no, b.name, b.group_ FROM `database` a INNER JOIN reorder_store b ON a.db_id=b.databse_id 
               WHERE b.store_id=?";
          $query = $this->db->query($cmd, array($store_id));
          
          if($store_id==2){ // ASC SM - SOD
               $cmd = "SELECT a.store_no, b.name, b.group_ FROM `database` a INNER JOIN reorder_store b ON a.db_id=b.databse_id 
               WHERE b.store_id=? OR a.store_no=?";
               $query = $this->db->query($cmd, array($store_id,"ASC-S0015"));

          }else if($store_id==3){ // PM SM - SOD
               $cmd = "SELECT a.store_no, b.name, b.group_ FROM `database` a INNER JOIN reorder_store b ON a.db_id=b.databse_id 
               WHERE b.store_id=? OR a.store_no=?";
               $query = $this->db->query($cmd, array($store_id,"PM-S0015"));

          }else if($store_id==6){ // CDC
               $cmd = "SELECT a.store_no, b.name, b.group_ FROM `database` a INNER JOIN reorder_store b 
                    ON a.db_id=b.databse_id WHERE b.group_ IN ('SM','SOD')";
               $query = $this->db->query($cmd);   
          }

          return $query->result_array();
     }

     function getStoreID($store_no){
          $store_id = 0;
          $cmd = "SELECT b.store_id FROM `database` a INNER JOIN reorder_store b ON a.db_id=b.databse_id 
                WHERE a.store_no=?";
          $query = $this->db->query($cmd, array($store_no));
          $row = $query->row_array();
          if(isset($row))
               $store_id = $row["store_id"];

          return $store_id;
     }

     function getStoreIDByDb($db_id){
          $store_id = 0;
          $cmd = "SELECT store_id FROM reorder_store WHERE databse_id=?";
          $query = $this->db->query($cmd, array($db_id));
          $row = $query->row_array();
          if(isset($row))
               $store_id = $row["store_id"];

          return $store_id;
     }

     function getSelectedStores($store_ids){
          $placeholders = implode(',', array_fill(0, count($store_ids), '?'));
          $cmd = "SELECT store_id, display_name FROM reorder_store WHERE store_id IN (".$placeholders.")";
          $query = $this->db->query($cmd, $store_ids);
          return $query->result_array();
     }

     function getDbIdsNonStores(){
          $db_ids = array();
          $cmd = "SELECT databse_id FROM reorder_store WHERE bu_type=?";
          $query = $this->db->query($cmd,array('NON STORE'));
          $result = $query->result_array();
          foreach($result as $row){
               $db_ids[] = $row["databse_id"];
          }

          return $db_ids;
     }

      function getDbDetailsNonStores(){
          $db_ids = array();
          $cmd = "SELECT databse_id, display_name FROM reorder_store WHERE bu_type=?";
          $query = $this->db->query($cmd,array('NON STORE'));
          $result = $query->result_array();
          foreach($result as $row){
               $db_ids_["databse_id"] = $row["databse_id"];
               $db_ids_["display_name"] = $row["display_name"];
               $db_ids[] = $db_ids_;
          }

          return $db_ids;
     }

     function getStoreIdsByGrps($group_){
          $grp = array();
          $cmd = "SELECT store_id FROM reorder_store WHERE group_=?";
          $query = $this->db->query($cmd,array($group_));
          $result = $query->result_array();
          foreach($result as $row){
               $grp[] = $row["store_id"];
          }

          return $grp;
     }

     function getGrpByStoreId($store_id){
          $grp = '';
          $cmd = "SELECT group_ FROM reorder_store WHERE store_id=?";
          $query = $this->db->query($cmd,array($store_id));
          $row = $query->row_array();
          if(isset($row)){
               $grp = $row["group_"];
          }

          return $grp;
     }

     function getPoDir($db_id){
          $query = $this->db->query("SELECT * FROM po_directory WHERE db_id=?;", array($db_id));
          return $query->row_array();
     }

     // PO Calendar
     function getUserStore(){
          $store = "";
          $query = $this->db->query("SELECT value_ FROM reorder_store INNER JOIN reorder_users ON reorder_store.store_id=reorder_users.store_id WHERE user_id=?;", array($_SESSION['user_id']));
        
          $row = $query->row_array();
          if(isset($row)){
               $store = $row["value_"];
          }

          return $store;

     }

     function getVendorTypeFromCas($vendor_code){
          $type = "";
          $query = $this->cas->query("SELECT type_ FROM nav_vendor WHERE vendor_code=?", $vendor_code);
          $row = $query->row_array();
          if(isset($row)){
               $type = $row["type_"];
          }

          return $type;

     }

     function getPoID($no_){
          $id = 0;
          $query = $this->db->query("SELECT po_id FROM po_calendar WHERE no_=?",array($no_));
          $row = $query->row_array();
          if(isset($row))
               $id = $row["po_id"];

          return $id;
     }

     function getPoCountNullById($id){
          $query = $this->db->query("SELECT COUNT(*) AS count_ FROM po_calendar WHERE po_id=? AND address IS NULL",array($id));
          return $query->row_array()["count_"];
     }

     function retrievePoCalendar($end_date,$group_code){
          $cmd = "SELECT no_, name_, start_date, end_date, frequency FROM po_calendar a INNER JOIN po_date b ON a.po_id=b.po_id WHERE 
               start_date<=? AND group_code=?";                     
          $query = $this->db->query($cmd,array($end_date,$group_code));
          return $query->result_array();
     }

     function getMaxDate($no_){
          $max_date = "";
          $cmd = "SELECT MAX(start_date) AS max_date FROM po_calendar a INNER JOIN po_date b ON a.po_id=b.po_id WHERE no_=?";
          $query = $this->db->query($cmd, array($no_));
          $row = $query->row_array();
        
          if(isset($row)){
               $max_date = $row["max_date"];
          }

          return $max_date;
     }

     function getVendorApprover($vendor_code){
          $approver = '';
          $cmd = "SELECT approver FROM po_calendar WHERE no_=?";
          $query = $this->db->query($cmd, array($vendor_code));
          $row = $query->row_array();
        
          if(isset($row)){
               $approver = $row["approver"];
          }

          return $approver;
     }

     function getReorderBatch($supplier_code,$date_tag,$is_count=false){ // Buyer
          $columns = ($is_count) ? 'count(*) AS count_' : 'a.reorder_batch';
          $cmd = "SELECT ".$columns." FROM reorder_report_data_batch a 
               INNER JOIN reorder_report_data_header_final b ON a.reorder_batch=b.reorder_batch 
               INNER JOIN reorder_store c ON a.store_id=c.store_id 
               WHERE supplier_code=? AND date_tag=? AND b.store=c.value_ AND user_id=? AND status!=?";                
          
          $query = $this->db->query($cmd,array($supplier_code,$date_tag,$_SESSION["user_id"],'ARCHIVE'));
          if($is_count){
               return $query->row_array()["count_"];
          }else{
               return $query->result_array();
          }
     }

     function getReorderBatches($supplier_code,$date_tag,$group_code,$cmd_ind,$is_count=false){ 
          // Corp Buyer, Category-Head, Corp-Manager, Store Buyer 
          $columns = ($is_count) ? 'count(*) AS count_' : 'a.reorder_batch, a.user_id, e.emp_id, a.date_generated, a.status';
          
          $cmd_arr = array(
               'SELECT '.$columns.' FROM reorder_report_data_batch a 
               INNER JOIN reorder_report_data_header_final b ON a.reorder_batch=b.reorder_batch 
               INNER JOIN reorder_store c ON a.store_id=c.store_id 
               INNER JOIN reorder_users e ON a.user_id=e.user_id 
               WHERE supplier_code=? AND date_tag=? AND b.store=c.value_ AND group_code_=? AND status!="ARCHIVE"',

               'SELECT '.$columns.' FROM reorder_report_data_batch a 
               INNER JOIN reorder_report_data_header_final b ON a.reorder_batch=b.reorder_batch 
               INNER JOIN reorder_store c ON a.store_id=c.store_id
               INNER JOIN reorder_users d ON a.store_id=d.store_id 
               INNER JOIN reorder_users e ON a.user_id=e.user_id 
               WHERE supplier_code=? AND date_tag=? AND b.store=c.value_ AND group_code_=?   
               AND d.user_id='.$_SESSION['user_id'].' AND a.status NOT IN ("ARCHIVE","pending")',

               'SELECT '.$columns.' FROM reorder_report_data_batch a 
               INNER JOIN reorder_report_data_header_final b ON a.reorder_batch=b.reorder_batch 
               INNER JOIN reorder_store c ON a.store_id=c.store_id
               INNER JOIN reorder_users e ON a.user_id=e.user_id 
               WHERE supplier_code=? AND date_tag=? AND b.store=c.value_ AND group_code_=? 
               AND a.status NOT IN ("ARCHIVE","pending","Approved by-buyer")',

               'SELECT '.$columns.' FROM reorder_report_data_batch a 
               INNER JOIN reorder_report_data_header_final b ON a.reorder_batch=b.reorder_batch 
               INNER JOIN reorder_store c ON a.store_id=c.store_id 
               INNER JOIN reorder_users e ON a.user_id=e.user_id 
               WHERE supplier_code=? AND date_tag=? AND b.store=c.value_ AND group_code_=? AND status!="ARCHIVE" 
               AND e.user_id='.$_SESSION['user_id']
          );                
          
          $query = $this->db->query($cmd_arr[$cmd_ind],array($supplier_code,$date_tag,$group_code));
          if($is_count){
               $result = $query->row_array()["count_"];
          }else{
               $list = $query->result_array();
               $result = array();
               foreach($list as $item){
                    $items["name"] = $this->Acct_mod->retrieveEmployeeName($item["emp_id"])["name"];
                    $items["date_generated"] = date("F d, Y -- h:i a", strtotime($item["date_generated"]));
                    $items["status"] = $item["status"];
                    $items["batch_id"] = $item["reorder_batch"];
                    $result[] = $items;
               }
          }

          return $result;
     }

     function getUserGroupCodes(){ 
          $vendors = array();
          $cmd = "SELECT user_type, group_code FROM reorder_users WHERE user_id=?";                
          $query = $this->db->query($cmd,array($_SESSION["user_id"]));
          $row = $query->row_array();
          
          if(isset($row)){
               if($row["user_type"]=="corp-manager" || $row["user_type"]=="super-admin" || $row["user_type"]=="dept-admin"){
                    $vendors = $this->retrieveGroupCodes();

               }else{
                   $list = explode(",", $row["group_code"]);
                    for($c=0; $c<count($list); $c++){
                         $vendors[] = $list[$c];    
                    }  
               }
                 
          }

          return $vendors;
     }

     function retrieveGroupCodes(){
          $cmd = "SELECT group_code FROM vendor_category";
          $query = $this->db->query($cmd);
          $list = $query->result_array();
          
          $group_codes = array();          
          foreach($list as $gc){
               $group_codes[] = $gc["group_code"];
          }

          return $group_codes;
     }

     function retrieveManagerKeys($group_code,$store_id){
          $cmd = "SELECT a.username AS m_user, a.password AS m_pass FROM manager_key a 
               INNER JOIN reorder_users b ON a.user_id=b.user_id
               WHERE group_code LIKE ? AND user_type=? AND store_id=?";

          $query = $this->db->query($cmd, array("%".$group_code."%","category-head",$store_id));
          return $query->result_array();
     }

     function retrieveVendor(){ // Super-Admin
          $cmd = "SELECT a.po_id AS id_, no_, name_, GROUP_CONCAT(start_date,':',frequency,':',group_code SEPARATOR '|') 
               AS po_details, approver, vend_type FROM po_calendar a INNER JOIN po_date b ON a.po_id=b.po_id GROUP BY no_";                
          $query = $this->db->query($cmd);
          return $query->result_array();
     }

     function getPoDates($po_id){ // Super-Admin
          $cmd = "SELECT group_code, frequency, start_date, end_date FROM po_date WHERE po_id=?";                
          $query = $this->db->query($cmd,array($po_id));
          return $query->result_array();
     }

     function countVendors(){ 
          $cmd = "SELECT a.po_id AS po_ids FROM po_calendar a INNER JOIN po_date b ON a.po_id=b.po_id GROUP BY no_";                
          $query = $this->db->query($cmd);
          return count($query->result_array());
     }

     function retrieveVendorInfo($vendor_code,$group_code){
          $cmd = "SELECT * FROM po_calendar a INNER JOIN po_date b ON a.po_id=b.po_id WHERE a.no_=? AND b.group_code=? 
               ORDER BY b.pd_id DESC LIMIT 1";                
          $query = $this->db->query($cmd,array($vendor_code,$group_code));
          return $query->row_array();
     }

     function getLastPo($po_id){
          $cmd = "SELECT * FROM po_date WHERE po_id=? ORDER BY pd_id DESC LIMIT 1";                
          $query = $this->db->query($cmd,array($po_id));
          return $query->row_array();
     }

     //Season
     function getSeasonTypes(){ // Dept-Admin
          $cmd = "SELECT * FROM season_type";                
          $query = $this->db->query($cmd);
          
          $result = array();
          $list = $query->result_array();
          
          foreach($list as $item){
               $items["type_id"] = $item["type_id"];
               $items["season_name"] = $item["season_name"];
               $items["season_type"] = $item["type_val"];
               
               $period_start_arr = explode("-",$item["period_start"]);
               $period_end_arr = explode("-",$item["period_end"]);
               $monthStartName = date('F', mktime(0, 0, 0, $period_start_arr[0], 1));
               $monthEndName = date('F', mktime(0, 0, 0, $period_end_arr[0], 1));
               $period_covered = $monthStartName." to ".$monthEndName;
               
               if(count($period_start_arr)>1){
                    $period_covered = $monthStartName." ".$period_start_arr[1]." to ".$monthEndName." ".$period_end_arr[1];
               }
                    
               $items["period_covered"] = $period_covered;
               $items["percentage"] = $item["percentage"];
               $items["no_ref_year"] = $item["no_ref_year"];
               $result[] = $items;

          }

          return $result;
     }

     function getSeasonTypesById($type_id){ // Dept-Admin
          $cmd = "SELECT * FROM season_type WHERE type_id=?";                
          $query = $this->db->query($cmd,array($type_id));
          
          $items = array();
          $item = $query->row_array();
          
          if(isset($item)){
               $items["type_id"] = $item["type_id"];
               $items["season_name"] = $item["season_name"];
               $items["season_type"] = $item["type_val"];
               
               $period_start_arr = explode("-",$item["period_start"]);
               $period_end_arr = explode("-",$item["period_end"]);
               $monthStartName = date('F', mktime(0, 0, 0, $period_start_arr[0], 1));
               $monthEndName = date('F', mktime(0, 0, 0, $period_end_arr[0], 1));
               $period_covered = $monthStartName." to ".$monthEndName;
               
               if(count($period_start_arr)>1){
                    $period_covered = $monthStartName." ".$period_start_arr[1]." to ".$monthEndName." ".$period_end_arr[1];
               }
               
               $items["month_start"]  = $period_start_arr[0];   
               $items["month_end"]  = $period_end_arr[0];   
               $items["period_covered"] = $period_covered;
               $items["percentage"] = $item["percentage"];
               $items["no_ref_year"] = $item["no_ref_year"];
          }

          return $items;
     }

     function getSeasonDetails($type_id){
          $cmd = "SELECT * FROM season_type WHERE type_id=?";                
          $query = $this->db->query($cmd,array($type_id));
          return $query->row_array();
     }

     function getSeasonalVendorsByUser(){ 
          $user_group_codes = $this->getUserGroupCodes();
          $in_arr = '(';
          for($c=0; $c<count($user_group_codes); $c++){
               $in_arr .= '"'.$user_group_codes[$c].'"';
               if($c<count($user_group_codes)-1)
                    $in_arr .= ',';
          }
          $in_arr .= ')';

          $vendor_codes = $this->getSeasonalVendors();
          
          $in_arr2 = '(';
          $c = 0;
          foreach($vendor_codes as $v_row) {
               $in_arr2 .= '"'.$v_row["vendor_code"].'"';
               if($c<count($vendor_codes)-1)
                    $in_arr2 .= ',';

               $c++;
          }
          $in_arr2 .= ')';

          $cmd = "SELECT no_, name_ FROM po_calendar a INNER JOIN po_date b ON a.po_id=b.po_id 
               WHERE group_code IN ".$in_arr." AND no_ IN ".$in_arr2." GROUP BY no_";                
          $query = $this->db->query($cmd);
          return $query->result_array();
     }

     function getCountOfSeasonalItem($vendor_code,$item_no){ // Dept-Admin
          $count = 0;
          $cmd = "SELECT COUNT(*) as count_ FROM seasonal_item WHERE vendor_code=? AND item_no=?";                
          $query = $this->db->query($cmd,array($vendor_code,$item_no));
          $row = $query->row_array();
          if(isset($row))
               $count = $row["count_"];

          return $count;
     }

     function getCountOfTagItem($item_id,$type_id){ // Dept-Admin
          $count = 0;
          $cmd = "SELECT COUNT(*) as count_ FROM seasonal_item_tag WHERE item_id=? AND type_id=?";                
          $query = $this->db->query($cmd,array($item_id,$type_id));
          $row = $query->row_array();
          if(isset($row))
               $count = $row["count_"];

          return $count;
     }

     function getItemPurchUom($item_no){ // Dept-Admin
          $cmd = "SELECT code FROM nav_uom_header WHERE item_no=? ORDER BY qty_per_unit_of_measure DESC LIMIT 1";                
          $query = $this->db->query($cmd,array($item_no));
          $row = $query->row_array();
          return isset($row) ? $row["code"] : "";
     }

     function getSeasonalItemDetailById($item_id){
          $cmd = "SELECT * FROM seasonal_item WHERE item_id=?";                
          $query = $this->db->query($cmd,array($item_id));
          return $query->row_array();
     }

     function getSeasonalItemPurchUom($vendor_code,$item_no,$type_id){
          $uom = "";
          $cmd = "SELECT a.purch_uom FROM seasonal_item a INNER JOIN seasonal_item_tag b ON a.item_id=b.item_id 
               WHERE a.vendor_code=? AND a.item_no=? AND b.type_id=? AND b.is_active='yes'";                
          $query = $this->db->query($cmd, array($vendor_code,$item_no,$type_id));
          $row = $query->row_array();
          if(isset($row)){
               $uom = $row["purch_uom"];
          }

          return $uom;
     }

     function getItemPurchUoms($item_no){ // Dept-Admin
          $cmd = "SELECT code AS uom FROM nav_uom_header WHERE item_no=?";                
          $query = $this->db->query($cmd,array($item_no));
          return $query->result_array();
     }

     function getIdOfSeasonalItem($vendor_code,$item_no){ // Dept-Admin
          $id = 0;
          $cmd = "SELECT item_id FROM seasonal_item WHERE vendor_code=? AND item_no=?";                
          $query = $this->db->query($cmd,array($vendor_code,$item_no));
          $row = $query->row_array();
          if(isset($row))
               $id = $row["item_id"];

          return $id;
     }

     function getSeasonTypesDirect(){ // Dept-Admin
          $cmd = "SELECT * FROM season_type";                
          $query = $this->db->query($cmd);
          return $query->result_array();
     }

     function getSeasonTypesDirectById($id){ // Dept-Admin
          $cmd = "SELECT * FROM season_type WHERE type_id=?";                
          $query = $this->db->query($cmd,array($id));
          return $query->row_array();
     }

     function getSeasonalItems(){ // Dept-Admin
          $cmd = "SELECT a.item_id, vendor_code, item_no, item_desc, GROUP_CONCAT(season_name SEPARATOR ', ') AS season_, 
                    purch_uom FROM seasonal_item a LEFT JOIN seasonal_item_tag b ON a.item_id=b.item_id 
                    INNER JOIN season_type c ON b.type_id=c.type_id WHERE b.is_active = 'yes' GROUP BY vendor_code, item_no";                
          $query = $this->db->query($cmd);
          return $query->result_array();
     }

     function countSeasonalItems(){ // Dept-Admin
          $cmd = "SELECT a.item_id AS item_ids FROM seasonal_item a LEFT JOIN seasonal_item_tag b ON a.item_id=b.item_id 
                    INNER JOIN season_type c ON b.type_id=c.type_id WHERE b.is_active = 'yes' GROUP BY vendor_code, item_no";             
          $query = $this->db->query($cmd);
          return count($query->result_array());
     }

     function getSeasonalVendors(){
          $cmd = "SELECT DISTINCT vendor_code FROM seasonal_item";                
          $query = $this->db->query($cmd);
          return $query->result_array();
     }

     function getSeasonTypesOfItem($item_id){
          $cmd = "SELECT * FROM seasonal_item_tag WHERE item_id=?";                
          $query = $this->db->query($cmd,array($item_id));
          return $query->result_array();
     }

     
     function getVendorDetailsLast($vendor_code){ // Last Date
          $cmd = "SELECT no_, name_, group_code FROM po_calendar a INNER JOIN po_date b ON a.po_id=b.po_id 
               WHERE no_=? ORDER BY b.start_date DESC LIMIT 1";                
          $query = $this->db->query($cmd,array($vendor_code));
          return $query->row_array();
          
     }

     // New Code: 12/04/2023
     function getVendorTypeByNo($vendor_code){
          $vend_type = "";
          $cmd = "SELECT vend_type FROM po_calendar WHERE no_=?";                
          $query = $this->db->query($cmd,array($vendor_code));
          $row = $query->row_array();
          if(isset($row))
               $vend_type = $row["vend_type"];

          return $vend_type;
     }

     function getIDSeasonReorderStoreEntry($store_id,$entry_id){
          $id = 0;
          $cmd = "SELECT store_entry_id FROM season_reorder_store_entry WHERE store_id=? AND entry_id=?";                
          $query = $this->db->query($cmd,array($store_id,$entry_id));
          $row = $query->row_array();
          if(isset($row)){
               $id = $row["store_entry_id"];
          }

          return $id;
     }

     function getIDSeasonReorderItemEntry($item_no,$variant_code,$batch_id){
          $id = 0;
          $cmd = "SELECT entry_id FROM season_reorder_item_entry WHERE item_no=? AND variant_code=? AND batch_id=?";                
          $query = $this->db->query($cmd,array($item_no,$variant_code,$batch_id));
          $row = $query->row_array();
          if(isset($row)){
               $id = $row["entry_id"];
          }

          return $id;
     }

     function getForecastHeaders($batch_id){
          $cmd = "SELECT GROUP_CONCAT(DISTINCT c.year_ref) AS years, GROUP_CONCAT(DISTINCT c.month_ref) AS months, 
               GROUP_CONCAT(DISTINCT d.display_name ORDER BY d.store_id) AS stores FROM season_reorder_item_entry a
               INNER JOIN season_reorder_store_entry b ON b.entry_id=a.entry_id
               INNER JOIN season_reorder_reference c ON c.store_entry_id=b.store_entry_id
               INNER JOIN reorder_store d ON d.store_id=b.store_id
               WHERE a.batch_id=?";

          $query = $this->db->query($cmd,array($batch_id));
          return $query->row_array();
     }

     function getForecastLines($batch_id,$perc){
          $cmd = "SELECT a.item_no, a.item_desc, a.variant_code, d.display_name, c.year_ref, c.month_ref, c.amount
               FROM season_reorder_item_entry a
               INNER JOIN season_reorder_store_entry b ON b.entry_id=a.entry_id
               INNER JOIN season_reorder_reference c ON c.store_entry_id=b.store_entry_id
               INNER JOIN reorder_store d ON d.store_id=b.store_id
               WHERE a.batch_id=?";

          $q1 = $this->db->query('SELECT DISTINCT item_no, variant_code FROM ('.$cmd.') AS forecast_tbl', array($batch_id));
          $item_list = $q1->result_array();
          
          $result = array();

          foreach($item_list as $item_no_){     
               $item_no = $item_no_["item_no"];
               $variant_code = $item_no_["variant_code"];
               $q2 = $this->db->query('SELECT * FROM ('.$cmd.') AS forecast_tbl WHERE item_no=? AND variant_code=?', array($batch_id,$item_no,$variant_code));

               $item_qty = $q2->result_array();
               $row = array();
               
               // Qty Columns
               foreach($item_qty as $item_qty_){
                    if(!in_array($item_qty_["item_no"],$row)){
                         $row["item_no"] = $item_qty_["item_no"];
                         $item_variant = (empty($item_qty_["variant_code"])) ? "" : " (".$item_qty_["variant_code"].")"; 
                         $row["item_desc"] = $item_qty_["item_desc"].$item_variant;
                    }

                    $key_name = $item_qty_["display_name"].'_'.$item_qty_["year_ref"].'_'.$item_qty_["month_ref"];
                    $row[$key_name] = $item_qty_["amount"]; // Qty
               }

               $cmd_ans = 'SELECT display_name, month_ref, GROUP_CONCAT(amount) AS grp, 
                         SUM(amount) AS total, COUNT(DISTINCT year_ref) AS year_count, 
                         (?/100) AS percent, ROUND((SUM(amount)/COUNT(DISTINCT year_ref))*((?/100)+1)) AS answer 
                         FROM ('.$cmd.')AS forecast_tbl WHERE item_no=? GROUP BY display_name, month_ref';

               $q3 = $this->db->query($cmd_ans, array($perc,$perc,$batch_id,$item_no));
               $item_ans = $q3->result_array();
               
               // Answer Columns
               foreach($item_ans as $item_ans_){
                    $key_name = $item_ans_["display_name"].'_'.date('Y').'_'.$item_ans_["month_ref"];
                    $row[$key_name] = $item_ans_["answer"];
                    $key_name = 'total_'.$item_ans_["month_ref"];

                    if(isset($row[$key_name])){ // Total Forecast
                         $row[$key_name] += $item_ans_["answer"];
                    }else{
                         $row[$key_name] = $item_ans_["answer"];  
                    }
               }

               $result[] = $row;
          }
          
          return $result;
     }

     function getRefYearSalesStores($stores,$season,$vendor,$years){
          $in_arr1 = '(';
          $in_arr2 = '(';
          $where_dates = '(';

          for($c=0; $c<count($stores); $c++){
               $in_arr1 .= '"'.$stores[$c].'"';

               if($c<count($stores)-1)
                    $in_arr1 .= ',';
          }
          $in_arr1 .= ')'; // ("ICM-S0001","ASC-S0001")
          

          for($c=0; $c<count($years); $c++){
               $in_arr2 .= '"'.$years[$c].'"';

               $where_dates .= "(STR_TO_DATE(a.cons_date, '%m-%d-%y') BETWEEN CONCAT('".$years[$c]."-',d.period_start) AND CONCAT('".$years[$c]."-',d.period_end))";

               if($c<count($years)-1){
                    $in_arr2 .= ',';
                    $where_dates .= ' OR ';
               }
          }
          $in_arr2 .= ')'; // ("2022","2019")
          $where_dates .= ')';

          $season_type = $this->getSeasonTypesDirectById($season)["type_val"];

          if($season_type=="Monthly"){
               $cmd =    "SELECT
                              a.item_no, b.item_desc, b.purch_uom as unit_of_measure, a.variant_code, SUM(a.quantity) AS sales,  
                              GROUP_CONCAT(a.quantity SEPARATOR '|') AS grp, a.store, g.store_id,
                              YEAR(STR_TO_DATE(a.cons_date, '%m-%d-%y')) AS year,MONTH(STR_TO_DATE(a.cons_date, '%m-%d-%y')) AS month,
                              DATE_FORMAT(STR_TO_DATE(a.cons_date, '%m-%d-%y'), '%M') AS month_name, d.percentage, d.season_name

                              FROM nav_cons_header a INNER JOIN seasonal_item b ON a.item_no=b.item_no 
                              INNER JOIN seasonal_item_tag c ON b.item_id=c.item_id
                              INNER JOIN season_type d ON d.type_id=c.type_id
                              INNER JOIN reorder_store g ON g.value_=a.store
                         WHERE
                              b.vendor_code=? AND c.type_id=? AND c.is_active='yes' 
                         AND
                              YEAR(STR_TO_DATE(a.cons_date, '%m-%d-%y')) IN ".$in_arr2."
                         AND
                              MONTH(STR_TO_DATE(a.cons_date, '%m-%d-%y')) BETWEEN d.period_start AND d.period_end
                         -- AND
                              -- NOT (MONTH(STR_TO_DATE(a.cons_date, '%m-%d-%y')) = MONTH(CURDATE()))       
                         AND
                              a.store_no IN ".$in_arr1."
                                               
                              GROUP BY year, month, a.store_no, a.item_no, a.variant_code ORDER BY year, month";

          }else{ // Daily
               $cmd =    "SELECT
                              a.item_no, b.item_desc, b.purch_uom as unit_of_measure, a.variant_code, SUM(a.quantity) AS sales,  
                              GROUP_CONCAT(a.quantity SEPARATOR '|') AS grp, a.store, g.store_id,
                              YEAR(STR_TO_DATE(a.cons_date, '%m-%d-%y')) AS year,MONTH(STR_TO_DATE(a.cons_date, '%m-%d-%y')) AS month,
                              DATE_FORMAT(STR_TO_DATE(a.cons_date, '%m-%d-%y'), '%M') AS month_name, d.percentage, d.season_name

                              FROM nav_cons_header a INNER JOIN seasonal_item b ON a.item_no=b.item_no 
                              INNER JOIN seasonal_item_tag c ON b.item_id=c.item_id
                              INNER JOIN season_type d ON d.type_id=c.type_id
                              INNER JOIN reorder_store g ON g.value_=a.store
                         WHERE
                              b.vendor_code=? AND c.type_id=? AND c.is_active='yes'  
                         AND
                              ".$where_dates."
                         -- AND
                              -- NOT (MONTH(STR_TO_DATE(cons_date, '%m-%d-%y')) = MONTH(CURDATE()))       
                         AND
                              a.store_no IN ".$in_arr1."
                                               
                              GROUP BY year, month, a.store_no, a.item_no, a.variant_code ORDER BY year, month";
          }

          $q1 = $this->db->query($cmd, array($vendor,$season));
          $list = $q1->result_array();
          
          foreach($list as &$item){
               $qty_uom = $this->getQtyUomFromNav($item["item_no"],$item["unit_of_measure"]); 
               $item["uom_qty"] = $qty_uom;
               if($item["uom_qty"]!=0){
                    $item["total"] = round($item["sales"]/$item["uom_qty"],0);
               }else{
                    $top_uom = $this->getTopUomFromNav($item["item_no"]); // uom, qty_uom
                    $item["unit_of_measure"] = $top_uom["uom"];
                    $item["total"] = round($item["sales"]/$top_uom["qty_uom"],0);
               }
          }

          return $list;
     }

     // Season Reorder List
     function retrieveSeasonReorderBatchByUser($opt,$is_count=false){
          $exc = '!';
          if($opt=="approved")
               $exc = '';
          
          $sel = "*";
          if($is_count)
               $sel = "COUNT(*) AS count_";

          $cmd = "SELECT ".$sel." FROM season_reorder_batch WHERE user_id=? AND status".$exc."=?";

          $query = $this->db->query($cmd,array($_SESSION["user_id"],'approved-by-corp-buyer'));
          return $query->result_array();
     }

     function retrieveSeasonReorderBatchByCdcSelf($opt,$is_count=false){
          $stat = ($opt=="approved") ? 'yes':'no';
     
          $sel = "*";
          if($is_count)
               $sel = "COUNT(*) AS count_";

          $cmd = "SELECT ".$sel." FROM season_reorder_batch WHERE store_id=6 AND user_id=? AND is_finalized=?";

          $query = $this->db->query($cmd,array($_SESSION["user_id"],$stat));
          return $query->result_array();
     }

     function retrieveSeasonReorderBatchByCategoryHead($store_id,$opt,$is_count=false){
          
          $user_group_codes = $this->getUserGroupCodes();
          $in_arr = '(';
          for($c=0; $c<count($user_group_codes); $c++){
               $in_arr .= '"'.$user_group_codes[$c].'"';
               if($c<count($user_group_codes)-1)
                    $in_arr .= ',';
          }
          $in_arr .= ')';

          
          if($store_id==6){ // CDC Category
               $approve_by = '("approved-by-buyer","approved-by-category","disapproved-by-category","disapproved-by-corp-manager",
                         "forwarded-to-incorp","disapproved-by-incorp") AND is_finalized="no"';

               if($opt=="approved"){
                    $approve_by = '("approved-by-category","approved-by-corp-manager","approved-by-incorp") AND is_finalized="yes"';
               }

          }else{ // Store
               $approve_by = '("approved-by-buyer","approved-by-category","disapproved-by-category","disapproved-by-corp-buyer")';

               if($opt=="approved"){
                    $approve_by = '("approved-by-corp-buyer")';
               }
          }

          $sel = "*";
          if($is_count)
               $sel = "COUNT(*) AS count_";

          $cmd = "SELECT ".$sel." FROM season_reorder_batch WHERE store_id=? AND status IN ".$approve_by." AND group_code IN ".$in_arr;  
          $query = $this->db->query($cmd,array($store_id));
          return $query->result_array();
     }

     function retrieveSeasonReorderBatchByCdcBuyer($opt,$is_count=false){
          $user_group_codes = $this->getUserGroupCodes();
          $in_arr = '(';
          for($c=0; $c<count($user_group_codes); $c++){
               $in_arr .= '"'.$user_group_codes[$c].'"';
               if($c<count($user_group_codes)-1)
                    $in_arr .= ',';
          }
          $in_arr .= ')';

          $approve_by = '("approved-by-category","disapproved-by-corp-buyer")';
          if($opt=="approved")
               $approve_by = '("approved-by-corp-buyer")';

          $sel = "*";
          if($is_count)
               $sel = "COUNT(*) AS count_";
          
          $cmd = "SELECT ".$sel." FROM season_reorder_batch WHERE store_id!=6 AND status IN ".$approve_by." AND group_code IN ".$in_arr;

          $query = $this->db->query($cmd);
          return $query->result_array();
     }

     function retrieveSeasonReorderBatchByCorpManager($opt,$is_count=false){
          $approve_by = '("forwarded-to-incorp","approved-by-category","disapproved-by-corp-manager","disapproved-by-incorp") 
                     AND is_finalized="no"';
          if($opt=="approved")
               $approve_by = '("approved-by-corp-manager","approved-by-incorp") AND is_finalized="yes"';

          $sel = "*";
          if($is_count)
               $sel = "COUNT(*) AS count_";

          $cmd = "SELECT ".$sel." FROM season_reorder_batch WHERE store_id=6 AND status IN ".$approve_by;

          $query = $this->db->query($cmd);
          return $query->result_array();
     }

     function retrieveSeasonReorderBatchByIncorporator($opt,$is_count=false){
          $approve_by = '("forwarded-to-incorp","disapproved-by-incorp")';
          if($opt=="approved")
               $approve_by = '("approved-by-incorp")';

          $sel = "*";
          if($is_count)
               $sel = "COUNT(*) AS count_";

          $cmd = "SELECT ".$sel." FROM season_reorder_batch WHERE store_id=6 AND status IN ".$approve_by;

          $query = $this->db->query($cmd);
          return $query->result_array();
     }

     function countSeasonReorderBatchByUser($batch_id){
          $cmd = "SELECT COUNT(*) as count_ FROM season_reorder_batch WHERE user_id=? AND batch_id=?";                
          $query = $this->db->query($cmd,array($_SESSION["user_id"],$batch_id));
          return $query->row_array()["count_"];
     }

     function countSeasonReorderBatchByCategoryHead($store_id,$batch_id){
          $user_group_codes = $this->getUserGroupCodes();
          $in_arr = '(';
          for($c=0; $c<count($user_group_codes); $c++){
               $in_arr .= '"'.$user_group_codes[$c].'"';
               if($c<count($user_group_codes)-1)
                    $in_arr .= ',';
          }
          $in_arr .= ')';

          $cmd = "SELECT COUNT(*) as count_ FROM season_reorder_batch WHERE store_id=? AND status!=? AND 
               group_code IN ".$in_arr." AND batch_id=?";                
          
          $query = $this->db->query($cmd,array($store_id,"pending",$batch_id));
          return $query->row_array()["count_"];
     }

     function countSeasonReorderBatchByCdcBuyer($batch_id){
          $cmd = "SELECT COUNT(*) as count_ FROM season_reorder_batch WHERE batch_id=?";                
          $query = $this->db->query($cmd,array($batch_id));
          return $query->row_array()["count_"];
     }

     function countSeasonReorderBatchByCorpManager($batch_id){
          $cmd = "SELECT COUNT(*) as count_ FROM season_reorder_batch WHERE store_id=6 AND status NOT IN 
               ('pending','approved-by-buyer','disapproved-by-category') AND batch_id=?";                
          $query = $this->db->query($cmd,array($batch_id));
          return $query->row_array()["count_"];
     }

     function countSeasonReorderBatchByIncorporator($batch_id){
          $cmd = "SELECT COUNT(*) as count_ FROM season_reorder_batch WHERE store_id=6 AND status IN 
               ('forwarded-to-incorp','approved-by-incorp','disapproved-by-incorp') AND batch_id=?";                
          $query = $this->db->query($cmd,array($batch_id));
          return $query->row_array()["count_"];
     }

     function getDistinctStores($batch_id){
          $cmd = "SELECT DISTINCT c.value_, c.store_id FROM season_reorder_item_entry a INNER JOIN season_reorder_store_entry b 
               ON a.entry_id=b.entry_id INNER JOIN reorder_store c ON c.store_id=b.store_id WHERE 
               a.batch_id=? ORDER BY c.store_id, c.group_";

          $query = $this->db->query($cmd,array($batch_id));
          return $query->result_array();
     }

     function getDistinctYears($batch_id){
          $years = array();
          $cmd =    "SELECT DISTINCT year_ref AS years FROM season_reorder_item_entry a INNER JOIN season_reorder_store_entry b 
                    ON a.entry_id=b.entry_id INNER JOIN season_reorder_reference c ON c.store_entry_id=b.store_entry_id WHERE 
                    a.batch_id=?";

          $query = $this->db->query($cmd,array($batch_id));
          $list = $query->result_array();
          foreach($list as $item){
               $years[] = $item["years"];
          }

          return $years;
     }

     function getDistinctMonths($batch_id){
          $months = array();
          $cmd =    "SELECT DISTINCT month_ref AS months FROM season_reorder_item_entry a INNER JOIN season_reorder_store_entry b 
                    ON a.entry_id=b.entry_id INNER JOIN season_reorder_reference c ON c.store_entry_id=b.store_entry_id WHERE 
                    a.batch_id=?";

          $query = $this->db->query($cmd,array($batch_id));
          $list = $query->result_array();
          foreach($list as $item){
               $months[] = $item["months"];
          }

          return $months;
     }

     function getStoresOfSeasonReorder($batch_id){
          $cmd = "SELECT b.store_id, value_ FROM reorder_store a INNER JOIN season_reorder_store_entry b 
               ON a.store_id=b.store_id WHERE b.batch_id=?";
          $query = $this->db->query($cmd,array($batch_id));
          return $query->result_array();
     }

     function retrieveSeasonReorderBatchCdc($batch_id){
          $cmd = "SELECT a.entry_id, a.item_no, a.item_desc, a.uom, a.variant_code,
               GROUP_CONCAT(DISTINCT b.store_id,':',b.qty_onhand SEPARATOR '|') AS qty_onhand,  
               GROUP_CONCAT(b.store_id,':',c.month_ref,'_',c.year_ref,':',c.amount SEPARATOR '|') AS sales, sum(c.amount) AS sum 
               FROM season_reorder_item_entry a INNER JOIN season_reorder_store_entry b ON a.entry_id=b.entry_id 
               INNER JOIN season_reorder_reference c ON c.store_entry_id=b.store_entry_id
               WHERE a.batch_id=? AND c.amount!=0 GROUP BY a.item_no, a.uom, a.variant_code ORDER BY a.item_no";

          $query = $this->db->query($cmd,array($batch_id));
          return $query->result_array();     
     }

     function retrieveSeasonReorderBatchStore($batch_id,$store_id=''){
          $ps = array($batch_id,$store_id);
          $q1 = "AND b.store_id=? ";
          if($store_id==''){
               $ps = array($batch_id);
               $q1 = "";
          }

          $cmd = "SELECT a.entry_id, a.item_no, a.item_desc, a.uom, a.variant_code, b.qty_onhand,  
               GROUP_CONCAT(DISTINCT c.year_ref SEPARATOR '|') AS years,
               GROUP_CONCAT(c.month_ref,'_',c.year_ref,':',c.amount SEPARATOR '|') AS sales, sum(c.amount) AS sum 
               FROM season_reorder_item_entry a INNER JOIN season_reorder_store_entry b ON a.entry_id=b.entry_id 
               INNER JOIN season_reorder_reference c ON c.store_entry_id=b.store_entry_id
               WHERE a.batch_id=? AND c.amount!=0 ".$q1."GROUP BY a.item_no, a.uom, a.variant_code ORDER BY a.item_no;";

          $query = $this->db->query($cmd,$ps);
          return $query->result_array();     
     }

     function retrieveSeasonReorderBatchParentCdc($batch_id){
          $cmd = "SELECT a.entry_id, a.item_no, a.item_desc, a.uom, a.variant_code,
               GROUP_CONCAT(DISTINCT b.store_id,':',b.qty_onhand SEPARATOR '|') AS qty_onhand,  
               GROUP_CONCAT(b.store_id,':',c.month_ref,'_',c.year_ref,':',c.amount SEPARATOR '|') AS sales, sum(c.amount) AS sum 
               FROM season_reorder_item_entry a INNER JOIN season_reorder_store_entry b ON a.entry_id=b.entry_id 
               INNER JOIN season_reorder_reference c ON c.store_entry_id=b.store_entry_id
               INNER JOIN season_reorder_batch d ON d.batch_id=a.batch_id
               WHERE d.batch_child=? AND c.amount!=0 GROUP BY a.item_no, a.uom, a.variant_code ORDER BY a.item_no";

          $query = $this->db->query($cmd,array($batch_id));
          return $query->result_array();     
     }

     function retrieveSeasonReorderBatchParentStore($batch_id){
          $cmd = "SELECT a.entry_id, a.item_no, a.item_desc, a.uom, a.variant_code, b.qty_onhand,  
               GROUP_CONCAT(DISTINCT c.year_ref SEPARATOR '|') AS years,
               GROUP_CONCAT(c.month_ref,'_',c.year_ref,':',c.amount SEPARATOR '|') AS sales, sum(c.amount) AS sum 
               FROM season_reorder_item_entry a INNER JOIN season_reorder_store_entry b ON a.entry_id=b.entry_id 
               INNER JOIN season_reorder_reference c ON c.store_entry_id=b.store_entry_id
               INNER JOIN season_reorder_batch d ON d.batch_id=a.batch_id
               WHERE d.batch_child=? AND c.amount!=0 GROUP BY a.item_no, a.uom, a.variant_code ORDER BY a.item_no;";

          $query = $this->db->query($cmd,array($batch_id));
          return $query->result_array();     
     }

     function retrieveSeasonReorderBatchItemEntries($batch_id){
          $cmd = "SELECT a.entry_id FROM season_reorder_item_entry a INNER JOIN season_reorder_store_entry b ON a.entry_id=b.entry_id 
               INNER JOIN season_reorder_reference c ON c.store_entry_id=b.store_entry_id
               WHERE a.batch_id=? AND c.amount!=0 GROUP BY a.item_no, a.uom, a.variant_code ORDER BY a.item_no";

          $query = $this->db->query($cmd,array($batch_id));
          return $query->result_array();     
     }

     function retrieveSeasonReorderBatchById($batch_id){
          $cmd = "SELECT * FROM season_reorder_batch WHERE batch_id=?";
          $query = $this->db->query($cmd,array($batch_id));
          return $query->row_array();
     }

     function retrieveSeasonReorderBatchParentById($batch_id){
          $cmd = "SELECT * FROM season_reorder_batch WHERE batch_child=?";
          $query = $this->db->query($cmd,array($batch_id));
          return $query->row_array();
     }

     function countSeasonReorderBatchChildById($batch_id){
          $cmd = "SELECT COUNT(*) AS count_ FROM season_reorder_batch WHERE batch_child=?";
          $query = $this->db->query($cmd,array($batch_id));
          return $query->row_array()["count_"];
     }

     // function retrieveSeasonReorderEntryById($batch_id){
     //      $cmd = "SELECT * FROM season_reorder_item_entry WHERE batch_id=?";                
     //      $query = $this->db->query($cmd,array($batch_id));
     //      return $query->result_array();
     // }

     function getSeasonReorderEntryById($entry_id){
          $cmd = "SELECT * FROM season_reorder_item_entry WHERE entry_id=?";                
          $query = $this->db->query($cmd,array($entry_id));
          return $query->row_array();
     }

     function retrieveSeasonReorderTablesJoined($batch_id,$not_in){ // not_in = item entries with 0 remaining forecasted qty
          
          $cmd = "SELECT a.entry_id, a.item_no, a.item_desc, a.uom, a.variant_code, b.qty_onhand, b.store_id, 
               c.year_ref, c.month_ref, c.amount,  
               GROUP_CONCAT(d.document_no,':',d.po_date,':',d.pending_qty,':',IFNULL(d.exp_del_date, 'none') SEPARATOR '|') as po_grp
               FROM season_reorder_item_entry a 
               INNER JOIN season_reorder_store_entry b ON b.entry_id=a.entry_id 
               INNER JOIN season_reorder_reference c ON c.store_entry_id=b.store_entry_id 
               LEFT JOIN season_reorder_pending_qty d ON d.store_entry_id=c.store_entry_id
               WHERE a.batch_id=? AND a.entry_id".$not_in."
               GROUP BY a.entry_id, b.store_id, c.year_ref, c.month_ref ORDER BY a.entry_id, b.store_id, c.year_ref";               
          $query = $this->db->query($cmd,array($batch_id));
          return $query->result_array();
     }

     // Reasons
     function getReorderReasons(){
          $cmd = "SELECT * FROM reorder_reasons";                
          $query = $this->db->query($cmd);
          return $query->result_array();
     }

     function getReorderReasonById($reason_id){
          $cmd = "SELECT * FROM reorder_reasons WHERE reason_id=?";                
          $query = $this->db->query($cmd,array($reason_id));
          return $query->row_array();
     }

     function getSeasonReorderStatusHistory($batch_id){
          $cmd = "SELECT status,date_set,emp_id FROM season_reorder_status_hist a INNER JOIN reorder_users b ON 
               a.user_id=b.user_id WHERE batch_id=?";                
          $query = $this->db->query($cmd,array($batch_id));
          return $query->result_array();
     }

     function getSeasonReorderEntryByAdjStatus($entry_id){
          $status = "";
          $cmd = "SELECT status FROM season_reorder_change_qty_hist WHERE entry_id=? ORDER BY date_inputted DESC LIMIT 1";                
          $query = $this->db->query($cmd,array($entry_id));
          $row = $query->row_array();
          if(isset($row))
               $status = $row["status"];

          return $status;
     }

     function getSeasonReorderEntryByAdjReason($entry_id){
          $reasons = '';
          $cmd = "SELECT reason_ids FROM season_reorder_change_qty_hist WHERE entry_id=? ORDER BY date_inputted DESC LIMIT 1";            
          $query = $this->db->query($cmd,array($entry_id));
          $row = $query->row_array();
          if(isset($row))
               $reasons = $row["reason_ids"];

          return $reasons;
     }

     function retrieveSeasonReorderChangeQtyHistoryById($entry_id){
          $cmd = "SELECT * FROM season_reorder_change_qty_hist WHERE entry_id=?";                
          $query = $this->db->query($cmd,array($entry_id));
          return $query->result_array();
     }

     function getSeasonReorderChangeQtyHistoryLatest($entry_id){
          $cmd = "SELECT * FROM season_reorder_change_qty_hist WHERE entry_id=? ORDER BY date_inputted DESC LIMIT 1";                
          $query = $this->db->query($cmd,array($entry_id));
          return $query->row_array();
     }

     function getSeasonReorderChangeQtyLast($entry_id){
          $qty = 0; 
          $cmd = "SELECT adj_qty,adj_qty_dr FROM season_reorder_change_qty_hist WHERE entry_id=? AND status=? ORDER BY date_inputted DESC LIMIT 1";                
          $query = $this->db->query($cmd,array($entry_id,"approved"));
          $row = $query->row_array();
          if(isset($row)){
               $qty = $row["adj_qty"]+$row["adj_qty_dr"];
          }

          return $qty;
     }

     function getSeasonReorderHistIdForApproval($batch_id){
          $cmd = "SELECT MAX(hist_id) as last_id FROM season_reorder_change_qty_hist a INNER JOIN season_reorder_item_entry b 
               ON a.entry_id=b.entry_id WHERE batch_id=? GROUP BY a.entry_id";

          $query = $this->db->query($cmd,array($batch_id));
          return $query->result_array();
     }

     function getSeasonReorderPendingQty($entry_id){
          $sum = 0;
          $cmd = "SELECT a.pending_qty, b.store_id, a.document_no FROM season_reorder_pending_qty a INNER JOIN 
               season_reorder_store_entry b ON a.store_entry_id=b.store_entry_id WHERE b.entry_id=?";           
          $query = $this->db->query($cmd,array($entry_id));
          $result = $query->result_array();
          foreach($result as $row){
               $count = $this->countCancelledPo($row["document_no"],$row["store_id"]);
               if($count<1)
                    $sum += $row["pending_qty"];
          }

          return $sum;
     }

     function countCancelledPo($doc_no,$store_id){
          $cmd = "SELECT COUNT(*) AS count_ FROM reorder_po WHERE document_number=? AND store_id=? AND status=?";           
          $query = $this->db->query($cmd,array($doc_no,$store_id,"Cancel"));
          return $query->row_array()["count_"];
          
     }

     function getSeasonReorderPendingQtyDetails($entry_id){
          $cmd = "SELECT a.pending_id, d.value_, a.document_no, a.po_date, c.uom, a.pending_qty, a.exp_del_date, b.store_id FROM 
               season_reorder_pending_qty a 
               INNER JOIN season_reorder_store_entry b ON a.store_entry_id=b.store_entry_id
               INNER JOIN season_reorder_item_entry c ON c.entry_id=b.entry_id 
               INNER JOIN reorder_store d ON d.store_id=b.store_id WHERE b.entry_id=?";                
          $query = $this->db->query($cmd,array($entry_id));
          return $query->result_array();
     }

     function countPoByEntryIdAndDocNo($store_entry_id,$doc_no){
          $count = 0;
          $cmd = "SELECT COUNT(*) AS count_ FROM season_reorder_pending_qty WHERE store_entry_id=? AND document_no=?";           
          $query = $this->db->query($cmd, array($store_entry_id,$doc_no));
          $row = $query->row_array();
          if(isset($row)){
               $count = $row["count_"];
          }

          return $count;
     }

     function getSeasonReorderTypeByEntryId($entry_id){ // Determine Whether Store or CDC
          $cmd = "SELECT d.store_id FROM season_reorder_item_entry a INNER JOIN season_reorder_batch d ON d.batch_id=a.batch_id 
          WHERE a.entry_id=? LIMIT 1";

          $query = $this->db->query($cmd,array($entry_id));
          return $query->row_array()["store_id"];
     }

     function retrieveJoinedForLines($batch_id,$si_dr){
          $adj_qty = ($si_dr=="SI") ? "e.adj_qty" : "e.adj_qty_dr";

          $cmd = "SELECT b.item_no, b.item_desc, b.uom, b.variant_code, 
               GROUP_CONCAT(DISTINCT c.store_entry_id,':',c.qty_onhand SEPARATOR '|') AS qty_onhand,
               b.unit_price, b.unit_price_vat, b.inventory_posting_grp, b.gen_prod, b.vat_prod, b.wht_prod, b.barcode,
               d.location_code, d.company_code, d.department_code, d.responsibility_center,
               $adj_qty AS adj_qty, (b.unit_price*$adj_qty) AS amt, (b.unit_price_vat*$adj_qty) AS amt_vat
               FROM season_reorder_batch a 
               INNER JOIN season_reorder_item_entry b ON b.batch_id=a.batch_id 
               INNER JOIN season_reorder_store_entry c ON c.entry_id=b.entry_id 
               INNER JOIN reorder_store d ON d.store_id=a.store_id
               INNER JOIN season_reorder_change_qty_hist e ON e.entry_id=b.entry_id
               WHERE b.batch_id=? AND $adj_qty!=0 AND e.is_reorder='yes'
               AND e.entry_id = (
                  SELECT entry_id
                  FROM season_reorder_change_qty_hist
                  WHERE entry_id = e.entry_id
                  ORDER BY date_inputted DESC
                  LIMIT 1
               )
               GROUP BY b.item_no, b.uom, b.variant_code ORDER BY b.entry_id";

          // base_qty = qty_per_uom*adj_qty(reorder)

          $query = $this->db->query($cmd,array($batch_id));
          $list = $query->result_array();
          
          foreach($list as &$entry){
               $entry["qty_per_unit_of_measure"] = round($this->getQtyUomFromNav($entry["item_no"],$entry["uom"]),4);
               $entry["base_qty"] = $entry["qty_per_unit_of_measure"]*$entry["adj_qty"];
          }

          return $list;
     }

     function getSignatureImage(){
          $cmd = "SELECT signature FROM reorder_users WHERE user_type=? AND user_id=?";
          $query = $this->db->query($cmd,array("corp-manager",16));
          return $query->row_array()["signature"]; 
     }

     function getBookingDbFromMW($db_id,$season,$years,$vendor_code){
          $store_id = $this->getStoreIDByDb($db_id);
          $season_ = $this->getSeasonTypesDirectById($season);

          $in_arr2 = '(';
          $where_dates = '(';
          
          for($c=0; $c<count($years); $c++){
               $in_arr2 .= "'".$years[$c]."'";

               $where_dates .= "(posting_date BETWEEN '".$years[$c]."-".$season_["period_start"]."' AND 
                              '".$years[$c]."-".$season_["period_end"]."')";

               if($c<count($years)-1){
                    $in_arr2 .= ',';
                    $where_dates .= ' OR ';
               }
          }
          $in_arr2 .= ')'; // ("2022","2019")
          $where_dates .= ')';

          if($season_["type_val"]=="Monthly"){
               $table_query = "SELECT no_ AS item_no, description AS item_desc, unit_of_measure, variant_code, SUM(quantity) AS sales,
                         YEAR(posting_date) AS year, MONTH(posting_date) AS month FROM mms_middleware 
                         WHERE vendor_no=? AND db_id=? AND YEAR(posting_date) IN ".$in_arr2." 
                         AND MONTH(posting_date) BETWEEN '".$season_["period_start"]."' AND '".$season_["period_end"]."' 
                         GROUP BY item_no,unit_of_measure,variant_code,year,month";
          
          }else{ // Daily
               $table_query = "SELECT no_ AS item_no, description AS item_desc, unit_of_measure, variant_code, SUM(quantity) AS sales,
                         YEAR(posting_date) AS year, MONTH(posting_date) AS month FROM mms_middleware 
                         WHERE vendor_no=? AND db_id=? AND ".$where_dates." 
                         GROUP BY item_no,unit_of_measure,variant_code,year,month";
          }
          
          $query = $this->mw->query($table_query,array($vendor_code,$db_id));
          $list = $query->result_array(); 

          $final_list = array(); // Get seasonal items and convert to purch uom
          foreach($list as $key_ => &$item){ 
               $purch_uom = $this->getSeasonalItemPurchUom($vendor_code,$item["item_no"],$season);
               $item["store_id"] = $store_id;

               if($purch_uom!=""){

                    $purch_qty_uom = $this->getQtyUomFromNav($item["item_no"],$purch_uom); 
                    $nav_qty_uom = $this->getQtyUomFromNav($item["item_no"],$item["unit_of_measure"]); 
                    
                    if($purch_qty_uom!=0){
                         $item["unit_of_measure"] = $purch_uom;
                         $item["total"] = round(($nav_qty_uom*$item["sales"])/$purch_qty_uom,0);
                    }else{
                         $top_uom = $this->getTopUomFromNav($item["item_no"]); // uom, qty_uom
                         $item["unit_of_measure"] = $top_uom["uom"];
                         $item["total"] = round(($nav_qty_uom*$item["sales"])/$top_uom["qty_uom"],0);
                    }

                    $found = $this->checkItemInArray($final_list,$item["item_no"],$item["variant_code"],$item["year"],$item["month"]);
                    if($found==-1) // If not exists
                         $final_list[] = $list[$key_];
                    else
                         $final_list[$found]["total"] += $item["total"];
               }
                    
          }

          return $final_list;
     }

     function getPendingPoFromMW($vendor_code,$item_code,$uom,$prev_months_3,$db_id){
          $table_query = "SELECT a.document_no, a.date_, b.pending_qty FROM mms_middleware_header a 
                    INNER JOIN mms_middleware_lines b ON a.hd_id=b.hd_id 
                    WHERE a.vendor=? AND b.item_code=? AND b.uom=? AND a.date_ BETWEEN ? AND ? AND a.db_id=?";
          $query = $this->mw->query($table_query,array($vendor_code,$item_code,$uom,$prev_months_3["start_date"],$prev_months_3["end_date"],$db_id));
          return $query->result_array(); 
     }

     private function setUpNavConnect($db_id){
          $cmd = "SELECT * FROM `database` WHERE db_id=?";
          $query = $this->db->query($cmd,array($db_id));
          return $query->row_array();
     }
     
     function getTopUomFromNav($item_code){ // Navision

          $nav_data = $this->setUpNavConnect(5);

          $table = '['.$nav_data['sub_db_name'].'$Item Unit of Measure]';
          $connect = odbc_connect($nav_data['db_name'], $nav_data['username'], $nav_data['password']);

          $table_query = "SELECT TOP 1 REPLACE([Code],'''','') AS uom, [Qty_ per Unit of Measure] AS qty_uom FROM ".$table." 
                         WHERE [Item No_]=? ORDER BY [Qty_ per Unit of Measure] DESC";
             
          $result = odbc_prepare($connect, $table_query);
          odbc_execute($result, array($item_code));
             
          $uom["uom"] = "";
          $uom["qty_uom"] = 0;

          if($row = odbc_fetch_array($result)) {
               $uom["uom"] = $row["uom"];
               $uom["qty_uom"] = $row["qty_uom"];
          }

          odbc_free_result($result);
          odbc_close($connect);
          return $uom;
     }

     function getQtyUomFromNav($item_code,$uom){ // Navision

          $nav_data = $this->setUpNavConnect(5);

          $table = '['.$nav_data['sub_db_name'].'$Item Unit of Measure]';
          $connect = odbc_connect($nav_data['db_name'], $nav_data['username'], $nav_data['password']);

          $table_query = "SELECT [Qty_ per Unit of Measure] AS qty_uom FROM ".$table." WHERE [Item No_]=? 
                         AND REPLACE([Code],'''','')=?";
             
          $result = odbc_prepare($connect, $table_query);
          odbc_execute($result, array($item_code,$uom));
             
          $qty_uom = 0;

          if($row = odbc_fetch_array($result)) {
               $qty_uom = $row["qty_uom"];
          }

          odbc_free_result($result);
          odbc_close($connect);
          return $qty_uom;
     }

     function getUomsFromNav($item_code){ // Navision

          $nav_data = $this->setUpNavConnect(5);

          $table = '['.$nav_data['sub_db_name'].'$Item Unit of Measure]';
          $connect = odbc_connect($nav_data['db_name'], $nav_data['username'], $nav_data['password']);

          $table_query = "SELECT REPLACE([Code],'''','') AS uom, [Qty_ per Unit of Measure] AS qty_uom 
                         FROM ".$table." WHERE [Item No_]=?";
             
          $result = odbc_prepare($connect, $table_query);
          odbc_execute($result, array($item_code));
             
          $uoms = array();

          while($row = odbc_fetch_array($result)) {
               $uom["uom"] = $row["uom"];
               $uom["qty_uom"] = round($row["qty_uom"],10);
               $uoms[] = $uom;
          }

          odbc_free_result($result);
          odbc_close($connect);
          return $uoms;
     }

     function getVariantsFromNav($item_code){ // Navision

          $nav_data = $this->setUpNavConnect(5);

          $table = '['.$nav_data['sub_db_name'].'$Item Variant]';
          $connect = odbc_connect($nav_data['db_name'], $nav_data['username'], $nav_data['password']);

          $table_query = "SELECT [Code] AS variant FROM ".$table." WHERE [Item No_]=?";
             
          $result = odbc_prepare($connect, $table_query);
          odbc_execute($result, array($item_code));
             
          $variants = array();

          while($row = odbc_fetch_array($result)) {
               $variants[] = $row["variant"];
          }

          odbc_free_result($result);
          odbc_close($connect);
          return $variants;
     }

     function getUnitPricesFromNav($item_code,$uom,$variant){ // Navision

          $nav_data = $this->setUpNavConnect(5);

          $table = '['.$nav_data['sub_db_name'].'$Sales Price]';
          $connect = odbc_connect($nav_data['db_name'], $nav_data['username'], $nav_data['password']);

          $table_query = "SELECT TOP 1 [Unit Price] AS unit_price, [Unit Price Including VAT] AS unit_price_vat FROM ".$table." 
                         WHERE [Item No_]=? AND REPLACE([Unit of Measure Code],'''','')=? AND [Variant Code]=? 
                         AND [Sales Code]='PRICE_D' ORDER BY DATEDIFF(day,[Starting Date], GETDATE())";
             
          $result = odbc_prepare($connect, $table_query);
          odbc_execute($result, array($item_code,$uom,$variant));
             
          $unit_prices["unit_price"] = 0;
          $unit_prices["unit_price_vat"] = 0;

          if($row = odbc_fetch_array($result)) {
               $unit_prices["unit_price"] = round($row["unit_price"],2);
               $unit_prices["unit_price_vat"] = round($row["unit_price_vat"],2);
          }

          odbc_free_result($result);
          odbc_close($connect);
          return $unit_prices;
    }

     function getItemProdFromNav($item_code){ // Navision

          $nav_data = $this->setUpNavConnect(5);

          $table = '['.$nav_data['sub_db_name'].'$Item]';
          $connect = odbc_connect($nav_data['db_name'], $nav_data['username'], $nav_data['password']);

          $table_query = "SELECT [Inventory Posting Group] AS inventory_posting_grp, [Gen_ Prod_ Posting Group] AS gen_prod,
                         [VAT Prod_ Posting Group] AS vat_prod, [WHT Prod_ Posting Group] AS wht_prod 
                         FROM ".$table." WHERE [No_]=?";
             
          $result = odbc_prepare($connect, $table_query);
          odbc_execute($result, array($item_code));
             
          $prod_arr["inventory_posting_grp"] = '';
          $prod_arr["gen_prod"] = '';
          $prod_arr["vat_prod"] = '';
          $prod_arr["wht_prod"] = '';
          
          if($row = odbc_fetch_array($result)) {
               $prod_arr["inventory_posting_grp"] = $row["inventory_posting_grp"];
               $prod_arr["gen_prod"] = $row["gen_prod"];
               $prod_arr["vat_prod"] = $row["vat_prod"];
               $prod_arr["wht_prod"] = $row["wht_prod"];
          }

          odbc_free_result($result);
          odbc_close($connect);
          return $prod_arr;
    }

     function getBarcodeFromNav($item_code,$uom,$variant){ // Navision

          $nav_data = $this->setUpNavConnect(5);

          $table = '['.$nav_data['sub_db_name'].'$Barcodes]';
          $connect = odbc_connect($nav_data['db_name'], $nav_data['username'], $nav_data['password']);

          $table_query = "SELECT TOP 1 [Barcode No_] AS barcode FROM ".$table." WHERE [Item No_]=? AND 
                         REPLACE([Unit of Measure Code],'''','')=? AND [Variant Code]=? 
                         ORDER BY DATEDIFF(day,[Last Date Modified], GETDATE())";
             
          $result = odbc_prepare($connect, $table_query);
          odbc_execute($result, array($item_code,$uom,$variant));
             
          $barcode = "";
          if($row = odbc_fetch_array($result)) {
               $barcode = $row["barcode"];
          }

          odbc_free_result($result);
          odbc_close($connect);
          return $barcode;
     }

     function getPendingQtyFromNav($vendor_code,$item_code,$uom,$prev_months_3,$db_id){

          $nav_data = $this->setUpNavConnect($db_id);

          $table = '['.$nav_data['sub_db_name'].'$Purchase Line]';
          $connect = odbc_connect($nav_data['db_name'], $nav_data['username'], $nav_data['password']);

          $table_query = "SELECT [Buy-from Vendor No_] AS vendor_code, [Document No_] AS document_no, [Order Date] AS po_date, 
                         [Outstanding Quantity] AS pending_qty, [No_] AS item_code, [Unit of Measure Code] AS uom 
                         FROM ".$table." WHERE [Buy-from Vendor No_]=? AND [No_]=? AND 
                         REPLACE([Unit of Measure Code],'''','')=? AND [Order Date] BETWEEN ? AND ?";
             
          $result = odbc_prepare($connect, $table_query);
          odbc_execute($result, array($vendor_code,$item_code,$uom,$prev_months_3["start_date"],$prev_months_3["end_date"]));
             
          $list = array();
          while($row = odbc_fetch_array($result)) {
               if(strpos($row["document_no"],"SMGM")!==false){
                    $info["vendor_code"] = $row["vendor_code"];
                    $info["document_no"] = $row["document_no"];
                    $info["po_date"] =  date('Y-m-d', strtotime($row["po_date"]));
                    $info["pending_qty"] = $row["pending_qty"];
                    $info["item_code"] = $row["item_code"];
                    $info["uom"] = $row["uom"];
                    $list[] = $info;
               }
               
          }

          odbc_free_result($result);
          odbc_close($connect);
          return $list;
     }
     
     function getSalesInvoice($db_id,$season,$years,$vendor_code){

          $store_id = $this->getStoreIDByDb($db_id);
          $season_ = $this->getSeasonTypesDirectById($season);

          $in_arr2 = '(';
          $where_dates = '(';
          
          for($c=0; $c<count($years); $c++){
               $in_arr2 .= "'".$years[$c]."'";

               $where_dates .= "(header.[Posting Date] BETWEEN '".$years[$c]."-".$season_["period_start"]."' AND 
                              '".$years[$c]."-".$season_["period_end"]."')";

               if($c<count($years)-1){
                    $in_arr2 .= ',';
                    $where_dates .= ' OR ';
               }
          }
          $in_arr2 .= ')'; // ("2022","2019")
          $where_dates .= ')';

          $nav_data = $this->setUpNavConnect($db_id);

          $table = '['.$nav_data['sub_db_name'].'$Sales Invoice Header]';
          $table_2 = '['.$nav_data['sub_db_name'].'$Sales Invoice Line]';

          $connect = odbc_connect($nav_data['db_name'], $nav_data['username'], $nav_data['password']);

          if($season_["type_val"]=="Monthly"){
               $table_query = "SELECT lines.[No_] AS item_no, lines.[Description] AS item_desc, 
                         lines.[Unit of Measure] AS uom, lines.[Variant Code] as variant_code, lines.[Quantity] AS qty, 
                         YEAR(header.[Posting Date]) AS year, MONTH(header.[Posting Date]) AS month 
                         FROM ".$table." AS header INNER JOIN ".$table_2." AS lines ON header.[No_]=lines.[Document No_] 
                         WHERE lines.[Vendor No_]=? AND YEAR(header.[Posting Date]) IN ".$in_arr2." 
                         AND MONTH(header.[Posting Date]) BETWEEN '".$season_["period_start"]."' AND '".$season_["period_end"]."'";
          }else{ // Daily
                $table_query = "SELECT lines.[No_] AS item_no, lines.[Description] AS item_desc, 
                         lines.[Unit of Measure] AS uom, lines.[Variant Code] as variant_code, lines.[Quantity] AS qty, 
                         YEAR(header.[Posting Date]) AS year, MONTH(header.[Posting Date]) AS month 
                         FROM ".$table." AS header INNER JOIN ".$table_2." AS lines ON header.[No_]=lines.[Document No_] 
                         WHERE lines.[Vendor No_]=? AND ".$where_dates;
          }
          
          // echo $table_query;   
          $result = odbc_prepare($connect, $table_query);
          odbc_execute($result, array($vendor_code));
             
          $list = array(); // Add distinct entries
          while($row = odbc_fetch_array($result)) { 
               // echo $row["uom"].'<br>'; // Test '½ GAL'
               $key = $row["item_no"].'-'.$row["uom"].'-'.$row["year"].'-'.$row["month"];
               if (!isset($list[$key])) {
               
                    $info["item_no"] = $row["item_no"];
                    $info["item_desc"] = $row["item_desc"];
                    $info["unit_of_measure"] = $row["uom"];
                    $info["variant_code"] = $row["variant_code"];
                    $info["sales"] = $row["qty"];
                    $info["year"] = $row["year"];
                    $info["month"] = $row["month"];
                    $info["store_id"] = $store_id;

                    $list[$key] = $info;
               
               }else{
                    $list[$key]["sales"] += $row["qty"];
               }
          }

          odbc_free_result($result);
          odbc_close($connect);

          $final_list = array(); // Get seasonal items and convert to purch uom
          foreach($list as $key_ => &$item){ 
               $purch_uom = $this->getSeasonalItemPurchUom($vendor_code,$item["item_no"],$season);
          
               if($purch_uom!=""){

                    $purch_qty_uom = $this->getQtyUomFromNav($item["item_no"],$purch_uom); 
                    $nav_qty_uom = $this->getQtyUomFromNav($item["item_no"],$item["unit_of_measure"]); 
                    
                    if($purch_qty_uom!=0){
                         $item["unit_of_measure"] = $purch_uom;
                         $item["total"] = round(($nav_qty_uom*$item["sales"])/$purch_qty_uom,0);
                    }else{
                         $top_uom = $this->getTopUomFromNav($item["item_no"]); // uom, qty_uom
                         $item["unit_of_measure"] = $top_uom["uom"];
                         $item["total"] = round(($nav_qty_uom*$item["sales"])/$top_uom["qty_uom"],0);
                    }

                    $found = $this->checkItemInArray($final_list,$item["item_no"],$item["variant_code"],$item["year"],$item["month"]);
                    if($found==-1) // If not exists
                         $final_list[] = $list[$key_];
                    else
                         $final_list[$found]["total"] += $item["total"];
               }
                    
          }

          return $final_list;

     }

     private function checkItemInArray($list,$item_no,$variant_code,$year,$month){
          $found = -1;
          for($c=0; $c<count($list); $c++){
               if($list[$c]["item_no"]==$item_no && $list[$c]["variant_code"]==$variant_code && $list[$c]["year"]==$year && $list[$c]["month"]==$month){
                    $found = $c;
                    break;
               }
          }

          return $found;
     }



}   
