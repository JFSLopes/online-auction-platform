function initializeProfile(){
    
    const sections = {
        wishlist: document.querySelector(".profile-wishlist"),
        reviews: document.querySelector(".profile-reviews"),
        bids: document.querySelector(".user-bids"),
        auctions: document.querySelector(".user-auctions"),
    };

    const buttons = {
        wishlist: document.querySelector(".profile-nav button:nth-child(4)"),
        reviews: document.querySelector(".profile-nav button:nth-child(3)"),
        bids: document.querySelector(".profile-nav button:nth-child(2)"),
        auctions: document.querySelector(".profile-nav button:nth-child(1)"),
    };

    const editProfileButton = document.querySelector(".profile-edit-button");
    const addFundsButton = document.querySelector(".balance-button");
    const withdrawFundsButton = document.querySelector(".withdraw-balance-button");

    addFundsButton?.addEventListener("click", () => {
        const userID = addFundsButton.getAttribute("data-target");
    
        const url = `/user/${userID}/add-funds`;
        window.location.href = url;
    })


    withdrawFundsButton?.addEventListener("click", () => {
        const userID = withdrawFundsButton.getAttribute("data-target");

        const url = `/user/${userID}/withdraw-funds`;
        window.location.href = url;
    });

    editProfileButton?.addEventListener("click", () => {
        const userId = editProfileButton.getAttribute("data-target"); // Get the user ID from the attribute

        fetch(`/user/${userId}/show-edit/profile`, {
            method: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error("Failed to load profile edit page.");
                }
                return response.text();
            })
            .then(data => {
                const mainInformation = document.getElementById("main-information");

                mainInformation.innerHTML = data;

                const scripts = mainInformation.querySelectorAll('script');

                scripts.forEach(script => {
                    const newScript = document.createElement('script');
                    newScript.src = script.src; // Copy the `src` attribute
                    newScript.type = script.type || 'text/javascript';
                    newScript.defer = true; // Add the defer attribute
                    document.body.appendChild(newScript); // Append the script to the body

                    newScript.onload = () => {
                        if (typeof initializeDashboard === "function") {
                            initializeDashboard();
                        }

                        if (typeof initializeProfile === "function") {
                            initializeProfile();
                        }
                    };
                });
            })
            .catch(error => {
                console.error("Error fetching profile edit page:", error);
            });
    });

    function hideAllSections() {
        Object.values(sections).forEach(section => {
            if (section) section.style.display = "none";
        });
    }

    function showSection(sectionKey) {
        hideAllSections();
        if (sections[sectionKey]) {
            sections[sectionKey].style.display = "flex";
        }
    }

    buttons.auctions?.addEventListener("click", () => showSection("auctions"));
    buttons.bids?.addEventListener("click", () => showSection("bids"));
    buttons.reviews?.addEventListener("click", () => showSection("reviews"));
    buttons.wishlist?.addEventListener("click", () => showSection("wishlist"));

    hideAllSections();
    showSection("auctions");
}

initializeProfile();
updateCountdownFunction();

document.addEventListener('DOMContentLoaded', function () {
    updateCountdownFunction();

});


function updateCountdownFunction(){

    const auctionElements = document.querySelectorAll('.auction-time');
    
    auctionElements.forEach(function (auctionElement) {
        const closeDate = auctionElement.getAttribute('data-closedate');
        const closeTime = new Date(closeDate);
        const timeLeftElement = auctionElement.querySelector('.time-left');

        function updateCountdown() {
            const now = new Date();
            const timeDifference = closeTime - now;

            if (timeDifference <= 0) {
                timeLeftElement.textContent = "Auction Closed";
                clearInterval(intervalId);
                return;
            }

            const seconds = Math.floor((timeDifference / 1000) % 60);
            const minutes = Math.floor((timeDifference / (1000 * 60)) % 60);
            const hours = Math.floor((timeDifference / (1000 * 60 * 60)) % 24);
            const days = Math.floor(timeDifference / (1000 * 60 * 60 * 24));

            timeLeftElement.textContent = `${days}d ${hours}h ${minutes}m ${seconds}s`;
        }

    const intervalId = setInterval(updateCountdown, 1000);
    updateCountdown();
    });

}

