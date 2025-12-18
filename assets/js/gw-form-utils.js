(function (window, document) {
    'use strict';

    function gwFormatPhone(value) {
        if (!value) {
            return '';
        }
        var digits = String(value).replace(/\D/g, '').slice(0, 10);
        if (digits.length !== 10) {
            return String(value).trim();
        }
        return digits.replace(/(\d{2})(?=\d)/g, '$1 ').trim();
    }

    function gwFormatSiret(value) {
        if (!value) {
            return '';
        }
        var digits = String(value).replace(/\D/g, '');
        if (digits.length === 9) {
            return digits.replace(/^(\d{3})(\d{3})(\d{3})$/, '$1 $2 $3');
        }
        if (digits.length === 14) {
            return digits.replace(/^(\d{3})(\d{3})(\d{3})(\d{5})$/, '$1 $2 $3 $4');
        }
        return String(value).trim();
    }

    function bindPhoneBlur(input) {
        if (!input) {
            return;
        }
        input.addEventListener('blur', function () {
            input.value = gwFormatPhone(input.value);
        });
    }

    function bindSiretBlur(input) {
        if (!input) {
            return;
        }
        input.addEventListener('blur', function () {
            input.value = gwFormatSiret(input.value);
        });
    }

    function bindChecklists() {
        if (!document || typeof document.querySelectorAll !== 'function') {
            return;
        }

        var checklists = document.querySelectorAll('[data-gw-checklist]');
        if (!checklists || !checklists.length) {
            return;
        }

        var normalize = function (value) {
            return String(value || '').toLowerCase().trim();
        };

        var forEachNode = function (nodes, cb) {
            if (!nodes) {
                return;
            }
            for (var i = 0; i < nodes.length; i++) {
                cb(nodes[i]);
            }
        };

        forEachNode(checklists, function (checklist) {
            var searchInput = checklist.querySelector('[data-gw-checklist-search]');
            var allButton = checklist.querySelector('[data-gw-checklist-all]');
            var noneButton = checklist.querySelector('[data-gw-checklist-none]');
            var items = checklist.querySelectorAll('[data-gw-checklist-item]');

            var applyFilter = function () {
                var query = normalize(searchInput ? searchInput.value : '');
                forEachNode(items, function (item) {
                    var text = item.getAttribute('data-gw-checklist-text') || item.textContent || '';
                    var visible = query === '' || normalize(text).indexOf(query) !== -1;
                    item.style.display = visible ? '' : 'none';
                });
            };

            var setAll = function (checked) {
                forEachNode(items, function (item) {
                    if (item.style.display === 'none') {
                        return;
                    }
                    var input = item.querySelector('input[type="checkbox"]');
                    if (!input || input.disabled) {
                        return;
                    }
                    input.checked = checked;
                });
            };

            if (searchInput) {
                searchInput.addEventListener('input', applyFilter);
            }
            if (allButton) {
                allButton.addEventListener('click', function () {
                    setAll(true);
                });
            }
            if (noneButton) {
                noneButton.addEventListener('click', function () {
                    setAll(false);
                });
            }

            applyFilter();
        });
    }

    function setNumericInputAttributes(input, maxLength) {
        if (!input) {
            return;
        }
        if (typeof maxLength === 'number') {
            input.maxLength = maxLength;
        }
        input.setAttribute('inputmode', 'numeric');
        var count = typeof maxLength === 'number' ? maxLength : 5;
        input.setAttribute('pattern', '[0-9]{' + count + '}');
    }

    function bindSettingsGeneral() {
        var telFixe = document.getElementById('gw_telephone_fixe');
        var telPortable = document.getElementById('gw_telephone_portable');
        var siretField = document.getElementById('gw_siret');

        if (!telFixe && !telPortable && !siretField) {
            return;
        }

        bindPhoneBlur(telFixe);
        bindPhoneBlur(telPortable);
        bindSiretBlur(siretField);
    }

    function setTierRequiredByType(typeValue, fields) {
        var isParticulier = typeValue === 'client_particulier';
        var isIndependant = typeValue === 'entreprise_independant';

        if (fields.raisonSocialeInput) {
            fields.raisonSocialeInput.required = !isParticulier;
        }
        if (fields.siretInput) {
            fields.siretInput.required = !isParticulier;
        }
        if (fields.nomInput) {
            fields.nomInput.required = isParticulier || isIndependant;
        }
        if (fields.prenomInput) {
            fields.prenomInput.required = isParticulier || isIndependant;
        }

        if (fields.siretField) {
            fields.siretField.style.display = isParticulier ? 'none' : '';
        }
        if (fields.formeJuridiqueField) {
            fields.formeJuridiqueField.style.display = isParticulier ? 'none' : '';
        }
        if (fields.raisonSocialeField) {
            fields.raisonSocialeField.style.display = isParticulier ? 'none' : '';
        }
        if (fields.nomField) {
            fields.nomField.style.display = (isParticulier || isIndependant) ? '' : 'none';
        }
        if (fields.prenomField) {
            fields.prenomField.style.display = (isParticulier || isIndependant) ? '' : 'none';
        }
        if (fields.searchButtonWrapper) {
            fields.searchButtonWrapper.style.display = isParticulier ? 'none' : '';
        }
    }

    function bindTierClient() {
        var tierCreateType = document.getElementById('gw_tier_create_type');
        var tierViewType = document.getElementById('gw_tier_view_type');

        var createFinanceurEntreprisesCard = document.getElementById('gw_tier_create_financeur_entreprises_card');
        var createEntrepriseFinanceursCard = document.getElementById('gw_tier_create_entreprise_financeurs_card');
        var viewFinanceurEntreprisesCard = document.getElementById('gw_tier_view_financeur_entreprises_card');
        var viewEntrepriseFinanceursCard = document.getElementById('gw_tier_view_entreprise_financeurs_card');

        var hasTierCreate = !!tierCreateType;
        var hasTierView = !!tierViewType;

        var contactTel1 = document.getElementById('gw_client_contact_tel1');
        var contactTel2 = document.getElementById('gw_client_contact_tel2');

        if (!hasTierCreate && !hasTierView && !contactTel1 && !contactTel2) {
            return;
        }

        if (hasTierCreate) {
            var createFields = {
                raisonSocialeInput: document.getElementById('gw_tier_create_raison_sociale'),
                nomInput: document.getElementById('gw_tier_create_nom'),
                prenomInput: document.getElementById('gw_tier_create_prenom'),
                siretInput: document.getElementById('gw_tier_create_siret'),

                siretField: document.getElementById('gw_tier_create_field_siret'),
                formeJuridiqueField: document.getElementById('gw_tier_create_field_forme_juridique'),
                raisonSocialeField: document.getElementById('gw_tier_create_field_raison_sociale'),
                nomField: document.getElementById('gw_tier_create_field_nom'),
                prenomField: document.getElementById('gw_tier_create_field_prenom'),
                searchButtonWrapper: document.getElementById('gw_tier_create_insee_button_wrapper')
            };

            var updateCreate = function () {
                setTierRequiredByType(tierCreateType.value, createFields);

                if (createFinanceurEntreprisesCard) {
                    createFinanceurEntreprisesCard.classList.toggle('gw-display-none', tierCreateType.value !== 'financeur');
                }
                if (createEntrepriseFinanceursCard) {
                    createEntrepriseFinanceursCard.classList.toggle(
                        'gw-display-none',
                        !(tierCreateType.value === 'entreprise' || tierCreateType.value === 'client_entreprise' || tierCreateType.value === 'entreprise_independant')
                    );
                }
            };

            updateCreate();
            tierCreateType.addEventListener('change', updateCreate);

            bindSiretBlur(createFields.siretInput);
            bindPhoneBlur(document.getElementById('gw_tier_create_phone'));
            bindPhoneBlur(document.getElementById('gw_tier_create_phone_mobile'));
        }

        if (hasTierView) {
            var viewFields = {
                raisonSocialeInput: document.getElementById('gw_tier_view_raison_sociale'),
                nomInput: document.getElementById('gw_tier_view_nom'),
                prenomInput: document.getElementById('gw_tier_view_prenom'),
                siretInput: document.getElementById('gw_tier_view_siret'),

                siretField: document.getElementById('gw_tier_view_field_siret'),
                formeJuridiqueField: document.getElementById('gw_tier_view_field_forme_juridique'),
                raisonSocialeField: document.getElementById('gw_tier_view_field_raison_sociale'),
                nomField: document.getElementById('gw_tier_view_field_nom'),
                prenomField: document.getElementById('gw_tier_view_field_prenom')
            };

            var updateView = function () {
                setTierRequiredByType(tierViewType.value, viewFields);

                if (viewFinanceurEntreprisesCard) {
                    viewFinanceurEntreprisesCard.classList.toggle('gw-display-none', tierViewType.value !== 'financeur');
                }
                if (viewEntrepriseFinanceursCard) {
                    viewEntrepriseFinanceursCard.classList.toggle(
                        'gw-display-none',
                        !(tierViewType.value === 'entreprise' || tierViewType.value === 'client_entreprise' || tierViewType.value === 'entreprise_independant')
                    );
                }
            };

            updateView();
            tierViewType.addEventListener('change', updateView);

            bindSiretBlur(viewFields.siretInput);
            bindPhoneBlur(document.getElementById('gw_tier_view_telephone'));
            bindPhoneBlur(document.getElementById('gw_tier_view_telephone_portable'));
        }

        bindPhoneBlur(contactTel1);
        bindPhoneBlur(contactTel2);
    }

    function bindApprenantForm() {
        var telField = document.getElementById('gw_apprenant_telephone');
        var cpField = document.getElementById('gw_apprenant_cp');

        bindPhoneBlur(telField);
        setNumericInputAttributes(cpField, 5);
    }

    function bindResponsableForm() {
        var telField = document.getElementById('gw_responsable_telephone');
        var cpField = document.getElementById('gw_responsable_code_postal');

        var sousTraitantSelect = document.getElementById('gw_responsable_sous_traitant');
        var ndaField = document.getElementById('gw_responsable_nda_sous_traitant');
        var ndaWrapper = document.getElementById('gw_responsable_field_nda_sous_traitant');

        bindPhoneBlur(telField);
        setNumericInputAttributes(cpField, 5);

        if (sousTraitantSelect && ndaField && ndaWrapper && typeof ndaWrapper.classList !== 'undefined') {
            var updateNdaVisibility = function () {
                var show = sousTraitantSelect.value === 'Oui';

                ndaWrapper.classList.toggle('gw-display-none', !show);
                ndaField.disabled = !show;
                if (!show) {
                    ndaField.required = false;
                }
            };

            updateNdaVisibility();
            sousTraitantSelect.addEventListener('change', updateNdaVisibility);
        }
    }

    var api = {
        gwFormatPhone: gwFormatPhone,
        gwFormatSiret: gwFormatSiret,
        formatPhone: gwFormatPhone,
        formatSiret: gwFormatSiret,
        bindSettingsGeneral: bindSettingsGeneral,
        bindTierClient: bindTierClient,
        bindApprenantForm: bindApprenantForm,
        bindResponsableForm: bindResponsableForm
    };

    window.GWFormUtils = window.GWFormUtils || api;

    function init() {
        bindSettingsGeneral();
        bindTierClient();
        bindApprenantForm();
        bindResponsableForm();
        bindChecklists();
    }

    if (document) {
        if (document.readyState === 'loading' && typeof document.addEventListener === 'function') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    }
})(window, document);
