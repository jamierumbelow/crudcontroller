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
 * Fetchers provide a bridge between CrudController and your model. Useful for when you need to, e.g.,
 * grab the collection through an associated model instead of calling all() directly.
 *
 * @internal
 * @uses \Rumbelow\Http\Controllers\CrudController
 * @used-by \Rumbelow\Http\Controllers\CrudController
 */
trait Fetchers
{
    /** @var bool If the fetcher fails, should it throw an exception, or just return NULL? */
    protected $fetcherShouldThrowException = true;

    /**
     * The following methods are called on a CRUD-method basis. These are the recommended methods
     * to overwrite, rather than the utility functions below.
     */

    protected function fetcherIndex(Request $request, $klass) { return $klass::all(); }
    protected function fetcherShow(Request $request, $klass, $id) { return $this->fetchInstance($klass, $id); }
    protected function fetcherCreate(Request $request, $klass) { return $this->fetchNewInstance($klass); }
    protected function fetcherStore(Request $request, $klass) { return $this->fetchNewInstance($klass); }
    protected function fetcherEdit(Request $request, $klass, $id) { return $this->fetchInstance($klass, $id); }
    protected function fetcherUpdate(Request $request, $klass, $id) { return $this->fetchInstance($klass, $id); }
    protected function fetcherConfirmDestroy(Request $request, $klass, $id) { return $this->fetchInstance($klass, $id); }
    protected function fetcherDestroy(Request $request, $klass, $id) { return $this->fetchInstance($klass, $id); }

    /**
     * Fetch an instance
     *
     * @var string $klass The class name to instantiate
     * @var int|null $id The ID of the instance to fetch
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function fetchInstance($klass, $id)
    {
        return $this->fetcherShouldThrowException ? $klass::findOrFail($id) : $klass::find($id);
    }

    /**
     * Fetch a new instance
     *
     * @var string The class name to instantiate
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function fetchNewInstance($klass)
    {
        return new $klass();
    }
}