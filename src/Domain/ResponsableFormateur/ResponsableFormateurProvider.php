<?php
/**
 * GestiWork ERP - ResponsableFormateur Provider
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

namespace GestiWork\Domain\ResponsableFormateur;

use wpdb;

class ResponsableFormateurProvider
{
    /**
     * Crée un nouveau responsable/formateur.
     */
    public static function create(array $data): int
    {
        global $wpdb;

        $table = $wpdb->prefix . 'gw_responsables_formateurs';
        
        $defaults = [
            'civilite' => '',
            'prenom' => '',
            'nom' => '',
            'fonction' => '',
            'email' => '',
            'telephone' => '',
            'role_type' => '',
            'sous_traitant' => 'Non',
            'nda_sous_traitant' => '',
            'adresse_postale' => '',
            'rue' => '',
            'code_postal' => '',
            'ville' => '',
        ];

        $responsableData = array_intersect_key($data, $defaults);
        $responsableData = array_merge($defaults, $responsableData);

        $result = $wpdb->insert(
            $table,
            $responsableData,
            [
                '%s', // civilite
                '%s', // prenom
                '%s', // nom
                '%s', // fonction
                '%s', // email
                '%s', // telephone
                '%s', // role_type
                '%s', // sous_traitant
                '%s', // nda_sous_traitant
                '%s', // adresse_postale
                '%s', // rue
                '%s', // code_postal
                '%s', // ville
            ]
        );

        return $result !== false ? (int) $wpdb->insert_id : 0;
    }

    /**
     * Récupère un responsable/formateur par ID.
     */
    public static function getById(int $id): ?array
    {
        global $wpdb;

        $table = $wpdb->prefix . 'gw_responsables_formateurs';
        
        $result = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        return $result ?: null;
    }

    /**
     * Met à jour un responsable/formateur.
     */
    public static function update(int $id, array $data): bool
    {
        global $wpdb;

        $table = $wpdb->prefix . 'gw_responsables_formateurs';

        $allowedFields = [
            'civilite', 'prenom', 'nom', 'fonction', 'email', 'telephone',
            'role_type', 'sous_traitant', 'nda_sous_traitant', 'adresse_postale',
            'rue', 'code_postal', 'ville'
        ];

        $updateData = array_intersect_key($data, array_flip($allowedFields));
        
        if (empty($updateData)) {
            return false;
        }

        $result = $wpdb->update(
            $table,
            $updateData,
            ['id' => $id],
            null, // WordPress détermine automatiquement les types
            ['%d']
        );

        return $result !== false;
    }

    /**
     * Supprime un responsable/formateur.
     */
    public static function delete(int $id): bool
    {
        global $wpdb;

        $table = $wpdb->prefix . 'gw_responsables_formateurs';
        
        $result = $wpdb->delete(
            $table,
            ['id' => $id],
            ['%d']
        );

        return $result !== false;
    }

    /**
     * Récupère tous les responsables/formateurs (avec pagination optionnelle).
     */
    public static function getAll(int $limit = 0, int $offset = 0): array
    {
        global $wpdb;

        $table = $wpdb->prefix . 'gw_responsables_formateurs';
        
        $sql = "SELECT * FROM {$table} ORDER BY nom, prenom";
        
        if ($limit > 0) {
            $sql .= $wpdb->prepare(" LIMIT %d OFFSET %d", $limit, $offset);
        }

        return $wpdb->get_results($sql, ARRAY_A) ?: [];
    }
}
