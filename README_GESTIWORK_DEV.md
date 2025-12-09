# GestiWork ERP – Documentation Technique & Fil Conducteur Développement
**Version :** 0.4.0  
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
 * Version: 0.4.0
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

# 10. État actuel (v0.4.0) – UI / Router / Aide / Paramètres

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
    → Onglet **« Options »** actif.  
    → **C’est le prochain gros chantier de développement fonctionnel.**
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
  - `#gw-aide-quotidien`
  - `#gw-aide-faq`
- Les sections de contenu détaillées sont dans des fichiers dédiés :
  - `section-introduction.php`
  - `section-demarrage.php`
  - `section-configuration.php` (guide détaillé de l’onglet « Général & Identité »)
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

## 10.4 Prochain focus : Onglet « Options »

Prochaine étape déclarée de développement front + back :

- Travailler l’onglet **« Options »** des paramètres GestiWork :
  - URL de référence pour accéder directement à cet onglet :  
    `https://audixor.fr/gestiwork/settings/options/`
  - Objet :
    - Structurer et persister les **options générales** (pages, comportements, quotas, seuils…).
    - Faire correspondre les sections déjà décrites dans la maquette (pages d’extranet, délais, limites, taxonomies, etc.) avec un stockage réel dans la base + contrôleurs dédiés.

Cette section doit être mise à jour régulièrement pour refléter l’état **réel** de l’interface au fil des commits.