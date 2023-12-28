<?php
	class Sale_monitoring_ctrl extends CI_Controller
	{
	      function __construct()
	     {
	        parent::__construct();
	        $this->load->library('session');
// =======================================================================================================================================================================================
	        $this->load->model('Sales_monitoring_mod'); 
	     }

	     function get_stores_and_years(){
	     	$store = $this->Sales_monitoring_mod->select_store();
           	$year  = $this->Sales_monitoring_mod->select_year_filter();

           	echo json_encode(array($store,$year));
	     }

	     function get_stores_and_years_filter(){
	     	$store        = $this->Sales_monitoring_mod->select_store();
           	echo json_encode(array($store));
	     }

	     function get_select_store_payments()
	     {
	      $year_conp = $this->Sales_monitoring_mod->select_year_conp($_POST['select_store_payments']);	
	      echo json_encode(array($year_conp));
	     }

	     function get_select_store_sales()
	     {
	      $year_cons = $this->Sales_monitoring_mod->select_year($_POST['select_store_sales']);
	      echo json_encode(array($year_cons));
	     }

// ================================================================================= SUPER SALES UPLOADING UI ==========================================================================
// function file upload payment ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
	    function payments_upload()
	    { 

          $uploadedFile      = $_FILES['file_select_p'];
		  $fileName          = $uploadedFile['name'];
		  $fileTmpPath       = $uploadedFile['tmp_name'];
		  $fileSize          = $uploadedFile['size'];
		  $fileError         = $uploadedFile['error'];


		  $substring 	     = substr($fileName, 0, 8); // get the file name ASC-MIAS 
		  $file_type         = '';

		  if($substring == 'ASC-MIAS')
		  {
		   $file_type = 'ASC-MIAS';
		  }else{
		  		$file_type = 'regular';
		       }


		  $card_no           = '';
		  $tender_type       = '';
		  $newDate           = '';
		  $store             = '';
		  $store_no          = '';
		  $amount_tendered   = '';

          
		   if ($fileError === UPLOAD_ERR_OK) 
		   {

				// get file upload payment :::::::::::::::::::::::::::::::::::::::::::::::::::
				        $fileContent       = file_get_contents($fileTmpPath);
				        $explode_da        = explode(PHP_EOL, $fileContent);

				// get file upload sales :::::::::::::::::::::::::::::::::::::::::::::::::::::
		        
				     for($c=0; $c<count($explode_da); $c++)
				     {
				     	$explode_2nd = explode("|",$explode_da[$c]);
				     	if(count($explode_2nd)>=5)
				     	{
					     	  if(!empty($explode_2nd[0]))
					     	  {
	                            $newDate = str_replace('/', '-', $explode_2nd[0]);
					     	  }else{
					     	  		$newDate = '';
					     	       }
					           
					          if(!empty($explode_2nd[1]))
					          {
					            $store_no  = $explode_2nd[1];
					            $get_store = explode("-",$store_no);
					            $store     = $get_store[0];
					          }else{
	                                $store_no = '';
	                                $store    = '';
					               }

					          if(!empty($explode_2nd[2]))
					          {
					          	$tender_type = $explode_2nd[2];
					          }else{
					          		 $tender_type = '';
					               }

					          if(!empty($explode_2nd[3]))
					          {
					          	$card_no = $explode_2nd[3];
					          }else{
					          		$card_no = '';
					               }

					          if(!empty($explode_2nd[4]))
					          {
					          	//$amount_tendered = $explode_2nd[4];
					          	 $amount_tendered  = str_replace(array(',', '-'), '', $explode_2nd[4]);
					          }else{
					          		 $amount_tendered = '';
					               }

			  	     	    $data = array(
							              'store'           => $store,
							              'conp_date'       => $newDate,
							              'Store_no'        => $store_no,
							              'Tender_type'     => $tender_type,
							              'Card_no'         => $card_no,
							              'Amount_tendered' => $amount_tendered,  
							              'file_type_p'     => $file_type  
							             );

			  	     	    $check_nav_conp_header = $this->Sales_monitoring_mod->check_nav_conp_header($data);

			  	     	    if ($check_nav_conp_header) {
						           echo 'Data exists';
					    	} else {
				                    $this->Sales_monitoring_mod->insert_nav_conp_header($store,$newDate,$store_no,$tender_type,$card_no,$amount_tendered,$file_type);
					    		   }

				       } // end of second if condition
				        	

				     } // end of for loop
		     
			}else{ 
			      // echo json_encode(['status' => 'error', 'message' => 'File upload failed.']);
			     }




		 }

// =======================================================================================================================================================================================
// function file upload sales ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

function sales_upload()
{
    
		    $memory_limit = ini_get('memory_limit');
            ini_set('memory_limit', -1);
            ini_set('max_execution_time', 0);
			// ini_set('memory_limit', '1024M'); // Set memory limit to 1GB


            // Retrieve the uploaded file content
            $fileContent = $_POST['file_content']; // JSON string of the file content

            $fileType = $_POST['fileType'];
            $file_name = $_POST['file_name'];

            // Convert the JSON string back to the original file content
            $filecontent = json_decode($fileContent);
           
            
            echo  '
                    <!DOCTYPE html>
                    <html>
                    <head>
                            <meta charset="utf-8">
                            <title>DATA UPLOAD</title>
                            <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">     
  
                            <link href="' . base_url() . 'assets/css/font-awesome.min.css" rel="stylesheet">
                            <link href="' . base_url() . 'assets/css/bootstrap.min.css" rel="stylesheet">
                            <script src="' . base_url() . 'assets/progress_bar/js/jquery-1.3.2.js"></script>           
                            


    
                    </head>
                    <body>
                          <h1></h1>


                           <div class="col-md-12" style="padding: 15px;margin-top: -11px;background: #150c42;color: #4dc3d7;">
                            <div class="col-md-12 pdd_1"></div>                                         
                            <div class="row" style="padding-left: 18px;font-size: 17px;">                    
                                <label class="col-md-12 pdd" style="margin:0px">
                                    <i class="fa fa-cloud-upload"></i>
                                    PROCESSING DATA
                                    &nbsp;&nbsp;<img src="' . base_url() . 'assets/img/nobackground white.gif" height="40" style="padding-bottom: 5px;">
                                  <span class="col-md-12 pdd fnt13 exist" id="exist" style="font-size: 17px;"></span>                                         
                                </label>  
                                <span class="col-md-7 pdd fnt13 status">Status: 0% Complete </span>                                            
                                <span class="col-md-4 pdd fnt13 toright rowprocess" style="margin-left: -130px;"> 0</span>
             
                              
                            </div>
                            
                            

                            <div class="progress" style="height: 28px;">
                                <div class="progress-bar progress-bar-striped bg-success progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%" id="percontent"></div>
                            </div>

                            <span class="col-md-12 pdd fnt13 empname" style="font-size: 17px; margin-left: 17px;">Entry:</span>
                            <span class="col-md-12 pdd fnt13 filename" style="margin-left: 5px;   font-size: 17px;"></span>
                          
                            <span class="col-md-12 pdd fnt13" style="font-size: 17px;  margin-left: 236px;">File Name:'.' '.$file_name.'</span>
                        </div>


                    ';


               
                flush();
                ob_flush();
                usleep(100);
                $rowproC = 1;

                $counter = 0;
                $newDate_sales               = '';
                $store_no_sales              = '';
                $store_name_sales            = '';
                $item_no_sales               = '';
                $variant_code                = '';
                $unit_of_measure             = '';
                $item_division               = '';
                $item_department             = '';
                $item_group                  = '';
                $quantity                    = '';
                $total_rounded_amt           = '';
                $discount_amount             = '';
                $line_discount               = '';
                $total_discount              = '';
                $total_disc                  = '';
                $periodic_discount           = '';
                $disc_amount_from_std_price  = '';
                $vat_amount                  = '';

                $total_files = count($filecontent);
                for ($a = 0; $a < count($filecontent); $a++) {

                    if ($rowproC > 0 && $total_files > 0) {
                        $percent = intval($rowproC / $total_files * 100) . "%";
                    } else {
                        $percent = "100%";
                    }

                    $explode_2nd_sales = explode("|", $filecontent[$a]);

                    if(count($explode_2nd_sales)>=17)
                    {

                                 // ..................................................................
                              if(!empty($explode_2nd_sales[0]))
                              {
                                $newDate_sales = str_replace('/', '-', $explode_2nd_sales[0]);
                              }else{
                                    $newDate_sales = '';
                                   }

                    
                              // ..................................................................
                              if(!empty($explode_2nd_sales[1]))
                              {
                                $store_no_sales       = $explode_2nd_sales[1];
                                $get_store_S          = explode("-",$store_no_sales);
                                $store_name_sales     = $get_store_S[0];
                              }else{
                                    $store_no_sales = '';
                                    $store_name_sales = '';
                                   }

                              // ..................................................................
                              if(!empty($explode_2nd_sales[2]))
                              {
                                $item_no_sales  = $explode_2nd_sales[2];
                          
                              }else{
                                    $item_no_sales = '';
                                   }

                              // ..................................................................
                              if(!empty($explode_2nd_sales[3]))
                              {
                               $variant_code  = $explode_2nd_sales[3];
                              }else{
                                    $variant_code = '';
                                   }

                              // ..................................................................
                              if(!empty($explode_2nd_sales[4]))
                              {
                               $unit_of_measure  = preg_replace('/[^a-zA-Z0-9\s]/', '', $explode_2nd_sales[4]); 
                              }else{
                                    $unit_of_measure = '';
                                   }


                              // ..................................................................
                              if(!empty($explode_2nd_sales[5]))
                              {
                                $store_no_sales       = $explode_2nd_sales[1];
                                $get_store_           = explode("-",$store_no_sales);
                                $store_name_          = $get_store_[0];
                                
                                $check_divison = array('div_code' => $explode_2nd_sales[5]);
                                $check_if_exist = $this->Sales_monitoring_mod->get_division_code($check_divison);

                                        // check if division code exist in other stores.......................................
                                        if($store_name_.'-S0015' == $store_no_sales)
                                        {
                                            if($check_if_exist)
                                            {
                                             $item_division = 'SOD'.$explode_2nd_sales[5];
                                            }else{
                                                   $item_division = $explode_2nd_sales[5];
                                                 }

                                        }else{
                                              $item_division  = $explode_2nd_sales[5];
                                             }

                              }else{
                                    $item_division = '';
                                   }


                              // ..................................................................
                              if(!empty($explode_2nd_sales[6]))
                              {
                               $item_department      = $explode_2nd_sales[6];
                              }else{
                                    $item_department = '';
                                   }

                              // ..................................................................
                              if(!empty($explode_2nd_sales[7]))
                              {
                               $item_group      = $explode_2nd_sales[7];
                              }else{
                                    $item_group = '';
                                   }


                              // ..................................................................
                              if(!empty($explode_2nd_sales[8]))
                              {

                               $quantity  = str_replace(array(',', '-'), '', $explode_2nd_sales[8]);

                              }else if($explode_2nd_sales[9] == '0'){

                                    $quantity = '0';

                                   }else{

                                   		$quantity = '';

                                   }

                              // ..................................................................
                              if(!empty($explode_2nd_sales[9]))
                              {

                               $total_rounded_amt  = str_replace(array(',', '-'), '', $explode_2nd_sales[9]);

                              }else if($explode_2nd_sales[9] == '0'){

                                    $total_rounded_amt = '0';

                                   }else{

                                   		$total_rounded_amt = '';

                                   }

                              // ..................................................................
                              if(!empty($explode_2nd_sales[10]))
                              {
                               $discount_amount      = $explode_2nd_sales[10];
                              }else{
                                    $discount_amount = '';
                                   }


                              // ..................................................................
                              if(!empty($explode_2nd_sales[11]))
                              {
                               $line_discount      = $explode_2nd_sales[11];
                              }else{
                                    $line_discount = '';
                                   }

                              // ..................................................................
                              if(!empty($explode_2nd_sales[12]))
                              {
                               $total_discount      = $explode_2nd_sales[12];
                              }else{
                                    $total_discount = '';
                                   }

                              // ..................................................................
                              if(!empty($explode_2nd_sales[13]))
                              {
                               $total_disc      = $explode_2nd_sales[13];
                              }else{
                                    $total_disc = '';
                                   }

                              // ..................................................................
                              if(!empty($explode_2nd_sales[14]))
                              {
                               $periodic_discount      = $explode_2nd_sales[14];
                              }else{
                                    $periodic_discount = '';
                                   }

                              // ..................................................................
                              if(!empty($explode_2nd_sales[14]))
                              {
                               $disc_amount_from_std_price      = $explode_2nd_sales[14];
                              }else{
                                    $disc_amount_from_std_price = '';
                                   }

                              // ..................................................................
                              if(!empty($explode_2nd_sales[14]))
                              {
                               $vat_amount      = $explode_2nd_sales[14];
                              }else{
                                    $vat_amount = '';
                                   }

                                 $check_data = array(
                                                     'store'                      => $store_name_sales,
                                                     'cons_date'                  => $newDate_sales,
                                                     'store_no'                   => $store_no_sales,
                                                     'item_no'                    => $item_no_sales,
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
                                                     'file_type'                  => $fileType
                                                    );




                                 $isDataExists = $this->Sales_monitoring_mod->isDataExists($check_data);
								 var_dump($isDataExists);					
                                 if(empty($isDataExists))
                                 {
                                            //$this->db->insert('nav_cons_header' ,$check_data);   

                                            echo '<script language="JavaScript">
                                                     $("span.filename").text("Item Code -' . $item_no_sales . '")
                                                     $("div#percontent").css({"width":"' . $percent . '"})
                                                     $("span.status").text("Status: ' . $percent . ' Complete")
                                                     $("span.rowprocess").text("Processed Row: ' . $rowproC++ . ' out of ' . $total_files . '")
                                                     $("span.empname").text("Entry: ") 

                                                     var spanElement = document.getElementById("exist");
                                                     spanElement.style.display = "none";
                                              </script>
                                                     ';
                                            str_repeat(' ', 1024 * 64);
                                            flush();
                                            ob_flush();
                                            usleep(100);

                                 }else{


                                        echo '<script language="JavaScript">
                                                 $("span.filename").text("Item Code -' . $item_no_sales . '")
                                                 $("div#percontent").css({"width":"' . $percent . '"})
                                                 $("span.status").text("Status: ' . $percent . ' Complete")
                                                 $("span.rowprocess").text("Processed Row: ' . $rowproC++ . ' out of ' . $total_files . '")
                                                 $("span.exist").text("Already Inserted")
                                          </script>
                                                 ';
                                        str_repeat(' ', 1024 * 64);
                                        flush();
                                        ob_flush();
                                        usleep(100);
                                 }
                    }
                }


  

		 }


// =======================================================================================================================================================================================
// function view uploaded sales server side ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

           function view_service_side()
           {
        
			    $year         = $this->input->post('year');
				$select_store = $this->input->post('select_store');
				$start        = $this->input->post('start');
				$length       = $this->input->post('length');
				$searchValue  = $this->input->post('search')['value'];

				$query = $this->db->select('DATE_FORMAT(STR_TO_DATE(cons_date, "%m-%d-%y"), "%b-%d-%Y") AS formatted_date,store,item_no, item_division,item_department,item_group, cons_date, item_no, unit_of_measure, quantity, total_rounded_amt')
					     ->from('nav_cons_header')
					     ->group_start()
					     ->like('store', $searchValue)
					     ->or_like('item_no', $searchValue)
					     ->or_like('item_division', $searchValue)
					     ->or_like('item_department', $searchValue)
					     ->or_like('item_group', $searchValue)
					     ->or_like('cons_date', $searchValue)
					     ->or_like('item_no', $searchValue)
					     ->or_like('unit_of_measure', $searchValue)
					     ->or_like('quantity', $searchValue)
					     ->or_like('total_rounded_amt', $searchValue)
					     ->group_end();

						if (!empty($year)) {
						    $this->db->where('cons_date', $year);
						}

						if (!empty($select_store)) {
						    $this->db->where('store', $select_store);
						}

						$this->db->limit($length, $start);
						$query = $this->db->get();

				$row_count = $query->num_rows();
				$totalRecords = $this->db->count_all_results('mpdi.nav_cons_header');
				$data = array(
							  'draw'            => $this->input->post('draw'),
							  'recordsTotal'    => $totalRecords,
							  'recordsFiltered' => $totalRecords,
							  'data'            => $query->result()
							 );
				echo json_encode($data);


           }
// ================================================================================================================================================================================
// function payments view record server side ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::      
           function payments_table_view_service_side()
           {
           	$year         = $this->input->post('year');
		    $select_store = $this->input->post('select_store');
            $start        = $this->input->post('start'); 
		    $length       = $this->input->post('length'); 
		    $searchValue  = $this->input->post('search')['value']; 


		    $query = $this->db->select('DATE_FORMAT(STR_TO_DATE(conp_date, "%m-%d-%y"), "%b-%d-%Y") AS formatted_date,store,conp_date,Store_no,Tender_type,Card_no,Amount_tendered')
		                      ->from('nav_conp_header')
		                      ->group_start()
		                      ->like('store', $searchValue)
						      ->or_like('conp_date', $searchValue)
						      ->or_like('Store_no', $searchValue)
						      ->or_like('Tender_type', $searchValue)
						      ->or_like('Card_no', $searchValue)
						      ->or_like('Amount_tendered', $searchValue)
						      ->group_end();

					            if (!empty($year)) {
						    	    $this->db->where('conp_date', $year);
						    	}
    
						    	if (!empty($select_store)) {
						    	    $this->db->where('store', $select_store);
						    	}
		                   
								$this->db->limit($length, $start);
				                $query = $this->db->get();

		    $totalRecords = $this->db->count_all('mpdi.nav_conp_header');
		    $data = array(
				          'draw'            => $this->input->post('draw'), 
				          'recordsTotal'    => $totalRecords,
				          'recordsFiltered' => $totalRecords,
				          'data'            => $query->result()
		                 );
		    echo json_encode($data); 
           }


// ===================================================================================================================================================================================
// function get montly and yearly report :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
         function view_yearly_montly_report()
	     {

	        $div_code             = '';
	        $tot                  = '';
	        $totalSum             = '0.00';
	        $totalSum_            = '0.00';
	        $tbl                  = '';
	        $range                = $_POST['range'];
	        $store                = $_POST['store'];
	        $year                 = $_POST['year'];
	        $report_type          = $_POST['report_type'];
	        $division_type        = $_POST['division'];
	        $totalSumAllDivisions = 0.00;
               
            // get 3 previous year ...............................................................
            $original_year        = $year;
	    	$sub_year             = 2;
	        $pre_year             = $original_year - $sub_year;
   
            $get_report_store     = $this->Sales_monitoring_mod->get_report_store($year,strval($pre_year)); 
            $get_monthly          = $this->Sales_monitoring_mod->get_monthly_report_mod($store,$year,strval($pre_year));  
            $year                 = array();           	
            $month_name           = array(
              		                      array('month'=>'January','number'=>1),
              		                      array('month'=>'February','number'=>2),
              		                      array('month'=>'March','number'=>3),
              		                      array('month'=>'April','number'=>4),
              		                      array('month'=>'May','number'=>5),
              		                      array('month'=>'June','number'=>6),
              		                      array('month'=>'July','number'=>7),
              		                      array('month'=>'August','number'=>8),
              		                      array('month'=>'September','number'=>9),
              		                      array('month'=>'October','number'=>10),
              		                      array('month'=>'November','number'=>11),
              		                      array('month'=>'December','number'=>12)
              	                        );
             $item_div           = array();
             $details            = array();
             $month              = array();
             $total_             = array();



           	foreach($get_monthly as $det)
           	{
       		  if(!in_array($det['year'],$year))
       		  {
       		  	  array_push($year,$det['year']);
       		  }

       		  if(!in_array($det['month_name'],$month))
       		  {
       		   array_push($month,$det['month_name']);
       		  		
       		  }

       		  if(!in_array($det['item_division'],$item_div))
       		  {
       		  	array_push($item_div,$det['item_division']);           		  	
       		  }	

       		  if(!in_array($det['total'], $total_))
       		  {
       		  	array_push($total_, $det['total']);
       		  }

                  array_push($details,array('item_division'=>$det['item_division'],'total'=>$det['total'],'year'=>$det['year'],'month'=>$det['month'],'month_name'=>$det['month_name'],'total_quantity'=>$det['total_quantity'],'store'=>$det['store'],'store_no'=>$det['store_no']));

           	}

	            $store_year       = array();
	            $store_month_name = array();
	            $store_name       = array();
	            $details_store    = array();

	          foreach($get_report_store as $per_store)
	          {
	              if(!in_array($per_store['year'], $store_year))
	              {
	              	array_push($store_year, $per_store['year']);
	              }

	              if(!in_array($per_store['month_name'], $store_month_name))
	              {
	              	array_push($store_month_name, $per_store['month_name']);
	              }

                   array_push($details_store,array('total'=>$per_store['total'],'year'=>$per_store['year'],'month'=>$per_store['month'],'month_name'=>$per_store['month_name'],'total_quantity'=>$per_store['total_quantity'],'store'=>$per_store['store']));
              }

            // View Sales in report_type condition..............................
	       	if($report_type == 'sales')
	       	{
                        		// division type view sales.................................
				                if($division_type == 'division')
	           	                {

	           	                			 // this if condition display all stores with division sales...................................
			           	                     if($store == 'Select_all_store')
			           	                     {
			           	                     	$index = 0;
												$divisions = array_unique(array_column($details, 'item_division'));
												$stores    = array_unique(array_column($details, 'store'));
												
												$over_all_final_total_arr = array();
												$over_all_final_total_arr_ADS = array();
												sort($year);
												foreach ($month_name as $month) 
												{
													 foreach ($year as $y) 
													 {
														 array_push($over_all_final_total_arr,0);
														 array_push($over_all_final_total_arr_ADS,0);
													 }

												}


												// this foreach display all stores with division........................
												foreach ($stores as $store)
												{

														 $final_total_arr       =  array();
														 $row_total             = 0;

														 $final_total_sales_ADS = array();
														 $final_total_qty_ADS   = array();

													     
														 foreach ($month_name as $month) 
														 {
													       foreach ($year as $y) {
													        $row_total +=1;
													        array_push($final_total_arr,0);
													        array_push($final_total_sales_ADS, 0);
													        
													       }
														 }

														$final_final_total_yearly_sales_quantity = array();
														foreach($year as $y)
														{
														  array_push($final_final_total_yearly_sales_quantity,0);	
														}

													    header("content-type: application/vnd.ms-excel");
									                    header("Content-Disposition: attachment; filename= Sale Montly and Yearly Report.xls");
													    $tbl = '<table class="table table-bordered table-responsive" id="view_table_sales_'.$index.'" style="background-color: white; width: 100%;color: #0f0b0b; width: auto; table-layout: auto;">';
													    $tbl .= '<thead style="text-align: center;color:white; background-color: rgb(0, 68, 100)">';

													    // Generate table headers.............................
													 
													    sort($year);
													
													    $tbl .= '<tr>';
													    $tbl .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; width: 224px;">----DIVISION_NAME----</th>';
														foreach ($month_name as $month) {
													        $tbl .= "<th style='text-align: center; background-color: #033e5b; color: white;' colspan=".count($year).">".$month['month']."</th>";
													    }

													    $tbl .= "<th style='text-align: center; background-color: #033e5b; color: white;' colspan=".count($year).">Total Per Division</th>";
													    $tbl .= '</tr>';
													    // 
													    $tbl .= '<tr>';
													    foreach ($month_name as $month) {
										          	        for ($a = 0; $a < count($year); $a++) {
										          	            $tbl .= '<th>'.$year[$a].'</th>';
										          	        }
										          	    }



										          	    for ($a = 0; $a < count($year); $a++) {
										          	        $tbl .= '<th>'.$year[$a].'</th>';
										          	    }
										          	    
													    $tbl .= '</tr>';
													    $tbl .= '</thead>';

												        $store_code_       = '-S0015';
												        $storeNoToExclude_ = $store.$store_code_;

											        	// get total SOD MALL per store...................................
												         $storeDivisionDetails_SOD_MALL = array_filter($details, function ($detail) use ($store, $storeNoToExclude_) {
												            return $detail['store'] === $store && $detail['store_no'] === $storeNoToExclude_;
												        });

												          $totalsPerMonthAndYear = array();
												          $totalsPerYear_sales = array();

												        $SOD_mall = '';
												        foreach ($storeDivisionDetails_SOD_MALL as $detail) 
														{
														    $monthNumber = $detail['month'];
														    $year_ = $detail['year'];
														    $total = abs($detail['total']);

														    $SOD_mall = $detail['store'];
												
														    // If the entry for this month and year doesn't exist in $totalsPerMonthAndYear, initialize it to 0...........................
														    if (!isset($totalsPerMonthAndYear[$year_][$monthNumber])) {
														        $totalsPerMonthAndYear[$year_][$monthNumber] = 0;
														    }
												
														    // Add the total to the corresponding month and year...........................
														    $totalsPerMonthAndYear[$year_][$monthNumber] += $total;

													       if (!isset($totalsPerYear_sales[$year_])) {
														        $totalsPerYear_sales[$year_] = 0;
														    }

														    $totalsPerYear_sales[$year_] += $total;
															   


														} // end of storeDivisionDetails_SOD_MALL foreach ........................

													    foreach ($divisions as $division) 
													    {
			                                                        $counter = 0;
															        // Find the details for the current store and division...........................
															        $storeDivisionDetails = array_filter($details, function ($detail) use ($store, $division, $storeNoToExclude_) {
															            return $detail['store'] === $store && $detail['item_division'] === $division && $detail['store_no'] !== $storeNoToExclude_;
															        });
			 														
															        if (!empty($storeDivisionDetails)) 
															        {

															            $div_name = $this->Sales_monitoring_mod->get_div_name_mod($division);
															            $tbl .= '<tr>';
															             if(!empty($div_code))
																		   {
																		   	$div_code = $division;
																		   }else{
																		   	     $div_code = 'No Division';
																		        }
															            if (!empty($div_name[0]['div_name'])) {
															                $tbl .= '<td style="position: sticky; left: 0;background-color: white;color: black;">' . $div_name[0]['div_name'] . '</td>';
															              
															            } else {
															                $tbl .= '<td style="position: sticky; left: 0;background-color: white;color: black;">'. $div_code .'</td>';
															                
															            }
															          

															            foreach ($month_name as $month)
															            {
															            	$monthlyTotal = 0.00;
															                foreach ($year as $y) 
															                {
															                    $total = '0.00';
															                    foreach ($storeDivisionDetails as $detail) 
															                    {

															                        if ($detail['month'] == $month['number'] && $detail['year'] == $y) {
															                      

															                            $total_ = abs($detail['total']);
															                            $total = number_format($total_, 2, '.', ',');
															                            break;
															                        }
															                    	
															                    } // end of storeDivisionDetails foreach................

															                    $tot = str_replace(',', '', $total);
															                    $totalSum += (float)$tot;
															                    $tbl .= '<td style="text-align: right;">' . $total . '</td>';
															                    $final_total_arr[$counter] += $tot; 
															                    $over_all_final_total_arr[$counter] += $tot; 
															                    $over_all_final_total_arr_ADS[$counter] += $tot / 30;

															                    $final_total_sales_ADS[$counter] +=  $tot / 30;

																				$counter ++;
															                } // end of $year foreach .................................. 

															            } // end of $month_name foreach...........................

															            $total_per_div = [];
																		$total_per_div_year = [];

																			// Initialize $total_per_div with empty arrays for all years................................
																			foreach ($year as $y) {
																			    $total_per_div[$y] = [];
																			}

																			foreach ($year as $y) 
																			{
																			    foreach ($storeDivisionDetails as $detail) 
																			    {
																			        if ($detail['year'] == $y) 
																			        {
																			            $division = $detail['item_division'];

																			            if (!isset($total_per_div[$y][$division]))
																			            {
																			                $total_per_div[$y][$division] = [
																			                    'total_sales' => 0,
																			                ];
																			            }

																			           
																			            $total_per_div[$y][$division]['total_sales'] += abs($detail['total']);

																			        }
																			    }
																			}


																		    foreach ($year as $y) 
																			{

																			    if (empty($total_per_div[$y])) 
																			    {
																			        // Add zero values for the year without data...............................................................
																			        $tbl .= '<td style="text-align: right;">0.00</td>';
																		
																			    } else {

																				          foreach ($total_per_div[$y] as $division => $totals)
																				          {
																				            $tbl .= '<td style="text-align: right;">' . number_format($totals['total_sales'], 2, '.', ',') . '</td>';
																				            $final_sales = $totals['total_sales'];
																				            break;

																				          }

																						    if (isset($final_final_total_yearly_sales_quantity[$counter])) {
								                                                                $final_final_total_yearly_sales_quantity[$counter] += $final_sales;
								                                                               
								                                                            } else {
								                                                                // If the counter doesn't exist, you are correctly creating an array with 'sales' and 'quantity' keys..
								                                                                $final_final_total_yearly_sales_quantity[$counter] = $final_sales;
								                                                            }
								                                                            
								                                                            // Increment the counter.................
								                                                            $counter++;
	    	
																			           } 

																		    }


															           
															            $tbl .= '</tr>';
	       
													        } // end of !empty($storeDivisionDetails if condition ..................................

													    } // end of $divisions foreach..................................

	                                                      //==================================================================================================================================
														  if($SOD_mall === $store)
														  {

															         $tbl .= "<tr>";
																	 $tbl .= "<td style='position: sticky; left: 0;background-color: white;color: black;'>" . $SOD_mall.'-SOD MALL' . "</td>";
																	 $counter = 0; 			
																	 foreach ($month_name as $month)
																	 {
				                                                              	    foreach ($year as $y) 
				                                                              	    {
				                                                              	        
				                                                              	      $total_sod = isset($totalsPerMonthAndYear[$y][$month['number']]) ? $totalsPerMonthAndYear[$y][$month['number']] : 0;
				                                                                    
				                                                              	        
				                                                              	      $final_total_arr[$counter] +=	$total_sod;
				                                                              	      $total_sod = number_format($total_sod, 2, '.', ',');
				                                                              	       
				                                                              		  $tbl .= "<td style='text-align:right;'>" . $total_sod . "</td>";
				                                                              		  $counter++;

				                                                              	    } // end of year foreach.................................

				                                                    } // end of month_name foreach.................................
		  
			                                                        $totalsPerYear_sales[$year_] += $total;
																
																    //===============================================================================================================
																    // this foreach display total of SOD in the table.................................................................
											                        foreach ($year as $y) 
									                          	    {
									                          	        
									                          	       $total_sod     = isset($totalsPerYear_sales[$y]) ? $totalsPerYear_sales[$y] : 0;
									                                   

									                          		   $tbl .= "<td style='text-align:right;'>" . number_format($total_sod, 2, '.', ',') . "</td>";
									                          		  

									                          		   if (isset($final_final_total_yearly_sales_quantity[$counter])) {
									                                        $final_final_total_yearly_sales_quantity[$counter] += $total_sod;
									                                        
									                                    } else {
									                                        // If the counter doesn't exist, you are correctly creating an array with 'sales' and 'quantity' keys.......................
									                                        $final_final_total_yearly_sales_quantity[$counter] = $total_sod;
									                                    }
									                          		
									                          		   $counter++;

									                          	    } // end of year foreach......................................

		        
				                  

			                                                   $tbl .= "</tr>";
													
														  } // $SOD_mall === $store view if condition.............................................

	                                                    //==================================================================================================================================
													    $store_name = $this->Sales_monitoring_mod->names_store($store);
													    $tbl .='<div class="responsive-div_top" style="margin-top: 1px; margin-bottom: 10px;"><div class="line-separator"></div></div>';
													    $tbl .= '<h3 style="font-size: 26px;">Sales Monthly and Yearly Report => Store Name: ' . $store_name[0]['nav_store_val'] . '</h3>';
													    $tbl .= '
													    			  <tfoot>
																	    <tr style="color: black;">
																	      <th style="position: sticky; left: 0; background: darkcyan;">Total</th>';
																	        
		                                                                    for($a=0;$a<count($final_total_arr);$a++)
		                                                                    {            
		                                                                        
			                                                                    $tbl .= '<th style="color:black; background: darkcyan; text-align: right;">'.number_format($final_total_arr[$a], 2, '.', ',').'</th>';
		                                                                    }


		                                                                    // foreach display grand total of all division per store.............................................................................	
							                                               $filtered_final_total_sales_quantity = array_filter($final_final_total_yearly_sales_quantity, function($value) {return $value !== 0;});

							                                               foreach($filtered_final_total_sales_quantity as $final_Value)
							                                               {
							                                               	 $tbl .= '<th style="color:black; background: darkcyan; text-align: right;">'.number_format($final_Value, 2,'.', ',').'</th>';

							                                               } 
														                			      
														$tbl .= '			    </tr>';

	                                                    //==================================================================================================================================
														$tbl .= '       <tr style="color: black;">
																	      <th style="position: sticky; left: 0; background: darkcyan;">ADS</th>';
																	        
		                                                                    for($a=0;$a<count($final_total_arr);$a++)
		                                                                    {            
		                                                                        
			                                                                    $tbl .= '<th style="color:black; background: darkcyan; text-align: right;">'.number_format($final_total_sales_ADS[$a], 2, '.', ',').'</th>';
		                                                                    }

		                                                                    // foreach display Average Daily Sales of all division per store......................................................................
								                                               $get_ADS_final_sales = 0;
								                                       
								                                               foreach($filtered_final_total_sales_quantity as $final_Value)
								                                               {
								                                               	 $get_ADS_final_sales = $final_Value / 30;
								                                                
								                                       	         $tbl .= '<th style="color:black; background: darkcyan; text-align: right;">'.number_format($get_ADS_final_sales, 2,'.', ',').'</th>';
								                                       	     
								                                               } 
														                			      
														$tbl .= '       </tr>
																</tfoot>	

													            ';

													    $tbl .= '</table>';
													    $tbl .= '<script>';
													    $tbl .= '$(document).ready(function() {';
													    $tbl .= '$("#view_table_sales_'.$index.'").DataTable({scrollX: true,  lengthChange: false,searching: false,info: false});';
													    $tbl .= '});';
													    $tbl .= '</script>';

													    $index++;
													    echo $tbl;

                                                  
									            } // end of foreach all store ....................................................

	                                                    //====================================================================================================================================
	                                                    // view grand total of all store .................................................................................................
										         	    $tbl2 = '<table class="table table-bordered table-responsive" id="view_table_sales_total" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; width: auto; table-layout: auto;">';
													    $tbl2 .= '<thead style="text-align: center;color:white;">';
									

													    $tbl2 .= '<tr>';
													    $tbl2 .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color:  #183e60;; width: 224px;">------------------------------</th>';
														foreach ($month_name as $month) {
													        $tbl2 .= "<th style='text-align: center; background-color: #033e5b; color: white;' colspan=".count($year).">".$month['month']."</th>";
													    }
													    $tbl2 .= '</tr>';
													    // 
													    $tbl2 .= '<tr>';
													    foreach ($month_name as $month) {
										          	        for ($a = 0; $a < count($year); $a++) {
										          	            $tbl2 .= '<th>'.$year[$a].'</th>';
										          	        }
										          	    }
													    $tbl2 .= '</tr>';
													    $tbl2 .= '</thead>';

	                                                    //====================================================================================================================================
													    $tbl2 .= '
													           
													                <tr style="color: black;">
																      <th style="position: sticky; left: 0; background: darkcyan;">Grand Total</th>';
																        
	                                                                    for($a=0;$a<count($over_all_final_total_arr);$a++)
	                                                                    {            
	                                                                        
		                                                                    $tbl2 .= '<th style="color: black; background: darkcyan; text-align: right;">'.number_format($over_all_final_total_arr[$a], 2, '.', ',').'</th>';
	                                                                    } 
													                			      

														$tbl2 .= '</tr>';

	                                                    //====================================================================================================================================
														$tbl2 .= '
													           
													                <tr style="color: black;">
																      <th style="position: sticky; left: 0; background: darkcyan;">ADS</th>';
																        
	                                                                    for($a=0;$a<count($over_all_final_total_arr);$a++)
	                                                                    {            
	                                                                        
		                                                                    $tbl2 .= '<th style="color: black; background: darkcyan; text-align: right;">'.number_format($over_all_final_total_arr_ADS[$a], 2, '.', ',').'</th>';
	                                                                    } 
													                			      

														$tbl2 .= '</tr>';
													    $tbl2 .= '</table>';
													    $tbl2 .= '<script>';
													    $tbl2 .= '$("#view_table_sales_total").DataTable({ scrollX: true,  lengthChange: false,searching: false,info: false});';
													    $tbl2 .= '</script>';

													    echo $tbl2;
									                

							

	           	                     }else{ // else $store == 'Select_all_store' condition ...............................................................................................


                                             //====================================================================================================================================
	           	                             // view division per store ........................................................................................
								             $divisions = array_unique(array_column($details, 'item_division')); // Get unique divisions from the details array...........................
								             $store_no = array_unique(array_column($details, 'store_no')); // Get unique divisions from the details array...........................
								             $total_sod = 0;
								             $grand_total_sod = 0;

								       
								             $store_name = $this->Sales_monitoring_mod->names_store($store);

						                     $final_total_arr_per_store =  array();
											 $row_total = 0;

											 $final_total_sales_ADS = array();

											 foreach ($month_name as $month) 
											 {
										       foreach ($year as $y) {
										        $row_total +=1;
										        array_push($final_total_arr_per_store,0);
										        array_push($final_total_sales_ADS,0);
										        
										       }
											 }

                                             //====================================================================================================================================
				                             $tbl  = '<table  class="table table-bordered table-responsive" id="view_table_sales" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; width: auto; table-layout: auto;">';

				                             if(!empty($store_name[0]['nav_store_val']))
				                             {
		                                      $tbl .='<div class="responsive-div_top" style="margin-top: 1px; margin-bottom: 10px;"><div class="line-separator"></div></div>';
				                              $tbl .= "<h3 style='font-size: 26px;'>Sales Monthly and Yearly Report => Store Name:".$store_name[0]['nav_store_val']."<h3>";
				                             }else{
				                                   $tbl .= "<h3 style='font-size: 26px;'>Sales Monthly and Yearly Report => Store Name:<h3>";
				                                  }
				                             $tbl .= '<thead style="text-align: center;color:white;">';
				                             $tbl .= "<tr>";
				                             $tbl .= "<th hidden colspan='36' style='font-size: 18px;background: white;color: white;'>Sales Monthly and Yearly Report => Store Name:".$store_name[0]['nav_store_val']."<th hidden>";
											 $tbl .= "</tr>";
								            
								             // Generate table headers..............................
								                        
							          	     sort($year);
									         $tbl .= "<tr>";  	    
							          	     $tbl .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; width: 224px;">----DIVISION_NAME----</th>';
											 foreach ($month_name as $month) {
											    $tbl .= "<th style='text-align: center; background-color: #033e5b; color: white;' colspan=".count($year).">".$month['month']."</th>";
											 } 
											 $tbl .= '</tr>';
											 // 
											 $tbl .= '<tr>';
											 foreach ($month_name as $month) {
											        for ($a = 0; $a < count($year); $a++) {
											            $tbl .= '<th>'.$year[$a].'</th>';
											        }
											    }
											 $tbl .= '</tr>';
							          		    
							          	     $tbl .= '</thead>';
							          	    			

                    	          	    			    $store_code = '-S0015';
                    					          	    $storeNoToExclude = $store.$store_code;
                    
                    									// Filter details based on store_no....................................................
                    									$filteredDetails = array_filter($details, function ($detail) use ($storeNoToExclude) {
                    									    return $detail['store_no'] !== $storeNoToExclude;
                    									});

                    									$filteredDetails_SOD_MALL = array_filter($details, function ($detail) use ($storeNoToExclude) {
                    									    return $detail['store_no'] === $storeNoToExclude;
                    									});

                                                        //===================================================================================================================
							          	    			// Get all SOD Mall..................................................................................................
														$totalsPerMonthAndYear = array();
														// Loop through $filteredDetails_SOD_MALL to calculate totals per month and year .....................................

														$SOD_mall = '';
														foreach ($filteredDetails_SOD_MALL as $detail) 
														{
														    $monthNumber = $detail['month'];
														    $year_ = $detail['year'];
														    $total = abs($detail['total']);
														    $SOD_mall = $detail['store'];
												
														    // If the entry for this month and year doesn't exist in $totalsPerMonthAndYear, initialize it to 0 ..............
														    if (!isset($totalsPerMonthAndYear[$year_][$monthNumber])) {
														        $totalsPerMonthAndYear[$year_][$monthNumber] = 0;
														    }
												
														    // Add the total to the corresponding month and year ........................
														    $totalsPerMonthAndYear[$year_][$monthNumber] += $total;
														}
							          	    			// end of Get all SOD Mall............................................................................................


                                                        //===================================================================================================================
														// Get all unique divisions after excluding sample ASC-S0015............................................
													    $divisionsToDisplay = array_unique(array_column($filteredDetails, 'item_division'));

													    // Disaply all division per store.................................
														foreach ($divisionsToDisplay as $division) 
														{
															    $counter = 0;

															    // Find the details for the current division (excluding ASC-S0015) ............................................
															    $divisionDetails = array_filter($filteredDetails, function ($detail) use ($division) {
															        return $detail['item_division'] === $division;
															    });

                                                             //================================================================================================================
															 if (!empty($divisionDetails))
															 {
															        $div_name = $this->Sales_monitoring_mod->get_div_name_mod($division);

															            // ... (code for handling $div_code and $div_name) ...............................................
															            if (!empty($divisionDetails))
													                    {
													                       $div_name = $this->Sales_monitoring_mod->get_div_name_mod($division);
													                       $tbl .= "<tr>";
				                                                        
				                                                        
													                       if(!empty($div_code))
													                       {
													                       	$div_code = $division;
													                       }else{
													                       	     $div_code = 'No Division';
													                            }
				                                                        	    
													                       if(!empty($div_name[0]['div_name']))
													                       {
													                        $tbl .= "<td style='position: sticky; left: 0;background-color: white;color: black;'>" . $div_name[0]['div_name'] . "</td>";
													                       }else{  
													                             $tbl .= "<td style='position: sticky; left: 0;background-color: white;color: black;'>" . $div_code . "</td>";
													                            }

															        foreach ($month_name as $month) 
															        {
															            foreach ($year as $y)
															            {

															                $total = '0.00';
															                foreach ($divisionDetails as $detail) 
															                {
															                    if ($detail['month'] == $month['number'] && $detail['year'] == $y)
															                    {
															                     $total = abs($detail['total']);
															                     $total = number_format($total, 2, '.', ',');
															                     break;
															                    }
															                }

															                 // ... (code for handling $totalSum, $final_total_arr_per_store, etc.) ......................
															                 $tot   = str_replace(',', '', $total);
													                         $totalSum += (float)$tot;
													                         $final_total_arr_per_store[$counter] += $tot;
													                         $final_total_sales_ADS[$counter] += $tot / 30;

															                 $tbl .= "<td style='text-align:right;'>" . $total . "</td>";
															                 $counter++;

															            } // end of year foreach ...................

															        } // end of month_name foreach ...................
															        	
															        $tbl .= '</tr>';
															    }

															} // if not empty condition divisionDetails ................... 

														} // end of division display foreach ...................

                                             			//==============================================================================================================================
														if($SOD_mall === $store)
														{
															 $tbl .= "<tr>";
															 $tbl .= "<td style='position: sticky; left: 0;background-color: white;color: black;'>" . $SOD_mall.'-SOD MALL' . "</td>";
															 $counter = 0; 			
															 foreach ($month_name as $month)
															 {
	                                                                  	    foreach ($year as $y) 
	                                                                  	    {
	                                                                  	        
	                                                                  	      $total_sod = isset($totalsPerMonthAndYear[$y][$month['number']]) ? $totalsPerMonthAndYear[$y][$month['number']] : 0;
	                                                                        
	                                                                  	        
	                                                                  	      $final_total_arr_per_store[$counter] +=	$total_sod;
	                                                                  	      $total_sod = number_format($total_sod, 2, '.', ',');
	                                                                  	       
	                                                                  		  $tbl .= "<td style='text-align:right;'>" . $total_sod . "</td>";
	                                                                  		  $counter++;

	                                                                  	    } // end of year foreach ...................

	                                                        } // end of month_name foreach ...................

	                                                        $tbl .= "</tr>";
														}
	                                             			//============================================================================================================================
											                $tbl .= '
												    			      <tfoot>
																	         <tr style="color: white;">
																	             <th style="position: sticky; left: 0; background: darkcyan;">Total</th>';
																	        
									                                              for($a=0;$a<count($final_total_arr_per_store);$a++)
									                                              {            
									                                                    
									                                               $tbl .= '<th style="color:white; background: darkcyan; text-align:right;">'.number_format($final_total_arr_per_store[$a], 2, '.', ',').'</th>';
									                                              } 

							                                           
														                			      
													  	   $tbl .= '         </tr>';

	                                             			//==============================================================================================================================

													  	   $tbl .= '         <tr style="color: white;">
																	             <th style="position: sticky; left: 0; background: darkcyan;">ADS</th>';
																	        
									                                              for($a=0;$a<count($final_total_arr_per_store);$a++)
									                                              {            
									                                                    
									                                               $tbl .= '<th style="color:white; background: darkcyan; text-align:right;">'.number_format($final_total_sales_ADS[$a], 2, '.', ',').'</th>';
									                                              } 

							                                           
														                			      
													  	   $tbl .= '         </tr>
																	</tfoot>	

													            ';

											               $tbl .= '</table>';
											               $tbl .= '<script>';
											               $tbl .= '$(document).ready(function() {';
														   $tbl .= 'console.log("Initializing DataTable...");';
														   $tbl .= '$("#view_table_sales").DataTable({ scrollX: true });';
														   $tbl .= '});';
											               $tbl .='</script>';

											               echo $tbl;

	           	                           } // else end of Select_all_store condition ..................................................

	           	                // View All store with no division Sales .................................
					            }else{ // else division_type condtion ..................................................

                                      
						               // view all store with no division  .............................................................................
					           	       $store_name = array_unique(array_column($details_store, 'store')); // Get unique divisions from the details array
					           	       $divisions = array_unique(array_column($details, 'item_division')); // Get unique divisions from the details array

						          	   sort($store_year);
						               $tbl  = '<table  class="table table-bordered table-responsive" id="payments_table" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; ">';
						               $tbl .= "<h3 >Stores Monthly and Yearly Sales Report<h3>";
	                                   $tbl .= '<thead style="text-align: center;color:white;">';
						            
						               $tbl .= "<tr>";
						               $tbl .= "<th hidden>STORES MONTHLY AND YEARLY SALES REPORT</th>";
						               $tbl .= "</tr>";
						               

						               $get_total_all_store     = array();
						               $get_total_all_store_ADS = array();
						               foreach ($month_name as $month) 
						               	    {
						               	    	foreach($store_year as $year_)
						               	    	{
						          	   	         array_push($get_total_all_store, 0);
						          	   	         array_push($get_total_all_store_ADS, 0);
						               	    	}
						               	    }
						          		    

						          		$tbl .= '<tr>';
										$tbl .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; width: 224px;">Stores</th>';
										foreach ($month_name as $month) {
										    $tbl .= "<th style='text-align: center; background-color: #033e5b; color: white;' colspan=".count($store_year).">".$month['month']."</th>";
										}
										$tbl .= '</tr>';
										// 
										$tbl .= '<tr>';
										foreach ($month_name as $month) {
										        for ($a = 0; $a < count($store_year); $a++) {
										            $tbl .= '<th style="text-align:center;">'.$store_year[$a].'</th>';
										        }
										    }
										$tbl .= '</tr>';

						          	    $tbl .= '</tr>';
						          	    $tbl .= '</thead>';

                                       //==============================================================================================================================
						          	    foreach ($store_name as $store)
						          	             {
						          	              $counter = 0;

					          	            	   $storeDetails = array_filter($details_store, function ($details_d) use ($store) {
								                       return $details_d['store'] === $store;});


								                    if (!empty($storeDetails))
								                    {
								                       $store_names = $this->Sales_monitoring_mod->names_store($store);
								                       
								                       $tbl .= "<tr>";
								                       $tbl .= "<td style='position: sticky; left: 0px; background-color: #0d4262;color: white;'>" . $store . "</td>";

								                       foreach ($month_name as $month)
								                        {
								                           foreach ($store_year as $y)
									                             {
									                               $total = '0.00';
									                               foreach ($storeDetails as $detail) 
									                                 {
									                                   if ($detail['month'] == $month['number'] && $detail['year'] == $y) {
									                                       $total = abs($detail['total']);
									                                       $total = number_format($total, 2, '.', ',');
									                                       break;
									                                   }
									                                 }
									                                       $tot   = str_replace(',', '', $total);

								                                           $totalSum += (float)$tot;
									                                       $tbl .= "<td> ₱" .$total . "</td>";

									                                       $get_total_all_store[$counter] += $tot;
									                                       $get_total_all_store_ADS[$counter] += $tot / 30;
									                                       $counter++;
									                            }  
								                        } // end foreach month name ..................................................

								                       $tbl .= '</tr>';

								                   } // end storeDetails ..................................................

						          	    		 } // end foreach store name ..................................................
						          	   
                                       //==============================================================================================================================
						          	    $tbl .= '
							    			     <tfoot>
													     <tr style="color: white;">
														      <th style="position: sticky; left: 0; background: darkcyan;">Total</th>';
														        
				                                              for($a=0;$a<count($get_total_all_store);$a++)
				                                              {            
				                                                    
				                                               $tbl .= '<th style="color:white; background: darkcyan; text-align:right;">₱'.number_format($get_total_all_store[$a], 2, '.', ',').'</th>';
				                                              } 

			                			      
                                       //==============================================================================================================================
								  	    $tbl .=   '      </tr>

											  	          <tr style="color: white;">
															      <th style="position: sticky; left: 0; background: darkcyan;">ADS</th>';
															        
					                                              for($a=0;$a<count($get_total_all_store);$a++)
					                                              {            
					                                                    
					                                               $tbl .= '<th style="color:white; background: darkcyan; text-align:right;">₱'.number_format($get_total_all_store_ADS[$a], 2, '.', ',').'</th>';
					                                              } 

						                			      
									     $tbl .=         '</tr>
								  	    
												</tfoot>';	
                                       //==============================================================================================================================
								            	
						                $tbl .= '</table>';
								        $tbl .= '<script>';
								        $tbl .= '$("#payments_table").DataTable({ scrollX: true })';
								        $tbl .='</script>';

							            echo $tbl;

						             } // end else division_type condition ..................................................


		    // View quantity in $report_type variable............
		    }else if($report_type == 'quantity'){ // else report_type condtion quantity

		    				// this division_type condition display store with division quantity.................... 
	        	            if($division_type == 'division')
	                        {

						   		//======================================================================================================================================
	                            // function view all store and there total quantity .....................................................................
	                           if($store == 'Select_all_store')
	                           {
                                    $index = 0;
									$divisions = array_unique(array_column($details, 'item_division'));
									$stores = array_unique(array_column($details, 'store'));
                                    
									    $over_all_total_store_monthly_qty =  array();
									    $over_all_total_store_monthly_qty_ADS =  array();
									    $row_total = 0;

									     
										 foreach ($month_name as $month) 
										 {
									       foreach ($year as $y) {
									        $row_total +=1;
									        array_push($over_all_total_store_monthly_qty,0);
									        array_push($over_all_total_store_monthly_qty_ADS,0);
									        
									       }
										 }

						           foreach ($stores as $store) 
							       {

										 $total_all_store_monthly_qty =  array();
										 $row_total = 0;

										 $total_all_store_monthly_qty_ADS = array();

										 foreach ($month_name as $month) 
										 {
									       foreach ($year as $y) 
									       {
									        $row_total +=1;
									        array_push($total_all_store_monthly_qty,0);
									        array_push($total_all_store_monthly_qty_ADS,0);
									        
									       }
										 }

									    //==========================================================================================================================================
									    $tbl = '<table class="table table-bordered table-responsive" id="view_table_sales_'.$index.'" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; width: auto; table-layout: auto;">';

									    $tbl .= '<thead style="text-align: center;color:white;">';

									    // Generate table headers ............................................
									    sort($year);

									    $tbl .= '<tr>';
										$tbl .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; width: 224px;">----DIVISION_NAME----</th>';
										foreach ($month_name as $month) {
										    $tbl .= "<th style='text-align: center; background-color: #033e5b; color: white;' colspan=".count($year).">".$month['month']."</th>";
										}
										$tbl .= '</tr>';
										// 
										$tbl .= '<tr>';
										foreach ($month_name as $month) {
										        for ($a = 0; $a < count($year); $a++) {
										            $tbl .= '<th>'.$year[$a].'</th>';
										        }
										    }
										$tbl .= '</tr>';
									    $tbl .= '</thead>';


									    $store_code_       = '-S0015';
									    $storeNoToExclude_ = $store.$store_code_;
									    // get total SOD MALL per store...............................
								         $storeDivisionDetails_SOD_MALL = array_filter($details, function ($detail) use ($store, $storeNoToExclude_) {
								            return $detail['store'] === $store && $detail['store_no'] === $storeNoToExclude_;
								        });


								        // this foreach display all SOD Mall.........................................
								        $totalsPerMonthAndYear = array();
								        $SOD_mall = '';
								        foreach ($storeDivisionDetails_SOD_MALL as $detail) 
										{
										    $monthNumber = $detail['month'];
										    $year_       = $detail['year'];
										    $total       = abs($detail['total_quantity']);
										    $SOD_mall    = $detail['store'];
								
										    // If the entry for this month and year doesn't exist in $totalsPerMonthAndYear, initialize it to 0 ...............................
										    if (!isset($totalsPerMonthAndYear[$year_][$monthNumber])) {
										        $totalsPerMonthAndYear[$year_][$monthNumber] = 0;
										    }
								
										    // Add the total to the corresponding month and year .......................................
										    $totalsPerMonthAndYear[$year_][$monthNumber] += $total;
										}

									    //======================================================================================================================================
									    foreach ($divisions as $division) 
									    {

									    	$counter = 0;
									        // Find the details for the current store and division ............................................
									        $storeDivisionDetails = array_filter($details, function ($detail) use ($store, $division, $storeNoToExclude_) {
									            return $detail['store'] === $store && $detail['item_division'] === $division && $detail['store_no'] !== $storeNoToExclude_;
									        });

									       //======================================================================================================================================
									        if (!empty($storeDivisionDetails)) 
									        {
									            $div_name = $this->Sales_monitoring_mod->get_div_name_mod($division);
									            $tbl .= '<tr>';
									             if(!empty($div_code))
												   {
												   	$div_code = $division;
												   }else{
												   	     $div_code = 'No Division';
												        }
									            if (!empty($div_name[0]['div_name'])) {
									                $tbl .= '<td style="position: sticky; left: 0;background-color: white;color: black;">' . $div_name[0]['div_name'] . '</td>';
									            } else {
									                $tbl .= '<td style="position: sticky; left: 0;background-color: white;color: black;">'. $div_code .'</td>';
									            }
									          

									            foreach ($month_name as $month)
									                    {
											                foreach ($year as $y)
											                {
											                    $total = '0.00';
											                    foreach ($storeDivisionDetails as $detail) 
											                    {
											                        if ($detail['month'] == $month['number'] && $detail['year'] == $y) 
											                        {
											                            //$total = abs($detail['total_quantity']);
											                            $total = round($detail['total_quantity']);
											                            $total = number_format($total);
											                            break;
											                        }
											                    }
											                    $tot = str_replace(',', '', $total);
											                    $totalSum += (float)$tot;
											                    $tbl .= '<td style="text-align: right;">' . $total . '</td>';

											                    $total_all_store_monthly_qty[$counter] += $tot;
											                    $total_all_store_monthly_qty_ADS[$counter] += $tot / 30;

											                    $over_all_total_store_monthly_qty_ADS[$counter] += $tot / 30;
											                    $over_all_total_store_monthly_qty[$counter] += $tot;

											                    $counter ++;
											                    
											               } // end of $year foreach.................

									                    } // end of $month_name foreach.....................

									            $tbl .= '</tr>';

									        } // end of !empty($storeDivisionDetails if condition...................

									    } // end of $divisions foreach............................

									    //======================================================================================================================================
									    // Display all SOD mall.................................
									     if($SOD_mall === $store)
									     {

										     $tbl .= "<tr>";
											 $tbl .= "<td style='position: sticky; left: 0;background-color: white;color: black;'>" . $store.'-SOD MALL' . "</td>";
											 $counter = 0; 			
											 foreach ($month_name as $month)
											 {
	                                                  	    foreach ($year as $y) 
	                                                  	    {
	                                                  	        
	                                                  	      $total_sod = isset($totalsPerMonthAndYear[$y][$month['number']]) ? $totalsPerMonthAndYear[$y][$month['number']] : 0;
	                                                       
	                                                  	      $total_all_store_monthly_qty[$counter] +=	$total_sod;
	                                                  	      $total_sod = number_format($total_sod);
	                                                  	       
	                                                  		  $tbl .= "<td style='text-align:right;'>" . $total_sod . "</td>";
	                                                  		  $counter++;

	                                                  	    } // end of year foreach ............................................

	                                         } // end of month_name foreach ............................................

									        //======================================================================================================================================
	                                         $tbl .= "</tr>";

									     }

									     $tbl .= '
								    			  <tfoot>
												    <tr style="color: white;">
												      <th style="position: sticky; left: 0; background: darkcyan;">Total</th>';
                                                        for($a=0;$a<count($total_all_store_monthly_qty);$a++)
                                                        {              
                                                          $tbl .= '<th style="color:white; background: darkcyan; text-align: right;">'.number_format($total_all_store_monthly_qty[$a]).'</th>';
                                                        } 
									                			      
										$tbl .= '	</tr>';

									    //======================================================================================================================================
										$tbl .= '   <tr style="color: white;">
												      <th style="position: sticky; left: 0; background: darkcyan;">ADS</th>';
                                                        for($a=0;$a<count($total_all_store_monthly_qty);$a++)
                                                        {             
                                                          $tbl .= '<th style="color:white; background: darkcyan; text-align: right;">'.number_format($total_all_store_monthly_qty_ADS[$a]).'</th>';
                                                        }   			      
										$tbl .= '	</tr>

											      </tfoot>';
									    //======================================================================================================================================

									    $store_name = $this->Sales_monitoring_mod->names_store($store);
									    $tbl .= '<h3 style="font-size: 24px;">Quantity Monthly and Yearly Report => Store Name: ' . $store_name[0]['nav_store_val'] . '</h3>';
									    $tbl .= '</table>';
									    $tbl .= '<script>';
									    $tbl .= '$(document).ready(function() {';
									    $tbl .= 'console.log("Initializing DataTable...");';
									    $tbl .= '$("#view_table_sales_'.$index.'").DataTable({ scrollX: true });';
									    $tbl .= '});';
									    $tbl .= '</script>';

									    echo $tbl;

									    $index++;
									 }


									    //======================================================================================================================================
	                           		    // view all store and there grand total quantity .......................................................................................
									    $tbl2 = '<table class="table table-bordered table-responsive" id="view_table_sales_total" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; width: auto; table-layout: auto;">';
									    $tbl2 .= '<thead style="text-align: center;color:white;">';
							         	sort($year);
							         
									    $tbl2 .= '<tr>';
										$tbl2 .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: #1d3c57;; width: 224px;">-----------------------------</th>';
										foreach ($month_name as $month) {
										    $tbl2 .= "<th style='text-align: center; background-color: #033e5b; color: white;' colspan=".count($year).">".$month['month']."</th>";
										}
										$tbl2 .= '</tr>';
										// 
										$tbl2 .= '<tr>';
										foreach ($month_name as $month) {
										        for ($a = 0; $a < count($year); $a++) {
										            $tbl2 .= '<th>'.$year[$a].'</th>';
										        }
										    }
										$tbl2 .= '</tr>';

									    $tbl2 .= '</thead>';

									    //======================================================================================================================================
									    $tbl2 .= '
									           
									                <tr style="color: white;">
												      <td style="position: sticky; left: 0; background: darkcyan;">Grand Total</td>';
												        
                                                        for($a=0;$a<count($over_all_total_store_monthly_qty);$a++)
                                                        {            
                                                            
                                                            $tbl2 .= '<td style="color: white; background: darkcyan; text-align: right;">'.number_format($over_all_total_store_monthly_qty[$a]).'</td>';
                                                        } 
									                			      

									    $tbl2 .= '</tr>';

									    //======================================================================================================================================
									    $tbl2 .= '
									           
									                <tr style="color: white;">
												      <td style="position: sticky; left: 0; background: darkcyan;">ADS</td>';
												        
                                                        for($a=0;$a<count($over_all_total_store_monthly_qty);$a++)
                                                        {            
                                                            
                                                            $tbl2 .= '<td style="color: white; background: darkcyan; text-align: right;">'.number_format($over_all_total_store_monthly_qty_ADS[$a]).'</td>';
                                                        } 
									                			      

									    $tbl2 .= '</tr>';
									    $tbl2 .= '</table>';
									    $tbl2 .= '<script>';
									    $tbl2 .= '$("#view_table_sales_total").DataTable({ scrollX: true,  lengthChange: false,searching: false,info: false});';
									    $tbl2 .= '</script>';

									    echo $tbl2;



	                           }else{// else View Table Division Per Store total Quantity ................................

									   //========================================================================================================================================
								       $divisions = array_unique(array_column($details, 'item_division')); 
						               $store_name = $this->Sales_monitoring_mod->names_store($store);

						               $final_total_arr_quantity = array();
						               $final_total_arr_quantity_ADS = array();
						               $row_total = 0;

						                     foreach ($month_name as $month) 
											 {
										       foreach ($year as $y) 
										       {
										        $row_total +=1;
										        array_push($final_total_arr_quantity,0);
										        array_push($final_total_arr_quantity_ADS,0); 
										       }
											 }

			                            $tbl  = '<table  class="table table-bordered table-responsive" id="payments_table" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b;">';

			                            if(!empty($store_name[0]['nav_store_val']))
				                             {
		                                      $tbl .='<div class="responsive-div_top" style="margin-top: 1px; margin-bottom: 10px;"><div class="line-separator"></div></div>';
				                              $tbl .= "<h3 style='font-size: 24px;'>Quantity Monthly and Yearly Report => Store Name:".$store_name[0]['nav_store_val']."<h3>";
				                             }else{
				                                   $tbl .= "<h3 style='font-size: 24px;'>Quantity Monthly and Yearly Report => Store Name:<h3>";
				                                  }
			                          
						          	    $tbl .= '<thead style="text-align: center;color:white;">';

						          	    $tbl .= "<tr>";
				                        $tbl .= "<th hidden colspan='36' style='font-size: 18px;background: white;color: white;'>Quantity Monthly and Yearly Report => Store Name:".$store_name[0]['nav_store_val']."<th hidden>";
										$tbl .= "</tr>";

									    //========================================================================================================================================
									    // Generate table headers....................
									    sort($year);

									    $tbl .= '<tr>';
										$tbl .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; width: 224px;">----DIVISION_NAME----</th>';
										foreach ($month_name as $month) {
										    $tbl .= "<th style='text-align: center; background-color: #033e5b; color: white;' colspan=".count($year).">".$month['month']."</th>";
										}
										$tbl .= '</tr>';

									    //========================================================================================================================================
										$tbl .= '<tr>';
										foreach ($month_name as $month)
										        {
										          for ($a = 0; $a < count($year); $a++) 
										          {
										           $tbl .= '<th>'.$year[$a].'</th>';
										          }
										        }
										$tbl .= '</tr>';
									    $tbl .= '</thead>';

									    $store_code = '-S0015';
    					          	    $storeNoToExclude = $store.$store_code;
    
    									// Filter details based on store_no ...................................................................
    									$filteredDetails = array_filter($details, function ($detail) use ($storeNoToExclude) {
    									    return $detail['store_no'] !== $storeNoToExclude;
    									});
    
    									$filteredDetails_SOD_MALL = array_filter($details, function ($detail) use ($storeNoToExclude) {
    									    return $detail['store_no'] === $storeNoToExclude;
    									});

				            
									    //========================================================================================================================================
										$totalsPerMonthAndYear = array();
								
										// Loop through $filteredDetails_SOD_MALL to calculate totals per month and year ...........................................
										foreach ($filteredDetails_SOD_MALL as $detail) 
										{
										    $monthNumber = $detail['month'];
										    $year_ = $detail['year'];
										    $total = abs($detail['total_quantity']);
								
										    // If the entry for this month and year doesn't exist in $totalsPerMonthAndYear, initialize it to 0 ..............................
										    if (!isset($totalsPerMonthAndYear[$year_][$monthNumber])) {
										        $totalsPerMonthAndYear[$year_][$monthNumber] = 0;
										    }
								
										    // Add the total to the corresponding month and year .................................................
										    $totalsPerMonthAndYear[$year_][$monthNumber] += $total;

										} // end of foreach filteredDetails_SOD_MALL .............................................................


									    //========================================================================================================================================
									    // Get all unique divisions after excluding sample ASC-S0015 .........................................................
									    $divisionsToDisplay = array_unique(array_column($filteredDetails, 'item_division'));

										foreach ($divisionsToDisplay as $division) 
										{
											    $counter = 0;

											    // Find the details for the current division (excluding ASC-S0015) ..................................................
											    $divisionDetails = array_filter($filteredDetails, function ($detail) use ($division) {
											        return $detail['item_division'] === $division;
											    });

											if (!empty($divisionDetails))
											{
											        $div_name = $this->Sales_monitoring_mod->get_div_name_mod($division);

											    // ... (code for handling $div_code and $div_name) ...............................................
									            if (!empty($divisionDetails))
							                    {
									                       $div_name = $this->Sales_monitoring_mod->get_div_name_mod($division);
									                       $tbl .= "<tr>";
                                                        
                                                        
									                       if(!empty($div_code))
									                       {
									                       	$div_code = $division;
									                       }else{
									                       	     $div_code = 'No Division';
									                            }
                                                        	    
									                       if(!empty($div_name[0]['div_name']))
									                       {
									                        $tbl .= "<td style='position: sticky; left: 0;background-color: white;color: black;'>" . $div_name[0]['div_name'] . "</td>";
									                       }else{  
									                             $tbl .= "<td style='position: sticky; left: 0;background-color: white;color: black;'>" . $div_code . "</td>";
									                            }

											        foreach ($month_name as $month) 
											        {
											            foreach ($year as $y)
											            {

											                $total = '0';
											                foreach ($divisionDetails as $detail) 
											                {
											                    if ($detail['month'] == $month['number'] && $detail['year'] == $y)
											                    {
											                     $total = abs($detail['total_quantity']);
										                         $total = round($total);
										                         $total = number_format($total);
											                     break;
											                    }
											                }

											                  // ... (code for handling $totalSum, $final_total_arr_per_store, etc.) .........................................
											                  $tot       = str_replace(',', '', $total);
				                                              $totalSum += (float)$tot;
								                              $final_total_arr_quantity[$counter] += $tot;
								                              $final_total_arr_quantity_ADS[$counter] += $tot / 30;
								                              $tbl .= "<td style='text-align:right;'>" .$total . "</td>";
											                  $counter++;

											            } // end of year foreach ...................................

											        } // end of month_name foreach ...................................................
											        	
											        $tbl .= '</tr>';

											    } // end of if condition divisionDetails ...............................................................

											} // if not empty condition divisionDetails ..............................................................................

										} // end of division display foreach .............................................................................................

										 // ==========================================================================================================================================
										 $tbl .= "<tr>";
										 $tbl .= "<td style='position: sticky; left: 0;background-color: white;color: black;'>" . $store.'-SOD MALL' . "</td>";
										 $counter = 0; 			
										 foreach ($month_name as $month)
										 {
                                                  	    foreach ($year as $y) 
                                                  	    {
                                                  	        
                                                  	      $total_sod = isset($totalsPerMonthAndYear[$y][$month['number']]) ? $totalsPerMonthAndYear[$y][$month['number']] : 0;
                                                        
                                                  	      $final_total_arr_quantity[$counter] +=	$total_sod;
                                                  	      $total_sod = number_format($total_sod, 2, '.', ',');
                                                  	       
                                                  		  $tbl .= "<td style='text-align:right;'>" . $total_sod . "</td>";
                                                  		  $counter++;

                                                  	    } // end of year foreach..............................

                                        } // end of month_name foreach.............................

										    // ==========================================================================================================================================
						                	// View Over all total Quantity ...........................................................................................................   
							                $tbl .= '
									    			  <tfoot>
													    <tr style="color: white;">
													      <th style="position: sticky; left: 0; background: darkcyan;">Total</th>';
													        
                                                            for($a=0;$a<count($final_total_arr_quantity);$a++)
                                                            {            
                                                             $tbl .= '<th style="color:white; background: darkcyan; text-align:right;">'.number_format($final_total_arr_quantity[$a]).'</th>';
                                                            } 
											                			      
										   $tbl .= '	</tr>';

										   // ==========================================================================================================================================
										   // View total Average Daily Sales ...........................................................................................................
										   $tbl .= '<tr style="color: white;">
													      <th style="position: sticky; left: 0; background: darkcyan;">ADS</th>';
													        
                                                            for($a=0;$a<count($final_total_arr_quantity);$a++)
                                                            {            
                                                             $tbl .= '<th style="color:white; background: darkcyan; text-align:right;">'.number_format($final_total_arr_quantity_ADS[$a]).'</th>';
                                                            } 
											                			      
										   $tbl .= '	</tr>
													  </tfoot>	

										            ';
							               $tbl .= '</table>';
								           $tbl .= '<script>';
								           $tbl .= '$("#payments_table").DataTable({ scrollX: true })';
								           $tbl .='</script>';
							               echo $tbl;
	                             } 
	                      //===========================================================================================================================================
           	              }else{ // else division_type condition no division .......................................
				           	       $store_name = array_unique(array_column($details_store, 'store')); // Get unique divisions from the details array.....

					               $tbl  = '<table  class="table table-bordered table-responsive" id="payments_table" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b;">';
					               $tbl .= "<h3>Stores Monthly and Yearly Quantity Report</h3>";
	                               $tbl .= '<thead style="text-align: center;color:white;">';

	                               $tbl .= "<tr>";
					               $tbl .= "<th hidden style='position: sticky; left: 0px; background-color: #0d4262;color: white;'>STORES MONTHLY AND YEARLY QUANTITY REPORT</th>";
	                               $tbl .= "</tr>";

					               $tbl .= "<tr>";
					               $tbl .= "<th rowspan='2' style='position: sticky; left: 0px; background-color: #0d4262;color: white;'>Store</th>";

					               foreach ($month_name as $month) 
					               	    {
					          		        $tbl .= "<th colspan=".count($store_year)." style='text-align: center;'>".$month['month']."</th>";
					               	    }
					          		    
					          	    $tbl .= '</tr>';

						            $tbl .= "<tr>";
						          	
				
						          	    foreach ($month_name as $month) 
							          	    {
							          	        for ($a = 0; $a < count($store_year); $a++)
							          	         {
							          	            $tbl .= '<th style="text-align: center";>'.$store_year[$a].'</th>';
							          	         }
							          	    }
							        $tbl .= '</tr>';
					          	    $tbl .= '</thead>';

						          	    sort($store_year);
						          	    
						          	    // Add sorted years as headers..................................................
						          	    foreach ($store_name as $store)
						          	            {
					          	            	   $storeDetails = array_filter($details_store, function ($details_d) use ($store) {
								                       return $details_d['store'] === $store;});


								                    if (!empty($storeDetails))
								                    {

								                       $store_names = $this->Sales_monitoring_mod->names_store($store);
								                      
								                       $tbl .= "<tr>";
								                       $tbl .= "<td style='position: sticky; left: 0px; background-color: #0d4262;color: white;'>" . $store . "</td>";
								                       

								                       foreach ($month_name as $month)
								                       {
								                           foreach ($store_year as $y)
								                            {
								                               $total = '0';
								                               foreach ($storeDetails as $detail)
								                                {
								                                   if ($detail['month'] == $month['number'] && $detail['year'] == $y) {
								                                       $total = abs($detail['total_quantity']);
								                                       $total = round($total);
								                                       $total = number_format($total);
								                                       break;
								                                   }
								                                } // end foreach storeDetails ..............................................

								                                $tot       = str_replace(',', '', $total);
				                                                $totalSum += (float)$tot;
								                                $tbl .= "<td style='text-align:right;'>" .$total . "</td>";
    
								                            } // end foreach store year .............................................. 

								                       } // end foreach month_name ..............................................

								                       $tbl .= '</tr>';

								                    } // end if storeDetails ..............................................

						          	    		 } // end foreach store_name ..............................................
						          	   

			                    $tbl .= '</table>';
					            $tbl .= '<script>';
					            $tbl .= '$("#payments_table").DataTable({scrollX: true})';
					            $tbl .='</script>';
				                echo $tbl;


           	                  } // end of else of division_type condition ..............................................

              
			   }else{ // end of else quantity .......................


	                //===========================================================================================================================================
			   		// this condition view all stores monthly sales and quantity................................................................................
			   	    if($division_type === 'no_division')
			   	    {

		   	    	  // view all store with no division .............................................................................
	           	       $store_name = array_unique(array_column($details_store, 'store')); // Get unique divisions from the details array
	           	       $divisions  = array_unique(array_column($details, 'item_division')); // Get unique divisions from the details array

		          	   sort($store_year);


		          	   $count_col_span = count($store_year)*2;
		               $tbl  = '<table  class="table table-bordered table-responsive" id="payments_table" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; ">';
		               $tbl .= "<h3 >Stores Monthly and Yearly Sales and Quantity Report<h3>";
                       $tbl .= '<thead style="text-align: center;color:white;">';
		            
		               $tbl .= "<tr>";
		               $tbl .= "<th hidden>STORES MONTHLY AND YEARLY SALES AND QUANTITY REPORT</th>";
		               $tbl .= "</tr>";
		               
                       // sales total............................
		               $get_total_all_store     = array();
		               $get_total_all_store_ADS = array();

                       // quantity total........................
		               $get_total_all_store_qty     = array();
		               $get_total_all_store_qty_ADS = array();

		               foreach ($month_name as $month) 
		               	    {
		               	    	foreach($store_year as $year_)
		               	    	{
		               	    	 // total sales ..........................
		          	   	         array_push($get_total_all_store, 0);
		          	   	         array_push($get_total_all_store_ADS, 0);

		               	    	 // total quantity .......................
		               	    	 array_push($get_total_all_store_qty, 0);
		               	    	 array_push($get_total_all_store_qty_ADS, 0);
		               	    	}
		               	    }
		          		    

		          		$tbl .= '<tr>';
						$tbl .= '<th rowspan="3" style="position: sticky; left: 0;background-color: #033e5b;color: white; width: 224px;">Stores</th>';
						foreach ($month_name as $month) {
						    $tbl .= "<th style='text-align: center; background-color: #033e5b; color: white;' colspan=".$count_col_span.">".$month['month']."</th>";
						}
						$tbl .= '</tr>';
						// 
						$tbl .= '<tr>';
						foreach ($month_name as $month) {
						        for ($a = 0; $a < count($store_year); $a++) {
						            $tbl .= '<th colspan="2" style="text-align:center;">'.$store_year[$a].'</th>';
						        }
						    }
						$tbl .= '</tr>';
                  
		          	    $tbl .= '<tr>';
						    foreach ($month_name as $month) {
						        foreach ($store_year as $y) {
						            $tbl .= '<th style="text-align: center; background-color: #0b4568;color: white;">SALES</th>';
						            $tbl .= '<th style="text-align: center; background-color: #0b4568;color: white;">QTY</th>';
						        }
						    }
						$tbl .= '</tr>';
		          	    $tbl .= '</thead>';


		             
	                    //===========================================================================================================================================
		          	    // this foreach display all store...................................
		          	    foreach ($store_name as $store)
		          	            {
		          	              $counter = 0;

	          	            	   $storeDetails = array_filter($details_store, function ($details_d) use ($store) {return $details_d['store'] === $store;});


				                    if (!empty($storeDetails))
				                    {
				                       $store_names = $this->Sales_monitoring_mod->names_store($store);
				                       
				                       $tbl .= "<tr>";
				                       $tbl .= "<td style='position: sticky; left: 0px; background-color: #0d4262;color: white;'>" . $store . "</td>";

				                       foreach ($month_name as $month)
				                        {
				                           foreach ($store_year as $y)
					                             {
					                                 $total = '0.00';
					                                 foreach ($storeDetails as $detail) 
					                                 {
					                                   if ($detail['month'] == $month['number'] && $detail['year'] == $y) {
					                                       $total          = abs($detail['total']);
					                                       $total          = number_format($total, 2, '.', ',');

					                                       $total_quantity = abs($detail['total_quantity']);
					                                       $total_qty = number_format($total_quantity);

					                                       break;
					                                   }
					                                 }
				                                       $tot   = str_replace(',', '', $total);

			                                           $totalSum += (float)$tot;
				                                       $tbl .= "<td style='text-align:right;'> ₱" .$total . "</td>";
				                                       $tbl .= "<td style='text-align:right;'> " .$total_qty . "</td>";
	                                                   

	                                                   // sales total....................................
				                                       $get_total_all_store[$counter] += $tot;
				                                       $get_total_all_store_ADS[$counter] += $tot / 30;
	                                                   
	                                                   // quantity total.................................
				                                       $get_total_all_store_qty[$counter] += $total_quantity;
													   $get_total_all_store_qty_ADS[$counter] += $total_quantity / 30;

				                                       $counter++;
					                            }  
				                        } // end foreach month name ..................................................

				                       $tbl .= '</tr>';

				                   } // end storeDetails ..................................................

		          	    		 } // end foreach store name ..................................................
		          	   
	                    //===========================================================================================================================================
		          	    $tbl .= '
			    			      <tfoot>
								    <tr style="color: white;">
								      <th style="position: sticky; left: 0; background: darkcyan;">Total</th>';
								        
                                      for($a=0;$a<count($get_total_all_store);$a++)
                                      {            
                                            
                                       $tbl .= '<th style="color:white; background: darkcyan; text-align:right;">₱'.number_format($get_total_all_store[$a], 2, '.', ',').'</th>';
                                       $tbl .= '<th style="color:white; background: darkcyan; text-align:right;">'.number_format($get_total_all_store_qty[$a]).'</th>';
                                      } 

            			      
	                    //===========================================================================================================================================
				  	    $tbl .=   '</tr>


				  	          <tr style="color: white;">
							      <th style="position: sticky; left: 0; background: darkcyan;">ADS</th>';
							        
                                  for($a=0;$a<count($get_total_all_store);$a++)
                                  {            
                                        
                                   $tbl .= '<th style="color:white; background: darkcyan; text-align:right;">₱'.number_format($get_total_all_store_ADS[$a], 2, '.', ',').'</th>';
                                   $tbl .= '<th style="color:white; background: darkcyan; text-align:right;">'.number_format($get_total_all_store_qty_ADS[$a]).'</th>';
                                  } 

            			      
	                    //===========================================================================================================================================
				  	    $tbl .=   '</tr>
				  	    
								</tfoot>';	

				            	
		                $tbl .= '</table>';
				        $tbl .= '<script>';
				        $tbl .= '$("#payments_table").DataTable({ scrollX: true })';
				        $tbl .='</script>';

			            echo $tbl;



			   	    }else{ // end if condition view all stores total sales and quantity......................................................................

	                    //===================================================================================================================================
					    // function view all total quantity and sales of all stores ..........................................................
						$total_quantity_final = 0;
						$total_quantity_      = 0;
						$total_quantity       = 0;
						$index                = 0;
						$divisions            = array_unique(array_column($details, 'item_division'));
						$stores               = array_unique(array_column($details, 'store'));
						

						$over_all_final_total_arr = array();
						$over_all_final_total_arr_ADS = array();
						$over_all_final_total_arr_qty = array();
						$over_all_final_total_arr_qty_ADS = array();
						sort($year);
						foreach ($month_name as $month) 
						{
							 foreach ($year as $y) 
							 {
								 array_push($over_all_final_total_arr,0);
								 array_push($over_all_final_total_arr_ADS,0);
								 array_push($over_all_final_total_arr_qty,0);
								 array_push($over_all_final_total_arr_qty_ADS,0);
							 }

						}

 						$count_store = '';

	                    //===================================================================================================================================
						foreach ($stores as $store) 
						{
							 $count_store+=count($store);
							
							 $final_total_arr          =  array();
							 $final_total_arr_ADS      =  array();
							 $final_total_arr_qty      =  array();
							 $final_total_arr_qty_ADS  =  array();
							 $row_total                = 0;

							 foreach ($month_name as $month) 
							 {
						       foreach ($year as $y) 
						       {
						        $row_total +=1;
						        array_push($final_total_arr,0);
						        array_push($final_total_arr_qty,0);
						        array_push($final_total_arr_ADS,0);
						        array_push($final_total_arr_qty_ADS,0); 
						       }
							 }

							 $final_final_total_yearly_sales_quantity = array();
							 foreach($year as $y)
							 {
                               array_push($final_final_total_yearly_sales_quantity, 0);
							 } 


							header("content-type: application/vnd.ms-excel");
		                    header("Content-Disposition: attachment; filename= Sale Montly and Yearly Report.xls");
						    $tbl = '<table class="table table-bordered table-responsive" id="view_table_sales_'.$index.'" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; width: auto; table-layout: auto;">';

						    $tbl .= '<thead style="text-align: center;color:white;">';

						    // Generate table headers
						    $store_name = $this->Sales_monitoring_mod->names_store($store);
						    sort($year);
						  
						    $count_col_span = count($year)*2;
						    $tbl .= '<tr>';
							$tbl .= '<th rowspan="3" style="position: sticky; left: 0;background-color: #033e5b;color: white; width: 224px;">----DIVISION_NAME----</th>';
							foreach ($month_name as $month) {
							    $tbl .= "<th style='text-align: center; background-color: #178395; color: white;' colspan=".$count_col_span.">".$month['month']."</th>";
							    //$tbl .= "<th style='text-align: center; background-color: #033e5b; color: white;' colspan=".count($year).">".$month['month']."</th>";
							}
							  $tbl .= "<th style='text-align: center; background-color: #178395; color: white;' colspan=".$count_col_span.">Total Per Division</th>";

							$tbl .= '</tr>';
							// 
							$tbl .= '<tr>';
							foreach ($month_name as $month) {
							        for ($a = 0; $a < count($year); $a++) {
							            $tbl .= '<th colspan="2" style="text-align: center; background: darkcyan;">'.$year[$a].'</th>';
							        }
							    }

						
	                         //====================================================================================================================================
						    // Header total All Stores Per division...........................................................
					        for ($a = 0; $a < count($year); $a++) {
					            $tbl .= '<th colspan="2" style="text-align: center; background: darkcyan;">'.$year[$a].'</th>';
					        }						    
							$tbl .= '</tr>';
						  
						    $tbl .= '<tr>';
						    foreach ($month_name as $month) {
						        foreach ($year as $y) {
						            $tbl .= '<th style="text-align: center; background-color: #0b4568;color: white;">SALES</th>';
						            $tbl .= '<th style="text-align: center; background-color: #0b4568;color: white;">QTY</th>';
						        }
						    }

	                        //======================================================================================================================================
						    // Header total All Stores Per division...........................................................
						    foreach ($year as $y)
						    {
							 $tbl .= '<th style="text-align: center; background-color: #0b4568;color: white;">SALES</th>';
							 $tbl .= '<th style="text-align: center; background-color: #0b4568;color: white;">QTY</th>';
						    }  
 
						    $tbl .= '</tr>';
						   
						    $tbl .= '</thead>';


						    $store_code_       = '-S0015';
					        $storeNoToExclude_ = $store.$store_code_;

                            //======================================================================================================================================
				        	// get total SOD MALL per store...............................................................................
					         $storeDivisionDetails_SOD_MALL = array_filter($details, function ($detail) use ($store, $storeNoToExclude_) {
					            return $detail['store'] === $store && $detail['store_no'] === $storeNoToExclude_;
					        });

					          $totalsPerMonthAndYear = array();
					          $totalsPerMonthAndYear_qty = array();

					          $totalsPerYear_sales = array();
					          $totalsPerYear_qty = array();
                              	
                            //======================================================================================================================================
  							// this foreach display all SOD Mall..............................
                            $SOD_mall = '';
					        foreach ($storeDivisionDetails_SOD_MALL as $detail) 
							{
							    $monthNumber = $detail['month'];
							    $year_ = $detail['year'];
							    $total = abs($detail['total']);
							    $total_quantity = abs($detail['total_quantity']);

							    $SOD_mall = $detail['store'];
					
							    // If the entry for this month and year doesn't exist in $totalsPerMonthAndYear, initialize it to 0..........................
							    if (!isset($totalsPerMonthAndYear[$year_][$monthNumber])) {
							        $totalsPerMonthAndYear[$year_][$monthNumber] = 0;
							    }


							    if (!isset($totalsPerMonthAndYear_qty[$year_][$monthNumber])) {
							        $totalsPerMonthAndYear_qty[$year_][$monthNumber] = 0;
							    }
					
							    // Add the total to the corresponding month and year..........................
							    $totalsPerMonthAndYear[$year_][$monthNumber] += $total;
							    $totalsPerMonthAndYear_qty[$year_][$monthNumber] += $total_quantity;


							    if (!isset($totalsPerYear_sales[$year_])) {
							        $totalsPerYear_sales[$year_] = 0;
							    }


							    if (!isset($totalsPerYear_qty[$year_])) {
							        $totalsPerYear_qty[$year_] = 0;
							    }

							    $totalsPerYear_sales[$year_] += $total;
							    $totalsPerYear_qty[$year_] += $total_quantity;

							} // end of $storeDivisionDetails_SOD_MALL.........................

                            //======================================================================================================================================
						    foreach ($divisions as $division) 
						    {
                                 $counter = 0;

						        // Find the details for the current store and division
						        $storeDivisionDetails = array_filter($details, function ($detail) use ($store, $division, $storeNoToExclude_) {
						            return $detail['store'] === $store && $detail['item_division'] === $division && $detail['store_no'] !== $storeNoToExclude_;
						        });

						    	 

						        if (!empty($storeDivisionDetails)) {

						            $div_name = $this->Sales_monitoring_mod->get_div_name_mod($division);
						            $tbl .= '<tr style="background: white;">';
						             if(!empty($div_code))
									   {
									   	$div_code = $division;
									   }else{
									   	     $div_code = 'No Division';
									        }
						            if (!empty($div_name[0]['div_name'])) {
						                $tbl .= '<td style="position: sticky; left: 0;background-color: white;color: black;">' . $div_name[0]['div_name'] . '</td>';
						              
						            } else {
						                $tbl .= '<td style="position: sticky; left: 0;background-color: white;color: black;">'. $div_code .'</td>';
						                
						            }

						            foreach ($month_name as $month) 
						            {
				
						                foreach ($year as $y) 
						                {
						                    $total                = '0.00';
						                    $total_               = '0.00';
						                    $tot_qty              = '0';
						                    $total_quantity_      = '0';
						                    $total_quantity_final = '0';

						            

						                    foreach ($storeDivisionDetails as $detail) {

						                        if ($detail['month'] == $month['number'] && $detail['year'] == $y) {

						                        	$total_quantity_ = abs($detail['total_quantity']);
						                        	$total_quantity_final = number_format($total_quantity_);

						                            $total_ = abs($detail['total']);
						                            $total = number_format($total_, 2, '.', ',');

						                            break;
						                        }
						                    }


						                    $tot_qty = $total_quantity_;
						                    $tot = str_replace(',', '', $total);
						                    $totalSum += (float)$tot;
						                    $tbl .= '<td style="text-align: right;">' . $total . '</td>';
						                    $tbl .= '<td style="text-align: right;">' . $total_quantity_final . '</td>';

						                    // get total per stores...................................................
						                    $final_total_arr[$counter] += $tot; 
						                    $final_total_arr_qty[$counter] += $tot_qty; 

						                    // get grand total of all store ..........................................
						                    $over_all_final_total_arr[$counter] += $tot; 
						                    $over_all_final_total_arr_qty[$counter] += $tot_qty; 

											// get Average daily sales ...............................................
	                                  	    $final_total_arr_ADS[$counter] +=	$tot / 30;
	                                  	    $final_total_arr_qty_ADS[$counter] +=	$tot_qty / 30;

											// get Average daily sales grand total ...................................
											$over_all_final_total_arr_ADS[$counter] += $tot / 30;
											$over_all_final_total_arr_qty_ADS[$counter] += $tot_qty / 30;


											$counter ++;
						                } // end of $year foreach......................

						            } // end of $month_name foreach...........................

						            

											$total_per_div = [];
											$total_per_div_year = [];

											// Initialize $total_per_div with empty arrays for all years................................
											foreach ($year as $y) {
											    $total_per_div[$y] = [];
											}

											foreach ($year as $y) 
											{
											    foreach ($storeDivisionDetails as $detail) 
											    {
											        if ($detail['year'] == $y) 
											        {
											            $division = $detail['item_division'];

											            if (!isset($total_per_div[$y][$division]))
											            {
											                $total_per_div[$y][$division] = [
											                    'total_quantity' => 0,
											                    'total_sales' => 0,
											                ];
											            }

											            $total_per_div[$y][$division]['total_quantity'] += abs($detail['total_quantity']);
											            $total_per_div[$y][$division]['total_sales'] += abs($detail['total']);

											        }
											    }
											}


											foreach ($year as $y) 
											{

											    if (empty($total_per_div[$y])) {
											        // Add zero values for the year without data...............................................................
											        $tbl .= '<td style="text-align: right;">0.00</td>';
											        $tbl .= '<td style="text-align: right;">0</td>';
											    } else {

												          foreach ($total_per_div[$y] as $division => $totals)
												          {
												            $tbl .= '<td style="text-align: right;">' . number_format($totals['total_sales'], 2, '.', ',') . '</td>';
												            $tbl .= '<td style="text-align: right;">' . number_format($totals['total_quantity']) . '</td>'; 

												            $final_sales = $totals['total_sales'];
												            $final_qty   = $totals['total_quantity'];
												          }

															if (isset($final_final_total_yearly_sales_quantity[$counter])) {
                                                                $final_final_total_yearly_sales_quantity[$counter]['sales'] += $final_sales;
                                                                $final_final_total_yearly_sales_quantity[$counter]['quantity'] += $final_qty;
                                                            } else {
                                                                // If the counter doesn't exist, you are correctly creating an array with 'sales' and 'quantity' keys.............
                                                                $final_final_total_yearly_sales_quantity[$counter] = ['sales' => $final_sales, 'quantity' => $final_qty];
                                                            }
                                                            
                                                            // Increment the counter.................
                                                            $counter++;
                                                               	
											           } 
										    }

						            $tbl .= '</tr>';
						            
						        }
						    }

						    //====================================================================================================================================================
						    // Display all SOD Mall.......................................................
						    if($SOD_mall === $store)
						    {

							     $tbl .= "<tr style='background: white;'>";
								 $tbl .= "<td style='position: sticky; left: 0;background-color: white;color: black;'>" . $SOD_mall.'-SOD MALL' . "</td>";
								 $counter = 0; 			
								 foreach ($month_name as $month)
								 {
		                                  	    foreach ($year as $y) 
		                                  	    {
		                                  	        
		                                  	      $total_sod = isset($totalsPerMonthAndYear[$y][$month['number']]) ? $totalsPerMonthAndYear[$y][$month['number']] : 0;
		                                  	      $total_sod_qty = isset($totalsPerMonthAndYear_qty[$y][$month['number']]) ? $totalsPerMonthAndYear_qty[$y][$month['number']] : 0;
		                                        
		                                  	        
		                                  	      $final_total_arr[$counter] +=	$total_sod;


		                                  	      $final_total_arr_qty[$counter] +=	$total_sod_qty;
		                                  	      $total_sod_qty = number_format($total_sod_qty);
		                                  	      $total_sod = number_format($total_sod, 2, '.', ',');
		                                  	       
		                                  		  $tbl .= "<td style='text-align:right;'>" . $total_sod . "</td>";
		                                  		  $tbl .= "<td style='text-align:right;'>" . $total_sod_qty . "</td>";
		                                  		  $counter++;

		                                  	    } // end of year foreach.......................

		                        } // end of month_name foreach.......................


		                        $totalsPerYear_sales[$year_] += $total;
							    $totalsPerYear_qty[$year_] += $total_quantity;

							    //===================================================================================================================================================
							    // this foreach display total of SOD in the table.................................................................
		                        foreach ($year as $y) 
                          	    {
                          	        
                          	       $total_sod     = isset($totalsPerYear_sales[$y]) ? $totalsPerYear_sales[$y] : 0;
                                   $total_sod_qty = isset($totalsPerYear_qty[$y]) ? $totalsPerYear_qty[$y] : 0;

                          		   $tbl .= "<td style='text-align:right;'>" . number_format($total_sod, 2, '.', ',') . "</td>";
                          		   $tbl .= "<td style='text-align:right;'>" . number_format($total_sod_qty) . "</td>";

                          		   if (isset($final_final_total_yearly_sales_quantity[$counter])) {
                                        $final_final_total_yearly_sales_quantity[$counter]['sales'] += $total_sod;
                                        $final_final_total_yearly_sales_quantity[$counter]['quantity'] += $total_sod_qty;
                                    } else {
                                        // If the counter doesn't exist, you are correctly creating an array with 'sales' and 'quantity' keys.......................
                                        $final_final_total_yearly_sales_quantity[$counter] = ['sales' => $total_sod, 'quantity' => $total_sod_qty];
                                    }
                          		
                          		   $counter++;

                          	    } // end of year foreach......................................

        
		                        $tbl .= "</tr>";
						 
						    }
                            
						    $tbl .='<div class="responsive-div_top" style="margin-top: 1px; margin-bottom: 10px;"><div class="line-separator"></div></div>';
						    $tbl .= '<h3 style="font-size: 22px;">Sales and Quantity Monthly and Yearly Report => Store Name: ' . $store_name[0]['nav_store_val'] . '</h3>';

						    //===========================================================================================================================================================
						    // td view total Sales and Quantity All Stores.........................................................................................................
						    $tbl .= '
						    		 <tfoot>
										    <tr style="color: black;">
											    <td style="position: sticky; left: 0; background: darkcyan;">TOTAL</td>';    
                                                for($a=0;$a<count($final_total_arr);$a++)
                                                {            
                                                    
                                                    $tbl .= '<td style="color:black; background: darkcyan; text-align: right;">'.number_format($final_total_arr[$a], 2, '.', ',').'</td>';
                                                    $tbl .= '<td style="color:black; background: darkcyan; text-align: right;">'.number_format($final_total_arr_qty[$a]).'</td>';
                                                } 


                                               // foreach display grand total of all division per store.............................................................................	
                                               $filtered_final_total_sales_quantity = array_filter($final_final_total_yearly_sales_quantity, function($value) {return $value !== 0;});
                                               foreach($filtered_final_total_sales_quantity as $final_Value)
                                               {
                                               	      $tbl .= '<td style="color:black; background: darkcyan; text-align: right;">'.number_format($final_Value['sales'], 2,'.', ',').'</td>';
                                                      
                                               	      $tbl .= '<td style="color:black; background: darkcyan; text-align: right;">'.number_format($final_Value['quantity']).'</td>';
                                               }


                                               		      
							$tbl .= '	    </tr>';

						    //===========================================================================================================================================================
							// td view Average Daily Sales and Quantity All Stores...................................................................................................
							$tbl .= '       <tr style="color: black; background:#2686b5;">
											    <td style="position: sticky; left: 0; background: #2686b5;">ADS</td>';    
                                                for($a=0;$a<count($final_total_arr);$a++)
                                                {            
                                                    
                                                    $tbl .= '<td style="color:black; background: #2686b5; text-align: right;">'.number_format($final_total_arr_ADS[$a], 2, '.', ',').'</td>';
                                                    $tbl .= '<td style="color:black; background: #2686b5; text-align: right;">'.number_format($final_total_arr_qty_ADS[$a]).'</td>';
                                                } 


                                               // foreach display Average Daily Sales of all division per store......................................................................
                                               $get_ADS_final_sales = 0;
                                               $get_ADS_final_qty = 0;
                                               foreach($filtered_final_total_sales_quantity as $final_Value)
                                               {
                                               	 $get_ADS_final_sales = $final_Value['sales'] / 30;
                                                 $get_ADS_final_qty = $final_Value['quantity'] / 30;

                                       	         $tbl .= '<td style="color:black; background: rgb(38, 134, 181); text-align: right;">'.number_format($get_ADS_final_sales, 2,'.', ',').'</td>';
                                       	         $tbl .= '<td style="color:black; background: rgb(38, 134, 181); text-align: right;">'.number_format($get_ADS_final_qty).'</td>';
                                               }

                                              
							                			      
							$tbl .= '	    </tr>
									 </tfoot>';

						    $tbl .= '</table>';
						    
						    $tbl .= '<script>';
						    $tbl .=       '$(document).ready(function() {';
						    $tbl .=       'console.log("Initializing DataTable...");';
						    $tbl .=       '$("#view_table_sales_'.$index.'").DataTable({ scrollX: true });';
						    $tbl .=       '});';
						    $tbl .= '</script>';


						    $index++;

						    echo $tbl;
									                

				     } // end of foreach all store sales and quantity..........................

				     //===========================================================================================================================================================
				     if($count_store !== 1)
				     {


				      		// view all grand total sales and quantity of all stores .............................................................................................
				            $tbl2 = '<table class="table table-bordered table-responsive" id="view_table_sales_total" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; width: auto; table-layout: auto;">';
						    $tbl2 .= '<thead style="text-align: center;color:black;">';

						    // Generate table headers......................................

						    $count_col_span = count($year)*2;
						    sort($year);
						 

						    $tbl2 .= '<tr>';
							$tbl2 .= '<th rowspan="3" style="background-color: #033e5b;color: white; width: 224px;">----DIVISION_NAME----</th>';
							foreach ($month_name as $month) {
							    $tbl2 .= "<th style='text-align: center; background-color: #178395;; color: white;' colspan=".$count_col_span.">".$month['month']."</th>";
							}
							$tbl2 .= '</tr>';
							// 
							$tbl2 .= '<tr>';
							foreach ($month_name as $month) {
							        for ($a = 0; $a < count($year); $a++) {
							            $tbl2 .= '<th style="background:darkcyan;" colspan="2" >'.$year[$a].'</th>';
							        }
							    }
							$tbl2 .= '</tr>';
				           //===========================================================================================================================================================

						    foreach ($month_name as $month) {
						        foreach ($year as $y) {
						            $tbl2 .= '<th style="text-align: center; background-color: #0b4568;color: white;">SALES</th>';
						            $tbl2 .= '<th style="text-align: center; background-color: #0b4568;color: white;">QTY</th>';
						        }
						    }
						    $tbl2 .= '</tr>';
						   
						    $tbl2 .= '</thead>';
                            
				           //===========================================================================================================================================================
                            // tr get grand total of all store .........................................................................................................................
						    $tbl2 .= '
						           
						                <tr style="color: black;">
									      <td style="background: darkcyan;">GRAND TOTAL</td>';
									        
                                            for($a=0;$a<count($over_all_final_total_arr);$a++)
                                            {            
                                                
                                                $tbl2 .= '<td style="color: black; background: darkcyan; text-align: right;">'.number_format($over_all_final_total_arr[$a], 2, '.', ',').'</td>';
                                                $tbl2 .= '<td style="color: black; background: darkcyan; text-align: right;">'.number_format($over_all_final_total_arr_qty[$a]).'</td>';
                                            } 
						                			      

							$tbl2 .= '</tr>';

				           //===========================================================================================================================================================
                            // tr get Average Daily Sales  of all store .................................................................................................................
							$tbl2 .= '<tr style="color: black; background:#2686b5;">
									      <td style="background: #2686b5;">ADS</td>';
									        
                                            for($a=0;$a<count($over_all_final_total_arr);$a++)
                                            {            
                                                
                                                $tbl2 .= '<td style="color: black; background: #2686b5; text-align: right;">'.number_format($over_all_final_total_arr_ADS[$a], 2, '.', ',').'</td>';
                                                $tbl2 .= '<td style="color: black; background: #2686b5; text-align: right;">'.number_format($over_all_final_total_arr_qty_ADS[$a]).'</td>';
                                            } 
						                			      

							$tbl2 .= '</tr>';

						    $tbl2 .= '</table>';
						    $tbl2 .= '<script>';
						    $tbl2 .= '$("#view_table_sales_total").DataTable({ scrollX: true,  lengthChange: false,searching: false,info: false});';
						    $tbl2 .= '</script>';

						    echo $tbl2;
				           //===========================================================================================================================================================
				     } // end of $count_store if condition...................................................

				} // end of else select all store......................................................

		    } // end of else view all stores sales and quantity................................. 
 
	    } // end get_yearly_montly_report function........................................


// ======================================================================================================================================================================================
// function get yearly sales report ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
     function view_yearly_report()
     {
     	  $div_code       = '';
          // function get previous year ......................................................
	      $range          = $_POST['range'];
          $store          = $_POST['store'];
          $year           = $_POST['year'];	
          $report_type    = $_POST['report_type'];
          $division       = $_POST['division'];
          $details_yearly = array();
          $totalSum       = '0.00';
          $totalSum_      = '0.00';
          $tbl            = '';
          $tot            = '';

          // get previous 3years range :......................................................
          $original_year  = $year;
	      $sub_year       = 2;
	      $pre_year       = $original_year - $sub_year;
          

          $get_yearly_store     = $this->Sales_monitoring_mod->get_yearly_store_mod(strval($pre_year),$year);

          $get_yearly           = $this->Sales_monitoring_mod->get_yearly_report_mod(strval($pre_year),$year,$store);
          $year_filter          = array();
          $year_store           = array();
          $details_yearly_store = array();

         //===========================================================================================================================================================================
         	foreach($get_yearly as $yearly)
         	{
              if(!in_array($yearly['year'],$year_filter))
              {
              	array_push($year_filter,$yearly['year']);
              }
              array_push($details_yearly,array('item_division'=>$yearly['item_division'],'total'=>$yearly['total'],'year'=>$yearly['year'], 'total_quantity_yearly'=>$yearly['total_quantity_yearly'], 'store'=>$yearly['store'],'store_no'=>$yearly['store_no']));
         	}


         //===========================================================================================================================================================================
         	foreach($get_yearly_store as $store_yearly)
         	{
     		 if(!in_array($store_yearly['year'], $year_store))
     		 {
     		 	 array_push($year_store, $store_yearly['year']);
     		 }

     		  array_push($details_yearly_store,array('store'=>$store_yearly['store'],'total'=>$store_yearly['total'],'year'=>$store_yearly['year'], 'total_quantity_yearly'=>$store_yearly['total_quantity_yearly']));
         	}

         //===========================================================================================================================================================================
         // View sales table.....................................
         if($report_type == 'sales')
         {
                    
	                if($division == 'division')
	                {

	                  if($store == 'Select_all_store')
	                  {
	                   $index       = 0;
  			 	       $divisions   = array_unique(array_column($details_yearly, 'item_division')); // Get unique divisions from the details array
  			 	       $stores      = array_unique(array_column($details_yearly, 'store'));
  			 	      

  			 	       	$over_all_final_total_arr_yearly = array();
  			 	       	$over_all_final_total_arr_yearly_rsort = array();
						foreach ($year_filter as $year_) 
						{
						  array_push($over_all_final_total_arr_yearly,0);
						  array_push($over_all_final_total_arr_yearly_rsort,0);
						}

  			 	       foreach($stores as $store)
  			 	       { 
  			 	         header("content-type: application/vnd.ms-excel");
				         header("Content-Disposition: attachment; filename= Sale Montly and Yearly Report.xls");
  			 	         $final_total_arr_yearly =  array();
						 $row_total = 0;
					     
						
					       foreach ($year_filter as $year_) {
					         $row_total +=1;
					         array_push($final_total_arr_yearly,0);
					        
					       }
						 
		                   $tbl  = '<table  class="table table-bordered table-responsive" id="payments_table_'.$index.'" style="background-color: white; width: 100%;color: #0f0b0b; ">';
		                   $tbl .= '<thead style="color:white; background-color: rgb(12, 66, 98);">';
	                       
		             	   $tbl .= "<tr>";
			               $tbl .= "<th>Division Name</th>";
				               foreach ($year_filter as $year_) 
				               	    {
				          		        $tbl .= "<th style='text-align: right;'>".$year_."</th>";
				               	    }

				               	    $tbl .= "<th style='background-color: #0b4568;color: white; text-align: center;'>PERF</th>";
					               	$tbl .= "<th style='background-color: #0b4568;color: white; text-align: center;'>AVERAGE MONTHLY</th>";
					               	$tbl .= "<th style='background-color: #0b4568;color: white; text-align: center;'>AVERAGE DAILY</th>";

				               	    $tbl .="</tr>";
				               	    $tbl .= '</thead>';

				               	      $storeDetails = array_filter($details_yearly, function ($detail) use ($store) {
									        return $detail['store'] === $store;
									    });

				               	        $store_code_       = '-S0015';
									    $storeNoToExclude_ = $store.$store_code_;

         								//=============================================================================================================================
									    // get total SOD MALL per store of all store sales.................................
								         $storeDivisionDetails_SOD_MALL = array_filter($details_yearly, function ($detail) use ($store, $storeNoToExclude_) {
								            return $detail['store'] === $store && $detail['store_no'] === $storeNoToExclude_;
								        });


								        // this foreach display all SOD Mall.........................
						               	$totalsPerYear = array();
						               	$SOD_mall = '';
						               	foreach ($storeDivisionDetails_SOD_MALL as $detail) 
										{
										    $year_ = $detail['year'];
										    $total = abs($detail['total']);

										    $SOD_mall = $detail['store'];
										    
								
										    // If the entry for this month and year doesn't exist in $totalsPerMonthAndYear, initialize it to 0.................................
										    if (!isset($totalsPerYear[$year_])) {
										        $totalsPerYear[$year_] = 0;
										    }
								
										    // Add the total to the corresponding month and year.................................
										    $totalsPerYear[$year_] += $total;
										}

         								//=============================================================================================================================
						                foreach ($divisions as $division)
						                {
						                    $counter = 0;
						                	$divisionDetails = array_filter($details_yearly, function ($detail) use ($store, $division, $storeNoToExclude_) {
												            return $detail['store'] === $store && $detail['item_division'] === $division && $detail['store_no'] !== $storeNoToExclude_;
												        });

						                   if (!empty($divisionDetails))
						                    {
						                       $div_name = $this->Sales_monitoring_mod->get_div_name_mod($division);
						                       $tbl .= "<tr>";
						                       

						                        if(!empty($div_code))
									                       {
									                       	$div_code = $division;
									                       }else{
									                       	     $div_code = 'No Division';
									                            }

						                       if(!empty($div_name[0]['div_name']))
						                       {
						                        $tbl .= "<td>" . $div_name[0]['div_name'] . "</td>";
						                       }else{
						                       		 $tbl .= "<td>" . $div_code . "</td>";
						                            }


						                            // get PERF : AVERAGE MONTHLY : AVERAGE DAILY ...........................................

						                                $prev_total     = 0;
								                        $divisionTotals = [];
								                        $divisionTotals_pre_years = [];
														$year_filter_   = $year_filter;
														rsort($year_filter_);

														$latest_year    = [];

														foreach ($year_filter_ as $year_) 
														{
														    $get_prev_2_years = $year_ -1;  
														    foreach ($divisionDetails as $detail)
														    {
														    	$divisionName = $detail['item_division'];
                                                                
														         if($detail['year'] == $year)
														         {

														           // get latest year total quantity and sales ...................................
							                                   	  
							                                       $total_sales       = abs($detail['total']);
							                                    
														            
							                                        // Add the totals to the division's running total.....................................
																    if (!isset($latest_year[$divisionName])) {
																        $latest_year[$divisionName] = [
																            'total_sales' => $total_sales,
																        ];
																    }else{
														                  $latest_year[$divisionName]['total_sales'] = $total_sales;
																         }
																	
							                               	
														        
														         }


                                                                // total sales and quantity previous years............................................
														    	if($detail['year'] == $get_prev_2_years)
														    	{
											
							                                   	  
							                                       $total_sales   = abs($detail['total']);
							                                       
														            
							                                        // Add the totals to the division's running total.....................................
																    if (!isset($divisionTotals_pre_years[$divisionName])) {
																        $divisionTotals_pre_years[$divisionName] = [
																            'total_sales' => $total_sales,
																        ];
																    }else{
														                  $divisionTotals_pre_years[$divisionName]['total_sales'] +=$total_sales;
																         }
														    	}
                                                                // end of total sales and quantity previous years.......................................

														        if($detail['year'] == $year_) 
														        {
														          
							                                   	   // get sale total ..............................
							                                       $total         = abs($detail['total']);
							                                       $total_sales   = abs($detail['total']);
							                                       $total         = number_format($total, 2, '.', ',');
														            
							                                        // Add the totals to the division's running total.....................................
																    if (!isset($divisionTotals[$divisionName])) {
																        $divisionTotals[$divisionName] = [
																            'total_sales' => $total_sales,
																        ];
																    }else{
														                  $divisionTotals[$divisionName]['total_sales'] -=$total_sales;
							                                           
																         }
																	
							                               	
														        } // end of $detail['year'] if condition .......................................

														    } // end of divisionDetails foreach condition .................................

														} // end of year_filter_ foreach condition .................................

															 // ===================================================================================
														     // get sales and quantity previous years...................................
															
															 $prev_years_totalSalesAllDivisions = 0;
																
															 foreach ($divisionTotals_pre_years as $divisionName => $totals)
															 {
										
															  $prev_years_totalSalesAllDivisions = $totals['total_sales'];
															 }
														     // end get sales and quantity previous years........................................... 

															 // ===================================================================================
															 // Calculate the overall total for all divisions......................................
											
															 $totalSalesAllDivisions = 0;
																
															 foreach ($divisionTotals as $divisionName => $totals)
															 {
															 
															  if($totals['total_sales'] == $prev_years_totalSalesAllDivisions)
															  {

															   $totalSalesAllDivisions = '-'.$totals['total_sales'];

															  }else{

															        $totalSalesAllDivisions = $totals['total_sales'];

															       }

															 }
															 // ===================================================================================
														     // end get sales and quantity previous years.............................
															 
															 $get_PERF_sales= 0;

											
															 if($prev_years_totalSalesAllDivisions !== 0)
															 {
															  $get_PERF_sales = round($totalSalesAllDivisions / $prev_years_totalSalesAllDivisions * 100, 2);
															 }


															 $latest_sales = 0.00;
															 $daily_sales  = 0.00;
															 foreach($latest_year as $latest_total)
															 {
															 	// get monthly average sales ..............................
															 	$latest_sales    = $latest_total['total_sales']/ 5;
										
															 	// get daily average sales ................................
															 	$daily_sales     = $latest_sales/ 30;
											
															 }

						                            // end of get PERF : AVERAGE MONTHLY : AVERAGE DAILY ....................................
						                     
						                           // =======================================================================================
						                           foreach ($year_filter as $y) {

						                               $total = '0.00';
						                               foreach ($divisionDetails as $detail) {
						                                   if ($detail['year'] == $y) {
						                                       $total = abs($detail['total']);
						                                       $total = number_format($total, 2, '.', ',');
						                                       break;
						                                   }

						                               } // end foreach divisionDetails

						                               $tot       = str_replace(',', '', $total);
							                           $totalSum += (float)$tot;
						                               $tbl .= "<td style='text-align: right;'>" .$total . "</td>";
						                               $final_total_arr_yearly[$counter] += $tot; 
						                               $over_all_final_total_arr_yearly[$counter] += $tot; 
						                               $counter ++;

						                           } // end foreach year_filter..................

						                           // =======================================================================================

						                           $get_rsort = $year_filter;
						                           rsort($get_rsort);
						                           foreach($get_rsort as $rsort_year)
						                           {

						                               $total = '0.00';
						                               foreach ($divisionDetails as $detail) {
						                                   if ($detail['year'] == $rsort_year) {
						                                       $total = abs($detail['total']);
						                                       break;
						                                   }

						                               } // end foreach divisionDetails.................

						                               $tot_rsort = str_replace(',', '', $total);

						                               if (isset($over_all_final_total_arr_yearly_rsort[$counter])) 
						                                {
														    $over_all_final_total_arr_yearly_rsort[$counter] += $tot_rsort;  
													    } else {
														         // Make sure to initialize the index if it doesn't exist.................
														         $over_all_final_total_arr_yearly_rsort[$counter] = $tot_rsort;
														       }

						                               $counter ++;
						                           }

						                            $sub_string = strpos($get_PERF_sales, "-");

						                           // =======================================================================================
					                        	   // View PERF ................................................................................
					                        	   if($sub_string !== false)
					                        	   {
					                                $tbl .= "<td style='text-align: right; color:red;'>" .number_format($get_PERF_sales,2,'.',',') . "%</td>";
					                        	   }else{
					                                     $tbl .= "<td style='text-align: right;color:green;'>" .number_format($get_PERF_sales,2,'.',',') . "%</td>";
					                        	        }
					                             
					                               // View Monthly Average Sales ...............................................................
					                               $tbl .= "<td style='text-align: right;'>" .number_format($latest_sales, 2,'.',',') . "</td>";
					                            
					                               // View Daily Average Sales ..................................................................
					                               $tbl .= "<td style='text-align: right;'>" .number_format($daily_sales, 2,'.',',') . "</td>";
						                             
						                           $tbl .= '</tr>';

						                   } // end if divisionDetails condition........................................

						              } // end foreach divisions........................................

						             //============================================================================================================================================= 	
						             // Display all SOD mall....................................
						             if($SOD_mall === $store)
						             {

							             $tbl .= "<tr>";
										 $tbl .= "<td style='position: sticky; left: 0;background-color: white;color: black;'>" . $SOD_mall.'-SOD MALL' . "</td>";
										 $counter = 0; 			
											
		                              	    foreach ($year_filter as $y) 
		                              	    {
		                              	        
		                              	      $total_sod = isset($totalsPerYear[$y]) ? $totalsPerYear[$y] : 0;
		                              	      $final_total_arr_yearly[$counter] +=	$total_sod;
		                              	      $total_sod = number_format($total_sod, 2, '.', ',');
		                              	       
		                              		  $tbl .= "<td style='text-align:right;'>" . $total_sod . "</td>";
		                              		  $counter++;

		                              	    } // end of year foreach..................................

		                              	// ===========================================================================
		                              	$rsort = $year_filter;
	                              	    rsort($rsort);

	                              	    $total_sod_sale_PERF_val     = 0;
	                              	    $total_sod_all_prev_sales    = 0;	           
   									    $total_sod_latest_year_sales = 0;

   									    $latest_year = max($rsort);
	                              	    foreach ($rsort as $y) 
	                              	    {
                              	         $total_sod = isset($totalsPerYear[$y]) ? $totalsPerYear[$y] : 0;
                              	        
	                              	   	   if ($y !== $latest_year) 
	                              	   	      {
	                              	   	   		$total_sod_all_prev_sales += $total_sod;
		                                        continue; // Skip the latest year..................................
		                                      }	

  										  $total_sod_latest_year_sales = $total_sod; 
  						
	                              	    } // end of year foreach......................................
		                              	// ===========================================================================

	                              	    foreach ($rsort as $y) 
	                              	    {
                              	         $total_sod = isset($totalsPerYear[$y]) ? $totalsPerYear[$y] : 0;
                              	         $total_sod_sale_PERF_val -= $total_sod;
                              	   
	                              	    } // end of year foreach......................................
                                       
		                              	// ===========================================================================
	                                    // get SOD PERF total percentage sales and quantity ....................
	                              	    $total_SOD_PERF_sales = 0;
	                              	    if($total_sod_all_prev_sales !== 0)
	                              	    {
	                              	     $total_SOD_PERF_sales = round($total_sod_sale_PERF_val / $total_sod_all_prev_sales * 100, 2);	
	                              	    }

		                              	// ===========================================================================
	                                    // Get total Average Monthly Sales And Quantity ..................
	                              	    $monthly_total_latest_year_sales = 0;
	                              	    if($total_sod_latest_year_sales !== 0)
	                              	    {
	                              	     $monthly_total_latest_year_sales =  $total_sod_latest_year_sales / 5;
	                              	    }

		                              	// ===========================================================================
	                              	    // Get Daily Average Sales and Quantity .....................
	                              	    $daily_total_latest_year_sales = 0;
	                              	    if($monthly_total_latest_year_sales !== 0)
	                              	    {
	                              	      $daily_total_latest_year_sales = $monthly_total_latest_year_sales / 30;
	                              	    }

		                              	// ===========================================================================
              	    	                // SOD View PERF ..........................................
              	    	                $sub_string = strpos($total_SOD_PERF_sales, "-");
              	    	                if($sub_string !== false)
              	    	                {

		                                 $tbl .= "<td style='text-align: right;color:red;'>" .number_format($total_SOD_PERF_sales,2,'.',',') . "%</td>";

              	    	                }else{

		                                      $tbl .= "<td style='text-align: right;color:green;'>" .number_format($total_SOD_PERF_sales,2,'.',',') . "%</td>";

              	    	                     }
		                                
		                              	// ===========================================================================
		                                // SOD View Monthly Average Sales ...............................
		                                $tbl .= "<td style='text-align: right;'>" .number_format($monthly_total_latest_year_sales,2,'.',',') . "</td>";
		                   
		                                // SOD View Daily Average Sales ....................
		                                $tbl .= "<td style='text-align: right;'>" .number_format($daily_total_latest_year_sales,2,'.',',') . "</td>";
		  
	                                    $tbl .= "</tr>";

						             }

		                              	// ===========================================================================
							            $store_name    = $this->Sales_monitoring_mod->names_store($store);
							            $tbl .='<div class="responsive-div_top" style="margin-top: 1px; margin-bottom: 10px;"><div class="line-separator"></div></div>';
							            $tbl .= "<h3 style='font-size: 23px;'>Sales Yearly Report => Store Name:".$store_name[0]['nav_store_val']."<h3>";
					                    // $tbl .= '
								    	// 		  <tfoot>
										// 		    <tr style="color: white;">
										// 		      <th style="position: sticky; left: 0; background: darkcyan;">Total</th>';
												    
												        
	                                    //                 for($a=0;$a<count($final_total_arr_yearly);$a++)
	                                    //                 {            
	                                                        
	                                    //                     $tbl .= '<th style="background: darkcyan; text-align: right;">'.number_format($final_total_arr_yearly[$a], 2, '.', ',').'</th>';
	                                    //                 } 
									                			      
									    // $tbl .= '	</tr>
										// 		 </tfoot>';

						                $tbl .= '</table>';
						                $tbl .= '<script>';
									    $tbl .= '$("#payments_table_'.$index.'").DataTable({})';
									    $tbl .='</script>';
						                echo $tbl;  
						                $index++;
  
	  			 	            }// end of foreach stores ....................................................................... 
   								  
	  			 	             //===================================================================================================================================================
	  			 	           	 // table view grand total all stores ................................................................................................................
	  			 	             $tbl2  = '<table  class="table table-bordered table-responsive" id="payments_table_yearly" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; ">';
							     $tbl2 .= '<thead style="color:white;">';
				         	     $tbl2 .= "<tr>";
				                 $tbl2 .= "<th style='color: #154351;'>-----------------------------</th>";
					                    foreach ($year_filter as $year_) 
					               	    {
					          		     $tbl2 .= "<th>".$year_."</th>";
					               	    }
					            
					             $tbl2 .="</tr>";
							     $tbl2 .= '</thead>';
							     $tbl2 .= '
							           
							                <tr style="color: white;">';
										    $tbl2 .= ' <td style="position: sticky; left: 0; background: darkcyan;">Grand Total</td>';
										        
                                                for($a=0;$a<count($over_all_final_total_arr_yearly);$a++)
                                                {            
                                                    
                                                    $tbl2 .= '<td style="color: white; background: darkcyan;text-align: right;">'.number_format($over_all_final_total_arr_yearly[$a], 2, '.', ',').'</td>';
                                                } 
							                			      

							    $tbl2 .= '</tr>';
							    $tbl2 .= '</table>';
							    $tbl2 .= '<script>';
							    $tbl2 .= '$("#payments_table_yearly").DataTable({ scrollX: true, lengthChange: false,searching: false,info: false});';
							    $tbl2 .= '</script>';
							    echo $tbl2;

	                  }else{ 

			                   $divisions     = array_unique(array_column($details_yearly, 'item_division')); // Get unique divisions from the details array..............................
		                       $store_name = $this->Sales_monitoring_mod->names_store($store);

		                       $final_total_yearly_sales =  array();
							   $row_total = 0;
						     
						       foreach ($year_filter as $year_) {
						         $row_total +=1;
						         array_push($final_total_yearly_sales,0);
						       }
			                   $tbl  = '<table  class="table table-bordered table-responsive" id="payments_table" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; ">';
			                   $tbl .= '<thead style="color:white;">';
			                   $tbl .= "<h3>Sales Yearly Report => Store Name:".$store_name[0]['nav_store_val']."<h3>";

			             	   $tbl .= "<tr>";
		                       $tbl .= "<th hidden>Sales Yearly Report => Store Name:".$store_name[0]['nav_store_val']."</th hidden>";
			             	   $tbl .= "</tr>";
			             	   $tbl .= "<tr>";
				               $tbl .= "<th>Division Name</th>";
				               foreach ($year_filter as $year_) 
				               	    {
				          		        $tbl .= "<th style='text-align:right;'>".$year_."</th>";
				               	    }

				               	    $tbl .= "<th style='background-color: #0b4568;color: white; text-align: center;'>PERF</th>";
					               	$tbl .= "<th style='background-color: #0b4568;color: white; text-align: center;'>AVERAGE MONTHLY</th>";
					               	$tbl .= "<th style='background-color: #0b4568;color: white; text-align: center;'>AVERAGE DAILY</th>";

				               	    $tbl .="</tr>";
				               	    $tbl .= '</thead>';

				               	    $store_code = '-S0015';
					          	    $storeNoToExclude = $store.$store_code;

									// Filter details based on store_no
									$filteredDetails = array_filter($details_yearly, function ($detail) use ($storeNoToExclude) {
									    return $detail['store_no'] !== $storeNoToExclude;
									});

									$filteredDetails_SOD_MALL = array_filter($details_yearly, function ($detail) use ($storeNoToExclude) {
									    return $detail['store_no'] === $storeNoToExclude;
									});

			            
									$totalsPerYear = array();
									
									//==================================================================================================================
									// Loop through $filteredDetails_SOD_MALL to calculate totals per month and year....................................
									$SOD_mall = '';
									foreach ($filteredDetails_SOD_MALL as $detail) 
									{
									    $year_ = $detail['year'];
									    $total = abs($detail['total']);
										
										$SOD_mall = $detail['store'];
									    // If the entry for this month and year doesn't exist in $totalsPerMonthAndYear, initialize it to 0....................................
									    if (!isset($totalsPerYear[$year_])) {
									        $totalsPerYear[$year_] = 0;
									    }
							
									    // Add the total to the corresponding month and year....................................
									    $totalsPerYear[$year_] += $total;
									}


										// Get all unique divisions after excluding sample ASC-S0015....................................
										$divisionsToDisplay = array_unique(array_column($filteredDetails, 'item_division'));

									//==================================================================================================================
									foreach ($divisionsToDisplay as $division) 
									{
										    $counter = 0;

										    // Find the details for the current division (excluding ASC-S0015)....................................
										    $divisionDetails = array_filter($filteredDetails, function ($detail) use ($division) {
										        return $detail['item_division'] === $division;
										    });

										 if (!empty($divisionDetails))
										 {
										        $div_name = $this->Sales_monitoring_mod->get_div_name_mod($division);

										            // ... (code for handling $div_code and $div_name) .......................................
										            if (!empty($divisionDetails))
								                    {
								                       $div_name = $this->Sales_monitoring_mod->get_div_name_mod($division);
								                       $tbl .= "<tr>";
                                                    
                                                    
								                       if(!empty($div_code))
								                       {
								                       	$div_code = $division;
								                       }else{
								                       	     $div_code = 'No Division';
								                            }
                                                    	    
								                       if(!empty($div_name[0]['div_name']))
								                       {
								                        $tbl .= "<td style='position: sticky; left: 0;background-color: white;color: black;'>" . $div_name[0]['div_name'] . "</td>";
								                       }else{  
								                             $tbl .= "<td style='position: sticky; left: 0;background-color: white;color: black;'>" . $div_code . "</td>";
								                            }


								                      // get PERF : AVERAGE MONTHLY : AVERAGE DAILY ...........................................

						                                $prev_total     = 0;
								                        $divisionTotals = [];
								                        $divisionTotals_pre_years = [];
														$year_filter_   = $year_filter;
														rsort($year_filter_);

														$latest_year    = [];

														foreach ($year_filter_ as $year_) 
														{

														    $get_prev_2_years = $year_ -1;  
 											

														    foreach ($divisionDetails as $detail)
														    {

														    	$divisionName = $detail['item_division'];
                                                                
														         if($detail['year'] == $year)
														         {

														           // get latest year total quantity and sales ...................................
							                                       $total_sales       = abs($detail['total']);
							                                    
														            
							                                        // Add the totals to the division's running total..............................................
																    if (!isset($latest_year[$divisionName])) {
																        $latest_year[$divisionName] = [
																            'total_sales' => $total_sales,
																        ];
																    }else{
														                  $latest_year[$divisionName]['total_sales'] = $total_sales;
																         }

														          }

                                                                // total sales and quantity previous years............................................
														    	if($detail['year'] == $get_prev_2_years)
														    	{
							                                        $total_sales   = abs($detail['total']);
							                                        // Add the totals to the division's running total..............................................
																    if (!isset($divisionTotals_pre_years[$divisionName])) 
																    {
																        $divisionTotals_pre_years[$divisionName] = [
																            'total_sales' => $total_sales,
																        ];
																    }else{
														                  $divisionTotals_pre_years[$divisionName]['total_sales'] +=$total_sales;
																         }
														    	}
                                                                // end of total sales and quantity previous years.......................................

														        if($detail['year'] == $year_) 
														        {
														          
							                                   	   // get sale total ..............................
							                                       $total         = abs($detail['total']);
							                                       $total_sales   = abs($detail['total']);
							                                       $total         = number_format($total, 2, '.', ',');
														            
							                                        // Add the totals to the division's running total..............................................
																    if (!isset($divisionTotals[$divisionName]))
																    {
																        $divisionTotals[$divisionName] = [
																            'total_sales' => $total_sales,
																        ];
																    }else{
														                  $divisionTotals[$divisionName]['total_sales'] -=$total_sales;
							                                           
																         }
																	
														        } // end of $detail['year'] if condition .......................................

														    } // end of divisionDetails foreach condition .................................

														} // end of year_filter_ foreach condition .................................


														     // get sales and quantity previous years............................................. 
															 $prev_years_totalSalesAllDivisions = 0;
															 foreach ($divisionTotals_pre_years as $divisionName => $totals)
															 {
										
															  $prev_years_totalSalesAllDivisions = $totals['total_sales'];
															 }
														     // end get sales and quantity previous years..........................................

															 // :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

															 // Calculate the overall total for all divisions...............................................
															 $totalSalesAllDivisions = 0;
															 foreach ($divisionTotals as $divisionName => $totals)
															 {

																 if($totals['total_sales'] == $prev_years_totalSalesAllDivisions)
																 {

																   $totalSalesAllDivisions = '-'.$totals['total_sales'];

																 }else{

																         $totalSalesAllDivisions = $totals['total_sales'];

																      }
															 }
														     // end get sales and quantity previous years................................................... 
															 
															 $get_PERF_sales= 0;
															 if($prev_years_totalSalesAllDivisions !== 0)
															 {
															  $get_PERF_sales = round($totalSalesAllDivisions / $prev_years_totalSalesAllDivisions * 100, 2);
															 }

															 $latest_sales = 0.00;
															 $daily_sales  = 0.00;
														
															 foreach($latest_year as $latest_total)
															 {

															 	// get monthly average sales ..............................
															 	$latest_sales    = $latest_total['total_sales']/ 5;
										
															 	// get daily average sales ................................
															 	$daily_sales     = $latest_sales/ 30;
															 }

						                            // end of get PERF : AVERAGE MONTHLY : AVERAGE DAILY ..............................
										            foreach ($year_filter as $y)
										            {

										                $total = '0.00';
										                foreach ($divisionDetails as $detail) 
										                {
										                    if ($detail['year'] == $y)
										                    {
										                     $total = abs($detail['total']);
										                     $total = number_format($total, 2, '.', ',');
										                     break;
										                    }
										                }

										                 // ... (code for handling $totalSum, $final_total_arr_per_store, etc.) ................................
										                 $tot   = str_replace(',', '', $total);
								                         $totalSum += (float)$tot;
								                         $final_total_yearly_sales[$counter] += $tot;

										                 $tbl .= "<td style='text-align:right;'>" . $total . "</td>";
										                 $counter++;

										            } // end of year foreach.....................................

								       			 // View PERF ................................................................................
										       $sub_string = strpos($get_PERF_sales, "-");
										       if($sub_string !== false)
										       {

				                               $tbl .= "<td style='text-align: right; color:red;'>" .number_format($get_PERF_sales,2,'.',',') . "%</td>";

										       }else{

				                                      $tbl .= "<td style='text-align: right; color:green;'>" .number_format($get_PERF_sales,2,'.',',') . "%</td>";

										       }
				                             
				                               // View Monthly Average Sales ...............................................................
				                               $tbl .= "<td style='text-align: right;'>" .number_format($latest_sales, 2,'.',',') . "</td>";
				                            
				                               // View Daily Average Sales ..................................................................
				                               $tbl .= "<td style='text-align: right;'>" .number_format($daily_sales, 2,'.',',') . "</td>";	
										       $tbl .= '</tr>';
										    }

										} // if not empty condition divisionDetails..................................... 

									} // end of division display foreach.....................................
                                    
									//==================================================================================================================
                                    // View SOD MALL per Store...........................................................
                                    if($SOD_mall === $store)
                                    {

										 $tbl .= "<tr>";
										 $tbl .= "<td style='position: sticky; left: 0;background-color: white;color: black;'>" . $SOD_mall.'-SOD MALL' . "</td>";
										 $counter = 0; 			
										 
                                  	    foreach ($year_filter as $y) 
                                  	    {
                                  	        
                                  	     $total_sod = isset($totalsPerYear[$y]) ? $totalsPerYear[$y] : 0;
                                           
                                  	     $final_total_yearly_sales[$counter] +=	$total_sod;
                                  	     $total_sod = number_format($total_sod, 2, '.', ',');
                                  	       
                                  		 $tbl .= "<td style='text-align:right;'>" . $total_sod . "</td>";
                                  		 $counter++;

                                  	    } // end of year foreach .........................................

                                  	    //======================================================================
                        			    $rsort 	= $year_filter;
	                              	    rsort($rsort);
	                              	    $total_sod_sale_PERF_val     = 0;
	                              	    $total_sod_all_prev_sales    = 0;
   									    $total_sod_latest_year_sales = 0;

   									    $latest_year = max($rsort);
	                              	    foreach ($rsort as $y) 
	                              	    {
                              	         $total_sod = isset($totalsPerYear[$y]) ? $totalsPerYear[$y] : 0;
                              	        
	                              	   	   if ($y !== $latest_year) {

	                              	   	   		    $total_sod_all_prev_sales += $total_sod;
		                                            continue; // Skip the latest year...........................
		                                        }	

  										  $total_sod_latest_year_sales = $total_sod; 
  						
	                              	    } // end of year foreach...........................................

                                  	    //======================================================================
	                              	    foreach ($rsort as $y) 
	                              	    {
                              	         $total_sod = isset($totalsPerYear[$y]) ? $totalsPerYear[$y] : 0;
                              	       
                              	         $total_sod_sale_PERF_val -= $total_sod;
                              	   

	                              	    } // end of year foreach...........................................
                                       
                                  	    //======================================================================
	                                   // get SOD PERF total percentage sales and quantity ................
	                              	    $total_SOD_PERF_sales = 0;
	                              	    if($total_sod_all_prev_sales !== 0)
	                              	    {
	                              	     $total_SOD_PERF_sales = round($total_sod_sale_PERF_val / $total_sod_all_prev_sales * 100, 2);	
	                              	    }

	                                   // Get total Average Monthly Sales And Quantity ....................
	                              	    $monthly_total_latest_year_sales = 0;
	                              	    if($total_sod_latest_year_sales !== 0)
	                              	    {
	                              	     $monthly_total_latest_year_sales =  $total_sod_latest_year_sales / 5;
	                              	    }

                                  	    //======================================================================
	                              	    // Get Daily Average Sales and Quantity ...........................
	                              	    $daily_total_latest_year_sales = 0;	                          
	                              	    if($monthly_total_latest_year_sales !== 0)
	                              	    {
	                              	      $daily_total_latest_year_sales = $monthly_total_latest_year_sales / 30;
	                              	    }

              	    	                // SOD View PERF ..................................................
              	    	                $sub_string_ = strpos($total_SOD_PERF_sales, "-");
										if($sub_string_ !== false)
										{

		                                 $tbl .= "<td style='text-align: right; color:red;'>" .number_format($total_SOD_PERF_sales,2,'.',',') . "%</td>";

										}else{

		                                     $tbl .= "<td style='text-align: right; color:green;'>" .number_format($total_SOD_PERF_sales,2,'.',',') . "%</td>";

										     }
		                               
		                                // SOD View Monthly Average Sales ...............................................................
		                                $tbl .= "<td style='text-align: right;'>" .number_format($monthly_total_latest_year_sales,2,'.',',') . "</td>";
		                   
 
		                                // SOD View Daily Average Sales ..................................................................
		                                $tbl .= "<td style='text-align: right;'>" .number_format($daily_total_latest_year_sales,2,'.',',') . "</td>";
                                        $tbl .= "</tr>";

                                    } // end of $SOD_mall === $store if condition................................................................................

                                  	   
						            $tbl .= '
							    			  <tfoot>
											    <tr style="color: white;">
											      <th style="position: sticky; left: 0; background: rgb(0, 68, 100);">Total</th>';
    
                                                    for($a=0;$a<count($final_total_yearly_sales);$a++)
                                                    {            
                                                        
                                                        $tbl .= '<th style="background: rgb(0, 68, 100); text-align: right;">'.number_format($final_total_yearly_sales[$a], 2, '.', ',').'</th>';
                                                    } 
								                			      
								    $tbl .= '	</tr>
											 </tfoot>';

					                $tbl .= '</table>';
					                $tbl .= '<script>';
								    $tbl .= '$("#payments_table").DataTable({})';
								    $tbl .='</script>';
					                echo $tbl;
	                        } // end of else select_all_store...........................................


                     }else{  // else display all stores no division....................................

	                	   $store_names = array_unique(array_column($details_yearly_store, 'store')); // Get unique divisions from the details array...................................

                	       $tbl  = '<table  class="table table-bordered table-responsive" id="payments_table" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b;">';
		                   $tbl .= "<h3>Stores Yearly Sales Report<h3>";
	                     
	                       $tbl .= '<thead style="color:white;">';

	                       $tbl .= "<tr>";
			               $tbl .= "<th hidden>Stores Yearly Sales Report</th>";
			               $tbl .= "</tr>";

		             	   $tbl .= "<tr>";
			               $tbl .= "<th>Store Name</th>";

			               		   $get_total_all_store_yearly = array();
			               		   $get_total_all_store_yearly_ADS = array();
					               foreach ($year_store as $year) 
					                  	    {
					             		        $tbl .= "<th style='text-align:right;'>".$year."</th>";
					             		        array_push($get_total_all_store_yearly, 0);
					             		        array_push($get_total_all_store_yearly_ADS, 0);
					                  	    }
			               $tbl .= '</tr>';
		               	   $tbl .= '</thead>';
   
				                 foreach ($store_names as $store_name)
						                 {
						                 	$counter = 0;
				                           $storeDetails = array_filter($details_yearly_store, function ($detail) use ($store_name) {return $detail['store'] === $store_name;});  
				                                   if (!empty($storeDetails))
										              {
								                       $store_names = $this->Sales_monitoring_mod->names_store($store_name);
								                       $tbl .= "<tr>";
								                       $tbl .= "<td>" . $store_names[0]['nav_store_val'] . "</td>";
								                   
								                           foreach ($year_store as $y) 
								                           {
								                                $total = '0.00';
								                               foreach ($storeDetails as $detail) 
								                               {
								                                   if ($detail['year'] == $y) 
								                                   {
								                                     $total = abs($detail['total']);
								                                     $total = number_format($total, 2, '.', ',');
								                                     break;
								                                   }

								                               } // end foreach storeDetails...................................

								                                 $tot   = str_replace(',', '', $total);
									                             $totalSum += (float)$tot;
								                                 $tbl .= "<td style='text-align:right;'>₱" .$total . "</td>";

								                                 $get_total_all_store_yearly[$counter] += $tot;
								                                 $get_total_all_store_yearly_ADS[$counter] += $tot / 30;

								                                 $counter++;


								                           } // end foreach year_store...................................
								                        

								                       $tbl .= '</tr>';

										              } // end if storeDetails condition...................................

						                } // end foreach store_names 

                              $tbl .= '
							    			  <tfoot>
											    <tr style="color: white;">
											      <th style="position: sticky; left: 0; color: white; background: darkcyan;">Total</th>';
    
                                                    for($a=0;$a<count($get_total_all_store_yearly);$a++)
                                                    {            
                                                        
                                                        $tbl .= '<th style="color: white; background: darkcyan; text-align: right;">₱'.number_format($get_total_all_store_yearly[$a], 2, '.', ',').'</th>';
                                                    } 
								                			      
							  $tbl .= '	</tr>';


							  $tbl .= '
							    			
											    <tr style="color: white;">
											      <th style="position: sticky; left: 0;color: white; background: darkcyan;">ADS</th>';
    
                                                    for($a=0;$a<count($get_total_all_store_yearly);$a++)
                                                    {            
                                                        
                                                        $tbl .= '<th style="color: white; background: darkcyan; text-align: right;">₱'.number_format($get_total_all_store_yearly_ADS[$a], 2, '.', ',').'</th>';
                                                    } 
								                			      
							  $tbl .= '	</tr>
									</tfoot>';

                             $tbl .= '</table>';
						     $tbl .= '<script>';
						     $tbl .= '$("#payments_table").DataTable({})';
						     $tbl .='</script>';
                             echo $tbl;

                          } // end of else division condition...................................

          // VIEW QUANTITY :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

          }else if($report_type == 'quantity'){ // else report_type quantity...................................

		       	     if($division == 'division')
		              {

		              	// this condition display all stores with division....................................
		                if($store == 'Select_all_store')
	                    {
			                    $index = 0;
		  			 	        $divisions     = array_unique(array_column($details_yearly, 'item_division')); // Get unique divisions from the detailsarray...................................
		  			 	        $stores        = array_unique(array_column($details_yearly, 'store'));
		  			 	     
		  			 	        $yearly_total_all_store_qty =  array();
						        $row_total = 0;


						       foreach ($year_filter as $y)
						       {
						        $row_total +=1;
						        array_push($yearly_total_all_store_qty,0); 
						       }


							   // foreach view all stores.................................................................................................................................
		  			 	       foreach($stores as $store)
		  			 	       { 	

		  			 	       	   $total_all_store_qty =  array();
								   $row_total = 0;

							       foreach ($year_filter as $y) 
							       {
							        $row_total +=1;
							        array_push($total_all_store_qty,0);
							       }
									 
		  			 	           $store_name    = $this->Sales_monitoring_mod->names_store($store);
				                   $tbl  = '<table  class="table table-bordered table-responsive" id="payments_table_'.$index.'" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; ">';
				                   $tbl .= '<thead style="color:white;">';
				                   $tbl .= "<h3 style='font-size: 23px;'>Quantity Yearly Report => Store Name:".$store_name[0]['nav_store_val']."<h3>";

			                       
				             	   $tbl .= "<tr>";
					               $tbl .= "<th style='background: darkcyan;'>Division Name</th>";
						               foreach ($year_filter as $year_) 
						               	    {
						          		        $tbl .= "<th style='background: darkcyan; text-align: right;'>".$year_."</th>";
						               	    }

						               	    $tbl .= "<th style='background-color: #0b4568;color: white; text-align: center;'>PERF</th>";
							               	$tbl .= "<th style='background-color: #0b4568;color: white; text-align: center;'>AVERAGE MONTHLY</th>";
							               	$tbl .= "<th style='background-color: #0b4568;color: white; text-align: center;'>AVERAGE DAILY</th>";

						               	    $tbl .="</tr>";
						               	    $tbl .= '</thead>';

						               	      $storeDetails = array_filter($details_yearly, function ($detail) use ($store) {
											        return $detail['store'] === $store;
											    });

						               	        $store_code_       = '-S0015';
											    $storeNoToExclude_ = $store.$store_code_;

											    // get total SOD MALL per store...................................
										         $storeDivisionDetails_SOD_MALL = array_filter($details_yearly, function ($detail) use ($store, $storeNoToExclude_) {
										            return $detail['store'] === $store && $detail['store_no'] === $storeNoToExclude_;
										        });

								               	$totalsPerYear_qty = array();
                                                	
								               	//=====================================================================================================
                                                // this foreach Display all SOD Mall quantity.....................
								                $SOD_mall = '';
								               	foreach ($storeDivisionDetails_SOD_MALL as $detail) 
												{
													$SOD_mall = $detail['store'];
												    $year_ = $detail['year'];
												    $total = abs($detail['total_quantity_yearly']);
										
												    // If the entry for this month and year doesn't exist in $totalsPerMonthAndYear, initialize it to 0...................
												    if (!isset($totalsPerYear_qty[$year_])) {
												        $totalsPerYear_qty[$year_] = 0;
												    }
										
												    // Add the total to the corresponding month and year...................................
												    $totalsPerYear_qty[$year_] += $total;
												}

								               	   
								               	// foreach ($divisions as $division) =====================================================================================================
								                foreach ($divisions as $division)
								                {
								                	$counter = 0;
								            
								                	$divisionDetails = array_filter($details_yearly, function ($detail) use ($store, $division, $storeNoToExclude_) {
														            return $detail['store'] === $store && $detail['item_division'] === $division && $detail['store_no'] !== $storeNoToExclude_;
														        });

								                    if (!empty($divisionDetails))
								                    {
								                       $div_name = $this->Sales_monitoring_mod->get_div_name_mod($division);
								                       $tbl .= "<tr>";
								                        if(!empty($div_code))
											                       {
											                       	$div_code = $division;
											                       }else{
											                       	     $div_code = 'No Division';
											                            }

								                       if(!empty($div_name[0]['div_name']))
								                       {
								                        $tbl .= "<td>" . $div_name[0]['div_name'] . "</td>";
								                       }else{
								                       		 $tbl .= "<td>" . $div_code . "</td>";
								                            }

								                             // get PERF : AVERAGE MONTHLY : AVERAGE DAILY ...........................................

								                                $prev_total               = 0;
										                        $divisionTotals           = [];
										                        $divisionTotals_pre_years = [];
																$year_filter_             = $year_filter;
																rsort($year_filter_);

																$latest_year    = [];

																foreach ($year_filter_ as $year_) 
																{

																    $get_prev_2_years = $year_ -1;  
		 											
																    foreach ($divisionDetails as $detail)
																    {
			
																    	$divisionName = $detail['item_division'];
		                                                                
																         if($detail['year'] == $year)
																         {

																           // get latest year total quantity and sales ...................................
									                                       $total_qty       = abs($detail['total_quantity_yearly']);
									                                    
																            
									                                        // Add the totals to the division's running total....................................
																		    if (!isset($latest_year[$divisionName]))
																		    {
																		        $latest_year[$divisionName] = [
																		            'total_qty' => $total_qty,
																		        ];
																		    }else{
																                  $latest_year[$divisionName]['total_qty'] = $total_qty;
																		         }

																         }


		                                                                // total sales and quantity previous years............................................
																    	if($detail['year'] == $get_prev_2_years)
																    	{
									                                       $total_qty   = abs($detail['total_quantity_yearly']);
		   
									                                        // Add the totals to the division's running total....................................
																		    if (!isset($divisionTotals_pre_years[$divisionName])) {
																		        $divisionTotals_pre_years[$divisionName] = [
																		            'total_qty' => $total_qty,
																		        ];
																		    }else{
																                  $divisionTotals_pre_years[$divisionName]['total_qty'] +=$total_qty;
																		         }
																    	}
		                                                                // end of total sales and quantity previous years.......................................

																        if($detail['year'] == $year_) 
																        {
																          
									                                   	   // get sale total ..............................
									                                     
									                                        $total_qty = abs($detail['total_quantity_yearly']);
									                                        // Add the totals to the division's running total...................................
																		    if (!isset($divisionTotals[$divisionName]))
																		    {
																		        $divisionTotals[$divisionName] = [
																		            'total_qty' => $total_qty,
																		        ];
																		    }else{
																                  $divisionTotals[$divisionName]['total_qty'] -=$total_qty;
									                                           
																		         }
																			  	
																         } // end of $detail['year'] if condition ...............................

																    } // end of divisionDetails foreach condition .........................

																} // end of year_filter_ foreach condition .........................


								               	                 //================================================================================
															     // get sales and quantity previous years.................... 			
																 $prev_years_totalSalesAllDivisions = 0;				
																 foreach ($divisionTotals_pre_years as $divisionName => $totals)
																 {
																  $prev_years_totalSalesAllDivisions = $totals['total_qty'];
																 }
															     // end get sales and quantity previous years.................... 

								               	                 //================================================================================
																 // Calculate the overall total for all divisions....................
																 $totalSalesAllDivisions = 0;
																 foreach ($divisionTotals as $divisionName => $totals)
																 {
																  $totalSalesAllDivisions = $totals['total_qty'];
																 }
															     // end get sales and quantity previous years.................... 
																 
																 $get_PERF_qty= 0;
																 if($prev_years_totalSalesAllDivisions !== 0)
																 {
																  $get_PERF_qty = round($totalSalesAllDivisions / $prev_years_totalSalesAllDivisions * 100, 2);
																 }

																 $latest_qty = 0;
																 $daily_qty  = 0;
								               	                 //================================================================================
																 foreach($latest_year as $latest_total)
																 {
																 	// get monthly average sales ..............................
																 	$latest_qty    = $latest_total['total_qty']/ 5;
											
																 	// get daily average sales ................................
																 	$daily_qty     = $latest_qty/ 30;
																 }

										                            // end of get PERF : AVERAGE MONTHLY : AVERAGE DAILY ....................................

										                           foreach ($year_filter as $y) 
										                           {
										                               $total = '0.00';
										                               foreach ($divisionDetails as $detail) 
										                               {
										                                    if ($detail['year'] == $y)
										                                    {
										                                      $total = abs($detail['total_quantity_yearly']);
										                                      $total = number_format($total);
										                                      break;
										                                    }

										                               } // end foreach divisionDetails

										                               $tot       = str_replace(',', '', $total);
											                           $totalSum += (float)$tot;
										                               $tbl .= "<td style='text-align: right;'>" .$total . "</td>";

										                               $total_all_store_qty[$counter] += $tot;
										                               $yearly_total_all_store_qty[$counter] += $tot;
										                               $counter ++;

										                           } // end foreach year_filter 

								               	                   //==========================================================================================
									                               // View PERF ................................................................................
									                               $tbl .= "<td style='text-align: right;'>" .number_format($get_PERF_qty,2,'.',',') . "%</td>";
									                            
								               	                   //==========================================================================================
									                               // View Monthly Average Sales ...............................................................
									                               $tbl .= "<td style='text-align: right;'>" .number_format($latest_qty, 2,'.',',') . "</td>";
									                            
								               	                   //==========================================================================================
									                               // View Daily Average Sales ..................................................................
									                               $tbl .= "<td style='text-align: right;'>" .number_format($daily_qty, 2,'.',',') . "</td>";
									                               $tbl .= '</tr>';

								                    } // end if divisionDetails condition...........................................

								                } //...........................................
								                //  end foreach divisions foreach =====================================================================================================


								             // $SOD_mall === $store ==================================================================================================================
								             if($SOD_mall === $store)
								             {

									             $tbl .= "<tr>";
												 $tbl .= "<td style='position: sticky; left: 0;background-color: white;color: black;'>" . $SOD_mall.'-SOD MALL' . "</td>";
												 $counter = 0; 			
												
				                              	    foreach ($year_filter as $y) 
				                              	    {
				                              	        
				                              	      $total_sod = isset($totalsPerYear_qty[$y]) ? $totalsPerYear_qty[$y] : 0;
				                              	      $total_all_store_qty[$counter] +=	$total_sod;
				                              	      $total_sod = number_format($total_sod);
				                              	       
				                              		  $tbl .= "<td style='text-align:right;'>" . $total_sod . "</td>";
				                              		  $counter++;

				                              	    } // end of year foreach............................................


		                              	    	$rsort = $year_filter;
			                              	    rsort($rsort);
			                              	    $total_sod_qty_PERF_val    = 0;	                           
			                              	    $total_sod_all_prev_qty    = 0;
		   									    $total_sod_latest_year_qty = 0;

								                //==========================================================================================
		   									    $latest_year = max($rsort);
			                              	    foreach ($rsort as $y) 
			                              	    {
		                              	         $total_sod = isset($totalsPerYear_qty[$y]) ? $totalsPerYear_qty[$y] : 0;
		                              	        
			                              	   	   if ($y !== $latest_year) {

			                              	   	   		    $total_sod_all_prev_qty += $total_sod;
				                                            continue; // Skip the latest year............................................
				                                        }	

		  										  $total_sod_latest_year_qty = $total_sod; 
		  						
			                              	    } // end of year foreach............................................

								                //==========================================================================================
			                              	    foreach ($rsort as $y) 
			                              	    {
		                              	         $total_sod = isset($totalsPerYear_qty[$y]) ? $totalsPerYear_qty[$y] : 0;
		                              	       
		                              	         $total_sod_qty_PERF_val -= $total_sod;
		                              	   

			                              	    } // end of year foreach............................................
		                                       
								                //==========================================================================================
			                                   // get SOD PERF total percentage sales and quantity .........................................
			                              	    $total_SOD_PERF_qty = 0;

			                              	    if($total_sod_all_prev_qty !== 0)
			                              	    {
			                              	     $total_SOD_PERF_qty = round($total_sod_qty_PERF_val / $total_sod_all_prev_qty * 100, 2);	
			                              	    }


								                //==========================================================================================
			                                   // Get total Average Monthly Sales And Quantity .............................................
			                              	    $monthly_total_latest_year_qty = 0;

			                              	    if($total_sod_latest_year_qty !== 0)
			                              	    {
			                              	     $monthly_total_latest_year_qty =  $total_sod_latest_year_qty / 5;
			                              	    }

			                              	   
								                //==========================================================================================
			                              	    // Get Daily Average Sales and Quantity ....................................................
			                              	    $daily_total_latest_year_qty = 0;
			                          
			                              	    if($monthly_total_latest_year_qty !== 0)
			                              	    {
			                              	      $daily_total_latest_year_qty = $monthly_total_latest_year_qty / 30;
			                              	    }
		  
								                //==========================================================================================
		              	    	                // SOD View PERF ................................................................................
				                                $tbl .= "<td style='text-align: right;'>" .number_format($total_SOD_PERF_qty,2,'.',',') . "%</td>";
				                                
								                //==========================================================================================
				                                // SOD View Monthly Average Sales ...............................................................
				                                $tbl .= "<td style='text-align: right;'>" .number_format($monthly_total_latest_year_qty,2,'.',',') . "</td>";
				                   
								                //==========================================================================================
				                                // SOD View Daily Average Sales ..................................................................
				                                $tbl .= "<td style='text-align: right;'>" .number_format($daily_total_latest_year_qty,2,'.',',') . "</td>";
		                                    
		                                        $tbl .= "</tr>";

								             } // end of SOD MALL if condition .................................................................................
								               // end of $SOD_mall === $store ===========================================================================================================

							               
								               // $tbl .= '
									    	   // 		  <tfoot>
											   // 		    <tr style="color: white;">
											   // 		      <th style="position: sticky; left: 0; background: darkcyan;">Total</th>';
											  		        
		                                       //                for($a=0;$a<count($total_all_store_qty);$a++)
		                                       //                {            
		                                                          
		                                       //                    $tbl .= '<th style="color:white; background: darkcyan; text-align:   right;">'.number_format($total_all_store_qty[$a]).'</th>';
		                                       //                } 
											  			                			      
											   // $tbl .= '   </tr>
											   // 		</tfoot>';

								             $tbl .= '</table>';
								             $tbl .= '<script>';
											 $tbl .= '$("#payments_table_'.$index.'").DataTable({})';
											 $tbl .='</script>';
								             echo $tbl;  
								             $index++;

				  			 	            } // end of foreach stores................................... 

								            //==================================================================================================
		  			 	            	    // View Grand total All store quantity  .............................................................
	  			 	                        $tbl2  = '<table  class="table table-bordered table-responsive" id="all_stores_quantity_grand_total" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; ">';
									        $tbl2 .= '<thead style="color:white;">';
						         	        $tbl2 .= "<tr>";
						                    $tbl2 .= "<th style='color: darkcyan; background: darkcyan; width:329px;'>Division Name</th>";
							                    foreach ($year_filter as $year_) 
							               	    {
							          		        $tbl2 .= "<th style='text-align: right;'>".$year_."</th>";
							               	    }

							                $tbl2 .="</tr>";
       
									        $tbl2 .= '</thead>';
								            //==================================================================================================
									        $tbl2 .= '
									                <tr style="color: white;">';
												    $tbl2 .= ' <td style="position: sticky; left: 0; background: darkcyan;">Grand Total</td>';												        
		                                                for($a=0;$a<count($yearly_total_all_store_qty);$a++)
		                                                {            
		                                                    
		                                                    $tbl2 .= '<td style="color: white; background: darkcyan; text-align: right;">'.number_format($yearly_total_all_store_qty[$a], 2, '.', ',').'</td>';
		                                                  
		                                                } 
									                			      

									        $tbl2 .= '</tr>';
									        $tbl2 .= '</table>';
									        $tbl2 .= '<script>';
									        $tbl2 .= '$("#all_stores_quantity_grand_total").DataTable({ scrollX: true, lengthChange: false,searching: false,info: false});';
									        $tbl2 .= '</script>';

									        echo $tbl2;

		  			 	         //  end of  View Grand total All store quantity  ............................................................................................

	                     }else{ // $store == 'Select_all_store' ... else view division per stores..........................................................

								  //==============================================================================================================
								  // Get unique divisions from the details array...........................................
				            	  $divisions  = array_unique(array_column($details_yearly, 'item_division'));           
				                  $store_name = $this->Sales_monitoring_mod->names_store($store);

								  $row_total 			  = 0;
				                  $store_yearly_total_qty =  array();

							       foreach ($year_filter as $y)
							       {
							         $row_total +=1;
							         array_push($store_yearly_total_qty,0); 
							       }
			                      $tbl  = '<table class="table table-bordered table-responsive" id="payments_table" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b;">';
			                      $tbl .= '<thead style="color:white;">';
			                      $tbl .= "<h3>Quantity Yearly Report => Store Name:".$store_name[0]['nav_store_val']."</h3>";

				             	  $tbl .= "<tr>";
				             	  $tbl .= "<th hidden>Quantity Yearly Report => Store Name:".$store_name[0]['nav_store_val']."</th hidden>";
				             	  $tbl .= "</tr>";
				             	  $tbl .= "<tr>";
					              $tbl .= "<th>Division Name</th>";
				                  foreach ($year_filter as $year_) 
				               	      {	
				          		        $tbl .= "<th style='text-align:right;'>".$year_."</th>";
				               	      }

				               	  $tbl .= "<th style='background-color: #0b4568;color: white; text-align: center;'>PERF</th>";
					              $tbl .= "<th style='background-color: #0b4568;color: white; text-align: center;'>AVERAGE MONTHLY</th>";
					              $tbl .= "<th style='background-color: #0b4568;color: white; text-align: center;'>AVERAGE DAILY</th>";
				                  $tbl .= '</tr>';
				                  $tbl .= '</thead>';
				               	   
			                        $store_code = '-S0015';
				          	        $storeNoToExclude = $store.$store_code;
    
							    	// Filter details based on store_no
							    	$filteredDetails = array_filter($details_yearly, function ($detail) use ($storeNoToExclude) {
							    	    return $detail['store_no'] !== $storeNoToExclude;
							    	});
    
							    	$filteredDetails_SOD_MALL = array_filter($details_yearly, function ($detail) use ($storeNoToExclude) {
							    	    return $detail['store_no'] === $storeNoToExclude;
							    	});
    
								    //==============================================================================================================
									// Loop through $filteredDetails_SOD_MALL to calculate totals per month and year...............
									$SOD_mall 				= '';
							    	$totalsPerYear_quantity = array();

									foreach ($filteredDetails_SOD_MALL as $detail) 
									{
									    $year_ = $detail['year'];
									    $total = abs($detail['total']);
										$SOD_mall = $detail['store'];
									    // If the entry for this month and year doesn't exist in $totalsPerMonthAndYear, initialize it to 0...............
									    if (!isset($totalsPerYear_quantity[$year_])) {
									        $totalsPerYear_quantity[$year_] = 0;
									    }
							
									    // Add the total to the corresponding month and year...............
									    $totalsPerYear_quantity[$year_] += $total;
									}

								    //==============================================================================================================
									// Get all unique divisions after excluding sample ASC-S0015...................................
								    $divisionsToDisplay = array_unique(array_column($filteredDetails, 'item_division'));
									foreach ($divisionsToDisplay as $division) 
									{
										    $counter = 0;

										    // Find the details for the current division (excluding ASC-S0015)...................................
										    $divisionDetails = array_filter($filteredDetails, function ($detail) use ($division) {
										        return $detail['item_division'] === $division;
										    });

										 if (!empty($divisionDetails))
										 {
										        $div_name = $this->Sales_monitoring_mod->get_div_name_mod($division);

										            // ... (code for handling $div_code and $div_name) ......................................
										            if (!empty($divisionDetails))
								                    {
								                       $div_name = $this->Sales_monitoring_mod->get_div_name_mod($division);
								                       $tbl .= "<tr>";
                                                    
                                                    
								                       if(!empty($div_code))
								                       {
								                       	$div_code = $division;
								                       }else{
								                       	     $div_code = 'No Division';
								                            }
                                                    	    
								                       if(!empty($div_name[0]['div_name']))
								                       {
								                        $tbl .= "<td style='position: sticky; left: 0;background-color: white;color: black;'>" . $div_name[0]['div_name'] . "</td>";
								                       }else{  
								                             $tbl .= "<td style='position: sticky; left: 0;background-color: white;color: black;'>" . $div_code . "</td>";
								                            }



								                        // get PERF : AVERAGE MONTHLY : AVERAGE DAILY ...........................................

						                                $prev_total     = 0;
								                        $divisionTotals = [];
								                        $divisionTotals_pre_years = [];
														$year_filter_   = $year_filter;
														rsort($year_filter_);

														$latest_year    = [];

														foreach ($year_filter_ as $year_) 
														{

														    $get_prev_2_years = $year_ -1;  
 											

														    foreach ($divisionDetails as $detail)
														    {

														    	    $divisionName = $detail['item_division'];
                                                                
															         if($detail['year'] == $year)
															         {

															           // get latest year total quantity and sales ...................................
								                                       $total_qty  = abs($detail['total_quantity_yearly']);

								                                        // Add the totals to the division's running total......................................
																	    if (!isset($latest_year[$divisionName])) {
																	        $latest_year[$divisionName] = [
																	            'total_qty' => $total_qty,
																	        ];
																	    }else{
															                  $latest_year[$divisionName]['total_qty'] = $total_qty;
																	         }

															         }


	                                                                // total sales and quantity previous years............................................
															    	if($detail['year'] == $get_prev_2_years)
															    	{
								                                       $total_qty   = abs($detail['total_quantity_yearly']);
	   
								                                        // Add the totals to the division's running total......................................
																	    if (!isset($divisionTotals_pre_years[$divisionName])) {
																	        $divisionTotals_pre_years[$divisionName] = [
																	            'total_qty' => $total_qty,
																	        ];
																	    }else{
															                  $divisionTotals_pre_years[$divisionName]['total_qty'] +=$total_qty;
																	         }
															    	}
                                                                   // end of total sales and quantity previous years.......................................

														           if($detail['year'] == $year_) 
														           {
														          
								                                   	   // get sale total ..............................
								                                     
								                                       $total_qty   = abs($detail['total_quantity_yearly']);
								                                 
															            
								                                        // Add the totals to the division's running total......................................
																	    if (!isset($divisionTotals[$divisionName])) {
																	        $divisionTotals[$divisionName] = [
																	            'total_qty' => $total_qty,
																	        ];
																	    }else{
															                  $divisionTotals[$divisionName]['total_qty'] -=$total_qty;
								                                           
																	         }
																	
								                               	
															        } // end of $detail['year'] if condition .......................................

															} // end of divisionDetails foreach condition .................................

													    } // end of year_filter_ foreach condition .................................


														     // get sales and quantity previous years........................................ 
															 $prev_years_totalSalesAllDivisions = 0;
															 foreach ($divisionTotals_pre_years as $divisionName => $totals)
															 {
										
															  $prev_years_totalSalesAllDivisions = $totals['total_qty'];
															 }
														     // end get sales and quantity previous years ....................................

															 // =================================================================================
															 // Calculate the overall total for all divisions....................................
															 $totalSalesAllDivisions = 0;
															 foreach ($divisionTotals as $divisionName => $totals)
															 {
															 
															  $totalSalesAllDivisions = $totals['total_qty'];
															 }
														     // end get sales and quantity previous years ....................................
															 $get_PERF_qty= 0;					
															 if($prev_years_totalSalesAllDivisions !== 0)
															 {
															  $get_PERF_qty = round($totalSalesAllDivisions / $prev_years_totalSalesAllDivisions * 100, 2);
															 }

															 // =================================================================================
															 $latest_qty = 0.00;
															 $daily_qty  = 0.00;
														
															 foreach($latest_year as $latest_total)
															 {
															 	// get monthly average sales ..............................
															 	$latest_qty    = $latest_total['total_qty']/ 5;
																
															 	//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::

															 	// get daily average sales ................................
															 	$daily_qty     = $latest_qty/ 30;
											
															 } // end of get PERF : AVERAGE MONTHLY : AVERAGE DAILY ....................................


											            foreach ($year_filter as $y)
											            {

											                $total = '0';
											                foreach ($divisionDetails as $detail) 
											                {
											                    if ($detail['year'] == $y)
											                    {
											                     $total = abs($detail['total_quantity_yearly']);
											                     $total = number_format($total);
											                     break;
											                    }
											                }

											                 // ... (code for handling $totalSum, $final_total_arr_per_store, etc.) .......................................
											                 $tot   = str_replace(',', '', $total);
									                         $totalSum += (float)$tot;
									                         $store_yearly_total_qty[$counter] += $tot;

											                 $tbl .= "<td style='text-align:right;'>" . $total . "</td>";
											                 $counter++;

											            } // end of year foreach.......................................

													   // ==========================================================================================
										               // View PERF ................................................................................
						                               $tbl .= "<td style='text-align: right;'>" .number_format($get_PERF_qty,2,'.',',') . "%</td>";
						                             
													   // ==========================================================================================
						                               // View Monthly Average Sales ...............................................................
						                               $tbl .= "<td style='text-align: right;'>" .number_format($latest_qty, 2,'.',',') . "</td>";
						                            
													   // ==========================================================================================
						                               // View Daily Average Sales ..................................................................
						                               $tbl .= "<td style='text-align: right;'>" .number_format($daily_qty, 2,'.',',') . "</td>";

										        $tbl .= '</tr>';
										    }

										} // if not empty condition divisionDetails .................................

									} // end of division display foreach.................................

									//===============================================================================================================================================
                                     	
                                      // View SOD MALL per Store.....................................................................
                                      if($SOD_mall === $store)
                                      {

							               $tbl .= "<tr>";
							               $tbl .= "<td style='position: sticky; left: 0;background-color: white;color: black;'>" . $store.'-SOD MALL' . "</td>";
							               $counter = 0; 			
							 
                                      	    foreach ($year_filter as $y) 
                                      	    {
                                      	        
                                      	      $total_sod = isset($totalsPerYear_quantity[$y]) ? $totalsPerYear_quantity[$y] : 0;
                                               
                                      	      $store_yearly_total_qty[$counter] +=	$total_sod;
                                      	      $total_sod = number_format($total_sod, 2, '.', ',');
                                      	       
                                      		  $tbl .= "<td style='text-align:right;'>" . $total_sod . "</td>";
                                      		  $counter++;

                                      	    }

                                      	    //============================================================================================
                            				$rsort = $year_filter;
		                              	    rsort($rsort);
		                              	    $total_sod_qty_PERF_val    = 0;
		                              	    $total_sod_all_prev_qty    = 0;
	   									    $total_sod_latest_year_qty = 0;

	   									    $latest_year = max($rsort);
		                              	    foreach ($rsort as $y) 
		                              	    {
	                              	         $total_sod = isset($totalsPerYear_quantity[$y]) ? $totalsPerYear_quantity[$y] : 0;
	                              	        
		                              	   	   if ($y !== $latest_year) {

		                              	   	   		    $total_sod_all_prev_qty += $total_sod;
			                                            continue; // Skip the latest year.................................
			                                        }	

	  										  $total_sod_latest_year_qty = $total_sod; 
	  						
		                              	    } // end of year foreach.................................

                                      	    //============================================================================================
		                              	    foreach ($rsort as $y) 
		                              	    {
	                              	         $total_sod = isset($totalsPerYear_quantity[$y]) ? $totalsPerYear_quantity[$y] : 0;
	                              	       
	                              	         $total_sod_qty_PERF_val -= $total_sod;
	                              	   

		                              	    } // end of year foreach.................................
	                                       
                                      	    //============================================================================================
		                                    // get SOD PERF total percentage sales and quantity ...................................
		                              	    $total_SOD_PERF_qty = 0;

		                              	    if($total_sod_all_prev_qty !== 0)
		                              	    {
		                              	     $total_SOD_PERF_qty = round($total_sod_qty_PERF_val / $total_sod_all_prev_qty * 100, 2);	
		                              	    }


                                      	    //============================================================================================
		                                    // Get total Average Monthly Sales And Quantity .......................................
		                              	    $monthly_total_latest_year_qty = 0;

		                              	    if($total_sod_latest_year_qty !== 0)
		                              	    {
		                              	     $monthly_total_latest_year_qty =  $total_sod_latest_year_qty / 5;
		                              	    }
 
                                      	    //============================================================================================
		                              	    // Get Daily Average Sales and Quantity ..............................................
		                              	    $daily_total_latest_year_qty = 0;
		                          
		                              	    if($monthly_total_latest_year_qty !== 0)
		                              	    {
		                              	      $daily_total_latest_year_qty = $monthly_total_latest_year_qty / 30;
		                              	    }

                                      	    //============================================================================================
	              	    	                // SOD View PERF .............................................................................
			                                $tbl .= "<td style='text-align: right;'>" .number_format($total_SOD_PERF_qty,2,'.',',') . "%</td>";
			                                
                                      	    //============================================================================================
			                                // SOD View Monthly Average Sales ............................................................
			                                $tbl .= "<td style='text-align: right;'>" .number_format($monthly_total_latest_year_qty,2,'.',',') . "</td>";
			                   
                                      	    //============================================================================================
			                                // SOD View Daily Average Sales ..............................................................
			                                $tbl .= "<td style='text-align: right;'>" .number_format($daily_total_latest_year_qty,2,'.',',') . "</td>";

                                            $tbl .= "</tr>";

                                        } // end of SOD_mall if condition..............................................................................


								             $tbl .= '
									    			  <tfoot>
													     <tr style="color: white;">
													       <th style="position: sticky; left: 0; background: darkcyan;">Total</th>';
													        
		                                                    for($a=0;$a<count($store_yearly_total_qty);$a++)
		                                                    {            
		                                                        
		                                                        $tbl .= '<th style="color:white; background: darkcyan; text-align: right;">'.number_format($store_yearly_total_qty[$a]).'</th>';
		                                                    } 
												                			      
											 $tbl .= '   </tr>
													</tfoot>';
							                 $tbl .= '<h2 ></h2>';	 
								             $tbl .= '</table>';
										     $tbl .= '<script>';
										     $tbl .= '$("#payments_table").DataTable({})';
										     $tbl .='</script>';
								             echo $tbl;

	                            } // end of select all store condition.............................................


	                    // else no division display all store============================================================================================================================
	                    }else{ // else no division display all store ..............................................

	                    			     // Get unique divisions from the details array.......................................................
	                					 $store_names = array_unique(array_column($details_yearly_store, 'store')); 

				                	     $tbl  = '<table  class="table table-bordered table-responsive" id="payments_table" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; ">';
	                                         $tbl .= '<thead style="color:white; text-align:center;">';
						                 $tbl .= "<h3 >Stores Yearly Quantity Report<h3>";
						             	 $tbl .= "<tr>";
							             $tbl .= "<th>Store Name</th>";
								            foreach ($year_store as $year) 
								                    {
								          	         $tbl .= "<th style='text-align:right;'>".$year."</th>";
								                    }
							             $tbl .= '</tr>';
							             $tbl .= '</thead>';

										                foreach ($store_names as $store)
								                        {
						                                      $storeDetails = array_filter($details_yearly_store, function ($detail) use ($store) {return $detail['store'] === $store;});  
						                                      if(!empty($storeDetails))
											                    {
											                       $store_names = $this->Sales_monitoring_mod->names_store($store);
											                       $tbl .= "<tr>";
											                       $tbl .= "<td>" . $store_names[0]['nav_store_val'] . "</td>";
											                   
											                            foreach ($year_store as $y) 
											                            {
											                               $total = '0';
											                               foreach ($storeDetails as $detail) 
											                               {
											                                   if ($detail['year'] == $y) {
											                                       $total = abs($detail['total_quantity_yearly']);
											                                       $total = round($total);
											                                       $total = number_format($total);
											                                       break;
											                                   }
											                               } // end if storeDetails...............................

											                               $tot       = str_replace(',', '', $total);
										                                   $totalSum += (float)$tot;
											                               $tbl .= "<td style='text-align:right;>" .$total . "</td>";

											                           } // end foreach year_store................................ 

											                       $tbl .= '</tr>';
											                    } // end if storeDetails  condition...............................

								                        } // end of foreach store_names.................................

							                     	    $tbl .= '<h2 hidden>Total Quantity: '.number_format($totalSum).'</h2>';		
							                            $tbl .= '</table>';
								                        $tbl .= '<script>';
								                        $tbl .= '$("#payments_table").DataTable({})';
								                        $tbl .='</script>';
							                            echo $tbl;


	                         } // end of else division condition...............................................
	                         // else no division display all store====================================================================================================================



                   }else{ // end of else  report_type condtion.......................................
           
	                      // VIEW ALL STORES NO DIVISION SALES AND QUANTITY ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
                        
                   	     // this condition display all stores total sales and quantity yearly............................................................................................
                         if($division === 'no_division')
                         {

                           $store_names = array_unique(array_column($details_yearly_store, 'store')); // Get unique divisions from the details array...................................
                	       $tbl  = '<table  class="table table-bordered table-responsive" id="payments_table" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b;">';
		                   $tbl .= "<h3>Stores Yearly Sales and Quantity Report<h3>";
	                     
	                       $tbl .= '<thead style="color:white;">';     
			               $tbl .= "<tr><th hidden>Stores Yearly Sales Report</th></tr>";
		             	   $tbl .= "<tr>";
			               $tbl .= "<th rowspan ='2' style='position: sticky; left: 0;background-color: #0b4568;color: white;'>Stores</th>";

					               		    // get total sales ......................
					               		    $get_total_all_store_yearly     = array();
					               		    $get_total_all_store_yearly_ADS = array();

					               		    // get total sales ......................
					               		    $get_total_all_store_yearly_qty     = array();
					               		    $get_total_all_store_yearly_qty_ADS = array();

					                        foreach ($year_store as $year) 
					                  	    {
										      // get total sales.............................
					             		      array_push($get_total_all_store_yearly, 0);
					             		      array_push($get_total_all_store_yearly_ADS, 0);

										      // get total quantity..........................
					             		      array_push($get_total_all_store_yearly_qty, 0);
					             		      array_push($get_total_all_store_yearly_qty_ADS, 0);
					                  	    }


		                   foreach ($year_store as $year_) 
		               	   {
		          		    $tbl .= "<th colspan='2' style='background-color: #0b4568;color: white; text-align: center;'>".$year_."</th>";
		               	   }
						    $tbl .="</tr>";

						    $tbl .="<tr>";
					               	     foreach ($year_store as $year_) 
					               	     {
				          		          $tbl .= '<th style="text-align: center; background-color: #0b4568;color: white;">SALES</th>';
					                      $tbl .= '<th style="text-align: center; background-color: #0b4568;color: white;">QTY</th>';
					               	     }

			                $tbl .= '</tr>';
		               	    $tbl .= '</thead>';
   
				                 foreach ($store_names as $store_name)
						                 {
						                   $counter = 0;
				                           $storeDetails = array_filter($details_yearly_store, function ($detail) use ($store_name) {return $detail['store'] === $store_name;});  
				                                   if (!empty($storeDetails))
										              {
								                       $store_names = $this->Sales_monitoring_mod->names_store($store_name);
								                       $tbl .= "<tr>";
								                       $tbl .=     "<td>" . $store_names[0]['nav_store_val'] . "</td>";
								                   
												                           foreach ($year_store as $y) 
												                           {
												                                $total = '0.00';
												                                foreach ($storeDetails as $detail) 
												                                {
												                                   if ($detail['year'] == $y) 
												                                   {
												                                   	 // total sales yearly..........................
												                                     $total = abs($detail['total']);
												                                     $total = number_format($total, 2, '.', ',');

												                                   	 // total quantity yearly.......................
												                                     $total_quantity_yearly = abs($detail['total_quantity_yearly']);
												                                     $total_qty      = number_format($total_quantity_yearly);

												                                     break;
												                                   }

												                                 } // end foreach storeDetails...................................

												                                 $tot   = str_replace(',', '', $total);
													                             $totalSum += (float)$tot;
												                                 $tbl .= "<td style='text-align:right;'>₱" .$total . "</td>";
												                                 $tbl .= "<td style='text-align:right;'>" .$total_qty . "</td>";

												                                 // get total sales yearly...................................
												                                 $get_total_all_store_yearly[$counter] += $tot;
												                                 $get_total_all_store_yearly_ADS[$counter] += $tot / 30;

												                                 // get total quantity yearly................................
												                                 $get_total_all_store_yearly_qty[$counter] += $total_quantity_yearly;
												                                 $get_total_all_store_yearly_qty_ADS[$counter] += $total_quantity_yearly / 30;

												                                 $counter++;


												                           } // end foreach year_store...................................
								                        
								                       $tbl .= '</tr>';

										              } // end if storeDetails condition...................................

						                } // end foreach store_names 

						      // =====================================================================================================================================================
						      // View total sales and quantity of all stores..........................................................................................................
                              $tbl .= '
							    	    <tfoot>
											    <tr style="color: white;">
											      <th style="position: sticky; left: 0; color: white; background: darkcyan;">Total</th>';
    
                                                    for($a=0;$a<count($get_total_all_store_yearly);$a++)
                                                    {            
                                                        
                                                     $tbl .= '<th style="color: white; background: darkcyan; text-align: right;">₱'.number_format($get_total_all_store_yearly[$a], 2, '.', ',').'</th>';
                                                     $tbl .= '<th style="color: white; background: darkcyan; text-align: right;">'.number_format($get_total_all_store_yearly_qty[$a]).'</th>';
                                                    } 
								                			      
							  $tbl .= '	      </tr>';
                              
						      // ======================================================================================================================================================
                              // View Average Daily Sales and Quantity..................................................................................................................
							  $tbl .= '
										      <tr style="color: white;">
												<th style="position: sticky; left: 0;color: white; background: darkcyan;">ADS</th>';
	    
	                                            for($a=0;$a<count($get_total_all_store_yearly);$a++)
	                                            {            
	                                                
	                                             $tbl .= '<th style="color: white; background: darkcyan; text-align: right;">₱'.number_format($get_total_all_store_yearly_ADS[$a], 2, '.', ',').'</th>';
	                                             $tbl .= '<th style="color: white; background: darkcyan; text-align: right;">'.number_format($get_total_all_store_yearly_qty_ADS[$a]).'</th>';
	                                            } 
									                			      
							  $tbl .= '      </tr>
									 </tfoot>';

                             $tbl .= '</table>';
						     $tbl .= '<script>';
						     $tbl .= '$("#payments_table").DataTable({})';
						     $tbl .='</script>';
                             echo $tbl;


                       }else{


                       	    // VIEW ALL STORES WITH DIVISION SALES AND QUANTITY ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

                   		    // function get all stores quantity and sales ...............................................................................
                   		    $index         = 0;
	  			 	        $divisions     = array_unique(array_column($details_yearly, 'item_division')); // Get unique divisions from the details array.............................
	  			 	        $stores        = array_unique(array_column($details_yearly, 'store'));
	  			 	      
	  			 	        // foreach get overall total sales and quantity all store yearly ....................................
	  			 	       	$over_all_final_total_arr_yearly = array();
	  			 	       	$over_all_final_total_arr_yearly_qty = array();
							foreach ($year_filter as $year_) 
							{
							  array_push($over_all_final_total_arr_yearly,0);
							  array_push($over_all_final_total_arr_yearly_qty,0);
							}
	  			 	        // end of foreach get overall total sales and quantity all store yearly ....................................

	  			 	       $count_store = '';

	  			 	       // foreach get all store sales and quantity total yearly .....................................
	  			 	       foreach($stores as $store)
	  			 	       { 
	  			 	       	 $count_store+=count($store);
	  			 	       	 
	  			 	         header("content-type: application/vnd.ms-excel");
					         header("Content-Disposition: attachment; filename= Sale Montly and Yearly Report.xls");
	  			 	         $final_total_arr_yearly =  array();
	  			 	         $final_total_arr_yearly_qty =  array();
							 $row_total = 0;

							 $overall_total_per_div = array();
						     
							
						       foreach ($year_filter as $year_) 
						       {
						         $row_total +=1;
						         array_push($final_total_arr_yearly,0);
						         array_push($final_total_arr_yearly_qty,0);
						         array_push($overall_total_per_div,0);
						       }

	
			                   $tbl  = '<table  class="table table-bordered table-responsive" id="payments_table_'.$index.'" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; ">';
			                   $tbl .= '<thead style="color:white;">';
       
			               	   $tbl .= "<tr>";

				               $tbl .= "<th rowspan ='2' style='position: sticky; left: 0;background-color: #0b4568;color: white;'>----Division_Name----</th>";
					                     foreach ($year_filter as $year_) 
					               	     {
					          		      $tbl .= "<th colspan='2' style='background-color: #0b4568;color: white; text-align: center;'>".$year_."</th>";
					               	     }
					               	      $tbl .= "<th colspan='2' style='background-color: #0b4568;color: white; text-align: center;'>PERF</th>";
					               	      $tbl .= "<th colspan='2' style='background-color: #0b4568;color: white; text-align: center;'>AVERAGE MONTHLY</th>";
					               	      $tbl .= "<th colspan='2' style='background-color: #0b4568;color: white; text-align: center;'>AVERAGE DAILY</th>";
					               	      $tbl .="</tr>";
 										
					               	     //
					               	      $tbl .="<tr>";
					               	     foreach ($year_filter as $year_) 
					               	     {
				          		          $tbl .= '<th style="text-align: center; background-color: #0b4568;color: white;">SALES</th>';
					                      $tbl .= '<th style="text-align: center; background-color: #0b4568;color: white;">QTY</th>';
					               	     }

					               	     //=========================================================================================
                                         // PERF Percentage ........................................................................
					               	      $tbl .= '<th style="text-align: center; background-color: #0b4568;color: white;">SALES</th>';
						                  $tbl .= '<th style="text-align: center; background-color: #0b4568;color: white;">QTY</th>';


					               	     //=========================================================================================
						                 // Monthly Daily ..........................................................................
					               	      $tbl .= '<th style="text-align: center; background-color: #0b4568;color: white;">SALES</th>';
						                  $tbl .= '<th style="text-align: center; background-color: #0b4568;color: white;">QTY</th>';

					               	     //=========================================================================================
						                 // Daily Sales ............................................................................
						                  $tbl .= '<th style="text-align: center; background-color: #0b4568;color: white;">SALES</th>';
						                  $tbl .= '<th style="text-align: center; background-color: #0b4568;color: white;">QTY</th>';

					               	      $tbl .="</tr>";
					              
					               	      $tbl .= '</thead>';

					               	      $storeDetails = array_filter($details_yearly, function ($detail) use ($store) {
										        return $detail['store'] === $store;
										    });
   
					               	        $store_code_       = '-S0015';
										    $storeNoToExclude_ = $store.$store_code_;

										    // get total SOD MALL per store ......................................................................................
									         $storeDivisionDetails_SOD_MALL = array_filter($details_yearly, function ($detail) use ($store, $storeNoToExclude_) {
									            return $detail['store'] === $store && $detail['store_no'] === $storeNoToExclude_;
									        });

							               	$totalsPerYear     = array();
							               	$totalsPerYear_qty = array();
							               	$SOD_store         = '';
							               	foreach ($storeDivisionDetails_SOD_MALL as $detail) 
											{
											    $year_     = $detail['year'];
											    $SOD_store = $detail['store'];
											    $total     = abs($detail['total']);
											    $total_qty = abs($detail['total_quantity_yearly']);
												
												//=============================================================================================================================
											    // If the entry for this month and year doesn't exist in $totalsPerMonthAndYear, initialize it to 0............................
											    if (!isset($totalsPerYear[$year_])) {
											        $totalsPerYear[$year_] = 0;
											    }

												//=============================================================================================================================
											     // If the entry for this month and year doesn't exist in $totalsPerMonthAndYear, initialize it to 0............................
											    if (!isset($totalsPerYear_qty[$year_])) {
											        $totalsPerYear_qty[$year_] = 0;
											    }
									
											    // Add the total to the corresponding month and year............................
											    $totalsPerYear[$year_] += $total;
											    $totalsPerYear_qty[$year_] += $total_qty;
											} // end of get total SOD MALL per store .............................

											// $divisions as $division ================================================================================================================
							                foreach ($divisions as $division)
							                {
							                    $counter = 0;
							                	$divisionDetails = array_filter($details_yearly, function ($detail) use ($store, $division, $storeNoToExclude_) {
													            return $detail['store'] === $store && $detail['item_division'] === $division && $detail['store_no'] !== $storeNoToExclude_;
													        });

							                    if(!empty($divisionDetails))
							                    {
							                       $div_name = $this->Sales_monitoring_mod->get_div_name_mod($division);
							                       $tbl .= "<tr style='background: white;'>";

							                        if(!empty($div_code))
								                       {
								                       	$div_code = $division;
								                       }else{
								                       	     $div_code = 'No Division';
								                            }

							                       if(!empty($div_name[0]['div_name']))
							                       {
							                        $tbl .= "<td style='background: white;position: sticky; left: 0;'>" . $div_name[0]['div_name'] . "</td>";
							                       }else{
							                       		 $tbl .= "<td style='background: white;position: sticky; left: 0;'>" . $div_code . "</td>";
							                            }

							                            $prev_total               = 0;
														$year_filter_             = $year_filter;
								                        $divisionTotals           = [];
								                        $divisionTotals_pre_years = [];
														rsort($year_filter_);

														$latest_year    = [];
														foreach ($year_filter_ as $year_) 
														{
														    $get_prev_2_years = $year_ -1; 

														    foreach ($divisionDetails as $detail)
														    {

														    	$divisionName = $detail['item_division'];
                                                                
														         if($detail['year'] == $year)
														         {

														           // get latest year total quantity and sales ...................................
							                                   	   $total_quantity    = round($detail['total_quantity_yearly']);
							                                       $total_sales       = abs($detail['total']);
							                                    
														            
							                                        // Add the totals to the division's running total............................
																    if (!isset($latest_year[$divisionName])) {
																        $latest_year[$divisionName] = [
																            'total_quantity' => $total_qty,
																            'total_sales' => $total_sales,
																        ];
																    }else{
														                  $latest_year[$divisionName]['total_sales']    = $total_sales;
							                                              $latest_year[$divisionName]['total_quantity'] = $total_quantity;
																         }

														         } // end of $detail['year'] if condition .......................


                                                                // total sales and quantity previous years............................................
														    	if($detail['year'] == $get_prev_2_years)
														    	{
														    	  // get quantity total ..........................
							                                   	   $total_qty     = round($detail['total_quantity_yearly']);
							                                   	   $total_qty_all = number_format($total_qty);

							                                   	   // get sale total ..............................
							                                 
							                                       $total_sales   = abs($detail['total']);
							                                      
														            
							                                        // Add the totals to the division's running total............................
																    if (!isset($divisionTotals_pre_years[$divisionName])) {
																        $divisionTotals_pre_years[$divisionName] = [
																            'total_quantity' => $total_qty,
																            'total_sales' => $total_sales,
																        ];
																    }else{
														                  $divisionTotals_pre_years[$divisionName]['total_sales'] +=$total_sales;
							                                              $divisionTotals_pre_years[$divisionName]['total_quantity'] += $total_qty;
																         }
														    	} // end of total sales and quantity previous years.....................


														        if($detail['year'] == $year_) 
														        {
														           // get quantity total ..........................
							                                   	   $total_qty     = round($detail['total_quantity_yearly']);
							                                   	   $total_qty_all = number_format($total_qty);

							                                   	   // get sale total ..............................
							                                       $total         = abs($detail['total']);
							                                       $total_sales   = abs($detail['total']);
							                                       $total         = number_format($total, 2, '.', ',');
														            
							                                        // Add the totals to the division's running total............................
																    if (!isset($divisionTotals[$divisionName])) {
																        $divisionTotals[$divisionName] = [
																            'total_quantity' => $total_qty,
																            'total_sales' => $total_sales,
																        ];
																    }else{
														                  $divisionTotals[$divisionName]['total_sales'] -=$total_sales;
							                                              $divisionTotals[$divisionName]['total_quantity'] -= $total_qty;
																         }
																	
							                               	
														        } // end of $detail['year'] if condition .......................................

														    } // end of divisionDetails foreach condition .................................

														} // end of year_filter_ foreach condition .................................



														     // get sales and quantity previous years............................ 
															 $prev_years_totalQuantityAllDivisions = 0;
															 $prev_years_totalSalesAllDivisions    = 0;
																
															 foreach ($divisionTotals_pre_years as $divisionName => $totals)
															 {
															  $prev_years_totalQuantityAllDivisions = $totals['total_quantity'];
															  $prev_years_totalSalesAllDivisions    = $totals['total_sales'];
															 }
														     // end get sales and quantity previous years 

															 // :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

															 // Calculate the overall total for all divisions............................
															 $totalQuantityAllDivisions = 0;
															 $totalSalesAllDivisions = 0;
																
															 foreach ($divisionTotals as $divisionName => $totals)
															 {
															  if($totals['total_sales'] == $prev_years_totalSalesAllDivisions && $totals['total_quantity'] == $prev_years_totalQuantityAllDivisions)
															  {
															  	$totalSalesAllDivisions = '-'.$totals['total_sales'];
															  	$totalQuantityAllDivisions = '-'.$totals['total_quantity'];
															  }else{
															  		 $totalSalesAllDivisions = $totals['total_sales'];
															  		 $totalQuantityAllDivisions = $totals['total_quantity'];
															       }
															 }

											
														     // end get sales and quantity previous years............................
															 $get_PERF_qty = 0;
															 $get_PERF_sales= 0;

															 // get PERF percentage.....................................
															 if($prev_years_totalQuantityAllDivisions !== 0)
															 {
														       $get_PERF_qty  = round($totalQuantityAllDivisions / $prev_years_totalQuantityAllDivisions * 100, 0);
															 }

															 if($prev_years_totalSalesAllDivisions !== 0)
															 {
															  $get_PERF_sales = round($totalSalesAllDivisions / $prev_years_totalSalesAllDivisions * 100, 2);
															 }

															   $daily_qty       = 0;
															   $daily_sales     = 0.00;
															   $latest_sales    = 0.00;
															   $latest_quantity = 0;
															 foreach($latest_year as $latest_total)
															 {
														       // get monthly average sales ..............................
														       $latest_sales    = $latest_total['total_sales']/ 5;
														       $latest_quantity = $latest_total['total_quantity']/ 5;
    
														       // get daily average sales ................................
														       $daily_sales     = $latest_sales/ 30;
														       $daily_qty       = $latest_quantity/ 30;
														   	 } // end of foreach latest_year ..............................................


								                           foreach ($year_filter as $y) 
								                           {
								                               $total		  = '0.00';
								                               $total_qty_all = '0';
	                   	                                       $total_qty     = '0';

								                               foreach ($divisionDetails as $detail) 
								                               {
								                               		  
								                                   if ($detail['year'] == $y) 
								                                   {
								                                   	   // get quantity total ..........................
								                                   	   $total_qty     = round($detail['total_quantity_yearly']);
								                                   	   $total_qty_all = number_format($total_qty);

								                                   	   // get sale total ..............................
								                                       $total         = abs($detail['total']);
								                                       $total         = number_format($total, 2, '.', ',');
								                                       break;
								                                   }

								                               } // end foreach divisionDetails................................

								                              
	    													   
								                               $tot       = str_replace(',', '', $total);
									                           $totalSum += (float)$tot;
								                               $tbl .= "<td style='text-align: right;'>" .$total . "</td>";
								                               $tbl .= "<td style='text-align: right;'>" .$total_qty_all . "</td>";

	                                                           // get sales total ....................................
								                               $final_total_arr_yearly[$counter] += $tot; 
								                               $over_all_final_total_arr_yearly[$counter] += $tot; 

	                                                           // get quantity total ....................................
								                               $final_total_arr_yearly_qty[$counter] += $total_qty;
								                               $over_all_final_total_arr_yearly_qty[$counter] += $total_qty;

								                               // get over all total per division ...........................
								                               $counter ++;
								                              
								                           } // end foreach year_filter................................ 

							                               // View PERF ................................................................................
								                           $sub_string     = strpos($get_PERF_sales, "-");
								                           $sub_string_qty = strpos($get_PERF_qty, "-");

								                           //===========================================================================================
								                           if($sub_string !== false && $sub_string_qty !== false)
								                           {
							                                 $tbl .= "<td style='text-align: right; color:red;'>" .number_format($get_PERF_sales,2,'.',',') . "%</td>";
							                                 $tbl .= "<td style='text-align: right; color:red;'>" .number_format($get_PERF_qty) . "%</td>";
								                           }else{
							                                      $tbl .= "<td style='text-align: right;color:green;'>" .number_format($get_PERF_sales,2,'.',',') . "%</td>";
							                                      $tbl .= "<td style='text-align: right;color:green;'>" .number_format($get_PERF_qty) . "%</td>";
								                                }

								                           //===========================================================================================
							                               // View Monthly Average Sales ................................................................
							                               $tbl .= "<td style='text-align: right;'>" .number_format($latest_sales, 2,'.',',') . "</td>";
							                               $tbl .= "<td style='text-align: right;'>" .number_format($latest_quantity) . "</td>";

								                           //===========================================================================================
							                               // View Daily Average Sales ..................................................................
							                               $tbl .= "<td style='text-align: right;'>" .number_format($daily_sales, 2,'.',',') . "</td>";
							                               $tbl .= "<td style='text-align: right;'>" .number_format($daily_qty) . "</td>";
							                        
							                               $tbl .= '</tr>';

							                   } // end if divisionDetails condition................................

							               } // end foreach divisions................................
											// $divisions as $division ================================================================================================================

// ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

                                        
                                        // display sod mall total ...........................................................................................
							            if($SOD_store === $store)
							            {
							             $tbl .= "<tr style='background: white;'>";  
										 $tbl .= "<td style='background: white;position: sticky; left: 0;color: black;'>" . $SOD_store.'-SOD MALL' . "</td>";
							             
										 $counter = 0; 			
										
	                              	    foreach ($year_filter as $y) 
	                              	    {
	                              	        
	                              	      $total_sod     = isset($totalsPerYear[$y]) ? $totalsPerYear[$y] : 0;
	                              	      $total_sod_qty = isset($totalsPerYear_qty[$y]) ? $totalsPerYear_qty[$y] : 0;
	                              	      //.........................................................................

	                              	      $final_total_arr_yearly[$counter] +=	$total_sod;
	                              	      $final_total_arr_yearly_qty[$counter] +=	$total_sod_qty;
	                              	      //.........................................................................

	                              	      $total_sod     = number_format($total_sod, 2, '.', ',');
	                              	      $total_sod_qty = number_format($total_sod_qty);
	                              	      //.........................................................................
	                              	      
	                              		  $tbl .= "<td style='text-align:right;'>" . $total_sod . "</td>";
	                              		  $tbl .= "<td style='text-align:right;'>" . $total_sod_qty . "</td>";
	                              		
	                              		  $counter++;

	                              	    } // end of year foreach................................

	                              	    $rsort = $year_filter;
	                              	    rsort($rsort);
	                              	    $total_sod_sale_PERF_val  = 0;
	                              	    $total_sod_qty_PERF_val   = 0;
	                              	    //............................

	                              	    $total_sod_all_prev_sales = 0;
	                              	    $total_sod_all_prev_qty   = 0;
	                              	    //............................

   									    $total_sod_latest_year_sales  = 0;
   									    $total_sod_latest_year_qty    = 0;
	                              	    //............................

   									    $latest_year = max($rsort);
	                              	    foreach ($rsort as $y) 
	                              	    {
                              	         $total_sod = isset($totalsPerYear[$y]) ? $totalsPerYear[$y] : 0;
                              	         $total_sod_qty = isset($totalsPerYear_qty[$y]) ? $totalsPerYear_qty[$y] : 0;

	                              	   	   if ($y !== $latest_year)
	                              	   	      {
	                              	   	   	   $total_sod_all_prev_sales += $total_sod;
	                              	   	   	   $total_sod_all_prev_qty += $total_sod_qty;
		                                       continue; // Skip the latest year.................................
		                                      }	

  										  	  $total_sod_latest_year_sales = $total_sod; 
  										  	  $total_sod_latest_year_qty   = $total_sod_qty; 
              
	                              	    } // end of year foreach................................

	                              	    foreach ($rsort as $y) 
	                              	    {
                              	         $total_sod = isset($totalsPerYear[$y]) ? $totalsPerYear[$y] : 0;
                              	         $total_sod_qty = isset($totalsPerYear_qty[$y]) ? $totalsPerYear_qty[$y] : 0;

                              	         $total_sod_sale_PERF_val -= $total_sod;
                              	         $total_sod_qty_PERF_val -= $total_sod_qty;

	                              	    } // end of year foreach................................
                                       
	                                   // get SOD PERF total percentage sales and quantity ..............................................
	                              	    $total_SOD_PERF_sales = 0;
	                              	    $total_SOD_PERF_qty   = 0;

	                              	    if($total_sod_all_prev_sales !== 0)
	                              	    {
	                              	     $total_SOD_PERF_sales = round($total_sod_sale_PERF_val / $total_sod_all_prev_sales * 100, 2);	
	                              	    }

	                              	    //...............................................................................................
	                              	    if($total_sod_all_prev_qty !== 0)
	                              	    {
	                              	     $total_SOD_PERF_qty = round($total_sod_qty_PERF_val / $total_sod_all_prev_qty * 100 ,2);
	                              	    }

	                                    // Get total Average Monthly Sales And Quantity ..................................................
	                              	    $monthly_total_latest_year_sales = 0;
	                              	    $monthly_total_latest_year_qty   = 0;

	                              	    if($total_sod_latest_year_sales !== 0)
	                              	    {
	                              	     $monthly_total_latest_year_sales =  $total_sod_latest_year_sales / 5;
	                              	    }

	                              	    if($total_sod_latest_year_qty  !== 0)
	                              	    {
	                              	     $monthly_total_latest_year_qty = $total_sod_latest_year_qty / 5;
	                              	    }

	                              	    // Get Daily Average Sales and Quantity .........................................................
	                              	    $daily_total_latest_year_sales = 0;
	                              	    $daily_total_latest_year_qty   = 0;

	                              	    if($monthly_total_latest_year_sales !== 0)
	                              	    {
	                              	      $daily_total_latest_year_sales = $monthly_total_latest_year_sales / 30;
	                              	    }
	                              	    //...............................................................................................
	                              	    if($monthly_total_latest_year_qty !== 0)
	                              	    {
	                              	      $daily_total_latest_year_qty = $monthly_total_latest_year_qty / 30;	
	                              	    }
 										
 									    $sub_string     = strpos($total_SOD_PERF_sales, "-");
								        $sub_string_qty = strpos($total_SOD_PERF_qty, "-");

								        //===============================================================================================
              	    	                // SOD View PERF ................................................................................
              	    	                if($sub_string !== false && $sub_string_qty !== false)
              	    	                {
		                                 $tbl .= "<td style='text-align: right; color:red;'>" .number_format($total_SOD_PERF_sales,2,'.',',') . "%</td>";
		                                 $tbl .= "<td style='text-align: right; color:red;'>" .number_format($total_SOD_PERF_qty) . "%</td>";

              	    	                }else{
		                                      $tbl .= "<td style='text-align: right; color:green;'>" .number_format($total_SOD_PERF_sales,2,'.',',') . "%</td>";
		                                      $tbl .= "<td style='text-align: right; color:green;'>" .number_format($total_SOD_PERF_qty) . "%</td>";
              	    	                     }
 
								        //===============================================================================================
		                                // SOD View Monthly Average Sales ...............................................................
		                                $tbl .= "<td style='text-align: right;'>" .number_format($monthly_total_latest_year_sales,2,'.',',') . "</td>";
		                                $tbl .= "<td style='text-align: right;'>" .number_format($monthly_total_latest_year_qty) . "</td>";
 
								        //===============================================================================================
		                                // SOD View Daily Average Sales ..................................................................
		                                $tbl .= "<td style='text-align: right;'>" .number_format($daily_total_latest_year_sales,2,'.',',') . "</td>";
		                                $tbl .= "<td style='text-align: right;'>" .number_format($daily_total_latest_year_qty,2,'.',',') . "</td>";
		                                
	                                    $tbl .= "</tr>";

	                                }

	                                    // end of SOD display total sales and quantity ..................................................................................
						               
							            $store_name    = $this->Sales_monitoring_mod->names_store($store);
							            $tbl .='<div class="responsive-div_top" style="margin-top: 1px; margin-bottom: 10px;"><div class="line-separator"></div></div>';
							            $tbl .= "<h3 style='font-size: 23px;'>Sales and Quantity Yearly Report => Store Name:".$store_name[0]['nav_store_val']."<h3>";
					                    // $tbl .= '
								    	// 		  <tfoot>
										// 		    <tr style="color: white;">
										// 		      <th style="background: darkcyan;">Total</th>';
												      
												        
	                                    //                 for($a=0;$a<count($final_total_arr_yearly);$a++)
	                                    //                 {            
	                                                        
	                                    //                     $tbl .= '<th style="background: darkcyan;text-align: right;">'.number_format($final_total_arr_yearly[$a], 2, '.', ',').'</th>';
	                                    //                     $tbl .= '<th style="background: darkcyan;text-align: right;">'.number_format($final_total_arr_yearly_qty[$a]).'</th>';
	                                    //                 }


	                                    //                 // View PERF ................................................................................
						                //                 $tbl .= "<td style='text-align: right;'></td>";
						                //                 $tbl .= "<td style='text-align: right;'></td>";
				 
						                //                 // View Monthly Average Sales ...............................................................
						                //                 $tbl .= "<td style='text-align: right;'></td>";
						                //                 $tbl .= "<td style='text-align: right;'></td>";
				 
						                //                 // View Daily Average Sales ..................................................................
						                //                 $tbl .= "<td style='text-align: right;'></td>";
						                //                 $tbl .= "<td style='text-align: right;'></td>"; 
									                			      
									    // $tbl .= '	</tr>
										// 		 </tfoot>';

										

						                $tbl .= '</table>';
						                $tbl .= '<script>';
									    $tbl .= '$("#payments_table_'.$index.'").DataTable({ scrollX: true })';
									    $tbl .='</script>';
						                echo $tbl;  
						                $index++;

		  			 	           } // end of foreach stores................................ 


		  			 	                // View Grand Total Sales and Quantity of all Store..............................................................
	                                    if($count_store !== 1)
	                                    {
			  			 	                $tbl2  = '<table  class="table table-bordered table-responsive" id="payments_table_yearly" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; ">';
										    $tbl2 .= '<thead style="color:white;">';
							         	    $tbl2 .= "<tr>";
							                $tbl2 .= "<th rowspan='2' style='position: sticky; left: 0;color:white;'></th>";

								                          foreach ($year_filter as $year_) 
								               	          {
								          		            $tbl2 .= "<th colspan='2' style='text-align: center; background:#0b4568; color:white;'>".$year_."</th>";
								               	          }

								            $tbl2 .="</tr>";
								            $tbl2 .= "<tr>";
											              foreach ($year_filter as $year_) 
											              {
											          		$tbl2 .= '<th style="text-align: center; background-color: #0b4568;color: white;">SALES</th>';
										                    $tbl2 .= '<th style="text-align: center; background-color: #0b4568;color: white;">QTY</th>';
											              }


								            $tbl2 .="</tr>";
										    $tbl2 .= '</thead>';
										    $tbl2 .= '
										              <tr style="color: white;">';
													      $tbl2 .= ' <td style="background: darkcyan;">Grand Total</td>';												        
			                                              for($a=0;$a<count($over_all_final_total_arr_yearly);$a++)
			                                              {            
			                                               $tbl2 .= '<td style="color: white; background: darkcyan; text-align: right;">'.number_format($over_all_final_total_arr_yearly[$a], 2, '.', ',').'</td>';
			                                               $tbl2 .= '<td style="color: white; background: darkcyan; text-align: right;">'.number_format($over_all_final_total_arr_yearly_qty[$a]).'</td>';
			                                              }   			      
										    $tbl2 .= '</tr>';
										    $tbl2 .= '</table>';
										    $tbl2 .= '<script>';
										    //$tbl2 .= '$("#payments_table_yearly").DataTable({ scrollX: true, lengthChange: false,searching: false,info: false});';
										    $tbl2 .= '</script>';
									        echo $tbl2;

		  			 	                      // end of View Grand Total Sales and Quantity of all Store.......................................................

	                                    } // end of $count_store !== 1 of condition................................................................

                           } // end of else view all stores sales and quantity yearly...................................................

                    }// end of else  report_type condtion view all store sales and quantity yearly............................

	       } // end if function get_yearly_report..................................................................

// ======================================================================================================================================================================================
// UOM UPLOADING UI :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

	       // function view server side uom table ....................................
	       function uom_table_view_server_side()
	       {
	        $code          = $this->input->post('code'); 
	        $select_store  = $this->input->post('select_store'); 
	        $start         = $this->input->post('start'); 
		    $length        = $this->input->post('length'); 
		    $searchValue   = $this->input->post('search')['value']; 
		 
		    $query = $this->db->select('nav_UOM_header_id,uom.store,uom.Item_No,msfl.Item_no,description,code,qty_per_unit_of_measure,length,width,height,cubage,weight,primary_key')
		                      ->from('mpdi.nav_uom_header as uom')
		                      ->join('nav_item_masterfile as msfl', 'msfl.item_no = uom.Item_No AND msfl.store = uom.store', 'inner')
		                      ->group_start()
		                      ->like('uom.store', $searchValue)
						      ->or_like('uom.Item_No', $searchValue)
						      ->or_like('code', $searchValue)
						      ->or_like('qty_per_unit_of_measure', $searchValue)
						      ->or_like('primary_key', $searchValue)
						      ->or_like('description', $searchValue)
						      ->group_end();

						      if (!empty($code)) {
						          $this->db->where('code', $code);
							  }

							  if (!empty($select_store)) {
							    $this->db->where('uom.store', $select_store);
							  }


							 $this->db->limit($length, $start);
				             $query = $this->db->get();

	         $totalRecords = $this->db->count_all('mpdi.nav_uom_header');
		   		     $data = array(
						           'draw'            => $this->input->post('draw'), 
						           'recordsTotal'    => $totalRecords,
						           'recordsFiltered' => $totalRecords,
						           'data'            => $query->result()
				                  );

		                    echo json_encode($data);  	
	       }

           // function get filter ...................................................
	       function get_uom_table_filter()
	       {
	     	$get_store  = $this->Sales_monitoring_mod->get_data_store();     	
           	echo json_encode(array($get_store));	     	
	       }

	       function get_uom_table_filter_code()
	       {	       	
	     	$get_code   = $this->Sales_monitoring_mod->get_data_code($_POST['select_store']);     	
           	echo json_encode(array($get_code));	   
	       }

	       // function upload uom text file ........................................
	       function uom_upload()
	       {	      	
	       	 $uploadedFile 		      = $_FILES['file'];
	      	 $fileName_s              = $uploadedFile['name'];
		  	 $fileTmpPath_s           = $uploadedFile['tmp_name'];
		  	 $fileSize_s              = $uploadedFile['size'];
		  	 $fileError               = $uploadedFile['error'];  
          	 $fileContent_s           = file_get_contents($fileTmpPath_s);
	      	 $lines                   = explode(PHP_EOL, $fileContent_s);

	      	 $select_dept             = $_POST['select_dept'];
	      	 $store                   = '';
		     $Item_No                 = '';
		     $code                    = '';
		     $qty_per_unit_of_measure = '';
		     $length                  = '';
		     $width                   = '';
		     $height                  = '';
		     $cubage                  = '';
		     $weight                  = '';
		     $primary_key             = '';
		     $num_in_barcode          = '';
		     $print_shelf_label       = '';
		     $text_on_shelf_label     = '';

		     $counter = '';

	      	 foreach($lines as $line)
	      	 {
 			  $uom_data = explode("|", $line);
 			  if(count($uom_data)>=12)
 			  {
 			  	        
	 			        //......................................
		 			    if(!empty($select_dept))
		 			    {
		 			      $store = $select_dept; 	
		 			    }else{
		 			    	   $store = '';
		 			         }
	 			        //......................................
		 			    if(!empty($uom_data[0]))
		 			    {
		 			      $Item_No = $uom_data[0];
		 			    }else{
		 			    	  $Item_No = '';
		 			         }
	 			        //......................................
		 			    if(!empty($uom_data[1]))
		 			    {
		 			      $code = str_replace("'", "", $uom_data[1]);

		 			    }else{
		 			    	  $code = '';
		 			         }
		 		        //......................................
		 			    if(!empty($uom_data[2]))
		 			    {
		 			      $qty_per_unit_of_measure = $uom_data[2];
		 			    }else{
		 			    	  $qty_per_unit_of_measure = '0';
		 			         }

		 	            //......................................
		 			    if(!empty($uom_data[3]))
		 			    {
		 			      $length = $uom_data[3];
		 			    }else{
		 			    	  $length = '0';
		 			         }
		 		        //......................................
		 			    if(!empty($uom_data[4]))
		 			    {
		 			      $width = $uom_data[4];
		 			    }else{
		 			    	  $width = '0';
		 			         }
		 		         //......................................
		 			    if(!empty($uom_data[5]))
		 			    {
		 			      $height = $uom_data[5];
		 			    }else{
		 			    	  $height = '0';
		 			         }
		 		         //......................................
		 			    if(!empty($uom_data[6]))
		 			    {
		 			      $cubage = $uom_data[6];
		 			    }else{
		 			    	  $cubage = '0';
		 			         }
		 		        //......................................
		 			    if(!empty($uom_data[7]))
		 			    {
		 			      $weight = $uom_data[7];
		 			    }else{
		 			    	  $weight = '0';
		 			         }
		 	            //......................................
		 			    if(!empty($uom_data[8]))
		 			    {
		 			      $primary_key = $uom_data[8];
		 			    }else{
		 			    	  $primary_key = '';
		 			         }
		 		        //......................................
		 			    if(!empty($uom_data[9]))
		 			    {
		 			      $num_in_barcode = $uom_data[9];
		 			    }else{
		 			    	  $num_in_barcode = '';
		 			         }
		 		        //......................................
		 			    if(!empty($uom_data[10]))
		 			    {
		 			      $print_shelf_label = $uom_data[10];
		 			    }else{
		 			    	  $print_shelf_label = '';
		 			         }
		 		        //......................................
		 			    if(!empty($uom_data[11]))
		 			    {
		 			      $text_on_shelf_label = $uom_data[11];
		 			    }else{
		 			    	  $text_on_shelf_label = '';
		 			         }
		 	            //......................................
		 			    $check_data = array(
								            'store' 				  => $store,
											'Item_No' 				  => $Item_No,
											'code' 					  => $code,
											'qty_per_unit_of_measure' => $qty_per_unit_of_measure,
											'length'                  => $length,
											'width'                   => $width,
											'height'                  => $height,
											'cubage'                  => $cubage,
											'weight'                  => $weight,
											'primary_key'             => $primary_key,
											'num_in_barcode' 		  => $num_in_barcode,
											'print_shelf_label'       => $print_shelf_label,
											'text_on_shelf_label'     => $text_on_shelf_label
							               );

                        $check_data = $this->Sales_monitoring_mod->check_data($check_data);

                        if($check_data)
                        {
                         echo 'naa na';
                        }else{
                        	    echo 'wala pa';
						        $this->Sales_monitoring_mod->insert_nav_uom_header($store,$Item_No,$code,$qty_per_unit_of_measure,$length,$width,$height,$cubage,$weight,$primary_key,$num_in_barcode,$print_shelf_label,$text_on_shelf_label);  
						       
                             }

 			    } // end of count condition.......................................

	      	   // $counter++;
		       // if($counter == 500)
		       // 	break;
	      	 } // end of foreach

	       } // end of function uom_upload....................


	       // function update UOM...................................................
	       function update_uom()
	       {
			  $rowData           = $this->input->post();
			  $nav_UOM_header_id = $rowData['nav_UOM_header_id'];
			  $data_updated      = $rowData['4'];

			  $this->db->set('code', $data_updated);
			  $this->db->where('nav_UOM_header_id', $nav_UOM_header_id);
			  $this->db->update('mpdi.nav_uom_header');
			
	       }


	    }


?>



