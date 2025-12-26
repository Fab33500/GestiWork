<?php
/**
 * GestiWork ERP - Système (placeholder UI)
 */

declare(strict_types=1);

if (! current_user_can('manage_options')) {
    wp_die(esc_html__('Accès non autorisé.', 'gestiwork'), 403);
}

$gw_section = get_query_var('gw_section');
if ($gw_section === '' && isset($_GET['gw_section'])) {
    $gw_section = (string) $_GET['gw_section'];
}
$gw_section = strtolower(trim((string) $gw_section));

$allowed_sections = ['logs', 'connexions'];
$active_section = in_array($gw_section, $allowed_sections, true) ? $gw_section : 'overview';

$base_url = home_url('/gestiwork/systeme/');
$logs_url = home_url('/gestiwork/systeme/logs/');
$connexions_url = home_url('/gestiwork/systeme/connexions/');
?>

<section class="gw-section gw-section-dashboard">
    <div class="gw-flex-between">
        <div>
            <h2 class="gw-section-title"><?php esc_html_e('Système', 'gestiwork'); ?></h2>
            <p class="gw-section-description">
                <?php esc_html_e('Visualiser les logs et les connexions au système.', 'gestiwork'); ?>
            </p>
        </div>
        <div class="gw-flex-end gw-gap-8">
            <a class="gw-button gw-button--secondary<?php echo $active_section === 'logs' ? ' gw-button--primary' : ''; ?>"
               href="<?php echo esc_url($logs_url); ?>">
                <span class="dashicons dashicons-list-view" aria-hidden="true"></span>
                <?php esc_html_e('Logs', 'gestiwork'); ?>
            </a>
            <a class="gw-button gw-button--secondary<?php echo $active_section === 'connexions' ? ' gw-button--primary' : ''; ?>"
               href="<?php echo esc_url($connexions_url); ?>">
                <span class="dashicons dashicons-admin-network" aria-hidden="true"></span>
                <?php esc_html_e('Connexions', 'gestiwork'); ?>
            </a>
        </div>
    </div>

    <div class="gw-settings-group">
        <?php if ($active_section === 'logs') : ?>
            <div class="gw-settings-field gw-settings-field--full">
                <div class="gw-flex-between gw-mb-12">
                    <p class="gw-settings-label gw-mb-0"><?php esc_html_e('Journaux techniques', 'gestiwork'); ?></p>
                    <button class="gw-button gw-button--secondary gw-button--danger" type="button" disabled>
                        <span class="dashicons dashicons-trash" aria-hidden="true"></span>
                        <?php esc_html_e('Purger les logs', 'gestiwork'); ?>
                    </button>
                </div>
                <div class="gw-table-wrapper">
                    <table class="gw-table gw-table--system">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Date et heure', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Formateur⋅ice', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Action', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Type entité', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Identifiant entité', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Nom entité', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Clé', 'gestiwork'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7" class="gw-table-empty">
                                    <?php esc_html_e('Aucun log enregistré pour le moment.', 'gestiwork'); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php elseif ($active_section === 'connexions') : ?>
            <div class="gw-settings-field gw-settings-field--full">
                <p class="gw-settings-label"><?php esc_html_e('Historique des connexions', 'gestiwork'); ?></p>
                <div class="gw-table-wrapper">
                    <table class="gw-table gw-table--system">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Date et heure', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Prénom et nom', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Rôle', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('E-mail', 'gestiwork'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4" class="gw-table-empty">
                                    <?php esc_html_e('Aucune connexion enregistrée pour le moment.', 'gestiwork'); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else : ?>
            <div class="gw-placeholder gw-placeholder--panel">
                <p class="gw-placeholder-title"><?php esc_html_e('Centre de pilotage', 'gestiwork'); ?></p>
                <p class="gw-placeholder-text">
                    <?php esc_html_e('Dashboards techniques, alertes automatisées et synthèse des tâches planifiées seront affichés ici.', 'gestiwork'); ?>
                </p>
                <div class="gw-placeholder-links">
                    <a class="gw-link-button" href="<?php echo esc_url($logs_url); ?>">
                        <?php esc_html_e('Accéder aux logs', 'gestiwork'); ?>
                    </a>
                    <a class="gw-link-button" href="<?php echo esc_url($connexions_url); ?>">
                        <?php esc_html_e('Voir les connexions', 'gestiwork'); ?>
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
