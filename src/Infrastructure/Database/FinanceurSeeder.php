<?php
/**
 * GestiWork ERP - Financeur Seeder
 *
 * @copyright Copyright (c) ...
 */

declare(strict_types=1);

namespace GestiWork\Infrastructure\Database;

use wpdb;

use function get_option;
use function update_option;

class FinanceurSeeder
{
    private const OPTION_KEY = 'gestiwork_financeur_seed_v2';

    public static function opcoRaisonSociales(): array
    {
        $names = [];
        foreach (self::financeurs() as $financeur) {
            $raisonSociale = isset($financeur['raison_sociale']) ? (string) $financeur['raison_sociale'] : '';
            if ($raisonSociale === '') {
                continue;
            }
            if (str_starts_with($raisonSociale, 'OPCO ')) {
                $names[] = $raisonSociale;
            }
        }

        return $names;
    }

    public static function seed(): void
    {
        if (get_option(self::OPTION_KEY) === '1') {
            return;
        }

        global $wpdb;

        if (!($wpdb instanceof wpdb)) {
            return;
        }

        $tableName = $wpdb->prefix . 'gw_tiers';
        $tableExists = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $tableName)
        );

        if ($tableExists !== $tableName) {
            return;
        }

        foreach (self::financeurs() as $financeur) {
            $raisonSociale = $financeur['raison_sociale'];
            $desiredType = str_starts_with($raisonSociale, 'OPCO ') ? 'opco' : 'financeur';

            $existingRow = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT id, type FROM {$tableName} WHERE LOWER(raison_sociale) = %s LIMIT 1",
                    strtolower($raisonSociale)
                ),
                ARRAY_A
            );

            if (is_array($existingRow) && (int) ($existingRow['id'] ?? 0) > 0) {
                $existingId = (int) $existingRow['id'];
                $existingType = (string) ($existingRow['type'] ?? '');
                if ($existingType !== $desiredType) {
                    $wpdb->update(
                        $tableName,
                        ['type' => $desiredType],
                        ['id' => $existingId],
                        ['%s'],
                        ['%d']
                    );
                }
                continue;
            }

            $wpdb->insert(
                $tableName,
                [
                    'type' => $desiredType,
                    'statut' => 'client',
                    'raison_sociale' => $raisonSociale,
                    'nom' => '',
                    'prenom' => '',
                    'siret' => $financeur['siret'],
                    'forme_juridique' => $financeur['forme_juridique'],
                    'email' => $financeur['email'],
                    'telephone' => $financeur['telephone'],
                    'telephone_portable' => '',
                    'adresse1' => $financeur['adresse1'],
                    'adresse2' => $financeur['adresse2'],
                    'cp' => $financeur['cp'],
                    'ville' => $financeur['ville'],
                ]
            );
        }

        update_option(self::OPTION_KEY, '1');
    }

    private static function financeurs(): array
    {
        return [
            [
                'raison_sociale' => 'OPCO Afdas',
                'siret' => '',
                'forme_juridique' => '',
                'email' => 'contact@afdas.com',
                'telephone' => '0144809500',
                'adresse1' => '66 rue Stendhal',
                'adresse2' => '',
                'cp' => '75020',
                'ville' => 'Paris',
            ],
            [
                'raison_sociale' => 'OPCO AKTO',
                'siret' => '',
                'forme_juridique' => '',
                'email' => 'contact@akto.fr',
                'telephone' => '0153434200',
                'adresse1' => '13 rue Georges Auric',
                'adresse2' => '',
                'cp' => '75019',
                'ville' => 'Paris',
            ],
            [
                'raison_sociale' => 'OPCO Atlas',
                'siret' => '',
                'forme_juridique' => '',
                'email' => 'contact@opco-atlas.fr',
                'telephone' => '0185653400',
                'adresse1' => '148 boulevard Haussmann',
                'adresse2' => '',
                'cp' => '75008',
                'ville' => 'Paris',
            ],
            [
                'raison_sociale' => 'OPCO Cohésion Sociale',
                'siret' => '',
                'forme_juridique' => '',
                'email' => 'contact@uniformation.fr',
                'telephone' => '0141362300',
                'adresse1' => '43 rue Jean Bleuzen',
                'adresse2' => '',
                'cp' => '92170',
                'ville' => 'Vanves',
            ],
            [
                'raison_sociale' => 'OPCO Commerce',
                'siret' => '',
                'forme_juridique' => '',
                'email' => 'contact@opcommerce.com',
                'telephone' => '0153443900',
                'adresse1' => '251 boulevard Pereire',
                'adresse2' => '',
                'cp' => '75017',
                'ville' => 'Paris',
            ],
            [
                'raison_sociale' => 'OPCO Construction',
                'siret' => '',
                'forme_juridique' => '',
                'email' => 'contact@constructys.fr',
                'telephone' => '0153430100',
                'adresse1' => '4 rue du Val de Marne',
                'adresse2' => '',
                'cp' => '75640',
                'ville' => 'Paris Cedex 13',
            ],
            [
                'raison_sociale' => 'OPCO Entreprises de Proximité',
                'siret' => '',
                'forme_juridique' => '',
                'email' => 'contact@opco-ep.fr',
                'telephone' => '0153880400',
                'adresse1' => '53 rue Ampère',
                'adresse2' => '',
                'cp' => '75017',
                'ville' => 'Paris',
            ],
            [
                'raison_sociale' => 'OPCO Mobilités',
                'siret' => '',
                'forme_juridique' => '',
                'email' => 'contact@opcomobilites.fr',
                'telephone' => '0130667000',
                'adresse1' => '43 avenue du Centre',
                'adresse2' => '',
                'cp' => '78180',
                'ville' => 'Montigny-le-Bretonneux',
            ],
            [
                'raison_sociale' => 'OPCO Santé',
                'siret' => '',
                'forme_juridique' => '',
                'email' => 'contact@opcosante.fr',
                'telephone' => '0140731630',
                'adresse1' => '31 rue Anatole France',
                'adresse2' => '',
                'cp' => '92300',
                'ville' => 'Levallois-Perret',
            ],
            [
                'raison_sociale' => 'OPCO OCAPIAT',
                'siret' => '',
                'forme_juridique' => '',
                'email' => 'contact@ocapiat.fr',
                'telephone' => '0148839200',
                'adresse1' => '128 rue de la Boétie',
                'adresse2' => '',
                'cp' => '75008',
                'ville' => 'Paris',
            ],
            [
                'raison_sociale' => 'OPCO 2i',
                'siret' => '',
                'forme_juridique' => '',
                'email' => 'contact@opco2i.fr',
                'telephone' => '0149430500',
                'adresse1' => '4 rue de la Convention',
                'adresse2' => '',
                'cp' => '94270',
                'ville' => 'Le Kremlin-Bicêtre',
            ],
            [
                'raison_sociale' => 'Caisse des Dépôts et Consignations',
                'siret' => '',
                'forme_juridique' => '',
                'email' => 'contact@caissedesdepots.fr',
                'telephone' => '0140748000',
                'adresse1' => '56 rue de Lille',
                'adresse2' => '',
                'cp' => '75007',
                'ville' => 'Paris',
            ],
        ];
    }
}
