<?php
/**
 * GestiWork ERP - Settings content (admin only)
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

use GestiWork\Domain\Settings\SettingsProvider;
use GestiWork\Domain\Settings\OptionsProvider;
use GestiWork\Infrastructure\Database\ShortcodeSeeder;

if (function_exists('wp_enqueue_media')) {
    wp_enqueue_media();
}

$ofIdentity = SettingsProvider::getOfIdentity();
$options    = OptionsProvider::getOptions();
$gwLogoUrl = '';

// Charger les modèles PDF depuis la base
global $wpdb;
$tablePdfTemplates = $wpdb->prefix . 'gw_pdf_templates';
$pdfTemplates = [];
$currentPdfTemplate = null;
$editPdfTemplateId = isset($_GET['edit_pdf_template']) ? (int) $_GET['edit_pdf_template'] : 0;

$tableExistsPdf = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $tablePdfTemplates));
if ($tableExistsPdf === $tablePdfTemplates) {
    $pdfTemplates = $wpdb->get_results("SELECT * FROM {$tablePdfTemplates} ORDER BY name ASC", ARRAY_A);
    if ($editPdfTemplateId > 0) {
        foreach ($pdfTemplates as $tpl) {
            if ((int) $tpl['id'] === $editPdfTemplateId) {
                $currentPdfTemplate = $tpl;
                break;
            }
        }
    }
}

// Charger les shortcodes PDF groupés par thème
$pdfShortcodesGrouped = ShortcodeSeeder::getActiveGrouped();
$pdfShortcodeGroupLabels = ShortcodeSeeder::getGroupLabels();

$regimeActuel = isset($ofIdentity['regime_tva']) ? (string) $ofIdentity['regime_tva'] : 'exonere';
if ($regimeActuel !== 'assujetti' && $regimeActuel !== 'exonere') {
    $regimeActuel = 'exonere';
}
$showTvaCard = ($regimeActuel === 'assujetti');

if (!empty($ofIdentity['logo_id'])) {
    $gwLogoUrl = wp_get_attachment_image_url((int) $ofIdentity['logo_id'], 'medium');
}

$gw_active_tab = 'general';
$gw_section_raw = get_query_var('gw_section');
if ($gw_section_raw === '' && isset($_GET['gw_section'])) {
    $gw_section_raw = (string) $_GET['gw_section'];
}
$gw_section = strtolower((string) $gw_section_raw);

// Normalisation des slugs de section pour supporter plusieurs variantes lisibles
if (in_array($gw_section, ['general', 'general-identite', 'general-et-identite'], true)) {
    $gw_active_tab = 'general';
} elseif (in_array($gw_section, ['options'], true)) {
    $gw_active_tab = 'options';
} elseif (in_array($gw_section, ['pdf', 'gestionpdf', 'gestion-pdf'], true)) {
    $gw_active_tab = 'pdf';
}

?>
<section class="gw-section gw-section-settings">
    <h2 class="gw-section-title"><?php esc_html_e('Paramètres GestiWork', 'gestiwork'); ?></h2>
    <p class="gw-section-description">
        <?php esc_html_e('Les paramètres ci-dessous sont organisés en trois onglets. Pour l’instant, tout est décrit en dur sans sauvegarde : il s’agit de la maquette fonctionnelle de la future page de configuration.', 'gestiwork'); ?>
    </p>

    <div class="gw-settings-tabs" role="tablist">
        <button  type="button" class="gw-settings-tab<?php echo $gw_active_tab === 'general' ? ' gw-settings-tab--active' : ''; ?>" data-gw-tab="general">
            <?php esc_html_e('Général & Identité', 'gestiwork'); ?>
        </button>
        <button type="button" class="gw-settings-tab<?php echo $gw_active_tab === 'options' ? ' gw-settings-tab--active' : ''; ?>" data-gw-tab="options">
            <?php esc_html_e('Options', 'gestiwork'); ?>
        </button>
        <button type="button" class="gw-settings-tab<?php echo $gw_active_tab === 'pdf' ? ' gw-settings-tab--active' : ''; ?>" data-gw-tab="pdf">
            <?php esc_html_e('Gestion PDF', 'gestiwork'); ?>
        </button>
    </div>

    <div class="gw-settings-panels">
        <div class="gw-settings-panel<?php echo $gw_active_tab === 'general' ? ' gw-settings-panel--active' : ''; ?>" data-gw-tab-panel="general">
            <h3 class="gw-section-subtitle"><?php esc_html_e('1. Général & Identité', 'gestiwork'); ?></h3>
            <p class="gw-section-description">
                <?php esc_html_e('Vue d’ensemble des informations d’identité, de fiscalité et de numérotation de votre organisme de formation.', 'gestiwork'); ?>
            </p>

            <button type="button" class="gw-button gw-button--secondary gw-button-modals" data-gw-modal-target="gw-modal-general">
                <?php esc_html_e('Modifier les informations de cet onglet', 'gestiwork'); ?>
            </button>

            <div class="gw-settings-group ">
                <h4 class="gw-section-subtitle"><?php esc_html_e('1.1 Identité de l’organisme de formation', 'gestiwork'); ?></h4>
                <div class="gw-settings-grid">
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Nom (raison sociale)', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php echo isset($ofIdentity['raison_sociale']) ? esc_html($ofIdentity['raison_sociale']) : ''; ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Adresse', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php echo isset($ofIdentity['adresse']) ? esc_html($ofIdentity['adresse']) : ''; ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Code postal / Ville', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">
                            <?php
                            $gw_cp   = isset($ofIdentity['code_postal']) ? (string) $ofIdentity['code_postal'] : '';
                            $gw_ville = isset($ofIdentity['ville']) ? (string) $ofIdentity['ville'] : '';
                            $gw_cp_ville = trim($gw_cp . ' ' . strtoupper($gw_ville));
                            echo $gw_cp_ville !== '' ? esc_html($gw_cp_ville) : '';
                            ?>
                        </p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Téléphone fixe', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php echo isset($ofIdentity['telephone_fixe']) ? esc_html($ofIdentity['telephone_fixe']) : ''; ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Téléphone portable', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php echo isset($ofIdentity['telephone_portable']) ? esc_html($ofIdentity['telephone_portable']) : ''; ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('E-mail de contact', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php echo isset($ofIdentity['email_contact']) ? esc_html($ofIdentity['email_contact']) : ''; ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Site Internet', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php echo isset($ofIdentity['site_internet']) ? esc_html($ofIdentity['site_internet']) : ''; ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Représentant légal', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">
                            <?php
                            $repNom = isset($ofIdentity['representant_nom']) ? (string) $ofIdentity['representant_nom'] : '';
                            $repPrenom = isset($ofIdentity['representant_prenom']) ? (string) $ofIdentity['representant_prenom'] : '';
                            $repFull = trim($repPrenom . ' ' . $repNom);
                            echo $repFull !== '' ? esc_html($repFull) : '';
                            ?>
                        </p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Habilitation INRS', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php echo isset($ofIdentity['habilitation_inrs']) ? esc_html($ofIdentity['habilitation_inrs']) : ''; ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Description (présentation OF)', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">
                            <?php
                            $gw_description_text = isset($ofIdentity['description']) ? wp_strip_all_tags((string) $ofIdentity['description']) : '';
                            if ($gw_description_text !== '') {
                                echo esc_html(wp_trim_words($gw_description_text, 40, '…'));
                            }
                            ?>
                        </p>
                        <button type="button" class="gw-button gw-button--secondary" id="gw-open-description-editor">
                            <?php esc_html_e('Modifier la présentation OF', 'gestiwork'); ?>
                        </button>
                    </div>
                    <div class="gw-settings-field gw-settings-field-logo">
                        <p class="gw-settings-label"><?php esc_html_e('Logo GestiWork', 'gestiwork'); ?></p>
                        <form method="post" action="" style="margin:0; padding:0;">
                            <?php wp_nonce_field('gw_save_of_logo', 'gw_settings_nonce_logo'); ?>
                            <input type="hidden" name="gw_settings_action" value="save_of_logo" />
                            <input type="hidden" id="gw_logo_id" name="gw_logo_id" value="<?php echo isset($ofIdentity['logo_id']) ? (int) $ofIdentity['logo_id'] : 0; ?>" />

                            <div style="margin-bottom:8px; text-align:center;">
                                <?php if (!empty($gwLogoUrl)) : ?>
                                    <img id="gw-logo-preview" src="<?php echo esc_url($gwLogoUrl); ?>" alt="<?php echo esc_attr($ofIdentity['raison_sociale'] ?? ''); ?>" style="max-width: 96px; max-height: 64px; height: auto; border-radius: 4px; border: 1px solid rgba(0,0,0,0.08); background: #fff; padding: 4px; display: inline-block;">
                                <?php else : ?>
                                    <img id="gw-logo-preview" src="" alt="" style="display:none; max-width: 96px; max-height: 64px; height: auto; border-radius: 4px; border: 1px solid rgba(0,0,0,0.08); background: #fff; padding: 4px;">
                                    <p class="gw-settings-placeholder"><?php esc_html_e('Sélection d’un logo dédié pour l’ERP (différent du logo du thème WordPress).', 'gestiwork'); ?></p>
                                <?php endif; ?>
                            </div>

                            <div style="display:flex; gap:6px; justify-content:center; flex-wrap:wrap;">
                                <button type="button" class="gw-button gw-button--secondary" id="gw-logo-select-button"><?php esc_html_e('Choisir / modifier le logo', 'gestiwork'); ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="gw-settings-group" id="gw-settings-group-description-editor" style="display: none;">
                <h4 class="gw-section-subtitle"><?php esc_html_e('1.x Description (présentation OF)', 'gestiwork'); ?></h4>
                <form method="post" action="">
                    <?php wp_nonce_field('gw_save_of_description', 'gw_settings_nonce_description'); ?>
                    <input type="hidden" name="gw_settings_action" value="save_of_description" />
                    <div class="gw-settings-grid">
                        <div class="gw-settings-field">
                            <?php
                            $gw_description_content = isset($ofIdentity['description']) ? (string) $ofIdentity['description'] : '';
                            wp_editor(
                                $gw_description_content,
                                'gw_description_editor',
                                [
                                    'textarea_name' => 'gw_description',
                                    'textarea_rows' => 12,
                                    'media_buttons' => false,
                                ]
                            );
                            ?>
                        </div>
                    </div>
                    <p>
                        <button type="submit" class="gw-button gw-button--primary"><?php esc_html_e('Enregistrer la présentation', 'gestiwork'); ?></button>
                    </p>
                </form>
            </div>

            <div class="gw-settings-group">
                <h4 class="gw-section-subtitle"><?php esc_html_e('1.2 Fiscalité & TVA', 'gestiwork'); ?></h4>
                <div class="gw-settings-grid">
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('SIRET / SIREN', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php echo isset($ofIdentity['siret']) ? esc_html($ofIdentity['siret']) : ''; ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Code APE (NAF)', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php echo isset($ofIdentity['code_ape']) ? esc_html($ofIdentity['code_ape']) : ''; ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('RCS / immatriculation', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php echo isset($ofIdentity['rcs']) ? esc_html($ofIdentity['rcs']) : ''; ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('NDA (numéro de déclaration d’activité)', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php echo isset($ofIdentity['nda']) ? esc_html($ofIdentity['nda']) : ''; ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Qualiopi', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php echo isset($ofIdentity['qualiopi']) ? esc_html($ofIdentity['qualiopi']) : ''; ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Datadock', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php echo isset($ofIdentity['datadock']) ? esc_html($ofIdentity['datadock']) : ''; ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('RM (registre des métiers)', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php echo isset($ofIdentity['rm']) ? esc_html($ofIdentity['rm']) : ''; ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('TVA intracommunautaire', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php echo isset($ofIdentity['tva_intracom']) ? esc_html($ofIdentity['tva_intracom']) : ''; ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Régime de TVA', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">
                            <?php
                            if ($regimeActuel === 'assujetti') {
                                esc_html_e('Assujetti', 'gestiwork');
                            } else {
                                esc_html_e('Exonéré (avec mention de l’article 261-4-4 du CGI).', 'gestiwork');
                            }
                            ?>
                        </p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Taux de TVA par défaut', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">
                            <?php
                            if (isset($ofIdentity['taux_tva']) && $ofIdentity['taux_tva'] !== '') {
                                echo esc_html((string) $ofIdentity['taux_tva']) . ' %';
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="gw-settings-group">
                <h4 class="gw-section-subtitle"><?php esc_html_e('1.3 Banque & règlements', 'gestiwork'); ?></h4>
                <div class="gw-settings-grid">
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Banque principale', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php echo isset($ofIdentity['banque_principale']) ? esc_html($ofIdentity['banque_principale']) : ''; ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Coordonnées IBAN / BIC', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">
                            <?php
                            $gw_iban = isset($ofIdentity['iban']) ? trim((string) $ofIdentity['iban']) : '';
                            $gw_bic  = isset($ofIdentity['bic']) ? trim((string) $ofIdentity['bic']) : '';
                            if ($gw_iban !== '') {
                                echo esc_html($gw_iban);
                            }
                            if ($gw_bic !== '') {
                                if ($gw_iban !== '') {
                                    echo '<br />';
                                }
                                echo esc_html($gw_bic);
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="gw-settings-group">
                <h4 class="gw-section-subtitle"><?php esc_html_e('1.4 Numérotation (séquences)', 'gestiwork'); ?></h4>
                <div class="gw-settings-grid">
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Format des numéros de devis / propositions', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php echo isset($ofIdentity['format_numero_devis']) ? esc_html($ofIdentity['format_numero_devis']) : ''; ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Compteur courant', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">
                            <?php
                            $gw_compteur = isset($ofIdentity['compteur_devis']) ? (int) $ofIdentity['compteur_devis'] : 0;
                            if ($gw_compteur > 0) {
                                echo esc_html(str_pad((string) $gw_compteur, 4, '0', STR_PAD_LEFT));
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="gw-settings-panel<?php echo $gw_active_tab === 'options' ? ' gw-settings-panel--active' : ''; ?>" data-gw-tab-panel="options">
            <h3 class="gw-section-subtitle"><?php esc_html_e('2. Options générales', 'gestiwork'); ?></h3>
            <p class="gw-section-description">
                <?php esc_html_e('Cette section rassemblera les options avancées de fonctionnement : pages de gestion, champs additionnels, délais, limites, etc.', 'gestiwork'); ?>
            </p>

            <button type="button" class="gw-button gw-button--secondary gw-button-modals" data-gw-modal-target="gw-modal-options">
                <?php esc_html_e('Modifier les options générales', 'gestiwork'); ?>
            </button>

            <div class="gw-settings-group">
                <h4 class="gw-section-subtitle"><?php esc_html_e('2.1 Activité & URLs de gestion', 'gestiwork'); ?></h4>
                <div class="gw-settings-grid">
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Première année d’activité gérée', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">
                            <?php
                            $firstYear = isset($options['first_year']) ? (int) $options['first_year'] : 0;
                            echo $firstYear > 0 ? esc_html((string) $firstYear) : '';
                            ?>
                        </p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Page de gestion principale', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">https://extranet.exemple-of.fr/gestion</p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Page personnelle des utilisateurs', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">https://extranet.exemple-of.fr/mon-compte</p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Page privée clients & stagiaires', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">https://extranet.exemple-of.fr/espace-clients</p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Page d’aide (bulle d’aide centralisée)', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">https://extranet.exemple-of.fr/aide</p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Page de gestion des exports de données', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">https://extranet.exemple-of.fr/exports</p>
                    </div>
                </div>
            </div>

            <div class="gw-settings-group">
                <h4 class="gw-section-subtitle"><?php esc_html_e('2.2 Champs additionnels et comportements', 'gestiwork'); ?></h4>
                <div class="gw-settings-grid">
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Numéro de contrat pour les clients', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">
                            <?php
                            $enabled = !empty($options['enable_client_contract_number']);
                            if ($enabled) {
                                echo '<span style="font-weight:600;color:var(--gw-color-warning);">' . esc_html__('Activé', 'gestiwork') . '</span> – ' . esc_html__('un champ dédié au numéro de contrat client est présent sur les documents.', 'gestiwork');
                            } else {
                                echo '<span style="font-weight:600;color:var(--gw-color-error);">' . esc_html__('Désactivé', 'gestiwork') . '</span> – ' . esc_html__('aucun numéro de contrat client spécifique n’est affiché sur les documents.', 'gestiwork');
                            }
                            ?>
                        </p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Période de validité des documents', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">
                            <?php
                            $enabled = !empty($options['enable_document_validity_period']);
                            if ($enabled) {
                                echo '<span style="font-weight:600;color:var(--gw-color-warning);">' . esc_html__('Activé', 'gestiwork') . '</span> – ' . esc_html__('une date de validité peut être définie et affichée sur les devis à signer.', 'gestiwork');
                            } else {
                                echo '<span style="font-weight:600;color:var(--gw-color-error);">' . esc_html__('Désactivé', 'gestiwork') . '</span> – ' . esc_html__('aucune date de validité spécifique n’est gérée sur les devis.', 'gestiwork');
                            }
                            ?>
                        </p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Statut et code d’activité des formateurs', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">
                            <?php
                            $enabled = !empty($options['enable_trainer_status_activity_code']);
                            if ($enabled) {
                                echo '<span style="font-weight:600;color:var(--gw-color-warning);">' . esc_html__('Activé', 'gestiwork') . '</span> – ' . esc_html__('vous pourrez renseigner pour chaque formateur son statut (salarié, indépendant, etc.).', 'gestiwork');
                            } else {
                                echo '<span style="font-weight:600;color:var(--gw-color-error);">' . esc_html__('Désactivé', 'gestiwork') . '</span> – ' . esc_html__('aucun statut ni code d’activité spécifique n’est demandé pour les formateurs.', 'gestiwork');
                            }
                            ?>
                        </p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Durée des actions', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">
                            <?php
                            $enabled = !empty($options['enable_free_text_duration']);
                            if ($enabled) {
                                echo '<span style="font-weight:600;color:var(--gw-color-warning);">' . esc_html__('Activé', 'gestiwork') . '</span> – ' . esc_html__('Permet de definir le decoupage de la session (journée, 1/2journée)', 'gestiwork');
                            } else {
                                echo '<span style="font-weight:600;color:var(--gw-color-error);">' . esc_html__('Désactivé', 'gestiwork') . '</span> – ' . esc_html__('Pas de decoupage sessions', 'gestiwork');
                            }
                            ?>
                        </p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Image de signature', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">
                            <?php
                            $enabled = !empty($options['enable_signature_image']);
                            if ($enabled) {
                                echo '<span style="font-weight:600;color:var(--gw-color-warning);">' . esc_html__('Activé', 'gestiwork') . '</span> – ' . esc_html__('une image de signature du responsable peut être téléversée et affichée sur certains documents.', 'gestiwork');
                            } else {
                                echo '<span style="font-weight:600;color:var(--gw-color-error);">' . esc_html__('Désactivé', 'gestiwork') . '</span> – ' . esc_html__('aucune image de signature n’est affichée sur les documents.', 'gestiwork');
                            }
                            ?>
                        </p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Connexion en tant que…', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">
                            <?php
                            $enabled = !empty($options['enable_impersonation_login']);
                            if ($enabled) {
                                echo '<span style="font-weight:600;color:var(--gw-color-warning);">' . esc_html__('Activé', 'gestiwork') . '</span> – ' . esc_html__('les responsables autorisés peuvent se connecter à la place d’un autre utilisateur depuis l’interface.', 'gestiwork');
                            } else {
                                echo '<span style="font-weight:600;color:var(--gw-color-error);">' . esc_html__('Désactivé', 'gestiwork') . '</span> – ' . esc_html__('la connexion en tant qu’un autre utilisateur est désactivée.', 'gestiwork');
                            }
                            ?>
                        </p>
                    </div>
                    
                </div>
            </div>

            <div class="gw-settings-group">
                <h4 class="gw-section-subtitle"><?php esc_html_e('2.3 Délais, seuils et limites', 'gestiwork'); ?></h4>
                <div class="gw-settings-grid">
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Délai minimum entre deux e-mails de demande de signature', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">
                            <?php
                            $val = isset($options['min_hours_between_signature_emails']) ? (int) $options['min_hours_between_signature_emails'] : 0;
                            echo $val > 0 ? esc_html($val . ' h') : '';
                            ?>
                        </p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Délai maximum avant alerte sur la veille personnelle', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">
                            <?php
                            $val = isset($options['max_days_veille_alert']) ? (int) $options['max_days_veille_alert'] : 0;
                            echo $val > 0 ? esc_html($val . ' j') : '';
                            ?>
                        </p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Durée de validité du jeton de connexion', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">
                            <?php
                            $val = isset($options['token_validity_hours']) ? (int) $options['token_validity_hours'] : 0;
                            if ($val > 0) {
                                echo esc_html($val . ' h');
                            } elseif ($val === 0) {
                                esc_html_e('Illimité', 'gestiwork');
                            }
                            ?>
                        </p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Tarif horaire plancher', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">
                            <?php
                            if (isset($options['min_hourly_rate']) && (float) $options['min_hourly_rate'] > 0) {
                                echo esc_html(number_format((float) $options['min_hourly_rate'], 2, ',', ' ')) . ' € / h';
                            }
                            ?>
                        </p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Pourcentage par défaut pour l’acompte', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">
                            <?php
                            if (isset($options['default_deposit_percent']) && (float) $options['default_deposit_percent'] > 0) {
                                echo esc_html(number_format((float) $options['default_deposit_percent'], 2, ',', ' ')) . ' %';
                            }
                            ?>
                        </p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Nombre maximum de lignes de log chargées', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">
                            <?php
                            $val = isset($options['max_log_rows']) ? (int) $options['max_log_rows'] : 0;
                            echo $val > 0 ? esc_html((string) $val) : '';
                            ?>
                        </p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Nombre de lignes par feuille d’émargement', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">
                            <?php
                            $val = isset($options['attendance_sheet_lines']) ? (int) $options['attendance_sheet_lines'] : 0;
                            echo $val > 0 ? esc_html((string) $val) : '';
                            ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="gw-settings-group">
                <h4 class="gw-section-subtitle"><?php esc_html_e('2.4 Taxonomies & bilans', 'gestiwork'); ?></h4>
                <div class="gw-settings-grid">
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Taxonomies pour les formations et sessions', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">
                            <?php
                            $mode = isset($options['taxonomy_mode']) ? (string) $options['taxonomy_mode'] : '';
                            if ($mode === 'tags') {
                                esc_html_e('Étiquettes (classification transversale)', 'gestiwork');
                            } else {
                                esc_html_e('Catégories (arborescence)', 'gestiwork');
                            }
                            ?>
                        </p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Page de présentation du bilan de compétences', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">https://extranet.exemple-of.fr/bilan-competences</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="gw-settings-panel<?php echo $gw_active_tab === 'pdf' ? ' gw-settings-panel--active' : ''; ?>" data-gw-tab-panel="pdf">
            <h3 class="gw-section-subtitle"><?php esc_html_e('3. Gestion PDF', 'gestiwork'); ?></h3>
            <p class="gw-section-description">
                <?php esc_html_e('Cette section sera dédiée à la mise en forme des documents PDF générés par GestiWork (propositions, conventions, convocations, attestations, etc.).', 'gestiwork'); ?>
            </p>

            <form method="post" action="" id="gw-pdf-template-form">
                <input type="hidden" name="gw_settings_action" value="save_pdf_template" />
                <input type="hidden" name="gw_pdf_template_id" id="gw_pdf_template_id" value="<?php echo $currentPdfTemplate ? (int) $currentPdfTemplate['id'] : 0; ?>" />
                <?php wp_nonce_field('gw_save_pdf_template', 'gw_settings_nonce_pdf'); ?>

                <div class="gw-settings-group">
                    <?php if ($currentPdfTemplate) : ?>
                        <h4 class="gw-section-subtitle" style="color:#d63638;">3.1a <?php esc_html_e(' : vous etes en train de modifier : ', 'gestiwork'); ?></h4>
                    <?php else : ?>
                        <h4 class="gw-section-subtitle"><?php esc_html_e('3.1 Nom du modèle PDF', 'gestiwork'); ?></h4>
                    <?php endif; ?>
                    <div class="gw-settings-grid" style="grid-template-columns: 1fr;">
                        <div class="gw-settings-field">
                            <?php if ($currentPdfTemplate) : ?>
                                <p class="gw-pdf-current-template-name">
                                    <?php echo esc_html($currentPdfTemplate['name']); ?>
                                </p>
                                <input type="hidden" name="gw_pdf_model_name" value="<?php echo esc_attr($currentPdfTemplate['name']); ?>" />
                            <?php else : ?>
                                <p class="gw-settings-label"><?php esc_html_e('Nom du modèle PDF ', 'gestiwork'); ?></p>
                                <div class="gw-pdf-model-name-actions" style="display:flex; gap:8px; align-items:center; flex-wrap:nowrap;">
                                    <input type="text" class="gw-modal-input" id="gw_pdf_model_name" name="gw_pdf_model_name" value="" placeholder="<?php esc_attr_e('Saisissez un nom pour le modèle', 'gestiwork'); ?>" style="max-width:260px;" />
                                    <button type="button" class="gw-button gw-button--secondary" id="gw_pdf_create_btn">
                                        <?php esc_html_e('Créer', 'gestiwork'); ?>
                                    </button>
                                    <button type="button" class="gw-button gw-button--link" id="gw_pdf_cancel_btn">
                                        <?php esc_html_e('Annuler', 'gestiwork'); ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="gw-settings-field">
                            <p class="gw-settings-label"><?php esc_html_e('Modèles existants', 'gestiwork'); ?></p>
                            <?php if (empty($pdfTemplates)) : ?>
                                <p class="gw-settings-placeholder"><?php esc_html_e('Aucun modèle PDF enregistré.', 'gestiwork'); ?></p>
                            <?php else : ?>
                                <ul class="gw-pdf-templates-list">
                                    <?php foreach ($pdfTemplates as $tpl) : ?>
                                        <li class="gw-pdf-template-item" data-template-id="<?php echo (int) $tpl['id']; ?>">
                                            <div class="gw-pdf-template-item-main">
                                                <span class="gw-pdf-template-name"><?php echo esc_html($tpl['name']); ?></span>
                                                <?php if (!empty($tpl['is_default'])) : ?>
                                                    <span class="dashicons dashicons-star-filled" title="<?php esc_attr_e('Modèle par défaut', 'gestiwork'); ?>"></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="gw-pdf-template-item-actions">
                                                <button type="button" class="gw-pdf-preview-template" data-template-id="<?php echo (int) $tpl['id']; ?>" title="<?php esc_attr_e('Voir un aperçu PDF', 'gestiwork'); ?>">
                                                    <span class="dashicons dashicons-pdf" aria-hidden="true"></span>
                                                </button>
                                                <button type="button" class="gw-pdf-duplicate-template" data-template-id="<?php echo (int) $tpl['id']; ?>" data-template-name="<?php echo esc_attr($tpl['name']); ?>" title="<?php esc_attr_e('Dupliquer le modèle', 'gestiwork'); ?>">
                                                    <span class="dashicons dashicons-admin-page" aria-hidden="true"></span>
                                                </button>
                                                <a href="<?php echo esc_url(add_query_arg('edit_pdf_template', (int) $tpl['id'], home_url('/gestiwork/settings/pdf/'))); ?>" title="<?php esc_attr_e('Modifier', 'gestiwork'); ?>" class="gw-pdf-template-edit">
                                                    <span class="dashicons dashicons-edit" aria-hidden="true"></span>
                                                </a>
                                                <button type="button" class="gw-pdf-delete-template" data-template-id="<?php echo (int) $tpl['id']; ?>" data-template-name="<?php echo esc_attr($tpl['name']); ?>" title="<?php esc_attr_e('Supprimer', 'gestiwork'); ?>">
                                                    <span class="dashicons dashicons-trash" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="gw-settings-group gw-settings-group--pdf-layout" id="gw-pdf-layout-group" style="<?php echo $currentPdfTemplate ? '' : 'display:none;'; ?>">
                <h4 class="gw-section-subtitle"><?php esc_html_e('3.2 Mise en forme PDF', 'gestiwork'); ?></h4>
                <button type="button" class="gw-button gw-button--secondary" id="gw-toggle-pdf-layout" style="margin-bottom: 8px;">
                    <?php esc_html_e('Afficher / masquer les réglages de mise en forme', 'gestiwork'); ?>
                </button>
                <?php
                // Valeurs par défaut ou valeurs du modèle en cours
                $pdfMarginTop = $currentPdfTemplate ? (float) $currentPdfTemplate['margin_top'] : 5;
                $pdfMarginBottom = $currentPdfTemplate ? (float) $currentPdfTemplate['margin_bottom'] : 5;
                $pdfMarginLeft = $currentPdfTemplate ? (float) $currentPdfTemplate['margin_left'] : 10;
                $pdfMarginRight = $currentPdfTemplate ? (float) $currentPdfTemplate['margin_right'] : 10;
                $pdfHeaderHeight = $currentPdfTemplate ? (float) $currentPdfTemplate['header_height'] : 20;
                $pdfFooterHeight = $currentPdfTemplate ? (float) $currentPdfTemplate['footer_height'] : 15;
                $pdfFontTitle = $currentPdfTemplate ? $currentPdfTemplate['font_title'] : 'sans-serif';
                $pdfFontBody = $currentPdfTemplate ? $currentPdfTemplate['font_body'] : 'sans-serif';
                $pdfFontTitleSize = $currentPdfTemplate && isset($currentPdfTemplate['font_title_size']) ? (int) $currentPdfTemplate['font_title_size'] : 14;
                $pdfFontBodySize = $currentPdfTemplate && isset($currentPdfTemplate['font_body_size']) ? (int) $currentPdfTemplate['font_body_size'] : 11;
                $pdfColorTitle = $currentPdfTemplate ? $currentPdfTemplate['color_title'] : '#023047';
                $pdfColorOtherTitles = $currentPdfTemplate ? $currentPdfTemplate['color_other_titles'] : '#023047';
                $pdfHeaderBgColor = $currentPdfTemplate && isset($currentPdfTemplate['header_bg_color']) ? $currentPdfTemplate['header_bg_color'] : 'transparent';
                $pdfFooterBgColor = $currentPdfTemplate && isset($currentPdfTemplate['footer_bg_color']) ? $currentPdfTemplate['footer_bg_color'] : 'transparent';
                $pdfCustomCss = $currentPdfTemplate ? $currentPdfTemplate['custom_css'] : '';
                $pdfHeaderHtml = $currentPdfTemplate ? $currentPdfTemplate['header_html'] : '';
                $pdfFooterHtml = $currentPdfTemplate ? $currentPdfTemplate['footer_html'] : '';
                $fontOptions = ['sans-serif', 'times', 'courier', 'helvetica', 'serif', 'monospace'];
                $fontSizeOptions = [8, 9, 10, 11, 12, 14, 16, 18, 20, 24];
                ?>
                <div class="gw-settings-grid gw-pdf-layout-grid" id="gw-pdf-layout-body" style="display:none;">
                    <div class="gw-settings-field gw-pdf-layout-block gw-pdf-layout-block--dimensions">
                        <p class="gw-settings-label"><?php esc_html_e('Dimensions et marges', 'gestiwork'); ?></p>
                        <div style="display:flex; gap:24px; flex-wrap:wrap;">
                            <div>
                                <p class="gw-settings-placeholder"><?php esc_html_e('Marges (en millimètres)', 'gestiwork'); ?></p>
                                <p><label for="gw_pdf_margin_top"><?php esc_html_e('Haut', 'gestiwork'); ?></label> <input type="number" id="gw_pdf_margin_top" name="gw_pdf_margin_top" value="<?php echo esc_attr($pdfMarginTop); ?>" style="width:80px;" step="0.1" /></p>
                                <p><label for="gw_pdf_margin_bottom"><?php esc_html_e('Bas', 'gestiwork'); ?></label> <input type="number" id="gw_pdf_margin_bottom" name="gw_pdf_margin_bottom" value="<?php echo esc_attr($pdfMarginBottom); ?>" style="width:80px;" step="0.1" /></p>
                                <p><label for="gw_pdf_margin_left"><?php esc_html_e('Gauche', 'gestiwork'); ?></label> <input type="number" id="gw_pdf_margin_left" name="gw_pdf_margin_left" value="<?php echo esc_attr($pdfMarginLeft); ?>" style="width:80px;" step="0.1" /></p>
                                <p><label for="gw_pdf_margin_right"><?php esc_html_e('Droite', 'gestiwork'); ?></label> <input type="number" id="gw_pdf_margin_right" name="gw_pdf_margin_right" value="<?php echo esc_attr($pdfMarginRight); ?>" style="width:80px;" step="0.1" /></p>
                            </div>
                            <div>
                                <p class="gw-settings-placeholder"><?php esc_html_e('Hauteurs (en millimètres)', 'gestiwork'); ?></p>
                                <p><label for="gw_pdf_header_height"><?php esc_html_e('En-tête', 'gestiwork'); ?></label> <input type="number" id="gw_pdf_header_height" name="gw_pdf_header_height" value="<?php echo esc_attr($pdfHeaderHeight); ?>" style="width:80px;" step="0.1" /></p>
                                <p><label for="gw_pdf_footer_height"><?php esc_html_e('Pied de page', 'gestiwork'); ?></label> <input type="number" id="gw_pdf_footer_height" name="gw_pdf_footer_height" value="<?php echo esc_attr($pdfFooterHeight); ?>" style="width:80px;" step="0.1" /></p>
                                <p class="gw-settings-placeholder" style="margin-top:8px;">
                                    <?php esc_html_e('Hauteur du corps calculée à partir du format de page et des marges.', 'gestiwork'); ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="gw-settings-field gw-pdf-layout-block gw-pdf-layout-block--typo">
                        <p class="gw-settings-label"><?php esc_html_e('Typographie et couleurs', 'gestiwork'); ?></p>
                        <div class="gw-pdf-layout-block--typo-title">
                            <p>
                                <label for="gw_pdf_font_title" class="top"><?php esc_html_e('Police des titres', 'gestiwork'); ?></label>
                                <select id="gw_pdf_font_title" name="gw_pdf_font_title" style="max-width:180px;">
                                    <?php foreach ($fontOptions as $font) : ?>
                                        <option value="<?php echo esc_attr($font); ?>"<?php selected($pdfFontTitle, $font); ?>><?php echo esc_html($font); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="gw_pdf_font_title_size" style="margin-left:12px;"><?php esc_html_e('Taille', 'gestiwork'); ?></label>
                                <select id="gw_pdf_font_title_size" name="gw_pdf_font_title_size" style="width:70px;">
                                    <?php foreach ($fontSizeOptions as $size) : ?>
                                        <option value="<?php echo esc_attr($size); ?>"<?php selected($pdfFontTitleSize, $size); ?>><?php echo esc_html($size); ?>pt</option>
                                    <?php endforeach; ?>
                                </select>
                            </p>
                            <p>
                                <label for="gw_pdf_font_body" class="top"><?php esc_html_e('Police du texte courant', 'gestiwork'); ?></label>
                                <select id="gw_pdf_font_body" name="gw_pdf_font_body" style="max-width:180px;">
                                    <?php foreach ($fontOptions as $font) : ?>
                                        <option value="<?php echo esc_attr($font); ?>"<?php selected($pdfFontBody, $font); ?>><?php echo esc_html($font); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="gw_pdf_font_body_size" style="margin-left:12px;"><?php esc_html_e('Taille', 'gestiwork'); ?></label>
                                <select id="gw_pdf_font_body_size" name="gw_pdf_font_body_size" style="width:70px;">
                                    <?php foreach ($fontSizeOptions as $size) : ?>
                                        <option value="<?php echo esc_attr($size); ?>"<?php selected($pdfFontBodySize, $size); ?>><?php echo esc_html($size); ?>pt</option>
                                    <?php endforeach; ?>
                                </select>
                            </p>
                            <p>
                                <label for="gw_pdf_color_title" class="top"><?php esc_html_e('Couleur des titres de document', 'gestiwork'); ?></label>
                                <input type="color" id="gw_pdf_color_title" name="gw_pdf_color_title" value="<?php echo esc_attr($pdfColorTitle); ?>" style="width:60px;height:32px;padding:2px;cursor:pointer;" />
                                <input type="text" id="gw_pdf_color_title_text" value="<?php echo esc_attr($pdfColorTitle); ?>" style="width:80px;margin-left:8px;" data-color-target="gw_pdf_color_title" />
                            </p>
                            <p>
                                <label for="gw_pdf_color_other_titles" class="top"><?php esc_html_e('Couleur des autres titres', 'gestiwork'); ?></label>
                                <input type="color" id="gw_pdf_color_other_titles" name="gw_pdf_color_other_titles" value="<?php echo esc_attr($pdfColorOtherTitles); ?>" style="width:60px;height:32px;padding:2px;cursor:pointer;" />
                                <input type="text" id="gw_pdf_color_other_titles_text" value="<?php echo esc_attr($pdfColorOtherTitles); ?>" style="width:80px;margin-left:8px;" data-color-target="gw_pdf_color_other_titles" />
                            </p>
                            <p>
                                <label for="gw_pdf_header_bg_color" class="top"><?php esc_html_e('Fond de l\'en-tête', 'gestiwork'); ?></label>
                                <input type="color" id="gw_pdf_header_bg_color_picker" value="<?php echo ($pdfHeaderBgColor !== 'transparent' ? esc_attr($pdfHeaderBgColor) : '#ffffff'); ?>" style="width:60px;height:32px;padding:2px;cursor:pointer;" data-bg-target="gw_pdf_header_bg_color" />
                                <input type="text" id="gw_pdf_header_bg_color" name="gw_pdf_header_bg_color" value="<?php echo esc_attr($pdfHeaderBgColor); ?>" style="width:100px;margin-left:8px;" placeholder="transparent" />
                            </p>
                            <p>
                                <label for="gw_pdf_footer_bg_color" class="top"><?php esc_html_e('Fond du pied de page', 'gestiwork'); ?></label>
                                <input type="color" id="gw_pdf_footer_bg_color_picker" value="<?php echo ($pdfFooterBgColor !== 'transparent' ? esc_attr($pdfFooterBgColor) : '#ffffff'); ?>" style="width:60px;height:32px;padding:2px;cursor:pointer;" data-bg-target="gw_pdf_footer_bg_color" />
                                <input type="text" id="gw_pdf_footer_bg_color" name="gw_pdf_footer_bg_color" value="<?php echo esc_attr($pdfFooterBgColor); ?>" style="width:100px;margin-left:8px;" placeholder="transparent" />
                            </p>
                        </div>
                    </div>

                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Feuille de style personnalisée (CSS)', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php esc_html_e('La feuille de style s\'appliquera après les réglages ci-dessus.', 'gestiwork'); ?></p>
                        <textarea id="gw_pdf_custom_css" name="gw_pdf_custom_css" rows="8" style="width:100%; max-width:100%;"><?php echo esc_textarea($pdfCustomCss); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="gw-settings-group" id="gw-pdf-header-footer-group" style="<?php echo $currentPdfTemplate ? '' : 'display:none;'; ?>">
                <h4 class="gw-section-subtitle"><?php esc_html_e('3.3 En-tête & pied de page', 'gestiwork'); ?></h4>
                <div class="gw-settings-grid">
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('En-tête commun', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php esc_html_e('Zone éditable (TinyMCE) pour l’en-tête commun à tous les PDF.', 'gestiwork'); ?></p>
                        <button type="button" class="gw-button gw-button--secondary" id="gw-open-pdf-header-editor">
                            <?php esc_html_e('Modifier le gabarit d’en-tête', 'gestiwork'); ?>
                        </button>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Pied de page commun', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php esc_html_e('Zone éditable (TinyMCE) pour le pied de page commun à tous les PDF.', 'gestiwork'); ?></p>
                        <button type="button" class="gw-button gw-button--secondary" id="gw-open-pdf-footer-editor">
                            <?php esc_html_e('Modifier le gabarit de pied de page', 'gestiwork'); ?>
                        </button>
                    </div>
                </div>
            </div>

            <div class="gw-settings-group" id="gw-pdf-editor" style="display: none;">
                <h4 class="gw-section-subtitle" id="gw-pdf-editor-title"><?php esc_html_e('3.x Édition du gabarit PDF (en-tête / pied de page)', 'gestiwork'); ?></h4>
                <div class="gw-settings-grid">
                    <div class="gw-settings-field">
                        <input type="hidden" id="gw_pdf_editor_context" value="header" />
                        <input type="hidden" id="gw_pdf_header_html" name="gw_pdf_header_html" value="<?php echo esc_attr($pdfHeaderHtml); ?>" />
                        <input type="hidden" id="gw_pdf_footer_html" name="gw_pdf_footer_html" value="<?php echo esc_attr($pdfFooterHtml); ?>" />
                        <?php
                        if (function_exists('wp_editor')) {
                            wp_editor(
                                $pdfHeaderHtml,
                                'gw_pdf_editor',
                                [
                                    'textarea_name' => 'gw_pdf_editor_content',
                                    'textarea_rows' => 14,
                                    'media_buttons' => false,
                                ]
                            );
                        }
                        ?>
                        <div class="gw-pdf-editor-switch" style="margin-top: 12px;">
                            <button type="button" class="gw-button gw-button--secondary" id="gw-pdf-switch-to-footer" style="display:inline-flex;align-items:center;gap:6px;">
                                <span class="dashicons dashicons-arrow-down-alt"></span>
                                <?php esc_html_e('Passer au pied de page', 'gestiwork'); ?>
                            </button>
                            <button type="button" class="gw-button gw-button--secondary" id="gw-pdf-switch-to-header" style="display:none;align-items:center;gap:6px;">
                                <span class="dashicons dashicons-arrow-up-alt"></span>
                                <?php esc_html_e('Passer à l\'en-tête', 'gestiwork'); ?>
                            </button>
                        </div>
                    </div>
                    <div class="gw-settings-field gw-pdf-shortcodes-panel">
                        <p class="gw-settings-label"><?php esc_html_e('Mots-clés disponibles', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php esc_html_e('Cliquez sur un mot-clé pour l\'insérer dans l\'éditeur.', 'gestiwork'); ?></p>

                        <?php foreach ($pdfShortcodesGrouped as $groupKey => $shortcodes) : ?>
                            <?php $groupLabel = isset($pdfShortcodeGroupLabels[$groupKey]) ? $pdfShortcodeGroupLabels[$groupKey] : ucfirst($groupKey); ?>
                            <div class="gw-pdf-shortcodes-group">
                                <p class="gw-pdf-shortcodes-group-title">
                                    <strong><?php echo esc_html($groupLabel); ?></strong>
                                    <button type="button" class="gw-pdf-shortcodes-toggle" data-group="<?php echo esc_attr($groupKey); ?>" title="<?php esc_attr_e('Afficher/masquer', 'gestiwork'); ?>">
                                        <span class="dashicons dashicons-arrow-down-alt2"></span>
                                    </button>
                                </p>
                                <ul class="gw-pdf-shortcodes-list" data-group="<?php echo esc_attr($groupKey); ?>" style="display:none;">
                                    <?php foreach ($shortcodes as $sc) : ?>
                                        <li>
                                            <code class="gw-pdf-shortcode-insert" data-shortcode="[<?php echo esc_attr($sc['code']); ?>]" title="<?php echo esc_attr($sc['description']); ?>">
                                                <?php echo esc_html($sc['code']); ?>
                                            </code>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="gw-settings-group" id="gw-pdf-actions-group" style="<?php echo $currentPdfTemplate ? '' : 'display:none;'; ?>">
                <div class="gw-settings-grid">
                    <div class="gw-settings-field" style="display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end;">
                        <a href="<?php echo esc_url(home_url('/gestiwork/settings/pdf/')); ?>" class="gw-button gw-button--secondary">
                            <?php esc_html_e('Annuler les modifications PDF', 'gestiwork'); ?>
                        </a>
                        <button type="submit" class="gw-button gw-button--primary" id="gw-pdf-submit-btn">
                            <?php esc_html_e('Enregistrer les réglages PDF', 'gestiwork'); ?>
                        </button>
                    </div>
                </div>
            </div>
            </form>

            <!-- Formulaire caché pour la suppression de modèle -->
            <form method="post" action="" id="gw-pdf-delete-form" style="display:none;">
                <input type="hidden" name="gw_settings_action" value="delete_pdf_template" />
                <input type="hidden" name="gw_pdf_template_id" id="gw_pdf_delete_template_id" value="0" />
                <?php wp_nonce_field('gw_save_pdf_template', 'gw_settings_nonce_pdf'); ?>
            </form>

            <!-- Formulaire caché pour la duplication de modèle -->
            <form method="post" action="" id="gw-pdf-duplicate-form" style="display:none;">
                <input type="hidden" name="gw_settings_action" value="duplicate_pdf_template" />
                <input type="hidden" name="gw_pdf_template_id" id="gw_pdf_duplicate_template_id" value="0" />
                <input type="hidden" name="gw_pdf_duplicate_name" id="gw_pdf_duplicate_name" value="" />
                <?php wp_nonce_field('gw_save_pdf_template', 'gw_settings_nonce_pdf'); ?>
            </form>
        </div>
    </div>

    <div class="gw-modal-backdrop" id="gw-modal-general" aria-hidden="true">
        <div class="gw-modal gw-modal" role="dialog" aria-modal="true" aria-labelledby="gw-modal-general-title">
            <div class="gw-modal-header">
                <h3 class="gw-modal-title" id="gw-modal-general-title"><?php esc_html_e('Modifier – Général & Identité', 'gestiwork'); ?></h3>
                <button type="button" class="gw-modal-close" data-gw-modal-close="gw-modal-general" aria-label="<?php esc_attr_e('Fermer', 'gestiwork'); ?>">×</button>
            </div>
            <form method="post" action="">
                <?php wp_nonce_field('gw_save_of_identity', 'gw_settings_nonce'); ?>
                <input type="hidden" name="gw_settings_action" value="save_of_identity" />
                <div class="gw-modal-body">
                    <p class="gw-modal-required-info">
                        <?php esc_html_e('Tous les champs marqués d’une astérisque rouge (*) sont obligatoires. Pour les numéros de téléphone, au moins un des deux champs (fixe ou portable) doit être renseigné.', 'gestiwork'); ?>
                    </p>
                    <div class="gw-modal-grid">
                        <div class="gw-modal-field">
                            <label for="gw_raison_sociale"><?php esc_html_e('Nom (raison sociale)', 'gestiwork'); ?> <span class="gw-required-asterisk" style="color:#d63638;">*</span></label>
                            <input type="text" class="gw-modal-input" id="gw_raison_sociale" name="gw_raison_sociale" value="<?php echo isset($ofIdentity['raison_sociale']) ? esc_attr($ofIdentity['raison_sociale']) : ''; ?>" required />
                        </div>
                        <div class="gw-modal-field">
                            <label for="gw_representant_nom"><?php esc_html_e('Nom du représentant légal', 'gestiwork'); ?> <span class="gw-required-asterisk" style="color:#d63638;">*</span></label>
                            <input type="text" class="gw-modal-input" id="gw_representant_nom" name="gw_representant_nom" value="<?php echo isset($ofIdentity['representant_nom']) ? esc_attr($ofIdentity['representant_nom']) : ''; ?>" required />
                        </div>
                        <div class="gw-modal-field">
                            <label for="gw_email_contact"><?php esc_html_e('E-mail de contact', 'gestiwork'); ?> <span class="gw-required-asterisk" style="color:#d63638;">*</span></label>
                            <input type="email" class="gw-modal-input" id="gw_email_contact" name="gw_email_contact" value="<?php echo isset($ofIdentity['email_contact']) ? esc_attr($ofIdentity['email_contact']) : ''; ?>" required />
                        </div>
                        <div class="gw-modal-field">
                            <label for="gw_representant_prenom"><?php esc_html_e('Prénom du représentant légal', 'gestiwork'); ?> <span class="gw-required-asterisk" style="color:#d63638;">*</span></label>
                            <input type="text" class="gw-modal-input" id="gw_representant_prenom" name="gw_representant_prenom" value="<?php echo isset($ofIdentity['representant_prenom']) ? esc_attr($ofIdentity['representant_prenom']) : ''; ?>" required />
                        </div>
                        <div class="gw-modal-field">
                            <label for="gw_site_internet"><?php esc_html_e('Site Internet', 'gestiwork'); ?></label>
                            <input type="url" class="gw-modal-input" id="gw_site_internet" name="gw_site_internet" value="<?php echo isset($ofIdentity['site_internet']) ? esc_attr($ofIdentity['site_internet']) : ''; ?>" />
                        </div>
                        <div class="gw-modal-field">
                            <label for="gw_telephone_fixe"><?php esc_html_e('Téléphone fixe', 'gestiwork'); ?> <span class="gw-required-asterisk" style="color:#d63638;">*</span></label>
                            <input
                                type="text"
                                class="gw-modal-input"
                                id="gw_telephone_fixe"
                                name="gw_telephone_fixe"
                                inputmode="tel"
                                pattern="[0-9]{2}( [0-9]{2}){4}"
                                placeholder="00 00 00 00 00"
                                value="<?php echo isset($ofIdentity['telephone_fixe']) ? esc_attr($ofIdentity['telephone_fixe']) : ''; ?>"
                            />
                        </div>
                        <div class="gw-modal-field">
                            <label for="gw_telephone_portable"><?php esc_html_e('Téléphone portable', 'gestiwork'); ?> <span class="gw-required-asterisk" style="color:#d63638;">*</span></label>
                            <input
                                type="text"
                                class="gw-modal-input"
                                id="gw_telephone_portable"
                                name="gw_telephone_portable"
                                inputmode="tel"
                                pattern="[0-9]{2}( [0-9]{2}){4}"
                                placeholder="00 00 00 00 00"
                                value="<?php echo isset($ofIdentity['telephone_portable']) ? esc_attr($ofIdentity['telephone_portable']) : ''; ?>"
                            />
                        </div>
                        <div class="gw-modal-field">
                            <label for="gw_adresse"><?php esc_html_e('Adresse', 'gestiwork'); ?> <span class="gw-required-asterisk" style="color:#d63638;">*</span></label>
                            <textarea class="gw-modal-textarea" id="gw_adresse" name="gw_adresse" required><?php echo isset($ofIdentity['adresse']) ? esc_textarea($ofIdentity['adresse']) : ''; ?></textarea>
                        </div>
                        <div class="gw-modal-field">
                            <label for="gw_code_postal"><?php esc_html_e('Code postal', 'gestiwork'); ?> <span class="gw-required-asterisk" style="color:#d63638;">*</span></label>
                            <input type="text" class="gw-modal-input" id="gw_code_postal" name="gw_code_postal" value="<?php echo isset($ofIdentity['code_postal']) ? esc_attr($ofIdentity['code_postal']) : ''; ?>" required />
                        </div>
                        <div class="gw-modal-field">
                            <label for="gw_ville"><?php esc_html_e('Ville', 'gestiwork'); ?> <span class="gw-required-asterisk" style="color:#d63638;">*</span></label>
                            <input type="text" class="gw-modal-input" id="gw_ville" name="gw_ville" value="<?php echo isset($ofIdentity['ville']) ? esc_attr($ofIdentity['ville']) : ''; ?>" required />
                        </div>
                        
                        <div class="gw-modal-field">
                            <label for="gw_siret"><?php esc_html_e('SIRET / SIREN', 'gestiwork'); ?> <span class="gw-required-asterisk" style="color:#d63638;">*</span></label>
                            <input
                                type="text"
                                class="gw-modal-input"
                                id="gw_siret"
                                name="gw_siret"
                                pattern="([0-9]{3} [0-9]{3} [0-9]{3})( [0-9]{5})?"
                                placeholder="SIREN : 123 456 789 ou SIRET : 123 456 789 00012"
                                value="<?php echo isset($ofIdentity['siret']) ? esc_attr($ofIdentity['siret']) : ''; ?>"
                                required
                            />
                            <small class="gw-modal-small-info">
                                <?php esc_html_e('SIREN (9 chiffres), soit SIRET (14 chiffres).', 'gestiwork'); ?>
                            </small>
                        </div>
                        <div class="gw-modal-field">
                            <label for="gw_code_ape"><?php esc_html_e('Code APE (NAF)', 'gestiwork'); ?> <span class="gw-required-asterisk" style="color:#d63638;">*</span></label>
                            <input type="text" class="gw-modal-input" id="gw_code_ape" name="gw_code_ape" value="<?php echo isset($ofIdentity['code_ape']) ? esc_attr($ofIdentity['code_ape']) : ''; ?>" required />
                        </div>
                        <div class="gw-modal-field">
                            <label for="gw_rcs"><?php esc_html_e('RCS / immatriculation', 'gestiwork'); ?></label>
                            <input type="text" class="gw-modal-input" id="gw_rcs" name="gw_rcs" value="<?php echo isset($ofIdentity['rcs']) ? esc_attr($ofIdentity['rcs']) : ''; ?>" />
                        </div>
                        <div class="gw-modal-field">
                            <label for="gw_nda"><?php esc_html_e('NDA (numéro de déclaration d’activité)', 'gestiwork'); ?> <span class="gw-required-asterisk" style="color:#d63638;">*</span></label>
                            <input type="text" class="gw-modal-input" id="gw_nda" name="gw_nda" value="<?php echo isset($ofIdentity['nda']) ? esc_attr($ofIdentity['nda']) : ''; ?>" required />
                        </div>
                        <div class="gw-modal-field">
                            <label for="gw_qualiopi"><?php esc_html_e('Qualiopi', 'gestiwork'); ?></label>
                            <input
                                type="text"
                                class="gw-modal-input"
                                id="gw_qualiopi"
                                name="gw_qualiopi"
                                placeholder="<?php esc_attr_e('N° de certification Qualiopi', 'gestiwork'); ?>"
                                value="<?php echo isset($ofIdentity['qualiopi']) ? esc_attr($ofIdentity['qualiopi']) : ''; ?>"
                            />
                        </div>
                        <div class="gw-modal-field">
                            <label for="gw_datadock"><?php esc_html_e('Datadock', 'gestiwork'); ?></label>
                            <input
                                type="text"
                                class="gw-modal-input"
                                id="gw_datadock"
                                name="gw_datadock"
                                placeholder="<?php esc_attr_e('Référence Datadock', 'gestiwork'); ?>"
                                value="<?php echo isset($ofIdentity['datadock']) ? esc_attr($ofIdentity['datadock']) : ''; ?>"
                            />
                        </div>
                        <div class="gw-modal-field">
                            <label for="gw_habilitation_inrs"><?php esc_html_e('Habilitation INRS', 'gestiwork'); ?></label>
                            <input
                                type="text"
                                class="gw-modal-input"
                                id="gw_habilitation_inrs"
                                name="gw_habilitation_inrs"
                                value="<?php echo isset($ofIdentity['habilitation_inrs']) ? esc_attr($ofIdentity['habilitation_inrs']) : ''; ?>"
                            />
                        </div>
                        <div class="gw-modal-field">
                            <label for="gw_rm"><?php esc_html_e('RM (registre des métiers)', 'gestiwork'); ?></label>
                            <input
                                type="text"
                                class="gw-modal-input"
                                id="gw_rm"
                                name="gw_rm"
                                placeholder="<?php esc_attr_e('Ex. : RM Paris 123 456 789', 'gestiwork'); ?>"
                                value="<?php echo isset($ofIdentity['rm']) ? esc_attr($ofIdentity['rm']) : ''; ?>"
                            />
                        </div>
                        <div class="gw-modal-field">
                            <label for="gw_regime_tva"><?php esc_html_e('Régime de TVA', 'gestiwork'); ?></label>
                            <select class="gw-modal-input" id="gw_regime_tva" name="gw_regime_tva">
                                <option value="exonere" <?php selected($regimeActuel, 'exonere'); ?>><?php esc_html_e('Exonéré (article 261-4-4 du CGI).', 'gestiwork'); ?></option>
                                <option value="assujetti" <?php selected($regimeActuel, 'assujetti'); ?>><?php esc_html_e('Assujetti', 'gestiwork'); ?></option>
                            </select>
                        </div>
                        <div class="gw-modal-field gw-modal-tva-card" id="gw_tva_card"<?php if (!$showTvaCard) : ?> style="display: none;"<?php endif; ?>>
                            <label for="gw_tva_intracom"><?php esc_html_e('TVA intracommunautaire', 'gestiwork'); ?></label>
                            <input type="text" class="gw-modal-input" id="gw_tva_intracom" name="gw_tva_intracom" value="<?php echo isset($ofIdentity['tva_intracom']) ? esc_attr($ofIdentity['tva_intracom']) : ''; ?>" />

                            <label for="gw_taux_tva" style="margin-top: 6px; display: block;"><?php esc_html_e('Taux de TVA par défaut', 'gestiwork'); ?></label>
                            <select class="gw-modal-input" id="gw_taux_tva" name="gw_taux_tva">
                                <?php
                                $taux_actuel = isset($ofIdentity['taux_tva']) ? (string) $ofIdentity['taux_tva'] : '20';
                                $taux_options = ['5.5', '10', '20'];
                                foreach ($taux_options as $taux) :
                                    ?>
                                    <option value="<?php echo esc_attr($taux); ?>" <?php selected($taux_actuel, $taux); ?>><?php echo esc_html($taux); ?>%</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="gw-modal-field">
                            <label for="gw_banque_principale"><?php esc_html_e('Banque principale', 'gestiwork'); ?></label>
                            <input
                                type="text"
                                class="gw-modal-input"
                                id="gw_banque_principale"
                                name="gw_banque_principale"
                                placeholder="<?php esc_attr_e('Nom de la banque où sont émis les règlements.', 'gestiwork'); ?>"
                                value="<?php echo isset($ofIdentity['banque_principale']) ? esc_attr($ofIdentity['banque_principale']) : ''; ?>"
                            />
                        </div>
                        <div class="gw-modal-field">
                            <label for="gw_iban"><?php esc_html_e('IBAN', 'gestiwork'); ?></label>
                            <input
                                type="text"
                                class="gw-modal-input"
                                id="gw_iban"
                                name="gw_iban"
                                inputmode="text"
                                pattern="[A-Z]{2}[0-9]{2}[A-Z0-9]{11,30}"
                                maxlength="34"
                                style="text-transform: uppercase;"
                                value="<?php echo isset($ofIdentity['iban']) ? esc_attr($ofIdentity['iban']) : ''; ?>"
                            />
                            <small class="gw-modal-small-info">
                                <?php esc_html_e('IBAN complet sans espaces (ex. FR7612345678901234567890123).', 'gestiwork'); ?>
                            </small>
                        </div>
                        <div class="gw-modal-field">
                            <label for="gw_bic"><?php esc_html_e('BIC', 'gestiwork'); ?></label>
                            <input
                                type="text"
                                class="gw-modal-input"
                                id="gw_bic"
                                name="gw_bic"
                                inputmode="text"
                                pattern="[A-Z]{4}[A-Z]{2}[A-Z0-9]{2}([A-Z0-9]{3})?"
                                maxlength="11"
                                style="text-transform: uppercase;"
                                value="<?php echo isset($ofIdentity['bic']) ? esc_attr($ofIdentity['bic']) : ''; ?>"
                            />
                            <small class="gw-modal-small-info">
                                <?php esc_html_e('Code BIC de 8 ou 11 caractères (ex. ABCDFRPPXXX).', 'gestiwork'); ?>
                            </small>
                        </div>
                        <div class="gw-modal-field">
                            <label for="gw_format_numero_devis"><?php esc_html_e('Format des numéros de devis / propositions', 'gestiwork'); ?> <span class="gw-required-asterisk" style="color:#d63638;">*</span></label>
                            <input
                                type="text"
                                class="gw-modal-input"
                                id="gw_format_numero_devis"
                                name="gw_format_numero_devis"
                                placeholder="<?php esc_attr_e('Exemple : GW-DEV-{annee}-{compteur}', 'gestiwork'); ?>"
                                value="<?php echo isset($ofIdentity['format_numero_devis']) ? esc_attr($ofIdentity['format_numero_devis']) : ''; ?>"
                                required
                            />
                        </div>
                        <div class="gw-modal-field">
                            <label for="gw_compteur_devis"><?php esc_html_e('Compteur courant', 'gestiwork'); ?></label>
                            <input type="number" min="1" class="gw-modal-input" id="gw_compteur_devis" name="gw_compteur_devis" value="<?php echo isset($ofIdentity['compteur_devis']) ? esc_attr((string) $ofIdentity['compteur_devis']) : '1'; ?>" />
                        </div>
                    </div>
                </div>
                <p id="gw_identity_error" class="gw-modal-error-info" style="display:none; color:#d63638; font-size:12px; margin: 4px 16px 0 16px;"></p>
                <div class="gw-modal-footer">
                    <button type="button" class="gw-button gw-button--secondary" data-gw-modal-close="gw-modal-general"><?php esc_html_e('Annuler', 'gestiwork'); ?></button>
                    <button type="submit" class="gw-button gw-button--primary" id="gw_identity_submit"><?php esc_html_e('Enregistrer', 'gestiwork'); ?></button>
                </div>
            </form>
        </div>
    </div>
    <div class="gw-modal-backdrop" id="gw-modal-options" aria-hidden="true">
            <div class="gw-modal gw-modal" role="dialog" aria-modal="true" aria-labelledby="gw-modal-options-title">
                <div class="gw-modal-header">
                    <h3 class="gw-modal-title" id="gw-modal-options-title"><?php esc_html_e('Modifier – Options générales', 'gestiwork'); ?></h3>
                    <button type="button" class="gw-modal-close" data-gw-modal-close="gw-modal-options" aria-label="<?php esc_attr_e('Fermer', 'gestiwork'); ?>">×</button>
                </div>
                <form method="post" action="">
                    <?php wp_nonce_field('gw_save_options', 'gw_settings_nonce_options'); ?>
                    <input type="hidden" name="gw_settings_action" value="save_options" />
                    <div class="gw-modal-body">
                        <p class="gw-modal-small-info">
                            <?php esc_html_e('Les champs marqués d’une astérisque rouge (*) sont obligatoires.', 'gestiwork'); ?>
                        </p>
                        <div class="gw-modal-grid">
                            <div class="gw-modal-field">
                                <label for="gw_first_year">
                                    <?php esc_html_e('Première année d’activité gérée', 'gestiwork'); ?>
                                    <span class="gw-required-asterisk" style="color:#d63638;">*</span>
                                </label>
                                <input
                                    type="number"
                                    class="gw-modal-input"
                                    id="gw_first_year"
                                    name="gw_first_year"
                                    min="2000"
                                    max="2100"
                                    required
                                    value="<?php echo isset($options['first_year']) ? esc_attr((string) $options['first_year']) : ''; ?>"
                                />
                            </div>
                            <div class="gw-modal-field">
                                <label for="gw_min_hours_between_signature_emails">
                                    <?php esc_html_e('Délai minimum entre deux e-mails de demande de signature (heures)', 'gestiwork'); ?>
                                    <span class="gw-required-asterisk" style="color:#d63638;">*</span>
                                </label>
                                <input
                                    type="number"
                                    min="0"
                                    class="gw-modal-input"
                                    id="gw_min_hours_between_signature_emails"
                                    name="gw_min_hours_between_signature_emails"
                                    required
                                    value="<?php echo isset($options['min_hours_between_signature_emails']) ? esc_attr((string) $options['min_hours_between_signature_emails']) : ''; ?>"
                                />
                            </div>
                            <div class="gw-modal-field">
                                <label for="gw_max_days_veille_alert">
                                    <?php esc_html_e('Délai maximum avant alerte sur la veille personnelle (jours)', 'gestiwork'); ?>
                                    <span class="gw-required-asterisk" style="color:#d63638;">*</span>
                                </label>
                                <input
                                    type="number"
                                    min="0"
                                    class="gw-modal-input"
                                    id="gw_max_days_veille_alert"
                                    name="gw_max_days_veille_alert"
                                    required
                                    value="<?php echo isset($options['max_days_veille_alert']) ? esc_attr((string) $options['max_days_veille_alert']) : ''; ?>"
                                />
                            </div>
                            <div class="gw-modal-field">
                                <label for="gw_token_validity_hours">
                                    <?php esc_html_e('Durée de validité du jeton de connexion (heures, 0 pour illimité)', 'gestiwork'); ?>
                                    <span class="gw-required-asterisk" style="color:#d63638;">*</span>
                                </label>
                                <input
                                    type="number"
                                    min="0"
                                    class="gw-modal-input"
                                    id="gw_token_validity_hours"
                                    name="gw_token_validity_hours"
                                    required
                                    value="<?php echo isset($options['token_validity_hours']) ? esc_attr((string) $options['token_validity_hours']) : ''; ?>"
                                />
                            </div>
                            <div class="gw-modal-field">
                                <label for="gw_min_hourly_rate"><?php esc_html_e('Tarif horaire plancher (€ / heure)', 'gestiwork'); ?></label>
                                <input type="number" min="0" step="0.01" class="gw-modal-input" id="gw_min_hourly_rate" name="gw_min_hourly_rate" value="<?php echo isset($options['min_hourly_rate']) ? esc_attr((string) $options['min_hourly_rate']) : ''; ?>" />
                            </div>
                            <div class="gw-modal-field">
                                <label for="gw_default_deposit_percent"><?php esc_html_e('Pourcentage par défaut pour l’acompte (%)', 'gestiwork'); ?></label>
                                <input type="number" min="0" max="100" step="0.01" class="gw-modal-input" id="gw_default_deposit_percent" name="gw_default_deposit_percent" value="<?php echo isset($options['default_deposit_percent']) ? esc_attr((string) $options['default_deposit_percent']) : ''; ?>" />
                            </div>
                            <div class="gw-modal-field">
                                <label for="gw_max_log_rows"><?php esc_html_e('Nombre maximum de lignes de log chargées', 'gestiwork'); ?></label>
                                <input type="number" min="0" class="gw-modal-input" id="gw_max_log_rows" name="gw_max_log_rows" value="<?php echo isset($options['max_log_rows']) ? esc_attr((string) $options['max_log_rows']) : ''; ?>" />
                            </div>
                            <div class="gw-modal-field">
                                <label for="gw_attendance_sheet_lines"><?php esc_html_e('Nombre de lignes par feuille d’émargement', 'gestiwork'); ?></label>
                                <input type="number" min="0" class="gw-modal-input" id="gw_attendance_sheet_lines" name="gw_attendance_sheet_lines" value="<?php echo isset($options['attendance_sheet_lines']) ? esc_attr((string) $options['attendance_sheet_lines']) : ''; ?>" />
                            </div>
                            <div class="gw-modal-field">
                                <label for="gw_taxonomy_mode"><?php esc_html_e('Taxonomies pour les formations et sessions', 'gestiwork'); ?></label>
                                <select class="gw-modal-input" id="gw_taxonomy_mode" name="gw_taxonomy_mode">
                                    <option value="categories" <?php selected(isset($options['taxonomy_mode']) ? $options['taxonomy_mode'] : 'categories', 'categories'); ?>><?php esc_html_e('Catégories (arborescence)', 'gestiwork'); ?></option>
                                    <option value="tags" <?php selected(isset($options['taxonomy_mode']) ? $options['taxonomy_mode'] : 'categories', 'tags'); ?>><?php esc_html_e('Étiquettes (classification transversale)', 'gestiwork'); ?></option>
                                </select>
                            </div>
                            <div class="gw-modal-field" style="grid-column: 1 / -1;">
                                <p><strong><?php esc_html_e('Champs additionnels et comportements', 'gestiwork'); ?></strong></p>
                                <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px 24px;align-items:flex-start;">
                                    <div>
                                        <label for="gw_enable_client_contract_number">
                                            <input type="checkbox" id="gw_enable_client_contract_number" name="gw_enable_client_contract_number" value="1" <?php checked(!empty($options['enable_client_contract_number']), true); ?> />
                                            <?php esc_html_e('Numéro de contrat pour les clients', 'gestiwork'); ?>
                                        </label>
                                        <small class="gw-modal-small-info">
                                            <?php esc_html_e('Champ dédié au numéro de contrat client présent sur les documents.', 'gestiwork'); ?>
                                        </small>
                                    </div>
                                    <div>
                                        <label for="gw_enable_document_validity_period">
                                            <input type="checkbox" id="gw_enable_document_validity_period" name="gw_enable_document_validity_period" value="1" <?php checked(!empty($options['enable_document_validity_period']), true); ?> />
                                            <?php esc_html_e('Période de validité des documents', 'gestiwork'); ?>
                                        </label>
                                        <small class="gw-modal-small-info">
                                            <?php esc_html_e('Date de validité définie et affichée sur les devis à signer.', 'gestiwork'); ?>
                                        </small>
                                    </div>
                                    <div>
                                        <label for="gw_enable_trainer_status_activity_code">
                                            <input type="checkbox" id="gw_enable_trainer_status_activity_code" name="gw_enable_trainer_status_activity_code" value="1" <?php checked(!empty($options['enable_trainer_status_activity_code']), true); ?> />
                                            <?php esc_html_e('Infos statut des formateurs', 'gestiwork'); ?>
                                        </label>
                                        <small class="gw-modal-small-info">
                                            <?php esc_html_e('vous pourrez renseigner pour chaque formateur son statut (salarié, indépendant, etc.) et un code interne.', 'gestiwork'); ?>
                                        </small>
                                    </div>
                                    <div>
                                        <label for="gw_enable_free_text_duration">
                                            <input type="checkbox" id="gw_enable_free_text_duration" name="gw_enable_free_text_duration" value="1" <?php checked(!empty($options['enable_free_text_duration']), true); ?> />
                                            <?php esc_html_e('Durée des actions', 'gestiwork'); ?>
                                        </label>
                                        <small class="gw-modal-small-info">
                                            <?php esc_html_e('Permet de definir le decoupage de la session (journée, 1/2journée)', 'gestiwork'); ?>
                                        </small>
                                    </div>
                                    <div>
                                        <label for="gw_enable_signature_image">
                                            <input type="checkbox" id="gw_enable_signature_image" name="gw_enable_signature_image" value="1" <?php checked(!empty($options['enable_signature_image']), true); ?> />
                                            <?php esc_html_e('Image de signature', 'gestiwork'); ?>
                                        </label>
                                        <small class="gw-modal-small-info">
                                            <?php esc_html_e('Une image de signature du responsable pourra être téléversée et affichée sur certains documents.', 'gestiwork'); ?>
                                        </small>
                                    </div>
                                    <div>
                                        <label for="gw_enable_impersonation_login">
                                            <input type="checkbox" id="gw_enable_impersonation_login" name="gw_enable_impersonation_login" value="1" <?php checked(!empty($options['enable_impersonation_login']), true); ?> />
                                            <?php esc_html_e('Connexion en tant que…', 'gestiwork'); ?>
                                        </label>
                                        <small class="gw-modal-small-info">
                                            <?php esc_html_e('les responsables autorisés pourront se connecter à la place d’un autre utilisateur depuis l’interface.', 'gestiwork'); ?>
                                        </small>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="gw-modal-footer">
                        <button type="button" class="gw-button gw-button--secondary" data-gw-modal-close="gw-modal-options"><?php esc_html_e('Annuler', 'gestiwork'); ?></button>
                        <button type="submit" class="gw-button gw-button--primary"><?php esc_html_e('Enregistrer', 'gestiwork'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
    (function () {
        var tabs = document.querySelectorAll('.gw-settings-tab');
        var panels = document.querySelectorAll('.gw-settings-panel');
        var modalTriggers = document.querySelectorAll('[data-gw-modal-target]');
        var modalCloseButtons = document.querySelectorAll('[data-gw-modal-close]');
        var allModals = document.querySelectorAll('.gw-modal-backdrop');

        if (tabs.length && panels.length) {
            tabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    var target = tab.getAttribute('data-gw-tab');
                    if (!target) {
                        return;
                    }

                    // Mise à jour de l'état visuel des onglets
                    tabs.forEach(function (t) {
                        t.classList.remove('gw-settings-tab--active');
                    });
                    panels.forEach(function (panel) {
                        panel.classList.remove('gw-settings-panel--active');
                        if (panel.getAttribute('data-gw-tab-panel') === target) {
                            panel.classList.add('gw-settings-panel--active');
                        }
                    });

                    tab.classList.add('gw-settings-tab--active');

                    // Mise à jour de l'URL pour refléter l'onglet actif, sans recharger la page
                    try {
                        if (typeof window !== 'undefined' && window.history && typeof window.history.replaceState === 'function') {
                            var loc = window.location;
                            var path = loc.pathname || '';
                            var newPath = path;

                            if (/\/settings\/[^/]+\/?$/.test(path)) {
                                // Remplace le dernier segment après /settings/
                                newPath = path.replace(/(\/settings\/)[^/]+\/?$/, '$1' + target + '/');
                            } else if (/\/settings\/?$/.test(path)) {
                                // Aucun segment de section encore présent
                                newPath = path.replace(/\/settings\/?$/, '/settings/' + target + '/');
                            }

                            if (newPath && newPath !== path) {
                                var newUrl = newPath + (loc.search || '') + (loc.hash || '');
                                window.history.replaceState(null, '', newUrl);
                            }
                        }
                    } catch (e) {
                        // En cas d'erreur, on ne bloque pas le changement d'onglet
                    }
                });
            });
        }

        modalTriggers.forEach(function (trigger) {
            trigger.addEventListener('click', function () {
                var targetId = trigger.getAttribute('data-gw-modal-target');
                if (!targetId) {
                    return;
                }

                // Ferme d'abord toutes les autres modales éventuellemen	 ouvertes
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

                    if (typeof modal.scrollIntoView === 'function') {
                        try {
                            modal.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        } catch (e) {
                            // ignore
                        }
                    }
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

        // Formatage automatique des champs au blur
        function gwFormatPhone(value) {
            if (!value) {
                return '';
            }
            var digits = value.replace(/\D/g, '').slice(0, 10);
            if (digits.length !== 10) {
                return value.trim();
            }
            return digits.replace(/(\d{2})(?=\d)/g, '$1 ').trim();
        }

        function gwFormatSiret(value) {
            if (!value) {
                return '';
            }
            var digits = value.replace(/\D/g, '');
            if (digits.length === 9) {
                // SIREN : 9 chiffres -> 123 456 789
                return digits.replace(/^(\d{3})(\d{3})(\d{3})$/, '$1 $2 $3');
            }
            if (digits.length === 14) {
                // SIRET : 14 chiffres -> 123 456 789 00012
                return digits.replace(/^(\d{3})(\d{3})(\d{3})(\d{5})$/, '$1 $2 $3 $4');
            }
            return value.trim();
        }

        function gwNormalizeIban(value) {
            if (!value) {
                return '';
            }
            return value.toUpperCase().replace(/\s+/g, '');
        }

        function gwNormalizeBic(value) {
            if (!value) {
                return '';
            }
            return value.toUpperCase().replace(/\s+/g, '');
        }

        var telFixe = document.getElementById('gw_telephone_fixe');
        if (telFixe) {
            telFixe.addEventListener('blur', function () {
                telFixe.value = gwFormatPhone(telFixe.value);
            });
        }

        var telPortable = document.getElementById('gw_telephone_portable');
        if (telPortable) {
            telPortable.addEventListener('blur', function () {
                telPortable.value = gwFormatPhone(telPortable.value);
            });
        }

        var siretField = document.getElementById('gw_siret');
        if (siretField) {
            siretField.addEventListener('blur', function () {
                siretField.value = gwFormatSiret(siretField.value);
            });
        }

        var ibanField = document.getElementById('gw_iban');
        if (ibanField) {
            ibanField.addEventListener('blur', function () {
                ibanField.value = gwNormalizeIban(ibanField.value);
            });
        }

        var bicField = document.getElementById('gw_bic');
        if (bicField) {
            bicField.addEventListener('blur', function () {
                bicField.value = gwNormalizeBic(bicField.value);
            });
        }

        var regimeSelect = document.getElementById('gw_regime_tva');
        var tvaCard = document.getElementById('gw_tva_card');
        if (regimeSelect && tvaCard) {
            var updateTvaCard = function () {
                if (regimeSelect.value === 'assujetti') {
                    tvaCard.style.display = '';
                } else {
                    tvaCard.style.display = 'none';
                }
            };
            updateTvaCard();
            regimeSelect.addEventListener('change', updateTvaCard);
        }

        // Gestion de la zone d'édition PDF (en-tête / pied de page)
        var pdfEditorGroup = document.getElementById('gw-pdf-editor');
        var pdfEditorTitle = document.getElementById('gw-pdf-editor-title');
        var pdfEditorContext = document.getElementById('gw_pdf_editor_context');
        var openPdfHeaderBtn = document.getElementById('gw-open-pdf-header-editor');
        var openPdfFooterBtn = document.getElementById('gw-open-pdf-footer-editor');

        function gwOpenPdfEditor(context) {
            if (!pdfEditorGroup) {
                return;
            }

            pdfEditorGroup.style.display = '';

            if (pdfEditorContext) {
                pdfEditorContext.value = context;
            }

            if (pdfEditorTitle) {
                if (context === 'header') {
                    pdfEditorTitle.textContent = 'Modifier l\'en-tête';
                } else if (context === 'footer') {
                    pdfEditorTitle.textContent = 'Modifier le pied de page';
                }
            }

            if (typeof pdfEditorGroup.scrollIntoView === 'function') {
                try {
                    pdfEditorGroup.scrollIntoView({ behavior: 'smooth', block: 'start' });
                } catch (e) {
                    // ignore
                }
            }
        }

        if (openPdfHeaderBtn) {
            openPdfHeaderBtn.addEventListener('click', function () {
                gwOpenPdfEditor('header');
            });
        }

        if (openPdfFooterBtn) {
            openPdfFooterBtn.addEventListener('click', function () {
                gwOpenPdfEditor('footer');
            });
        }

        // Ouverture / fermeture de la zone 3.2 Mise en forme PDF
        var pdfLayoutToggle = document.getElementById('gw-toggle-pdf-layout');
        var pdfLayoutBody = document.getElementById('gw-pdf-layout-body');
        if (pdfLayoutToggle && pdfLayoutBody) {
            pdfLayoutToggle.addEventListener('click', function () {
                var current = pdfLayoutBody.style.display;
                if (!current || current === 'none') {
                    pdfLayoutBody.style.display = '';
                } else {
                    pdfLayoutBody.style.display = 'none';
                }
            });
        }

        // Affichage conditionnel des groupes avancés PDF (3.2 / 3.3)
        var pdfLayoutGroup = document.getElementById('gw-pdf-layout-group');
        var pdfHeaderFooterGroup = document.getElementById('gw-pdf-header-footer-group');
        var pdfActionsGroup = document.getElementById('gw-pdf-actions-group');
        var pdfCreateBtn = document.getElementById('gw_pdf_create_btn');
        var pdfCancelBtn = document.getElementById('gw_pdf_cancel_btn');
        var pdfModelNameInput = document.getElementById('gw_pdf_model_name');

        if (pdfCreateBtn && pdfLayoutGroup && pdfHeaderFooterGroup && pdfActionsGroup && pdfModelNameInput) {
            pdfCreateBtn.addEventListener('click', function () {
                var name = pdfModelNameInput.value ? pdfModelNameInput.value.trim() : '';
                if (name === '') {
                    window.alert('<?php echo esc_js(__('Veuillez saisir un nom de modèle avant de continuer.', 'gestiwork')); ?>');
                    pdfModelNameInput.focus();
                    return;
                }

                // Afficher les groupes de réglages avancés pour permettre de configurer le modèle
                pdfLayoutGroup.style.display = '';
                pdfHeaderFooterGroup.style.display = '';
                pdfActionsGroup.style.display = '';

                // Ouvrir par défaut le bloc 3.2 si l'utilisateur ne l'a jamais ouvert
                if (pdfLayoutBody && (!pdfLayoutBody.style.display || pdfLayoutBody.style.display === 'none')) {
                    pdfLayoutBody.style.display = '';
                }
            });
        }

        if (pdfCancelBtn && pdfLayoutGroup && pdfHeaderFooterGroup && pdfActionsGroup && pdfModelNameInput) {
            pdfCancelBtn.addEventListener('click', function () {
                // Réinitialiser simplement le nom et masquer les groupes avancés.
                pdfModelNameInput.value = '';
                pdfLayoutGroup.style.display = 'none';
                pdfHeaderFooterGroup.style.display = 'none';
                pdfActionsGroup.style.display = 'none';
            });
        }

        // Gestion de la suppression des modèles PDF
        var pdfDeleteButtons = document.querySelectorAll('.gw-pdf-delete-template');
        var pdfDeleteForm = document.getElementById('gw-pdf-delete-form');
        var pdfDeleteTemplateId = document.getElementById('gw_pdf_delete_template_id');
        if (pdfDeleteForm && pdfDeleteTemplateId && pdfDeleteButtons.length) {
            pdfDeleteButtons.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var templateId = btn.getAttribute('data-template-id');
                    var templateName = btn.getAttribute('data-template-name');
                    if (confirm('Êtes-vous sûr de vouloir supprimer le modèle "' + templateName + '" ?')) {
                        pdfDeleteTemplateId.value = templateId;
                        pdfDeleteForm.submit();
                    }
                });
            });
        }

        // Gestion de la duplication des modèles PDF
        var pdfDuplicateButtons = document.querySelectorAll('.gw-pdf-duplicate-template');
        var pdfDuplicateForm = document.getElementById('gw-pdf-duplicate-form');
        var pdfDuplicateTemplateId = document.getElementById('gw_pdf_duplicate_template_id');
        var pdfDuplicateNameInput = document.getElementById('gw_pdf_duplicate_name');
        if (pdfDuplicateForm && pdfDuplicateTemplateId && pdfDuplicateNameInput && pdfDuplicateButtons.length) {
            pdfDuplicateButtons.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var templateId = btn.getAttribute('data-template-id');
                    var templateName = btn.getAttribute('data-template-name');
                    var defaultName = templateName ? (templateName + ' (copie)') : '';
                    var newName = window.prompt('Nouveau nom pour le modèle dupliqué :', defaultName);
                    if (newName === null) {
                        return; // Annulé
                    }
                    newName = newName.trim();
                    if (newName === '') {
                        return; // On ne crée pas sans nom
                    }

                    pdfDuplicateTemplateId.value = templateId;
                    pdfDuplicateNameInput.value = newName;
                    pdfDuplicateForm.submit();
                });
            });
        }

        // Synchronisation du contenu TinyMCE avec les champs cachés header/footer
        var pdfHeaderHtmlInput = document.getElementById('gw_pdf_header_html');
        var pdfFooterHtmlInput = document.getElementById('gw_pdf_footer_html');
        var pdfTemplateForm = document.getElementById('gw-pdf-template-form');

        function gwSyncTinyMceToHiddenField() {
            var context = pdfEditorContext ? pdfEditorContext.value : 'header';
            var editorContent = '';

            // Récupérer le contenu de TinyMCE
            if (typeof tinymce !== 'undefined' && tinymce.get('gw_pdf_editor')) {
                editorContent = tinymce.get('gw_pdf_editor').getContent();
            } else {
                var textarea = document.getElementById('gw_pdf_editor');
                if (textarea) {
                    editorContent = textarea.value;
                }
            }

            // Stocker dans le bon champ caché
            if (context === 'header' && pdfHeaderHtmlInput) {
                pdfHeaderHtmlInput.value = editorContent;
            } else if (context === 'footer' && pdfFooterHtmlInput) {
                pdfFooterHtmlInput.value = editorContent;
            }
        }

        function gwLoadTinyMceFromHiddenField(context) {
            var content = '';
            if (context === 'header' && pdfHeaderHtmlInput) {
                content = pdfHeaderHtmlInput.value;
            } else if (context === 'footer' && pdfFooterHtmlInput) {
                content = pdfFooterHtmlInput.value;
            }

            // Charger dans TinyMCE
            if (typeof tinymce !== 'undefined' && tinymce.get('gw_pdf_editor')) {
                tinymce.get('gw_pdf_editor').setContent(content);
            } else {
                var textarea = document.getElementById('gw_pdf_editor');
                if (textarea) {
                    textarea.value = content;
                }
            }
        }

        // Modifier gwOpenPdfEditor pour synchroniser avant de changer de contexte
        var originalGwOpenPdfEditor = gwOpenPdfEditor;
        gwOpenPdfEditor = function (context) {
            // Sauvegarder le contenu actuel avant de changer de contexte
            gwSyncTinyMceToHiddenField();
            // Charger le nouveau contenu
            gwLoadTinyMceFromHiddenField(context);
            // Appeler la fonction originale
            originalGwOpenPdfEditor(context);
        };

        // Synchroniser avant la soumission du formulaire
        if (pdfTemplateForm) {
            pdfTemplateForm.addEventListener('submit', function () {
                gwSyncTinyMceToHiddenField();
            });
        }

        // Toggle des groupes de shortcodes (afficher/masquer)
        var shortcodeToggles = document.querySelectorAll('.gw-pdf-shortcodes-toggle');
        shortcodeToggles.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var groupKey = btn.getAttribute('data-group');
                var list = document.querySelector('.gw-pdf-shortcodes-list[data-group="' + groupKey + '"]');
                var icon = btn.querySelector('.dashicons');
                if (list) {
                    if (list.style.display === 'none' || list.style.display === '') {
                        list.style.display = 'block';
                        if (icon) {
                            icon.classList.remove('dashicons-arrow-down-alt2');
                            icon.classList.add('dashicons-arrow-up-alt2');
                        }
                    } else {
                        list.style.display = 'none';
                        if (icon) {
                            icon.classList.remove('dashicons-arrow-up-alt2');
                            icon.classList.add('dashicons-arrow-down-alt2');
                        }
                    }
                }
            });
        });

        // Insertion des shortcodes dans TinyMCE au clic
        var shortcodeInsertButtons = document.querySelectorAll('.gw-pdf-shortcode-insert');
        shortcodeInsertButtons.forEach(function (code) {
            code.style.cursor = 'pointer';
            code.addEventListener('click', function () {
                var shortcode = code.getAttribute('data-shortcode');
                if (!shortcode) return;

                // Insérer dans TinyMCE si disponible
                if (typeof tinymce !== 'undefined' && tinymce.get('gw_pdf_editor')) {
                    tinymce.get('gw_pdf_editor').insertContent(shortcode);
                    tinymce.get('gw_pdf_editor').focus();
                } else {
                    // Fallback sur le textarea
                    var textarea = document.getElementById('gw_pdf_editor');
                    if (textarea) {
                        var start = textarea.selectionStart;
                        var end = textarea.selectionEnd;
                        var text = textarea.value;
                        textarea.value = text.substring(0, start) + shortcode + text.substring(end);
                        textarea.selectionStart = textarea.selectionEnd = start + shortcode.length;
                        textarea.focus();
                    }
                }
            });
        });

        // Boutons pour basculer entre en-tête et pied de page
        var switchToFooterBtn = document.getElementById('gw-pdf-switch-to-footer');
        var switchToHeaderBtn = document.getElementById('gw-pdf-switch-to-header');

        if (switchToFooterBtn && switchToHeaderBtn) {
            switchToFooterBtn.addEventListener('click', function () {
                gwOpenPdfEditor('footer');
                switchToFooterBtn.style.display = 'none';
                switchToHeaderBtn.style.display = 'inline-flex';
            });

            switchToHeaderBtn.addEventListener('click', function () {
                gwOpenPdfEditor('header');
                switchToHeaderBtn.style.display = 'none';
                switchToFooterBtn.style.display = 'inline-flex';
            });
        }

        // Synchronisation des color pickers avec les champs texte
        var colorInputs = document.querySelectorAll('input[type="color"]');
        colorInputs.forEach(function (colorInput) {
            var textInput = document.querySelector('input[data-color-target="' + colorInput.id + '"]');
            if (textInput) {
                // Quand le color picker change, mettre à jour le champ texte
                colorInput.addEventListener('input', function () {
                    textInput.value = colorInput.value;
                });

                // Quand le champ texte change, mettre à jour le color picker
                textInput.addEventListener('input', function () {
                    var val = textInput.value.trim();
                    // Valider le format hex
                    if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
                        colorInput.value = val;
                    }
                });

                // Au blur, corriger le format si nécessaire
                textInput.addEventListener('blur', function () {
                    var val = textInput.value.trim();
                    if (!/^#[0-9A-Fa-f]{6}$/.test(val)) {
                        textInput.value = colorInput.value;
                    }
                });
            }
        });

        // Synchronisation des color pickers pour les fonds (supportent "transparent")
        var bgColorPickers = document.querySelectorAll('input[data-bg-target]');
        bgColorPickers.forEach(function (picker) {
            var targetId = picker.getAttribute('data-bg-target');
            var textInput = document.getElementById(targetId);
            if (textInput) {
                // Quand le color picker change, mettre à jour le champ texte
                picker.addEventListener('input', function () {
                    textInput.value = picker.value;
                });

                // Quand le champ texte change, mettre à jour le color picker si c'est un hex valide
                textInput.addEventListener('input', function () {
                    var val = textInput.value.trim();
                    if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
                        picker.value = val;
                    }
                    // Si "transparent" ou vide, on laisse tel quel
                });
            }
        });

        // Icônes d'aperçu PDF par modèle dans la liste "Modèles existants"
        var pdfPreviewButtons = document.querySelectorAll('.gw-pdf-preview-template');
        if (pdfPreviewButtons && pdfPreviewButtons.length) {
            pdfPreviewButtons.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var templateId = btn.getAttribute('data-template-id') || '0';
                    if (templateId === '0' || templateId === '') {
                        return;
                    }

                    var previewUrl = '<?php echo esc_url(home_url('/gestiwork/pdf-preview/')); ?>?template_id=' + templateId;
                    window.open(previewUrl, '_blank', 'width=900,height=700,scrollbars=yes,resizable=yes');
                });
            });
        }

        // Validation du formulaire d'identité au moment de la soumission
        var identityModal = document.getElementById('gw-modal-general');
        if (identityModal) {
            var identityForm = identityModal.querySelector('form');
            var identityError = document.getElementById('gw_identity_error');
            if (identityForm && identityError) {
                identityForm.addEventListener('submit', function (e) {
                    // Champs strictement obligatoires (non vides)
                    var requiredFieldIds = [
                        'gw_raison_sociale',
                        'gw_email_contact',
                        'gw_adresse',
                        'gw_code_postal',
                        'gw_ville',
                        'gw_siret',
                        'gw_code_ape',
                        'gw_nda',
                        'gw_format_numero_devis'
                    ];

                    var requiredFields = requiredFieldIds
                        .map(function (id) { return document.getElementById(id); })
                        .filter(function (el) { return !!el; });

                    var missingRequired = requiredFields.some(function (field) {
                        return field.value.trim() === '';
                    });

                    var telFixeField = document.getElementById('gw_telephone_fixe');
                    var telPortableField = document.getElementById('gw_telephone_portable');
                    var hasAtLeastOnePhone = false;
                    if (telFixeField && telFixeField.value.trim() !== '') {
                        hasAtLeastOnePhone = true;
                    }
                    if (telPortableField && telPortableField.value.trim() !== '') {
                        hasAtLeastOnePhone = true;
                    }

                    if (missingRequired || !hasAtLeastOnePhone) {
                        e.preventDefault();

                        if (missingRequired && !hasAtLeastOnePhone) {
                            identityError.textContent = 'Merci de renseigner tous les champs obligatoires marqués d’une astérisque rouge (*) et au moins un numéro de téléphone (fixe ou portable).';
                        } else if (missingRequired) {
                            identityError.textContent = 'Merci de renseigner tous les champs obligatoires marqués d’une astérisque rouge (*).';
                        } else {
                            identityError.textContent = 'Merci de renseigner au moins un numéro de téléphone (fixe ou portable).';
                        }

                        identityError.style.display = '';
                        identityError.focus && identityError.focus();
                    } else {
                        // Tout est OK, on masque le message d'erreur éventuel et on laisse le formulaire se soumettre
                        identityError.textContent = '';
                        identityError.style.display = 'none';
                    }
                });
            }
        }

        // Ouverture de la section d'édition de la description (présentation OF)
        var openDescBtn = document.getElementById('gw-open-description-editor');
        var descSection = document.getElementById('gw-settings-group-description-editor');
        if (openDescBtn && descSection) {
            openDescBtn.addEventListener('click', function () {
                descSection.style.display = '';
                if (typeof descSection.scrollIntoView === 'function') {
                    descSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        }

        // Sélection du logo GestiWork via la médiathèque WordPress
        var logoSelectButton = document.getElementById('gw-logo-select-button');
        var logoIdInput = document.getElementById('gw_logo_id');
        var logoPreview = document.getElementById('gw-logo-preview');
        if (logoSelectButton && logoIdInput && logoPreview) {
            var gwLogoFrame = null;

            logoSelectButton.addEventListener('click', function (e) {
                e.preventDefault();

                // Vérifier la disponibilité de wp.media au moment du clic
                if (typeof wp === 'undefined' || !wp.media || typeof wp.media !== 'function') {
                    return;
                }

                if (gwLogoFrame) {
                    gwLogoFrame.open();
                    return;
                }

                gwLogoFrame = wp.media({
                    title: 'Choisir un logo GestiWork',
                    button: { text: 'Utiliser ce logo' },
                    multiple: false
                });

                gwLogoFrame.on('select', function () {
                    var attachment = gwLogoFrame.state().get('selection').first();
                    if (!attachment) {
                        return;
                    }

                    var data = attachment.toJSON();
                    logoIdInput.value = data.id || 0;

                    if (data.url) {
                        logoPreview.src = data.url;
                        logoPreview.style.display = 'inline-block';
                    }

                    // Soumettre automatiquement le formulaire pour enregistrer le logo
                    var form = logoSelectButton.closest('form');
                    if (form && typeof form.submit === 'function') {
                        form.submit();
                    }
                });

                gwLogoFrame.open();
            });
        }
    })();
</script>
