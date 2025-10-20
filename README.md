# SortirEntreAmis

## Description
> SortirEntreAmis est une application web permettant aux utilisateurs rejoindre des sorties entre amis.<br>
> Ces sorties sont gérées et administrées par les administrateurs du site.

## Prérequis
Avant d’installer l'application, assurez-vous d’avoir :
- MySQL v9.1.0
- PHP >= 8.x
- Composer
- WAMP ou tout autre serveur local (Apache + MySQL)
- Symfony CLI (optionnel mais recommandé)

## Installation
Pour installer l'application, copiez le répertoire dans : <br>
```C:\wamp64\www\SortirEntreAmis```

Ouvrez ensuite une invite de commandes **PowerShell**.<br>
Pour accéder au bon répertoire, tapez la commande suivante : <br>
```cd c:\wamp64\www\sortirentreamis```

Pour mettre à jour la base de données, utilisez :<br>
```php bin/console doctrine:schema:update --force```

Pour charger les données de mockup (fixtures) :<br>
```php bin/console doctrine:fixtures:load -q```

Les comptes créés par défaut sont les suivants :<br>
admin  : admin@gmail.com / admin1234<br>
admin2 : admin2@gmail.com / admin1234<br>
user   : user@gmail.com  / user1234      
user2  : user2@gmail.com  / user1234

Enfin, pour lancer le serveur Symfony :<br>
```symfony serve```

Le serveur sera accessible par défaut sur http://localhost:8000
