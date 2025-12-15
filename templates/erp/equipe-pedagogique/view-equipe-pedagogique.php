<?php

declare(strict_types=1);

if (! current_user_can('manage_options')) {
    wp_die(esc_html__('Accès non autorisé.', 'gestiwork'), 403);
}

$formateurs = [
    [
        'competences' => ['Management'],
        'nom' => 'Vincent PAUGAM',
        'email' => 'paugam.vincent@gmail.com',
        'role' => 'Interne',
        'sous_traitant' => 'Non',
        'created_at' => '20/09/2023',
    ],
    [
        'competences' => ['Premiers secours'],
        'nom' => 'Valentin CARLOS',
        'email' => 'c.valentin@grunnings.fr',
        'role' => 'Externe',
        'sous_traitant' => 'Oui',
        'created_at' => '20/09/2023',
    ],
    [
        'competences' => ['Bureautique'],
        'nom' => 'Jérémy FRENKLIN',
        'email' => 'julie.frenchin@laposte.net',
        'role' => 'Externe',
        'sous_traitant' => 'Oui',
        'created_at' => '20/09/2023',
    ],
];

$gwEquipePedagogiqueResetUrl = home_url('/gestiwork/equipe-pedagogique/');

$gw_search_action_url = '';
$gw_search_reset_url = $gwEquipePedagogiqueResetUrl;
$gw_search_submit_label = __('Rechercher', 'gestiwork');
$gw_search_fields = [
    [
        'type' => 'text',
        'id' => 'gw_formateurs_search_query',
        'name' => 'gw_formateurs_query',
        'label' => __('Recherche (nom, e-mail...)', 'gestiwork'),
        'value' => isset($_GET['gw_formateurs_query']) ? sanitize_text_field(wp_unslash((string) $_GET['gw_formateurs_query'])) : '',
        'placeholder' => __('Paugam, vincent@...', 'gestiwork'),
    ],
    [
        'type' => 'select',
        'id' => 'gw_formateurs_search_role',
        'name' => 'gw_formateurs_role',
        'label' => __('Rôle', 'gestiwork'),
        'value' => isset($_GET['gw_formateurs_role']) ? sanitize_text_field(wp_unslash((string) $_GET['gw_formateurs_role'])) : '',
        'options' => [
            '' => __('Tous', 'gestiwork'),
            'Interne' => __('Interne', 'gestiwork'),
            'Externe' => __('Externe', 'gestiwork'),
        ],
    ],
    [
        'type' => 'select',
        'id' => 'gw_formateurs_search_sous_traitant',
        'name' => 'gw_formateurs_sous_traitant',
        'label' => __('Sous-traitant', 'gestiwork'),
        'value' => isset($_GET['gw_formateurs_sous_traitant']) ? sanitize_text_field(wp_unslash((string) $_GET['gw_formateurs_sous_traitant'])) : '',
        'options' => [
            '' => __('Tous', 'gestiwork'),
            'Oui' => __('Oui', 'gestiwork'),
            'Non' => __('Non', 'gestiwork'),
        ],
    ],
];

?>

<section class="gw-section gw-section-dashboard">
    <div class="gw-flex-header">
        <div>
            <h2 class="gw-section-title"><?php esc_html_e('Équipe pédagogique', 'gestiwork'); ?></h2>
            <p class="gw-section-description"><?php esc_html_e('Gérez vos formateurs : création, recherche et compétences.', 'gestiwork'); ?></p>
        </div>

    </div>

    <div class="gw-settings-group">
        <h3 class="gw-section-subtitle"><?php esc_html_e('Nouveau formateur', 'gestiwork'); ?></h3>
        <p class="gw-section-description">
            <?php esc_html_e('Créez une nouvelle fiche formateur pour l’intégrer à votre équipe pédagogique. La création détaillée sera disponible prochainement.', 'gestiwork'); ?>
        </p>

        <a class="gw-button gw-button--primary" href="#">
            <?php esc_html_e('Nouveau formateur', 'gestiwork'); ?>
        </a>
    </div>

    <div class="gw-settings-group">
        <h3 class="gw-section-subtitle"><?php esc_html_e('Vue d’ensemble de l’équipe pédagogique', 'gestiwork'); ?></h3>
        <p class="gw-section-description">
            <?php esc_html_e('Consultez la liste des formateurs et appliquez des filtres grâce à la recherche avancée.', 'gestiwork'); ?>
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
                <p class="gw-settings-label"><?php esc_html_e('Formateurs récents', 'gestiwork'); ?></p>
                <div class="gw-table-wrapper">
                    <table class="gw-table">
                        <thead>
                            <tr>
                                <th class="gw-table-col-select"><input type="checkbox" /></th>
                                <th><?php esc_html_e('Compétences', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Prénom et nom', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Email', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Rôle', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Sous-traitant', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Date de création', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Actions', 'gestiwork'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($formateurs as $formateur) : ?>
                                <tr>
                                    <td><input type="checkbox" /></td>
                                    <td>
                                        <?php foreach (($formateur['competences'] ?? []) as $competence) : ?>
                                            <span class="gw-tag gw-tag--soft gw-tag--spaced">
                                                <?php echo esc_html($competence); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </td>
                                    <td>
                                        <a href="#" class="gw-link-primary-strong">
                                            <?php echo esc_html((string) ($formateur['nom'] ?? '')); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php if (!empty($formateur['email'])) : ?>
                                            <a href="mailto:<?php echo esc_attr((string) $formateur['email']); ?>">
                                                <?php echo esc_html((string) $formateur['email']); ?>
                                            </a>
                                        <?php else : ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="gw-tag">
                                            <?php echo esc_html((string) ($formateur['role'] ?? '-')); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="gw-tag <?php echo (($formateur['sous_traitant'] ?? '') === 'Oui') ? 'gw-tag--warning' : 'gw-tag--soft'; ?>">
                                            <?php echo esc_html((string) ($formateur['sous_traitant'] ?? '-')); ?>
                                        </span>
                                    </td>
                                    <td><?php echo esc_html((string) ($formateur['created_at'] ?? '-')); ?></td>
                                    <td>
                                        <a class="gw-button gw-button--secondary" href="#" title="<?php echo esc_attr__('Voir', 'gestiwork'); ?>">
                                            <span class="dashicons dashicons-visibility" aria-hidden="true"></span>
                                        </a>
                                        <a class="gw-button gw-button--secondary" href="#" title="<?php echo esc_attr__('Modifier', 'gestiwork'); ?>">
                                            <span class="dashicons dashicons-edit" aria-hidden="true"></span>
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
