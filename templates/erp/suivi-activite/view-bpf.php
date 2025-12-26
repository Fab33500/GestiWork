<?php
/**
 * GestiWork ERP - BPF (placeholder UI)
 */

declare(strict_types=1);

if (! current_user_can('manage_options')) {
    wp_die(esc_html__('Accès non autorisé.', 'gestiwork'), 403);
}
?>

<section class="gw-section gw-section-dashboard">
    <div class="gw-flex-between">
        <div>
            <h2 class="gw-section-title"><?php esc_html_e('BPF & conformité Qualiopi', 'gestiwork'); ?></h2>
            <p class="gw-section-description">
                <?php esc_html_e('Centralisez les preuves exigées par la certification et suivez vos plans d’actions.', 'gestiwork'); ?>
            </p>
        </div>
        <div class="gw-flex-end">
            <button class="gw-button gw-button--secondary" type="button" disabled>
                <span class="dashicons dashicons-yes-alt" aria-hidden="true"></span>
                <?php esc_html_e('Ajouter une preuve', 'gestiwork'); ?>
            </button>
        </div>
    </div>

    <div class="gw-settings-group">
        <div class="gw-placeholder gw-placeholder--panel">
            <p class="gw-placeholder-title"><?php esc_html_e('Suivi conformité', 'gestiwork'); ?></p>
            <p class="gw-placeholder-text"><?php esc_html_e('Inventaires, statuts (OK / À compléter / En alerte) et pièces jointes figureront ici.', 'gestiwork'); ?></p>
        </div>
    </div>
</section>
