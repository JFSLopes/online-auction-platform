function showNotification___(message) {
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

function showGoodNotification___(message) {
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

var deleteButton = document.querySelector('#delete-auction');

if (deleteButton != null){
    deleteButton.addEventListener('click',function(e){
        e.preventDefault();
        let auction_id;
        if (window.location.pathname.split('/').includes('profile')){
            auction_id = this.dataset.auction;
        } else {
            auction_id = window.location.pathname.split('/')[3];
        }
        const user_id = this.dataset.target;
        const admin = this.dataset.admin;
        let url;
        if(admin == 1){
            url = '/admin/' + user_id + '/delete/auction/' + auction_id;
        }
        else url = '/user/' + user_id + '/delete/auction/' + auction_id;

        fetch(url, {
            method: 'POST', 
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showGoodNotification___('Auction deleted successfully!');
                location.reload();
            } else {
                showNotification___('Failed to delete auction: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification___('An error occurred. Please try again.');
        });
    });
}