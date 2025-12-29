(function () {
    function initCpVilleForModal() {
        if (!window.GWGeoCpVille || !window.GWGeoCpVille.contexts || !window.GWGeoCpVille.contexts.lieu_create) {
            return;
        }

        var context = window.GWGeoCpVille.contexts.lieu_create;
        var cpField = document.querySelector(context.cp);
        var villeField = document.querySelector(context.ville);

        if (!cpField || !villeField) {
            return;
        }

        var selectId = 'gw_geo_ville_select_lieu_create';
        var existingSelect = document.getElementById(selectId);
        if (existingSelect) {
            existingSelect.remove();
        }

        var select = document.createElement('select');
        select.id = selectId;
        select.className = (villeField.className || '') + ' gw-geo-ville-select';
        select.style.marginTop = '6px';
        select.style.display = 'none';
        villeField.insertAdjacentElement('afterend', select);

        var debounceTimer = null;
        var lastRequestId = 0;

        function normalizeCp(value) {
            return String(value || '').replace(/\s+/g, '').replace(/[^0-9]/g, '').slice(0, 5);
        }

        function handleCpChange() {
            var cp = normalizeCp(cpField.value);

            if (cp.length < 5) {
                select.style.display = 'none';
                select.innerHTML = '';
                return;
            }

            var requestId = ++lastRequestId;
            var url = window.GWGeoCpVille.apiUrl + '?codePostal=' + encodeURIComponent(cp) + '&fields=nom&format=json';

            fetch(url, { method: 'GET' }).then(function (res) {
                if (!res.ok) throw new Error('http_' + res.status);
                return res.json();
            }).then(function (items) {
                if (requestId !== lastRequestId || !Array.isArray(items)) return;

                var cityMap = {};
                items.forEach(function (item) {
                    if (item && typeof item.nom === 'string') {
                        var city = item.nom.trim();
                        if (city) cityMap[city] = true;
                    }
                });

                var cities = Object.keys(cityMap).sort(function (a, b) {
                    return a.localeCompare(b, 'fr', { sensitivity: 'base' });
                });

                if (!cities.length) {
                    select.style.display = 'none';
                    select.innerHTML = '';
                    return;
                }

                if (cities.length === 1) {
                    select.style.display = 'none';
                    if (String(villeField.value || '').trim() === '') {
                        villeField.value = cities[0];
                        villeField.dispatchEvent(new Event('input', { bubbles: true }));
                        villeField.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    return;
                }

                select.innerHTML = '';
                var placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = window.GWGeoCpVille.i18n.chooseCity || 'Choisir une ville';
                select.appendChild(placeholder);

                cities.forEach(function (city) {
                    var opt = document.createElement('option');
                    opt.value = city;
                    opt.textContent = city;
                    select.appendChild(opt);
                });

                select.style.display = '';
            }).catch(function () {
                if (requestId === lastRequestId) {
                    select.style.display = 'none';
                    select.innerHTML = '';
                }
            });
        }

        cpField.addEventListener('input', function () {
            if (debounceTimer) window.clearTimeout(debounceTimer);
            debounceTimer = window.setTimeout(handleCpChange, 250);
        });

        cpField.addEventListener('change', handleCpChange);

        select.addEventListener('change', function () {
            var chosen = String(select.value || '').trim();
            if (chosen) {
                villeField.value = chosen;
                villeField.dispatchEvent(new Event('input', { bubbles: true }));
                villeField.dispatchEvent(new Event('change', { bubbles: true }));
                select.style.display = 'none';
            }
        });
    }

    function initLieuxModal() {
        var modalTriggers = document.querySelectorAll('[data-gw-modal-target="gw-modal-lieu"]');
        var modalTitle = document.getElementById('gw-modal-lieu-title');
        var modalForm = document.getElementById('gw-form-lieu');
        
        if (!modalTriggers.length || !modalTitle || !modalForm) {
            return;
        }

        var actionInput = document.getElementById('gw_lieu_action');
        var idInput = document.getElementById('gw_lieu_id');
        var nomInput = document.getElementById('gw_lieu_nom');
        var capaciteInput = document.getElementById('gw_lieu_capacite');
        var descriptionInput = document.getElementById('gw_lieu_description');
        var adresseInput = document.getElementById('gw_lieu_adresse');
        var codePostalInput = document.getElementById('gw_lieu_code_postal');
        var villeInput = document.getElementById('gw_lieu_ville');

        modalTriggers.forEach(function (trigger) {
            trigger.addEventListener('click', function () {
                var action = trigger.getAttribute('data-gw-lieu-action');
                
                if (action === 'create') {
                    modalTitle.textContent = 'Créer un lieu';
                    if (actionInput) actionInput.value = 'create';
                    if (idInput) idInput.value = '';
                    
                    if (nomInput) nomInput.value = '';
                    if (capaciteInput) capaciteInput.value = '';
                    if (descriptionInput) descriptionInput.value = '';
                    if (adresseInput) adresseInput.value = '';
                    if (codePostalInput) codePostalInput.value = '';
                    if (villeInput) villeInput.value = '';
                } else if (action === 'edit') {
                    modalTitle.textContent = 'Modifier un lieu';
                    if (actionInput) actionInput.value = 'edit';
                    
                    var lieuId = trigger.getAttribute('data-gw-lieu-id');
                    var lieuNom = trigger.getAttribute('data-gw-lieu-nom');
                    var lieuCapacite = trigger.getAttribute('data-gw-lieu-capacite');
                    var lieuDescription = trigger.getAttribute('data-gw-lieu-description');
                    var lieuAdresse = trigger.getAttribute('data-gw-lieu-adresse');
                    var lieuCodePostal = trigger.getAttribute('data-gw-lieu-code-postal');
                    var lieuVille = trigger.getAttribute('data-gw-lieu-ville');
                    
                    if (idInput) idInput.value = lieuId || '';
                    if (nomInput) nomInput.value = lieuNom || '';
                    if (capaciteInput) capaciteInput.value = lieuCapacite || '';
                    if (descriptionInput) descriptionInput.value = lieuDescription || '';
                    if (adresseInput) adresseInput.value = lieuAdresse || '';
                    if (codePostalInput) codePostalInput.value = lieuCodePostal || '';
                    if (villeInput) villeInput.value = lieuVille || '';
                }
                
                window.setTimeout(function() {
                    initCpVilleForModal();
                }, 100);
            });
        });

        modalForm.addEventListener('submit', function (e) {
            e.preventDefault();
            console.log('Formulaire lieu soumis (mode statique - pas de sauvegarde réelle)');
            console.log('Action:', actionInput ? actionInput.value : 'N/A');
            console.log('Données:', {
                id: idInput ? idInput.value : '',
                nom: nomInput ? nomInput.value : '',
                capacite: capaciteInput ? capaciteInput.value : '',
                description: descriptionInput ? descriptionInput.value : '',
                adresse: adresseInput ? adresseInput.value : '',
                code_postal: codePostalInput ? codePostalInput.value : '',
                ville: villeInput ? villeInput.value : ''
            });
            
            var closeBtn = document.querySelector('[data-gw-modal-close="gw-modal-lieu"]');
            if (closeBtn) {
                closeBtn.click();
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLieuxModal);
    } else {
        initLieuxModal();
    }
})();
