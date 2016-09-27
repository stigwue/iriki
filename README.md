# Iriki

The Humongous View Controller.

Source code can be found here: [stigwue/iriki](https://github.com/stigwue/iriki).

## Why Iriki

Why did I write this? CakePHP 3 came out without MongoDB support. And some other Cake related stuff I hated doing was getting to me. For instance, this is how I begin my average Cake app.

1. Set up a database and create and connect tables using MySQL Workbench.

2. Bake which creates models, views and controllers in Cake from the database tables.

3. Tweak tweak tweak.

Now, I was tired of using MySQL. I preferred the fluidity of MongoDB so modeling could be easier. I wasn't a good UI/UX developer so I'd wanted to replace the View with an API capable of handling whatever a front-end developer would build. The controller would remain similar to Cake's as I loved its easy routing. Also, modifying models in CakePHP was hell. Say the database table was modified, Bake wasn't selective enough for me in integrating the new changes.

So the idea was then a new framework which would allow me define models in an easier way (json) and from the definition, the database is built. Also, to replace the html views with an independent front end which will POST/GET to and from auto defined URLs gotten from routing.

So, a PHP (for now, maybe Python later) backend handling routing and DB operations, a javascript based front end or whatever the frontend dev is comfortable with.

## How it works

Everything is in a single important directory "app". A frontend developer will POST/GET to and from this drectory.

All requests to be handled by the Iriki app will be POSTed to or GET from this file. It works with the routes and the models.

There are three very key directories within app: engine, models and routes.

### Routes
Routes contains the url routing: controllers, their actions and expected parameters. There is an index.json file which defines default route actions for every route, route aliases and the valid routes. Note that json files defining a route may exist but will be ignored if they are not specified in index.json.

### Models
Models holds the JSON description of the database models/collections/tables. I would advice that the database should not be tempered with directly except through the model definitions. I hope to add a way to incorporating code-side changes to the database like Laravel does. Note that these model definitions are called when they are defined or needed from the route definitions.

### Engine

Engine contains Iriki code and should be left untouched. There is app.php which contains code for general Iriki utilities, model.php for model IO operations (MongoDB and MySQL via RedBeanPHP?), route.php for the routing of all requests: POSTs and GETs and test.php for tests.


## To Do
Enable MySQL IO using RedBean PHP

Port to Python? Is there a Python equivalent for RedBean?
