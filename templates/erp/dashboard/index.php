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
}

$dashboard_url = home_url('/gestiwork/');
$settings_url  = $is_admin ? home_url('/gestiwork/settings/general/') : $dashboard_url;
$help_url      = home_url('/gestiwork/aide/');
$tiers_url     = $is_admin ? home_url('/gestiwork/Tiers/') : $dashboard_url;
$apprenants_url = $is_admin ? home_url('/gestiwork/apprenants/') : $dashboard_url;
$equipe_pedagogique_url = $is_admin ? home_url('/gestiwork/equipe-pedagogique/') : $dashboard_url;

$nav_items = [
    [
        'label' => __('Dashboard', 'gestiwork'),
        'url'   => $dashboard_url,
        'active'=> $active_view === 'dashboard',
    ],
    [
        'label' => __('Aide', 'gestiwork'),
        'url'   => $help_url,
        'active'=> $active_view === 'aide',
    ],
];

if ($is_admin) {
    $nav_items[] = [
        'label' => __('Paramètres', 'gestiwork'),
        'url'   => $settings_url,
        'active'=> $active_view === 'settings',
    ];

    $nav_items[] = [
        'label' => __('Tiers', 'gestiwork'),
        'url'   => $tiers_url,
        'active'=> $active_view === 'tiers',
    ];

    $nav_items[] = [
        'label' => __('Stagiaires', 'gestiwork'),
        'url'   => $apprenants_url,
        'active'=> $active_view === 'apprenants',
    ];

    $nav_items[] = [
        'label' => __('Équipe pédagogique', 'gestiwork'),
        'url'   => $equipe_pedagogique_url,
        'active'=> $active_view === 'equipe_pedagogique',
    ];
}

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
} else {
    $content_template = GW_PLUGIN_DIR . 'templates/erp/dashboard/view-dashboard.php';
}

require GW_PLUGIN_DIR . 'templates/layouts/erp-shell.php';
