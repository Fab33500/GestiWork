(function () {
    'use strict';

    var config = window.GWGeoCpVille || null;
    if (!config || !config.contexts) {
        return;
    }

    function dispatchFieldEvents(field) {
        if (!field) {
            return;
        }

        var inputEvent = new Event('input', { bubbles: true });
        field.dispatchEvent(inputEvent);
        var changeEvent = new Event('change', { bubbles: true });
        field.dispatchEvent(changeEvent);
    }

    function normalizeCp(value) {
        return String(value || '').replace(/\s+/g, '').replace(/[^0-9]/g, '').slice(0, 5);
    }

    function ensureSelect(contextKey, villeField) {
        if (!villeField) {
            return null;
        }

        var selectId = 'gw_geo_ville_select_' + contextKey;
        var existing = document.getElementById(selectId);
        if (existing) {
            return existing;
        }

        var select = document.createElement('select');
        select.id = selectId;
        select.className = (villeField.className || '') + ' gw-geo-ville-select';
        select.style.marginTop = '6px';
        select.style.display = 'none';

        villeField.insertAdjacentElement('afterend', select);

        return select;
    }

    function fillSelect(select, cities) {
        if (!select) {
            return;
        }

        select.innerHTML = '';

        var placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = (config.i18n && config.i18n.chooseCity) ? config.i18n.chooseCity : 'Choisir une ville';
        select.appendChild(placeholder);

        cities.forEach(function (city) {
            var opt = document.createElement('option');
            opt.value = city;
            opt.textContent = city;
            select.appendChild(opt);
        });

        select.style.display = '';
    }

    function hideSelect(select) {
        if (!select) {
            return;
        }

        select.style.display = 'none';
        select.innerHTML = '';
    }

    function fetchCitiesByCp(cp) {
        var url = config.apiUrl + '?codePostal=' + encodeURIComponent(cp) + '&fields=nom&format=json';
        return fetch(url, { method: 'GET' }).then(function (res) {
            if (!res.ok) {
                throw new Error('http_' + res.status);
            }
            return res.json();
        }).then(function (items) {
            if (!Array.isArray(items)) {
                return [];
            }

            var map = Object.create(null);
            items.forEach(function (item) {
                if (item && typeof item.nom === 'string') {
                    var city = item.nom.trim();
                    if (city) {
                        map[city] = true;
                    }
                }
            });

            return Object.keys(map).sort(function (a, b) {
                return a.localeCompare(b, 'fr', { sensitivity: 'base' });
            });
        });
    }

    function attachToContext(contextKey, context) {
        if (!context || !context.cp || !context.ville) {
            return;
        }

        var cpField = document.querySelector(context.cp);
        var villeField = document.querySelector(context.ville);

        if (!cpField || !villeField) {
            return;
        }

        var select = ensureSelect(contextKey, villeField);
        var lastRequestId = 0;
        var debounceTimer = null;

        function handleCpChange() {
            var cp = normalizeCp(cpField.value);

            if (cp.length < 5) {
                hideSelect(select);
                return;
            }

            var requestId = ++lastRequestId;

            fetchCitiesByCp(cp).then(function (cities) {
                if (requestId !== lastRequestId) {
                    return;
                }

                if (villeField.disabled) {
                    hideSelect(select);
                    return;
                }

                if (!cities.length) {
                    hideSelect(select);
                    return;
                }

                if (cities.length === 1) {
                    hideSelect(select);
                    if (String(villeField.value || '').trim() === '') {
                        villeField.value = cities[0];
                        dispatchFieldEvents(villeField);
                    }
                    return;
                }

                fillSelect(select, cities);
            }).catch(function () {
                if (requestId !== lastRequestId) {
                    return;
                }
                hideSelect(select);
            });
        }

        cpField.addEventListener('input', function () {
            if (debounceTimer) {
                window.clearTimeout(debounceTimer);
            }
            debounceTimer = window.setTimeout(handleCpChange, 250);
        });

        cpField.addEventListener('change', function () {
            handleCpChange();
        });

        if (select) {
            select.addEventListener('change', function () {
                if (villeField.disabled) {
                    hideSelect(select);
                    return;
                }

                var chosen = String(select.value || '').trim();
                if (!chosen) {
                    return;
                }

                villeField.value = chosen;
                dispatchFieldEvents(villeField);
                hideSelect(select);
            });
        }
    }

    Object.keys(config.contexts).forEach(function (contextKey) {
        attachToContext(contextKey, config.contexts[contextKey]);
    });
})();
