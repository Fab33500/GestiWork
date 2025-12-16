<?php

declare(strict_types=1);

if (! current_user_can('manage_options')) {
    wp_die(esc_html__('Accès non autorisé.', 'gestiwork'), 403);
}

$apprenants = [
    [
        'nom' => 'Géraldine COUVERT',
        'email' => 'gege@yahoo.fr',
        'entreprise' => 'Groupe BB - siège social Beaux Bâtons',
        'origine' => 'Campagne',
    ],
    [
        'nom' => 'Emeline POUILLON',
        'email' => 'e.pouillon@toto.fr',
        'entreprise' => 'HP2M',
        'origine' => 'France travail',
    ],
    [
        'nom' => 'Harry POTTER',
        'email' => 'h.potter@smartof.tech',
        'entreprise' => 'Beaux Bâtons',
        'origine' => 'Réseaux sociaux',
    ],
];

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
    <div class="gw-flex-header">
        <div>
            <h2 class="gw-section-title"><?php esc_html_e('Apprenants', 'gestiwork'); ?></h2>
            <p class="gw-section-description"><?php esc_html_e('Gérez vos apprenants : création, recherche et suivi.', 'gestiwork'); ?></p>
        </div>
        <div class="gw-flex-end">
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
                            <?php foreach ($apprenants as $index => $apprenant) : ?>
                                <tr>
                                    <td>
                                        <?php
                                        $apprenantId = (int) $index + 1;
                                        $apprenantViewUrl = add_query_arg(
                                            ['gw_view' => 'Apprenant', 'gw_apprenant_id' => $apprenantId],
                                            home_url('/gestiwork/')
                                        );
                                        ?>
                                        <a href="<?php echo esc_url($apprenantViewUrl); ?>" class="gw-link-primary-strong">
                                            <?php echo esc_html($apprenant['nom']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="mailto:<?php echo esc_attr($apprenant['email']); ?>">
                                            <?php echo esc_html($apprenant['email']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo esc_html($apprenant['entreprise']); ?></td>
                                    <td>
                                        <span class="gw-tag">
                                            <?php echo esc_html($apprenant['origine']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a class="gw-button gw-button--secondary" href="<?php echo esc_url($apprenantViewUrl); ?>" title="<?php echo esc_attr__('Voir', 'gestiwork'); ?>">
                                            <span class="dashicons dashicons-visibility" aria-hidden="true"></span>
                                        </a>
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
