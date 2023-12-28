// worker.js
 

// Function to be executed by the worker
var stores_arr  = [];
var start_index = 0;
var base_url         = 'http://172.16.174.201:81/test_navision/test_mms';

function workerFunction() 
{
  // Your code here
  // This code runs in a separate thread

             

         

  postMessage("Worker has finished its task."); // Send a message back to the main thread
}

fetch_store();
// function fetch_store()
// {
//      $.ajax({ 
//                 type:'POST',
//                 url:'<?php echo base_url() ?>Mms_ctrl/fetch_stores',
//                 dataType:'JSON',
//                 success: function(data)
//                 {                     
//                      stores_arr.push(...data.store_arr);
//                 }
//             });    


// }

function fetch_store() 
{
  fetch(base_url+'/Mms_ctrl/fetch_stores')
    .then(response => response.json())
    .then(data => 
    {
      // Handle the data
      stores_arr.push(...data.store_arr);
      console.log(stores_arr);
    })
    .catch(error => 
    {
      console.error(error);
      postMessage({ error: error.message });
    });
}


 setTimeout(function()
 {
     fetch_pending_PO();  
 },5000);


 function fetch_pending_PO()
 {

    var url = base_url + '/Mms_ctrl/fetch_pending_PO?store=' + encodeURIComponent(stores_arr[start_index]);

    fetch(url)
    .then(response => response.json())
    .then(data => 
    {
      // Handle the data
                                if(data.response == 'success')
                                {               

                                    if ((stores_arr.length - 1) === start_index) 
                                    {
                                         //console.log('this is the last element in the array.');
                                         start_index = 0;    
                                    } 
                                    else
                                    {
                                         //console.log('this is not the last element in the array.');
                                         start_index += 1;
                                    }

                                    console.log(stores_arr[start_index]);

                                    setTimeout(function()
                                    {
                                        fetch_pending_PO();              

                                    },5000); 
                                }
      
      console.log('success');
    })
    .catch(error => 
    {
      console.error(error);
      postMessage({ error: error.message });
    });
 }


// Listen for messages from the main thread
onmessage = function (e) {
  if (e.data === "start") {
    workerFunction(); // Call the worker function
  }
};
 
 