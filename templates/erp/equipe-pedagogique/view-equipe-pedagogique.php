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

        <button type="button" class="gw-button gw-button--primary" data-gw-modal-target="gw-modal-formateur-create">
            <?php esc_html_e('Nouveau formateur', 'gestiwork'); ?>
        </button>
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
                            <?php foreach ($formateurs as $index => $formateur) : ?>
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
                                        <?php
                                        $responsableId = (int) $index + 1;
                                        $responsableViewUrl = add_query_arg(
                                            ['gw_view' => 'Responsable', 'gw_responsable_id' => $responsableId],
                                            home_url('/gestiwork/')
                                        );
                                        ?>
                                        <a href="<?php echo esc_url($responsableViewUrl); ?>" class="gw-link-primary-strong">
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
                                        <a class="gw-button gw-button--secondary" href="<?php echo esc_url($responsableViewUrl); ?>" title="<?php echo esc_attr__('Voir', 'gestiwork'); ?>">
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

<div class="gw-modal-backdrop" id="gw-modal-formateur-create" aria-hidden="true">
    <div class="gw-modal" role="dialog" aria-modal="true" aria-labelledby="gw-modal-formateur-create-title">
        <div class="gw-modal-header">
            <h3 class="gw-modal-title" id="gw-modal-formateur-create-title"><?php esc_html_e('Créer un formateur', 'gestiwork'); ?></h3>
            <button type="button" class="gw-modal-close" data-gw-modal-close="gw-modal-formateur-create" aria-label="<?php esc_attr_e('Fermer', 'gestiwork'); ?>">×</button>
        </div>

        <form method="post" action="">
            <input type="hidden" name="gw_action" value="gw_formateur_create" />
            <?php wp_nonce_field('gw_formateur_manage', 'gw_nonce'); ?>

            <div class="gw-modal-body">
                <p class="gw-modal-required-info">
                    <?php esc_html_e('Renseignez les informations principales du formateur. Les champs obligatoires sont marqués d’une astérisque (*).', 'gestiwork'); ?>
                </p>

                <div class="gw-modal-grid">
                    <div class="gw-modal-field">
                        <label for="gw_formateur_civilite"><?php esc_html_e('Civilité', 'gestiwork'); ?></label>
                        <select id="gw_formateur_civilite" name="civilite" class="gw-modal-input">
                            <option value=""><?php esc_html_e('Non renseigné', 'gestiwork'); ?></option>
                            <option value="Madame"><?php esc_html_e('Madame', 'gestiwork'); ?></option>
                            <option value="Monsieur"><?php esc_html_e('Monsieur', 'gestiwork'); ?></option>
                        </select>
                    </div>

                    <div class="gw-modal-field">
                        <label for="gw_formateur_prenom"><?php esc_html_e('Prénom', 'gestiwork'); ?> <span class="gw-required-asterisk">*</span></label>
                        <input type="text" id="gw_formateur_prenom" name="prenom" class="gw-modal-input" value="" required />
                    </div>

                    <div class="gw-modal-field">
                        <label for="gw_formateur_nom"><?php esc_html_e('Nom', 'gestiwork'); ?> <span class="gw-required-asterisk">*</span></label>
                        <input type="text" id="gw_formateur_nom" name="nom" class="gw-modal-input" value="" required />
                    </div>

                    <div class="gw-modal-field">
                        <label for="gw_formateur_fonction"><?php esc_html_e('Fonction', 'gestiwork'); ?></label>
                        <input type="text" id="gw_formateur_fonction" name="fonction" class="gw-modal-input" value="" />
                    </div>

                    <div class="gw-modal-field gw-full-width">
                        <label for="gw_formateur_email"><?php esc_html_e('E-mail', 'gestiwork'); ?> <span class="gw-required-asterisk">*</span></label>
                        <input type="email" id="gw_formateur_email" name="email" class="gw-modal-input" value="" required />
                    </div>

                    <div class="gw-modal-field">
                        <label for="gw_formateur_telephone"><?php esc_html_e('Numéro de téléphone', 'gestiwork'); ?></label>
                        <input type="tel" id="gw_formateur_telephone" name="telephone" class="gw-modal-input" value="" />
                    </div>

                    <div class="gw-modal-field">
                        <label for="gw_formateur_role"><?php esc_html_e('Rôle', 'gestiwork'); ?></label>
                        <select id="gw_formateur_role" name="role" class="gw-modal-input">
                            <option value=""><?php esc_html_e('Sélectionner', 'gestiwork'); ?></option>
                            <option value="Interne"><?php esc_html_e('Interne', 'gestiwork'); ?></option>
                            <option value="Externe"><?php esc_html_e('Externe', 'gestiwork'); ?></option>
                        </select>
                    </div>

                    <div class="gw-modal-field">
                        <label for="gw_formateur_sous_traitant"><?php esc_html_e('Sous-traitant', 'gestiwork'); ?></label>
                        <select id="gw_formateur_sous_traitant" name="sous_traitant" class="gw-modal-input">
                            <option value=""><?php esc_html_e('Sélectionner', 'gestiwork'); ?></option>
                            <option value="Non"><?php esc_html_e('Non', 'gestiwork'); ?></option>
                            <option value="Oui"><?php esc_html_e('Oui', 'gestiwork'); ?></option>
                        </select>
                    </div>

                    <div class="gw-modal-field gw-full-width">
                        <label for="gw_formateur_nda_sous_traitant"><?php esc_html_e('NDA de l’organisme du sous-traitant', 'gestiwork'); ?></label>
                        <input type="text" id="gw_formateur_nda_sous_traitant" name="nda_sous_traitant" class="gw-modal-input" value="" />
                    </div>

                    <div class="gw-modal-field gw-full-width">
                        <label for="gw_formateur_adresse_postale"><?php esc_html_e('Adresse postale', 'gestiwork'); ?></label>
                        <input type="text" id="gw_formateur_adresse_postale" name="adresse_postale" class="gw-modal-input" value="" />
                    </div>

                    <div class="gw-modal-field gw-full-width">
                        <label for="gw_formateur_rue"><?php esc_html_e('Numéro de rue et rue', 'gestiwork'); ?></label>
                        <input type="text" id="gw_formateur_rue" name="rue" class="gw-modal-input" value="" />
                    </div>

                    <div class="gw-modal-field">
                        <label for="gw_formateur_code_postal"><?php esc_html_e('Code postal', 'gestiwork'); ?></label>
                        <input type="text" id="gw_formateur_code_postal" name="code_postal" class="gw-modal-input" value="" />
                    </div>

                    <div class="gw-modal-field">
                        <label for="gw_formateur_ville"><?php esc_html_e('Ville', 'gestiwork'); ?></label>
                        <input type="text" id="gw_formateur_ville" name="ville" class="gw-modal-input" value="" />
                    </div>

                    <div class="gw-modal-field gw-full-width">
                        <label for="gw_formateur_competences"><?php esc_html_e('Compétences', 'gestiwork'); ?></label>
                        <input type="text" id="gw_formateur_competences" name="competences" class="gw-modal-input" value="" placeholder="<?php echo esc_attr__('Ex. : Management, Bureautique...', 'gestiwork'); ?>" />
                    </div>
                </div>
            </div>

            <div class="gw-modal-footer">
                <button type="button" class="gw-button gw-button--secondary" data-gw-modal-close="gw-modal-formateur-create"><?php esc_html_e('Annuler', 'gestiwork'); ?></button>
                <button type="submit" class="gw-button gw-button--primary"><?php esc_html_e('Créer', 'gestiwork'); ?></button>
            </div>
        </form>
    </div>
</div>
