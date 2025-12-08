<?php if (current_user_can('manage_options')) : ?>
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
<?php endif; ?>
