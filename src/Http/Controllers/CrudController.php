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

use Rumbelow\CrudController\Traits\PublicActions,
    Rumbelow\CrudController\Traits\Input,
    Rumbelow\CrudController\Traits\Output,
    Rumbelow\CrudController\Traits\Fetchers,
    Rumbelow\CrudController\Traits\Callbacks,
    Rumbelow\CrudController\Traits\Routing,
    Rumbelow\CrudController\Traits\I18n,
    Rumbelow\CrudController\Traits\Validation;

abstract class CrudController extends Controller
{
    use PublicActions, Input, Output, Fetchers, Callbacks, Routing, I18n, Validation;

    /**
     * The array of booted controllers.
     *
     * @var array
     */
    protected static $booted = [];

    /**
     * The current action being processed by the controller.
     *
     * @var string
     **/
    protected $currentAction;

    /**
     * Class constructor.
     */
    public function __construct(Request $request)
    {
        self::bootIfNotBooted();

        parent::__construct();

        $this->currentAction = explode('@', $request->route()->getActionName())[1];
    }

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
     * Booting Mechanism (taken from \Illuminate\Database\Eloquent\Model)
     *
     * @author Taylor Otwell
     * @author Jamie Rumbelow
     * -----------------------------------------------------------------------------------------------------------------------------
     */

    /**
     * Check if the controller needs to be booted and if so, do it.
     *
     * @return void
     */
    protected function bootIfNotBooted()
    {
        if (! isset(static::$booted[static::class])) {
            static::$booted[static::class] = true;
            static::boot( $this );
        }
    }

    /**
     * The "booting" method of the controller.
     *
     * @param \Rumbelow\Http\Controllers\CrudController $instance The instance being booted
     * @return void
     */
    protected static function boot( $instance )
    {
        static::bootTraits($instance);
    }

    /**
     * Boot all of the bootable traits on the controller.
     *
     * @param \Rumbelow\Http\Controllers\CrudController $instance The instance being booted
     * @return void
     */
    protected static function bootTraits( $instance )
    {
        $class = static::class;

        foreach (class_uses_recursive($class) as $trait) {
            if (method_exists($class, $method = 'boot'.class_basename($trait))) {
                forward_static_call_array([$class, $method], [$instance]);
            }
        }
    }
}