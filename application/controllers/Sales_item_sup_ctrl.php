<?php
class Sales_item_vendor_ctrl extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->model('Acct_mod');
		$this->load->model('Po_mod');

	}

	function view_yearly_monthly_report()
	{
		$tot            = '';
     	$totalSum       = '0.00';
     	$totalSumQ       = '0';
     	$totalSum_      = '0.00';
		$tbl            = '';
		$range          = $_POST['range'];
		$store_no       = $_POST['store_no'];
		$year           = $_POST['year'];
		$report_type    = $_POST['report_type'];
		$category     	= $_POST['category'];
		$code 			= $_POST['code'];

		// get 3 previous year
		$original_year = $year;
		$sub_year      = 2;
		$pre_year      = $original_year - $sub_year;

		$details   = $this->Acct_mod->get_monthly_report_vendor_mod($store_no,$year,strval($pre_year), $category,$code);     
		$year          = array($original_year,$original_year-1,$pre_year); 
		// var_dump($get_monthly);           	
		$month_name    = array(
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
		$vendor_no      = array();
		// $details       	= array();
		$month      = array('January','February','March','April','May','June','July','August','September','October','November','December');
		$total_ 		= array();

			
		if($report_type == 'sales')
		{
			if($store_no == 'Select_all_store') {

	        	$vendors = array_unique(array_column($details, 'vendor_no')); // Get unique vendors from the details array
	        	$stores = array_unique(array_column($details, 'store'));

				$cat = ($category == 'dept') ? "Department" : "Group";
				
				$dept_name 	= $this->Acct_mod->get_dept_name($code);
				$group_name = $this->Acct_mod->get_group_name($code);
				$store_name = $this->Acct_mod->get_store_name($store_no);
				
				$cat_name = ($category == 'dept') ? $dept_name[0]['dept_name'] : $group_name[0]['group_name'];

               	$index = 0;
               	//var_dump($stores);

               	$over_all_final_total_arr = array();
				sort($year);

				foreach ($month_name as $month) 
				{
					foreach ($year as $y) 
					{
						array_push($over_all_final_total_arr,0);
					}

				}

				foreach ($stores as $store){
		        	$final_total_arr =  array();
					$row_total = 0;

					foreach ($month_name as $month) 
				 	{
			       		foreach ($year as $y) {
			        		$row_total +=1;
			        		array_push($final_total_arr,0);
			       		}
				 	}
				 	header("content-type: application/vnd.ms-excel");
		            header("Content-Disposition: attachment; filename= Monthly Sales Report per ".$cat." per Supplier.xls");
			     	$tbl  = '<table border="1" class="table table-bordered table-responsive" id="view_table_sales_vendor_'.$index.'" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b;">';
		               	
		      		$tbl .= '<thead style="text-align: center;color:white;">';
		         	
					sort($year);

					$tbl .= '<tr>';
						//$tbl .= "<th style='position: sticky; left: 0; background-color: #033e5b; color: white; padding-right: 156px;'>Department</th>";
					$tbl .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; padding-right: 156px;">Vendor</th>';
					foreach ($month_name as $month) {
				        $tbl .= "<th style='text-align: center; color: white;' colspan=".count($year).">".$month['month']."</th>";
				    }
				    $tbl .= '</tr>';
				    // 
				    $tbl .= '<tr>';
				    foreach ($month_name as $month) {
		      	        for ($a = 0; $a < count($year); $a++) {
		      	            $tbl .= '<th style="color: white;">'.$year[$a].'</th>';
		      	        }
		      	    }
				    $tbl .= '</tr>';
		      	    $tbl .= '</thead>';

			     	foreach ($vendors as $vendor)
		     		{

				     	$counter = 0;
			         	$vendorDetails = [];

					    // Find the details for the current group
					    foreach ($details as $detail) {
					        if ($detail['store'] === $store && $detail['vendor_no'] === $vendor) {
					            $vendorDetails[] = $detail;
					        }
					    }

			     		if (!empty($vendorDetails))
			     		{
				     		$vendor_name = $this->Acct_mod->get_vendor_name($vendor);
				     		//echo($vendor);die();
				     		$vendors_name = (count($vendor_name)>0) ? $vendor_name[0]['Name'] : 'No Vendor';
				     		$tbl .= "<tr>";
				     		//$tbl .= "<td>" . $vendor . "</td>";
				     		//$tbl .= "<td>" . $vendors_name . "</td>";
				     		$tbl .= "<td style='position: sticky; left: 0; background-color: #fff;'>" . $vendors_name . ' - ' . $vendor . "</td>";


				     		foreach ($month_name as $month)
				     		{
				     			foreach ($year as $y) {
				     				$total = '0.00';
				     				foreach ($vendorDetails as $detail) {
				     					if ($detail['month'] == $month['number'] && $detail['year'] == $y) {
				     						$total = abs($detail['total']);
				     						$total = number_format($total, 2, '.', ',');
				     						break;
				     					}
				     				}
				     				$tot   = str_replace(',', '', $total);
									$totalSum += (float)$tot;
				     				$tbl .= "<td style='background-color: white; color: black;'>&#8369; " . $total . "</td>";

				     				$final_total_arr[$counter] += $tot;
				     				$over_all_final_total_arr[$counter] += $tot;
									$counter ++;
				     			}
				     		}

				     		$tbl .= '</tr>';
				     	}
		     		}
			     	$store_name = $this->Acct_mod->get_store_name($store);
						 
	              	$tbl .='<div class="responsive-div_top" style="margin-top: 1px; margin-bottom: 10px;"><div class="line-separator"></div></div>';
	              	//$tbl .= "<h4>Monthly Sales Report per Department => Store Name:".$store_name[0]['nav_store_val']."<h4>";
				    //$tbl .='<h3 style="font-size: 26px;">Monthly Sales Report per Group => Store Name: ' . $store_name[0]['nav_store_val'] . '</h3>';
				    // $tbl .='<h3 style="font-size: 17px;">Total Sales: &#8369;' . number_format($totalSum, 2, '.', ',') . '</h3>';
				    $tbl .= "<h4>Monthly Sales Report per ".$cat." per Supplier => Store Name: ".$store_name[0]['nav_store_val']."<h4>";
		    		$tbl .= "<h4>".$cat." Name: ".$code." - ".$cat_name."<h4>";

				    $tbl .= '
				    			  <tfoot>
								    <tr style="color: white;">
								      <th style="position: sticky; left: 0; background: darkcyan;">Total</th>';
								        
                                        for($a=0;$a<count($final_total_arr);$a++)
                                        {            
                                            
                                            $tbl .= '<th style="background: darkcyan;">&#8369; '.number_format($final_total_arr[$a], 2, '.', ',').'</th>';
                                        } 
					                			      
					$tbl .= '			    </tr>
								  </tfoot>	

						            ';		
		         	$tbl .= '</table>';
		         	$tbl .= '<script>';
					$tbl .= '$(document).ready(function() {';
					$tbl .= 'console.log("Initializing DataTable...");';
					$tbl .= '$("#view_table_sales_vendor_'.$index.'").DataTable({ scrollX: true });';
					$tbl .= '});';
					$tbl .= '</script>';
		         	echo $tbl;
		         	$index++;
		        } // end foreach store

		        $tbl2 = '<table border="1" class="table table-bordered table-responsive" id="view_table_sales_group_total" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; width: auto; table-layout: auto;">';
			    $tbl2 .= '<thead style="text-align: center;color:white;">';
         	    $tbl2 .= '<tr>';
         	   	$tbl2 .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; padding-right: 156px;">Vendor</th>';
			    sort($year);
					foreach ($month_name as $month) {
				        $tbl2 .= "<th style='text-align: center; color: white;' colspan=".count($year).">".$month['month']."</th>";
				    }
				    $tbl2 .= '</tr>';
				    // 
				    $tbl2 .= '<tr>';
				    foreach ($month_name as $month) {
	          	        for ($a = 0; $a < count($year); $a++) {
	          	            $tbl2 .= '<th style="color: white;">'.$year[$a].'</th>';
	          	        }
	          	    }
			    $tbl2 .= '</tr>';
			    $tbl2 .= '</thead>';

			    $tbl2 .= '
				           
				                <tr style="color: white;">
							      <td style="position: sticky; left: 0; background: darkcyan;">Grand Total</td>';
							        
                                    for($a=0;$a<count($over_all_final_total_arr);$a++)
                                    {            
                                        
                                        $tbl2 .= '<td style="background-color: white; color: black;">&#8369;'.number_format($over_all_final_total_arr[$a], 2, '.', ',').'</td>';
                                    } 
				                			      

					$tbl2 .= '</tr>';
			    $tbl2 .= '</table>';
			    $tbl2 .= '<script>';
			    $tbl2 .= '$("#view_table_sales_group_total").DataTable({ scrollX: true,  lengthChange: false,searching: false,info: false});';
			    $tbl2 .= '</script>';

			    echo $tbl2;     
	        } // end for all store
	        else{
				$vendors = array_unique(array_column($details, 'vendor_no')); // Get unique vendors from the details array
				$cat = ($category == 'dept') ? "Department" : "Group";
				
				$dept_name 	= $this->Acct_mod->get_dept_name($code);
				$group_name = $this->Acct_mod->get_group_name($code);
				$store_name = $this->Acct_mod->get_store_name($store_no);
				
				$cat_name = ($category == 'dept') ? $dept_name[0]['dept_name'] : $group_name[0]['group_name'];

				$final_total_arr_per_store =  array();
				$row_total = 0;

				foreach ($month_name as $month) 
			 	{
		       		foreach ($year as $y) {
		        		$row_total +=1;
		        		array_push($final_total_arr_per_store,0);
		       		}
			 	}
			    
			    $tbl  = '<table  class="table table-bordered table-responsive" id="payments_table_sales" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b;">';
	               	
	        	$tbl .= '<thead style="text-align: center;color:white;">';
	           	// Generate table headers
	        	//$tbl .= "<h4>Monthly Sales Report per Department => Store Name:".$store_name[0]['nav_store_val']."<h4>";
	        	$tbl .= "<h4>Monthly Sales Report per ".$cat." per Supplier => Store Name: ".$store_name[0]['nav_store_val']."<h4>";
		    	$tbl .= "<h4>".$cat." Name: ".$code." - ".$cat_name."<h4>";
	          
	           	//$tbl .= "<tr>";
	           	sort($year);

				$tbl .= '<tr>';
					//$tbl .= "<th style='position: sticky; left: 0; background-color: #033e5b; color: white; padding-right: 156px;'>Department</th>";
				$tbl .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; padding-right: 156px;">Vendor</th>';
				foreach ($month_name as $month) {
			        $tbl .= "<th style='text-align: center;' colspan=".count($year).">".$month['month']."</th>";
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
			    
	     		foreach ($vendors as $vendor)
	     		{

			     	$counter = 0;
		         	$vendorDetails = [];

				    // Find the details for the current group
				    foreach ($details as $detail) {
				        if ($detail['vendor_no'] === $vendor) {
				            $vendorDetails[] = $detail;
				        }
				    }

		     		if (!empty($vendorDetails))
		     		{
			     		$vendor_name = $this->Acct_mod->get_vendor_name($vendor);
			     		//echo($vendor);die();
			     		$vendors_name = (count($vendor_name)>0) ? $vendor_name[0]['Name'] : 'No Vendor';
			     		$tbl .= "<tr>";
			     		//$tbl .= "<td>" . $vendor . "</td>";
			     		//$tbl .= "<td>" . $vendors_name . "</td>";
			     		$tbl .= "<td style='position: sticky; left: 0; background-color: #fff;'>" . $vendors_name . ' - ' . $vendor . "</td>";


			     		foreach ($month_name as $month)
			     		{
			     			foreach ($year as $y) {
			     				$total = '0.00';
			     				foreach ($vendorDetails as $detail) {
			     					if ($detail['month'] == $month['number'] && $detail['year'] == $y) {
			     						$total = abs($detail['total']);
			     						$total = number_format($total, 2, '.', ',');
			     						break;
			     					}
			     				}
			     				$tot   = str_replace(',', '', $total);
								$totalSum += (float)$tot;
			     				$tbl .= "<td>₱ " . $total . "</td>";

			     				$final_total_arr_per_store[$counter] += $tot;
								$counter ++;
			     			}
			     		}

			     		$tbl .= '</tr>';
			     	}
	     		}
	     		$tbl .= '<tfoot>
				    	<tr style="color: white;">
				      	<th style="position: sticky; left: 0; background: darkcyan;">Total</th>';
				        
	                  	for($a=0;$a<count($final_total_arr_per_store);$a++)
	                  	{            
	                        
	                   		$tbl .= '<th style="color:white; background: darkcyan;">₱'.number_format($final_total_arr_per_store[$a], 2, '.', ',').'</th>';
	                  	} 
	                			      
		  		$tbl .= '</tr>
				  	</tfoot>';		
	     		$tbl .= '</table>';
	          	$tbl .= '<script>';
				$tbl .= '$(document).ready(function() {';
				$tbl .= 'console.log("Initializing DataTable...");';
				$tbl .= '$("#payments_table_sales").DataTable({ scrollX: true });';
				$tbl .= '});';
				$tbl .= '</script>';
	            echo $tbl;
	        }

	   	}
	   	elseif($report_type == 'both')
		{
			if($store_no == 'Select_all_store') {

	        	$vendors = array_unique(array_column($details, 'vendor_no')); // Get unique vendors from the details array
	        	$stores = array_unique(array_column($details, 'store'));

				$cat = ($category == 'dept') ? "Department" : "Group";
				
				$dept_name 	= $this->Acct_mod->get_dept_name($code);
				$group_name = $this->Acct_mod->get_group_name($code);
				$store_name = $this->Acct_mod->get_store_name($store_no);
				
				$cat_name = ($category == 'dept') ? $dept_name[0]['dept_name'] : $group_name[0]['group_name'];

               	$index = 0;
               	//var_dump($stores);

               	$over_all_final_total_arr = array();
               	$over_all_final_total_arr2 = array();
				sort($year);

				foreach ($month_name as $month) 
				{
					foreach ($year as $y) 
					{
						array_push($over_all_final_total_arr,0);
						array_push($over_all_final_total_arr2,0);
					}

				}

				foreach ($stores as $store){

				 	$final_total_arr =  array();
			     	$final_total_arr2 =  array();
					$row_total = 0;
					$row_total2 = 0;

					foreach ($month_name as $month) 
				 	{
			       		foreach ($year as $y) {
			        		$row_total +=1;
			        		$row_total2 +=1;
			        		array_push($final_total_arr,0);
			        		array_push($final_total_arr2,0);
			       		}
				 	}
				 	header("content-type: application/vnd.ms-excel; charset=utf-8");
		            header("Content-Disposition: attachment; filename= Monthly Sales and Quantity Report per ".$cat." per Supplier.xls");
			     	$tbl  = '<table border="1" class="table table-bordered table-responsive" id="view_table_sales_qty_vendor_'.$index.'" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b;">';
		               	
		      		$tbl .= '<thead style="text-align: center;color:white;">';
		         	// Generate table headers
		      		//$tbl .= "<h4>Monthly Sales Report per Group => Store Name:".$store_name[0]['nav_store_val']."<h4>";
			     	//$tbl .= "<h2>Monthly Sales Report per Group => Store Name:".$store_no."<h2>";

			     	// Generate table headers

					sort($year);
	          	    $tbl .= '<tr>';
					//$tbl .= "<th style='position: sticky; left: 0; background-color: #033e5b; color: white; padding-right: 156px;'>Department</th>";
					$tbl .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; padding-right: 156px;">Vendor</th>';
					foreach ($month_name as $month) {
				        foreach ($year as $y) {
				            $tbl .= '<th colspan="2" style="text-align: center; background-color:  #033e5b;color: white;">' . $month['month'] . '-' . $y . '</th>';
				        }
				    }
				    $tbl .= '</tr>';
				    // 
				    $tbl .= '<tr>';
				    foreach ($month_name as $month) {
				        foreach ($year as $y) {
				            $tbl .= '<th style="text-align: center; background-color:  #033e5b;color: white;">SALES</th>';
				            $tbl .= '<th style="text-align: center; background-color:  #033e5b;color: white;">QTY</th>';
				        }
				    }
				    $tbl .= '</tr>';

	          	    $tbl .= '</thead>';

			     	foreach ($vendors as $vendor)
		     		{

				     	$counter = 0;
				     	$counter2 = 0;
			         	$vendorDetails = [];

					    // Find the details for the current group
					    foreach ($details as $detail) {
					        if ($detail['store'] === $store && $detail['vendor_no'] === $vendor) {
					            $vendorDetails[] = $detail;
					        }
					    }

			     		if (!empty($vendorDetails))
			     		{
				     		$vendor_name = $this->Acct_mod->get_vendor_name($vendor);
				     		//echo($vendor);die();
				     		$vendors_name = (count($vendor_name)>0) ? $vendor_name[0]['Name'] : 'No Vendor';
				     		$tbl .= "<tr>";
				     		//$tbl .= "<td>" . $vendor . "</td>";
				     		//$tbl .= "<td>" . $vendors_name . "</td>";
				     		$tbl .= "<td style='position: sticky; left: 0; background-color: #fff;'>" . $vendors_name . ' - ' . $vendor . "</td>";


				     		foreach ($month_name as $month)
				     		{
				     			foreach ($year as $y) {
				     				$total = '0.00';
				     				$total_qty = '0';
				     				foreach ($vendorDetails as $detail) {
				     					if ($detail['month'] == $month['number'] && $detail['year'] == $y) {
				     							$total = abs($detail['total']);
						                        $total = number_format($total, 2, '.', ',');

						                        $total_qty = abs($detail['total_quantity']);
		                                       	$total_qty = round($total_qty, 0); 
												$total_qty = intval($total_qty); // Convert to integer to remove decimal places
												$total_qty = number_format($total_qty);
						                        break;
						                    }
						                }
						                $tot = str_replace(',', '', $total);
						                $totalSum += (float)$tot;
						                $tbl .= "<td style='background-color: white;'>&#8369; {$total}</td>";

						                $tot_qty       = str_replace(',', '', $total_qty);
	                             		$totalSumQ += (float)$tot_qty;
	                             		$tbl .= "<td style='background-color: white;'>" .$total_qty . "</td>";

						                $final_total_arr[$counter] += $tot; 
					                    $over_all_final_total_arr[$counter] += $tot; 
										$counter ++;

										$final_total_arr2[$counter2] += $tot_qty; 
					                    $over_all_final_total_arr2[$counter2] += $tot_qty; 
										$counter2 ++;
				     			}
				     		}

				     		$tbl .= '</tr>';
				     	}
		     		}
			     	$store_name = $this->Acct_mod->get_store_name($store);
						 
	              	$tbl .='<div class="responsive-div_top" style="margin-top: 1px; margin-bottom: 10px;"><div class="line-separator"></div></div>';
	              	//$tbl .= "<h4>Monthly Sales Report per Department => Store Name:".$store_name[0]['nav_store_val']."<h4>";
				    //$tbl .='<h3 style="font-size: 26px;">Monthly Sales Report per Group => Store Name: ' . $store_name[0]['nav_store_val'] . '</h3>';
				    // $tbl .='<h3 style="font-size: 17px;">Total Sales: &#8369;' . number_format($totalSum, 2, '.', ',') . '</h3>';
				    $tbl .= "<h4>Monthly Sales and Quantity Report per ".$cat." per Supplier => Store Name: ".$store_name[0]['nav_store_val']."<h4>";
		    		$tbl .= "<h4>".$cat." Name: ".$code." - ".$cat_name."<h4>";

				    $tbl .= '
		    			  <tfoot>
						    <tr style="color: white;">
						      <th style="position: sticky; left: 0; background: darkcyan;">Total</th>';
						        
                                $max_length = max(count($final_total_arr), count($final_total_arr2));

								for ($a = 0; $a < $max_length; $a++) {
								    if ($a < count($final_total_arr)) {
								        $tbl .= '<th >&#8369;' . number_format($final_total_arr[$a], 2, '.', ',') . '</th>';
								    } else {
								        $tbl .= '<th></th>'; // Add an empty cell if the first array doesn't have a value for this iteration
								    }

								    if ($a < count($final_total_arr2)) {
								        $tbl .= '<th>' . number_format($final_total_arr2[$a]) . '</th>';
								    } else {
								        $tbl .= '<th></th>'; // Add an empty cell if the second array doesn't have a value for this iteration
								    }
								}
            		$tbl .= '		</tr>
			                
						  </tfoot>	

						            ';		
		         	$tbl .= '</table>';
		         	$tbl .= '<script>';
					$tbl .= '$(document).ready(function() {';
					$tbl .= 'console.log("Initializing DataTable...");';
					$tbl .= '$("#view_table_sales_qty_vendor_'.$index.'").DataTable({ scrollX: true });';
					$tbl .= '});';
					$tbl .= '</script>';
		         	echo $tbl;
		         	$index++;
		        } // end foreach store

		        $tbl2 = '<table border="1" class="table table-bordered table-responsive" id="view_table_sales_qty_vendor_total" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; width: auto; table-layout: auto;">';
			    $tbl2 .= '<thead style="text-align: center;color:white;">';
         	    sort($year);
	          	    $tbl2 .= '<tr>';
					//$tbl2 .= "<th style='position: sticky; left: 0; background-color: #033e5b; color: white; padding-right: 156px;'>Department</th>";
					$tbl2 .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; padding-right: 156px;">Vendor</th>';
					foreach ($month_name as $month) {
				        foreach ($year as $y) {
				            $tbl2 .= '<th colspan="2" style="text-align: center; background-color:  #033e5b;color: white;">' . $month['month'] . '-' . $y . '</th>';
				        }
				    }
				    $tbl2 .= '</tr>';
				    // 
				    $tbl2 .= '<tr>';
				    foreach ($month_name as $month) {
				        foreach ($year as $y) {
				            $tbl2 .= '<th style="text-align: center; background-color:  #033e5b;color: white;">SALES</th>';
				            $tbl2 .= '<th style="text-align: center; background-color:  #033e5b;color: white;">QTY</th>';
				        }
				    }
				    $tbl2 .= '</tr>';

	          	    $tbl2 .= '</thead>';

				    $tbl2 .= '<tr style="color: white;">';
				    $tbl2 .= ' <td style="position: sticky; left: 0; background: darkcyan;">Grand Total</td>';												        
	                    for($a=0;$a<count($over_all_final_total_arr);$a++)
	                    {            
	                        
	                        $tbl2 .= '<td style="color: white; background: darkcyan; text-align: right;">&#8369; '.number_format($over_all_final_total_arr[$a], 2, '.', ',').'</td>';
	                        $tbl2 .= '<td style="color: white; background: darkcyan; text-align: right;">'.number_format($over_all_final_total_arr2[$a]).'</td>';
	                    } 
				                			      

					$tbl2 .= '</tr>';
				    $tbl2 .= '</table>';
			    $tbl2 .= '<script>';
			    $tbl2 .= '$("#view_table_sales_qty_vendor_total").DataTable({ scrollX: true,  lengthChange: false,searching: false,info: false});';
			    $tbl2 .= '</script>';

			    echo $tbl2;     
	        } // end for all store
	        else{

				$vendors = array_unique(array_column($details, 'vendor_no')); // Get unique vendors from the details array
				$cat = ($category == 'dept') ? "Department" : "Group";
				
				$dept_name 	= $this->Acct_mod->get_dept_name($code);
				$group_name = $this->Acct_mod->get_group_name($code);
				$store_name = $this->Acct_mod->get_store_name($store_no);
				
				$cat_name = ($category == 'dept') ? $dept_name[0]['dept_name'] : $group_name[0]['group_name'];

				$final_total_arr_per_store =  array();
		     	$final_total_arr_per_store2 =  array();
				$row_total = 0;
				$row_total2 = 0;

				foreach ($month_name as $month) 
			 	{
		       		foreach ($year as $y) {
		        		$row_total +=1;
		        		$row_total2 +=1;
		        		array_push($final_total_arr_per_store,0);
		        		array_push($final_total_arr_per_store2,0);
		       		}
			 	}
			    
			    $tbl  = '<table  class="table table-bordered table-responsive" id="payments_table_sales" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b;">';
	               	
	        	$tbl .= '<thead style="text-align: center;color:white;">';
	           	// Generate table headers
	        	//$tbl .= "<h4>Monthly Sales Report per Department => Store Name:".$store_name[0]['nav_store_val']."<h4>";
	        	$tbl .= "<h4>Monthly Sales and Quantity Report per ".$cat." per Supplier => Store Name: ".$store_name[0]['nav_store_val']."<h4>";
		    	$tbl .= "<h4>".$cat." Name: ".$code." - ".$cat_name."<h4>";
	          
	           	//$tbl .= "<tr>";
	           	sort($year);

				$tbl .= '<tr>';
					
				$tbl .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; padding-right: 156px;">Vendor</th>';
				foreach ($month_name as $month) {
			        foreach ($year as $y) {
			            $tbl .= '<th colspan="2" style="text-align: center; background-color:  #033e5b;color: white;">' . $month['month'] . '-' . $y . '</th>';
			        }
			    }
			    $tbl .= '</tr>';
			    // 
			    $tbl .= '<tr>';
			    foreach ($month_name as $month) {
			        foreach ($year as $y) {
			            $tbl .= '<th style="text-align: center; background-color:  #033e5b;color: white;">SALES</th>';
			            $tbl .= '<th style="text-align: center; background-color:  #033e5b;color: white;">QTY</th>';
			        }
			    }
			    $tbl .= '</tr>';
	      	    $tbl .= '</thead>';
			    
	     		foreach ($vendors as $vendor)
	     		{

			     	$counter = 0;
			     	$counter2 = 0;
		         	$vendorDetails = [];

				    // Find the details for the current group
				    foreach ($details as $detail) {
				        if ($detail['vendor_no'] === $vendor) {
				            $vendorDetails[] = $detail;
				        }
				    }

		     		if (!empty($vendorDetails))
		     		{
			     		$vendor_name = $this->Acct_mod->get_vendor_name($vendor);
			     		//echo($vendor);die();
			     		$vendors_name = (count($vendor_name)>0) ? $vendor_name[0]['Name'] : 'No Vendor';
			     		$tbl .= "<tr>";
			     		//$tbl .= "<td>" . $vendor . "</td>";
			     		//$tbl .= "<td>" . $vendors_name . "</td>";
			     		$tbl .= "<td style='position: sticky; left: 0; background-color: #fff;'>" . $vendors_name . ' - ' . $vendor . "</td>";


			     		foreach ($month_name as $month)
			     		{
			     			foreach ($year as $y) {
			     				$total = '0.00';
			     				$total_qty = '0';
			     				foreach ($vendorDetails as $detail) {
			     					if ($detail['month'] == $month['number'] && $detail['year'] == $y) {
			     						$total = abs($detail['total']);
			     						$total = number_format($total, 2, '.', ',');

			     						$total_qty = abs($detail['total_quantity']);
	                                   	$total_qty = round($total_qty, 0); 
										$total_qty = intval($total_qty); // Convert to integer to remove decimal places
										$total_qty = number_format($total_qty);
			     						break;
			     					}
			     				}
			     				// $tot   = str_replace(',', '', $total);
								// $totalSum += (float)$tot;
			     				// $tbl .= "<td>₱ " . $total . "</td>";

			     				// $final_total_arr_per_store[$counter] += $tot;
								// $counter ++;

								$tot   = str_replace(',', '', $total);
								$totalSum += (float)$tot;
			     				$tbl .= "<td>₱ " . $total . "</td>";

			     				$tot_qty       = str_replace(',', '', $total_qty);
	                     		$totalSumQ += (float)$tot_qty;
	                     		$tbl .= "<td>" .$total_qty . "</td>";

				                $final_total_arr_per_store[$counter] += $tot; 
			                    //$over_all_final_total_arr_per_store[$counter] += $tot; 
								$counter ++;

								$final_total_arr_per_store2[$counter2] += $tot_qty; 
			                    //$over_all_final_total_arr_per_store2[$counter2] += $tot_qty; 
								$counter2 ++;
			     			}
			     		}

			     		$tbl .= '</tr>';
			     	}
	     		}
	     		$tbl .= '
	    			  <tfoot>
					    <tr style="color: white;">
					      <th style="position: sticky; left: 0; background: darkcyan;">Total</th>';
					        
	                        $max_length = max(count($final_total_arr_per_store), count($final_total_arr_per_store2));

							for ($a = 0; $a < $max_length; $a++) {
							    if ($a < count($final_total_arr_per_store)) {
							        $tbl .= '<th>₱' . number_format($final_total_arr_per_store[$a], 2, '.', ',') . '</th>';
							    } else {
							        $tbl .= '<th></th>'; // Add an empty cell if the first array doesn't have a value for this iteration
							    }

							    if ($a < count($final_total_arr_per_store2)) {
							        $tbl .= '<th>' . number_format($final_total_arr_per_store2[$a]) . '</th>';
							    } else {
							        $tbl .= '<th></th>'; // Add an empty cell if the second array doesn't have a value for this iteration
							    }
							}
	    		$tbl .= '		</tr>
		                
					  </tfoot>	

					            ';		
	         	$tbl .= '</table>';
	          	$tbl .= '<script>';
				$tbl .= '$(document).ready(function() {';
				$tbl .= 'console.log("Initializing DataTable...");';
				$tbl .= '$("#payments_table_sales").DataTable({ scrollX: true });';
				$tbl .= '});';
				$tbl .= '</script>';
	            echo $tbl;
	        }
	   	}
	   	else{
	   		if($store_no == 'Select_all_store') {

	        	$vendors = array_unique(array_column($details, 'vendor_no')); // Get unique vendors from the details array
	        	$stores = array_unique(array_column($details, 'store'));

				$cat = ($category == 'dept') ? "Department" : "Group";
				
				$dept_name 	= $this->Acct_mod->get_dept_name($code);
				$group_name = $this->Acct_mod->get_group_name($code);
				$store_name = $this->Acct_mod->get_store_name($store_no);
				
				$cat_name = ($category == 'dept') ? $dept_name[0]['dept_name'] : $group_name[0]['group_name'];

               	$index = 0;
               	//var_dump($stores);

               	$over_all_final_total_arr = array();
				sort($year);

				foreach ($month_name as $month) 
				{
					foreach ($year as $y) 
					{
						array_push($over_all_final_total_arr,0);
					}

				}

				foreach ($stores as $store){
		        	$final_total_arr =  array();
					$row_total = 0;

					foreach ($month_name as $month) 
				 	{
			       		foreach ($year as $y) {
			        		$row_total +=1;
			        		array_push($final_total_arr,0);
			       		}
				 	}
				 	header("content-type: application/vnd.ms-excel; charset=utf-8");
		            header("Content-Disposition: attachment; filename= Monthly Sales Report per ".$cat." per Supplier.xls");
			     	$tbl  = '<table border="1" class="table table-bordered table-responsive" id="view_table_qty_vendor_'.$index.'" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b;">';
		               	
		      		$tbl .= '<thead style="text-align: center;color:white;">';

					sort($year);

					$tbl .= '<tr>';
						//$tbl .= "<th style='position: sticky; left: 0; background-color: #033e5b; color: white; padding-right: 156px;'>Department</th>";
					$tbl .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; padding-right: 156px;">Vendor</th>';
					foreach ($month_name as $month) {
				        $tbl .= "<th style='text-align: center; color: white;' colspan=".count($year).">".$month['month']."</th>";
				    }
				    $tbl .= '</tr>';
				    // 
				    $tbl .= '<tr>';
				    foreach ($month_name as $month) {
		      	        for ($a = 0; $a < count($year); $a++) {
		      	            $tbl .= '<th style="color: white;">'.$year[$a].'</th>';
		      	        }
		      	    }
				    $tbl .= '</tr>';
		      	    $tbl .= '</thead>';

			     	foreach ($vendors as $vendor)
		     		{

				     	$counter = 0;
			         	$vendorDetails = [];

					    // Find the details for the current group
					    foreach ($details as $detail) {
					        if ($detail['store'] === $store && $detail['vendor_no'] === $vendor) {
					            $vendorDetails[] = $detail;
					        }
					    }

			     		if (!empty($vendorDetails))
			     		{
				     		$vendor_name = $this->Acct_mod->get_vendor_name($vendor);
				     		//echo($vendor);die();
				     		$vendors_name = (count($vendor_name)>0) ? $vendor_name[0]['Name'] : 'No Vendor';
				     		$tbl .= "<tr>";
				     		//$tbl .= "<td>" . $vendor . "</td>";
				     		//$tbl .= "<td>" . $vendors_name . "</td>";
				     		$tbl .= "<td style='position: sticky; left: 0; background-color: #fff;'>" . $vendors_name . ' - ' . $vendor . "</td>";


				     		foreach ($month_name as $month)
				     		{
				     			foreach ($year as $y) {
				     				$total = '0';
				     				foreach ($vendorDetails as $detail) {
				     					if ($detail['month'] == $month['number'] && $detail['year'] == $y) {
				     						$total = abs($detail['total_quantity']);
				     						//$total = number_format($total, 2, '.', ',');
				     						$total = round($total, 0); 
											$total = intval($total); // Convert to integer to remove decimal places
											$total = number_format($total, 0, '', ','); // Format as comma-separated number without decimal places
				     						break;
				     					}
				     				}
				     				$tot = str_replace(',', '', $total);
				                    $totalSum_ += (float)$tot;
				                    $totalSum  = round($totalSum_);
				     				$tbl .= "<td style='background-color:white; color: black'>" .$total . "</td>";

				     				$final_total_arr[$counter] += $tot;
				     				$over_all_final_total_arr[$counter] += $tot;
									$counter ++;
				     			}
				     		}

				     		$tbl .= '</tr>';
				     	}
		     		}
			     	$store_name = $this->Acct_mod->get_store_name($store);
						 
	              	$tbl .='<div class="responsive-div_top" style="margin-top: 1px; margin-bottom: 10px;"><div class="line-separator"></div></div>';
	              	$tbl .= "<h4>Monthly Quantity Report per ".$cat." per Supplier => Store Name: ".$store_name[0]['nav_store_val']."<h4>";
		    		$tbl .= "<h4>".$cat." Name: ".$code." - ".$cat_name."<h4>";

				    $tbl .= '
				    			  <tfoot>
								    <tr style="color: white;">
								      <th style="position: sticky; left: 0; background: darkcyan;">Total</th>';
								        
                                        for($a=0;$a<count($final_total_arr);$a++)
                                        {            
                                            
                                            $tbl .= '<th style="background: darkcyan;"> '.number_format($final_total_arr[$a]).'</th>';
                                        } 
					                			      
					$tbl .= '			    </tr>
								  </tfoot>	

						            ';		
		         	$tbl .= '</table>';
		         	$tbl .= '<script>';
					$tbl .= '$(document).ready(function() {';
					$tbl .= 'console.log("Initializing DataTable...");';
					$tbl .= '$("#view_table_qty_vendor_'.$index.'").DataTable({ scrollX: true });';
					$tbl .= '});';
					$tbl .= '</script>';
		         	echo $tbl;
		         	$index++;
		        } // end foreach store

		        $tbl2 = '<table border="1" class="table table-bordered table-responsive" id="view_table_qty_vendor_total" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; width: auto; table-layout: auto;">';
			    $tbl2 .= '<thead style="text-align: center;color:white;">';
         	    $tbl2 .= '<tr>';
         	   	$tbl2 .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; padding-right: 156px;">Vendor</th>';
			    sort($year);
					foreach ($month_name as $month) {
				        $tbl2 .= "<th style='text-align: center; color: white;' colspan=".count($year).">".$month['month']."</th>";
				    }
				    $tbl2 .= '</tr>';
				    // 
				    $tbl2 .= '<tr>';
				    foreach ($month_name as $month) {
	          	        for ($a = 0; $a < count($year); $a++) {
	          	            $tbl2 .= '<th style="color: white;">'.$year[$a].'</th>';
	          	        }
	          	    }
			    $tbl2 .= '</tr>';
			    $tbl2 .= '</thead>';

			    $tbl2 .= '
				           
				                <tr style="color: white;">
							      <td style="position: sticky; left: 0; background: darkcyan;">Grand Total</td>';
							        
                                    for($a=0;$a<count($over_all_final_total_arr);$a++)
                                    {            
                                        
                                        $tbl2 .= '<td style="background-color: white; color: black;">'.number_format($over_all_final_total_arr[$a]).'</td>';
                                    } 
				                			      

					$tbl2 .= '</tr>';
			    $tbl2 .= '</table>';
			    $tbl2 .= '<script>';
			    $tbl2 .= '$("#view_table_qty_vendor_total").DataTable({ scrollX: true,  lengthChange: false,searching: false,info: false});';
			    $tbl2 .= '</script>';

			    echo $tbl2;     
	        } // end for all store
	        else{
		     	$vendors = array_unique(array_column($details, 'vendor_no')); // Get unique vendors from the details array

		     	$cat = ($category == 'dept') ? "Department" : "Group";
				
				$dept_name = $this->Acct_mod->get_dept_name($code);
				$group_name = $this->Acct_mod->get_group_name($code);
				$store_name = $this->Acct_mod->get_store_name($store_no);

				$cat_name = ($category == 'dept') ? $dept_name[0]['dept_name'] : $group_name[0]['group_name'];

				$final_total_arr_per_store =  array();
				$row_total = 0;

				foreach ($month_name as $month) 
			 	{
		       		foreach ($year as $y) {
		        		$row_total +=1;
		        		array_push($final_total_arr_per_store,0);
		       		}
			 	}

		     	$tbl  = '<table  class="table table-bordered table-responsive" id="payments_table_qty" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b;">';
	           	$tbl .= '<thead style="text-align: center;color:white;">';
	           	//$tbl .= "<h4 >Monthly Quantity Report per Department => Store Name:".$store_name[0]['nav_store_val']."<h4>";

	           	$tbl .= "<h4>Monthly Quantity Report per ".$cat." per Supplier => Store Name:".$store_name[0]['nav_store_val']."<h4>";
		     	$tbl .= "<h4>".$cat." Name: ".$code." - ".$cat_name."<h4>";
	        	
	           	sort($year);

				$tbl .= '<tr>';
					
				$tbl .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; padding-right: 156px;">Vendor</th>';
				foreach ($month_name as $month) {
			        $tbl .= "<th style='text-align: center;' colspan=".count($year).">".$month['month']."</th>";
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
		     	
		     	foreach ($vendors as $vendor)
		     	{
			       	$counter = 0;
		         	$vendorDetails = [];

				    // Find the details for the current group
				    foreach ($details as $detail) {
				        if ($detail['vendor_no'] === $vendor) {
				            $vendorDetails[] = $detail;
				        }
				    }

			     	if (!empty($vendorDetails))
			     	{
			     		$vendor_name = $this->Acct_mod->get_vendor_name($vendor);
			     		$vendors_name = (count($vendor_name)>0) ? $vendor_name[0]['Name'] : "No Vendor";
			     		$tbl .= "<tr>";
			     		//$tbl .= "<td>" . $vendor . "</td>";
			     		$tbl .= "<td style='position: sticky; left: 0; background-color: #fff;'>" . $vendors_name . ' - ' . $vendor . "</td>";

			     		foreach ($month_name as $month)
			     		{
			     			foreach ($year as $y) {
			     				$total = '0';
			     				foreach ($vendorDetails as $detail) {
			     					if ($detail['month'] == $month['number'] && $detail['year'] == $y) {
			     						$total = abs($detail['total_quantity']);
			     						//$total = number_format($total, 2, '.', ',');
			     						$total = round($total, 0); 
										$total = intval($total); // Convert to integer to remove decimal places
										$total = number_format($total, 0, '', ','); // Format as comma-separated number without decimal places
			     						break;
			     					}
			     				}
			     				$tot = str_replace(',', '', $total);
			                    $totalSum_ += (float)$tot;
			                    $totalSum  = round($totalSum_);
			     				$tbl .= "<td>" .$total . "</td>";

			     				$final_total_arr_per_store[$counter] += $tot;
								$counter ++;
			     			}
			     		}

			     		$tbl .= '</tr>';
			     	}
		     	}
		     	$tbl .= '<tfoot>
				    	<tr style="color: white;">
				      	<th style="position: sticky; left: 0; background: darkcyan;">Total</th>';
				        
	                  	for($a=0;$a<count($final_total_arr_per_store);$a++)
	                  	{            
	                        
	                   		$tbl .= '<th style="color:white; background: darkcyan;">'.number_format($final_total_arr_per_store[$a]).'</th>';
	                  	} 
	                			      
		  		$tbl .= '</tr>
				  	</tfoot>';		
	     		$tbl .= '</table>';
		        $tbl .= '<script>';
		        $tbl .= '$("#payments_table_qty").DataTable({ scrollX: true })';
		        $tbl .='</script>';
		        
	         	echo $tbl;
	        }
	   	}
	}

}
?>