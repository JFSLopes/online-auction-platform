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
    notification.style.backgroundColor = "#f8d7da";
    notification.style.color = "#8be78b";
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

async function fetchAuctionHistory(id) {
    const url = `/home/auction/${id}/history`;

    try {
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error(`Error fetching auction history: ${response.statusText}`);
        }

        const data = await response.json();
        renderAuctionHistory(data);
        return data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
}

async function fetchAuctionDetails(id) {
    const url = `/home/bi-json/auctions/${id}`;

    try {
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error(`Error fetching auction details: ${response.statusText}`);
        }

        const data = await response.json();
        renderAuctionDetails(data);
        return data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
}

let currentBidData = [];

let bids = 0;

function renderAuctionHistory(bidData) {
    const listContainer = document.getElementById('bid-history_ul');

    if (JSON.stringify(bidData) === JSON.stringify(currentBidData)) {
        return;
    }

    currentBidData = bidData;

    listContainer.innerHTML = '';

    bids = bidData.length;

    bidData.forEach(bid => {
        const listItem = document.createElement('li');

        listItem.innerHTML = `
            <img src="/home/bi-api/userspic/${bid.userid}" alt="Seller Image">
            <div class="bidder-info">
                <div class="bidder-name">${bid.username}</div>
                <div class="bid-amount">${parseFloat(bid.amount).toFixed(2)} €</div>
                <div class="bid-time">${new Date(bid.biddate).toLocaleString()}</div>
            </div>
        `;

        listContainer.appendChild(listItem);
    });

    value_auction = document.getElementById("value_auction");

    if (bids != 0) {
        const latestBid = bidData.reduce((latest, bid) => new Date(bid.biddate) > new Date(latest.biddate) ? bid : latest, bidData[0]);
        value_auction.innerHTML = parseFloat(latestBid.amount).toFixed(2) + "€" + " (" + bidData.length + " bids)";
    }
}



let currentAuctionData = [];

function renderAuctionDetails(auctionData) {

    if (JSON.stringify(auctionData) === JSON.stringify(currentBidData)) {
        return;
    }

    currentAuctionData = auctionData;

    closing_date = document.getElementById("close_date");
    category = document.getElementById("categories");
    subcategory = document.getElementById("sub-categories");
    stateProduct = document.getElementById("state-product");
    fullDescription = document.getElementById("full-description");
    product_name = document.getElementById("product_name");
    product_short_description = document.getElementById("product_short_description");

    date = new Date(auctionData.closedate);

    closing_date.innerHTML = String(date.getFullYear()).padStart(4,'0') + '-' + (String(date.getMonth() + 1).padStart(2,'0')) + '-' + String(date.getDate()).padStart(2,'0') + ' ' + String(date.getHours()).padStart(2,'0') + ':' + String(date.getMinutes()).padStart(2,'0') + ':' + String(date.getSeconds()).padStart(2,'0');
    

    category.innerHTML = `<li>${auctionData.categoryname}</li>`;

    subcategory.innerHTML = `<li>${auctionData.subcategoryname}</li>`;

    stateProduct.childNodes[1].nodeValue = `${auctionData.state}`;

    fullDescription.innerHTML = auctionData.description;

    if(product_name.dataset.premium == 1){
        product_name.innerHTML = auctionData.title + '<i class="fa-solid fa-gavel fa-lg" style="color: #FFD43B;"></i>';
    }
    else product_name.innerHTML = auctionData.title;
    

    if(bids == 0){
        value_auction = document.getElementById("value_auction");
        value_auction.innerHTML = parseFloat(auctionData.initvalue).toFixed(2) + "€" + " (" + bids + " bids)";

    }
}

function getAuctionIdFromUrl() {
    const path = window.location.pathname;
    const parts = path.split('/');
    return parts[3];
}

const auction_id = getAuctionIdFromUrl();

fetchAuctionHistory(auction_id);

fetchAuctionDetails(auction_id);

setInterval(() => fetchAuctionHistory(auction_id), 5000);

setInterval(() => fetchAuctionDetails(auction_id), 10000);

document.addEventListener("DOMContentLoaded", function () {
    const closingDate = document.getElementById("close_date").innerHTML;
    closing = new Date(closingDate).getTime();

    const userImage = document.getElementById("seller-img");

    userImage.addEventListener("click", () => {
        const selletid = userImage.getAttribute('data-target');
        window.location.href = `/home/profile/${selletid}`;
    });

    const revierwImage = document.getElementById("review-img");
    if (revierwImage){
        revierwImage.addEventListener("click", () => {
            const reviewerid = revierwImage.getAttribute('data-target');
            window.location.href = `/home/profile/${reviewerid}`;
        });
    }

    const sellerDiv = document.getElementById("seller-info");
    sellerDiv.addEventListener("click", () => {
        const selletid = userImage.getAttribute('data-target');
        window.location.href = `/home/profile/${selletid}`;
    });
    
    function updateTimer() {
        const now = new Date().getTime();
        const timeLeft = closing - now;

        if (timeLeft <= 0) {
            document.querySelector(".time-left").innerHTML = "<p>Auction Ended</p>";
            clearInterval(timerInterval);
            return;
        }

        const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
        const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

        document.querySelector(".time-left p:nth-child(2)").textContent = 
            `${days} d : ${hours}h : ${minutes}m : ${seconds}s`;
    }

    const bidForm = document.getElementById("bidForm");
    const bidButton = document.getElementById("submitButton");

    if(bidButton){
    
        bidButton.addEventListener("click", async (event) => {
            event.preventDefault(); 
        
            try {
                const formData = new FormData(bidForm);
            
                const response = await fetch(bidForm.action, {
                    method: "POST",
                    body: formData,
                });
            
                if (response.redirected) {
                    window.location.href = response.url; 
                    return;
                }
            
                const data = await response.json();
            
                if (data.error === "Insufficient funds") {
                    showNotification("Insufficient funds");
                } else {
                }
            } catch (error) {
                console.error("An error occurred:", error);
            }
        });
    }
    const timerInterval = setInterval(updateTimer, 1000);

    updateTimer();

    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('rating');
    
    stars.forEach(star => {
        star.addEventListener('mouseover', () => {
            highlightStars(star.dataset.value);
        });

        star.addEventListener('mouseout', () => {
            highlightStars(ratingInput.value);
        });

        star.addEventListener('click', () => {
            // Set rating and lock stars
            ratingInput.value = star.dataset.value;
            highlightStars(ratingInput.value);
        });
    });

    function highlightStars(value) {
        stars.forEach(star => {
            star.classList.toggle('glow', star.dataset.value <= value);
        });
    }

    function handleInputLimit(inputElement, counterElement, maxLength) {

        if(inputElement != null){
            inputElement.addEventListener("input", function () {
                inputElement.value = sanitizeInput(inputElement.value);

                const currentLength = inputElement.value.length;

                if (currentLength > maxLength) {
                    inputElement.value = inputElement.value.substring(0, maxLength);
                }

                counterElement.textContent = `${inputElement.value.length}/${maxLength}`;
            });
        }
    }

    const reviewInput = document.getElementById("review");
    const charCounter = document.getElementById("char-counter");

    handleInputLimit(reviewInput, charCounter, 500);
    

});

document.addEventListener('DOMContentLoaded', () => {
    let lat = document.getElementById('latitudine-hidden').value;
    let lon = document.getElementById('longitudine-hidden').value;  

        if (lat && lon) {
            const map = L.map('map').setView([lat, lon], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            L.circle([lat, lon], {
                color: 'red',
                fillColor: '#f03',
                fillOpacity: 0.5,
                radius: 2000
            }).addTo(map);
        }
});

function showReportForm(auctionId, userId, authId) {
    document.getElementById('report-form').classList.remove('hidden');
}

function hideReportForm() {
    document.getElementById('report-form').classList.add('hidden');
}

async function sendReport(auctionId, userReported, userWhoReported) {
    confirm("Are you sure you want to report the auction?");
    // Gather data from the form
    const reason = document.getElementById('reason-report').value;

    // Validate the input (optional client-side validation)
    if (!reason || reason.length > 150) {
        showNotification("Please enter a valid reason (max 150 characters).");
        return;
    }

    // Create the payload
    const payload = {
        userwhoreported: userWhoReported,
        userreported: userReported,
        content: reason,
        auctionid: auctionId,
    };

    try {
        // Send the POST request
        const response = await fetch(`/user/${userWhoReported}/report`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, // Laravel CSRF token
            },
            body: JSON.stringify(payload),
        });

        // Parse the response
        const result = await response.json();

        // Handle the response
        if (result.success) {
            showGoodNotification("Report submitted successfully.");
        } else {
            showNotification(`Error: ${result.message}`);
        }
    } catch (error) {
        console.error("Error submitting report:", error);
        showNotification("An error occurred while submitting the report.");
    } finally {
        // Hide the report form
        hideReportForm();
    }
}
