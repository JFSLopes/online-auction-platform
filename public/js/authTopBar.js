function showNotification__(message) {
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

function showGoodNotification__(message) {
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

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-input');
    const searchContainer = document.querySelector('.search-container');

    // Add the hover-effect class when the input is clicked
    searchInput.addEventListener('click', function() {
        searchContainer.classList.add('hover-effect');
    });

    // Remove the class when the input loses focus
    searchInput.addEventListener('blur', function() {
        searchContainer.classList.remove('hover-effect');
    });

    const balanceContainer = document.querySelector('.balance-container');
    const balanceTooltip = document.querySelector('.balance-tooltip');
    const notificationContainer = document.querySelector('.notifications-container');
    const notificationTooltip = document.querySelector('.notification-tooltip');
    
    let balanceTimeout = null;
    let notificationTimeout = null;
    
    // Show balance tooltip and hide notification tooltip
    function showBalanceTooltip() {
        clearTimeout(notificationTimeout); // Cancel the hide for notification
        clearTimeout(balanceTimeout); // Clear any pending hide for balance

        // Hide the notification tooltip if it's visible
        notificationTooltip.style.opacity = '0';
        setTimeout(() => {
            notificationTooltip.style.display = 'none';
        }, 300); // Match fade-out duration

        // Show the balance tooltip
        balanceTooltip.style.display = 'block';
        balanceTooltip.style.opacity = '1';
    }

    // Show notification tooltip and hide balance tooltip
    function showNotificationTooltip() {
        clearTimeout(balanceTimeout); // Cancel the hide for balance
        clearTimeout(notificationTimeout); // Clear any pending hide for notification

        // Hide the balance tooltip if it's visible
        balanceTooltip.style.opacity = '0';
        setTimeout(() => {
            balanceTooltip.style.display = 'none';
        }, 300); // Match fade-out duration

        // Show the notification tooltip
        notificationTooltip.style.display = 'block';
        notificationTooltip.style.opacity = '1';
    }

    // Hide balance tooltip with delay
    function hideBalanceTooltip() {
        balanceTimeout = setTimeout(() => {
            balanceTooltip.style.opacity = '0'; // Fade-out
            setTimeout(() => {
                balanceTooltip.style.display = 'none'; // Fully hide
            }, 300); // Match fade-out duration
        }, 1000); // Delay before starting fade-out
    }

    // Hide notification tooltip with delay
    function hideNotificationTooltip() {
        notificationTimeout = setTimeout(() => {
            notificationTooltip.style.opacity = '0'; // Fade-out
            setTimeout(() => {
                notificationTooltip.style.display = 'none'; // Fully hide
            }, 300); // Match fade-out duration
        }, 1000); // Delay before starting fade-out
    }

    function immediateHideTooltip(tooltip) {
        clearTimeout(notificationTimeout);
        clearTimeout(balanceTimeout);
        tooltip.style.opacity = '0';
        setTimeout(() => {
            tooltip.style.display = 'none';
        }, 300);
    }

    if(balanceContainer) {
        // Balance icon click events
        balanceContainer.addEventListener('click', () => {
            showBalanceTooltip(); // Show balance tooltip on click
            clearTimeout(balanceTimeout); // Prevent automatic hide

        });

        balanceContainer.addEventListener('mouseenter', () => {
            showBalanceTooltip(); // Show balance tooltip on hover
            notificationShown = false; // Hide notification tooltip
            hideNotificationTooltip();
        });

        balanceContainer.addEventListener('mouseleave', () => {
            hideBalanceTooltip(); // Delay hide balance tooltip when mouse leaves
        });
    }

    if(notificationContainer) {
        // Notification icon click events
        let notificationShown = false;
        notificationContainer.addEventListener('click', () => {
            notificationShown = !notificationShown;
            if (notificationShown) {
                showNotificationTooltip(); // Show notification tooltip on click
            }
            else {
                immediateHideTooltip(notificationTooltip); // Hide notification tooltip on click
            }
            clearTimeout(notificationTimeout); // Prevent automatic hide
        });
    }
    
    
    const seen_button = document.querySelector('.seen-button');
    const seen_form = document.querySelectorAll('.seen-form');

    if(seen_button) {
        seen_button.addEventListener('click', () => {
            seen_form.submit();
        });
    }
});

function performSearch() {
    const searchQuery = document.getElementById('search-auth-bar').value.trim();

    // Ensure there is a search query
    if (!searchQuery) {
        showNotification__('Please enter a search term.');
        return;
    }

    // Construct the URL with the search query
    const url = `/home/search?search=${encodeURIComponent(searchQuery)}`;

    // Redirect to the URL
    window.location.href = url;
}