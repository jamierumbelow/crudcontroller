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
    Rumbelow\CrudController\Fetchers;

use Input, Redirect, Response;

abstract class CrudController extends Controller
{
    use PublicActions, Fetchers;

    /**
     * --------------------------------------------------------------------------------------------------------------
     * The following methods are the 'configuration' methods. They enable child classes to customise the basic values
     * used in the CRUD methods above. If you need to customise the /behaviour/, you should use the callbacks, found
     * below, or else just overload one of the basic methods.
     */

    abstract protected function getClass();
    abstract protected function getCollectionName();
    abstract protected function getValidationRules(Request $request, Model $obj);

    protected function getSingleName()
    {
        return str_singular($this->getCollectionName());
    }

    protected function getLanguageName()
    {
        return $this->getCollectionName();
    }

    protected function getRouteBase()
    {
        return $this->getCollectionName();
    }

    protected function getInputData(Request $request, $model)
    {
        if ($request->method() == "PUT")
            $data = array_filter(Input::only( ( method_exists($model, 'getFillableUpdate') ) ? $model->getFillableUpdate(Input::instance()) : $model->getFillable() ));
        else
            $data = array_filter(Input::only( $model->getFillable() ));

        // Since array_filter will cancel out any '0' strings, we won't be able to let through checkboxes
        // by having a hidden <input> field. But no worries, since that's messy anyway. Instead, let's
        // define a getCheckboxes() function which can set the appropriate boolean.
        foreach ( $this->getCheckboxes($request, $model) as $cb )
            $data[$cb] = (bool)$request->get($cb);

        // array_filter will also get rid of the empty values, and we may want to allow users to set the
        // column as NULL. So we'll do something 
        foreach ( $this->getNullable($request, $model) as $nc )
            if ( ! isset($data[$nc]) && ! is_null($request->get($nc)) && empty($request->get($nc)) )
                $data[$nc] = null;
        
        // ...and return the data.
        return $data;
    }

    protected function getCheckboxes(Request $request, $model)
    {
        return [];
    }

    protected function getNullable(Request $request, $model)
    {
        return [];
    }

    protected function getRedirectSuccess( Request $request, $type = null )
    {
        return $request->has('_redirect') ? Redirect::to($request->get('_redirect')) : Redirect::route( $this->getRouteBase() . '.index' );
    }

    /**
     * --------------------------------------------------------------------------------------------------------------
     * Parameter getters – add any params you'd like to pass to the view
     */

    protected function toParams(Request $request, array $params) { return $params; }
    protected function toParamsIndex(Request $request, array $params) { return $params; }
    protected function toParamsShow(Request $request, array $params) { return $params; }
    protected function toParamsCreate(Request $request, array $params) { return $params; }
    protected function toParamsEdit(Request $request, array $params) { return $params; }
    protected function toParamsConfirmDestroy(Request $request, array $params) { return $params; }

    /**
     * --------------------------------------------------------------------------------------------------------------
     * These callback methods are called, like all good callback methods ought to be.
     */

    protected function beforeAll(Request $request) { }
    protected function beforeEdit(Request $request, $model) { }
    protected function beforeStore(Request $request, $model) { }
    protected function beforeUpdate(Request $request, $model) { }
    protected function beforeSave(Request $request, $model) { }
    protected function afterCreate(Request $request, $model) { }
    protected function afterUpdate(Request $request, $model) { }
    protected function afterSave(Request $request, $model) { }

    /**
     * --------------------------------------------------------------------------------------------------------------
     * Internal methods
     */

    private function validationRules(Request $request, Model $obj)
    {
        $rulesets = $this->getValidationRules($request, $obj);

        switch ( $request->method() )
        {
            case "POST":
                return $rulesets["creating"];
                break;

            case "PUT":
                return $rulesets["updating"];
                break;

            default:
                return [];
        }
    }
}