<?php
/**
 * GestiWork ERP - Tier Provider
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

namespace GestiWork\Domain\Tiers;

use wpdb;

class TierProvider
{
    public static function getById(int $tierId): ?array
    {
        global $wpdb;

        if ($tierId <= 0) {
            return null;
        }

        if (!($wpdb instanceof wpdb)) {
            return null;
        }

        $tableName = $wpdb->prefix . 'gw_tiers';

        $tableExists = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $tableName)
        );

        if ($tableExists !== $tableName) {
            return null;
        }

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$tableName} WHERE id = %d LIMIT 1",
                $tierId
            ),
            ARRAY_A
        );

        if (!is_array($row)) {
            return null;
        }

        return $row;
    }

    public static function getByEmail(string $email): ?array
    {
        global $wpdb;

        $email = trim(strtolower($email));
        if ($email === '' || !is_email($email)) {
            return null;
        }

        if (!($wpdb instanceof wpdb)) {
            return null;
        }

        $tableName = $wpdb->prefix . 'gw_tiers';

        $tableExists = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $tableName)
        );

        if ($tableExists !== $tableName) {
            return null;
        }

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$tableName} WHERE LOWER(email) = %s LIMIT 1",
                $email
            ),
            ARRAY_A
        );

        return is_array($row) ? $row : null;
    }

    public static function getClientParticulierByEmail(string $email): ?array
    {
        global $wpdb;

        $email = trim(strtolower($email));
        if ($email === '' || !is_email($email)) {
            return null;
        }

        if (!($wpdb instanceof wpdb)) {
            return null;
        }

        $tableName = $wpdb->prefix . 'gw_tiers';

        $tableExists = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $tableName)
        );

        if ($tableExists !== $tableName) {
            return null;
        }

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$tableName} WHERE type = %s AND LOWER(email) = %s LIMIT 1",
                'client_particulier',
                $email
            ),
            ARRAY_A
        );

        return is_array($row) ? $row : null;
    }

    public static function listByType(string $type, int $limit = 500): array
    {
        global $wpdb;

        $type = trim($type);
        $limit = max(1, min(5000, $limit));

        if ($type === '') {
            return [];
        }

        if (!($wpdb instanceof wpdb)) {
            return [];
        }

        $tableName = $wpdb->prefix . 'gw_tiers';

        $tableExists = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $tableName)
        );

        if ($tableExists !== $tableName) {
            return [];
        }

        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$tableName} WHERE type = %s ORDER BY raison_sociale ASC, nom ASC, prenom ASC LIMIT %d",
                $type,
                $limit
            ),
            ARRAY_A
        );

        return is_array($rows) ? $rows : [];
    }

    /**
     * Recherche simple (V1) : filtre par query/type/statut/ville + pagination.
     *
     * @return array{items: array<int,array>, total: int, page: int, page_size: int}
     */
    public static function search(array $filters, int $page = 1, int $pageSize = 15): array
    {
        global $wpdb;

        $page = max(1, $page);
        $pageSize = max(1, min(200, $pageSize));

        $defaults = [
            'items' => [],
            'total' => 0,
            'page' => $page,
            'page_size' => $pageSize,
        ];

        if (!($wpdb instanceof wpdb)) {
            return $defaults;
        }

        $tableName = $wpdb->prefix . 'gw_tiers';

        $tableExists = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $tableName)
        );

        if ($tableExists !== $tableName) {
            return $defaults;
        }

        $where = ['1=1'];
        $params = [];

        $query = isset($filters['query']) ? trim((string) $filters['query']) : '';
        if ($query !== '') {
            $like = '%' . $wpdb->esc_like($query) . '%';
            $where[] = '(raison_sociale LIKE %s OR nom LIKE %s OR prenom LIKE %s OR email LIKE %s OR telephone LIKE %s OR telephone_portable LIKE %s)';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $type = isset($filters['type']) ? trim((string) $filters['type']) : '';
        if ($type !== '') {
            $where[] = 'type = %s';
            $params[] = $type;
        }

        $statut = isset($filters['statut']) ? trim((string) $filters['statut']) : '';
        if ($statut !== '') {
            $where[] = 'statut = %s';
            $params[] = $statut;
        }

        $ville = isset($filters['ville']) ? trim((string) $filters['ville']) : '';
        if ($ville !== '') {
            $where[] = 'ville LIKE %s';
            $params[] = '%' . $wpdb->esc_like($ville) . '%';
        }

        $whereSql = 'WHERE ' . implode(' AND ', $where);

        $totalSql = "SELECT COUNT(*) FROM {$tableName} {$whereSql}";
        $total = (int) $wpdb->get_var(
            $params ? $wpdb->prepare($totalSql, $params) : $totalSql
        );

        $offset = ($page - 1) * $pageSize;

        $itemsSql = "SELECT * FROM {$tableName} {$whereSql} ORDER BY id DESC LIMIT %d OFFSET %d";
        $itemsParams = array_merge($params, [$pageSize, $offset]);

        $items = $wpdb->get_results(
            $wpdb->prepare($itemsSql, $itemsParams),
            ARRAY_A
        );

        if (!is_array($items)) {
            $items = [];
        }

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'page_size' => $pageSize,
        ];
    }

    /**
     * Création V1.
     *
     * @return int ID du tiers créé, 0 sinon
     */
    public static function create(array $data): int
    {
        global $wpdb;

        if (!($wpdb instanceof wpdb)) {
            return 0;
        }

        $tableName = $wpdb->prefix . 'gw_tiers';

        $tableExists = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $tableName)
        );

        if ($tableExists !== $tableName) {
            return 0;
        }

        $insertData = self::normalizeTierData($data);

        $ok = $wpdb->insert($tableName, $insertData);
        if ($ok === false) {
            return 0;
        }

        return (int) $wpdb->insert_id;
    }

    public static function update(int $tierId, array $data): bool
    {
        global $wpdb;

        if ($tierId <= 0) {
            return false;
        }

        if (!($wpdb instanceof wpdb)) {
            return false;
        }

        $tableName = $wpdb->prefix . 'gw_tiers';

        $tableExists = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $tableName)
        );

        if ($tableExists !== $tableName) {
            return false;
        }

        $updateData = self::normalizeTierData($data);

        $ok = $wpdb->update(
            $tableName,
            $updateData,
            ['id' => $tierId],
            null,
            ['%d']
        );

        return $ok !== false;
    }

    public static function delete(int $tierId): bool
    {
        global $wpdb;

        if ($tierId <= 0) {
            return false;
        }

        if (!($wpdb instanceof wpdb)) {
            return false;
        }

        $tableName = $wpdb->prefix . 'gw_tiers';

        $tableExists = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $tableName)
        );

        if ($tableExists !== $tableName) {
            return false;
        }

        $ok = $wpdb->delete(
            $tableName,
            ['id' => $tierId],
            ['%d']
        );

        return $ok !== false && $ok > 0;
    }

    private static function normalizeTierData(array $data): array
    {
        $type = isset($data['type']) ? (string) $data['type'] : 'entreprise';
        $statut = isset($data['statut']) ? (string) $data['statut'] : 'client';

        $normalized = [
            'type' => $type,
            'statut' => $statut,
            'raison_sociale' => isset($data['raison_sociale']) ? (string) $data['raison_sociale'] : '',
            'nom' => isset($data['nom']) ? (string) $data['nom'] : '',
            'prenom' => isset($data['prenom']) ? (string) $data['prenom'] : '',
            'siret' => isset($data['siret']) ? (string) $data['siret'] : '',
            'forme_juridique' => isset($data['forme_juridique']) ? (string) $data['forme_juridique'] : '',
            'email' => isset($data['email']) ? (string) $data['email'] : '',
            'telephone' => isset($data['telephone']) ? (string) $data['telephone'] : '',
            'telephone_portable' => isset($data['telephone_portable']) ? (string) $data['telephone_portable'] : '',
            'adresse1' => isset($data['adresse1']) ? (string) $data['adresse1'] : '',
            'adresse2' => isset($data['adresse2']) ? (string) $data['adresse2'] : '',
            'cp' => isset($data['cp']) ? (string) $data['cp'] : '',
            'ville' => isset($data['ville']) ? (string) $data['ville'] : '',
        ];

        return $normalized;
    }
}
