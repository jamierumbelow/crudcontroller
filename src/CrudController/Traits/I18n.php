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

use Rumbelow\CrudController\Interfaces\Formerable;

use Former;

/**
 * I18n handles the internationalisation / language support
 *
 * @internal
 * @uses \Rumbelow\Http\Controllers\CrudController
 * @used-by \Rumbelow\Http\Controllers\CrudController
 */
trait I18n
{
    /**
     * Boot the I18n trait.
     *
     * @internal
     */
    protected static function bootI18n($instance)
    {
        if ( is_subclass_of($instance, Formerable::class) )
        {
            Former::setOption('translate_from', $instance->getLanguageBase());
        }
    }

    /**
     * Get the name for this controller's language file. Defaults to the collection name.
     *
     * @return string
     */
    protected function getLanguageBase()
    {
        return $this->getCollectionName();
    }
}