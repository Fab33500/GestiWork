<?php
/**
 * GestiWork ERP - Router
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

namespace GestiWork\UI\Router;

use GestiWork\UI\Controller\PdfPreviewController;

class GestiWorkRouter
{
    public const QUERY_VAR = 'gw_gestiwork';

    public static function register(): void
    {
        add_action('init', [self::class, 'registerRewriteRules']);
        add_filter('query_vars', [self::class, 'registerQueryVars']);
        add_filter('template_include', [self::class, 'handleTemplate']);
    }

    public static function registerRewriteRules(): void
    {
        add_rewrite_rule('^gestiwork/?$', 'index.php?' . self::QUERY_VAR . '=1', 'top');

        add_rewrite_rule(
            '^gestiwork/([^/]+)/?$',
            'index.php?' . self::QUERY_VAR . '=1&gw_view=$matches[1]',
            'top'
        );

        add_rewrite_rule(
            '^gestiwork/([^/]+)/([^/]+)/?$',
            'index.php?' . self::QUERY_VAR . '=1&gw_view=$matches[1]&gw_section=$matches[2]',
            'top'
        );
    }

    /**
     * @param array<string> $vars
     * @return array<string>
     */
    public static function registerQueryVars(array $vars): array
    {
        $vars[] = self::QUERY_VAR;

        $vars[] = 'gw_view';

        $vars[] = 'gw_section';

        return $vars;
    }

    public static function handleTemplate(string $template): string
    {
        if (get_query_var(self::QUERY_VAR) !== '1') {
            return $template;
        }

        // Gestion de l'aperçu PDF
        $gwView = get_query_var('gw_view');
        if ($gwView === 'pdf-preview') {
            $templateId = isset($_GET['template_id']) ? (int) $_GET['template_id'] : 0;
            if ($templateId > 0) {
                PdfPreviewController::renderPreview($templateId);
                exit;
            }
        }

        $gwTemplate = GW_PLUGIN_DIR . 'templates/erp/dashboard/index.php';
        if (is_readable($gwTemplate)) {
            return $gwTemplate;
        }

        return $template;
    }
}
