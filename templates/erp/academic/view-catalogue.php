<?php
/**
 * GestiWork ERP - Catalogue Formations (placeholder UI)
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
            <h2 class="gw-section-title"><?php esc_html_e('Catalogue Formations', 'gestiwork'); ?></h2>
            <p class="gw-section-description">
                <?php esc_html_e(
                    'Centralisez vos programmes, objectifs pédagogiques et modalités d’évaluation. Cette vue affichera bientôt l’intégralité du catalogue GestiWork.',
                    'gestiwork'
                ); ?>
            </p>
        </div>
        <div class="gw-flex-end">
            <button class="gw-button gw-button--secondary" type="button" disabled>
                <span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span>
                <?php esc_html_e('Créer une formation', 'gestiwork'); ?>
            </button>
        </div>
    </div>

    <div class="gw-settings-group">
        <p class="gw-section-description">
            <?php esc_html_e(
                'L’interface détaillera prochainement les fiches programmes, les blocs de compétences et les documents PDF générés automatiquement.',
                'gestiwork'
            ); ?>
        </p>
        <div class="gw-placeholder gw-placeholder--panel">
            <p class="gw-placeholder-title"><?php esc_html_e('Module en préparation', 'gestiwork'); ?></p>
            <p class="gw-placeholder-text">
                <?php esc_html_e(
                    'Les filtres (thématique, durée, modalité) et la recherche globale seront intégrés ici.',
                    'gestiwork'
                ); ?>
            </p>
        </div>
    </div>
</section>
