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

if (function_exists('wp_enqueue_media')) {
    wp_enqueue_media();
}

$ofIdentity = SettingsProvider::getOfIdentity();
$gwLogoUrl = '';

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
            <div class="gw-settings-group">
                <h4 class="gw-section-subtitle"><?php esc_html_e('2.1 Pages & URLs de gestion', 'gestiwork'); ?></h4>
                <div class="gw-settings-grid">
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Première année d’activité gérée', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">2024</p>
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
                        <p class="gw-settings-placeholder"><?php esc_html_e('Activer un champ dédié au numéro de contrat client sur les documents.', 'gestiwork'); ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Période de validité des documents', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php esc_html_e('Permettre de définir une date de validité pour les devis à signer.', 'gestiwork'); ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Marque des formateurs', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php esc_html_e('Les formateurs peuvent mettre en avant leur propre marque sur certains documents.', 'gestiwork'); ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Statut et code d’activité des formateurs', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php esc_html_e('Enregistrer le statut (salarié, indépendant, etc.) et un identifiant ou code d’activité.', 'gestiwork'); ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Genre des stagiaires', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php esc_html_e('Activer le champ de genre pour les besoins BPF ou statistiques.', 'gestiwork'); ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Durée textuelle des actions', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php esc_html_e('Permettre des durées libres comme "soit deux demi-journées" dans les documents.', 'gestiwork'); ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Image de signature', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php esc_html_e('Autoriser l’intégration d’une image de signature du responsable (non valide juridiquement).', 'gestiwork'); ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Connexion en tant que…', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php esc_html_e('Autoriser les responsables à se connecter à la place d’un autre utilisateur.', 'gestiwork'); ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Mode d’identification', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php esc_html_e('Gestion locale des comptes ou mode délégué (SSO, etc.), à définir.', 'gestiwork'); ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Droits des formateurs', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php esc_html_e('Autoriser ou non les formateurs à supprimer des formations ou sessions.', 'gestiwork'); ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Détail du tarif pédagogique', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php esc_html_e('Permettre aux formateurs de détailler animation / préparation par client.', 'gestiwork'); ?></p>
                    </div>
                </div>
            </div>

            <div class="gw-settings-group">
                <h4 class="gw-section-subtitle"><?php esc_html_e('2.3 Délais, seuils et limites', 'gestiwork'); ?></h4>
                <div class="gw-settings-grid">
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Délai minimum entre deux e-mails de demande de signature', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php esc_html_e('Exemple : 4 heures', 'gestiwork'); ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Délai maximum avant alerte sur la veille personnelle', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php esc_html_e('Exemple : 30 jours', 'gestiwork'); ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Durée de validité du jeton de connexion', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php esc_html_e('Exemple : 24 heures (0 pour illimité)', 'gestiwork'); ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Tarif horaire plancher', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php esc_html_e('Exemple : 40 € / heure (déclenche une alerte si inférieur).', 'gestiwork'); ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Pourcentage par défaut pour l’acompte', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php esc_html_e('Exemple : 30 %', 'gestiwork'); ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Nombre maximum de lignes de log chargées', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">1000</p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Nombre de lignes par feuille d’émargement', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder">25</p>
                    </div>
                </div>
            </div>

            <div class="gw-settings-group">
                <h4 class="gw-section-subtitle"><?php esc_html_e('2.4 Taxonomies & bilans', 'gestiwork'); ?></h4>
                <div class="gw-settings-grid">
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Taxonomies pour les formations et sessions', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php esc_html_e('Choix entre catégories (arborescence) et étiquettes (classification transversale).', 'gestiwork'); ?></p>
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
            <div class="gw-settings-group">
                <h4 class="gw-section-subtitle"><?php esc_html_e('3.1 En-tête & pied de page', 'gestiwork'); ?></h4>
                <div class="gw-settings-grid">
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('En-tête commun', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php esc_html_e('Zone éditable (TinyMCE) pour l’en-tête commun à tous les PDF.', 'gestiwork'); ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Pied de page commun', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php esc_html_e('Zone éditable (TinyMCE) pour le pied de page commun à tous les PDF.', 'gestiwork'); ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Logo & coordonnées dans l’en-tête', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php esc_html_e('Sélection du logo et paramétrage des coordonnées visibles sur tous les documents.', 'gestiwork'); ?></p>
                    </div>
                </div>
            </div>

            <div class="gw-settings-group">
                <h4 class="gw-section-subtitle"><?php esc_html_e('3.2 Zone de mise en forme PDF', 'gestiwork'); ?></h4>
                <div class="gw-settings-grid">
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Règles de mise en page communes', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php esc_html_e('Marges, typographie, couleurs de base pour tous les documents générés.', 'gestiwork'); ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Styles de titres, tableaux et listes', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php esc_html_e('Définition des styles pour les titres, sous-titres, tableaux et listes.', 'gestiwork'); ?></p>
                    </div>
                    <div class="gw-settings-field">
                        <p class="gw-settings-label"><?php esc_html_e('Blocs de contenu réutilisables', 'gestiwork'); ?></p>
                        <p class="gw-settings-placeholder"><?php esc_html_e('Préparation de clauses légales et mentions spécifiques OF / Sous-traitant.', 'gestiwork'); ?></p>
                    </div>
                </div>
            </div>
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
                            <label for="gw_email_contact"><?php esc_html_e('E-mail de contact', 'gestiwork'); ?> <span class="gw-required-asterisk" style="color:#d63638;">*</span></label>
                            <input type="email" class="gw-modal-input" id="gw_email_contact" name="gw_email_contact" value="<?php echo isset($ofIdentity['email_contact']) ? esc_attr($ofIdentity['email_contact']) : ''; ?>" required />
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
</section>

<script>
    (function () {
        var tabs = document.querySelectorAll('.gw-settings-tab');
        var panels = document.querySelectorAll('.gw-settings-panel');
        var modalTriggers = document.querySelectorAll('[data-gw-modal-target]');
        var modalCloseButtons = document.querySelectorAll('[data-gw-modal-close]');

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
