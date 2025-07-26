
document.addEventListener("DOMContentLoaded", function() {
//all console logs are for debugging purposes

    window.isEditMode = false;

    var editBtn = document.getElementById('editButton'); 
    var submitBtn = document.getElementById('submitButton'); 
    var cancelBtn = document.getElementById('cancelButton'); 
    var exitBtn = document.querySelector('.exit-btn'); 
    var form = document.querySelector("form"); 

    if (editBtn) {
        editBtn.addEventListener('click', function() {
            console.log("Edit button clicked");
            window.isEditMode = true;
            
            var inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="date"]');
            inputs.forEach(function(input) {
                if (input.hasAttribute('readonly')) {
                    input.removeAttribute('readonly'); 
                }
            });

            var selects = document.querySelectorAll('select');
            selects.forEach(function(select) {
                if (select.hasAttribute('disabled')) {
                    select.removeAttribute('disabled'); 
                }
            });

            editBtn.style.display = 'none'; 
            submitBtn.style.display = 'inline-block'; 
            cancelBtn.style.display = 'inline-block'; 

            if (exitBtn) {
                exitBtn.style.display = 'none'; 
            }
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            console.log("Cancel button being clicked");
            window.location.reload(); 
        });
    }

    if (submitBtn) {
        submitBtn.addEventListener('click', function() {
        });
    }
    
    if (form) {
        form.addEventListener("submit", function(e) {
            console.log("profile has been passing...");
        });
    }
});

function triggerFileInput() {
  if (!window.isEditMode) {
    return;
  }
  console.log("triggerFileInput being invoked");
  document.getElementById('profileFileInput').click();
}

document.getElementById('profileFileInput').addEventListener('change', function(event) {
  var file = event.target.files[0];
  if (file) {
    var reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('profileImage').src = e.target.result;
    }
    reader.readAsDataURL(file);
  }
});