document.addEventListener('DOMContentLoaded', function() {
    const navToggle = document.querySelector('#nav-toggle');
    const navBar = document.querySelector('#nav-bar');
    const mainContent = document.querySelector('section.main-information');

    // Event Listener for Burger Button
    if (navToggle == null) return;
    navToggle.addEventListener('change', function() {
        updateNavbarState();
    });

    // Update Navbar State Function
    function updateNavbarState() {
        navBarWidth = navBar.offsetWidth;
        if (navBarWidth == 80) {
            navBar.classList.add('open');
            mainContent.style.marginLeft = 'calc(var(--navbar-width) + 2rem)';
        } else {
            navBar.classList.remove('open');
            mainContent.style.marginLeft = 'calc(8rem)';
        }
    }
    updateNavbarState();

    firstLoad = true;
    if(firstLoad){
        navBar.classList.remove('open');
        mainContent.style.marginLeft = 'calc(var(--navbar-width) + 2rem)';
    }
});
