
# Iriki

Iriki, an API framework.

Source code is at [stigwue/iriki](https://github.com/stigwue/iriki).

## To Do

* 100% test coverage.

* 100% self-documentation using Doxygen parsable comments.

* Specify HTTP request method per request, handle PUT and DELETE methods (?).

* Add user and user_group authentication to existing everyone authentication (anybody: authenticate=false, everyone: authenticate=true, user: user_authenticate=true, group: group_authenticate=true).

* Add 'scarf', an app for automating common Iriki tasks.

* Use _hasmany_ relationship to recurse read.

* Better/standard error description?

* Add other NoSQL db support?

* Build in inter-operability with other Iriki apps (app.json should hold an array of apps, callable via config/code).

* Provide configurations in more than json files?

* Rewrite in PHP7.
