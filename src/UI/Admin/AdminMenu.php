<?php
/**
 * GestiWork ERP - Admin main menu entry
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

namespace GestiWork\UI\Admin;

class AdminMenu
{
    public const MENU_SLUG = 'gestiwork-erp';

    public static function register(): void
    {
        add_action('admin_menu', [self::class, 'registerMenu']);
        add_action('admin_print_footer_scripts', [self::class, 'overrideMenuLink']);
    }

    public static function registerMenu(): void
    {
        add_menu_page(
            __('GestiWork ERP', 'gestiwork'),
            __('GestiWork ERP', 'gestiwork'),
            'manage_options',
            self::MENU_SLUG,
            [self::class, 'redirectToFrontend'],
            'dashicons-welcome-learn-more',
            26
        );
    }

    public static function redirectToFrontend(): void
    {
        $url = esc_url_raw(home_url('/gestiwork/'));

        // Tentative de redirection serveur classique
        if (! headers_sent()) {
            wp_safe_redirect($url);
            exit;
        }

        // Fallback : redirection JavaScript + lien manuel
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Redirection vers GestiWork ERP', 'gestiwork'); ?></h1>
            <p><?php esc_html_e('Vous allez être redirigé vers l’interface GestiWork.', 'gestiwork'); ?></p>
            <p>
                <a class="button button-primary" href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer">
                    <?php esc_html_e('Accéder à GestiWork', 'gestiwork'); ?>
                </a>
            </p>
            <script>
                window.location.href = <?php echo wp_json_encode($url); ?>;
            </script>
        </div>
        <?php
        exit;
    }

    public static function overrideMenuLink(): void
    {
        $url = esc_url(home_url('/gestiwork/'));
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var menu = document.getElementById('toplevel_page_gestiwork-erp');
                if (!menu) {
                    return;
                }

                var link = menu.querySelector('a.menu-top');
                if (!link) {
                    return;
                }

                var targetUrl = <?php echo wp_json_encode($url); ?>;
                link.setAttribute('href', targetUrl);

                link.addEventListener('click', function (event) {
                    event.preventDefault();
                    window.location.href = targetUrl;
                });
            });
        </script>
        <?php
    }
}
