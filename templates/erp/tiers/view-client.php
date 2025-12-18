<?php
/**
 * GestiWork ERP - Fiche Client (Tiers)
 *
 * This file is part of GestiWork ERP.
 *
 * GestiWork ERP is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * GestiWork ERP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GestiWork ERP. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

use GestiWork\Domain\Apprenant\ApprenantProvider;
use GestiWork\Domain\Tiers\TierProvider;
use GestiWork\Domain\Tiers\TierContactProvider;
use GestiWork\Domain\Tiers\TierFinanceurProvider;

if (! current_user_can('manage_options')) {
    wp_die(esc_html__('Accès non autorisé.', 'gestiwork'), 403);
}

$clientId = isset($_GET['gw_tier_id']) ? (int) $_GET['gw_tier_id'] : 0;
$mode = isset($_GET['mode']) ? (string) $_GET['mode'] : '';

$isCreate = ($mode === 'create');
$isEdit = ($mode === 'edit');

$prefill = [];
if ($isCreate) {
    $prefill = [
        'type' => isset($_GET['gw_prefill_type']) ? sanitize_text_field(wp_unslash((string) $_GET['gw_prefill_type'])) : '',
        'statut' => isset($_GET['gw_prefill_statut']) ? sanitize_text_field(wp_unslash((string) $_GET['gw_prefill_statut'])) : '',
        'raison_sociale' => isset($_GET['gw_prefill_raison_sociale']) ? sanitize_text_field(wp_unslash((string) $_GET['gw_prefill_raison_sociale'])) : '',
        'nom' => isset($_GET['gw_prefill_nom']) ? sanitize_text_field(wp_unslash((string) $_GET['gw_prefill_nom'])) : '',
        'prenom' => isset($_GET['gw_prefill_prenom']) ? sanitize_text_field(wp_unslash((string) $_GET['gw_prefill_prenom'])) : '',
        'siret' => isset($_GET['gw_prefill_siret']) ? sanitize_text_field(wp_unslash((string) $_GET['gw_prefill_siret'])) : '',
        'forme_juridique' => isset($_GET['gw_prefill_forme_juridique']) ? sanitize_text_field(wp_unslash((string) $_GET['gw_prefill_forme_juridique'])) : '',
        'email' => isset($_GET['gw_prefill_email']) ? sanitize_email(wp_unslash((string) $_GET['gw_prefill_email'])) : '',
        'telephone' => isset($_GET['gw_prefill_telephone']) ? sanitize_text_field(wp_unslash((string) $_GET['gw_prefill_telephone'])) : '',
        'telephone_portable' => isset($_GET['gw_prefill_telephone_portable']) ? sanitize_text_field(wp_unslash((string) $_GET['gw_prefill_telephone_portable'])) : '',
        'adresse1' => isset($_GET['gw_prefill_adresse1']) ? sanitize_text_field(wp_unslash((string) $_GET['gw_prefill_adresse1'])) : '',
        'adresse2' => isset($_GET['gw_prefill_adresse2']) ? sanitize_text_field(wp_unslash((string) $_GET['gw_prefill_adresse2'])) : '',
        'cp' => isset($_GET['gw_prefill_cp']) ? sanitize_text_field(wp_unslash((string) $_GET['gw_prefill_cp'])) : '',
        'ville' => isset($_GET['gw_prefill_ville']) ? sanitize_text_field(wp_unslash((string) $_GET['gw_prefill_ville'])) : '',
    ];
}

$activeTab = isset($_GET['tab']) ? (string) $_GET['tab'] : '';
$activeTab = strtolower(trim($activeTab));

$allowedTabs = $isCreate ? [
    'informations_generales',
] : [
    'informations_generales',
    'historique_commercial',
    'apprenants',
    'historique_sessions',
    'taches_activites',
];

if ($activeTab === '' || !in_array($activeTab, $allowedTabs, true)) {
    $activeTab = 'informations_generales';
}

$backUrl = home_url('/gestiwork/Tiers/');

$tierNotice = isset($_GET['gw_notice']) ? (string) $_GET['gw_notice'] : '';
$tierNotice = strtolower(trim($tierNotice));

$dbClientData = null;
if (! $isCreate && $clientId > 0) {
    $dbClientData = TierProvider::getById($clientId);
}

$clientData = [
    'raison_sociale' => '',
    'nom' => '',
    'prenom' => '',
    'type' => 'client_particulier',
    'statut' => 'client',
    'siret' => '',
    'forme_juridique' => '',
    'email' => '',
    'telephone' => '',
    'telephone_portable' => '',
    'adresse1' => '',
    'adresse2' => '',
    'cp' => '',
    'ville' => '',
];

if (is_array($dbClientData)) {
    $clientData = array_merge($clientData, $dbClientData);
}
if (!isset($clientData['type']) || trim((string) $clientData['type']) === '') {
    $clientData['type'] = 'client_particulier';
}


$tierContacts = [];
if (is_array($dbClientData) && $clientId > 0) {
    $tierContacts = TierContactProvider::listByTierId($clientId);
}

$clientApprenants = [];
if (!$isCreate && $clientId > 0) {
    $clientApprenants = ApprenantProvider::listByEntrepriseId($clientId);
}
$clientApprenantsCount = is_array($clientApprenants) ? count($clientApprenants) : 0;

$entreprises = array_merge(
    TierProvider::listByType('entreprise', 2000),
    TierProvider::listByType('client_entreprise', 2000)
);
$financeurs = TierProvider::listByType('financeur', 2000);

$linkedEntreprises = [];
$linkedFinanceurs = [];
if (!$isCreate && $clientId > 0) {
    if (($clientData['type'] ?? '') === 'financeur') {
        $linkedEntreprises = TierFinanceurProvider::getEntreprisesByFinanceurId($clientId);
    } elseif (($clientData['type'] ?? '') === 'entreprise' || ($clientData['type'] ?? '') === 'client_entreprise') {
        $linkedFinanceurs = TierFinanceurProvider::getFinanceursByEntrepriseId($clientId);
    }
}

$selectedEntrepriseIds = array_map(static function (array $row): int {
    return (int) ($row['id'] ?? 0);
}, is_array($linkedEntreprises) ? $linkedEntreprises : []);

$selectedFinanceurIds = array_map(static function (array $row): int {
    return (int) ($row['id'] ?? 0);
}, is_array($linkedFinanceurs) ? $linkedFinanceurs : []);

$viewUrl = add_query_arg([
    'gw_view' => 'Client',
    'gw_tier_id' => $clientId,
    'tab' => $activeTab,
], home_url('/gestiwork/'));

$editUrl = add_query_arg([
    'gw_view' => 'Client',
    'gw_tier_id' => $clientId,
    'tab' => $activeTab,
    'mode' => 'edit',
], home_url('/gestiwork/'));

$cancelEditUrl = add_query_arg([
    'gw_view' => 'Client',
    'gw_tier_id' => $clientId,
    'tab' => $activeTab,
], home_url('/gestiwork/'));

?>

<section class="gw-section gw-section-dashboard">
    <?php if ($tierNotice === 'tier_created') : ?>
        <div class="notice notice-success gw-notice-spacing">
            <p><?php esc_html_e('Tiers créé avec succès.', 'gestiwork'); ?></p>
        </div>
    <?php elseif ($tierNotice === 'tier_updated') : ?>
        <div class="notice notice-success gw-notice-spacing">
            <p><?php esc_html_e('Tiers mis à jour avec succès.', 'gestiwork'); ?></p>
        </div>
    <?php elseif ($tierNotice === 'contact_created') : ?>
        <div class="notice notice-success gw-notice-spacing">
            <p><?php esc_html_e('Contact ajouté avec succès.', 'gestiwork'); ?></p>
        </div>
    <?php elseif ($tierNotice === 'contact_updated') : ?>
        <div class="notice notice-success gw-notice-spacing">
            <p><?php esc_html_e('Contact modifié avec succès.', 'gestiwork'); ?></p>
        </div>
    <?php elseif ($tierNotice === 'contact_deleted') : ?>
        <div class="notice notice-success gw-notice-spacing">
            <p><?php esc_html_e('Contact supprimé avec succès.', 'gestiwork'); ?></p>
        </div>
    <?php elseif (isset($_GET['gw_error']) && $_GET['gw_error'] !== '') : ?>
        <div class="notice notice-error gw-notice-spacing">
            <p>
            <?php
            $errorType = (string) $_GET['gw_error'];
            switch ($errorType) {
                case 'nonce':
                    esc_html_e('Erreur de sécurité : veuillez recharger la page et réessayer.', 'gestiwork');
                    break;
                case 'apprenant_create_failed':
                    esc_html_e('Erreur lors de la création automatique du stagiaire. Veuillez réessayer.', 'gestiwork');
                    break;
                case 'validation':
                    if (isset($_GET['gw_error_msg']) && $_GET['gw_error_msg'] !== '') {
                        echo esc_html((string) $_GET['gw_error_msg']);
                    } else {
                        esc_html_e('Merci de vérifier les champs du formulaire.', 'gestiwork');
                    }
                    break;
                case 'invalid_id':
                    esc_html_e('Erreur : identifiant de tiers invalide.', 'gestiwork');
                    break;
                case 'create_failed':
                    esc_html_e('Erreur lors de la création du tiers. Veuillez réessayer.', 'gestiwork');
                    break;
                case 'update_failed':
                    esc_html_e('Erreur lors de la mise à jour du tiers. Veuillez réessayer.', 'gestiwork');
                    break;
                case 'contact_create_failed':
                    esc_html_e('Erreur lors de la création du contact. Veuillez réessayer.', 'gestiwork');
                    break;
                case 'contact_update_failed':
                    esc_html_e('Erreur lors de la modification du contact. Veuillez réessayer.', 'gestiwork');
                    break;
                case 'contact_delete_failed':
                    esc_html_e('Erreur lors de la suppression du contact. Veuillez réessayer.', 'gestiwork');
                    break;
                case 'tier_delete_failed':
                    esc_html_e('Erreur lors de la suppression du tiers. Veuillez réessayer.', 'gestiwork');
                    break;
                default:
                    esc_html_e('Une erreur est survenue lors de l\'enregistrement.', 'gestiwork');
                    break;
            }
            ?>
            </p>
        </div>
    <?php endif; ?>

    <div class="gw-flex-between">
        <div>
            <?php if ($isCreate) : ?>
                <h2 class="gw-section-title"><?php esc_html_e('Création Tiers', 'gestiwork'); ?></h2>
            <?php else : ?>
                <h2 class="gw-section-title"><?php esc_html_e('Fiche client', 'gestiwork'); ?></h2>
                <p class="gw-section-description">
                    <?php
                    $tierType = isset($clientData['type']) ? (string) $clientData['type'] : '';
                    $tierRaisonSociale = isset($clientData['raison_sociale']) ? trim((string) $clientData['raison_sociale']) : '';
                    $tierNom = isset($clientData['nom']) ? trim((string) $clientData['nom']) : '';
                    $tierPrenom = isset($clientData['prenom']) ? trim((string) $clientData['prenom']) : '';
                    $tierLabelParticulier = trim($tierPrenom . ' ' . $tierNom);
                    $tierLabel = $tierType === 'client_particulier' ? $tierLabelParticulier : $tierRaisonSociale;

                    if ($tierLabel === '') {
                        $tierLabel = (string) $clientId;
                    }

                    echo esc_html(
                        sprintf(
                            /* translators: %s: client label */
                            __('Fiche client : %s', 'gestiwork'),
                            $tierLabel
                        )
                    );
                    ?>
                </p>
            <?php endif; ?>
        </div>
        <div class="gw-flex-end">
            <a class="gw-button gw-button--secondary" href="<?php echo esc_url($backUrl); ?>">
                <?php esc_html_e('Retour aux tiers', 'gestiwork'); ?>
            </a>
            <?php if (! $isEdit && ! $isCreate) : ?>
                <a class="gw-button gw-button--primary" href="<?php echo esc_url($editUrl); ?>">
                    <?php esc_html_e('Modifier', 'gestiwork'); ?>
                </a>
            <?php endif; ?>
            <?php if (! $isCreate && $clientId > 0) : ?>
                <form method="post" action="" class="gw-form-inline">
                    <input type="hidden" name="gw_action" value="gw_tier_delete" />
                    <input type="hidden" name="tier_id" value="<?php echo (int) $clientId; ?>" />
                    <?php wp_nonce_field('gw_tier_delete', 'gw_nonce'); ?>
                    <button type="submit" class="gw-button gw-button--secondary gw-tier-delete gw-delete-button">
                        <span class="dashicons dashicons-trash" aria-hidden="true"></span>
                        <?php esc_html_e('Supprimer le client', 'gestiwork'); ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php if (! $isCreate) : ?>
        <div class="gw-settings-tabs" role="tablist">
            <button type="button" class="gw-settings-tab<?php echo $activeTab === 'informations_generales' ? ' gw-settings-tab--active' : ''; ?>" data-gw-tab="informations_generales">
                <?php esc_html_e('Informations générales', 'gestiwork'); ?>
            </button>
            <button type="button" class="gw-settings-tab<?php echo $activeTab === 'historique_commercial' ? ' gw-settings-tab--active' : ''; ?>" data-gw-tab="historique_commercial">
                <?php esc_html_e('Historique commercial', 'gestiwork'); ?>
            </button>
            <button type="button" class="gw-settings-tab<?php echo $activeTab === 'apprenants' ? ' gw-settings-tab--active' : ''; ?>" data-gw-tab="apprenants">
                <?php esc_html_e('Apprenants', 'gestiwork'); ?>
            </button>
            <button type="button" class="gw-settings-tab<?php echo $activeTab === 'historique_sessions' ? ' gw-settings-tab--active' : ''; ?>" data-gw-tab="historique_sessions">
                <?php esc_html_e('Historique des sessions', 'gestiwork'); ?>
            </button>
            <button type="button" class="gw-settings-tab<?php echo $activeTab === 'taches_activites' ? ' gw-settings-tab--active' : ''; ?>" data-gw-tab="taches_activites">
                <?php esc_html_e('Tâches et activités', 'gestiwork'); ?>
            </button>
        </div>
    <?php endif; ?>

    <div class="gw-settings-panels">
        <div class="gw-settings-panel<?php echo $activeTab === 'informations_generales' ? ' gw-settings-panel--active' : ''; ?>" data-gw-tab-panel="informations_generales">
            <?php if ($isCreate) : ?>
                <div class="gw-tier-info-layout gw-grid-layout">
                    <div class="gw-settings-group gw-m-0">
                        <div class="gw-flex-between-center">
                            <h3 class="gw-section-subtitle gw-m-0"><?php esc_html_e('Informations générales', 'gestiwork'); ?></h3>
                            <div class="gw-flex-end" id="gw_tier_create_insee_button_wrapper">
                                <button type="button"
                                    class="gw-link-button"
                                    data-gw-modal-target="gw-insee-modal"
                                    data-gw-insee-context="tier_create">
                                    <span class="dashicons dashicons-search" aria-hidden="true"></span>
                                    <?php esc_html_e('Rechercher une entreprise', 'gestiwork'); ?>
                                </button>
                            </div>
                        </div>

                        <form method="post" action="" class="gw-mt-12" id="gw_tier_create_form">
                            <input type="hidden" name="gw_action" value="gw_tier_create" />
                            <?php wp_nonce_field('gw_tier_create', 'gw_nonce'); ?>
                            <div class="gw-grid-2">
                                <div>
                                    <label class="gw-settings-placeholder" for="gw_tier_create_type"><?php esc_html_e('Catégorie', 'gestiwork'); ?></label>
                                    <select id="gw_tier_create_type" name="type" class="gw-modal-input">
                                        <?php $prefillType = isset($prefill['type']) && $prefill['type'] !== '' ? (string) $prefill['type'] : 'client_particulier'; ?>
                                        <option value="entreprise"<?php echo $prefillType === 'entreprise' ? ' selected' : ''; ?>><?php esc_html_e('Entreprise', 'gestiwork'); ?></option>
                                        <option value="client_particulier"<?php echo $prefillType === 'client_particulier' ? ' selected' : ''; ?>><?php esc_html_e('Particulier', 'gestiwork'); ?></option>
                                        <option value="financeur"<?php echo $prefillType === 'financeur' ? ' selected' : ''; ?>><?php esc_html_e('Financeur / OPCO', 'gestiwork'); ?></option>
                                        <option value="of_donneur_ordre"<?php echo $prefillType === 'of_donneur_ordre' ? ' selected' : ''; ?>><?php esc_html_e('OF donneur d\'ordre', 'gestiwork'); ?></option>
                                    </select>
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_tier_create_statut"><?php esc_html_e('Statut', 'gestiwork'); ?></label>
                                    <select id="gw_tier_create_statut" name="statut" class="gw-modal-input">
                                        <?php $prefillStatut = isset($prefill['statut']) && $prefill['statut'] !== '' ? (string) $prefill['statut'] : 'client'; ?>
                                        <option value="prospect"<?php echo $prefillStatut === 'prospect' ? ' selected' : ''; ?>><?php esc_html_e('Prospect', 'gestiwork'); ?></option>
                                        <option value="client"<?php echo $prefillStatut === 'client' ? ' selected' : ''; ?>><?php esc_html_e('Client', 'gestiwork'); ?></option>
                                    </select>
                                </div>

                                <div id="gw_tier_create_field_raison_sociale" class="gw-full-width">
                                    <label class="gw-settings-placeholder" for="gw_tier_create_raison_sociale"><?php esc_html_e('Nom / Raison sociale', 'gestiwork'); ?> <span class="gw-required-asterisk">*</span></label>
                                    <input type="text" id="gw_tier_create_raison_sociale" name="raison_sociale" class="gw-modal-input" value="<?php echo esc_attr((string) ($prefill['raison_sociale'] ?? '')); ?>" />
                                </div>

                                <div id="gw_tier_create_field_nom">
                                    <label class="gw-settings-placeholder" for="gw_tier_create_nom"><?php esc_html_e('Nom', 'gestiwork'); ?> <span class="gw-required-asterisk">*</span></label>
                                    <input type="text" id="gw_tier_create_nom" name="nom" class="gw-modal-input" value="<?php echo esc_attr((string) ($prefill['nom'] ?? '')); ?>" />
                                </div>

                                <div id="gw_tier_create_field_prenom">
                                    <label class="gw-settings-placeholder" for="gw_tier_create_prenom"><?php esc_html_e('Prénom', 'gestiwork'); ?> <span class="gw-required-asterisk">*</span></label>
                                    <input type="text" id="gw_tier_create_prenom" name="prenom" class="gw-modal-input" value="<?php echo esc_attr((string) ($prefill['prenom'] ?? '')); ?>" />
                                </div>

                                <div id="gw_tier_create_field_siret" class="gw-full-width">
                                    <label class="gw-settings-placeholder" for="gw_tier_create_siret"><?php esc_html_e('SIRET / SIREN', 'gestiwork'); ?> <span class="gw-required-asterisk">*</span></label>
                                    <input type="text" id="gw_tier_create_siret" name="siret" class="gw-modal-input" value="<?php echo esc_attr((string) ($prefill['siret'] ?? '')); ?>" />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_tier_create_email"><?php esc_html_e('Adresse e-mail', 'gestiwork'); ?> <span class="gw-required-asterisk">*</span></label>
                                    <input type="email" id="gw_tier_create_email" name="email" class="gw-modal-input" value="<?php echo esc_attr((string) ($prefill['email'] ?? '')); ?>" />
                                </div>
                                <div>
                                    <label class="gw-settings-placeholder" for="gw_tier_create_phone"><?php esc_html_e('Numéro de téléphone', 'gestiwork'); ?> <span class="gw-required-asterisk">*</span></label>
                                    <input type="text" id="gw_tier_create_phone" name="telephone" class="gw-modal-input" value="<?php echo esc_attr((string) ($prefill['telephone'] ?? '')); ?>" />
                                </div>
                                <div>
                                    <label class="gw-settings-placeholder" for="gw_tier_create_phone_mobile"><?php esc_html_e('Téléphone portable', 'gestiwork'); ?> <span class="gw-required-asterisk">*</span></label>
                                    <input type="text" id="gw_tier_create_phone_mobile" name="telephone_portable" class="gw-modal-input" value="<?php echo esc_attr((string) ($prefill['telephone_portable'] ?? '')); ?>" />
                                </div>

                                <div class="gw-full-width">
                                    <label class="gw-settings-placeholder" for="gw_tier_create_adresse1"><?php esc_html_e('Numéro de rue et rue', 'gestiwork'); ?> <span class="gw-required-asterisk">*</span></label>
                                    <input type="text" id="gw_tier_create_adresse1" name="adresse1" class="gw-modal-input" value="<?php echo esc_attr((string) ($prefill['adresse1'] ?? '')); ?>" />
                                </div>
                                <div class="gw-full-width">
                                    <label class="gw-settings-placeholder" for="gw_tier_create_adresse2"><?php esc_html_e('Complément d\'adresse', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_tier_create_adresse2" name="adresse2" class="gw-modal-input" value="<?php echo esc_attr((string) ($prefill['adresse2'] ?? '')); ?>" />
                                </div>
                                <div>
                                    <label class="gw-settings-placeholder" for="gw_tier_create_cp"><?php esc_html_e('Code postal', 'gestiwork'); ?> <span class="gw-required-asterisk">*</span></label>
                                    <input type="text" id="gw_tier_create_cp" name="cp" class="gw-modal-input" value="<?php echo esc_attr((string) ($prefill['cp'] ?? '')); ?>" />
                                </div>
                                <div>
                                    <label class="gw-settings-placeholder" for="gw_tier_create_ville"><?php esc_html_e('Ville', 'gestiwork'); ?> <span class="gw-required-asterisk">*</span></label>
                                    <input type="text" id="gw_tier_create_ville" name="ville" class="gw-modal-input" value="<?php echo esc_attr((string) ($prefill['ville'] ?? '')); ?>" />
                                </div>

                                <div id="gw_tier_create_field_forme_juridique">
                                    <label class="gw-settings-placeholder" for="gw_tier_create_forme_juridique"><?php esc_html_e('Forme juridique', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_tier_create_forme_juridique" name="forme_juridique" class="gw-modal-input" value="<?php echo esc_attr((string) ($prefill['forme_juridique'] ?? '')); ?>" />
                                </div>

                                <div class="gw-full-width">
                                    <p id="gw_tier_create_error" class="gw-modal-error-info gw-display-none"></p>
                                </div>

                                <div class="gw-full-width gw-mt-6">
                                    <button type="submit" class="gw-button gw-button--primary">
                                        <?php esc_html_e('Enregistrer', 'gestiwork'); ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="gw-grid-layout">
                        <div class="gw-card gw-display-none" id="gw_tier_create_financeur_entreprises_card">
                            <div class="gw-flex-between-center">
                                <h3 class="gw-section-subtitle gw-m-0"><?php esc_html_e('Entreprises associées', 'gestiwork'); ?></h3>
                            </div>
                            <div class="gw-mt-10 gw-color-muted-text gw-button-small">
                                <?php esc_html_e('Sélectionnez les entreprises à associer. Les associations seront enregistrées lors de la création du financeur.', 'gestiwork'); ?>
                            </div>
                            <div class="gw-checklist-container" data-gw-checklist>
                                <input type="text" class="gw-modal-input" data-gw-checklist-search placeholder="<?php echo esc_attr__('Rechercher...', 'gestiwork'); ?>" />
                                <div class="gw-checklist-buttons">
                                    <button type="button" class="gw-button gw-button--secondary" data-gw-checklist-all><?php esc_html_e('Tout sélectionner', 'gestiwork'); ?></button>
                                    <button type="button" class="gw-button gw-button--secondary" data-gw-checklist-none><?php esc_html_e('Tout désélectionner', 'gestiwork'); ?></button>
                                </div>
                                <div class="gw-checklist-items">
                                    <?php foreach ($entreprises as $entreprise) : ?>
                                        <?php
                                        $eid = (int) ($entreprise['id'] ?? 0);
                                        $label = trim((string) ($entreprise['raison_sociale'] ?? ''));
                                        if ($label === '') {
                                            $label = trim(((string) ($entreprise['prenom'] ?? '')) . ' ' . ((string) ($entreprise['nom'] ?? '')));
                                        }
                                        if ($label === '') {
                                            $label = (string) $eid;
                                        }
                                        $labelSearch = function_exists('mb_strtolower') ? mb_strtolower($label) : strtolower($label);
                                        $inputId = 'gw_tier_create_entreprise_' . $eid;
                                        ?>
                                        <label data-gw-checklist-item data-gw-checklist-text="<?php echo esc_attr($labelSearch); ?>" for="<?php echo esc_attr($inputId); ?>" class="gw-checklist-item">
                                            <input id="<?php echo esc_attr($inputId); ?>" type="checkbox" name="entreprise_ids[]" value="<?php echo (int) $eid; ?>" form="gw_tier_create_form" />
                                            <span><?php echo esc_html($label); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="gw-card gw-display-none" id="gw_tier_create_entreprise_financeurs_card">
                            <div class="gw-flex-between-center">
                                <h3 class="gw-section-subtitle gw-m-0"><?php esc_html_e('Financeurs / OPCO associés', 'gestiwork'); ?></h3>
                            </div>
                            <div class="gw-mt-10 gw-color-muted-text gw-button-small">
                                <?php esc_html_e('Sélectionnez les financeurs à associer. Les associations seront enregistrées lors de la création de l\'entreprise.', 'gestiwork'); ?>
                            </div>
                            <div class="gw-checklist-container" data-gw-checklist>
                                <input type="text" class="gw-modal-input" data-gw-checklist-search placeholder="<?php echo esc_attr__('Rechercher...', 'gestiwork'); ?>" />
                                <div class="gw-checklist-buttons">
                                    <button type="button" class="gw-button gw-button--secondary" data-gw-checklist-all><?php esc_html_e('Tout sélectionner', 'gestiwork'); ?></button>
                                    <button type="button" class="gw-button gw-button--secondary" data-gw-checklist-none><?php esc_html_e('Tout désélectionner', 'gestiwork'); ?></button>
                                </div>
                                <div class="gw-checklist-items">
                                    <?php foreach ($financeurs as $financeur) : ?>
                                        <?php
                                        $fid = (int) ($financeur['id'] ?? 0);
                                        $label = trim((string) ($financeur['raison_sociale'] ?? ''));
                                        if ($label === '') {
                                            $label = trim(((string) ($financeur['prenom'] ?? '')) . ' ' . ((string) ($financeur['nom'] ?? '')));
                                        }
                                        if ($label === '') {
                                            $label = (string) $fid;
                                        }
                                        $labelSearch = function_exists('mb_strtolower') ? mb_strtolower($label) : strtolower($label);
                                        $inputId = 'gw_tier_create_financeur_' . $fid;
                                        ?>
                                        <label data-gw-checklist-item data-gw-checklist-text="<?php echo esc_attr($labelSearch); ?>" for="<?php echo esc_attr($inputId); ?>" class="gw-checklist-item">
                                            <input id="<?php echo esc_attr($inputId); ?>" type="checkbox" name="financeur_ids[]" value="<?php echo (int) $fid; ?>" form="gw_tier_create_form" />
                                            <span><?php echo esc_html($label); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="gw-card">
                            <div class="gw-flex-between-center">
                                <h3 class="gw-section-subtitle gw-m-0"><?php esc_html_e('Contacts clients', 'gestiwork'); ?></h3>
                                <?php if (! $isCreate && $clientId > 0) : ?>
                                    <a href="#" data-gw-modal-target="gw-modal-client-contacts" class="gw-link-no-underline">
                                        <span class="dashicons dashicons-plus" aria-hidden="true"></span>
                                        <?php esc_html_e('Associer des contacts clients', 'gestiwork'); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <?php if (is_array($tierContacts) && count($tierContacts) > 0) : ?>
                                <div class="gw-container-list">
                                    <?php foreach ($tierContacts as $index => $contact) : ?>
                                        <?php
                                        $contactNom = isset($contact['nom']) ? (string) $contact['nom'] : '';
                                        $contactPrenom = isset($contact['prenom']) ? (string) $contact['prenom'] : '';
                                        $contactFonction = isset($contact['fonction']) ? (string) $contact['fonction'] : '';
                                        $contactMail = isset($contact['mail']) ? (string) $contact['mail'] : '';
                                        $contactTel1 = isset($contact['tel1']) ? (string) $contact['tel1'] : '';
                                        $contactTel2 = isset($contact['tel2']) ? (string) $contact['tel2'] : '';
                                        $contactIdRow = isset($contact['id']) ? (int) $contact['id'] : 0;
                                        $phones = [];
                                        if (trim($contactTel1) !== '') {
                                            $phones[] = trim($contactTel1);
                                        }
                                        if (trim($contactTel2) !== '') {
                                            $phones[] = trim($contactTel2);
                                        }
                                        $contactTel = implode(' — ', $phones);
                                        $contactLabel = trim($contactPrenom . ' ' . $contactNom);
                                        $contactMeta = trim($contactFonction) !== '' ? trim($contactFonction) : __('Fonction non renseignée', 'gestiwork');
                                        ?>
                                        <div class="gw-info-card">
                                            <div class="gw-info-card-header">
                                                <div class="gw-info-card-content">
                                                    <div class="gw-info-card-title">
                                                        <a href="#" class="gw-link-no-underline gw-color-primary-text gw-font-weight-600">
                                                            <?php echo esc_html($contactLabel !== '' ? $contactLabel : '-'); ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="gw-info-card-actions">
                                                    <span class="gw-contact-tag">
                                                        <?php echo esc_html($contactMeta); ?>
                                                    </span>

                                                    <div class="gw-contact-actions">
                                                        <button
                                                            type="button"
                                                            class="gw-button gw-button--secondary gw-contact-actions-trigger"
                                                            aria-haspopup="menu"
                                                            aria-expanded="false"
                                                            aria-label="<?php esc_attr_e('Actions', 'gestiwork'); ?>"
                                                            data-contact-actions="<?php echo (int) $contactIdRow; ?>">
                                                            <span class="dashicons dashicons-ellipsis" aria-hidden="true"></span>
                                                        </button>
                                                        <div
                                                            class="gw-contact-actions-menu"
                                                            role="menu">
                                                            <button
                                                                type="button"
                                                                role="menuitem"
                                                                class="gw-link-button"
                                                                style="width:100%; text-align:left; padding:8px 10px; border-radius:8px;"
                                                                data-gw-modal-target="gw-modal-client-contact-edit"
                                                                data-contact-id="<?php echo (int) $contactIdRow; ?>"
                                                                data-contact-civilite="<?php echo esc_attr((string) ($contact['civilite'] ?? 'non_renseigne')); ?>"
                                                                data-contact-fonction="<?php echo esc_attr((string) $contactFonction); ?>"
                                                                data-contact-nom="<?php echo esc_attr((string) $contactNom); ?>"
                                                                data-contact-prenom="<?php echo esc_attr((string) $contactPrenom); ?>"
                                                                data-contact-mail="<?php echo esc_attr((string) $contactMail); ?>"
                                                                data-contact-tel1="<?php echo esc_attr((string) $contactTel1); ?>"
                                                                data-contact-tel2="<?php echo esc_attr((string) $contactTel2); ?>">
                                                                <span class="dashicons dashicons-edit" aria-hidden="true" style="margin-right:6px;"></span>
                                                                <?php esc_html_e('Voir et modifier le contact', 'gestiwork'); ?>
                                                            </button>
                                                            <form method="post" action="" style="margin:0;">
                                                                <input type="hidden" name="gw_action" value="gw_tier_contact_delete" />
                                                                <input type="hidden" name="tier_id" value="<?php echo (int) $clientId; ?>" />
                                                                <input type="hidden" name="contact_id" value="<?php echo (int) $contactIdRow; ?>" />
                                                                <?php wp_nonce_field('gw_tier_contact_manage', 'gw_nonce'); ?>
                                                                <button type="submit" role="menuitem" class="gw-link-button gw-contact-delete" style="width:100%; text-align:left; padding:8px 10px; border-radius:8px; color:#d63638;">
                                                                    <span class="dashicons dashicons-trash" aria-hidden="true" style="margin-right:6px;"></span>
                                                                    <?php esc_html_e('Supprimer le contact', 'gestiwork'); ?>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div style="margin-top: 8px; display:grid; gap:6px; color: var(--gw-color-text);">
                                                <?php if ($contactTel !== '') : ?>
                                                    <?php if (trim($contactTel1) !== '' && trim($contactTel2) !== '') : ?>
                                                        <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
                                                            <span style="display:inline-flex; align-items:center; gap:8px;">
                                                                <span class="dashicons dashicons-phone" aria-hidden="true" style="color: var(--gw-color-muted);"></span>
                                                                <span><?php echo esc_html(trim($contactTel1)); ?></span>
                                                            </span>
                                                            <span style="display:inline-flex; align-items:center; gap:8px;">
                                                                <span class="dashicons dashicons-phone" aria-hidden="true" style="color: var(--gw-color-muted);"></span>
                                                                <span><?php echo esc_html(trim($contactTel2)); ?></span>
                                                            </span>
                                                        </div>
                                                    <?php else : ?>
                                                        <div style="display:flex; align-items:center; gap:8px;">
                                                            <span class="dashicons dashicons-phone" aria-hidden="true" style="color: var(--gw-color-muted);"></span>
                                                            <span><?php echo esc_html($contactTel); ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endif; ?>

                                                <?php if ($contactMail !== '') : ?>
                                                    <div style="display:flex; align-items:center; gap:8px;">
                                                        <span class="dashicons dashicons-email" aria-hidden="true" style="color: var(--gw-color-muted);"></span>
                                                        <a href="mailto:<?php echo esc_attr($contactMail); ?>" style="text-decoration:none; color: inherit;">
                                                            <?php echo esc_html($contactMail); ?>
                                                        </a>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else : ?>
                                <div class="gw-mt-10 gw-color-muted-text gw-button-small">
                                    <?php if ($isCreate) : ?>
                                        <?php esc_html_e('Enregistrez d\'abord le client. Vous pourrez ensuite créer ses contacts depuis sa fiche (Clients > ouvrir le client).', 'gestiwork'); ?>
                                    <?php else : ?>
                                        <?php esc_html_e('Cette entreprise n\'a aucun contact client associé.', 'gestiwork'); ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <div class="gw-tier-info-layout" style="display:grid; gap: 14px; align-items:start;">
                    <div class="gw-settings-group" style="margin:0;">
                        <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;">
                            <h3 class="gw-section-subtitle gw-m-0"><?php esc_html_e('Informations générales', 'gestiwork'); ?></h3>
                        </div>

                        <form method="post" action="" style="margin-top: 12px;" id="gw_tier_update_form">
                            <input type="hidden" name="gw_action" value="gw_tier_update" />
                            <input type="hidden" name="tier_id" value="<?php echo (int) $clientId; ?>" />
                            <?php wp_nonce_field('gw_tier_update', 'gw_nonce'); ?>
                            <div style="display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px;">
                                <div>
                                    <label class="gw-settings-placeholder" for="gw_tier_view_type"><?php esc_html_e('Catégorie', 'gestiwork'); ?></label>
                                    <select id="gw_tier_view_type" name="type" class="gw-modal-input"<?php echo $isEdit ? '' : ' disabled'; ?>>
                                        <option value="entreprise"<?php echo $clientData['type'] === 'entreprise' ? ' selected' : ''; ?>><?php esc_html_e('Entreprise', 'gestiwork'); ?></option>
                                        <option value="client_particulier"<?php echo $clientData['type'] === 'client_particulier' ? ' selected' : ''; ?>><?php esc_html_e('Particulier', 'gestiwork'); ?></option>
                                        <option value="financeur"<?php echo $clientData['type'] === 'financeur' ? ' selected' : ''; ?>><?php esc_html_e('Financeur / OPCO', 'gestiwork'); ?></option>
                                        <option value="of_donneur_ordre"<?php echo $clientData['type'] === 'of_donneur_ordre' ? ' selected' : ''; ?>><?php esc_html_e('OF donneur d\'ordre', 'gestiwork'); ?></option>
                                    </select>
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_tier_view_statut"><?php esc_html_e('Statut', 'gestiwork'); ?></label>
                                    <select id="gw_tier_view_statut" name="statut" class="gw-modal-input"<?php echo $isEdit ? '' : ' disabled'; ?>>
                                        <option value="prospect"<?php echo $clientData['statut'] === 'prospect' ? ' selected' : ''; ?>><?php esc_html_e('Prospect', 'gestiwork'); ?></option>
                                        <option value="client"<?php echo $clientData['statut'] === 'client' ? ' selected' : ''; ?>><?php esc_html_e('Client', 'gestiwork'); ?></option>
                                    </select>
                                </div>

                                <div id="gw_tier_view_field_raison_sociale" class="gw-full-width">
                                    <label class="gw-settings-placeholder" for="gw_tier_view_raison_sociale"><?php esc_html_e('Nom / Raison sociale', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_tier_view_raison_sociale" name="raison_sociale" class="gw-modal-input" value="<?php echo esc_attr($clientData['raison_sociale']); ?>"<?php echo $isEdit ? '' : ' disabled'; ?> />
                                </div>

                                <div id="gw_tier_view_field_nom">
                                    <label class="gw-settings-placeholder" for="gw_tier_view_nom"><?php esc_html_e('Nom', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_tier_view_nom" name="nom" class="gw-modal-input" value="<?php echo esc_attr($clientData['nom'] ?? ''); ?>"<?php echo $isEdit ? '' : ' disabled'; ?> />
                                </div>

                                <div id="gw_tier_view_field_prenom">
                                    <label class="gw-settings-placeholder" for="gw_tier_view_prenom"><?php esc_html_e('Prénom', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_tier_view_prenom" name="prenom" class="gw-modal-input" value="<?php echo esc_attr($clientData['prenom'] ?? ''); ?>"<?php echo $isEdit ? '' : ' disabled'; ?> />
                                </div>

                                <div id="gw_tier_view_field_siret" class="gw-full-width">
                                    <label class="gw-settings-placeholder" for="gw_tier_view_siret"><?php esc_html_e('SIRET / SIREN', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_tier_view_siret" name="siret" class="gw-modal-input" value="<?php echo esc_attr($clientData['siret']); ?>"<?php echo $isEdit ? '' : ' disabled'; ?> />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_tier_view_email"><?php esc_html_e('Adresse e-mail', 'gestiwork'); ?></label>
                                    <input type="email" id="gw_tier_view_email" name="email" class="gw-modal-input" value="<?php echo esc_attr($clientData['email']); ?>"<?php echo $isEdit ? '' : ' disabled'; ?> />
                                </div>
                                <div>
                                    <label class="gw-settings-placeholder" for="gw_tier_view_telephone"><?php esc_html_e('Numéro de téléphone', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_tier_view_telephone" name="telephone" class="gw-modal-input" value="<?php echo esc_attr($clientData['telephone']); ?>"<?php echo $isEdit ? '' : ' disabled'; ?> />
                                </div>
                                <div>
                                    <label class="gw-settings-placeholder" for="gw_tier_view_telephone_portable"><?php esc_html_e('Téléphone portable', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_tier_view_telephone_portable" name="telephone_portable" class="gw-modal-input" value="<?php echo esc_attr($clientData['telephone_portable']); ?>"<?php echo $isEdit ? '' : ' disabled'; ?> />
                                </div>

                                <div class="gw-full-width">
                                    <label class="gw-settings-placeholder" for="gw_tier_view_adresse1"><?php esc_html_e('Numéro de rue et rue', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_tier_view_adresse1" name="adresse1" class="gw-modal-input" value="<?php echo esc_attr($clientData['adresse1']); ?>"<?php echo $isEdit ? '' : ' disabled'; ?> />
                                </div>
                                <div class="gw-full-width">
                                    <label class="gw-settings-placeholder" for="gw_tier_view_adresse2"><?php esc_html_e('Complément d\'adresse', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_tier_view_adresse2" name="adresse2" class="gw-modal-input" value="<?php echo esc_attr($clientData['adresse2']); ?>"<?php echo $isEdit ? '' : ' disabled'; ?> />
                                </div>
                                <div>
                                    <label class="gw-settings-placeholder" for="gw_tier_view_cp"><?php esc_html_e('Code postal', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_tier_view_cp" name="cp" class="gw-modal-input" value="<?php echo esc_attr($clientData['cp']); ?>"<?php echo $isEdit ? '' : ' disabled'; ?> />
                                </div>
                                <div>
                                    <label class="gw-settings-placeholder" for="gw_tier_view_ville"><?php esc_html_e('Ville', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_tier_view_ville" name="ville" class="gw-modal-input" value="<?php echo esc_attr($clientData['ville']); ?>"<?php echo $isEdit ? '' : ' disabled'; ?> />
                                </div>

                                <div id="gw_tier_view_field_forme_juridique">
                                    <label class="gw-settings-placeholder" for="gw_tier_view_forme_juridique"><?php esc_html_e('Forme juridique', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_tier_view_forme_juridique" name="forme_juridique" class="gw-modal-input" value="<?php echo esc_attr($clientData['forme_juridique']); ?>"<?php echo $isEdit ? '' : ' disabled'; ?> />
                                </div>

                                <?php if ($isEdit) : ?>
                                    <div class="gw-full-width gw-mt-6 gw-flex-end">
                                        <a class="gw-button gw-button--secondary" href="<?php echo esc_url($cancelEditUrl); ?>">
                                            <?php esc_html_e('Annuler', 'gestiwork'); ?>
                                        </a>
                                        <button type="submit" class="gw-button gw-button--primary">
                                            <?php esc_html_e('Enregistrer', 'gestiwork'); ?>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>

                    <div style="display:grid; gap:14px;">
                        <div class="gw-card gw-display-none" id="gw_tier_view_financeur_entreprises_card">
                            <h3 class="gw-section-subtitle gw-m-0"><?php esc_html_e('Entreprises associées', 'gestiwork'); ?></h3>
                            <?php if ($isEdit) : ?>
                                <div class="gw-mt-8 gw-color-muted-text gw-button-small">
                                    <?php esc_html_e('La sélection remplace les associations existantes lors de l\'enregistrement.', 'gestiwork'); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($isEdit) : ?>
                                <div class="gw-checklist-container" data-gw-checklist>
                                    <input type="text" class="gw-modal-input" data-gw-checklist-search placeholder="<?php echo esc_attr__('Rechercher...', 'gestiwork'); ?>" />
                                    <div class="gw-checklist-buttons">
                                        <button type="button" class="gw-button gw-button--secondary" data-gw-checklist-all><?php esc_html_e('Tout sélectionner', 'gestiwork'); ?></button>
                                        <button type="button" class="gw-button gw-button--secondary" data-gw-checklist-none><?php esc_html_e('Tout désélectionner', 'gestiwork'); ?></button>
                                    </div>
                                    <div class="gw-checklist-items">
                                        <?php foreach ($entreprises as $entreprise) : ?>
                                            <?php
                                            $eid = (int) ($entreprise['id'] ?? 0);
                                            $label = trim((string) ($entreprise['raison_sociale'] ?? ''));
                                            if ($label === '') {
                                                $label = trim(((string) ($entreprise['prenom'] ?? '')) . ' ' . ((string) ($entreprise['nom'] ?? '')));
                                            }
                                            if ($label === '') {
                                                $label = (string) $eid;
                                            }
                                            $labelSearch = function_exists('mb_strtolower') ? mb_strtolower($label) : strtolower($label);
                                            $inputId = 'gw_tier_update_entreprise_' . $eid;
                                            ?>
                                            <label data-gw-checklist-item data-gw-checklist-text="<?php echo esc_attr($labelSearch); ?>" for="<?php echo esc_attr($inputId); ?>" class="gw-checklist-item">
                                                <input id="<?php echo esc_attr($inputId); ?>" type="checkbox" name="entreprise_ids[]" value="<?php echo (int) $eid; ?>" form="gw_tier_update_form"<?php echo in_array($eid, $selectedEntrepriseIds, true) ? ' checked' : ''; ?> />
                                                <span><?php echo esc_html($label); ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php else : ?>
                                <?php if (is_array($linkedEntreprises) && count($linkedEntreprises) > 0) : ?>
                                    <div class="gw-container-list">
                                        <?php foreach ($linkedEntreprises as $entreprise) : ?>
                                            <?php
                                            $label = trim((string) ($entreprise['raison_sociale'] ?? ''));
                                            if ($label === '') {
                                                $label = trim(((string) ($entreprise['prenom'] ?? '')) . ' ' . ((string) ($entreprise['nom'] ?? '')));
                                            }
                                            ?>
                                            <div class="gw-info-card">
                                                <?php echo esc_html($label !== '' ? $label : '-'); ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else : ?>
                                    <div class="gw-mt-10 gw-color-muted-text gw-button-small">
                                        <?php esc_html_e('Aucune entreprise associée.', 'gestiwork'); ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <div class="gw-card gw-display-none" id="gw_tier_view_entreprise_financeurs_card">
                            <h3 class="gw-section-subtitle gw-m-0"><?php esc_html_e('Financeurs / OPCO associés', 'gestiwork'); ?></h3>
                            <?php if ($isEdit) : ?>
                                <div class="gw-mt-8 gw-color-muted-text gw-button-small">
                                    <?php esc_html_e('La sélection remplace les associations existantes lors de l\'enregistrement.', 'gestiwork'); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($isEdit) : ?>
                                <div class="gw-checklist-container" data-gw-checklist>
                                    <input type="text" class="gw-modal-input" data-gw-checklist-search placeholder="<?php echo esc_attr__('Rechercher...', 'gestiwork'); ?>" />
                                    <div class="gw-checklist-buttons">
                                        <button type="button" class="gw-button gw-button--secondary" data-gw-checklist-all><?php esc_html_e('Tout sélectionner', 'gestiwork'); ?></button>
                                        <button type="button" class="gw-button gw-button--secondary" data-gw-checklist-none><?php esc_html_e('Tout désélectionner', 'gestiwork'); ?></button>
                                    </div>
                                    <div class="gw-checklist-items">
                                        <?php foreach ($financeurs as $financeur) : ?>
                                            <?php
                                            $fid = (int) ($financeur['id'] ?? 0);
                                            $label = trim((string) ($financeur['raison_sociale'] ?? ''));
                                            if ($label === '') {
                                                $label = trim(((string) ($financeur['prenom'] ?? '')) . ' ' . ((string) ($financeur['nom'] ?? '')));
                                            }
                                            if ($label === '') {
                                                $label = (string) $fid;
                                            }
                                            $labelSearch = function_exists('mb_strtolower') ? mb_strtolower($label) : strtolower($label);
                                            $inputId = 'gw_tier_update_financeur_' . $fid;
                                            ?>
                                            <label data-gw-checklist-item data-gw-checklist-text="<?php echo esc_attr($labelSearch); ?>" for="<?php echo esc_attr($inputId); ?>" class="gw-checklist-item">
                                                <input id="<?php echo esc_attr($inputId); ?>" type="checkbox" name="financeur_ids[]" value="<?php echo (int) $fid; ?>" form="gw_tier_update_form"<?php echo in_array($fid, $selectedFinanceurIds, true) ? ' checked' : ''; ?> />
                                                <span><?php echo esc_html($label); ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php else : ?>
                                <?php if (is_array($linkedFinanceurs) && count($linkedFinanceurs) > 0) : ?>
                                    <div class="gw-container-list">
                                        <?php foreach ($linkedFinanceurs as $financeur) : ?>
                                            <?php
                                            $label = trim((string) ($financeur['raison_sociale'] ?? ''));
                                            if ($label === '') {
                                                $label = trim(((string) ($financeur['prenom'] ?? '')) . ' ' . ((string) ($financeur['nom'] ?? '')));
                                            }
                                            ?>
                                            <div class="gw-info-card">
                                                <?php echo esc_html($label !== '' ? $label : '-'); ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else : ?>
                                    <div class="gw-mt-10 gw-color-muted-text gw-button-small">
                                        <?php esc_html_e('Aucun financeur associé.', 'gestiwork'); ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <div class="gw-card">
                            <h3 class="gw-section-subtitle gw-m-0"><?php esc_html_e('Aucune tâche programmée', 'gestiwork'); ?></h3>
                            <div style="margin-top: 8px;">
                                <a href="#" class="gw-link-no-underline">
                                    <?php esc_html_e('Voir les tâches', 'gestiwork'); ?>
                                </a>
                            </div>
                        </div>

                        <div class="gw-card">
                            <div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
                                <h3 class="gw-section-subtitle gw-m-0"><?php esc_html_e('Contacts clients', 'gestiwork'); ?></h3>
                                <?php if (! $isCreate && $clientId > 0) : ?>
                                    <a href="#" data-gw-modal-target="gw-modal-client-contacts" class="gw-link-no-underline">
                                        <span class="dashicons dashicons-plus" aria-hidden="true"></span>
                                        <?php esc_html_e('Associer des contacts clients', 'gestiwork'); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <?php if (is_array($tierContacts) && count($tierContacts) > 0) : ?>
                                <div class="gw-container-list">
                                    <?php foreach ($tierContacts as $index => $contact) : ?>
                                        <?php
                                        $contactNom = isset($contact['nom']) ? (string) $contact['nom'] : '';
                                        $contactPrenom = isset($contact['prenom']) ? (string) $contact['prenom'] : '';
                                        $contactFonction = isset($contact['fonction']) ? (string) $contact['fonction'] : '';
                                        $contactMail = isset($contact['mail']) ? (string) $contact['mail'] : '';
                                        $contactTel1 = isset($contact['tel1']) ? (string) $contact['tel1'] : '';
                                        $contactTel2 = isset($contact['tel2']) ? (string) $contact['tel2'] : '';
                                        $contactIdRow = isset($contact['id']) ? (int) $contact['id'] : 0;
                                        $phones = [];
                                        if (trim($contactTel1) !== '') {
                                            $phones[] = trim($contactTel1);
                                        }
                                        if (trim($contactTel2) !== '') {
                                            $phones[] = trim($contactTel2);
                                        }
                                        $contactTel = implode(' — ', $phones);
                                        $contactLabel = trim($contactPrenom . ' ' . $contactNom);
                                        $contactMeta = trim($contactFonction) !== '' ? trim($contactFonction) : __('Fonction non renseignée', 'gestiwork');
                                        ?>
                                        <div class="gw-info-card">
                                            <div class="gw-info-card-header">
                                                <div class="gw-info-card-content">
                                                    <div class="gw-info-card-title">
                                                        <a href="#" class="gw-link-no-underline gw-color-primary-text gw-font-weight-600">
                                                            <?php echo esc_html($contactLabel !== '' ? $contactLabel : '-'); ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="gw-info-card-actions">
                                                    <span class="gw-contact-tag">
                                                        <?php echo esc_html($contactMeta); ?>
                                                    </span>

                                                    <div class="gw-contact-actions">
                                                        <button
                                                            type="button"
                                                            class="gw-button gw-button--secondary gw-contact-actions-trigger"
                                                            aria-haspopup="menu"
                                                            aria-expanded="false"
                                                            aria-label="<?php esc_attr_e('Actions', 'gestiwork'); ?>"
                                                            data-contact-actions="<?php echo (int) $contactIdRow; ?>">
                                                            <span class="dashicons dashicons-ellipsis" aria-hidden="true"></span>
                                                        </button>
                                                        <div
                                                            class="gw-contact-actions-menu"
                                                            role="menu">
                                                            <button
                                                                type="button"
                                                                role="menuitem"
                                                                class="gw-link-button"
                                                                style="width:100%; text-align:left; padding:8px 10px; border-radius:8px;"
                                                                data-gw-modal-target="gw-modal-client-contact-edit"
                                                                data-contact-id="<?php echo (int) $contactIdRow; ?>"
                                                                data-contact-civilite="<?php echo esc_attr((string) ($contact['civilite'] ?? 'non_renseigne')); ?>"
                                                                data-contact-fonction="<?php echo esc_attr((string) $contactFonction); ?>"
                                                                data-contact-nom="<?php echo esc_attr((string) $contactNom); ?>"
                                                                data-contact-prenom="<?php echo esc_attr((string) $contactPrenom); ?>"
                                                                data-contact-mail="<?php echo esc_attr((string) $contactMail); ?>"
                                                                data-contact-tel1="<?php echo esc_attr((string) $contactTel1); ?>"
                                                                data-contact-tel2="<?php echo esc_attr((string) $contactTel2); ?>">
                                                                <span class="dashicons dashicons-edit" aria-hidden="true" style="margin-right:6px;"></span>
                                                                <?php esc_html_e('Voir et modifier le contact', 'gestiwork'); ?>
                                                            </button>
                                                            <form method="post" action="" style="margin:0;">
                                                                <input type="hidden" name="gw_action" value="gw_tier_contact_delete" />
                                                                <input type="hidden" name="tier_id" value="<?php echo (int) $clientId; ?>" />
                                                                <input type="hidden" name="contact_id" value="<?php echo (int) $contactIdRow; ?>" />
                                                                <?php wp_nonce_field('gw_tier_contact_manage', 'gw_nonce'); ?>
                                                                <button type="submit" role="menuitem" class="gw-link-button gw-contact-delete" style="width:100%; text-align:left; padding:8px 10px; border-radius:8px; color:#d63638;">
                                                                    <span class="dashicons dashicons-trash" aria-hidden="true" style="margin-right:6px;"></span>
                                                                    <?php esc_html_e('Supprimer le contact', 'gestiwork'); ?>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div style="margin-top: 8px; display:grid; gap:6px; color: var(--gw-color-text);">
                                                <?php if ($contactTel !== '') : ?>
                                                    <?php if (trim($contactTel1) !== '' && trim($contactTel2) !== '') : ?>
                                                        <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
                                                            <span style="display:inline-flex; align-items:center; gap:8px;">
                                                                <span class="dashicons dashicons-phone" aria-hidden="true" style="color: var(--gw-color-muted);"></span>
                                                                <span><?php echo esc_html(trim($contactTel1)); ?></span>
                                                            </span>
                                                            <span style="display:inline-flex; align-items:center; gap:8px;">
                                                                <span class="dashicons dashicons-phone" aria-hidden="true" style="color: var(--gw-color-muted);"></span>
                                                                <span><?php echo esc_html(trim($contactTel2)); ?></span>
                                                            </span>
                                                        </div>
                                                    <?php else : ?>
                                                        <div style="display:flex; align-items:center; gap:8px;">
                                                            <span class="dashicons dashicons-phone" aria-hidden="true" style="color: var(--gw-color-muted);"></span>
                                                            <span><?php echo esc_html($contactTel); ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endif; ?>

                                                <?php if ($contactMail !== '') : ?>
                                                    <div style="display:flex; align-items:center; gap:8px;">
                                                        <span class="dashicons dashicons-email" aria-hidden="true" style="color: var(--gw-color-muted);"></span>
                                                        <a href="mailto:<?php echo esc_attr($contactMail); ?>" style="text-decoration:none; color: inherit;">
                                                            <?php echo esc_html($contactMail); ?>
                                                        </a>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else : ?>
                                <div class="gw-mt-10 gw-color-muted-text gw-button-small">
                                    <?php if ($isCreate) : ?>
                                        <?php esc_html_e('Enregistrez d\'abord le client. Vous pourrez ensuite créer ses contacts depuis sa fiche (Clients > ouvrir le client).', 'gestiwork'); ?>
                                    <?php else : ?>
                                        <?php esc_html_e('Cette entreprise n\'a aucun contact client associé.', 'gestiwork'); ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if (! $isCreate) : ?>
        <div class="gw-settings-panel<?php echo $activeTab === 'historique_commercial' ? ' gw-settings-panel--active' : ''; ?>" data-gw-tab-panel="historique_commercial">
            <div class="gw-settings-group">
                <h3 class="gw-section-subtitle"><?php esc_html_e('Opportunités commerciales', 'gestiwork'); ?></h3>
                <p class="gw-section-description"><?php esc_html_e('Aucune opportunité commerciale associée', 'gestiwork'); ?></p>
            </div>

            <div class="gw-settings-group">
                <h3 class="gw-section-subtitle"><?php esc_html_e('Devis', 'gestiwork'); ?></h3>
                <div class="gw-table-wrapper">
                    <table class="gw-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('N° de devis', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Date d\'émission', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Échéance', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Statut', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Total HT', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Opportunité commerciale', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Modifié le', 'gestiwork'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><a href="#" onclick="return false;">D-2025-0003</a></td>
                                <td>02/03/2025</td>
                                <td>28/03/2025</td>
                                <td><?php esc_html_e('Brouillon', 'gestiwork'); ?></td>
                                <td>3 090,00 €</td>
                                <td>-</td>
                                <td>13/06/2025</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="gw-settings-group">
                <h3 class="gw-section-subtitle"><?php esc_html_e('Factures', 'gestiwork'); ?></h3>
                <div class="gw-table-wrapper">
                    <table class="gw-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('N° de facture', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Date d\'émission', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Échéance', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Statut', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Total HT', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Facturé le', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Facture réglée le', 'gestiwork'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><a href="#" onclick="return false;">F-2025-0012</a></td>
                                <td>15/06/2025</td>
                                <td>15/07/2025</td>
                                <td><?php esc_html_e('Émise', 'gestiwork'); ?></td>
                                <td>3 090,00 €</td>
                                <td>15/06/2025</td>
                                <td>-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="gw-settings-panel<?php echo $activeTab === 'apprenants' ? ' gw-settings-panel--active' : ''; ?>" data-gw-tab-panel="apprenants">
            <div class="gw-settings-group">
                <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;">
                    <h3 class="gw-section-subtitle" style="margin:0;"><?php esc_html_e('Liste des apprenants', 'gestiwork'); ?></h3>
                    <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap; justify-content:flex-end;">
                        <div style="position:relative; min-width:240px;">
                            <input type="text" class="gw-modal-input" placeholder="<?php esc_attr_e('Rechercher un apprenant', 'gestiwork'); ?>" style="padding-right:34px;" />
                            <span class="dashicons dashicons-search" aria-hidden="true" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); color: var(--gw-color-muted);"></span>
                        </div>
                        <button type="button" class="gw-button gw-button--primary" disabled>
                            <span class="dashicons dashicons-plus" aria-hidden="true"></span>
                            <?php esc_html_e('Ajouter un apprenant', 'gestiwork'); ?>
                        </button>
                    </div>
                </div>

                <div class="gw-table-wrapper" style="margin-top:10px;">
                    <table class="gw-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('ID', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Nom de l\'apprenant', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Adresse e-mail', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Numéro de téléphone', 'gestiwork'); ?></th>
                                <th style="text-align:right;"><?php esc_html_e('Actions', 'gestiwork'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($clientApprenantsCount === 0) : ?>
                                <tr>
                                    <td colspan="5" style="color: var(--gw-color-muted); font-style: italic;">
                                        <?php esc_html_e('Aucun apprenant enregistré', 'gestiwork'); ?>
                                    </td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($clientApprenants as $apprenantRow) : ?>
                                    <?php
                                    $apprenantIdRow = (int) ($apprenantRow['id'] ?? 0);
                                    $apprenantLabelId = $apprenantIdRow > 0 ? ('APP-' . str_pad((string) $apprenantIdRow, 3, '0', STR_PAD_LEFT)) : '';
                                    $apprenantNom = trim((string) ($apprenantRow['nom'] ?? ''));
                                    $apprenantPrenom = trim((string) ($apprenantRow['prenom'] ?? ''));
                                    $apprenantLabel = trim($apprenantPrenom . ' ' . $apprenantNom);
                                    $apprenantEmail = (string) ($apprenantRow['email'] ?? '');
                                    $apprenantTelephone = (string) ($apprenantRow['telephone'] ?? '');

                                    $apprenantViewUrl = add_query_arg([
                                        'gw_view' => 'Apprenant',
                                        'gw_apprenant_id' => $apprenantIdRow,
                                    ], home_url('/gestiwork/'));
                                    ?>
                                    <tr>
                                        <td><?php echo esc_html($apprenantLabelId); ?></td>
                                        <td><?php echo esc_html($apprenantLabel); ?></td>
                                        <td><?php echo esc_html($apprenantEmail); ?></td>
                                        <td><?php echo esc_html($apprenantTelephone); ?></td>
                                        <td style="text-align:right;">
                                            <?php if ($apprenantIdRow > 0) : ?>
                                                <a href="<?php echo esc_url($apprenantViewUrl); ?>"><?php esc_html_e('Voir', 'gestiwork'); ?></a>
                                            <?php else : ?>
                                                <a href="#" onclick="return false;">-</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap; margin-top:10px; color: var(--gw-color-muted); font-size: 12px;">
                    <div style="display:flex; gap:8px; align-items:center;">
                        <button type="button" class="gw-button gw-button--secondary" disabled>&laquo;</button>
                        <button type="button" class="gw-button gw-button--secondary" disabled>&lsaquo;</button>
                        <button type="button" class="gw-button gw-button--secondary" disabled>1</button>
                        <button type="button" class="gw-button gw-button--secondary" disabled>&rsaquo;</button>
                        <button type="button" class="gw-button gw-button--secondary" disabled>&raquo;</button>
                    </div>
                    <div style="display:flex; gap:10px; align-items:center; justify-content:flex-end;">
                        <span>
                            <?php
                            $from = $clientApprenantsCount > 0 ? 1 : 0;
                            $to = $clientApprenantsCount;
                            echo esc_html(sprintf('Montrer %d à %d de %d lignes', $from, $to, $clientApprenantsCount));
                            ?>
                        </span>
                        <select class="gw-modal-input" style="width:auto;">
                            <option selected>15</option>
                            <option>30</option>
                            <option>50</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="gw-settings-panel<?php echo $activeTab === 'historique_sessions' ? ' gw-settings-panel--active' : ''; ?>" data-gw-tab-panel="historique_sessions">
            <div class="gw-settings-group">
                <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;">
                    <h3 class="gw-section-subtitle" style="margin:0;"><?php esc_html_e('Liste des sessions de formation', 'gestiwork'); ?></h3>
                    <div style="position:relative; min-width:260px;">
                        <input type="text" class="gw-modal-input" placeholder="<?php esc_attr_e('Rechercher une session', 'gestiwork'); ?>" style="padding-right:34px;" />
                        <span class="dashicons dashicons-search" aria-hidden="true" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); color: var(--gw-color-muted);"></span>
                    </div>
                </div>

                <div class="gw-table-wrapper" style="margin-top:10px;">
                    <table class="gw-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('ID', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Nom de la session de formation', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Début', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Fin', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Statut', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Modifié le', 'gestiwork'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" style="color: var(--gw-color-muted); font-style: italic;">
                                    <?php esc_html_e('Aucune session enregistrée', 'gestiwork'); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap; margin-top:10px; color: var(--gw-color-muted); font-size: 12px;">
                    <div style="display:flex; gap:8px; align-items:center;">
                        <button type="button" class="gw-button gw-button--secondary" disabled>&laquo;</button>
                        <button type="button" class="gw-button gw-button--secondary" disabled>&lsaquo;</button>
                        <button type="button" class="gw-button gw-button--secondary" disabled>1</button>
                        <button type="button" class="gw-button gw-button--secondary" disabled>&rsaquo;</button>
                        <button type="button" class="gw-button gw-button--secondary" disabled>&raquo;</button>
                    </div>
                    <div style="display:flex; gap:10px; align-items:center; justify-content:flex-end;">
                        <span><?php esc_html_e('Montrer 0 à 0 de 0 lignes', 'gestiwork'); ?></span>
                        <select class="gw-modal-input" style="width:auto;">
                            <option selected>15</option>
                            <option>30</option>
                            <option>50</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="gw-settings-panel<?php echo $activeTab === 'taches_activites' ? ' gw-settings-panel--active' : ''; ?>" data-gw-tab-panel="taches_activites">
            <div class="gw-settings-group">
                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 14px;">
                    <div style="border:1px solid var(--gw-color-border); border-radius: 12px; background:#fff; padding: 12px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
                            <h3 class="gw-section-subtitle gw-m-0"><?php esc_html_e('Historique d\'activités', 'gestiwork'); ?></h3>
                            <a href="#" onclick="return false;" style="text-decoration:none; font-size:13px;">
                                <span class="dashicons dashicons-plus" aria-hidden="true"></span>
                                <?php esc_html_e('Ajouter une note', 'gestiwork'); ?>
                            </a>
                        </div>
                        <div style="margin-top:10px; display:grid; gap:10px;">
                            <div style="border:1px solid var(--gw-color-border); border-radius: 10px; padding: 10px;">
                                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:10px;">
                                    <div style="display:flex; gap:8px; align-items:flex-start;">
                                        <span class="dashicons dashicons-admin-comments" aria-hidden="true"></span>
                                        <div>
                                            <div style="font-weight: var(--gw-font-weight-medium);">test</div>
                                        </div>
                                    </div>
                                    <span class="dashicons dashicons-ellipsis" aria-hidden="true" style="color: var(--gw-color-muted);"></span>
                                </div>
                                <div style="margin-top: 8px; color: var(--gw-color-muted); font-size: 12px;">
                                    <?php esc_html_e('Créée le 12/12/2025 - par fabrice LAURET', 'gestiwork'); ?>
                                </div>
                                <div style="margin-top: 6px; color: var(--gw-color-muted); font-size: 12px;">
                                    <?php esc_html_e('1 association', 'gestiwork'); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="border:1px solid var(--gw-color-border); border-radius: 12px; background:#fff; padding: 12px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
                            <h3 class="gw-section-subtitle gw-m-0"><?php esc_html_e('Tâches à venir', 'gestiwork'); ?></h3>
                            <a href="#" onclick="return false;" style="text-decoration:none; font-size:13px;">
                                <span class="dashicons dashicons-plus" aria-hidden="true"></span>
                                <?php esc_html_e('Ajouter une tâche', 'gestiwork'); ?>
                            </a>
                        </div>
                        <div style="margin-top:10px; display:grid; gap:10px;">
                            <div style="border:1px solid var(--gw-color-border); border-radius: 10px; padding: 10px;">
                                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:10px;">
                                    <div style="display:flex; gap:8px; align-items:flex-start;">
                                        <span class="dashicons dashicons-marker" aria-hidden="true" style="color: var(--gw-color-muted);"></span>
                                        <div>
                                            <div style="color:#d92d20; font-size:12px; font-weight: var(--gw-font-weight-medium);">01/03/2024 · <?php esc_html_e('Échéance dépassée', 'gestiwork'); ?></div>
                                            <div style="margin-top:4px; font-weight: var(--gw-font-weight-medium); color:#d92d20;">Relancer prospect - Signature devis de formation</div>
                                            <div style="margin-top:6px; color: var(--gw-color-muted); font-size: 12px;">
                                                <?php esc_html_e('Créée le 21/09/2023 - Tâche non attribuée', 'gestiwork'); ?>
                                            </div>
                                            <div style="margin-top: 6px; color: var(--gw-color-muted); font-size: 12px;">
                                                <?php esc_html_e('13 associations', 'gestiwork'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="dashicons dashicons-ellipsis" aria-hidden="true" style="color: var(--gw-color-muted);"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php if (! $isCreate && $clientId > 0) : ?>

<div class="gw-modal-backdrop" id="gw-modal-client-contacts" aria-hidden="true">
    <div class="gw-modal gw-modal" role="dialog" aria-modal="true" aria-labelledby="gw-modal-client-contacts-title">
        <div class="gw-modal-header">
            <h3 class="gw-modal-title" id="gw-modal-client-contacts-title"><?php esc_html_e('Associer des contacts clients', 'gestiwork'); ?></h3>
            <button type="button" class="gw-modal-close" data-gw-modal-close="gw-modal-client-contacts" aria-label="<?php esc_attr_e('Fermer', 'gestiwork'); ?>">×</button>
        </div>
        <form method="post" action="">
            <input type="hidden" name="gw_action" value="gw_tier_contact_create" />
            <input type="hidden" name="tier_id" value="<?php echo (int) $clientId; ?>" />
            <?php wp_nonce_field('gw_tier_contact_manage', 'gw_nonce'); ?>
            <div class="gw-modal-body">
                <div class="gw-modal-grid">
                    <div class="gw-modal-field">
                        <label for="gw_client_contact_civilite"><?php esc_html_e('Civilité', 'gestiwork'); ?></label>
                        <select id="gw_client_contact_civilite" name="civilite" class="gw-modal-input">
                            <option value="non_renseigne" selected><?php esc_html_e('Non renseigné', 'gestiwork'); ?></option>
                            <option value="madame"><?php esc_html_e('Madame', 'gestiwork'); ?></option>
                            <option value="monsieur"><?php esc_html_e('Monsieur', 'gestiwork'); ?></option>
                        </select>
                    </div>
                    <div class="gw-modal-field">
                        <label for="gw_client_contact_fonction"><?php esc_html_e('Fonction', 'gestiwork'); ?></label>
                        <input type="text" id="gw_client_contact_fonction" name="fonction" class="gw-modal-input" value="" />
                    </div>
                    <div class="gw-modal-field">
                        <label for="gw_client_contact_nom"><?php esc_html_e('Nom', 'gestiwork'); ?></label>
                        <input type="text" id="gw_client_contact_nom" name="nom" class="gw-modal-input" value="" required />
                    </div>
                    <div class="gw-modal-field">
                        <label for="gw_client_contact_prenom"><?php esc_html_e('Prénom', 'gestiwork'); ?></label>
                        <input type="text" id="gw_client_contact_prenom" name="prenom" class="gw-modal-input" value="" required />
                    </div>
                    <div class="gw-modal-field gw-full-width">
                        <label for="gw_client_contact_mail"><?php esc_html_e('Mail', 'gestiwork'); ?></label>
                        <input type="email" id="gw_client_contact_mail" name="mail" class="gw-modal-input" value="" required />
                    </div>
                    <div class="gw-modal-field">
                        <label for="gw_client_contact_tel1"><?php esc_html_e('Numéro de téléphone 1', 'gestiwork'); ?></label>
                        <input type="text" id="gw_client_contact_tel1" name="tel1" class="gw-modal-input" value="" pattern="[0-9]{2}( [0-9]{2}){4}" placeholder="00 00 00 00 00" />
                    </div>
                    <div class="gw-modal-field">
                        <label for="gw_client_contact_tel2"><?php esc_html_e('Numéro de téléphone 2', 'gestiwork'); ?></label>
                        <input type="text" id="gw_client_contact_tel2" name="tel2" class="gw-modal-input" value="" pattern="[0-9]{2}( [0-9]{2}){4}" placeholder="00 00 00 00 00" />
                    </div>
                </div>
            </div>
            <p id="gw_client_contact_error" class="gw-modal-error-info gw-display-none"></p>
            <div class="gw-modal-footer">
                <button type="button" class="gw-button gw-button--secondary" data-gw-modal-close="gw-modal-client-contacts"><?php esc_html_e('Annuler', 'gestiwork'); ?></button>
                <button type="submit" class="gw-button gw-button--primary">
                    <?php esc_html_e('Créer', 'gestiwork'); ?>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="gw-modal-backdrop" id="gw-modal-client-contact-edit" aria-hidden="true">
    <div class="gw-modal gw-modal" role="dialog" aria-modal="true" aria-labelledby="gw-modal-client-contact-edit-title">
        <div class="gw-modal-header">
            <h3 class="gw-modal-title" id="gw-modal-client-contact-edit-title"><?php esc_html_e('Voir et modifier le contact', 'gestiwork'); ?></h3>
            <button type="button" class="gw-modal-close" data-gw-modal-close="gw-modal-client-contact-edit" aria-label="<?php esc_attr_e('Fermer', 'gestiwork'); ?>">×</button>
        </div>
        <form method="post" action="">
            <input type="hidden" name="gw_action" value="gw_tier_contact_update" />
            <input type="hidden" name="tier_id" value="<?php echo (int) $clientId; ?>" />
            <input type="hidden" name="contact_id" id="gw_client_contact_edit_id" value="0" />
            <?php wp_nonce_field('gw_tier_contact_manage', 'gw_nonce'); ?>
            <div class="gw-modal-body">
                <div class="gw-modal-grid">
                    <div class="gw-modal-field">
                        <label for="gw_client_contact_edit_civilite"><?php esc_html_e('Civilité', 'gestiwork'); ?></label>
                        <select id="gw_client_contact_edit_civilite" name="civilite" class="gw-modal-input">
                            <option value="non_renseigne"><?php esc_html_e('Non renseigné', 'gestiwork'); ?></option>
                            <option value="madame"><?php esc_html_e('Madame', 'gestiwork'); ?></option>
                            <option value="monsieur"><?php esc_html_e('Monsieur', 'gestiwork'); ?></option>
                        </select>
                    </div>
                    <div class="gw-modal-field">
                        <label for="gw_client_contact_edit_fonction"><?php esc_html_e('Fonction', 'gestiwork'); ?></label>
                        <input type="text" id="gw_client_contact_edit_fonction" name="fonction" class="gw-modal-input" value="" />
                    </div>
                    <div class="gw-modal-field">
                        <label for="gw_client_contact_edit_nom"><?php esc_html_e('Nom', 'gestiwork'); ?></label>
                        <input type="text" id="gw_client_contact_edit_nom" name="nom" class="gw-modal-input" value="" required />
                    </div>
                    <div class="gw-modal-field">
                        <label for="gw_client_contact_edit_prenom"><?php esc_html_e('Prénom', 'gestiwork'); ?></label>
                        <input type="text" id="gw_client_contact_edit_prenom" name="prenom" class="gw-modal-input" value="" required />
                    </div>
                    <div class="gw-modal-field gw-full-width">
                        <label for="gw_client_contact_edit_mail"><?php esc_html_e('Mail', 'gestiwork'); ?></label>
                        <input type="email" id="gw_client_contact_edit_mail" name="mail" class="gw-modal-input" value="" required />
                    </div>
                    <div class="gw-modal-field">
                        <label for="gw_client_contact_edit_tel1"><?php esc_html_e('Numéro de téléphone 1', 'gestiwork'); ?></label>
                        <input type="text" id="gw_client_contact_edit_tel1" name="tel1" class="gw-modal-input" value="" pattern="[0-9]{2}( [0-9]{2}){4}" placeholder="00 00 00 00 00" />
                    </div>
                    <div class="gw-modal-field">
                        <label for="gw_client_contact_edit_tel2"><?php esc_html_e('Numéro de téléphone 2', 'gestiwork'); ?></label>
                        <input type="text" id="gw_client_contact_edit_tel2" name="tel2" class="gw-modal-input" value="" pattern="[0-9]{2}( [0-9]{2}){4}" placeholder="00 00 00 00 00" />
                    </div>
                </div>
            </div>
            <p id="gw_client_contact_edit_error" class="gw-modal-error-info gw-display-none"></p>
            <div class="gw-modal-footer">
                <button type="button" class="gw-button gw-button--secondary" data-gw-modal-close="gw-modal-client-contact-edit"><?php esc_html_e('Annuler', 'gestiwork'); ?></button>
                <button type="submit" class="gw-button gw-button--primary">
                    <?php esc_html_e('Enregistrer', 'gestiwork'); ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?php else : ?>
    <p><?php esc_html_e('Enregistrez d\'abord le client. Vous pourrez ensuite créer ses contacts depuis sa fiche (Clients > ouvrir le client).', 'gestiwork'); ?></p>
<?php endif; ?>
