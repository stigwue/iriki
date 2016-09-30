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

These are the key directories within app: application, engine, models and routes.

### Routes

Routes contains the url routing: controllers, their actions and expected parameters. There is an index.json file which defines default route actions for every route, route aliases and the valid routes. Note that json files defining a route may exist but will be ignored if they are not specified in index.json.

### Models

Models holds the JSON description of the database models/collections/tables. I would advice that the database should not be tempered with directly except through the model definitions. Note that model definitions are called when they are defined or needed from the route definitions.

### Engine

Engine contains Iriki code and should be left untouched. Code covers general Iriki utilities, model IO operations (MongoDB and MySQL via RedBeanPHP), routing of all requests: POSTs and GETs and tests.

### Application

Application contains... Bit undecided for now. Either code for pre-IO operations or code to replace override model IO operations.


## To Do
Enable MySQL IO using RedBean PHP

Add changes to the database like Laravel does outside runtime. 

Port to Python? Is there a Python equivalent for RedBean?


