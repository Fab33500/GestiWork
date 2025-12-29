<?php
/**
 * GestiWork ERP - Internal dashboard entry point (site dans le site)
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

$is_admin = current_user_can('manage_options');
$active_view = 'dashboard';

$gw_view = get_query_var('gw_view');
if ($gw_view === '' && isset($_GET['gw_view'])) {
    $gw_view = (string) $_GET['gw_view'];
}

$gw_view_normalized = strtolower(trim((string) $gw_view));

if ($gw_view_normalized === 'aide') {
    $active_view = 'aide';
} elseif ($is_admin && $gw_view_normalized === 'settings') {
    $active_view = 'settings';
} elseif ($is_admin && $gw_view_normalized === 'tiers') {
    $active_view = 'tiers';
} elseif ($is_admin && $gw_view_normalized === 'client') {
    $active_view = 'client';
} elseif ($is_admin && $gw_view_normalized === 'apprenants') {
    $active_view = 'apprenants';
} elseif ($is_admin && $gw_view_normalized === 'equipe-pedagogique') {
    $active_view = 'equipe_pedagogique';
} elseif ($is_admin && $gw_view_normalized === 'apprenant') {
    $active_view = 'apprenant';
} elseif ($is_admin && $gw_view_normalized === 'responsable') {
    $active_view = 'responsable';
} elseif ($is_admin && $gw_view_normalized === 'catalogue') {
    $active_view = 'catalogue';
} elseif ($is_admin && $gw_view_normalized === 'questionnaires') {
    $active_view = 'questionnaires';
} elseif ($is_admin && $gw_view_normalized === 'enquetes') {
    $active_view = 'enquetes';
} elseif ($is_admin && $gw_view_normalized === 'sessions') {
    $active_view = 'sessions';
} elseif ($is_admin && $gw_view_normalized === 'planing') {
    $active_view = 'planing';
} elseif ($is_admin && $gw_view_normalized === 'lieux') {
    $active_view = 'lieux';
} elseif ($is_admin && $gw_view_normalized === 'rapports') {
    $active_view = 'rapports';
} elseif ($is_admin && $gw_view_normalized === 'bpf') {
    $active_view = 'bpf';
} elseif ($is_admin && $gw_view_normalized === 'veille') {
    $active_view = 'veille';
} elseif ($is_admin && $gw_view_normalized === 'ged') {
    $active_view = 'ged';
} elseif ($is_admin && $gw_view_normalized === 'systeme') {
    $active_view = 'systeme';
}

$dashboard_url = home_url('/gestiwork/');
$settings_url  = $is_admin ? home_url('/gestiwork/settings/general/') : $dashboard_url;
$help_url      = home_url('/gestiwork/aide/');
$tiers_url     = $is_admin ? home_url('/gestiwork/Tiers/') : $dashboard_url;
$apprenants_url = $is_admin ? home_url('/gestiwork/apprenants/') : $dashboard_url;
$equipe_pedagogique_url = $is_admin ? home_url('/gestiwork/equipe-pedagogique/') : $dashboard_url;
$catalogue_url = $is_admin ? home_url('/gestiwork/catalogue/') : $dashboard_url;
$questionnaires_url = $is_admin ? home_url('/gestiwork/questionnaires/') : $dashboard_url;
$enquetes_url = $is_admin ? home_url('/gestiwork/enquetes/') : $dashboard_url;
$sessions_url = $is_admin ? home_url('/gestiwork/sessions/') : $dashboard_url;
$planing_url = $is_admin ? home_url('/gestiwork/planing/') : $dashboard_url;
$lieux_url = $is_admin ? home_url('/gestiwork/lieux/') : $dashboard_url;
$rapports_url = $is_admin ? home_url('/gestiwork/rapports/') : $dashboard_url;
$bpf_url = $is_admin ? home_url('/gestiwork/bpf/') : $dashboard_url;
$veille_url = $is_admin ? home_url('/gestiwork/veille/') : $dashboard_url;
$ged_url = $is_admin ? home_url('/gestiwork/ged/') : $dashboard_url;
$systeme_url = $is_admin ? home_url('/gestiwork/systeme/') : $dashboard_url;

$nav_items = [
    [
        'type'  => 'link',
        'label' => '🏠 ' . __('Dashboard', 'gestiwork'),
        'url'   => $dashboard_url,
        'active'=> $active_view === 'dashboard',
    ],
];

$utilitaires_items = [
    [
        'label' => '❓ ' . __('Aide', 'gestiwork'),
        'url'   => $help_url,
        'active'=> $active_view === 'aide',
    ],
];

if ($is_admin) {
    $nav_items[] = [
        'type'  => 'group',
        'label' => __('Gestion des contacts', 'gestiwork'),
        'items' => [
            [
                'label' => '👤 ' . __('Tiers', 'gestiwork'),
                'url'   => $tiers_url,
                'active'=> $active_view === 'tiers',
            ],
            [
                'label' => '🧑‍🎓 ' . __('Stagiaires', 'gestiwork'),
                'url'   => $apprenants_url,
                'active'=> $active_view === 'apprenants',
            ],
            [
                'label' => '🧑‍🏫 ' . __('Équipe pédagogique', 'gestiwork'),
                'url'   => $equipe_pedagogique_url,
                'active'=> $active_view === 'equipe_pedagogique',
            ],
        ],
    ];

    $nav_items[] = [
        'type'  => 'group',
        'label' => __('Gestion des sessions', 'gestiwork'),
        'items' => [
            [
                'label' => '🎓 ' . __('Sessions', 'gestiwork'),
                'url'   => $sessions_url,
                'active'=> $active_view === 'sessions',
            ],
            [
                'label' => '📅 ' . __('Planning', 'gestiwork'),
                'url'   => $planing_url,
                'active'=> $active_view === 'planing',
            ],
            [
                'label' => '📍 ' . __('Lieux', 'gestiwork'),
                'url'   => $lieux_url,
                'active'=> $active_view === 'lieux',
            ],
        ],
    ];

    $nav_items[] = [
        'type'  => 'group',
        'label' => __('Parcours & programmes', 'gestiwork'),
        'items' => [
            [
                'label' => '🗂️ ' . __('Catalogue Formations', 'gestiwork'),
                'url'   => $catalogue_url,
                'active'=> $active_view === 'catalogue',
            ],
        ],
    ];

    $nav_items[] = [
        'type'  => 'group',
        'label' => __('Suivi opérationnel', 'gestiwork'),
        'items' => [
            [
                'label' => '📝 ' . __('Questionnaires', 'gestiwork'),
                'url'   => $questionnaires_url,
                'active'=> $active_view === 'questionnaires',
            ],
            [
                'label' => '📣 ' . __('Enquêtes satisfactions', 'gestiwork'),
                'url'   => $enquetes_url,
                'active'=> $active_view === 'enquetes',
            ],
            [
                'label' => '📈 ' . __('Rapports', 'gestiwork'),
                'url'   => $rapports_url,
                'active'=> $active_view === 'rapports',
            ],
            [
                'label' => '🧾 ' . __('BPF', 'gestiwork'),
                'url'   => $bpf_url,
                'active'=> $active_view === 'bpf',
            ],
            [
                'label' => '🔎 ' . __('Veille', 'gestiwork'),
                'url'   => $veille_url,
                'active'=> $active_view === 'veille',
            ],
        ],
    ];

    $utilitaires_items[] = [
        'label' => '⚙️ ' . __('Paramètres', 'gestiwork'),
        'url'   => $settings_url,
        'active'=> $active_view === 'settings',
    ];
    $utilitaires_items[] = [
        'label' => '🖥️ ' . __('Système', 'gestiwork'),
        'url'   => $systeme_url,
        'active'=> $active_view === 'systeme',
    ];
    $utilitaires_items[] = [
        'label' => '📁 ' . __('GED', 'gestiwork'),
        'url'   => $ged_url,
        'active'=> $active_view === 'ged',
    ];
}

$nav_items[] = [
    'type'  => 'group',
    'label' => __('Utilitaires', 'gestiwork'),
    'items' => $utilitaires_items,
];

$layout_mode = $is_admin ? 'gw-layout--with-nav' : 'gw-layout--full';

if ($active_view === 'settings' && $is_admin) {
    $content_template = GW_PLUGIN_DIR . 'templates/erp/settings/view-settings.php';
} elseif ($active_view === 'aide') {
    $content_template = GW_PLUGIN_DIR . 'templates/erp/aide/view-aide.php';
} elseif ($active_view === 'tiers' && $is_admin) {
    $content_template = GW_PLUGIN_DIR . 'templates/erp/tiers/view-tiers.php';
} elseif ($active_view === 'client' && $is_admin) {
    $content_template = GW_PLUGIN_DIR . 'templates/erp/tiers/view-client.php';
} elseif ($active_view === 'apprenants' && $is_admin) {
    $content_template = GW_PLUGIN_DIR . 'templates/erp/apprenants/view-apprenants.php';
} elseif ($active_view === 'equipe_pedagogique' && $is_admin) {
    $content_template = GW_PLUGIN_DIR . 'templates/erp/equipe-pedagogique/view-equipe-pedagogique.php';
} elseif ($active_view === 'apprenant' && $is_admin) {
    $content_template = GW_PLUGIN_DIR . 'templates/erp/apprenants/view-apprenant.php';
} elseif ($active_view === 'responsable' && $is_admin) {
    $content_template = GW_PLUGIN_DIR . 'templates/erp/equipe-pedagogique/view-responsable.php';
} elseif ($active_view === 'catalogue' && $is_admin) {
    $content_template = GW_PLUGIN_DIR . 'templates/erp/academic/view-catalogue.php';
} elseif ($active_view === 'questionnaires' && $is_admin) {
    $content_template = GW_PLUGIN_DIR . 'templates/erp/qualite/view-questionnaires.php';
} elseif ($active_view === 'enquetes' && $is_admin) {
    $content_template = GW_PLUGIN_DIR . 'templates/erp/qualite/view-enquetes.php';
} elseif ($active_view === 'sessions' && $is_admin) {
    $content_template = GW_PLUGIN_DIR . 'templates/erp/academic/view-sessions.php';
} elseif ($active_view === 'planing' && $is_admin) {
    $content_template = GW_PLUGIN_DIR . 'templates/erp/academic/view-planing.php';
} elseif ($active_view === 'lieux' && $is_admin) {
    $content_template = GW_PLUGIN_DIR . 'templates/erp/academic/view-lieux.php';
} elseif ($active_view === 'rapports' && $is_admin) {
    $content_template = GW_PLUGIN_DIR . 'templates/erp/suivi-activite/view-rapports.php';
} elseif ($active_view === 'bpf' && $is_admin) {
    $content_template = GW_PLUGIN_DIR . 'templates/erp/suivi-activite/view-bpf.php';
} elseif ($active_view === 'veille' && $is_admin) {
    $content_template = GW_PLUGIN_DIR . 'templates/erp/suivi-activite/view-veille.php';
} elseif ($active_view === 'ged' && $is_admin) {
    $content_template = GW_PLUGIN_DIR . 'templates/erp/suivi-activite/view-ged.php';
} elseif ($active_view === 'systeme' && $is_admin) {
    $content_template = GW_PLUGIN_DIR . 'templates/erp/suivi-activite/view-systeme.php';
} else {
    $content_template = GW_PLUGIN_DIR . 'templates/erp/dashboard/view-dashboard.php';
}

require GW_PLUGIN_DIR . 'templates/layouts/erp-shell.php';
