// Function to handle the filter logic
function applyFilters(ignore=true) {
    event.preventDefault();

    // Retrieve values from input fields
    const searchBar = document.getElementById('search-bar').value.trim();
    const minValue = document.getElementById('min_value').value;
    const maxValue = document.getElementById('max_value').value;
    const startDateTime = document.getElementById('start_datetime').value;
    const endDateTime = document.getElementById('end_datetime').value;

    // Retrieve selected conditions
    const conditions = Array.from(document.querySelectorAll('input[name="condition[]"]:checked'))
        .map(checkbox => checkbox.value)
        .join('@'); // Use '@' as the separator

    // Retrieve category from the URL
    const urlParams = new URLSearchParams(window.location.search);
    const category = urlParams.get('category') || '';
    const activePageLink = document.querySelector('#search-pagination .active');
    const page = ignore ? 1 : (activePageLink ? parseInt(activePageLink.textContent) : 1);

    // Build the query string
    const queryParams = new URLSearchParams({
        search: searchBar,
        min_value: minValue,
        max_value: maxValue,
        conditions: conditions,
        start_datetime: startDateTime,
        end_datetime: endDateTime,
        category: category,
        page: page
    });

    // Construct the GET request URL
    const requestUrl = `/home/bi-json/search?${queryParams.toString()}`;

    fetch(requestUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {

            // Clear existing auctions
            const searchAuctionsDiv = document.getElementById('search-auctions');
            searchAuctionsDiv.innerHTML = '';

            // Check if data is empty
            if (data.results.length === 0) {
                searchAuctionsDiv.innerHTML = '<p>No auctions found</p>';
                return;
            }

            // Populate the auctions
            data.results.forEach(auction => {
                const auctionDiv = document.createElement('div');
                auctionDiv.classList.add('auction-div');

                auctionDiv.innerHTML = `
                    <a href="/home/auction/${auction.auctionid}" class="text-decoration-a">
                        <img src="/home/bi-api/auctionspic/${auction.auctionid}" alt="Auction" class="auction-icon" onerror="this.onerror=null;this.src='/images/svg/auction.svg';">
                        <div class="auction-info">
                            <h2 class="auction-title">${auction.title}</h2>
                            <p class="auction-description">${auction.description}</p>
                            <p class="auction-price">Price: $${parseFloat(auction.initvalue).toFixed(2)}</p>
                            <p class="auction-time">Start Date: ${auction.initdate}</p>
                        </div>
                    </a>
                `;

                searchAuctionsDiv.appendChild(auctionDiv);

                const message = document.getElementById('message-search');

                message.innerHTML = data.searchType;

            });

            // Generate pagination
            generatePagination(data.currentPage, data.totalPages);
        })
        .catch(error => {
            console.error('Error fetching auctions:', error);
            const searchAuctionsDiv = document.getElementById('search-auctions');
            searchAuctionsDiv.innerHTML = '<p>Error loading auctions. Please try again later.</p>';
        });
}

// Attach click event to the "Apply Filters" button
document.querySelector('.apply-filters').addEventListener('click', applyFilters);

// Attach keydown event to the search bar for Enter key
document.querySelector('#search-bar').addEventListener('keydown', function (event) {
    if (event.key === 'Enter') {
        applyFilters();
    }
});


document.querySelector('#search-bar').addEventListener('input', function (e) {
    ignore_page = true;
    const searchBar = document.getElementById('search-bar').value.trim();
    // Only send the request if the input has at least 3 characters
    if (searchBar.length < 3) {
        document.getElementById('search-list-suggestions').innerHTML = ''; // Clear suggestions if fewer than 3 characters
        return;
    }

    const minValue = document.getElementById('min_value').value;
    const maxValue = document.getElementById('max_value').value;
    const startDateTime = document.getElementById('start_datetime').value;
    const endDateTime = document.getElementById('end_datetime').value;

    // Retrieve selected conditions
    const conditions = Array.from(document.querySelectorAll('input[name="condition[]"]:checked'))
        .map(checkbox => checkbox.value)
        .join('@');

    // Retrieve category from the URL
    const urlParams = new URLSearchParams(window.location.search);
    const category = urlParams.get('category') || '';

    // Build the query string
    const queryParams = new URLSearchParams({
        search: searchBar,
        min_value: minValue,
        max_value: maxValue,
        conditions: conditions,
        start_datetime: startDateTime,
        end_datetime: endDateTime,
        category: category,
        page: 1
    });

    // Build the query string and send a GET request
    const requestUrl = `/home/bi-json/search?${queryParams.toString()}`;
    fetch(requestUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            const suggestionsList = document.getElementById('search-list-suggestions');
            suggestionsList.innerHTML = ''; // Clear the list before adding new suggestions

            // Limit the suggestions to the first 3 names
            const suggestions = data.results.slice(0, 3);

            if (suggestions.length === 0) {
                suggestionsList.innerHTML = '<li>No suggestions found</li>';
                return;
            }

            // Populate the suggestions
            suggestions.forEach(item => {
                const li = document.createElement('li');
                li.textContent = item.title;
                li.addEventListener('click', function () {
                    // Set the search bar value to the clicked suggestion
                    document.getElementById('search-bar').value = item.title;
                    suggestionsList.innerHTML = ''; // Clear the suggestions
                });
                suggestionsList.appendChild(li);
            });
        })
        .catch(error => {
            console.error('Error fetching search suggestions:', error);
        });
});


// Add event listener to each category item
document.querySelectorAll('.category-item').forEach(item => {
    item.addEventListener('click', function() {
        document.getElementById('search-bar').value = ''; // Clear search bar

        const categoryName = this.getAttribute('data-category-name');
        
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('category', categoryName); // Set the selected category in URL query

        window.history.pushState({}, '', `${window.location.pathname}?${urlParams}`);
    });
});

// Add event listener to each category item
document.querySelectorAll('.category-item').forEach(item => {
    item.addEventListener('click', function () {
        document.getElementById('search-bar').value = ''; // Clear search bar

        const categoryName = this.getAttribute('data-category-name');
        
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('category', categoryName);
        window.history.pushState({}, '', `${window.location.pathname}?${urlParams}`);
        
        document.querySelectorAll('.category-item').forEach(item => {
            item.classList.remove('selected-category');
        });

        this.classList.add('selected-category');
    });
});

// Function to generate pagination controls
function generatePagination(currentPage, totalPages) {
    const paginationDiv = document.getElementById('search-pagination');

    paginationDiv.innerHTML = "";
    
    // Create 'previous' button
    let prevPage = currentPage - 1;
    if (prevPage < 1) prevPage = 1;

    let nextPage = currentPage + 1;
    if (nextPage > totalPages) nextPage = totalPages;

    paginationDiv.innerHTML = `
        <span onclick="applyPage(${prevPage})">&laquo;</span>
    `;
    
    // Create page number buttons
    for (let i = 1; i <= totalPages; i++) {
        const pageLink = document.createElement('span');
        pageLink.textContent = i;
        if (i == currentPage) {
            pageLink.classList.add('active');
        }
        pageLink.setAttribute('onclick', `applyPage(${i})`);
        paginationDiv.appendChild(pageLink);
    }

    // Create 'next' button
    paginationDiv.innerHTML += `
        <span onclick="applyPage(${nextPage})">&raquo;</span>
    `;
}

function applyPage(page) {
    // Find all pagination links and remove the 'active' class
    const paginationLinks = document.querySelectorAll('#search-pagination span');
    paginationLinks.forEach(link => {
        const linkPage = parseInt(link.textContent, 10);
        if (!isNaN(linkPage) && linkPage == page) {
            link.classList.add('active'); // Add 'active' class if it matches
        } else if (!isNaN(linkPage)){
            link.classList.remove('active'); // Remove 'active' class otherwise
        }
    });

    // Call the applyFilters function to fetch the auctions for the selected page
    applyFilters(false);
}