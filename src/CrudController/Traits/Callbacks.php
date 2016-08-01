<?php
/**
 * CrudController is a shared base controller that provides a CRUD basis for Laravel applications.
 *
 * @package jamierumbelow/crudcontroller
 * @author Jamie Rumbelow <jamie@jamierumbelow.net>
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/jamierumbelow/crudcontroller
 */

namespace Rumbelow\CrudController\Traits;

use Illuminate\Http\Request;

/**
 * Callback support
 *
 * @internal
 * @uses \Rumbelow\Http\Controllers\CrudController
 * @used-by \Rumbelow\Http\Controllers\CrudController
 */
trait Callbacks
{
    /**
     * These callback methods are called, like all good callback methods ought to be.
     *
     * @todo Rather than directly accessing the methods, these callbacks should be called 
     *       via a 'callback()' method, with a callback registry.
     * @var \Illuminate\Http\Request $request The request object
     * @var \Illuminate\Database\Eloquent\Model $model The model object
     * @return null
     */

    protected function beforeAll(Request $request) { }
    protected function beforeEdit(Request $request, $model) { }
    protected function beforeStore(Request $request, $model) { }
    protected function beforeUpdate(Request $request, $model) { }
    protected function beforeSave(Request $request, $model) { }
    protected function afterCreate(Request $request, $model) { }
    protected function afterUpdate(Request $request, $model) { }
    protected function afterSave(Request $request, $model) { }
}