<?php

declare(strict_types=1);

namespace GestiWork\Domain\Tiers;

use wpdb;

class TierFinanceurProvider
{
    public static function getEntreprisesByFinanceurId(int $financeurId): array
    {
        global $wpdb;

        if ($financeurId <= 0) {
            return [];
        }

        if (!($wpdb instanceof wpdb)) {
            return [];
        }

        $tiersTable = $wpdb->prefix . 'gw_tiers';
        $linksTable = $wpdb->prefix . 'gw_tier_financeurs';

        $tiersTableExists = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $tiersTable)
        );
        $linksTableExists = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $linksTable)
        );

        if ($tiersTableExists !== $tiersTable || $linksTableExists !== $linksTable) {
            return [];
        }

        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT t.* FROM {$tiersTable} t INNER JOIN {$linksTable} l ON l.entreprise_id = t.id WHERE l.financeur_id = %d ORDER BY t.id DESC",
                $financeurId
            ),
            ARRAY_A
        );

        return is_array($rows) ? $rows : [];
    }

    public static function getFinanceursByEntrepriseId(int $entrepriseId): array
    {
        global $wpdb;

        if ($entrepriseId <= 0) {
            return [];
        }

        if (!($wpdb instanceof wpdb)) {
            return [];
        }

        $tiersTable = $wpdb->prefix . 'gw_tiers';
        $linksTable = $wpdb->prefix . 'gw_tier_financeurs';

        $tiersTableExists = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $tiersTable)
        );
        $linksTableExists = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $linksTable)
        );

        if ($tiersTableExists !== $tiersTable || $linksTableExists !== $linksTable) {
            return [];
        }

        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT t.* FROM {$tiersTable} t INNER JOIN {$linksTable} l ON l.financeur_id = t.id WHERE l.entreprise_id = %d ORDER BY t.id DESC",
                $entrepriseId
            ),
            ARRAY_A
        );

        return is_array($rows) ? $rows : [];
    }

    public static function setEntreprisesForFinanceur(int $financeurId, array $entrepriseIds): bool
    {
        global $wpdb;

        if ($financeurId <= 0) {
            return false;
        }

        if (!($wpdb instanceof wpdb)) {
            return false;
        }

        $linksTable = $wpdb->prefix . 'gw_tier_financeurs';

        $linksTableExists = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $linksTable)
        );

        if ($linksTableExists !== $linksTable) {
            return false;
        }

        $normalized = [];
        foreach ($entrepriseIds as $id) {
            $id = (int) $id;
            if ($id > 0 && $id !== $financeurId) {
                $normalized[$id] = $id;
            }
        }

        $deleteOk = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$linksTable} WHERE financeur_id = %d",
                $financeurId
            )
        );

        if ($deleteOk === false) {
            return false;
        }

        foreach ($normalized as $entrepriseId) {
            $ok = $wpdb->insert(
                $linksTable,
                [
                    'financeur_id' => $financeurId,
                    'entreprise_id' => $entrepriseId,
                ]
            );

            if ($ok === false) {
                return false;
            }
        }

        return true;
    }

    public static function setFinanceursForEntreprise(int $entrepriseId, array $financeurIds): bool
    {
        global $wpdb;

        if ($entrepriseId <= 0) {
            return false;
        }

        if (!($wpdb instanceof wpdb)) {
            return false;
        }

        $linksTable = $wpdb->prefix . 'gw_tier_financeurs';

        $linksTableExists = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $linksTable)
        );

        if ($linksTableExists !== $linksTable) {
            return false;
        }

        $normalized = [];
        foreach ($financeurIds as $id) {
            $id = (int) $id;
            if ($id > 0 && $id !== $entrepriseId) {
                $normalized[$id] = $id;
            }
        }

        $deleteOk = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$linksTable} WHERE entreprise_id = %d",
                $entrepriseId
            )
        );

        if ($deleteOk === false) {
            return false;
        }

        foreach ($normalized as $financeurId) {
            $ok = $wpdb->insert(
                $linksTable,
                [
                    'financeur_id' => $financeurId,
                    'entreprise_id' => $entrepriseId,
                ]
            );

            if ($ok === false) {
                return false;
            }
        }

        return true;
    }

    public static function deleteLinksByTierId(int $tierId): bool
    {
        global $wpdb;

        if ($tierId <= 0) {
            return false;
        }

        if (!($wpdb instanceof wpdb)) {
            return false;
        }

        $linksTable = $wpdb->prefix . 'gw_tier_financeurs';

        $linksTableExists = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $linksTable)
        );

        if ($linksTableExists !== $linksTable) {
            return false;
        }

        $ok = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$linksTable} WHERE financeur_id = %d OR entreprise_id = %d",
                $tierId,
                $tierId
            )
        );

        return $ok !== false;
    }
}
