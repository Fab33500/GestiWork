<?php
/**
 * GestiWork ERP - Sessions (placeholder UI)
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

if (! current_user_can('manage_options')) {
    wp_die(esc_html__('Accès non autorisé.', 'gestiwork'), 403);
}
?>

<section class="gw-section gw-section-dashboard">
    <div class="gw-flex-between">
        <div>
            <h2 class="gw-section-title"><?php esc_html_e('Sessions de formation', 'gestiwork'); ?></h2>
            <p class="gw-section-description">
                <?php esc_html_e(
                    'Planifiez vos sessions, affectez les formateurs et gérez les émargements. L’écran est en cours de construction.',
                    'gestiwork'
                ); ?>
            </p>
        </div>
        <div class="gw-flex-end">
            <button class="gw-button gw-button--secondary" type="button" disabled>
                <span class="dashicons dashicons-calendar-alt" aria-hidden="true"></span>
                <?php esc_html_e('Créer une session', 'gestiwork'); ?>
            </button>
        </div>
    </div>

    <div class="gw-settings-group">
        <p class="gw-section-description">
            <?php esc_html_e('Les filtres par période, lieu, formateur et statut seront disponibles prochainement.', 'gestiwork'); ?>
        </p>
        <div class="gw-placeholder gw-placeholder--panel">
            <p class="gw-placeholder-title"><?php esc_html_e('Suivi activité', 'gestiwork'); ?></p>
            <p class="gw-placeholder-text">
                <?php esc_html_e('Modules à venir :', 'gestiwork'); ?>
            </p>
            <ul class="gw-placeholder-list">
                <li><?php esc_html_e('Rapports', 'gestiwork'); ?></li>
                <li><?php esc_html_e('BPF', 'gestiwork'); ?></li>
                <li><?php esc_html_e('Veille', 'gestiwork'); ?></li>
                <li><?php esc_html_e('GED', 'gestiwork'); ?></li>
                <li><?php esc_html_e('Système', 'gestiwork'); ?></li>
            </ul>
        </div>
    </div>
</section>
