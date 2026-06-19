// Existing form submission handling
document.getElementById('login-form').addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent default form submission
    
    // Form validation logic
    const username = document.getElementById('username').value; // Ensure the ID matches the HTML input ID
    const password = document.getElementById('password').value; // Ensure the ID matches the HTML input ID
  
    if (username && password) {
      showLoadingSpinner(); // Show loading spinner on form submit
  
      // Simulate form submission process (e.g., AJAX request)
      setTimeout(function () {
        console.log('Login process complete');
        // Redirect to home page after login
        window.location.href = 'Foxes.html'; 
      }, 2000); // Simulate processing time
    } else {
      console.log('Please enter both username and password');
    }
});

// Handle Register link click
document.getElementById('register-link').addEventListener('click', function (e) {
    e.preventDefault(); // Prevent default link behavior
    showLoadingSpinner(); // Show loading spinner

    setTimeout(function () {
      window.location.href = 'register.php'; // Redirect to the register page
    }, 2000); // Simulate loading time (2 seconds)
});

// Function to display the loading spinner
function showLoadingSpinner() {
    const spinner = document.getElementById('loading'); // Ensure the ID matches the HTML
    spinner.style.display = 'flex'; // Show the spinner

    // After 2 seconds (or any desired delay), hide the loading spinner
    setTimeout(function() {
        spinner.style.display = 'none'; // Hide spinner after redirection
    }, 2000); // Hide spinner after 2 seconds
}
