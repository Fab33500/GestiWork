<?php
/**
 * GestiWork ERP - ResponsableFormateur Controller
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

use GestiWork\Domain\ResponsableFormateur\ResponsableFormateurProvider;
use GestiWork\Domain\ResponsableFormateur\FormateurCompetenceProvider;

class ResponsableFormateurController
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
            || $currentView !== 'responsable'
        ) {
            return;
        }

        $action = sanitize_text_field($_POST['gw_action']);

        switch ($action) {
            case 'gw_formateur_create':
                self::handleCreate();
                break;
            case 'gw_formateur_update':
                self::handleUpdate();
                break;
            case 'gw_formateur_delete':
                self::handleDelete();
                break;
            case 'gw_formateur_competences':
                self::handleCompetences();
                break;
            case 'gw_formateur_cout':
                self::handleCout();
                break;
        }
    }

    private static function handleCreate(): void
    {
        if (!wp_verify_nonce($_POST['gw_nonce'] ?? '', 'gw_formateur_manage')) {
            wp_die(__('Erreur de sécurité. Veuillez réessayer.', 'gestiwork'));
        }

        if (!current_user_can('manage_options')) {
            wp_die(__('Vous n\'avez pas les permissions nécessaires.', 'gestiwork'));
        }

        $validationError = self::validateResponsablePayload($_POST);
        if ($validationError !== null) {
            $redirectUrl = add_query_arg([
                'gw_view' => 'responsable',
                'mode' => 'create',
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
            'fonction' => sanitize_text_field($_POST['fonction'] ?? ''),
            'email' => sanitize_email($_POST['email'] ?? ''),
            'telephone' => sanitize_text_field($_POST['telephone'] ?? ''),
            'role_type' => sanitize_text_field($_POST['role_type'] ?? ''),
            'sous_traitant' => sanitize_text_field($_POST['sous_traitant'] ?? 'Non'),
            'nda_sous_traitant' => sanitize_text_field($_POST['nda_sous_traitant'] ?? ''),
            'adresse_postale' => sanitize_text_field($_POST['adresse_postale'] ?? ''),
            'rue' => sanitize_text_field($_POST['rue'] ?? ''),
            'code_postal' => sanitize_text_field($_POST['code_postal'] ?? ''),
            'ville' => sanitize_text_field($_POST['ville'] ?? ''),
        ];

        $responsableId = ResponsableFormateurProvider::create($data);

        if ($responsableId > 0) {
            $competencesRaw = sanitize_textarea_field($_POST['competences'] ?? '');
            $competences = array_map('trim', explode(',', $competencesRaw));
            $competences = array_filter($competences);

            if (!empty($competences)) {
                FormateurCompetenceProvider::saveCompetences($responsableId, $competences);
            }

            $couts = [
                'cout_jour_ht' => !empty($_POST['cout_jour_ht']) ? (float) $_POST['cout_jour_ht'] : null,
                'cout_heure_ht' => !empty($_POST['cout_heure_ht']) ? (float) $_POST['cout_heure_ht'] : null,
                'heures_par_jour' => !empty($_POST['heures_par_jour']) ? (float) $_POST['heures_par_jour'] : 7.00,
                'tva_rate' => isset($_POST['tva_rate']) ? (float) $_POST['tva_rate'] : 0.00,
            ];

            if ($couts['cout_jour_ht'] !== null || $couts['cout_heure_ht'] !== null) {
                FormateurCompetenceProvider::saveCouts($responsableId, $couts);
            }
        }

        if ($responsableId > 0) {
            $redirectUrl = add_query_arg([
                'gw_view' => 'responsable',
                'gw_responsable_id' => $responsableId,
                'gw_saved' => '1',
            ], home_url('/gestiwork/'));
        } else {
            $redirectUrl = add_query_arg([
                'gw_view' => 'responsable',
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

    private static function validateResponsablePayload(array $post): ?string
    {
        $roleType = isset($post['role_type']) ? trim((string) $post['role_type']) : '';
        $civilite = isset($post['civilite']) ? trim((string) $post['civilite']) : '';
        $prenom = isset($post['prenom']) ? trim((string) $post['prenom']) : '';
        $nom = isset($post['nom']) ? trim((string) $post['nom']) : '';
        $fonction = isset($post['fonction']) ? trim((string) $post['fonction']) : '';
        $email = isset($post['email']) ? sanitize_email((string) $post['email']) : '';
        $telephone = isset($post['telephone']) ? trim((string) $post['telephone']) : '';
        $sousTraitant = isset($post['sous_traitant']) ? trim((string) $post['sous_traitant']) : '';
        $nda = isset($post['nda_sous_traitant']) ? trim((string) $post['nda_sous_traitant']) : '';
        $adressePostale = isset($post['adresse_postale']) ? trim((string) $post['adresse_postale']) : '';
        $rue = isset($post['rue']) ? trim((string) $post['rue']) : '';
        $cp = isset($post['code_postal']) ? trim((string) $post['code_postal']) : '';
        $ville = isset($post['ville']) ? trim((string) $post['ville']) : '';

        if ($roleType === '') {
            return 'Merci de sélectionner le type de membre.';
        }

        if ($civilite === '' || $prenom === '' || $nom === '' || $fonction === '') {
            return 'Merci de renseigner la civilité, le prénom, le nom et la fonction.';
        }

        if ($email === '' || !is_email($email)) {
            return 'Merci de renseigner une adresse e-mail valide.';
        }

        if (!self::validatePhone($telephone)) {
            return 'Merci de renseigner un numéro de téléphone valide (10 chiffres).';
        }

        if ($sousTraitant !== 'Oui' && $sousTraitant !== 'Non') {
            return 'Merci de sélectionner si la personne est sous-traitante.';
        }

        if ($sousTraitant === 'Oui' && $nda === '') {
            return 'Merci de renseigner le NDA de l’organisme du sous-traitant.';
        }

        if ($adressePostale === '' || $ville === '') {
            return 'Merci de renseigner l\'adresse (ligne 1) et la ville.';
        }

        if (!self::validateCp($cp)) {
            return 'Merci de renseigner un code postal valide (5 chiffres).';
        }

        return null;
    }

    private static function handleUpdate(): void
    {
        if (!wp_verify_nonce($_POST['gw_nonce'] ?? '', 'gw_formateur_manage')) {
            wp_die(__('Erreur de sécurité. Veuillez réessayer.', 'gestiwork'));
        }

        if (!current_user_can('manage_options')) {
            wp_die(__('Vous n\'avez pas les permissions nécessaires.', 'gestiwork'));
        }

        $responsableId = (int) ($_POST['responsable_id'] ?? 0);
        if ($responsableId <= 0) {
            wp_die(__('ID du responsable manquant.', 'gestiwork'));
        }

        $validationError = self::validateResponsablePayload($_POST);
        if ($validationError !== null) {
            $redirectUrl = add_query_arg([
                'gw_view' => 'responsable',
                'gw_responsable_id' => $responsableId,
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
            'fonction' => sanitize_text_field($_POST['fonction'] ?? ''),
            'email' => sanitize_email($_POST['email'] ?? ''),
            'telephone' => sanitize_text_field($_POST['telephone'] ?? ''),
            'role_type' => sanitize_text_field($_POST['role_type'] ?? ''),
            'sous_traitant' => sanitize_text_field($_POST['sous_traitant'] ?? 'Non'),
            'nda_sous_traitant' => sanitize_text_field($_POST['nda_sous_traitant'] ?? ''),
            'adresse_postale' => sanitize_text_field($_POST['adresse_postale'] ?? ''),
            'rue' => sanitize_text_field($_POST['rue'] ?? ''),
            'code_postal' => sanitize_text_field($_POST['code_postal'] ?? ''),
            'ville' => sanitize_text_field($_POST['ville'] ?? ''),
        ];

        $success = ResponsableFormateurProvider::update($responsableId, $data);

        if ($success) {
            $redirectUrl = add_query_arg([
                'gw_view' => 'responsable',
                'gw_responsable_id' => $responsableId,
                'gw_updated' => '1',
            ], home_url('/gestiwork/'));
        } else {
            $redirectUrl = add_query_arg([
                'gw_view' => 'responsable',
                'gw_responsable_id' => $responsableId,
                'mode' => 'edit',
                'gw_error' => '1',
            ], home_url('/gestiwork/'));
        }

        wp_redirect($redirectUrl);
        exit;
    }

    private static function handleDelete(): void
    {
        if (!wp_verify_nonce($_POST['gw_nonce'] ?? '', 'gw_formateur_delete')) {
            wp_die(__('Erreur de sécurité. Veuillez réessayer.', 'gestiwork'));
        }

        if (!current_user_can('manage_options')) {
            wp_die(__('Vous n\'avez pas les permissions nécessaires.', 'gestiwork'));
        }

        $responsableId = (int) ($_POST['responsable_id'] ?? 0);
        if ($responsableId <= 0) {
            wp_die(__('ID du responsable manquant.', 'gestiwork'));
        }

        $competencesDeleted = FormateurCompetenceProvider::deleteByFormateurId($responsableId);
        $responsableDeleted = false;
        if ($competencesDeleted) {
            $responsableDeleted = ResponsableFormateurProvider::delete($responsableId);
        }

        if ($competencesDeleted && $responsableDeleted) {
            $redirectUrl = add_query_arg([
                'gw_deleted' => '1',
            ], home_url('/gestiwork/equipe-pedagogique/'));
        } else {
            $redirectUrl = add_query_arg([
                'gw_view' => 'responsable',
                'gw_responsable_id' => $responsableId,
                'gw_error' => '1',
            ], home_url('/gestiwork/'));
        }

        wp_redirect($redirectUrl);
        exit;
    }

    private static function handleCompetences(): void
    {
        if (!wp_verify_nonce($_POST['gw_nonce'] ?? '', 'gw_formateur_competences')) {
            wp_die(__('Erreur de sécurité. Veuillez réessayer.', 'gestiwork'));
        }

        if (!current_user_can('manage_options')) {
            wp_die(__('Vous n\'avez pas les permissions nécessaires.', 'gestiwork'));
        }

        $responsableId = (int) ($_POST['responsable_id'] ?? 0);
        $competencesRaw = sanitize_textarea_field($_POST['competences'] ?? '');

        if ($responsableId <= 0) {
            wp_die(__('ID du responsable manquant.', 'gestiwork'));
        }

        // Séparer les compétences par virgule et nettoyer
        $competences = array_map('trim', explode(',', $competencesRaw));
        $competences = array_filter($competences); // Supprimer les éléments vides

        FormateurCompetenceProvider::saveCompetences($responsableId, $competences);

        $redirectUrl = add_query_arg([
            'gw_view' => 'responsable',
            'gw_responsable_id' => $responsableId,
            'gw_competences_saved' => '1',
        ], home_url('/gestiwork/'));

        wp_redirect($redirectUrl);
        exit;
    }

    private static function handleCout(): void
    {
        if (!wp_verify_nonce($_POST['gw_nonce'] ?? '', 'gw_formateur_cout')) {
            wp_die(__('Erreur de sécurité. Veuillez réessayer.', 'gestiwork'));
        }

        if (!current_user_can('manage_options')) {
            wp_die(__('Vous n\'avez pas les permissions nécessaires.', 'gestiwork'));
        }

        $responsableId = (int) ($_POST['responsable_id'] ?? 0);

        if ($responsableId <= 0) {
            wp_die(__('ID du responsable manquant.', 'gestiwork'));
        }

        $couts = [
            'cout_jour_ht' => !empty($_POST['cout_jour_ht']) ? (float) $_POST['cout_jour_ht'] : null,
            'cout_heure_ht' => !empty($_POST['cout_heure_ht']) ? (float) $_POST['cout_heure_ht'] : null,
            'heures_par_jour' => !empty($_POST['heures_par_jour']) ? (float) $_POST['heures_par_jour'] : 7.00,
            'tva_rate' => isset($_POST['tva_rate']) ? (float) $_POST['tva_rate'] : 0.00,
        ];

        FormateurCompetenceProvider::saveCouts($responsableId, $couts);

        $redirectUrl = add_query_arg([
            'gw_view' => 'responsable',
            'gw_responsable_id' => $responsableId,
            'gw_cout_saved' => '1',
        ], home_url('/gestiwork/'));

        wp_redirect($redirectUrl);
        exit;
    }
}
