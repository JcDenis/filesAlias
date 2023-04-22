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
use dcNsProcess;

class Frontend extends dcNsProcess
{
    public static function init(): bool
    {
        static::$init = defined('DC_RC_PATH');

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
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
