# GestiWork ERP

GestiWork est un mini-ERP modulaire pour **Organismes de Formation (OF)**, distribué sous forme de **plugin WordPress autonome**.

Il couvre la gestion :

- **Académique** : catalogue, sessions, formateurs.
- **Commerciale** : propositions, conventions.
- **Administrative & BPF** : convocations, émargements, attestations, données réglementaires.
- **Extranet** : portail stagiaire / entreprise.

Le plugin est conçu pour fonctionner aussi bien en **OF principal** qu'en **sous-traitant** d'un autre OF.

---

## Stack technique

- **WordPress** (plugin autonome).
- **PHP 8.0+** (version minimale requise) avec typage strict. Testé en PHP 8.3.25.
- **MySQL** avec tables dédiées préfixées `wp_gw_` (aucune donnée métier dans `post` / `postmeta`).
- **Architecture Hexagonale + DDD** (Domain-Driven Design) avec cœur métier isolé.

---

## Structure du projet (vue rapide)

Quelques dossiers clés :

- `assets/` : ressources front (CSS, JS, images).
- `templates/` : vues pour l'admin ERP et l'extranet.
- `src/Domain/` : cœur métier (Catalogue, Tiers, Commercial, Quality, etc.).
- `src/Infrastructure/` : base de données, PDF, export, mailer.
- `src/UI/` : couches d'interface (Admin, Router, Controller).

Pour l'arborescence complète et détaillée, voir la section dédiée dans la documentation technique.

---

## Documentation technique détaillée

Le fichier principal de documentation pour le développement est :

- `README_GESTIWORK_DEV.md`

Il décrit :

- Le **contexte & la vision** du projet.
- Le **périmètre fonctionnel** (Académique, Commercial, Administratif, Extranet).
- L'**architecture des données** (tables `wp_gw_*`).
- La **stack & les conventions** (namespaces, CSS, noms de tables).
- L'**arborescence du projet**.
- La **roadmap de développement** avec cases à cocher.
- Les **fichiers d'initialisation & scaffolding** (script `install.py`, `gestiwork.php`, `composer.json`, `Installer.php`).

---

## Installation rapide

### Prérequis

- WordPress 6.x ou supérieur.
- PHP 8.0+.
- MySQL compatible avec les tables custom préfixées `wp_gw_`.
- Accès administrateur à l’interface WordPress.

### Étapes d’installation (comme un plugin WordPress classique)

1. Récupérer l’archive ZIP du plugin GestiWork (par exemple depuis la page "Releases" de ce dépôt).
2. Dans l’admin WordPress, aller dans : **Extensions → Ajouter → Téléverser une extension**.
3. Sélectionner le fichier ZIP du plugin puis cliquer sur **Installer**.
4. Une fois l’installation terminée, cliquer sur **Activer l’extension**.

GestiWork sera alors disponible comme n’importe quel autre plugin WordPress. La configuration métier et les pages ERP se feront ensuite via l’interface d’administration du plugin.

Pour le setup développeur (structure, Composer, base de données, etc.), se référer à `README_GESTIWORK_DEV.md`.

---

## Statut du projet

Le projet est en cours de développement actif. Les fonctionnalités et la structure peuvent encore évoluer.

Référez-vous toujours à `README_GESTIWORK_DEV.md` pour les informations les plus à jour sur le scope et les décisions techniques.

---

## Modèle commercial

- Le plugin **GestiWork Core** est utilisable tel quel, avec certaines limites pensées pour un usage **test / POC**.
- Une **licence de services GestiWork** permet de :
  - débloquer des fonctionnalités **complémentaires / Pro**,
  - bénéficier des **mises à jour officielles**,
  - accéder au **support** fonctionnel et technique.
- Ces conditions concernent les **services** (clé de licence, updates, support) et ne restreignent pas vos droits GPL sur le code.

Les détails sont décrits dans le fichier `LICENSE_COMMERCIALE.md`.

---

## Licence

GestiWork ERP est distribué sous licence **GNU General Public License, version 3 ou ultérieure (GPLv3+)**.

- Vous êtes libre d'utiliser, d'étudier, de modifier et de redistribuer le code, conformément à la GPL.
- Le texte complet de la licence est disponible dans le fichier `LICENSE` à la racine du projet.

Les conditions commerciales éventuelles (support, mises à jour, services) viennent **en complément** de la GPL et ne restreignent pas les droits accordés par celle-ci sur le code.

