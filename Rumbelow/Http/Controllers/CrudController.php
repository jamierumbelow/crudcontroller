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

use Rumbelow\CrudController\Fetchers;

use Input, Redirect, Response;

abstract class CrudController extends Controller
{
    use Fetchers;

    public function index(Request $request)
    {
        $this->beforeAll($request);

        $klass = $this->getClass();
        $this->requireAccess('index', $klass);

        return $this->toParamsIndex($request, $this->toParams($request, array(
            $this->getCollectionName() => $this->fetcherIndex($request, $klass),
        )));
    }

    public function create(Request $request)
    {
        $this->beforeAll($request);

        $klass = $this->getClass();
        $this->requireAccess('create', $klass);

        return $this->toParamsCreate($request, $this->toParams($request, array(
            $this->getSingleName() => $this->fetcherCreate($request, $klass),
        )));
    }

    public function store(Request $request)
    {
        $this->beforeAll($request);

        // Check if we're allowed to proceed...
        $klass = $this->getClass();
        $this->requireAccess('create', $klass);

        // Grab the input data and create a new instance of the model
        $obj = $this->fetcherStore($request, $klass);
        $input = $this->getInputData($request, $obj);

        // Validate the incoming data. If it's bad, then Laravel will redirect us away.
        $this->validate($input, $this->validationRules($request, $obj));

        $obj->fill( $input );

        $this->beforeStore($request, $obj);
        $this->beforeSave($request, $obj);

        if ( ! $obj->save() )
        {
            return Redirect::back()
                ->withErrors($obj->getErrors())
                ->withInput();
        }
        else
        {
            $this->afterCreate($request, $obj);
            $this->afterSave($request, $obj);

            return $this->getRedirectSuccess($request, 'create')
                ->with('alerts.success', trans( $this->getLanguageName() . '.success_create' ));
        }
    }

    public function show(Request $request)
    {
        $this->beforeAll($request);

        $id = $request->route($this->getCollectionName());
        $klass = $this->getClass();
        $obj = $this->fetcherShow($request, $klass, $id);

        $this->requireAccess('read', $obj);

        return $this->toParamsShow($request, $this->toParams($request, array(
            $this->getSingleName() => $obj
        )));
    }

    public function edit(Request $request)
    {
        $this->beforeAll($request);

        $id = $request->route($this->getCollectionName());
        $klass = $this->getClass();
        $obj = $this->fetcherEdit($request, $klass, $id);

        $this->requireAccess('update', $obj);

        Former::populate($obj);
        $this->beforeEdit($request, $obj);

        return $this->toParamsEdit($request, $this->toParams($request, array(
            $this->getSingleName() => $obj,
            'edit' => TRUE
        )));
    }

    public function update(Request $request)
    {
        $this->beforeAll($request);

        $id = $request->route($this->getCollectionName());
        $klass = $this->getClass();

        $obj = $this->fetcherUpdate($request, $klass, $id);
        $input = $this->getInputData($request, $obj);
        
        $this->requireAccess('update', $obj);

        // Validate the incoming data. If it's bad, then Laravel will redirect us away.
        $this->validate($input, $this->validationRules($request, $obj));

        $obj->fill( $input );

        $this->beforeUpdate($request, $obj);
        $this->beforeSave($request, $obj);
        
        if ( ! $obj->save() )
        {
            if ( $request->wantsJson() )
                return Response::json([ 'success' => false ]);
            else
                return Redirect::back()
                    ->withErrors($obj->getErrors())
                    ->withInput();
        }
        else
        {
            $this->afterUpdate($request, $obj);
            $this->afterSave($request, $obj);

            if ( $request->wantsJson() )
                return Response::json([ 'success' => true ]);
            else
                return $this->getRedirectSuccess($request, 'update')
                    ->with('alerts.success', trans( $this->getLanguageName() . '.success_update' ));
        }
    }

    public function confirmDestroy(Request $request)
    {
        $this->beforeAll($request);

        $id = $request->route($this->getCollectionName());
        $klass = $this->getClass();
        $obj = $this->fetcherConfirmDestroy($request, $klass, $id);

        $this->requireAccess('destroy', $obj);

        return $this->toParamsConfirmDestroy($request, $this->toParams($request, array(
            $this->getSingleName() => $obj,
        )));
    }

    public function destroy(Request $request)
    {
        $this->beforeAll($request);

        $id = $request->route($this->getCollectionName());
        $klass = $this->getClass();
        $obj = $this->fetcherDestroy($request, $klass, $id);
        
        $this->requireAccess('destroy', $obj);

        $obj->delete();

        return Redirect::route( $this->getRouteBase() . '.index')
            ->with('alerts.success', trans( $this->getLanguageName() . '.success_destroy' ));
    }

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