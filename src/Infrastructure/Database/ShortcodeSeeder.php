<?php
/**
 * GestiWork ERP - PDF Shortcode Seeder
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

namespace GestiWork\Infrastructure\Database;

use GestiWork\Domain\Pdf\PdfShortcodeCatalog;

/**
 * Seeder pour alimenter la table gw_pdf_shortcodes
 * à partir du catalogue de référence PdfShortcodeCatalog.
 */
final class ShortcodeSeeder
{
    /**
     * Synchronise la table gw_pdf_shortcodes avec le catalogue PHP.
     *
     * - Insère les nouveaux shortcodes
     * - Met à jour les shortcodes existants (label, group_key, description)
     * - Ne supprime pas les shortcodes absents du catalogue (pour permettre des ajouts manuels)
     *
     * @return array{inserted: int, updated: int, errors: int}
     */
    public static function seed(): array
    {
        global $wpdb;

        $tableName = $wpdb->prefix . 'gw_pdf_shortcodes';
        $result = [
            'inserted' => 0,
            'updated'  => 0,
            'errors'   => 0,
        ];

        // Vérifier que la table existe
        $tableExists = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $tableName)
        );

        if ($tableExists !== $tableName) {
            return $result;
        }

        $catalog = PdfShortcodeCatalog::getAll();

        foreach ($catalog as $shortcode) {
            $code = $shortcode['code'];
            $label = $shortcode['label'];
            $groupKey = $shortcode['group_key'];
            $description = $shortcode['description'];

            // Vérifier si le shortcode existe déjà
            $existingId = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT id FROM {$tableName} WHERE code = %s LIMIT 1",
                    $code
                )
            );

            if ($existingId) {
                // Mise à jour
                $updated = $wpdb->update(
                    $tableName,
                    [
                        'label'       => $label,
                        'group_key'   => $groupKey,
                        'description' => $description,
                    ],
                    ['id' => (int) $existingId],
                    ['%s', '%s', '%s'],
                    ['%d']
                );

                if ($updated !== false) {
                    $result['updated']++;
                } else {
                    $result['errors']++;
                }
            } else {
                // Insertion
                $inserted = $wpdb->insert(
                    $tableName,
                    [
                        'code'        => $code,
                        'label'       => $label,
                        'group_key'   => $groupKey,
                        'description' => $description,
                        'is_active'   => 1,
                    ],
                    ['%s', '%s', '%s', '%s', '%d']
                );

                if ($inserted !== false) {
                    $result['inserted']++;
                } else {
                    $result['errors']++;
                }
            }
        }

        return $result;
    }

    /**
     * Récupère tous les shortcodes actifs depuis la base, groupés par group_key.
     *
     * @return array<string, array<int, array{id: int, code: string, label: string, group_key: string, description: string}>>
     */
    public static function getActiveGrouped(): array
    {
        global $wpdb;

        $tableName = $wpdb->prefix . 'gw_pdf_shortcodes';
        $grouped = [];

        // Vérifier que la table existe
        $tableExists = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $tableName)
        );

        if ($tableExists !== $tableName) {
            // Fallback sur le catalogue PHP si la table n'existe pas
            return PdfShortcodeCatalog::getGrouped();
        }

        $rows = $wpdb->get_results(
            "SELECT id, code, label, group_key, description 
             FROM {$tableName} 
             WHERE is_active = 1 
             ORDER BY group_key ASC, label ASC",
            ARRAY_A
        );

        if (empty($rows)) {
            // Fallback sur le catalogue PHP si la table est vide
            return PdfShortcodeCatalog::getGrouped();
        }

        foreach ($rows as $row) {
            $key = $row['group_key'];
            if (!isset($grouped[$key])) {
                $grouped[$key] = [];
            }
            $grouped[$key][] = $row;
        }

        return $grouped;
    }

    /**
     * Retourne les libellés des groupes.
     *
     * @return array<string, string>
     */
    public static function getGroupLabels(): array
    {
        return PdfShortcodeCatalog::getGroups();
    }
}
