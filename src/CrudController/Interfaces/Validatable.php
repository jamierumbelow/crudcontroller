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

use Illuminate\Http\Request,
    Illuminate\Database\Eloquent\Model;

/**
 * Data validation
 *
 * @uses \Rumbelow\Http\Controllers\CrudController
 * @used-by \Rumbelow\Http\Controllers\CrudController
 */

interface Validatable {

    /**
     * Get the validation rules. Either a two-dimensional array, with 'creating' and 'updating'
     * keys, or a straight validation rules array.
     *
     * @param \Illuminate\Http\Request $request The request object
     * @param \Illuminate\Database\Eloquent\Model $obj The model object
     * @return array
     */
    public function getValidationRules(Request $request, Model $obj);

}