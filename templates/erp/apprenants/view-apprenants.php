<?php

declare(strict_types=1);

use GestiWork\Domain\Apprenant\ApprenantProvider;
use GestiWork\Domain\Tiers\TierProvider;

if (! current_user_can('manage_options')) {
    wp_die(esc_html__('Accès non autorisé.', 'gestiwork'), 403);
}

$notice = isset($_GET['gw_notice']) ? strtolower(trim((string) $_GET['gw_notice'])) : '';

// Récupérer les filtres de recherche
$searchFilters = [];
if (!empty($_GET['gw_apprenants_query'])) {
    $searchFilters['query'] = sanitize_text_field(wp_unslash((string) $_GET['gw_apprenants_query']));
}
if (!empty($_GET['gw_apprenants_entreprise'])) {
    $searchFilters['entreprise'] = sanitize_text_field(wp_unslash((string) $_GET['gw_apprenants_entreprise']));
}
if (!empty($_GET['gw_apprenants_origine'])) {
    $searchFilters['origine'] = sanitize_text_field(wp_unslash((string) $_GET['gw_apprenants_origine']));
}

// Utiliser search() si des filtres sont présents, sinon getAll()
$apprenants = !empty($searchFilters) ? ApprenantProvider::search($searchFilters) : ApprenantProvider::getAll();

$gwApprenantsResetUrl = home_url('/gestiwork/apprenants/');

$gw_search_action_url = '';
$gw_search_reset_url = $gwApprenantsResetUrl;
$gw_search_submit_label = __('Rechercher', 'gestiwork');
$gw_search_fields = [
    [
        'type' => 'text',
        'id' => 'gw_apprenants_search_query',
        'name' => 'gw_apprenants_query',
        'label' => __('Recherche (nom, e-mail...)', 'gestiwork'),
        'value' => isset($_GET['gw_apprenants_query']) ? sanitize_text_field(wp_unslash((string) $_GET['gw_apprenants_query'])) : '',
        'placeholder' => __('Dupont, jean@...', 'gestiwork'),
    ],
    [
        'type' => 'text',
        'id' => 'gw_apprenants_search_entreprise',
        'name' => 'gw_apprenants_entreprise',
        'label' => __('Entreprise', 'gestiwork'),
        'value' => isset($_GET['gw_apprenants_entreprise']) ? sanitize_text_field(wp_unslash((string) $_GET['gw_apprenants_entreprise'])) : '',
        'placeholder' => __('HP2M', 'gestiwork'),
    ],
    [
        'type' => 'select',
        'id' => 'gw_apprenants_search_origine',
        'name' => 'gw_apprenants_origine',
        'label' => __('Origine', 'gestiwork'),
        'value' => isset($_GET['gw_apprenants_origine']) ? sanitize_text_field(wp_unslash((string) $_GET['gw_apprenants_origine'])) : '',
        'options' => [
            '' => __('Toutes', 'gestiwork'),
            'Campagne' => __('Campagne', 'gestiwork'),
            'France travail' => __('France travail', 'gestiwork'),
            'Réseaux sociaux' => __('Réseaux sociaux', 'gestiwork'),
        ],
    ],
];

?>

<section class="gw-section gw-section-dashboard">
    <?php if ($notice === 'sync_particuliers_apprenants_done') : ?>
        <div class="notice notice-success gw-notice-spacing">
            <p>
                <?php
                $created = isset($_GET['gw_sync_created']) ? (int) $_GET['gw_sync_created'] : 0;
                $linked = isset($_GET['gw_sync_linked']) ? (int) $_GET['gw_sync_linked'] : 0;
                $skipped = isset($_GET['gw_sync_skipped']) ? (int) $_GET['gw_sync_skipped'] : 0;
                $conflicts = isset($_GET['gw_sync_conflicts']) ? (int) $_GET['gw_sync_conflicts'] : 0;

                echo esc_html(sprintf('Synchronisation terminée. Créés: %d, rattachés: %d, déjà OK: %d, conflits: %d.', $created, $linked, $skipped, $conflicts));
                ?>
            </p>
        </div>
    <?php elseif ($notice === 'sync_particuliers_apprenants_failed') : ?>
        <div class="notice notice-error gw-notice-spacing">
            <p><?php esc_html_e('Erreur lors de la synchronisation des clients particuliers.', 'gestiwork'); ?></p>
        </div>
    <?php endif; ?>

    <div class="gw-flex-header">
        <div>
            <h2 class="gw-section-title"><?php esc_html_e('Apprenants', 'gestiwork'); ?></h2>
            <p class="gw-section-description"><?php esc_html_e('Gérez vos apprenants : création, recherche et suivi.', 'gestiwork'); ?></p>
        </div>
        <div class="gw-flex-end">
            <form method="post" action="" class="gw-form-inline">
                <input type="hidden" name="gw_action" value="gw_sync_particuliers_apprenants" />
                <?php wp_nonce_field('gw_sync_particuliers_apprenants', 'gw_nonce'); ?>
                <button type="submit" class="gw-button gw-button--secondary" title="<?php echo esc_attr__('Synchroniser les clients particuliers', 'gestiwork'); ?>">
                    <?php esc_html_e('Synchroniser clients particuliers', 'gestiwork'); ?>
                </button>
            </form>
            <a class="gw-button gw-button--secondary gw-button--cta" href="<?php echo esc_url(add_query_arg(['gw_view' => 'Apprenant', 'mode' => 'create'], home_url('/gestiwork/'))); ?>">
                <?php esc_html_e('Créer un nouvel apprenant', 'gestiwork'); ?>
            </a>
        </div>
    </div>

    <div class="gw-settings-group">
        <p class="gw-section-description">
            <?php esc_html_e('Retrouvez ici la liste des apprenants et filtrez-la grâce à la recherche avancée.', 'gestiwork'); ?>
        </p>

        <div class="gw-settings-grid">
            <div class="gw-settings-field gw-settings-field--full">
                <p class="gw-settings-label"><?php esc_html_e('Recherche avancée', 'gestiwork'); ?></p>

                <?php
                $partial = GW_PLUGIN_DIR . 'templates/erp/partials/advanced-search.php';
                if (is_readable($partial)) {
                    require $partial;
                }
                ?>
            </div>

            <div class="gw-settings-field gw-settings-field--full">
                <p class="gw-settings-label"><?php esc_html_e('Apprenants inscrits', 'gestiwork'); ?></p>
                <div class="gw-table-wrapper">
                    <table class="gw-table gw-table--apprenants">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Prénom et nom', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Email', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Entreprise', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Origine', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Actions', 'gestiwork'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($apprenants as $apprenant) : ?>
                                <tr>
                                    <td>
                                        <?php
                                        $apprenantId = isset($apprenant['id']) ? (int) $apprenant['id'] : 0;
                                        $apprenantViewUrl = add_query_arg(
                                            ['gw_view' => 'Apprenant', 'gw_apprenant_id' => $apprenantId],
                                            home_url('/gestiwork/')
                                        );

                                        $prenom = isset($apprenant['prenom']) ? trim((string) $apprenant['prenom']) : '';
                                        $nom = isset($apprenant['nom']) ? trim((string) $apprenant['nom']) : '';
                                        $label = trim($prenom . ' ' . $nom);
                                        if ($label === '') {
                                            $label = $apprenantId > 0 ? (string) $apprenantId : '-';
                                        }
                                        ?>
                                        <?php if ($apprenantId > 0) : ?>
                                            <a href="<?php echo esc_url($apprenantViewUrl); ?>" class="gw-link-primary-strong">
                                                <?php echo esc_html($label); ?>
                                            </a>
                                        <?php else : ?>
                                            <?php echo esc_html($label); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php $email = isset($apprenant['email']) ? trim((string) $apprenant['email']) : ''; ?>
                                        <?php if ($email !== '') : ?>
                                            <a href="mailto:<?php echo esc_attr($email); ?>">
                                                <?php echo esc_html($email); ?>
                                            </a>
                                        <?php else : ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $entrepriseLabel = '-';
                                        $entrepriseId = isset($apprenant['entreprise_id']) ? (int) $apprenant['entreprise_id'] : 0;
                                        if ($entrepriseId > 0) {
                                            $tier = TierProvider::getById($entrepriseId);
                                            if (is_array($tier)) {
                                                $raisonSociale = isset($tier['raison_sociale']) ? trim((string) $tier['raison_sociale']) : '';
                                                $nomTier = isset($tier['nom']) ? trim((string) $tier['nom']) : '';
                                                $prenomTier = isset($tier['prenom']) ? trim((string) $tier['prenom']) : '';
                                                $entrepriseLabel = $raisonSociale !== '' ? $raisonSociale : trim($prenomTier . ' ' . $nomTier);
                                                if ($entrepriseLabel === '') {
                                                    $entrepriseLabel = (string) $entrepriseId;
                                                }
                                            }
                                        }
                                        echo esc_html($entrepriseLabel);
                                        ?>
                                    </td>
                                    <td>
                                        <span class="gw-tag">
                                            <?php echo esc_html((string) ($apprenant['origine'] ?? '-')); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($apprenantId > 0) : ?>
                                            <a class="gw-button gw-button--secondary" href="<?php echo esc_url($apprenantViewUrl); ?>" title="<?php echo esc_attr__('Voir', 'gestiwork'); ?>">
                                                <span class="dashicons dashicons-visibility" aria-hidden="true"></span>
                                            </a>
                                        <?php else : ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
