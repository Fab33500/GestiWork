<?php

declare(strict_types=1);

namespace GestiWork\Domain\Settings;

use wpdb;

class OptionsProvider
{
    /**
     * Retourne les options générales de GestiWork.
     *
     * - Si la table existe et contient une ligne, on la lit.
     * - Sinon, on renvoie des valeurs par défaut raisonnables.
     */
    public static function getOptions(): array
    {
        global $wpdb;

        $currentYear = (int) gmdate('Y');

        $defaults = [
            'first_year'                         => $currentYear,
            'min_hours_between_signature_emails' => 4,
            'max_days_veille_alert'              => 30,
            'token_validity_hours'               => 24,
            'min_hourly_rate'                    => 40.0,
            'default_deposit_percent'            => 30.0,
            'max_log_rows'                       => 1000,
            'attendance_sheet_lines'             => 25,
            'enable_client_contract_number'      => 0,
            'enable_document_validity_period'    => 0,
            'enable_trainer_status_activity_code'=> 0,
            'enable_free_text_duration'          => 0,
            'enable_signature_image'             => 0,
            'enable_impersonation_login'         => 0,
            'taxonomy_mode'                      => 'categories',
        ];

        if (!($wpdb instanceof wpdb)) {
            return $defaults;
        }

        $tableName = $wpdb->prefix . 'gw_options';

        $tableExists = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $tableName)
        );

        if ($tableExists !== $tableName) {
            return $defaults;
        }

        $row = $wpdb->get_row("SELECT * FROM {$tableName} LIMIT 1", ARRAY_A);

        if (!is_array($row)) {
            return $defaults;
        }

        // Normalisation de quelques champs numériques si présents
        if (isset($row['first_year'])) {
            $row['first_year'] = (int) $row['first_year'];
        }

        foreach ([
            'min_hours_between_signature_emails',
            'max_days_veille_alert',
            'token_validity_hours',
            'max_log_rows',
            'attendance_sheet_lines',
            'enable_client_contract_number',
            'enable_document_validity_period',
            'enable_trainer_status_activity_code',
            'enable_free_text_duration',
            'enable_signature_image',
            'enable_impersonation_login',
        ] as $intKey) {
            if (isset($row[$intKey])) {
                $row[$intKey] = (int) $row[$intKey];
            }
        }

        foreach ([
            'min_hourly_rate',
            'default_deposit_percent',
        ] as $floatKey) {
            if (isset($row[$floatKey])) {
                $row[$floatKey] = (float) $row[$floatKey];
            }
        }

        return array_merge($defaults, $row);
    }
}
