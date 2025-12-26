<?php
/**
 * GestiWork ERP - Rapports (placeholder UI)
 */

declare(strict_types=1);

if (! current_user_can('manage_options')) {
    wp_die(esc_html__('Accès non autorisé.', 'gestiwork'), 403);
}
?>

<section class="gw-section gw-section-dashboard">
    <div class="gw-flex-between">
        <div>
            <h2 class="gw-section-title"><?php esc_html_e('Rapports d’activité', 'gestiwork'); ?></h2>
            <p class="gw-section-description">
                <?php esc_html_e('Cette zone centralisera vos exports PDF/Excel et les indicateurs consolidés.', 'gestiwork'); ?>
            </p>
        </div>
        <div class="gw-flex-end">
            <button class="gw-button gw-button--secondary" type="button" disabled>
                <span class="dashicons dashicons-media-spreadsheet" aria-hidden="true"></span>
                <?php esc_html_e('Exporter', 'gestiwork'); ?>
            </button>
        </div>
    </div>

    <div class="gw-settings-group">
        <div class="gw-placeholder gw-placeholder--panel">
            <p class="gw-placeholder-title"><?php esc_html_e('Rapports planifiés', 'gestiwork'); ?></p>
            <p class="gw-placeholder-text"><?php esc_html_e('Planification des envois, filtres dynamiques et archives seront affichés ici.', 'gestiwork'); ?></p>
        </div>
    </div>
</section>
