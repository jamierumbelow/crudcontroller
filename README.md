# CrudController

[![Latest Version](https://img.shields.io/github/release/jamierumbelow/crudcontroller.svg)](https://github.com/jamierumbelow/crudcontroller/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Build Status](https://img.shields.io/scrutinizer/build/g/jamierumbelow/crudcontroller.svg)](https://scrutinizer-ci.com/g/jamierumbelow/crudcontroller/build-status/master)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/jamierumbelow/crudcontroller.svg)](https://scrutinizer-ci.com/g/jamierumbelow/crudcontroller/code-structure)
[![Code Quality](https://img.shields.io/scrutinizer/g/jamierumbelow/crudcontroller.svg)](https://scrutinizer-ci.com/g/jamierumbelow/crudcontroller/code-structure)
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

`CrudController` is an abstract class; there are two required methods you **must** implement.

* `getClass()`, which should return the name of the Eloquent model which this controller will manage. Typically there will be a 1:1 controller:model mapping.
* `getCollectionName()`, which should return a string of the plural name of the collection under management. For instance, if the controller is managing a `Book` model, then the collection might be named `books`. By default, this collection name is used for language files, routing, and variable names.

Once this is done, the CRUD functionality will work out-of-the box.

### View Scaffolding

I've included some basic views.

### Former Support

I use [Former](https://github.com/formers/former) for pretty much everything nowadays; it's a highly recommended alternative to the old Laravel form builder. Naturally, CrudController comes with Former support out of the box.

If you'd like to enable Former support, just implement the `Rumbelow\CrudController\Interfaces\Formerable` interface:

```php
use Rumbelow\CrudController\Interfaces\Formerable;

class CommentsController extends CrudController implements Formerable {
```

If Former support is enabled, CrudController will automatically populate your Former forms with the object data, using `Former::populate`. It'll also set the `translate_from` Former configuration value to the appropriate language file (see `Rumbelow\CrudController\Traits\I18n#getLanguageName`).

## Input

### Validation

If your controller implements the `Validatable` interface, controller-level validation support will be enabled on the implementing controller. The interface will demand you implement a `getValidationRules()` method. This method is designed to provide flexibility to your validating rules.

`getValidationRules()` should return an array. Typically, this will be a normal validation array, like you're used to with Laravel:

```php
protected function getValidationRules(Request $request, Model $obj)
{
    return [
        'title' => 'required|max:100',
        'author_ip' => 'required|ip',
        'category_id' => 'integer',
        'body' => 'required',
    ];
}
```

In some cases, you might want your validation rules to differ depending on if you're creating or updating an instance. `getValidationRules()` is passed an instance of `Illuminate\Http\Request`, which you can use to test the request HTTP method or route name:

```php
protected function getValidationRules(Request $request, Model $obj)
{
    $rules = [
        'title' => 'required|max:100',
        'author_ip' => 'required|ip',
        'category_id' => 'integer',
        'body' => 'required',
    ];

    if ( $request->route()->getName() === 'posts.update' )
        unset($rules['author_ip']);

    return $rules;
}
```

## Output

### Routing

## Authorization

If your controller implements the `Rumbelow\CrudController\Interfaces\Authorizable` interface, each of the base CRUD methods will check if the user is authorized to perform the given action before proceeding. It does so [using Laravel's built in `authorize()` method](https://laravel.com/docs/5.2/authorization#controller-authorization), so it will call your policies as you expect.

[For more information on how to set up Laravel's built-in authorization, see here.](https://laravel.com/docs/5.2/authorization)

Permissions match up, by and large, with the base CRUD methods in [`src/CrudController/Traits/PublicActions.php`](src/CrudController/Traits/PublicActions.php).

* `index` – called on `index()` requests. Passed the class name as a string (`$klass`).
* `read` – called on `show()` requests. Passed the instance of whatever class is being displayed.
* `create` – called on `create()` and `store()` requests. Passed the class name as a string (`$klass`).
* `update` – called on `edit()` and `update()` requests. Passed the instance of whatever class is being updated.
* `destroy` – called on `confirmDestroy()` and `destroy()` requests. Passed the instance of whatever class is being destroyed.

## Fetchers

## Language / I18n

## Contributions

## License

CrudController uses an MIT license – which is to say, please do what you like with it. [For more, read the full license](LICENSE.md).
