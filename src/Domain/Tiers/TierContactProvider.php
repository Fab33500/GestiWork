<?php
/**
 * GestiWork ERP - Tier Contact Provider
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

class TierContactProvider
{
    public static function getById(int $contactId): ?array
    {
        global $wpdb;

        if ($contactId <= 0) {
            return null;
        }

        if (!($wpdb instanceof wpdb)) {
            return null;
        }

        $tableName = $wpdb->prefix . 'gw_tier_contacts';

        $tableExists = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $tableName)
        );

        if ($tableExists !== $tableName) {
            return null;
        }

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$tableName} WHERE id = %d LIMIT 1",
                $contactId
            ),
            ARRAY_A
        );

        return is_array($row) ? $row : null;
    }

    public static function getByEmail(string $email, int $excludeContactId = 0): ?array
    {
        global $wpdb;

        $email = trim(strtolower($email));
        if ($email === '' || !is_email($email)) {
            return null;
        }

        if (!($wpdb instanceof wpdb)) {
            return null;
        }

        $tableName = $wpdb->prefix . 'gw_tier_contacts';

        $tableExists = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $tableName)
        );

        if ($tableExists !== $tableName) {
            return null;
        }

        if ($excludeContactId > 0) {
            $row = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM {$tableName} WHERE LOWER(mail) = %s AND id <> %d LIMIT 1",
                    $email,
                    $excludeContactId
                ),
                ARRAY_A
            );
        } else {
            $row = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM {$tableName} WHERE LOWER(mail) = %s LIMIT 1",
                    $email
                ),
                ARRAY_A
            );
        }

        return is_array($row) ? $row : null;
    }

    /**
     * @return array<int,array>
     */
    public static function listByTierId(int $tierId): array
    {
        global $wpdb;

        if ($tierId <= 0) {
            return [];
        }

        if (!($wpdb instanceof wpdb)) {
            return [];
        }

        $tableName = $wpdb->prefix . 'gw_tier_contacts';

        $tableExists = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $tableName)
        );

        if ($tableExists !== $tableName) {
            return [];
        }

        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$tableName} WHERE tier_id = %d ORDER BY id DESC",
                $tierId
            ),
            ARRAY_A
        );

        return is_array($rows) ? $rows : [];
    }

    /**
     * @return int ID du contact créé, 0 sinon
     */
    public static function create(int $tierId, array $data): int
    {
        global $wpdb;

        if ($tierId <= 0) {
            return 0;
        }

        if (!($wpdb instanceof wpdb)) {
            return 0;
        }

        $tableName = $wpdb->prefix . 'gw_tier_contacts';

        $tableExists = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $tableName)
        );

        if ($tableExists !== $tableName) {
            return 0;
        }

        $insertData = self::normalizeContactData($data);
        $insertData['tier_id'] = $tierId;

        $ok = $wpdb->insert($tableName, $insertData);
        if ($ok === false) {
            return 0;
        }

        return (int) $wpdb->insert_id;
    }

    public static function update(int $contactId, array $data): bool
    {
        global $wpdb;

        if ($contactId <= 0) {
            return false;
        }

        if (!($wpdb instanceof wpdb)) {
            return false;
        }

        $tableName = $wpdb->prefix . 'gw_tier_contacts';

        $tableExists = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $tableName)
        );

        if ($tableExists !== $tableName) {
            return false;
        }

        $updateData = self::normalizeContactData($data);

        $ok = $wpdb->update(
            $tableName,
            $updateData,
            ['id' => $contactId],
            null,
            ['%d']
        );

        return $ok !== false;
    }

    public static function delete(int $contactId): bool
    {
        global $wpdb;

        if ($contactId <= 0) {
            return false;
        }

        if (!($wpdb instanceof wpdb)) {
            return false;
        }

        $tableName = $wpdb->prefix . 'gw_tier_contacts';

        $tableExists = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $tableName)
        );

        if ($tableExists !== $tableName) {
            return false;
        }

        $ok = $wpdb->delete(
            $tableName,
            ['id' => $contactId],
            ['%d']
        );

        return $ok !== false && $ok > 0;
    }

    public static function deleteByTierId(int $tierId): bool
    {
        global $wpdb;

        if ($tierId <= 0) {
            return false;
        }

        if (!($wpdb instanceof wpdb)) {
            return false;
        }

        $tableName = $wpdb->prefix . 'gw_tier_contacts';

        $tableExists = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $tableName)
        );

        if ($tableExists !== $tableName) {
            return false;
        }

        $ok = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$tableName} WHERE tier_id = %d",
                $tierId
            )
        );

        return $ok !== false;
    }

    private static function normalizeContactData(array $data): array
    {
        $civilite = isset($data['civilite']) ? (string) $data['civilite'] : 'non_renseigne';

        return [
            'civilite' => $civilite,
            'fonction' => isset($data['fonction']) ? (string) $data['fonction'] : '',
            'nom' => isset($data['nom']) ? (string) $data['nom'] : '',
            'prenom' => isset($data['prenom']) ? (string) $data['prenom'] : '',
            'mail' => isset($data['mail']) ? (string) $data['mail'] : '',
            'tel1' => isset($data['tel1']) ? (string) $data['tel1'] : '',
            'tel2' => isset($data['tel2']) ? (string) $data['tel2'] : '',
            'participe_formation' => isset($data['participe_formation']) && (int) $data['participe_formation'] === 1 ? 1 : 0,
            'apprenant_id' => isset($data['apprenant_id']) && (int) $data['apprenant_id'] > 0 ? (int) $data['apprenant_id'] : null,
        ];
    }
}
