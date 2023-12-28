<?php

class Acct_mod extends CI_Model{

     function __construct(){
        parent::__construct();
        $this->hr = $this->load->database('hr', TRUE);
        $this->nav = $this->load->database('navision', TRUE);
       
     }

     function retrieveEmployee($name){
          $cmd = 'select emp_id, name, position, business_unit, dept_name from pis.employee3 inner join pis.locate_business_unit on concat(employee3.company_code,employee3.bunit_code)=locate_business_unit.bcode left join pis.locate_department on concat(employee3.company_code,employee3.bunit_code,employee3.dept_code)=locate_department.dcode where employee3.current_status = "Active" and name like ? limit 5';
          $query = $this->hr->query($cmd, array("%".$name."%"));
          return $query->result_array();
     }

     function retrieveEmployeeName($emp_id){
          $cmd = 'select name from pis.employee3 where emp_id=?';
          $query = $this->hr->query($cmd, array($emp_id));
          return $query->row_array();
     }

     function getPhoto($emp_id){
          $cmd = 'select photo from pis.applicant where app_id=?';
          $query = $this->hr->query($cmd, array($emp_id));
          return $query->row_array()["photo"];
     }


     function get_all_users(){
          $query = $this->db->query("select * from reorder_users");
          return $query->result_array(); 
     }

     function getUsernameCount($user){
          $query = $this->db->query("select * from reorder_users where username=?;", array($user));
          return $query->num_rows(); 
     }

     function getUserCountById($id){
          $query = $this->db->query("select * from reorder_users where user_id=?;", array($id));
          return $query->num_rows(); 
     }

     function retrieveAccountID($user,$pass){
          $query = $this->db->query("select password from reorder_users where username=?;", array($user));
          $row = $query->row_array();
          if(isset($row)){
               if(password_verify($pass,$row["password"])){
                    $query = $this->db->query("select user_id,emp_id from reorder_users where username=?;", array($user));
                    $row = $query->row_array();
                        
                    if (isset($row)){
                         return $row["user_id"];
                    }

                         return 0;  
               }    
          }
        
          return 0;
     }

     function getUserType($id){
          $query = $this->db->query("select user_type from reorder_users where user_id=?;", array($id));
          $row = $query->row_array();
          if(isset($row)){
               return $row["user_type"];
          }

          return 0;
     }


     function retrieveUserDetails(){
          $query = $this->db->query("select * from reorder_users where user_id=?;", array($_SESSION['user_id']));
          $row = $query->row_array();
          return $row;
     }

     function getUserDetailsById($user_id){
          $query = $this->db->query("select * from reorder_users where user_id=?;", array($user_id));
          $row = $query->row_array();
          return $row;
     }

     function updateUser($userpass,$id){ //userpass is an array.
          $setVal = "";
          foreach($userpass as $key => $val) {
               if($setVal!="")
                    $setVal.= ", ";

               $setVal.= $key."='".$val."'";
          }
        
          $query = $this->db->query("update reorder_users set ".$setVal." where user_id=?;", array($id));
          return $query;
          //return $setVal;
     }

     function updateUser2($data,$user_id) {
               $this->db->set($data);
               $this->db->where("user_id", $user_id);
               $this->db->update("reorder_users", $data);
          }

     function addUser($data) {
          if ($this->db->insert("reorder_users", $data)) {
               return true;
          }
     }

     function addKey($data) {
          if ($this->db->insert("manager_key", $data)) {
               return true;
          }
     }

     function editKey($data,$user_id) {
               $this->db->set($data);
               $this->db->where("user_id", $user_id);
               $this->db->update("manager_key", $data);
          }

     function checkUserEmp_id($emp_id)
     {
          $query = $this->db->query('SELECT * FROM reorder_users WHERE emp_id = "'.$emp_id.'"');
            
          $result = $query->num_rows();
          if($result > 0)
          {
               return true;
          }else
          {
             return false;
          }
     }

     function checkUserName($username)
     {
          $query = $this->db->query('SELECT * FROM reorder_users WHERE username = "'.$username.'"');
            
          $result = $query->num_rows();
          if($result > 0)
          {
               return true;
          }else
          {
             return false;
          }
     }

     function checkKeyExist($user_id)
     {
          $query = $this->db->query('SELECT * FROM manager_key WHERE user_id = "'.$user_id.'"');
            
          $result = $query->num_rows();
          if($result > 0)
          {
               return true;
          }else
          {
             return false;
          }
     }

     function getUserData($user_id)
     {
          //$userid = $this->session->userdata['user_id'];
          $this->db->select('*');
          $this->db->from('reorder_users');
          $this->db->where('reorder_users.user_id', $user_id);
          $query = $this->db->get();
          return $query->row();
     }

     function getUserData2($user_id)
     {
          //$userid = $this->session->userdata['user_id'];
          $this->db->select('*');
          $this->db->from('reorder_users');
          $this->db->where('reorder_users.user_id', $user_id);
          $query = $this->db->get();
          return $query->result();
     }

     function getKeyData($user_id)
     {
          //$userid = $this->session->userdata['user_id'];
          $this->db->select('*');
          $this->db->from('manager_key');
          $this->db->where('manager_key.user_id', $user_id);
          $query = $this->db->get();
          return $query->row();
     }

     var $column_order = array('user_type', 'username');
     var $search_column = array('user_type', 'username'); //set column field database for datatable searchable 
     var $order = array('user_id' => 'desc'); // default order 

          private function make_query(){   

             //$this->db->from('blacklist'); 
               //->join('pis.locate_business_unit', 'locate_business_unit.bunit_code = pis.employee3.bunit_code')

             $this->db->select('*')
               ->from('reorder_users')
               ->order_by('user_id','asc');

             $i = 0;
             foreach ($this->search_column as $item) // loop column 
             {
                 if($_POST['search']['value']) // if datatable send POST for search
                 {
                      
                     if($i===0) // first loop
                     {
                         $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                         $this->db->like($item, $_POST['search']['value']);
                     }
                     else
                     {
                         $this->db->or_like($item, $_POST['search']['value']);
                     }
         
                     if(count($this->search_column) - 1 == $i) //last loop
                         $this->db->group_end(); //close bracket
                 }
                 $i++;
             } 

             if(isset($_POST['order'])) // here order processing
             {
                 $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
             } 
             else if(isset($this->order))
             {
                 $order = $this->order;
                 $this->db->order_by(key($order), $order[key($order)]);
             }  
         }  

     function get_users(){  
          $this->make_query();  
          if(@$_POST["length"] != -1)  
          {  
               $this->db->limit($_POST['length'], $_POST['start']);  
          }  
          $query = $this->db->get();  
          return $query->result();  
     } 

     function get_filtered_data(){  
          $this->make_query();  
          $query = $this->db->get();  
          return $query->num_rows();  
     }       
           
     function get_all_data(){  
         
          //$this->db->get('users2');  
          // return $this->db->count_all_results();  
          $query = $this->db->get('reorder_users');  
          return $query->num_rows();
     }

     public function find_an_employee($emp_id)
          {
          $query = $this->hr->from('pis.employee3')
                    
                    ->where('employee3.emp_id', $emp_id)
                    ->get();
          return $query->row();
          }

     function bu_name($bunit_code, $company_code)
     {
          $query = $this->hr->select('business_unit')
                    ->where('bunit_code', $bunit_code)
                    ->where('company_code', $company_code)
                    ->get('locate_business_unit');
          return $query->row();
     }

     function dept_name($bunit_code, $company_code, $dept_code)
     {
          $query = $this->hr->select('dept_name')
                    ->where('company_code', $company_code)
                    ->where('bunit_code', $bunit_code)
                    ->where('dept_code', $dept_code)
                    ->get('locate_department');
          return $query->row();
     }

    function select_all(){
        $this->db->select("*");
        $this->db->from("nav_cons_header");
        $this->db->limit(10);
        $query = $this->db->get();
        return $query->result_array();
    }

     function select_year()
    {
     $this->db->select("cons_date");
     $this->db->from("nav_cons_header");
     $this->db->group_by('cons_date');
     $query = $this->db->get();
       return $query->result_array();
    }

    function select_store()
    {
     $this->db->select("store_no");
     $this->db->from("nav_cons_header");
     $this->db->group_by('store');
     $query = $this->db->get();
     return $query->result_array();
    }

    function select_dept()
    {
     $this->db->select("*");
     $this->db->from("dept_tbl");
     //$this->db->group_by('dept_name');
     $query = $this->db->get();
     return $query->result_array();
    }

    function select_group()
    {
     $this->db->select("*");
     $this->db->from("group_tbl");
     //$this->db->group_by('dept_name');
     $query = $this->db->get();
     return $query->result_array();
    }

     function get_report_store($year,$pre_year)
     {


     $query = $this->db->query("
                               SELECT
                                     store,SUM(total_rounded_amt) AS total,
                                     YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) AS year,
                                     MONTH(STR_TO_DATE(cons_date, '%m-%d-%y')) AS month,
                                     DATE_FORMAT(STR_TO_DATE(cons_date, '%m-%d-%y'), '%M') AS month_name,
                                     SUM(quantity) AS total_quantity     
                                FROM
                                     nav_cons_header
                                WHERE
                                     YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) >= '$pre_year'
                                AND 
                                     YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) <= '$year'      
                            GROUP BY
                                     YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')),
                                     MONTH(STR_TO_DATE(cons_date, '%m-%d-%y')),
                                     store     
                            ORDER BY 
                                    store asc,
                                    MONTH(STR_TO_DATE(cons_date, '%m-%d-%y'))
                                      
                              ");
         return $query->result_array();
     }

     // function get monthly report per department
     function get_monthly_report_mod($store,$year,$pre_year)
     {
          if($store == 'Select_all_store')
          {
               $query = $this->db->query("
                                        SELECT
                                             store,item_department,SUM(total_rounded_amt) AS total,
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) AS year,
                                             MONTH(STR_TO_DATE(cons_date, '%m-%d-%y')) AS month,
                                             DATE_FORMAT(STR_TO_DATE(cons_date, '%m-%d-%y'), '%M') AS month_name,
                                             SUM(quantity) AS total_quantity

                                        FROM
                                             nav_cons_header
                                        WHERE
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) >= '$pre_year'
                                        AND 
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) <= '$year'
                                        AND store_no in ('ICM-S0001','ASC-S0001', 'PM-S0001','MAN-S0001', 'COL-SM', 'TAL-S0001')
                                      
                                        GROUP BY
                                             store,item_department,
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')),
                                             MONTH(STR_TO_DATE(cons_date, '%m-%d-%y'))
                                                
                                         
                                        ORDER BY 
                                             store asc, 
                                             item_department asc,
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')),
                                             MONTH(STR_TO_DATE(cons_date, '%m-%d-%y'));
                                                    
                                            ");
               return $query->result_array();
          }else{

               if($store == 'ASC'){
                    $condition = "store_no = 'ASC-S0001'";
               }else if($store == 'COL'){
                    $condition = "store_no = 'COL-SM'";
               }else if($store == 'ICM'){
                    $condition = "store_no = 'ICM-S0001'";
               }else if($store == 'PM'){
                    $condition = "store_no = 'PM-S0001'";
               }else if($store == 'MAN'){
                    $condition = "store_no = 'MAN-S0001'";
               }else if($store == 'TAL'){
                    $condition = "store_no = 'TAL-S0001'";
               }
               
               $query = $this->db->query("
                    SELECT
                           store,item_department,SUM(total_rounded_amt) AS total,
                           YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) AS year,
                           MONTH(STR_TO_DATE(cons_date, '%m-%d-%y')) AS month,
                           DATE_FORMAT(STR_TO_DATE(cons_date, '%m-%d-%y'), '%M') AS month_name,
                           SUM(quantity) AS total_quantity

                     FROM
                           nav_cons_header
                     WHERE
                           YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) >= '$pre_year'
                     AND 
                           YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) <= '$year'
                     AND   store = '$store' AND $condition

                  
                     GROUP BY
                            YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')),
                            MONTH(STR_TO_DATE(cons_date, '%m-%d-%y')),
                            item_department
                     
                     ORDER BY 
                           item_department asc,
                           MONTH(STR_TO_DATE(cons_date, '%m-%d-%y'));
                           
                   ");
               return $query->result_array(); 
          }
     }

     // function get yearly report per department
     function get_yearly_report_mod($pre_year,$year,$store)
     {    
          if($store == 'Select_all_store')
          {
               $query = $this->db->query("SELECT 
                                             store,item_department,SUM(total_rounded_amt) AS total,
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) AS year,
                                             SUM(quantity) as total_quantity_yearly

     
                                        FROM nav_cons_header 
                                        WHERE store_no in ('ICM-S0001','ASC-S0001', 'PM-S0001','MAN-S0001', 'COL-SM', 'TAL-S0001') AND 
                                        (
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) >= '$pre_year' AND 
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) <= '$year'
                                        )
                                 
                                        GROUP BY   YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')), store, item_department
                                                       
                                        ORDER BY 
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')),
                                             store asc, item_department asc
                                        
                                        ");
               return $query->result_array();
          }
          else{

               if($store == 'ASC'){
                    $condition = "store_no = 'ASC-S0001'";
               }else if($store == 'COL'){
                    $condition = "store_no = 'COL-SM'";
               }else if($store == 'ICM'){
                    $condition = "store_no = 'ICM-S0001'";
               }else if($store == 'PM'){
                    $condition = "store_no = 'PM-S0001'";
               }else if($store == 'MAN'){
                    $condition = "store_no = 'MAN-S0001'";
               }else if($store == 'TAL'){
                    $condition = "store_no = 'TAL-S0001'";
               }

               $query = $this->db->query("select 
                                        store,item_department,SUM(total_rounded_amt) AS total,
                                        YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) AS year,
                                        SUM(quantity) as total_quantity_yearly

     
                                   FROM nav_cons_header 
                                  where 
                                        store ='$store' AND $condition
                                    AND 
                                        (
                                        YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) >= '$pre_year'
                                    AND 
                                        YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) <= '$year')
                                 
                               GROUP BY YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')),
                                        item_department           
                               ORDER BY 
                                        
                                        YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')),
                                        item_department asc
                                        
                             ");
               return $query->result_array();
          }    
     }

     // function get monthly report per group
     function get_monthly_report_group_mod($store,$year,$pre_year)
     {
          if($store == 'Select_all_store')
          {
               $query = $this->db->query("
                                        SELECT
                                             store,item_group,SUM(total_rounded_amt) AS total,
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) AS year,
                                             MONTH(STR_TO_DATE(cons_date, '%m-%d-%y')) AS month,
                                             DATE_FORMAT(STR_TO_DATE(cons_date, '%m-%d-%y'), '%M') AS month_name,
                                             SUM(quantity) AS total_quantity

                                        FROM
                                             nav_cons_header
                                        WHERE
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) >= '$pre_year'
                                        AND 
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) <= '$year'
                                        AND  store_no in ('ICM-S0001','ASC-S0001', 'PM-S0001','MAN-S0001', 'COL-SM', 'TAL-S0001')
                                      
                                        GROUP BY
                                             store,item_group,
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')),
                                             MONTH(STR_TO_DATE(cons_date, '%m-%d-%y'))
                                             
                                         
                                         ORDER BY 
                                             store asc,item_group asc,
                                             MONTH(STR_TO_DATE(cons_date, '%m-%d-%y'))
                                               
                                       ");
               return $query->result_array();
          }// end for all stores
          else{
               if($store == 'ASC'){
                    $condition = "store_no = 'ASC-S0001'";
               }else if($store == 'COL'){
                    $condition = "store_no = 'COL-SM'";
               }else if($store == 'ICM'){
                    $condition = "store_no = 'ICM-S0001'";
               }else if($store == 'PM'){
                    $condition = "store_no = 'PM-S0001'";
               }else if($store == 'MAN'){
                    $condition = "store_no = 'MAN-S0001'";
               }else if($store == 'TAL'){
                    $condition = "store_no = 'TAL-S0001'";
               }
               $query = $this->db->query("
                                        select
                                               store,item_group,SUM(total_rounded_amt) AS total,
                                               YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) AS year,
                                               MONTH(STR_TO_DATE(cons_date, '%m-%d-%y')) AS month,
                                               DATE_FORMAT(STR_TO_DATE(cons_date, '%m-%d-%y'), '%M') AS month_name,
                                               SUM(quantity) AS total_quantity

                                         FROM
                                               nav_cons_header
                                         WHERE
                                               YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) >= '$pre_year'
                                         AND 
                                               YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) <= '$year'
                                         AND   store = '$store'  AND $condition
                                      
                                         GROUP BY
                                                YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')),
                                                MONTH(STR_TO_DATE(cons_date, '%m-%d-%y')),
                                                item_group
                                         
                                         ORDER BY 
                                               item_group asc,
                                               MONTH(STR_TO_DATE(cons_date, '%m-%d-%y'))
                                               
                                       ");
               return $query->result_array();
          }       
     }

     // function get yearly total sales  :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
     
     // function get yearly report per group
     function get_yearly_report_group_mod($pre_year,$year,$store)
     {    
          if($store == 'Select_all_store')
          {
               $query = $this->db->query("SELECT 
                                             store,item_group,SUM(total_rounded_amt) AS total,
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) AS year,
                                             SUM(quantity) as total_quantity_yearly

     
                                        FROM nav_cons_header 
                                        WHERE store_no in ('ICM-S0001','ASC-S0001', 'PM-S0001','MAN-S0001', 'COL-SM', 'TAL-S0001') AND 
                                        (
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) >= '$pre_year' AND 
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) <= '$year'
                                        )
                                 
                                        GROUP BY   YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')), store, item_group
                                                       
                                        ORDER BY 
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')),
                                             store asc, item_group asc
                                        
                                        ");
               return $query->result_array();
          }
          else{

               if($store == 'ASC'){
                    $condition = "store_no = 'ASC-S0001'";
               }else if($store == 'COL'){
                    $condition = "store_no = 'COL-SM'";
               }else if($store == 'ICM'){
                    $condition = "store_no = 'ICM-S0001'";
               }else if($store == 'PM'){
                    $condition = "store_no = 'PM-S0001'";
               }else if($store == 'MAN'){
                    $condition = "store_no = 'MAN-S0001'";
               }else if($store == 'TAL'){
                    $condition = "store_no = 'TAL-S0001'";
               }

               $query = $this->db->query("SELECT
                                        store,item_group,SUM(total_rounded_amt) AS total,
                                        YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) AS year,
                                        SUM(quantity) as total_quantity_yearly

     
                                   FROM nav_cons_header 
                                   WHERE 
                                        store ='$store' AND $condition
                                   AND 
                                        (
                                        YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) >= '$pre_year'
                                   AND 
                                        YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) <= '$year')
                                 
                                   GROUP BY YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')),
                                        item_group           
                                   ORDER BY 
                                        
                                        YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')),
                                        item_group asc
                                        
                             ");
               return $query->result_array();
          }    
     }

     var $column_order2 = array('store');
     var $search_column2 = array('store'); //set column field database for datatable searchable 
     var $order2 = array('store' => 'desc'); // default order 

     private function make_query2($month,$year){   

          // $this->db->select('header.store as store,vendor.No AS vendor_no,SUM(total_rounded_amt) AS total')
          //      ->from('nav_cons_header AS header')
          //      ->join('nav_item_masterfile AS item', 'item.item_no = header.item_no', 'inner')
          //      ->join('nav_vendors AS vendor', 'vendor.No = item.vendor_no', 'inner')
          //      ->where('MONTH(STR_TO_DATE(header.cons_date, \'%m-%d-%Y\')) =', $month)
          //      ->where('YEAR(STR_TO_DATE(header.cons_date, \'%m-%d-%Y\')) =', $year)
          //      ->where('item.store =', 'SM')
          //      ->where_in('header.store_no', array('ICM-S0001','ASC-S0001', 'PM-S0001','MAN-S0001', 'COL-SM', 'TAL-S0001'))
          //      ->group_by('YEAR(STR_TO_DATE(header.cons_date, \'%m-%d-%Y\')), MONTH(STR_TO_DATE(header.cons_date, \'%m-%d-%Y\')), header.store, vendor.No');

          $this->db->select('header.store as store,item_no,SUM(total_rounded_amt) AS total, vendor.No AS vendor_no')
               ->from('nav_cons_header AS header')
               ->join('nav_item_masterfile AS item', 'item.item_no = header.item_no', 'inner')
               ->join('nav_vendors AS vendor', 'vendor.No = item.vendor_no', 'inner')
               ->where('MONTH(STR_TO_DATE(header.cons_date, \'%m-%d-%Y\')) =', $month)
               ->where('YEAR(STR_TO_DATE(header.cons_date, \'%m-%d-%Y\')) =', $year)
              ->group_by('YEAR(STR_TO_DATE(header.cons_date, \'%m-%d-%Y\')), MONTH(STR_TO_DATE(header.cons_date, \'%m-%d-%Y\')), header.store, vendor.No');

          $i = 0;
          foreach ($this->search_column2 as $item) // loop column 
          {
               if($_POST['search']['value']) // if datatable send POST for search
               {
                 
                    if($i===0) // first loop
                    {
                         $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                         $this->db->like($item, $_POST['search']['value']);
                    }
                    else
                    {
                         $this->db->or_like($item, $_POST['search']['value']);
                    }
    
                    if(count($this->search_column2) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
               }
               $i++;
          } 

          if(isset($_POST['order'])) // here order processing
          {
               $this->db->order_by($this->column_order2[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
          } 
          else if(isset($this->order))
          {
               $order2 = $this->order;
               $this->db->order_by(key($order2), $order2[key($order2)]);
          }  
     }

      function get_vendors($data){  
        
          $month = $data['month'];
          $year = $data['year']; 
        
          $this->make_query2($month, $year);   
          if(@$_POST["length"] != -1)  
          {  
               $this->db->limit($_POST['length'], $_POST['start']);  
          }  
          $query = $this->db->get();
          //var_dump($query);  
          return $query->result();  
     }

      function get_filtered_data2($data){  
          $month = $data['month'];
          $year = $data['year'];
        
          $this->make_query2($month, $year);     
          $query = $this->db->get();  
          return $query->num_rows();  
     } 

      function get_all_data2(){  
         
        $query = $this->db->get('nav_cons_header');  
        return $query->num_rows();
    }

     // function get monthly report per vendor
     function get_monthly_report_vendor_mod($store,$year,$pre_year,$category,$code)
     {
          if($store == 'Select_all_store')
          {
               if($category == 'dept'){
                    if($code == 'NO_DEPT'){
                         
                         $condition = "header.item_department = '' ";
                    }else{
                         $condition = "header.item_department = '$code' ";    
                    }
                    
                    
               }else{
                    if($code == 'NO_GROUP'){
                         
                         $condition = "header.item_group = '' ";
                    }else{
                         $condition = "header.item_group = '$code' ";
                    }
               }
               $query = $this->db->query("
                                        SELECT
                                             header.store as store,vendor.No AS vendor_no,SUM(total_rounded_amt) AS total,
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) AS year,
                                             MONTH(STR_TO_DATE(cons_date, '%m-%d-%y')) AS month,
                                             DATE_FORMAT(STR_TO_DATE(cons_date, '%m-%d-%y'), '%M') AS month_name,
                                             SUM(quantity) AS total_quantity

                                        FROM
                                             nav_cons_header as header
                                             inner join nav_item_masterfile as item on item.item_no = header.item_no
                                             inner join nav_vendors as vendor on vendor.No = item.vendor_no
                                        WHERE
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) >= '$pre_year'
                                        AND 
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) <= '$year'
                                        AND $condition AND header.store_no in ('ICM-S0001','ASC-S0001', 'PM-S0001','MAN-S0001', 'COL-SM', 'TAL-S0001')
                                      
                                        GROUP BY
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')),
                                             MONTH(STR_TO_DATE(cons_date, '%m-%d-%y')),
                                             store, vendor.Name
                                         
                                        ORDER BY 
                                             store asc, vendor.Name asc,
                                             MONTH(STR_TO_DATE(cons_date, '%m-%d-%y'))
                                         
                                        
                             
                                       ");
               return $query->result_array();

          }else{

               if($category == 'dept'){
                    if($code == 'NO_DEPT'){
                         
                         $condition = "header.item_department = '' ";
                    }else{
                         $condition = "header.item_department = '$code' ";    
                    }
                    
                    
               }else{
                    if($code == 'NO_GROUP'){
                         
                         $condition = "header.item_group = '' ";
                    }else{
                         $condition = "header.item_group = '$code' ";
                    }
               }

               if($store == 'ASC'){
                    $condition2 = "header.store_no = 'ASC-S0001'";
               }else if($store == 'COL'){
                    $condition2 = "header.store_no = 'COL-SM'";
               }else if($store == 'ICM'){
                    $condition2 = "header.store_no = 'ICM-S0001'";
               }else if($store == 'PM'){
                    $condition2 = "header.store_no = 'PM-S0001'";
               }else if($store == 'MAN'){
                    $condition2 = "header.store_no = 'MAN-S0001'";
               }else if($store == 'TAL'){
                    $condition2 = "header.store_no = 'TAL-S0001'";
               }
        
               $query = $this->db->query("
                                        SELECT
                                             header.store as store,vendor.No AS vendor_no,SUM(total_rounded_amt) AS total,
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) AS year,
                                             MONTH(STR_TO_DATE(cons_date, '%m-%d-%y')) AS month,
                                             DATE_FORMAT(STR_TO_DATE(cons_date, '%m-%d-%y'), '%M') AS month_name,
                                             SUM(quantity) AS total_quantity

                                        FROM
                                             nav_cons_header as header
                                             inner join nav_item_masterfile as item on item.item_no = header.item_no
                                             inner join nav_vendors as vendor on vendor.No = item.vendor_no
                                        WHERE
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) >= '$pre_year'
                                        AND 
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) <= '$year'
                                        AND $condition AND header.store = '$store' AND $condition2 
                                      
                                        GROUP BY
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')),
                                             MONTH(STR_TO_DATE(cons_date, '%m-%d-%y')),
                                             vendor.Name
                                         
                                        ORDER BY 
                                             vendor.Name asc,
                                             MONTH(STR_TO_DATE(cons_date, '%m-%d-%y'))
                                               
                                       ");
               return $query->result_array();
          }
     }

     // function get yearly report per vendor
     function get_yearly_report_vendor_mod($store,$year,$pre_year,$category,$code)
     {

          if($store == 'Select_all_store')
          {
               if($category == 'dept'){
                    if($code == 'NO_DEPT'){
                         
                         $condition = "header.item_department = '' ";
                    }else{
                         $condition = "header.item_department = '$code' ";    
                    }
                    
                    
               }else{
                    if($code == 'NO_GROUP'){
                         
                         $condition = "header.item_group = '' ";
                    }else{
                         $condition = "header.item_group = '$code' ";
                    }
               }

               $query = $this->db->query("SELECT 
                                        header.store as store,vendor.No AS vendor_no,SUM(total_rounded_amt) AS total,
                                        YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) AS year,
                                        SUM(quantity) as total_quantity_yearly

                                        FROM nav_cons_header as header
                                             inner join nav_item_masterfile as item on item.item_no = header.item_no
                                             inner join nav_vendors as vendor on vendor.No = item.vendor_no
                                        WHERE 
                                             $condition AND header.store_no in ('ICM-S0001','ASC-S0001', 'PM-S0001','MAN-S0001', 'COL-SM', 'TAL-S0001') 
                                        AND 
                                        (
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) >= '$pre_year'
                                        AND 
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) <= '$year')
                                      
                                        GROUP BY YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')),
                                             store,vendor.Name           
                                        ORDER BY 
                                             
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')), 
                                             store asc,vendor.Name asc
                                             
                                        ");
               return $query->result_array();

          }
          else{

               if($category == 'dept'){
                    $condition = "header.item_department = '$code' ";
               }else{
                    $condition = "header.item_group = '$code' ";
               }

               if($store == 'ASC'){
                    $condition2 = "header.store_no = 'ASC-S0001'";
               }else if($store == 'COL'){
                    $condition2 = "header.store_no = 'COL-SM'";
               }else if($store == 'ICM'){
                    $condition2 = "header.store_no = 'ICM-S0001'";
               }else if($store == 'PM'){
                    $condition2 = "header.store_no = 'PM-S0001'";
               }else if($store == 'MAN'){
                    $condition2 = "header.store_no = 'MAN-S0001'";
               }else if($store == 'TAL'){
                    $condition2 = "header.store_no = 'TAL-S0001'";
               }
               $query = $this->db->query("SELECT 
                                        header.store as store,vendor.No AS vendor_no,SUM(total_rounded_amt) AS total,
                                        YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) AS year,
                                        SUM(quantity) as total_quantity_yearly

                                        FROM nav_cons_header as header
                                             inner join nav_item_masterfile as item on item.item_no = header.item_no
                                             inner join nav_vendors as vendor on vendor.No = item.vendor_no
                                        WHERE 
                                             $condition AND header.store ='$store' AND  $condition2
                                        AND 
                                        (
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) >= '$pre_year'
                                        AND 
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) <= '$year')
                                      
                                        GROUP BY YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')),
                                             vendor.Name           
                                        ORDER BY 
                                             
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')), vendor.Name asc
                                             
                                        ");
               return $query->result_array();
          }
          
     }

     // function get month name 
     function get_month_name($store_no,$month,$pre_month)
     {
          $query = $this->db->query("SELECT 
                                        DATE_FORMAT(STR_TO_DATE(cons_date, '%m-%d-%y'), '%M') AS month_name
                                    FROM
                                        nav_cons_header
                                    WHERE
                                        MONTH(STR_TO_DATE(cons_date, '%m-%d-%y')) BETWEEN '$pre_month' AND '$month'
                                        AND store_no = '$store_no'
                                    GROUP BY
                                        MONTH(STR_TO_DATE(cons_date, '%m-%d-%y'))
                                        ");
          return $query->result_array(); 
     }

     // function get select year range 
     function get_year_range($year)
     {
          $query = $this->db->query("select * 
                                   FROM mpdi.nav_cons_header 
                                  where YEAR(DATE_SUB(STR_TO_DATE(cons_date, '%m-%d-%y'), INTERVAL 2 YEAR)) = '$year' limit 1");
          return $query->result_array();
     }

     // function get select month range 
     function get_month_range($month)
     {
          $query = $this->db->query("Select *
                                  FROM mpdi.nav_cons_header
                                 where MONTH(DATE_SUB(STR_TO_DATE(cons_date, '%m-%d-%y'), INTERVAL 2 MONTH)) = '$month' limit 1");
          return $query->result_array();
     }

     // function get division name 
     function get_dept_name($department)
     {
          $this->db->select("dept_name");
          $this->db->from("dept_tbl");
          $this->db->where("dept_code", $department);
          $query = $this->db->get();
          return $query->result_array();
     }

     function get_group_name($group)
     {
          $this->db->select("group_name");
          $this->db->from("group_tbl");
          $this->db->where("group_code", $group);
          $query = $this->db->get();
          return $query->result_array();
     }

     function get_vendor_name($vendor)
     {
          $this->db->select("Name");
          $this->db->from("nav_vendors");
          $this->db->where("No", $vendor);
          $query = $this->db->get();
          return $query->result_array();
     }

     function get_store_name($store)
     {
          $this->db->select("nav_store_val");
          $this->db->from("nav_store_names");
          $this->db->where("nav_store", $store);
          $query = $this->db->get();
          return $query->result_array();
     }

     // herbert added code .................

       function get_user_info($id)
       {
          $this->db->select("*");
          $this->db->from("reorder_users");
          $this->db->where("user_id",$id);
          $query = $this->db->get();
          return $query->result_array();
       }
     // end of herbert code .................

}   
