<?php
/**
 * GestiWork ERP - Settings Provider
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

namespace GestiWork\Domain\Settings;

use wpdb;

class SettingsProvider
{
    /**
     * Retourne les informations d'identité de l'OF.
     *
     * - Si la table GestiWork existe et contient une ligne, on la lit.
     * - Sinon, on renvoie des valeurs par défaut basées sur WordPress + exemples.
     */
    public static function getOfIdentity(): array
    {
        global $wpdb;

        $blogname     = (string) get_option('blogname', '');
        $adminEmail   = (string) get_option('admin_email', '');
        $homeUrl      = (string) home_url('/');
        $customLogoId = (int) get_theme_mod('custom_logo');

        $defaults = [
            'raison_sociale'      => $blogname !== '' ? $blogname : 'PREVENSE FORMATION',
            'adresse'             => '',
            'code_postal'         => '',
            'ville'               => '',
            'telephone_fixe'      => '',
            'telephone_portable'  => '',
            'email_contact'       => $adminEmail !== '' ? $adminEmail : 'contact@exemple-of.fr',
            'site_internet'       => $homeUrl !== '' ? $homeUrl : 'https://www.exemple-of.fr',
            'description'         => '',
            'logo_id'             => $customLogoId > 0 ? $customLogoId : null,
            'siret'               => '',
            'code_ape'            => '',
            'rcs'                 => '',
            'forme_juridique'     => '',
            'capital_social'      => '',
            'nda'                 => '',
            'qualiopi'            => '',
            'datadock'            => '',
            'rm'                  => '',
            'tva_intracom'        => '',
            'regime_tva'          => 'exonere',
            'taux_tva'            => '',
            'banque_principale'   => '',
            'iban'                => '',
            'bic'                 => '',
            'format_numero_devis' => '',
            'compteur_devis'      => 0,
            'representant_nom'    => '',
            'representant_prenom' => '',
            'habilitation_inrs'   => '',
            'representant_legal'  => '',
        ];

        if (!($wpdb instanceof wpdb)) {
            return $defaults;
        }

        $tableName = $wpdb->prefix . 'gw_of_identity';

        // On vérifie d'abord si la table existe pour éviter les erreurs SQL sur une installation incomplète.
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

        // Si aucun logo spécifique n'est enregistré en base, on conserve le logo par défaut du thème.
        if (!isset($row['logo_id']) || (int) $row['logo_id'] <= 0) {
            unset($row['logo_id']);
        }

        return array_merge($defaults, $row);
    }
}
