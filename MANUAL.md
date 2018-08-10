# Iriki Manual

Source code is at [stigwue/iriki](https://github.com/stigwue/iriki).

## Installing

An Iriki installation is made up of two parts: the engine and the app. 

The engine is the framework. Vendor provided third-party code needs to be installed using composer. Just change directory to the _engine_ directory and run:

```
composer install
```

Then copy _config.default.php_ to _config.php_ to use the default configurations for your installation. Note that paths in _config.php_ should be absolute or relative to _index.php_. Those in the app configuration _app.json_ should be absolute. 

Please note that Iriki requires the Apache webserver with mod_rewrite module (with .htaccess) enabled.

## Tests

```
engine/vendor/bin/phpunit --bootstrap engine/tests/bootstrap.php engine/tests/
```

## How it works

Iriki files all reside in a single directory. All requests to be handled by the framework will be POST/GET to the URL served from this directory.

One need only change configurations in the config.php file (rename config.default.php to start with the default configuration).

The _engine_ directory contains Iriki code and should be left untouched. Routes, models, general Iriki utilities like database operations, routing of requests: POSTs and GETs and unit tests are handled.

Routes contains the url routing: controllers, their actions and expected parameters. There is an index.json file which defines default route actions for every route, route aliases and the valid routes. Note that json files defining a route may exist but will be ignored if they are not specified in index.json.

Models holds the JSON description of the database models/collections/tables. I would advice that the database should not be tempered with directly except through the model definitions. Note that model definitions are called when they are defined or needed from the route definitions.