<?php

declare(strict_types=1);

use GestiWork\Domain\ResponsableFormateur\ResponsableFormateurProvider;
use GestiWork\Domain\ResponsableFormateur\FormateurCompetenceProvider;

if (! current_user_can('manage_options')) {
    wp_die(esc_html__('Accès non autorisé.', 'gestiwork'), 403);
}

$responsableId = isset($_GET['gw_responsable_id']) ? (int) $_GET['gw_responsable_id'] : 0;
$mode = isset($_GET['mode']) ? (string) $_GET['mode'] : '';

$isCreate = ($mode === 'create');
$isEdit = ($mode === 'edit');

$backUrl = home_url('/gestiwork/equipe-pedagogique/');

$editUrl = add_query_arg([
    'gw_view' => 'Responsable',
    'gw_responsable_id' => $responsableId,
    'mode' => 'edit',
], home_url('/gestiwork/'));

$cancelEditUrl = add_query_arg([
    'gw_view' => 'Responsable',
    'gw_responsable_id' => $responsableId,
], home_url('/gestiwork/'));

$responsableDefaults = [
    'civilite' => '',
    'prenom' => '',
    'nom' => '',
    'fonction' => '',
    'email' => '',
    'telephone' => '',
    'role_type' => '',
    'sous_traitant' => '',
    'nda_sous_traitant' => '',
    'adresse_postale' => '',
    'rue' => '',
    'code_postal' => '',
    'ville' => '',
];

$responsable = null;
if ($isCreate) {
    $responsable = $responsableDefaults;
} elseif ($responsableId > 0) {
    $dbResponsable = ResponsableFormateurProvider::getById($responsableId);
    if (is_array($dbResponsable)) {
        $responsable = array_merge($responsableDefaults, $dbResponsable);
    }
}

$competences = [];
if ($responsableId > 0) {
    $competences = FormateurCompetenceProvider::getCompetencesByFormateurId($responsableId);
}

$couts = null;
if ($responsableId > 0) {
    $couts = FormateurCompetenceProvider::getCoutsByFormateurId($responsableId);
}

$responsableLabel = '';
if (is_array($responsable)) {
    $responsableLabel = trim((string) ($responsable['prenom'] ?? '') . ' ' . (string) ($responsable['nom'] ?? ''));
}

?>

<section class="gw-section gw-section-dashboard">
    <?php if (isset($_GET['gw_error']) && (string) $_GET['gw_error'] === 'validation') : ?>
        <div class="notice notice-error gw-notice-spacing">
            <p>
                <?php
                if (isset($_GET['gw_error_msg']) && $_GET['gw_error_msg'] !== '') {
                    echo esc_html((string) $_GET['gw_error_msg']);
                } else {
                    esc_html_e('Merci de vérifier les champs du formulaire.', 'gestiwork');
                }
                ?>
            </p>
        </div>
    <?php elseif (isset($_GET['gw_error']) && $_GET['gw_error'] !== '') : ?>
        <div class="notice notice-error gw-notice-spacing">
            <p><?php esc_html_e('Une erreur est survenue lors de l\'enregistrement.', 'gestiwork'); ?></p>
        </div>
    <?php endif; ?>
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
            <?php if (! $isEdit && ! $isCreate && $responsableId > 0) : ?>
                <a class="gw-button gw-button--primary" href="<?php echo esc_url($editUrl); ?>">
                    <?php esc_html_e('Modifier', 'gestiwork'); ?>
                </a>
                <form method="post" action="" class="gw-form-inline">
                    <input type="hidden" name="gw_action" value="gw_formateur_delete" />
                    <input type="hidden" name="responsable_id" value="<?php echo (int) $responsableId; ?>" />
                    <?php wp_nonce_field('gw_formateur_delete', 'gw_nonce'); ?>
                    <button type="submit" class="gw-button gw-button--secondary gw-formateur-delete gw-delete-button">
                        <span class="dashicons dashicons-trash" aria-hidden="true"></span>
                        <?php esc_html_e('Supprimer', 'gestiwork'); ?>
                    </button>
                </form>
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
                        <form method="post" action="" class="gw-mt-12" id="gw-responsable-form">
                            <input type="hidden" name="gw_action" value="<?php echo $isCreate ? 'gw_formateur_create' : 'gw_formateur_update'; ?>" />
                            <?php if (! $isCreate && $responsableId > 0) : ?>
                                <input type="hidden" name="responsable_id" value="<?php echo (int) $responsableId; ?>" />
                            <?php endif; ?>
                            <?php wp_nonce_field('gw_formateur_manage', 'gw_nonce'); ?>

                            <p class="gw-modal-required-info">Merci de renseigner tous les champs obligatoires marqués d’une astérisque rouge (*).</p>

                            <!-- Type de membre -->
                            <div class="gw-full-width gw-mb-8">
                                <label class="gw-settings-placeholder"><?php esc_html_e('Type de membre', 'gestiwork'); ?> <span class="gw-required-asterisk">*</span></label>
                                <div class="gw-radio-group" style="margin-top: 6px; display: flex; gap: 20px; flex-wrap: wrap;">
                                    <?php $roleType = isset($responsable['role_type']) ? (string) $responsable['role_type'] : ''; ?>
                                    <label style="display: flex; align-items: center; gap: 6px; font-size: 13px;">
                                        <input type="radio" name="role_type" value="responsable_pedagogique"<?php echo $roleType === 'responsable_pedagogique' ? ' checked' : ''; ?><?php echo ($isCreate || $isEdit) ? '' : ' disabled'; ?> />
                                        <?php esc_html_e('Responsable pédagogique', 'gestiwork'); ?>
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 6px; font-size: 13px;">
                                        <input type="radio" name="role_type" value="formateur"<?php echo $roleType === 'formateur' ? ' checked' : ''; ?><?php echo ($isCreate || $isEdit) ? '' : ' disabled'; ?> />
                                        <?php esc_html_e('Formateur', 'gestiwork'); ?>
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 6px; font-size: 13px;">
                                        <input type="radio" name="role_type" value="les_deux"<?php echo $roleType === 'les_deux' ? ' checked' : ''; ?><?php echo ($isCreate || $isEdit) ? '' : ' disabled'; ?> />
                                        <?php esc_html_e('Les deux', 'gestiwork'); ?>
                                    </label>
                                </div>
                            </div>

                            <div class="gw-grid-2">
                                <div>
                                    <label class="gw-settings-placeholder" for="gw_responsable_civilite"><?php esc_html_e('Civilité', 'gestiwork'); ?> <span class="gw-required-asterisk">*</span></label>
                                    <?php $civilite = isset($responsable['civilite']) ? (string) $responsable['civilite'] : ''; ?>
                                    <select id="gw_responsable_civilite" name="civilite" class="gw-modal-input"<?php echo ($isCreate || $isEdit) ? '' : ' disabled'; ?>>
                                        <option value=""<?php echo $civilite === '' ? ' selected' : ''; ?>><?php esc_html_e('Non renseigné', 'gestiwork'); ?></option>
                                        <option value="Madame"<?php echo $civilite === 'Madame' ? ' selected' : ''; ?>><?php esc_html_e('Madame', 'gestiwork'); ?></option>
                                        <option value="Monsieur"<?php echo $civilite === 'Monsieur' ? ' selected' : ''; ?>><?php esc_html_e('Monsieur', 'gestiwork'); ?></option>
                                    </select>
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_responsable_prenom"><?php esc_html_e('Prénom', 'gestiwork'); ?> <span class="gw-required-asterisk">*</span></label>
                                    <input type="text" id="gw_responsable_prenom" name="prenom" class="gw-modal-input" value="<?php echo esc_attr((string) ($responsable['prenom'] ?? '')); ?>"<?php echo ($isCreate || $isEdit) ? '' : ' disabled'; ?> />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_responsable_nom"><?php esc_html_e('Nom', 'gestiwork'); ?> <span class="gw-required-asterisk">*</span></label>
                                    <input type="text" id="gw_responsable_nom" name="nom" class="gw-modal-input" value="<?php echo esc_attr((string) ($responsable['nom'] ?? '')); ?>"<?php echo ($isCreate || $isEdit) ? '' : ' disabled'; ?> />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_responsable_fonction"><?php esc_html_e('Fonction', 'gestiwork'); ?> <span class="gw-required-asterisk">*</span></label>
                                    <input type="text" id="gw_responsable_fonction" name="fonction" class="gw-modal-input" value="<?php echo esc_attr((string) ($responsable['fonction'] ?? '')); ?>"<?php echo ($isCreate || $isEdit) ? '' : ' disabled'; ?> />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_responsable_email"><?php esc_html_e('Adresse e-mail', 'gestiwork'); ?> <span class="gw-required-asterisk">*</span></label>
                                    <input type="email" id="gw_responsable_email" name="email" class="gw-modal-input" value="<?php echo esc_attr((string) ($responsable['email'] ?? '')); ?>"<?php echo ($isCreate || $isEdit) ? '' : ' disabled'; ?> />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_responsable_telephone"><?php esc_html_e('Numéro de téléphone', 'gestiwork'); ?> <span class="gw-required-asterisk">*</span></label>
                                    <input type="tel" id="gw_responsable_telephone" name="telephone" class="gw-modal-input" value="<?php echo esc_attr((string) ($responsable['telephone'] ?? '')); ?>" pattern="[0-9]{2}( [0-9]{2}){4}" placeholder="00 00 00 00 00"<?php echo ($isCreate || $isEdit) ? '' : ' disabled'; ?> />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_responsable_sous_traitant"><?php esc_html_e('Sous-traitant', 'gestiwork'); ?> <span class="gw-required-asterisk">*</span></label>
                                    <?php $sousTraitant = isset($responsable['sous_traitant']) ? (string) $responsable['sous_traitant'] : ''; ?>
                                    <select id="gw_responsable_sous_traitant" name="sous_traitant" class="gw-modal-input"<?php echo ($isCreate || $isEdit) ? '' : ' disabled'; ?>>
                                        <option value=""<?php echo $sousTraitant === '' ? ' selected' : ''; ?>><?php esc_html_e('Sélectionner', 'gestiwork'); ?></option>
                                        <option value="Non"<?php echo $sousTraitant === 'Non' ? ' selected' : ''; ?>><?php esc_html_e('Non', 'gestiwork'); ?></option>
                                        <option value="Oui"<?php echo $sousTraitant === 'Oui' ? ' selected' : ''; ?>><?php esc_html_e('Oui', 'gestiwork'); ?></option>
                                    </select>
                                </div>

                                <div id="gw_responsable_field_nda_sous_traitant">
                                    <label class="gw-settings-placeholder" for="gw_responsable_nda_sous_traitant"><?php esc_html_e('NDA de l’organisme du sous-traitant', 'gestiwork'); ?> <span class="gw-required-asterisk">*</span></label>
                                    <input type="text" id="gw_responsable_nda_sous_traitant" name="nda_sous_traitant" class="gw-modal-input" value="<?php echo esc_attr((string) ($responsable['nda_sous_traitant'] ?? '')); ?>"<?php echo ($isCreate || $isEdit) ? '' : ' disabled'; ?> />
                                </div>

                                <div class="gw-full-width">
                                    <label class="gw-settings-placeholder" for="gw_responsable_adresse_postale"><?php esc_html_e('Adresse (ligne 1)', 'gestiwork'); ?> <span class="gw-required-asterisk">*</span></label>
                                    <input type="text" id="gw_responsable_adresse_postale" name="adresse_postale" class="gw-modal-input" value="<?php echo esc_attr((string) ($responsable['adresse_postale'] ?? '')); ?>"<?php echo ($isCreate || $isEdit) ? '' : ' disabled'; ?> />
                                </div>

                                <div class="gw-full-width">
                                    <label class="gw-settings-placeholder" for="gw_responsable_rue"><?php esc_html_e('Adresse (ligne 2)', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_responsable_rue" name="rue" class="gw-modal-input" value="<?php echo esc_attr((string) ($responsable['rue'] ?? '')); ?>"<?php echo ($isCreate || $isEdit) ? '' : ' disabled'; ?> />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_responsable_code_postal"><?php esc_html_e('Code postal', 'gestiwork'); ?> <span class="gw-required-asterisk">*</span></label>
                                    <input type="text" id="gw_responsable_code_postal" name="code_postal" class="gw-modal-input" value="<?php echo esc_attr((string) ($responsable['code_postal'] ?? '')); ?>" maxlength="5" pattern="[0-9]{5}" inputmode="numeric"<?php echo ($isCreate || $isEdit) ? '' : ' disabled'; ?> />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_responsable_ville"><?php esc_html_e('Ville', 'gestiwork'); ?> <span class="gw-required-asterisk">*</span></label>
                                    <input type="text" id="gw_responsable_ville" name="ville" class="gw-modal-input" value="<?php echo esc_attr((string) ($responsable['ville'] ?? '')); ?>"<?php echo ($isCreate || $isEdit) ? '' : ' disabled'; ?> />
                                </div>

                                <div class="gw-full-width">
                                    <p id="gw_responsable_error" class="gw-modal-error-info gw-display-none"></p>
                                </div>


                                <?php if ($isCreate || $isEdit) : ?>
                                    <div class="gw-full-width gw-mt-6 gw-flex-end">
                                        <?php if ($isEdit) : ?>
                                            <a class="gw-button gw-button--secondary" href="<?php echo esc_url($cancelEditUrl); ?>">
                                                <?php esc_html_e('Annuler', 'gestiwork'); ?>
                                            </a>
                                        <?php endif; ?>
                                        <button type="submit" class="gw-button gw-button--primary">
                                            <?php esc_html_e('Enregistrer', 'gestiwork'); ?>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>

                <div class="gw-grid-layout">
                    <div class="gw-card">
                        <h3 class="gw-section-subtitle gw-m-0"><?php esc_html_e('Aucune tâche programmée', 'gestiwork'); ?></h3>
                        <p class="gw-section-description gw-mt-6"><?php esc_html_e('Aucune tâche planifiée pour ce membre.', 'gestiwork'); ?></p>
                        <div class="gw-mt-6">
                            <a class="gw-link-button" href="#"><?php esc_html_e('Voir les tâches', 'gestiwork'); ?></a>
                        </div>
                    </div>

                    <div class="gw-card">
                        <div class="gw-flex-between-center">
                            <h3 class="gw-section-subtitle gw-m-0"><?php esc_html_e('Compétences', 'gestiwork'); ?></h3>
                            <?php if ($isEdit && $responsableId > 0) : ?>
                                <button type="button" class="gw-link-button" data-gw-modal-target="gw-modal-competences" style="text-decoration:none; font-size:13px; display:inline-flex; align-items:center; gap:6px;">
                                    <span class="dashicons dashicons-plus" aria-hidden="true"></span>
                                    <?php esc_html_e('Gérer les compétences', 'gestiwork'); ?>
                                </button>
                            <?php endif; ?>
                        </div>
                        <?php if ($isCreate) : ?>
                            <div style="margin-top: 10px;">
                                <label class="gw-settings-placeholder" for="gw_responsable_competences_inline"><?php esc_html_e('Compétences (séparées par des virgules)', 'gestiwork'); ?></label>
                                <textarea id="gw_responsable_competences_inline" name="competences" class="gw-modal-textarea" rows="3" form="gw-responsable-form" placeholder="<?php esc_attr_e('Ex: Management, Bureautique, Formation professionnelle', 'gestiwork'); ?>"></textarea>
                            </div>
                        <?php elseif (is_array($competences) && count($competences) > 0) : ?>
                            <div style="margin-top: 10px; display: flex; gap: 6px; flex-wrap: wrap;">
                                <?php foreach ($competences as $competence) : ?>
                                    <span class="gw-tag gw-tag--soft">
                                        <?php echo esc_html($competence); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php else : ?>
                            <p class="gw-section-description gw-mt-6"><?php esc_html_e('Aucune compétence renseignée.', 'gestiwork'); ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="gw-card">
                        <div class="gw-flex-between-center">
                            <h3 class="gw-section-subtitle gw-m-0"><?php esc_html_e('Coût du formateur', 'gestiwork'); ?></h3>
                            <?php if ($isEdit && $responsableId > 0) : ?>
                                <button type="button" class="gw-link-button" data-gw-modal-target="gw-modal-cout-formateur" style="text-decoration:none; font-size:13px; display:inline-flex; align-items:center; gap:6px;">
                                    <span class="dashicons dashicons-edit" aria-hidden="true"></span>
                                    <?php esc_html_e('Modifier', 'gestiwork'); ?>
                                </button>
                            <?php endif; ?>
                        </div>

                        <?php
                        $coutJour = null;
                        $coutHeure = null;
                        $tvaRate = null;
                        if (is_array($couts)) {
                            $coutJour = array_key_exists('cout_jour_ht', $couts) ? $couts['cout_jour_ht'] : null;
                            $coutHeure = array_key_exists('cout_heure_ht', $couts) ? $couts['cout_heure_ht'] : null;
                            $tvaRate = array_key_exists('tva_rate', $couts) ? $couts['tva_rate'] : null;
                        }
                        ?>

                        <?php if ($isCreate) : ?>
                            <div style="margin-top: 12px; display:grid; gap:10px;">
                                <div>
                                    <label class="gw-settings-placeholder" for="gw_responsable_heures_jour_inline"><?php esc_html_e('Heures par jour', 'gestiwork'); ?></label>
                                    <input type="number" step="0.5" min="1" max="12" id="gw_responsable_heures_jour_inline" name="heures_par_jour" class="gw-modal-input gw-heures-jour" form="gw-responsable-form" value="7" data-cout-jour-target="gw_responsable_cout_jour_inline" data-cout-heure-target="gw_responsable_cout_heure_inline" />
                                </div>
                                <div>
                                    <label class="gw-settings-placeholder" for="gw_responsable_cout_jour_inline"><?php esc_html_e('Coût journée (HT)', 'gestiwork'); ?></label>
                                    <input type="number" step="0.01" min="0" id="gw_responsable_cout_jour_inline" name="cout_jour_ht" class="gw-modal-input gw-cout-jour" form="gw-responsable-form" value="" data-cout-heure-target="gw_responsable_cout_heure_inline" data-heures-jour-source="gw_responsable_heures_jour_inline" />
                                </div>
                                <div>
                                    <label class="gw-settings-placeholder" for="gw_responsable_cout_heure_inline"><?php esc_html_e('Coût heure (HT)', 'gestiwork'); ?></label>
                                    <input type="number" step="0.01" min="0" id="gw_responsable_cout_heure_inline" name="cout_heure_ht" class="gw-modal-input gw-cout-heure" form="gw-responsable-form" value="" data-cout-jour-target="gw_responsable_cout_jour_inline" data-heures-jour-source="gw_responsable_heures_jour_inline" />
                                </div>
                                <div>
                                    <label class="gw-settings-placeholder" for="gw_responsable_tva_inline"><?php esc_html_e('Taux TVA', 'gestiwork'); ?></label>
                                    <select id="gw_responsable_tva_inline" name="tva_rate" class="gw-modal-input" form="gw-responsable-form">
                                        <option value="0" selected><?php esc_html_e('Non soumis (art. 293 B du CGI)', 'gestiwork'); ?></option>
                                        <option value="20"><?php esc_html_e('20% (taux normal)', 'gestiwork'); ?></option>
                                        <option value="10"><?php esc_html_e('10% (taux intermédiaire)', 'gestiwork'); ?></option>
                                        <option value="5.5"><?php esc_html_e('5,5% (taux réduit)', 'gestiwork'); ?></option>
                                        <option value="2.1"><?php esc_html_e('2,1% (taux particulier)', 'gestiwork'); ?></option>
                                    </select>
                                </div>
                            </div>
                        <?php else : ?>
                            <div style="margin-top: 12px;">
                                <div style="font-size: 22px; font-weight: 700; color: var(--gw-color-text);">
                                    <?php
                                    if ($coutJour !== null && $coutJour !== '') {
                                        echo esc_html(number_format((float) $coutJour, 2, ',', ' ')) . ' ' . esc_html__('€ HT / jour', 'gestiwork');
                                    } else {
                                        echo esc_html__('—', 'gestiwork');
                                    }
                                    ?>
                                </div>
                                <div class="gw-section-description" style="margin-top:6px;">
                                    <?php
                                    if ($coutHeure !== null && $coutHeure !== '') {
                                        echo '(' . esc_html(number_format((float) $coutHeure, 2, ',', ' ')) . ' ' . esc_html__('€ HT / heure', 'gestiwork') . ')';
                                    } else {
                                        echo esc_html__('(coût horaire non défini)', 'gestiwork');
                                    }
                                    ?>
                                </div>
                                <div class="gw-section-description" style="margin-top:6px;">
                                    <?php
                                    if ($tvaRate !== null && $tvaRate !== '') {
                                        echo esc_html__('Taux de TVA : ', 'gestiwork') . esc_html(number_format((float) $tvaRate, 2, ',', ' ')) . '%';
                                    } else {
                                        echo esc_html__('Taux de TVA : non défini', 'gestiwork');
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modale Gestion des compétences -->
<div class="gw-modal-backdrop" id="gw-modal-competences" aria-hidden="true">
    <div class="gw-modal" role="dialog" aria-modal="true" aria-labelledby="gw-modal-competences-title">
        <div class="gw-modal-header">
            <h3 class="gw-modal-title" id="gw-modal-competences-title"><?php esc_html_e('Gérer les compétences', 'gestiwork'); ?></h3>
            <button type="button" class="gw-modal-close" data-gw-modal-close="gw-modal-competences" aria-label="<?php esc_attr_e('Fermer', 'gestiwork'); ?>">&times;</button>
        </div>
        <div class="gw-modal-body">
            <form method="post" action="">
                <input type="hidden" name="gw_action" value="gw_formateur_competences" />
                <input type="hidden" name="responsable_id" value="<?php echo (int) $responsableId; ?>" />
                <?php wp_nonce_field('gw_formateur_competences', 'gw_nonce'); ?>
                
                <div class="gw-modal-field">
                    <label for="gw_competences_input"><?php esc_html_e('Compétences (séparées par des virgules)', 'gestiwork'); ?></label>
                    <textarea id="gw_competences_input" name="competences" class="gw-modal-textarea" rows="4" placeholder="<?php esc_attr_e('Ex: Management, Bureautique, Formation professionnelle', 'gestiwork'); ?>"><?php echo esc_textarea(implode(', ', $competences)); ?></textarea>
                    <p class="gw-section-description" style="margin-top:6px;"><?php esc_html_e('Séparez chaque compétence par une virgule.', 'gestiwork'); ?></p>
                </div>
                
                <div class="gw-modal-footer">
                    <button type="button" class="gw-button gw-button--secondary" data-gw-modal-close="gw-modal-competences"><?php esc_html_e('Annuler', 'gestiwork'); ?></button>
                    <button type="submit" class="gw-button gw-button--primary"><?php esc_html_e('Enregistrer', 'gestiwork'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modale Coût du formateur -->
<div class="gw-modal-backdrop" id="gw-modal-cout-formateur" aria-hidden="true">
    <div class="gw-modal" role="dialog" aria-modal="true" aria-labelledby="gw-modal-cout-formateur-title">
        <div class="gw-modal-header">
            <h3 class="gw-modal-title" id="gw-modal-cout-formateur-title"><?php esc_html_e('Définir le coût formateur', 'gestiwork'); ?></h3>
            <button type="button" class="gw-modal-close" data-gw-modal-close="gw-modal-cout-formateur" aria-label="<?php esc_attr_e('Fermer', 'gestiwork'); ?>">&times;</button>
        </div>
        <div class="gw-modal-body">
            <form method="post" action="">
                <input type="hidden" name="gw_action" value="gw_formateur_cout" />
                <input type="hidden" name="responsable_id" value="<?php echo (int) $responsableId; ?>" />
                <?php wp_nonce_field('gw_formateur_cout', 'gw_nonce'); ?>
                
                <?php
                $currentTvaRate = $tvaRate !== null ? (string) $tvaRate : '0';
                $currentCoutJour = $coutJour !== null ? number_format((float) $coutJour, 2, '.', '') : '';
                $currentCoutHeure = $coutHeure !== null ? number_format((float) $coutHeure, 2, '.', '') : '';
                $heuresParJour = is_array($couts) && array_key_exists('heures_par_jour', $couts) ? (float) $couts['heures_par_jour'] : 7.00;
                ?>
                <div class="gw-modal-grid">
                    <div class="gw-modal-field">
                        <label for="gw_heures_par_jour"><?php esc_html_e('Heures par jour', 'gestiwork'); ?></label>
                        <input type="number" step="0.5" min="1" max="12" id="gw_heures_par_jour" name="heures_par_jour" class="gw-modal-input gw-heures-jour" value="<?php echo esc_attr(number_format($heuresParJour, 1, '.', '')); ?>" data-cout-jour-target="gw_cout_jour_ht" data-cout-heure-target="gw_cout_heure_ht" />
                    </div>
                    
                    <div class="gw-modal-field">
                        <label for="gw_cout_jour_ht"><?php esc_html_e('Coût journée (HT)', 'gestiwork'); ?></label>
                        <input type="number" step="0.01" min="0" id="gw_cout_jour_ht" name="cout_jour_ht" class="gw-modal-input gw-cout-jour" value="<?php echo esc_attr($currentCoutJour); ?>" data-cout-heure-target="gw_cout_heure_ht" data-heures-jour-source="gw_heures_par_jour" />
                    </div>
                    
                    <div class="gw-modal-field">
                        <label for="gw_cout_heure_ht"><?php esc_html_e('Coût heure (HT)', 'gestiwork'); ?></label>
                        <input type="number" step="0.01" min="0" id="gw_cout_heure_ht" name="cout_heure_ht" class="gw-modal-input gw-cout-heure" value="<?php echo esc_attr($currentCoutHeure); ?>" data-cout-jour-target="gw_cout_jour_ht" data-heures-jour-source="gw_heures_par_jour" />
                    </div>
                    
                    <div class="gw-modal-field">
                        <label for="gw_tva_rate"><?php esc_html_e('Taux TVA', 'gestiwork'); ?></label>
                        <select id="gw_tva_rate" name="tva_rate" class="gw-modal-input">
                            <option value="0"<?php echo $currentTvaRate === '0' ? ' selected' : ''; ?>><?php esc_html_e('Non soumis (art. 293 B du CGI)', 'gestiwork'); ?></option>
                            <option value="20"<?php echo $currentTvaRate === '20' || $currentTvaRate === '20.00' ? ' selected' : ''; ?>><?php esc_html_e('20% (taux normal)', 'gestiwork'); ?></option>
                            <option value="10"<?php echo $currentTvaRate === '10' || $currentTvaRate === '10.00' ? ' selected' : ''; ?>><?php esc_html_e('10% (taux intermédiaire)', 'gestiwork'); ?></option>
                            <option value="5.5"<?php echo $currentTvaRate === '5.5' || $currentTvaRate === '5.50' ? ' selected' : ''; ?>><?php esc_html_e('5,5% (taux réduit)', 'gestiwork'); ?></option>
                            <option value="2.1"<?php echo $currentTvaRate === '2.1' || $currentTvaRate === '2.10' ? ' selected' : ''; ?>><?php esc_html_e('2,1% (taux particulier)', 'gestiwork'); ?></option>
                        </select>
                    </div>
                </div>
                
                <div class="gw-modal-footer">
                    <button type="button" class="gw-button gw-button--secondary" data-gw-modal-close="gw-modal-cout-formateur"><?php esc_html_e('Annuler', 'gestiwork'); ?></button>
                    <button type="submit" class="gw-button gw-button--primary"><?php esc_html_e('Enregistrer', 'gestiwork'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
