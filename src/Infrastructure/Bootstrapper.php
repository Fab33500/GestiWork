<?php

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
    }

    public static function onActivation(): void
    {
        if (class_exists(\GestiWork\UI\Router\GestiWorkRouter::class)) {
            \GestiWork\UI\Router\GestiWorkRouter::registerRewriteRules();
        }

        flush_rewrite_rules();
    }
}
