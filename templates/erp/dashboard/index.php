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

if ($is_admin && isset($_GET['gw_view']) && $_GET['gw_view'] === 'settings') {
    $active_view = 'settings';
}

$dashboard_url = home_url('/gestiwork/');
$settings_url = $is_admin ? add_query_arg('gw_view', 'settings', $dashboard_url) : $dashboard_url;

$nav_items = [
    [
        'label' => __('Dashboard', 'gestiwork'),
        'url' => $dashboard_url,
        'active' => $active_view === 'dashboard',
    ],
];

if ($is_admin) {
    $nav_items[] = [
        'label' => __('Paramètres', 'gestiwork'),
        'url' => $settings_url,
        'active' => $active_view === 'settings',
    ];
}

$layout_mode = $is_admin ? 'gw-layout--with-nav' : 'gw-layout--full';
$content_template = $active_view === 'settings' && $is_admin
    ? GW_PLUGIN_DIR . 'templates/erp/settings/view-settings.php'
    : GW_PLUGIN_DIR . 'templates/erp/dashboard/view-dashboard.php';

require GW_PLUGIN_DIR . 'templates/layouts/erp-shell.php';
