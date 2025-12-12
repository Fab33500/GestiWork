<?php
/**
 * GestiWork ERP - Vue Tiers (clients / financeurs / OF donneur d'ordre)
 *
 * Modèle de page "en dur" pour l'onglet Tiers.
 * Cette vue présente une maquette fonctionnelle : sections, texte d'aide
 * et tableau de tiers fictifs, afin de poser l'UX avant le branchement
 * sur la base de données.
 */

declare(strict_types=1);

if (! current_user_can('manage_options')) {
    // Par sécurité, on ne montre rien aux non-admins.
    wp_die(esc_html__('Accès non autorisé.', 'gestiwork'), 403);
}
?>

<section class="gw-section gw-section-dashboard">
    <h2 class="gw-section-title"><?php esc_html_e('Tiers (Entreprises, clients particuliers, financeurs, OF donneurs d\'ordre)', 'gestiwork'); ?></h2>
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

        <button type="button" class="gw-button gw-button--secondary gw-button-modals" data-gw-modal-target="gw-modal-tiers">
            <?php esc_html_e('Créer un nouveau tiers', 'gestiwork'); ?>
        </button>
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
            <div class="gw-settings-field">
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
                                <td>Entreprise Exemple SARL</td>
                                <td><?php esc_html_e('Client', 'gestiwork'); ?></td>
                                <td>Jean Dupont</td>
                                <td><a href="mailto:contact@exemple-client.fr">contact@exemple-client.fr</a></td>
                                <td>01 23 45 67 89</td>
                                <td>Paris</td>
                                <td>
                                    <button type="button" class="gw-button gw-button--secondary" title="<?php esc_attr_e('Voir le tiers', 'gestiwork'); ?>">
                                        <span class="dashicons dashicons-visibility" aria-hidden="true"></span>
                                    </button>
                                    <button type="button" class="gw-button gw-button--secondary" title="<?php esc_attr_e('Modifier le tiers', 'gestiwork'); ?>">
                                        <span class="dashicons dashicons-edit" aria-hidden="true"></span>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>OPCO Démo</td>
                                <td><?php esc_html_e('Financeur', 'gestiwork'); ?></td>
                                <td>Service Financement</td>
                                <td><a href="mailto:financement@opco-demo.fr">financement@opco-demo.fr</a></td>
                                <td>04 56 78 90 12</td>
                                <td>Lyon</td>
                                <td>
                                    <button type="button" class="gw-button gw-button--secondary" title="<?php esc_attr_e('Voir le tiers', 'gestiwork'); ?>">
                                        <span class="dashicons dashicons-visibility" aria-hidden="true"></span>
                                    </button>
                                    <button type="button" class="gw-button gw-button--secondary" title="<?php esc_attr_e('Modifier le tiers', 'gestiwork'); ?>">
                                        <span class="dashicons dashicons-edit" aria-hidden="true"></span>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>OF Donneur d’ordre Alpha</td>
                                <td><?php esc_html_e('OF donneur d\'ordre', 'gestiwork'); ?></td>
                                <td>Marie Martin</td>
                                <td><a href="mailto:contact@of-alpha.fr">contact@of-alpha.fr</a></td>
                                <td>05 11 22 33 44</td>
                                <td>Bordeaux</td>
                                <td>
                                    <button type="button" class="gw-button gw-button--secondary" title="<?php esc_attr_e('Voir le tiers', 'gestiwork'); ?>">
                                        <span class="dashicons dashicons-visibility" aria-hidden="true"></span>
                                    </button>
                                    <button type="button" class="gw-button gw-button--secondary" title="<?php esc_attr_e('Modifier le tiers', 'gestiwork'); ?>">
                                        <span class="dashicons dashicons-edit" aria-hidden="true"></span>
                                    </button>
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

<div class="gw-modal-backdrop" id="gw-modal-tiers" aria-hidden="true">
    <div class="gw-modal gw-modal" role="dialog" aria-modal="true" aria-labelledby="gw-modal-tiers-title">
        <div class="gw-modal-header">
            <h3 class="gw-modal-title" id="gw-modal-tiers-title"><?php esc_html_e('Nouveau tiers', 'gestiwork'); ?></h3>
            <button type="button" class="gw-modal-close" data-gw-modal-close="gw-modal-tiers" aria-label="<?php esc_attr_e('Fermer', 'gestiwork'); ?>">
                ×
            </button>
        </div>

        <form method="post" action="">
            <div class="gw-modal-body">
                <p class="gw-modal-required-info">
                    <?php esc_html_e('Ce formulaire est une maquette : les données ne sont pas encore enregistrées en base. Il permet de poser la structure des informations pour un tiers.', 'gestiwork'); ?>
                </p>

                <div class="gw-modal-grid">
                    <div class="gw-modal-field">
                        <label for="gw_tier_type"><?php esc_html_e('Type de tiers', 'gestiwork'); ?></label>
                        <select id="gw_tier_type" name="gw_tier_type" class="gw-modal-input">
                            <option value="client_particulier"><?php esc_html_e('Client particulier', 'gestiwork'); ?></option>
                            <option value="entreprise"><?php esc_html_e('Entreprise', 'gestiwork'); ?></option>
                            <option value="financeur"><?php esc_html_e('Financeur / OPCO', 'gestiwork'); ?></option>
                            <option value="of_donneur_ordre"><?php esc_html_e('OF donneur d\'ordre', 'gestiwork'); ?></option>
                        </select>
                    </div>

                    <div class="gw-modal-field">
                        <label for="gw_tier_raison_sociale"><?php esc_html_e('Raison sociale', 'gestiwork'); ?></label>
                        <input type="text" id="gw_tier_raison_sociale" name="gw_tier_raison_sociale" class="gw-modal-input" placeholder="Entreprise Exemple SARL" />
                    </div>

                    <div class="gw-modal-field">
                        <label for="gw_tier_contact_nom"><?php esc_html_e('Nom du contact principal', 'gestiwork'); ?></label>
                        <input type="text" id="gw_tier_contact_nom" name="gw_tier_contact_nom" class="gw-modal-input" placeholder="Dupont" />
                    </div>
                    <div class="gw-modal-field">
                        <label for="gw_tier_contact_prenom"><?php esc_html_e('Prénom du contact principal', 'gestiwork'); ?></label>
                        <input type="text" id="gw_tier_contact_prenom" name="gw_tier_contact_prenom" class="gw-modal-input" placeholder="Jean" />
                    </div>

                    <div class="gw-modal-field">
                        <label for="gw_tier_email"><?php esc_html_e('E-mail de contact', 'gestiwork'); ?></label>
                        <input type="email" id="gw_tier_email" name="gw_tier_email" class="gw-modal-input" placeholder="contact@exemple-client.fr" />
                    </div>
                    <div class="gw-modal-field">
                        <label for="gw_tier_telephone"><?php esc_html_e('Téléphone', 'gestiwork'); ?></label>
                        <input type="text" id="gw_tier_telephone" name="gw_tier_telephone" class="gw-modal-input" placeholder="01 23 45 67 89" />
                    </div>

                    <div class="gw-modal-field gw-modal-field--full">
                        <label for="gw_tier_adresse"><?php esc_html_e('Adresse', 'gestiwork'); ?></label>
                        <textarea id="gw_tier_adresse" name="gw_tier_adresse" class="gw-modal-textarea" rows="2" placeholder="15 rue des Entrepreneurs&#10;Bâtiment B"></textarea>
                    </div>

                    <div class="gw-modal-field">
                        <label for="gw_tier_code_postal"><?php esc_html_e('Code postal', 'gestiwork'); ?></label>
                        <input type="text" id="gw_tier_code_postal" name="gw_tier_code_postal" class="gw-modal-input" placeholder="75010" />
                    </div>
                    <div class="gw-modal-field">
                        <label for="gw_tier_ville"><?php esc_html_e('Ville', 'gestiwork'); ?></label>
                        <input type="text" id="gw_tier_ville" name="gw_tier_ville" class="gw-modal-input" placeholder="Paris" />
                    </div>

                    <div class="gw-modal-field">
                        <label for="gw_tier_siret"><?php esc_html_e('SIRET / SIREN', 'gestiwork'); ?></label>
                        <input type="text" id="gw_tier_siret" name="gw_tier_siret" class="gw-modal-input" placeholder="123 456 789 00012" />
                    </div>
                    <div class="gw-modal-field">
                        <label for="gw_tier_tva_intra"><?php esc_html_e('TVA intracommunautaire', 'gestiwork'); ?></label>
                        <input type="text" id="gw_tier_tva_intra" name="gw_tier_tva_intra" class="gw-modal-input" placeholder="FR12 12345678901" />
                    </div>

                    <div class="gw-modal-field">
                        <label for="gw_tier_banque"><?php esc_html_e('Banque principale', 'gestiwork'); ?></label>
                        <input type="text" id="gw_tier_banque" name="gw_tier_banque" class="gw-modal-input" placeholder="Banque Populaire, Crédit Agricole..." />
                    </div>
                    <div class="gw-modal-field">
                        <label for="gw_tier_iban"><?php esc_html_e('IBAN', 'gestiwork'); ?></label>
                        <input type="text" id="gw_tier_iban" name="gw_tier_iban" class="gw-modal-input" placeholder="FR76 1234 5678 9012 3456 7890 123" />
                    </div>

                    <div class="gw-modal-field">
                        <label for="gw_tier_bic"><?php esc_html_e('BIC', 'gestiwork'); ?></label>
                        <input type="text" id="gw_tier_bic" name="gw_tier_bic" class="gw-modal-input" placeholder="ABCDFRPPXXX" />
                    </div>
                    <div class="gw-modal-field">
                        <label for="gw_tier_mode_reglement"><?php esc_html_e('Mode de règlement par défaut', 'gestiwork'); ?></label>
                        <select id="gw_tier_mode_reglement" name="gw_tier_mode_reglement" class="gw-modal-input">
                            <option value="a_reception"><?php esc_html_e('À réception de facture', 'gestiwork'); ?></option>
                            <option value="30j_net"><?php esc_html_e('30J Net', 'gestiwork'); ?></option>
                            <option value="30j_fdm"><?php esc_html_e('30J FDM', 'gestiwork'); ?></option>
                            <option value="60j_net"><?php esc_html_e('60J Net', 'gestiwork'); ?></option>
                            <option value="60j_fdm"><?php esc_html_e('60J FDM', 'gestiwork'); ?></option>
                        </select>
                    </div>

                    <div class="gw-modal-field gw-modal-field--full">
                        <label for="gw_tier_notes"><?php esc_html_e('Remarques internes', 'gestiwork'); ?></label>
                        <textarea id="gw_tier_notes" name="gw_tier_notes" class="gw-modal-textarea" rows="3" placeholder="Notes internes sur ce tiers (conditions particulières, interlocuteurs secondaires, etc.)."></textarea>
                    </div>
                </div>
            </div>

            <div class="gw-modal-footer">
                <button type="button" class="gw-button gw-button--secondary" data-gw-modal-close="gw-modal-tiers">
                    <?php esc_html_e('Annuler', 'gestiwork'); ?>
                </button>
                <button type="submit" class="gw-button gw-button--primary">
                    <?php esc_html_e('Créer le tiers', 'gestiwork'); ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        var triggers = document.querySelectorAll('[data-gw-modal-target="gw-modal-tiers"]');
        var modal = document.getElementById('gw-modal-tiers');
        var closeButtons = document.querySelectorAll('[data-gw-modal-close="gw-modal-tiers"]');

        if (!modal || !triggers.length) {
            return;
        }

        function openModal() {
            modal.classList.add('gw-modal-backdrop--open');
            modal.setAttribute('aria-hidden', 'false');
            if (typeof modal.scrollIntoView === 'function') {
                modal.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        function closeModal() {
            modal.classList.remove('gw-modal-backdrop--open');
            modal.setAttribute('aria-hidden', 'true');
        }

        triggers.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                openModal();
            });
        });

        closeButtons.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                closeModal();
            });
        });
    })();
</script>
