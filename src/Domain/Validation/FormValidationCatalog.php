<?php

declare(strict_types=1);

namespace GestiWork\Domain\Validation;

class FormValidationCatalog
{
    public static function rules(): array
    {
        return [
            'settings_identity' => [
                'errorSelector' => '#gw_identity_error',
                'messages' => [
                    'requiredOnly' => 'Merci de renseigner tous les champs obligatoires marqués d’une astérisque rouge (*).',
                    'groupOnly' => 'Merci de renseigner au moins un numéro de téléphone (fixe ou portable).',
                    'requiredAndGroup' => 'Merci de renseigner tous les champs obligatoires marqués d’une astérisque rouge (*) et au moins un numéro de téléphone (fixe ou portable).',
                ],
                'fields' => [
                    'gw_raison_sociale' => [
                        'required' => true,
                        'label' => 'Nom (raison sociale)',
                    ],
                    'gw_email_contact' => [
                        'required' => true,
                        'label' => 'E-mail de contact',
                        'type' => 'email',
                    ],
                    'gw_adresse' => [
                        'required' => true,
                        'label' => 'Adresse',
                    ],
                    'gw_code_postal' => [
                        'required' => true,
                        'label' => 'Code postal',
                        'pattern' => '[0-9]{5}',
                        'maxlength' => 5,
                        'inputmode' => 'numeric',
                    ],
                    'gw_ville' => [
                        'required' => true,
                        'label' => 'Ville',
                    ],
                    'gw_siret' => [
                        'required' => true,
                        'label' => 'SIRET / SIREN',
                        'pattern' => '([0-9]{3} [0-9]{3} [0-9]{3})( [0-9]{5})?',
                    ],
                    'gw_code_ape' => [
                        'required' => true,
                        'label' => 'Code APE (NAF)',
                    ],
                    'gw_nda' => [
                        'required' => true,
                        'label' => 'NDA (numéro de déclaration d’activité)',
                    ],
                    'gw_format_numero_devis' => [
                        'required' => true,
                        'label' => 'Format des numéros de devis / propositions',
                    ],
                    'gw_telephone_fixe' => [
                        'label' => 'Téléphone fixe',
                        'pattern' => '[0-9]{2}( [0-9]{2}){4}',
                        'inputmode' => 'tel',
                    ],
                    'gw_telephone_portable' => [
                        'label' => 'Téléphone portable',
                        'pattern' => '[0-9]{2}( [0-9]{2}){4}',
                        'inputmode' => 'tel',
                    ],
                    'gw_iban' => [
                        'label' => 'IBAN',
                        'pattern' => '[A-Z]{2}[0-9]{2}[A-Z0-9]{11,30}',
                        'maxlength' => 34,
                        'inputmode' => 'text',
                    ],
                    'gw_bic' => [
                        'label' => 'BIC',
                        'pattern' => '[A-Z]{4}[A-Z]{2}[A-Z0-9]{2}([A-Z0-9]{3})?',
                        'maxlength' => 11,
                        'inputmode' => 'text',
                    ],
                ],
                'groups' => [
                    [
                        'type' => 'atLeastOne',
                        'fields' => ['gw_telephone_fixe', 'gw_telephone_portable'],
                        'label' => 'au moins un numéro de téléphone (fixe ou portable)',
                    ],
                ],
            ],

            'tier_create' => [
                'errorSelector' => '#gw_tier_create_error',
                'messages' => [
                    'requiredOnly' => 'Merci de renseigner tous les champs obligatoires marqués d’une astérisque rouge (*).',
                    'groupOnly' => 'Merci de renseigner au moins un numéro de téléphone (fixe ou portable).',
                    'requiredAndGroup' => 'Merci de renseigner tous les champs obligatoires marqués d’une astérisque rouge (*) et au moins un numéro de téléphone (fixe ou portable).',
                ],
                'fields' => [
                    'gw_tier_create_nom' => [
                        'requiredIf' => ['field' => 'gw_tier_create_type', 'equals' => ['client_particulier', 'entreprise_independant']],
                        'label' => 'Nom',
                    ],
                    'gw_tier_create_prenom' => [
                        'requiredIf' => ['field' => 'gw_tier_create_type', 'equals' => ['client_particulier', 'entreprise_independant']],
                        'label' => 'Prénom',
                    ],
                    'gw_tier_create_raison_sociale' => [
                        'requiredIf' => ['field' => 'gw_tier_create_type', 'notEquals' => 'client_particulier'],
                        'label' => 'Nom / Raison sociale',
                    ],
                    'gw_tier_create_siret' => [
                        'requiredIf' => ['field' => 'gw_tier_create_type', 'notEquals' => 'client_particulier'],
                        'label' => 'SIRET / SIREN',
                        'pattern' => '([0-9]{3} [0-9]{3} [0-9]{3})( [0-9]{5})?',
                    ],
                    'gw_tier_create_email' => [
                        'required' => true,
                        'label' => 'Adresse e-mail',
                        'type' => 'email',
                    ],
                    'gw_tier_create_adresse1' => [
                        'required' => true,
                        'label' => 'Numéro de rue et rue',
                    ],
                    'gw_tier_create_cp' => [
                        'required' => true,
                        'label' => 'Code postal',
                        'pattern' => '[0-9]{5}',
                        'maxlength' => 5,
                        'inputmode' => 'numeric',
                    ],
                    'gw_tier_create_ville' => [
                        'required' => true,
                        'label' => 'Ville',
                    ],
                    'gw_tier_create_phone' => [
                        'label' => 'Numéro de téléphone',
                        'pattern' => '[0-9]{2}( [0-9]{2}){4}',
                        'inputmode' => 'tel',
                    ],
                    'gw_tier_create_phone_mobile' => [
                        'label' => 'Téléphone portable',
                        'pattern' => '[0-9]{2}( [0-9]{2}){4}',
                        'inputmode' => 'tel',
                    ],
                ],
                'groups' => [
                    [
                        'type' => 'atLeastOne',
                        'fields' => ['gw_tier_create_phone', 'gw_tier_create_phone_mobile'],
                        'label' => 'au moins un numéro de téléphone (fixe ou portable)',
                    ],
                ],
            ],

            'apprenant' => [
                'errorSelector' => '#gw_apprenant_error',
                'fields' => [
                    'gw_apprenant_civilite' => [
                        'required' => true,
                        'label' => 'Civilité',
                    ],
                    'gw_apprenant_prenom' => [
                        'required' => true,
                        'label' => 'Prénom',
                    ],
                    'gw_apprenant_nom' => [
                        'required' => true,
                        'label' => 'Nom',
                    ],
                    'gw_apprenant_nom_naissance' => [
                        'required' => true,
                        'label' => 'Nom de naissance',
                    ],
                    'gw_apprenant_date_naissance' => [
                        'required' => true,
                        'label' => 'Date de naissance',
                    ],
                    'gw_apprenant_telephone' => [
                        'required' => true,
                        'label' => 'Numéro de téléphone',
                        'pattern' => '[0-9]{2}( [0-9]{2}){4}',
                        'inputmode' => 'tel',
                    ],
                    'gw_apprenant_email' => [
                        'required' => true,
                        'label' => 'Adresse e-mail',
                        'type' => 'email',
                    ],
                    'gw_apprenant_adresse1' => [
                        'required' => true,
                        'label' => 'Adresse (ligne 1)',
                    ],
                    'gw_apprenant_cp' => [
                        'required' => true,
                        'label' => 'Code postal',
                        'pattern' => '[0-9]{5}',
                        'maxlength' => 5,
                        'inputmode' => 'numeric',
                    ],
                    'gw_apprenant_ville' => [
                        'required' => true,
                        'label' => 'Ville',
                    ],
                ],
            ],

            'responsable' => [
                'errorSelector' => '#gw_responsable_error',
                'messages' => [
                    'requiredOnly' => 'Merci de renseigner tous les champs obligatoires marqués d’une astérisque rouge (*).',
                    'groupOnly' => 'Merci de sélectionner le type de membre.',
                    'requiredAndGroup' => 'Merci de renseigner tous les champs obligatoires marqués d’une astérisque rouge (*) et sélectionner le type de membre.',
                ],
                'fields' => [
                    'gw_responsable_civilite' => [
                        'required' => true,
                        'label' => 'Civilité',
                    ],
                    'gw_responsable_prenom' => [
                        'required' => true,
                        'label' => 'Prénom',
                    ],
                    'gw_responsable_nom' => [
                        'required' => true,
                        'label' => 'Nom',
                    ],
                    'gw_responsable_fonction' => [
                        'required' => true,
                        'label' => 'Fonction',
                    ],
                    'gw_responsable_email' => [
                        'required' => true,
                        'label' => 'Adresse e-mail',
                        'type' => 'email',
                    ],
                    'gw_responsable_telephone' => [
                        'required' => true,
                        'label' => 'Numéro de téléphone',
                        'pattern' => '[0-9]{2}( [0-9]{2}){4}',
                        'inputmode' => 'tel',
                    ],
                    'gw_responsable_sous_traitant' => [
                        'required' => true,
                        'label' => 'Sous-traitant',
                    ],
                    'gw_responsable_nda_sous_traitant' => [
                        'requiredIf' => ['field' => 'gw_responsable_sous_traitant', 'equals' => 'Oui'],
                        'label' => 'NDA de l’organisme du sous-traitant',
                    ],
                    'gw_responsable_adresse_postale' => [
                        'required' => true,
                        'label' => 'Adresse (ligne 1)',
                    ],
                    'gw_responsable_rue' => [
                        'label' => 'Adresse (ligne 2)',
                    ],
                    'gw_responsable_code_postal' => [
                        'required' => true,
                        'label' => 'Code postal',
                        'pattern' => '[0-9]{5}',
                        'maxlength' => 5,
                        'inputmode' => 'numeric',
                    ],
                    'gw_responsable_ville' => [
                        'required' => true,
                        'label' => 'Ville',
                    ],
                ],
                'groups' => [
                    [
                        'type' => 'requiredRadio',
                        'name' => 'role_type',
                        'label' => 'Type de membre',
                    ],
                ],
            ],

            'client_contact_create' => [
                'errorSelector' => '#gw_client_contact_error',
                'messages' => [
                    'groupOnly' => 'Merci de renseigner au moins un numéro de téléphone.',
                    'requiredAndGroup' => 'Merci de renseigner tous les champs obligatoires et au moins un numéro de téléphone.',
                ],
                'fields' => [
                    'gw_client_contact_civilite' => [
                        'required' => true,
                        'label' => 'Civilité',
                        'invalidValues' => ['non_renseigne'],
                    ],
                    'gw_client_contact_fonction' => [
                        'required' => true,
                        'label' => 'Fonction',
                    ],
                    'gw_client_contact_nom' => [
                        'required' => true,
                        'label' => 'Nom',
                    ],
                    'gw_client_contact_prenom' => [
                        'required' => true,
                        'label' => 'Prénom',
                    ],
                    'gw_client_contact_mail' => [
                        'required' => true,
                        'label' => 'Mail',
                        'type' => 'email',
                    ],
                    'gw_client_contact_tel1' => [
                        'label' => 'Numéro de téléphone 1',
                        'pattern' => '[0-9]{2}( [0-9]{2}){4}',
                        'inputmode' => 'tel',
                    ],
                    'gw_client_contact_tel2' => [
                        'label' => 'Numéro de téléphone 2',
                        'pattern' => '[0-9]{2}( [0-9]{2}){4}',
                        'inputmode' => 'tel',
                    ],
                ],
                'groups' => [
                    [
                        'type' => 'atLeastOne',
                        'fields' => ['gw_client_contact_tel1', 'gw_client_contact_tel2'],
                        'label' => 'au moins un numéro de téléphone',
                    ],
                ],
            ],

            'client_contact_edit' => [
                'errorSelector' => '#gw_client_contact_edit_error',
                'messages' => [
                    'groupOnly' => 'Merci de renseigner au moins un numéro de téléphone.',
                    'requiredAndGroup' => 'Merci de renseigner tous les champs obligatoires et au moins un numéro de téléphone.',
                ],
                'fields' => [
                    'gw_client_contact_edit_civilite' => [
                        'required' => true,
                        'label' => 'Civilité',
                        'invalidValues' => ['non_renseigne'],
                    ],
                    'gw_client_contact_edit_fonction' => [
                        'required' => true,
                        'label' => 'Fonction',
                    ],
                    'gw_client_contact_edit_nom' => [
                        'required' => true,
                        'label' => 'Nom',
                    ],
                    'gw_client_contact_edit_prenom' => [
                        'required' => true,
                        'label' => 'Prénom',
                    ],
                    'gw_client_contact_edit_mail' => [
                        'required' => true,
                        'label' => 'Mail',
                        'type' => 'email',
                    ],
                    'gw_client_contact_edit_tel1' => [
                        'label' => 'Numéro de téléphone 1',
                        'pattern' => '[0-9]{2}( [0-9]{2}){4}',
                        'inputmode' => 'tel',
                    ],
                    'gw_client_contact_edit_tel2' => [
                        'label' => 'Numéro de téléphone 2',
                        'pattern' => '[0-9]{2}( [0-9]{2}){4}',
                        'inputmode' => 'tel',
                    ],
                ],
                'groups' => [
                    [
                        'type' => 'atLeastOne',
                        'fields' => ['gw_client_contact_edit_tel1', 'gw_client_contact_edit_tel2'],
                        'label' => 'au moins un numéro de téléphone',
                    ],
                ],
            ],
        ];
    }
}
