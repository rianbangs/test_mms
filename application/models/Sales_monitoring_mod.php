
<?php
class Sales_monitoring_mod extends CI_Model
{
  function __construct()
    {
        parent::__construct();
        $this->nav = $this->load->database('navision', TRUE);
       
    }

// ============================================================================= SALES MONITORING UI =================================================================================
// function get year ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    function select_year($select_store_sales)
    {
    $query = $this->db->query("
                               SELECT                                     
                                      cons_date,DATE_FORMAT(STR_TO_DATE(cons_date, '%m-%d-%y'), '%b-%d-%Y') AS month_sales, YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) AS year
                                 FROM 
                                      nav_cons_header 
                                WHERE
                                      store = '$select_store_sales'
                             GROUP BY
                                      cons_date                                        
                              ");
     return $query->result_array();
    }

// function get year ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    function select_year_conp($select_store_payments)
    {
     $query = $this->db->query("
                               SELECT                                      
                                      conp_date,DATE_FORMAT(STR_TO_DATE(conp_date, '%m-%d-%y'), '%b-%d-%Y') AS month_payments, YEAR(STR_TO_DATE(conp_date, '%m-%d-%y')) AS year
                                 FROM 
                                      nav_conp_header
                                WHERE
                                      store = '$select_store_payments'
                             GROUP BY
                                      conp_date                                        
                              ");
     return $query->result_array();
    }


// function get store :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    function select_store()
    {
     $this->db->select("store,nav_store_val");
     $this->db->from("nav_cons_header as cons");
     $this->db->join("nav_store_names as store", "cons.store = store.nav_store", "inner");
     $this->db->group_by('store');
     $query = $this->db->get();
     return $query->result_array();
    }

// function get store :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    function select_store2()
    {
     $this->db->select("store,nav_store_val,store_no");
     $this->db->from("nav_cons_header as cons");
     $this->db->join("nav_store_names as store", "cons.store = store.nav_store", "inner");
     $this->db->group_by('cons.store_no');
     $query = $this->db->get();
     return $query->result_array();
    }
    
// function get montly and yearly total sales ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    function get_monthly_report_mod($store,$year,$pre_year)
    {
        $cond = '';
    
        if($store != 'Select_all_store')
            $cond = "AND store = '$store' ";

        $query = $this->db->query("
                                       SELECT
                                              store_no,store,item_division,SUM(total_rounded_amt) AS total,
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
                                        $cond                                     
                                     GROUP BY
                                              store,
                                              item_division,
                                              YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')),
                                              MONTH(STR_TO_DATE(cons_date, '%m-%d-%y'))
                                        
                                    ORDER BY 
                                             item_division ASC,
                                             store ASC,
                                             YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')),
                                             MONTH(STR_TO_DATE(cons_date, '%m-%d-%y'));
                                              
                                      ");
             return $query->result_array();
     
    }
         
    


// function get yearly total sales  :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    function get_yearly_report_mod($pre_year,$year,$store)
    {

       $cond = '';
       if($store != 'Select_all_store')
      
        $cond = "AND store = '$store' ";

          $query = $this->db->query("
                                 SELECT 
                                       store_no,store,item_division,SUM(total_rounded_amt) AS total,
                                       YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) AS year,
                                       SUM(quantity) as total_quantity_yearly

                                  FROM
                                       nav_cons_header
                                 where 
                                      
                                       YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) >= '$pre_year'
                                  AND 
                                       YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) <= '$year'
                                $cond
                               
                             GROUP BY
                                      store,
                                      item_division,
                                      YEAR(STR_TO_DATE(cons_date, '%m-%d-%y'))        
                             ORDER BY 
                                      store asc,
                                      item_division asc,
                                      YEAR(STR_TO_DATE(cons_date, '%m-%d-%y'))
                                              
                                   ");
             return $query->result_array();
      
     
    }

// function get month name :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

    function get_month_name($store,$month,$pre_month)
    {
      $query = $this->db->query("
                                 SELECT 
                                        DATE_FORMAT(STR_TO_DATE(cons_date, '%m-%d-%y'), '%M') AS month_name
                                   FROM
                                        nav_cons_header
                                  WHERE
                                        MONTH(STR_TO_DATE(cons_date, '%m-%d-%y')) BETWEEN '$pre_month' AND '$month'
                                    AND 
                                        store = '$store'
                               GROUP BY
                                        MONTH(STR_TO_DATE(cons_date, '%m-%d-%y'))
                               ");
     return $query->result_array(); 
    }

// function get select year range :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    function get_year_range($year)
    {
      $query = $this->db->query("select * 
                                   FROM mpdi.nav_cons_header 
                                  where YEAR(DATE_SUB(STR_TO_DATE(cons_date, '%m-%d-%y'), INTERVAL 2 YEAR)) = '$year' limit 1");
      return $query->result_array();
    }

// function get select month range ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    function get_month_range($month)
    {
     $query = $this->db->query("Select *
                                  FROM mpdi.nav_cons_header
                                 where MONTH(DATE_SUB(STR_TO_DATE(cons_date, '%m-%d-%y'), INTERVAL 2 MONTH)) = '$month' limit 1");
     return $query->result_array();
    }
 // function get division name ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    function get_div_name_mod($division_code)
    {
     $this->db->select("div_name");
     $this->db->from("division_tbl");
     $this->db->where("div_code", $division_code);
     $query = $this->db->get();
     return $query->result_array();
    }

 // function get total quantity monthly and yearly ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
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

// function get total quantity yearly :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    function get_yearly_store_mod($pre_year,$year)
    {

       $query = $this->db->query("
                                    SELECT 
                                           store,SUM(total_rounded_amt) AS total,
                                           YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) AS year,
                                           SUM(quantity) as total_quantity_yearly   
                                      FROM
                                           nav_cons_header 
                                     WHERE
                                           YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) >= '$pre_year'
                                       AND 
                                           YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) <= '$year'
                                  GROUP BY YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')), 
                                           store         
                                  ORDER BY 
                                           store asc,
                                           YEAR(STR_TO_DATE(cons_date, '%m-%d-%y'))
                                           
                                ");

       return $query->result_array();
    }

// function  get store names :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    function names_store($store_names)
    {
     $this->db->select("*");
     $this->db->from("nav_store_names");
     $this->db->where("nav_store", $store_names);
     $query = $this->db->get();
     return $query->result_array();  
    }

// function get year ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    function select_year_filter()
    {
      $query = $this->db->query(" SELECT
                                         YEAR(STR_TO_DATE(cons_date, '%m-%d-%y')) AS year
                                   FROM
                                         nav_cons_header
                                GROUP BY
                                         YEAR(STR_TO_DATE(cons_date, '%m-%d-%y'))
                                ORDER BY  
                                         YEAR(STR_TO_DATE(cons_date, '%m-%d-%y'))");  
       return $query->result_array();  
    }

    function select_month_filter()
{
    $query = $this->db->query("
                                SELECT
                                       MONTH(STR_TO_DATE(cons_date, '%m-%d-%y')) AS month
                                  FROM
                                       nav_cons_header
                              GROUP BY
                                       MONTH(STR_TO_DATE(cons_date, '%m-%d-%y'))
                              ORDER BY
                                       MONTH(STR_TO_DATE(cons_date, '%m-%d-%y'))");

    return $query->result_array();
}


// ================================================================== SUPER SALES UPLOADING UI =================================================================================
// function insert nav_conp_header table ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    function insert_nav_conp_header($store,$conp_date,$Store_no,$Tender_type,$Card_no,$Amount_tendered,$file_type)
    {
      $data = array(
                     'store'           => $store,
                     'conp_date'       => $conp_date,
                     'Store_no'        => $Store_no,
                     'Tender_type'     => $Tender_type,
                     'Card_no'         => $Card_no,
                     'Amount_tendered' => $Amount_tendered,
                     'file_type_p'       => $file_type
                   );
      $this->db->insert('nav_conp_header', $data);
    }

// function insert nav_cons_header table ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  function insert_nav_cons_header_table(
                                        $store,
                                        $cons_date,
                                        $store_no,
                                        $item_no,
                                        $variant_code,
                                        $unit_of_measure,
                                        $item_division,
                                        $item_department,
                                        $item_group,
                                        $quantity,
                                        $total_rounded_amt,
                                        $discount_amount,
                                        $line_discount,
                                        $total_discount,
                                        $total_disc,
                                        $periodic_discount,
                                        $disc_amount_from_std_price,
                                        $vat_amount,
                                        $file_type
                                       )
    {

     

      $data = array(
                    'store'                      => $store,
                    'cons_date'                  => $cons_date,
                    'store_no'                   => $store_no,
                    'item_no'                    => $item_no,
                    'variant_code'               => $variant_code,
                    'unit_of_measure'            => $unit_of_measure,
                    'item_division'              => $item_division,
                    'item_department'            => $item_department,
                    'item_group'                 => $item_group,
                    'quantity'                   => $quantity,
                    'total_rounded_amt'          => $total_rounded_amt,
                    'discount_amount'            => $discount_amount,
                    'line_discount'              => $line_discount,
                    'total_discount'             => $total_discount,
                    'total_disc'                 => $total_disc,
                    'periodic_discount'          => $periodic_discount,
                    'disc_amount_from_std_price' => $disc_amount_from_std_price,
                    'vat_amount'                 => $vat_amount,                 
                    'file_type'                  => $file_type                  
                  );

       $this->db->insert('nav_cons_header' ,$data);    
              
    }

// function check if data exist nav_cons_header  :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    public function isDataExists($check_data)
    {
     $this->db->from('nav_cons_header');
     $this->db->where($check_data);
     $query = $this->db->get();
     echo $this->db->last_query();
     return $query->num_rows() > 0;
    }

// function check if data exist nav_conp_header  :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    function check_nav_conp_header($data)
    {
     $this->db->from('nav_conp_header');
     $this->db->where($data);
     $query = $this->db->get();
     return $query->num_rows() > 0;   
    }

//============================================================================= UOM UPLOADING MODEL ======================================================================================
// function get column filter ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    function get_data_store()
    {
     $this->db->select("store");
     $this->db->from("nav_uom_header");
     $this->db->group_by('store');
     $query = $this->db->get();
     return $query->result_array();  
    }

// function get column code  :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    function get_data_code($store)
    {
     $this->db->select("code");
     $this->db->from("nav_uom_header");
     $this->db->where("store", $store);
     $this->db->group_by('code');
     $query = $this->db->get();
     return $query->result_array();  
    }

// function check if data exist nav_uom_header  ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    public function check_data($check_data)
    {
     $this->db->from('nav_uom_header');
     $this->db->where($check_data);
     $query = $this->db->get();
     return $query->num_rows() > 0;
    }
// function insert nav_uom_header  :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    function insert_nav_uom_header($store,$Item_No,$code,$qty_per_unit_of_measure,$length,$width,$height,$cubage,$weight,$primary_key,$num_in_barcode,$print_shelf_label,$text_on_shelf_label)
    {

      $insert_data = array(
                          'store'                   => $store,
                          'Item_No'                 => $Item_No,
                          'code'                    => $code,
                          'qty_per_unit_of_measure' => $qty_per_unit_of_measure,
                          'length'                  => $length,
                          'width'                   => $width,
                          'height'                  => $height,
                          'cubage'                  => $cubage,
                          'weight'                  => $weight,
                          'primary_key'             => $primary_key,
                          'num_in_barcode'          => $num_in_barcode,
                          'print_shelf_label'       => $print_shelf_label,
                          'text_on_shelf_label'     => $text_on_shelf_label
                        );

       $this->db->insert('nav_uom_header' ,$insert_data);    
    }

  // function get division code ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    function get_division_code($div_code)
    {
     $this->db->from('division_tbl');
     $this->db->where($div_code);
     $query = $this->db->get();
     return $query->num_rows() > 0;
    }


}
