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
use Former;

use Rumbelow\CrudController\Interfaces\Validatable,
    Rumbelow\CrudController\Interfaces\Formerable,
    Rumbelow\CrudController\Interfaces\Authorizable;

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
        $this->callback('beforeAll', $request);

        $klass = $this->getClass();

        if ( $this instanceof Authorizable ) {
            $this->authorize('index', $klass);
        }

        $params = $this->_params($request, [
            $this->getCollectionName() => $this->fetcherIndex($request, $klass),
        ]);

        return $this->loadView($request, $params);
    }

    public function create(Request $request)
    {
        $this->callback('beforeAll', $request);

        $klass = $this->getClass();

        if ( $this instanceof Authorizable ) {
            $this->authorize('create', $klass);
        }

        $params = $this->_params($request, [
            $this->getSingleName() => $this->fetcherCreate($request, $klass),
        ]);

        return $this->loadView($request, $params);
    }

    public function store(Request $request)
    {
        $this->callback('beforeAll', $request);

        $klass = $this->getClass();

        // Check if we're allowed to proceed...
        if ( $this instanceof Authorizable ) {
            $this->authorize('create', $klass);
        }

        // Grab the input data and create a new instance of the model
        $obj = $this->fetcherStore($request, $klass);
        $input = $this->getInputData($request, $obj);

        // Validate?
        if ( $this instanceof Validatable ) {
            $this->validate($input, $this->getValidationRules($request, $obj));
        }

        $obj->fill( $input );

        $this->callback('beforeStore', $request, $obj);
        $this->callback('beforeSave', $request, $obj);

        if ( ! $obj->save() )
        {
            return $this->getRedirectFailureStore(function($r) use ($obj)
            {
                return $r->withErrors($obj->getErrors())
                    ->withInput();
            });
        }
        else
        {
            $this->callback('afterStore', $request, $obj);
            $this->callback('afterSave', $request, $obj);

            return $this->getRedirectSuccessStore($request, function($r)
            {
                return $r->with($this->alertSuccessKey, trans( $this->getLanguageBase() . '.success_create' ));
            });
        }
    }

    public function show(Request $request)
    {
        $this->callback('beforeAll', $request);

        $id = $request->route($this->getCollectionName());
        $klass = $this->getClass();
        $obj = $this->fetcherShow($request, $klass, $id);

        if ( $this instanceof Authorizable ) {
            $this->authorize('read', $obj);
        }

        $params = $this->_params($request, [
            $this->getSingleName() => $obj
        ]);

        return $this->loadView($request, $params);
    }

    public function edit(Request $request)
    {
        $this->callback('beforeAll', $request);

        $id = $request->route($this->getCollectionName());
        $klass = $this->getClass();
        $obj = $this->fetcherEdit($request, $klass, $id);

        if ( $this instanceof Authorizable ) {
            $this->authorize('update', $obj);
        }

        // If we've enabled Former support, then we should populate the form with the object data.
        if ( $this instanceof Formerable ) {
            Former::populate($obj);
        }

        $params = $this->_params($request, [
            $this->getSingleName() => $obj,
            'edit' => TRUE
        ]);

        return $this->loadView($request, $params);
    }

    public function update(Request $request)
    {
        $this->callback('beforeAll', $request);

        $id = $request->route($this->getCollectionName());
        $klass = $this->getClass();

        $obj = $this->fetcherUpdate($request, $klass, $id);
        $input = $this->getInputData($request, $obj);
        
        if ( $this instanceof Authorizable ) {
            $this->authorize('update', $obj);
        }

        if ( $this instanceof Validatable ) {
            $this->validate($input, $this->getValidationRules($request, $obj));
        }

        $obj->fill( $input );

        $this->callback('beforeUpdate', $request, $obj);
        $this->callback('beforeSave', $request, $obj);
        
        if ( ! $obj->save() )
        {
            $errors = $obj->getErrors();

            if ( $request->wantsJson() ) {
                return response()->json([ 'success' => false, 'errors' => $errors ]);
            }
            else {
                return $this->getRedirectFailureUpdate(function($r) use ($errors)
                {
                    return $r->withErrors($errors)
                        ->withInput();
                });
            }
        }
        else
        {
            $this->callback('afterUpdate', $request, $obj);
            $this->callback('afterSave', $request, $obj);

            if ( $request->wantsJson() ) {
                return response()->json([ 'success' => true ]);
            }
            else {
                return $this->getRedirectSuccessUpdate($request, function($r)
                {
                    return $r->with($this->alertSuccessKey, trans( $this->getLanguageBase() . '.success_update' ));
                });
            }
        }
    }

    public function confirmDestroy(Request $request)
    {
        $this->callback('beforeAll', $request);

        $id = $request->route($this->getCollectionName());
        $klass = $this->getClass();
        $obj = $this->fetcherConfirmDestroy($request, $klass, $id);

        if ( $this instanceof Authorizable ) {
            $this->authorize('destroy', $obj);
        }

        $params = $this->_params($request, [
            $this->getSingleName() => $obj,
        ]);

        return $this->loadView($request, $params);
    }

    public function destroy(Request $request)
    {
        $this->beforeAll($request);

        $id = $request->route($this->getCollectionName());
        $klass = $this->getClass();
        $obj = $this->fetcherDestroy($request, $klass, $id);
        
        if ( $this instanceof Authorizable ) {
            $this->authorize('destroy', $obj);
        }

        $this->callback('beforeDestroy', $request, $obj);

        $obj->delete();

        $this->callback('afterDestroy', $request, $obj);

        return $this->getRedirectSuccessDestroy($request, function($r)
        {
            return $r->with($this->alertSuccessKey, trans( $this->getLanguageBase() . '.success_destroy' ));
        });
    }
}