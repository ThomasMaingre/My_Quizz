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

> A remplir

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


## Description

My Quiz est un site de quiz qui permet aux utilisateurs de tester leur culture générale à travers une série de questions. Le projet vise à collecter des informations sur les utilisateurs, leurs intérêts, leurs préférences, etc., afin de proposer un contenu ciblé.

Le développement du site est basé sur le Framework Symfony version 3 ou supérieure. Le JavaScript est interdit dans ce projet, y compris pour les bonus. Les réponses aux quiz ne doivent pas être déduites du code source.
