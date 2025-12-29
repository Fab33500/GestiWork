<?php
/**
 * GestiWork ERP - Lieux (UI statique)
 */

declare(strict_types=1);

if (! current_user_can('manage_options')) {
    wp_die(esc_html__('Accès non autorisé.', 'gestiwork'), 403);
}
?>

<section class="gw-section gw-section-dashboard">
    <div class="gw-flex-between">
        <div>
            <h2 class="gw-section-title"><?php esc_html_e('Lieux', 'gestiwork'); ?></h2>
            <p class="gw-section-description">
                <?php esc_html_e(
                    'Centralisez vos sites, salles, capacités et équipements pour alimenter le planning des sessions.',
                    'gestiwork'
                ); ?>
            </p>
        </div>
        <div class="gw-flex-end">
            <button class="gw-button gw-button--secondary gw-button--cta" type="button" disabled>
                <span class="dashicons dashicons-admin-multisite" aria-hidden="true"></span>
                <?php esc_html_e('Nouveau lieu', 'gestiwork'); ?>
            </button>
        </div>
    </div>

    <div class="gw-settings-group">
        <div class="gw-flex-between gw-flex-between--stack">
            <div>
                <h3 class="gw-subsection-title"><?php esc_html_e('Liste des lieux', 'gestiwork'); ?></h3>
                <p class="gw-section-description">
                    <?php esc_html_e(
                        'La recherche portera à terme sur le nom, la ville, la capacité ou les équipements.',
                        'gestiwork'
                    ); ?>
                </p>
            </div>
            <form class="gw-search-form" action="#" method="get">
                <label class="screen-reader-text" for="gw_room_search"><?php esc_html_e('Rechercher un lieu', 'gestiwork'); ?></label>
                <div class="gw-search-form__fieldset">
                    <span class="dashicons dashicons-search" aria-hidden="true"></span>
                    <input
                        id="gw_room_search"
                        class="gw-input"
                        type="search"
                        name="gw_room_query"
                        placeholder="<?php esc_attr_e('Rechercher un lieu', 'gestiwork'); ?>"
                        disabled
                    />
                </div>
            </form>
        </div>

        <div class="gw-table-wrapper">
            <table class="gw-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Nom du lieu', 'gestiwork'); ?> <span class="dashicons dashicons-sort" aria-hidden="true"></span></th>
                        <th><?php esc_html_e('Capacité max.', 'gestiwork'); ?> <span class="dashicons dashicons-sort" aria-hidden="true"></span></th>
                        <th><?php esc_html_e('Adresse email', 'gestiwork'); ?> <span class="dashicons dashicons-sort" aria-hidden="true"></span></th>
                        <th><?php esc_html_e('Description', 'gestiwork'); ?> <span class="dashicons dashicons-sort" aria-hidden="true"></span></th>
                        <th><?php esc_html_e('Adresse', 'gestiwork'); ?> <span class="dashicons dashicons-sort" aria-hidden="true"></span></th>
                        <th><?php esc_html_e('Code postal', 'gestiwork'); ?> <span class="dashicons dashicons-sort" aria-hidden="true"></span></th>
                        <th><?php esc_html_e('Ville', 'gestiwork'); ?> <span class="dashicons dashicons-sort" aria-hidden="true"></span></th>
                        <th><?php esc_html_e('Actions', 'gestiwork'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php esc_html_e('Lieu Bréhat', 'gestiwork'); ?></td>
                        <td><?php echo esc_html(number_format_i18n(12)); ?></td>
                        <td>—</td>
                        <td><?php esc_html_e('Salle en U avec vidéoprojecteur', 'gestiwork'); ?></td>
                        <td><?php esc_html_e('6 rue de l’Île Vierge', 'gestiwork'); ?></td>
                        <td><?php esc_html_e('35000', 'gestiwork'); ?></td>
                        <td><?php esc_html_e('Rennes', 'gestiwork'); ?></td>
                        <td>
                            <button class="gw-button gw-button--icon-only" type="button" disabled>
                                <span class="dashicons dashicons-admin-generic" aria-hidden="true"></span>
                                <span class="screen-reader-text"><?php esc_html_e('Actions lieu Bréhat', 'gestiwork'); ?></span>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="gw-pagination gw-pagination--compact">
            <div class="gw-pagination__info">
                <?php esc_html_e('Montrer 1 à 1 des 1 lignes', 'gestiwork'); ?>
            </div>
            <div class="gw-pagination__controls">
                <button class="gw-button gw-button--secondary" type="button" disabled>&laquo;</button>
                <button class="gw-button gw-button--secondary" type="button" disabled>&lsaquo;</button>
                <span class="gw-pagination__current"><?php esc_html_e('1', 'gestiwork'); ?></span>
                <button class="gw-button gw-button--secondary" type="button" disabled>&rsaquo;</button>
                <button class="gw-button gw-button--secondary" type="button" disabled>&raquo;</button>
            </div>
            <div class="gw-pagination__page-size">
                <label for="gw_room_page_size"><?php esc_html_e('Lignes', 'gestiwork'); ?></label>
                <select id="gw_room_page_size" class="gw-select" disabled>
                    <option selected>15</option>
                    <option>30</option>
                    <option>50</option>
                </select>
            </div>
        </div>
    </div>
</section>
