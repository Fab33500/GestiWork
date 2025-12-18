<?php
/**
 * GestiWork ERP - Tiers Controller
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

namespace GestiWork\UI\Controller;

use GestiWork\Domain\Tiers\TierProvider;
use GestiWork\Domain\Tiers\TierContactProvider;
use GestiWork\Domain\Tiers\TierFinanceurProvider;

class TiersController
{
    public static function register(): void
    {
        add_action('init', [self::class, 'handlePost']);
    }

    public static function handlePost(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            return;
        }

        if (!is_user_logged_in() || !current_user_can('manage_options')) {
            return;
        }

        $action = isset($_POST['gw_action']) ? (string) $_POST['gw_action'] : '';
        $action = strtolower(trim($action));

        if ($action === 'gw_tier_create') {
            self::handleTierCreate();
        } elseif ($action === 'gw_tier_update') {
            self::handleTierUpdate();
        } elseif ($action === 'gw_tier_delete') {
            self::handleTierDelete();
        } elseif ($action === 'gw_tier_contact_create') {
            self::handleTierContactCreate();
        } elseif ($action === 'gw_tier_contact_update') {
            self::handleTierContactUpdate();
        } elseif ($action === 'gw_tier_contact_delete') {
            self::handleTierContactDelete();
        }
    }

    private static function handleTierCreate(): void
    {
        if (!isset($_POST['gw_nonce']) || !wp_verify_nonce((string) $_POST['gw_nonce'], 'gw_tier_create')) {
            self::redirectWithError('nonce');
            return;
        }

        $type = isset($_POST['type']) ? sanitize_text_field((string) $_POST['type']) : 'client_particulier';

        $validationError = self::validateTierPayload($type, $_POST);
        if ($validationError !== null) {
            self::redirectWithError('validation', null, $validationError);
            return;
        }

        $data = [
            'type' => $type,
            'statut' => isset($_POST['statut']) ? sanitize_text_field((string) $_POST['statut']) : 'client',
            'raison_sociale' => isset($_POST['raison_sociale']) ? sanitize_text_field((string) $_POST['raison_sociale']) : '',
            'nom' => isset($_POST['nom']) ? sanitize_text_field((string) $_POST['nom']) : '',
            'prenom' => isset($_POST['prenom']) ? sanitize_text_field((string) $_POST['prenom']) : '',
            'siret' => isset($_POST['siret']) ? sanitize_text_field((string) $_POST['siret']) : '',
            'forme_juridique' => isset($_POST['forme_juridique']) ? sanitize_text_field((string) $_POST['forme_juridique']) : '',
            'email' => isset($_POST['email']) ? sanitize_email((string) $_POST['email']) : '',
            'telephone' => isset($_POST['telephone']) ? sanitize_text_field((string) $_POST['telephone']) : '',
            'telephone_portable' => isset($_POST['telephone_portable']) ? sanitize_text_field((string) $_POST['telephone_portable']) : '',
            'adresse1' => isset($_POST['adresse1']) ? sanitize_text_field((string) $_POST['adresse1']) : '',
            'adresse2' => isset($_POST['adresse2']) ? sanitize_text_field((string) $_POST['adresse2']) : '',
            'cp' => isset($_POST['cp']) ? sanitize_text_field((string) $_POST['cp']) : '',
            'ville' => isset($_POST['ville']) ? sanitize_text_field((string) $_POST['ville']) : '',
        ];

        $newId = TierProvider::create($data);
        if ($newId > 0) {
            if ($type === 'financeur') {
                $entrepriseIds = isset($_POST['entreprise_ids']) && is_array($_POST['entreprise_ids']) ? array_map('intval', $_POST['entreprise_ids']) : [];
                TierFinanceurProvider::setEntreprisesForFinanceur($newId, $entrepriseIds);
            } elseif ($type === 'entreprise' || $type === 'client_entreprise') {
                $financeurIds = isset($_POST['financeur_ids']) && is_array($_POST['financeur_ids']) ? array_map('intval', $_POST['financeur_ids']) : [];
                TierFinanceurProvider::setFinanceursForEntreprise($newId, $financeurIds);
            }

            $redirectUrl = add_query_arg([
                'gw_view' => 'Client',
                'gw_tier_id' => $newId,
                'gw_notice' => 'tier_created',
            ], home_url('/gestiwork/'));
            wp_safe_redirect($redirectUrl);
            exit;
        }

        self::redirectWithError('create_failed');
    }

    private static function handleTierUpdate(): void
    {
        if (!isset($_POST['gw_nonce']) || !wp_verify_nonce((string) $_POST['gw_nonce'], 'gw_tier_update')) {
            self::redirectWithError('nonce');
            return;
        }

        $tierId = isset($_POST['tier_id']) ? (int) $_POST['tier_id'] : 0;
        if ($tierId <= 0) {
            self::redirectWithError('invalid_id');
            return;
        }

        $type = isset($_POST['type']) ? sanitize_text_field((string) $_POST['type']) : 'client_particulier';

        $validationError = self::validateTierPayload($type, $_POST);
        if ($validationError !== null) {
            self::redirectWithError('validation', $tierId, $validationError);
            return;
        }

        $data = [
            'type' => $type,
            'statut' => isset($_POST['statut']) ? sanitize_text_field((string) $_POST['statut']) : 'client',
            'raison_sociale' => isset($_POST['raison_sociale']) ? sanitize_text_field((string) $_POST['raison_sociale']) : '',
            'nom' => isset($_POST['nom']) ? sanitize_text_field((string) $_POST['nom']) : '',
            'prenom' => isset($_POST['prenom']) ? sanitize_text_field((string) $_POST['prenom']) : '',
            'siret' => isset($_POST['siret']) ? sanitize_text_field((string) $_POST['siret']) : '',
            'forme_juridique' => isset($_POST['forme_juridique']) ? sanitize_text_field((string) $_POST['forme_juridique']) : '',
            'email' => isset($_POST['email']) ? sanitize_email((string) $_POST['email']) : '',
            'telephone' => isset($_POST['telephone']) ? sanitize_text_field((string) $_POST['telephone']) : '',
            'telephone_portable' => isset($_POST['telephone_portable']) ? sanitize_text_field((string) $_POST['telephone_portable']) : '',
            'adresse1' => isset($_POST['adresse1']) ? sanitize_text_field((string) $_POST['adresse1']) : '',
            'adresse2' => isset($_POST['adresse2']) ? sanitize_text_field((string) $_POST['adresse2']) : '',
            'cp' => isset($_POST['cp']) ? sanitize_text_field((string) $_POST['cp']) : '',
            'ville' => isset($_POST['ville']) ? sanitize_text_field((string) $_POST['ville']) : '',
        ];

        $ok = TierProvider::update($tierId, $data);
        if ($ok) {
            TierFinanceurProvider::deleteLinksByTierId($tierId);

            if ($type === 'financeur') {
                $entrepriseIds = isset($_POST['entreprise_ids']) && is_array($_POST['entreprise_ids']) ? array_map('intval', $_POST['entreprise_ids']) : [];
                TierFinanceurProvider::setEntreprisesForFinanceur($tierId, $entrepriseIds);
            } elseif ($type === 'entreprise' || $type === 'client_entreprise') {
                $financeurIds = isset($_POST['financeur_ids']) && is_array($_POST['financeur_ids']) ? array_map('intval', $_POST['financeur_ids']) : [];
                TierFinanceurProvider::setFinanceursForEntreprise($tierId, $financeurIds);
            }

            $redirectUrl = add_query_arg([
                'gw_view' => 'Client',
                'gw_tier_id' => $tierId,
                'gw_notice' => 'tier_updated',
            ], home_url('/gestiwork/'));
            wp_safe_redirect($redirectUrl);
            exit;
        }

        self::redirectWithError('update_failed', $tierId);
    }

    private static function handleTierDelete(): void
    {
        if (!isset($_POST['gw_nonce']) || !wp_verify_nonce((string) $_POST['gw_nonce'], 'gw_tier_delete')) {
            self::redirectWithError('nonce');
            return;
        }

        $tierId = isset($_POST['tier_id']) ? (int) $_POST['tier_id'] : 0;
        if ($tierId <= 0) {
            self::redirectWithError('invalid_id');
            return;
        }

        $existing = TierProvider::getById($tierId);
        if (!is_array($existing)) {
            self::redirectWithError('invalid_id');
            return;
        }

        $contactsDeletedOk = TierContactProvider::deleteByTierId($tierId);
        if (!$contactsDeletedOk) {
            self::redirectWithError('tier_delete_failed', $tierId);
            return;
        }

        TierFinanceurProvider::deleteLinksByTierId($tierId);

        $tierDeletedOk = TierProvider::delete($tierId);
        if ($tierDeletedOk) {
            $redirectUrl = home_url('/gestiwork/Tiers/');
            wp_safe_redirect($redirectUrl);
            exit;
        }

        self::redirectWithError('tier_delete_failed', $tierId);
    }

    private static function handleTierContactCreate(): void
    {
        if (!isset($_POST['gw_nonce']) || !wp_verify_nonce((string) $_POST['gw_nonce'], 'gw_tier_contact_manage')) {
            self::redirectWithError('nonce');
            return;
        }

        $tierId = isset($_POST['tier_id']) ? (int) $_POST['tier_id'] : 0;
        if ($tierId <= 0) {
            self::redirectWithError('invalid_id');
            return;
        }

        $validationError = self::validateTierContactPayload($_POST);
        if ($validationError !== null) {
            self::redirectWithError('validation', $tierId, $validationError);
            return;
        }

        $data = [
            'civilite' => isset($_POST['civilite']) ? sanitize_text_field((string) $_POST['civilite']) : 'non_renseigne',
            'fonction' => isset($_POST['fonction']) ? sanitize_text_field((string) $_POST['fonction']) : '',
            'nom' => isset($_POST['nom']) ? sanitize_text_field((string) $_POST['nom']) : '',
            'prenom' => isset($_POST['prenom']) ? sanitize_text_field((string) $_POST['prenom']) : '',
            'mail' => isset($_POST['mail']) ? sanitize_email((string) $_POST['mail']) : '',
            'tel1' => isset($_POST['tel1']) ? sanitize_text_field((string) $_POST['tel1']) : '',
            'tel2' => isset($_POST['tel2']) ? sanitize_text_field((string) $_POST['tel2']) : '',
        ];

        $newContactId = TierContactProvider::create($tierId, $data);
        if ($newContactId > 0) {
            $redirectUrl = add_query_arg([
                'gw_view' => 'Client',
                'gw_tier_id' => $tierId,
                'gw_notice' => 'contact_created',
            ], home_url('/gestiwork/'));
            wp_safe_redirect($redirectUrl);
            exit;
        }

        self::redirectWithError('contact_create_failed', $tierId);
    }

    private static function handleTierContactUpdate(): void
    {
        if (!isset($_POST['gw_nonce']) || !wp_verify_nonce((string) $_POST['gw_nonce'], 'gw_tier_contact_manage')) {
            self::redirectWithError('nonce');
            return;
        }

        $tierId = isset($_POST['tier_id']) ? (int) $_POST['tier_id'] : 0;
        if ($tierId <= 0) {
            self::redirectWithError('invalid_id');
            return;
        }

        $contactId = isset($_POST['contact_id']) ? (int) $_POST['contact_id'] : 0;
        if ($contactId <= 0) {
            self::redirectWithError('invalid_id', $tierId);
            return;
        }

        $existing = TierContactProvider::getById($contactId);
        if (!is_array($existing) || (int) ($existing['tier_id'] ?? 0) !== $tierId) {
            self::redirectWithError('invalid_id', $tierId);
            return;
        }

        $validationError = self::validateTierContactPayload($_POST);
        if ($validationError !== null) {
            self::redirectWithError('validation', $tierId, $validationError);
            return;
        }

        $data = [
            'civilite' => isset($_POST['civilite']) ? sanitize_text_field((string) $_POST['civilite']) : 'non_renseigne',
            'fonction' => isset($_POST['fonction']) ? sanitize_text_field((string) $_POST['fonction']) : '',
            'nom' => isset($_POST['nom']) ? sanitize_text_field((string) $_POST['nom']) : '',
            'prenom' => isset($_POST['prenom']) ? sanitize_text_field((string) $_POST['prenom']) : '',
            'mail' => isset($_POST['mail']) ? sanitize_email((string) $_POST['mail']) : '',
            'tel1' => isset($_POST['tel1']) ? sanitize_text_field((string) $_POST['tel1']) : '',
            'tel2' => isset($_POST['tel2']) ? sanitize_text_field((string) $_POST['tel2']) : '',
        ];

        $ok = TierContactProvider::update($contactId, $data);
        if ($ok) {
            $redirectUrl = add_query_arg([
                'gw_view' => 'Client',
                'gw_tier_id' => $tierId,
                'gw_notice' => 'contact_updated',
            ], home_url('/gestiwork/'));
            wp_safe_redirect($redirectUrl);
            exit;
        }

        self::redirectWithError('contact_update_failed', $tierId);
    }

    private static function handleTierContactDelete(): void
    {
        if (!isset($_POST['gw_nonce']) || !wp_verify_nonce((string) $_POST['gw_nonce'], 'gw_tier_contact_manage')) {
            self::redirectWithError('nonce');
            return;
        }

        $tierId = isset($_POST['tier_id']) ? (int) $_POST['tier_id'] : 0;
        if ($tierId <= 0) {
            self::redirectWithError('invalid_id');
            return;
        }

        $contactId = isset($_POST['contact_id']) ? (int) $_POST['contact_id'] : 0;
        if ($contactId <= 0) {
            self::redirectWithError('invalid_id', $tierId);
            return;
        }

        $existing = TierContactProvider::getById($contactId);
        if (!is_array($existing) || (int) ($existing['tier_id'] ?? 0) !== $tierId) {
            self::redirectWithError('invalid_id', $tierId);
            return;
        }

        $ok = TierContactProvider::delete($contactId);
        if ($ok) {
            $redirectUrl = add_query_arg([
                'gw_view' => 'Client',
                'gw_tier_id' => $tierId,
                'gw_notice' => 'contact_deleted',
            ], home_url('/gestiwork/'));
            wp_safe_redirect($redirectUrl);
            exit;
        }

        self::redirectWithError('contact_delete_failed', $tierId);
    }

    private static function validatePhone(?string $value): bool
    {
        $value = trim((string) $value);
        if ($value === '') {
            return true;
        }
        $digits = preg_replace('/\D+/', '', $value);
        return is_string($digits) && strlen($digits) === 10;
    }

    private static function validateSiretOrSiren(?string $value): bool
    {
        $value = trim((string) $value);
        if ($value === '') {
            return true;
        }
        $digits = preg_replace('/\D+/', '', $value);
        if (!is_string($digits)) {
            return false;
        }
        $len = strlen($digits);
        return $len === 9 || $len === 14;
    }

    private static function validateCp(?string $value): bool
    {
        $value = trim((string) $value);
        return $value !== '' && preg_match('/^[0-9]{5}$/', $value) === 1;
    }

    private static function validateTierPayload(string $type, array $post): ?string
    {
        $type = strtolower(trim($type));

        $isParticulier = ($type === 'client_particulier');

        $nom = isset($post['nom']) ? trim((string) $post['nom']) : '';
        $prenom = isset($post['prenom']) ? trim((string) $post['prenom']) : '';
        $raisonSociale = isset($post['raison_sociale']) ? trim((string) $post['raison_sociale']) : '';
        $siret = isset($post['siret']) ? trim((string) $post['siret']) : '';

        $email = isset($post['email']) ? sanitize_email((string) $post['email']) : '';
        $tel = isset($post['telephone']) ? trim((string) $post['telephone']) : '';
        $telMobile = isset($post['telephone_portable']) ? trim((string) $post['telephone_portable']) : '';
        $adresse1 = isset($post['adresse1']) ? trim((string) $post['adresse1']) : '';
        $cp = isset($post['cp']) ? trim((string) $post['cp']) : '';
        $ville = isset($post['ville']) ? trim((string) $post['ville']) : '';

        if ($isParticulier) {
            if ($nom === '' || $prenom === '') {
                return 'Merci de renseigner le nom et le prénom.';
            }
        } else {
            if ($raisonSociale === '' || $siret === '') {
                return 'Merci de renseigner la raison sociale et le SIRET/SIREN.';
            }
        }

        if ($email === '' || !is_email($email)) {
            return 'Merci de renseigner une adresse e-mail valide.';
        }

        if ($adresse1 === '' || $ville === '') {
            return 'Merci de renseigner l\'adresse (ligne 1) et la ville.';
        }

        if (!self::validateCp($cp)) {
            return 'Merci de renseigner un code postal valide (5 chiffres).';
        }

        if ($tel === '' && $telMobile === '') {
            return 'Merci de renseigner au moins un numéro de téléphone (fixe ou portable).';
        }

        if (!self::validatePhone($tel) || !self::validatePhone($telMobile)) {
            return 'Merci de renseigner un numéro de téléphone valide (10 chiffres).';
        }

        if (!$isParticulier && !self::validateSiretOrSiren($siret)) {
            return 'Merci de renseigner un SIRET/SIREN valide (9 ou 14 chiffres).';
        }

        return null;
    }

    private static function validateTierContactPayload(array $post): ?string
    {
        $civilite = isset($post['civilite']) ? trim((string) $post['civilite']) : 'non_renseigne';
        $fonction = isset($post['fonction']) ? trim((string) $post['fonction']) : '';
        $nom = isset($post['nom']) ? trim((string) $post['nom']) : '';
        $prenom = isset($post['prenom']) ? trim((string) $post['prenom']) : '';
        $mail = isset($post['mail']) ? sanitize_email((string) $post['mail']) : '';
        $tel1 = isset($post['tel1']) ? trim((string) $post['tel1']) : '';
        $tel2 = isset($post['tel2']) ? trim((string) $post['tel2']) : '';

        if ($civilite === '' || $civilite === 'non_renseigne') {
            return 'Merci de renseigner la civilité.';
        }

        if ($fonction === '' || $nom === '' || $prenom === '') {
            return 'Merci de renseigner la fonction, le nom et le prénom.';
        }

        if ($mail === '' || !is_email($mail)) {
            return 'Merci de renseigner une adresse e-mail valide.';
        }

        if ($tel1 === '' && $tel2 === '') {
            return 'Merci de renseigner au moins un numéro de téléphone.';
        }

        if (!self::validatePhone($tel1) || !self::validatePhone($tel2)) {
            return 'Merci de renseigner un numéro de téléphone valide (10 chiffres).';
        }

        return null;
    }

    private static function redirectWithError(string $errorType, ?int $tierId = null, ?string $errorMessage = null): void
    {
        $args = ['gw_error' => $errorType];

        if ($errorMessage !== null && $errorMessage !== '') {
            $args['gw_error_msg'] = $errorMessage;
        }
        
        if ($tierId && $tierId > 0) {
            $args['gw_view'] = 'Client';
            $args['gw_tier_id'] = $tierId;
        } else {
            $args['gw_view'] = 'Client';
            $args['mode'] = 'create';
        }

        $redirectUrl = add_query_arg($args, home_url('/gestiwork/'));
        wp_safe_redirect($redirectUrl);
        exit;
    }
}
