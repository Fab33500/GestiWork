(function () {
    function onReady(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback);
            return;
        }
        callback();
    }

    function initConfirmations() {
        var i18n = (window.GWResponsable && window.GWResponsable.i18n) ? window.GWResponsable.i18n : {};
        var confirmDeleteText = i18n.confirmDeleteFormateur || 'Supprimer définitivement ce formateur / responsable pédagogique ?';

        document.querySelectorAll('.gw-formateur-delete').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                if (!window.confirm(confirmDeleteText)) {
                    e.preventDefault();
                }
            });
        });
    }

    onReady(function () {
        initConfirmations();
    });
})();
