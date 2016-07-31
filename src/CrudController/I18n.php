<?php
/**
 * CrudController is a shared base controller that provides a CRUD basis for Laravel applications.
 *
 * @package jamierumbelow/crudcontroller
 * @author Jamie Rumbelow <jamie@jamierumbelow.net>
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/jamierumbelow/crudcontroller
 */

namespace Rumbelow\CrudController;

use Illuminate\Http\Request;

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
     * Get the name for this controller's language file. Defaults to the collection name.
     *
     * @return string
     */
    protected function getLanguageName()
    {
        return $this->getCollectionName();
    }
}