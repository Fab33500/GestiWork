<?php
/**
 * Plugin Name: GestiWork ERP
 * Description: ERP pour Organismes de Formation (OF) sur WordPress : gestion académique, commerciale, administrative et Qualiopi/BPF.
 * Plugin URI: https://example.com/gestiwork
 * Version: 0.4.0
 * Author: LAURET Fabrice
 * Author URI: https://example.com
 * Text Domain: gestiwork
 * Domain Path: /languages
 * License: GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
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

// Bloquer l'accès direct au fichier
if (! defined('ABSPATH')) {
    exit;
}

// Garde-fou version PHP (minimum 8.0)
if (version_compare(PHP_VERSION, '8.0', '<')) {
    if (is_admin()) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error"><p>';
            echo esc_html__('Le plugin GestiWork nécessite PHP 8.0 ou supérieur.', 'gestiwork');
            echo '</p></div>';
        });
    }

    return;
}

// Constantes de base du plugin
if (! defined('GW_PLUGIN_DIR')) {
    define('GW_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

if (! defined('GW_PLUGIN_URL')) {
    define('GW_PLUGIN_URL', plugin_dir_url(__FILE__));
}

if (! defined('GW_VERSION')) {
    define('GW_VERSION', '0.4.0');
}

// Chargement de l'autoloader Composer si disponible
$gestiworkAutoload = GW_PLUGIN_DIR . 'vendor/autoload.php';
if (file_exists($gestiworkAutoload)) {
    require_once $gestiworkAutoload;
}

// Initialisation du plugin via le Bootstrapper
if (class_exists(\GestiWork\Infrastructure\Bootstrapper::class)) {
    register_activation_hook(__FILE__, [\GestiWork\Infrastructure\Bootstrapper::class, 'onActivation']);
    \GestiWork\Infrastructure\Bootstrapper::init();
}
