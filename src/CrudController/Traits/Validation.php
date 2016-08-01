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

use Illuminate\Foundation\Validation\ValidatesRequests,
    Illuminate\Http\Request;

/**
 * Validation
 *
 * @internal
 * @uses Rumbelow\Http\Controllers\CrudController
 * @used-by Rumbelow\Http\Controllers\CrudController
 */
trait Validation
{
    use ValidatesRequests;

    /**
     * We're overriding the main point of access to Laravel's ValidatesRequests, because we wat
     * to support the validation of data arrays instead of request objects.
     *
     * @param array|\Illuminate\Http\Request $data The data to validate (or the request object)
     * @param array $rules The validation rules to run on the data
     * @param array $messages
     * @param array $customAttributes
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     **/
    public function validate($data, array $rules, array $messages = [], array $customAttributes = [])
    {
        if ( $data instanceof Request )
        {
            $request = $data;
            $data = $data->all();
        }
        else
            $request = request();

        $validator = $this->getValidationFactory()->make($data, $rules, $messages, $customAttributes);

        if ($validator->fails())
        {
            $this->throwValidationException($request, $validator);
        }
    }
}