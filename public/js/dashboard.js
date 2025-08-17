function initializeDashboard() {

    const userButton = document.querySelector('.users-nav');
    const activeBidsButton = document.querySelector('.active-bids');
    const itemsWonButton = document.querySelector('.items-won-nav');

    const userSection = document.querySelector('.information-div');
    const activeBidsSection = document.querySelector('.active-bids-div');
    const itemsWonSection = document.querySelector('.items-won-div');

    if(userButton || activeBidsButton || itemsWonButton) {
        // Set up the click event listeners
        userButton.addEventListener('click', function() {
            toggleSection(userSection);
        });

        activeBidsButton.addEventListener('click', function() {
            toggleSection(activeBidsSection);
        });

        itemsWonButton.addEventListener('click', function() {
            toggleSection(itemsWonSection);
        });
    }

    // Function to toggle sections
    function toggleSection(activeSection) {
        // Hide all sections
        userSection.classList.remove('active');
        activeBidsSection.classList.remove('active');
        itemsWonSection.classList.remove('active');

        // Show the clicked section
        activeSection.classList.add('active');
    }

    // By default, display the user information
    if(userSection){
        userSection.classList.add('active');
    }
}

initializeDashboard();