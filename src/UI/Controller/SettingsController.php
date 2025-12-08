<?php

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
        if ($action !== 'save_of_identity' && $action !== 'save_of_description' && $action !== 'save_of_logo') {
            return;
        }

        if (!is_user_logged_in() || !current_user_can('manage_options')) {
            return;
        }

        global $wpdb;

        if (!($wpdb instanceof wpdb)) {
            return;
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

            $redirectUrl = add_query_arg(
                [
                    'gw_view'    => 'settings',
                    'tab'        => 'general',
                    'gw_updated' => '1',
                ],
                home_url('/gestiwork/')
            );

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
                    'gw_view'    => 'settings',
                    'tab'        => 'general',
                    'gw_updated' => '1',
                ],
                home_url('/gestiwork/')
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
        ];

        $existingId = $wpdb->get_var("SELECT id FROM {$tableName} LIMIT 1");

        if ($existingId) {
            $wpdb->update($tableName, $data, ['id' => (int) $existingId], $formats, ['%d']);
        } else {
            $wpdb->insert($tableName, $data, $formats);
        }

        $redirectUrl = add_query_arg(
            [
                'gw_view'    => 'settings',
                'tab'        => 'general',
                'gw_updated' => '1',
            ],
            home_url('/gestiwork/')
        );

        wp_safe_redirect($redirectUrl);
        exit;
    }
}
