---
status: accepted
date: 2026-06-17
---

# Montants stockés en centimes (entier)

## Contexte

Les montants monétaires doivent être stockés et manipulés sans perte de précision.

## Options envisagées

* **`DECIMAL(15,2)`** en base — précis, lisible directement en SQL.
* **`BIGINT` en centimes** — entier toujours positif, arithmétique sans virgule flottante.

## Décision

`BIGINT` en centimes, toujours positif. Le signe est porté par le type (`income` / `expense`) ou la direction (`from_account_id` / `to_account_id`).

## Justification

Les flottants introduisent des erreurs d'arrondi inacceptables pour de la comptabilité. `DECIMAL` est précis mais impose une conversion côté application dès qu'on fait des calculs. Les centimes en entier sont la représentation canonique des systèmes de paiement (Stripe, etc.) — pas de conversion, pas d'arrondi.

## Conséquences

* Toute valeur saisie en euros/dollars côté Vue est multipliée par 100 avant envoi (`Math.round(amount * 100)`).
* L'affichage divise par 100 et formate selon la devise du compte.
* Les colonnes sont nommées `*_cents` pour lever toute ambiguïté.
