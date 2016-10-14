# Iriki

The Humongous View Controller.

Source code can be found here: [stigwue/iriki](https://github.com/stigwue/iriki).

## Why Iriki

For most PHP MVC frameworks, a relational database such as MySQL is used for the model persistence. A view also comes bundled in.

Preferring the fluidity of NoSQL options (such as MongoDB) in making modeling easier,

Thinking of apps where a UI is independent, consuming an API capable of handling whatever a front-end developer would build,

Preserving the easy routing of the frameworks and

Wishing that frequently used models be shared:

The idea of a new framework which would:

allow definition of models language agnostic (json),

replace traditional framework views with an independent UI, which REQUESTs URLs birthed from preconfigured routing

was born featuring a PHP (for now, perhaps Python later) backend handling routing and database operations.

## How it works

Everything is in a single important directory "app". All requests to be handled by the Iriki app will be POST/GET to or from this directory.

These are the key directories within app: engine, application and vendors.

### Engine

Engine contains Iriki code and should be left untouched. Code covers routes, models and general Iriki utilities like its database operations, routing of all requests: POSTs and GETs and tests.

#### Routes

Routes contains the url routing: controllers, their actions and expected parameters. There is an index.json file which defines default route actions for every route, route aliases and the valid routes. Note that json files defining a route may exist but will be ignored if they are not specified in index.json.

#### Models

Models holds the JSON description of the database models/collections/tables. I would advice that the database should not be tempered with directly except through the model definitions. Note that model definitions are called when they are defined or needed from the route definitions.

### Application

Application is non-Iriki code. It can reside anywhere as long as it is pointed to in the application configuration. When a request is made, a matching model is first looked for in engine-space. If not found, then the application folder will then be used. There will pe provision to override Iriki models.


## To Do
Rethink DB?

IO with another API

Enable MySQL IO using RedBean PHP

Add changes to the database like Laravel does outside runtime. 

Port to other languages? Hack? Python? Java?
