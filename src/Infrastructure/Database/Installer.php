<?php

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

        $charsetCollate = $wpdb->get_charset_collate();
        $tableIdentity  = $wpdb->prefix . 'gw_of_identity';
        $tableOptions   = $wpdb->prefix . 'gw_options';

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
    }
}
