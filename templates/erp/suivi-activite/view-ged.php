<?php
/**
 * GestiWork ERP - GED (placeholder UI)
 */

declare(strict_types=1);

if (! current_user_can('manage_options')) {
    wp_die(esc_html__('Accès non autorisé.', 'gestiwork'), 403);
}
?>

<section class="gw-section gw-section-dashboard">
    <div class="gw-flex-between">
        <div>
            <h2 class="gw-section-title"><?php esc_html_e('GED - Gestion électronique des documents', 'gestiwork'); ?></h2>
            <p class="gw-section-description">
                <?php esc_html_e('Classez vos modèles, conventions signées et justificatifs dans une arborescence dédiée.', 'gestiwork'); ?>
            </p>
        </div>
        <div class="gw-flex-end">
            <button class="gw-button gw-button--secondary" type="button" disabled>
                <span class="dashicons dashicons-upload" aria-hidden="true"></span>
                <?php esc_html_e('Ajouter un document', 'gestiwork'); ?>
            </button>
        </div>
    </div>

    <div class="gw-settings-group">
        <div class="gw-placeholder gw-placeholder--panel">
            <p class="gw-placeholder-title"><?php esc_html_e('Bibliothèque centrale', 'gestiwork'); ?></p>
            <p class="gw-placeholder-text"><?php esc_html_e('Arborescence, filtres par type de document et statut de validation seront affichés ici.', 'gestiwork'); ?></p>
        </div>
    </div>
</section>
