<?php
/**
 * GestiWork ERP - Shell layout (header, nav, main, footer)
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

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="gw-gestiwork-root">
<head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php esc_html_e('GestiWork ERP', 'gestiwork'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class('gw-body gw-gestiwork-dashboard'); ?>>
<div class="gw-app-wrapper">
    <header class="gw-header">
        <div class="gw-header-left">
            <h1 class="gw-title"><?php esc_html_e('GestiWork ERP', 'gestiwork'); ?></h1>
            <p class="gw-subtitle"><?php esc_html_e('Interface ERP - zone dédiée /gestiwork/', 'gestiwork'); ?></p>
        </div>

        <?php if (! empty($nav_items)) : ?>
            <button class="gw-header-toggle" type="button" aria-label="<?php esc_attr_e('Ouvrir le menu GestiWork', 'gestiwork'); ?>" aria-controls="gw-nav" aria-expanded="false">
                <span class="gw-header-toggle-bar"></span>
                <span class="gw-header-toggle-bar"></span>
                <span class="gw-header-toggle-bar"></span>
            </button>
        <?php endif; ?>
    </header>

    <div class="gw-layout <?php echo isset($layout_mode) ? esc_attr($layout_mode) : ''; ?>">
        <?php if (! empty($nav_items)) : ?>
            <nav id="gw-nav" class="gw-nav">
                <ul class="gw-nav-list">
                    <?php foreach ($nav_items as $item) : ?>
                        <li class="gw-nav-item">
                            <a href="<?php echo esc_url($item['url']); ?>"
                               class="gw-nav-link <?php echo ! empty($item['active']) ? 'gw-nav-link--active' : ''; ?>">
                                <?php echo esc_html($item['label']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        <?php endif; ?>

        <main class="gw-main">
            <div class="gw-main-inner">
                <?php if (! empty($content_template) && is_readable($content_template)) : ?>
                    <?php require $content_template; ?>
                <?php else : ?>
                    <section class="gw-section">
                        <h2 class="gw-section-title"><?php esc_html_e('Contenu indisponible', 'gestiwork'); ?></h2>
                        <p class="gw-section-description"><?php esc_html_e('Le contenu prévu n’a pas été trouvé.', 'gestiwork'); ?></p>
                    </section>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <div class="gw-modal-backdrop" id="gw-insee-modal" aria-hidden="true">
        <div class="gw-modal" role="dialog" aria-modal="true" aria-labelledby="gw-insee-modal-title">
            <div class="gw-modal-header">
                <h3 class="gw-modal-title" id="gw-insee-modal-title"><?php esc_html_e('Rechercher dans la base INSEE', 'gestiwork'); ?></h3>
                <button type="button" class="gw-modal-close" data-gw-modal-close="gw-insee-modal" aria-label="<?php esc_attr_e('Fermer', 'gestiwork'); ?>">×</button>
            </div>
            <div class="gw-modal-body">
                <div class="gw-insee-search-bar">
                    <label class="gw-sr-only" for="gw_insee_search_term"><?php esc_html_e('Saisissez un nom d’entreprise, un SIREN ou un SIRET', 'gestiwork'); ?></label>
                    <input type="text" id="gw_insee_search_term" class="gw-modal-input gw-insee-search-input" placeholder="<?php esc_attr_e('Raison sociale, SIREN ou SIRET', 'gestiwork'); ?>" autocomplete="off" />
                    <button type="button" class="gw-button gw-button--primary gw-insee-search-btn" id="gw_insee_search_button">
                        <span class="dashicons dashicons-search" aria-hidden="true"></span>
                        <?php esc_html_e('Rechercher', 'gestiwork'); ?>
                    </button>
                </div>
                <p class="gw-insee-hint">
                    <?php esc_html_e('Saisissez au moins 3 caractères. Vous pouvez rechercher par raison sociale, SIREN (9 chiffres) ou SIRET (14 chiffres).', 'gestiwork'); ?>
                    <br />
                    <?php esc_html_e('Pour un résultat précis, saisissez le nom complet ou un SIREN/SIRET ; sinon seuls les 25 premiers établissements sont affichés.', 'gestiwork'); ?>
                </p>
                <div id="gw_insee_search_status" class="gw-insee-status" aria-live="polite"></div>
                <div id="gw_insee_results" class="gw-insee-results" role="list"></div>
            </div>
            <div class="gw-modal-footer">
                <button type="button" class="gw-button gw-button--secondary" data-gw-modal-close="gw-insee-modal"><?php esc_html_e('Fermer', 'gestiwork'); ?></button>
            </div>
        </div>
    </div>

    <footer class="gw-footer">
        <span><?php esc_html_e('GestiWork ERP — Site dédié /gestiwork/', 'gestiwork'); ?></span>
    </footer>
</div>


<?php wp_footer(); ?>
</body>
</html>
