document.getElementById('userForm').addEventListener('submit', function(e) { //get userform
    e.preventDefault();  // Stop the default form submission (prevent page reload)
 //  Validate email, phone, and password first
    if (!validateEmail() || !validatePhone() || !validatePasswordStrength()) {
        return;// If any validation fails, stop here
    }
 //  Prepare form data to send, including image file
    let formData = new FormData(this);
    //  Send form data to server using AJAX (fetch API)
    fetch('update_user.php', {
        method: 'POST',  // Send as POST request
        body: formData // Send the collected form data
    })
    .then(response => response.json()) 	// Change server response to JSON format
    .then(result => {
        if (result.status === 'success') {
            showPopup('✅ Update successful!', 'success'); //this tick i get from chatgpt hahah

            //  Wait 2 seconds, then redirect to updated user profile page
            setTimeout(() => {
                const userId = document.querySelector('input[name="user_id"]').value;
                window.location.href = 'user_detail.php?user_id=' + encodeURIComponent(userId);
            }, 2000);
            //  Show error popup with server message if update failed
        } else {
            showPopup(result.message, 'error');
        }
    })
    //use for debug
    .catch(() => showPopup('Server error!', 'error')); 
});

// Show popup message (either success or error)
function showPopup(message, type='success') {
    // Get the correct popup div based on message type
    let popup = type === 'success' ? document.getElementById('successMsg') : document.getElementById('errorMsg');
    popup.textContent = message; // Set the popup message text
    popup.classList.add('show-popup'); // Add CSS class to make it visible
    // Automatically hide popup after 3 seconds
    setTimeout(() => {
        popup.classList.remove('show-popup');
    }, 3000);
}
//  Validate that the email format is correct
function validateEmail() {
    let email = document.querySelector("input[name='email']").value.trim();
    let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        showPopup("Invalid email format!", 'error');// Show error if email is invalid
        return false;
    }
    return true;// Email format is valid
}

// 🌟 Validate that the phone number is 10-11 digits
function validatePhone() {
    let phone = document.querySelector("input[name='phone']").value.trim();
    let phonePattern = /^[0-9]{10,11}$/; 
    if (!phonePattern.test(phone)) {
        showPopup("Invalid phone number! Must be 10-11 digits.", 'error');
        return false;
    }
    return true;
}

function validatePasswordStrength() {
    let password = document.getElementById("password").value;
    let passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#_?&])[A-Za-z\d@$!%*#_?&]{8,}$/;
    
    if (password && !passwordPattern.test(password)) {
        showPopup("Weak Password: Must contain uppercase, lowercase, digit, special character, min 8 characters.", 'error');
        return false;
    }
    return true;
}
