# CrudController

[![Latest Version](https://img.shields.io/github/release/jamierumbelow/crudcontroller.svg)](https://github.com/jamierumbelow/crudcontroller/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/jamierumbelow/crudcontroller/master.svg)](https://travis-ci.org/jamierumbelow/crudcontroller)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/jamierumbelow/crudcontroller.svg)](https://scrutinizer-ci.com/g/jamierumbelow/crudcontroller/code-structure)
[![Total Downloads](https://img.shields.io/packagist/dt/jamierumbelow/crudcontroller.svg)](https://packagist.org/packages/jamierumbelow/crudcontroller)

**CrudController is developed thanks to the generous support of [Platform IQ](https://www.platformiq.com/).**

CrudController is a base controller for Laravel 5 applications, making it painfully easy to implement CRUD behaviour – without restricting you to what I think is right. It's easily customisable and very, very flexible.

## Synopsis

```php
class PostsController extends CrudController
{
    protected function getClass()
    {
        return Post::class;
    }

    protected function getCollectionName()
    {
        return 'posts';
    }

    protected function getValidationRules(Request $request, Model $obj)
    {
        $rules = [
            'title' => 'required|max:100',
            'body' => 'required',
        ];

        return [
            'creating' => $rules,
            'updating' => $rules,
        ];
    }
}
```

## Table of Contents

Todo

## Requirements and Installation

You'll need at least **PHP 5.4**, although 5.5 is recommended. You'll also need to be running **>= Laravel 5.1**.

Installation is easy. Require the package in composer:

    $ php composer.phar require jamierumbelow/crudcontroller

...and start using it:

```php
use Rumbelow\Http\Controllers\CrudController;

class PostsController extends CrudController {
```

You can also get the bleeding-edge version by pointing to this GitHub repository in your `composer.json` file:

```json
"require": {
    "jamierumbelow/crudcontroller": "dev-master",
},
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/jamierumbelow/crudcontroller.git"
    }
]
```

## The Basics

To use CrudController, you'll need to extend your application controllers from `Rumbelow\Http\Controllers\CrudController`. For now, CrudController automatically extends the default Laravel `App\Http\Controllers\Controller`, so you're able to make class-level changes without having to create a new subclass.

`CrudController` is an abstract class; there are three required methods you must implement.

* `getClass()`, which should return the name of the Eloquent model which this controller will manage. Typically there will be a 1:1 controller:model mapping.
* `getCollectionName()`, which should return a string of the plural name of the collection under management. For instance, if the controller is managing a `Book` model, then the collection might be named `books`. By default, this collection name is used for language files, routing, and variable names.
* `getValidationRules()`, which should return an array of validation rules.

Once this is done, the CRUD functionality will work out-of-the box.

### View Scaffolding

I've included some basic views.

### Former Support

I use [Former](https://github.com/formers/former) for pretty much everything nowadays; it's a highly recommended alternative to the old Laravel form builder. Naturally, CrudController comes with Former support out of the box.

If you'd like to enable Former support, ...

## Input

### Validation

## Output

### Routing

## Authorization

## Fetchers

## Language / I18n

## Contributions

## License

CrudController uses an MIT license – which is to say, please do what you like with it. [For more, read the full license](LICENSE.md).
