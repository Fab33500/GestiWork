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
        
        $viewsWithForms = ['settings', 'client'];

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
                return $view;
            default:
                return 'dashboard';
        }
    }
}
