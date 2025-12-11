# GestiWork ERP – Documentation Technique & Fil Conducteur Développement
**Version :** 0.5.0  
**Type :** Plugin WordPress autonome / ERP Formation  
**Objectif :** Gestion administrative, pédagogique, commerciale et qualité d’un Organisme de Formation (OF), utilisable également en **Sous-traitance pour un autre OF**.

---

## Table des matières

- **0. Objectifs du document**
- **1. Contexte & Vision**
- **2. Nouvel Élément : Utilisation en tant qu’OF ou Sous-Traitant**
- **3. Architecture des données (SQL `wp_gw_*`)**
- **4. Stack & Conventions**
- **5. Arborescence projet (Hexagonale)**
- **6. Roadmap de Développement**
- **7. Fichiers d’initialisation & scaffolding**
- **8. Modèle d’en-tête de licence pour les fichiers PHP**
- **9. Stratégie Produit : Core vs Pro & Capabilities**
- **10. État actuel (v0.5.0) – UI / Router / Aide / Paramètres / PDF**

# 0. Objectifs du document
Ce fichier est la **référence unique** pour développer GestiWork.  
Il regroupe :

- Contexte & vision  
- Architecture fonctionnelle  
- Architecture technique  
- Base de données (MCD + UML)  
- Roadmap structurée  
- Arborescence du projet  
- Fichiers d’initialisation  
- Checklists d’avancement  

Chaque section contient des **cases à cocher** pour suivre l’état du développement.

---

# 1. Contexte & Vision

**GestiWork** est un mini-ERP modulaire spécialisé pour les **Organismes de Formation**.  
Il remplace les Custom Post Types (CPT) par des **tables SQL dédiées** et sépare strictement la logique métier du noyau WordPress.  
Contrairement aux plugins WordPress classiques, GestiWork fonctionne comme un **“site dans le site”**, avec une architecture **DDD + Hexagonale** isolant complètement le métier du cœur WordPress.

### 🎯 Finalités principales
- Gestion académique : catalogue, sessions, formateurs  
- Gestion commerciale : propositions, conventions  
- Gestion administrative : convocations, émargements, attestations  
- Conformité réglementaire : Qualiopi & BPF  
- Portail extranet stagiaires / entreprises  
- Fonctionnement en **OF principal** ou **Sous-traitant**  

### 💡 Positionnement
- **Philosophie :** "Un site dans le site". Interface autonome (Extranet/Admin) isolée du thème WordPress.
- **Stratégie finance :** Gestion des Propositions Commerciales et Conventions, avec facturation finale externalisée (connecteur compta futur).
- **Cœur de valeur :** Automatisation des documents réglementaires et conformité Qualiopi/BPF.

### ⚙️ Périmètre fonctionnel (Scope)

#### A. Gestion Académique & RH
- **Catalogue :** Programmes (Objectifs, Pré-requis, Public, Modalités).
- **Sessions :** Planification (Dates, Lieux, Formateurs).
- **Ressources :** Gestion Formateurs (Validité compétences, NDA).

#### B. Gestion Commerciale (Offres & Contrats)
- **Propositions commerciales :** Devis formatés "Formation" (TVA spécifique ou exonération art. 261-4-4).
- **Package OF :** Envoi automatique de la "Proposition + Programme + Règlement Intérieur".
- **Conventions :** Génération du contrat légal (Convention Professionnelle ou Contrat Individuel).
- **Connecteur futur :** Préparation des données pour export vers logiciel comptable (JSON/API).

#### C. Administratif & BPF (Cœur réglementaire)
- **Suivi de l'action :** Convocations, Émargements (Numérique/Papier), Attestations de fin.
- **Données BPF :** Collecte obligatoire à l'inscription (CSP, Niveau d'entrée, Type financement : OPCO/Entreprise/Perso).
- **Traçabilité :** Archivage des preuves pour l'audit Qualiopi.

#### D. Extranet & LMS Lite
- **Portail stagiaire :** Accès sécurisé aux documents et convocations.
- **Pédagogie :** Mise à disposition des supports de cours et Quiz d'évaluation.
- **Qualité :** Questionnaires à chaud (J+0) et à froid (J+90).

---

# 2. Nouvel Élément : Utilisation en tant qu’OF ou Sous-Traitant

### Modes d’exploitation :
1. **Mode OF Principal**  
   → L’organisme gère son catalogue, ses sessions, ses documents et ses stagiaires.

2. **Mode Sous-traitant**  
   → L’organisme intervient pour le compte d’un autre OF.  
   → Impacts :
   - Les **documents** doivent afficher les informations de l’OF donneur d’ordre.  
   - Les **sessions** peuvent appartenir soit à l’OF local, soit à l’OF principal.  
   - Les **conventions**, **émargements**, **attestations** doivent être générés selon le rôle :  
     - Sous-traitant = exécution  
     - Donneur d’ordre = responsabilité juridique  

### Checklist
- [ ] Mise en place du modèle de données (champ `mode_of` + table partenaire OF)  
- [ ] Ajustement de la génération documentaire  
- [ ] Gestion permissions & context switching  
- [ ] Paramètre global (OF / Sous-traitant)

---

# 3. Architecture des données (SQL `wp_gw_*`)

## Tables principales
- `wp_gw_organismes` — infos légales OF  
- `wp_gw_formations` — catalogue  
- `wp_gw_sessions` — planning  
- `wp_gw_tiers` — clients / financeurs / OF donneur d’ordre  
- `wp_gw_stagiaires` — apprenants (BPF)  
- `wp_gw_inscriptions` — pivot session/stagiaire  
- `wp_gw_proposals` — propositions commerciales  
- `wp_gw_conventions` — conventions signées  
- `wp_gw_documents` — GED PDF  
- `wp_gw_logs` — audit trail Qualiopi  

### Checklist
- [ ] Migration SQL générée  
- [x] Installation plugin  
- [ ] Requêtes CRUD basiques  
- [ ] Repositories DDD  

---

# 4. Stack & Conventions

- PHP 8.0+ (strict types)  
- Architecture Hexagonale + DDD  
- BDD MySQL avec tables custom préfixées `wp_gw_`  
- Aucune donnée métier dans WordPress (`post`, `postmeta`)  
- Namespace racine : `GestiWork\`  
- Préfixe CSS : `.gw-`  
- Interface isolée via router interne `/gestiwork/`

### Conventions de nommage

| Type | Format | Exemple |
| :--- | :--- | :--- |
| **Namespace** | `GestiWork\\` | `GestiWork\\Domain\\Commercial\\Proposal` |
| **Tables SQL** | `wp_gw_` | `wp_gw_proposals`, `wp_gw_conventions` |
| **Classes CSS** | `.gw-` | `.gw-modal`, `.gw-table` |

### Checklist
- [x] Bootstrapper  
- [x] Router interne  
- [x] Namespace organisé  
- [x] UI isolée du thème WP  

---

# 5. Arborescence projet (Hexagonale)

- `assets/css`
- `assets/js`
- `assets/img`
- `config/`
- `templates/layouts`
- `templates/admin`
- `templates/erp/dashboard`
- `templates/erp/commercial`
- `templates/erp/academic`
- `templates/erp/tiers`
- `templates/extranet`
- `src/Domain/Catalog`
- `src/Domain/Planning`
- `src/Domain/Tiers`
- `src/Domain/Commercial`
- `src/Domain/Quality`
- `src/Infrastructure/Database`
- `src/Infrastructure/Pdf`
- `src/Infrastructure/Export`
- `src/Infrastructure/Mailer`
- `src/UI/Admin`
- `src/UI/Router`
- `src/UI/Controller`

---

# 6. Roadmap de Développement

### 🏁 Phase 1 : Fondations
- [x] **1.1 Structure :** Composer, Namespaces, Arborescence.
- [x] **1.2 Router :** Interception URL `/gestiwork/` (bypass thème WP).
- [ ] **1.3 Base :** Installateur SQL des tables de configuration (`organismes`).
- [ ] **1.4 Mode OF / Sous-traitant :** Paramètre global et logique de contexte.

### 🧱 Phase 2 : Catalogue & CRM (Données Maîtres)
- [ ] **2.1 Catalogue :** CRUD Formations + génération PDF Programme.
- [ ] **2.2 Tiers :** Gestion Entreprises & Financeurs.
- [ ] **2.3 Stagiaires :** Fiche complète avec champs BPF obligatoires.

### 🤝 Phase 3 : Commercial & Contractualisation
- [ ] **3.1 Moteur de Devis :** Création d'une proposition (Sélection Formation + Tarif).
- [ ] **3.2 Documents :** Génération PDF "Proposition" et "Convention" (OF / Sous-traitant).
- [ ] **3.3 Workflow :** Statuts (Brouillon -> Envoyé -> Signé/Validé).

### 📅 Phase 4 : Sessions & Suivi
- [ ] **4.1 Planning :** Affectation Formateur & Salles.
- [ ] **4.2 Inscriptions :** Lier Stagiaire -> Session (via Convention signée).
- [ ] **4.3 Administratif :** Génération Convocations & Émargements.
- [ ] **4.4 Attestations :** Attestations de fin de formation.

### 🚀 Phase 5 : Extranet & Qualité
- [ ] **5.1 Portail Stagiaire :** Vue "Mes Formations" et accès aux documents.
- [ ] **5.2 Questionnaires :** Envoi auto des liens d'évaluation (J+0 & J+90).
- [ ] **5.3 Pédagogie :** Mise à disposition des supports de cours et Quiz d'évaluation.

### 🔌 Phase 6 : API & Export (Futur)
- [ ] **6.1 Export Compta :** Génération CSV/JSON des propositions validées pour import compta.

---

# 7. Fichiers d’initialisation & scaffolding

### 7.1 Script `install.py`

- Rôle : script de scaffolding à lancer une fois en développement pour créer la structure initiale du plugin.  
- Actions principales :  
  - Crée l'arborescence décrite en section 5.  
  - Génère le fichier `composer.json`.  
  - Génère le fichier principal WordPress `gestiwork.php`.  
  - Crée `assets/css/gw-app.css` avec un style de base isolant `.gw-app`.  
  - Crée un squelette `src/Infrastructure/Database/Installer.php`.  
- Une fois la structure créée et versionnée, ce script peut être supprimé du projet.

### 7.2 Fichier principal `gestiwork.php`

- Point d'entrée du plugin côté WordPress.  
- Contient le header standard (nom du plugin, description, version, auteur, text domain).  
- Vérifie la constante `ABSPATH` puis charge `vendor/autoload.php` si disponible.  
- Définit les constantes :  
  - `GW_PLUGIN_DIR`  
  - `GW_PLUGIN_URL`  
  - `GW_VERSION`  
- Doit à terme appeler le bootstrapper applicatif :  
  - `\GestiWork\Infrastructure\Bootstrapper::init()` (TODO).

#### Garde-fou version PHP (à intégrer dans `gestiwork.php`)

Exemple de vérification simple pour refuser l'activation si la version de PHP est inférieure à **8.0** :

```php
if (version_compare(PHP_VERSION, '8.0', '<')) {
    if (is_admin()) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error"><p>';
            echo esc_html__('Le plugin GestiWork nécessite PHP 8.0 ou supérieur.', 'gestiwork');
            echo '</p></div>';
        });
    }

    // Empêcher l'exécution du plugin si la version de PHP est trop basse
    return;
}
```

### 7.3 Fichier `composer.json`

- Gère les dépendances PHP du plugin.  
- Déclare le namespace racine `GestiWork\` pour l'autoloading (PSR-4).  
- Sert de référence pour l'organisation du code dans `src/`.

### 7.4 Installateur SQL `Installer.php`

- Emplacement : `src/Infrastructure/Database/Installer.php`.  
- Contient la classe `GestiWork\\Infrastructure\\Database\\Installer`.  
- Rôle : centraliser la création et la mise à jour des tables `wp_gw_*` (TODO : implémentation à compléter).

---

## 8. Modèle d’en-tête de licence pour les fichiers PHP

À utiliser en tête des fichiers PHP du plugin (y compris, plus tard, `gestiwork.php`), en adaptant si besoin la description :

```php
/**
 * Plugin Name: GestiWork ERP
 * Description: ERP pour Organismes de Formation (OF) sur WordPress : gestion académique, commerciale, administrative et Qualiopi/BPF.
 * Plugin URI: https://example.com/gestiwork
 * Version: 0.5.0
 * Author: LAURET Fabrice
 * Author URI: https://example.com
 * Text Domain: gestiwork
 * Domain Path: /languages
 * License: GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
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
```

---

## 9. Stratégie Produit : Core vs Pro & Capabilities

### 9.1 Modèle Core / Pro

- **GestiWork Core**  
  - Version utilisable telle quelle, pensée pour un usage **test / POC** ou pour de petits OF.  
  - Comporte des **limitations fonctionnelles** possibles, par exemple :  
    - nombre limité de clients / sessions / formations,  
    - absence de certains exports avancés (Excel/CSV, exports comptables),  
    - fonctionnalités réduites pour le mode Sous-traitant ou le multi-organisme.  

- **GestiWork Pro**  
  - Débloque certaines limitations de Core.  
  - Active des fonctionnalités complémentaires : exports avancés, connecteurs API, options multi-OF / Sous-traitant étendues, automatisations supplémentaires, etc.  
  - L’activation Pro se fait via une **licence de services** (clé de licence).

### 9.2 Licence de services GestiWork

- Le code du plugin reste sous **GPLv3+** (voir fichier `LICENSE`).  
- Une **licence de services GestiWork** porte uniquement sur :  
  - l’accès aux **mises à jour officielles**,  
  - l’accès au **support** fonctionnel et technique,  
  - l’activation des **fonctionnalités Pro** via une clé de licence,  
  - et, le cas échéant, l’accès à des **services distants** (API / SaaS).  
- Par défaut, une licence de services est associée à **1 domaine de production**, avec expiration après **12 mois** (renouvelable).  
- Pour le détail complet du modèle commercial et des conditions, se référer à `LICENSE_COMMERCIALE.md`.

### 9.3 Orientations de développement (Capabilities / Plan)

Pour garder un code propre et séparer Core / Pro :

- Introduire une abstraction de type `Capabilities` ou `Plan` (par exemple dans le domaine ou l’infrastructure) qui expose des méthodes métier :  
  - `canExportCompta()`  
  - `supportsMultiOrganisme()`  
  - `hasAdvancedSubcontractorFeatures()`  
  - etc.  
- En mode **Core**, cette abstraction renvoie `false` pour les capacités Pro ; en mode **Pro**, elle renvoie `true` si la **clé de licence** est valide.  
- Le code applicatif ne doit **pas** disperser des vérifications `if ($isPro)` partout, mais plutôt interroger `Capabilities`.  
- Les limites Core (ex. nombre max de clients) doivent être centralisées via ces capacités / règles, et non en dur dans chaque contrôleur.

Ainsi, le fil conducteur de développement est :

- Concevoir le **périmètre complet** (Core + Pro) au niveau du domaine.  
- Implémenter d’abord une version **Core** stable, en prévoyant dès le départ les points d’extension via `Capabilities`.  
- Ajouter progressivement les fonctionnalités **Pro** derrière ces capacités, sans casser la version Core ni dupliquer la logique métier.

---

# 10. État actuel (v0.5.0) – UI / Router / Aide / Paramètres / PDF

Cette section synthétise l'état réel de l'interface au fur et à mesure du développement, afin d'avoir un **point d'entrée unique** pour les URLs et les écrans déjà implémentés.

## 10.1 Router interne & URLs propres

Le router `GestiWork\UI\Router\GestiWorkRouter` intercepte désormais les URLs publiques suivantes :

- `/gestiwork/`  
  → Entrée principale du « site dans le site » GestiWork (dashboard interne).

- `/gestiwork/settings/` + section  
  → Vue **Paramètres** (admin uniquement), onglet déterminé par `gw_section` :
  - `/gestiwork/settings/general/`  
    → Onglet **« Général & Identité »** actif.
  - `/gestiwork/settings/options/`  
    → Onglet **« Options »** actif, avec stockage des options dans la table `gw_options` et redirections propres sans paramètres `gw_updated`.
  - `/gestiwork/settings/pdf/` (et alias `gestionpdf`, `gestion-pdf`)  
    → Onglet **« Gestion PDF »** actif.

En interne, les segments sont remontés dans les query vars WordPress :

- `gw_view` : `dashboard` / `settings` / `Aide` (vue principale)  
- `gw_section` : `general`, `options`, `pdf` (et alias) pour les paramètres.

## 10.2 Page d’aide GestiWork

Fichier principal : `templates/erp/aide/view-aide.php` + sous-sections dans `templates/erp/aide/sections/`.

Comportement :

- Sommaire en haut de page avec ancres :
  - `#gw-aide-introduction`
  - `#gw-aide-demarrage`
  - `#gw-aide-configuration` (visible uniquement pour les admins)
  - `#gw-aide-options` (section dédiée à l’onglet « Options » des paramètres, visible uniquement pour les admins)
  - `#gw-aide-quotidien`
  - `#gw-aide-faq`
- Les sections de contenu détaillées sont dans des fichiers dédiés :
  - `section-introduction.php`
  - `section-demarrage.php`
  - `section-configuration.php` (guide détaillé de l’onglet « Général & Identité » **et** de l’onglet « Options » via la sous-section `#gw-aide-options`)
  - `section-quotidien.php`
  - `section-faq.php`
- Comportement UX :
  - Les sections détaillées sont masquées par défaut.
  - Un clic dans le sommaire affiche uniquement la section demandée.
  - L’URL peut pointer directement sur une section via l’ancre HTML (ex. `https://audixor.fr/gestiwork/Aide/#gw-aide-faq`).
  - **Nouveau :** support de `gw_section` pour ouvrir une section dès le chargement via une URL type :
    - `/gestiwork/Aide/introduction/`
    - `/gestiwork/Aide/demarrage/`
    - `/gestiwork/Aide/configuration/`
    - `/gestiwork/Aide/options/`
    - `/gestiwork/Aide/quotidien/`
    - `/gestiwork/Aide/faq/`

## 10.3 Onglet « Général & Identité » (Paramètres)

Fichier : `templates/erp/settings/view-settings.php`.

État fonctionnel principal :

- Recap des informations d’identité, de coordonnées, de fiscalité, de numérotation, déjà **alimenté par la base** (`gw_of_identity`) via `SettingsProvider::getOfIdentity()`.
- Modal d’édition « Général & Identité » avec :
  - Champs obligatoires marqués d’une astérisque rouge.
  - Règle métier : au moins **un** des deux téléphones (fixe ou portable) doit être renseigné.
  - Vérifications côté JS au submit + messages d’erreur clairs dans la modale.
  - Normalisation des formats (téléphone, SIRET/SIREN, IBAN, BIC).
- Gestion spécifique du **logo GestiWork** :
  - Utilisation de la médiathèque WordPress (`wp.media`).
  - Prévisualisation du logo dédié à l’ERP.
  - Soumission automatique du formulaire au choix du logo pour sauvegarde immédiate.

## 10.4 Onglet « Options » – État actuel

L’onglet **« Options »** des paramètres GestiWork est désormais partiellement implémenté :

- **URL de référence** pour accéder directement à cet onglet :  
  `https://audixor.fr/gestiwork/settings/options/`
- **Stockage** : les options techniques sont centralisées dans la table `gw_options`, exposées côté code via `OptionsProvider` et gérées en écriture par `SettingsController::handlePost()` (action `save_options`).
- **UI** :
  - Bloc 2.1 « Activité & URLs de gestion » (année de début d’activité, URLs cibles pour l’extranet GestiWork).
  - Bloc 2.2 « Champs additionnels et comportements » (cases à cocher permettant d’activer ou non des champs et comportements spécifiques : numéro de contrat client, date de validité, statut formateur, durée des actions, image de signature, impersonation, etc.).
  - Blocs 2.3 et 2.4 pour les délais, seuils et taxonomies (affichage déjà en place, raffinements à prévoir côté domaine/contrôleurs si besoin).
- **Redirections après sauvegarde** :
  - Lors de la validation de la modale de l’onglet Options, l’utilisateur reste bien sur l’URL propre `/gestiwork/settings/options/` **sans** ajout de paramètres de type `gw_updated`.
  - Même logique pour l’onglet Général (`/gestiwork/settings/general/`).
- **Aide intégrée** : la page `/gestiwork/Aide/` dispose d’une section dédiée `#gw-aide-options` (sommaire + ancre) décrivant le fonctionnement de l’onglet Options pour un utilisateur non technique.

Cette section du README doit continuer à être mise à jour au fil des itérations (ajout de nouvelles options, comportements, validations, etc.) afin de rester alignée sur l’état réel de l’interface.

## 10.5 Onglet « Gestion PDF » – État actuel

Fichiers principaux :

- `templates/erp/settings/view-settings.php` (onglet **Gestion PDF**)
- `src/UI/Controller/PdfPreviewController.php` (aperçu PDF par modèle)
- `src/Infrastructure/Database/Installer.php` (tables `gw_pdf_templates` et `gw_of_identity`)
- `src/Domain/Pdf/PdfShortcodeCatalog.php` / `src/Infrastructure/Database/ShortcodeSeeder.php`
- `assets/css/gw-pdf.css` (feuille de style générique PDF)
- `assets/css/gw-layout.css` (layout de la liste des modèles + nom du modèle en cours d’édition)

### 10.5.1 Modèles PDF & UI de gestion

- Accès : `/gestiwork/settings/pdf/` (et alias `gestionpdf`, `gestion-pdf`).
- La section **3.1 Nom du modèle PDF** permet :
  - de créer un nouveau modèle via un champ texte `gw_pdf_model_name` accompagné de boutons **Créer / Annuler** ;
  - de lister les **modèles existants** avec actions : aperçu, duplication, modification, suppression ;
  - d’indiquer clairement le modèle en cours d’édition (titre 3.1a rouge + nom centré en `.gw-pdf-current-template-name`).
- UX :
  - par défaut (aucun modèle sélectionné), les blocs **3.2 Mise en forme PDF**, **3.3 En-tête & pied de page** et le bloc d’actions (**Annuler les modifications PDF / Enregistrer les réglages PDF**) sont **masqués** ;
  - ils sont affichés lorsque :
    - on clique sur **Créer** avec un nom de modèle non vide ;
    - on clique sur l’icône **modifier** d’un modèle existant ;
    - après duplication puis sélection du modèle dupliqué ;
  - le bouton **Enregistrer les réglages PDF** reste le point d’entrée unique pour la sauvegarde (action `save_pdf_template`).

### 10.5.2 Mise en forme PDF (3.2) – Layout option 1

- La section **3.2 Mise en forme PDF** expose les champs techniques stockés dans `gw_pdf_templates` :
  - marges (`margin_top/bottom/left/right` en mm) ;
  - hauteurs fixes d’en-tête et de pied de page (`header_height`, `footer_height` en mm) ;
  - familles de polices titres / corps (`font_title`, `font_body`) + tailles (`font_title_size`, `font_body_size`) ;
  - couleurs des titres (`color_title`, `color_other_titles`) ;
  - couleurs de fond d’en-tête et de pied (`header_bg_color`, `footer_bg_color`, avec support de `transparent`).
- `PdfPreviewController` utilise **l’option 1 de layout** :
  - en-tête et pied de page sont positionnés en `position:fixed` dans les marges de page, avec hauteurs strictes (tout dépassement est masqué) ;
  - le corps du document s’écoule entre ces deux bandes via la règle `@page margin` ;
  - la feuille `assets/css/gw-pdf.css` contient les styles génériques imprimables, puis chaque modèle injecte son CSS dynamique (marges, polices, couleurs, hauteurs) et enfin un **CSS personnalisé** libre.
- Un champ `custom_css` (type `LONGTEXT`) est disponible et éditable via **« Feuille de style personnalisée (CSS) »** :
  - stocké dans `gw_pdf_templates.custom_css` ;
  - injecté tel quel en fin de `<style>` dans `buildPdfHtml()` ;
  - permet de gérer finement la mise en page (par ex. alignement du logo, entête 3 zones, etc.).

### 10.5.3 En-tête & pied de page (3.3) – Gabarits et shortcodes

- La section **3.3 En-tête & pied de page** ouvre un éditeur TinyMCE (zone 3.x) piloté par :
  - un champ caché `gw_pdf_header_html` (HTML de l’en-tête) ;
  - un champ caché `gw_pdf_footer_html` (HTML du pied de page) ;
  - un sélecteur de contexte (`gw_pdf_editor_context`) pour charger/sauver header ou footer.
- `PdfPreviewController::buildPdfHtml()` :
  - remplace les shortcodes par leurs valeurs via `replaceShortcodes()` (OF, client, session, stagiaire, formateur…) ;
  - génère un HTML complet d’aperçu avec filigrane **« APERÇU »** et un bloc de démonstration pour le corps.
- **Shortcodes Organisme** :
  - `of:representant_legal` s’appuie désormais sur deux colonnes dédiées dans `gw_of_identity` : `representant_nom` + `representant_prenom` (concaténés en « Prénom Nom ») ;
  - **nouveau** : `of:habilitation_inrs` (texte libre saisi dans l’onglet Général & Identité) est ajouté au catalogue (`PdfShortcodeCatalog`) et synchronisé en BDD (`gw_pdf_shortcodes`) ;
  - ces shortcodes sont listés dans la section **3.3 – Mots-clés disponibles : Organisme de formation**.

### 10.5.4 Entête PDF en 3 zones (ZONE1 / ZONE2 / ZONE3)

- `PdfPreviewController` supporte désormais un découpage logique de l’en-tête en **3 zones** :
  - `ZONE1` (gauche) – typiquement logo OF ;
  - `ZONE2` (centre) – par ex. titre du document ou programme de formation ;
  - `ZONE3` (droite) – coordonnées / bloc texte ou QR code.
- Mécanisme :
  - le champ brut `header_html` (saisi dans l’éditeur 3.3) peut contenir des marqueurs `[ZONE1]`, `[ZONE2]`, `[ZONE3]` ;
  - la méthode privée `splitHeaderZones()` découpe ce contenu en 3 segments avant remplacement des shortcodes ;
  - si **aucun** marqueur n’est présent, tout le contenu est automatiquement placé en **zone 1** (comportement rétrocompatible) ;
  - dans le HTML généré, l’en-tête est rendu comme :
    - `<div class="pdf-header-zone pdf-header-zone-1">…</div>`
    - `<div class="pdf-header-zone pdf-header-zone-2">…</div>`
    - `<div class="pdf-header-zone pdf-header-zone-3">…</div>`
  - ce qui permet de contrôler la mise en page via `custom_css` (flex, table, positions absolues, etc.).

### 10.5.5 Nouveaux champs Identité OF et stockage

- La table `gw_of_identity` contient désormais, en plus des champs existants :
  - `representant_nom` (`VARCHAR(190)`) ;
  - `representant_prenom` (`VARCHAR(190)`) ;
  - `habilitation_inrs` (`VARCHAR(190)`).
- `Installer::install()` crée ces colonnes sur une nouvelle installation, et `runMigrations()` les ajoute si la table existe déjà.
- `SettingsProvider::getOfIdentity()` expose ces valeurs avec des défauts vides et une clé virtuelle `representant_legal`.
- `SettingsController::handlePost()` (action `save_of_identity`) lit/sauvegarde ces champs depuis la modale **Général & Identité**.
- L’onglet **Général & Identité** affiche :
  - une **card Représentant légal** (nom + prénom concaténés) ;
  - une **card Habilitation INRS**.

### 10.5.6 Checklist PDF (v0.5.0)

- [x] Aperçu PDF par modèle via `PdfPreviewController` avec filigrane « APERÇU ».
- [x] Layout **option 1** stabilisé (hauteurs fixes header/footer, corps entre les marges).
- [x] Feuille de style générique `assets/css/gw-pdf.css` + CSS dynamique par modèle.
- [x] Champ `custom_css` stocké en base et injecté après les réglages génériques.
- [x] UI de gestion des modèles : création (Nom + Créer/Annuler), liste éditer/dupliquer/supprimer, actions regroupées.
- [x] Masquage par défaut des sections **3.2** / **3.3** / actions, affichage en création/édition.
- [x] Support de l’entête en **3 zones** `[ZONE1]`, `[ZONE2]`, `[ZONE3]` dans `header_html`.
- [x] Ajout des champs `representant_nom`, `representant_prenom`, `habilitation_inrs` dans `gw_of_identity` + migration.
- [x] Mise à jour des shortcodes OF (`of:representant_legal`, `of:habilitation_inrs`).
- [ ] Génération PDF finale des documents métier (propositions, conventions, convocations, attestations).

Cette sous-section doit continuer à être complétée au fil des itérations (nouveaux types de documents PDF, options de layout, shortcodes supplémentaires, etc.) pour rester fidèle à l’état réel de la génération PDF et de l’UI associée.