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
 * View rendering
 *
 * @internal
 * @uses \Rumbelow\Http\Controllers\CrudController
 * @used-by \Rumbelow\Http\Controllers\CrudController
 */
trait Output
{
    /**
     * The following methods are called on their respective CRUD methods. They're passed an array
     * with the parameters that the controller thinks relevant; if you'd like to inject more values
     * into the view, this is the place to do it. toParams() is called on every request.
     *
     * @var \Illuminate\Http\Request $request The request object
     * @var array $params The existing parameters
     * @return array
     */

    protected function toParams(Request $request, array $params) { return $params; }
    protected function toParamsIndex(Request $request, array $params) { return $params; }
    protected function toParamsShow(Request $request, array $params) { return $params; }
    protected function toParamsCreate(Request $request, array $params) { return $params; }
    protected function toParamsEdit(Request $request, array $params) { return $params; }
    protected function toParamsConfirmDestroy(Request $request, array $params) { return $params; }

    /**
     * Get the single name, used for the variable name in the show, new, edit and confirmDestroy.
     * Defaults to the collection name, singularised.
     *
     * @return string
     */
    protected function getSingleName()
    {
        return str_singular($this->getCollectionName());
    }
}