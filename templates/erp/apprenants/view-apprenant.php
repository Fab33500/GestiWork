<?php

declare(strict_types=1);

use GestiWork\Domain\Apprenant\ApprenantProvider;
use GestiWork\Domain\Tiers\TierProvider;

if (! current_user_can('manage_options')) {
    wp_die(esc_html__('Accès non autorisé.', 'gestiwork'), 403);
}

$apprenantId = isset($_GET['gw_apprenant_id']) ? (int) $_GET['gw_apprenant_id'] : 0;
$mode = isset($_GET['mode']) ? (string) $_GET['mode'] : '';

$isCreate = ($mode === 'create');
$isEdit = ($mode === 'edit');

$backUrl = home_url('/gestiwork/apprenants/');

$editUrl = add_query_arg([
    'gw_view' => 'Apprenant',
    'gw_apprenant_id' => $apprenantId,
    'mode' => 'edit',
], home_url('/gestiwork/'));

$cancelEditUrl = add_query_arg([
    'gw_view' => 'Apprenant',
    'gw_apprenant_id' => $apprenantId,
], home_url('/gestiwork/'));

$apprenantDefaults = [
    'civilite' => '',
    'prenom' => '',
    'nom' => '',
    'nom_naissance' => '',
    'date_naissance' => '',
    'email' => '',
    'telephone' => '',
    'origine' => '',
    'statut_bpf' => '',
    'adresse1' => '',
    'adresse2' => '',
    'cp' => '',
    'ville' => '',
    'entreprise_id' => 0,
];

$apprenant = null;
if ($isCreate) {
    $apprenant = $apprenantDefaults;
} elseif ($apprenantId > 0) {
    $dbApprenant = ApprenantProvider::getById($apprenantId);
    if (is_array($dbApprenant)) {
        $apprenant = array_merge($apprenantDefaults, $dbApprenant);
    }
}

$entreprisesAssociees = [];
if (is_array($apprenant)) {
    $entrepriseId = isset($apprenant['entreprise_id']) ? (int) $apprenant['entreprise_id'] : 0;
    if ($entrepriseId > 0) {
        $tier = TierProvider::getById($entrepriseId);
        if (is_array($tier)) {
            $entreprisesAssociees[] = [
                'id' => $entrepriseId,
                'nom' => (string) ($tier['raison_sociale'] ?? ''),
                'adresse' => (string) ($tier['adresse1'] ?? ''),
                'ville' => (string) ($tier['ville'] ?? ''),
                'telephone' => (string) ($tier['telephone'] ?? ''),
                'email' => (string) ($tier['email'] ?? ''),
                'url' => '#',
            ];
        }
    }
}

$entrepriseLabel = '';
if (count($entreprisesAssociees) > 0) {
    $entrepriseLabel = isset($entreprisesAssociees[0]['nom']) ? (string) $entreprisesAssociees[0]['nom'] : '';
}

$apprenantLabel = '';
if (is_array($apprenant)) {
    $apprenantLabel = trim((string) ($apprenant['prenom'] ?? '') . ' ' . (string) ($apprenant['nom'] ?? ''));
}

?>

<section class="gw-section gw-section-dashboard">
    <div class="gw-flex-between">
        <div>
            <h2 class="gw-section-title"><?php esc_html_e('Fiche apprenant', 'gestiwork'); ?></h2>
            <?php if ($isCreate) : ?>
                <p class="gw-section-description">
                    <?php esc_html_e('Créer un nouvel apprenant', 'gestiwork'); ?>
                </p>
            <?php elseif ($apprenantLabel !== '') : ?>
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
            <?php if (! $isEdit && ! $isCreate && $apprenantId > 0) : ?>
                <a class="gw-button gw-button--primary" href="<?php echo esc_url($editUrl); ?>">
                    <?php esc_html_e('Modifier', 'gestiwork'); ?>
                </a>
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

                    <?php if (! is_array($apprenant)) : ?>
                        <p class="gw-section-description gw-mt-12">
                            <?php esc_html_e('Sélectionnez un apprenant depuis la liste pour afficher sa fiche.', 'gestiwork'); ?>
                        </p>
                    <?php else : ?>
                        <form method="post" action="" class="gw-mt-12" id="gw-apprenant-form">
                            <input type="hidden" name="gw_action" value="<?php echo $isCreate ? 'gw_apprenant_create' : 'gw_apprenant_update'; ?>" />
                            <?php if (! $isCreate && $apprenantId > 0) : ?>
                                <input type="hidden" name="apprenant_id" value="<?php echo (int) $apprenantId; ?>" />
                            <?php endif; ?>
                            <?php wp_nonce_field('gw_apprenant_manage', 'gw_nonce'); ?>

                            <div class="gw-grid-2">
                                <div>
                                    <label class="gw-settings-placeholder" for="gw_apprenant_civilite"><?php esc_html_e('Civilité', 'gestiwork'); ?></label>
                                    <select id="gw_apprenant_civilite" name="civilite" class="gw-modal-input"<?php echo ($isCreate || $isEdit) ? '' : ' disabled'; ?>>
                                        <?php $civilite = isset($apprenant['civilite']) ? (string) $apprenant['civilite'] : ''; ?>
                                        <option value=""<?php echo $civilite === '' ? ' selected' : ''; ?>><?php esc_html_e('Non renseigné', 'gestiwork'); ?></option>
                                        <option value="Madame"<?php echo $civilite === 'Madame' ? ' selected' : ''; ?>><?php esc_html_e('Madame', 'gestiwork'); ?></option>
                                        <option value="Monsieur"<?php echo $civilite === 'Monsieur' ? ' selected' : ''; ?>><?php esc_html_e('Monsieur', 'gestiwork'); ?></option>
                                    </select>
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_apprenant_prenom"><?php esc_html_e('Prénom', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_apprenant_prenom" name="prenom" class="gw-modal-input" value="<?php echo esc_attr((string) ($apprenant['prenom'] ?? '')); ?>"<?php echo ($isCreate || $isEdit) ? '' : ' disabled'; ?> />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_apprenant_nom"><?php esc_html_e('Nom', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_apprenant_nom" name="nom" class="gw-modal-input" value="<?php echo esc_attr((string) ($apprenant['nom'] ?? '')); ?>"<?php echo ($isCreate || $isEdit) ? '' : ' disabled'; ?> />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_apprenant_nom_naissance"><?php esc_html_e('Nom de naissance', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_apprenant_nom_naissance" name="nom_naissance" class="gw-modal-input" value="<?php echo esc_attr((string) ($apprenant['nom_naissance'] ?? '')); ?>"<?php echo ($isCreate || $isEdit) ? '' : ' disabled'; ?> />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_apprenant_date_naissance"><?php esc_html_e('Date de naissance', 'gestiwork'); ?></label>
                                    <input type="date" id="gw_apprenant_date_naissance" name="date_naissance" class="gw-modal-input" value="<?php echo esc_attr((string) ($apprenant['date_naissance'] ?? '')); ?>"<?php echo ($isCreate || $isEdit) ? '' : ' disabled'; ?> />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_apprenant_telephone"><?php esc_html_e('Numéro de téléphone', 'gestiwork'); ?></label>
                                    <input type="tel" id="gw_apprenant_telephone" name="telephone" class="gw-modal-input" value="<?php echo esc_attr((string) ($apprenant['telephone'] ?? '')); ?>"<?php echo ($isCreate || $isEdit) ? '' : ' disabled'; ?> />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_apprenant_email"><?php esc_html_e('Adresse e-mail', 'gestiwork'); ?></label>
                                    <input type="email" id="gw_apprenant_email" name="email" class="gw-modal-input" value="<?php echo esc_attr((string) ($apprenant['email'] ?? '')); ?>"<?php echo ($isCreate || $isEdit) ? '' : ' disabled'; ?> />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_apprenant_entreprise"><?php esc_html_e('Entreprise', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_apprenant_entreprise" name="entreprise" class="gw-modal-input" value="<?php echo esc_attr($entrepriseLabel); ?>" disabled />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_apprenant_origine"><?php esc_html_e('Origine', 'gestiwork'); ?></label>
                                    <?php $origine = isset($apprenant['origine']) ? (string) $apprenant['origine'] : ''; ?>
                                    <select id="gw_apprenant_origine" name="origine" class="gw-modal-input"<?php echo ($isCreate || $isEdit) ? '' : ' disabled'; ?>>
                                        <option value=""<?php echo $origine === '' ? ' selected' : ''; ?>><?php esc_html_e('Sélectionner', 'gestiwork'); ?></option>
                                        <option value="Campagne"<?php echo $origine === 'Campagne' ? ' selected' : ''; ?>><?php esc_html_e('Campagne', 'gestiwork'); ?></option>
                                        <option value="France travail"<?php echo $origine === 'France travail' ? ' selected' : ''; ?>><?php esc_html_e('France travail', 'gestiwork'); ?></option>
                                        <option value="Réseaux sociaux"<?php echo $origine === 'Réseaux sociaux' ? ' selected' : ''; ?>><?php esc_html_e('Réseaux sociaux', 'gestiwork'); ?></option>
                                    </select>
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_apprenant_statut_bpf"><?php esc_html_e('Statut BPF', 'gestiwork'); ?></label>
                                    <?php $statutBpf = isset($apprenant['statut_bpf']) ? (string) $apprenant['statut_bpf'] : ''; ?>
                                    <select id="gw_apprenant_statut_bpf" name="statut_bpf" class="gw-modal-input"<?php echo ($isCreate || $isEdit) ? '' : ' disabled'; ?>>
                                        <option value=""<?php echo $statutBpf === '' ? ' selected' : ''; ?>><?php esc_html_e('Non renseigné', 'gestiwork'); ?></option>
                                        <option value="ex1"<?php echo $statutBpf === 'ex1' ? ' selected' : ''; ?>><?php esc_html_e('ex1', 'gestiwork'); ?></option>
                                        <option value="ex2"<?php echo $statutBpf === 'ex2' ? ' selected' : ''; ?>><?php esc_html_e('ex2', 'gestiwork'); ?></option>
                                        <option value="ex3"<?php echo $statutBpf === 'ex3' ? ' selected' : ''; ?>><?php esc_html_e('ex3', 'gestiwork'); ?></option>
                                    </select>
                                </div>

                                <div class="gw-full-width">
                                    <label class="gw-settings-placeholder" for="gw_apprenant_adresse1"><?php esc_html_e('Adresse (ligne 1)', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_apprenant_adresse1" name="adresse1" class="gw-modal-input" value="<?php echo esc_attr((string) ($apprenant['adresse1'] ?? '')); ?>"<?php echo ($isCreate || $isEdit) ? '' : ' disabled'; ?> />
                                </div>

                                <div class="gw-full-width">
                                    <label class="gw-settings-placeholder" for="gw_apprenant_adresse2"><?php esc_html_e('Adresse (ligne 2)', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_apprenant_adresse2" name="adresse2" class="gw-modal-input" value="<?php echo esc_attr((string) ($apprenant['adresse2'] ?? '')); ?>"<?php echo ($isCreate || $isEdit) ? '' : ' disabled'; ?> />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_apprenant_cp"><?php esc_html_e('Code postal', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_apprenant_cp" name="cp" class="gw-modal-input" value="<?php echo esc_attr((string) ($apprenant['cp'] ?? '')); ?>" maxlength="5" pattern="[0-9]{5}" inputmode="numeric"<?php echo ($isCreate || $isEdit) ? '' : ' disabled'; ?> />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_apprenant_ville"><?php esc_html_e('Ville', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_apprenant_ville" name="ville" class="gw-modal-input" value="<?php echo esc_attr((string) ($apprenant['ville'] ?? '')); ?>"<?php echo ($isCreate || $isEdit) ? '' : ' disabled'; ?> />
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
                        <p class="gw-section-description gw-mt-6"><?php esc_html_e('Données fictives : 0 tâche planifiée pour cet apprenant.', 'gestiwork'); ?></p>
                        <div class="gw-mt-6">
                            <a class="gw-link-button" href="#"><?php esc_html_e('Voir les tâches', 'gestiwork'); ?></a>
                        </div>
                    </div>

                    <div class="gw-card">
                        <div class="gw-flex-between-center">
                            <h3 class="gw-section-subtitle gw-m-0"><?php esc_html_e('Entreprises associées', 'gestiwork'); ?></h3>
                            <?php if ($isEdit && $apprenantId > 0) : ?>
                                <a href="#" onclick="return false;" data-gw-modal-target="gw-modal-associer-entreprise" style="text-decoration:none; font-size:13px;">
                                    <span class="dashicons dashicons-plus" aria-hidden="true"></span>
                                    <?php esc_html_e('Associer une entreprise', 'gestiwork'); ?>
                                </a>
                            <?php endif; ?>
                        </div>

                        <?php if ($isCreate) : ?>
                            <div style="margin-top: 10px; display:grid; gap:6px; font-size: 13px;">
                                <div>
                                    <label class="gw-settings-placeholder" for="gw_apprenant_entreprise_id"><?php esc_html_e('Sélectionner une entreprise', 'gestiwork'); ?></label>
                                    <select id="gw_apprenant_entreprise_id" name="entreprise_id" class="gw-modal-input" form="gw-apprenant-form">
                                        <option value=""><?php esc_html_e('-- Choisir une entreprise --', 'gestiwork'); ?></option>
                                        <?php
                                        $entreprisesSearch = TierProvider::search(['type' => 'entreprise'], 1, 200);
                                        $entreprisesDisponibles = isset($entreprisesSearch['items']) && is_array($entreprisesSearch['items']) ? $entreprisesSearch['items'] : [];
                                        foreach ($entreprisesDisponibles as $entreprise) :
                                            $entrepriseId = isset($entreprise['id']) ? (int) $entreprise['id'] : 0;
                                            $nom = isset($entreprise['raison_sociale']) ? trim((string) $entreprise['raison_sociale']) : '';
                                            if ($nom === '') {
                                                $nom = trim((string) ($entreprise['prenom'] ?? '') . ' ' . (string) ($entreprise['nom'] ?? ''));
                                            }
                                            if ($nom === '') {
                                                $nom = $entrepriseId > 0 ? (string) $entrepriseId : '-';
                                            }
                                        ?>
                                            <option value="<?php echo (int) $entrepriseId; ?>">
                                                <?php echo esc_html($nom); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <p class="gw-section-description" style="margin-top:6px;">
                                        <?php esc_html_e('L\'entreprise sélectionnée sera associée lors de l\'enregistrement.', 'gestiwork'); ?>
                                    </p>
                                </div>
                            </div>
                        <?php elseif (is_array($entreprisesAssociees) && count($entreprisesAssociees) > 0) : ?>
                            <div style="margin-top: 10px; display:grid; gap:6px; font-size: 13px;">
                                <?php foreach ($entreprisesAssociees as $entreprise) : ?>
                                    <?php
                                    $entrepriseId = isset($entreprise['id']) ? (int) $entreprise['id'] : 0;
                                    $entrepriseNom = isset($entreprise['nom']) ? (string) $entreprise['nom'] : '';
                                    $entrepriseAdresse = isset($entreprise['adresse']) ? (string) $entreprise['adresse'] : '';
                                    $entrepriseVille = isset($entreprise['ville']) ? (string) $entreprise['ville'] : '';
                                    $entrepriseTel = isset($entreprise['telephone']) ? (string) $entreprise['telephone'] : '';
                                    $entrepriseEmail = isset($entreprise['email']) ? (string) $entreprise['email'] : '';
                                    $entrepriseUrl = isset($entreprise['url']) ? (string) $entreprise['url'] : '#';
                                    $entrepriseAdresseComplete = trim($entrepriseAdresse . "\n" . $entrepriseVille);
                                    ?>
                                    <div style="border:1px solid var(--gw-color-border); border-radius:12px; padding:12px; background:#f6f7f7;">
                                        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:10px;">
                                            <div style="min-width:0;">
                                                <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                                                    <a href="<?php echo esc_url($entrepriseUrl); ?>" onclick="return false;" style="text-decoration:none; font-weight:600; color: var(--gw-color-primary);">
                                                        <?php echo esc_html($entrepriseNom !== '' ? $entrepriseNom : '-'); ?>
                                                    </a>
                                                </div>
                                            </div>

                                            <?php if ($isEdit && $apprenantId > 0) : ?>
                                                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; justify-content:flex-end;">
                                                    <button type="button" class="gw-link-button" style="font-size:13px; color:#d63638;" onclick="return false;">
                                                        <span class="dashicons dashicons-trash" aria-hidden="true" style="margin-right:4px;"></span>
                                                        <?php esc_html_e('Dissocier', 'gestiwork'); ?>
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div style="margin-top: 8px; display:grid; gap:6px; color: var(--gw-color-text);">
                                            <?php if (trim($entrepriseAdresseComplete) !== '') : ?>
                                                <div style="display:flex; align-items:flex-start; gap:8px;">
                                                    <span class="dashicons dashicons-location" aria-hidden="true" style="color: var(--gw-color-muted);"></span>
                                                    <span style="white-space:pre-line;"><?php echo esc_html($entrepriseAdresseComplete); ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (trim($entrepriseTel) !== '') : ?>
                                                <div style="display:flex; align-items:center; gap:8px;">
                                                    <span class="dashicons dashicons-phone" aria-hidden="true" style="color: var(--gw-color-muted);"></span>
                                                    <span><?php echo esc_html($entrepriseTel); ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (trim($entrepriseEmail) !== '') : ?>
                                                <div style="display:flex; align-items:center; gap:8px;">
                                                    <span class="dashicons dashicons-email" aria-hidden="true" style="color: var(--gw-color-muted);"></span>
                                                    <a href="mailto:<?php echo esc_attr($entrepriseEmail); ?>" onclick="return false;" style="text-decoration:none; color: var(--gw-color-primary);">
                                                        <?php echo esc_html($entrepriseEmail); ?>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else : ?>
                            <p class="gw-section-description gw-mt-6"><?php esc_html_e('Cet apprenant n\'a aucune entreprise associée.', 'gestiwork'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modale Associer une entreprise -->
<div class="gw-modal-backdrop" id="gw-modal-associer-entreprise" aria-hidden="true">
    <div class="gw-modal" role="dialog" aria-modal="true" aria-labelledby="gw-modal-associer-entreprise-title">
        <div class="gw-modal-header">
            <h3 class="gw-modal-title" id="gw-modal-associer-entreprise-title"><?php esc_html_e('Associer une entreprise', 'gestiwork'); ?></h3>
            <button type="button" class="gw-modal-close" data-gw-modal-close="gw-modal-associer-entreprise" aria-label="<?php esc_attr_e('Fermer', 'gestiwork'); ?>">&times;</button>
        </div>
        <div class="gw-modal-body">
            <form method="post" action="">
                <input type="hidden" name="gw_action" value="gw_apprenant_associer_entreprise" />
                <input type="hidden" name="apprenant_id" value="<?php echo (int) $apprenantId; ?>" />
                <?php wp_nonce_field('gw_apprenant_associer_entreprise', 'gw_nonce'); ?>
                
                <div class="gw-modal-field">
                    <label for="gw_entreprise_select"><?php esc_html_e('Sélectionner une entreprise', 'gestiwork'); ?></label>
                    <select id="gw_entreprise_select" name="entreprise_id" class="gw-modal-input" required>
                        <option value=""><?php esc_html_e('-- Choisir une entreprise --', 'gestiwork'); ?></option>
                        <?php
                        $entreprisesSearch = TierProvider::search(['type' => 'entreprise'], 1, 200);
                        $entreprisesDisponibles = isset($entreprisesSearch['items']) && is_array($entreprisesSearch['items']) ? $entreprisesSearch['items'] : [];
                        foreach ($entreprisesDisponibles as $entreprise) :
                            $entrepriseId = isset($entreprise['id']) ? (int) $entreprise['id'] : 0;
                            $nom = isset($entreprise['raison_sociale']) ? trim((string) $entreprise['raison_sociale']) : '';
                            if ($nom === '') {
                                $nom = trim((string) ($entreprise['prenom'] ?? '') . ' ' . (string) ($entreprise['nom'] ?? ''));
                            }
                            if ($nom === '') {
                                $nom = $entrepriseId > 0 ? (string) $entrepriseId : '-';
                            }
                        ?>
                            <option value="<?php echo (int) $entrepriseId; ?>">
                                <?php echo esc_html($nom); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="gw-section-description" style="margin-top:6px;"><?php esc_html_e('L\'apprenant sera associé à cette entreprise.', 'gestiwork'); ?></p>
                </div>
                
                <div class="gw-modal-footer">
                    <button type="button" class="gw-button gw-button--secondary" data-gw-modal-close="gw-modal-associer-entreprise"><?php esc_html_e('Annuler', 'gestiwork'); ?></button>
                    <button type="submit" class="gw-button gw-button--primary"><?php esc_html_e('Associer', 'gestiwork'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
