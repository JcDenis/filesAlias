<?php

declare(strict_types=1);

namespace Dotclear\Plugin\filesAlias;

use ArrayObject;
use Dotclear\App;

/**
 * @brief       filesAlias frontend template class.
 * @ingroup     filesAlias
 *
 * @author      Osku (author)
 * @author      Jean-Christian Denis (latest)
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
class FrontendTemplate
{
    /**
     * Display file alias URL.
     *
     * attributes:
     *
     *      - any filters     See Tpl::getFilters()
     *
     * @param   ArrayObject     $attr   The attributes
     *
     * @return  string
     */
    public static function fileAliasURL(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf(
            App::frontend()->template()->getFilters($attr),
            'App::blog()->url().App::url()->getBase("filesalias")."/".App::frontend()->context()->filealias->filesalias_url'
        ) . '; ?>';
    }
}
