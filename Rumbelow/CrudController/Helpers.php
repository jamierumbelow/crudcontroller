<?php
/**
 * CrudController is a shared base controller that provides a CRUD basis for Laravel applications.
 *
 * @package jamierumbelow/crudcontroller
 * @author Jamie Rumbelow <jamie@jamierumbelow.net>
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/jamierumbelow/crudcontroller
 */

namespace Rumbelow\CrudController;

use Illuminate\Http\Request;

/**
 * Methods I couldn't find a better place for
 *
 * @internal
 * @uses \Rumbelow\Http\Controllers\CrudController
 * @used-by \Rumbelow\Http\Controllers\CrudController
 */
trait Helpers
{
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