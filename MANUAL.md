# Iriki Manual

Source code is at [stigwue/iriki](https://github.com/stigwue/iriki).

## Setup

Iriki is made up of three important components: the engine, the app and vendor provided libraries.

### Engine

The engine comes with the framework.

### App

A sample app (kronos) comes with the default Iriki installation. It should serve as an example.

### Vendors

Vendor provided third-party code need to be installed using composer.

1. Composer
```
composer install
```

## How it works

Iriki files all reside in a single directory. All requests to be handled by the framework will be POST/GET to or from this directory.

These are the key directories within: engine, app and vendor.

### Engine

Engine contains Iriki code and should be left untouched. Routes, models, general Iriki utilities like database operations, routing of all requests: POSTs and GETs and unit tests are handled.

#### Routes

Routes contains the url routing: controllers, their actions and expected parameters. There is an index.json file which defines default route actions for every route, route aliases and the valid routes. Note that json files defining a route may exist but will be ignored if they are not specified in index.json.

#### Models

Models holds the JSON description of the database models/collections/tables. I would advice that the database should not be tempered with directly except through the model definitions. Note that model definitions are called when they are defined or needed from the route definitions.

### App

App is non-Iriki code. It can reside anywhere as long as it is pointed to in the application configuration. When a request is made, a matching model is first looked for in application space. If not found, then the engine folder will be searched.

### Vendor

Vendor contain third-party code managed by composer.