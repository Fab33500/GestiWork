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
    </div>

    <div class="gw-settings-group">
        <h3 class="gw-section-subtitle"><?php esc_html_e('Nouveau apprenant', 'gestiwork'); ?></h3>
        <p class="gw-section-description">
            <?php esc_html_e('Ajoutez un nouvel apprenant et préparez son suivi administratif. La création détaillée sera disponible prochainement.', 'gestiwork'); ?>
        </p>

        <button type="button" class="gw-button gw-button--primary" data-gw-modal-target="gw-modal-apprenant-create">
            <?php esc_html_e('Ajouter un apprenant', 'gestiwork'); ?>
        </button>
    </div>

    <div class="gw-settings-group">
        <h3 class="gw-section-subtitle"><?php esc_html_e('Vue d’ensemble des apprenants', 'gestiwork'); ?></h3>
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
                <p class="gw-settings-label"><?php esc_html_e('Apprenants récents', 'gestiwork'); ?></p>
                <div class="gw-table-wrapper">
                    <table class="gw-table">
                        <thead>
                            <tr>
                                <th class="gw-table-col-select"><input type="checkbox" /></th>
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
                                    <td><input type="checkbox" /></td>
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

<div class="gw-modal-backdrop" id="gw-modal-apprenant-create" aria-hidden="true">
    <div class="gw-modal" role="dialog" aria-modal="true" aria-labelledby="gw-modal-apprenant-create-title">
        <div class="gw-modal-header">
            <h3 class="gw-modal-title" id="gw-modal-apprenant-create-title"><?php esc_html_e('Créer un apprenant', 'gestiwork'); ?></h3>
            <button type="button" class="gw-modal-close" data-gw-modal-close="gw-modal-apprenant-create" aria-label="<?php esc_attr_e('Fermer', 'gestiwork'); ?>">×</button>
        </div>

        <form method="post" action="">
            <input type="hidden" name="gw_action" value="gw_apprenant_create" />
            <?php wp_nonce_field('gw_apprenant_manage', 'gw_nonce'); ?>

            <div class="gw-modal-body">
                <p class="gw-modal-required-info">
                    <?php esc_html_e('Renseignez les informations principales de l’apprenant. Les champs obligatoires sont marqués d’une astérisque (*).', 'gestiwork'); ?>
                </p>

                <div class="gw-modal-grid">
                    <div class="gw-modal-field">
                        <label for="gw_apprenant_civilite"><?php esc_html_e('Civilité', 'gestiwork'); ?></label>
                        <select id="gw_apprenant_civilite" name="civilite" class="gw-modal-input">
                            <option value=""><?php esc_html_e('Non renseigné', 'gestiwork'); ?></option>
                            <option value="Madame"><?php esc_html_e('Madame', 'gestiwork'); ?></option>
                            <option value="Monsieur"><?php esc_html_e('Monsieur', 'gestiwork'); ?></option>
                        </select>
                    </div>

                    <div class="gw-modal-field">
                        <label for="gw_apprenant_prenom"><?php esc_html_e('Prénom', 'gestiwork'); ?> <span class="gw-required-asterisk">*</span></label>
                        <input type="text" id="gw_apprenant_prenom" name="prenom" class="gw-modal-input" value="" required />
                    </div>

                    <div class="gw-modal-field">
                        <label for="gw_apprenant_nom"><?php esc_html_e('Nom', 'gestiwork'); ?> <span class="gw-required-asterisk">*</span></label>
                        <input type="text" id="gw_apprenant_nom" name="nom" class="gw-modal-input" value="" required />
                    </div>

                    <div class="gw-modal-field">
                        <label for="gw_apprenant_nom_naissance"><?php esc_html_e('Nom de naissance', 'gestiwork'); ?></label>
                        <input type="text" id="gw_apprenant_nom_naissance" name="nom_naissance" class="gw-modal-input" value="" />
                    </div>

                    <div class="gw-modal-field gw-full-width">
                        <label for="gw_apprenant_email"><?php esc_html_e('E-mail', 'gestiwork'); ?> <span class="gw-required-asterisk">*</span></label>
                        <input type="email" id="gw_apprenant_email" name="email" class="gw-modal-input" value="" required />
                    </div>

                    <div class="gw-modal-field">
                        <label for="gw_apprenant_date_naissance"><?php esc_html_e('Date de naissance', 'gestiwork'); ?></label>
                        <input type="date" id="gw_apprenant_date_naissance" name="date_naissance" class="gw-modal-input" value="" />
                    </div>

                    <div class="gw-modal-field">
                        <label for="gw_apprenant_telephone"><?php esc_html_e('Numéro de téléphone', 'gestiwork'); ?></label>
                        <input type="tel" id="gw_apprenant_telephone" name="telephone" class="gw-modal-input" value="" />
                    </div>

                    <div class="gw-modal-field">
                        <label for="gw_apprenant_entreprise"><?php esc_html_e('Entreprise', 'gestiwork'); ?></label>
                        <input type="text" id="gw_apprenant_entreprise" name="entreprise" class="gw-modal-input" value="" />
                    </div>

                    <div class="gw-modal-field">
                        <label for="gw_apprenant_origine"><?php esc_html_e('Origine', 'gestiwork'); ?></label>
                        <select id="gw_apprenant_origine" name="origine" class="gw-modal-input">
                            <option value=""><?php esc_html_e('Sélectionner', 'gestiwork'); ?></option>
                            <option value="Campagne"><?php esc_html_e('Campagne', 'gestiwork'); ?></option>
                            <option value="France travail"><?php esc_html_e('France travail', 'gestiwork'); ?></option>
                            <option value="Réseaux sociaux"><?php esc_html_e('Réseaux sociaux', 'gestiwork'); ?></option>
                        </select>
                    </div>

                    <div class="gw-modal-field">
                        <label for="gw_apprenant_statut_bpf"><?php esc_html_e('Statut BPF', 'gestiwork'); ?></label>
                        <select id="gw_apprenant_statut_bpf" name="statut_bpf" class="gw-modal-input">
                            <option value=""><?php esc_html_e('Non renseigné', 'gestiwork'); ?></option>
                            <option value="ex1"><?php esc_html_e('ex1', 'gestiwork'); ?></option>
                            <option value="ex2"><?php esc_html_e('ex2', 'gestiwork'); ?></option>
                            <option value="ex3"><?php esc_html_e('ex3', 'gestiwork'); ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="gw-modal-footer">
                <button type="button" class="gw-button gw-button--secondary" data-gw-modal-close="gw-modal-apprenant-create"><?php esc_html_e('Annuler', 'gestiwork'); ?></button>
                <button type="submit" class="gw-button gw-button--primary"><?php esc_html_e('Créer', 'gestiwork'); ?></button>
            </div>
        </form>
    </div>
</div>
