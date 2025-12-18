(function () {
    function onReady(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback);
            return;
        }
        callback();
    }

    onReady(function () {
        // Logique spécifique Settings : gestion URL avec '/settings/tab/'
        var tabs = document.querySelectorAll('.gw-settings-tab');

        if (tabs.length) {
            tabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    var target = tab.getAttribute('data-gw-tab');
                    if (!target) {
                        return;
                    }

                    // Mise à jour de l'URL pour refléter l'onglet actif, sans recharger la page
                    try {
                        if (typeof window !== 'undefined' && window.history && typeof window.history.replaceState === 'function') {
                            var loc = window.location;
                            var path = loc.pathname || '';
                            var newPath = path;

                            if (/\/settings\/[^/]+\/?$/.test(path)) {
                                // Remplace le dernier segment après /settings/
                                newPath = path.replace(/(\/settings\/)[^/]+\/?$/, '$1' + target + '/');
                            } else if (/\/settings\/?$/.test(path)) {
                                // Aucun segment de section encore présent
                                newPath = path.replace(/\/settings\/?$/, '/settings/' + target + '/');
                            }

                            if (newPath && newPath !== path) {
                                var newUrl = newPath + (loc.search || '') + (loc.hash || '');
                                window.history.replaceState(null, '', newUrl);
                            }
                        }
                    } catch (e) {
                        // En cas d'erreur, on ne bloque pas le changement d'onglet
                    }
                });
            });
        }

        // Formatage automatique des champs au blur
        function gwFormatPhone(value) {
            if (window.GWFormUtils && typeof window.GWFormUtils.gwFormatPhone === 'function') {
                return window.GWFormUtils.gwFormatPhone(value);
            }
            return value ? String(value).trim() : '';
        }

        function gwFormatSiret(value) {
            if (window.GWFormUtils && typeof window.GWFormUtils.gwFormatSiret === 'function') {
                return window.GWFormUtils.gwFormatSiret(value);
            }
            return value ? String(value).trim() : '';
        }

        function gwNormalizeIban(value) {
            if (!value) {
                return '';
            }
            return value.toUpperCase().replace(/\s+/g, '');
        }

        function gwNormalizeBic(value) {
            if (!value) {
                return '';
            }
            return value.toUpperCase().replace(/\s+/g, '');
        }

        var telFixe = document.getElementById('gw_telephone_fixe');
        if (telFixe) {
            telFixe.addEventListener('blur', function () {
                telFixe.value = gwFormatPhone(telFixe.value);
            });
        }

        var telPortable = document.getElementById('gw_telephone_portable');
        if (telPortable) {
            telPortable.addEventListener('blur', function () {
                telPortable.value = gwFormatPhone(telPortable.value);
            });
        }

        var siretField = document.getElementById('gw_siret');
        if (siretField) {
            siretField.addEventListener('blur', function () {
                siretField.value = gwFormatSiret(siretField.value);
            });
        }

        var ibanField = document.getElementById('gw_iban');
        if (ibanField) {
            ibanField.addEventListener('blur', function () {
                ibanField.value = gwNormalizeIban(ibanField.value);
            });
        }

        var bicField = document.getElementById('gw_bic');
        if (bicField) {
            bicField.addEventListener('blur', function () {
                bicField.value = gwNormalizeBic(bicField.value);
            });
        }

        var regimeSelect = document.getElementById('gw_regime_tva');
        var tvaCard = document.getElementById('gw_tva_card');
        if (regimeSelect && tvaCard) {
            var updateTvaCard = function () {
                if (regimeSelect.value === 'assujetti') {
                    tvaCard.style.display = '';
                } else {
                    tvaCard.style.display = 'none';
                }
            };
            updateTvaCard();
            regimeSelect.addEventListener('change', updateTvaCard);
        }

        var settings = window.GWSettings || {};
        var i18n = settings.i18n || {};

        // Gestion de la zone d'édition PDF (en-tête / pied de page)
        var pdfEditorGroup = document.getElementById('gw-pdf-editor');
        var pdfEditorTitle = document.getElementById('gw-pdf-editor-title');
        var pdfEditorContext = document.getElementById('gw_pdf_editor_context');
        var openPdfHeaderBtn = document.getElementById('gw-open-pdf-header-editor');
        var openPdfFooterBtn = document.getElementById('gw-open-pdf-footer-editor');

        function gwOpenPdfEditor(context) {
            if (!pdfEditorGroup) {
                return;
            }

            pdfEditorGroup.style.display = '';

            if (pdfEditorContext) {
                pdfEditorContext.value = context;
            }

            if (pdfEditorTitle) {
                if (context === 'header') {
                    pdfEditorTitle.textContent = 'Modifier l\'en-tête';
                } else if (context === 'footer') {
                    pdfEditorTitle.textContent = 'Modifier le pied de page';
                }
            }

            if (typeof pdfEditorGroup.scrollIntoView === 'function') {
                try {
                    pdfEditorGroup.scrollIntoView({ behavior: 'smooth', block: 'start' });
                } catch (e) {
                    // ignore
                }
            }
        }

        if (openPdfHeaderBtn) {
            openPdfHeaderBtn.addEventListener('click', function () {
                gwOpenPdfEditor('header');
            });
        }

        if (openPdfFooterBtn) {
            openPdfFooterBtn.addEventListener('click', function () {
                gwOpenPdfEditor('footer');
            });
        }

        // Ouverture / fermeture de la zone 3.2 Mise en forme PDF
        var pdfLayoutToggle = document.getElementById('gw-toggle-pdf-layout');
        var pdfLayoutBody = document.getElementById('gw-pdf-layout-body');
        if (pdfLayoutToggle && pdfLayoutBody) {
            pdfLayoutToggle.addEventListener('click', function () {
                var current = pdfLayoutBody.style.display;
                if (!current || current === 'none') {
                    pdfLayoutBody.style.display = '';
                } else {
                    pdfLayoutBody.style.display = 'none';
                }
            });
        }

        // Affichage conditionnel des groupes avancés PDF (3.2 / 3.3)
        var pdfLayoutGroup = document.getElementById('gw-pdf-layout-group');
        var pdfHeaderFooterGroup = document.getElementById('gw-pdf-header-footer-group');
        var pdfActionsGroup = document.getElementById('gw-pdf-actions-group');
        var pdfCreateBtn = document.getElementById('gw_pdf_create_btn');
        var pdfCancelBtn = document.getElementById('gw_pdf_cancel_btn');
        var pdfModelNameInput = document.getElementById('gw_pdf_model_name');

        if (pdfCreateBtn && pdfLayoutGroup && pdfHeaderFooterGroup && pdfActionsGroup && pdfModelNameInput) {
            pdfCreateBtn.addEventListener('click', function () {
                var name = pdfModelNameInput.value ? pdfModelNameInput.value.trim() : '';
                if (name === '') {
                    window.alert(i18n.pdfModelNameRequired || 'Veuillez saisir un nom de modèle avant de continuer.');
                    pdfModelNameInput.focus();
                    return;
                }

                // Afficher les groupes de réglages avancés pour permettre de configurer le modèle
                pdfLayoutGroup.style.display = '';
                pdfHeaderFooterGroup.style.display = '';
                pdfActionsGroup.style.display = '';

                // Ouvrir par défaut le bloc 3.2 si l'utilisateur ne l'a jamais ouvert
                if (pdfLayoutBody && (!pdfLayoutBody.style.display || pdfLayoutBody.style.display === 'none')) {
                    pdfLayoutBody.style.display = '';
                }
            });
        }

        if (pdfCancelBtn && pdfLayoutGroup && pdfHeaderFooterGroup && pdfActionsGroup && pdfModelNameInput) {
            pdfCancelBtn.addEventListener('click', function () {
                // Réinitialiser simplement le nom et masquer les groupes avancés.
                pdfModelNameInput.value = '';
                pdfLayoutGroup.style.display = 'none';
                pdfHeaderFooterGroup.style.display = 'none';
                pdfActionsGroup.style.display = 'none';
            });
        }

        // Gestion de la suppression des modèles PDF
        var pdfDeleteButtons = document.querySelectorAll('.gw-pdf-delete-template');
        var pdfDeleteForm = document.getElementById('gw-pdf-delete-form');
        var pdfDeleteTemplateId = document.getElementById('gw_pdf_delete_template_id');
        if (pdfDeleteForm && pdfDeleteTemplateId && pdfDeleteButtons.length) {
            pdfDeleteButtons.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var templateId = btn.getAttribute('data-template-id');
                    var templateName = btn.getAttribute('data-template-name');
                    var pattern = i18n.pdfDeleteTemplateConfirm || 'Êtes-vous sûr de vouloir supprimer le modèle "%s" ?';
                    var message = pattern.replace('%s', templateName || '');
                    if (confirm(message)) {
                        pdfDeleteTemplateId.value = templateId;
                        pdfDeleteForm.submit();
                    }
                });
            });
        }

        // Gestion de la duplication des modèles PDF
        var pdfDuplicateButtons = document.querySelectorAll('.gw-pdf-duplicate-template');
        var pdfDuplicateForm = document.getElementById('gw-pdf-duplicate-form');
        var pdfDuplicateTemplateId = document.getElementById('gw_pdf_duplicate_template_id');
        var pdfDuplicateNameInput = document.getElementById('gw_pdf_duplicate_name');
        if (pdfDuplicateForm && pdfDuplicateTemplateId && pdfDuplicateNameInput && pdfDuplicateButtons.length) {
            pdfDuplicateButtons.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var templateId = btn.getAttribute('data-template-id');
                    var templateName = btn.getAttribute('data-template-name');
                    var defaultName = templateName ? (templateName + ' (copie)') : '';
                    var newName = window.prompt(i18n.pdfDuplicatePrompt || 'Nouveau nom pour le modèle dupliqué :', defaultName);
                    if (newName === null) {
                        return; // Annulé
                    }
                    newName = newName.trim();
                    if (newName === '') {
                        return; // On ne crée pas sans nom
                    }

                    pdfDuplicateTemplateId.value = templateId;
                    pdfDuplicateNameInput.value = newName;
                    pdfDuplicateForm.submit();
                });
            });
        }

        // Synchronisation du contenu TinyMCE avec les champs cachés header/footer
        var pdfHeaderHtmlInput = document.getElementById('gw_pdf_header_html');
        var pdfFooterHtmlInput = document.getElementById('gw_pdf_footer_html');
        var pdfTemplateForm = document.getElementById('gw-pdf-template-form');

        function gwSyncTinyMceToHiddenField() {
            var context = pdfEditorContext ? pdfEditorContext.value : 'header';
            var editorContent = '';

            // Récupérer le contenu de TinyMCE
            if (typeof tinymce !== 'undefined' && tinymce.get('gw_pdf_editor')) {
                editorContent = tinymce.get('gw_pdf_editor').getContent();
            } else {
                var textarea = document.getElementById('gw_pdf_editor');
                if (textarea) {
                    editorContent = textarea.value;
                }
            }

            // Stocker dans le bon champ caché
            if (context === 'header' && pdfHeaderHtmlInput) {
                pdfHeaderHtmlInput.value = editorContent;
            } else if (context === 'footer' && pdfFooterHtmlInput) {
                pdfFooterHtmlInput.value = editorContent;
            }
        }

        function gwLoadTinyMceFromHiddenField(context) {
            var content = '';
            if (context === 'header' && pdfHeaderHtmlInput) {
                content = pdfHeaderHtmlInput.value;
            } else if (context === 'footer' && pdfFooterHtmlInput) {
                content = pdfFooterHtmlInput.value;
            }

            // Charger dans TinyMCE
            if (typeof tinymce !== 'undefined' && tinymce.get('gw_pdf_editor')) {
                tinymce.get('gw_pdf_editor').setContent(content);
            } else {
                var textarea = document.getElementById('gw_pdf_editor');
                if (textarea) {
                    textarea.value = content;
                }
            }
        }

        // Modifier gwOpenPdfEditor pour synchroniser avant de changer de contexte
        var originalGwOpenPdfEditor = gwOpenPdfEditor;
        gwOpenPdfEditor = function (context) {
            // Sauvegarder le contenu actuel avant de changer de contexte
            gwSyncTinyMceToHiddenField();
            // Charger le nouveau contenu
            gwLoadTinyMceFromHiddenField(context);
            // Appeler la fonction originale
            originalGwOpenPdfEditor(context);
        };

        // Synchroniser avant la soumission du formulaire
        if (pdfTemplateForm) {
            pdfTemplateForm.addEventListener('submit', function () {
                gwSyncTinyMceToHiddenField();
            });
        }

        // Toggle des groupes de shortcodes (afficher/masquer)
        var shortcodeToggles = document.querySelectorAll('.gw-pdf-shortcodes-toggle');
        shortcodeToggles.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var groupKey = btn.getAttribute('data-group');
                var list = document.querySelector('.gw-pdf-shortcodes-list[data-group="' + groupKey + '"]');
                var icon = btn.querySelector('.dashicons');
                if (list) {
                    if (list.style.display === 'none' || list.style.display === '') {
                        list.style.display = 'block';
                        if (icon) {
                            icon.classList.remove('dashicons-arrow-down-alt2');
                            icon.classList.add('dashicons-arrow-up-alt2');
                        }
                    } else {
                        list.style.display = 'none';
                        if (icon) {
                            icon.classList.remove('dashicons-arrow-up-alt2');
                            icon.classList.add('dashicons-arrow-down-alt2');
                        }
                    }
                }
            });
        });

        // Insertion des shortcodes dans TinyMCE au clic
        var shortcodeInsertButtons = document.querySelectorAll('.gw-pdf-shortcode-insert');
        shortcodeInsertButtons.forEach(function (code) {
            code.style.cursor = 'pointer';
            code.addEventListener('click', function () {
                var shortcode = code.getAttribute('data-shortcode');
                if (!shortcode) return;

                // Insérer dans TinyMCE si disponible
                if (typeof tinymce !== 'undefined' && tinymce.get('gw_pdf_editor')) {
                    tinymce.get('gw_pdf_editor').insertContent(shortcode);
                    tinymce.get('gw_pdf_editor').focus();
                } else {
                    // Fallback sur le textarea
                    var textarea = document.getElementById('gw_pdf_editor');
                    if (textarea) {
                        var start = textarea.selectionStart;
                        var end = textarea.selectionEnd;
                        var text = textarea.value;
                        textarea.value = text.substring(0, start) + shortcode + text.substring(end);
                        textarea.selectionStart = textarea.selectionEnd = start + shortcode.length;
                        textarea.focus();
                    }
                }
            });
        });

        // Boutons pour basculer entre en-tête et pied de page
        var switchToFooterBtn = document.getElementById('gw-pdf-switch-to-footer');
        var switchToHeaderBtn = document.getElementById('gw-pdf-switch-to-header');

        if (switchToFooterBtn && switchToHeaderBtn) {
            switchToFooterBtn.addEventListener('click', function () {
                gwOpenPdfEditor('footer');
                switchToFooterBtn.style.display = 'none';
                switchToHeaderBtn.style.display = 'inline-flex';
            });

            switchToHeaderBtn.addEventListener('click', function () {
                gwOpenPdfEditor('header');
                switchToHeaderBtn.style.display = 'none';
                switchToFooterBtn.style.display = 'inline-flex';
            });
        }

        // Synchronisation des color pickers avec les champs texte
        var colorInputs = document.querySelectorAll('input[type="color"]');
        colorInputs.forEach(function (colorInput) {
            var textInput = document.querySelector('input[data-color-target="' + colorInput.id + '"]');
            if (textInput) {
                // Quand le color picker change, mettre à jour le champ texte
                colorInput.addEventListener('input', function () {
                    textInput.value = colorInput.value;
                });

                // Quand le champ texte change, mettre à jour le color picker
                textInput.addEventListener('input', function () {
                    var val = textInput.value.trim();
                    // Valider le format hex
                    if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
                        colorInput.value = val;
                    }
                });

                // Au blur, corriger le format si nécessaire
                textInput.addEventListener('blur', function () {
                    var val = textInput.value.trim();
                    if (!/^#[0-9A-Fa-f]{6}$/.test(val)) {
                        textInput.value = colorInput.value;
                    }
                });
            }
        });

        // Synchronisation des color pickers pour les fonds (supportent "transparent")
        var bgColorPickers = document.querySelectorAll('input[data-bg-target]');
        bgColorPickers.forEach(function (picker) {
            var targetId = picker.getAttribute('data-bg-target');
            var textInput = document.getElementById(targetId);
            if (textInput) {
                // Quand le color picker change, mettre à jour le champ texte
                picker.addEventListener('input', function () {
                    textInput.value = picker.value;
                });

                // Quand le champ texte change, mettre à jour le color picker si c'est un hex valide
                textInput.addEventListener('input', function () {
                    var val = textInput.value.trim();
                    if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
                        picker.value = val;
                    }
                    // Si "transparent" ou vide, on laisse tel quel
                });
            }
        });

        // Icônes d'aperçu PDF par modèle dans la liste "Modèles existants"
        var pdfPreviewButtons = document.querySelectorAll('.gw-pdf-preview-template');
        if (pdfPreviewButtons && pdfPreviewButtons.length) {
            pdfPreviewButtons.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var templateId = btn.getAttribute('data-template-id') || '0';
                    if (templateId === '0' || templateId === '') {
                        return;
                    }

                    var base = settings.pdfPreviewBaseUrl || '';
                    var previewUrl = base + '?template_id=' + templateId;
                    window.open(previewUrl, '_blank', 'width=900,height=700,scrollbars=yes,resizable=yes');
                });
            });
        }

        // Validation du formulaire d'identité au moment de la soumission
        var identityModal = document.getElementById('gw-modal-general');
        if (identityModal) {
            var identityForm = identityModal.querySelector('form');
            var identityError = document.getElementById('gw_identity_error');
            if (identityForm && identityError) {
                identityForm.addEventListener('submit', function (e) {
                    // Champs strictement obligatoires (non vides)
                    var requiredFieldIds = [
                        'gw_raison_sociale',
                        'gw_email_contact',
                        'gw_adresse',
                        'gw_code_postal',
                        'gw_ville',
                        'gw_siret',
                        'gw_code_ape',
                        'gw_nda',
                        'gw_format_numero_devis'
                    ];

                    var requiredFields = requiredFieldIds
                        .map(function (id) { return document.getElementById(id); })
                        .filter(function (el) { return !!el; });

                    var missingRequired = requiredFields.some(function (field) {
                        return field.value.trim() === '';
                    });

                    var telFixeField = document.getElementById('gw_telephone_fixe');
                    var telPortableField = document.getElementById('gw_telephone_portable');
                    var hasAtLeastOnePhone = false;
                    if (telFixeField && telFixeField.value.trim() !== '') {
                        hasAtLeastOnePhone = true;
                    }
                    if (telPortableField && telPortableField.value.trim() !== '') {
                        hasAtLeastOnePhone = true;
                    }

                    if (missingRequired || !hasAtLeastOnePhone) {
                        e.preventDefault();

                        if (missingRequired && !hasAtLeastOnePhone) {
                            identityError.textContent = 'Merci de renseigner tous les champs obligatoires marqués d’une astérisque rouge (*) et au moins un numéro de téléphone (fixe ou portable).';
                        } else if (missingRequired) {
                            identityError.textContent = 'Merci de renseigner tous les champs obligatoires marqués d’une astérisque rouge (*).';
                        } else {
                            identityError.textContent = 'Merci de renseigner au moins un numéro de téléphone (fixe ou portable).';
                        }

                        identityError.style.display = '';
                        identityError.focus && identityError.focus();
                    } else {
                        // Tout est OK, on masque le message d'erreur éventuel et on laisse le formulaire se soumettre
                        identityError.textContent = '';
                        identityError.style.display = 'none';
                    }
                });
            }
        }

        // Ouverture de la section d'édition de la description (présentation OF)
        var openDescBtn = document.getElementById('gw-open-description-editor');
        var descSection = document.getElementById('gw-settings-group-description-editor');
        if (openDescBtn && descSection) {
            openDescBtn.addEventListener('click', function () {
                descSection.style.display = '';
                if (typeof descSection.scrollIntoView === 'function') {
                    descSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        }

        // Sélection du logo GestiWork via la médiathèque WordPress
        var logoSelectButton = document.getElementById('gw-logo-select-button');
        var logoIdInput = document.getElementById('gw_logo_id');
        var logoPreview = document.getElementById('gw-logo-preview');
        if (logoSelectButton && logoIdInput && logoPreview) {
            var gwLogoFrame = null;

            logoSelectButton.addEventListener('click', function (e) {
                e.preventDefault();

                // Vérifier la disponibilité de wp.media au moment du clic
                if (typeof wp === 'undefined' || !wp.media || typeof wp.media !== 'function') {
                    return;
                }

                if (gwLogoFrame) {
                    gwLogoFrame.open();
                    return;
                }

                gwLogoFrame = wp.media({
                    title: 'Choisir un logo GestiWork',
                    button: { text: 'Utiliser ce logo' },
                    multiple: false
                });

                gwLogoFrame.on('select', function () {
                    var attachment = gwLogoFrame.state().get('selection').first();
                    if (!attachment) {
                        return;
                    }

                    var data = attachment.toJSON();
                    logoIdInput.value = data.id || 0;

                    if (data.url) {
                        logoPreview.src = data.url;
                        logoPreview.style.display = 'inline-block';
                    }

                    // Soumettre automatiquement le formulaire pour enregistrer le logo
                    var form = logoSelectButton.closest('form');
                    if (form && typeof form.submit === 'function') {
                        form.submit();
                    }
                });

                gwLogoFrame.open();
            });
        }
    });
})();
