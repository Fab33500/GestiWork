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
        $tablePdfTemplates = $wpdb->prefix . 'gw_pdf_templates';

        // Vérifier si la table existe
        $tableExists = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $tablePdfTemplates)
        );

        if ($tableExists !== $tablePdfTemplates) {
            return;
        }

        // Liste des colonnes à ajouter si elles n'existent pas
        $columnsToAdd = [
            'font_title_size'  => "INT(11) UNSIGNED NOT NULL DEFAULT 14 AFTER font_body",
            'font_body_size'   => "INT(11) UNSIGNED NOT NULL DEFAULT 11 AFTER font_title_size",
            'header_bg_color'  => "VARCHAR(11) NOT NULL DEFAULT 'transparent' AFTER color_other_titles",
            'footer_bg_color'  => "VARCHAR(11) NOT NULL DEFAULT 'transparent' AFTER header_bg_color",
        ];

        // Récupérer les colonnes existantes
        $existingColumns = $wpdb->get_col("SHOW COLUMNS FROM {$tablePdfTemplates}", 0);

        foreach ($columnsToAdd as $column => $definition) {
            if (!in_array($column, $existingColumns, true)) {
                $wpdb->query("ALTER TABLE {$tablePdfTemplates} ADD COLUMN {$column} {$definition}");
            }
        }
    }
}
