<?php
/**
 * GestiWork ERP - Apprenant Controller
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

use GestiWork\Domain\Apprenant\ApprenantProvider;
use GestiWork\Domain\Tiers\TierProvider;

class ApprenantController
{
    public static function register(): void
    {
        add_action('init', [self::class, 'handlePosts']);
    }

    public static function handlePosts(): void
    {
        $viewVar = get_query_var('gw_view');
        if (is_string($viewVar) && $viewVar !== '') {
            $currentView = strtolower(trim($viewVar));
        } else {
            $currentView = isset($_GET['gw_view']) ? strtolower(trim((string) $_GET['gw_view'])) : '';
        }

        if (
            $_SERVER['REQUEST_METHOD'] !== 'POST'
            || !isset($_POST['gw_action'])
            || $currentView !== 'apprenant'
        ) {
            return;
        }

        $action = sanitize_text_field($_POST['gw_action']);

        switch ($action) {
            case 'gw_apprenant_create':
                self::handleCreate();
                break;
            case 'gw_apprenant_update':
                self::handleUpdate();
                break;
            case 'gw_apprenant_delete':
                self::handleDelete();
                break;
            case 'gw_apprenant_associer_entreprise':
                self::handleAssocierEntreprise();
                break;
        }
    }

    private static function handleCreate(): void
    {
        if (!wp_verify_nonce($_POST['gw_nonce'] ?? '', 'gw_apprenant_manage')) {
            wp_die(__('Erreur de sécurité. Veuillez réessayer.', 'gestiwork'));
        }

        if (!current_user_can('manage_options')) {
            wp_die(__('Vous n\'avez pas les permissions nécessaires.', 'gestiwork'));
        }

        $validationError = self::validateApprenantPayload($_POST);
        if ($validationError !== null) {
            $redirectUrl = add_query_arg([
                'gw_view' => 'apprenant',
                'mode' => 'create',
                'gw_error' => 'validation',
                'gw_error_msg' => $validationError,
            ], home_url('/gestiwork/'));

            wp_safe_redirect($redirectUrl);
            exit;
        }

        $email = sanitize_email($_POST['email'] ?? '');
        $existing = $email !== '' ? ApprenantProvider::getByEmail($email) : null;
        if (is_array($existing)) {
            $prenomExisting = isset($existing['prenom']) ? trim((string) $existing['prenom']) : '';
            $nomExisting = isset($existing['nom']) ? trim((string) $existing['nom']) : '';
            $labelExisting = trim($prenomExisting . ' ' . $nomExisting);
            if ($labelExisting === '') {
                $existingId = isset($existing['id']) ? (int) $existing['id'] : 0;
                $labelExisting = $existingId > 0 ? (string) $existingId : __('un apprenant existant', 'gestiwork');
            }

            $redirectUrl = add_query_arg([
                'gw_view' => 'apprenant',
                'mode' => 'create',
                'gw_error' => 'validation',
                'gw_error_msg' => sprintf('Cet e-mail est déjà utilisé par %s. L\'apprenant n\'a pas été créé.', $labelExisting),
            ], home_url('/gestiwork/'));

            wp_safe_redirect($redirectUrl);
            exit;
        }

        $tierAutoLinkId = null;
        $tierAutoLink = $email !== '' ? TierProvider::getClientParticulierByEmail($email) : null;
        if (!is_array($tierAutoLink) && $email !== '') {
            $tierAutoLink = TierProvider::getEntrepriseIndependantByEmail($email);
        }
        if (is_array($tierAutoLink)) {
            $tierId = (int) ($tierAutoLink['id'] ?? 0);
            if ($tierId > 0) {
                $tierAutoLinkId = $tierId;
            }
        }

        $data = [
            'civilite' => sanitize_text_field($_POST['civilite'] ?? ''),
            'prenom' => sanitize_text_field($_POST['prenom'] ?? ''),
            'nom' => sanitize_text_field($_POST['nom'] ?? ''),
            'nom_naissance' => sanitize_text_field($_POST['nom_naissance'] ?? ''),
            'date_naissance' => sanitize_text_field($_POST['date_naissance'] ?? '') ?: null,
            'email' => $email,
            'telephone' => sanitize_text_field($_POST['telephone'] ?? ''),
            'entreprise_id' => $tierAutoLinkId !== null ? $tierAutoLinkId : (!empty($_POST['entreprise_id']) ? (int) $_POST['entreprise_id'] : null),
            'origine' => sanitize_text_field($_POST['origine'] ?? ''),
            'statut_bpf' => sanitize_text_field($_POST['statut_bpf'] ?? ''),
            'adresse1' => sanitize_text_field($_POST['adresse1'] ?? ''),
            'adresse2' => sanitize_text_field($_POST['adresse2'] ?? ''),
            'cp' => sanitize_text_field($_POST['cp'] ?? ''),
            'ville' => sanitize_text_field($_POST['ville'] ?? ''),
        ];

        $apprenantId = ApprenantProvider::create($data);

        if ($apprenantId > 0) {
            $redirectUrl = add_query_arg([
                'gw_view' => 'apprenant',
                'gw_apprenant_id' => $apprenantId,
                'gw_saved' => '1',
            ], home_url('/gestiwork/'));
        } else {
            $redirectUrl = add_query_arg([
                'gw_view' => 'apprenant',
                'mode' => 'create',
                'gw_error' => '1',
            ], home_url('/gestiwork/'));
        }

        wp_redirect($redirectUrl);
        exit;
    }

    private static function validatePhone(?string $value): bool
    {
        $value = trim((string) $value);
        if ($value === '') {
            return false;
        }
        $digits = preg_replace('/\D+/', '', $value);
        return is_string($digits) && strlen($digits) === 10;
    }

    private static function validateCp(?string $value): bool
    {
        $value = trim((string) $value);
        return $value !== '' && preg_match('/^[0-9]{5}$/', $value) === 1;
    }

    private static function validateApprenantPayload(array $post): ?string
    {
        $civilite = isset($post['civilite']) ? trim((string) $post['civilite']) : '';
        $prenom = isset($post['prenom']) ? trim((string) $post['prenom']) : '';
        $nom = isset($post['nom']) ? trim((string) $post['nom']) : '';
        $nomNaissance = isset($post['nom_naissance']) ? trim((string) $post['nom_naissance']) : '';
        $dateNaissance = isset($post['date_naissance']) ? trim((string) $post['date_naissance']) : '';
        $email = isset($post['email']) ? sanitize_email((string) $post['email']) : '';
        $telephone = isset($post['telephone']) ? trim((string) $post['telephone']) : '';
        $adresse1 = isset($post['adresse1']) ? trim((string) $post['adresse1']) : '';
        $cp = isset($post['cp']) ? trim((string) $post['cp']) : '';
        $ville = isset($post['ville']) ? trim((string) $post['ville']) : '';

        if ($civilite === '') {
            return 'Merci de renseigner la civilité.';
        }

        if ($prenom === '' || $nom === '' || $nomNaissance === '') {
            return 'Merci de renseigner le prénom, le nom et le nom de naissance.';
        }

        if ($dateNaissance === '') {
            return 'Merci de renseigner la date de naissance.';
        }

        if ($email === '' || !is_email($email)) {
            return 'Merci de renseigner une adresse e-mail valide.';
        }

        if (!self::validatePhone($telephone)) {
            return 'Merci de renseigner un numéro de téléphone valide (10 chiffres).';
        }

        if ($adresse1 === '' || $ville === '') {
            return 'Merci de renseigner l\'adresse (ligne 1) et la ville.';
        }

        if (!self::validateCp($cp)) {
            return 'Merci de renseigner un code postal valide (5 chiffres).';
        }

        return null;
    }

    private static function handleUpdate(): void
    {
        if (!wp_verify_nonce($_POST['gw_nonce'] ?? '', 'gw_apprenant_manage')) {
            wp_die(__('Erreur de sécurité. Veuillez réessayer.', 'gestiwork'));
        }

        if (!current_user_can('manage_options')) {
            wp_die(__('Vous n\'avez pas les permissions nécessaires.', 'gestiwork'));
        }

        $apprenantId = (int) ($_POST['apprenant_id'] ?? 0);
        if ($apprenantId <= 0) {
            wp_die(__('ID de l\'apprenant manquant.', 'gestiwork'));
        }

        $validationError = self::validateApprenantPayload($_POST);
        if ($validationError !== null) {
            $redirectUrl = add_query_arg([
                'gw_view' => 'apprenant',
                'gw_apprenant_id' => $apprenantId,
                'mode' => 'edit',
                'gw_error' => 'validation',
                'gw_error_msg' => $validationError,
            ], home_url('/gestiwork/'));

            wp_safe_redirect($redirectUrl);
            exit;
        }

        $data = [
            'civilite' => sanitize_text_field($_POST['civilite'] ?? ''),
            'prenom' => sanitize_text_field($_POST['prenom'] ?? ''),
            'nom' => sanitize_text_field($_POST['nom'] ?? ''),
            'nom_naissance' => sanitize_text_field($_POST['nom_naissance'] ?? ''),
            'date_naissance' => sanitize_text_field($_POST['date_naissance'] ?? '') ?: null,
            'email' => sanitize_email($_POST['email'] ?? ''),
            'telephone' => sanitize_text_field($_POST['telephone'] ?? ''),
            'entreprise_id' => !empty($_POST['entreprise_id']) ? (int) $_POST['entreprise_id'] : null,
            'origine' => sanitize_text_field($_POST['origine'] ?? ''),
            'statut_bpf' => sanitize_text_field($_POST['statut_bpf'] ?? ''),
            'adresse1' => sanitize_text_field($_POST['adresse1'] ?? ''),
            'adresse2' => sanitize_text_field($_POST['adresse2'] ?? ''),
            'cp' => sanitize_text_field($_POST['cp'] ?? ''),
            'ville' => sanitize_text_field($_POST['ville'] ?? ''),
        ];

        $success = ApprenantProvider::update($apprenantId, $data);

        if ($success) {
            $redirectUrl = add_query_arg([
                'gw_view' => 'apprenant',
                'gw_apprenant_id' => $apprenantId,
                'gw_updated' => '1',
            ], home_url('/gestiwork/'));
        } else {
            $redirectUrl = add_query_arg([
                'gw_view' => 'apprenant',
                'gw_apprenant_id' => $apprenantId,
                'mode' => 'edit',
                'gw_error' => '1',
            ], home_url('/gestiwork/'));
        }

        wp_redirect($redirectUrl);
        exit;
    }

    private static function handleDelete(): void
    {
        if (!wp_verify_nonce($_POST['gw_nonce'] ?? '', 'gw_apprenant_delete')) {
            wp_die(__('Erreur de sécurité. Veuillez réessayer.', 'gestiwork'));
        }

        if (!current_user_can('manage_options')) {
            wp_die(__('Vous n\'avez pas les permissions nécessaires.', 'gestiwork'));
        }

        $apprenantId = (int) ($_POST['apprenant_id'] ?? 0);
        if ($apprenantId <= 0) {
            wp_die(__('ID de l\'apprenant manquant.', 'gestiwork'));
        }

        $success = ApprenantProvider::delete($apprenantId);

        if ($success) {
            $redirectUrl = add_query_arg([
                'gw_deleted' => '1',
            ], home_url('/gestiwork/apprenants/'));
        } else {
            $redirectUrl = add_query_arg([
                'gw_view' => 'apprenant',
                'gw_apprenant_id' => $apprenantId,
                'gw_error' => '1',
            ], home_url('/gestiwork/'));
        }

        wp_redirect($redirectUrl);
        exit;
    }

    private static function handleAssocierEntreprise(): void
    {
        if (!wp_verify_nonce($_POST['gw_nonce'] ?? '', 'gw_apprenant_associer_entreprise')) {
            wp_die(__('Erreur de sécurité. Veuillez réessayer.', 'gestiwork'));
        }

        if (!current_user_can('manage_options')) {
            wp_die(__('Vous n\'avez pas les permissions nécessaires.', 'gestiwork'));
        }

        $apprenantId = (int) ($_POST['apprenant_id'] ?? 0);
        $entrepriseId = (int) ($_POST['entreprise_id'] ?? 0);

        if ($apprenantId <= 0 || $entrepriseId <= 0) {
            wp_die(__('Données manquantes.', 'gestiwork'));
        }

        // @TODO: Vérifier que l'entreprise existe dans gw_tiers
        
        $success = ApprenantProvider::update($apprenantId, [
            'entreprise_id' => $entrepriseId
        ]);

        $redirectUrl = add_query_arg([
            'gw_view' => 'apprenant',
            'gw_apprenant_id' => $apprenantId,
            'gw_entreprise_associee' => $success ? '1' : '0',
        ], home_url('/gestiwork/'));

        wp_redirect($redirectUrl);
        exit;
    }
}
