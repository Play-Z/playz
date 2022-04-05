# PlayZ
#### *the playz to play*

## Table of Contents


## Description 

"What is Playz?" In one sentence PlayZ is "an esport event organization and management website allowing the creation of tournaments on the most popular video games of the esport scene"

But PlayZ is above all THE PLAYZ TO BE for :

- follow your favourite tournaments
- follow your favourite teams
- follow your favourite players
- follow quality esport news

So if we summarize, in one place you can follow all the e-sport news you want in a snap and with an integration with apps like twitch, discord or twitter it has never been so easy to be an esport fan.

But let's say you don't just want to follow esports from afar. You want to live it because you're a G@merZ. 

This is also possible on PlayZ :

Just create an account and join the PlayerZ community. You'll have access to tons of tournaments on all your favourite games
Whether it's remote Rocket League tournaments, amateur Starcraft LANS or big professional League of Legends events. There's something for everyone.

If you are a tournament organizer and still not convinced. On PlayZ you'll have access to increased tournament management and creation options.

So are you ready to join **THE PLAYZ TO PLAY?**

## Technologies in use
- [Symfony 5.3.9](https://symfony.com/doc/5.2/index.html)
- [Tailwind CSS](https://tailwindcss.com/docs)
- [Php-8.0](https://www.php.net/manual-lookup.php?pattern=php+unit&scope=quickref)
- [Docker](https://docs.docker.com/)
- [Git](https://git-scm.com/doc)


## Requirement for starting

### Required

- [Docker](https://docs.docker.com/engine/install/)
- [Docker-Compose](https://docs.docker.com/compose/install/)
- Git (With configured [SSH](https://docs.github.com/en/authentication/connecting-to-github-with-ssh) and [GPG](https://docs.github.com/en/authentication/managing-commit-signature-verification/generating-a-new-gpg-key) Keys for signed commits)
- Knowledge of [Git Flow](https://www.atlassian.com/fr/git/tutorials/comparing-workflows/gitflow-workflow#:~:text=git%2Dflow%20est%20un%20outil,ex%C3%A9cuter%20brew%20install%20git%2Dflow%20.)

#### Optional

- [PhpStorm](https://www.jetbrains.com/fr-fr/phpstorm/download/#section=windows)
- [GitKraken](https://www.gitkraken.com/)

## Getting started

#### Start Application
```bash
docker-compose build --pull --no-cache
docker-compose up -d
```

#### Front-end initialisation
```bash
docker-compose exec php npm install

//For one scss compilation :
docker-compose exec php npm run dev

OR

//For constent scss compilation :
docker-compose exec php npm run watch
```

#### Configuration
```text
# URL
http://127.0.0.1

#MAIL SMTP
MAILER_DSN=smtp://mailcatcher:1025

# Env DB
DATABASE_URL="postgresql://postgres:password@db:5432/db?serverVersion=13&charset=utf8"
```

## Useful commands
```
# List all existing commands 
docker-compose exec php php bin/console
# Delete browser cache
docker-compose exec php php bin/console cache:clear
# Creating a blank file
docker-compose exec php php bin/console make:controller
docker-compose exec php php bin/console make:form
# Creation of a complete CRUD
docker-compose exec php php bin/console make:crud
# Create user automatically (-a => is admin)
docker-compose exec php bin/console createUser (-a) "your email" "your password"
```

## Database management

#### Entity creation commands
```
docker-compose exec php php bin/console make:entity
```
Document on relationships between entities
https://symfony.com/doc/current/doctrine/associations.html

#### Updating the database
```
# See the requests that will be played with force
docker-compose exec php php bin/console doctrine:schema:update --dump-sql
# Execute DB requests
docker-compose exec php php bin/console doctrine:schema:update --force
```

#### Création des dataFixtures

https://symfony.com/bundles/DoctrineFixturesBundle/current/index.html

Utilisation avec FakerBundle : https://github.com/fzaninotto/Faker#seeding-the-generator

#### Commande pour exécuter les datasFixtures

```
docker-compose exec php php bin/console doctrine:fixtures:load
```

## Gestion des formulaires

https://symfony.com/doc/current/reference/forms/types.html

## Gestion de l'authentification

https://symfony.com/doc/current/components/security/authentication.html

#### Commande pour générer l'auth

```
docker-compose exec php php bin/console make:user
docker-compose exec php php bin/console doctrine:schema:update --force
docker-compose exec php php bin/console make:auth
// Puis aller dans votre le fichier "custom authenticator" pour choisir la route de redirection après connexion (ligne 54).
```

## Sécurité

#### Contrôle d'accèss par role
https://symfony.com/doc/current/security.html#securing-controllers-and-other-code

#### Validation des formulaires avec les Assert
https://symfony.com/doc/current/validation.html

#### Création de test d'accessibilité avec les voters
https://symfony.com/doc/current/security/voters.html

## Gestion des messages flash
https://symfony.com/doc/current/controller.html#flash-messages

## Bundle d'aide

#### Gedmo
https://symfony.com/bundles/StofDoctrineExtensionsBundle/current/index.html
https://github.com/doctrine-extensions/DoctrineExtensions/tree/main/doc

#### Vich Uploader
https://github.com/dustin10/VichUploaderBundle/blob/master/docs/generating_urls.md

## Contact

- Antoine SAUNIER
- Alexandre BAUDRY
- Arthur GRATTON
- Kurunchi CHANDRASEKARAM
