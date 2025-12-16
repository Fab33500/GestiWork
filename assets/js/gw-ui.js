(function () {
    // Gestion des modales communes
    function initModals() {
        var modalTriggers = document.querySelectorAll('[data-gw-modal-target]');
        var modalCloseButtons = document.querySelectorAll('[data-gw-modal-close]');
        var allModals = document.querySelectorAll('.gw-modal-backdrop');
        var lastTriggerByModalId = {};
        var previousModalByModalId = {};

        function openModal(modal) {
            if (!modal) {
                return;
            }
            modal.classList.add('gw-modal-backdrop--open');
            modal.setAttribute('aria-hidden', 'false');
        }

        function closeModal(modal) {
            if (!modal) {
                return;
            }
            modal.classList.remove('gw-modal-backdrop--open');
            modal.setAttribute('aria-hidden', 'true');
        }

        function isFocusableVisible(el) {
            if (!el || typeof el.closest !== 'function') {
                return false;
            }
            var hiddenAncestor = el.closest('[aria-hidden="true"]');
            if (hiddenAncestor) {
                return false;
            }
            return !!(el.offsetWidth || el.offsetHeight || el.getClientRects().length);
        }

        modalTriggers.forEach(function (trigger) {
            trigger.addEventListener('click', function () {
                var targetId = trigger.getAttribute('data-gw-modal-target');
                if (!targetId) {
                    return;
                }

                var modal = document.getElementById(targetId);
                if (!modal) {
                    return;
                }

                // Mémorise le déclencheur pour restaurer le focus à la fermeture
                lastTriggerByModalId[targetId] = trigger;

                // Retient la modale actuellement ouverte (si on ouvre une modale depuis une autre)
                previousModalByModalId[targetId] = null;
                if (allModals && allModals.length) {
                    allModals.forEach(function (backdrop) {
                        if (backdrop !== modal && backdrop.classList.contains('gw-modal-backdrop--open')) {
                            previousModalByModalId[targetId] = backdrop.id || null;
                        }
                    });
                }

                // Ouvre d'abord la modale cible et place le focus dedans,
                // puis ferme les autres modales. Cela évite de masquer (aria-hidden)
                // une modale contenant encore le focus, ce que certains navigateurs bloquent.
                openModal(modal);

                var focusable = modal.querySelector(
                    'input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), a[href], [tabindex]:not([tabindex="-1"])'
                );
                if (focusable && typeof focusable.focus === 'function') {
                    focusable.focus();
                } else {
                    modal.setAttribute('tabindex', '-1');
                    if (typeof modal.focus === 'function') {
                        modal.focus();
                    }
                }

                // Ferme toutes les autres modales éventuellement ouvertes
                if (allModals && allModals.length) {
                    allModals.forEach(function (backdrop) {
                        if (backdrop === modal) {
                            return;
                        }
                        closeModal(backdrop);
                    });
                }

                if (typeof modal.scrollIntoView === 'function') {
                    try {
                        modal.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    } catch (e) {
                        // ignore
                    }
                }
            });
        });

        modalCloseButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var targetId = button.getAttribute('data-gw-modal-close');
                if (!targetId) {
                    return;
                }
                var modal = document.getElementById(targetId);
                if (modal) {
                    // Déplacer le focus HORS de la modale avant de la masquer (sinon warning aria-hidden)
                    var previousModalId = previousModalByModalId[targetId];
                    var previousModal = previousModalId ? document.getElementById(previousModalId) : null;
                    if (previousModal) {
                        openModal(previousModal);
                    }

                    var lastTrigger = lastTriggerByModalId[targetId];
                    if (lastTrigger && typeof lastTrigger.focus === 'function' && isFocusableVisible(lastTrigger)) {
                        lastTrigger.focus();
                    } else if (previousModal) {
                        var previousFocusable = previousModal.querySelector(
                            'input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), a[href], [tabindex]:not([tabindex="-1"])'
                        );
                        if (previousFocusable && typeof previousFocusable.focus === 'function') {
                            previousFocusable.focus();
                        }
                    } else if (document.body && typeof document.body.focus === 'function') {
                        document.body.focus();
                    }

                    closeModal(modal);
                }
            });
        });
    }

    // Gestion des onglets basique (sans URL spécifique)
    function initBasicTabs() {
        var tabs = document.querySelectorAll('.gw-settings-tab');
        var panels = document.querySelectorAll('.gw-settings-panel');

        if (!tabs.length || !panels.length) {
            return;
        }

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                var target = tab.getAttribute('data-gw-tab');
                if (!target) {
                    return;
                }

                // Mise à jour de l'état visuel des onglets
                tabs.forEach(function (t) {
                    t.classList.remove('gw-settings-tab--active');
                });
                panels.forEach(function (panel) {
                    panel.classList.remove('gw-settings-panel--active');
                    if (panel.getAttribute('data-gw-tab-panel') === target) {
                        panel.classList.add('gw-settings-panel--active');
                    }
                });

                tab.classList.add('gw-settings-tab--active');
            });
        });
    }

    // Validation formulaires
    function initFormValidation() {
        var forms = document.querySelectorAll('form');
        
        forms.forEach(function (form) {
            form.addEventListener('submit', function (e) {
                var requiredFields = form.querySelectorAll('[required]');
                var hasErrors = false;
                
                requiredFields.forEach(function (field) {
                    var value = field.value.trim();
                    
                    // Retirer les anciens marqueurs d'erreur
                    field.style.borderColor = '';
                    field.style.backgroundColor = '';
                    
                    if (value === '') {
                        // Marquer le champ en erreur
                        field.style.borderColor = '#d63638';
                        field.style.backgroundColor = '#ffeaea';
                        hasErrors = true;
                        
                        // Focus sur le premier champ en erreur
                        if (!hasErrors || field === requiredFields[0]) {
                            field.focus();
                        }
                    }
                });
                
                if (hasErrors) {
                    e.preventDefault();
                    return false;
                }
            });
            
            // Validation en temps réel : retirer l'erreur dès que l'utilisateur tape
            var requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(function (field) {
                field.addEventListener('input', function () {
                    if (field.value.trim() !== '') {
                        field.style.borderColor = '';
                        field.style.backgroundColor = '';
                    }
                });
            });
        });
    }

    // Auto-remplir le champ Entreprise lors de la sélection dans la card (Apprenant create)
    function initEntrepriseAutoFill() {
        var entrepriseSelect = document.getElementById('gw_apprenant_entreprise_id');
        var entrepriseInput = document.getElementById('gw_apprenant_entreprise');

        if (!entrepriseSelect || !entrepriseInput) {
            return;
        }

        entrepriseSelect.addEventListener('change', function () {
            var selectedOption = entrepriseSelect.options[entrepriseSelect.selectedIndex];
            if (selectedOption && selectedOption.value !== '') {
                entrepriseInput.value = selectedOption.text;
            } else {
                entrepriseInput.value = '';
            }
        });
    }

    // Calcul automatique coût jour <-> heure (basé sur heures_par_jour dynamique)
    function initCoutCalculation() {
        var DEFAULT_HEURES_PAR_JOUR = 7;

        // Récupère le nombre d'heures par jour depuis le champ associé ou utilise la valeur par défaut
        function getHeuresParJour(input) {
            var sourceId = input.getAttribute('data-heures-jour-source');
            if (sourceId) {
                var sourceInput = document.getElementById(sourceId);
                if (sourceInput) {
                    var value = parseFloat(sourceInput.value);
                    if (!isNaN(value) && value > 0) {
                        return value;
                    }
                }
            }
            return DEFAULT_HEURES_PAR_JOUR;
        }

        // Coût jour -> coût heure
        var coutJourInputs = document.querySelectorAll('.gw-cout-jour');
        coutJourInputs.forEach(function (input) {
            input.addEventListener('input', function () {
                var targetId = input.getAttribute('data-cout-heure-target');
                if (!targetId) return;
                var targetInput = document.getElementById(targetId);
                if (!targetInput) return;

                var heuresParJour = getHeuresParJour(input);
                var coutJour = parseFloat(input.value);
                if (!isNaN(coutJour) && coutJour > 0) {
                    var coutHeure = coutJour / heuresParJour;
                    targetInput.value = coutHeure.toFixed(2);
                } else if (input.value === '') {
                    targetInput.value = '';
                }
            });
        });

        // Coût heure -> coût jour
        var coutHeureInputs = document.querySelectorAll('.gw-cout-heure');
        coutHeureInputs.forEach(function (input) {
            input.addEventListener('input', function () {
                var targetId = input.getAttribute('data-cout-jour-target');
                if (!targetId) return;
                var targetInput = document.getElementById(targetId);
                if (!targetInput) return;

                var heuresParJour = getHeuresParJour(input);
                var coutHeure = parseFloat(input.value);
                if (!isNaN(coutHeure) && coutHeure > 0) {
                    var coutJour = coutHeure * heuresParJour;
                    targetInput.value = coutJour.toFixed(2);
                } else if (input.value === '') {
                    targetInput.value = '';
                }
            });
        });

        // Quand heures_par_jour change, recalculer le coût heure à partir du coût jour
        var heuresJourInputs = document.querySelectorAll('.gw-heures-jour');
        heuresJourInputs.forEach(function (input) {
            input.addEventListener('input', function () {
                var coutJourTargetId = input.getAttribute('data-cout-jour-target');
                var coutHeureTargetId = input.getAttribute('data-cout-heure-target');
                if (!coutJourTargetId || !coutHeureTargetId) return;

                var coutJourInput = document.getElementById(coutJourTargetId);
                var coutHeureInput = document.getElementById(coutHeureTargetId);
                if (!coutJourInput || !coutHeureInput) return;

                var heuresParJour = parseFloat(input.value);
                if (isNaN(heuresParJour) || heuresParJour <= 0) return;

                var coutJour = parseFloat(coutJourInput.value);
                if (!isNaN(coutJour) && coutJour > 0) {
                    var coutHeure = coutJour / heuresParJour;
                    coutHeureInput.value = coutHeure.toFixed(2);
                }
            });
        });
    }

    // Initialisation
    initModals();
    initBasicTabs();
    initFormValidation();
    initEntrepriseAutoFill();
    initCoutCalculation();

    // Exposer pour réutilisation
    window.GWUIUtils = {
        initModals: initModals,
        initBasicTabs: initBasicTabs,
        initFormValidation: initFormValidation,
        initEntrepriseAutoFill: initEntrepriseAutoFill,
        initCoutCalculation: initCoutCalculation
    };
})();
