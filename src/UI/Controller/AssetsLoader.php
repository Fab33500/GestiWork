<?php
/**
 * GestiWork ERP - Assets Loader
 *
 * This file is part of GestiWork ERP.
 *
 * GestiWork ERP is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * GestiWork ERP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GestiWork ERP. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace GestiWork\UI\Controller;

use GestiWork\Domain\Validation\FormValidationCatalog;
use GestiWork\Domain\Tiers\LegalFormCatalog;
use GestiWork\UI\Router\GestiWorkRouter;

class AssetsLoader
{
    public static function register(): void
    {
        add_action('wp_enqueue_scripts', [self::class, 'enqueueFrontendAssets']);
    }

    public static function enqueueFrontendAssets(): void
    {
        if (get_query_var(GestiWorkRouter::QUERY_VAR) !== '1') {
            return;
        }

        // Assets globaux chargés sur toutes les vues GestiWork
        wp_enqueue_style('dashicons');

        wp_enqueue_style(
            'gestiwork-variables',
            GW_PLUGIN_URL . 'assets/css/gw-variables.css',
            [],
            GW_VERSION
        );

        wp_enqueue_style(
            'gestiwork-layout',
            GW_PLUGIN_URL . 'assets/css/gw-layout.css',
            ['gestiwork-variables'],
            GW_VERSION
        );

        wp_enqueue_script(
            'gestiwork-nav',
            GW_PLUGIN_URL . 'assets/js/gw-nav.js',
            [],
            GW_VERSION,
            true
        );

        wp_enqueue_script(
            'gestiwork-ui',
            GW_PLUGIN_URL . 'assets/js/gw-ui.js',
            [],
            GW_VERSION,
            true
        );

        // Assets conditionnels selon la vue active
        $currentView = self::getCurrentView();
        
        $viewsWithForms = ['settings', 'client', 'apprenant', 'responsable', 'lieux'];

        // Assets spécifiques à la vue Aide
        if ($currentView === 'aide') {
            wp_enqueue_style(
                'gestiwork-aide',
                GW_PLUGIN_URL . 'assets/css/gw-aide.css',
                ['gestiwork-layout'],
                GW_VERSION
            );

            wp_enqueue_script(
                'gestiwork-aide',
                GW_PLUGIN_URL . 'assets/js/gw-aide.js',
                [],
                GW_VERSION,
                true
            );
        }

        // gw-form-utils nécessaire sur Settings et Client (formulaires avec formatage)
        if (in_array($currentView, $viewsWithForms, true)) {
            wp_enqueue_script(
                'gestiwork-form-utils',
                GW_PLUGIN_URL . 'assets/js/gw-form-utils.js',
                [],
                GW_VERSION,
                true
            );

            wp_enqueue_script(
                'gestiwork-form-validation',
                GW_PLUGIN_URL . 'assets/js/gw-form-validation.js',
                ['gestiwork-form-utils'],
                GW_VERSION,
                true
            );

            wp_localize_script('gestiwork-form-validation', 'GWFormValidation', [
                'i18n' => [
                    'requiredOnly' => __('Merci de renseigner tous les champs obligatoires marqués d’une astérisque rouge (*).', 'gestiwork'),
                    'groupOnly' => __('Merci de renseigner les informations requises.', 'gestiwork'),
                    'requiredAndGroup' => __('Merci de renseigner tous les champs obligatoires marqués d’une astérisque rouge (*) et les informations requises.', 'gestiwork'),
                ],
                'rules' => FormValidationCatalog::rules(),
            ]);
        }

        if ($currentView === 'client') {
            wp_enqueue_script(
                'gestiwork-tiers-client',
                GW_PLUGIN_URL . 'assets/js/pages/gw-tiers-client.js',
                ['gestiwork-ui'],
                GW_VERSION,
                true
            );

            wp_localize_script('gestiwork-tiers-client', 'GWTiersClient', [
                'i18n' => [
                    'confirmDeleteContact' => __('Supprimer ce contact ?', 'gestiwork'),
                    'confirmDeleteTier' => __('Supprimer définitivement ce client et tous ses contacts ?', 'gestiwork'),
                ],
            ]);
        }

        if ($currentView === 'apprenant') {
            wp_enqueue_script(
                'gestiwork-apprenant',
                GW_PLUGIN_URL . 'assets/js/pages/gw-apprenant.js',
                ['gestiwork-ui'],
                GW_VERSION,
                true
            );

            wp_localize_script('gestiwork-apprenant', 'GWApprenant', [
                'i18n' => [
                    'confirmDeleteApprenant' => __('Supprimer définitivement cet apprenant ?', 'gestiwork'),
                ],
            ]);
        }

        if ($currentView === 'responsable') {
            wp_enqueue_script(
                'gestiwork-responsable',
                GW_PLUGIN_URL . 'assets/js/pages/gw-responsable.js',
                ['gestiwork-ui'],
                GW_VERSION,
                true
            );

            wp_localize_script('gestiwork-responsable', 'GWResponsable', [
                'i18n' => [
                    'confirmDeleteFormateur' => __('Supprimer définitivement ce formateur / responsable pédagogique ?', 'gestiwork'),
                ],
            ]);
        }

        if ($currentView === 'settings') {
            wp_enqueue_media();

            wp_enqueue_script(
                'gestiwork-settings',
                GW_PLUGIN_URL . 'assets/js/pages/gw-settings.js',
                ['gestiwork-ui', 'gestiwork-form-utils'],
                GW_VERSION,
                true
            );

            wp_localize_script('gestiwork-settings', 'GWSettings', [
                'pdfPreviewBaseUrl' => home_url('/gestiwork/pdf-preview/'),
                'i18n' => [
                    'pdfModelNameRequired' => __('Veuillez saisir un nom de modèle avant de continuer.', 'gestiwork'),
                    'pdfDeleteTemplateConfirm' => __('Êtes-vous sûr de vouloir supprimer le modèle "%s" ?', 'gestiwork'),
                    'pdfDuplicatePrompt' => __('Nouveau nom pour le modèle dupliqué :', 'gestiwork'),
                ],
            ]);
        }

        if (in_array($currentView, $viewsWithForms, true)) {
            wp_enqueue_script(
                'gestiwork-geo-cp-ville',
                GW_PLUGIN_URL . 'assets/js/gw-geo-cp-ville.js',
                ['gestiwork-ui'],
                GW_VERSION,
                true
            );

            $geoContexts = [
                'tier_create' => [
                    'cp' => '#gw_tier_create_cp',
                    'ville' => '#gw_tier_create_ville',
                ],
                'tier_view' => [
                    'cp' => '#gw_tier_view_cp',
                    'ville' => '#gw_tier_view_ville',
                ],
                'settings_identity' => [
                    'cp' => '#gw_code_postal',
                    'ville' => '#gw_ville',
                ],
                'apprenant_create' => [
                    'cp' => '#gw_apprenant_cp',
                    'ville' => '#gw_apprenant_ville',
                ],
                'responsable_create' => [
                    'cp' => '#gw_responsable_code_postal',
                    'ville' => '#gw_responsable_ville',
                ],
                'lieu_create' => [
                    'cp' => '#gw_lieu_code_postal',
                    'ville' => '#gw_lieu_ville',
                ],
            ];

            wp_localize_script('gestiwork-geo-cp-ville', 'GWGeoCpVille', [
                'apiUrl' => 'https://geo.api.gouv.fr/communes',
                'i18n' => [
                    'chooseCity' => __('Choisir une ville', 'gestiwork'),
                ],
                'contexts' => $geoContexts,
            ]);

            wp_enqueue_script(
                'gestiwork-insee-search',
                GW_PLUGIN_URL . 'assets/js/gw-insee-search.js',
                ['gestiwork-ui', 'gestiwork-form-utils'],
                GW_VERSION,
                true
            );

            $contexts = [
                'tier_create' => [
                    'fields' => [
                        'raison_sociale' => '#gw_tier_create_raison_sociale',
                        'siret' => '#gw_tier_create_siret',
                        'adresse1' => '#gw_tier_create_adresse1',
                        'adresse2' => '#gw_tier_create_adresse2',
                        'cp' => '#gw_tier_create_cp',
                        'ville' => '#gw_tier_create_ville',
                        'forme_juridique' => '#gw_tier_create_forme_juridique',
                    ],
                ],
                'settings_identity' => [
                    'fields' => [
                        'raison_sociale' => '#gw_raison_sociale',
                        'siret' => '#gw_siret',
                        'adresse' => '#gw_adresse',
                        'cp' => '#gw_code_postal',
                        'ville' => '#gw_ville',
                        'forme_juridique' => '#gw_forme_juridique',
                        'code_ape' => '#gw_code_ape',
                    ],
                ],
            ];

            wp_localize_script('gestiwork-insee-search', 'GWInseeLookup', [
                'apiUrl' => 'https://recherche-entreprises.api.gouv.fr/search',
                'perPage' => 25,
                'i18n' => [
                    'emptyTerm' => __('Veuillez saisir un SIRET ou une raison sociale.', 'gestiwork'),
                    'minChars' => __('Veuillez saisir au moins 3 caractères.', 'gestiwork'),
                    'loading' => __('Recherche en cours…', 'gestiwork'),
                    'noResults' => __('Aucun établissement trouvé.', 'gestiwork'),
                    'resultsFor' => __('Résultats pour “%s”.', 'gestiwork'),
                    'error' => __('La recherche a échoué : ', 'gestiwork'),
                    'inserted' => __('Les informations ont été préremplies.', 'gestiwork'),
                    'selectLabel' => __('Insérer ces informations', 'gestiwork'),
                    'activityLabel' => __('Activité principale', 'gestiwork'),
                    'legalLabel' => __('Forme juridique', 'gestiwork'),
                    'initialHint' => __('Lancez une recherche pour afficher les résultats.', 'gestiwork'),
                    'contextMissing' => __('Aucun contexte actif pour l’insertion.', 'gestiwork'),
                ],
                'contexts' => $contexts,
                'legalForms' => LegalFormCatalog::labels(),
            ]);
        }

        if ($currentView === 'planing') {
            wp_enqueue_style(
                'gestiwork-planing-css',
                GW_PLUGIN_URL . 'assets/css/gw-planing.css',
                ['gestiwork-layout'],
                GW_VERSION
            );

            wp_enqueue_style(
                'gestiwork-fullcalendar',
                'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css',
                [],
                '6.1.10'
            );

            wp_enqueue_script(
                'gestiwork-fullcalendar',
                'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js',
                [],
                '6.1.10',
                true
            );

            wp_enqueue_script(
                'gestiwork-planing',
                GW_PLUGIN_URL . 'assets/js/pages/gw-planing.js',
                ['gestiwork-fullcalendar', 'gestiwork-ui'],
                GW_VERSION,
                true
            );
        }

        if ($currentView === 'lieux') {
            wp_enqueue_script(
                'gestiwork-lieux',
                GW_PLUGIN_URL . 'assets/js/pages/gw-lieux.js',
                ['gestiwork-ui'],
                GW_VERSION,
                true
            );
        }
    }

    private static function getCurrentView(): string
    {
        $viewVar = get_query_var('gw_view');
        if (is_string($viewVar) && $viewVar !== '') {
            $view = strtolower(trim($viewVar));
        } else {
            $view = isset($_GET['gw_view']) ? strtolower(trim((string) $_GET['gw_view'])) : '';
        }
        
        // Normaliser les vues connues
        switch ($view) {
            case 'settings':
            case 'client':
            case 'tiers':
            case 'aide':
            case 'apprenants':
            case 'apprenant':
            case 'equipe-pedagogique':
            case 'responsable':
            case 'planing':
            case 'sessions':
            case 'catalogue':
            case 'questionnaires':
            case 'enquetes':
            case 'rapports':
            case 'bpf':
            case 'veille':
            case 'ged':
            case 'systeme':
            case 'lieux':
                return $view;
            default:
                return 'dashboard';
        }
    }
}
