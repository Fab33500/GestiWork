<?php

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
