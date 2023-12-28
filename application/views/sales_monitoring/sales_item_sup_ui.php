    
    <style>
    #view_records {
        overflow-x: auto;
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
        overflow: auto;
    }

    #payments_table_qty{
        background-color: white;
        
    }

    #payments_table2{
        background-color: white;
    }
    </style>

    <div class="row">
        <h4>Sales Report per Supplier</h4>
        <div class="col-sm-10"></div>
        <div class="col-sm-2"></div>
    </div>
 
    <!-- <div class="col-sm-2"style="border-right:1px solid black; margin-left: -8px;">
        <select class="form-control option" id="select_store" ></select>
    </div> -->

    <!-- <div class="col-sm-2"style="border-right:1px solid black; margin-left: 0px;">
        <select class="form-control option" id="select_category" disabled>
            <option>Select Category</option>
            <option value="dept">Department Code</option>
            <option value="group">Group Code</option> 
        </select>
    </div> -->

    <!-- <div class="col-sm-2"style="border-right:1px solid black; margin-left: -1px;" id="dept_cat" hidden>
        <select style="width: 160px; height: 34px; padding: 6px 12px; font-size: 14px; line-height: 1.42857143;" class="form-control select-dept option" id="select_department" disabled></select>
    </div>

    <div class="col-sm-2"style="border-right:1px solid black; margin-left: -1px;" id="group_cat" hidden>
        <select style="width: 160px; height: 34px; padding: 6px 12px; font-size: 14px; line-height: 1.42857143;" class="form-control select-group option" id="select_group" disabled></select>
    </div> -->

    <div class="col-sm-2"style="border-right:1px solid black; margin-left: 0px; width: 120px; ">
        <select class="form-control option" id="month" disabled>
            
        </select>
    </div>

    <div class="col-sm-2"style="border-right:1px solid black;margin-left: -1px; width: 120px; ">
        <select class="form-control option" id="year" disabled></select>
    </div>

    <div class="col-sm-2"style="border-right:1px solid black;  margin-left: -1px; width: 160px;">
        <select class="form-control option" id="report_type" disabled>
            <option value="">Report Type</option>  
            <option value="sales">Total Sales</option>
            <option value="quantity">Total Quantity</option>
        </select>
    </div>

    <div class="col-sm-2  " style="padding-top: 0px; margin-left: ; margin-left: 13px;">
        <button class="btn btn-md btn-danger" style="margin-left: -14px;" id="go" disabled>EXPORT EXCEL FILE</button>
    </div> 

    <div class="responsive-div_top" style="margin-top: 55px; margin-bottom: 10px;">
        <div class="line-separator"></div>
    </div>

    <!-- <h3 id="header" style="font-size: 24px;margin-top: 7px;margin-bottom: -4px;margin-left: 15px;"></h3>  -->


    <div class="col-md-12" id="view_records" style="margin-top: -17px; font-size: 14px;"></div> 

    <!-- <div class="col-md-12" id="view_records2" style="margin-top: -17px; font-size: 14px;"></div> -->
    <h3 id="total" style="font-size: 17px; margin-left: 12px;"></h3>


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

                    var html = '<option value="">Select Month</option>';
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

            if(range != '' && store_no != '' && year != '' && month != '' && report_type != '' && category != '')
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

            if(range != '' && store_no != '' && year != '' && month != '' && report_type != '' && category != '')
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

            if(range != '' && store_no != '' && year != '' && month != '' && report_type != '' && category != '')
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
                document.getElementById("select_range").disabled      = false;
                document.getElementById("year").disabled              = false;
                $("#group_cat").hide();
                //document.getElementById("go").disabled             = false;
            }else if(category == 'group'){
                $("#group_cat").fadeIn();
                document.getElementById("select_group").disabled      = false;
                document.getElementById("select_department").disabled      = true;
                document.getElementById("select_range").disabled      = false;
                document.getElementById("year").disabled              = false;
                $("#dept_cat").hide();
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

            if(range != '' && store_no != '' && year != '' && month != '' && report_type != '' && category != '')
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
                loader();
                $.ajax({
                    type:'post',
                    url:'<?php echo base_url(); ?>Sales_item_vendor_ctrl/get_yearly_monthly_report',
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
            else{
                loader();
                $.ajax({
                    type:'post',
                    url:'<?php echo base_url(); ?>Sales_item_vendor_ctrl/get_yearly_report',
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