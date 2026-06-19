---
status: accepted
date: 2026-06-17
---

# Sérialisation des données Inertia via toArray()

## Contexte

Les controllers Inertia construisent des tableaux de données à passer aux composants Vue. La question est de savoir où définir la forme exacte (shape) de ces données et comment gérer le formatage (dates, enums).

## Options envisagées

* **Tableaux inline dans les controllers** — `['id' => $model->id, ...]` avec formatage explicite (`->format('Y-m-d')`).
* **Laravel API Resources** — classes dédiées `JsonResource` par shape exposée.
* **`Arr::only($model->toArray(), [...])` dans les controllers** — délègue la sérialisation au système de casts du modèle.

## Décision

`Arr::only($model->toArray(), [...])` pour les objets uniques (formulaires d'édition). Pour les listes paginées, mapping manuel sur `$paginator->items()` avec la même approche, et structure `{data, meta}` construite explicitement.

## Justification

Les `JsonResource` sont conçues pour les API REST, pas pour Inertia. Le mismatch se manifeste immédiatement par l'enveloppe `data` qu'on doit contourner avec `->resolve()` — signe qu'on travaille contre le framework.

`toArray()` est cohérent avec Inertia car Inertia JSON-encode les props : le système de casts Laravel s'applique (`date:Y-m-d` → string `"2024-01-15"`, backed enum → string `"income"`), sans formatage explicite dans le controller. `Arr::only()` garantit qu'on n'expose que les champs voulus.

## Conséquences

* Le formatage des dates est déclaré une fois dans le modèle (`'transacted_at' => 'date:Y-m-d'`) et s'applique automatiquement partout via `toArray()`.
* La shape exposée à Vue est visible directement dans le controller — pas de fichier resource à aller chercher.
* La structure de pagination `{data, meta}` est construite manuellement dans chaque controller index, avec une légère duplication entre `EntryController` et `TransferController`.
