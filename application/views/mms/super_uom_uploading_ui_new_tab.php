  

<style>


/* upload div separator :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

  @media (max-width: 700px) {
    /* Styles for extra small screens */
    .col-sm-3 {
      margin-top: 10px;
    }
    
    .form-control {
      margin-top: 10px;
    }
    
    .input-group-append {
      margin-top: 10px;
    }
  }
  
  @media (min-width: 700px) and (max-width: 900px) {
    /* Styles for small screens */
    .col-sm-3 {
      margin-top: 30px;
    }
    
    .form-control {
      margin-top: 5px;
    }
    
    .input-group-append {
      margin-top: 5px;
    }
  }
  
  .responsive-div {
      width: 100%; 
      max-width: 1600px; 
      height: 4px; 
      background-color: #0c6b99;
      position: relative; 
    }

    .line-separator {
      position: absolute;
      bottom: 0; 
      left: 0;
      width: 100%; 
      height: 0px;
      background-color: black; 
    }

    @media (max-width: 768px) {
      .responsive-div {
        width: 100%;
      }
    }

/* header saparator :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
    .responsive-div_top{
      width: 100%; 
      max-width: 1600px; 
      height: 2px; 
      background-color: #0c6b99;
      position: relative;
    }

    #main_div{
      height:810px;
    }

    .fas.fa-bars 
     {
      display: none;
     }

     .profile{
       display: none;
     }

     .sidebar-menu{
      display: none;
     }

      

</style>

  <?php


     $uploadedFile            = $sample;

     $fileName_s              = $uploadedFile['name'];
     $fileTmpPath_s           = $uploadedFile['tmp_name'];
     $fileSize_s              = $uploadedFile['size'];
     $fileError               = $uploadedFile['error'];  
     $fileContent_s           = file_get_contents($fileTmpPath_s);
     $lines                   = explode(PHP_EOL, $fileContent_s);
     var_dump($lines);

  ?>


 <div class="row">
 
<h3 style="margin-top: -21px;">Sample Uploading</h3>

<!-- ==================================================================================================================================================== -->
   
  <div class="responsive-div_top" style="margin-top: -9px; margin-bottom: 10px;">
        <div class="line-separator"></div>
  </div>


<script>
var element = document.querySelector('div'); // Replace 'div' with the appropriate selector for your element
element.className = 'active';


</script>