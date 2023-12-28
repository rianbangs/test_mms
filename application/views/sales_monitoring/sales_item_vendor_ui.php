    
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
    </style>

    

    <div class="col-sm-12" style="display: flex; font-size: 14px;">
    <label style="margin-right: 20px;">
        <input type="radio" name="report_option" id="report_option_1" value="1"> Sales Comparison per Category per Vendor
    </label>
    <label>
        <input type="radio" name="report_option" id="report_option_2" value="2"> Sales Report per GPR per Supplier per Subsidiary
    </label>
    </div>


    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
     <script>
        $(document).ready(function() {
            $("input[name='report_option']").on("change", function() {
                var report = $("input[name='report_option']:checked").val();
                if(report == '1'){
                $("#cat").fadeIn();
                $("#sup").hide();
            }else{
                $("#sup").fadeIn();
                $("#cat").hide();
            }
            });
        });
    </script> 

    <br><br>

    <div class="row" id="cat" >
        <div class="row">
            <h4 style="padding-left: 20px;">Sales Comparison per Category per Vendor</h4>
            <div class="col-sm-10"></div>
            <div class="col-sm-2"></div>
        </div>
     
        <div class="col-sm-2"style="border-right:1px solid black; margin-left: -8px; width: 120px;">
            <select class="form-control option" id="select_store" ></select>
        </div>

        <div class="col-sm-2"style="border-right:1px solid black; margin-left: 0px; width: 120px;">
            <select class="form-control option" id="select_category" disabled>
                <option>Select Category</option>
                <option value="dept">Department Code</option>
                <option value="group">Group Code</option> 
            </select>
        </div>

        <div class="col-sm-2"style="border-right:1px solid black; margin-left: -1px; width: 150px;" id="dept_cat" hidden>
            <select style="width: 120px; height: 34px; padding: 6px 12px; font-size: 14px; line-height: 1.42857143;" class="form-control select-dept option" id="select_department" disabled></select>
        </div>

        <div class="col-sm-2"style="border-right:1px solid black; margin-left: -1px; width: 150px;" id="group_cat" hidden>
            <select style="width: 120px; height: 34px; padding: 6px 12px; font-size: 14px; line-height: 1.42857143;" class="form-control select-group option" id="select_group" disabled></select>
        </div>

        <div class="col-sm-2"style="border-right:1px solid black; margin-left: 0px; width: 120px; ">
            <select class="form-control option" id="select_range" disabled>
                <option>Select Date Range</option>
                <option value="Monthly">Monthly</option>
                <option value="Yearly">Yearly</option> 
            </select>
        </div>

       
        <div class="col-sm-2"style="border-right:1px solid black;margin-left: -1px; width: 120px; ">
            <select class="form-control option" id="year" disabled></select>
        </div>

        <div class="col-sm-2"style="border-right:1px solid black;  margin-left: -1px; width: 120px;">
            <select class="form-control option" id="report_type" disabled>
                <option value="">Report Type</option>  
                <option value="sales">Total Sales</option>
                <option value="quantity">Total Quantity</option>
                <option value="both">Total Sales & Quantity</option>
            </select>
        </div>

        <div class="col-sm-2" style="padding-top: 0px; margin-left: 13px; margin-bottom: 15px;">
            <button class="btn btn-md btn-danger" style="margin-left: -14px;" id="go" disabled>EXPORT EXCEL FILE</button>
        </div> 

        <!-- <div class="responsive-div_top" style="margin-top: 55px; margin-bottom: 10px;">
            <div class="line-separator"></div>
        </div> -->

        <div class="col-md-12" id="view_records" style="margin-top: -17px; font-size: 14px;"></div> 
    </div>

    <div class="row" id="sup" hidden>
        <div class="row">
            <h4 style="padding-left: 20px;">Sales Report per GPR per Supplier per Subsidiary</h4>
            <div class="col-sm-10"></div>
            <div class="col-sm-2"></div>
        </div>
     
        <div class="col-sm-2"style="border-right:1px solid black; margin-left: 0px; width: 120px;" id="month_cat">
            <select class="form-control option" name="select_month2" id="select_month2" >
                <option value="1">January</option>
                <option value="2">February</option>
                <option value="3">March</option>
                <option value="4">April</option>
                <option value="5">May</option>
                <option value="6">June</option>
                <option value="7">July</option>
                <option value="8">August</option>
                <option value="9">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option> 
            </select>
        </div>

        <div class="col-sm-2"style="border-right:1px solid black;margin-left: -1px; width: 120px; ">
            <select class="form-control option" id="year2" ></select>
        </div>

        <div class="col-sm-2" style="padding-top: 0px; margin-left: 13px; margin-bottom: 15px;">
            <button class="btn btn-md btn-danger" style="margin-left: -14px;" id="go2" >GENERATE</button>
        </div> 

       <!--  <div class="responsive-div_top" style="margin-top: 55px; margin-bottom: 10px;">
            <div class="line-separator"></div>
        </div> -->

        <div class="row">
            <div class="col-12 table-responsive" style="padding: 20px;">
                <table id="vendor-table" class="table table-striped table-bordered table-responsive" style="background-color: rgb(5, 68, 104);">
                    <thead style="text-align: center;color:white;">
                        <th>ITEM CODE</th>
                        <th>STORE</th>
                        <th>TOTAL</th>
                        
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>     
        </div>
    </div>
    <script src="<?=base_url()?>assets/js/xlsx.js"></script>
    <!-- <script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script> -->
    <script>

        

        function load_select_options(){
            $("#go").html('<img src="<?php echo base_url(); ?>assets/loader/loading-gif.gif" width="20" heigth="20">');
            $.ajax({
                type:'post',
                url:'<?php echo base_url(); ?>Sale_monitoring_ctrl/get_stores_and_years',      
                success: function(data)
                {
                    $("#go").html('EXPORT EXCEL FILE');
                    var jObj = JSON.parse(data);

                    var html = '<option value="">Select Store</option><option value="Select_all_store">Select All Stores</option>';
                    for(var c=0; c<jObj[0].length; c++){
                        if(jObj[0][c].store!='')
                        html += '<option value="'+jObj[0][c].store+'">'+jObj[0][c].store+'</option>';
                    }

                    $("#select_store").html(html);

                    html = '<option value="">Select Year</option>';
                    for(var c=0; c<jObj[1].length; c++){
                        if(jObj[1][c].year!='0')
                        html += '<option value="'+jObj[1][c].year+'">'+jObj[1][c].year+'</option>';
                    }

                    $("#year").html(html);
                    $("#year2").html(html);
                }     
            });
        }

        load_select_options();

        function load_select_options_(){
        
            $.ajax({
                type:'post',
                url:'<?php echo base_url(); ?>Sales_ctrl/load_dept_and_group',      
                success: function(data)
                {
                    var jObj = JSON.parse(data);

                    var html = '<option value="">Select Department</option>';
                    for(var c=0; c<jObj[0].length; c++){

                        html += '<option value="'+jObj[0][c].dept_code+'">'+jObj[0][c].dept_code+' - '+jObj[0][c].dept_name+'</option>';
                    }

                    $("#select_department").html(html);

                    html = '<option value="">Select Year</option>';
                    for(var c=0; c<jObj[1].length; c++){
                    
                        html += '<option value="'+jObj[1][c].group_code+'">'+jObj[1][c].group_code+' - '+jObj[1][c].group_name+'</option>';
                    }

                    $("#select_group").html(html);
                }      
            });
        }

        load_select_options_();

        function load_records()
        {
            var category     = $("#select_category").val();
            var range        = $("#select_range").val();
            var store_no     = $("#select_store").val();
            var year         = $("#year").val();
            var month        = $("#month").val();
            var report_type  = $("#report_type").val();
              
            if(category == 'dept'){
                var code      = $('#select_department').select2("val");
            }else{
                var code      = $('#select_group').select2("val");
            }
            
            if(range == 'Monthly')
            {
                loader();
                $.ajax({
                    type:'post',
                    url:'<?php echo base_url(); ?>Sales_item_vendor_ctrl/view_yearly_monthly_report',
                    data:{
                        'range'       :range,
                        'store_no'    :store_no,
                        'year'        :year,
                        'report_type' :report_type, 
                        'category'    :category, 
                        'code'        :code
                        },       
                    success: function(data)
                                          {
                                           Swal.close();
                                           var tempDiv = $('<div>').html(data);
                                           var h3Content = tempDiv.find('h3').html();
                                           var total = tempDiv.find('h3').html();
                                           
                                           $('#header').html(h3Content)
                                           $('#total').html(total)
                                           $('div#view_records').html(data);

                                          }      

                });
            }else{

                loader();
                $.ajax({
                    type:'post',
                    url:'<?php echo base_url(); ?>Sales_item_vendor_ctrl/view_yearly_report',
                    data:{
                        'range'       :range,
                        'store_no'    :store_no,
                        'year'        :year,
                        'report_type' :report_type, 
                        'category'    :category, 
                        'code'        :code
                        },
                    success: function(response){
                                              Swal.close();
                                                  var tempDiv = $('<div>').html(response);
                                                  var h3Content = tempDiv.find('h3').html();
                                                  var total = tempDiv.find('h3').html();
                                                  $('#header').html(h3Content)
                                                  $('#header2').html(h3Content)
                                                  $('#total').html(total)
                                                  $('div#view_records').html(response);


                                            }      

                      });
              }
        }

        $('#select_range').on("change", function(event)
        {

            var category     = $("#select_category").val();
            var range        = $("#select_range").val();
            var store_no     = $("#select_store").val();
            var year         = $("#year").val();
            var month        = $("#month").val();
            var report_type  = $("#report_type").val();

            if(category == 'dept'){
                var code      = $('#select_department').select2("val");
            }else{
                var code      = $('#select_group').select2("val");
            }

            if(range != '' && store_no != '' && year != '' && month != '' && report_type != '' && category != '' && code != '')
            {
                load_records();   
            }
            if(range == 'Monthly')
            {
                document.getElementById("year").disabled             = false;
                document.getElementById("report_type").disabled      = false;
                //document.getElementById("go").disabled             = false;
            }else if(range == 'Yearly'){
                document.getElementById("report_type").disabled      = false;
                document.getElementById("year").disabled             = false;
                //document.getElementById("go").disabled             = false;
            }else{
                document.getElementById("year").disabled             = false;
                document.getElementById("report_type").disabled      = true;
                document.getElementById("go").disabled               = true;
            }
        })

        $("#select_store").on("change", function(event)
        {
            
            var category     = $("#select_category").val();
            var range        = $("#select_range").val();
            var store_no     = $("#select_store").val();
            var year         = $("#year").val();
            var month        = $("#month").val();
            var report_type  = $("#report_type").val();

            if(category == 'dept'){
                var code      = $('#select_department').select2("val");
            }else{
                var code      = $('#select_group').select2("val");
            }

            if(range != '' && store_no != '' && year != '' && month != '' && report_type != '' && category != '' && code != '')
            {
                load_records();   
            }



            if(store_no == '')
            {
                document.getElementById("year").disabled              = true;
                document.getElementById("report_type").disabled       = true;
                document.getElementById("go").disabled                = true;
                document.getElementById("select_range").disabled      = true;
                document.getElementById("select_department").disabled = true;
            }
            else if(store_no == 'Select_all_store')
            {
                document.getElementById("select_category").disabled = false;
                document.getElementById("select_range").disabled      = true;
                //document.getElementById("select_month").disabled      = false;
                $("#month_cat").fadeIn();
            }else{
                document.getElementById("select_category").disabled = false;
            }
        });

        $("#select_category").on("change", function(event)
        {
            
            var category     = $("#select_category").val();
            var range        = $("#select_range").val();
            var store_no     = $("#select_store").val();
            var year         = $("#year").val();
            var month        = $("#month").val();
            var report_type  = $("#report_type").val();

            if(category == 'dept'){
                var code      = $('#select_department').select2("val");
            }else{
                var code      = $('#select_group').select2("val");
            }

            if(range != '' && store_no != '' && year != '' && month != '' && report_type != '' && category != '' && code != '')
            {
                load_records();   
            }

            if(category == '')
            {
                document.getElementById("year").disabled              = true;
                document.getElementById("report_type").disabled       = true;
                document.getElementById("go").disabled                = true;
                document.getElementById("select_range").disabled      = true;
                document.getElementById("select_department").disabled = true;
            }else if(category == 'dept'){
                $("#dept_cat").fadeIn();
                document.getElementById("select_department").disabled      = false;
                document.getElementById("select_group").disabled      = true;
                document.getElementById("select_range").disabled      = true;
                document.getElementById("year").disabled              = true;
                $("#group_cat").hide();
                //document.getElementById("go").disabled             = false;
            }else if(category == 'group'){
                $("#group_cat").fadeIn();
                document.getElementById("select_group").disabled      = false;
                document.getElementById("select_department").disabled      = true;
                document.getElementById("select_range").disabled      = true;
                document.getElementById("year").disabled              = true;
                $("#dept_cat").hide();
            }else{
                document.getElementById("select_range").disabled = false;
            }
        });

        $("#select_department").on("change", function(event)
        {
            
            var category     = $("#select_category").val();
            var range        = $("#select_range").val();
            var store_no     = $("#select_store").val();
            var year         = $("#year").val();
            var month        = $("#month").val();
            var report_type  = $("#report_type").val();
            var code         = $('#select_department').select2("val");
            
            if(range != '' && store_no != '' && year != '' && month != '' && report_type != '' && category != '' && code != '')
            {
                load_records();   
            }

            if(code == '')
            {
                document.getElementById("year").disabled              = true;
                document.getElementById("report_type").disabled       = true;
                document.getElementById("go").disabled                = true;
                document.getElementById("select_range").disabled      = true;
                //document.getElementById("select_department").disabled = true;
            }else{
                document.getElementById("select_range").disabled = false;
            }
        });

        $("#select_group").on("change", function(event)
        {
            
            var category     = $("#select_category").val();
            var range        = $("#select_range").val();
            var store_no     = $("#select_store").val();
            var year         = $("#year").val();
            var month        = $("#month").val();
            var report_type  = $("#report_type").val();
            var code         = $('#select_group').select2("val");
            
            if(range != '' && store_no != '' && year != '' && month != '' && report_type != '' && category != '' && code != '')
            {
                load_records();   
            }

            if(code == '')
            {
                document.getElementById("year").disabled              = true;
                document.getElementById("report_type").disabled       = true;
                document.getElementById("go").disabled                = true;
                document.getElementById("select_range").disabled      = true;
                //document.getElementById("select_department").disabled = true;
            }else{
                document.getElementById("select_range").disabled = false;
            }
        });

        $("#report_type").on("change", function(event)
        {
            
            var category     = $("#select_category").val();
            var range        = $("#select_range").val();
            var store_no     = $("#select_store").val();
            var year         = $("#year").val();
            var month        = $("#month").val();
            var report_type  = $("#report_type").val();

            if(category == 'dept'){
                var code      = $('#select_department').select2("val");
            }else{
                var code      = $('#select_group').select2("val");
            }

            if(range != '' && store_no != '' && year != '' && month != '' && report_type != '' && category != '' && code != '')
            {
                load_records();   
            }

            if(report_type == '')
            {
                document.getElementById("go").disabled  = true; 
            }else{
                document.getElementById("go").disabled  = false;
            }
        });

        $("#go").on("click", function(event)
        { 
            var category     = $("#select_category").val();
            var range        = $("#select_range").val();
            var store_no     = $("#select_store").val();
            var year         = $("#year").val();
            var month        = $("#month").val();
            var report_type  = $("#report_type").val();
              
            if(category == 'dept'){
                var code      = $('#select_department').select2("val");
            }else{
                var code      = $('#select_group').select2("val");
            }
         
            if(range == 'Monthly')
            {
                if (store_no == "Select_all_store") {
                    loader();
                    $.ajax({
                        type:'post',
                        url:'<?php echo base_url(); ?>Sales_item_vendor_ctrl/view_yearly_monthly_report',
                        data:{
                            'range'       :range,
                            'store_no'    :store_no,
                            'year'        :year,
                            'report_type' :report_type, 
                            'category'    :category, 
                            'code'        :code 
                              
                        },       
                        success: function(response)
                        {
                            Swal.close();
                            var blob      = new Blob([response], { type: 'application/vnd.ms-excel' }); 
                            var url       = URL.createObjectURL(blob);                                  
                            var link      = document.createElement('a');                                        
                            link.href     = url;                                                   
                            
                            if(report_type == 'both'){
                                report_type = 'Sales and Quantity';
                            }
                            var cat = (category == 'dept') ? "Department" : "Group";
                            report_type = report_type.charAt(0).toUpperCase() + report_type.slice(1).toLowerCase();
                            link.download = 'Monthly ' + report_type + ' Report per ' + cat + ' per Supplier.xls';

                            document.body.appendChild(link);                                       
                            link.click();                                                          
                            document.body.removeChild(link);
                        }      
                    });
                }
                // condition for per store
                else{
                    loader();
                    $.ajax({
                        type:'post',
                        url:'<?php echo base_url(); ?>Sales_item_vendor_ctrl/view_yearly_monthly_report',
                        data:{
                            'range'       :range,
                            'store_no'    :store_no,
                            'year'        :year,
                            'report_type' :report_type, 
                            'category'    :category, 
                            'code'        :code
                        },       
                        success: function(response){
                            Swal.close();
                                    
                            var blob = new Blob([response], { type: 'application/vnd.ms-excel' });
                            var url = URL.createObjectURL(blob);
                            var link = document.createElement('a');

                            link.href = url;

                            // Protect the downloaded Excel file
                            setTimeout(function() {
                              protectExcelFile(url);
                            }, 100);

                            function protectExcelFile(fileUrl) {
                                var xhr = new XMLHttpRequest();
                                xhr.open('GET', fileUrl, true);
                                xhr.responseType = 'arraybuffer';

                                xhr.onload = function() {
                                    if (xhr.status === 200) {
                                        var arrayBuffer = xhr.response;
                                        //console.log(arrayBuffer);

                                        var data = new Uint8Array(arrayBuffer);
                                        var workbook = XLSX.read(data, { type: 'array' });

                                        //console.log(workbook);
                                        // Assuming the first sheet in the workbook is the one to be protected
                                        var sheetName = workbook.SheetNames[0];
                                        var sheet = workbook.Sheets[sheetName];

                                        // Set the sheet protection options
                                        sheet['!protect'] = {
                                            password: '12345678',
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

                                        // Initialize the cols array
                                        sheet['!cols'] = [];

                                        // Auto adjust column sizes
                                        var range = XLSX.utils.decode_range(sheet['!ref']);
                                        var columnWidths = [];
                                        for (var col = range.s.c + 1; col <= range.e.c; col++) {
                                            var maxWidth = 0;
                                            for (var row = range.s.r; row <= range.e.r; row++) {
                                                var cellAddress = XLSX.utils.encode_cell({ r: row, c: col });
                                                var cell = sheet[cellAddress];
                                                if (cell && cell.v) {
                                                    var contentLength = cell.v.toString().length;
                                                    if (contentLength > maxWidth) {
                                                        maxWidth = contentLength;
                                                    }
                                                }
                                               
                                            }
                                            columnWidths[col] = { width: maxWidth + 2 };
                                        }

                                        // Set the first 4 rows to bold
                                        for (var row = range.s.r; row <= range.s.r + 3; row++) {
                                            for (var col = range.s.c; col <= range.e.c; col++) {
                                                var cellAddress = XLSX.utils.encode_cell({ r: row, c: col });
                                                var cell = sheet[cellAddress];
                                                if (cell && cell.v) {
                                                    if (!cell.s) cell.s = {};
                                                    if (!cell.s.font) cell.s.font = {};
                                                    cell.s.font.bold = true;
                                                }
                                            }
                                        }

                                        sheet['!cols'] = columnWidths;


                                        var workbook = XLSX.utils.book_new();
                                        XLSX.utils.book_append_sheet(workbook, sheet, 'Sheet 1');

                                        var wbout = XLSX.write(workbook, { bookType: 'xlsx', type: 'binary' });
                                        var s2ab = function (s) {
                                        var buf = new ArrayBuffer(s.length);
                                        var view = new Uint8Array(buf);
                                            for (var i = 0; i < s.length; i++) {
                                                view[i] = s.charCodeAt(i) & 0xff;
                                            }
                                            return buf;
                                        };

                                        var blob = new Blob([s2ab(wbout)], { type: 'application/octet-stream' });
                                        var url = URL.createObjectURL(blob);

                                        var link = document.createElement('a');
                                        link.href = url;
                                        if(report_type == 'both'){
                                            report_type = 'Sales and Quantity';
                                        }
                                        var cat = (category == 'dept') ? "Department" : "Group";
                                        report_type = report_type.charAt(0).toUpperCase() + report_type.slice(1).toLowerCase();
                                        link.download = 'Monthly ' + report_type + ' Report per ' + cat + ' per Supplier.xlsx';
                                        link.click();
                                    }
                                };
                                xhr.send();
                            }
                        }                              
                    });
                }
            }
            else{

                if (store_no == "Select_all_store") {
                    loader();
                    $.ajax({
                        type:'post',
                        url:'<?php echo base_url(); ?>Sales_item_vendor_ctrl/view_yearly_report',
                        data:{
                            'range'       :range,
                            'store_no'    :store_no,
                            'year'        :year,
                            'report_type' :report_type, 
                            'category'    :category, 
                            'code'        :code 
                              
                        },       
                        success: function(response)
                        {
                            Swal.close();
                            var blob      = new Blob([response], { type: 'application/vnd.ms-excel' }); 
                            var url       = URL.createObjectURL(blob);                                  
                            var link      = document.createElement('a');                                        
                            link.href     = url;                                                   
                            
                            if(report_type == 'both'){
                                report_type = 'Sales and Quantity';
                            }
                            var cat = (category == 'dept') ? "Department" : "Group";
                            report_type = report_type.charAt(0).toUpperCase() + report_type.slice(1).toLowerCase();
                            link.download = 'Yearly ' + report_type + ' Report per ' + cat + ' per Supplier.xls';

                            document.body.appendChild(link);                                       
                            link.click();                                                          
                            document.body.removeChild(link);
                        }      
                    });
                }
                // condition for per store
                else {
                    loader();
                    $.ajax({
                        type:'post',
                        url:'<?php echo base_url(); ?>Sales_item_vendor_ctrl/view_yearly_report',
                        data:{
                            'range'       :range,
                            'store_no'    :store_no,
                            'year'        :year,
                            'category'    :category, 
                            'code'        :code,
                            'report_type' :report_type 
                        },
                        success: function(response){
                            Swal.close();
                            
                            var blob = new Blob([response], { type: 'application/vnd.ms-excel' });
                            var url = URL.createObjectURL(blob);
                            var link = document.createElement('a');

                            link.href = url;

                            // Protect the downloaded Excel file
                            setTimeout(function() {
                              protectExcelFile(url);
                            }, 100);

                            function protectExcelFile(fileUrl) {
                                var xhr = new XMLHttpRequest();
                                xhr.open('GET', fileUrl, true);
                                xhr.responseType = 'arraybuffer';

                                xhr.onload = function() {
                                    if (xhr.status === 200) {
                                        var arrayBuffer = xhr.response;
                                        //console.log(arrayBuffer);

                                        var data = new Uint8Array(arrayBuffer);
                                        var workbook = XLSX.read(data, { type: 'array' });

                                        //console.log(workbook);
                                        // Assuming the first sheet in the workbook is the one to be protected
                                        var sheetName = workbook.SheetNames[0];
                                        var sheet = workbook.Sheets[sheetName];

                                        // Set the sheet protection options
                                        sheet['!protect'] = {
                                            password: '12345678',
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

                                        // Auto adjust column sizes
                                        var range = XLSX.utils.decode_range(sheet['!ref']);
                                        var columnWidths = [];
                                        for (var col = range.s.c + 1; col <= range.e.c; col++) {
                                            var maxWidth = 0;
                                            for (var row = range.s.r; row <= range.e.r; row++) {
                                                var cellAddress = XLSX.utils.encode_cell({ r: row, c: col });
                                                var cell = sheet[cellAddress];
                                                if (cell && cell.v) {
                                                    var contentLength = cell.v.toString().length;
                                                    if (contentLength > maxWidth) {
                                                        maxWidth = contentLength;
                                                    }
                                                }
                                               
                                            }
                                            columnWidths[col] = { width: maxWidth + 2 };
                                        }
                                        sheet['!cols'] = columnWidths;
                                        var workbook = XLSX.utils.book_new();
                                        XLSX.utils.book_append_sheet(workbook, sheet, 'Sheet 1');

                                        var wbout = XLSX.write(workbook, { bookType: 'xlsx', type: 'binary' });
                                        var s2ab = function (s) {
                                        var buf = new ArrayBuffer(s.length);
                                        var view = new Uint8Array(buf);
                                            for (var i = 0; i < s.length; i++) {
                                                view[i] = s.charCodeAt(i) & 0xff;
                                            }
                                            return buf;
                                        };

                                        var blob = new Blob([s2ab(wbout)], { type: 'application/octet-stream' });
                                        var url = URL.createObjectURL(blob);

                                        var link = document.createElement('a');
                                        link.href = url;
                                        if(report_type == 'both'){
                                            report_type = 'Sales and Quantity';
                                        }
                                        report_type = report_type.charAt(0).toUpperCase() + report_type.slice(1).toLowerCase();
                                        var cat = (category == 'dept') ? "Department" : "Group";

                                        link.download = 'Yearly ' + report_type + ' Report per ' + cat + ' per Supplier.xlsx';
                                        link.click();
                                    }
                                };
                                xhr.send();
                            }
                        }      

                    });
                }
            }
        });

        
        $(function(){

            $("#go2").on("click", function(e) { 
            e.preventDefault();

            var year         = $("#year2").val();
            var month        = $("#select_month2").val();

            var table = $('table#vendor-table').DataTable({
                "destroy": true,
                'serverSide': true,
                'processing': true,
                "ajax": {
                    url: "<?php echo site_url('mms/vendors'); ?>",
                    type: "POST",
                    data : {
                        year:         year,
                        month:         month,
                        
                    },
                },
                "order": [ [ 0, 'asc' ]],

                // "columnDefs": [{
                //     "targets": [1],
                //     "orderable": true,
                //     "searchable": true,
                //     "className": "text-left",
                // }]
                });
            });
        });
  
        $(".select-dept").select2({
            placeholder: "Select a Department",
            allowClear: true
        });

        $('.select-group').select2({
            placeholder: "Select a Group",
            allowClear: true
        });

        function loader(){
              
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

        // Add click event listener to all elements with class "view"
        $(".view").on("click", function(event) {
          // Prevent the default behavior of the link
          event.preventDefault();
 
        loader();
      
        // Get the href attribute of the clicked element
        const href = $(this).attr("href");
      
        // Redirect to the href URL after a short delay
        setTimeout(function() {
            window.location.href = href;
        }, 1000);
        });
    </script>