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

document.addEventListener("DOMContentLoaded", () => {
    const buttonsContainer = document.querySelector(".sidebar-list");
    const auctionValueInput = document.querySelector('input[name="auctionId"]');
    const messagesArea = document.querySelector(".messages-area");

    auctionValueInput.value = "";

    const url = window.location.href;
    const segments = url.split("/");
    const userId = segments[segments.indexOf("user") + 1];

    let currentAuctionId = null;

    function update_side_bar() {

        const url = window.location.href;
        const segments = url.split("/");
        const userId = segments[segments.indexOf("user") + 1];
    
        fetch(`/home/bi-json/users/${userId}/currentchats`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            const sidebar = document.querySelector('.sidebar-list');
    
            sidebar.innerHTML = '';
    
            data.forEach(message => {
                    const sidebarItem = document.createElement('div');
                    sidebarItem.classList.add('sidebar-item');
                    sidebarItem.setAttribute('data-auction-id', message.auctionid);
    
                    sidebarItem.innerHTML = `
                        <img src="/home/bi-api/auctionspic/${message.auctionid}" alt="Item" class="sidebar-avatar">
                        <span class="sidebar-name">${message.title}</span>
                        <span class="message-content">${message.content}</span>
                        <span class="message-date">${message.sentdate}</span>
                    `;
    
                    sidebar.appendChild(sidebarItem);
    
                    sidebarItem.addEventListener("click", (e) => {
                        currentAuctionId = message.auctionid;
                        fetchMessages(currentAuctionId);
                    });
            });
        })
        .catch(error => console.error('Error updating sidebar:', error));
    }


    const fetchMessages = (auctionId) => {
        fetch(`/home/bi-json/users/${userId}/auction/${auctionId}/message`, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {

            // Clear the messages area
            messagesArea.innerHTML = "";

            // Populate messages
            data.forEach(message => {
                const messageDiv = document.createElement("div");

                if (message.uid == userId) {
                    messageDiv.classList.add("message-sent");
                } else {
                    messageDiv.classList.add("message-received");
                }

                const messageContent = document.createElement("p");
                messageContent.textContent = message.content;

                const messageDate = document.createElement("p");
                date = new Date(message.sentdate);
                messageDate.textContent = String(date.getFullYear()).padStart(4,'0') + '-' + (String(date.getMonth() + 1).padStart(2,'0')) + '-' + String(date.getDate()).padStart(2,'0') + ' ' + String(date.getHours()).padStart(2,'0') + ':' + String(date.getMinutes()).padStart(2,'0') + ':' + String(date.getSeconds()).padStart(2,'0');
                messageDate.classList.add("message-date");

                const message_img_text = document.createElement('div');

                const img = document.createElement('img');

                img.src = '/home/bi-api/userspic/' + message.uid;

                img.classList.add('sidebar-avatar');

                message_img_text.classList.add('message-img-text');

                message_img_text.appendChild(img);
                message_img_text.appendChild(messageContent);

                messageDiv.appendChild(message_img_text);
                messageDiv.appendChild(messageDate);

                messagesArea.appendChild(messageDiv);
            });

            // Update the auction ID input value
            auctionValueInput.value = auctionId;
        })
        .catch(error => {
            console.error("Error fetching messages:", error);
        });
    };

    // Set up click event listeners for the buttons
    for (let i = 0; i < buttonsContainer.children.length; i++) {
        const button = buttonsContainer.children[i];

        button.addEventListener("click", (e) => {
            const auctionId = button.getAttribute("data-auction-id");
            currentAuctionId = auctionId;

            // Fetch messages immediately when the button is clicked
            fetchMessages(auctionId);
        });
    }

    // Set an interval to fetch messages every 8 seconds for the current auction ID
    setInterval(() => {
        if (currentAuctionId) {
            fetchMessages(currentAuctionId);
        }

        update_side_bar();

    }, 8000);
});


document.getElementById('message-send-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const url = form.action;

    if(formData.get('auctionId') != "") {

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: formData,
        })
        .then(response => {
            if (!response.ok) throw new Error('Failed to send the message');
            return response.json();
        })
        .then(data => {
            message = data.message;
            const messageDiv = document.createElement("div");

            const messages_area = document.querySelector(".messages-area");

            messageDiv.classList.add("message-sent");

            const messageContent = document.createElement("p");
            messageContent.textContent = message.content;
            const messageDate = document.createElement("p");
            date = new Date(message.sentdate);
            messageDate.textContent = String(date.getFullYear()).padStart(4,'0') + '-' + (String(date.getMonth() + 1).padStart(2,'0')) + '-' + String(date.getDate()).padStart(2,'0') + ' ' + String(date.getHours()).padStart(2,'0') + ':' + String(date.getMinutes()).padStart(2,'0') + ':' + String(date.getSeconds()).padStart(2,'0');  
            messageDate.classList.add("message-date");

            const message_img_text = document.createElement('div');

            const img = document.createElement('img');

            img.src = '/home/bi-api/userspic/' + data.uid;

            img.classList.add('sidebar-avatar');

            message_img_text.classList.add('message-img-text');

            message_img_text.appendChild(img);
            message_img_text.appendChild(messageContent);

            messageDiv.appendChild(message_img_text);
            messageDiv.appendChild(messageDate);

            messages_area.appendChild(messageDiv);

            scrollToBottom();

            const inputField = document.querySelector('input[name="message"]');

            inputField.value = '';
        })
        .catch(error => {
            console.error('Error:', error);
        });
        
    }else{
        showNotification('You should first select an auction to send a message');
    }
});

function scrollToBottom() {
    const messageContainer = document.querySelector('.messages-area');
    messageContainer.scrollTop = messageContainer.scrollHeight;
}