function getUserIdFromUrl() {
    const path = window.location.pathname;
    const parts = path.split('/');
    return parts[3];
}

function updateNavbarState(navBar , mainContent) {
    const navBarWidth = navBar.offsetWidth;
    if (navBarWidth == 80) {
        navBar.classList.add('open');
        mainContent.style.marginLeft = 'calc(var(--navbar-width) + 2rem)';
    } else {
        navBar.classList.remove('open');
        mainContent.style.marginLeft = 'calc(8rem)';
    }
}

function unloadScripts(mandatoryScripts) {
    
    scripts = document.querySelectorAll('script');

    scripts.forEach(script => {

    });

    scripts.forEach(script => {
        const scriptContent = script.src || script.innerHTML;
        const containsMandatoryScript = mandatoryScripts.some(mandatoryScript => scriptContent.includes(mandatoryScript));

        if (!containsMandatoryScript) {
            script.remove();
        }
    });

    scripts = document.querySelectorAll('script');

    scripts.forEach(script => {
    });
}

document.addEventListener("DOMContentLoaded", function () {
    
    const navButtons = Array.from(document.querySelectorAll(".nav-button")).filter(button => button.id !== "logout-button");
    
    const logoutButton = document.getElementById("logout-button");
    const logoutForm = document.getElementById("logout-form");
    
    if (logoutButton == null) return;

    logoutButton.addEventListener("click", function () {
        logoutForm.submit();
    });

    const mainInformation = document.getElementById("main-information");

    const mandatoryScripts = ["profile.js", "navBar.js", "authTopBar.js"];

    const navToggle = document.querySelector('#nav-toggle');
    const navBar = document.querySelector('#nav-bar');
    const mainContent = document.querySelector('section.main-information');

    // Event Listener for Burger Button
    navToggle.addEventListener('change', function() {
        updateNavbarState(navBar, mainContent);
    });
    
    updateNavbarState(navBar, mainContent);

    navButtons.forEach(button => {
        if (button != null){
            button.addEventListener("click", function () {

                unloadScripts(mandatoryScripts);
    
                const target = this.dataset.target; // Get the target page name
                mainInformation.innerHTML = ""; // Clear current content
                
                partialFetchRequest = '/load-' + target + '/' + getUserIdFromUrl();
                
                fetch(partialFetchRequest, {
                    method: "GET",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        "Content-Type": "application/json",
                    },
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error("Failed to load content");
                        }
                        return response.text();
                    })
                    .then(html => {
                        mainInformation.innerHTML = html; // Replace the content
    
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
                        console.error("Error loading content:", error);
                        mainInformation.innerHTML = "<p>Error loading content. Please try again later.</p>";
                    });
    
            });
        }
    });
});

