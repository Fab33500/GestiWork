<?php

declare(strict_types=1);

if (!defined('ARRAY_A')) {
    define('ARRAY_A', 'ARRAY_A');
}

if (!function_exists('current_time')) {
    function current_time(string $type = 'mysql'): string
    {
        if ($type === 'mysql') {
            return gmdate('Y-m-d H:i:s');
        }
        return gmdate('c');
    }
}

class wpdb
{
    public string $prefix = 'wp_';

    public int $insert_id = 0;

    private array $tables = [];

    private int $tiersAutoId = 0;

    private int $contactsAutoId = 0;

    public function __construct()
    {
        $this->tables[$this->prefix . 'gw_tiers'] = [];
        $this->tables[$this->prefix . 'gw_tier_contacts'] = [];
    }

    public function prepare(string $query, ...$args): string
    {
        if (!$args) {
            return $query;
        }
        return vsprintf($query, $args);
    }

    public function esc_like(string $text): string
    {
        return $text;
    }

    public function get_var(string $query)
    {
        if (stripos($query, 'SHOW TABLES LIKE') !== false) {
            if (preg_match("~SHOW TABLES LIKE\s+'([^']+)'~i", $query, $m)) {
                $table = $m[1];
                return array_key_exists($table, $this->tables) ? $table : null;
            }
            if (preg_match('~SHOW TABLES LIKE\s+([^\s]+)~i', $query, $m)) {
                $table = trim($m[1], "'\"");
                return array_key_exists($table, $this->tables) ? $table : null;
            }
        }

        if (stripos($query, 'SELECT COUNT(*)') !== false) {
            if (preg_match('~FROM\s+([^\s]+)~i', $query, $m)) {
                $table = $m[1];
                if (!array_key_exists($table, $this->tables)) {
                    return 0;
                }
                return count($this->tables[$table]);
            }
        }

        if (stripos($query, 'SELECT id FROM') !== false) {
            if (preg_match('~FROM\s+([^\s]+)~i', $query, $m)) {
                $table = $m[1];
                if (!array_key_exists($table, $this->tables) || !$this->tables[$table]) {
                    return null;
                }
                $first = reset($this->tables[$table]);
                return $first['id'] ?? null;
            }
        }

        return null;
    }

    public function insert(string $table, array $data)
    {
        if (!array_key_exists($table, $this->tables)) {
            return false;
        }

        if (str_ends_with($table, 'gw_tiers')) {
            $this->tiersAutoId++;
            $data['id'] = $this->tiersAutoId;
            $data['deleted_at'] = $data['deleted_at'] ?? null;
            $this->tables[$table][$this->tiersAutoId] = $data;
            $this->insert_id = $this->tiersAutoId;
            return 1;
        }

        if (str_ends_with($table, 'gw_tier_contacts')) {
            $this->contactsAutoId++;
            $data['id'] = $this->contactsAutoId;
            $data['deleted_at'] = $data['deleted_at'] ?? null;
            $this->tables[$table][$this->contactsAutoId] = $data;
            $this->insert_id = $this->contactsAutoId;
            return 1;
        }

        return false;
    }

    public function update(string $table, array $data, array $where, $format = null, array $whereFormat = [])
    {
        if (!array_key_exists($table, $this->tables)) {
            return false;
        }

        $id = isset($where['id']) ? (int) $where['id'] : 0;
        if ($id <= 0 || !isset($this->tables[$table][$id])) {
            return 0;
        }

        $this->tables[$table][$id] = array_merge($this->tables[$table][$id], $data);
        return 1;
    }

    public function delete(string $table, array $where, array $whereFormat = [])
    {
        if (!array_key_exists($table, $this->tables)) {
            return false;
        }

        $id = isset($where['id']) ? (int) $where['id'] : 0;
        if ($id <= 0 || !isset($this->tables[$table][$id])) {
            return 0;
        }

        unset($this->tables[$table][$id]);
        return 1;
    }

    public function query(string $query)
    {
        if (preg_match('~DELETE\s+FROM\s+([^\s]+)\s+WHERE\s+tier_id\s*=\s*(\d+)~i', $query, $m)) {
            $table = $m[1];
            $tierId = (int) $m[2];
            if (!array_key_exists($table, $this->tables)) {
                return false;
            }

            foreach ($this->tables[$table] as $id => $row) {
                if ((int) ($row['tier_id'] ?? 0) === $tierId) {
                    unset($this->tables[$table][$id]);
                }
            }

            return 1;
        }

        return false;
    }

    public function get_row(string $query, $output = ARRAY_A)
    {
        if (preg_match('~FROM\s+([^\s]+)\s+WHERE\s+id\s*=\s*(\d+)~i', $query, $m)) {
            $table = $m[1];
            $id = (int) $m[2];
            if (!array_key_exists($table, $this->tables) || !isset($this->tables[$table][$id])) {
                return null;
            }

            $row = $this->tables[$table][$id];
            if (array_key_exists('deleted_at', $row) && $row['deleted_at'] !== null && stripos($query, 'deleted_at IS NULL') !== false) {
                return null;
            }

            return $row;
        }

        return null;
    }

    public function get_results(string $query, $output = ARRAY_A)
    {
        if (preg_match('~FROM\s+([^\s]+)\s+WHERE\s+tier_id\s*=\s*(\d+)~i', $query, $m)) {
            $table = $m[1];
            $tierId = (int) $m[2];
            if (!array_key_exists($table, $this->tables)) {
                return [];
            }

            $rows = [];
            foreach ($this->tables[$table] as $row) {
                if ((int) ($row['tier_id'] ?? 0) !== $tierId) {
                    continue;
                }
                if (isset($row['deleted_at']) && $row['deleted_at'] !== null && stripos($query, 'deleted_at IS NULL') !== false) {
                    continue;
                }
                $rows[] = $row;
            }

            usort($rows, function (array $a, array $b): int {
                return ((int) ($b['id'] ?? 0)) <=> ((int) ($a['id'] ?? 0));
            });

            return $rows;
        }

        return [];
    }
}

require_once __DIR__ . '/../vendor/autoload.php';

use GestiWork\Domain\Tiers\TierProvider;
use GestiWork\Domain\Tiers\TierContactProvider;
use GestiWork\Domain\Tiers\LegalFormCatalog;

$GLOBALS['wpdb'] = new wpdb();

function expectTrue(bool $condition, string $message): void
{
    if ($condition) {
        return;
    }

    fwrite(STDERR, "FAIL: {$message}\n");
    exit(1);
}

function expectSame($expected, $actual, string $message): void
{
    if ($expected === $actual) {
        return;
    }

    $exp = var_export($expected, true);
    $act = var_export($actual, true);
    fwrite(STDERR, "FAIL: {$message}\nexpected={$exp}\nactual={$act}\n");
    exit(1);
}

function expectNotEmpty($value, string $message): void
{
    if (!empty($value)) {
        return;
    }

    fwrite(STDERR, "FAIL: {$message}\n");
    exit(1);
}

function info(string $message): void
{
    fwrite(STDOUT, $message . "\n");
}

$start = microtime(true);
info('GestiWork tests/run.php');

$particulierId = TierProvider::create([
    'type' => 'client_particulier',
    'statut' => 'client',
    'nom' => 'Dupont',
    'prenom' => 'Jean',
    'email' => 'jean.dupont@example.test',
    'adresse1' => '1 rue de test',
    'cp' => '17000',
    'ville' => 'La Rochelle',
]);

expectTrue($particulierId > 0, 'Création client particulier: id > 0');
info("- Client particulier créé: id={$particulierId}");
$particulier = TierProvider::getById($particulierId);
expectTrue(is_array($particulier), 'Création client particulier: getById retourne un array');
expectSame('17000', $particulier['cp'] ?? null, 'Création client particulier: CP persiste');
expectSame('La Rochelle', $particulier['ville'] ?? null, 'Création client particulier: ville persiste');
expectSame('client_particulier', $particulier['type'] ?? null, 'Création client particulier: type persiste');
info('- Assertions particulier OK (cp/ville/type)');

$labels = LegalFormCatalog::labels();
expectNotEmpty($labels['6598'] ?? '', 'LegalFormCatalog: code 6598 doit avoir un libellé');
info("- LegalFormCatalog[6598] = " . ($labels['6598'] ?? ''));

$entrepriseId = TierProvider::create([
    'type' => 'client_entreprise',
    'statut' => 'client',
    'raison_sociale' => 'Entreprise Exemple',
    'siret' => '12345678901234',
    'adresse1' => '15 Rue des Lilas',
    'cp' => '93100',
    'ville' => 'MONTREUIL',
    'forme_juridique' => $labels['6598'],
]);

expectTrue($entrepriseId > 0, 'Création client entreprise: id > 0');
info("- Client entreprise créé: id={$entrepriseId}");
$entreprise = TierProvider::getById($entrepriseId);
expectTrue(is_array($entreprise), 'Création client entreprise: getById retourne un array');
expectSame('93100', $entreprise['cp'] ?? null, 'Création client entreprise: CP persiste');
expectSame('MONTREUIL', $entreprise['ville'] ?? null, 'Création client entreprise: ville persiste (libellé)');
expectSame($labels['6598'], $entreprise['forme_juridique'] ?? null, 'Création client entreprise: forme_juridique persiste (libellé)');
info('- Assertions entreprise OK (cp/ville/forme_juridique)');

$contact1Id = TierContactProvider::create($entrepriseId, [
    'civilite' => 'monsieur',
    'fonction' => 'Gérant',
    'nom' => 'Martin',
    'prenom' => 'Paul',
    'mail' => 'paul.martin@example.test',
    'tel1' => '06 00 00 00 01',
]);

$contact2Id = TierContactProvider::create($entrepriseId, [
    'civilite' => 'madame',
    'fonction' => 'Compta',
    'nom' => 'Durand',
    'prenom' => 'Alice',
    'mail' => 'alice.durand@example.test',
    'tel1' => '06 00 00 00 02',
    'tel2' => '05 00 00 00 02',
]);

expectTrue($contact1Id > 0, 'Création contact 1: id > 0');
expectTrue($contact2Id > 0, 'Création contact 2: id > 0');
info("- Contacts créés: id1={$contact1Id}, id2={$contact2Id}");

$contacts = TierContactProvider::listByTierId($entrepriseId);
expectSame(2, count($contacts), 'Création contacts: listByTierId doit retourner 2 lignes');
info('- Assertions contacts OK (2 contacts liés)');

$contactIds = array_map(function (array $row): int {
    return (int) ($row['id'] ?? 0);
}, $contacts);

expectTrue(in_array($contact1Id, $contactIds, true), 'Création contacts: contact1 présent');
expectTrue(in_array($contact2Id, $contactIds, true), 'Création contacts: contact2 présent');

$elapsedMs = (microtime(true) - $start) * 1000;
info(sprintf('OK (%.1f ms)', $elapsedMs));
