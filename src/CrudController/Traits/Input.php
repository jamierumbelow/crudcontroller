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

use Illuminate\Http\Request,
    Illuminate\Database\Eloquent\Model;

/**
 * Handles retrieval of data from the input.
 *
 * @internal
 * @uses \Rumbelow\Http\Controllers\CrudController
 * @used-by \Rumbelow\Http\Controllers\CrudController
 */
trait Input
{
    /** @var bool Should we enable input filtering? */
    protected $enableInputFilter = true;

    /** @var bool Should we enable checkboxes? */
    protected $enableCheckboxes = true;

    /** @var bool Should we enable nullable columns? */
    protected $enableNullable = true;

    /**
     * Get the model attributes marked as checkboxes in the form.
     *
     * @param \Illuminate\Http\Request $request The request object
     * @param \Illuminate\Database\Eloquent\Model $model The model object
     * @return array[string]
     */
    protected function getCheckboxes(Request $request, Model $model)
    {
        return [];
    }

    /**
     * Get the model attributes marked as nullable.
     *
     * @param \Illuminate\Http\Request $request The request object
     * @param \Illuminate\Database\Eloquent\Model $model The model object
     * @return array[string]
     */
    protected function getNullable(Request $request, Model $model)
    {
        return [ '*' ];
    }

    /**
     * Given the request object, return the ID to pass to the fetcher
     *
     * @param \Illuminate\Http\Request $request The request object
     * @return array[string]
     */
    protected function getId(Request $request)
    {
        return $request->route($this->getRouteBase());
    }

    /**
     * Get the apropriate input data from the request.
     *
     * @param \Illuminate\Http\Request $request The request object
     * @param \Illuminate\Database\Eloquent\Model $model The model object
     * @return array
     */
    protected function getInputData(Request $request, Model $model)
    {
        // First, get the fillable attributes from the model. We allow a getFillableUpdate() method, in case
        // what's expected changes depending on if it's a new or existing model. (Think, e.g., passwords).
        if ($request->method() == "PUT")
            $attributes = method_exists($model, 'getFillableUpdate') 
                ? $model->getFillableUpdate( $request )
                : $model->getFillable();
        else
            $attributes = $model->getFillable();
            
        // Now fetch the data
        $data = $request->only( $attributes );

        // And filter it! Or not.
        $data = $this->enableInputFilter ? array_filter($data) : $data;

        // Since array_filter will cancel out any '0' strings, we won't be able to let through checkboxes
        // by having a hidden <input> field. But no worries, since that's messy anyway. Instead, let's
        // define a getCheckboxes() function which can set the appropriate boolean.
        if ( $this->enableCheckboxes )
        {
            foreach ( $this->getCheckboxes($request, $model) as $cb ) {
                $data[$cb] = (bool)$request->get($cb);
            }
        }

        // array_filter will also get rid of the empty values, and we may want to allow users to set the
        // column as NULL. So we'll do something similar to above, with a getNullable().
        if ( $this->enableNullable )
        {
            $columns = $this->getNullable($request, $model);

            if ( count($columns) === 1 && $columns[0] === '*' ) {
                $columns = $attributes;
            }

            foreach ( $columns as $nc ) {
                if ( ! isset($data[$nc]) && ! is_null($request->get($nc)) && empty($request->get($nc)) ) {
                    $data[$nc] = null;
                }
            }
        }

        // Time for a callback
        if ( $this->hasCallback('input') ) {
            $data = $this->callback('input', $data);
        }
        
        // ...and return the data.
        return $data;
    }
}