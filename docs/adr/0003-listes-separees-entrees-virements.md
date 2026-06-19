---
status: accepted
date: 2026-06-17
---

# Listes séparées pour les entrées et les virements

## Contexte

Les entrées (revenus/dépenses) et les virements vivent dans deux tables distinctes (ADR 0001). La question est de savoir si l'UI présente une vue chronologique unifiée ou deux listes séparées.

## Options envisagées

* **Vue unifiée** — une seule liste triée par date, mêlant entrées et virements. Nécessite de fusionner deux sources.
* **Listes séparées** — deux pages distinctes, chacune paginée indépendamment par la base de données.

## Décision

Deux listes séparées : `/transactions` pour les entrées, `/transactions/transfers` pour les virements.

## Justification

La vue unifiée avec pagination correcte nécessite un `UNION SQL` ou une fusion en mémoire PHP. La fusion en mémoire charge toutes les lignes avant de paginer — inacceptable à l'échelle. Le `UNION SQL` est faisable mais sort du confort Eloquent et complique les filtres.

Les listes séparées permettent une pagination native Eloquent (`->paginate(25)`) sans compromis sur les perfs. Les deux entités ont aussi des colonnes différentes (account vs from/to), ce qui rend une vue unifiée moins lisible.

## Conséquences

* Navigation entre les deux listes via des liens dédiés.
* Pas de vue chronologique globale — acceptable pour un usage personnel à faible volume de saisie.
* Si une vue unifiée est souhaitée à terme, implémenter un `UNION SQL` ou une table de vue matérialisée.
