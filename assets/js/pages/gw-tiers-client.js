(function () {
    function onReady(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback);
            return;
        }
        callback();
    }

    function setActiveTabWithUrl(target) {
        if (!target) {
            return;
        }

        try {
            if (typeof window !== 'undefined' && window.history && typeof window.history.replaceState === 'function') {
                var url = new URL(window.location.href);
                url.searchParams.set('tab', target);
                window.history.replaceState(null, '', url.toString());
            }
        } catch (e) {
            // ignore
        }
    }

    function initTabsUrlSync() {
        var tabs = document.querySelectorAll('.gw-settings-tab');

        if (!tabs.length) {
            return;
        }

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                var target = tab.getAttribute('data-gw-tab');
                setActiveTabWithUrl(target);
            });
        });
    }

    function closeAllContactActionMenus() {
        document.querySelectorAll('.gw-contact-actions-menu').forEach(function (menu) {
            menu.style.display = 'none';
        });
        document.querySelectorAll('.gw-contact-actions-trigger').forEach(function (btn) {
            btn.setAttribute('aria-expanded', 'false');
        });
    }

    function initContactActionsMenu() {
        document.addEventListener('click', function (e) {
            var trigger = e.target && e.target.closest ? e.target.closest('.gw-contact-actions-trigger') : null;

            if (!trigger) {
                closeAllContactActionMenus();
                return;
            }

            e.preventDefault();
            e.stopPropagation();

            var wrapper = trigger.parentElement;
            if (!wrapper) {
                return;
            }

            var menu = wrapper.querySelector('.gw-contact-actions-menu');
            if (!menu) {
                return;
            }

            var willOpen = menu.style.display === 'none' || menu.style.display === '';
            closeAllContactActionMenus();

            if (willOpen) {
                menu.style.display = 'block';
                trigger.setAttribute('aria-expanded', 'true');
            }
        });
    }

    function initConfirmations() {
        var i18n = (window.GWTiersClient && window.GWTiersClient.i18n) ? window.GWTiersClient.i18n : {};
        var confirmDeleteContactText = i18n.confirmDeleteContact || 'Supprimer ce contact ?';
        var confirmDeleteTierText = i18n.confirmDeleteTier || 'Supprimer définitivement ce client et tous ses contacts ?';

        document.querySelectorAll('.gw-contact-delete').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                if (!window.confirm(confirmDeleteContactText)) {
                    e.preventDefault();
                }
            });
        });

        document.querySelectorAll('.gw-tier-delete').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                if (!window.confirm(confirmDeleteTierText)) {
                    e.preventDefault();
                }
            });
        });
    }

    function applyEditData(button) {
        if (!button) {
            return;
        }

        var id = button.getAttribute('data-contact-id') || '0';
        var civilite = button.getAttribute('data-contact-civilite') || 'non_renseigne';
        var fonction = button.getAttribute('data-contact-fonction') || '';
        var nom = button.getAttribute('data-contact-nom') || '';
        var prenom = button.getAttribute('data-contact-prenom') || '';
        var mail = button.getAttribute('data-contact-mail') || '';
        var tel1 = button.getAttribute('data-contact-tel1') || '';
        var tel2 = button.getAttribute('data-contact-tel2') || '';
        var participeFormation = button.getAttribute('data-contact-participe-formation') || '0';

        var idInput = document.getElementById('gw_client_contact_edit_id');
        var civiliteInput = document.getElementById('gw_client_contact_edit_civilite');
        var fonctionInput = document.getElementById('gw_client_contact_edit_fonction');
        var nomInput = document.getElementById('gw_client_contact_edit_nom');
        var prenomInput = document.getElementById('gw_client_contact_edit_prenom');
        var mailInput = document.getElementById('gw_client_contact_edit_mail');
        var tel1Input = document.getElementById('gw_client_contact_edit_tel1');
        var tel2Input = document.getElementById('gw_client_contact_edit_tel2');
        var participeFormationInputs = document.querySelectorAll('#gw-modal-client-contact-edit input[name="participe_formation"]');

        if (idInput) {
            idInput.value = id;
        }
        if (civiliteInput) {
            civiliteInput.value = civilite;
        }
        if (fonctionInput) {
            fonctionInput.value = fonction;
        }
        if (nomInput) {
            nomInput.value = nom;
        }
        if (prenomInput) {
            prenomInput.value = prenom;
        }
        if (mailInput) {
            mailInput.value = mail;
        }
        if (tel1Input) {
            tel1Input.value = tel1;
        }
        if (tel2Input) {
            tel2Input.value = tel2;
        }

        if (participeFormationInputs && participeFormationInputs.length) {
            participeFormationInputs.forEach(function (input) {
                input.checked = input.value === String(participeFormation);
            });
        }
    }

    function initContactEditModalPrefill() {
        document.querySelectorAll('[data-gw-modal-target="gw-modal-client-contact-edit"]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                applyEditData(btn);
                closeAllContactActionMenus();
            });
        });
    }

    function initContactCreateModalReset() {
        var openButtons = document.querySelectorAll('[data-gw-modal-target="gw-modal-client-contacts"]');
        if (!openButtons.length) {
            return;
        }

        openButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var inputs = document.querySelectorAll('#gw-modal-client-contacts input[name="participe_formation"]');
                if (inputs && inputs.length) {
                    inputs.forEach(function (input) {
                        input.checked = false;
                    });
                }
            });
        });
    }

    onReady(function () {
        initTabsUrlSync();
        initContactActionsMenu();
        initConfirmations();
        initContactEditModalPrefill();
        initContactCreateModalReset();
    });
})();
