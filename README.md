# My_Quizz 🎲✅❌

## Introduction

Ce projet est un site de quizz interactif en utilisant Symfony, avec la particularité d'exclure l'utilisation de JavaScript. Nous avons été chargé de concevoir et de développer un système de quizz complet, comprenant différentes catégories, un ensemble varié de questions et un mécanisme de score.

## Installation

1. Clonez ce dépôt sur votre machine locale.
2. Assurez-vous d'avoir [Composer](https://getcomposer.org/) installé.
3. Dans le répertoire du projet, exécutez la commande suivante pour installer les dépendances nécessaires :

```php
composer install
```
## Configuration de la base de données

1. Récuperez le fichier nommé "my_quizz.sql" et importez le dans votre base de donnée.
2. Pour configurer la connexion à la base de données, ouvrez le fichier `.env` à la racine du projet.
3. Modifiez les valeurs des variables `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` et `DB_PASSWORD` pour correspondre à votre configuration de base de données.
4. Sauvegardez les modifications apportées au fichier `.env`.

## Lancement

Après avoir installé les dépendances, exécutez la commande suivante pour lancer le serveur PHP intégré :

```php
php bin/console server:run
```

## Présentation du projet

L'objectif principal de ce projet était de créer un site de quizz interactif. Nous avons travaillé sur la mise en place d'une structure de base solide, permettant la création de catégories de quizz personnalisées. Enfin, j'ai mis en place un mécanisme de score pour évaluer les performances des utilisateurs et leur fournir un retour sur leurs réponses.

### Utilisateurs non connectés

- Répondre aux quizz.
- Consulter l'historique des quizz passés et les notes obtenues.
- S'inscrire (validation du compte par e-mail) et se connecter.

### Utilisateurs connectés (fonctionnalités supplémentaires)

- Changer l'adresse e-mail (soumise à revalidation) et le mot de passe.
- Créer un quizz.

### Administrateurs

- Créer/mettre à jour/supprimer un compte utilisateur.
- Créer/mettre à jour/supprimer une catégorie et un quizz.
- Promouvoir un utilisateur au statut d'administrateur.
- Envoyer des e-mails aux utilisateurs en fonction de différents critères (quizz passés, dernières connexions, etc.).
- Consulter des statistiques détaillées sur les quizz et les utilisateurs.
- Afficher un graphique du nombre de visiteurs uniques et de quizz effectués sur différentes périodes de temps.
