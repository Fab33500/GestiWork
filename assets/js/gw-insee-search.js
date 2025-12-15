(function () {
    'use strict';

    if (typeof window.GWInseeLookup === 'undefined') {
        return;
    }

    var config = window.GWInseeLookup;
    var modal = document.getElementById('gw-insee-modal');
    var searchInput = document.getElementById('gw_insee_search_term');
    var searchButton = document.getElementById('gw_insee_search_button');
    var statusEl = document.getElementById('gw_insee_search_status');
    var resultsEl = document.getElementById('gw_insee_results');
    var closeButton = document.querySelector('[data-gw-modal-close="gw-insee-modal"]');

    if (!modal || !searchInput || !searchButton || !statusEl || !resultsEl) {
        return;
    }

    var currentContext = null;
    var lastResults = [];
    var lastTerm = '';
    var legalFormMap = (config && config.legalForms) ? config.legalForms : null;

    function formatLegalForm(legal) {
        if (!legalFormMap || !legal) {
            return legal;
        }
        if (typeof legal === 'object' && legal.libelle) {
            return legal.libelle;
        }

        var code = typeof legal === 'string' ? legal.trim() : '';
        if (code === '') {
            return '';
        }

        return legalFormMap[code] || code;
    }

    function formatString(template, value) {
        if (typeof template !== 'string') {
            return '';
        }

        return template.replace('%s', value);
    }

    function setStatus(message, type) {
        statusEl.textContent = message || '';
        statusEl.className = 'gw-insee-status';
        if (type) {
            statusEl.classList.add('gw-insee-status--' + type);
        }
    }

    function resetModalState() {
        if (searchInput) {
            searchInput.value = '';
        }
        lastResults = [];
        lastTerm = '';
        resultsEl.innerHTML = '';
        setStatus(config.i18n.initialHint, 'info');
    }

    function buildAdresseValue(data) {
        var ligne1 = data.adresse1 ? data.adresse1.trim() : '';
        var ligne2 = data.adresse2 ? data.adresse2.trim() : '';
        var lines = [];
        if (ligne1 !== '') {
            lines.push(ligne1);
        }
        if (ligne2 !== '') {
            lines.push(ligne2);
        }

        return lines.join('\n');
    }

    function applyFieldValue(field, value) {
        if (!field) {
            return;
        }

        if (field.tagName === 'TEXTAREA' || field.tagName === 'INPUT') {
            field.value = value;
        } else {
            field.textContent = value;
        }

        var inputEvent = new Event('input', { bubbles: true });
        field.dispatchEvent(inputEvent);
        var changeEvent = new Event('change', { bubbles: true });
        field.dispatchEvent(changeEvent);
    }

    function fillFields(contextKey, data) {
        if (!config || !config.contexts || !config.contexts[contextKey]) {
            return;
        }

        var context = config.contexts[contextKey];
        var fields = context.fields || {};

        Object.keys(fields).forEach(function (key) {
            var selector = fields[key];
            if (!selector) {
                return;
            }

            var field = document.querySelector(selector);
            if (!field) {
                return;
            }

            var value = '';
            if (key === 'adresse') {
                value = buildAdresseValue(data);
            } else if (typeof data[key] !== 'undefined') {
                value = data[key];
            }

            if (key === 'siret' && window.GWFormUtils && typeof window.GWFormUtils.formatSiret === 'function') {
                value = window.GWFormUtils.formatSiret(value);
            }

            applyFieldValue(field, value);
        });
    }

    function normalizeEntry(entry) {
        if (!entry || typeof entry !== 'object') {
            return null;
        }

        var siege = entry.siege || {};
        var legal = entry.nature_juridique || siege.nature_juridique || {};
        var adresse1 = siege.adresse_ligne_1 || siege.geo_adresse || '';

        return {
            raison_sociale: entry.nom_complet || entry.nom_raison_sociale || entry.denomination || '',
            siret: siege.siret || entry.siret || '',
            adresse1: adresse1,
            adresse2: siege.adresse_ligne_2 || '',
            cp: siege.code_postal || '',
            ville: siege.commune || siege.libelle_commune || '',
            code_ape: entry.activite_principale || (siege.activite_principale || ''),
            forme_juridique: formatLegalForm(legal),
            displayAdresse: [
                adresse1,
                siege.adresse_ligne_2 || '',
                [siege.code_postal || '', siege.commune || siege.libelle_commune || ''].join(' ').trim()
            ].filter(function (part) {
                return part && part.trim() !== '';
            }).join(', ')
        };
    }

    function renderResults(items, term) {
        resultsEl.innerHTML = '';

        if (!items.length) {
            setStatus(config.i18n.noResults, 'info');
            return;
        }

        setStatus(formatString(config.i18n.resultsFor, term), 'info');
        lastResults = items.slice(0);

        items.forEach(function (item, index) {
            var card = document.createElement('div');
            card.className = 'gw-insee-card';
            card.setAttribute('role', 'listitem');

            var header = document.createElement('div');
            header.className = 'gw-insee-card-header';

            var name = document.createElement('div');
            name.className = 'gw-insee-card-name';
            name.textContent = item.raison_sociale || '';

            var siret = document.createElement('span');
            siret.className = 'gw-insee-card-siret';
            siret.textContent = item.siret ? ('SIRET ' + item.siret) : '';

            header.appendChild(name);
            header.appendChild(siret);

            var meta = document.createElement('div');
            meta.className = 'gw-insee-card-meta';
            meta.textContent = item.displayAdresse || '';

            var extra = document.createElement('div');
            extra.className = 'gw-insee-card-extra';

            if (item.code_ape) {
                var ape = document.createElement('span');
                ape.innerHTML = '<strong>' + config.i18n.activityLabel + ' :</strong> ' + item.code_ape;
                extra.appendChild(ape);
            }

            if (item.forme_juridique) {
                var legal = document.createElement('span');
                legal.innerHTML = '<strong>' + config.i18n.legalLabel + ' :</strong> ' + item.forme_juridique;
                extra.appendChild(legal);
            }

            var actions = document.createElement('div');
            actions.className = 'gw-insee-card-actions';
            var insertBtn = document.createElement('button');
            insertBtn.type = 'button';
            insertBtn.className = 'gw-button gw-button--primary gw-insee-insert-btn';
            insertBtn.textContent = config.i18n.selectLabel;
            insertBtn.addEventListener('click', function () {
                handleInsert(index);
            });
            actions.appendChild(insertBtn);

            card.appendChild(header);
            if (meta.textContent) {
                card.appendChild(meta);
            }
            if (extra.children.length > 0) {
                card.appendChild(extra);
            }
            card.appendChild(actions);

            resultsEl.appendChild(card);
        });
    }

    function setSearching(isLoading) {
        if (!searchButton) {
            return;
        }

        if (isLoading) {
            searchButton.setAttribute('aria-busy', 'true');
            searchButton.disabled = true;
        } else {
            searchButton.removeAttribute('aria-busy');
            searchButton.disabled = false;
        }
    }

    function handleInsert(index) {
        if (!currentContext) {
            setStatus(config.i18n.contextMissing || config.i18n.error, 'error');
            return;
        }

        var data = lastResults[index];
        if (!data) {
            setStatus(config.i18n.error, 'error');
            return;
        }

        fillFields(currentContext, data);
        setStatus(config.i18n.inserted, 'success');

        if (closeButton) {
            closeButton.click();
        }
    }

    function handleSearch() {
        if (!currentContext) {
            setStatus(config.i18n.contextMissing || config.i18n.error, 'error');
            return;
        }

        var term = searchInput.value.trim();
        if (!term) {
            setStatus(config.i18n.emptyTerm, 'error');
            return;
        }

        if (term.length < 3) {
            setStatus(config.i18n.minChars, 'error');
            return;
        }

        setSearching(true);
        setStatus(config.i18n.loading, 'info');

        var perPage = config.perPage || 10;
        var url = config.apiUrl + '?q=' + encodeURIComponent(term) + '&page=1&per_page=' + encodeURIComponent(perPage);

        window.fetch(url, {
            headers: {
                'Accept': 'application/json'
            }
        }).then(function (response) {
            setSearching(false);
            if (!response.ok) {
                throw new Error(response.statusText);
            }
            return response.json();
        }).then(function (payload) {
            var enterprises = Array.isArray(payload.results) ? payload.results : [];
            var normalized = enterprises.map(normalizeEntry).filter(function (entry) {
                return entry !== null;
            });
            renderResults(normalized, term);
        }).catch(function (error) {
            setSearching(false);
            setStatus(config.i18n.error + (error && error.message ? error.message : ''), 'error');
        });
    }

    function attachContextButtons() {
        var buttons = document.querySelectorAll('[data-gw-insee-context]');
        if (!buttons.length) {
            return;
        }

        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                currentContext = button.getAttribute('data-gw-insee-context') || null;
                resetModalState();

                window.setTimeout(function () {
                    if (modal && modal.classList.contains('gw-modal-backdrop--open') && searchInput) {
                        searchInput.focus();
                    }
                }, 180);
            });
        });
    }

    attachContextButtons();
    resetModalState();

    searchButton.addEventListener('click', function () {
        handleSearch();
    });

    searchInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            handleSearch();
        }
    });
})();
