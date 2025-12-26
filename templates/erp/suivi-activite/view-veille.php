<?php
/**
 * GestiWork ERP - Veille (placeholder UI)
 */

declare(strict_types=1);

if (! current_user_can('manage_options')) {
    wp_die(esc_html__('Accès non autorisé.', 'gestiwork'), 403);
}
?>

<section class="gw-section gw-section-dashboard">
    <div class="gw-flex-between">
        <div>
            <h2 class="gw-section-title"><?php esc_html_e('Veille réglementaire & pédagogique', 'gestiwork'); ?></h2>
            <p class="gw-section-description">
                <?php esc_html_e('Consignez vos sources de veille, alertes et évolutions réglementaires.', 'gestiwork'); ?>
            </p>
        </div>
        <div class="gw-flex-end">
            <button class="gw-button gw-button--secondary" type="button" disabled>
                <span class="dashicons dashicons-rss" aria-hidden="true"></span>
                <?php esc_html_e('Ajouter une veille', 'gestiwork'); ?>
            </button>
        </div>
    </div>

    <div class="gw-settings-group">
        <div class="gw-settings-field gw-settings-field--full">
            <p class="gw-settings-label"><?php esc_html_e('Récapitulatif des veilles', 'gestiwork'); ?></p>
            <div class="gw-table-wrapper">
                <table class="gw-table gw-table--veille">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Formateurs / Formatrices', 'gestiwork'); ?></th>
                            <th><?php esc_html_e('Dernière MAJ', 'gestiwork'); ?></th>
                            <th><?php esc_html_e('Catégorie', 'gestiwork'); ?></th>
                            <th><?php esc_html_e('Dernière veille', 'gestiwork'); ?></th>
                            <th><?php esc_html_e('Total veilles', 'gestiwork'); ?></th>
                            <th><?php esc_html_e('Pièces jointes', 'gestiwork'); ?></th>
                            <th><?php esc_html_e('Actions', 'gestiwork'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="7" class="gw-table-empty">
                                <?php esc_html_e('Aucune veille enregistrée pour le moment.', 'gestiwork'); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
