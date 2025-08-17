user_image = document.getElementById('user_photo');
photo_inputer = document.getElementById('photo-inputer');
reset_button = document.getElementById('reset-button');
submit_button = document.getElementById('submitButton1');
form = document.getElementById('edit-profile-form');

lastImage = user_image.src;
lastImageFile = null;
let cropper;

function sanitizeInput(input) {
    input = String(input);

    const map = new Map([
        ['&', '&amp;'],
        ['<', '&lt;'],
        ['>', '&gt;'],
        ['"', '&quot;'],
        ["'", '&#x27;'],
        ['/', '&#x2F;']
    ]);

    return input.replace(/[&<>"'/]/g, match => map.get(match));
}

// Show notifications function (already in your code)
function showNotification(message) {
    const notification = document.createElement("div");
    notification.textContent = message;

    notification.style.position = "absolute";
    notification.style.top = "10px";
    notification.style.left = "50%";
    notification.style.transform = "translateX(-50%)";
    notification.style.backgroundColor = "#f8d7da";
    notification.style.color = "#721c24";
    notification.style.padding = "10px 20px";
    notification.style.border = "1px solid #f5c6cb";
    notification.style.borderRadius = "5px";
    notification.style.boxShadow = "0 4px 6px rgba(0, 0, 0, 0.1)";
    notification.style.zIndex = "1000";
    notification.style.opacity = "1"; 
    notification.style.transition = "opacity 0.5s ease-in-out"; 
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.opacity = "0";  
    }, 2000);

    notification.addEventListener("transitionend", () => {
        notification.remove();
    });
}

photo_inputer.addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function () {
            const result = reader.result;
            user_image.src = result;

            if (cropper) {
                cropper.destroy();
            }

            // Initialize the cropper
            cropper = new Cropper(user_image, {
                aspectRatio: 1,
                viewMode: 1,
                autoCropArea: 1,
                responsive: true,
                dragMode: 'move',
            });
        };
        reader.readAsDataURL(file);
    }
});


submit_button.addEventListener('click', function () {
    const name = sanitizeInput(document.getElementById('name').value);
    const phoneNumber = sanitizeInput(document.getElementById('phone').value);
    const email = sanitizeInput(document.getElementById('email').value);
    const password = sanitizeInput(document.getElementById('password').value);
    const confirmPassword = sanitizeInput(document.getElementById('password-confirmation').value);
    const address = sanitizeInput(document.getElementById('address').value);

    if (name == null || phoneNumber == null || email == null || address == null) {
        showNotification('All fields, except for password, are required');
        return;
    }

    if (name == "" || phoneNumber == "" || email == "" || address == "") {
        showNotification('All fields, except for password, are required');
        return;
    }

    if (password != confirmPassword) {
        showNotification('Passwords do not match');
        return;
    }

    if (phoneNumber.length != 9) {
        showNotification('Phone number must be 9 digits');
        return;
    }

    if (isNaN(phoneNumber)) {
        showNotification('Phone number must be a number');
        return;
    }

    if (cropper) { //Submit the image cropped if it exists
        const canvas = cropper.getCroppedCanvas();
        canvas.toBlob(function (blob) {
            const file = new File([blob], 'cropped_image.jpg', { type: 'image/jpeg' });

            const formData = new FormData(form);
            formData.set('photos', file);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Profile updated successfully');
                    }
                })
                .catch(error => {
                    showNotification('An error occurred');
                });
        });
    } else {
        form.submit(); // If anything else insn't changed, submit the form normally
    }
});
