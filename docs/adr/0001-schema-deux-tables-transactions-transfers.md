---
status: accepted
date: 2026-06-17
---

# Deux tables séparées pour les transactions et les virements

## Contexte

Un mouvement financier peut être une entrée/sortie sur un compte (revenu, dépense) ou un virement entre deux comptes. La question est de savoir comment les stocker.

## Options envisagées

* **Table unique `transactions`** avec un type `transfer` et deux colonnes `linked_transaction_id` + `transfer_direction` — chaque virement crée deux lignes miroir reliées entre elles.
* **Table unique `transactions`** avec un type `transfer` et des colonnes `from_account_id` / `to_account_id` sur la même ligne.
* **Deux tables séparées** : `transactions` pour les revenus/dépenses, `transfers` pour les virements.

## Décision

Deux tables séparées : `transactions` (income | expense) et `transfers`.

## Justification

L'approche miroir avec `linked_transaction_id` duplique la donnée et crée un risque de désynchronisation (les deux lignes peuvent diverger). L'approche colonne unique avec `from_account_id` sur `transactions` mélange deux concepts distincts dans le même schéma.

La table `transfers` est structurellement différente : elle relie deux comptes, n'a pas de `type`, et son sens est intrinsèque (de → vers). Garder ce concept dans sa propre table évite les nullables et rend chaque table cohérente avec son domaine.

## Conséquences

* Le solde d'un compte se calcule en interrogeant les deux tables : `transactions` (income/expense) + `transfers` (out/in via `from_account_id` / `to_account_id`).
* Pas de vue unifiée triviale en SQL — une liste chronologique mixte nécessite un `UNION` ou une agrégation en mémoire.
* Les listes entrées et virements sont séparées dans l'UI (voir ADR 0003).
