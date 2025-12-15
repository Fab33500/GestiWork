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
                'Créez une nouvelle fiche tiers (client, entreprise, financeur ou organisme donneur d\'ordre). La création détaillée sera disponible prochainement.',
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
                'Consultez la liste des tiers et affinez les résultats grâce à la recherche avancée.',
                'gestiwork'
            ); ?>
        </p>

        <div class="gw-settings-grid">
            <div class="gw-settings-field gw-settings-field--full">
                <p class="gw-settings-label"><?php esc_html_e('Recherche avancée', 'gestiwork'); ?></p>
                <?php
                $gw_search_action_url = '';
                $gw_search_reset_url = $currentResetUrl;
                $gw_search_submit_label = __('Rechercher', 'gestiwork');
                $gw_search_fields = [
                    [
                        'type' => 'text',
                        'id' => 'gw_tiers_search_query',
                        'name' => 'gw_tiers_query',
                        'label' => __('Recherche (nom, e-mail, téléphone...)', 'gestiwork'),
                        'value' => $requestQuery,
                        'placeholder' => __('Entreprise, Dupont...', 'gestiwork'),
                    ],
                    [
                        'type' => 'select',
                        'id' => 'gw_tiers_search_type',
                        'name' => 'gw_tiers_type',
                        'label' => __('Type de tiers', 'gestiwork'),
                        'value' => $requestType,
                        'options' => [
                            '' => __('Tous', 'gestiwork'),
                            'client_particulier' => __('Particulier', 'gestiwork'),
                            'entreprise' => __('Entreprise', 'gestiwork'),
                            'financeur' => __('Financeur / OPCO', 'gestiwork'),
                            'of_donneur_ordre' => __('OF donneur d\'ordre', 'gestiwork'),
                        ],
                    ],
                    [
                        'type' => 'select',
                        'id' => 'gw_tiers_search_status',
                        'name' => 'gw_tiers_statut',
                        'label' => __('Statut', 'gestiwork'),
                        'value' => $requestStatut,
                        'options' => [
                            '' => __('Tous', 'gestiwork'),
                            'prospect' => __('Prospect', 'gestiwork'),
                            'client' => __('Client', 'gestiwork'),
                        ],
                    ],
                    [
                        'type' => 'text',
                        'id' => 'gw_tiers_search_city',
                        'name' => 'gw_tiers_ville',
                        'label' => __('Ville', 'gestiwork'),
                        'value' => $requestVille,
                        'placeholder' => __('Paris', 'gestiwork'),
                    ],
                ];

                $partial = GW_PLUGIN_DIR . 'templates/erp/partials/advanced-search.php';
                if (is_readable($partial)) {
                    require $partial;
                }
                ?>
            </div>
            <div class="gw-settings-field gw-settings-field--full">
                <p class="gw-settings-label"><?php esc_html_e('Tiers récents', 'gestiwork'); ?></p>
                <?php if ($tiersTotalPages > 1) : ?>
                    <p class="gw-section-description gw-section-description--compact">
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
                                    <td colspan="7" class="gw-table-empty">
                                        <?php esc_html_e('Aucun tiers enregistré.', 'gestiwork'); ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($tiersTotalPages > 1) : ?>
                    <div class="gw-pagination">
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

</section>
