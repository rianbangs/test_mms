<?php

	function view_yearly_montly_report()
	{

		// View Sales in report_type condition..............................
		if($report_type == 'sales')
		{

			// division type view sales.................................
			if($division_type == 'division')
	          {

	             // this if condition display all stores with division sales...................................
	           	 if($store == 'Select_all_store')
			     {
			       // code...........

			     }

			  // View All store with no division Sales .................................
	          }else{

	               }

		// View quantity in $report_type variable............
		}else if($report_type == 'quantity')
		        {


							// this division_type condition display store with division quantity.................... 
				            if($division_type == 'division')
				            {


				            	  // function view all store and there total quantity .....................................................................
					              if($store == 'Select_all_store')
					              {


					              }else{// else View Table Division Per Store total Quantity ................................

					              	
					              	   }


							// else division_type condition no division .......................................
				            }else{

				                 }




		     }

	}
?>