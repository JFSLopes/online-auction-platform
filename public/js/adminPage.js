const element = document.getElementById('toggle-search-bar');

if (element != null){
    element.addEventListener('click', function () {
        const searchBar = document.getElementById('searchBar');
        if (searchBar.style.display === 'none' || searchBar.style.display === '') {
            searchBar.style.display = 'flex';
        } else {
            searchBar.style.display = 'none';
        }
    });
}


const toggleSearchBarButton = document.getElementById('toggle-search-bar');
const searchBar = document.getElementById('searchBar');

const toggleRegisterUserButton = document.getElementById('toggle-register-user-form');
const registerUserForm = document.getElementById('register-user-form');

const toggleRegisterAdminButton = document.getElementById('toggle-register-admin-form');
const registerAdminForm = document.getElementById('register-admin-form');

// Toggle visibility function
function toggleVisibility(button, form) {
    if (button && form) {
        button.addEventListener('click', () => {
            form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
        });
    }
}

// Apply toggles
toggleVisibility(toggleRegisterUserButton, registerUserForm);
toggleVisibility(toggleRegisterAdminButton, registerAdminForm);