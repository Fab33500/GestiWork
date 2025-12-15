<?php
/**
 * GestiWork ERP - Vue Tiers (clients / financeurs / OF donneur d'ordre)
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

use GestiWork\Domain\Tiers\TierProvider;

if (! current_user_can('manage_options')) {
    // Par sécurité, on ne montre rien aux non-admins.
    wp_die(esc_html__('Accès non autorisé.', 'gestiwork'), 403);
}

$requestQuery = isset($_GET['gw_tiers_query']) ? sanitize_text_field(wp_unslash((string) $_GET['gw_tiers_query'])) : '';
$requestType = isset($_GET['gw_tiers_type']) ? sanitize_text_field(wp_unslash((string) $_GET['gw_tiers_type'])) : '';
$requestStatut = isset($_GET['gw_tiers_statut']) ? sanitize_text_field(wp_unslash((string) $_GET['gw_tiers_statut'])) : '';
$requestVille = isset($_GET['gw_tiers_ville']) ? sanitize_text_field(wp_unslash((string) $_GET['gw_tiers_ville'])) : '';
$requestPage = isset($_GET['gw_tiers_page']) ? (int) $_GET['gw_tiers_page'] : 1;

$tiersFilters = [
    'query' => $requestQuery,
    'type' => $requestType,
    'statut' => $requestStatut,
    'ville' => $requestVille,
];

$tiersSearchResult = TierProvider::search($tiersFilters, max(1, $requestPage), 15);
$tiersItems = $tiersSearchResult['items'] ?? [];
$hasDbTiers = is_array($tiersItems) && count($tiersItems) > 0;
$tiersTotal = isset($tiersSearchResult['total']) ? (int) $tiersSearchResult['total'] : 0;
$tiersPage = isset($tiersSearchResult['page']) ? (int) $tiersSearchResult['page'] : 1;
$tiersPageSize = isset($tiersSearchResult['page_size']) ? (int) $tiersSearchResult['page_size'] : 15;
$tiersTotalPages = $tiersPageSize > 0 ? (int) ceil($tiersTotal / $tiersPageSize) : 1;

$currentResetUrl = home_url('/gestiwork/Tiers/');
?>

<section class="gw-section gw-section-dashboard">
    <h2 class="gw-section-title"><?php esc_html_e('Tiers (Entreprises,  particuliers, financeurs, OF donneurs d\'ordre)', 'gestiwork'); ?></h2>
    <p class="gw-section-description">
        <?php esc_html_e(
            'Cet écran regroupera à terme tous vos tiers : entreprises clientes, financeurs, et organismes donneurs d\'ordre.',
            'gestiwork'
        ); ?>
    </p>

    <div class="gw-settings-group">
        <h3 class="gw-section-subtitle"><?php esc_html_e('Nouveau tiers', 'gestiwork'); ?></h3>
        <p class="gw-section-description">
            <?php esc_html_e(
                'Ce formulaire sert de maquette pour la création d’un tiers (client particulier, entreprise, financeur ou organisme donneur d\'ordre). La logique d’enregistrement sera branchée ultérieurement.',
                'gestiwork'
            ); ?>
        </p>

        <a class="gw-button gw-button--secondary" href="<?php echo esc_url(add_query_arg(['gw_view' => 'Client', 'mode' => 'create'], home_url('/gestiwork/'))); ?>">
            <?php esc_html_e('Créer un nouveau tiers', 'gestiwork'); ?>
        </a>
    </div>

    <div class="gw-settings-group">
        <h3 class="gw-section-subtitle"><?php esc_html_e('Vue d’ensemble des tiers', 'gestiwork'); ?></h3>
        <p class="gw-section-description">
            <?php esc_html_e(
                'Pour l’instant, cette page présente un exemple de mise en forme. La prochaine étape consistera à brancher ce tableau sur la base de données (table gw_tiers).',
                'gestiwork'
            ); ?>
        </p>

        <div class="gw-settings-grid">
            <div class="gw-settings-field" style="grid-column: 1 / -1;">
                <p class="gw-settings-label"><?php esc_html_e('Recherche avancée', 'gestiwork'); ?></p>
                <form method="get" action="" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 10px; align-items: end;">
                    <div>
                        <label class="gw-settings-placeholder" for="gw_tiers_search_query"><?php esc_html_e('Recherche (nom, e-mail, téléphone...)', 'gestiwork'); ?></label>
                        <input type="text" id="gw_tiers_search_query" name="gw_tiers_query" class="gw-modal-input" value="<?php echo esc_attr($requestQuery); ?>" placeholder="Entreprise, Dupont..." />
                    </div>
                    <div>
                        <label class="gw-settings-placeholder" for="gw_tiers_search_type"><?php esc_html_e('Type de tiers', 'gestiwork'); ?></label>
                        <select id="gw_tiers_search_type" name="gw_tiers_type" class="gw-modal-input">
                            <option value="" <?php selected($requestType, ''); ?>><?php esc_html_e('Tous', 'gestiwork'); ?></option>
                            <option value="client_particulier" <?php selected($requestType, 'client_particulier'); ?>><?php esc_html_e('Particulier', 'gestiwork'); ?></option>
                            <option value="entreprise" <?php selected($requestType, 'entreprise'); ?>><?php esc_html_e('Entreprise', 'gestiwork'); ?></option>
                            <option value="financeur" <?php selected($requestType, 'financeur'); ?>><?php esc_html_e('Financeur / OPCO', 'gestiwork'); ?></option>
                            <option value="of_donneur_ordre" <?php selected($requestType, 'of_donneur_ordre'); ?>><?php esc_html_e('OF donneur d\'ordre', 'gestiwork'); ?></option>
                        </select>
                    </div>
                    <div>
                        <label class="gw-settings-placeholder" for="gw_tiers_search_status"><?php esc_html_e('Statut', 'gestiwork'); ?></label>
                        <select id="gw_tiers_search_status" name="gw_tiers_statut" class="gw-modal-input">
                            <option value="" <?php selected($requestStatut, ''); ?>><?php esc_html_e('Tous', 'gestiwork'); ?></option>
                            <option value="prospect" <?php selected($requestStatut, 'prospect'); ?>><?php esc_html_e('Prospect', 'gestiwork'); ?></option>
                            <option value="client" <?php selected($requestStatut, 'client'); ?>><?php esc_html_e('Client', 'gestiwork'); ?></option>
                        </select>
                    </div>
                    <div>
                        <label class="gw-settings-placeholder" for="gw_tiers_search_city"><?php esc_html_e('Ville', 'gestiwork'); ?></label>
                        <input type="text" id="gw_tiers_search_city" name="gw_tiers_ville" class="gw-modal-input" value="<?php echo esc_attr($requestVille); ?>" placeholder="Paris" />
                    </div>
                    <div style="display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end;">
                        <a class="gw-button gw-button--secondary" href="<?php echo esc_url($currentResetUrl); ?>"><?php esc_html_e('Réinitialiser', 'gestiwork'); ?></a>
                        <button type="submit" class="gw-button gw-button--primary"><?php esc_html_e('Rechercher', 'gestiwork'); ?></button>
                    </div>
                </form>
            </div>
            <div class="gw-settings-field" style="grid-column: 1 / -1;">
                <p class="gw-settings-label"><?php esc_html_e('Tiers récents', 'gestiwork'); ?></p>
                <?php if ($tiersTotalPages > 1) : ?>
                    <p class="gw-section-description" style="margin-top: -4px;">
                        <?php echo esc_html(sprintf(__('Page %d sur %d — %d résultat(s).', 'gestiwork'), $tiersPage, $tiersTotalPages, $tiersTotal)); ?>
                    </p>
                <?php endif; ?>
                <div class="gw-table-wrapper">
                    <table class="gw-table gw-table--tiers">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Nom / Raison sociale', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Type', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Contact principal', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('E-mail', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Téléphone', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Ville', 'gestiwork'); ?></th>
                                <th><?php esc_html_e('Actions', 'gestiwork'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($hasDbTiers) : ?>
                                <?php foreach ($tiersItems as $tier) : ?>
                                    <?php
                                    $tierId = isset($tier['id']) ? (int) $tier['id'] : 0;
                                    $tierType = isset($tier['type']) ? (string) $tier['type'] : '';
                                    $tierName = isset($tier['raison_sociale']) ? (string) $tier['raison_sociale'] : '';
                                    $tierNom = isset($tier['nom']) ? (string) $tier['nom'] : '';
                                    $tierPrenom = isset($tier['prenom']) ? (string) $tier['prenom'] : '';
                                    $tierEmail = isset($tier['email']) ? (string) $tier['email'] : '';
                                    $tierTel = isset($tier['telephone']) ? (string) $tier['telephone'] : '';
                                    $tierTelMobile = isset($tier['telephone_portable']) ? (string) $tier['telephone_portable'] : '';
                                    $tierVille = isset($tier['ville']) ? (string) $tier['ville'] : '';

                                    $displayName = $tierName;
                                    if ($displayName === '') {
                                        $displayName = trim($tierPrenom . ' ' . $tierNom);
                                    }

                                    $contactPrincipal = trim($tierPrenom . ' ' . $tierNom);
                                    if ($contactPrincipal === '') {
                                        $contactPrincipal = '-';
                                    }

                                    $typeLabel = $tierType;
                                    if ($tierType === 'client_particulier') {
                                        $typeLabel = __('Particulier', 'gestiwork');
                                    } elseif ($tierType === 'entreprise') {
                                        $typeLabel = __('Entreprise', 'gestiwork');
                                    } elseif ($tierType === 'financeur') {
                                        $typeLabel = __('Financeur', 'gestiwork');
                                    } elseif ($tierType === 'of_donneur_ordre') {
                                        $typeLabel = __('OF donneur d\'ordre', 'gestiwork');
                                    }

                                    $telephoneDisplay = $tierTel !== '' ? $tierTel : $tierTelMobile;
                                    if ($telephoneDisplay === '') {
                                        $telephoneDisplay = '-';
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo esc_url(add_query_arg(['gw_view' => 'Client', 'gw_tier_id' => $tierId], home_url('/gestiwork/'))); ?>">
                                                <?php echo esc_html($displayName !== '' ? $displayName : ('#' . $tierId)); ?>
                                            </a>
                                        </td>
                                        <td><?php echo esc_html($typeLabel); ?></td>
                                        <td><?php echo esc_html($contactPrincipal); ?></td>
                                        <td>
                                            <?php if ($tierEmail !== '') : ?>
                                                <a href="mailto:<?php echo esc_attr($tierEmail); ?>"><?php echo esc_html($tierEmail); ?></a>
                                            <?php else : ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo esc_html($telephoneDisplay); ?></td>
                                        <td><?php echo esc_html($tierVille !== '' ? $tierVille : '-'); ?></td>
                                        <td>
                                            <a class="gw-button gw-button--secondary" href="<?php echo esc_url(add_query_arg(['gw_view' => 'Client', 'gw_tier_id' => $tierId], home_url('/gestiwork/'))); ?>" title="<?php esc_attr_e('Voir le tiers', 'gestiwork'); ?>">
                                                <span class="dashicons dashicons-visibility" aria-hidden="true"></span>
                                            </a>
                                            <a class="gw-button gw-button--secondary" href="<?php echo esc_url(add_query_arg(['gw_view' => 'Client', 'gw_tier_id' => $tierId, 'mode' => 'edit'], home_url('/gestiwork/'))); ?>" title="<?php esc_attr_e('Modifier le tiers', 'gestiwork'); ?>">
                                                <span class="dashicons dashicons-edit" aria-hidden="true"></span>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="7" style="color: var(--gw-color-muted); font-style: italic;">
                                        <?php esc_html_e('Aucun tiers enregistré.', 'gestiwork'); ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($tiersTotalPages > 1) : ?>
                    <div style="display:flex; gap:8px; justify-content:flex-end; margin-top: 12px;">
                        <?php
                        $queryArgs = $_GET;
                        unset($queryArgs['gw_tiers_page']);
                        $prevUrl = $tiersPage > 1 ? add_query_arg(array_merge($queryArgs, ['gw_tiers_page' => $tiersPage - 1]), $currentResetUrl) : '';
                        $nextUrl = $tiersPage < $tiersTotalPages ? add_query_arg(array_merge($queryArgs, ['gw_tiers_page' => $tiersPage + 1]), $currentResetUrl) : '';
                        ?>
                        <?php if ($prevUrl !== '') : ?>
                            <a class="gw-button gw-button--secondary" href="<?php echo esc_url($prevUrl); ?>"><?php esc_html_e('Précédent', 'gestiwork'); ?></a>
                        <?php endif; ?>
                        <?php if ($nextUrl !== '') : ?>
                            <a class="gw-button gw-button--secondary" href="<?php echo esc_url($nextUrl); ?>"><?php esc_html_e('Suivant', 'gestiwork'); ?></a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="gw-settings-group">
        <h3 class="gw-section-subtitle"><?php esc_html_e('Ce qui sera possible prochainement', 'gestiwork'); ?></h3>
        <ul class="gw-list">
            <li><?php esc_html_e('Ajouter un nouveau tiers (client, financeur, OF) via un formulaire dédié.', 'gestiwork'); ?></li>
            <li><?php esc_html_e('Rechercher un tiers par nom, type ou ville.', 'gestiwork'); ?></li>
            <li><?php esc_html_e('Accéder à la fiche détaillée d’un tiers (coordonnées complètes, historique des formations, etc.).', 'gestiwork'); ?></li>
            <li><?php esc_html_e('Sélectionner un client ou financeur lors de la création de devis, conventions et convocations.', 'gestiwork'); ?></li>
        </ul>
    </div>
</section>
