function autoFetchAuctionsPremium() {
    const interval = 10000;
    setInterval(() => {
        let currentPagePremium = Number(document.querySelector("#premium-page").value);

        // Fetch the next page
        getPage(true, currentPagePremium);
    }, interval);
}

function autoFetchAuctionsFeatured() {
    const interval = 10000;
    setInterval(() => {
        let currentPageFeatured = Number(document.querySelector("#featured-page").value);

        // Fetch the next page
        getPage(false, currentPageFeatured);
    }, interval);
}


function getPage(isPremium, pageNum) {
    const premium = isPremium ? "1" : "0";
    const requestUrl = `/home/bi-json/search/home?premium=${premium}&page=${pageNum}`;

    fetch(requestUrl)
        .then((response) => {
            if (!response.ok) {
                throw new Error("Network response was not ok " + response.statusText);
            }
            return response.json();
        })
        .then((data) => {
            // Render the new auctions in the target section
            if (data.results.length != 0){
                if (data.premium == "1") {
                    renderAuctionsPremium(data.results);
                } else {
                    renderAuctionsFeatured(data.results);
                }
            }

            // Update the current page and check if we need to loop back
            let nextPage = Number(data.currentPage) < Number(data.totalPages) ? Number(data.currentPage) + 1 : 1;
            if (data.premium == '1') {
                document.querySelector("#premium-page").value = nextPage;
            } else {
                document.querySelector("#featured-page").value = nextPage;
            }
        })
        .catch((error) => console.error("Error fetching auctions:", error));
}


document.addEventListener("DOMContentLoaded", () => {
    autoFetchAuctionsFeatured(); // Featured auctions
    autoFetchAuctionsPremium();  // Premium auctions
});

function renderAuctionsFeatured(auctions) {
    const targetElement = document.querySelector(`.trending-auctions-container`);

    // Clear the target element content
    targetElement.innerHTML = "";

    // Iterate over auctions
    auctions.forEach(auction => {
        const auctionHtml = `
            <a href="home/auction/${auction.auctionid}" class="text-decoration-a">
                <div class="auction-div-app">
                    <img class="current-image" src="/home/bi-api/auctionspic/${auction.auctionid}" alt="Item Image" onerror="this.onerror=null;this.src='/images/svg/auction.svg';">
                    <div class="auction-info-app">
                        <h2 class="auction-title-app">${auction.title}</h2>
                        <p class="auction-description-app">${auction.description}</p>
                        <p class="auction-price-app">Price: $${parseFloat(auction.initvalue).toFixed(2)}</p>
                        <p class="auction-time-app">Start Date: ${auction.initdate}</p>
                    </div>
                </div>
            </a>
        `;
        // Append the HTML to the target element
        targetElement.insertAdjacentHTML("beforeend", auctionHtml);
    });
}

function renderAuctionsPremium(auctions) {
    const targetElement = document.querySelector(`.premium-auctions-container`);

    targetElement.innerHTML = "";

    // Iterate over auctions
    auctions.forEach(auction => {
        date = new Date(auction.auction.initdate);
        const auctionHtml = `
            <a href="/home/auction/${auction.auction.auctionid}" class="text-decoration-a">
                <div class="auction-div-app premium-hammer">
                    <img class="current-image" src="/home/bi-api/auctionspic/${auction.auction.auctionid}" alt="Item Image" onerror="this.onerror=null;this.src='/images/svg/auction.svg';">
                    <div class="auction-info-app">
                        <h3>${auction.product.title}</h3>
                        <p>${auction.product.description}</p>
                        <p>Price: $${parseFloat(auction.auction.initvalue).toFixed(2)}</p>
                        <p>Start Date: ${String(date.getFullYear()).padStart(4,'0') + '-' + (String(date.getMonth() + 1).padStart(2,'0')) + '-' + String(date.getDate()).padStart(2,'0') + ' ' + String(date.getHours()).padStart(2,'0') + ':' + String(date.getMinutes()).padStart(2,'0') + ':' + String(date.getSeconds()).padStart(2,'0')}</p>
                    </div>
                </div>
            </a>
        `;
        // Append the HTML to the target element
        targetElement.insertAdjacentHTML("beforeend", auctionHtml);
    });
}