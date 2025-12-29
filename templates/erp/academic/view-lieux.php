<?php
/**
 * GestiWork ERP - Lieux (UI statique)
 */

declare(strict_types=1);

if (! current_user_can('manage_options')) {
    wp_die(esc_html__('Accès non autorisé.', 'gestiwork'), 403);
}

$lieux_data = [
    [
        'id' => 1,
        'nom' => 'Salle Bréhat',
        'capacite' => 12,
        'description' => 'Salle en U avec vidéoprojecteur',
        'adresse' => '6 rue de l\'Île Vierge',
        'code_postal' => '35000',
        'ville' => 'RENNES',
    ],
    [
        'id' => 2,
        'nom' => 'Centre de formation Atlantique',
        'capacite' => 20,
        'description' => 'Salle principale avec tableau interactif et équipement audiovisuel complet',
        'adresse' => '15 avenue de la Mer',
        'code_postal' => '33000',
        'ville' => 'BORDEAUX',
    ],
    [
        'id' => 3,
        'nom' => 'Espace Molière',
        'capacite' => 0,
        'description' => 'Petite salle pour formations en petit comité',
        'adresse' => '22 boulevard Molière',
        'code_postal' => '75001',
        'ville' => 'PARIS',
    ],
    [
        'id' => 4,
        'nom' => 'Salle Lumière',
        'capacite' => 30,
        'description' => 'Grande salle modulable, configuration théâtre ou îlots',
        'adresse' => '8 place des Terreaux',
        'code_postal' => '69001',
        'ville' => 'LYON',
    ],
    [
        'id' => 5,
        'nom' => 'Annexe Sud',
        'capacite' => 0,
        'description' => 'Salle climatisée avec accès PMR',
        'adresse' => '45 cours Mirabeau',
        'code_postal' => '13100',
        'ville' => 'AIX-EN-PROVENCE',
    ],
];
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
            <button class="gw-button gw-button--secondary gw-button--cta" type="button" data-gw-modal-target="gw-modal-lieu" data-gw-lieu-action="create">
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
            <table class="gw-table gw-table--stackable">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Nom du lieu', 'gestiwork'); ?> <span class="dashicons dashicons-sort" aria-hidden="true"></span></th>
                        <th><?php esc_html_e('Capacité max.', 'gestiwork'); ?> <span class="dashicons dashicons-sort" aria-hidden="true"></span></th>
                        <th><?php esc_html_e('Adresse', 'gestiwork'); ?> <span class="dashicons dashicons-sort" aria-hidden="true"></span></th>
                        <th><?php esc_html_e('Code postal', 'gestiwork'); ?> <span class="dashicons dashicons-sort" aria-hidden="true"></span></th>
                        <th><?php esc_html_e('Ville', 'gestiwork'); ?> <span class="dashicons dashicons-sort" aria-hidden="true"></span></th>
                        <th><?php esc_html_e('Actions', 'gestiwork'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lieux_data as $lieu) : ?>
                    <tr>
                        <td data-label="<?php esc_attr_e('Nom du lieu', 'gestiwork'); ?>"><?php echo esc_html($lieu['nom']); ?></td>
                        <td data-label="<?php esc_attr_e('Capacité max.', 'gestiwork'); ?>"><?php echo $lieu['capacite'] > 0 ? esc_html(number_format_i18n($lieu['capacite'])) : '—'; ?></td>
                        <td data-label="<?php esc_attr_e('Adresse', 'gestiwork'); ?>"><?php echo esc_html($lieu['adresse']); ?></td>
                        <td data-label="<?php esc_attr_e('Code postal', 'gestiwork'); ?>"><?php echo esc_html($lieu['code_postal']); ?></td>
                        <td data-label="<?php esc_attr_e('Ville', 'gestiwork'); ?>"><?php echo esc_html($lieu['ville']); ?></td>
                        <td data-label="<?php esc_attr_e('Actions', 'gestiwork'); ?>">
                            <button 
                                class="gw-button gw-button--icon-only" 
                                type="button" 
                                data-gw-modal-target="gw-modal-lieu" 
                                data-gw-lieu-action="edit"
                                data-gw-lieu-id="<?php echo esc_attr((string) $lieu['id']); ?>"
                                data-gw-lieu-nom="<?php echo esc_attr($lieu['nom']); ?>"
                                data-gw-lieu-capacite="<?php echo esc_attr((string) $lieu['capacite']); ?>"
                                data-gw-lieu-description="<?php echo esc_attr($lieu['description']); ?>"
                                data-gw-lieu-adresse="<?php echo esc_attr($lieu['adresse']); ?>"
                                data-gw-lieu-code-postal="<?php echo esc_attr($lieu['code_postal']); ?>"
                                data-gw-lieu-ville="<?php echo esc_attr($lieu['ville']); ?>"
                                title="<?php esc_attr_e('Modifier ce lieu', 'gestiwork'); ?>">
                                <span class="dashicons dashicons-edit" aria-hidden="true"></span>
                                <span class="screen-reader-text"><?php echo esc_html(sprintf(__('Modifier %s', 'gestiwork'), $lieu['nom'])); ?></span>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="gw-pagination gw-pagination--compact">
            <div class="gw-pagination__info">
                <?php echo esc_html(sprintf(__('Montrer 1 à %d des %d lignes', 'gestiwork'), count($lieux_data), count($lieux_data))); ?>
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

    <div class="gw-modal-backdrop" id="gw-modal-lieu" aria-hidden="true">
        <div class="gw-modal gw-modal" role="dialog" aria-modal="true" aria-labelledby="gw-modal-lieu-title">
            <div class="gw-modal-header">
                <h3 class="gw-modal-title" id="gw-modal-lieu-title"><?php esc_html_e('Créer un lieu', 'gestiwork'); ?></h3>
                <button type="button" class="gw-modal-close" data-gw-modal-close="gw-modal-lieu" aria-label="<?php esc_attr_e('Fermer', 'gestiwork'); ?>">×</button>
            </div>
            <form method="post" action="" id="gw-form-lieu">
                <?php wp_nonce_field('gw_save_lieu', 'gw_lieu_nonce'); ?>
                <input type="hidden" name="gw_lieu_action" id="gw_lieu_action" value="create" />
                <input type="hidden" name="gw_lieu_id" id="gw_lieu_id" value="" />
                <div class="gw-modal-body">
                    <p class="gw-modal-required-info">
                        <?php esc_html_e('Les champs marqués d\'une astérisque rouge (*) sont obligatoires.', 'gestiwork'); ?>
                    </p>
                    <div class="gw-modal-grid">
                        <div class="gw-modal-field">
                            <label for="gw_lieu_nom"><?php esc_html_e('Nom du lieu', 'gestiwork'); ?> <span class="gw-required-asterisk gw-color-error">*</span></label>
                            <input type="text" class="gw-modal-input" id="gw_lieu_nom" name="gw_lieu_nom" value="" required />
                        </div>
                        <div class="gw-modal-field">
                            <label for="gw_lieu_capacite"><?php esc_html_e('Capacité maximale', 'gestiwork'); ?></label>
                            <input type="number" min="0" class="gw-modal-input" id="gw_lieu_capacite" name="gw_lieu_capacite" value="" />
                        </div>
                        <div class="gw-modal-field" style="grid-column: 1 / -1;">
                            <label for="gw_lieu_description"><?php esc_html_e('Description', 'gestiwork'); ?></label>
                            <textarea class="gw-modal-textarea" id="gw_lieu_description" name="gw_lieu_description" rows="3"></textarea>
                        </div>
                        <div class="gw-modal-field" style="grid-column: 1 / -1;">
                            <label for="gw_lieu_adresse"><?php esc_html_e('Adresse', 'gestiwork'); ?> <span class="gw-required-asterisk gw-color-error">*</span></label>
                            <textarea class="gw-modal-textarea" id="gw_lieu_adresse" name="gw_lieu_adresse" rows="2" required></textarea>
                        </div>
                        <div class="gw-modal-field">
                            <label for="gw_lieu_code_postal"><?php esc_html_e('Code postal', 'gestiwork'); ?> <span class="gw-required-asterisk gw-color-error">*</span></label>
                            <input type="text" class="gw-modal-input" id="gw_lieu_code_postal" name="gw_lieu_code_postal" value="" required />
                        </div>
                        <div class="gw-modal-field">
                            <label for="gw_lieu_ville"><?php esc_html_e('Ville', 'gestiwork'); ?> <span class="gw-required-asterisk gw-color-error">*</span></label>
                            <input type="text" class="gw-modal-input" id="gw_lieu_ville" name="gw_lieu_ville" value="" required />
                        </div>
                    </div>
                </div>
                <div class="gw-modal-footer">
                    <button type="button" class="gw-button gw-button--secondary" data-gw-modal-close="gw-modal-lieu"><?php esc_html_e('Annuler', 'gestiwork'); ?></button>
                    <button type="submit" class="gw-button gw-button--primary" id="gw_lieu_submit"><?php esc_html_e('Enregistrer', 'gestiwork'); ?></button>
                </div>
            </form>
        </div>
    </div>
</section>
