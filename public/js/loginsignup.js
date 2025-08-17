const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');
const nextButton = document.getElementById('nextButton');

registerBtn.addEventListener('click', () => {
    container.classList.add("active");
});

loginBtn.addEventListener('click', () => {
    container.classList.remove("active");
});


document.getElementById('nextButton').addEventListener('click', function() {
    // Oculta o primeiro formulário de registro
    document.querySelector('.form-container.sign-up').style.display = 'none';
    // Mostra o segundo formulário de registro
    document.querySelector('.form-container.sign-up.register-details').style.display = 'block';
    // Mostra o painel de registro à direita
    document.querySelector('.toggle-panel.toggle-right').style.display = 'block';
});