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

use dcCore;
use Dotclear\Core\Process;

class Frontend extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::FRONTEND));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        // add path to template
        dcCore::app()->tpl->setPath(
            dcCore::app()->tpl->getPath(),
            My::path() . DIRECTORY_SEPARATOR . 'default-templates'
        );
        // register template value for file alias
        dcCore::app()->tpl->addValue(
            'fileAliasURL',
            [FrontendTemplate::class, 'fileAliasURL']
        );

        return true;
    }
}
