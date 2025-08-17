function showNotification_(message) {
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

function showGoodNotification_(message) {
    const notification = document.createElement("div");
    notification.textContent = message;

    notification.style.position = "absolute";
    notification.style.top = "10px";
    notification.style.left = "50%";
    notification.style.transform = "translateX(-50%)";
    notification.style.backgroundColor = "#8be78b";
    notification.style.color = "#37c57e";
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

document.addEventListener('DOMContentLoaded', () => {
    const deleteButtons = document.querySelectorAll('.btn.btn-delete');
    const blockButtons = document.querySelectorAll('.btn.btn-suspend');

    const adminId = window.location.pathname.split('/')[2];

    deleteButtons.forEach((button) => {
        button.addEventListener('click', async function (event) {
            event.preventDefault();

            const userId = this.dataset.target;
            const confirmation = confirm(`Are you sure you want to delete user with ID ${userId}?`);
            if (!confirmation) return;

            const response = await sendAction(`/admin/${adminId}/user/${userId}/del`);
            if (response.success) {
                showGoodNotification_(response.message || 'User deleted successfully');
                this.closest('.user-card').remove();
            } else {
                showNotification_(response.message || 'Failed to delete user');
            }
        });
    });

    blockButtons.forEach((button) => {
        button.addEventListener('click', async function (event) {
            event.preventDefault();

            const userId = this.dataset.target;
            const isBlocked = this.querySelector('i').classList.contains('fa-user-slash');
            const action = isBlocked ? 'unblock' : 'block';
            const confirmation = confirm(`Are you sure you want to ${action} user with ID ${userId}?`);
            if (!confirmation) return;

            const response = await sendAction(`/admin/${adminId}/user/${userId}/block`);
            if (response.success) {
                showGoodNotification_(response.message || `User ${action}ed successfully`);
                // Toggle UI elements
                const icon = this.querySelector('i');
                if (isBlocked) {
                    icon.classList.remove('fa-user-slash');
                    icon.classList.remove('fa-solid');
                    icon.classList.add('fa-user');
                    icon.classList.add('fa-regular');
                } else {
                    icon.classList.remove('fa-user');
                    icon.classList.remove('fa-regular');
                    icon.classList.add('fa-user-slash');
                    icon.classList.add('fa-solid');
                }
            } else {
                showNotification_(response.message || `Failed to ${action} user`);
            }
        });
    });
});

// Function to send a fetch request
async function sendAction(url) {
    const method = 'POST';
    const body = {};
    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(body),
        });
        return await response.json();
    } catch (error) {
        return { success: false, message: 'An error occurred. Please try again.' };
    }
}
