<?php

declare(strict_types=1);

use GestiWork\Domain\ResponsableFormateur\ResponsableFormateurProvider;
use GestiWork\Domain\ResponsableFormateur\FormateurCompetenceProvider;

if (! current_user_can('manage_options')) {
    wp_die(esc_html__('Accès non autorisé.', 'gestiwork'), 403);
}

$formateurs = ResponsableFormateurProvider::getAll();

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
            <p class="gw-section-description"><?php esc_html_e('Gérez vos formateurs & responsables pédagogiques : création, recherche et compétences.', 'gestiwork'); ?></p>
        </div>
        <div class="gw-flex-end">
            <a class="gw-button gw-button--secondary gw-button--cta" href="<?php echo esc_url(add_query_arg(['gw_view' => 'Responsable', 'mode' => 'create'], home_url('/gestiwork/'))); ?>">
                <?php esc_html_e('Créer un nouveau membre de votre équipe', 'gestiwork'); ?>
            </a>
        </div>
    </div>

    <div class="gw-settings-group">
        <p class="gw-section-description">
            <?php esc_html_e('Consultez la liste des membres de votre équipe et appliquez des filtres grâce à la recherche avancée.', 'gestiwork'); ?>
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
                <p class="gw-settings-label"><?php esc_html_e('Membres & intervenants externes', 'gestiwork'); ?></p>
                <div class="gw-table-wrapper">
                    <table class="gw-table gw-table--formateurs">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Prénom et nom', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Email', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Rôle', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Sous-traitant', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Date de création', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Compétences', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Actions', 'gestiwork'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($formateurs as $formateur) : ?>
                                <tr>
                                    <td>
                                        <?php
                                        $responsableId = isset($formateur['id']) ? (int) $formateur['id'] : 0;
                                        $responsableViewUrl = add_query_arg(
                                            ['gw_view' => 'Responsable', 'gw_responsable_id' => $responsableId],
                                            home_url('/gestiwork/')
                                        );

                                        $prenom = isset($formateur['prenom']) ? trim((string) $formateur['prenom']) : '';
                                        $nom = isset($formateur['nom']) ? trim((string) $formateur['nom']) : '';
                                        $label = trim($prenom . ' ' . $nom);
                                        if ($label === '') {
                                            $label = $responsableId > 0 ? (string) $responsableId : '-';
                                        }
                                        ?>
                                        <?php if ($responsableId > 0) : ?>
                                            <a href="<?php echo esc_url($responsableViewUrl); ?>" class="gw-link-primary-strong">
                                                <?php echo esc_html($label); ?>
                                            </a>
                                        <?php else : ?>
                                            <?php echo esc_html($label); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php $email = isset($formateur['email']) ? trim((string) $formateur['email']) : ''; ?>
                                        <?php if ($email !== '') : ?>
                                            <a href="mailto:<?php echo esc_attr($email); ?>">
                                                <?php echo esc_html($email); ?>
                                            </a>
                                        <?php else : ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="gw-tag">
                                            <?php echo esc_html((string) ($formateur['role_type'] ?? '-')); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php $sousTraitant = (string) ($formateur['sous_traitant'] ?? '-'); ?>
                                        <span class="gw-tag <?php echo ($sousTraitant === 'Oui') ? 'gw-tag--warning' : 'gw-tag--soft'; ?>">
                                            <?php echo esc_html($sousTraitant); ?>
                                        </span>
                                    </td>
                                    <td><?php echo esc_html((string) ($formateur['created_at'] ?? '-')); ?></td>
                                    <td>
                                        <?php
                                        $competences = [];
                                        if ($responsableId > 0) {
                                            $competences = FormateurCompetenceProvider::getCompetencesByFormateurId($responsableId);
                                        }
                                        if (is_array($competences) && count($competences) > 0) :
                                            foreach ($competences as $competence) :
                                        ?>
                                                <span class="gw-tag gw-tag--soft gw-tag--spaced">
                                                    <?php echo esc_html((string) $competence); ?>
                                                </span>
                                        <?php
                                            endforeach;
                                        else :
                                        ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($responsableId > 0) : ?>
                                            <a class="gw-button gw-button--secondary" href="<?php echo esc_url($responsableViewUrl); ?>" title="<?php echo esc_attr__('Voir', 'gestiwork'); ?>">
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
