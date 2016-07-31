<?php
/**
 * CrudController is a shared base controller that provides a CRUD basis for Laravel applications.
 *
 * @package jamierumbelow/crudcontroller
 * @author Jamie Rumbelow <jamie@jamierumbelow.net>
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/jamierumbelow/crudcontroller
 */

namespace Rumbelow\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Rumbelow\CrudController\PublicActions,
    Rumbelow\CrudController\Input,
    Rumbelow\CrudController\Output,
    Rumbelow\CrudController\Fetchers,
    Rumbelow\CrudController\Callbacks,
    Rumbelow\CrudController\Routing,
    Rumbelow\CrudController\I18n,
    Rumbelow\CrudController\Validation;

abstract class CrudController extends Controller
{
    use PublicActions, Input, Output, Fetchers, Callbacks, Routing, I18n, Validation;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * The following methods are the base configuration methods. They enable child classes to customise the basic values
     * used in the CRUD methods. If you need to customise the /behaviour/, you should use the callbacks, found below, or 
     * else just overload one of the basic methods.
     */

    /**
     * Get the model class.
     *
     * @return string
     */
    abstract protected function getClass();

    /**
     * Get the plural name of the collection of models (e.g. App\User::class => 'users')
     *
     * @return string
     */
    abstract protected function getCollectionName();

    /**
     * Get the validation rules. Either a two-dimensional array, with 'creating' and 'updating'
     * keys, or a straight validation rules array.
     *
     * @var \Illuminate\Http\Request $request The request object
     * @var \Illuminate\Database\Eloquent\Model $obj The model object
     * @return array
     */
    abstract protected function getValidationRules(Request $request, Model $obj);
}