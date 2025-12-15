(function () {
    var button = document.querySelector('.gw-header-toggle');
    var nav = document.getElementById('gw-nav');

    if (!button || !nav) {
        return;
    }

    button.addEventListener('click', function () {
        var isOpen = nav.classList.toggle('gw-nav--open');
        button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        document.documentElement.classList.toggle('gw-nav-open', isOpen);
    });
})();
