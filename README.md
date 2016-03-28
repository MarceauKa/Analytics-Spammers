# Analytics Spammers

## Qu'est-ce que c'est ?

Ce modeste dépôt a pour objectif de bâtir une liste des spammeurs Analytics ainsi que des snippets et modules à intégrer dans son appli pour s'en protéger.

Si vous ne savez pas ce que c'est, je vous invite à lire cet article : https://marceau.casals.fr/blog/2015/05/le-htaccess-ce-heros
On appelle communément ce type de spam du **SPAM par Référent** ou **Referer SPAM**.

Pour résumer, de nombreux sites tentent de faire du pognon en polluant les statistiques (Piwik, Google Analytics et consor) en faisant du SPAM par site référent.

Je vous invite à regarder dans la liste des sites référents dans vos stats pour vous rendre compte pourquoi c'est important et pourquoi c'est génant quand on cherche à avoir des stats fiables pour ses sites web.

## Usage

L'usage de cette liste est multiple. Cette dernière liste les noms de domaines référents habituellement utilisés pour le SPAM.

Il y'a plusieurs façon de les bloquer :
* Filtres Google Analytics
* .htaccess ou configuration nginx
* Script PHP (voir Module Laravel 5)

## Snippets

Le dossier snippets a pour objectif de contenir des scripts tout fait pour bloquer les spammeurs; en commençant par un fichier de configuration pour Apache et NGINX.

En fonction du temps à ma disposition, j'essaieai de faire des scripts pour les différents Frameworks : CodeIgniter, Laravel et Symfony, pour ceux que je connais bien.

## Module Laravel 5
 
Un module pour Laravel 5 est disponible si vous souhaitez automatiser le blocage.  
Il s'agit d'un middleware pour votre application que vous pourrez utiliser de manière globale ou seulement pour certaines routes.  

Attention, bien que fonctionnel, le plugin est vraiment en phase de test. N'hésitez pas à l'améliorer :)
Notez également que le filtre est conçu pour fonctionner uniquement en production (APP_ENV = production)._

#### Installation via Composer

```bash
composer require akibatech/analytics-spammers dev-master
```

#### Intégration à Laravel

Il suffit pour cela d'ajouter une entrée à votre fichier App/Http/Kernel.php. Il y'a deux manières de procéder :

De manière globale, comme le fait le middleware par défaut **CheckForMaintenance** :
```php
protected $middleware = [
    \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
    \Akibatech\Spammers\Laravel\Http\Middleware\CheckForSpammers::class, // Sera appliqué pour chaque requête
];
```

Ou pour certains groupes de route, par exemple pour le groupe **web** :
```php
protected $middlewareGroups = [
    'web' => [
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \App\Http\Middleware\Locale::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\VerifyCsrfToken::class,
        \Akibatech\Spammers\Laravel\Http\Middleware\CheckForSpammers::class, // Sera appliqué pour les routes sous le joug du groupe web
    ],
    'api' => [
        'throttle:60,1',
    ],
];
```

#### Mise à jour du dictionnaire

```bash
composer update akibatech/analytics-spammers
```

Ceci mettra automatiquement à jour le dictionnaire de spammers, à savoir le fichier **spammers.json**.

## Contribuer

Si le projet vous semble d'utilité publique, libre à vous de Puller sur ce dépôt en ajoutant les spammers à la liste ou en proposant vos snippets.

## Note

Il existe également une base de données similaire proposée par Piwik : https://github.com/piwik/referrer-spam-blacklist

## Contributeurs

* Marceau Casals (Initiateur)
* Thomas Sileghem (Générateur Node.js)