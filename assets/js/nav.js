// Ajout automatique d'une classe active sur le lien du menu qui correspond à la page courante
const currentPath = window.location.pathname;
const navLinks = document.querySelectorAll('.navbar-nav .nav-link');

navLinks.forEach(link => {
    if(link.getAttribute('href') === currentPath) {
        link.classList.add('active');
    } else {
        link.classList.remove('active');
    }
});
