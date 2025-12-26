<?php
/**
 * GestiWork ERP - Planning (placeholder UI)
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

$planningYearMin = 2025;
$planningYearMax = 2050;

if (! current_user_can('manage_options')) {
    wp_die(esc_html__('Accès non autorisé.', 'gestiwork'), 403);
}
?>

<section class="gw-section gw-section-dashboard">
    <div class="gw-flex-between">
        <div>
            <h2 class="gw-section-title"><?php esc_html_e('Planning des sessions', 'gestiwork'); ?></h2>
        </div>
    </div>

    <div class="gw-settings-group">
        <p class="gw-section-description">
            <?php esc_html_e(
                'Affinez les résultats grâce aux filtres de planning.',
                'gestiwork'
            ); ?>
        </p>

        <div class="gw-settings-grid">
            <div class="gw-settings-field gw-settings-field--full">
                <p class="gw-settings-label"><?php esc_html_e('Filtres de planning', 'gestiwork'); ?></p>
                <form class="gw-advanced-search-form">
                    <div>
                        <label class="gw-settings-placeholder" for="gw_planing_start"><?php esc_html_e('Date de début', 'gestiwork'); ?></label>
                        <input type="date" id="gw_planing_start" class="gw-modal-input" disabled />
                    </div>
                    <div>
                        <label class="gw-settings-placeholder" for="gw_planing_end"><?php esc_html_e('Date de fin', 'gestiwork'); ?></label>
                        <input type="date" id="gw_planing_end" class="gw-modal-input" disabled />
                    </div>
                    <div>
                        <label class="gw-settings-placeholder" for="gw_planing_formateur"><?php esc_html_e('Formateur / Responsable', 'gestiwork'); ?></label>
                        <select id="gw_planing_formateur" class="gw-modal-input" disabled>
                            <option value=""><?php esc_html_e('Tous', 'gestiwork'); ?></option>
                        </select>
                    </div>
                    <div>
                        <label class="gw-settings-placeholder" for="gw_planing_statut"><?php esc_html_e('Statut', 'gestiwork'); ?></label>
                        <select id="gw_planing_statut" class="gw-modal-input" disabled>
                            <option value=""><?php esc_html_e('Tous', 'gestiwork'); ?></option>
                            <option><?php esc_html_e('Planifiée', 'gestiwork'); ?></option>
                            <option><?php esc_html_e('En cours', 'gestiwork'); ?></option>
                            <option><?php esc_html_e('Clôturée', 'gestiwork'); ?></option>
                            <option><?php esc_html_e('Annulée', 'gestiwork'); ?></option>
                        </select>
                    </div>

                    <div class="gw-advanced-search-actions">
                        <button class="gw-button gw-button--primary" type="button" disabled><?php esc_html_e('Appliquer', 'gestiwork'); ?></button>
                        <button class="gw-button gw-button--secondary" type="button" disabled><?php esc_html_e('Réinitialiser', 'gestiwork'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="gw-settings-group">
        <div class="gw-planing-view-switch-buttons">
            <div class="gw-planing-month-picker" id="gw_planing_month_picker_wrapper">
                <div class="gw-planing-month-picker__field">
                    
                    <select id="gw_planing_month_picker_month" class="gw-planing-month-picker__select gw-modal-input">
                        <?php for ($monthIndex = 1; $monthIndex <= 12; $monthIndex++) : ?>
                            <?php
                            $timestamp = strtotime('2025-' . $monthIndex . '-01');
                            $label = wp_date('F', $timestamp);
                            ?>
                            <option value="<?php echo esc_attr($monthIndex - 1); ?>"><?php echo esc_html(ucfirst($label)); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="gw-planing-month-picker__field">
                    <select
                        id="gw_planing_month_picker_year"
                        class="gw-planing-month-picker__select gw-modal-input"
                        data-gw-planing-year-min="<?php echo esc_attr($planningYearMin); ?>"
                        data-gw-planing-year-max="<?php echo esc_attr($planningYearMax); ?>"
                    >
                        <?php for ($year = $planningYearMin; $year <= $planningYearMax; $year++) : ?>
                            <option value="<?php echo esc_attr($year); ?>"><?php echo esc_html($year); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            <div class="gw-planing-vue-buttons-picker__field">
            <span class="gw-planing-view-label"><?php esc_html_e('Vue :', 'gestiwork'); ?></span>
            <button class="gw-button gw-button--ghost gw-planing-view-button gw-planing-view-button--active" data-gw-planing-view="year"><?php esc_html_e('Année', 'gestiwork'); ?></button>
            <button class="gw-button gw-button--ghost gw-planing-view-button" data-gw-planing-view="dayGridMonth"><?php esc_html_e('Mois', 'gestiwork'); ?></button>
            <button class="gw-button gw-button--ghost gw-planing-view-button" data-gw-planing-view="dayGridWeek"><?php esc_html_e('Semaine', 'gestiwork'); ?></button>
            <button class="gw-button gw-button--ghost gw-planing-view-button" data-gw-planing-view="dayGridDay"><?php esc_html_e('Jour', 'gestiwork'); ?></button>
            </div>
        </div>
        <div class="gw-settings-grid">
            <div class="gw-settings-field gw-settings-field--full">
                <div class="gw-planing-calendar-toolbar">
                    <div class="gw-planing-nav-group" id="gw_planing_semester_nav">
                        <button class="gw-link-button" type="button" data-gw-planing-nav="prev" data-gw-planing-only-year="1"><?php esc_html_e('Semestre précédent', 'gestiwork'); ?></button>
                        <span id="gw_planing_label" class="gw-planing-nav-label"><?php esc_html_e('Décembre 2025', 'gestiwork'); ?></span>
                        <button class="gw-link-button" type="button" data-gw-planing-nav="next" data-gw-planing-only-year="1"><?php esc_html_e('Semestre suivant', 'gestiwork'); ?></button>
                    </div>
                    <div class="gw-planing-week-nav" id="gw_planing_week_nav" hidden>
                        <span id="gw_planing_week_info" class="gw-planing-week-info"><?php esc_html_e('Décembre 2025 - S50', 'gestiwork'); ?></span>
                        <div class="gw-planing-week-arrows">
                            <button class="gw-button-icon" type="button" data-gw-planing-week-nav="prev" aria-label="<?php esc_attr_e('Semaine précédente', 'gestiwork'); ?>">
                                <span class="dashicons dashicons-arrow-left-alt2" aria-hidden="true"></span>
                            </button>
                            <span id="gw_planing_week_day" class="gw-planing-week-day"><?php esc_html_e('24 décembre 2025', 'gestiwork'); ?></span>
                            <button class="gw-button-icon" type="button" data-gw-planing-week-nav="next" aria-label="<?php esc_attr_e('Semaine suivante', 'gestiwork'); ?>">
                                <span class="dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div id="gw_planing_calendar" class="gw-planing-calendar" data-gw-planing></div>
                <div id="gw_planing_year_view" class="gw-planing-year-view" hidden></div>
            </div>
        </div>
    </div>

    <div class="gw-planing-legend">
        <span><?php esc_html_e('Légende :', 'gestiwork'); ?></span>
        <span class="gw-planing-badge gw-planing-badge--inter"><?php esc_html_e('INTER', 'gestiwork'); ?></span>
        <span class="gw-planing-badge gw-planing-badge--intra"><?php esc_html_e('INTRA', 'gestiwork'); ?></span>
        <span class="gw-planing-badge gw-planing-badge--dist"><?php esc_html_e('Distanciel', 'gestiwork'); ?></span>
        <span class="gw-planing-badge gw-planing-badge--cpf"><?php esc_html_e('CPF', 'gestiwork'); ?></span>
        <span class="gw-planing-badge gw-planing-badge--na"><?php esc_html_e('Non renseigné', 'gestiwork'); ?></span>
    </div>

    </section>
