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

        // Icônes WordPress (Dashicons) pour le bouton de menu mobile
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
            'gestiwork-form-utils',
            GW_PLUGIN_URL . 'assets/js/gw-form-utils.js',
            [],
            GW_VERSION,
            true
        );
    }
}
