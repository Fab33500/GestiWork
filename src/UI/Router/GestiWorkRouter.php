<?php

declare(strict_types=1);

namespace GestiWork\UI\Router;

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

        $gwTemplate = GW_PLUGIN_DIR . 'templates/erp/dashboard/index.php';
        if (is_readable($gwTemplate)) {
            return $gwTemplate;
        }

        return $template;
    }
}
