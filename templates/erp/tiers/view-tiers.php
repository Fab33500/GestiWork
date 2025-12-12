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

if (! current_user_can('manage_options')) {
    // Par sécurité, on ne montre rien aux non-admins.
    wp_die(esc_html__('Accès non autorisé.', 'gestiwork'), 403);
}
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
                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 10px; align-items: end;">
                    <div>
                        <label class="gw-settings-placeholder" for="gw_tiers_search_query"><?php esc_html_e('Recherche (nom, e-mail, téléphone...)', 'gestiwork'); ?></label>
                        <input type="text" id="gw_tiers_search_query" class="gw-modal-input" placeholder="Entreprise, Dupont..." />
                    </div>
                    <div>
                        <label class="gw-settings-placeholder" for="gw_tiers_search_type"><?php esc_html_e('Type de tiers', 'gestiwork'); ?></label>
                        <select id="gw_tiers_search_type" class="gw-modal-input">
                            <option value=""><?php esc_html_e('Tous', 'gestiwork'); ?></option>
                            <option value="client_particulier"><?php esc_html_e('Particulier', 'gestiwork'); ?></option>
                            <option value="entreprise"><?php esc_html_e('Entreprise', 'gestiwork'); ?></option>
                            <option value="financeur"><?php esc_html_e('Financeur / OPCO', 'gestiwork'); ?></option>
                            <option value="of_donneur_ordre"><?php esc_html_e('OF donneur d\'ordre', 'gestiwork'); ?></option>
                        </select>
                    </div>
                    <div>
                        <label class="gw-settings-placeholder" for="gw_tiers_search_status"><?php esc_html_e('Statut', 'gestiwork'); ?></label>
                        <select id="gw_tiers_search_status" class="gw-modal-input">
                            <option value=""><?php esc_html_e('Tous', 'gestiwork'); ?></option>
                            <option value="prospect"><?php esc_html_e('Prospect', 'gestiwork'); ?></option>
                            <option value="client"><?php esc_html_e('Client', 'gestiwork'); ?></option>
                        </select>
                    </div>
                    <div>
                        <label class="gw-settings-placeholder" for="gw_tiers_search_city"><?php esc_html_e('Ville', 'gestiwork'); ?></label>
                        <input type="text" id="gw_tiers_search_city" class="gw-modal-input" placeholder="Paris" />
                    </div>
                    <div style="display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end;">
                        <button type="button" class="gw-button gw-button--secondary"><?php esc_html_e('Réinitialiser', 'gestiwork'); ?></button>
                        <button type="button" class="gw-button gw-button--primary"><?php esc_html_e('Rechercher', 'gestiwork'); ?></button>
                    </div>
                </div>
            </div>
            <div class="gw-settings-field" style="grid-column: 1 / -1;">
                <p class="gw-settings-label"><?php esc_html_e('Tiers récents (exemple fictif)', 'gestiwork'); ?></p>
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
                            <tr>
                                <td>
                                    <a href="<?php echo esc_url(add_query_arg(['gw_view' => 'Client', 'gw_tier_id' => 1], home_url('/gestiwork/'))); ?>">
                                        Entreprise Exemple SARL
                                    </a>
                                </td>
                                <td><?php esc_html_e('Client', 'gestiwork'); ?></td>
                                <td>Jean Dupont</td>
                                <td><a href="mailto:contact@exemple-client.fr">contact@exemple-client.fr</a></td>
                                <td>01 23 45 67 89</td>
                                <td>Paris</td>
                                <td>
                                    <a class="gw-button gw-button--secondary" href="<?php echo esc_url(add_query_arg(['gw_view' => 'Client', 'gw_tier_id' => 1], home_url('/gestiwork/'))); ?>" title="<?php esc_attr_e('Voir le tiers', 'gestiwork'); ?>">
                                        <span class="dashicons dashicons-visibility" aria-hidden="true"></span>
                                    </a>
                                    <a class="gw-button gw-button--secondary" href="<?php echo esc_url(add_query_arg(['gw_view' => 'Client', 'gw_tier_id' => 1, 'mode' => 'edit'], home_url('/gestiwork/'))); ?>" title="<?php esc_attr_e('Modifier le tiers', 'gestiwork'); ?>">
                                        <span class="dashicons dashicons-edit" aria-hidden="true"></span>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="<?php echo esc_url(add_query_arg(['gw_view' => 'Client', 'gw_tier_id' => 2], home_url('/gestiwork/'))); ?>">
                                        OPCO Démo
                                    </a>
                                </td>
                                <td><?php esc_html_e('Financeur', 'gestiwork'); ?></td>
                                <td>Service Financement</td>
                                <td><a href="mailto:financement@opco-demo.fr">financement@opco-demo.fr</a></td>
                                <td>04 56 78 90 12</td>
                                <td>Lyon</td>
                                <td>
                                    <a class="gw-button gw-button--secondary" href="<?php echo esc_url(add_query_arg(['gw_view' => 'Client', 'gw_tier_id' => 2], home_url('/gestiwork/'))); ?>" title="<?php esc_attr_e('Voir le tiers', 'gestiwork'); ?>">
                                        <span class="dashicons dashicons-visibility" aria-hidden="true"></span>
                                    </a>
                                    <a class="gw-button gw-button--secondary" href="<?php echo esc_url(add_query_arg(['gw_view' => 'Client', 'gw_tier_id' => 2, 'mode' => 'edit'], home_url('/gestiwork/'))); ?>" title="<?php esc_attr_e('Modifier le tiers', 'gestiwork'); ?>">
                                        <span class="dashicons dashicons-edit" aria-hidden="true"></span>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="<?php echo esc_url(add_query_arg(['gw_view' => 'Client', 'gw_tier_id' => 3], home_url('/gestiwork/'))); ?>">
                                        OF Donneur d’ordre Alpha
                                    </a>
                                </td>
                                <td><?php esc_html_e('OF donneur d\'ordre', 'gestiwork'); ?></td>
                                <td>Marie Martin</td>
                                <td><a href="mailto:contact@of-alpha.fr">contact@of-alpha.fr</a></td>
                                <td>05 11 22 33 44</td>
                                <td>Bordeaux</td>
                                <td>
                                    <a class="gw-button gw-button--secondary" href="<?php echo esc_url(add_query_arg(['gw_view' => 'Client', 'gw_tier_id' => 3], home_url('/gestiwork/'))); ?>" title="<?php esc_attr_e('Voir le tiers', 'gestiwork'); ?>">
                                        <span class="dashicons dashicons-visibility" aria-hidden="true"></span>
                                    </a>
                                    <a class="gw-button gw-button--secondary" href="<?php echo esc_url(add_query_arg(['gw_view' => 'Client', 'gw_tier_id' => 3, 'mode' => 'edit'], home_url('/gestiwork/'))); ?>" title="<?php esc_attr_e('Modifier le tiers', 'gestiwork'); ?>">
                                        <span class="dashicons dashicons-edit" aria-hidden="true"></span>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="<?php echo esc_url(add_query_arg(['gw_view' => 'Client', 'gw_tier_id' => 4], home_url('/gestiwork/'))); ?>">
                                        Camille Leroy
                                    </a>
                                </td>
                                <td><?php esc_html_e('Particulier', 'gestiwork'); ?></td>
                                <td>Camille Leroy</td>
                                <td><a href="mailto:camille.leroy@example.com">camille.leroy@example.com</a></td>
                                <td>06 22 33 44 55</td>
                                <td>Nantes</td>
                                <td>
                                    <a class="gw-button gw-button--secondary" href="<?php echo esc_url(add_query_arg(['gw_view' => 'Client', 'gw_tier_id' => 4], home_url('/gestiwork/'))); ?>" title="<?php esc_attr_e('Voir le tiers', 'gestiwork'); ?>">
                                        <span class="dashicons dashicons-visibility" aria-hidden="true"></span>
                                    </a>
                                    <a class="gw-button gw-button--secondary" href="<?php echo esc_url(add_query_arg(['gw_view' => 'Client', 'gw_tier_id' => 4, 'mode' => 'edit'], home_url('/gestiwork/'))); ?>" title="<?php esc_attr_e('Modifier le tiers', 'gestiwork'); ?>">
                                        <span class="dashicons dashicons-edit" aria-hidden="true"></span>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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
