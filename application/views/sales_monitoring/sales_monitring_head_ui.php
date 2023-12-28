<style>
    #view_records {
        overflow: auto;
    }


   .responsive-div_top{
      width: 100%; 
      max-width: 1600px; 
      height: 4px; 
      background-color: #0c6b99;
      position: relative;
    }

    #payments_table{
                    background-color: white;
                   }
</style>
 <div class="row">
      <h4 style="font-size: 25px;margin-top: -4px;margin-left: 27px;">Sales Comparison per Division</h4>
      <div class="col-sm-10"></div>
      <div class="col-sm-2">      
       
      </div>
 </div>

 <!-- SELECT DIVISION ======================================================================================================================= -->
 
 <div class="col-sm-2"style="border-right:1px solid black; margin-left: -6px; width: 160px;">
  <select class="form-control option" id="division_type" >
    <option value="">Division Type</option>  
    <option value="division">Division</option>
    <option value="no_division">No Division</option>
  </select>
</div>

 <!-- SELECT STORE ========================================================================================================================== -->

<div class="col-sm-2"style="border-right:1px solid black; margin-left: -8px; margin-left: 28px;">
  <select class="form-control option" id="select_store" style="width: 171px;margin-left: -24px;margin-top: 1px;" disabled></select>
</div> 

 <!-- SELECT DATE RANGE ===================================================================================================================== -->

<div class="col-sm-2"style="border-right:1px solid black; margin-left: 0px;">
  <select class="form-control option" id="select_range" disabled>
    <option value="">Select Date Range</option>
    <option value="Monthly">Monthly</option>
    <option value="Yearly">Yearly</option> 
  </select>
</div>

 <!-- SELECT YEAR =========================================================================================================================== -->

<div class="col-sm-2"style="border-right:1px solid black;">
  <select class="form-control option" id="year" disabled></select>
</div>

 <!-- SELECT REPORT TYPE ===================================================================================================================== -->

 <div class="col-sm-2"style="border-right:1px solid black; margin-left: -1px; width: 160px;">
  <select class="form-control option" id="report_type" disabled>
  <!-- <select class="form-control option" onchange="load_records();" id="report_type" disabled> -->
    <option value="">Report Type</option>  
    <option value="sales">Total Sales</option>
    <option value="quantity">Total Quantity</option>
    <option value="sales_quantity">Sales and Quantity</option>
  
  </select>
</div>

 <!-- BUTTON GENERATE REPORT ================================================================================================================= -->
 
<div class="col-sm-2  " style="padding-top: 0px; margin-left: ; margin-left: 13px; margin-top: -25px;">
      <input style="margin-left: 6px;" type="checkbox" id="check_box" name="vehicle1" value="check">
      <label for="vehicle1">Check to Export Excel</label><br>
      <button class="btn btn-md btn-danger" style="margin-left: 5px;" id="go" disabled>GENERATE REPORT</button>
</div>


<div class="col-md-12" id="view_records" style="margin-top: 5px; font-size: 14px;"></div> 


 <!-- ========================================================================================================================================= -->
 <script>

// ============================================================================================================================================
// function load select option..............................
     function load_select_options()
     {
        $("#go").html('<img src="<?php echo base_url(); ?>assets/loader/loading-gif.gif" width="20" heigth="20">');
        $.ajax({
                  type   :'post',
                  url    :'<?php echo base_url(); ?>Sale_monitoring_ctrl/get_stores_and_years',      
                  success: function(data)
                            {
                               $("#go").html('GENERATE REPORT');
                               var jObj = JSON.parse(data);


                               var html = '<option value="">Select Store</option>';
                               for(var c=0; c<jObj[0].length; c++){
                                if(jObj[0][c].store!='')
                                  html += '<option value="'+jObj[0][c].store+'">'+jObj[0][c].nav_store_val+'</option>';
                               }
                                  html += '<option value="Select_all_store">Select All Store</option>';

                               $("#select_store").html(html);

                               html = '<option value="">Select Year</option>';
                               for(var c=0; c<jObj[1].length; c++){
                                if(jObj[1][c].year!='0')
                                  html += '<option value="'+jObj[1][c].year+'">'+jObj[1][c].year+'</option>';
                               }

                               $("#year").html(html);

                            }      

            });
      }

      load_select_options();
    
// ============================================================================================================================================

     $('#select_range').on("change", function(event)
     {
        var report_type  = $("#report_type").val();
        var range        = $("#select_range").val();
        var year         = $("#year").val();
        var division     = $("#division_type").val();
        var select_store = $("#select_store").val();

        if(report_type != '' && select_store != '' && division != '' && division != '' && year != '')
        {
         //load_records();   
        }else if(division == 'no_division'){
                  if(select_store == '' && year != '' && report_type != '')
                  {
                   //load_records();   
                  }
                }

      if(range == 'Monthly')
      {
        if(report_type == '' || year == '')
        {
          document.getElementById("year").disabled        = false;
          document.getElementById("report_type").disabled = false;
          document.getElementById("go").disabled          = true;
        }else{
              document.getElementById("year").disabled           = false;
              document.getElementById("report_type").disabled    = false;
              document.getElementById("go").disabled             = false;
             }
       
      }else if(range == 'Yearly')
             {
              document.getElementById("report_type").disabled      = false;
              document.getElementById("year").disabled             = false;
             }else{
                   document.getElementById("year").disabled        = true;
                   document.getElementById("report_type").disabled = true;
                   document.getElementById("go").disabled          = true;
                  }

     })

// ============================================================================================================================================
     $("#select_store").on("change", function(event)
     {
        var store = $("#select_store").val();

        var division     = $("#division_type").val();
        var report_type  = $("#report_type").val();
        var select_range = $("#select_range").val();
        var year         = $("#year").val();

        if(division != '' && report_type != '' && select_range != '' && year != '')
        {
         //load_records();   
        }


        if(store == '')
        {
         document.getElementById("year").disabled              = true;
         document.getElementById("report_type").disabled       = true;
         document.getElementById("go").disabled                = true;
         document.getElementById("select_range").disabled      = true;
        }else{
              document.getElementById("select_range").disabled = false;
             }
     });

// ============================================================================================================================================
     $("#division_type").on("change", function(event)
     {

       var get_store    = $("#get_store").val();
       var division     = $("#division_type").val();
       var report_type  = $("#report_type").val();
       var select_store = $("#select_store").val();
       var select_range = $("#select_range").val();
       var year         = $("#year").val();

        if(report_type != '' && select_store != '' && select_range != '' && year != '')
        {
         //load_records();   
        }


         if(division == '')
        {
            document.getElementById("go").disabled  = true;
        }else if(division == 'no_division')
             {
                 document.getElementById("select_store").disabled  = true;
                 document.getElementById("year").disabled  = false;
                 document.getElementById("report_type").disabled  = false;
                 document.getElementById("select_range").disabled  = false;
             }else{
                    document.getElementById("year").disabled              = false;
                    document.getElementById("report_type").disabled       = false;
                    document.getElementById("select_range").disabled      = false;
                    document.getElementById("select_store").disabled      = false;

                  }
     });
// ============================================================================================================================================    
     $("#year").on("change", function(event)
     {
         var year           = $("#year").val();
         var select_range   = $("#select_range").val();
         var report_type    = $("#report_type").val();
         var division_type  = $("#division_type").val();
         var select_store   = $("#select_store").val();
  
          if(report_type != '' && select_store != '' && select_range != '' && division_type != '')
          {
           //load_records();   
          }else if(division_type == 'no_division'){
                     if(select_store == '' && select_range != '' && report_type != '')
                      {
                       //load_records();   
                      }
                  }


         if(year == '')
         {
           document.getElementById("go").disabled  = true; 

         }else{

                if(select_range == '' || report_type == '')
                {
                  document.getElementById("go").disabled  = true; 
                }else{

                       if(division_type == 'no_division')
                       {
                        document.getElementById("go").disabled             = false;
                        document.getElementById("report_type").disabled    = false;
                        document.getElementById("select_range").disabled   = false;
                        document.getElementById("select_store").disabled   = true;
                       }else{
                            document.getElementById("go").disabled             = false;
                            document.getElementById("report_type").disabled    = false;
                            document.getElementById("select_range").disabled   = false;
                            document.getElementById("select_store").disabled   = false;
                            }
                     }
              }
     });

// ============================================================================================================================================
     $("#report_type").on("change", function(event)
     {


        var range         = $("#select_range").val();
        var report_type   = $("#report_type").val();
        var year          = $("#year").val();
        var select_store  = $("#select_store").val();
        var division_type = $("#division_type").val();
        

        if(range != '' && select_store != '' && division_type != '' && year != '')
        {
         //load_records();   
        }else if(division_type == 'no_division'){
                     if(select_store == '' && select_range != '' && year != '')
                      {
                       //load_records();   
                      }
                  }

          if(report_type == '')
          {
           document.getElementById("go").disabled  = true; 
          }else{
                    if(division_type == 'no_division')
                    {
                       if(select_store == '')
                       {
                       document.getElementById("go").disabled  = false;  
                       document.getElementById("select_store").disabled  = true;            
                       }
                    }

                    if(division_type == '')
                    {
                     document.getElementById("go").disabled  = true;  
                    }

                    if(range == '' || year == '')
                    {
                      document.getElementById("go").disabled  = true;  
                    }else{

                          document.getElementById("year").disabled  = false;
                          document.getElementById("report_type").disabled  = false;
                          document.getElementById("go").disabled  = false;
                         }
               }
     });

   

     var checkbox = document.getElementById("check_box");
     // function download the data to excel ..........................................
     $("#go").on("click", function(event)
     { 

        var check_box = '';
        if (checkbox.checked) 
        {              
          check_box = 'check';      
        } else {
                 check_box = 'uncheck';        
               }
         var range        = $("#select_range").val();
         var store        = $("#select_store").val();
         var year         = $("#year").val();
         var month        = $("#month").val();
         var report_type  = $("#report_type").val();
         var division     = $("#division_type").val();
        
                     if(range == 'Monthly')
                     {
                 
                       if(store == 'Select_all_store')
                       {
                          // ajax get yearly report sales and quantity all store .......................................
                           loader();
                           $.ajax({
                                    type:'post',
                                    url:'<?php echo base_url(); ?>Sale_monitoring_ctrl/view_yearly_montly_report',
                                    data:{
                                          'range'       :range,
                                          'store'       :store,
                                          'year'        :year,
                                          'report_type' :report_type, 
                                          'division'    :division
                                         },       
                                   success: function(response)
                                                            {

                                                                if(check_box == 'check')
                                                                {

                                                                 Swal.close();
                                                                 var blob      = new Blob([response], { type: 'application/vnd.ms-excel' }); 
                                                                 var url       = URL.createObjectURL(blob);                                  
                                                                 var link      = document.createElement('a');                                        
                                                                 link.href     = url;                                                   
                                                                 link.download = 'Sale Montly and Yearly Report.xls';                        
                                                                 document.body.appendChild(link);                                       
                                                                 link.click();                                                          
                                                                 document.body.removeChild(link);
                                                                 
                                                                 $('div#view_records').html(response);

                                                                }else{
                                                                      Swal.close();
                                                                      $('div#view_records').html(response);
                                                                      }
                                                            }      

                                 });

                       }else{ // range condition


                               // ajax get yearly report sales and quantity.............................................
                               loader();
                                $.ajax({
                                        type: 'post',
                                        url:'<?php echo base_url(); ?>Sale_monitoring_ctrl/view_yearly_montly_report',
                                        data: {
                                              'range'      : range,
                                              'store'      : store,
                                              'year'       : year,
                                              'report_type': report_type,
                                              'division'   : division
                                              },
                                        success: function (response)
                                                          {
                                                            if(check_box == 'check')
                                                            {

                                                             Swal.close();
                                                             disabled_edit_in_excel(response);    
                                                             $('div#view_records').html(response);

                                                            }else{
                                                                   Swal.close();
                                                                   $('div#view_records').html(response);  
                                                                 }  
                                                          }
                                    });


                            } // end of range condition ..........................................



                 }else{ 



                        if(store == 'Select_all_store')
                        {
                            // ajax get yearly report sales and quantity all store ........................................
                            loader();
                            $.ajax({
                                      type:'post',
                                      url:'<?php echo base_url(); ?>Sale_monitoring_ctrl/view_yearly_report',
                                      data:{
                                            'range'       :range,
                                            'store'       :store,
                                            'year'        :year,
                                            'report_type' :report_type,
                                            'division'    :division
                                           },
                                     success: function(response){
                                                                 if(check_box == 'check')
                                                                 {
     
                                                                  Swal.close();
                                                                  var blob      = new Blob([response], { type: 'application/vnd.ms-excel' }); 
                                                                  var url       = URL.createObjectURL(blob);                                  
                                                                  var link      = document.createElement('a');                                        
                                                                  link.href     = url;                                                   
                                                                  link.download = 'Sale Montly and Yearly Report.xls';                        
                                                                  document.body.appendChild(link);                                       
                                                                  link.click();                                                          
                                                                  document.body.removeChild(link);
                                                                  $('div#view_records').html(response);
     
                                                                 }else{
                                                                       Swal.close();
                                                                        $('div#view_records').html(response);
                                                                  }
                                                                }      

                                  }); 
                        }else{

                                // ajax get yearly report sales and quantity ..................................................
                                loader();
                                $.ajax({
                                          type:'post',
                                          url:'<?php echo base_url(); ?>Sale_monitoring_ctrl/view_yearly_report',
                                          data:{
                                                'range'       :range,
                                                'store'       :store,
                                                'year'        :year,
                                                'report_type' :report_type,
                                                'division'    :division
                                               },
                                         success: function(response)
                                                    {
                                                     if(check_box == 'check')
                                                     {
                                                      Swal.close();
                                                      disabled_edit_in_excel(response);    
                                                      $('div#view_records').html(response);  
                                                      }else{
                                                            Swal.close();
                                                            $('div#view_records').html(response);  
                                                           }  
                                                    }    

                                        });

                                 } // end of else select all store ......................

                      } //end of else ......................
  
     });
  
  


    function loader()
    {           
      Swal.fire({
                 imageUrl: '<?php echo base_url(); ?>assets/mms/images/Cube-1s-200px.svg',
                 imageHeight: 203,
                 imageAlt: 'loading',
                 text: 'loading, please wait',
                 allowOutsideClick:false,
                 showCancelButton: false,
                 showConfirmButton: false
               })              
    } 

    function disabled_edit_in_excel(response){
        var blob = new Blob([response], { type: 'application/vnd.ms-excel' });
        var url  = URL.createObjectURL(blob);
        var link = document.createElement('a');

        link.href = url;

        // Protect the downloaded Excel file.....................................
        setTimeout(function() {
          protectExcelFile(url);
        }, 100);


       function protectExcelFile(fileUrl)
       {

            var xhr = new XMLHttpRequest();
            xhr.open('GET', fileUrl, true);
            xhr.responseType = 'arraybuffer';
        
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var arrayBuffer = xhr.response;
                    var data = new Uint8Array(arrayBuffer);
                    var workbook = XLSX.read(data, { type: 'array' });

        
                    // Assuming the first sheet in the workbook is the one to be protected.........................................
                    var sheetName = workbook.SheetNames[0];
                    var sheet = workbook.Sheets[sheetName];

                    //Set the sheet protection options..............................
                    sheet['!protect'] = {
                                         password: 'herbert',
                                         formatCells: false,
                                         formatColumns: false,
                                         formatRows: false,
                                         insertColumns: false,
                                         insertRows: false,
                                         insertHyperlinks: false,
                                         deleteColumns: false,
                                         deleteRows: false,
                                         selectLockedCells: true,
                                         selectUnlockedCells: true,
                                         sort: false,
                                         autoFilter: false,
                                         pivotTables: false,
                                         objects: true,
                                         scenarios: true,
                                         sheet: false
                                       };


                   // Auto adjust column sizes.....................................
                    var range = XLSX.utils.decode_range(sheet['!ref']);
                    var columnWidths = [];
                    for (var col = range.s.c; col <= range.e.c; col++) {
                        var maxWidth = 0;
                        for (var row = range.s.r + 1; row <= range.e.r; row++) {
                            var cellAddress = XLSX.utils.encode_cell({ r: row, c: col });
                            var cell = sheet[cellAddress];
                            if (cell && cell.v) {
                                var contentLength = cell.v.toString().length;
                                if (contentLength > maxWidth) {
                                    maxWidth = contentLength;

                                } // end contentlength if condition

                             } // end of cell if condition
                           
                         } // end of range row for loop

                        columnWidths[col] = { width: maxWidth + 1 };

                    } // end of range for loop 

                    sheet['!cols'] = columnWidths;
                   // ========================================================================================================================
        
                    var newWorkbook = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(newWorkbook, sheet, 'Sheet 1');
        
                    var wbout = XLSX.write(newWorkbook, { bookType: 'xlsx', type: 'binary' });
                    var s2ab = function (s) {
                        var buf = new ArrayBuffer(s.length);
                        var view = new Uint8Array(buf);
                        for (var i = 0; i < s.length; i++) {
                            view[i] = s.charCodeAt(i) & 0xff;

                        } // end of view for loop

                        return buf;

                    }; // end of s2ab function 

                   // ========================================================================================================================
        
                    var blob = new Blob([s2ab(wbout)], { type: 'application/octet-stream' });
                    var url = URL.createObjectURL(blob);
        
                    var link = document.createElement('a');
                    link.href = url;
                    link.download = 'Sale Monthly and Yearly Report.xlsx';
                    document.body.appendChild(link);   
                    link.click();
                    document.body.removeChild(link);  

                } // end of xhr.status if condition 

            }; // end of onload function

            xhr.send();

        } // end of protectExcelFile function 
    }


 </script>