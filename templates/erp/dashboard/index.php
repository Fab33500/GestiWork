<?php
/**
 * GestiWork ERP - Internal dashboard entry point (site dans le site)
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
        <h1 class="gw-title"><?php esc_html_e('GestiWork ERP', 'gestiwork'); ?></h1>
        <p class="gw-subtitle"><?php esc_html_e('Interface ERP - zone dédiée /gestiwork/', 'gestiwork'); ?></p>
    </header>

    <main class="gw-main">
        <p><?php esc_html_e('Le tableau de bord GestiWork est en cours de construction.', 'gestiwork'); ?></p>
    </main>
</div>

<?php wp_footer(); ?>
</body>
</html>
