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

        $mappings = [
            '0000' => 'Non déterminé',
            '1000' => 'Entrepreneur individuel',
            '1100' => 'Services déconcentrés de l’État',
            '1200' => 'Collectivités territoriales',
            '1300' => 'Établissements publics administratifs',
            '1400' => 'Organismes de sécurité sociale',
            '1500' => 'Autres personnes morales de droit public',
            '1900' => 'Administration publique',
            '2110' => 'Société civile immobilière',
            '2210' => 'Société civile professionnelle',
            '2220' => 'Société civile de moyens',
            '2230' => 'Société civile d’attribution',
            '2240' => 'Groupement foncier agricole',
            '2250' => 'Société civile agricole',
            '2290' => 'Autre société civile',
            '2385' => 'Société coopérative artisanale',
            '2395' => 'Société coopérative d\'intérêt collectif',
            '2600' => 'Profession libérale',
            '2700' => 'Syndic de copropriété',
            '2800' => 'Autres groupements de droit privé',
            '3110' => 'SA à conseil d’administration',
            '3120' => 'SA à directoire',
            '3150' => 'Société en commandite par actions',
            '3205' => 'SAS, société par actions simplifiée',
            '3220' => 'SAS à associé unique ou Sasu',
            '3290' => 'Autres sociétés par actions',
            '3410' => 'SARL, société à responsabilité limitée',
            '3420' => 'SARL à associé unique ou EURL',
            '3490' => 'Autres SARL',
            '4110' => 'Entrepreneur individuel',
            '4120' => 'Micro-entrepreneur (EI)',
            '4130' => 'Exploitant agricole',
            '4140' => 'Profession libérale',
            '4150' => 'Artisan',
            '4160' => 'Commerçant',
            '4170' => 'Officier public ou ministériel',
            '4180' => 'Autre entrepreneur individuel',
            '5191' => 'Société en nom collectif',
            '5192' => 'Société en commandite simple',
            '5193' => 'Société en participation',
            '5194' => 'Société créée de fait',
            '5195' => 'Association en participation',
            '5196' => 'SARL coopérative',
            '5305' => 'Coopérative agricole',
            '5307' => 'Société d’intérêt collectif agricole (Sica)',
            '5310' => 'Union de coopératives agricoles',
            '5320' => 'Autres sociétés coopératives agricoles',
            '5370' => 'Société d’assurance mutuelle',
            '5385' => 'Société mutualiste',
            '5410' => 'Société coopérative artisanale',
            '5420' => 'Société coopérative d’intérêt collectif',
            '5430' => 'Coopérative maritime',
            '5440' => 'Coopérative de transport',
            '5450' => 'Société coopérative de construction',
            '5460' => 'Autres sociétés coopératives',
            '5499' => 'Société à responsabilité limitée (SARL)',
            '5505' => 'Société coopérative d’HLM',
            '5510' => 'Société d’économie mixte',
            '5520' => 'Société publique locale',
            '5550' => 'Société d\'aménagement foncier et d\'établissement rural',
            '5599' => 'Autres sociétés coopératives',
            '5605' => 'Société à mission',
            '5710' => 'Société par actions simplifiée (SAS)',
            '5720' => 'Société par actions simplifiée unipersonnelle (SASU)',
            '5785' => 'Société anonyme (SA)',
            '5800' => 'Autres formes de société commerciale',
            '6310' => 'Association déclarée',
            '6311' => 'Association déclarée d’utilité publique',
            '6312' => 'Association non déclarée',
            '6313' => 'Association de droit local',
            '6320' => 'Congrégation',
            '6330' => 'Fondation',
            '6340' => 'Syndicat de copropriétaires',
            '6400' => 'Autres personnes morales de droit privé',
            '6540' => 'Autres groupements de droit privé',
            '7112' => 'Organisme professionnel',
            '7120' => 'Organisation patronale',
            '7210' => 'Syndicat de salariés',
            '7220' => 'Syndicat mixte',
            '7312' => 'Association syndicale autorisée',
            '7313' => 'Association syndicale libre',
            '7314' => 'Association foncière urbaine',
            '7320' => 'Comité d’entreprise ou similaire',
            '7380' => 'Autres collectivités ou regroupements',
            '8110' => 'Établissement public industriel ou commercial',
            '8120' => 'Établissement public local',
            '8130' => 'Établissement public de santé',
            '8140' => 'Autres établissements publics',
            '8210' => 'Groupement d’intérêt public',
            '8310' => 'Autre organisme public à caractère industriel ou commercial',
            '9220' => 'Syndicat de salariés',
            '9999' => 'Autre catégorie juridique',
        ];

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
