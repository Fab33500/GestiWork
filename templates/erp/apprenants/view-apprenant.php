<?php

declare(strict_types=1);

if (! current_user_can('manage_options')) {
    wp_die(esc_html__('Accès non autorisé.', 'gestiwork'), 403);
}

$apprenantId = isset($_GET['gw_apprenant_id']) ? (int) $_GET['gw_apprenant_id'] : 0;

$backUrl = home_url('/gestiwork/apprenants/');

$apprenants = [
    1 => [
        'civilite' => 'Madame',
        'prenom' => 'Géraldine',
        'nom' => 'COUVERT',
        'nom_naissance' => 'DURAND',
        'date_naissance' => '1992-04-16',
        'email' => 'gege@yahoo.fr',
        'telephone' => '06 00 00 00 00',
        'entreprise' => 'Groupe BB - siège social Beaux Bâtons',
        'origine' => 'Campagne',
        'statut_bpf' => 'ex1',
    ],
    2 => [
        'civilite' => 'Madame',
        'prenom' => 'Emeline',
        'nom' => 'POUILLON',
        'nom_naissance' => '',
        'date_naissance' => '',
        'email' => 'e.pouillon@toto.fr',
        'telephone' => '06 00 00 00 01',
        'entreprise' => 'HP2M',
        'origine' => 'France travail',
        'statut_bpf' => 'ex2',
    ],
    3 => [
        'civilite' => 'Monsieur',
        'prenom' => 'Harry',
        'nom' => 'POTTER',
        'nom_naissance' => '',
        'date_naissance' => '',
        'email' => 'h.potter@smartof.tech',
        'telephone' => '06 00 00 00 02',
        'entreprise' => 'Beaux Bâtons',
        'origine' => 'Réseaux sociaux',
        'statut_bpf' => 'ex3',
    ],
];

$apprenant = isset($apprenants[$apprenantId]) ? $apprenants[$apprenantId] : null;

$apprenantLabel = '';
if (is_array($apprenant)) {
    $apprenantLabel = trim((string) ($apprenant['prenom'] ?? '') . ' ' . (string) ($apprenant['nom'] ?? ''));
}

?>

<section class="gw-section gw-section-dashboard">
    <div class="gw-flex-between">
        <div>
            <h2 class="gw-section-title"><?php esc_html_e('Fiche apprenant', 'gestiwork'); ?></h2>
            <?php if ($apprenantLabel !== '') : ?>
                <p class="gw-section-description">
                    <?php
                    echo esc_html(
                        sprintf(
                            /* translators: %s: apprenant label */
                            __('Fiche apprenant : %s', 'gestiwork'),
                            $apprenantLabel
                        )
                    );
                    ?>
                </p>
            <?php endif; ?>
        </div>
        <div class="gw-flex-end">
            <a class="gw-button gw-button--secondary" href="<?php echo esc_url($backUrl); ?>">
                <?php esc_html_e('Retour aux apprenants', 'gestiwork'); ?>
            </a>
        </div>
    </div>

    <div class="gw-settings-tabs" role="tablist">
        <button type="button" class="gw-settings-tab gw-settings-tab--active" data-gw-tab="informations_generales">
            <?php esc_html_e('Informations générales', 'gestiwork'); ?>
        </button>
    </div>

    <div class="gw-settings-panels">
        <div class="gw-settings-panel gw-settings-panel--active" data-gw-tab-panel="informations_generales">
            <div class="gw-tier-info-layout gw-grid-layout">
                <div class="gw-settings-group gw-m-0">
                    <div class="gw-flex-between-center">
                        <h3 class="gw-section-subtitle gw-m-0"><?php esc_html_e('Informations générales', 'gestiwork'); ?></h3>
                    </div>

                    <?php if (! is_array($apprenant)) : ?>
                        <p class="gw-section-description gw-mt-12">
                            <?php esc_html_e('Sélectionnez un apprenant depuis la liste pour afficher sa fiche.', 'gestiwork'); ?>
                        </p>
                    <?php else : ?>
                        <form method="post" action="" class="gw-mt-12">
                            <input type="hidden" name="gw_action" value="gw_apprenant_create" />
                            <?php wp_nonce_field('gw_apprenant_manage', 'gw_nonce'); ?>

                            <div class="gw-grid-2">
                                <div>
                                    <label class="gw-settings-placeholder" for="gw_apprenant_civilite"><?php esc_html_e('Civilité', 'gestiwork'); ?></label>
                                    <select id="gw_apprenant_civilite" name="civilite" class="gw-modal-input">
                                        <?php $civilite = isset($apprenant['civilite']) ? (string) $apprenant['civilite'] : ''; ?>
                                        <option value=""<?php echo $civilite === '' ? ' selected' : ''; ?>><?php esc_html_e('Non renseigné', 'gestiwork'); ?></option>
                                        <option value="Madame"<?php echo $civilite === 'Madame' ? ' selected' : ''; ?>><?php esc_html_e('Madame', 'gestiwork'); ?></option>
                                        <option value="Monsieur"<?php echo $civilite === 'Monsieur' ? ' selected' : ''; ?>><?php esc_html_e('Monsieur', 'gestiwork'); ?></option>
                                    </select>
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_apprenant_prenom"><?php esc_html_e('Prénom', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_apprenant_prenom" name="prenom" class="gw-modal-input" value="<?php echo esc_attr((string) ($apprenant['prenom'] ?? '')); ?>" />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_apprenant_nom"><?php esc_html_e('Nom', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_apprenant_nom" name="nom" class="gw-modal-input" value="<?php echo esc_attr((string) ($apprenant['nom'] ?? '')); ?>" />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_apprenant_nom_naissance"><?php esc_html_e('Nom de naissance', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_apprenant_nom_naissance" name="nom_naissance" class="gw-modal-input" value="<?php echo esc_attr((string) ($apprenant['nom_naissance'] ?? '')); ?>" />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_apprenant_date_naissance"><?php esc_html_e('Date de naissance', 'gestiwork'); ?></label>
                                    <input type="date" id="gw_apprenant_date_naissance" name="date_naissance" class="gw-modal-input" value="<?php echo esc_attr((string) ($apprenant['date_naissance'] ?? '')); ?>" />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_apprenant_telephone"><?php esc_html_e('Numéro de téléphone', 'gestiwork'); ?></label>
                                    <input type="tel" id="gw_apprenant_telephone" name="telephone" class="gw-modal-input" value="<?php echo esc_attr((string) ($apprenant['telephone'] ?? '')); ?>" />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_apprenant_email"><?php esc_html_e('Adresse e-mail', 'gestiwork'); ?></label>
                                    <input type="email" id="gw_apprenant_email" name="email" class="gw-modal-input" value="<?php echo esc_attr((string) ($apprenant['email'] ?? '')); ?>" />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_apprenant_entreprise"><?php esc_html_e('Entreprise', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_apprenant_entreprise" name="entreprise" class="gw-modal-input" value="<?php echo esc_attr((string) ($apprenant['entreprise'] ?? '')); ?>" />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_apprenant_origine"><?php esc_html_e('Origine', 'gestiwork'); ?></label>
                                    <?php $origine = isset($apprenant['origine']) ? (string) $apprenant['origine'] : ''; ?>
                                    <select id="gw_apprenant_origine" name="origine" class="gw-modal-input">
                                        <option value=""<?php echo $origine === '' ? ' selected' : ''; ?>><?php esc_html_e('Sélectionner', 'gestiwork'); ?></option>
                                        <option value="Campagne"<?php echo $origine === 'Campagne' ? ' selected' : ''; ?>><?php esc_html_e('Campagne', 'gestiwork'); ?></option>
                                        <option value="France travail"<?php echo $origine === 'France travail' ? ' selected' : ''; ?>><?php esc_html_e('France travail', 'gestiwork'); ?></option>
                                        <option value="Réseaux sociaux"<?php echo $origine === 'Réseaux sociaux' ? ' selected' : ''; ?>><?php esc_html_e('Réseaux sociaux', 'gestiwork'); ?></option>
                                    </select>
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_apprenant_statut_bpf"><?php esc_html_e('Statut BPF', 'gestiwork'); ?></label>
                                    <?php $statutBpf = isset($apprenant['statut_bpf']) ? (string) $apprenant['statut_bpf'] : ''; ?>
                                    <select id="gw_apprenant_statut_bpf" name="statut_bpf" class="gw-modal-input">
                                        <option value=""<?php echo $statutBpf === '' ? ' selected' : ''; ?>><?php esc_html_e('Non renseigné', 'gestiwork'); ?></option>
                                        <option value="ex1"<?php echo $statutBpf === 'ex1' ? ' selected' : ''; ?>><?php esc_html_e('ex1', 'gestiwork'); ?></option>
                                        <option value="ex2"<?php echo $statutBpf === 'ex2' ? ' selected' : ''; ?>><?php esc_html_e('ex2', 'gestiwork'); ?></option>
                                        <option value="ex3"<?php echo $statutBpf === 'ex3' ? ' selected' : ''; ?>><?php esc_html_e('ex3', 'gestiwork'); ?></option>
                                    </select>
                                </div>

                                <div class="gw-full-width gw-mt-6">
                                    <button type="submit" class="gw-button gw-button--primary">
                                        <?php esc_html_e('Enregistrer', 'gestiwork'); ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>

                <div class="gw-grid-layout">
                    <div class="gw-card">
                        <h3 class="gw-section-subtitle gw-m-0"><?php esc_html_e('Aucune tâche programmée', 'gestiwork'); ?></h3>
                        <p class="gw-section-description gw-mt-6"><?php esc_html_e('Données fictives : 0 tâche planifiée pour cet apprenant.', 'gestiwork'); ?></p>
                        <div class="gw-mt-6">
                            <a class="gw-link-button" href="#"><?php esc_html_e('Voir les tâches', 'gestiwork'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
