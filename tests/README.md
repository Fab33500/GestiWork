# Tests (développement)

Ce dossier contient un test automatisé **local** destiné aux développeurs.

## Pré-requis

- PHP >= 8.0
- Dépendances Composer installées (`vendor/` présent)

## Exécuter le test

À la racine du plugin :

```bash
php tests/run.php
```

## Ce que teste `tests/run.php`

Le script exécute des assertions sur les Providers (couche domaine), avec un `wpdb` **stub** (simulé) :

- Création d’un **client particulier** avec `cp` et `ville`
- Création d’un **client entreprise** (données “type INSEE”) avec :
  - `cp` / `ville` (libellé)
  - `forme_juridique` (libellé)
- Création de **2 contacts** liés au client entreprise et vérification que la lecture retourne bien 2 lignes

La sortie est volontairement verbeuse (IDs créés, validations) et se termine par :

- `OK` si tout passe
- `FAIL: ...` et un code retour `1` si une assertion échoue

## Portée / limites

- Ce test **ne teste pas** l’interface (JavaScript), ni les appels réseau (INSEE / geo.api.gouv.fr).
- Ce test **ne teste pas** un vrai WordPress ni une vraie base MySQL (pas de WP test suite).
- L’objectif est d’avoir un **smoke test rapide** pour éviter les régressions sur la création Tiers/Contacts.
