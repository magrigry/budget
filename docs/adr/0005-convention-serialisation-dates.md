---
status: accepted
date: 2026-06-17
---

# Convention de sérialisation des dates

## Contexte

Les dates transitent entre le backend PHP et les composants Vue via Inertia (JSON). Il faut décider d'un format de sérialisation et de la responsabilité de formatage côté frontend.

## Options envisagées

* **Objet `Date` TypeScript** — transformer les strings reçues en objets `Date` via un DTO dans chaque composant.
* **String ISO 8601 complète** — ex. `"2024-01-15T00:00:00.000000Z"`, laisser le frontend parser.
* **String `Y-m-d`** — format machine lisible, compatible `<input type="date">`, formatage display délégué à un composable.

## Décision

Le backend sérialise toutes les dates en `Y-m-d` (ex. `"2024-01-15"`). Le frontend affiche via `formatDate()` pour les humains, et utilise la string brute pour les `<input type="date">`.

## Justification

JSON n'a pas de type `Date` — les dates arrivent toujours comme strings. Avec Inertia, les props sont injectées automatiquement sans couche de fetch explicite, donc il n'y a pas d'endroit naturel pour brancher une transformation DTO. Forcer la conversion string → `Date` dans chaque composant crée du boilerplate, et les deux usages principaux (affichage via `Intl.DateTimeFormat`, binding sur `<input type="date">`) fonctionnent avec des strings.

Le format `Y-m-d` est déclaré une fois dans le cast du modèle (`'transacted_at' => 'date:Y-m-d'`) et s'applique automatiquement via `toArray()`.

## Conséquences

* Les interfaces TypeScript typent les dates comme `string` — c'est le type réel, pas un raccourci.
* `formatDate()` (`resources/js/composables/useFormatDate.ts`) est le point d'entrée unique pour l'affichage localisé.
* Si des calculs de durée ou des comparaisons de dates côté front deviennent nécessaires, réévaluer avec un branded type (`type IsoDateString = string & { readonly __brand: 'IsoDate' }`) ou un DTO localisé.
