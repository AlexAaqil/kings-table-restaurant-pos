document.addEventListener('DOMContentLoaded', function () {
    var burgerIcon = document.getElementById('burgerIcon');
    var navLinks = document.querySelector('.nav_links');

    if (burgerIcon) {
        burgerIcon.addEventListener('click', function () {
            navLinks.classList.toggle('show');
            burgerIcon.classList.toggle('active_burger', navLinks.classList.contains('show'));
        });
    }
});