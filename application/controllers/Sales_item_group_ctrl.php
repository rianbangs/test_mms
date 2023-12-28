<?php
	class Sales_item_group_ctrl extends CI_Controller
	{
		function __construct()
		{
			parent::__construct();
			$this->load->library('session');
		        //$this->load->model('Sales_monitoring_mod');
			$this->load->model('Acct_mod');
			$this->load->model('Po_mod');

		}

		// function get monthly and yearly report
		function get_yearly_monthly_report()
		{
			$memory_limit = ini_get('memory_limit');
			ini_set('memory_limit',-1);
			ini_set('max_execution_time', 0);

			$tot            = '';
	     	$totalSum       = '0.00';
	     	$totalSumQ       = '0.00';
	     	$totalSum_      = '0.00';
			$tbl            = '';
			$range          = $_POST['range'];
			$store_no       = $_POST['store_no'];
			$year           = $_POST['year'];
			$report_type    = $_POST['report_type'];




			// get 3 previous year :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
			$original_year = $year;
			$sub_year      = 2;
			$pre_year      = $original_year - $sub_year;

			$details   = $this->Acct_mod->get_monthly_report_group_mod($store_no,$year,strval($pre_year));     
			$year          = array($original_year,$original_year-1,$pre_year);        	
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
			$item_group      = array();
			// $details       = array();
			$month         = array('January','February','March','April','May','June','July','August','September','October','November','December');
			$total_ 		 = array();
			

			if($report_type == 'sales')
			{
		        if($store_no == 'Select_all_store') {

		        	$groups = array_unique(array_column($details, 'item_group')); // Get unique groups from the details array
		        	$stores = array_unique(array_column($details, 'store'));

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
		               	header("Content-Disposition: attachment; filename= Monthly Sales Report per Group.xls");
		               	$tbl = '<table border="1" class="table table-responsive" id="view_table_sales_'.$index.'" style="width: auto; table-layout: auto;">';
		               	
		            	$tbl .= '<thead style="text-align: center;color:white;">';
			         	// Generate table headers
			      		//$tbl .= "<h4>Monthly Sales Report per Group => Store Name:".$store_name[0]['nav_store_val']."<h4>";
				     	//$tbl .= "<h2>Monthly Sales Report per Group => Store Name:".$store_no."<h2>";

				     	// Generate table headers

						sort($year);

						$tbl .= '<tr>';
							//$tbl .= "<th style='position: sticky; left: 0;  color: white; padding-right: 156px;'>group</th>";
						$tbl .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; padding-right: 156px;">Group</th>';
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

				     	foreach ($groups as $group)
				     	{
				       		$counter = 0;
				         	
				     		$groupDetails = [];

						    // Find the details for the current group
						    foreach ($details as $detail) {
						        if ($detail['store'] === $store && $detail['item_group'] === $group) {
						            $groupDetails[] = $detail;
						        }
						    }
		
				     		if (!empty($groupDetails)) {
						        $group_name = $this->Acct_mod->get_group_name($group);
						        $groups_name = (count($group_name) > 0) ? $group_name[0]['group_name'] : 'No Group Name';
						        $tbl .= "<tr>";
						        $tbl .= "<td style='position: sticky; left: 0; background-color: #fff;'>{$group} - {$groups_name}</td>";

						        foreach ($month_name as $month) {
						            foreach ($year as $y) {
						                $total = '0.00';
						                foreach ($groupDetails as $detail) {
						                    if ($detail['month'] == $month['number'] && $detail['year'] == $y) {
						                        $total = abs($detail['total']);
						                        $total = number_format($total, 2, '.', ',');
						                        break;
						                    }
						                }
						                $tot = str_replace(',', '', $total);
						                $totalSum += (float)$tot;
						                //$tbl .= "<td style='text-align:left; background-color: white; color: black; '>₱ {$total}</td>";
						                $tbl .= "<td style='text-align:left; background-color: white; color: black; '>&#8369; " . $total . "</td>";

						                $final_total_arr[$counter] += $tot;
						                $over_all_final_total_arr[$counter] += $tot;
						                $counter++;
						            }
						        }

						        $tbl .= '</tr>';
						    }
				     	}
				     	$store_name = $this->Acct_mod->get_store_name($store);
							 
		              	$tbl .='<div class="responsive-div_top" style="margin-top: 1px; margin-bottom: 10px;"><div class="line-separator"></div></div>';
		              	//$tbl .= "<h4>Monthly Sales Report per group => Store Name:".$store_name[0]['nav_store_val']."<h4>";
					    $tbl .='<h3 style="font-size: 26px;">Monthly Sales Report per Group => Store Name: ' . $store_name[0]['nav_store_val'] . '</h3>';
					    // $tbl .='<h3 style="font-size: 17px;">Total Sales: &#8369;' . number_format($totalSum, 2, '.', ',') . '</h3>';

					    $tbl .= '
					    		<tfoot>
									<tr style="color: white;">
									    <th style="position: sticky; left: 0; background: darkcyan;">Total</th>';
									        
	                                    for($a=0;$a<count($final_total_arr);$a++)
	                                    {            
	                                        $tbl .= '<th style="background: darkcyan; color: white;">₱' . number_format($final_total_arr[$a], 2, '.', ',') . '</th>';

											
	                                    }	      
						$tbl .= '	</tr>
								</tfoot>';
						$tbl .= '</table>';
			         	// $tbl .= '<script>';
						// $tbl .= '$(document).ready(function() {';
						// $tbl .= 'console.log("Initializing DataTable...");';
						// $tbl .= '$("#view_table_sales_group_'.$index.'").DataTable({ scrollX: true });';
						// $tbl .= '});';
						// $tbl .= '</script>';
			         	echo $tbl;
			         	$index++;
			        } // end foreach store

			        $tbl2 = '<table border="1" class="table table-bordered table-responsive" id="view_table_sales_group_total" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; width: auto; table-layout: auto;">';
				    $tbl2 .= '<thead style="text-align: center;color:white;">';
	         	    $tbl2 .= '<tr>';
	         	   	$tbl2 .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; padding-right: 156px;">Group</th>';
				    sort($year);
						foreach ($month_name as $month) {
					        $tbl2 .= "<th style='text-align: center;' colspan=".count($year).">".$month['month']."</th>";
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

				    $tbl2 .= '
					           
					                <tr style="color: white;">
								      <td style="position: sticky; left: 0; background: darkcyan;">Grand Total</td>';
								        
	                                    for($a=0;$a<count($over_all_final_total_arr);$a++)
	                                    {            
	                                        
	                                        $tbl2 .= '<td style="color: black;">₱'.number_format($over_all_final_total_arr[$a], 2, '.', ',').'</td>';
	                                    } 
					                			      

						$tbl2 .= '</tr>';
				    $tbl2 .= '</table>';
				    $tbl2 .= '<script>';
				    $tbl2 .= '$("#view_table_sales_group_total").DataTable({ scrollX: true,  lengthChange: false,searching: false,info: false});';
				    $tbl2 .= '</script>';

				    echo $tbl2;     
		        }
		        else{
		        	$groups = array_unique(array_column($details, 'item_group')); // Get unique groups from the details array

			     	$store_name = $this->Acct_mod->get_store_name($store_no);
			     	$final_total_arr_per_store =  array();
					$row_total = 0;

					foreach ($month_name as $month) 
				 	{
			       		foreach ($year as $y) {
			        		$row_total +=1;
			        		array_push($final_total_arr_per_store,0);
			       		}
				 	}
			     	header("content-type: application/vnd.ms-excel");
			     	header("Content-Disposition: attachment; filename= Monthly Sales Report per Group.xls");

			     	$tbl = '<table border="1">';
			     	$tbl .= "<h2>Monthly Sales Report per Group => Store Name:".$store_no."<h2>";

			     	// Generate table headers

			     	$tbl .= "<tr>";
		        	$tbl .= "<th style='font-weight: bold; text-align: center;'>Monthly Sales Report per Group => Store Name: ".$store_name[0]['nav_store_val']."</th>";
		        	$tbl .= "</tr>";
		     
					$tbl .= "<tr>";
		        	$tbl .= "<th></th>";
		        	$tbl .= "</tr>";
			     	$tbl .= "<tr>";
			     	$tbl .= "<th>Code</th>";
			     	$tbl .= "<th>Group Name</th>";
			     	foreach ($month_name as $month) 
			     	{
			     		$tbl .= "<th colspan=".count($year).">".$month['month']."</th>";
			     	}

			     	$tbl .= '</tr>';

			     	sort($year);

				    // Add sorted years as headers
			     	$tbl .= "<tr>";
			     	$tbl .= "<td></td>";
			    	$tbl .= "<td></td>";
			     	foreach ($month_name as $month) {
				     	for ($a = 0; $a < count($year); $a++) {
				     		$tbl .= '<th>'.$year[$a].'</th>';
				     	}
			     	}
			     	$tbl .= '</tr>';

			     	foreach ($groups as $group)
			     	{
			       		$counter = 0;
			         	$groupDetails = [];
					    // Find the details for the current group
					    foreach ($details as $detail) {
					        if ($detail['item_group'] === $group) {
					            $groupDetails[] = $detail;
					        }
					    }


			     		if (!empty($groupDetails))
				     	{
				     		$group_name = $this->Acct_mod->get_group_name($group);
				     		$groups_name = (count($group_name)>0) ? $group_name[0]['group_name'] : 'No Group Name';
				     		$groups_code = (count($group_name)>0) ? $group : 'No Group Code';
				     		$tbl .= "<tr>";
				     		$tbl .= "<td>" . $groups_code . "</td>";
				     		$tbl .= "<td>" . $groups_name . "</td>";

				 
				     		foreach ($month_name as $month)
				     		{
				     			foreach ($year as $y) {
				     				$total = '0.00';
				     				foreach ($groupDetails as $detail) {
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
			     	$tbl .= "<tr>";
		        	$tbl .= "<td colspan='25'></td>";
		        	$tbl .= "</tr>";
		         	$tbl .= '<tfoot>
						    	<tr style="color: black;">
						      	<th style="background: darkcyan;">Total</th>
						        <th style="background: darkcyan;"></th>';
		                      	for($a=0;$a<count($final_total_arr_per_store);$a++)
		                      	{            
		                            
		                       		$tbl .= '<th style="color:black; ">₱'.number_format($final_total_arr_per_store[$a], 2, '.', ',').'</th>';
		                      	} 
			                			      
		  	  		$tbl .= '</tr>
						  	</tfoot>';
			     	$tbl .= '</table>';
			     	echo $tbl;
		        }
		    }       
		    elseif($report_type == 'both')
			{
		        if($store_no == 'Select_all_store') {

		        	$groups = array_unique(array_column($details, 'item_group')); // Get unique groups from the details array
		        	$stores = array_unique(array_column($details, 'store'));

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
						
			      		header("content-type: application/vnd.ms-excel");
		               	header("Content-Disposition: attachment; filename= Monthly Sales and Quantity Report per Group.xls");
		               	$tbl = '<table border="1" class="table table-responsive" id="view_table_sales_qty_group_'.$index.'" style="width: auto; table-layout: auto;">';
		               	
		            	$tbl .= '<thead style="text-align: center;color:white;">';
			         	// Generate table headers
			      		//$tbl .= "<h4>Monthly Sales Report per Group => Store Name:".$store_name[0]['nav_store_val']."<h4>";
				     	//$tbl .= "<h2>Monthly Sales Report per Group => Store Name:".$store_no."<h2>";

				     	// Generate table headers

						sort($year);
		          	    $tbl .= '<tr>';
						//$tbl .= "<th style='position: sticky; left: 0; background-color: #033e5b; color: white; padding-right: 156px;'>group</th>";
						$tbl .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; padding-right: 156px;">Group</th>';
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

				     	foreach ($groups as $group)
				     	{
				       		$counter = 0;
				       		$counter2 = 0;
				         	// Find the details for the current group
				     		// $groupDetails = array_filter($details, function ($detail) use ($store,$group) {
				     		// 	return $detail['store'] === $store && $detail['item_group'] === $group ;
				     		// });

				     		$groupDetails = [];

						    // Find the details for the current group
						    foreach ($details as $detail) {
						        if ($detail['store'] === $store && $detail['item_group'] === $group) {
						            $groupDetails[] = $detail;
						        }
						    }
		
				     		if (!empty($groupDetails)) {
						        $group_name = $this->Acct_mod->get_group_name($group);
						        $groups_name = (count($group_name) > 0) ? $group_name[0]['group_name'] : 'No Group Name';
						        $tbl .= "<tr>";
						        $tbl .= "<td style='position: sticky; left: 0; background-color: #fff;'>{$group} - {$groups_name}</td>";

						        foreach ($month_name as $month) {
						            foreach ($year as $y) {
						                $total = '0.00';
						                $total_qty  = '0';
						                foreach ($groupDetails as $detail) {
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
						                $tbl .= "<td>₱ {$total}</td>";

						                $tot_qty       = str_replace(',', '', $total_qty);
	                             		$totalSumQ += (float)$tot_qty;
	                             		$tbl .= "<td>" .$total_qty . "</td>";

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
		              	//$tbl .= "<h4>Monthly Sales Report per group => Store Name:".$store_name[0]['nav_store_val']."<h4>";
					    $tbl .='<h3 style="font-size: 26px;">Monthly Sales Report per Group => Store Name: ' . $store_name[0]['nav_store_val'] . '</h3>';
					    // $tbl .='<h3 style="font-size: 17px;">Total Sales: &#8369;' . number_format($totalSum, 2, '.', ',') . '</h3>';

					    $tbl .= '
			    			  <tfoot>
							    <tr style="color: white;">
							      <th style="position: sticky; left: 0; background: darkcyan;">Total</th>';
							        
	                                $max_length = max(count($final_total_arr), count($final_total_arr2));

									for ($a = 0; $a < $max_length; $a++) {
									    if ($a < count($final_total_arr)) {
									        $tbl .= '<th>₱' . number_format($final_total_arr[$a], 2, '.', ',') . '</th>';
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
						$tbl .= '$("#view_table_sales_qty_group_'.$index.'").DataTable({ scrollX: true });';
						$tbl .= '});';
						$tbl .= '</script>';
			         	echo $tbl;
			         	$index++;
			        } // end foreach store

			        $tbl2 = '<table border="1" class="table table-bordered table-responsive" id="view_table_sales_qty_group_total" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; width: auto; table-layout: auto;">';
				    $tbl2 .= '<thead style="text-align: center;color:white;">';
	         	    sort($year);
	          	    $tbl2 .= '<tr>';
					//$tbl2 .= "<th style='position: sticky; left: 0; background-color: #033e5b; color: white; padding-right: 156px;'>group</th>";
					$tbl2 .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; padding-right: 156px;">Group</th>';
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
	                        
	                        $tbl2 .= '<td style="color: white; background: darkcyan; text-align: right;">₱ '.number_format($over_all_final_total_arr[$a], 2, '.', ',').'</td>';
	                        $tbl2 .= '<td style="color: white; background: darkcyan; text-align: right;">'.number_format($over_all_final_total_arr2[$a]).'</td>';
	                    } 
				                			      

					$tbl2 .= '</tr>';
				    $tbl2 .= '</table>';
				    $tbl2 .= '<script>';
				    $tbl2 .= '$("#view_table_sales_qty_group_total").DataTable({ scrollX: true,  lengthChange: false,searching: false,info: false});';
				    $tbl2 .= '</script>';

				    echo $tbl2;     
		        } // end for all store
		        else{ // condition for per store
		        	$groups = array_unique(array_column($details, 'item_group')); // Get unique groups from the details array

			     	$store_name = $this->Acct_mod->get_store_name($store_no);

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

			     	$tbl  = '<table  class="table table-bordered table-responsive" id="payments_table_sales_qty_group" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b;">';
		               	
		      		$tbl .= '<thead style="text-align: center;color:white;">';
		         	// Generate table headers
		      		//$tbl .= "<h4>Monthly Sales Report per Group => Store Name:".$store_name[0]['nav_store_val']."<h4>";
			     	$tbl .= "<h2>Monthly Sales Report per Group => Store Name:".$store_no."<h2>";

			     	sort($year);
	          	    $tbl .= '<tr>';
					//$tbl .= "<th style='position: sticky; left: 0; background-color: #033e5b; color: white; padding-right: 156px;'>Department</th>";
					$tbl .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; padding-right: 156px;">Department</th>';
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

			     	foreach ($groups as $group)
			     	{
			       		$counter = 0;
			       		$counter2 = 0;
			         	$groupDetails = [];

					    // Find the details for the current group
					    foreach ($details as $detail) {
					        if ($detail['item_group'] === $group) {
					            $groupDetails[] = $detail;
					        }
					    }


			     		if (!empty($groupDetails))
				     	{
				     		$group_name = $this->Acct_mod->get_group_name($group);
				     		$groups_name = (count($group_name)>0) ? $group_name[0]['group_name'] : 'No Group Name';
				     		$groups_code = (count($group_name)>0) ? $group : 'No Group Code';
				     		$tbl .= "<tr>";
				     		$tbl .= "<td style='position: sticky; left: 0; background-color: #fff;'>" . $group . ' - ' . $groups_name . "</td>";
				     		//$tbl .= "<td>" . $groups_name . "</td>";

				 
				     		foreach ($month_name as $month)
				     		{
				     			foreach ($year as $y) {
				     				$total = '0.00';
				     				$total_qty = '0';
				     				foreach ($groupDetails as $detail) {
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
			     	//$tbl .= '<h3 hidden>Total Sales: ₱'.number_format($totalSum, 2, '.', ',').'</h3>';
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
					$tbl .= '$("#payments_table_sales_qty_group").DataTable({ scrollX: true });';
					$tbl .= '});';
					$tbl .= '</script>';
		         	echo $tbl;
		        } // end for per store      
	   		} // end for both sales and queantity

	   		// for quantity
	   		else{

	   			if($store_no == 'Select_all_store') {

		        	$groups = array_unique(array_column($details, 'item_group')); // Get unique groups from the details array
		        	$stores = array_unique(array_column($details, 'store'));

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
	     				header("Content-Disposition: attachment; filename= Monthly Quantity Report per Group.xls");
				     	//$tbl  = '<table border="1"  class="table table-bordered table-responsive" id="view_table_qty_group_'.$index.'"  width: 100%;color: #0f0b0b;">';
			            $tbl  = '<table border="1" class="table table-responsive" id="view_table_qty_group_'.$index.'" style="width: auto; table-layout: auto;">';   	
			      		$tbl .= '<thead style="text-align: center;color:white;">';
			         	// Generate table headers
			      		//$tbl .= "<h4>Monthly Sales Report per Group => Store Name:".$store_name[0]['nav_store_val']."<h4>";
				     	//$tbl .= "<h2>Monthly Sales Report per Group => Store Name:".$store_no."<h2>";

				     	// Generate table headers

						sort($year);

						$tbl .= '<tr>';
							//$tbl .= "<th style='position: sticky; left: 0; background-color: #033e5b; color: white; padding-right: 156px;'>Department</th>";
						
					    $tbl .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; padding-right: 156px;">Group</th>';
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

				     	foreach ($groups as $group)
				     	{
				       		$counter = 0;
				         	// Find the details for the current group
				     		// $groupDetails = array_filter($details, function ($detail) use ($store,$group) {
				     		// 	return $detail['store'] === $store && $detail['item_group'] === $group ;
				     		// });

				     		$groupDetails = [];

						    // Find the details for the current group
						    foreach ($details as $detail) {
						        if ($detail['store'] === $store && $detail['item_group'] === $group) {
						            $groupDetails[] = $detail;
						        }
						    }
		
				     		if (!empty($groupDetails)) {
						        $group_name = $this->Acct_mod->get_group_name($group);
						        $groups_name = (count($group_name) > 0) ? $group_name[0]['group_name'] : 'No Group Name';
						        $tbl .= "<tr>";
						        $tbl .= "<td style='position: sticky; left: 0; background-color: #fff;'>{$group} - {$groups_name}</td>";

						        foreach ($month_name as $month) {
						            foreach ($year as $y) {
						                $total = '0';
						                foreach ($groupDetails as $detail) {
						                    if ($detail['month'] == $month['number'] && $detail['year'] == $y) {
						                        
						                        $total = abs($detail['total_quantity']);
					     						$total = round($total, 0); 
												$total = intval($total); // Convert to integer to remove decimal places
												$total = number_format($total, 0, '', ','); // Format as comma-separated number without decimal places
					     						break;
						                    }
						                }
						                
						                $tot = str_replace(',', '', $total);
			                     		$totalSumQ += (float)$tot;
			                     		$totalSum  = round($totalSumQ);
					     				//$tbl .= "<td style='text-align:right;'>" .$total . "</td>";
					     				$tbl .= "<td style='text-align:left; background-color: white; color: black; '>&#8369; " . $total . "</td>";

						                $final_total_arr[$counter] += $tot;
						                $over_all_final_total_arr[$counter] += $tot;
						                $counter++;
						            }
						        }

						        $tbl .= '</tr>';
						    }
				     	}
				     	$store_name = $this->Acct_mod->get_store_name($store);
							 
		              	$tbl .='<div class="responsive-div_top" style="margin-top: 1px; margin-bottom: 10px;"><div class="line-separator"></div></div>';
		              	//$tbl .= "<h4>Monthly Sales Report per Department => Store Name:".$store_name[0]['nav_store_val']."<h4>";
					    $tbl .='<h3 style="font-size: 26px;">Monthly Sales Report per Group => Store Name: ' . $store_name[0]['nav_store_val'] . '</h3>';
					    // $tbl .='<h3 style="font-size: 17px;">Total Sales: &#8369;' . number_format($totalSum, 2, '.', ',') . '</h3>';

					    $tbl .= '
					    		<tfoot>
								    <tr style="color: white;">
								      	<th style="position: sticky; left: 0; background: darkcyan;">Total</th>';
								        
	                                    for($a=0;$a<count($final_total_arr);$a++)
	                                    {            
	                                        
	                                        //$tbl .= '<th style="background: darkcyan;">&#8369; '.number_format($final_total_arr[$a], 2, '.', ',').'</th>';
	                                        $tbl .= '<th style="color:white; background: darkcyan;">'.number_format($final_total_arr[$a]).'</th>';
	                                    } 
						                			      
						$tbl .= '</tr>
								</tfoot>';		
			         	$tbl .= '</table>';
			         	$tbl .= '<script>';
						$tbl .= '$(document).ready(function() {';
						$tbl .= 'console.log("Initializing DataTable...");';
						$tbl .= '$("#view_table_qty_group_'.$index.'").DataTable({ scrollX: true });';
						$tbl .= '});';
						$tbl .= '</script>';
			         	echo $tbl;
			         	$index++;
			        } // end foreach store

			        $tbl2 = '<table border="1" class="table table-bordered table-responsive" id="view_table_sales_group_total" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; width: auto; table-layout: auto;">';
				    $tbl2 .= '<thead style="text-align: center;color:white;">';
	         	    $tbl2 .= '<tr>';
	         	   	$tbl2 .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; padding-right: 156px;">Group</th>';
				    sort($year);
						foreach ($month_name as $month) {
					        $tbl2 .= "<th style='text-align: center;' colspan=".count($year).">".$month['month']."</th>";
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

				    $tbl2 .= '
					           
					                <tr style="color: white;">
								      <td style="position: sticky; left: 0; background: darkcyan;">Grand Total</td>';
								        
	                                    for($a=0;$a<count($over_all_final_total_arr);$a++)
	                                    {            
	                                        
	                                        $tbl2 .= '<td style="color: black;">'.number_format($over_all_final_total_arr[$a]).'</td>';
	                                    } 
					                			      

						$tbl2 .= '</tr>';
				    $tbl2 .= '</table>';
				    $tbl2 .= '<script>';
				    $tbl2 .= '$("#view_table_sales_group_total").DataTable({ scrollX: true,  lengthChange: false,searching: false,info: false});';
				    $tbl2 .= '</script>';

				    echo $tbl2;     
		        } // end for all store
		        else{
		     		$groups = array_unique(array_column($details, 'item_group')); // Get unique groups from the details array

		     		$store_name = $this->Acct_mod->get_store_name($store_no);

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
			         	// Generate table headers
			      	// $tbl .= "<h4>Monthly Quantity Report per Group => Store Name:".$store_name[0]['nav_store_val']."<h4>";
			     		$tbl .= "<h2>Monthly Quantity Report per Group => Store Name:".$store_no."<h2>";

			   		sort($year);

					$tbl .= '<tr>';
						//$tbl .= "<th style='position: sticky; left: 0; background-color: #033e5b; color: white; padding-right: 156px;'>Department</th>";
					$tbl .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; padding-right: 156px;">Group</th>';
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

		     		foreach ($groups as $group)
		     		{
		     			$counter = 0;
		     			$groupDetails = [];
					    // Find the details for the current group
					    foreach ($details as $detail) {
					        if ($detail['item_group'] === $group) {
					            $groupDetails[] = $detail;
					        }
					    }

				     	if (!empty($groupDetails))
				     	{
				     		
				     		$group_name = $this->Acct_mod->get_group_name($group);
				     		$groups_name = (count($group_name)>0) ? $group_name[0]['group_name'] : 'No Group Name';
				     		$groups_code = (count($group_name)>0) ? $group : 'No Group Code';
				     		
				     		$tbl .= "<tr>";
		            		//$tbl .= "<td style='position: sticky; left: 0;; background-color: #fff;color: white;background-color: #034160;'>" . $department . "</td>";
		            		$tbl .= "<td style='position: sticky; left: 0; background-color: #fff;'>" . $group . ' - ' . $groups_name . "</td>";

				     		foreach ($month_name as $month)
				     		{
				     			foreach ($year as $y) {
				     				$total = '0';
				     				foreach ($groupDetails as $detail) {
				     					if ($detail['month'] == $month['number'] && $detail['year'] == $y) {
				     						$total = abs($detail['total_quantity']);
				     						$total = round($total, 0); 
											$total = intval($total); // Convert to integer to remove decimal places
											$total = number_format($total, 0, '', ','); // Format as comma-separated number without decimal places
				     						break;
				     					}
				     				}
			     					$tot = str_replace(',', '', $total);
		                     		$totalSumQ += (float)$tot;
		                     		$totalSum  = round($totalSumQ);
				     				$tbl .= "<td style='text-align:right;'>" .$total . "</td>";

				     				$final_total_arr_per_store[$counter] += $tot;
									$counter ++;
				     			}
				     		}

				     		$tbl .= '</tr>';
				     	}
		     		}
		     		// $tbl .= '<h3 hidden >Total Quantity: '.number_format($totalSum).'</h3>';	
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

	 	function view_yearly_monthly_report()
		{
			$memory_limit = ini_get('memory_limit');
			ini_set('memory_limit',-1);
			ini_set('max_execution_time', 0);
			
			$tot            = '';
			$tot_qty        = '';
	     	$totalSum       = '0.00';
	     	$totalSumQ      = '0.00';
			$tbl            = '';
			$range          = $_POST['range'];
			$store_no       = $_POST['store_no'];
			$year           = $_POST['year'];
			$report_type    = $_POST['report_type'];

			// get 3 previous year :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
			$original_year = $year;
			$sub_year      = 2;
			$pre_year      = $original_year - $sub_year;

			$details   = $this->Acct_mod->get_monthly_report_group_mod($store_no,$year,strval($pre_year));
			// var_dump($details);   
			$year          = array($original_year,$original_year-1,$pre_year);           	
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
			//$item_group      = array();
			// $details       = array();
			$month         = array('January','February','March','April','May','June','July','August','September','October','November','December');
			//$store         = array('January','February','March','April','May','June','July','August','September','October','November','December');
			$total_ 		 = array();
			

			if($report_type == 'sales')
			{
		        if($store_no == 'Select_all_store') {

		        	$groups = array_unique(array_column($details, 'item_group')); // Get unique groups from the details array
		        	$stores = array_unique(array_column($details, 'store'));

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

				     	$tbl  = '<table  class="table table-bordered table-responsive" id="view_table_sales_group_'.$index.'" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b;">';
			               	
			      		$tbl .= '<thead style="text-align: center;color:white;">';
			         	// Generate table headers
			      		//$tbl .= "<h4>Monthly Sales Report per Group => Store Name:".$store_name[0]['nav_store_val']."<h4>";
				     	//$tbl .= "<h2>Monthly Sales Report per Group => Store Name:".$store_no."<h2>";

				     	// Generate table headers

						sort($year);

						$tbl .= '<tr>';
							//$tbl .= "<th style='position: sticky; left: 0; background-color: #033e5b; color: white; padding-right: 156px;'>Department</th>";
						$tbl .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; padding-right: 156px;">Group</th>';
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

				     	foreach ($groups as $group)
				     	{
				       		$counter = 0;
				         	// Find the details for the current group
				     		// $groupDetails = array_filter($details, function ($detail) use ($store,$group) {
				     		// 	return $detail['store'] === $store && $detail['item_group'] === $group ;
				     		// });

				     		$groupDetails = [];

						    // Find the details for the current group
						    foreach ($details as $detail) {
						        if ($detail['store'] === $store && $detail['item_group'] === $group) {
						            $groupDetails[] = $detail;
						        }
						    }
		
				     		if (!empty($groupDetails)) {
						        $group_name = $this->Acct_mod->get_group_name($group);
						        $groups_name = (count($group_name) > 0) ? $group_name[0]['group_name'] : 'No Group Name';
						        $tbl .= "<tr>";
						        $tbl .= "<td style='position: sticky; left: 0; background-color: #fff;'>{$group} - {$groups_name}</td>";

						        foreach ($month_name as $month) {
						            foreach ($year as $y) {
						                $total = '0.00';
						                foreach ($groupDetails as $detail) {
						                    if ($detail['month'] == $month['number'] && $detail['year'] == $y) {
						                        $total = abs($detail['total']);
						                        $total = number_format($total, 2, '.', ',');
						                        break;
						                    }
						                }
						                $tot = str_replace(',', '', $total);
						                $totalSum += (float)$tot;
						                $tbl .= "<td>₱ {$total}</td>";

						                $final_total_arr[$counter] += $tot;
						                $over_all_final_total_arr[$counter] += $tot;
						                $counter++;
						            }
						        }

						        $tbl .= '</tr>';
						    }
				     	}
				     	$store_name = $this->Acct_mod->get_store_name($store);
							 
		              	$tbl .='<div class="responsive-div_top" style="margin-top: 1px; margin-bottom: 10px;"><div class="line-separator"></div></div>';
		              	//$tbl .= "<h4>Monthly Sales Report per Department => Store Name:".$store_name[0]['nav_store_val']."<h4>";
					    $tbl .='<h3 style="font-size: 26px;">Monthly Sales Report per Group => Store Name: ' . $store_name[0]['nav_store_val'] . '</h3>';
					    // $tbl .='<h3 style="font-size: 17px;">Total Sales: &#8369;' . number_format($totalSum, 2, '.', ',') . '</h3>';

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
						$tbl .= '$("#view_table_sales_group_'.$index.'").DataTable({ scrollX: true });';
						$tbl .= '});';
						$tbl .= '</script>';
			         	echo $tbl;
			         	$index++;
			        } // end foreach store

			        $tbl2 = '<table border="1" class="table table-bordered table-responsive" id="view_table_sales_group_total" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; width: auto; table-layout: auto;">';
				    $tbl2 .= '<thead style="text-align: center;color:white;">';
	         	    $tbl2 .= '<tr>';
	         	   	$tbl2 .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; padding-right: 156px;">Group</th>';
				    sort($year);
						foreach ($month_name as $month) {
					        $tbl2 .= "<th style='text-align: center;' colspan=".count($year).">".$month['month']."</th>";
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

				    $tbl2 .= '
					           
					                <tr style="color: white;">
								      <td style="position: sticky; left: 0; background: darkcyan;">Grand Total</td>';
								        
	                                    for($a=0;$a<count($over_all_final_total_arr);$a++)
	                                    {            
	                                        
	                                        $tbl2 .= '<td style="color: black;">₱'.number_format($over_all_final_total_arr[$a], 2, '.', ',').'</td>';
	                                    } 
					                			      

						$tbl2 .= '</tr>';
				    $tbl2 .= '</table>';
				    $tbl2 .= '<script>';
				    $tbl2 .= '$("#view_table_sales_group_total").DataTable({ scrollX: true,  lengthChange: false,searching: false,info: false});';
				    $tbl2 .= '</script>';

				    echo $tbl2;     
		        } // end for all store
		        else{ // condition for per store
		        	$groups = array_unique(array_column($details, 'item_group')); // Get unique groups from the details array

			     	$store_name = $this->Acct_mod->get_store_name($store_no);

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
		      		$tbl .= "<h4>Monthly Sales Report per Group => Store Name:".$store_name[0]['nav_store_val']."<h4>";
			     	//$tbl .= "<h2>Monthly Sales Report per Group => Store Name:".$store_no."<h2>";

			     	// Generate table headers

					sort($year);

					$tbl .= '<tr>';
						//$tbl .= "<th style='position: sticky; left: 0; background-color: #033e5b; color: white; padding-right: 156px;'>Department</th>";
					$tbl .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; padding-right: 156px;">Group</th>';
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

			     	foreach ($groups as $group)
			     	{
			       		$counter = 0;
			         	$groupDetails = [];

					    // Find the details for the current group
					    foreach ($details as $detail) {
					        if ($detail['item_group'] === $group) {
					            $groupDetails[] = $detail;
					        }
					    }


			     		if (!empty($groupDetails))
				     	{
				     		$group_name = $this->Acct_mod->get_group_name($group);
				     		$groups_name = (count($group_name)>0) ? $group_name[0]['group_name'] : 'No Group Name';
				     		$groups_code = (count($group_name)>0) ? $group : 'No Group Code';
				     		$tbl .= "<tr>";
				     		$tbl .= "<td style='position: sticky; left: 0; background-color: #fff;'>" . $group . ' - ' . $groups_name . "</td>";
				     		//$tbl .= "<td>" . $groups_name . "</td>";

				 
				     		foreach ($month_name as $month)
				     		{
				     			foreach ($year as $y) {
				     				$total = '0.00';
				     				foreach ($groupDetails as $detail) {
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
			     	//$tbl .= '<h3 hidden>Total Sales: ₱'.number_format($totalSum, 2, '.', ',').'</h3>';
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
		        } // end for per store      
	   		} // end for sales

	   		elseif($report_type == 'both')
			{
		        if($store_no == 'Select_all_store') {

		        	$groups = array_unique(array_column($details, 'item_group')); // Get unique groups from the details array
		        	$stores = array_unique(array_column($details, 'store'));

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

				     	$tbl  = '<table  class="table table-bordered table-responsive" id="view_table_sales_qty_group_'.$index.'" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b;">';
			               	
			      		$tbl .= '<thead style="text-align: center;color:white;">';
			         	// Generate table headers
			      		//$tbl .= "<h4>Monthly Sales Report per Group => Store Name:".$store_name[0]['nav_store_val']."<h4>";
				     	//$tbl .= "<h2>Monthly Sales Report per Group => Store Name:".$store_no."<h2>";

				     	// Generate table headers

						sort($year);
		          	    $tbl .= '<tr>';
						//$tbl .= "<th style='position: sticky; left: 0; background-color: #033e5b; color: white; padding-right: 156px;'>Department</th>";
						$tbl .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; padding-right: 156px;">Group</th>';
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

				     	foreach ($groups as $group)
				     	{
				       		$counter = 0;
				       		$counter2 = 0;
				         	// Find the details for the current group
				     		// $groupDetails = array_filter($details, function ($detail) use ($store,$group) {
				     		// 	return $detail['store'] === $store && $detail['item_group'] === $group ;
				     		// });

				     		$groupDetails = [];

						    // Find the details for the current group
						    foreach ($details as $detail) {
						        if ($detail['store'] === $store && $detail['item_group'] === $group) {
						            $groupDetails[] = $detail;
						        }
						    }
		
				     		if (!empty($groupDetails)) {
						        $group_name = $this->Acct_mod->get_group_name($group);
						        $groups_name = (count($group_name) > 0) ? $group_name[0]['group_name'] : 'No Group Name';
						        $tbl .= "<tr>";
						        $tbl .= "<td style='position: sticky; left: 0; background-color: #fff;'>{$group} - {$groups_name}</td>";

						        foreach ($month_name as $month) {
						            foreach ($year as $y) {
						                $total = '0.00';
						                $total_qty  = '0';
						                foreach ($groupDetails as $detail) {
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
						                $tbl .= "<td>₱ {$total}</td>";

						                $tot_qty       = str_replace(',', '', $total_qty);
	                             		$totalSumQ += (float)$tot_qty;
	                             		$tbl .= "<td>" .$total_qty . "</td>";

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
					    $tbl .='<h3 style="font-size: 26px;">Monthly Sales Report per Group => Store Name: ' . $store_name[0]['nav_store_val'] . '</h3>';
					    // $tbl .='<h3 style="font-size: 17px;">Total Sales: &#8369;' . number_format($totalSum, 2, '.', ',') . '</h3>';

					    $tbl .= '
			    			  <tfoot>
							    <tr style="color: white;">
							      <th style="position: sticky; left: 0; background: darkcyan;">Total</th>';
							        
	                                $max_length = max(count($final_total_arr), count($final_total_arr2));

									for ($a = 0; $a < $max_length; $a++) {
									    if ($a < count($final_total_arr)) {
									        $tbl .= '<th>₱' . number_format($final_total_arr[$a], 2, '.', ',') . '</th>';
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
						$tbl .= '$("#view_table_sales_qty_group_'.$index.'").DataTable({ scrollX: true });';
						$tbl .= '});';
						$tbl .= '</script>';
			         	echo $tbl;
			         	$index++;
			        } // end foreach store

			        $tbl2 = '<table border="1" class="table table-bordered table-responsive" id="view_table_sales_qty_group_total" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; width: auto; table-layout: auto;">';
				    $tbl2 .= '<thead style="text-align: center;color:white;">';
	         	    sort($year);
	          	    $tbl2 .= '<tr>';
					//$tbl2 .= "<th style='position: sticky; left: 0; background-color: #033e5b; color: white; padding-right: 156px;'>Department</th>";
					$tbl2 .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; padding-right: 156px;">Group</th>';
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
	                        
	                        $tbl2 .= '<td style="color: white; background: darkcyan; text-align: right;">₱ '.number_format($over_all_final_total_arr[$a], 2, '.', ',').'</td>';
	                        $tbl2 .= '<td style="color: white; background: darkcyan; text-align: right;">'.number_format($over_all_final_total_arr2[$a]).'</td>';
	                    } 
				                			      

					$tbl2 .= '</tr>';
				    $tbl2 .= '</table>';
				    $tbl2 .= '<script>';
				    $tbl2 .= '$("#view_table_sales_qty_group_total").DataTable({ scrollX: true,  lengthChange: false,searching: false,info: false});';
				    $tbl2 .= '</script>';

				    echo $tbl2;     
		        } // end for all store
		        else{ // condition for per store
		        	$groups = array_unique(array_column($details, 'item_group')); // Get unique groups from the details array

			     	$store_name = $this->Acct_mod->get_store_name($store_no);

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

			     	$tbl  = '<table  class="table table-bordered table-responsive" id="payments_table_sales_qty_group" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b;">';
		               	
		      		$tbl .= '<thead style="text-align: center;color:white;">';
		         	// Generate table headers
		      		$tbl .= "<h4>Monthly Sales Report per Group => Store Name:".$store_name[0]['nav_store_val']."<h4>";
			     	//$tbl .= "<h2>Monthly Sales Report per Group => Store Name:".$store_no."<h2>";

			     	sort($year);
	          	    $tbl .= '<tr>';
					//$tbl .= "<th style='position: sticky; left: 0; background-color: #033e5b; color: white; padding-right: 156px;'>Department</th>";
					$tbl .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; padding-right: 156px;">Department</th>';
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

			     	foreach ($groups as $group)
			     	{
			       		$counter = 0;
			       		$counter2 = 0;
			         	$groupDetails = [];

					    // Find the details for the current group
					    foreach ($details as $detail) {
					        if ($detail['item_group'] === $group) {
					            $groupDetails[] = $detail;
					        }
					    }


			     		if (!empty($groupDetails))
				     	{
				     		$group_name = $this->Acct_mod->get_group_name($group);
				     		$groups_name = (count($group_name)>0) ? $group_name[0]['group_name'] : 'No Group Name';
				     		$groups_code = (count($group_name)>0) ? $group : 'No Group Code';
				     		$tbl .= "<tr>";
				     		$tbl .= "<td style='position: sticky; left: 0; background-color: #fff;'>" . $group . ' - ' . $groups_name . "</td>";
				     		//$tbl .= "<td>" . $groups_name . "</td>";

				 
				     		foreach ($month_name as $month)
				     		{
				     			foreach ($year as $y) {
				     				$total = '0.00';
				     				$total_qty = '0';
				     				foreach ($groupDetails as $detail) {
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
			     	//$tbl .= '<h3 hidden>Total Sales: ₱'.number_format($totalSum, 2, '.', ',').'</h3>';
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
					$tbl .= '$("#payments_table_sales_qty_group").DataTable({ scrollX: true });';
					$tbl .= '});';
					$tbl .= '</script>';
		         	echo $tbl;
		        } // end for per store      
	   		} // end for both sales and queantity
	   		// for quantity
	   		else{

	   			if($store_no == 'Select_all_store') {

		        	$groups = array_unique(array_column($details, 'item_group')); // Get unique groups from the details array
		        	$stores = array_unique(array_column($details, 'store'));

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

				     	$tbl  = '<table  class="table table-bordered table-responsive" id="view_table_qty_group_'.$index.'" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b;">';
			               	
			      		$tbl .= '<thead style="text-align: center;color:white;">';
			         	// Generate table headers
			      		//$tbl .= "<h4>Monthly Sales Report per Group => Store Name:".$store_name[0]['nav_store_val']."<h4>";
				     	//$tbl .= "<h2>Monthly Sales Report per Group => Store Name:".$store_no."<h2>";

				     	// Generate table headers

						sort($year);

						$tbl .= '<tr>';
							//$tbl .= "<th style='position: sticky; left: 0; background-color: #033e5b; color: white; padding-right: 156px;'>Department</th>";
						$tbl .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; padding-right: 156px;">Group</th>';
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

				     	foreach ($groups as $group)
				     	{
				       		$counter = 0;
				         	// Find the details for the current group
				     		// $groupDetails = array_filter($details, function ($detail) use ($store,$group) {
				     		// 	return $detail['store'] === $store && $detail['item_group'] === $group ;
				     		// });

				     		$groupDetails = [];

						    // Find the details for the current group
						    foreach ($details as $detail) {
						        if ($detail['store'] === $store && $detail['item_group'] === $group) {
						            $groupDetails[] = $detail;
						        }
						    }
		
				     		if (!empty($groupDetails)) {
						        $group_name = $this->Acct_mod->get_group_name($group);
						        $groups_name = (count($group_name) > 0) ? $group_name[0]['group_name'] : 'No Group Name';
						        $tbl .= "<tr>";
						        $tbl .= "<td style='position: sticky; left: 0; background-color: #fff;'>{$group} - {$groups_name}</td>";

						        foreach ($month_name as $month) {
						            foreach ($year as $y) {
						                $total = '0';
						                foreach ($groupDetails as $detail) {
						                    if ($detail['month'] == $month['number'] && $detail['year'] == $y) {
						                        
						                        $total = abs($detail['total_quantity']);
					     						$total = round($total, 0); 
												$total = intval($total); // Convert to integer to remove decimal places
												$total = number_format($total, 0, '', ','); // Format as comma-separated number without decimal places
					     						break;
						                    }
						                }
						                
						                $tot = str_replace(',', '', $total);
			                     		$totalSumQ += (float)$tot;
			                     		$totalSum  = round($totalSumQ);
					     				$tbl .= "<td style='text-align:right;'>" .$total . "</td>";

						                $final_total_arr[$counter] += $tot;
						                $over_all_final_total_arr[$counter] += $tot;
						                $counter++;
						            }
						        }

						        $tbl .= '</tr>';
						    }
				     	}
				     	$store_name = $this->Acct_mod->get_store_name($store);
							 
		              	$tbl .='<div class="responsive-div_top" style="margin-top: 1px; margin-bottom: 10px;"><div class="line-separator"></div></div>';
		              	//$tbl .= "<h4>Monthly Sales Report per Department => Store Name:".$store_name[0]['nav_store_val']."<h4>";
					    $tbl .='<h3 style="font-size: 26px;">Monthly Sales Report per Group => Store Name: ' . $store_name[0]['nav_store_val'] . '</h3>';
					    // $tbl .='<h3 style="font-size: 17px;">Total Sales: &#8369;' . number_format($totalSum, 2, '.', ',') . '</h3>';

					    $tbl .= '
					    		<tfoot>
								    <tr style="color: white;">
								      	<th style="position: sticky; left: 0; background: darkcyan;">Total</th>';
								        
	                                    for($a=0;$a<count($final_total_arr);$a++)
	                                    {            
	                                        
	                                        //$tbl .= '<th style="background: darkcyan;">&#8369; '.number_format($final_total_arr[$a], 2, '.', ',').'</th>';
	                                        $tbl .= '<th style="color:white; background: darkcyan;">'.number_format($final_total_arr[$a]).'</th>';
	                                    } 
						                			      
						$tbl .= '</tr>
								</tfoot>';		
			         	$tbl .= '</table>';
			         	$tbl .= '<script>';
						$tbl .= '$(document).ready(function() {';
						$tbl .= 'console.log("Initializing DataTable...");';
						$tbl .= '$("#view_table_qty_group_'.$index.'").DataTable({ scrollX: true });';
						$tbl .= '});';
						$tbl .= '</script>';
			         	echo $tbl;
			         	$index++;
			        } // end foreach store

			        $tbl2 = '<table border="1" class="table table-bordered table-responsive" id="view_table_sales_group_total" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; width: auto; table-layout: auto;">';
				    $tbl2 .= '<thead style="text-align: center;color:white;">';
	         	    $tbl2 .= '<tr>';
	         	   	$tbl2 .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; padding-right: 156px;">Group</th>';
				    sort($year);
						foreach ($month_name as $month) {
					        $tbl2 .= "<th style='text-align: center;' colspan=".count($year).">".$month['month']."</th>";
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

				    $tbl2 .= '
					           
					                <tr style="color: white;">
								      <td style="position: sticky; left: 0; background: darkcyan;">Grand Total</td>';
								        
	                                    for($a=0;$a<count($over_all_final_total_arr);$a++)
	                                    {            
	                                        
	                                        $tbl2 .= '<td style="color: black;">'.number_format($over_all_final_total_arr[$a]).'</td>';
	                                    } 
					                			      

						$tbl2 .= '</tr>';
				    $tbl2 .= '</table>';
				    $tbl2 .= '<script>';
				    $tbl2 .= '$("#view_table_sales_group_total").DataTable({ scrollX: true,  lengthChange: false,searching: false,info: false});';
				    $tbl2 .= '</script>';

				    echo $tbl2;     
		        } // end for all store
		        else{
		     		$groups = array_unique(array_column($details, 'item_group')); // Get unique groups from the details array

		     		$store_name = $this->Acct_mod->get_store_name($store_no);

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
			         	// Generate table headers
			      	$tbl .= "<h4>Monthly Quantity Report per Group => Store Name:".$store_name[0]['nav_store_val']."<h4>";
			     		//$tbl .= "<h2>Monthly Quantity Report per Group => Store Name:".$store_no."<h2>";

			   		sort($year);

					$tbl .= '<tr>';
						//$tbl .= "<th style='position: sticky; left: 0; background-color: #033e5b; color: white; padding-right: 156px;'>Department</th>";
					$tbl .= '<th rowspan="2" style="position: sticky; left: 0;background-color: #033e5b;color: white; padding-right: 156px;">Group</th>';
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

		     		foreach ($groups as $group)
		     		{
		     			$counter = 0;
		     			$groupDetails = [];
					    // Find the details for the current group
					    foreach ($details as $detail) {
					        if ($detail['item_group'] === $group) {
					            $groupDetails[] = $detail;
					        }
					    }

				     	if (!empty($groupDetails))
				     	{
				     		
				     		$group_name = $this->Acct_mod->get_group_name($group);
				     		$groups_name = (count($group_name)>0) ? $group_name[0]['group_name'] : 'No Group Name';
				     		$groups_code = (count($group_name)>0) ? $group : 'No Group Code';
				     		
				     		$tbl .= "<tr>";
		            		//$tbl .= "<td style='position: sticky; left: 0;; background-color: #fff;color: white;background-color: #034160;'>" . $department . "</td>";
		            		$tbl .= "<td style='position: sticky; left: 0; background-color: #fff;'>" . $group . ' - ' . $groups_name . "</td>";

				     		foreach ($month_name as $month)
				     		{
				     			foreach ($year as $y) {
				     				$total = '0';
				     				foreach ($groupDetails as $detail) {
				     					if ($detail['month'] == $month['number'] && $detail['year'] == $y) {
				     						$total = abs($detail['total_quantity']);
				     						$total = round($total, 0); 
											$total = intval($total); // Convert to integer to remove decimal places
											$total = number_format($total, 0, '', ','); // Format as comma-separated number without decimal places
				     						break;
				     					}
				     				}
			     					$tot = str_replace(',', '', $total);
		                     		$totalSumQ += (float)$tot;
		                     		$totalSum  = round($totalSumQ);
				     				$tbl .= "<td style='text-align:right;'>" .$total . "</td>";

				     				$final_total_arr_per_store[$counter] += $tot;
									$counter ++;
				     			}
				     		}

				     		$tbl .= '</tr>';
				     	}
		     		}
		     		// $tbl .= '<h3 hidden >Total Quantity: '.number_format($totalSum).'</h3>';	
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

		// function get yearly sales report 
	   	function get_yearly_report()
	   	{

			$tot            = '';
	    	$totalSum       = '0.00';
	    	$totalSum_      = '0.00';
	       	$range          = $_POST['range'];
	       	$store_no       = $_POST['store_no'];
	       	$year           = $_POST['year'];	
	       	$report_type    = $_POST['report_type'];
	       	$tbl            = '';
	       	$details_yearly = array();


	       	$original_year  = $year;
	       	$sub_year       = 2;
	       	$pre_year       = $original_year - $sub_year;

	       	$get_yearly     = $this->Acct_mod->get_yearly_report_group_mod(strval($pre_year),$year,$store_no);
	       	$year_filter    = array();

	       	foreach($get_yearly as $yearly)
	       	{
	       		if(!in_array($yearly['year'],$year_filter))
	       		{
	       			array_push($year_filter,$yearly['year']);
	       		}
	       		array_push($details_yearly,array('item_group'=>$yearly['item_group'],'total'=>$yearly['total'],'year'=>$yearly['year'], 'total_quantity_yearly'=>$yearly['total_quantity_yearly']));
	       	}


	       	if($report_type == 'sales')
	       	{

	            $groups     = array_unique(array_column($details_yearly, 'item_group')); // Get unique groups from the details array

	            $store_name = $this->Acct_mod->get_store_name($store_no);

	            header("content-type: application/vnd.ms-excel");
	            header("Content-Disposition: attachment; filename= Yearly Sales Report per Group.xls");

	            $tbl = '<table border="1">';
	            $tbl .= "<h2>Yearly Sales Report per Group => Store Name:".$store_no."<h2>";

	            $tbl .= "<tr>";
		        	$tbl .= "<th style='font-weight: bold; text-align: center;'>Yearly Sales Report per Group => Store Name: ".$store_name[0]['nav_store_val']."</th>";
		        	$tbl .= "</tr>";
		     
					$tbl .= "<tr>";
		        	$tbl .= "<th></th>";
		        	$tbl .= "</tr>";
	            $tbl .= "<tr>";
	            $tbl .= "<th>Code</th>";
	            $tbl .= "<th>Group Name</th>";
	            foreach ($year_filter as $year_) 
	            {
	           	    	//var_dump($year_);
	            	$tbl .= "<th>".$year_."</th>";
	            }

	            foreach ($groups as $group)
	            {
	             
	               	// Find the details for the current group
	            	$groupDetails = array_filter($details_yearly, function ($detail) use ($group) {
	            		return $detail['item_group'] === $group;
	            	});

	            	// $groupDetails = [];

				    // // Find the details for the current group
				    // foreach ($details_yearly as $detail) {
				    //     if ($detail['item_group'] === $group) {
				    //         $groupDetails[] = $detail;
				    //     }
				    // }


	            	if (!empty($groupDetails))
	            	{
	            		$group_name = $this->Acct_mod->get_group_name($group);
				     		$groups_name = (count($group_name)>0) ? $group_name[0]['group_name'] : 'No Group Name';
				     		$groups_code = (count($group_name)>0) ? $group : 'No Group Code';
				     		$tbl .= "<tr>";
				     		$tbl .= "<td>" . $groups_code . "</td>";
				     		$tbl .= "<td>" . $groups_name . "</td>";

	            		foreach ($year_filter as $y) {
	            			$total = '0.00';
	            			foreach ($groupDetails as $detail) {
	            				if ($detail['year'] == $y) {
	            					$total = abs($detail['total']);
	            					$total = number_format($total, 2, '.', ',');
	            					break;
	            				}
	            			}
	            			$tot   = str_replace(',', '', $total);
								$totalSum += (float)$tot;
	            			$tbl .= "<td>₱ " . $total . "</td>";
	            		}


	            		$tbl .= '</tr>';
	            	}
	            }
	            $tbl .= "<tr>";
		        	$tbl .= "<td colspan='25'></td>";
		        	$tbl .= "</tr>";
	            $tbl .= '<tfoot>								                 
		                   	<tr>
		                    	<td colspan="25"><b>Grand Total: </b> ₱ '.number_format($totalSum, 2, '.', ',').'</td>
		                   	</tr>
	                   	</tfoot>';
	            $tbl .= '</table>';
	            echo $tbl;
	                
		    }else{

	    	   	$groups     = array_unique(array_column($details_yearly, 'item_group')); // Get unique groups from the details array

	    	   	$store_name = $this->Acct_mod->get_store_name($store_no);

	    	   	header("content-type: application/vnd.ms-excel");
	    	   	header("Content-Disposition: attachment; filename= Yearly Quantity Report per Group.xls");

	    	   	$tbl = '<table border="1">';
	    	   	$tbl .= "<h2>Yearly Quantity Report per Group => Store Name:".$store_no."<h2>";

	    	   	$tbl .= "<tr>";
		        	$tbl .= "<th style='font-weight: bold; text-align: center;'>Yearly Quantity Report per Group => Store Name: ".$store_name[0]['nav_store_val']."</th>";
		        	$tbl .= "</tr>";
		     
					$tbl .= "<tr>";
		        	$tbl .= "<th></th>";
		        	$tbl .= "</tr>";
	    	   	$tbl .= "<tr>";
	    	   	$tbl .= "<th>Code</th>";
	    	   	$tbl .= "<th>Group Name</th>";
	    	   	foreach ($year_filter as $year_) 
	    	   	{
	           	    //var_dump($year_);
	    	   		$tbl .= "<th>".$year_."</th>";
	    	   	}

	            	foreach ($groups as $group)
	            	{
		                 
	         	   	$groupDetails = array_filter($details_yearly, function ($detail) use ($group) {
	         	   		return $detail['item_group'] === $group;
	         	   	});


	         	   	if (!empty($groupDetails))
	         	   	{
	         	   		$group_name = $this->Acct_mod->get_group_name($group);
					     		$groups_name = (count($group_name)>0) ? $group_name[0]['group_name'] : 'No Group Name';
					     		$groups_code = (count($group_name)>0) ? $group : 'No Group Code';
					     		$tbl .= "<tr>";
					     		$tbl .= "<td>" . $groups_code . "</td>";
					     		$tbl .= "<td>" . $groups_name . "</td>";

	         	   		foreach ($year_filter as $y) {
	         	   			$total = '0.00';
	         	   			foreach ($groupDetails as $detail) {
	         	   				if ($detail['year'] == $y) {
	         	   					$total = abs($detail['total_quantity_yearly']);
	                                       
	         	   					$total = round($total, 0); 
									$total = intval($total); // Convert to integer to remove decimal places
									$total = number_format($total, 0, '', ','); // Format as comma-separated number without decimal places
	         	   					break;
	         	   				}
	         	   			}
	         	   			$tot       = str_replace(',', '', $total);
	                        $totalSum_ += (float)$tot;
	                        $totalSum  = round($totalSum_);
	         	   			$tbl .= "<td>" .$total . "</td>";
	         	   		}


	         	   		$tbl .= '</tr>';
	         	   	}
	            	}

	         			$tbl .= "<tr>";
				        	$tbl .= "<td colspan='25'></td>";
				        	$tbl .= "</tr>";
			            $tbl .= '<tfoot>								                 
				                   	<tr>
				                    	<td colspan="25"><b>Grand Total: </b> '.number_format($totalSum).'</td>
				                   	</tr>
			                   	</tfoot>';
	         	   	$tbl .= '</table>';
	         	   	echo $tbl;
	         }
	    }

	    function view_yearly_report()
	   	{

			$tot            = '';
	    	$totalSum       = '0.00';
	    	$totalSum_      = '0.00';
	       	$range          = $_POST['range'];
	       	$store_no       = $_POST['store_no'];
	       	$year           = $_POST['year'];	
	       	$report_type    = $_POST['report_type'];
	       	$tbl            = '';
	       	$details_yearly = array();


	       	$original_year  = $year;
	       	$sub_year       = 2;
	       	$pre_year       = $original_year - $sub_year;

	       	$get_yearly     = $this->Acct_mod->get_yearly_report_group_mod(strval($pre_year),$year,$store_no);
	       	$year_filter    = array();

	       	foreach($get_yearly as $yearly)
	       	{
	       		if(!in_array($yearly['year'],$year_filter))
	       		{
	       			array_push($year_filter,$yearly['year']);
	       		}
	       		array_push($details_yearly,array('item_group'=>$yearly['item_group'],'total'=>$yearly['total'],'year'=>$yearly['year'], 'total_quantity_yearly'=>$yearly['total_quantity_yearly'], 'store'=>$yearly['store']));
	       	}


	       	if($report_type == 'sales')
	       	{
	       		if($store_no == 'Select_all_store'){
	     			$index = 0;

	     			$groups   = array_unique(array_column($details_yearly, 'item_group')); // Get unique groups from the details array
	                $stores   = array_unique(array_column($details_yearly, 'store'));
				 	      
				 	$over_all_final_total_arr_yearly = array();

					foreach ($year_filter as $year_) 
					{
					  	array_push($over_all_final_total_arr_yearly,0);
					}

		 	       	foreach($stores as $store)
		 	       	{ 

		 	         	$final_total_arr_yearly =  array();
					 	$row_total = 0;
				     
				       	foreach ($year_filter as $year_) {
				         	$row_total +=1;
				         	array_push($final_total_arr_yearly,0);
				        
				       	}
				       	header("content-type: application/vnd.ms-excel; charset=utf-8");
                    	header("Content-Disposition: attachment; filename= Yearly Sales Report per Group.xls");
	           			$tbl  = '<table border="1" class="table table-bordered table-responsive" id="payments_group_table_'.$index.'" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; ">';
	            		$tbl .= '<thead style="color:white;">';
	           			//$tbl .= "<h4>Yearly Sales Report per group => Store Name:".$store_name[0]['nav_store_val']."<h4>";

	           
		         	   	$tbl .= "<tr>";
		               	$tbl .= "<th style=' color: white;'>Code</th>";
		               	$tbl .= "<th style=' color: white;'>Group Name</th>";
		               	foreach ($year_filter as $year_) 
		           	    {
		           	    	//var_dump($year_);
		      		        $tbl .= "<th style=' color: white;'>".$year_."</th>";
		           	    }

		           	    $tbl .="</tr>";
				        $tbl .= '</thead>';

				        // $storeDetails = array_filter($details_yearly, function ($detail) use ($store) {
					    //     return $detail['store'] === $store;
					    // });

		                foreach ($groups as $group)
		                {
		                 	$counter = 0;
		                   	
		                   	$groupDetails = [];
			            	// Find the details for the current group
						    foreach ($details_yearly as $detail) {
						        if ($detail['store'] === $store && $detail['item_group'] === $group) {
						            $groupDetails[] = $detail;
						        }
						    }

			               	if(!empty($groupDetails))
			                {
			                  
			                  	$group_name = $this->Acct_mod->get_group_name($group);
					     		$groups_name = (count($group_name)>0) ? $group_name[0]['group_name'] : 'No Group Name';
					     		$groups_code = (count($group_name)>0) ? $group : 'No Group Code';
					     		$tbl .= "<tr>";
					     		$tbl .= "<td style='background-color: white; color: black;'>" . $group . "</td>";
					     		$tbl .= "<td style='background-color: white; color: black;'>" . $groups_name . "</td>";

			                   	foreach ($year_filter as $y) {
			                       	$total = '0.00';
			                       	foreach ($groupDetails as $detail) {
			                           	if ($detail['year'] == $y) {
			                               	$total = abs($detail['total']);
			                               	$total = number_format($total, 2, '.', ',');
			                               	break;
			                           	}
			                       	}
			                       	$tot   = str_replace(',', '', $total);
									$totalSum += (float)$tot;
			                       	$tbl .= "<td style='background-color: white; color: black;'>₱ " . $total . "</td>";

			                       	$final_total_arr_yearly[$counter] += $tot; 
	                                $over_all_final_total_arr_yearly[$counter] += $tot; 
	                                $counter ++;
			                   	} // end foreach groupDetails
			                   	$tbl .= '</tr>';
			               	}
		              	} // end foreach groups

	              		$store_name = $this->Acct_mod->get_store_name($store);
	              		$tbl .='<div class="responsive-div_top" style="margin-top: 1px; margin-bottom: 10px;"><div class="line-separator"></div></div>';
			            $tbl .= "<h3 style='font-size: 23px;'>Yearly Sales Report per Group => Store Name:".$store_name[0]['nav_store_val']."<h3>";
	                    $tbl .= '
				    			  <tfoot>
								    <tr style="color: white;">
								      <th style="position: sticky; left: 0; background: darkcyan;">Total</th>
								      <th style="position: sticky; left: 0; background: darkcyan;color: darkcyan;">Total</th>';
								        
	                                    for($a=0;$a<count($final_total_arr_yearly);$a++)
	                                    {            
	                                        
	                                        $tbl .= '<th style="color: white;">₱'.number_format($final_total_arr_yearly[$a], 2, '.', ',').'</th>';
	                                    } 
					                			      
					    $tbl .= '	</tr>
								 </tfoot>';

		                $tbl .= '</table>';
		                $tbl .= '<script>';
					    $tbl .= '$("#payments_group_table_'.$index.'").DataTable({})';
					    $tbl .='</script>';
		                echo $tbl;  
		                $index++;
		            } // end foreach store

				    $tbl2  = '<table border="1" class="table table-bordered table-responsive" id="payments_table_yearly" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; ">';
				    $tbl2 .= '<thead style="color:white;">';
	         	    $tbl2 .= "<tr>";
	                $tbl2 .= "<th style='color: #154351;'>Code</th>";
	                $tbl2 .= "<th style='color: #154351;'>Group Name</th>";
		            foreach ($year_filter as $year_) 
	           	    {
	      		        $tbl2 .= "<th style='color: white;'>".$year_."</th>";
	           	    }

	           	    $tbl2 .="</tr>";
				    $tbl2 .= '</thead>';

				    $tbl2 .= '
				           
				        <tr style="color: white;">';
							$tbl2 .= ' <td style="position: sticky; left: 0; background: darkcyan;">Grand Total</td>';
						    $tbl2 .= ' <td style="position: sticky; left: 0; background: darkcyan; color: darkcyan;">Grand Total</td>';
						        
	                        for($a=0;$a<count($over_all_final_total_arr_yearly);$a++)
	                        {            
	                            
	                            $tbl2 .= '<td style="color: white;">₱'.number_format($over_all_final_total_arr_yearly[$a], 2, '.', ',').'</td>';
	                        } 
					$tbl2 .= '</tr>';
				    $tbl2 .= '</table>';
				    $tbl2 .= '<script>';
				    $tbl2 .= '$("#payments_table_yearly").DataTable({ scrollX: true, lengthChange: false,searching: false,info: false});';
				    $tbl2 .= '</script>';

				    echo $tbl2;
	     		} // end for all store

	     		else{ // condition for per store
		            $groups     = array_unique(array_column($details_yearly, 'item_group')); // Get unique groups from the details array

		            $store_name = $this->Acct_mod->get_store_name($store_no);

		            $final_total_arr_yearly =  array();
				 	$row_total = 0;
			     
			       	foreach ($year_filter as $year_) {
			         	$row_total +=1;
			         	array_push($final_total_arr_yearly,0);
			        
			       	}

			        $tbl  = '<table  class="table table-bordered table-responsive" id="sales_table_yearly" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; ">';
		            $tbl .= '<thead style="color:white;">';
		            // $tbl .= "<h4>Yearly Sales Report per Group => Store Name:".$store_name[0]['nav_store_val']."<h4>";

		      	   	$tbl .= "<tr>";
		            $tbl .= "<th>Code</th>";
		            $tbl .= "<th>Group Name</th>";
		            foreach ($year_filter as $year_) 
			    	{
		        	    //var_dump($year_);
		   		        $tbl .= "<th>".$year_."</th>";
		        	}

		        	$tbl .="</tr>";
			        $tbl .= '</thead>';

		            foreach ($groups as $group)
		            {
		            	$counter = 0;
		            	$groupDetails = [];
		            	// Find the details for the current group
					    foreach ($details_yearly as $detail) {
					        if ($detail['item_group'] === $group) {
					            $groupDetails[] = $detail;
					        }
					    }

		            	if (!empty($groupDetails))
		            	{
		            		$group_name = $this->Acct_mod->get_group_name($group);
				     		$groups_name = (count($group_name)>0) ? $group_name[0]['group_name'] : 'No Group Name';
				     		$groups_code = (count($group_name)>0) ? $group : 'No Group Code';
				     		$tbl .= "<tr>";
				     		$tbl .= "<td>" . $group . "</td>";
				     		$tbl .= "<td>" . $groups_name . "</td>";

		            		foreach ($year_filter as $y) {
		            			$total = '0.00';
		            			foreach ($groupDetails as $detail) {
		            				if ($detail['year'] == $y) {
		            					$total = abs($detail['total']);
		            					$total = number_format($total, 2, '.', ',');
		            					break;
		            				}
		            			}
		            			$tot   = str_replace(',', '', $total);
								$totalSum += (float)$tot;
		            			$tbl .= "<td>₱ " . $total . "</td>";

		            			$final_total_arr_yearly[$counter] += $tot; 
		                        $counter ++;
		            		}


		            		$tbl .= '</tr>';
		            	}
		            }
		            $store_name = $this->Acct_mod->get_store_name($store_no);
		      		$tbl .='<div class="responsive-div_top" style="margin-top: 1px; margin-bottom: 10px;"><div class="line-separator"></div></div>';
		            $tbl .= "<h3 style='font-size: 23px;'>Yearly Sales Report per Group => Store Name:".$store_name[0]['nav_store_val']."<h3>";
		            $tbl .= '
			    			  <tfoot>
							    <tr style="color: white;">
							      <th style="position: sticky; left: 0; background: darkcyan;">Total</th>
							      <th style="position: sticky; left: 0; background: darkcyan;color: darkcyan;">Total</th>';
							        
		                            for($a=0;$a<count($final_total_arr_yearly);$a++)
		                            {            
		                                
		                                $tbl .= '<th>₱'.number_format($final_total_arr_yearly[$a], 2, '.', ',').'</th>';
		                            } 
				                			      
				    $tbl .= '	</tr>
							 </tfoot>';
		          	$tbl .= '</table>';
		          	$tbl .= '<script>';
				  	$tbl .= '$("#sales_table_yearly").DataTable({})';
				  	$tbl .='</script>';
		      		echo $tbl;
	            } // end for per store  
		    }
		    else if($report_type == 'both')
         	{
         		if($store_no == 'Select_all_store'){
         			$index = 0;

         			$groups   = array_unique(array_column($details_yearly, 'item_group')); // Get unique groups from the details array
	                $stores   = array_unique(array_column($details_yearly, 'store'));
  			 	      
  			 	   	$over_all_final_total_arr_yearly = array();
  			 	   	$over_all_final_total_arr_yearly2 = array();

					foreach ($year_filter as $year_) 
					{
					  	array_push($over_all_final_total_arr_yearly,0);
					  	array_push($over_all_final_total_arr_yearly2,0);
					}

		 	       	foreach($stores as $store)
		 	       	{ 

		 	         	$final_total_arr_yearly =  array();
		 	         	$final_total_arr_yearly2 =  array();
					 	$row_total = 0;
					 	$row_total2 = 0;
				     
				       	foreach ($year_filter as $year_) {
				         	$row_total +=1;
				         	$row_total2 +=1;
				         	array_push($final_total_arr_yearly,0);
				         	array_push($final_total_arr_yearly2,0);
				        
				       	}
				       	header("content-type: application/vnd.ms-excel; charset=utf-8");
                    	header("Content-Disposition: attachment; filename= Yearly Sales and Quantity Report per Group.xls");
               			$tbl  = '<table border="1" class="table table-bordered table-responsive" id="payments_table_'.$index.'" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; ">';
	            		$tbl .= '<thead style="color:white;">';
               			//$tbl .= "<h4>Yearly Sales Report per group => Store Name:".$store_name[0]['nav_store_val']."<h4>";

	            		$tbl .= "<tr>";
		               	$tbl .= "<th rowspan ='2' style='color: white;'>Code</th>";
		               	$tbl .= "<th rowspan ='2' style='color: white;'>Group Name</th>";
		               	sort($year_filter);
		               	foreach ($year_filter as $year_) 
	               	    {
	          		        $tbl .= "<th colspan='2' style='color: white; text-align: center;'>".$year_."</th>";
	               	    }
	               	    $tbl .="</tr>";

	               	    //
	               	    $tbl .="<tr>";
	               	    sort($year_filter);
	               	    foreach ($year_filter as $year_) 
	               	    {
	          		         $tbl .= '<th style="text-align: center; color: white;">SALES</th>';
		                     $tbl .= '<th style="text-align: center; color: white;">QTY</th>';
	               	    }
	               	    $tbl .="</tr>";
				        $tbl .= '</thead>';

				        // $storeDetails = array_filter($details_yearly, function ($detail) use ($store) {
					    //     return $detail['store'] === $store;
					    // });

		                foreach ($groups as $group)
		                {
		                 	$counter = 0;
		                 	$counter2 = 0;
		                   	// Find the details for the current group
		                   	$groupDetails = [];
			            	// Find the details for the current group
						    foreach ($details_yearly as $detail) {
						        if ($detail['store'] === $store && $detail['item_group'] === $group) {
						            $groupDetails[] = $detail;
						        }
						    }

			               	if(!empty($groupDetails))
			                {
			                  
			                  	$group_name = $this->Acct_mod->get_group_name($group);
					     		$groups_name = (count($group_name)>0) ? $group_name[0]['group_name'] : 'No Group Name';
					     		$groups_code = (count($group_name)>0) ? $group : 'No Group Code';
					     		$tbl .= "<tr>";
					     		$tbl .= "<td style='background-color: white; color: black;'>" . $group . "</td>";
					     		$tbl .= "<td style='background-color: white; color: black;'>" . $groups_name . "</td>";

			                   	foreach ($year_filter as $y) {
			                       	$total = '0.00';
			                       	$total_qty = '0';
			                       	foreach ($groupDetails as $detail) {
			                           	if ($detail['year'] == $y) {
			                               	$total = abs($detail['total']);
			                               	$total = number_format($total, 2, '.', ',');

			                               	$total_qty = abs($detail['total_quantity_yearly']);
		                                   	$total_qty = round($total_qty, 0); 
											$total_qty = intval($total_qty); // Convert to integer to remove decimal places
											$total_qty = number_format($total_qty, 0, '', ',');
			                               	break;
			                           	}
			                       	}
			                       	$tot   = str_replace(',', '', $total);
									$totalSum += (float)$tot;
			                       	$tbl .= "<td style='background-color: white; color: black;'>₱ " . $total . "</td>";

			                       	$tot_qty    = str_replace(',', '', $total_qty);
		                            $totalSum_ += (float)$tot_qty;
		                            $totalSum  = round($totalSum_);
		                           	$tbl .= "<td style='background-color: white; color: black;'>" .$total_qty . "</td>";

			                       	$final_total_arr_yearly[$counter] += $tot; 
	                                $over_all_final_total_arr_yearly[$counter] += $tot; 
	                                $counter ++;

	                                $final_total_arr_yearly2[$counter2] += $tot_qty; 
	                                $over_all_final_total_arr_yearly2[$counter2] += $tot_qty; 
	                                $counter2 ++;



			                   		} // end foreach groupDetails
			                   	$tbl .= '</tr>';
			               	}
		              	} // end foreach groups
		              		$store_name = $this->Acct_mod->get_store_name($store);
		              		$tbl .='<div class="responsive-div_top" style="margin-top: 1px; margin-bottom: 10px;"><div class="line-separator"></div></div>';
				            $tbl .= "<h3 style='font-size: 23px;'>Yearly Sales Report per group => Store Name:".$store_name[0]['nav_store_val']."<h3>";
		                    $tbl .= '
					    			  <tfoot>
									    <tr style="color: white;">
									      <th style="position: sticky; left: 0; background: darkcyan;">Total</th>
									      <th style="position: sticky; left: 0; background: darkcyan;color: darkcyan;">Total</th>';
									        
                                            // for($a=0;$a<count($final_total_arr_yearly);$a++)
                                            // {            
                                                
                                            //     $tbl .= '<th>₱'.number_format($final_total_arr_yearly[$a], 2, '.', ',').'</th>';
                                            // }

                                            $max_length = max(count($final_total_arr_yearly), count($final_total_arr_yearly2));

												for ($a = 0; $a < $max_length; $a++) {
												    if ($a < count($final_total_arr_yearly)) {
												        $tbl .= '<th style=" color: white;">₱' . number_format($final_total_arr_yearly[$a], 2, '.', ',') . '</th>';
												    } else {
												        $tbl .= '<th></th>'; // Add an empty cell if the first array doesn't have a value for this iteration
												    }

												    if ($a < count($final_total_arr_yearly2)) {
												        $tbl .= '<th style=" color: white;">' . number_format($final_total_arr_yearly2[$a]) . '</th>';
												    } else {
												        $tbl .= '<th></th>'; // Add an empty cell if the second array doesn't have a value for this iteration
												    }
												} 
						                			      
						    $tbl .= '	</tr>
									 </tfoot>';

			                $tbl .= '</table>';
			                $tbl .= '<script>';
						    $tbl .= '$("#payments_table_'.$index.'").DataTable({})';
						    $tbl .='</script>';
			                echo $tbl;  
			                $index++;


		            } // end foreach store

		            
	 	            $tbl2  = '<table border="1"  class="table table-bordered table-responsive" id="payments_table_yearly" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; ">';
				    $tbl2 .= '<thead style="color:white;">';
	         	    
	               	$tbl2 .= "<tr>";
	               	$tbl2 .= "<th rowspan ='2' style=' color: #033e5b;'>Code</th>";
	               	$tbl2 .= "<th rowspan ='2' style='color: #033e5b;'>group Name</th>";
	               	sort($year_filter);
	               	foreach ($year_filter as $year_) 
               	    {
          		        $tbl2 .= "<th colspan='2' style='color: white; text-align: center;'>".$year_."</th>";
               	    }
               	    $tbl2 .="</tr>";

               	    //
               	    $tbl2 .="<tr>";
               	    sort($year_filter);
               	    foreach ($year_filter as $year_) 
               	    {
          		         $tbl2 .= '<th style="text-align: center; color: white;">SALES</th>';
	                     $tbl2 .= '<th style="text-align: center; color: white;">QTY</th>';
               	    }
               	    $tbl2 .="</tr>";


			    	$tbl2 .= '</thead>';
			    	$tbl2 .= '
				           
				                <tr style="color: white;">';
							    $tbl2 .= ' <td style="position: sticky; left: 0; background: darkcyan;">Grand Total</td>
							    		<td style="position: sticky; left: 0; background: darkcyan; color:darkcyan;">Grand Total</td>';												        
                                    for($a=0;$a<count($over_all_final_total_arr_yearly);$a++)
                                    {            
                                        
                                        $tbl2 .= '<td style="color: white; background: darkcyan; text-align: right;">₱'.number_format($over_all_final_total_arr_yearly[$a], 2, '.', ',').'</td>';
                                        $tbl2 .= '<td style="color: white; background: darkcyan; text-align: right;">'.number_format($over_all_final_total_arr_yearly2[$a]).'</td>';
                                    } 
				                			      

					$tbl2 .= '</tr>';
				    $tbl2 .= '</table>';
				    $tbl2 .= '<script>';
				    $tbl2 .= '$("#payments_table_yearly").DataTable({ scrollX: true, lengthChange: false,searching: false,info: false});';
				    $tbl2 .= '</script>';

				    echo $tbl2;
         		} // end for all store

         		else{ // condition for per store
         			$groups     = array_unique(array_column($details_yearly, 'item_group')); // Get unique groups from the details array

	                $store_name = $this->Acct_mod->get_store_name($store);

	                $final_total_arr_yearly_per_store =  array();
	 	         	$final_total_arr_yearly_per_store2 =  array();
				 	$row_total = 0;
				 	$row_total2 = 0;

				 	foreach ($year_filter as $year_) {
			         	$row_total +=1;
			         	$row_total2 +=1;
			         	array_push($final_total_arr_yearly_per_store,0);
			         	array_push($final_total_arr_yearly_per_store2,0);
			        
			       	}

	               	$tbl  = '<table  class="table table-bordered table-responsive" id="payments_table" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; ">';
		            $tbl .= '<thead style="color:white;">';
	               	//$tbl .= "<h4>Yearly Sales Report per group => Store Name:".$store_name[0]['nav_store_val']."<h4>";

	               
	         	   	// $tbl .= "<tr>";
	               	$tbl .= "<tr>";
		               	$tbl .= "<th rowspan ='2' style='color: white;'>Code</th>";
		               	$tbl .= "<th rowspan ='2' style='color: white;'>Group Name</th>";
		               	sort($year_filter);
		               	foreach ($year_filter as $year_) 
	               	    {
	          		        $tbl .= "<th colspan='2' style=';color: white; text-align: center;'>".$year_."</th>";
	               	    }
	               	    $tbl .="</tr>";

	               	    //
	               	    $tbl .="<tr>";
	               	    sort($year_filter);
	               	    foreach ($year_filter as $year_) 
	               	    {
	          		         $tbl .= '<th style="text-align: center; color: white;">SALES</th>';
		                     $tbl .= '<th style="text-align: center; color: white;">QTY</th>';
	               	    }
	               	    $tbl .="</tr>";
			        $tbl .= '</thead>';

	                foreach ($groups as $group)
	                {
	                 	$counter = 0;
		                $counter2 = 0;
	                   	// Find the details for the current group
	                   	$groupDetails = [];
		            	// Find the details for the current group
					    foreach ($details_yearly as $detail) {
					        if ($detail['item_group'] === $group) {
					            $groupDetails[] = $detail;
					        }
					    }


			     	   	if (!empty($groupDetails))
			     	   	{
			     	   		$group_name = $this->Acct_mod->get_group_name($group);
				     		$groups_name = (count($group_name)>0) ? $group_name[0]['group_name'] : 'No Group Name';
				     		$groups_code = (count($group_name)>0) ? $group : 'No Group Code';
				     		$tbl .= "<tr>";
				     		$tbl .= "<td>" . $group . "</td>";
				     		$tbl .= "<td>" . $groups_name . "</td>";

		                   	foreach ($year_filter as $y) {
		                       	$total 		= '0.00';
		                       	$total_qty 	= '0';
		                       	foreach ($groupDetails as $detail) {
		                           	if ($detail['year'] == $y) {
		                               	$total = abs($detail['total']);
		                               	$total = number_format($total, 2, '.', ',');

		                               	$total_qty = abs($detail['total_quantity_yearly']);
	                                   	$total_qty = round($total_qty, 0); 
										$total_qty = intval($total_qty); // Convert to integer to remove decimal places
										$total_qty = number_format($total_qty, 0, '', ',');
		                               	break;
		                           	}
		                       	}
		                       	$tot   = str_replace(',', '', $total);
								$totalSum += (float)$tot;
		                       	$tbl .= "<td>₱ " . $total . "</td>";

		                       	$tot_qty    = str_replace(',', '', $total_qty);
	                            $totalSum_ += (float)$tot_qty;
	                            $totalSum  = round($totalSum_);
	                           	$tbl .= "<td>" .$total_qty . "</td>";

	                           	$final_total_arr_yearly_per_store[$counter] += $tot; 
                                //$over_all_final_total_arr_yearly[$counter] += $tot; 
                                $counter ++;

                                $final_total_arr_yearly_per_store2[$counter2] += $tot_qty; 
                                //$over_all_final_total_arr_yearly2[$counter2] += $tot_qty; 
                                $counter2 ++;
		                   	}
		                   	$tbl .= '</tr>';
		               	}
	              	} // end foreach groups
	              		//$tbl .= '<h3 colspan="39" hidden>Total Sales: ₱'.number_format($totalSum, 2, '.', ',').'</h3>';

	              		$store_name = $this->Acct_mod->get_store_name($store);
	              		$tbl .='<div class="responsive-div_top" style="margin-top: 1px; margin-bottom: 10px;"><div class="line-separator"></div></div>';
			            $tbl .= "<h3 style='font-size: 23px;'>Yearly Sales Report per group => Store Name:".$store_name[0]['nav_store_val']."<h3>";
	                    $tbl .= '
				    			  <tfoot>
								    <tr style="color: white;">
								      <th style="position: sticky; left: 0; background: darkcyan;">Total</th>
								      <th style="position: sticky; left: 0; background: darkcyan;color: darkcyan;">Total</th>';
								        
                                        
                                        $max_length = max(count($final_total_arr_yearly_per_store), count($final_total_arr_yearly_per_store2));

											for ($a = 0; $a < $max_length; $a++) {
											    if ($a < count($final_total_arr_yearly_per_store)) {
											        $tbl .= '<th>₱' . number_format($final_total_arr_yearly_per_store[$a], 2, '.', ',') . '</th>';
											    } else {
											        $tbl .= '<th></th>'; // Add an empty cell if the first array doesn't have a value for this iteration
											    }

											    if ($a < count($final_total_arr_yearly_per_store2)) {
											        $tbl .= '<th>' . number_format($final_total_arr_yearly_per_store2[$a]) . '</th>';
											    } else {
											        $tbl .= '<th></th>'; // Add an empty cell if the second array doesn't have a value for this iteration
											    }
											} 
					                			      
					    $tbl .= '	</tr>
								 </tfoot>';	
		              	$tbl .= '</table>';
		              	$tbl .= '<script>';
					  	$tbl .= '$("#payments_table").DataTable({})';
					  	$tbl .='</script>';
	              		echo $tbl;
         		} // end condition for per store 
       		} // end condition both sales and quantity
		    else{ // for quantity

		    	if($store_no == 'Select_all_store'){
	     			$index = 0;

	     			$groups   = array_unique(array_column($details_yearly, 'item_group')); // Get unique groups from the details array
	                $stores   = array_unique(array_column($details_yearly, 'store'));
				 	      
				 	$over_all_final_total_arr_yearly = array();

					foreach ($year_filter as $year_) 
					{
					  	array_push($over_all_final_total_arr_yearly,0);
					}

		 	       	foreach($stores as $store)
		 	       	{ 

		 	         	$final_total_arr_yearly =  array();
					 	$row_total = 0;
				     
				       	foreach ($year_filter as $year_) {
				         	$row_total +=1;
				         	array_push($final_total_arr_yearly,0);
				        
				       	}

	           			$tbl  = '<table border="1" class="table table-bordered table-responsive" id="payments_group_table_qty'.$index.'" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; ">';
	            		$tbl .= '<thead style="color:white;">';
	           			//$tbl .= "<h4>Yearly Sales Report per group => Store Name:".$store_name[0]['nav_store_val']."<h4>";

	           
		         	   	$tbl .= "<tr>";
		               	$tbl .= "<th style='color: white;'>Code</th>";
		               	$tbl .= "<th style='color: white;'>Group Name</th>";
		               	foreach ($year_filter as $year_) 
		           	    {
		           	    	//var_dump($year_);
		      		        $tbl .= "<th style='color: white;'>".$year_."</th>";
		           	    }

		           	    $tbl .="</tr>";
				        $tbl .= '</thead>';

				        // $storeDetails = array_filter($details_yearly, function ($detail) use ($store) {
					    //     return $detail['store'] === $store;
					    // });

		                foreach ($groups as $group)
		                {
		                 	$counter = 0;
		                   	
		                   	$groupDetails = [];
			            	// Find the details for the current group
						    foreach ($details_yearly as $detail) {
						        if ($detail['store'] === $store && $detail['item_group'] === $group) {
						            $groupDetails[] = $detail;
						        }
						    }

			               	if(!empty($groupDetails))
			                {
			                  
			                  	$group_name = $this->Acct_mod->get_group_name($group);
					     		$groups_name = (count($group_name)>0) ? $group_name[0]['group_name'] : 'No Group Name';
					     		$groups_code = (count($group_name)>0) ? $group : 'No Group Code';
					     		$tbl .= "<tr>";
					     		$tbl .= "<td style='background-color: white; color: black;'>" . $group . "</td>";
					     		$tbl .= "<td style='background-color: white; color: black;'>" . $groups_name . "</td>";

			                   	foreach ($year_filter as $y) {
			                       	$total = '0';
				     	   			foreach ($groupDetails as $detail) {
				     	   				if ($detail['year'] == $y) {
				     	   					$total = abs($detail['total_quantity_yearly']);
				                                   
				     	   					$total = round($total, 0); 
											$total = intval($total); // Convert to integer to remove decimal places
											$total = number_format($total, 0, '', ','); // Format as comma-separated number without decimal places
				     	   					break;
				     	   				}
				     	   			}
			                       	// $tot   = str_replace(',', '', $total);
									// $totalSum += (float)$tot;
			                       	// $tbl .= "<td>₱ " . $total . "</td>";

			                       	$tot       = str_replace(',', '', $total);
				                    $totalSum_ += (float)$tot;
				                    $totalSum  = round($totalSum_);
				     	   			$tbl .= "<td style='background-color: white; color: black;'>" .$total . "</td>";

			                       	$final_total_arr_yearly[$counter] += $tot; 
	                                $over_all_final_total_arr_yearly[$counter] += $tot; 
	                                $counter ++;
			                   	} // end foreach groupDetails
			                   	$tbl .= '</tr>';
			               	}
		              	} // end foreach groups

	              		$store_name = $this->Acct_mod->get_store_name($store);
	              		$tbl .='<div class="responsive-div_top" style="margin-top: 1px; margin-bottom: 10px;"><div class="line-separator"></div></div>';
			            $tbl .= "<h3 style='font-size: 23px;'>Yearly Quantity Report per Group => Store Name:".$store_name[0]['nav_store_val']."<h3>";
	                    $tbl .= '
				    			  <tfoot>
								    <tr style="color: white;">
								      <th style="position: sticky; left: 0; background: darkcyan;">Total</th>
								      <th style="position: sticky; left: 0; background: darkcyan;color: darkcyan;">Total</th>';
								        
	                                    for($a=0;$a<count($final_total_arr_yearly);$a++)
	                                    {            
	                                        
	                                        $tbl .= '<th style="color: white;">'.number_format($final_total_arr_yearly[$a]).'</th>';
	                                    } 
					                			      
					    $tbl .= '	</tr>
								 </tfoot>';

		                $tbl .= '</table>';
		                $tbl .= '<script>';
					    $tbl .= '$("#payments_group_table_qty'.$index.'").DataTable({})';
					    $tbl .='</script>';
		                echo $tbl;  
		                $index++;
		            } // end foreach store

				    $tbl2  = '<table border="1" class="table table-bordered table-responsive" id="payments_table_yearly" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; ">';
				    $tbl2 .= '<thead style="color:white;">';
	         	    $tbl2 .= "<tr>";
	                $tbl2 .= "<th style='color: #154351;'>Code</th>";
	                $tbl2 .= "<th style='color: #154351;'>Group Name</th>";
		            foreach ($year_filter as $year_) 
	           	    {
	      		        $tbl2 .= "<th style='color: white;'>".$year_."</th>";
	           	    }

	           	    $tbl2 .="</tr>";
				    $tbl2 .= '</thead>';

				    $tbl2 .= '
				           
				        <tr style="color: white;">';
							$tbl2 .= ' <td style="position: sticky; left: 0; background: darkcyan;">Grand Total</td>';
						    $tbl2 .= ' <td style="position: sticky; left: 0; background: darkcyan; color: darkcyan;">Grand Total</td>';
						        
	                        for($a=0;$a<count($over_all_final_total_arr_yearly);$a++)
	                        {            
	                            
	                            $tbl2 .= '<td style="color: black;">'.number_format($over_all_final_total_arr_yearly[$a]).'</td>';
	                        } 
					$tbl2 .= '</tr>';
				    $tbl2 .= '</table>';
				    $tbl2 .= '<script>';
				    $tbl2 .= '$("#payments_table_yearly").DataTable({ scrollX: true, lengthChange: false,searching: false,info: false});';
				    $tbl2 .= '</script>';

				    echo $tbl2;
	     		} // end for all store

	     		else{

		    	   	$groups     = array_unique(array_column($details_yearly, 'item_group')); // Get unique groups from the details array
		    	   	$store_name = $this->Acct_mod->get_store_name($store_no);
		    	   	$final_total_arr_yearly =  array();
				 	$row_total = 0;
			     
			       	foreach ($year_filter as $year_) {
			         	$row_total +=1;
			         	array_push($final_total_arr_yearly,0);
			        
			       	}
		    	   	$tbl  = '<table  class="table table-bordered table-responsive" id="payments_table" style="background-color: rgb(0, 68, 100); width: 100%;color: #0f0b0b; ">';
		            $tbl .= '<thead style="color:white;">';
		           // $tbl .= "<h4>Yearly Quantity Report per Group => Store Name:".$store_name[0]['nav_store_val']."<h4>";
		               	
		   	   		$tbl .= "<tr>";
		         	$tbl .= "<th>Code</th>";
		         	$tbl .= "<th>Group Name</th>";
		         	foreach ($year_filter as $year_) 
		 	    	{
		 	    	//var_dump($year_);
			        	$tbl .= "<th>".$year_."</th>";
		 	    	}
		 	    	$tbl .="</tr>";
		    		$tbl .= '</thead>';
		        	foreach ($groups as $group)
		        	{
		                 
			     	   	$counter = 0;
		            	$groupDetails = [];
		            	// Find the details for the current group
					    foreach ($details_yearly as $detail) {
					        if ($detail['item_group'] === $group) {
					            $groupDetails[] = $detail;
					        }
					    }


			     	   	if (!empty($groupDetails))
			     	   	{
			     	   		$group_name = $this->Acct_mod->get_group_name($group);
				     		$groups_name = (count($group_name)>0) ? $group_name[0]['group_name'] : 'No Group Name';
				     		$groups_code = (count($group_name)>0) ? $group : 'No Group Code';
				     		$tbl .= "<tr>";
				     		$tbl .= "<td>" . $group . "</td>";
				     		$tbl .= "<td>" . $groups_name . "</td>";

			     	   		foreach ($year_filter as $y) {
			     	   			$total = '0.00';
			     	   			foreach ($groupDetails as $detail) {
			     	   				if ($detail['year'] == $y) {
			     	   					$total = abs($detail['total_quantity_yearly']);
			                                   
			     	   					$total = round($total, 0); 
										$total = intval($total); // Convert to integer to remove decimal places
										$total = number_format($total, 0, '', ','); // Format as comma-separated number without decimal places
			     	   					break;
			     	   				}
			     	   			}
			     	   			$tot       = str_replace(',', '', $total);
			                    $totalSum_ += (float)$tot;
			                    $totalSum  = round($totalSum_);
			     	   			$tbl .= "<td>" .$total . "</td>";

			     	   			$final_total_arr_yearly[$counter] += $tot; 
		                        $counter ++;
			     	   		}


			     	   		$tbl .= '</tr>';
			     	   	}
		            }

	      			$store_name = $this->Acct_mod->get_store_name($store_no);
		      		$tbl .='<div class="responsive-div_top" style="margin-top: 1px; margin-bottom: 10px;"><div class="line-separator"></div></div>';
		            $tbl .= "<h3 style='font-size: 23px;'>Yearly Sales Report per Group => Store Name:".$store_name[0]['nav_store_val']."<h3>";
		            $tbl .= '
			    			  <tfoot>
							    <tr style="color: white;">
							      <th style="position: sticky; left: 0; background: darkcyan;">Total</th>
							      <th style="position: sticky; left: 0; background: darkcyan;color: darkcyan;">Total</th>';
							        
		                            for($a=0;$a<count($final_total_arr_yearly);$a++)
		                            {            
		                                
		                                $tbl .= '<th>'.number_format($final_total_arr_yearly[$a]).'</th>';
		                            } 
				                			      
				    $tbl .= '	</tr>
							 </tfoot>';
		          	$tbl .= '</table>';
			     	$tbl .= '<script>';
			     	$tbl .= '$("#payments_table").DataTable({})';
			     	$tbl .='</script>';
	             	echo $tbl;
	            }
	        }
	    }
	}
?>