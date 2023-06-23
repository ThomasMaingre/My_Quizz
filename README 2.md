# My Quiz (Symfony)


## Description

My Quiz est un site de quiz qui permet aux utilisateurs de tester leur culture générale à travers une série de questions. Le projet vise à collecter des informations sur les utilisateurs, leurs intérêts, leurs préférences, etc., afin de proposer un contenu ciblé.

Le développement du site est basé sur le Framework Symfony version 3 ou supérieure. Le JavaScript est interdit dans ce projet, y compris pour les bonus. Les réponses aux quiz ne doivent pas être déduites du code source.

## Fonctionnalités

### Utilisateurs non connectés

- Répondre aux quiz
- Consulter l'historique des quiz passés et les notes obtenues
- S'inscrire (validation du compte par e-mail) et se connecter

### Utilisateurs connectés (fonctionnalités supplémentaires)

- Changer l'adresse e-mail (soumise à revalidation) et le mot de passe
- Créer un quiz

### Administrateurs

- Créer/mettre à jour/supprimer un compte utilisateur
- Créer/mettre à jour/supprimer une catégorie et un quiz
- Promouvoir un utilisateur au statut d'administrateur
- Envoyer des e-mails aux utilisateurs en fonction de différents critères (quiz passés, dernières connexions, etc.)
- Consulter des statistiques détaillées sur les quiz et les utilisateurs
- Afficher un graphique du nombre de visiteurs uniques et de quiz effectués sur différentes périodes de temps

## Notions 

- Entities
- Relations de modèles (one to many, many to many, etc.)
- Form builder
- Twig

## Installation

1. Cloner le dépôt GitHub : `git clone https://github.com/votre-utilisateur/MVC_My_Quiz.git`
2. Installer les dépendances : `composer install`
3. Configurer la base de données dans le fichier `.env`
4. Créer la base de données : `php bin/console doctrine:database:create`
5. Effectuer les migrations : `php bin/console doctrine:migrations:migrate`
6. Lancer le serveur Symfony : `php bin/console server:run`

## Contributions

Les contributions sont les bienvenues ! Si vous souhaitez améliorer le projet, veuillez soumettre une demande de pull avec vos modifications.
