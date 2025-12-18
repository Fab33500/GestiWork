(function () {
    'use strict';

    var config = window.GWFormValidation || null;
    if (!config || !config.rules) {
        return;
    }

    var HIDDEN_CLASS = 'gw-display-none';

    function isConditionMet(condition) {
        if (!condition || typeof condition !== 'object') {
            return true;
        }

        var fieldId = condition.field ? String(condition.field) : '';
        if (fieldId === '') {
            return true;
        }

        var field = getFieldById(fieldId);
        if (!field) {
            return false;
        }

        var currentValue = normalizeValue(field.value);
        if (typeof condition.equals !== 'undefined') {
            if (Array.isArray(condition.equals)) {
                return condition.equals.some(function (val) {
                    return currentValue === String(val);
                });
            }
            return currentValue === String(condition.equals);
        }

        if (typeof condition.notEquals !== 'undefined') {
            if (Array.isArray(condition.notEquals)) {
                return condition.notEquals.every(function (val) {
                    return currentValue !== String(val);
                });
            }
            return currentValue !== String(condition.notEquals);
        }

        return true;
    }

    function isFieldRequired(fieldRule) {
        if (!fieldRule) {
            return false;
        }

        if (fieldRule.required === true) {
            return true;
        }

        if (fieldRule.requiredIf) {
            return isConditionMet(fieldRule.requiredIf);
        }

        return false;
    }

    function isValueInvalid(value, fieldRule) {
        if (!fieldRule || !Array.isArray(fieldRule.invalidValues)) {
            return false;
        }
        var normalized = normalizeValue(value);
        return fieldRule.invalidValues.some(function (invalidValue) {
            return normalized === String(invalidValue);
        });
    }

    function normalizeValue(value) {
        return String(value || '').trim();
    }

    function getFieldById(id) {
        if (!id) {
            return null;
        }
        return document.getElementById(id);
    }

    function isFieldHidden(field) {
        if (!field || typeof field.closest !== 'function') {
            return false;
        }
        return !!field.closest('.' + HIDDEN_CLASS);
    }

    function applyFieldConstraints(field, rule) {
        if (!field || !rule) {
            return;
        }

        if (field.disabled || isFieldHidden(field)) {
            return;
        }

        field.required = isFieldRequired(rule);

        if (typeof rule.pattern === 'string' && rule.pattern !== '') {
            field.setAttribute('pattern', rule.pattern);
        }

        if (typeof rule.maxlength === 'number' && rule.maxlength > 0) {
            field.setAttribute('maxlength', String(rule.maxlength));
        }

        if (typeof rule.inputmode === 'string' && rule.inputmode !== '') {
            field.setAttribute('inputmode', rule.inputmode);
        }

        if (rule.type === 'email' && field.tagName === 'INPUT') {
            field.setAttribute('type', 'email');
        }
    }

    function renderError(contextRule, message) {
        var errorSelector = contextRule && contextRule.errorSelector ? String(contextRule.errorSelector) : '';
        if (errorSelector === '') {
            return;
        }

        var errorEl = document.querySelector(errorSelector);
        if (!errorEl) {
            return;
        }

        errorEl.textContent = message || '';

        if (message) {
            errorEl.classList.remove(HIDDEN_CLASS);
        } else {
            errorEl.classList.add(HIDDEN_CLASS);
        }

        if (message && typeof errorEl.focus === 'function') {
            errorEl.focus();
        }
    }

    function validateContext(contextKey, contextRule) {
        if (!contextRule) {
            return true;
        }

        var fieldsRule = contextRule.fields || {};
        var fieldIds = Object.keys(fieldsRule);

        var missingRequired = false;

        fieldIds.forEach(function (fieldId) {
            var field = getFieldById(fieldId);
            var rule = fieldsRule[fieldId] || {};

            if (!field) {
                return;
            }

            if (field.disabled || isFieldHidden(field)) {
                return;
            }

            applyFieldConstraints(field, rule);

            if (isFieldRequired(rule)) {
                var value = normalizeValue(field.value);
                if (value === '' || isValueInvalid(value, rule)) {
                    missingRequired = true;
                }
            }
        });

        var groups = Array.isArray(contextRule.groups) ? contextRule.groups : [];
        var missingGroup = false;

        groups.forEach(function (group) {
            if (!group || typeof group !== 'object') {
                return;
            }

            if (group.type === 'atLeastOne' && Array.isArray(group.fields)) {
                var hasOne = group.fields.some(function (fieldId) {
                    var field = getFieldById(fieldId);
                    if (!field || field.disabled) {
                        return false;
                    }
                    return normalizeValue(field.value) !== '';
                });

                if (!hasOne) {
                    missingGroup = true;
                }
            }

            if (group.type === 'requiredRadio') {
                var name = group.name ? String(group.name) : '';
                if (name === '') {
                    return;
                }

                var radios = document.querySelectorAll('input[type="radio"][name="' + name.replace(/"/g, '\\"') + '"]');
                if (!radios || !radios.length) {
                    return;
                }

                var hasChecked = false;
                radios.forEach(function (r) {
                    if (r && !r.disabled && r.checked) {
                        hasChecked = true;
                    }
                });

                if (!hasChecked) {
                    missingGroup = true;
                }
            }
        });

        if (missingRequired || missingGroup) {
            var contextMessages = (contextRule && contextRule.messages) ? contextRule.messages : null;
            var i18n = contextMessages || ((config && config.i18n) ? config.i18n : {});

            if (missingRequired && missingGroup) {
                renderError(contextRule, i18n.requiredAndGroup || 'Merci de renseigner tous les champs obligatoires et les informations requises.');
            } else if (missingRequired) {
                renderError(contextRule, i18n.requiredOnly || 'Merci de renseigner tous les champs obligatoires.');
            } else {
                renderError(contextRule, i18n.groupOnly || 'Merci de renseigner les informations requises.');
            }

            return false;
        }

        renderError(contextRule, '');
        return true;
    }

    function attachContext(contextKey, contextRule) {
        if (!contextRule || !contextRule.fields) {
            return;
        }

        var fieldsRule = contextRule.fields || {};
        var fieldIds = Object.keys(fieldsRule);

        var anyField = null;
        for (var i = 0; i < fieldIds.length; i++) {
            anyField = getFieldById(fieldIds[i]);
            if (anyField) {
                break;
            }
        }

        if (!anyField) {
            return;
        }

        var form = anyField.closest('form');
        if (!form) {
            return;
        }

        fieldIds.forEach(function (fieldId) {
            var field = getFieldById(fieldId);
            if (!field) {
                return;
            }
            if (isFieldHidden(field)) {
                return;
            }
            applyFieldConstraints(field, fieldsRule[fieldId]);
        });

        form.addEventListener('submit', function (e) {
            var ok = validateContext(contextKey, contextRule);
            if (!ok) {
                e.preventDefault();
            }
        });
    }

    Object.keys(config.rules).forEach(function (contextKey) {
        attachContext(contextKey, config.rules[contextKey]);
    });
})();
