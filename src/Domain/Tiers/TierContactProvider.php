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
                "SELECT * FROM {$tableName} WHERE tier_id = %d AND deleted_at IS NULL ORDER BY id DESC",
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

    public static function softDelete(int $contactId): bool
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

        $ok = $wpdb->update(
            $tableName,
            ['deleted_at' => current_time('mysql')],
            ['id' => $contactId],
            ['%s'],
            ['%d']
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
        ];
    }
}
