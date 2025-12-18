(function () {
    function initHelpNavigation() {
        var container = document.querySelector('.gw-section[data-gw-help-section]');
        var initialSectionSlug = container ? container.getAttribute('data-gw-help-section') : '';
        var aideSections = document.querySelectorAll('.gw-aide-section');
        if (!aideSections.length) {
            return;
        }

        function showSectionById(sectionId) {
            var found = false;
            aideSections.forEach(function (section) {
                if (section.id === sectionId) {
                    section.style.display = '';
                    found = true;
                } else {
                    section.style.display = 'none';
                }
            });
            return found;
        }

        // Masquer toutes les sections au chargement
        aideSections.forEach(function (section) {
            section.style.display = 'none';
        });

        if (initialSectionSlug) {
            var initialIdFromSection = 'gw-aide-' + initialSectionSlug;
            if (showSectionById(initialIdFromSection)) {
                return;
            }
        }

        // Gestion du clic sur le sommaire
        var summaryLinks = document.querySelectorAll('.gw-section .gw-list a[href^="#gw-aide-"]');
        summaryLinks.forEach(function (link) {
            link.addEventListener('click', function (e) {
                var target = link.getAttribute('href');
                if (!target || target.charAt(0) !== '#') {
                    return;
                }

                e.preventDefault();
                var sectionId = target.substring(1);
                if (showSectionById(sectionId)) {
                    if (typeof window.history.replaceState === 'function') {
                        window.history.replaceState(null, '', target);
                    } else {
                        window.location.hash = target;
                    }

                    var sectionEl = document.getElementById(sectionId);
                    if (sectionEl && typeof sectionEl.scrollIntoView === 'function') {
                        sectionEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            });
        });

        // Si une ancre est présente dans l'URL au chargement, on affiche directement la section correspondante
        var initialHash = window.location.hash;
        if (initialHash && initialHash.charAt(0) === '#') {
            var initialId = initialHash.substring(1);
            showSectionById(initialId);
            return;
        }

        // Fallback : si aucune section n'est sélectionnée, afficher la première
        var firstSection = aideSections[0];
        if (firstSection && firstSection.id) {
            showSectionById(firstSection.id);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initHelpNavigation);
    } else {
        initHelpNavigation();
    }
})();
