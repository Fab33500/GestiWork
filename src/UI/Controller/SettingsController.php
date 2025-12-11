<?php
/**
 * GestiWork ERP - Settings Controller
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

namespace GestiWork\UI\Controller;

use GestiWork\Domain\Settings\SettingsProvider;
use wpdb;

class SettingsController
{
    public static function register(): void
    {
        add_action('init', [self::class, 'handlePost']);
    }

    public static function handlePost(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            return;
        }

        $action = isset($_POST['gw_settings_action']) ? (string) $_POST['gw_settings_action'] : '';
        if (
            $action !== 'save_of_identity'
            && $action !== 'save_of_description'
            && $action !== 'save_of_logo'
            && $action !== 'save_options'
            && $action !== 'save_pdf_template'
            && $action !== 'delete_pdf_template'
            && $action !== 'duplicate_pdf_template'
        ) {
            return;
        }

        if (!is_user_logged_in() || !current_user_can('manage_options')) {
            return;
        }

        global $wpdb;

        if (!($wpdb instanceof wpdb)) {
            return;
        }

        // Sauvegarde des options générales (onglet "Options")
        if ($action === 'save_options') {
            $tableOptions = $wpdb->prefix . 'gw_options';

            $tableExistsOptions = $wpdb->get_var(
                $wpdb->prepare('SHOW TABLES LIKE %s', $tableOptions)
            );

            if ($tableExistsOptions !== $tableOptions) {
                return;
            }

            if (!isset($_POST['gw_settings_nonce_options']) || !wp_verify_nonce($_POST['gw_settings_nonce_options'], 'gw_save_options')) {
                return;
            }

            $currentYear = (int) gmdate('Y');

            $dataOptions = [
                'first_year'                          => isset($_POST['gw_first_year']) ? max(0, (int) $_POST['gw_first_year']) : $currentYear,
                'min_hours_between_signature_emails'  => isset($_POST['gw_min_hours_between_signature_emails']) ? max(0, (int) $_POST['gw_min_hours_between_signature_emails']) : 0,
                'max_days_veille_alert'               => isset($_POST['gw_max_days_veille_alert']) ? max(0, (int) $_POST['gw_max_days_veille_alert']) : 0,
                'token_validity_hours'                => isset($_POST['gw_token_validity_hours']) ? max(0, (int) $_POST['gw_token_validity_hours']) : 0,
                'min_hourly_rate'                     => isset($_POST['gw_min_hourly_rate']) ? (float) str_replace(',', '.', (string) wp_unslash($_POST['gw_min_hourly_rate'])) : 0.0,
                'default_deposit_percent'             => isset($_POST['gw_default_deposit_percent']) ? (float) str_replace(',', '.', (string) wp_unslash($_POST['gw_default_deposit_percent'])) : 0.0,
                'max_log_rows'                        => isset($_POST['gw_max_log_rows']) ? max(0, (int) $_POST['gw_max_log_rows']) : 1000,
                'attendance_sheet_lines'              => isset($_POST['gw_attendance_sheet_lines']) ? max(0, (int) $_POST['gw_attendance_sheet_lines']) : 25,
                'enable_client_contract_number'       => isset($_POST['gw_enable_client_contract_number']) ? 1 : 0,
                'enable_document_validity_period'     => isset($_POST['gw_enable_document_validity_period']) ? 1 : 0,
                'enable_trainer_status_activity_code' => isset($_POST['gw_enable_trainer_status_activity_code']) ? 1 : 0,
                'enable_free_text_duration'           => isset($_POST['gw_enable_free_text_duration']) ? 1 : 0,
                'enable_signature_image'              => isset($_POST['gw_enable_signature_image']) ? 1 : 0,
                'enable_impersonation_login'          => isset($_POST['gw_enable_impersonation_login']) ? 1 : 0,
                'taxonomy_mode'                       => isset($_POST['gw_taxonomy_mode']) ? sanitize_text_field(wp_unslash($_POST['gw_taxonomy_mode'])) : 'categories',
            ];

            if ($dataOptions['taxonomy_mode'] !== 'tags') {
                $dataOptions['taxonomy_mode'] = 'categories';
            }

            $formatsOptions = [
                '%d', // first_year
                '%d', // min_hours_between_signature_emails
                '%d', // max_days_veille_alert
                '%d', // token_validity_hours
                '%f', // min_hourly_rate
                '%f', // default_deposit_percent
                '%d', // max_log_rows
                '%d', // attendance_sheet_lines
                '%d', // enable_client_contract_number
                '%d', // enable_document_validity_period
                '%d', // enable_trainer_status_activity_code
                '%d', // enable_free_text_duration
                '%d', // enable_signature_image
                '%d', // enable_impersonation_login
                '%s', // taxonomy_mode
            ];

            $existingOptionsId = $wpdb->get_var("SELECT id FROM {$tableOptions} LIMIT 1");

            if ($existingOptionsId) {
                $wpdb->update($tableOptions, $dataOptions, ['id' => (int) $existingOptionsId], $formatsOptions, ['%d']);
            } else {
                $wpdb->insert($tableOptions, $dataOptions, $formatsOptions);
            }

            $redirectUrl = home_url('/gestiwork/settings/options/');

            wp_safe_redirect($redirectUrl);
            exit;
        }

        // Sauvegarde ou création d'un modèle PDF
        if ($action === 'save_pdf_template') {
            $tablePdfTemplates = $wpdb->prefix . 'gw_pdf_templates';

            $tableExistsPdf = $wpdb->get_var(
                $wpdb->prepare('SHOW TABLES LIKE %s', $tablePdfTemplates)
            );

            if ($tableExistsPdf !== $tablePdfTemplates) {
                wp_safe_redirect(home_url('/gestiwork/settings/pdf/'));
                exit;
            }

            if (!isset($_POST['gw_settings_nonce_pdf']) || !wp_verify_nonce($_POST['gw_settings_nonce_pdf'], 'gw_save_pdf_template')) {
                wp_safe_redirect(home_url('/gestiwork/settings/pdf/'));
                exit;
            }

            $templateId   = isset($_POST['gw_pdf_template_id']) ? (int) $_POST['gw_pdf_template_id'] : 0;
            $templateName = isset($_POST['gw_pdf_model_name']) ? sanitize_text_field(wp_unslash($_POST['gw_pdf_model_name'])) : '';

            if (empty($templateName)) {
                wp_safe_redirect(home_url('/gestiwork/settings/pdf/'));
                exit;
            }

            // Données du modèle (réglages 3.2 + en-tête/pied de page)
            // On prépare toutes les valeurs possibles, puis on filtrera en fonction
            // des colonnes réellement présentes dans la table pour éviter les erreurs SQL.
            $allFields = [
                'name'               => [
                    'value'  => $templateName,
                    'format' => '%s',
                ],
                'document_type'      => [
                    'value'  => isset($_POST['gw_pdf_document_type']) ? sanitize_text_field(wp_unslash($_POST['gw_pdf_document_type'])) : '',
                    'format' => '%s',
                ],
                'is_default'         => [
                    'value'  => isset($_POST['gw_pdf_is_default']) ? 1 : 0,
                    'format' => '%d',
                ],
                'header_html'        => [
                    'value'  => isset($_POST['gw_pdf_header_html']) ? wp_kses_post(wp_unslash($_POST['gw_pdf_header_html'])) : '',
                    'format' => '%s',
                ],
                'footer_html'        => [
                    'value'  => isset($_POST['gw_pdf_footer_html']) ? wp_kses_post(wp_unslash($_POST['gw_pdf_footer_html'])) : '',
                    'format' => '%s',
                ],
                'page_format'        => [
                    'value'  => isset($_POST['gw_pdf_page_format']) ? sanitize_text_field(wp_unslash($_POST['gw_pdf_page_format'])) : 'A4',
                    'format' => '%s',
                ],
                'margin_top'         => [
                    'value'  => isset($_POST['gw_pdf_margin_top']) ? (float) $_POST['gw_pdf_margin_top'] : 5.0,
                    'format' => '%f',
                ],
                'margin_bottom'      => [
                    'value'  => isset($_POST['gw_pdf_margin_bottom']) ? (float) $_POST['gw_pdf_margin_bottom'] : 5.0,
                    'format' => '%f',
                ],
                'margin_left'        => [
                    'value'  => isset($_POST['gw_pdf_margin_left']) ? (float) $_POST['gw_pdf_margin_left'] : 10.0,
                    'format' => '%f',
                ],
                'margin_right'       => [
                    'value'  => isset($_POST['gw_pdf_margin_right']) ? (float) $_POST['gw_pdf_margin_right'] : 10.0,
                    'format' => '%f',
                ],
                'header_height'      => [
                    'value'  => isset($_POST['gw_pdf_header_height']) ? (float) $_POST['gw_pdf_header_height'] : 20.0,
                    'format' => '%f',
                ],
                'footer_height'      => [
                    'value'  => isset($_POST['gw_pdf_footer_height']) ? (float) $_POST['gw_pdf_footer_height'] : 15.0,
                    'format' => '%f',
                ],
                'font_title'         => [
                    'value'  => isset($_POST['gw_pdf_font_title']) ? sanitize_text_field(wp_unslash($_POST['gw_pdf_font_title'])) : 'sans-serif',
                    'format' => '%s',
                ],
                'font_body'          => [
                    'value'  => isset($_POST['gw_pdf_font_body']) ? sanitize_text_field(wp_unslash($_POST['gw_pdf_font_body'])) : 'sans-serif',
                    'format' => '%s',
                ],
                'font_title_size'    => [
                    'value'  => isset($_POST['gw_pdf_font_title_size']) ? (int) $_POST['gw_pdf_font_title_size'] : 14,
                    'format' => '%d',
                ],
                'font_body_size'     => [
                    'value'  => isset($_POST['gw_pdf_font_body_size']) ? (int) $_POST['gw_pdf_font_body_size'] : 11,
                    'format' => '%d',
                ],
                'color_title'        => [
                    'value'  => isset($_POST['gw_pdf_color_title']) ? sanitize_hex_color(wp_unslash($_POST['gw_pdf_color_title'])) : '#000000',
                    'format' => '%s',
                ],
                'color_other_titles' => [
                    'value'  => isset($_POST['gw_pdf_color_other_titles']) ? sanitize_hex_color(wp_unslash($_POST['gw_pdf_color_other_titles'])) : '#000000',
                    'format' => '%s',
                ],
                'header_bg_color'    => [
                    'value'  => isset($_POST['gw_pdf_header_bg_color']) ? sanitize_text_field(wp_unslash($_POST['gw_pdf_header_bg_color'])) : 'transparent',
                    'format' => '%s',
                ],
                'footer_bg_color'    => [
                    'value'  => isset($_POST['gw_pdf_footer_bg_color']) ? sanitize_text_field(wp_unslash($_POST['gw_pdf_footer_bg_color'])) : 'transparent',
                    'format' => '%s',
                ],
                'custom_css'         => [
                    'value'  => isset($_POST['gw_pdf_custom_css']) ? wp_strip_all_tags(wp_unslash($_POST['gw_pdf_custom_css'])) : '',
                    'format' => '%s',
                ],
            ];

            // Normalisation de certaines valeurs (sécurité / valeurs par défaut)
            if ($allFields['color_title']['value'] === '' || $allFields['color_title']['value'] === null) {
                $allFields['color_title']['value'] = '#000000';
            }
            if ($allFields['color_other_titles']['value'] === '' || $allFields['color_other_titles']['value'] === null) {
                $allFields['color_other_titles']['value'] = '#000000';
            }
            if ($allFields['header_bg_color']['value'] === '' || $allFields['header_bg_color']['value'] === null) {
                $allFields['header_bg_color']['value'] = 'transparent';
            }
            if ($allFields['footer_bg_color']['value'] === '' || $allFields['footer_bg_color']['value'] === null) {
                $allFields['footer_bg_color']['value'] = 'transparent';
            }

            // Récupérer les colonnes réellement présentes dans la table
            $existingColumns = $wpdb->get_col("SHOW COLUMNS FROM {$tablePdfTemplates}", 0);

            $dataPdf = [];
            $formatsPdf = [];

            foreach ($allFields as $column => $info) {
                if (in_array($column, $existingColumns, true)) {
                    $dataPdf[$column] = $info['value'];
                    $formatsPdf[] = $info['format'];
                }
            }

            // Si, pour une raison quelconque, aucune colonne n'est reconnue, on annule proprement
            if (empty($dataPdf)) {
                wp_safe_redirect(home_url('/gestiwork/settings/pdf/'));
                exit;
            }

            if ($templateId > 0) {
                // Mise à jour d'un modèle existant
                $wpdb->update($tablePdfTemplates, $dataPdf, ['id' => $templateId], $formatsPdf, ['%d']);
            } else {
                // Création d'un nouveau modèle
                $wpdb->insert($tablePdfTemplates, $dataPdf, $formatsPdf);
            }

            wp_safe_redirect(home_url('/gestiwork/settings/pdf/'));
            exit;
        }

        // Suppression d'un modèle PDF
        if ($action === 'delete_pdf_template') {
            $tablePdfTemplates = $wpdb->prefix . 'gw_pdf_templates';

            $tableExistsPdf = $wpdb->get_var(
                $wpdb->prepare('SHOW TABLES LIKE %s', $tablePdfTemplates)
            );

            if ($tableExistsPdf !== $tablePdfTemplates) {
                wp_safe_redirect(home_url('/gestiwork/settings/pdf/'));
                exit;
            }

            if (!isset($_POST['gw_settings_nonce_pdf']) || !wp_verify_nonce($_POST['gw_settings_nonce_pdf'], 'gw_save_pdf_template')) {
                wp_safe_redirect(home_url('/gestiwork/settings/pdf/'));
                exit;
            }

            $templateId = isset($_POST['gw_pdf_template_id']) ? (int) $_POST['gw_pdf_template_id'] : 0;

            if ($templateId > 0) {
                $wpdb->delete($tablePdfTemplates, ['id' => $templateId], ['%d']);
            }

            wp_safe_redirect(home_url('/gestiwork/settings/pdf/'));
            exit;
        }

        // Duplication d'un modèle PDF
        if ($action === 'duplicate_pdf_template') {
            $tablePdfTemplates = $wpdb->prefix . 'gw_pdf_templates';

            $tableExistsPdf = $wpdb->get_var(
                $wpdb->prepare('SHOW TABLES LIKE %s', $tablePdfTemplates)
            );

            if ($tableExistsPdf !== $tablePdfTemplates) {
                wp_safe_redirect(home_url('/gestiwork/settings/pdf/'));
                exit;
            }

            if (!isset($_POST['gw_settings_nonce_pdf']) || !wp_verify_nonce($_POST['gw_settings_nonce_pdf'], 'gw_save_pdf_template')) {
                wp_safe_redirect(home_url('/gestiwork/settings/pdf/'));
                exit;
            }

            $templateId = isset($_POST['gw_pdf_template_id']) ? (int) $_POST['gw_pdf_template_id'] : 0;
            $overrideName = isset($_POST['gw_pdf_duplicate_name']) ? sanitize_text_field(wp_unslash($_POST['gw_pdf_duplicate_name'])) : '';

            if ($templateId > 0) {
                $original = $wpdb->get_row(
                    $wpdb->prepare("SELECT * FROM {$tablePdfTemplates} WHERE id = %d", $templateId),
                    ARRAY_A
                );

                if (is_array($original) && !empty($original)) {
                    // Préparer les données pour l'insertion (sans l'ID)
                    if (isset($original['id'])) {
                        unset($original['id']);
                    }

                    // Nouveau nom : celui fourni par l'utilisateur, sinon suffixe "(copie)"
                    if (isset($original['name'])) {
                        $baseName = (string) $original['name'];
                        if ($overrideName !== '') {
                            $original['name'] = $overrideName;
                        } else {
                            $original['name'] = $baseName . ' (copie)';
                        }
                    }

                    // On ne duplique pas le statut "par défaut"
                    if (isset($original['is_default'])) {
                        $original['is_default'] = 0;
                    }

                    $wpdb->insert($tablePdfTemplates, $original);
                }
            }

            wp_safe_redirect(home_url('/gestiwork/settings/pdf/'));
            exit;
        }

        $tableName = $wpdb->prefix . 'gw_of_identity';

        // Vérifier que la table existe
        $tableExists = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $tableName)
        );

        if ($tableExists !== $tableName) {
            return;
        }

        // Gestion de la sauvegarde dédiée au logo GestiWork
        if ($action === 'save_of_logo') {
            if (!isset($_POST['gw_settings_nonce_logo']) || !wp_verify_nonce($_POST['gw_settings_nonce_logo'], 'gw_save_of_logo')) {
                return;
            }

            $logoId = isset($_POST['gw_logo_id']) ? (int) $_POST['gw_logo_id'] : 0;
            if ($logoId < 0) {
                $logoId = 0;
            }

            $existingId = $wpdb->get_var("SELECT id FROM {$tableName} LIMIT 1");

            if ($existingId) {
                $wpdb->update(
                    $tableName,
                    ['logo_id' => $logoId],
                    ['id' => (int) $existingId],
                    ['%d'],
                    ['%d']
                );
            } else {
                $wpdb->insert(
                    $tableName,
                    ['logo_id' => $logoId],
                    ['%d']
                );
            }

            $redirectUrl = home_url('/gestiwork/settings/general/');

            wp_safe_redirect($redirectUrl);
            exit;
        }

        // Gestion de la sauvegarde dédiée à la description (présentation OF)
        if ($action === 'save_of_description') {
            if (!isset($_POST['gw_settings_nonce_description']) || !wp_verify_nonce($_POST['gw_settings_nonce_description'], 'gw_save_of_description')) {
                return;
            }

            $description = isset($_POST['gw_description']) ? wp_kses_post(wp_unslash($_POST['gw_description'])) : '';

            $existingId = $wpdb->get_var("SELECT id FROM {$tableName} LIMIT 1");

            if ($existingId) {
                $wpdb->update(
                    $tableName,
                    ['description' => $description],
                    ['id' => (int) $existingId],
                    ['%s'],
                    ['%d']
                );
            } else {
                $wpdb->insert(
                    $tableName,
                    ['description' => $description],
                    ['%s']
                );
            }

            $redirectUrl = add_query_arg(
                [
                    'gw_updated' => '1',
                ],
                home_url('/gestiwork/settings/general/')
            );

            wp_safe_redirect($redirectUrl);
            exit;
        }

        // Sauvegarde générale de l'identité (sans la description, gérée séparément)
        if (!isset($_POST['gw_settings_nonce']) || !wp_verify_nonce($_POST['gw_settings_nonce'], 'gw_save_of_identity')) {
            return;
        }

        $regimeRaw = isset($_POST['gw_regime_tva']) ? sanitize_text_field(wp_unslash($_POST['gw_regime_tva'])) : '';
        $regimeValue = ($regimeRaw === 'assujetti') ? 'assujetti' : 'exonere';

        $data = [
            'raison_sociale'      => isset($_POST['gw_raison_sociale']) ? sanitize_text_field(wp_unslash($_POST['gw_raison_sociale'])) : '',
            'adresse'             => isset($_POST['gw_adresse']) ? sanitize_textarea_field(wp_unslash($_POST['gw_adresse'])) : '',
            'code_postal'         => isset($_POST['gw_code_postal']) ? sanitize_text_field(wp_unslash($_POST['gw_code_postal'])) : '',
            'ville'               => isset($_POST['gw_ville']) ? sanitize_text_field(wp_unslash($_POST['gw_ville'])) : '',
            'telephone_fixe'      => isset($_POST['gw_telephone_fixe']) ? sanitize_text_field(wp_unslash($_POST['gw_telephone_fixe'])) : '',
            'telephone_portable'  => isset($_POST['gw_telephone_portable']) ? sanitize_text_field(wp_unslash($_POST['gw_telephone_portable'])) : '',
            'email_contact'       => isset($_POST['gw_email_contact']) ? sanitize_email(wp_unslash($_POST['gw_email_contact'])) : '',
            'site_internet'       => isset($_POST['gw_site_internet']) ? esc_url_raw(wp_unslash($_POST['gw_site_internet'])) : '',
            'siret'               => isset($_POST['gw_siret']) ? sanitize_text_field(wp_unslash($_POST['gw_siret'])) : '',
            'code_ape'            => isset($_POST['gw_code_ape']) ? sanitize_text_field(wp_unslash($_POST['gw_code_ape'])) : '',
            'rcs'                 => isset($_POST['gw_rcs']) ? sanitize_text_field(wp_unslash($_POST['gw_rcs'])) : '',
            'nda'                 => isset($_POST['gw_nda']) ? sanitize_text_field(wp_unslash($_POST['gw_nda'])) : '',
            'qualiopi'            => isset($_POST['gw_qualiopi']) ? sanitize_text_field(wp_unslash($_POST['gw_qualiopi'])) : '',
            'datadock'            => isset($_POST['gw_datadock']) ? sanitize_text_field(wp_unslash($_POST['gw_datadock'])) : '',
            'rm'                  => isset($_POST['gw_rm']) ? sanitize_text_field(wp_unslash($_POST['gw_rm'])) : '',
            'tva_intracom'        => isset($_POST['gw_tva_intracom']) ? sanitize_text_field(wp_unslash($_POST['gw_tva_intracom'])) : '',
            'regime_tva'          => $regimeValue,
            'taux_tva'            => isset($_POST['gw_taux_tva']) ? (float) str_replace(',', '.', (string) wp_unslash($_POST['gw_taux_tva'])) : 0.0,
            'banque_principale'   => isset($_POST['gw_banque_principale']) ? sanitize_text_field(wp_unslash($_POST['gw_banque_principale'])) : '',
            'iban'                => isset($_POST['gw_iban']) ? sanitize_text_field(wp_unslash($_POST['gw_iban'])) : '',
            'bic'                 => isset($_POST['gw_bic']) ? sanitize_text_field(wp_unslash($_POST['gw_bic'])) : '',
            'format_numero_devis' => isset($_POST['gw_format_numero_devis']) ? sanitize_text_field(wp_unslash($_POST['gw_format_numero_devis'])) : '',
            'compteur_devis'      => isset($_POST['gw_compteur_devis']) ? (int) $_POST['gw_compteur_devis'] : 1,
            'representant_nom'    => isset($_POST['gw_representant_nom']) ? sanitize_text_field(wp_unslash($_POST['gw_representant_nom'])) : '',
            'representant_prenom' => isset($_POST['gw_representant_prenom']) ? sanitize_text_field(wp_unslash($_POST['gw_representant_prenom'])) : '',
            'habilitation_inrs'   => isset($_POST['gw_habilitation_inrs']) ? sanitize_text_field(wp_unslash($_POST['gw_habilitation_inrs'])) : '',
        ];

        $formats = [
            '%s', // raison_sociale
            '%s', // adresse
            '%s', // code_postal
            '%s', // ville
            '%s', // telephone_fixe
            '%s', // telephone_portable
            '%s', // email_contact
            '%s', // site_internet
            '%s', // siret
            '%s', // code_ape
            '%s', // rcs
            '%s', // nda
            '%s', // qualiopi
            '%s', // datadock
            '%s', // rm
            '%s', // tva_intracom
            '%s', // regime_tva
            '%f', // taux_tva
            '%s', // banque_principale
            '%s', // iban
            '%s', // bic
            '%s', // format_numero_devis
            '%d', // compteur_devis
            '%s', // representant_nom
            '%s', // representant_prenom
            '%s', // habilitation_inrs
        ];

        $existingId = $wpdb->get_var("SELECT id FROM {$tableName} LIMIT 1");

        if ($existingId) {
            $wpdb->update($tableName, $data, ['id' => (int) $existingId], $formats, ['%d']);
        } else {
            $wpdb->insert($tableName, $data, $formats);
        }

        $redirectUrl = home_url('/gestiwork/settings/general/');

        wp_safe_redirect($redirectUrl);
        exit;
    }
}
