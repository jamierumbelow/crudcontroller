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
use Former\Former;

use Rumbelow\CrudController\Interfaces\Validatable,
    Rumbelow\CrudController\Interfaces\Formerable;

/**
 * PublicActions are the core of the CRUD functionality; the methods accessed directly through the router.
 *
 * @internal
 * @uses \Rumbelow\Http\Controllers\CrudController
 * @used-by \Rumbelow\Http\Controllers\CrudController
 */
trait PublicActions
{
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

        // Validate?
        if ( $this instanceof Validatable )
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

        // If we've enabled Former support, then we should populate the form with the object data.
        if ( $this instanceof Formerable )
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

        if ( $this instanceof Validatable )
            $this->validate($input, $this->validationRules($request, $obj));

        $obj->fill( $input );

        $this->beforeUpdate($request, $obj);
        $this->beforeSave($request, $obj);
        
        if ( ! $obj->save() )
        {
            if ( $request->wantsJson() )
                return response()->json([ 'success' => false ]);
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
                return response()->json([ 'success' => true ]);
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
}