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
     * @param \Illuminate\Http\Request $request The request object
     * @param array $params The existing parameters
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

    /**
     * Get the base directory to be prepended to the view name
     *
     * @param string $klass The current class short name
     * @return string
     **/
    protected function getViewBase($klass)
    {
        return str_replace('_controller', '', snake_case($klass));
    }

    /**
     * Load a view
     *
     * @param \Illuminate\Http\Request $request The request object
     * @param array|\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse $data Parameters to pass to view, or a direct Laravel response
     * @param string $viewName the name of the view, usually automatically guessed
     * @return void
     * @author 
     **/
    protected function loadView(Request $request, array $data = null, $viewName = null)
    {
        if (is_array($data) || is_null($data))
        {
            $reflection = new \ReflectionClass($this);
            $klass = $reflection->getShortName();

            $viewName = $this->getViewBase($klass) . '.' . $this->currentAction;

            $response = view($viewName)
                ->with($data ?: array());
        }

        // Allow for returning instances of Redirect or Response.
        else
        {
            $response = $data;
        }

        return $response;
    }

    /**
     * Process the toParams methods
     *
     * @internal
     * @param \Illuminate\Http\Request $request
     * @param array $params
     * @return array
     **/
    protected function _params(Request $request, $params = [])
    {
        $method = 'toParams' . $this->currentAction;

        return $this->$method($request, $this->toParams($request, $params));
    }
}