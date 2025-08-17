function showNotification(message) {
    // Create a new div element for the notification
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

    // Append the notification to the body
    document.body.appendChild(notification);

    // Remove the notification after 2 seconds
    setTimeout(() => {
        notification.remove();
    }, 2000);
}

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


document.addEventListener('DOMContentLoaded', async () => {
    const bidForm = document.getElementById("funds-form");
    const bidButton = document.getElementById("submit-button");
    
    bidButton.addEventListener("click", async (event) => {
        event.preventDefault(); // Prevent default form submission
        
        bidFormvalue = sanitizeInput(bidForm.value);        

        if(bidFormvalue === "" || bidFormvalue === null){
            showNotification("Please enter a valid amount.");
            return;
        }

        if(bidFormvalue <= 0){
            showNotification("Please enter a valid amount.");
            return;
        }

        bidForm.value = bidFormvalue;

        try {
            // Create a FormData object from the form
            const formData = new FormData(bidForm);
    
            // Send the form data to the server
            const response = await fetch(bidForm.action, {
                method: "POST",
                body: formData,
            });
    
            if (response.redirected) {
                // Handle redirect without JSON response
                window.location.href = response.url; // Redirect the user
                return;
            }
    
            // Parse the JSON response if not redirected
            const data = await response.json();
    
            if (data.error === "Insufficient funds") {
                showNotification("Insufficient funds");
            } else {
            }
        } catch (error) {
            console.error("An error occurred:", error);
        }
    });
});