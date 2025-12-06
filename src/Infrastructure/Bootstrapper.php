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
        // Exemple futur : Router interne, enregistrement des endpoints, etc.
    }
}
