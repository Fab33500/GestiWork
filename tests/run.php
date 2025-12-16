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

if (!function_exists('__')) {
    function __(string $text, string $domain = ''): string
    {
        return $text;
    }
}

if (!function_exists('esc_html__')) {
    function esc_html__(string $text, string $domain = ''): string
    {
        return $text;
    }
}

if (!function_exists('esc_attr__')) {
    function esc_attr__(string $text, string $domain = ''): string
    {
        return $text;
    }
}

if (!function_exists('esc_html_e')) {
    function esc_html_e(string $text, string $domain = ''): void
    {
        echo $text;
    }
}

if (!function_exists('esc_attr_e')) {
    function esc_attr_e(string $text, string $domain = ''): void
    {
        echo $text;
    }
}

if (!function_exists('esc_html')) {
    function esc_html(string $text): string
    {
        return $text;
    }
}

if (!function_exists('esc_attr')) {
    function esc_attr(string $text): string
    {
        return $text;
    }
}

if (!function_exists('esc_url')) {
    function esc_url(string $url): string
    {
        return $url;
    }
}

if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field(string $text): string
    {
        return trim($text);
    }
}

if (!function_exists('wp_unslash')) {
    function wp_unslash(string $text): string
    {
        return $text;
    }
}

if (!function_exists('current_user_can')) {
    function current_user_can(string $cap): bool
    {
        return true;
    }
}

if (!function_exists('wp_die')) {
    function wp_die(string $message = '', int $code = 0): void
    {
        throw new RuntimeException($message !== '' ? $message : 'wp_die');
    }
}

if (!function_exists('home_url')) {
    function home_url(string $path = ''): string
    {
        return 'http://example.test' . $path;
    }
}

if (!function_exists('wp_nonce_field')) {
    function wp_nonce_field($action = -1, string $name = '_wpnonce', bool $referer = true, bool $echo = true)
    {
        $field = '<input type="hidden" name="' . esc_attr($name) . '" value="test_nonce" />';
        if ($echo) {
            echo $field;
            return '';
        }
        return $field;
    }
}

if (!function_exists('get_bloginfo')) {
    function get_bloginfo(string $show = ''): string
    {
        if ($show === 'name') {
            return 'Site de test';
        }
        return '';
    }
}

if (!function_exists('get_theme_mod')) {
    function get_theme_mod(string $name, $default = false)
    {
        return $default;
    }
}

if (!function_exists('wp_get_attachment_image_url')) {
    function wp_get_attachment_image_url(int $attachment_id, string $size = 'full')
    {
        return '';
    }
}

if (!function_exists('get_site_icon_url')) {
    function get_site_icon_url(int $size = 512, string $url = ''): string
    {
        return '';
    }
}

if (!function_exists('language_attributes')) {
    function language_attributes(string $doctype = 'html'): void
    {
        echo 'lang="fr-FR"';
    }
}

if (!function_exists('bloginfo')) {
    function bloginfo(string $show = ''): void
    {
        if ($show === 'charset') {
            echo 'UTF-8';
            return;
        }
        echo '';
    }
}

if (!function_exists('wp_head')) {
    function wp_head(): void
    {
    }
}

if (!function_exists('wp_footer')) {
    function wp_footer(): void
    {
    }
}

if (!function_exists('body_class')) {
    function body_class($class = ''): void
    {
        $classes = [];
        if (is_array($class)) {
            $classes = $class;
        } elseif (is_string($class) && $class !== '') {
            $classes = preg_split('~\s+~', $class) ?: [];
        }

        $class_attr = trim(implode(' ', array_filter(array_map('strval', $classes))));
        echo 'class="' . esc_attr($class_attr) . '"';
    }
}

if (!function_exists('add_query_arg')) {
    function add_query_arg(array $args, string $url): string
    {
        $parsed = parse_url($url);
        $existing = [];
        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $existing);
        }
        $merged = array_merge($existing, $args);
        $query = http_build_query($merged);

        $base = $url;
        if (isset($parsed['scheme'], $parsed['host'])) {
            $base = $parsed['scheme'] . '://' . $parsed['host'] . ($parsed['path'] ?? '');
        }

        return $query !== '' ? ($base . '?' . $query) : $base;
    }
}

if (!function_exists('sanitize_email')) {
    function sanitize_email(string $email): string
    {
        return trim($email);
    }
}

if (!function_exists('sanitize_textarea_field')) {
    function sanitize_textarea_field(string $text): string
    {
        return trim($text);
    }
}

if (!function_exists('wp_verify_nonce')) {
    function wp_verify_nonce(string $nonce, string $action): bool
    {
        return true; // Toujours valide en test
    }
}

if (!function_exists('wp_redirect')) {
    function wp_redirect(string $location, int $status = 302): void
    {
        // Ne fait rien en test
    }
}

if (!function_exists('get_query_var')) {
    function get_query_var(string $var, $default = '')
    {
        return $_GET[$var] ?? $default;
    }
}

if (!function_exists('add_action')) {
    function add_action(string $hook, $callback, int $priority = 10, int $accepted_args = 1): void
    {
        // Ne fait rien en test
    }
}

if (!function_exists('esc_textarea')) {
    function esc_textarea(string $text): string
    {
        return htmlspecialchars($text);
    }
}

if (!function_exists('selected')) {
    function selected(string $value, string $current): void
    {
        if ($value === $current) {
            echo ' selected="selected"';
        }
    }
}

class wpdb
{
    public string $prefix = 'wp_';

    public int $insert_id = 0;

    private array $tables = [];

    private int $tiersAutoId = 0;

    private int $contactsAutoId = 0;
    private int $apprenantsAutoId = 0;
    private int $responsablesAutoId = 0;
    private int $competencesAutoId = 0;

    public function __construct()
    {
        $this->tables[$this->prefix . 'gw_tiers'] = [];
        $this->tables[$this->prefix . 'gw_tier_contacts'] = [];
        $this->tables[$this->prefix . 'gw_apprenants'] = [];
        $this->tables[$this->prefix . 'gw_responsables_formateurs'] = [];
        $this->tables[$this->prefix . 'gw_formateur_competences'] = [];
    }

    public function prepare(string $query, ...$args): string
    {
        if (!$args) {
            return $query;
        }

        if (count($args) === 1 && is_array($args[0])) {
            $args = $args[0];
        }

        $normalized = [];
        foreach ($args as $arg) {
            if (is_string($arg)) {
                $normalized[] = "'" . str_replace("'", "\\'", $arg) . "'";
                continue;
            }
            $normalized[] = $arg;
        }

        return vsprintf($query, $normalized);
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

                $rows = array_values($this->tables[$table]);
                if (stripos($query, "type = 'entreprise'") !== false) {
                    $rows = array_values(array_filter($rows, function (array $row): bool {
                        return (string) ($row['type'] ?? '') === 'entreprise';
                    }));
                }
                if (stripos($query, 'deleted_at IS NULL') !== false) {
                    $rows = array_values(array_filter($rows, function (array $row): bool {
                        return !isset($row['deleted_at']) || $row['deleted_at'] === null;
                    }));
                }

                return count($rows);
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

        if (str_ends_with($table, 'gw_apprenants')) {
            $this->apprenantsAutoId++;
            $data['id'] = $this->apprenantsAutoId;
            $this->tables[$table][$this->apprenantsAutoId] = $data;
            $this->insert_id = $this->apprenantsAutoId;
            return 1;
        }

        if (str_ends_with($table, 'gw_responsables_formateurs')) {
            $this->responsablesAutoId++;
            $data['id'] = $this->responsablesAutoId;
            $this->tables[$table][$this->responsablesAutoId] = $data;
            $this->insert_id = $this->responsablesAutoId;
            return 1;
        }

        if (str_ends_with($table, 'gw_formateur_competences')) {
            $this->competencesAutoId++;
            $data['id'] = $this->competencesAutoId;
            $this->tables[$table][$this->competencesAutoId] = $data;
            $this->insert_id = $this->competencesAutoId;
            return 1;
        }

        return false;
    }

    public function update(string $table, array $data, array $where, $format = null, array $whereFormat = [])
    {
        if (!array_key_exists($table, $this->tables)) {
            return false;
        }

        // Support pour UPDATE WHERE formateur_id = X
        if (isset($where['formateur_id'])) {
            $formateurId = (int) $where['formateur_id'];
            $updated = 0;
            foreach ($this->tables[$table] as $id => $row) {
                if ((int) ($row['formateur_id'] ?? 0) === $formateurId) {
                    $this->tables[$table][$id] = array_merge($this->tables[$table][$id], $data);
                    $updated++;
                }
            }
            return $updated;
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

        // Support pour DELETE by formateur_id
        if (isset($where['formateur_id'])) {
            $formateurId = (int) $where['formateur_id'];
            $deleted = 0;
            foreach ($this->tables[$table] as $id => $row) {
                if ((int) ($row['formateur_id'] ?? 0) === $formateurId) {
                    unset($this->tables[$table][$id]);
                    $deleted++;
                }
            }
            return $deleted;
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

        // Support pour SELECT cout_* FROM table WHERE formateur_id = X LIMIT 1
        if (preg_match('~SELECT\s+cout_jour_ht,\s*cout_heure_ht,\s*heures_par_jour,\s*tva_rate\s+FROM\s+([^\s]+)\s+WHERE\s+formateur_id\s*=\s*(\d+)\s+LIMIT\s+1~i', $query, $m)) {
            $table = $m[1];
            $formateurId = (int) $m[2];
            if (!array_key_exists($table, $this->tables) || empty($this->tables[$table])) {
                return null;
            }

            foreach ($this->tables[$table] as $row) {
                if ((int) ($row['formateur_id'] ?? 0) === $formateurId) {
                    return [
                        'cout_jour_ht' => $row['cout_jour_ht'] ?? null,
                        'cout_heure_ht' => $row['cout_heure_ht'] ?? null,
                        'heures_par_jour' => $row['heures_par_jour'] ?? 7.00,
                        'tva_rate' => $row['tva_rate'] ?? 0.00,
                    ];
                }
            }

            return null;
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

        // Support pour SELECT competence FROM table WHERE formateur_id = X
        if (preg_match('~SELECT\s+competence\s+FROM\s+([^\s]+)\s+WHERE\s+formateur_id\s*=\s*(\d+)~i', $query, $m)) {
            $table = $m[1];
            $formateurId = (int) $m[2];
            if (!array_key_exists($table, $this->tables)) {
                return [];
            }

            $rows = [];
            foreach ($this->tables[$table] as $row) {
                if ((int) ($row['formateur_id'] ?? 0) === $formateurId) {
                    $rows[] = ['competence' => $row['competence'] ?? ''];
                }
            }

            return $rows;
        }

        // Support pour SELECT * FROM table ORDER BY ...
        if (preg_match('~SELECT\s+\*\s+FROM\s+([^\s]+)(?:\s+ORDER\s+BY\s+[^;]+)?~i', $query, $m)) {
            $table = $m[1];
            if (!array_key_exists($table, $this->tables)) {
                return [];
            }

            $rows = array_values($this->tables[$table]);

            // Filtrages simples utilisés par TierProvider::search (type, deleted_at)
            if (stripos($query, "type = 'entreprise'") !== false) {
                $rows = array_values(array_filter($rows, function (array $row): bool {
                    return (string) ($row['type'] ?? '') === 'entreprise';
                }));
            }
            if (stripos($query, 'deleted_at IS NULL') !== false) {
                $rows = array_values(array_filter($rows, function (array $row): bool {
                    return !isset($row['deleted_at']) || $row['deleted_at'] === null;
                }));
            }

            // Support minimal LIMIT/OFFSET
            if (preg_match('~LIMIT\s+(\d+)\s+OFFSET\s+(\d+)~i', $query, $lm)) {
                $limit = (int) $lm[1];
                $offset = (int) $lm[2];
                $rows = array_slice($rows, $offset, $limit);
            }

            return $rows;
        }

        return [];
    }
}

require_once __DIR__ . '/../vendor/autoload.php';

use GestiWork\Domain\Tiers\TierProvider;
use GestiWork\Domain\Tiers\TierContactProvider;
use GestiWork\Domain\Tiers\LegalFormCatalog;
use GestiWork\Domain\Apprenant\ApprenantProvider;
use GestiWork\Domain\ResponsableFormateur\ResponsableFormateurProvider;
use GestiWork\Domain\ResponsableFormateur\FormateurCompetenceProvider;

$GLOBALS['wpdb'] = new wpdb();

if (!defined('GW_PLUGIN_DIR')) {
    $pluginRoot = realpath(__DIR__ . '/..');
    if ($pluginRoot === false) {
        $pluginRoot = __DIR__ . '/..';
    }
    define('GW_PLUGIN_DIR', rtrim($pluginRoot, "\\/") . DIRECTORY_SEPARATOR);
}

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

info('--- Smoke tests templates (Apprenants / Équipe pédagogique) ---');

$renderTemplate = function (string $absolutePath, array $get = []): string {
    $oldGet = $_GET;
    $_GET = $get;

    ob_start();
    try {
        require $absolutePath;
    } catch (Throwable $e) {
        ob_end_clean();
        fwrite(STDERR, "FAIL: Template {$absolutePath} a levé une exception: {$e->getMessage()}\n");
        exit(1);
    }
    $out = ob_get_clean();

    $_GET = $oldGet;
    return $out;
};

$apprenantsHtml = $renderTemplate(GW_PLUGIN_DIR . 'templates/erp/apprenants/view-apprenants.php', [
    'gw_apprenants_query' => 'Dupont',
]);
expectNotEmpty($apprenantsHtml, 'Template apprenants: doit produire du HTML');
expectTrue(strpos($apprenantsHtml, 'Recherche avancée') !== false, 'Template apprenants: contient "Recherche avancée"');
expectTrue(strpos($apprenantsHtml, 'liste des apprenants') !== false, 'Template apprenants: contient "liste des apprenants"');

$equipeHtml = $renderTemplate(GW_PLUGIN_DIR . 'templates/erp/equipe-pedagogique/view-equipe-pedagogique.php', [
    'gw_formateurs_query' => 'Paugam',
]);
expectNotEmpty($equipeHtml, 'Template équipe pédagogique: doit produire du HTML');
expectTrue(strpos($equipeHtml, 'Recherche avancée') !== false, 'Template équipe pédagogique: contient "Recherche avancée"');
expectTrue(strpos($equipeHtml, 'formateurs & responsables') !== false, 'Template équipe pédagogique: contient "formateurs & responsables"');

$apprenantFicheHtml = $renderTemplate(GW_PLUGIN_DIR . 'templates/erp/apprenants/view-apprenant.php', [
    'gw_apprenant_id' => 1,
]);
expectNotEmpty($apprenantFicheHtml, 'Template fiche apprenant: doit produire du HTML');
expectTrue(strpos($apprenantFicheHtml, 'Fiche apprenant') !== false, 'Template fiche apprenant: contient "Fiche apprenant"');

$responsableFicheHtml = $renderTemplate(GW_PLUGIN_DIR . 'templates/erp/equipe-pedagogique/view-responsable.php', [
    'gw_responsable_id' => 1,
]);
expectNotEmpty($responsableFicheHtml, 'Template fiche responsable: doit produire du HTML');
expectTrue(strpos($responsableFicheHtml, 'Fiche formateur / responsable pédagogique') !== false, 'Template fiche responsable: contient "Fiche formateur / responsable pédagogique"');

info('--- Tests nouveaux providers (Apprenants / ResponsableFormateur) ---');

// Test création apprenant
$apprenantId = ApprenantProvider::create([
    'civilite' => 'Monsieur',
    'prenom' => 'Harry',
    'nom' => 'Potter',
    'email' => 'harry.potter@poudlard.com',
    'telephone' => '01 23 45 67 89',
    'origine' => 'École de magie',
    'adresse1' => '4 Privet Drive',
    'cp' => '75001',
    'ville' => 'Paris',
]);

expectTrue($apprenantId > 0, 'Création apprenant: id > 0');
info("- Apprenant créé: id={$apprenantId}");

$apprenant = ApprenantProvider::getById($apprenantId);
expectTrue(is_array($apprenant), 'Création apprenant: getById retourne un array');
expectSame('Harry', $apprenant['prenom'] ?? null, 'Création apprenant: prénom persiste');
expectSame('Potter', $apprenant['nom'] ?? null, 'Création apprenant: nom persiste');
expectSame('École de magie', $apprenant['origine'] ?? null, 'Création apprenant: origine persiste');
info('- Assertions apprenant OK (prénom/nom/origine)');

// Test mise à jour apprenant
$updateSuccess = ApprenantProvider::update($apprenantId, [
    'entreprise_id' => $entrepriseId, // Association avec l'entreprise créée plus haut
    'statut_bpf' => 'En formation',
]);

expectTrue($updateSuccess, 'Mise à jour apprenant: doit réussir');
$apprenantMisAJour = ApprenantProvider::getById($apprenantId);
expectSame($entrepriseId, (int)($apprenantMisAJour['entreprise_id'] ?? 0), 'Mise à jour apprenant: entreprise_id persiste');
expectSame('En formation', $apprenantMisAJour['statut_bpf'] ?? null, 'Mise à jour apprenant: statut_bpf persiste');
info('- Mise à jour apprenant OK (entreprise_id/statut_bpf)');

// Test création responsable/formateur
$responsableId = ResponsableFormateurProvider::create([
    'civilite' => 'Madame',
    'prenom' => 'Minerva',
    'nom' => 'McGonagall',
    'fonction' => 'Directrice adjointe',
    'email' => 'mcgonagall@poudlard.com',
    'telephone' => '01 98 76 54 32',
    'role_type' => 'responsable_pedagogique',
    'sous_traitant' => 'Non',
    'adresse_postale' => 'Château de Poudlard',
    'rue' => 'Grande Salle',
    'code_postal' => '12345',
    'ville' => 'Écosse',
]);

expectTrue($responsableId > 0, 'Création responsable: id > 0');
info("- Responsable créé: id={$responsableId}");

$responsable = ResponsableFormateurProvider::getById($responsableId);
expectTrue(is_array($responsable), 'Création responsable: getById retourne un array');
expectSame('Minerva', $responsable['prenom'] ?? null, 'Création responsable: prénom persiste');
expectSame('McGonagall', $responsable['nom'] ?? null, 'Création responsable: nom persiste');
expectSame('responsable_pedagogique', $responsable['role_type'] ?? null, 'Création responsable: role_type persiste');
info('- Assertions responsable OK (prénom/nom/role_type)');

// Test compétences formateur
$competences = ['Métamorphose', 'Direction pédagogique', 'Gestion de classe'];
FormateurCompetenceProvider::saveCompetences($responsableId, $competences);

$competencesRecuperees = FormateurCompetenceProvider::getCompetencesByFormateurId($responsableId);
expectSame(3, count($competencesRecuperees), 'Compétences formateur: doit avoir 3 compétences');
expectTrue(in_array('Métamorphose', $competencesRecuperees, true), 'Compétences formateur: contient Métamorphose');
expectTrue(in_array('Direction pédagogique', $competencesRecuperees, true), 'Compétences formateur: contient Direction pédagogique');
info('- Compétences formateur OK (3 compétences sauvegardées)');

// Test coûts formateur (après création des compétences car ils sont dans la même table)
$couts = [
    'cout_jour_ht' => 850.00,
    'cout_heure_ht' => 125.00,
    'heures_par_jour' => 6.80,
    'tva_rate' => 20.00,
];
FormateurCompetenceProvider::saveCouts($responsableId, $couts);

$coutsRecuperes = FormateurCompetenceProvider::getCoutsByFormateurId($responsableId);
expectTrue(is_array($coutsRecuperes), 'Coûts formateur: getCoutsByFormateurId retourne un array');
expectSame(850.0, (float)($coutsRecuperes['cout_jour_ht'] ?? 0), 'Coûts formateur: cout_jour_ht persiste');
expectSame(125.0, (float)($coutsRecuperes['cout_heure_ht'] ?? 0), 'Coûts formateur: cout_heure_ht persiste');
expectSame(6.8, (float)($coutsRecuperes['heures_par_jour'] ?? 0), 'Coûts formateur: heures_par_jour persiste');
expectSame(20.0, (float)($coutsRecuperes['tva_rate'] ?? 0), 'Coûts formateur: tva_rate persiste');
info('- Coûts formateur OK (850€/jour, 125€/h, 6.8h/jour, TVA 20%)');

info('--- Tests de récupération de listes ---');

$tousApprenants = ApprenantProvider::getAll();
expectTrue(is_array($tousApprenants), 'Liste apprenants: getAll retourne un array');
expectTrue(count($tousApprenants) >= 1, 'Liste apprenants: contient au moins 1 apprenant');
info("- Liste apprenants: " . count($tousApprenants) . " apprenant(s) trouvé(s)");

$tousResponsables = ResponsableFormateurProvider::getAll();
expectTrue(is_array($tousResponsables), 'Liste responsables: getAll retourne un array');
expectTrue(count($tousResponsables) >= 1, 'Liste responsables: contient au moins 1 responsable');
info("- Liste responsables: " . count($tousResponsables) . " responsable(s) trouvé(s)");

$elapsedMs = (microtime(true) - $start) * 1000;
info(sprintf('OK - Tous les tests passés (%.1f ms)', $elapsedMs));
