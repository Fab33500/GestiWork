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

        if (fields.raisonSocialeInput) {
            fields.raisonSocialeInput.required = !isParticulier;
        }
        if (fields.siretInput) {
            fields.siretInput.required = !isParticulier;
        }
        if (fields.nomInput) {
            fields.nomInput.required = isParticulier;
        }
        if (fields.prenomInput) {
            fields.prenomInput.required = isParticulier;
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
            fields.nomField.style.display = isParticulier ? '' : 'none';
        }
        if (fields.prenomField) {
            fields.prenomField.style.display = isParticulier ? '' : 'none';
        }
    }

    function bindTierClient() {
        var tierCreateType = document.getElementById('gw_tier_create_type');
        var tierViewType = document.getElementById('gw_tier_view_type');

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
                prenomField: document.getElementById('gw_tier_create_field_prenom')
            };

            var updateCreate = function () {
                setTierRequiredByType(tierCreateType.value, createFields);
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

    var api = {
        gwFormatPhone: gwFormatPhone,
        gwFormatSiret: gwFormatSiret,
        bindSettingsGeneral: bindSettingsGeneral,
        bindTierClient: bindTierClient
    };

    window.GWFormUtils = window.GWFormUtils || api;

    function init() {
        bindSettingsGeneral();
        bindTierClient();
    }

    if (document) {
        if (document.readyState === 'loading' && typeof document.addEventListener === 'function') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    }
})(window, document);
