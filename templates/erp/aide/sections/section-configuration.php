<?php
/**
 * GestiWork ERP - Aide : section Configuration
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

if (current_user_can('manage_options')) : ?>
    <div class="gw-settings-group gw-aide-section" id="gw-aide-configuration">
        <h3 class="gw-section-subtitle"><?php esc_html_e('Configuration et paramétrage (administrateur)', 'gestiwork'); ?></h3>
        <p class="gw-section-description">
            Cet onglet permet de définir l’identité officielle de votre organisme de formation, ses informations fiscales,
            bancaires et le format des numéros de devis. Ces informations sont ensuite réutilisées automatiquement dans
            les documents générés par GestiWork (propositions, conventions, factures, etc.).
        </p>

        <p class="gw-section-description">
            Les champs marqués d’une <strong>astérisque rouge (*)</strong> sont obligatoires. Pour les téléphones, au moins
            <strong>un des deux champs</strong> (fixe ou portable) doit être renseigné.
        </p>

        <h4 class="gw-section-subtitle"><span>1.</span> Accéder à l’onglet « Général &amp; Identité »</h4>
        <ul class="gw-list">
            <li>Ouvrez le menu <strong>Paramètres</strong> dans la barre latérale de GestiWork.</li>
            <li>Choisissez l’onglet <strong>« Général &amp; Identité »</strong>.</li>
            <li>Cliquez sur le bouton <strong>« Modifier les informations de cet onglet »</strong> pour ouvrir la fenêtre d’édition.</li>
        </ul>

        <h4 class="gw-section-subtitle"><span>2.</span> Renseigner l’identité de l’organisme</h4>
        <ul class="gw-list">
            <li><strong>Nom (raison sociale) *</strong><br />
                Indiquez le nom légal de votre organisme (par exemple « Audixor Formation », « SARL Dupont Conseil »).
                Ce nom sera utilisé sur vos documents officiels.
            </li>
            <li><strong>E‑mail de contact *</strong><br />
                Adresse e‑mail principale de contact (par exemple <code>contact@monof.fr</code>). Elle peut être utilisée comme
                adresse de réponse dans les échanges.
            </li>
            <li><strong>Site Internet</strong><br />
                Adresse de votre site web (par exemple <code>https://www.monof.fr</code>). Champ facultatif.
            </li>
            <li><strong>Téléphone fixe * et Téléphone portable *</strong><br />
                Vous disposez de deux champs de téléphone. Vous pouvez remplir les deux, mais au minimum l’un des deux doit être renseigné.
                Le format attendu est guidé : deux chiffres par groupe (ex. <code>01 23 45 67 89</code> ou <code>06 12 34 56 78</code>).
            </li>
            <li><strong>Adresse *</strong><br />
                Rue et complément d’adresse (ex. « 15 rue des Entrepreneurs – Bâtiment B »).
            </li>
            <li><strong>Code postal * et Ville *</strong><br />
                Indiquez le code postal (ex. <code>75010</code>) et la ville (ex. <code>Paris</code>). Ces informations sont réutilisées dans
                l’en‑tête des documents et certains exports.
            </li>
        </ul>

        <h4 class="gw-section-subtitle"><span>3.</span> Compléter les informations fiscales et réglementaires</h4>
        <ul class="gw-list">
            <li><strong>SIRET / SIREN *</strong><br />
                Saisissez votre SIREN (9 chiffres, ex. <code>123 456 789</code>) ou votre SIRET (14 chiffres, ex. <code>123 456 789 00012</code>).
                Ces informations sont indispensables sur vos documents contractuels.
            </li>
            <li><strong>Code APE (NAF) *</strong><br />
                Indiquez le code APE/NAF de votre activité (ex. <code>8559A</code>). Il figure sur votre extrait Kbis ou votre certificat d’inscription.
            </li>
            <li><strong>RCS / immatriculation</strong><br />
                Informations d’immatriculation (ex. « RCS Paris 123 456 789 »). Champ facultatif mais recommandé.
            </li>
            <li><strong>NDA (numéro de déclaration d’activité) *</strong><br />
                Numéro de déclaration d’activité en tant qu’organisme de formation (ex. format régional habituel). Ce numéro est obligatoire pour
                la plupart des organismes de formation.
            </li>
            <li><strong>Qualiopi</strong><br />
                Numéro de certification Qualiopi, si vous êtes certifié. Cette information est utile pour vos documents qualité et vos échanges
                avec les financeurs.
            </li>
            <li><strong>Datadock</strong><br />
                Référence Datadock éventuelle. Champ facultatif.
            </li>
            <li><strong>RM (registre des métiers)</strong><br />
                Référence d’immatriculation au registre des métiers si vous y êtes inscrit (ex. « RM Paris 123 456 789 »).
            </li>
        </ul>

        <h4 class="gw-section-subtitle"><span>4.</span> Régime de TVA et numéros associés</h4>
        <ul class="gw-list">
            <li><strong>Régime de TVA</strong><br />
                Choisissez entre :
                <ul>
                    <li><em>Exonéré (article 261‑4‑4 du CGI)</em> : pour les organismes de formation exonérés de TVA.</li>
                    <li><em>Assujetti</em> : si votre organisme est soumis à la TVA.</li>
                </ul>
                Ce choix détermine si les champs liés à la TVA sont affichés ou non.
            </li>
            <li><strong>TVA intracommunautaire</strong> (visible si vous êtes assujetti)<br />
                Numéro de TVA intracommunautaire (ex. <code>FR12 12345678901</code>), sans espaces superflus.
            </li>
            <li><strong>Taux de TVA par défaut</strong> (visible si vous êtes assujetti)<br />
                Taux appliqué par défaut à vos prestations (5,5 %, 10 % ou 20 % selon votre situation).
            </li>
        </ul>

        <h4 class="gw-section-subtitle"><span>5.</span> Banque et règlements</h4>
        <ul class="gw-list">
            <li><strong>Banque principale</strong><br />
                Nom de la banque dans laquelle vous recevez les règlements (ex. « Banque Populaire », « Crédit Agricole »).
            </li>
            <li><strong>IBAN</strong><br />
                Coordonnées complètes de votre IBAN, sans espaces (ex. <code>FR7612345678901234567890123</code>).
            </li>
            <li><strong>BIC</strong><br />
                Code BIC de votre banque (8 ou 11 caractères, ex. <code>ABCDFRPPXXX</code>).
            </li>
        </ul>

        <h4 class="gw-section-subtitle"><span>6.</span> Numérotation des devis / propositions</h4>
        <ul class="gw-list">
            <li><strong>Format des numéros de devis / propositions *</strong><br />
                Définissez le modèle de numéro de vos devis. Par exemple :
                <ul>
                    <li><code>GW-DEV-{annee}-{compteur}</code></li>
                    <li><code>DEV-{annee}-{mois}-{compteur}</code></li>
                </ul>
                GestiWork utilisera l’année et un compteur automatique pour construire le numéro final.
            </li>
            <li><strong>Compteur courant</strong><br />
                Numéro en cours du compteur. Si vous commencez avec GestiWork, vous pouvez laisser « 1 ».
                Si vous migrez depuis un autre outil et que vos derniers devis allaient jusqu’à « 0050 », vous pouvez commencer à « 51 ».
            </li>
        </ul>

        <h4 class="gw-section-subtitle"><span>7.</span> Enregistrer vos modifications</h4>
        <ul class="gw-list">
            <li>Vérifiez que tous les champs marqués d’une astérisque rouge (*) sont bien remplis.</li>
            <li>Assurez-vous d’avoir renseigné au moins un numéro de téléphone (fixe ou portable).</li>
            <li>Cliquez sur le bouton <strong>« Enregistrer »</strong> au bas de la fenêtre.</li>
        </ul>

        <p class="gw-section-description">
            Si un champ obligatoire est manquant ou si aucun téléphone n’est renseigné, un message d’information s’affiche
            au bas de la fenêtre pour vous indiquer ce qui manque. Corrigez les éléments signalés, puis cliquez à nouveau
            sur <strong>« Enregistrer »</strong>.
        </p>
    </div>

    <div class="gw-settings-group gw-aide-section" id="gw-aide-options">
        <h4 class="gw-section-subtitle"><span>8.</span> Utiliser l’onglet « Options »</h4>
        <p class="gw-section-description">
            L’onglet <strong>« Options »</strong> permet d’affiner le comportement de GestiWork : année de début
            d’activité, champs additionnels sur les documents, durées et seuils techniques, ainsi que quelques
            réglages de taxonomie. Ces paramètres influencent la façon dont l’outil présente certaines informations
            à vos clients, stagiaires et formateurs.
        </p>

        <h4 class="gw-section-subtitle"><span>8.1</span> Accéder aux options et ouvrir la fenêtre d’édition</h4>
        <ul class="gw-list">
            <li>Depuis le menu <strong>Paramètres</strong> de GestiWork, sélectionnez l’onglet <strong>« Options »</strong>.</li>
            <li>La page présente plusieurs blocs récapitulatifs numérotés (2.1, 2.2, 2.3, 2.4).
                Ils affichent en lecture seule l’état actuel de vos réglages.</li>
            <li>Pour modifier ces réglages, cliquez sur le bouton <strong>« Modifier les options de cet onglet »</strong>.
                Une fenêtre d’édition s’ouvre avec l’ensemble des champs disponibles.</li>
        </ul>

        <h4 class="gw-section-subtitle"><span>8.2</span> Bloc 2.1 « Activité &amp; URLs de gestion »</h4>
        <ul class="gw-list">
            <li><strong>Année de début d’activité</strong><br />
                Indiquez l’année de création ou de démarrage de votre activité de formation (par exemple <code>2015</code>).
                Cette information est notamment utilisée pour certaines présentations et peut servir de repère dans vos
                rapports ou exports.
            </li>
            <li><strong>URLs de gestion (extranet, comptes, aide, exports)</strong><br />
                Ces liens représentent l’architecture cible de votre extranet (page de gestion principale, espace clients
                &amp; stagiaires, page d’aide centralisée, page d’exports, etc.).
                Ils servent de <em>référence fonctionnelle</em> pour la mise en place de GestiWork avec votre équipe technique
                ou votre prestataire web.
            </li>
        </ul>

        <h4 class="gw-section-subtitle"><span>8.3</span> Bloc 2.2 « Champs additionnels et comportements »</h4>
        <p class="gw-section-description">
            Ce bloc regroupe des <strong>cases à cocher</strong> qui activent ou désactivent des champs
            supplémentaires et certains comportements dans l’application. Selon vos besoins, vous pouvez
            garder un fonctionnement simple ou activer des options plus avancées.
        </p>
        <ul class="gw-list">
            <li><strong>Numéro de contrat pour les clients</strong><br />
                Lorsque cette option est activée, un champ spécifique permet de renseigner un numéro de contrat
                client. Ce numéro peut ensuite apparaître sur vos documents (devis, conventions, factures), ce qui
                facilite l’identification des dossiers pour vos clients et pour votre service administratif.
            </li>
            <li><strong>Période de validité des documents</strong><br />
                En activant cette option, vous pouvez définir et afficher une date de validité sur vos devis
                et documents à signer. Cela clarifie pour vos clients jusqu’à quelle date la proposition est valable.
            </li>
            <li><strong>Statut et code d’activité des formateurs</strong><br />
                Cette option ajoute des informations complémentaires sur les formateurs (statut, code d’activité).
                Elle est utile si vous devez distinguer différents types de statuts (salarié, indépendant, etc.) ou
                produire des synthèses plus fines pour vos bilans.
            </li>
            <li><strong>Durée des actions</strong><br />
                Permet de détailler la durée des sessions (par journée, demi-journée, etc.) au-delà du simple nombre d’heures.
                Cette option est utile si vous souhaitez un suivi plus précis du découpage des actions de formation.
            </li>
            <li><strong>Image de signature</strong><br />
                Lorsque l’option est activée, vous pouvez téléverser une image de signature (par exemple celle du responsable
                de l’organisme) qui pourra être intégrée sur certains documents PDF.
            </li>
            <li><strong>Connexion en tant que…</strong><br />
                Cette fonction permet à un administrateur autorisé de se connecter temporairement à la place d’un autre utilisateur.
                Elle est particulièrement utile pour tester une configuration ou assister un utilisateur en cas de difficultés.
                Pour des raisons de sécurité, n’activez cette option que si vous en avez réellement besoin.
            </li>
        </ul>

        <h4 class="gw-section-subtitle"><span>8.4</span> Blocs 2.3 et 2.4 : délais, seuils et taxonomies</h4>
        <ul class="gw-list">
            <li><strong>Délais et seuils</strong><br />
                Vous pouvez définir des durées (en heures ou en jours) utilisées par GestiWork pour certains automatismes :
                délai minimum entre deux demandes de signature, délai avant alerte sur la veille personnelle, durée de validité
                des liens de connexion, etc. Ces valeurs permettent d’adapter le fonctionnement de l’outil à vos pratiques internes.
            </li>
            <li><strong>Seuils financiers</strong><br />
                Des champs comme le tarif horaire plancher ou le pourcentage par défaut de l’acompte vous aident à cadrer vos
                propositions commerciales et à harmoniser les pratiques de facturation au sein de l’équipe.
            </li>
            <li><strong>Taxonomies et bilans</strong><br />
                Les taxonomies (catégories ou étiquettes) structurent vos formations et vos sessions. Choisissez le mode qui
                correspond le mieux à votre façon de classer vos actions (arborescence classique ou classification plus
                transversale). Une URL de page de présentation de bilan de compétences peut également servir de référence
                pour vos communications.
            </li>
        </ul>

    </div>

    <div class="gw-settings-group gw-aide-section" id="gw-aide-pdf">
        <h4 class="gw-section-subtitle"><span>9.</span> Utiliser l’onglet « Gestion PDF »</h4>
        <p class="gw-section-description">
            L’onglet <strong>« Gestion PDF »</strong> vous permet de définir l’apparence des documents générés par GestiWork
            (propositions, conventions, convocations, attestations, etc.). Vous pouvez créer plusieurs modèles, ajuster la
            mise en forme générale (marges, polices, couleurs) et personnaliser l’en-tête et le pied de page.
        </p>

        <h5 class="gw-section-subtitle"><span>9.1</span> Accéder à l’onglet et repérer les modèles</h5>
        <ul class="gw-list">
            <li>Depuis le menu <strong>Paramètres</strong> de GestiWork, sélectionnez l’onglet <strong>« Gestion PDF »</strong>.</li>
            <li>La partie haute (section <strong>3.1</strong>) affiche :
                <ul>
                    <li>un champ <strong>Nom du modèle PDF</strong> pour créer un nouveau modèle ;</li>
                    <li>la liste des <strong>modèles existants</strong>, chacun avec des icônes pour <em>apercevoir</em>,
                        <em>dupliquer</em>, <em>modifier</em> ou <em>supprimer</em> le modèle.</li>
                </ul>
            </li>
            <li>Lorsqu’un modèle est en cours de modification, son nom apparaît clairement en rouge, au centre de la zone 3.1.</li>
        </ul>

        <h5 class="gw-section-subtitle"><span>9.2</span> Créer un nouveau modèle PDF</h5>
        <ul class="gw-list">
            <li>Dans le champ <strong>Nom du modèle PDF</strong>, saisissez un nom explicite
                (par exemple « Proposition commerciale standard », « Convention sous-traitant », « Programme formation »).</li>
            <li>Cliquez sur le bouton <strong>« Créer »</strong> :
                <ul>
                    <li>les sections <strong>3.2 « Mise en forme PDF »</strong> et <strong>3.3 « En-tête & pied de page »</strong>
                        s’ouvrent ;</li>
                    <li>un bloc d’actions en bas de page affiche les boutons
                        <strong>« Annuler les modifications PDF »</strong> et <strong>« Enregistrer les réglages PDF »</strong>.</li>
                </ul>
            </li>
            <li>Le modèle n’est réellement enregistré qu’au clic sur <strong>« Enregistrer les réglages PDF »</strong>.</li>
        </ul>

        <h5 class="gw-section-subtitle"><span>9.3</span> Modifier, dupliquer ou supprimer un modèle existant</h5>
        <ul class="gw-list">
            <li><strong>Modifier</strong> : cliquez sur l’icône en forme de crayon. La page se recharge avec le modèle en cours
                d’édition, les sections 3.2 et 3.3 visibles, et le nom du modèle figé (non modifiable) en haut de la zone 3.1.</li>
            <li><strong>Dupliquer</strong> : cliquez sur l’icône de feuille. Une fenêtre vous propose un nouveau nom pour la copie
                (par défaut « &lt;Nom du modèle&gt; (copie) »). Le nouveau modèle reprend tous les réglages du modèle d’origine.
            </li>
            <li><strong>Supprimer</strong> : cliquez sur l’icône de corbeille. Une confirmation est demandée avant la suppression
                définitive du modèle.</li>
            <li><strong>Aperçu PDF</strong> : l’icône PDF ouvre un aperçu du modèle dans une nouvelle fenêtre. Cet aperçu contient
                un filigrane « APERÇU » et un contenu de démonstration pour visualiser la mise en page.</li>
        </ul>

        <h5 class="gw-section-subtitle"><span>9.4</span> Régler la mise en forme générale (section 3.2)</h5>
        <ul class="gw-list">
            <li><strong>Dimensions et marges</strong><br />
                Ajustez les marges <em>haut / bas / gauche / droite</em> ainsi que les hauteurs d’en‑tête et de pied de page
                (en millimètres). Ces valeurs définissent la zone dans laquelle le texte principal s’affichera.</li>
            <li><strong>Typographie et couleurs</strong><br />
                Choisissez la police et la taille pour les titres et le texte courant, ainsi que les couleurs de vos titres
                et sous‑titres. Vous pouvez également définir une couleur de fond pour l’en‑tête et le pied de page.</li>
            <li><strong>Feuille de style personnalisée (CSS)</strong><br />
                Cette zone avancée permet aux utilisateurs à l’aise avec le CSS d’affiner la mise en page
                (alignement du logo, colonnes dans l’en‑tête, bordures spécifiques, etc.). Le CSS saisi ici vient
                compléter les réglages précédents.</li>
        </ul>

        <h5 class="gw-section-subtitle"><span>9.5</span> Personnaliser l’en‑tête et le pied de page (section 3.3)</h5>
        <ul class="gw-list">
            <li>Dans la section <strong>3.3 « En‑tête & pied de page »</strong>, utilisez les boutons
                <strong>« Modifier le gabarit d’en‑tête »</strong> et <strong>« Modifier le gabarit de pied de page »</strong>
                pour ouvrir l’éditeur de contenu.</li>
            <li>Vous disposez d’un éditeur de texte enrichi (TinyMCE) pour saisir :
                <ul>
                    <li>le logo de l’organisme (via le shortcode <code>[of:logo]</code>) ;</li>
                    <li>les informations d’identité (raison sociale, coordonnées, habilitations, etc.) ;</li>
                    <li>les coordonnées de contact ou des mentions légales.</li>
                </ul>
            </li>
            <li>Vous pouvez insérer des <strong>shortcodes</strong> (mots‑clés entre crochets) pour afficher automatiquement des
                données (ex. <code>[of:raison_sociale]</code>, <code>[of:representant_legal]</code>, <code>[of:habilitation_inrs]</code>, etc.).
                La liste complète des shortcodes disponibles est affichée à côté de l’éditeur.</li>
            <li>Pour aller plus loin, l’en‑tête peut être découpé en <strong>3 zones logiques</strong> :
                <ul>
                    <li><code>[ZONE1]</code> : zone de gauche (souvent utilisée pour le logo) ;</li>
                    <li><code>[ZONE2]</code> : zone centrale (par exemple titre du document ou programme) ;</li>
                    <li><code>[ZONE3]</code> : zone de droite (par exemple coordonnées ou QR code).</li>
                </ul>
                Si vous ne mettez aucun de ces marqueurs, tout le contenu de l’en‑tête sera placé dans une seule zone par défaut.</li>
        </ul>

        <h5 class="gw-section-subtitle"><span>9.6</span> Enregistrer ou annuler vos réglages PDF</h5>
        <ul class="gw-list">
            <li>Après avoir ajusté la mise en forme et/ou l’en‑tête et le pied de page :
                <ul>
                    <li>cliquez sur <strong>« Enregistrer les réglages PDF »</strong> pour sauvegarder le modèle ;</li>
                    <li>ou cliquez sur <strong>« Annuler les modifications PDF »</strong> pour revenir à l’état précédemment sauvegardé.</li>
                </ul>
            </li>
            <li>Vous pouvez ensuite utiliser le bouton d’aperçu PDF de la liste des modèles pour vérifier le rendu
                avant de générer de vrais documents pour vos clients et stagiaires.</li>
        </ul>
    </div>
<?php endif; ?>
