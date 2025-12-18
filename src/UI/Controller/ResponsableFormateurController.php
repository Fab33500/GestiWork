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
