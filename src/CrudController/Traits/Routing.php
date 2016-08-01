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
     * @param string $type The type of success, either 'create' or 'update'
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function getRedirectSuccess( Request $request, $type = null )
    {
        return $request->has('_redirect') ? redirect($request->get('_redirect')) : redirect(route( $this->getRouteBase() . '.index' ));
    }
}