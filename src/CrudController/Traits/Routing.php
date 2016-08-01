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
use Redirect;

/**
 * All routing-related methods
 *
 * @internal
 * @uses Rumbelow\Http\Controllers\CrudController
 * @used-by Rumbelow\Http\Controllers\CrudController
 */
trait Routing
{
    /**
     * Get the named route base. Defaults to the collection name.
     *
     * @return string
     */
    protected function getRouteBase()
    {
        return $this->getCollectionName();
    }

    /**
     * Get the redirect object on success
     *
     * @param \Illuminate\Http\Request $request The request object
     * @param callable $cb
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function getRedirectSuccess(Request $request, callable $cb = null)
    {
        $redirect = $request->has('_redirect') ? redirect($request->get('_redirect')) : redirect(route( $this->getRouteBase() . '.index' ));

        if ( is_callable($cb) ) {
            $redirect = call_user_func_array($cb, [ $redirect ]);
        }

        return $redirect;
    }

    /**
     * Get the redirect object on failure
     *
     * @param \Illuminate\Http\Request $request The request object
     * @param callable $cb
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function getRedirectFailure(Request $request, callable $cb = null)
    {
        $redirect = redirect()->back();

        if ( is_callable($cb) ) {
            $redirect = call_user_func_array($cb, [ $redirect ]);
        }

        return $redirect;
    }

    protected function getRedirectSuccessStore(Request $request, callable $cb = null) { return $this->getRedirectSuccess($request, $cb); }
    protected function getRedirectSuccessUpdate(Request $request, callable $cb = null) { return $this->getRedirectSuccess($request, $cb); }
    protected function getRedirectSuccessDestroy(Request $request, callable $cb = null) { return $this->getRedirectSuccess($request, $cb); }
    protected function getRedirectFailureStore(Request $request, callable $cb = null) { return $this->getRedirectFailure($request, $cb); }
    protected function getRedirectFailureUpdate(Request $request, callable $cb = null) { return $this->getRedirectFailure($request, $cb); }
}