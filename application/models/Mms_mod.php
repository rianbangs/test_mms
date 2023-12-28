<?php

class Mms_mod extends CI_Model
{
	function __construct()
    {
        parent::__construct();
        $this->nav = $this->load->database('navision', TRUE);
        $this->pis = $this->load->database('pis',TRUE);
        $this->mdlw = $this->load->database('middleware',TRUE);
       
    }








    function get_emp_details($emp_id)
    {
        $this->pis->select('*');
        $this->pis->from('pis.employee3');
        $this->pis->where('emp_id',$emp_id);
        $query = $this->pis->get();
        return $query->result_array();
    }

    function get_user_connection($user_id)
    {
         $this->db->select('*');
         $this->db->from('reorder_users as users');
         if($user_id != '')
         {
             $this->db->join('reorder_store as store','store.store_id = users.store_id','INNER');
             $this->db->where('users.user_id',$user_id);  
         }
         $query = $this->db->get();
         return $query->result_array();
    }


    function get_connection($db_id)
    {
         $this->db->select('*');
         $this->db->from('database as db');
         //$this->db->join('store_info as inf','inf.address_id = db.address_id','INNER');
         $this->db->where('db_id',$db_id);
         $query = $this->db->get();
         return $query->result_array();
    }


  /*  function check_reorder_report_data_table()
    {
         $this->db->select('*');
         $this->db->from('reorder_report_data');
         $this->db->where('user_id',$_SESSION['user_id']);
         $query = $this->db->get();
         return $query->result_array();
    }*/

/* Stephanie and Sir Gershom Code ---------------------------------------------------------------------------*/

    function login_entry($user_id)
    {
        $this->db->insert('reorder_report_data_header_final', array("user_id" => $user_id, "reorder_date" => date("Y-m-d")));
    }

/* End of the Code ------------------------------------------------------------------------------------------*/
     

    function select($select,$table,$where)
    {
        $this->db->select($select);
        
        if($table != '')
        {            
            $this->db->from($table);
        }

        if($where != '')
        {            
            $this->db->where($where);
        }
        
        $query = $this->db->get();
        return $query->result_array();
    }


        
    
    function get_a_store($store)
    {
         $this->db->select('*');          
         $this->db->from('reorder_store');
         if($store != '')
         {
             $this->db->where('value_',$store);
         }

         $query = $this->db->get();
         return $query->result_array();
    }



    function get_store_list($store_name)
    {
          
         $this->db->select('*');          
         if($store_name == '')   
         {
             $this->db->from('reorder_store as store');
             $this->db->join('database as  db','db.db_id = store.databse_id');
         }
         else           
         {
             $this->db->from('reorder_users as users');
             $this->db->join('reorder_store as store','store.store_id = users.store_id','INNER');
             $this->db->where('users.user_id',$_SESSION['user_id']);   
         }

         $query = $this->db->get();
         return $query->result_array();         
    }


    function check_reorder_report($where)
    {
         $this->db->select('*');
         $this->db->from('mpdi.reorder_report_data');
         if($where == '')
         {
             $this->db->where('user_id',$_SESSION['user_id']);
         }
         else 
         if($where == 'truncate')
         {
             $this->db->where('user_id !=',$_SESSION['user_id']);
         }

         $query = $this->db->get();
         return $query->result_array();
    }

    
    function delete_entry($table,$index,$primary_key)
    {
         $this->db->where_in($primary_key,$index);
         $this->db->delete($table); 
    }


    function truncate($table)
    {
        $this->db->query('truncate '.$table);
    }

    function check_item_vendor_sales_report_filter($where)
    {
         $this->db->select('*');
         $this->db->from('item_vendor_sales_report_filter'); 
         if($where == '')       
         {
             $this->db->where('user_id',$_SESSION['user_id']);
         }
         else 
         if($where == 'truncate')   
         {
             $this->db->where('user_id !=',$_SESSION['user_id']);            
         }
         $query = $this->db->get();
         return $query->result_array();   
    }

    function get_item_vendor_sales_report_entry()
    {
        $this->db->select('*');
        $this->db->from('item_vendor_sales_report_entry');
        $this->db->where('user_id',$_SESSION['user_id']);
        $query = $this->db->get();
        return $query->result_array();       
    }



    function insert_reorder_report_data($supplier_code,$value_name,$value_type,$index_number,$store)
    {
         $this->db->set('supplier_code',$supplier_code);
         $this->db->set('value_name',$value_name);
         $this->db->set('value_type',$value_type);
         $this->db->set('date_uploaded',date('Y-m-d'));
         $this->db->set('index_number',$index_number);
         $this->db->set('store',$store);
         $this->db->set('user_id',$_SESSION['user_id']);
         $this->db->insert('reorder_report_data');
    }

    function check_reorder_report_data($supplier_code,$value_name,$value_type,$index_number,$store)
    {
         $this->db->select('*');
         $this->db->from('reorder_report_data');
         $this->db->where('supplier_code',$supplier_code);
         //$this->db->where('value_name',$value_name);
         // $this->db->where('value_type',$value_type);
         $this->db->where('index_number',$index_number);
         $this->db->where('store',$store);
         $query = $this->db->get();

         return $query->result_array();
    }


    function get_reorder_report_data($supplier_code,$store)
    {
        $this->db->select('*');
        $this->db->from('reorder_report_data');  
        $this->db->where('supplier_code',$supplier_code);
        $this->db->where('store',$store);
        $query = $this->db->get();
        return $query->result_array();
    } 

    function get_supplier_code()
    {
        $this->db->select('*');
        $this->db->from('reorder_report_data');  
        $this->db->group_by('supplier_code','asc');
        $this->db->group_by('store','asc');  
        $query = $this->db->get();
        return $query->result_array();
    }

/* Stephanie and Sir Gershom Code ---------------------------------------------------------------------------*/

    function get_reorder_report_data_all()
    {
        $this->db->select('*');
        $this->db->from('reorder_report_data');
        $this->db->where('user_id',$_SESSION['user_id']);
        $this->db->where_in('value_type', array('supplier_code_number','supplier_name','lead_time_factor','item_code', 'Description', 'Pending-month', 'PREV_qty-1', 'PREV_qty-2', 'PREV_qty-3', 'ave_sales', 'max_level', 'qty_on_hand', 'Unit_of_measure'));
        $this->db->order_by('reorder_id', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function get_details_item_vendor_by_code($code)
    {
        $this->db->select('*');
        $this->db->from('item_vendor_sales_report_entry');  
        $this->db->where('item_no', $code);
        $query = $this->db->get();
        return $query->result_array();
    }

    function get_qty_item_vendor_by_code($code)
    {
        $qty = 0;
        $this->db->select('qty');
        $this->db->from('item_vendor_sales_report_entry');  
        $this->db->where('item_no', $code);
        $query = $this->db->get();
        $row = $query->row_array();
        if(isset($row))
            $qty = round($row['qty'],2);

        return $qty;
    }

    function get_reorder_no_by_login(){
        $this->db->select('reorder_number');
        $this->db->from('reorder_report_data_header_final');  
        $this->db->where('user_id', $_SESSION['user_id']);
        $this->db->order_by('reorder_number', 'DESC');
        $query = $this->db->get();
        $row = $query->row_array();
        return $row['reorder_number'];
    }

    function get_nav_uom_header_details($item_code,$uom)
    {
         $this->db->select('*');
         $this->db->from('nav_uom_header');
         $this->db->where('Item_No',$item_code);
         $this->db->where('code',$uom);
         $query = $this->db->get();
         return $query->result_array();
    }



    function convert_unit_of_measure($db_id,$item_no,$uom_from,$uom_to,$qty)
    {
         $database_details = $this->get_connection($db_id);
         
         if($database_details[0]['department'] == 'SM')
         {
             $get_connection = $this->get_connection(5);//ICM_SM_SERVER_POS_SQL  ang kuhaan sa setup sa price D ug barcode         
         }
         else 
         if($database_details[0]['department'] == 'MP')
         {
             $get_connection = $this->get_connection(23);//ATCTPHARMA_BE  ang kuhaan sa setup sa price D ug barcode            
         }   

         foreach($get_connection  as $con)
         {
                $username    = $con['username'];
                $password    = $con['password']; 
                $connection  = $con['db_name'];
                $sub_db_name = $con['sub_db_name'];
         }

         $connect      = odbc_connect($connection, $username, $password);
         $table        = '['.$sub_db_name.'$Item Unit of Measure]';        

// from UOM -------------------------------------------------------------
         $table_query  = "  
                            SELECT
                                    *
                            FROM 
                                   ".$table." 
                            WHERE 
                                   [Item No_] = '".$item_no."'
                             AND 
                                   [Code] = '".$uom_from."'      
                                   ";

         $table_row_from    = odbc_exec($connect, $table_query);

         if(odbc_num_rows($table_row_from) > 0)
         {             
             while ($row_from = odbc_fetch_array($table_row_from))
             {                 
                  $from_qty_uom   = $row_from['Qty_ per Unit of Measure'];                  
             }
         }
// to UOM------------------------------------------------------------------
        $table_query  = "  
                            SELECT
                                    *
                            FROM 
                                   ".$table." 
                            WHERE 
                                   [Item No_] = '".$item_no."'
                             AND 
                                   [Code] = '".$uom_to."'      
                                   ";

         $table_row_to    = odbc_exec($connect, $table_query);

         if(odbc_num_rows($table_row_to) > 0)
         {             
             while ($row_to = odbc_fetch_array($table_row_to))
             {                 
                  $to_qty_uom   = $row_to['Qty_ per Unit of Measure'];                  
             }
         }   



         if(isset($from_qty_uom) && isset($to_qty_uom))
         {
             $final_qty = (round($qty,2) * round($from_qty_uom,2) ) / round($to_qty_uom,2);
         }
         else 
         {
              $final_qty = 0.00;
             
         }


         // if($item_no == '137534')
         // {
         //    echo 'item code->'.$item_no.' ------'.$final_qty.'= ('.$qty.' * '.$from_qty_uom.') / '.$to_qty_uom."<br>product".$product.' ======='.odbc_num_rows($table_row_from).' > 0 && '.odbc_num_rows($table_row_to).' > 0<br>';
         // }

         return $final_qty;                         

    }



    function get_item_uom_details($department,$item_no,$uom)
    {
         if(in_array($department,array('SM','SOD')) )
         {
             $get_connection = $this->get_connection(5);//ICM_SM_SERVER_POS_SQL  ang kuhaan sa setup sa price D ug barcode         
         }
         else 
         if($department == 'MP')
         {
             $get_connection = $this->get_connection(23);//ATCTPHARMA_BE  ang kuhaan sa setup sa price D ug barcode            
         }   

         foreach($get_connection  as $con)
         {
                $username    = $con['username'];
                $password    = $con['password']; 
                $connection  = $con['db_name'];
                $sub_db_name = $con['sub_db_name'];
         }

         $connect      = odbc_connect($connection, $username, $password);
         $table        = '['.$sub_db_name.'$Item Unit of Measure]';         
         $table_query  = "  
                            SELECT
                                    *                                    
                            FROM 
                                   ".$table." 
                            WHERE 
                                   [Item No_] = '".$item_no."'
                             AND 
                                   [Code] = '".$uom."'      
                                   ";
         $table_row    = odbc_exec($connect, $table_query);                         
         $uom_details = array();                          
         if(odbc_num_rows($table_row) > 0)
         {
             
             while ($row = odbc_fetch_array($table_row))
             {
                  array_push($uom_details,array(                                                     
                                                      'qty_uom' => $row['Qty_ per Unit of Measure'],
                                                      'item_code' => $row['Item No_']                   
                                               ));                  
             }
         }     

         return $uom_details;                     

    }




    function get_nav_uom_header($db_id,$quantity_on_hand,$quantity,$item_no,$code)
    {       

         $database_details = $this->get_connection($db_id);
         
         if($database_details[0]['department'] == 'SM')
         {
             $get_connection = $this->get_connection(5);//ICM_SM_SERVER_POS_SQL  ang kuhaan sa setup sa price D ug barcode         
         }
         else 
         if($database_details[0]['department'] == 'MP')
         {
             $get_connection = $this->get_connection(23);//ATCTPHARMA_BE  ang kuhaan sa setup sa price D ug barcode            
         }   


         foreach($get_connection  as $con)
         {
                $username    = $con['username'];
                $password    = $con['password']; 
                $connection  = $con['db_name'];
                $sub_db_name = $con['sub_db_name'];
         }
         $connect      = odbc_connect($connection, $username, $password);
         $table        = '['.$sub_db_name.'$Item Unit of Measure]';         
         $table_query  = "  
                            SELECT
                                    ((".$quantity_on_hand.") - (".$quantity." / [Qty_ per Unit of Measure]) ) as store_qty,[Qty_ per Unit of Measure],[Item No_]
                            FROM 
                                   ".$table." 
                            WHERE 
                                   [Item No_] = '".$item_no."'
                             AND 
                                   [Code] = '".$code."'      
                                   ";

         $table_row    = odbc_exec($connect, $table_query);

         $item_details = array();

         if(odbc_num_rows($table_row) > 0)
         {
             //while(odbc_fetch_row($table_row))
             while ($row = odbc_fetch_array($table_row))
             {
                  $store_qty = $row['store_qty'];
                  $qty_uom   = $row['Qty_ per Unit of Measure'];
                  $item_code = $row['Item No_']; 
             }
         }

         //var_dump($store_qty,$qty_uom,$item_code);
         
         $datas = array(
                           array("store_qty"=>$store_qty)
                       );


         return $datas;


        // $this->nav->select('(('.$quantity_on_hand.') - ('.$quantity.' / qty_per_unit_of_measure) ) as store_qty');
        // $this->nav->select('qty_per_unit_of_measure');
        // $this->nav->from('nav_uom_header');
        // $this->nav->where('item_no',trim($item_no));
        // $this->nav->where('code',trim($code));
        // $query = $this->nav->get();
        // return $query->result_array();
    }







    function get_all_nav_uom_header()
    {
         $this->nav->select('*');
         $this->nav->from('nav_uom_header');         
         $query = $this->nav->get();

         return $query->result_array();
    }    


    function get_archive_table()
    {
        $this->db->select('*'); 
        $this->db->from('reorder_report_data_batch as batch');
        $this->db->join('reorder_report_data_header_final AS header','batch.reorder_batch = header.reorder_batch','INNER');
        //$this->db->join('reorder_report_data_lines_final as lines_','header.reorder_number = lines_.reorder_number','INNER');
        $this->db->where('batch.status','ARCHIVE');
        $this->db->group_by('year(reorder_date),month(reorder_date),supplier_code,db_id,');
        $query = $this->db->get();
        return $query->result_array();
    }


    function get_quantity_on_hand($reorder_batch,$item_code)
    {
        $this->db->select('*'); 
        $this->db->from('reorder_report_data_header_final AS header');
        $this->db->join('reorder_report_data_lines_final as lines_','header.reorder_number = lines_.reorder_number','INNER');
        $this->db->where('header.reorder_batch',$reorder_batch);
        $this->db->where('lines_.item_code',$item_code);
        $query = $this->db->get();
        return $query->result_array();
    }


    function get_reorder_report_data_item_vendor($reorder_number,$reorder_batch,$item_code)
    {        
        //echo $reorder_number.'-->'.$reorder_batch.'-->'.$item_code."<br>";
         // $this->db->select('*');
         // $this->db->from('reorder_report_data_item_vendor');
         // $this->db->where('reorder_number',$reorder_number);
         // $this->db->where('reorder_batch',$reorder_batch);
         // $this->db->where('item_code',$item_code);
         // $query = $this->db->get();

         $query = $this->db->query("select
                                    *

                                   from 
                                         reorder_report_data_item_vendor
                                   where
                                         reorder_number = '".$reorder_number."'
                                   and
                                         reorder_batch = '".$reorder_batch."'
                                   and
                                         item_code ='".$item_code."'");     
                                  

         return $query->result_array();
    }


    function generate_reorder_report_mod($reorder_batch,$store,$user_id) //report para ordering  
    {
        // $this->db->select('_lines.item_code, _lines.ave_sales');
        // $this->db->select('(SELECT SUM(ave_sales) 
        //                     FROM reorder_report_data_header_final AS fin
        //                     INNER JOIN reorder_report_data_batch AS bat ON bat.reorder_batch = fin.reorder_batch
        //                     INNER JOIN reorder_report_data_lines_final AS ln ON ln.reorder_number = fin.reorder_number
        //                     WHERE bat.reorder_batch = 1
        //                     AND ln.item_code  = _lines.item_code) as total');
        // $this->db->select('(SELECT SUM(ave_sales) 
        //                     FROM reorder_report_data_header_final AS fin
        //                     INNER JOIN reorder_report_data_batch AS bat ON bat.reorder_batch = fin.reorder_batch
        //                     INNER JOIN reorder_report_data_lines_final AS ln ON ln.reorder_number = fin.reorder_number
        //                     WHERE bat.reorder_batch = 1
        //                     AND ln.item_code  = _lines.item_code)
        //                     / 
        //                     (SELECT COUNT(reorder_number) FROM reorder_report_data_header_final WHERE reorder_batch = 1) 
        //                     AS all_ave_sales');
        // $this->db->from('reorder_report_data_batch AS batch');
        // $this->db->join('reorder_report_data_header_final AS header', 'header.reorder_batch = batch.reorder_batch');
        // $this->db->join('reorder_report_data_lines_final AS _lines', '_lines.reorder_number = header.reorder_number');
        // $this->db->join('reorder_users AS users', 'users.user_id = batch.user_id');
        // $this->db->join('reorder_store AS store', 'store.store_id = users.store_id');
        // $this->db->where('batch.reorder_batch', 1);
        // $this->db->where('users.user_id', 1);
        // $this->db->where('header.store', 'cdc');

        // $query = $this->db->get();


          $user_details = $this->get_user_connection($user_id);         


          if($user_details[0]['value_'] == 'cdc')
          {
             $where     = 'AND fin.store != "cdc"';
             $where_all = '';
          }
          else 
          {
             //$where = 'AND fin.store = "'.$user_details[0]['value_'].'"';
             $where     = 'AND fin.store = "cdc"';
             $where_all = '';
          }

          

          $query = $this->db->query('
                                              SELECT  
                                    
                                                      _lines.item_code,
                                                      _lines.Item_description,
                                                      _lines.uom,
                                                      _lines.month_sales_1,
                                                      _lines.month_sales_2,
                                                      _lines.month_sales_3,
                                                      _lines.ave_sales,
                                                      _lines.reorder_number, 
                                                      _lines.quantity_on_hand,
                                                      _lines.reoder_id,
                                                      _lines.barcode,
                                                       batch.status,
                                                       (
                                                         SELECT
                                                                 SUM(ave_sales) 
                                                         FROM reorder_report_data_header_final      AS fin
                                                         INNER JOIN reorder_report_data_batch       AS bat on bat.reorder_batch = fin.reorder_batch
                                                         INNER JOIN reorder_report_data_lines_final AS ln on ln.reorder_number = fin.reorder_number
                                                         WHERE
                                                         bat.reorder_batch = "'.$reorder_batch.'"
                                                         AND ln.item_code  = _lines.item_code
                                                       )as total,
                                                       (
                                                         SELECT
                                                                 SUM(ave_sales) 
                                                         FROM reorder_report_data_header_final      AS fin
                                                         INNER JOIN reorder_report_data_batch       AS bat on bat.reorder_batch = fin.reorder_batch
                                                         INNER JOIN reorder_report_data_lines_final AS ln on ln.reorder_number = fin.reorder_number
                                                         WHERE
                                                         bat.reorder_batch = "'.$reorder_batch.'"
                                                         AND ln.item_code  = _lines.item_code
                                                         AND fin.store = "cdc"  
                                                       )as total_cdc
                                                       ,
                                                       (
                                                         SELECT
                                                                 SUM(ave_sales) 
                                                         FROM reorder_report_data_header_final      AS fin
                                                         INNER JOIN reorder_report_data_batch       AS bat on bat.reorder_batch = fin.reorder_batch
                                                         INNER JOIN reorder_report_data_lines_final AS ln on ln.reorder_number = fin.reorder_number
                                                         WHERE
                                                         bat.reorder_batch = "'.$reorder_batch.'"
                                                         AND ln.item_code  = _lines.item_code
                                                         '.$where.'  
                                                       ) 
                                                       #/ 
                                                       #(        
                                                       #   SELECT COUNT(reorder_number) FROM reorder_report_data_header_final AS fin where fin.reorder_batch = "'.$reorder_batch.'"   '.$where.'                 
                                                       #)
                                                       AS all_ave_sales,


                                                       (
                                                         SELECT
                                                                 SUM(ave_sales) 
                                                         FROM reorder_report_data_header_final      AS fin
                                                         INNER JOIN reorder_report_data_batch       AS bat on bat.reorder_batch = fin.reorder_batch
                                                         INNER JOIN reorder_report_data_lines_final AS ln on ln.reorder_number = fin.reorder_number
                                                         WHERE
                                                         bat.reorder_batch = "'.$reorder_batch.'"
                                                         AND ln.item_code  = _lines.item_code
                                                         '.$where_all.'  
                                                       ) 
                                                       / 
                                                       (        
                                                          SELECT COUNT(reorder_number) FROM reorder_report_data_header_final AS fin where fin.reorder_batch = "'.$reorder_batch.'"   '.$where_all.'                 
                                                        )AS consolidated_ave_sales,


                                                        _lines.maximum_level,
                                                        _lines.quantity_on_hand,
                                                        (
                                                         SELECT
                                                                 SUM(quantity_on_hand) 
                                                         FROM reorder_report_data_header_final      AS fin
                                                         INNER JOIN reorder_report_data_batch       AS bat on bat.reorder_batch = fin.reorder_batch
                                                         INNER JOIN reorder_report_data_lines_final AS ln on ln.reorder_number = fin.reorder_number
                                                         WHERE
                                                         bat.reorder_batch = "'.$reorder_batch.'"
                                                         AND ln.item_code  = _lines.item_code
                                                         
                                                       )as total_qty,
                                                       _lines.last_direct_cost,
                                                       _lines.last_rcv_qty,
                                                       _lines.last_del_date,
                                                       _lines.suggested_reord_qty,
                                                       _lines.suggested_reord_qty_dr,
                                                       _lines.unit_price_incl_vat,
                                                       _lines.unit_price,
                                                       header.month_1,
                                                       header.month_2,
                                                       header.month_3, 
                                                       header.reorder_batch,
                                                       header.supplier_code,
                                                       store.location_code,
                                                       store.company_code,
                                                       store.department_code,
                                                       store.responsibility_center,
                                                       store.value_,
                                                       db.department,
                                                       db.db_id 
                                                FROM reorder_report_data_batch AS batch
                                                INNER JOIN reorder_report_data_header_final AS header ON header.reorder_batch = batch.reorder_batch
                                                INNER JOIN reorder_report_data_lines_final AS _lines ON _lines.reorder_number = header.reorder_number
                                                INNER JOIN reorder_users AS users ON users.user_id = batch.user_id
                                                INNER JOIN reorder_store AS store ON store.store_id = users.store_id
                                                INNER JOIN mpdi.database      AS db    ON db.db_id = store.databse_id      
                                                WHERE batch.reorder_batch = "'.$reorder_batch.'"
                                                #AND  users.user_id = '.$_SESSION['user_id'].'
                                                AND   users.user_id = '.$user_id.'
                                                AND   header.store = "'.$store.'";




                                    ');

            return $query->result_array();
    }



    function get_ave_sales_per_month($reorder_batch)
    {
         $this->db->select('*');
         $this->db->from('reorder_report_data_header_final as header');
         $this->db->join('reorder_report_data_lines_final  as line','line.reorder_number = header.reorder_number','INNER');
         $this->db->join('reorder_report_data_batch as batch','batch.reorder_batch = header.reorder_batch','INNER');
         $this->db->join('reorder_store as str','str.databse_id = header.db_id','INNER');
         $this->db->where('header.reorder_batch',$reorder_batch);
         $query = $this->db->get();
         return $query->result_array(); 
    }   



    
    function get_all_average_sales($reorder_batch)
    {
        $this->db->select('
                             lines_.item_code,
                             lines_.Item_description,
                             lines_.ave_sales,
                             lines_.uom,
                             lines_.quantity_on_hand,                             
                             header.reorder_number,
                             header.reorder_batch,
                             store.display_name,
                             store.bu_type,
                             customer_name,
                             header.month_1,
                             header.month_2,
                             header.month_3,
                             header.db_id,
                             lines_.month_sales_1,
                             lines_.month_sales_2,
                             lines_.month_sales_3,
                             lines_.reoder_id,
                             batch.user_id 
                         ');
        $this->db->from('reorder_report_data_lines_final AS lines_');
        $this->db->join('reorder_report_data_header_final  AS header','header.reorder_number = lines_.reorder_number','INNER');
        $this->db->join('reorder_report_data_batch as batch','batch.reorder_batch = header.reorder_batch','INNER');
        $this->db->join('reorder_store as store','store.value_ = header.store');
        $this->db->where('header.reorder_batch',$reorder_batch);
        $query = $this->db->get();
        return $query->result_array();

        // $query = $this->db->query("select 
        //                                            * 
        //                                     from 
        //                                         reorder_report_data_lines_final AS lines_
                                                      
        //                                     INNER JOIN reorder_report_data_header_final  AS header ON header.reorder_number = lines_.reorder_number      
        //                                     WHERE
        //                                          header.reorder_batch = '1'  
        //                           ");
    }


   function insert_table($table,$insert_data)
   {
         $this->db->insert($table, $insert_data);
         return $this->db->insert_id();
   }





    function insert_reorder_report_data_header_final($insert_data){
        $this->db->insert("reorder_report_data_header_final", $insert_data);
        return $this->db->insert_id();
    }

    function insert_reorder_report_data_lines_final($insert_batch_data){
        $this->db->insert_batch("reorder_report_data_lines_final", $insert_batch_data);
        return $this->db->affected_rows();
    }

    function get_entries_reorder_report_data_header_final($r_no){
        $this->db->select('*');
        $this->db->from('reorder_report_data_header_final as header');  
        $this->db->join('reorder_report_data_batch as batch','batch.reorder_batch = header.reorder_batch','inner');
        $this->db->join('reorder_users as user','user.user_id = batch.user_id','inner');
        $this->db->join('reorder_store as store','store.store_id = user.store_id','inner');
        $this->db->where('batch.reorder_batch', $r_no);
        $query = $this->db->get();
        $row = $query->row_array();
        return $row;
    }


    function get_user_details()
    {
         $this->db->select('*');
         $this->db->from('reorder_users as users');         
         $this->db->where('users.user_id',$_SESSION['user_id']);  
         $query = $this->db->get();
         return $query->result_array();
    }


    function get_reorder_report_data_header_final_details($reorder_batch)
    {
         $this->db->select('*');
         $this->db->from('reorder_report_data_header_final as head');
         $this->db->join('reorder_store as store','store.databse_id = head.db_id','INNER');
         $this->db->where('reorder_batch',$reorder_batch);
         $query = $this->db->get();
         return $query->result_array();
    }
        

    function get_entries_reorder_report_data_header_final_by_bu($reorder_batch)
    {
        $get_user_details = $this->get_user_details();
        $con_details = $this->get_user_connection($_SESSION['user_id']);
        
        $this->db->select('*');
        $this->db->from('reorder_report_data_batch as batch');
        $this->db->join('reorder_report_data_header_final as header','header.reorder_batch = batch.reorder_batch','inner');  
        $this->db->join('reorder_users as users','users.user_id = batch.user_id','inner');
        $this->db->join('reorder_store as store','store.store_id = users.store_id','inner');


         
        if($reorder_batch != '') 
        {
             $this->db->where('batch.reorder_batch',$reorder_batch);
        }
        else
        if($get_user_details[0]['user_type'] == 'buyer')
        {
             if($con_details[0]['value_'] != 'cdc')
             {
                 $this->db->where('users.user_id',$_SESSION['user_id']);
             }
             else 
             {
                $where = " (users.user_id = '".$_SESSION['user_id']."' OR store.value_ != 'cdc') "; 
                $this->db->where($where);                
             }

        }


        if($get_user_details[0]['user_type'] == 'category-head')
        {
            // $this->db->where('batch.store_id',$get_user_details[0]['store_id']); 
              $group_code = explode(",",$get_user_details[0]['group_code']);

              $this->db->where_in('batch.group_code_',$group_code);


              if($con_details[0]['value_'] == 'cdc')
              {
                  $this->db->where_in('batch.status',array('Approved by-buyer','Approved by-corp-manager','Approved by-category-head','Forward to-Incorporator','Approved by-incorporator'));  
              }
              else 
              {
                  $this->db->where_in('batch.status',array('Approved by-buyer','Approved by-category-head','Approved by-corp-buyer'));                  
              }
        }
        
        if($get_user_details[0]['user_type'] == 'corp-manager')
        {             
             $this->db->where_in('batch.status',array('Approved by-corp-manager','Approved by-category-head','Forward to-Incorporator','Approved by-incorporator'));     
        }    

        if($get_user_details[0]['user_type'] == 'incorporator')
        {             
             $this->db->where_in('batch.status',array('Forward to-Incorporator','Approved by-incorporator'));     
        }




        $this->db->where('header.store = store.value_');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }


    function get_entries_reorder_report_data_lines_final($r_no){
        $this->db->select('*');
        $this->db->from('reorder_report_data_lines_final');  
        $this->db->where('reorder_number', $r_no);
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }



    function get_po_directory($db_id)
    {
         $this->db->select('*');
         $this->db->from('po_directory');
         if($db_id != '')
         {
             $this->db->where('db_id',$db_id);
         }     
         $query =$this->db->get();
         return $query->result_array();
    }


    function get_all_po_directory_by_store()
    {
         $this->db->select("*");
         $this->db->from("reorder_store as str");
         $this->db->join("po_directory as po","po.db_id = str.po_db_id","INNER");
         $this->db->where("str.file_extension !=",'');
         // $this->db->limit(1);
         // $this->db->where("str.file_extension","CENT-DC");
         $query =  $this->db->get();
         return $query->result_array();
    }

    function get_all_mms_directory()
    {
         $this->db->select("*");
         $this->db->from("po_directory as str");
         $this->db->group_by('mms_directory'); 
         $query =  $this->db->get();
         return $query->result_array();
    }




    function get_pending_po($store_handled,$past_3_month_years,$item_code)
    {
          $store_details = $this->get_a_store($store_handled);
          $get_dir       = $this->get_po_directory($store_details[0]['po_db_id']);

         


          $memory_limit = ini_get('memory_limit');
          ini_set('memory_limit',-1);
          ini_set('max_execution_time', 0);

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
                $po_arr = array();
                while (($entry = readdir($handle)) !== false) 
                {
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
                }  

               
                return $po_arr;
                ini_set('memory_limit',$memory_limit );

          } 
          else
          {
              // handle the error
              echo "Failed to open directory: {$dir}\n";
          }


    }



    function get_item_details_sql($database_id,$item_no,$uom)
    {

         if(strstr($item_no,'-'))
         {
             $exp_item  = explode('-',$item_no);
             $item_code = $exp_item[0];
             $variant   = $exp_item[1]; 
         }
         else 
         {
             $item_code = $item_no;
             $variant   = '';   
         }

         $get_connection = $this->get_connection($database_id);
         foreach($get_connection  as $con)
         {
                $username    = $con['username'];
                $password    = $con['password']; 
                $connection  = $con['db_name'];
                $sub_db_name = $con['sub_db_name'];
         }
         $connect      = odbc_connect($connection, $username, $password);
         $table        = '['.$sub_db_name.'$Item]';
         $table_2      = '['.$sub_db_name.'$Sales Price]';
         $table_query  = "  
                            SELECT
                                    TOP 1 *
                            FROM 
                                   ".$table."  AS itm
                            INNER JOIN  ".$table_2."  AS sp ON sp.[Item No_] = itm.[No_]                                  
                            WHERE 
                                   itm.[No_] = '".$item_code."' 
                            AND 
                                   [Variant Code] = '".$variant."' 
                            AND 
                                   [Base Unit of Measure] = '".$uom."'    
                            AND 
                                   [Sales Code] = 'PRICE_D'
                            ORDER BY [Starting Date] DESC            
                                   ";
         $table_row_to    = odbc_exec($connect, $table_query);

         $item_details = array();       

          

         if(odbc_num_rows($table_row_to) > 0)
         {             
             while ($row_to = odbc_fetch_array($table_row_to))
             {
                  $inventory_posting_group = $row_to['Inventory Posting Group']; 
                  $description             = $row_to['Description']; 
                  $vat_prod_posting_grp    = $row_to['VAT Prod_ Posting Group']; 
                  $gen_prod_posting_grp    = $row_to['Gen_ Prod_ Posting Group']; 
                  $wht_prod_posting_grp    = $row_to['WHT Prod_ Posting Group'];  


                  array_push($item_details,array(
                                                  'inventory_posting_group'=>$inventory_posting_group,
                                                  'description'=>$description,
                                                  'vat_prod_posting_grp'=>$vat_prod_posting_grp,
                                                  'gen_prod_posting_grp'=>$gen_prod_posting_grp,
                                                  'wht_prod_posting_grp'=>$wht_prod_posting_grp
                                                ));                
             }
             // if(odbc_num_rows($table_row) > 0)
             // {
             //     while(odbc_fetch_row($table_row))
             //     {
             //          $inventory_posting_group = odbc_result($table_row, 10); 
             //          $description             = odbc_result($table_row, 4); 
             //          $vat_prod_posting_grp    = odbc_result($table_row, 56); 
             //          $gen_prod_posting_grp    = odbc_result($table_row, 50); 
             //          $wht_prod_posting_grp    = odbc_result($table_row, 100); 

             //          array_push($item_details,array(
             //                                          'inventory_posting_group'=>$inventory_posting_group,
             //                                          'description'=>$description,
             //                                          'vat_prod_posting_grp'=>$vat_prod_posting_grp,
             //                                          'gen_prod_posting_grp'=>$gen_prod_posting_grp,
             //                                          'wht_prod_posting_grp'=>$wht_prod_posting_grp
             //                                        ));
             //     }
             // }
         }   



         return $item_details;
    }



    function get_sql_po($supplier_code,$from,$to,$item_code,$database_id)
    {
        // $user_type      = $this->get_user_details();

         $user_data      = $this->get_user_connection($_SESSION['user_id']);
         

         //$get_connection = $this->get_connection($user_data[0]['databse_id']);

         $get_connection = $this->get_connection($database_id);
         $po_row         = array();

         


         foreach($get_connection  as $con)
         {
                $username    = $con['username'];
                $password    = $con['password']; 
                $connection  = $con['db_name'];
                $sub_db_name = $con['sub_db_name'];
         }
         $connect      = odbc_connect($connection, $username, $password);

         $exp_from = explode('-',$from);
         $exp_to   = explode('-',$to);
         //($supplier_code,$exp_from,$exp_to);
         $table        = '['.$sub_db_name.'$Purchase Line]';

         if($item_code != '')
         {
            $where = "AND [No_] ='".$item_code."'";
         }
         else 
         {
            $where = '';
         }   

         $table_query  = "SELECT  * FROM ".$table." WHERE [Buy-from Vendor No_] = '".$supplier_code."'   ".$where."   AND   (YEAR([Order Date])  >= '".$exp_from[0]."' AND MONTH([Order Date])>= '".$exp_from[1]."' ) AND (YEAR([Order Date])  <= '".$exp_to[0]."' AND MONTH([Order Date])<= '".$exp_to[1]."' ) ORDER BY  [Order Date]";
         $table_row    = odbc_exec($connect, $table_query);
         $po_row       = array();
         if(odbc_num_rows($table_row) > 0)
         {
             while(odbc_fetch_row($table_row))
             {
                 $vendor      = odbc_result($table_row, 5); 
                 $document_no = odbc_result($table_row, 3); 
                 $date        = odbc_result($table_row, 121); 
                 $pending_qty = odbc_result($table_row, 15);
                 $item_code   = odbc_result($table_row, 7);
                 $uom         = odbc_result($table_row, 84);

                 array_push($po_row,array("vendor"=>$vendor,'document_no'=>$document_no,'date'=>$date,'pending_qty'=>$pending_qty,'item_code'=>$item_code,'uom'=>$uom));  

             }
         }         
         return $po_row;
    }


    function check_smgm($document_no,$item_code,$uom,$databse_id)
    {
         //($document_no,$item_code,$uom,$databse_id);
         $user_data      = $this->get_user_connection($_SESSION['user_id']);
         $get_connection = $this->get_connection($databse_id);
         //$get_connection = $this->get_connection($user_data[0]['databse_id']);
         $po_row         = array();
         //($get_connection);
         foreach($get_connection  as $con)
         {
                $username    = $con['username'];
                $password    = $con['password']; 
                $connection  = $con['db_name'];
                $sub_db_name = $con['sub_db_name'];
         }


         $connect      = odbc_connect($connection, $username, $password);

         $table        = '['.$sub_db_name.'$Purchase Line]';
         $table_query  = "SELECT  * FROM ".$table." WHERE [Document No_] = '".$document_no."' AND  [No_] = '".$item_code."'   AND  [Unit of Measure] = '".$uom."'";
         $table_row    = odbc_exec($connect, $table_query);
         //$ord_num_arr  = array();
         if(odbc_num_rows($table_row) > 0)
         {
             while(odbc_fetch_row($table_row))
             {
                 $document_no = odbc_result($table_row, 3); 
                 $pending_qty = odbc_result($table_row, 15); 

                 array_push($po_row,array('document_no'=>$document_no,'item_code'=>$item_code,'uom'=>$uom,'pending_qty'=>$pending_qty) );
             }
         }

         return $po_row;

    }


    function get_reason($where,$data)
    {
          $this->db->select('*');  
          $this->db->from('reorder_reasons');
          if($where == 'reason details')
          {
              $this->db->where_in('reason_id',$data);
          }
          $query = $this->db->get();
          return $query->result_array();
    }



    function check_reorder_report_pending_qty($reorder_batch,$document_no,$item_code,$po_date,$pending_qty,$expected_delivery_date,$user_id)
    {
            if($po_date ==' ')
            {
                 $po_date = '0000-00-00';
            }
            
            if( $expected_delivery_date ==' ')
            {
                $expected_delivery_date = '0000-00-00';
            }
            
            

            
           // ($reorder_batch,$document_no,$item_code,$po_date,$pending_qty,$expected_delivery_date,$user_id);
            $this->db->select('*');
            $this->db->from('reorder_report_pending_qty');
            if($reorder_batch != '') 
            {
                 $this->db->where('reorder_batch',$reorder_batch);
            }
            $this->db->where('document_no',$document_no);
            $this->db->where('item_code',$item_code);
            if($po_date != '')
            {
                 $this->db->where('po_date',$po_date);
            }
            if($pending_qty != '')
            {
                 $this->db->where('pending_qty',$pending_qty);
            }
            if($expected_delivery_date != '')
            {
                 $this->db->where('expected_delivery_date',$expected_delivery_date);
            }

            $this->db->where('user_id',$user_id);
            $query = $this->db->get();
            return $query->result_array();
    }    



    function update_table($table,$column_data,$column_filter)
    {
        $this->db->set($column_data);
        $this->db->where($column_filter);
        $this->db->update($table);        
    }


    function update_reorder_report_pending_qty($reorder_batch,$document_no,$item_code,$po_date,$pending_qty,$expected_delivery_date,$user_id,$pending_id)
    {

         $this->db->set('reorder_batch',$reorder_batch);
         $this->db->set('document_no',$document_no);
         $this->db->set('item_code',$item_code);
         $this->db->set('po_date',$po_date);
         $this->db->set('pending_qty',$pending_qty);
         $this->db->set('expected_delivery_date',$expected_delivery_date);
         $this->db->set('user_id',$user_id);
         $this->db->where('pending_id',$pending_id);
         $this->db->update('reorder_report_pending_qty');             
    }

    function update_number_generated($number_generated,$reorder_batch)
    {
        $this->db->set('number_generated',$number_generated);
        $this->db->where('reorder_batch',$reorder_batch);
        $this->db->update('reorder_report_data_batch');        
    }



    function update_reorder_report_data_batch($status,$reorder_batch)
    {
         $this->db->set('status',$status);
         $this->db->where('reorder_batch',$reorder_batch);
         $this->db->update('reorder_report_data_batch');         
    }


 

    function get_reorder_report_data_lines_final_single_line($reorder_id)
    {
         $this->db->select('*');
         $this->db->from('reorder_report_data_lines_final as reord');
         $this->db->join('reorder_report_data_header_final as head','head.reorder_number = reord.reorder_number','inner');
         $this->db->where('reord.reoder_id',$reorder_id);
         $query = $this->db->get();
         return $query->result_array();
    }




    function get_reorder_report_change_quantity_history($reorder_id,$quantity_id,$user_type)
    {

         $this->db->select('*');
         $this->db->from('reorder_report_change_quantity_history as hist');

         if($user_type == 'category_head')
         {
              $this->db->join('reorder_users as users','users.user_id = hist.approved_by','inner');
         }
         else 
         {
              $this->db->join('reorder_users as users','users.user_id = hist.hist_user_id','inner');
         }

         $this->db->join('reorder_report_data_lines_final as lines','lines.reoder_id = hist.reorder_id','inner');


         if($quantity_id == '' )
         {
             $this->db->where('hist.reorder_id',$reorder_id);
         }
         else 
         {
             $this->db->where('hist.quantity_id',$quantity_id);            
         }


         if($user_type == 'limit')
         {
             $this->db->order_by('hist.quantity_id','desc');
             $this->db->limit(1);
         }
         else 
         {
             $this->db->order_by('hist.quantity_id','asc');

         }
         
         $query = $this->db->get();
         return $query->result_array();
    }


    function check_change_quantity_history($reorder_batch)
    {
         $this->db->select('*');
         $this->db->from('reorder_report_data_batch as batch');
         $this->db->join('reorder_report_data_header_final as header','header.reorder_batch = batch.reorder_batch','inner');
         $this->db->join('reorder_report_data_lines_final  as lines','lines.reorder_number = header.reorder_number','inner');
         $this->db->join('reorder_report_change_quantity_history as hist','hist.reorder_id = lines.reoder_id');
         $this->db->where('batch.reorder_batch',$reorder_batch);
         $this->db->where('hist.status','Pending');
         $query = $this->db->get();
         return $query->result_array();

    }



    function get_reorder_report_data_po($item_code,$reorder_batch,$where)
    {
        if($where == 'all')
        {
             $this->db->select('document_no,item_code,all_store_ave_sales,all_store_qty_on_hand,pending_qty,reorder_batch,po_date,uom,db_id');
        }
        else 
        {
             //$this->db->select('sum(pending_qty) as total_pend_qty,document_no,item_code,all_store_ave_sales,all_store_qty_on_hand,pending_qty,reorder_batch,po_date,uom,db_id');
             $this->db->select('pending_qty,document_no,item_code,all_store_ave_sales,all_store_qty_on_hand,pending_qty,reorder_batch,po_date,uom,db_id');
        }
        $this->db->from('reorder_report_data_po');
        $this->db->where('item_code',$item_code);
        $this->db->where('reorder_batch',$reorder_batch);
        $query = $this->db->get();
        return $query->result_array();
    }

    function update_batch($status,$reorder_batch)
    {
         $this->db->set('status',$status);
         $this->db->set('approved_by',$_SESSION['user_id']);
         $this->db->where('reorder_batch',$reorder_batch);
         $this->db->update('reorder_report_data_batch');
    }

    function update_reorder_report_change_quantity_history($quantity_id,$status)
    {

         if($status == 'clear pending')   
         {
            $final_status = 'Disapproved';
         }
         else 
         {
            $final_status = $status;            
         }

         $this->db->set('status',$final_status);
         $this->db->set('approved_by',$_SESSION['user_id']);

         if($status == 'clear pending')   
         {
             $this->db->where('reorder_id',$quantity_id); 
             $this->db->where('status','Pending');
         }
         else 
         {
             $this->db->where('quantity_id',$quantity_id);            
         }
         
         $this->db->update('reorder_report_change_quantity_history');
    }



     public function extract_vendor($RESS2,$store,$reorder_number,$reorder_batch)
     {

          $store_name = $this->get_a_store($store);  

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

                      $this->insert_table($table,$insert_batch);  


                  }

               }
               //($exp_header_column);              
          }
           
           
     }




     public function get_previous_3_months($db_id,$month_name,$month_val,$month_sale_name,$year,$item_code)
     {
         $this->db->select($month_sale_name); 
         $this->db->from('reorder_report_data_lines_final as line');
         $this->db->join('reorder_report_data_header_final as header','header.reorder_number = line.reorder_number','INNER');
         $this->db->where('header.db_id',$db_id);
         $this->db->where($month_name,$month_val);
         $where = $month_sale_name."!= '0.00'";

         $this->db->where($where);
         $this->db->where('year(header.reorder_date)',$year);
         $this->db->where('item_code',$item_code);
         $query = $this->db->get();

         // Echo the SQL query statement
         //echo $this->db->last_query();

         return $query->result_array();
     } 



     public function extract_reorderV3($RESS2,$reorder_batch,$store,$databse_id,$database_details)
     {          



           $new_line = PHP_EOL;  //pang new line ni


           $exp_html = explode($new_line, $RESS2);
           // Remove elements with empty values
           $exp_html = array_filter($exp_html);

           // Reindex the array
           $exp_html = array_values($exp_html);

           // $index = array_search('PREVIOUS 3 MOS. (QTY) ', $exp_html);
           $current_line = ''; 
           $items        = array();
           $line         = array();
           
           for($a=0;$a<count($exp_html);$a++)
           {
               $value = $exp_html[$a];

               if(strstr($value,'REORDER REPORT - ACTUAL QTY'))
               {
                     $clean_date   = str_replace('&nbsp','',$exp_html[$a + 1]);
                     $reorder_date = date('Y-m-d', strtotime($clean_date));
               }
               else 
               if(strstr($value,'LEAD TIME FACTOR:'))
               {
                     $exp_lead= explode(':',$value); 
                     $lead_time_factor  = trim(str_replace('&nbsp','',$exp_lead[1])); 
               }
               else 
               if(strstr($value,'SUPPLIER CODE:'))
               {
                     $insert_data['supplier_code']    = trim(str_replace('&nbsp','',$exp_html[$a+1]));  
                     $insert_data['supplier_name']    = trim(str_replace('&nbsp','',$exp_html[$a+2]));  
               }    
               else  
               if(strstr($value,'PREVIOUS 3 MOS. (QTY)'))
               {
                    if($exp_html[$a+1] == 'AVE. SALES ')
                    {
                         $m_1 =  trim($exp_html[$a + 12]); 
                         $m_2 =  trim($exp_html[$a + 13]); 
                         $m_3 =  trim($exp_html[$a + 14]); 

                    }
                    else 
                    {
                         $m_1 =  trim($exp_html[$a + 1]); 
                         $m_2 =  trim($exp_html[$a + 2]); 
                         $m_3 =  trim($exp_html[$a + 3]); 
                    }

                    $insert_data['month_1'] = trim($m_1);
                    $insert_data['month_2'] = trim($m_2);
                    $insert_data['month_3'] = trim($m_3);
               }              
               else 
               if(strstr($value,'PAGE NO:')) 
               {
                     $current_line = 'end of page';
               }
               else 
               if(strstr($value,'SubTotal')) 
               {
                     $current_line = 'subtotal';
               }



                //  ^ and $ in the regular expression represent the start and end of the string, respectively, ensuring that the pattern matches the entire string.
                // 'V\d{4}' is the regular expression pattern:
                // 'V' matches the character 'V' literally.
                // '\d' matches any digit (0-9).
                // '{4}' specifies that there must be exactly four digits.

               if( (is_numeric($value) && strlen((string)$value) === 6 && ctype_digit((string)$value))  || preg_match('/^V\d{4}$/', $value) ) //check if item code na ni dapita or variant ba siya
               {  
                     array_push($items,$line); 
                     $line         = array();
                     $current_line = 'start item';
               }

               if($current_line == 'start item')
               {  
                    array_push($line,$value);
               }

           }  

           array_push($items,$line);  //ang last entry nga item  sa html kuhaon  
           $insert_data['reorder_batch']    = trim($reorder_batch);
           $insert_data['lead_time_factor'] = trim($lead_time_factor); 
           $insert_data['store']            = trim($store);   
           $insert_data['db_id']            = trim($databse_id);  
           $insert_data['reorder_date']     = trim($reorder_date); 
           $table                           = 'reorder_report_data_header_final';
           $reorder_number                  = $this->insert_table($table,$insert_data);

           $extract_year_month = date('Y-m',strtotime($reorder_date));

           $item_code = '';
           $uom       = '';
           foreach($items as $itm)
           {                 
                if(!empty($itm))
                {                     
                     if(strstr($itm[0],'V'))
                     {
                         $final_item = $item_code.'-'.$itm[0];
                         $proceed    = true;
                     }
                     else 
                     if(count($itm) == 14)
                     {
                        $item_code  = '';                        
                        $final_item = $itm[0];
                        $proceed    = true;
                        $uom        = $itm[2];      
                     }
                     else 
                     {
                        $item_code  = $itm[0];
                        $final_item = $itm[0];
                        $uom        = $itm[2];      
                        $proceed    = true;
                     }
                     



                     if($proceed == true && count($itm) > 12)
                     {
                          // echo $final_item.' Uom:'.$uom."<br>";         
                          // var_dump($itm);
                          if(count($itm) == 13) //if variant ni siya
                          { 
                              $m_1 = $itm[2]; 
                              $m_2 = $itm[3]; 
                              $m_3 = $itm[4]; 
                              $quantity_on_hand = $itm[7];    
                              $last_direct_cost = $itm[8]; 
                              $last_rcv_qty     = $itm[9]; 
                              $last_deliv_date  = $itm[11];
                          }    
                          else 
                          {
                              $m_1 = $itm[3]; 
                              $m_2 = $itm[4]; 
                              $m_3 = $itm[5]; 
                              $quantity_on_hand = $itm[8];    
                              $last_direct_cost = $itm[9]; 
                              $last_rcv_qty     = $itm[10]; 
                              $last_deliv_date  = $itm[12];
                          }        


                          $insert_data_lines['reorder_number']     = trim($reorder_number);                           
                          $insert_data_lines['item_code']          = $final_item;
                          $insert_data_lines['Item_description']   = trim($itm[1]);
                          $insert_data_lines['uom']                = trim($uom); 
                          
                          $month_sales_1                           = str_replace(',','',trim($m_1));
                          $month_sales_2                           = str_replace(',','',trim($m_2));
                          $month_sales_3                           = str_replace(',','',trim($m_3)); 


                          // get the past 3 month years from the current month
                          $past_3_month = array();
                          $past_3_year  = array(); 
                          for($i = 1; $i <= 3; $i++) 
                          {
                             $past_month_year  = date('Y-m-01', strtotime("-{$i} month", strtotime($extract_year_month.'-01'))); //if year month kuhaon                
                             $past_3_month[]   = strtoupper($past_month_year);
                             $past_3_year[]    = date('Y',strtotime($past_month_year));
                          }         

                          // Use asort() to sort the $past_3_month array in ascending order while maintaining key-value association
                          sort($past_3_month);

                          for($j=0;$j<count($past_3_month);$j++)
                          {
                             $past_3_month[$j] = strtoupper(date('M', strtotime($past_3_month[$j])));  //change from Y-m to M (himun siyag month name)
                          }

                          $month_name       = array('month_1','month_2','month_3');  
                          $month_sale_name  = array('month_sales_1','month_sales_2','month_sales_3');  
                          $orig_sale_value  = array($month_sales_1,$month_sales_2,$month_sales_3);  
                          $reord_year       = date('Y',strtotime($reorder_date));
                          $total_ave_sales  = 0;  


                          for($b=0;$b<count($month_name);$b++)
                          { 
                             if($orig_sale_value[$b] == 0.00)   
                             {
                                 $sale_qty = $this->get_previous_3_months($databse_id,$month_name[$b],$past_3_month[$b],$month_sale_name[$b],$past_3_year[$b],$final_item);                    
                                 if(!empty($sale_qty))
                                 {
                                     $insert_data_lines[ $month_sale_name[$b] ] = $sale_qty[0][ $month_sale_name[$b] ];  
                                     $total_ave_sales += $sale_qty[0][ $month_sale_name[$b] ];  
                                 }
                                 else 
                                 {
                                     $insert_data_lines[ $month_sale_name[$b] ] = $orig_sale_value[$b];
                                     $total_ave_sales += $orig_sale_value[$b];   
                                 }
                             }
                             else 
                             {
                                 $insert_data_lines[ $month_sale_name[$b] ] =   $orig_sale_value[$b]; 
                                 $total_ave_sales += $orig_sale_value[$b]; 
                             }
                          }    



                          $total_ave_sales = $total_ave_sales/90;
                          $min_level       = $total_ave_sales * $lead_time_factor; 
 
                         
                          $insert_data_lines['ave_sales']          = str_replace(',','',$total_ave_sales);                         
                          $insert_data_lines['maximum_level']      = str_replace(',','',$min_level);
                          $insert_data_lines['quantity_on_hand']   = str_replace(',','',trim($quantity_on_hand));
                          $insert_data_lines['last_direct_cost']   = str_replace(',','',trim($last_direct_cost));
                          $insert_data_lines['last_rcv_qty']       = str_replace(',','',trim($last_rcv_qty)); 
 
                          if(in_array($database_details[0]['department'],array('SM','SOD')))
                          {
                             $get_connection                = $this->get_connection(5);//ICM_SM_SERVER_POS_SQL  ang kuhaan sa setup sa price D ug barcode
                             $COLUMN_unit_price_incl_vat    = 18;
                             $COLUMN_unit_price             = 10;
                             $sales_code                    = 'PRICE_D';
                             $COLUMN_vat_prod_posting_group = 56;
                          }
                          else 
                          if($database_details[0]['department'] == 'MP')
                          {
                             $get_connection                = $this->get_connection(23);//ATCTPHARMA_BE  ang kuhaan sa setup sa price D ug barcode
                             $COLUMN_unit_price_incl_vat    = 19;
                             $COLUMN_unit_price             = 10;
                             $sales_code                    = 'ALL';
                             $COLUMN_vat_prod_posting_group = 57;
                          }     

                          foreach($get_connection  as $con)
                          {
                             $username    = $con['username'];
                             $password    = $con['password']; 
                             $connection  = $con['db_name'];
                             $sub_db_name = $con['sub_db_name'];
                          }

                          $connect = odbc_connect($connection, $username, $password);   

                          // Find the position of the hyphen
                          // $position = strpos($final_item, '-');

                          // if ($position !== false) 
                          // {
                          //     // If a hyphen is found, extract the part of the string before it
                          //     $final_item = substr($final_item, 0, $position);
                          // }    

                          if(strstr($final_item,'-'))
                          {
                              $item_exp   = explode("-",$final_item);
                              $final_item = $item_exp[0];
                              $variant    = $item_exp[1];
                          }
                          else 
                          {
                              $variant    = ''; 
                          }

                          
                          // -------------------------------------------------------------------pag kuha sa unit price ug price including vat nga price------------
                          $table              = '['.$sub_db_name.'$Sales Price]';
                          $sales_price_query  = "SELECT
                                                            TOP 1  *
                                                 FROM 
                                                            ".$table." 
                                                 WHERE
                                                           [Item No_] = '".$final_item."' 
                                                 AND 
                                                           [Variant Code] = '".$variant."'          
                                                 AND
                                                           [Unit of Measure Code] = '".$uom."'
                                                 AND 
                                                           [Sales Code] = '".$sales_code."'
                                                     
                                                           ORDER BY ABS(DATEDIFF(day, [Starting Date], '".date('Y-m-d')."'))       
    
                                                       ";

                          $table_SP_row       = odbc_exec($connect, $sales_price_query);                                   
                          if(odbc_num_rows($table_SP_row) > 0)
                          {
                              while(odbc_fetch_row($table_SP_row))
                              {
                                  $insert_data_lines['unit_price_incl_vat'] = odbc_result($table_SP_row, $COLUMN_unit_price_incl_vat);
                                  $insert_data_lines['unit_price']          = odbc_result($table_SP_row, $COLUMN_unit_price);                                                  
                              }
                          }     
                          //---------------------------------------------------------------------------------------------------------------------------------------   


                          // ***************************************************pagkuha sa vat posting group If VAT 12 (with vat) ba siya or NO VAT siya **********
                          $table          = '['.$sub_db_name.'$Item]'; 
                          $item_query     = "SELECT * FROM ".$table." WHERE [No_] = '".$final_item."' /*AND [Base Unit of Measure] = '".$uom."'*/";
                          $table_item_row = odbc_exec($connect, $item_query); 
                          if(odbc_num_rows($table_item_row) > 0)
                          {
                              while(odbc_fetch_row($table_item_row))
                              {
                                  $insert_data_lines['vat_prod_posting_group'] = odbc_result($table_item_row, $COLUMN_vat_prod_posting_group);                                             
                              }
                          } 
                          // ****************************************************************************************************************************************

                          $table              = '['.$sub_db_name.'$Barcodes]';
                          $barcodes_query     = "SELECT * FROM ".$table." WHERE [Item No_] = '".$final_item."' AND [Variant Code] = '".$variant."' AND [Unit of Measure Code] = '".$uom."'";
                          $table_barcodes_row =  odbc_exec($connect, $barcodes_query); 
                          if(odbc_num_rows($table_barcodes_row) > 0)
                          {
                               while(odbc_fetch_row($table_barcodes_row))
                               {
                                   $insert_data_lines['barcode'] = odbc_result($table_barcodes_row, 2);                                             
                               }
                          } 



                          //  In this code:
                          //  /\/|gwapo/ is the regular expression pattern that searches for either the / character or the string '&nbsp'.
                          //  preg_match() is used to perform the search.
                          //  If either the / character or 'gwapo' is found in $last_deliv_date, the code inside the first block will execute. Otherwise, the code inside the second block will execute. 

                          //if(strstr($last_deliv_date,'/') && )
                          if(preg_match('/\/|&nbsp/', $last_deliv_date))
                          {
                             $last_deliv_date = str_replace('&nbsp','',$last_deliv_date);
                             $dateString      = $last_deliv_date;
                             $dateTimestamp   = strtotime($dateString);
                             $last_del_date   = date('Y-m-d', $dateTimestamp);
                          }
                          else 
                          {
                             $last_del_date = $last_deliv_date;
                          }

                          $insert_data_lines['last_del_date'] = $last_del_date;

                          $table = 'reorder_report_data_lines_final';
                          if($insert_data_lines['item_code']  != '')
                          {
                              $this->insert_table($table,$insert_data_lines);
                          }   

                     }
                     //echo $itm[0]."<br>";
                }
           }    

           return $reorder_number;

           // var_dump($exp_html);
     }









     public function extract_reorderV2($RESS2,$reorder_batch)
     {

         $store_list = $this->get_a_store('');
         foreach($store_list as $str)
         {       
              if($str['reorder_report_header'] != '')
              {                
                  $keywords = explode('^',$str['reorder_report_header']);
                  $exists   = false; 
                  foreach ($keywords as $keyword) 
                  {
                        if($str['bu_type'] != 'NON STORE')
                        {             
                          if (strpos($RESS2, $keyword) !== false)
                          {
                             $store      = $str['value_']; 
                             $databse_id = $str['databse_id']; 

                          }                                
                        }
                  }           
              }
         }



                 $database_details  = $this->get_connection($databse_id);
                  
         // if(in_array($database_details[0]['department'],array('SOD') ))
         // {
              return  $this->extract_reorderV3($RESS2,$reorder_batch,$store,$databse_id,$database_details);
         // }
         // else 
         // {



         //         $html_exp          = explode("_________________________",$RESS2);
         //         $exp_header_column = explode("\n", $html_exp[0]);
         //         $header_arr        = array_splice($exp_header_column,0, -18); // Remove the last 18 indexes  
                 

         //         // Step 1: Remove &nbsp; from the string
         //         $clean_date   = str_replace('&nbsp', '', $header_arr[29]);
         //         // Step 2: Convert the remaining string into a date format
         //         $timestamp    = strtotime($clean_date);
         //         $reorder_date = date('Y-m-d', $timestamp);
         //         $exp_lead          = explode(':',$header_arr[44]);
                 
         //         $lead_time_factor  = trim($exp_lead[1]);

         //         $insert_data['supplier_code']    = trim($header_arr[37]); 
         //         $insert_data['supplier_name']    = trim($header_arr[42]); 
         //         $insert_data['lead_time_factor'] = trim($lead_time_factor); 
         //         $insert_data['month_1']          = trim($header_arr[99]);
         //         $insert_data['month_2']          = trim($header_arr[101]);
         //         $insert_data['month_3']          = trim($header_arr[103]);
         //         $insert_data['reorder_batch']    = trim($reorder_batch);
         //         $insert_data['store']            = trim($store);   
         //         $insert_data['db_id']            = trim($databse_id);  
         //         $insert_data['reorder_date']    = trim($reorder_date); 
         //         $table                           = 'reorder_report_data_header_final';
         //         $reorder_number                  = $this->insert_table($table,$insert_data);


         //         $extract_year_month = date('Y-m',strtotime($reorder_date));

         //         for($a=0;$a<count($html_exp);$a++)
         //         {
         //             $exp_per_column = explode("\n", $html_exp[$a]);      
         //             $clean_row      = array_slice($exp_per_column, -18);     
                     
         //             $insert_data_lines['reorder_number']     = trim($reorder_number);
         //             $item_code                               = trim($clean_row[0]);
         //             $insert_data_lines['item_code']          = $item_code;
         //             $insert_data_lines['Item_description']   = trim($clean_row[1]);
         //             $insert_data_lines['uom']                = trim($clean_row[2]);

                     
         //             $month_sales_1                           = str_replace(',','',trim($clean_row[3]));
         //             $month_sales_2                           = str_replace(',','',trim($clean_row[4]));
         //             $month_sales_3                           = str_replace(',','',trim($clean_row[5])); 

         //             if($month_sales_1 == 0.00 )
         //             {

         //             }

         //             //  // get the past 3 month years from the current month
         //             $past_3_month = array();
         //             $past_3_year  = array(); 
         //             for($i = 1; $i <= 3; $i++) 
         //             {
         //                 $past_month_year  = date('Y-m-01', strtotime("-{$i} month", strtotime($extract_year_month.'-01'))); //if year month kuhaon                
         //                 $past_3_month[]   = strtoupper($past_month_year);
         //                 $past_3_year[]    = date('Y',strtotime($past_month_year));
         //             }         

         //             // Use asort() to sort the $past_3_month array in ascending order while maintaining key-value association
         //             sort($past_3_month);

         //             for($j=0;$j<count($past_3_month);$j++)
         //             {
         //                $past_3_month[$j] = strtoupper(date('M', strtotime($past_3_month[$j])));  //change from Y-m to M (himun siyag month name)
         //             }

                     

         //             $month_name       = array('month_1','month_2','month_3');  
         //             $month_sale_name  = array('month_sales_1','month_sales_2','month_sales_3');  
         //             $orig_sale_value  = array($month_sales_1,$month_sales_2,$month_sales_3);  
         //             $reord_year       = date('Y',strtotime($reorder_date));
         //             $total_ave_sales  = 0;     

         //             for($b=0;$b<count($month_name);$b++)
         //             { 

         //                 if($orig_sale_value[$b] == 0.00)   
         //                 {
                              

         //                      $sale_qty = $this->get_previous_3_months($databse_id,$month_name[$b],$past_3_month[$b],$month_sale_name[$b],$past_3_year[$b],$item_code);                    
         //                      if(!empty($sale_qty))
         //                      {
         //                          $insert_data_lines[ $month_sale_name[$b] ] = $sale_qty[0][ $month_sale_name[$b] ];  
         //                          $total_ave_sales += $sale_qty[0][ $month_sale_name[$b] ];  
         //                      }
         //                      else 
         //                      {
         //                          $insert_data_lines[ $month_sale_name[$b] ] = $orig_sale_value[$b];
         //                          $total_ave_sales += $orig_sale_value[$b];   
         //                      }
         //                 }
         //                 else 
         //                 {
         //                      $insert_data_lines[ $month_sale_name[$b] ] =   $orig_sale_value[$b]; 
         //                      $total_ave_sales += $orig_sale_value[$b]; 
         //                 }

                     

         //             }           

         //             $total_ave_sales = $total_ave_sales/90;
         //             $min_level       = $total_ave_sales * $lead_time_factor; 

         //             //$insert_data_lines['ave_sales']          = str_replace(',','',trim($clean_row[6]));
         //             $insert_data_lines['ave_sales']          = str_replace(',','',$total_ave_sales);
         //             //$insert_data_lines['maximum_level']      = str_replace(',','',trim($clean_row[7]));
         //             $insert_data_lines['maximum_level']      = str_replace(',','',$min_level);
         //             $insert_data_lines['quantity_on_hand']   = str_replace(',','',trim($clean_row[8]));
         //             $insert_data_lines['last_direct_cost']   = str_replace(',','',trim($clean_row[9]));
         //             $insert_data_lines['last_rcv_qty']       = str_replace(',','',trim($clean_row[11])); 

         //             $item_code = trim($clean_row[0]); 
         //             $uom       = trim($clean_row[2]);
                      
         //             if($database_details[0]['department'] == 'SM')
         //             {
         //                 $get_connection                = $this->get_connection(5);//ICM_SM_SERVER_POS_SQL  ang kuhaan sa setup sa price D ug barcode
         //                 $COLUMN_unit_price_incl_vat    = 18;
         //                 $COLUMN_unit_price             = 10;
         //                 $sales_code                    = 'PRICE_D';
         //                 $COLUMN_vat_prod_posting_group = 56;
         //             }
         //             else 
         //             if($database_details[0]['department'] == 'MP')
         //             {
         //                 $get_connection                = $this->get_connection(23);//ATCTPHARMA_BE  ang kuhaan sa setup sa price D ug barcode
         //                 $COLUMN_unit_price_incl_vat    = 19;
         //                 $COLUMN_unit_price             = 10;
         //                 $sales_code                    = 'ALL';
         //                 $COLUMN_vat_prod_posting_group = 57;
         //             }     


         //             foreach($get_connection  as $con)
         //             {
         //                    $username    = $con['username'];
         //                    $password    = $con['password']; 
         //                    $connection  = $con['db_name'];
         //                    $sub_db_name = $con['sub_db_name'];
         //             }
         //             $connect      = odbc_connect($connection, $username, $password);       

         //             // -------------------------------------------------------------------pag kuha sa unit price ug price including vat nga price------------
         //             $table              = '['.$sub_db_name.'$Sales Price]';
         //             $sales_price_query  = "SELECT
         //                                            TOP 1  *
         //                                    FROM 
         //                                            ".$table." 
         //                                    WHERE
         //                                           [Item No_] = '".$item_code."' 
         //                                    AND
         //                                           [Unit of Measure Code] = '".$uom."'
         //                                    AND 
         //                                           [Sales Code] = '".$sales_code."'
                                             
         //                                           ORDER BY ABS(DATEDIFF(day, [Starting Date], '".date('Y-m-d')."'))       

         //                                           ";
         //             $table_SP_row       = odbc_exec($connect, $sales_price_query);                                   
         //             if(odbc_num_rows($table_SP_row) > 0)
         //             {
         //                 while(odbc_fetch_row($table_SP_row))
         //                 {
         //                     $insert_data_lines['unit_price_incl_vat'] = odbc_result($table_SP_row, $COLUMN_unit_price_incl_vat);
         //                     $insert_data_lines['unit_price']          = odbc_result($table_SP_row, $COLUMN_unit_price);                                                  
         //                 }
         //             }     
         //             //---------------------------------------------------------------------------------------------------------------------------------------

         //             // ***************************************************pagkuha sa vat posting group If VAT 12 (with vat) ba siya or NO VAT siya **********
         //             $table      = '['.$sub_db_name.'$Item]'; 
         //             $item_query = "SELECT * FROM ".$table." WHERE [No_] = '".$item_code."' /*AND [Base Unit of Measure] = '".$uom."'*/";
         //             $table_item_row = odbc_exec($connect, $item_query); 
         //             if(odbc_num_rows($table_item_row) > 0)
         //             {
         //                 while(odbc_fetch_row($table_item_row))
         //                 {
         //                     $insert_data_lines['vat_prod_posting_group'] = odbc_result($table_item_row, $COLUMN_vat_prod_posting_group);                                             
         //                 }
         //             } 
         //             // ****************************************************************************************************************************************

         //             $table              = '['.$sub_db_name.'$Barcodes]';
         //             $barcodes_query     = "SELECT * FROM ".$table." WHERE [Item No_] = '".$item_code."' AND [Unit of Measure Code] = '".$uom."'";
         //             $table_barcodes_row =  odbc_exec($connect, $barcodes_query); 
         //             if(odbc_num_rows($table_barcodes_row) > 0)
         //             {
         //                  while(odbc_fetch_row($table_barcodes_row))
         //                  {
         //                      $insert_data_lines['barcode'] = odbc_result($table_barcodes_row, 2);                                             
         //                  }
         //             } 


         //             if(strstr($clean_row[14],'/'))
         //             {
         //                 $dateString    = $clean_row[14];
         //                 $dateTimestamp = strtotime($dateString);
         //                 $last_del_date = date('Y-m-d', $dateTimestamp);
         //             }
         //             else 
         //             {
         //                 $last_del_date                       = $clean_row[14];
         //             }

         //             $insert_data_lines['last_del_date']      = $last_del_date;

         //             $table = 'reorder_report_data_lines_final';
         //             if($insert_data_lines['item_code']  != '')
         //             {
         //                 $this->insert_table($table,$insert_data_lines);
         //             }   
         //         }

         //         return $reorder_number;
         // }


     }





     public function extract_reorder($RESS2,$store,$reorder_batch)
     { 
         
         $store_list = $this->get_a_store('');
         foreach($store_list as $str)
         {  
             if (strpos($RESS2, $str['name']) !== false) 
             {
                $store = $str['value_'];         
             }   
         }

         

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
         $reorder_number = $this->insert_table($table,$insert_data);

         


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

             $item_code                               = trim($clean_row[0]); 
             $uom                                     = trim($clean_row[2]);

             $user_details = $this->get_user_connection($_SESSION['user_id']);
             $db_details   = $this->get_connection($user_details[0]['databse_id']);
            

             if($db_details[0]['department'] == 'SM')
             {
                 $get_connection = $this->get_connection(5);//ICM_SM_SERVER_POS_SQL  ang kuhaan sa setup sa price D ug barcode

                 foreach($get_connection  as $con)
                 {
                        $username    = $con['username'];
                        $password    = $con['password']; 
                        $connection  = $con['db_name'];
                        $sub_db_name = $con['sub_db_name'];
                 }
                 $connect      = odbc_connect($connection, $username, $password);       

                 // -------------------------------------------------------------------pag kuha sa unit price ug price including vat nga price------------
                 $table              = '['.$sub_db_name.'$Sales Price]';
                 $sales_price_query  = "SELECT
                                                TOP 1  *
                                        FROM 
                                                ".$table." 
                                        WHERE
                                               [Item No_] = '".$item_code."' 
                                        AND
                                               [Unit of Measure Code] = '".$uom."'
                                        AND 
                                               [Sales Code] = 'PRICE_D'
                                         
                                               ORDER BY ABS(DATEDIFF(day, [Starting Date], '".date('Y-m-d')."'))       

                                               ";
                 $table_SP_row       = odbc_exec($connect, $sales_price_query);                                   
                 if(odbc_num_rows($table_SP_row) > 0)
                 {
                     while(odbc_fetch_row($table_SP_row))
                     {
                         $insert_data_lines['unit_price_incl_vat'] = odbc_result($table_SP_row, 18);
                         $insert_data_lines['unit_price']          = odbc_result($table_SP_row, 10);                                                  
                     }
                 }     
                 //---------------------------------------------------------------------------------------------------------------------------------------

                 // ***************************************************pagkuha sa vat posting group If VAT 12 (with vat) ba siya or NO VAT siya **********
                 $table      = '['.$sub_db_name.'$Item]'; 
                 $item_query = "SELECT * FROM ".$table." WHERE [No_] = '".$item_code."' /*AND [Base Unit of Measure] = '".$uom."'*/";
                 $table_item_row = odbc_exec($connect, $item_query); 
                 if(odbc_num_rows($table_item_row) > 0)
                 {
                     while(odbc_fetch_row($table_item_row))
                     {
                         $insert_data_lines['vat_prod_posting_group'] = odbc_result($table_item_row, 56);                                             
                     }
                 } 
                 // ****************************************************************************************************************************************

                  $table              = '['.$sub_db_name.'$Barcodes]';
                  $barcodes_query     = "SELECT * FROM ".$table." WHERE [Item No_] = '".$item_code."' AND [Unit of Measure Code] = '".$uom."'";
                  $table_barcodes_row =  odbc_exec($connect, $barcodes_query); 
                  if(odbc_num_rows($table_barcodes_row) > 0)
                  {
                         while(odbc_fetch_row($table_barcodes_row))
                         {
                             $insert_data_lines['barcode'] = odbc_result($table_barcodes_row, 2);                                             
                         }
                  } 

             }

             

             if(strstr($clean_row[14],'/'))
             {
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
                 $this->insert_table($table,$insert_data_lines);
             }                     
                               
             

         }        
         
         return $reorder_number;
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
             $this->insert_table($table,$insert_data);
     }

     function get_po_calendar($supplier_code)
     {
         $this->db->select('*');
         $this->db->from('po_calendar as poc');
         $this->db->join('po_date as pod','pod.po_id = poc.po_id','INNER');         
         $this->db->where('no_',$supplier_code);
         $query = $this->db->get();
         return $query->result_array();
     }


     function insert($table,$data)
     {
        $this->db->set($data);
        $this->db->insert($table);
     }

     // herbert added code 8/29/2023..........................
     function check_data($check_data)
     {
       $this->db->from('reorder_po');
       $this->db->where($check_data);
       $query = $this->db->get();
       return $query->num_rows() > 0;
        
     }


     function get_reorder_po($check_data)
     {
         $this->db->select('*');
         $this->db->from('reorder_po');
         $this->db->where($check_data);
         $query = $this->db->get();
         return $query->result_array();
     }


      function check_data_po($check_data)
     {
       $this->db->from('reorder_po');
       $this->db->where($check_data);
       $query = $this->db->get();
       return $query->num_rows() > 0;
        
     }


     function insert_seasonal_data($document_no,$store_id)
     {
      $data = array(
                    'document_number'=> $document_no,
                    'store_id'   => $store_id,
                   );
      $this->db->insert("reorder_po",$data);
     }


    function get_season_po_list()
    {
     $this->db->select('pend.document_no,str_ent.store_id');
     $this->db->from('season_reorder_pending_qty as pend');
     $this->db->join('season_reorder_store_entry as str_ent','str_ent.store_entry_id = pend.store_entry_id','INNER');
     $this->db->join('reorder_store as store','store.store_id = str_ent.store_id','INNER');
     $this->db->group_by("pend.document_no");
     $query = $this->db->get();
     return $query->result_array();
    }

    function get_reorder_po_list()
    {
     $this->db->select('document_no,store_id');
     $this->db->from('reorder_report_data_po as po');
     $this->db->join('reorder_store as str','str.databse_id  = po.db_id','INNER');
     $this->db->group_by("po.document_no");
     $query = $this->db->get();
     return $query->result_array();
    }


    function get_all_reorder_report_data_batch()
    {
         $this->db->select("*");
         $this->db->from("reorder_report_data_batch");
         $query = $this->db->get();
         return $query->result_array();
    }


    function get_all_user()
    {
        $this->db->select("*");
        $this->db->from("reorder_users as usr");
        $this->db->join("reorder_store as store"."store.store_id = usr.store_id","inner");
        $query = $this->db->get();
        return $query->result_array();
    }


    function get_all_reorder_report_data_header_final()
    {
        $this->db->select("*");
        $this->db->from("reorder_report_data_header_final");
        $this->db->group_by("supplier_code");
        $query = $this->db->get();
        return $query->result_array();
    }

    function get_all_vendor_po_calendar()
    {
        $this->db->select("no_");
        $this->db->from("po_calendar");
        $query =  $this->db->get();
        return $query->result_array();
    }


    function get_last_entry_mms_middleware($db_id)
    {
        $this->mdlw->select('*');
        $this->mdlw->from('mms_middleware');
        $this->mdlw->where('db_id',$db_id);
        $this->mdlw->order_by('document_no','desc'); 
        $this->mdlw->limit(1);
        $query = $this->mdlw->get();
        return $query->result_array();
    }


   function insert_mdl_table($table,$insert_data)
   {
         $this->mdlw->insert($table, $insert_data);
         return $this->mdlw->insert_id();
   }

   function check_middleware($db_id,$document_no,$no_)
   {
      $this->mdlw->select("*");
      $this->mdlw->from('mms_middleware');
      $this->mdlw->where("db_id",$db_id);
      $this->mdlw->where("document_no",$document_no);
      $this->mdlw->where("no_",$no_);
      $query =  $this->mdlw->get();
      return $query->result_array();
   }

    function update_mdl_table($table,$column_data,$column_filter)
    {
        $this->mdlw->set($column_data);
        $this->mdlw->where($column_filter);
        $this->mdlw->update($table);        
    }

    function get_middleware_data($year_1,$year_2,$year_3,$month_1,$month_2,$month_3,$vendor_no,$item_code)
    {
         if(strstr($item_code,"-"))
         {
              $exp_item = explode("-",$item_code);

              $where = "AND 
                             (no_ = '".$exp_item[0]."'  AND variant_code='".$exp_item[0]."')
                       ";
         }
         else 
         {
             $where = " AND
                           no_ = '".$item_code."'";
         }


         $query = $this->mdlw->query("SELECT
                                           * 
                                      FROM wms.mms_middleware 
                                      WHERE
                                      (
                                         (year='".$year_1."' AND month = '".$month_1."') OR
                                         (year='".$year_2."' AND month = '".$month_2."') OR
                                         (year='".$year_3."' AND month = '".$month_3."')
                                      )
                                      AND
                                          vendor_no = '".$vendor_no."'
                                      ".$where."
                                    ; 
                                    ");

        return $query->result_array();                                         
    }


    function search_mms_middleware_header($document_no,$textfile_name,$db_id)
    {
         $this->mdlw->select("*");
         $this->mdlw->from("mms_middleware_header");
         if($document_no != '')
         {
             $this->mdlw->where("document_no",$document_no);
         }
         if($textfile_name != '')
         {
             $this->mdlw->where("textfile_name",$textfile_name);
         }
         if($db_id != '')
         {
             $this->mdlw->where("db_id",$db_id);
         }

         $query = $this->mdlw->get();
         return $query->result_array();
    }





    // function search_mms_middleware_lines($hd_id,$item_code)
    // {
    //      $this->mdlw->select("*"); 
    //      $this->mdlw->from("mms_middleware_lines");
    //      $this->mdlw->where("hd_id",$hd_id);
    //      $this->mdlw->where("item_code",$item_code);
    //      $query = $this->mdlw->get();
    //      return $query->result_array();
    // }


    function get_smgm_textfiles($from,$to,$vendor_no,$db_id)
    {
        $this->mdlw->select("*");
        $this->mdlw->from("mms_middleware_header as head");
        $this->mdlw->join("mms_middleware_lines as mdl", "mdl.hd_id = head.hd_id", "INNER");
        $this->mdlw->where("head.date_ >=", $from);
        $this->mdlw->where("head.date_ <=", $to);
        $this->mdlw->where("head.vendor", $vendor_no);
        $this->mdlw->where("db_id",$db_id);
        $this->mdlw->where("status !=","CANCELED");
        $this->mdlw->where("head.textfile_name NOT LIKE '%-PST'", null, false);
        $query = $this->mdlw->get();

        // echo $this->mdlw->last_query();

        return $query->result_array();
    }

/* End of the Code -----------------------------------------------------------------------------------------*/
}    
