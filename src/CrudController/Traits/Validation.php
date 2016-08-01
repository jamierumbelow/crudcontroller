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
 * Data validation
 *
 * @internal
 * @uses \Rumbelow\Http\Controllers\CrudController
 * @used-by \Rumbelow\Http\Controllers\CrudController
 */
trait Validation
{
    /**
     * Fetch the validation rules. A wrapper around getValidationRules() – use that
     * instead of overloading.
     *
     * @var \Illuminate\Http\Request $request The request object
     * @var \Illuminate\Database\Eloquent\Model $obj The model object
     * @return array
     */
    protected function validationRules(Request $request, Model $obj)
    {
        $rules = $this->getValidationRules($request, $obj);

        // We will allow either a direct rules array, in which case we need to put it into the
        // two-dimensional format we expect, or we return the 2d array.
        if ( ! ( count($rules) === 2 && isset($rulesets['creating']) && isset($rulesets['updating']) ) )
        {
            $rulesets = [
                'creating' => $rulesets,
                'updating' => $rulesets,
            ];
        }

        // Switch the HTTP method and return the appropriate rules array.
        switch ( $request->method() )
        {
            case "POST":
                return $rulesets["creating"];
                break;

            case "PUT":
            case "PATCH":
                return $rulesets["updating"];
                break;

            default:
                return [];
        }
    }
}