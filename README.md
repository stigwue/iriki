[![Build Status](https://img.shields.io/travis/stigwue/iriki/master.svg)](https://travis-ci.org/stigwue/iriki)

# Iriki

Iriki, an API framework.

Source code is at [stigwue/iriki](https://github.com/stigwue/iriki).

## Why Iriki

Most PHP frameworks come with views, routing and a way to describe models and persist them.

Iriki thinks of apps as an independent UI consuming an API. It prefers the fluidity of NoSQL for easier modeling, easy routing and reusable models.

As such, Iriki uses language agnostic (JSON) configuration to handle models and routing.

## Features

Iriki comes with user (grouping and authentication) management, email sending (using Mailgun) and logging to get you started.

## To Do

* 100% test coverage.

* Introduce logging.

* Better/standard error description.

* Add user and user_group authentication to existing everyone authentication.

* Make code self-document using parsable comments.

* Specify HTTP request per request, handle PUT and DELETE methods.

* Add MySQL support via RedbeanPHP (?).

* Build in inter-operability (app.json should hold an array of apps, callable via config/code).

* Rewrite in PHP7.
