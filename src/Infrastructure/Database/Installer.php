<?php
/**
 * GestiWork ERP - Database Installer
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

namespace GestiWork\Infrastructure\Database;

use wpdb;
use function get_option;
use function update_option;

class Installer
{
    public static function install(): void
    {
        global $wpdb;

        if (!($wpdb instanceof wpdb)) {
            return;
        }

        $charsetCollate    = $wpdb->get_charset_collate();
        $tableIdentity     = $wpdb->prefix . 'gw_of_identity';
        $tableOptions      = $wpdb->prefix . 'gw_options';
        $tableTiers        = $wpdb->prefix . 'gw_tiers';
        $tableTierContacts = $wpdb->prefix . 'gw_tier_contacts';
        $tablePdfTemplates = $wpdb->prefix . 'gw_pdf_templates';
        $tablePdfShortcodes = $wpdb->prefix . 'gw_pdf_shortcodes';

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $sqlIdentity = "CREATE TABLE {$tableIdentity} (
id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
raison_sociale VARCHAR(255) NOT NULL DEFAULT '',
adresse TEXT NOT NULL,
code_postal VARCHAR(20) NOT NULL DEFAULT '',
ville VARCHAR(100) NOT NULL DEFAULT '',
telephone_fixe VARCHAR(50) NOT NULL DEFAULT '',
telephone_portable VARCHAR(50) NOT NULL DEFAULT '',
email_contact VARCHAR(190) NOT NULL DEFAULT '',
site_internet VARCHAR(255) NOT NULL DEFAULT '',
description LONGTEXT NULL,
logo_id BIGINT(20) UNSIGNED NULL,
siret VARCHAR(20) NOT NULL DEFAULT '',
code_ape VARCHAR(10) NOT NULL DEFAULT '',
rcs VARCHAR(190) NOT NULL DEFAULT '',
forme_juridique VARCHAR(190) NOT NULL DEFAULT '',
capital_social VARCHAR(190) NOT NULL DEFAULT '',
nda VARCHAR(50) NOT NULL DEFAULT '',
qualiopi VARCHAR(190) NOT NULL DEFAULT '',
datadock VARCHAR(190) NOT NULL DEFAULT '',
rm VARCHAR(190) NOT NULL DEFAULT '',
tva_intracom VARCHAR(32) NOT NULL DEFAULT '',
regime_tva VARCHAR(100) NOT NULL DEFAULT '',
taux_tva DECIMAL(5,2) NOT NULL DEFAULT 0.00,
banque_principale VARCHAR(190) NOT NULL DEFAULT '',
iban VARCHAR(34) NOT NULL DEFAULT '',
bic VARCHAR(11) NOT NULL DEFAULT '',
format_numero_devis VARCHAR(190) NOT NULL DEFAULT '',
compteur_devis INT(11) UNSIGNED NOT NULL DEFAULT 1,
representant_nom VARCHAR(190) NOT NULL DEFAULT '',
representant_prenom VARCHAR(190) NOT NULL DEFAULT '',
habilitation_inrs VARCHAR(190) NOT NULL DEFAULT '',
created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY  (id)
) {$charsetCollate};";

        dbDelta($sqlIdentity);

        $sqlOptions = "CREATE TABLE {$tableOptions} (
id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
first_year INT(4) UNSIGNED NOT NULL DEFAULT 0,
min_hours_between_signature_emails INT(11) UNSIGNED NOT NULL DEFAULT 0,
max_days_veille_alert INT(11) UNSIGNED NOT NULL DEFAULT 0,
token_validity_hours INT(11) UNSIGNED NOT NULL DEFAULT 0,
min_hourly_rate DECIMAL(10,2) NOT NULL DEFAULT 0.00,
default_deposit_percent DECIMAL(5,2) NOT NULL DEFAULT 0.00,
max_log_rows INT(11) UNSIGNED NOT NULL DEFAULT 1000,
attendance_sheet_lines INT(11) UNSIGNED NOT NULL DEFAULT 25,
enable_client_contract_number TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
enable_document_validity_period TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
enable_trainer_status_activity_code TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
enable_free_text_duration TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
enable_signature_image TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
enable_impersonation_login TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
taxonomy_mode VARCHAR(20) NOT NULL DEFAULT '',
created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY  (id)
) {$charsetCollate};";

        dbDelta($sqlOptions);

        $sqlTiers = "CREATE TABLE {$tableTiers} (
id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
type VARCHAR(30) NOT NULL DEFAULT 'entreprise',
statut VARCHAR(20) NOT NULL DEFAULT 'client',
raison_sociale VARCHAR(255) NOT NULL DEFAULT '',
nom VARCHAR(190) NOT NULL DEFAULT '',
prenom VARCHAR(190) NOT NULL DEFAULT '',
siret VARCHAR(20) NOT NULL DEFAULT '',
forme_juridique VARCHAR(190) NOT NULL DEFAULT '',
email VARCHAR(190) NOT NULL DEFAULT '',
telephone VARCHAR(50) NOT NULL DEFAULT '',
telephone_portable VARCHAR(50) NOT NULL DEFAULT '',
adresse1 VARCHAR(255) NOT NULL DEFAULT '',
adresse2 VARCHAR(255) NOT NULL DEFAULT '',
cp VARCHAR(20) NOT NULL DEFAULT '',
ville VARCHAR(100) NOT NULL DEFAULT '',
deleted_at DATETIME NULL,
created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY  (id),
KEY type (type),
KEY statut (statut),
KEY ville (ville),
KEY deleted_at (deleted_at)
) {$charsetCollate};";

        dbDelta($sqlTiers);

        $sqlTierContacts = "CREATE TABLE {$tableTierContacts} (
id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
tier_id BIGINT(20) UNSIGNED NOT NULL,
civilite VARCHAR(30) NOT NULL DEFAULT 'non_renseigne',
fonction VARCHAR(190) NOT NULL DEFAULT '',
nom VARCHAR(190) NOT NULL DEFAULT '',
prenom VARCHAR(190) NOT NULL DEFAULT '',
mail VARCHAR(190) NOT NULL DEFAULT '',
tel1 VARCHAR(50) NOT NULL DEFAULT '',
tel2 VARCHAR(50) NOT NULL DEFAULT '',
deleted_at DATETIME NULL,
created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY  (id),
KEY tier_id (tier_id),
KEY mail (mail),
KEY deleted_at (deleted_at)
) {$charsetCollate};";

        dbDelta($sqlTierContacts);

        $sqlPdfTemplates = "CREATE TABLE {$tablePdfTemplates} (
id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
name VARCHAR(190) NOT NULL DEFAULT '',
document_type VARCHAR(50) NOT NULL DEFAULT '',
is_default TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
header_html LONGTEXT NULL,
footer_html LONGTEXT NULL,
page_format VARCHAR(20) NOT NULL DEFAULT 'A4',
margin_top DECIMAL(5,2) NOT NULL DEFAULT 0.00,
margin_bottom DECIMAL(5,2) NOT NULL DEFAULT 0.00,
margin_left DECIMAL(5,2) NOT NULL DEFAULT 0.00,
margin_right DECIMAL(5,2) NOT NULL DEFAULT 0.00,
header_height DECIMAL(5,2) NOT NULL DEFAULT 0.00,
footer_height DECIMAL(5,2) NOT NULL DEFAULT 0.00,
font_title VARCHAR(50) NOT NULL DEFAULT 'sans-serif',
font_body VARCHAR(50) NOT NULL DEFAULT 'sans-serif',
font_title_size INT(11) UNSIGNED NOT NULL DEFAULT 14,
font_body_size INT(11) UNSIGNED NOT NULL DEFAULT 11,
color_title VARCHAR(9) NOT NULL DEFAULT '#000000',
color_other_titles VARCHAR(9) NOT NULL DEFAULT '#000000',
header_bg_color VARCHAR(11) NOT NULL DEFAULT 'transparent',
footer_bg_color VARCHAR(11) NOT NULL DEFAULT 'transparent',
custom_css LONGTEXT NULL,
created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY  (id),
KEY document_type (document_type),
KEY is_default (is_default)
) {$charsetCollate};";

        dbDelta($sqlPdfTemplates);

        $sqlPdfShortcodes = "CREATE TABLE {$tablePdfShortcodes} (
id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
code VARCHAR(100) NOT NULL DEFAULT '',
label VARCHAR(190) NOT NULL DEFAULT '',
group_key VARCHAR(50) NOT NULL DEFAULT '',
description TEXT NULL,
is_active TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY  (id),
UNIQUE KEY code (code),
KEY group_key (group_key),
KEY is_active (is_active)
) {$charsetCollate};";

        dbDelta($sqlPdfShortcodes);

        // Alimenter la table des shortcodes avec le catalogue de référence
        ShortcodeSeeder::seed();

        // Migrations pour ajouter les colonnes manquantes
        self::runMigrations($wpdb);
    }

    /**
     * Exécute les migrations pour ajouter les colonnes manquantes.
     */
    private static function runMigrations(\wpdb $wpdb): void
    {
        // === Migrations table des modèles PDF ===
        $tablePdfTemplates = $wpdb->prefix . 'gw_pdf_templates';

        $tableExistsPdf = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $tablePdfTemplates)
        );

        if ($tableExistsPdf === $tablePdfTemplates) {
            $columnsToAddPdf = [
                'font_title_size'  => "INT(11) UNSIGNED NOT NULL DEFAULT 14 AFTER font_body",
                'font_body_size'   => "INT(11) UNSIGNED NOT NULL DEFAULT 11 AFTER font_title_size",
                'header_bg_color'  => "VARCHAR(11) NOT NULL DEFAULT 'transparent' AFTER color_other_titles",
                'footer_bg_color'  => "VARCHAR(11) NOT NULL DEFAULT 'transparent' AFTER header_bg_color",
                'custom_css'       => "LONGTEXT NULL AFTER footer_bg_color",
            ];

            $existingPdfColumns = $wpdb->get_col("SHOW COLUMNS FROM {$tablePdfTemplates}", 0);

            foreach ($columnsToAddPdf as $column => $definition) {
                if (!in_array($column, $existingPdfColumns, true)) {
                    $wpdb->query("ALTER TABLE {$tablePdfTemplates} ADD COLUMN {$column} {$definition}");
                }
            }
        }

        // === Migrations table identité OF ===
        $tableIdentity = $wpdb->prefix . 'gw_of_identity';

        $tableExistsIdentity = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $tableIdentity)
        );

        if ($tableExistsIdentity === $tableIdentity) {
            $columnsToAddIdentity = [
                'representant_nom'    => "VARCHAR(190) NOT NULL DEFAULT '' AFTER compteur_devis",
                'representant_prenom' => "VARCHAR(190) NOT NULL DEFAULT '' AFTER representant_nom",
                'habilitation_inrs'   => "VARCHAR(190) NOT NULL DEFAULT '' AFTER representant_prenom",
                'forme_juridique'     => "VARCHAR(190) NOT NULL DEFAULT '' AFTER rcs",
                'capital_social'      => "VARCHAR(190) NOT NULL DEFAULT '' AFTER forme_juridique",
            ];

            $existingIdentityColumns = $wpdb->get_col("SHOW COLUMNS FROM {$tableIdentity}", 0);

            foreach ($columnsToAddIdentity as $column => $definition) {
                if (!in_array($column, $existingIdentityColumns, true)) {
                    $wpdb->query("ALTER TABLE {$tableIdentity} ADD COLUMN {$column} {$definition}");
                }
            }
        }

        self::migrateLegacyLegalForms($wpdb);
    }

    private static function migrateLegacyLegalForms(\wpdb $wpdb): void
    {
        $optionKey = 'gestiwork_legal_form_labels_migrated';
        if (\get_option($optionKey) === '1') {
            return;
        }

        $mappings = \GestiWork\Domain\Tiers\LegalFormCatalog::labels();

        if (empty($mappings)) {
            return;
        }

        $tables = [
            $wpdb->prefix . 'gw_tiers',
            $wpdb->prefix . 'gw_of_identity',
        ];

        foreach ($tables as $table) {
            $tableExists = $wpdb->get_var(
                $wpdb->prepare('SHOW TABLES LIKE %s', $table)
            );

            if ($tableExists !== $table) {
                continue;
            }

            foreach ($mappings as $code => $label) {
                $wpdb->query(
                    $wpdb->prepare(
                        "UPDATE {$table} SET forme_juridique = %s WHERE forme_juridique = %s",
                        $label,
                        $code
                    )
                );
            }
        }

        \update_option($optionKey, '1');
    }
}
