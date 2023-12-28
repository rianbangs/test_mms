<?php


   class Po_view_mod extends CI_Model
   {

     function __construct()
     {
        parent::__construct();
        $this->nav = $this->load->database('navision', TRUE);
        $this->pis = $this->load->database('pis',TRUE);
       
     }


     // herbert added code 8/29/2023..........................
     function check_data($check_data)
     {
       $this->db->from('reorder_po');
       $this->db->where($check_data);
       $query = $this->db->get();
       return $query->num_rows() > 0;
        
     }


      function check_data_po($check_data)
     {
       $this->db->from('reorder_po');
       $this->db->where($check_data);
       $query = $this->db->get();
       return $query->num_rows() > 0;
        
     }


     function insert_seasonal_data($document_no,$store_id,$status,$vendor_code,$vendor_name)
     {
      $data = array(
                    'document_number' => $document_no,
                    'store_id'        => $store_id,
                    'status'          => $status,
                    'vendor_code'     => $vendor_code,
                    'vendor_name'     => $vendor_name
                   );
      $this->db->insert("reorder_po",$data);
     }


     function get_season_po_list()
    {
     // $this->db->select('pend.document_no,str_ent.store_id');
     // $this->db->from('season_reorder_pending_qty as pend');
     // $this->db->join('season_reorder_store_entry as str_ent','str_ent.store_entry_id = pend.store_entry_id','INNER');
     // $this->db->join('reorder_store as store','store.store_id = str_ent.store_id','INNER');

     // $this->db->group_by("pend.document_no");
     // $query = $this->db->get();
     // return $query->result_array();

      $query = $this->db->query("Select pend.document_no,ro_ent.store_id,vendor_code,vendor_name
                                  from
                                             season_reorder_batch as ro_btc
                                  inner join
                                             season_reorder_item_entry as ro_itm on ro_itm.batch_id = ro_btc.batch_id
                                  inner join
                                             season_reorder_store_entry as ro_ent on ro_ent.entry_id = ro_itm.entry_id
                                  inner join
                                             reorder_store as ro_str on ro_str.store_id = ro_ent.store_id
                                  inner join 
                                             season_reorder_pending_qty as pend on  pend.store_entry_id = ro_ent.store_entry_id

                                  group by pend.document_no");
      return $query->result_array();
    }

    function get_reorder_po_list()
    {
     $this->db->select('document_no,store_id,supplier_code,supplier_name');
     $this->db->from('reorder_report_data_po as po');
     $this->db->join('reorder_store as str','str.databse_id  = po.db_id','INNER');
     $this->db->join('reorder_report_data_header_final as ro_rpt','ro_rpt.reorder_batch  = po.reorder_batch','INNER');
     $this->db->group_by("po.document_no");
     $query = $this->db->get();
     return $query->result_array();
    }
    /* End of the Code -----------------------------------------------------------------------------------------*/






  }



?>