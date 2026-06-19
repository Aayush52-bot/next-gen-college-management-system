function validateForm() {
  let email = document.getElementById("email").value;
  let phone = document.getElementById("phone").value;
  let password = document.getElementById("password").value;
  let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  let phoneRegex = /^[0-9]{10}$/;

  if (!emailRegex.test(email)) {
      alert("Enter a valid email address.");
      return false;
  }
  
  if (!phoneRegex.test(phone)) {
      alert("Phone number must be 10 digits.");
      return false;
  }

  if (password.length < 6) {
      alert("Password must be at least 6 characters.");
      return false;
  }

  return true;
}
