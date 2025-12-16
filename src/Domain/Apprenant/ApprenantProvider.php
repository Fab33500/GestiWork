<?php
/**
 * GestiWork ERP - Apprenant Provider
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

namespace GestiWork\Domain\Apprenant;

use wpdb;

class ApprenantProvider
{
    /**
     * Crée un nouvel apprenant.
     */
    public static function create(array $data): int
    {
        global $wpdb;

        $table = $wpdb->prefix . 'gw_apprenants';
        
        $defaults = [
            'civilite' => '',
            'prenom' => '',
            'nom' => '',
            'nom_naissance' => '',
            'date_naissance' => null,
            'email' => '',
            'telephone' => '',
            'entreprise_id' => null,
            'origine' => '',
            'statut_bpf' => '',
            'adresse1' => '',
            'adresse2' => '',
            'cp' => '',
            'ville' => '',
        ];

        $apprenantData = array_intersect_key($data, $defaults);
        $apprenantData = array_merge($defaults, $apprenantData);

        $result = $wpdb->insert(
            $table,
            $apprenantData,
            [
                '%s', // civilite
                '%s', // prenom
                '%s', // nom
                '%s', // nom_naissance
                '%s', // date_naissance
                '%s', // email
                '%s', // telephone
                '%d', // entreprise_id
                '%s', // origine
                '%s', // statut_bpf
                '%s', // adresse1
                '%s', // adresse2
                '%s', // cp
                '%s', // ville
            ]
        );

        return $result !== false ? (int) $wpdb->insert_id : 0;
    }

    /**
     * Récupère un apprenant par ID.
     */
    public static function getById(int $id): ?array
    {
        global $wpdb;

        $table = $wpdb->prefix . 'gw_apprenants';
        
        $result = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        return $result ?: null;
    }

    /**
     * Met à jour un apprenant.
     */
    public static function update(int $id, array $data): bool
    {
        global $wpdb;

        $table = $wpdb->prefix . 'gw_apprenants';

        $allowedFields = [
            'civilite', 'prenom', 'nom', 'nom_naissance', 'date_naissance',
            'email', 'telephone', 'entreprise_id', 'origine', 'statut_bpf',
            'adresse1', 'adresse2', 'cp', 'ville'
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
     * Supprime un apprenant.
     */
    public static function delete(int $id): bool
    {
        global $wpdb;

        $table = $wpdb->prefix . 'gw_apprenants';
        
        $result = $wpdb->delete(
            $table,
            ['id' => $id],
            ['%d']
        );

        return $result !== false;
    }

    /**
     * Récupère tous les apprenants (avec pagination optionnelle).
     */
    public static function getAll(int $limit = 0, int $offset = 0): array
    {
        global $wpdb;

        $table = $wpdb->prefix . 'gw_apprenants';
        
        $sql = "SELECT * FROM {$table} ORDER BY nom, prenom";
        
        if ($limit > 0) {
            $sql .= $wpdb->prepare(" LIMIT %d OFFSET %d", $limit, $offset);
        }

        return $wpdb->get_results($sql, ARRAY_A) ?: [];
    }

    /**
     * Recherche des apprenants avec filtres.
     *
     * @param array $filters Filtres possibles: query, entreprise, origine
     */
    public static function search(array $filters = []): array
    {
        global $wpdb;

        $table = $wpdb->prefix . 'gw_apprenants';
        $tiersTable = $wpdb->prefix . 'gw_tiers';

        $where = ['1=1'];
        $params = [];

        // Recherche textuelle (nom, prénom, email)
        $query = isset($filters['query']) ? trim((string) $filters['query']) : '';
        if ($query !== '') {
            $like = '%' . $wpdb->esc_like($query) . '%';
            $where[] = '(a.nom LIKE %s OR a.prenom LIKE %s OR a.email LIKE %s)';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        // Filtre par entreprise (recherche dans le nom de l'entreprise liée)
        $entreprise = isset($filters['entreprise']) ? trim((string) $filters['entreprise']) : '';
        if ($entreprise !== '') {
            $like = '%' . $wpdb->esc_like($entreprise) . '%';
            $where[] = '(t.raison_sociale LIKE %s OR t.nom LIKE %s)';
            $params[] = $like;
            $params[] = $like;
        }

        // Filtre par origine
        $origine = isset($filters['origine']) ? trim((string) $filters['origine']) : '';
        if ($origine !== '') {
            $where[] = 'a.origine = %s';
            $params[] = $origine;
        }

        $whereSql = 'WHERE ' . implode(' AND ', $where);

        // Jointure avec la table tiers pour filtrer par entreprise
        $sql = "SELECT a.* FROM {$table} a 
                LEFT JOIN {$tiersTable} t ON a.entreprise_id = t.id 
                {$whereSql} 
                ORDER BY a.nom, a.prenom";

        if (!empty($params)) {
            $sql = $wpdb->prepare($sql, $params);
        }

        return $wpdb->get_results($sql, ARRAY_A) ?: [];
    }
}
