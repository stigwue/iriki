[![Build Status](https://img.shields.io/travis/stigwue/iriki/master.svg)](https://travis-ci.org/stigwue/iriki)

# Iriki

Iriki, a framework for APIs.

Source code can be found here: [stigwue/iriki](https://github.com/stigwue/iriki).

## Why Iriki

Most PHP frameworks use a relational database as a default for persistence with views bundled.

Iriki:

* prefers the fluidity of NoSQL options (such as MongoDB) for easier modeling,

* thinks of apps as independent UIs consuming an API capable of handling whatever a front-end developer would build,

* preserves known easy routing and

* wishes that frequently used models be re-usable.

As such, Iriki:

* allows for the definition of language agnostic models (JSON) and

* replaces traditionally included views with an independent UI, which REQUESTs URLs birthed from configured routing.

Iriki features a PHP backend handling request routing, database operations, unit tests and third party libraries via composer.

## Features

Iriki comes with the following features out-of-box:

* User management

* User group management

* User sesssion management

* Mailgun support

## How it works

Iriki files all reside in a single directory. All requests to be handled by the framework will be POST/GET to or from this directory.

These are the key directories within: engine, app and vendors.

### Engine

Engine contains Iriki code and should be left untouched. Routes, models, general Iriki utilities like database operations, routing of all requests: POSTs and GETs and unit tests are handled.

#### Routes

Routes contains the url routing: controllers, their actions and expected parameters. There is an index.json file which defines default route actions for every route, route aliases and the valid routes. Note that json files defining a route may exist but will be ignored if they are not specified in index.json.

#### Models

Models holds the JSON description of the database models/collections/tables. I would advice that the database should not be tempered with directly except through the model definitions. Note that model definitions are called when they are defined or needed from the route definitions.

### App

App is non-Iriki code. It can reside anywhere as long as it is pointed to in the application configuration. When a request is made, a matching model is first looked for in application space. If not found, then the engine folder will be searched.

### Vendors

Vendors contain third-party code. Some of it provided via composer.


## To Do

### Features

* Handle HTTP PUT and DELETE request methods.

### Database

* MySql, Rethink DB, another API persistent support.

* Handle migration (changes to the database). 

### Language

* Port to PHP7? Or NodeJS?

### Miscellaneous

* Comments

* Tests

* Open Source
