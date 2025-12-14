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

use GestiWork\Domain\Tiers\TierProvider;
use GestiWork\Domain\Tiers\TierContactProvider;

if (! current_user_can('manage_options')) {
    wp_die(esc_html__('Accès non autorisé.', 'gestiwork'), 403);
}

$clientId = isset($_GET['gw_tier_id']) ? (int) $_GET['gw_tier_id'] : 0;
$mode = isset($_GET['mode']) ? (string) $_GET['mode'] : '';

$isCreate = ($mode === 'create');
$isEdit = ($mode === 'edit');

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

$postError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['gw_action']) ? (string) $_POST['gw_action'] : '';
    $action = strtolower(trim($action));

    if ($action === 'gw_tier_create') {
        if (!isset($_POST['gw_nonce']) || !wp_verify_nonce((string) $_POST['gw_nonce'], 'gw_tier_create')) {
            $postError = 'nonce';
        } else {
            $data = [
                'type' => isset($_POST['type']) ? sanitize_text_field((string) $_POST['type']) : 'client_particulier',
                'statut' => isset($_POST['statut']) ? sanitize_text_field((string) $_POST['statut']) : 'client',
                'raison_sociale' => isset($_POST['raison_sociale']) ? sanitize_text_field((string) $_POST['raison_sociale']) : '',
                'nom' => isset($_POST['nom']) ? sanitize_text_field((string) $_POST['nom']) : '',
                'prenom' => isset($_POST['prenom']) ? sanitize_text_field((string) $_POST['prenom']) : '',
                'siret' => isset($_POST['siret']) ? sanitize_text_field((string) $_POST['siret']) : '',
                'forme_juridique' => isset($_POST['forme_juridique']) ? sanitize_text_field((string) $_POST['forme_juridique']) : '',
                'email' => isset($_POST['email']) ? sanitize_email((string) $_POST['email']) : '',
                'telephone' => isset($_POST['telephone']) ? sanitize_text_field((string) $_POST['telephone']) : '',
                'telephone_portable' => isset($_POST['telephone_portable']) ? sanitize_text_field((string) $_POST['telephone_portable']) : '',
                'adresse1' => isset($_POST['adresse1']) ? sanitize_text_field((string) $_POST['adresse1']) : '',
                'adresse2' => isset($_POST['adresse2']) ? sanitize_text_field((string) $_POST['adresse2']) : '',
                'cp' => isset($_POST['cp']) ? sanitize_text_field((string) $_POST['cp']) : '',
                'ville' => isset($_POST['ville']) ? sanitize_text_field((string) $_POST['ville']) : '',
            ];

            $newId = TierProvider::create($data);
            if ($newId > 0) {
                $redirectUrl = add_query_arg([
                    'gw_view' => 'Client',
                    'gw_tier_id' => $newId,
                    'gw_notice' => 'tier_created',
                ], home_url('/gestiwork/'));
                wp_safe_redirect($redirectUrl);
                exit;
            }

            $postError = 'create_failed';
        }
    }

    if ($action === 'gw_tier_update') {
        if (!isset($_POST['gw_nonce']) || !wp_verify_nonce((string) $_POST['gw_nonce'], 'gw_tier_update')) {
            $postError = 'nonce';
        } else {
            $tierId = isset($_POST['tier_id']) ? (int) $_POST['tier_id'] : 0;
            if ($tierId <= 0) {
                $postError = 'invalid_id';
            } else {
                $data = [
                    'type' => isset($_POST['type']) ? sanitize_text_field((string) $_POST['type']) : 'client_particulier',
                    'statut' => isset($_POST['statut']) ? sanitize_text_field((string) $_POST['statut']) : 'client',
                    'raison_sociale' => isset($_POST['raison_sociale']) ? sanitize_text_field((string) $_POST['raison_sociale']) : '',
                    'nom' => isset($_POST['nom']) ? sanitize_text_field((string) $_POST['nom']) : '',
                    'prenom' => isset($_POST['prenom']) ? sanitize_text_field((string) $_POST['prenom']) : '',
                    'siret' => isset($_POST['siret']) ? sanitize_text_field((string) $_POST['siret']) : '',
                    'forme_juridique' => isset($_POST['forme_juridique']) ? sanitize_text_field((string) $_POST['forme_juridique']) : '',
                    'email' => isset($_POST['email']) ? sanitize_email((string) $_POST['email']) : '',
                    'telephone' => isset($_POST['telephone']) ? sanitize_text_field((string) $_POST['telephone']) : '',
                    'telephone_portable' => isset($_POST['telephone_portable']) ? sanitize_text_field((string) $_POST['telephone_portable']) : '',
                    'adresse1' => isset($_POST['adresse1']) ? sanitize_text_field((string) $_POST['adresse1']) : '',
                    'adresse2' => isset($_POST['adresse2']) ? sanitize_text_field((string) $_POST['adresse2']) : '',
                    'cp' => isset($_POST['cp']) ? sanitize_text_field((string) $_POST['cp']) : '',
                    'ville' => isset($_POST['ville']) ? sanitize_text_field((string) $_POST['ville']) : '',
                ];

                $ok = TierProvider::update($tierId, $data);
                if ($ok) {
                    $redirectUrl = add_query_arg([
                        'gw_view' => 'Client',
                        'gw_tier_id' => $tierId,
                        'gw_notice' => 'tier_updated',
                    ], home_url('/gestiwork/'));
                    wp_safe_redirect($redirectUrl);
                    exit;
                }

                $postError = 'update_failed';
            }
        }
    }

    if ($action === 'gw_tier_contact_create') {
        if (!isset($_POST['gw_nonce']) || !wp_verify_nonce((string) $_POST['gw_nonce'], 'gw_tier_contact_create')) {
            $postError = 'nonce';
        } else {
            $tierId = isset($_POST['tier_id']) ? (int) $_POST['tier_id'] : 0;
            if ($tierId <= 0) {
                $postError = 'invalid_id';
            } else {
                $data = [
                    'civilite' => isset($_POST['civilite']) ? sanitize_text_field((string) $_POST['civilite']) : 'non_renseigne',
                    'fonction' => isset($_POST['fonction']) ? sanitize_text_field((string) $_POST['fonction']) : '',
                    'nom' => isset($_POST['nom']) ? sanitize_text_field((string) $_POST['nom']) : '',
                    'prenom' => isset($_POST['prenom']) ? sanitize_text_field((string) $_POST['prenom']) : '',
                    'mail' => isset($_POST['mail']) ? sanitize_email((string) $_POST['mail']) : '',
                    'tel1' => isset($_POST['tel1']) ? sanitize_text_field((string) $_POST['tel1']) : '',
                    'tel2' => isset($_POST['tel2']) ? sanitize_text_field((string) $_POST['tel2']) : '',
                ];

                $newContactId = TierContactProvider::create($tierId, $data);
                if ($newContactId > 0) {
                    $redirectUrl = add_query_arg([
                        'gw_view' => 'Client',
                        'gw_tier_id' => $tierId,
                        'gw_notice' => 'contact_created',
                    ], home_url('/gestiwork/'));
                    wp_safe_redirect($redirectUrl);
                    exit;
                }

                $postError = 'contact_create_failed';
            }
        }
    }
}

$tierContacts = [];
if (is_array($dbClientData) && $clientId > 0) {
    $tierContacts = TierContactProvider::listByTierId($clientId);
}

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
        <div class="notice notice-success" style="margin: 12px 0;">
            <p><?php esc_html_e('Tiers créé avec succès.', 'gestiwork'); ?></p>
        </div>
    <?php elseif ($tierNotice === 'tier_updated') : ?>
        <div class="notice notice-success" style="margin: 12px 0;">
            <p><?php esc_html_e('Tiers mis à jour avec succès.', 'gestiwork'); ?></p>
        </div>
    <?php elseif ($tierNotice === 'contact_created') : ?>
        <div class="notice notice-success" style="margin: 12px 0;">
            <p><?php esc_html_e('Contact ajouté avec succès.', 'gestiwork'); ?></p>
        </div>
    <?php elseif ($postError !== '') : ?>
        <div class="notice notice-error" style="margin: 12px 0;">
            <p><?php esc_html_e('Une erreur est survenue lors de l\'enregistrement.', 'gestiwork'); ?></p>
        </div>
    <?php endif; ?>

    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap;">
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
        <div style="display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end;">
            <a class="gw-button gw-button--secondary" href="<?php echo esc_url($backUrl); ?>">
                <?php esc_html_e('Retour aux tiers', 'gestiwork'); ?>
            </a>
            <?php if (! $isEdit && ! $isCreate) : ?>
                <a class="gw-button gw-button--primary" href="<?php echo esc_url($editUrl); ?>">
                    <?php esc_html_e('Modifier', 'gestiwork'); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="gw-settings-tabs" role="tablist">
        <button type="button" class="gw-settings-tab<?php echo $activeTab === 'informations_generales' ? ' gw-settings-tab--active' : ''; ?>" data-gw-tab="informations_generales">
            <?php esc_html_e('Informations générales', 'gestiwork'); ?>
        </button>
        <?php if (! $isCreate) : ?>
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
        <?php endif; ?>
    </div>

    <div class="gw-settings-panels">
        <div class="gw-settings-panel<?php echo $activeTab === 'informations_generales' ? ' gw-settings-panel--active' : ''; ?>" data-gw-tab-panel="informations_generales">
            <?php if ($isCreate) : ?>
                <div class="gw-tier-info-layout" style="display:grid; gap: 14px; align-items:start;">
                    <div class="gw-settings-group" style="margin:0;">
                        <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;">
                            <h3 class="gw-section-subtitle" style="margin:0;"><?php esc_html_e('Informations générales', 'gestiwork'); ?></h3>
                            <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap; justify-content:flex-end;">
                                <a href="#" onclick="return false;" style="text-decoration:none; font-size:13px;">
                                    <span class="dashicons dashicons-search" aria-hidden="true"></span>
                                    <?php esc_html_e('Rechercher dans la base de l\'INSEE', 'gestiwork'); ?>
                                </a>
                            </div>
                        </div>

                        <form method="post" action="" style="margin-top: 12px;">
                            <input type="hidden" name="gw_action" value="gw_tier_create" />
                            <?php wp_nonce_field('gw_tier_create', 'gw_nonce'); ?>
                            <div style="display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px;">
                                <div>
                                    <label class="gw-settings-placeholder" for="gw_tier_create_type"><?php esc_html_e('Catégorie', 'gestiwork'); ?></label>
                                    <select id="gw_tier_create_type" name="type" class="gw-modal-input">
                                        <option value="entreprise"><?php esc_html_e('Entreprise', 'gestiwork'); ?></option>
                                        <option value="client_particulier" selected><?php esc_html_e('Particulier', 'gestiwork'); ?></option>
                                        <option value="financeur"><?php esc_html_e('Financeur / OPCO', 'gestiwork'); ?></option>
                                        <option value="of_donneur_ordre"><?php esc_html_e('OF donneur d\'ordre', 'gestiwork'); ?></option>
                                    </select>
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_tier_create_statut"><?php esc_html_e('Statut', 'gestiwork'); ?></label>
                                    <select id="gw_tier_create_statut" name="statut" class="gw-modal-input">
                                        <option value="prospect"><?php esc_html_e('Prospect', 'gestiwork'); ?></option>
                                        <option value="client" selected><?php esc_html_e('Client', 'gestiwork'); ?></option>
                                    </select>
                                </div>

                                <div id="gw_tier_create_field_raison_sociale" style="grid-column: 1 / -1;">
                                    <label class="gw-settings-placeholder" for="gw_tier_create_raison_sociale"><?php esc_html_e('Nom / Raison sociale', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_tier_create_raison_sociale" name="raison_sociale" class="gw-modal-input" placeholder="Groupe BB - siège social Beaux Bâtons" />
                                </div>

                                <div id="gw_tier_create_field_nom">
                                    <label class="gw-settings-placeholder" for="gw_tier_create_nom"><?php esc_html_e('Nom', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_tier_create_nom" name="nom" class="gw-modal-input" placeholder="DUPONT" />
                                </div>

                                <div id="gw_tier_create_field_prenom">
                                    <label class="gw-settings-placeholder" for="gw_tier_create_prenom"><?php esc_html_e('Prénom', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_tier_create_prenom" name="prenom" class="gw-modal-input" placeholder="Jean" />
                                </div>

                                <div id="gw_tier_create_field_siret" style="grid-column: 1 / -1;">
                                    <label class="gw-settings-placeholder" for="gw_tier_create_siret"><?php esc_html_e('SIRET / SIREN', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_tier_create_siret" name="siret" class="gw-modal-input" placeholder="007007000777" />
                                </div>

                                <div>
                                    <label class="gw-settings-placeholder" for="gw_tier_create_email"><?php esc_html_e('Adresse e-mail', 'gestiwork'); ?></label>
                                    <input type="email" id="gw_tier_create_email" name="email" class="gw-modal-input" placeholder="contact@bb-batons.fr" />
                                </div>
                                <div>
                                    <label class="gw-settings-placeholder" for="gw_tier_create_phone"><?php esc_html_e('Numéro de téléphone', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_tier_create_phone" name="telephone" class="gw-modal-input" placeholder="01 52 63 41 52" />
                                </div>
                                <div>
                                    <label class="gw-settings-placeholder" for="gw_tier_create_phone_mobile"><?php esc_html_e('Téléphone portable', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_tier_create_phone_mobile" name="telephone_portable" class="gw-modal-input" placeholder="06 12 34 56 78" />
                                </div>

                                <div style="grid-column: 1 / -1;">
                                    <label class="gw-settings-placeholder" for="gw_tier_create_adresse1"><?php esc_html_e('Numéro de rue et rue', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_tier_create_adresse1" name="adresse1" class="gw-modal-input" placeholder="1 chemin de traverse" />
                                </div>
                                <div style="grid-column: 1 / -1;">
                                    <label class="gw-settings-placeholder" for="gw_tier_create_adresse2"><?php esc_html_e('Complément d\'adresse', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_tier_create_adresse2" name="adresse2" class="gw-modal-input" placeholder="ex. 3e étage, BP 456" />
                                </div>
                                <div>
                                    <label class="gw-settings-placeholder" for="gw_tier_create_cp"><?php esc_html_e('Code postal', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_tier_create_cp" name="cp" class="gw-modal-input" placeholder="75000" />
                                </div>
                                <div>
                                    <label class="gw-settings-placeholder" for="gw_tier_create_ville"><?php esc_html_e('Ville', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_tier_create_ville" name="ville" class="gw-modal-input" placeholder="PARIS" />
                                </div>

                                <div id="gw_tier_create_field_forme_juridique">
                                    <label class="gw-settings-placeholder" for="gw_tier_create_forme_juridique"><?php esc_html_e('Forme juridique', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_tier_create_forme_juridique" name="forme_juridique" class="gw-modal-input" placeholder="SAS, SARL, EI..." />
                                </div>

                                <div style="grid-column: 1 / -1; margin-top: 6px;">
                                    <button type="submit" class="gw-button gw-button--primary">
                                        <?php esc_html_e('Enregistrer', 'gestiwork'); ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div style="display:grid; gap:14px;">
                        <div style="border:1px solid var(--gw-color-border); border-radius: 12px; background:#fff; padding: 12px;">
                            <div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
                                <h3 class="gw-section-subtitle" style="margin:0;"><?php esc_html_e('Contacts clients', 'gestiwork'); ?></h3>
                                <a href="#" onclick="return false;" data-gw-modal-target="gw-modal-client-contacts" style="text-decoration:none; font-size:13px;">
                                    <span class="dashicons dashicons-plus" aria-hidden="true"></span>
                                    <?php esc_html_e('Associer des contacts clients', 'gestiwork'); ?>
                                </a>
                            </div>
                            <?php if (is_array($tierContacts) && count($tierContacts) > 0) : ?>
                                <div style="margin-top: 10px; display:grid; gap:6px; font-size: 13px;">
                                    <?php foreach ($tierContacts as $index => $contact) : ?>
                                        <?php
                                        $contactNom = isset($contact['nom']) ? (string) $contact['nom'] : '';
                                        $contactPrenom = isset($contact['prenom']) ? (string) $contact['prenom'] : '';
                                        $contactFonction = isset($contact['fonction']) ? (string) $contact['fonction'] : '';
                                        $contactMail = isset($contact['mail']) ? (string) $contact['mail'] : '';
                                        $contactTel1 = isset($contact['tel1']) ? (string) $contact['tel1'] : '';
                                        $contactTel2 = isset($contact['tel2']) ? (string) $contact['tel2'] : '';
                                        $contactTel = trim($contactTel1);
                                        if ($contactTel === '') {
                                            $contactTel = trim($contactTel2);
                                        }
                                        $contactLabel = trim($contactPrenom . ' ' . $contactNom);
                                        $contactMeta = trim($contactFonction);
                                        $isDefaultContact = ($index === 0);
                                        ?>
                                        <div style="border:1px solid var(--gw-color-border); border-radius:12px; padding:12px; background:#f6f7f7;">
                                            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:10px;">
                                                <div style="min-width:0;">
                                                    <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                                                        <a href="#" onclick="return false;" style="text-decoration:none; font-weight:600; color: var(--gw-color-primary);">
                                                            <?php echo esc_html($contactLabel !== '' ? $contactLabel : '-'); ?>
                                                        </a>
                                                        <span class="dashicons dashicons-external" aria-hidden="true" style="font-size:16px; line-height:1; color: var(--gw-color-primary);"></span>
                                                    </div>
                                                </div>
                                                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; justify-content:flex-end;">
                                                    <?php if ($isDefaultContact) : ?>
                                                        <span style="display:inline-flex; align-items:center; padding:4px 10px; border-radius:8px; border:1px solid #c7b9ff; background:#f4f1ff; color:#5b47ff; font-size:12px; font-weight:500; white-space:nowrap;">
                                                            <?php esc_html_e('Contact par défaut', 'gestiwork'); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    <button type="button" class="gw-button gw-button--secondary" style="padding:4px 8px; line-height:1;" onclick="return false;" aria-label="<?php esc_attr_e('Actions', 'gestiwork'); ?>">
                                                        <span class="dashicons dashicons-ellipsis" aria-hidden="true"></span>
                                                    </button>
                                                </div>
                                            </div>

                                            <div style="margin-top: 8px; display:grid; gap:6px; color: var(--gw-color-text);">
                                                <?php if ($contactMeta !== '') : ?>
                                                    <div style="display:flex; align-items:center; gap:8px;">
                                                        <span class="dashicons dashicons-briefcase" aria-hidden="true" style="color: var(--gw-color-muted);"></span>
                                                        <span><?php echo esc_html($contactMeta); ?></span>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if ($contactTel !== '') : ?>
                                                    <div style="display:flex; align-items:center; gap:8px;">
                                                        <span class="dashicons dashicons-phone" aria-hidden="true" style="color: var(--gw-color-muted);"></span>
                                                        <span><?php echo esc_html($contactTel); ?></span>
                                                    </div>
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
                                <div style="margin-top: 10px; color: var(--gw-color-muted); font-size: 13px;">
                                    <?php esc_html_e('Cette entreprise n\'a aucun contact client associé.', 'gestiwork'); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <div class="gw-tier-info-layout" style="display:grid; gap: 14px; align-items:start;">
                    <div class="gw-settings-group" style="margin:0;">
                        <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;">
                            <h3 class="gw-section-subtitle" style="margin:0;"><?php esc_html_e('Informations générales', 'gestiwork'); ?></h3>
                        </div>

                        <form method="post" action="" style="margin-top: 12px;">
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

                                <div id="gw_tier_view_field_raison_sociale" style="grid-column: 1 / -1;">
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

                                <div id="gw_tier_view_field_siret" style="grid-column: 1 / -1;">
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

                                <div style="grid-column: 1 / -1;">
                                    <label class="gw-settings-placeholder" for="gw_tier_view_adresse1"><?php esc_html_e('Numéro de rue et rue', 'gestiwork'); ?></label>
                                    <input type="text" id="gw_tier_view_adresse1" name="adresse1" class="gw-modal-input" value="<?php echo esc_attr($clientData['adresse1']); ?>"<?php echo $isEdit ? '' : ' disabled'; ?> />
                                </div>
                                <div style="grid-column: 1 / -1;">
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
                                    <div style="grid-column: 1 / -1; margin-top: 6px; display:flex; gap:8px; flex-wrap:wrap;">
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
                        <div style="border:1px solid var(--gw-color-border); border-radius: 12px; background:#fff; padding: 12px;">
                            <h3 class="gw-section-subtitle" style="margin:0;"><?php esc_html_e('Aucune tâche programmée', 'gestiwork'); ?></h3>
                            <div style="margin-top: 8px;">
                                <a href="#" onclick="return false;" style="text-decoration:none; font-size:13px;">
                                    <?php esc_html_e('Voir les tâches', 'gestiwork'); ?>
                                </a>
                            </div>
                        </div>

                        <div style="border:1px solid var(--gw-color-border); border-radius: 12px; background:#fff; padding: 12px;">
                            <div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
                                <h3 class="gw-section-subtitle" style="margin:0;"><?php esc_html_e('Contacts clients', 'gestiwork'); ?></h3>
                                <a href="#" onclick="return false;" data-gw-modal-target="gw-modal-client-contacts" style="text-decoration:none; font-size:13px;">
                                    <span class="dashicons dashicons-plus" aria-hidden="true"></span>
                                    <?php esc_html_e('Associer des contacts clients', 'gestiwork'); ?>
                                </a>
                            </div>
                            <?php if (is_array($tierContacts) && count($tierContacts) > 0) : ?>
                                <div style="margin-top: 10px; display:grid; gap:6px; font-size: 13px;">
                                    <?php foreach ($tierContacts as $index => $contact) : ?>
                                        <?php
                                        $contactNom = isset($contact['nom']) ? (string) $contact['nom'] : '';
                                        $contactPrenom = isset($contact['prenom']) ? (string) $contact['prenom'] : '';
                                        $contactFonction = isset($contact['fonction']) ? (string) $contact['fonction'] : '';
                                        $contactMail = isset($contact['mail']) ? (string) $contact['mail'] : '';
                                        $contactTel1 = isset($contact['tel1']) ? (string) $contact['tel1'] : '';
                                        $contactTel2 = isset($contact['tel2']) ? (string) $contact['tel2'] : '';
                                        $contactTel = trim($contactTel1);
                                        if ($contactTel === '') {
                                            $contactTel = trim($contactTel2);
                                        }
                                        $contactLabel = trim($contactPrenom . ' ' . $contactNom);
                                        $contactMeta = trim($contactFonction);
                                        $isDefaultContact = ($index === 0);
                                        ?>
                                        <div style="border:1px solid var(--gw-color-border); border-radius:12px; padding:12px; background:#f6f7f7;">
                                            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:10px;">
                                                <div style="min-width:0;">
                                                    <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                                                        <a href="#" onclick="return false;" style="text-decoration:none; font-weight:600; color: var(--gw-color-primary);">
                                                            <?php echo esc_html($contactLabel !== '' ? $contactLabel : '-'); ?>
                                                        </a>
                                                        <span class="dashicons dashicons-external" aria-hidden="true" style="font-size:16px; line-height:1; color: var(--gw-color-primary);"></span>
                                                    </div>
                                                </div>
                                                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; justify-content:flex-end;">
                                                    <?php if ($isDefaultContact) : ?>
                                                        <span style="display:inline-flex; align-items:center; padding:4px 10px; border-radius:8px; border:1px solid #c7b9ff; background:#f4f1ff; color:#5b47ff; font-size:12px; font-weight:500; white-space:nowrap;">
                                                            <?php esc_html_e('Contact par défaut', 'gestiwork'); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    <button type="button" class="gw-button gw-button--secondary" style="padding:4px 8px; line-height:1;" onclick="return false;" aria-label="<?php esc_attr_e('Actions', 'gestiwork'); ?>">
                                                        <span class="dashicons dashicons-ellipsis" aria-hidden="true"></span>
                                                    </button>
                                                </div>
                                            </div>

                                            <div style="margin-top: 8px; display:grid; gap:6px; color: var(--gw-color-text);">
                                                <?php if ($contactMeta !== '') : ?>
                                                    <div style="display:flex; align-items:center; gap:8px;">
                                                        <span class="dashicons dashicons-briefcase" aria-hidden="true" style="color: var(--gw-color-muted);"></span>
                                                        <span><?php echo esc_html($contactMeta); ?></span>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if ($contactTel !== '') : ?>
                                                    <div style="display:flex; align-items:center; gap:8px;">
                                                        <span class="dashicons dashicons-phone" aria-hidden="true" style="color: var(--gw-color-muted);"></span>
                                                        <span><?php echo esc_html($contactTel); ?></span>
                                                    </div>
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
                                <div style="margin-top: 10px; color: var(--gw-color-muted); font-size: 13px;">
                                    <?php esc_html_e('Cette entreprise n\'a aucun contact client associé.', 'gestiwork'); ?>
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
                            <tr>
                                <td>APP-004</td>
                                <td>Patricia DUMONT</td>
                                <td>dumont.patou@gmail.com</td>
                                <td>06 45 02 65 00</td>
                                <td style="text-align:right;"><a href="#" onclick="return false;">Actions</a></td>
                            </tr>
                            <tr>
                                <td>APP-005</td>
                                <td>Sylvain ELMAHRI</td>
                                <td>sylvain.elmahri@toto.fr</td>
                                <td>06 45 02 65 00</td>
                                <td style="text-align:right;"><a href="#" onclick="return false;">Actions</a></td>
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
                        <span><?php esc_html_e('Montrer 1 à 2 de 2 lignes', 'gestiwork'); ?></span>
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
                            <h3 class="gw-section-subtitle" style="margin:0;"><?php esc_html_e('Historique d\'activités', 'gestiwork'); ?></h3>
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
                            <h3 class="gw-section-subtitle" style="margin:0;"><?php esc_html_e('Tâches à venir', 'gestiwork'); ?></h3>
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

<div class="gw-modal-backdrop" id="gw-modal-client-contacts" aria-hidden="true">
    <div class="gw-modal gw-modal" role="dialog" aria-modal="true" aria-labelledby="gw-modal-client-contacts-title">
        <div class="gw-modal-header">
            <h3 class="gw-modal-title" id="gw-modal-client-contacts-title"><?php esc_html_e('Associer des contacts clients', 'gestiwork'); ?></h3>
            <button type="button" class="gw-modal-close" data-gw-modal-close="gw-modal-client-contacts" aria-label="<?php esc_attr_e('Fermer', 'gestiwork'); ?>">×</button>
        </div>
        <form method="post" action="">
            <input type="hidden" name="gw_action" value="gw_tier_contact_create" />
            <input type="hidden" name="tier_id" value="<?php echo (int) $clientId; ?>" />
            <?php wp_nonce_field('gw_tier_contact_create', 'gw_nonce'); ?>
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
                    <div class="gw-modal-field" style="grid-column: 1 / -1;">
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
            <div class="gw-modal-footer">
                <button type="button" class="gw-button gw-button--secondary" data-gw-modal-close="gw-modal-client-contacts"><?php esc_html_e('Annuler', 'gestiwork'); ?></button>
                <button type="submit" class="gw-button gw-button--primary">
                    <?php esc_html_e('Créer', 'gestiwork'); ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        var tabs = document.querySelectorAll('.gw-settings-tab');
        var panels = document.querySelectorAll('.gw-settings-panel');
        var modalTriggers = document.querySelectorAll('[data-gw-modal-target]');
        var modalCloseButtons = document.querySelectorAll('[data-gw-modal-close]');
        var allModals = document.querySelectorAll('.gw-modal-backdrop');

        if (!tabs.length || !panels.length) {
            return;
        }

        function setActiveTab(target) {
            if (!target) {
                return;
            }

            tabs.forEach(function (t) {
                t.classList.remove('gw-settings-tab--active');
                if (t.getAttribute('data-gw-tab') === target) {
                    t.classList.add('gw-settings-tab--active');
                }
            });

            panels.forEach(function (panel) {
                panel.classList.remove('gw-settings-panel--active');
                if (panel.getAttribute('data-gw-tab-panel') === target) {
                    panel.classList.add('gw-settings-panel--active');
                }
            });

            try {
                if (typeof window !== 'undefined' && window.history && typeof window.history.replaceState === 'function') {
                    var url = new URL(window.location.href);
                    url.searchParams.set('tab', target);
                    window.history.replaceState(null, '', url.toString());
                }
            } catch (e) {
                // ignore
            }
        }

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                var target = tab.getAttribute('data-gw-tab');
                setActiveTab(target);
            });
        });

        modalTriggers.forEach(function (trigger) {
            trigger.addEventListener('click', function () {
                var targetId = trigger.getAttribute('data-gw-modal-target');
                if (!targetId) {
                    return;
                }

                if (allModals && allModals.length) {
                    allModals.forEach(function (backdrop) {
                        backdrop.classList.remove('gw-modal-backdrop--open');
                        backdrop.setAttribute('aria-hidden', 'true');
                    });
                }

                var modal = document.getElementById(targetId);
                if (modal) {
                    modal.classList.add('gw-modal-backdrop--open');
                    modal.setAttribute('aria-hidden', 'false');
                }
            });
        });

        modalCloseButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var targetId = button.getAttribute('data-gw-modal-close');
                if (!targetId) {
                    return;
                }
                var modal = document.getElementById(targetId);
                if (modal) {
                    modal.classList.remove('gw-modal-backdrop--open');
                    modal.setAttribute('aria-hidden', 'true');
                }
            });
        });
    })();
</script>
