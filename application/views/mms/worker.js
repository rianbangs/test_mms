// worker.js

// Function to be executed by the worker
function workerFunction() {
  // Your code here
  // This code runs in a separate thread
  postMessage("Worker has finished its task."); // Send a message back to the main thread
}

// Listen for messages from the main thread
onmessage = function (e) {
  if (e.data === "start") {
    workerFunction(); // Call the worker function
  }
};
