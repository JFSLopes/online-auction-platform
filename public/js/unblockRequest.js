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

function showGoodNotification(message) {
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

async function sendRequest(userId, acceptValue, isReport) {
    try {
        const response = await fetch(window.location.href, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                userid: userId,
                accept: acceptValue,
                report: isReport
            })
        });

        const result = await response.json();

        if (result.success) {
            showGoodNotification("Success: " + result.message);
            // Optional: Reload the page or update the UI
            window.location.reload();
        } else {
            showNotification("Error: " + result.message);
        }
    } catch (error) {
        console.error("An error occurred:", error);
        showNotification("An unexpected error occurred. Please try again.");
    }
}

function acceptRequest(userId) {
    sendRequest(userId, 1, 0);
}

function denyRequest(userId) {
    sendRequest(userId, 0, 0);
}

function acceptReport(userId) {
    sendRequest(userId, 1, 1);
}

function denyReport(userId) {
    sendRequest(userId, 0, 1);
}