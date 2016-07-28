# MongoVC

The Mongo View Controller.

Source code can be found here: [stigwue/mongovc](https://github.com/stigwue/mongovc).

## Why MongoVC

Why did I write this? CakePHP 3 came out without MongoDB support. And some other Cake related stuff I hated doing were getting to me. For instance, this is how I begin my average Cake app.

1. Set up a database and create and connect tables using MySQL Workbench.

2. Bake the cake, this creates models, views and controllers in Cake from the database tables.

3. Tweak tweak tweak.

Now, I was tired of using MySQL. I preferred the fluidity of MongoDB so modeling could be easier. I wasn't a good UI/UX developer so I'd wanted to replace the View with an API capable of handling whatever a front-end developer would build. The controller would remain similar to Cake's as I loved its easy routing.

## How it works

There are two files that are important.

There is index.php which is the app's landing page. Feel free to do whatever with this. MongoVC will hardly deal with front-end presentation.

Next is api.php. All requests to be handled by the MongoVC app will be POSTed to or GET from this file. It works with the router.

There are three very key directories: engine, models and routes.

Engine contains MongoVC code and should be left untouched.

Models holds the JSON description of the database models/collections/tables. I would advice that the database should not be tempered with directly except through the model definitions. I hope to add a way to incorporating code-side changes to the database like Laravel does.

Routes contains the url routing and controllers.

### Engine
