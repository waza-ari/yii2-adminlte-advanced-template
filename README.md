Yii 2 Advanced Project Template - AdminLTE, Swagger API, Docker, RBAC, i18n
===============================

This advanced project template is based on the Yii2 advanced project templates. It is intented as a starting point
when developing a web application and already incorporates some commonly required patterns. Further, it already includes
the [AdminLTE template](https://adminlte.io) and provides support for:

- RBAC with pre-populated, basic structure
- Bearer-Token based REST API with basic login and logoff functionality, using multiple tokens per user
- Versioned API
- Pre-Configured Swagger API Documentation
- Docker-compose and docker files for ready-to-use development environment

The template includes three tiers: front end, api, and console, each of which
is a separate Yii application.

The template is designed to work in a team development environment. It supports
deploying the application in different environments.

Run this in docker
------------------

To run this template in docker, install docker and docker-compose according to your OS.
In this folder, then simply run:

```
docker-compose up -d
```

After building the containers, the following applications should be ready to serve:

- App itself at http://localhost
- PHPMyAdmin at http://localhost:8080

Database credentials are root:secret

DIRECTORY STRUCTURE
-------------------

```
api
    common/
        controllers      contains controllers models for all API versions
        models           contains common models for all API versions
    config/              contains API configuration files
    modules              contains modules, one for each API version
        v1               contains API version 1
            controllers/ contains controller for API v1
            models/      contains models for API v1
            swagger/     contains swagger (API documentation) definition files
common
    config/              contains shared configurations
    mail/                contains view files for e-mails
    models/              contains model classes used in both backend and frontend
    tests/               contains tests for common classes    
console
    config/              contains console configurations
    controllers/         contains console controllers (commands)
    migrations/          contains database migrations
    models/              contains console-specific model classes
    runtime/             contains files generated during runtime
docker                   contains docker files and nginx/php configuration files
frontend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains frontend configurations
    controllers/         contains Web controller classes
    models/              contains frontend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for frontend application
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
    widgets/             contains frontend widgets
vendor/                  contains dependent 3rd-party packages
environments/            contains environment-based overrides
```

TEMPLATE COMPONENTS
-------------------

## The Frontend

The frontend is based on the [AdminLTE template](https://adminlte.io). When running the application first,
all sites are restricted to logged in users, hence you will be redirected to the login page. Default credentials are
admin:admin.

The AdminLTE plugins are not enabled by default, but you can enable them following the instructions found at
[this page](https://github.com/dmstr/yii2-adminlte-asset).

## Login System

A standard Yii2 RBAC model is employed. When creating the initial application, an admin user is created having the
administrator role. By default this is required to access the interactive Swagger API documentation.

Login for the user works both via API and web interface.

## API

The project comes preconfigured to provide a versioned REST API. Code is annotated using the Swagger version 2 syntax,
such that the API documentation can be automatically generated. As soon as the project is running (i'm assuming docker
here), head to the following URL:

http://localhost/site/doc

You may need to login first, as access is restricted to users having the administrative role. When generating the API
documentation, the following directories are scanned by default:

- `@api/modules/v1/swagger`
- `@api/modules/v1/controllers`
- `@api/modules/v1/models`
- `@frontend/models`
- `@common/models`

This list can be modified or extended from the `frontend/SiteController.php` file. For more information regarding
the Yii2 swagger integration refer to the [yii2-swagger documentation](https://github.com/yii2mod/yii2-swagger). For
advanced topics regarding the Swagger annotation itself refer to the 
[PHP swagger module](https://github.com/zircote/swagger-php). A huge advantage of a correct swagger annotation (besides
the obvious fact that it automatically generates a documentation) is that you can easily generate client code for
many client languages.


DB Migrations
-------------
To work with database migrations, you can use the reguar yii2 migration feature. The migrations are located in 
`console/migratons/...`. TODO: Link to migration commands

If you need to add migrations of other modules as well and don't want to copy them over to your migration folder,
you can just edit the `console/config/main.php` file. Both namespaced migrations as well as migration paths can be
added there.

Create Migration: `./yii migrate/create <name>`
Apply Migrations: `./yii migrate`


If running in a docker environment, you may want to run the commands from the PHP docker container, for example:

```
docker exec -it yii2adminlteadvancedtemplate_php_1 ./yii migrate
```

Internationalization (i18n)
---------------------------

This template makes use of the Yii2 built-in i18n feature. It contains a config which can be used to extract all
strings from `Yii::t()` calls to the Gettext files, which are located in `common/messages`. The source language is set
to `en_US`, which matches the Yii2 recommendations, as it is usually easier to find someone to translate from english
to non-english rather than from non-english to non-english.

You can specifiy the required target languages in the `common/config/i18n.php` configuration file. When extracting
messages, new strings will be added to a separate *.po file for each language. You can extract the strings using the 
following command:

`docker exec -it yii2adminlteadvancedtemplate_php_1 ./yii message common/config/i18n.php`

You can also use view translation, as has been done for the `site/index` view. Refer to the
[Yii2 i18n documentation](http://www.yiiframework.com/doc-2.0/guide-tutorial-i18n.html) for further information.

After changing the PO files, you need to convert them to MO files, for example like this:

`docker exec -it yii2adminlteadvancedtemplate_php_1 msgfmt common/messages/de-DE/messages.po -o common/messages/de-DE/messages.mo`
