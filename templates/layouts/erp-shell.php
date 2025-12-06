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
        <div>
            <h1 class="gw-title"><?php esc_html_e('GestiWork ERP', 'gestiwork'); ?></h1>
            <p class="gw-subtitle"><?php esc_html_e('Interface ERP - zone dédiée /gestiwork/', 'gestiwork'); ?></p>
        </div>
    </header>

    <div class="gw-layout <?php echo isset($layout_mode) ? esc_attr($layout_mode) : ''; ?>">
        <?php if (! empty($nav_items)) : ?>
            <nav class="gw-nav">
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

    <footer class="gw-footer">
        <span><?php esc_html_e('GestiWork ERP — Site dédié /gestiwork/', 'gestiwork'); ?></span>
    </footer>
</div>

<?php wp_footer(); ?>
</body>
</html>
