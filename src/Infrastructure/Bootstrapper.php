<?php
/**
 * GestiWork ERP - Bootstrapper
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

namespace GestiWork\Infrastructure;

class Bootstrapper
{
    public static function init(): void
    {
        // Hook principal appelé une fois que tous les plugins sont chargés
        add_action('plugins_loaded', [self::class, 'onPluginsLoaded']);
    }

    public static function onPluginsLoaded(): void
    {
        // Chargement de la traduction
        load_plugin_textdomain(
            'gestiwork',
            false,
            dirname(plugin_basename(GW_PLUGIN_DIR . 'gestiwork.php')) . '/languages'
        );

        // TODO : initialiser ici les couches Domain / Infrastructure / UI
        // Exemple : Router interne, enregistrement des endpoints, chargement des assets, etc.
        if (class_exists(\GestiWork\UI\Router\GestiWorkRouter::class)) {
            \GestiWork\UI\Router\GestiWorkRouter::register();
        }

        if (class_exists(\GestiWork\UI\Admin\AdminMenu::class)) {
            \GestiWork\UI\Admin\AdminMenu::register();
        }

        if (class_exists(\GestiWork\UI\Controller\AssetsLoader::class)) {
            \GestiWork\UI\Controller\AssetsLoader::register();
        }

        if (class_exists(\GestiWork\UI\Controller\SettingsController::class)) {
            \GestiWork\UI\Controller\SettingsController::register();
        }

        if (class_exists(\GestiWork\UI\Controller\TiersController::class)) {
            \GestiWork\UI\Controller\TiersController::register();
        }

        if (class_exists(\GestiWork\UI\Controller\ApprenantController::class)) {
            \GestiWork\UI\Controller\ApprenantController::register();
        }

        if (class_exists(\GestiWork\UI\Controller\ResponsableFormateurController::class)) {
            \GestiWork\UI\Controller\ResponsableFormateurController::register();
        }

    }

    public static function onActivation(): void
    {
        if (class_exists(\GestiWork\Infrastructure\Database\Installer::class)) {
            \GestiWork\Infrastructure\Database\Installer::install();
        }

        if (class_exists(\GestiWork\UI\Router\GestiWorkRouter::class)) {
            \GestiWork\UI\Router\GestiWorkRouter::registerRewriteRules();
        }

        flush_rewrite_rules();
    }
}
