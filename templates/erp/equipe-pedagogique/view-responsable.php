<?php

declare(strict_types=1);

if (! current_user_can('manage_options')) {
    wp_die(esc_html__('Accès non autorisé.', 'gestiwork'), 403);
}

$responsableId = isset($_GET['gw_responsable_id']) ? (int) $_GET['gw_responsable_id'] : 0;
$mode = isset($_GET['mode']) ? (string) $_GET['mode'] : '';

$isCreate = ($mode === 'create');

$backUrl = home_url('/gestiwork/equipe-pedagogique/');

$editUrl = add_query_arg([
    'gw_view' => 'Responsable',
    'gw_responsable_id' => $responsableId,
    'mode' => 'edit',
], home_url('/gestiwork/'));

$responsables = [
    1 => [
        'civilite' => 'Monsieur',
        'prenom' => 'Vincent',
        'nom' => 'PAUGAM',
        'fonction' => 'Formateur',
        'email' => 'paugam.vincent@gmail.com',
        'telephone' => '06 00 00 00 10',
        'role' => 'Interne',
        'sous_traitant' => 'Non',
        'nda_sous_traitant' => '',
        'adresse_postale' => '',
        'rue' => '',
        'code_postal' => '',
        'ville' => '',
        'competences' => ['Management'],
    ],
    2 => [
        'civilite' => 'Monsieur',
        'prenom' => 'Valentin',
        'nom' => 'CARLOS',
        'fonction' => 'Formateur',
        'email' => 'c.valentin@grunnings.fr',
        'telephone' => '06 00 00 00 11',
        'role' => 'Externe',
        'sous_traitant' => 'Oui',
        'nda_sous_traitant' => '12345678900',
        'adresse_postale' => 'Organisme sous-traitant',
        'rue' => '12 rue des Lilas',
        'code_postal' => '75000',
        'ville' => 'Paris',
        'competences' => ['Premiers secours'],
    ],
    3 => [
        'civilite' => 'Monsieur',
        'prenom' => 'Jérémy',
        'nom' => 'FRENKLIN',
        'fonction' => 'Formateur',
        'email' => 'julie.frenchin@laposte.net',
        'telephone' => '06 00 00 00 12',
        'role' => 'Externe',
        'sous_traitant' => 'Oui',
        'nda_sous_traitant' => '98765432100',
        'adresse_postale' => 'Organisme sous-traitant',
        'rue' => '4 avenue de la Gare',
        'code_postal' => '33000',
        'ville' => 'Bordeaux',
        'competences' => ['Bureautique'],
    ],
];

$responsable = isset($responsables[$responsableId]) ? $responsables[$responsableId] : null;

$responsableDefaults = [
    'civilite' => '',
    'prenom' => '',
    'nom' => '',
    'fonction' => '',
    'email' => '',
    'telephone' => '',
    'role' => '',
    'sous_traitant' => '',
    'nda_sous_traitant' => '',
    'adresse_postale' => '',
    'rue' => '',
    'code_postal' => '',
    'ville' => '',
    'competences' => [],
];

if ($isCreate) {
    $responsable = $responsableDefaults;
}

$responsableLabel = '';
if (is_array($responsable)) {
    $responsableLabel = trim((string) ($responsable['prenom'] ?? '') . ' ' . (string) ($responsable['nom'] ?? ''));
}

?>

<section class="gw-section gw-section-dashboard">
    <div class="gw-flex-between">
        <div>
            <h2 class="gw-section-title"><?php esc_html_e('Fiche formateur / responsable pédagogique', 'gestiwork'); ?></h2>
            <?php if ($isCreate) : ?>
                <p class="gw-section-description">
                    <?php esc_html_e('Créer un nouveau formateur', 'gestiwork'); ?>
                </p>
            <?php elseif ($responsableLabel !== '') : ?>
                <p class="gw-section-description">
                    <?php
                    echo esc_html(
                        sprintf(
                            /* translators: %s: responsable label */
                            __('Fiche formateur / responsable pédagogique : %s', 'gestiwork'),
                            $responsableLabel
                        )
                    );
                    ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="gw-flex-end">
            <a class="gw-button gw-button--secondary" href="<?php echo esc_url($backUrl); ?>">
                <?php esc_html_e('Retour à l’équipe pédagogique', 'gestiwork'); ?>
            </a>
            <?php if (! $isCreate && $responsableId > 0) : ?>
                <a class="gw-button gw-button--primary" href="<?php echo esc_url($editUrl); ?>" onclick="return false;">
                    <?php esc_html_e('Modifier', 'gestiwork'); ?>
                </a>
                <button type="button" class="gw-button gw-button--secondary" style="border-color:#d63638; color:#d63638; background:#fff;" onclick="return false;">
                    <span class="dashicons dashicons-trash" aria-hidden="true"></span>
                    <?php esc_html_e('Supprimer', 'gestiwork'); ?>
                </button>
            <?php endif; ?>
        </div>
    </div>

    <?php if (! $isCreate) : ?>
        <div class="gw-settings-tabs" role="tablist">
            <button type="button" class="gw-settings-tab gw-settings-tab--active" data-gw-tab="informations_generales">
                <?php esc_html_e('Informations générales', 'gestiwork'); ?>
            </button>
        </div>
    <?php endif; ?>

    <div class="gw-settings-panels">
        <div class="gw-settings-panel gw-settings-panel--active" data-gw-tab-panel="informations_generales">
            <div class="gw-tier-info-layout gw-grid-layout">
                <div class="gw-settings-group gw-m-0">
                    <div class="gw-flex-between-center">
                        <h3 class="gw-section-subtitle gw-m-0"><?php esc_html_e('Informations générales', 'gestiwork'); ?></h3>
                    </div>

                    <?php if (! is_array($responsable)) : ?>
                        <p class="gw-section-description gw-mt-12">
                            <?php esc_html_e('Sélectionnez un formateur depuis la liste pour afficher sa fiche.', 'gestiwork'); ?>
                        </p>
                    <?php else : ?>
                        <?php
                        $competencesValue = '';
                        if (isset($responsable['competences']) && is_array($responsable['competences'])) {
                            $competencesValue = implode(', ', array_map('strval', $responsable['competences']));
                        }
                        ?>
                        <form method="post" action="" class="gw-mt-12">
                            <input type="hidden" name="gw_action" value="gw_formateur_create" />
                            <?php wp_nonce_field('gw_formateur_manage', 'gw_nonce'); ?>

                            <div class="gw-grid-2">
                                <div>
                                    <label class="gw-settings-placeholder" for="gw_responsable_civilite"><?php esc_html_e('Civilité', 'gestiwork'); ?></label>
                                    <?php $civilite = isset($responsable['civilite']) ? (string) $responsable['civilite'] : ''; ?>
                                    <select id="gw_responsable_civilite" name="civilite" class="gw-modal-input">
                                        <option value=""<?php echo $civilite === '' ? ' selected' : ''; ?>><?php esc_html_e('Non renseigné', 'gestiwork'); ?></option>
                                        <option value="Madame"<?php echo $civilite === 'Madame' ? ' selected' : ''; ?>><?php esc_html_e('Madame', 'gestiwork'); ?></option>
                                        <option value="Monsieur"<?php echo $civilite === 'Monsieur' ? ' selected' : ''; ?>><?php esc_html_e('Monsieur', 'gestiwork'); ?></option>
                                    </select>
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_responsable_prenom"><?php esc_html_e('Prénom', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_responsable_prenom" name="prenom" class="gw-modal-input" value="<?php echo esc_attr((string) ($responsable['prenom'] ?? '')); ?>" />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_responsable_nom"><?php esc_html_e('Nom', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_responsable_nom" name="nom" class="gw-modal-input" value="<?php echo esc_attr((string) ($responsable['nom'] ?? '')); ?>" />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_responsable_fonction"><?php esc_html_e('Fonction', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_responsable_fonction" name="fonction" class="gw-modal-input" value="<?php echo esc_attr((string) ($responsable['fonction'] ?? '')); ?>" />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_responsable_email"><?php esc_html_e('Adresse e-mail', 'gestiwork'); ?></label>
                                    <input type="email" id="gw_responsable_email" name="email" class="gw-modal-input" value="<?php echo esc_attr((string) ($responsable['email'] ?? '')); ?>" />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_responsable_telephone"><?php esc_html_e('Numéro de téléphone', 'gestiwork'); ?></label>
                                    <input type="tel" id="gw_responsable_telephone" name="telephone" class="gw-modal-input" value="<?php echo esc_attr((string) ($responsable['telephone'] ?? '')); ?>" />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_responsable_role"><?php esc_html_e('Rôle', 'gestiwork'); ?></label>
                                    <?php $role = isset($responsable['role']) ? (string) $responsable['role'] : ''; ?>
                                    <select id="gw_responsable_role" name="role" class="gw-modal-input">
                                        <option value=""<?php echo $role === '' ? ' selected' : ''; ?>><?php esc_html_e('Sélectionner', 'gestiwork'); ?></option>
                                        <option value="Interne"<?php echo $role === 'Interne' ? ' selected' : ''; ?>><?php esc_html_e('Interne', 'gestiwork'); ?></option>
                                        <option value="Externe"<?php echo $role === 'Externe' ? ' selected' : ''; ?>><?php esc_html_e('Externe', 'gestiwork'); ?></option>
                                    </select>
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_responsable_sous_traitant"><?php esc_html_e('Sous-traitant', 'gestiwork'); ?></label>
                                    <?php $sousTraitant = isset($responsable['sous_traitant']) ? (string) $responsable['sous_traitant'] : ''; ?>
                                    <select id="gw_responsable_sous_traitant" name="sous_traitant" class="gw-modal-input">
                                        <option value=""<?php echo $sousTraitant === '' ? ' selected' : ''; ?>><?php esc_html_e('Sélectionner', 'gestiwork'); ?></option>
                                        <option value="Non"<?php echo $sousTraitant === 'Non' ? ' selected' : ''; ?>><?php esc_html_e('Non', 'gestiwork'); ?></option>
                                        <option value="Oui"<?php echo $sousTraitant === 'Oui' ? ' selected' : ''; ?>><?php esc_html_e('Oui', 'gestiwork'); ?></option>
                                    </select>
                                </div>

                                <div class="gw-full-width">
                                    <label class="gw-settings-placeholder" for="gw_responsable_nda_sous_traitant"><?php esc_html_e('NDA de l’organisme du sous-traitant', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_responsable_nda_sous_traitant" name="nda_sous_traitant" class="gw-modal-input" value="<?php echo esc_attr((string) ($responsable['nda_sous_traitant'] ?? '')); ?>" />
                                </div>

                                <div class="gw-full-width">
                                    <label class="gw-settings-placeholder" for="gw_responsable_adresse_postale"><?php esc_html_e('Adresse postale', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_responsable_adresse_postale" name="adresse_postale" class="gw-modal-input" value="<?php echo esc_attr((string) ($responsable['adresse_postale'] ?? '')); ?>" />
                                </div>

                                <div class="gw-full-width">
                                    <label class="gw-settings-placeholder" for="gw_responsable_rue"><?php esc_html_e('Numéro de rue et rue', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_responsable_rue" name="rue" class="gw-modal-input" value="<?php echo esc_attr((string) ($responsable['rue'] ?? '')); ?>" />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_responsable_code_postal"><?php esc_html_e('Code postal', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_responsable_code_postal" name="code_postal" class="gw-modal-input" value="<?php echo esc_attr((string) ($responsable['code_postal'] ?? '')); ?>" />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_responsable_ville"><?php esc_html_e('Ville', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_responsable_ville" name="ville" class="gw-modal-input" value="<?php echo esc_attr((string) ($responsable['ville'] ?? '')); ?>" />
                                </div>

                                <div class="gw-full-width">
                                    <label class="gw-settings-placeholder" for="gw_responsable_competences"><?php esc_html_e('Compétences', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_responsable_competences" name="competences" class="gw-modal-input" value="<?php echo esc_attr($competencesValue); ?>" placeholder="<?php echo esc_attr__('Ex. : Management, Bureautique...', 'gestiwork'); ?>" />
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
                        <p class="gw-section-description gw-mt-6"><?php esc_html_e('Données fictives : 0 tâche planifiée pour ce formateur.', 'gestiwork'); ?></p>
                        <div class="gw-mt-6">
                            <a class="gw-link-button" href="#"><?php esc_html_e('Voir les tâches', 'gestiwork'); ?></a>
                        </div>
                    </div>

                    <div class="gw-card">
                        <div class="gw-flex-between-center">
                            <h3 class="gw-section-subtitle gw-m-0"><?php esc_html_e('Coût du formateur', 'gestiwork'); ?></h3>
                            <a href="#" onclick="return false;" style="text-decoration:none; font-size:13px; display:inline-flex; align-items:center; gap:6px;">
                                <span class="dashicons dashicons-edit" aria-hidden="true"></span>
                                <?php esc_html_e('Modifier', 'gestiwork'); ?>
                            </a>
                        </div>

                        <div style="margin-top: 12px;">
                            <div style="font-size: 22px; font-weight: 700; color: var(--gw-color-text);">
                                <?php esc_html_e('0,00 € HT / jour', 'gestiwork'); ?>
                            </div>
                            <div class="gw-section-description" style="margin-top:6px;">
                                <?php esc_html_e('(soit 0,00 € HT / heure)', 'gestiwork'); ?>
                            </div>
                            <div class="gw-section-description" style="margin-top:6px;">
                                <?php esc_html_e('Taux de TVA : 20%', 'gestiwork'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
