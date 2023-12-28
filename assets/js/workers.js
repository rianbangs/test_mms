// worker.js

// Listen for messages from the main thread
onmessage = async function (event) {
  console.log('Message received from main thread:', event.data);

  // Perform an asynchronous network request using fetch
  try {
        const response = await fetch('http://172.16.174.201:81/test_navision/API/MMS_ctrl/middleware', 
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          // No need for a body if you're not sending any data
        });

        // Check if the request was successful (status code 200-299)
        if (response.ok)
        {
          const data = await response.json();
          console.log('Success:', data);
        } else {
          console.error('Request failed with status:', response.status);
        }
  } catch (error) {
    console.error('Error during fetch:', error);
  }

  // You can post a message back to the main thread if needed
  postMessage('Network request completed');
};

