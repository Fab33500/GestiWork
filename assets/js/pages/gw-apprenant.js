(function () {
    function onReady(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback);
            return;
        }
        callback();
    }

    function initConfirmations() {
        var i18n = (window.GWApprenant && window.GWApprenant.i18n) ? window.GWApprenant.i18n : {};
        var confirmDeleteText = i18n.confirmDeleteApprenant || 'Supprimer définitivement cet apprenant ?';

        document.querySelectorAll('.gw-apprenant-delete').forEach(function (btn) {
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
