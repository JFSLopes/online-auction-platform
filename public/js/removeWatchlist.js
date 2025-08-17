var remove_watchlist = document.querySelector('#remove-watchlist-button');

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

if (remove_watchlist){
    remove_watchlist.addEventListener('click',function (e){
        e.preventDefault();
        let auctionId;
        if (window.location.pathname.split('/').includes('profile')){
            auctionId = this.dataset.auction;
        } else {
            auctionId = window.location.pathname.split('/')[3];
        }
        const user_id = this.dataset.target;
    
        const form = {
            userid : user_id,
            auctionid : auctionId
        }
    
        url = "/user/" + user_id + "/watchlist/remove/" + auctionId;
        fetch(url,{
            method: 'POST',
            headers:{
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body:JSON.stringify(form)
        })
        .then(response => response.json())
        .then(data => {
            if (data.message == 'true'){
                showGoodNotification('Auction removed from Watchlist');
                window.location.reload();
            }else{
                showNotification(data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred. Please try again later.');
        });
    
    });
}
