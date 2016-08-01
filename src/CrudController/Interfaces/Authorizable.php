<?php
/**
 * CrudController is a shared base controller that provides a CRUD basis for Laravel applications.
 *
 * @package jamierumbelow/crudcontroller
 * @author Jamie Rumbelow <jamie@jamierumbelow.net>
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/jamierumbelow/crudcontroller
 */

namespace Rumbelow\CrudController\Interfaces;

use Illuminate\Http\Request;

/**
 * Support for Laravel's authorization libraries
 *
 * @uses \Rumbelow\Http\Controllers\CrudController
 * @used-by \Rumbelow\Http\Controllers\CrudController
 */

interface Authorizable { }