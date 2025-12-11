<?php
/**
 * GestiWork ERP - PDF Shortcode Catalog
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

namespace GestiWork\Domain\Pdf;

/**
 * Catalogue des shortcodes PDF disponibles.
 *
 * Ce catalogue sert de source de vérité pour les shortcodes utilisables
 * dans les gabarits PDF (en-tête, pied de page, corps).
 * Il est utilisé pour alimenter la table gw_pdf_shortcodes.
 */
final class PdfShortcodeCatalog
{
    /**
     * Groupes de shortcodes avec leur libellé.
     *
     * @return array<string, string>
     */
    public static function getGroups(): array
    {
        return [
            'organisme'  => 'Organisme de formation',
            'document'   => 'Document',
            'client'     => 'Client / Financeur',
            'session'    => 'Session de formation',
            'stagiaire'  => 'Stagiaire',
            'formateur'  => 'Formateur',
        ];
    }

    /**
     * Catalogue complet des shortcodes.
     *
     * @return array<int, array{code: string, label: string, group_key: string, description: string}>
     */
    public static function getAll(): array
    {
        return [
            // ========================================
            // ORGANISME DE FORMATION (depuis gw_of_identity)
            // ========================================
            [
                'code'        => 'of:raison_sociale',
                'label'       => 'Raison sociale',
                'group_key'   => 'organisme',
                'description' => 'Nom officiel de l\'organisme de formation',
            ],
            [
                'code'        => 'of:nom_commercial',
                'label'       => 'Nom commercial',
                'group_key'   => 'organisme',
                'description' => 'Nom commercial (si différent de la raison sociale)',
            ],
            [
                'code'        => 'of:adresse',
                'label'       => 'Adresse',
                'group_key'   => 'organisme',
                'description' => 'Adresse postale de l\'organisme',
            ],
            [
                'code'        => 'of:code_postal',
                'label'       => 'Code postal',
                'group_key'   => 'organisme',
                'description' => 'Code postal de l\'organisme',
            ],
            [
                'code'        => 'of:ville',
                'label'       => 'Ville',
                'group_key'   => 'organisme',
                'description' => 'Ville de l\'organisme',
            ],
            [
                'code'        => 'of:telephone_fixe',
                'label'       => 'Téléphone fixe',
                'group_key'   => 'organisme',
                'description' => 'Numéro de téléphone fixe',
            ],
            [
                'code'        => 'of:telephone_portable',
                'label'       => 'Téléphone portable',
                'group_key'   => 'organisme',
                'description' => 'Numéro de téléphone portable',
            ],
            [
                'code'        => 'of:email_contact',
                'label'       => 'Email de contact',
                'group_key'   => 'organisme',
                'description' => 'Adresse email principale',
            ],
            [
                'code'        => 'of:site_web',
                'label'       => 'Site web',
                'group_key'   => 'organisme',
                'description' => 'URL du site internet',
            ],
            [
                'code'        => 'of:siret',
                'label'       => 'SIRET',
                'group_key'   => 'organisme',
                'description' => 'Numéro SIRET (14 chiffres)',
            ],
            [
                'code'        => 'of:code_ape',
                'label'       => 'Code APE',
                'group_key'   => 'organisme',
                'description' => 'Code APE / NAF',
            ],
            [
                'code'        => 'of:nda',
                'label'       => 'N° Déclaration Activité',
                'group_key'   => 'organisme',
                'description' => 'Numéro de déclaration d\'activité (NDA)',
            ],
            [
                'code'        => 'of:tva_intra',
                'label'       => 'TVA intracommunautaire',
                'group_key'   => 'organisme',
                'description' => 'Numéro de TVA intracommunautaire',
            ],
            [
                'code'        => 'of:representant_legal',
                'label'       => 'Représentant légal',
                'group_key'   => 'organisme',
                'description' => 'Nom du représentant légal',
            ],
            [
                'code'        => 'of:habilitation_inrs',
                'label'       => 'Habilitation INRS',
                'group_key'   => 'organisme',
                'description' => 'Texte d\'habilitation INRS de l\'organisme',
            ],
            [
                'code'        => 'of:logo',
                'label'       => 'Logo',
                'group_key'   => 'organisme',
                'description' => 'Logo de l\'organisme (balise img)',
            ],

            // ========================================
            // DOCUMENT
            // ========================================
            [
                'code'        => 'document:titre',
                'label'       => 'Titre du document',
                'group_key'   => 'document',
                'description' => 'Titre du document PDF généré',
            ],
            [
                'code'        => 'document:type',
                'label'       => 'Type de document',
                'group_key'   => 'document',
                'description' => 'Type (Convention, Devis, Attestation, etc.)',
            ],
            [
                'code'        => 'document:numero',
                'label'       => 'Numéro du document',
                'group_key'   => 'document',
                'description' => 'Numéro de référence du document',
            ],
            [
                'code'        => 'document:date_creation',
                'label'       => 'Date de création',
                'group_key'   => 'document',
                'description' => 'Date de création du document',
            ],
            [
                'code'        => 'document:date_jour',
                'label'       => 'Date du jour',
                'group_key'   => 'document',
                'description' => 'Date du jour de génération du PDF',
            ],
            [
                'code'        => 'document:page_courante',
                'label'       => 'Page courante',
                'group_key'   => 'document',
                'description' => 'Numéro de la page courante',
            ],
            [
                'code'        => 'document:total_pages',
                'label'       => 'Nombre total de pages',
                'group_key'   => 'document',
                'description' => 'Nombre total de pages du document',
            ],

            // ========================================
            // CLIENT / FINANCEUR
            // ========================================
            [
                'code'        => 'client:raison_sociale',
                'label'       => 'Raison sociale',
                'group_key'   => 'client',
                'description' => 'Nom de l\'entreprise cliente',
            ],
            [
                'code'        => 'client:adresse',
                'label'       => 'Adresse',
                'group_key'   => 'client',
                'description' => 'Adresse postale du client',
            ],
            [
                'code'        => 'client:code_postal',
                'label'       => 'Code postal',
                'group_key'   => 'client',
                'description' => 'Code postal du client',
            ],
            [
                'code'        => 'client:ville',
                'label'       => 'Ville',
                'group_key'   => 'client',
                'description' => 'Ville du client',
            ],
            [
                'code'        => 'client:siret',
                'label'       => 'SIRET',
                'group_key'   => 'client',
                'description' => 'Numéro SIRET du client',
            ],
            [
                'code'        => 'client:contact_nom',
                'label'       => 'Nom du contact',
                'group_key'   => 'client',
                'description' => 'Nom du contact principal',
            ],
            [
                'code'        => 'client:contact_email',
                'label'       => 'Email du contact',
                'group_key'   => 'client',
                'description' => 'Email du contact principal',
            ],

            // ========================================
            // SESSION DE FORMATION
            // ========================================
            [
                'code'        => 'session:intitule',
                'label'       => 'Intitulé de la formation',
                'group_key'   => 'session',
                'description' => 'Titre de la formation',
            ],
            [
                'code'        => 'session:date_debut',
                'label'       => 'Date de début',
                'group_key'   => 'session',
                'description' => 'Date de début de la session',
            ],
            [
                'code'        => 'session:date_fin',
                'label'       => 'Date de fin',
                'group_key'   => 'session',
                'description' => 'Date de fin de la session',
            ],
            [
                'code'        => 'session:duree_heures',
                'label'       => 'Durée (heures)',
                'group_key'   => 'session',
                'description' => 'Durée totale en heures',
            ],
            [
                'code'        => 'session:lieu',
                'label'       => 'Lieu',
                'group_key'   => 'session',
                'description' => 'Lieu de la formation',
            ],
            [
                'code'        => 'session:objectifs',
                'label'       => 'Objectifs',
                'group_key'   => 'session',
                'description' => 'Objectifs pédagogiques',
            ],
            [
                'code'        => 'session:tarif_ht',
                'label'       => 'Tarif HT',
                'group_key'   => 'session',
                'description' => 'Tarif hors taxes',
            ],
            [
                'code'        => 'session:tarif_ttc',
                'label'       => 'Tarif TTC',
                'group_key'   => 'session',
                'description' => 'Tarif toutes taxes comprises',
            ],

            // ========================================
            // STAGIAIRE
            // ========================================
            [
                'code'        => 'stagiaire:civilite',
                'label'       => 'Civilité',
                'group_key'   => 'stagiaire',
                'description' => 'Civilité (M., Mme)',
            ],
            [
                'code'        => 'stagiaire:nom',
                'label'       => 'Nom',
                'group_key'   => 'stagiaire',
                'description' => 'Nom de famille du stagiaire',
            ],
            [
                'code'        => 'stagiaire:prenom',
                'label'       => 'Prénom',
                'group_key'   => 'stagiaire',
                'description' => 'Prénom du stagiaire',
            ],
            [
                'code'        => 'stagiaire:nom_complet',
                'label'       => 'Nom complet',
                'group_key'   => 'stagiaire',
                'description' => 'Prénom + Nom du stagiaire',
            ],
            [
                'code'        => 'stagiaire:email',
                'label'       => 'Email',
                'group_key'   => 'stagiaire',
                'description' => 'Adresse email du stagiaire',
            ],
            [
                'code'        => 'stagiaire:telephone',
                'label'       => 'Téléphone',
                'group_key'   => 'stagiaire',
                'description' => 'Numéro de téléphone du stagiaire',
            ],

            // ========================================
            // FORMATEUR
            // ========================================
            [
                'code'        => 'formateur:civilite',
                'label'       => 'Civilité',
                'group_key'   => 'formateur',
                'description' => 'Civilité (M., Mme)',
            ],
            [
                'code'        => 'formateur:nom',
                'label'       => 'Nom',
                'group_key'   => 'formateur',
                'description' => 'Nom de famille du formateur',
            ],
            [
                'code'        => 'formateur:prenom',
                'label'       => 'Prénom',
                'group_key'   => 'formateur',
                'description' => 'Prénom du formateur',
            ],
            [
                'code'        => 'formateur:nom_complet',
                'label'       => 'Nom complet',
                'group_key'   => 'formateur',
                'description' => 'Prénom + Nom du formateur',
            ],
            [
                'code'        => 'formateur:email',
                'label'       => 'Email',
                'group_key'   => 'formateur',
                'description' => 'Adresse email du formateur',
            ],
            [
                'code'        => 'formateur:telephone',
                'label'       => 'Téléphone',
                'group_key'   => 'formateur',
                'description' => 'Numéro de téléphone du formateur',
            ],
        ];
    }

    /**
     * Retourne les shortcodes groupés par group_key.
     *
     * @return array<string, array<int, array{code: string, label: string, group_key: string, description: string}>>
     */
    public static function getGrouped(): array
    {
        $all = self::getAll();
        $grouped = [];

        foreach ($all as $shortcode) {
            $key = $shortcode['group_key'];
            if (!isset($grouped[$key])) {
                $grouped[$key] = [];
            }
            $grouped[$key][] = $shortcode;
        }

        return $grouped;
    }
}
