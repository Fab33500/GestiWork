<?php
/**
 * GestiWork ERP - FormateurCompetence Provider
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

class FormateurCompetenceProvider
{
    /**
     * Sauvegarde les compétences d'un formateur (remplace toutes les compétences existantes).
     */
    public static function saveCompetences(int $formateurId, array $competences): bool
    {
        global $wpdb;

        $table = $wpdb->prefix . 'gw_formateur_competences';

        $existingCouts = self::getCoutsByFormateurId($formateurId);

        // Supprimer les compétences existantes
        $wpdb->delete(
            $table,
            ['formateur_id' => $formateurId],
            ['%d']
        );

        // Insérer les nouvelles compétences
        foreach ($competences as $competence) {
            $competence = trim($competence);
            if ($competence === '') {
                continue;
            }

            $wpdb->insert(
                $table,
                [
                    'formateur_id' => $formateurId,
                    'competence' => $competence,
                ],
                ['%d', '%s']
            );
        }

        if (is_array($existingCouts)) {
            self::saveCouts($formateurId, $existingCouts);
        }

        return true;
    }

    /**
     * Sauvegarde les coûts d'un formateur.
     */
    public static function saveCouts(int $formateurId, array $couts): bool
    {
        global $wpdb;

        $table = $wpdb->prefix . 'gw_formateur_competences';

        // Mettre à jour tous les enregistrements de ce formateur avec les nouveaux coûts
        $result = $wpdb->update(
            $table,
            [
                'cout_jour_ht' => $couts['cout_jour_ht'] ?? null,
                'cout_heure_ht' => $couts['cout_heure_ht'] ?? null,
                'heures_par_jour' => $couts['heures_par_jour'] ?? 7.00,
                'tva_rate' => $couts['tva_rate'] ?? 0.00,
            ],
            ['formateur_id' => $formateurId],
            ['%f', '%f', '%f', '%f'],
            ['%d']
        );

        if ($result === false) {
            return false;
        }

        if ($result === 0) {
            $inserted = $wpdb->insert(
                $table,
                [
                    'formateur_id' => $formateurId,
                    'competence' => '',
                    'cout_jour_ht' => $couts['cout_jour_ht'] ?? null,
                    'cout_heure_ht' => $couts['cout_heure_ht'] ?? null,
                    'heures_par_jour' => $couts['heures_par_jour'] ?? 7.00,
                    'tva_rate' => $couts['tva_rate'] ?? 0.00,
                ],
                ['%d', '%s', '%f', '%f', '%f', '%f']
            );

            return $inserted !== false;
        }

        return true;
    }

    /**
     * Récupère les compétences d'un formateur.
     */
    public static function getCompetencesByFormateurId(int $formateurId): array
    {
        global $wpdb;

        $table = $wpdb->prefix . 'gw_formateur_competences';

        $results = $wpdb->get_results(
            $wpdb->prepare("SELECT competence FROM {$table} WHERE formateur_id = %d ORDER BY competence", $formateurId),
            ARRAY_A
        );

        $competences = array_column($results ?: [], 'competence');
        $competences = array_map('strval', $competences);
        $competences = array_map('trim', $competences);

        return array_values(array_filter($competences, static function (string $value): bool {
            return $value !== '';
        }));
    }

    /**
     * Récupère les coûts d'un formateur.
     */
    public static function getCoutsByFormateurId(int $formateurId): ?array
    {
        global $wpdb;

        $table = $wpdb->prefix . 'gw_formateur_competences';

        $result = $wpdb->get_row(
            $wpdb->prepare("SELECT cout_jour_ht, cout_heure_ht, heures_par_jour, tva_rate FROM {$table} WHERE formateur_id = %d LIMIT 1", $formateurId),
            ARRAY_A
        );

        return $result ?: null;
    }

    /**
     * Supprime toutes les compétences et coûts d'un formateur.
     */
    public static function deleteByFormateurId(int $formateurId): bool
    {
        global $wpdb;

        $table = $wpdb->prefix . 'gw_formateur_competences';

        $result = $wpdb->delete(
            $table,
            ['formateur_id' => $formateurId],
            ['%d']
        );

        return $result !== false;
    }
}
