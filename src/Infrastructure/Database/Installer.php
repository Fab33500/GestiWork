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
        $tableName      = $wpdb->prefix . 'gw_of_identity';

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $sql = "CREATE TABLE {$tableName} (
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

        dbDelta($sql);
    }
}
