<?php
/**
 * @brief filesAlias, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugin
 *
 * @author Osku and contributors
 *
 * @copyright Jean-Christian Denis
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

namespace Dotclear\Plugin\filesAlias;

use ArrayObject;
use dcCore;

/**
 * File alias frontend template.
 */
class FrontendTemplate
{
    /**
     * Display file alias URL.
     *
     * attributes:
     *
     *      - any filters     See dcTemplate::getFilters()
     *
     * @param   ArrayObject     $attr   The attributes
     *
     * @return  string
     */
    public static function fileAliasURL(ArrayObject $attr): string
    {
        return '<?php echo ' . sprintf(
            dcCore::app()->tpl->getFilters($attr),
            'dcCore::app()->blog->url.dcCore::app()->url->getBase("filesalias")."/".dcCore::app()->ctx->filealias->filesalias_url'
        ) . '; ?>';
    }
}
